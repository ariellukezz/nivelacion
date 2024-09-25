<template>
    <Head title="Documentos" />
    <AuthenticatedLayout>
        <div class="flex justify-between items-start">
    <!-- Filtros alineados a la izquierda -->
            <div class="flex flex-wrap items-start" style="gap: 1rem;">
                <!-- Filtro de escuela profesional -->
                <div class="mb-3" style="width: 240px;">
                    <Dropdown v-model="programa" :options="programasselect" filter optionLabel="label" optionValue="value" placeholder="Seleccione escuela profesional" style="width:100%;" class="w-full md:w-11rem">
                        <template #option="slotProps">
                            <div class="flex align-items-center" style="width: 400px; font-size:0.9rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                                <div>{{ slotProps.option.label }}</div>
                            </div>
                        </template>
                    </Dropdown>
                </div>

                <!-- Filtro de periodo -->
                <div class="mb-3" style="width: 240px;">
                    <Dropdown v-model="periodo" :options="periodosselect" filter optionLabel="label" optionValue="value" placeholder="Seleccione periodo" style="width:100%;" class="w-full md:w-11rem">
                        <template #option="slotProps">
                            <div class="flex align-items-center" style="width: 400px; font-size:0.9rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                                <div>{{ slotProps.option.label }}</div>
                            </div>
                        </template>
                    </Dropdown>
                </div>

                <!-- Filtro de tipo -->
                <div class="mb-3" style="width: 240px;">
                    <Dropdown v-model="tipo" :options="tiposselect" filter optionLabel="label" optionValue="value" placeholder="Seleccione tipo de documento" style="width:100%;" class="w-full md:w-11rem">
                        <template #option="slotProps">
                            <div class="flex align-items-center" style="width: 400px; font-size:0.9rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                                <div>{{ slotProps.option.label }}</div>
                            </div>
                        </template>
                    </Dropdown>
                </div>

                <!-- Botón para limpiar filtros -->
                <Button
                    label="Limpiar filtros"
                    icon="pi pi-filter-slash"
                    class="p-button-secondary"
                    @click="limpiarFiltros"
                />
            </div>

            <!-- Búsqueda alineada a la derecha -->
            <div>
                <span class="p-input-icon-left">
                    <i class="pi pi-search" />
                    <InputText v-model="buscar" placeholder="Search" />
                </span>
            </div>
        </div>

<!-- {{ documentos }} -->
        <div class="mt-3">
            <DataTable
                :showGridlines="false"
                :value="documentos"
                paginator
                :rows="10"
                :totalRecords="totalRegistros"
                :rowsPerPageOptions="[ 10, 20, 50]"
                :currentPage.sync="pagina"
                :class="'p-datatable-sm'"
                tableStyle="min-width: 50rem"
                style="font-size: .9rem;">
                <Column field="periodo" header="Periodo"></Column>
                <Column field="tipo" header="TIPO"></Column>
                <Column field="escuela" header="ESCUELA PROFESIONAL"></Column>
                <Column field="fecha_subida" header="FECHA"></Column>
                <Column field="username" header="USUARIO"></Column>
                <Column field="url" header="Ver Documento" style="min-width: 12rem">
                    <template #body="{ data }">
                        <Button label="Link" @click="abrirDocumento(data.url)" link>
                            <i class="pi pi-download" style="color: green"></i>
                        </Button>

                    </template>

                </Column>
                <Column field="id_programa" header="Estado" width="90px">
    <template #body="{ data }">
        <div class="flex">
            <div class="mr-2">
                <Button
                    v-if="data.aceptado === 1"
                    severity="success"
                    label="Aceptado"
                    aria-label="Submit"

                    size="small"
                    style="width: 90px; height: 25px;"
                ></Button>
                <Button
                    v-else-if="data.aceptado === 0"
                    severity="danger"
                    label="Rechazado"
                    aria-label="Submit"

                    size="small"
                    style="width: 90px; height: 25px;"
                ></Button>
                <Button
                    v-else
                    severity="secondary"
                    label="Pendiente"
                    aria-label="Submit"

                    size="small"
                    style="width: 90px; height: 25px;"
                ></Button>
            </div>
        </div>
    </template>
</Column>
                <Column header="Observaciones">
            <template #body="{ data }">
                <div>
                    {{ data.obser }}
                </div>
            </template>
        </Column>

        <Column field="id_programa" header="Acciones" width="90px">


                <template #body="{ data }">
                  <div class="flex">
                    <div class="mr-2">
                        <div v-if="data.aceptado == 1">
                            <Button severity="success" icon="pi pi-check" aria-label="Submit" @click="cambiarEstado(data)" size="small" style="width: 25px; height: 25px;"/>
                        </div>
                        <div v-if="data.aceptado == 0">
                            <Button severity="danger" icon="pi pi-times" aria-label="Submit" @click="cambiarEstado(data)" size="small" style="width: 25px; height: 25px;"/>
                        </div>
                        <div v-if="data.aceptado == null">
                            <Button severity="secondary" icon="pi pi-spinner" label="" @click="cambiarEstado(data)" size="small" style="width: 25px; height: 25px;"/>
                        </div>

                    </div>
                    <div class="mr-2">
                      <Button class="secondary" icon="pi pi-pencil" aria-label="Submit" @click="editar(data)" size="small" style="width: 25px; height: 25px;"/>
                    </div>
                  </div>
                </template>
              </Column>

            </DataTable>
            <div>

            </div>
        </div>

        <Dialog v-model:visible="modaleditar" modal header="Observación" :style="{ width: '25rem' }">
            <div class="w-full">
                <div>
                    <Textarea  v-model="archivo.obser" style="width: 100%;" variant="filled" rows="6"/>
                </div>
            </div>

            <template #footer>
                <div class="flex justify-end">
                    <Button icon="pi pi-save" label="Observar" severity="success" aria-label="Submit" @click="observar()"  size="small"  style="height: 25px;"/>
                </div>
            </template>
        </Dialog>
        <Toast/>


    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/LayoutSupervisor.vue';
import { Head } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { ref, watch } from 'vue'
import InputText from 'primevue/inputtext';
import 'primeicons/primeicons.css';
import Dropdown from 'primevue/dropdown';
import Paginator from 'primevue/paginator';
import Dialog from 'primevue/dialog';
import Textarea from 'primevue/textarea';
import Button from 'primevue/button';

import Toast from 'primevue/toast';
import { useToast } from "primevue/usetoast";
const toast = useToast();
const showToast = (tipo, titulo, detalle) => {
  toast.add({ severity: tipo, summary: titulo, detail: detalle, life: 3000 });
};


const modaleditar = ref(false);

const pagina = ref(1);
const buscar = ref(null);
const programa = ref(null);
const periodo = ref(null);
const tipo = ref(null); // Variable para el filtro de tipo
const aceptado = ref(null); // Variable para el filtro de aceptado
const totalRegistros = ref(0);
const documentos = ref([]);
const pageSize = ref(10);

// Opciones de periodos, tipos y aceptado
const periodosselect = ref([
    { value: '2024-I', label: '2024-I' },
    { value: '2024-II', label: '2024-II' },
]);

const tiposselect = ref([
    { value: 'Dictantes', label: 'Dictantes' },
    { value: 'Informe', label: 'Informe' },
    { value: 'Plan', label: 'Plan' },
    { value: 'Resolucion', label: 'Resolucion' },
    { value: 'Otro', label: 'Otro' }
]);

const aceptadoselect = ref([
    { value: 1, label: 'Aceptado' },
    { value: 0, label: 'Rechazado' },
    { value: null, label: 'Pendiente' }
]);


const getDocumentos = async () => {
    let res = await axios.post("get-documentos?page=" + pagina.value, { term: buscar.value, paginashoja: pageSize.value, programa: programa.value, periodo: periodo.value, tipo: tipo.value, aceptado: aceptado.value});
    documentos.value = res.data.datos.data;
    totalRegistros.value = res.data.datos.total;
}

const abrirDocumento = (url) => {
    window.open("../" + url, "_blank");
}

const archivo = ref({});


const observar = async () => {
    let res = await axios.post("observar-documento", archivo.value );
    getDocumentos();
    showToast('info', 'DOCUMENTO OBSERVADO', '');
    modaleditar.value= false;
    archivo.value = {}
}

const cambiarEstado = async (item) => {
    let res = await axios.post("cambiar-estado-documento", item );
    getDocumentos();
    showToast('info', 'ESTADO MODIFICADO', '');
}

const editar = (item) => {
    modaleditar.value = true;
    archivo.value = item;
}

const limpiarFiltros = () => {
    programa.value = null;
    periodo.value = null;
    tipo.value = null;
    aceptado.value = null;
    buscar.value = null;
    getDocumentos();  // Llama la función para recargar los documentos sin filtros
};

// Escuchar cambios en los filtros
watch(buscar, () => { getDocumentos(); });
watch(programa, () => { getDocumentos(); });
watch(periodo, () => { getDocumentos(); });
watch(tipo, () => { getDocumentos(); }); // Watch para "tipo"
watch(aceptado, () => { getDocumentos(); }); // Watch para "aceptado"
watch(pageSize, () => { getDocumentos(); });

const programasselect = ref([
    { value: 1, label: "INGENIERIA AGRONOMICA - PUNO" },
    { value: 2, label: "INGENIERIA AGROINDUSTRIAL - PUNO" },
    { value: 3, label: "INGENIERIA TOPOGRAFICA Y AGRIMENSURA - PUNO" },
    { value: 4, label: "MEDICINA VETERINARIA Y ZOOTECNIA - PUNO" },
    { value: 5, label: "INGENIERIA ECONOMICA - PUNO" },
    { value: 6, label: "CIENCIAS CONTABLES - PUNO" },
    { value: 7, label: "ADMINISTRACION - PUNO" },
    { value: 8, label: "TRABAJO SOCIAL - PUNO" },
    { value: 9, label: "ENFERMERIA - PUNO" },
    { value: 10, label: "INGENIERIA DE MINAS - PUNO" },
    { value: 11, label: "HUMANIDADES - PUNO" },
    { value: 12, label: "SOCIOLOGIA - PUNO" },
    { value: 13, label: "TURISMO - PUNO" },
    { value: 14, label: "ANTROPOLOGIA - PUNO" },
    { value: 15, label: "CIENCIAS DE LA COMUNICACION SOCIAL - PUNO" },
    { value: 16, label: "ARTE - PUNO" },
    { value: 17, label: "BIOLOGIA - PUNO" },
    { value: 18, label: "EDUCACION SECUNDARIA - PUNO" },
    { value: 19, label: "EDUCACION PRIMARIA - PUNO" },
    { value: 20, label: "EDUCACION INICIAL - PUNO" },
    { value: 21, label: "EDUCACION FISICA - PUNO" },
    { value: 22, label: "INGENIERIA ESTADISTICA E INFORMATICA - PUNO" },
    { value: 23, label: "DERECHO - PUNO" },
    { value: 24, label: "INGENIERIA QUIMICA - PUNO" },
    { value: 25, label: "ODONTOLOGIA - PUNO" },
    { value: 26, label: "NUTRICION HUMANA - PUNO" },
    { value: 27, label: "INGENIERIA GEOLOGICA - PUNO" },
    { value: 28, label: "INGENIERIA METALURGICA - PUNO" },
    { value: 29, label: "INGENIERIA CIVIL - PUNO" },
    { value: 30, label: "ARQUITECTURA Y URBANISMO - PUNO" },
    { value: 31, label: "CIENCIAS FISICO MATEMATICAS - PUNO" },
    { value: 32, label: "INGENIERIA AGRICOLA - PUNO" },
    { value: 33, label: "MEDICINA HUMANA - PUNO" },
    { value: 34, label: "INGENIERIA MECANICA ELECTRICA - PUNO" },
    { value: 35, label: "INGENIERIA ELECTRONICA - PUNO" },
    { value: 36, label: "INGENIERIA DE SISTEMAS - PUNO" },
    { value: 37, label: "PSICOLOGIA - PUNO" },
    { value: 38, label: "INGENIERIA ECONOMICA - AZANGARO" },
    { value: 39, label: "INGENIERIA DE MINAS - AZANGARO" },
    { value: 40, label: "INGENIERIA TELECOMUNICACIONES - AZANGARO" },
    { value: 41, label: "CIENCIAS CONTABLES - JULI" },
    { value: 42, label: "ARQUITECTURA Y URBANISMO - JULI" },
    { value: 43, label: "INGENIERIA AGROINDUSTRIAL - JULI" }
]);



getDocumentos()



</script>
