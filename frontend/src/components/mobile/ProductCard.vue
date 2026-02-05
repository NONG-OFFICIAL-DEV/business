<script setup>
  import { ref, computed } from 'vue'

  const props = defineProps({
    items: Array,
    cart: Array
  })

  const emit = defineEmits(['add', 'update'])

  const MAX_QTY_PER_ITEM = 5
  const snackbar = ref(false)

  // Variant Sheet
  const showVariantSheet = ref(false)
  const selectedProduct = ref(null)
  const selectedVariant = ref(null)
  const selectedVariantQty = ref(0) // track variant quantity

  // Sugar + note
  const selectedSugar = ref(100)
  const note = ref('')
  const sugarOptions = [0, 25, 50, 75, 100]

  const activeProduct = computed(() => selectedProduct.value)

  // ----------------------
  // Helpers
  // ----------------------
  const getQty = (cart, id) => cart.find(i => i.id === id)?.qty || 0

  const getProductTotalQty = product => {
    if (!product?.has_variants) return getQty(props.cart, product.id)
    return (product.variants || []).reduce(
      (sum, v) => sum + getQty(props.cart, v.id),
      0
    )
  }

  const resetVariantExtras = () => {
    selectedVariant.value = null
    selectedVariantQty.value = 0
    selectedSugar.value = 100
    note.value = ''
  }

  const openVariantPicker = product => {
    selectedProduct.value = product
    resetVariantExtras()
    showVariantSheet.value = true
  }

  const getLastVariantInCart = product => {
    if (!product?.has_variants) return null
    const variantIds = (product.variants || []).map(v => v.id)
    return (
      [...props.cart].reverse().find(i => variantIds.includes(i.id)) || null
    )
  }

  // ----------------------
  // Main actions
  // ----------------------
  const handleIncrease = product => {
    // Normal product
    if (!product.has_variants) {
      const currentQty = getQty(props.cart, product.id)

      if (currentQty >= MAX_QTY_PER_ITEM) {
        snackbar.value = true
        return
      }

      emit('add', { ...product, qty: 1 })
      return
    }

    // Variant product => ALWAYS open customize sheet
    openVariantPicker(product)
  }

  const handleDecrease = product => {
    if (!product.has_variants) {
      emit('update', product.id, -1)
      return
    }

    const lastVariant = getLastVariantInCart(product)
    if (!lastVariant) return
    emit('update', lastVariant.id, -1)
  }

  // ----------------------
  // Variant selection
  // ----------------------
  const selectVariant = variant => {
    selectedVariant.value = variant
  }

  const increaseVariantQty = () => {
    if (!selectedProduct.value) return

    const totalInCart = getProductTotalQty(selectedProduct.value)

    // total already in cart + what user is selecting now
    if (totalInCart + selectedVariantQty.value >= MAX_QTY_PER_ITEM) {
      snackbar.value = true
      return
    }

    selectedVariantQty.value++
  }

  const decreaseVariantQty = () => {
    if (selectedVariantQty.value <= 0) return
    selectedVariantQty.value--
  }

  const confirmAddVariant = () => {
    if (!selectedProduct.value || !selectedVariant.value) return
    if (selectedVariantQty.value === 0) return

    const totalInCart = getProductTotalQty(selectedProduct.value)

    if (totalInCart + selectedVariantQty.value > MAX_QTY_PER_ITEM) {
      snackbar.value = true
      return
    }

    const itemToAdd = {
      ...selectedProduct.value,
      id: selectedVariant.value.id,
      menu_id: selectedProduct.value.id,
      variant_id: selectedVariant.value.id,
      name: `${selectedProduct.value.name} (${selectedVariant.value.name})`,
      price: selectedVariant.value.price,
      size: selectedVariant.value.name,
      customizations: {
        variant_name: selectedVariant.value.name,
        sugar: selectedSugar.value
      },
      note: note.value
    }

    for (let i = 0; i < selectedVariantQty.value; i++) {
      emit('add', { ...itemToAdd, qty: 1 })
    }

    showVariantSheet.value = false
    resetVariantExtras()
  }
</script>

<template>
  <!-- Snackbar -->
  <v-snackbar
    v-model="snackbar"
    :timeout="2000"
    location="top"
    color="amber-darken-3"
    rounded="pill"
    class="mt-4"
  >
    <div class="d-flex align-center">
      <v-icon start icon="mdi-alert-circle" />
      <span>Maximum limit of {{ MAX_QTY_PER_ITEM }} reached</span>
    </div>
  </v-snackbar>

  <!-- Product Grid -->
  <v-row class="pa-2">
    <transition-group name="list-stagger">
      <v-col v-for="p in items" :key="p.id" cols="6" class="pa-2">
        <v-card flat rounded="xl" class="pa-3 product-card border">
          <v-img
            :src="p.image_url"
            aspect-ratio="1"
            cover
            class="rounded-circle mx-auto mb-3"
            width="100"
          />

          <div class="text-subtitle-2 font-weight-bold text-center mb-1">
            {{ p.name }}
          </div>

          <div class="d-flex justify-space-between align-center px-1">
            <span
              v-if="p.has_variants"
              class="text-subtitle-1 font-weight-black"
            >
              ${{ p.variants?.[0]?.price }}
            </span>
            <span v-else class="text-subtitle-1 font-weight-black">
              ${{ p.price }}
            </span>

            <!-- Add / Qty control -->
            <div v-if="getProductTotalQty(p) === 0">
              <v-btn
                icon="mdi-plus"
                size="30"
                color="primary"
                elevation="1"
                variant="flat"
                @click="handleIncrease(p)"
              />
            </div>

            <div
              v-else
              class="d-flex align-center border rounded-pill px-1 bg-grey-lighten-5"
            >
              <v-btn
                icon="mdi-minus"
                size="24"
                variant="text"
                density="comfortable"
                @click="handleDecrease(p)"
              />

              <span class="mx-2 text-caption font-weight-bold">
                {{ getProductTotalQty(p) }}
              </span>

              <v-btn
                icon="mdi-plus"
                size="24"
                variant="text"
                density="comfortable"
                color="primary"
                :class="{
                  'opacity-30': getProductTotalQty(p) >= MAX_QTY_PER_ITEM
                }"
                @click="handleIncrease(p)"
              />
            </div>
          </div>
        </v-card>
      </v-col>
    </transition-group>
  </v-row>

  <!-- Variant Sheet -->
  <v-bottom-sheet v-model="showVariantSheet">
    <v-card rounded="t-xl" class="variant-sheet px-4 pt-2 pb-6">
      <div class="d-flex justify-space-between align-center py-4">
        <div>
          <div class="text-h6 font-weight-black line-height-1">
            {{ activeProduct?.name }}
          </div>
          <div class="text-caption text-grey-darken-1">
            Choose size, sugar & note
          </div>
        </div>
        <v-btn
          icon="mdi-close"
          variant="text"
          density="comfortable"
          @click="showVariantSheet = false"
        />
      </div>

      <v-divider class="mb-5" />

      <div class="sheet-body">
        <div class="mb-6">
          <div class="d-flex justify-space-between align-center mb-3">
            <div class="text-subtitle-2 font-weight-bold">Size</div>
            <div class="text-caption text-grey-darken-1">
              Max {{ MAX_QTY_PER_ITEM }} per product
            </div>
          </div>

          <div class="modern-variant-grid">
            <v-card
              v-for="v in activeProduct?.variants"
              :key="v.id"
              class="size-card"
              :class="{ 'selected-card': selectedVariant?.id === v.id }"
              flat
              border
              rounded="xl"
              @click="selectVariant(v)"
            >
              <div class="pa-3 d-flex flex-column position-relative h-100">
                <v-icon
                  class="selection-icon"
                  :icon="
                    selectedVariant?.id === v.id
                      ? 'mdi-check-circle'
                      : 'mdi-circle-outline'
                  "
                  :color="
                    selectedVariant?.id === v.id ? 'primary' : 'grey-lighten-1'
                  "
                  size="20"
                />
                <div class="text-subtitle-2 pt-1">
                  {{ v.name }}
                </div>
                <div class="text-caption text-grey-darken-1">
                  ${{ v.price }}
                </div>
              </div>
            </v-card>
          </div>
        </div>

        <div
          v-if="selectedVariant"
          class="mb-6 d-flex align-center justify-space-between"
        >
          <div class="text-subtitle-2 font-weight-bold">Quantity</div>

          <div class="compact-qty-selector">
            <v-btn
              icon="mdi-minus"
              variant="flat"
              size="28"
              rounded="circle"
              class="bg-white elevation-1"
              :disabled="selectedVariantQty <= 0"
              @click="decreaseVariantQty"
            />
            <span class="mx-5 font-weight-black text-body-1">
              {{ selectedVariantQty }}
            </span>
            <v-btn
              icon="mdi-plus"
              variant="flat"
              size="28"
              rounded="circle"
              class="bg-primary elevation-1"
              @click="increaseVariantQty"
            />
          </div>
        </div>

        <div class="mb-6">
          <div class="text-subtitle-2 font-weight-bold mb-3">Sugar</div>
          <div class="d-flex flex-wrap ga-2">
            <v-chip
              v-for="s in sugarOptions"
              :key="s"
              filter
              variant="flat"
              :color="selectedSugar === s ? 'primary' : 'grey-lighten-4'"
              class="px-4"
              @click="selectedSugar = s"
            >
              {{ s }}%
            </v-chip>
          </div>
        </div>

        <div class="mb-8">
          <div class="text-subtitle-2 font-weight-bold mb-3">Note</div>
          <v-textarea
            v-model="note"
            rows="1"
            auto-grow
            variant="outlined"
            rounded="xl"
            placeholder="Example: less ice, extra hot, no straw..."
            hide-details
            color="primary"
            bg-color="grey-lighten-5"
            class="custom-textarea"
          />
        </div>
      </div>

      <v-btn
        block
        size="large"
        rounded="xl"
        color="primary"
        elevation="0"
        class="text-none font-weight-bold py-6"
        :disabled="!selectedVariant || selectedVariantQty === 0"
        @click="confirmAddVariant"
      >
        CONFIRM ORDER
      </v-btn>
    </v-card>
  </v-bottom-sheet>
</template>

<style scoped>
  .opacity-30 {
    opacity: 0.3;
  }
  .v-row {
    width: 100%;
    margin: 0 !important;
  }
  .product-card {
    background: white;
    transition: transform 0.2s ease;
  }
  .product-card:active {
    transform: scale(0.98);
  }

  /* Compact Qty Selector */
  .compact-qty-selector {
    background-color: #f5f5f5;
    border-radius: 50px;
    padding: 4px;
    display: flex;
    align-items: center;
  }
  .line-height-1 {
    line-height: 1.2;
  }

  /* 3 Column Grid */
  .modern-variant-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
  }

  .size-card {
    border: 1.5px solid #e0e0e0 !important;
    transition: all 0.2s ease;
    background-color: transparent;
  }

  .selected-card {
    border: 2px solid var(--v-primary-base, #4db6ac) !important;
    background-color: #f1f8f7 !important; /* Very light primary tint */
  }

  .selection-icon {
    position: absolute;
    top: 10px;
    right: 10px;
  }

  /* Modern Qty Bar */
  .modern-qty-bar {
    background-color: #f5f5f5;
    border-radius: 100px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 6px;
    max-width: 100%;
  }

  .custom-textarea :deep(.v-field__outline) {
    --v-field-border-opacity: 0.1;
  }

  .v-btn--disabled {
    opacity: 0.4;
  }
</style>
