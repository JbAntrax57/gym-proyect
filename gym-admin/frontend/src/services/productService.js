import api from './api'

class ProductService {
  /**
   * Obtener productos con paginación y filtros
   */
  async getProducts(page = 1, perPage = 15, filters = {}, sortBy = 'name', descending = false) {
    try {
      const params = {
        page,
        per_page: perPage,
        sortBy,
        descending,
        ...filters
      }
      const response = await api.get('/products', { params })
      return response.data
    } catch (error) {
      console.error('❌ ProductService - Error getting products:', error)
      throw error
    }
  }

  /**
   * Obtener un producto específico
   */
  async getProduct(id) {
    try {
      const response = await api.get(`/products/${id}`)
      return response.data
    } catch (error) {
      console.error('❌ ProductService - Error getting product:', error)
      throw error
    }
  }

  /**
   * Crear un nuevo producto
   */
  async createProduct(productData) {
    try {
      const response = await api.post('/products', productData)
      return response.data
    } catch (error) {
      console.error('❌ ProductService - Error creating product:', error)
      throw error
    }
  }

  /**
   * Actualizar un producto existente
   */
  async updateProduct(id, productData) {
    try {
      const response = await api.put(`/products/${id}`, productData)
      return response.data
    } catch (error) {
      console.error('❌ ProductService - Error updating product:', error)
      throw error
    }
  }

  /**
   * Eliminar un producto
   */
  async deleteProduct(id) {
    try {
      const response = await api.delete(`/products/${id}`)
      return response.data
    } catch (error) {
      console.error('❌ ProductService - Error deleting product:', error)
      throw error
    }
  }

  /**
   * Obtener productos activos
   */
  async getActiveProducts() {
    try {
      const response = await api.get('/products/active')
      return response.data
    } catch (error) {
      console.error('❌ ProductService - Error getting active products:', error)
      throw error
    }
  }

  /**
   * Obtener productos en stock
   */
  async getInStockProducts() {
    try {
      const response = await api.get('/products/in-stock')
      return response.data
    } catch (error) {
      console.error('❌ ProductService - Error getting in-stock products:', error)
      throw error
    }
  }

  /**
   * Obtener productos con stock bajo
   */
  async getLowStockProducts(threshold = 5) {
    try {
      const response = await api.get('/products/low-stock', { params: { threshold } })
      return response.data
    } catch (error) {
      console.error('❌ ProductService - Error getting low-stock products:', error)
      throw error
    }
  }

  /**
   * Obtener productos por categoría
   */
  async getProductsByCategory(category) {
    try {
      const response = await api.get(`/products/category/${category}`)
      return response.data
    } catch (error) {
      console.error('❌ ProductService - Error getting products by category:', error)
      throw error
    }
  }

  /**
   * Buscar productos
   */
  async searchProducts(query) {
    try {
      const response = await api.get('/products/search', { params: { query } })
      return response.data
    } catch (error) {
      console.error('❌ ProductService - Error searching products:', error)
      throw error
    }
  }

  /**
   * Actualizar stock de un producto
   */
  async updateStock(id, quantity, operation) {
    try {
      const response = await api.post(`/products/${id}/stock`, {
        quantity,
        operation
      })
      return response.data
    } catch (error) {
      console.error('❌ ProductService - Error updating stock:', error)
      throw error
    }
  }

  /**
   * Agregar stock a un producto
   */
  async addStock(id, quantity) {
    return this.updateStock(id, quantity, 'add')
  }

  /**
   * Restar stock de un producto
   */
  async subtractStock(id, quantity) {
    return this.updateStock(id, quantity, 'subtract')
  }

  /**
   * Establecer stock específico de un producto
   */
  async setStock(id, quantity) {
    return this.updateStock(id, quantity, 'set')
  }
}

export default new ProductService() 