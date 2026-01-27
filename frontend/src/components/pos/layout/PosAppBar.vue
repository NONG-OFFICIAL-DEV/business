<script setup>
  import { computed } from 'vue'
  import { usePosStore } from '@/stores/posStore'
  import { usePermission } from '../../../composables/usePermission'
  const posStore = usePosStore()
  const { isAdmin, isManager } = usePermission()

  function selectStore(store) {
    posStore.selectStore(store)
  }
  const store = computed(() => posStore.selectedStore)

  defineProps({
    search: String,
    user: Object
  })

  const emit = defineEmits(['update:search', 'update:store', 'logout'])

  function handleLogout() {
    emit('logout')
  }
</script>

<template>
  <v-app-bar
    elevation="0"
    color="white"
    class="px-4 border-b d-flex align-center"
  >
    <!-- LOGO -->
    <div class="d-flex align-center mr-6">
      <v-icon icon="mdi-lightning-bolt" color="primary" class="mr-1" />
      <span class="font-weight-black text-h6">
        QUICK
        <span class="text-primary">POS</span>
      </span>
    </div>
    <!-- STORE SWITCH -->
    <v-menu location="bottom" :disabled="!isAdmin && !isManager">
      <template #activator="{ props }">
        <v-btn
          v-bind="props"
          variant="tonal"
          color="primary"
          class="rounded-pill px-4"
          width="200"
        >
          <v-icon size="18" class="mr-2">mdi-store</v-icon>
          <span class="font-weight-bold">{{ store.name }}</span>
          <v-icon size="18" class="ml-2">mdi-chevron-down</v-icon>
        </v-btn>
      </template>

      <v-list rounded="lg">
        <v-list-item
          v-for="s in posStore.stores"
          :key="s.id"
          @click="selectStore(s)"
          :active="store.id === s.id"
        >
          <v-list-item-title class="font-weight-bold">
            {{ s.name }}
          </v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>

    <v-spacer />

    <!-- SEARCH -->
    <v-responsive width="250">
      <v-text-field
        :model-value="search"
        @update:model-value="emit('update:search', $event)"
        prepend-inner-icon="mdi-barcode-scan"
        placeholder="Scan barcode or search product..."
        hide-details
        density="comfortable"
        variant="solo-filled"
        flat
        rounded="lg"
        autofocus
      />
    </v-responsive>

    <v-spacer />

    <!-- USER -->
    <div class="d-flex align-center">
      <v-chip color="success" variant="tonal" class="mr-3" size="small">
        SHIFT ACTIVE
      </v-chip>

      <div class="text-right d-none d-md-block">
        <div class="text-caption font-weight-bold text-capitalize">
          {{ user?.username }}
        </div>
        <div class="text-caption text-grey">POS Operator</div>
      </div>

      <v-avatar size="42" class="ml-3 border">
        <v-img
          src="https://api.dicebear.com/7.x/avataaars/svg?seed=Alex"
          cover
        />
      </v-avatar>

      <v-btn
        icon="mdi-logout"
        variant="text"
        color="error"
        class="ml-2"
        @click="handleLogout"
      />
    </div>
  </v-app-bar>
</template>
