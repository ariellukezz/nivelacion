<template>
  <AuthenticatedLayout>
    <Head title="Panel de Control de Módulos" />
    <Toast />
    <ConfirmDialog></ConfirmDialog>

    <div class="p-4">
      <Card>
        <template #title>
          <h1 class="text-xl font-bold">Panel de Control de Módulos</h1>
        </template>

        <template #content>
          <Toolbar class="mb-4">
            <template #start>
              <div class="flex items-center gap-2 flex-wrap">
                <InputText v-model="filtros.q" placeholder="Buscar por Módulo o Nombre..." @input="debounceBusqueda" class="w-full sm:w-auto" />
                <Dropdown v-model="filtros.modulo" :options="opcionesModulo" optionLabel="label" optionValue="value" placeholder="Filtrar Módulo" showClear @change="cargarReglas(1)" class="w-full sm:w-auto" />
                <Button icon="pi pi-times" severity="secondary" @click="limpiarFiltros" rounded />
              </div>
            </template>
            <template #end>
              <div v-if="seleccionados.length > 0" class="flex items-center gap-2">
                <span class="text-sm font-semibold">{{ seleccionados.length }} seleccionado(s)</span>
                <Button label="Activar" icon="pi pi-check" size="small" severity="success" @click="actualizarSeleccionados(true)" />
                <Button label="Desactivar" icon="pi pi-times" size="small" severity="danger" @click="actualizarSeleccionados(false)" />
              </div>
            </template>
          </Toolbar>

          <DataTable :value="reglas.data" v-model:selection="seleccionados" dataKey="id"
            :loading="loading" stripedRows responsiveLayout="scroll"
            paginator :rows="reglas.per_page"
            :totalRecords="reglas.total"
            :first="first"
            @page="onPage"
            :lazy="true"
            :rowClass="claseFila">

            <Column selectionMode="multiple" headerStyle="width: 3em"></Column>
            <Column field="modulo" header="Módulo" :sortable="true" style="min-width: 10rem;"></Column>
            <Column field="nombre_usuario" header="Usuario" :sortable="true" style="min-width: 12rem;">
              <template #body="{ data }">
                {{ data.nombre_usuario || 'N/A (Global o Escuela)' }}
              </template>
            </Column>
            <!-- <Column field="id_escuela" header="ID Escuela" :sortable="true" style="width: 8rem;"></Column> -->
            <Column header="Vigencia" style="min-width: 12rem;">
              <template #body="{ data }">
                <div class="text-xs"><strong>Inicio:</strong> {{ formatearFecha(data.fecha_inicio) }}</div>
                <div class="text-xs"><strong>Fin:</strong> {{ formatearFecha(data.fecha_fin) }}</div>
              </template>
            </Column>
            <Column header="Tiempo Restante" style="min-width: 10rem;">
              <template #body="{ data }">
                <Tag :severity="claseSeveridadTiempo(data)" :value="tiempoRestanteTexto(data)"></Tag>
              </template>
            </Column>
            <Column header="Estado" style="width: 6rem;">
              <template #body="{ data }">
                <InputSwitch v-model="data.estado" @change="cambiarEstado(data)" />
              </template>
            </Column>
            <Column header="Acciones" headerStyle="width: 5em; text-align: center">
              <template #body="{ data }">
                <Button icon="pi pi-pencil" class="p-button-rounded p-button-info" @click="abrirDialogoEdicion(data)" />
              </template>
            </Column>

            <template #empty>
              <div class="text-center p-4">No se encontraron reglas con los filtros actuales.</div>
            </template>
          </DataTable>
        </template>
      </Card>
    </div>

    <Dialog v-model:visible="dialogoVisible" modal header="Editar Regla" :style="{ width: '30rem' }">
        <div v-if="reglaEditada" class="flex flex-col gap-4 p-4">
            <div>Módulo: <strong>{{ reglaEditada.modulo }}</strong></div>
            <div v-if="reglaEditada.nombre_usuario">Usuario: <strong>{{ reglaEditada.nombre_usuario }}</strong></div>
            <div v-if="reglaEditada.id_escuela">Escuela ID: <strong>{{ reglaEditada.id_escuela }}</strong></div>
            <Divider />
            <div>
                <label class="block text-sm font-medium mb-1">Fecha de Inicio</label>
                <Calendar v-model="reglaEditada.fecha_inicio" showTime hourFormat="24" class="w-full" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Fecha de Fin</label>
                <Calendar v-model="reglaEditada.fecha_fin" showTime hourFormat="24" class="w-full" />
            </div>
            <div class="flex items-center gap-2 mt-2">
                <InputSwitch v-model="reglaEditada.estado" />
                <label class="font-medium">Estado {{ reglaEditada.estado ? 'Activo' : 'Inactivo' }}</label>
            </div>
        </div>
        <template #footer>
            <Button label="Cancelar" severity="secondary" @click="dialogoVisible = false"></Button>
            <Button label="Guardar Cambios" @click="guardarEdicion" :loading="loading" />
        </template>
    </Dialog>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/LayoutSupervisor.vue';
import { Head } from '@inertiajs/vue3';
import { useToast } from "primevue/usetoast";
import { useConfirm } from "primevue/useconfirm";

// --- Importaciones de Componentes de PrimeVue ---
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import InputSwitch from 'primevue/inputswitch';
import Dialog from 'primevue/dialog';
import Calendar from 'primevue/calendar';
import Toast from 'primevue/toast';
import ConfirmDialog from 'primevue/confirmdialog';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Toolbar from 'primevue/toolbar';
import Card from 'primevue/card';
import Tag from 'primevue/tag';
import Divider from 'primevue/divider';

// --- Inicialización de Servicios ---
const toast = useToast();
const confirm = useConfirm();
let debounceTimer = null;

// --- Estado Reactivo del Componente ---
const reglas = ref({
  data: [],
  total: 0,
  per_page: 10,
  current_page: 1,
  last_page: 1
});
const filtros = ref({ q: '', modulo: null, id_escuela: null });
const loading = ref(false);
const seleccionados = ref([]);
const dialogoVisible = ref(false);
const reglaEditada = ref(null);
const opcionesModulo = ref([
    { label: 'Resolucion', value: 'Resolucion' }, { label: 'Plan', value: 'Plan' },
    { label: 'Informe', value: 'Informe' }, { label: 'Dictantes', value: 'Dictantes' },
    { label: 'Otros', value: 'Otros' },
]);

// Computed para calcular el first value correctamente
const first = computed(() => {
  return (reglas.value.current_page - 1) * reglas.value.per_page;
});

// --- Lógica Principal: Carga de Datos ---
const cargarReglas = async (page = 1) => {
    loading.value = true;
    try {
        const params = {
          ...filtros.value,
          page: page,
          per_page: reglas.value.per_page
        };

        console.log('Enviando parámetros:', params);
        const res = await axios.post('get-controles', params);
        console.log('Respuesta recibida:', res.data);

        // Procesar la respuesta del backend
        if (res.data && res.data.datos) {
          // Si es una respuesta paginada de Laravel
          if (res.data.datos.data) {
            reglas.value = {
              data: res.data.datos.data,
              total: res.data.datos.total,
              per_page: res.data.datos.per_page,
              current_page: res.data.datos.current_page,
              last_page: res.data.datos.last_page
            };
          } else {
            // Si es un array directo
            reglas.value = {
              data: res.data.datos,
              total: res.data.datos.length,
              per_page: params.per_page,
              current_page: page,
              last_page: Math.ceil(res.data.datos.length / params.per_page)
            };
          }
        } else if (res.data && Array.isArray(res.data.datos)) {
          // Si viene directamente el array en datos
          reglas.value = {
            data: res.data.datos,
            total: res.data.datos.length,
            per_page: params.per_page,
            current_page: page,
            last_page: Math.ceil(res.data.datos.length / params.per_page)
          };
        } else {
          // Estructura por defecto si no coincide
          reglas.value = {
            data: res.data.data || res.data || [],
            total: res.data.total || (Array.isArray(res.data) ? res.data.length : 0),
            per_page: res.data.per_page || params.per_page,
            current_page: res.data.current_page || page,
            last_page: res.data.last_page || 1
          };
        }

        console.log('Reglas procesadas:', reglas.value);

    } catch (error) {
        console.error('Error cargando reglas:', error);
        toast.add({
          severity: 'error',
          summary: 'Error de Carga',
          detail: 'No se pudieron cargar las reglas.',
          life: 3000
        });
    } finally {
        loading.value = false;
    }
};

const limpiarFiltros = () => {
    filtros.value = { q: '', modulo: null, id_escuela: null };
    cargarReglas(1);
};

// Espera 500ms después de que el usuario deja de escribir para buscar
const debounceBusqueda = () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        cargarReglas(1);
    }, 500);
};

// --- Lógica de Actualización (Centralizada) ---
const enviarActualizacion = async (payload, successMsg) => {
    loading.value = true;
    try {
        await axios.post('update-controles', payload);
        toast.add({
          severity: 'success',
          summary: 'Éxito',
          detail: successMsg,
          life: 3000
        });
        cargarReglas(reglas.value.current_page);
        return true;
    } catch (error) {
        console.error('Error en actualización:', error);
        toast.add({
          severity: 'error',
          summary: 'Error',
          detail: 'La operación falló.',
          life: 3000
        });
        return false;
    } finally {
        loading.value = false;
    }
};

const cambiarEstado = async (regla) => {
    const success = await enviarActualizacion(
      { ids: [regla.id], estado: regla.estado },
      'Estado actualizado.'
    );
    if (!success) {
        regla.estado = !regla.estado;
    }
};

const guardarEdicion = async () => {
    if (!reglaEditada.value) return;
    const payload = {
        ids: [reglaEditada.value.id],
        estado: reglaEditada.value.estado,
        fecha_inicio: reglaEditada.value.fecha_inicio.toISOString().slice(0, 19).replace('T', ' '),
        fecha_fin: reglaEditada.value.fecha_fin.toISOString().slice(0, 19).replace('T', ' ')
    };
    const success = await enviarActualizacion(payload, 'Regla guardada con éxito.');
    if (success) {
        dialogoVisible.value = false;
    }
};

const actualizarSeleccionados = (nuevoEstado) => {
    if (seleccionados.value.length === 0) return;
    const idsParaActualizar = seleccionados.value.map(s => s.id);
    confirm.require({
        message: `¿Estás seguro de ${nuevoEstado ? 'activar' : 'desactivar'} las ${idsParaActualizar.length} reglas seleccionadas?`,
        header: 'Confirmación de Acción Masiva',
        icon: 'pi pi-exclamation-triangle',
        acceptLabel: 'Sí, continuar',
        rejectLabel: 'Cancelar',
        accept: async () => {
            const success = await enviarActualizacion(
              { ids: idsParaActualizar, estado: nuevoEstado },
              'Las reglas seleccionadas han sido actualizadas.'
            );
            if (success) {
                seleccionados.value = [];
            }
        }
    });
};

// --- Funciones de Ayuda y Utilidad ---
const onPage = (event) => {
    // PrimeVue event.page es el índice de página (base 0)
    // Convertimos a base 1 para Laravel
    const nuevaPagina = event.page + 1;
    console.log('Cambiando a página:', nuevaPagina);
    cargarReglas(nuevaPagina);
};

const abrirDialogoEdicion = (regla) => {
    reglaEditada.value = {
        ...regla,
        fecha_inicio: new Date(regla.fecha_inicio),
        fecha_fin: new Date(regla.fecha_fin)
    };
    dialogoVisible.value = true;
};

const formatearFecha = (fecha) => {
    if (!fecha) return 'N/A';
    return new Date(fecha).toLocaleString('es-PE', {
        dateStyle: 'short',
        timeStyle: 'short'
    });
};

// --- Funciones para Estilos Dinámicos ---
const claseFila = (regla) => {
    const estaActiva = regla.estado && (new Date(regla.fecha_fin) > new Date());
    return estaActiva ? 'fila-activa' : 'fila-inactiva';
};

const tiempoRestanteTexto = (regla) => {
    if (!regla.estado) return 'Desactivado';
    const ahora = new Date();
    const fechaFin = new Date(regla.fecha_fin);
    const diff = fechaFin - ahora;
    if (diff <= 0) return 'Expirado';

    const dias = Math.floor(diff / (1000 * 60 * 60 * 24));
    const horas = Math.floor((diff / (1000 * 60 * 60)) % 24);
    const minutos = Math.floor((diff / 1000 / 60) % 60);

    if (dias > 0) return `~ ${dias} d, ${horas} h`;
    if (horas > 0) return `${horas} h, ${minutos} min`;
    if (minutos > 0) return `${minutos} min`;
    return 'Menos de un minuto';
};

const claseSeveridadTiempo = (regla) => {
    if (!regla.estado || new Date(regla.fecha_fin) < new Date()) {
        return 'danger';
    }

    const ahora = new Date();
    const fechaFin = new Date(regla.fecha_fin);
    const diffHoras = (fechaFin - ahora) / (1000 * 60 * 60);

    if (diffHoras < 24) {
        return 'warn';
    }

    return 'success';
};

// --- Carga Inicial de Datos ---
onMounted(() => {
    cargarReglas();
});
</script>

<style scoped>
.fila-activa {
    background-color: rgba(0, 255, 0, 0.05);
}

.fila-inactiva {
    background-color: rgba(255, 0, 0, 0.05);
}
</style>
