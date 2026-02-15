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
                :items="supplierStore.suppliers.data"
                v-model="form.supplier_id"
                item-title="name"
                item-value="id"
                :rules="[rules.required]"
              >
                <template #label>
                  Supplier
                  <span class="required-star">*</span>
                </template>
                <template #append-item>
                  <v-divider />
                  <v-list-item class="text-primary" @click="openCreateSupplier">
                    <v-list-item-title>
                      <v-icon>mdi-plus</v-icon>
                      Create new supplier
                    </v-list-item-title>
                  </v-list-item>
                </template>
              </v-select>
            </v-col>
            <v-col cols="12" sm="4">
              <v-text-field
                v-model="form.name"
                :rules="[rules.required]"
                required
              >
                <template #label>
                  Name
                  <span class="required-star">*</span>
                </template>
              </v-text-field>
            </v-col>
            <v-col cols="12" sm="4">
              <v-select
                v-model="form.category_id"
                :items="categoryStore.categories.data"
                item-title="name"
                item-value="id"
                :rules="[rules.required]"
              >
                <template #label>
                  Category
                  <span class="required-star">*</span>
                </template>
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
                :rules="[rules.required]"
              >
                <template #label>
                  Unit
                  <span class="required-star">*</span>
                </template>
              </v-select>
            </v-col>
            <v-col cols="12" sm="6">
              <v-text-field
                v-model="form.sku"
                label="SKU"
                :rules="[rules.required]"
              />
            </v-col>
          </v-row>
          <v-row align="center">
            <v-col cols="12" sm="6">
              <v-text-field
                v-model="form.price"
                type="number"
                prefix="$"
                :rules="[rules.required, rules.minZero]"
                persistent-hint
                :hint="pricePreview"
              >
                <template #label>
                  Price
                  <span class="required-star">*</span>
                </template>
              </v-text-field>
            </v-col>
            <v-col cols="12" sm="6">
              <div class="d-flex align-center mt-n2">
                <v-switch
                  v-model="form.status"
                  :true-value="'active'"
                  :false-value="'inactive'"
                  color="success"
                  inset
                  hide-details
                >
                  <template #label>
                    <span class="mr-2">Status:</span>
                    <v-chip
                      size="x-small"
                      :color="form.status === 'active' ? 'success' : 'error'"
                      class="text-uppercase"
                    >
                      {{ form.status }}
                    </v-chip>
                  </template>
                </v-switch>
              </div>
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
                class="mt-2 rounded border"
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
  import { ref, watch, onMounted, computed } from 'vue'
  import { useCategoryStore } from '@/stores/categoryStore'
  import { useUnitStore } from '@/stores/unitStore'
  import { useSupplierStore } from '@/stores/supplierStore'
  import SupplierDialog from '@/components/SupplierDialog.vue'
  import CategoryDialog from '@/components/CategoryDialog.vue'
  import { useAppUtils } from '@/composables/useAppUtils'
  import { useI18n } from 'vue-i18n'
  import { useCurrency } from '@/composables/useCurrency.js'
  const { formatCurrency } = useCurrency()

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
    status: 'active', // Default to active
    unit_id: null,
    supplier_id: null,
    category_id: null,
    price: 0
  })

  const image = ref(null)
  const preview = ref(null)

  // Create a reactive preview of the price
  const pricePreview = computed(() => {
    if (!form.value.price) return ''
    return formatCurrency(form.value.price)
  })
  const rules = {
    required: v => !!v || 'This field is required',
    minZero: v => v >= 0 || 'Price cannot be negative'
  }

  const previewImage = value => {
    if (!value) {
      preview.value = props.product?.image_url || null
      return
    }
    const file = Array.isArray(value) ? value[0] : value
    if (!(file instanceof File)) return
    preview.value = URL.createObjectURL(file)
  }

  const openCreateSupplier = () => {
    selectedSupplier.value = null
    isDialogSupplierOpen.value = true
  }
  const openCreateCatetory = () => {
    selectedCategory.value = null
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

  const resetForm = () => {
    form.value = {
      id: null,
      name: '',
      sku: '',
      price: 0,
      status: 'active',
      unit_id: null,
      category_id: null,
      supplier_id: null
    }
    preview.value = null
    image.value = null
    isValid.value = false
    formRef.value?.resetValidation()
  }

  watch(
    () => props.isOpen,
    val => {
      internalOpen.value = val
      if (val) {
        if (props.product) {
          form.value = { ...props.product }
          preview.value = props.product.image_url
        } else {
          resetForm()
        }
      }
    },
    { immediate: true }
  )

  watch(internalOpen, val => emit('update:isOpen', val))

  const close = () => {
    internalOpen.value = false
  }

  const submit = async () => {
    const { valid } = await formRef.value?.validate()
    if (valid) {
      emit('save', { ...form.value, image: image.value })
      close()
    }
  }

  const loadData = () => {
    categoryStore.fetchCategories({ per_page: -1 })
    unitStore.fetchUnits()
    supplierStore.fetchSuppliers({ status: 1, per_page: -1 })
  }
  onMounted(() => loadData())
</script>
