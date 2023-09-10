<template>
  <header class="z-10 py-4 bg-white shadow-md" style="background-color:white; height: 75px;" >
    <div class="container flex justify-between items-center px-6 mx-auto h-full text-purple-600 md:justify-end">
      <!-- Mobile hamburger -->
      <button @click="$page.props.showingMobileMenu = !$page.props.showingMobileMenu" class="p-1 mr-5 -ml-1 rounded-md md:hidden focus:outline-none focus:shadow-outline-purple" aria-label="Menu">
        <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
        </svg>
      </button>

      <Dropdown v-if="usuario.usuario != null" style="cursor: pointer;" >
        <template #trigger >
          <div class="flex" style="align-items: center; height: 37px; color: #000000D9;">
          <div style="text-align: end;  margin-top: 0px;">
            <div style=" width: 200px; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
              <span style="font-size: 0.9rem;">{{ usuario.usuario.escuela }}</span> 
            </div>
            <div style="margin-top: -7px;">
              <span style="font-size: 0.9rem; font-weight: bold;">{{ usuario.usuario.nombres }} </span>
            </div>
          </div>
          <div style="margin-left: 10px;">
            <i class="pi pi-angle-down"></i>
          </div>
        </div>
        </template>

        <template #content>
          <!-- <DropdownLink :href="route('profile.edit')">
            <template #icon>
              <svg class="mr-3 w-4 h-4" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
              </svg>
              Profile
            </template>
          </DropdownLink> -->

          <DropdownLink :href="route('logout')" method="post" as="button">
            <template #icon>
              <svg class="mr-3 w-4 h-4" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
              </svg>
              Salir del Sistema
            </template>
          </DropdownLink>
        </template>
      </Dropdown>

      <div v-if="usuario.usuario">
        <div v-if="usuario.usuario.e_contra == 1">
          <Dialog v-model:visible="modalContra" modal header="Cambiar contraseña" :style="{ width: '360px' }">
            <label>Nueva contraseña</label>
            <div style="width: 100%;"> 
              <InputText type="password" v-model="contra" style="width: 315px;"/>
            </div>
            <div class="flex justify-end mt-5">
              <Button @click="saveContra" style="width: 100%; justify-content: center;"> Cambiar contraseña </Button>
            </div>
          </Dialog>
        </div>
      </div>


      <!-- <Dropdown v-else>
        <template #trigger >
          <div class="flex" style="align-items: center; height: 37px; color: #000000D9;">
          <div style="text-align: end;  margin-top: 0px;">
            <div style=" width: 200px; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
              <span style="font-size: 0.9rem;">Administrador </span>
            </div>
            <div style="margin-top: -7px;">
              <span style="font-size: 0.9rem; font-weight: bold;"> Usuario </span>
            </div>
          </div>
          <div style="margin-left: 10px;">
            <i class="pi pi-angle-down"></i>
          </div>
        </div>
        </template>

        <template #content>
          <DropdownLink :href="route('profile.edit')">
            <template #icon>
              <svg class="mr-3 w-4 h-4" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
              </svg>
              Profile
            </template>
          </DropdownLink>

          <DropdownLink :href="route('logout')" method="post" as="button">
            <template #icon>
              <svg class="mr-3 w-4 h-4" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
              </svg>
              Log Out
            </template>
          </DropdownLink>
        </template>
      </Dropdown> -->

      

    </div>
  </header>
</template>

<script setup>
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import Button from 'primevue/button';
import SplitButton from 'primevue/splitbutton';
import { ref } from 'vue';
import 'primeicons/primeicons.css';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Toast from 'primevue/toast';
import { useToast } from "primevue/usetoast";


const toast = useToast();

const showToast = (tipo, titulo, detalle) => {
  toast.add({ severity: tipo, summary: titulo, detail: detalle, life: 3000 });
};

showToast('success', 'GUARDADO', '--');

const usuario = defineProps(['usuario'])
const modalContra = ref(true)
const contra = ref("")

const saveContra =  async () => {
  let res = await axios.post("/save-contrasenia",{ contra: contra.value });
  modalContra.value = false;
  showToast(res.data.tipo, res.data.titulo, res.data.mensaje);

}

//import Dropdown from 'primevue/dropdown';
</script>