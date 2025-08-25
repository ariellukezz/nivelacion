<template>
  <Head title="Sorteo de Participantes" />
  <AuthenticatedLayout>
    <div class="text-center mb-4">
      <h2 class="text-2xl font-bold text-gray-800">Sorteo de Participantes del Evento</h2>
    </div>

    <div class="w-full max-w-full mx-auto flex flex-col items-center gap-2">
      <!-- Filtros -->
      <div class="w-full flex flex-wrap gap-4 justify-center">
        <div class="w-full sm:w-1/2 lg:w-1/4">
          <label for="programa">Programa</label>
          <Dropdown
            v-model="filters.programa_id"
            :options="programas"
            optionLabel="programa"
            optionValue="id"
            placeholder="Selecciona un programa"
            class="w-full"
            filter
          />
        </div>

        <div class="w-full sm:w-1/2 lg:w-1/4">
          <label for="escuela">Escuela</label>
          <Dropdown
            v-model="filters.escuela"
            :options="escuelas"
            optionLabel="escuela"
            optionValue="escuela"
            placeholder="Selecciona una escuela"
            class="w-full"
            filter
          />
        </div>

        <div class="w-full sm:w-1/2 lg:w-1/4">
          <label for="facultad">Facultad</label>
          <Dropdown
            v-model="filters.facultad"
            :options="facultades"
            optionLabel="facultad"
            optionValue="facultad"
            placeholder="Selecciona una facultad"
            class="w-full"
            filter
          />
        </div>

        <div class="w-full sm:w-1/3 lg:w-1/5">
          <label for="area">Área</label>
          <Dropdown
            v-model="filters.area"
            :options="areas"
            optionLabel="area"
            optionValue="area"
            placeholder="Selecciona un área"
            class="w-full"
          />
        </div>

        <!-- Número de ganadores -->
        <div v-if="participantes.length > 0" class="w-full sm:w-1/4 lg:w-1/6">
          <label for="numero_ganadores">Número de ganadores</label>
          <InputNumber
            v-model="numeroGanadores"
            min="1"
            :max="participantes.length"
            placeholder="Número de ganadores"
            class="w-full"
          />
        </div>

        <!-- Botones -->
        <Button
          label="Cargar Participantes"
          severity="info"
          @click="cargarParticipantes"
          class="w-full sm:w-1/4 lg:w-1/6"
        />
        <Button
          v-if="numeroGanadores > 0"
          label="Ejecutar Sorteo"
          severity="success"
          @click="ejecutarSorteo"
          class="w-full sm:w-1/4 lg:w-1/6"
        />
        <Button
          label="Limpiar Todo"
          severity="danger"
          @click="limpiarTodo"
          class="w-full sm:w-auto"
          icon="pi pi-times"
        />
      </div>

      <!-- Lista de participantes -->
      <div v-if="participantes.length > 0 && !ganadores.length" class="w-full mt-4">
        <h3 class="text-xl font-semibold">Participantes</h3>
        <ul class="list-disc pl-6">
          <li v-for="(participante, index) in participantes" :key="index">
            {{ participante.nombres }} {{ participante.paterno }} ({{ participante.programa }})
          </li>
        </ul>
      </div>

      <!-- Ganadores -->
      <div v-if="ganadores.length > 0" class="w-full mt-6">
        <h3 class="text-4xl font-bold text-green-700 text-center">¡Ganadores!</h3>
        <ul class="list-none mt-4 w-full px-4">
        <li
            v-for="ganador in ganadores"
            :key="ganador.codigo_est"
            class="my-3 text-left flex items-baseline gap-x-4 whitespace-nowrap overflow-hidden"
        >
            <span class="text-3xl font-bold text-gray-800 truncate">
            {{ ganador.orden_sorteo }}. {{ ganador.nombres }} {{ ganador.paterno }} {{ ganador.materno }}
            </span>
            <span class="text-sm text-gray-600">
            (Programa: {{ ganador.programa }})
            </span>
        </li>
        </ul>
        <!-- Botones de acción -->
        <div class="w-full flex flex-col sm:flex-row justify-center gap-4 mt-6">
          <Button
            label="Guardar Ganadores"
            severity="primary"
            @click="guardarGanadores"
            class="px-6 py-3 text-lg"
          />

          <Button
            label="Exportar PDF"
            severity="warning"
            icon="pi pi-file-pdf"
            @click="exportarPDF"
            class="px-6 py-3 text-lg"
          />
        </div>
      </div>
    </div>

    <!-- Toast de notificaciones -->
    <Toast />
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Dropdown from 'primevue/dropdown';
import InputNumber from 'primevue/inputnumber';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/LayoutSupervisor.vue';
import axios from 'axios';

const toast = useToast();

const filters = ref({
  programa_id: null,
  escuela: null,
  facultad: null,
  area: null,
});

const programas = ref([]);
const escuelas = ref([]);
const facultades = ref([]);
const areas = ref([]);

const participantes = ref([]);
const numeroGanadores = ref(1);
const ganadores = ref([]);

const eventoId = 1; // Puedes hacerlo dinámico si deseas

// Cargar filtros disponibles
const cargarFiltros = async () => {
  try {
    const response = await axios.get('filtros');
    programas.value = response.data.programas;
    escuelas.value = response.data.escuelas;
    facultades.value = response.data.facultades;
    areas.value = response.data.areas;
  } catch (error) {
    showError("Error al cargar los filtros");
  }
};

// Cargar participantes filtrados
const cargarParticipantes = async () => {
  try {
    const response = await axios.get(`participantes-evento/${eventoId}`, {
      params: filters.value,
    });
    participantes.value = response.data;
    ganadores.value = []; // Limpiar ganadores al recargar
  } catch (error) {
    showError("Error al cargar participantes");
  }
};

// Ejecutar sorteo (sin guardar aún)
const ejecutarSorteo = async () => {
  if (numeroGanadores.value <= 0 || numeroGanadores.value > participantes.value.length) {
    showError("Número de ganadores inválido.");
    return;
  }

  try {
    const response = await axios.post('ejecutar-sorteo', {
      numero_ganadores: numeroGanadores.value,
      evento_id: eventoId,
      ...filters.value, // Enviar filtros al backend
    });

    ganadores.value = response.data;
    showSuccess("Sorteo ejecutado. ¡Ahora puedes guardar los ganadores!");
  } catch (error) {
    showError("Error al ejecutar el sorteo");
  }
};

// Guardar ganadores
const guardarGanadores = async () => {
  if (!ganadores.value.length) {
    showError("No hay ganadores para guardar.");
    return;
  }

  try {
    await axios.post('guardar-ganadores', {
      evento_id: eventoId,
      ganadores: ganadores.value.map(g => ({
        codigo_est: g.codigo_est,
        orden_sorteo: g.orden_sorteo,
      })),
    });

    showSuccess("Ganadores guardados exitosamente.");
  } catch (error) {
    showError("Error al guardar ganadores.");
  }
};

// Exportar PDF
const exportarPDF = () => {
  const url = `/supervisor/exportar-ganadores-pdf/${eventoId}`;
  window.open(url, '_blank');
};

// Limpiar todo
const limpiarTodo = () => {
  filters.value = {
    programa_id: null,
    escuela: null,
    facultad: null,
    area: null,
  };
  participantes.value = [];
  numeroGanadores.value = 1;
  ganadores.value = [];
  showSuccess("Filtros y datos limpiados.");
};

// Notificaciones
const showSuccess = (message) => {
  toast.add({ severity: 'success', summary: 'Éxito', detail: message, life: 3000 });
};

const showError = (message) => {
  toast.add({ severity: 'error', summary: 'Error', detail: message, life: 5000 });
};

// Inicializar
onMounted(() => {
  cargarFiltros();
});
</script>
