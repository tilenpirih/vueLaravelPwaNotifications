import axios from 'axios'

const api = axios.create({
  baseURL: 'http://localhost:8000/api', // Change if your backend runs elsewhere
  withCredentials: true, // For Laravel session/cookie auth
})

export default api
