import { defineStore } from 'pinia'
import kitchenService from '@/api/kitchen'

export const useKitchenStore = defineStore('kitchen', {
  state: () => ({
    orders: [],
    loading: false,
    eventSource: null,
    previousCount: 0 // ✅ track previous orders count
  }),

  getters: {
    pendingOrders: state =>
      state.orders.filter(o => o.kitchen_status === 'pending'),

    preparingOrders: state =>
      state.orders.filter(o => o.kitchen_status === 'preparing'),

    readyOrders: state => state.orders.filter(o => o.kitchen_status === 'ready')
  },

  actions: {
    async fetchOrders() {
      this.loading = true
      const { data } = await kitchenService.getOrders()
      this.orders = data
      this.loading = false
    },

    async startCooking(orderId) {
      await kitchenService.startCooking(orderId)
      await this.fetchOrders()
    },

    async markReady(orderId) {
      await kitchenService.markReady(orderId)
      await this.fetchOrders()
    },

    async ordersStream() {
      await kitchenService.ordersStream()
    },
    // 🔥 Start SSE
    startOrdersStream() {
      if (this.eventSource) return

      this.loading = true

      this.eventSource = new EventSource('/api/kitchen/orders/stream')

      this.eventSource.addEventListener('orders', event => {
        const data = JSON.parse(event.data)

        // 🔔 Play sound ONLY when new order arrives
        if (data.length > this.previousCount) {
          new Audio('/sounds/restaurant-bell.mp3').play()
        }

        this.previousCount = data.length
        this.orders = data
        this.loading = false
      })

      this.eventSource.onerror = () => {
        console.error('SSE connection error')
        this.stopOrdersStream()
      }
    },

    // 🛑 Stop SSE
    stopOrdersStream() {
      if (this.eventSource) {
        this.eventSource.close()
        this.eventSource = null
      }
    }
  }
})
