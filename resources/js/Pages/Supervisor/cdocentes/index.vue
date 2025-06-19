<template>
    <AuthenticatedLayout>
    <Head title="Docentes y Competencias" />

    <div class="p-4 bg-white rounded-lg shadow">
      <!-- Filtros -->
      <div class="mb-6 flex gap-4">
        <div class="flex-1">
          <label class="block mb-2">Programa</label>
          <Dropdown
            v-model="filtrosLocal.programa_id"
            :options="props.opcionesFiltros.programas"
            optionLabel="nombre"
            optionValue="id"
            placeholder="Todos los programas"
            class="w-full"
            @change="filtrarDatos"
          />
        </div>
        <div class="flex-1">
          <label class="block mb-2">Competencia</label>
          <Dropdown
            v-model="filtrosLocal.competencia_id"
            :options="props.opcionesFiltros.competencias"
            optionLabel="nombre"
            optionValue="id"
            placeholder="Todas las competencias"
            class="w-full"
            @change="filtrarDatos"
          />
        </div>
      </div>

      <!-- Tabla -->
      <Card>
        <DataTable
          :value="props.resultados"
          :paginator="true"
          :rows="10"
          stripedRows
        >
          <Column field="nombres" header="Nombres">
            <template #body="{ data }">
              {{ data.nombres }} {{ data.paterno }} {{ data.materno }}
            </template>
          </Column>
          <Column field="curso_nombre" header="Curso"></Column>
          <Column field="competencia_nombre" header="Competencia"></Column>
          <Column field="programa" header="Programa"></Column>
        </DataTable>
      </Card>
    </div>
</AuthenticatedLayout>
  </template>

  <script setup>
  import { Head } from '@inertiajs/vue3';
  import AuthenticatedLayout from '@/Layouts/LayoutSupervisor.vue';
  import { ref, watch } from 'vue';
  import { router } from '@inertiajs/vue3';
  import DataTable from 'primevue/datatable';
  import Column from 'primevue/column';
  import Dropdown from 'primevue/dropdown';
  import Card from 'primevue/card';

  defineOptions({
    layout: AuthenticatedLayout
  });

  const props = defineProps({
    resultados: {
      type: Array,
      required: true,
      default: () => []
    },
    filtros: {
      type: Object,
      default: () => ({})
    },
    opcionesFiltros: {
      type: Object,
      default: () => ({ programas: [], competencias: [] })
    }
  });

  const filtrosLocal = ref({
    programa_id: props.filtros.programa_id || null,
    competencia_id: props.filtros.competencia_id || null
  });

  const filtrarDatos = () => {
    router.get(route('supervisor-docentes-competencias'), {
      programa_id: filtrosLocal.value.programa_id,
      competencia_id: filtrosLocal.value.competencia_id
    }, {
      preserveState: true
    });
  };

  // Debug: Verificar datos
  console.log('Datos recibidos:', {
    resultados: props.resultados,
    filtros: props.filtros,
    opcionesFiltros: props.opcionesFiltros
  });
  </script>
