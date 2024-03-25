<template>
  <Head title="Pregunta" />
  <AuthenticatedLayout>
    <div>
      <!-- <pre>{{ preguntas }}</pre> -->


      <Button label="nueva pregunta" severity="info" @click="modal = true" />


      <Dialog v-model:visible="modal" modal header="Nuevo Usuario" :style="{ width: '400px' }">
        <div style="width: 100%;">
          <InputText type="text" v-model="texto" />
        </div>
        <div style="width: 100%;">
          <InputText type="text" v-model="aspecto" />
        </div>
        <div style="width: 100%;">
          <InputText type="text" v-model="id_encuesta" />
        </div>
        <div>
          <Button label="Guardar" severity="info" @click="save()" />
        </div>

      </Dialog>


      <DataTable :value="preguntas" :class="'p-datatable-sm'" paginator :rows="10" tableStyle="min-width: 50rem"
        style="font-size: 0.8rem;">
        <Column field="id" header="id"></Column>
        <Column field="texto" header="texto"></Column>
        <Column field="aspecto" header="aspecto"></Column>
        <Column field="id_encuesta" header="id_encuesta"></Column>

        <Column field="id_programa" header="Acciones" width="90px">
          <template #body="{ data }">
            <div class="flex">
              <div class="mr-2">
                <Button class="secondary" icon="pi pi-pencil" aria-label="Submit" @click="abrirEditar(data)" size="small"
                  style="width: 25px; height: 25px;" />
              </div>
              <Button icon="pi pi-trash" severity="danger" aria-label="Submit" @click="eliminarp(data)"
                size="small" style="width: 25px; height: 25px;" />
            </div>
          </template>
        </Column>

      </DataTable>

    </div>
  </AuthenticatedLayout>
</template>
  
<script setup>

import InputText from 'primevue/inputtext';

import MultiSelect from 'primevue/multiselect';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import AuthenticatedLayout from '@/Layouts/LayoutSuperadmi.vue';
import { Head } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { ref, watch } from 'vue';
import 'primeicons/primeicons.css';
import Dropdown from 'primevue/dropdown';


const texto = ref("");
const aspecto = ref("");
const id = ref(null);
const id_encuesta = ref(null);

const preguntas = ref([]);
const modal = ref(false);



const getPregunta = async () => {
  try {
    const response = await axios.get('get-pregunta');
    preguntas.value = response.data
  } catch (error) {
    // Manejar errores de la solicitud
    console.error('Error al enviar la solicitud POST:', error);
  }
};

const save = async () => {
  try {
    const response = await axios.post('guardarp', { id: id.value, texto: texto.value, aspecto: aspecto.value, id_encuesta: id_encuesta.value });
    // respuesta.value = response.data;
  } catch (error) {
    // Manejar errores de la solicitud
    console.error('Error al enviar la solicitud POST:', error);
  }
  modal.value = false;
  getPregunta();
};


const abrirEditar = (item) => {
    modal.value = true
    id.value = item.id
    texto.value = item.texto
    aspecto.value = item.aspecto
    id_encuesta.value = item.id_encuesta
};


const eliminarp = async (item) => {
    try {
        const response = await axios.get('eliminarp/' + item.id);
    } catch (error) {
        console.error('Error al enviar la solicitud POST:', error);
    }
    getPregunta();
}



getPregunta();

</script>