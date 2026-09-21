<template>
  <Head title="Reporte de Integración Admisión" />

  <AuthenticatedLayout>
    <Toast />

    <div class="p-4 bg-white rounded-lg shadow-xs space-y-5">
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <div>
          <h2 class="text-xl font-bold text-gray-800">Reporte de Integración Admisión</h2>
          <p class="text-sm text-gray-500">
            El reporte normal usa solo la base local. La API se consulta únicamente cuando usted solicita buscar novedades.
          </p>
        </div>

        <Link href="/superadmi/admision-integracion">
          <Button label="Volver a integración" icon="pi pi-arrow-left" severity="secondary" outlined />
        </Link>
      </div>

      <!-- Reporte local -->
      <section class="border rounded-lg p-4 space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-3">
          <div class="w-full lg:max-w-sm">
            <label class="block text-sm font-semibold mb-1">Período</label>
            <Dropdown
              v-model="periodoId"
              :options="periodosOpciones"
              optionLabel="label"
              optionValue="id_periodo"
              class="w-full"
              placeholder="Seleccione período"
              @change="cambioPeriodo"
            />
          </div>

          <div class="flex flex-wrap gap-2">
            <Button
              label="Actualizar reporte local"
              icon="pi pi-refresh"
              severity="secondary"
              outlined
              :loading="loadingLocal"
              :disabled="!periodoId"
              @click="cargarLocal"
            />
            <Button
              label="Consultar novedades API"
              icon="pi pi-cloud-download"
              :loading="loadingApi"
              :disabled="!periodoId"
              @click="consultarNovedades"
            />
          </div>
        </div>

        <div v-if="reporteLocalCargado" class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
          <div class="border rounded p-3 bg-gray-50">
            <div class="text-gray-500">Última importación de matriz</div>
            <div class="font-semibold text-gray-800">{{ formatoFecha(resumenLocal.ultima_matriz) }}</div>
          </div>
          <div class="border rounded p-3 bg-gray-50">
            <div class="text-gray-500">Última sincronización API guardada</div>
            <div class="font-semibold text-gray-800">{{ formatoFecha(resumenLocal.ultima_sincronizacion) }}</div>
          </div>
        </div>
      </section>

      <template v-if="reporteLocalCargado">
        <!-- Resumen local -->
        <section class="space-y-3">
          <div>
            <h3 class="font-bold text-gray-800">Datos actualmente guardados</h3>
            <p class="text-xs text-gray-500">Estos valores salen solamente de nuestra base de datos; no realizan llamadas a Admisión.</p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <button type="button" class="border rounded-lg p-4 text-left bg-green-50 hover:shadow-sm" @click="filtros.estado = 'COMPLETO'">
              <div class="text-sm text-gray-600">Completo API + Matriz</div>
              <div class="text-3xl font-bold text-green-800">{{ resumenLocal.completo || 0 }}</div>
            </button>
            <button type="button" class="border rounded-lg p-4 text-left bg-amber-50 hover:shadow-sm" @click="filtros.estado = 'SOLO_MATRIZ'">
              <div class="text-sm text-gray-600">Solo Matriz</div>
              <div class="text-3xl font-bold text-amber-800">{{ resumenLocal.solo_matriz || 0 }}</div>
            </button>
            <button type="button" class="border rounded-lg p-4 text-left bg-blue-50 hover:shadow-sm" @click="filtros.estado = 'SOLO_API'">
              <div class="text-sm text-gray-600">Solo API sincronizada</div>
              <div class="text-3xl font-bold text-blue-800">{{ resumenLocal.solo_api || 0 }}</div>
            </button>
            <button type="button" class="border rounded-lg p-4 text-left bg-red-50 hover:shadow-sm" @click="filtros.estado = 'INCIDENCIA'">
              <div class="text-sm text-gray-600">Incidencias locales</div>
              <div class="text-3xl font-bold text-red-800">{{ resumenLocal.incidencias || 0 }}</div>
            </button>
          </div>

          <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <ResumenCard titulo="Matriz total" :valor="resumenLocal.matriz_total" />
            <ResumenCard titulo="API sincronizada" :valor="resumenLocal.api_sincronizados" />
            <ResumenCard titulo="Con código" :valor="resumenLocal.con_codigo" />
            <ResumenCard titulo="Sincronizados" :valor="resumenLocal.sincronizados" />
          </div>
        </section>

        <!-- Filtros -->
        <section class="border rounded-lg p-4 space-y-4">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
            <div>
              <h3 class="font-bold text-gray-800">Filtros del reporte local</h3>
              <p class="text-xs text-gray-500">Todos los filtros se pueden combinar.</p>
            </div>
            <Button label="Limpiar filtros" icon="pi pi-filter-slash" severity="secondary" outlined @click="limpiarFiltros" />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-semibold mb-1">Programa</label>
              <Dropdown v-model="filtros.programa" :options="programasFiltro" optionLabel="label" optionValue="value" placeholder="Todos" showClear filter class="w-full" />
            </div>
            <div>
              <label class="block text-sm font-semibold mb-1">Estado</label>
              <Dropdown v-model="filtros.estado" :options="estadosFiltro" optionLabel="label" optionValue="value" placeholder="Todos" showClear class="w-full" />
            </div>
            <div>
              <label class="block text-sm font-semibold mb-1">Matriz</label>
              <Dropdown v-model="filtros.matriz" :options="siNoTodos" optionLabel="label" optionValue="value" class="w-full" />
            </div>
            <div>
              <label class="block text-sm font-semibold mb-1">API sincronizada</label>
              <Dropdown v-model="filtros.api" :options="siNoTodos" optionLabel="label" optionValue="value" class="w-full" />
            </div>
            <div>
              <label class="block text-sm font-semibold mb-1">Código</label>
              <Dropdown v-model="filtros.codigo" :options="codigoFiltro" optionLabel="label" optionValue="value" class="w-full" />
            </div>
            <div>
              <label class="block text-sm font-semibold mb-1">Sincronizado</label>
              <Dropdown v-model="filtros.sincronizado" :options="siNoTodos" optionLabel="label" optionValue="value" class="w-full" />
            </div>
            <div>
              <label class="block text-sm font-semibold mb-1">DNI / código / estudiante</label>
              <InputText v-model="filtros.texto" placeholder="Buscar..." class="w-full" />
            </div>
          </div>

          <div class="text-sm text-gray-600">
            Mostrando <strong>{{ registrosFiltrados.length }}</strong> de <strong>{{ registrosLocal.length }}</strong> registro(s).
          </div>
        </section>

        <!-- Tabla local -->
        <section class="border rounded-lg overflow-hidden">
          <div class="p-3 bg-gray-50 border-b flex items-center justify-between gap-2">
            <div class="font-semibold text-gray-800">Detalle guardado en nuestra base</div>
            <div class="text-xs text-gray-500">Sin consulta API</div>
          </div>

          <DataTable :value="registrosFiltrados" class="p-datatable-sm" paginator :rows="50" :rowsPerPageOptions="[25, 50, 100, 200]" scrollable tableStyle="min-width: 95rem">
            <Column field="dni" header="DNI" frozen />
            <Column field="codigo" header="Código" />
            <Column field="estudiante" header="Estudiante" style="min-width: 18rem" />
            <Column field="programa" header="Programa" style="min-width: 17rem">
              <template #body="{ data }">{{ data.programa || 'Sin identificar' }}</template>
            </Column>
            <Column header="Matriz">
              <template #body="{ data }"><Tag :value="data.matriz ? 'SÍ' : 'NO'" :severity="data.matriz ? 'success' : 'secondary'" /></template>
            </Column>
            <Column header="API">
              <template #body="{ data }"><Tag :value="data.api ? 'SÍ' : 'NO'" :severity="data.api ? 'success' : 'secondary'" /></template>
            </Column>
            <Column header="Código">
              <template #body="{ data }"><Tag :value="data.con_codigo ? 'CON CÓDIGO' : 'SIN CÓDIGO'" :severity="data.con_codigo ? 'success' : 'warning'" /></template>
            </Column>
            <Column header="Sincronizado">
              <template #body="{ data }"><Tag :value="data.sincronizado ? 'SÍ' : 'NO'" :severity="data.sincronizado ? 'success' : 'secondary'" /></template>
            </Column>
            <Column header="Estado" style="min-width: 12rem">
              <template #body="{ data }"><Tag :value="labelEstado(data.estado_principal)" :severity="severityEstado(data.estado_principal)" /></template>
            </Column>
            <Column field="incidencia" header="Incidencia" style="min-width: 15rem">
              <template #body="{ data }">{{ data.incidencia || '-' }}</template>
            </Column>
            <Column field="observacion" header="Observación matriz" style="min-width: 16rem" />
          </DataTable>
        </section>
      </template>

      <!-- Novedades API, solo bajo demanda -->
      <section class="border rounded-lg p-4 space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
          <div>
            <h3 class="font-bold text-gray-800">Novedades en Admisión</h3>
            <p class="text-xs text-gray-500">Esta sección sí consulta la API. Úsela solamente cuando quiera comprobar si existen cambios nuevos.</p>
          </div>
          <Button label="Consultar novedades API" icon="pi pi-cloud-download" :loading="loadingApi" :disabled="!periodoId" @click="consultarNovedades" />
        </div>

        <div v-if="!novedadesConsultadas" class="text-sm text-gray-500 border border-dashed rounded p-4">
          No se ha consultado la API en esta sesión. El reporte de arriba sigue mostrando únicamente los datos locales guardados.
        </div>

        <template v-else>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <button class="border rounded p-3 text-left bg-green-50" @click="abrirDetalleApi('nuevos_con_codigo', 'Nuevos con código')">
              <div class="text-xs text-gray-500">Nuevos con código</div>
              <div class="text-2xl font-bold">{{ novedades.resumen.nuevos_con_codigo || 0 }}</div>
            </button>
            <button class="border rounded p-3 text-left bg-amber-50" @click="abrirDetalleApi('api_sin_codigo', 'Sin código todavía')">
              <div class="text-xs text-gray-500">Sin código todavía</div>
              <div class="text-2xl font-bold">{{ novedades.resumen.api_sin_codigo || 0 }}</div>
            </button>
            <button class="border rounded p-3 text-left bg-orange-50" @click="abrirDetalleApi('codigo_cambiado', 'Código cambiado')">
              <div class="text-xs text-gray-500">Código cambiado</div>
              <div class="text-2xl font-bold">{{ novedades.resumen.codigo_cambiado || 0 }}</div>
            </button>
            <button class="border rounded p-3 text-left bg-red-50" @click="abrirDetalleApi('incidencias_api', 'Incidencias API')">
              <div class="text-xs text-gray-500">Incidencias API</div>
              <div class="text-2xl font-bold">{{ totalIncidenciasApi }}</div>
            </button>
          </div>

          <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <ResumenCard titulo="API detectados" :valor="novedades.resumen.api_total" />
            <ResumenCard titulo="Con código" :valor="novedades.resumen.api_con_codigo" />
            <ResumenCard titulo="Ya sincronizados" :valor="novedades.resumen.ya_sincronizados" />
            <ResumenCard titulo="Completo API + Matriz" :valor="novedades.resumen.coinciden" />
          </div>

          <div
            v-if="Number(novedades.resumen.nuevos_con_codigo || 0) > 0 || progresoSync.visible"
            class="border border-green-200 bg-green-50 rounded-lg p-4 space-y-3"
          >
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
              <div>
                <div class="font-bold text-green-900">
                  {{ Number(novedades.resumen.nuevos_con_codigo || 0) }} nuevo(s) con código listo(s) para sincronizar
                </div>
                <div class="text-xs text-green-800">
                  La sincronización se ejecuta programa por programa para mostrar el avance real.
                </div>
              </div>
              <Button
                :label="`Sincronizar ${Number(novedades.resumen.nuevos_con_codigo || 0)} nuevo(s)`"
                icon="pi pi-cloud-download"
                severity="success"
                :loading="loadingSync"
                :disabled="loadingSync || !Number(novedades.resumen.nuevos_con_codigo || 0)"
                @click="sincronizarNuevos"
              />
            </div>

            <div v-if="progresoSync.visible" class="space-y-2">
              <div class="flex justify-between gap-3 text-sm">
                <span class="font-semibold">{{ progresoSync.mensaje }}</span>
                <span>{{ progresoSync.porcentaje }}%</span>
              </div>
              <div class="w-full h-3 bg-white border rounded-full overflow-hidden">
                <div
                  class="h-full bg-green-600 transition-all duration-300"
                  :style="{ width: `${progresoSync.porcentaje}%` }"
                ></div>
              </div>
              <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
                <div>Programas: <strong>{{ progresoSync.actual }}/{{ progresoSync.total }}</strong></div>
                <div>Insertados: <strong>{{ progresoSync.insertados }}</strong></div>
                <div>Ya sincronizados: <strong>{{ progresoSync.ya_sincronizados }}</strong></div>
                <div>Incidencias: <strong>{{ progresoSync.incidencias }}</strong></div>
              </div>
              <div v-if="progresoSync.programa" class="text-xs text-gray-600">
                Programa actual: <strong>{{ progresoSync.programa }}</strong>
              </div>
            </div>
          </div>

          <div v-if="novedades.errores_api?.length" class="border border-orange-200 bg-orange-50 rounded p-3 text-sm text-orange-800">
            {{ novedades.errores_api.length }} consulta(s) a la API presentaron error. Los resultados de novedades pueden estar incompletos.
          </div>
        </template>
      </section>
    </div>

    <Dialog v-model:visible="detalleApiVisible" modal :header="detalleApiTitulo" :style="{ width: '90vw', maxWidth: '1100px' }">
      <div class="mb-3 text-sm text-gray-500">{{ detalleApi.length }} registro(s)</div>
      <DataTable :value="detalleApi" class="p-datatable-sm" paginator :rows="25" :rowsPerPageOptions="[25, 50, 100]" scrollable tableStyle="min-width: 70rem">
        <Column field="dni" header="DNI" frozen />
        <Column field="codigo" header="Código" />
        <Column field="codigo_guardado" header="Código guardado" />
        <Column field="estudiante" header="Estudiante" style="min-width: 18rem" />
        <Column field="programa" header="Programa" style="min-width: 16rem" />
        <Column field="proceso_nombre" header="Proceso" style="min-width: 15rem" />
        <Column field="situacion" header="Situación" style="min-width: 15rem" />
      </DataTable>
    </Dialog>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/LayoutSuperadmi.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, h, onMounted, reactive, ref } from 'vue';
import Button from 'primevue/button';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import Dialog from 'primevue/dialog';
import Dropdown from 'primevue/dropdown';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';

const props = defineProps({ periodos: { type: Array, default: () => [] } });
const toast = useToast();

const periodoId = ref(null);
const loadingLocal = ref(false);
const loadingApi = ref(false);
const loadingSync = ref(false);
const progresoSync = ref({
  visible: false,
  porcentaje: 0,
  actual: 0,
  total: 0,
  programa: '',
  mensaje: '',
  insertados: 0,
  ya_sincronizados: 0,
  incidencias: 0,
  errores: []
});
const reporteLocalCargado = ref(false);
const novedadesConsultadas = ref(false);
const registrosLocal = ref([]);
const programasServidor = ref([]);

const resumenLocal = ref({
  total: 0,
  matriz_total: 0,
  api_sincronizados: 0,
  completo: 0,
  solo_matriz: 0,
  solo_api: 0,
  incidencias: 0,
  sincronizados: 0,
  con_codigo: 0,
  ultima_sincronizacion: null,
  ultima_matriz: null
});

const novedades = ref({ resumen: {}, listas: {}, registros: [], errores_api: [] });
const detalleApiVisible = ref(false);
const detalleApiTitulo = ref('');
const detalleApi = ref([]);

const filtros = reactive({
  programa: null,
  estado: null,
  matriz: null,
  api: null,
  codigo: null,
  sincronizado: null,
  texto: ''
});

const periodosOpciones = computed(() => props.periodos.map(item => ({
  ...item,
  label: `${item.nombre}${String(item.estado).toLowerCase() === 'activo' ? ' - Activo' : ''}`
})));

const estadosFiltro = [
  { label: 'Completo API + Matriz', value: 'COMPLETO' },
  { label: 'Solo Matriz', value: 'SOLO_MATRIZ' },
  { label: 'Solo API sincronizada', value: 'SOLO_API' },
  { label: 'Incidencias', value: 'INCIDENCIA' }
];

const siNoTodos = [
  { label: 'Todos', value: null },
  { label: 'Sí', value: true },
  { label: 'No', value: false }
];

const codigoFiltro = [
  { label: 'Todos', value: null },
  { label: 'Con código', value: true },
  { label: 'Sin código', value: false }
];

const programasFiltro = computed(() => [
  ...programasServidor.value,
  { value: '__SIN_PROGRAMA__', label: 'Sin identificar / Solo Matriz' }
]);

const registrosFiltrados = computed(() => {
  const texto = filtros.texto.trim().toLowerCase();

  return registrosLocal.value.filter(r => {
    if (filtros.programa === '__SIN_PROGRAMA__' && r.id_programa_admision) return false;
    if (filtros.programa !== null && filtros.programa !== '__SIN_PROGRAMA__' && Number(r.id_programa_admision) !== Number(filtros.programa)) return false;
    if (filtros.estado && r.estado_principal !== filtros.estado) return false;
    if (filtros.matriz !== null && Boolean(r.matriz) !== filtros.matriz) return false;
    if (filtros.api !== null && Boolean(r.api) !== filtros.api) return false;
    if (filtros.codigo !== null && Boolean(r.con_codigo) !== filtros.codigo) return false;
    if (filtros.sincronizado !== null && Boolean(r.sincronizado) !== filtros.sincronizado) return false;

    if (texto) {
      const bolsa = [r.dni, r.codigo, r.estudiante, r.programa, r.proceso_nombre, r.incidencia]
        .filter(Boolean).join(' ').toLowerCase();
      if (!bolsa.includes(texto)) return false;
    }

    return true;
  });
});

const totalIncidenciasApi = computed(() => {
  const r = novedades.value.resumen || {};
  return Number(r.conflicto_codigo || 0) + Number(r.conflicto_api || 0) + Number(r.error_programa || 0);
});

const ResumenCard = {
  props: ['titulo', 'valor'],
  setup(componentProps) {
    return () => h('div', { class: 'border rounded-lg p-3 bg-gray-50' }, [
      h('div', { class: 'text-xs text-gray-500' }, componentProps.titulo),
      h('div', { class: 'text-xl font-bold text-gray-800 mt-1' }, String(componentProps.valor ?? 0))
    ]);
  }
};

const labelEstado = estado => ({
  COMPLETO: 'COMPLETO',
  SOLO_MATRIZ: 'SOLO MATRIZ',
  SOLO_API: 'SOLO API',
  INCIDENCIA: 'INCIDENCIA'
}[estado] || estado);

const severityEstado = estado => ({
  COMPLETO: 'success',
  SOLO_MATRIZ: 'warning',
  SOLO_API: 'info',
  INCIDENCIA: 'danger'
}[estado] || 'secondary');

const formatoFecha = valor => {
  if (!valor) return 'Sin registros';
  const d = new Date(valor);
  return Number.isNaN(d.getTime()) ? String(valor) : d.toLocaleString();
};

const limpiarFiltros = () => {
  filtros.programa = null;
  filtros.estado = null;
  filtros.matriz = null;
  filtros.api = null;
  filtros.codigo = null;
  filtros.sincronizado = null;
  filtros.texto = '';
};

const cargarLocal = async () => {
  if (!periodoId.value) return;
  loadingLocal.value = true;
  try {
    const { data } = await axios.get('/superadmi/admision/reporte-data', {
      params: { id_periodo: periodoId.value }
    });

    resumenLocal.value = data.resumen || {};
    registrosLocal.value = data.registros || [];
    programasServidor.value = data.programas || [];
    reporteLocalCargado.value = true;
  } catch (e) {
    toast.add({ severity: 'error', summary: 'ERROR', detail: e.response?.data?.message || 'No se pudo cargar el reporte local.', life: 5000 });
  } finally {
    loadingLocal.value = false;
  }
};

const cambioPeriodo = async () => {
  limpiarFiltros();
  novedadesConsultadas.value = false;
  novedades.value = { resumen: {}, listas: {}, errores_api: [] };
  await cargarLocal();
};

const consultarNovedades = async () => {
  if (!periodoId.value) return;
  loadingApi.value = true;
  try {
    const { data } = await axios.get('/superadmi/admision/reporte-cobertura', {
      params: { id_periodo: periodoId.value }
    });
    novedades.value = data;
    novedadesConsultadas.value = true;

    if (data.errores_api?.length) {
      toast.add({ severity: 'warn', summary: 'CONSULTA PARCIAL', detail: `${data.errores_api.length} consulta(s) API tuvieron error.`, life: 5000 });
    } else {
      toast.add({ severity: 'success', summary: 'API CONSULTADA', detail: 'Se compararon las novedades de Admisión contra nuestra base local.', life: 3500 });
    }
  } catch (e) {
    toast.add({ severity: 'error', summary: 'ERROR', detail: e.response?.data?.message || 'No se pudo consultar la API.', life: 5000 });
  } finally {
    loadingApi.value = false;
  }
};

const sincronizarNuevos = async () => {
  const cantidad = Number(novedades.value.resumen?.nuevos_con_codigo || 0);
  if (!cantidad) return;
  if (!window.confirm(`Se sincronizarán ${cantidad} postulante(s) nuevo(s) con código. ¿Continuar?`)) return;

  const nuevos = (novedades.value.registros || []).filter(r =>
    r.api && r.con_codigo && !r.sincronizado && !r.incidencia && r.id_programa_admision
  );

  const grupos = new Map();
  for (const r of nuevos) {
    const id = Number(r.id_programa_admision);
    if (!grupos.has(id)) {
      grupos.set(id, {
        id,
        nombre: r.programa || `Programa ${id}`,
        cantidad: 0
      });
    }
    grupos.get(id).cantidad++;
  }

  const programasObjetivo = [...grupos.values()];
  if (!programasObjetivo.length) {
    toast.add({ severity: 'warn', summary: 'SIN DETALLE', detail: 'La consulta detectó nuevos, pero no se pudo identificar sus programas. Vuelva a consultar novedades API.', life: 5000 });
    return;
  }

  loadingSync.value = true;
  progresoSync.value = {
    visible: true,
    porcentaje: 0,
    actual: 0,
    total: programasObjetivo.length,
    programa: '',
    mensaje: 'Preparando sincronización...',
    insertados: 0,
    ya_sincronizados: 0,
    incidencias: 0,
    errores: []
  };

  try {
    for (let i = 0; i < programasObjetivo.length; i++) {
      const programa = programasObjetivo[i];
      progresoSync.value.actual = i + 1;
      progresoSync.value.programa = programa.nombre;
      progresoSync.value.mensaje = `Sincronizando ${programa.nombre}...`;
      progresoSync.value.porcentaje = Math.round((i / programasObjetivo.length) * 100);

      try {
        const { data } = await axios.post('/superadmi/admision/sincronizar-nuevos-codigo', {
          id_periodo: periodoId.value,
          id_programa_admision: programa.id
        });

        const d = data.datos || {};
        progresoSync.value.insertados += Number(d.insertados || 0);
        progresoSync.value.ya_sincronizados += Number(d.ya_sincronizados || 0);
        progresoSync.value.incidencias +=
          Number(d.codigo_cambiado || 0) +
          Number(d.conflicto_codigo || 0) +
          Number(d.conflicto_api || 0) +
          Number(d.error_programa || 0);
      } catch (e) {
        progresoSync.value.errores.push({
          programa: programa.nombre,
          mensaje: e.response?.data?.message || 'Error de sincronización'
        });
      }

      progresoSync.value.porcentaje = Math.round(((i + 1) / programasObjetivo.length) * 100);
    }

    progresoSync.value.programa = '';
    progresoSync.value.mensaje = progresoSync.value.errores.length
      ? 'Sincronización finalizada con observaciones.'
      : 'Sincronización completada correctamente.';
    progresoSync.value.porcentaje = 100;

    await cargarLocal();

    // La consulta API anterior ya no representa el estado actual.
    novedadesConsultadas.value = false;
    novedades.value = { resumen: {}, listas: {}, registros: [], errores_api: [] };

    if (progresoSync.value.errores.length) {
      toast.add({ severity: 'warn', summary: 'SINCRONIZACIÓN PARCIAL', detail: `${progresoSync.value.insertados} nuevo(s) insertado(s). ${progresoSync.value.errores.length} programa(s) presentaron error.`, life: 6000 });
    } else {
      toast.add({ severity: 'success', summary: 'SINCRONIZACIÓN COMPLETADA', detail: `${progresoSync.value.insertados} postulante(s) nuevo(s) fueron sincronizados.`, life: 5000 });
    }
  } finally {
    loadingSync.value = false;
  }
};

const abrirDetalleApi = (clave, titulo) => {
  detalleApiTitulo.value = titulo;

  if (clave === 'incidencias_api') {
    detalleApi.value = [
      ...(novedades.value.listas?.conflicto_codigo || []),
      ...(novedades.value.listas?.conflicto_api || []),
      ...(novedades.value.listas?.error_programa || [])
    ];
  } else {
    detalleApi.value = novedades.value.listas?.[clave] || [];
  }

  detalleApiVisible.value = true;
};

onMounted(async () => {
  const activo = periodosOpciones.value.find(x => String(x.estado).toLowerCase() === 'activo');
  periodoId.value = activo?.id_periodo ?? periodosOpciones.value[0]?.id_periodo ?? null;
  if (periodoId.value) await cargarLocal();
});
</script>
