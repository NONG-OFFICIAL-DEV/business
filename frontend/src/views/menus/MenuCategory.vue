<template>
  <v-container fluid class="pa-0">
    <custom-title icon="mdi-shape">Menu Category Management</custom-title>

    <v-row class="mt-4">
      <v-col cols="12" md="4">
        <v-card flat rounded="xl" class="border pa-4">
          <div class="text-h6 font-weight-black mb-4">
            {{ isCategoryEdit ? 'Edit Category' : 'Create New Category' }}
          </div>

          <v-form @submit.prevent="saveData">
            <v-text-field
              v-model="form.name"
              label="Category Name"
              variant="outlined"
              density="comfortable"
              placeholder="e.g. Coffee, Main Course"
              class="mb-2"
            />

            <v-textarea
              v-model="form.description"
              label="Description (Optional)"
              variant="outlined"
              rows="3"
            />

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn
                v-if="isCategoryEdit"
                variant="text"
                color="grey"
                class="text-none"
                @click="resetForm"
              >
                Cancel
              </v-btn>
              <v-btn
                color="primary"
                elevation="0"
                class="text-none font-weight-bold rounded-lg"
                type="submit"
                :loading="loading"
              >
                {{ isCategoryEdit ? 'Update' : 'Save' }}
              </v-btn>
            </v-card-actions>
          </v-form>
        </v-card>
      </v-col>

      <v-col cols="12" md="8">
        <v-card flat rounded="xl" class="border">
          <v-table>
            <thead>
              <tr class="bg-grey-lighten-4">
                <th class="font-weight-bold">Name</th>
                <th class="font-weight-bold">Description</th>
                <th class="text-right font-weight-bold">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="cat in categoryStore.items" :key="cat.id">
                <td class="font-weight-bold">{{ cat.name }}</td>
                <td class="text-grey-darken-1">{{ cat.description || '-' }}</td>
                <td class="text-right">
                  <v-btn
                    icon="mdi-pencil-outline"
                    variant="text"
                    color="primary"
                    size="small"
                    @click="editCategory(cat)"
                  />
                  <v-btn
                    icon="mdi-trash-can-outline"
                    variant="text"
                    color="error"
                    size="small"
                    @click="confirmDelete(cat)"
                  />
                </td>
              </tr>
              <tr v-if="categoryStore.items.length === 0">
                <td colspan="3" class="text-center py-8 text-grey">
                  No categories found.
                </td>
              </tr>
            </tbody>
          </v-table>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
  import { ref, onMounted, reactive } from 'vue'
  import { useCategoryMenuStore } from '@/stores/categoryMenu'
  import { useAppUtils } from '@/composables/useAppUtils'
  import { useI18n } from 'vue-i18n'

  const { confirm, notif } = useAppUtils()
  const { t } = useI18n()
  const categoryStore = useCategoryMenuStore()

  const isCategoryEdit = ref(false)
  const loading = ref(false)

  const form = reactive({
    id: null,
    name: '',
    description: ''
  })

  const resetForm = () => {
    isCategoryEdit.value = false
    form.id = null
    form.name = ''
    form.description = ''
  }

  const editCategory = item => {
    isCategoryEdit.value = true
    form.id = item.id
    form.name = item.name
    form.description = item.description
  }

  const saveData = async () => {
    if (!form.name) return notif('Name is required', { type: 'warning' })

    loading.value = true
    try {
      if (isCategoryEdit.value) {
        await categoryStore.updateItem(form.id, { ...form })
      } else {
        await categoryStore.createItem({ ...form })
      }
      notif(t('messages.saved_success'), { type: 'success' })
      resetForm()
      await categoryStore.fetchAllMenuCategory({ loading: 'overlay' })
    } catch {
      notif(t('messages.error_occurred'), { type: 'error' })
    } finally {
      loading.value = false
    }
  }

  const confirmDelete = item => {
    confirm({
      title: t('Confirm Delete'),
      message: `Are you sure delete category "${item.name}"?`,
      options: { type: 'error', color: 'error', width: 550 },
      agree: async () => {
        await categoryStore.deleteItem(item.id)
        notif(t('messages.deleted_success'), { type: 'success' })
        await categoryStore.fetchAllMenuCategory({ loading: 'overlay' })
      }
    })
  }

  onMounted(async () => {
    await categoryStore.fetchAllMenuCategory({ loading: 'overlay' })
  })
</script>
