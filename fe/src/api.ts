import axios from 'axios'

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
})

// Add a request interceptor to attach the token
api.interceptors.request.use(config => {
  const token = localStorage.getItem('access_token') // or however you store it
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
}, error => {
  return Promise.reject(error)
})

export default api