<script setup>
  import { ref, computed, onMounted, watch } from 'vue'
  import CustomerOrdersDialog from '../../components/backOffice/CustomerOrdersDialog.vue'

  import { useAppUtils } from '@/composables/useAppUtils'
  import { useDate } from '@/composables/useDate'
  import { useCurrency } from '@/composables/useCurrency'
  import { useI18n } from 'vue-i18n'

  const { confirm, notif } = useAppUtils()
  const { formatDate } = useDate()
  const { formatCurrency } = useCurrency()
  const { t } = useI18n()

  // ------------------------------
  // STATE
  // ------------------------------
  const isLoading = ref(true)
  const orders = ref([])

  const search = ref('')
  const selectedStatus = ref('All')
  const dateFrom = ref(null)
  const dateTo = ref(null)

  const page = ref(1)
  const itemsPerPage = ref(10)
  const sortBy = ref([{ key: 'date', order: 'desc' }])

  // dialogs
  const isDetailsOpen = ref(false)
  const isEditOpen = ref(false)
  const isDeleteOpen = ref(false)

  const selectedOrder = ref(null)
  const isSaving = ref(false)

  // ------------------------------
  // TABLE HEADERS
  // ------------------------------
  const headers = [
    { title: 'Order ID', key: 'id', align: 'start' },
    { title: 'Customer', key: 'customer' },
    { title: 'Date', key: 'date' },
    { title: 'Amount', key: 'amount', align: 'end' },
    { title: 'Status', key: 'status', align: 'center' },
    { title: 'Actions', key: 'actions', sortable: false, align: 'end' }
  ]

  // ------------------------------
  // HELPERS
  // ------------------------------
  const statusOptions = ['All', 'Pending', 'Completed', 'Cancelled']

  const getStatusColor = status => {
    if (status === 'Completed') return 'success'
    if (status === 'Pending') return 'warning'
    if (status === 'Cancelled') return 'error'
    return 'grey'
  }

  const formatMoney = value => {
    // value could be "$120.00" or number
    if (typeof value === 'string') return value
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'USD'
    }).format(value)
  }

  const normalizeAmount = amount => {
    // "$1,200.00" => 1200
    if (typeof amount === 'number') return amount
    return Number(String(amount).replace(/[^0-9.]/g, '')) || 0
  }

  const parseDate = dateStr => {
    // "2024-05-01"
    const d = new Date(dateStr)
    return isNaN(d.getTime()) ? null : d
  }

  // ------------------------------
  // MOCK API
  // ------------------------------
  const fetchOrders = async () => {
    isLoading.value = true

    // simulate API delay
    await new Promise(r => setTimeout(r, 1200))

    orders.value = [
      {
        id: 'ORD-001',
        customer: 'John Doe',
        date: '2024-05-01',
        amount: '$120.00',
        status: 'Completed',
        phone: '+855 12 345 678',
        address: 'Phnom Penh',
        items: [
          { name: 'Coca Cola', qty: 2, price: 1.5 },
          { name: 'Burger', qty: 1, price: 4.2 }
        ],
        note: 'Deliver ASAP',
        paymentMethod: 'Cash'
      },
      {
        id: 'ORD-002',
        customer: 'Jane Smith',
        date: '2024-05-02',
        amount: '$85.50',
        status: 'Pending',
        phone: '+855 77 111 222',
        address: 'Siem Reap',
        items: [{ name: 'Pizza', qty: 2, price: 8.5 }],
        note: '',
        paymentMethod: 'ABA'
      },
      {
        id: 'ORD-003',
        customer: 'Robert Brown',
        date: '2024-05-02',
        amount: '$210.00',
        status: 'Completed',
        phone: '+855 10 222 333',
        address: 'Battambang',
        items: [
          { name: 'Chicken', qty: 3, price: 6.0 },
          { name: 'Rice', qty: 3, price: 1.0 }
        ],
        note: 'No spicy',
        paymentMethod: 'Cash'
      },
      {
        id: 'ORD-004',
        customer: 'Alice Wong',
        date: '2024-05-03',
        amount: '$45.00',
        status: 'Cancelled',
        phone: '+855 88 999 000',
        address: 'Phnom Penh',
        items: [{ name: 'Noodles', qty: 2, price: 2.0 }],
        note: 'Customer cancelled',
        paymentMethod: 'Card'
      },
      {
        id: 'ORD-005',
        customer: 'Tech Solutions Inc',
        date: '2024-05-04',
        amount: '$1,200.00',
        status: 'Pending',
        phone: '+855 23 555 444',
        address: 'Phnom Penh',
        items: [
          { name: 'Office Chair', qty: 4, price: 100 },
          { name: 'Desk', qty: 2, price: 250 }
        ],
        note: 'Invoice required',
        paymentMethod: 'Bank Transfer'
      }
    ]

    isLoading.value = false
  }

  onMounted(fetchOrders)

  // ------------------------------
  // FILTERING
  // ------------------------------
  const filteredOrders = computed(() => {
    let data = [...orders.value]

    // status filter
    if (selectedStatus.value !== 'All') {
      data = data.filter(o => o.status === selectedStatus.value)
    }

    // search filter
    if (search.value.trim()) {
      const q = search.value.toLowerCase().trim()
      data = data.filter(o => {
        return (
          o.id.toLowerCase().includes(q) ||
          o.customer.toLowerCase().includes(q) ||
          String(o.phone || '')
            .toLowerCase()
            .includes(q)
        )
      })
    }

    // date range filter
    if (dateFrom.value) {
      const from = new Date(dateFrom.value)
      data = data.filter(o => {
        const d = parseDate(o.date)
        return d && d >= from
      })
    }

    if (dateTo.value) {
      const to = new Date(dateTo.value)
      to.setHours(23, 59, 59, 999)

      data = data.filter(o => {
        const d = parseDate(o.date)
        return d && d <= to
      })
    }

    return data
  })

  // reset page when filters change
  watch([search, selectedStatus, dateFrom, dateTo], () => {
    page.value = 1
  })

  // ------------------------------
  // STATS
  // ------------------------------
  const totalRevenue = computed(() => {
    return filteredOrders.value
      .filter(o => o.status === 'Completed')
      .reduce((sum, o) => sum + normalizeAmount(o.amount), 0)
  })

  const totalOrders = computed(() => filteredOrders.value.length)

  const pendingCount = computed(
    () => filteredOrders.value.filter(o => o.status === 'Pending').length
  )

  // ------------------------------
  // ACTIONS
  // ------------------------------
  const openDetails = order => {
    selectedOrder.value = order
    isDetailsOpen.value = true
  }

  const openEdit = order => {
    selectedOrder.value = { ...order } // copy
    isEditOpen.value = true
  }

  const saveEdit = async () => {
    if (!selectedOrder.value) return

    isSaving.value = true
    try {
      // simulate API
      await new Promise(r => setTimeout(r, 800))

      // update local list
      const idx = orders.value.findIndex(o => o.id === selectedOrder.value.id)
      if (idx !== -1) {
        orders.value[idx] = {
          ...orders.value[idx],
          status: selectedOrder.value.status,
          note: selectedOrder.value.note
        }
      }

      isEditOpen.value = false
    } finally {
      isSaving.value = false
    }
  }

  const confirmDelete = async item => {
    confirm({
      title: 'Delete Customer Order',
      message: `Are you sure you want to delete ${item.id || 'this product'}?`,
      options: { type: 'error', width: 500 },
      agree: async () => {
        try {
          orders.value = orders.value.filter(
            o => o.id !== selectedOrder.value.id
          )
          notif(t('messages.deleted_success'), { type: 'success' })
          fetchData()
        } catch (error) {
          // Dynamic error handling
          const errorMessage =
            error.response?.data?.message || 'An unexpected error occurred.'
          notif(errorMessage, { type: 'error' }) // Changed to error type for visibility
        }
      }
    })
  }

  const clearFilters = () => {
    search.value = ''
    selectedStatus.value = 'All'
    dateFrom.value = null
    dateTo.value = null
  }

  const exportCSV = () => {
    const rows = filteredOrders.value.map(o => ({
      id: o.id,
      customer: o.customer,
      phone: o.phone,
      date: o.date,
      amount: normalizeAmount(o.amount),
      status: o.status
    }))

    const header = Object.keys(rows[0] || {}).join(',')
    const body = rows
      .map(r =>
        Object.values(r)
          .map(v => `"${String(v).replace(/"/g, '""')}"`)
          .join(',')
      )
      .join('\n')

    const csv = [header, body].filter(Boolean).join('\n')

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' })
    const url = URL.createObjectURL(blob)

    const a = document.createElement('a')
    a.href = url
    a.download = `customer_orders_${new Date().toISOString().slice(0, 10)}.csv`
    a.click()

    URL.revokeObjectURL(url)
  }
</script>

<template>
  <v-container fluid class="pa-0">
    <!-- Header -->
    <custom-title icon="mdi-office-building-cog">
      Back Office - Customer Orders

      <template #right>
        <div class="d-flex ga-2">
          <v-btn
            variant="tonal"
            prepend-icon="mdi-refresh"
            @click="fetchOrders"
          >
            Refresh
          </v-btn>

          <v-btn
            variant="tonal"
            prepend-icon="mdi-file-export-outline"
            @click="exportCSV"
          >
            Export CSV
          </v-btn>

          <v-btn color="primary" prepend-icon="mdi-plus">Create Order</v-btn>
        </div>
      </template>
    </custom-title>

    <!-- Stats -->
    <v-row class="mt-2" dense>
      <v-col cols="12" md="4">
        <v-card variant="outlined" class="rounded-lg">
          <v-card-text class="d-flex align-center justify-space-between">
            <div>
              <div class="text-caption text-medium-emphasis">Total Orders</div>
              <div class="text-h6 font-weight-bold">{{ totalOrders }}</div>
            </div>
            <v-icon size="28">mdi-format-list-bulleted</v-icon>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card variant="outlined" class="rounded-lg">
          <v-card-text class="d-flex align-center justify-space-between">
            <div>
              <div class="text-caption text-medium-emphasis">Pending</div>
              <div class="text-h6 font-weight-bold">{{ pendingCount }}</div>
            </div>
            <v-icon size="28">mdi-clock-outline</v-icon>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card variant="outlined" class="rounded-lg">
          <v-card-text class="d-flex align-center justify-space-between">
            <div>
              <div class="text-caption text-medium-emphasis">
                Completed Revenue
              </div>
              <div class="text-h6 font-weight-bold">
                {{ formatMoney(totalRevenue) }}
              </div>
            </div>
            <v-icon size="28">mdi-cash-check</v-icon>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Filters -->
    <v-card variant="outlined" class="rounded-lg mt-4">
      <v-card-text>
        <v-row dense>
          <v-col cols="12" md="4">
            <v-text-field
              v-model="search"
              label="Search order, customer, phone..."
              prepend-inner-icon="mdi-magnify"
              variant="outlined"
              density="comfortable"
              hide-details
            />
          </v-col>

          <v-col cols="12" md="3">
            <v-select
              v-model="selectedStatus"
              :items="statusOptions"
              label="Status"
              prepend-inner-icon="mdi-filter-outline"
              variant="outlined"
              density="comfortable"
              hide-details
            />
          </v-col>

          <v-col cols="12" md="2">
            <v-text-field
              v-model="dateFrom"
              label="From"
              type="date"
              variant="outlined"
              density="comfortable"
              hide-details
            />
          </v-col>

          <v-col cols="12" md="2">
            <v-text-field
              v-model="dateTo"
              label="To"
              type="date"
              variant="outlined"
              density="comfortable"
              hide-details
            />
          </v-col>

          <v-col cols="12" md="1" class="d-flex align-center">
            <v-btn variant="text" icon="mdi-close" @click="clearFilters" />
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <!-- TABLE -->
    <v-card variant="outlined" class="rounded-lg mt-4">
      <v-data-table
        :headers="headers"
        :items="filteredOrders"
        :loading="isLoading"
        :page="page"
        :items-per-page="itemsPerPage"
        :sort-by="sortBy"
        hover
      >
        <template #loading>
          <div class="pa-4">
            <v-skeleton-loader type="table" />
          </div>
        </template>

        <template #item.status="{ item }">
          <v-chip
            :color="getStatusColor(item.status)"
            size="small"
            class="text-uppercase font-weight-bold"
          >
            {{ item.status }}
          </v-chip>
        </template>

        <template #item.amount="{ item }">
          <div class="text-right font-weight-bold">
            {{ item.amount }}
          </div>
        </template>

        <template #item.actions="{ item }">
          <div class="d-flex justify-end ga-1">
            <v-btn
              icon="mdi-eye-outline"
              variant="text"
              size="small"
              @click="openDetails(item)"
            />

            <v-btn
              icon="mdi-pencil-outline"
              variant="text"
              size="small"
              color="blue"
              @click="openEdit(item)"
            />

            <v-btn
              icon="mdi-delete-outline"
              variant="text"
              size="small"
              color="red"
              @click="confirmDelete(item)"
            />
          </div>
        </template>

        <template #no-data>
          <div class="pa-6 text-center text-medium-emphasis">
            No orders found.
          </div>
        </template>
      </v-data-table>
    </v-card>

    <CustomerOrdersDialog
      v-model="isDetailsOpen"
      type="details"
      :order="selectedOrder"
    />

    <CustomerOrdersDialog
      v-model="isEditOpen"
      type="edit"
      :order="selectedOrder"
      :isSaving="isSaving"
      @save="saveEdit"
    />
  </v-container>
</template>

<style scoped>
  .quantity-stepper {
    height: 30px;
  }
</style>
