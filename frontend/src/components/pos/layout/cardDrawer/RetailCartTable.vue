<script setup>
  import { useCurrency } from '@/composables/useCurrency'
  const { formatCurrency } = useCurrency()

  defineProps({
    items: Array
  })

  defineEmits(['update-qty', 'remove'])
</script>

<template>
  <v-table density="compact" class="pos-table">
    <thead>
      <tr>
        <th class="text-left text-grey-darken-2">Item Description</th>
        <th class="text-center text-grey-darken-2">Qty</th>
        <th class="text-right text-grey-darken-2">Total</th>
        <th style="width: 40px"></th>
      </tr>
    </thead>

    <tbody>
      <tr v-for="item in items" :key="item.id" class="item-row">
        <td>
          <div class="item-name text-truncate">{{ item.name }}</div>
          <div class="item-meta">
            {{ formatCurrency(item.price) }} <span v-if="item.qty > 1">per unit</span>
          </div>
        </td>

        <td class="text-center">
          <!-- <div class="qty-badge"> -->
            <span class="text-body-2 font-weight-black">X {{ item.qty }}</span>
          <!-- </div> -->
        </td>

        <td class="text-right">
          <div class="font-weight-black text-primary">
            {{ formatCurrency(item.price * item.qty) }}
          </div>
        </td>

        <td class="text-right pa-0">
          <v-btn
            icon="mdi-delete-outline"
            color="grey-lighten-1"
            size="x-small"
            variant="text"
            class="remove-btn"
            @click="$emit('remove', item.id)"
          />
        </td>
      </tr>
    </tbody>
  </v-table>
</template>

<style scoped>
.pos-table {
  background: transparent !important;
}

/* Header styling for a clean "Receipt" look */
.pos-table thead th {
  font-size: 0.75rem !important;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-weight: 700 !important;
  border-bottom: 2px solid #333 !important;
}

.item-row:hover {
  background-color: #f5f5f5;
}

.pos-table :deep(td) {
  height: 48px !important; /* Thinner rows for more items on screen */
  border-bottom: 1px solid #eee !important;
}

.item-name {
  font-size: 0.9rem;
  font-weight: 600;
  max-width: 180px;
}

.item-meta {
  font-size: 0.7rem;
  color: #888;
  margin-top: -2px;
}

/* Minimalist Qty Indicator */
.qty-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: #eee;
  border-radius: 4px;
  min-width: 32px;
  height: 24px;
}

.remove-btn:hover {
  color: #ff5252 !important;
}
</style>
