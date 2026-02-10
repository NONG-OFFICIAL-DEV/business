<script setup>
  import { ref, computed, watch } from 'vue'
  import { useCartStore } from '@/stores/cartStore'

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
      price: 3.0,
      zone: 'province'
    },
    {
      id: 'vet',
      name: 'VET Express',
      time: '1-2 business days',
      price: 3.0,
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

  const submitOrder = () => {
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
      <div class="mx-auto" style="max-width: 600px">
        <div class="section-label mb-2 ml-1">Contact Information</div>
        <v-card flat class="rounded-xl border-light pa-4 mb-6">
          <v-text-field
            v-model="form.name"
            label="Full Name"
            variant="outlined"
            color="teal-darken-2"
            rounded="lg"
            hide-details
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
            prepend-inner-icon="mdi-phone-outline"
          />
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
              label="Select Province"
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
              label="Select District"
              variant="outlined"
              color="teal-darken-2"
              rounded="lg"
              class="mb-4"
              prepend-inner-icon="mdi-map-marker-outline"
            />
          </v-expand-transition>

          <!-- Address only for Phnom Penh -->
          <v-textarea
            v-if="form.locationType === 'pp'"
            v-model="form.address"
            label="Google Maps Location (Paste Link)"
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
                  ${{ method.price.toFixed(2) }}
                </div>
              </div>
            </v-card>
          </v-slide-y-transition>
        </v-radio-group>
      </div>
    </div>

    <v-sheet class="sticky-footer px-6 pt-5 pb-8 shadow-top">
      <div class="mx-auto" style="max-width: 600px">
        <div class="d-flex justify-space-between mb-4">
          <span class="text-grey-darken-1">Order Total</span>
          <span class="font-weight-black text-h6">
            ${{ (cartStore.cartTotal + currentDeliveryFee).toLocaleString() }}
          </span>
        </div>
        <v-btn
          block
          size="x-large"
          color="teal-darken-2"
          class="rounded-xl text-none font-weight-bold"
          elevation="4"
          :disabled="!isFormValid"
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
