<template>
  <Head title="Asignación" />
  <AuthenticatedLayout>
    <div
      class="flex mb-0"
      style="justify-content: space-between; align-items:center; margin-top:0; border-bottom:solid 1px #cdcdcd9D; min-height:50px; background:white; padding: 4px 8px; gap: 10px;"
    >
      <div class="flex align-items-center" style="min-width: 0; flex-wrap: wrap;">
        <Button severity="secondary" style="font-size: 0.85rem" text @click="Inicio">Inicio</Button>

        <div v-if="escuela" class="flex align-items-center">
          <i class="pi pi-angle-right" />
          <Button severity="secondary" @click="volverEscuela" style="font-size: 0.85rem" text>
            <span class="breadcrumb-text">{{ escuela.escuela }}</span>
          </Button>
        </div>

        <div v-if="programaSeleccionado" class="flex align-items-center">
          <i class="pi pi-angle-right" />
          <Button severity="secondary" @click="volverPrograma" style="font-size: 0.85rem" text>
            <span class="breadcrumb-text">{{ programaSeleccionado.label }}</span>
          </Button>
        </div>

        <div v-if="cursoseleccionado" class="flex align-items-center">
          <i class="pi pi-angle-right" />
          <Button severity="secondary" style="font-size: 0.85rem" text>
            <span class="breadcrumb-text">{{ cursoseleccionado.nombre }}</span>
          </Button>
        </div>
      </div>

      <div v-if="!escuela" class="flex mr-2">
        <span class="p-input-icon-left">
          <i class="pi pi-search" />
          <InputText v-model="buscarescuela" class="compact-input" placeholder="Buscar escuela" />
        </span>
      </div>

      <div v-else-if="!programaSeleccionado" class="flex mr-2">
        <span class="p-input-icon-left">
          <i class="pi pi-search" />
          <InputText v-model="buscarprograma" class="compact-input" placeholder="Buscar programa" />
        </span>
      </div>

      <div v-else-if="!cursoseleccionado" class="flex align-items-center gap-2 mr-2" style="flex-wrap: wrap; justify-content: flex-end;">
        <Dropdown
          v-model="periodoSeleccionado"
          :options="periodos"
          optionLabel="label"
          optionValue="value"
          placeholder="Período"
          class="compact-dropdown"
          style="min-width: 180px"
        >
          <template #option="slotProps">
            <div class="flex align-items-center gap-2">
              <span>{{ slotProps.option.label }}</span>
              <Tag v-if="slotProps.option.estado === 'activo'" severity="success" value="Activo" />
            </div>
          </template>
        </Dropdown>

        <Dropdown
          v-model="competencia"
          :options="competencias"
          optionLabel="label"
          optionValue="value"
          showClear
          placeholder="Todas las competencias"
          class="compact-dropdown"
          style="min-width: 260px"
        />
      </div>

      <div v-else class="flex mr-2">
        <span class="p-input-icon-left">
          <i class="pi pi-search" />
          <InputText v-model="buscarDetalle" class="compact-input" placeholder="Buscar alumno" />
        </span>
      </div>
    </div>

    <div class="bg-white shadow-xs p-4 asignacion-container">
      <!-- PASO 1: ESCUELA -->
      <div v-if="!escuela" class="card">
        <div class="section-title">Seleccione una Escuela Profesional</div>
        <DataTable
          v-model:selection="escuela"
          selectionMode="single"
          dataKey="id"
          :value="escuelas"
          class="p-datatable-sm compact-table"
          :paginator="true"
          :rows="10"
          responsiveLayout="scroll"
        >
          <Column field="filial" header="Ubicación" />
          <Column field="escuela" header="Escuela Profesional" />
          <Column field="facultad" header="Facultad" />
          <Column field="area" header="Área" />
        </DataTable>
      </div>

      <!-- PASO 2: PROGRAMA -->
      <div v-else-if="!programaSeleccionado">
        <div class="section-title">Programas de estudio de {{ escuela.escuela }}</div>
        <DataTable
          v-model:selection="programaSeleccionado"
          selectionMode="single"
          dataKey="value"
          :value="programas"
          class="p-datatable-sm compact-table"
          :paginator="true"
          :rows="12"
          responsiveLayout="scroll"
        >
          <Column field="label" header="Programa de estudio" />
          <Column header="Acción" style="width: 110px">
            <template #body="{ data }">
              <Button label="Ingresar" size="small" outlined @click.stop="programaSeleccionado = data" />
            </template>
          </Column>
        </DataTable>
      </div>

      <!-- PASO 3: CURSOS -->
      <div v-else-if="!cursoseleccionado">
        <div class="flex align-items-center mb-3" style="justify-content: space-between; gap: 10px; flex-wrap: wrap;">
          <div class="flex align-items-center gap-2">
            <Button
              severity="primary"
              label="Nuevo Curso"
              icon="pi pi-plus"
              size="small"
              :disabled="!esPeriodoActivo"
              @click="nuevoCurso"
            />
            <Tag v-if="esPeriodoActivo" severity="success" value="Período activo" />
            <Tag v-else severity="secondary" value="Período histórico: solo consulta" />
          </div>

          <span class="p-input-icon-left">
            <i class="pi pi-search" />
            <InputText v-model="buscarcurso" class="compact-input" placeholder="Buscar curso" />
          </span>
        </div>

        <DataTable
          v-model:selection="cursoseleccionado"
          selectionMode="single"
          dataKey="id"
          :value="cursos"
          class="p-datatable-sm compact-table"
          :paginator="true"
          :rows="10"
          responsiveLayout="scroll"
        >
          <Column field="nombre" header="Curso" />
          <Column field="competencia" header="Competencia" />
          <Column field="docente" header="Docente">
            <template #body="{ data }">
              <span>{{ data.docente || 'Sin docente' }}</span>
            </template>
          </Column>
          <Column field="grupo" header="Grupo" style="width: 75px" />
          <Column field="periodo" header="Período" />
          <Column field="programa" header="Programa" />
          <Column field="estado" header="Estado" style="width: 90px">
            <template #body="{ data }">
              <Tag v-if="Number(data.estado) === 1" severity="info" value="Activo" />
              <Tag v-else severity="secondary" value="Inactivo" />
            </template>
          </Column>
          <Column header="Lista" style="width: 70px">
            <template #body="{ data }">
              <Button
                :disabled="Number(data.estado) !== 1"
                icon="pi pi-print"
                severity="success"
                size="small"
                text
                @click.stop="descargarPDF(data.id)"
              />
            </template>
          </Column>
          <Column header="Acciones" style="width: 115px">
            <template #body="{ data }">
              <div class="flex gap-1">
                <Button
                  icon="pi pi-pencil"
                  size="small"
                  text
                  :disabled="!esPeriodoActivo"
                  @click.stop="editar(data)"
                />
                <Button
                  icon="pi pi-trash"
                  severity="danger"
                  size="small"
                  text
                  :disabled="!esPeriodoActivo"
                  @click.stop="confirmarEliminar($event, data)"
                />
              </div>
            </template>
          </Column>
        </DataTable>
      </div>

      <!-- PASO 4: ALUMNOS DEL CURSO -->
      <div v-else>
        <div class="flex align-items-center mb-3" style="justify-content: space-between; gap: 10px; flex-wrap: wrap;">
          <div class="flex align-items-center gap-2">
            <Button
              severity="primary"
              label="Seleccionar Alumnos"
              icon="pi pi-users"
              size="small"
              :disabled="!esPeriodoActivo || Number(cursoseleccionado?.estado) !== 1"
              @click="abrirSeleccionar"
            />
            <Tag v-if="!esPeriodoActivo" severity="secondary" value="Período histórico: solo consulta" />
          </div>
        </div>

        <DataTable
          :value="detalle_curso"
          class="p-datatable-sm compact-table"
          :paginator="true"
          :rows="10"
          responsiveLayout="scroll"
        >
          <Column field="codigo_est" header="Código" />
          <Column field="semestre" header="Ingreso" />
          <Column field="programa" header="Programa" />
          <Column field="nombres" header="Nombres" />
          <Column field="paterno" header="Paterno" />
          <Column field="materno" header="Materno" />
          <Column field="nota_actual" header="Nota inicial" style="width: 95px" />
          <Column field="nota" header="Nota curso" style="width: 95px" />
          <Column header="Condición" style="width: 110px">
            <template #body="{ data }">
              <Tag v-if="data.nota !== null && Number(data.nota) >= 10.5" severity="success" value="Aprobado" />
              <Tag v-else-if="data.nota !== null" severity="danger" value="Desaprobado" />
              <Tag v-else severity="secondary" value="Sin nota" />
            </template>
          </Column>
        </DataTable>
      </div>

      <Toast />
      <ConfirmPopup />

      <!-- CURSO -->
      <Dialog
        v-model:visible="visible"
        modal
        :header="curso.id ? 'Editar Curso' : 'Curso nuevo'"
        :style="{ width: '520px', maxWidth: '95vw' }"
      >
        <div class="mb-3">
          <label class="field-label">Escuela Profesional</label>
          <InputText :modelValue="escuela?.escuela || ''" disabled class="w-full compact-input-field" />
        </div>

        <div class="mb-3">
          <label class="field-label">Programa de estudio</label>
          <InputText :modelValue="programaSeleccionado?.label || ''" disabled class="w-full compact-input-field" />
        </div>

        <div class="flex gap-3">
          <div class="mb-3" style="flex: 1">
            <label class="field-label">Competencia</label>
            <Dropdown
              v-model="cursocompetencia"
              :options="competencias"
              optionLabel="label"
              optionValue="value"
              placeholder="Seleccione una competencia"
              class="w-full"
              :disabled="Boolean(curso.id) && Number(curso.alumnos_count) > 0"
            />
            <small v-if="curso.id && Number(curso.alumnos_count) > 0" style="color:#666">
              La competencia no puede cambiarse porque el curso ya tiene alumnos matriculados.
            </small>
          </div>
          <div class="mb-3" style="width: 120px">
            <label class="field-label">Grupo</label>
            <Dropdown
              v-model="curso.grupo"
              :options="grupos"
              optionLabel="label"
              optionValue="value"
              class="w-full"
            />
          </div>
        </div>

        <div class="mb-3">
          <label class="field-label">Nombre del Curso</label>
          <InputText v-model="curso.nombre" class="w-full compact-input-field" />
        </div>

        <div class="mb-3">
          <label class="field-label">Docente <small>(opcional)</small></label>
          <Dropdown
            v-model="curso.id_docente"
            :options="docentes2"
            filter
            showClear
            optionLabel="nombres"
            optionValue="id"
            placeholder="Sin docente / seleccione un docente"
            class="w-full"
          />
        </div>

        <div class="flex align-items-center gap-2 mb-2">
          <InputSwitch v-model="curso.estado" />
          <label>Curso activo</label>
        </div>

        <template #footer>
          <Button label="Cancelar" outlined size="small" @click="visible = false" />
          <Button label="Guardar" size="small" @click="guardar" />
        </template>
      </Dialog>

      <!-- ALUMNOS -->
      <Dialog
        v-model:visible="modal_registro"
        modal
        header="Seleccionar alumnos para matricular"
        :style="{ width: '950px', maxWidth: '96vw' }"
      >
        <div class="mb-3">
          <div class="text-sm"><strong>Programa:</strong> {{ programaSeleccionado?.label }}</div>
          <div class="text-sm"><strong>Curso:</strong> {{ cursoseleccionado?.nombre }}</div>
          <div class="text-sm"><strong>Competencia:</strong> {{ cursoseleccionado?.competencia }}</div>
        </div>

        <DataTable
          v-model:selection="alumnos_seleccionados_registro"
          selectionMode="multiple"
          dataKey="id"
          :metaKeySelection="false"
          :value="alumnosregistro"
          class="p-datatable-sm compact-table"
          :paginator="true"
          :rows="10"
          responsiveLayout="scroll"
        >
          <Column selectionMode="multiple" headerStyle="width: 3rem" />
          <Column field="codigo_est" header="Código" />
          <Column field="semestre" header="Ingreso" />
          <Column field="programa" header="Programa" />
          <Column field="nombres" header="Nombres" />
          <Column field="paterno" header="Paterno" />
          <Column field="materno" header="Materno" />
          <Column field="nota_actual" header="Nota inicial" />
        </DataTable>

        <template #footer>
          <Button label="Cancelar" outlined size="small" @click="modal_registro = false" />
          <Button label="Guardar matrícula" size="small" @click="asignar" />
        </template>
      </Dialog>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/LayoutSuperadmi.vue';
import { Head } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import axios from 'axios';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dialog from 'primevue/dialog';
import Dropdown from 'primevue/dropdown';
import InputSwitch from 'primevue/inputswitch';
import Toast from 'primevue/toast';
import Tag from 'primevue/tag';
import ConfirmPopup from 'primevue/confirmpopup';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';

const base = '/superadmi';
const toast = useToast();
const confirm = useConfirm();

const escuela = ref(null);
const escuelas = ref([]);
const buscarescuela = ref('');

const programas = ref([]);
const programaSeleccionado = ref(null);
const buscarprograma = ref('');

const competencias = ref([]);
const competencia = ref(null);

const periodos = ref([]);
const periodoActivo = ref(null);
const periodoSeleccionado = ref(null);
const inicializandoPeriodo = ref(false);

const cursos = ref([]);
const cursoseleccionado = ref(null);
const buscarcurso = ref('');

const detalle_curso = ref([]);
const buscarDetalle = ref('');

const visible = ref(false);
const cursocompetencia = ref(null);
const docentes2 = ref([]);
const cargandoEdicion = ref(false);

const curso = ref({
  id: null,
  nombre: '',
  id_docente: null,
  grupo: 'A',
  estado: true,
  alumnos_count: 0,
});

const grupos = [
  { value: 'A', label: 'Grupo A' },
  { value: 'B', label: 'Grupo B' },
  { value: 'C', label: 'Grupo C' },
  { value: 'D', label: 'Grupo D' },
  { value: 'E', label: 'Grupo E' },
];

const modal_registro = ref(false);
const alumnosregistro = ref([]);
const alumnos_seleccionados_registro = ref([]);
const seleccionadosTemp = ref([]);

const esPeriodoActivo = computed(() =>
  periodoActivo.value !== null && Number(periodoSeleccionado.value) === Number(periodoActivo.value)
);

const obtenerLista = (payload) => {
  if (Array.isArray(payload?.datos)) return payload.datos;
  if (Array.isArray(payload?.datos?.data)) return payload.datos.data;
  if (Array.isArray(payload?.data)) return payload.data;
  if (Array.isArray(payload)) return payload;
  return [];
};

const mostrarError = (error, titulo = 'Error') => {
  const mensaje = error?.response?.data?.mensaje || error?.response?.data?.message || error?.message || 'No se pudo completar la operación.';
  toast.add({ severity: 'error', summary: titulo, detail: mensaje, life: 4000 });
};

const showToast = (severity, summary, detail) => {
  toast.add({ severity: severity || 'info', summary: summary || '', detail: detail || '', life: 3000 });
};

const getEscuelas = async () => {
  try {
    const res = await axios.post(`${base}/get-escuelas`, { term: buscarescuela.value });
    escuelas.value = obtenerLista(res.data);
  } catch (error) {
    escuelas.value = [];
    mostrarError(error, 'No se pudieron cargar las escuelas');
  }
};

const getProgramasEscuela = async () => {
  if (!escuela.value?.id) {
    programas.value = [];
    return;
  }

  try {
    const res = await axios.post(`${base}/get-programas-escuela`, {
      id_escuela: escuela.value.id,
      term: buscarprograma.value,
    });
    programas.value = obtenerLista(res.data);
  } catch (error) {
    programas.value = [];
    mostrarError(error, 'No se pudieron cargar los programas');
  }
};

const getCompetenciasPrograma = async () => {
  if (!programaSeleccionado.value?.value) {
    competencias.value = [];
    return;
  }

  try {
    const res = await axios.post(`${base}/get-competencias`, {
      term: '',
      programa: programaSeleccionado.value.value,
    });
    competencias.value = obtenerLista(res.data);
  } catch (error) {
    competencias.value = [];
    mostrarError(error, 'No se pudieron cargar las competencias');
  }
};

const getDocenteXcompetencia = async () => {
  if (!cursocompetencia.value) {
    docentes2.value = [];
    return;
  }

  try {
    const res = await axios.post(`${base}/get-docente-competencia`, {
      term: '',
      competencia: cursocompetencia.value,
    });
    docentes2.value = obtenerLista(res.data);
  } catch (error) {
    docentes2.value = [];
    mostrarError(error, 'No se pudieron cargar los docentes');
  }
};

const getCursos = async () => {
  if (!programaSeleccionado.value?.value) {
    cursos.value = [];
    return;
  }

  try {
    const res = await axios.post(`${base}/get-cursos`, {
      term: buscarcurso.value,
      competencia: competencia.value,
      programa: programaSeleccionado.value.value,
      periodo: periodoSeleccionado.value,
    });

    cursos.value = obtenerLista(res.data);
    periodos.value = Array.isArray(res.data?.periodos) ? res.data.periodos : periodos.value;
    periodoActivo.value = res.data?.periodo_activo ?? periodoActivo.value;

    if (!periodoSeleccionado.value && periodoActivo.value) {
      inicializandoPeriodo.value = true;
      periodoSeleccionado.value = periodoActivo.value;
      inicializandoPeriodo.value = false;
    }
  } catch (error) {
    cursos.value = [];
    mostrarError(error, 'No se pudieron cargar los cursos');
  }
};

const limpiarCurso = () => {
  cursocompetencia.value = null;
  docentes2.value = [];
  curso.value = { id: null, nombre: '', id_docente: null, grupo: 'A', estado: true, alumnos_count: 0 };
};

const nuevoCurso = async () => {
  if (!esPeriodoActivo.value) {
    showToast('warn', 'Período histórico', 'Los cursos nuevos se registran únicamente en el período activo.');
    return;
  }

  limpiarCurso();
  await getCompetenciasPrograma();
  visible.value = true;
};

const editar = async (item) => {
  if (!esPeriodoActivo.value) {
    showToast('warn', 'Solo consulta', 'Los períodos anteriores no se pueden modificar.');
    return;
  }

  limpiarCurso();
  cargandoEdicion.value = true;

  try {
    await getCompetenciasPrograma();
    curso.value = {
      id: item.id,
      nombre: item.nombre || '',
      id_docente: item.id_docente || null,
      grupo: item.grupo || 'A',
      estado: Number(item.estado) === 1,
      alumnos_count: Number(item.alumnos_count || 0),
    };
    cursocompetencia.value = item.id_competencia || null;
    await getDocenteXcompetencia();
    curso.value.id_docente = item.id_docente || null;
    visible.value = true;
  } finally {
    cargandoEdicion.value = false;
  }
};

const guardar = async () => {
  if (!programaSeleccionado.value?.value) return;
  if (!curso.value.nombre?.trim()) {
    showToast('warn', 'Falta información', 'Ingrese el nombre del curso.');
    return;
  }
  if (!cursocompetencia.value) {
    showToast('warn', 'Falta información', 'Seleccione una competencia.');
    return;
  }

  try {
    const res = await axios.post(`${base}/save-curso`, {
      id: curso.value.id,
      nombre: curso.value.nombre,
      id_competencia: cursocompetencia.value,
      id_docente: curso.value.id_docente || null,
      grupo: curso.value.grupo,
      estado: curso.value.estado,
      id_programa: programaSeleccionado.value.value,
    });

    showToast(res.data?.tipo, res.data?.titulo, res.data?.mensaje);
    visible.value = false;
    limpiarCurso();
    await getCursos();
  } catch (error) {
    mostrarError(error, 'No se pudo guardar el curso');
  }
};

const getDetalleCurso = async () => {
  if (!cursoseleccionado.value?.id) {
    detalle_curso.value = [];
    return;
  }

  try {
    const res = await axios.post(`${base}/get-detalle-curso`, {
      term: buscarDetalle.value,
      curso: cursoseleccionado.value.id,
    });
    detalle_curso.value = obtenerLista(res.data);
    alumnos_seleccionados_registro.value = obtenerLista({ datos: res.data?.registrados });
    seleccionadosTemp.value = [...alumnos_seleccionados_registro.value];
  } catch (error) {
    detalle_curso.value = [];
    mostrarError(error, 'No se pudo cargar el detalle del curso');
  }
};

const getAlumnosRegistros = async () => {
  if (!cursoseleccionado.value?.id_competencia || !programaSeleccionado.value?.value) {
    alumnosregistro.value = [];
    return;
  }

  try {
    const res = await axios.post(`${base}/get-alumnos-registro`, {
      term: '',
      programa: programaSeleccionado.value.value,
      curso: cursoseleccionado.value.id_competencia,
      id_curso: cursoseleccionado.value.id,
    });
    alumnosregistro.value = obtenerLista(res.data);
  } catch (error) {
    alumnosregistro.value = [];
    mostrarError(error, 'No se pudieron cargar los alumnos');
  }
};

const abrirSeleccionar = async () => {
  if (!esPeriodoActivo.value) {
    showToast('warn', 'Solo consulta', 'Los alumnos de períodos anteriores no se pueden modificar.');
    return;
  }

  if (Number(cursoseleccionado.value?.estado) !== 1) {
    showToast('warn', 'Curso inactivo', 'No se puede modificar la matrícula de un curso inactivo.');
    return;
  }

  // Refrescamos la matrícula antes de abrir el modal para evitar trabajar
  // con una selección antigua si otro usuario hizo cambios.
  await getDetalleCurso();
  await getAlumnosRegistros();
  modal_registro.value = true;
};

const asignar = async () => {
  const nuevos = alumnos_seleccionados_registro.value.filter(
    (a) => !seleccionadosTemp.value.some((b) => b.id === a.id)
  );
  const retirados = seleccionadosTemp.value.filter(
    (a) => !alumnos_seleccionados_registro.value.some((b) => b.id === a.id)
  );

  try {
    const res = await axios.post(`${base}/asignar-curso-nivelacion`, {
      curso: cursoseleccionado.value.id,
      alumnos: alumnos_seleccionados_registro.value,
      anteriores: seleccionadosTemp.value,
      diferencia: nuevos,
      diferencia2: retirados,
    });
    showToast(res.data?.tipo, res.data?.titulo, res.data?.mensaje);
    modal_registro.value = false;
    await getDetalleCurso();
  } catch (error) {
    mostrarError(error, 'No se pudo realizar la matrícula');
  }
};

const deleteCurso = async (id) => {
  try {
    const res = await axios.get(`${base}/delete-curso/${id}`);
    showToast(res.data?.tipo, res.data?.titulo, res.data?.mensaje);
    await getCursos();
  } catch (error) {
    mostrarError(error, 'No se pudo eliminar el curso');
  }
};

const confirmarEliminar = (event, item) => {
  confirm.require({
    target: event.currentTarget,
    message: `¿Está seguro de eliminar el curso ${item?.nombre || ''}?`,
    icon: 'pi pi-info-circle',
    acceptClass: 'p-button-danger',
    accept: () => deleteCurso(item.id),
  });
};

const descargarPDF = (id) => {
  window.open(`${base}/generar-pdf/${id}`, '_self');
};

const Inicio = () => {
  escuela.value = null;
  programaSeleccionado.value = null;
  cursoseleccionado.value = null;
  programas.value = [];
  cursos.value = [];
  detalle_curso.value = [];
  competencias.value = [];
  competencia.value = null;
  periodoSeleccionado.value = null;
};

const volverEscuela = () => {
  programaSeleccionado.value = null;
  cursoseleccionado.value = null;
  cursos.value = [];
  detalle_curso.value = [];
  competencias.value = [];
  competencia.value = null;
  periodoSeleccionado.value = null;
};

const volverPrograma = () => {
  cursoseleccionado.value = null;
  detalle_curso.value = [];
};

watch(escuela, async (value) => {
  programaSeleccionado.value = null;
  cursoseleccionado.value = null;
  cursos.value = [];
  detalle_curso.value = [];
  periodoSeleccionado.value = null;
  if (value) await getProgramasEscuela();
});

watch(programaSeleccionado, async (value) => {
  cursoseleccionado.value = null;
  cursos.value = [];
  detalle_curso.value = [];
  competencia.value = null;
  periodoSeleccionado.value = null;
  periodoActivo.value = null;
  if (value) {
    await getCompetenciasPrograma();
    await getCursos();
  }
});

watch(cursoseleccionado, async (value) => {
  if (value) await getDetalleCurso();
});

watch(buscarescuela, getEscuelas);
watch(buscarprograma, getProgramasEscuela);
watch(buscarcurso, getCursos);
watch(buscarDetalle, getDetalleCurso);
watch(competencia, getCursos);
watch(periodoSeleccionado, async () => {
  if (!inicializandoPeriodo.value && programaSeleccionado.value) await getCursos();
});

watch(cursocompetencia, async (value) => {
  if (cargandoEdicion.value) return;
  curso.value.id_docente = null;
  docentes2.value = [];

  if (!value) {
    if (!curso.value.id) curso.value.nombre = '';
    return;
  }

  await getDocenteXcompetencia();

  if (!curso.value.id) {
    const comp = competencias.value.find((item) => Number(item.value) === Number(value));
    if (comp) curso.value.nombre = comp.label;
  }
});

watch(visible, (value) => {
  if (!value) limpiarCurso();
});

getEscuelas();
</script>

<style scoped>
.asignacion-container {
  min-height: calc(100vh - 140px);
  overflow-x: auto;
  font-family: Arial, Helvetica, sans-serif;
  font-size: 0.88rem;
}

.section-title {
  font-weight: 600;
  font-size: 0.95rem;
  margin-bottom: 12px;
}

.breadcrumb-text {
  max-width: 190px;
  display: inline-block;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.compact-input {
  height: 36px;
  padding-left: 38px;
  font-size: 0.85rem;
}

.compact-input-field {
  height: 38px;
  font-size: 0.88rem;
}

.field-label {
  display: block;
  font-size: 0.85rem;
  margin-bottom: 5px;
}

.compact-table {
  font-size: 0.84rem;
}

:deep(.compact-table .p-datatable-thead > tr > th),
:deep(.compact-table .p-datatable-tbody > tr > td) {
  padding: 0.48rem 0.6rem;
}

:deep(.compact-dropdown .p-dropdown-label) {
  padding-top: 0.55rem;
  padding-bottom: 0.55rem;
  font-size: 0.85rem;
}

@media (max-width: 900px) {
  .breadcrumb-text {
    max-width: 120px;
  }

  .asignacion-container {
    padding: 0.75rem !important;
  }
}
</style>
