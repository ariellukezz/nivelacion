<template>
  <AuthenticatedLayout>
    <Head title="Búsqueda de Estudiantes" />

    <div class="p-4">
      <!-- Breadcrumb -->
      <Breadcrumb :model="breadcrumbItems" class="mb-4">
        <template #item="{ item }">
          <router-link :to="item.url" class="text-primary hover:text-primary-dark">
            <i :class="item.icon" class="mr-2"></i>
            {{ item.label }}
          </router-link>
        </template>
      </Breadcrumb>

      <h2 class="text-xl font-semibold mb-4">Búsqueda de Estudiantes</h2>

      <!-- Barra de búsqueda -->
      <div class="mb-6">
        <span class="p-input-icon-left w-full">
          <i class="pi pi-search" />
          <InputText
            v-model="searchTerm"
            placeholder="Buscar por código, DNI, nombres o apellidos..."
            class="w-full"
            @input="debouncedSearch"
          />
        </span>
      </div>

      <!-- Tabla de resultados -->
      <DataTable
        :value="filteredEstudiantes"
        :loading="loading"
        stripedRows
        paginator
        :rows="10"
        :rowsPerPageOptions="[10, 25, 50]"
        class="p-datatable-sm"
        currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} estudiantes"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
        @row-click="openDetails($event.data)"
      >
        <Column field="codigo_est" header="Código" sortable>
          <template #body="{ data }">
            <span class="cursor-pointer text-primary">{{ data.codigo_est }}</span>
          </template>
        </Column>
        <Column field="nombre_completo" header="Nombre Completo" sortable>
          <template #body="{ data }">
            <span class="cursor-pointer text-primary">
              {{ data.paterno }} {{ data.materno }}, {{ data.nombres }}
            </span>
          </template>
        </Column>

        <template #empty>
          <div class="text-center py-8">
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

      <!-- Modal para detalles -->
      <Dialog
        v-model:visible="showDetails"
        header="Detalles del Estudiante"
        :modal="true"
        :style="{ width: '50vw' }"
        :breakpoints="{ '960px': '75vw', '640px': '100vw' }"
      >
        <div v-if="selectedEstudiante" class="p-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <h3 class="text-lg font-semibold mb-2">Información Personal</h3>
              <p><strong>Código:</strong> {{ selectedEstudiante.codigo_est }}</p>
              <p><strong>DNI:</strong> {{ selectedEstudiante.dni }}</p>
              <p><strong>Nombre:</strong> {{ selectedEstudiante.nombres }} {{ selectedEstudiante.paterno }} {{ selectedEstudiante.materno }}</p>
              <p><strong>Sexo:</strong> {{ selectedEstudiante.sexo }}</p>
              <p><strong>Teléfono:</strong> {{ selectedEstudiante.telefono }}</p>
              <p><strong>Email:</strong> {{ selectedEstudiante.email }}</p>
              <p><strong>Dirección:</strong> {{ selectedEstudiante.direccion }}</p>
            </div>
            <div>
              <h3 class="text-lg font-semibold mb-2">Información Académica</h3>
              <p><strong>Semestre:</strong> {{ selectedEstudiante.semestre }}</p>
              <p><strong>Modalidad:</strong> {{ selectedEstudiante.modalidad }}</p>
              <p><strong>Tipo Examen:</strong> {{ selectedEstudiante.t_examen }}</p>
              <p><strong>Año:</strong> {{ selectedEstudiante.anio }}</p>
              <p><strong>Programa:</strong> {{ selectedEstudiante.nombre_programa }}</p>
              <p><strong>Escuela:</strong> {{ selectedEstudiante.nombre_escuela }}</p>
              <p><strong>Filial:</strong> {{ selectedEstudiante.filial }}</p>
            </div>
            <div class="col-span-2">
              <h3 class="text-lg font-semibold mb-2">Competencias</h3>
              <div class="grid grid-cols-2 gap-2">
                <p v-for="i in 11" :key="i">
                  <strong>Competencia {{ i }}:</strong> {{ selectedEstudiante[`competencia_${i}`] }}
                </p>
              </div>
            </div>
          </div>
        </div>
        <template #footer>
          <Button
            label="Cerrar"
            icon="pi pi-times"
            class="p-button-text"
            @click="showDetails = false"
          />
        </template>
      </Dialog>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/LayoutSuperadmi.vue';
import { Head } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import Breadcrumb from 'primevue/breadcrumb';

// Implementación manual de debounce
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

const estudiantes = ref([]);
const filteredEstudiantes = ref([]);
const searchTerm = ref('');
const loading = ref(false);
const showDetails = ref(false);
const selectedEstudiante = ref(null);

// Breadcrumb items
const breadcrumbItems = ref([
  { label: 'Inicio', icon: 'pi pi-home', url: '/' },
  { label: 'Supervisor', icon: 'pi pi-user', url: '/supervisor' },
  { label: 'Búsqueda de Estudiantes', icon: 'pi pi-search', url: '/estudiantes' }
]);

// Obtener datos de la API
const fetchEstudiantes = async (search = '') => {
  try {
    loading.value = true;
    const response = await axios.get('busqueda-estudiantes', {
      params: { search }
    });

    if (response.data.success) {
      estudiantes.value = response.data.data;
      filteredEstudiantes.value = response.data.data;
    } else {
      console.error('Error en la respuesta:', response.data.message);
    }
  } catch (error) {
    console.error('Error al obtener estudiantes:', error);
  } finally {
    loading.value = false;
  }
};

// Función de búsqueda con debounce
const debouncedSearch = debounce(() => {
  fetchEstudiantes(searchTerm.value);
}, 300);

// Mostrar detalles del estudiante
const openDetails = (estudiante) => {
  selectedEstudiante.value = estudiante;
  showDetails.value = true;
};

// Cargar datos iniciales
onMounted(() => fetchEstudiantes());
</script>

<style scoped>
/* Estilos mínimos para un look formal y presentable */
:deep(.p-datatable) {
  font-size: 0.9rem;
}

:deep(.p-datatable-thead > tr > th) {
  background-color: #f8fafc;
  color: #374151;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.75rem;
}

:deep(.p-paginator) {
  background-color: transparent;
  border: none;
}

:deep(.p-dialog-content) {
  padding: 1.5rem;
}

:deep(.p-dialog-header) {
  background-color: #f8fafc;
  color: #374151;
  font-weight: 600;
}

:deep(.p-breadcrumb) {
  background: transparent;
  border: none;
  padding: 0;
}

.text-primary {
  color: #000000; /* Color primario de PrimeVue */
}

.text-primary-dark {
  color: #000000; /* Tono más oscuro para hover */
}
</style>
