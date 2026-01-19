<script setup>
  import { ref, computed, onMounted } from 'vue'
  import { useProductStore } from '@/stores/productStore'
  import { useSaleStore } from '@/stores/salePOSStore'

  import PosAppBar from './PosAppBar.vue'
  import PosCartDrawer from './PosCartDrawer.vue'
  import PosProductGrid from './PosProductGrid.vue'
  import OrderCustomizationDialog from '@/components/pos/OrderCustomizationDialog.vue'
  import QRPaymentDialog from '@/components/pos/QRPaymentDialog.vue'

  const productStore = useProductStore()
  const saleStore = useSaleStore()

  // State
  const search = ref('')
  const paymentMethod = ref('qr')
  const cart = ref([])
  const showCustomizeDialog = ref(false)
  const selectedProduct = ref(null)
  const showQRDialog = ref(false)

  const stores = [
    { id: 1, name: 'Mart', type: 'retail' },
    { id: 2, name: 'Coffee Store', type: 'hospitality' },
    { id: 3, name: 'Restaurant', type: 'hospitality' }
  ]
  const selectedStore = ref(stores[1])

  // OPS Logic: Filter products based on selected store and search
  const filteredProducts = computed(() => {
    const data = productStore.products.data || []
    return data
  })

  const total = computed(() =>
    cart.value.reduce((s, i) => s + i.price * i.qty, 0)
  )

  // Actions
  function handleAddProductToCart(orderData) {
    const existing = cart.value.find(
      i =>
        i.id === orderData.id &&
        JSON.stringify(i.customizations) ===
          JSON.stringify(orderData.customizations)
    )
    if (existing) {
      existing.qty += orderData.qty
    } else {
      cart.value.push({ ...orderData })
    }
  }

  function handleUpdateQty({ item, change }) {
    const index = cart.value.findIndex(i => i === item)
    if (index !== -1) {
      cart.value[index].qty += change
      if (cart.value[index].qty <= 0) {
        cart.value.splice(index, 1)
      }
    }
  }

  function openCustomizer(product) {
    selectedProduct.value = product
    showCustomizeDialog.value = true
  }

  function handleQuickAdd(product) {
    handleAddProductToCart({
      id: product.id,
      name: product.name,
      price: product.price,
      image_url: product.image_url,
      qty: 1,
      customizations: {}
    })
  }

  async function handleCheckout() {
    if (!cart.value.length) return alert('Cart is empty!')
    if (paymentMethod.value === 'qr') showQRDialog.value = true
    try {
      const saleData = {
        items: cart.value.map(i => ({
          product_id: i.id,
          qty: i.qty,
          price: i.price,
          customizations: i.customizations || null
        })),
        total_amount: total.value,
        payment_method: paymentMethod.value
      }

      await saleStore.checkout(saleData)
      cart.value = []
      await productStore.fetchProducts()
    } catch {
      alert('Checkout failed')
    }
  }

  onMounted(() => productStore.fetchProducts())
</script>

<template>
  <v-layout class="bg-grey-lighten-4">
    <PosAppBar
      v-model:search="search"
      v-model:store="selectedStore"
      :stores="stores"
    />

    <PosCartDrawer
      v-model:paymentMethod="paymentMethod"
      :cart="cart"
      :total="total"
      :storeType="selectedStore.type"
      @update-qty="handleUpdateQty"
      @clear-cart="cart = []"
      @checkout="handleCheckout"
    />

    <v-main>
      <PosProductGrid
        :products="filteredProducts"
        @select="openCustomizer"
        @quick-add="handleQuickAdd"
      />
    </v-main>

    <OrderCustomizationDialog
      v-model="showCustomizeDialog"
      :product="selectedProduct"
      @add-to-cart="handleAddProductToCart"
    />

    <QRPaymentDialog v-model="showQRDialog" :total="total" />
  </v-layout>
</template>
