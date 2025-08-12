<template>
  <q-page class="q-pa-md">
    <!-- Header del Dashboard -->
    <div class="row q-mb-lg items-center">
      <div class="text-h4 text-weight-bold text-gym-black">
        <q-icon name="dashboard" class="q-mr-md" />
        Dashboard del Gym
      </div>
      <q-space />
      <q-btn class="btn-gym-primary" icon="refresh" label="Actualizar" @click="refreshData" />
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="row q-gutter-md q-mb-lg">
      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="card-gym animate-fade-in-up">
          <q-card-section class="text-center">
            <q-icon name="people" size="3rem" color="var(--gym-red)" />
            <div class="text-h4 text-weight-bold text-gym-black q-mt-sm">{{ stats.totalClients }}</div>
            <div class="text-subtitle2 text-gym-red">Total de Clientes</div>
          </q-card-section>
        </q-card>
      </div>

      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="card-gym animate-fade-in-up" style="animation-delay: 0.1s;">
          <q-card-section class="text-center">
            <q-icon name="fitness_center" size="3rem" color="var(--gym-red)" />
            <div class="text-h4 text-weight-bold text-gym-black q-mt-sm">{{ stats.activeMemberships }}</div>
            <div class="text-subtitle2 text-gym-red">Membresías Activas</div>
          </q-card-section>
        </q-card>
      </div>

      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="card-gym animate-fade-in-up" style="animation-delay: 0.2s;">
          <q-card-section class="text-center">
            <q-icon name="point_of_sale" size="3rem" color="var(--gym-red)" />
            <div class="text-h4 text-weight-bold text-gym-black q-mt-sm">${{ stats.totalSales }}</div>
            <div class="text-subtitle2 text-gym-red">Ventas del Mes</div>
          </q-card-section>
        </q-card>
      </div>

      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="card-gym animate-fade-in-up" style="animation-delay: 0.3s;">
          <q-card-section class="text-center">
            <q-icon name="stars" size="3rem" color="var(--gym-red)" />
            <div class="text-h4 text-weight-bold text-gym-black q-mt-sm">{{ stats.expiringSoon }}</div>
            <div class="text-subtitle2 text-gym-red">Vencen Pronto</div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <!-- Gráficos y Tablas -->
    <div class="row q-gutter-md">
      <!-- Gráfico de Membresías -->
      <div class="col-12 col-lg-8">
        <q-card class="card-gym">
          <q-card-section class="header-gym">
            <div class="text-h6 text-gym-white">
              <q-icon name="analytics" class="q-mr-sm" />
              Membresías por Tipo
            </div>
          </q-card-section>
          <q-card-section>
            <div class="text-center q-pa-lg">
              <q-icon name="insert_chart" size="8rem" color="var(--gym-border-color)" />
              <div class="text-h6 text-gym-black q-mt-md">Gráfico de Membresías</div>
              <div class="text-caption text-gym-red">Próximamente: Gráficos interactivos</div>
            </div>
          </q-card-section>
        </q-card>
      </div>

      <!-- Actividad Reciente -->
      <div class="col-12 col-lg-4">
        <q-card class="card-gym">
          <q-card-section class="header-gym">
            <div class="text-h6 text-gym-white">
              <q-icon name="schedule" class="q-mr-sm" />
              Actividad Reciente
            </div>
          </q-card-section>
          <q-card-section>
            <q-list>
              <q-item v-for="(activity, index) in recentActivity" :key="index" class="q-pa-none q-mb-sm">
                <q-item-section avatar>
                  <q-avatar :color="activity.color" text-color="white" size="sm">
                    <q-icon :name="activity.icon" />
                  </q-avatar>
                </q-item-section>
                <q-item-section>
                  <q-item-label class="text-gym-black">{{ activity.title }}</q-item-label>
                  <q-item-label caption class="text-gym-red">{{ activity.time }}</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <!-- Tabla de Clientes Recientes -->
    <div class="row q-mt-lg">
      <div class="col-12">
        <q-card class="card-gym">
          <q-card-section class="header-gym">
            <div class="text-h6 text-gym-white">
              <q-icon name="people" class="q-mr-sm" />
              Clientes Recientes
            </div>
          </q-card-section>
          <q-card-section>
            <q-table
              :rows="recentClients"
              :columns="clientColumns"
              row-key="id"
              :pagination="{ rowsPerPage: 5 }"
              class="table-gym"
            >
              <template v-slot:body-cell-actions="props">
                <q-td :props="props">
                  <q-btn
                    size="sm"
                    class="btn-gym-secondary"
                    icon="visibility"
                    flat
                    round
                    @click="viewClient(props.row)"
                  >
                    <q-tooltip>Ver Cliente</q-tooltip>
                  </q-btn>
                </q-td>
              </template>
            </q-table>
          </q-card-section>
        </q-card>
      </div>
    </div>
  </q-page>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

export default defineComponent({
  name: 'DashboardPage',
  setup() {
    const router = useRouter()
    
    // Estadísticas del dashboard
    const stats = ref({
      totalClients: 156,
      activeMemberships: 142,
      totalSales: '12,450',
      expiringSoon: 8
    })

    // Actividad reciente
    const recentActivity = ref([
      {
        icon: 'person_add',
        title: 'Nuevo cliente registrado',
        time: 'Hace 5 minutos',
        color: 'var(--gym-success)'
      },
      {
        icon: 'fitness_center',
        title: 'Membresía renovada',
        time: 'Hace 15 minutos',
        color: 'var(--gym-info)'
      },
      {
        icon: 'point_of_sale',
        title: 'Venta realizada',
        time: 'Hace 1 hora',
        color: 'var(--gym-warning)'
      },
      {
        icon: 'stars',
        title: 'Puntos de lealtad otorgados',
        time: 'Hace 2 horas',
        color: 'var(--gym-red)'
      }
    ])

    // Clientes recientes
    const recentClients = ref([
      {
        id: 1,
        name: 'Carlos López',
        email: 'carlos@email.com',
        membership: 'Mensual',
        status: 'Activo'
      },
      {
        id: 2,
        name: 'Ana García',
        email: 'ana@email.com',
        membership: 'Anual',
        status: 'Activo'
      },
      {
        id: 3,
        name: 'Luis Rodríguez',
        email: 'luis@email.com',
        membership: 'Semanal',
        status: 'Activo'
      }
    ])

    // Columnas de la tabla
    const clientColumns = [
      {
        name: 'name',
        label: 'Nombre',
        field: 'name',
        align: 'left'
      },
      {
        name: 'email',
        label: 'Email',
        field: 'email',
        align: 'left'
      },
      {
        name: 'membership',
        label: 'Membresía',
        field: 'membership',
        align: 'center'
      },
      {
        name: 'status',
        label: 'Estado',
        field: 'status',
        align: 'center'
      },
      {
        name: 'actions',
        label: 'Acciones',
        align: 'center'
      }
    ]

    // Métodos
    const refreshData = () => {
      // Aquí se actualizarían los datos del dashboard
      console.log('Actualizando datos del dashboard...')
    }

    const viewClient = (client) => {
      router.push(`/clients/${client.id}`)
    }

    onMounted(() => {
      // Cargar datos iniciales del dashboard
      console.log('Dashboard montado')
    })

    return {
      stats,
      recentActivity,
      recentClients,
      clientColumns,
      refreshData,
      viewClient
    }
  }
})
</script>

<style scoped>
/* Estilos específicos del dashboard */
.card-gym {
  transition: var(--gym-transition);
}

.card-gym:hover {
  transform: translateY(-4px);
  box-shadow: var(--gym-shadow-lg);
}

/* Animaciones escalonadas */
.animate-fade-in-up:nth-child(1) { animation-delay: 0s; }
.animate-fade-in-up:nth-child(2) { animation-delay: 0.1s; }
.animate-fade-in-up:nth-child(3) { animation-delay: 0.2s; }
.animate-fade-in-up:nth-child(4) { animation-delay: 0.3s; }
</style> 