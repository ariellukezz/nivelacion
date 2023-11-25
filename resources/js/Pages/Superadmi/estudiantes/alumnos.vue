<template>
  <Head title="Alumnos" />
  <AuthenticatedLayout>
    <div class="bg-white shadow-xs p-4" style="height: calc(100vh - 110px); font-family: Arial, Helvetica, sans-serif;">
      <div class="flex" style="justify-content: space-between;">
        <span class="p-input-icon-left">
                <i class="pi pi-search" />
                <InputText v-model="buscar" placeholder="Search"/>
        </span>

        <div class="flex" style="width: 100%; justify-content: space-between;">
            <div class="mb-3" style="width: 240px;">
               
              <Dropdown v-model="compes" :options="compeselect" filter optionLabel="label" optionValue="value"  placeholder="Competencias" style="width:100%;" class="w-full md:w-11rem">            
                <template #option="slotProps">
                    <div class="flex align-items-center" style="width: 400px; font-size:0.9rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                        <div>{{ slotProps.option.label }}</div>
                    </div>
                </template>
              </Dropdown>
            </div>
        </div>

        
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
import { ref, watch } from 'vue';
import 'primeicons/primeicons.css';
import Dropdown from 'primevue/dropdown';


const alumnos = ref([]);
const buscar = ref("")

const totalpaginas = ref(0)
const pagina = ref(1)

const compes = ref(1);

// const getAlumnos =  async (event) => {
//   let res = await axios.post(
//   "getAlumnosc",{competencia:1,buscar:buscar.value}
//   );
//   alumnos.value = res.data.datos;
// }

watch(buscar, (newValue, oldValue) => { getAlumnos(); })
watch(compes, (newValue, oldValue) => { getAlumnos(); })


const getAlumnos =  async (event) => {
  let res = await axios.post(
  "getAlumnosc",{ page: pagina.value, competencia:compes.value, buscar:buscar.value}
  );
  alumnos.value = res.data.datos;
  totalpaginas.value = res.data.datos;
}


watch(buscar, ( newValue, oldValue ) => {
   getAlumnos()
})

const compeselect = ref([
        {value:1, label:"C1"},
        {value:2, label:"C2"},
        {value:3, label:"C3"},
        {value:4, label:"C4"},
        {value:5, label:"C5"},
        {value:6, label:"C6"},
        {value:7, label:"C7"},
        {value:8, label:"C8"},
        {value:9, label:"C9"},
        {value:10, label:"C10"},
        {value:11, label:"C11"},

  ])

// const getAlumnos = async () => {
//   // Aquí puedes realizar una llamada a tu API para obtener datos reales de alumnos
//   // Reemplaza los datos de ejemplo con los datos reales
//   const res = await axios.get("getAlumnosc/1");
//   alumnos.value = res.data.datos;
// };

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