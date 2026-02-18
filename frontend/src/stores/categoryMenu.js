import { defineStore } from 'pinia'
import { ref } from 'vue'
import { categoryMenuService } from '@/api/categoryMenu'

export const useCategoryMenuStore = defineStore('categoryMenu', () => {
  const items = ref([])

  async function fetchAllMenuCategory(loading) {
    const { data } = await categoryMenuService.getAll(loading)
    items.value = data.data || []
  }

  async function createItem(payload) {
   return await categoryMenuService.create(payload)
  }

  async function updateItem(id, payload) {
    return await categoryMenuService.update(id, payload)
  }

  async function deleteItem(id) {
    await categoryMenuService.delete(id)
    items.value = items.value.filter(item => item.id !== id)
  }

  return {
    items,
    fetchAllMenuCategory,
    createItem,
    updateItem,
    deleteItem
  }
})
