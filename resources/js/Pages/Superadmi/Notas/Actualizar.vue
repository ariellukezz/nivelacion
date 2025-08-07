<template>
  <Head title="Actualización de Notas" />
  <AuthenticatedLayout>
    <div class="text-center mb-4">
      <h2 class="text-2xl font-bold text-gray-800">Actualización Masiva de Notas</h2>
    </div>

    <div class="card p-fluid">
      <div class="grid">
        <!-- Selector de Escuela -->
        <div class="col-12 md:col-4">
          <div class="field">
            <label>Escuela</label>
            <Dropdown v-model="filters.id_escuela" :options="escuelas"
                     optionLabel="nombre" optionValue="id"
                     placeholder="Seleccione escuela" class="w-full"
                     :loading="loading.escuelas" />
          </div>
        </div>

        <!-- Selector de Periodo -->
        <div class="col-12 md:col-4">
          <div class="field">
            <label>Periodo</label>
            <Dropdown v-model="filters.id_periodo" :options="periodos"
                     optionLabel="nombre" optionValue="id_periodo"
                     placeholder="Seleccione periodo" class="w-full"
                     :loading="loading.periodos" />
          </div>
        </div>

        <!-- Rango de Notas -->
        <div class="col-12 md:col-2">
          <div class="field">
            <label>Nota mínima</label>
            <InputNumber v-model="filters.nota_minima" mode="decimal"
                         :min="0" :max="20" class="w-full" />
          </div>
        </div>

        <div class="col-12 md:col-2">
          <div class="field">
            <label>Nota máxima</label>
            <InputNumber v-model="filters.nota_maxima" mode="decimal"
                         :min="0" :max="20" class="w-full" />
          </div>
        </div>
      </div>

      <!-- Botones de Acción -->
      <div class="flex justify-end gap-3 mt-4">
        <Button label="Previsualizar" icon="pi pi-eye" severity="info"
                @click="previewUpdate" :loading="loading.preview" />
        <Button label="Ejecutar Actualización" icon="pi pi-check" severity="success"
                @click="confirmExecute" :disabled="!previewData.length"
                :loading="loading.execute" />
      </div>
    </div>

    <!-- Tabla de Previsualización -->
    <div class="card mt-4" v-if="previewData.length">
      <DataTable :value="previewData" :class="'p-datatable-sm'" paginator :rows="10"
                 tableStyle="min-width: 100rem" style="font-size: 0.8rem;" scrollable scrollHeight="flex">
        <Column field="codigo_est" header="Código Estudiante" frozen></Column>

        <Column v-for="competencia in 11" :key="competencia"
                :header="'C' + competencia" :field="'C' + competencia">
          <template #body="{ data }">
            <div class="flex items-center gap-2">
              <span :class="{'text-red-500': data['C' + competencia] !== data['actual_C' + competencia]}">
                {{ data['C' + competencia] ?? '-' }}
              </span>
              <i v-if="data['C' + competencia] !== data['actual_C' + competencia]"
                 class="pi pi-arrow-right text-xs text-gray-500"></i>
              <span v-if="data['C' + competencia] !== data['actual_C' + competencia]"
                    class="text-green-500">
                {{ data['actual_C' + competencia] ?? '-' }}
              </span>
            </div>
          </template>
        </Column>
      </DataTable>

      <div class="mt-4 p-3 bg-gray-100 rounded">
        <span class="font-semibold">Resumen:</span>
        Se actualizarán {{ previewData.length }} registros
        <span v-if="filters.id_escuela">de la escuela seleccionada</span>
        <span v-if="filters.id_periodo">para el periodo {{ getPeriodoNombre(filters.id_periodo) }}</span>
      </div>
    </div>

    <ConfirmDialog></ConfirmDialog>
    <Toast />
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/LayoutSuperadmi.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";

// PrimeVue Components
import Button from 'primevue/button';
import Dropdown from 'primevue/dropdown';
import InputNumber from 'primevue/inputnumber';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ConfirmDialog from 'primevue/confirmdialog';
import Toast from 'primevue/toast';

const confirm = useConfirm();
const toast = useToast();

// Data
const escuelas = ref([]);
const periodos = ref([]);
const previewData = ref([]);
const loading = ref({
  escuelas: false,
  periodos: false,
  preview: false,
  execute: false
});

// Filters
const filters = ref({
  id_escuela: null,
  id_periodo: null,
  nota_minima: 0,
  nota_maxima: 20
});

// Cargar datos iniciales
onMounted(async () => {
  try {
    loading.value.escuelas = true;
    loading.value.periodos = true;

    const [escuelasRes, periodosRes] = await Promise.all([
      axios.get('/superadmi/obtener-escuelas'),
      axios.get('/superadmi/obtener-periodos')
    ]);

    escuelas.value = escuelasRes.data;
    periodos.value = periodosRes.data;
  } catch (error) {
    showError('Error al cargar datos iniciales');
  } finally {
    loading.value.escuelas = false;
    loading.value.periodos = false;
  }
});

// Previsualizar cambios
const previewUpdate = async () => {
  loading.value.preview = true;
  try {
    const response = await axios.post('/superadmi/notas/preview', filters.value);
    previewData.value = response.data.data;
    showSuccess(`Previsualización: ${previewData.value.length} registros afectados`);
  } catch (error) {
    showError(error.response?.data?.message || 'Error en la previsualización');
  } finally {
    loading.value.preview = false;
  }
};

// Confirmar ejecución
const confirmExecute = () => {
  confirm.require({
    message: `¿Estás seguro de actualizar ${previewData.value.length} registros? Esta acción no se puede deshacer.`,
    header: 'Confirmar Actualización',
    icon: 'pi pi-exclamation-triangle',
    acceptLabel: 'Sí, actualizar',
    rejectLabel: 'Cancelar',
    accept: () => executeUpdate(),
    reject: () => {
      toast.add({ severity: 'info', summary: 'Cancelado', detail: 'No se realizaron cambios', life: 3000 });
    }
  });
};

// Ejecutar actualización
const executeUpdate = async () => {
  loading.value.execute = true;
  try {
    const response = await axios.post('/superadmi/notas/execute', filters.value);
    showSuccess(`Actualización completada: ${response.data.affected_rows} registros modificados`);
    previewData.value = [];
  } catch (error) {
    showError(error.response?.data?.message || 'Error en la actualización');
  } finally {
    loading.value.execute = false;
  }
};

// Helper para obtener nombre de periodo
const getPeriodoNombre = (id) => {
  const periodo = periodos.value.find(p => p.id_periodo === id);
  return periodo ? periodo.nombre : '';
};

// Mostrar notificación de éxito
const showSuccess = (message) => {
  toast.add({ severity: 'success', summary: 'Éxito', detail: message, life: 5000 });
};

// Mostrar notificación de error
const showError = (message) => {
  toast.add({ severity: 'error', summary: 'Error', detail: message, life: 8000 });
};
</script>

<style scoped>
.p-datatable ::v-deep(.p-datatable-thead > tr > th) {
  background-color: #f8fafc;
  font-weight: 600;
  text-align: center;
}

.text-red-500 {
  color: #ef4444;
  font-weight: 600;
}

.text-green-500 {
  color: #10b981;
  font-weight: 600;
}
</style>
