import { defineStore } from 'pinia'
import { ref, computed, watch } from 'vue'

export const useCartStore = defineStore('cart', () => {
  const cart = ref([]) // [{id, name, price, image, qty}]

  /** Load cart from localStorage */
  const loadCart = () => {
    const saved = localStorage.getItem('cart')
    if (saved) cart.value = JSON.parse(saved)
  }

  /** Save cart to localStorage */
  watch(
    cart,
    val => {
      localStorage.setItem('cart', JSON.stringify(val))
    },
    { deep: true }
  )

  const cartCount = computed(() => {
    return cart.value.reduce((sum, item) => sum + item.qty, 0)
  })

  const cartTotal = computed(() => {
    return cart.value.reduce((sum, item) => sum + item.price * item.qty, 0)
  })

  const addToCart = product => {
    const exist = cart.value.find(i => i.id === product.id)

    if (exist) {
      exist.qty += 1
    } else {
      cart.value.push({
        id: product.id,
        name: product.name,
        price: product.price,
        image: product.image,
        qty: 1
      })
    }
  }

  const setQty = (id, qty) => {
    const item = cart.value.find(i => i.id === id)
    if (!item) return

    item.qty = Math.max(1, qty)
  }

  const removeItem = id => {
    cart.value = cart.value.filter(i => i.id !== id)
  }

  const clearCart = () => {
    cart.value = []
  }

  // Auto load cart once
  loadCart()

  return {
    cart,
    cartCount,
    cartTotal,
    addToCart,
    setQty,
    removeItem,
    clearCart
  }
})
