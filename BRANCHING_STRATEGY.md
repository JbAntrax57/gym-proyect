# 🌿 Estrategia de Ramas del Proyecto Gym

## 📋 **Descripción General**

Este proyecto utiliza una estrategia de ramas basada en **Git Flow** adaptada para un proyecto de desarrollo ágil con tres ramas principales.

## 🌳 **Estructura de Ramas**

### **Ramas Principales**

#### 🎯 **`master`** - Producción
- **Propósito**: Código estable y listo para producción
- **Características**:
  - Solo código completamente probado
  - Deploy automático a producción
  - Tags de versiones
  - Merge solo desde `development`

#### 🚀 **`development`** - Desarrollo Integrado
- **Propósito**: Integración de features y testing
- **Características**:
  - Código en desarrollo activo
  - Testing de integración
  - Merge desde `test` y feature branches
  - Deploy a staging/testing

#### 🧪 **`test`** - Testing y Validación
- **Propósito**: Testing de features individuales
- **Características**:
  - Testing de funcionalidades
  - Validación de código
  - Merge desde feature branches
  - Deploy a testing

### **Ramas de Trabajo**

#### 🌿 **`feature/[nombre-feature]`** - Nuevas Funcionalidades
- **Propósito**: Desarrollo de nuevas características
- **Origen**: `development`
- **Destino**: `test` → `development`
- **Ejemplos**:
  - `feature/user-authentication`
  - `feature/membership-management`
  - `feature/pos-system`

#### 🐛 **`hotfix/[descripcion]`** - Correcciones Urgentes
- **Propósito**: Correcciones críticas para producción
- **Origen**: `master`
- **Destino**: `master` + `development`
- **Ejemplos**:
  - `hotfix/security-vulnerability`
  - `hotfix/critical-bug`

#### 🔧 **`bugfix/[descripcion]`** - Correcciones de Bugs
- **Propósito**: Corrección de errores no críticos
- **Origen**: `development`
- **Destino**: `test` → `development`
- **Ejemplos**:
  - `bugfix/login-validation`
  - `bugfix/data-export`

## 📝 **Flujo de Trabajo**

### **1. Desarrollo de Features**

```bash
# 1. Crear feature branch desde development
git checkout development
git pull origin development
git checkout -b feature/nueva-funcionalidad

# 2. Desarrollar y hacer commits
git add .
git commit -m "feat: implementar nueva funcionalidad"

# 3. Push al repositorio remoto
git push origin feature/nueva-funcionalidad

# 4. Crear Pull Request a test
# 5. Testing y validación
# 6. Merge a test
# 7. Merge a development
```

### **2. Testing y Validación**

```bash
# 1. Cambiar a rama test
git checkout test
git pull origin test

# 2. Merge desde feature branch
git merge feature/nueva-funcionalidad

# 3. Testing y validación
# 4. Push a test
git push origin test

# 5. Si todo está bien, merge a development
```

### **3. Integración a Development**

```bash
# 1. Cambiar a development
git checkout development
git pull origin development

# 2. Merge desde test
git merge test

# 3. Testing de integración
# 4. Push a development
git push origin development
```

### **4. Deploy a Producción**

```bash
# 1. Cambiar a master
git checkout master
git pull origin master

# 2. Merge desde development
git merge development

# 3. Crear tag de versión
git tag -a v1.0.0 -m "Release version 1.0.0"

# 4. Push a master y tags
git push origin master
git push origin --tags
```

## 🏷️ **Convención de Commits**

### **Formato**
```
<tipo>(<alcance>): <descripción>

[cuerpo opcional]

[pie opcional]
```

### **Tipos de Commits**
- **`feat`**: Nueva funcionalidad
- **`fix`**: Corrección de bug
- **`docs`**: Documentación
- **`style`**: Cambios de formato
- **`refactor`**: Refactorización de código
- **`test`**: Agregar o modificar tests
- **`chore`**: Tareas de mantenimiento

### **Ejemplos**
```bash
git commit -m "feat(clients): implementar CRUD de clientes"
git commit -m "fix(auth): corregir validación de login"
git commit -m "docs(api): actualizar documentación de endpoints"
git commit -m "refactor(models): optimizar consultas de base de datos"
```

## 🚀 **Deploy y Entornos**

### **Entornos de Deploy**

| Rama | Entorno | URL | Propósito |
|------|---------|-----|-----------|
| `master` | Producción | https://gym-app.com | Usuarios finales |
| `development` | Staging | https://staging.gym-app.com | Testing de integración |
| `test` | Testing | https://test.gym-app.com | Testing de features |

### **Proceso de Deploy**

1. **Feature Branch** → Desarrollo local
2. **Test** → Testing automatizado
3. **Development** → Staging y testing manual
4. **Master** → Producción

## 📋 **Checklist de Merge**

### **Antes de Merge a Test**
- [ ] Código revisado y aprobado
- [ ] Tests unitarios pasando
- [ ] Tests de integración pasando
- [ ] Documentación actualizada
- [ ] Sin conflictos de merge

### **Antes de Merge a Development**
- [ ] Testing en rama test completado
- [ ] QA aprobado
- [ ] Performance testing realizado
- [ ] Security review completado

### **Antes de Merge a Master**
- [ ] Testing de integración completado
- [ ] Staging testing aprobado
- [ ] Backup de producción realizado
- [ ] Rollback plan preparado

## 🛠️ **Comandos Útiles**

### **Gestión de Ramas**
```bash
# Ver todas las ramas
git branch -a

# Cambiar de rama
git checkout nombre-rama

# Crear y cambiar a nueva rama
git checkout -b nombre-rama

# Eliminar rama local
git branch -d nombre-rama

# Eliminar rama remota
git push origin --delete nombre-rama
```

### **Sincronización**
```bash
# Actualizar rama local
git pull origin nombre-rama

# Push de cambios
git push origin nombre-rama

# Ver estado del repositorio
git status

# Ver historial de commits
git log --oneline --graph
```

## 📚 **Recursos Adicionales**

- [Git Flow Workflow](https://nvie.com/posts/a-successful-git-branching-model/)
- [Conventional Commits](https://www.conventionalcommits.org/)
- [GitHub Flow](https://guides.github.com/introduction/flow/)
- [Git Best Practices](https://git-scm.com/book/en/v2)

---

**📝 Nota**: Esta estrategia puede evolucionar según las necesidades del proyecto y el equipo de desarrollo. 