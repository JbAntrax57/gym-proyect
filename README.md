# 🏋️‍♂️ Proyecto Gym - Sistema de Gestión

Sistema completo de gestión para gimnasios que incluye gestión de clientes, membresías, productos, punto de venta y reportes.

## 🏗️ **Arquitectura del Proyecto**

- **Backend**: Laravel 12 + PHP 8.2+ + SQLite
- **Frontend**: Quasar 2.6.0 + Vue.js 3
- **Base de Datos**: SQLite (desarrollo) / MySQL/PostgreSQL (producción)
- **API**: RESTful con autenticación

## 📁 **Estructura del Proyecto**

```
Proyecto Gym/
├── gym-admin/                 # Directorio principal del proyecto
│   ├── gym-backend/          # Backend Laravel
│   │   ├── app/              # Lógica de aplicación
│   │   ├── database/         # Migraciones y seeders
│   │   ├── routes/           # Rutas de la API
│   │   └── ...
│   └── quasar-project/       # Frontend Quasar
│       ├── src/              # Código fuente Vue.js
│       ├── public/           # Archivos públicos
│       └── ...
└── README.md                 # Este archivo
```

## 🚀 **Requisitos del Sistema**

- **PHP**: 8.2 o superior
- **Composer**: Última versión estable
- **Node.js**: 16.x o superior
- **npm**: 8.x o superior
- **Quasar CLI**: 2.x

## ⚙️ **Instalación y Configuración**

### 1. **Clonar el Repositorio**
```bash
git clone https://github.com/JbAntrax57/gym-proyect.git
cd gym-proyect
```

### 2. **Configurar el Backend (Laravel)**
```bash
cd gym-admin/gym-backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

### 3. **Configurar el Frontend (Quasar)**
```bash
cd gym-admin/quasar-project
npm install
quasar dev
```

## 🌐 **Acceso a la Aplicación**

- **Backend API**: http://127.0.0.1:8000
- **Frontend**: http://localhost:9000
- **API Documentation**: http://127.0.0.1:8000/api

## 📊 **Módulos Implementados**

### ✅ **Completados**
- [x] **Gestión de Clientes**: CRUD completo con validaciones
- [x] **Base de Datos**: Migraciones y modelos Eloquent
- [x] **API REST**: Endpoints para clientes
- [x] **Frontend**: Interfaz de usuario con Quasar
- [x] **Conexión Backend-Frontend**: Comunicación API funcional

### 🚧 **En Desarrollo**
- [ ] **Gestión de Membresías**: Tipos y asignación a clientes
- [ ] **Módulo de Productos**: Inventario y gestión
- [ ] **Punto de Venta (POS)**: Ventas de productos y membresías
- [ ] **Sistema de Reportes**: Dashboard y estadísticas

### 📋 **Pendientes**
- [ ] **Sistema de Notificaciones**: Alertas y recordatorios
- [ ] **Gestión de Pagos**: Historial y estados
- [ ] **Sistema de Lealtad**: Puntos y descuentos
- [ ] **Backup Automático**: Respaldo de datos
- [ ] **Exportación**: PDF y Excel

## 🔧 **Configuración de Desarrollo**

### **Variables de Entorno**
```bash
# Backend (.env)
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
APP_DEBUG=true
APP_ENV=local

# Frontend (quasar.config.js)
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

## 📚 **Documentación de la API**

### **Endpoints de Clientes**
- `GET /api/clients` - Listar clientes
- `POST /api/clients` - Crear cliente
- `GET /api/clients/{id}` - Obtener cliente
- `PUT /api/clients/{id}` - Actualizar cliente
- `DELETE /api/clients/{id}` - Eliminar cliente

## 🧪 **Testing**

```bash
# Backend
cd gym-admin/gym-backend
php artisan test

# Frontend
cd gym-admin/quasar-project
npm run test
```

## 📦 **Despliegue**

### **Backend (Laravel)**
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### **Frontend (Quasar)**
```bash
quasar build
```

## 🤝 **Contribución**

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📄 **Licencia**

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 👥 **Autores**

- **JbAntrax57** - *Desarrollo inicial* - [GitHub](https://github.com/JbAntrax57)

## 🙏 **Agradecimientos**

- Laravel Framework
- Quasar Framework
- Vue.js Community
- Todos los contribuidores

---

**⭐ Si te gusta este proyecto, dale una estrella en GitHub!** 