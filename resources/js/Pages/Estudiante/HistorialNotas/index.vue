<template>
<Head title="Historial de Notas"/>
<AuthenticatedLayout>
    <div class="card mb-4">
        <Message severity="info">
            Historial completo de todas tus notas por periodo académico.
        </Message>
    </div>

    <div class="bg-white shadow-xs p-4" style="min-height: calc(100vh - 110px);">
        <div v-for="periodo in periodosAgrupados" :key="periodo.nombre" class="mb-6">
            <!-- Header del Periodo -->
            <div class="periodo-header p-3 mb-3" :class="{'periodo-activo': periodo.estado === 'activo'}">
                <h3 class="text-lg font-bold">
                    {{ periodo.nombre }}
                    <Tag
                        v-if="periodo.estado === 'activo'"
                        severity="success"
                        value="PERIODO ACTUAL"
                        class="ml-2"
                    />
                </h3>
            </div>

            <!-- Tabla de notas del periodo -->
            <DataTable
                :showGridlines="false"
                :value="periodo.cursos"
                class="p-datatable-sm mb-4"
                tableStyle="min-width: 50rem"
                style="font-size: .9rem;"
            >
                <Column field="cod" header="CÓDIGO" width="100px">
                    <template #body="{ data }">
                        <Tag :value="data.cod" />
                    </template>
                </Column>

                <Column field="curso_nombre" header="CURSO"></Column>

                <Column field="nombres" header="DOCENTE">
                    <template #body="{ data }">
                        <div class="flex" style="justify-content: flex-start;">
                            <div v-if="data.nombres">
                                {{ data.nombres }} {{ data.paterno }} {{ data.materno }}
                            </div>
                            <div v-else style="color: red; font-weight: bold;">
                                Sin docente
                            </div>
                        </div>
                    </template>
                </Column>

                <Column field="grupo" header="GRUPO" width="100px"></Column>
                <Column field="escuela" header="PROGRAMA" width="200px"></Column>
                <Column field="nota" header="NOTA" width="100px"></Column>

                <Column field="condicion" header="ESTADO" width="120px">
                    <template #body="{ data }">
                        <div class="flex" v-if="data.nota >= 10.5 && data.nota <= 20">
                            <Tag severity="success" value="Aprobado"></Tag>
                        </div>
                        <div class="flex" v-if="data.nota >= 0 && data.nota < 10.5">
                            <Tag severity="danger" value="Reprobado"></Tag>
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>

        <!-- Mensaje si no hay notas -->
        <div v-if="notas.length === 0" class="text-center py-8">
            <p style="color: #666;">ERROR DE SISTEMA - No se encontraron notas en tu historial (En Mantenimiento)</p>
        </div>
    </div>
</AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/LayoutEstudiante.vue';
import { Head } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Tag from 'primevue/tag';
import { ref, onMounted, computed } from 'vue'
import Message from 'primevue/message';

const notas = ref([])

// Agrupar notas por periodo
const periodosAgrupados = computed(() => {
    const agrupados = {};

    notas.value.forEach(nota => {
        if (!agrupados[nota.periodo_nombre]) {
            agrupados[nota.periodo_nombre] = {
                nombre: nota.periodo_nombre,
                estado: nota.periodo_estado,
                cursos: []
            };
        }
        agrupados[nota.periodo_nombre].cursos.push(nota);
    });

    return Object.values(agrupados);
});

const getHistorialNotas = async () => {
    let res = await axios.post("get-historial-notas");
    notas.value = res.data.datos;
}

getHistorialNotas();
</script>

<style scoped>
.periodo-header {
    background: #f8f9fa;
    border-left: 4px solid #6c757d;
    border-radius: 4px;
}

.periodo-activo {
    background: #d4edda;
    border-left-color: #28a745;
}
</style>
