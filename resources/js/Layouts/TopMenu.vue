<template>
  <header
    class="z-10 py-4 bg-white shadow-md"
    style="background-color: white; height: 75px;"
  >
    <div
      class="container flex justify-between items-center px-6 mx-auto h-full text-purple-600 md:justify-end"
    >
      <!-- Menú móvil -->
      <button
        type="button"
        @click="$page.props.showingMobileMenu = !$page.props.showingMobileMenu"
        class="p-1 mr-5 -ml-1 rounded-md md:hidden focus:outline-none focus:shadow-outline-purple"
        aria-label="Menu"
      >
        <svg
          class="w-6 h-6"
          aria-hidden="true"
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path
            fill-rule="evenodd"
            d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
            clip-rule="evenodd"
          />
        </svg>
      </button>

      <!-- Usuario -->
      <Dropdown
        v-if="usuario.usuario != null"
        style="cursor: pointer;"
      >
        <template #trigger>
          <div
            class="flex"
            style="align-items: center; height: 37px; color: #000000D9;"
          >
            <div style="text-align: end; margin-top: 0px;">
              <div
                style="
                  width: 200px;
                  white-space: nowrap;
                  text-overflow: ellipsis;
                  overflow: hidden;
                "
              >
                <span style="font-size: 0.9rem;">
                  {{ usuario.usuario.escuela }}
                </span>
              </div>

              <div style="margin-top: -7px;">
                <span style="font-size: 0.9rem; font-weight: bold;">
                  {{ usuario.usuario.nombres }}
                </span>
              </div>
            </div>

            <div style="margin-left: 10px;">
              <i class="pi pi-angle-down"></i>
            </div>
          </div>
        </template>

        <template #content>
          <button
            type="button"
            @click="abrirPerfil"
            class="inline-flex w-full items-center rounded-md px-2 py-2 text-sm font-semibold transition-colors duration-150 hover:bg-gray-100 hover:text-gray-800"
          >
            <i class="pi pi-user mr-3"></i>
            Mi perfil
          </button>

          <DropdownLink
            :href="route('logout')"
            method="post"
            as="button"
          >
            <template #icon>
              <i class="pi pi-sign-out mr-3"></i>
            </template>

            Salir del Sistema
          </DropdownLink>
        </template>
      </Dropdown>

      <!-- Modal Mi Perfil -->
      <Dialog
        v-model:visible="modalPerfil"
        modal
        header="Mi perfil"
        :style="{ width: '520px' }"
      >
        <div
          v-if="loadingPerfil"
          class="py-5 text-center"
        >
          Cargando información...
        </div>

        <div v-else-if="perfil">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <span class="text-sm text-gray-500">
                Nombres y apellidos
              </span>

              <p class="font-semibold text-gray-800">
                {{ perfil.nombre_completo || '-' }}
              </p>
            </div>

            <div>
              <span class="text-sm text-gray-500">
                Correo
              </span>

              <p class="font-semibold text-gray-800">
                {{ perfil.email || '-' }}
              </p>
            </div>

            <div>
              <span class="text-sm text-gray-500">
                Rol
              </span>

              <p class="font-semibold text-gray-800">
                {{ perfil.rol || '-' }}
              </p>
            </div>

            <div v-if="perfil.documento">
              <span class="text-sm text-gray-500">
                Documento
              </span>

              <p class="font-semibold text-gray-800">
                {{ perfil.documento }}
              </p>
            </div>

            <div v-if="perfil.escuela">
              <span class="text-sm text-gray-500">
                Escuela Profesional
              </span>

              <p class="font-semibold text-gray-800">
                {{ perfil.escuela }}
              </p>
            </div>

            <div v-if="perfil.programa">
              <span class="text-sm text-gray-500">
                Programa
              </span>

              <p class="font-semibold text-gray-800">
                {{ perfil.programa }}
              </p>
            </div>

            <div v-if="perfil.telefono">
              <span class="text-sm text-gray-500">
                Celular
              </span>

              <p class="font-semibold text-gray-800">
                {{ perfil.telefono }}
              </p>
            </div>
          </div>

          <!-- Cambio de contraseña -->
          <div class="border-t mt-6 pt-5">
            <h3 class="font-bold text-gray-800 mb-4">
              Cambiar contraseña
            </h3>

            <div class="mb-3">
              <label class="block mb-1">
                Contraseña actual
              </label>

              <Password
                v-model="passwordForm.actual"
                toggleMask
                :feedback="false"
                class="w-full"
                inputClass="w-full"
                autocomplete="current-password"
                />
            </div>

            <div class="mb-3">
              <label class="block mb-1">
                Nueva contraseña
              </label>

              <Password
                v-model="passwordForm.nueva"
                toggleMask
                :feedback="false"
                class="w-full"
                inputClass="w-full"
                autocomplete="new-password"
                />

              <small class="text-gray-500">
                La contraseña debe tener como mínimo 5 caracteres.
              </small>
            </div>

            <div class="mb-4">
              <label class="block mb-1">
                Confirmar nueva contraseña
              </label>

              <Password
                v-model="passwordForm.confirmacion"
                toggleMask
                :feedback="false"
                class="w-full"
                inputClass="w-full"
                autocomplete="new-password"
                />
            </div>

            <div class="flex justify-end">
              <Button
                type="button"
                label="Cambiar contraseña"
                icon="pi pi-key"
                :loading="cambiandoPassword"
                @click.prevent="cambiarPassword"
              />
            </div>
          </div>
        </div>
      </Dialog>

      <!-- Cambio obligatorio de contraseña -->
      <div v-if="usuario.usuario">
        <div v-if="usuario.usuario.e_contra == 1">
          <Dialog
            v-model:visible="modalContra"
            modal
            header="Cambiar contraseña"
            :closable="false"
            :style="{ width: '360px' }"
          >
            <label>Nueva contraseña</label>

            <div style="width: 100%;">
              <Password
                v-model="contra"
                toggleMask
                :feedback="false"
                class="w-full"
                inputClass="w-full"
                autocomplete="new-password"
                />
            </div>

            <small class="text-gray-500">
              La contraseña debe tener como mínimo 5 caracteres.
            </small>

            <div class="flex justify-end mt-5">
              <Button
                type="button"
                @click="saveContra"
                style="width: 100%; justify-content: center;"
              >
                Cambiar contraseña
              </Button>
            </div>
          </Dialog>
        </div>
      </div>

      <!-- Notificación inicial -->
      <Dialog
        v-model:visible="modalNoti"
        modal
        header="Notificación"
        :style="{ width: '520px' }"
      >
        <div
          v-if="loadingNoti"
          class="py-4 text-center"
        >
          Cargando...
        </div>

        <div v-else>
          <div
            v-if="!noti"
            class="text-gray-500"
          >
            No hay notificaciones pendientes.
          </div>

          <div
            v-else
            class="space-y-3 p-3 rounded-md"
            :class="{
              'border-l-4 border-green-500 bg-green-50':
                noti.tipo === 'success',

              'border-l-4 border-yellow-500 bg-yellow-50':
                noti.tipo === 'warning' ||
                noti.tipo === 'warn',

              'border-l-4 border-red-500 bg-red-50':
                noti.tipo === 'error',

              'border-l-4 border-blue-500 bg-blue-50':
                ![
                  'success',
                  'warning',
                  'warn',
                  'error'
                ].includes(noti.tipo)
            }"
          >
            <div class="font-semibold text-base">
              {{ noti.titulo || 'Aviso' }}
            </div>

            <div
              v-if="noti.imagen_url"
              class="mt-1"
            >
              <img
                :src="noti.imagen_url"
                alt="imagen de la notificación"
                class="w-full rounded-md max-h-72 object-contain"
              />
            </div>

            <div class="font-bold text-sm whitespace-pre-line">
              {{ noti.mensaje }}
            </div>

            <div class="flex justify-end gap-2 mt-4">
              <a
                v-if="noti.url"
                :href="noti.url"
                target="_blank"
                rel="noopener"
                class="px-3 py-2 rounded-md border text-sm hover:bg-gray-50 underline"
              >
                Ir al enlace
              </a>

              <Button
                type="button"
                @click="leerNoti"
              >
                Leí la notificación y estoy informado
              </Button>
            </div>
          </div>
        </div>
      </Dialog>

      <Toast />
    </div>
  </header>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';


import 'primeicons/primeicons.css';

const usuario = defineProps(['usuario']);

const toast = useToast();

const showToast = (tipo, titulo, detalle) => {
  toast.add({
    severity: tipo,
    summary: titulo,
    detail: detalle,
    life: 4000
  });
};


/* ==========================================================
   CAMBIO OBLIGATORIO DE CONTRASEÑA
========================================================== */

const modalContra = ref(true);
const contra = ref('');

const saveContra = async () => {
  if (!contra.value) {
    showToast(
      'warn',
      'CONTRASEÑA',
      'Ingrese una nueva contraseña.'
    );

    return;
  }

  if (contra.value.length <= 4) {
    showToast(
      'warn',
      'CONTRASEÑA',
      'La contraseña debe tener como mínimo 5 caracteres.'
    );

    return;
  }

  try {
    const res = await axios.post('/save-contrasenia', {
      contra: contra.value
    });

    showToast(
      res.data.tipo,
      res.data.titulo,
      res.data.mensaje
    );

    if (!res.data.estado) {
      return;
    }

    contra.value = '';
    modalContra.value = false;

  } catch (error) {
    const errores = error.response?.data?.errors;

    const mensaje =
      errores?.contra?.[0] ||
      error.response?.data?.mensaje ||
      error.response?.data?.message ||
      'No se pudo modificar la contraseña.';

    showToast(
      'error',
      'ERROR',
      mensaje
    );
  }
};


/* ==========================================================
   PERFIL
========================================================== */

const modalPerfil = ref(false);
const loadingPerfil = ref(false);
const cambiandoPassword = ref(false);
const perfil = ref(null);

const passwordForm = ref({
  actual: '',
  nueva: '',
  confirmacion: ''
});

const abrirPerfil = async () => {
  modalPerfil.value = true;
  loadingPerfil.value = true;

  try {
    const res = await axios.get('/mi-perfil');

    perfil.value = res.data.datos;

  } catch (error) {
    showToast(
      'error',
      'ERROR',
      error.response?.data?.mensaje ||
      error.response?.data?.message ||
      'No se pudo cargar la información del perfil.'
    );

  } finally {
    loadingPerfil.value = false;
  }
};

const cambiarPassword = async () => {
  if (
    !passwordForm.value.actual ||
    !passwordForm.value.nueva ||
    !passwordForm.value.confirmacion
  ) {
    showToast(
      'warn',
      'DATOS INCOMPLETOS',
      'Complete todos los campos.'
    );

    return;
  }

  // No se permite 4 caracteres o menos
  if (passwordForm.value.nueva.length <= 4) {
    showToast(
      'warn',
      'CONTRASEÑA',
      'La nueva contraseña debe tener como mínimo 5 caracteres.'
    );

    return;
  }

  if (
    passwordForm.value.nueva !==
    passwordForm.value.confirmacion
  ) {
    showToast(
      'warn',
      'CONTRASEÑA',
      'La confirmación de la contraseña no coincide.'
    );

    return;
  }

  cambiandoPassword.value = true;

  try {
    const res = await axios.post('/save-contrasenia', {
      current_password: passwordForm.value.actual,
      password: passwordForm.value.nueva,
      password_confirmation: passwordForm.value.confirmacion
    });

    showToast(
      res.data.tipo,
      res.data.titulo,
      res.data.mensaje
    );

    if (!res.data.estado) {
      return;
    }

    passwordForm.value = {
      actual: '',
      nueva: '',
      confirmacion: ''
    };

  } catch (error) {
    const errores = error.response?.data?.errors;

    const mensaje =
      errores?.current_password?.[0] ||
      errores?.password?.[0] ||
      error.response?.data?.mensaje ||
      error.response?.data?.message ||
      'No se pudo modificar la contraseña.';

    showToast(
      'error',
      'ERROR',
      mensaje
    );

  } finally {
    cambiandoPassword.value = false;
  }
};


/* ==========================================================
   NOTIFICACIONES
========================================================== */

const modalNoti = ref(false);
const noti = ref(null);
const loadingNoti = ref(false);

const cargarNoti = async () => {
  loadingNoti.value = true;

  try {
    const { data } = await axios.post('/get-noti');

    noti.value = data?.datos ?? null;

    if (noti.value) {
      modalNoti.value = true;
    }

  } catch (error) {
    console.error(error);

  } finally {
    loadingNoti.value = false;
  }
};

const leerNoti = async () => {
  try {
    if (noti.value?.id) {
      await axios.post(`/read-noti/${noti.value.id}`);
    }

  } catch (error) {
    console.error(error);

  } finally {
    modalNoti.value = false;
    noti.value = null;
  }
};


/* ==========================================================
   INICIO
========================================================== */

onMounted(async () => {
  await cargarNoti();
});
</script>
