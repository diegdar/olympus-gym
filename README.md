# 🏋️ Olympus Gym - Gestión de Gimnasio

¡Bienvenido a **Olympus Gym**! 🚀  
Esta aplicación web te permite gestionar un gimnasio de forma profesional y moderna, con una experiencia pensada tanto para administradores como para socios.  
[🌐 Accede a la demo aquí](https://project-gym.diegochacondev.es/)

---

## ✨ Características principales

- **Registro de usuarios e inicio de sesión**:  
  Puedes crear tu cuenta mediante un formulario tradicional, o iniciar sesión rápidamente usando tu cuenta de **GitHub** o **Google**.  
- **Gestión de roles y permisos**:  
  El sistema implementa roles y permisos granulares para controlar el acceso a cada funcionalidad.
- **Datos ficticios pre-cargados**:  
  La aplicación ya viene poblada con usuarios, actividades, horarios y salas para que puedas probar todas las funcionalidades sin tener que crear datos manualmente.
- **Dark mode y diseño responsivo**:  
  Todas las vistas están optimizadas para dispositivos móviles y cuentan con modo oscuro por defecto.
- **Tests automáticos**:  
  Incluye tests unitarios y funcionales para garantizar la calidad y prevenir regresiones.

---

## 👤 Usuarios de prueba

Estos usuarios están creados por defecto en la base de datos (ver [`UserSeeder`](database/seeders/UserSeeder.php)):

| Usuario                | Email                           | Contraseña     | Rol         | Permisos principales                                                                 |
|------------------------|---------------------------------|---------------|-------------|--------------------------------------------------------------------------------------|
| diego_superadmin       | diego_superadmin@superadmin.com | PassNix$123   | super-admin | Acceso total: gestión de usuarios, roles, actividades, salas, suscripciones, etc.    |
| luis_admin             | luis_admin@admin.com            | PassNix$123   | admin       | Gestión de actividades, salas, horarios, estadísticas de suscripciones.              |
| raul_socio             | raul_socio@socio.com            | PassNix$123   | member      | Reservar actividades, ver suscripción, gestionar perfil y reservas.                  |

> Puedes usar estos datos para iniciar sesión y probar la aplicación con diferentes permisos.

---

## 🛡️ Roles y permisos

- **super-admin**:  
  - Acceso completo a todas las funcionalidades.
  - Puede crear, editar y eliminar usuarios, roles, actividades, salas y horarios.
- **admin**:  
  - Gestión de actividades, salas, horarios y estadísticas.
  - No puede gestionar roles ni usuarios.
- **member**:  
  - Reservar actividades, ver y cambiar su suscripción, gestionar su perfil.
  - No puede acceder a la administración.

Los permisos están definidos en [`RoleSeeder`](database/seeders/RoleSeeder.php) y se gestionan con Spatie Laravel Permission.

---

## 🧪 Tests

La aplicación incluye tests unitarios y funcionales para asegurar la calidad del código.  
Para ejecutarlos, usa el siguiente comando:

```sh
php artisan test
```

- Los tests cubren casos de registro, gestión de actividades, reservas, roles y permisos.
- El entorno de testing se inicializa con datos ligeros y deterministas (ver [`docs/testing.md`](docs/testing.md)).

---

## 📦 Datos ficticios

La base de datos se pobla automáticamente con actividades, horarios, salas y usuarios para que puedas probar la aplicación sin configuraciones adicionales.  
Esto se realiza mediante migraciones y seeders, siguiendo buenas prácticas de Laravel.

---

## 📲 Registro y acceso

- **Formulario de registro**:  
  Completa tus datos y acepta la política de privacidad.
- **Acceso con GitHub o Google**:  
  Haz clic en cualquiera de los botones para iniciar sesión rapidamente(si ya te habias registrado previamente) usando tu cuenta de Github o Google.

---

## 📞 Contacto del desarrollador

**Dev:** Diego Chacon Delgado  
**Email:** info@diegochacondev.es

---

¡Gracias por probar Olympus Gym! 💪  