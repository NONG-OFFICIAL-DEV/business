<template>
  <div class="cart-anchor px-4">
    <v-card
      flat
      rounded="pill"
      class="shadow-top bg-teal-darken-4 text-white pa-2 d-flex align-center justify-space-between"
    >
      <v-btn
        v-for="item in menuItems"
        :key="item.value"
        icon
        variant="text"
        :to="item.to"
        class="nav-btn"
        :class="{ 'active-item': isCurrentRoute(item.to) }"
      >
        <v-icon size="26" v-if="item.to != '/cashier'">{{ item.icon }}</v-icon>

        <v-tooltip activator="parent" location="top">
          {{ item.tooltip }}
        </v-tooltip>
        <v-badge
          v-if="item.to == '/cashier'"
          :content="orderCount"
          color="error"
          overlap
          offset-x="3"
          offset-y="3"
        >
          <v-icon size="26">{{ item.icon }}</v-icon>
        </v-badge>
      </v-btn>
    </v-card>
  </div>
</template>

<script setup>
  import { useRoute } from 'vue-router'

  const route = useRoute()

  defineProps({
    orderCount: { type: Number, default: 0 }
  })

  const isCurrentRoute = path => route.path === path

  const menuItems = [
    {
      tooltip: 'Table',
      icon: 'mdi-apps',
      value: 'Dining Table',
      to: '/dining-table-view'
    },
    { tooltip: 'Menu', icon: 'mdi-food', value: 'Menu List', to: '/menu-list' },
    {
      tooltip: 'Kitchen',
      icon: 'mdi-silverware',
      value: 'Kitchen Display',
      to: '/kds'
    },
    {
      tooltip: 'Orders',
      icon: 'mdi-cash-register',
      value: 'Cashier',
      to: '/cashier'
    },
    {
      tooltip: 'Reports',
      icon: 'mdi-chart-bar',
      value: 'Reports',
      to: '/reports'
    }
  ]
</script>

<style scoped>
  .cart-anchor {
    position: fixed;
    bottom: 20px;
    left: 0;
    right: 0;
    z-index: 1000;
    max-width: 500px;
    margin: 0 auto;
  }

  .shadow-top {
    box-shadow: 0 10px 30px rgba(0, 77, 64, 0.4) !important;
    background: linear-gradient(145deg, #004d40, #002d2d) !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
  }

  .nav-btn {
    transition: all 0.3s ease;
    opacity: 0.7;
  }

  .active-item {
    opacity: 1 !important;
    color: #4db6ac !important; /* Lighter teal for the active icon */
    transform: translateY(-2px);
  }

  .active-item::after {
    content: '';
    position: absolute;
    bottom: 4px;
    width: 4px;
    height: 4px;
    background-color: currentColor;
    border-radius: 50%;
  }
</style>
<!-- <v-navigation-drawer rail permanent>
    <v-list nav>
      <v-tooltip
        v-for="item in menuItems"
        :key="item.value"
        :text="item.tooltip"
        location="right"
      >
        <template v-slot:activator="{ props }">
          <v-list-item
            v-bind="props"
            :prepend-icon="item.icon"
            :value="item.value"
            :to="item.to"
          />
        </template>
      </v-tooltip>
    </v-list>
  </v-navigation-drawer> -->
