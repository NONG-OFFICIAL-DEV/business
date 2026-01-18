import { ref, computed } from 'vue'

export function useCart() {
  const cart = ref([])

  const totalItems = computed(() =>
    cart.value.reduce((s, p) => s + p.qty, 0)
  )

  const cartTotal = computed(() =>
    cart.value.reduce((s, p) => s + p.price * p.qty, 0)
  )

  function addToCart(product) {
    const existing = cart.value.find(p => p.id === product.id)
    if (existing) existing.qty++
    else cart.value.push({ ...product, qty: 1 })
  }

  function updateQty(id, delta) {
    const item = cart.value.find(p => p.id === id)
    if (!item) return

    item.qty += delta
    if (item.qty <= 0) {
      cart.value = cart.value.filter(p => p.id !== id)
    }
  }

  function clearCart() {
    cart.value = []
  }

  return {
    cart,
    totalItems,
    cartTotal,
    addToCart,
    updateQty,
    clearCart
  }
}
