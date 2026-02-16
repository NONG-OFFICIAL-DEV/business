import aiApi from './aiApi'

export default {
  // Get Low Stock AI recommendations
  listSaleProductPOS() {
    return aiApi.get('/products', {
      meta: { loader: 'skeleton' }
    })
  },

  checkoutSale(saleData) {
    return aiApi.post('/sales', saleData)
  }
}
