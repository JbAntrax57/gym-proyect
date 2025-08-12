<template>
  <q-page class="q-pa-md">
    <div class="row q-mb-md items-center justify-between">
      <div class="text-h5 text-weight-bold">Gestión de Clientes</div>
      <q-btn 
        color="primary" 
        icon="add" 
        label="Nuevo Cliente" 
        @click="openCreateDialog"
        no-caps
      />
    </div>

    <!-- Filtros y búsqueda -->
    <div class="row q-mb-md q-gutter-md">
      <div class="col-12 col-md-4">
        <q-input
          v-model="searchQuery"
          placeholder="Buscar clientes..."
          dense
          outlined
          clearable
          @update:model-value="handleSearch"
          @keyup.enter="handleSearch"
        >
          <template v-slot:prepend>
            <q-icon name="search" />
          </template>
          <template v-slot:append v-if="searchQuery">
            <q-btn
              flat
              round
              dense
              icon="close"
              @click="clearSearch"
              color="grey"
            />
          </template>
        </q-input>
      </div>
      <div class="col-12 col-md-3">
        <q-select
          v-model="statusFilter"
          :options="statusOptions"
          label="Estado"
          dense
          outlined
          clearable
          @update:model-value="handleSearch"
        />
      </div>
      <div class="col-12 col-md-3">
        <q-btn
          color="secondary"
          icon="refresh"
          label="Recargar"
          @click="loadClients"
          dense
          outline
        />
      </div>
    </div>

    <!-- Tabla de clientes -->
    <q-card class="shadow-1">
      <q-table
        :rows="clients"
        :columns="columns"
        :loading="loading"
        :pagination="pagination"
        row-key="id"
        @request="onRequest"
        :rows-per-page-options="[10, 15, 25, 50]"
        class="text-sm"
      >
        <!-- Columna de acciones -->
        <template v-slot:body-cell-actions="props">
          <q-td :props="props">
            <div class="row q-gutter-xs">
              <q-btn
                size="sm"
                color="info"
                icon="edit"
                @click="openEditDialog(props.row)"
                flat
                round
              >
                <q-tooltip>Editar</q-tooltip>
              </q-btn>
              <q-btn
                size="sm"
                color="negative"
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

        <!-- Columna de puntos de lealtad -->
        <template v-slot:body-cell-loyalty_points="props">
          <q-td :props="props">
            <q-chip
              color="warning"
              :label="`${props.row.loyalty_points} pts`"
              size="sm"
            />
          </q-td>
        </template>
        
        <!-- Mensaje cuando no hay clientes -->
        <template v-slot:no-data>
          <div class="full-width row flex-center q-pa-lg">
            <div class="text-center">
              <q-icon name="sentiment_dissatisfied" size="4rem" color="grey-5" />
              <div class="text-h6 text-grey-6 q-mt-md">No se encontraron clientes</div>
              <div class="text-caption text-grey-5">
                Intenta ajustar los filtros o crear un nuevo cliente
              </div>
            </div>
          </div>
        </template>
      </q-table>
      
      <!-- Información de paginación -->
      <div class="row q-mt-md q-gutter-md items-center justify-between" v-if="clients.length > 0">
        <div class="col-12 col-md-6">
          <q-chip
            color="info"
            text-color="white"
            :label="`Mostrando ${clients.length} de ${pagination.rowsNumber} clientes`"
            size="sm"
          />
        </div>
        <div class="col-12 col-md-6 text-right">
          <q-chip
            color="secondary"
            text-color="white"
            :label="`Página ${pagination.page} de ${Math.ceil(pagination.rowsNumber / pagination.rowsPerPage)}`"
            size="sm"
          />
        </div>
      </div>
    </q-card>

    <!-- Modal para crear/editar cliente -->
    <q-dialog v-model="showDialog" persistent>
      <q-card style="min-width: 500px">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">{{ isEditing ? 'Editar Cliente' : 'Nuevo Cliente' }}</div>
          <q-space />
          <q-btn icon="close" flat round dense @click="closeDialog" />
        </q-card-section>

        <q-card-section>
          <q-form @submit="handleSubmit" class="q-gutter-md">
            <div class="row q-gutter-md">
              <div class="col-12 col-md-6">
                <q-input
                  v-model="form.name"
                  label="Nombre completo *"
                  outlined
                  dense
                  :rules="[val => !!val || 'El nombre es requerido']"
                />
              </div>
              <div class="col-12 col-md-6">
                <q-input
                  v-model="form.email"
                  label="Email *"
                  type="email"
                  outlined
                  dense
                  :rules="[
                    val => !!val || 'El email es requerido',
                    val => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val) || 'Email inválido'
                  ]"
                />
              </div>
            </div>

            <div class="row q-gutter-md">
              <div class="col-12 col-md-6">
                <q-input
                  v-model="form.phone"
                  label="Teléfono *"
                  outlined
                  dense
                  :rules="[val => !!val || 'El teléfono es requerido']"
                />
              </div>
              <div class="col-12 col-md-6">
                <q-input
                  v-model="form.address"
                  label="Dirección"
                  outlined
                  dense
                />
              </div>
            </div>

            <div class="row q-gutter-md">
              <div class="col-12 col-md-6">
                <q-toggle
                  v-model="form.is_active"
                  label="Cliente activo"
                  color="positive"
                />
              </div>
              <div class="col-12 col-md-6">
                <q-input
                  v-model.number="form.loyalty_points"
                  label="Puntos de lealtad"
                  type="number"
                  outlined
                  dense
                  min="0"
                />
              </div>
            </div>

            <div class="row justify-end q-gutter-sm">
              <q-btn
                label="Cancelar"
                color="grey"
                flat
                @click="closeDialog"
              />
              <q-btn
                :label="isEditing ? 'Actualizar' : 'Crear'"
                type="submit"
                color="primary"
                :loading="submitting"
              />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>

    <!-- Modal de confirmación para eliminar -->
    <q-dialog v-model="showDeleteDialog">
      <q-card>
        <q-card-section class="row items-center">
          <q-avatar icon="warning" color="warning" text-color="white" />
          <span class="q-ml-sm">¿Estás seguro de que quieres eliminar este cliente?</span>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancelar" color="grey" v-close-popup />
          <q-btn flat label="Eliminar" color="negative" @click="deleteClient" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { Notify } from 'quasar'
import clientService from 'src/services/clientService'

export default defineComponent({
  name: 'ClientsPage',
  setup() {
    const $q = useQuasar()
    
    // Estado reactivo
    const clients = ref([])
    const loading = ref(false)
    const submitting = ref(false)
    const showDialog = ref(false)
    const showDeleteDialog = ref(false)
    const isEditing = ref(false)
    const searchQuery = ref('')
    const statusFilter = ref(null)
    const selectedClient = ref(null)
    
    // Paginación
    const pagination = ref({
      sortBy: 'name',
      descending: false,
      page: 1,
      rowsPerPage: 15,
      rowsNumber: 0,
      rowsPerPageOptions: [10, 15, 25, 50]
    })

    // Formulario
    const form = ref({
      name: '',
      email: '',
      phone: '',
      address: '',
      is_active: true,
      loyalty_points: 0
    })

    // Opciones de filtro
    const statusOptions = [
      { label: 'Todos', value: null },
      { label: 'Activos', value: true },
      { label: 'Inactivos', value: false }
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
        name: 'email',
        label: 'Email',
        align: 'left',
        field: 'email',
        sortable: true
      },
      {
        name: 'phone',
        label: 'Teléfono',
        align: 'left',
        field: 'phone'
      },
      {
        name: 'loyalty_points',
        label: 'Puntos',
        align: 'center',
        field: 'loyalty_points',
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
        name: 'created_at',
        label: 'Fecha de registro',
        align: 'center',
        field: 'created_at',
        sortable: true,
        format: (val) => new Date(val).toLocaleDateString('es-ES')
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
      console.log('🔔 ClientsPage - Intentando mostrar notificación:', { message, type, icon })
      
      try {
        // Intentar diferentes formas de usar Notify en Quasar v2
        if (typeof Notify === 'function') {
          // Forma 1: Notify directo
          Notify({
            type: type,
            message: message,
            icon: icon,
            position: 'top-right',
            timeout: 3000
          })
        } else if (Notify && typeof Notify.create === 'function') {
          // Forma 2: Notify.create (Quasar v1)
          Notify.create({
            type: type,
            message: message,
            icon: icon,
            position: 'top-right',
            timeout: 3000
          })
        } else if ($q && $q.notify) {
          // Forma 3: $q.notify
          $q.notify({
            type: type,
            message: message,
            icon: icon,
            position: 'top-right',
            timeout: 3000
          })
        } else {
          throw new Error('Notify no disponible')
        }
        
        console.log('✅ ClientsPage - Notificación creada exitosamente')
      } catch (error) {
        console.error('❌ ClientsPage - Error al crear notificación:', error)
        // Fallback: usar alert si Notify falla
        alert(`${type.toUpperCase()}: ${message}`)
      }
    }

    // Métodos
    const loadClients = async () => {
      try {
        console.log('🔄 ClientsPage - INICIANDO loadClients...')
        loading.value = true
        const filters = {}
        
        if (statusFilter.value !== null) {
          filters.is_active = statusFilter.value
        }
        
        if (searchQuery.value.trim()) {
          filters.search = searchQuery.value.trim()
        }
        
        console.log('🔄 ClientsPage - Iniciando carga de clientes...')
        console.log('🔍 ClientsPage - Filtros aplicados:', filters)
        console.log('📄 ClientsPage - Página actual:', pagination.value.page)
        console.log('📊 ClientsPage - Filas por página:', pagination.value.rowsPerPage)
        
        const response = await clientService.getClients(
          pagination.value.page,
          pagination.value.rowsPerPage,
          filters,
          pagination.value.sortBy,
          pagination.value.descending
        )
        
        console.log('📡 ClientsPage - Respuesta del servicio:', response)
        
        // La nueva respuesta de Laravel tiene esta estructura:
        // response = {
        //   success: true,
        //   data: [...], // Los clientes directamente
        //   pagination: {
        //     current_page: 1,
        //     per_page: 15,
        //     total: 10,
        //     last_page: 1,
        //     from: 1,
        //     to: 10,
        //     rowCount: 10
        //   },
        //   message: "..."
        // }
        
        if (response && response.success && response.data) {
          // Los clientes ahora vienen directamente en data
          const clientsData = response.data || []
          const paginationData = response.pagination
          
          console.log('👥 ClientsPage - Clientes extraídos:', clientsData)
          console.log('📊 ClientsPage - Datos de paginación:', paginationData)
          
          // Asegurar que clients sea siempre un array
          if (Array.isArray(clientsData)) {
            clients.value = clientsData
            console.log('✅ ClientsPage - Clientes asignados correctamente:', clients.value.length)
          } else {
            console.warn('⚠️ ClientsPage - Los datos no son un array:', clientsData)
            clients.value = []
          }
          
          // Actualizar paginación usando rowCount del backend
          if (paginationData && paginationData.rowCount !== undefined) {
            pagination.value = {
              ...pagination.value,
              rowsNumber: paginationData.rowCount,
              page: paginationData.current_page || 1,
              rowsPerPage: paginationData.per_page || 15
            }
            console.log('📄 ClientsPage - Paginación actualizada con rowCount:', pagination.value)
          }
        } else {
          console.warn('⚠️ ClientsPage - Respuesta inesperada:', response)
          clients.value = []
        }
        
      } catch (error) {
        console.error('❌ ClientsPage - Error loading clients:', error)
        clients.value = [] // Asegurar que sea un array vacío en caso de error
        showNotification('Error al cargar los clientes', 'negative', 'error')
      } finally {
        loading.value = false
        console.log('🏁 ClientsPage - loadClients COMPLETADO. Total de clientes:', clients.value.length)
      }
    }

    const handleSearch = () => {
      // Resetear a la primera página cuando se busca
      pagination.value.page = 1
      console.log('🔍 ClientsPage - Búsqueda iniciada:', searchQuery.value)
      loadClients()
    }

    // Función para limpiar búsqueda
    const clearSearch = () => {
      searchQuery.value = ''
      pagination.value.page = 1
      console.log('🧹 ClientsPage - Búsqueda limpiada')
      loadClients()
    }

    const onRequest = (props) => {
      console.log('📄 ClientsPage - onRequest llamado con:', props.pagination)
      
      // Actualizar la paginación local
      pagination.value = {
        ...pagination.value,
        page: props.pagination.page,
        rowsPerPage: props.pagination.rowsPerPage,
        sortBy: props.pagination.sortBy,
        descending: props.pagination.descending
      }
      
      console.log('📄 ClientsPage - Paginación actualizada:', pagination.value)
      
      // Cargar clientes con la nueva paginación
      loadClients()
    }

    const openCreateDialog = () => {
      isEditing.value = false
      selectedClient.value = null
      resetForm()
      showDialog.value = true
    }

    const openEditDialog = (client) => {
      isEditing.value = true
      selectedClient.value = client
      form.value = { ...client }
      showDialog.value = true
    }

    const resetForm = () => {
      form.value = {
        name: '',
        email: '',
        phone: '',
        address: '',
        is_active: true,
        loyalty_points: 0
      }
    }

    const handleSubmit = async () => {
      try {
        console.log('🚀 ClientsPage - Iniciando envío del formulario...')
        submitting.value = true
        
        if (isEditing.value) {
          console.log('✏️ ClientsPage - Actualizando cliente:', selectedClient.value.id)
          const updateResponse = await clientService.updateClient(selectedClient.value.id, form.value)
          console.log('✅ ClientsPage - Cliente actualizado:', updateResponse)
          
          // Mostrar notificación
          showNotification('Cliente actualizado exitosamente', 'positive', 'check_circle')
          console.log('🔔 ClientsPage - Notificación de éxito mostrada')
        } else {
          console.log('➕ ClientsPage - Creando nuevo cliente')
          const createResponse = await clientService.createClient(form.value)
          console.log('✅ ClientsPage - Cliente creado:', createResponse)
          
          // Mostrar notificación
          showNotification('Cliente creado exitosamente', 'positive', 'check_circle')
          console.log('🔔 ClientsPage - Notificación de éxito mostrada')
        }
        
        console.log('✅ ClientsPage - Operación exitosa, cerrando modal...')
        
        // Cerrar modal usando la función específica
        closeDialogWithDelay()
        console.log('🔒 ClientsPage - Modal cerrado usando closeDialog')
        
        // Recargar lista de clientes
        console.log('🔄 ClientsPage - Recargando lista de clientes...')
        await loadClients()
        console.log('✅ ClientsPage - Lista recargada exitosamente')
        
      } catch (error) {
        console.error('❌ ClientsPage - Error saving client:', error)
        showNotification(
          error.response?.data?.message || 'Error al guardar el cliente', 
          'negative', 
          'error'
        )
      } finally {
        submitting.value = false
        console.log('🏁 ClientsPage - Proceso de envío completado')
      }
    }

    const confirmDelete = (client) => {
      selectedClient.value = client
      showDeleteDialog.value = true
    }

    const deleteClient = async () => {
      try {
        await clientService.deleteClient(selectedClient.value.id)
        
        showNotification('Cliente eliminado exitosamente', 'positive', 'check_circle')
        
        showDeleteDialog.value = false
        selectedClient.value = null
        await loadClients()
        
      } catch (error) {
        console.error('Error deleting client:', error)
        showNotification('Error al eliminar el cliente', 'negative', 'error')
      }
    }

    const closeDialog = () => {
      console.log('🔒 ClientsPage - Ejecutando closeDialog...')
      showDialog.value = false
      resetForm()
      selectedClient.value = null
      console.log('✅ ClientsPage - closeDialog ejecutado correctamente')
    }

    const closeDialogWithDelay = () => {
      console.log('⏳ ClientsPage - Cerrando modal con delay...')
      // Delay más largo para asegurar que la notificación se muestre
      setTimeout(() => {
        console.log('🔒 ClientsPage - Ejecutando cierre del modal...')
        closeDialog()
      }, 1000) // Aumentado a 1 segundo
    }

    // Cargar datos al montar el componente
    onMounted(() => {
      loadClients()
    })

    return {
      // Estado
      clients,
      loading,
      submitting,
      showDialog,
      showDeleteDialog,
      isEditing,
      searchQuery,
      statusFilter,
      selectedClient,
      pagination,
      form,
      statusOptions,
      columns,
      
      // Métodos
      loadClients,
      handleSearch,
      clearSearch,
      onRequest,
      openCreateDialog,
      openEditDialog,
      resetForm,
      handleSubmit,
      confirmDelete,
      deleteClient,
      closeDialog,
      closeDialogWithDelay
    }
  }
})
</script>

<style scoped>
.q-table {
  background: white;
}

.q-card {
  box-shadow: 0 4px 25px 0 rgba(0, 0, 0, 0.1);
}
</style> 