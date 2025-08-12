<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated class="bg-primary text-white">
      <q-toolbar>
        <q-btn
          flat
          dense
          round
          icon="menu"
          aria-label="Menu"
          @click="toggleLeftDrawer"
        />

        <q-toolbar-title class="row items-center">
          <q-icon name="fitness_center" size="2rem" class="q-mr-sm" />
          <span class="text-h6">Gym Admin</span>
        </q-toolbar-title>

        <div class="row items-center q-gutter-sm">
          <q-btn flat round icon="notifications" size="sm">
            <q-badge color="red" floating>{{ notificationsCount }}</q-badge>
          </q-btn>
          <q-btn flat round icon="account_circle" size="sm">
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
      class="bg-grey-1"
    >
      <q-list>
        <q-item-label header class="text-grey-8">
          <q-icon name="fitness_center" class="q-mr-sm" />
          Sistema de Gimnasio
        </q-item-label>

        <q-item
          v-for="link in navigationLinks"
          :key="link.title"
          :to="link.path"
          clickable
          v-ripple
          :active="$route.path === link.path"
          active-class="bg-primary text-white"
        >
          <q-item-section avatar>
            <q-icon :name="link.icon" />
          </q-item-section>
          <q-item-section>
            <q-item-label>{{ link.title }}</q-item-label>
            <q-item-label caption>{{ link.caption }}</q-item-label>
          </q-item-section>
        </q-item>

        <q-separator class="q-my-md" />

        <q-item-label header class="text-grey-8">
          <q-icon name="analytics" class="q-mr-sm" />
          Reportes
        </q-item-label>

        <q-item
          to="/reports"
          clickable
          v-ripple
          :active="$route.path === '/reports'"
          active-class="bg-primary text-white"
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
.q-item.active {
  border-radius: 8px;
  margin: 0 8px;
}

.q-item:hover {
  background-color: rgba(0, 0, 0, 0.05);
  border-radius: 8px;
  margin: 0 8px;
}
</style>
