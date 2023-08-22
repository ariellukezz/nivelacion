<template>
<AuthenticatedLayout>
    <div class="p-4" style="background: white;">

        <div class="flex justify-end mb-2">
            <Button label="Exportar excel" @click="exportarExcel" size="small" style="height: 40px;"/>
        </div>

        <table style="border-radius: 4px; width: 100%; overflow: hidden;">
            <thead style="height: 30px; color: white; background-color: var(--primary-color); padding: 0px 10px;">
                <tr style="font-size: .9rem;">
                    <th style="border: 1px solid #d9d9d9;">DNI</th>
                    <th style="border: 1px solid #d9d9d9;">NOMBRES</th>
                    <th style="border: 1px solid #d9d9d9;">C1</th>
                    <th style="border: 1px solid #d9d9d9;">C2</th>
                    <th style="border: 1px solid #d9d9d9;">C3</th>
                    <th style="border: 1px solid #d9d9d9;">C4</th>
                    <th style="border: 1px solid #d9d9d9;">C5</th>
                    <th style="border: 1px solid #d9d9d9;">C6</th>
                    <th style="border: 1px solid #d9d9d9;">C7</th>
                    <th style="border: 1px solid #d9d9d9;">C8</th>
                    <th style="border: 1px solid #d9d9d9;">C9</th>
                    <th style="border: 1px solid #d9d9d9;">C10</th>
                    <th style="border: 1px solid #d9d9d9;">C11</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in estudiantes" :key="item.id" style="font-size: .9rem;">
                    <td style="border: 1px solid #d9d9d9; font-weight: .8rem; text-align: center;"><div class="pl-1 pr-1">{{item.dni }}</div></td>
                    <td style="border: 1px solid #d9d9d9; font-weight: .7rem;"><div class="pl-1 pr-1">{{item.nombre }} {{ item.paterno }} {{ item.materno }}</div> </td>
                    <td v-for="(ite,index) in 11" :key="index" style="border: 1px solid #d9d9d9; min-width:40px;" >
                        <!-- {{ buscarValor(item.notas, index) }} -->
                        <div v-if="buscarValor(item.notas, ite) !== null" style="text-align: center;">
                            <div v-if="item.notas[buscarValor(item.notas, ite)]">
                                {{ item.notas[buscarValor(item.notas, ite)].nota}}
                            </div>
                            <div v-else>
                                <span style="font-size: .9rem;"></span>
                            </div>
                        </div>
                        <div v-else style="text-align: center;">
                            - -
                        </div>
                    </td> 
                </tr>

            </tbody>

        </table>
    </div>
</AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import {ref} from 'vue';
import XLSX from 'xlsx';
import Button from 'primevue/button';


function buscarValor(array, valor) {
  for (var i = 0; i < array.length; i++) {
    if (array[i].competencia === valor) {
      return i;
    }
  }
  return null;
}

const pagina = ref(1)
const estudiantes = ref([])
const getEstudiantes =  async () => {
    let res = await axios.get(
    "prueba2?page=" + pagina.value,
    { term: "" }
    );
    estudiantes.value = res.data.datos;
    generarObjetosEstudiantes();
}



const objetosEstudiantes = ref([]);

const generarObjetosEstudiantes = () => {
  objetosEstudiantes.value = estudiantes.value.map(item => {
    const estudiante = {
      dni: item.dni,
      nombreCompleto: `${item.nombre} ${item.paterno} ${item.materno}`,
    };

    for (let i = 1; i <= 11; i++) {
      const index = buscarValor(item.notas, i);
      if (index !== null) {
        estudiante[`C${i}`] = item.notas[index].nota;
      } else {
        estudiante[`C${i}`] = '--';
      }
    }

    return estudiante;
  });
};


const exportarExcel = () => {
  const ws = XLSX.utils.json_to_sheet(objetosEstudiantes.value);
  const wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, ws, 'Estudiantes');

  XLSX.writeFile(wb, 'estudiantes.xlsx');
};


getEstudiantes()




</script>