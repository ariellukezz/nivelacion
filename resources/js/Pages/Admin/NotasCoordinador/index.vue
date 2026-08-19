<template>
    <Head title="Registro / Edición de Notas" />

    <AuthenticatedLayout>
        <Toast />

        <div class="p-4">
            <!-- ENCABEZADO -->
            <div class="flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div>
                    <h2 class="m-0">Registro / Edición de Notas</h2>
                    <small class="text-600">
                        Módulo para Director / Coordinador de Escuela
                    </small>
                </div>

                <Button
                    label="Actualizar"
                    icon="pi pi-refresh"
                    severity="secondary"
                    outlined
                    :loading="cargandoCompetencias"
                    @click="recargarTodo"
                />
            </div>

            <Message severity="info" :closable="false" class="mb-4">
                Seleccione una competencia y luego un curso. La nueva nota se registra
                directamente desde la tabla de estudiantes.
            </Message>

            <!-- PASO 1: COMPETENCIA -->
            <div class="card mb-4">
                <div class="grid">
                    <div class="col-12 md:col-6">
                        <label class="font-semibold block mb-2">
                            Competencia
                        </label>

                        <Dropdown
                            v-model="competenciaSeleccionada"
                            :options="competencias"
                            optionLabel="label"
                            optionValue="value"
                            filter
                            showClear
                            :loading="cargandoCompetencias"
                            placeholder="Seleccione una competencia"
                            class="w-full"
                        >
                            <template #option="slotProps">
                                <div style="max-width:600px; white-space:normal;">
                                    {{ slotProps.option.label }}
                                </div>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </div>

            <!-- PASO 2: CURSOS -->
            <div v-if="competenciaSeleccionada !== null" class="card mb-4">
                <div class="flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                    <div>
                        <h3 class="m-0">Cursos de la competencia</h3>
                        <small class="text-600">
                            Seleccione el curso que desea gestionar.
                        </small>
                    </div>

                    <span class="p-input-icon-left">
                        <i class="pi pi-search" />
                        <InputText
                            v-model="buscarCurso"
                            placeholder="Buscar curso, programa o docente"
                            style="min-width:280px"
                        />
                    </span>
                </div>

                <div v-if="cargandoCursos" class="flex justify-content-center py-5">
                    <ProgressSpinner
                        style="width:45px; height:45px"
                        strokeWidth="4"
                    />
                </div>

                <DataTable
                    v-else
                    :value="cursosFiltrados"
                    dataKey="id"
                    paginator
                    :rows="8"
                    :rowsPerPageOptions="[8, 15, 30]"
                    responsiveLayout="scroll"
                    class="p-datatable-sm"
                    stripedRows
                    tableStyle="min-width:60rem"
                >
                    <Column
                        field="nombre"
                        header="Nombre del Curso"
                        style="min-width:220px"
                    />

                    <Column
                        field="programa"
                        header="Programa"
                        style="min-width:260px"
                    />

                    <Column
                        field="grupo"
                        header="Grupo"
                        style="width:90px"
                    />

                    <Column
                        field="docente"
                        header="Docente asignado"
                        style="min-width:240px"
                    >
                        <template #body="{ data }">
                            <span v-if="data.docente">
                                {{ data.docente }}
                            </span>

                            <Tag
                                v-else
                                severity="warning"
                                value="Sin docente"
                            />
                        </template>
                    </Column>

                    <Column
                        header="Acciones"
                        style="width:170px; text-align:center"
                    >
                        <template #body="{ data }">
                            <Button
                                label="Gestionar notas"
                                icon="pi pi-list"
                                size="small"
                                @click.stop="seleccionarCurso(data)"
                            />
                        </template>
                    </Column>

                    <template #empty>
                        <div class="text-center py-4 text-600">
                            No se encontraron cursos para esta competencia.
                        </div>
                    </template>
                </DataTable>
            </div>

            <!-- PASO 3: ALUMNOS -->
            <div v-if="cursoSeleccionado" class="card">
                <div class="flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                    <div>
                        <div class="flex align-items-center gap-2 mb-1">
                            <Button
                                icon="pi pi-arrow-left"
                                severity="secondary"
                                text
                                rounded
                                @click="volverCursos"
                            />

                            <h3 class="m-0">
                                Estudiantes del curso
                            </h3>
                        </div>

                        <div class="text-600">
                            <strong>{{ cursoSeleccionado.nombre }}</strong>

                            <span v-if="cursoSeleccionado.programa">
                                · {{ cursoSeleccionado.programa }}
                            </span>

                            <span v-if="cursoSeleccionado.grupo">
                                · Grupo {{ cursoSeleccionado.grupo }}
                            </span>
                        </div>

                        <div
                            v-if="cursoSeleccionado.docente"
                            class="text-600 mt-1"
                        >
                            Docente:
                            <strong>{{ cursoSeleccionado.docente }}</strong>
                        </div>
                    </div>

                    <div class="flex align-items-center gap-2">
                        <span class="p-input-icon-left">
                            <i class="pi pi-search" />
                            <InputText
                                v-model="buscarAlumno"
                                placeholder="Buscar estudiante"
                                style="min-width:260px"
                            />
                        </span>

                        <Button
                            icon="pi pi-refresh"
                            severity="secondary"
                            outlined
                            :loading="cargandoAlumnos"
                            @click="getAlumnos"
                        />
                    </div>
                </div>

                <Message severity="warn" :closable="false" class="mb-3">
                    Para modificar una nota, escriba la nueva nota en la fila del
                    estudiante y presione <strong>Guardar</strong>.
                </Message>

                <div v-if="cargandoAlumnos" class="flex justify-content-center py-5">
                    <ProgressSpinner
                        style="width:45px; height:45px"
                        strokeWidth="4"
                    />
                </div>

                <DataTable
                    v-else
                    :value="alumnosFiltrados"
                    dataKey="id_detalle"
                    paginator
                    :rows="10"
                    :rowsPerPageOptions="[10, 20, 50]"
                    responsiveLayout="scroll"
                    class="p-datatable-sm"
                    stripedRows
                    tableStyle="min-width:95rem"
                >
                    <Column
                        field="codigo_est"
                        header="Código"
                        style="min-width:120px"
                    />

                    <Column
                        header="Estudiante"
                        style="min-width:260px"
                    >
                        <template #body="{ data }">
                            {{ nombreCompleto(data) }}
                        </template>
                    </Column>

                    <Column
                        field="programa"
                        header="Programa"
                        style="min-width:250px"
                    />

                    <Column
                        header="Nota Matriz"
                        style="width:120px; text-align:center"
                    >
                        <template #body="{ data }">
                            <Tag
                                v-if="tieneNota(data.nota_matriz)"
                                :value="formatearNota(data.nota_matriz)"
                                :severity="severidadNota(data.nota_matriz)"
                            />

                            <span v-else class="text-500">
                                -
                            </span>
                        </template>
                    </Column>

                    <Column
                        header="Nota Actual"
                        style="width:120px; text-align:center"
                    >
                        <template #body="{ data }">
                            <Tag
                                v-if="tieneNota(data.nota_actual)"
                                :value="formatearNota(data.nota_actual)"
                                :severity="severidadNota(data.nota_actual)"
                            />

                            <span v-else class="text-500">
                                Sin nota
                            </span>
                        </template>
                    </Column>

                    <!-- NUEVA NOTA DIRECTAMENTE EN LA TABLA -->
                    <Column
                        header="Nueva Nota"
                        style="min-width:230px"
                    >
                        <template #body="{ data }">
                            <div class="flex align-items-center gap-2">
                                <InputNumber
                                    v-model="data.nota_nueva"
                                    :min="0"
                                    :max="20"
                                    :minFractionDigits="2"
                                    :maxFractionDigits="2"
                                    locale="es-PE"
                                    inputStyle="width:95px"
                                    placeholder="0.00"
                                />

                                <Button
                                    label="Guardar"
                                    icon="pi pi-save"
                                    size="small"
                                    :loading="guardandoId === data.id_detalle"
                                    :disabled="guardandoId !== null && guardandoId !== data.id_detalle"
                                    @click.stop="guardarNotaFila(data)"
                                />
                            </div>
                        </template>
                    </Column>

                    <!-- YA NO SE PIDE OBSERVACIÓN AL USUARIO -->
                    <Column
                        header="Modificado por"
                        style="min-width:230px"
                    >
                        <template #body="{ data }">
                            <span v-if="data.edicion_nota" class="font-medium">
                                {{ data.edicion_nota }}
                            </span>

                            <span v-else class="text-500">
                                -
                            </span>
                        </template>
                    </Column>

                    <Column
                        header="Condición"
                        style="width:130px; text-align:center"
                    >
                        <template #body="{ data }">
                            <Tag
                                v-if="tieneNota(data.nota_actual)"
                                :severity="
                                    Number(data.nota_actual) >= 10.5
                                        ? 'success'
                                        : 'danger'
                                "
                                :value="
                                    Number(data.nota_actual) >= 10.5
                                        ? 'Aprobado'
                                        : 'Desaprobado'
                                "
                            />

                            <Tag
                                v-else
                                severity="secondary"
                                value="Pendiente"
                            />
                        </template>
                    </Column>

                    <template #empty>
                        <div class="text-center py-4 text-600">
                            No hay estudiantes registrados en este curso.
                        </div>
                    </template>
                </DataTable>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import {
    ref,
    computed,
    watch,
    onMounted
} from 'vue';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

import Button from 'primevue/button';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import Dropdown from 'primevue/dropdown';
import InputNumber from 'primevue/inputnumber';
import InputText from 'primevue/inputtext';
import Message from 'primevue/message';
import ProgressSpinner from 'primevue/progressspinner';
import Tag from 'primevue/tag';
import Toast from 'primevue/toast';

import { useToast } from 'primevue/usetoast';

/* =========================================================
   CONFIGURACIÓN
========================================================= */

const toast = useToast();

const baseUrl = '/coordinador/notas';

const textoEdicion = 'Modificado por Director/Coordinador';

/* =========================================================
   ESTADOS
========================================================= */

const competencias = ref([]);
const competenciaSeleccionada = ref(null);

const cursos = ref([]);
const cursoSeleccionado = ref(null);

const alumnos = ref([]);

const buscarCurso = ref('');
const buscarAlumno = ref('');

const cargandoCompetencias = ref(false);
const cargandoCursos = ref(false);
const cargandoAlumnos = ref(false);

const guardandoId = ref(null);

/* =========================================================
   AYUDANTES
========================================================= */

const obtenerLista = (respuesta) => {
    if (!respuesta) {
        return [];
    }

    if (Array.isArray(respuesta?.datos)) {
        return respuesta.datos;
    }

    if (Array.isArray(respuesta?.datos?.data)) {
        return respuesta.datos.data;
    }

    if (Array.isArray(respuesta)) {
        return respuesta;
    }

    return [];
};

const nombreCompleto = (alumno) => {
    return [
        alumno?.paterno,
        alumno?.materno,
        alumno?.nombres
    ]
        .filter(Boolean)
        .join(' ');
};

const tieneNota = (nota) => {
    return nota !== null &&
        nota !== undefined &&
        nota !== '' &&
        !Number.isNaN(Number(nota));
};

const formatearNota = (nota) => {
    if (!tieneNota(nota)) {
        return '-';
    }

    return Number(nota).toFixed(2);
};

const severidadNota = (nota) => {
    if (!tieneNota(nota)) {
        return 'secondary';
    }

    return Number(nota) >= 10.5
        ? 'success'
        : 'danger';
};

const mostrarError = (
    error,
    mensaje = 'Ocurrió un error al procesar la solicitud.'
) => {
    console.error(error);

    const detalle =
        error?.response?.data?.mensaje ??
        error?.response?.data?.message ??
        mensaje;

    toast.add({
        severity: 'error',
        summary: 'Error',
        detail: detalle,
        life: 4000
    });
};

/* =========================================================
   FILTROS
========================================================= */

const cursosFiltrados = computed(() => {
    const term = buscarCurso.value
        .trim()
        .toLowerCase();

    if (!term) {
        return cursos.value;
    }

    return cursos.value.filter((item) => {
        return [
            item?.nombre,
            item?.programa,
            item?.grupo,
            item?.docente,
            item?.competencia
        ]
            .filter(Boolean)
            .some((valor) =>
                String(valor)
                    .toLowerCase()
                    .includes(term)
            );
    });
});

const alumnosFiltrados = computed(() => {
    const term = buscarAlumno.value
        .trim()
        .toLowerCase();

    if (!term) {
        return alumnos.value;
    }

    return alumnos.value.filter((item) => {
        return [
            item?.codigo_est,
            item?.nombres,
            item?.paterno,
            item?.materno,
            item?.programa
        ]
            .filter(Boolean)
            .some((valor) =>
                String(valor)
                    .toLowerCase()
                    .includes(term)
            );
    });
});

/* =========================================================
   COMPETENCIAS
========================================================= */

const getCompetencias = async () => {
    cargandoCompetencias.value = true;

    try {
        const res = await axios.post(
            `${baseUrl}/get-competencias`,
            {
                term: ''
            }
        );

        competencias.value =
            obtenerLista(res.data);

    } catch (error) {
        competencias.value = [];

        mostrarError(
            error,
            'No se pudieron cargar las competencias.'
        );
    } finally {
        cargandoCompetencias.value = false;
    }
};

/* =========================================================
   CURSOS
========================================================= */

const getCursos = async () => {
    cursos.value = [];
    cursoSeleccionado.value = null;
    alumnos.value = [];

    if (!competenciaSeleccionada.value) {
        return;
    }

    cargandoCursos.value = true;

    try {
        const res = await axios.post(
            `${baseUrl}/get-cursos`,
            {
                competencia:
                    competenciaSeleccionada.value
            }
        );

        cursos.value =
            obtenerLista(res.data);

    } catch (error) {
        cursos.value = [];

        mostrarError(
            error,
            'No se pudieron cargar los cursos.'
        );
    } finally {
        cargandoCursos.value = false;
    }
};

/* =========================================================
   ALUMNOS
========================================================= */

const seleccionarCurso = async (curso) => {
    cursoSeleccionado.value = curso;
    buscarAlumno.value = '';

    await getAlumnos();
};

const getAlumnos = async () => {
    if (!cursoSeleccionado.value?.id) {
        alumnos.value = [];
        return;
    }

    cargandoAlumnos.value = true;

    try {
        const res = await axios.post(
            `${baseUrl}/get-alumnos`,
            {
                curso:
                    cursoSeleccionado.value.id
            }
        );

        /*
         * Agregamos nota_nueva al objeto de cada alumno.
         * Por defecto queda vacía para evitar guardar
         * accidentalmente la misma nota.
         */
        alumnos.value = obtenerLista(res.data).map(
            (item) => ({
                ...item,
                nota_nueva: null
            })
        );

        if (
            res.data?.curso &&
            typeof res.data.curso === 'object'
        ) {
            cursoSeleccionado.value = {
                ...cursoSeleccionado.value,
                ...res.data.curso
            };
        }

    } catch (error) {
        alumnos.value = [];

        mostrarError(
            error,
            'No se pudieron cargar los estudiantes.'
        );
    } finally {
        cargandoAlumnos.value = false;
    }
};

/* =========================================================
   GUARDAR NOTA DIRECTAMENTE DESDE LA FILA
========================================================= */

const guardarNotaFila = async (alumno) => {
    if (
        alumno.nota_nueva === null ||
        alumno.nota_nueva === undefined ||
        alumno.nota_nueva === ''
    ) {
        toast.add({
            severity: 'warn',
            summary: 'Nota requerida',
            detail: 'Ingrese la nueva nota antes de guardar.',
            life: 3000
        });

        return;
    }

    const nota = Number(alumno.nota_nueva);

    if (
        Number.isNaN(nota) ||
        nota < 0 ||
        nota > 20
    ) {
        toast.add({
            severity: 'warn',
            summary: 'Nota inválida',
            detail: 'La nota debe estar entre 0 y 20.',
            life: 3000
        });

        return;
    }

    guardandoId.value = alumno.id_detalle;

    try {
        /*
         * Seguimos enviando "observacion" para ser compatibles
         * con el controlador que ya tienes funcionando.
         *
         * Ya no se pide al Director que escriba nada.
         */
        const res = await axios.post(
            `${baseUrl}/update-nota`,
            {
                id_detalle:
                    alumno.id_detalle,

                nota:
                    nota,

                observacion:
                    textoEdicion
            }
        );

        toast.add({
            severity:
                res.data?.tipo ?? 'success',
            summary:
                res.data?.titulo ??
                'Nota actualizada',
            detail:
                res.data?.mensaje ??
                'La nota fue actualizada correctamente.',
            life: 3000
        });

        /*
         * Actualizamos inmediatamente la fila.
         */
        alumno.nota_actual = nota;
        alumno.edicion_nota = textoEdicion;
        alumno.condicion = nota >= 10.5 ? 1 : 0;
        alumno.nota_nueva = null;

    } catch (error) {
        mostrarError(
            error,
            'No se pudo actualizar la nota.'
        );
    } finally {
        guardandoId.value = null;
    }
};

/* =========================================================
   NAVEGACIÓN
========================================================= */

const volverCursos = () => {
    cursoSeleccionado.value = null;
    alumnos.value = [];
    buscarAlumno.value = '';
};

const recargarTodo = async () => {
    const competenciaAnterior =
        competenciaSeleccionada.value;

    competenciaSeleccionada.value = null;
    cursoSeleccionado.value = null;

    cursos.value = [];
    alumnos.value = [];

    await getCompetencias();

    if (
        competenciaAnterior &&
        competencias.value.some(
            (item) =>
                item.value ===
                competenciaAnterior
        )
    ) {
        competenciaSeleccionada.value =
            competenciaAnterior;
    }
};

/* =========================================================
   WATCH
========================================================= */

watch(
    competenciaSeleccionada,
    async (newValue, oldValue) => {
        if (newValue === oldValue) {
            return;
        }

        buscarCurso.value = '';
        cursoSeleccionado.value = null;
        alumnos.value = [];

        if (!newValue) {
            cursos.value = [];
            return;
        }

        await getCursos();
    }
);

/* =========================================================
   INICIO
========================================================= */

onMounted(async () => {
    await getCompetencias();
});
</script>

<style scoped>
.card {
    background: #ffffff;
    border-radius: 8px;
    padding: 1.25rem;
    border: 1px solid #e5e7eb;
}

:deep(.p-inputnumber-input) {
    text-align: center;
}

@media (max-width: 768px) {
    .card {
        padding: 1rem;
    }
}
</style>
