<script setup>
  import { ref, computed, onMounted } from 'vue'
  import { useProductStore } from '@/stores/productStore'
  import { useCategoryStore } from '@/stores/categoryStore'
  import { usePosStore } from '@/stores/posStore' // to get selectedStore
  import { useMenuStore } from '@/stores/menuStore'
  import { useCategoryMenuStore } from '@/stores/categoryMenu'

  const productStore = useProductStore()
  const categoryStore = useCategoryStore()
  const menuStore = useMenuStore()
  const menuCategoryStore = useCategoryMenuStore()

  const posStore = usePosStore()

  const search = ref('')

  const selectedCategory = ref('All')

  const popularDishes = computed(() => {
    return filteredProducts.value.filter(p => p.is_popular)
  })

  const regularDishes = computed(() => {
    return filteredProducts.value.filter(p => !p.is_popular)
  })

  const filteredProducts = computed(() => {
    // 1. Determine which data source to use
    const isHospitality = posStore.selectedStore.type === 'hospitality'
    const rawData = isHospitality
      ? menuStore.menus?.data || []
      : productStore.products?.data || []

    // 2. Filter by Category first (Efficient)
    let filtered = rawData
    if (selectedCategory.value !== 'All') {
      // Note: use p.menu_category_id or p.category_id based on your API structure
      filtered = filtered.filter(
        p =>
          p.menu_category_id === selectedCategory.value ||
          p.category_id === selectedCategory.value
      )
    }

    // 3. Filter by Search string
    if (search.value) {
      const query = search.value.toLowerCase()
      filtered = filtered.filter(p => p.name.toLowerCase().includes(query))
    }

    return filtered
  })

  const categoriesList = computed(() => {
    if (posStore.selectedStore.type === 'hospitality') {
      const data = menuCategoryStore.items || []
      return data
    } else {
      const data = menuStore.menus?.data || []
      return data
    }
  })
  // Emits
  const emit = defineEmits(['select', 'quick-add'])

  function handleProductClick(product) {
    // Retail / Mart: quick-add
    if (!product.has_variants) {
      // storeStore.selectedStore.type === 'retail'
      emit('quick-add', product)
    } else {
      // Coffee/Restaurant: open customizer
      emit('select', product)
    }
  }

  // Fetch products and categories
  onMounted(async () => {
    await productStore.fetchProducts()
    await categoryStore.fetchCategories()
  })
</script>
<template>
  <v-container fluid class="pa-0">
    <!-- Category Filter -->
    <div class="mb-6">
      <div class="d-flex justify-space-between align-center mb-4 px-1">
        <span class="text-subtitle-2 text-grey-darken-4">Categories</span>
        <span class="text-subtitle-2 cursor-pointer">View All</span>
      </div>

      <v-slide-group
        v-model="selectedCategory"
        mandatory
        :show-arrows="true"
        class="category-slider"
      >
        <v-slide-group-item v-slot="{ isSelected, toggle }" value="All">
          <v-card
            :class="[
              'category-pill',
              isSelected ? 'active-pill' : 'inactive-pill'
            ]"
            flat
            @click="toggle"
          >
            <div class="d-flex align-center pa-2 pr-4">
              <v-avatar
                size="36"
                :color="isSelected ? 'white' : 'grey-lighten-4'"
                class="mr-3"
              >
                <v-icon
                  size="20"
                  :color="isSelected ? 'primary' : 'grey-darken-1'"
                >
                  mdi-star-outline
                </v-icon>
              </v-avatar>
              <span class="text-body-2">All</span>
            </div>
          </v-card>
        </v-slide-group-item>

        <v-slide-group-item
          v-for="cat in categoriesList"
          :key="cat.id"
          v-slot="{ isSelected, toggle }"
          :value="cat.id"
        >
          <v-card
            :class="[
              'category-pill mx-2',
              isSelected ? 'active-pill' : 'inactive-pill'
            ]"
            flat
            @click="toggle"
          >
            <div class="d-flex align-center pa-2 pr-4">
              <v-avatar
                size="36"
                :color="isSelected ? 'white' : 'grey-lighten-4'"
                class="mr-3 shadow-sm"
              >
                <v-icon
                  size="20"
                  :color="isSelected ? 'primary' : 'grey-darken-3'"
                >
                  mdi-food-croissant
                </v-icon>
              </v-avatar>
              <span class="text-body-2">{{ cat.name }}</span>
            </div>
          </v-card>
        </v-slide-group-item>
      </v-slide-group>
    </div>
    <!-- Products Grid -->
    <div class="d-flex justify-space-between align-center mb-4 px-1">
      <span class="text-subtitle-2 text-grey-darken-4">Menus List</span>
      <span class="text-subtitle-2 cursor-pointer">View All</span>
    </div>
    <v-row>
      <v-col
        v-for="product in filteredProducts"
        :key="product.id"
        cols="12"
        sm="6"
        md="4"
        lg="3"
      >
        <v-card
          flat
          class="product-card rounded-xl border-sm"
          @click="handleProductClick(product)"
        >
          <v-img :src="product.image_url" height="160" cover />
          <v-card-text class="pa-3 text-start">
            <div class="text-subtitle-2 font-weight-bold text-truncate">
              {{ product.name }}
            </div>
            <div class="d-flex align-center justify-space-between mt-1">
              <div
                v-if="product.has_variants"
                class="text-h6 font-weight-black text-primary"
              >
                ${{ product.variants[0].price }}
              </div>
              <div v-else class="text-h6 font-weight-black text-primary">
                ${{ product.price }}
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>


<style scoped>
  .product-card {
    transition: transform 0.2s;
    cursor: pointer;
  }
  .product-card:hover {
    transform: translateY(-4px);
  }
  /* The Pill Container */
  .category-pill {
    border-radius: 16px !important;
    transition: all 0.3s ease;
    min-width: 120px;
    cursor: pointer;
    border: 1px solid transparent !important;
  }

  /* Selected State (Orange style from image) */
  .active-pill {
    background: #ff7043 !important; /* Deep Orange */
    color: white !important;
    box-shadow: 0 4px 15px rgba(255, 112, 67, 0.4) !important;
  }

  /* Unselected State */
  .inactive-pill {
    background: white !important;
    color: #2c3e50 !important;
    border: 1px solid #f0f0f0 !important;
  }

  /* Avatar/Icon logic */
  .v-avatar {
    transition: background-color 0.3s ease;
  }

  .category-slider {
    background: transparent;
  }
</style>
