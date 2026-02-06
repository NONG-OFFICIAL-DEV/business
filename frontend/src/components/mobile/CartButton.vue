<script setup>
  import { useCurrency } from '@/composables/useCurrency.js'
  const { formatCurrency } = useCurrency()
  defineProps({
    totalItems: Number,
    totalPrice: Number
  })
  defineEmits(['open'])
</script>

<template>
  <div class="cart-anchor px-4">
    <v-btn
      block
      size="large"
      color="primary"
      rounded="pill"
      class="shadow-top"
      elevation="8"
      @click="$emit('open')"
    >
      <v-avatar color="white" size="28" class="mr-3">
        <span class="text-caption font-weight-bold" style="color: #3b828e">
          {{ totalItems }}
        </span>
      </v-avatar>
      Review Basket
      <v-spacer />
      <span class="ms-4">
        {{ formatCurrency(totalPrice) }}
      </span>
    </v-btn>
  </div>
</template>

<style scoped>
  .cart-anchor {
    position: fixed;
    bottom: 24px; /* Native feel */
    left: 0;
    right: 0;
    z-index: 999;
  }
  .shadow-top {
    box-shadow: 0 10px 30px rgba(59, 130, 142, 0.4) !important;
  }
</style>
