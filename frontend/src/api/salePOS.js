import aiApi from './aiApi'

export default {
  checkoutSale(saleData) {
    return aiApi.post('/sales', saleData)
  }
}
