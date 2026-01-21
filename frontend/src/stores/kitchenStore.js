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
      const backendUrl = import.meta.env.VITE_APP_API_BASE_URL
      this.eventSource = new EventSource(`${backendUrl}/kitchen/orders/stream`)

      // Ensure audio can play after user interaction
      let audioAllowed = false
      const audio = new Audio('/sounds/restaurant-bell.mp3')
      const enableAudio = () => {
        audioAllowed = true
      }
      document.addEventListener('click', enableAudio, { once: true })

      this.eventSource.addEventListener('orders', event => {
        const data = JSON.parse(event.data)

        // 🔔 Play sound ONLY if user interacted and new order arrives
        if (audioAllowed && data.length > this.previousCount) {
          audio.play().catch(() => {})
        }

        this.previousCount = data.length
        this.orders = data
        this.loading = false
      })

      this.eventSource.onerror = () => {
        // console.error('SSE connection error')
        this.stopOrdersStream()
        // Optional: retry after a delay
        setTimeout(() => this.startOrdersStream(), 5000)
      }
    },

    stopOrdersStream() {
      if (this.eventSource) {
        this.eventSource.close()
        this.eventSource = null
      }
    }
  }
})
