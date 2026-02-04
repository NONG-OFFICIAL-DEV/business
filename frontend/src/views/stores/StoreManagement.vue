<script setup>
  import { ref, computed, onMounted } from 'vue'
  import StoreCreateDialog from '@/components/stores/StoreCreateDialog.vue'
  import { useStoreStore } from '../../stores/storeStore'
  import { useI18n } from 'vue-i18n'
  import { useAppUtils } from '@/composables/useAppUtils'

  const { t } = useI18n()
  const { confirm, notif } = useAppUtils()

  const storeStore = useStoreStore()
  const stores = computed(() => storeStore.stores)

  const selectedStore = ref(null)
  const showDialog = ref(false)

  const onEdit = store => {
    selectedStore.value = store
    showDialog.value = true
  }

  const createStore = async payload => {
    if (payload.id) {
      await storeStore.updateStore(payload)
    } else {
      await storeStore.addStore(payload)
    }
    await storeStore.fetchStores()
  }

  const handleDelete = id => {
    confirm({
      title: 'Are you sure?',
      message: `Are you sure you want to delete this"?`,
      options: { type: 'error', color: 'error', width: 550 },
      agree: async () => {
        try {
          await storeStore.deleteStore(id)
          notif(t('messages.deleted_success'), {
            type: 'success',
            color: 'primary'
          })
          await storeStore.fetchStores()
        } catch (err) {
          notif(err.response?.data?.error || t('messages.delete_failed'), {
            type: 'error',
            color: 'error'
          })
        }
      }
    })
  }
  const openCreateDialog = () => {
    showDialog.value = true
  }

  onMounted(async () => {
    await storeStore.fetchStores()
  })
</script>

<template>
  <v-container class="pa-0" fluid>
    <custom-title icon="mdi-domain">
      Store Network
      <template #right>
        <v-btn
          color="primary"
          prepend-icon="mdi-plus"
          class="text-none"
          @click="openCreateDialog"
        >
          New Store
        </v-btn>
      </template>
    </custom-title>
    <v-row>
      <v-col
        v-for="store in stores?.data?.data"
        :key="store.id"
        cols="12"
        sm="6"
        md="4"
        lg="3"
        class="pa-3"
      >
        <v-card flat class="compact-premium-card">
          <div class="card-image-wrapper">
            <v-img
              :src="store.logo_url"
              aspect-ratio="2"
              cover
              class="store-img"
            >
              <div class="d-flex justify-end pa-2">
                <v-btn
                  icon="mdi-pencil"
                  size="x-small"
                  color="white"
                  class="edit-btn-mini"
                  elevation="4"
                  @click="onEdit(store)"
                ></v-btn>
              </div>
            </v-img>
          </div>

          <v-card-text class="px-4 pb-4 pt-0">
            <div class="d-flex justify-space-between align-center mb-1">
              <h3
                class="text-subtitle-1 font-weight-black color-primary truncate-text"
              >
                {{ store.name }}
              </h3>
            </div>

            <div class="d-flex align-center text-grey-darken-1 mb-3">
              <v-icon size="14" class="mr-1">mdi-map-marker-outline</v-icon>
              <span class="text-caption truncate-text">
                {{ store.address }}
              </span>
            </div>

            <div class="d-flex align-center justify-space-between">
              <v-chip
                size="x-small"
                variant="flat"
                color="grey-lighten-4"
                class="font-weight-bold text-grey-darken-2"
              >
                <v-icon start size="12">mdi-account-group</v-icon>
                {{ store.staffCount }} Staff
              </v-chip>

              <v-btn
                variant="text"
                color="error"
                size="x-small"
                class="text-none font-weight-bold"
                prepend-icon="mdi-trash-can-outline"
                @click="handleDelete(store.id)"
              >
                Delete
              </v-btn>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <StoreCreateDialog
      v-model="showDialog"
      :store="selectedStore"
      @created="createStore"
    />
  </v-container>
</template>

<style scoped>
  .color-primary {
    color: #1e293b;
  }
  .color-teal {
    color: #0d9488;
  }

  .compact-premium-card {
    border-radius: 20px !important;
    background: #ffffff !important;
    border: 1px solid #f1f5f9 !important;
    transition: all 0.3s ease;
  }

  .compact-premium-card:hover {
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05) !important;
    border-color: #e2e8f0 !important;
  }

  .card-image-wrapper {
    padding: 8px;
  }

  .store-img {
    border-radius: 14px !important;
  }

  .edit-btn-mini {
    width: 28px !important;
    height: 28px !important;
    opacity: 0.8;
  }

  .edit-btn-mini:hover {
    opacity: 1;
  }

  .truncate-text {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 140px; /* Adjust based on your grid */
  }

  /* Remove default card padding that might interfere */
  .v-card-text {
    line-height: 1.2;
  }
</style>
