import http from './api'

export default {
  getOrders() {
    return http.get('/kitchen/orders')
  },
  startCooking(orderId) {
    return http.patch(`/kitchen/orders/${orderId}/start`)
  },
  markReady(orderId) {
    return http.patch(`/kitchen/orders/${orderId}/ready`)
  },
  markServed(orderId) {
    return http.put(`/kitchen/orders/mark-served/${orderId}`)
  },
  updateItemStatus(itemId, newStatus) {
    return http.patch(`/order-items/${itemId}/status`, { kitchen_status: newStatus })
  },
  // ordersStream() {
  //   return http.get('/kitchen/orders/stream', {
  //     headers: { Accept: 'text/event-stream' },
  //     responseType: 'stream'
  //   })
  // }
}
