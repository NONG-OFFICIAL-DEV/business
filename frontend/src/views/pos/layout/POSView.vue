<script setup>
  import { ref, computed, onMounted } from 'vue'
  import { useProductStore } from '@/stores/productStore'
  import { useSaleStore } from '@/stores/salePOSStore'

  import PosAppBar from '@/components/pos/layout/PosAppBar.vue'
  import PosCartDrawer from '@/components/pos/layout/PosCartDrawer.vue'
  import PosProductGrid from '@/components/pos/layout/PosProductGrid.vue'
  import OrderCustomizationDialog from '@/components/pos/OrderCustomizationDialog.vue'
  import QRPaymentDialog from '@/components/pos/QRPaymentDialog.vue'
  import { useMenuStore } from '@/stores/menuStore'
  import { useCategoryMenuStore } from '@/stores/categoryMenu'

  const categoryStore = useCategoryMenuStore()
  const productStore = useProductStore()
  const saleStore = useSaleStore()
  const menuStore = useMenuStore()

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
    if (selectedStore.value.type === 'hospitality') {
      const data = menuStore.menus?.data || []
      if (!search.value) return data
      return data.filter(p =>
        p.name.toLowerCase().includes(search.value.toLowerCase())
      )
    } else {
      const data = productStore.products?.data || []
      if (!search.value) return data
      return data.filter(p =>
        p.name.toLowerCase().includes(search.value.toLowerCase())
      )
    }
    // const data = productStore.products.data || []
    // return data
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

  onMounted(() => {
    // if (selectedStore.value.type === 'hospitality') {
    menuStore.fetchMenus()
    // } else {
    productStore.fetchProducts()
    // }
    categoryStore.fetchAll()
  })
</script>

<template>
  <v-layout class="bg-grey-lighten-4">
    <PosAppBar
      v-model:search="search"
      v-model:store="selectedStore"
      :stores="stores"
    />
    <v-navigation-drawer rail permanent v-if="selectedStore.name === 'Restaurant'">
      <v-list nav>
        <v-tooltip text="Dining Table" location="right">
          <template v-slot:activator="{ props }">
            <v-list-item
              v-bind="props"
              prepend-icon="mdi-apps"
              value="Dining Table"
              to="/dining-table"
            ></v-list-item>
          </template>
        </v-tooltip>

        <v-tooltip text="Kitchen Display" location="right">
          <template v-slot:activator="{ props }">
            <v-list-item
              v-bind="props"
              prepend-icon="mdi-silverware"
              value="Menu"
              to="/kds"
            ></v-list-item>
          </template>
        </v-tooltip>

        <v-tooltip text="Cashier Desk" location="right">
          <template v-slot:activator="{ props }">
            <v-list-item
              v-bind="props"
              prepend-icon="mdi-cash-register"
              value="cashier"
              to="/cashier"
            ></v-list-item>
          </template>
        </v-tooltip>

        <v-tooltip text="Sales Reports" location="right">
          <template v-slot:activator="{ props }">
            <v-list-item
              v-bind="props"
              prepend-icon="mdi-chart-bar"
              value="reports"
              to="/reports"
            ></v-list-item>
          </template>
        </v-tooltip>
      </v-list>
    </v-navigation-drawer>
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
      <!-- <PosProductGrid
        :products="filteredProducts"
        :categories="categoryStore.items"
        :mode="selectedStore.type"
        @select="openCustomizer"
        @quick-add="handleQuickAdd"
      /> -->
      <router-view />
    </v-main>

    <OrderCustomizationDialog
      v-model="showCustomizeDialog"
      :product="selectedProduct"
      @add-to-cart="handleAddProductToCart"
    />

    <QRPaymentDialog v-model="showQRDialog" :total="total" />
  </v-layout>
</template>
