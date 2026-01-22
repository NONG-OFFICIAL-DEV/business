<template>
  <v-card class="mb-4 order-card" elevation="2" border>
    <v-card-item
      :class="['pa-0', getUrgencyBgColor(order.order_time)]"
      class="border-bottom"
    >
      <div class="d-flex align-stretch" style="height: 70px">
        <div class="pa-3 d-flex flex-column justify-center flex-grow-1">
          <span
            class="text-caption font-weight-black text-grey-darken-1 line-height-1"
          >
            ORDER #{{ order.order_no.slice(-4) }}
          </span>
          <div class="d-flex align-center mt-1">
            <v-icon size="small" class="mr-1" color="grey">
              mdi-clock-outline
            </v-icon>
            <span class="text-subtitle-2 font-weight-bold mr-2">
              <v-chip
                size="x-small"
                :color="getUrgencyColor(order.order_time)"
                variant="flat"
              >
                {{ getElapsedTime(order.order_time) }} ago
              </v-chip>
            </span>
          </div>
        </div>

        <div
          class="bg-primary d-flex flex-column align-center justify-center px-6"
        >
          <span
            class="text-caption font-weight-black text-white opacity-80 line-height-1"
          >
            Table
          </span>
          <span class="text-h6 font-weight-black text-white line-height-1">
            {{ order.table.table_number }}
          </span>
        </div>
      </div>
    </v-card-item>

    <v-divider />

    <v-card-text class="py-3">
      <v-list density="compact" class="bg-transparent pa-0">
        <v-list-item v-for="(item, i) in order.items" :key="i" class="px-0">
          <template v-slot:prepend>
            <v-chip color="success" class="mr-3" size="small">
              X {{ item.quantity }}
            </v-chip>
          </template>

          <div>
            <v-list-item-title
              class="text-subtitle-2"
              v-text="item.name"
            ></v-list-item-title>
            <v-list-item-subtitle v-if="item.note">
              ⚠️ {{ item.note }}
            </v-list-item-subtitle>
          </div>
        </v-list-item>
      </v-list>
    </v-card-text>

    <v-card-actions v-if="order.kitchen_status === 'ready'" class="pa-0">
      <v-btn
        block
        color="success"
        height="50"
        variant="flat"
        class="font-weight-black text-none"
        @click.stop="$emit('served', order.id)"
      >
        <v-icon start>mdi-check-circle</v-icon>
        BUMP / SERVED in {{ timeLabel }}
      </v-btn>
    </v-card-actions>
    <v-divider />
    <div class="bg-grey-lighten-4 py-1 d-flex justify-center cursor-move">
      <v-icon color="grey">mdi-drag-horizontal</v-icon>
    </div>
  </v-card>
</template>

<script setup>
  import { ref, watch, onUnmounted, computed } from 'vue'
  import { useDate } from '@/composables/useDate'
  const { formatDateTime } = useDate()

  const props = defineProps({
    order: { type: Object, required: true }
  })

  const emit = defineEmits(['served'])

  const AUTO_SERVE_MINUTES = 2

  const remainingSeconds = ref(null)
  let timer = null

  const startTimer = () => {
    clearTimer()

    remainingSeconds.value = AUTO_SERVE_MINUTES * 60

    timer = setInterval(() => {
      remainingSeconds.value--

      if (remainingSeconds.value <= 0) {
        emit('served', props.order.id)
        clearTimer()
      }
    }, 1000)
  }

  const clearTimer = () => {
    if (timer) {
      clearInterval(timer)
      timer = null
    }
  }

  watch(
    () => props.order.kitchen_status,
    status => {
      if (status === 'ready') {
        startTimer()
      } else {
        clearTimer()
      }
    },
    { immediate: true }
  )

  const timeLabel = computed(() => {
    if (remainingSeconds.value === null) return ''
    const m = Math.floor(remainingSeconds.value / 60)
    const s = remainingSeconds.value % 60
    return `${m}:${s.toString().padStart(2, '0')}`
  })

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
  onUnmounted(clearTimer)
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
