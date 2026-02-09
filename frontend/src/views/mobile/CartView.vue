<script setup>
  import { useCartStore } from '@/stores/cartStore'
  const cartStore = useCartStore()

  const sendWhatsAppInquiry = () => {
    const items = cartStore.cart
      .map(i => `${i.qty}x ${i.name} ($${(i.price * i.qty).toLocaleString()})`)
      .join('\n')
    const message = `👋 Hello! I'd like to order:\n\n${items}\n\n✨ Total: $${cartStore.cartTotal.toLocaleString()}\n\nPlease let me know the next steps!`
    const phone = '123456789' // Replace with real number
    window.open(
      `https://wa.me/${phone}?text=${encodeURIComponent(message)}`,
      '_blank'
    )
  }
</script>
<template>
  <div class="cart-wrapper bg-grey-lighten-5">
    <v-sheet class="header-bar border-b px-4 py-3" elevation="0">
      <div class="d-flex align-center justify-space-between w-100">
        <v-btn
          icon="mdi-chevron-left"
          variant="outlined"
          size="x-small"
          class="rounded-lg back-btn"
          @click="$router.back()"
        ></v-btn>

        <div class="text-center">
          <h2 class="text-body-1 font-weight-black mb-0">My Cart</h2>
          <div class="text-caption text-grey-darken-1 mt-n1">
            {{ cartStore.cart.length }} items selected
          </div>
        </div>

        <v-btn
          v-if="cartStore.cart.length"
          icon="mdi-delete-outline"
          variant="text"
          color="red-darken-1"
          size="small"
          @click="cartStore.clearCart()"
        ></v-btn>
        <div v-else style="width: 40px"></div>
      </div>
    </v-sheet>

    <div class="scroll-area px-4 pt-4">
      <div class="mx-auto" style="max-width: 600px">
        <div
          v-if="!cartStore.cart.length"
          class="empty-state-container d-flex flex-column align-center justify-center"
        >
          <v-icon size="80" color="grey-lighten-3">mdi-basket-outline</v-icon>
          <div class="text-h6 mt-4 font-weight-bold text-grey-lighten-1">
            Basket is empty
          </div>
          <v-btn
            class="rounded-pill px-10 text-none mt-6"
            color="black"
            flat
            @click="$router.push('/mobile-product')"
          >
            Go Shopping
          </v-btn>
        </div>

        <v-slide-y-transition group>
          <v-card
            v-for="item in cartStore.cart"
            :key="item.id"
            flat
            class="rounded-xl mb-3 item-card border shadow-sm"
          >
            <div class="d-flex pa-3">
              <v-avarta>
                <v-img
                  :src="item.image"
                  width="100"
                  height="100"
                  cover
                  class="rounded-lg bg-grey-lighten-4"
                />
              </v-avarta>

              <div
                class="ml-4 flex-grow-1 d-flex flex-column justify-space-between"
              >
                <div>
                  <div class="d-flex justify-space-between align-start">
                    <span class="text-body-2 font-weight-bold text-black">
                      {{ item.name }}
                    </span>
                    <span class="text-body-2 font-weight-black text-success">
                      ${{ item.price.toLocaleString() }}
                    </span>
                  </div>
                  <v-chip
                    size="x-small"
                    label
                    color="grey-lighten-4"
                    class="text-grey-darken-2 mt-1"
                  >
                    ${{ item.price }}
                  </v-chip>
                </div>

                <div class="d-flex align-center justify-end mt-2">
                  <div
                    class="quantity-stepper d-flex align-center border rounded-pill px-1 bg-white"
                  >
                    <v-btn
                      icon="mdi-minus"
                      size="x-small"
                      variant="text"
                      @click="cartStore.decreaseQty(item.id)"
                    />
                    <span class="mx-2 text-caption font-weight-black">
                      {{ item.qty }}
                    </span>
                    <v-btn
                      icon="mdi-plus"
                      size="x-small"
                      variant="text"
                      color="teal-darken-2"
                      @click="cartStore.increaseQty(item.id)"
                    />
                  </div>
                </div>
              </div>
            </div>
          </v-card>
        </v-slide-y-transition>
      </div>
    </div>

    <v-sheet
      v-if="cartStore.cart.length"
      class="sticky-footer px-6 pt-5 pb-8 shadow-top"
    >
      <div class="mx-auto" style="max-width: 600px">
        <div class="d-flex justify-space-between align-center mb-1">
          <span class="text-caption text-grey-darken-1">Total Amount</span>
          <span class="text-caption text-grey-darken-1">
            {{ cartStore.cart.length }} Items
          </span>
        </div>
        <div class="d-flex justify-space-between align-center mb-6">
          <span class="text-h5 font-weight-black text-teal-darken-3">
            ${{ cartStore.cartTotal.toLocaleString() }}
          </span>
        </div>

        <v-btn
          block
          color="teal-darken-2"
          size="x-large"
          height="55"
          class="rounded-pill text-none font-weight-bold order-btn"
          @click="sendWhatsAppInquiry"
        >
          <div class="d-flex align-center justify-center w-100">
            <span class="text-uppercase letter-spacing-1">Place Order</span>
            <v-icon size="20" class="ml-2">mdi-chevron-right</v-icon>
          </div>
        </v-btn>
      </div>
    </v-sheet>
  </div>
</template>

<style scoped>
  .header-bar {
    position: sticky;
    top: 0;
    z-index: 100;
    background: white !important;
  }

  .back-btn {
    border-color: #e0e0e0 !important;
    color: #424242 !important;
  }

  .cart-wrapper {
    height: 100vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }

  .scroll-area {
    flex-grow: 1;
    overflow-y: auto;
    scrollbar-width: none;
  }
  .scroll-area::-webkit-scrollbar {
    display: none;
  }

  .item-card {
    background: white !important;
    transition: transform 0.1s ease;
  }

  .sticky-footer {
    background: white !important;
    border-radius: 24px 24px 0 0;
    box-shadow: 0 -8px 30px rgba(0, 0, 0, 0.05) !important;
  }

  .quantity-stepper {
    height: 32px;
  }

  .letter-spacing-1 {
    letter-spacing: 1.5px !important;
  }

  .empty-state-container {
    height: 60vh;
  }
</style>
