<script setup>
  import { computed } from 'vue'
  import { useCurrency } from '@/composables/useCurrency'

  const { formatCurrency } = useCurrency()

  const props = defineProps({
    cart: Array,
    paymentMethod: String,
    total: Number,
    storeType: String // 'retail' | 'hospitality'
  })

  const emit = defineEmits([
    'update:paymentMethod',
    'checkout',
    'update-qty',
    'clear-cart'
  ])

  const subtotal = computed(() =>
    props.cart.reduce((s, i) => s + i.price * i.qty, 0)
  )
</script>

<template>
  <!-- ========================= -->
  <!-- RESTAURANT / COFFEE CART -->
  <!-- ========================= -->
  <template v-if="storeType === 'hospitality'">
    <v-navigation-drawer
      location="end"
      width="380"
      permanent
      elevation="0"
      class="border-l-sm bg-grey-lighten-5"
    >
      <div class="d-flex flex-column fill-height">
        <!-- Header -->
        <div
          class="px-4 py-3 bg-white border-b d-flex align-center justify-space-between"
        >
          <div class="d-flex align-center">
            <v-icon icon="mdi-receipt-text-outline" class="mr-2" size="small" />
            <span class="text-subtitle-2 font-weight-black text-uppercase">
              Current Order
            </span>
          </div>
          <v-btn
            :disabled="!cart.length"
            variant="text"
            color="error"
            icon="mdi-trash-can-outline"
            size="small"
            @click="emit('clear-cart')"
          />
        </div>

        <!-- Cart Items -->
        <div class="flex-grow-1 overflow-y-auto pa-3">
          <v-card
            v-for="(item, index) in cart"
            :key="index"
            flat
            rounded="lg"
            class="mb-2 border-sm"
          >
            <div class="pa-2 d-flex align-center">
              <v-avatar size="48" rounded="md" class="bg-grey-lighten-4 border">
                <v-img :src="item.image_url" cover />
              </v-avatar>

              <div class="ml-3 flex-grow-1">
                <div class="d-flex justify-space-between align-start">
                  <span
                    class="font-weight-bold text-truncate"
                    style="max-width: 140px"
                  >
                    {{ item.name }}
                  </span>
                  <span class="text-subtitle-1 font-weight-black text-primary">
                    {{ formatCurrency(item.price * item.qty) }}
                  </span>
                </div>

                <!-- Customizations -->
                <div
                  v-if="Object.keys(item.customizations || {}).length"
                  class="d-flex flex-wrap gap-1 mt-1"
                >
                  <v-chip
                    v-for="(val, key) in item.customizations"
                    :key="key"
                    size="x-small"
                    variant="tonal"
                  >
                    {{ val }}
                  </v-chip>
                </div>

                <!-- Qty Control -->
                <div class="d-flex align-center justify-space-between mt-2">
                  <span class="text-grey text-caption">${{ item.price }}</span>

                  <div
                    class="d-flex align-center bg-grey-lighten-4 rounded-pill border"
                  >
                    <v-btn
                      icon="mdi-minus"
                      variant="text"
                      size="x-small"
                      @click="emit('update-qty', { item, change: -1 })"
                    />
                    <span class="px-2 text-caption font-weight-bold">
                      {{ item.qty }}
                    </span>
                    <v-btn
                      icon="mdi-plus"
                      variant="text"
                      size="x-small"
                      @click="emit('update-qty', { item, change: 1 })"
                    />
                  </div>
                </div>
              </div>
            </div>
          </v-card>
        </div>

        <!-- Payment & Total -->
        <v-sheet elevation="16" class="pa-4 border-t" color="white">
          <v-row no-gutters class="mx-n1 mb-4">
            <v-col
              cols="4"
              v-for="method in [
                { id: 'qr', icon: 'mdi-qrcode-scan', label: 'QR' },
                { id: 'cash', icon: 'mdi-cash', label: 'Cash' },
                { id: 'card', icon: 'mdi-credit-card-outline', label: 'Card' }
              ]"
              :key="method.id"
              class="pa-1"
            >
              <v-card
                flat
                @click="emit('update:paymentMethod', method.id)"
                :color="
                  paymentMethod === method.id ? 'orange-lighten-5' : 'white'
                "
                class="text-center py-2 rounded-lg border-sm"
              >
                <v-icon :icon="method.icon" size="20" class="mb-1" />
                <div class="text-caption font-weight-bold">
                  {{ method.label }}
                </div>
              </v-card>
            </v-col>
          </v-row>

          <div class="mb-3">
            <div class="d-flex justify-space-between text-caption mb-1">
              <span>Subtotal</span>
              <span>{{ formatCurrency(subtotal) }}</span>
            </div>
            <div
              class="d-flex justify-space-between text-h6 font-weight-black border-t-sm pt-2"
            >
              <span>Total</span>
              <span class="text-primary">{{ formatCurrency(total) }}</span>
            </div>
          </div>

          <v-btn
            block
            height="52"
            color="primary"
            flat
            rounded="lg"
            class="font-weight-black"
            :disabled="!cart.length"
            @click="emit('checkout')"
          >
            COMPLETE ORDER
          </v-btn>
        </v-sheet>
      </div>
    </v-navigation-drawer>
  </template>

  <!-- ================= -->
  <!-- MART / RETAIL CART -->
  <!-- ================= -->
  <template v-else>
    <v-navigation-drawer
      location="end"
      width="500"
      permanent
      class="border-l-sm bg-white"
    >
      <div class="d-flex flex-column fill-height">
        <div
          class="pa-4 bg-primary text-white d-flex justify-space-between align-center"
        >
          <div>
            <div class="text-caption">Items Count {{ cart.length }} Items</div>
          </div>
          <v-btn
            icon="mdi-delete-sweep"
            variant="tonal"
            color="white"
            size="x-small"
            @click="emit('clear-cart')"
          />
        </div>

        <div class="flex-grow-1 overflow-y-auto">
          <v-table density="compact" class="mart-table">
            <thead class="header-table bg-grey-lighten-4">
              <tr>
                <th class="text-left font-weight-bold">Item name</th>
                <th class="text-center font-weight-bold" style="width: 80px">
                  Qty
                </th>
                <th class="text-right font-weight-bold">Total</th>
                <th class="text-right font-weight-bold"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in cart" :key="index">
                <td class="py-2">
                  <div
                    class="font-weight-bold text-truncate"
                    style="max-width: 140px"
                  >
                    {{ item.name }}
                  </div>
                  <div class="text-caption text-grey">${{ item.price }}</div>
                </td>
                <td class="text-center">
                  <div
                    class="d-flex align-center bg-grey-lighten-4 rounded-pill border"
                  >
                    <v-btn
                      icon="mdi-minus"
                      variant="text"
                      size="x-small"
                      @click="emit('update-qty', { item, change: -1 })"
                    />
                    <span class="px-2 text-caption font-weight-bold">
                      {{ item.qty }}
                    </span>
                    <v-btn
                      icon="mdi-plus"
                      variant="text"
                      size="x-small"
                      @click="emit('update-qty', { item, change: 1 })"
                    />
                  </div>
                </td>
                <td class="text-right font-weight-bold">
                  {{ formatCurrency(item.price * item.qty) }}
                </td>
                <td class="text-right pa-0">
                  <v-btn
                    icon="mdi-close-circle"
                    variant="text"
                    color="red"
                    size="x-small"
                    class="remove-btn"
                    @click="emit('update-qty', { item, change: -item.qty })"
                  />
                </td>
              </tr>
            </tbody>
          </v-table>
        </div>

        <v-sheet class="pa-4 bg-grey-lighten-4 border-t">
          <div class="d-flex justify-space-between mb-1 text-subtitle-2">
            <span>Subtotal</span>
            <span>{{ formatCurrency(total) }}</span>
          </div>
          <div
            class="d-flex justify-space-between mb-4 text-h5 font-weight-black text-primary"
          >
            <span>Total</span>
            <span>{{ formatCurrency(total) }}</span>
          </div>

          <v-row no-gutters class="mx-n1 mb-4">
            <v-col
              cols="4"
              v-for="method in [
                { id: 'qr', icon: 'mdi-qrcode-scan', label: 'QR' },
                { id: 'cash', icon: 'mdi-cash', label: 'Cash' },
                { id: 'card', icon: 'mdi-credit-card-outline', label: 'Card' }
              ]"
              :key="method.id"
              class="pa-1"
            >
              <v-card
                flat
                @click="emit('update:paymentMethod', method.id)"
                :color="
                  paymentMethod === method.id ? 'orange-lighten-5' : 'white'
                "
                class="text-center py-2 rounded-lg border-sm"
              >
                <v-icon :icon="method.icon" size="20" class="mb-1" />
                <div class="text-caption font-weight-bold">
                  {{ method.label }}
                </div>
              </v-card>
            </v-col>
          </v-row>

          <v-btn
            block
            height="52"
            color="primary"
            flat
            rounded="lg"
            class="font-weight-black"
            :disabled="!cart.length"
            @click="emit('checkout')"
          >
            F10 - PAY NOW
          </v-btn>
        </v-sheet>
      </div>
    </v-navigation-drawer>
  </template>
</template>

<style scoped>
  .mart-table :deep(td) {
    height: 55px !important;
    border-bottom: 1px dashed #e0e0e0 !important;
  }
</style>
