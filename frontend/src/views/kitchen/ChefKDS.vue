<template>
  <v-container fluid class="fill-height bg-grey-lighten-4 pa-6">
    <v-row class="fill-height">
      
      <v-col v-for="col in columns" :key="col.status" cols="12" md="4" class="d-flex flex-column h-100">
        <div :class="['pa-4 rounded-t-lg text-white d-flex justify-space-between align-center', col.color]">
          <h2 class="text-h6 font-weight-bold">{{ col.title }}</h2>
          <v-chip color="white" variant="outlined" size="small">{{ col.list.length }} Orders</v-chip>
        </div>

        <draggable
          v-model="col.list"
          group="orders"
          item-key="id"
          class="flex-grow-1 bg-grey-lighten-3 pa-3 rounded-b-lg scroll-y"
          @change="(e) => handleMove(e, col.status)"
        >
          <template #item="{ element }">
            <OrderCard :order="element" />
          </template>
        </draggable>
      </v-col>

    </v-row>
  </v-container>
</template>

<script setup>
import { ref, computed } from "vue";
import draggable from "vuedraggable";
import OrderCard from "@/components/kitchen/OrderCard.vue";

const orders = ref([
  { id: 1, order_no: "ORD-101", table: "T1", kitchen_status: "pending", items: [{ name: "Fried Rice", quantity: 2, note: "No spicy" }], time: "12:40" },
  { id: 2, order_no: "ORD-102", table: "T3", kitchen_status: "preparing", items: [{ name: "Beef Noodles", quantity: 1 }], time: "12:45" },
  { id: 3, order_no: "ORD-103", table: "T5", kitchen_status: "ready", items: [{ name: "Fried Chicken", quantity: 3 }], time: "12:30" },
  { id: 4, order_no: "ORD-104", table: "T2", kitchen_status: "pending", items: [{ name: "Chicken Salad", quantity: 1 }], time: "12:50" },
  { id: 5, order_no: "ORD-105", table: "T3", kitchen_status: "preparing", items: [{ name: "Beef Noodles", quantity: 1 }], time: "12:45" },
  { id: 6, order_no: "ORD-106", table: "T5", kitchen_status: "ready", items: [{ name: "Fried Chicken", quantity: 3 }], time: "12:30" }
]);

const columns = computed(() => [
  { title: "🆕 NEW", status: "pending", color: "bg-deep-orange-darken-2", list: orders.value.filter(o => o.kitchen_status === 'pending') },
  { title: "🔥 PREPARING", status: "preparing", color: "bg-amber-darken-3", list: orders.value.filter(o => o.kitchen_status === 'preparing') },
  { title: "✅ READY", status: "ready", color: "bg-green-darken-2", list: orders.value.filter(o => o.kitchen_status === 'ready') }
]);

const handleMove = (evt, newStatus) => {
  if (evt.added) {
    const orderId = evt.added.element.id;
    const order = orders.value.find(o => o.id === orderId);
    if (order) order.kitchen_status = newStatus;
  }
};
</script>

<style scoped>
.scroll-y {
  overflow-y: auto;
  max-height: calc(100vh - 160px);
  min-height: 500px;
}
</style>