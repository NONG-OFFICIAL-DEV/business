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
import { onMounted, onBeforeUnmount } from 'vue'
import { Html5Qrcode } from 'html5-qrcode'
import { useProductStore } from '@/stores/productStore'
import { usePosStore } from '@/stores/posStore'

const productStore = useProductStore()
const posStore = usePosStore()
let scanner

const onScanSuccess = async (barcode) => {
  try {
    const product = await productStore.scanProduct(barcode)

    // Add scanned product to cart with qty = 1
    posStore.addToCart({
      ...product,
      qty: 1,
      customizations: {}
    })

    // Optional: beep sound for feedback
    const beep = new Audio('/sounds/beep.mp3') // add beep.mp3 in public folder
    beep.play()

  } catch {
    alert('Product not found')
  }
}

onMounted(() => {
  scanner = new Html5Qrcode('scanner')
  scanner.start(
    { facingMode: 'environment' }, // use back camera
    { fps: 10, qrbox: 250 },       // scan every 100ms, 250px box
    onScanSuccess
  )
})

onBeforeUnmount(() => {
  if (scanner) scanner.stop()
})
</script>
<template>
  <div id="scanner" style="width:100%;height:300px;"></div>
</template>