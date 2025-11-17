<template>
    <AuthenticatedLayout>
        <Head title="Monitoreo de Docentes" />

        <div class="p-4 bg-white rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Monitoreo de Docentes</h2>

            <!-- Filtros -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Filtro de Periodo -->
                <Dropdown
                    v-model="selectedPeriodo"
                    :options="periodos"
                    optionLabel="nombre"
                    optionValue="id_periodo"
                    placeholder="Seleccionar Periodo"
                    class="w-full"
                    @change="cargarDashboard"
                    showClear
                    filter
                />

                <!-- Filtro de Programa -->
                <Dropdown
                    v-model="selectedPrograma"
                    :options="programas"
                    optionLabel="programa"
                    optionValue="id"
                    placeholder="Seleccionar Programa"
                    class="w-full"
                    @change="cargarDashboard"
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

                <!-- Botones de acción -->
                <div class="flex gap-2 items-end">
                    <Button
                        label="Generar PDF"
                        icon="pi pi-file-pdf"
                        @click="generarPDF"
                        class="p-button-danger"
                        :disabled="!selectedPeriodo || !estadisticas"
                        :loading="generandoPDF"
                    />
                    <Button
                        label="Competencias sin docente"
                        @click="verCompetenciasSinDocente"
                        size="small"
                        class="p-button-info"
                        :disabled="!selectedPeriodo"
                    />
                    <Button
                        label="Docentes faltan notas"
                        @click="verDocentesFaltanNotas"
                        size="small"
                        class="p-button-warning"
                        :disabled="!selectedPeriodo"
                    />
                </div>
            </div>

            <!-- Loading -->
            <div v-if="loading" class="text-center py-8">
                <i class="pi pi-spin pi-spinner" style="font-size: 2rem"></i>
                <p class="mt-2">Cargando información...</p>
            </div>

            <!-- Estadísticas -->
            <div v-else-if="estadisticas" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <div class="text-blue-600 font-bold text-2xl">{{ estadisticas.competencias_sin_docente || 0 }}</div>
                    <div class="text-blue-800 text-sm">Competencias sin docente</div>
                </div>
                <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                    <div class="text-orange-600 font-bold text-2xl">{{ estadisticas.docentes_faltan_notas || 0 }}</div>
                    <div class="text-orange-800 text-sm">Docentes faltan notas</div>
                </div>
                <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                    <div class="text-red-600 font-bold text-2xl">{{ estadisticas.total_estudiantes_sin_nota || 0 }}</div>
                    <div class="text-red-800 text-sm">Estudiantes sin nota</div>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                    <div class="text-purple-600 font-bold text-2xl">{{ estadisticas.programas_afectados || 0 }}</div>
                    <div class="text-purple-800 text-sm">Programas afectados</div>
                </div>
            </div>

            <div v-else-if="!loading" class="text-center py-8 text-gray-500">
                No hay datos para mostrar. Selecciona un período.
            </div>

            <!-- Contenido principal -->
            <div v-if="!loading && estadisticas" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Competencias sin docente -->
                <div class="border rounded-lg">
                    <div class="bg-blue-600 text-white p-3 font-bold flex justify-between items-center">
                        <span>Competencias que Necesitan Docente</span>
                        <span class="bg-blue-800 px-2 py-1 rounded-full text-sm">{{ competenciasSinDocente.length }}</span>
                    </div>
                    <div class="p-4 max-h-96 overflow-y-auto">
                        <div v-if="competenciasSinDocente.length === 0" class="text-gray-500 text-center py-4">
                            No hay competencias sin docente asignado
                        </div>
                        <div v-else class="space-y-3">
                            <div
                                v-for="item in competenciasSinDocente"
                                :key="item.competencia_id"
                                class="border-b pb-2 last:border-b-0"
                            >
                                <div class="font-semibold text-blue-700">{{ item.competencia_nombre }}</div>
                                <div class="text-sm text-gray-600">{{ item.programa }} - {{ item.filial }}</div>
                                <div class="flex justify-between items-center mt-1">
                                    <div class="text-sm text-red-600 font-semibold">
                                        {{ item.estudiantes_afectados }} estudiantes afectados
                                    </div>
                                    <div class="text-xs bg-gray-100 px-2 py-1 rounded">
                                        {{ item.estudiantes_sin_nota }} sin nota
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Docentes que faltan subir notas - ACTUALIZADO -->
                <div class="border rounded-lg">
                    <div class="bg-orange-600 text-white p-3 font-bold flex justify-between items-center">
                        <span>Docentes que Faltan Subir Notas</span>
                        <span class="bg-orange-800 px-2 py-1 rounded-full text-sm">{{ docentesFaltanNotas.length }}</span>
                    </div>
                    <div class="p-4 max-h-96 overflow-y-auto">
                        <div v-if="docentesFaltanNotas.length === 0" class="text-gray-500 text-center py-4">
                            Todos los docentes han subido sus notas
                        </div>
                        <div v-else class="space-y-3">
                            <div
                                v-for="item in docentesFaltanNotas"
                                :key="item.docente_id"
                                class="border-b pb-2 last:border-b-0"
                            >
                                <div class="font-semibold text-orange-700">{{ item.docente_nombre }}</div>
                                <div class="text-sm text-gray-600">
                                    {{ item.competencia_nombre }} - {{ item.programa }}
                                </div>

                                <!-- Estadísticas de estudiantes - NUEVO -->
                                <div class="mt-2 space-y-1">
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="text-red-600 font-semibold">
                                            📝 {{ item.estudiantes_sin_nota }} estudiantes sin nota
                                        </span>
                                        <span class="text-red-500 font-bold">
                                            {{ item.porcentaje_sin_nota }}%
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="text-green-600">
                                            ✅ {{ item.estudiantes_con_nota || 0 }} estudiantes con nota
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center text-xs border-t pt-1 mt-1">
                                        <span class="text-blue-600 font-bold">
                                            📊 Total: {{ item.total_estudiantes }} estudiantes
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del reporte -->
            <div v-if="!loading && estadisticas" class="mt-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="font-semibold mb-2">Información del Reporte</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <strong>Período:</strong> {{ periodoSeleccionado?.nombre || 'No seleccionado' }}
                    </div>
                    <div>
                        <strong>Programa:</strong> {{ programaSeleccionado?.programa || 'Todos los programas' }}
                    </div>
                    <div>
                        <strong>Fecha de generación:</strong> {{ fechaActual }}
                    </div>
                    <div>
                        <strong>Total de registros:</strong> {{ totalRegistros }}
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/LayoutSupervisor.vue';
import { Head } from '@inertiajs/vue3';
import Dropdown from 'primevue/dropdown';
import Button from 'primevue/button';

const periodos = ref([]);
const programas = ref([]);
const selectedPeriodo = ref(null);
const selectedPrograma = ref(null);

const estadisticas = ref(null);
const competenciasSinDocente = ref([]);
const docentesFaltanNotas = ref([]);
const loading = ref(false);
const generandoPDF = ref(false);

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

const totalRegistros = computed(() => {
    return competenciasSinDocente.value.length + docentesFaltanNotas.value.length;
});

// Obtener label del programa seleccionado
const getProgramaLabel = (programaId) => {
    const programa = programas.value.find(p => p.id === programaId);
    return programa ? `${programa.programa} (${programa.filial})` : '';
};

// Cargar periodos
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

        if (periodos.value.length > 0) {
            const periodoActivo = periodos.value.find(p => p.estado === 'activo') || periodos.value[0];
            selectedPeriodo.value = periodoActivo.id_periodo;
        }
    } catch (error) {
        console.error('Error cargando periodos:', error);
        periodos.value = [];
    }
};

// Cargar programas desde la nueva ruta
const cargarProgramas = async () => {
    try {
        const res = await axios.get('get-programas-filial');

        if (res.data.estado && Array.isArray(res.data.programas)) {
            programas.value = res.data.programas;
        } else {
            programas.value = [];
        }
    } catch (error) {
        console.error('Error cargando programas:', error);
        programas.value = [];
    }
};

// Cargar dashboard
const cargarDashboard = async () => {
    if (!selectedPeriodo.value) return;

    try {
        loading.value = true;

        const params = {
            periodo: selectedPeriodo.value
        };

        if (selectedPrograma.value) {
            params.programa = selectedPrograma.value;
        }

        const res = await axios.get('dashboard-docentes', { params });

        if (res.data.estado) {
            estadisticas.value = res.data.estadisticas || {};
            competenciasSinDocente.value = res.data.competencias_sin_docente || [];
            docentesFaltanNotas.value = res.data.docentes_faltan_notas || [];

            console.log('Datos de docentes:', docentesFaltanNotas.value);
        }
    } catch (error) {
        console.error('Error cargando dashboard:', error);
        estadisticas.value = null;
    } finally {
        loading.value = false;
    }
};

// Generar PDF
const generarPDF = async () => {
    if (!selectedPeriodo.value || !estadisticas.value) return;

    try {
        generandoPDF.value = true;

        // Crear contenido HTML para el PDF
        const contenidoHTML = `
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Reporte de Monitoreo de Docentes</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
                    .header h1 { color: #2c5aa0; margin: 0; }
                    .header .subtitle { color: #666; font-size: 14px; }
                    .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 30px; }
                    .stat-card { border: 1px solid #ddd; padding: 15px; text-align: center; border-radius: 5px; }
                    .stat-number { font-size: 24px; font-weight: bold; margin-bottom: 5px; }
                    .stat-label { font-size: 12px; color: #666; }
                    .section { margin-bottom: 25px; }
                    .section-title { background-color: #2c5aa0; color: white; padding: 10px; font-weight: bold; border-radius: 5px 5px 0 0; }
                    .section-content { border: 1px solid #ddd; border-top: none; padding: 15px; }
                    .item { margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #eee; }
                    .item:last-child { border-bottom: none; }
                    .item-title { font-weight: bold; color: #2c5aa0; }
                    .item-details { font-size: 12px; color: #666; margin: 5px 0; }
                    .item-stats { font-size: 12px; margin: 5px 0; }
                    .stat-row { display: flex; justify-content: space-between; margin: 2px 0; }
                    .info-section { background-color: #f5f5f5; padding: 15px; border-radius: 5px; margin-top: 20px; }
                    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 12px; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>Reporte de Monitoreo de Docentes</h1>
                    <div class="subtitle">Sistema de Nivelación - Universidad</div>
                </div>

                <div class="info-section">
                    <div class="info-grid">
                        <div><strong>Período:</strong> ${periodoSeleccionado.value?.nombre || 'No seleccionado'}</div>
                        <div><strong>Programa:</strong> ${programaSeleccionado.value?.programa || 'Todos los programas'}</div>
                        <div><strong>Fecha de generación:</strong> ${fechaActual.value}</div>
                        <div><strong>Total de registros:</strong> ${totalRegistros.value}</div>
                    </div>
                </div>

                <div class="stats">
                    <div class="stat-card">
                        <div class="stat-number">${estadisticas.value.competencias_sin_docente || 0}</div>
                        <div class="stat-label">Competencias sin docente</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">${estadisticas.value.docentes_faltan_notas || 0}</div>
                        <div class="stat-label">Docentes faltan notas</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">${estadisticas.value.total_estudiantes_sin_nota || 0}</div>
                        <div class="stat-label">Estudiantes sin nota</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">${estadisticas.value.programas_afectados || 0}</div>
                        <div class="stat-label">Programas afectados</div>
                    </div>
                </div>

                <div class="section">
                    <div class="section-title">Competencias que Necesitan Docente (${competenciasSinDocente.value.length})</div>
                    <div class="section-content">
                        ${competenciasSinDocente.value.length === 0 ?
                            '<p style="text-align: center; color: #666;">No hay competencias sin docente asignado</p>' :
                            competenciasSinDocente.value.map(item => `
                                <div class="item">
                                    <div class="item-title">${item.competencia_nombre}</div>
                                    <div class="item-details">${item.programa} - ${item.filial}</div>
                                    <div class="item-stats">
                                        <div class="stat-row">
                                            <span>${item.estudiantes_afectados} estudiantes afectados</span>
                                            <span>${item.estudiantes_sin_nota} sin nota</span>
                                        </div>
                                    </div>
                                </div>
                            `).join('')
                        }
                    </div>
                </div>

                <div class="section">
                    <div class="section-title">Docentes que Faltan Subir Notas (${docentesFaltanNotas.value.length})</div>
                    <div class="section-content">
                        ${docentesFaltanNotas.value.length === 0 ?
                            '<p style="text-align: center; color: #666;">Todos los docentes han subido sus notas</p>' :
                            docentesFaltanNotas.value.map(item => `
                                <div class="item">
                                    <div class="item-title">${item.docente_nombre}</div>
                                    <div class="item-details">${item.competencia_nombre} - ${item.programa}</div>
                                    <div class="item-stats">
                                        <div class="stat-row">
                                            <span style="color: red; font-weight: bold;">📝 ${item.estudiantes_sin_nota} estudiantes sin nota</span>
                                            <span style="color: red; font-weight: bold;">${item.porcentaje_sin_nota}%</span>
                                        </div>
                                        <div class="stat-row">
                                            <span style="color: green;">✅ ${item.estudiantes_con_nota || 0} estudiantes con nota </span>
                                        </div>
                                        <div class="stat-row" style="border-top: 1px solid #eee; padding-top: 5px; margin-top: 5px;">
                                            <span style="color: blue; font-weight: bold;">📊 Total: ${item.total_estudiantes} estudiantes</span>
                                        </div>
                                    </div>
                                </div>
                            `).join('')
                        }
                    </div>
                </div>
            </body>
            </html>
        `;

        // Abrir ventana para imprimir/guardar como PDF
        const ventana = window.open('', '_blank');
        ventana.document.write(contenidoHTML);
        ventana.document.close();

        // Esperar a que cargue el contenido y luego imprimir
        setTimeout(() => {
            ventana.print();
        }, 500);

    } catch (error) {
        console.error('Error generando PDF:', error);
        alert('Error al generar el PDF. Por favor, intente nuevamente.');
    } finally {
        generandoPDF.value = false;
    }
};

// Ver competencias sin docente
// REEMPLAZAR ESTOS DOS MÉTODOS:

// Ver competencias sin docente - NUEVA VERSIÓN
const verCompetenciasSinDocente = () => {
    if (!selectedPeriodo.value) return;

    const params = new URLSearchParams();
    params.append('periodo', selectedPeriodo.value);
    if (selectedPrograma.value) params.append('programa', selectedPrograma.value);

    // Cambiar esta línea para abrir la VISTA en lugar del JSON
    window.open(`monitoreo-docentes/competencias-sin-docente?${params.toString()}`, '_blank');
};

// Ver docentes que faltan notas - NUEVA VERSIÓN
const verDocentesFaltanNotas = () => {
    if (!selectedPeriodo.value) return;

    const params = new URLSearchParams();
    params.append('periodo', selectedPeriodo.value);
    if (selectedPrograma.value) params.append('programa', selectedPrograma.value);

    // Cambiar esta línea para abrir la VISTA en lugar del JSON
    window.open(`monitoreo-docentes/docentes-faltan-notas?${params.toString()}`, '_blank');
};

// Cargar datos iniciales
onMounted(async () => {
    await cargarPeriodos();
    await cargarProgramas();
    setTimeout(() => {
        cargarDashboard();
    }, 100);
});
</script>

<style scoped>
:deep(.p-dropdown) {
    width: 100%;
}

.stat-card {
    transition: transform 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
}
</style>
