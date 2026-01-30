import http from './api'

export default {
  getOrders() {
    return http.get('/kitchen/orders')
  },
  startCooking(orderItemId) {
    return http.patch(`/kitchen/orders/${orderItemId}/start`)
  },
  markReady(orderItemId) {
    return http.patch(`/kitchen/orders/${orderItemId}/ready`)
  },
  markServed(orderItemId) {
    return http.put(`/kitchen/orders/${orderItemId}/mark-served`)
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
