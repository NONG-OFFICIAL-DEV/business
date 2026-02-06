<script setup>
  defineProps({
    tableNumber: { type: String, default: '00' },
    search: String,
    cartCount: { type: Number, default: 0 }
  })
  defineEmits(['view-process','view-cart', 'update:search'])
</script>

<template>
  <div class="header-container sticky-header">
    <div class="d-flex align-center justify-space-between px-4 py-2">
      <div class="d-flex align-center">
        <div class="table-pill d-flex align-center px-3 py-1 mr-2">
          <v-icon size="16" color="#3b828e" class="mr-2">
            mdi-table-furniture
          </v-icon>
          <span class="text-subtitle-2 font-weight-black color-primary">
            {{ tableNumber }}
          </span>
        </div>
        <h1 class="text-subtitle-1 font-weight-bold">Menu</h1>
      </div>

      <!-- Right: Actions -->
      <div class="d-flex align-center ga-2">
        <!-- Cart -->
        <v-btn
          class="action-btn"
          icon
          variant="flat"
          size="small"
          @click="$emit('view-cart')"
        >
          <v-badge
            :content="cartCount"
            :model-value="cartCount > 0"
            color="primary"
            location="top end"
          >
            <v-icon icon="mdi-cart-outline" color="primary" size="22" />
          </v-badge>
        </v-btn>

        <!-- Tracking -->
        <v-btn
          class="action-btn"
          icon
          size="small"
          variant="flat"
          @click="$emit('view-process')"
        >
          <v-icon icon="mdi-room-service-outline" color="primary" size="22" />
        </v-btn>
      </div>
    </div>

    <div v-if="search !== undefined" class="px-4 pb-3">
      <v-text-field
        :model-value="search"
        @update:model-value="$emit('update:search', $event)"
        placeholder="Search dishes, drinks..."
        variant="solo"
        density="compact"
        hide-details
        flat
        bg-color="#f5f5f5"
        prepend-inner-icon="mdi-magnify"
        rounded="xl"
        class="search-input"
      >
        <template v-slot:append-inner v-if="search">
          <v-icon size="18" color="grey" @click="$emit('update:search', '')">
            mdi-close-circle
          </v-icon>
        </template>
      </v-text-field>
    </div>
  </div>
</template>

<style scoped>
  .header-container {
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(10px); /* Modern blur effect */
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
  }

  .sticky-header {
    position: sticky;
    top: 0;
    z-index: 100;
  }

  .table-pill {
    background: #3b828e15;
    border: 1px solid #3b828e30;
    border-radius: 100px;
  }

  .color-primary {
    color: #3b828e;
  }

  .order-btn {
    letter-spacing: 0.5px;
    background-color: #3b828e !important;
    color: white !important;
  }

  /* Make search feel like a native mobile input */
  :deep(.search-input .v-field) {
    border-radius: 12px !important;
    font-size: 14px;
  }

  :deep(.search-input .v-field__input) {
    min-height: 40px !important;
    padding-top: 0;
    padding-bottom: 0;
  }
  /* Modern glass header */
  .header {
    background: rgba(255, 255, 255, 0.82);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
  }

  .sticky-header {
    position: sticky;
    top: 0;
    z-index: 100;
  }

  /* Table pill */
  .table-pill {
    background: #3b828e12;
    border: 1px solid #3b828e30;
    border-radius: 999px;
  }

  .color-primary {
    color: #3b828e;
  }

  /* Modern action button */
  .action-btn {
    border-radius: 14px;
    background: white !important;
    border: 1px solid rgba(0, 0, 0, 0.06);
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
  }

  /* Search styling */
  :deep(.search-input .v-field) {
    border-radius: 16px !important;
    font-size: 14px;
  }

  :deep(.search-input .v-field__input) {
    min-height: 44px !important;
    padding-top: 0;
    padding-bottom: 0;
  }

  .cursor-pointer {
    cursor: pointer;
  }
</style>
