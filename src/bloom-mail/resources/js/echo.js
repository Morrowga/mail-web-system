import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    //wssPort: import.meta.env.VITE_REVERB_PORT_WSS ?? 443,
    forceTLS: import.meta.env.VITE_REVERB_TLS == 'false' ? false: true,
    enabledTransports: ['ws', import.meta.env.VITE_REVERB_WS_CONFIG],
});
