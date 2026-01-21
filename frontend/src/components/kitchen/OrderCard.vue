<template>
  <v-card class="mb-4 order-card" elevation="2" border>
    <v-card-item
      :class="['border-bottom py-2', getUrgencyBgColor(order.order_time)]"
    >
      <template v-slot:prepend>
        <div class="d-flex flex-column align-start">
          <span class="text-subtitle-2 font-weight-black mr-2 text-no-wrap">
            #{{ order.order_no }}
          </span>

          <v-chip
            size="small"
            :color="getUrgencyColor(order.order_time)"
            variant="flat"
            class="font-weight-bold justify-center"
            style="min-width: 55px; height: 18px; font-size: 0.7rem"
          >
            {{ getElapsedTime(order.order_time) }}
          </v-chip>
        </div>
      </template>

      <v-card-title class="d-flex flex-column align-end">
        <span
          class="text-caption font-weight-medium text-uppercase text-grey-darken-2"
        >
          Order Time
        </span>
        <span class="text-subtitle-2 font-weight-bold">
          {{ formatDateTime(order.order_time) }}
        </span>
      </v-card-title>
    </v-card-item>

    <v-divider />

    <v-card-text class="py-3">
      <div class="d-flex align-center justify-space-between mb-1">
        <span class="text-h6 font-weight-black text-primary">
          Table {{ order.table.table_number }}
        </span>
      </div>

      <v-list density="compact" class="bg-transparent pa-0">
        <v-list-item v-for="(item, i) in order.items" :key="i" class="px-0">
          <div class="d-flex align-start">
            <v-avatar
              color="grey-lighten-4"
              size="32"
              class="mr-3 font-weight-black"
            >
              {{ item.quantity }}
            </v-avatar>
            <div>
              <div class="text-body-1 font-weight-medium">{{ item.name }}</div>
              <div
                v-if="item.note"
                class="text-caption text-error font-weight-bold uppercase"
              >
                ⚠️ {{ item.note }}
              </div>
            </div>
          </div>
        </v-list-item>
      </v-list>
    </v-card-text>

    <v-divider />
    <div class="bg-grey-lighten-4 py-1 d-flex justify-center cursor-move">
      <v-icon color="grey">mdi-drag-horizontal</v-icon>
    </div>
  </v-card>
</template>

<script setup>
  import { useDate } from '@/composables/useDate'
  const { formatDateTime } = useDate()
  defineProps({ order: Object })

  const getElapsedTime = time => {
    const diff = Math.floor((new Date() - new Date(time)) / 60000)
    return `${diff} min`
  }

  // Returns colors based on kitchen standards
  const getUrgencyColor = time => {
    const diff = Math.floor((new Date() - new Date(time)) / 60000)
    if (diff > 15) return 'error' // Red for > 15 mins
    if (diff > 8) return 'warning' // Orange for > 8 mins
    return 'success' // Green for fresh
  }

  // NEW: Highlights the whole header background slightly
  const getUrgencyBgColor = time => {
    const diff = Math.floor((new Date() - new Date(time)) / 60000)
    if (diff > 15) return 'bg-red-lighten-5'
    if (diff > 8) return 'bg-orange-lighten-5'
    return 'bg-white'
  }
</script>

<style scoped>
  .order-card {
    border-radius: 12px;
    cursor: grab;
    transition: transform 0.2s ease;
  }
  .order-card:active {
    cursor: grabbing;
    transform: scale(0.95);
  }
  .cursor-move {
    cursor: grab;
  }
</style>
