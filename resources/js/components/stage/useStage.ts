import { router } from '@inertiajs/vue3';
import type { Ref } from 'vue';
import { onBeforeUnmount, onMounted } from 'vue';
import { rng } from './stains';

/**
 * The particle layer of the Stage (specs/018-site-refresh): one canvas, clear
 * specks on the night theme and ink specks on paper, the same motion in both.
 * The effect is uavv's night sky, reused and mirrored: a slow parallax under
 * the pointer and the scroll, and at every page change the sky shifts and
 * twinkles while the content swaps.
 *
 * Budget and safety, all in this file:
 * - one canvas, device pixel ratio capped at 2, at most ~30 frames a second;
 * - fewer particles on small screens;
 * - nothing runs while the tab is hidden, and a long pause never bursts
 *   (the time step is clamped);
 * - `prefers-reduced-motion` (and the site's own `data-motion`) draws one
 *   still frame and stops, and starts again if the preference is switched off.
 */

interface Particle {
    x: number;
    y: number;
    r: number;
    depth: number;
    phase: number;
}

const FRAME_MS = 1000 / 30;

function particleCount(width: number, height: number): number {
    const area = width * height;
    const perSpeck = width < 640 ? 26000 : 14000;

    return Math.max(36, Math.min(140, Math.round(area / perSpeck)));
}

function motionAllowed(): boolean {
    return (
        !window.matchMedia('(prefers-reduced-motion: reduce)').matches &&
        document.documentElement.getAttribute('data-motion') !== 'reduced'
    );
}

export function useStage(canvas: Ref<HTMLCanvasElement | null>): void {
    let context: CanvasRenderingContext2D | null = null;
    let particles: Particle[] = [];
    let width = 0;
    let height = 0;
    let dpr = 1;
    let frame = 0;
    let lastDraw = 0;
    let lastTick = 0;
    let running = false;

    // Parallax target and current, in px; the page-change shift and twinkle.
    let targetX = 0;
    let targetY = 0;
    let offsetX = 0;
    let offsetY = 0;
    let shiftX = 0;
    let shiftY = 0;
    let twinkle = 0;
    let scrollY = 0;

    let color = '#121212';
    let baseOpacity = 0.5;

    const random = rng(Math.floor(Math.random() * 2 ** 31));
    const cleanups: (() => void)[] = [];

    function readTokens(): void {
        const style = getComputedStyle(document.documentElement);
        color = style.getPropertyValue('--sw-particle').trim() || color;
        baseOpacity =
            Number.parseFloat(
                style.getPropertyValue('--sw-particle-opacity'),
            ) || baseOpacity;
    }

    function seed(): void {
        const count = particleCount(width, height);
        particles = Array.from({ length: count }, () => ({
            x: random(),
            y: random(),
            r: 0.5 + random() * 1.1,
            depth: 0.25 + random() * 0.75,
            phase: random() * Math.PI * 2,
        }));
    }

    function resize(): void {
        const el = canvas.value;

        if (!el) {
            return;
        }

        dpr = Math.min(window.devicePixelRatio || 1, 2);
        width = window.innerWidth;
        height = window.innerHeight;
        el.width = Math.round(width * dpr);
        el.height = Math.round(height * dpr);
        context = el.getContext('2d');
        context?.setTransform(dpr, 0, 0, dpr, 0, 0);
        seed();
        draw(performance.now());
    }

    function draw(now: number): void {
        if (!context) {
            return;
        }

        context.clearRect(0, 0, width, height);
        context.fillStyle = color;
        const t = now / 1000;

        for (const p of particles) {
            const px =
                (((p.x * width + (offsetX + shiftX) * p.depth) % width) +
                    width) %
                width;
            const py =
                (((p.y * height +
                    (offsetY + shiftY - scrollY * 0.04) * p.depth) %
                    height) +
                    height) %
                height;
            const tw = 0.6 + 0.4 * Math.sin(t * (0.6 + p.depth) + p.phase);
            context.globalAlpha = Math.min(
                1,
                baseOpacity * tw * (0.55 + p.depth * 0.6) * (1 + twinkle * 0.9),
            );
            context.fillRect(px, py, p.r * 1.4, p.r * 1.4);
        }

        context.globalAlpha = 1;
    }

    function tick(now: number): void {
        if (!running) {
            return;
        }

        frame = requestAnimationFrame(tick);

        if (now - lastDraw < FRAME_MS) {
            return;
        }

        // A long pause (a hidden tab, a stalled frame) must not burst: clamp the step.
        const dt = Math.min(64, now - (lastTick || now));
        lastTick = now;
        lastDraw = now;
        const ease = 1 - Math.exp(-dt / 220);
        offsetX += (targetX - offsetX) * ease;
        offsetY += (targetY - offsetY) * ease;
        shiftX *= Math.exp(-dt / 420);
        shiftY *= Math.exp(-dt / 420);
        twinkle *= Math.exp(-dt / 380);
        draw(now);
    }

    function start(): void {
        if (running || !motionAllowed() || document.hidden) {
            return;
        }

        running = true;
        lastTick = 0;
        frame = requestAnimationFrame(tick);
    }

    function stop(): void {
        running = false;
        cancelAnimationFrame(frame);
    }

    function refreshMotion(): void {
        if (motionAllowed()) {
            start();
        } else {
            stop();
            shiftX = shiftY = twinkle = 0;
            draw(performance.now());
        }
    }

    onMounted(() => {
        readTokens();
        resize();
        start();

        const onResize = () => resize();
        const onPointer = (event: PointerEvent) => {
            targetX = -((event.clientX / width) * 2 - 1) * 14;
            targetY = -((event.clientY / height) * 2 - 1) * 14;
        };
        const onScroll = () => {
            scrollY = window.scrollY;
        };
        const onTilt = (event: DeviceOrientationEvent) => {
            if (event.gamma === null || event.beta === null) {
                return;
            }

            targetX = -Math.max(-30, Math.min(30, event.gamma)) * 0.5;
            targetY = -Math.max(-30, Math.min(30, event.beta - 45)) * 0.5;
        };
        const onVisibility = () => (document.hidden ? stop() : start());

        window.addEventListener('resize', onResize, { passive: true });
        window.addEventListener('pointermove', onPointer, { passive: true });
        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('deviceorientation', onTilt, { passive: true });
        document.addEventListener('visibilitychange', onVisibility);
        cleanups.push(
            () => window.removeEventListener('resize', onResize),
            () => window.removeEventListener('pointermove', onPointer),
            () => window.removeEventListener('scroll', onScroll),
            () => window.removeEventListener('deviceorientation', onTilt),
            () =>
                document.removeEventListener('visibilitychange', onVisibility),
        );

        // The preference can change while the page is open.
        const query = window.matchMedia('(prefers-reduced-motion: reduce)');
        query.addEventListener('change', refreshMotion);
        cleanups.push(() => query.removeEventListener('change', refreshMotion));

        // The theme and the motion switch are attributes on <html>.
        const observer = new MutationObserver(() => {
            readTokens();
            refreshMotion();
            draw(performance.now());
        });
        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['data-theme', 'data-motion'],
        });
        cleanups.push(() => observer.disconnect());

        // Each page change: the sky shifts by a good third and twinkles, the menu stays.
        const stopNavigate = router.on('navigate', () => {
            if (!motionAllowed()) {
                return;
            }

            shiftX += (Math.random() - 0.5) * 600;
            shiftY += (Math.random() - 0.5) * 400;
            twinkle = 1;
        });
        cleanups.push(stopNavigate);
    });

    onBeforeUnmount(() => {
        stop();
        cleanups.forEach((fn) => fn());
    });
}
