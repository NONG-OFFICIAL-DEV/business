import { defineStore } from "pinia";
import orderService from "@/api/order";

export const useOrderStore = defineStore("order", {
  state: () => ({
    orders: [],
    loading: false
  }),


  actions: {
    async createOrder(payload) {
      this.loading = true;
      const { data } = await orderService.createOrder(payload);
      this.orders = data;
      this.loading = false;
    }
  }
});
