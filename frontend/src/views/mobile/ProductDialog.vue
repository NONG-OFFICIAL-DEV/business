<template>
  <v-dialog
    v-model="model"
    max-width="500"
    transition="dialog-bottom-transition"
    class="product-modal"
  >
    <v-card class="rounded-xl overflow-hidden border-0">
      <div class="position-relative">
        <v-img
          :src="product?.image_url"
          height="340"
          cover
          class="bg-grey-lighten-4"
        >
          <div class="pa-4 d-flex justify-end">
            <v-btn
              icon="mdi-close"
              size="small"
              color="white"
              variant="flat"
              class="close-btn-glass shadow-sm"
              @click="model = false"
            />
          </div>
        </v-img>
      </div>

      <v-card-text class="pa-6">
        <div class="d-flex justify-space-between align-center mb-1">
          <span class="text-overline text-grey-darken-1 font-weight-bold">
            {{ product?.category.name || 'Premium Collection' }}
          </span>
          <div class="text-h6 font-weight-black text-black">
            ${{ product?.price }}
          </div>
        </div>

        <h2 class="text-h5 font-weight-black mb-3">
          {{ product?.name }}
        </h2>

        <p class="text-body-2 text-grey-darken-1 leading-relaxed mb-8">
          This premium selection features a modern design aesthetic paired with
          high-quality materials. Perfect for those who appreciate minimalist
          style and everyday durability.
        </p>

        <v-btn
          block
          color="black"
          size="x-large"
          variant="flat"
          class="rounded-pill text-none font-weight-bold py-4 mt-2"
          @click="handleAddToCart"
        >
          <v-icon start class="mr-2">mdi-plus</v-icon>
          Add to cart
        </v-btn>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script setup>
  import { computed } from 'vue'
  import { useCartStore } from '@/stores/cartStore'

  const props = defineProps({
    modelValue: Boolean,
    product: Object
  })

  const emit = defineEmits(['update:modelValue'])
  const cartStore = useCartStore()

  const model = computed({
    get: () => props.modelValue,
    set: val => emit('update:modelValue', val)
  })

  const handleAddToCart = () => {
    if (!props.product) return
    cartStore.addToCart(props.product)
    model.value = false
  }
</script>

<style scoped>
  /* Glassmorphism for the close button */
  .close-btn-glass {
    background: rgba(255, 255, 255, 0.8) !important;
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.3);
  }

  .leading-relaxed {
    line-height: 1.6;
  }

  .text-overline {
    letter-spacing: 1px !important;
  }

  /* Custom shadow to make it pop */
  .product-modal :deep(.v-overlay__content) {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
  }

  /* Image zoom effect on hover */
  .v-img :deep(img) {
    transition: transform 0.6s ease;
  }
  .product-modal:hover :deep(img) {
    transform: scale(1.05);
  }
</style>
