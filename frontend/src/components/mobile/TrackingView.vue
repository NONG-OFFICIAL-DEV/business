<script setup>
  defineProps({
    cart: { type: Array, default: () => [] },
    tableNumber: String
  })
  defineEmits(['reset'])

  // Optional: format price/total if passed, or just list items
</script>

<template>
  <v-container
    class="fill-height bg-grey-lighten-5 d-flex align-center justify-center"
  >
    <div class="w-100 px-4" style="max-width: 450px">
      <div class="text-center mb-6">
        <v-avatar
          color="success"
          size="72"
          class="elevation-4 mb-4 pulse-animation"
        >
          <v-icon size="40" color="white">mdi-check-bold</v-icon>
        </v-avatar>
        <h2 class="text-h5 font-weight-black mb-1">Order Received!</h2>
        <p class="text-body-2 text-medium-emphasis">
          We're preparing your food for
          <b>Table {{ tableNumber }}</b>
        </p>
      </div>

      <v-card flat class="rounded-xl border-sm mb-6 overflow-hidden">
        <div class="pa-4 bg-white">
          <div class="text-overline font-weight-bold text-grey mb-3">
            Order Summary
          </div>

          <v-divider class="mb-4" />
          <div
            v-for="item in cart"
            :key="item.id"
            class="d-flex justify-space-between mb-2"
          >
            <span class="text-body-2">
              <span class="font-weight-bold text-teal-darken-2">
                {{ item.qty }}x
              </span>
              {{ item.name }}
            </span>
            <span class="text-body-2 text-medium-emphasis">
              ${{ (item.price * item.qty).toFixed(2) }}
            </span>
          </div>

          <v-divider class="border-dashed my-4" />

          <div class="d-flex align-center justify-center py-2">
            <v-progress-linear
              indeterminate
              color="#3b828e"
              rounded
              height="6"
              class="rounded-pill"
            />
          </div>
          <div
            class="text-center text-caption text-teal-darken-2 font-weight-bold mt-2"
          >
            CHEF IS COOKING...
          </div>
        </div>
      </v-card>

      <div class="px-4">
        <v-btn
          block
          color="#3b828e"
          size="large"
          variant="tonal"
          class="rounded-pill font-weight-bold mb-3 text-none"
          @click="$emit('reset')"
        >
          Add more items
        </v-btn>

        <v-btn
          block
          variant="text"
          color="grey-darken-1"
          size="small"
          class="text-none"
        >
          Need help? Call staff
        </v-btn>
      </div>
    </div>
  </v-container>
</template>

<style scoped>
  /* High-end pulse animation for the success icon */
  .pulse-animation {
    animation: pulse 2s infinite;
  }

  @keyframes pulse {
    0% {
      box-shadow: 0 0 0 0 rgba(76, 175, 80, 0.4);
    }
    70% {
      box-shadow: 0 0 0 15px rgba(76, 175, 80, 0);
    }
    100% {
      box-shadow: 0 0 0 0 rgba(76, 175, 80, 0);
    }
  }

  .border-dashed {
    border-style: dashed !important;
    border-top-width: 2px !important;
  }

  /* Colors to match your teal theme */
  .text-teal-darken-2 {
    color: #3b828e !important;
  }
</style>
