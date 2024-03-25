<template>
<div >
<Card style="height: 200px; width: 240px;">
    <template #content>
        <div class="flex justify-center align-center" style="width: 100%; height: 150px; align-items: center;">
            <div class="">
                <div class="flex justify-center">
                    <div  style="width: 180px;white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-align: center;">
                        <h1 style="font-size: 1.2rem; color: var(--primary-color);">{{titulo}}</h1>
                    </div>
                </div>
                <div  class="flex justify-center">
                    <hr style="width: 50px;  border: solid salmon 2px;">
                </div>
                <div class="mt-5" style="width: 180px;">
                    <ProgressBar :value="data[id_escuela]"> &nbsp; </ProgressBar>
                </div>

                <div class="flex justify-center">
                    <span style="font-weight: 500; color: var(--primary-color); font-size: 1.7rem;">{{ data[id_escuela] }}%</span>       
                </div>
            </div>
        </div>
    </template>
</Card>
</div>
</template>
  

<script setup>
import {ref} from 'vue';
import Card from 'primevue/card';
import ProgressBar from 'primevue/progressbar';
import { defineProps } from 'vue';

const props = defineProps({
  titulo: String,
  id_escuela: Number,
  data:null
});


</script>
  
  