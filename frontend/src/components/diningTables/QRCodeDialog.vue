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
            <div v-if="qrCode" v-html="qrCode" class="mx-auto"></div>
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
  import { ref, computed, watch } from 'vue'
  import { useDiningTableStore } from '../../stores/diningTableStore'

  const diningTables = useDiningTableStore()
  const props = defineProps({
    modelValue: Boolean,
    tableSelect: Object
  })

  const emit = defineEmits(['update:modelValue'])

  const internalValue = computed({
    get: () => props.modelValue,
    set: val => emit('update:modelValue', val)
  })

  const qrCode = ref(null)

  // Watch tableSelect and fetch QR when table changes
  watch(
    () => props.tableSelect, // <- plain getter
    async newTable => {
      // <- async callback is fine
      if (newTable?.id) {
        try {
          const res = await diningTables.showQRCode(newTable.id) // call your service
          qrCode.value = res // make sure to use res.data (SVG from Laravel)
        } catch (err) {
          console.error('Failed to fetch QR code', err)
        }
      }
    },
    { immediate: true }
  )
  function close() {
    internalValue.value = false
  }

  function printQR() {
    // Logic to trigger browser print or thermal printer
    console.log('Printing QR for Table:', props.table.table_number)
  }

  async function downloadQR() {
    if (!qrCode.value) return

    const svgString = qrCode.value

    // 1) Convert SVG string to Blob
    const svgBlob = new Blob([svgString], {
      type: 'image/svg+xml;charset=utf-8'
    })

    const url = URL.createObjectURL(svgBlob)

    // 2) Create Image
    const img = new Image()

    img.onload = () => {
      // 3) Create canvas
      const canvas = document.createElement('canvas')
      const size = 500 // PNG size
      canvas.width = size
      canvas.height = size

      const ctx = canvas.getContext('2d')

      // White background
      ctx.fillStyle = '#ffffff'
      ctx.fillRect(0, 0, size, size)

      // Draw SVG into canvas
      ctx.drawImage(img, 0, 0, size, size)

      // 4) Convert canvas to PNG and download
      canvas.toBlob(blob => {
        const pngUrl = URL.createObjectURL(blob)

        const link = document.createElement('a')
        link.href = pngUrl
        link.download = `table-${props.tableSelect?.table_number}-qr.png`
        link.click()

        URL.revokeObjectURL(pngUrl)
      }, 'image/png')

      URL.revokeObjectURL(url)
    }

    img.onerror = err => {
      console.error('QR PNG download failed', err)
      URL.revokeObjectURL(url)
    }

    img.src = url
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
