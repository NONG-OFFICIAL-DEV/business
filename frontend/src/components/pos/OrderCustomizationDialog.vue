<script setup>
  import { ref, computed } from 'vue'

  const props = defineProps({
    modelValue: Boolean,
    product: Object
  })

  const emit = defineEmits(['update:modelValue', 'add-to-cart'])

  // State for selections
  const quantity = ref(1)
  const selectedCup = ref('Small (Hot)')
  const selectedSugar = ref('Less Sugar')
  const orderType = ref('pack')

  // Options Data
  const cupOptions = ['Small (Hot)', 'Medium (Ice)', 'Large (Ice)']
  const sugarOptions = ['Less Sugar', 'Normal Sugar']

  // --- Calculations ---

  const totalPrice = computed(() => {
    const basePrice = parseFloat(props.product?.price || 12.83)
    return (basePrice * quantity.value).toFixed(2)
  })

  // --- Actions ---

  function close() {
    emit('update:modelValue', false)
  }

  function submitOrder() {
    const orderData = {
      ...props.product,
      qty: quantity.value,
      customizations: {
        cup: selectedCup.value,
        sugar: selectedSugar.value,
        orderType: orderType.value
      },
      finalPrice: totalPrice.value
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
    <v-card rounded="xl" class="pa-4">
      <v-card-title class="d-flex justify-space-between align-center px-2">
        <div class="d-flex align-center font-weight-bold">
          <v-icon icon="mdi-tray-arrow-down" class="mr-2" size="small" />
          Add Order
        </div>
        <v-btn
          icon="mdi-close"
          variant="tonal"
          size="small"
          @click="close"
          color="grey-darken-2"
        />
      </v-card-title>

      <v-card-text class="pa-2">
        <label class="text-subtitle-2 font-weight-bold">Order Type</label>
        <v-btn-toggle
          v-model="orderType"
          mandatory
          color="primary"
          class="d-flex mb-4 mt-2"
          variant="outlined"
          density="compact"
        >
          <v-btn value="pack" flex-grow-1>
            <v-icon start>mdi-package-variant</v-icon>
            Dine-in
          </v-btn>
          <v-btn value="drink_in" flex-grow-1>
            <v-icon start>mdi-coffee-to-go</v-icon>
            Takeaway
          </v-btn>
          <v-btn value="delivery" flex-grow-1>
            <v-icon start>mdi-moped</v-icon>
            Delivery
          </v-btn>
        </v-btn-toggle>

        <div class="coffee-box d-flex align-center mb-5 pa-3 rounded-lg">
          <v-avatar size="70" rounded="lg" color="grey-lighten-4">
            <v-img
              :src="product?.image_url || 'https://via.placeholder.com/80'"
              cover
            />
          </v-avatar>

          <div class="ml-3 flex-grow-1">
            <div class="text-subtitle-2 font-weight-bold">
              {{ product?.name || 'French Vanilla Fantasy' }}
            </div>
            <div class="text-h6 font-weight-black text-orange">
              ${{ product?.price || '12.83' }}
            </div>
          </div>

          <div class="d-flex align-center bg-white rounded-pill border px-1">
            <v-btn
              icon="mdi-minus"
              variant="text"
              size="x-small"
              @click="quantity > 1 ? quantity-- : null"
            />
            <span class="px-3 font-weight-bold">{{ quantity }}</span>
            <v-btn
              icon="mdi-plus"
              variant="text"
              size="x-small"
              @click="quantity++"
            />
          </div>
        </div>

        <div class="mb-4">
          <label class="text-subtitle-2 font-weight-bold d-block mb-2">
            Select a Cup
            <span class="text-orange">*</span>
          </label>
          <v-radio-group
            v-model="selectedCup"
            hide-details
            class="custom-radio-group"
            density="compact"
          >
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
          <label class="text-subtitle-2 font-weight-bold d-block mb-2">
            Sugar Level
            <span class="text-orange">*</span>
          </label>
          <v-radio-group
            v-model="selectedSugar"
            hide-details
            class="custom-radio-group"
            density="compact"
          >
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
      </v-card-text>

      <v-card-actions class="pa-2">
        <v-btn
          block
          color="orange-lighten-2"
          height="54"
          variant="flat"
          rounded="lg"
          class="text-white font-weight-bold text-none"
          size="large"
          @click="submitOrder"
        >
          (${{ totalPrice }}) Add to Order
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<style scoped>
  .custom-radio-group :deep(.v-selection-control-group) {
    gap: 0;
  }
  .coffee-box {
    border: 1px solid #e0e0e0;
    background: #fafafa;
  }
</style>
