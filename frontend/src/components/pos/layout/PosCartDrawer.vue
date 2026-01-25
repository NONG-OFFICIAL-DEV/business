<script setup>
  import { computed } from 'vue'
  import { usePosStore } from '@/stores/posStore'

  /* Sub components */
  import CartHeader from './cardDrawer/CartHeader.vue'
  import HospitalityCartItems from './cardDrawer/HospitalityCartItems.vue'
  import RetailCartTable from './cardDrawer/RetailCartTable.vue'
  import CartFooter from './cardDrawer/CartFooter.vue'
  const emit = defineEmits(['checkout', 'print-bill'])

  /* Store */
  const posStore = usePosStore()

  /* Computed */
  const isHospitality = computed(
    () => posStore.selectedStore?.type === 'hospitality'
  )

  const cartItems = computed(() => posStore.cart)
  const selectedBillPay = computed(() => posStore.selectedBill)
  const table = computed(() => posStore.selectedTable)

  /* Actions */
  const updateQty = (itemId, qty) => {
    posStore.updateQty(itemId, qty)
  }

  const removeItem = itemId => {
    posStore.removeFromCart(itemId)
  }

  const clearCart = () => {
    posStore.clearCart()
  }

  const selectPayment = method => {
    posStore.setPaymentMethod(method)
  }

  const checkout = () => {
    emit('checkout', {
      cart: posStore.cart,
      payment: posStore.paymentMethod
    })
  }
  const handlePrintBill = () => {
    emit('print-bill')
  }
</script>
<template>
  <v-navigation-drawer
    location="end"
    permanent
    elevation="0"
    :width="isHospitality ? 380 : 500"
    class="border-l-sm"
  >
    <div class="d-flex flex-column fill-height">
      <!-- HEADER -->
      <!-- {{ selectedBillPay }} -->
      <CartHeader
        :is-hospitality="isHospitality"
        :table="table"
        :count="cartItems.length"
        @clear="clearCart"
      />

      <!-- CONTENT -->
      <div class="flex-grow-1 overflow-y-auto pa-3">
        <HospitalityCartItems
          v-if="isHospitality"
          :items="cartItems"
          @update-qty="updateQty"
        />
        <!-- :billPayment="selectedBillPay" -->

        <RetailCartTable
          v-else
          :items="cartItems"
          @update-qty="updateQty"
          @remove="removeItem"
        />
      </div>

      <!-- FOOTER -->
      <CartFooter
        :subtotal="posStore.subtotal"
        :total="posStore.total"
        :payment-method="posStore.paymentMethod"
        :payment-methods="posStore.paymentMethods"
        :disabled="!cartItems.length"
        @select-payment="selectPayment"
        @checkout="checkout"
        @print-bill="handlePrintBill"
      />
    </div>
  </v-navigation-drawer>
</template>
