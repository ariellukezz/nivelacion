<template>
    <AuthenticatedLayout>
        <Head title="Competencias Sin Docente" />

        <div class="p-4 bg-white rounded-lg shadow">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Competencias Sin Docente Asignado</h2>
                    <p class="text-gray-600">Lista de competencias que requieren asignación de docente</p>
                </div>
                <Button
                    label="Volver al Dashboard"
                    icon="pi pi-arrow-left"
                    @click="volverAlDashboard"
                    class="p-button-secondary"
                />
            </div>

            <!-- Filtros -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-gray-50 rounded-lg">
                <Dropdown
                    v-model="selectedPeriodo"
                    :options="periodos"
                    optionLabel="nombre"
                    optionValue="id_periodo"
                    placeholder="Seleccionar Periodo"
                    class="w-full"
                    @change="cargarCompetencias"
                    showClear
                    filter
                />

                <Dropdown
                    v-model="selectedPrograma"
                    :options="programas"
                    optionLabel="programa"
                    optionValue="id"
                    placeholder="Seleccionar Programa"
                    class="w-full"
                    @change="cargarCompetencias"
                    showClear
                    filter
                >
                    <template #value="slotProps">
                        <div v-if="slotProps.value">
                            <span>{{ getProgramaLabel(slotProps.value) }}</span>
                        </div>
                        <span v-else>
                            {{ slotProps.placeholder }}
                        </span>
                    </template>
                    <template #option="slotProps">
                        <div class="flex justify-between items-center">
                            <span>{{ slotProps.option.programa }}</span>
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                {{ slotProps.option.filial }}
                            </span>
                        </div>
                    </template>
                </Dropdown>
            </div>

            <!-- Loading -->
            <div v-if="loading" class="text-center py-8">
                <i class="pi pi-spin pi-spinner" style="font-size: 2rem"></i>
                <p class="mt-2">Cargando competencias...</p>
            </div>

            <!-- Tabla de competencias -->
            <div v-else-if="competencias.length > 0" class="bg-white rounded-lg border">
                <div class="p-4 bg-blue-600 text-white font-bold flex justify-between items-center">
                    <span>Competencias Sin Docente ({{ competencias.length }})</span>
                    <Button
                        label="Exportar PDF"
                        icon="pi pi-file-pdf"
                        @click="exportarPDF"
                        class="p-button-outlined p-button-sm"
                    />
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Competencia
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Programa - Filial
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Estudiantes
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Estudiantes Sin Nota
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Estudiantes Con Nota
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    % Sin Nota
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr
                                v-for="(item, index) in competencias"
                                :key="item.competencia_id"
                                :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
                            >
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-semibold text-blue-700">{{ item.competencia_nombre }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ item.programa }}</div>
                                    <div class="text-sm text-gray-500">{{ item.filial }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">
                                        {{ item.total_estudiantes }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                                        {{ item.estudiantes_sin_nota }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                        {{ item.estudiantes_con_nota }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center">
                                        <span
                                            class="px-3 py-1 rounded-full text-sm font-semibold"
                                            :class="getPorcentajeClass(item.porcentaje_sin_nota)"
                                        >
                                            {{ item.porcentaje_sin_nota }}%
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Sin datos -->
            <div v-else class="text-center py-12 bg-gray-50 rounded-lg">
                <i class="pi pi-inbox" style="font-size: 3rem; color: #9CA3AF;"></i>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No hay competencias sin docente</h3>
                <p class="mt-2 text-gray-500">Todas las competencias tienen docente asignado para los filtros seleccionados.</p>
            </div>

            <!-- Información del reporte -->
            <div v-if="competencias.length > 0" class="mt-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="font-semibold mb-2">Información del Reporte</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div><strong>Período:</strong> {{ periodoSeleccionado?.nombre || 'No seleccionado' }}</div>
                    <div><strong>Programa:</strong> {{ programaSeleccionado?.programa || 'Todos los programas' }}</div>
                    <div><strong>Fecha:</strong> {{ fechaActual }}</div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/LayoutSupervisor.vue';
import { Head } from '@inertiajs/vue3';
import Dropdown from 'primevue/dropdown';
import Button from 'primevue/button';

const periodos = ref([]);
const programas = ref([]);
const selectedPeriodo = ref(null);
const selectedPrograma = ref(null);
const competencias = ref([]);
const loading = ref(false);

// Computed properties
const periodoSeleccionado = computed(() => {
    return periodos.value.find(p => p.id_periodo === selectedPeriodo.value);
});

const programaSeleccionado = computed(() => {
    return programas.value.find(p => p.id === selectedPrograma.value);
});

const fechaActual = computed(() => {
    return new Date().toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
});

// Obtener parámetros de la URL
const page = usePage();
const urlParams = new URLSearchParams(window.location.search);
const periodoUrl = urlParams.get('periodo');
const programaUrl = urlParams.get('programa');

// Métodos
const getProgramaLabel = (programaId) => {
    const programa = programas.value.find(p => p.id === programaId);
    return programa ? `${programa.programa} (${programa.filial})` : '';
};

const getPorcentajeClass = (porcentaje) => {
    if (porcentaje >= 80) return 'bg-red-100 text-red-800';
    if (porcentaje >= 50) return 'bg-orange-100 text-orange-800';
    return 'bg-yellow-100 text-yellow-800';
};

const cargarPeriodos = async () => {
    try {
        const res = await axios.get('obtener-periodos');

        if (res.data && Array.isArray(res.data.periodos)) {
            periodos.value = res.data.periodos;
        } else if (res.data && Array.isArray(res.data.raw)) {
            periodos.value = res.data.raw;
        } else if (Array.isArray(res.data)) {
            periodos.value = res.data;
        } else {
            periodos.value = [];
        }

        // Establecer periodo desde URL o por defecto
        if (periodoUrl) {
            selectedPeriodo.value = parseInt(periodoUrl);
        } else if (periodos.value.length > 0) {
            const periodoActivo = periodos.value.find(p => p.estado === 'activo') || periodos.value[0];
            selectedPeriodo.value = periodoActivo.id_periodo;
        }
    } catch (error) {
        console.error('Error cargando periodos:', error);
        periodos.value = [];
    }
};

const cargarProgramas = async () => {
    try {
        const res = await axios.get('get-programas-filial');

        if (res.data.estado && Array.isArray(res.data.programas)) {
            programas.value = res.data.programas;
        } else {
            programas.value = [];
        }

        // Establecer programa desde URL
        if (programaUrl) {
            selectedPrograma.value = parseInt(programaUrl);
        }
    } catch (error) {
        console.error('Error cargando programas:', error);
        programas.value = [];
    }
};

const cargarCompetencias = async () => {
    if (!selectedPeriodo.value) return;

    try {
        loading.value = true;

        const params = {
            periodo: selectedPeriodo.value
        };

        if (selectedPrograma.value) {
            params.programa = selectedPrograma.value;
        }

        const res = await axios.get('competencias-sin-docente', { params });

        if (res.data.estado) {
            competencias.value = res.data.datos || [];
        } else {
            competencias.value = [];
        }
    } catch (error) {
        console.error('Error cargando competencias:', error);
        competencias.value = [];
    } finally {
        loading.value = false;
    }
};

const volverAlDashboard = () => {
    router.get(route('monitoreo-docentes'));
};

const exportarPDF = () => {
    // Implementar exportación PDF similar al dashboard
    const contenidoHTML = `
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Competencias Sin Docente</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
                .header h1 { color: #2c5aa0; margin: 0; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
                th { background-color: #2c5aa0; color: white; }
                tr:nth-child(even) { background-color: #f2f2f2; }
                .info { background-color: #f5f5f5; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Reporte de Competencias Sin Docente</h1>
                <p>Sistema de Nivelación - Universidad</p>
            </div>

            <div class="info">
                <p><strong>Período:</strong> ${periodoSeleccionado.value?.nombre || 'No seleccionado'}</p>
                <p><strong>Programa:</strong> ${programaSeleccionado.value?.programa || 'Todos los programas'}</p>
                <p><strong>Fecha:</strong> ${fechaActual.value}</p>
                <p><strong>Total de competencias:</strong> ${competencias.value.length}</p>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Competencia</th>
                        <th>Programa</th>
                        <th>Filial</th>
                        <th>Total Estudiantes</th>
                        <th>Est. Sin Nota</th>
                        <th>Est. Con Nota</th>
                        <th>% Sin Nota</th>
                    </tr>
                </thead>
                <tbody>
                    ${competencias.value.map(item => `
                        <tr>
                            <td>${item.competencia_nombre}</td>
                            <td>${item.programa}</td>
                            <td>${item.filial}</td>
                            <td>${item.total_estudiantes}</td>
                            <td>${item.estudiantes_sin_nota}</td>
                            <td>${item.estudiantes_con_nota}</td>
                            <td>${item.porcentaje_sin_nota}%</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </body>
        </html>
    `;

    const ventana = window.open('', '_blank');
    ventana.document.write(contenidoHTML);
    ventana.document.close();

    setTimeout(() => {
        ventana.print();
    }, 500);
};

// Cargar datos iniciales
onMounted(async () => {
    await cargarPeriodos();
    await cargarProgramas();
    setTimeout(() => {
        cargarCompetencias();
    }, 100);
});
</script>

<style scoped>
:deep(.p-dropdown) {
    width: 100%;
}
</style>
