<template>
<Head title="Encuestas"/>
<AuthenticatedLayout>   
  <div class="gallery">

    <Card v-for="item in cursos" :key="item.id" :class="'card-item'">
      <template #content>
      <div class="card-content" style="text-align: center;">
        <h1 style="font-size: 1rem;">{{ item.nombre }}</h1>
        <span style="font-size: .7rem;"> Encuesta de satisfacción </span>
        <div class="flex justify-center mt-2 mb-4">
          <hr style="width: 50px; background: tomato; height: 4px;">
        </div>
      </div>
      <div style="scale: .9;" class="mt-2">
          <Button v-if="item.encuesta === 0" disabled label="Empezar" style="width: 100%;"/>
          <Button v-else label="Empezar" @click="abrirModal(item.id)" style="width: 100%;"/>
        </div>
    </template>
    </Card>
  </div>
  <Toast />

  <div>
    <Dialog v-model:visible="modalEncuesta" modal header="Encuesta Alumno" :style="{ width: '50vw' }">
      <div>
          <!-- {{ respuestas }} -->
          <!-- <pre>{{ preguntas }}</pre>-->
            <table>
              <thead>
                <tr>
                  <th style="border: solid 1px rebeccapurple;" colspan="1" rowspan="2">Preguntas</th>
                  <th style="border: solid 1px rebeccapurple;" colspan="4" rowspan="1"><div class="mt-2 mb-2">Categorias</div></th>
                </tr>
                <tr style="border: solid 1px rebeccapurple;">
                  <th style="border: solid 1px rebeccapurple;" colspan="1" rowspan="Muy insatisfecho"><div class="pl-2 pr-2"> Muy insatisfecho </div></th>
                  <th style="border: solid 1px rebeccapurple;" colspan="1" rowspan="Insatisfecho"><div class="pl-2 pr-2"> Insatisfecho </div></th>
                  <th style="border: solid 1px rebeccapurple;" colspan="1" rowspan="Satisfecho"><div class="pl-2 pr-2"> Satisfecho </div></th>
                  <th style="border: solid 1px rebeccapurple;" colspan="1" rowspan="Muy satisfecho"><div class="pl-2 pr-2"> Muy satisfecho </div></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(pregunta, index) in preguntas" style="border: solid 1px rebeccapurple;">
                  <td style="border: solid 1px rebeccapurple;" colspan="1" rowspan="1"><div> {{ pregunta.texto }}</div></td>
                  <td style="border: solid 1px rebeccapurple;" colspan="1" rowspan="1">
                    <div class="flex align-items-center justify-center">
                        <RadioButton v-model="respuestas[index]" inputId="ingredient1" name="pizza" :value="1"/>
                    </div>
                  </td>
                  <td style="border: solid 1px rebeccapurple;" colspan="1" rowspan="1">
                    <div class="flex align-items-center justify-center">
                        <RadioButton v-model="respuestas[index]" inputId="ingredient2" name="pizza" :value="2"/>
                    </div>
                  </td>
                  <td style="border: solid 1px rebeccapurple;" colspan="1" rowspan="1">
                    <div class="flex align-items-center justify-center">
                        <RadioButton v-model="respuestas[index]" inputId="ingredient3" name="pizza" :value="3"/>
                    </div>
                  </td>
                  <td style="border: solid 1px rebeccapurple;" colspan="1" rowspan="1">
                    <div class="flex align-items-center justify-center">
                        <RadioButton v-model="respuestas[index]" inputId="ingredient3" name="pizza" :value="4"/>
                    </div>
                  </td>
                </tr>
                <tr style="border: solid 1px rebeccapurple;">
                  <td style="border: solid 1px rebeccapurple;" colspan="1" rowspan="1"><div> Sugerencias</div></td>
                  <td style="border: solid 1px rebeccapurple;" colspan="4" rowspan="1">
                    <div class="flex align-items-center justify-center">
                      <InputText type="text" v-model="sugerencia" style="width: 100%;"/>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
            <div class="flex justify-end mt-3">
              <Button label="Guardar" @click="save()"/>
            </div>
      </div>
    </Dialog>
  </div>
</AuthenticatedLayout>
</template>
  
<script setup>
import {ref} from 'vue'
import AuthenticatedLayout from '@/Layouts/LayoutEstudiante.vue';
import { Head } from '@inertiajs/vue3';
import Button from 'primevue/button';
import  Card  from 'primevue/card';
import Dialog from 'primevue/dialog';
import RadioButton from 'primevue/radiobutton';
import InputText from 'primevue/inputtext';
import Toast from 'primevue/toast';
import { useToast } from "primevue/usetoast";

const toast = useToast();
const respuestas = ref([])
const sugerencia = ref("")

const cursos = ref([])
const getCursos = ( ) => {
  axios.get('cursos-encuesta', { })
    .then(response => {
    console.log(response.data)
    cursos.value = response.data.datos
  })
  .catch(error => {
    console.error(error);
  });
}

const preguntas = ref([])

const getPreguntas = ( ) => {
  axios.get('get-preguntas/'+1)
    .then(response => {
    console.log(response.data)
    preguntas.value = response.data.datos
  })
  .catch(error => {
    console.error(error);
  });
}

const modalEncuesta = ref(false);
const cursoID = ref(null);

const abrirModal = (curso_id) => {
  cursoID.value = curso_id;
  modalEncuesta.value = true;
}

const temp = ref(null)

const save = ( ) => {
  axios.post('save-encuesta-estudiante',{
      preguntas: preguntas.value,
      respuestas: respuestas.value,
      curso: cursoID.value,
      sugerencia: sugerencia.value
  })
    .then(response => {
      modalEncuesta.value = false;
      respuestas.value = []
      cursoID.value = null;
      sugerencia.value = null;
      temp.value = response.data;
      getCursos()
      showToast(response.data.tipo, response.data.titulo, response.data.mensaje)
  })
  .catch(error => {
    console.error(error);
  });
}


const showToast = (tipo, titulo, detalle) => {
    toast.add({ severity: tipo, summary: titulo, detail: detalle, life: 3000 });
};


getCursos()
getPreguntas()

</script>

<style scoped>
.gallery {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
}

.card-item {
  margin: 8px;
  width: 250px;
  height: 240px;
}

.card-content {
  height: 130px;
  padding: 16px;
}
</style>
