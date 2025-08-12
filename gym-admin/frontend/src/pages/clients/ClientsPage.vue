<template>
  <q-page class="q-pa-md">
    <div class="row q-mb-md items-center justify-between">
      <div class="text-h5 text-weight-bold">Gestión de Clientes</div>
             <q-btn 
         class="btn-gym-primary"
         icon="add" 
         label="Nuevo Cliente" 
         @click="openCreateDialog"
         no-caps
       />
    </div>

    <!-- Filtros y búsqueda -->
    <div class="row q-mb-md q-gutter-md">
      <div class="col-12 col-md-6">
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
      <div class="col-12 col-md-4">
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

         <!-- Tabla de clientes -->
     <q-card class="card-gym">
             <q-table
         :rows="clients"
         :columns="columns"
         :loading="loading"
         row-key="id"
         v-model:pagination="pagination"
         :filter="filter"
         @request="qTableRequest"
         :rows-per-page-options="[10, 15, 25, 50]"
         class="text-sm table-gym"
       >
                 <!-- Búsqueda en la tabla -->
         <template v-slot:top>
           <div style="width: 100%;">
             <q-input
               dense
               debounce="300"
               v-model="filter"
               placeholder="Buscar en la tabla..."
               @update:model-value="v => { filter = v.toUpperCase() }"
             >
               <template v-slot:append>
                 <q-icon name="search" />
               </template>
             </q-input>
           </div>
         </template>

         <!-- Headers personalizados para columnas específicas -->
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
                   name="person" 
                   size="sm" 
                   class="q-mr-xs"
                 />
                 <q-icon 
                   v-else-if="col.name === 'email'" 
                   name="email" 
                   size="sm" 
                   class="q-mr-xs"
                 />
                 <q-icon 
                   v-else-if="col.name === 'phone'" 
                   name="phone" 
                   size="sm" 
                   class="q-mr-xs"
                 />
                 <q-icon 
                   v-else-if="col.name === 'loyalty_points'" 
                   name="stars" 
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
                   v-else-if="col.name === 'created_at'" 
                   name="schedule" 
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
    </q-card>

              <!-- Modal para crear/editar cliente -->
     <q-dialog v-model="showDialog" persistent>
       <q-card style="min-width: 600px" class="modal-gym-enhanced">
         <!-- Header del modal con colores del gym -->
         <q-card-section class="modal-header-gym q-pa-md">
           <div class="row items-center full-width">
             <div class="col">
               <div class="text-h5 text-gym-white text-weight-bold">
                 <q-icon 
                   :name="isEditing ? 'edit' : 'person_add'" 
                   size="2rem" 
                   class="q-mr-md" 
                 />
                 {{ isEditing ? 'Editar Cliente' : 'Nuevo Cliente' }}
               </div>
               <div class="text-subtitle2 text-gym-text-secondary q-mt-xs">
                 {{ isEditing ? 'Modifica la información del cliente' : 'Completa los datos del nuevo cliente' }}
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
             <!-- Nombre completo -->
             <div class="row">
               <div class="col-12">
                 <q-input
                   v-model="form.name"
                   label="Nombre completo *"
                   outlined
                   dense
                   class="input-gym-enhanced"
                   :rules="[val => !!val || 'El nombre es requerido']"
                   @blur="formatName"
                   hint="Se convertirá a mayúsculas automáticamente"
                   persistent-hint
                 >
                   <template v-slot:prepend>
                     <q-icon name="person" color="var(--gym-red)" />
                   </template>
                 </q-input>
               </div>
             </div>

             <!-- Email -->
             <div class="row">
               <div class="col-12">
                 <q-input
                   v-model="form.email"
                   label="Email *"
                   type="email"
                   outlined
                   dense
                   class="input-gym-enhanced"
                   :rules="[
                     val => !!val || 'El email es requerido',
                     val => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val) || 'Email inválido'
                   ]"
                 >
                   <template v-slot:prepend>
                     <q-icon name="email" color="var(--gym-red)" />
                   </template>
                 </q-input>
               </div>
             </div>

             <!-- Teléfono -->
             <div class="row">
               <div class="col-12">
                 <q-input
                   v-model="form.phone"
                   label="Teléfono *"
                   outlined
                   dense
                   class="input-gym-enhanced"
                   :rules="[val => !!val || 'El teléfono es requerido']"
                   mask="(###) ###-####"
                   hint="Formato: (123) 456-7890"
                   persistent-hint
                 >
                   <template v-slot:prepend>
                     <q-icon name="phone" color="var(--gym-red)" />
                   </template>
                 </q-input>
               </div>
             </div>

             <!-- Dirección -->
             <div class="row">
               <div class="col-12">
                 <q-input
                   v-model="form.address"
                   label="Dirección"
                   outlined
                   dense
                   class="input-gym-enhanced"
                   @blur="formatAddress"
                   hint="Se convertirá a mayúsculas automáticamente"
                   persistent-hint
                 >
                   <template v-slot:prepend>
                     <q-icon name="location_on" color="var(--gym-red)" />
                   </template>
                 </q-input>
               </div>
             </div>

             <!-- Estado y Puntos de Lealtad -->
             <div class="row q-gutter-md">
               <div class="col-12 col-md-6">
                 <div class="text-subtitle2 text-gym-black q-mb-sm">Estado del Cliente</div>
                 <q-toggle
                   v-model="form.is_active"
                   :label="form.is_active ? 'Cliente Activo' : 'Cliente Inactivo'"
                   :color="form.is_active ? 'positive' : 'negative'"
                   size="lg"
                   class="toggle-gym"
                 />
               </div>
               <div class="col-12 col-md-6">
                 <q-input
                   v-model.number="form.loyalty_points"
                   label="Puntos de lealtad"
                   type="number"
                   outlined
                   dense
                   class="input-gym-enhanced"
                   min="0"
                   hint="Puntos acumulados por el cliente"
                   persistent-hint
                 >
                   <template v-slot:prepend>
                     <q-icon name="stars" color="var(--gym-red)" />
                   </template>
                   <template v-slot:append>
                     <q-icon name="emoji_events" color="var(--gym-warning)" />
                   </template>
                 </q-input>
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
                 :label="isEditing ? 'Actualizar Cliente' : 'Crear Cliente'"
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
                 icon="person" 
                 color="var(--gym-red)" 
                 text-color="white" 
                 size="4rem"
                 class="q-mr-md"
               />
             </div>
             <div class="col">
               <div class="text-h6 text-gym-black">
                 ¿Eliminar a <strong>{{ selectedClient?.name }}</strong>?
               </div>
               <div class="text-body2 text-gym-red q-mt-xs">
                 Se eliminarán todos los datos asociados a este cliente
               </div>
             </div>
           </div>

           <div class="row q-gutter-md">
             <div class="col-12 col-md-6">
               <div class="text-caption text-gym-black q-mb-xs">Email:</div>
               <div class="text-body1">{{ selectedClient?.email }}</div>
             </div>
             <div class="col-12 col-md-6">
               <div class="text-caption text-gym-black q-mb-xs">Teléfono:</div>
               <div class="text-body1">{{ selectedClient?.phone }}</div>
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
             label="Eliminar Cliente" 
             class="btn-gym-primary" 
             @click="deleteClient" 
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
    const filter = ref('')
    const selectedClient = ref(null)
    
    // Paginación simplificada
    const pagination = ref({
      sortBy: 'name',
      descending: false,
      page: 1,
      rowsPerPage: 15,
      rowsNumber: 0
    })

    // Filtros consolidados
    const filters = ref({
      status: null,
      search: ''
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
        filter: filter.value
      })
    }

    const qTableRequest = async (props) => {
      try {
        loading.value = true
        
        // Actualizar paginación local
        pagination.value = props.pagination
        filter.value = props.filter
        
        // Preparar parámetros
        const params = { 
          ...filters.value,
          filter: props.filter,
          pagination: props.pagination
        }
        
        // Aplicar filtros de estado
        if (filters.value.status !== null) {
          params.is_active = filters.value.status
        }
        
        // Aplicar búsqueda general
        if (searchQuery.value.trim()) {
          params.search = searchQuery.value.trim()
        }
        
        console.log('🔍 Parámetros de búsqueda:', params)
        
        const response = await clientService.getClients(
          pagination.value.page,
          pagination.value.rowsPerPage,
          params,
          pagination.value.sortBy,
          pagination.value.descending
        )
        
        console.log('📡 Respuesta del servicio:', response)
        
        if (response && response.success && response.data) {
          // Los clientes vienen directamente en data
          clients.value = response.data || []
          
          // Actualizar paginación usando rowCounts del backend
          if (response.pagination && response.pagination.rowCounts !== undefined) {
            pagination.value.rowsNumber = response.pagination.rowCounts
            console.log('📊 Paginación actualizada con rowCounts:', pagination.value)
          }
        } else {
          clients.value = []
        }
        
      } catch (error) {
        console.error('❌ Error loading clients:', error)
        clients.value = []
        showNotification('Error al cargar los clientes', 'negative', 'error')
      } finally {
        loading.value = false
      }
    }

    const handleSearch = () => {
      // Resetear a la primera página cuando se busca
      pagination.value.page = 1
      fetchFromServer()
    }

    const clearSearch = () => {
      searchQuery.value = ''
      filter.value = ''
      filters.value.status = null
      pagination.value.page = 1
      fetchFromServer()
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
        submitting.value = true
        
        if (isEditing.value) {
          const updateResponse = await clientService.updateClient(selectedClient.value.id, form.value)
          showNotification('Cliente actualizado exitosamente', 'positive', 'check_circle')
        } else {
          const createResponse = await clientService.createClient(form.value)
          showNotification('Cliente creado exitosamente', 'positive', 'check_circle')
        }
        
        // Cerrar modal y recargar datos
        closeDialog()
        await fetchFromServer()
        
      } catch (error) {
        console.error('Error saving client:', error)
        showNotification(
          error.response?.data?.message || 'Error al guardar el cliente', 
          'negative', 
          'error'
        )
      } finally {
        submitting.value = false
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
        await fetchFromServer()
      } catch (error) {
        console.error('Error deleting client:', error)
        showNotification('Error al eliminar el cliente', 'negative', 'error')
      }
    }

         // Función para formatear nombre a mayúsculas
     const formatName = () => {
       if (form.value.name) {
         form.value.name = form.value.name.toUpperCase()
       }
     }

     // Función para formatear dirección a mayúsculas
     const formatAddress = () => {
       if (form.value.address) {
         form.value.address = form.value.address.toUpperCase()
       }
     }

     const closeDialog = () => {
       showDialog.value = false
       resetForm()
       selectedClient.value = null
     }

    // Cargar datos al montar el componente
    onMounted(() => {
      fetchFromServer()
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
      filter,
      filters,
      selectedClient,
      pagination,
      form,
      statusOptions,
      columns,
      
             // Métodos
       fetchFromServer,
       qTableRequest,
       handleSearch,
       clearSearch,
       openCreateDialog,
       openEditDialog,
       resetForm,
       handleSubmit,
       confirmDelete,
       deleteClient,
       closeDialog,
       formatName,
       formatAddress
    }
  }
})
</script>

<style scoped>
/* Estilos específicos de la página de clientes usando el tema global del gym */

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

/* Estilo para las columnas ordenables */
.q-table thead th.sortable {
  cursor: pointer;
}

.q-table thead th.sortable:hover {
  background: var(--gym-gradient-red) !important;
  transform: translateY(-1px) !important;
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

/* Estilos específicos para diferentes tipos de columnas */
.custom-header:has(.q-icon[name="person"]),
.custom-header:has(.q-icon[name="phone"]),
.custom-header:has(.q-icon[name="check_circle"]),
.custom-header:has(.q-icon[name="settings"]) {
  background: var(--gym-gradient-black) !important;
}

.custom-header:has(.q-icon[name="email"]),
.custom-header:has(.q-icon[name="stars"]),
.custom-header:has(.q-icon[name="schedule"]) {
  background: var(--gym-gradient-black-light) !important;
}

/* Hover específico para cada tipo de columna - Cambia a rojo */
.custom-header:has(.q-icon[name="person"]):hover,
.custom-header:has(.q-icon[name="email"]):hover,
.custom-header:has(.q-icon[name="phone"]):hover,
.custom-header:has(.q-icon[name="stars"]):hover,
.custom-header:has(.q-icon[name="check_circle"]):hover,
.custom-header:has(.q-icon[name="schedule"]):hover,
.custom-header:has(.q-icon[name="settings"]):hover {
  background: var(--gym-gradient-red) !important;
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