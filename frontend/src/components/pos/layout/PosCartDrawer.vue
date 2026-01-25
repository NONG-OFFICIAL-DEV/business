<script setup>
  import { computed } from 'vue'
  import { usePosStore } from '@/stores/posStore'

  /* Sub components */
  import CartHeader from './cardDrawer/CartHeader.vue'
  import HospitalityCartItems from './cardDrawer/HospitalityCartItems.vue'
  import RetailCartTable from './cardDrawer/RetailCartTable.vue'
  import CartFooter from './cardDrawer/CartFooter.vue'

  /* Store */
  const posStore = usePosStore()

  /* Computed */
  const isHospitality = computed(
    () => posStore.selectedStore?.type === 'hospitality'
  )

  const cartItems = computed(() => posStore.cart)
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
    // later: validate / send to backend
    console.log('Checkout', {
      cart: posStore.cart,
      payment: posStore.paymentMethod
    })
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
      />
    </div>
  </v-navigation-drawer>
</template>

