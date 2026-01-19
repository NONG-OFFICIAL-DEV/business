<script setup>
defineProps({
  product: Object,
  qty: {
    type: Number,
    default: 0
  }
})

defineEmits(['add', 'update'])
</script>

<template>
  <v-card flat rounded="xl" class="bg-white border-0 elevation-0">
    <div class="d-flex pa-2">
      <v-img
        :src="product.image_url"
        width="90"
        height="90"
        cover
        class="rounded-xl flex-grow-0"
      />

      <div class="ml-3 flex-grow-1 d-flex flex-column justify-center">
        <div class="text-body-2 font-weight-bold mb-0">
          {{ product.name }}
        </div>

        <div class="text-caption text-grey-darken-1 mb-2 line-clamp-2">
          {{ product.desc }}
        </div>

        <div class="d-flex justify-space-between align-center">
          <span class="text-subtitle-2 font-weight-black text-teal-darken-2">
            ${{ product.price }}
          </span>

          <div
            class="d-flex align-center border rounded-pill px-1 bg-grey-lighten-5"
          >
            <v-btn
              icon="mdi-minus"
              size="32"
              variant="text"
              :color="qty <= 1 ? 'error' : 'default'"
              @click="$emit('update', product.id, -1)"
              :disabled="qty === 0"
            />

            <span class="px-3 font-weight-bold">{{ qty }}</span>

            <v-btn
              icon="mdi-plus"
              size="32"
              variant="text"
              color="primary"
              @click="$emit('add', product)"
            />
          </div>
        </div>
      </div>
    </div>
  </v-card>
</template>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-height: 1.2;
}
</style>
