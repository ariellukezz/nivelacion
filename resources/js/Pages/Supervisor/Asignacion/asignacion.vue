<template>
    <Head title="Documentos" />
    <AuthenticatedLayout>
        <div class="flex justify-between">

        <div class="flex" style="width: 100%; justify-content: space-between;">
            <div class="mb-3" style="width: 240px;">

              <Dropdown v-model="programa" :options="programasselect" filter optionLabel="label" optionValue="value"  placeholder="Seleccione escuela profesional" style="width:100%;" class="w-full md:w-11rem">
                <template #option="slotProps">
                    <div class="flex align-items-center" style="width: 400px; font-size:0.9rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                        <div>{{ slotProps.option.label }}</div>
                    </div>
                </template>
              </Dropdown>
            </div>
        </div>


            <span class="p-input-icon-left">
                <i class="pi pi-search" />
                <InputText v-model="buscar" placeholder="Search" />
            </span>
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
const totalRegistros = ref(0);
const documentos = ref([]);
const pageSize = ref(10);
watch(buscar, (newValue, oldValue) => { getDocumentos(); })
watch(programa, (newValue, oldValue) => { getDocumentos(); })
watch(pageSize, (newValue, oldValue) => { getDocumentos(); })


const getDocumentos = async () => {
    let res = await axios.post("get-documentos?page=" + pagina.value, { term: buscar.value, paginashoja: pageSize.value, programa: programa.value });
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

const programasselect = ref([
        {value:1, label:"INGENIERIA AGRONOMICA"},
        {value:2, label:"INGENIERIA AGROINDUSTRIAL"},
        {value:3, label:"INGENIERIA TOPOGRAFICA Y AGRIMENSURA"},
        {value:4, label:"MEDICINA VETERINARIA Y ZOOTECNIA"},
        {value:5, label:"INGENIERIA ECONOMICA"},
        {value:6, label:"CIENCIAS CONTABLES"},
        {value:7, label:"ADMINISTRACION"},
        {value:8, label:"TRABAJO SOCIAL"},
        {value:9, label:"ENFERMERIA"},
        {value:10, label:"INGENIERIA DE MINAS"},
        {value:11, label:"HUMANIDADES"},
        {value:12, label:"SOCIOLOGIA"},
        {value:13, label:"TURISMO"},
        {value:14, label:"ANTROPOLOGIA"},
        {value:15, label:"CIENCIAS DE LA COMUNICACION SOCIAL"},
        {value:16, label:"ARTE"},
        {value:17, label:"BIOLOGIA"},
        {value:18, label:"EDUCACION SECUNDARIA"},
        {value:19, label:"EDUCACION PRIMARIA"},
        {value:20, label:"EDUCACION INICIAL"},
        {value:21, label:"EDUCACION FISICA"},
        {value:22, label:"INGENIERIA ESTADISTICA E INFORMATICA"},
        {value:23, label:"DERECHO"},
        {value:24, label:"INGENIERIA QUIMICA"},
        {value:25, label:"ODONTOLOGIA"},
        {value:26, label:"NUTRICION HUMANA"},
        {value:27, label:"INGENIERIA GEOLOGICA"},
        {value:28, label:"INGENIERIA METALURGICA"},
        {value:29, label:"INGENIERIA CIVIL"},
        {value:30, label:"ARQUITECTURA Y URBANISMO"},
        {value:31, label:"CIENCIAS FISICO MATEMATICAS"},
        {value:32, label:"INGENIERIA AGRICOLA"},
        {value:33, label:"MEDICINA HUMANA"},
        {value:34, label:"INGENIERIA MECANICA ELECTRICA"},
        {value:35, label:"INGENIERIA ELECTRONICA"},
        {value:36, label:"INGENIERIA DE SISTEMAS"},
        {value:37, label:"PSICOLOGIA"},

  ])


getDocumentos()



</script>
