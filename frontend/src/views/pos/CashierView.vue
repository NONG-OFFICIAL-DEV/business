<template>
  <v-container fluid class="pa-0">
    <custom-title icon="mdi-cash-clock">
      Unpaid Orders List
      <template #right>
        <!-- Live indicator -->
        <div class="live-indicator">
          <v-icon size="10" :color="connected ? 'green' : 'red'">
            mdi-circle
          </v-icon>
          <span class="text-caption text-grey ms-1">
            {{ connected ? 'Live' : 'Reconnecting...' }}
          </span>
        </div>
      </template>
    </custom-title>

    <!-- Loading skeleton -->
    <v-row v-if="orderStore.loading" dense>
      <v-col v-for="n in 4" :key="n" cols="12" sm="4" md="4" lg="3">
        <v-skeleton-loader type="card" rounded="xl" />
      </v-col>
    </v-row>

    <!-- Empty state -->
    <div
      v-else-if="orderStore.orders.length === 0"
      class="d-flex flex-column align-center justify-center pa-12 text-grey"
    >
      <v-icon size="64" class="mb-4">mdi-receipt-text-outline</v-icon>
      <div class="text-h6">No active orders</div>
      <div class="text-caption">New orders will appear here automatically</div>
    </div>

    <!-- Orders grid -->
    <v-row v-else dense>
      <v-col
        v-for="bill in orderStore.orders"
        :key="bill.order_id"
        cols="12"
        sm="4"
        md="4"
        lg="3"
      >
        <v-card
          @click="selectBill(bill)"
          :class="[
            'order-card rounded-xl transition-swing',
            selectedBill?.order_id === bill.order_id
              ? 'selected-bill'
              : 'bg-white',
            newOrderIds.has(bill.order_id) ? 'new-order-flash' : ''
          ]"
          elevation="0"
          border
        >
          <div
            :class="['type-stripe', bill.table ? 'bg-primary' : 'bg-orange']"
          ></div>

          <!-- NEW badge -->
          <v-chip
            v-if="newOrderIds.has(bill.order_id)"
            class="new-badge"
            color="green"
            size="x-small"
            label
          >
            NEW
          </v-chip>

          <v-card-text class="pa-4">
            <div class="d-flex justify-space-between align-start mb-2">
              <div>
                <div
                  class="text-overline font-weight-black text-grey-darken-1 line-height-1"
                >
                  ORDER #{{ bill.order_id }}
                </div>
                <div class="d-flex align-center text-caption text-grey">
                  <v-icon size="14" class="me-1">mdi-clock-outline</v-icon>
                  {{ formatTimeAgo(bill.created_at) }}
                </div>
                <div class="text-h5 font-weight-black mt-1">
                  {{ bill.table ? 'Table ' + bill.table : 'Takeaway' }}
                </div>
              </div>
              <v-icon
                :icon="bill.table ? 'mdi-table-chair' : 'mdi-moped'"
                color="grey-lighten-1"
              />
            </div>

            <div
              class="d-flex align-center text-caption text-grey-darken-1 mb-4"
            >
              <v-icon size="14" class="me-1">mdi-package-variant</v-icon>
              <span>{{ bill.item_count || 0 }} Qty</span>
            </div>

            <v-divider class="mb-3 border-dashed" />

            <div class="d-flex justify-space-between align-center">
              <div>
                <div class="text-caption text-grey">Total Amount</div>
                <div class="text-h5 font-weight-black text-primary">
                  ${{ Number(bill.total).toFixed(2) }}
                </div>
              </div>
              <v-btn
                icon="mdi-chevron-right"
                variant="tonal"
                color="primary"
                size="small"
              />
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
  import { onMounted, onUnmounted, ref } from 'vue'
  import { useOrderStore } from '@/stores/orderStore'
  import { usePosStore } from '@/stores/posStore'
  import { useDate } from '@/composables/useDate'
  import echo from '@/utils/echo'

  const { formatTimeAgo } = useDate()
  const orderStore = useOrderStore()
  const posStore = usePosStore()
  const selectedBill = ref(null)
  const connected = ref(false)
  const newOrderIds = ref(new Set())

  onMounted(async () => {
    await orderStore.fetchAllOrders()
    orderStore.subscribeToOrders()

    // Track new orders for highlighting
    echo.channel('orders').listen('.order.created', data => {
      newOrderIds.value = new Set([...newOrderIds.value, data.order_id])
      // Remove highlight after 6 seconds
      setTimeout(() => {
        newOrderIds.value.delete(data.order_id)
        newOrderIds.value = new Set(newOrderIds.value) // trigger reactivity
      }, 6000)
    })

    // Connection state
    echo.connector.pusher.connection.bind(
      'connected',
      () => (connected.value = true)
    )
    echo.connector.pusher.connection.bind(
      'disconnected',
      () => (connected.value = false)
    )
  })

  onUnmounted(() => {
    orderStore.unsubscribeFromOrders()
  })

  function selectBill(bill) {
    selectedBill.value = bill
    posStore.selectBill(bill)
    posStore.orderId = bill.order_id
  }
</script>

<style scoped>
  .type-stripe {
    height: 4px;
    border-radius: 12px 12px 0 0;
  }

  .order-card {
    position: relative;
    cursor: pointer;
    transition:
      transform 0.15s ease,
      box-shadow 0.15s ease;
  }

  .order-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08) !important;
  }

  .selected-bill {
    border-color: rgb(var(--v-theme-primary)) !important;
    background: rgba(var(--v-theme-primary), 0.04) !important;
  }

  /* Flash animation for new orders */
  .new-order-flash {
    animation: flash-border 1s ease-in-out 3;
    border-color: #22c55e !important;
  }

  @keyframes flash-border {
    0%,
    100% {
      box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.2);
    }
    50% {
      box-shadow: 0 0 0 6px rgba(34, 197, 94, 0.4);
    }
  }

  .new-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 1;
  }

  .live-indicator {
    display: flex;
    align-items: center;
    padding: 4px 0;
  }
</style>
