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
              <InputText v-model="buscarescuela" style="padding-left: 40px; height: 40px;" placeholder="Buscar" />
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
      <!-- {{ escuela }} -->
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
          <Button severity="primary" @click="visible = true" style="height:40px"> Nuevo Curso </Button>
          <div>
            <div class="flex mb-3" style="justify-content: flex-end;">
              <span class="p-input-icon-left">
                  <i class="pi pi-search" />
                  <InputText v-model="buscarcurso" style="padding-left: 40px; height: 40px;" placeholder="Buscar" />
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
                <Column field="competencia" header="Curso"></Column>
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
      <!-- {{cursoseleccionado}} -->
      <div v-if="escuela !== null && cursoseleccionado !== null"> 

        <div class="flex" style="justify-content: space-between;">
          <Button severity="primary" @click="abrirseleccionar()" style="height:40px"> Seleccionar </Button>
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
      <Dialog v-model:visible="visible" modal :header="!curso.id?'Curso nuevo':'Editar Curso'" :style="{ width: '500px' }">
  
        <!-- {{ curso }} {{ cursocompetencia }} -->
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
              <Dropdown v-model="cursocompetencia" :options="competencias" optionLabel="label" optionValue="value"  placeholder="Seleccione una competencia" style="width:100%;" class="w-full md:w-11rem">            
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
              <div><label>Programa de estudio</label></div>  
              <Dropdown v-model="prog" :options="programasselect" filter optionLabel="label" optionValue="value"  placeholder="Seleccione un programa de estudio" style="width:100%;" class="w-full md:w-11rem">            
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

        <div v-if="alumnosregistro">
        
          <DataTable 
            v-model:selection="alumnos_seleccionados_registro"
            selectionMode="multiple" 
            dataKey="id"
            :metaKeySelection="false" 
            :row-selection="false"
            :value="alumnosregistro" 
            :class="'p-datatable-sm'"  
            tableStyle="min-width: 50rem" 
            style="font-size: .9rem;"
            :paginator="true" 
            :rows="9"
          >
              <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
              <Column field="programa" header="Programa"></Column>
              <Column field="dni" header="DNI"></Column>
              <Column field="nombres" header="Nombres"></Column>
              <Column field="paterno" header="Paterno"></Column>
              <Column field="materno" header="Materno"></Column>
          </DataTable> 
        </div>    
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
  import Tag from 'primevue/tag';

  const escuela = ref(null);
  
  const toast = useToast();
  const confirm = useConfirm();

  const modal_registro = ref(null)
  const alumnos_seleccionados_registro = ref([])

  const cursos = ref([]);
  const cursoseleccionado = ref(null);
  const buscarcurso = ref("")

  const competencias = ref([])
  const programas = ref([])
  const totalpaginas = ref(0)
  const pagina = ref(1)
  const buscar = ref("")
  const buscarescuela = ref("")
  const seleccionadosTemp = ref([])

  const competencia = ref(null)
  
  const docentes = ref([]) 
  const visible = ref(false);
  const temp = ref("");
  
  const grupo = ref('A');
  const grupos = ref([
    {value:"A", label:"Grupo A"},
    {value:"B", label:"Grupo B"},
    {value:"C", label:"Grupo C"},
    {value:"D", label:"Grupo D"},
    {value:"E", label:"Grupo E"},
  ])

  const prog = ref(null);
  const programasselect = ref([
    {value:1, label:"INGENIERIA AGRONOMICA"},
    {value:2, label:"INGENIERIA AGROINDUSTRIAL"},
    {value:3, label:"INGENIERIA TOPOGRAFICA Y AGRIMENSURA"},
    {value:4, label:"MEDICINA VETERINARIA Y ZOOTECNIA"},
    {value:5, label:"INGENIERIA ECONOMICA"},
    {value:6, label:"CIENCIAS CONTABLES"},
    {value:7, label:"ADMINISTRACION"},
    {value:8, label:"TRABAJO SOCIAL"},
    {value:9, label:"ENFERMERIA"},
    {value:10, label:"INGENIERIA DE MINAS"},
    {value:11, label:"HUMANIDADES"},
    {value:12, label:"SOCIOLOGIA"},
    {value:13, label:"TURISMO"},
    {value:14, label:"ANTROPOLOGIA"},
    {value:15, label:"CIENCIAS DE LA COMUNICACION SOCIAL"},
    {value:16, label:"ARTE: ARTES PLASTICAS"},
    {value:17, label:"ARTE: MUSICA"},
    {value:18, label:"ARTE: DANZA"},
    {value:19, label:"BIOLOGIA: ECOLOGIA"},
    {value:20, label:"BIOLOGIA: MICROBIOLOGIA Y LABORATORIO CLINICO"},
    {value:21, label:"BIOLOGIA: PESQUERIA"},
    {value:22, label:"EDUC. SEC.: CIENCIA, TECNOLOGIA Y AMBIENTE"},
    {value:23, label:"EDUC. SEC.: CIENCIAS SOCIALES"},
    {value:24, label:"EDUC. SEC.: LIT. PSICOLOGIA Y FILOSOFIA"},
    {value:25, label:"EDUC. SEC.: MATEMATICA, FISICA, COMP. E INFORMATICA"},
    {value:26, label:"EDUCACION PRIMARIA"},
    {value:27, label:"EDUCACION INICIAL"},
    {value:28, label:"EDUCACION FISICA"},
    {value:29, label:"INGENIERIA ESTADISTICA E INFORMATICA"},
    {value:30, label:"DERECHO"},
    {value:31, label:"INGENIERIA QUIMICA"},
    {value:32, label:"ODONTOLOGIA"},
    {value:33, label:"NUTRICION HUMANA"},
    {value:34, label:"INGENIERIA GEOLOGICA"},
    {value:35, label:"INGENIERIA METALURGICA"},
    {value:36, label:"INGENIERIA CIVIL"},
    {value:37, label:"ARQUITECTURA Y URBANISMO"},
    {value:38, label:"CIENCIAS FISICO MATEMATICAS: FISICA"},
    {value:39, label:"CIENCIAS FISICO MATEMATICAS: MATEMATICAS "},
    {value:40, label:"INGENIERIA AGRICOLA"},
    {value:41, label:"MEDICINA HUMANA"},
    {value:42, label:"INGENIERIA MECANICA ELECTRICA"},
    {value:43, label:"INGENIERIA ELECTRONICA"},
    {value:44, label:"INGENIERIA DE SISTEMAS"},
  ])

  const alumnosregistro = ref([])

  const docentes2 = ref([])
  const docente2 = ref(null)
  const escuelas = ref([])
  const detalle_curso = ref([])

  const cursocompetencia = ref(null)
  const curso = ref({
    id: null,
    nombre:"",
    id_docente:"",
    grupo:"A",
    estado:true
  })
  
  const getDocentes =  async () => {
    let res = await axios.post( "/coordinador/get-docentes?page=" + pagina.value, { term: buscar.value, } );
    docentes.value = res.data.datos.data;
    totalpaginas.value = res.data.datos.total;
  }
  
  const getProgramas =  async () => {
    let res = await axios.post( "/get-programas?page=" + pagina.value, { term: "" } );
    programas.value = res.data.datos.data;
  }

  const getEscuelas =  async () => {
    let res = await axios.post( "/get-escuelas", { term: buscarescuela.value } );
    escuelas.value = res.data.datos.data;
  }
  
  const getCompetencias =  async () => {
    let res = await axios.post( "/coordinador/get-competencias?page=",{ term: "" } );
    competencias.value = res.data.datos;
  }

  const getDocenteXcompetencia =  async () => {
    let res = await axios.post( "/get-docente-competencia?page=",{ term: "", competencia:cursocompetencia.value });
    docentes2.value = res.data.datos.data;
    docente2.value = res.data.datos.data[0].id;
  }  

  const getCursos =  async () => {
    let res = await axios.post( "get-cursos?page=",{ term: buscarcurso.value, competencia:competencia.value, escuela:escuela.value.escuela} );
    cursos.value = res.data.datos.data;
  }

  const emod = ref(false);

  const editar =  async (item) => {
    visible.value = true;
    emod.value = true;
    curso.value.id = item.id;
    curso.value.nombre = item.nombre;
    curso.value.grupo = item.grupo;
    curso.value.id_docente = item.id_docente;
    cursocompetencia.value = item.id_competencia;
    curso.value.id = item.id;
    if(item.estado === 1) { curso.value.estado = true } else { curso.value.estado = false }
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
        estado: curso.value.estado,
        id_programa: prog.value 
      }
    );
  
    showToast(res.data.tipo, res.data.titulo, res.data.mensaje)
    getCursos()  
    visible.value = false
    limpiar()
    // roles.value = res.data.datos.data;
  }

  const getDetalleCurso =  async () => {
    let res = await axios.post( "get-detalle-curso?page=",{ term: "", curso: cursoseleccionado.value.id, } );
    detalle_curso.value = res.data.datos.data;
    alumnos_seleccionados_registro.value = res.data.registrados.data;
    seleccionadosTemp.value = res.data.registrados.data ;
  }
  const diferenciaAB = ref(null);
  const diferenciaBA = ref(null);

  const asignar =  async () => {
    compararArrays();
    compararArrays2();
    let res = await axios.post(
      "asignar-curso-nivelacion",
      {
        curso: cursoseleccionado.value.id,
        alumnos: alumnos_seleccionados_registro.value,
        anteriores: seleccionadosTemp.value,
        diferencia: diferenciaAB.value,
        diferencia2: diferenciaBA.value
      }
    );
  
    showToast(res.data.tipo, res.data.titulo, res.data.mensaje)
    getDetalleCurso()  
    modal_registro.value = false
    //limpiar()
  }

  const compararArrays = () => {
    diferenciaAB.value = alumnos_seleccionados_registro.value.filter(objeto1 => {
      return !seleccionadosTemp.value.some(objeto2 => objeto2.id === objeto1.id);
    });
    console.log('Elementos en array1 que no están en array2:', diferenciaAB.value);
  };

  const compararArrays2 = () => {
    diferenciaBA.value = seleccionadosTemp.value.filter(objeto1 => {
      return !alumnos_seleccionados_registro.value.some(objeto2 => objeto2.id === objeto1.id);
    });
    console.log('Elementos en array1 que no están en array2:', diferenciaBA.value);
  };

  
  const eliminar =  async (id) => {
    let res = await axios.get("delete-docente/"+id );
    showToast(res.data.tipo, res.data.titulo, res.data.mensaje)
    getDocentes() 
  }

  const eliminarcurso =  async (id) => {
    let res = await axios.get("delete-curso/"+id );
    showToast(res.data.tipo, res.data.titulo, res.data.mensaje)
    getCursos();
  }

  const getAlumnosRegistros =  async () => {
    let res = await axios.post( "/get-alumnos-registro?page=",{ term: "", escuela: escuela.value.id, curso: cursoseleccionado.value.id_competencia });
    alumnosregistro.value = res.data.datos;
  }
  
  watch(visible, ( newValue, oldValue ) => {
      if(emod.value == true  && visible.value == false ){
        visible.value = false
        curso.value.id = null;
        curso.value.nombre = null;
        curso.value.grupo = null;
        curso.value.estado = true
     }
})

    
  watch(buscarescuela, ( newValue, oldValue ) => { getEscuelas() })
  watch(buscarcurso, ( newValue, oldValue ) => { getCursos() })  
  watch(buscar, ( newValue, oldValue ) => { getDocentes(); })
  watch(cursocompetencia, ( newValue, oldValue ) => { getDocenteXcompetencia(); })  
  watch(competencia, ( newValue, oldValue ) => { getCursos(); })
  watch(escuela, ( newValue, oldValue ) => { getCursos(); })

  watch(cursoseleccionado, ( newValue, oldValue ) => {
    curso.value = cursoseleccionado.value
    getDetalleCurso()
    getAlumnosRegistros()
  })

  const abrirseleccionar = () => {  modal_registro.value = true  }
  
  const showToast = (tipo, titulo, detalle) => {
      toast.add({ severity: tipo, summary: titulo, detail: detalle, life: 3000 });
  };
  
  const confirm2 = (event,doc) => {
      confirm.require({
          target: event.currentTarget,
          message: '¿Estas seguro de eliminar el docente '+ doc.nombre+'?',
          icon: 'pi pi-info-circle',
          acceptClass: 'p-button-danger',
          accept: () => {
            eliminarcurso(doc.id)
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
  // getAlumnosRegistros()

</script>