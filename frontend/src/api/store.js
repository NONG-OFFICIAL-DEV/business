import http from './api'

export default {
  getAll() {
    return http.get('/stores')
  },
  create(payload) {
    const formData = new FormData()

    Object.keys(payload).forEach(key => {
      if (payload[key] !== null && payload[key] !== '') {
        formData.append(key, payload[key])
      }
    })

    return http.post('/stores', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
  },

  update(id, payload) {
    const formData = new FormData()

    Object.keys(payload).forEach(key => {
      if (payload[key] !== null && payload[key] !== '') {
        formData.append(key, payload[key])
      }
    })

    // Laravel needs method spoofing for file upload PUT
    formData.append('_method', 'PUT')

    return http.post(`/stores/${id}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
  },
  delete(id) {
    return http.delete(`/stores/${id}`)
  }
}
