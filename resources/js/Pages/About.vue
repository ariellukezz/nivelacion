<template>
  <Head title="Documentos"/>

  <AuthenticatedLayout>
    <Toast />
    <div class="p-4 bg-white rounded-lg shadow-xs">
      

      <div class="card">
        <TabView>
          <TabPanel header="Resolución decanal">
                <div class="mb-3">
                  <form @submit.prevent="submit">
                    <div class="flex justify-between">
                      <input type="file" @change="onChange"/>
                      <Button label="Subir" style="height:38px;" @click="submit"/>              
                    </div>
                  </form>
                </div>
                <div class="card" >
        <Message :closable="false" style=" color: rgb(139, 62, 62)">Subir en formato PDF (un solo Archivo).</Message>
    </div>
                <div>
                  <DataTable :value="resoluciones" :class="'p-datatable-sm'" tableStyle="min-width: 50rem">
                    <Column field="nombre" header="Nombre"></Column>
                    <Column field="tipo" header="Tipo"></Column>
                    <Column field="fecha" header="Fecha"></Column>
                    <Column field="id_programa" header="Acciones" width="50" style> 
                      <template #body="{ data }">
                        <div class="flex justify-center">
                          <!-- <div class="mr-2">
                            <Button class="secondary" icon="pi pi-pencil" aria-label="Submit" @click="editar(data)" size="small" style="width: 25px; height: 25px;"/>
                          </div> -->
                          <Button icon="pi pi-eye" severity="info" aria-label="Submit" @click="verdocumento(data)"  size="small"  style="width: 25px; height: 25px;"/>
                        </div>
                      </template>
                    </Column>
                  </DataTable>
                    <!-- {{ resoluciones }} -->
                </div>
          </TabPanel>

          <TabPanel header="Plan de trabajo">
                <div class="mb-3">
                  <form @submit.prevent="submit">
                    <div class="flex justify-between">
                      <input type="file" @change="onChange2"/>
                      <div class="flex">
                        <div class="mr-2">
                          <Button label="Descargar esquema" @click="descargar('esquema.pdf')" outlined style="height:38px;" />              
                        </div>
                        <div>
                          <Button label="Subir" style="height:38px;" @click="submit2"/>                            
                        </div> 
                      </div>
                    </div>
                  </form>
                </div>
                <div class="card" >
        <Message :closable="false" style=" color: rgb(139, 62, 62)">Subir en formato PDF (un solo Archivo).</Message>
    </div>
                <div>
                  <DataTable :value="planes" :class="'p-datatable-sm'" tableStyle="min-width: 50rem">
                    <Column field="nombre" header="Nombre"></Column>
                    <Column field="tipo" header="Tipo"></Column>
                    <Column field="fecha" header="Fecha"></Column>
                    <Column field="id_programa" header="Acciones" width="50" style> 
                      <template #body="{ data }">
                        <div class="flex justify-center">
                          <!-- <div class="mr-2">
                            <Button class="secondary" icon="pi pi-pencil" aria-label="Submit" @click="editar(data)" size="small" style="width: 25px; height: 25px;"/>
                          </div> -->
                          <Button icon="pi pi-eye" severity="info" aria-label="Submit" @click="verdocumento(data)"  size="small"  style="width: 25px; height: 25px;"/>
                        </div>
                      </template>
                    </Column>
                  </DataTable>
                    <!-- {{ resoluciones }} -->
                </div>
          </TabPanel>
          <TabPanel header="Informe">
                <div class="mb-3">
                  <form @submit.prevent="submit">
                    <div class="flex justify-between">
                      <input type="file" @change="onChange3"/>
                      <div class="flex justify-end">
                        <div class="mr-2">
                          <Button label="Descargar ejemplo" @click="descargar('ejemplo.docx')" outlined style="height:38px;" />              
                        </div>
                        <div>
                          <Button label="Subir" style="height:38px;" @click="submit3"/>              
                        </div>
                      </div>

                    </div>
                  </form>
                </div>
                <div class="card" >
        <Message :closable="false" style=" color: rgb(139, 62, 62)">Subir en formato .RAR ó .ZIP (un solo Archivo Comprimido).</Message>
    </div>

                <div>
                  <DataTable :value="informes" :class="'p-datatable-sm'" tableStyle="min-width: 50rem">
                    <Column field="nombre" header="Nombre"></Column>
                    <Column field="tipo" header="Tipo"></Column>
                    <Column field="fecha" header="Fecha"></Column>
                    <Column field="id_programa" header="Acciones" width="50" style> 
                      <template #body="{ data }">
                        <div class="flex justify-center">
                          <!-- <div class="mr-2">
                            <Button class="secondary" icon="pi pi-pencil" aria-label="Submit" @click="editar(data)" size="small" style="width: 25px; height: 25px;"/>
                          </div> -->
                          <Button icon="pi pi-eye" severity="info" @click="verdocumento(data)"  size="small"  style="width: 25px; height: 25px;"/>
                        </div>
                      </template>
                    </Column>
                  </DataTable>
                    <!-- {{ resoluciones }} -->
                </div>
            </TabPanel>
          </TabView>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import Button from 'primevue/button';
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import Toast from 'primevue/toast';
import { useToast } from "primevue/usetoast";
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Message from 'primevue/message';
const baseUrl = window.location.origin;


const pagina = ref(1)
const resoluciones = ref([])
const planes = ref([])
const informes = ref([])


const toast = useToast();

const showToast = (tipo, titulo, detalle) => {
    toast.add({ severity: tipo, summary: titulo, detail: detalle, life: 3000 });
};

const imagen = ref(null);
const imagen2 = ref(null);
const imagen3 = ref(null);

const onChange = (e) => {
  console.log("Selected file", e.target.files[0])
  imagen.value = e.target.files[0];
}

const onChange2 = (e) => {
  console.log("Selected file", e.target.files[0])
  imagen2.value = e.target.files[0];
}
const onChange3 = (e) => {
  console.log("Selected file", e.target.files[0])
  imagen3.value = e.target.files[0];
}

const submit = async () => {
  let fd = new FormData();
  fd.append('img', imagen.value)
  await axios.post("/documento/resolucion", fd).then(res=>{
    showToast("success","2",res.data.menssje);
    getResoluciones()
  }).catch(err=>console.log(err))
}

const submit2 = async () => {
  let fd = new FormData();
  fd.append('img', imagen2.value)
  await axios.post("/documento/plan", fd).then(res=>{
    showToast("success","2",res.data.menssje);
    getPlanes()
  }).catch(err=>console.log(err))
}
//RUTA http://nivelacion.test/documentos/planes/29718938.pdf
const submit3 = async () => {
  let fd = new FormData();
  fd.append('img', imagen3.value)
  await axios.post("/documento/informe", fd).then(res=>{
    showToast("success","2",res.data.menssje);
    getInformes()
  }).catch(err=>console.log(err))
}

  
const getResoluciones =  async () => {
    let res = await axios.post(
    "get-resoluciones?page=" + pagina.value,
    { term: "" }
    );
    resoluciones.value = res.data.datos.data;
}
const getPlanes =  async () => {
    let res = await axios.post(
    "get-planes?page=" + pagina.value,
    { term: "" }
    );
    planes.value = res.data.datos.data;
}
const getInformes =  async () => {
    let res = await axios.post(
    "get-informes?page=" + pagina.value,
    { term: "" }
    );
    informes.value = res.data.datos.data;
}

const verdocumento = (data) => {
  window.open(data.url, '_blank', 'fullscreen=yes'); return false;
}

const descargar = (nombre) => {
  // window.open(baseUrl+'/documentos/'+nombre, '_blank');
  window.open(baseUrl+'/documentos/'+nombre);
}




getResoluciones()
getPlanes()
getInformes()

</script>

<style scoped>
input[type=file]::file-selector-button {
  margin-right: 10px;
  border: none;
  background: var(--primary-color);
  padding: 7px 20px;
  border-radius: 5px;
  color: #fff;
  cursor: pointer;
  transition: background .2s ease-in-out;
}

input[type=file]::file-selector-button:hover {
  background: var(--primary-600);
}
</style>