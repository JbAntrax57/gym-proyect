<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated class="header-gym">
      <q-toolbar>
        <q-btn
          flat
          dense
          round
          icon="menu"
          aria-label="Menu"
          @click="toggleLeftDrawer"
          class="text-gym-white"
        />

        <q-toolbar-title class="row items-center text-gym-white">
          <q-icon name="fitness_center" size="2rem" class="q-mr-sm animate-pulse-gym" />
          <span class="text-h6 text-weight-bold">GYM ADMIN</span>
        </q-toolbar-title>

        <div class="row items-center q-gutter-sm">
          <q-btn flat round icon="notifications" size="sm" class="text-gym-white">
            <q-badge color="red" floating>{{ notificationsCount }}</q-badge>
          </q-btn>
          <q-btn flat round icon="account_circle" size="sm" class="text-gym-white">
            <q-menu>
              <q-list style="min-width: 150px">
                <q-item clickable v-close-popup>
                  <q-item-section>Perfil</q-item-section>
                </q-item>
                <q-item clickable v-close-popup>
                  <q-item-section>Configuración</q-item-section>
                </q-item>
                <q-separator />
                <q-item clickable v-close-popup>
                  <q-item-section>Cerrar Sesión</q-item-section>
                </q-item>
              </q-list>
            </q-menu>
          </q-btn>
        </div>
      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      bordered
      class="sidebar-gym"
    >
      <q-list class="text-gym-white">
        <q-item-label header class="text-gym-white q-pa-md">
          <q-icon name="fitness_center" class="q-mr-sm" />
          <span class="text-weight-bold">SISTEMA DE GIMNASIO</span>
        </q-item-label>

        <q-item
          v-for="link in navigationLinks"
          :key="link.title"
          :to="link.path"
          clickable
          v-ripple
          :active="$route.path === link.path"
          active-class="bg-gym-red text-gym-white"
          class="sidebar-item-gym"
        >
          <q-item-section avatar>
            <q-icon :name="link.icon" />
          </q-item-section>
          <q-item-section>
            <q-item-label>{{ link.title }}</q-item-label>
            <q-item-label caption>{{ link.caption }}</q-item-label>
          </q-item-section>
        </q-item>

        <q-separator class="q-my-md bg-gym-red" />

        <q-item-label header class="text-gym-white q-pa-md">
          <q-icon name="analytics" class="q-mr-sm" />
          <span class="text-weight-bold">REPORTES</span>
        </q-item-label>

        <q-item
          to="/reports"
          clickable
          v-ripple
          :active="$route.path === '/reports'"
          active-class="bg-gym-red text-gym-white"
          class="sidebar-item-gym"
        >
          <q-item-section avatar>
            <q-icon name="assessment" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Reportes</q-item-label>
            <q-item-label caption>Análisis y estadísticas</q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
import { defineComponent, ref, computed } from 'vue'
import { useRoute } from 'vue-router'

export default defineComponent({
  name: 'MainLayout',

  setup () {
    const leftDrawerOpen = ref(false)
    const route = useRoute()

    const navigationLinks = [
      {
        title: 'Dashboard',
        caption: 'Vista general del sistema',
        icon: 'dashboard',
        path: '/'
      },
      {
        title: 'Clientes',
        caption: 'Gestión de clientes',
        icon: 'people',
        path: '/clients'
      },
      {
        title: 'Membresías',
        caption: 'Control de membresías',
        icon: 'fitness_center',
        path: '/memberships'
      },
      {
        title: 'Productos',
        caption: 'Inventario del gym',
        icon: 'inventory',
        path: '/products'
      },
      {
        title: 'Punto de Venta',
        caption: 'Ventas y transacciones',
        icon: 'point_of_sale',
        path: '/pos'
      }
    ]

    const notificationsCount = ref(3)

    return {
      navigationLinks,
      notificationsCount,
      leftDrawerOpen,
      toggleLeftDrawer () {
        leftDrawerOpen.value = !leftDrawerOpen.value
      }
    }
  }
})
</script>

<style scoped>
/* Estilos del sidebar del gym */
.sidebar-item-gym {
  border-radius: var(--gym-radius-md);
  margin: 0 var(--gym-spacing-sm);
  transition: var(--gym-transition);
}

.sidebar-item-gym:hover {
  background: var(--gym-gradient-red) !important;
  transform: translateX(4px);
}

.sidebar-item-gym.active {
  background: var(--gym-gradient-red) !important;
  border-radius: var(--gym-radius-md);
  margin: 0 var(--gym-spacing-sm);
  box-shadow: var(--gym-shadow-red);
}

/* Animaciones del header */
.animate-pulse-gym {
  animation: pulseGym 2s infinite;
}

@keyframes pulseGym {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
  }
}
</style>
