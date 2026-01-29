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
  <div>
    <div class="sticky-header shadow-sm px-2">
      <v-row no-gutters align="center">
        <v-col cols="2">
          <v-btn
            icon="mdi-chevron-left"
            variant="text"
            density="comfortable"
            @click="$emit('back')"
          />
        </v-col>

        <v-col cols="8" class="text-center">
          <div class="text-subtitle-1 font-weight-black">My Basket</div>
          <div v-if="cart.length > 0" class="text-caption text-grey mt-n1">
            {{ cart.length }} items selected
          </div>
        </v-col>

        <v-col cols="2" class="d-flex justify-end">
          <v-btn
            v-if="cart.length > 0"
            icon="mdi-delete-outline"
            variant="text"
            color="error"
            density="comfortable"
            @click="$emit('clear')"
          />
        </v-col>
      </v-row>
    </div>
    <v-container class="pb-15">
      <!-- <v-row no-gutters align="center" class="mb-4">
        <v-btn
          icon="mdi-chevron-left"
          variant="text"
          density="comfortable"
          @click="$emit('back')"
        />
        <v-spacer />
        <div class="text-center">
          <div class="text-subtitle-2 font-weight-bold">My Basket</div>
        </div>
        <v-spacer />
        <v-btn
          v-if="cart.length > 0"
          variant="text"
          color="error"
          size="small"
          class="text-caption"
          rounded="pill"
          @click="$emit('clear')"
        >
          Clear
        </v-btn>
      </v-row> -->

      <v-fade-transition hide-on-leave>
        <div v-if="cart.length === 0" class="text-center py-12">
          <v-icon size="48" color="grey-lighten-1">mdi-basket-outline</v-icon>
          <p class="text-body-2 text-medium-emphasis mt-2">
            Your basket is empty
          </p>
        </div>
      </v-fade-transition>

      <div v-if="cart.length > 0">
        <v-card
          v-for="item in cart"
          :key="item.id"
          flat
          class="mb-4 overflow-hidden rounded-xl border-sm"
        >
          <div class="d-flex pa-3">
            <v-img
              :src="item.image_url"
              width="80"
              height="80"
              cover
              class="rounded-lg flex-grow-0"
            />

            <div class="ml-4 d-flex flex-column justify-center flex-grow-1">
              <div class="d-flex justify-space-between align-start">
                <span class="font-weight-bold text-subtitle-1">
                  {{ item.name }}
                </span>
                <span class="font-weight-black">
                  ${{ (item.price * item.qty).toFixed(2) }}
                </span>
              </div>

              <div class="d-flex justify-space-between align-center mt-2">
                <span class="text-caption text-primary font-weight-bold">
                  ${{ item.price }}
                </span>

                <div
                  class="d-flex align-center border rounded-pill px-1 bg-grey-lighten-5"
                >
                  <v-btn
                    icon="mdi-minus"
                    size="32"
                    variant="text"
                    :color="item.qty <= 1 ? 'error' : 'default'"
                    @click="$emit('update', item.id, -1)"
                  />
                  <span class="px-3 font-weight-bold">{{ item.qty }}</span>
                  <v-btn
                    icon="mdi-plus"
                    size="32"
                    variant="text"
                    color="primary"
                    @click="$emit('update', item.id, 1)"
                  />
                </div>
              </div>
            </div>
          </div>
        </v-card>
      </div>

      <v-footer
        v-if="cart.length > 0"
        app
        fixed
        elevation="24"
        class="pa-4 flex-column rounded-t-xl sticky-footer"
      >
        <div class="w-100 container-max-width">
          <div class="d-flex justify-space-between align-center mb-4 px-2">
            <div>
              <span class="text-caption text-medium-emphasis d-block">
                Total Amount
              </span>
              <span class="font-weight-black text-h5 text-primary">
                ${{ total.toFixed(2) }}
              </span>
            </div>
            <div class="text-right">
              <span class="text-caption text-medium-emphasis d-block">
               {{totalItems}} Items
              </span>
              <span class="text-subtitle-2">Table {{ tableNumber }}</span>
            </div>
          </div>

          <v-btn
            block
            color="primary"
            height="60"
            rounded="pill"
            class="font-weight-bold text-subtitle-1 elevation-8 checkout-btn"
            :loading="loading"
            @click="$emit('submit')"
          >
            PLACE ORDER
            <v-icon end class="ml-2">mdi-chevron-right</v-icon>
          </v-btn>
        </div>
      </v-footer>
    </v-container>
  </div>
</template>

<style scoped>
  .sticky-footer {
    /* This ensures it sits on top of everything at the bottom */
    position: fixed !important;
    bottom: 0;
    left: 0;
    right: 0;
    background: white !important;
    z-index: 1000;
    /* Adds space for iPhone 'home bar' notch */
    padding-bottom: calc(env(safe-area-inset-bottom) + 16px) !important;
    border-top: 1px solid rgba(0, 0, 0, 0.08);
  }

  .checkout-btn {
    /* Adds a slight 'pop' to the primary action */
    transition: transform 0.2s;
  }
  .checkout-btn:active {
    transform: scale(0.96);
  }

  .pb-32 {
    /* Match this to the height of your footer so the last item can be scrolled into view */
    padding-bottom: 180px !important;
  }
  .sticky-header {
    position: sticky;
    top: 0;
    z-index: 100;
    background: rgba(255, 255, 255, 0.95); /* Semi-transparent white */
    backdrop-filter: blur(10px); /* Modern frosted glass effect */
    padding-top: env(safe-area-inset-top); /* Handles phone status bars */
    padding-bottom: 8px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
  }

  .content-wrapper {
    /* This prevents the first item from hiding under the sticky header */
    margin-top: 8px;
  }

  /* Optional: Smooth shadow that appears as you scroll */
  .shadow-sm {
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05) !important;
  }
</style>
