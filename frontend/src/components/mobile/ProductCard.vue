<script setup>
defineProps({
  items: Array,
  cart: Array
})
const emit = defineEmits(['add', 'update'])

const getQty = (cart, id) => {
  return cart.find(i => i.id === id)?.qty || 0
}
</script>

<template>
  <v-row class="pa-2">
    <transition-group name="list-stagger" tag="div" class="v-row ma-0">
      <v-col 
        v-for="p in items" 
        :key="p.id" 
        cols="6" 
        class="pa-2"
      >
        <v-card flat rounded="xl" class="pa-3 product-card">
          <v-img
            :src="p.image_url"
            aspect-ratio="1"
            cover
            class="rounded-circle mx-auto mb-3"
            width="100"
          />
  
          <div class="text-subtitle-2 font-weight-bold text-center mb-1">
            {{ p.name }}
          </div>
  <!-- 
          <div class="d-flex align-center justify-center mb-3">
            <v-icon icon="mdi-leaf" color="green-lighten-1" size="14" class="mr-1" />
            <span class="text-caption text-grey-lighten-1 font-weight-bold">
              {{ p.calories }} kcal.
            </span>
          </div> -->
  
          <div class="d-flex justify-space-between align-center px-1">
            <span class="text-subtitle-1 font-weight-black">${{ p.price }}</span>
  
            <div v-if="getQty(cart, p.id) === 0">
              <v-btn
                icon="mdi-plus"
                size="30"
                color="success"
                elevation="1"
                variant="flat"
                @click="emit('add', p)"
              />
            </div>
  
            <div 
              v-else 
              class="d-flex align-center border rounded-pill px-1 bg-grey-lighten-5"
            >
              <v-btn
                icon="mdi-minus"
                size="24"
                variant="text"
                density="comfortable"
                @click="emit('update', p.id, -1)"
              />
              <span class="mx-2 text-caption font-weight-bold">
                {{ getQty(cart, p.id) }}
              </span>
              <v-btn
                icon="mdi-plus"
                size="24"
                variant="text"
                density="comfortable"
                color="success"
                @click="emit('update', p.id, 1)"
              />
            </div>
          </div>
        </v-card>
      </v-col>
    </transition-group>
  </v-row>
</template>

<style scoped>
.product-card {
  background: white;
  transition: transform 0.2s ease;
}
.product-card:active {
  transform: scale(0.98);
}
</style>