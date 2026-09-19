# Docker para Laravel

## docker-compose de desarrollo

Estructura mínima esperada (ajusta nombres de servicio al proyecto, pero mantén esta separación de responsabilidades):

```yaml
services:
  app:            # php-fpm, corre el código Laravel
    build:
      context: .
      dockerfile: docker/php/Dockerfile
      target: development
    volumes:
      - .:/var/www/html
    depends_on:
      - mysql
    environment:
      - DB_HOST=mysql

  nginx:
    image: nginx:stable-alpine
    ports:
      - "8000:80"
    volumes:
      - .:/var/www/html
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
    depends_on:
      - app

  mysql:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: ${DB_DATABASE}
      MYSQL_USER: ${DB_USERNAME}
      MYSQL_PASSWORD: ${DB_PASSWORD}
      MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASSWORD}
    ports:
      - "3306:3306"
    volumes:
      - mysql_data:/var/lib/mysql

  redis:           # opcional: solo si el proyecto usa colas/cache con Redis
    image: redis:alpine

volumes:
  mysql_data:
```

Puntos clave, no solo copiar el YAML:

- El volumen `mysql_data` es obligatorio — sin volumen nombrado, los datos se pierden en cada `docker compose down`.
- `app` y `nginx` comparten el mismo volumen de código para que nginx sirva los assets estáticos y php-fpm ejecute el PHP.
- Variables sensibles (contraseñas, claves) siempre vía `${VAR}` desde `.env`, nunca hardcodeadas en el `docker-compose.yml`.
- Redis solo si el proyecto realmente lo usa (colas, cache, broadcasting) — no lo agregues por defecto a un proyecto que no lo necesita.

## Dockerfile multi-stage para PHP

```dockerfile
FROM php:8.3-fpm-alpine AS base
RUN apk add --no-cache libpng libzip-dev oniguruma-dev \
    && docker-php-ext-install pdo_mysql mbstring zip bcmath
WORKDIR /var/www/html

FROM base AS development
RUN apk add --no-cache $PHPIZE_DEPS && pecl install xdebug && docker-php-ext-enable xdebug
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY . .
RUN composer install

FROM base AS production
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY . .
RUN composer install --no-dev --optimize-autoloader \
    && php artisan config:cache && php artisan route:cache
USER www-data
```

Ideas clave a preservar aunque ajustes versiones/extensiones:

- Un stage `base` compartido con las extensiones PHP comunes, para no duplicar `apk add` en cada stage.
- `development` incluye Xdebug y `composer install` completo (con dev dependencies); `production` no — Xdebug en producción es un riesgo de performance y seguridad.
- `production` corre `composer install --no-dev --optimize-autoloader` y cachea config/rutas — nunca dejes `composer install` sin `--no-dev` en la imagen de producción.
- Corre como usuario no-root (`www-data`) en producción.

## Variables de entorno

- `.env` nunca se commitea; siempre debe existir `.env.example` actualizado con todas las variables que el proyecto necesita (sin valores reales).
- Variables de Docker Compose (`DB_ROOT_PASSWORD`, etc.) y variables de Laravel (`.env` dentro del contenedor) son cosas distintas — no asumas que una sola fuente basta; documenta cuáles van en cuál.
- Separa claramente dev/prod: un `docker-compose.yml` base + `docker-compose.override.yml` (dev, se aplica automático) + `docker-compose.prod.yml` (se invoca explícito con `-f`), en vez de un único archivo con condicionales manuales.

## Buenas prácticas generales

- `.dockerignore` con al menos `node_modules`, `vendor`, `.git`, `storage/logs` — evita builds lentos y contextos gigantes.
- Healthcheck en el servicio `mysql` (`mysqladmin ping`) si otros servicios dependen de que MySQL ya esté listo, no solo "iniciado" (`depends_on` sin condición no espera a que el servicio esté realmente disponible).
- Para levantar el entorno completo: `docker compose up -d --build` seguido de `docker compose exec app php artisan migrate --seed` — no asumas que las migraciones corren solas al levantar el contenedor a menos que lo hayas configurado explícitamente (p. ej. en el entrypoint).
