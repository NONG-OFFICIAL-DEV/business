<script setup>
  import { ref, computed, onMounted, watch } from 'vue' // Added watch
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
  import { useLoadingStore } from '@/stores/loading'
  import { useCategoryMenuStore } from '@/stores/categoryMenu'

  const orderStore = useOrderStore()
  const menuStore = useMenuStore()
  const diningTableStore = useDiningTableStore()
  const route = useRoute()
  const token = route.params.token
  const tableNumber = ref()
  const tableId = ref()
  const loadingStore = useLoadingStore()
  const menuCategoryStore = useCategoryMenuStore()
  // --- PERSISTENCE LOGIC START ---
  // Load the page from localStorage immediately on script load
  const page = ref(localStorage.getItem('active_page') || 'home')

  // Save the page to localStorage whenever it changes
  watch(page, newPage => {
    localStorage.setItem('active_page', newPage)
  })
  // --- PERSISTENCE LOGIC END ---

  onMounted(async () => {
    const params = new URLSearchParams(window.location.search)
    if (params.get('table')) tableNumber.value = params.get('table')

    await menuStore.fetchMenus()
    const res = await diningTableStore.getTableNumberByToken(token)
    tableNumber.value = res.table.table_number
    tableId.value = res.table.id

    // UX Protection: If page is 'cart' but cart is empty after refresh, go home
    if (page.value === 'cart' && cart.value.length === 0) {
      page.value = 'home'
    }
    await menuCategoryStore.fetchAllMenuCategory({loading:'skeleton'})
  })

  const selectedCategory = ref('All')
  const isOrdering = ref(false)
  const { cart, totalItems, cartTotal, addToCart, updateQty, clearCart } =
    useCart()
  const viewProcess = ref(false)

  async function placeOrder() {
    isOrdering.value = true
    try {
      const orderData = {
        table_id: tableId.value,
        items: cart.value.map(i => ({
          // menu_id: i.id,
          menu_id: i.menu_id,
          quantity: i.qty,
          price: i.price,
          note: i.note || null,
          customizations: i.customizations || null
        }))
      }

      await orderStore.createOrder(orderData, 'noLoading')
      await menuStore.fetchMenus()

      // Navigate to tracking
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
  function goToCart() {
    page.value = 'cart'
  }

  function handleReset() {
    clearCart()
    // Clear page persistence on manual reset
    localStorage.removeItem('active_page')
    page.value = 'home'
  }

  const search = ref('')

  const filteredProducts = computed(() => {
    let list = menuStore.menus.data || []
    if (selectedCategory.value !== 'All') {
      list = list.filter(p => p.menu_category_id === selectedCategory.value)
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
      @view-cart="goToCart"
      :tableNumber="tableNumber"
      :cartCount="totalItems"
    />

    <v-main>
      <transition name="fade-slide" mode="out-in">
        <div :key="page">
          <div v-if="page === 'home'">
            <div class="sticky-nav bg-white shadow-sm">
              <CategoryTabs
                :categories="menuCategoryStore.items"
                v-model="selectedCategory"
                v-model:search="search"
              />
            </div>

            <v-container class="pb-16">
              <v-row>
                <template
                  v-if="
                    loadingStore.isLoading && loadingStore.mode === 'skeleton'
                  "
                >
                  <v-col v-for="n in 6" :key="n" cols="6" class="pa-2">
                    <v-card flat rounded="xl" class="pa-3 bg-white">
                      <v-skeleton-loader
                        type="avatar"
                        height="100"
                        class="mx-auto mb-2"
                      ></v-skeleton-loader>
                      <v-skeleton-loader
                        type="text"
                        width="80%"
                        class="mx-auto mb-4"
                      ></v-skeleton-loader>
                      <div class="d-flex justify-space-between align-center">
                        <v-skeleton-loader
                          type="text"
                          width="40%"
                        ></v-skeleton-loader>
                        <v-skeleton-loader
                          type="avatar"
                          size="32"
                        ></v-skeleton-loader>
                      </div>
                    </v-card>
                  </v-col>
                </template>

                <template v-else-if="filteredProducts.length === 0">
                  <v-col
                    cols="12"
                    class="d-flex flex-column align-center justify-center py-12"
                  >
                    <v-avatar color="#3b828e10" size="100" class="mb-6">
                      <v-icon size="48" color="#3b828e">
                        mdi-book-search-outline
                      </v-icon>
                    </v-avatar>
                    <h3 class="text-h6 font-weight-black mb-1">
                      No dishes found
                    </h3>
                    <v-btn
                      variant="tonal"
                      color="#3b828e"
                      rounded="pill"
                      @click="search = ''"
                    >
                      Clear search
                    </v-btn>
                  </v-col>
                </template>

                <template v-else>
                  <ProductCard
                    :items="filteredProducts"
                    :cart="cart"
                    @add="addToCart"
                    @update="updateQty"
                  />
                </template>
              </v-row>
            </v-container>
          </div>

          <CartView
            v-else-if="page === 'cart'"
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
            v-else-if="page === 'tracking'"
            :cart="cart"
            @back="page = 'home'"
            :tableNumber="tableNumber"
            :tableId="tableId"
            v-model="viewProcess"
            @reset="handleReset"
          />
        </div>
      </transition>
    </v-main>

    <transition name="pop">
      <CartButton
        v-if="cart.length && page === 'home'"
        :totalItems="totalItems"
        :totalPrice="cartTotal"
        @open="page = 'cart'"
      />
    </transition>
  </v-app>
</template>
<style scoped>
  /* Update your style section */
  .sticky-nav {
    position: -webkit-sticky; /* Support for Safari */
    position: sticky;
    top: 0;
    z-index: 10;
    background: white;
    width: 100%;
  }

  /* Ensure the main area has enough space for the floating cart button */
  .pb-16 {
    padding-bottom: 120px !important;
  }
  /* 1. Page Switch: Fade and Slight Slide */
  .fade-slide-enter-active,
  .fade-slide-leave-active {
    transition: all 0.3s ease;
  }

  .fade-slide-enter-from {
    opacity: 0;
    transform: translateX(20px); /* Slides in from right */
  }

  .fade-slide-leave-to {
    opacity: 0;
    transform: translateX(-20px); /* Slides out to left */
  }

  /* 2. List Filtering: Smooth items sliding */
  .list-stagger-enter-active,
  .list-stagger-leave-active {
    transition: all 0.4s ease;
  }

  .list-stagger-enter-from {
    opacity: 0;
    transform: translateY(15px);
  }

  .list-stagger-leave-to {
    opacity: 0;
    transform: scale(0.9);
  }

  /* 3. Floating Cart Button: Pop up effect */
  .pop-enter-active {
    animation: pop-in 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  }
  .pop-leave-active {
    animation: pop-in 0.2s reverse;
  }

  @keyframes pop-in {
    0% {
      transform: scale(0.5) translateY(100px);
      opacity: 0;
    }
    100% {
      transform: scale(1) translateY(0);
      opacity: 1;
    }
  }

  /* Ensures list items move smoothly when one is removed */
  .list-stagger-move {
    transition: transform 0.4s ease;
  }
</style>
