<template>
  <v-dialog v-model="show" max-width="700px" scrollable>
    <v-card rounded="lg">
      <v-card-title class="d-flex align-center bg-primary">
        <v-icon class="mr-3">mdi-account-plus</v-icon>
        <span class="text-h6 font-weight-black">
          {{ isEdit ? 'Update Staff Member' : 'Add New Staff' }}
        </span>
        <v-spacer></v-spacer>
        <v-btn icon="mdi-close" variant="text" @click="show = false"></v-btn>
      </v-card-title>

      <v-card-text class="pa-6">
        <v-form ref="formRef" v-model="isFormValid">
          <div class="text-overline mb-4 text-primary font-weight-bold">Personal Information</div>
          <v-row>
            <v-col cols="12" sm="6">
              <v-text-field
                v-model="form.first_name"
                label="First Name *"
                variant="outlined"
                density="comfortable"
                :rules="[v => !!v || 'Required']"
              ></v-text-field>
            </v-col>
            <v-col cols="12" sm="6">
              <v-text-field
                v-model="form.last_name"
                label="Last Name *"
                variant="outlined"
                density="comfortable"
                :rules="[v => !!v || 'Required']"
              ></v-text-field>
            </v-col>
            <v-col cols="12" sm="6">
              <v-select
                v-model="form.gender"
                :items="['Male', 'Female', 'Other']"
                label="Gender"
                variant="outlined"
                density="comfortable"
              ></v-select>
            </v-col>
            <v-col cols="12" sm="6">
              <v-text-field
                v-model="form.dob"
                label="Date of Birth"
                type="date"
                variant="outlined"
                density="comfortable"
              ></v-text-field>
            </v-col>
          </v-row>

          <div class="text-overline my-4 text-primary font-weight-bold">Employment Details</div>
          <v-row>
            <v-col cols="12" sm="6">
              <v-text-field
                v-model="form.joining_date"
                label="Joining Date"
                type="date"
                variant="outlined"
                density="comfortable"
              ></v-text-field>
            </v-col>
            <v-col cols="12" sm="6">
              <v-text-field
                v-model="form.job_title"
                label="Job Title"
                variant="outlined"
                density="comfortable"
              ></v-text-field>
            </v-col>
            <v-col cols="12" sm="6">
              <v-select
                v-model="form.department"
                :items="['Kitchen', 'Service', 'Management', 'Delivery']"
                label="Department"
                variant="outlined"
                density="comfortable"
              ></v-select>
            </v-col>
            <v-col cols="12" sm="6">
              <v-select
                v-model="form.role"
                :items="['Admin', 'Staff', 'Manager']"
                label="System Role"
                variant="outlined"
                density="comfortable"
              ></v-select>
            </v-col>
            <v-col cols="12" sm="6">
              <v-select
                v-model="form.status"
                :items="['Active', 'On Leave', 'Terminated']"
                label="Employment Status"
                variant="outlined"
                density="comfortable"
              ></v-select>
            </v-col>
          </v-row>

          <div class="text-overline my-4 text-primary font-weight-bold">Contact & Emergency</div>
          <v-row>
            <v-col cols="12" sm="6">
              <v-text-field
                v-model="form.email"
                label="Email Address"
                type="email"
                variant="outlined"
                density="comfortable"
                prepend-inner-icon="mdi-email-outline"
              ></v-text-field>
            </v-col>
            <v-col cols="12" sm="6">
              <v-text-field
                v-model="form.phone"
                label="Phone Number"
                variant="outlined"
                density="comfortable"
                prepend-inner-icon="mdi-phone"
              ></v-text-field>
            </v-col>
            <v-col cols="12" sm="6">
                <v-text-field
                v-model="form.emergency_name"
                label="Emergency Contact Name"
                variant="outlined"
                density="comfortable"
                prepend-inner-icon="mdi-account-alert"
                ></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-textarea
                v-model="form.address"
                label="Residential Address"
                variant="outlined"
                density="comfortable"
                rows="2"
              ></v-textarea>
            </v-col>
          </v-row>
        </v-form>
      </v-card-text>

      <v-divider></v-divider>

      <v-card-actions class="px-4">
        <v-btn
          variant="text"
          class="text-none font-weight-bold"
          @click="show = false"
        >
          Cancel
        </v-btn>
        <v-spacer></v-spacer>
        <v-btn
          color="primary"
          variant="flat"
          class="text-none font-weight-bold"
          :loading="loading"
          :disabled="!isFormValid"
          @click="submit"
        >
          {{ isEdit ? 'Save Changes' : 'Register Staff' }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { ref, reactive, watch, computed } from 'vue'

const props = defineProps({
  modelValue: Boolean,
  initialData: Object,
  loading: Boolean
})

const emit = defineEmits(['update:modelValue', 'save'])

const show = computed({
  get: () => props.modelValue,
  set: val => emit('update:modelValue', val)
})

const isEdit = computed(() => !!props.initialData?.id)
const isFormValid = ref(false)
const formRef = ref(null)

const defaultForm = {
  id: null,
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  gender: 'Male',
  dob: '',
  job_title: '',
  department: '',
  role: 'Staff',
  employee_id: '',
  joining_date: new Date().toISOString().slice(0, 10),
  status: 'Active',
  address: '',
  emergency_name: '',
  emergency_phone: ''
}

const form = reactive({ ...defaultForm })

watch(
  () => props.initialData,
  newVal => {
    if (newVal) {
      Object.assign(form, defaultForm, newVal)
    } else {
      Object.assign(form, defaultForm)
    }
  },
  { immediate: true }
)

const submit = async () => {
  const { valid } = await formRef.value.validate()
  if (valid) {
    emit('save', { ...form })
  }
}
</script>

<style scoped>
.text-primary {
  color: #3b828e !important;
}
</style>