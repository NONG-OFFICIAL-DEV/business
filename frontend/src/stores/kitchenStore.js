import { defineStore } from "pinia";
import kitchenService from "@/api/kitchen";

export const useKitchenStore = defineStore("kitchen", {
  state: () => ({
    orders: [],
    loading: false
  }),

  getters: {
    pendingOrders: state =>
      state.orders.filter(o => o.kitchen_status === "pending"),

    preparingOrders: state =>
      state.orders.filter(o => o.kitchen_status === "preparing"),

    readyOrders: state =>
      state.orders.filter(o => o.kitchen_status === "ready")
  },

  actions: {
    async fetchOrders() {
      this.loading = true;
      const { data } = await kitchenService.getOrders();
      this.orders = data;
      this.loading = false;
    },

    async startCooking(orderId) {
      await kitchenService.startCooking(orderId);
      await this.fetchOrders();
    },

    async markReady(orderId) {
      await kitchenService.markReady(orderId);
      await this.fetchOrders();
    }
  }
});
