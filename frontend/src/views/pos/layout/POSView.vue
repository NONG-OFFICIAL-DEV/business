<script setup>
  import { ref, computed, onMounted } from 'vue'
  import { useProductStore } from '@/stores/productStore'
  import { useSaleStore } from '@/stores/salePOSStore'
  import { useMenuStore } from '@/stores/menuStore'
  import { useCategoryMenuStore } from '@/stores/categoryMenu'
  import { usePosStore } from '@/stores/posStore'

  import PosAppBar from '@/components/pos/layout/PosAppBar.vue'
  import PosCartDrawer from '@/components/pos/layout/PosCartDrawer.vue'
  import OrderCustomizationDialog from '@/components/pos/OrderCustomizationDialog.vue'
  import QRPaymentDialog from '@/components/pos/QRPaymentDialog.vue'
  import SidebarMenu from '../../../components/pos/layout/SidebarMenu.vue'
  /* -------------------------
STORES
--------------------------*/
  const posStore = usePosStore()
  const productStore = useProductStore()
  const saleStore = useSaleStore()
  const menuStore = useMenuStore()
  const categoryStore = useCategoryMenuStore()

  /* -------------------------
LOCAL STATE
--------------------------*/
  const search = ref('')
  const selectedProduct = ref(null)
  const showCustomizeDialog = ref(false)
  const showQRDialog = ref(false)

  /* -------------------------
COMPUTED
--------------------------*/
  const filteredProducts = computed(() => {
    const term = search.value.toLowerCase()
    if (posStore.selectedStore.type === 'hospitality') {
      const data = menuStore.menus?.data || []
      return term ? data.filter(p => p.name.toLowerCase().includes(term)) : data
    } else {
      const data = productStore.products?.data || []
      return term ? data.filter(p => p.name.toLowerCase().includes(term)) : data
    }
  })

  const total = computed(() =>
    posStore.cart.reduce((sum, item) => sum + item.price * item.qty, 0)
  )

  /* -------------------------
ACTIONS
--------------------------*/
  function handleAddProductToCart(orderData) {
    posStore.addToCart(orderData)
  }

  function handleUpdateQty({ item, change }) {
    posStore.updateQty(
      item.id,
      posStore.cart.find(i => i === item).qty + change
    )
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
    if (!posStore.cart.length) return alert('Cart is empty!')
    try {
      const saleData = {
        items: posStore.cart.map(i => ({
          product_id: i.id,
          qty: i.qty,
          price: i.price,
          customizations: i.customizations || null
        })),
        total_amount: total.value,
        payment_method: posStore.paymentMethod
      }

      if (posStore.paymentMethod === 'qr') showQRDialog.value = true

      await saleStore.checkout(saleData)
      posStore.clearCart()
      await productStore.fetchProducts()
    } catch {
      alert('Checkout failed!')
    }
  }

  /* -------------------------
ON MOUNT
--------------------------*/
  onMounted(() => {
    menuStore.fetchMenus()
    productStore.fetchProducts()
    categoryStore.fetchAll()
  })
</script>

<template>
    <!-- APPBAR -->
    <PosAppBar v-model:search="search" />
    <!-- SIDEBAR MENU -->
    <SidebarMenu v-if="posStore.selectedStore.name == 'Restaurant'"/>
    <!-- CART DRAWER -->
    <PosCartDrawer
      @checkout="handleCheckout"
    />

    <!-- MAIN VIEW -->
    <v-main>
      <router-view v-slot="{ Component }" :key="$route.fullPath">
        <component
          :is="Component"
          :filtered-products="filteredProducts"
          @quick-add="handleQuickAdd"
          @select="openCustomizer"
        />
      </router-view>
    </v-main>

    <!-- DIALOGS -->
    <OrderCustomizationDialog
      v-model="showCustomizeDialog"
      :product="selectedProduct"
      @add-to-cart="handleAddProductToCart"
    />

    <QRPaymentDialog v-model="showQRDialog" :total="total" />
</template>
