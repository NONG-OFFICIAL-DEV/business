<script setup>
  import { ref, computed, onMounted } from 'vue'
  import { useCart } from '@/composables/useCart'
  import { useRoute } from 'vue-router'

  import AppHeader from '@/components/mobile/AppHeader.vue'
  import CategoryTabs from '@/components/mobile/CategoryTabs.vue'
  import ProductCard from '@/components/mobile/ProductCard.vue'
  import CartButton from '@/components/mobile/CartButton.vue'
  import CartView from '@/components/mobile/CartView.vue'
  import TrackingView from '@/components/mobile/TrackingView.vue'
  import { useOrderStore } from '@/stores/orderStore'
  import { useMenuStore } from '@/stores/menuStore'
  import { useDiningTableStore } from '../../stores/diningTableStore'

  // const tableNumber = ref('05')
  const orderStore = useOrderStore()
  const menuStore = useMenuStore()
  const diningTableStore = useDiningTableStore()
  const route = useRoute()
  const token = route.params.token
  const tableNumber = ref()
  const tableId = ref()

  onMounted(async () => {
    const params = new URLSearchParams(window.location.search)
    if (params.get('table')) tableNumber.value = params.get('table')
    await menuStore.fetchMenus()
    const res = await diningTableStore.getTableNumberByToken(token)
    tableNumber.value = res.table.table_number
    tableId.value = res.table.id
    // console.log(res.table.table_number)
  })

  const categories = ['All', 'Coffee', 'Tea', 'Pastries', 'Food']
  const selectedCategory = ref('All')
  const page = ref('home')
  const isOrdering = ref(false)
  const { cart, totalItems, cartTotal, addToCart, updateQty, clearCart } =
    useCart()
  const viewProcess = ref(false)

  async function placeOrder() {
    isOrdering.value = true
    try {
      // Prepare backend payload
      const orderData = {
        table_id: tableId.value,
        items: cart.value.map(i => ({
          menu_id: i.id,
          quantity: i.qty,
          note: i.customizations || null
        }))
      }

      // Call API via order store
      await orderStore.createOrder(orderData)
      await menuStore.fetchMenus()
      // Go to tracking page
      page.value = 'tracking'
    } catch (err) {
      console.error(err)
    } finally {
      isOrdering.value = false
    }
  }

  function goToTracking() {
    page.value = 'tracking'
  }

  function handleReset() {
    clearCart()
    page.value = 'home'
  }
  const search = ref('')

  const filteredProducts = computed(() => {
    let list = menuStore.menus.data || []
    if (selectedCategory.value !== 'All') {
      list = list.filter(p => p.category === selectedCategory.value)
    }

    if (search.value) {
      list = list.filter(p =>
        p.name.toLowerCase().includes(search.value.toLowerCase())
      )
    }

    return list
  })
</script>

<template>
  <v-app class="bg-grey-lighten-5">
    <AppHeader
      v-if="page === 'home'"
      @view-process="goToTracking"
      :tableNumber="tableNumber"
    />
    <v-main>
      <template v-if="page === 'home'">
        <div class="sticky-nav bg-white shadow-sm">
          <CategoryTabs
            :categories="categories"
            v-model="selectedCategory"
            v-model:search="search"
          />
        </div>

        <v-container class="pb-16">
          <v-row>
            <v-col
              v-for="p in filteredProducts"
              :key="p.id"
              cols="12"
              class="py-1"
            >
              <ProductCard
                :product="p"
                @add="addToCart"
                :qty="cart.find(i => i.id === p.id)?.qty || 0"
                @update="updateQty"
              />
            </v-col>
          </v-row>

          <div v-if="filteredProducts.length === 0" class="text-center py-10">
            <v-icon size="48" color="grey-lighten-1">mdi-magnify-close</v-icon>
            <p class="text-grey mt-2">No items found matching your search.</p>
          </div>
        </v-container>
      </template>

      <CartView
        v-if="page === 'cart'"
        :cart="cart"
        :total="cartTotal"
        :tableNumber="tableNumber"
        :loading="isOrdering"
        @back="page = 'home'"
        @update="updateQty"
        @submit="placeOrder"
        @clear="clearCart"
      />

      <TrackingView
        v-if="page === 'tracking'"
        :cart="cart"
        :tableNumber="tableNumber"
        :tableId="tableId"
        v-model="viewProcess"
        @reset="handleReset"
      />
    </v-main>

    <CartButton
      v-if="cart.length && page === 'home'"
      :totalItems="totalItems"
      :totalPrice="cartTotal"
      @open="page = 'cart'"
    />
  </v-app>
</template>

<style scoped>
  .sticky-nav {
    position: sticky;
    top: 0;
    z-index: 100;
    /* Soft shadow to separate from product cards */
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
  }

  /* Ensure the main area has enough space for the floating cart button */
  .pb-16 {
    padding-bottom: 120px !important;
  }
</style>
