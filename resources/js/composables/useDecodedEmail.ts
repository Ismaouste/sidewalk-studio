import { onMounted, ref } from 'vue';
import type { Ref } from 'vue';

const PREFIX = 'b64:';

/** An address as the server sent it (`b64:` and base64) back to plain text; anything else is returned as is. */
export function decodeEmail(value: string): string {
    if (!value.startsWith(PREFIX)) {
        return value;
    }

    try {
        return atob(value.slice(PREFIX.length));
    } catch {
        return '';
    }
}

/**
 * The plain address, once the page is in the browser.
 *
 * Empty on the server and during hydration, so the markup a crawler fetches has no address in it; filled right after mounting, which
 * is when a mailto link can be built. Use `mailto` for the link: it is `undefined` until then.
 */
export function useDecodedEmail(source: () => string): {
    email: Ref<string>;
    mailto: () => string | undefined;
} {
    const email = ref('');

    onMounted(() => {
        email.value = decodeEmail(source());
    });

    return {
        email,
        mailto: () => (email.value ? `mailto:${email.value}` : undefined),
    };
}
