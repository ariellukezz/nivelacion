<template>
    <Head title="Estudiantes" />
    <AuthenticatedLayout>
      <div class="bg-white shadow-xs p-2" style="height: calc(100vh - 90px); font-family: Arial, Helvetica, sans-serif;">
        <div class="flex justify-between mb-2">
          <span class="p-input-icon-left" style="font-size: 0.9rem;">
            <i class="pi pi-search" />
            <InputText v-model="buscar" placeholder="Buscar por DNIcoddd o Nombres" style="font-size: 0.9rem;" />
          </span>
        </div>

        <!-- Tabla de Estudiantes -->
        <DataTable :value="estudiantes" :class="'p-datatable-sm'" paginator :rows="10" tableStyle="min-width: 50rem; font-size: 0.75rem;">
          <Column field="codigo_est" header="codigo_est"></Column>
          <!-- <Column field="dni" header="DNI"></Column> -->
          <Column field="nombres" header="Nombres">
            <template #body="{ data }">
              {{ data.nombres }}, {{ data.paterno }} {{ data.materno }}
            </template>
          </Column>
          <Column field="sexo" header="Sexo"></Column>
          <Column field="telefono" header="Teléfono"></Column>
          <Column field="email" header="Correo"></Column>
          <Column field="direccion" header="Dirección"></Column>
          <Column field="semestre" header="Semestre"></Column>
          <Column field="modalidad" header="Modalidad"></Column>
          <Column field="anio" header="Año"></Column>
          <Column field="nombre_programa" header="Programa"></Column>
          <Column field="nombre_escuela" header="Escuela"></Column>
          <Column field="competencia_1" header="C1"></Column>
          <Column field="competencia_2" header="C2"></Column>
          <Column field="competencia_3" header="C3"></Column>
          <Column field="competencia_4" header="C4"></Column>
          <Column field="competencia_5" header="C5"></Column>
          <Column field="competencia_6" header="C6"></Column>
          <Column field="competencia_7" header="C7"></Column>
          <Column field="competencia_8" header="C8"></Column>
          <Column field="competencia_9" header="C9"></Column>
          <Column field="competencia_10" header="C10"></Column>
          <Column field="competencia_11" header="C11"></Column>
        </DataTable>
      </div>
    </AuthenticatedLayout>
  </template>

  <script setup>
  import { ref, watch } from 'vue';
  import axios from 'axios';
  import InputText from 'primevue/inputtext';
  import DataTable from 'primevue/datatable';
  import Column from 'primevue/column';
  import AuthenticatedLayout from '@/Layouts/LayoutSuperadmi.vue';

  // Variables reactivas
  const estudiantes = ref([]);  // Aquí se almacenan los datos de los estudiantes
  const buscar = ref("");  // Variable para almacenar el término de búsqueda

  // Función para obtener los estudiantes desde el backend
  const getEstudiantes = async () => {
    try {
      const response = await axios.get('get-estudiantes', {
        buscar: buscar.value  // Enviar el término de búsqueda al backend
      });
      estudiantes.value = response.data.datos;  // Guardamos los resultados de la búsqueda en 'estudiantes'
    } catch (error) {
      console.error('Error al obtener los estudiantes:', error);
    }
  };

  // Ejecutar la búsqueda cada vez que el valor de 'buscar' cambie
  watch(buscar, () => {
    getEstudiantes();  // Llamamos a la función cada vez que el usuario escribe en el campo de búsqueda
  });

  // Obtener los estudiantes al cargar la página por primera vez
  getEstudiantes();

  </script>
