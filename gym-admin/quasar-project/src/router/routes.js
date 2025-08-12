
const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { 
        path: '', 
        name: 'dashboard',
        component: () => import('pages/dashboard/DashboardPage.vue'),
        meta: { title: 'Dashboard' }
      },
      { 
        path: '/clients', 
        name: 'clients',
        component: () => import('pages/clients/ClientsPage.vue'),
        meta: { title: 'Clientes' }
      },
      { 
        path: '/memberships', 
        name: 'memberships',
        component: () => import('pages/memberships/MembershipsPage.vue'),
        meta: { title: 'Membresías' }
      },
      { 
        path: '/products', 
        name: 'products',
        component: () => import('pages/products/ProductsPage.vue'),
        meta: { title: 'Productos' }
      },
      { 
        path: '/pos', 
        name: 'pos',
        component: () => import('pages/pos/PosPage.vue'),
        meta: { title: 'Punto de Venta' }
      },
      { 
        path: '/reports', 
        name: 'reports',
        component: () => import('pages/reports/ReportsPage.vue'),
        meta: { title: 'Reportes' }
      }
    ]
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue')
  }
]

export default routes
