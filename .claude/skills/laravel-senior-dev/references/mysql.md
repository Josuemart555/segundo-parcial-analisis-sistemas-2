# MySQL

## Diseño de esquema

- Normaliza hasta 3FN por defecto; desnormaliza solo con una razón concreta de performance medida, no por anticipación.
- Tipos de columna ajustados al dato real: `unsignedBigInteger`/`foreignId` para IDs, `decimal(10,2)` para dinero (nunca `float`/`double`), `enum` o tabla de lookup para estados fijos, `timestamp` con `useCurrent()` para auditoría.
- `NOT NULL` por defecto; una columna nullable debe ser una decisión consciente (¿de verdad "no tiene valor" es un estado válido?).
- Timestamps (`created_at`, `updated_at`) y soft deletes (`deleted_at`) solo cuando el proyecto los necesita — no los agregues automáticamente a tablas puramente pivote.

## Índices

- Toda columna usada en un `WHERE`, `JOIN` o `ORDER BY` frecuente necesita índice — pero no indexes todo "por si acaso": cada índice cuesta escritura y espacio.
- Claves foráneas ya crean índice automáticamente en MySQL/Laravel — no dupliques.
- Índices compuestos cuando se filtra/ordena por combinación de columnas consistentemente (`index(['status', 'created_at'])`), con el orden de columnas basado en cuál filtra más (columna más selectiva primero, salvo que el orden de la query dicte otra cosa).
- Columnas usadas en búsqueda de texto libre: considera un índice `FULLTEXT` en vez de `LIKE '%term%'`, que no puede usar índice normal.

## Claves foráneas

- Toda relación padre-hijo en el esquema debe tener FK real a nivel de base de datos (no solo a nivel de Eloquent) — protege la integridad aunque algo escriba directo a la DB.
- Decide `onDelete` explícitamente por relación: `cascadeOnDelete()` cuando el hijo no tiene sentido sin el padre (order_items sin order), `restrictOnDelete()` cuando borrar el padre por accidente sería grave (no dejar borrar un `user` que tiene `orders`), `nullOnDelete()` cuando la relación es opcional.

## Migraciones seguras

- Nunca un `down()` vacío en una migración que sí se puede revertir sin pérdida de datos.
- Cambios que sí pierden datos (dropColumn con datos existentes, cambio de tipo incompatible) — avísalo explícitamente al usuario antes de aplicarlo, y sugiere un plan de dos pasos en producción (agregar columna nueva → migrar datos → quitar columna vieja en un release posterior) en vez de un único paso destructivo.
- Migraciones grandes en tablas con muchos datos en producción: evita locks largos — agregar una columna `NOT NULL` sin default en una tabla enorme bloquea escritura; agrega con default o nullable y backfilla después.

## Base de datos de pruebas, aislada de la principal

- **Nunca** ejecutes tests, seeders, `php artisan migrate:fresh`/`migrate:refresh`/`db:wipe`, ni factories contra la conexión MySQL principal que usa el sistema en producción o desarrollo real. Esos comandos truncan o borran tablas — contra la DB equivocada, es pérdida de datos irreversible.
- Antes de correr `php artisan test`, `pest`, o cualquier comando que toque la base de datos, confirma qué conexión está activa: revisa `.env.testing` (Laravel lo usa automáticamente en el entorno `testing`) y la variable `DB_CONNECTION`/`DB_DATABASE` en `phpunit.xml`. Si no existe un `.env.testing` con una base separada, créalo antes de correr tests, no asumas que ya está aislado.
- Para tests rápidos y unitarios, prefiere SQLite en memoria (`DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:` en `phpunit.xml`) cuando el esquema no depende de features específicas de MySQL (JSON avanzado, fulltext, etc.). Para tests de feature que sí dependen de comportamiento MySQL real, usa una base MySQL de test con nombre claramente distinto (`app_testing`, nunca el mismo nombre que la principal con un sufijo fácil de confundir).
- Si en algún punto no es evidente contra qué base de datos va a correr un comando (por ejemplo, un `.env` compartido sin separación clara de entornos), detente y pregúntale al usuario cuál es la base de datos principal antes de ejecutar nada — no lo asumas ni lo verifiques "por prueba y error".

## Transacciones

- Cualquier operación que escribe en más de una tabla y debe ser atómica va dentro de `DB::transaction(function () { ... })` — p. ej. crear una orden y descontar stock a la vez.
- Mantén las transacciones cortas: no hagas llamadas a servicios externos (HTTP, email) dentro de una transacción de DB — eso mantiene locks abiertos más de lo necesario.

## N+1 y eager loading

- Antes de entregar un endpoint que lista modelos con relaciones, revisa si hay N+1: si el Resource accede a `$order->customer->name`, el controlador debe hacer `Order::with('customer')->paginate()`, no dejar que Eloquent dispare una query por fila.
- Usa `withCount()` en vez de cargar toda la relación solo para contar (`$order->items_count` vs `$order->items->count()`).
- En desarrollo, si el proyecto no tiene ya una herramienta de detección de N+1, sugiere `barryvdh/laravel-debugbar` o activar `Model::preventLazyLoading()` en el entorno local (`AppServiceProvider::boot()`) para que Laravel lance excepción si algo hace lazy load — así los N+1 se detectan antes de llegar a producción.
