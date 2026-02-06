<script setup>
  import { computed } from 'vue'
  import { useCurrency } from '@/composables/useCurrency.js'
  import CustomAppHeader from './CustomAppHeader.vue'

  import QtyStepper from '../customs/QtyStepper.vue'
  const { formatCurrency } = useCurrency()

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

  const totalItems = computed(() => {
    return prop.cart.reduce((sum, item) => sum + item.qty, 0)
  })
</script>

<template>
  <div class="cart-page-wrapper">
    <CustomAppHeader
      title="My Basket"
      :subtitle="`${totalItems} items selected`"
      :showBack="true"
      @back="$emit('back')"
    >
      <template #right>
        <v-btn
          v-if="cart.length > 0"
          icon="mdi-delete-outline"
          variant="text"
          color="error"
          density="comfortable"
          @click="$emit('clear')"
        />
      </template>
    </CustomAppHeader>

    <main class="scroll-content pa-4">
      <v-fade-transition hide-on-leave>
        <div v-if="cart.length === 0" class="text-center py-12">
          <v-avatar color="grey-lighten-4" size="80" class="mb-4">
            <v-icon size="40" color="grey-lighten-1">mdi-basket-outline</v-icon>
          </v-avatar>
          <p class="text-body-2 text-medium-emphasis">Your basket is empty</p>
          <v-btn
            variant="text"
            color="primary"
            class="mt-2"
            @click="$emit('back')"
          >
            Go Shopping
          </v-btn>
        </div>
      </v-fade-transition>

      <div v-if="cart.length > 0">
        <v-card
          v-for="item in cart"
          :key="item.id"
          flat
          class="cart-card mb-4 border rounded-xl"
        >
          <div class="d-flex pa-3">
            <v-img
              :src="item.image_url"
              width="90"
              height="90"
              cover
              class="rounded-lg flex-grow-0 shadow-sm border"
            />

            <div class="ml-4 flex-grow-1 d-flex flex-column">
              <div class="d-flex justify-space-between align-start mb-1">
                <div class="pr-2">
                  <div class="font-weight-black text-body-1 line-height-tight">
                    {{ item.name }}
                  </div>
                  <div
                    class="text-caption text-grey-darken-1 mt-1"
                    v-if="item.has_variants"
                  >
                    {{ item.customizations.variant_name }}
                    <span v-if="item.customizations.sugar">
                      | {{ item.customizations.sugar }}% Sugar
                    </span>
                  </div>
                </div>
                <div
                  class="text-teal-darken-3 font-weight-black text-subtitle-1"
                >
                  {{ formatCurrency(item.price * item.qty) }}
                </div>
              </div>

              <div class="mt-auto d-flex justify-space-between align-center">
                <div
                  class="text-caption font-weight-bold text-grey-darken-1 bg-grey-lighten-4 px-2 py-0.5 rounded"
                >
                  {{ formatCurrency(item.price) }}
                </div>
                <QtyStepper
                  :modelValue="item.qty"
                  small
                  strict
                  :max="5"
                  :min="0"
                  @change="delta => $emit('update', item.id, delta)"
                />
              </div>
            </div>
          </div>
        </v-card>
      </div>
    </main>

    <footer
      v-if="cart.length > 0"
      class="fixed-footer pa-4 rounded-t-xl shadow-top"
    >
      <div class="d-flex justify-space-between align-end mb-4 px-2">
        <div>
          <span class="text-caption text-medium-emphasis d-block">
            Total Amount
          </span>
          <span class="font-weight-black text-h5 text-primary">
            {{ formatCurrency(total) }}
          </span>
        </div>
        <div class="text-right">
          <span class="text-caption text-medium-emphasis d-block">
            {{ totalItems }} Items
          </span>
          <span class="text-subtitle-2 font-weight-black">
            Table {{ tableNumber }}
          </span>
        </div>
      </div>

      <v-btn
        block
        color="primary"
        size="large"
        rounded="pill"
        class="text-subtitle-1 elevation-4 checkout-btn font-weight-bold"
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
  .cart-page-wrapper {
    display: flex;
    flex-direction: column;
    height: 100dvh;
    overflow: hidden;
    background-color: #f8f9fa;
  }

  .scroll-content {
    flex-grow: 1;
    overflow-y: auto;
    padding-bottom: 220px !important;
    -webkit-overflow-scrolling: touch;
  }

  /* Improved Stepper Styling */
  .stepper-box {
    display: flex;
    align-items: center;
    background: #f5f5f5;
    padding: 3px;
    border-radius: 50px;
  }

  .qty-display {
    min-width: 30px;
    text-align: center;
    font-weight: 800;
    font-size: 0.95rem;
  }

  .line-height-tight {
    line-height: 1.2;
  }

  .fixed-footer {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: white;
    z-index: 1000;
    padding-bottom: calc(env(safe-area-inset-bottom) + 16px) !important;
    border-top: 1px solid rgba(0, 0, 0, 0.08);
  }

  .shadow-top {
    box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.05) !important;
  }

  .checkout-btn {
    transition: transform 0.1s;
    height: 54px !important;
  }

  .checkout-btn:active {
    transform: scale(0.97);
  }

  .cart-card {
    background: white !important;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
  }
</style>
