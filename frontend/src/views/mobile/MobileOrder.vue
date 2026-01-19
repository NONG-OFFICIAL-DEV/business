<script setup>
  import { ref, computed, onMounted } from 'vue'
  import { useCart } from '@/composables/useCart'

  import AppHeader from '@/components/mobile/AppHeader.vue'
  import CategoryTabs from '@/components/mobile/CategoryTabs.vue'
  import ProductCard from '@/components/mobile/ProductCard.vue'
  import CartButton from '@/components/mobile/CartButton.vue'
  import CartView from '@/components/mobile/CartView.vue'
  import TrackingView from '@/components/mobile/TrackingView.vue'
  import { useSaleStore } from '@/stores/salePOSStore'
  import { useProductStore } from '@/stores/productStore'

  const tableNumber = ref('05')
  const saleStore = useSaleStore()
  const productStore = useProductStore()

  onMounted(() => {
    const params = new URLSearchParams(window.location.search)
    if (params.get('table')) tableNumber.value = params.get('table')
    productStore.fetchProducts()
  })

  const categories = ['All', 'Coffee', 'Tea', 'Pastries', 'Food']
  const selectedCategory = ref('All')
  const page = ref('home')
  const isOrdering = ref(false)
  const { cart, totalItems, cartTotal, addToCart, updateQty, clearCart } =
    useCart()


  async function placeOrder() {
    isOrdering.value = true
    setTimeout(() => {
      isOrdering.value = false
      page.value = 'tracking'
    }, 2000)
    try {
      const saleData = {
        items: cart.value.map(i => ({
          product_id: i.id,
          qty: i.qty,
          price: i.price,
          customizations: i.customizations || null
        })),
        total_amount: cartTotal.value,
        payment_method: 'cash'
      }

      await saleStore.checkout(saleData)
      await productStore.fetchProducts()
    } catch {
      alert('Checkout failed')
    }
  }
  function handleReset() {
    clearCart()
    page.value = 'home'
  }
  const search = ref('')

  const filteredProducts = computed(() => {
    let list = productStore.products.data || []
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
    <AppHeader v-if="page === 'home'" :tableNumber="tableNumber" />

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
