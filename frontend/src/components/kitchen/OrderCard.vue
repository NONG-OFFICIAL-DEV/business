<template>
  <v-card class="mb-4 order-card" elevation="0">
    <v-card-item :class="getUrgencyBgColor(order.order_time)">
      <div class="d-flex justify-space-between">
        <div>
          <div class="text-caption font-weight-black">
            {{ order.order_no }}
          </div>
          <v-chip size="x-small">
            {{ getElapsedTime(order.order_time) }} ago
          </v-chip>
        </div>

        <div class="text-h6 font-weight-black">
          T-{{ order.table_number }}
        </div>
      </div>
    </v-card-item>

    <v-divider />

    <v-card-text>
      <div class="d-flex align-center">
        <v-chip color="success" size="small" class="mr-2 font-weight-bold">
          x{{ order.quantity }}
        </v-chip>
        <div class="text-subtitle-1 font-weight-bold">
          {{ order.menu_name }}
        </div>
      </div>

      <div v-if="order.note" class="text-caption text-error mt-1">
        ⚠ {{ order.note }}
      </div>
    </v-card-text>

    <v-card-actions v-if="order.kitchen_status === 'ready'">
      <v-btn
        block
        color="success"
        variant="tonal"
        @click="$emit('served', order.item_id)"
      >
        DONE
      </v-btn>
    </v-card-actions>
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
