<script setup>
  import { computed } from 'vue'
  const prop = defineProps({
    cart: {
      type: Array,
      default: () => []
    },
    total: {
      type: Number,
      default: 0
    },
    tableNumber: String,
    loading: Boolean
  })

  defineEmits(['back', 'update', 'submit', 'clear'])
  // Inside your useCart.js or parent script
  const totalItems = computed(() => {
    return prop.cart.reduce((sum, item) => sum + item.qty, 0)
  })
</script>

<template>
  <div class="cart-page-wrapper">
    
    <header class="sticky-header px-2">
      <v-row no-gutters align="center" class="py-2">
        <v-col cols="2">
          <v-btn
            icon="mdi-chevron-left"
            variant="text"
            @click="$emit('back')"
          />
        </v-col>
        <v-col cols="8" class="text-center">
          <div class="text-subtitle-1 font-weight-black">My Basket</div>
          <div v-if="cart.length > 0" class="text-caption text-grey mt-n1">
            {{ cart.length }} items selected
          </div>
          <div v-else class="text-caption text-grey mt-n1">
            0 items selected
          </div>
        </v-col>
        <v-col cols="2" class="d-flex justify-end">
          <v-btn
            v-if="cart.length > 0"
            icon="mdi-delete-outline"
            variant="text"
            color="error"
            @click="$emit('clear')"
          />
        </v-col>
      </v-row>
    </header>

    <main class="scroll-content pa-4">
      <v-fade-transition hide-on-leave>
        <div v-if="cart.length === 0" class="text-center py-12">
          <v-avatar color="grey-lighten-4" size="80" class="mb-4">
            <v-icon size="40" color="grey-lighten-1">mdi-basket-outline</v-icon>
          </v-avatar>
          <p class="text-body-2 text-medium-emphasis">Your basket is empty</p>
          <v-btn variant="text" color="primary" class="mt-2" @click="$emit('back')">
            Go Shopping
          </v-btn>
        </div>
      </v-fade-transition>

      <div v-if="cart.length > 0">
        <v-card
          v-for="item in cart"
          :key="item.id"
          flat
          class="mb-4 rounded-xl border-sm overflow-hidden"
        >
          <div class="d-flex pa-3">
            <v-img
              :src="item.image_url"
              width="85"
              height="85"
              cover
              class="rounded-lg flex-grow-0"
            />
            <div class="ml-4 d-flex flex-column justify-center flex-grow-1">
              <div class="d-flex justify-space-between align-start">
                <span class="font-weight-bold text-subtitle-1">{{ item.name }}</span>
                <span class="font-weight-black text-teal-darken-3">
                  ${{ (item.price * item.qty).toFixed(2) }}
                </span>
              </div>
              <div class="d-flex justify-space-between align-center mt-2">
                <span class="text-caption text-grey-darken-1 font-weight-bold">
                  ${{ item.price }}
                </span>
                <div class="d-flex align-center border rounded-pill px-1 bg-grey-lighten-5">
                  <v-btn
                    icon="mdi-minus"
                    size="32"
                    variant="text"
                    density="comfortable"
                    @click="$emit('update', item.id, -1)"
                  />
                  <span class="px-2 font-weight-bold">{{ item.qty }}</span>
                  <v-btn
                    icon="mdi-plus"
                    size="32"
                    variant="text"
                    color="primary"
                    density="comfortable"
                    @click="$emit('update', item.id, 1)"
                  />
                </div>
              </div>
            </div>
          </div>
        </v-card>
      </div>
    </main>

    <footer v-if="cart.length > 0" class="fixed-footer pa-4 rounded-t-xl shadow-top">
      <div class="d-flex justify-space-between align-center mb-4 px-2">
        <div>
          <span class="text-caption text-medium-emphasis d-block">Total Amount</span>
          <span class="font-weight-black text-h5 text-primary">
            ${{ total.toFixed(2) }}
          </span>
        </div>
        <div class="text-right">
          <span class="text-caption text-medium-emphasis d-block">
            {{ totalItems }} Items
          </span>
          <span class="text-subtitle-2">Table {{ tableNumber }}</span>
        </div>
      </div>
      <v-btn
        block
        color="primary"
        size="large"
        rounded="pill"
        class="text-subtitle-1 elevation-4 checkout-btn"
        :loading="loading"
        @click="$emit('submit')"
      >
        PLACE ORDER
        <v-icon end class="ml-2">mdi-chevron-right</v-icon>
      </v-btn>
    </footer>
  </div>
</template>

<style scoped>
  /* 1. Reset the wrapper to be a non-scrolling flex container */
  .cart-page-wrapper {
    display: flex;
    flex-direction: column;
    height: 100vh;
    height: 100dvh; /* Dynamic viewport height for mobile browsers */
    overflow: hidden;
    background-color: #f8f9fa;
  }

  /* 2. Header stays at top, supports iOS notch */
  .sticky-header {
    flex-shrink: 0;
    background: white;
    z-index: 100;
    padding-top: env(safe-area-inset-top);
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
  }

  /* 3. This area grows to fill space and scrolls internally */
  .scroll-content {
    flex-grow: 1;
    overflow-y: auto;
    /* Extra padding at bottom so items aren't hidden by the floating footer */
    padding-bottom: 200px !important;
    -webkit-overflow-scrolling: touch; /* Smooth scroll for iOS */
  }

  /* 4. Footer is fixed to the viewport bottom */
  .fixed-footer {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: white;
    z-index: 1000;
    /* Bottom padding accounts for iPhone home bar */
    padding-bottom: calc(env(safe-area-inset-bottom) + 16px) !important;
    border-top: 1px solid rgba(0, 0, 0, 0.08);
  }

  .shadow-top {
    box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.08) !important;
  }

  .checkout-btn {
    transition: transform 0.1s;
  }
  .checkout-btn:active {
    transform: scale(0.97);
  }

  /* Custom styling for cart cards */
  .border-sm {
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
  }
</style>