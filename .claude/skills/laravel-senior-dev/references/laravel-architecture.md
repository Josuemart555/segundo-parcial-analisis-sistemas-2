# Arquitectura Laravel

## Estructura de proyecto

Sigue la estructura estándar de Laravel; no inventes capas nuevas a menos que el proyecto ya las tenga:

```
app/
  Http/
    Controllers/Api/V1/   # controladores de API, agrupados por versión
    Requests/              # Form Requests (validación)
    Resources/              # API Resources (transformación de salida)
    Middleware/
  Models/
  Actions/ o Services/     # solo si hay lógica de negocio no trivial (ver SKILL.md)
  Jobs/
  Events/
  Listeners/
  Providers/
database/
  migrations/
  factories/
  seeders/
routes/
  api.php                  # todo lo de API vive aquí, nunca en web.php
tests/
  Feature/
  Unit/
```

## Eloquent

- Un modelo por tabla, con `$fillable` explícito (evita `$guarded = []` salvo que sepas exactamente por qué).
- Define las relaciones (`hasMany`, `belongsTo`, etc.) en el modelo, con return type hint (`BelongsTo`, `HasMany`...).
- Usa `casts()` (Laravel 11+) o `$casts` para fechas, JSON, enums y booleanos — evita convertir tipos a mano en el controlador.
- Scopes locales (`scopeActive`, etc.) para filtros que se repiten en más de un lugar, en vez de repetir `where(...)` por todo el código.
- Nunca pongas lógica de negoción pesada en accessors/mutators si afecta el rendimiento de listados (se ejecutan por cada fila serializada).

## Migraciones

- Una migración por cambio lógico, nombre descriptivo: `create_orders_table`, `add_status_to_orders_table`.
- Siempre define `down()` cuando el cambio es reversible de forma segura (casi siempre lo es para crear tablas/columnas). Si no es reversible sin pérdida de datos (p. ej. borrar una columna con datos), dilo explícitamente en tu respuesta al usuario.
- Claves foráneas con `foreignId('user_id')->constrained()->cascadeOnDelete()` (o `restrictOnDelete()` cuando borrar en cascada sería peligroso) — nunca una FK sin `onDelete` explícito pensado.
- Ver [mysql.md](mysql.md) para índices y diseño de esquema.

## Form Requests

- Toda validación de entrada de un endpoint va en un Form Request dedicado (`php artisan make:request StoreOrderRequest`), nunca `$request->validate()` inline en el controlador salvo para endpoints triviales de un solo campo.
- El método `authorize()` debe reflejar la regla de negocio real (¿puede este usuario crear/editar este recurso?), no simplemente `return true;` por defecto sin pensarlo.
- Mensajes de error personalizados solo cuando el mensaje por defecto de Laravel no es claro para el consumidor de la API.

## API Resources

- Toda respuesta JSON de un endpoint pasa por un `JsonResource` o `ResourceCollection`, nunca se serializa un modelo directamente ni se arma un array a mano en el controlador.
- El Resource controla exactamente qué campos se exponen — nunca expongas columnas sensibles (`password`, tokens, etc.) por accidente vía `Model::all()->toJson()`.
- Usa `whenLoaded()` para relaciones opcionales, así el mismo Resource sirve para listados (sin relaciones cargadas) y detalle (con relaciones).

## Jobs, Events, Listeners

- Cualquier trabajo que tarde más de ~200ms o dependa de un servicio externo (email, PDF, integraciones) va a un Job en cola (`php artisan make:job`), no se ejecuta síncrono en el request.
- Usa Events + Listeners cuando una acción dispara efectos secundarios desacoplados (p. ej. "OrderCreated" dispara notificación + actualización de inventario) — no todo necesita eventos, solo cuando hay más de un listener o el desacoplamiento aporta claridad real.

## Validación y manejo de excepciones

- Configura el manejador de excepciones (`bootstrap/app.php` en Laravel 11+, o `app/Exceptions/Handler.php` en versiones previas) para que las respuestas de API siempre devuelvan JSON con un formato consistente — ver [api-design.md](api-design.md).
- Lanza excepciones específicas de dominio (`class InsufficientStockException extends Exception`) en vez de `abort(400, 'mensaje')` genérico cuando la regla de negocio es reutilizable.

## Service Providers

- No agregues lógica de arranque directamente en `AppServiceProvider` si crece — crea un provider dedicado (`DomainEventServiceProvider`, etc.) solo cuando `AppServiceProvider` se vuelve difícil de leer, no de entrada.
