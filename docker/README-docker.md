# Laravel Gym - Guía de Docker

Esta guía explica cómo trabajar con los contenedores Docker del proyecto Laravel Gym.

## URLs del Proyecto

- **Aplicación Web**: [http://localhost:8088](http://localhost:8088)
- **phpMyAdmin**: [http://localhost:8081](http://localhost:8081)
  - Usuario: root
  - Contraseña: root

## Gestión de Contenedores

### Iniciar los contenedores
```bash
docker compose up -d
```

### Detener los contenedores
```bash
docker compose down
```

### Detener y eliminar todo (contenedores, volúmenes y networks)
```bash
docker compose down -v --remove-orphans
```

### Reconstruir los contenedores (cuando hay cambios en Dockerfile)
```bash
docker compose build --no-cache
docker compose up -d
```

### Ver el estado de los contenedores
```bash
docker compose ps
```

## Comandos de Laravel

Para ejecutar comandos de Laravel dentro del contenedor, usa el siguiente formato:

```bash
docker compose exec app php artisan <comando>
```

### Comandos comunes:

1. **Migraciones**
   ```bash
   # Ejecutar migraciones
   docker compose exec app php artisan migrate

   # Revertir todas las migraciones y volver a ejecutarlas
   docker compose exec app php artisan migrate:fresh

   # Ejecutar migraciones con datos de prueba
   docker compose exec app php artisan migrate:fresh --seed
   ```

2. **Cache**
   ```bash
   # Limpiar cache
   docker compose exec app php artisan cache:clear

   # Limpiar y regenerar cache de configuración
   docker compose exec app php artisan config:clear
   docker compose exec app php artisan config:cache

   # Limpiar cache de rutas
   docker compose exec app php artisan route:clear
   ```

3. **Crear nuevos archivos**
   ```bash
   # Crear un controlador
   docker compose exec app php artisan make:controller NombreController

   # Crear un modelo
   docker compose exec app php artisan make:model Nombre

   # Crear una migración
   docker compose exec app php artisan make:migration create_nombre_table
   ```

## Composer

Para ejecutar comandos de Composer:

```bash
# Instalar dependencias
docker compose exec app composer install

# Actualizar dependencias
docker compose exec app composer update

# Agregar un nuevo paquete
docker compose exec app composer require nombre/paquete
```

## Solución de Problemas

Si encuentras problemas con los permisos:

```bash
# Corregir permisos de storage
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
```

Si la base de datos no se puede conectar:
1. Verifica que el contenedor de MySQL esté corriendo: `docker compose ps`
2. Revisa los logs: `docker compose logs mysql`
3. Asegúrate de que las credenciales en el archivo .env coincidan con las del docker-compose.yml

## Puertos Utilizados

- **8088**: Apache (Aplicación Laravel)
- **8081**: phpMyAdmin
- **3307**: MySQL

## Notas Importantes

- Los cambios en el código se reflejan automáticamente gracias al volumen montado
- La base de datos persiste entre reinicios gracias al volumen mysql-data
- Si modificas el Dockerfile, necesitas reconstruir los contenedores con `docker compose build --no-cache`
