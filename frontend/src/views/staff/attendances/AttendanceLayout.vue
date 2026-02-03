<template>
  <v-app class="modern-app">
    <v-main class="bg-main overflow-hidden h-100dvh">
      <router-view v-slot="{ Component }">
        <v-fade-transition mode="out-in">
          <component :is="Component" />
        </v-fade-transition>
      </router-view>
    </v-main>

    <v-bottom-navigation 
      v-model="activeTab" 
      grow 
      class="modern-nav"
      elevation="0"
    >
      <v-btn value="home" to="/attendance-layout/home" class="nav-item">
        <v-icon size="24">mdi-home-variant-outline</v-icon>
        <span class="nav-text">Home</span>
      </v-btn>

      <v-btn value="scan" to="/attendance-layout/scan" class="scan-wrapper">
        <div class="scan-fab-container">
          <div class="scan-glow"></div>
          <div class="scan-fab">
            <v-icon color="white" size="30">mdi-qrcode-scan</v-icon>
          </div>
        </div>
      </v-btn>

      <v-btn value="history" to="/attendance-layout/history" class="nav-item">
        <v-icon size="24">mdi-clock-outline</v-icon>
        <span class="nav-text">History</span>
      </v-btn>
    </v-bottom-navigation>
  </v-app>
</template>

<style scoped>
.h-100dvh {
  height: 100vh;
  height: 100dvh;
}

.modern-nav {
  position: fixed;
  bottom: 20px !important;
  left: 20px !important;
  right: 20px !important;
  width: calc(100% - 40px) !important;
  height: 72px !important;
  border-radius: 24px !important;
  background: rgba(255, 255, 255, 0.9) !important;
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.5) !important;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08) !important;
  z-index: 100;
}

.scan-wrapper {
  overflow: visible !important;
}

.scan-fab-container {
  position: absolute;
  display: flex;
  justify-content: center;
  align-items: center;
  width: 64px;
}

.scan-fab {
  width: 60px;
  height: 60px;
  /* Premium Teal Gradient from your reference images */
  background: linear-gradient(135deg, #3d808c 0%, #2a5a63 100%);
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 8px 15px rgba(61, 128, 140, 0.4);
  border: 4px solid #fff;
}

.scan-glow {
  position: absolute;
  width: 50px;
  height: 50px;
  background: #3d808c;
  filter: blur(15px);
  opacity: 0.3;
}

.nav-text {
  font-size: 10px;
  font-weight: 800;
  letter-spacing: 0.5px;
}
</style>