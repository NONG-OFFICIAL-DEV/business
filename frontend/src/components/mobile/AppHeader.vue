<script setup>
  defineProps({
    tableNumber: String,
    search: String
  })
</script>

<template>
  <div class="bg-white px-4 pt-4 pb-2">
    <div class="d-flex align-center mb-4">
      <v-icon color="#3b828e" class="mr-3">mdi-qrcode-scan</v-icon>
      <div>
        <div
          class="text-overline font-weight-black text-grey-lighten-1"
          style="line-height: 1"
        >
          ORDERING FROM
        </div>
        <div class="text-h6 font-weight-black" style="line-height: 1.2">
          Table {{ tableNumber }}
        </div>
      </div>
      <v-spacer />
      <v-chip
        size="x-small"
        color="success"
        variant="flat"
        class="font-weight-bold px-3"
      >
        Store Open
      </v-chip>
    </div>
  </div>
</template>

<style scoped>
  
</style>
