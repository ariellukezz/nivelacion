<template>
  <Head title="Asignación" />
  <AuthenticatedLayout>
    <!-- Barra superior -->
    <div
      class="flex mb-0"
      style="justify-content: space-between; align-items:center; margin-top:0px; border-bottom:solid 1px #cdcdcd9D; height:50px; background:white;"
    >
      <div class="flex">
        <Button severity="secondary" style="font-size: 0.9rem" text @click="Inicio">Inicio</Button>

        <div v-if="escuela !== null" class="flex justify-content-center" style="align-items:center;">
          <i class="pi pi-angle-right" />
          <Button severity="secondary" @click="resEsuela" style="font-size: 0.9rem" text>
            <div style="max-width: 180px; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
              <span>{{ escuela.escuela }}</span>
            </div>
          </Button>
        </div>

        <div v-if="cursoseleccionado !== null" class="flex justify-content-center" style="align-items:center;">
          <i class="pi pi-angle-right" />
          <Button severity="secondary" style="font-size: 0.9rem" text>
            <div style="max-width: 180px; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
              <span>{{ cursoseleccionado.nombre }}</span>
            </div>
          </Button>
        </div>
      </div>

      <!-- Buscador de escuela (cuando aún no se selecciona) -->
      <div v-if="escuela === null">
        <div class="flex mr-4" style="justify-content: flex-end;">
          <span class="p-input-icon-left">
            <i class="pi pi-search" />
            <InputText v-model="buscarescuela" style="padding-left: 40px; height: 40px;" placeholder="Buscar escuela" />
          </span>
        </div>
      </div>

      <!-- Filtro de competencia (cuando ya hay escuela y aún no se eligió un curso) -->
      <div v-if="escuela !== null && cursoseleccionado === null">
        <Dropdown
          v-model="competencia"
          :options="competencias"
          severity="primary"
          optionLabel="label"
          optionValue="value"
          placeholder="Selecciona una competencia"
          style="width:325px; height:38px"
          class="w-full md:w-11rem mr-4"
        >
          <template #option="slotProps">
            <div
              class="flex align-items-center"
              style="width: 280px; font-size:0.9rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;"
            >
              <div>{{ slotProps.option.label }}</div>
            </div>
          </template>
        </Dropdown>
      </div>
    </div>

    <!-- Contenido -->
    <div class="bg-white shadow-xs p-4" style="height: calc(100vh - 140px); font-family: Arial, Helvetica, sans-serif;">
      <!-- PASO 1: Seleccionar Escuela -->
      <div v-if="escuela === null" class="card">
        <DataTable
          v-model:selection="escuela"
          selectionMode="single"
          :value="escuelas"
          :class="'p-datatable-sm'"
          tableStyle="min-width: 50rem"
          style="font-size: .9rem;"
          :paginator="true"
          :rows="10"
          :filters="filters"
        >
          <!-- ✅ CAMBIO: agregamos Ubicación (campo 'filial') igual que en Coordinador -->
          <Column field="filial" header="Ubicación"></Column>
          <Column field="escuela" header="Escuela"></Column>
          <Column field="facultad" header="Facultad"></Column>
          <Column field="area" header="Área"></Column>
        </DataTable>
      </div>

      <!-- PASO 2: Cursos de la Escuela -->
      <div v-if="escuela !== null && cursoseleccionado === null">
        <div class="flex" style="justify-content: space-between;">
          <Button severity="primary" @click="nuevoCurso" style="height:40px">Nuevo Curso</Button>
          <div>
            <div class="flex mb-3" style="justify-content: flex-end;">
              <span class="p-input-icon-left">
                <i class="pi pi-search" />
                <InputText v-model="buscarcurso" style="padding-left: 40px; height: 40px;" placeholder="Buscar curso" />
              </span>
            </div>
          </div>
        </div>

        <div class="mt-3">
          <DataTable
            v-model:selection="cursoseleccionado"
            selectionMode="single"
            :value="cursos"
            :class="'p-datatable-sm'"
            tableStyle="min-width: 50rem"
            style="font-size: .9rem;"
            :paginator="true"
            :rows="9"
          >
            <Column field="nombre" header="Nombre del Curso"></Column>
            <Column field="competencia" header="(Competencia) Curso"></Column>
            <Column field="docente" header="Docente">
              <template #body="{ data }">
                <div class="flex" style="justify-content: flex-start;">
                  <div>{{ data.docente }}</div>
                </div>
              </template>
            </Column>
            <Column field="grupo" header="Grupo"></Column>
            <Column field="periodo" header="Periodo"></Column>

            <!-- Programa y Escuela Profesional -->
            <Column field="programa" header="Programa"></Column>
            <Column field="escuela" header="Escuela Prof."></Column>

            <!-- ✅ CAMBIO: columna 'Lista' con botón de PDF igual que Coordinador
                 🔸 Requiere que en el <script> exista el método:  const descargarPDF = id => window.open(`${base}/generar-pdf/`+id,'_self')
                 Si aún no lo agregaste en tu script, añade esa función para que este botón funcione. -->
            <Column field="estado" header="Lista" style="text-align: center;" width="80px">
              <template #body="{ data }">
                <div class="flex" style="justify-content: center;">
                  <Button
                    :disabled="data.estado !== 1"
                    class="secondary"
                    severity="success"
                    icon="pi pi-print"
                    aria-label="Imprimir"
                    @click.stop="descargarPDF && data.estado === 1 ? descargarPDF(data.id) : null"
                    size="small"
                    style="width: 25px; height: 25px;"
                  />
                </div>
              </template>
            </Column>

            <Column field="estado" header="Estado" width="90px">
              <template #body="{ data }">
                <div class="flex" style="justify-content: center;">
                  <Tag v-if="data.estado === 1" severity="info" value="Activo"></Tag>
                  <Tag v-else :style="{ background: '#CDCDCD' }" value="Inactivo"></Tag>
                </div>
              </template>
            </Column>

            <Column field="id_programa" header="Acciones" width="100px">
              <template #body="{ data }">
                <div class="flex">
                  <div class="mr-2">
                    <Button
                      class="secondary"
                      icon="pi pi-pencil"
                      aria-label="Editar"
                      @click.stop="editar(data)"
                      size="small"
                      style="width: 25px; height: 25px;"
                    />
                  </div>
                  <Button
                    icon="pi pi-trash"
                    severity="danger"
                    aria-label="Eliminar"
                    @click.stop="confirm2($event, data)"
                    size="small"
                    style="width: 25px; height: 25px;"
                  />
                </div>
              </template>
            </Column>
          </DataTable>
        </div>
      </div>

      <!-- PASO 3: Detalle del Curso y Asignaciones -->
      <div v-if="escuela !== null && cursoseleccionado !== null">
        <div class="flex" style="justify-content: space-between;">
          <Button severity="primary" @click="abrirseleccionar()" style="height:40px">Seleccionar Alumnos</Button>
          <div>
            <div class="flex mb-3" style="justify-content: flex-end;">
              <span class="p-input-icon-left">
                <i class="pi pi-search" />
                <InputText v-model="buscar" style="padding-left: 40px; height: 40px;" placeholder="Buscar" />
              </span>
            </div>
          </div>
        </div>

        <div class="mt-3">
          <DataTable
            selectionMode="single"
            :value="detalle_curso"
            :class="'p-datatable-sm'"
            tableStyle="min-width: 50rem"
            style="font-size: .9rem;"
            :paginator="true"
            :rows="9"
          >
            <Column field="codigo_est" header="Código"></Column>

            <!-- ✅ CAMBIO: igual que Coordinador, mostramos 'Ingreso' si tu backend envía 'semestre' -->
            <Column field="semestre" header="Ingreso"></Column>

            <Column field="nombres" header="Nombres"></Column>
            <Column field="paterno" header="Paterno"></Column>
            <Column field="materno" header="Materno"></Column>
            <Column field="curso" header="Curso"></Column>
            <Column field="nota" header="Nota"></Column>

            <Column field="estado" header="Condición" width="100px">
              <template #body="{ data }">
                <div class="flex" style="justify-content: center;">
                  <Tag v-if="data.nota >= 10.5" severity="info" value="Aprobado"></Tag>
                  <Tag v-else severity="danger" value="Desaprobado"></Tag>
                </div>
              </template>
            </Column>
          </DataTable>
        </div>
      </div>

      <!-- Toasts y Confirm -->
      <Toast />
      <ConfirmPopup />

      <!-- MODAL: Crear/Editar Curso -->
      <Dialog v-model:visible="visible" modal :header="!curso.id ? 'Curso nuevo' : 'Editar Curso'" :style="{ width: '500px' }">
        <div class="flex mt-0 mb-3 align-items-center" style="justify-content: flex-end;">
          <label>Estado</label>
          <div class="ml-3"><InputSwitch v-model="curso.estado" /></div>
        </div>

        <div class="flex" style="width: 100%; justify-content: space-between;">
          <div class="mb-3" style="width: 68%;">
            <div><label>Nombre del Curso</label></div>
            <InputText style="width: 100%; height: 40px;" type="text" v-model="curso.nombre" />
          </div>

          <div class="mb-3" style="width: 28%;">
            <div><label>Grupo</label></div>
            <Dropdown
              v-model="curso.grupo"
              :options="grupos"
              optionLabel="label"
              optionValue="value"
              placeholder="Selecciona un grupo"
              style="width:100%;"
              class="w-full md:w-11rem"
            >
              <template #option="slotProps">
                <div
                  class="flex align-items-center"
                  style="font-size:0.9rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;"
                >
                  <div>{{ slotProps.option.label }}</div>
                </div>
              </template>
            </Dropdown>
          </div>
        </div>

        <div class="flex" style="width: 100%; justify-content: space-between;">
          <div class="mb-3" style="width: 100%;">
            <div><label>Competencia</label></div>
            <Dropdown
              v-model="cursocompetencia"
              :options="competencias"
              optionLabel="label"
              optionValue="value"
              placeholder="Seleccione una competencia"
              style="width:100%;"
              class="w-full md:w-11rem"
            >
              <template #option="slotProps">
                <div
                  class="flex align-items-center"
                  style="width: 400px; font-size:0.9rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;"
                >
                  <div>{{ slotProps.option.label }}</div>
                </div>
              </template>
            </Dropdown>
          </div>
        </div>

        <div class="flex" style="width: 100%; justify-content: space-between;">
          <div class="mb-3" style="width: 100%;">
            <div><label>Programa de estudio</label></div>
            <Dropdown
              v-model="prog"
              :options="programas"
              filter
              optionLabel="label"
              optionValue="value"
              placeholder="Seleccione un programa de estudio"
              style="width:100%;"
              class="w-full md:w-11rem"
            >
              <template #option="slotProps">
                <div
                  class="flex align-items-center"
                  style="width: 400px; font-size:0.9rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;"
                >
                  <div>{{ slotProps.option.label }}</div>
                </div>
              </template>
            </Dropdown>
          </div>
        </div>

        <div class="flex" style="width: 100%; justify-content: space-between;">
          <div class="mb-3" style="width: 100%;">
            <div><label>Docente</label></div>
            <Dropdown
              v-model="curso.id_docente"
              :options="docentes2"
              filter
              optionLabel="nombres"
              optionValue="id"
              placeholder="Selecciona un docente"
              style="width:100%;"
              class="w-full md:w-11rem"
            >
              <template #option="slotProps">
                <div
                  class="flex align-items-center"
                  style="width: 400px; font-size:0.9rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;"
                >
                  <div>{{ slotProps.option.nombres }}</div>
                </div>
              </template>
            </Dropdown>
          </div>
        </div>

        <template #footer>
          <div class="flex" style="justify-content: flex-end;">
            <div>
              <Button label="Cancelar" outlined @click="visible = false" size="small" />
            </div>
            <Button label="Guardar" @click="guardar" size="small" />
          </div>
        </template>
      </Dialog>

      <!-- MODAL: Asignar Alumnos -->
      <Dialog v-model:visible="modal_registro" modal header="Asignar Alumnos" :style="{ width: '900px' }">
        <div v-if="alumnosregistro">
          <DataTable
            v-model:selection="alumnos_seleccionados_registro"
            selectionMode="multiple"
            dataKey="id"
            :metaKeySelection="false"
            :row-selection="false"
            :value="alumnosregistro"
            :class="'p-datatable-sm'"
            tableStyle="min-width: 50rem"
            style="font-size: .9rem;"
            :paginator="true"
            :rows="9"
          >
            <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
            <Column field="programa" header="Programa"></Column>
            <Column field="codigo_est" header="Código"></Column>
            <!-- <Column field="dni" header="DNI"></Column> -->
            <Column field="nombres" header="Nombres"></Column>
            <Column field="paterno" header="Paterno"></Column>
            <Column field="materno" header="Materno"></Column>
          </DataTable>
        </div>

        <div class="flex" style="width: 100%; justify-content: flex-end;">
          <Button severity="primary" style="font-size: 0.9rem" text @click="asignar">Asignar</Button>
        </div>
      </Dialog>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
/* ===== Imports ===== */
import AuthenticatedLayout from '@/Layouts/LayoutSuperadmi.vue';
import { Head } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import axios from 'axios';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dialog from 'primevue/dialog';
import Dropdown from 'primevue/dropdown';
import InputSwitch from 'primevue/inputswitch';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';
import ConfirmPopup from 'primevue/confirmpopup';
import Tag from 'primevue/tag';

/* ===== Configuración ===== */
const base = '/superadmi';

/* ===== Helpers PrimeVue ===== */
const toast = useToast();
const confirm = useConfirm();

/* ===== Estado general ===== */
const escuela = ref(null);
const escuelas = ref([]);
const buscarescuela = ref('');
const filters = ref({});

const competencias = ref([]);
const competencia = ref(null);

const cursos = ref([]);
const cursoseleccionado = ref(null);
const buscarcurso = ref('');

const docentes = ref([]);
const docentes2 = ref([]);
const programas = ref([]);
const totalpaginas = ref(0);
const pagina = ref(1);
const buscar = ref('');

/* ===== Modal Curso ===== */
const visible = ref(false);
const cargandoEdicion = ref(false);

const grupos = ref([
  { value: 'A', label: 'Grupo A' },
  { value: 'B', label: 'Grupo B' },
  { value: 'C', label: 'Grupo C' },
  { value: 'D', label: 'Grupo D' },
  { value: 'E', label: 'Grupo E' },
]);

const prog = ref(null);
const cursocompetencia = ref(null);

const curso = ref({
  id: null,
  nombre: '',
  id_docente: '',
  grupo: 'A',
  estado: true,
});

/* ===== Detalle / asignación ===== */
const detalle_curso = ref([]);
const alumnosregistro = ref([]);
const alumnos_seleccionados_registro = ref([]);
const seleccionadosTemp = ref([]);
const diferenciaAB = ref([]);
const diferenciaBA = ref([]);
const modal_registro = ref(false);

/*
 * Normaliza respuestas Laravel que pueden venir como:
 * { datos: [...] }
 * { datos: { data: [...] } }
 * { data: [...] }
 */
const obtenerLista = (payload) => {
  if (Array.isArray(payload?.datos)) return payload.datos;
  if (Array.isArray(payload?.datos?.data)) return payload.datos.data;
  if (Array.isArray(payload?.data)) return payload.data;
  if (Array.isArray(payload)) return payload;
  return [];
};

const mostrarError = (error, titulo = 'Error') => {
  const mensaje =
    error?.response?.data?.mensaje ||
    error?.response?.data?.message ||
    error?.message ||
    'No se pudo completar la operación.';

  toast.add({
    severity: 'error',
    summary: titulo,
    detail: mensaje,
    life: 4000,
  });
};

/* ===== Catálogos ===== */
const getEscuelas = async () => {
  try {
    const res = await axios.post(`${base}/get-escuelas`, {
      term: buscarescuela.value,
    });

    escuelas.value = obtenerLista(res.data);
  } catch (error) {
    escuelas.value = [];
    mostrarError(error, 'No se pudieron cargar las escuelas');
  }
};

const getCompetencias = async () => {
  try {
    const res = await axios.post(`${base}/get-competencias`, {
      term: '',
    });

    // IMPORTANTE: SuperadmiController devuelve un paginate(), por eso
    // debemos tomar datos.data y no el objeto paginator completo.
    competencias.value = obtenerLista(res.data);
  } catch (error) {
    competencias.value = [];
    mostrarError(error, 'No se pudieron cargar las competencias');
  }
};

const getProgramas = async () => {
  try {
    const res = await axios.post(`${base}/get-programas?page=${pagina.value}`, {
      term: '',
    });

    programas.value = obtenerLista(res.data);
  } catch (error) {
    programas.value = [];
    mostrarError(error, 'No se pudieron cargar los programas');
  }
};

const getDocentes = async () => {
  try {
    const res = await axios.post(`${base}/get-docentes?page=${pagina.value}`, {
      term: buscar.value,
    });

    docentes.value = obtenerLista(res.data);
    totalpaginas.value = res.data?.datos?.total ?? res.data?.total ?? 0;
  } catch (error) {
    docentes.value = [];
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

/* ===== Cursos ===== */
const getCursos = async () => {
  if (!escuela.value) {
    cursos.value = [];
    return;
  }

  try {
    const res = await axios.post(`${base}/get-cursos`, {
      term: buscarcurso.value,
      competencia: competencia.value,
      escuela: escuela.value?.escuela ?? '',
    });

    cursos.value = obtenerLista(res.data);
  } catch (error) {
    cursos.value = [];
    mostrarError(error, 'No se pudieron cargar los cursos');
  }
};

/* ===== Nuevo Curso ===== */
const nuevoCurso = async () => {
  limpiar();

  // Garantiza que la modal abra con Competencia y Programa cargados.
  await Promise.all([
    getCompetencias(),
    getProgramas(),
  ]);

  visible.value = true;
};

/* ===== Editar Curso ===== */
const editar = async (item) => {
  limpiar();
  cargandoEdicion.value = true;

  try {
    // Primero cargamos los catálogos para que los Dropdown tengan opciones.
    await Promise.all([
      getCompetencias(),
      getProgramas(),
    ]);

    curso.value.id = item.id;
    curso.value.nombre = item.nombre ?? '';
    curso.value.grupo = item.grupo ?? 'A';
    curso.value.estado = Number(item.estado) === 1;

    // Programa guardado en el curso.
    prog.value = item.id_programa ?? null;

    // Competencia guardada en el curso.
    cursocompetencia.value = item.id_competencia ?? null;

    // Luego cargamos los docentes de ESA competencia.
    await getDocenteXcompetencia();

    // Finalmente seleccionamos el docente guardado.
    curso.value.id_docente = item.id_docente ?? '';

    visible.value = true;
  } catch (error) {
    mostrarError(error, 'No se pudo cargar el curso para editar');
  } finally {
    cargandoEdicion.value = false;
  }
};

const saveCurso = async () => {
  if (!curso.value.nombre) {
    showToast('warn', 'Falta información', 'Ingrese el nombre del curso.');
    return;
  }

  if (!cursocompetencia.value) {
    showToast('warn', 'Falta información', 'Seleccione una competencia.');
    return;
  }

  if (!prog.value) {
    showToast('warn', 'Falta información', 'Seleccione un programa de estudio.');
    return;
  }

  if (!curso.value.id_docente) {
    showToast('warn', 'Falta información', 'Seleccione un docente.');
    return;
  }

  try {
    const res = await axios.post(`${base}/save-curso`, {
      id: curso.value.id,
      nombre: curso.value.nombre,
      id_competencia: cursocompetencia.value,
      id_docente: curso.value.id_docente,
      escuela: escuela.value?.escuela ?? '',
      grupo: curso.value.grupo,
      estado: curso.value.estado,
      id_programa: prog.value,
    });

    showToast(
      res.data?.tipo,
      res.data?.titulo,
      res.data?.mensaje
    );

    await getCursos();
    visible.value = false;
    limpiar();
  } catch (error) {
    mostrarError(error, 'No se pudo guardar el curso');
  }
};

/* ===== Detalle del Curso ===== */
const getDetalleCurso = async () => {
  if (!cursoseleccionado.value?.id) {
    detalle_curso.value = [];
    return;
  }

  try {
    const res = await axios.post(`${base}/get-detalle-curso`, {
      term: '',
      curso: cursoseleccionado.value.id,
    });

    detalle_curso.value = obtenerLista(res.data);
    alumnos_seleccionados_registro.value = obtenerLista({
      datos: res.data?.registrados,
    });
    seleccionadosTemp.value = [...alumnos_seleccionados_registro.value];
  } catch (error) {
    detalle_curso.value = [];
    alumnos_seleccionados_registro.value = [];
    seleccionadosTemp.value = [];
    mostrarError(error, 'No se pudo cargar el detalle del curso');
  }
};

/* ===== Alumnos ===== */
const getAlumnosRegistros = async () => {
  if (!escuela.value?.id || !cursoseleccionado.value?.id_competencia) {
    alumnosregistro.value = [];
    return;
  }

  try {
    const res = await axios.post(`${base}/get-alumnos-registro`, {
      term: '',
      escuela: escuela.value.id,
      curso: cursoseleccionado.value.id_competencia,
    });

    alumnosregistro.value = obtenerLista(res.data);
  } catch (error) {
    alumnosregistro.value = [];
    mostrarError(error, 'No se pudieron cargar los alumnos');
  }
};

const abrirseleccionar = async () => {
  await getAlumnosRegistros();
  modal_registro.value = true;
};

const compararArrays = () => {
  diferenciaAB.value = alumnos_seleccionados_registro.value.filter((a) =>
    !seleccionadosTemp.value.some((b) => b.id === a.id)
  );
};

const compararArrays2 = () => {
  diferenciaBA.value = seleccionadosTemp.value.filter((a) =>
    !alumnos_seleccionados_registro.value.some((b) => b.id === a.id)
  );
};

const asignarCursoNivelacion = async () => {
  compararArrays();
  compararArrays2();

  try {
    const res = await axios.post(`${base}/asignar-curso-nivelacion`, {
      curso: cursoseleccionado.value?.id,
      alumnos: alumnos_seleccionados_registro.value,
      anteriores: seleccionadosTemp.value,
      diferencia: diferenciaAB.value,
      diferencia2: diferenciaBA.value,
    });

    showToast(
      res.data?.tipo,
      res.data?.titulo,
      res.data?.mensaje
    );

    await getDetalleCurso();
    modal_registro.value = false;
  } catch (error) {
    mostrarError(error, 'No se pudo realizar la asignación');
  }
};

/* ===== Eliminar ===== */
const deleteCurso = async (id) => {
  try {
    const res = await axios.get(`${base}/delete-curso/${id}`);

    showToast(
      res.data?.tipo,
      res.data?.titulo,
      res.data?.mensaje
    );

    await getCursos();
  } catch (error) {
    mostrarError(error, 'No se pudo eliminar el curso');
  }
};

const confirm2 = (event, item) => {
  confirm.require({
    target: event.currentTarget,
    message: `¿Está seguro de eliminar el curso ${item?.nombre ?? ''}?`,
    icon: 'pi pi-info-circle',
    acceptClass: 'p-button-danger',
    accept: () => deleteCurso(item.id),
    reject: () => {
      toast.add({
        severity: 'info',
        summary: 'Eliminación cancelada',
        detail: 'No se eliminó el curso.',
        life: 3000,
      });
    },
  });
};

/* ===== PDF ===== */
const descargarPDF = (id) => {
  window.open(`${base}/generar-pdf/${id}`, '_self');
};

/* ===== Utilidades ===== */
const limpiar = () => {
  cursocompetencia.value = null;
  docentes2.value = [];
  prog.value = null;

  curso.value = {
    id: null,
    nombre: '',
    id_docente: '',
    grupo: 'A',
    estado: true,
  };
};

const showToast = (tipo, titulo, detalle) => {
  toast.add({
    severity: tipo || 'info',
    summary: titulo || '',
    detail: detalle || '',
    life: 3000,
  });
};

const Inicio = () => {
  escuela.value = null;
  cursoseleccionado.value = null;
  competencia.value = null;
  cursos.value = [];
  detalle_curso.value = [];
};

const resEsuela = () => {
  cursoseleccionado.value = null;
  detalle_curso.value = [];
  alumnosregistro.value = [];
};

/* ===== Watchers ===== */
watch(visible, (newValue) => {
  if (newValue === false) {
    limpiar();
  }
});

watch(escuela, async (newValue) => {
  cursoseleccionado.value = null;
  detalle_curso.value = [];
  alumnosregistro.value = [];
  alumnos_seleccionados_registro.value = [];
  seleccionadosTemp.value = [];
  competencia.value = null;

  if (newValue) {
    await Promise.all([
      getCursos(),
      getProgramas(),
    ]);
  }
});

watch(buscarescuela, () => {
  getEscuelas();
});

watch(buscarcurso, () => {
  getCursos();
});

watch(buscar, () => {
  getDocentes();
});

watch(competencia, () => {
  getCursos();
});

watch(cursocompetencia, async (newValue) => {
  // En Editar cargamos manualmente y evitamos duplicar la petición.
  if (cargandoEdicion.value) return;

  curso.value.id_docente = '';
  docentes2.value = [];

  if (!newValue) {
    if (!curso.value.id) {
      curso.value.nombre = '';
    }
    return;
  }

  await getDocenteXcompetencia();

  // Igual que el módulo Coordinador: al crear, el nombre del curso
  // se completa con el nombre de la competencia seleccionada.
  if (!curso.value.id) {
    const comp = competencias.value.find(
      (item) => item.value === newValue
    );

    if (comp) {
      curso.value.nombre = comp.label;
    }
  }
});

watch(cursoseleccionado, async (newValue) => {
  if (!newValue) return;

  await Promise.all([
    getDetalleCurso(),
    getAlumnosRegistros(),
  ]);
});

/* ===== Inicio ===== */
const iniciar = async () => {
  await Promise.all([
    getEscuelas(),
    getCompetencias(),
    getProgramas(),
  ]);
};

iniciar();

/* ===== Alias usados por el template ===== */
const guardar = saveCurso;
const asignar = asignarCursoNivelacion;
</script>
