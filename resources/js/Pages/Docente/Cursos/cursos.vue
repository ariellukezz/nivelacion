<template>
<Head title="Cursos"/>
<AuthenticatedLayout>
    <!-- HEADER BODY -->

    <div class="flex" style="background:white; border-bottom: 1px solid #CDCDCD;">
      <Button severity="secondary" style="font-size: 0.9rem"  text @click="Inicio"> Inicio </Button>
      <div v-if="cursoseleccionado !== null" class="flex justify-content-center" style="align-items:center;">
        <i class="pi pi-angle-right " />
        <Button severity="secondary" @click="resEsuela" style="font-size: 0.9rem" text> 
          <div style=" max-width: 180px; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
              <span> {{ cursoseleccionado.nombre }} </span>
          </div>
        </Button>
      </div>

    </div>
      
    <!-- END HEADER BODY -->

  <div class="bg-white shadow-xs p-4" style=" height: calc(100vh - 110px); font-family: Arial, Helvetica, sans-serif;">
    <Toast />
    <!-- CURSOS  1 -->  
    <div v-if="cursoseleccionado === null">
        <div>
          <div class="flex" style="justify-content: flex-end;">
            <span class="p-input-icon-left ">
                <i class="pi pi-search" />
                <InputText v-model="buscar" style="padding-left: 40px; height: 40px;" placeholder="Search" />
            </span>
          </div>
        </div>

        <div>
          <div class="card" >

            <DataTable 
              selectionMode="single"
              v-model:selection="cursoseleccionado"
              :value="cursos" 
              :class="'p-datatable-sm mt-4'"  
              tableStyle="min-width: 50rem" 
              style="font-size: .9rem;">
            
                <Column field="nombre" header="Curso"></Column>
                <Column field="grupo" header="Grupo"></Column>
                <Column field="escuela" header="Escuela"></Column>

                <Column field="estado" style=" justify-content: center; display: flex;" header="Estado" width="70px"> 
                    <template #body="{ data }">
                        <div class="flex" style="justify-content: center;">
                        <div v-if="data.estado === 1">
                            <Tag severity="info" value="Activo"></Tag>
                        </div>
                        <div v-if="data.estado === 0">
                            <Tag :style="{ background: '#CDCDCD' }" value="Inactivo"></Tag>
                        </div>
                        </div>
                    </template>
                </Column>
                    
                <Column field="id_programa" header="Acciones" width="90px"> 
                  <template #body="{ data }">
                    <div class="flex">
                      <Button class="" severity="help" icon="pi pi-eye" aria-label="Submit" @click="editar(data)" size="small" style="width: 25px; height: 25px;" />                    </div>
                  </template>
                </Column>
            </DataTable>
          </div>
        </div>
    </div>
    <!-- END PASO 1 -->

    <!--- PASO 2 -->
  
    <div v-if="cursoseleccionado != null">
        <div>
          <div class="flex mb-3" style="justify-content: flex-end;">
            
            <span class="p-input-icon-left ">
                <i class="pi pi-search" />
                <InputText v-model="buscarAlumno" style="padding-left: 40px; height: 40px;" placeholder="Search" />
            </span>

          </div>
        </div>


        <!-- <pre>{{ alumnosCurso }}</pre> -->
        <!-- {{ alumnoseleccionado }} -->
        <!-- {{ nota }} -->

        <div class="card p-fluid">

            <DataTable :value="alumnosCurso"               :class="'p-datatable-sm mt-4'"   selectionMode="single" v-model:selection="alumnoseleccionado" editMode="cell" @cell-edit-complete="onCellEditComplete"  tableStyle="min-width: 50rem">
                <Column v-for="col of columns" :key="col.field" :field="col.field" :header="col.header" style="width: 25%"></Column>


                <Column field="nota" header="Nota" width="120"> 
                  <template #editor="{ data, field }">
                    <InputNumber style="width:100px;" v-model="data[field]" inputId="minmaxfraction" :max="20" :min="0" :maxFractionDigits="2" autofocus />
                  </template>
                  <template #body="{ data, field }">
                    <div v-if="data[field] === null || data[field] === '' || data[field] === 0 " style="display:flex; justify-content:center;"> - </div>
                    <div v-else> {{ Number(data[field]).toFixed(2) }} </div>
                  </template>
                </Column>
                <Column field="id_programa" header="Condición" width="90px"> 
                    <template #body="{ data }">
                      <div class="flex" v-if="data.condicion === 1" >
                        <Tag severity="info" value="Aprobado"></Tag>
                      </div>
                      <div class="flex" v-if="data.condicion === 0" >
                        <Tag severity="danger" value="Desaprobado"></Tag>
                      </div>
                    </template>
                  </Column>
            </DataTable>
        </div>

    </div>

    <!-- END PASO 2-->



  </div>
</AuthenticatedLayout>

</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/LayoutDocente.vue';
import { Head } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { ref,watch } from 'vue'
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber'
import Toast from 'primevue/toast';
import { useToast } from "primevue/usetoast";
import Tag from 'primevue/tag';

const toast = useToast();

const nota = ref(null)


const pagina = ref(1);
const buscar = ref("")
const cursos = ref([])
const cursoseleccionado = ref(null)

const alumnoseleccionado = ref(null)

const alumnosCurso = ref([])
const buscarAlumno = ref("")

const getCursos =  async (event) => {
  let res = await axios.post(
  "/docente/get-cursos?page=" + pagina.value,
  { term: buscar.value }
  );
  cursos.value = res.data.datos.data;
}

const updateNota =  async (id, nota) => {
  let res = await axios.post(
  "/docente/update-nota",
  {  id_detallecurso: id, nota: nota, });
  getAlumnosXcurso()
  showToast(res.data.tipo, res.data.titulo, res.data.mensaje)
}

const getAlumnosXcurso =  async () => {
  let res = await axios.post("/docente/get-alumnos-curso?page=" + pagina.value, {   term: buscarAlumno.value, curso: cursoseleccionado.value.id } );
  alumnosCurso.value = res.data.datos.data;
}

const Inicio = () => {
  cursoseleccionado.value = null;
}

watch(buscar, ( newValue, oldValue ) => { getCursos() })
watch(cursoseleccionado, ( newValue, oldValue ) => { getAlumnosXcurso() })
watch(buscarAlumno, ( newValue, oldValue ) => { getAlumnosXcurso() }) 

getCursos()

const columns = ref([
    { field: 'dni', header: 'DNI' },
    { field: 'nombres', header: 'Nombres' },
    { field: 'paterno', header: 'Paterno' }
]);

const onCellEditComplete = (event) => {
    let { data, newValue, field } = event;
    // console.log("::: Data :_:", data.id);
    // console.log(":::DATA:::", newValue);
    updateNota(data.id, newValue);
    switch (field) {
        case 'nota':
        case 'nota':
            if (isPositiveInteger(newValue)) data[field] = newValue;
            else event.preventDefault();
            break;

        default:
            if (newValue.trim().length > 0) data[field] = newValue;
            else event.preventDefault();
            break;
    }
};
const isPositiveInteger = (val) => {
    let str = String(val);
    str = str.trim();
    var n = Math.floor(Number(str));
    return n >= 0;
};

const showToast = (tipo, titulo, detalle) => {
      toast.add({ severity: tipo, summary: titulo, detail: detalle, life: 3000 });
  };
  


</script>