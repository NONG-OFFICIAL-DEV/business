<script setup>
  defineProps({
    tableNumber: String,
    search: String
  })
</script>

<template>
  <div class="bg-white px-4 pt-3 pb-3 border-bottom sticky-header">
    <div class="d-flex align-center">
      <div class="d-flex align-center flex-grow-1">
        <v-avatar color="#3b828e15" size="40" class="mr-3">
          <v-icon color="#3b828e" size="20">mdi-table-chair</v-icon>
        </v-avatar>
        
        <div>
          <div class="text-caption font-weight-bold text-grey-darken-1 line-height-1">
            TABLE
          </div>
          <div class="text-h6 font-weight-black line-height-1">
            {{ tableNumber }}
          </div>
        </div>
      </div>

      <v-btn
        color="#3b828e"
        variant="tonal"
        rounded="pill"
        size="small"
        class="text-none font-weight-bold px-4"
        @click="$emit('view-process')"
      >
        <v-badge
          color="error"
          dot
          offset-x="-2"
          offset-y="-2"
        >
          <v-icon start size="18">mdi-clock-fast</v-icon>
        </v-badge>
        My Orders
      </v-btn>
    </div>

    <v-text-field
      v-if="search !== undefined"
      :model-value="search"
      @update:model-value="$emit('update:search', $event)"
      placeholder="Search menu..."
      variant="filled"
      density="compact"
      hide-details
      prepend-inner-icon="mdi-magnify"
      rounded="lg"
      class="mt-3 bg-grey-lighten-4 custom-search"
    />
  </div>
</template>

<style scoped>
.sticky-header {
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.line-height-1 {
  line-height: 1.1 !important;
}

.border-bottom {
  border-bottom: 1px solid #eeeeee !important;
}

/* Custom styling to make the search field less "boxy" */
:deep(.custom-search .v-field__outline) {
  display: none;
}
:deep(.custom-search .v-field__input) {
  font-size: 0.9rem;
}
</style>