<template>
  <Head title="Registro de Ingresos" />
  <AuthenticatedLayout>
    <div class="min-h-screen max-w-7xl mx-auto bg-gray-50 p-4 md:p-6">
      <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md p-6 md:p-8">
        <div class="text-center mb-6">
          <h2 class="text-3xl font-bold text-gray-800">Registro de Ingreso al Evento</h2>
          <p class="text-gray-600 mt-2">Sistema de control de acceso para eventos</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Sección de entrada de datos -->
          <div class="space-y-6">
            <!-- Campo para ingresar el DNI -->
            <div class="w-full">
              <label for="dni" class="block text-sm font-medium text-gray-700 mb-1">Escanea el DNI o digítalo</label>
              <InputText
                id="dni"
                type="text"
                v-model="dni"
                class="w-full"
                placeholder="Ingrese DNI del estudiante"
                autofocus
              />
            </div>

            <!-- Dropdown para seleccionar el evento -->
            <div class="w-full">
              <label for="evento" class="block text-sm font-medium text-gray-700 mb-1">Evento</label>
              <Dropdown
                v-model="evento_id"
                :options="eventos"
                optionLabel="nombre_evento"
                optionValue="id"
                class="w-full"
                placeholder="Seleccione un evento"
              />
            </div>

            <!-- Botones -->
            <div class="flex w-full gap-3">
              <Button
                label="Limpiar"
                severity="secondary"
                @click="limpiarDNI"
                class="flex-1"
              />
              <!-- NUEVO: botón para abrir resumen -->
              <Button
                label="Ver resumen"
                icon="pi pi-chart-bar"
                @click="abrirResumen"
                class="flex-1"
              />
            </div>
          </div>

          <!-- Sección de resultados -->
          <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-2xl font-semibold text-gray-800 mb-4">Resultado del registro</h3>

            <div v-if="!resultado" class="text-center py-8 text-gray-500">
              <i class="pi pi-id-card text-4xl opacity-50 mb-3"></i>
              <p>Ingrese un DNI y seleccione un evento para comenzar</p>
            </div>

            <div
              v-else
              class="w-full bg-white border p-4 rounded-lg shadow"
              :class="{
                'bg-green-50 border-green-200': resultado.estudiante.firma_ingreso === 1,
                'bg-blue-50 border-blue-200': resultado.estudiante.firma_ingreso !== 1
              }"
            >
              <div class="flex items-center mb-3">
                <i
                  class="pi pi-check-circle text-green-500 text-xl mr-2"
                  v-if="resultado.estudiante.firma_ingreso === 1"
                ></i>
                <i
                  class="pi pi-info-circle text-blue-500 text-xl mr-2"
                  v-else
                ></i>
                <h3
                  class="text-lg font-semibold"
                  :class="{
                    'text-green-700': resultado.estudiante.firma_ingreso === 1,
                    'text-blue-700': resultado.estudiante.firma_ingreso !== 1
                  }"
                >
                  {{
                    resultado.estudiante.firma_ingreso === 1
                      ? 'Ingreso previamente registrado'
                      : 'Ingreso registrado correctamente'
                  }}
                </h3>
              </div>

              <div class="grid grid-cols-1 gap-2 text-xl">
                <p><strong>Estudiante:</strong> {{ resultado.estudiante.nombres }} {{ resultado.estudiante.paterno }} {{ resultado.estudiante.materno }}</p>
                <p><strong>Programa:</strong> {{ resultado.estudiante.programa }}</p>
                <p><strong>Mesa:</strong> {{ resultado.asignacion.mesa }}</p>
                <p><strong>Número:</strong> {{ resultado.asignacion.numero }}</p>
                <p><strong>Serie:</strong> {{ resultado.asignacion.serie }}</p>
              </div>

              <div
                class="mt-4 pt-3 border-t border-gray-200"
                v-if="resultado.estudiante.firma_ingreso === 1"
              >
                <p class="text-sm text-amber-600 flex items-center">
                  <i class="pi pi-exclamation-triangle mr-2"></i> Este estudiante ya había ingresado anteriormente.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- NUEVO: Modal de Resumen -->
    <Dialog v-model:visible="mostrarResumen" modal header="Resumen de ingresados por programa"
      :style="{ width: '42rem', maxWidth: '95vw' }">
      <div v-if="cargandoResumen" class="py-6 text-center text-gray-500">
        <i class="pi pi-spin pi-spinner text-2xl"></i>
        <p class="mt-2">Cargando resumen...</p>
      </div>

      <div v-else>
        <div class="mb-4">
          <p class="text-sm text-gray-600">Evento ID: {{ evento_id }}</p>
          <p class="text-lg font-semibold">Total general: {{ resumen.total_general }}</p>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full bg-white rounded-lg shadow">
            <thead>
              <tr class="text-left border-b">
                <th class="p-3">Programa</th>
                <th class="p-3">Ingresaron</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="row in resumen.por_programa" :key="row.programa_id" class="border-b">
                <td class="p-3">{{ row.programa }}</td>
                <td class="p-3 font-semibold">{{ row.total_ingresados }}</td>
              </tr>
              <tr v-if="!resumen.por_programa || resumen.por_programa.length === 0">
                <td class="p-3 text-gray-500" colspan="2">Sin datos para mostrar.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="mt-4 text-right">
          <Button label="Cerrar" severity="secondary" @click="mostrarResumen = false" />
        </div>
      </div>
    </Dialog>

    <!-- Mensaje de Toast para notificaciones -->
    <Toast />
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Dropdown from 'primevue/dropdown';
import Dialog from 'primevue/dialog'; // NUEVO: modal
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/LayoutSupervisor.vue';
import axios from 'axios';

const toast = useToast();

const dni = ref("");
const evento_id = ref(null);
const eventos = ref([]);
const resultado = ref(null);

// NUEVO: estado y datos del resumen
const mostrarResumen = ref(false);
const cargandoResumen = ref(false);
const resumen = ref({ total_general: 0, por_programa: [] });

// Observa el DNI: si son 8 dígitos, registra automáticamente
watch(dni, (newVal) => {
  if (newVal.length === 8 && /^\d{8}$/.test(newVal)) {
    registrarIngreso();
  }
});

// Cargar los eventos desde la API
const getEventos = async () => {
  try {
    const response = await axios.get('get-eventos');
    eventos.value = response.data;
  } catch (error) {
    showError("Error al cargar eventos");
  }
};

// Registrar el ingreso del estudiante
const registrarIngreso = async () => {
  if (!dni.value || !evento_id.value) {
    showError("Debe ingresar el DNI y seleccionar un evento");
    return;
  }

  try {
    const response = await axios.post('registrar-ingreso', {
      dni: dni.value,
      evento_id: evento_id.value
    });

    if (response.data.success) {
      resultado.value = {
        estudiante: response.data.estudiante,
        asignacion: response.data.asignacion
      };
      showSuccess(response.data.message);
    } else {
      showError(response.data.message);
    }
  } catch (error) {
    const msg = error.response?.data?.message || 'Error al registrar ingreso';
    showError(msg);
  } finally {
    // Limpiar solo el campo DNI
    dni.value = "";
  }
};

// NUEVO: abrir modal y cargar resumen
const abrirResumen = async () => {
  if (!evento_id.value) {
    showError("Seleccione un evento para ver el resumen");
    return;
  }
  mostrarResumen.value = true;
  await cargarResumen();
};

// NUEVO: llamada al endpoint de resumen
const cargarResumen = async () => {
  cargandoResumen.value = true;
  try {
    const { data } = await axios.get(`participantes-evento/${evento_id.value}/resumen`);
    resumen.value = data;
  } catch (e) {
    showError("No se pudo cargar el resumen");
  } finally {
    cargandoResumen.value = false;
  }
};

// Limpiar solo el campo DNI
const limpiarDNI = () => {
  dni.value = "";
};

// Función para mostrar el mensaje de éxito
const showSuccess = (message) => {
  toast.add({ severity: 'success', summary: 'Correcto', detail: message, life: 3000 });
};

// Función para mostrar el mensaje de error
const showError = (message) => {
  toast.add({ severity: 'error', summary: 'Error', detail: message, life: 5000 });
};

// Cargar eventos al montar el componente
onMounted(() => {
  getEventos();
});
</script>
