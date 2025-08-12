<template>
  <q-page class="q-pa-md">
    <!-- Header de la página -->
    <div class="row q-mb-md items-center justify-between">
      <div class="text-h5 text-weight-bold text-gym-black">
        <q-icon name="inventory" class="q-mr-md" />
        Gestión de Productos
      </div>
      <q-btn 
        class="btn-gym-primary"
        icon="add" 
        label="Nuevo Producto" 
        @click="openCreateDialog"
        no-caps
      />
    </div>

    <!-- Filtros y búsqueda -->
    <div class="row q-mb-md q-gutter-md">
      <div class="col-12 col-md-4">
        <q-input
          v-model="searchQuery"
          placeholder="Buscar productos..."
          dense
          outlined
          clearable
          @update:model-value="handleSearch"
          @keyup.enter="handleSearch"
        >
          <template v-slot:prepend>
            <q-icon name="search" />
          </template>
        </q-input>
      </div>
      <div class="col-12 col-md-3">
        <q-select
          v-model="filters.category"
          :options="categoryOptions"
          label="Categoría"
          dense
          outlined
          clearable
          @update:model-value="handleSearch"
        />
      </div>
      <div class="col-12 col-md-3">
        <q-select
          v-model="filters.stockStatus"
          :options="stockStatusOptions"
          label="Estado de Stock"
          dense
          outlined
          clearable
          @update:model-value="handleSearch"
        />
      </div>
      <div class="col-12 col-md-2">
        <q-select
          v-model="filters.status"
          :options="statusOptions"
          label="Estado"
          dense
          outlined
          clearable
          @update:model-value="handleSearch"
        />
      </div>
    </div>

    <!-- Tabla de productos -->
    <q-card class="card-gym">
      <q-table
        :rows="products"
        :columns="columns"
        :loading="loading"
        row-key="id"
        v-model:pagination="pagination"
        @request="qTableRequest"
        :rows-per-page-options="[10, 15, 25, 50]"
        class="text-sm table-gym"
      >
        <!-- Headers personalizados -->
        <template v-slot:header="props">
          <q-tr :props="props">
            <q-th
              v-for="col in props.cols"
              :key="col.name"
              :props="props"
              class="custom-header"
            >
              <div class="header-content">
                <q-icon 
                  v-if="col.name === 'name'" 
                  name="inventory" 
                  size="sm" 
                  class="q-mr-xs"
                />
                <q-icon 
                  v-else-if="col.name === 'price'" 
                  name="attach_money" 
                  size="sm" 
                  class="q-mr-xs"
                />
                <q-icon 
                  v-else-if="col.name === 'stock'" 
                  name="inventory_2" 
                  size="sm" 
                  class="q-mr-xs"
                />
                <q-icon 
                  v-else-if="col.name === 'category'" 
                  name="category" 
                  size="sm" 
                  class="q-mr-xs"
                />
                <q-icon 
                  v-else-if="col.name === 'is_active'" 
                  name="check_circle" 
                  size="sm" 
                  class="q-mr-xs"
                />
                <q-icon 
                  v-else-if="col.name === 'actions'" 
                  name="settings" 
                  size="sm" 
                  class="q-mr-xs"
                />
                {{ col.label }}
              </div>
            </q-th>
          </q-tr>
        </template>

        <!-- Columna de acciones -->
        <template v-slot:body-cell-actions="props">
          <q-td :props="props">
            <div class="row q-gutter-xs">
              <q-btn
                size="sm"
                class="btn-gym-secondary"
                icon="edit"
                @click="openEditDialog(props.row)"
                flat
                round
              >
                <q-tooltip>Editar</q-tooltip>
              </q-btn>
              <q-btn
                size="sm"
                class="btn-gym-primary"
                icon="inventory_2"
                @click="openStockDialog(props.row)"
                flat
                round
              >
                <q-tooltip>Gestionar Stock</q-tooltip>
              </q-btn>
              <q-btn
                size="sm"
                class="btn-gym-primary"
                icon="delete"
                @click="confirmDelete(props.row)"
                flat
                round
              >
                <q-tooltip>Eliminar</q-tooltip>
              </q-btn>
            </div>
          </q-td>
        </template>

        <!-- Columna de estado -->
        <template v-slot:body-cell-is_active="props">
          <q-td :props="props">
            <q-chip
              :color="props.row.is_active ? 'positive' : 'negative'"
              :label="props.row.is_active ? 'Activo' : 'Inactivo'"
              size="sm"
            />
          </q-td>
        </template>

        <!-- Columna de stock -->
        <template v-slot:body-cell-stock="props">
          <q-td :props="props">
            <q-chip
              :color="getStockColor(props.row.stock)"
              :label="`${props.row.stock} unidades`"
              size="sm"
            />
          </q-td>
        </template>

        <!-- Columna de precio -->
        <template v-slot:body-cell-price="props">
          <q-td :props="props">
            <span class="text-weight-bold text-gym-red">
              ${{ props.row.price }}
            </span>
          </q-td>
        </template>

        <!-- Mensaje cuando no hay productos -->
        <template v-slot:no-data>
          <div class="full-width row flex-center q-pa-lg">
            <div class="text-center">
              <q-icon name="inventory_2" size="4rem" color="grey-5" />
              <div class="text-h6 text-grey-6 q-mt-md">No se encontraron productos</div>
              <div class="text-caption text-grey-5">
                Intenta ajustar los filtros o crear un nuevo producto
              </div>
            </div>
          </div>
        </template>
      </q-table>
    </q-card>

    <!-- Modal para crear/editar producto -->
    <q-dialog v-model="showDialog" persistent>
      <q-card style="min-width: 600px" class="modal-gym-enhanced">
        <!-- Header del modal -->
        <q-card-section class="modal-header-gym q-pa-md">
          <div class="row items-center full-width">
            <div class="col">
              <div class="text-h5 text-gym-white text-weight-bold">
                <q-icon 
                  :name="isEditing ? 'edit' : 'add'" 
                  size="2rem" 
                  class="q-mr-md" 
                />
                {{ isEditing ? 'Editar Producto' : 'Nuevo Producto' }}
              </div>
              <div class="text-subtitle2 text-gym-text-secondary q-mt-xs">
                {{ isEditing ? 'Modifica la información del producto' : 'Completa los datos del nuevo producto' }}
              </div>
            </div>
            <div class="col-auto">
              <q-btn 
                icon="close" 
                flat 
                round 
                dense 
                @click="closeDialog"
                class="text-gym-white"
                size="lg"
              />
            </div>
          </div>
        </q-card-section>

        <!-- Contenido del modal -->
        <q-card-section class="q-pa-lg">
          <q-form @submit="handleSubmit" class="q-gutter-lg">
            <!-- Nombre del producto -->
            <div class="row">
              <div class="col-12">
                <q-input
                  v-model="form.name"
                  label="Nombre del producto *"
                  outlined
                  dense
                  class="input-gym-enhanced"
                  :rules="[val => !!val || 'El nombre es requerido']"
                  @blur="formatName"
                  hint="Se convertirá a mayúsculas automáticamente"
                  persistent-hint
                >
                  <template v-slot:prepend>
                    <q-icon name="inventory" color="var(--gym-red)" />
                  </template>
                </q-input>
              </div>
            </div>

            <!-- Descripción -->
            <div class="row">
              <div class="col-12">
                <q-input
                  v-model="form.description"
                  label="Descripción"
                  outlined
                  dense
                  class="input-gym-enhanced"
                  type="textarea"
                  rows="3"
                >
                  <template v-slot:prepend>
                    <q-icon name="description" color="var(--gym-red)" />
                  </template>
                </q-input>
              </div>
            </div>

            <!-- Precio y Stock -->
            <div class="row q-gutter-md">
              <div class="col-12 col-md-6">
                <q-input
                  v-model.number="form.price"
                  label="Precio *"
                  outlined
                  dense
                  class="input-gym-enhanced"
                  type="number"
                  step="0.01"
                  min="0"
                  :rules="[val => val > 0 || 'El precio debe ser mayor a 0']"
                  hint="Precio en pesos"
                  persistent-hint
                >
                  <template v-slot:prepend>
                    <q-icon name="attach_money" color="var(--gym-red)" />
                  </template>
                </q-input>
              </div>
              <div class="col-12 col-md-6">
                <q-input
                  v-model.number="form.stock"
                  label="Stock inicial *"
                  outlined
                  dense
                  class="input-gym-enhanced"
                  type="number"
                  min="0"
                  :rules="[val => val >= 0 || 'El stock no puede ser negativo']"
                  hint="Cantidad disponible"
                  persistent-hint
                >
                  <template v-slot:prepend>
                    <q-icon name="inventory_2" color="var(--gym-red)" />
                  </template>
                </q-input>
              </div>
            </div>

            <!-- Categoría y Estado -->
            <div class="row q-gutter-md">
              <div class="col-12 col-md-6">
                <q-select
                  v-model="form.category"
                  :options="categoryOptions"
                  label="Categoría *"
                  outlined
                  dense
                  class="input-gym-enhanced"
                  :rules="[val => !!val || 'La categoría es requerida']"
                  hint="Selecciona una categoría"
                  persistent-hint
                >
                  <template v-slot:prepend>
                    <q-icon name="category" color="var(--gym-red)" />
                  </template>
                </q-select>
              </div>
              <div class="col-12 col-md-6">
                <div class="text-subtitle2 text-gym-black q-mb-sm">Estado del Producto</div>
                <q-toggle
                  v-model="form.is_active"
                  :label="form.is_active ? 'Producto Activo' : 'Producto Inactivo'"
                  :color="form.is_active ? 'positive' : 'negative'"
                  size="lg"
                  class="toggle-gym"
                />
              </div>
            </div>

            <!-- Botones de acción -->
            <div class="row justify-end q-gutter-md q-mt-lg">
              <q-btn
                label="Cancelar"
                class="btn-gym-secondary"
                flat
                @click="closeDialog"
                size="lg"
              />
              <q-btn
                :label="isEditing ? 'Actualizar Producto' : 'Crear Producto'"
                type="submit"
                class="btn-gym-primary"
                :loading="submitting"
                size="lg"
                :icon="isEditing ? 'update' : 'add'"
              />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>

    <!-- Modal de gestión de stock -->
    <q-dialog v-model="showStockDialog" persistent>
      <q-card style="min-width: 500px" class="modal-gym-enhanced">
        <q-card-section class="modal-header-gym q-pa-md">
          <div class="text-h5 text-gym-white text-weight-bold">
            <q-icon name="inventory_2" size="2rem" class="q-mr-md" />
            Gestión de Stock
          </div>
        </q-card-section>

        <q-card-section class="q-pa-lg">
          <div class="row q-mb-lg">
            <div class="col-12">
              <div class="text-h6 text-gym-black">
                {{ selectedProduct?.name }}
              </div>
              <div class="text-subtitle2 text-gym-red">
                Stock actual: {{ selectedProduct?.stock }} unidades
              </div>
            </div>
          </div>

          <div class="row q-gutter-md">
            <div class="col-12 col-md-6">
              <q-input
                v-model.number="stockForm.quantity"
                label="Cantidad"
                outlined
                dense
                class="input-gym-enhanced"
                type="number"
                min="1"
                :rules="[val => val > 0 || 'La cantidad debe ser mayor a 0']"
              />
            </div>
            <div class="col-12 col-md-6">
              <q-select
                v-model="stockForm.operation"
                :options="stockOperations"
                label="Operación"
                outlined
                dense
                class="input-gym-enhanced"
              />
            </div>
          </div>
        </q-card-section>

        <q-card-actions align="right" class="q-pa-lg q-pt-none">
          <q-btn
            label="Cancelar"
            class="btn-gym-secondary"
            flat
            @click="closeStockDialog"
            size="lg"
          />
          <q-btn
            label="Actualizar Stock"
            class="btn-gym-primary"
            @click="handleStockUpdate"
            size="lg"
            :loading="updatingStock"
            icon="update"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Modal de confirmación para eliminar -->
    <q-dialog v-model="showDeleteDialog">
      <q-card class="modal-gym-enhanced delete-confirmation-modal">
        <q-card-section class="modal-header-gym q-pa-md">
          <div class="row items-center full-width">
            <div class="col">
              <div class="text-h5 text-gym-white text-weight-bold">
                <q-icon name="warning" size="2rem" class="q-mr-md" />
                Confirmar Eliminación
              </div>
              <div class="text-subtitle2 text-gym-text-secondary q-mt-xs">
                Esta acción no se puede deshacer
              </div>
            </div>
          </div>
        </q-card-section>

        <q-card-section class="q-pa-lg">
          <div class="row items-center q-mb-lg">
            <div class="col-auto">
              <q-avatar 
                icon="inventory" 
                color="var(--gym-red)" 
                text-color="white" 
                size="4rem"
                class="q-mr-md"
              />
            </div>
            <div class="col">
              <div class="text-h6 text-gym-black">
                ¿Eliminar <strong>{{ selectedProduct?.name }}</strong>?
              </div>
              <div class="text-body2 text-gym-red q-mt-xs">
                Se eliminarán todos los datos asociados a este producto
              </div>
            </div>
          </div>

          <div class="row q-gutter-md">
            <div class="col-12 col-md-6">
              <div class="text-caption text-gym-black q-mb-xs">Categoría:</div>
              <div class="text-body1">{{ selectedProduct?.category }}</div>
            </div>
            <div class="col-12 col-md-6">
              <div class="text-caption text-gym-black q-mb-xs">Precio:</div>
              <div class="text-body1">${{ selectedProduct?.price }}</div>
            </div>
          </div>
        </q-card-section>

        <q-card-actions align="right" class="q-pa-lg q-pt-none">
          <q-btn 
            label="Cancelar" 
            class="btn-gym-secondary" 
            flat 
            v-close-popup 
            size="lg"
          />
          <q-btn 
            label="Eliminar Producto" 
            class="btn-gym-primary" 
            @click="deleteProduct" 
            size="lg"
            icon="delete_forever"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { Notify } from 'quasar'
import productService from 'src/services/productService'

export default defineComponent({
  name: 'ProductsPage',
  setup() {
    const $q = useQuasar()
    
    // Estado reactivo
    const products = ref([])
    const loading = ref(false)
    const submitting = ref(false)
    const updatingStock = ref(false)
    const showDialog = ref(false)
    const showStockDialog = ref(false)
    const showDeleteDialog = ref(false)
    const isEditing = ref(false)
    const searchQuery = ref('')
    const selectedProduct = ref(null)
    
    // Paginación
    const pagination = ref({
      sortBy: 'name',
      descending: false,
      page: 1,
      rowsPerPage: 15,
      rowsNumber: 0
    })

    // Filtros
    const filters = ref({
      category: null,
      stockStatus: null,
      status: null
    })

    // Formulario
    const form = ref({
      name: '',
      description: '',
      price: 0,
      stock: 0,
      category: '',
      is_active: true
    })

    // Formulario de stock
    const stockForm = ref({
      quantity: 1,
      operation: 'add'
    })

    // Opciones de filtro
    const categoryOptions = [
      'Bebidas',
      'Suplementos',
      'Accesorios',
      'Ropa Deportiva',
      'Equipamiento',
      'Otros'
    ]

    const stockStatusOptions = [
      { label: 'Todos', value: null },
      { label: 'En Stock', value: 'in_stock' },
      { label: 'Stock Bajo', value: 'low_stock' },
      { label: 'Sin Stock', value: 'out_of_stock' }
    ]

    const statusOptions = [
      { label: 'Todos', value: null },
      { label: 'Activos', value: true },
      { label: 'Inactivos', value: false }
    ]

    const stockOperations = [
      { label: 'Agregar Stock', value: 'add' },
      { label: 'Restar Stock', value: 'subtract' },
      { label: 'Establecer Stock', value: 'set' }
    ]

    // Columnas de la tabla
    const columns = [
      {
        name: 'name',
        required: true,
        label: 'Nombre',
        align: 'left',
        field: 'name',
        sortable: true
      },
      {
        name: 'description',
        label: 'Descripción',
        align: 'left',
        field: 'description',
        style: 'max-width: 200px'
      },
      {
        name: 'price',
        label: 'Precio',
        align: 'center',
        field: 'price',
        sortable: true
      },
      {
        name: 'stock',
        label: 'Stock',
        align: 'center',
        field: 'stock',
        sortable: true
      },
      {
        name: 'category',
        label: 'Categoría',
        align: 'center',
        field: 'category',
        sortable: true
      },
      {
        name: 'is_active',
        label: 'Estado',
        align: 'center',
        field: 'is_active',
        sortable: true
      },
      {
        name: 'actions',
        label: 'Acciones',
        align: 'center',
        field: 'actions'
      }
    ]

    // Función para mostrar notificaciones
    const showNotification = (message, type = 'positive', icon = 'check_circle') => {
      try {
        Notify({
          type: type,
          message: message,
          icon: icon,
          position: 'top-right',
          timeout: 3000
        })
      } catch (error) {
        console.error('Error al crear notificación:', error)
        alert(`${type.toUpperCase()}: ${message}`)
      }
    }

    // Métodos principales
    const fetchFromServer = () => {
      qTableRequest({
        pagination: pagination.value,
        filter: searchQuery.value
      })
    }

    const qTableRequest = async (props) => {
      try {
        loading.value = true
        
        // Actualizar paginación local
        pagination.value = props.pagination
        
        // Preparar parámetros
        const params = { 
          ...filters.value,
          search: props.filter
        }
        
        const response = await productService.getProducts(
          pagination.value.page,
          pagination.value.rowsPerPage,
          params,
          pagination.value.sortBy,
          pagination.value.descending
        )
        
        if (response && response.success && response.data) {
          products.value = response.data.data || []
          
          if (response.data.total !== undefined) {
            pagination.value.rowsNumber = response.data.total
          }
        } else {
          products.value = []
        }
        
      } catch (error) {
        console.error('❌ Error loading products:', error)
        products.value = []
        showNotification('Error al cargar los productos', 'negative', 'error')
      } finally {
        loading.value = false
      }
    }

    const handleSearch = () => {
      pagination.value.page = 1
      fetchFromServer()
    }

    const openCreateDialog = () => {
      isEditing.value = false
      selectedProduct.value = null
      resetForm()
      showDialog.value = true
    }

    const openEditDialog = (product) => {
      isEditing.value = true
      selectedProduct.value = product
      form.value = { ...product }
      showDialog.value = true
    }

    const openStockDialog = (product) => {
      selectedProduct.value = product
      stockForm.value.quantity = 1
      stockForm.value.operation = 'add'
      showStockDialog.value = true
    }

    const resetForm = () => {
      form.value = {
        name: '',
        description: '',
        price: 0,
        stock: 0,
        category: '',
        is_active: true
      }
    }

    const handleSubmit = async () => {
      try {
        submitting.value = true
        
        if (isEditing.value) {
          await productService.updateProduct(selectedProduct.value.id, form.value)
          showNotification('Producto actualizado exitosamente', 'positive', 'check_circle')
        } else {
          await productService.createProduct(form.value)
          showNotification('Producto creado exitosamente', 'positive', 'check_circle')
        }
        
        closeDialog()
        await fetchFromServer()
        
      } catch (error) {
        console.error('Error saving product:', error)
        showNotification(
          error.response?.data?.message || 'Error al guardar el producto', 
          'negative', 
          'error'
        )
      } finally {
        submitting.value = false
      }
    }

    const handleStockUpdate = async () => {
      try {
        updatingStock.value = true
        
        await productService.updateStock(
          selectedProduct.value.id,
          stockForm.value.quantity,
          stockForm.value.operation
        )
        
        showNotification('Stock actualizado exitosamente', 'positive', 'check_circle')
        closeStockDialog()
        await fetchFromServer()
        
      } catch (error) {
        console.error('Error updating stock:', error)
        showNotification(
          error.response?.data?.message || 'Error al actualizar el stock', 
          'negative', 
          'error'
        )
      } finally {
        updatingStock.value = false
      }
    }

    const confirmDelete = (product) => {
      selectedProduct.value = product
      showDeleteDialog.value = true
    }

    const deleteProduct = async () => {
      try {
        await productService.deleteProduct(selectedProduct.value.id)
        showNotification('Producto eliminado exitosamente', 'positive', 'check_circle')
        showDeleteDialog.value = false
        selectedProduct.value = null
        await fetchFromServer()
      } catch (error) {
        console.error('Error deleting product:', error)
        showNotification('Error al eliminar el producto', 'negative', 'error')
      }
    }

    const closeDialog = () => {
      showDialog.value = false
      resetForm()
      selectedProduct.value = null
    }

    const closeStockDialog = () => {
      showStockDialog.value = false
      selectedProduct.value = null
    }

    // Función para formatear nombre a mayúsculas
    const formatName = () => {
      if (form.value.name) {
        form.value.name = form.value.name.toUpperCase()
      }
    }

    // Función para obtener color del stock
    const getStockColor = (stock) => {
      if (stock <= 0) return 'negative'
      if (stock <= 5) return 'warning'
      return 'positive'
    }

    // Cargar datos al montar el componente
    onMounted(() => {
      fetchFromServer()
    })

    return {
      // Estado
      products,
      loading,
      submitting,
      updatingStock,
      showDialog,
      showStockDialog,
      showDeleteDialog,
      isEditing,
      searchQuery,
      selectedProduct,
      pagination,
      form,
      stockForm,
      filters,
      categoryOptions,
      stockStatusOptions,
      statusOptions,
      stockOperations,
      columns,
      
      // Métodos
      fetchFromServer,
      qTableRequest,
      handleSearch,
      openCreateDialog,
      openEditDialog,
      openStockDialog,
      resetForm,
      handleSubmit,
      handleStockUpdate,
      confirmDelete,
      deleteProduct,
      closeDialog,
      closeStockDialog,
      formatName,
      getStockColor
    }
  }
})
</script>

<style scoped>
/* Estilos específicos de la página de productos usando el tema global del gym */

/* Personalización del header de la tabla usando variables globales */
.q-table thead th {
  background: var(--gym-gradient-black) !important;
  color: var(--gym-text-primary) !important;
  font-weight: 600 !important;
  font-size: 0.9rem !important;
  text-transform: uppercase !important;
  letter-spacing: 0.5px !important;
  border: none !important;
  box-shadow: var(--gym-shadow-sm) !important;
}

/* Hover en el header - Cambia a rojo */
.q-table thead th:hover {
  background: var(--gym-gradient-red) !important;
  transition: var(--gym-transition);
  transform: translateY(-1px) !important;
  box-shadow: var(--gym-shadow-red) !important;
}

/* Estilos para headers personalizados usando variables globales */
.custom-header {
  background: var(--gym-gradient-black) !important;
  color: var(--gym-text-primary) !important;
  font-weight: 600 !important;
  text-align: center !important;
  padding: var(--gym-spacing-md) var(--gym-spacing-sm) !important;
  box-shadow: var(--gym-shadow-sm) !important;
}

.header-content {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: var(--gym-spacing-xs);
}

/* Hover en headers personalizados - Cambia a rojo */
.custom-header:hover {
  background: var(--gym-gradient-red) !important;
  transition: var(--gym-transition);
  transform: translateY(-1px) !important;
  box-shadow: var(--gym-shadow-red) !important;
}

/* Efectos adicionales para el tema Gym */
.q-table {
  border-radius: var(--gym-radius-md) !important;
  overflow: hidden !important;
}

/* Separadores entre columnas con tema gym */
.q-table thead th:not(:last-child) {
  border-right: 1px solid var(--gym-border-color) !important;
}

/* Iconos de ordenamiento con tema gym */
.q-table thead th .q-icon {
  color: var(--gym-red) !important;
  opacity: 0.8;
}

/* Bordes del header con tema gym */
.q-table thead th:first-child {
  border-top-left-radius: var(--gym-radius-md);
}

.q-table thead th:last-child {
  border-top-right-radius: var(--gym-radius-md);
}

/* Animación de entrada para los headers */
.custom-header {
  animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Estilos específicos para los filtros */
.q-input, .q-select {
  transition: var(--gym-transition);
}

.q-input:focus, .q-select:focus {
  border-color: var(--gym-red) !important;
  box-shadow: 0 0 0 3px rgba(211, 47, 47, 0.1) !important;
}

/* Estilos para los chips de estado */
.q-chip {
  font-weight: 600;
  transition: var(--gym-transition);
}

.q-chip:hover {
  transform: scale(1.05);
}

/* ========================================
   ESTILOS DEL MODAL MEJORADO
   ======================================== */

/* Modal mejorado del gym */
.modal-gym-enhanced {
  border: 3px solid var(--gym-red) !important;
  border-radius: var(--gym-radius-lg) !important;
  box-shadow: var(--gym-shadow-lg) !important;
  overflow: hidden !important;
}

/* Header del modal con colores del gym */
.modal-header-gym {
  background: var(--gym-gradient-black) !important;
  color: var(--gym-text-primary) !important;
  border-bottom: 3px solid var(--gym-red) !important;
  position: relative !important;
}

.modal-header-gym::after {
  content: '' !important;
  position: absolute !important;
  bottom: 0 !important;
  left: 0 !important;
  right: 0 !important;
  height: 2px !important;
  background: linear-gradient(90deg, var(--gym-red) 0%, var(--gym-red-light) 50%, var(--gym-red) 100%) !important;
  animation: shimmer 2s infinite !important;
}

@keyframes shimmer {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(100%); }
}

/* Inputs mejorados del gym */
.input-gym-enhanced {
  background: rgba(211, 47, 47, 0.05) !important;
  border: 2px solid var(--gym-border-color) !important;
  border-radius: var(--gym-radius-md) !important;
  transition: var(--gym-transition) !important;
}

.input-gym-enhanced:hover {
  background: rgba(211, 47, 47, 0.08) !important;
  border-color: var(--gym-red) !important;
  transform: translateY(-1px) !important;
  box-shadow: var(--gym-shadow-sm) !important;
}

.input-gym-enhanced:focus-within {
  background: rgba(211, 47, 47, 0.1) !important;
  border-color: var(--gym-red) !important;
  box-shadow: 0 0 0 3px rgba(211, 47, 47, 0.15) !important;
  transform: translateY(-2px) !important;
}

/* Toggle mejorado del gym */
.toggle-gym {
  transition: var(--gym-transition) !important;
}

.toggle-gym:hover {
  transform: scale(1.05) !important;
}

/* Hints del formulario */
.q-field__hint {
  color: var(--gym-red) !important;
  font-size: 0.8rem !important;
  font-style: italic !important;
}

/* Iconos de los inputs */
.input-gym-enhanced .q-field__prepend .q-icon {
  transition: var(--gym-transition) !important;
}

.input-gym-enhanced:hover .q-field__prepend .q-icon {
  transform: scale(1.1) !important;
  color: var(--gym-red-dark) !important;
}

/* Botones del modal */
.modal-gym-enhanced .btn-gym-primary,
.modal-gym-enhanced .btn-gym-secondary {
  font-weight: 600 !important;
  letter-spacing: 0.5px !important;
  text-transform: uppercase !important;
  transition: var(--gym-transition) !important;
}

.modal-gym-enhanced .btn-gym-primary:hover,
.modal-gym-enhanced .btn-gym-secondary:hover {
  transform: translateY(-3px) !important;
  box-shadow: var(--gym-shadow-md) !important;
}

/* Animaciones del modal */
.modal-gym-enhanced {
  animation: modalSlideIn 0.4s ease-out !important;
}

@keyframes modalSlideIn {
  from {
    opacity: 0;
    transform: scale(0.9) translateY(-20px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

/* Responsive del modal */
@media (max-width: 768px) {
  .modal-gym-enhanced {
    min-width: 95vw !important;
    margin: 10px !important;
  }
  
  .modal-header-gym .text-h5 {
    font-size: 1.2rem !important;
  }
  
  .modal-header-gym .q-icon {
    font-size: 1.5rem !important;
  }
}

/* Modal de confirmación de eliminación */
.delete-confirmation-modal {
  border-color: var(--gym-error) !important;
  box-shadow: 0 8px 32px rgba(211, 47, 47, 0.3) !important;
}

.delete-confirmation-modal .modal-header-gym {
  background: linear-gradient(135deg, var(--gym-error) 0%, var(--gym-red-dark) 100%) !important;
  border-bottom-color: var(--gym-error) !important;
}

.delete-confirmation-modal .modal-header-gym::after {
  background: linear-gradient(90deg, var(--gym-error) 0%, var(--gym-red) 50%, var(--gym-error) 100%) !important;
}

/* Efectos especiales para el modal de eliminación */
.delete-confirmation-modal .q-avatar {
  animation: pulseWarning 2s infinite !important;
}

@keyframes pulseWarning {
  0%, 100% {
    transform: scale(1);
    box-shadow: 0 0 0 0 rgba(211, 47, 47, 0.7);
  }
  50% {
    transform: scale(1.05);
    box-shadow: 0 0 0 10px rgba(211, 47, 47, 0);
  }
}
</style> 