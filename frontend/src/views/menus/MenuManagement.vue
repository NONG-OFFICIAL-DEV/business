<template>
  <v-container fluid class="pa-0">
    <custom-title icon="mdi-food">
      Menu Management
      <template #right>
        <v-btn color="primary" prepend-icon="mdi-plus" @click="openAddDialog">
          Add New Menu Item
        </v-btn>
      </template>
    </custom-title>

    <v-row class="mb-4 align-center">
      <v-col cols="4">
        <v-select
          v-model="selectedCategory"
          :items="categoryStore.items"
          item-title="name"
          item-value="id"
          label="Category"
          clearable
          hide-details
        >
          <template #append-item>
            <v-divider />
            <v-list-item class="text-primary" @click="openCategoryDialog()">
              <v-list-item-title>
                <v-icon>mdi-plus</v-icon>
                Create new category
              </v-list-item-title>
            </v-list-item>
          </template>
        </v-select>
      </v-col>
      <v-col cols="8" class="d-flex justify-end">
        <v-pagination
          v-model="currentPage"
          :length="pageCount"
          total-visible="5"
          rounded="circle"
          size="small"
        />
      </v-col>
    </v-row>

    <v-row>
      <v-col
        v-if="menuStore.loading"
        v-for="n in 8"
        :key="n"
        cols="12"
        sm="6"
        md="4"
        lg="3"
        xl="2"
      >
        <v-skeleton-loader type="card" class="rounded-xl" />
      </v-col>

      <v-col
        v-else
        v-for="product in paginatedProducts"
        :key="product.id"
        cols="12"
        sm="6"
        md="4"
        lg="3"
        xl="2"
      >
        <v-hover v-slot="{ hover }">
          <v-card
            flat
            class="rounded-xl border overflow-hidden"
          >
            <v-img
              :src="product.image_url"
              cover
              aspect-ratio="2"
              class="bg-grey-lighten-3"
            >
              <div class="d-flex justify-end pa-2">
                <v-chip
                  size="x-small"
                  :color="product.status === 'active' ? 'green' : 'orange'"
                  class="font-weight-bold"
                  variant="flat"
                >
                  {{ product.status === 'active' ? 'LIVE' : 'OFFLINE' }}
                </v-chip>
              </div>
            </v-img>

            <v-card-text>
              <div class="text-subtitle-1 text-truncate mb-1">
                {{ product.name }}
              </div>
              <div class="text-h6 font-weight-bold text-primary">
                ${{
                  product.has_variants
                    ? product.variants[0].price
                    : product.price
                }}
              </div>
            </v-card-text>

            <v-divider />

            <v-card-actions class="pa-2">
              <v-spacer />
              <v-btn
                variant="tonal"
                color="blue-darken-1"
                size="small"
                icon="mdi-pencil-outline"
                class="rounded-lg"
                @click="editMenu(product)"
              />
              <v-btn
                variant="tonal"
                color="error"
                size="small"
                icon="mdi-delete-outline"
                class="ms-2 rounded-lg"
                @click="confirmDelete(product)"
              />
            </v-card-actions>
          </v-card>
        </v-hover>
      </v-col>

      <v-col
        v-if="filteredProducts.length === 0"
        cols="12"
        class="text-center py-12"
      >
        <v-icon size="64" color="grey-lighten-2">mdi-food-off</v-icon>
        <div class="text-grey mt-2">No menu items found for this category.</div>
      </v-col>
    </v-row>

    <!-- Dialogs -->
    <MenuFormDialog
      v-model="dialog"
      :edit-mode="isEdit"
      :item="selectedItem"
      :categories="categoryStore.items"
      @save="handleSave"
    />
    <CategoryFormDialog
      v-model="categoryDialog"
      :edit-mode="isCategoryEdit"
      :item="selectedCategoryItem"
      @save="handleCategorySave"
    />
  </v-container>
</template>

<script setup>
  import { ref, computed, onMounted } from 'vue'
  import { useMenuStore } from '@/stores/menuStore'
  import { useCategoryMenuStore } from '@/stores/categoryMenu'
  import MenuFormDialog from '@/components/sale/MenuFormDialog.vue'
  import CategoryFormDialog from '@/components/sale/CategoryFormDialog.vue'
  import { useAppUtils } from '@/composables/useAppUtils'
  import { useI18n } from 'vue-i18n'

  const { confirm, notif } = useAppUtils()
  const { t } = useI18n()

  const menuStore = useMenuStore()
  const categoryStore = useCategoryMenuStore()

  const selectedCategory = ref(null)
  const dialog = ref(false)
  const isEdit = ref(false)
  const selectedItem = ref(null)

  const categoryDialog = ref(false)
  const isCategoryEdit = ref(false)
  const selectedCategoryItem = ref(null)

  // --- Pagination ---
  const currentPage = ref(1)
  const itemsPerPage = 8

  const filteredProducts = computed(() => {
    const data = menuStore.menus?.data || []
    if (!selectedCategory.value) return data
    return data.filter(item => item.menu_category_id === selectedCategory.value)
  })

  const pageCount = computed(() =>
    Math.ceil(filteredProducts.value.length / itemsPerPage)
  )
  const paginatedProducts = computed(() =>
    filteredProducts.value.slice(
      (currentPage.value - 1) * itemsPerPage,
      currentPage.value * itemsPerPage
    )
  )

  // --- Menu Actions ---
  const openAddDialog = () => {
    isEdit.value = false
    selectedItem.value = null
    dialog.value = true
  }

  const editMenu = menu => {
    isEdit.value = true
    selectedItem.value = { ...menu }
    dialog.value = true
  }

  const handleSave = async data => {
    try {
      await menuStore.saveMenu(data)
      notif(t('messages.saved_success'), { type: 'success', color: 'primary' })
      await menuStore.fetchMenus()
      dialog.value = false
    } catch {
      notif(t('messages.save_failed'), { type: 'error' })
    }
  }

  const confirmDelete = menu => {
    confirm({
      title: t('Confirm Delete'),
      message: `Are you sure delete "${menu.name}"?`,
      options: { type: 'error', color: 'error', with: 200 },
      agree: async () => {
        await menuStore.deleteMenu(menu.id)
        notif(t('messages.deleted_success'), { type: 'success' })
        await menuStore.fetchMenus()
      }
    })
  }

  // --- Category Actions ---
  const openCategoryDialog = (item = null) => {
    isCategoryEdit.value = !!item
    selectedCategoryItem.value = item
    categoryDialog.value = true
  }

  const handleCategorySave = async data => {
    try {
      if (isCategoryEdit.value) await categoryStore.updateItem(data.id, data)
      else await categoryStore.createItem(data)
      notif(t('messages.saved_success'), { type: 'success' })
      categoryDialog.value = false
      await categoryStore.fetchAllMenuCategory({ loading: 'overlay' })
    } catch {
      notif(t('messages.save_failed'), { type: 'error' })
    }
  }

  onMounted(() => {
    menuStore.fetchMenus()
    categoryStore.fetchAllMenuCategory({ loading: 'overlay' })
  })
</script>
