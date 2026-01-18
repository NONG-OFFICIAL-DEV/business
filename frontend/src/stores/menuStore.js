import { defineStore } from 'pinia'
import { ref } from 'vue'
import { menuService } from '@/api/menu'

export const useMenuStore = defineStore('menu', () => {
  const menus = ref([])
  const loading = ref(false)

  async function fetchMenus() {
    loading.value = true
    try {
      const { data } = await menuService.fetchMenus()
      menus.value = data.data || []
    } catch (err) {
      console.error(err)
    } finally {
      loading.value = false
    }
  }

  async function saveMenu(menu) {
    if (menu.id) {
      return await menuService.updateMenu(menu.id, menu)
    } else {
      return await menuService.createMenu(menu)
    }
  }

  async function deleteMenu(id) {
    loading.value = true
    try {
      await menuService.deleteMenu(id)
      menus.value = menus.value.filter(m => m.id !== id)
    } catch (err) {
      console.error(err)
      throw err
    } finally {
      loading.value = false
    }
  }

  return { menus, loading, fetchMenus, saveMenu, deleteMenu }
})
