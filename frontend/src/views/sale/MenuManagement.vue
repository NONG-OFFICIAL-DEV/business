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
        <v-chip
          v-for="cat in ['All Items', 'Coffee', 'Tea', 'Pastries', 'Lunch']"
          :key="cat"
          :value="cat"
          class="px-6"
        >
          {{ cat }}
        </v-chip>
      </v-chip-group>
    </div>
    <v-row>
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
                color="green lighten-2"
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
                class="rounded-lg font-weight-bold"
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
    </v-row>
    <MenuFormDialog
      v-model="dialog"
      :edit-mode="isEdit"
      :item="selectedItem"
      @save="handleSave"
    />
  </v-container>
</template>

<script setup>
  import { ref, computed, onMounted } from 'vue'
  import { useMenuStore } from '@/stores/menuStore'
  import MenuFormDialog from '@/components/sale/MenuFormDialog.vue'
  import { useAppUtils } from '@/composables/useAppUtils'
  import { useI18n } from 'vue-i18n'

  const { confirm, notif } = useAppUtils()
  const { t } = useI18n()

  const selectedCategory = ref('All Items')
  const dialog = ref(false)
  const isEdit = ref(false)
  const selectedItem = ref(null)

  const menuStore = useMenuStore()

  onMounted(() => {
    menuStore.fetchMenus()
  })

  /* FILTER BY CATEGORY */
  const filteredProducts = computed(() => {
    if (selectedCategory.value === 'All Items') {
      return menuStore.menus.data
    }
    return menuStore.menus.data.filter(
      item => item.category === selectedCategory.value
    )
  })

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

  function confirmDelete(menu) {
    confirm({
      title: 'Are you sure?',
      message: `Are you sure you want to delete this menu "${menu.name}"?`,
      options: { type: 'error', color: 'error', width: 500 },
      agree: async () => {
        try {
          menuStore.deleteMenu(menu.id)
          notif(t('messages.deleted_success'), {
            type: 'success',
            color: 'primary'
          })
          menuStore.fetchMenus()
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
    if (isEdit.value) {
      await menuStore.saveMenu(data)
    } else {
      await menuStore.saveMenu(data)
    }
    notif(t('messages.saved_success'), {
      type: 'success',
      color: 'primary'
    })
    menuStore.fetchMenus()
  }
</script>

<style scoped>
  .product-card {
    transition: all 0.2s ease-in-out;
    border: 1px solid #e0e0e0 !important;
  }
  .product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 20px rgba(0, 0, 0, 0.08) !important;
    border-color: rgb(var(--v-theme-primary)) !important;
  }
  .no-scrollbar::-webkit-scrollbar {
    display: none;
  }
</style>
