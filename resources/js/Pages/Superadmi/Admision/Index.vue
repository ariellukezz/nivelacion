<template>
  <Head title="Integración Admisión" />

  <AuthenticatedLayout>
    <Toast />

    <div class="p-4 bg-white rounded-lg shadow-xs space-y-5">
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <div>
          <h2 class="text-xl font-bold text-gray-800">Integración con Admisión</h2>
          <p class="text-sm text-gray-500">
            API y matriz se almacenan primero en tablas de trabajo. La matriz se controla por Periodo de Nivelación.
          </p>
        </div>

        <Button
          label="Actualizar procesos"
          icon="pi pi-refresh"
          :loading="loadingProcesos"
          @click="sincronizarProcesos"
        />
      </div>

      <!-- Procesos y periodo -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-semibold mb-1">Proceso de Admisión</label>
          <Dropdown
            v-model="procesoId"
            :options="procesos"
            optionLabel="nombre"
            optionValue="id_admision"
            placeholder="Seleccione proceso"
            filter
            class="w-full"
            @change="cambioProceso"
          />
        </div>

        <div>
          <label class="block text-sm font-semibold mb-1">Periodo de Nivelación</label>
          <Dropdown
            v-model="periodoId"
            :options="periodos"
            optionLabel="label"
            optionValue="id_periodo"
            placeholder="Seleccione periodo"
            showClear
            class="w-full"
            @change="cambioPeriodo"
          />
        </div>

        <div class="flex items-end">
          <Button
            label="Guardar relación"
            icon="pi pi-link"
            severity="secondary"
            :disabled="!procesoSeleccionado"
            @click="guardarPeriodo"
          />
        </div>
      </div>

      <div v-if="procesoSeleccionado" class="p-3 rounded border bg-gray-50 text-sm">
        <strong>{{ procesoSeleccionado.nombre }}</strong>
        <span class="ml-2 text-gray-500">Semestre: {{ procesoSeleccionado.semestre_detectado || '-' }}</span>
        <span class="ml-2 text-gray-500">Sede: {{ sedeNombre(procesoSeleccionado.id_sede_filial) }}</span>
      </div>

      <!-- API -->
      <div class="border-t pt-5">
        <div class="flex flex-col lg:flex-row lg:items-end gap-4">
          <div class="flex-1">
            <label class="block text-sm font-semibold mb-1">Programa de Admisión</label>

            <Dropdown
              v-model="programaId"
              :options="programas"
              optionLabel="label"
              optionValue="id_admision"
              placeholder="Seleccione programa"
              filter
              class="w-full"
            >
              <template #option="slotProps">
                <div>
                  <div class="font-semibold">{{ slotProps.option.nombre_admision }}</div>
                  <small :class="slotProps.option.vinculado ? 'text-green-600' : 'text-red-600'">
                    {{
                      slotProps.option.vinculado
                        ? `Nivelación: ${slotProps.option.programa_nivelacion}`
                        : 'Sin equivalencia en programa.id_admision'
                    }}
                  </small>
                </div>
              </template>
            </Dropdown>
          </div>

          <Button
            label="Verificar programas"
            icon="pi pi-check-circle"
            severity="secondary"
            :loading="loadingProgramas"
            @click="cargarProgramas"
          />

          <Button
            label="Sincronizar postulantes"
            icon="pi pi-cloud-download"
            :disabled="!procesoId || !programaId"
            :loading="loadingPostulantes"
            @click="sincronizarPostulantes"
          />

          <Button
            label="Verificar proceso"
            icon="pi pi-search"
            severity="secondary"
            :disabled="!procesoId || !periodoId || loadingVerificacion"
            :loading="loadingVerificacion"
            @click="verificarProcesoCompleto"
          />

          <Button
            label="Sincronizar proceso completo"
            icon="pi pi-sync"
            severity="success"
            :disabled="!procesoId || !periodoId || loadingProcesoCompleto"
            :loading="loadingProcesoCompleto"
            @click="sincronizarProcesoCompleto"
          />
        </div>
      </div>

      <div
        v-if="verificacion.iniciada"
        class="p-4 rounded-lg border bg-blue-50 text-blue-900"
      >
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
          <div>
            <div class="font-bold">Verificación del proceso</div>
            <div class="text-sm">{{ procesoSeleccionado?.nombre || '-' }}</div>
          </div>
          <Tag
            :value="verificacion.nuevos_con_codigo > 0 ? 'HAY NUEVOS DATOS' : 'SIN NUEVOS CÓDIGOS'"
            :severity="verificacion.nuevos_con_codigo > 0 ? 'success' : 'info'"
          />
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3 mt-4">
          <ResumenCard titulo="API revisados" :valor="verificacion.total_api" />
          <ResumenCard titulo="Con código" :valor="verificacion.con_codigo" />
          <ResumenCard titulo="Nuevos con código" :valor="verificacion.nuevos_con_codigo" />
          <ResumenCard titulo="Ya sincronizados" :valor="verificacion.ya_sincronizados" />
          <ResumenCard titulo="Código cambiado" :valor="verificacion.codigo_cambiado" />
          <ResumenCard titulo="Pendientes sin código" :valor="verificacion.sin_codigo" />
        </div>

        <div class="mt-3 text-sm">
          <strong>Programas revisados:</strong>
          {{ verificacion.actual }} / {{ verificacion.total_programas }}
        </div>

        <div class="mt-2 h-3 rounded-full bg-blue-100 overflow-hidden">
          <div
            class="h-full bg-blue-600 transition-all"
            :style="{ width: `${verificacion.porcentaje}%` }"
          ></div>
        </div>

        <div
          v-if="verificacion.pendientes.length"
          class="mt-4"
        >
          <div class="font-semibold mb-2">
            Pendientes de código / control biométrico
          </div>

          <DataTable
            :value="verificacion.pendientes"
            class="p-datatable-sm"
            :rows="10"
            paginator
            scrollable
            tableStyle="min-width: 45rem"
          >
            <Column field="dni" header="DNI" />
            <Column field="estudiante" header="Estudiante" />
            <Column field="programa" header="Programa" />
          </DataTable>
        </div>
      </div>

      <div
        v-if="progresoProceso.iniciado"
        class="p-4 rounded-lg border bg-green-50 text-green-900"
      >
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
          <div>
            <div class="font-bold">Sincronización del proceso completo</div>
            <div class="text-sm">{{ procesoSeleccionado?.nombre || '-' }}</div>
          </div>

          <div class="text-sm font-semibold">
            {{ progresoProceso.actual }} / {{ progresoProceso.total }} programas
          </div>
        </div>

        <div class="mt-3 h-3 rounded-full bg-green-100 overflow-hidden">
          <div
            class="h-full bg-green-600 transition-all"
            :style="{ width: `${progresoProceso.porcentaje}%` }"
          ></div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3 mt-4">
          <ResumenCard titulo="API recibidos" :valor="progresoProceso.total_api" />
          <ResumenCard titulo="Con código" :valor="progresoProceso.con_codigo" />
          <ResumenCard titulo="Insertados" :valor="progresoProceso.insertados" />
          <ResumenCard titulo="Actualizados" :valor="progresoProceso.actualizados" />
          <ResumenCard titulo="Sin código omitidos" :valor="progresoProceso.sin_codigo" />
          <ResumenCard titulo="Errores" :valor="progresoProceso.errores.length" />
        </div>

        <div class="mt-3 text-sm">
          <strong>Programa actual:</strong>
          {{ progresoProceso.programa || 'Preparando...' }}
        </div>

        <div
          v-if="progresoProceso.pendientes.length"
          class="mt-4"
        >
          <div class="font-semibold mb-2">
            Registros sin código que NO ingresaron a la base
          </div>

          <DataTable
            :value="progresoProceso.pendientes"
            class="p-datatable-sm"
            :rows="10"
            paginator
            scrollable
            tableStyle="min-width: 45rem"
          >
            <Column field="dni" header="DNI" />
            <Column field="estudiante" header="Estudiante" />
            <Column field="programa" header="Programa" />
          </DataTable>
        </div>

        <div
          v-if="progresoProceso.omitidos.length"
          class="mt-2 text-xs text-amber-700"
        >
          {{ progresoProceso.omitidos.length }} programa(s) no fueron consultados porque todavía no tienen equivalencia en programa.id_admision.
        </div>
      </div>

      <!-- Matriz -->
      <div class="border-t pt-5">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-3">
          <div>
            <h3 class="font-bold text-gray-800">Matriz de competencias</h3>
            <p class="text-sm text-gray-500">
              La matriz pertenece al periodo. El programa y el nuevo código del estudiante llegarán desde Admisión.
            </p>
          </div>

          <Button
            label="Descargar plantilla"
            icon="pi pi-file-excel"
            severity="success"
            outlined
            :disabled="!periodoId"
            @click="descargarPlantillaMatriz"
          />
        </div>

        <div
          v-if="periodoSeleccionado"
          class="mb-4 p-4 rounded border bg-blue-50 text-blue-900"
        >
          <div class="text-sm font-semibold">Periodo destino de la matriz</div>
          <div class="text-lg font-bold mt-1">
            ID {{ periodoSeleccionado.id_periodo }} - {{ periodoSeleccionado.nombre }}
          </div>
          <div class="text-xs text-blue-700 mt-1">
            Cada fila del Excel debe contener este mismo id_periodo.
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-end">
          <div class="lg:col-span-2">
            <label class="block text-sm font-semibold mb-1">Archivo Excel</label>
            <input
              type="file"
              accept=".xlsx,.xls"
              @change="leerMatriz"
              class="w-full border rounded p-2"
            />
            <small class="text-gray-500">
              Obligatorios: id_periodo y dni. También puede usar observacion para registrar una nota adicional.
            </small>
          </div>

          <Button
            label="Importar matriz"
            icon="pi pi-upload"
            severity="info"
            :disabled="!puedeImportarMatriz"
            :loading="loadingMatriz"
            @click="importarMatriz"
          />
        </div>

        <!-- Previsualización -->
        <div v-if="matrizLeida" class="mt-5 border rounded-lg overflow-hidden">
          <div class="p-4 bg-gray-50 border-b">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-2">
              <div>
                <h4 class="font-bold text-gray-800">Previsualización antes de importar</h4>
                <p class="text-sm text-gray-500">{{ archivoMatriz }}</p>
              </div>

              <Tag
                :value="puedeImportarMatriz ? 'LISTA PARA IMPORTAR' : 'REVISAR ARCHIVO'"
                :severity="puedeImportarMatriz ? 'success' : 'danger'"
              />
            </div>
          </div>

          <div class="p-4">
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3 mb-4">
              <ResumenCard titulo="Periodo ID" :valor="periodoId" />
              <ResumenCard titulo="Filas Excel" :valor="preview.total" />
              <ResumenCard titulo="DNI únicos" :valor="preview.unicos" />
              <ResumenCard titulo="Duplicados" :valor="preview.duplicados" />
              <ResumenCard titulo="Periodo diferente" :valor="preview.periodo_diferente" />
              <ResumenCard titulo="Listos" :valor="preview.validos" />
            </div>

            <div
              v-if="preview.errores.length"
              class="mb-4 p-3 rounded border border-red-200 bg-red-50 text-sm text-red-700"
            >
              <div class="font-semibold mb-1">Observaciones del archivo:</div>
              <div v-for="(error, index) in preview.errores.slice(0, 10)" :key="index">
                • {{ error }}
              </div>
              <div v-if="preview.errores.length > 10" class="mt-1">
                ... y {{ preview.errores.length - 10 }} observaciones adicionales.
              </div>
            </div>

            <div class="text-sm mb-3">
              <strong>Se insertará/actualizará en:</strong>
              periodo ID {{ periodoSeleccionado?.id_periodo }} - {{ periodoSeleccionado?.nombre }}
            </div>

            <DataTable
              :value="preview.filas"
              class="p-datatable-sm"
              scrollable
              tableStyle="min-width: 72rem"
              :rows="20"
              paginator
            >
              <Column field="fila" header="Fila" frozen />
              <Column field="id_periodo" header="ID periodo" />
              <Column field="dni" header="DNI" />
              <Column field="C1" header="C1" />
              <Column field="C2" header="C2" />
              <Column field="C3" header="C3" />
              <Column field="C4" header="C4" />
              <Column field="C5" header="C5" />
              <Column field="C6" header="C6" />
              <Column field="C7" header="C7" />
              <Column field="C8" header="C8" />
              <Column field="C9" header="C9" />
              <Column field="C10" header="C10" />
              <Column field="C11" header="C11" />
              <Column field="nivelar" header="Nivelar" />
              <Column field="observacion" header="Observación" style="min-width: 18rem" />
              <Column field="estado" header="Estado">
                <template #body="{ data }">
                  <Tag
                    :value="data.estado"
                    :severity="data.estado === 'OK' ? 'success' : 'danger'"
                  />
                </template>
              </Column>
            </DataTable>
          </div>
        </div>
      </div>

      <!-- Cruce -->
      <div v-if="periodoId" class="border-t pt-5">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 mb-4">
          <div>
            <h3 class="font-bold text-gray-800">Cruce por DNI + periodo</h3>
            <p class="text-xs text-gray-500">
              API muestra únicamente los registros guardados con código. Los pendientes sin código se visualizan durante la verificación y no ingresan a la base.
            </p>
          </div>

          <div class="flex flex-wrap gap-2">
            <Link href="/superadmi/admision-reportes">
              <Button
                label="Reportes detallados"
                icon="pi pi-chart-bar"
                severity="secondary"
                outlined
              />
            </Link>

            <Button
              label="Actualizar resumen"
              icon="pi pi-refresh"
              text
              @click="cargarResumenYCruce"
            />
          </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-7 gap-3 mb-5">
          <ResumenCard titulo="API con código" :valor="resumen.api" />
          <ResumenCard titulo="Matriz" :valor="resumen.matriz" />
          <ResumenCard titulo="Listos" :valor="resumen.listos" />
          <ResumenCard titulo="Solo matriz" :valor="resumen.solo_matriz" />
          <ResumenCard titulo="Solo API" :valor="resumen.solo_api" />
          <ResumenCard titulo="Error programa" :valor="resumen.error_programa" />
          <ResumenCard titulo="Ya registrados periodo" :valor="resumen.ya_registrado" />
        </div>

        <div class="flex flex-col md:flex-row gap-3 mb-3">
          <InputText
            v-model="term"
            placeholder="DNI, código, estudiante o programa"
            class="flex-1"
            @keyup.enter="cargarCruce(1)"
          />

          <Dropdown
            v-model="estadoFiltro"
            :options="estados"
            placeholder="Todos los estados"
            showClear
            class="w-full md:w-64"
          />

          <Button label="Buscar" icon="pi pi-search" @click="cargarCruce(1)" />
        </div>

        <DataTable
          :value="cruce.data"
          :loading="loadingCruce"
          class="p-datatable-sm"
          scrollable
          tableStyle="min-width: 70rem"
        >
          <Column field="dni" header="DNI" frozen />
          <Column field="codigo" header="Código API" />
          <Column field="estudiante" header="Estudiante" />
          <Column field="programa" header="Programa Nivelación" />
          <Column field="programa_admision" header="Programa Admisión" />
          <Column field="proceso_nombre" header="Proceso Admisión" />
          <Column field="observacion_matriz" header="Observación matriz" style="min-width: 18rem" />
          <Column field="estado_cruce" header="Estado">
            <template #body="{ data }">
              <Tag :value="data.estado_cruce" :severity="severityEstado(data.estado_cruce)" />
            </template>
          </Column>
        </DataTable>

        <div
          class="flex justify-end items-center gap-2 mt-3"
          v-if="cruce.last_page > 1"
        >
          <Button
            icon="pi pi-angle-left"
            text
            :disabled="cruce.current_page <= 1"
            @click="cargarCruce(cruce.current_page - 1)"
          />
          <span class="text-sm">Página {{ cruce.current_page }} de {{ cruce.last_page }}</span>
          <Button
            icon="pi pi-angle-right"
            text
            :disabled="cruce.current_page >= cruce.last_page"
            @click="cargarCruce(cruce.current_page + 1)"
          />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/LayoutSuperadmi.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, h, onMounted, ref } from 'vue';
import Button from 'primevue/button';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import Dropdown from 'primevue/dropdown';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import XLSX from 'xlsx';

const props = defineProps({
  periodos: {
    type: Array,
    default: () => []
  }
});

const toast = useToast();

const procesos = ref([]);
const programas = ref([]);
const procesoId = ref(null);
const periodoId = ref(null);
const programaId = ref(null);

const matrizData = ref([]);
const archivoMatriz = ref('');
const matrizLeida = ref(false);
const preview = ref({
  total: 0,
  unicos: 0,
  duplicados: 0,
  periodo_diferente: 0,
  validos: 0,
  errores: [],
  filas: []
});

const term = ref('');
const estadoFiltro = ref(null);

const loadingProcesos = ref(false);
const loadingProgramas = ref(false);
const loadingPostulantes = ref(false);
const loadingProcesoCompleto = ref(false);
const loadingVerificacion = ref(false);
const loadingMatriz = ref(false);

const progresoProceso = ref({
  iniciado: false,
  actual: 0,
  total: 0,
  porcentaje: 0,
  programa: '',
  total_api: 0,
  con_codigo: 0,
  insertados: 0,
  actualizados: 0,
  pendientes: [],
  errores: [],
  omitidos: []
});

const verificacion = ref({
  iniciada: false,
  actual: 0,
  total_programas: 0,
  porcentaje: 0,
  total_api: 0,
  con_codigo: 0,
  nuevos_con_codigo: 0,
  ya_sincronizados: 0,
  codigo_cambiado: 0,
  pendientes: [],
  errores: []
});
const loadingCruce = ref(false);

const resumen = ref({
  api: 0,
  matriz: 0,
  listos: 0,
  solo_matriz: 0,
  solo_api: 0,
  error_programa: 0,
  ya_registrado: 0,
  ultima_sincronizacion: null
});

const cruce = ref({
  data: [],
  current_page: 1,
  last_page: 1
});

const estados = [
  'LISTO',
  'SOLO MATRIZ',
  'SOLO API',
  'ERROR PROGRAMA',
  'YA REGISTRADO'
];

const periodos = computed(() =>
  props.periodos.map(item => ({
    ...item,
    label: `ID ${item.id_periodo} - ${item.nombre}`
  }))
);

const procesoSeleccionado = computed(() =>
  procesos.value.find(x => Number(x.id_admision) === Number(procesoId.value)) || null
);

const periodoSeleccionado = computed(() =>
  props.periodos.find(x => Number(x.id_periodo) === Number(periodoId.value)) || null
);

const puedeImportarMatriz = computed(() =>
  Boolean(
    periodoId.value &&
    matrizLeida.value &&
    matrizData.value.length &&
    preview.value.duplicados === 0 &&
    preview.value.periodo_diferente === 0 &&
    preview.value.errores.length === 0
  )
);

const ResumenCard = {
  props: ['titulo', 'valor'],
  setup(props) {
    return () => h(
      'div',
      { class: 'border rounded-lg p-3 bg-gray-50' },
      [
        h('div', { class: 'text-xs text-gray-500' }, props.titulo),
        h('div', { class: 'text-xl font-bold text-gray-800 mt-1' }, String(props.valor ?? 0))
      ]
    );
  }
};

const showToast = (severity, summary, detail) =>
  toast.add({ severity, summary, detail, life: 4000 });

const sedeNombre = id =>
  ({ 1: 'Puno', 2: 'Azángaro', 3: 'Chucuito Juli' }[id] || `Sede ${id ?? '-'}`);

const severityEstado = estado => ({
  'LISTO': 'success',
  'SOLO MATRIZ': 'warning',
  'SOLO API': 'info',
  'ERROR PROGRAMA': 'danger',
  'YA REGISTRADO': 'info'
}[estado] || 'info');

const limpiarPreview = () => {
  matrizData.value = [];
  archivoMatriz.value = '';
  matrizLeida.value = false;
  preview.value = {
    total: 0,
    unicos: 0,
    duplicados: 0,
    periodo_diferente: 0,
    validos: 0,
    errores: [],
    filas: []
  };
};

const cargarProcesos = async () => {
  const { data } = await axios.get('/superadmi/admision/procesos');
  procesos.value = data.datos || [];
};

const sincronizarProcesos = async () => {
  loadingProcesos.value = true;

  try {
    const { data } = await axios.post('/superadmi/admision/sincronizar-procesos');
    showToast(data.tipo, data.titulo, data.mensaje);
    await cargarProcesos();
  } catch (e) {
    showToast(
      'error',
      'ERROR',
      e.response?.data?.message || 'No se pudo consultar la API de procesos.'
    );
  } finally {
    loadingProcesos.value = false;
  }
};

const cambioProceso = async () => {
  periodoId.value = procesoSeleccionado.value?.id_periodo || null;
  programaId.value = null;
  verificacion.value.iniciada = false;
  limpiarPreview();
  await cargarResumenYCruce();
};

const cambioPeriodo = async () => {
  limpiarPreview();
  await cargarResumenYCruce();
};

const guardarPeriodo = async () => {
  if (!procesoSeleccionado.value) return;

  const { data } = await axios.post('/superadmi/admision/asignar-periodo', {
    id: procesoSeleccionado.value.id,
    id_periodo: periodoId.value
  });

  showToast(data.tipo, data.titulo, data.mensaje);
  await cargarProcesos();
};

const cargarProgramas = async () => {
  loadingProgramas.value = true;

  try {
    const { data } = await axios.get('/superadmi/admision/programas');

    programas.value = (data.datos || []).map(x => ({
      ...x,
      label: `${x.id_admision} - ${x.nombre_admision}`
    }));

    if (data.sin_vincular > 0) {
      showToast(
        'warn',
        'PROGRAMAS PENDIENTES',
        `${data.sin_vincular} programas de Admisión no tienen equivalencia local.`
      );
    }
  } catch (e) {
    showToast(
      'error',
      'ERROR',
      e.response?.data?.message || 'No se pudo consultar los programas de Admisión.'
    );
  } finally {
    loadingProgramas.value = false;
  }
};

const sincronizarPostulantes = async () => {
  loadingPostulantes.value = true;

  try {
    const { data } = await axios.post('/superadmi/admision/sincronizar-postulantes', {
      id_proceso_admision: procesoId.value,
      id_programa_admision: programaId.value
    });

    showToast(data.tipo, data.titulo, data.mensaje);

    if (data.estado && data.pendientes_sin_codigo?.length) {
      progresoProceso.value.iniciado = true;
      progresoProceso.value.pendientes = data.pendientes_sin_codigo;
      progresoProceso.value.total_api = data.total_api || 0;
      progresoProceso.value.con_codigo = data.con_codigo || 0;
      progresoProceso.value.sin_codigo = data.sin_codigo || 0;
      progresoProceso.value.insertados = data.insertados || 0;
      progresoProceso.value.actualizados = data.actualizados || 0;
    }

    if (data.estado) {
      await cargarResumenYCruce();
    }
  } catch (e) {
    showToast(
      'error',
      'ERROR',
      e.response?.data?.message || 'No se pudo sincronizar postulantes.'
    );
  } finally {
    loadingPostulantes.value = false;
  }
};

const verificarProcesoCompleto = async () => {
  if (!procesoId.value || !periodoId.value) return;

  loadingVerificacion.value = true;

  verificacion.value = {
    iniciada: true,
    actual: 0,
    total_programas: 0,
    porcentaje: 0,
    total_api: 0,
    con_codigo: 0,
      nuevos_con_codigo: 0,
    ya_sincronizados: 0,
    codigo_cambiado: 0,
    pendientes: [],
    errores: []
  };

  try {
    if (!programas.value.length) {
      await cargarProgramas();
    }

    const lista = programas.value.filter(x => x.vinculado);

    verificacion.value.total_programas = lista.length;

    for (let i = 0; i < lista.length; i++) {
      const programa = lista[i];

      try {
        const { data } = await axios.post(
          '/superadmi/admision/verificar-postulantes',
          {
            id_proceso_admision: procesoId.value,
            id_programa_admision: programa.id_admision
          }
        );

        if (data.estado) {
          verificacion.value.total_api += Number(data.total_api || 0);
          verificacion.value.con_codigo += Number(data.con_codigo || 0);
          verificacion.value.sin_codigo += Number(data.sin_codigo || 0);
          verificacion.value.nuevos_con_codigo += Number(
            data.nuevos_con_codigo || 0
          );
          verificacion.value.ya_sincronizados += Number(
            data.ya_sincronizados || 0
          );
          verificacion.value.codigo_cambiado += Number(
            data.codigo_cambiado || 0
          );

          if (data.pendientes_sin_codigo?.length) {
            verificacion.value.pendientes.push(
              ...data.pendientes_sin_codigo
            );
          }
        } else {
          verificacion.value.errores.push(
            `${programa.nombre_admision}: ${data.mensaje || 'Error'}`
          );
        }
      } catch (e) {
        verificacion.value.errores.push(
          `${programa.nombre_admision}: ${
            e.response?.data?.message ||
            e.response?.data?.mensaje ||
            'Error de consulta'
          }`
        );
      }

      verificacion.value.actual = i + 1;
      verificacion.value.porcentaje = Math.round(
        ((i + 1) / lista.length) * 100
      );
    }

    showToast(
      verificacion.value.nuevos_con_codigo > 0 ? 'success' : 'info',
      verificacion.value.nuevos_con_codigo > 0
        ? 'HAY NUEVOS DATOS'
        : 'SIN NUEVOS CÓDIGOS',
      `${verificacion.value.nuevos_con_codigo} nuevos registros con código y ${verificacion.value.sin_codigo} pendientes sin código.`
    );
  } finally {
    loadingVerificacion.value = false;
  }
};

const sincronizarProcesoCompleto = async () => {
  if (!procesoId.value || !periodoId.value) return;

  loadingProcesoCompleto.value = true;

  progresoProceso.value = {
    iniciado: true,
    actual: 0,
    total: 0,
    porcentaje: 0,
    programa: 'Preparando...',
    total_api: 0,
    con_codigo: 0,
      insertados: 0,
    actualizados: 0,
    pendientes: [],
    errores: [],
    omitidos: []
  };

  try {
    if (!programas.value.length) {
      await cargarProgramas();
    }

    const vinculados = programas.value.filter(x => x.vinculado);
    const noVinculados = programas.value.filter(x => !x.vinculado);

    progresoProceso.value.total = vinculados.length;
    progresoProceso.value.omitidos = noVinculados.map(
      x => `${x.id_admision} - ${x.nombre_admision}`
    );

    for (let i = 0; i < vinculados.length; i++) {
      const programa = vinculados[i];

      progresoProceso.value.actual = i + 1;
      progresoProceso.value.programa =
        `${programa.id_admision} - ${programa.nombre_admision}`;

      try {
        const { data } = await axios.post(
          '/superadmi/admision/sincronizar-postulantes',
          {
            id_proceso_admision: procesoId.value,
            id_programa_admision: programa.id_admision
          }
        );

        if (data.estado) {
          progresoProceso.value.total_api += Number(data.total_api || 0);
          progresoProceso.value.con_codigo += Number(data.con_codigo || 0);
          progresoProceso.value.sin_codigo += Number(data.sin_codigo || 0);
          progresoProceso.value.insertados += Number(data.insertados || 0);
          progresoProceso.value.actualizados += Number(data.actualizados || 0);

          if (data.pendientes_sin_codigo?.length) {
            progresoProceso.value.pendientes.push(
              ...data.pendientes_sin_codigo
            );
          }
        } else {
          progresoProceso.value.errores.push(
            `${programa.nombre_admision}: ${data.mensaje || 'Error'}`
          );
        }
      } catch (e) {
        progresoProceso.value.errores.push(
          `${programa.nombre_admision}: ${
            e.response?.data?.message ||
            e.response?.data?.mensaje ||
            'Error de comunicación'
          }`
        );
      }

      progresoProceso.value.porcentaje = Math.round(
        ((i + 1) / vinculados.length) * 100
      );
    }

    showToast(
      progresoProceso.value.errores.length ? 'warn' : 'success',
      'SINCRONIZACIÓN FINALIZADA',
      `${progresoProceso.value.insertados} nuevos, ${progresoProceso.value.actualizados} actualizados y ${progresoProceso.value.sin_codigo} sin código omitidos.`
    );

    await cargarResumenYCruce();

    // Refresca la verificación para mostrar si aún quedan nuevos.
    await verificarProcesoCompleto();
  } finally {
    loadingProcesoCompleto.value = false;
  }
};

const descargarPlantillaMatriz = () => {
  if (!periodoId.value) {
    showToast(
      'warn',
      'SELECCIONE PERIODO',
      'Seleccione primero el Periodo de Nivelación.'
    );
    return;
  }

  const nombrePeriodo = (periodoSeleccionado.value?.nombre || 'PERIODO')
    .replace(/[^a-zA-Z0-9_-]+/g, '_');

  const link = document.createElement('a');
  link.href = '/plantillas/Plantilla_Matriz_Nivelacion_Ingresantes.xlsx';
  link.download = `Matriz_ID_${periodoId.value}_${nombrePeriodo}.xlsx`;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};

const leerMatriz = event => {
  const file = event.target.files?.[0];

  if (!file) return;

  if (!periodoId.value) {
    event.target.value = '';
    showToast(
      'warn',
      'SELECCIONE PERIODO',
      'Seleccione primero el periodo al que se insertará la matriz.'
    );
    return;
  }

  limpiarPreview();
  archivoMatriz.value = file.name;

  const reader = new FileReader();

  reader.onload = e => {
    try {
      const workbook = XLSX.read(
        new Uint8Array(e.target.result),
        { type: 'array' }
      );

      const hoja =
        workbook.Sheets['MATRIZ'] ||
        workbook.Sheets[workbook.SheetNames[0]];

      const filas = XLSX.utils.sheet_to_json(
        hoja,
        { defval: null }
      );

      if (!filas.length) {
        showToast(
          'warn',
          'MATRIZ VACÍA',
          'La hoja MATRIZ no contiene registros.'
        );
        return;
      }

      const tienePeriodo = filas.some(row =>
        row.id_periodo !== undefined
      );

      const tieneDni = filas.some(row =>
        row.dni !== undefined || row.DNI !== undefined
      );

      if (!tienePeriodo || !tieneDni) {
        showToast(
          'error',
          'COLUMNAS OBLIGATORIAS',
          'El Excel debe contener las columnas id_periodo y dni.'
        );
        return;
      }

      const conteoDni = {};
      const errores = [];

      const datos = filas.map((row, index) => {
        const dni = String(row.dni ?? row.DNI ?? '').trim();
        const filaPeriodo = Number(row.id_periodo ?? 0);

        if (dni) {
          conteoDni[dni] = (conteoDni[dni] || 0) + 1;
        }

        const item = {
          fila: index + 2,
          id_periodo: filaPeriodo || null,
          dni,
          nivelar: row.nivelar ?? null,
          no_nivelar: row.no_nivelar ?? null,
          observacion: row.observacion !== null && row.observacion !== undefined
            ? String(row.observacion).trim()
            : null
        };

        for (let i = 1; i <= 11; i++) {
          item[`C${i}`] = row[`C${i}`] ?? null;
          item[`C${i}_R`] = row[`C${i}_R`] ?? null;
        }

        return item;
      });

      const duplicadosDni = new Set(
        Object.entries(conteoDni)
          .filter(([, cantidad]) => cantidad > 1)
          .map(([dni]) => dni)
      );

      let periodoDiferente = 0;
      let validos = 0;

      const filasPreview = datos.map(item => {
        let estado = 'OK';

        if (!item.id_periodo) {
          estado = 'SIN PERIODO';
          errores.push(`Fila ${item.fila}: falta id_periodo.`);
        } else if (Number(item.id_periodo) !== Number(periodoId.value)) {
          estado = 'PERIODO DIFERENTE';
          periodoDiferente++;
          errores.push(
            `Fila ${item.fila}: id_periodo ${item.id_periodo}; seleccionado ${periodoId.value}.`
          );
        }

        if (!item.dni) {
          estado = 'SIN DNI';
          errores.push(`Fila ${item.fila}: falta DNI.`);
        } else if (item.observacion && item.observacion.length > 255) {
          estado = 'OBSERVACIÓN LARGA';
          errores.push(`Fila ${item.fila}: observacion supera los 255 caracteres.`);
        } else if (duplicadosDni.has(item.dni)) {
          estado = 'DNI DUPLICADO';
        }

        if (estado === 'OK') {
          validos++;
        }

        return {
          ...item,
          estado
        };
      });

      if (duplicadosDni.size) {
        errores.push(
          `Existen ${duplicadosDni.size} DNI repetidos dentro del mismo archivo y periodo.`
        );
      }

      matrizData.value = datos.filter(item => item.dni);

      preview.value = {
        total: datos.length,
        unicos: Object.keys(conteoDni).length,
        duplicados: duplicadosDni.size,
        periodo_diferente: periodoDiferente,
        validos,
        errores,
        filas: filasPreview
      };

      matrizLeida.value = true;

      if (errores.length) {
        showToast(
          'warn',
          'PREVISUALIZACIÓN CON OBSERVACIONES',
          'Revise el resumen antes de importar.'
        );
      } else {
        showToast(
          'success',
          'MATRIZ VALIDADA',
          `${validos} registros listos para ID ${periodoId.value} - ${periodoSeleccionado.value?.nombre}.`
        );
      }
    } catch (e) {
      limpiarPreview();

      showToast(
        'error',
        'ARCHIVO NO VÁLIDO',
        'No se pudo leer el Excel. Use la plantilla descargada desde el sistema.'
      );
    }
  };

  reader.readAsArrayBuffer(file);
};

const importarMatriz = async () => {
  if (!puedeImportarMatriz.value) return;

  loadingMatriz.value = true;

  try {
    const chunkSize = 500;
    let procesados = 0;

    for (let i = 0; i < matrizData.value.length; i += chunkSize) {
      const chunk = matrizData.value.slice(i, i + chunkSize);

      const { data } = await axios.post(
        '/superadmi/admision/importar-matriz',
        {
          id_periodo: periodoId.value,
          archivo_origen: archivoMatriz.value,
          datos: chunk
        }
      );

      if (!data.estado) {
        throw new Error(data.mensaje || 'Error al importar matriz.');
      }

      procesados += chunk.length;
    }

    showToast(
      'success',
      'MATRIZ IMPORTADA',
      `${procesados} registros insertados/actualizados en ID ${periodoId.value} - ${periodoSeleccionado.value?.nombre}.`
    );

    limpiarPreview();
    await cargarResumenYCruce();
  } catch (e) {
    showToast(
      'error',
      'ERROR',
      e.response?.data?.message ||
        e.message ||
        'No se pudo importar la matriz.'
    );
  } finally {
    loadingMatriz.value = false;
  }
};

const cargarResumen = async () => {
  if (!periodoId.value) return;

  const { data } = await axios.get(
    '/superadmi/admision/resumen',
    {
      params: {
        id_periodo: periodoId.value
      }
    }
  );

  resumen.value = data.datos;
};

const cargarCruce = async (page = 1) => {
  if (!periodoId.value) return;

  loadingCruce.value = true;

  try {
    const { data } = await axios.get(
      '/superadmi/admision/cruce',
      {
        params: {
          id_periodo: periodoId.value,
          estado: estadoFiltro.value,
          term: term.value,
          page
        }
      }
    );

    cruce.value = data.datos;
  } finally {
    loadingCruce.value = false;
  }
};

const cargarResumenYCruce = async () => {
  if (!periodoId.value) {
    resumen.value = {
      api: 0,
      matriz: 0,
      listos: 0,
      solo_matriz: 0,
      solo_api: 0,
          error_programa: 0,
      ya_registrado: 0
    };

    cruce.value = {
      data: [],
      current_page: 1,
      last_page: 1
    };

    return;
  }

  await Promise.all([
    cargarResumen(),
    cargarCruce(1)
  ]);
};

onMounted(async () => {
  await cargarProcesos();
  await cargarProgramas();
});
</script>
