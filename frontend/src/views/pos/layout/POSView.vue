<script setup>
  import { ref, computed, onMounted } from 'vue'
  import { useRouter } from 'vue-router'
  import { useI18n } from 'vue-i18n'

  /* STORES */
  import { usePosStore } from '@/stores/posStore'
  import { useProductStore } from '@/stores/productStore'
  import { useSaleStore } from '@/stores/salePOSStore'
  import { useMenuStore } from '@/stores/menuStore'
  import { useCategoryMenuStore } from '@/stores/categoryMenu'
  import { useOrderStore } from '@/stores/orderStore'
  import { usePaymentStore } from '@/stores/payment'
  import { useAuthStore } from '@/stores/auth'
  /* COMPONENTS */
  import PosAppBar from '@/components/pos/layout/PosAppBar.vue'
  import SidebarMenu from '@/components/pos/layout/SidebarMenu.vue'
  import PosCartDrawer from '@/components/pos/layout/PosCartDrawer.vue'
  import OrderCustomizationDialog from '@/components/pos/OrderCustomizationDialog.vue'
  import QRPaymentDialog from '@/components/pos/QRPaymentDialog.vue'

  /* COMPOSABLES */
  import { useAppUtils } from '@/composables/useAppUtils'

  /* -------------------------
STORES / UTILITIES
--------------------------*/
  const posStore = usePosStore()
  const productStore = useProductStore()
  const saleStore = useSaleStore()
  const menuStore = useMenuStore()
  const categoryStore = useCategoryMenuStore()
  const orderStore = useOrderStore()
  const paymentStore = usePaymentStore()
  const authStore = useAuthStore()

  const router = useRouter()
  const { t } = useI18n()
  const { notif } = useAppUtils()

  /* -------------------------
LOCAL STATE
--------------------------*/
  const search = ref('')
  const selectedProduct = ref(null)
  const showCustomizeDialog = ref(false)
  const showQRDialog = ref(false)
  const user = ref(null)

  /* -------------------------
COMPUTED
--------------------------*/
  // Filter products or menu based on store type and search term
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

  // Use POS store computed: activeItems, subtotal, total
  const activeItems = computed(() => posStore.activeItems)
  const subtotal = computed(() => posStore.subtotal)
  const total = computed(() => posStore.total)

  /* -------------------------
ACTIONS
--------------------------*/
  function handleAddProductToCart(item) {
    posStore.addToCart(item)
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

  function openCustomizer(product) {
    selectedProduct.value = product
    showCustomizeDialog.value = true
  }

  async function handleCheckout() {
    if (!activeItems.value.length) return alert('Cart is empty!')

    try {
      if (posStore.selectedStore.type === 'retail') {
        // Retail / Mart: direct checkout
        const saleData = {
          items: activeItems.value.map(i => ({
            product_id: i.id,
            qty: i.qty,
            price: i.price,
            customizations: i.customizations || null
          })),
          total_amount: total.value,
          payment_method: posStore.paymentMethod
        }
        await saleStore.checkout(saleData)
      }

      // Hospitality / restaurant: create order for table
      if (posStore.selectedStore.type === 'hospitality') {
        const orderData = {
          table_id: posStore.selectedTable?.id || 1,
          items: activeItems.value.map(i => ({
            menu_id: i.id,
            quantity: i.qty,
            price: i.price,
            note: 'Test'
          }))
        }
        await orderStore.createOrder(orderData, 'overlay')
        await menuStore.fetchMenus()
      }

      // Handle QR payment dialog if QR method
      if (posStore.paymentMethod === 'qr') {
        showQRDialog.value = true
      }

      // Clear cart and refresh products
      posStore.clearCart()
      await productStore.fetchProducts()
    } catch (error) {
      console.error(error)
      alert('Checkout failed!')
    }
  }

  async function handlePrintBill() {
    // Print the selected bill
    const selectOrderId = posStore.orderId
    const res = await saleStore.printBillForPayment(selectOrderId)
    if (res.status == 200) {
      await orderStore.fetchAllOrders()
      window.open(res.data.invoice_url, '_blank')
    }
    console.log(res.status)
  }

  async function handleLogout() {
    await authStore.logout()
    notif(t('messages.logout_sucess'), { type: 'success', color: 'primary' })
    router.push({ name: 'Login' })
  }

  const handleScanBarcode = async barcode => {
    try {
      const product = await productStore.scanProduct(barcode)
      handleAddProductToCart({
        ...product,
        qty: 1, // force qty = 1
        customizations: {}
      })
    } catch {
      notif(t('messages.error'), { type: 'error', color: 'primary' })
    }
  }

  /* -------------------------
ON MOUNT
--------------------------*/
  onMounted(async () => {
    await menuStore.fetchMenus()
    await productStore.fetchProducts()
    await categoryStore.fetchAll({loading:'overlay'})
    try {
      await authStore.fetchMe()
      user.value = authStore.me
    } catch {
      await authStore.logout()
      router.push({ name: 'Login' })
    }
  })
</script>

<template>
  <!-- APPBAR -->
  <PosAppBar v-model:search="search" :user="user" @logout="handleLogout" />

  <!-- SIDEBAR MENU (Hospitality only) -->
  <SidebarMenu v-if="posStore.selectedStore.type === 'hospitality'" />

  <!-- CART DRAWER -->
  <PosCartDrawer
    :items="activeItems"
    :subtotal="subtotal"
    :total="total"
    :payment-method="posStore.paymentMethod"
    :payment-methods="posStore.paymentMethods"
    @checkout="handleCheckout"
    @print-bill="handlePrintBill"
    @scan="handleScanBarcode"
  />

  <!-- MAIN VIEW -->
  <v-main>
    <v-container class="px-4" fluid>
      <router-view v-slot="{ Component }" :key="$route.fullPath">
        <component
          :is="Component"
          :filtered-products="filteredProducts"
          @quick-add="handleQuickAdd"
          @select="openCustomizer"
        />
      </router-view>
    </v-container>
  </v-main>

  <!-- DIALOGS -->
  <OrderCustomizationDialog
    v-model="showCustomizeDialog"
    :product="selectedProduct"
    @add-to-cart="handleAddProductToCart"
  />

  <QRPaymentDialog v-model="showQRDialog" :total="total" />
</template>
<style scoped>
  .cart-anchor {
    position: fixed;
    bottom: 24px; /* Space from bottom edge */
    left: 0;
    right: 0;
    z-index: 999;
    max-width: 450px; /* Optional: Keep it centered on larger screens */
    margin: 0 auto;
    margin-right: 45%;
  }
</style>
