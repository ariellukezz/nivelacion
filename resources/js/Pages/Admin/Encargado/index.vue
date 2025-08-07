<template>
  <Head title="Encargados del Sistema" />
  <AuthenticatedLayout>
    <div class="text-center mb-4">
      <h2 class="text-2xl font-bold text-gray-800">Datos del encargado del Sistema</h2>
    </div>
    <div class="bg-white shadow-xs p-4" style="height: calc(100vh - 110px); font-family: Arial, Helvetica, sans-serif;">
      <div>
        <div class="card">
          <Message severity="warn" sticky>
            La persona registrada será responsable y encargada del proceso de nivelación en el sistema. Al finalizar su cargo, podrá ser cambiada al estado INACTIVO.
Complete todos los datos requeridos para asegurar una comunicación constante durante el proceso
          </Message>
        </div>
        <div class="flex" style="justify-content: space-between;">
          <Button label="Encargado Nuevo" @click="visible = true" size="small" style="height: 40px;" />
          <span class="p-input-icon-left">
            <i class="pi pi-search" />
            <InputText v-model="buscar" style="padding-left: 40px; height: 40px;" placeholder="Buscar" />
          </span>
        </div>
      </div>
      <Toast />
      <ConfirmPopup></ConfirmPopup>

      <div>
        <div class="card">
          <DataTable :value="encargados" :class="'p-datatable-sm'" paginator :rows="10" :rowsPerPageOptions="[10, 20, 50]" tableStyle="min-width: 50rem" style="font-size: .9rem;">
            <Column field="dni" header="DNI"></Column>
            <Column field="nombres" header="Nombres">
              <template #body="{ data }">
                <div class="flex" style="justify-content: flex-start;">
                  <div>{{ data.nombres }} {{ data.apellidos }}</div>
                </div>
              </template>
            </Column>
            <Column field="correo" header="Correo"></Column>
            <Column field="num_celular" header="Celular"></Column>
            <Column field="cargo" header="Cargo"></Column>
            <Column field="estado" header="Estado" style="justify-content: center; display: flex;" width="70px">
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
            <Column field="id_encargado" header="Acciones" width="90px">
              <template #body="{ data }">
                <div class="flex">
                  <div class="mr-2">
                    <Button class="secondary" icon="pi pi-pencil" aria-label="Editar" @click="editar(data)" size="small" style="width: 25px; height: 25px;" />
                  </div>
                  <Button icon="pi pi-trash" severity="danger" aria-label="Eliminar" @click="confirmEliminar($event, data)" size="small" style="width: 25px; height: 25px;" />
                </div>
              </template>
            </Column>
          </DataTable>
        </div>
      </div>

      <Dialog v-model:visible="visible" :header="emod ? 'Editar Encargado' : 'Encargado Nuevo'" :style="{ width: '60vw', height: '600px' }">
        <div>
          <div class="flex mt-0 mb-3 align-items-center" style="justify-content: flex-end;">
            <div class="mr-3">
              <label>Estado</label>
              <div><InputSwitch v-model="encargado.estado" /></div>
            </div>
          </div>

          <div class="flex" style="width: 100%; justify-content: space-between;">
            <div class="mb-2" style="width: 48%;">
              <div><label>DNI</label></div>
              <InputText style="width: 100%; height: 40px;" type="text" v-model="encargado.dni" />
            </div>
            <div class="mb-2" style="width: 48%;">
              <div><label>Nombres</label></div>
              <InputText style="width: 100%; height: 40px;" type="text" v-model="encargado.nombres" />
            </div>
          </div>

          <div class="flex" style="width: 100%; justify-content: space-between;">
            <div class="mb-2" style="width: 48%;">
              <div><label>Apellidos</label></div>
              <InputText style="width: 100%; height: 40px;" type="text" v-model="encargado.apellidos" />
            </div>
            <div class="mb-2" style="width: 48%;">
              <div><label>Correo</label></div>
              <InputText style="width: 100%; height: 40px;" type="email" v-model="encargado.correo" />
            </div>
          </div>

          <div class="flex" style="width: 100%; justify-content: space-between;">
            <div class="mb-2" style="width: 48%;">
              <div><label>Celular (Opcional)</label></div>
              <InputText style="width: 100%; height: 40px;" type="text" v-model="encargado.num_celular" />
            </div>
            <div class="mb-2" style="width: 48%;">
              <div><label>Cargo</label></div>
              <InputText style="width: 100%; height: 40px;" type="text" v-model="encargado.cargo" />
            </div>
          </div>

          <div class="flex" style="width: 100%; justify-content: space-between;">
            <div class="mb-2" style="width: 48%;">
              <div><label>Fecha de Designación (Opcional)</label></div>
              <Calendar style="width: 100%; height: 40px;" dateFormat="yy-mm-dd" v-model="encargado.fecha_designacion" />
            </div>
            <div class="mb-2" style="width: 48%;">
              <div><label>Fecha de Fin de Designación (Opcional)</label></div>
              <Calendar style="width: 100%; height: 40px;" dateFormat="yy-mm-dd" v-model="encargado.fecha_fin_designacion" />
            </div>
          </div>

          <div class="mb-2" style="width: 100%;">
            <div><label>Observaciones (Opcional)</label></div>
            <InputText style="width: 100%; height: 40px;" type="text" v-model="encargado.observaciones" />
          </div>
        </div>

        <template #footer style="margin-top: -10px">
          <div class="flex" style="justify-content: flex-end;">
            <div>
              <Button label="Cancelar" outlined @click="visible = false" size="small" />
            </div>
            <Button label="Guardar" @click="guardar" size="small" />
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
import InputSwitch from 'primevue/inputswitch';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';
import ConfirmPopup from 'primevue/confirmpopup';
import Calendar from 'primevue/calendar';
import Tag from 'primevue/tag';
import Message from 'primevue/message';
import axios from 'axios';

const toast = useToast();
const confirm = useConfirm();

const encargados = ref([]);
const visible = ref(false);
const buscar = ref('');
const emod = ref(false);

const encargado = ref({
  id_encargado: null,
  dni: '',
  nombres: '',
  apellidos: '',
  correo: '',
  num_celular: '',
  cargo: '',
  fecha_designacion: null,
  fecha_fin_designacion: null,
  observaciones: '',
  estado: true,
  usuario_id: null,
});

const getEncargados = async () => {
  try {
    const res = await axios.post('/get-encargados', { term: buscar.value });
    encargados.value = res.data;
  } catch (error) {
    const message = error.response?.data?.message || 'No se pudieron cargar los encargados';
    showToast('error', 'Error', message);
  }
};

const guardar = async () => {
  try {
    const res = await axios.post('/save-encargado', {
      id_encargado: encargado.value.id_encargado,
      dni: encargado.value.dni,
      nombres: encargado.value.nombres,
      apellidos: encargado.value.apellidos,
      correo: encargado.value.correo,
      num_celular: encargado.value.num_celular || null,
      cargo: encargado.value.cargo,
      fecha_designacion: encargado.value.fecha_designacion ? encargado.value.fecha_designacion.toISOString().substring(0, 10) : null,
      fecha_fin_designacion: encargado.value.fecha_fin_designacion ? encargado.value.fecha_fin_designacion.toISOString().substring(0, 10) : null,
      observaciones: encargado.value.observaciones || null,
      estado: encargado.value.estado ? 1 : 0,
      usuario_id: encargado.value.usuario_id || null,
    });
    showToast('success', 'Éxito', res.data.message || 'Encargado guardado con éxito');
    getEncargados();
    visible.value = false;
    limpiar();
  } catch (error) {
    const message = error.response?.data?.message || 'No se pudo guardar el encargado';
    if (error.response?.status === 422) {
      const errors = error.response.data.errors;
      const firstError = Object.values(errors)[0][0];
      showToast('error', 'Error de validación', firstError);
    } else {
      showToast('error', 'Error', message);
    }
  }
};

const eliminar = async (id) => {
  try {
    const res = await axios.delete(`/delete-encargado/${id}`);
    showToast('success', 'Éxito', res.data.message || 'Encargado eliminado con éxito');
    getEncargados();
  } catch (error) {
    const message = error.response?.data?.message || 'No se pudo eliminar el encargado';
    showToast('error', 'Error', message);
  }
};

const editar = (item) => {
  visible.value = true;
  emod.value = true;
  encargado.value = {
    id_encargado: item.id_encargado,
    dni: item.dni,
    nombres: item.nombres,
    apellidos: item.apellidos,
    correo: item.correo,
    num_celular: item.num_celular || '',
    cargo: item.cargo,
    fecha_designacion: item.fecha_designacion ? new Date(item.fecha_designacion) : null,
    fecha_fin_designacion: item.fecha_fin_designacion ? new Date(item.fecha_fin_designacion) : null,
    observaciones: item.observaciones || '',
    estado: item.estado === 1,
    usuario_id: item.usuario_id || null,
  };
};

const limpiar = () => {
  encargado.value = {
    id_encargado: null,
    dni: '',
    nombres: '',
    apellidos: '',
    correo: '',
    num_celular: '',
    cargo: '',
    fecha_designacion: null,
    fecha_fin_designacion: null,
    observaciones: '',
    estado: true,
    usuario_id: null,
  };
  emod.value = false;
};

const confirmEliminar = (event, item) => {
  confirm.require({
    target: event.currentTarget,
    message: `¿Estás seguro de eliminar al encargado ${item.nombres} ${item.apellidos}?`,
    icon: 'pi pi-info-circle',
    acceptClass: 'p-button-danger',
    accept: () => {
      eliminar(item.id_encargado);
    },
    reject: () => {
      showToast('error', 'Cancelado', `Se ha cancelado la eliminación del encargado ${item.nombres} ${item.apellidos}`);
    },
  });
};

const showToast = (tipo, titulo, detalle) => {
  toast.add({ severity: tipo, summary: titulo, detail: detalle, life: 3000 });
};

watch(visible, (newValue) => {
  if (!newValue && emod.value) {
    limpiar();
  }
});

watch(buscar, () => {
  getEncargados();
});

getEncargados();
</script>
