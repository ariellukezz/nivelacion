<template>
<Head title="Coordinadores"/>
<AuthenticatedLayout>
<div class="bg-white shadow-xs p-4" style=" height: calc(100vh - 110px); ">
    
    <div>
      <div class="flex" style="justify-content: space-between;">
        <Button label="Nuevo" @click="abrir()" size="small" style="height: 40px;"/>
      
        <span class="p-input-icon-left ">
            <i class="pi pi-search" />
            <InputText v-model="buscar" class="lt" style="padding-left: 40px; height: 40px;" placeholder="Search" />
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
        <DataTable :value="coordinadores" :class="'p-datatable-sm lt'"  tableStyle="min-width: 50rem">
            <Column field="dni" header="DNI"></Column>
            <Column field="nombres" header="Nombres"></Column>
            <Column field="apellidos" header="Apellidos"></Column>
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

    <Dialog v-model:visible="visible" modal :header="titulomodal" :style="{ width: '50vw' }">

      <div class="flex mt-2 align-items-center" style="justify-content: flex-end;" >
        <div class="mr-3"> <label>Estado</label> </div>  
        <InputSwitch v-model="coordinador.estado" />
      </div>

      <div class="flex" style="width: 100%; justify-content: space-between;">
        <div class="mb-2" style="width: 48%;">
          <div><label>Nombres</label></div>  
          <InputText style="width: 100%; height: 40px;"  type="text" v-model="coordinador.nombres" />
        </div>

        <div class="mb-2" style="width: 48%;">
          <div><label>Apellidos</label></div>  
          <InputText style="width: 100%; height: 40px;"  type="text" v-model="coordinador.apellidos" />
        </div>
      </div>
      
      <div class="flex" style="width: 100%; justify-content: space-between;">
        <div class="mb-2" style="width: 48%;">
          <div><label>Dni</label></div>  
          <InputText style="width: 100%; height: 40px;"  type="text" v-model="coordinador.dni"/>
        </div>

        <div class="mb-2" style="width: 48%;">
          <div><label>Celular</label></div>
          <InputText style="width: 100%; height: 40px;"  type="text" v-model="coordinador.celular" />
        </div>
      </div>

      <div class="mb-2">
        <div><label>Correo</label></div>  
        <InputText style="width: 100%; height: 40px;"  type="text" v-model="coordinador.email" />
      </div>

      <div class="mb-2">
        <div><label>Escuela</label></div>  
        <Dropdown 
          v-model="coordinador.escuela"  
          showClear
          style="height: 45px;"
          :options="escuelas" 
          @complete="getEscuelas" 
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


const toast = useToast();
const confirm = useConfirm();

const escuelas = ref([])
const totalpaginas = ref(0)
const pagina = ref(1)
const buscar = ref("")

const roles = ref([])
const coordinadores = ref([]) 
const visible = ref(false);


const titulomodal = ref("Nuevo Coordinador");


const coordinador = ref({
  id: null,
  nombres:"",
  apellidos:"",
  escuela:null,
  dni: "",
  email:"",
  celular:"",
  estado:true
})

const getcoordinadores =  async (event) => {
  let res = await axios.post(
  "/coordinador/get-coordinadores?page=" + pagina.value,
  { term: buscar.value }
  );
  coordinadores.value = res.data.datos.data;
  totalpaginas.value = res.data.datos.total;
}

const getEscuelas =  async () => {
  let res = await axios.post(
  "/coordinador/get-escuelas", { term: "" } );
  escuelas.value = res.data.datos.data;
  coordinador.value.escuela = res.data.datos.data[0].value;
}


const guardar =  async () => {
  let res = await axios.post(
    "/coordinador/save-coordinador",
    { 
      id: coordinador.value.id,
      nombres: coordinador.value.nombres,
      apellidos: coordinador.value.apellidos,
      email: coordinador.value.email,
      escuela: coordinador.value.escuela,
      estado: coordinador.value.estado,
      dni: coordinador.value.dni,
      celular: coordinador.value.celular
    }
  );

  showToast(res.data.tipo, res.data.titulo, res.data.mensaje)
  getcoordinadores()  
  visible.value = false
  limpiar()
  // roles.value = res.data.datos.data;
}

const eliminar =  async (id) => {
  let res = await axios.get(
  "/coordinador/delete-coordinador/"+id);
  showToast(res.data.tipo, res.data.titulo, res.data.mensaje)
  getcoordinadores() 
}

const abrir = () => {
  titulomodal.value = 'Nuevo Coordinador'
  visible.value = true;
}

const editar =  async (item) => {

  titulomodal.value = 'Editar Coordinador'
  visible.value = true;
  coordinador.value.id = item.id
  coordinador.value.nombres = item.nombres
  coordinador.value.apellidos = item.apellidos
  coordinador.value.email = item.correo
  coordinador.value.escuela = item.id_escuela
  coordinador.value.dni = item.dni
  coordinador.value.celular = item.celular
  if(item.estado == 1) {coordinador.value.estado = true} else { coordinador.value.estado = false }

  console.log(item);
}

watch(buscar, ( newValue, oldValue ) => {
   getcoordinadores()
})


const showToast = (tipo, titulo, detalle) => {
    toast.add({ severity: tipo, summary: titulo, detail: detalle, life: 3000 });
};

const confirm2 = (event,user) => {
    confirm.require({
        target: event.currentTarget,
        message: '¿Estas seguro de eliminar al coordinador '+ user.nombres+'?',
        icon: 'pi pi-info-circle',
        acceptClass: 'p-button-danger',
        accept: () => {
          eliminar(user.id)
        },
        reject: () => {
            toast.add({ severity: 'error', summary: 'Elimación cancelada', detail: 'Se ha cancelado la eliminación del coordinador'+user.nombres, life: 3000 });
        }
    });
};

watch(visible, ( newValue, oldValue ) => {
  if(visible.value == false && coordinador.value.id != null ){
    limpiar()
  }  

})

const limpiar = () => { 
  coordinador.value.id= null,
  coordinador.value.nombres = "",
  coordinador.value.apellidos = "",
  coordinador.value.dni = "",
  coordinador.value.celular = "",
  coordinador.value.email = "",
  coordinador.value.estado = true
}


// watch(rol, ( newValue, oldValue ) => {
//     getRoles()
// })

// watch(programa, ( newValue, oldValue ) => {
//     getRoles()
// })



getcoordinadores()
getEscuelas()

</script>
<style scoped>
.lt{
  font-size: 0.9rem;
}
</style>