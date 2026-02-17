<script setup>
  import { computed, ref, watch } from 'vue'

  const props = defineProps({
    modelValue: Boolean,
    table: Object
  })

  const emit = defineEmits(['update:modelValue', 'save'])

  const form = ref({
    table_number: '',
    capacity: null,
    status: 'available',
    area: '',
    is_active: true
  })

  /* Detect edit mode */
  const isEdit = computed(() => !!props.table?.id)

  /* Sync when opening */
  watch(
    () => props.table,
    val => {
      form.value = { ...val }
    },
    { immediate: true }
  )

    const reset = () => {
      form.value = {
        table_number: '',
        capacity: null,
        status: 'available',
        area: '',
        note: '',
        is_active: true
      }
    }

  const close = () => {
    emit('update:modelValue', false)
    reset()
  }

  const submit = () => {
    emit('save', form.value)
    reset()
  }
</script>

<template>
  <v-dialog
    :model-value="modelValue"
    max-width="600"
    @update:model-value="emit('update:modelValue', $event)"
  >
    <v-card class="rounded-xl">
      <v-card-title class="text-h6 font-weight-bold">
        {{ isEdit ? 'Edit Table' : 'Create Table' }}
      </v-card-title>

      <v-card-text>
        <v-row>
          <v-col col="6">
            <v-text-field
              v-model="form.table_number"
              label="Table Number"
              required
            />
          </v-col>
          <v-col col="6">
            <v-text-field
              v-model.number="form.capacity"
              type="number"
              label="Capacity"
            />
          </v-col>
        </v-row>
        <v-row>
          <v-col col="6">
            <v-text-field
              v-model="form.area"
              label="Area (Indoor, Outdoor, VIP)"
            />
          </v-col>
          <v-col col="6">
            <v-select
              v-model="form.status"
              label="Status"
              :items="['available', 'occupied', 'reserved', 'disabled']"
            />
          </v-col>
        </v-row>

        <v-switch v-model="form.is_active" label="Active" color="primary" inset/>
      </v-card-text>

      <v-card-actions>
        <v-spacer />
        <v-btn variant="text" @click="close">Cancel</v-btn>
        <v-btn color="primary" @click="submit">
          {{ isEdit ? 'Update' : 'Create' }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>
