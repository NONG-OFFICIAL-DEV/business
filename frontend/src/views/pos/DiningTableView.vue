<template>
  <v-container fluid>
    <!-- HEADER -->
    <custom-title>
      Floor Plan
      <template #right>
        <v-chip color="success" class="me-2">
          Available: {{ availableCount }}
        </v-chip>
        <v-chip color="error" class="me-2">
          Occupied: {{ occupiedCount }}
        </v-chip>
        <v-chip color="warning">Reserved: {{ reservedCount }}</v-chip>
      </template>
    </custom-title>

    <!-- TABLES GRID -->
    <v-row dense>
      <v-col
        v-for="table in tables"
        :key="table.id"
        cols="6"
        sm="4"
        md="3"
        lg="2"
        xl="1"
      >
        <v-card
          class="rounded-xl pa-4 text-center cursor-pointer border-lg"
          :class="tableBorder(table.status)"
          elevation="0"
          @click="openTable(table)"
        >
          <v-icon size="42" :color="statusColor(table.status)">
            {{ statusIcon(table.status) }}
          </v-icon>

          <div class="text-h5 font-weight-black mt-2">T-{{ table.number }}</div>

          <div class="text-caption text-grey">
            {{ table.seats }} seats · {{ table.status.toUpperCase() }}
          </div>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
  import { ref, computed } from 'vue'

  /* -------------------------
  FAKE DATA (REALISTIC)
--------------------------*/
  const tables = ref([
    { id: 1, number: 1, seats: 4, status: 'available' },
    { id: 2, number: 2, seats: 2, status: 'occupied' },
    { id: 3, number: 3, seats: 6, status: 'occupied' },
    { id: 4, number: 4, seats: 4, status: 'available' },
    { id: 5, number: 5, seats: 8, status: 'reserved' },
    { id: 6, number: 6, seats: 4, status: 'occupied' },
    { id: 7, number: 7, seats: 2, status: 'available' },
    { id: 8, number: 8, seats: 6, status: 'available' },
    { id: 9, number: 9, seats: 4, status: 'occupied' },
    { id: 10, number: 10, seats: 2, status: 'available' },
    { id: 11, number: 11, seats: 4, status: 'available' },
    { id: 12, number: 12, seats: 6, status: 'reserved' }
  ])

  /* -------------------------
  COUNTERS
--------------------------*/
  const availableCount = computed(
    () => tables.value.filter(t => t.status === 'available').length
  )
  const occupiedCount = computed(
    () => tables.value.filter(t => t.status === 'occupied').length
  )
  const reservedCount = computed(
    () => tables.value.filter(t => t.status === 'reserved').length
  )

  /* -------------------------
  UI HELPERS
--------------------------*/
  const statusIcon = status => {
    if (status === 'occupied') return 'mdi-account-group'
    if (status === 'reserved') return 'mdi-clock-outline'
    return 'mdi-table-chair'
  }

  const statusColor = status => {
    if (status === 'occupied') return 'error'
    if (status === 'reserved') return 'warning'
    return 'success'
  }

  const tableBorder = status => {
    if (status === 'occupied') return 'border-error'
    if (status === 'reserved') return 'border-warning'
    return 'border-success'
  }

  /* -------------------------
  CLICK LOGIC
--------------------------*/
  const openTable = table => {
    if (table.status === 'available') {
      console.log('Open menu for table', table.number)
      // → open order/menu page
    } else if (table.status === 'occupied') {
      console.log('Open current bill', table.number)
      // → open checkout / add items
    } else {
      console.log('Reserved table', table.number)
    }
  }
</script>

<style scoped>
  .cursor-pointer {
    cursor: pointer;
  }
</style>
