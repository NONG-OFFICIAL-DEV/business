<script setup>
  import { ref, computed, onMounted } from 'vue'
  import { useProductStore } from '@/stores/productStore'
  import { useCategoryStore } from '@/stores/categoryStore'
  import { usePosStore } from '@/stores/posStore' // to get selectedStore
  import { useMenuStore } from '@/stores/menuStore'
  import { useCategoryMenuStore } from '@/stores/categoryMenu'
  import { useLoadingStore } from '@/stores/loading'

  const productStore = useProductStore()
  const categoryStore = useCategoryStore()
  const menuStore = useMenuStore()
  const menuCategoryStore = useCategoryMenuStore()

  const posStore = usePosStore()
  const loadingStore = useLoadingStore()

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
      const data = categoryStore.categories.data || []
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
    await productStore.fetchProducts({}, { loading: 'skeleton' })
    await categoryStore.fetchCategories({}, { loading: 'skeleton' })
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
          <v-btn
            :color="isSelected ? 'primary' : undefined"
            class="ma-2 text-none"
            rounded
            prepend-icon="mdi-food-croissant"
            variant="tonal"
            @click="toggle"
          >
            All
          </v-btn>
        </v-slide-group-item>
        <v-slide-group-item
          v-for="cat in categoriesList"
          :key="cat.id"
          :value="cat.id"
          v-slot="{ isSelected, toggle }"
        >
          <v-btn
            :color="isSelected ? 'primary' : undefined"
            class="ma-2 text-none"
            rounded
            prepend-icon="mdi-food-croissant"
            variant="tonal"
            @click="toggle"
          >
            {{ cat.name }}
          </v-btn>
        </v-slide-group-item>
        <v-slide-group-item v-for="(item, n) in 12" :key="n" :value="n">
          <v-skeleton-loader
            v-if="!categoriesList.length"
            width="200"
            class="px-0 ma-1 rounded-lg"
          ></v-skeleton-loader>
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
    <v-row v-if="loadingStore.isLoading">
      <v-col v-for="(item, n) in 12" :key="n" cols="12" lg="3" md="3">
        <v-skeleton-loader
          type="image, list-item-two-line"
          class="rounded-xl"
        ></v-skeleton-loader>
      </v-col>
    </v-row>
    <v-row v-if="!filteredProducts.length && !loadingStore.isLoading" justify="center" class="py-12">
      <v-col cols="12" class="text-center">
        <v-icon
          icon="mdi-package-variant-closed"
          size="64"
          color="grey-lighten-1"
          class="mb-4"
        ></v-icon>

        <div class="text-h6 text-grey-darken-1">No Items Found</div>
        <p class="text-body-2 text-grey">
          We couldn't find any POS operators matching your criteria.
        </p>
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

  .category-slider {
    background: transparent;
  }

  :deep(.v-skeleton-loader__image) {
    height: 200px;
    border-radius: 25px;
  }
</style>
