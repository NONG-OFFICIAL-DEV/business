<template>
  <v-dialog
    :model-value="modelValue"
    @update:model-value="$emit('update:modelValue', $event)"
    max-width="600"
    scrollable
    transition="dialog-bottom-transition"
    fullscreen
  >
    <v-card>
      <v-card-title class="bg-grey-darken-4 text-white d-flex align-center">
        <v-icon start>mdi-history</v-icon>
        Recent Bumps (History) from 7 mornig untill now (time)
        <v-spacer></v-spacer>
        <v-btn
          icon="mdi-close"
          variant="text"
          size="small"
          @click="$emit('update:modelValue', false)"
        ></v-btn>
      </v-card-title>

      <!-- <v-card-text class="pa-0" style="height: 500px">
        <v-list lines="two">
          <v-list-item
            v-for="(order, index) in bumpedHistory"
            :key="order.id"
            class="border-bottom"
          >
            <template v-slot:prepend>
              <v-avatar color="grey-lighten-3" class="font-weight-bold">
                #{{ order.order_no.slice(-3) }}
              </v-avatar>
            </template>

            <v-list-item-title class="font-weight-bold">
              Table {{ order.table.table_number }}
            </v-list-item-title>

            <v-list-item-subtitle>
              {{ order.items.map(i => `${i.quantity}x ${i.name}`).join(', ') }}
            </v-list-item-subtitle>

            <template v-slot:append>
              <v-btn
                color="primary"
                variant="tonal"
                rounded="pill"
                size="small"
                @click="restoreOrder(order, index)"
              >
                <v-icon start>mdi-undo</v-icon>
                RECALL
              </v-btn>
            </template>
          </v-list-item>

          <v-sheet
            v-if="bumpedHistory.length === 0"
            class="pa-12 text-center text-grey"
          >
            <v-icon size="64" class="mb-2">mdi-tray-full</v-icon>
            <div>No recently completed orders.</div>
          </v-sheet>
        </v-list>
      </v-card-text> -->
    </v-card>
  </v-dialog>
</template>
<script setup>
  import { useKitchenStore } from '@/stores/kitchenStore'

  const kitchenStore = useKitchenStore()
  defineProps({ modelValue: Boolean })
  defineEmits(['update:modelValue'])

  const restoreOrder = order => {
    // kitchenStore.recallOrder(order)
  }
</script>
