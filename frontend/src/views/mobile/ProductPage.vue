<script setup>
  import { ref, computed } from 'vue'

  const products = ref([
    // TECH CATEGORY
    {
      id: 1,
      name: 'Ultra Headphones',
      price: 299,
      category: 'Tech',
      image:
        'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500'
    },
    {
      id: 2,
      name: 'Smart Watch S10',
      price: 450,
      category: 'Tech',
      image:
        'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500'
    },
    {
      id: 3,
      name: 'Minimal Camera',
      price: 890,
      category: 'Tech',
      image:
        'https://images.unsplash.com/photo-1495707902641-75cac588d2e9?w=500'
    },
    {
      id: 4,
      name: 'Studio Mic',
      price: 199,
      category: 'Tech',
      image:
        'https://images.unsplash.com/photo-1590602847861-f357a9332bbc?w=500'
    },
    {
      id: 5,
      name: 'Wireless Mouse',
      price: 75,
      category: 'Tech',
      image:
        'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=500'
    },
    {
      id: 6,
      name: 'Mechanical Keyboard',
      price: 160,
      category: 'Tech',
      image:
        'https://images.unsplash.com/photo-1511467687858-23d96c32e4ae?w=500'
    },

    // STYLE CATEGORY
    {
      id: 7,
      name: 'Leather Bag',
      price: 120,
      category: 'Style',
      image: 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=500'
    },
    {
      id: 8,
      name: 'Classic Sunglasses',
      price: 85,
      category: 'Style',
      image:
        'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=500'
    },
    {
      id: 9,
      name: 'Wool Beanie',
      price: 35,
      category: 'Style',
      image:
        'https://images.unsplash.com/photo-1576871337622-98d48d385e2c?w=500'
    },
    {
      id: 10,
      name: 'Minimalist Wallet',
      price: 55,
      category: 'Style',
      image:
        'https://images.unsplash.com/photo-1627123430985-71d464a0b89a?w=500'
    },
    {
      id: 11,
      name: 'Denim Jacket',
      price: 110,
      category: 'Style',
      image:
        'https://images.unsplash.com/photo-1523205771623-e0faa4d2813d?w=500'
    },
    {
      id: 12,
      name: 'Canvas Sneakers',
      price: 95,
      category: 'Style',
      image:
        'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?w=500'
    },

    // HOME CATEGORY
    {
      id: 13,
      name: 'Ceramic Vase',
      price: 45,
      category: 'Home',
      image:
        'https://images.unsplash.com/photo-1581781870027-04212e231e96?w=500'
    },
    {
      id: 14,
      name: 'Soy Wax Candle',
      price: 28,
      category: 'Home',
      image:
        'https://images.unsplash.com/photo-1603007905991-65500b164b38?w=500'
    },
    {
      id: 15,
      name: 'Modern Desk Lamp',
      price: 79,
      category: 'Home',
      image:
        'https://images.unsplash.com/photo-1534073828943-f801091bb18c?w=500'
    },
    {
      id: 17,
      name: 'Glass Carafe',
      price: 40,
      category: 'Home',
      image:
        'https://images.unsplash.com/photo-1616645258469-ce5e762463b2?w=500'
    },
    {
      id: 18,
      name: 'Bamboo Storage Box',
      price: 32,
      category: 'Home',
      image:
        'https://images.unsplash.com/photo-1591123120675-6f7f1aae0e5b?w=500'
    },

    // WELLNESS / ACCESSORIES CATEGORY
    {
      id: 19,
      name: 'Yoga Mat',
      price: 50,
      category: 'Wellness',
      image:
        'https://images.unsplash.com/photo-1592432678891-9c654914a5ad?w=500'
    },
    {
      id: 20,
      name: 'Insulated Bottle',
      price: 38,
      category: 'Wellness',
      image:
        'https://images.unsplash.com/photo-1602143399827-bd959518b450?w=500'
    },
    {
      id: 21,
      name: 'Essential Oil Diffuser',
      price: 60,
      category: 'Wellness',
      image:
        'https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?w=500'
    },
    {
      id: 22,
      name: 'Weighted Eye Mask',
      price: 25,
      category: 'Wellness',
      image:
        'https://images.unsplash.com/photo-1585128719715-46776b56a0d1?w=500'
    },
    {
      id: 23,
      name: 'Foam Roller',
      price: 42,
      category: 'Wellness',
      image:
        'https://images.unsplash.com/photo-1593110014310-918903259960?w=500'
    },
    {
      id: 24,
      name: 'Adjustable Dumbbells',
      price: 180,
      category: 'Wellness',
      image:
        'https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?w=500'
    }
  ])

  const categories = ['All', 'Tech', 'Style', 'Home']
  const cartCount = ref(2) // Sample cart state
  const showSearch = ref(false)
  const searchQuery = ref('')
  const activeCategory = ref('All')

  const openProduct = p => console.log('Opening', p.name)

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
  const toggleSearch = () => {
    showSearch.value = !showSearch.value
    if (!showSearch.value) searchQuery.value = ''
  }
</script>

<template>
  <v-app-bar
    app
    flat
    :height="showSearch ? 140 : 70"
    color="white"
    class="premium-header px-2"
  >
    <div class="header-stack w-100">
      <div class="d-flex align-center justify-space-between w-100 px-3 pt-2">
        <div class="brand-section">
          <h1 class="text-h6 font-weight-black tracking-tighter">STORE.CA</h1>
        </div>

        <div class="actions-section d-flex align-center">
          <v-btn
            icon
            variant="tonal"
            size="small"
            class="mr-2"
            :color="showSearch ? 'black' : 'grey-darken-3'"
            @click="toggleSearch"
          >
            <v-icon>{{ showSearch ? 'mdi-close' : 'mdi-magnify' }}</v-icon>
          </v-btn>

          <v-btn icon variant="tonal" size="small" color="black">
            <v-badge :content="cartCount" color="error" size="x-small">
              <v-icon>mdi-shopping-outline</v-icon>
            </v-badge>
          </v-btn>
        </div>
      </div>

      <transition name="search-expand">
        <div v-if="showSearch" class="px-4 pb-4 mt-2">
          <v-text-field
            v-model="searchQuery"
            placeholder="Search our collection..."
            variant="solo-filled"
            flat
            hide-details
            rounded="lg"
            bg-color="grey-lighten-4"
            prepend-inner-icon="mdi-magnify"
            density="comfortable"
            autofocus
          ></v-text-field>
        </div>
      </transition>
    </div>
  </v-app-bar>

  <v-main class="bg-grey-lighten-5">
    <v-container class="pa-0 pb-16">
      <transition name="reveal-up" appear>
        <div class="px-5 pt-6 mb-4">
          <v-card
            flat
            class="banner-card rounded-xl overflow-hidden elevation-0"
          >
            <v-img
              src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=800"
              height="160"
              cover
            >
              <div class="banner-overlay pa-6">
                <span class="text-overline text-white font-weight-bold">
                  NEW ARRIVALS
                </span>
                <h2 class="text-h5 font-weight-black text-white">
                  Summer Gear 2026
                </h2>
                <v-btn
                  color="white"
                  variant="flat"
                  rounded="pill"
                  size="small"
                  class="mt-3 text-none px-6"
                >
                  Shop Now
                </v-btn>
              </div>
            </v-img>
          </v-card>
        </div>
      </transition>

      <div class="sticky-nav">
        <v-slide-group v-model="activeCategory" mandatory class="py-2">
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
              class="mx-1 text-none font-weight-bold"
              @click="toggle"
            >
              {{ cat }}
            </v-btn>
          </v-slide-group-item>
        </v-slide-group>
      </div>

      <v-container class="px-3">
        <v-row no-gutters>
          <v-col
            v-for="(product, i) in filteredProducts"
            :key="product.id"
            cols="6"
            class="pa-2"
          >
            <transition
              name="card-pop"
              appear
              :style="{ transitionDelay: i * 50 + 'ms' }"
            >
              <v-card flat class="product-card" @click="openProduct(product)">
                <v-img
                  :src="product.image"
                  height="160"
                  cover
                  class="rounded-xl bg-grey-lighten-4"
                ></v-img>
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
                      elevation="2"
                    ></v-btn>
                  </div>
                </div>
              </v-card>
            </transition>
          </v-col>
        </v-row>
      </v-container>
    </v-container>
  </v-main>

  <div class="bottom-nav">
    <v-sheet class="nav-pill d-flex justify-space-around align-center">
      <v-btn icon="mdi-home-variant" variant="text" color="black"></v-btn>
      <v-btn
        icon="mdi-magnify"
        variant="text"
        color="grey-lighten-1"
        @click="showSearch = true"
      ></v-btn>
      <v-btn
        icon="mdi-heart-outline"
        variant="text"
        color="grey-lighten-1"
      ></v-btn>
      <v-btn
        icon="mdi-account-outline"
        variant="text"
        color="grey-lighten-1"
      ></v-btn>
    </v-sheet>
  </div>
</template>

<style scoped>
  .tracking-tighter {
    letter-spacing: -1px;
  }

  .premium-header {
    border-bottom: 1px solid #eee !important;
    transition: height 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .search-expand-enter-active,
  .search-expand-leave-active {
    transition: all 0.3s ease;
  }
  .search-expand-enter-from,
  .search-expand-leave-to {
    opacity: 0;
    transform: translateY(-10px);
  }

  .sticky-nav {
    position: sticky;
    top: 70px;
    z-index: 10;
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
  }

  .product-card {
    background: transparent !important;
    transition: transform 0.2s ease;
  }
  .product-card:active {
    transform: scale(0.96);
  }

  .banner-overlay {
    background: linear-gradient(90deg, rgba(0, 0, 0, 0.7) 0%, transparent 100%);
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }

  .bottom-nav {
    position: fixed;
    bottom: 20px;
    width: 100%;
    display: flex;
    justify-content: center;
    z-index: 100;
  }
  .nav-pill {
    width: 90%;
    max-width: 400px;
    height: 64px;
    border-radius: 32px;
    background: rgba(255, 255, 255, 0.9) !important;
    backdrop-filter: blur(20px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
    border: 1px solid rgba(255, 255, 255, 0.3);
  }

  /* Animations */
  .card-pop-enter-active {
    transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  }
  .card-pop-enter-from {
    opacity: 0;
    transform: scale(0.9) translateY(20px);
  }

  .reveal-up-enter-active {
    transition: all 0.7s ease-out;
  }
  .reveal-up-enter-from {
    opacity: 0;
    transform: translateY(30px);
  }
</style>
