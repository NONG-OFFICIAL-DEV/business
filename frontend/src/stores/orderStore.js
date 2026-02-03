import { defineStore } from 'pinia'
import orderService from '@/api/order'

export const useOrderStore = defineStore('order', {
  state: () => ({
    orders: [],
    loading: false
  }),

  actions: {
    async createOrder(payload, loading) {
      this.loading = true
      const res = await orderService.createOrder(payload, loading)
      this.orders = res
      this.loading = false
      return res
    },
    async fetchOrderByTable(tableNumber) {
      const { data } = await orderService.getOrderByTable(tableNumber)
      return data
    },
    async fetchAllOrders() {
      const { data } = await orderService.getAllOrder()
      this.orders = data
      return data
    }
  }
})
