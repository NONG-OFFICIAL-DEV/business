<script setup>
  import { ref, computed, onMounted } from 'vue'
  import { useSaleStore } from '@/stores/salePOSStore'
  import { useProductStore } from '@/stores/productStore'
  import { useCurrency } from '@/composables/useCurrency'
  import OrderCustomizationDialog from '@/components/pos/OrderCustomizationDialog.vue'
  import QRPaymentDialog from '@/components/pos/QRPaymentDialog.vue'

  const saleStore = useSaleStore()
  const productStore = useProductStore()
  const { formatCurrency } = useCurrency()

  const search = ref('')
  const paymentMethod = ref('qr')
  const cart = ref([])

  const showCustomizeDialog = ref(false)
  const selectedProduct = ref(null)
  const showQRDialog = ref(false)

  const stores = [
    { id: 1, name: 'Mart' },
    { id: 2, name: 'Coffee Store' },
    { id: 3, name: 'Restaurant' }
  ]

  const selectedStore = ref(stores[1]) // Coffee Store

  const filteredProducts = computed(() =>
    productStore.products.data?.filter(p =>
      p.name.toLowerCase().includes(search.value.toLowerCase())
    )
  )

  const subtotal = computed(() =>
    cart.value.reduce((s, i) => s + i.price * i.qty, 0)
  )
  const tax = computed(() => 0)
  const discount = computed(() => 0)
  const total = computed(() => subtotal.value + tax.value - discount.value)

  // --- Cart Actions ---
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

  function openCustomizer(product) {
    selectedProduct.value = product
    showCustomizeDialog.value = true
  }

  function handleAddCustomizedProduct(orderData) {
    const existingItem = cart.value.find(
      item =>
        item.id === orderData.id &&
        JSON.stringify(item.customizations) ===
          JSON.stringify(orderData.customizations)
    )
    if (existingItem) existingItem.qty += orderData.qty
    else cart.value.push(orderData)
  }

  // --- Checkout ---
  async function handleCheckout() {
    if (!cart.value.length) return alert('Cart is empty!')

    if (paymentMethod.value === 'qr') return (showQRDialog.value = true)
    await handleNormalCheckout()
  }

  async function handleNormalCheckout() {
    try {
      const saleData = {
        items: cart.value.map(i => ({
          product_id: i.id,
          qty: i.qty,
          price: i.price,
          customizations: i.customizations || null
        })),
        total_amount: total.value,
        payment_method: paymentMethod.value
      }

      await saleStore.checkout(saleData)
      cart.value = []
      await productStore.fetchProducts()
    } catch {
      alert('Checkout failed')
    }
  }

  // --- Fetch Products ---
  onMounted(() => productStore.fetchProducts())
</script>

<template>
  <v-layout class="bg-grey-lighten-4">
    <!-- App Bar -->
    <v-app-bar
      elevation="0"
      color="white"
      class="px-4 border-b d-flex align-center"
    >
      <!-- LOGO -->
      <div class="d-flex align-center mr-6">
        <v-icon icon="mdi-lightning-bolt" color="primary" class="mr-1" />
        <span class="font-weight-black text-h6">
          QUICK
          <span class="text-primary">POS</span>
        </span>
      </div>

      <!-- STORE SWITCH -->
      <v-menu location="bottom">
        <template #activator="{ props }">
          <v-btn
            v-bind="props"
            variant="tonal"
            color="primary"
            class="rounded-pill px-4"
            width="200"
          >
            <v-icon size="18" class="mr-2">mdi-store</v-icon>
            <span class="font-weight-bold">{{ selectedStore.name }}</span>
            <v-icon size="18" class="ml-2">mdi-chevron-down</v-icon>
          </v-btn>
        </template>

        <v-list rounded="lg">
          <v-list-item
            v-for="store in stores"
            :key="store.id"
            @click="selectedStore = store"
            :active="selectedStore.id === store.id"
          >
            <v-list-item-title class="font-weight-bold">
              {{ store.name }}
            </v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>

      <v-spacer />

      <!-- SEARCH (MAIN ACTION) -->
      <v-responsive width="250">
        <v-text-field
          v-model="search"
          prepend-inner-icon="mdi-barcode-scan"
          placeholder="Scan barcode or search product..."
          hide-details
          density="comfortable"
          variant="solo-filled"
          flat
          rounded="lg"
          autofocus
        />
      </v-responsive>

      <v-spacer />

      <!-- USER / SHIFT -->
      <div class="d-flex align-center">
        <v-chip color="success" variant="tonal" class="mr-3" size="small">
          SHIFT ACTIVE
        </v-chip>

        <div class="text-right d-none d-md-block">
          <div class="text-caption font-weight-bold">Alex Cashier</div>
          <div class="text-caption text-grey">POS Operator</div>
        </div>

        <v-avatar size="42" class="ml-3 border">
          <v-img
            src="https://api.dicebear.com/7.x/avataaars/svg?seed=Alex"
            cover
          />
        </v-avatar>

        <v-btn icon="mdi-logout" variant="text" color="error" class="ml-2" />
      </div>
    </v-app-bar>

    <!-- Sidebar / Cart -->
    <v-navigation-drawer
      location="end"
      width="380"
      permanent
      elevation="0"
      class="border-l-sm bg-grey-lighten-5"
    >
      <div class="d-flex flex-column fill-height">
        <!-- Current Order Header -->
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
          />
        </div>

        <!-- Cart Items -->
        <div class="flex-grow-1 overflow-y-auto pa-3">
          <v-card
            v-for="(item, index) in cart"
            :key="index"
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
                  <span class="text-subtitle-1 font-weight-black text-primary">
                    {{ formatCurrency(item.price * item.qty) }}
                  </span>
                </div>

                <div class="d-flex flex-wrap gap-1 mt-1">
                  <v-chip size="x-small" variant="tonal">
                    {{ item.customizations?.cup }} |
                    {{ item.customizations?.sugar }}
                  </v-chip>
                </div>

                <div class="d-flex align-center justify-space-between mt-2">
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

        <!-- Payment + Total -->
        <v-sheet elevation="16" class="pa-4 border-t" color="white">
          <v-row no-gutters class="mx-n1 mb-4">
            <v-col
              cols="4"
              v-for="method in [
                { id: 'qr', icon: 'mdi-qrcode-scan', label: 'QR' },
                { id: 'cash', icon: 'mdi-cash', label: 'Cash' },
                { id: 'card', icon: 'mdi-credit-card-outline', label: 'Debit' }
              ]"
              :key="method.id"
              class="pa-1"
            >
              <v-card
                flat
                @click="paymentMethod = method.id"
                :color="
                  paymentMethod === method.id ? 'orange-lighten-5' : 'white'
                "
                :class="[
                  'text-center py-2 rounded-lg border-sm transition-all',
                  paymentMethod === method.id
                    ? 'border-orange-darken-2'
                    : 'border-grey-lighten-2'
                ]"
              >
                <v-icon
                  :icon="method.icon"
                  size="20"
                  :color="
                    paymentMethod === method.id
                      ? 'orange-darken-2'
                      : 'grey-darken-1'
                  "
                  class="mb-1"
                />
                <div
                  :class="[
                    'text-caption font-weight-bold',
                    paymentMethod === method.id
                      ? 'text-orange-darken-2'
                      : 'text-grey-darken-1'
                  ]"
                  style="font-size: 0.65rem !important; line-height: 1"
                >
                  {{ method.label }}
                </div>
              </v-card>
            </v-col>
          </v-row>

          <div class="mb-3">
            <div class="d-flex justify-space-between text-caption mb-1">
              <span class="text-medium-emphasis">Subtotal</span>
              <span>{{ formatCurrency(subtotal) }}</span>
            </div>
            <div
              class="d-flex justify-space-between text-h6 font-weight-black border-t-sm pt-2"
            >
              <span>Total</span>
              <span class="text-primary">{{ formatCurrency(total) }}</span>
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

    <!-- Products -->
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

    <!-- Dialogs -->
    <OrderCustomizationDialog
      v-model="showCustomizeDialog"
      :product="selectedProduct"
      @add-to-cart="handleAddCustomizedProduct"
    />
    <QRPaymentDialog v-model="showQRDialog" :total="total" />
  </v-layout>
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
