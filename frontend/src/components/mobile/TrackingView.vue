<script setup>
  import { computed } from 'vue'

  const props = defineProps({
    cart: { type: Array, default: () => [] },
    tableNumber: String
  })

  defineEmits(['reset'])

  const totalAmount = computed(() => {
    return props.cart
      .reduce((sum, item) => sum + item.price * item.qty, 0)
      .toFixed(2)
  })
</script>

<template>
  <v-container
    class="fill-height bg-grey-lighten-5 d-flex align-center justify-center"
  >
    <div class="w-100 px-4" style="max-width: 450px">
      <div class="text-center mb-8">
        <v-avatar
          color="success"
          size="80"
          class="elevation-6 mb-4 pulse-animation"
        >
          <v-icon size="48" color="white">mdi-silverware-clean</v-icon>
        </v-avatar>
        <h2 class="text-h4 font-weight-black mb-1" style="color: #2c5157">
          Order Placed!
        </h2>
        <p class="text-body-1 text-medium-emphasis">
          Sit back and relax, Table
          <b>{{ tableNumber }}</b>
          .
        </p>
      </div>

      <v-card flat class="rounded-xl border-sm mb-6 receipt-card">
        <div class="pa-5 bg-white">
          <div class="d-flex justify-space-between mb-6 px-2">
            <div class="d-flex flex-column align-center">
              <v-icon color="success">mdi-check-circle</v-icon>
              <span class="text-caption font-weight-bold">Ordered</span>
            </div>
            <v-divider class="mt-3 mx-2" color="success" thickness="2" />
            <div class="d-flex flex-column align-center">
              <v-icon color="#3b828e" class="chef-icon-animation">
                mdi-fire
              </v-icon>
              <span class="text-caption font-weight-bold text-teal-darken-2">
                Cooking
              </span>
            </div>
            <v-divider class="mt-3 mx-2" />
            <div class="d-flex flex-column align-center text-grey-lighten-1">
              <v-icon>mdi-room-service</v-icon>
              <span class="text-caption">Ready</span>
            </div>
          </div>

          <div class="text-overline font-weight-black text-grey-darken-1 mb-2">
            Receipt Details
          </div>

          <div
            v-for="item in cart"
            :key="item.id"
            class="d-flex justify-space-between mb-3"
          >
            <div class="d-flex flex-column">
              <span class="text-body-2 font-weight-bold">
                {{ item.name }}
              </span>
              <span class="text-caption text-grey">Qty: {{ item.qty }}</span>
            </div>
            <span class="text-body-2 font-weight-medium">
              ${{ (item.price * item.qty).toFixed(2) }}
            </span>
          </div>

          <v-divider class="border-dashed my-4" />

          <div class="d-flex justify-space-between align-center py-1">
            <span class="text-subtitle-1 font-weight-bold">Total Paid</span>
            <span class="text-h6 font-weight-black text-teal-darken-2">
              ${{ totalAmount }}
            </span>
          </div>
        </div>

        <div class="receipt-zigzag"></div>
      </v-card>

      <div class="text-center px-2">
        <v-btn
          block
          color="#3b828e"
          size="x-large"
          class="rounded-pill font-weight-bold mb-4 text-none elevation-2"
          @click="$emit('reset')"
        >
          <v-icon start>mdi-plus</v-icon>
          Order more
        </v-btn>

        <v-btn
          variant="text"
          color="grey-darken-2"
          class="text-none text-body-2"
          prepend-icon="mdi-account-voice"
        >
          Need anything?
          <u>Call our friendly staff</u>
        </v-btn>
      </div>
    </div>
  </v-container>
</template>

<style scoped>
  .text-teal-darken-2 {
    color: #3b828e !important;
  }

  /* Smooth pulse for success */
  .pulse-animation {
    animation: pulse 2.5s infinite;
  }

  @keyframes pulse {
    0% {
      transform: scale(1);
      box-shadow: 0 0 0 0 rgba(76, 175, 80, 0.4);
    }
    70% {
      transform: scale(1.05);
      box-shadow: 0 0 0 15px rgba(76, 175, 80, 0);
    }
    100% {
      transform: scale(1);
      box-shadow: 0 0 0 0 rgba(76, 175, 80, 0);
    }
  }

  /* Chef icon "cooking" animation */
  .chef-icon-animation {
    animation: flicker 1.5s infinite;
  }

  @keyframes flicker {
    0%,
    100% {
      opacity: 1;
      transform: translateY(0);
    }
    50% {
      opacity: 0.7;
      transform: translateY(-2px);
    }
  }

  .border-dashed {
    border-style: dashed !important;
    border-top-width: 2px !important;
  }

  /* Makes the card look like a receipt */
  .receipt-card {
    position: relative;
    filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.05));
  }

  .receipt-zigzag {
    height: 10px;
    background: radial-gradient(
        circle,
        transparent,
        transparent 50%,
        #fff 50%,
        #fff 100%
      )
      0 -5px / 10px 10px repeat-x;
    transform: rotate(180deg);
  }
</style>
