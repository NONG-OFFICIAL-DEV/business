<template>
  <custom-title icon="">
    Employee Management
    <template #right>
      <v-btn color="primary" @click="openDialog()">Add Employee</v-btn>
    </template>
  </custom-title>

  <v-container class="pa-0">
    <!-- <v-row class="mb-4">
      <v-col></v-col>
    </v-row> -->

    <v-data-table :headers="headers" :items="employees" item-key="id">
      <template #item.actions="{ item }">
        <v-btn icon size="small" @click="openDialog(item)">✏️</v-btn>
        <v-btn icon size="small" @click="deleteEmployee(item.id)">🗑️</v-btn>
      </template>
    </v-data-table>

    <!-- Dialog -->
    <EmployeeDialog
      v-model="dialog"
      :employee="selectedEmployee"
      @save="saveEmployee"
    />
  </v-container>
</template>

<script setup>
  import { ref } from 'vue'
  import EmployeeDialog from '@/components/EmployeeDialog.vue'

  const dialog = ref(false)
  const selectedEmployee = ref(null)

  const headers = [
    { text: 'Name', value: 'name' },
    { text: 'Email', value: 'email' },
    { text: 'Position', value: 'position' },
    { text: 'Salary', value: 'salary' },
    { text: 'Actions', value: 'actions', sortable: false }
  ]

  const employees = ref([
    {
      id: 1,
      name: 'Alexis',
      email: 'alexis@mail.com',
      position: 'Manager',
      salary: 1200
    }
  ])

  function openDialog(employee = null) {
    selectedEmployee.value = employee
    dialog.value = true
  }

  function saveEmployee(employee) {
    if (employee.id) {
      const index = employees.value.findIndex(e => e.id === employee.id)
      employees.value[index] = employee
    } else {
      employee.id = Date.now()
      employees.value.push(employee)
    }
  }

  function deleteEmployee(id) {
    employees.value = employees.value.filter(e => e.id !== id)
  }
</script>
