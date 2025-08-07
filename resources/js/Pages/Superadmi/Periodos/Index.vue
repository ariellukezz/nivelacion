<template>
  <Head title="Periodos" />
  <AuthenticatedLayout>
    <div class="text-center mb-4">
      <h2 class="text-2xl font-bold text-gray-800">Gestión de Periodos</h2>
    </div>
    <div>
      <Button label="Nuevo Periodo" severity="info" @click="modal = true" />

      <Dialog v-model:visible="modal" modal header="Nuevo Periodo" :style="{ width: '500px' }">
        <div class="flex flex-col gap-3">
          <div>
            <label>Nombre</label>
            <InputText type="text" v-model="nombre" class="w-full" />
          </div>

          <div class="flex gap-3">
            <div class="flex-1">
              <label>Fecha Inicio</label>
              <InputText type="date" v-model="fecha_inicio" class="w-full" />
            </div>
            <div class="flex-1">
              <label>Fecha Fin</label>
              <InputText type="date" v-model="fecha_fin" class="w-full" />
            </div>
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

      <DataTable :value="periodos" :class="'p-datatable-sm'" paginator :rows="10" tableStyle="min-width: 50rem"
        style="font-size: 0.8rem;">
        <Column field="id_periodo" header="ID"></Column>
        <Column field="nombre" header="Nombre"></Column>
        <Column field="fecha_inicio" header="Fecha Inicio">
          <template #body="{ data }">
            {{ formatDate(data.fecha_inicio) }}
          </template>
        </Column>
        <Column field="fecha_fin" header="Fecha Fin">
          <template #body="{ data }">
            {{ formatDate(data.fecha_fin) }}
          </template>
        </Column>
        <Column field="estado" header="Estado">
        <template #body="{ data }">
            <Tag
            :value="data.estado"
            :severity="data.estado === 'activo' ? 'success' : 'danger'"
            :rounded="true"
            class="capitalize"
            />
        </template>
        </Column>

        <Column header="Acciones" width="120px">
          <template #body="{ data }">
            <div class="flex">
              <div class="mr-2">
                <Button class="secondary" icon="pi pi-pencil" aria-label="Submit" @click="abrirEditar(data)" size="small"
                  style="width: 25px; height: 25px;" />
              </div>
              <Button icon="pi pi-trash" severity="danger" aria-label="Submit" @click="confirmarEliminar(data)"
                size="small" style="width: 25px; height: 25px;" />
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
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";
import Tag from 'primevue/tag';

const confirm = useConfirm();
const toast = useToast();

// Datos del formulario
const nombre = ref("");
const fecha_inicio = ref("");
const fecha_fin = ref("");
const estado = ref("activo");
const id_periodo = ref(null);

// Lista de periodos y control del modal
const periodos = ref([]);
const modal = ref(false);

// Opciones para el dropdown de estado
const estados = ref([
  { label: 'Activo', value: 'activo' },
  { label: 'Inactivo', value: 'inactivo' }
]);

// Formatear fecha para mostrar
const formatDate = (dateString) => {
  const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
  return new Date(dateString).toLocaleDateString('es-ES', options);
};

// Obtener todos los periodos
const getPeriodos = async () => {
  try {
    const response = await axios.get('get-periodos');
    periodos.value = response.data;
  } catch (error) {
    showError('Error al obtener periodos');
  }
};

// Guardar o actualizar periodo
const save = async () => {
  try {
    const action = id_periodo.value ? 'actualizado' : 'creado';
    await axios.post('guardar-periodo', {
      id_periodo: id_periodo.value,
      nombre: nombre.value,
      fecha_inicio: fecha_inicio.value,
      fecha_fin: fecha_fin.value,
      estado: estado.value
    });

    showSuccess(`Periodo ${action} correctamente`);
    resetForm();
    modal.value = false;
    getPeriodos();
  } catch (error) {
    showError(error.response?.data?.message || 'Error al guardar periodo');
  }
};

// Confirmar eliminación
const confirmarEliminar = (item) => {
  confirm.require({
    message: `¿Estás seguro de eliminar el periodo "${item.nombre}"?`,
    header: 'Confirmación',
    icon: 'pi pi-exclamation-triangle',
    acceptLabel: 'Sí, eliminar',
    rejectLabel: 'Cancelar',
    accept: () => eliminarPeriodo(item),
    reject: () => {
      toast.add({ severity: 'info', summary: 'Cancelado', detail: 'No se eliminó el periodo', life: 3000 });
    }
  });
};

// Eliminar periodo
const eliminarPeriodo = async (item) => {
  try {
    await axios.get('eliminar-periodo/' + item.id_periodo);
    showSuccess('Periodo eliminado correctamente');
    getPeriodos();
  } catch (error) {
    const errorMsg = error.response?.data?.message ||
                    (error.response?.status === 409 ?
                     'No se puede eliminar porque tiene datos relacionados' :
                     'Error al eliminar periodo');
    showError(errorMsg);
  }
};

// Abrir modal para editar
const abrirEditar = (item) => {
  modal.value = true;
  id_periodo.value = item.id_periodo;
  nombre.value = item.nombre;
  fecha_inicio.value = item.fecha_inicio.split('T')[0]; // Formato YYYY-MM-DD
  fecha_fin.value = item.fecha_fin.split('T')[0];
  estado.value = item.estado;
};

// Limpiar formulario
const resetForm = () => {
  id_periodo.value = null;
  nombre.value = "";
  fecha_inicio.value = "";
  fecha_fin.value = "";
  estado.value = "activo";
};

// Mostrar notificación de éxito
const showSuccess = (message) => {
  toast.add({ severity: 'success', summary: 'Éxito', detail: message, life: 3000 });
};

// Mostrar notificación de error
const showError = (message) => {
  toast.add({ severity: 'error', summary: 'Error', detail: message, life: 5000 });
};

// Cargar datos al montar el componente
onMounted(() => {
  getPeriodos();
});
</script>
