# 🏋️‍♂️ Frontend Quasar - Proyecto Gym

Frontend de la aplicación de gestión para gimnasios desarrollado con Quasar Framework y Vue.js 3.

## 🚀 **Características**

- **Framework**: Quasar 2.6.0
- **Vue.js**: 3.x
- **UI Framework**: Material Design con Quasar
- **Build Tool**: Vite
- **Lenguaje**: JavaScript/TypeScript
- **Arquitectura**: SPA (Single Page Application)

## 📁 **Estructura del Proyecto**

```
frontend/
├── src/
│   ├── components/            # Componentes reutilizables
│   ├── layouts/               # Layouts de la aplicación
│   ├── pages/                 # Páginas de la aplicación
│   │   ├── clients/          # Gestión de clientes
│   │   ├── dashboard/        # Dashboard principal
│   │   ├── memberships/      # Gestión de membresías
│   │   ├── products/         # Gestión de productos
│   │   ├── pos/              # Punto de venta
│   │   └── reports/          # Reportes y estadísticas
│   ├── router/                # Configuración de rutas
│   ├── services/              # Servicios de API
│   └── css/                   # Estilos globales
├── public/                    # Archivos públicos
└── quasar.config.js           # Configuración de Quasar
```

## ⚙️ **Instalación**

### **Requisitos Previos**
- Node.js 16.x o superior
- npm 8.x o superior
- Quasar CLI 2.x

### **Instalar Quasar CLI**
```bash
npm install -g @quasar/cli
```

### **Pasos de Instalación**
```bash
# 1. Instalar dependencias
npm install

# 2. Iniciar servidor de desarrollo
quasar dev

# 3. Construir para producción
quasar build
```

## 🌐 **Páginas Implementadas**

### **Dashboard** (`/`)
- Vista general del gimnasio
- Estadísticas principales
- Acceso rápido a módulos

### **Clientes** (`/clients`)
- ✅ **CRUD completo** implementado
- Lista paginada de clientes
- Búsqueda y filtros
- Formulario de creación/edición
- Gestión de estado activo/inactivo
- Sistema de puntos de lealtad

### **Membresías** (`/memberships`)
- Gestión de tipos de membresía
- Asignación a clientes
- Control de fechas de expiración

### **Productos** (`/products`)
- Inventario del gimnasio
- Gestión de stock
- Categorización

### **Punto de Venta** (`/pos`)
- Ventas de productos
- Ventas de membresías
- Sistema de pagos

### **Reportes** (`/reports`)
- Estadísticas de ventas
- Reporte de clientes
- Análisis de membresías

## 🔧 **Configuración**

### **Configuración de Desarrollo (quasar.config.js)**
```javascript
devServer: {
  port: 9000,
  proxy: {
    '/api': {
      target: 'http://127.0.0.1:8000',
      changeOrigin: true
    }
  }
}
```

### **Variables de Entorno**
```bash
# .env
VITE_API_URL=http://127.0.0.1:8000/api
VITE_APP_TITLE=Gym Management System
```

## 🌐 **Servicios de API**

### **API Service** (`src/services/api.js`)
- Configuración de Axios
- Interceptores de request/response
- Manejo de errores centralizado
- Base URL configurable

### **Client Service** (`src/services/clientService.js`)
- CRUD completo de clientes
- Paginación
- Filtros y búsqueda
- Manejo de respuestas de Laravel

## 🎨 **Componentes UI**

### **Componentes Principales**
- **MainLayout**: Layout principal con navegación
- **ClientsPage**: Gestión completa de clientes
- **DashboardPage**: Dashboard principal
- **EssentialLink**: Enlaces de navegación

### **Características de UI**
- Material Design con Quasar
- Responsive design
- Tema personalizable
- Iconos integrados
- Componentes accesibles

## 🚀 **Scripts Disponibles**

```bash
# Desarrollo
npm run dev          # Iniciar servidor de desarrollo
quasar dev           # Alternativa con Quasar CLI

# Construcción
npm run build        # Construir para producción
quasar build         # Alternativa con Quasar CLI

# Testing
npm run test         # Ejecutar tests
npm run test:unit    # Tests unitarios
npm run test:e2e     # Tests end-to-end

# Linting
npm run lint         # Verificar código
npm run lint:fix     # Corregir problemas automáticamente
```

## 🔌 **Integración con Backend**

### **Configuración de CORS**
El backend incluye middleware CORS personalizado para permitir requests desde el frontend.

### **Endpoints Utilizados**
- `GET /api/clients` - Listar clientes
- `POST /api/clients` - Crear cliente
- `PUT /api/clients/{id}` - Actualizar cliente
- `DELETE /api/clients/{id}` - Eliminar cliente

### **Estructura de Respuesta**
```javascript
// Respuesta de Laravel
{
  success: true,
  data: {
    data: [...],        // Datos paginados
    current_page: 1,
    total: 10,
    per_page: 15
  },
  message: "Mensaje de éxito"
}
```

## 🧪 **Testing**

### **Configuración de Tests**
- Jest para tests unitarios
- Cypress para tests e2e
- Mocks para servicios de API

### **Ejecutar Tests**
```bash
# Tests unitarios
npm run test:unit

# Tests e2e
npm run test:e2e

# Todos los tests
npm run test
```

## 📱 **Responsive Design**

### **Breakpoints**
- **xs**: < 600px (móviles)
- **sm**: 600px - 1024px (tablets)
- **md**: 1024px - 1440px (desktop)
- **lg**: > 1440px (pantallas grandes)

### **Adaptaciones**
- Navegación colapsable en móviles
- Tablas responsivas
- Formularios adaptativos
- Modales optimizados para touch

## 🚀 **Despliegue**

### **Producción**
```bash
# Construir aplicación
quasar build

# Los archivos se generan en dist/spa/
# Subir a servidor web estático
```

### **Configuración de Servidor**
- Servidor web estático (Nginx, Apache)
- Configurar rewrite rules para SPA
- Headers de cache apropiados
- Compresión gzip/brotli

## 🔍 **Debugging**

### **Herramientas de Desarrollo**
- Vue DevTools
- Quasar DevTools
- Console logs detallados
- Network tab del navegador

### **Logs de la Aplicación**
```javascript
// Logs detallados en servicios
console.log('🔍 ClientService - Parámetros:', params)
console.log('📡 ClientService - Respuesta:', response)
console.log('✅ ClientService - Operación exitosa')
```

## 🤝 **Contribución**

1. Crear feature branch desde `development`
2. Desarrollar funcionalidad
3. Ejecutar tests
4. Verificar linting
5. Crear Pull Request a `test`

## 📚 **Recursos**

- [Documentación de Quasar](https://quasar.dev)
- [Vue.js 3](https://vuejs.org)
- [Vite](https://vitejs.dev)
- [Material Design](https://material.io)

## 📄 **Licencia**

Este proyecto está bajo la Licencia MIT.

---

**Desarrollado con ❤️ usando Quasar Framework y Vue.js**
