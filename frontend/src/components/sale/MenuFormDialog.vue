<script setup>
  import { ref, watch, computed } from 'vue'

  /* ================= PROPS / EMITS ================= */
  const props = defineProps({
    modelValue: Boolean,
    editMode: Boolean,
    item: Object,
    categories: Object
  })

  const emit = defineEmits(['update:modelValue', 'save'])

  /* ================= CONSTANTS ================= */
  const SIZE_CATEGORIES = ['Coffee', 'Tea']

  /* ================= DEFAULT FORM ================= */
  const getDefaultForm = () => ({
    id: null,
    name: '',
    menu_category_id: null,
    description: '',
    is_available: true,
    variants: [],
    price: 0
  })

  const form = ref(getDefaultForm())
  const image = ref(null)
  const imagePreview = ref(null)

  /* ================= WATCHERS ================= */

  // Open dialog
  watch(
    () => props.modelValue,
    open => {
      if (!open) return

      if (props.editMode && props.item) {
        form.value = JSON.parse(JSON.stringify(props.item))
        imagePreview.value = props.item.image_url || null
      } else {
        form.value = getDefaultForm()
        imagePreview.value = null
      }
    }
  )

  // Category → auto manage sizes
  watch(
    () => form.value.menu_category_id,
    menu_category_id => {
      const category = props.categories.find(c => c.id === menu_category_id)
      if (SIZE_CATEGORIES.includes(category?.name)) {
        if (form.value.variants.length === 0) {
          form.value.variants = [
            { name: 'Small', price: 0 },
            { name: 'Medium', price: 0 },
            { name: 'Large', price: 0 }
          ]
        }
      } else {
        form.value.variants = []
      }
    },
    { immediate: true }
  )

  /* ================= METHODS ================= */
  function handleImageUpload(file) {
    if (!file) {
      imagePreview.value = null
      return
    }

    const realFile = Array.isArray(file) ? file[0] : file
    if (!(realFile instanceof File)) return

    imagePreview.value = URL.createObjectURL(realFile)
  }
  // Computed property for better performance and readability
  const showSizeOptions = computed(() => {
    return SIZE_CATEGORIES.includes(
      props.categories.find(c => c.id === form.value.menu_category_id)?.name
    )
  })

  function addSize() {
    form.value.variants.push({ name: '', price: 0 })
  }

  function removeSize(index) {
    form.value.variants.splice(index, 1)
  }

  function close() {
    emit('update:modelValue', false)
  }

  function save() {
    const hasVariants =
      SIZE_CATEGORIES.includes(
        props.categories.find(c => c.id === form.value.menu_category_id)?.name
      ) && form.value.variants.length > 0

    const payload = {
      id: form.value.id,
      name: form.value.name,
      menu_category_id: form.value.menu_category_id,
      description: form.value.description,
      is_available: form.value.is_available,
      has_variants: hasVariants,
      price: hasVariants ? 0 : Number(form.value.price || 0),
      sizes: hasVariants
        ? form.value.variants.map(v => ({
            name: v.name,
            price: Number(v.price)
          }))
        : [],
      image: image.value
    }

    emit('save', payload)
    close()
  }
</script>

<template>
  <v-dialog :model-value="modelValue" max-width="850" persistent>
    <v-card rounded="lg">
      <v-card-title class="d-flex align-center bg-primary">
        <span>
          {{ editMode ? 'Edit Menu Item' : 'New Menu Item' }}
        </span>

        <v-spacer></v-spacer>

        <v-btn
          icon="mdi-close"
          variant="text"
          size="small"
          color="white"
          @click="close"
        ></v-btn>
      </v-card-title>
      <v-card-text>
        <v-row>
          <!-- IMAGE -->
          <v-col cols="12" md="4" class="text-center">
            <v-hover v-slot="{ isHovering, props }">
              <v-card
                v-bind="props"
                :elevation="isHovering ? 4 : 0"
                class="mx-auto cursor-pointer position-relative mb-4 bg-grey-lighten-4 d-flex align-center justify-center"
                rounded="xl"
                height="220"
                @click="$refs.fileInput.click()"
              >
                <v-img
                  v-if="imagePreview"
                  :src="imagePreview"
                  cover
                  class="fill-height"
                />
                <div v-else class="d-flex flex-column align-center">
                  <v-icon size="40" color="grey-darken-1">
                    mdi-camera-plus-outline
                  </v-icon>
                  <span class="text-caption mt-2">Upload Photo</span>
                </div>

                <v-overlay
                  :model-value="isHovering"
                  contained
                  scrim="black"
                  class="align-center justify-center"
                >
                  <v-btn size="small" variant="flat">Change Image</v-btn>
                </v-overlay>
              </v-card>
            </v-hover>

            <v-file-input
              ref="fileInput"
              v-model="image"
              class="d-none"
              accept="image/*"
              @update:modelValue="handleImageUpload"
            />

            <v-sheet
              border
              rounded="lg"
              class="pa-2 d-flex align-center justify-space-between"
            >
              <span class="text-body-2 font-weight-medium px-2">
                Visibility
              </span>
              <v-switch
                v-model="form.is_available"
                hide-details
                color="success"
                inset
                density="compact"
              ></v-switch>
            </v-sheet>
          </v-col>

          <!-- FORM -->
          <v-col cols="12" md="8">
            <v-text-field v-model="form.name" label="Name">
              <template #label>
                Menu name
                <span class="required-star">*</span>
              </template>
            </v-text-field>
            <v-select
              v-model="form.menu_category_id"
              :items="categories"
              item-title="name"
              item-value="id"
            >
              <template #label>
                Menu category
                <span class="required-star">*</span>
              </template>
            </v-select>

            <v-textarea
              v-model="form.description"
              label="Description"
              rows="2"
            />

            <!-- VARIANTS -->
            <v-col cols="12" class="pa-0" v-if="showSizeOptions">
              <div class="d-flex align-center justify-space-between mb-2">
                <div class="d-flex align-center">
                  <v-icon color="primary" class="mr-2">mdi-currency-usd</v-icon>
                  <span class="text-subtitle-1 font-weight-bold">
                    Pricing by Size
                  </span>
                </div>
                <v-btn
                  prepend-icon="mdi-plus"
                  variant="tonal"
                  color="primary"
                  size="small"
                  rounded="pill"
                  @click="addSize"
                >
                  Add Size
                </v-btn>
              </div>

              <v-sheet border rounded="lg" class="pa-4 bg-grey-lighten-5">
                <v-row
                  v-if="form.variants.length > 0"
                  no-gutters
                  class="mb-2 px-1 text-caption text-grey-darken-1 font-weight-bold"
                >
                  <v-col cols="6">SIZE NAME</v-col>
                  <v-col cols="4" class="pl-2">PRICE</v-col>
                  <v-col cols="2"></v-col>
                </v-row>

                <v-row
                  v-for="(v, i) in form.variants"
                  :key="i"
                  align="center"
                  class="mb-2"
                  dense
                >
                  <v-col cols="6">
                    <v-text-field
                      v-model="v.name"
                      placeholder="e.g. Regular"
                      density="compact"
                      hide-details
                      variant="outlined"
                      bg-color="white"
                    />
                  </v-col>

                  <v-col cols="4">
                    <v-text-field
                      v-model="v.price"
                      type="number"
                      prefix="$"
                      density="compact"
                      hide-details
                      variant="outlined"
                      bg-color="white"
                    />
                  </v-col>

                  <v-col cols="2" class="text-right">
                    <v-btn
                      icon="mdi-delete-outline"
                      color="error"
                      variant="text"
                      size="small"
                      :disabled="form.variants.length <= 1"
                      @click="removeSize(i)"
                    />
                  </v-col>
                </v-row>

                <div
                  v-if="form.variants.length === 0"
                  class="text-center py-4 text-grey"
                >
                  No sizes added. Click "Add Size" to begin.
                </div>
              </v-sheet>
            </v-col>

            <!-- FIXED PRICE -->
            <v-text-field
              v-if="!showSizeOptions"
              v-model="form.price"
              type="number"
              label="Price"
            />
          </v-col>
        </v-row>
      </v-card-text>

      <v-card-actions>
        <v-spacer />
        <v-btn variant="text" @click="close">Cancel</v-btn>
        <v-btn color="primary" @click="save">Save</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>
