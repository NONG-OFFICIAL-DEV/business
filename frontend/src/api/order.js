import http from "./api";

export default {
  createOrder(payload) {
    return http.post("/orders", payload);
  },
  getOrderByTable(tableNumber) {
    return http.get(`/orders/by-table/${tableNumber}`);
  }
};
