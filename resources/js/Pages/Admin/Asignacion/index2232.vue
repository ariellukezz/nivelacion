<template>
  <Head title="Nivelación"/>
  <AuthenticatedLayout>
  <!-- <pre>{{ alumnosregistro }}</pre> -->

  <div class="flex mb-0" style="justify-content: space-between; align-items:center; margin-top:0px; border-bottom:solid 1px #cdcdcd9D; height:50px; background:white; ">
      <div class="flex">
        <Button severity="secondary" style="font-size: 0.9rem"  text @click="Inicio"> Inicio </Button>
        <div v-if="escuela !== null" class="flex justify-content-center" style="align-items:center;">
          <i class="pi pi-angle-right " />
          <Button  severity="secondary" @click="resEsuela" style="font-size: 0.9rem" text>
            <div style=" max-width: 180px; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                <span> {{ escuela.escuela }} </span>
            </div>
          </Button>
        </div>

        <div v-if="cursoseleccionado !== null" class="flex justify-content-center" style="align-items:center;">
          <i class="pi pi-angle-right " />
          <Button  severity="secondary" style="font-size: 0.9rem" text>
            <div style=" max-width: 180px; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                <span> {{ cursoseleccionado.nombre }} </span>
            </div>
          </Button>
        </div>
      </div>

      <div v-if="escuela === null">
        <div class="flex mr-4" style="justify-content: flex-end;">
          <span class="p-input-icon-left">
              <i class="pi pi-search" />
              <InputText v-model="buscarescuela" style="padding-left: 40px; height: 40px;" placeholder="Buscar" />
          </span>
        </div>
      </div>

      <div v-if="escuela !== null && cursoseleccionado === null">
        <Dropdown
          v-model="competencia"
          :options="competencias"
          severity="primary"
          optionLabel="label"
          optionValue="value"
          placeholder="Selecciona una competencia"
          style="width:325px; height:38px"
          class="w-full md:w-11rem mr-4">
          <template #option="slotProps">
              <div class="flex align-items-center" style="width: 280px; font-size:0.9rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                  <div>{{ slotProps.option.label }}</div>
              </div>
          </template>
        </Dropdown>
      </div>
  </div>

  <div class="bg-white shadow-xs p-4" style=" height: calc(100vh - 140px); font-family: Arial, Helvetica, sans-serif;">
      <!--- PASO 1-->
      <!-- {{ escuela }} -->
      <div>
        <div v-if="escuela === null" class="card">
          <DataTable
            v-model:selection="escuela"
            selectionMode="single"
            :value="escuelas"
            :class="'p-datatable-sm'"
            tableStyle="min-width: 50rem"
            style="font-size: .9rem;"
            :paginator="true" :rows="10" :filters="filters"
            >
              <Column field="filial" header="Ubicación"></Column>
              <Column field="escuela" header="Escuela"></Column>
              <Column field="facultad" header="Facultad"></Column>
              <Column field="area" header="Area"></Column>
          </DataTable>
        </div>
      </div>
      <!--- END PASO 1-->

      <!--- PASO 2 -->
      <div v-if="escuela !== null && cursoseleccionado === null">

        <div class="flex" style="justify-content: space-between;">
          <Button severity="primary" @click="abrirNuevoCurso" style="height:40px"> Nuevo Curso </Button>
          <div>
            <div class="flex mb-3" style="justify-content: flex-end;">
              <span class="p-input-icon-left">
                  <i class="pi pi-search" />
                  <InputText v-model="buscarcurso" style="padding-left: 40px; height: 40px;" placeholder="Buscar" />
              </span>
            </div>
          </div>

        </div>

        <!-- {{ cursoseleccionado }} -->
        <div class="card" >
          <!-- <Message severity="warn" sticky>Debe crearse un solo curso por competencia. En caso de múltiples docentes,
            uno asume la responsabilidad de ingresar notas tras coordinar con los demás, calcular el promedio de las notas finales del curso. Este docente de preferencia de la escuela profesional o,
             si no, el docente de servicio.
          </Message> -->
        </div>
        <div class="mt-3" >
          <DataTable
            v-model:selection="cursoseleccionado"
            selectionMode="single"
            :value="cursos"
            :class="'p-datatable-sm'"
            tableStyle="min-width: 50rem"
            style="font-size: .9rem;"
            v-model:first="cursoFirst" :paginator="true" :rows="6"
            >
                <Column field="nombre" header="Nombre del Curso"></Column>
                <Column field="competencia" header="(Competencia) Curso"></Column>
                <Column field="docente" header="Docente" style="min-width: 210px;">
                    <template #body="{ data }">
                        <div class="flex flex-column gap-2" style="align-items: flex-start;">
                            <span v-if="data.docente" style="font-size: 0.85rem;">
                                {{ data.docente }}
                            </span>
                            <span v-else style="font-size: 0.85rem; color: #777;">
                                Sin docente asignado
                            </span>

                            <Button
                                :label="data.id_docente ? 'Cambiar docente' : 'Asignar docente'"
                                icon="pi pi-user-edit"
                                size="small"
                                severity="secondary"
                                outlined
                                @click.stop="abrirAsignarDocente(data)"
                            />
                        </div>
                    </template>
                </Column>
                <Column field="grupo" header="Grupo"></Column>
                <Column field="programa" header="Programa"></Column>
                <Column field="escuela" header="Escuela Prof."></Column>


                <Column field="estado" header="Lista" style="text-align: center;">
                    <template #body="{ data }">
                        <div class="flex" style="justify-content: center;">
                        <div v-if="data.estado === 1">
                            <!-- <Button  @click.stop="descargarPDF(data.id)" label="Generar PDF" /> -->
                            <Button class="secondary" severity="success" icon="pi pi-print" aria-label="Submit" @click.stop="descargarPDF(data.id)" size="small" style="width: 25px; height: 25px;"/>

                        </div>
                        <div v-else>
                            <Button disabled class="secondary" severity="success" icon="pi pi-print" aria-label="Submit" @click.stop="descargarPDF(data.id)" size="small" style="width: 25px; height: 25px;"/>
                        </div>
                        </div>
                    </template>
                    </Column>

                    <Column field="estado" header="Estado" style="text-align: center;">
                    <template #body="{ data }">
                        <div class="flex" style="justify-content: center;">
                        <div v-if="data.estado === 1">
                            <Tag severity="info" value="Activo"></Tag>
                        </div>
                        <div v-else>
                            <Tag :style="{ background: '#CDCDCD' }" value="Inactivo"></Tag>
                        </div>
                        </div>
                    </template>
                </Column>
                <Column field="id_programa" header="Acciones" width="90px">
                  <template #body="{ data }">
                    <div class="flex">
                      <div class="mr-2">
                        <Button class="secondary" icon="pi pi-pencil" aria-label="Submit" @click.stop="editar(data)" size="small" style="width: 25px; height: 25px;"/>
                      </div>
                      <Button icon="pi pi-trash" severity="danger" aria-label="Submit" @click.stop="confirm2($event, data)"  size="small"  style="width: 25px; height: 25px;"/>
                    </div>
                  </template>
                </Column>
          </DataTable>
        </div>

      </div>

      <!-- END PASO 2 -->
      <!--- PASO 3 -->
      <!-- {{cursoseleccionado}} -->
      <div v-if="escuela !== null && cursoseleccionado !== null">

        <div class="flex" style="justify-content: space-between;">
          <Button severity="primary" @click="abrirseleccionar()" style="height:40px">Seleccionar Alumnos</Button>
          <div>
            <div class="flex mb-3" style="justify-content: flex-end;">
              <span class="p-input-icon-left">
                  <i class="pi pi-search" />
                  <InputText v-model="buscar" style="padding-left: 40px; height: 40px;" placeholder="Buscar" />
              </span>
            </div>
          </div>
        </div>

        <!-- {{ cursoseleccionado }} -->
        <div class="mt-3" >

        <DataTable
          selectionMode="single"
          :value="detalle_curso"
          :class="'p-datatable-sm'"
          tableStyle="min-width: 50rem"
          style="font-size: .9rem;"
          :paginator="true" :rows="9"
          >
            <Column field="codigo_est" header="Codigo"></Column>
            <!--bdhh <Column field="dni" header="DNI"></Column> -->
            <Column field="semestre" header="Ingreso"></Column>
            <Column field="programa" header="Programa"></Column>
            <Column field="nombres" header="Nombres"></Column>
            <Column field="paterno" header="Paterno"></Column>
            <Column field="materno" header="Materno"></Column>
            <Column field="curso" header="Curso"></Column>
            <Column field="nota_actual" header="Nota Actual" style="text-align:center;">
              <template #body="{ data }">
                <strong>{{ data.nota_actual ?? '-' }}</strong>
              </template>
            </Column>
            <Column field="nota" header="Nueva Nota" style="text-align:center;">
              <template #body="{ data }">
                <strong>{{ data.nota ?? '-' }}</strong>
              </template>
            </Column>
            <Column field="estado" style=" justify-content: center; display: flex;" header="Condición" width="70px">
            <template #body="{ data }">
              <div class="flex" style="justify-content: center;">
                <div v-if="data.nota >= 10.50">
                    <Tag severity="info" value="Aprobado"></Tag>
                </div>
                <div v-if="data.nota <= 10.49">
                    <Tag severity="danger" value="Desprobado"></Tag>
                </div>
              </div>
            </template>
            </Column>
          </DataTable>
        </div>
        </div>

        <!-- END PASO 3 -->

      <Toast />
      <ConfirmPopup></ConfirmPopup>

      <!--- MODAL -->
      <Dialog v-model:visible="visible" modal :header="!curso.id?'Curso nuevo':'Editar Curso'" :style="{ width: '750px' }">

        <!-- {{ curso }} {{ cursocompetencia }} -->
        <!-- <pre>{{ docente }}</pre> -->
        <!-- <div class="card" >
          <Message severity="warn" sticky>llenar los datos completos (grupo solo para escuelas con progama de estudio)
          </Message>
        </div> -->


        <!-- DESDE AQUI EMPIEZA PARA PODER EDITAR -->
        <div class="flex mt-0 mb-3 align-items-center" style="justify-content: flex-end;" >
            <label>Estado</label>
            <div class="ml-3"> <InputSwitch v-model="curso.estado" /></div>
        </div>

        <div class="flex" style="width: 100%; justify-content: space-between;">
            <div class="mb-3" style="width: 68%;">
              <div><label>Nombre del Curso.</label></div>
              <InputText style="width: 100%; height: 40px;"  type="text" v-model="curso.nombre" />
            </div>

            <div class="mb-3" style="width: 28%;">
              <div><label>Grupo</label></div>
              <Dropdown v-model="curso.grupo" :options="grupos" optionLabel="label" optionValue="value"  placeholder="Selecciona una competencia" style="width:100%;" class="w-full md:w-11rem">
                <template #option="slotProps">
                    <div class="flex align-items-center" style=" font-size:0.9rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                        <div>{{ slotProps.option.label }}</div>
                    </div>
                </template>
              </Dropdown>
            </div>
        </div>

        <div class="flex" style="width: 100%; justify-content: space-between;">
            <div class="mb-3" style="width: 100%;">
              <div><label>Competencia</label></div>
              <Dropdown v-model="cursocompetencia" :options="competencias" optionLabel="label" optionValue="value"  placeholder="Seleccione una competencia" style="width:100%;" class="w-full md:w-11rem">
                <template #option="slotProps">
                    <div class="flex align-items-center" style="width: 600px; font-size:0.9rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                        <div>{{ slotProps.option.label }}</div>
                    </div>
                </template>
              </Dropdown>
            </div>
        </div>

        <div class="flex" style="width: 100%; justify-content: space-between;">
            <div class="mb-3" style="width: 100%;">
              <div><label>Programa de estudio</label></div>
                <Dropdown v-model="prog" :options="programas" filter optionLabel="label" optionValue="value"  placeholder="Seleccione un programa de estudio" style="width:100%;" class="w-full md:w-11rem">
                <template #option="slotProps">
                    <div class="flex align-items-center" style="width: 600px; font-size:0.9rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                        <div>{{ slotProps.option.label }}</div>
                    </div>
                </template>
              </Dropdown>
            </div>
        </div>
        <!-- TERMINA AQUI PARA LA EDICION DEL CAMPO EDITAR -->



        <!-- AQUI EMPIEZA SOLO PARA SOLO VISUALIZACION -->
<!--
        <div class="flex mt-0 mb-3 align-items-center" style="justify-content: flex-end;">
        <label>Estado</label>
        <div class="ml-3">
            <InputSwitch :modelValue="curso.estado" disabled />
        </div>
        </div>

        <div class="flex" style="width: 100%; justify-content: space-between;">
        <div class="mb-3" style="width: 68%;">
            <div><label>Nombre del Curso.</label></div>

            <InputText
            style="width: 100%; height: 40px;"
            type="text"
            v-model="curso.nombre"
            readonly
            disabled
            />
        </div>

        <div class="mb-3" style="width: 28%;">
            <div><label>Grupo</label></div>
            <Dropdown
            v-model="curso.grupo"
            :options="grupos"
            optionLabel="label"
            optionValue="value"
            placeholder="Selecciona una competencia"
            style="width:100%;"
            class="w-full md:w-11rem"
            disabled
            >
            <template #option="slotProps">
                <div
                class="flex align-items-center"
                style="font-size:0.9rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;"
                >
                <div>{{ slotProps.option.label }}</div>
                </div>
            </template>
            </Dropdown>
        </div>
        </div>

        <div class="flex" style="width: 100%; justify-content: space-between;">
        <div class="mb-3" style="width: 100%;">
            <div><label>Competencia</label></div>
            <Dropdown
            v-model="cursocompetencia"
            :options="competencias"
            optionLabel="label"
            optionValue="value"
            placeholder="Seleccione una competencia"
            style="width:100%;"
            class="w-full md:w-11rem"
            disabled
            >
            <template #option="slotProps">
                <div
                class="flex align-items-center"
                style="width: 600px; font-size:0.9rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;"
                >
                <div>{{ slotProps.option.label }}</div>
                </div>
            </template>
            </Dropdown>
        </div>
        </div>

        <div class="flex" style="width: 100%; justify-content: space-between;">
        <div class="mb-3" style="width: 100%;">
            <div><label>Programa de estudio</label></div>
            <Dropdown
            v-model="prog"
            :options="programasselect"
            filter
            optionLabel="label"
            optionValue="value"
            placeholder="Seleccione un programa de estudio"
            style="width:100%;"
            class="w-full md:w-11rem"
            disabled
            >
            <template #option="slotProps">
                <div
                class="flex align-items-center"
                style="width: 600px; font-size:0.9rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;"
                >
                <div>{{ slotProps.option.label }}</div>
                </div>
            </template>
            </Dropdown>
        </div>
        </div> -->

         <!-- AQUI TERMINA SOLO PARA SOLO VISUALIZACION -->


          <div class="flex" style="width: 100%; justify-content: space-between;">
            <div class="mb-3" style="width: 100%;">
            <label style="font-weight: bold;">ASIGNAR DOCENTE</label>
              <Dropdown v-model="curso.id_docente" :options="docentes2" filter optionLabel="nombres" optionValue="id"  placeholder="Selecciona un docente" style="width:100%;" class="w-full md:w-11rem">
                  <template #option="slotProps">
                      <div class="flex align-items-center" style="width: 600px; font-size:0.9rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                          <div>{{ slotProps.option.nombres }}</div>
                      </div>
                  </template>
              </Dropdown>
            </div>
          </div>

          <template #footer>
            <div class="flex" style="justify-content: flex-end;">
              <div>
                <Button label="Cancelar" outlined="" @click="visible = false" size="small" />
              </div>
              <Button label="Guardar" @click="guardar" size="small"/>
            </div>
          </template>

      </Dialog>

      <!--- END MODAL -->

      <!-- MODAL ASIGNAR / CAMBIAR DOCENTE -->
      <Dialog
        v-model:visible="modal_docente"
        modal
        header="Asignar docente"
        :style="{ width: '650px' }"
      >
        <div v-if="cursoDocente" class="mb-3">
          <div style="font-size: 0.9rem; color: #666;">Curso</div>
          <div style="font-weight: bold;">{{ cursoDocente.nombre }}</div>
          <div style="font-size: 0.85rem; margin-top: 4px;">{{ cursoDocente.competencia }}</div>
        </div>

        <div class="mb-3">
          <label style="font-weight: bold;">Docente</label>
          <Dropdown
            v-model="docenteAsignar"
            :options="docentes2"
            filter
            optionLabel="nombres"
            optionValue="id"
            placeholder="Seleccione un docente"
            style="width:100%; margin-top:6px;"
            class="w-full md:w-11rem"
          >
            <template #option="slotProps">
              <div
                class="flex align-items-center"
                style="width: 520px; font-size:0.9rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;"
              >
                <div>{{ slotProps.option.nombres }}</div>
              </div>
            </template>
          </Dropdown>
        </div>

        <template #footer>
          <div class="flex" style="justify-content: flex-end; gap: 8px;">
            <Button label="Cancelar" outlined @click="modal_docente = false" size="small" />
            <Button
              label="Guardar docente"
              icon="pi pi-check"
              @click="guardarDocenteAsignado"
              :loading="guardandoDocente"
              size="small"
            />
          </div>
        </template>
      </Dialog>


      <!--- MODAL ASIGNACION -->

            <!--- MODAL -->
      <Dialog v-model:visible="modal_registro" modal header="Asignar Alumnos" :style="{ width: '900px' }">
        <!-- {{ programasAsignacion }} -->
        <div class="flex" style="width: 100%; justify-content: space-between;">
    <div class="mb-3" style="width: 100%;">
        <div>
            <label>Programa de estudio</label>
        </div>

        <Dropdown
            v-model="progselection"
            :options="programasAsignacion"
            filter
            optionLabel="label"
            optionValue="value"
            placeholder="Seleccione un programa de estudio"
            style="width:100%;"
            class="w-full md:w-11rem"
        >
            <template #option="slotProps">
                <div
                    class="flex align-items-center"
                    style="width: 400px; font-size:0.9rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;"
                >
                    <div>{{ slotProps.option.label }}</div>
                </div>
            </template>
        </Dropdown>
    </div>
</div>

        <div v-if="alumnosregistro">
            <!-- {{ alumnosregistro }} -->

          <DataTable
            v-model:selection="alumnos_seleccionados_registro"
            selectionMode="multiple"
            dataKey="id"
            :metaKeySelection="false"
            :row-selection="false"
            :value="alumnosregistro"
            :class="'p-datatable-sm'"
            tableStyle="min-width: 50rem"
            style="font-size: .9rem;"
            :paginator="true"
            :rows="10"
          >
              <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
              <Column field="programa" header="Programa"></Column>
              <Column field="nota_actual" header="Nota Actual" style="text-align:center;">
                <template #body="{ data }">
                  <strong>{{ data.nota_actual ?? '-' }}</strong>
                </template>
              </Column>
              <Column field="codigo_est" header="Codigo"></Column>
              <!--bdhh <Column field="dni" header="DNI"></Column> -->
              <Column field="semestre" header="Ingreso"></Column>
              <Column field="nombres" header="Nombres"></Column>
              <Column field="paterno" header="Paterno"></Column>
              <Column field="materno" header="Materno"></Column>
          </DataTable>
        </div>
        <div class="flex" style="width: 100%; justify-content: flex-end;">
          <Button severity="primary" style="font-size: 0.9rem"  text @click="asignar"> Asignar </Button>
        </div>

      </Dialog>

  </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dialog from 'primevue/dialog';
import Dropdown from 'primevue/dropdown';
import InputSwitch from 'primevue/inputswitch';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';
import ConfirmPopup from 'primevue/confirmpopup';
import Tag from 'primevue/tag';
import Message from 'primevue/message';

const toast = useToast();
const confirm = useConfirm();

const escuela = ref(null);
const escuelas = ref([]);
const cursos = ref([]);
const cursoseleccionado = ref(null);
const detalle_curso = ref([]);

const competencias = ref([]);
const competencia = ref(null);
const cursocompetencia = ref(null);

// IMPORTANTE:
// "programas" se usa SOLO para Nuevo/Editar Curso y se carga desde /get-mis-programas.
// "programasAsignacion" se usa SOLO para la modal Asignar Alumnos.
const programas = ref([]);
const programasAsignacion = ref([]);
const prog = ref(null);
const progselection = ref(null);

const docentes2 = ref([]);
const docente2 = ref(null);

const visible = ref(false);
const modal_registro = ref(false);
const modal_docente = ref(false);
const cargandoEdicion = ref(false);
const guardandoDocente = ref(false);
const cursoDocente = ref(null);
const docenteAsignar = ref(null);

const buscarcurso = ref('');
const buscarescuela = ref('');
const buscar = ref('');

const pagina = ref(1);
const cursoFirst = ref(0);

const alumnosregistro = ref([]);
const alumnos_seleccionados_registro = ref([]);
const seleccionadosTemp = ref([]);

const diferenciaAB = ref([]);
const diferenciaBA = ref([]);

const filters = ref({});

const grupos = ref([
    { value: 'A', label: 'Grupo A' },
    { value: 'B', label: 'Grupo B' },
    { value: 'C', label: 'Grupo C' },
    { value: 'D', label: 'Grupo D' },
    { value: 'E', label: 'Grupo E' },
]);

const curso = ref({
    id: null,
    nombre: '',
    id_docente: null,
    id_programa: null,
    grupo: 'A',
    estado: true,
});

// Permite trabajar tanto con respuestas paginadas {datos:{data:[]}}
// como con respuestas directas {datos:[]}.
const obtenerLista = (response) => {
    if (Array.isArray(response?.datos)) return response.datos;
    if (Array.isArray(response?.datos?.data)) return response.datos.data;
    return [];
};

const showToast = (tipo, titulo, detalle) => {
    toast.add({
        severity: tipo || 'info',
        summary: titulo || 'Información',
        detail: detalle || '',
        life: 3000,
    });
};

const mostrarError = (error, mensaje = 'Ocurrió un error') => {
    console.error(error);
    const detalle = error?.response?.data?.message || mensaje;
    showToast('error', 'Error', detalle);
};

const getMisProgramas = async () => {
    try {
        const res = await axios.post('/get-mis-programas', { term: '' });
        programas.value = obtenerLista(res.data);
        return programas.value;
    } catch (error) {
        programas.value = [];
        mostrarError(error, 'No se pudieron cargar los programas de estudio');
        return [];
    }
};

const getProgramasEscuela = async () => {
    if (!escuela.value?.id) {
        programasAsignacion.value = [];
        return [];
    }

    try {
        const res = await axios.post('/get-programas-escuela?page=' + pagina.value, {
            term: '',
            id_escuela: escuela.value.id,
        });

        programasAsignacion.value = obtenerLista(res.data);
        return programasAsignacion.value;
    } catch (error) {
        programasAsignacion.value = [];
        mostrarError(error, 'No se pudieron cargar los programas de la escuela');
        return [];
    }
};

const getEscuelas = async () => {
    try {
        const res = await axios.post('/get-escuelas', {
            term: buscarescuela.value,
        });
        escuelas.value = obtenerLista(res.data);
    } catch (error) {
        escuelas.value = [];
        mostrarError(error, 'No se pudieron cargar las escuelas');
    }
};

const getCompetencias = async () => {
    try {
        const res = await axios.post('/coordinador/get-competencias', {
            term: '',
        });
        competencias.value = obtenerLista(res.data);
        return competencias.value;
    } catch (error) {
        competencias.value = [];
        mostrarError(error, 'No se pudieron cargar las competencias');
        return [];
    }
};

const getDocenteXcompetencia = async (idCompetencia = cursocompetencia.value) => {
    if (!idCompetencia) {
        docentes2.value = [];
        docente2.value = null;
        return [];
    }

    try {
        const res = await axios.post('/coordinador/get-docente-competencia', {
            term: '',
            competencia: idCompetencia,
        });

        docentes2.value = obtenerLista(res.data);
        docente2.value = docentes2.value[0]?.id ?? null;
        return docentes2.value;
    } catch (error) {
        docentes2.value = [];
        docente2.value = null;
        mostrarError(error, 'No se pudieron cargar los docentes de la competencia');
        return [];
    }
};

const getCursos = async () => {
    if (!escuela.value?.escuela) {
        cursos.value = [];
        return [];
    }

    try {
        const res = await axios.post('/coordinador/get-cursos', {
            term: buscarcurso.value,
            competencia: competencia.value,
            escuela: escuela.value.escuela,
        });

        cursos.value = obtenerLista(res.data);
        return cursos.value;
    } catch (error) {
        cursos.value = [];
        mostrarError(error, 'No se pudieron cargar los cursos');
        return [];
    }
};

const getDetalleCurso = async () => {
    if (!cursoseleccionado.value?.id) {
        detalle_curso.value = [];
        alumnos_seleccionados_registro.value = [];
        seleccionadosTemp.value = [];
        return;
    }

    try {
        const res = await axios.post('/coordinador/get-detalle-curso', {
            term: '',
            curso: cursoseleccionado.value.id,
        });

        detalle_curso.value = obtenerLista(res.data);
        alumnos_seleccionados_registro.value = Array.isArray(res.data?.registrados?.data)
            ? res.data.registrados.data
            : [];
        seleccionadosTemp.value = [...alumnos_seleccionados_registro.value];
    } catch (error) {
        detalle_curso.value = [];
        alumnos_seleccionados_registro.value = [];
        seleccionadosTemp.value = [];
        mostrarError(error, 'No se pudo cargar el detalle del curso');
    }
};

const getAlumnosRegistros = async () => {
    if (!escuela.value?.id || !cursoseleccionado.value?.id_competencia || !progselection.value) {
        alumnosregistro.value = [];
        return;
    }

    try {
        const res = await axios.post('/get-alumnos-registro', {
            term: '',
            escuela: escuela.value.id,
            curso: cursoseleccionado.value.id_competencia,
            programa: progselection.value,
        });

        alumnosregistro.value = Array.isArray(res.data?.datos)
            ? res.data.datos
            : obtenerLista(res.data);
    } catch (error) {
        alumnosregistro.value = [];
        mostrarError(error, 'No se pudieron cargar los alumnos');
    }
};

const limpiar = () => {
    cursocompetencia.value = null;
    prog.value = null;
    docentes2.value = [];
    docente2.value = null;

    curso.value = {
        id: null,
        nombre: '',
        id_docente: null,
        id_programa: null,
        grupo: 'A',
        estado: true,
    };
};

const abrirNuevoCurso = async () => {
    cargandoEdicion.value = true;
    limpiar();

    // Cargar siempre los catálogos antes de mostrar la modal.
    await Promise.all([
        getCompetencias(),
        getMisProgramas(),
    ]);

    cargandoEdicion.value = false;
    visible.value = true;
};

const editar = async (item) => {
    cargandoEdicion.value = true;
    limpiar();

    try {
        // Primero recuperamos las listas necesarias para que los Dropdown
        // puedan encontrar sus optionValue al asignar el registro.
        await Promise.all([
            getCompetencias(),
            getMisProgramas(),
        ]);

        curso.value.id = item.id;
        curso.value.nombre = item.nombre ?? '';
        curso.value.grupo = item.grupo ?? 'A';
        curso.value.estado = Number(item.estado) === 1;
        curso.value.id_programa = item.id_programa ?? null;

        prog.value = item.id_programa ?? null;
        cursocompetencia.value = item.id_competencia ?? null;

        // Después de conocer la competencia cargamos sus docentes y recién
        // entonces recuperamos el docente que estaba guardado.
        await getDocenteXcompetencia(item.id_competencia);
        curso.value.id_docente = item.id_docente ?? null;

        visible.value = true;
    } finally {
        cargandoEdicion.value = false;
    }
};

const abrirAsignarDocente = async (item) => {
    cursoDocente.value = item;
    docenteAsignar.value = item.id_docente ?? null;

    // La lista de docentes depende de la competencia del curso.
    await getDocenteXcompetencia(item.id_competencia);

    // Recuperar el docente actual después de cargar las opciones del Dropdown.
    docenteAsignar.value = item.id_docente ?? null;
    modal_docente.value = true;
};

const guardarDocenteAsignado = async () => {
    if (!cursoDocente.value) return;

    if (!docenteAsignar.value) {
        showToast('warn', 'Falta docente', 'Seleccione un docente para asignar al curso.');
        return;
    }

    guardandoDocente.value = true;

    try {
        const item = cursoDocente.value;

        const res = await axios.post('/coordinador/save-curso', {
            id: item.id,
            nombre: item.nombre,
            id_competencia: item.id_competencia,
            id_docente: docenteAsignar.value,
            escuela: item.escuela,
            grupo: item.grupo,
            estado: Number(item.estado) === 1,
            id_programa: item.id_programa,
        });

        showToast(res.data.tipo, res.data.titulo, 'Docente asignado correctamente.');
        await getCursos();
        modal_docente.value = false;
        cursoDocente.value = null;
        docenteAsignar.value = null;
    } catch (error) {
        mostrarError(error, 'No se pudo asignar el docente');
    } finally {
        guardandoDocente.value = false;
    }
};

const guardar = async () => {
    if (!escuela.value?.escuela) {
        showToast('warn', 'Falta escuela', 'Seleccione una escuela profesional.');
        return;
    }

    try {
        const res = await axios.post('/coordinador/save-curso', {
            id: curso.value.id,
            nombre: curso.value.nombre,
            id_competencia: cursocompetencia.value,
            id_docente: curso.value.id_docente,
            escuela: escuela.value.escuela,
            grupo: curso.value.grupo,
            estado: curso.value.estado,
            id_programa: prog.value,
        });

        showToast(res.data.tipo, res.data.titulo, res.data.mensaje);
        await getCursos();
        visible.value = false;
    } catch (error) {
        mostrarError(error, 'No se pudo guardar el curso');
    }
};

const compararArrays = () => {
    diferenciaAB.value = alumnos_seleccionados_registro.value.filter((objeto1) => {
        return !seleccionadosTemp.value.some((objeto2) => objeto2.id === objeto1.id);
    });
};

const compararArrays2 = () => {
    diferenciaBA.value = seleccionadosTemp.value.filter((objeto1) => {
        return !alumnos_seleccionados_registro.value.some((objeto2) => objeto2.id === objeto1.id);
    });
};

const asignar = async () => {
    compararArrays();
    compararArrays2();

    try {
        const res = await axios.post('/coordinador/asignar-curso-nivelacion', {
            curso: cursoseleccionado.value.id,
            alumnos: alumnos_seleccionados_registro.value,
            anteriores: seleccionadosTemp.value,
            diferencia: diferenciaAB.value,
            diferencia2: diferenciaBA.value,
        });

        showToast(res.data.tipo, res.data.titulo, res.data.mensaje);
        await getDetalleCurso();
        modal_registro.value = false;
    } catch (error) {
        mostrarError(error, 'No se pudo realizar la asignación');
    }
};

const eliminarcurso = async (id) => {
    try {
        const res = await axios.get('/coordinador/delete-curso/' + id);
        showToast(res.data.tipo, res.data.titulo, res.data.mensaje);
        await getCursos();
    } catch (error) {
        mostrarError(error, 'No se pudo eliminar el curso');
    }
};

const confirm2 = (event, doc) => {
    confirm.require({
        target: event.currentTarget,
        message: '¿Estas seguro de eliminar el curso ' + doc.nombre + '?',
        icon: 'pi pi-info-circle',
        acceptClass: 'p-button-danger',
        accept: () => eliminarcurso(doc.id),
        reject: () => {
            toast.add({
                severity: 'error',
                summary: 'Eliminación cancelada',
                detail: 'Se ha cancelado la eliminación del curso ' + doc.nombre,
                life: 3000,
            });
        },
    });
};

const abrirseleccionar = async () => {
    progselection.value = null;
    alumnosregistro.value = [];
    await getProgramasEscuela();
    modal_registro.value = true;
};

const Inicio = () => {
    escuela.value = null;
};

const resEsuela = async () => {
    cursoseleccionado.value = null;
    detalle_curso.value = [];
    alumnosregistro.value = [];
    progselection.value = null;
    await getCursos();
};

const descargarPDF = (id) => {
    window.open('/coordinador/generar-pdf/' + id, '_self');
};

// ------------------------- WATCHERS -------------------------

watch(visible, (newValue) => {
    if (!newValue) {
        cargandoEdicion.value = true;
        limpiar();
        cargandoEdicion.value = false;
    }
});

watch(escuela, async (newValue) => {
    cursoseleccionado.value = null;
    competencia.value = null;
    cursos.value = [];
    detalle_curso.value = [];
    alumnosregistro.value = [];
    alumnos_seleccionados_registro.value = [];
    seleccionadosTemp.value = [];
    progselection.value = null;
    cursoFirst.value = 0;

    if (newValue) {
        await Promise.all([
            getCursos(),
            getMisProgramas(),
        ]);
    }
});

watch(buscarescuela, () => {
    getEscuelas();
});

watch(buscarcurso, () => {
    if (escuela.value) getCursos();
});

watch(competencia, () => {
    cursoFirst.value = 0;
    if (escuela.value) getCursos();
});

watch(cursocompetencia, async (newValue) => {
    if (cargandoEdicion.value) return;

    curso.value.id_docente = null;
    docentes2.value = [];

    if (!newValue) {
        if (!curso.value.id) curso.value.nombre = '';
        return;
    }

    await getDocenteXcompetencia(newValue);

    // En Nuevo Curso mantenemos el comportamiento original:
    // el nombre se completa con el nombre de la competencia.
    // En Editar NO se pisa el nombre existente.
    if (!curso.value.id) {
        const comp = competencias.value.find((item) => item.value === newValue);
        if (comp) curso.value.nombre = comp.label;
    }
});

watch(cursoseleccionado, async (newValue) => {
    if (!newValue) return;
    await getDetalleCurso();
});

watch(progselection, async (newValue) => {
    if (!newValue) {
        alumnosregistro.value = [];
        return;
    }

    if (cursoseleccionado.value) {
        await getAlumnosRegistros();
    }
});

// ------------------------- CARGA INICIAL -------------------------
// No llamar getProgramas() y getMisProgramas() a la vez.
// Eso era una condición de carrera: ambas funciones escribían programas.value.
getCompetencias();
getEscuelas();
getMisProgramas();

</script>
