<script setup>
  import { ref, computed, onMounted } from 'vue'
  import { useCart } from '@/composables/useCart'

  import AppHeader from '@/components/mobile/AppHeader.vue'
  import CategoryTabs from '@/components/mobile/CategoryTabs.vue'
  import ProductCard from '@/components/mobile/ProductCard.vue'
  import CartButton from '@/components/mobile/CartButton.vue'
  import CartView from '@/components/mobile/CartView.vue'
  import TrackingView from '@/components/mobile/TrackingView.vue'

  const tableNumber = ref('05')
  onMounted(() => {
    const params = new URLSearchParams(window.location.search)
    if (params.get('table')) tableNumber.value = params.get('table')
  })

  const categories = ['All', 'Coffee', 'Tea', 'Pastries', 'Food']
  const selectedCategory = ref('All')
  const page = ref('home')
  const isOrdering = ref(false)
  const { cart, totalItems, cartTotal, addToCart, updateQty, clearCart } =
    useCart()

  const products = ref([
    {
      id: 1,
      name: 'Caramel Macchiato',
      price: 4.5,
      image: 'https://images.unsplash.com/photo-1485808191679-5f86510681a2',
      category: 'Coffee',
      desc: 'Fresh espresso with caramel.'
    },
    {
      id: 2,
      name: 'Iced Matcha Latte',
      price: 5.25,
      image: 'https://images.unsplash.com/photo-1515823064-d6e0c04616a7',
      category: 'Tea',
      desc: 'Premium matcha with oat milk.'
    },
    {
      id: 2,
      name: 'Iced Matcha Latte',
      price: 5.25,
      image:
        'https://i.pinimg.com/1200x/b8/9e/e1/b89ee14374a9fe089e456059f1c57961.jpg',
      category: 'Food',
      desc: 'Premium matcha with oat milk.'
    },
    {
      id: 2,
      name: 'Iced Matcha Latte',
      price: 5.25,
      image:
        'https://i.pinimg.com/1200x/e7/df/6d/e7df6d197bb73f8177549c69d2229b00.jpg',
      category: 'Food',
      desc: 'Premium matcha with oat milk.'
    },
    {
      id: 2,
      name: 'Iced Matcha Latte',
      price: 5.25,
      image:
        'https://i.pinimg.com/1200x/54/66/30/5466301c06c0f6ac47593cb1b81f0b23.jpg',
      category: 'Food',
      desc: 'Premium matcha with oat milk.'
    },
    {
      id: 2,
      name: 'Iced Matcha Latte',
      price: 5.25,
      image:
        'https://i.pinimg.com/736x/06/41/47/0641478bdba67c0112bd08f3e689a755.jpg',
      category: 'Food',
      desc: 'Premium matcha with oat milk.'
    },
    {
      id: 2,
      name: 'Iced Matcha Latte',
      price: 5.25,
      image:
        'https://i.pinimg.com/736x/e5/d6/67/e5d66743b20bf1330c4730df1cc8e50e.jpg',
      category: 'Food',
      desc: 'Premium matcha with oat milk.'
    }
  ])

  function placeOrder() {
    isOrdering.value = true
    setTimeout(() => {
      isOrdering.value = false
      page.value = 'tracking'
    }, 2000)
  }
  function handleReset() {
    clearCart()
    page.value = 'home'
  }
  const search = ref('')

  const filteredProducts = computed(() => {
    let list = products.value

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
