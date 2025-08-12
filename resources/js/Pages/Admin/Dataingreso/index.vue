<template>
  <AuthenticatedLayout>
    <!-- (opcional) título de la pestaña del navegador -->
    <Head title="Reprobados Nivelación" />

      <!-- ⬇️ TÍTULO CENTRADO AQUÍ -->
      <div class="text-center mb-4">
        <h1 class="text-xl font-bold">INGRESANTES – NUEVOS</h1>
      </div>

    <div class="p-4" style="background: white;">
      <div class="flex justify-end mb-2">
        <Button
            label="Exportar Excel"
            icon="pi pi-download"
            @click="exportarExcel"
            size="small"
            style="height:40px;"
            />
      </div>

      <table style="border-radius: 4px; width: 100%; overflow: hidden;">
        <thead style="height: 30px; color: white; background-color: var(--primary-color);">
          <tr style="font-size: .9rem;">
            <th style="border:1px solid #d9d9d9;">DNI</th>
            <th style="border:1px solid #d9d9d9;">APELLIDOS Y NOMBRES</th>
            <th style="border:1px solid #d9d9d9;">SEXO</th>
            <th style="border:1px solid #d9d9d9;">EMAIL</th>
            <th style="border:1px solid #d9d9d9;">CELULAR</th>
            <th style="border:1px solid #d9d9d9;">PROGRAMA DE ESTUDIO</th>
            <th style="border:1px solid #d9d9d9;">MODALIDAD INGRESO</th>
            <th style="border:1px solid #d9d9d9;">C1</th>
            <th style="border:1px solid #d9d9d9;">C2</th>
            <th style="border:1px solid #d9d9d9;">C3</th>
            <th style="border:1px solid #d9d9d9;">C4</th>
            <th style="border:1px solid #d9d9d9;">C5</th>
            <th style="border:1px solid #d9d9d9;">C6</th>
            <th style="border:1px solid #d9d9d9;">C7</th>
            <th style="border:1px solid #d9d9d9;">C8</th>
            <th style="border:1px solid #d9d9d9;">C9</th>
            <th style="border:1px solid #d9d9d9;">C10</th>
            <th style="border:1px solid #d9d9d9;">C11</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, idx) in ingresantes" :key="idx" style="font-size: .9rem;">
            <td style="border:1px solid #d9d9d9; text-align:center;"><div class="px-1">{{ item.dni_ingr }}</div></td>
            <td style="border:1px solid #d9d9d9;"><div class="px-1">{{ item.primer_apellido }} {{ item.segundo_apellido }}, {{ item.nombres_ingr }}</div></td>
            <td style="border:1px solid #d9d9d9; text-align:center;"><div class="px-1">{{ item.sexo }}</div></td>
            <td style="border:1px solid #d9d9d9; text-align:center;"><div class="px-1">{{ item.email }}</div></td>
            <td style="border:1px solid #d9d9d9; text-align:center;"><div class="px-1">{{ item.celular_ingre }}</div></td>
            <td style="border:1px solid #d9d9d9; text-align:center;"><div class="px-1">{{ item.programa }}</div></td>
            <td style="border:1px solid #d9d9d9; text-align:center;"><div class="px-1">{{ item.mod_ingr }}</div></td>

            <td style="border:1px solid #d9d9d9; text-align:center; min-width:40px;"><div class="px-1">{{ aSiNo(item.i_C1_R) }}</div></td>
            <td style="border:1px solid #d9d9d9; text-align:center; min-width:40px;"><div class="px-1">{{ aSiNo(item.i_C2_R) }}</div></td>
            <td style="border:1px solid #d9d9d9; text-align:center; min-width:40px;"><div class="px-1">{{ aSiNo(item.i_C3_R) }}</div></td>
            <td style="border:1px solid #d9d9d9; text-align:center; min-width:40px;"><div class="px-1">{{ aSiNo(item.i_C4_R) }}</div></td>
            <td style="border:1px solid #d9d9d9; text-align:center; min-width:40px;"><div class="px-1">{{ aSiNo(item.i_C5_R) }}</div></td>
            <td style="border:1px solid #d9d9d9; text-align:center; min-width:40px;"><div class="px-1">{{ aSiNo(item.i_C6_R) }}</div></td>
            <td style="border:1px solid #d9d9d9; text-align:center; min-width:40px;"><div class="px-1">{{ aSiNo(item.i_C7_R) }}</div></td>
            <td style="border:1px solid #d9d9d9; text-align:center; min-width:40px;"><div class="px-1">{{ aSiNo(item.i_C8_R) }}</div></td>
            <td style="border:1px solid #d9d9d9; text-align:center; min-width:40px;"><div class="px-1">{{ aSiNo(item.i_C9_R) }}</div></td>
            <td style="border:1px solid #d9d9d9; text-align:center; min-width:40px;"><div class="px-1">{{ aSiNo(item.i_C10_R) }}</div></td>
            <td style="border:1px solid #d9d9d9; text-align:center; min-width:40px;"><div class="px-1">{{ aSiNo(item.i_C11_R) }}</div></td>
          </tr>
        </tbody>
      </table>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import Button from 'primevue/button'
import * as XLSX from 'xlsx'

const ingresantes = ref([])
const pagina = ref(1)

const aSiNo = (v) => {
  if (v === null || v === undefined || v === '') return '--'
  const n = Number(String(v).replace(',', '.'))
  if (Number.isNaN(n)) return '--'
  if (n <= 10) return 'SI'
  if (n >= 11 && n <= 20) return 'NO'
  return '--'
}

const getIngresantes = async () => {
  const res = await axios.get('/ingresantes', { params: { page: pagina.value } })
  ingresantes.value = Array.isArray(res.data?.datos) ? res.data.datos : []
}

const exportarExcel = () => {
  const data = ingresantes.value.map(i => ({
    DNI: i.dni_ingr,
    'Apellidos y Nombres': `${i.primer_apellido} ${i.segundo_apellido}, ${i.nombres_ingr}`,
    Sexo: i.sexo,
    Email: i.email,
    Celular: i.celular_ingre,
    Programa: i.programa,
    'Modalidad Ingreso': i.mod_ingr,
    C1: aSiNo(i.i_C1_R),
    C2: aSiNo(i.i_C2_R),
    C3: aSiNo(i.i_C3_R),
    C4: aSiNo(i.i_C4_R),
    C5: aSiNo(i.i_C5_R),
    C6: aSiNo(i.i_C6_R),
    C7: aSiNo(i.i_C7_R),
    C8: aSiNo(i.i_C8_R),
    C9: aSiNo(i.i_C9_R),
    C10: aSiNo(i.i_C10_R),
    C11: aSiNo(i.i_C11_R)
  }))

  const ws = XLSX.utils.json_to_sheet(data)
  const wb = XLSX.utils.book_new()
  XLSX.utils.book_append_sheet(wb, ws, 'Ingresantes')
  XLSX.writeFile(wb, 'ingresantes.xlsx')
}

getIngresantes()
</script>
