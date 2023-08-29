<template>
  <Head title="Tutores"/>
  <AuthenticatedLayout>
  <div class="bg-white shadow-xs p-4" style=" height: calc(100vh - 110px); font-family: Arial, Helvetica, sans-serif;">

      <div>
        <div class="flex" style="justify-content: space-between;">
          <Button label="Nuevo" @click="visible = true" size="small" style="height: 40px;"/>
        
          <span class="p-input-icon-left ">
              <i class="pi pi-search" />
              <InputText v-model="buscar" style="padding-left: 40px; height: 40px;" placeholder="Search" />
          </span>

        </div>
      </div>
      <Toast />
      <ConfirmPopup></ConfirmPopup>
  
      <div>
        <div class="card">
          <div class="flex justify-content-center mb-4">
              <!-- <SelectButton v-model="size" :options="sizeOptions" optionLabel="label" dataKey="label" /> -->
          </div>
          <DataTable :value="docentes" :class="'p-datatable-sm'"  tableStyle="min-width: 50rem" style="font-size: .9rem;">
              <Column field="nro_doc" header="N° Doc"></Column>
              <Column v-if="conf_codigo === true" field="Codigo" header="Código"></Column>
              <Column field="nombres" header="Nombres">
                  <template #body="{ data }">
                      <div class="flex" style="justify-content: flex-start;">
                          <div>
                              {{ data.nombres }} {{ data.paterno }} {{ data.materno }}
                          </div>
                      </div>
                  </template>
              </Column> 
              <Column field="sexo" header="Sexo"></Column>
              <Column field="telefono" header="Celular"></Column>
              <Column field="email" header="Correo"></Column>
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
                    <div class="mr-2">
                      <Button class="secondary" icon="pi pi-pencil" aria-label="Submit" @click="editar(data)" size="small" style="width: 25px; height: 25px;"/>
                    </div>
                    <Button icon="pi pi-trash" severity="danger" aria-label="Submit" @click="confirm2($event, data)"  size="small"  style="width: 25px; height: 25px;"/>
                  </div>
                </template>
              </Column>
          </DataTable>
        </div>
      </div>
  
      <Dialog v-model:visible="visible" header="Docente Nuevo" :style="{ width: '60vw', height:'600px' }">

        <div style="">
        <TabView>
            <TabPanel header="Datos Docente" >

        <!-- <pre>{{ docente }}</pre> -->
        <div class="flex mt-0 mb-3 align-items-center" style="justify-content: flex-end;" >
            <div class="flex flex-wrap mr-0">
                <div>
                  <div class="mr-2"> <label>Tipo doc </label> </div>  
                  <div class="flex">
                    <div class="flex align-items-center mr-2">
                        <RadioButton v-model="docente.tipo_doc" name="pizza" :value="1" />
                        <label for="ingredient1" class="ml-2">DNI</label>
                    </div>
                    <div class="flex align-items-center">
                        <RadioButton v-model="docente.tipo_doc" name="pizza" :value="2" />
                        <label for="ingredient2" class="ml-2">C. Ext</label>
                    </div>

                  </div>
                </div>
            </div>
            <Divider layout="vertical"/>  

            <div class="flex flex-wrap mr-0">
                <div>
                  <div class="mr-2"> <label>Sexo </label> </div>  
                  <div class="flex">
                    <div class="flex align-items-center mr-2">
                        <RadioButton v-model="docente.sexo" name="pizza" value="M" />
                        <label for="ingredient1" class="ml-2">M</label>
                    </div>
                    <div class="flex align-items-center">
                        <RadioButton v-model="docente.sexo" name="pizza" value="F" />
                        <label for="ingredient2" class="ml-2">F</label>
                    </div>

                  </div>
                </div>
            </div>
            <Divider layout="vertical" />  
          <div class="mr-3"> 
            <label>Estado</label> 
            <div> <InputSwitch v-model="docente.estado" /></div>
          </div>  

        </div>
 
          <div class="flex" style="width: 100%; justify-content: space-between;">
            <div class="mb-2" style="width: 48%;">
              <div><label>Nro documento</label></div>  
              <InputText style="width: 100%; height: 40px;"  type="text" v-model="docente.nro_doc" />
            </div>
    
            <div class="mb-2" style="width: 48%;">
              <div><label>Nombres</label></div>  
              <InputText style="width: 100%; height: 40px;"  type="text" v-model="docente.nombres" />
            </div>
          </div>

          <div class="flex" style="width: 100%; justify-content: space-between;">
            <div class="mb-2" style="width: 48%;">
              <div><label>Primer apellido</label></div>  
              <InputText style="width: 100%; height: 40px;"  type="text" v-model="docente.primer_apellido" />
            </div>
    
            <div class="mb-2" style="width: 48%;">
              <div><label>Segundo apellido</label></div>  
              <InputText style="width: 100%; height: 40px;"  type="text" v-model="docente.segundo_apellido" />
            </div>
          </div>

          <div class="flex" style="width: 100%; justify-content: space-between;">
            <div class="mb-2" style="width: 23%;">
              <div><label>Celular</label></div>  
              <InputText style="width: 100%; height: 40px;"  type="text" v-model="docente.celular" />
            </div>
    
            <div class="mb-2" style="width: 73%;">
              <div><label>Correo</label></div>  
              <InputText style="width: 100%; height: 40px;"  type="text" v-model="docente.correo" />
            </div>
          </div>


          <div class="flex" style="width: 100%; justify-content: space-between; margin-bottom:-20px;" >
            <div class="" style="width: 100%;">
              <div><label>Dirección</label></div>  
              <InputText style="width: 100%; height: 40px;"  type="text" v-model="docente.direccion" />
            </div>
    
            <!-- <div class="" style="width: 23%;">
              <div><label>Fecha nacimiento</label></div>  
              <Calendar style="width: 100%; height: 40px;" dateFormat="dd/mm/yy" v-model="docente.fecha" />
            </div> -->
          </div>
 


        </TabPanel>
        <TabPanel header="Cursos que puede dictar">
          <div style="height:340px;">
            

            <div class="flex mt-5" style="width: 100%; justify-content: space-between; margin-bottom:-20px;" >
              <div class="" style="width: 100%;">
                <div class="mb-2"><h2>Habilidades</h2></div>  
                  <div style="column-count: 2;">
                  <div v-for="category of categories" :key="category.key" class="mb-2" style="font-size: .9rem" >
                        <div v-if="category.value % 2 === 0" class="flex">
                          <Checkbox v-model="selectedCategories" :inputId="category.key" class="mr-2" name="category" :value="category.value" />
                          <div style=" max-width: 230px; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                            <label :for="category.key">{{ category.name }}</label>
                          </div>
                        </div>

                        <div v-else class="flex">
                          <Checkbox v-model="selectedCategories" :inputId="category.key" class="mr-2" name="category" :value="category.value" />
                          <div style=" max-width: 230px; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                            <label :for="category.key">{{ category.name }}</label>
                          </div>
                        </div>
                  </div>
                </div>
              </div>
            </div>



          </div>
        </TabPanel>
      
      </TabView> 
      </div>

        

        <template #footer style="margin-top: -10px">
            <div class="flex" style="justify-content: flex-end;">
              <div>
                <Button label="Cancelar" outlined="" @click="visible = false" size="small" />
              </div>
              <Button label="Guardar" @click="guardar" size="small"/>
            </div>
          </template>

      </Dialog>
  
  </div>
  </AuthenticatedLayout>
  </template>
  
  <script setup>
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
  import { Head } from '@inertiajs/vue3';
  import { ref, watch } from 'vue';
  import Button from 'primevue/button';
  import InputText from 'primevue/inputtext';
  import DataTable from 'primevue/datatable';
  import Column from 'primevue/column';
  import Dialog from 'primevue/dialog';
  import Dropdown from 'primevue/dropdown'
  import InputSwitch from 'primevue/inputswitch';
  import Toast from 'primevue/toast';
  import { useToast } from "primevue/usetoast";
  import { useConfirm } from "primevue/useconfirm";
  import ConfirmPopup from 'primevue/confirmpopup';
  import RadioButton from 'primevue/radiobutton';
  import Divider from 'primevue/divider';
  import Calendar from 'primevue/calendar';
  import Tag from 'primevue/tag';
  import TabView from 'primevue/tabview';
  import TabPanel from 'primevue/tabpanel';
  import Checkbox from 'primevue/checkbox';

  
  const toast = useToast();
  const confirm = useConfirm();
  
  const programas = ref([])
  const totalpaginas = ref(0)
  const pagina = ref(1)
  const buscar = ref("")
  
  const docentes = ref([]) 
  const visible = ref(false);
  const temp = ref("");
  

  const op = ref();
  const toggle = (event) => {
      op.value.toggle(event);
  }

  const docente = ref({
    id: null,
    tipo_doc:1,
    nro_doc:"",
    nombres:"",
    primer_apellido:"",
    segundo_apellido:"",
    celular:"",
    correo:"",
    direccion:"",
    sexo:"M",
    estado:true
  })
  
  const getDocentes =  async (event) => {
    let res = await axios.post(
    "/coordinador/get-docentes?page=" + pagina.value,
    { 
      term: buscar.value,
   }
    );
    docentes.value = res.data.datos.data;
    totalpaginas.value = res.data.datos.total;
  }

  const getCompetenciaDocente =  async (idd) => {
    let res = await axios.post( "/coordinador/get-competencia-x-docente?page=" + pagina.value, { id_docente: idd});
    selectedCategories.value = res.data.datos;
  }
  

  const guardar =  async () => {
    // let fec = null;
    // if(docente.value.fecha != null && docente.value.fecha !== temp.value ){
    //   fec = docente.value.fecha.toISOString().substring(0, 10);
    // }
    // else {
    //   fec = docente.value.fecha
    // }

    let res = await axios.post(
      "/coordinador/save-docente",
      { 
        id: docente.value.id,
        tipo_doc: docente.value.tipo_doc,
        nro_doc: docente.value.nro_doc,
        nombres: docente.value.nombres,
        primer_apellido: docente.value.primer_apellido,
        segundo_apellido: docente.value.segundo_apellido,
        celular: docente.value.celular,
        correo: docente.value.correo,
        direccion: docente.value.direccion,
        //fecha: fec,
        sexo : docente.value.sexo,
        estado : docente.value.estado,
        competencias : selectedCategories.value
      }
    );
  
    showToast(res.data.tipo, res.data.titulo, res.data.mensaje)
    getDocentes()  
    visible.value = false
    limpiar()
    // roles.value = res.data.datos.data;
  }
  
  const eliminar =  async (id) => {
    let res = await axios.get(
    "delete-docente/"+id);
    showToast(res.data.tipo, res.data.titulo, res.data.mensaje)
    getDocentes() 
  }  

  const getdataprisma =  async ( ) => {
    let res = await axios.get("get-data-docente/"+docente.value.nro_doc);
    docente.value.nombres = res.data.nombre;
    docente.value.primer_apellido = res.data.paterno;
    docente.value.segundo_apellido = res.data.materno;
  }  
  
  const emod = ref(false);

  const editar =  async (item) => {
    getCompetenciaDocente(item.id)
    visible.value = true;
    emod.value = true;
    docente.value.id = item.id
    docente.value.tipo_doc = item.tipo_doc
    docente.value.nro_doc = item.nro_doc
    docente.value.nombres = item.nombres
    docente.value.primer_apellido = item.paterno
    docente.value.segundo_apellido = item.materno
    docente.value.celular = item.telefono
    docente.value.correo = item.email
    docente.value.direccion = item.direccion
//    docente.value.fecha = item.f_nac
    temp.value = item.f_nac
    docente.value.sexo = item.sexo
    if(item.estado === 1) { docente.value.estado = true } else { docente.value.estado = false }

  }

  watch(visible, ( newValue, oldValue ) => {
      if(emod.value == true  && visible.value == false ){
        visible.value = false
        docente.value.id = null
        docente.value.tipo_doc = 1
        docente.value.nro_doc = null
        docente.value.nombres = null
        docente.value.primer_apellido = null
        docente.value.segundo_apellido = null
        docente.value.celular = null
        docente.value.correo = null
        docente.value.direccion = null
    //  docente.value.fecha = null
        temp.value = null
        docente.value.sexo = 'M'
        docente.value.estado = true
      }
  })

  watch(buscar, ( newValue, oldValue ) => {
      getDocentes()
  })

  watch(() => docente.value.nro_doc, (newValue, oldValue) => {
    if( newValue.length == 8){
      getdataprisma();
    }
  });
  
  const showToast = (tipo, titulo, detalle) => {
      toast.add({ severity: tipo, summary: titulo, detail: detalle, life: 3000 });
  };
  
  const confirm2 = (event,doc) => {
      confirm.require({
          target: event.currentTarget,
          message: '¿Estas seguro de eliminar al docente '+ doc.nombres+'?',
          icon: 'pi pi-info-circle',
          acceptClass: 'p-button-danger',
          accept: () => {
            eliminar(doc.id)
          },
          reject: () => {
              toast.add({ severity: 'error', summary: 'Elimación cancelada', detail: 'Se ha cancelado la eliminación del docente '+doc.nombres, life: 3000 });
          }
      });
  };

  getDocentes()
  // getProgramas()


  const categories = ref([
    {value:1, name: "(C1) EDUCACION CIVICA"},
    {value:2, name: "(C2) HISTORIA Y GEOGRAFÍA"},
    {value:3, name: "(C3) EDUCACIÓN FISICA"},
    {value:4, name: "(C4) ARTE Y CULTURA"},
    {value:5, name: "(C5) LENGUAJE: LITERATURA, RAZONAMIENTO VERBAL, INGLES, QUECHUA Y AIMARA"},
    {value:6, name: "(C6) FISICA, QUIMICA Y BIOLOGÍA"},
    {value:7, name: "(C7) MATEMÁTICA"},
    {value:8, name: "(C8) EDUCACIÓN PARA EL TRABAJO, ECONOMÍA"},
    {value:9, name: "(C9) TIC"},
    {value:10, name: "(C10) ESTRATEGIA DE APRENDIZAJE"},
    {value:11, name: "(C11) RELIGIÓN"}
  ]);
  const selectedCategories = ref([]);

  const limpiar = () => {
    docente.value.id = null
    docente.value.tipo_doc = 1
    docente.value.nro_doc = null
    docente.value.nombres = null
    docente.value.primer_apellido = null
    docente.value.segundo_apellido = null
    docente.value.celular = null
    docente.value.correo = null
    docente.value.direccion = null
  //  docente.value.fecha = null
    temp.value = null
    docente.value.sexo = 'M'
    docente.value.estado = true
    selectedCategories.value = []
  }

  </script>
  