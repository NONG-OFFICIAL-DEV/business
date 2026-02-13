<template>
  <v-container fluid class="pa-0">
    <custom-title>
      Staff Management
      <template #right>
        <v-btn
          color="primary"
          prepend-icon="mdi-account-plus"
          rounded="lg"
          @click="openStaffDialog"
        >
          Add Staff
        </v-btn>
      </template>
    </custom-title>

    <v-container fluid class="pa-0">
      <v-row>
        <v-col
          v-for="member in staffList"
          :key="member.id"
          cols="12"
          sm="6"
          md="4"
          lg="3"
        >
          <v-card rounded="lg" border="sm" flat class="staff-card elevation-0">
            <v-card-text class="pa-4">
              <div class="d-flex align-center mb-3">
                <v-avatar size="48" class="border">
                  <v-img :src="member.avatar"></v-img>
                </v-avatar>

                <div class="ml-3 overflow-hidden">
                  <div class="text-subtitle-2 font-weight-bold text-truncate">
                    {{ member.first_name }} {{ member.last_name }}
                  </div>
                  <v-chip
                    size="x-small"
                    variant="flat"
                    :color="getRoleColor(member.role)"
                    class="mt-1 font-weight-bold text-uppercase"
                  >
                    {{ member.role }}
                  </v-chip>
                </div>

                <v-spacer></v-spacer>

                <v-menu location="end">
                  <template v-slot:activator="{ props }">
                    <v-btn
                      v-bind="props"
                      icon="mdi-dots-vertical"
                      variant="text"
                      density="comfortable"
                      size="small"
                    ></v-btn>
                  </template>

                  <v-list>
                    <v-list-item
                      v-for="(item, index) in items(member)"
                      :key="index"
                      @click="item.action()"
                    >
                      <v-list-item-title>{{ item.title }}</v-list-item-title>
                    </v-list-item>
                  </v-list>
                </v-menu>
              </div>

              <v-divider class="mb-3 border-opacity-25"></v-divider>

              <div class="d-flex justify-space-between mb-1">
                <span class="text-caption text-grey-darken-1">ID</span>
                <span class="text-caption font-weight-bold">
                  #{{ member.employee_id }}
                </span>
              </div>

              <div class="d-flex justify-space-between mb-1">
                <span class="text-caption text-grey-darken-1">Department</span>
                <span class="text-caption font-weight-medium">
                  {{ member.department }}
                </span>
              </div>

              <div class="d-flex justify-space-between align-center mt-2">
                <span class="text-caption text-grey-darken-1">Status</span>
                <v-chip
                  :color="
                    member.status === 'Active' ? 'success' : 'grey-lighten-2'
                  "
                  :text-color="
                    member.status === 'Active' ? 'white' : 'grey-darken-2'
                  "
                  size="x-small"
                  variant="flat"
                  class="font-weight-bold"
                >
                  <v-icon
                    start
                    size="12"
                    :icon="
                      member.status === 'Active'
                        ? 'mdi-circle'
                        : 'mdi-circle-outline'
                    "
                  ></v-icon>
                  {{ member.status }}
                </v-chip>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
    <StaffDialogForm
      v-model="isStaffDialogOpen"
      :initial-data="selectedStaff"
      :loading="isSaving"
      @save="handleSaveStaff"
    />
  </v-container>
</template>

<script setup>
  import { ref, computed, onMounted } from 'vue'
  import { useStaffStore } from '@/stores/employeeStore'
  import StaffDialogForm from '../../components/staffs/StaffDialogForm.vue'

  // --- Store ---
  const employeeStore = useStaffStore()

  onMounted(() => {
    employeeStore.fetchEmployees()
  })

  const items = row => [
    { title: 'Details', action: () => viewDetails(row) },
    { title: 'Update', action: () => viewDetails(row) },
    { title: 'Delete', action: () => onDeleteEmployee(row) }
  ]
  // --- Dialog & Form State ---
  const isStaffDialogOpen = ref(false)
  const selectedStaff = ref(null)
  const isSaving = ref(false)
  const searchStaff = ref('')

  // ✅ READ staff list from store (DO NOT MUTATE)
  const staffList = computed(() => employeeStore.employees)

  // --- Methods ---
  const openStaffDialog = () => {
    selectedStaff.value = null
    isStaffDialogOpen.value = true
  }

  const viewDetails = member => {
    selectedStaff.value = { ...member }
    isStaffDialogOpen.value = true
  }
  const onDeleteEmployee = member => {
    employeeStore.deleteEmployee(member.id)
  }

  // ❌ Local only (NO store update)
  const handleSaveStaff = async formData => {
    isSaving.value = true

    try {
      if (formData.id) {
        await employeeStore.updateEmployee(formData.id, formData)
      } else {
        await employeeStore.createEmployee(formData)
      }

      isStaffDialogOpen.value = false
    } catch (err) {
      console.error(err)
    } finally {
      isSaving.value = false
    }
  }


  // --- Role Colors ---
  const getRoleColor = role => {
    const map = {
      Admin: 'deep-purple',
      Staff: 'blue',
      Manager: 'teal',
      Chef: 'deep-orange'
    }
    return map[role] || 'grey'
  }
</script>

<style scoped>
  .staff-card {
    transition: all 0.3s ease;
    background: white;
  }

  .staff-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 20px rgba(0, 0, 0, 0.08) !important;
  }

  .gap-3 {
    gap: 12px;
  }

  /* Ensure iPad text doesn't wrap awkwardly */
  .leading-tight {
    line-height: 1.2;
  }
  .staff-card {
    transition:
      transform 0.2s ease,
      box-shadow 0.2s ease;
    background: white;
  }
  /* Ensure names don't break the card layout */
  .text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 120px;
  }
</style>
