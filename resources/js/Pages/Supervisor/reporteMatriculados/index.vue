<template>
  <AuthenticatedLayout>
    <Head title="Reporte de Matriculados por Competencia" />

    <div class="p-4 bg-white rounded-lg shadow">
      <h2 class="text-xl font-semibold mb-4">Reporte de Matriculados por Competencia</h2>

      <!-- Filtros -->
      <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Filtro de Programa -->
        <Dropdown
          v-model="selectedPrograma"
          :options="programas"
          :optionLabel="getProgramaLabel"
          optionValue="id"
          placeholder="Todos los programas"
          class="w-full"
          @change="fetchReporte"
          showClear
          filter
        />

        <!-- Filtro de Competencia -->
        <Dropdown
          v-model="selectedCompetencia"
          :options="competencias"
          optionLabel="label"
          optionValue="value"
          placeholder="Todas las competencias"
          class="w-full"
          @change="fetchReporte"
          showClear
          filter
        />

        <!-- Filtro de Filial -->
        <Dropdown
          v-model="selectedFilial"
          :options="filiales"
          optionLabel="filial"
          optionValue="filial"
          placeholder="Todas las filiales"
          class="w-full"
          @change="fetchReporte"
          showClear
          filter
        />

        <!-- Filtro de Periodo -->
        <Dropdown
          v-model="selectedPeriodo"
          :options="periodos"
          optionLabel="nombre"
          optionValue="id_periodo"
          placeholder="Seleccionar Periodo"
          class="w-full"
          @change="fetchReporte"
          showClear
          filter
        />
      </div>

      <!-- Botones de acción -->
      <div class="mb-4 flex items-center gap-3">
        <Button
          label="Limpiar Filtros"
          icon="pi pi-times"
          class="p-button-outlined p-button-sm"
          @click="clearFilters"
        />
        <Button
          icon="pi pi-refresh"
          label="Actualizar"
          class="p-button-outlined p-button-sm"
          @click="fetchReporte"
          :loading="loading"
        />
        <Button
          icon="pi pi-file-excel"
          label="Exportar Excel"
          severity="success"
          :loading="exportLoading"
          @click="exportarExcel"
        />
      </div>

      <!-- Estadísticas rápidas -->
      <div v-if="datos.length > 0" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
          <div class="text-blue-600 font-semibold">Total Registros</div>
          <div class="text-2xl font-bold text-blue-700">{{ totalRegistros }}</div>
        </div>
        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
          <div class="text-green-600 font-semibold">Programas</div>
          <div class="text-2xl font-bold text-green-700">{{ programasUnicos }}</div>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
          <div class="text-purple-600 font-semibold">Competencias</div>
          <div class="text-2xl font-bold text-purple-700">{{ competenciasUnicas }}</div>
        </div>
        <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
          <div class="text-orange-600 font-semibold">Periodo</div>
          <div class="text-2xl font-bold text-orange-700">{{ periodoFiltrado }}</div>
        </div>
      </div>

      <!-- Tabla de datos -->
      <DataTable
        :value="datos"
        :loading="loading"
        stripedRows
        paginator
        :rows="11"
        :rowsPerPageOptions="[10, 25, 50, 100]"
        class="p-datatable-sm"
        currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} registros"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
      >
        <Column field="programa" header="Programa" sortable>
          <template #body="{ data }">
            <div class="font-medium">{{ data.programa }}</div>
            <div class="text-sm text-gray-500">{{ data.filial }}</div>
          </template>
        </Column>

        <Column field="competencia_nombre" header="Competencia" sortable>
          <template #body="{ data }">
            <div class="font-semibold text-sm">{{ data.competencia_codigo }}</div>
            <div class="text-xs text-gray-600">{{ data.competencia_nombre }}</div>
          </template>
        </Column>

        <Column field="docente_nombre" header="Docente" sortable>
          <template #body="{ data }">
            <span :class="data.docente_nombre === 'SIN DOCENTE' ? 'text-red-600 font-semibold' : 'text-gray-700'">
              {{ data.docente_nombre }}
            </span>
          </template>
        </Column>

        <Column field="total_matriculados" header="Matriculados" sortable>
          <template #body="{ data }">
            <div class="text-center font-semibold">{{ data.total_matriculados }}</div>
          </template>
        </Column>

        <Column field="estudiantes_con_nota" header="Con Nota" sortable>
          <template #body="{ data }">
            <div class="text-center text-green-600 font-semibold">
              {{ data.estudiantes_con_nota }}
            </div>
          </template>
        </Column>

        <Column field="estudiantes_sin_nota" header="Sin Nota" sortable>
          <template #body="{ data }">
            <div class="text-center text-red-600 font-semibold">
              {{ data.estudiantes_sin_nota }}
            </div>
          </template>
        </Column>

        <Column field="porcentaje_con_nota" header="% Con Nota" sortable>
          <template #body="{ data }">
            <div class="text-center">
              <span
                :class="{
                  'text-green-600 font-semibold': data.porcentaje_con_nota >= 80,
                  'text-yellow-600 font-semibold': data.porcentaje_con_nota >= 50 && data.porcentaje_con_nota < 80,
                  'text-red-600 font-semibold': data.porcentaje_con_nota < 50
                }"
              >
                {{ data.porcentaje_con_nota }}%
              </span>
            </div>
          </template>
        </Column>

        <template #empty>
          <div class="text-center py-8 text-gray-500">
            <i class="pi pi-info-circle mr-2"></i>
            No se encontraron resultados para los filtros seleccionados
          </div>
        </template>

        <template #loading>
          <div class="text-center py-8">
            <i class="pi pi-spinner pi-spin mr-2"></i>
            Cargando datos...
          </div>
        </template>
      </DataTable>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/LayoutSupervisor.vue';
import { Head } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dropdown from 'primevue/dropdown';
import Button from 'primevue/button';

// Datos reactivos
const datos = ref([]);
const loading = ref(false);
const exportLoading = ref(false);
const programas = ref([]);
const competencias = ref([]);
const filiales = ref([]);
const periodos = ref([]);

// Filtros
const selectedPrograma = ref(null);
const selectedCompetencia = ref(null);
const selectedFilial = ref(null);
const selectedPeriodo = ref(null);
const periodoFiltrado = ref(null);

// Función para mostrar "Programa (Filial)" en el dropdown
const getProgramaLabel = (programa) => {
  return `${programa.programa} (${programa.filial})`;
};

// Obtener datos del reporte
const fetchReporte = async () => {
  try {
    loading.value = true;

    const params = {};
    if (selectedPrograma.value) params.programa = selectedPrograma.value;
    if (selectedCompetencia.value) params.competencia = selectedCompetencia.value;
    if (selectedFilial.value) params.filial = selectedFilial.value;
    if (selectedPeriodo.value) params.periodo = selectedPeriodo.value;

    const response = await axios.get('reporte-matriculados', { params });

    if (response.data.estado) {
      datos.value = response.data.datos;
      periodoFiltrado.value = response.data.periodo_filtrado;
    } else {
      console.error('Error en la respuesta:', response.data.mensaje);
    }
  } catch (error) {
    console.error('Error al obtener el reporte:', error);
  } finally {
    loading.value = false;
  }
};

// Obtener datos para filtros
const fetchFiltros = async () => {
  try {
    // Obtener programas
    const programasResponse = await axios.get('get-programas-filial');
    if (programasResponse.data.estado && Array.isArray(programasResponse.data.programas)) {
      programas.value = programasResponse.data.programas;
      // Extraer filiales únicas de los programas
      filiales.value = [...new Set(programasResponse.data.programas.map(p => p.filial))].map(filial => ({ filial }));
    } else {
      programas.value = [];
    }

    // Obtener competencias
    const competenciasResponse = await axios.post('get-competencias');
    if (competenciasResponse.data.estado) {
      competencias.value = competenciasResponse.data.datos.data || [];
    }

    // Obtener periodos
    const periodosResponse = await axios.get('obtener-periodos');
    if (periodosResponse.data) {
      periodos.value = periodosResponse.data;
    }

  } catch (error) {
    console.error('Error al obtener filtros:', error);
  }
};

// Limpiar filtros
const clearFilters = () => {
  selectedPrograma.value = null;
  selectedCompetencia.value = null;
  selectedFilial.value = null;
  selectedPeriodo.value = null;
  fetchReporte();
};

// Exportar a Excel
let XLSX = null;
const exportarExcel = async () => {
  try {
    exportLoading.value = true;

    if (!XLSX) {
      XLSX = await import('xlsx');
    }

    const rows = datos.value || [];
    if (!rows.length) {
      console.warn('No hay datos para exportar');
      exportLoading.value = false;
      return;
    }

    const dataForExcel = rows.map(r => ({
      'Programa': r.programa || '',
      'Filial': r.filial || '',
      'Código Competencia': r.competencia_codigo || '',
      'Competencia': r.competencia_nombre || '',
      'Docente': r.docente_nombre || '',
      'Total Matriculados': r.total_matriculados || 0,
      'Estudiantes con Nota': r.estudiantes_con_nota || 0,
      'Estudiantes sin Nota': r.estudiantes_sin_nota || 0,
      'Porcentaje con Nota': `${r.porcentaje_con_nota || 0}%`
    }));

    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.json_to_sheet(dataForExcel, { skipHeader: false });

    const headers = Object.keys(dataForExcel[0]);
    const colWidths = headers.map(h => {
      const maxLen = Math.max(
        h.length,
        ...dataForExcel.map(row => String(row[h] ?? '').length)
      );
      return { wch: Math.min(Math.max(maxLen + 2, 12), 60) };
    });
    ws['!cols'] = colWidths;

    XLSX.utils.book_append_sheet(wb, ws, 'Matriculados');

    const now = new Date();
    const pad = n => String(n).padStart(2, '0');
    const stamp = `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}_${pad(now.getHours())}${pad(now.getMinutes())}`;

    XLSX.writeFile(wb, `reporte_matriculados_${stamp}.xlsx`);
  } catch (e) {
    console.error('Error al exportar:', e);
  } finally {
    exportLoading.value = false;
  }
};

// Computed properties para estadísticas
const totalRegistros = computed(() => datos.value.length);
const programasUnicos = computed(() => new Set(datos.value.map(d => d.programa_id)).size);
const competenciasUnicas = computed(() => new Set(datos.value.map(d => d.competencia_id)).size);

// Cargar datos al montar el componente
onMounted(() => {
  fetchFiltros();
  fetchReporte();
});
</script>

<style scoped>
:deep(.p-datatable) {
  font-size: 0.9rem;
}

:deep(.p-datatable-thead > tr > th) {
  background-color: #f8fafc;
  color: #64748b;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.05em;
}

:deep(.p-paginator) {
  background-color: transparent;
  border: none;
}
</style>
