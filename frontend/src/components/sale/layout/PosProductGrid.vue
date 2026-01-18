<script setup>
  import { ref, computed } from 'vue'

  const props = defineProps({
    products: Array
  })

  const emit = defineEmits(['select', 'quick-add'])
  const selectedCategory = ref('All')

  const categories = computed(() => [
    'All',
    ...new Set(props.products.map(p => p.category))
  ])

  const displayProducts = computed(() => {
    if (selectedCategory.value === 'All') return props.products
    return props.products.filter(p => p.category === selectedCategory.value)
  })

  function handleProductClick(product) {
    // If Mart Item -> Quick Add
    if (product.type === 'stock') {
      emit('quick-add', product)
    }
    // If Coffee/Restaurant -> Customize
    else {
      emit('select', product)
    }
  }
</script>

<template>
  <v-container fluid class="pa-6">
    <!-- <v-chip-group v-model="selectedCategory" mandatory class="mb-4">
      <v-chip
        v-for="cat in categories"
        :key="cat"
        :value="cat"
        variant="outlined"
      >
        {{ cat }}
      </v-chip>
    </v-chip-group> -->

    <v-row>
      <v-col
        v-for="product in displayProducts"
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

<style scoped>
  .product-card {
    transition: transform 0.2s;
    cursor: pointer;
  }
  .product-card:hover {
    transform: translateY(-4px);
  }
</style>
