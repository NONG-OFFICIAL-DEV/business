import { defineStore } from 'pinia'
import kitchenService from '@/api/kitchen'

export const useKitchenStore = defineStore('kitchen', {
  state: () => ({
    orders: [],
    loading: false,
    eventSource: null,
    previousCount: 0, // ✅ track previous orders count
    audio: null,
    audioUnlocked: false,
    isConnected: false
  }),


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
    async markServed(orderId) {
      const res = await kitchenService.markServed(orderId)
      return res
    },
    async ordersStream() {
      await kitchenService.ordersStream()
    },
    // 1. THIS IS THE KEY: Call this from a BUTTON CLICK in the UI
    initAudio() {
      if (!this.audio) {
        this.audio = new Audio('/sounds/restaurant-bell.mp3')
        this.audio.preload = 'auto'
      }

      // Unlocking the "Audio Context"
      this.audio
        .play()
        .then(() => {
          this.audio.pause()
          this.audio.currentTime = 0
          this.audioUnlocked = true
          console.log('✅ Audio Unlocked and Ready')
        })
        .catch(err => {
          console.error('❌ Audio Unlock Failed. User must click first.', err)
        })
    },

    startOrdersStream() {
      if (this.eventSource) return

      this.loading = true
      const backendUrl = import.meta.env.VITE_APP_API_BASE_URL
      this.eventSource = new EventSource(`${backendUrl}/kitchen/orders/stream`)

      this.eventSource.onopen = () => {
        this.isConnected = true
        this.loading = false
      }

      this.eventSource.addEventListener('kitchen-items', event => {
        const data = JSON.parse(event.data)
        // 🔔 LOGIC:
        // 1. Must be unlocked
        // 2. data.length must be more than previous
        // 3. previousCount MUST NOT BE 0 (to avoid sound on page refresh)
        if (
          this.audioUnlocked &&
          this.previousCount > 0 &&
          data.length > this.previousCount
        ) {
          this.audio
            .play()
            .catch(e => console.warn('Playback blocked by browser logic', e))
        }

        this.orders = data
        this.previousCount = data.length
      })

      this.eventSource.onerror = () => {
        this.isConnected = false
        this.stopOrdersStream()
        // Retry connection
        setTimeout(() => this.startOrdersStream(), 5000)
      }
    },

    stopOrdersStream() {
      if (this.eventSource) {
        this.eventSource.close()
        this.eventSource = null
        this.isConnected = false
      }
    }
  }
})
