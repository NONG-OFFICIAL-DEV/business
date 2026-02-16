<script setup>
  import { ref, computed, onMounted } from 'vue'
  import ProductDialog from './ProductDialog.vue'

  import { useProductStore } from '@/stores/productStore'
  import { useCategoryStore } from '@/stores/categoryStore'
  import { useCartStore } from '@/stores/cartStore'
  import { useLoadingStore } from '@/stores/loading'

  const productStore = useProductStore()
  const categoryStore = useCategoryStore()
  const cartStore = useCartStore()
  const loadingStore = useLoadingStore()

  const langSheet = ref(false)
  const activeLang = ref('KH')

  const searchQuery = ref('')
  const activeCategory = ref('All')

  const dialog = ref(false)
  const selectedProduct = ref(null)

  const filteredProducts = computed(() => {
    return productStore.products.data?.filter(p => {
      const matchCat =
        activeCategory.value === 'All' || p.category_id === activeCategory.value

      const matchSearch =
        !searchQuery.value ||
        p.name.toLowerCase().includes(searchQuery.value.toLowerCase())

      return matchCat && matchSearch
    })
  })

  const openProduct = product => {
    selectedProduct.value = product
    dialog.value = true
  }

  const addToCart = product => {
    cartStore.addToCart(product)
  }

  const fetchData = async () => {
    loadingStore.start('skeleton')

    try {
      await Promise.all([
        productStore.fetchProducts({}, { loading: 'skeleton' }),
        categoryStore.fetchCategories({ per_page: -1 }, { loading: 'skeleton' })
      ])
    } finally {
      loadingStore.stop()
    }
  }

  const languages = [
    {
      name: 'English',
      code: 'EN',
      flag: 'https://flagcdn.com/w80/gb.png'
    },
    {
      name: 'ភាសាខ្មែរ',
      code: 'KH',
      flag: 'https://flagcdn.com/w80/kh.png'
    }
  ]

  const currentLang = computed(() => {
    return languages.find(l => l.code === activeLang.value)
  })

  const changeLanguage = lang => {
    activeLang.value = lang.code
    langSheet.value = false

    // If using vue-i18n:
    // locale.value = lang.code.toLowerCase()

    console.log('Language changed to:', lang.name)
  }

  onMounted(fetchData)
</script>

<template>
  <!-- HEADER -->
  <v-app-bar app flat color="white" class="premium-header border-b">
    <v-container class="d-flex align-center justify-space-between py-0 px-4">
      <h1
        class="text-h6 font-weight-black tracking-tighter"
        @click="$router.push('/')"
      >
        STORE.CA
      </h1>

      <v-spacer></v-spacer>

      <v-btn
        variant="text"
        @click="langSheet = true"
        class="px-2 mr-1 rounded-pill"
      >
        <v-avatar size="20" class="mr-2">
          <v-img :src="currentLang.flag"></v-img>
        </v-avatar>
        <span class="text-caption font-weight-bold">
          {{ currentLang.code }}
        </span>
      </v-btn>

      <v-btn icon @click="$router.push('/mobile-cart')">
        <v-badge
          :content="cartStore.cartCount"
          color="error"
          offset-x="3"
          offset-y="3"
          v-if="cartStore.cartCount > 0"
        >
          <v-icon size="24">mdi-shopping-outline</v-icon>
        </v-badge>
        <v-icon v-else size="24">mdi-shopping-outline</v-icon>
      </v-btn>
    </v-container>
  </v-app-bar>

  <v-bottom-sheet v-model="langSheet">
    <v-card class="rounded-t-xl pa-4">
      <div class="d-flex justify-center mb-4">
        <div class="close-tab"></div>
      </div>
      <div class="text-subtitle-1 font-weight-black mb-4 px-2">
        Select Language
      </div>

      <v-list class="bg-transparent">
        <v-list-item
          v-for="lang in languages"
          :key="lang.code"
          @click="changeLanguage(lang)"
          :active="activeLang === lang.code"
          class="rounded-pill mb-2 border"
        >
          <template v-slot:prepend>
            <v-avatar size="28">
              <v-img :src="lang.flag"></v-img>
            </v-avatar>
          </template>
          <v-list-item-title class="ml-2">
            {{ lang.name }}
          </v-list-item-title>
          <template v-slot:append v-if="activeLang === lang.code">
            <v-icon color="primary">mdi-check-circle</v-icon>
          </template>
        </v-list-item>
      </v-list>
    </v-card>
  </v-bottom-sheet>

  <v-main>
    <v-container class="pa-0 pb-16 pb-md-4">
      <!-- SEARCH -->
      <div class="px-4 py-4">
        <v-text-field
          v-model="searchQuery"
          placeholder="Search our collection..."
          variant="solo-filled"
          flat
          hide-details
          rounded="pill"
          bg-color="grey-lighten-4"
          prepend-inner-icon="mdi-magnify"
          density="comfortable"
        />
      </div>

      <!-- BANNER -->
      <transition name="reveal-up" appear>
        <div class="px-4 mb-6">
          <v-card
            flat
            class="banner-card rounded-xl overflow-hidden elevation-0"
          >
            <v-img
              src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1200"
              class="banner-image"
              cover
            >
              <div class="banner-overlay pa-6 pa-md-12">
                <span class="text-overline text-white font-weight-bold">
                  NEW ARRIVALS
                </span>

                <h2 class="text-h4 text-md-h2 font-weight-black text-white">
                  Summer Gear 2026
                </h2>

                <v-btn
                  color="white"
                  variant="flat"
                  rounded="pill"
                  size="large"
                  class="mt-4 text-none px-8"
                >
                  Shop Now
                </v-btn>
              </div>
            </v-img>
          </v-card>
        </div>
      </transition>

      <!-- CATEGORIES -->
      <div class="px-3 mb-4">
        <v-slide-group v-model="activeCategory" mandatory show-arrows>
          <v-slide-group-item v-slot="{ isSelected, toggle }" value="All">
            <v-btn
              :color="isSelected ? 'black' : 'grey-darken-1'"
              class="mx-1 text-none"
              rounded
              prepend-icon="mdi-sort"
              :variant="isSelected ? 'flat' : 'text'"
              @click="toggle"
            >
              All
            </v-btn>
          </v-slide-group-item>
          <v-slide-group-item
            v-for="cat in categoryStore.categories?.data"
            :key="cat.id"
            :value="cat.id"
            v-slot="{ isSelected, toggle }"
          >
            <v-btn
              :variant="isSelected ? 'flat' : 'text'"
              :color="isSelected ? 'black' : 'grey-darken-1'"
              rounded="pill"
              class="mx-1 text-none"
              @click="toggle"
            >
              {{ cat.name }}
            </v-btn>
          </v-slide-group-item>
          <v-slide-group-item v-for="(item, n) in 12" :key="n" :value="n">
            <v-skeleton-loader
              v-if="loadingStore.isLoading"
              width="200"
              class="px-0 ma-1 rounded-lg"
            ></v-skeleton-loader>
          </v-slide-group-item>
        </v-slide-group>
      </div>

      <!-- PRODUCTS -->
      <div class="px-2">
        <v-row no-gutters>
          <!-- SKELETON LOADING -->
          <template v-if="loadingStore.isLoading">
            <v-col v-for="i in 8" :key="i" cols="6" sm="4" md="3" class="pa-2">
              <v-card flat rounded="xl" class="pa-0 bg-white">
                <v-skeleton-loader
                  type="image"
                  class="mx-auto mb-2"
                ></v-skeleton-loader>
                <div class="d-flex justify-space-between align-center">
                  <v-skeleton-loader
                    type="list-item-two-line"
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

          <!-- REAL PRODUCTS -->
          <template v-else>
            <v-col
              v-for="(product, i) in filteredProducts"
              :key="product.id"
              cols="6"
              sm="4"
              md="3"
              class="pa-2"
            >
              <transition
                name="card-pop"
                appear
                :style="{ transitionDelay: i * 30 + 'ms' }"
              >
                <v-card flat class="product-card" @click="openProduct(product)">
                  <v-img
                    :src="product.image_url"
                    aspect-ratio="1"
                    cover
                    class="rounded-xl bg-grey-lighten-4"
                  />

                  <div class="pa-2">
                    <div class="text-body-2 font-weight-bold text-truncate">
                      {{ product.name }}
                    </div>

                    <div class="d-flex align-center justify-space-between mt-1">
                      <span class="font-weight-black">
                        ${{ product.price }}
                      </span>

                      <v-btn
                        icon="mdi-plus"
                        size="x-small"
                        color="black"
                        class="elevation-1"
                        @click.stop="addToCart(product)"
                      />
                    </div>
                  </div>
                </v-card>
              </transition>
            </v-col>
          </template>
        </v-row>
      </div>
    </v-container>
  </v-main>

  <ProductDialog v-model="dialog" :product="selectedProduct" />
</template>

<style scoped>
  :deep(.v-skeleton-loader__image) {
    height: 150px;
    border-radius: 25px;
  }
  .close-tab {
    width: 40px;
    height: 4px;
    background: #e0e0e0;
    border-radius: 10px;
  }
  .tracking-tighter {
    letter-spacing: -1.5px;
  }

  /* Responsive Banner Height */
  .banner-image {
    height: 200px;
  }
  @media (min-width: 960px) {
    .banner-image {
      height: 400px;
    }
  }

  .banner-overlay {
    background: linear-gradient(90deg, rgba(0, 0, 0, 0.8) 0%, transparent 80%);
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }

  .product-card {
    cursor: pointer;
    transition: all 0.2s ease;
    border-radius: 25px;
  }

  .product-card:hover {
    transform: translateY(-4px);
  }
  .product-card:active {
    transform: scale(0.96);
  }

  /* Standard Transitions */
  .card-pop-enter-active {
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.1);
  }
  .card-pop-enter-from {
    opacity: 0;
    transform: scale(0.94) translateY(10px);
  }
  .reveal-up-enter-active {
    transition: all 0.6s ease-out;
  }
  .reveal-up-enter-from {
    opacity: 0;
    transform: translateY(20px);
  }
</style>
