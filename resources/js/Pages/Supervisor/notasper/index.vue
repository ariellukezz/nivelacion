<template>
    <AuthenticatedLayout>
        <div class="p-4" style="background: white;">
            <div class="flex justify-between mb-2 gap-4 flex-wrap">

                <!-- 🔹 PRIMERO: FILTRO DE PERIODO -->
                <div>
                    <label for="periodo">Periodo: </label>
                    <select
                        id="periodo"
                        v-model="filtroPeriodo"
                        @change="cambiarPeriodo"
                    >
                        <option
                            v-for="p in periodos"
                            :key="p.id_periodo"
                            :value="p.id_periodo"
                        >
                            {{ p.nombre }} <span v-if="p.estado === 'activo'">(actual)</span>
                        </option>
                    </select>
                </div>

                <!-- 🔹 PROGRAMA -->
                <div>
                    <label for="programa">Programa de Estudio: </label>
                    <select
                        id="programa"
                        v-model="filtroPrograma"
                        @change="filtrarEstudiantes"
                    >
                        <option v-for="programa in programas" :key="programa" :value="programa">
                            {{ programa }}
                        </option>
                    </select>
                </div>

                <!-- 🔹 BÚSQUEDA -->
                <div>
                    <label for="busqueda">Buscar por Nombre o CODIGO: </label>
                    <input
                        id="busqueda"
                        v-model="filtroBusqueda"
                        @input="filtrarEstudiantes"
                        type="text"
                        placeholder="Buscar..."
                    >
                </div>

                <!-- 🔹 EXPORTAR -->
                <div class="flex items-end">
                    <Button
                        label="Exportar excel"
                        @click="exportarExcel"
                        size="small"
                        style="height: 40px;"
                        :disabled="estudiantesFiltrados.length === 0"
                    />
                </div>
            </div>

            <!-- Mensajes de estado -->
            <div v-if="cargando" class="mb-2 text-sm text-gray-600">
                Cargando información...
            </div>
            <div v-else-if="!cargando && estudiantesFiltrados.length === 0" class="mb-2 text-sm text-gray-600">
                No se encontraron estudiantes para el período seleccionado.
            </div>

            <table
                v-if="estudiantesFiltrados.length > 0"
                style="border-radius: 4px; width: 100%; overflow: hidden;"
            >
                <thead
                    style="height: 30px; color: white; background-color: var(--primary-color); padding: 0px 10px;"
                >
                    <tr style="font-size: .9rem;">
                        <th style="border: 1px solid #d9d9d9;">CODIGO</th>
                        <!-- <th style="border: 1px solid #d9d9d9;">DNI</th> -->
                        <th style="border: 1px solid #d9d9d9;">APELLIDOS Y NOMBRES</th>
                        <th style="border: 1px solid #d9d9d9;">UBICACION</th>
                        <th style="border: 1px solid #d9d9d9;">PROGRAMA DE ESTUDIO</th>
                        <th style="border: 1px solid #d9d9d9;">INGRESO</th>
                        <th style="border: 1px solid #d9d9d9;">C1</th>
                        <th style="border: 1px solid #d9d9d9;">C2</th>
                        <th style="border: 1px solid #d9d9d9;">C3</th>
                        <th style="border: 1px solid #d9d9d9;">C4</th>
                        <th style="border: 1px solid #d9d9d9;">C5</th>
                        <th style="border: 1px solid #d9d9d9;">C6</th>
                        <th style="border: 1px solid #d9d9d9;">C7</th>
                        <th style="border: 1px solid #d9d9d9;">C8</th>
                        <th style="border: 1px solid #d9d9d9;">C9</th>
                        <th style="border: 1px solid #d9d9d9;">C10</th>
                        <th style="border: 1px solid #d9d9d9;">C11</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="item in estudiantesFiltrados"
                        :key="item.id_estudiante"
                        style="font-size: .9rem;"
                    >
                        <td
                            style="border: 1px solid #d9d9d9; font-weight: .8rem; text-align: center;"
                        >
                            <div class="pl-1 pr-1">{{ item.codigo_est }}</div>
                        </td>

                        <td style="border: 1px solid #d9d9d9; font-weight: .7rem;">
                            <div class="pl-1 pr-1">
                                {{ item.paterno }} {{ item.materno }}, {{ item.nombre }}
                            </div>
                        </td>

                        <td
                            style="border: 1px solid #d9d9d9; font-weight: .8rem; text-align: center;"
                        >
                            <div class="pl-1 pr-1">{{ item.filial }}</div>
                        </td>

                        <td
                            style="border: 1px solid #d9d9d9; font-weight: .8rem; text-align: center;"
                        >
                            <div class="pl-1 pr-1">{{ item.programa }}</div>
                        </td>

                        <td
                            style="border: 1px solid #d9d9d9; font-weight: .8rem; text-align: center;"
                        >
                            <div class="pl-1 pr-1">{{ item.semestre }}</div>
                        </td>

                        <!-- C1 a C11 con estilos condicionales -->
                        <td
                            v-for="i in 11"
                            :key="i"
                            style="border: 1px solid #d9d9d9; min-width: 40px;"
                            :class="getClassForCompetencia(item['C' + i])"
                        >
                            <div style="text-align: center; font-weight: bold;">
                                {{ item['C' + i] }}
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Leyenda de colores -->
            <div v-if="estudiantesFiltrados.length > 0" class="mt-4 flex gap-4 text-xs">
                <div class="flex items-center gap-1">
                    <div class="w-4 h-4 bg-green-100 border border-green-400"></div>
                    <span>AP (Aprobado)</span>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-4 h-4 bg-orange-100 border border-orange-400"></div>
                    <span>S/N (Sin Nota)</span>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-4 h-4 bg-red-100 border border-red-400"></div>
                    <span>Nota 0-10</span>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-4 h-4 bg-white border border-gray-300"></div>
                    <span>Nota 11-20</span>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/LayoutSupervisor.vue';
import { ref } from 'vue';
import axios from 'axios';
import XLSX from 'xlsx';
import Button from 'primevue/button';

const pagina = ref(1);
const estudiantes = ref([]);
const estudiantesFiltrados = ref([]);
const programas = ref([]);
const filtroPrograma = ref('');
const filtroBusqueda = ref('');
const objetosEstudiantes = ref([]);

const periodos = ref([]);
const filtroPeriodo = ref(null);
const cargando = ref(false);

// 🔹 Función para determinar la clase CSS según el valor de la competencia
const getClassForCompetencia = (valor) => {
    if (valor === 'AP') {
        return 'bg-green-100 border-green-400';
    } else if (valor === 'S/N') {
        return 'bg-orange-100 border-orange-400';
    } else {
        // Intentar convertir a número para verificar el rango
        const nota = parseFloat(valor);
        if (!isNaN(nota) && nota >= 0 && nota <= 10) {
            return 'bg-red-100 border-red-400';
        }
    }
    return 'bg-white';
};

// 🔹 Obtener periodos desde backend: GET /periodos
const getPeriodos = async () => {
    try {
        cargando.value = true;
        const res = await axios.get('periodos');

        let lista = [];

        if (Array.isArray(res.data.raw)) {
            lista = res.data.raw;
        } else if (Array.isArray(res.data.periodos)) {
            lista = res.data.periodos;
        } else if (Array.isArray(res.data)) {
            lista = res.data;
        } else if (Array.isArray(res.data.rows)) {
            lista = res.data.rows;
        } else {
            console.warn('Formato inesperado en /periodos:', res.data);
        }

        periodos.value = lista;

        if (!Array.isArray(periodos.value) || periodos.value.length === 0) {
            filtroPeriodo.value = null;
            estudiantes.value = [];
            estudiantesFiltrados.value = [];
            return;
        }

        // Seleccionar primero el periodo ACTIVO, si no hay, el primero
        const activo = periodos.value.find(p => p.estado === 'activo');
        if (activo) {
            filtroPeriodo.value = activo.id_periodo;
        } else {
            filtroPeriodo.value = periodos.value[0].id_periodo;
        }

        // Una vez definido el periodo, traemos estudiantes
        await getEstudiantes();
    } catch (error) {
        console.error('Error al obtener periodos', error);
        periodos.value = [];
        filtroPeriodo.value = null;
    } finally {
        cargando.value = false;
    }
};

// 🔹 Obtener estudiantes según periodo seleccionado
const getEstudiantes = async () => {
    if (!filtroPeriodo.value) {
        estudiantes.value = [];
        estudiantesFiltrados.value = [];
        return;
    }

    try {
        cargando.value = true;
        const res = await axios.get('notasperf-supervisor', {
            params: {
                page: pagina.value,
                periodo: filtroPeriodo.value,
            }
        });

        // Backend ahora devuelve: id_estudiante, codigo_est, paterno, materno, nombre, programa, semestre, filial, C1..C11
        estudiantes.value = res.data.datos ?? [];
        generarProgramas();

        if (programas.value.length > 0) {
            filtroPrograma.value = programas.value[0]; // primer programa por defecto
            filtrarEstudiantes();
        } else {
            estudiantesFiltrados.value = [];
        }
    } catch (error) {
        console.error('Error al obtener estudiantes', error);
        estudiantes.value = [];
        estudiantesFiltrados.value = [];
    } finally {
        cargando.value = false;
    }
};

// 🔹 Cuando cambia el periodo
const cambiarPeriodo = async () => {
    filtroPrograma.value = '';
    filtroBusqueda.value = '';
    await getEstudiantes();
};

const generarProgramas = () => {
    const programasSet = new Set();
    estudiantes.value.forEach(item => {
        programasSet.add(item.programa);
    });
    programas.value = Array.from(programasSet);
};

const filtrarEstudiantes = () => {
    estudiantesFiltrados.value = estudiantes.value.filter(item => {
        const coincidePrograma = filtroPrograma.value
            ? item.programa === filtroPrograma.value
            : true;

        const texto = filtroBusqueda.value.toLowerCase();

        const coincideBusqueda =
            item.codigo_est.includes(filtroBusqueda.value) ||
            `${item.paterno} ${item.materno} ${item.nombre}`.toLowerCase().includes(texto);

        return coincidePrograma && coincideBusqueda;
    });

    generarObjetosEstudiantes();
};

// 🔹 Armar data para exportar Excel
const generarObjetosEstudiantes = () => {
    objetosEstudiantes.value = estudiantesFiltrados.value.map(item => {
        const estudiante = {
            codigo_est: item.codigo_est,
            programa: item.programa,
            semestre: item.semestre,
            filial: item.filial,
            nombreCompleto: `${item.paterno} ${item.materno},  ${item.nombre}`,
        };

        // C1..C11 directo del backend (ya con AP / S/N / nota)
        for (let i = 1; i <= 11; i++) {
            estudiante[`C${i}`] = item[`C${i}`];
        }

        return estudiante;
    });
};

const exportarExcel = () => {
    const ws = XLSX.utils.json_to_sheet(objetosEstudiantes.value);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Estudiantes');

    XLSX.writeFile(wb, 'estudiantes.xlsx');
};

// 🔹 Al cargar la página: primero periodos, luego estudiantes
getPeriodos();
</script>
