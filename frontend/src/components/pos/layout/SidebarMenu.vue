<template>
  <v-navigation-drawer rail permanent>
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
  </v-navigation-drawer>
</template>

<script setup>
  defineProps({
    orderCount: { type: Number, default: 0 }
  })

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

