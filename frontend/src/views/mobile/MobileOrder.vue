<script setup>
import { ref, computed, onMounted } from 'vue'

// --- REAL WORLD LOGIC: Capture Table from URL ---
const tableNumber = ref('05') // Default
onMounted(() => {
  const params = new URLSearchParams(window.location.search)
  if (params.get('table')) tableNumber.value = params.get('table')
})

const categories = ['All', 'Coffee', 'Tea', 'Pastries', 'Food']
const selectedCategory = ref('All')
const page = ref('home') // home | cart | tracking
const cart = ref([])
const isOrdering = ref(false)

const products = ref([
  { id: 1, name: 'Caramel Macchiato', price: 4.5, image: 'https://images.unsplash.com/photo-1485808191679-5f86510681a2?q=80&w=400', category: 'Coffee', desc: 'Fresh espresso with creamy milk and caramel.' },
  { id: 2, name: 'Iced Matcha Latte', price: 5.25, image: 'https://images.unsplash.com/photo-1515823064-d6e0c04616a7?q=80&w=400', category: 'Tea', desc: 'Premium grade matcha whisked with oat milk.' },
  { id: 3, name: 'Butter Croissant', price: 3.0, image: 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?q=80&w=400', category: 'Pastries', desc: 'Flaky, buttery, and baked fresh daily.' },
  { id: 4, name: 'Classic Burger', price: 8.5, image: 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?q=80&w=400', category: 'Food', desc: 'Angus beef with cheddar and secret sauce.' }
])

const filteredProducts = computed(() => {
  if (selectedCategory.value === 'All') return products.value
  return products.value.filter(p => p.category === selectedCategory.value)
})

const cartTotal = computed(() => cart.value.reduce((s, p) => s + p.price * p.qty, 0))
const totalItems = computed(() => cart.value.reduce((s, p) => s + p.qty, 0))

function addToCart(product) {
  const existing = cart.value.find(p => p.id === product.id)
  if (existing) existing.qty++
  else cart.value.push({ ...product, qty: 1 })
}

function updateQty(id, delta) {
  const item = cart.value.find(p => p.id === id)
  if (item) {
    item.qty += delta
    if (item.qty <= 0) cart.value = cart.value.filter(p => p.id !== id)
  }
}

// SIMULATE SENDING TO POS
function placeOrder() {
  isOrdering.value = true
  setTimeout(() => {
    isOrdering.value = false
    page.value = 'tracking'
    // In real world: axios.post('/api/orders', { table: tableNumber.value, items: cart.value })
  }, 2000)
}
</script>

<template>
  <v-app class="bg-grey-lighten-5">
    
    <v-app-bar flat color="white" class="border-b px-2">
      <v-avatar size="36" color="primary-lighten-5" class="mr-3">
        <v-icon color="primary" size="small">mdi-qrcode-scan</v-icon>
      </v-avatar>
      <div class="d-flex flex-column">
        <span class="text-caption text-grey font-weight-bold" style="line-height: 1">ORDERING FROM</span>
        <span class="text-subtitle-1 font-weight-black">Table {{ tableNumber }}</span>
      </div>
      <v-spacer />
      <v-chip size="small" color="success" variant="tonal" class="font-weight-bold">Store Open</v-chip>
    </v-app-bar>

    <v-main>
      <template v-if="page === 'home'">
        <v-container class="pt-4">
          <v-card flat rounded="xl" color="grey-darken-4" class="pa-5 d-flex align-center">
            <div>
              <div class="text-h5 font-weight-black text-white">Welcome! 👋</div>
              <p class="text-caption text-grey-lighten-1">Order directly from your phone and we'll bring it to your table.</p>
            </div>
          </v-card>
        </v-container>

        <v-sheet class="bg-transparent mt-2 overflow-x-auto">
          <v-tabs
            v-model="selectedCategory"
            color="primary"
            hide-slider
            class="px-4"
          >
            <v-tab
              v-for="cat in categories"
              :key="cat"
              :value="cat"
              :class="selectedCategory === cat ? 'bg-primary text-white' : 'bg-white text-grey-darken-1'"
              class="text-none font-weight-bold mr-2 rounded-pill border transition-all"
              style="min-width: 80px"
            >
              {{ cat }}
            </v-tab>
          </v-tabs>
        </v-sheet>

        <v-container>
          <v-row>
            <v-col v-for="product in filteredProducts" :key="product.id" cols="12" sm="6">
              <v-card flat rounded="xl" class="border overflow-hidden product-card">
                <div class="d-flex pa-3">
                  <v-img :src="product.image" width="100" height="100" cover class="rounded-xl" />
                  <div class="ml-4 flex-grow-1">
                    <div class="text-subtitle-2 font-weight-black">{{ product.name }}</div>
                    <div class="text-caption text-grey mb-2 line-clamp-2">{{ product.desc }}</div>
                    <div class="d-flex justify-space-between align-center">
                      <span class="text-subtitle-1 font-weight-black text-primary">${{ product.price }}</span>
                      <v-btn color="primary" icon="mdi-plus" size="x-small" flat class="rounded-lg" @click="addToCart(product)" />
                    </div>
                  </div>
                </div>
              </v-card>
            </v-col>
          </v-row>
        </v-container>
      </template>

      <template v-if="page === 'cart'">
        <v-container>
          <v-btn variant="text" icon="mdi-arrow-left" @click="page='home'" class="mb-2" />
          <h2 class="text-h5 font-weight-black px-2 mb-4">My Basket</h2>
          
          <v-card v-for="item in cart" :key="item.id" flat class="border rounded-xl pa-3 mb-3">
            <div class="d-flex align-center">
              <v-avatar size="60" rounded="lg"><v-img :src="item.image" cover /></v-avatar>
              <div class="ml-4 flex-grow-1">
                <div class="text-subtitle-2 font-weight-bold">{{ item.name }}</div>
                <div class="text-caption font-weight-bold text-primary">${{ item.price }}</div>
              </div>
              <div class="d-flex align-center bg-grey-lighten-4 rounded-pill border">
                <v-btn icon="mdi-minus" size="x-small" variant="text" @click="updateQty(item.id, -1)" />
                <span class="px-2 font-weight-bold">{{ item.qty }}</span>
                <v-btn icon="mdi-plus" size="x-small" variant="text" @click="updateQty(item.id, 1)" />
              </div>
            </div>
          </v-card>

          <v-card flat class="mt-6 pa-4 rounded-xl border-dashed bg-primary-lighten-5">
            <div class="d-flex justify-space-between mb-4">
              <span class="text-h6 font-weight-black">Total Payable</span>
              <span class="text-h6 font-weight-black text-primary">${{ cartTotal.toFixed(2) }}</span>
            </div>
            <v-btn 
              block color="primary" size="large" rounded="xl" 
              class="font-weight-black" :loading="isOrdering"
              @click="placeOrder"
            >
              PLACE ORDER TO TABLE {{ tableNumber }}
            </v-btn>
          </v-card>
        </v-container>
      </template>

      <template v-if="page === 'tracking'">
        <v-container class="text-center pt-10">
          <v-icon size="80" color="success" class="mb-4">mdi-check-circle</v-icon>
          <h2 class="text-h4 font-weight-black mb-2">Order Received!</h2>
          <p class="text-grey mb-8">Relax, we are preparing your order for <b>Table {{ tableNumber }}</b></p>
          
          <v-card flat class="border rounded-xl pa-6 bg-white mb-6">
            <div class="d-flex justify-space-between align-center mb-6">
              <div class="text-left">
                <div class="text-caption text-grey">ORDER STATUS</div>
                <div class="text-subtitle-1 font-weight-bold text-primary">Preparing your coffee...</div>
              </div>
              <v-progress-circular indeterminate color="primary" />
            </div>
            <v-divider />
            <div class="mt-4 text-left">
              <div v-for="item in cart" :key="item.id" class="text-subtitle-2 mb-1">
                {{ item.qty }}x {{ item.name }}
              </div>
            </div>
          </v-card>

          <v-btn variant="text" color="primary" class="font-weight-bold" @click="page='home'; cart=[]">
            Order More
          </v-btn>
        </v-container>
      </template>
    </v-main>

    <div v-if="cart.length > 0 && page === 'home'" class="fixed-bottom px-6">
      <v-btn block color="primary" height="60" rounded="xl" class="elevation-10" @click="page='cart'">
        <v-badge color="white" :content="totalItems" inline class="mr-2" />
        <span class="font-weight-black">Review Basket</span>
        <v-spacer />
        <span class="font-weight-black">${{ cartTotal.toFixed(2) }}</span>
      </v-btn>
    </div>

    <v-bottom-navigation grow v-if="page !== 'tracking'">
      <v-btn @click="page='home'"><v-icon>mdi-home</v-icon>Menu</v-btn>
      <v-btn @click="page='tracking'"><v-icon>mdi-clock-outline</v-icon>My Order</v-btn>
    </v-bottom-navigation>

  </v-app>
</template>

<style scoped>
.product-card {
  transition: transform 0.2s;
  background: white;
}
.product-card:active {
  transform: scale(0.98);
}
.fixed-bottom {
  position: fixed;
  bottom: 80px;
  width: 100%;
  z-index: 100;
}
.border-dashed {
  border: 2px dashed #1976D2 !important;
}
/* Fix for Tab Text visibility */
.v-tab--selected {
  opacity: 1 !important;
}
</style>