<template>
  <v-container fluid class="pa-0">
    <custom-title icon="mdi-sale">
      Sales Report
      <template #right>
        <v-btn color="primary" prepend-icon="mdi-download" @click="exportData">
          Export CSV
        </v-btn>
      </template>
    </custom-title>
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
          >
            <template v-slot:item.status="{ value }">
              <v-chip :color="getStatusColor(value)" size="small">
                {{ value }}
              </v-chip>
            </template>
            <template v-slot:item.amount="{ value }">
              ${{ value }}
            </template>
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
  import { ref, onMounted, watch } from 'vue'
  import { useSaleStore } from '../../stores/salePOSStore'
  import { Line } from 'vue-chartjs'
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

  // Search logic
  const search = ref('')
  const saleStore = useSaleStore()
  const tableData = ref([])

  onMounted(async () => {
    // Fetch sales report data
    const reportData = await saleStore.saleReport()
    tableData.value = reportData.table_data
  })
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
    },
    {
      title: 'Avg. Order Value',
      value: '$103.50',
      trend: '-2% vs last month',
      trendColor: 'error'
    },
    {
      title: 'Conversion Rate',
      value: '3.4%',
      trend: '+0.4%',
      trendColor: 'success'
    }
  ]

  // Table Configuration
  const headers = [
    { title: 'Order ID', key: 'id' },
    { title: 'Date', key: 'sale_date' },
    { title: 'Amount', key: 'total_amount' },
    { title: 'Status', key: 'status' }
  ]

  const salesData = ref([
    {
      id: '#1024',
      date: '2026-01-18',
      amount: 150.0,
      status: 'Completed'
    },
    {
      id: '#1025',
      date: '2026-01-19',
      amount: 85.5,
      status: 'Pending'
    },
    {
      id: '#1026',
      date: '2026-01-20',
      amount: 2100.0,
      status: 'Completed'
    }
    // Add more rows as needed
  ])

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
