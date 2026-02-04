import { defineStore } from 'pinia'
import storeAPI from '@/api/store' // ✅ MUST be this file

export const useStoreStore = defineStore('storeStore', {
  state: () => ({
    stores: []
  }),

  actions: {
    async fetchStores() {
      const res = await storeAPI.getAll()
      this.stores = res.data
    },

    async addStore(store) {
      await storeAPI.create(store)
    },

    async updateStore(store) {
      await storeAPI.update(store.id, store)
    },

    async deleteStore(id) {
      await storeAPI.delete(id)
    }
  }
})
