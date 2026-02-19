import Echo from 'laravel-echo';
import io from 'socket.io-client';  // ← replace pusher-js

window.io = io;  // ← NOT window.Pusher

window.Echo = new Echo({
  broadcaster: 'socket.io',          // ← was 'pusher'
  host: 'http://127.0.0.1:6001',     // ← single host field, not wsHost/wsPort
  // remove: key, forceTLS, encrypted, disableStats, enabledTransports, cluster
});

window.Echo.channel('orders')
  .listen('OrderCreated', e => {
    console.log('New order:', e.order);
  });