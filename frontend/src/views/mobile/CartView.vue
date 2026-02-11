<script setup>
  import { useCartStore } from '@/stores/cartStore'
  const cartStore = useCartStore()

  const confirmClearCart = () => {
    cartStore.clearCart()
  }
</script>
<template>
  <div class="cart-wrapper bg-slate-50">
    <v-sheet class="header-bar px-4 py-3" elevation="0">
      <div class="d-flex align-center justify-space-between w-100">
        <v-btn
          icon="mdi-arrow-left"
          variant="tonal"
          size="small"
          class="rounded-xl"
          @click="$router.back()"
        ></v-btn>

        <div class="text-center">
          <h2 class="text-subtitle-1 font-weight-bold mb-0">Shopping Cart</h2>
        </div>

        <v-btn
          v-if="cartStore.cart.length"
          icon="mdi-trash-can-outline"
          variant="text"
          color="red-darken-2"
          size="small"
          @click="confirmClearCart"
        ></v-btn>
        <div v-else style="width: 40px"></div>
      </div>
    </v-sheet>

    <div class="scroll-area px-4 pt-4">
      <div class="mx-auto">
        <div
          v-if="!cartStore.cart.length"
          class="empty-state-container d-flex flex-column align-center justify-center"
        >
          <div class="empty-icon-wrapper mb-6">
            <v-icon size="100" color="teal-lighten-4">mdi-cart-off</v-icon>
          </div>
          <h3 class="text-h6 font-weight-bold text-grey-darken-3">
            Your cart is empty
          </h3>
          <p class="text-body-2 text-grey-darken-1 text-center px-10 mt-2">
            Looks like you haven't added anything to your cart yet.
          </p>
          <v-btn
            class="rounded-pill px-10 text-none mt-8"
            color="teal-darken-2"
            size="large"
            elevation="4"
            @click="$router.push('/online-store')"
          >
            Start Discovering
          </v-btn>
        </div>

        <v-slide-y-transition group>
          <v-card
            v-for="item in cartStore.cart"
            :key="item.id"
            flat
            class="rounded-xl mb-4 item-card border-light"
          >
            <div class="d-flex pa-3">
              <v-avatar size="90" class="rounded-lg bg-grey-lighten-4">
                <v-img :src="item.image" cover />
              </v-avatar>

              <div class="ml-4 flex-grow-1 d-flex flex-column">
                <div class="d-flex justify-space-between align-start">
                  <div>
                    <h4 class="text-body-1 font-weight-bold text-black mb-1">
                      {{ item.name }}
                    </h4>
                    <div
                      class="text-caption text-teal-darken-2 font-weight-bold"
                    >
                      ${{ item.price.toLocaleString() }} / unit
                    </div>
                  </div>
                  <v-btn
                    icon="mdi-close"
                    variant="text"
                    size="x-small"
                    color="red-darken-2"
                    @click="cartStore.removeItem(item.id)"
                  ></v-btn>
                </div>

                <div class="mt-auto d-flex align-center justify-space-between">
                  <div class="text-subtitle-2 font-weight-black">
                    ${{ (item.price * item.qty).toLocaleString() }}
                  </div>

                  <div
                    class="quantity-stepper d-flex align-center border rounded-pill bg-grey-lighten-5"
                  >
                    <v-btn
                      icon="mdi-minus"
                      size="x-small"
                      variant="text"
                      :disabled="item.qty <= 1"
                      @click="cartStore.decreaseQty(item.id)"
                    />
                    <span class="mx-3 text-caption font-weight-bold">
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
      <div class="mx-auto">
        <div class="d-flex justify-space-between align-center mb-4">
          <div>
            <span class="text-caption text-grey-darken-1 d-block">
              Total Payment
            </span>
            <span class="text-h5 font-weight-black text-black">
              ${{ cartStore.cartTotal.toLocaleString() }}
            </span>
          </div>
          <div class="text-right">
            <v-chip size="small" class="font-weight-bold">
              {{ cartStore.cart.length }} Items
            </v-chip>
          </div>
        </div>

        <v-btn
          block
          color="primary"
          size="large"
          class="rounded-pill text-none checkout-btn"
          elevation="8"
          @click="$router.push('/mobile-checkout')"
          >
          <span>Complete Order</span>
          <v-icon size="20" class="ml-2 animate-arrow">mdi-arrow-right</v-icon>
        </v-btn>
      </div>
    </v-sheet>
  </div>
</template>

<style scoped>
  .bg-slate-50 {
    background-color: #f8fafc;
  }

  .header-bar {
    position: sticky;
    top: 0;
    z-index: 100;
    background: rgba(255, 255, 255, 0.9) !important;
  }

  .cart-wrapper {
    height: 100vh;
    height: 100dvh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }

  .scroll-area {
    flex-grow: 1;
    overflow-y: auto;
  }

  .border-light {
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
  }

  .item-card {
    transition: all 0.2s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03) !important;
  }

  .item-card:active {
    transform: scale(0.98);
  }

  .sticky-footer {
    background: white !important;
    border-top: 1px solid rgba(0, 0, 0, 0.05);
    border-radius: 32px 32px 0 0;
  }

  .quantity-stepper {
    height: 36px;
    border: 1px solid #eee !important;
  }

  .empty-icon-wrapper {
    background: white;
    padding: 30px;
    border-radius: 50%;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
  }

  .animate-arrow {
    transition: transform 0.2s ease;
  }

  .checkout-btn:hover .animate-arrow {
    transform: translateX(4px);
  }

  .empty-state-container {
    height: 60vh;
  }
</style>
