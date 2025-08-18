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
          <Button severity="primary" @click="visible = true" style="height:40px">Nuevo Curso</Button>
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

            <!-- ✅ CAMBIO: como en Coordinador, mostramos Programa y Escuela Prof. si tu API los devuelve -->
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
                    @click="descargarPDF && data.estado === 1 ? descargarPDF(data.id) : null"
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
                      @click="editar(data)"
                      size="small"
                      style="width: 25px; height: 25px;"
                    />
                  </div>
                  <Button
                    icon="pi pi-trash"
                    severity="danger"
                    aria-label="Eliminar"
                    @click="confirm2($event, data)"
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
              :options="programasselect"
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
import { useToast } from "primevue/usetoast";
import { useConfirm } from "primevue/useconfirm";
import ConfirmPopup from 'primevue/confirmpopup';
import Tag from 'primevue/tag';

/* ===== Config ===== */
const base = '/superadmi'; // todas las rutas pegadas a SuperadmiController

/* ===== State ===== */
// UI helpers
const toast = useToast();
const confirm = useConfirm();

// Tabla escuelas
const escuela = ref(null);
const escuelas = ref([]);
const buscarescuela = ref("");
const filters = ref({}); // para :filters en DataTable (opcional)

// Filtros/combos
const competencias = ref([]);
const competencia = ref(null);

// Cursos
const cursos = ref([]);
const cursoseleccionado = ref(null);
const buscarcurso = ref("");

// Docentes / Programas
const docentes = ref([]);
const docentes2 = ref([]);
const programas = ref([]);
const totalpaginas = ref(0);
const pagina = ref(1);
const buscar = ref("");

// Modal Curso
const visible = ref(false);
const grupos = ref([
  { value: "A", label: "Grupo A" },
  { value: "B", label: "Grupo B" },
  { value: "C", label: "Grupo C" },
  { value: "D", label: "Grupo D" },
  { value: "E", label: "Grupo E" },
]);
const prog = ref(null);
// Lista fija como en tu ejemplo (si luego quieres, poblas desde backend)
const programasselect = ref([
  {value:1, label:"INGENIERIA AGRONOMICA"},
  {value:2, label:"INGENIERIA AGROINDUSTRIAL"},
  {value:3, label:"INGENIERIA TOPOGRAFICA Y AGRIMENSURA"},
  {value:4, label:"MEDICINA VETERINARIA Y ZOOTECNIA"},
  {value:5, label:"INGENIERIA ECONOMICA"},
  {value:6, label:"CIENCIAS CONTABLES"},
  {value:7, label:"ADMINISTRACION"},
  {value:8, label:"TRABAJO SOCIAL"},
  {value:9, label:"ENFERMERIA"},
  {value:10, label:"INGENIERIA DE MINAS"},
  {value:11, label:"HUMANIDADES"},
  {value:12, label:"SOCIOLOGIA"},
  {value:13, label:"TURISMO"},
  {value:14, label:"ANTROPOLOGIA"},
  {value:15, label:"CIENCIAS DE LA COMUNICACION SOCIAL"},
  {value:16, label:"ARTE: ARTES PLASTICAS"},
  {value:17, label:"ARTE: MUSICA"},
  {value:18, label:"ARTE: DANZA"},
  {value:19, label:"BIOLOGIA: ECOLOGIA"},
  {value:20, label:"BIOLOGIA: MICROBIOLOGIA Y LABORATORIO CLINICO"},
  {value:21, label:"BIOLOGIA: PESQUERIA"},
  {value:22, label:"EDUC. SEC.: CIENCIA, TECNOLOGIA Y AMBIENTE"},
  {value:23, label:"EDUC. SEC.: CIENCIAS SOCIALES"},
  {value:24, label:"EDUC. SEC.: LIT. PSICOLOGIA Y FILOSOFIA"},
  {value:25, label:"EDUC. SEC.: MATEMATICA, FISICA, COMP. E INFORMATICA"},
  {value:26, label:"EDUCACION PRIMARIA"},
  {value:27, label:"EDUCACION INICIAL"},
  {value:28, label:"EDUCACION FISICA"},
  {value:29, label:"INGENIERIA ESTADISTICA E INFORMATICA"},
  {value:30, label:"DERECHO"},
  {value:31, label:"INGENIERIA QUIMICA"},
  {value:32, label:"ODONTOLOGIA"},
  {value:33, label:"NUTRICION HUMANA"},
  {value:34, label:"INGENIERIA GEOLOGICA"},
  {value:35, label:"INGENIERIA METALURGICA"},
  {value:36, label:"INGENIERIA CIVIL"},
  {value:37, label:"ARQUITECTURA Y URBANISMO"},
  {value:38, label:"CIENCIAS FISICO MATEMATICAS: FISICA"},
  {value:39, label:"CIENCIAS FISICO MATEMATICAS: MATEMATICAS "},
  {value:40, label:"INGENIERIA AGRICOLA"},
  {value:41, label:"MEDICINA HUMANA"},
  {value:42, label:"INGENIERIA MECANICA ELECTRICA"},
  {value:43, label:"INGENIERIA ELECTRONICA"},
  {value:44, label:"INGENIERIA DE SISTEMAS"},
]);

// Detalle / Asignaciones
const detalle_curso = ref([]);
const alumnosregistro = ref([]);
const alumnos_seleccionados_registro = ref([]);
const seleccionadosTemp = ref([]);
const diferenciaAB = ref([]);
const diferenciaBA = ref([]);
const modal_registro = ref(false);

// Model del modal curso
const cursocompetencia = ref(null);
const curso = ref({
  id: null,
  nombre: "",
  id_docente: "",
  grupo: "A",
  estado: true
});

/* ===== API Calls (SuperadmiController) ===== */

// Escuelas con filial/ubicación/facultad/área
const getEscuelas = async () => {
  const res = await axios.post(`${base}/get-escuelas`, { term: buscarescuela.value });
  // espera estructura { datos: { data: [...] } } o { data: [...] }
  escuelas.value = res.data?.datos?.data ?? res.data?.data ?? [];
};

// Catálogos
const getCompetencias = async () => {
  const res = await axios.post(`${base}/get-competencias`, { term: "" });
  competencias.value = res.data?.datos ?? res.data ?? [];
};

const getProgramas = async () => {
  const res = await axios.post(`${base}/get-programas?page=${pagina.value}`, { term: "" });
  programas.value = res.data?.datos?.data ?? res.data?.data ?? [];
};

// Docentes
const getDocentes = async () => {
  const res = await axios.post(`${base}/get-docentes?page=${pagina.value}`, { term: buscar.value });
  docentes.value = res.data?.datos?.data ?? res.data?.data ?? [];
  totalpaginas.value = res.data?.datos?.total ?? res.data?.total ?? 0;
};

const getDocenteXcompetencia = async () => {
  if (!cursocompetencia.value) return;
  const res = await axios.post(`${base}/get-docente-competencia`, { term: "", competencia: cursocompetencia.value });
  docentes2.value = res.data?.datos?.data ?? res.data?.data ?? [];
};

// Cursos
const getCursos = async () => {
  if (!escuela.value) return; // guard
  const payload = {
    term: buscarcurso.value,
    competencia: competencia.value,
    escuela: escuela.value?.escuela ?? ''
  };
  const res = await axios.post(`${base}/get-cursos`, payload);
  cursos.value = res.data?.datos?.data ?? res.data?.data ?? [];
};

const saveCurso = async () => {
  const res = await axios.post(`${base}/save-curso`, {
    id: curso.value.id,
    nombre: curso.value.nombre,
    id_competencia: cursocompetencia.value,
    id_docente: curso.value.id_docente,
    escuela: escuela.value?.escuela ?? '',
    grupo: curso.value.grupo,
    estado: curso.value.estado,
    id_programa: prog.value
  });
  showToast(res.data?.tipo, res.data?.titulo, res.data?.mensaje);
  await getCursos();
  visible.value = false;
  limpiar();
};

const getDetalleCurso = async () => {
  if (!cursoseleccionado.value?.id) return;
  const res = await axios.post(`${base}/get-detalle-curso`, { term: "", curso: cursoseleccionado.value.id });
  detalle_curso.value = res.data?.datos?.data ?? res.data?.data ?? [];
  alumnos_seleccionados_registro.value = res.data?.registrados?.data ?? [];
  seleccionadosTemp.value = res.data?.registrados?.data ?? [];
};

const asignarCursoNivelacion = async () => {
  compararArrays();
  compararArrays2();
  const res = await axios.post(`${base}/asignar-curso-nivelacion`, {
    curso: cursoseleccionado.value?.id,
    alumnos: alumnos_seleccionados_registro.value,
    anteriores: seleccionadosTemp.value,
    diferencia: diferenciaAB.value,
    diferencia2: diferenciaBA.value
  });
  showToast(res.data?.tipo, res.data?.titulo, res.data?.mensaje);
  await getDetalleCurso();
  modal_registro.value = false;
};

const deleteCurso = async (id) => {
  const res = await axios.get(`${base}/delete-curso/${id}`);
  showToast(res.data?.tipo, res.data?.titulo, res.data?.mensaje);
  await getCursos();
};

// PDF Lista
const descargarPDF = (id) => {
  window.open(`${base}/generar-pdf/` + id, '_self');
};

// Alumnos para asignación
const getAlumnosRegistros = async () => {
  if (!escuela.value?.id || !cursoseleccionado.value?.id_competencia) return;
  const res = await axios.post(`${base}/get-alumnos-registro`, {
    term: "",
    escuela: escuela.value.id,
    curso: cursoseleccionado.value.id_competencia
  });
  alumnosregistro.value = res.data?.datos ?? res.data ?? [];
};

/* ===== Helpers ===== */
const editar = (item) => {
  limpiar();
  visible.value = true;
  curso.value.id = item.id;
  curso.value.nombre = item.nombre;
  curso.value.grupo = item.grupo;
  curso.value.id_docente = item.id_docente;
  cursocompetencia.value = item.id_competencia;
  curso.value.estado = (item.estado === 1);
};

const abrirseleccionar = () => { modal_registro.value = true; };

const limpiar = () => {
  cursocompetencia.value = null;
  curso.value.id = null;
  curso.value.nombre = "";
  curso.value.id_docente = "";
  curso.value.grupo = "A";
  curso.value.estado = true;
  prog.value = null;
};

const compararArrays = () => {
  diferenciaAB.value = alumnos_seleccionados_registro.value.filter(a =>
    !seleccionadosTemp.value.some(b => b.id === a.id)
  );
};

const compararArrays2 = () => {
  diferenciaBA.value = seleccionadosTemp.value.filter(a =>
    !alumnos_seleccionados_registro.value.some(b => b.id === a.id)
  );
};

const showToast = (tipo, titulo, detalle) => {
  toast.add({ severity: tipo || 'info', summary: titulo || '', detail: detalle || '', life: 3000 });
};

const confirm2 = (event, doc) => {
  confirm.require({
    target: event.currentTarget,
    message: '¿Estas seguro de eliminar el curso ' + (doc?.nombre ?? '') + '?',
    icon: 'pi pi-info-circle',
    acceptClass: 'p-button-danger',
    accept: () => { deleteCurso(doc.id); },
    reject: () => {
      toast.add({ severity: 'error', summary: 'Eliminación cancelada', detail: 'Se ha cancelado la eliminación', life: 3000 });
    }
  });
};

const Inicio = () => { escuela.value = null; cursoseleccionado.value = null; };
const resEsuela = () => { cursoseleccionado.value = null; };

/* ===== Watchers ===== */
watch(visible, (nv) => {
  if (nv === false) limpiar();
});

watch(escuela, (val) => {
  if (val) {
    cursoseleccionado.value = null;
    cursos.value = null;
    detalle_curso.value = null;
    alumnos_seleccionados_registro.value = null;
    seleccionadosTemp.value = null;
    getCursos(); // refresca cursos al elegir escuela
  }
});

watch(buscarescuela, () => { getEscuelas(); });
watch(buscarcurso,   () => { getCursos(); });
watch(buscar,        () => { getDocentes(); });
watch(competencia,   () => { getCursos(); });
watch(cursocompetencia, () => { getDocenteXcompetencia(); });

watch(cursoseleccionado, () => {
  if (cursoseleccionado.value) {
    curso.value = cursoseleccionado.value;
    getDetalleCurso();
    getAlumnosRegistros();
  }
});

/* ===== Init ===== */
getEscuelas();     // lista escuelas (con filial/ubicación/facultad/área)
getCompetencias();
getDocentes();
getProgramas();

/* ===== Expose (usados en el template) ===== */
const guardar = saveCurso;
const asignar = asignarCursoNivelacion;

</script>
