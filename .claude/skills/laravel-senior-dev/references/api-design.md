# Diseño de API REST con Laravel

## Rutas y versionado

- Todas las rutas de API viven en `routes/api.php`, nunca mezcladas con `web.php`.
- Agrupa por versión desde el día uno, aunque solo exista v1: `Route::prefix('v1')->group(...)` o vía namespace `Api\V1`. Esto evita romper clientes cuando llegue v2.
- Nombres de recursos en plural y kebab/snake consistente con el resto del proyecto: `/v1/orders`, `/v1/order-items`.
- Usa `Route::apiResource()` para CRUDs estándar en vez de declarar las 5 rutas a mano — más corto y menos propenso a errores de método HTTP.
- Rutas anidadas solo un nivel (`/orders/{order}/items`), evita anidar más de eso — pasa a query params o a un endpoint dedicado.

## Controladores

- `php artisan make:controller Api/V1/OrderController --api --model=Order` como punto de partida.
- Un método por acción REST estándar (index, store, show, update, destroy). Si necesitas una acción que no encaja en CRUD (p. ej. `POST /orders/{order}/cancel`), créala como controlador de acción única (`CancelOrderController` con `__invoke`) en vez de inflar `OrderController`.
- El controlador orquesta (valida vía Form Request, llama al modelo/Action, devuelve un Resource) — no contiene lógica de negocio compleja inline.

## Respuestas y formato de error

Usa un formato consistente en todo el API. Para éxito, deja que el Resource/ResourceCollection controle el shape. Para error, estandariza así (ajusta al estilo del proyecto si ya existe uno):

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

- Errores de validación → 422 (automático con Form Requests).
- No autenticado → 401. Autenticado pero sin permiso → 403.
- Recurso no encontrado → 404 (Laravel lo hace automático con route model binding + `ModelNotFoundException`).
- Conflicto de negocio (p. ej. stock insuficiente) → 409 o 422 según el caso, con mensaje claro, nunca 500.
- Nunca expongas el stack trace o el mensaje crudo de una excepción interna en producción — configura el exception handler para mapear excepciones de dominio a respuestas JSON controladas.

## Autenticación

- **Sanctum** para SPA propia o apps móviles first-party consumiendo la misma API (es el default recomendado hoy en Laravel para la mayoría de casos).
- **Passport** solo si de verdad necesitas OAuth2 completo (terceros consumiendo tu API con su propio flujo de autorización) — no lo uses por defecto, es más complejo de lo que la mayoría de proyectos necesita.
- Protege rutas con `auth:sanctum` middleware; usa Policies (`php artisan make:policy`) para autorización a nivel de recurso, invocadas en el Form Request (`authorize()`) o vía `$this->authorize()` en el controlador.

## Paginación, filtrado y ordenamiento

- Listados siempre paginados (`->paginate(15)` o `simplePaginate` para listas muy grandes donde no necesitas el conteo total) — nunca devuelvas colecciones completas sin paginar en un endpoint `index`.
- Filtros vía query params explícitos y validados (no aceptes cualquier columna como filtro sin whitelist) — p. ej. `?status=pending&from=2024-01-01`.
- Ordenamiento vía `?sort=-created_at,name` con whitelist de columnas permitidas, para evitar que el cliente ordene por una columna sin índice y tumbe el performance.

## Rate limiting

- Define límites explícitos por tipo de ruta en `bootstrap/app.php` / `RouteServiceProvider` (Laravel 11+) o vía middleware `throttle:60,1` — no dejes las rutas de API sin límite.
- Límites más estrictos en endpoints sensibles (login, recuperación de contraseña, creación de recursos costosos).

## Documentación

- Si el proyecto ya usa Scribe o similar, mantén los bloques de anotación actualizados al tocar un endpoint.
- Si no hay documentación automática configurada y el usuario pide una API "seria" o de cara a terceros, sugiere instalar `knuckleswtf/scribe` (genera OpenAPI + docs desde el código) en vez de escribir un OpenAPI YAML a mano.
