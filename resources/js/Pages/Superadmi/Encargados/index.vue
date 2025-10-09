<template>
  <AuthenticatedLayout>
    <Head title="Encargados del Sistema" />
    <div class="p-4" style="background:white;">
      <div class="text-center mb-4">
        <h1 class="text-xl font-bold">ENCARGADOS DEL SISTEMA</h1>
      </div>

      <div class="flex items-center gap-2 mb-3">
        <InputText
          v-model="q"
          placeholder="Buscar por nombre o escuela"
          class="w-full"
          @keyup.enter="cargar"
        />
        <Button label="Buscar" size="small" @click="cargar" />
        <Button label="Limpiar" size="small" severity="secondary" @click="limpiar" />
      </div>

      <table style="border-radius:4px; width:100%; overflow:hidden;">
        <thead style="height:30px; color:#fff; background-color:var(--primary-color);">
          <tr style="font-size:.9rem;">
            <th class="th">NOMBRES</th>
            <th class="th">APELLIDOS</th>
            <th class="th">CORREO</th>
            <th class="th">CELULAR</th>
            <th class="th">CARGO</th>
            <th class="th">ESTADO</th>
            <th class="th">OBSERVACIONES</th>
            <th class="th">ESCUELA</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="r in rows" :key="r.id" style="font-size:.9rem;">
            <td class="td">{{ r.nombres }}</td>
            <td class="td">{{ r.apellidos }}</td>
            <td class="td" style="text-align:center;">{{ r.correo }}</td>
            <td class="td" style="text-align:center;">{{ r.num_celular }}</td>
            <td class="td" style="text-align:center;">{{ r.cargo }}</td>
            <td class="td" :class="estadoClase(r.estado)" style="text-align:center;">
              {{ estadoTexto(r.estado) }}
            </td>
            <td class="td">{{ r.observaciones || '—' }}</td>
            <td class="td" style="text-align:center;">{{ r.escuela }}</td>
          </tr>

          <tr v-if="rows.length === 0">
            <td class="td" colspan="8" style="text-align:center; padding:10px;">Sin resultados</td>
          </tr>
        </tbody>
      </table>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/LayoutSuperadmi.vue'
import { Head } from '@inertiajs/vue3'
import { ref } from 'vue'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'

const q = ref('')
const rows = ref([])

const estadoTexto = (v) => {
  if (v === true || v === 1 || v === '1' || String(v).toLowerCase() === 'activo') return 'Activo'
  if (v === false || v === 0 || v === '0' || String(v).toLowerCase() === 'inactivo') return 'Inactivo'
  return String(v ?? '—')
}
const estadoClase = (v) => (estadoTexto(v) === 'Activo' ? 'ok' : 'bad')

async function cargar() {
  const res = await axios.get('encargados-sistema/data', { params: { q: q.value } })
  rows.value = Array.isArray(res.data?.data) ? res.data.data : []
}
function limpiar() {
  q.value = ''
  cargar()
}

// carga inicial
cargar()
</script>

<style scoped>
.th { border:1px solid #d9d9d9; padding:6px; }
.td { border:1px solid #d9d9d9; padding:6px; }
.ok { color:#16a34a; font-weight:600; }   /* Activo */
.bad { color:#dc2626; font-weight:600; }  /* Inactivo */
</style>
