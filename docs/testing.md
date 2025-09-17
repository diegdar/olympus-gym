## Política de datos de prueba (testing)

Para acelerar el tiempo de ejecución de la suite y reducir uso de memoria, el seeding en entorno `testing` es ligero por defecto:

- `DatabaseSeeder` en `testing` solo ejecuta: `RoleSeeder`, `SubscriptionSeeder`, `ActivitySeeder`, `RoomSeeder` y `UserSeeder` (con menos usuarios).
- `ActivitySchedulesSeeder` reduce el rango a 7 días y 2-3 slots por día, y evita disparar observers.
- `UserAttendanceSeeder` se omite en `testing`.

Esto hace que `\$this->seed()` en tests sea rápido y determinista. Cuando un test necesita datos adicionales debe crear factories o llamar seeders concretos.

### Cómo forzar seed completo

Si necesitas ejecutar el seeding completo (por ejemplo en desarrollo local):

1. Asegúrate de no estar en entorno `testing` (`APP_ENV` distinto de `testing`).
2. Ejecuta:

```
php artisan migrate:fresh --seed
```

### Buenas prácticas en tests

- Prefiere factories para datos específicos del caso de prueba.
- Evita depender del `DatabaseSeeder` completo: añade solo lo que necesitas.
- Para listas horarias, usa `ActivityScheduleFactory` (genera horarios en el futuro, dentro de la ventana que lista el servicio).
