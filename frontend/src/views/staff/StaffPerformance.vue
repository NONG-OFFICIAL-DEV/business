<template>
  <v-container fluid class="pa-0">
    <custom-title>
      Staff Performance
      <template #right>
        <v-col cols="auto">
          <v-btn-toggle
            v-model="timeFrame"
            mandatory
            rounded="lg"
            color="primary"
            density="comfortable"
          >
            <v-btn value="weekly">Weekly</v-btn>
            <v-btn value="monthly">Monthly</v-btn>
          </v-btn-toggle>
        </v-col>
      </template>
    </custom-title>

    <v-row class="mb-6">
      <v-col v-for="kpi in kpis" :key="kpi.title" cols="12" sm="6" md="3">
        <v-card flat rounded="xl" class="border pa-4">
          <div class="d-flex align-center justify-space-between mb-2">
            <v-avatar :color="kpi.color + '-lighten-5'" rounded="lg" size="48">
              <v-icon :icon="kpi.icon" :color="kpi.color" />
            </v-avatar>
            <v-chip
              :color="kpi.trend > 0 ? 'success' : 'error'"
              size="x-small"
              label
              class="font-weight-bold"
            >
              {{ kpi.trend > 0 ? '+' : '' }}{{ kpi.trend }}%
            </v-chip>
          </div>
          <div class="text-h5 font-weight-black mt-2">{{ kpi.value }}</div>
          <div class="text-caption font-weight-bold text-grey">
            {{ kpi.title }}
          </div>
        </v-card>
      </v-col>
    </v-row>

    <v-row>
      <v-col cols="12" md="8">
        <v-card flat rounded="xl" class="border">
          <v-toolbar color="white" flat class="px-4">
            <v-toolbar-title class="text-subtitle-1 font-weight-bold">
              Performance Leaderboard
            </v-toolbar-title>
            <v-spacer />
            <v-btn icon="mdi-dots-vertical" variant="text" />
          </v-toolbar>

          <v-data-table :headers="headers" :items="staffData" hover>
            <template #[`item.staff`]="{ item }">
              <div class="d-flex align-center py-2">
                <v-avatar size="32" color="grey-lighten-3" class="mr-3">
                  <v-img v-if="item.avatar" :src="item.avatar" />
                  <span v-else class="text-caption font-weight-bold">
                    {{ item.name.charAt(0) }}
                  </span>
                </v-avatar>
                <div>
                  <div class="font-weight-bold">{{ item.name }}</div>
                  <div class="text-caption text-grey">{{ item.role }}</div>
                </div>
              </div>
            </template>

            <template #[`item.rating`]="{ item }">
              <v-rating
                :model-value="item.rating"
                color="amber-darken-2"
                density="compact"
                size="small"
                half-increments
                readonly
              />
            </template>

            <template #[`item.productivity`]="{ item }">
              <div class="d-flex align-center">
                <v-progress-linear
                  :model-value="item.productivity"
                  :color="getProgressColor(item.productivity)"
                  height="6"
                  rounded
                  class="mr-2"
                  style="width: 60px"
                />
                <span class="text-caption font-weight-bold">
                  {{ item.productivity }}%
                </span>
              </div>
            </template>
          </v-data-table>
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card flat rounded="xl" class="border pa-4 mb-4">
          <div class="text-subtitle-1 font-weight-bold mb-4">Top Performer</div>
          <div class="text-center py-4">
            <v-avatar size="80" class="border-lg border-primary mb-3">
              <v-img src="https://i.pravatar.cc/150?u=1" />
            </v-avatar>
            <div class="text-h6 font-weight-black">Sophea Kim</div>
            <div class="text-caption text-primary font-weight-bold">
              MVP of the Month
            </div>

            <div class="d-flex justify-space-around mt-6">
              <div>
                <div class="text-h6 font-weight-black">48</div>
                <div class="text-caption text-grey">Tasks Done</div>
              </div>
              <v-divider vertical inset />
              <div>
                <div class="text-h6 font-weight-black">4.9</div>
                <div class="text-caption text-grey">Rating</div>
              </div>
            </div>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
  import { ref } from 'vue'

  const timeFrame = ref('monthly')

  const kpis = [
    {
      title: 'Avg. Attendance',
      value: '94.2%',
      icon: 'mdi-calendar-check',
      color: 'blue',
      trend: 2.5
    },
    {
      title: 'Tasks Completed',
      value: '1,240',
      icon: 'mdi-checkbox-marked-circle-outline',
      color: 'green',
      trend: 12
    },
    {
      title: 'Customer Rating',
      value: '4.8/5',
      icon: 'mdi-star',
      color: 'amber',
      trend: -0.4
    },
    {
      title: 'Overtime Hours',
      value: '142h',
      icon: 'mdi-clock-alert-outline',
      color: 'deep-purple',
      trend: 5
    }
  ]

  const headers = [
    { title: 'Staff Member', key: 'staff' },
    { title: 'Productivity', key: 'productivity' },
    { title: 'Avg Rating', key: 'rating' },
    { title: 'Status', key: 'status' }
  ]

  const staffData = ref([
    {
      name: 'Sophea Kim',
      role: 'Head Server',
      productivity: 98,
      rating: 4.9,
      status: 'Active'
    },
    {
      name: 'Rithy Pen',
      role: 'Chef de Partie',
      productivity: 85,
      rating: 4.5,
      status: 'Active'
    },
    {
      name: 'Bopha Chan',
      role: 'Cashier',
      productivity: 92,
      rating: 4.7,
      status: 'On Break'
    },
    {
      name: 'Dara Vann',
      role: 'Waiter',
      productivity: 74,
      rating: 4.0,
      status: 'Active'
    }
  ])

  const getProgressColor = val => {
    if (val > 90) return 'success'
    if (val > 75) return 'primary'
    if (val > 50) return 'warning'
    return 'error'
  }
</script>
