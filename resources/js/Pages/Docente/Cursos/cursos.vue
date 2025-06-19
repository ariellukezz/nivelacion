<template>
<Head title="Cursos"/>
<AuthenticatedLayout>
    <!-- HEADER BODY -->
    <div class="flex" style="background:white; border-bottom: 1px solid #CDCDCD;">
      <Button severity="secondary" style="font-size: 0.9rem"  text @click="Inicio"> Inicio </Button>
      <div v-if="cursoseleccionado !== null" class="flex justify-content-center" style="align-items:center;">
        <i class="pi pi-angle-right " />
        <Button severity="secondary" @click="resEsuela" style="font-size: 0.9rem" text>
          <div style=" max-width: 180px; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
              <span> {{ cursoseleccionado.nombre }} </span>
          </div>
        </Button>
      </div>
    </div>
    <!-- END HEADER BODY -->

  <div class="bg-white shadow-xs p-4" style=" height: calc(100vh - 110px); font-family: Arial, Helvetica, sans-serif;">
    <Toast />
    <!-- CURSOS  1 -->
    <div v-if="cursoseleccionado === null">
        <div>
          <div class="flex" style="justify-content: flex-end;">
            <span class="p-input-icon-left ">
                <i class="pi pi-search" />
                <InputText v-model="buscar" style="padding-left: 40px; height: 40px;" placeholder="Search" />
            </span>
          </div>
        </div>

        <div>
          <div class="card" >
            <DataTable
              selectionMode="single"
              v-model:selection="cursoseleccionado"
              :value="cursos"
              :class="'p-datatable-sm mt-4'"
              tableStyle="min-width: 50rem"
              style="font-size: .9rem;">

                <Column field="nombre" header="Curso"></Column>
                <Column field="grupo" header="Grupo"></Column>
                <Column field="escuela" header="Escuela"></Column>
                <Column field="programa" header="programa"></Column>

                <Column field="estado" style=" justify-content: center; display: flex;" header="Estado" width="70px">
                    <template #body="{ data }">
                        <div class="flex" style="justify-content: center;">
                        <div v-if="data.estado === 1">
                            <Tag severity="info" value="Activo"></Tag>
                        </div>
                        <div v-if="data.estado === 0">
                            <Tag :style="{ background: '#CDCDCD' }" value="Inactivo"></Tag>
                        </div>
                        </div>
                    </template>
                </Column>

                <Column field="id_programa" header="Acciones" width="90px">
                  <template #body="{ data }">
                    <div class="flex">
                      <Button class="" severity="help" icon="pi pi-eye" aria-label="Submit" @click="editar(data)" size="small" style="width: 25px; height: 25px;" />
                    </div>
                  </template>
                </Column>
            </DataTable>
          </div>
        </div>
    </div>
    <!-- END PASO 1 -->

    <!--- PASO 2 -->
    <div v-if="cursoseleccionado != null">
        <div>
          <div class="flex mb-3" style="justify-content: space-between;">
            <div class="flex align-items-center">
                <Button
                    @click="descargarPDF()"
                    label="Descargar Acta PDF"
                    icon="pi pi-file-pdf"
                    severity="help"
                    class="mr-4"
                />
                <Divider layout="vertical" />
                <Button
                    @click="guardarNotas()"
                    label="Guardar Notas"
                    icon="pi pi-save"
                    :disabled="Object.keys(notasEditadas).length === 0"
                    class="ml-4"
                    severity="success"
                />
            </div>
            <span class="p-input-icon-left ">
                <i class="pi pi-search" />
                <InputText v-model="buscarAlumno" style="padding-left: 40px; height: 40px;" placeholder="Buscar" />
            </span>
          </div>
        </div>

        <div class="card p-fluid">
            <DataTable
                :value="alumnosCurso"
                :class="'p-datatable-sm mt-4'"
                selectionMode="single"
                v-model:selection="alumnoseleccionado"
                editMode="cell"
                @cell-edit-init="onCellEditInit"
                tableStyle="min-width: 50rem">

                <Column field="codigo_est" header="Código" sortable></Column>

                <Column header="Nombre Completo" sortable>
                    <template #body="{ data }">
                        {{ data.nombres }}, {{ data.paterno }} {{ data.materno }}
                    </template>
                </Column>

                <Column field="semestre" header="Ingreso" sortable></Column>

                <Column field="ant" header="Nota Admisión" sortable>
                    <template #body="{ data }">
                        <div class="flex justify-center">
                            {{ data.ant || '-' }}
                        </div>
                    </template>
                </Column>

                <Column field="nota" header="Nota Final" sortable>
                    <template #editor="{ data }">
                        <InputNumber
                            style="width:100px;"
                            v-model="notasEditadas[data.id]"
                            inputId="minmaxfraction"
                            :max="20"
                            :min="0"
                            :maxFractionDigits="2"
                            autofocus
                        />
                    </template>
                    <template #body="{ data }">
                        <div v-if="notasEditadas[data.id] !== undefined">
                            {{ Number(notasEditadas[data.id]).toFixed(2) }}
                        </div>
                        <div v-else-if="data.nota === null || data.nota === '' || data.nota === 0">
                            -
                        </div>
                        <div v-else>
                            {{ Number(data.nota).toFixed(2) }}
                        </div>
                    </template>
                </Column>

                <Column header="Condición">
                    <template #body="{ data }">
                        <div class="flex justify-center">
                            <Tag v-if="notasEditadas[data.id] !== undefined"
                                :severity="notasEditadas[data.id] >= 10.5 ? 'info' : 'danger'"
                                :value="notasEditadas[data.id] >= 10.5 ? 'Aprobado' : 'Desaprobado'">
                            </Tag>
                            <Tag v-else
                                :severity="data.condicion ? 'info' : 'danger'"
                                :value="data.condicion ? 'Aprobado' : 'Desaprobado'">
                            </Tag>
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>
    </div>
    <!-- END PASO 2-->
  </div>
</AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/LayoutDocente.vue';
import { Head } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { ref, watch } from 'vue'
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber'
import Toast from 'primevue/toast';
import { useToast } from "primevue/usetoast";
import Tag from 'primevue/tag';
import Divider from 'primevue/divider';

const toast = useToast();

const pagina = ref(1);
const buscar = ref("")
const cursos = ref([])
const cursoseleccionado = ref(null)
const alumnoseleccionado = ref(null)
const alumnosCurso = ref([])
const buscarAlumno = ref("")
const notasEditadas = ref({}) // Almacena las notas editadas temporalmente

// Obtener cursos
const getCursos = async () => {
  let res = await axios.post(
    "/docente/get-cursos?page=" + pagina.value,
    { term: buscar.value }
  );
  cursos.value = res.data.datos.data;
}

// Obtener alumnos por curso
const getAlumnosXcurso = async () => {
  let res = await axios.post(
    "/docente/get-alumnos-curso?page=" + pagina.value,
    {
      term: buscarAlumno.value,
      curso: cursoseleccionado.value.id
    }
  );
  alumnosCurso.value = res.data.datos.data;
  notasEditadas.value = {}; // Limpiar notas editadas al cambiar de curso
}

// Volver al inicio
const Inicio = () => {
  cursoseleccionado.value = null;
}

// Descargar PDF
const descargarPDF = async () => {
  window.open("/docente/generar-pdf/"+cursoseleccionado.value.id, '_blank');
}

// Cuando se inicia la edición de una celda
const onCellEditInit = (event) => {
  const { data, field } = event;
  if (field === 'nota' && notasEditadas.value[data.id] === undefined) {
    notasEditadas.value[data.id] = data.nota !== null ? parseFloat(data.nota) : null;
  }
};

// Guardar todas las notas editadas
const guardarNotas = async () => {
  if (Object.keys(notasEditadas.value).length === 0) {
    showToast('warn', 'Advertencia', 'No hay cambios para guardar');
    return;
  }

  try {
    // Crear array de promesas para todas las actualizaciones
    const promises = Object.entries(notasEditadas.value).map(([id, nota]) => {
      return axios.post("/docente/update-nota", {
        id_detallecurso: id,
        nota: nota
      });
    });

    // Ejecutar todas las actualizaciones
    await Promise.all(promises);

    showToast('success', 'Éxito', 'Notas guardadas correctamente');
    getAlumnosXcurso(); // Refrescar los datos

  } catch (error) {
    showToast('error', 'Error', 'Ocurrió un error al guardar las notas');
    console.error("Error al guardar notas:", error);
  }
};

// Mostrar notificación
const showToast = (tipo, titulo, detalle) => {
  toast.add({ severity: tipo, summary: titulo, detail: detalle, life: 3000 });
};

// Watchers
watch(buscar, (newValue, oldValue) => { getCursos() });
watch(cursoseleccionado, (newValue, oldValue) => {
  if (newValue) getAlumnosXcurso()
});
watch(buscarAlumno, (newValue, oldValue) => { getAlumnosXcurso() });

// Columnas de la tabla de alumnos
const columns = ref([
    { field: 'codigo_est', header: 'Código' },
    {
        field: 'nombre_completo',
        header: 'Nombre Completo',
        body: (data) => `${data.nombres} ${data.paterno} ${data.materno}`
    },
    { field: 'semestre', header: 'Ingreso' }
]);

// Cargar datos iniciales
getCursos();
</script>
