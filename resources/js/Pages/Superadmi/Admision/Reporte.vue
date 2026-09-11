<template>
  <Head title="Reportes de Integración Admisión" />

  <AuthenticatedLayout>
    <Toast />

    <div class="p-4 bg-white rounded-lg shadow-xs space-y-5">
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <div>
          <h2 class="text-xl font-bold text-gray-800">
            Reportes de Integración Admisión
          </h2>
          <p class="text-sm text-gray-500">
            Consulta detallada del cruce entre API, matriz y registros del Sistema de Nivelación.
          </p>
        </div>

        <Link href="/superadmi/admision-integracion">
          <Button
            label="Volver a integración"
            icon="pi pi-arrow-left"
            severity="secondary"
            outlined
          />
        </Link>
      </div>

      <div class="border rounded-lg p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-semibold mb-1">Periodo</label>
            <Dropdown
              v-model="filtros.id_periodo"
              :options="periodosOpciones"
              optionLabel="label"
              optionValue="id_periodo"
              placeholder="Seleccione periodo"
              class="w-full"
              @change="cambioPeriodo"
            />
          </div>

          <div>
            <label class="block text-sm font-semibold mb-1">Proceso de Admisión</label>
            <Dropdown
              v-model="filtros.id_proceso_admision"
              :options="procesosFiltrados"
              optionLabel="nombre"
              optionValue="id_admision"
              placeholder="Todos"
              showClear
              filter
              class="w-full"
            />
          </div>

          <div>
            <label class="block text-sm font-semibold mb-1">Programa</label>
            <Dropdown
              v-model="filtros.id_programa_admision"
              :options="programas"
              optionLabel="label"
              optionValue="id_admision"
              placeholder="Todos"
              showClear
              filter
              class="w-full"
            />
          </div>

          <div>
            <label class="block text-sm font-semibold mb-1">Estado</label>
            <Dropdown
              v-model="filtros.estado"
              :options="estados"
              placeholder="Todos"
              showClear
              class="w-full"
            />
          </div>

          <div>
            <label class="block text-sm font-semibold mb-1">DNI</label>
            <InputText
              v-model="filtros.dni"
              placeholder="Buscar DNI"
              class="w-full"
              @keyup.enter="buscar"
            />
          </div>

          <div>
            <label class="block text-sm font-semibold mb-1">Código</label>
            <InputText
              v-model="filtros.codigo"
              placeholder="Código API"
              class="w-full"
              @keyup.enter="buscar"
            />
          </div>

          <div>
            <label class="block text-sm font-semibold mb-1">
              Estudiante / programa / proceso
            </label>
            <InputText
              v-model="filtros.nombre"
              placeholder="Texto de búsqueda"
              class="w-full"
              @keyup.enter="buscar"
            />
          </div>

          <div>
            <label class="block text-sm font-semibold mb-1">
              Observación matriz
            </label>
            <Dropdown
              v-model="filtros.observacion"
              :options="opcionesObservacion"
              optionLabel="label"
              optionValue="value"
              placeholder="Todas"
              showClear
              class="w-full"
            />
          </div>
        </div>

        <div class="flex flex-wrap gap-2 mt-4">
          <Button
            label="Buscar"
            icon="pi pi-search"
            :disabled="!filtros.id_periodo"
            @click="buscar"
          />

          <Button
            label="Limpiar filtros"
            icon="pi pi-filter-slash"
            severity="secondary"
            outlined
            @click="limpiar"
          />
        </div>
      </div>

      <div
        v-if="filtros.id_periodo"
        class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3"
      >
        <ResumenCard titulo="Total filtrado" :valor="resumen.total" />
        <ResumenCard titulo="Listos" :valor="resumen.listos" />
        <ResumenCard titulo="Solo matriz" :valor="resumen.solo_matriz" />
        <ResumenCard titulo="Solo API" :valor="resumen.solo_api" />
        <ResumenCard titulo="Error programa" :valor="resumen.error_programa" />
        <ResumenCard titulo="Ya registrados" :valor="resumen.ya_registrado" />
      </div>

      <div class="border rounded-lg overflow-hidden">
        <div class="p-3 bg-gray-50 border-b flex flex-col md:flex-row md:items-center md:justify-between gap-2">
          <div class="font-semibold text-gray-800">
            Detalle del cruce
          </div>

          <div class="text-sm text-gray-500">
            {{ datos.total || 0 }} registro(s)
          </div>
        </div>

        <DataTable
          :value="datos.data"
          :loading="loading"
          class="p-datatable-sm"
          scrollable
          tableStyle="min-width: 90rem"
        >
          <Column field="dni" header="DNI" frozen />
          <Column field="codigo" header="Código" />
          <Column field="estudiante" header="Estudiante" style="min-width: 18rem" />
          <Column field="programa" header="Programa Nivelación" style="min-width: 16rem" />
          <Column field="programa_admision" header="Programa Admisión" style="min-width: 16rem" />
          <Column field="proceso_nombre" header="Proceso" style="min-width: 16rem" />
          <Column field="observacion_matriz" header="Observación matriz" style="min-width: 18rem" />
          <Column field="estado_cruce" header="Estado">
            <template #body="{ data }">
              <Tag
                :value="data.estado_cruce"
                :severity="severityEstado(data.estado_cruce)"
              />
            </template>
          </Column>
        </DataTable>

        <div
          v-if="datos.last_page > 1"
          class="flex flex-wrap justify-end items-center gap-2 p-3 border-t"
        >
          <Button
            icon="pi pi-angle-double-left"
            text
            :disabled="datos.current_page <= 1"
            @click="cargar(1)"
          />

          <Button
            icon="pi pi-angle-left"
            text
            :disabled="datos.current_page <= 1"
            @click="cargar(datos.current_page - 1)"
          />

          <span class="text-sm">
            Página {{ datos.current_page }} de {{ datos.last_page }}
          </span>

          <Button
            icon="pi pi-angle-right"
            text
            :disabled="datos.current_page >= datos.last_page"
            @click="cargar(datos.current_page + 1)"
          />

          <Button
            icon="pi pi-angle-double-right"
            text
            :disabled="datos.current_page >= datos.last_page"
            @click="cargar(datos.last_page)"
          />
        </div>
      </div>
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
import Dropdown from 'primevue/dropdown';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
  periodos: {
    type: Array,
    default: () => []
  }
});

const toast = useToast();
const loading = ref(false);
const procesos = ref([]);
const programas = ref([]);

const filtros = reactive({
  id_periodo: null,
  id_proceso_admision: null,
  id_programa_admision: null,
  estado: null,
  dni: '',
  codigo: '',
  nombre: '',
  observacion: null
});

const estados = [
  'LISTO',
  'SOLO MATRIZ',
  'SOLO API',
  'ERROR PROGRAMA',
  'YA REGISTRADO'
];

const opcionesObservacion = [
  { label: 'Con observación', value: 'si' },
  { label: 'Sin observación', value: 'no' }
];

const resumen = ref({
  total: 0,
  listos: 0,
  solo_matriz: 0,
  solo_api: 0,
  error_programa: 0,
  ya_registrado: 0
});

const datos = ref({
  data: [],
  total: 0,
  current_page: 1,
  last_page: 1
});

const periodosOpciones = computed(() =>
  props.periodos.map(x => ({
    ...x,
    label: `ID ${x.id_periodo} - ${x.nombre}`
  }))
);

const procesosFiltrados = computed(() => {
  if (!filtros.id_periodo) return procesos.value;

  return procesos.value.filter(
    x => Number(x.id_periodo) === Number(filtros.id_periodo)
  );
});

const ResumenCard = {
  props: ['titulo', 'valor'],
  setup(props) {
    return () => h(
      'div',
      { class: 'border rounded-lg p-3 bg-gray-50' },
      [
        h('div', { class: 'text-xs text-gray-500' }, props.titulo),
        h(
          'div',
          { class: 'text-xl font-bold text-gray-800 mt-1' },
          String(props.valor ?? 0)
        )
      ]
    );
  }
};

const severityEstado = estado => ({
  'LISTO': 'success',
  'SOLO MATRIZ': 'warning',
  'SOLO API': 'info',
  'ERROR PROGRAMA': 'danger',
  'YA REGISTRADO': 'secondary'
}[estado] || 'info');

const cargarCatalogos = async () => {
  const [respProcesos, respProgramas] = await Promise.all([
    axios.get('/superadmi/admision/procesos'),
    axios.get('/superadmi/admision/programas')
  ]);

  procesos.value = respProcesos.data.datos || [];

  programas.value = (respProgramas.data.datos || [])
    .filter(x => x.vinculado)
    .map(x => ({
      ...x,
      label: `${x.id_admision} - ${x.nombre_admision}`
    }));
};

const cambioPeriodo = () => {
  filtros.id_proceso_admision = null;
  buscar();
};

const params = page => ({
  ...filtros,
  page,
  per_page: 50
});

const cargar = async (page = 1) => {
  if (!filtros.id_periodo) return;

  loading.value = true;

  try {
    const { data } = await axios.get(
      '/superadmi/admision/reporte-data',
      { params: params(page) }
    );

    resumen.value = data.resumen;
    datos.value = data.datos;
  } catch (e) {
    toast.add({
      severity: 'error',
      summary: 'ERROR',
      detail:
        e.response?.data?.message ||
        'No se pudo cargar el reporte.',
      life: 4000
    });
  } finally {
    loading.value = false;
  }
};

const buscar = () => cargar(1);

const limpiar = () => {
  filtros.id_proceso_admision = null;
  filtros.id_programa_admision = null;
  filtros.estado = null;
  filtros.dni = '';
  filtros.codigo = '';
  filtros.nombre = '';
  filtros.observacion = null;

  if (filtros.id_periodo) {
    buscar();
  }
};

onMounted(async () => {
  await cargarCatalogos();

  if (periodosOpciones.value.length) {
    filtros.id_periodo = periodosOpciones.value[0].id_periodo;
    await buscar();
  }
});
</script>
