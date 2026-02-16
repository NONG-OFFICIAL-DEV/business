<script setup>
  import { computed, ref } from 'vue'

  const props = defineProps({
    modelValue: { type: Boolean, default: false },
    type: { type: String, default: 'details' }, // "details" | "edit"
    order: { type: Object, default: null },
    isSaving: { type: Boolean, default: false }
  })

  const emit = defineEmits(['update:modelValue', 'save'])

  const isOpen = computed({
    get: () => props.modelValue,
    set: v => emit('update:modelValue', v)
  })

  const close = () => {
    isOpen.value = false
  }
  const copied = ref(false)

  const copyToClipboard = async text => {
    try {
      await navigator.clipboard.writeText(text)
      copied.value = true
      setTimeout(() => {
        copied.value = false
      }, 2000)
    } catch (err) {
      console.error('Failed to copy!', err)
    }
  }
</script>

<template>
  <v-dialog v-model="isOpen" max-width="800" v-if="type === 'details'">
    <v-card class="rounded-lg">
      <v-card-title class="d-flex align-center justify-space-between pa-4">
        <div class="d-flex align-center">
          <v-icon
            icon="mdi-receipt-text-outline"
            class="mr-2"
            color="primary"
          />
          <span class="text-h6 font-weight-bold">
            Order Details - {{ order?.id }}
          </span>
        </div>
        <v-btn
          icon="mdi-close"
          variant="text"
          density="comfortable"
          @click="close"
        />
      </v-card-title>

      <v-divider />

      <v-card-text v-if="order" class="pa-6">
        <v-row>
          <v-col cols="12" md="7">
            <v-row dense>
              <v-col cols="12" sm="6">
                <div class="text-caption text-medium-emphasis">Customer</div>
                <div class="font-weight-bold text-body-1">
                  {{ order.customer }}
                </div>
              </v-col>

              <v-col cols="12" sm="6">
                <div class="text-caption text-medium-emphasis">Phone</div>
                <div class="font-weight-bold text-body-1">
                  {{ order.phone }}
                </div>
              </v-col>

              <v-col cols="12" class="mt-2">
                <div class="text-caption text-medium-emphasis">
                  Delivery Address
                </div>
                <v-hover v-slot="{ isHovering, props }">
                  <div
                    v-bind="props"
                    class="pa-3 rounded bg-grey-lighten-4 d-flex align-center justify-space-between mt-1 cursor-pointer"
                    @click="copyToClipboard(order.address)"
                  >
                    <div class="font-weight-medium text-body-2 pr-2">
                      {{ order.address }}
                    </div>
                    <v-icon
                      :color="isHovering ? 'primary' : 'grey'"
                      size="small"
                      :icon="copied ? 'mdi-check' : 'mdi-content-copy'"
                    />
                  </div>
                </v-hover>
              </v-col>
            </v-row>
          </v-col>

          <v-col cols="12" md="5" class="border-s-md pl-md-6">
            <div class="mb-4">
              <div class="text-caption text-medium-emphasis">
                Delivery Express
              </div>
              <v-chip
                color="deep-purple"
                size="small"
                variant="flat"
                prepend-icon="mdi-truck-delivery"
              >
                {{ order.deliveryExpress || 'EVT Express' }}
              </v-chip>
            </div>

            <v-row dense>
              <v-col cols="6">
                <div class="text-caption text-medium-emphasis">Date</div>
                <div class="font-weight-bold">{{ order.date }}</div>
              </v-col>
              <v-col cols="6">
                <div class="text-caption text-medium-emphasis">Payment</div>
                <v-chip size="x-small" label border>
                  {{ order.paymentMethod }}
                </v-chip>
              </v-col>
            </v-row>
          </v-col>
        </v-row>

        <div class="mt-6">
          <div class="text-subtitle-2 font-weight-bold mb-2">Ordered Items</div>
          <v-table density="compact" class="border rounded-lg">
            <thead class="bg-grey-lighten-5">
              <tr>
                <th>Product</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Price</th>
                <th class="text-right">Total</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(it, idx) in order.items" :key="idx">
                <td class="py-2">{{ it.name }}</td>
                <td class="text-right">{{ it.qty }}</td>
                <td class="text-right">${{ it.price }}</td>
                <td class="text-right font-weight-bold">
                  ${{ (it.qty * it.price).toFixed(2) }}
                </td>
              </tr>
            </tbody>
          </v-table>
        </div>
      </v-card-text>

      <v-divider />

      <v-card-actions class="pa-4 bg-grey-lighten-5">
        <v-spacer />
        <v-btn variant="text" color="grey-darken-1" @click="close">Close</v-btn>
        <v-btn color="primary" prepend-icon="mdi-printer" variant="tonal">
          Print Invoice
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>
<style scoped>
  .cursor-pointer {
    cursor: pointer;
    transition: background 0.2s;
  }
  .cursor-pointer:hover {
    background-color: #eeeeee !important;
  }
</style>
