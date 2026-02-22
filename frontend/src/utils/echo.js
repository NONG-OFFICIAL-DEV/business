import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

window.Pusher = Pusher
// Disable Pusher's default logging
Pusher.logToConsole = false

const echo = new Echo({
  broadcaster: 'reverb',
  key: import.meta.env.VITE_REVERB_APP_KEY,
  wsHost: import.meta.env.VITE_REVERB_HOST,
  wsPort: import.meta.env.VITE_REVERB_PORT,
  wssPort: import.meta.env.VITE_REVERB_PORT,
  forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
  enabledTransports: ['ws', 'wss'], // ← only use WebSocket, skip other transports
  disableStats: true // ← stop pinging Pusher stats servers
})

export default echo
