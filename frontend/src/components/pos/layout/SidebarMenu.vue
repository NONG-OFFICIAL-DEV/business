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
    <template v-slot:append>
      <v-list nav>
        <v-menu
          v-model="menu"
          :close-on-content-click="false"
          location="end"
          offset="15"
        >
          <template v-slot:activator="{ props: menuProps }">
            <v-tooltip text="Settings" location="right">
              <template v-slot:activator="{ props: tooltipProps }">
                <v-list-item
                  v-bind="mergeProps(menuProps, tooltipProps)"
                  prepend-icon="mdi-cog"
                  value="settings"
                  :active="menu"
                />
              </template>
            </v-tooltip>
          </template>

          <v-card min-width="250" rounded="lg" elevation="10" class="border">
            <v-list density="compact">
              <v-list-item
                title="App Settings"
                subtitle="Preferences"
                class="mb-2"
              >
                <template v-slot:prepend>
                  <v-icon icon="mdi-cog" color="primary"></v-icon>
                </template>
              </v-list-item>

              <v-divider></v-divider>

              <v-list-item
                v-for="s in posStore.stores"
                :key="s.id"
                @click="selectStore(s)"
                :active="store.id === s.id"
              >
                <v-list-item-title>
                  {{ s.name }}
                </v-list-item-title>
              </v-list-item>
            </v-list>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn variant="text" size="small" @click="menu = false">
                Close
              </v-btn>
              <v-btn
                color="primary"
                variant="flat"
                size="small"
                rounded="lg"
                @click="menu = false"
              >
                Save
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-menu>
      </v-list>
    </template>
  </v-navigation-drawer>
</template>

<script setup>
  import { ref, mergeProps, computed } from 'vue'
  import { usePosStore } from '@/stores/posStore'
  import { usePermission } from '@/composables/usePermission'
  const posStore = usePosStore()
  const { isAdmin, isManager } = usePermission()

  function selectStore(store) {
    posStore.selectStore(store)
  }
  const store = computed(() => posStore.selectedStore)

  const menu = ref(false)
  const message = ref(true)
  const hints = ref(true)
  defineProps({
    orderCount: { type: Number, default: 0 }
  })

  const menuItems = [
    {
      tooltip: 'Table',
      icon: 'mdi-apps',
      value: 'Dining Table',
      to: '/pos/dining-table-view'
    },
    {
      tooltip: 'Menu',
      icon: 'mdi-food',
      value: 'Menu List',
      to: '/pos/menu-list'
    },
    {
      tooltip: 'Kitchen',
      icon: 'mdi-silverware',
      value: 'Kitchen Display',
      to: '/pos/kds'
    },
    {
      tooltip: 'Orders',
      icon: 'mdi-cash-register',
      value: 'Cashier',
      to: '/pos/cashier'
    },
    {
      tooltip: 'Reports',
      icon: 'mdi-chart-bar',
      value: 'Reports',
      to: '/pos/reports'
    }
  ]
</script>
