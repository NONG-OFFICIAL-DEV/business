<script setup>
  import { ref, computed, watch } from 'vue'
  import { useCartStore } from '@/stores/cartStore'
  import { useCurrency } from '@/composables/useCurrency.js'
  const { formatCurrency } = useCurrency()

  const cartStore = useCartStore()

  const form = ref({
    phone: '',
    name: '',
    locationType: 'pp',
    province: null,
    district: null,
    address: '',
    deliveryMethod: 'standard'
  })
  const formRef = ref(null)
  const loading = ref(false)
  // --- VALIDATION RULES ---
  const rules = {
    required: v => !!v || 'This field is required',
    phone: v => {
      const pattern = /^(0\d{8,9})$/
      return pattern.test(v) || 'Enter a valid phone number (e.g., 012345678)'
    },
    googleMaps: v => {
      if (!v) return true // Address is optional/handled by rules below
      return (
        v.includes('google.com/maps') || 'Please paste a valid Google Maps link'
      )
    }
  }
  const provinces = [
    'Siem Reap',
    'Sihanoukville',
    'Battambang',
    'Kampot',
    'Kandal',
    'Takeo',
    'others...'
  ]

  // ✅ District list by province (sample for Siem Reap)
  const districtsByProvince = {
    'Siem Reap': [
      'Siem Reap City',
      'Angkor Chum',
      'Angkor Thom',
      'Banteay Srei',
      'Chi Kraeng',
      'Kralanh',
      'Puok',
      'Prasat Bakong',
      'Sout Nikom',
      'Srei Snam',
      'Svay Leu',
      'Varin'
    ],
    Sihanoukville: ['Prey Nob', 'Sihanoukville City', 'Stueng Hav'],
    Battambang: ['Battambang City', 'Banan', 'Thma Koul'],
    Kampot: ['Kampot City', 'Chhouk', 'Banteay Meas'],
    Kandal: ['Ta Khmau', 'Kien Svay', 'Muk Kampul'],
    Takeo: ['Doun Kaev', 'Tram Kak', 'Bati']
  }

  const availableDistricts = computed(() => {
    if (!form.value.province) return []
    return districtsByProvince[form.value.province] || []
  })

  const deliveryOptions = [
    {
      id: 'standard',
      name: 'Grab Delivery',
      time: '1-2 hours',
      price: 1.5,
      zone: 'pp'
    },
    {
      id: 'express',
      name: 'L192 Express',
      time: '1-2 business days',
      price: 1.25,
      zone: 'province'
    },
    {
      id: 'vet',
      name: 'VET Express',
      time: '1-2 business days',
      price: 2.25,
      zone: 'province'
    }
  ]

  const filteredOptions = computed(() => {
    return deliveryOptions.filter(
      option => option.zone === form.value.locationType
    )
  })

  // ✅ When switching location type
  watch(
    () => form.value.locationType,
    newVal => {
      if (newVal === 'pp') {
        form.value.deliveryMethod = 'standard'
        form.value.province = null
        form.value.district = null
      } else {
        form.value.deliveryMethod = 'express'
        form.value.province = null
        form.value.district = null
        form.value.address = '' // optional: clear address
      }
    }
  )

  // ✅ When province changes -> reset district
  watch(
    () => form.value.province,
    () => {
      form.value.district = null
    }
  )

  const currentDeliveryFee = computed(() => {
    const method = deliveryOptions.find(m => m.id === form.value.deliveryMethod)
    return method ? method.price : 0
  })

  const isFormValid = computed(() => {
    return (
      form.value.phone &&
      ((form.value.locationType === 'pp' && form.value.address) ||
        (form.value.locationType === 'province' &&
          form.value.province &&
          form.value.district))
    )
  })

  const submitOrder = async () => {
    loading.value = true
    const { valid } = await formRef.value.validate()

    if (valid) {
      loading.value = false
      const items = cartStore.cart.map(i => `${i.qty}x ${i.name}`).join('\n')

      const location =
        form.value.locationType === 'pp'
          ? 'Phnom Penh'
          : `${form.value.province} - ${form.value.district}`

      const addressText =
        form.value.locationType === 'pp'
          ? `Address: ${form.value.address}%0A`
          : ''

      const text =
        `New Order from App!%0A%0A` +
        `Name: ${form.value.name}%0A` +
        `Phone: ${form.value.phone}%0A` +
        `Location: ${location}%0A` +
        addressText +
        `%0AItems:%0A${items}%0A%0A` +
        `Total: $${(cartStore.cartTotal + currentDeliveryFee.value).toLocaleString()}`

      window.open(`https://wa.me/85512345678?text=${text}`, '_blank')
    } else {
      loading.value = false
      // Scroll to the first error if mobile
      window.scrollTo({ top: 0, behavior: 'smooth' })
    }
  }

  const openGoogleMaps = () => {
    window.open('https://www.google.com/maps', '_blank')
  }
</script>

<template>
  <div class="checkout-wrapper bg-slate-50">
    <v-sheet class="header-bar px-4 py-3" elevation="0">
      <div class="d-flex align-center">
        <v-btn
          icon="mdi-arrow-left"
          variant="tonal"
          size="small"
          class="rounded-xl"
          @click="$router.back()"
        />
        <v-spacer />
        <h2 class="text-subtitle-1 font-weight-bold mb-0">Delivery Details</h2>
        <v-spacer />
        <div style="width: 40px"></div>
      </div>
    </v-sheet>
    <div class="scroll-area px-4 py-6">
      <v-form ref="formRef">
        <div class="mx-auto">
          <div class="section-label mb-2 ml-1">Contact Information</div>
          <v-card flat class="rounded-xl border-light pa-0 mb-6">
            <v-card-text>
              <v-text-field
                v-model="form.name"
                variant="outlined"
                color="teal-darken-2"
                rounded="lg"
                label="Full Name"
                prepend-inner-icon="mdi-account-outline"
              />
              <v-text-field
                v-model="form.phone"
                label="Phone Number *"
                placeholder="012 345 678"
                variant="outlined"
                color="teal-darken-2"
                rounded="lg"
                class="mt-4"
                :rules="[rules.required, rules.phone]"
                type="tel"
                prepend-inner-icon="mdi-phone-outline"
              />
            </v-card-text>
          </v-card>

          <div class="section-label mb-2 ml-1">Delivery Location</div>
          <v-card flat class="rounded-xl border-light pa-4 mb-6">
            <v-btn-toggle
              v-model="form.locationType"
              mandatory
              color="teal-darken-2"
              variant="outlined"
              class="w-100 mb-4 location-toggle"
              rounded="lg"
            >
              <v-btn value="pp" class="flex-grow-1">Phnom Penh</v-btn>
              <v-btn value="province" class="flex-grow-1">Province</v-btn>
            </v-btn-toggle>

            <v-expand-transition>
              <v-select
                v-if="form.locationType === 'province'"
                v-model="form.province"
                :items="provinces"
                :rules="[rules.required]"
                label="Select Province *"
                variant="outlined"
                color="teal-darken-2"
                rounded="lg"
                class="mb-4"
                prepend-inner-icon="mdi-map-marker-outline"
              />
            </v-expand-transition>

            <!-- District (only when province selected) -->
            <v-expand-transition>
              <v-select
                v-if="form.locationType === 'province' && form.province"
                v-model="form.district"
                :items="availableDistricts"
                label="Select District *"
                variant="outlined"
                color="teal-darken-2"
                rounded="lg"
                class="mb-4"
                prepend-inner-icon="mdi-map-marker-outline"
                :rules="[rules.required]"
              />
            </v-expand-transition>

            <!-- Address only for Phnom Penh -->
            <v-textarea
              v-if="form.locationType === 'pp'"
              v-model="form.address"
              label="Google Maps Location (Paste Link) *"
              :rules="[rules.required, rules.googleMaps]"
              variant="outlined"
              color="teal-darken-2"
              rounded="lg"
              rows="2"
              hide-details
              prepend-inner-icon="mdi-map-outline"
            >
              <template #append-inner>
                <v-btn
                  icon="mdi-google-maps"
                  size="small"
                  variant="text"
                  color="teal-darken-2"
                  @click="openGoogleMaps"
                />
              </template>
            </v-textarea>
          </v-card>

          <div class="section-label mb-2 ml-1">Delivery Method</div>
          <v-radio-group v-model="form.deliveryMethod" hide-details>
            <v-slide-y-transition group>
              <v-card
                v-for="method in filteredOptions"
                :key="method.id"
                flat
                :class="[
                  'rounded-xl border-light mb-3 method-card',
                  form.deliveryMethod === method.id ? 'active-method' : ''
                ]"
                @click="form.deliveryMethod = method.id"
              >
                <div class="d-flex align-center pa-4">
                  <v-radio :value="method.id" color="teal-darken-2"></v-radio>
                  <div class="ml-2">
                    <div class="font-weight-bold">{{ method.name }}</div>
                    <div class="text-caption text-grey">{{ method.time }}</div>
                  </div>
                  <v-spacer></v-spacer>
                  <div class="font-weight-bold text-teal-darken-2">
                    {{ formatCurrency(method.price) }}
                  </div>
                </div>
              </v-card>
            </v-slide-y-transition>
          </v-radio-group>
        </div>
      </v-form>
    </div>
    <v-sheet class="sticky-footer px-6 pt-5 pb-8 shadow-top">
      <div class="mx-auto">
        <div class="d-flex justify-space-between mb-2">
          <span class="text-caption text-grey-darken-1">Order Subtotal</span>
          <span class="text-caption font-weight-bold text-black">
            {{ formatCurrency(cartStore.cartTotal) }}
          </span>
        </div>
        <div class="d-flex justify-space-between">
          <span class="text-caption text-grey-darken-1">Delivery Fee</span>
          <span class="text-caption font-weight-bold text-teal-darken-2">
            {{ formatCurrency(currentDeliveryFee) }}
          </span>
        </div>

        <v-divider class="my-3"></v-divider>

        <div class="d-flex justify-space-between align-center">
          <span class="text-subtitle-2 font-weight-black">Total Amount</span>
          <span class="text-h5 font-weight-black text-black">
            {{ formatCurrency(cartStore.cartTotal + currentDeliveryFee) }}
          </span>
        </div>
        <v-btn
          block
          size="x-large"
          color="teal-darken-2"
          class="rounded-pill text-none mt-4"
          elevation="4"
          :disabled="!isFormValid"
          :loading="loading"
          @click="submitOrder"
        >
          Confirm and Pay
        </v-btn>
      </div>
    </v-sheet>
  </div>
</template>

<style scoped>
  /* Keeping your existing styles */
  .bg-slate-50 {
    background-color: #f8fafc;
  }
  .header-bar {
    position: sticky;
    top: 0;
    z-index: 100;
    background: white !important;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05) !important;
  }
  .checkout-wrapper {
    height: 100vh;
    height: 100dvh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }
  .scroll-area {
    flex-grow: 1;
    overflow-y: auto;
  }
  .section-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 700;
    color: #64748b;
  }
  .border-light {
    border: 1px solid rgba(0, 0, 0, 0.08) !important;
  }
  .location-toggle {
    height: 48px !important;
  }
  .method-card {
    transition: all 0.2s ease;
    cursor: pointer;
    border: 2px solid transparent !important;
    background: white !important;
  }
  .active-method {
    border-color: #00796b !important;
    background: #f0fdfa !important;
  }
  .sticky-footer {
    background: white !important;
    border-top: 1px solid rgba(0, 0, 0, 0.05);
    border-radius: 32px 32px 0 0;
  }
</style>
