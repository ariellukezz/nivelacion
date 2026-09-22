<template>
  <Head title="Integración a Nivelación" />

  <AuthenticatedLayout>
    <Toast />

    <div class="p-4 bg-white rounded-lg shadow-xs space-y-5">
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <div>
          <h2 class="text-xl font-bold text-gray-800">Integración a Nivelación</h2>
          <p class="text-sm text-gray-500">
            Transfiere únicamente registros completos de Admisión hacia users, estudiante, matriz y datos_ingreso.
          </p>
        </div>

        <Link href="/superadmi/admision-integracion">
          <Button label="Volver a Admisión" icon="pi pi-arrow-left" severity="secondary" outlined />
        </Link>
      </div>

      <section class="border rounded-lg p-4 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-semibold mb-1">Período</label>
            <Dropdown
              v-model="filtros.id_periodo"
              :options="periodosOpciones"
              optionLabel="label"
              optionValue="id_periodo"
              placeholder="Seleccione período"
              class="w-full"
              @change="cambioPeriodo"
            />
          </div>

          <div>
            <label class="block text-sm font-semibold mb-1">Ámbito</label>
            <Dropdown
              v-model="filtros.ambito"
              :options="ambitos"
              optionLabel="label"
              optionValue="value"
              class="w-full"
              @change="cambioAmbito"
            />
          </div>

          <div v-if="filtros.ambito === 'escuela'">
            <label class="block text-sm font-semibold mb-1">Escuela</label>
            <Dropdown
              v-model="filtros.id_escuela"
              :options="escuelasOpciones"
              optionLabel="label"
              optionValue="value"
              placeholder="Seleccione escuela"
              filter
              class="w-full"
              @change="cargarDatos"
            />
          </div>

          <div v-if="filtros.ambito === 'programa'">
            <label class="block text-sm font-semibold mb-1">Escuela</label>
            <Dropdown
              v-model="filtros.id_escuela"
              :options="escuelasOpciones"
              optionLabel="label"
              optionValue="value"
              placeholder="Seleccione escuela"
              filter
              class="w-full"
              @change="cambioEscuelaPrograma"
            />
          </div>

          <div v-if="filtros.ambito === 'programa'">
            <label class="block text-sm font-semibold mb-1">Programa</label>
            <Dropdown
              v-model="filtros.id_programa"
              :options="programasPorEscuela"
              optionLabel="label"
              optionValue="value"
              placeholder="Seleccione programa"
              filter
              class="w-full"
              @change="cargarDatos"
            />
          </div>
        </div>

        <div class="flex flex-wrap gap-2">
          <Button
            label="Actualizar vista previa"
            icon="pi pi-refresh"
            severity="secondary"
            outlined
            :loading="loading"
            :disabled="!puedeCargar"
            @click="cargarDatos"
          />
          <Button
            :label="`Integrar ${resumen.pendientes || 0} pendiente(s)`"
            icon="pi pi-database"
            severity="success"
            :disabled="!puedeIntegrar"
            :loading="integrando"
            @click="integrarPendientes"
          />
        </div>
      </section>

      <section v-if="cargado" class="space-y-4">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
          <ResumenCard titulo="Completos Admisión" :valor="resumen.completos_admision" />
          <ResumenCard titulo="Pendientes" :valor="resumen.pendientes" tono="success" />
          <ResumenCard titulo="Integrados" :valor="resumen.integrados" />
          <ResumenCard titulo="Parciales" :valor="resumen.parciales" tono="warning" />
          <ResumenCard titulo="Conflictos" :valor="resumen.conflictos" tono="danger" />
        </div>

        <div class="border rounded-lg p-4 space-y-3">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
            <div>
              <h3 class="font-semibold text-gray-800">Detalle de integración</h3>
              <p class="text-xs text-gray-500">Los filtros no cambian qué se integra; solo ayudan a revisar la lista.</p>
            </div>
            <Button label="Limpiar filtros" icon="pi pi-filter-slash" text @click="limpiarFiltrosTabla" />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
              <label class="block text-sm font-semibold mb-1">Estado</label>
              <Dropdown v-model="tabla.estado" :options="estados" optionLabel="label" optionValue="value" showClear placeholder="Todos" class="w-full" />
            </div>
            <div>
              <label class="block text-sm font-semibold mb-1">Programa</label>
              <Dropdown v-model="tabla.programa" :options="programasTabla" optionLabel="label" optionValue="value" showClear filter placeholder="Todos" class="w-full" />
            </div>
            <div>
              <label class="block text-sm font-semibold mb-1">Buscar</label>
              <InputText v-model="tabla.texto" placeholder="Código, DNI o estudiante" class="w-full" />
            </div>
          </div>

          <div class="text-sm text-gray-500">Mostrando {{ registrosFiltrados.length }} de {{ registros.length }} registro(s).</div>

          <DataTable
            :value="registrosFiltrados"
            class="p-datatable-sm"
            paginator
            :rows="50"
            :rowsPerPageOptions="[25, 50, 100, 200]"
            scrollable
            tableStyle="min-width: 95rem"
          >
            <Column field="codigo" header="Código" frozen />
            <Column field="dni" header="DNI" />
            <Column field="estudiante" header="Estudiante" style="min-width: 18rem" />
            <Column field="escuela" header="Escuela" style="min-width: 16rem" />
            <Column field="programa" header="Programa" style="min-width: 18rem" />
            <Column header="User" style="width: 6rem">
              <template #body="{ data }"><Tag :value="data.user ? 'Sí' : 'No'" :severity="data.user ? 'success' : 'secondary'" /></template>
            </Column>
            <Column header="Estudiante" style="width: 7rem">
              <template #body="{ data }"><Tag :value="data.t_estudiante ? 'Sí' : 'No'" :severity="data.t_estudiante ? 'success' : 'secondary'" /></template>
            </Column>
            <Column header="Matriz" style="width: 6rem">
              <template #body="{ data }"><Tag :value="data.t_matriz ? 'Sí' : 'No'" :severity="data.t_matriz ? 'success' : 'secondary'" /></template>
            </Column>
            <Column header="Datos ingreso" style="width: 8rem">
              <template #body="{ data }"><Tag :value="data.t_datos_ingreso ? 'Sí' : 'No'" :severity="data.t_datos_ingreso ? 'success' : 'secondary'" /></template>
            </Column>
            <Column field="estado" header="Estado" style="min-width: 10rem">
              <template #body="{ data }"><Tag :value="data.estado" :severity="severityEstado(data.estado)" /></template>
            </Column>
            <Column header="Detalle" style="min-width: 18rem">
              <template #body="{ data }">
                <span v-if="data.problemas?.length" class="text-xs text-red-700">{{ data.problemas.join(' | ') }}</span>
                <span v-else-if="data.faltantes?.length" class="text-xs text-amber-700">Falta: {{ data.faltantes.join(', ') }}</span>
                <span v-else class="text-xs text-green-700">Completo</span>
              </template>
            </Column>
          </DataTable>
        </div>
      </section>

      <Dialog v-model:visible="progreso.visible" modal header="Integración a Nivelación" :closable="!integrando" :style="{ width: '92vw', maxWidth: '850px' }">
        <div class="space-y-4">
          <div>
            <div class="flex justify-between text-sm mb-1">
              <span>{{ progreso.mensaje }}</span>
              <span>{{ progreso.porcentaje }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
              <div class="bg-blue-600 h-3 transition-all duration-300" :style="{ width: `${progreso.porcentaje}%` }"></div>
            </div>
          </div>

          <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <ResumenCard titulo="Programas" :valor="`${progreso.actual}/${progreso.total}`" />
            <ResumenCard titulo="Integrados" :valor="progreso.integrados" tono="success" />
            <ResumenCard titulo="Omitidos" :valor="progreso.omitidos" tono="warning" />
            <ResumenCard titulo="Errores" :valor="progreso.errores.length" tono="danger" />
          </div>

          <div v-if="progreso.programa" class="border rounded p-3 bg-gray-50 text-sm">
            Programa actual: <strong>{{ progreso.programa }}</strong>
          </div>

          <div v-if="progreso.errores.length" class="border border-red-200 bg-red-50 rounded p-3 max-h-52 overflow-auto text-sm">
            <div class="font-semibold text-red-800 mb-2">Errores encontrados</div>
            <div v-for="(item, i) in progreso.errores" :key="i" class="mb-1">
              • {{ item.programa }}: {{ item.mensaje }}
            </div>
          </div>
        </div>
      </Dialog>
    </div>
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

const props = defineProps({
  periodos: { type: Array, default: () => [] },
  escuelas: { type: Array, default: () => [] },
  programas: { type: Array, default: () => [] }
});

const toast = useToast();
const loading = ref(false);
const integrando = ref(false);
const cargado = ref(false);
const registros = ref([]);
const programasPendientes = ref([]);
const resumen = ref({ completos_admision: 0, pendientes: 0, integrados: 0, parciales: 0, conflictos: 0 });

const filtros = reactive({ id_periodo: null, ambito: 'todas', id_escuela: null, id_programa: null });
const tabla = reactive({ estado: null, programa: null, texto: '' });
const progreso = ref({ visible: false, porcentaje: 0, actual: 0, total: 0, programa: '', mensaje: '', integrados: 0, omitidos: 0, errores: [] });

const ambitos = [
  { label: 'Todo el período', value: 'todas' },
  { label: 'Una escuela', value: 'escuela' },
  { label: 'Un programa', value: 'programa' }
];
const estados = [
  { label: 'Pendiente', value: 'PENDIENTE' },
  { label: 'Integrado', value: 'INTEGRADO' },
  { label: 'Parcial', value: 'PARCIAL' },
  { label: 'Conflicto', value: 'CONFLICTO' }
];

const periodosOpciones = computed(() => props.periodos.map(p => ({ ...p, label: `${p.nombre}${String(p.estado).toLowerCase() === 'activo' ? ' - Activo' : ''}` })));
const escuelasOpciones = computed(() => props.escuelas.map(e => ({ label: e.nombre, value: Number(e.id) })));
const programasPorEscuela = computed(() => props.programas
  .filter(p => !filtros.id_escuela || Number(p.id_escuela) === Number(filtros.id_escuela))
  .map(p => ({ label: p.programa, value: Number(p.id) })));
const programasTabla = computed(() => {
  const mapa = new Map();
  registros.value.forEach(r => mapa.set(Number(r.id_programa), r.programa));
  return Array.from(mapa, ([value, label]) => ({ value, label })).sort((a,b) => a.label.localeCompare(b.label));
});
const puedeCargar = computed(() => {
  if (!filtros.id_periodo) return false;
  if (filtros.ambito === 'escuela') return !!filtros.id_escuela;
  if (filtros.ambito === 'programa') return !!filtros.id_programa;
  return true;
});
const puedeIntegrar = computed(() => puedeCargar.value && !integrando.value && Number(resumen.value.pendientes || 0) > 0);
const registrosFiltrados = computed(() => {
  const texto = String(tabla.texto || '').trim().toLowerCase();
  return registros.value.filter(r => {
    if (tabla.estado && r.estado !== tabla.estado) return false;
    if (tabla.programa && Number(r.id_programa) !== Number(tabla.programa)) return false;
    if (texto) {
      const base = `${r.codigo || ''} ${r.dni || ''} ${r.estudiante || ''} ${r.programa || ''}`.toLowerCase();
      if (!base.includes(texto)) return false;
    }
    return true;
  });
});

const ResumenCard = {
  props: ['titulo', 'valor', 'tono'],
  setup(p) {
    return () => h('div', { class: `border rounded-lg p-3 ${p.tono === 'success' ? 'bg-green-50' : p.tono === 'warning' ? 'bg-amber-50' : p.tono === 'danger' ? 'bg-red-50' : 'bg-gray-50'}` }, [
      h('div', { class: 'text-xs text-gray-500' }, p.titulo),
      h('div', { class: 'text-xl font-bold text-gray-800 mt-1' }, String(p.valor ?? 0))
    ]);
  }
};

const severityEstado = e => ({ PENDIENTE: 'info', INTEGRADO: 'success', PARCIAL: 'warning', CONFLICTO: 'danger' }[e] || 'secondary');
const notify = (severity, summary, detail) => toast.add({ severity, summary, detail, life: 5000 });

const paramsScope = () => ({
  id_periodo: filtros.id_periodo,
  ambito: filtros.ambito,
  id_escuela: filtros.id_escuela || null,
  id_programa: filtros.id_programa || null
});

const cargarDatos = async () => {
  if (!puedeCargar.value) return;
  loading.value = true;
  try {
    const { data } = await axios.get('/superadmi/integracion-nivelacion/datos', { params: paramsScope() });
    resumen.value = data.resumen || {};
    registros.value = data.registros || [];
    programasPendientes.value = data.programas_pendientes || [];
    cargado.value = true;
  } catch (e) {
    notify('error', 'ERROR', e.response?.data?.message || 'No se pudo cargar la integración.');
  } finally {
    loading.value = false;
  }
};

const cambioPeriodo = async () => {
  filtros.id_escuela = null;
  filtros.id_programa = null;
  cargado.value = false;
  registros.value = [];
  if (filtros.ambito === 'todas') await cargarDatos();
};
const cambioAmbito = async () => {
  filtros.id_escuela = null;
  filtros.id_programa = null;
  cargado.value = false;
  registros.value = [];
  if (filtros.ambito === 'todas' && filtros.id_periodo) await cargarDatos();
};
const cambioEscuelaPrograma = () => {
  filtros.id_programa = null;
  cargado.value = false;
  registros.value = [];
};
const limpiarFiltrosTabla = () => { tabla.estado = null; tabla.programa = null; tabla.texto = ''; };

const integrarPendientes = async () => {
  if (!puedeIntegrar.value) return;

  const objetivos = programasPendientes.value.filter(p => Number(p.pendientes || 0) > 0);
  if (!objetivos.length) return;

  integrando.value = true;
  progreso.value = { visible: true, porcentaje: 0, actual: 0, total: objetivos.length, programa: '', mensaje: 'Preparando integración...', integrados: 0, omitidos: 0, errores: [] };

  try {
    for (let i = 0; i < objetivos.length; i++) {
      const p = objetivos[i];
      progreso.value.actual = i + 1;
      progreso.value.programa = `${p.escuela} - ${p.programa}`;
      progreso.value.mensaje = `Integrando ${p.programa}...`;
      progreso.value.porcentaje = Math.round((i / objetivos.length) * 100);

      try {
        const { data } = await axios.post('/superadmi/integracion-nivelacion/integrar', {
          id_periodo: filtros.id_periodo,
          id_programa: p.id_programa
        });
        const d = data.datos || {};
        progreso.value.integrados += Number(d.integrados || 0);
        progreso.value.omitidos += Number(d.ya_integrados || 0) + Number(d.parciales || 0) + Number(d.conflictos || 0);
        (d.errores || []).forEach(err => progreso.value.errores.push({ programa: p.programa, mensaje: `${err.codigo || ''} ${err.dni || ''}: ${err.mensaje}` }));
      } catch (e) {
        progreso.value.errores.push({ programa: p.programa, mensaje: e.response?.data?.message || 'Error durante la integración.' });
      }

      progreso.value.porcentaje = Math.round(((i + 1) / objetivos.length) * 100);
    }

    progreso.value.programa = '';
    progreso.value.mensaje = progreso.value.errores.length ? 'Integración finalizada con observaciones.' : 'Integración completada correctamente.';
    progreso.value.porcentaje = 100;
    await cargarDatos();

    notify(
      progreso.value.errores.length ? 'warn' : 'success',
      progreso.value.errores.length ? 'INTEGRACIÓN CON OBSERVACIONES' : 'INTEGRACIÓN COMPLETADA',
      `${progreso.value.integrados} estudiante(s) integrado(s).`
    );
  } finally {
    integrando.value = false;
  }
};

onMounted(async () => {
  const activo = periodosOpciones.value.find(p => String(p.estado).toLowerCase() === 'activo');
  filtros.id_periodo = activo?.id_periodo ?? periodosOpciones.value[0]?.id_periodo ?? null;
  if (filtros.id_periodo) await cargarDatos();
});
</script>
