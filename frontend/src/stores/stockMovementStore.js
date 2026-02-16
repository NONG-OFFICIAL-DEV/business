import { defineStore } from 'pinia'
import stockMovementService from '../api/stockMovement'

export const useStockMovementStore = defineStore('stockMovements', {
  state: () => ({
    movements: []
  }),

  actions: {
    async fetchMovements(productId) {
      const res = await stockMovementService.getByProductId(productId)
      this.movements = res.data.data
    },

    async createMovement(payload) {
      const res = await stockMovementService.create(payload)
      return res.data
    },

    async returnStock(payload) {
      await stockMovementService.returnStock(payload)
    },

    async adjustStock(payload) {
      await stockMovementService.adjustStock(payload)
    },

    async reportLoss(payload) {
      await stockMovementService.reportLoss(payload)
    }
  }
})
