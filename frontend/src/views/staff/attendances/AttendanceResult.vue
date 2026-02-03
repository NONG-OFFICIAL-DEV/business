<template>
  <v-container class="fill-height d-flex flex-column justify-center align-center">
    <v-card class="pa-4 text-center" elevation="4">
      <v-icon size="64" :color="iconColor">{{ icon }}</v-icon>
      <h3 class="my-4">{{ message }}</h3>
      <p v-if="type">Type: {{ type }}</p>
      <p v-if="time">Time: {{ time }}</p>

      <v-btn color="primary" class="mt-4" @click="goBack">
        Scan Again
      </v-btn>
    </v-card>
  </v-container>
</template>

<script setup>
import { useRoute, useRouter } from 'vue-router'
import { ref } from 'vue'

const route = useRoute()
const router = useRouter()

const status = route.query.status || 'error'
const type = route.query.type || ''
const time = new Date().toLocaleTimeString()

const message = ref(status === 'success' ? 'Attendance Recorded!' : 'Scan Failed!')
const icon = ref(status === 'success' ? 'mdi-check-circle-outline' : 'mdi-alert-circle-outline')
const iconColor = ref(status === 'success' ? 'green' : 'red')

function goBack() {
  router.push({ name: 'AttendanceScan' })
}
</script>

<style scoped>
.fill-height {
  min-height: 100vh;
  background-color: #f5f5f5;
}
</style>
