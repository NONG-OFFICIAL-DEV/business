<script setup>
  import { ref, watch } from 'vue'

  const props = defineProps({
    modelValue: Boolean,
    editMode: Boolean,
    item: Object
  })

  const emit = defineEmits(['update:modelValue', 'save'])

  const getDefaultForm = () => ({
    name: '',
    category: 'Coffee',
    description: '',
    is_available: true,
    image: null,
    image_url: '',
    sizes: [
      { name: 'Small', price: 0 },
      { name: 'Medium', price: 0 },
      { name: 'Large', price: 0 }
    ]
  })

  const form = ref(getDefaultForm())
  const imagePreview = ref(null)
  const image = ref(null)

  watch(
    () => props.modelValue,
    isOpen => {
      if (!isOpen) return

      if (props.editMode && props.item) {
        form.value = JSON.parse(JSON.stringify(props.item))
        imagePreview.value = props.item.image_url || null
      } else {
        form.value = getDefaultForm()
        imagePreview.value = null
      }
    }
  )

  function handleImageUpload(file) {
    // const image = file?.[0]
    // if (!image) return

    // form.value.image = image
    // imagePreview.value = URL.createObjectURL(image)
    if (!file) {
      imagePreview.value = null
      return
    }

    // Vuetify may return File OR File[]
    const fileExist = Array.isArray(file) ? file[0] : file

    // Extra safety
    if (!(fileExist instanceof File)) {
      imagePreview.value = null
      return
    }

    imagePreview.value = URL.createObjectURL(file)
  }

  function addSize() {
    form.value.sizes.push({ name: '', price: 0 })
  }

  function removeSize(index) {
    form.value.sizes.splice(index, 1)
  }

  function close() {
    emit('update:modelValue', false)
  }

  function save() {
    const payload = {
      ...form.value,
      price: form.value.sizes[0]?.price || 0,
      image: image.value
    }

    emit('save', payload)
    close()
  }
</script>
<template>
  <v-dialog
    :model-value="modelValue"
    @update:model-value="val => emit('update:modelValue', val)"
    max-width="850"
    persistent
  >
    <v-card rounded="xl" class="pa-2">
      <v-card-title class="font-weight-black text-h5 d-flex align-center pa-4">
        <v-icon icon="mdi-food" class="mr-2" color="primary" />
        {{ editMode ? 'Edit Menu Item' : 'New Menu Item' }}
      </v-card-title>

      <v-card-text class="pa-4">
        <v-row no-gutters>
          <v-col cols="12" md="4" class="pr-md-6 border-e">
            <div class="d-flex flex-column align-center">
              <v-avatar
                size="200"
                rounded="xl"
                class="mb-4 border-sm bg-grey-lighten-4"
              >
                <v-img v-if="imagePreview" :src="imagePreview" cover />
                <v-icon v-else size="48" color="grey-lighten-1">
                  mdi-image-outline
                </v-icon>
              </v-avatar>

              <v-file-input
                v-model="image"
                label="Upload Image"
                accept="image/*"
                variant="solo-filled"
                flat
                density="compact"
                rounded="lg"
                prepend-icon=""
                prepend-inner-icon="mdi-camera"
                class="w-100 mb-2"
                hide-details
                @update:modelValue="handleImageUpload"
              >
                <template v-slot:selection="{ fileNames }">
                  <template v-for="fileName in fileNames" :key="fileName">
                    <v-chip
                      size="small"
                      label
                      color="primary"
                      class="text-truncate"
                    >
                      {{ fileName }}
                    </v-chip>
                  </template>
                </template>
              </v-file-input>

              <v-switch
                v-model="form.is_available"
                color="success"
                label="Available"
                inset
                density="compact"
                hide-details
                class="mt-2 font-weight-bold"
              />
            </div>
          </v-col>

          <v-col cols="12" md="8" class="pl-md-6">
            <v-row density="compact">
              <v-col cols="8">
                <v-text-field
                  v-model="form.name"
                  label="Menu Name"
                  variant="outlined"
                  rounded="lg"
                  hide-details
                  class="mb-3"
                />
              </v-col>
              <v-col cols="4">
                <v-select
                  v-model="form.category"
                  :items="['Coffee', 'Tea', 'Pastries', 'Lunch']"
                  label="Category"
                  variant="outlined"
                  rounded="lg"
                  hide-details
                />
              </v-col>
              <v-col cols="12">
                <v-textarea
                  v-model="form.description"
                  label="Description"
                  rows="2"
                  variant="outlined"
                  rounded="lg"
                  hide-details
                />
              </v-col>
            </v-row>

            <div class="mt-4">
              <div class="d-flex align-center justify-space-between mb-2">
                <div
                  class="text-subtitle-2 font-weight-bold text-grey-darken-2"
                >
                  PRICING BY SIZE
                </div>
                <v-btn
                  variant="text"
                  color="primary"
                  size="small"
                  prepend-icon="mdi-plus"
                  @click="addSize"
                  class="font-weight-black"
                >
                  Add Size
                </v-btn>
              </div>

              <v-row dense v-for="(size, index) in form.sizes" :key="index">
                <v-col cols="12" md="12" class="d-flex align-center">
                  <v-text-field
                    v-model="size.name"
                    label="Size Name"
                    variant="outlined"
                    rounded="lg"
                    hide-details
                    class="flex-grow-1 me-3"
                    density="compact"
                  />
                  <v-text-field
                    v-model="size.price"
                    label="Price"
                    type="number"
                    variant="outlined"
                    rounded="lg"
                    hide-details
                    class="flex-grow-1 me-3"
                    density="compact"
                  />
                  <v-btn
                    icon="mdi-close-circle"
                    variant="text"
                    color="error"
                    size="small"
                    @click="removeSize(index)"
                    v-if="form.sizes.length > 1"
                  />
                </v-col>
              </v-row>
            </div>
          </v-col>
        </v-row>
      </v-card-text>

      <v-divider />
      <v-card-actions class="pa-4">
        <v-btn
          variant="text"
          color="grey-darken-1"
          rounded="lg"
          @click="close"
          class="px-6"
        >
          Cancel
        </v-btn>
        <v-spacer />
        <v-btn
          color="primary"
          variant="flat"
          rounded="lg"
          width="180"
          height="44"
          class="font-weight-black"
          @click="save"
        >
          SAVE CHANGES
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<style scoped>
  /* Prevents the select and input fields from pushing out the card width */
  .v-text-field :deep(input) {
    font-size: 0.9rem;
  }

  /* Custom scrollbar for sizes section if it ever overflows */
  .bg-grey-lighten-5::-webkit-scrollbar {
    width: 4px;
  }
  .bg-grey-lighten-5::-webkit-scrollbar-thumb {
    background: #e0e0e0;
    border-radius: 10px;
  }
</style>
