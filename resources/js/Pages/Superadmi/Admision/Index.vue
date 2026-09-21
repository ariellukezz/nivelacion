<template>
  <Head title="Integración de Admisión" />

  <AuthenticatedLayout>
    <Toast />

    <div class="p-4 bg-white rounded-lg shadow-xs space-y-5">
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <div>
          <h2 class="text-xl font-bold text-gray-800">Integración de Admisión</h2>
          <p class="text-sm text-gray-500">
            Flujo operativo: seleccione el período, cargue la matriz, verifique la API y sincronice únicamente los nuevos con código.
          </p>
        </div>

        <Link href="/superadmi/admision-reportes">
          <Button label="Ver reportes" icon="pi pi-chart-bar" severity="secondary" outlined />
        </Link>
      </div>

      <!-- 1. Periodo y ámbito -->
      <section class="border rounded-lg p-4 space-y-4">
        <div class="flex items-center gap-2">
          <span class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">1</span>
          <div>
            <h3 class="font-bold text-gray-800">Período y ámbito de trabajo</h3>
            <p class="text-xs text-gray-500">Todo lo que se verifique o sincronice queda limitado al período seleccionado.</p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-semibold mb-1">Período</label>
            <Dropdown
              v-model="periodoId"
              :options="periodosOpciones"
              optionLabel="label"
              optionValue="id_periodo"
              placeholder="Seleccione período"
              class="w-full"
              @change="cambioPeriodo"
            />
          </div>

          <div>
            <label class="block text-sm font-semibold mb-1">Ámbito de API</label>
            <Dropdown
              v-model="ambito"
              :options="ambitos"
              optionLabel="label"
              optionValue="value"
              class="w-full"
              @change="cambioAmbito"
            />
          </div>

          <div v-if="ambito === 'programa'">
            <label class="block text-sm font-semibold mb-1">Programa de Admisión</label>
            <Dropdown
              v-model="programaId"
              :options="programasVinculados"
              optionLabel="label"
              optionValue="id_admision"
              placeholder="Seleccione programa"
              filter
              class="w-full"
              @change="limpiarVerificacion"
            />
          </div>
        </div>

        <div v-if="ambito === 'programa'" class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded p-3">
          En modo Programa, la API se consulta solo para ese programa. Los registros <strong>Solo Matriz</strong> no pueden atribuirse a un programa hasta que aparezcan en la API, porque la matriz no guarda programa.
        </div>
      </section>

      <!-- 2. Matriz -->
      <section class="border rounded-lg p-4 space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
          <div class="flex items-center gap-2">
            <span class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">2</span>
            <div>
              <h3 class="font-bold text-gray-800">Matriz de competencias</h3>
              <p class="text-xs text-gray-500">Se importa por período y DNI. Volver a importar actualiza los registros existentes.</p>
            </div>
          </div>

          <div class="flex flex-wrap gap-2">
            <Button
              label="Descargar plantilla"
              icon="pi pi-download"
              severity="secondary"
              outlined
              :disabled="!periodoId"
              @click="descargarPlantillaMatriz"
            />
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
          <ResumenCard titulo="Matriz actualmente cargada" :valor="matrizExistente" />
          <ResumenCard titulo="Archivo leído" :valor="preview.total" />
          <ResumenCard titulo="Registros válidos" :valor="preview.validos" />
        </div>

        <div class="flex flex-col md:flex-row md:items-center gap-3">
          <input
            type="file"
            accept=".xlsx,.xls"
            class="block w-full text-sm border rounded p-2"
            :disabled="!periodoId"
            @change="leerMatriz"
          />

          <Button
            label="Importar / actualizar matriz"
            icon="pi pi-upload"
            :disabled="!puedeImportarMatriz"
            :loading="loadingMatriz"
            @click="importarMatriz"
          />
        </div>

        <div v-if="archivoMatriz" class="text-sm text-gray-600">
          Archivo: <strong>{{ archivoMatriz }}</strong>
        </div>

        <div v-if="matrizLeida" class="grid grid-cols-2 md:grid-cols-4 gap-3">
          <ResumenCard titulo="DNI únicos" :valor="preview.unicos" />
          <ResumenCard titulo="DNI duplicados" :valor="preview.duplicados" />
          <ResumenCard titulo="Período diferente" :valor="preview.periodo_diferente" />
          <ResumenCard titulo="Observaciones" :valor="preview.errores.length" />
        </div>

        <div v-if="preview.errores.length" class="border border-amber-200 bg-amber-50 rounded p-3 text-sm text-amber-800">
          <div class="font-semibold mb-1">Revise el archivo antes de importar:</div>
          <div v-for="(error, i) in preview.errores.slice(0, 8)" :key="i">• {{ error }}</div>
          <div v-if="preview.errores.length > 8">... y {{ preview.errores.length - 8 }} observación(es) más.</div>
        </div>
      </section>

      <!-- 3. API -->
      <section class="border rounded-lg p-4 space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
          <div class="flex items-center gap-2">
            <span class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">3</span>
            <div>
              <h3 class="font-bold text-gray-800">API de Admisión</h3>
              <p class="text-xs text-gray-500">Primero se muestra lo guardado en nuestra base. La API solo se consulta cuando presione Consultar novedades.</p>
            </div>
          </div>

          <div class="flex flex-wrap gap-2">
            <Button
              label="Consultar novedades API"
              icon="pi pi-refresh"
              :loading="loadingVerificacion"
              :disabled="!puedeVerificar"
              @click="verificarApi"
            />
          </div>
        </div>

        <div class="border rounded-lg p-4 bg-gray-50 space-y-3">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
            <div>
              <div class="font-semibold text-gray-800">Estado guardado en nuestra base</div>
              <div class="text-xs text-gray-500">Carga rápida, sin consultar la API.</div>
            </div>
            <Button label="Actualizar datos locales" icon="pi pi-refresh" severity="secondary" outlined :loading="loadingLocal" :disabled="!periodoId" @click="cargarEstadoLocal" />
          </div>
          <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
            <ResumenCard titulo="Matriz" :valor="estadoLocal.matriz_total" />
            <ResumenCard titulo="API sincronizada" :valor="estadoLocal.api_sincronizados" />
            <ResumenCard titulo="Completo" :valor="estadoLocal.completo" />
            <ResumenCard titulo="Solo Matriz" :valor="estadoLocal.solo_matriz" />
            <ResumenCard titulo="Solo API" :valor="estadoLocal.solo_api" />
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs text-gray-600">
            <div>Última matriz: <strong>{{ formatoFecha(estadoLocal.ultima_matriz) }}</strong></div>
            <div>Última sincronización API: <strong>{{ formatoFecha(estadoLocal.ultima_sincronizacion) }}</strong></div>
          </div>
        </div>

        <div v-if="!verificacion.hecha" class="border border-dashed rounded-lg p-4 text-sm text-gray-500">
          Presione <strong>Consultar novedades API</strong> solamente cuando quiera comprobar si Admisión tiene nuevos códigos o cambios que todavía no están guardados.
        </div>

        <div v-else class="space-y-4">
          <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <ResumenCard titulo="API total" :valor="verificacion.resumen.api_total" />
            <ResumenCard titulo="Con código" :valor="verificacion.resumen.api_con_codigo" />
            <ResumenCard titulo="Sin código" :valor="verificacion.resumen.api_sin_codigo" />
            <ResumenCard titulo="Matriz del período" :valor="verificacion.resumen.matriz_total" />
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
            <div class="border rounded-lg p-4 bg-green-50">
              <div class="text-sm text-gray-600">Completo API + Matriz</div>
              <div class="text-3xl font-bold text-green-800">{{ verificacion.resumen.coinciden || 0 }}</div>
              <div class="text-xs text-gray-600 mt-2">
                Con código: <strong>{{ verificacion.resumen.coinciden_con_codigo || 0 }}</strong> ·
                Sin código: <strong>{{ verificacion.resumen.coinciden_sin_codigo || 0 }}</strong>
              </div>
            </div>

            <div class="border rounded-lg p-4 bg-amber-50">
              <div class="text-sm text-gray-600">Solo Matriz</div>
              <div class="text-3xl font-bold text-amber-800">{{ ambito === 'programa' ? '—' : (verificacion.resumen.solo_matriz || 0) }}</div>
              <div class="text-xs text-gray-600 mt-2">{{ ambito === 'programa' ? 'No se puede atribuir Solo Matriz a un programa hasta que aparezca en API.' : 'Todavía no aparece en la API del período.' }}</div>
            </div>

            <div class="border rounded-lg p-4 bg-blue-50">
              <div class="text-sm text-gray-600">Solo API</div>
              <div class="text-3xl font-bold text-blue-800">{{ verificacion.resumen.solo_api || 0 }}</div>
              <div class="text-xs text-gray-600 mt-2">Aparece en API, pero aún no está en la matriz.</div>
            </div>
          </div>

          <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <ResumenCard titulo="Nuevos con código" :valor="verificacion.resumen.nuevos_con_codigo" />
            <ResumenCard titulo="Ya sincronizados" :valor="verificacion.resumen.ya_sincronizados" />
            <ResumenCard titulo="Incidencias" :valor="totalIncidencias" />
            <ResumenCard titulo="Programas sin equivalencia" :valor="verificacion.resumen.error_programa" />
          </div>

          <div
            v-if="Number(verificacion.resumen.nuevos_con_codigo || 0) > 0 || progresoSync.visible"
            class="border border-green-200 bg-green-50 rounded-lg p-4 space-y-3"
          >
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
              <div>
                <div class="font-bold text-green-900">
                  {{ Number(verificacion.resumen.nuevos_con_codigo || 0) }} nuevo(s) con código listo(s) para sincronizar
                </div>
                <div class="text-xs text-green-800">
                  Solo se insertan los nuevos válidos. Los ya sincronizados, códigos cambiados o conflictos no se duplican.
                </div>
              </div>
              <Button
                :label="`Sincronizar ${Number(verificacion.resumen.nuevos_con_codigo || 0)} nuevo(s)`"
                icon="pi pi-cloud-download"
                severity="success"
                :loading="loadingSincronizacion"
                :disabled="loadingSincronizacion || !Number(verificacion.resumen.nuevos_con_codigo || 0)"
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

          <div v-if="verificacion.errores_api?.length" class="border border-orange-200 bg-orange-50 rounded p-3 text-sm text-orange-800">
            {{ verificacion.errores_api.length }} consulta(s) de API presentaron error. Los totales pueden estar incompletos.
          </div>
        </div>
      </section>

      <!-- Configuración avanzada -->
      <details class="border rounded-lg">
        <summary class="cursor-pointer p-4 font-semibold text-gray-800 bg-gray-50">
          Configuración avanzada: procesos y equivalencias de programas
        </summary>

        <div class="p-4 space-y-5">
          <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            <div>
              <div class="font-semibold">Procesos de Admisión</div>
              <div class="text-xs text-gray-500">Actualice el catálogo ocasionalmente y vincule cada proceso con su período de Nivelación.</div>
            </div>
            <Button
              label="Actualizar catálogo de procesos"
              icon="pi pi-refresh"
              severity="secondary"
              outlined
              :loading="loadingProcesos"
              @click="sincronizarProcesos"
            />
          </div>

          <DataTable :value="procesos" class="p-datatable-sm" paginator :rows="10">
            <Column field="id_admision" header="ID" />
            <Column field="nombre" header="Proceso" style="min-width: 20rem" />
            <Column field="semestre_detectado" header="Detectado" />
            <Column header="Período Nivelación" style="min-width: 15rem">
              <template #body="{ data }">
                <Dropdown
                  v-model="data.id_periodo"
                  :options="periodosOpciones"
                  optionLabel="label"
                  optionValue="id_periodo"
                  showClear
                  class="w-full"
                  @change="guardarPeriodoProceso(data)"
                />
              </template>
            </Column>
          </DataTable>

          <div>
            <div class="font-semibold mb-2">Equivalencias de programas</div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-3">
              <ResumenCard titulo="Programas API" :valor="programas.length" />
              <ResumenCard titulo="Vinculados" :valor="programasVinculados.length" />
              <ResumenCard titulo="Sin equivalencia" :valor="programasSinVincular.length" />
            </div>

            <div v-if="programasSinVincular.length" class="border border-amber-200 bg-amber-50 rounded p-3 text-sm">
              <div class="font-semibold mb-2">Programas que requieren configurar programa.id_admision:</div>
              <div v-for="item in programasSinVincular" :key="item.id_admision">
                • ID {{ item.id_admision }} - {{ item.nombre_admision }}
              </div>
            </div>
            <div v-else class="text-sm text-green-700">Todos los programas de Admisión están vinculados.</div>
          </div>
        </div>
      </details>
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
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import XLSX from 'xlsx';

const props = defineProps({
  periodos: { type: Array, default: () => [] }
});

const toast = useToast();
const periodoId = ref(null);
const ambito = ref('todo');
const programaId = ref(null);
const procesos = ref([]);
const programas = ref([]);
const matrizExistente = ref(0);
const estadoLocal = ref({
  matriz_total: 0, api_sincronizados: 0, completo: 0, solo_matriz: 0, solo_api: 0, incidencias: 0,
  ultima_sincronizacion: null, ultima_matriz: null
});

const loadingProcesos = ref(false);
const loadingLocal = ref(false);
const loadingMatriz = ref(false);
const loadingVerificacion = ref(false);
const loadingSincronizacion = ref(false);
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

const archivoMatriz = ref('');
const matrizData = ref([]);
const matrizLeida = ref(false);
const preview = ref({ total: 0, unicos: 0, duplicados: 0, periodo_diferente: 0, validos: 0, errores: [] });

const verificacion = ref({
  hecha: false,
  resumen: {},
  registros: [],
  errores_api: []
});

const ambitos = [
  { label: 'Todo el período', value: 'todo' },
  { label: 'Un programa específico', value: 'programa' }
];

const periodosOpciones = computed(() => props.periodos.map(item => ({
  ...item,
  label: `${item.nombre}${String(item.estado).toLowerCase() === 'activo' ? ' - Activo' : ''}`
})));

const periodoSeleccionado = computed(() =>
  props.periodos.find(x => Number(x.id_periodo) === Number(periodoId.value)) || null
);

const programasVinculados = computed(() => programas.value
  .filter(x => x.vinculado)
  .map(x => ({ ...x, label: `${x.id_admision} - ${x.nombre_admision}` }))
  .sort((a, b) => String(a.nombre_admision).localeCompare(String(b.nombre_admision)))
);

const programasSinVincular = computed(() => programas.value.filter(x => !x.vinculado));

const puedeVerificar = computed(() =>
  Boolean(periodoId.value && (ambito.value === 'todo' || programaId.value))
);

const puedeImportarMatriz = computed(() => Boolean(
  periodoId.value && matrizLeida.value && matrizData.value.length &&
  preview.value.duplicados === 0 && preview.value.periodo_diferente === 0 && preview.value.errores.length === 0
));

const totalIncidencias = computed(() => {
  const r = verificacion.value.resumen || {};
  return Number(r.codigo_cambiado || 0) + Number(r.conflicto_codigo || 0) + Number(r.conflicto_api || 0) + Number(r.error_programa || 0);
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

const showToast = (severity, summary, detail) => toast.add({ severity, summary, detail, life: 4500 });

const formatoFecha = valor => {
  if (!valor) return 'Sin registros';
  const d = new Date(valor);
  return Number.isNaN(d.getTime()) ? String(valor) : d.toLocaleString();
};

const limpiarVerificacion = () => {
  verificacion.value = { hecha: false, resumen: {}, registros: [], errores_api: [] };
};

const limpiarPreview = () => {
  archivoMatriz.value = '';
  matrizData.value = [];
  matrizLeida.value = false;
  preview.value = { total: 0, unicos: 0, duplicados: 0, periodo_diferente: 0, validos: 0, errores: [] };
};

const cargarProcesos = async () => {
  const { data } = await axios.get('/superadmi/admision/procesos');
  procesos.value = data.datos || [];
};

const cargarProgramas = async () => {
  const { data } = await axios.get('/superadmi/admision/programas');
  programas.value = data.datos || [];
};

const cargarEstadoLocal = async () => {
  if (!periodoId.value) return;
  loadingLocal.value = true;
  try {
    const { data } = await axios.get('/superadmi/admision/reporte-data', {
      params: { id_periodo: periodoId.value, summary_only: 1 }
    });
    estadoLocal.value = data.resumen || {};
    matrizExistente.value = Number(data.resumen?.matriz_total || 0);
  } catch (e) {
    showToast('error', 'ERROR', e.response?.data?.message || 'No se pudo cargar el estado local.');
  } finally {
    loadingLocal.value = false;
  }
};

const cargarMatrizExistente = async () => {
  matrizExistente.value = 0;
  if (!periodoId.value) return;
  const { data } = await axios.get('/superadmi/admision/matriz-periodo', {
    params: { id_periodo: periodoId.value }
  });
  matrizExistente.value = Number(data.total || 0);
};

const cambioPeriodo = async () => {
  programaId.value = null;
  limpiarPreview();
  limpiarVerificacion();
  await Promise.all([cargarMatrizExistente(), cargarEstadoLocal()]);
};

const cambioAmbito = () => {
  programaId.value = null;
  limpiarVerificacion();
};

const sincronizarProcesos = async () => {
  loadingProcesos.value = true;
  try {
    const { data } = await axios.post('/superadmi/admision/sincronizar-procesos');
    showToast(data.tipo || 'success', data.titulo || 'PROCESOS', data.mensaje || 'Procesos actualizados.');
    await cargarProcesos();
  } catch (e) {
    showToast('error', 'ERROR', e.response?.data?.message || 'No se pudieron actualizar los procesos.');
  } finally {
    loadingProcesos.value = false;
  }
};

const guardarPeriodoProceso = async proceso => {
  try {
    const { data } = await axios.post('/superadmi/admision/asignar-periodo', {
      id: proceso.id,
      id_periodo: proceso.id_periodo || null
    });
    showToast(data.tipo || 'success', data.titulo || 'PERÍODO', data.mensaje || 'Relación actualizada.');
    limpiarVerificacion();
  } catch (e) {
    showToast('error', 'ERROR', e.response?.data?.message || 'No se pudo relacionar el proceso.');
  }
};

const verificarApi = async () => {
  if (!puedeVerificar.value) return;
  loadingVerificacion.value = true;
  try {
    const params = { id_periodo: periodoId.value };
    if (ambito.value === 'programa') params.id_programa_admision = programaId.value;

    const { data } = await axios.get('/superadmi/admision/reporte-cobertura', { params });
    verificacion.value = {
      hecha: true,
      resumen: data.resumen || {},
      registros: data.registros || [],
      errores_api: data.errores_api || []
    };
    matrizExistente.value = Number(data.resumen?.matriz_total || matrizExistente.value);

    if (data.errores_api?.length) {
      showToast('warn', 'VERIFICACIÓN PARCIAL', `${data.errores_api.length} consulta(s) a la API presentaron error.`);
    } else {
      showToast('success', 'VERIFICACIÓN COMPLETA', 'La API fue comparada con la matriz del período seleccionado.');
    }
  } catch (e) {
    showToast('error', 'ERROR', e.response?.data?.message || 'No se pudo verificar la API.');
  } finally {
    loadingVerificacion.value = false;
  }
};

const sincronizarNuevos = async () => {
  const cantidad = Number(verificacion.value.resumen?.nuevos_con_codigo || 0);
  if (!cantidad) return;
  if (!window.confirm(`Se sincronizarán ${cantidad} postulante(s) nuevo(s) con código. ¿Continuar?`)) return;

  // Obtenemos únicamente los programas que realmente tienen nuevos válidos.
  const nuevos = (verificacion.value.registros || []).filter(r =>
    r.api && r.con_codigo && !r.sincronizado && !r.incidencia && r.id_programa_admision
  );

  const grupos = new Map();
  for (const r of nuevos) {
    const id = Number(r.id_programa_admision);
    if (!grupos.has(id)) {
      const catalogo = programasVinculados.value.find(x => Number(x.id_admision) === id);
      grupos.set(id, {
        id,
        nombre: r.programa || catalogo?.nombre_admision || `Programa ${id}`,
        cantidad: 0
      });
    }
    grupos.get(id).cantidad++;
  }

  let programasObjetivo = [...grupos.values()];

  // Respaldo: si la verificación reportó nuevos pero no llegó el detalle,
  // sincronizamos el programa seleccionado o los programas vinculados.
  if (!programasObjetivo.length) {
    if (ambito.value === 'programa' && programaId.value) {
      const p = programasVinculados.value.find(x => Number(x.id_admision) === Number(programaId.value));
      programasObjetivo = [{
        id: Number(programaId.value),
        nombre: p?.nombre_admision || `Programa ${programaId.value}`,
        cantidad
      }];
    } else {
      programasObjetivo = programasVinculados.value.map(p => ({
        id: Number(p.id_admision),
        nombre: p.nombre_admision,
        cantidad: 0
      }));
    }
  }

  loadingSincronizacion.value = true;
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

    await cargarEstadoLocal();

    // Evita volver a sincronizar usando el mismo resultado anterior.
    verificacion.value = { hecha: false, resumen: {}, registros: [], errores_api: [] };

    if (progresoSync.value.errores.length) {
      showToast('warn', 'SINCRONIZACIÓN PARCIAL', `${progresoSync.value.insertados} nuevo(s) insertado(s). ${progresoSync.value.errores.length} programa(s) presentaron error.`);
    } else {
      showToast('success', 'SINCRONIZACIÓN COMPLETADA', `${progresoSync.value.insertados} postulante(s) nuevo(s) fueron sincronizados.`);
    }
  } finally {
    loadingSincronizacion.value = false;
  }
};

const descargarPlantillaMatriz = () => {
  if (!periodoId.value) return;
  const nombre = (periodoSeleccionado.value?.nombre || 'PERIODO').replace(/[^a-zA-Z0-9_-]+/g, '_');
  const link = document.createElement('a');
  link.href = '/plantillas/Plantilla_Matriz_Nivelacion_Ingresantes.xlsx';
  link.download = `Matriz_ID_${periodoId.value}_${nombre}.xlsx`;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};

const leerMatriz = event => {
  const file = event.target.files?.[0];
  if (!file || !periodoId.value) return;

  limpiarPreview();
  archivoMatriz.value = file.name;
  const reader = new FileReader();

  reader.onload = e => {
    try {
      const workbook = XLSX.read(new Uint8Array(e.target.result), { type: 'array' });
      const hoja = workbook.Sheets['MATRIZ'] || workbook.Sheets[workbook.SheetNames[0]];
      const filas = XLSX.utils.sheet_to_json(hoja, { defval: null });
      if (!filas.length) throw new Error('La hoja MATRIZ está vacía.');

      const conteo = {};
      const errores = [];
      const datos = filas.map((row, index) => {
        const dni = String(row.dni ?? row.DNI ?? '').trim();
        const filaPeriodo = Number(row.id_periodo ?? 0);
        if (dni) conteo[dni] = (conteo[dni] || 0) + 1;

        const item = {
          fila: index + 2,
          id_periodo: filaPeriodo || null,
          dni,
          nivelar: row.nivelar ?? null,
          no_nivelar: row.no_nivelar ?? null,
          observacion: row.observacion != null ? String(row.observacion).trim() : null
        };
        for (let i = 1; i <= 11; i++) {
          item[`C${i}`] = row[`C${i}`] ?? null;
          item[`C${i}_R`] = row[`C${i}_R`] ?? null;
        }
        return item;
      });

      const duplicados = new Set(Object.entries(conteo).filter(([, n]) => n > 1).map(([dni]) => dni));
      let periodoDiferente = 0;
      let validos = 0;

      datos.forEach(item => {
        if (!item.dni) errores.push(`Fila ${item.fila}: falta DNI.`);
        else if (!item.id_periodo) errores.push(`Fila ${item.fila}: falta id_periodo.`);
        else if (Number(item.id_periodo) !== Number(periodoId.value)) {
          periodoDiferente++;
          errores.push(`Fila ${item.fila}: pertenece al período ${item.id_periodo}.`);
        } else if (duplicados.has(item.dni)) {
          // contado aparte
        } else if (item.observacion && item.observacion.length > 255) {
          errores.push(`Fila ${item.fila}: observación supera 255 caracteres.`);
        } else validos++;
      });

      if (duplicados.size) errores.push(`Existen ${duplicados.size} DNI duplicado(s) en el archivo.`);

      matrizData.value = datos.filter(x => x.dni);
      preview.value = {
        total: datos.length,
        unicos: Object.keys(conteo).length,
        duplicados: duplicados.size,
        periodo_diferente: periodoDiferente,
        validos,
        errores
      };
      matrizLeida.value = true;
    } catch (e) {
      limpiarPreview();
      showToast('error', 'ARCHIVO NO VÁLIDO', e.message || 'No se pudo leer el Excel.');
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
      const { data } = await axios.post('/superadmi/admision/importar-matriz', {
        id_periodo: periodoId.value,
        archivo_origen: archivoMatriz.value,
        datos: chunk
      });
      if (!data.estado) throw new Error(data.mensaje || 'Error al importar matriz.');
      procesados += chunk.length;
    }
    showToast('success', 'MATRIZ IMPORTADA', `${procesados} registros insertados/actualizados.`);
    limpiarPreview();
    limpiarVerificacion();
    await Promise.all([cargarMatrizExistente(), cargarEstadoLocal()]);
  } catch (e) {
    showToast('error', 'ERROR', e.response?.data?.message || e.message || 'No se pudo importar la matriz.');
  } finally {
    loadingMatriz.value = false;
  }
};

onMounted(async () => {
  await Promise.all([cargarProcesos(), cargarProgramas()]);
  const activo = periodosOpciones.value.find(x => String(x.estado).toLowerCase() === 'activo');
  periodoId.value = activo?.id_periodo ?? periodosOpciones.value[0]?.id_periodo ?? null;
  await Promise.all([cargarMatrizExistente(), cargarEstadoLocal()]);
});
</script>
