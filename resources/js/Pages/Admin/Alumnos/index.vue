<template>
    <Head title="Documentos"/>
  
    <AuthenticatedLayout>
      <Toast />
      <div class="p-4 bg-white rounded-lg shadow-xs">
        
        <div class="card">
          <TabView>
            <TabPanel header="Estudiantes">
                  <div class="mb-3">
                    <form @submit.prevent="submit">
                      <div class="flex justify-between">
                        <!-- <input type="file" @change="onChange"/> -->
                        <input type="file" @change="handleFileUpload" />
                        <Button label="Subir" style="height:38px;" @click="subirDataEstudiante"/>              
                      </div>
                    </form>
                  </div>
                  <div v-if="excelData">
                    <!-- {{ excelData }}   -->
                    <DataTable :value="excelData" :class="'p-datatable-sm'" tableStyle="min-width: 50rem">
                      <!-- <Column field="id" header="ID"/> -->
                      <Column field="codigo" header="CODIGO"/>
                      <Column field="dni" header="DNI"/>
                      <Column field="paterno" header="PATERNO"/>
                      <Column field="materno" header="MATERNO"/>
                      <Column field="nombres" header="NOMBRES"/>
                      <Column field="sexo" header="SEXO"/>
                      <Column field="email" header="EMAIL"/>
                      <Column field="f_nacimiento" header="F NAC"/>
                      <Column field="ubigeo_nacimiento" header="UBIGEO"/>
                      <Column field="estado_civil" header="E_CIVIL"/>
                      <Column field="anio_egreso" header="EGRESO"/>
                      <Column field="tipo_colegio" header="T. COLEGIO"/>
                      <Column field="nombre_colegio" header="COLEGIO"/>
                      <Column field="ubigeo_colegio" header="UB. COLEGIO"/>
                      <Column field="apto" header="INGRESO"/>
                      <Column field="direccion" header="DIRECCION"/>
                      <Column field="telefono" header="TELEFONO"/>
                    </DataTable>
                  </div>
            </TabPanel>
  
            <TabPanel header="Datos ingreso">
                  <div class="mb-3">
                    <form @submit.prevent="submit">
                      <div class="flex justify-between">
                        <input type="file" @change="onChange2"/>
                        <Button label="Subir" style="height:38px;" @click="submit2"/>              
                      </div>
                    </form>
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
            <TabPanel header="Matriz">
                  <div class="mb-3">
                    <form @submit.prevent="submit">
                      <div class="flex justify-between">
                        <input type="file" @change="onChange3"/>
                        <Button label="Subir" style="height:38px;" @click="submit3"/>              
                      </div>
                    </form>
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
  import XLSX from 'xlsx';
  
  const pagina = ref(1)
  const resoluciones = ref([])
  const planes = ref([])
  const informes = ref([])
  const excelData = ref([1])

  
  
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
  
  const subirDataEstudiante = async () => {
    await axios.post("/importar-excel-estudiante", { datos: excelData.value })
    .then(res=>{
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
    //console.log(data.url)
    window.open(data.url, '_blank', 'fullscreen=yes'); return false;
  }


  const handleFileUpload = (event) => {
    const file = event.target.files[0];
    const reader = new FileReader();

    reader.onload = (e) => {
      const data = new Uint8Array(e.target.result);
      const workbook = XLSX.read(data, { type: 'array' });
      const firstSheetName = workbook.SheetNames[0];
      const worksheet = workbook.Sheets[firstSheetName];
      const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

      const headers = jsonData[0]; // Assuming the first row contains headers

      const arrayOfObjects = jsonData.slice(1).map((row) => {
        const obj = {};
        headers.forEach((header, index) => {
          obj[header] = row[index];
        });
        return obj;
      });

      excelData.value = arrayOfObjects;
    };

    reader.readAsArrayBuffer(file);
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
<!-- <template>
    <div>
      <input type="file" @change="handleFileUpload" />
      <table>
        <tr v-for="(row, index) in excelData" :key="index">
          <td v-for="(cell, cellIndex) in row" :key="cellIndex">{{ cell }}</td>
        </tr>
      </table>
    </div>
  </template>
  
  <script>
  import XLSX from 'xlsx';
  
  export default {
    data() {
      return {
        excelData: []
      };
    },
    methods: {
      handleFileUpload(event) {
        const file = event.target.files[0];
        const reader = new FileReader();
  
        reader.onload = (e) => {
          const data = new Uint8Array(e.target.result);
          const workbook = XLSX.read(data, { type: 'array' });
          const firstSheetName = workbook.SheetNames[0];
          const worksheet = workbook.Sheets[firstSheetName];
          const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
  
          this.excelData = jsonData;
        };
  
        reader.readAsArrayBuffer(file);
      }
    }
  };
  </script> -->