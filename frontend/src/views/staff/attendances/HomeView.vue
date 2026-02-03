<template>
  <v-container class="pa-6">
    <header class="d-flex align-center justify-space-between mb-8">
      <div>
        <h4 class="text-grey-lighten-1 text-subtitle-2 mb-1">Welcome back,</h4>
        <h2 class="text-h5 font-weight-black" style="color: #1E293B;">John Doe</h2>
      </div>
      <v-avatar size="52" class="elevation-2">
        <v-img src="https://i.pravatar.cc/150?u=john"></v-img>
      </v-avatar>
    </header>

    <v-card class="main-card pa-6 mb-6" elevation="0">
      <div class="d-flex justify-space-between align-center mb-6">
        <span class="status-label">Today's Shift</span>
        <v-chip size="small" color="success" class="font-weight-bold">Active</v-chip>
      </div>

      <v-row no-gutters>
        <v-col cols="6">
          <div class="d-flex align-center mb-1">
            <v-icon size="16" color="blue" class="mr-1">mdi-clock-check</v-icon>
            <span class="card-caption">Check In</span>
          </div>
          <div class="card-time">08:30 AM</div>
        </v-col>
        <v-col cols="6" class="ps-4" style="border-left: 1px solid rgba(255,255,255,0.2)">
          <div class="d-flex align-center mb-1">
            <v-icon size="16" color="white" class="mr-1">mdi-clock-arrow-out</v-icon>
            <span class="card-caption">Check Out</span>
          </div>
          <div class="card-time opacity-50">-- : --</div>
        </v-col>
      </v-row>
    </v-card>

    <v-row>
      <v-col cols="6">
        <v-card class="stat-card pa-4" elevation="0">
          <v-icon color="blue-darken-2" class="mb-2">mdi-calendar-check</v-icon>
          <div class="stat-value">22</div>
          <div class="stat-label">Days Worked</div>
        </v-card>
      </v-col>
      <v-col cols="6">
        <v-card class="stat-card pa-4" elevation="0" style="background: #FFF7ED;">
          <v-icon color="orange-darken-2" class="mb-2">mdi-alert-circle-outline</v-icon>
          <div class="stat-value text-orange-darken-3">02</div>
          <div class="stat-label">Late Arrivals</div>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<style scoped>
.main-card {
  background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
  border-radius: 32px;
  color: white;
  box-shadow: 0 20px 30px rgba(30, 41, 59, 0.15) !important;
}

.status-label {
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1px;
  opacity: 0.7;
}

.card-caption {
  font-size: 11px;
  font-weight: 600;
  color: #94A3B8;
}

.card-time {
  font-size: 1.4rem;
  font-weight: 800;
  letter-spacing: -0.5px;
}

.stat-card {
  background: #EFF6FF;
  border-radius: 24px;
  border: 1px solid rgba(0,0,0,0.02);
}

.stat-value {
  font-size: 1.5rem;
  font-weight: 900;
  color: #1E293B;
}

.stat-label {
  font-size: 11px;
  font-weight: 700;
  color: #64748B;
  text-transform: uppercase;
}
</style>