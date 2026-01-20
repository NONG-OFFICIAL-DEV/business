import http from "./api";

export default {
  createOrder(payload) {
    return http.post("/orders", payload);
  }
};
