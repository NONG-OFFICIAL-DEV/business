<script setup>
  import { useCurrency } from '@/composables/useCurrency'
  const { formatCurrency } = useCurrency()
  import { usePosStore } from '@/stores/posStore'
  const posStore = usePosStore()

  defineProps({
    subtotal: Number,
    total: Number,
    paymentMethod: String,
    paymentMethods: Array,
    disabled: Boolean
  })

  const emit = defineEmits(['select-payment', 'checkout', 'print-bill'])

  const handleClick = () => {
    if (posStore.isPrintBill) {
      emit('print-bill')
    } else {
      emit('checkout')
    }
  }
</script>

<template>
  <v-sheet class="pa-4 border-t bg-white">
    <!-- Payment -->
    <v-row no-gutters class="mx-n1 mb-4">
      <v-col
        cols="4"
        v-for="method in paymentMethods"
        :key="method.id"
        class="pa-1"
      >
        <v-card
          flat
          class="text-center py-2 rounded-lg border-sm"
          :color="paymentMethod === method.id ? 'orange-lighten-5' : 'white'"
          @click="$emit('select-payment', method.id)"
        >
          <v-icon :icon="method.icon" size="20" />
          <div class="text-caption font-weight-bold">
            {{ method.label }}
          </div>
        </v-card>
      </v-col>
    </v-row>

    <!-- Totals -->
    <div class="mb-3">
      <div class="d-flex justify-space-between text-caption">
        <span>Subtotal</span>
        <span>{{ formatCurrency(subtotal) }}</span>
      </div>

      <div class="d-flex justify-space-between text-h6 font-weight-black">
        <span>Total</span>
        <span class="text-primary">
          {{ formatCurrency(total) }}
        </span>
      </div>
    </div>

    <!-- Action -->
    <v-btn
      block
      height="52"
      color="primary"
      flat
      rounded="lg"
      class="font-weight-black text-none"
      :disabled="disabled"
      @click="handleClick()"
    >
      <v-icon start size="22" class="me-2">
        {{
          posStore.isPrintBill ? 'mdi-printer-check' : 'mdi-credit-card-check'
        }}
      </v-icon>

      {{ posStore.isPrintBill ? 'PRINT & PAY' : 'CONFIRM PAYMENT' }}
    </v-btn>
  </v-sheet>
</template>
