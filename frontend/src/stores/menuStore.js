import { defineStore } from 'pinia'
import { ref } from 'vue'
import { menuService } from '@/api/menu'

export const useMenuStore = defineStore('menu', () => {
  const menus = ref([])
  const loading = ref(false)

  async function fetchMenus() {
    const { data } = await menuService.fetchMenus()
    menus.value = data.data || []
  }

  async function saveMenu(menu) {
    if (menu.id) {
      return await menuService.updateMenu(menu.id, menu)
    } else {
      return await menuService.createMenu(menu)
    }
  }

  async function deleteMenu(id) {
    await menuService.deleteMenu(id)
  }

  return { menus, loading, fetchMenus, saveMenu, deleteMenu }
})
