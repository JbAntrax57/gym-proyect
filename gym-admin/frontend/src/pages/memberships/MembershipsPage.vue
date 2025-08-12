<template>
  <q-page class="q-pa-md">
    <div class="row q-mb-md">
      <div class="col">
        <h4 class="q-my-none text-h4 text-weight-bold text-primary">
          Gestión de Membresías
        </h4>
        <p class="text-subtitle1 text-grey-7 q-mt-sm">
          Administra los diferentes tipos de membresías disponibles en el gimnasio
        </p>
      </div>
      <div class="col-auto">
        <q-btn
          color="primary"
          icon="add"
          label="Nueva Membresía"
          @click="openDialog()"
          class="q-px-md"
        />
      </div>
    </div>

    <!-- Estadísticas -->
    <MembershipStats :stats="stats" />

    <!-- Filtros y Búsqueda -->
    <q-card class="q-mb-md">
      <q-card-section>
        <div class="row q-gutter-md items-center">
          <div class="col-md-4">
            <q-input
              v-model="searchTerm"
              placeholder="Buscar membresías..."
              dense
              outlined
              clearable
              @update:model-value="handleSearch"
            >
              <template v-slot:prepend>
                <q-icon name="search" />
              </template>
            </q-input>
          </div>
          <div class="col-md-2">
            <q-select
              v-model="filterType"
              :options="typeOptions"
              placeholder="Tipo"
              dense
              outlined
              clearable
              @update:model-value="handleSearch"
            />
          </div>
          <div class="col-md-2">
            <q-select
              v-model="filterStatus"
              :options="statusOptions"
              placeholder="Estado"
              dense
              outlined
              clearable
              @update:model-value="handleSearch"
            />
          </div>
          <div class="col-md-2">
            <q-toggle
              v-model="filterActive"
              label="Solo Activas"
              @update:model-value="handleSearch"
            />
          </div>
          <div class="col-md-2">
            <q-btn
              color="secondary"
              icon="refresh"
              label="Limpiar"
              @click="clearFilters"
              flat
            />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Tabla de Membresías -->
    <q-card>
      <q-card-section>
        <q-table
          :rows="memberships"
          :columns="columns"
          :loading="loading"
          :pagination="pagination"
          row-key="id"
          @request="onRequest"
          class="memberships-table"
        >
          <!-- Columna de Acciones -->
          <template v-slot:body-cell-actions="props">
            <q-td :props="props">
              <div class="row q-gutter-xs">
                <q-btn
                  size="sm"
                  color="info"
                  icon="visibility"
                  @click="viewDetails(props.row)"
                  flat
                  round
                >
                  <q-tooltip>Ver Detalles</q-tooltip>
                </q-btn>
                <q-btn
                  size="sm"
                  color="primary"
                  icon="edit"
                  @click="openDialog(props.row)"
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

          <!-- Columna de Estado -->
          <template v-slot:body-cell-status="props">
            <q-td :props="props">
              <q-chip
                :color="getStatusColor(props.value)"
                text-color="white"
                size="sm"
              >
                {{ props.value }}
              </q-chip>
            </q-td>
          </template>

          <!-- Columna de Tipo -->
          <template v-slot:body-cell-type="props">
            <q-td :props="props">
              <q-chip
                :color="getTypeColor(props.value)"
                text-color="white"
                size="sm"
              >
                {{ props.value }}
              </q-chip>
            </q-td>
          </template>

          <!-- Columna de Precio -->
          <template v-slot:body-cell-price="props">
            <q-td :props="props">
              <span class="text-weight-bold text-positive">
                ${{ Number(props.value).toFixed(2) }}
              </span>
            </q-td>
          </template>

          <!-- Columna de Activo -->
          <template v-slot:body-cell-is_active="props">
            <q-td :props="props">
              <q-icon
                :name="props.value ? 'check_circle' : 'cancel'"
                :color="props.value ? 'positive' : 'negative'"
                size="sm"
              />
            </q-td>
          </template>
        </q-table>
      </q-card-section>
    </q-card>

    <!-- Diálogo para Crear/Editar -->
    <q-dialog v-model="dialog" persistent>
      <q-card style="min-width: 500px">
        <q-card-section class="row items-center">
          <div class="text-h6">
            {{ editingMembership ? 'Editar' : 'Nueva' }} Membresía
          </div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section>
          <q-form @submit="saveMembership" class="q-gutter-md">
            <div class="row q-gutter-md">
              <div class="col-12">
                <q-input
                  v-model="form.name"
                  label="Nombre de la Membresía"
                  outlined
                  dense
                  :rules="[val => !!val || 'El nombre es requerido']"
                />
              </div>
            </div>

            <div class="row q-gutter-md">
              <div class="col-md-6">
                <q-select
                  v-model="form.type"
                  :options="typeOptions"
                  label="Tipo"
                  outlined
                  dense
                  :rules="[val => !!val || 'El tipo es requerido']"
                />
              </div>
              <div class="col-md-6">
                <q-select
                  v-model="form.status"
                  :options="statusOptions"
                  label="Estado"
                  outlined
                  dense
                  :rules="[val => !!val || 'El estado es requerido']"
                />
              </div>
            </div>

            <div class="row q-gutter-md">
              <div class="col-md-6">
                <q-input
                  v-model.number="form.duration"
                  label="Duración (días)"
                  type="number"
                  outlined
                  dense
                  :rules="[val => val > 0 || 'La duración debe ser mayor a 0']"
                />
              </div>
              <div class="col-md-6">
                <q-input
                  v-model.number="form.max_visits"
                  label="Máximo de Visitas"
                  type="number"
                  outlined
                  dense
                  :rules="[val => val > 0 || 'El máximo de visitas debe ser mayor a 0']"
                />
              </div>
            </div>

            <div class="row q-gutter-md">
              <div class="col-md-6">
                <q-input
                  v-model.number="form.price"
                  label="Precio"
                  type="number"
                  step="0.01"
                  outlined
                  dense
                  :rules="[val => val > 0 || 'El precio debe ser mayor a 0']"
                />
              </div>
              <div class="col-md-6">
                <q-toggle
                  v-model="form.is_active"
                  label="Activa"
                />
              </div>
            </div>

            <div class="row">
              <div class="col-12">
                <q-input
                  v-model="form.description"
                  label="Descripción"
                  type="textarea"
                  outlined
                  dense
                  rows="3"
                />
              </div>
            </div>

            <div class="row q-gutter-md q-mt-md">
              <div class="col">
                <q-btn
                  label="Cancelar"
                  color="grey"
                  flat
                  v-close-popup
                />
              </div>
              <div class="col-auto">
                <q-btn
                  :label="editingMembership ? 'Actualizar' : 'Crear'"
                  type="submit"
                  color="primary"
                  :loading="saving"
                />
              </div>
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>

    <!-- Diálogo de Confirmación de Eliminación -->
    <q-dialog v-model="deleteDialog" persistent>
      <q-card>
        <q-card-section class="row items-center">
          <q-avatar icon="warning" color="negative" />
          <span class="q-ml-sm">¿Estás seguro de que quieres eliminar esta membresía?</span>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancelar" color="grey" v-close-popup />
          <q-btn flat label="Eliminar" color="negative" @click="deleteMembership" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { defineComponent, ref, computed, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import membershipService from 'src/services/membershipService'
import MembershipStats from 'src/components/MembershipStats.vue'

export default defineComponent({
  name: 'MembershipsPage',
  components: { MembershipStats },
  setup() {
    const $q = useQuasar()

    // Estados reactivos
    const memberships = ref([])
    const loading = ref(false)
    const saving = ref(false)
    const dialog = ref(false)
    const deleteDialog = ref(false)
    const editingMembership = ref(null)
    const searchTerm = ref('')
    const filterType = ref(null)
    const filterStatus = ref(null)
    const filterActive = ref(false)
    const stats = ref({
      total: 0,
      active: 0,
      totalRevenue: 0,
      avgPrice: 0,
      typeStats: {},
      statusStats: {}
    })

    // Paginación
    const pagination = ref({
      sortBy: 'name',
      descending: false,
      page: 1,
      rowsPerPage: 10,
      rowsNumber: 0
    })

    // Formulario
    const form = ref({
      name: '',
      type: '',
      duration: 1,
      price: 0,
      status: 'Activa',
      description: '',
      max_visits: 1,
      is_active: true
    })

    // Opciones para los selects
    const typeOptions = ['Semanal', 'Mensual', 'Visita']
    const statusOptions = ['Activa', 'Inactiva', 'Suspendida']

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
        name: 'type',
        label: 'Tipo',
        align: 'center',
        field: 'type',
        sortable: true
      },
      {
        name: 'duration',
        label: 'Duración',
        align: 'center',
        field: 'duration',
        sortable: true
      },
      {
        name: 'price',
        label: 'Precio',
        align: 'right',
        field: 'price',
        sortable: true
      },
      {
        name: 'status',
        label: 'Estado',
        align: 'center',
        field: 'status',
        sortable: true
      },
      {
        name: 'is_active',
        label: 'Activa',
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

    // Métodos
    const loadMemberships = async () => {
      try {
        console.log('🔄 MembershipsPage - INICIANDO loadMemberships...')
        loading.value = true
        
        const filters = {}
        if (searchTerm.value) {
          filters.search = searchTerm.value
        }
        if (filterType.value) {
          filters.type = filterType.value
        }
        if (filterStatus.value) {
          filters.status = filterStatus.value
        }
        if (filterActive.value) {
          filters.is_active = true
        }

        console.log('🔍 MembershipsPage - Filtros aplicados:', filters)
        
        const response = await membershipService.getAllMemberships(
          pagination.value.page,
          pagination.value.rowsPerPage,
          filters
        )

        if (response && response.success && response.data) {
          const membershipsData = response.data.data || []
          const paginationData = response.data
          
          if (Array.isArray(membershipsData)) {
            memberships.value = membershipsData
          } else {
            memberships.value = []
          }
          
          if (paginationData.total !== undefined) {
            pagination.value.rowsNumber = paginationData.total
            pagination.value.page = paginationData.current_page || 1
            pagination.value.rowsPerPage = paginationData.per_page || 10
          }
        } else {
          memberships.value = []
        }
      } catch (error) {
        console.error('❌ MembershipsPage - Error loading memberships:', error)
        memberships.value = []
        $q.notify({
          color: 'negative',
          message: 'Error al cargar las membresías',
          icon: 'error'
        })
      } finally {
        loading.value = false
      }
    }

    const loadStats = async () => {
      try {
        const response = await membershipService.getMembershipStats()
        if (response && response.success) {
          stats.value = response.data
        }
      } catch (error) {
        console.error('❌ Error loading stats:', error)
      }
    }

    const handleSearch = () => {
      pagination.value.page = 1
      loadMemberships()
    }

    const clearFilters = () => {
      searchTerm.value = ''
      filterType.value = null
      filterStatus.value = null
      filterActive.value = false
      pagination.value.page = 1
      loadMemberships()
    }

    const openDialog = (membership = null) => {
      editingMembership.value = membership
      if (membership) {
        form.value = { ...membership }
      } else {
        form.value = {
          name: '',
          type: '',
          duration: 1,
          price: 0,
          status: 'Activa',
          description: '',
          max_visits: 1,
          is_active: true
        }
      }
      dialog.value = true
    }

    const saveMembership = async () => {
      try {
        saving.value = true
        
        if (editingMembership.value) {
          await membershipService.updateMembership(editingMembership.value.id, form.value)
          $q.notify({
            color: 'positive',
            message: 'Membresía actualizada correctamente',
            icon: 'check_circle'
          })
        } else {
          await membershipService.createMembership(form.value)
          $q.notify({
            color: 'positive',
            message: 'Membresía creada correctamente',
            icon: 'check_circle'
          })
        }
        
        dialog.value = false
        loadMemberships()
        loadStats()
      } catch (error) {
        console.error('❌ Error saving membership:', error)
        $q.notify({
          color: 'negative',
          message: 'Error al guardar la membresía',
          icon: 'error'
        })
      } finally {
        saving.value = false
      }
    }

    const confirmDelete = (membership) => {
      editingMembership.value = membership
      deleteDialog.value = true
    }

    const deleteMembership = async () => {
      try {
        await membershipService.deleteMembership(editingMembership.value.id)
        $q.notify({
          color: 'positive',
          message: 'Membresía eliminada correctamente',
          icon: 'check_circle'
        })
        deleteDialog.value = false
        loadMemberships()
        loadStats()
      } catch (error) {
        console.error('❌ Error deleting membership:', error)
        $q.notify({
          color: 'negative',
          message: 'Error al eliminar la membresía',
          icon: 'error'
        })
      }
    }

    const viewDetails = (membership) => {
      // Implementar vista de detalles
      console.log('Ver detalles de:', membership)
    }

    const onRequest = (props) => {
      pagination.value = props.pagination
      loadMemberships()
    }

    const getStatusColor = (status) => {
      const colors = {
        'Activa': 'positive',
        'Inactiva': 'negative',
        'Suspendida': 'warning'
      }
      return colors[status] || 'grey'
    }

    const getTypeColor = (type) => {
      const colors = {
        'Semanal': 'blue',
        'Mensual': 'green',
        'Visita': 'orange'
      }
      return colors[type] || 'grey'
    }

    // Cargar datos al montar el componente
    onMounted(() => {
      loadMemberships()
      loadStats()
    })

    return {
      // Estados
      memberships,
      loading,
      saving,
      dialog,
      deleteDialog,
      editingMembership,
      searchTerm,
      filterType,
      filterStatus,
      filterActive,
      stats,
      pagination,
      form,
      typeOptions,
      statusOptions,
      columns,

      // Métodos
      loadMemberships,
      loadStats,
      handleSearch,
      clearFilters,
      openDialog,
      saveMembership,
      confirmDelete,
      deleteMembership,
      viewDetails,
      onRequest,
      getStatusColor,
      getTypeColor
    }
  }
})
</script>

<style scoped>
.memberships-table {
  border-radius: 8px;
}

.q-table th {
  font-weight: bold;
  background-color: #f8f9fa;
}
</style>
