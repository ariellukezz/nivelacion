<template>
  <Head title="Lista de Ganadores" />
  <AuthenticatedLayout>
    <div class="text-center mb-6">
      <h2 class="text-2xl font-bold text-gray-800">Exportar Lista de Ganadores</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div>
        <label>Programa</label>
        <Dropdown v-model="filtros.programa_id" :options="programas" optionLabel="programa" optionValue="id" placeholder="Todos" class="w-full" />
      </div>
      <div>
        <label>Facultad</label>
        <Dropdown v-model="filtros.facultad" :options="facultades" optionLabel="facultad" optionValue="facultad" placeholder="Todas" class="w-full" />
      </div>
      <div>
        <label>Escuela</label>
        <Dropdown v-model="filtros.escuela" :options="escuelas" optionLabel="escuela" optionValue="escuela" placeholder="Todas" class="w-full" />
      </div>
      <div>
        <label>Área</label>
        <Dropdown v-model="filtros.area" :options="areas" optionLabel="area" optionValue="area" placeholder="Todas" class="w-full" />
      </div>
    </div>

    <div class="mt-4">
      <label>ID del Evento</label>
      <InputText v-model="eventoId" class="w-full" placeholder="Ej: 1" />
    </div>

    <div class="mt-6 flex justify-end">
      <Button label="Descargar PDF" severity="info" @click="descargarPDF" />
    </div>

    <Toast />
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/LayoutSupervisor.vue';
import { Head } from '@inertiajs/vue3';
import Dropdown from 'primevue/dropdown';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const toast = useToast();

const filtros = ref({
  programa_id: '',
  facultad: '',
  escuela: '',
  area: '',
});
const eventoId = ref('');

const programas = ref([]);
const facultades = ref([]);
const escuelas = ref([]);
const areas = ref([]);

onMounted(async () => {
  const res = await axios.get('filtros');
  programas.value = res.data.programas;
  facultades.value = res.data.facultades;
  escuelas.value = res.data.escuelas;
  areas.value = res.data.areas;
});

const descargarPDF = () => {
  if (!eventoId.value) {
    toast.add({ severity: 'warn', summary: 'Falta Evento', detail: 'Debes ingresar el ID del evento', life: 3000 });
    return;
  }

  const query = new URLSearchParams(filtros.value).toString();
  const url = `/supervisor/exportar-ganadores-filtrado/${eventoId.value}?${query}`;
  window.open(url, '_blank');
};
</script>
