<template>
  <v-dialog
    v-model="internalValue"
    max-width="400"
    persistent
    transition="dialog-bottom-transition"
  >
    <v-card class="rounded-xl overflow-hidden">
      <v-toolbar color="primary" class="px-2" flat>
        <v-icon color="white" class="me-2">mdi-qrcode-scan</v-icon>
        <v-toolbar-title class="text-white font-weight-black">
          Table {{ tableSelect?.table_number }} QR
        </v-toolbar-title>
        <v-btn icon="mdi-close" color="white" variant="text" @click="close" />
      </v-toolbar>
      <v-card-text class="pa-8 text-center bg-grey-lighten-4">
        <div class="qr-wrapper bg-white pa-4 rounded-xl shadow-sm mb-6">
          <div class="qr-placeholder d-flex align-center justify-center">
            <v-icon size="200" color="grey-lighten-2">mdi-qrcode</v-icon>
          </div>
        </div>

        <div class="text-h6 font-weight-bold text-grey-darken-3">
          T-{{ tableSelect?.table_number }}
        </div>
        <p class="text-caption text-grey-darken-1">
          Scan this code to view the digital menu or place an order.
        </p>
      </v-card-text>

      <v-divider />

      <v-card-actions class="pa-4 bg-white">
        <v-btn
          variant="tonal"
          color="secondary"
          class="rounded-lg px-4 font-weight-bold"
          prepend-icon="mdi-download"
          @click="downloadQR"
        >
          Save
        </v-btn>

        <v-spacer />

        <v-btn
          variant="flat"
          color="primary"
          class="rounded-lg px-6 font-weight-bold"
          prepend-icon="mdi-printer"
          @click="printQR"
        >
          Print Label
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
  import { computed } from 'vue'

  const props = defineProps({
    modelValue: Boolean,
    tableSelect: Object
  })

  const emit = defineEmits(['update:modelValue'])

  const internalValue = computed({
    get: () => props.modelValue,
    set: val => emit('update:modelValue', val)
  })

  function close() {
    internalValue.value = false
  }

  function printQR() {
    // Logic to trigger browser print or thermal printer
    console.log('Printing QR for Table:', props.table.table_number)
  }

  function downloadQR() {
    // Logic to save the QR as PNG
    console.log('Downloading QR...')
  }
</script>

<style scoped>
  .qr-wrapper {
    border: 2px dashed #e0e0e0;
    display: inline-block;
    transition: transform 0.3s ease;
  }

  .qr-wrapper:hover {
    transform: scale(1.02);
    border-color: rgb(var(--v-theme-primary));
  }

  .qr-placeholder {
    width: 200px;
    height: 200px;
    border-radius: 12px;
  }

  /* Print Specific CSS */
  @media print {
    .v-dialog,
    .v-overlay {
      display: block !important;
      position: absolute;
      left: 0;
      top: 0;
    }
  }
</style>
