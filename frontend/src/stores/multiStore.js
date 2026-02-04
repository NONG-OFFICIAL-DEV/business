// stores/multiStore.js
import { defineStore } from 'pinia'

export const useMultiStore = defineStore('multiStore', {
  state: () => ({
    activeStoreId: null,
    stores: [
      { id: 1, name: 'Downtown Branch', location: 'Street 51', isOpen: true },
      { id: 2, name: 'Riverside Cafe', location: 'Preah Sisowath', isOpen: true },
      { id: 3, name: 'Mall Outlet', location: 'Floor 2', isOpen: false }
    ]
  }),
  actions: {
    setStore(id) {
      this.activeStoreId = id
      // logic to reload live orders for this specific store
    }
  }
})