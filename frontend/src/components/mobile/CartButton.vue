<script setup>
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
      class="text-none shadow-top"
      elevation="8"
      @click="$emit('open')"
    >
      <v-avatar color="white" size="28" class="mr-3">
        <span class="text-caption font-weight-bold" style="color: #3b828e">
          {{ totalItems }}
        </span>
      </v-avatar>

      <span
        class="text-subtitle-2 font-weight-bold text-uppercase tracking-widest"
      >
        Review Basket
      </span>

      <v-spacer />
      <span class="text-subtitle-2 font-weight-black ms-4">
        ${{ totalPrice.toFixed(2) }}
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
