<script setup>
  import { ref, computed, watch } from 'vue'

  const props = defineProps({
    modelValue: Boolean,
    product: Object
  })

  const emit = defineEmits(['update:modelValue', 'add-to-cart'])

  // --- State for selections ---
  const quantity = ref(1)
  const selectedCup = ref('Small (Hot)')
  const selectedSugar = ref('Less Sugar')
  const orderType = ref('Dine-in')

  // --- Options Data ---
  const cupOptions = ['Small (Hot)', 'Medium (Ice)', 'Large (Ice)']
  const sugarOptions = ['Less Sugar', 'Normal Sugar']

  // --- RESET LOGIC ---
  // This is the fix: whenever the dialog opens, reset all fields
  watch(() => props.modelValue, (isOpen) => {
    if (isOpen) {
      quantity.value = 1
      selectedCup.value = 'Small (Hot)'
      selectedSugar.value = 'Less Sugar'
      orderType.value = 'Dine-in'
    }
  })

  // --- Calculations ---
  const totalPrice = computed(() => {
    const basePrice = parseFloat(props.product?.price || 0)
    return (basePrice * quantity.value).toFixed(2)
  })

  // --- Actions ---
  function close() {
    emit('update:modelValue', false)
  }

  function submitOrder() {
    const orderData = {
      ...props.product, // Keep original product data (id, name, type)
      qty: quantity.value,
      customizations: {
        cup: selectedCup.value,
        sugar: selectedSugar.value,
        orderType: orderType.value
      }
      // Note: We don't need to send 'finalPrice' because the 
      // parent computed 'total' will recalculate based on qty * price
    }
    emit('add-to-cart', orderData)
    close()
  }
</script>

<template>
  <v-dialog
    :model-value="modelValue"
    @update:model-value="close"
    max-width="435"
  >
    <v-card rounded="xl" class="pa-4 shadow-lg">
      <v-card-title class="d-flex justify-space-between align-center px-2">
        <div class="d-flex align-center font-weight-bold">
          <v-icon icon="mdi-tray-arrow-down" class="mr-2" size="small" color="primary" />
          Customize Order
        </div>
        <v-btn icon="mdi-close" variant="tonal" size="small" @click="close" />
      </v-card-title>

      <v-card-text class="pa-2">
        <label class="text-subtitle-2 font-weight-bold">Order Type</label>
        <v-btn-toggle
          v-model="orderType"
          mandatory
          color="orange-darken-2"
          class="d-flex mb-4 mt-2"
          variant="outlined"
          density="compact"
        >
          <v-btn value="Dine-in" class="flex-grow-1">Dine-in</v-btn>
          <v-btn value="Takeaway" class="flex-grow-1">Takeaway</v-btn>
          <v-btn value="Delivery" class="flex-grow-1">Delivery</v-btn>
        </v-btn-toggle>

        <div class="coffee-box d-flex align-center mb-5 pa-3 rounded-lg">
          <v-avatar size="70" rounded="lg">
            <v-img :src="product?.image_url" cover />
          </v-avatar>

          <div class="ml-3 flex-grow-1">
            <div class="text-subtitle-2 font-weight-bold text-truncate" style="max-width: 150px;">
              {{ product?.name }}
            </div>
            <div class="text-h6 font-weight-black text-orange-darken-2">
              ${{ product?.price }}
            </div>
          </div>

          <div class="d-flex align-center bg-white rounded-pill border px-1 shadow-sm">
            <v-btn icon="mdi-minus" variant="text" size="x-small" @click="quantity > 1 ? quantity-- : null" />
            <span class="px-3 font-weight-bold">{{ quantity }}</span>
            <v-btn icon="mdi-plus" variant="text" size="x-small" @click="quantity++" />
          </div>
        </div>

        <template v-if="product?.type !== 'stock'">
            <div class="mb-4">
            <label class="text-subtitle-2 font-weight-bold d-block mb-2">Select a Cup</label>
            <v-radio-group v-model="selectedCup" hide-details density="compact">
                <v-radio
                v-for="cup in cupOptions"
                :key="cup"
                :label="cup"
                :value="cup"
                class="border rounded-lg mb-2 px-3 py-1"
                color="orange-darken-1"
                />
            </v-radio-group>
            </div>

            <div class="mb-4">
            <label class="text-subtitle-2 font-weight-bold d-block mb-2">Sugar Level</label>
            <v-radio-group v-model="selectedSugar" hide-details density="compact">
                <v-radio
                v-for="sugar in sugarOptions"
                :key="sugar"
                :label="sugar"
                :value="sugar"
                class="border rounded-lg mb-2 px-3 py-1"
                color="orange-darken-1"
                />
            </v-radio-group>
            </div>
        </template>
      </v-card-text>

      <v-card-actions class="pa-2">
        <v-btn
          block
          color="primary"
          height="54"
          variant="flat"
          rounded="lg"
          class="text-white font-weight-bold"
          @click="submitOrder"
        >
          Add to Order - ${{ totalPrice }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>