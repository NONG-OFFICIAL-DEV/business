<script setup>
  import { ref, computed, onMounted } from 'vue'
  import DiningTableDialog from '@/components/diningTables/DiningTableDialog.vue'
  import QRCodeDialog from '../../components/diningTables/QRCodeDialog.vue'
  import { usePermission } from '@/composables/usePermission'
  import { useDiningTableStore } from '@/stores/diningTableStore'
  import { usePosStore } from '@/stores/posStore'
  import { useAppUtils } from '@/composables/useAppUtils'
  import { useI18n } from 'vue-i18n'
  const { t } = useI18n()

  // ------------------------------
  // Composables & Utils
  // ------------------------------
  const { isAdmin, isManager } = usePermission()
  const { confirm, notif } = useAppUtils()
  const posStore = usePosStore()
  const tableStore = useDiningTableStore()

  const dialog = ref(false)
  const dialogQR = ref(false)
  const tableSelect = ref()
  const editedTable = ref(null)

  /* Fetch tables */
  onMounted(() => {
    tableStore.fetchTables()
  })

  /* Open create */
  const openCreate = () => {
    editedTable.value = null
    dialog.value = true
  }
  const openQR = table => {
    dialogQR.value = true
    tableSelect.value = table
  }

  /* Open edit */
  const openEdit = table => {
    editedTable.value = { ...table }
    dialog.value = true
  }

  /* Delete */
  const deleteTable = async tableId => {
    confirm({
      title: 'Are you sure?',
      message: 'Are you sure you want to delete this Dining Table?',
      options: {
        type: 'error',
        color: 'error',
        width: 500
      },
      agree: async () => {
        await tableStore.deleteTable(tableId)
        notif(t('messages.deleted_success'), {
          type: 'success',
          color: 'primary'
        })
        await tableStore.fetchTables()
      }
    })
  }

  /* Save (create / update) */
  const saveTable = async data => {
    if (data.id) {
      await tableStore.updateTable(data)
    } else {
      await tableStore.addTable(data)
    }
    dialog.value = false
    await tableStore.fetchTables()
  }

  function openTable(table) {
    posStore.selectTable(table)
  }

  /* -------------------------
  COUNTERS
--------------------------*/
  const availableCount = computed(
    () => tableStore.tables.filter(t => t.status === 'available').length
  )
  const occupiedCount = computed(
    () => tableStore.tables.filter(t => t.status === 'occupied').length
  )
  const reservedCount = computed(
    () => tableStore.tables.filter(t => t.status === 'reserved').length
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
</script>
<template>
  <v-container fluid class="pa-0">
    <custom-title icon="mdi-table-chair">
      Floor Plan

      <template #right v-if="!isAdmin">
        <v-chip color="success" class="me-2">
          Available: {{ availableCount }}
        </v-chip>

        <v-chip color="error" class="me-2">
          Occupied: {{ occupiedCount }}
        </v-chip>

        <v-chip color="warning">Reserved: {{ reservedCount }}</v-chip>
      </template>

      <template #right v-else>
        <BaseButton
          class="ms-4"
          icon="mdi-plus"
          @click="openCreate"
          v-if="isAdmin"
        >
          Add Table
        </BaseButton>
      </template>
    </custom-title>

    <!-- <v-tabs v-model="selectedArea" color="primary" class="mb-6">
      <v-tab value="All">All Areas</v-tab>
      <v-tab v-for="area in uniqueAreas" :key="area" :value="area">
        {{ area }}
      </v-tab>
    </v-tabs> -->

    <v-row dense>
      <v-col
        v-for="table in tableStore.tables"
        :key="table.id"
        cols="6"
        sm="4"
        md="3"
        lg="2"
      >
        <v-card
          class="table-card rounded-xl border-0 overflow-hidden"
          :class="{ 'admin-mode': isAdmin }"
          elevation="0"
          :disabled="table.status === 'occupied'"
          @click="openTable(table)"
        >
          <div :class="['status-ribbon', statusColor(table.status)]"></div>

          <v-card-text class="pa-4 text-center">
            <div class="d-flex justify-space-between align-start">
              <v-icon :color="statusColor(table.status)" size="28">
                {{ statusIcon(table.status) }}
              </v-icon>
              <v-tooltip text="Capacity">
                <template v-slot:activator="{ props }">
                  <v-chip
                    v-bind="props"
                    size="small"
                    variant="tonal"
                    density="compact"
                    prepend-icon="mdi-account"
                  >
                    {{ table.capacity }}
                  </v-chip>
                </template>
              </v-tooltip>
            </div>

            <div class="text-h4 font-weight-black my-2 text-grey-darken-4">
              T-{{ table.table_number }}
            </div>

            <div
              class="text-overline font-weight-bold text-grey-darken-1 line-height-1"
            >
              {{ table.area }}
            </div>

            <div v-if="isAdmin">
              <div class="admin-actions d-flex justify-center gap-1 mt-3">
                <v-btn
                  icon="mdi-pencil"
                  size="x-small"
                  variant="flat"
                  color="white"
                  @click="openEdit(table)"
                />
                <v-btn
                  icon="mdi-qrcode"
                  size="x-small"
                  variant="flat"
                  color="white"
                  @click="openQR(table)"
                />
                <v-btn
                  icon="mdi-delete"
                  size="x-small"
                  variant="flat"
                  color="error"
                  @click="deleteTable(table.id)"
                />
              </div>
            </div>
          </v-card-text>

          <div
            :class="[
              'py-1 text-center text-caption font-weight-black text-white',
              `bg-${statusColor(table.status)}`
            ]"
          >
            {{ table.status.toUpperCase() }}
          </div>
        </v-card>
      </v-col>
    </v-row>

    <DiningTableDialog
      v-model="dialog"
      :table="editedTable"
      @save="saveTable"
    />
    <QRCodeDialog v-model="dialogQR" :tableSelect="tableSelect" />
  </v-container>
</template>

<style scoped>
  .table-card {
    position: relative;
    background: white;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid #edf2f7 !important;
  }

  .table-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05) !important;
    border-color: rgb(var(--v-theme-primary)) !important;
  }

  .status-ribbon {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
  }

  .line-height-1 {
    line-height: 1;
  }
  .gap-1 {
    gap: 4px;
  }
  .gap-2 {
    gap: 8px;
  }

  /* Styles for status colors to use in dynamic classes */
  .bg-success {
    background-color: #4caf50 !important;
  }
  .bg-error {
    background-color: #ff5252 !important;
  }
  .bg-warning {
    background-color: #fb8c00 !important;
  }

  .admin-actions {
    background: rgba(0, 0, 0, 0.05);
    padding: 8px;
    border-radius: 12px;
  }
</style>
