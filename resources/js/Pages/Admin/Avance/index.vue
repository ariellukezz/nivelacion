<template>
<Head title="Avance"/>
<AuthenticatedLayout>
    <div class="gallery">
      <CardComponent v-for="item, in escuelas" :key="item.id" :titulo="item.nombre_corto" :id_escuela="item.id_escuela" :data="data"/>
    </div>
</AuthenticatedLayout>
</template>
  
<script setup>
import {ref} from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import CardComponent from './components/cardAvance.vue';

const data = ref([])
const escuelas = ref([])

const getAvance =  async (event) => {
  let res = await axios.get(
  "/get-avance");
  data.value = res.data.datos;
  escuelas.value = res.data.escuelas;
}

getAvance()

</script>

<style scoped>
    .gallery {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 20px;
    }
</style>
  