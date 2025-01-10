import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

document.addEventListener('DOMContentLoaded', () => {
    const checkMetaTag = () => {
        const userIdMeta = document.head.querySelector('meta[name="user-id"]');
        if (userIdMeta) {
            const userId = userIdMeta.content;

            window.Echo.channel(`transcriptions.${userId}`)
                .subscribed(() => {
                    console.log(`Successfully subscribed to the transcriptions channel for user ${userId}.`);
                })
                .listen('TranscriptionStarted', (e) => {
                    console.log('Transcription started:', e);
                })
                .listen('TranscriptionCompleted', (e) => {
                    console.log('Transcription completed:', e);
                });
        } else {
            console.error('Meta tag with name "user-id" not found.');
        }
    };

    // Retry checking for the meta tag every 100ms until found
    const intervalId = setInterval(() => {
        if (document.head.querySelector('meta[name="user-id"]')) {
            clearInterval(intervalId);
            checkMetaTag();
        }
    }, 100);
});
