# Evidencia — Módulo de Control de Citas (HIS)

## 1. Levantar el entorno

```bash
docker compose up -d --build
```

```
 Container his_citas_db     Started (healthy)
 Container his_citas_app    Started
 Container his_citas_web    Started
```

### `docker ps`

```
NAMES           IMAGE                                      STATUS                   PORTS
his_citas_web   nginx:1.27-alpine                          Up                       0.0.0.0:8080->80/tcp
his_citas_app   segundo-parcial-analisis-sistemas-2-app    Up                       9000/tcp
his_citas_db    mysql:8.0                                  Up (healthy)             0.0.0.0:3306->3306/tcp
```

## 2. Migraciones y datos semilla

```bash
docker compose exec app php artisan migrate:fresh --seed
```

```
  2024_01_01_000000_create_doctores_table ....... DONE
  2024_01_01_000001_create_pacientes_table ...... DONE
  2024_01_01_000002_create_citas_table .......... DONE

  Database\Seeders\DoctorSeeder ................. DONE
  Database\Seeders\PacienteSeeder ............... DONE
  Database\Seeders\CitaSeeder ................... DONE
```

```bash
docker compose exec db mysql -uhis_user -phis_password his_citas \
  -e "SELECT COUNT(*) FROM doctores; SELECT COUNT(*) FROM pacientes; SELECT COUNT(*) FROM citas;"
```

```
doctores: 5
pacientes: 7
citas: 6
```

## 3. API REST (curl)

### Crear cita — 201

```bash
curl -s -w "\nHTTP %{http_code}\n" -X POST http://localhost:8080/api/citas \
  -H "Content-Type: application/json" \
  -d '{"paciente_id":1,"doctor_id":1,"fecha_inicio":"2026-09-25 09:00:00","fecha_fin":"2026-09-25 09:30:00","motivo":"Base"}'
```
```
HTTP 201
```

### Conflicto de horario con el mismo doctor — 409

```bash
curl -s -w "\nHTTP %{http_code}\n" -X POST http://localhost:8080/api/citas \
  -H "Content-Type: application/json" \
  -d '{"paciente_id":2,"doctor_id":1,"fecha_inicio":"2026-09-25 09:15:00","fecha_fin":"2026-09-25 09:45:00","motivo":"Conflicto"}'
```
```
{"message":"El doctor ya tiene una cita activa en ese horario."}
HTTP 409
```

### Cita inexistente — 404

```bash
curl -s -o /dev/null -w "HTTP %{http_code}\n" http://localhost:8080/api/citas/9999
```
```
HTTP 404
```

### Datos inválidos — 400

```bash
curl -s -w "\nHTTP %{http_code}\n" -X POST http://localhost:8080/api/citas \
  -H "Content-Type: application/json" -d '{"paciente_id":999,"doctor_id":2,"fecha_inicio":"","motivo":""}'
```
```
{"message":"The selected paciente id is invalid. (and 3 more errors)","errors":{...}}
HTTP 400
```

### Reprogramar (drag & drop) — 200

```bash
curl -s -w "\nHTTP %{http_code}\n" -X PUT http://localhost:8080/api/citas/7 \
  -H "Content-Type: application/json" \
  -d '{"fecha_inicio":"2026-09-26 10:00:00","fecha_fin":"2026-09-26 10:30:00"}'
```
```
HTTP 200
```

### Cambiar estado — 200 / transición inválida — 400

```bash
curl -s -w "\nHTTP %{http_code}\n" -X PATCH http://localhost:8080/api/citas/7/estado -d '{"estado":"confirmada"}'
# HTTP 200
curl -s -w "\nHTTP %{http_code}\n" -X PATCH http://localhost:8080/api/citas/7/estado -d '{"estado":"cancelada"}'
# HTTP 200
curl -s -w "\nHTTP %{http_code}\n" -X PATCH http://localhost:8080/api/citas/7/estado -d '{"estado":"confirmada"}'
# {"message":"No se puede cambiar la cita de estado 'cancelada' a 'confirmada'."}
# HTTP 400
```

### Listar / filtrar — 200

```bash
curl -s "http://localhost:8080/api/citas?doctor_id=1"
curl -s http://localhost:8080/api/doctores
curl -s http://localhost:8080/api/pacientes
```

## 4. Interfaz FullCalendar

Verificado manualmente en `http://localhost:8080/calendario`:

- Vista mensual y semanal (`month` / `week`) con eventos coloreados por
  estado (amarillo=pendiente, azul=confirmada, gris=cancelada,
  verde=atendida) y leyenda en el encabezado.
- Clic en un día vacío abre el modal "Nueva cita" con selects de
  paciente/doctor poblados desde `/api/doctores` y `/api/pacientes`;
  al guardar hace `POST /api/citas` y refresca el calendario.
- Clic en un evento abre el modal de detalle (paciente, doctor,
  horario, motivo, estado) con acciones para confirmar/atender/
  cancelar, ocultando las transiciones no permitidas.
- Arrastrar un evento a otro día dispara `PUT /api/citas/{id}`; se
  comprobó que el cambio persiste en MySQL (`GET /api/citas/{id}`
  refleja la nueva fecha) y que, si el servidor responde 409, el
  evento vuelve a su posición original.
- Filtro por doctor sobre el propio listado de la API.
- Probado en resolución de escritorio (1280px) y tablet (768px),
  manteniéndose usable en ambas.

## 5. Historial Git — ramas, PRs y merges

```bash
git log --oneline --graph --all
```

```
* f15f772 feat(ui): integra FullCalendar interactivo consumiendo la API de citas
*   23ab761 Merge pull request #3 from Josuemart555/feature/validacion-conflictos-estados
|\
| * 5ed5de3 feat(citas): valida conflictos de horario y controla transición de estados
|/
*   23c5d54 Merge pull request #2 from Josuemart555/feature/api-rest-citas
|\
| * a116a67 feat(api): agrega API REST de citas, doctores y pacientes
|/
*   608ab4a Merge pull request #1 from Josuemart555/feature/docker-mysql-schema
|\
| * 5536bde feat(db): agrega esquema de citas médicas y datos semilla
| * f64593e feat(docker): agrega stack Docker con nginx, PHP-FPM 8.4 y MySQL 8
|/
* b3bc92a chore: scaffold base Laravel 11 project
```

Pull Requests (repositorio `Josuemart555/segundo-parcial-analisis-sistemas-2`):

| # | Rama | Alcance | RQF/RQNF |
|---|------|---------|----------|
| [#1](https://github.com/Josuemart555/segundo-parcial-analisis-sistemas-2/pull/1) | `feature/docker-mysql-schema` | Docker Compose + esquema MySQL + seeders | RQNF-01, RQNF-02 |
| [#2](https://github.com/Josuemart555/segundo-parcial-analisis-sistemas-2/pull/2) | `feature/api-rest-citas` | API REST de citas/doctores/pacientes | RQF-01, RQF-06, RQF-07, RQF-08, RQNF-03 |
| [#3](https://github.com/Josuemart555/segundo-parcial-analisis-sistemas-2/pull/3) | `feature/validacion-conflictos-estados` | Conflictos de horario + estados | RQF-03, RQF-05, RQNF-03, RQNF-07 |
| [#4](https://github.com/Josuemart555/segundo-parcial-analisis-sistemas-2/pull/4) | `feature/fullcalendar-ui` | FullCalendar interactivo | RQF-02, RQF-04, RQF-09, RQF-10, RQNF-06 |

Las 4 ramas fueron fusionadas a `main` mediante Pull Request (merge commit),
quedando el historial completo visible con `git log --graph --all`.
