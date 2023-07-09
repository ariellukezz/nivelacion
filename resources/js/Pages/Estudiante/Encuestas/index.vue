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
          <Button v-else label="Empezar" style="width: 100%;"/>
        </div>
    </template>
    </Card>
  </div>
</AuthenticatedLayout>
</template>
  
<script setup>
import {ref} from 'vue'
import AuthenticatedLayout from '@/Layouts/LayoutEstudiante.vue';
import { Head } from '@inertiajs/vue3';
import Button from 'primevue/button';
import  Card  from 'primevue/card';

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

getCursos()

const galleryItems = [
  { id: 1, title: 'Competencia 1', description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.' },
  { id: 2, title: 'Competencia 2', description: 'Praesent eu dolor at nisl consectetur hendrerit.' },
  { id: 3, title: 'Competencia 2', description: 'Nulla finibus neque sed ipsum facilisis, ut ultricies est placerat.' },
  { id: 4, title: 'Competencia 2', description: 'Sed tincidunt elit nec diam auctor, vitae volutpat mauris euismod.' },
  { id: 5, title: 'Competencia 2', description: 'Fusce dapibus nisl eget elit gravida, in consequat sem sollicitudin.' },
];

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
