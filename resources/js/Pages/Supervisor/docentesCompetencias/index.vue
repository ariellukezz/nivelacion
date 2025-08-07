<template>
  <AuthenticatedLayout>
    <Head title="Docentes por Competencias" />

    <div class="p-4 bg-white rounded-lg shadow">
      <h2 class="text-xl font-semibold mb-4">Docentes por Competencias</h2>

      <!-- Filtros y búsqueda -->
      <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Barra de búsqueda -->
        <span class="p-input-icon-left">
          <i class="pi pi-search" />
          <InputText
            v-model="searchTerm"
            placeholder="Buscar docentes..."
            class="w-full"
            @input="filterDocentes"
          />
        </span>

        <!-- Filtro de Programa -->
        <Dropdown
          v-model="selectedPrograma"
          :options="programas"
          optionLabel="programa"
          optionValue="programa_id"
          placeholder="Seleccionar Programa"
          class="w-full"
          @change="filterDocentes"
          showClear
        />

        <!-- Filtro de Periodo -->
        <Dropdown
          v-model="selectedPeriodo"
          :options="periodos"
          optionLabel="periodo_nombre"
          optionValue="id_periodo"
          placeholder="Seleccionar Periodo"
          class="w-full"
          @change="filterDocentes"
          showClear
        />

        <!-- Filtro de Competencia -->
        <Dropdown
          v-model="selectedCompetencia"
          :options="competencias"
          optionLabel="competencia_nombre"
          optionValue="id_competencia"
          placeholder="Seleccionar Competencia"
          class="w-full"
          @change="filterDocentes"
          showClear
        />
      </div>

      <!-- Botón para limpiar filtros -->
      <div class="mb-4">
        <Button
          label="Limpiar Filtros"
          icon="pi pi-times"
          class="p-button-outlined p-button-sm"
          @click="clearFilters"
        />
      </div>

      <!-- Tabla de PrimeVue -->
      <DataTable
        :value="filteredDocentes"
        :loading="loading"
        stripedRows
        paginator
        :rows="10"
        :rowsPerPageOptions="[10, 25, 50]"
        class="p-datatable-sm"
        currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} docentes"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
      >
        <Column field="docente" header="Docente" sortable>
          <template #body="{ data }">
            <div class="font-medium">{{ data.paterno }} {{ data.materno }}, {{ data.nombres }}</div>
            <div class="text-sm text-gray-500">{{ data.email }}</div>
            <div class="text-sm text-gray-500">{{ data.telefono }}</div>
          </template>
        </Column>

        <Column field="competencia" header="Competencia" sortable>
          <template #body="{ data }">
            <span class="font-semibold">{{ data.competencia_cod }}</span> - {{ data.competencia_nombre }}
          </template>
        </Column>

        <Column field="curso" header="Curso" sortable>
          <template #body="{ data }">
            {{ data.curso_nombre }} <span class="text-gray-400">(Grupo {{ data.grupo }})</span>
          </template>
        </Column>

        <Column field="programa" header="Programa" sortable></Column>

        <Column field="periodo" header="Periodo" sortable>
          <template #body="{ data }">
            {{ data.periodo_nombre }}
          </template>
        </Column>

        <template #empty>
          <div class="text-center py-8 text-gray-500">
            <i class="pi pi-info-circle mr-2"></i>
            No se encontraron resultados
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
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/LayoutSupervisor.vue';
import { Head } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import Button from 'primevue/button';

const docentes = ref([]);
const filteredDocentes = ref([]);
const searchTerm = ref('');
const selectedPrograma = ref(null);
const selectedPeriodo = ref(null);
const selectedCompetencia = ref(null);
const programas = ref([]);
const periodos = ref([]);
const competencias = ref([]);
const loading = ref(true);

// Obtener datos de la API
const fetchDocentes = async () => {
  try {
    loading.value = true;
    const response = await axios.get('docentes-competencias-data'); // Cambia la URL aquí
    if (response.data.success) {
      docentes.value = response.data.data.docentes;
      filteredDocentes.value = response.data.data.docentes;
      programas.value = response.data.data.filtros.programas;
      periodos.value = response.data.data.filtros.periodos;
      competencias.value = response.data.data.filtros.competencias;
    } else {
      console.error('Error en la respuesta:', response.data.message);
    }
  } catch (error) {
    console.error('Error al obtener docentes:', error);
  } finally {
    loading.value = false;
  }
};

// Filtrar docentes
const filterDocentes = () => {
  let filtered = docentes.value;

  // Filtrar por búsqueda
  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase();
    filtered = filtered.filter(docente => (
      (docente.paterno && docente.paterno.toLowerCase().includes(term)) ||
      (docente.materno && docente.materno.toLowerCase().includes(term)) ||
      (docente.nombres && docente.nombres.toLowerCase().includes(term)) ||
      (docente.email && docente.email.toLowerCase().includes(term)) ||
      (docente.telefono && docente.telefono.toLowerCase().includes(term)) ||
      (docente.competencia_nombre && docente.competencia_nombre.toLowerCase().includes(term)) ||
      (docente.curso_nombre && docente.curso_nombre.toLowerCase().includes(term)) ||
      (docente.programa && docente.programa.toLowerCase().includes(term)) ||
      (docente.periodo_nombre && docente.periodo_nombre.toLowerCase().includes(term))
    ));
  }

  // Filtrar por programa
  if (selectedPrograma.value) {
    filtered = filtered.filter(docente => docente.programa_id === selectedPrograma.value);
  }

  // Filtrar por periodo
  if (selectedPeriodo.value) {
    filtered = filtered.filter(docente => docente.id_periodo === selectedPeriodo.value);
  }

  // Filtrar por competencia
  if (selectedCompetencia.value) {
    filtered = filtered.filter(docente => docente.id_competencia === selectedCompetencia.value);
  }

  filteredDocentes.value = filtered;
};

// Limpiar filtros
const clearFilters = () => {
  searchTerm.value = '';
  selectedPrograma.value = null;
  selectedPeriodo.value = null;
  selectedCompetencia.value = null;
  filteredDocentes.value = docentes.value;
};

// Cargar datos al montar el componente
onMounted(fetchDocentes);
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
