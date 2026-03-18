<template>
<AuthenticatedLayout>

    <Head title="Reprobados Nivelación" />

    <!-- ✅ NUEVO TÍTULO CON PERIODOS -->
    <div class="text-center mb-4">
      <h1 class="text-xl font-bold tracking-wide">
        REPROBADOS {{ periodoAnterior }} – NIVELACIÓN
      </h1>
      <p class="text-sm mt-1 text-gray-700">
        NECESARIO LLEVAR NIVELACIÓN EN EL PRESENTE PERIODO
        <strong class="text-blue-600">{{ periodoActual }}</strong>
      </p>
    </div>

<!-- Leyenda que explica el significado de SI, NO y -- -->
<div class="mb-4">
  <p class="text-sm text-gray-700">
    <strong>SI</strong> significa que el estudiante <strong>requiere nivelación</strong>.<br>
    <strong>NO</strong> significa que el estudiante <strong>no requiere nivelación</strong>.<br>
    <strong>--</strong> significa que el estudiante <strong>ya aprobó y no requiere nivelación</strong>.
  </p>
</div>


  <div class="p-4" style="background: white;">
    <div class="flex justify-end mb-2">
      <Button label="Exportar excel" @click="exportarExcel" size="small" style="height: 40px;"/>
    </div>

    <table style="border-radius: 4px; width: 100%; overflow: hidden;">
      <thead style="height: 30px; color: white; background-color: var(--primary-color); padding: 0px 10px;">
        <tr style="font-size: .9rem;">
          <th style="border: 1px solid #d9d9d9;">CODIGO</th>
          <th style="border: 1px solid #d9d9d9;">APELLIDOS Y NOMBRES</th>
          <th style="border: 1px solid #d9d9d9;">EMAIL</th>
          <th style="border: 1px solid #d9d9d9;">CELULAR</th>
          <th style="border: 1px solid #d9d9d9;">PROGRAMA DE ESTUDIO</th>
          <th style="border: 1px solid #d9d9d9;">INGRESO</th>
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
        <tr v-for="item in estudiantes" :key="item.id_estudiante" style="font-size: .9rem;">
          <td style="border: 1px solid #d9d9d9; text-align: center;"><div class="pl-1 pr-1">{{ item.codigo_est }}</div></td>
          <td style="border: 1px solid #d9d9d9;"><div class="pl-1 pr-1">{{ item.paterno }} {{ item.materno }},  {{ item.nombre }}</div></td>
          <td style="border: 1px solid #d9d9d9; text-align: center;"><div class="pl-1 pr-1">{{ item.email }}</div></td>
          <td style="border: 1px solid #d9d9d9; text-align: center;"><div class="pl-1 pr-1">{{ item.telefono }}</div></td>
          <td style="border: 1px solid #d9d9d9; text-align: center;"><div class="pl-1 pr-1">{{ item.programa }}</div></td>
          <td style="border: 1px solid #d9d9d9; text-align: center;"><div class="pl-1 pr-1">{{ item.semestre }}</div></td>

          <td v-for="(ite,index) in 11" :key="index" style="border: 1px solid #d9d9d9; min-width:40px; text-align:center;">
            <div v-if="buscarValor(item.notas, ite) !== null">
              <div v-if="item.notas[buscarValor(item.notas, ite)]">
                {{ aSiNo(item.notas[buscarValor(item.notas, ite)].nota) }}
              </div>
              <div v-else>
                <span style="font-size: .9rem;">--</span>
              </div>
            </div>
            <div v-else>--</div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';
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

function aSiNo(v) {
  if (v === null || v === undefined || String(v).trim() === '') return 'SI'
  const n = Number(String(v).replace(',', '.'))
  if (Number.isNaN(n)) return 'SI'
  if (n <= 10) return 'SI'
  if (n >= 11 && n <= 20) return 'NO'
  return 'SI'
}

const pagina             = ref(1);
const estudiantes        = ref([]);
// ✅ NUEVO: variables para los periodos
const periodoAnterior    = ref('');
const periodoActual      = ref('');

const getEstudiantes = async () => {
  const res = await axios.get('/reprobados-nivelacion/data?page=' + pagina.value);
  estudiantes.value     = res.data.datos;
  // ✅ NUEVO: capturar periodos de la respuesta
  periodoAnterior.value = res.data.periodo_anterior;
  periodoActual.value   = res.data.periodo_actual;
  generarObjetosEstudiantes();
};

const objetosEstudiantes = ref([]);

const generarObjetosEstudiantes = () => {
  objetosEstudiantes.value = estudiantes.value.map(item => {
    const estudiante = {
      codigo_est: item.codigo_est,
      programa: item.programa,
      semestre: item.semestre,
      filial: item.filial,
      email: item.email,
      telefono: item.telefono,
      nombreCompleto: `${item.paterno} ${item.materno},  ${item.nombre}`,
    };

    for (let i = 1; i <= 11; i++) {
      const idx = buscarValor(item.notas, i);
      if (idx !== null && item.notas[idx]) {
        estudiante[`C${i}`] = aSiNo(item.notas[idx].nota);
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
  XLSX.utils.book_append_sheet(wb, ws, 'Reprobados');
  // ✅ NUEVO: nombre de archivo con periodos
  XLSX.writeFile(wb, `reprobados_${periodoAnterior.value}_nivelacion_${periodoActual.value}.xlsx`);
};

getEstudiantes();
</script>
