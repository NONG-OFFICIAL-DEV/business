<script setup>
  import { ref } from 'vue'

  const stores = ref([
    {
      id: 1,
      name: 'Downtown Bistro',
      address: 'Central District, NY',
      staffCount: 12,
      image:
        'https://images.unsplash.com/photo-1552566626-52f8b828add9?q=80&w=400'
    },
    {
      id: 2,
      name: 'Riverside Grill',
      address: 'East Side, Brooklyn',
      staffCount: 8,
      image:
        'https://i.pinimg.com/736x/85/bc/84/85bc84f9f109447e4064e4fea66eb2c3.jpg'
    },
    {
      id: 3,
      name: 'Riverside Grill',
      address: 'East Side, Brooklyn',
      staffCount: 8,
      image:
        'https://i.pinimg.com/1200x/14/c0/de/14c0deb99b3dd66de1ea65386adeefed.jpg'
    }
    // Add more stores here...
  ])

  const deleteDialog = ref(false)
  const selectedStore = ref(null)

  const confirmDelete = store => {
    selectedStore.value = store
    deleteDialog.value = true
  }

  const handleDelete = () => {
    stores.value = stores.value.filter(s => s.id !== selectedStore.value.id)
    deleteDialog.value = false
  }
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
        v-for="store in stores"
        :key="store.id"
        cols="12"
        sm="6"
        md="4"
        lg="3"
        class="pa-3"
      >
        <v-card flat class="compact-premium-card">
          <div class="card-image-wrapper">
            <v-img :src="store.image" aspect-ratio="2" cover class="store-img">
              <div class="d-flex justify-end pa-2">
                <v-btn
                  icon="mdi-pencil"
                  size="x-small"
                  color="white"
                  class="edit-btn-mini"
                  elevation="4"
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
                @click="confirmDelete(store)"
              >
                Delete
              </v-btn>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
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
