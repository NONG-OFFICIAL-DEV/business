<template>
  <div class="scanner-screen d-flex flex-column h-100">
    <div class="flex-grow-1 d-flex flex-column align-center justify-center">
      <div class="target-box">
        <div class="bracket tl"></div>
        <div class="bracket tr"></div>
        <div class="bracket bl"></div>
        <div class="bracket br"></div>
        <div class="laser"></div>
      </div>
      <p class="text-dark font-weight-bold mt-6 opacity-70">Align QR Code to scan</p>
    </div>

    <v-card class="staff-card pa-6 mx-4 mb-10 elevation-0">
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <h3 class="text-h6 font-weight-black">Staff Attendance</h3>
          <p class="text-caption text-grey">Monday, 3 Feb 2026</p>
        </div>
        <v-chip color="teal-lighten-5" class="text-teal font-weight-bold" size="small">
          <v-icon start size="14">mdi-map-marker</v-icon> GPS Active
        </v-chip>
      </div>

      <v-btn block height="54" color="#3d808c" class="text-white mb-3 rounded-xl font-weight-bold" elevation="0">
        Check In
      </v-btn>
      <v-btn block height="54" variant="outlined" color="grey-lighten-1" class="rounded-xl font-weight-bold" elevation="0">
        Check Out
      </v-btn>
    </v-card>
  </div>
</template>

<style scoped>
.target-box {
  width: 220px;
  height: 220px;
  position: relative;
}

.bracket {
  position: absolute;
  width: 30px;
  height: 30px;
  border: 4px solid #3d808c;
}
.tl { top: 0; left: 0; border-right: 0; border-bottom: 0; border-radius: 12px 0 0 0; }
.tr { top: 0; right: 0; border-left: 0; border-bottom: 0; border-radius: 0 12px 0 0; }
.bl { bottom: 0; left: 0; border-right: 0; border-top: 0; border-radius: 0 0 0 12px; }
.br { bottom: 0; right: 0; border-left: 0; border-top: 0; border-radius: 0 0 12px 0; }

.laser {
  position: absolute;
  width: 100%;
  height: 2px;
  background: #3d808c;
  box-shadow: 0 0 15px #3d808c;
  animation: scan-loop 3s infinite ease-in-out;
}

@keyframes scan-loop {
  0%, 100% { top: 0%; opacity: 0; }
  50% { top: 100%; opacity: 1; }
}

.staff-card {
  border-radius: 28px !important;
  background: white;
  z-index: 1;
}
</style>