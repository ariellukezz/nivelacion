<template>
    <Head title="Alumnos"/>
    <AuthenticatedLayout>
    <div class="bg-white shadow-xs p-4" style=" height: calc(100vh - 110px); font-family: Arial, Helvetica, sans-serif;">
        
        <!-- <pre>{{ config }}</pre> -->

        <div>
          <div class="flex" style="justify-content: space-between;">
            <Button label="Nuevo" @click="visible = true" size="small" style="height: 40px;"/>
          
            <span class="p-input-icon-left ">
                <i class="pi pi-search" />
                <InputText v-model="buscar" style="padding-left: 40px; height: 40px;" placeholder="Search" />
                <Button label="Nuevo" @click="toggle" size="small" style="height: 40px;">
                    <i class="pi pi-cog" />
                </Button>
            </span>

          </div>
        </div>
        <Toast />
        <ConfirmPopup></ConfirmPopup>
    
    <!-- 
        <pre>{{ programas }}</pre> -->
        <!-- <pre>{{ buscar }}</pre>  -->
        <!-- <AutoComplete v-model="buscar" optionLabel="label" :suggestions="programas" @complete="getProgramas" /> -->
        
        <!-- <pre>{{ usuarios }}</pre> -->
    
        <div>
          <div class="card">
            <div class="flex justify-content-center mb-4">
                <!-- <SelectButton v-model="size" :options="sizeOptions" optionLabel="label" dataKey="label" /> -->
            </div>
            <DataTable :value="usuarios" :class="'p-datatable-sm'"  tableStyle="min-width: 50rem" style="font-size: .9rem;">
                <Column field="dni" header="Dni"></Column>
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
                <Column field="tipo_examen" header="Tipo Examen"></Column>
                <Column field="programa" header="Programa">
                    <template #body="{ data }">
                        <div class="flex" style="justify-content: flex-start;">
                            <div style=" width: 230px; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                                <span> {{ data.programa }} </span>
                            </div>
                        </div>
                    </template>
                </Column>
                <Column v-if="conf_telefono === true" field="telefono" header="Telefono"></Column>
                <Column v-if="conf_colegio === true" field="colegio" header="Colegio"></Column>
                <Column v-if="conf_tipo_colegio === true" field="tipo_colegio" header="Tipo Colegio"></Column>
                <Column v-if="conf_estado_civil === true" field="estado_civil" header="Estado civil"></Column>
                <Column v-if="conf_area === true" field="area" header="Area"></Column>
                <Column v-if="conf_modalidad === true" field="modalidad" header="Modalidad"></Column>

                <!-- <Column field="id_programa" header="Acciones" width="90px"> 
                  <template #body="{ data }">
                    <div class="flex">
                      <div class="mr-2">
                        <Button class="secondary" icon="pi pi-pencil" aria-label="Submit" @click="editar(data)" size="small" style="width: 25px; height: 25px;"/>
                      </div>
                      <Button icon="pi pi-trash" severity="danger" aria-label="Submit" @click="confirm2($event, data)"  size="small"  style="width: 25px; height: 25px;"/>
                    </div>
                  </template>
                </Column> -->
            </DataTable>
          </div>
        </div>
    
        <Dialog v-model:visible="visible" modal header="Nuevo usuario" :style="{ width: '50vw' }">
    
          <!-- <pre>{{ usuario }}</pre> -->
    
          <div class="flex mt-2 align-items-center" style="justify-content: flex-end;" >
            <div class="mr-3"> <label>Estado</label> </div>  
            <InputSwitch v-model="usuario.estado" />
          </div>
    
          <div class="flex" style="width: 100%; justify-content: space-between;">
            <div class="mb-2" style="width: 48%;">
              <div><label>Nombres</label></div>  
              <InputText style="width: 100%; height: 40px;"  type="text" v-model="usuario.nombres" />
            </div>
    
            <div class="mb-2" style="width: 48%;">
              <div><label>Apellidos</label></div>  
              <InputText style="width: 100%; height: 40px;"  type="text" v-model="usuario.apellidos" />
            </div>
          </div>
    
          <div class="mb-2">
            <div><label>Rol</label></div>  
            <Dropdown 
              v-model:modelValue="usuario.rol" 
              optionValue="value" 
              :options="roles" 
              showClear
              style="height: 45px;"
              @complete="changeRol" 
              optionLabel="label" 
              placeholder="Selecciona el rol" 
              class="w-full md:w-10rem">
            </Dropdown> 
          </div>
    
          <div class="flex" style="width: 100%; justify-content: space-between;">
            <div class="mb-2" style="width: 48%;">
              <div><label>Correo</label></div>  
              <InputText style="width: 100%; height: 40px;"  type="text" v-model="usuario.email"/>
            </div>
    
            <div class="mb-2" style="width: 48%;">
              <div><label>Contraseña</label></div>
              <InputText style="width: 100%; height: 40px;"  type="password" v-model="usuario.password" />
            </div>
          </div>
    
          <div class="mb-2">
            <div><label>Programa</label></div>  
            <Dropdown 
              v-model="usuario.programa"  
              showClear
              style="height: 45px;"
              :options="programas" 
              @complete="getProgramas" 
              optionValue="value" 
              optionLabel="label" 
              placeholder="Selecciona el programa"
              class="w-full md:w-14rem"/>
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

        <OverlayPanel ref="op">
            <div class="card">
                <div class="flex mt-1" style="justify-content: flex-end;">Codigo: <InputSwitch v-model="conf_codigo" /></div>
                <div class="flex mt-1" style="justify-content: flex-end;">telefono: <InputSwitch v-model="conf_telefono" /></div>
                <div class="flex mt-1" style="justify-content: flex-end;">Colegio: <InputSwitch v-model="conf_colegio" /></div>
                <div class="flex mt-1" style="justify-content: flex-end;">Tipo colegio: <InputSwitch v-model="conf_tipo_colegio" /></div>
                <div class="flex mt-1" style="justify-content: flex-end;">Estado civil: <InputSwitch v-model="conf_estado_civil" /></div>
                <div class="flex mt-1" style="justify-content: flex-end;">area: <InputSwitch v-model="conf_area" /></div>
                <div class="flex mt-1" style="justify-content: flex-end;">modalidad: <InputSwitch v-model="conf_modalidad" /></div>
            </div>
        </OverlayPanel>
    
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
    import OverlayPanel from 'primevue/overlaypanel';
    
    const toast = useToast();
    const confirm = useConfirm();
    
    const programas = ref([])
    const totalpaginas = ref(0)
    const pagina = ref(1)
    const buscar = ref("")
    
    const roles = ref([])
    const usuarios = ref([]) 
    const visible = ref(false);
    
    

    const conf_telefono = ref(true);
    const conf_codigo = ref(false);
    const conf_colegio = ref(false);
    const conf_tipo_colegio = ref(false);
    const conf_estado_civil = ref(false);
    const conf_area = ref(true);
    const conf_modalidad = ref(false);

    const op = ref();
    const toggle = (event) => {
        op.value.toggle(event);
    }

    const usuario = ref({
      id: null,
      nombres:"",
      apellidos:"",
      rol:2,
      programa:3,
      email:"",
      password:"",
      estado:true
    })
    
    const getAlumnos =  async (event) => {
      let res = await axios.post(
      "get-alumnos?page=" + pagina.value,
      { 
        term: buscar.value,
        telefono: conf_telefono.value,
        codigo: conf_codigo.value,
        colegio: conf_colegio.value,
        tipo_colegio: conf_tipo_colegio.value,
        estado_civil: conf_estado_civil.value,
        area: conf_area.value,
        modalidad: conf_modalidad.value
     }
      );
      usuarios.value = res.data.datos.data;
      totalpaginas.value = res.data.datos.total;
    }
    
    const getProgramas =  async () => {
      let res = await axios.post(
      "get-programas?page=" + pagina.value,
      { term: "" }
      );
      programas.value = res.data.datos.data;
    }
    
    const getRoles =  async (term = "") => {
      let res = await axios.post(
      "get-roles?page=",
      { term: "" }
      );
      roles.value = res.data.datos.data;
    }
    
    const guardar =  async () => {
      let res = await axios.post(
        "save-usuario",
        { 
          id: usuario.value.id,
          nombres : usuario.value.nombres,
          apellidos : usuario.value.apellidos,
          email : usuario.value.email,
          password : usuario.value.password,
          programa : usuario.value.programa,
          estado : usuario.value.estado,
          rol : usuario.value.rol
        }
      );
    
      showToast(res.data.tipo, res.data.titulo, res.data.mensaje)
      getAlumnos()  
      visible.value = false
      limpiar()
      // roles.value = res.data.datos.data;
    }
    
    const eliminar =  async (id) => {
      let res = await axios.get(
      "delete-usuario/"+id);
      showToast(res.data.tipo, res.data.titulo, res.data.mensaje)
      getAlumnos() 
    }
    
    
    const editar =  async (item) => {
      visible.value = true;
      usuario.value.id = item.id
      usuario.value.nombres = item.nombres
      usuario.value.apellidos = item.apellidos
      usuario.value.email = item.email
      usuario.value.programa = item.id_programa
      usuario.value.rol = item.id_rol
      console.log(item);
    }
    
    const changeRol = (event) => {
      console.log("::ROL::",event);
    }
    
    watch(buscar, ( newValue, oldValue ) => {
        getAlumnos()
    })
    
    
    const showToast = (tipo, titulo, detalle) => {
        toast.add({ severity: tipo, summary: titulo, detail: detalle, life: 3000 });
    };
    
    const confirm2 = (event,user) => {
        confirm.require({
            target: event.currentTarget,
            message: '¿Estas seguro de eliminar al usuario '+ user.nombres+'?',
            icon: 'pi pi-info-circle',
            acceptClass: 'p-button-danger',
            accept: () => {
              eliminar(user.id)
            },
            reject: () => {
                toast.add({ severity: 'error', summary: 'Elimación cancelada', detail: 'Se ha cancelado la eliminación del usuario'+user.nombres, life: 3000 });
            }
        });
    };
    
    watch(visible, ( newValue, oldValue ) => {
      if(visible.value == false && usuario.value.id != null ){
        limpiar()
      }  
    
    })
    
    const limpiar = () => { 
      usuario.value.id= null,
      usuario.value.nombres = "",
      usuario.value.apellidos = "",
      usuario.value.rol = 2,
      usuario.value.programa = 3,
      usuario.value.email = "",
      usuario.value.password = "",
      usuario.value.estado = true
    }
    
    watch(conf_codigo, ( newValue, oldValue ) => { getAlumnos()})
    watch(conf_telefono, ( newValue, oldValue ) => { getAlumnos()})
    watch(conf_colegio, ( newValue, oldValue ) => { getAlumnos()})
    watch(conf_tipo_colegio, ( newValue, oldValue ) => { getAlumnos()})
    watch(conf_estado_civil, ( newValue, oldValue ) => { getAlumnos()})
    watch(conf_area, ( newValue, oldValue ) => { getAlumnos()})
    watch(conf_modalidad, ( newValue, oldValue ) => { getAlumnos()})
    
    // watch(programa, ( newValue, oldValue ) => {
    //     getRoles()
    // })
    
    
    
    getAlumnos()
    getProgramas()
    getRoles()
    
    </script>
    