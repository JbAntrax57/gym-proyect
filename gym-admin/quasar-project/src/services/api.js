import axios from 'axios'

// Configuración base de la API
const api = axios.create({
  baseURL: 'http://127.0.0.1:8000/api',
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// Interceptor para requests
api.interceptors.request.use(
  (config) => {
    // Aquí se puede agregar token de autenticación si es necesario
    console.log('API Request:', config.method?.toUpperCase(), config.url)
    return config
  },
  (error) => {
    console.error('API Request Error:', error)
    return Promise.reject(error)
  }
)

// Interceptor para responses
api.interceptors.response.use(
  (response) => {
    console.log('API Response:', response.status, response.config.url)
    return response
  },
  (error) => {
    console.error('API Response Error:', error.response?.status, error.response?.data)
    
    // Manejo de errores comunes
    if (error.response?.status === 422) {
      // Error de validación
      console.error('Validation Errors:', error.response.data.errors)
    } else if (error.response?.status === 404) {
      console.error('Resource not found')
    } else if (error.response?.status >= 500) {
      console.error('Server error')
    }
    
    return Promise.reject(error)
  }
)

export default api 