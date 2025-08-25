<template>
  <Head title="Eventos" />
  <AuthenticatedLayout>
    <div class="text-center mb-4">
      <h2 class="text-2xl font-bold text-gray-800">Gestión de Eventos</h2>
    </div>

    <div>
      <Button label="Nuevo Evento" severity="info" @click="modal = true" />

      <Dialog v-model:visible="modal" modal header="Nuevo Evento" :style="{ width: '500px' }">
        <div class="flex flex-col gap-3">
          <div>
            <label>Nombre del Evento</label>
            <InputText type="text" v-model="nombre_evento" class="w-full" />
          </div>

          <div>
            <label>Fecha</label>
            <InputText type="date" v-model="fecha" class="w-full" />
          </div>

          <div>
            <label>Estado</label>
            <Dropdown v-model="estado" :options="estados" optionLabel="label" optionValue="value" class="w-full" />
          </div>

          <div class="flex justify-end mt-3">
            <Button label="Guardar" severity="info" @click="save()" />
          </div>
        </div>
      </Dialog>

      <ConfirmDialog></ConfirmDialog>
      <Toast />

      <DataTable :value="eventos" :class="'p-datatable-sm'" paginator :rows="10" tableStyle="min-width: 50rem" style="font-size: 0.8rem;">
        <Column field="id" header="ID"></Column>
        <Column field="nombre_evento" header="Nombre del Evento"></Column>
        <Column field="fecha" header="Fecha">
          <template #body="{ data }">
            {{ formatDate(data.fecha) }}
          </template>
        </Column>
        <Column field="estado" header="Estado">
          <template #body="{ data }">
            <Tag :value="data.estado" :severity="data.estado === 'activo' ? 'success' : 'danger'" :rounded="true" class="capitalize" />
          </template>
        </Column>
        <Column header="Acciones" width="120px">
          <template #body="{ data }">
            <div class="flex">
              <div class="mr-2">
                <Button class="secondary" icon="pi pi-pencil" aria-label="Editar" @click="abrirEditar(data)" size="small" style="width: 25px; height: 25px;" />
              </div>
              <Button icon="pi pi-trash" severity="danger" aria-label="Eliminar" @click="confirmarEliminar(data)" size="small" style="width: 25px; height: 25px;" />
            </div>
          </template>
        </Column>
      </DataTable>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import AuthenticatedLayout from '@/Layouts/LayoutSuperadmi.vue';
import { Head } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import ConfirmDialog from 'primevue/confirmdialog';
import Toast from 'primevue/toast';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import Tag from 'primevue/tag';

const confirm = useConfirm();
const toast = useToast();

const nombre_evento = ref("");
const fecha = ref("");
const estado = ref("activo");
const id = ref(null);

const eventos = ref([]);
const modal = ref(false);

const estados = ref([
  { label: 'Activo', value: 'activo' },
  { label: 'Inactivo', value: 'inactivo' }
]);

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
  return new Date(dateString).toLocaleDateString('es-ES', options);
};

const getEventos = async () => {
  try {
    const response = await axios.get('get-eventos');
    eventos.value = response.data;
  } catch (error) {
    showError('Error al obtener eventos');
  }
};

const save = async () => {
  try {
    const action = id.value ? 'actualizado' : 'creado';
    await axios.post('guardar-evento', {
      id: id.value,
      nombre_evento: nombre_evento.value,
      fecha: fecha.value,
      estado: estado.value
    });

    showSuccess(`Evento ${action} correctamente`);
    resetForm();
    modal.value = false;
    getEventos();
  } catch (error) {
    showError(error.response?.data?.message || 'Error al guardar evento');
  }
};

const confirmarEliminar = (item) => {
  confirm.require({
    message: `¿Estás seguro de cerrar el evento "${item.nombre_evento}"?`,
    header: 'Confirmación',
    icon: 'pi pi-exclamation-triangle',
    acceptLabel: 'Sí, cerrar',
    rejectLabel: 'Cancelar',
    accept: () => eliminarEvento(item),
    reject: () => {
      toast.add({ severity: 'info', summary: 'Cancelado', detail: 'No se cerró el evento', life: 3000 });
    }
  });
};

const eliminarEvento = async (item) => {
  try {
    await axios.get('eliminar-evento/' + item.id);
    showSuccess('Evento cerrado correctamente');
    getEventos();
  } catch (error) {
    const errorMsg = error.response?.data?.message || 'Error al cerrar evento';
    showError(errorMsg);
  }
};

const abrirEditar = (item) => {
  modal.value = true;
  id.value = item.id;
  nombre_evento.value = item.nombre_evento;
  fecha.value = item.fecha.split('T')[0];
  estado.value = item.estado;
};

const resetForm = () => {
  id.value = null;
  nombre_evento.value = "";
  fecha.value = "";
  estado.value = "activo";
};

const showSuccess = (message) => {
  toast.add({ severity: 'success', summary: 'Éxito', detail: message, life: 3000 });
};

const showError = (message) => {
  toast.add({ severity: 'error', summary: 'Error', detail: message, life: 5000 });
};

onMounted(() => {
  getEventos();
});
</script>
