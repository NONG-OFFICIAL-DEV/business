<template>
  <v-dialog v-model="model" max-width="750" persistent>
    <v-card rounded="xl" class="pa-2">
      <!-- Header -->
      <v-card-title class="d-flex align-center justify-space-between">
        <div class="d-flex align-center">
          <v-icon class="mr-2" color="primary">mdi-store-plus</v-icon>
          <span class="font-weight-black text-h6">Create Store</span>
        </div>

        <v-btn icon="mdi-close" variant="text" @click="close" />
      </v-card-title>

      <v-divider />

      <!-- Body -->
      <v-card-text class="pt-5">
        <v-form ref="formRef" v-model="isValid">
          <v-row>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.name"
                label="Store Name"
                placeholder="Phnom Penh Branch"
                variant="outlined"
                rounded="lg"
                :rules="[rules.required]"
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-file-input
                v-model="form.logo"
                label="Store Logo"
                accept="image/*"
                variant="outlined"
                rounded="lg"
                density="comfortable"
                prepend-icon=""
                append-inner-icon="mdi-image"
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.phone"
                label="Phone"
                placeholder="012 345 678"
                variant="outlined"
                rounded="lg"
              />
            </v-col>

            <v-col cols="12">
              <v-textarea
                v-model="form.address"
                label="Address"
                placeholder="Street, Building, etc..."
                variant="outlined"
                rounded="lg"
                rows="2"
              />
            </v-col>

            <v-col cols="12" md="4">
              <v-text-field
                v-model="form.city"
                label="City"
                variant="outlined"
                rounded="lg"
              />
            </v-col>

            <v-col cols="12" md="4">
              <v-text-field
                v-model="form.province"
                label="Province"
                variant="outlined"
                rounded="lg"
              />
            </v-col>

            <v-col cols="12" md="4">
              <v-text-field
                v-model="form.country"
                label="Country"
                variant="outlined"
                rounded="lg"
              />
            </v-col>

            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.opening_time"
                label="Opening Time"
                type="time"
                variant="outlined"
                rounded="lg"
              />
            </v-col>

            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.closing_time"
                label="Closing Time"
                type="time"
                variant="outlined"
                rounded="lg"
              />
            </v-col>

            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.latitude"
                label="Latitude"
                type="number"
                variant="outlined"
                rounded="lg"
              />
            </v-col>

            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.longitude"
                label="Longitude"
                type="number"
                variant="outlined"
                rounded="lg"
              />
            </v-col>

            <v-col cols="12">
              <v-switch
                label="Active"
                v-model="form.status"
                color="primary"
                true-value="active"
                fale-value="inctive"
                inset
              ></v-switch>
            </v-col>
          </v-row>
        </v-form>
      </v-card-text>

      <!-- Footer -->
      <v-divider />

      <v-card-actions class="pa-4">
        <v-btn variant="outlined" rounded="xl" class="text-none" @click="close">
          Cancel
        </v-btn>

        <v-spacer />

        <v-btn
          color="primary"
          rounded="xl"
          class="text-none font-weight-black px-6"
          :loading="loading"
          :disabled="!isValid"
          @click="submit"
        >
          Create Store
          <v-icon end>mdi-check</v-icon>
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
  import { ref, reactive, watch } from 'vue'

  const props = defineProps({
    modelValue: Boolean,
    store: { type: Object, default: null } // existing store for edit, or null for create
  })

  const emit = defineEmits(['update:modelValue', 'created'])
  const model = ref(false)
  const formRef = ref(null)
  const isValid = ref(false)
  const loading = ref(false)

  // reactive form
  const form = ref({
    id: null,
    code: '',
    name: '',
    phone: '',
    address: '',
    city: '',
    province: '',
    country: 'Cambodia',
    latitude: null,
    longitude: null,
    opening_time: null,
    closing_time: null,
    status: 'active',
    logo: null
  })

  const resetForm = () => {
    form.value.code = ''
    form.value.name = ''
    form.value.phone = ''
    form.value.address = ''
    form.value.city = ''
    form.value.province = ''
    form.value.country = 'Cambodia'
    form.value.latitude = null
    form.value.longitude = null
    form.value.opening_time = null
    form.value.closing_time = null
    form.value.status = 'active'
    form.value.logo = null
  }
  // update dialog open/close
  watch(
    () => props.modelValue,
    val => (model.value = val)
  )
  watch(model, val => emit('update:modelValue', val))

  // update form when editing
  watch(
  () => props.store,
  newVal => {
    if (newVal) {
      form.value.id = newVal.id
      form.value.code = newVal.code
      form.value.name = newVal.name
      form.value.phone = newVal.phone
      form.value.address = newVal.address
      form.value.city = newVal.city
      form.value.province = newVal.province
      form.value.country = newVal.country || 'Cambodia'
      form.value.latitude = newVal.latitude
      form.value.longitude = newVal.longitude
      form.value.opening_time = newVal.opening_time
      form.value.closing_time = newVal.closing_time
      form.value.status = newVal.status
      form.value.logo = null            // user can upload a new file
      form.value.logo_url = newVal.logo_url  // display current logo
    }
  },
  { immediate: true }
)


  // emit dialog open/close
  watch(model, val => emit('update:modelValue', val))

  const rules = {
    required: v => !!v || 'Required'
  }

  const close = () => {
    model.value = false
    resetForm()
    formRef.value?.resetValidation()
  }

  const submit = async () => {
    const valid = await formRef.value?.validate()
    if (!valid?.valid) return

    loading.value = true
    try {
      // emit payload to parent for create or update
      emit('created', { ...form })
      close()
    } finally {
      loading.value = false
    }
  }
</script>
