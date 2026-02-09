<script setup>
  defineProps({
    categories: Array,
    modelValue: null,
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
    <div class="category-wrapper py-0 px-4 d-flex no-wrap overflow-x-auto">
      <v-slide-group
        :model-value="modelValue"
        @update:model-value="$emit('update:modelValue', $event)"
        :show-arrows="false"
        mandatory
      >
        <v-slide-group-item v-slot="{ isSelected, select }" value="All">
          <v-btn
            :color="isSelected ? 'primary' : 'white'"
            :class="isSelected ? 'text-white' : 'text-grey-darken-1 border'"
            rounded="pill"
            flat
            class="ma-2 text-none font-weight-bold px-6"
            height="40"
            prepend-icon="mdi-food"
            size="x-small"
            @click="select"
          >
            All
          </v-btn>
        </v-slide-group-item>
        <v-slide-group-item
          v-for="cat in categories"
          :key="cat"
          :value="cat.id"
          v-slot="{ isSelected, select }"
        >
          <v-btn
            :color="isSelected ? 'primary' : 'white'"
            :class="isSelected ? 'text-white' : 'text-grey-darken-1 border'"
            rounded="pill"
            flat
            class="ma-2 text-none font-weight-bold px-6"
            height="40"
            prepend-icon="mdi-food"
            size="x-small"
            @click="select"
          >
            {{ cat.name }}
          </v-btn>
        </v-slide-group-item>
      </v-slide-group>
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
