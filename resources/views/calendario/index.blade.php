<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Citas - HIS</title>

    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/calendario.css') }}">
</head>
<body>
    <header class="his-header">
        <h1>HIS &mdash; Control de Citas Médicas</h1>
        <div class="his-leyenda">
            <span class="his-badge his-badge--pendiente">Pendiente</span>
            <span class="his-badge his-badge--confirmada">Confirmada</span>
            <span class="his-badge his-badge--cancelada">Cancelada</span>
            <span class="his-badge his-badge--atendida">Atendida</span>
        </div>
    </header>

    <main class="his-main">
        <section class="his-filtros">
            <label>
                Filtrar por doctor
                <select id="filtro-doctor">
                    <option value="">Todos</option>
                </select>
            </label>
        </section>

        <div id="calendario"></div>
    </main>

    <!-- Modal: crear cita -->
    <div id="modal-crear" class="his-modal" hidden>
        <div class="his-modal__contenido">
            <h2>Nueva cita</h2>
            <form id="form-crear-cita">
                <label>Paciente
                    <select name="paciente_id" required></select>
                </label>
                <label>Doctor
                    <select name="doctor_id" required></select>
                </label>
                <label>Fecha y hora inicio
                    <input type="datetime-local" name="fecha_inicio" required>
                </label>
                <label>Fecha y hora fin
                    <input type="datetime-local" name="fecha_fin" required>
                </label>
                <label>Motivo
                    <input type="text" name="motivo" maxlength="255" required>
                </label>
                <p class="his-modal__error" id="error-crear"></p>
                <div class="his-modal__acciones">
                    <button type="button" class="his-btn his-btn--secundario" data-cerrar-modal="modal-crear">Cancelar</button>
                    <button type="submit" class="his-btn his-btn--primario">Guardar cita</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: detalle / cambiar estado -->
    <div id="modal-detalle" class="his-modal" hidden>
        <div class="his-modal__contenido">
            <h2>Detalle de la cita</h2>
            <dl class="his-detalle">
                <dt>Paciente</dt><dd id="detalle-paciente"></dd>
                <dt>Doctor</dt><dd id="detalle-doctor"></dd>
                <dt>Horario</dt><dd id="detalle-horario"></dd>
                <dt>Motivo</dt><dd id="detalle-motivo"></dd>
                <dt>Estado</dt><dd id="detalle-estado"></dd>
            </dl>
            <p class="his-modal__error" id="error-detalle"></p>
            <div class="his-modal__acciones his-modal__acciones--estado">
                <button type="button" class="his-btn his-btn--confirmar" data-estado="confirmada">Confirmar</button>
                <button type="button" class="his-btn his-btn--atender" data-estado="atendida">Marcar atendida</button>
                <button type="button" class="his-btn his-btn--cancelar" data-estado="cancelada">Cancelar cita</button>
            </div>
            <div class="his-modal__acciones">
                <button type="button" class="his-btn his-btn--primario" id="btn-abrir-editar">Editar</button>
                <button type="button" class="his-btn his-btn--secundario" data-cerrar-modal="modal-detalle">Cerrar</button>
            </div>
        </div>
    </div>

    <!-- Modal: editar cita -->
    <div id="modal-editar" class="his-modal" hidden>
        <div class="his-modal__contenido">
            <h2>Editar cita</h2>
            <form id="form-editar-cita">
                <label>Paciente
                    <select name="paciente_id" required></select>
                </label>
                <label>Doctor
                    <select name="doctor_id" required></select>
                </label>
                <label>Fecha y hora inicio
                    <input type="datetime-local" name="fecha_inicio" required>
                </label>
                <label>Fecha y hora fin
                    <input type="datetime-local" name="fecha_fin" required>
                </label>
                <label>Motivo
                    <input type="text" name="motivo" maxlength="255" required>
                </label>
                <p class="his-modal__error" id="error-editar"></p>
                <div class="his-modal__acciones">
                    <button type="button" class="his-btn his-btn--secundario" data-cerrar-modal="modal-editar">Cancelar</button>
                    <button type="submit" class="his-btn his-btn--primario">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('js/calendario.js') }}"></script>
</body>
</html>
