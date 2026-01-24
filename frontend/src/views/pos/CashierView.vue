<template>
  <v-container fluid>
    <custom-title icon="mid-paind">Unpaid Orders List</custom-title>
    <v-row dense>
      <v-col
        v-for="bill in orderStore.orders"
        :key="bill.id"
        cols="12"
        sm="4"
        md="4"
        lg="3"
        xl="1"
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
<!-- {{ bill }} -->
          <v-card-text class="pa-4">
            <div class="d-flex justify-space-between align-start mb-2">
              <div>
                <div
                  class="text-overline font-weight-black text-grey-darken-1 line-height-1"
                >
                  ORDER #{{ bill.order_id }}
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
              <span>{{ bill.item_count || 0 }} items</span>
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
  import { onMounted, ref } from 'vue'
  import { useOrderStore } from '../../stores/orderStore'

  const orderStore = useOrderStore()

  onMounted(() => {
    orderStore.fetchAllOrders()
  })
  const unpaidBills = ref([
    {
      id: 101,
      order_no: 'ORD-101',
      table_no: 3,
      customer_name: null, // dine-in
      total: 18.75,
      currency: 'USD',
      items: [
        { name: 'Latte', qty: 2, price: 3.5 },
        { name: 'Beef Burger', qty: 1, price: 8.75 }
      ]
    },
    {
      id: 102,
      order_no: 'ORD-102',
      table_no: 5,
      customer_name: null,
      total: 24.5,
      currency: 'USD',
      items: [
        { name: 'Americano', qty: 1, price: 2.5 },
        { name: 'Fried Rice', qty: 2, price: 11 }
      ]
    },
    {
      id: 103,
      order_no: 'ORD-103',
      table_no: null,
      customer_name: 'Takeaway',
      total: 9.25,
      currency: 'USD',
      items: [
        { name: 'Cappuccino', qty: 1, price: 3.75 },
        { name: 'Croissant', qty: 1, price: 5.5 }
      ]
    },
    {
      id: 103,
      order_no: 'ORD-103',
      table_no: null,
      customer_name: 'Takeaway',
      total: 9.25,
      currency: 'USD',
      items: [
        { name: 'Cappuccino', qty: 1, price: 3.75 },
        { name: 'Croissant', qty: 1, price: 5.5 }
      ]
    }
  ])

  const selectedBill = ref(null)
  const paymentMethod = ref('Cash')

  const selectBill = bill => {
    selectedBill.value = bill
  }

  const pay = () => {
    alert(
      `Paid Order #${selectedBill.value.order_no} via ${paymentMethod.value}`
    )
    // later:
    // 1. call API
    // 2. print invoice
    // 3. remove from unpaidBills
  }
</script>
