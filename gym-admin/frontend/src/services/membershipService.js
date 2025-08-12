import api from './api'

class MembershipService {
  // Obtener todas las membresías con paginación
  async getAllMemberships(page = 1, perPage = 15, filters = {}) {
    try {
      const params = {
        page,
        per_page: perPage,
        ...filters
      }
      
      console.log('🔍 MembershipService - Parámetros de búsqueda:', params)
      
      const response = await api.get('/memberships', { params })
      console.log('📡 MembershipService - Respuesta completa de la API:', response)
      console.log('📊 MembershipService - Datos de la respuesta:', response.data)
      
      return response.data
    } catch (error) {
      console.error('❌ MembershipService - Error getting memberships:', error)
      throw error
    }
  }

  // Obtener membresía por ID
  async getMembershipById(id) {
    try {
      const response = await api.get(`/memberships/${id}`)
      return response.data
    } catch (error) {
      console.error('❌ MembershipService - Error getting membership:', error)
      throw error
    }
  }

  // Crear nueva membresía
  async createMembership(membershipData) {
    try {
      const response = await api.post('/memberships', membershipData)
      return response.data
    } catch (error) {
      console.error('❌ MembershipService - Error creating membership:', error)
      throw error
    }
  }

  // Actualizar membresía existente
  async updateMembership(id, membershipData) {
    try {
      const response = await api.put(`/memberships/${id}`, membershipData)
      return response.data
    } catch (error) {
      console.error('❌ MembershipService - Error updating membership:', error)
      throw error
    }
  }

  // Eliminar membresía
  async deleteMembership(id) {
    try {
      const response = await api.delete(`/memberships/${id}`)
      return response.data
    } catch (error) {
      console.error('❌ MembershipService - Error deleting membership:', error)
      throw error
    }
  }

  // Buscar membresías
  async searchMemberships(query) {
    try {
      const response = await api.get('/memberships/search', {
        params: { query }
      })
      return response.data
    } catch (error) {
      console.error('❌ MembershipService - Error searching memberships:', error)
      throw error
    }
  }

  // Obtener estadísticas de membresías
  async getMembershipStats() {
    try {
      const response = await api.get('/memberships/stats')
      return response.data
    } catch (error) {
      console.error('❌ MembershipService - Error getting membership stats:', error)
      throw error
    }
  }

  // Obtener tipos de membresía disponibles
  async getMembershipTypes() {
    try {
      const response = await api.get('/memberships/types')
      return response.data
    } catch (error) {
      console.error('❌ MembershipService - Error getting membership types:', error)
      throw error
    }
  }

  // Obtener estados de membresía disponibles
  async getMembershipStatuses() {
    try {
      const response = await api.get('/memberships/statuses')
      return response.data
    } catch (error) {
      console.error('❌ MembershipService - Error getting membership statuses:', error)
      throw error
    }
  }
}

export default new MembershipService()
