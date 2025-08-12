<template>
  <Head title="Documentos" />
  <AuthenticatedLayout>
    <div class="flex justify-between items-start">
      <ConfirmDialog />

      <!-- Filtros (izquierda) -->
      <div class="flex flex-wrap items-start" style="gap: 1rem;">
        <!-- Escuela profesional -->
        <div class="mb-3" style="width: 260px;">
          <Dropdown v-model="programa" :options="programasselect" filter optionLabel="label" optionValue="value"
                    placeholder="Seleccione escuela profesional" style="width:100%;">
            <template #option="slotProps">
              <div class="flex align-items-center" style="width: 420px; font-size:0.9rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                <div>{{ slotProps.option.label }}</div>
              </div>
            </template>
          </Dropdown>
        </div>

        <!-- Periodo (desde BD) -->
        <div class="mb-3" style="width: 200px;">
          <Dropdown v-model="periodo" :options="periodosselect" filter optionLabel="label" optionValue="value"
                    placeholder="Periodo" style="width:100%;" :loading="loadingPeriodos" />
        </div>

        <!-- Tipo -->
        <div class="mb-3" style="width: 200px;">
          <Dropdown v-model="tipo" :options="tiposselect" filter optionLabel="label" optionValue="value"
                    placeholder="Tipo de documento" style="width:100%;" />
        </div>

        <!-- Aceptado -->
        <div class="mb-3" style="width: 220px;">
          <Dropdown v-model="aceptado" :options="aceptadoselect" filter optionLabel="label" optionValue="value"
                    placeholder="Estado (todos)" style="width:100%;" />
        </div>

        <!-- Limpiar -->
        <Button label="Limpiar filtros" icon="pi pi-filter-slash" class="p-button-secondary" @click="limpiarFiltros" />
      </div>

      <!-- Búsqueda + Export (derecha) -->
      <div class="flex items-center" style="gap:.5rem;">
        <span class="p-input-icon-left">
          <i class="pi pi-search" />
          <InputText v-model="buscar" placeholder="Buscar" />
        </span>
        <Button icon="pi pi-file-excel" label="Exportar Excel" severity="success" :loading="exportLoading" @click="exportarExcel" />
      </div>
    </div>

    <div class="mt-3">
      <DataTable
        :value="documentos"
        :showGridlines="false"
        paginator
        lazy
        :rows="pageSize"
        :totalRecords="totalRegistros"
        :rowsPerPageOptions="[10,20,50]"
        :first="(pagina-1)*pageSize"
        @page="onPage"
        class="p-datatable-sm"
        tableStyle="min-width: 50rem"
        style="font-size:.9rem;"
      >
        <Column field="periodo" header="Periodo" />
        <Column field="periodo" header="Periodo Mod" width="170px">
          <template #body="{ data }">
            <Dropdown
              v-model="data.periodo"
              :options="periodosselect"
              optionLabel="label"
              optionValue="value"
              placeholder="Seleccione periodo"
              @change="cambiarPeriodo(data)"
              style="width:100%;"
            />
          </template>
        </Column>

        <Column field="tipo" header="TIPO" />
        <Column field="escuela" header="ESCUELA PROFESIONAL" />
        <Column field="fecha_subida" header="FECHA" />
        <Column field="username" header="USUARIO">
          <template #body="{ data }">
            {{ data.username }} {{ data.userlastname }}
          </template>
        </Column>

        <Column field="url" header="Ver Documento" style="min-width: 12rem">
          <template #body="{ data }">
            <Button label="Link" @click="abrirDocumento(data.url)" link>
              <i class="pi pi-download" style="color: green"></i>
            </Button>
          </template>
        </Column>

        <Column field="aceptado" header="Estado" width="140px">
          <template #body="{ data }">
            <Tag
              v-if="data.aceptado === 1"
              value="Aceptado"
              severity="success"
              class="cursor-pointer"
              @click="editar(data)"
            />
            <Tag
              v-else-if="data.aceptado === 0"
              value="Rechazado"
              severity="danger"
              class="cursor-pointer"
              @click="editar(data)"
            />
            <Tag
              v-else
              value="Pendiente"
              severity="secondary"
              class="cursor-pointer"
              @click="editar(data)"
            />
          </template>
        </Column>

        <Column header="Observaciones" style="min-width:12rem">
          <template #body="{ data }">
            <div class="line-clamp-2">{{ data.obser }}</div>
          </template>
        </Column>

        <Column header="Acciones" width="120px">
          <template #body="{ data }">
            <div class="flex gap-2">
              <!-- Botón editar (abre modal estado+observación) -->
              <Button class="secondary" icon="pi pi-pencil" @click="editar(data)" size="small" style="width: 32px; height: 32px;" />
              <!-- Acceso rápido: marcar aceptado o rechazado directamente -->
              <Button icon="pi pi-check" severity="success" @click="cambiarEstadoExplicito(data, 1)" size="small" style="width: 32px; height: 32px;" />
              <Button icon="pi pi-times" severity="danger" @click="cambiarEstadoExplicito(data, 0)" size="small" style="width: 32px; height: 32px;" />
            </div>
          </template>
        </Column>
      </DataTable>
    </div>

    <!-- MODAL: Estado + Observación -->
    <Dialog v-model:visible="modaleditar" modal header="Actualizar documento" :style="{ width: '28rem' }">
      <div class="w-full flex flex-col gap-3">
        <!-- Estado -->
        <div>
          <label class="block mb-1 text-sm text-gray-600">Estado del documento</label>
          <Dropdown
            v-model="archivo.aceptado"
            :options="aceptadoselect"
            optionLabel="label"
            optionValue="value"
            placeholder="Selecciona estado"
            style="width: 100%;"
          />
        </div>

        <!-- Observación -->
        <div>
          <label class="block mb-1 text-sm text-gray-600">Observación</label>
          <Textarea v-model="archivo.obser" style="width: 100%;" variant="filled" rows="6" />
        </div>
      </div>

      <template #footer>
        <div class="flex justify-end gap-2">
          <Button label="Cancelar" @click="modaleditar=false" severity="secondary" size="small" />
          <Button icon="pi pi-save" label="Guardar" severity="success" @click="guardarDesdeModal" size="small" />
        </div>
      </template>
    </Dialog>

    <Toast/>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/LayoutSuperadmi.vue';
import { Head } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { ref, watch, onMounted } from 'vue';
import InputText from 'primevue/inputtext';
import 'primeicons/primeicons.css';
import Dropdown from 'primevue/dropdown';
import Dialog from 'primevue/dialog';
import Textarea from 'primevue/textarea';
import Button from 'primevue/button';
import { useConfirm } from 'primevue/useconfirm';
import ConfirmDialog from 'primevue/confirmdialog';
import Toast from 'primevue/toast';
import { useToast } from "primevue/usetoast";
import Tag from 'primevue/tag';
import axios from 'axios';

// SheetJS (xlsx)
let XLSX = null;

const toast = useToast();
const showToast = (tipo, titulo, detalle) => {
  toast.add({ severity: tipo, summary: titulo, detail: detalle, life: 3000 });
};

const modaleditar = ref(false);
const confirm = useConfirm();

const pagina = ref(1);
const pageSize = ref(10);
const totalRegistros = ref(0);

const buscar = ref(null);
const programa = ref(null);
const periodo = ref(null);
const tipo = ref(null);
// undefined => NO filtra; null => solo Pendiente; 1/0 => aceptado/rechazado
const aceptado = ref(undefined);

const exportLoading = ref(false);
const loadingPeriodos = ref(false);
const periodosselect = ref([]); // se carga desde /periodos

const documentos = ref([]);
const archivo = ref({}); // { id, aceptado, obser }

// Opciones estáticas
const tiposselect = ref([
  { value: 'Dictantes', label: 'Dictantes' },
  { value: 'Informe', label: 'Informe' },
  { value: 'Plan', label: 'Plan' },
  { value: 'Resolucion', label: 'Resolución' },
  { value: 'Otro', label: 'Otro' }
]);

const aceptadoselect = ref([
  { value: 1,    label: 'Aceptado' },
  { value: 0,    label: 'Rechazado' },
  { value: null, label: 'Pendiente' }
]);

const programasselect = ref([
  { value: 1, label: "INGENIERIA AGRONOMICA - PUNO" },
  { value: 2, label: "INGENIERIA AGROINDUSTRIAL - PUNO" },
  { value: 3, label: "INGENIERIA TOPOGRAFICA Y AGRIMENSURA - PUNO" },
  { value: 4, label: "MEDICINA VETERINARIA Y ZOOTECNIA - PUNO" },
  { value: 5, label: "INGENIERIA ECONOMICA - PUNO" },
  { value: 6, label: "CIENCIAS CONTABLES - PUNO" },
  { value: 7, label: "ADMINISTRACION - PUNO" },
  { value: 8, label: "TRABAJO SOCIAL - PUNO" },
  { value: 9, label: "ENFERMERIA - PUNO" },
  { value: 10, label: "INGENIERIA DE MINAS - PUNO" },
  { value: 11, label: "HUMANIDADES - PUNO" },
  { value: 12, label: "SOCIOLOGIA - PUNO" },
  { value: 13, label: "TURISMO - PUNO" },
  { value: 14, label: "ANTROPOLOGIA - PUNO" },
  { value: 15, label: "CIENCIAS DE LA COMUNICACION SOCIAL - PUNO" },
  { value: 16, label: "ARTE - PUNO" },
  { value: 17, label: "BIOLOGIA - PUNO" },
  { value: 18, label: "EDUCACION SECUNDARIA - PUNO" },
  { value: 19, label: "EDUCACION PRIMARIA - PUNO" },
  { value: 20, label: "EDUCACION INICIAL - PUNO" },
  { value: 21, label: "EDUCACION FISICA - PUNO" },
  { value: 22, label: "INGENIERIA ESTADISTICA E INFORMATICA - PUNO" },
  { value: 23, label: "DERECHO - PUNO" },
  { value: 24, label: "INGENIERIA QUIMICA - PUNO" },
  { value: 25, label: "ODONTOLOGIA - PUNO" },
  { value: 26, label: "NUTRICION HUMANA - PUNO" },
  { value: 27, label: "INGENIERIA GEOLOGICA - PUNO" },
  { value: 28, label: "INGENIERIA METALURGICA - PUNO" },
  { value: 29, label: "INGENIERIA CIVIL - PUNO" },
  { value: 30, label: "ARQUITECTURA Y URBANISMO - PUNO" },
  { value: 31, label: "CIENCIAS FISICO MATEMATICAS - PUNO" },
  { value: 32, label: "INGENIERIA AGRICOLA - PUNO" },
  { value: 33, label: "MEDICINA HUMANA - PUNO" },
  { value: 34, label: "INGENIERIA MECANICA ELECTRICA - PUNO" },
  { value: 35, label: "INGENIERIA ELECTRONICA - PUNO" },
  { value: 36, label: "INGENIERIA DE SISTEMAS - PUNO" },
  { value: 37, label: "PSICOLOGIA - PUNO" },
  { value: 38, label: "INGENIERIA ECONOMICA - AZANGARO" },
  { value: 39, label: "INGENIERIA DE MINAS - AZANGARO" },
  { value: 40, label: "INGENIERIA TELECOMUNICACIONES - AZANGARO" },
  { value: 41, label: "CIENCIAS CONTABLES - JULI" },
  { value: 42, label: "ARQUITECTURA Y URBANISMO - JULI" },
  { value: 43, label: "INGENIERIA AGROINDUSTRIAL - JULI" }
]);

const getPeriodos = async () => {
  loadingPeriodos.value = true;
  try {
    const { data } = await axios.get('periodos');
    periodosselect.value = data.options || [];
  } finally {
    loadingPeriodos.value = false;
  }
};

const getDocumentos = async () => {
  const params = {
    term: buscar.value,
    paginashoja: pageSize.value,
    programa: programa.value ?? undefined,
    periodo: periodo.value ?? undefined,
    tipo: tipo.value ?? undefined,
  };
  if (aceptado.value !== undefined) {
    params.aceptado = aceptado.value; // 1, 0 o null
  }

  const url = `get-documentos?page=${pagina.value}`;
  const res = await axios.post(url, params);
  documentos.value = res.data.datos.data;
  totalRegistros.value = res.data.datos.total;
};

const onPage = (event) => {
  pageSize.value = event.rows;
  pagina.value = event.page + 1;
  getDocumentos();
};

const abrirDocumento = (url) => {
  window.open("../" + url, "_blank");
};

// Abre modal con estado+observación actual
const editar = (item) => {
  modaleditar.value = true;
  archivo.value = {
    id: item.id,
    obser: item.obser ?? '',
    aceptado: item.aceptado === 0 ? 0 : (item.aceptado === 1 ? 1 : null),
  };
};

// Guardar desde modal: observación + estado explícito
const guardarDesdeModal = async () => {
  const payload = {
    id: archivo.value.id,
    obser: archivo.value.obser ?? '',
    aceptado: archivo.value.aceptado === null ? 'null' : archivo.value.aceptado
  };

  await axios.post("observar-documento", payload);
  modaleditar.value = false;
  archivo.value = {};
  await getDocumentos();

  const etiqueta = payload.aceptado === 'null' ? 'Pendiente'
                  : payload.aceptado === 1 ? 'Aceptado'
                  : payload.aceptado === 0 ? 'Rechazado' : 'Actualizado';
  showToast('success', `Documento ${etiqueta}`, 'Cambios guardados.');
};

// Acceso rápido: cambiar estado explícito sin abrir modal
const cambiarEstadoExplicito = async (item, nuevoEstado) => {
  await axios.post("cambiar-estado-documento", {
    id: item.id,
    aceptado: nuevoEstado
  });
  await getDocumentos();
  showToast('info', `Estado: ${nuevoEstado === 1 ? 'Aceptado' : 'Rechazado'}`, '');
};

const cambiarPeriodo = (item) => {
  confirm.require({
    message: '¿Está seguro de cambiar el período?',
    header: 'Confirmar cambio de período',
    icon: 'pi pi-exclamation-triangle',
    accept: async () => {
      try {
        await axios.post('cambiar-periodo-documento', { id: item.id, periodo: item.periodo });
        getDocumentos();
        showToast('info', 'Periodo modificado', '');
      } catch (error) {
        showToast('error', 'ERROR', 'No se pudo cambiar el periodo.');
      }
    },
    reject: () => {
      showToast('info', 'Cambio cancelado', 'No se realizó ningún cambio.');
    }
  });
};

const limpiarFiltros = () => {
  programa.value = null;
  periodo.value = null;
  tipo.value = null;
  aceptado.value = undefined; // vuelve a "no filtra"
  buscar.value = null;
  pagina.value = 1;
  getDocumentos();
};

// Export Excel en el front con SheetJS
const exportarExcel = async () => {
  try {
    exportLoading.value = true;
    if (!XLSX) {
      XLSX = await import('xlsx');
    }

    // Traer TODOS los registros filtrados (sin paginación)
    const params = {
      term: buscar.value ?? undefined,
      paginashoja: 100000,
      programa: programa.value ?? undefined,
      periodo: periodo.value ?? undefined,
      tipo: tipo.value ?? undefined,
    };
    if (aceptado.value !== undefined) {
      params.aceptado = aceptado.value;
    }

    const res = await axios.post('get-documentos?page=1', params);
    const rows = res.data?.datos?.data ?? [];

    if (!rows.length) {
      showToast('warn', 'Sin datos', 'No hay registros para exportar.');
      exportLoading.value = false;
      return;
    }

    const dataForExcel = rows.map(d => ({
      ID: d.id,
      Periodo: d.periodo ?? '',
      Tipo: d.tipo ?? '',
      Escuela: d.escuela ?? '',
      'Fecha Subida': d.fecha_subida ?? '',
      Usuario: `${d.username ?? ''} ${d.userlastname ?? ''}`.trim(),
      URL: d.url ?? '',
      Estado: d.aceptado === 1 ? 'Aceptado' : (d.aceptado === 0 ? 'Rechazado' : 'Pendiente'),
      Observación: d.obser ?? ''
    }));

    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.json_to_sheet(dataForExcel, { skipHeader: false });
    XLSX.utils.book_append_sheet(wb, ws, 'Documentos');

    // auto width simple
    const cols = Object.keys(dataForExcel[0] || {});
    ws['!cols'] = cols.map(k => ({ wch: Math.min(Math.max(k.length, 16), 50) }));

    const fecha = new Date().toISOString().slice(0,19).replace('T','_').replace(/:/g,'');
    XLSX.writeFile(wb, `documentos_${fecha}.xlsx`);
    showToast('success', 'Exportado', 'Se descargó el Excel.');
  } catch (e) {
    console.error(e);
    showToast('error', 'Error al exportar', 'Intenta nuevamente.');
  } finally {
    exportLoading.value = false;
  }
};

watch(buscar, () => { pagina.value = 1; getDocumentos(); });
watch(programa, () => { pagina.value = 1; getDocumentos(); });
watch(periodo, () => { pagina.value = 1; getDocumentos(); });
watch(tipo, () => { pagina.value = 1; getDocumentos(); });
watch(aceptado, () => { pagina.value = 1; getDocumentos(); });
watch(pageSize, () => { pagina.value = 1; getDocumentos(); });

onMounted(async () => {
  await getPeriodos();
  await getDocumentos(); // sin filtros; backend ya ordena pendientes primero
});
</script>

<style scoped>
/* Trunca observación a dos líneas en la tabla */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
