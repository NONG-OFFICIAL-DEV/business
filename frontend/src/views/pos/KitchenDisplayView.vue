<template>
  <v-container fluid class="pa-0 fill-height bg-grey-darken-4">
    <v-row no-gutters class="fill-height overflow-x-auto flex-nowrap pa-4">
      <v-card 
        v-for="order in activeOrders" :key="order.id"
        width="300" 
        class="ma-2 rounded-lg d-flex flex-column"
        style="min-width: 300px"
      >
        <v-toolbar :color="getTimeColor(order.minutes)" density="compact">
          <v-toolbar-title class="text-subtitle-2 font-weight-bold">
            TABLE {{ order.table_no }} • #{{ order.id }}
          </v-toolbar-title>
          <v-spacer />
          <span class="me-2">{{ order.minutes }}m</span>
        </v-toolbar>

        <v-list class="flex-grow-1 pa-0" density="comfortable">
          <v-list-item v-for="item in order.items" :key="item.id" border="bottom">
            <template v-slot:prepend>
              <span class="font-weight-black text-h6 me-3">{{ item.qty }}x</span>
            </template>
            <v-list-item-title class="font-weight-bold">{{ item.name }}</v-list-item-title>
            <v-list-item-subtitle v-if="item.note" class="text-error font-italic">
              * {{ item.note }}
            </v-list-item-subtitle>
          </v-list-item>
        </v-list>

        <v-btn block color="success" height="60" class="rounded-0" @click="completeOrder(order.id)">
          DONE / SERVED
        </v-btn>
      </v-card>
    </v-row>
  </v-container>
</template>