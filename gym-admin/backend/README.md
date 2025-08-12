# 🏋️‍♂️ Backend Laravel - Proyecto Gym

Backend de la aplicación de gestión para gimnasios desarrollado con Laravel 12.

## 🚀 **Características**

- **Framework**: Laravel 12
- **PHP**: 8.2+
- **Base de Datos**: SQLite (desarrollo) / MySQL/PostgreSQL (producción)
- **API**: RESTful con autenticación
- **Arquitectura**: MVC con Eloquent ORM

## 📁 **Estructura del Proyecto**

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/           # Controladores de la API
│   │   └── Middleware/        # Middleware personalizado
│   ├── Models/                # Modelos Eloquent
│   └── Providers/             # Proveedores de servicios
├── database/
│   ├── migrations/            # Migraciones de base de datos
│   └── seeders/               # Seeders para datos iniciales
├── routes/
│   └── api.php                # Rutas de la API
└── ...
```

## ⚙️ **Instalación**

### **Requisitos Previos**
- PHP 8.2 o superior
- Composer
- SQLite (desarrollo) o MySQL/PostgreSQL

### **Pasos de Instalación**
```bash
# 1. Instalar dependencias
composer install

# 2. Configurar variables de entorno
cp .env.example .env

# 3. Generar clave de aplicación
php artisan key:generate

# 4. Ejecutar migraciones
php artisan migrate

# 5. Ejecutar seeders
php artisan db:seed

# 6. Iniciar servidor de desarrollo
php artisan serve
```

## 🌐 **API Endpoints**

### **Clientes**
- `GET /api/clients` - Listar clientes
- `POST /api/clients` - Crear cliente
- `GET /api/clients/{id}` - Obtener cliente
- `PUT /api/clients/{id}` - Actualizar cliente
- `DELETE /api/clients/{id}` - Eliminar cliente

### **Tipos de Membresía**
- `GET /api/membership-types` - Listar tipos de membresía

### **Productos**
- `GET /api/products` - Listar productos
- `POST /api/products` - Crear producto
- `GET /api/products/{id}` - Obtener producto
- `PUT /api/products/{id}` - Actualizar producto
- `DELETE /api/products/{id}` - Eliminar producto

## 🗄️ **Base de Datos**

### **Tablas Principales**
- `clients` - Información de clientes
- `membership_types` - Tipos de membresía disponibles
- `client_memberships` - Relación clientes-membresías
- `products` - Productos del gimnasio
- `sales` - Ventas realizadas
- `sale_items` - Items de cada venta
- `payments_history` - Historial de pagos
- `notifications` - Sistema de notificaciones
- `loyalty_discounts` - Descuentos por lealtad

### **Migraciones**
```bash
# Ejecutar migraciones
php artisan migrate

# Revertir migraciones
php artisan migrate:rollback

# Ver estado de migraciones
php artisan migrate:status
```

### **Seeders**
```bash
# Ejecutar todos los seeders
php artisan db:seed

# Ejecutar seeder específico
php artisan db:seed --class=MembershipTypeSeeder
```

## 🧪 **Testing**

```bash
# Ejecutar tests
php artisan test

# Ejecutar tests con coverage
php artisan test --coverage

# Ejecutar tests específicos
php artisan test --filter=ClientTest
```

## 🔧 **Configuración de Desarrollo**

### **Variables de Entorno (.env)**
```bash
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

### **CORS**
El proyecto incluye middleware CORS personalizado para permitir requests desde el frontend.

## 📊 **Logs y Debugging**

```bash
# Ver logs de la aplicación
tail -f storage/logs/laravel.log

# Limpiar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## 🚀 **Despliegue**

### **Producción**
```bash
# Instalar dependencias de producción
composer install --optimize-autoloader --no-dev

# Cachear configuraciones
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimizar autoloader
composer dump-autoload --optimize
```

### **Variables de Entorno de Producción**
```bash
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_DATABASE=your-db-name
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password
```

## 🤝 **Contribución**

1. Crear feature branch desde `development`
2. Desarrollar funcionalidad
3. Ejecutar tests
4. Crear Pull Request a `test`
5. Después de testing, merge a `development`

## 📚 **Recursos**

- [Documentación de Laravel](https://laravel.com/docs)
- [Eloquent ORM](https://laravel.com/docs/eloquent)
- [API Resources](https://laravel.com/docs/api-resources)
- [Testing](https://laravel.com/docs/testing)

## 📄 **Licencia**

Este proyecto está bajo la Licencia MIT.

---

**Desarrollado con ❤️ usando Laravel Framework**
