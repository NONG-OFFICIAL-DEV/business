<template>
  <v-container fluid class="pa-0 fill-height">
    <v-row no-gutters class="fill-height">
      <v-col cols="12" md="12" class="pa-6 bg-grey-lighten-4">
        <h2 class="text-h4 font-weight-black mb-6">Unpaid Orders</h2>
        <v-row>
          <v-col v-for="bill in unpaidBills" :key="bill.id" cols="12" sm="6" md="4">
            <v-card @click="selectBill(bill)" class="rounded-xl border-lg" elevation="0">
              <v-card-text>
                <div class="d-flex justify-space-between mb-2">
                  <span class="text-overline">Order #{{ bill.id }}</span>
                  <v-chip size="x-small" color="primary">READY TO PAY</v-chip>
                </div>
                <div class="text-h5 font-weight-black">{{ bill.customer_name || 'Table ' + bill.table_no }}</div>
                <div class="text-h4 text-primary font-weight-black mt-2">${{ bill.total }}</div>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
  </v-container>
</template>
<script setup>
import { ref } from 'vue'

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