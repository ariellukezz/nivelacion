<template>
  <Head title="Lista de Ganadores" />

  <AuthenticatedLayout>
    <div class="text-center mb-6">
      <h2 class="text-2xl font-bold text-gray-800">
        Exportar Lista de Ganadores
      </h2>

      <p v-if="eventoActivo" class="mt-2 text-gray-600">
        Evento activo:
        <strong>{{ eventoActivo.nombre_evento }}</strong>
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div>
        <label>Programa</label>
        <Dropdown
          v-model="filtros.programa_id"
          :options="programas"
          optionLabel="programa"
          optionValue="id"
          placeholder="Todos"
          class="w-full"
          filter
          showClear
        />
      </div>

      <div>
        <label>Facultad</label>
        <Dropdown
          v-model="filtros.facultad"
          :options="facultades"
          optionLabel="facultad"
          optionValue="facultad"
          placeholder="Todas"
          class="w-full"
          filter
          showClear
        />
      </div>

      <div>
        <label>Escuela</label>
        <Dropdown
          v-model="filtros.escuela"
          :options="escuelas"
          optionLabel="escuela"
          optionValue="escuela"
          placeholder="Todas"
          class="w-full"
          filter
          showClear
        />
      </div>

      <div>
        <label>Área</label>
        <Dropdown
          v-model="filtros.area"
          :options="areas"
          optionLabel="area"
          optionValue="area"
          placeholder="Todas"
          class="w-full"
          showClear
        />
      </div>
    </div>

    <div class="mt-6 flex justify-end gap-3">
      <Button
        label="Limpiar Filtros"
        severity="secondary"
        icon="pi pi-times"
        @click="limpiarFiltros"
      />

      <Button
        label="Descargar PDF"
        severity="info"
        icon="pi pi-file-pdf"
        :disabled="!eventoId"
        @click="descargarPDF"
      />
    </div>

    <Toast />
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/LayoutSupervisor.vue';
import { Head } from '@inertiajs/vue3';
import Dropdown from 'primevue/dropdown';
import Button from 'primevue/button';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const toast = useToast();

const filtros = ref({
  programa_id: null,
  facultad: null,
  escuela: null,
  area: null,
});

const eventoId = ref(null);
const eventoActivo = ref(null);

const programas = ref([]);
const facultades = ref([]);
const escuelas = ref([]);
const areas = ref([]);

const cargarEventoActivo = async () => {
  try {
    const response = await axios.get('/supervisor/get-eventos');

    if (response.data.length > 0) {
      eventoActivo.value = response.data[0];
      eventoId.value = response.data[0].id;
    } else {
      eventoActivo.value = null;
      eventoId.value = null;

      showError('No existe un evento activo.');
    }
  } catch (error) {
    showError(
      error.response?.data?.message ||
      'Error al obtener el evento activo.'
    );
  }
};

const cargarFiltros = async () => {
  try {
    const response = await axios.get('/supervisor/filtros');

    programas.value = response.data.programas;
    facultades.value = response.data.facultades;
    escuelas.value = response.data.escuelas;
    areas.value = response.data.areas;
  } catch (error) {
    showError('Error al cargar los filtros.');
  }
};

const descargarPDF = () => {
  if (!eventoId.value) {
    showError('No existe un evento activo.');
    return;
  }

  const params = new URLSearchParams();

  if (filtros.value.programa_id) {
    params.append('programa_id', filtros.value.programa_id);
  }

  if (filtros.value.facultad) {
    params.append('facultad', filtros.value.facultad);
  }

  if (filtros.value.escuela) {
    params.append('escuela', filtros.value.escuela);
  }

  if (filtros.value.area) {
    params.append('area', filtros.value.area);
  }

  let url =
    `/supervisor/exportar-ganadores-filtrado/${eventoId.value}`;

  const query = params.toString();

  if (query) {
    url += `?${query}`;
  }

  window.open(url, '_blank');
};

const limpiarFiltros = () => {
  filtros.value = {
    programa_id: null,
    facultad: null,
    escuela: null,
    area: null,
  };
};

const showError = (message) => {
  toast.add({
    severity: 'error',
    summary: 'Error',
    detail: message,
    life: 5000,
  });
};

onMounted(async () => {
  await Promise.all([
    cargarEventoActivo(),
    cargarFiltros(),
  ]);
});
</script>
