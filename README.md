# HIS — Módulo de Control de Citas Médicas

Módulo de control de citas de un sistema hospitalario integrado (HIS): calendario
interactivo (FullCalendar) para crear, editar, reprogramar (drag & drop) y cancelar
citas médicas, respaldado por una API REST propia (Laravel) y una base de datos
MySQL que corre en Docker. Incluye login, roles y permisos, y CRUD de administración
para especialidades, estados de cita y usuarios.

## Stack

- Laravel 12 (PHP 8.4)
- MySQL 8 (contenedor Docker, con volumen persistente)
- Nginx (contenedor Docker)
- FullCalendar 6 (vía CDN) + Blade + fetch
- Laravel Breeze (login) + Laravel Sanctum (sesión stateful para la API)
- `spatie/laravel-permission` (roles y permisos)
- Todo corre en Docker: PHP-FPM, Nginx y MySQL. El `Dockerfile` es multi-stage:
  una etapa Node compila los assets de Tailwind/Vite y la etapa final de PHP
  los copia — no hace falta tener Node ni npm instalados en la máquina.

## Requisitos

- Docker y Docker Compose (Docker Desktop en macOS/Windows).
- Nada más. No se necesita PHP, Composer, Node ni MySQL instalados localmente.

## Cómo levantar el proyecto

1. Clonar el repositorio y entrar a la carpeta del proyecto.

2. Copiar el archivo de variables de entorno (si `.env` no existe todavía):

   ```bash
   cp .env.example .env
   ```

   Los valores por defecto ya están configurados para el entorno Docker
   (`DB_HOST=db`, `DB_DATABASE=his_citas`, etc.), no hace falta editar nada
   para levantar el proyecto localmente.

3. Levantar el stack completo con un solo comando:

   ```bash
   docker compose up -d --build
   ```

   Esto construye la imagen de la app (incluyendo el build de los assets de
   Breeze/Tailwind) y levanta tres contenedores:

   | Contenedor      | Servicio          | Puerto expuesto |
   |------------------|-------------------|-----------------|
   | `his_citas_app`  | PHP-FPM (Laravel) | — (interno)     |
   | `his_citas_web`  | Nginx             | `8080`          |
   | `his_citas_db`   | MySQL 8           | `3306`          |

4. Generar la clave de la aplicación (solo la primera vez, si `.env` no trae
   ya un `APP_KEY`):

   ```bash
   docker compose exec app php artisan key:generate
   ```

5. Ejecutar las migraciones y poblar la base de datos con los datos semilla:

   ```bash
   docker compose exec app php artisan migrate --seed
   ```

6. Abrir el sistema en el navegador:

   ```
   http://localhost:8080
   ```

   Redirige automáticamente a `/login`. Ver la sección de **usuarios de
   prueba** abajo para las credenciales.

### Reiniciar la base de datos a su estado inicial

Cuando se quiera volver a dejar la base de datos limpia con los datos
semilla (por ejemplo, después de probar el sistema):

```bash
docker compose exec app php artisan migrate:fresh --seed
```

> ⚠️ Este comando borra todas las tablas y las vuelve a crear. Úsalo solo
> en este entorno de desarrollo/evaluación — nunca contra una base de datos
> con información real.

### Detener el proyecto

```bash
docker compose down
```

Los datos de MySQL persisten entre reinicios gracias al volumen `db_data`
definido en `docker-compose.yml` (se pierden solo si se corre
`docker compose down -v`).

## Usuarios de prueba (creados por el seeder)

Todos los usuarios sembrados usan la misma contraseña: **`password`**.

| Rol | Email | Contraseña | Notas |
|---|---|---|---|
| Administrador | `admin@his.test` | `password` | Acceso total: Calendario + Especialidades + Estados de cita + Usuarios |
| Recepcionista | `recepcion@his.test` | `password` | Gestiona citas desde el calendario |
| Doctora (Medicina General) | `ana.morales@his.test` | `password` | Solo ve el Calendario |
| Doctor (Pediatría) | `carlos.perez@his.test` | `password` | Solo ve el Calendario |
| Doctora (Cardiología) | `lucia.ramirez@his.test` | `password` | Solo ve el Calendario |
| Doctor (Traumatología) | `miguel.sanchez@his.test` | `password` | Solo ve el Calendario |
| Doctora (Dermatología) | `paola.gomez@his.test` | `password` | Solo ve el Calendario |

El registro público de usuarios está deshabilitado a propósito: los usuarios
del sistema se crean desde el CRUD **Usuarios** (solo visible/accesible para
el rol `admin`), asignando su rol y, si es doctor, su especialidad.

## Roles y permisos

Implementados con `spatie/laravel-permission`.

| Rol | Puede |
|---|---|
| `admin` | Todo: calendario, y los CRUD de especialidades, estados de cita y usuarios |
| `recepcionista` | Ver y gestionar citas en el calendario (crear, editar, reprogramar, cambiar estado) |
| `doctor` | Ver el calendario |

Todas las rutas de la aplicación (`/calendario`, la API de citas, y los CRUD
de administración) requieren sesión iniciada. Los CRUD de administración
además requieren el rol `admin`; un usuario sin ese rol recibe `403
Forbidden`.

## Funcionalidades

- **Calendario interactivo** (`/calendario`): vistas de mes y semana, crear
  cita haciendo clic en un día, ver detalle al hacer clic en una cita,
  reprogramar arrastrando el evento (drag & drop), editar la cita completa
  (paciente, doctor, horario, motivo), y cambiar de estado con botones
  generados dinámicamente según los estados configurados.
- **Especialidades** (`/especialidades`, solo admin): CRUD de las
  especialidades médicas que se pueden asignar a un doctor.
- **Estados de cita** (`/estados-cita`, solo admin): CRUD de los estados
  posibles de una cita (nombre, color, si es un estado terminal, y si
  bloquea el horario del doctor para nuevas citas).
- **Usuarios** (`/usuarios`, solo admin): CRUD de usuarios del sistema, con
  asignación de rol y, si el rol es doctor, de especialidad.

## API REST

Todos los endpoints requieren sesión iniciada (`auth:sanctum`, stateful —
las peticiones `fetch()` del propio calendario se autentican por la cookie
de sesión).

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/api/citas` | Lista citas; filtros `doctor_id`, `paciente_id`, `desde`, `hasta` |
| POST | `/api/citas` | Crea una cita (409 si hay conflicto de horario con el mismo doctor) |
| GET | `/api/citas/{id}` | Detalle de una cita |
| PUT | `/api/citas/{id}` | Edita/reprograma una cita completa |
| PATCH | `/api/citas/{id}/estado` | Cambia el estado de una cita |
| GET | `/api/doctores` | Lista los usuarios con rol doctor |
| GET | `/api/pacientes` | Lista los pacientes registrados |
| GET | `/api/estados-cita` | Lista los estados de cita configurados (usado por el calendario para colores/leyenda) |

Ejemplo:

```bash
curl -s -w "\nHTTP %{http_code}\n" -X POST http://localhost:8080/api/citas \
  -H "Content-Type: application/json" \
  -d '{"paciente_id":1,"doctor_id":3,"fecha_inicio":"2026-09-25 09:00:00","fecha_fin":"2026-09-25 09:30:00","motivo":"Consulta"}'
```

## Estructura del código (por capas)

- **Controllers** (`app/Http/Controllers`, `app/Http/Controllers/Api`): capa
  HTTP, sin lógica de negocio.
- **Form Requests** (`app/Http/Requests`): validación de entrada.
- **Services** (`app/Services/CitaService.php`): lógica de negocio no
  trivial (validación de conflicto de horario, transición de estados) —
  las tres nuevas entidades de administración (especialidades, estados,
  usuarios) son CRUD simples de un solo modelo y no necesitan una capa de
  Service aparte.
- **Models** (`app/Models`): Eloquent, acceso a datos y relaciones.
- **Resources** (`app/Http/Resources`): forma de las respuestas JSON de la
  API.

## Tests

```bash
docker compose exec app php artisan test
```

Los tests corren contra SQLite en memoria (`phpunit.xml` fuerza
`DB_CONNECTION=sqlite` / `DB_DATABASE=:memory:`), nunca contra la base de
datos MySQL de Docker.

## Evidencia

Ver [`EVIDENCIA.md`](EVIDENCIA.md) (o `EVIDENCIA.pdf`) para capturas de
pantalla, comandos ejecutados, respuestas de la API y el historial de Git
(`git log --graph --all`) con las ramas, Pull Requests y merges del
proyecto.
