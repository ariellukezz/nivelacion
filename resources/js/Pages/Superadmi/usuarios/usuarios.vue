<template>
  <Head title="Usuarios" />
  <AuthenticatedLayout>
    <div class="bg-white shadow-xs p-4" style="height: calc(100vh - 110px); font-family: Arial, Helvetica, sans-serif;">
    <div style="display:flex; justify-content:space-between;">
      <div class="mb-3" style="width: 240px;">
        <Dropdown v-model="rol" :options="tipouser" filter optionLabel="label" optionValue="value"
          placeholder="Tipo Usuario" style="width:100%;" class="w-full md:w-11rem">
          <template #option="slotProps">
            <div class="flex align-items-center"
              style="width: 400px; font-size:0.9rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
              <div>{{ slotProps.option.label }}</div>
            </div>
          </template>
        </Dropdown>
      </div>

      <div class="flex" style="justify-content: space-between;">
        <span class="p-input-icon-left">
          <i class="pi pi-search" />
          <InputText v-model="buscar" placeholder="Search" />
        </span>
      </div>
    </div>

      <DataTable :value="usuarios" :class="'p-datatable-sm'" paginator :rows="10" tableStyle="min-width: 50rem"
        style="font-size: 0.8rem;">
        <Column field="nombres" header="nombres"></Column>
        <Column field="apellidos" header="apellidos"></Column>
        <Column field="email" header="email"></Column>
        <Column field="estado_contraseña" header="estado_contraseña"></Column>
        <Column field="programa" header="Opciones" width="80px">
            <template #body="{ data }">
              <div class="flex">
                <Button class="text-6xl text-primary-500" severity="help" aria-label="Submit" @click="restablecer(data)" size="small">restablecer</Button>
              </div>
            </template>
          </Column>

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
import 'primeicons/primeicons.css';
import Dropdown from 'primevue/dropdown';


const usuario = ref([]);
const usuarios = ref([]);
const alumnos = ref([]);
const buscar = ref("")

const totalpaginas = ref(0)
const pagina = ref(1)
const rol = ref(1)


// const getAlumnos =  async (event) => {
//   let res = await axios.post(
//   "getAlumnosc",{competencia:1,buscar:buscar.value}
//   );
//   alumnos.value = res.data.datos;
// }


const getUsuarios = async (event) => {
  let res = await axios.post(
    "getUsuarios", { page: pagina.value, buscar: buscar.value, rol:  rol.value}
  );
  usuarios.value = res.data.datos;
  totalpaginas.value = res.data.datos;
}

const restablecer = async (data) => {
  let res = await axios.post(
    "restablecerContraseña", { id: data.id}
  );
}


watch(buscar, (newValue, oldValue) => {
  getUsuarios()
})

watch(rol, (newValue, oldValue) => {
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

const tipouser = ref([
        {value:0, label:"SUPERADMI"},
        {value:1, label:"DIRECTOR"},
        {value:2, label:"COORDINADOR"},
        {value:3, label:"ADMISION"},
        {value:4, label:"DOCENTE"},
        {value:5, label:"ALUMNO"},
        {value:6, label:"SUPERVISOR"},
  ])

getUsuarios();
</script>