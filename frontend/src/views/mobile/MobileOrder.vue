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
  import { useLoadingStore } from '@/stores/loading'

  // const tableNumber = ref('05')
  const orderStore = useOrderStore()
  const menuStore = useMenuStore()
  const diningTableStore = useDiningTableStore()
  const route = useRoute()
  const token = route.params.token
  const tableNumber = ref()
  const tableId = ref()
  const loadingStore = useLoadingStore()
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
      <transition name="fade-slide" mode="out-in">
        <div>
          <div v-if="page === 'home'" :key="'home'">
            <div class="sticky-nav bg-white shadow-sm">
              <CategoryTabs
                :categories="categories"
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
                  <v-col
                    v-for="n in 6"
                    :key="`skeleton-${n}`"
                    cols="12"
                    class="py-1"
                  >
                    <v-skeleton-loader
                      type="list-item-avatar-two-line"
                      class="rounded-xl"
                      height="100"
                    ></v-skeleton-loader>
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
                    <p
                      class="text-body-2 text-medium-emphasis text-center px-10 mb-6"
                    >
                      We couldn't find any items matching
                      <br />
                      Try checking your spelling or search for something else!
                    </p>

                    <v-btn
                      variant="tonal"
                      color="#3b828e"
                      rounded="pill"
                      class="text-none font-weight-bold px-6"
                      prepend-icon="mdi-refresh"
                      @click="$emit('update:search', '')"
                    >
                      Clear search
                    </v-btn>
                  </v-col>
                </template>
                <template v-else>
                  <transition-group name="list-stagger">
                    <!-- <v-col
                      v-for="p in filteredProducts"
                      :key="p.id"
                      cols="12"
                      class="py-1"
                    > -->
                    <ProductCard
                      :items="filteredProducts"
                      :cart="cart"
                      @add="addToCart"
                      @update="updateQty"
                    />
                    <!-- :qty="cart.find(i => i.id === p.id)?.qty || 0" -->
                    <!-- </v-col> -->
                  </transition-group>
                </template>
              </v-row>
            </v-container>
          </div>

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
