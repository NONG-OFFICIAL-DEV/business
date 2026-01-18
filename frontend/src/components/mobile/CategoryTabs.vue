<script setup>
defineProps({
  categories: Array,
  modelValue: String,
  search: String
})

defineEmits(['update:modelValue', 'update:search'])
</script>

<template>
  <!-- Sticky Wrapper -->
  <div class="sticky-bar">
    <!-- Search -->
    <div class="py-3 px-4">
      <v-text-field
        :model-value="search"
        @update:model-value="$emit('update:search', $event)"
        placeholder="Search for coffee, tea..."
        prepend-inner-icon="mdi-magnify"
        flat
        rounded="pill"
        density="compact"
        hide-details
        class="search-bar"
      />
    </div>

    <!-- Categories -->
    <div class="category-wrapper py-3 px-4 d-flex no-wrap overflow-x-auto">
      <v-btn
        v-for="cat in categories"
        :key="cat"
        @click="$emit('update:modelValue', cat)"
        :color="modelValue === cat ? '#3b828e' : 'white'"
        :class="modelValue === cat ? 'text-white' : 'text-grey-darken-1 border'"
        flat
        rounded="pill"
        class="text-none font-weight-bold mr-2 px-6"
        size="small"
        height="40"
      >
        {{ cat }}
      </v-btn>
    </div>
  </div>
</template>

<style scoped>
/* Sticky behavior */
.sticky-bar {
  position: sticky;
  top: 64px; /* height of AppHeader */
  z-index: 10;
  background: white;
}

/* Search input */
.search-bar :deep(.v-field__input) {
  font-size: 0.9rem;
}

/* Hide horizontal scrollbar */
.category-wrapper {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
.category-wrapper::-webkit-scrollbar {
  display: none;
}

/* Button border */
.border {
  border: 1px solid #e0e0e0 !important;
}
</style>
