import { defineStore } from 'pinia'
import tableService from '@/api/table'

export const useDiningTableStore = defineStore('diningTable', {
  state: () => ({
    tables: [],
    loading: false
  }),

  actions: {
    /* -------------------------
      FETCH ALL TABLES
    --------------------------*/
    async fetchTables(params = {}) {
      this.loading = true
      try {
        const res = await tableService.getAllTables(params)
        this.tables = res.data.data
      } finally {
        this.loading = false
      }
    },

    /* -------------------------
      CREATE
    --------------------------*/
    async addTable(data) {
      const res = await tableService.createTable(data)
      return res.data
    },

    /* -------------------------
      UPDATE
    --------------------------*/
    async updateTable(table) {
      const res = await tableService.updateTable(table.id, table)
      return res.data
    },

    /* -------------------------
      DELETE
    --------------------------*/
    async deleteTable(id) {
      await tableService.deleteTable(id)
    },

    /* -------------------------
      UPDATE STATUS (POS / KDS)
    --------------------------*/
    async updateTableStatus(id, status) {
      const res = await tableService.updateStatus(id, status)
      return res.data
    }
  }
})
