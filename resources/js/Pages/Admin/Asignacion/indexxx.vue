<template>
  <Head title="Asignación" />
  <AuthenticatedLayout>
    <div
      class="flex flex-wrap gap-2 mb-0 px-3"
      style="justify-content: space-between; align-items:center; border-bottom:solid 1px #cdcdcd9d; min-height:50px; background:white;"
    >
      <div class="flex flex-wrap align-items-center">
        <Button severity="secondary" style="font-size:.9rem" text @click="Inicio">Inicio</Button>

        <div v-if="programaSeleccionado" class="flex align-items-center">
          <i class="pi pi-angle-right" />
          <Button severity="secondary" style="font-size:.9rem" text @click="volverPrograma">
            <span class="texto-corto">{{ programaSeleccionado.label }}</span>
          </Button>
        </div>

        <div v-if="cursoseleccionado" class="flex align-items-center">
          <i class="pi pi-angle-right" />
          <Button severity="secondary" style="font-size:.9rem" text>
            <span class="texto-corto">{{ cursoseleccionado.nombre }}</span>
          </Button>
        </div>
      </div>

      <div v-if="!programaSeleccionado" class="flex align-items-center gap-2">
        <span class="p-input-icon-left">
          <i class="pi pi-search" />
          <InputText
            v-model="buscarprograma"
            style="padding-left:40px; height:38px; width:260px"
            placeholder="Buscar programa"
          />
        </span>
      </div>

      <div v-else-if="!cursoseleccionado" class="flex flex-wrap align-items-center gap-2">
        <Dropdown
          v-model="periodoSeleccionado"
          :options="periodos"
          optionLabel="label"
          optionValue="value"
          placeholder="Período"
          style="width:170px; height:38px"
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
          placeholder="Todas las competencias"
          showClear
          style="width:300px; height:38px"
        />
      </div>
    </div>

    <div class="bg-white shadow-xs p-4 contenido-asignacion">
      <!-- PASO 1: PROGRAMAS DE LA ESCUELA DEL USUARIO -->
      <div v-if="!programaSeleccionado" class="card">
        <div class="mb-3">
          <h3 class="m-0">Programas de estudio</h3>
          <small class="text-600">Se muestran únicamente los programas pertenecientes a su Escuela Profesional.</small>
        </div>

        <DataTable
          v-model:selection="programaSeleccionado"
          selectionMode="single"
          :value="programas"
          dataKey="value"
          :class="'p-datatable-sm'"
          tableStyle="min-width: 34rem"
          style="font-size:.9rem"
          :paginator="true"
          :rows="12"
        >
          <Column field="label" header="Programa de estudio" />
          <Column header="Acción" style="width:130px; text-align:center">
            <template #body="{ data }">
              <Button
                label="Ingresar"
                icon="pi pi-angle-right"
                size="small"
                @click.stop="programaSeleccionado = data"
              />
            </template>
          </Column>
        </DataTable>
      </div>

      <!-- PASO 2: CURSOS DEL PROGRAMA -->
      <div v-else-if="!cursoseleccionado">
        <div class="flex flex-wrap gap-2 mb-3" style="justify-content:space-between; align-items:center">
          <div>
            <strong>{{ programaSeleccionado.label }}</strong>
            <div style="font-size:.82rem; color:#666">
              Los cursos se muestran por programa y período; ya no dependen del usuario que los creó.
            </div>
          </div>

          <span class="p-input-icon-left">
            <i class="pi pi-search" />
            <InputText
              v-model="buscarcurso"
              style="padding-left:40px; height:38px; width:260px"
              placeholder="Buscar curso"
            />
          </span>
        </div>

        <div class="tabla-scroll">
          <DataTable
            v-model:selection="cursoseleccionado"
            selectionMode="single"
            :value="cursos"
            dataKey="id"
            :class="'p-datatable-sm'"
            tableStyle="min-width: 66rem"
            style="font-size:.88rem"
            v-model:first="cursoFirst"
            :paginator="true"
            :rows="8"
          >
            <Column field="nombre" header="Curso" />
            <Column field="competencia" header="Competencia" />
            <Column field="docente" header="Docente" style="min-width:220px">
              <template #body="{ data }">
                <div class="flex flex-column gap-2" style="align-items:flex-start">
                  <span v-if="data.docente">{{ data.docente }}</span>
                  <span v-else style="color:#777">Sin docente asignado</span>

                  <Button
                    :label="data.id_docente ? 'Cambiar docente' : 'Asignar docente'"
                    icon="pi pi-user-edit"
                    size="small"
                    severity="secondary"
                    outlined
                    :disabled="Number(data.id_periodo) !== Number(periodoActivoId)"
                    @click.stop="abrirAsignarDocente(data)"
                  />
                </div>
              </template>
            </Column>
            <Column field="grupo" header="Grupo" style="width:75px" />
            <Column field="programa" header="Programa" />
            <Column field="periodo" header="Período" style="width:100px" />
            <Column field="estado" header="Estado" style="text-align:center; width:95px">
              <template #body="{ data }">
                <Tag v-if="Number(data.estado) === 1" severity="info" value="Activo" />
                <Tag v-else severity="secondary" value="Inactivo" />
              </template>
            </Column>
            <Column header="Lista" style="text-align:center; width:80px">
              <template #body="{ data }">
                <Button
                  icon="pi pi-print"
                  severity="success"
                  size="small"
                  style="width:28px; height:28px"
                  :disabled="Number(data.estado) !== 1"
                  @click.stop="descargarPDF(data.id)"
                />
              </template>
            </Column>
          </DataTable>
        </div>
      </div>

      <!-- PASO 3: ALUMNOS DEL CURSO -->
      <div v-else>
        <div class="flex flex-wrap gap-2 mb-3" style="justify-content:space-between; align-items:center">
          <Button
            severity="primary"
            label="Seleccionar alumnos"
            icon="pi pi-users"
            :disabled="Number(cursoseleccionado.id_periodo) !== Number(periodoActivoId)"
            @click="abrirseleccionar"
          />

          <span class="p-input-icon-left">
            <i class="pi pi-search" />
            <InputText
              v-model="buscar"
              style="padding-left:40px; height:38px; width:260px"
              placeholder="Buscar alumno"
            />
          </span>
        </div>

        <div class="tabla-scroll">
          <DataTable
            :value="detalle_curso"
            :class="'p-datatable-sm'"
            tableStyle="min-width: 65rem"
            style="font-size:.88rem"
            :paginator="true"
            :rows="10"
          >
            <Column field="codigo_est" header="Código" />
            <Column field="semestre" header="Ingreso" />
            <Column field="programa" header="Programa" />
            <Column field="nombres" header="Nombres" />
            <Column field="paterno" header="Paterno" />
            <Column field="materno" header="Materno" />
            <Column field="nota_actual" header="Nota inicial" style="text-align:center">
              <template #body="{ data }"><strong>{{ data.nota_actual ?? '-' }}</strong></template>
            </Column>
            <Column field="nota" header="Nueva nota" style="text-align:center">
              <template #body="{ data }"><strong>{{ data.nota ?? '-' }}</strong></template>
            </Column>
            <Column header="Condición" style="text-align:center; width:110px">
              <template #body="{ data }">
                <Tag v-if="data.nota !== null && Number(data.nota) >= 10.5" severity="success" value="Aprobado" />
                <Tag v-else-if="data.nota !== null" severity="danger" value="Desaprobado" />
                <span v-else>-</span>
              </template>
            </Column>
          </DataTable>
        </div>
      </div>

      <Toast />

      <!-- ASIGNAR / CAMBIAR DOCENTE -->
      <Dialog v-model:visible="modal_docente" modal header="Asignar docente" :style="{ width: '650px', maxWidth: '95vw' }">
        <div v-if="cursoDocente" class="mb-3">
          <div style="font-size:.85rem; color:#666">Curso</div>
          <div style="font-weight:bold">{{ cursoDocente.nombre }}</div>
          <div style="font-size:.85rem; margin-top:4px">{{ cursoDocente.competencia }}</div>
          <div style="font-size:.82rem; color:#666; margin-top:4px">{{ cursoDocente.programa }}</div>
        </div>

        <div class="mb-3">
          <label style="font-weight:bold">Docente</label>
          <Dropdown
            v-model="docenteAsignar"
            :options="docentes2"
            filter
            showClear
            optionLabel="nombres"
            optionValue="id"
            placeholder="Sin docente / seleccione un docente"
            style="width:100%; margin-top:6px"
          />
          <small style="color:#666">Puede guardar el curso sin docente y asignarlo posteriormente.</small>
        </div>

        <template #footer>
          <div class="flex" style="justify-content:flex-end; gap:8px">
            <Button label="Cancelar" outlined @click="modal_docente = false" size="small" />
            <Button label="Guardar" icon="pi pi-check" @click="guardarDocenteAsignado" :loading="guardandoDocente" size="small" />
          </div>
        </template>
      </Dialog>

      <!-- ASIGNAR ALUMNOS -->
      <Dialog v-model:visible="modal_registro" modal header="Asignar alumnos" :style="{ width: '950px', maxWidth: '96vw' }">
        <div class="mb-3 p-3" style="background:#f6f7f9; border-radius:6px">
          <div style="font-size:.82rem; color:#666">Programa de estudio</div>
          <strong>{{ cursoseleccionado?.programa }}</strong>
          <div style="font-size:.82rem; color:#666; margin-top:4px">
            Se muestran únicamente alumnos de este programa que requieren nivelación en la competencia seleccionada.
          </div>
        </div>

        <div class="tabla-scroll">
          <DataTable
            v-model:selection="alumnos_seleccionados_registro"
            selectionMode="multiple"
            dataKey="id"
            :metaKeySelection="false"
            :value="alumnosregistro"
            :class="'p-datatable-sm'"
            tableStyle="min-width: 56rem"
            style="font-size:.88rem"
            :paginator="true"
            :rows="10"
          >
            <Column selectionMode="multiple" headerStyle="width:3rem" />
            <Column field="programa" header="Programa" />
            <Column field="nota_actual" header="Nota actual" style="text-align:center">
              <template #body="{ data }"><strong>{{ data.nota_actual ?? '-' }}</strong></template>
            </Column>
            <Column field="codigo_est" header="Código" />
            <Column field="semestre" header="Ingreso" />
            <Column field="nombres" header="Nombres" />
            <Column field="paterno" header="Paterno" />
            <Column field="materno" header="Materno" />
          </DataTable>
        </div>

        <template #footer>
          <div class="flex" style="justify-content:flex-end; gap:8px">
            <Button label="Cancelar" outlined @click="modal_registro = false" />
            <Button label="Guardar asignación" icon="pi pi-check" @click="asignar" />
          </div>
        </template>
      </Dialog>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { nextTick, ref, watch } from 'vue';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dialog from 'primevue/dialog';
import Dropdown from 'primevue/dropdown';
import Toast from 'primevue/toast';
import Tag from 'primevue/tag';
import { useToast } from 'primevue/usetoast';

const toast = useToast();

const programas = ref([]);
const programaSeleccionado = ref(null);
const buscarprograma = ref('');

const cursos = ref([]);
const cursoseleccionado = ref(null);
const cursoFirst = ref(0);
const buscarcurso = ref('');

const competencias = ref([]);
const competencia = ref(null);

const periodos = ref([]);
const periodoSeleccionado = ref(null);
const periodoActivoId = ref(null);
let inicializandoPeriodo = false;

const detalle_curso = ref([]);
const buscar = ref('');

const modal_docente = ref(false);
const guardandoDocente = ref(false);
const cursoDocente = ref(null);
const docenteAsignar = ref(null);
const docentes2 = ref([]);

const modal_registro = ref(false);
const alumnosregistro = ref([]);
const alumnos_seleccionados_registro = ref([]);
const seleccionadosTemp = ref([]);

const obtenerLista = (response) => {
  if (Array.isArray(response?.datos)) return response.datos;
  if (Array.isArray(response?.datos?.data)) return response.datos.data;
  return [];
};

const showToast = (tipo, titulo, detalle) => {
  toast.add({
    severity: tipo || 'info',
    summary: titulo || 'Información',
    detail: detalle || '',
    life: 3500,
  });
};

const mostrarError = (error, mensaje = 'Ocurrió un error') => {
  console.error(error);
  const detalle = error?.response?.data?.mensaje || error?.response?.data?.message || mensaje;
  showToast('error', 'Error', detalle);
};

const getMisProgramas = async () => {
  try {
    const res = await axios.post('/get-mis-programas', { term: buscarprograma.value });
    programas.value = obtenerLista(res.data);
  } catch (error) {
    programas.value = [];
    mostrarError(error, 'No se pudieron cargar los programas de estudio');
  }
};

const getCompetencias = async () => {
  if (!programaSeleccionado.value?.value) {
    competencias.value = [];
    return;
  }

  try {
    const res = await axios.post('/coordinador/get-competencias', {
      programa: programaSeleccionado.value.value,
      term: '',
    });
    competencias.value = obtenerLista(res.data);
  } catch (error) {
    competencias.value = [];
    mostrarError(error, 'No se pudieron cargar las competencias del programa');
  }
};

const getCursos = async () => {
  if (!programaSeleccionado.value?.value) {
    cursos.value = [];
    return;
  }

  try {
    const res = await axios.post('/coordinador/get-cursos', {
      term: buscarcurso.value,
      competencia: competencia.value,
      programa: programaSeleccionado.value.value,
      periodo: periodoSeleccionado.value,
    });

    cursos.value = obtenerLista(res.data);

    if (Array.isArray(res.data?.periodos)) {
      periodos.value = res.data.periodos;
    }

    periodoActivoId.value = res.data?.periodo_activo ?? periodoActivoId.value;

    if (!periodoSeleccionado.value && res.data?.periodo_activo) {
      inicializandoPeriodo = true;
      periodoSeleccionado.value = Number(res.data.periodo_activo);
      await nextTick();
      inicializandoPeriodo = false;
    }
  } catch (error) {
    cursos.value = [];
    mostrarError(error, 'No se pudieron cargar los cursos');
  }
};

const getDocenteXcompetencia = async (idCompetencia) => {
  if (!idCompetencia) {
    docentes2.value = [];
    return;
  }

  try {
    const res = await axios.post('/coordinador/get-docente-competencia', {
      term: '',
      competencia: idCompetencia,
    });
    docentes2.value = obtenerLista(res.data);
  } catch (error) {
    docentes2.value = [];
    mostrarError(error, 'No se pudieron cargar los docentes');
  }
};

const getDetalleCurso = async () => {
  if (!cursoseleccionado.value?.id) {
    detalle_curso.value = [];
    alumnos_seleccionados_registro.value = [];
    seleccionadosTemp.value = [];
    return;
  }

  try {
    const res = await axios.post('/coordinador/get-detalle-curso', {
      term: buscar.value,
      curso: cursoseleccionado.value.id,
    });

    detalle_curso.value = obtenerLista(res.data);
    alumnos_seleccionados_registro.value = Array.isArray(res.data?.registrados?.data)
      ? res.data.registrados.data
      : [];
    seleccionadosTemp.value = [...alumnos_seleccionados_registro.value];
  } catch (error) {
    detalle_curso.value = [];
    alumnos_seleccionados_registro.value = [];
    seleccionadosTemp.value = [];
    mostrarError(error, 'No se pudo cargar el detalle del curso');
  }
};

const getAlumnosRegistros = async () => {
  if (!cursoseleccionado.value?.id_competencia || !cursoseleccionado.value?.id_programa) {
    alumnosregistro.value = [];
    return;
  }

  try {
    const res = await axios.post('/get-alumnos-registro', {
      term: '',
      curso: cursoseleccionado.value.id_competencia,
      programa: cursoseleccionado.value.id_programa,
    });

    alumnosregistro.value = Array.isArray(res.data?.datos) ? res.data.datos : obtenerLista(res.data);
  } catch (error) {
    alumnosregistro.value = [];
    mostrarError(error, 'No se pudieron cargar los alumnos del programa');
  }
};

const abrirAsignarDocente = async (item) => {
  cursoDocente.value = item;
  docenteAsignar.value = item.id_docente ?? null;
  await getDocenteXcompetencia(item.id_competencia);
  modal_docente.value = true;
};

const guardarDocenteAsignado = async () => {
  if (!cursoDocente.value) return;

  guardandoDocente.value = true;
  try {
    const item = cursoDocente.value;
    const res = await axios.post('/coordinador/save-curso', {
      id: item.id,
      nombre: item.nombre,
      id_competencia: item.id_competencia,
      id_docente: docenteAsignar.value || null,
      grupo: item.grupo,
      estado: Number(item.estado) === 1,
      id_programa: item.id_programa,
    });

    showToast(res.data.tipo, res.data.titulo, res.data.mensaje || 'Docente actualizado correctamente.');
    await getCursos();
    modal_docente.value = false;
    cursoDocente.value = null;
    docenteAsignar.value = null;
  } catch (error) {
    mostrarError(error, 'No se pudo actualizar el docente');
  } finally {
    guardandoDocente.value = false;
  }
};

const abrirseleccionar = async () => {
  alumnosregistro.value = [];
  await getAlumnosRegistros();
  modal_registro.value = true;
};

const asignar = async () => {
  const diferenciaAgregar = alumnos_seleccionados_registro.value.filter(
    (actual) => !seleccionadosTemp.value.some((anterior) => anterior.id === actual.id)
  );

  const diferenciaQuitar = seleccionadosTemp.value.filter(
    (anterior) => !alumnos_seleccionados_registro.value.some((actual) => actual.id === anterior.id)
  );

  try {
    const res = await axios.post('/coordinador/asignar-curso-nivelacion', {
      curso: cursoseleccionado.value.id,
      diferencia: diferenciaAgregar,
      diferencia2: diferenciaQuitar,
    });

    showToast(res.data.tipo, res.data.titulo, res.data.mensaje);
    modal_registro.value = false;
    await getDetalleCurso();
  } catch (error) {
    mostrarError(error, 'No se pudo guardar la asignación de alumnos');
  }
};

const Inicio = () => {
  programaSeleccionado.value = null;
  cursoseleccionado.value = null;
  cursos.value = [];
  detalle_curso.value = [];
  competencias.value = [];
  competencia.value = null;
  periodoSeleccionado.value = null;
};

const volverPrograma = async () => {
  cursoseleccionado.value = null;
  detalle_curso.value = [];
  alumnosregistro.value = [];
  buscar.value = '';
  await getCursos();
};

const descargarPDF = (id) => {
  window.open('/coordinador/generar-pdf/' + id, '_self');
};

watch(buscarprograma, () => getMisProgramas());

watch(programaSeleccionado, async (nuevo) => {
  cursoseleccionado.value = null;
  cursos.value = [];
  competencia.value = null;
  periodoSeleccionado.value = null;
  periodoActivoId.value = null;
  cursoFirst.value = 0;

  if (nuevo) {
    await getCompetencias();
    await getCursos();
  }
});

watch(buscarcurso, () => {
  if (programaSeleccionado.value) getCursos();
});

watch(competencia, () => {
  cursoFirst.value = 0;
  if (programaSeleccionado.value) getCursos();
});

watch(periodoSeleccionado, () => {
  if (!inicializandoPeriodo && programaSeleccionado.value) {
    cursoseleccionado.value = null;
    cursoFirst.value = 0;
    getCursos();
  }
});

watch(cursoseleccionado, async (nuevo) => {
  if (nuevo) {
    buscar.value = '';
    await getDetalleCurso();
  }
});

watch(buscar, () => {
  if (cursoseleccionado.value) getDetalleCurso();
});

getMisProgramas();
</script>

<style scoped>
.contenido-asignacion {
  min-height: calc(100vh - 140px);
  font-family: Arial, Helvetica, sans-serif;
  overflow-x: hidden;
}

.tabla-scroll {
  width: 100%;
  overflow-x: auto;
}

.texto-corto {
  display: inline-block;
  max-width: 260px;
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
}

@media (max-width: 768px) {
  .contenido-asignacion {
    padding: .75rem !important;
  }

  .texto-corto {
    max-width: 150px;
  }
}
</style>
