<template>
  <v-container fluid class="pa-0">
    <!-- Category Filter -->
    <v-chip-group
      v-model="selectedCategory"
      mandatory
      selected-class="bg-primary text-white"
      class="mb-6"
    >
      <v-chip value="All" class="px-6">All Items</v-chip>
      <v-chip
        v-if="categoryStore.categories.leght > 0"
        v-for="cat in categoryStore.categories"
        :key="cat.id"
        :value="cat.id"
      >
        {{ cat.name }}
      </v-chip>
    </v-chip-group>
    <!-- Products Grid -->
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
              <span class="text-h6 font-weight-black text-primary">
                ${{ product.price }}
              </span>
              <v-icon
                :icon="
                  product.type === 'stock'
                    ? 'mdi-plus-circle'
                    : 'mdi-cog-outline'
                "
                color="primary"
              />
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
  import { ref, computed, onMounted } from 'vue'
  import { useProductStore } from '@/stores/productStore'
  import { useCategoryStore } from '@/stores/categoryStore'
  import { usePosStore } from '@/stores/posStore' // to get selectedStore
  import { useMenuStore } from '@/stores/menuStore'

  const productStore = useProductStore()
  const categoryStore = useCategoryStore()
  const menuStore = useMenuStore()

  const posStore = usePosStore() 

  const search = ref('')

  const selectedCategory = ref('All')

  // Fetch products and categories
  onMounted(async () => {
    await productStore.fetchProducts()
    await categoryStore.fetchCategories()
  })

  const filteredProducts = computed(() => {
    if (posStore.selectedStore.type === 'hospitality') {
      const data = menuStore.menus?.data || []
      if (!search.value) return data
      return data.filter(p =>
        p.name.toLowerCase().includes(search.value.toLowerCase())
      )
    } else {
      const data = productStore.products?.data || []
      if (!search.value) return data
      return data.filter(p =>
        p.name.toLowerCase().includes(search.value.toLowerCase())
      )
    }
    // const data = productStore.products.data || []
    // return data
  })
  // Emits
  const emit = defineEmits(['select', 'quick-add'])

  function handleProductClick(product) {
    // Retail / Mart: quick-add
    if (!product.has_variants ) {
      // storeStore.selectedStore.type === 'retail'
      emit('quick-add', product)
    } else {
      // Coffee/Restaurant: open customizer
      emit('select', product)
    }
  }
</script>

<style scoped>
  .product-card {
    transition: transform 0.2s;
    cursor: pointer;
  }
  .product-card:hover {
    transform: translateY(-4px);
  }
</style>
