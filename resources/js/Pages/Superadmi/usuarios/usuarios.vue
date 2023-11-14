<template>
  <Head title="Usuarios" />
  <AuthenticatedLayout>
    <div class="bg-white shadow-xs p-4" style="height: calc(100vh - 110px); font-family: Arial, Helvetica, sans-serif;">



      <div class="flex" style="justify-content: space-between;">
        <span class="p-input-icon-left">
                <i class="pi pi-search" />
                <InputText v-model="buscar" placeholder="Search"/>
        </span>

    <div>
      <pre>
        {{ usuarios }}
         </pre>
    </div>
    </div>
      <DataTable :value="usuarios" :class="'p-datatable-sm'" paginator :rows="10" tableStyle="min-width: 50rem" style="font-size: 0.8rem;">
        <Column field="paterno" header="xxxx"></Column>
        <Column field="materno" header="xxx"></Column>
        <Column field="email" header="email"></Column>
        
      </DataTable>
    </div>
  </AuthenticatedLayout>
  
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

const usuario = ref([]);
const usuarios = ref([]);
const alumnos = ref([]);
const buscar = ref("")

const totalpaginas = ref(0)
const pagina = ref(1)


// const getAlumnos =  async (event) => {
//   let res = await axios.post(
//   "getAlumnosc",{competencia:1,buscar:buscar.value}
//   );
//   alumnos.value = res.data.datos;
// }


const getUsuarios =  async (event) => {
  let res = await axios.post(
  "getUsuarios",{ page: pagina.value, buscar:buscar.value}
  );
  usuarios.value = res.data.datos;
  totalpaginas.value = res.data.datos;
}


watch(buscar, ( newValue, oldValue ) => {
  getUsuarios()
})


// const getAlumnos = async () => {
//   // Aquí puedes realizar una llamada a tu API para obtener datos reales de alumnos
//   // Reemplaza los datos de ejemplo con los datos reales
//   const res = await axios.get("getAlumnosc/1");
//   alumnos.value = res.data.datos;
// };

const visible = ref(false);
const usuarioSeleccionado = ref([]);

const verDetalles = (data) => {
  // Mostrar el cuadro de diálogo con los detalles del alumno
  visible.value = true;
  // Establecer el alumno seleccionado para mostrar en la tabla de detalles
  usuarioSeleccionado.value = [data]; // Crear un arreglo con un solo alumno (el seleccionado)
};

getUsuarios();
</script>