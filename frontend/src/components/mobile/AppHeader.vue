<script setup>
  defineProps({
    tableNumber: { type: String, default: '00' },
    search: String
  })
  defineEmits(['view-process', 'update:search'])
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

      <v-btn class="text-none" icon  variant="text">
        <v-badge
          location="top right"
          color="primary"
          content="1"
          bordered
          @click="$emit('view-process')"
        >
          <v-icon icon="mdi-cart" color="primary"></v-icon>
        </v-badge>
      </v-btn>
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
</style>
