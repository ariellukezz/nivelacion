<template>
  <Head title="Alumnos" />
  <AuthenticatedLayout>
    <div class="bg-white shadow-xs p-4" style="height: calc(100vh - 110px); font-family: Arial, Helvetica, sans-serif;">



      <div class="flex" style="justify-content: space-between;">
        <span class="p-input-icon-left">
                <i class="pi pi-search" />
                <InputText v-model="buscar" placeholder="Search" />
            </span>
      </div>


      <DataTable :value="alumnos" :class="'p-datatable-sm'" paginator :rows="10" tableStyle="min-width: 50rem" style="font-size: 0.8rem;">
        <Column field="dni" header="Dni"></Column>
        <Column field="codigo" header="codigo"></Column>
        <Column v-if="conf_codigo === true" field="Codigo" header="Código"></Column>
        <Column field="nombres" header="Nombres">
          <template #body="{ data }">
            <div class="flex" style="justify-content: flex-start;">
              <div>
                {{ data.nombres }}, {{ data.paterno }} {{ data.materno }}
              </div>
            </div>
          </template>
        </Column>
        <Column field="sexo" header="Sexo"></Column>
        <Column field="email" header="Correo"></Column>
        <Column field="telefono" header="Teléfono"></Column>
        <Column field="programa" header="Programa"></Column>
        <Column field="modalidad" header="modalidad"></Column>
        

        <Column field="programa" header="Ver" width="90px">
          <template #body="{ data }">
            <div class="flex">
              <Button class="" severity="help" icon="pi pi-eye" aria-label="Submit" @click="verDetalles(data)" size="small" style="width: 25px; height: 25px;" />
            </div>
          </template>
        </Column>
      </DataTable>
    </div>
  </AuthenticatedLayout>
  
  <Dialog v-model:visible="visible" maximizable modal header="Detalles del Alumno" :style="{ width: '50vw' }">
    <DataTable :value="alumnoSeleccionado" :class="'p-datatable-sm'" style="font-size: 0.8rem;">
      <Column field="dni" header="Dni"></Column>
      <Column field="nombre_colegio" header="Nombre del Colegio"></Column>
      <Column field="anio_egreso" header="anio_egreso"></Column>
      <Column field="semestre" header="semestre"></Column>
      <Column field="nombre" header="Nombre"></Column>
        <Column field="nota" header="Notas"></Column>
        <Column field="C1_R" header="C1 R"></Column>

    </DataTable>
  </Dialog>
</template>

<script setup>

import InputText from 'primevue/inputtext';

import MultiSelect from 'primevue/multiselect';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import AuthenticatedLayout from '@/Layouts/LayoutSuperadmi.vue';
import { Head } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { ref } from 'vue';

const alumnos = ref([]);
const buscar = ref("")





const getAlumnos = async () => {
  // Aquí puedes realizar una llamada a tu API para obtener datos reales de alumnos
  // Reemplaza los datos de ejemplo con los datos reales
  const res = await axios.get("getAlumnosc/1");
  alumnos.value = res.data.datos;
};

const visible = ref(false);
const alumnoSeleccionado = ref([]);

const verDetalles = (data) => {
  // Mostrar el cuadro de diálogo con los detalles del alumno
  visible.value = true;
  // Establecer el alumno seleccionado para mostrar en la tabla de detalles
  alumnoSeleccionado.value = [data]; // Crear un arreglo con un solo alumno (el seleccionado)
};

getAlumnos();
</script>