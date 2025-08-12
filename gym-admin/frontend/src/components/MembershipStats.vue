<template>
  <div class="membership-stats">
    <div class="row q-gutter-md q-mb-lg">
      <!-- Total de Membresías -->
      <div class="col-md-3 col-sm-6">
        <q-card class="stats-card total-card">
          <q-card-section class="text-center">
            <div class="text-h4 text-weight-bold text-primary">
              {{ stats.total || 0 }}
            </div>
            <div class="text-subtitle2 text-grey-7">
              Total de Membresías
            </div>
            <q-icon name="fitness_center" size="2rem" color="primary" class="q-mt-sm" />
          </q-card-section>
        </q-card>
      </div>

      <!-- Membresías Activas -->
      <div class="col-md-3 col-sm-6">
        <q-card class="stats-card active-card">
          <q-card-section class="text-center">
            <div class="text-h4 text-weight-bold text-positive">
              {{ stats.active || 0 }}
            </div>
            <div class="text-subtitle2 text-grey-7">
              Membresías Activas
            </div>
            <q-icon name="check_circle" size="2rem" color="positive" class="q-mt-sm" />
          </q-card-section>
        </q-card>
      </div>

      <!-- Ingresos Totales -->
      <div class="col-md-3 col-sm-6">
        <q-card class="stats-card revenue-card">
          <q-card-section class="text-center">
            <div class="text-h4 text-weight-bold text-orange">
              ${{ stats.totalRevenue || '0.00' }}
            </div>
            <div class="text-subtitle2 text-grey-7">
              Ingresos Totales
            </div>
            <q-icon name="attach_money" size="2rem" color="orange" class="q-mt-sm" />
          </q-card-section>
        </q-card>
      </div>

      <!-- Precio Promedio -->
      <div class="col-md-3 col-sm-6">
        <q-card class="stats-card avg-card">
          <q-card-section class="text-center">
            <div class="text-h4 text-weight-bold text-teal">
              ${{ stats.avgPrice || '0.00' }}
            </div>
            <div class="text-subtitle2 text-grey-7">
              Precio Promedio
            </div>
            <q-icon name="trending_up" size="2rem" color="teal" class="q-mt-sm" />
          </q-card-section>
        </q-card>
      </div>
    </div>

    <!-- Distribución por Tipo -->
    <div class="row q-gutter-md q-mb-lg">
      <div class="col-12">
        <q-card>
          <q-card-section>
            <div class="text-h6 q-mb-md">Distribución por Tipo</div>
            <div class="row q-gutter-md">
              <div 
                v-for="(count, type) in stats.typeStats" 
                :key="type"
                class="col-md-4 col-sm-6"
              >
                <div class="type-distribution">
                  <div class="type-label">
                    <q-chip
                      :color="getTypeColor(type)"
                      text-color="white"
                      size="sm"
                    >
                      {{ type }}
                    </q-chip>
                  </div>
                  <div class="type-count text-h5 text-weight-bold">
                    {{ count }}
                  </div>
                  <div class="type-percentage text-caption text-grey-7">
                    {{ getPercentage(count, stats.total) }}% del total
                  </div>
                </div>
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <!-- Distribución por Estado -->
    <div class="row q-gutter-md">
      <div class="col-12">
        <q-card>
          <q-card-section>
            <div class="text-h6 q-mb-md">Distribución por Estado</div>
            <div class="row q-gutter-md">
              <div 
                v-for="(count, status) in stats.statusStats" 
                :key="status"
                class="col-md-4 col-sm-6"
              >
                <div class="status-distribution">
                  <div class="status-label">
                    <q-chip
                      :color="getStatusColor(status)"
                      text-color="white"
                      size="sm"
                    >
                      {{ status }}
                    </q-chip>
                  </div>
                  <div class="status-count text-h5 text-weight-bold">
                    {{ count }}
                  </div>
                  <div class="status-percentage text-caption text-grey-7">
                    {{ getPercentage(count, stats.total) }}% del total
                  </div>
                </div>
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, computed } from 'vue'

export default defineComponent({
  name: 'MembershipStats',
  props: {
    stats: {
      type: Object,
      default: () => ({
        total: 0,
        active: 0,
        totalRevenue: 0,
        avgPrice: 0,
        typeStats: {},
        statusStats: {}
      })
    }
  },
  setup(props) {
    const getTypeColor = (type) => {
      const colors = {
        'Semanal': 'blue',
        'Mensual': 'green',
        'Visita': 'orange'
      }
      return colors[type] || 'grey'
    }

    const getStatusColor = (status) => {
      const colors = {
        'Activa': 'positive',
        'Inactiva': 'negative',
        'Suspendida': 'warning'
      }
      return colors[status] || 'grey'
    }

    const getPercentage = (value, total) => {
      if (total === 0) return 0
      return Math.round((value / total) * 100)
    }

    return {
      getTypeColor,
      getStatusColor,
      getPercentage
    }
  }
})
</script>

<style scoped>
.membership-stats {
  margin-bottom: 2rem;
}

.stats-card {
  transition: transform 0.2s ease-in-out;
  border-radius: 12px;
}

.stats-card:hover {
  transform: translateY(-2px);
}

.total-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.active-card {
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  color: white;
}

.revenue-card {
  background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
  color: white;
}

.avg-card {
  background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
  color: white;
}

.type-distribution,
.status-distribution {
  text-align: center;
  padding: 1rem;
  border-radius: 8px;
  background: #f8f9fa;
}

.type-label,
.status-label {
  margin-bottom: 0.5rem;
}

.type-count,
.status-count {
  margin-bottom: 0.25rem;
}

.type-percentage,
.status-percentage {
  opacity: 0.7;
}
</style>
