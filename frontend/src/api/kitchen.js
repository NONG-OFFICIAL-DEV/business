import http from "./api";

export default {
  getOrders() {
    return http.get("/kitchen/orders");
  },
  startCooking(orderId) {
    return http.patch(`/kitchen/orders/${orderId}/start`);
  },
  markReady(orderId) {
    return http.patch(`/kitchen/orders/${orderId}/ready`);
  }
};
