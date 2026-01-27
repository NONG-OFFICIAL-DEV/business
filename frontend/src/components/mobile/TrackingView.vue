<script setup>
  import { ref, computed, onMounted } from 'vue'
  import { useOrderStore } from '@/stores/orderStore'

  const props = defineProps({
    tableNumber: String,
    tableId: Number
  })

  defineEmits(['reset'])

  const order = ref(null)
  const loading = ref(true)

  const orderStore = useOrderStore()

  // Compute total safely
  const totalAmount = computed(() => {
    if (!order.value || !order.value.items) return '0.00'
    return order.value.items
      .reduce((sum, item) => sum + item.price * item.qty, 0)
      .toFixed(2)
  })

  onMounted(async () => {
    try {
      if (!props.tableNumber) return
      order.value = await orderStore.fetchOrderByTable(props.tableId)
    } catch (e) {
      console.error('Failed to load order', e)
    } finally {
      loading.value = false
    }
  })
</script>

<template>
  <v-container
    class="fill-height bg-grey-lighten-5 d-flex align-center justify-center"
  >
    <!-- No active orders -->
    <div v-if="!order || order.items?.length === 0" class="text-center px-6">
      <v-avatar color="grey-lighten-4" size="120" class="mb-6">
        <v-icon size="60" color="grey-lighten-1">mdi-tray-full</v-icon>
      </v-avatar>
      <h3 class="text-h5 font-weight-black mb-2">No active orders</h3>
      <p class="text-body-2 text-medium-emphasis mb-6">
        Looks like you haven't ordered anything yet. Let's find something
        delicious!
      </p>
      <v-btn
        color="#3b828e"
        size="large"
        block
        rounded="pill"
        class="text-none font-weight-bold"
        @click="$emit('reset')"
      >
        View Menu
      </v-btn>
    </div>

    <!-- Active order -->
    <div v-else class="w-100 px-4" style="max-width: 450px">
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
          <b>{{ props.tableNumber }}</b>
          .
        </p>
      </div>

      <v-card flat class="rounded-xl border-sm mb-6 receipt-card">
        <div class="pa-5 bg-white">
          <div class="d-flex justify-space-between mb-6 px-2">
            <!-- Ordered -->
            <div class="d-flex flex-column align-center">
              <v-icon
                :color="
                  order.kitchen_status !== 'pending'
                    ? 'success'
                    : 'grey-lighten-1'
                "
              >
                mdi-check-circle
              </v-icon>
              <span
                class="text-caption font-weight-bold"
                :class="
                  order.kitchen_status !== 'pending'
                    ? ''
                    : 'text-grey-lighten-1'
                "
              >
                Ordered
              </span>
            </div>

            <v-divider
              class="mt-3 mx-2"
              :color="
                order.kitchen_status === 'preparing'
                  ? '#3b828e'
                  : 'grey-lighten-1'
              "
              thickness="2"
            />

            <!-- Cooking -->
            <div class="d-flex flex-column align-center">
              <v-icon
                :color="
                  order.kitchen_status !== 'preparing'
                    ? 'success'
                    : 'grey-lighten-1'
                "
                class="chef-icon-animation"
              >
                mdi-fire
              </v-icon>
              <span
                class="text-caption font-weight-bold"
                :class="
                  order.kitchen_status === 'preparing'
                    ? 'text-teal-darken-2'
                    : 'grey-lighten-1'
                "
              >
                Cooking
              </span>
            </div>

            <v-divider
              class="mt-3 mx-2"
              :color="
                order.kitchen_status === 'ready' ? 'success' : 'grey-lighten-1'
              "
            />

            <!-- Ready -->
            <div class="d-flex flex-column align-center">
              <v-icon
                :color="
                  order.kitchen_status === 'ready'
                    ? 'success'
                    : 'grey-lighten-1'
                "
              >
                mdi-room-service
              </v-icon>
              <span
                class="text-caption font-weight-bold"
                :class="
                  order.kitchen_status === 'ready' ? '' : 'text-grey-lighten-1'
                "
              >
                Ready
              </span>
            </div>
          </div>

          <div class="text-overline font-weight-black text-grey-darken-1 mb-2">
            Receipt Details
          </div>

          <div
            v-for="item in order.items || []"
            :key="item.id"
            class="d-flex justify-space-between mb-3"
          >
            <div class="d-flex flex-column">
              <span class="text-body-2 font-weight-bold">{{ item.name }}</span>
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
          <v-icon start>mdi-arrow-left</v-icon>
          Back to Menu
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

  /* .chef-icon-animation {
    animation: flicker 1.5s infinite;
  } */

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
