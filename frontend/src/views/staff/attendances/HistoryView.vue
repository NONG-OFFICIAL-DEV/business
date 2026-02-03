<template>
  <v-container class="pa-4">
    <h2 class="text-h6 font-weight-black mb-4">Attendance History</h2>
    
    <v-list class="bg-transparent">
      <v-card v-for="n in 5" :key="n" class="mb-3 rounded-xl" elevation="0">
        <v-list-item class="py-3">
          <template v-slot:prepend>
            <div class="date-box mr-3">
              <div class="text-caption font-weight-bold">FEB</div>
              <div class="text-h6 font-weight-black">0{{ n }}</div>
            </div>
          </template>

          <v-list-item-title class="font-weight-bold">Monday</v-list-item-title>
          <v-list-item-subtitle class="text-caption">
            <v-icon size="14" color="success">mdi-clock-in</v-icon> 08:30 - 
            <v-icon size="14" color="error">mdi-clock-out</v-icon> 17:30
          </v-list-item-subtitle>

          <template v-slot:append>
            <v-chip size="x-small" color="success" variant="flat">On Time</v-chip>
          </template>
        </v-list-item>
      </v-card>
    </v-list>
  </v-container>
</template>

<style scoped>
.date-box {
  background: #f1f5f9;
  padding: 4px 12px;
  border-radius: 12px;
  text-align: center;
  min-width: 60px;
}
</style>