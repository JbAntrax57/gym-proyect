<template>
  <q-page class="q-pa-md">
    <div class="row q-col-gutter-md">
      <!-- Título del Dashboard -->
      <div class="col-12">
        <h4 class="text-h4 q-mb-md text-primary">
          <q-icon name="dashboard" class="q-mr-sm" />
          Dashboard del Gimnasio
        </h4>
      </div>

      <!-- Tarjetas de Métricas -->
      <div class="col-12 col-md-3">
        <q-card class="bg-primary text-white">
          <q-card-section>
            <div class="text-h6">{{ totalClients }}</div>
            <div class="text-subtitle2">Total de Clientes</div>
            <q-icon name="people" size="3rem" class="absolute-bottom-right q-mr-md q-mb-md opacity-20" />
          </q-card-section>
        </q-card>
      </div>

      <div class="col-12 col-md-3">
        <q-card class="bg-positive text-white">
          <q-card-section>
            <div class="text-h6">{{ activeMemberships }}</div>
            <div class="text-subtitle2">Membresías Activas</div>
            <q-icon name="fitness_center" size="3rem" class="absolute-bottom-right q-mr-md q-mb-md opacity-20" />
          </q-card-section>
        </q-card>
      </div>

      <div class="col-12 col-md-3">
        <q-card class="bg-warning text-white">
          <q-card-section>
            <div class="text-h6">{{ expiringSoon }}</div>
            <div class="text-subtitle2">Próximas a Vencer</div>
            <q-icon name="schedule" size="3rem" class="absolute-bottom-right q-mr-md q-mb-md opacity-20" />
          </q-card-section>
        </q-card>
      </div>

      <div class="col-12 col-md-3">
        <q-card class="bg-info text-white">
          <q-card-section>
            <div class="text-h6">${{ totalSales }}</div>
            <div class="text-subtitle2">Ventas del Mes</div>
            <q-icon name="attach_money" size="3rem" class="absolute-bottom-right q-mr-md q-mb-md opacity-20" />
          </q-card-section>
        </q-card>
      </div>

      <!-- Gráfico de Ventas -->
      <div class="col-12 col-md-8">
        <q-card>
          <q-card-section>
            <div class="text-h6">Ventas de los Últimos 7 Días</div>
            <div class="text-subtitle2 text-grey-6">Tendencia de ventas diarias</div>
          </q-card-section>
          <q-card-section>
            <div class="text-center q-pa-lg">
              <q-icon name="insert_chart" size="5rem" color="primary" />
              <div class="text-grey-6 q-mt-sm">Gráfico de Ventas</div>
            </div>
          </q-card-section>
        </q-card>
      </div>

      <!-- Membresías por Tipo -->
      <div class="col-12 col-md-4">
        <q-card>
          <q-card-section>
            <div class="text-h6">Membresías por Tipo</div>
            <div class="text-subtitle2 text-grey-6">Distribución actual</div>
          </q-card-section>
          <q-card-section>
            <div class="q-gutter-y-sm">
              <div v-for="type in membershipTypes" :key="type.id" class="row items-center">
                <div class="col-8">
                  <div class="text-body2">{{ type.name }}</div>
                  <div class="text-caption text-grey-6">{{ type.count }} clientes</div>
                </div>
                <div class="col-4 text-right">
                  <q-badge :color="getMembershipColor(type.name)" :label="type.percentage + '%'" />
                </div>
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>

      <!-- Actividad Reciente -->
      <div class="col-12 col-md-6">
        <q-card>
          <q-card-section>
            <div class="text-h6">Actividad Reciente</div>
            <div class="text-subtitle2 text-grey-6">Últimas acciones del sistema</div>
          </q-card-section>
          <q-card-section>
            <q-list>
              <q-item v-for="activity in recentActivities" :key="activity.id">
                <q-item-section avatar>
                  <q-avatar :color="activity.color" text-color="white">
                    <q-icon :name="activity.icon" />
                  </q-avatar>
                </q-item-section>
                <q-item-section>
                  <q-item-label>{{ activity.title }}</q-item-label>
                  <q-item-label caption>{{ activity.description }}</q-item-label>
                </q-item-section>
                <q-item-section side>
                  <q-item-label caption>{{ activity.time }}</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </q-card-section>
        </q-card>
      </div>

      <!-- Productos con Stock Bajo -->
      <div class="col-12 col-md-6">
        <q-card>
          <q-card-section>
            <div class="text-h6">Stock Bajo</div>
            <div class="text-subtitle2 text-grey-6">Productos que necesitan reposición</div>
          </q-card-section>
          <q-card-section>
            <q-list>
              <q-item v-for="product in lowStockProducts" :key="product.id">
                <q-item-section avatar>
                  <q-avatar color="warning" text-color="white">
                    <q-icon name="warning" />
                  </q-avatar>
                </q-item-section>
                <q-item-section>
                  <q-item-label>{{ product.name }}</q-item-label>
                  <q-item-label caption>Stock: {{ product.stock }} unidades</q-item-label>
                </q-item-section>
                <q-item-section side>
                  <q-btn flat round color="primary" icon="add" size="sm" />
                </q-item-section>
              </q-item>
            </q-list>
          </q-card-section>
        </q-card>
      </div>
    </div>
  </q-page>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue'

export default defineComponent({
  name: 'DashboardPage',
  setup() {
    const totalClients = ref(0)
    const activeMemberships = ref(0)
    const expiringSoon = ref(0)
    const totalSales = ref(0)
    const membershipTypes = ref([])
    const recentActivities = ref([])
    const lowStockProducts = ref([])

    const getMembershipColor = (name) => {
      const colors = {
        'Diaria': 'blue',
        'Semanal': 'green',
        'Mensual': 'orange',
        'Anual': 'purple'
      }
      return colors[name] || 'grey'
    }

    const loadDashboardData = async () => {
      try {
        // Aquí se cargarían los datos reales desde la API
        // Por ahora usamos datos de ejemplo
        totalClients.value = 156
        activeMemberships.value = 142
        expiringSoon.value = 8
        totalSales.value = '24,500'
        
        membershipTypes.value = [
          { id: 1, name: 'Mensual', count: 89, percentage: 63 },
          { id: 2, name: 'Semanal', count: 32, percentage: 23 },
          { id: 3, name: 'Anual', count: 15, percentage: 11 },
          { id: 4, name: 'Diaria', count: 6, percentage: 3 }
        ]

        recentActivities.value = [
          {
            id: 1,
            title: 'Nueva Membresía',
            description: 'Juan Pérez renovó su membresía mensual',
            time: 'Hace 2 horas',
            icon: 'fitness_center',
            color: 'positive'
          },
          {
            id: 2,
            title: 'Venta Realizada',
            description: 'Se vendieron 2 bebidas energéticas',
            time: 'Hace 3 horas',
            icon: 'shopping_cart',
            color: 'info'
          },
          {
            id: 3,
            title: 'Cliente Registrado',
            description: 'María García se registró como nueva cliente',
            time: 'Hace 5 horas',
            icon: 'person_add',
            color: 'primary'
          }
        ]

        lowStockProducts.value = [
          { id: 1, name: 'Agua Mineral', stock: 3 },
          { id: 2, name: 'Bebida Energética', stock: 2 },
          { id: 3, name: 'Proteína en Polvo', stock: 1 }
        ]
      } catch (error) {
        console.error('Error cargando datos del dashboard:', error)
      }
    }

    onMounted(() => {
      loadDashboardData()
    })

    return {
      totalClients,
      activeMemberships,
      expiringSoon,
      totalSales,
      membershipTypes,
      recentActivities,
      lowStockProducts,
      getMembershipColor
    }
  }
})
</script>

<style scoped>
.q-card {
  transition: all 0.3s ease;
}

.q-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 25px 0 rgba(0, 0, 0, 0.1);
}
</style> 