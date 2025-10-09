<template>
    <Head title="Documentos" />

    <AuthenticatedLayout>
        <Toast />
        <div class="p-4 bg-white rounded-lg shadow-xs">
            <div class="card">
                <Message severity="warn" sticky>
                    Sube todos los documentos necesarios. La aprobación es obligatoria para la ratificación mediante Resolución Rectoral. Sin aprobación, no habrá conformidad. Estén atentos al estado del documento; les informaremos si fue aprobado o no. Podrán ver las observaciones en caso de haberlas.
                </Message>
            </div>
            <div class="card">
                <TabView>
                    <TabPanel header="(1) R.D. Comisión">
                        <div class="mb-3">
                            <form @submit.prevent="submit">
                                <div class="flex justify-between items-center">
                                    <input type="file" @change="onChange" />
                                    <div class="flex items-center space-x-2">
                                        <Button label="Descargar Reglamento" @click="descargar('nivelacion_ingresantes.pdf')" outlined style="height:38px;" />

                                        <div v-if="botonResolucion.habilitado">
                                            <Button label="Subir Documento" icon="pi pi-upload" @click="submit" />
                                        </div>
                                        <p v-else class="text-sm text-red-600">Módulo desactivado</p>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <p v-if="botonResolucion.tiempoRestante > 0 && botonResolucion.habilitado" class="text-sm text-gray-600 mt-2 text-right">
                            Tiempo restante: {{ formatoContador(botonResolucion.tiempoRestante) }}
                        </p>

                        <ProgressBar :value="progress" class="my-4"></ProgressBar>

                        <div class="card">
                            <Message :closable="false" style="color: rgb(139, 62, 62)">Cargar la Resolución Decanal de la Comisión de Nivelación, conforme al Artículo 7 del Reglamento General de Nivelación de Ingresantes para Pregrado, en formato PDF (un solo archivo).</Message>
                        </div>

                        <div>
                            <DataTable :value="resoluciones" :class="'p-datatable-sm'" tableStyle="min-width: 50rem">
                                <Column field="nombre" header="Nombre"></Column>
                                <Column field="fecha" header="Fecha"></Column>
                                <Column header="Estado">
                                  <template #body="{ data }">
                                    <Tag :severity="data.aceptado === 1 ? 'success' : data.aceptado === 0 ? 'danger' : 'secondary'"
                                         :value="data.aceptado === 1 ? 'Aceptado' : data.aceptado === 0 ? 'Rechazado' : 'Pendiente'"></Tag>
                                  </template>
                                </Column>
                                <Column field="obser" header="Observaciones"></Column>
                                <Column header="Acciones">
                                  <template #body="{ data }">
                                    <Button icon="pi pi-eye" severity="info" @click="verdocumento(data)" size="small" style="width: 25px; height: 25px;" />
                                  </template>
                                </Column>
                            </DataTable>
                        </div>
                    </TabPanel>

                    <TabPanel header="(2) Plan de Trabajo">
                        <div class="mb-3">
                            <form @submit.prevent="submit2">
                                <div class="flex justify-between items-center">
                                    <input type="file" @change="onChange2" />
                                    <div class="flex items-center space-x-2">
                                        <Button label="Descargar esquema" @click="descargar('esquema.pdf')" outlined style="height:38px;" />

                                        <div v-if="botonPlan.habilitado">
                                            <Button label="Subir Documento" icon="pi pi-upload" @click="submit2" />
                                        </div>
                                        <p v-else class="text-sm text-red-600">Módulo desactivado</p>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <p v-if="botonPlan.tiempoRestante > 0 && botonPlan.habilitado" class="text-sm text-gray-600 mt-2 text-right">
                            Tiempo restante: {{ formatoContador(botonPlan.tiempoRestante) }}
                        </p>

                        <ProgressBar :value="progress" class="my-4"></ProgressBar>

                        <div class="card">
                            <Message :closable="false" style="color: rgb(139, 62, 62)">Cargar la Resolución Decanal del Plan de Trabajo de Actividades de Nivelación, conforme al Artículo 14 del Reglamento General de Nivelación de Ingresantes para Pregrado, en formato PDF (un solo archivo).</Message>
                        </div>

                        <div>
                            <DataTable :value="planes" :class="'p-datatable-sm'" tableStyle="min-width: 50rem">
                                <Column field="nombre" header="Nombre"></Column>
                                <Column field="fecha" header="Fecha"></Column>
                                <Column header="Estado">
                                  <template #body="{ data }">
                                    <Tag :severity="data.aceptado === 1 ? 'success' : data.aceptado === 0 ? 'danger' : 'secondary'"
                                         :value="data.aceptado === 1 ? 'Aceptado' : data.aceptado === 0 ? 'Rechazado' : 'Pendiente'"></Tag>
                                  </template>
                                </Column>
                                <Column field="obser" header="Observaciones"></Column>
                                <Column header="Acciones">
                                  <template #body="{ data }">
                                    <Button icon="pi pi-eye" severity="info" @click="verdocumento(data)" size="small" style="width: 25px; height: 25px;" />
                                  </template>
                                </Column>
                            </DataTable>
                        </div>
                    </TabPanel>

                    <TabPanel header="(3) Informe">
                        <div class="mb-3">
                            <form @submit.prevent="submit3">
                                <div class="flex justify-between items-center">
                                    <input type="file" @change="onChange3" />
                                    <div class="flex items-center space-x-2">
                                        <Button label="Descargar ejemplo" @click="descargar('ejemplo.docx')" outlined style="height:38px;" />

                                        <div v-if="botonInforme.habilitado">
                                            <Button label="Subir Documento" icon="pi pi-upload" @click="submit3" />
                                        </div>
                                        <p v-else class="text-sm text-red-600">Módulo desactivado</p>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <p v-if="botonInforme.tiempoRestante > 0 && botonInforme.habilitado" class="text-sm text-gray-600 mt-2 text-right">
                            Tiempo restante: {{ formatoContador(botonInforme.tiempoRestante) }}
                        </p>

                        <ProgressBar :value="progress" class="my-4"></ProgressBar>

                        <div class="card">
                            <Message :closable="false" style="color: rgb(139, 62, 62)">Cargar los informes de evaluación y los resultados del proceso de nivelación, conforme a los Artículos 21 y 24 del Reglamento General de Nivelación de Ingresantes para Pregrado, en formato .RAR o .ZIP.</Message>
                        </div>

                        <div>
                           <DataTable :value="informes" :class="'p-datatable-sm'" tableStyle="min-width: 50rem">
                                <Column field="nombre" header="Nombre"></Column>
                                <Column field="fecha" header="Fecha"></Column>
                                <Column header="Estado">
                                  <template #body="{ data }">
                                    <Tag :severity="data.aceptado === 1 ? 'success' : data.aceptado === 0 ? 'danger' : 'secondary'"
                                         :value="data.aceptado === 1 ? 'Aceptado' : data.aceptado === 0 ? 'Rechazado' : 'Pendiente'"></Tag>
                                  </template>
                                </Column>
                                <Column field="obser" header="Observaciones"></Column>
                                <Column header="Acciones">
                                  <template #body="{ data }">
                                    <Button icon="pi pi-eye" severity="info" @click="verdocumento(data)" size="small" style="width: 25px; height: 25px;" />
                                  </template>
                                </Column>
                            </DataTable>
                        </div>
                    </TabPanel>

                    <TabPanel header="(4) R.D Felicitación Docentes">
                        <div class="mb-3">
                            <form @submit.prevent="submit5">
                                <div class="flex justify-between items-center">
                                    <input type="file" @change="onChange5" />
                                    <div class="flex items-center space-x-2">
                                        <Button label="Descargar ejemplo" @click="descargar('FORMATO-DOCENTES.xlsx')" outlined style="height:38px;" />

                                        <div v-if="botonDictantes.habilitado">
                                            <Button label="Subir Documento" icon="pi pi-upload" @click="submit5" />
                                        </div>
                                        <p v-else class="text-sm text-red-600">Módulo desactivado</p>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <p v-if="botonDictantes.tiempoRestante > 0 && botonDictantes.habilitado" class="text-sm text-gray-600 mt-2 text-right">
                            Tiempo restante: {{ formatoContador(botonDictantes.tiempoRestante) }}
                        </p>

                        <ProgressBar :value="progress" class="my-4"></ProgressBar>

                        <div class="card">
                            <Message :closable="false" style="color: rgb(139, 62, 62)">Cargar la Resolución Decanal de felicitación de los docentes para su ratificación con Resolución Rectoral, conforme al Artículo 25 del Reglamento General de Nivelación de Ingresantes para Pregrado, en formato PDF.</Message>
                        </div>

                        <div>
                           <DataTable :value="dictantes" :class="'p-datatable-sm'" tableStyle="min-width: 50rem">
                                <Column field="nombre" header="Nombre"></Column>
                                <Column field="fecha" header="Fecha"></Column>
                                <Column header="Estado">
                                  <template #body="{ data }">
                                    <Tag :severity="data.aceptado === 1 ? 'success' : data.aceptado === 0 ? 'danger' : 'secondary'"
                                         :value="data.aceptado === 1 ? 'Aceptado' : data.aceptado === 0 ? 'Rechazado' : 'Pendiente'"></Tag>
                                  </template>
                                </Column>
                                <Column field="obser" header="Observaciones"></Column>
                                <Column header="Acciones">
                                  <template #body="{ data }">
                                    <Button icon="pi pi-eye" severity="info" @click="verdocumento(data)" size="small" style="width: 25px; height: 25px;" />
                                  </template>
                                </Column>
                            </DataTable>
                        </div>
                    </TabPanel>

                    <TabPanel header="Complemento">
                        <div class="mb-3">
                            <form @submit.prevent="submit4">
                                <div class="flex justify-between items-center">
                                    <input type="file" @change="onChange4" />
                                    <div class="flex items-center space-x-2">
                                        <div v-if="botonOtros.habilitado">
                                            <Button label="Subir Documento" icon="pi pi-upload" @click="submit4" />
                                        </div>
                                        <p v-else class="text-sm text-red-600">Módulo desactivado</p>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <p v-if="botonOtros.tiempoRestante > 0 && botonOtros.habilitado" class="text-sm text-gray-600 mt-2 text-right">
                            Tiempo restante: {{ formatoContador(botonOtros.tiempoRestante) }}
                        </p>

                        <ProgressBar :value="progress" class="my-4"></ProgressBar>

                        <div class="card">
                            <Message :closable="false" style="color: rgb(139, 62, 62)">Subir en formato .RAR ó .ZIP (un solo Archivo Comprimido). No obligatorio.</Message>
                        </div>

                        <div>
                           <DataTable :value="otros" :class="'p-datatable-sm'" tableStyle="min-width: 50rem">
                                <Column field="nombre" header="Nombre"></Column>
                                <Column field="fecha" header="Fecha"></Column>
                                <Column header="Estado">
                                  <template #body="{ data }">
                                    <Tag :severity="data.aceptado === 1 ? 'success' : data.aceptado === 0 ? 'danger' : 'secondary'"
                                         :value="data.aceptado === 1 ? 'Aceptado' : data.aceptado === 0 ? 'Rechazado' : 'Pendiente'"></Tag>
                                  </template>
                                </Column>
                                <Column field="obser" header="Observaciones"></Column>
                                <Column header="Acciones">
                                  <template #body="{ data }">
                                    <Button icon="pi pi-eye" severity="info" @click="verdocumento(data)" size="small" style="width: 25px; height: 25px;" />
                                  </template>
                                </Column>
                            </DataTable>
                        </div>
                    </TabPanel>
                </TabView>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

  <script setup>
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
  import { Head } from '@inertiajs/vue3';
  import { ref, onMounted  } from 'vue';
  import axios from 'axios';
  import Button from 'primevue/button';
  import TabView from 'primevue/tabview';
  import TabPanel from 'primevue/tabpanel';
  import Toast from 'primevue/toast';
  import { useToast } from "primevue/usetoast";
  import DataTable from 'primevue/datatable';
  import Column from 'primevue/column';
  import Tag from 'primevue/tag';
  import Message from 'primevue/message';
  import ProgressBar from 'primevue/progressbar';

  const baseUrl = window.location.origin;

  const otemp = ref({});
  const levantarobservacion = (item) => {
    otemp.value = item;
  }

  const pagina = ref(1)
  const resoluciones = ref([])
  const planes = ref([])
  const informes = ref([])
  const dictantes = ref([])
  const otros = ref([])

  const toast = useToast();

  const showToast = (tipo, titulo, detalle) => {
    toast.add({ severity: tipo, summary: titulo, detail: detalle, life: 3000 });
  };

















  // --- INICIA EL NUEVO BLOQUE DE LÓGICA ---

// 1. Estados individuales para cada botón.
const botonResolucion = ref({ habilitado: false, tiempoRestante: 0 });
const botonPlan = ref({ habilitado: false, tiempoRestante: 0 });
const botonInforme = ref({ habilitado: false, tiempoRestante: 0 });
const botonDictantes = ref({ habilitado: false, tiempoRestante: 0 });
const botonOtros = ref({ habilitado: false, tiempoRestante: 0 });

const intervalo = ref(null);

// 2. Esta función se ejecuta cuando la página carga
onMounted(() => {
  // Pide el estado de CADA botón al servidor
  axios.get('/estado-boton/Resolucion').then(res => { botonResolucion.value = res.data; });
  axios.get('/estado-boton/Plan').then(res => { botonPlan.value = res.data; });
  axios.get('/estado-boton/Informe').then(res => { botonInforme.value = res.data; });
  axios.get('/estado-boton/Dictantes').then(res => { botonDictantes.value = res.data; });
  axios.get('/estado-boton/Otros').then(res => { botonOtros.value = res.data; });

  // Inicia un solo temporizador que actualiza todos los contadores
  intervalo.value = setInterval(actualizarContadores, 1000);
});

onMounted(() => {
  clearInterval(intervalo.value);
});

// 3. Función que actualiza TODOS los contadores a la vez
function actualizarContadores() {
    if (botonResolucion.value.tiempoRestante > 0) botonResolucion.value.tiempoRestante--;
    if (botonPlan.value.tiempoRestante > 0) botonPlan.value.tiempoRestante--;
    if (botonInforme.value.tiempoRestante > 0) botonInforme.value.tiempoRestante--;
    if (botonDictantes.value.tiempoRestante > 0) botonDictantes.value.tiempoRestante--;
    if (botonOtros.value.tiempoRestante > 0) botonOtros.value.tiempoRestante--;
}

// 4. Función para dar formato de HH:mm:ss al tiempo
const formatoContador = (segundos) => {
  if (isNaN(segundos) || segundos === null) return '00:00:00';
  const horas = Math.floor(segundos / 3600);
  const minutos = Math.floor((segundos % 3600) / 60);
  const segundosRestantes = segundos % 60;
  return `${horas.toString().padStart(2, '0')}:${minutos.toString().padStart(2, '0')}:${segundosRestantes.toString().padStart(2, '0')}`;
};

// --- FIN DEL NUEVO BLOQUE DE LÓGICA ---




  const imagen = ref(null);
  const imagen2 = ref(null);
  const imagen3 = ref(null);
  const imagen4 = ref(null);
  const imagen5 = ref(null);

  const progress = ref(0);

  const onChange = (e) => {
    console.log("Selected file", e.target.files[0])
    imagen.value = e.target.files[0];
  }

  const onChange2 = (e) => {
    console.log("Selected file", e.target.files[0])
    imagen2.value = e.target.files[0];
  }
  const onChange3 = (e) => {
    console.log("Selected file", e.target.files[0])
    imagen3.value = e.target.files[0];
  }

  const onChange4 = (e) => {
    console.log("Selected file", e.target.files[0])
    imagen4.value = e.target.files[0];
  }
  const onChange5 = (e) => {
    console.log("Selected file", e.target.files[0])
    imagen5.value = e.target.files[0];
  }

  const submit = async () => {
    let fd = new FormData();
    fd.append('img', imagen.value);

    const config = {
      onUploadProgress: progressEvent => {
        progress.value = Math.round((progressEvent.loaded * 100) / progressEvent.total);
      }
    };

    await axios.post("/documento/resolucion", fd, config)
      .then(res => {
        showToast("success", "Documento guardado", res.data.menssje);
        progress.value = 0; // Reset progress after completion
        getResoluciones();
      })
      .catch(err => console.log(err));
  }

  const submit2 = async () => {
    let fd = new FormData();
    fd.append('img', imagen2.value);

    const config = {
      onUploadProgress: progressEvent => {
        progress.value = Math.round((progressEvent.loaded * 100) / progressEvent.total);
      }
    };

    await axios.post("/documento/plan", fd, config)
      .then(res => {
        showToast("success", "Documento guardado", res.data.menssje);
        progress.value = 0; // Reset progress after completion
        getPlanes();
      })
      .catch(err => console.log(err));
  }

  const submit3 = async () => {
    let fd = new FormData();
    fd.append('img', imagen3.value);

    const config = {
      onUploadProgress: progressEvent => {
        progress.value = Math.round((progressEvent.loaded * 100) / progressEvent.total);
      }
    };

    await axios.post("/documento/informe", fd, config)
      .then(res => {
        showToast("success", "Archivo guardado", res.data.menssje);
        progress.value = 0; // Reset progress after completion
        getInformes();
      })
      .catch(err => console.log(err));
  }

  const submit4 = async () => {
    let fd = new FormData();
    fd.append('img', imagen4.value);

    const config = {
      onUploadProgress: progressEvent => {
        progress.value = Math.round((progressEvent.loaded * 100) / progressEvent.total);
      }
    };

    await axios.post("/documento/otros", fd, config)
      .then(res => {
        showToast("success", "Archivo guardado", res.data.menssje);
        progress.value = 0; // Reset progress after completion
        getOtros();
      })
      .catch(err => console.log(err));
  }

  const submit5 = async () => {
    let fd = new FormData();
    fd.append('img', imagen5.value);

    const config = {
      onUploadProgress: progressEvent => {
        progress.value = Math.round((progressEvent.loaded * 100) / progressEvent.total);
      }
    };

    await axios.post("/documento/dictantes", fd, config)
      .then(res => {
        showToast("success", "Archivo guardado", res.data.menssje);
        progress.value = 0; // Reset progress after completion
        getDictantes();
      })
      .catch(err => console.log(err));
  }

  const getResoluciones = async () => {
    let res = await axios.post(
      "get-resoluciones?page=" + pagina.value,
      { term: "" }
    );
    resoluciones.value = res.data.datos.data;
  }
  const getPlanes = async () => {
    let res = await axios.post(
      "get-planes?page=" + pagina.value,
      { term: "" }
    );
    planes.value = res.data.datos.data;
  }
  const getInformes = async () => {
    let res = await axios.post(
      "get-informes?page=" + pagina.value,
      { term: "" }
    );
    informes.value = res.data.datos.data;
  }

  const getOtros = async () => {
    let res = await axios.post(
      "get-otros?page=" + pagina.value,
      { term: "" }
    );
    otros.value = res.data.datos.data;
  }

  const getDictantes = async () => {
    let res = await axios.post(
      "get-dictantes?page=" + pagina.value,
      { term: "" }
    );
    dictantes.value = res.data.datos.data;
  }

  const verdocumento = (data) => {
    window.open(data.url, '_blank', 'fullscreen=yes'); return false;
  }

  const descargar = (nombre) => {
    window.open(baseUrl + '/documentos/' + nombre);
  }

  getResoluciones();
  getPlanes();
  getInformes();
  getDictantes();
  getOtros();

  </script>

  <style scoped>
  input[type=file]::file-selector-button {
    margin-right: 10px;
    border: none;
    background: var(--primary-color);
    padding: 7px 20px;
    border-radius: 5px;
    color: #fff;
    cursor: pointer;
    transition: background .2s ease-in-out;
  }

  input[type=file]::file-selector-button:hover {
    background: var(--primary-600);
  }
  </style>
