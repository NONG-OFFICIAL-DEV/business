<script setup>
  import { ref } from 'vue'
  const props = defineProps({
    items: Array,
    cart: Array
  })
  const emit = defineEmits(['add', 'update'])
  const MAX_QTY_PER_ITEM = 5
  const snackbar = ref(false)

  const getQty = (cart, id) => {
    return cart.find(i => i.id === id)?.qty || 0
  }

  // New function to handle the click and the alert logic
  const handleIncrease = (item, isNewAdd = false) => {
    const currentQty = getQty(props.cart, item.id || item) // handle both object and id

    if (currentQty >= MAX_QTY_PER_ITEM) {
      snackbar.value = true
      return // Stop the execution here
    }

    if (isNewAdd) {
      emit('add', item)
    } else {
      emit('update', item, 1) // item here is id
    }
  }
</script>

<template>
  <v-snackbar
    v-model="snackbar"
    :timeout="2000"
    location="top"
    color="amber-darken-3"
    rounded="pill"
    class="mt-4"
  >
    <div class="d-flex align-center">
      <v-icon start icon="mdi-alert-circle" />
      <span>Maximum limit of {{ MAX_QTY_PER_ITEM }} reached for this item</span>
    </div>
  </v-snackbar>

  <v-row class="pa-2">
    <transition-group name="list-stagger">
      <v-col v-for="p in items" :key="p.id" cols="6" class="pa-2">
        <v-card flat rounded="xl" class="pa-3 product-card border">
          <v-img
            :src="p.image_url"
            aspect-ratio="1"
            cover
            class="rounded-circle mx-auto mb-3"
            width="100"
          />

          <div
            class="text-subtitle-2 font-weight-bold text-center mb-1 truncate"
          >
            {{ p.name }}
          </div>

          <div class="d-flex justify-space-between align-center px-1">
            <span class="text-subtitle-1 font-weight-black">
              ${{ p.price }}
            </span>

            <div v-if="getQty(cart, p.id) === 0">
              <v-btn
                icon="mdi-plus"
                size="30"
                color="success"
                elevation="1"
                variant="flat"
                @click="handleIncrease(p, true)"
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
                :class="{ 'opacity-30': getQty(cart, p.id) >= 5 }"
                @click="handleIncrease(p.id)"
              />
            </div>
          </div>
        </v-card>
      </v-col>
    </transition-group>
  </v-row>
</template>

<style scoped>
  .opacity-30 {
    opacity: 0.3;
  }
  .v-row {
    width: 100%;
    margin: 0 !important;
  }
  .product-card {
    background: white;
    transition: transform 0.2s ease;
  }
  .product-card:active {
    transform: scale(0.98);
  }
</style>
