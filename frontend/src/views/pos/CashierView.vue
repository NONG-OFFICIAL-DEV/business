<template>
  <v-container fluid class="pa-0">
    <custom-title icon="mdi-cash-clock">Unpaid Orders List</custom-title>
    <v-row dense>
      <v-col
        v-for="bill in orderStore.orders"
        :key="bill.id"
        cols="12"
        sm="4"
        md="4"
        lg="3"
      >
        <v-card
          @click="selectBill(bill)"
          :class="[
            'order-card rounded-xl transition-swing',
            selectedBill?.id === bill.id ? 'selected-bill' : 'bg-white'
          ]"
          elevation="0"
          border
        >
          <div
            :class="['type-stripe', bill.table_no ? 'bg-primary' : 'bg-orange']"
          ></div>
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
                :icon="bill.table_no ? 'mdi-table-chair' : 'mdi-moped'"
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
                  ${{ bill.total }}
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
  import { onMounted, ref, onBeforeUnmount } from 'vue'
  import { useOrderStore } from '../../stores/orderStore'
  import { usePosStore } from '../../stores/posStore'
  import { useDate } from '@/composables/useDate'
  import { connectOrderStream, closeOrderStream } from '@/services/orderStream'

  const { formatTimeAgo } = useDate()
  const orderStore = useOrderStore()
  const posStore = usePosStore()
  let intervalId = null
  // onMounted(() => {
  //   orderStore.fetchAllOrders()
  // })

  const fetchOrders = async () => {
    await orderStore.fetchAllOrders()
  }

  onMounted(() => {
    fetchOrders()
    intervalId = setInterval(fetchOrders, 3000)
  })

  onBeforeUnmount(() => {
    clearInterval(intervalId)
  })

  function selectBill(bill) {
    // console.log(bill.order_id);

    posStore.selectBill(bill)
    posStore.orderId = bill.order_id
  }

  const selectedBill = ref(null)
</script>
