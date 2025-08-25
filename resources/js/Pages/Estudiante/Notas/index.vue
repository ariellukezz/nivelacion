<template>
<Head title="Notas"/>
<AuthenticatedLayout>
    <div class="card">
    <Message severity="warn">
    Si todavía no aparece el nombre del docente, significa que la coordinación de nivelacion de ingresantes aún no lo ha asignado.
    En caso de desaprobar la <strong>Nivelación de Ingresantes</strong>, deberás asistir nuevamente a la próxima
    nivelación que se realiza al inicio de cada semestre académico.
    Te recomendamos estar atento al <strong>cronograma académico oficial</strong>.
    </Message>
  </div>

    <div class="bg-white shadow-xs p-4" style=" height: calc(100vh - 110px); ">
        <DataTable
            :showGridlines= "false"
            :value="notas"
            :class="'p-datatable-sm'"
            tableStyle="min-width: 50rem"
            style="font-size: .9rem;"
            >
            <Column field="nombre" header="CURSO"></Column>

            <Column field="nombres" header="NOMBRE DEL DOCENTE ENCARGADO">
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

            <Column field="grupo" header="GRUPO"></Column>
            <Column field="escuela" header="PROGRAMA DE ESTUDIO"></Column>
            <Column field="nota" header="NOTA"></Column>
            <Column field="id_programa" header="CONDICIÓN" width="90px">
                <template #body="{ data }">
                    <div class="flex" v-if="data.nota >= 10.5 && data.nota <= 20 " >
                        <Tag severity="info" value="Aprobado"></Tag>
                    </div>
                    <div class="flex" v-if="data.nota >= 0 &&  data.nota < 10.5" >
                        <Tag severity="danger" value="Reprobado"></Tag>
                    </div>

                </template>
            </Column>

        </DataTable>
    </div>








</AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/LayoutEstudiante.vue';
import { Head } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Tag from 'primevue/tag';
import { ref } from 'vue'
import Message from 'primevue/message';

const notas = ref([])
const pagina = ref(1)

const getNotas =  async (event) => {
  let res = await axios.post("get-notas?page=" + pagina.value);
  notas.value = res.data.datos.data;
}


getNotas()
</script>
