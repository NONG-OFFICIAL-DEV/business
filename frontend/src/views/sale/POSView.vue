<script setup>
  import { ref, computed, onMounted } from 'vue'
  import { useSaleStore } from '@/stores/salePOSStore'
  import { useProductStore } from '@/stores/productStore'
  import OrderCustomizationDialog from '../../components/pos/OrderCustomizationDialog.vue'

  const saleStore = useSaleStore()
  const productStore = useProductStore()
  const search = ref('')
  const paymentMethod = ref('qr')

  const cart = ref([])

  const filteredProducts = computed(() => {
    if (productStore.products.data) {
      return productStore.products.data.filter(p =>
        p.name.toLowerCase().includes(search.value.toLowerCase())
      )
    }
  })

  const subtotal = computed(() =>
    cart.value.reduce((s, i) => s + i.price * i.qty, 0)
  )

  const tax = computed(() => subtotal.value * 0)
  const discount = computed(() => (subtotal.value > 500 ? 0 : 0))
  const total = computed(() => subtotal.value + tax.value - discount.value)

  function addToCart(product) {
    const item = cart.value.find(i => i.id === product.id)
    if (item) item.qty++
    else cart.value.push({ ...product, qty: 1 })
  }

  function decrease(item) {
    if (item.qty > 1) item.qty--
    else cart.value = cart.value.filter(i => i.id !== item.id)
  }

  function increase(item) {
    item.qty++
  }
  function remove(item) {
    cart.value = cart.value.filter(i => i.id !== item.id)
  }

  const showQRDialog = ref(false)
  const qrCodeUrl = ref('')

  const showCustomizeDialog = ref(false)
  const selectedProduct = ref(null)

  function openCustomizer(product) {
    selectedProduct.value = product
    showCustomizeDialog.value = true
  }

  function handleAddCustomizedProduct(orderData) {
    // logic to push orderData to cart
    cart.value.push(orderData)
  }
  async function handleQRPayment() {
    try {
      // const saleData = {
      //   items: cart.value.map(i => ({
      //     product_id: i.id,
      //     qty: i.qty,
      //     price: i.price
      //   })),
      //   total: cart.value.reduce((s, i) => s + i.qty * i.price, 0),
      //   payment_method: 'qr'
      // }

      // // Step 1: Request QR code from backend
      // const res = await saleStore.createQRPayment(saleData)
      // qrCodeUrl.value = res.qr_code_url

      // // Step 2: Show modal with QR
      showQRDialog.value = true

      // // Step 3: Poll backend to check if payment completed
      // const interval = setInterval(async () => {
      //   const status = await saleStore.checkQRStatus(res.payment_id)
      //   if (status.paid) {
      //     clearInterval(interval)
      //     showQRDialog.value = false
      //     alert(`Payment completed! Sale ID: ${status.sale_id}`)
      //     cart.value = []
      //     await saleStore.fetchProducts()
      //   }
      // }, 3000) // check every 3s
    } catch {
      alert('QR Payment failed')
    }
  }

  async function handleCheckout() {
    if (!cart.value.length) return alert('Cart is empty!')

    if (paymentMethod.value === 'qr') {
      // Step 1: Generate QR payment
      await handleQRPayment()
      return
    }

    // Step 2: Normal checkout (Cash/Card)
    await handleNormalCheckout()
  }

  async function handleNormalCheckout() {
    try {
      const saleData = {
        items: cart.value.map(i => ({
          product_id: i.id,
          qty: i.qty,
          price: i.price
        })),
        total_amount: cart.value.reduce((s, i) => s + i.qty * i.price, 0),
        payment_method: paymentMethod.value
      }

      await saleStore.checkout(saleData)
      cart.value = []
      await productStore.fetchProducts() // refresh stock
    } catch {
      alert('Checkout failed')
    }
  }

  onMounted(async () => {
    await productStore.fetchProducts()
  })
</script>

<template>
  <v-layout class="bg-grey-lighten-4">
    <v-app-bar elevation="0" class="px-4 border-b" color="white">
      <div class="d-flex align-center">
        <v-icon icon="mdi-lightning-bolt" color="primary" class="mr-2" />
        <v-app-bar-title
          class="font-weight-black text-uppercase"
          style="letter-spacing: 1px"
        >
          Quick
          <span class="text-primary">POS</span>
        </v-app-bar-title>
      </div>

      <v-spacer></v-spacer>

      <v-responsive max-width="400">
        <v-text-field
          v-model="search"
          prepend-inner-icon="mdi-magnify"
          label="Search products or scan barcode..."
          hide-details
          density="compact"
          variant="solo-filled"
          flat
          rounded="pill"
        ></v-text-field>
      </v-responsive>

      <v-btn
        icon="mdi-history"
        class="ml-2"
        variant="tonal"
        color="grey-darken-1"
      ></v-btn>
      <v-btn
        icon="mdi-cog-outline"
        variant="tonal"
        color="grey-darken-1"
        class="ml-2"
      ></v-btn>
    </v-app-bar>

    <v-navigation-drawer
      location="end"
      width="380"
      permanent
      elevation="0"
      class="border-l-sm bg-grey-lighten-5"
    >
      <div class="d-flex flex-column fill-height">
        <div
          class="px-4 py-3 bg-white border-b d-flex align-center justify-space-between"
        >
          <div class="d-flex align-center">
            <v-icon
              icon="mdi-receipt-text-outline"
              color="grey-darken-1"
              class="mr-2"
              size="small"
            />
            <span class="text-subtitle-2 font-weight-black text-uppercase">
              Current Order #82
            </span>
          </div>
          <v-btn
            :disabled="!cart.length"
            variant="text"
            color="error"
            icon="mdi-close-circle-outline"
            size="small"
            @click="cart = []"
          ></v-btn>
        </div>

        <div class="flex-grow-1 overflow-y-auto pa-3">
          <v-card
            v-for="item in cart"
            :key="item.id"
            flat
            rounded="lg"
            class="mb-2 border-sm"
          >
            <div class="pa-2 d-flex align-center">
              <v-avatar size="48" rounded="md" class="bg-grey-lighten-4 border">
                <v-img :src="item.image_url" cover />
              </v-avatar>

              <div class="ml-3 flex-grow-1">
                <div class="d-flex justify-space-between align-start">
                  <span
                    class="font-weight-bold text-truncate"
                    style="max-width: 140px"
                  >
                    {{ item.name }}
                  </span>
                  <span class="font-weight-black">
                    ${{ (item.price * item.qty).toFixed(2) }}
                  </span>
                </div>

                <div class="d-flex align-center justify-space-between mt-1">
                  <span class="text-grey text-caption">${{ item.price }}</span>

                  <div
                    class="d-flex align-center bg-grey-lighten-4 rounded-pill border"
                  >
                    <v-btn
                      icon="mdi-minus"
                      variant="text"
                      size="x-small"
                      @click="decrease(item)"
                    />
                    <span class="px-2 text-caption font-weight-bold">
                      {{ item.qty }}
                    </span>
                    <v-btn
                      icon="mdi-plus"
                      variant="text"
                      size="x-small"
                      @click="increase(item)"
                    />
                  </div>
                </div>
              </div>
            </div>
          </v-card>
        </div>

        <v-sheet elevation="16" class="pa-4 border-t" color="white">
          <v-select
            v-model="paymentMethod"
            :items="[
              {
                title: 'Cash',
                value: 'cash',
                props: { prependIcon: 'mdi-cash' }
              },
              {
                title: 'Card',
                value: 'card',
                props: { prependIcon: 'mdi-credit-card' }
              },
              {
                title: 'QR Payment',
                value: 'qr',
                props: { prependIcon: 'mdi-qrcode' }
              }
            ]"
            label="Payment Method"
            density="compact"
            variant="outlined"
            rounded="lg"
            hide-details
            class="mb-4 text-caption"
            color="primary"
          >
            <template v-slot:selection="{ item }">
              <div class="d-flex align-center">
                <v-icon
                  :icon="item.props.prependIcon"
                  size="small"
                  class="mr-2"
                  color="primary"
                />
                <span class="text-caption font-weight-bold">
                  {{ item.title }}
                </span>
              </div>
            </template>
          </v-select>
          <div class="mb-3">
            <div class="d-flex justify-space-between text-caption mb-1">
              <span class="text-medium-emphasis">Subtotal</span>
              <span>${{ subtotal.toFixed(2) }}</span>
            </div>
            <div
              class="d-flex justify-space-between text-h6 font-weight-black border-t-sm pt-2"
            >
              <span>Total</span>
              <span class="text-primary">${{ total.toFixed(2) }}</span>
            </div>
          </div>

          <v-btn
            block
            height="52"
            color="primary"
            flat
            rounded="lg"
            class="text-button font-weight-black"
            :disabled="!cart.length"
            @click="handleCheckout"
          >
            COMPLETE ORDER
          </v-btn>
        </v-sheet>
      </div>
    </v-navigation-drawer>
    <v-main>
      <v-container fluid class="pa-6">
        <div class="d-flex overflow-x-auto pb-4 no-scrollbar">
          <v-chip-group mandatory selected-class="bg-primary text-white">
            <v-chip
              v-for="cat in ['All Items', 'Coffee', 'Tea', 'Pastries', 'Lunch']"
              :key="cat"
              class="px-6"
            >
              {{ cat }}
            </v-chip>
          </v-chip-group>
        </div>

        <v-row>
          <v-col
            v-for="product in filteredProducts"
            :key="product.id"
            cols="12"
            sm="6"
            md="4"
            lg="3"
            xl="2"
          >
            <v-card
              flat
              class="product-card rounded-xl"
              @click="openCustomizer(product)"
            >
              <v-img :src="product.image_url" height="180" cover>
                <div class="d-flex justify-end pa-2">
                  <v-chip
                    size="x-small"
                    color="white"
                    class="font-weight-bold"
                    variant="flat"
                  >
                    STOCK: {{ product.stock }}
                  </v-chip>
                </div>
              </v-img>

              <v-card-text class="pa-4 bg-white">
                <div
                  class="text-subtitle-2 font-weight-bold text-truncate mb-1"
                >
                  {{ product.name }}
                </div>
                <div class="text-h6 font-weight-black text-primary">
                  ${{ product.price }}
                </div>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-main>

    <v-dialog
      v-model="showQRDialog"
      max-width="450"
      transition="scale-transition"
    >
      <v-card rounded="xl" class="text-center pa-6">
        <div class="text-h5 font-weight-black mb-1">Scan to Pay</div>
        <div class="text-body-2 text-grey mb-6">
          Total Amount: ${{ total.toFixed(2) }}
        </div>

        <v-sheet border rounded="xl" class="pa-4 mx-auto mb-6" max-width="300">
          <v-img
            src="https://www.masskh.com/wp-content/uploads/2023/11/photo1700496472-568x800.jpeg"
            rounded="lg"
            width="1000px"
          />
        </v-sheet>

        <v-btn
          block
          color="primary"
          size="large"
          rounded="pill"
          @click="showQRDialog = false"
        >
          Done / Close
        </v-btn>
      </v-card>
    </v-dialog>
  </v-layout>
  <OrderCustomizationDialog
    v-model="showCustomizeDialog"
    :product="selectedProduct"
    @add-to-cart="handleAddCustomizedProduct"
  />
</template>

<style scoped>
  .product-card {
    transition: all 0.2s ease-in-out;
    border: 1px solid transparent;
    cursor: pointer;
  }

  .product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 20px rgba(0, 0, 0, 0.08) !important;
    border-color: rgb(var(--v-theme-primary));
  }

  .no-scrollbar::-webkit-scrollbar {
    display: none;
  }
  .no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
  }
</style>
