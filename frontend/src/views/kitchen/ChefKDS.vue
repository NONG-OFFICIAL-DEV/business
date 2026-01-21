<template>
  <div>
    <v-app-bar color="primary" flat border="bottom">
      <v-app-bar-title class="font-weight-black text-h5">
        KITCHEN
        <span class="text-teal-lighten-3">DASHBOARD</span>
      </v-app-bar-title>

      <v-spacer></v-spacer>

      <div class="d-none d-md-flex align-center mr-6">
        <v-divider vertical inset class="mr-4"></v-divider>
        <div class="text-center">
          <div class="text-caption text-teal-lighten-4 line-height-1">
            SYSTEM TIME
          </div>
          <div
            class="text-subtitle-1 font-weight-bold text-white line-height-1"
          >
            {{ currentTime }}
          </div>
        </div>
      </div>

      <v-tooltip text="Sound Check" location="bottom">
        <template v-slot:activator="{ props }">
          <v-btn v-bind="props" icon="mdi-volume-high" variant="text"></v-btn>
          <!-- @click="testSound" -->
        </template>
      </v-tooltip>

      <!-- <v-chip
        :color="isConnected ? 'success' : 'error'"
        class="mr-4 font-weight-black"
        size="small"
        label
      >
        <v-icon start size="14">
          {{ isConnected ? 'mdi-wifi' : 'mdi-wifi-off' }}
        </v-icon>
        {{ isConnected ? 'LIVE' : 'OFFLINE' }}
      </v-chip> -->
    </v-app-bar>
    <v-main class="bg-grey-lighten-4">
      <v-container fluid class="fill-height bg-grey-lighten-4">
        <v-row class="fill-height">
          <v-col
            v-for="col in columns"
            :key="col.status"
            cols="12"
            md="4"
            class="d-flex flex-column h-100"
          >
            <div
              :class="[
                'pa-2 rounded-t-lg text-white d-flex justify-space-between align-center',
                col.color
              ]"
            >
              <h2 class="text-h6 font-weight-bold">{{ col.title }}</h2>
              <v-chip color="white" variant="outlined" size="small">
                {{ col.list.length }} Orders
              </v-chip>
            </div>

            <draggable
              v-model="col.list"
              group="orders"
              item-key="id"
              class="flex-grow-1 bg-grey-lighten-3 pa-3 rounded-b-lg scroll-y"
              min-height="100px"
              @change="e => handleMove(e, col.status)"
            >
              <template #item="{ element }">
                <OrderCard :order="element" />
              </template>
            </draggable>
          </v-col>
        </v-row>
      </v-container>
    </v-main>
  </div>
</template>

<script setup>
  import { ref, computed, onMounted, onUnmounted } from 'vue'
  import draggable from 'vuedraggable'
  import OrderCard from '@/components/kitchen/OrderCard.vue'
  import { useKitchenStore } from '@/stores/kitchenStore'

  const kitchenStore = useKitchenStore()
  const currentTime = ref('')
  let timer = null

  const updateClock = () => {
    const now = new Date()
    currentTime.value = now.toLocaleTimeString([], {
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit'
    })
  }

  onMounted(() => {
    updateClock()
    timer = setInterval(updateClock, 1000)
    kitchenStore.startOrdersStream()
  })
  onUnmounted(() => {
    if (timer) clearInterval(timer)
    kitchenStore.stopOrdersStream()
  })
  const columns = computed(() => [
    {
      title: '🆕 NEW',
      status: 'pending',
      color: 'bg-deep-orange-darken-2',
      list: kitchenStore.orders.filter(o => o.kitchen_status === 'pending')
    },
    {
      title: '🔥 PREPARING',
      status: 'preparing',
      color: 'bg-amber-darken-3',
      list: kitchenStore.orders.filter(o => o.kitchen_status === 'preparing')
    },
    {
      title: '✅ READY',
      status: 'ready',
      color: 'bg-green-darken-2',
      list: kitchenStore.orders.filter(o => o.kitchen_status === 'ready')
    }
  ])

  const handleMove = async (evt, newStatus) => {
    if (evt.added) {
      const orderId = evt.added.element.id
      const order = kitchenStore.orders.find(o => o.id === orderId)
      if (order) {
        try {
          // Update local state immediately for smooth UI
          order.kitchen_status = newStatus

          // Call backend service to persist
          if (newStatus === 'preparing') {
            await kitchenStore.startCooking(orderId)
          } else if (newStatus === 'ready') {
            await kitchenStore.markReady(orderId)
          }
        } catch (err) {
          console.error('Failed to update order status', err)
          // revert if needed
          evt.added.element.kitchen_status = evt.from.dataset.status
        }
      }
    }
  }
</script>

<style scoped>
  .scroll-y {
    overflow-y: auto;
    max-height: calc(100vh - 160px);
    min-height: 100vh;
  }
</style>
