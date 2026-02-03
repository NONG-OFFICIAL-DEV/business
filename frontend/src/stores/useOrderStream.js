// stores/useOrderStream.js
import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useOrderStream = defineStore('orderStream', () => {
  const order = ref(null)
  const isConnected = ref(false)
  const loading = ref(false)

  let eventSource = null
  let currentTableId = null
  let retryTimeout = null

  function connect(tableId) {
    if (eventSource) return

    currentTableId = tableId
    loading.value = true

    const backendUrl = import.meta.env.VITE_APP_API_BASE_URL
    eventSource = new EventSource(
      `${backendUrl}/orders/stream/by-table/${tableId}`
    )

    eventSource.onopen = () => {
      isConnected.value = true
      loading.value = false
      console.log('✅ Table order stream connected')
    }

    eventSource.addEventListener('order', event => {
      const data = JSON.parse(event.data)
      order.value = data
    })

    eventSource.onerror = () => {
      console.warn('⚠️ Table order stream error')
      isConnected.value = false
      loading.value = false
      disconnect(false)

      // retry
      retryTimeout = setTimeout(() => {
        connect(currentTableId)
      }, 3000)
    }
  }

  function disconnect(clearData = true) {
    if (eventSource) {
      eventSource.close()
      eventSource = null
    }

    if (retryTimeout) {
      clearTimeout(retryTimeout)
      retryTimeout = null
    }

    isConnected.value = false

    if (clearData) {
      order.value = null
    }
  }

  return {
    order,
    isConnected,
    loading,
    connect,
    disconnect
  }
})
