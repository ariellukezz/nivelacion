<template>
    <Head title="Docentes" />
    <AuthenticatedLayout>
      <div class="bg-white shadow-xs p-4" style="height: calc(100vh - 110px); font-family: Arial, Helvetica, sans-serif;">

        <div class="flex mb-4" style="justify-content: space-between;">
          <Button label="Docente Nuevo" @click="visible = true" size="small" style="height: 40px;"/> 

          <span class="p-input-icon-left ">
              <i class="pi pi-search" />
              <InputText v-model="buscar" style="padding-left: 40px; height: 40px;" placeholder="Search" />
          </span>

        </div>      
    
        <!-- <pre>{{ docentes }}</pre> -->

        <DataTable :value="docentes" :class="'p-datatable-sm'" paginator :rows="10" tableStyle="min-width: 50rem" style="font-size: 0.8rem;">
          <Column field="nro_doc" header="nro_doc"></Column>
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
          <Column field="nombre_usuario" header="nombre_usuario"></Column>
          <Column field="nombre_escuela" header="nombre_escuela"></Column>
          
  
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
      <DataTable :value="docenteSeleccionado" :class="'p-datatable-sm'" style="font-size: 0.8rem;">
        <Column field="nro_doc" header="Dni"></Column>
        <Column field="estado" header="Estado pass"></Column>

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
import { ref, watch } from 'vue'

const docentes=ref([])
const buscar = ref("")

const totalpaginas = ref(0)
const pagina = ref(1)

const getDocentes =  async (event) => {
  let res = await axios.post(
  "getDocentes",{page: pagina.value, buscar:buscar.value}
  );
  docentes.value = res.data.datos;
  totalpaginas.value = res.data.datos.total;
}


watch(buscar, ( newValue, oldValue ) => {
   getDocentes()
})



// const getDocentes = async () => {
//     let res = await axios.get("getDocentes");
//     docentes.value = res.data.datos;
// };
const visible = ref(false);
const docenteSeleccionado = ref([]);
const verDetalles = (data) => {
    // Mostrar el cuadro de diálogo con los detalles del alumno
    visible.value = true;
    // Establecer el alumno seleccionado para mostrar en la tabla de detalles
    docenteSeleccionado.value = [data]; // Crear un arreglo con un solo alumno (el seleccionado)
  };


getDocentes()

</script>