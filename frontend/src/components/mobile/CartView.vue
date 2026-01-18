<script setup>
  defineProps({
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
</script>

<template>
  <v-container class="pb-15">
    <v-row no-gutters align="center" class="mb-4">
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
    </v-row>

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
            :src="item.image"
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
      elevation="10"
      class="pa-4 flex-column rounded-t-xl"
    >
      <div class="w-100 mb-4">
        <div class="d-flex justify-space-between align-center mb-1">
          <span class="text-medium-emphasis">Subtotal</span>
          <span>${{ total.toFixed(2) }}</span>
        </div>
        <div class="d-flex justify-space-between align-center mb-4">
          <span class="font-weight-black text-h6">Total</span>
          <span class="font-weight-black text-h6 text-primary">
            ${{ total.toFixed(2) }}
          </span>
        </div>

        <v-btn
          block
          color="primary"
          height="56"
          rounded="pill"
          class="font-weight-bold elevation-4"
          :loading="loading"
          @click="$emit('submit')"
        >
          PLACE ORDER • TABLE {{ tableNumber }}
          <template v-slot:append>
            <v-icon>mdi-arrow-right</v-icon>
          </template>
        </v-btn>
      </div>
    </v-footer>
  </v-container>
</template>

<style scoped>
  .v-footer {
    background: white !important;
    border-top: 1px solid rgba(0, 0, 0, 0.05);
  }
</style>
