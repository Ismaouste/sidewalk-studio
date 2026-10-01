// The server-side render, as a serverless function. The PHP gateway (App\Support\Ssr\TimedHttpGateway) posts an Inertia page here;
// the bundle built by `vite build --ssr resources/js/ssr-render.ts` returns the `{ head, body }` it puts in the document.
// If this fails or is slow, the gateway gives up and the browser renders the page, as it always did: nothing depends on it.
import render from '../../bootstrap/ssr-render/ssr-render.js';

export default async function handler(request, response) {
    if (request.method !== 'POST') {
        response.status(405).setHeader('Allow', 'POST').end();

        return;
    }

    try {
        const page = typeof request.body === 'string' ? JSON.parse(request.body) : request.body;

        if (!page || typeof page.component !== 'string') {
            response.status(400).json({ error: 'Not an Inertia page.' });

            return;
        }

        const rendered = await render(page);

        response.setHeader('Cache-Control', 'no-store');
        response.status(200).json({ head: rendered.head, body: rendered.body });
    } catch (error) {
        response.status(500).json({ error: String(error?.message ?? error), type: 'render' });
    }
}
