<template>
  <v-dialog v-model="internalOpen" max-width="800">
    <v-card>
      <v-toolbar
        :title="form.id ? 'Edit Product' : 'Add Product'"
        class="bg-primary"
      >
        <v-spacer />
        <v-btn icon="mdi-close" @click="close"></v-btn>
      </v-toolbar>
      <v-card-text class="mt-4">
        <v-form ref="formRef" v-model="isValid">
          <v-row>
            <v-col cols="12" sm="4" md="4">
              <v-select
                label="Supplier"
                :items="supplierStore.suppliers.data"
                v-model="form.supplier_id"
                item-title="name"
                item-value="id"
                :rules="[rules.required]"
              >
                <template #append-item>
                  <v-divider />

                  <v-list-item class="text-primary" @click="openCreateSupplier">
                    <v-list-item-title>
                      ➕ Create new supplier
                    </v-list-item-title>
                  </v-list-item>
                </template>
              </v-select>
            </v-col>
            <v-col cols="12" sm="4">
              <v-text-field
                v-model="form.name"
                label="Name"
                :rules="[rules.required]"
                required
              />
            </v-col>
            <v-col cols="12" sm="4">
              <v-select
                v-model="form.category_id"
                :items="categoryStore.categories.data"
                item-title="name"
                item-value="id"
                label="Category"
                :rules="[rules.required]"
              >
                <template #append-item>
                  <v-divider />

                  <v-list-item class="text-primary" @click="openCreateCatetory">
                    <v-list-item-title>
                      <v-icon>mdi-plus</v-icon>
                      Create new category
                    </v-list-item-title>
                  </v-list-item>
                </template>
              </v-select>
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12" sm="6">
              <v-select
                v-model="form.unit_id"
                :items="unitStore.units"
                item-title="name"
                item-value="id"
                label="Unit"
                :rules="[rules.required]"
              />
            </v-col>
            <v-col cols="12" sm="6">
              <v-text-field v-model="form.sku" label="SKU" />
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12" sm="6">
              <v-text-field v-model="form.price" label="Price" type="number" />
            </v-col>
            <v-col cols="12" sm="6">
              <v-select
                v-model="form.status"
                :items="statusOptions"
                item-title="name"
                item-value="id"
                label="Status"
                :rules="[rules.required]"
              />
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12" md="6">
              <v-file-input
                v-model="image"
                label="Product Image"
                density="comfortable"
                accept="image/*"
                prepend-icon=""
                append-inner-icon="mdi-camera"
                show-size
                :multiple="false"
                @update:modelValue="previewImage"
                variant="outlined"
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-img
                v-if="preview"
                :src="preview"
                max-width="150"
                class="mt-2 rounded"
                aspect-ratio="1"
                cover
              />
            </v-col>
          </v-row>
        </v-form>
      </v-card-text>
      <v-card-actions>
        <v-spacer />
        <v-btn text @click="close">Cancel</v-btn>
        <v-btn color="primary" :disabled="!isValid" @click="submit">Save</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
  <SupplierDialog
    v-model="isDialogSupplierOpen"
    :supplier="selectedSupplier"
    @save="handleSave"
  />
  <CategoryDialog
    v-model="isDialogCategoryOpen"
    :category="selectedCategory"
    @save="handleSaveCategory"
  />
</template>

<script setup>
  import { ref, watch, onMounted } from 'vue'
  import { useCategoryStore } from '@/stores/categoryStore'
  import { useUnitStore } from '@/stores/unitStore'
  import { useSupplierStore } from '@/stores/supplierStore'
  import SupplierDialog from '@/components/SupplierDialog.vue'
  import CategoryDialog from '@/components/CategoryDialog.vue'
  import { useAppUtils } from '@/composables/useAppUtils'
  import { useI18n } from 'vue-i18n'

  const unitStore = useUnitStore()
  const categoryStore = useCategoryStore()
  const supplierStore = useSupplierStore()
  const isDialogSupplierOpen = ref(false)
  const isDialogCategoryOpen = ref(false)
  const selectedSupplier = ref(null)
  const selectedCategory = ref(null)
  const { notif } = useAppUtils()
  const { t } = useI18n()

  const props = defineProps({
    isOpen: Boolean,
    product: { type: Object, default: null }
  })
  const emit = defineEmits(['update:isOpen', 'save'])

  const internalOpen = ref(false)
  const formRef = ref(null)
  const isValid = ref(false)

  const form = ref({
    id: null,
    name: '',
    sku: '',
    status: '',
    unit_id: null,
    // category_id: null,
    price: 0
  })

  const image = ref(null)
  const preview = ref(props.product?.image_url || null)

  const previewImage = value => {
    // Clear preview
    if (!value) {
      preview.value = null
      return
    }

    // Vuetify may return File OR File[]
    const file = Array.isArray(value) ? value[0] : value

    // Extra safety
    if (!(file instanceof File)) {
      preview.value = null
      return
    }

    preview.value = URL.createObjectURL(file)
  }

  const statusOptions = ref([
    { id: 'active', name: 'Active' },
    { id: 'inactive', name: 'Inactive' }
  ])

  const rules = {
    required: v => !!v || 'This field is required'
  }
  const openCreateSupplier = supplier => {
    selectedSupplier.value = { ...supplier }
    isDialogSupplierOpen.value = true
  }
  const openCreateCatetory = categories => {
    selectedCategory.value = { ...categories }
    isDialogCategoryOpen.value = true
  }
  const handleSave = async supplier => {
    await supplierStore.addSupplier(supplier)
    notif(t('messages.saved_success'), { type: 'success' })

    isDialogSupplierOpen.value = false
    supplierStore.fetchSuppliers({ status: 1, per_page: -1 })
  }
  const handleSaveCategory = async category => {
    await categoryStore.addCategory(category)
    notif(t('messages.saved_success'), {
      type: 'success',
      color: 'primary'
    })
    isDialogCategoryOpen.value = false
    categoryStore.fetchCategories({
      per_page: -1
    })
  }
  // 🧹 Reset form
  const resetForm = () => {
    form.value = { id: null, name: '', sku: '', price: 0 }
    isValid.value = false
    formRef.value?.resetValidation()
  }
  // 🔄 Sync with parent
  watch(
    () => props.isOpen,
    val => {
      internalOpen.value = val
      if (val) {
        // opening dialog → load product if editing, otherwise reset
        if (props.product) {
          form.value = { ...props.product }
        } else {
          resetForm()
        }
      }
    },
    { immediate: true }
  )

  // 🔄 Emit back
  watch(internalOpen, val => emit('update:isOpen', val))

  const close = () => {
    internalOpen.value = false
    resetForm()
  }

  const submit = async () => {
    const ok = await formRef.value?.validate()
    if (ok) {
      emit('save', { ...form.value, image: image.value })
      close() // close & reset after successful save
    }
  }
  const loadData = () => {
    categoryStore.fetchCategories({
      per_page: -1
    })
    unitStore.fetchUnits()
    supplierStore.fetchSuppliers({ status: 1, per_page: -1 })
  }
  onMounted(() => loadData())
</script>
