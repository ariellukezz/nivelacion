<template>
    <AuthenticatedLayout>
        <div class="p-4" style="background: white;">
            <div class="flex justify-between mb-2">
                <div>
                    <label for="programa">Programa de Estudio: </label>
                    <select id="programa" v-model="filtroPrograma" @change="filtrarEstudiantes">
                        <option v-for="programa in programas" :key="programa" :value="programa">{{ programa }}</option>
                    </select>
                </div>
                <div>
                    <label for="busqueda">Buscar por Nombre o DNI: </label>
                    <input id="busqueda" v-model="filtroBusqueda" @input="filtrarEstudiantes" type="text" placeholder="Buscar...">
                </div>
                <Button label="Exportar excel" @click="exportarExcel" size="small" style="height: 40px;"/>
            </div>

            <table style="border-radius: 4px; width: 100%; overflow: hidden;">
                <thead style="height: 30px; color: white; background-color: var(--primary-color); padding: 0px 10px;">
                    <tr style="font-size: .9rem;">
                        <th style="border: 1px solid #d9d9d9;">DNI</th>
                        <th style="border: 1px solid #d9d9d9;">APELLIDOS Y NOMBRES</th>
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
                    <tr v-for="item in estudiantesFiltrados" :key="item.id" style="font-size: .9rem;">
                        <td style="border: 1px solid #d9d9d9; font-weight: .8rem; text-align: center;">
                            <div class="pl-1 pr-1">{{ item.dni }}</div>
                        </td>
                        <td style="border: 1px solid #d9d9d9; font-weight: .7rem;">
                            <div class="pl-1 pr-1">{{ item.paterno }} {{ item.materno }},  {{ item.nombre }}</div>
                        </td>
                        <td style="border: 1px solid #d9d9d9; font-weight: .8rem; text-align: center;">
                            <div class="pl-1 pr-1">{{ item.programa }}</div>
                        </td>
                        <td style="border: 1px solid #d9d9d9; font-weight: .8rem; text-align: center;">
                            <div class="pl-1 pr-1">{{ item.semestre }}</div>
                        </td>
                        <td v-for="(ite, index) in 11" :key="index" style="border: 1px solid #d9d9d9; min-width: 40px;">
                            <div v-if="buscarValor(item.notas, ite) !== null" style="text-align: center;">
                                <div v-if="item.notas[buscarValor(item.notas, ite)]">
                                    {{ item.notas[buscarValor(item.notas, ite)].nota }}
                                </div>
                                <div v-else>
                                    <span style="font-size: .9rem;"></span>
                                </div>
                            </div>
                            <div v-else style="text-align: center;">
                                - -
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/LayoutSupervisor.vue';
import { ref } from 'vue';
import axios from 'axios';
import XLSX from 'xlsx';
import Button from 'primevue/button';

function buscarValor(array, valor) {
    for (var i = 0; i < array.length; i++) {
        if (array[i].competencia === valor) {
            return i;
        }
    }
    return null;
}

const pagina = ref(1);
const estudiantes = ref([]);
const estudiantesFiltrados = ref([]);
const programas = ref([]);
const filtroPrograma = ref('');
const filtroBusqueda = ref('');
const objetosEstudiantes = ref([]);

const getEstudiantes = async () => {
    let res = await axios.get('notasperf-supervisor?page=' + pagina.value, { term: '' });
    estudiantes.value = res.data.datos;
    generarProgramas();
    if (programas.value.length > 0) {
        filtroPrograma.value = programas.value[0]; // Seleccionar el primer programa por defecto
        filtrarEstudiantes();
    }
}

const generarProgramas = () => {
    const programasSet = new Set();
    estudiantes.value.forEach(item => {
        programasSet.add(item.programa);
    });
    programas.value = Array.from(programasSet);
}

const filtrarEstudiantes = () => {
    estudiantesFiltrados.value = estudiantes.value.filter(item => {
        return item.programa === filtroPrograma.value && (
            item.dni.includes(filtroBusqueda.value) ||
            `${item.paterno} ${item.materno} ${item.nombre}`.toLowerCase().includes(filtroBusqueda.value.toLowerCase())
        );
    });
    generarObjetosEstudiantes();
}

const generarObjetosEstudiantes = () => {
    objetosEstudiantes.value = estudiantesFiltrados.value.map(item => {
        const estudiante = {
            dni: item.dni,
            programa: item.programa,
            semestre: item.semestre,
            nombreCompleto: `${item.paterno} ${item.materno},  ${item.nombre}`,
        };

        for (let i = 1; i <= 11; i++) {
            const index = buscarValor(item.notas, i);
            if (index !== null) {
                estudiante[`C${i}`] = item.notas[index].nota;
            } else {
                estudiante[`C${i}`] = '--';
            }
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

getEstudiantes();
</script>
