<script setup>
  import { ref, computed } from 'vue'

  const props = defineProps({
    products: Array,
    categories: Array,
    mode: {
      type: String,
      default: 'retail' // 'retail' or 'hospitality'
    }
  })

  const emit = defineEmits(['select', 'quick-add'])
  const selectedCategory = ref('All')

  const displayProducts = computed(() => {
    if (selectedCategory.value === 'All') return props.products
    if (selectedCategory.value === 'All') return props.products
    return props.products.filter(
      p => p.menu_category_id === selectedCategory.value
    )
  })

  function handleProductClick(product) {
    // If Mart Item -> Quick Add
    if (product.has_variants === false || props.mode === 'retail') {
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
    <v-chip-group
      v-model="selectedCategory"
      mandatory
      selected-class="bg-primary text-white"
    >
      <v-chip value="All" class="px-6">All Items</v-chip>
      <v-chip
        v-for="cat in categories"
        :key="cat.name"
        :value="cat.id"
      >
        {{ cat.name }}
      </v-chip>
    </v-chip-group>

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
