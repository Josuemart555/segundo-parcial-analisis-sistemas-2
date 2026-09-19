(function () {
    'use strict';

    const API_BASE = '/api';

    const ESTADO_COLOR = {
        pendiente: '#f59e0b',
        confirmada: '#2563eb',
        cancelada: '#6b7280',
        atendida: '#16a34a',
    };

    const ESTADO_LABEL = {
        pendiente: 'Pendiente',
        confirmada: 'Confirmada',
        cancelada: 'Cancelada',
        atendida: 'Atendida',
    };

    const modalCrear = document.getElementById('modal-crear');
    const modalDetalle = document.getElementById('modal-detalle');
    const formCrear = document.getElementById('form-crear-cita');
    const errorCrear = document.getElementById('error-crear');
    const errorDetalle = document.getElementById('error-detalle');
    const filtroDoctor = document.getElementById('filtro-doctor');

    let citaSeleccionadaId = null;
    let calendar;

    function abrirModal(modal) {
        modal.hidden = false;
    }

    function cerrarModal(modal) {
        modal.hidden = true;
    }

    document.querySelectorAll('[data-cerrar-modal]').forEach((btn) => {
        btn.addEventListener('click', () => {
            cerrarModal(document.getElementById(btn.dataset.cerrarModal));
        });
    });

    async function peticionJSON(url, opciones = {}) {
        const respuesta = await fetch(url, {
            headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
            ...opciones,
        });
        const cuerpo = await respuesta.json().catch(() => ({}));
        return { ok: respuesta.ok, status: respuesta.status, body: cuerpo };
    }

    function llenarSelect(select, items, valorFn, textoFn, incluirVacio) {
        select.innerHTML = '';
        if (incluirVacio) {
            const opt = document.createElement('option');
            opt.value = '';
            opt.textContent = 'Todos';
            select.appendChild(opt);
        }
        items.forEach((item) => {
            const opt = document.createElement('option');
            opt.value = valorFn(item);
            opt.textContent = textoFn(item);
            select.appendChild(opt);
        });
    }

    async function cargarDoctoresYPacientes() {
        const [doctoresRes, pacientesRes] = await Promise.all([
            peticionJSON(`${API_BASE}/doctores`),
            peticionJSON(`${API_BASE}/pacientes`),
        ]);

        const doctores = doctoresRes.body.data || [];
        const pacientes = pacientesRes.body.data || [];

        llenarSelect(filtroDoctor, doctores, (d) => d.id, (d) => d.nombre, true);
        llenarSelect(formCrear.doctor_id, doctores, (d) => d.id, (d) => `${d.nombre} (${d.especialidad})`, false);
        llenarSelect(formCrear.paciente_id, pacientes, (p) => p.id, (p) => `${p.nombre} - ${p.documento}`, false);
    }

    function formatearFechaHora(iso) {
        const fecha = new Date(iso);
        return fecha.toLocaleString('es-GT', { dateStyle: 'medium', timeStyle: 'short' });
    }

    function abrirDetalle(cita) {
        citaSeleccionadaId = cita.id;
        document.getElementById('detalle-paciente').textContent = cita.paciente?.nombre ?? '—';
        document.getElementById('detalle-doctor').textContent = cita.doctor?.nombre ?? '—';
        document.getElementById('detalle-horario').textContent =
            `${formatearFechaHora(cita.fecha_inicio)} — ${formatearFechaHora(cita.fecha_fin)}`;
        document.getElementById('detalle-motivo').textContent = cita.motivo;
        document.getElementById('detalle-estado').textContent = ESTADO_LABEL[cita.estado] ?? cita.estado;
        errorDetalle.textContent = '';

        const esTerminal = cita.estado === 'cancelada' || cita.estado === 'atendida';
        document.querySelectorAll('#modal-detalle [data-estado]').forEach((btn) => {
            btn.hidden = esTerminal || btn.dataset.estado === cita.estado;
        });

        abrirModal(modalDetalle);
    }

    document.querySelectorAll('#modal-detalle [data-estado]').forEach((btn) => {
        btn.addEventListener('click', async () => {
            errorDetalle.textContent = '';
            const { ok, status, body } = await peticionJSON(
                `${API_BASE}/citas/${citaSeleccionadaId}/estado`,
                { method: 'PATCH', body: JSON.stringify({ estado: btn.dataset.estado }) }
            );

            if (!ok) {
                errorDetalle.textContent = body.message || `Error ${status} al cambiar el estado.`;
                return;
            }

            cerrarModal(modalDetalle);
            calendar.refetchEvents();
        });
    });

    function toDatetimeLocal(date) {
        const pad = (n) => String(n).padStart(2, '0');
        return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
    }

    document.addEventListener('DOMContentLoaded', async () => {
        await cargarDoctoresYPacientes();

        const calendarEl = document.getElementById('calendario');
        calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'es',
            height: 'auto',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek',
            },
            initialView: 'dayGridMonth',
            editable: true,
            selectable: true,

            events: async (fetchInfo, successCallback, failureCallback) => {
                const params = new URLSearchParams({
                    desde: fetchInfo.startStr,
                    hasta: fetchInfo.endStr,
                });
                if (filtroDoctor.value) {
                    params.set('doctor_id', filtroDoctor.value);
                }

                const { ok, body } = await peticionJSON(`${API_BASE}/citas?${params.toString()}`);
                if (!ok) {
                    failureCallback(new Error('No se pudieron cargar las citas.'));
                    return;
                }

                const eventos = (body.data || []).map((cita) => ({
                    id: String(cita.id),
                    title: `${cita.paciente?.nombre ?? 'Paciente'} · ${cita.doctor?.nombre ?? 'Doctor'}`,
                    start: cita.fecha_inicio,
                    end: cita.fecha_fin,
                    color: ESTADO_COLOR[cita.estado] ?? '#94a3b8',
                    extendedProps: { cita },
                }));

                successCallback(eventos);
            },

            dateClick: (info) => {
                errorCrear.textContent = '';
                formCrear.reset();
                const inicio = info.date;
                const fin = new Date(inicio.getTime() + 30 * 60000);
                formCrear.fecha_inicio.value = toDatetimeLocal(inicio);
                formCrear.fecha_fin.value = toDatetimeLocal(fin);
                abrirModal(modalCrear);
            },

            eventClick: (info) => {
                abrirDetalle(info.event.extendedProps.cita);
            },

            eventDrop: async (info) => {
                const cita = info.event.extendedProps.cita;
                const { ok, body } = await peticionJSON(`${API_BASE}/citas/${cita.id}`, {
                    method: 'PUT',
                    body: JSON.stringify({
                        fecha_inicio: info.event.startStr,
                        fecha_fin: info.event.endStr || info.event.startStr,
                    }),
                });

                if (!ok) {
                    alert(body.message || 'No se pudo reprogramar la cita (posible conflicto de horario).');
                    info.revert();
                    return;
                }

                calendar.refetchEvents();
            },

            eventResize: async (info) => {
                const cita = info.event.extendedProps.cita;
                const { ok, body } = await peticionJSON(`${API_BASE}/citas/${cita.id}`, {
                    method: 'PUT',
                    body: JSON.stringify({
                        fecha_inicio: info.event.startStr,
                        fecha_fin: info.event.endStr,
                    }),
                });

                if (!ok) {
                    alert(body.message || 'No se pudo reprogramar la cita (posible conflicto de horario).');
                    info.revert();
                    return;
                }

                calendar.refetchEvents();
            },
        });

        calendar.render();

        filtroDoctor.addEventListener('change', () => calendar.refetchEvents());

        formCrear.addEventListener('submit', async (event) => {
            event.preventDefault();
            errorCrear.textContent = '';

            const datos = Object.fromEntries(new FormData(formCrear).entries());

            const { ok, body } = await peticionJSON(`${API_BASE}/citas`, {
                method: 'POST',
                body: JSON.stringify(datos),
            });

            if (!ok) {
                errorCrear.textContent = body.message || 'No se pudo crear la cita.';
                return;
            }

            cerrarModal(modalCrear);
            calendar.refetchEvents();
        });
    });
})();
