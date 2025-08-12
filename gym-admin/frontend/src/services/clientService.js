import api from './api'

class ClientService {
  // Obtener todos los clientes con paginación
  async getClients(page = 1, perPage = 15, filters = {}, sortBy = 'name', descending = false) {
    try {
      const params = {
        page,
        per_page: perPage,
        sortBy,
        descending,
        ...filters
      }
      
      console.log('🔍 ClientService - Parámetros de búsqueda:', params)
      
      const response = await api.get('/clients', { params })
      console.log('📡 ClientService - Respuesta completa de la API:', response)
      console.log('📊 ClientService - Datos de la respuesta:', response.data)
      
      // La nueva respuesta de Laravel tiene esta estructura:
      // {
      //   success: true,
      //   data: [...], // Los clientes directamente
      //   pagination: {
      //     current_page: 1,
      //     per_page: 15,
      //     total: 10,
      //     last_page: 1,
      //     from: 1,
      //     to: 10,
      //     rowCounts: 10
      //   },
      //   message: "..."
      // }
      
      return response.data
    } catch (error) {
      console.error('❌ ClientService - Error getting clients:', error)
      throw error
    }
  }

  // Obtener un cliente específico
  async getClient(id) {
    try {
      const response = await api.get(`/clients/${id}`)
      return response.data
    } catch (error) {
      console.error('Error getting client:', error)
      throw error
    }
  }

  // Crear nuevo cliente
  async createClient(clientData) {
    try {
      const response = await api.post('/clients', clientData)
      return response.data
    } catch (error) {
      console.error('Error creating client:', error)
      throw error
    }
  }

  // Actualizar cliente existente
  async updateClient(id, clientData) {
    try {
      const response = await api.put(`/clients/${id}`, clientData)
      return response.data
    } catch (error) {
      console.error('Error updating client:', error)
      throw error
    }
  }

  // Eliminar cliente
  async deleteClient(id) {
    try {
      const response = await api.delete(`/clients/${id}`)
      return response.data
    } catch (error) {
      console.error('Error deleting client:', error)
      throw error
    }
  }

  // Buscar clientes
  async searchClients(query) {
    try {
      const response = await api.get('/clients/search', {
        params: { query }
      })
      return response.data
    } catch (error) {
      console.error('Error searching clients:', error)
      throw error
    }
  }

  // Obtener estadísticas del cliente
  async getClientStats(id) {
    try {
      const response = await api.get(`/clients/${id}/stats`)
      return response.data
    } catch (error) {
      console.error('Error getting client stats:', error)
      throw error
    }
  }
}

export default new ClientService() 