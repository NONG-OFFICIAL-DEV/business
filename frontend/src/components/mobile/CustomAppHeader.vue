<template>
  <header class="app-header-wrapper">
    <div class="status-bar-safe"></div>

    <div class="header-content d-flex align-center px-4">
      <div class="header-left">
        <v-btn
          v-if="showBack && !$slots.left"
          icon="mdi-chevron-left"
          variant="flat"
          size="32"
          class="action-btn"
          @click="$emit('back')"
        />
        <slot name="left"></slot>
      </div>

      <div class="header-center flex-grow-1 text-center">
        <div class="text-subtitle-1 font-weight-black">{{ title }}</div>

        <div v-if="subtitle || $slots.subtitle" class="header-subtitle">
          <slot name="subtitle">
            <div class="d-flex align-center justify-center ga-1">
              <span v-if="live" class="live-dot"></span>
              <span class="text-caption text-grey mt-n1">{{ subtitle }}</span>
            </div>
          </slot>
        </div>
      </div>

      <div class="header-right d-flex justify-end">
        <slot name="right">
          <div style="width: 32px" v-if="showBack && !$slots.left"></div>
        </slot>
      </div>
    </div>
  </header>
</template>

<script setup>
  defineProps({
    title: String,
    subtitle: String,
    showBack: { type: Boolean, default: true },
    live: { type: Boolean, default: false }
  })
  defineEmits(['back'])
</script>

<style scoped>
  .app-header-wrapper {
    position: sticky;
    top: 0;
    z-index: 1000;
    /* Glassmorphism effect */
    background: white;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
  }

  .status-bar-safe {
    height: env(safe-area-inset-top);
    background: transparent;
  }

  .header-content {
    height: 64px; /* Standard mobile header height */
  }

  .header-left,
  .header-right {
    width: 48px; /* Fixed width side containers ensure center stays center */
    display: flex;
    align-items: center;
  }

  .header-subtitle {
    margin-top: 2px;
  }

  /* Action Button Style */
  .action-btn {
    background: white !important;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04) !important;
    border-radius: 12px !important;
  }

  /* Live Indicator */
  .live-dot {
    width: 6px;
    height: 6px;
    background-color: #4caf50;
    border-radius: 50%;
    box-shadow: 0 0 0 rgba(76, 175, 80, 0.4);
    animation: pulse 2s infinite;
    margin-bottom: 4px;
  }

  @keyframes pulse {
    0% {
      transform: scale(0.95);
      box-shadow: 0 0 0 0 rgba(76, 175, 80, 0.7);
    }
    70% {
      transform: scale(1);
      box-shadow: 0 0 0 6px rgba(76, 175, 80, 0);
    }
    100% {
      transform: scale(0.95);
      box-shadow: 0 0 0 0 rgba(76, 175, 80, 0);
    }
  }
</style>
