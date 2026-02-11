<script setup>
  import { ref, computed } from 'vue'
  import ProductDialog from './ProductDialog.vue'

  import { useCartStore } from '@/stores/cartStore'

  const langSheet = ref(false)
  const activeLang = ref('KH') // Default language

  const cartStore = useCartStore()
  /** PRODUCTS */
  const products = ref([
    {
      id: 1,
      name: 'Ultra Headphones',
      price: 299,
      category: 'Style',
      image:
        'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500',
      description: 'Premium sound with noise cancellation and soft ear pads.'
    },
    {
      id: 2,
      name: 'Smart Watch S10',
      price: 450,
      category: 'Tech',
      image:
        'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500',
      description: 'Track fitness, notifications, heart rate and sleep quality.'
    },
    {
      id: 3,
      name: 'Minimal Camera',
      price: 890,
      category: 'Tech',
      image:
        'https://images.unsplash.com/photo-1495707902641-75cac588d2e9?w=500',
      description: 'Clean design camera with ultra sharp lens and 4K video.'
    },
    {
      id: 4,
      name: 'Studio Mic',
      price: 199,
      category: 'Tech',
      image:
        'https://images.unsplash.com/photo-1590602847861-f357a9332bbc?w=500',
      description: 'Perfect microphone for podcast, studio and voice recording.'
    },
    {
      id: 5,
      name: 'Wireless Mouse',
      price: 75,
      category: 'Tech',
      image:
        'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=500',
      description: 'Fast, smooth and ergonomic mouse for daily work.'
    },
    {
      id: 6,
      name: 'Mechanical Keyboard',
      price: 160,
      category: 'Tech',
      image:
        'https://images.unsplash.com/photo-1511467687858-23d96c32e4ae?w=500',
      description: 'Mechanical keys, premium typing feel, modern minimal look.'
    },
    {
      id: 7,
      name: 'Leather Bag',
      price: 120,
      category: 'Style',
      image: 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=500',
      description: 'Elegant leather bag for office, travel, and everyday style.'
    },
    {
      id: 8,
      name: 'Classic Sunglasses',
      price: 85,
      category: 'Style',
      image:
        'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=500',
      description: 'Classic UV protection sunglasses with premium frame.'
    },
    {
      id: 9,
      name: 'Wool Beanie',
      price: 35,
      category: 'Style',
      image:
        'https://images.unsplash.com/photo-1576871337622-98d48d385e2c?w=500',
      description: 'Warm wool beanie, perfect for cold season and fashion.'
    },
    {
      id: 10,
      name: 'Minimalist Wallet',
      price: 55,
      category: 'Style',
      image:
        'https://images.unsplash.com/photo-1627123430985-71d464a0b89a?w=500',
      description: 'Slim wallet with modern design and strong material.'
    },
    {
      id: 11,
      name: 'Denim Jacket',
      price: 110,
      category: 'Style',
      image:
        'https://images.unsplash.com/photo-1523205771623-e0faa4d2813d?w=500',
      description: 'Classic denim jacket. Stylish, durable and comfortable.'
    },
    {
      id: 12,
      name: 'Canvas Sneakers',
      price: 95,
      category: 'Style',
      image:
        'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?w=500',
      description: 'Lightweight sneakers for daily walking and casual outfits.'
    },
    {
      id: 13,
      name: 'Ceramic Vase',
      price: 45,
      category: 'Home',
      image:
        'https://images.unsplash.com/photo-1581781870027-04212e231e96?w=500',
      description: 'Minimal ceramic vase to decorate your home interior.'
    },
    {
      id: 14,
      name: 'Soy Wax Candle',
      price: 28,
      category: 'Home',
      image:
        'https://images.unsplash.com/photo-1603007905991-65500b164b38?w=500',
      description: 'Natural soy wax candle with relaxing fragrance.'
    },
    {
      id: 15,
      name: 'Modern Desk Lamp',
      price: 79,
      category: 'Home',
      image:
        'https://images.unsplash.com/photo-1534073828943-f801091bb18c?w=500',
      description: 'Modern lamp for office desk and bedroom lighting.'
    },
    {
      id: 17,
      name: 'Glass Carafe',
      price: 40,
      category: 'Home',
      image:
        'https://images.unsplash.com/photo-1616645258469-ce5e762463b2?w=500',
      description: 'Beautiful glass carafe for water, juice or cold tea.'
    },
    {
      id: 18,
      name: 'Bamboo Storage Box',
      price: 32,
      category: 'Home',
      image:
        'https://images.unsplash.com/photo-1591123120675-6f7f1aae0e5b?w=500',
      description: 'Eco bamboo storage box. Clean look, strong and useful.'
    },
    {
      id: 19,
      name: 'Yoga Mat',
      price: 50,
      category: 'Wellness',
      image:
        'https://images.unsplash.com/photo-1592432678891-9c654914a5ad?w=500',
      description: 'Anti-slip yoga mat for home and gym training.'
    },
    {
      id: 20,
      name: 'Insulated Bottle',
      price: 38,
      category: 'Wellness',
      image:
        'https://images.unsplash.com/photo-1602143399827-bd959518b450?w=500',
      description: 'Keeps your drink cold/hot for long time.'
    },
    {
      id: 21,
      name: 'Essential Oil Diffuser',
      price: 60,
      category: 'Wellness',
      image:
        'https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?w=500',
      description: 'Relaxing diffuser for aromatherapy and fresh air.'
    },
    {
      id: 22,
      name: 'Weighted Eye Mask',
      price: 25,
      category: 'Wellness',
      image:
        'https://images.unsplash.com/photo-1585128719715-46776b56a0d1?w=500',
      description: 'Soft weighted eye mask for sleep and stress relief.'
    },
    {
      id: 23,
      name: 'Foam Roller',
      price: 42,
      category: 'Wellness',
      image:
        'https://images.unsplash.com/photo-1593110014310-918903259960?w=500',
      description: 'Perfect for muscle recovery and workout stretching.'
    },
    {
      id: 24,
      name: 'Adjustable Dumbbells',
      price: 180,
      category: 'Wellness',
      image:
        'https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?w=500',
      description: 'Adjustable weights for full body home training.'
    }
  ])

  /** UI FILTERS */
  const categories = ['All', 'Tech', 'Style', 'Home', 'Wellness']
  const searchQuery = ref('')
  const activeCategory = ref('All')

  /** DIALOG */
  const dialog = ref(false)
  const selectedProduct = ref(null)

  const filteredProducts = computed(() => {
    return products.value.filter(p => {
      const matchCat =
        activeCategory.value === 'All' || p.category === activeCategory.value

      const matchSearch =
        !searchQuery.value ||
        p.name.toLowerCase().includes(searchQuery.value.toLowerCase())

      return matchCat && matchSearch
    })
  })

  /** FUNCTIONS */
  const openProduct = product => {
    selectedProduct.value = product
    dialog.value = true
  }

  const addToCart = product => {
    cartStore.addToCart(product)
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
          <v-slide-group-item
            v-for="cat in categories"
            :key="cat"
            :value="cat"
            v-slot="{ isSelected, toggle }"
          >
            <v-btn
              :variant="isSelected ? 'flat' : 'text'"
              :color="isSelected ? 'black' : 'grey-darken-1'"
              rounded="pill"
              class="mx-1 text-none"
              @click="toggle"
            >
              {{ cat }}
            </v-btn>
          </v-slide-group-item>
        </v-slide-group>
      </div>

      <!-- PRODUCTS -->
      <div class="px-2">
        <v-row no-gutters>
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
                  :src="product.image"
                  aspect-ratio="1"
                  cover
                  class="rounded-xl bg-grey-lighten-4"
                />

                <div class="pa-2">
                  <div class="text-body-2 font-weight-bold text-truncate">
                    {{ product.name }}
                  </div>

                  <div class="d-flex align-center justify-space-between mt-1">
                    <span class="font-weight-black">${{ product.price }}</span>

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
        </v-row>
      </div>
    </v-container>
  </v-main>

  <ProductDialog v-model="dialog" :product="selectedProduct" />
</template>

<style scoped>
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
    background: transparent !important;
    cursor: pointer;
    transition: all 0.2s ease;
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
