<template>
  <div>
    <div class="modern-header px-4 d-flex align-center">
      <v-btn
        icon="mdi-arrow-left"
        variant="flat"
        size="small"
        @click="$emit('back')"
      ></v-btn>
      <div class="flex-grow-1 text-center">
        <h2 class="text-subtitle-1 font-weight-black mb-0">Order Tracker</h2>
        <div class="d-flex align-center justify-center">
          <span class="status-indicator"></span>
          <span
            class="text-caption font-weight-bold text-grey-darken-1 text-uppercase tracking-widest"
          >
            Table {{ props.tableNumber || '...' }} • Live
          </span>
        </div>
      </div>
      <div style="width: 40px"></div>
    </div>

    <v-container class="px-5 pt-4 pb-16 mb-5">
      <template v-if="isInitialLoading">
        <v-card v-for="i in 3" :key="i" flat class="modern-card mb-5 pa-4">
          <div class="d-flex align-center mb-4">
            <v-skeleton-loader type="avatar" size="50" class="mr-3" />
            <v-skeleton-loader type="list-item-two-line" width="150" />
          </div>
          <v-skeleton-loader type="list-item" />
        </v-card>
      </template>

      <div
        v-else-if="
          hasReceivedFirstStreamData &&
          (!order || !order.items || order.items.length === 0)
        "
        class="empty-state-container d-flex flex-column align-center justify-center py-16 px-6"
      >
        <div class="visual-wrapper mb-8">
          <div class="blob-bg"></div>
          <v-icon size="90" class="floating-icon" color="primary">
            mdi-silverware-clean
          </v-icon>
        </div>

        <h3 class="text-h5 font-weight-black mb-2 text-slate-900">
          Your table is ready!
        </h3>

        <p
          class="text-body-1 text-grey-darken-1 text-center mb-10 max-width-300"
        >
          Scan the menu and place your first order. We'll track every step of
          your meal right here.
        </p>

        <v-btn
          color="primary"
          size="large"
          block
          rounded="pill"
          elevation="1"
          class="text-none"
          @click="$emit('reset')"
        >
          <v-icon start class="mr-2">mdi-book-open-variant</v-icon>
          Browse Full Menu
        </v-btn>
      </div>
      <div v-else>
        <v-card
          v-for="item in order?.items"
          :key="item.id"
          class="modern-card mb-5 overflow-visible"
          flat
        >
          <v-card-text class="pa-4">
            <div class="d-flex justify-space-between align-start mb-6">
              <div class="d-flex align-center">
                <div class="item-icon-wrapper">
                  <v-avatar size="50">
                    <v-img alt="John" :src="item.image_url"></v-img>
                  </v-avatar>
                  <!-- <v-icon color="#3b828e" size="24">
                    mdi-silverware-variant
                  </v-icon> -->
                </div>
                <div class="ml-3">
                  <h3 class="text-body-1 font-weight-black mb-0">
                    {{ item.name }}
                  </h3>
                  <p class="text-caption text-grey-darken-1 font-weight-medium">
                    Quantity: {{ item.qty }}
                  </p>
                </div>
              </div>
              <div class="price-tag">
                {{ formatCurrency(item.price * item.qty) }}
              </div>
            </div>

            <div class="custom-stepper-container">
              <div class="stepper-track-bg">
                <div
                  class="stepper-track-fill"
                  :style="{ width: getProgressValue(item.status) + '%' }"
                  :class="item.status?.toLowerCase()"
                ></div>
              </div>

              <div class="stepper-points">
                <div
                  v-for="step in ['pending', 'preparing', 'ready']"
                  :key="step"
                  class="point-group"
                  :class="{
                    active: isCurrentStep(item.status, step),
                    done: isStepCompleted(item.status, step)
                  }"
                >
                  <div class="point-circle">
                    <v-icon size="14">{{ getStepIcon(step) }}</v-icon>
                  </div>
                  <span class="point-label text-capitalize">{{ step }}</span>
                </div>
              </div>
            </div>
          </v-card-text>
        </v-card>

        <footer class="fixed-footer pa-4 rounded-t-xl shadow-top">
          <div class="d-flex justify-space-between align-center">
            <div>
              <p class="text-overline font-weight-black text-grey mb-n1">
                Total Payable
              </p>
              <h2 class="text-h5 font-weight-black color-primary">
                {{ formatCurrency(totalAmount) }}
              </h2>
            </div>
            <v-btn
              color="primary"
              rounded="pill"
              class="px-6 text-none"
              elevation="0"
              @click="$emit('reset')"
            >
              Add More
              <v-icon end size="18">mdi-plus</v-icon>
            </v-btn>
          </div>
        </footer>
      </div>
    </v-container>
  </div>
</template>

<script setup>
  import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
  import { useRoute } from 'vue-router'
  import { useDiningTableStore } from '../../stores/diningTableStore'
  import { useOrderStream } from '@/stores/useOrderStream'
  import { useCurrency } from '@/composables/useCurrency.js'

  const { formatCurrency } = useCurrency()
  const diningTableStore = useDiningTableStore()
  const route = useRoute()
  const token = route.params.token
  const steamStore = useOrderStream()

  const props = defineProps({
    tableNumber: String,
    tableId: Number
  })

  defineEmits(['reset', 'back'])

  const isInitialLoading = ref(true)
  const hasReceivedFirstStreamData = ref(false)

  // order from stream
  const order = computed(() => steamStore.order)

  watch(
    () => steamStore.order,
    val => {
      // Once we receive the first message (even empty), stop loading.
      if (val !== null && val !== undefined) {
        hasReceivedFirstStreamData.value = true
        isInitialLoading.value = false
      }
    },
    { immediate: true }
  )

  const totalAmount = computed(() => {
    if (!order.value || !order.value.items) return '0.00'
    return order.value.items
      .reduce((sum, item) => sum + item.price * item.qty, 0)
      .toFixed(2)
  })

  onMounted(async () => {
    isInitialLoading.value = true
    hasReceivedFirstStreamData.value = false

    try {
      const res = await diningTableStore.getTableNumberByToken(token)

      if (res?.table?.id) {
        steamStore.connect(res.table.id)

        // SAFETY NET: If no data arrives in 3 seconds, stop loading
        setTimeout(() => {
          if (isInitialLoading.value) {
            console.log('Stream timeout: showing empty state')
            isInitialLoading.value = false
            hasReceivedFirstStreamData.value = true
          }
        }, 3000)
      } else {
        isInitialLoading.value = false
      }
    } catch (err) {
      isInitialLoading.value = false
    }
  })

  onUnmounted(() => {
    steamStore.disconnect()
  })

  // Modern UI Helpers
  const getStepIcon = step => {
    if (step === 'pending') return 'mdi-receipt-text-check'
    if (step === 'preparing') return 'mdi-fire'
    return 'mdi-room-service'
  }

  const isCurrentStep = (status, step) => {
    const s = status?.toLowerCase()
    const map = {
      ordered: 'pending',
      pending: 'pending',
      preparing: 'preparing',
      ready: 'ready'
    }
    return map[s] === step.toLowerCase()
  }

  const isStepCompleted = (status, step) => {
    const s = status?.toLowerCase()
    const levels = { ordered: 1, pending: 1, preparing: 2, ready: 3 }
    const stepWeights = { pending: 1, preparing: 2, ready: 3 }
    return levels[s] > stepWeights[step]
  }

  const getProgressValue = status => {
    const s = status?.toLowerCase()
    if (s === 'ready') return 100
    if (s === 'preparing') return 50
    return 0
  }
</script>

<style scoped>
  /* Header */
  .modern-header {
    height: 72px;
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(12px);
    position: sticky;
    top: 0;
    z-index: 100;
    border-bottom: 1px solid rgba(0, 0, 0, 0.04);
  }
  .shadow-top {
    box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.08) !important;
  }

  .status-indicator {
    width: 6px;
    height: 6px;
    background: #4caf50;
    border-radius: 50%;
    margin-right: 6px;
    box-shadow: 0 0 0 rgba(76, 175, 80, 0.4);
    animation: pulse-green 2s infinite;
  }

  @keyframes pulse-green {
    0% {
      transform: scale(0.95);
      box-shadow: 0 0 0 0 rgba(76, 175, 80, 0.7);
    }
    70% {
      transform: scale(1);
      box-shadow: 0 0 0 8px rgba(76, 175, 80, 0);
    }
    100% {
      transform: scale(0.95);
      box-shadow: 0 0 0 0 rgba(76, 175, 80, 0);
    }
  }

  /* Item Cards */
  .section-label {
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 1.5px;
    color: #94a3b8;
    text-transform: uppercase;
  }

  /* 4. Footer is fixed to the viewport bottom */
  .fixed-footer {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: white;
    z-index: 1000;
    /* Bottom padding accounts for iPhone home bar */
    padding-bottom: calc(env(safe-area-inset-bottom) + 16px) !important;
    border-top: 1px solid rgba(0, 0, 0, 0.08);
  }
  .modern-card {
    background: white !important;
    border-radius: 20px !important;
    box-shadow: 0 8px 24px rgba(149, 157, 165, 0.1) !important;
    border: 1px solid rgba(0, 0, 0, 0.02) !important;
  }

  .item-icon-wrapper {
    width: 44px;
    height: 44px;
    background: #f1f5f9;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .price-tag {
    background: #3b828e11;
    color: #3b828e;
    padding: 4px 12px;
    border-radius: 10px;
    font-weight: 800;
    font-size: 14px;
  }

  /* Custom Stepper */
  .custom-stepper-container {
    position: relative;
    margin-top: 10px;
  }

  .stepper-track-bg {
    position: absolute;
    top: 16px;
    left: 30px;
    right: 30px;
    height: 3px;
    background: #f1f5f9;
    z-index: 1;
  }

  .stepper-track-fill {
    height: 100%;
    background: #3b828e;
    border-radius: 2px;
    transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .stepper-track-fill.ready {
    background: #4caf50;
  }

  .stepper-points {
    display: flex;
    justify-content: space-between;
    position: relative;
    z-index: 2;
  }

  .point-group {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 70px;
  }

  .point-circle {
    width: 32px;
    height: 32px;
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    transition: all 0.4s ease;
  }
  .point-label {
    font-size: 11px;
    font-weight: 700;
    color: #94a3b8;
    margin-top: 6px;
  }

  .active .point-circle {
    background: white;
    border-color: #3b828e;
    color: #3b828e;
    transform: scale(1.15);
    box-shadow: 0 4px 10px rgba(59, 130, 142, 0.2);
  }
  .active .point-label {
    color: #0f172a;
  }

  .done .point-circle {
    background-color: #3b828e !important;
    border-color: #3b828e;
    color: white;
  }

  /* Floating Total */
  .summary-anchor {
    position: fixed;
    bottom: 0px;
    left: 0px;
    right: 0px;
    z-index: 99;
  }

  .summary-floating-card {
    background: rgba(255, 255, 255, 0.95) !important;
    backdrop-filter: blur(10px);
    border-radius: 24px !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12) !important;
    border: 1px solid rgba(255, 255, 255, 0.3) !important;
  }

  .color-primary {
    color: #3b828e;
  }
  .tracking-widest {
    letter-spacing: 1px;
  }

  /* Empty State */
  .empty-state-visual {
    width: 120px;
    height: 120px;
    background: #f1f5f9;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
  }
</style>
