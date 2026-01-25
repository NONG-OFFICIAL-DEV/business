<script setup>
import { useCurrency } from '@/composables/useCurrency'

const { formatCurrency } = useCurrency()

defineProps({
  items: Array
})

defineEmits(['update-qty'])
</script>

<template>
  <v-card
    v-for="item in items"
    :key="item.id"
    flat
    rounded="lg"
    class="mb-2 border-sm"
  >
    <div class="pa-2 d-flex align-center">
      <v-avatar size="48" rounded="md" class="bg-grey-lighten-4 border">
        <v-img :src="item.image_url" cover />
      </v-avatar>

      <div class="ml-3 flex-grow-1">
        <div class="d-flex justify-space-between">
          <span class="font-weight-bold text-truncate">
            {{ item.name }}
          </span>
          <span class="font-weight-black text-primary">
            {{ formatCurrency(item.price * item.qty) }}
          </span>
        </div>

        <!-- Customizations -->
        <div
          v-if="Object.keys(item.customizations || {}).length"
          class="d-flex flex-wrap gap-1 mt-1"
        >
          <v-chip
            v-for="(val, key) in item.customizations"
            :key="key"
            size="x-small"
            variant="tonal"
          >
            {{ val }}
          </v-chip>
        </div>

        <!-- Qty -->
        <div class="d-flex justify-space-between align-center mt-2">
          <span class="text-caption text-grey">
            {{ formatCurrency(item.price) }}
          </span>

          <div class="d-flex align-center bg-grey-lighten-4 rounded-pill border">
            <v-btn
              icon="mdi-minus"
              size="x-small"
              variant="text"
              @click="$emit('update-qty', item.id, item.qty - 1)"
            />
            <span class="px-2 font-weight-bold text-caption">
              {{ item.qty }}
            </span>
            <v-btn
              icon="mdi-plus"
              size="x-small"
              variant="text"
              @click="$emit('update-qty', item.id, item.qty + 1)"
            />
          </div>
        </div>
      </div>
    </div>
  </v-card>
</template>
