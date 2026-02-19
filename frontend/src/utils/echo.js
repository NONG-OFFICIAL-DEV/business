import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

window.Pusher = Pusher

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: 'local', // must match laravel-echo-server.json
  wsHost: '127.0.0.1', // host of Echo server
  wsPort: 6001,
  forceTLS: false,
  disableStats: true,
  enabledTransports: ['ws', 'wss'] // optional, ensures WebSocket only
})

window.Echo.channel('orders').listen('OrderCreated', e => {
  console.log('New order:', e.order)
  // Update your Vue store/orders array here
})
