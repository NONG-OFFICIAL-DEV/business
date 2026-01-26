<script setup>
  import { ref, computed, onMounted } from 'vue'
  import { useProductStore } from '@/stores/productStore'
  import { useSaleStore } from '@/stores/salePOSStore'
  import { useMenuStore } from '@/stores/menuStore'
  import { useCategoryMenuStore } from '@/stores/categoryMenu'
  import { usePosStore } from '@/stores/posStore'
  import { useOrderStore } from '@/stores/orderStore'
  import { useAuthStore } from '@/stores/auth'
  import { useRouter } from 'vue-router'
  import { useAppUtils } from '@/composables/useAppUtils'
  import { useI18n } from 'vue-i18n'

  import PosAppBar from '@/components/pos/layout/PosAppBar.vue'
  import PosCartDrawer from '@/components/pos/layout/PosCartDrawer.vue'
  import OrderCustomizationDialog from '@/components/pos/OrderCustomizationDialog.vue'
  import QRPaymentDialog from '@/components/pos/QRPaymentDialog.vue'
  import SidebarMenu from '../../../components/pos/layout/SidebarMenu.vue'
  /* -------------------------
STORES
--------------------------*/
  const { t } = useI18n()
  const { notif } = useAppUtils()
  const posStore = usePosStore()
  const productStore = useProductStore()
  const saleStore = useSaleStore()
  const menuStore = useMenuStore()
  const categoryStore = useCategoryMenuStore()
  const orderStore = useOrderStore()
  const authStore = useAuthStore()
  const router = useRouter()
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
      if (posStore.selectedStore.type === 'retail') {
        // Retail / Mart
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

        //for mart sale direcly or order
        await saleStore.checkout(saleData)
      }

      // Handle QR payment dialog
      if (posStore.paymentMethod === 'qr') {
        showQRDialog.value = true
      }

      // If hospitality, also create order for table
      if (posStore.selectedStore.type === 'hospitality') {
        const orderData = {
          table_id: posStore.selectedTable?.id || 1,
          items: posStore.cart.map(i => ({
            menu_id: i.id,
            quantity: i.qty,
            price: i.price,
            note: 'Test'
            // customizations:i.customizations
          }))
        }
        await orderStore.createOrder(orderData)
        await menuStore.fetchMenus()
      }

      // Clear cart and refresh products
      posStore.clearCart()
      await productStore.fetchProducts()
    } catch (error) {
      console.error(error)
      alert('Checkout failed!')
    }
  }

  const handlePrintBill = () => {
    const saleId = 2
    window.open(`/sales/${saleId}/invoice`, '_blank')
    // Restaurant flow
    console.log('test')
    // printBillOnly()
  }
  const handleLogout = async () => {
    await authStore.logout()
    // notif(t('messages.logout_sucess'), {
    //   type: 'success',
    //   color: 'primary'
    // })
    router.push({ name: 'Login' })
  }
  /* -------------------------
  ON MOUNT
  --------------------------*/
  onMounted(async () => {
    await menuStore.fetchMenus()
    await productStore.fetchProducts()
    await categoryStore.fetchAll()
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
  <!-- SIDEBAR MENU -->
  <SidebarMenu v-if="posStore.selectedStore.name == 'Restaurant'" />
  <!-- CART DRAWER -->
  <PosCartDrawer @checkout="handleCheckout" @print-bill="handlePrintBill" />
  <!-- MAIN VIEW -->
  <v-main>
    <v-container class="px-4" fluid>
      <router-view v-slot="{ Component }" :key="$route.fullPath">
        {{ posStore.selectedStore.type }}
        {{ posStore.selectedStore.name }}
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
