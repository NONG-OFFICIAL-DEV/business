import http from "./api";

export default {
  createOrder(payload) {
    return http.post("/orders", payload);
  },
  getOrderByTable(tableNumber) {
    return http.get(`/orders/by-table/${tableNumber}`);
  },
  markServed(orderId) {
    return http.get(`/orders/mark-served/${orderId}`);
  }
};
