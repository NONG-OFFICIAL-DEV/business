<template>
  <v-container fluid class="pa-0">
    <custom-title icon="mdi-food">
      Menu Management
      <template #right>
        <v-btn
          color="primary"
          prepend-icon="mdi-plus"
          class="text-none font-weight-black"
          @click="openAddDialog"
        >
          Add New Menu Item
        </v-btn>
      </template>
    </custom-title>

    <div class="d-flex overflow-x-auto pb-4 mt-4 no-scrollbar">
      <v-chip-group
        v-model="selectedCategory"
        mandatory
        selected-class="bg-primary text-white"
      >
        <v-chip value="All Items" class="px-6">
          {{ t('All Items') }}
        </v-chip>

        <v-chip
          v-for="cat in categoryMenuStore.items"
          :key="cat.id"
          :value="cat.name"
          class="px-6"
        >
          {{ cat.name }}
        </v-chip>
      </v-chip-group>
    </div>

    <v-row v-if="menuStore.loading">
      <v-col v-for="n in 4" :key="n" cols="12" sm="6" md="4" lg="3">
        <v-skeleton-loader type="card" class="rounded-xl" />
      </v-col>
    </v-row>

    <v-row v-else>
      <v-col
        v-for="product in filteredProducts"
        :key="product.id"
        cols="12"
        sm="6"
        md="4"
        lg="3"
        xl="2"
      >
        <v-card flat class="product-card rounded-xl border">
          <v-img :src="product.image_url" height="160" cover>
            <div class="d-flex justify-end pa-2">
              <v-chip
                size="x-small"
                color="green-lighten-2"
                class="font-weight-bold"
                variant="flat"
              >
                LIVE
              </v-chip>
            </div>
          </v-img>
          
          <v-card-text class="pa-4 bg-white">
            <div class="text-subtitle-2 font-weight-bold text-truncate mb-1">
              {{ product.name }}
            </div>
            <div class="text-h6 font-weight-black text-primary mb-3">
              ${{ product.price }}
            </div>
            <v-chip size="x-small" variant="outlined" color="grey">
              {{ product.category }}
            </v-chip>
          </v-card-text>

          <v-divider />

          <v-card-actions>
            <v-spacer></v-spacer>
            <div class="d-flex gap-2">
              <v-btn
                variant="tonal"
                color="blue-darken-1"
                size="small"
                icon="mdi-pencil-outline"
                class="rounded-lg"
                @click="editMenu(product)"
              ></v-btn>
              <v-btn
                icon="mdi-delete-outline"
                variant="tonal"
                color="error"
                size="small"
                class="ms-1 rounded-lg"
                @click="confirmDelete(product)"
              ></v-btn>
            </div>
          </v-card-actions>
        </v-card>
      </v-col>

      <v-col v-if="filteredProducts.length === 0" cols="12" class="text-center py-12">
        <v-icon size="64" color="grey-lighten-1">mdi-tray-blank</v-icon>
        <div class="text-grey-darken-1 mt-2">No items found in this category.</div>
      </v-col>
    </v-row>

    <MenuFormDialog
      v-model="dialog"
      :edit-mode="isEdit"
      :item="selectedItem"
      :categories="categoryMenuStore.items"
      @save="handleSave"
    />
  </v-container>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useMenuStore } from '@/stores/menuStore'
import { useCategoryMenuStore } from '@/stores/categoryMenuStore'
import MenuFormDialog from '@/components/sale/MenuFormDialog.vue'
import { useAppUtils } from '@/composables/useAppUtils'
import { useI18n } from 'vue-i18n'

const { confirm, notif } = useAppUtils()
const { t } = useI18n()

// State
const selectedCategory = ref('All Items')
const dialog = ref(false)
const isEdit = ref(false)
const selectedItem = ref(null)

// Stores
const menuStore = useMenuStore()
const categoryMenuStore = useCategoryMenuStore()

/* FILTER LOGIC */
const filteredProducts = computed(() => {
  const allMenus = menuStore.menus?.data || []
  
  if (selectedCategory.value === 'All Items') {
    return allMenus
  }
  
  // Filtering based on category name
  return allMenus.filter(
    item => item.category === selectedCategory.value
  )
})

/* ACTIONS */
function openAddDialog() {
  isEdit.value = false
  selectedItem.value = null
  dialog.value = true
}

function editMenu(menu) {
  isEdit.value = true
  selectedItem.value = { ...menu }
  dialog.value = true
}

async function confirmDelete(menu) {
  confirm({
    title: t('messages.confirm_title') || 'Are you sure?',
    message: `${t('messages.confirm_delete_text')} "${menu.name}"?`,
    options: { type: 'error', color: 'error', width: 400 },
    agree: async () => {
      try {
        await menuStore.deleteMenu(menu.id)
        notif(t('messages.deleted_success'), { type: 'success', color: 'primary' })
        await menuStore.fetchMenus()
      } catch (err) {
        notif(err.response?.data?.error || t('messages.delete_failed'), {
          type: 'error',
          color: 'error'
        })
      }
    }
  })
}

async function handleSave(data) {
  try {
    // Assuming saveMenu handles both create and update internally or via separate calls
    await menuStore.saveMenu(data)
    notif(t('messages.saved_success'), { type: 'success', color: 'primary' })
    dialog.value = false
    await menuStore.fetchMenus()
  } catch (err) {
    notif(t('messages.save_failed'), { type: 'error', color: 'error' })
  }
}

onMounted(async () => {
  // Fetch both menus and categories on load
  await Promise.all([
    menuStore.fetchMenus(),
    categoryMenuStore.fetchAll()
  ])
})
</script>

<style scoped>
.product-card {
  transition: all 0.25s ease-in-out;
  border: 1px solid #e0e0e0 !important;
  display: flex;
  flex-direction: column;
  height: 100%;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
  border-color: rgb(var(--v-theme-primary)) !important;
}

.no-scrollbar::-webkit-scrollbar {
  display: none;
}

.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.gap-2 {
  gap: 8px;
}
</style>