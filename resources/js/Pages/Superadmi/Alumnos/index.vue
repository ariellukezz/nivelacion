<template>
  <Head title="Importación Masiva de Estudiantes" />

  <AuthenticatedLayout>
    <Toast />
    <div class="p-4 bg-white rounded-lg shadow-xs">
      <div class="card">
        <div class="mb-3">
          <div class="flex justify-between gap-4">
            <div class="flex-1">
              <input
                type="file"
                @change="handleFileUpload"
                accept=".xlsx, .xls"
                class="w-full"
              />
            </div>
            <Button
              label="Importar Estudiantes"
              icon="pi pi-upload"
              :disabled="!excelData.length"
              @click="subirDataEstudiante"
              severity="info"
            />
          </div>
        </div>

        <div v-if="excelData.length">
          <div class="mb-4 p-4 bg-blue-50 rounded-md">
            <p class="font-semibold">Resumen de importación:</p>
            <p>Registros listos: {{ excelData.length }}</p>
            <p v-if="reporte">
              Éxitos: {{ reporte.registros_exitosos }} |
              Duplicados: {{ reporte.duplicados.length }} |
              Errores: {{ reporte.errores.length }}
            </p>
          </div>

          <DataTable
            :value="excelData"
            :class="'p-datatable-sm'"
            tableStyle="min-width: 150rem"
            scrollable
            scrollHeight="500px"
            removableSort
          >
            <!-- DATOS BÁSICOS -->
            <Column field="codigo" header="CODIGO" :sortable="true" frozen />
            <Column field="codigo_est" header="COD. EST" :sortable="true" frozen />
            <Column field="dni" header="DNI" :sortable="true" frozen />
            <Column field="paterno" header="PATERNO" :sortable="true" />
            <Column field="materno" header="MATERNO" :sortable="true" />
            <Column field="nombres" header="NOMBRES" :sortable="true" />
            <Column field="sexo" header="SEXO" :sortable="true" />
            <Column field="email" header="EMAIL" :sortable="true" />
            <Column field="f_nacimiento" header="f_nacimiento" :sortable="true" />
            <Column field="ubigeo_nacimiento" header="UBIGEO NAC." :sortable="true" />
            <Column field="estado_civil" header="EST. CIVIL" :sortable="true" />
            <Column field="anio_egreso" header="AÑO EGRESO" :sortable="true" />
            <Column field="tipo_colegio" header="TIPO COLEGIO" :sortable="true" />
            <Column field="nombre_colegio" header="COLEGIO" :sortable="true" />
            <Column field="ubigeo_colegio" header="UBIGEO COL." :sortable="true" />
            <Column field="apto" header="APTO" :sortable="true" />
            <Column field="direccion" header="DIRECCIÓN" :sortable="true" />
            <Column field="telefono" header="TELÉFONO" :sortable="true" />
            <Column field="id_programa" header="ID PROGRAMA" :sortable="true" />
            <Column field="id_escuela" header="ID ESCUELA" :sortable="true" />
              <Column field="C1" header="C1" :sortable="true" />
              <Column field="C2" header="C2" :sortable="true" />
              <Column field="C3" header="C3" :sortable="true" />
              <Column field="C4" header="C4" :sortable="true" />
              <Column field="C5" header="C5" :sortable="true" />
              <Column field="C6" header="C6" :sortable="true" />
              <Column field="C7" header="C7" :sortable="true" />
              <Column field="C8" header="C8" :sortable="true" />
              <Column field="C9" header="C9" :sortable="true" />
              <Column field="C10" header="C10" :sortable="true" />
              <Column field="C11" header="C11" :sortable="true" />
              <Column field="C1_R" header="C1_R" :sortable="true" />
              <Column field="C2_R" header="C2_R" :sortable="true" />
              <Column field="C3_R" header="C3_R" :sortable="true" />
              <Column field="C4_R" header="C4_R" :sortable="true" />
              <Column field="C5_R" header="C5_R" :sortable="true" />
              <Column field="C6_R" header="C6_R" :sortable="true" />
              <Column field="C7_R" header="C7_R" :sortable="true" />
              <Column field="C8_R" header="C8_R" :sortable="true" />
              <Column field="C9_R" header="C9_R" :sortable="true" />
              <Column field="C10_R" header="C10_R" :sortable="true" />
              <Column field="C11_R" header="C11_R" :sortable="true" />
              <Column field="nivelar" header="NIVELAR" :sortable="true" />
              <Column field="no_nivelar" header="NO_NIVELAR" :sortable="true" />
              <Column field="anio" header="AÑO" :sortable="true" />
              <Column field="semestre" header="SEMESTRE" :sortable="true" />
              <Column field="f_examen" header="F. EXAMEN" :sortable="true" />
              <Column field="t_examen" header="TIPO EXAMEN" :sortable="true" />
              <Column field="puntaje_examen" header="PUNTAJE" :sortable="true" />
              <Column field="modalidad" header="MODALIDAD" :sortable="true" />
              <Column field="observacion" header="OBSERVACIÓN" :sortable="true" />
            <!-- ESTADO -->
            <Column header="ESTADO" bodyClass="text-center" frozen alignFrozen="right">
              <template #body="{ data }">
                <Tag
                  :value="getEstado(data)"
                  :severity="getSeverity(data)"
                />
              </template>
            </Column>
          </DataTable>

          <!-- REPORTE DE DUPLICADOS -->
          <div v-if="reporte?.duplicados?.length" class="mt-6">
            <h3 class="font-bold text-red-600 mb-2">Registros Duplicados</h3>
            <DataTable :value="reporte.duplicados" class="p-datatable-sm">
              <Column field="linea" header="Línea" />
              <Column field="dni" header="DNI" />
              <Column field="nombre" header="Nombre" />
              <Column header="Existe en">
                <template #body="{ data }">
                  <div class="flex gap-2">
                    <Tag v-if="data.tablas.alumnos" value="Alumnos" severity="danger" />
                    <Tag v-if="data.tablas.matriz" value="Matriz" severity="danger" />
                    <Tag v-if="data.tablas.datos_ingreso" value="Ingreso" severity="danger" />
                  </div>
                </template>
              </Column>
            </DataTable>
          </div>

          <!-- REPORTE DE ERRORES -->
          <div v-if="reporte?.errores?.length" class="mt-6">
            <h3 class="font-bold text-red-600 mb-2">Errores en Importación</h3>
            <DataTable :value="reporte.detalle_errores" class="p-datatable-sm">
              <Column field="linea" header="Línea" />
              <Column field="dni" header="DNI" />
              <Column field="error" header="Error" />
            </DataTable>
          </div>
        </div>

        <div v-else class="p-4 text-center text-gray-500 border-2 border-dashed rounded-lg">
          <i class="pi pi-file-excel text-4xl mb-2 text-blue-500"></i>
          <p class="text-lg font-medium">Suba un archivo Excel con datos de estudiantes</p>
          <p class="text-sm mt-1">Formatos soportados: .xlsx, .xls</p>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/LayoutSuperadmi.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import Button from 'primevue/button';
import Toast from 'primevue/toast';
import { useToast } from "primevue/usetoast";
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ColumnGroup from 'primevue/columngroup';
import Tag from 'primevue/tag';
import XLSX from 'xlsx';

// Toast
const toast = useToast();
const showToast = (tipo, titulo, detalle) => {
    toast.add({ severity: tipo, summary: titulo, detail: detalle, life: 3000 });
};

// Estados
const excelData = ref([]);
const reporte = ref(null);
const ignorarDuplicados = ref(false);


// Procesamiento de archivo Excel
const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (e) => {
        try {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: 'array' });
            const firstSheetName = workbook.SheetNames[0];
            const worksheet = workbook.Sheets[firstSheetName];

            // Convertir a JSON con encabezados
            const jsonData = XLSX.utils.sheet_to_json(worksheet);

            // Mapear datos para coincidir con el controlador
            const processedData = jsonData.map(item => ({
                // Datos básicos del estudiante
                codigo: item.codigo || '',
                codigo_est: item.codigo_est || '',
                dni: item.dni || '',
                paterno: item.paterno || '',
                materno: item.materno || '',
                nombres: item.nombres || '',
                sexo: item.sexo || '',
                email: item.email || '',
                f_nacimiento: item.f_nacimiento || '',
                ubigeo_nacimiento: item.ubigeo_nacimiento || '',
                estado_civil: item.estado_civil || '',
                anio_egreso: item.anio_egreso || '',
                tipo_colegio: item.tipo_colegio || '',
                nombre_colegio: item.nombre_colegio || '',
                ubigeo_colegio: item.ubigeo_colegio || '',
                apto: item.apto || '',
                direccion: item.direccion || '',
                telefono: item.telefono || '',
                id_programa: item.id_programa || '',
                id_escuela: item.id_escuela || '',

                // Datos de matriz de notas
                C1: item.C1 || 0,
                C2: item.C2 || 0,
                C3: item.C3 || 0,
                C4: item.C4 || 0,
                C5: item.C5 || 0,
                C6: item.C6 || 0,
                C7: item.C7 || 0,
                C8: item.C8 || 0,
                C9: item.C9 || 0,
                C10: item.C10 || 0,
                C11: item.C11 || 0,
                C1_R: item.C1_R || 0,
                C2_R: item.C2_R || 0,
                C3_R: item.C3_R || 0,
                C4_R: item.C4_R || 0,
                C5_R: item.C5_R || 0,
                C6_R: item.C6_R || 0,
                C7_R: item.C7_R || 0,
                C8_R: item.C8_R || 0,
                C9_R: item.C9_R || 0,
                C10_R: item.C10_R || 0,
                C11_R: item.C11_R || 0,
                nivelar: item.nivelar || 0,
                no_nivelar: item.no_nivelar || 0,

                // Datos de ingreso
                anio: item.anio || new Date().getFullYear(),
                semestre: item.semestre || '--',
                f_examen: item.f_examen || new Date().toISOString().split('-')[0],
                t_examen: item.t_examen || 'Ordinario',
                puntaje_examen: item.puntaje_examen || 0,
                modalidad: item.modalidad || 'Regular',
                observacion: item.observacion || '',

                // Estado inicial
                estado: 'Pendiente'
            }));

            excelData.value = processedData;
            reporte.value = null;
            showToast('success', 'Archivo cargado', `${processedData.length} registros listos para importar`);
        } catch (error) {
            showToast('error', 'Error', 'Formato de archivo no válido');
            console.error(error);
        }
    };
    reader.readAsArrayBuffer(file);
};

// Envío de datos al backend
const subirDataEstudiante = async () => {
    try {
        showToast('info', 'Procesando', 'Iniciando importación de estudiantes...');

        const response = await axios.post('/importar-excel-estudiante', {
            datos: excelData.value,
            ignorar_duplicados: ignorarDuplicados.value
        });

        reporte.value = response.data.data;

        // Mostrar confirmación si hay duplicados y aún no se pidió ignorarlos
        if (reporte.value.duplicados.length && !ignorarDuplicados.value) {
            const confirmar = confirm(
                `Se detectaron ${reporte.value.duplicados.length} duplicados.\n¿Desea continuar ignorándolos e importar el resto?`
            );

            if (confirmar) {
                ignorarDuplicados.value = true;
                await subirDataEstudiante(); // Reintenta
                return;
            }
        }

        showToast('success', 'Importación completada',
            `Éxitos: ${reporte.value.registros_exitosos} |
             Duplicados: ${reporte.value.duplicados.length} |
             Errores: ${reporte.value.errores.length}`);

        // Actualizar estado de los registros en la tabla
        excelData.value = excelData.value.map((item, index) => ({
            ...item,
            estado: getEstadoFromReporte(index + 1, reporte.value)
        }));

    } catch (error) {
        let errorMessage = 'Error desconocido';

        if (error.response) {
            errorMessage = error.response.data.message || error.response.statusText;

            if (error.response.data.reporte) {
                reporte.value = error.response.data.reporte;

                excelData.value = excelData.value.map((item, index) => ({
                    ...item,
                    estado: getEstadoFromReporte(index + 1, reporte.value)
                }));
            }
        }

        showToast('error', 'Error en importación', errorMessage);
        console.error('Error en importación:', error);
    }
};


// Funciones auxiliares para estados
const getEstadoFromReporte = (linea, reporte) => {
    if (!reporte) return 'Pendiente';

    // Verificar si es duplicado
    const duplicado = reporte.duplicados?.find(d => d.linea === linea);
    if (duplicado) {
        return 'Duplicado';
    }

    // Verificar si tiene error
    if (reporte.errores?.includes(linea)) {
        return 'Error';
    }

    // Verificar en detalle de errores (por si hay mensaje específico)
    const errorDetalle = reporte.detalle_errores?.find(e => e.linea === linea);
    if (errorDetalle) {
        return 'Error';
    }

    // Si pasó todas las validaciones y no está en errores
    if (linea <= reporte.registros_exitosos) {
        return 'Éxito';
    }

    return 'Pendiente';
};

const getEstado = (data) => {
    return data.estado || 'Pendiente';
};

const getSeverity = (data) => {
    switch (getEstado(data)) {
        case 'Éxito': return 'success';
        case 'Error': return 'danger';
        case 'Duplicado': return 'warning';
        default: return 'info';
    }
};

// Función para descargar reporte de errores
const descargarReporteErrores = () => {
    if (!reporte.value?.detalle_errores?.length) return;

    try {
        // Crear hoja de cálculo
        const ws = XLSX.utils.json_to_sheet(reporte.value.detalle_errores);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Errores");

        // Generar archivo y descargar
        const fecha = new Date().toISOString().slice(0, 10);
        XLSX.writeFile(wb, `errores_importacion_${fecha}.xlsx`);

        showToast('success', 'Reporte descargado', 'Se ha descargado el reporte de errores');
    } catch (error) {
        showToast('error', 'Error', 'No se pudo generar el reporte de errores');
        console.error(error);
    }
};
</script>
