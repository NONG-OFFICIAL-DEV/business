<template>
  <v-container fluid class="pa-0">
    <custom-title icon="mdi-sale">
      Sales Report
      <template #right>
        <BaseButtonFilter class="me-4" @click="toggleFilterForm" />
        <v-btn color="primary" prepend-icon="mdi-download" @click="exportData">
          Export CSV
        </v-btn>
      </template>
    </custom-title>
    <!-- Filters users actually expect -->
    <v-card border flat class="pa-4 mb-4" v-show="showFilterForm">
      <v-row>
        <v-col cols="12" md="3">
          <v-text-field
            v-model="filters.from"
            type="date"
            label="From"
            density="compact"
          />
        </v-col>
        <v-col cols="12" md="3">
          <v-text-field
            v-model="filters.to"
            type="date"
            label="To"
            density="compact"
          />
        </v-col>
      </v-row>
    </v-card>

    <v-row>
      <v-col v-for="card in metrics" :key="card.title" cols="12" sm="6" md="3">
        <v-card border flat class="pa-4">
          <div class="text-overline mb-1">{{ card.title }}</div>
          <div class="text-h5 font-weight-black">{{ card.value }}</div>
          <v-chip :color="card.trendColor" size="x-small" class="mt-2">
            {{ card.trend }}
          </v-chip>
        </v-card>
      </v-col>

      <v-col cols="12">
        <v-card border flat class="pa-4">
          <v-card-title>Monthly Revenue Trend</v-card-title>
          <div style="height: 300px">
            <Line :data="chartData" :options="chartOptions" />
          </div>
        </v-card>
      </v-col>

      <v-col cols="12">
        <v-card border flat>
          <v-card-title class="d-flex align-center pe-2">
            Recent Transactions
            <v-spacer></v-spacer>
            <v-text-field
              v-model="search"
              density="compact"
              label="Search orders..."
              prepend-inner-icon="mdi-magnify"
              variant="solo-filled"
              flat
              hide-details
              single-line
            ></v-text-field>
          </v-card-title>
          <v-data-table
            :headers="headers"
            :items="tableData.data"
            :search="search"
            hover
            density="compact"
          >
            <template v-slot:item.sale_date="{ item }">
              {{ formatDate(item.sale_date) }}
            </template>
            <template v-slot:item.total_amount="{ item }">
              {{ formatCurrency(item.total_amount) }}
            </template>
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>
    <v-row>
      Top Selling Items (To be implemented)
    </v-row>
    <v-row>
      Top Menu Ordered (To be implemented)
    </v-row>
  </v-container>
</template>

<script setup>
  import { ref, onMounted, reactive } from 'vue'
  import { useSaleStore } from '../../stores/salePOSStore'
  import { Line } from 'vue-chartjs'
  import { useCurrency } from '@/composables/useCurrency'
  import { useDate } from '@/composables/useDate'

  import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    LineElement,
    CategoryScale,
    LinearScale,
    PointElement
  } from 'chart.js'

  ChartJS.register(
    Title,
    Tooltip,
    Legend,
    LineElement,
    CategoryScale,
    LinearScale,
    PointElement
  )
  const { formatCurrency } = useCurrency()
  const { formatDate } = useDate()

  // Search logic
  const search = ref('')
  const saleStore = useSaleStore()
  const tableData = ref([])
  const filters = reactive({ search: '', status: null, from: null, to: null })
  const showFilterForm = ref(false)

  onMounted(async () => {
    // Fetch sales report data
    const reportData = await saleStore.saleReport()
    saleStore.topMenusReport(filters.from, filters.to)
    tableData.value = reportData.table_data
  })
  const toggleFilterForm = () => {
    showFilterForm.value = !showFilterForm.value
  }
  // Summary Metrics
  const metrics = [
    {
      title: 'Total Revenue',
      value: '$128,430',
      trend: '+12% vs last month',
      trendColor: 'success'
    },
    {
      title: 'Total Orders',
      value: '1,240',
      trend: '+5% vs last month',
      trendColor: 'success'
    }
  ]

  // Table Configuration
  const headers = [
    { title: 'Order ID', key: 'id' },
    { title: 'Date', key: 'sale_date' },
    { title: 'Amount', key: 'total_amount' }
    // { title: 'Status', key: 'status' }
  ]

  // Chart Logic
  const chartData = {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
    datasets: [
      {
        label: 'Revenue',
        backgroundColor: '#1867C0',
        borderColor: '#1867C0',
        data: [40, 39, 10, 40, 39, 80],
        tension: 0.4
      }
    ]
  }

  const chartOptions = { responsive: true, maintainAspectRatio: false }

  // Helper functions
  const getStatusColor = status => {
    if (status === 'Completed') return 'success'
    if (status === 'Pending') return 'warning'
    return 'grey'
  }

  const exportData = () => {
    alert('Exporting data to CSV...')
  }
</script>
