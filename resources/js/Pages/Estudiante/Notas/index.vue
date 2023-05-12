<template>
<Head title="Notas"/>
<AuthenticatedLayout>
    <div class="bg-white shadow-xs p-4" style=" height: calc(100vh - 110px); ">
        <DataTable 
            :showGridlines= "false"
            :value="notas" 
            :class="'p-datatable-sm'"  
            tableStyle="min-width: 50rem" 
            style="font-size: .9rem;"
            >
            <Column field="nombre" header="CURSO"></Column>
            <Column field="grupo" header="GRUPO"></Column>
            <Column field="escuela" header="ESCUELA"></Column>
            <Column field="nota" header="NOTA"></Column>
            <Column field="id_programa" header="Condición" width="90px"> 
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

const notas = ref([])
const pagina = ref(1)

const getNotas =  async (event) => {
  let res = await axios.post("get-notas?page=" + pagina.value);
  notas.value = res.data.datos.data;
}


getNotas()
</script>