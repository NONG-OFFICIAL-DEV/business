import { defineStore } from 'pinia'
import userAPI from '@/api/user' // ✅ MUST be this file

export const useUserStore = defineStore('userStore', {
  state: () => ({
    users: {
      data: []
    }
  }),

  actions: {
    async fetchUsers(param) {
      const res = await userAPI.getAll(param)
      this.users.data = res
    },

    async addUser(user) {
      await userAPI.create(user)
    },

    async updateUser(user) {
      await userAPI.update(user.id, user)
    },

    async deleteUser(id) {
      await userAPI.remove(id)
    }
  }
})
