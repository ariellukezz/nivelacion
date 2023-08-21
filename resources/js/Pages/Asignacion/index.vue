<template>
  <Head title="Nivelación"/>
  <AuthenticatedLayout>

  <!-- <pre>{{ alumnosregistro }}</pre> -->

  <div class="flex mb-0" style="justify-content: space-between; align-items:center; margin-top:0px; border-bottom:solid 1px #cdcdcd9D; height:50px; background:white; ">

      <div class="flex">
        <Button severity="secondary" style="font-size: 0.9rem"  text @click="Inicio"> Inicio </Button>
        <div v-if="escuela !== null" class="flex justify-content-center" style="align-items:center;">
          <i class="pi pi-angle-right " />
          <Button  severity="secondary" @click="resEsuela" style="font-size: 0.9rem" text> 
            <div style=" max-width: 180px; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                <span> {{ escuela.escuela }} </span>
            </div>
          </Button>
        </div>

        <div v-if="cursoseleccionado !== null" class="flex justify-content-center" style="align-items:center;">
          <i class="pi pi-angle-right " />
          <Button  severity="secondary" style="font-size: 0.9rem" text> 
            <div style=" max-width: 180px; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                <span> {{ cursoseleccionado.nombre }} </span>
            </div>
          </Button>
        </div>
      </div>

      <div v-if="escuela === null">
        <div class="flex mr-4" style="justify-content: flex-end;">
          <span class="p-input-icon-left">
              <i class="pi pi-search" /> 
              <InputText v-model="buscar" style="padding-left: 40px; height: 40px;" placeholder="Buscar" />
          </span>
        </div>
      </div>


      <div v-if="escuela !== null && cursoseleccionado === null">
        <Dropdown 
          v-model="competencia" 
          :options="competencias"
          severity="primary" 
          optionLabel="label" 
          optionValue="value"  
          placeholder="Selecciona una competencia" 
          style="width:325px; height:38px" 
          class="w-full md:w-11rem mr-4">            
          <template #option="slotProps">
              <div class="flex align-items-center" style="width: 280px; font-size:0.9rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                  <div>{{ slotProps.option.label }}</div>
              </div>
          </template>
        </Dropdown>
      </div>

  </div>
  

  <div class="bg-white shadow-xs p-4" style=" height: calc(100vh - 140px); font-family: Arial, Helvetica, sans-serif;">

      <!--- PASO 1-->
      <div>
        <div v-if="escuela === null" class="card">
          <DataTable 
            v-model:selection="escuela"
            selectionMode="single"
            :value="escuelas" 
            :class="'p-datatable-sm'"  
            tableStyle="min-width: 50rem" 
            style="font-size: .9rem;"
            :paginator="true" :rows="10" :filters="filters"
            >
              <Column field="escuela" header="Escuela"></Column>
              <Column field="facultad" header="Facultad"></Column>
              <Column field="area" header="Area"></Column>              
          </DataTable>
        </div>
      </div>
      <!--- END PASO 1-->



      <!--- PASO 2 -->
      <div v-if="escuela !== null && cursoseleccionado === null"> 

        <div class="flex" style="justify-content: space-between;">
          <Button severity="primary" @click="visible = true" style="height:40px"> Nuevo </Button>
          <div>
            <div class="flex mb-3" style="justify-content: flex-end;">
              <span class="p-input-icon-left">
                  <i class="pi pi-search" />
                  <InputText v-model="buscar" style="padding-left: 40px; height: 40px;" placeholder="Buscar" />
              </span>
            </div>
          </div>
    
        </div>

        <!-- {{ cursoseleccionado }} -->
        <div class="mt-3" >
        <DataTable 
          v-model:selection="cursoseleccionado"
          selectionMode="single" 
          :value="cursos" 
          :class="'p-datatable-sm'"  
          tableStyle="min-width: 50rem" 
          style="font-size: .9rem;"
          :paginator="true" :rows="9"
          >
              <Column field="nombre" header="Nombre"></Column>
              <Column field="competencia" header="Competencia"></Column>
              <Column field="docente" header="Docente">
                  <template #body="{ data }">
                      <div class="flex" style="justify-content: flex-start;">
                          <div>
                              {{ data.docente }}
                          </div>
                      </div>
                  </template>
              </Column> 
              <Column field="grupo" header="Grupo"></Column>
              <Column field="escuela" header="Escuela Prof."></Column>
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

      <!-- END PASO 2 -->



      <!--- PASO 3 -->

      <div v-if="escuela !== null && cursoseleccionado !== null"> 

        <div class="flex" style="justify-content: space-between;">
          <Button severity="primary" @click="modal_registro = true" style="height:40px"> Nuevo </Button>
          <div>
            <div class="flex mb-3" style="justify-content: flex-end;">
              <span class="p-input-icon-left">
                  <i class="pi pi-search" />
                  <InputText v-model="buscar" style="padding-left: 40px; height: 40px;" placeholder="Buscar" />
              </span>
            </div>
          </div>

        </div>

        <!-- {{ cursoseleccionado }} -->
        <div class="mt-3" >

        <DataTable 
          selectionMode="single" 
          :value="detalle_curso" 
          :class="'p-datatable-sm'"  
          tableStyle="min-width: 50rem" 
          style="font-size: .9rem;"
          :paginator="true" :rows="9"
          >
        
              <Column field="dni" header="DNI"></Column>
              <Column field="nombres" header="Nombres"></Column>
              <Column field="paterno" header="Paterno"></Column>
              <Column field="materno" header="Materno"></Column>
              <Column field="curso" header="Curso"></Column>
              <Column field="nota" header="Nota"></Column>
              <Column field="estado" style=" justify-content: center; display: flex;" header="Condición" width="70px"> 
              <template #body="{ data }">
                <div class="flex" style="justify-content: center;">
                  <div v-if="data.nota >= 10.50">
                      <Tag severity="info" value="Aprobado"></Tag>
                  </div>
                  <div v-if="data.nota <= 10.49">
                      <Tag severity="danger" value="Desprobado"></Tag>
                  </div>
                </div>
              </template>
              </Column>
          </DataTable> 
        </div>

        </div>

        <!-- END PASO 3 -->

      <Toast />
      <ConfirmPopup></ConfirmPopup>

      <!--- MODAL -->
      <Dialog v-model:visible="visible" modal header="Registrar Docente" :style="{ width: '500px' }">
        <!-- <pre>{{ docente }}</pre> -->
        <div class="flex mt-0 mb-3 align-items-center" style="justify-content: flex-end;" >
            <label>Estado</label> 
            <div class="ml-3"> <InputSwitch v-model="curso.estado" /></div>  
        </div>
 
        <div class="flex" style="width: 100%; justify-content: space-between;">
            <div class="mb-3" style="width: 68%;">
              <div><label>Nombre</label></div>  
              <InputText style="width: 100%; height: 40px;"  type="text" v-model="curso.nombre" />
            </div>
    
            <div class="mb-3" style="width: 28%;">
              <div><label>Grupo</label></div>  
              <Dropdown v-model="curso.grupo" :options="grupos" optionLabel="label" optionValue="value"  placeholder="Selecciona una competencia" style="width:100%;" class="w-full md:w-11rem">            
                <template #option="slotProps">
                    <div class="flex align-items-center" style=" font-size:0.9rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                        <div>{{ slotProps.option.label }}</div>
                    </div>
                </template>
              </Dropdown>
            </div>
        </div>

        <div class="flex" style="width: 100%; justify-content: space-between;">
            <div class="mb-3" style="width: 100%;">
              <div><label>Competencia</label></div>  
              <Dropdown v-model="cursocompetencia" :options="competencias" optionLabel="label" optionValue="value"  placeholder="Selecciona una competencia" style="width:100%;" class="w-full md:w-11rem">            
                <template #option="slotProps">
                    <div class="flex align-items-center" style="width: 400px; font-size:0.9rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                        <div>{{ slotProps.option.label }}</div>
                    </div>
                </template>
              </Dropdown>
            </div>
        </div>

          <div class="flex" style="width: 100%; justify-content: space-between;">
            <div class="mb-3" style="width: 100%;">
            <div><label>Docente</label></div>  
              <Dropdown v-model="curso.id_docente" :options="docentes2" filter optionLabel="nombres" optionValue="id"  placeholder="Selecciona una competencia" style="width:100%;" class="w-full md:w-11rem">            
                  <template #option="slotProps">
                      <div class="flex align-items-center" style="width: 400px; font-size:0.9rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                          <div>{{ slotProps.option.nombres }}</div>
                      </div>
                  </template>
              </Dropdown>
            </div>
          </div>
 
          <template #footer>
            <div class="flex" style="justify-content: flex-end;">
              <div>
                <Button label="Cancelar" outlined="" @click="visible = false" size="small" />
              </div>
              <Button label="Guardar" @click="guardar" size="small"/>
            </div>
          </template>

      </Dialog>
  
      <!--- END MODAL -->


      <!--- MODAL ASIGNACION -->

            <!--- MODAL -->
      <Dialog v-model:visible="modal_registro" modal header="Asignar Alumnos" :style="{ width: '900px' }">

        <!-- <pre> {{ cursoseleccionado }}</pre>
        <pre> {{ alumnos_seleccionados_registro }}</pre> -->
        <DataTable 
          v-model:selection="alumnos_seleccionados_registro"
          :row-selection="rowSelection"
          :value="alumnosregistro" 
          :class="'p-datatable-sm'"  
          tableStyle="min-width: 50rem" 
          style="font-size: .9rem;"
          :paginator="true" :rows="9"
        >
            <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
            <Column field="dni" header="DNI"></Column>
            <Column field="nombres" header="Nombres"></Column>
            <Column field="paterno" header="Paterno"></Column>
            <Column field="materno" header="Materno"></Column>
        </DataTable> 
    
        <div class="flex" style="width: 100%; justify-content: flex-end;">
          <Button severity="primary" style="font-size: 0.9rem"  text @click="asignar"> Asignar </Button>
        </div>

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
  import Breadcrumb from 'primevue/breadcrumb';
  import AutoComplete from 'primevue/autocomplete';

  const escuela = ref(null);
  
  const toast = useToast();
  const confirm = useConfirm();

  const modal_registro = ref(null)
  const alumnos_seleccionados_registro = ref([])

  const buscarDocente = ref("");

  const cursos = ref([]);
  const cursoseleccionado = ref(null);

  const competencias = ref([])
  const programas = ref([])
  const totalpaginas = ref(0)
  const pagina = ref(1)
  const buscar = ref("")

  const competencia = ref(null)
  
  const docentes = ref([]) 
  const visible = ref(false);
  const temp = ref("");
  
  const grupo = ref('A');
  const grupos = ref([
    {value:"A", label:"Grupo A"},
    {value:"B", label:"Grupo B"},
    {value:"C", label:"Grupo C"}
  ])

  const alumnosregistro = ref([])

  const items = ref([
      {label: 'Computer'},
      {label: 'Notebook'},
  ]);


  const docentes2 = ref([])
  const docente2 = ref(null)
  const escuelas = ref([])
  const detalle_curso = ref([])

  const op = ref();
  const toggle = (event) => {
      op.value.toggle(event);
  }

  const cursocompetencia = ref(null)
  const curso = ref({
    id: null,
    nombre:"",
    id_docente:"",
    grupo:"A",
    estado:true
  })
  
  const getDocentes =  async (event) => {
    let res = await axios.post(
    "get-docentes?page=" + pagina.value,
    { 
      term: buscar.value,
   }
    );
    docentes.value = res.data.datos.data;
    totalpaginas.value = res.data.datos.total;
  }
  
  const getProgramas =  async () => {
    let res = await axios.post(
    "get-programas?page=" + pagina.value,
    { term: "" }
    );
    programas.value = res.data.datos.data;
  }

  const getEscuelas =  async () => {
    let res = await axios.post(
    "get-escuelas", { term: "" }
    );
    escuelas.value = res.data.datos.data;
  }
  
  const getCompetencias =  async () => {
    let res = await axios.post(
    "get-competencias?page=",{ term: "" }
    );
    competencias.value = res.data.datos.data;
  }

  const getDocenteXcompetencia =  async () => {
    let res = await axios.post(
    "get-docente-competencia?page=",{ term: "", competencia:cursocompetencia.value }
    );
    docentes2.value = res.data.datos.data;
    docente2.value = res.data.datos.data[0].id;
  }
  

  const getCursos =  async () => {
    let res = await axios.post(
    "get-cursos?page=",{ term: "", competencia:competencia.value, escuela:escuela.value.escuela}
    );
    cursos.value = res.data.datos.data;
  }

  const guardar =  async () => {
    let res = await axios.post(
      "save-curso",
      { 
        id: curso.value.id,
        nombre: curso.value.nombre,
        id_competencia: cursocompetencia.value,
        id_docente: curso.value.id_docente,
        escuela: escuela.value.escuela,
        grupo: curso.value.grupo,
        estado: curso.value.estado        
      }
    );
  
    showToast(res.data.tipo, res.data.titulo, res.data.mensaje)
    getDocentes()  
    visible.value = false
    limpiar()
    // roles.value = res.data.datos.data;
  }

  const getDetalleCurso =  async () => {
    let res = await axios.post(
    "get-detalle-curso?page=",{ term: "", curso: cursoseleccionado.value.id, }
    );
    detalle_curso.value = res.data.datos.data;
  }


  const asignar =  async () => {
    let res = await axios.post(
      "asignar-curso-nivelacion",
      { 
        curso: cursoseleccionado.value.id,
        alumnos: alumnos_seleccionados_registro.value,
      }
    );
  
    showToast(res.data.tipo, res.data.titulo, res.data.mensaje)
    getDetalleCurso()  
    modal_registro.value = false
    //limpiar()
  }
  
  const eliminar =  async (id) => {
    let res = await axios.get(
    "delete-docente/"+id);
    showToast(res.data.tipo, res.data.titulo, res.data.mensaje)
    getDocentes() 
  }

  const getAlumnosRegistros =  async () => {
    let res = await axios.post(
    "get-alumnos-registro?page=",{ term: ""}
    );
    alumnosregistro.value = res.data.datos.data;
  }
  
  
  const emod = ref(false);

  const editar =  async (item) => {
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
    docente.value.fecha = item.f_nac
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
        docente.value.fecha = null
        temp.value = null
        docente.value.sexo = 'M'
        docente.value.estado = true
      }
  })




  
  watch(buscar, ( newValue, oldValue ) => {
      getDocentes()
  })

  watch(cursocompetencia, ( newValue, oldValue ) => {
      getDocenteXcompetencia()
  })
  
  watch(competencia, ( newValue, oldValue ) => {
      getCursos()
  })

  watch(escuela, ( newValue, oldValue ) => {
      getCursos()
  })

  watch(cursoseleccionado, ( newValue, oldValue ) => {
    getDetalleCurso()
  })
  
  
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


  const Inicio = () => { escuela.value = null; cursoseleccionado.value = null  }

  const resEsuela = () => { cursoseleccionado.value = null }




  getDocentes()
  getProgramas()
  getCompetencias()
  getEscuelas()
  getAlumnosRegistros()






  </script>
  

