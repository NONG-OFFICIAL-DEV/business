<!-- <script setup>
  import { onMounted, onBeforeUnmount } from 'vue'
  import { Html5Qrcode } from 'html5-qrcode'
  import { useProductStore } from '../../stores/productStore'
  const productStore = useProductStore()

  let scanner

  const onScanSuccess = async barcode => {
    try {
      const res = await productStore.scanProduct(barcode)
      addToCart(res)
    } catch {
      alert('Product not found')
    }
  }

  const addToCart = product => {
    // your cart logic here
    console.log('Add to cart', product)
  }

  const scanByInput = async e => {
    const barcode = e.target.value
    e.target.value = ''
    onScanSuccess(barcode)
  }

  onMounted(() => {
    scanner = new Html5Qrcode('scanner')
    scanner.start(
      { facingMode: 'environment' },
      { fps: 10, qrbox: 250 },
      onScanSuccess
    )
  })

  onBeforeUnmount(() => {
    if (scanner) scanner.stop()
  })
</script>

<template>
  <input ref="barcodeInput" @keyup.enter="scanByInput" autofocus />

  <div id="scanner" style="width:100%;height:300px;"></div>
</template> -->

<script setup>
  import { ref, onMounted, onBeforeUnmount } from 'vue'
  import { Html5Qrcode } from 'html5-qrcode'
  import { useProductStore } from '@/stores/productStore'
  import { usePosStore } from '@/stores/posStore'

  const productStore = useProductStore()
  const posStore = usePosStore()
  let scanner

  // For snackbar notification
  const scanMessage = ref('')
  const showScanSnackbar = ref(false)

  const onScanSuccess = async barcode => {
    const cleanCode = barcode.trim()
    try {
      const product = await productStore.scanProduct(cleanCode)

      posStore.addToCart({
        ...product,
        qty: 1,
        customizations: {}
      })

      // Show UI feedback
      scanMessage.value = `${product.name} added to cart`
      showScanSnackbar.value = true

      // Optional beep
      const beep = new Audio('/sounds/beep.mp3')
      beep.play()
    } catch {
      scanMessage.value = `Product not found`
      showScanSnackbar.value = true
    }
  }

  onMounted(() => {
    scanner = new Html5Qrcode('scanner')
    scanner.start(
      { facingMode: 'environment' },
      {
        fps: 10,
        qrbox: { width: 320, height: 160 } // 👈 barcode shape
      },
      onScanSuccess
    )
  })

  onBeforeUnmount(() => {
    if (scanner) scanner.stop()
  })
</script>

<template>
  <div>
    <div id="scanner" class="scanner-box"></div>

    <!-- Snackbar -->
    <v-snackbar v-model="showScanSnackbar" timeout="1500" color="success">
      {{ scanMessage }}
    </v-snackbar>
  </div>
</template>
<style scoped>
  .scanner-box {
    width: 100%;
    height: 60vh; /* dynamic for phone */
    max-height: 520px;
    border-radius: 16px;
    overflow: hidden;
  }
</style>
