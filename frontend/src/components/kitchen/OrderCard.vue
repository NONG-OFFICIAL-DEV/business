<template>
  <v-card class="mb-4 order-card" elevation="2" border>
    <v-card-item class="bg-white border-bottom">
      <template v-slot:prepend>
        <v-icon :color="getUrgencyColor(order.time)" size="small">mdi-clock-outline</v-icon>
        <span class="text-caption ml-1 font-weight-black">{{ order.time }}</span>
      </template>
      
      <v-card-title class="text-subtitle-1 font-weight-bold d-flex justify-end">
        #{{ order.order_no }}
      </v-card-title>
    </v-card-item>

    <v-divider />

    <v-card-text class="py-3">
      <div class="d-flex align-center justify-space-between mb-1">
        <span class="text-h6 font-weight-black text-primary">Table {{ order.table.table_number }}</span>
      </div>

      <v-list density="compact" class="bg-transparent pa-0">
        <v-list-item v-for="(item, i) in order.items" :key="i" class="px-0">
          <div class="d-flex align-start">
            <v-avatar color="grey-lighten-4" size="32" class="mr-3 font-weight-black">
              {{ item.quantity }}
            </v-avatar>
            <div>
              <div class="text-body-1 font-weight-medium">{{ item.name }}</div>
              <div v-if="item.note" class="text-caption text-error font-weight-bold uppercase">
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
defineProps({ order: Object });

// Simple logic to show red if order is > 15 mins old
const getUrgencyColor = (startTime) => {
  return "error"; // You can inject actual time comparison logic here
};
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