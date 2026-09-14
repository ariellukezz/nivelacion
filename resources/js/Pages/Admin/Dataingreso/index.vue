<template>
  <AuthenticatedLayout>
    <Head title="Ingresantes Nuevos" />

    <div class="data-ingreso-page w-full min-w-0 max-w-full p-2 sm:p-3 space-y-3">
      <div class="text-center">
        <h1 class="text-base sm:text-lg font-bold tracking-wide leading-tight">
          INGRESANTES NUEVOS PARA EL PERIODO
          <span class="text-blue-600">{{ periodoActual }}</span>
        </h1>

        <p class="text-xs text-gray-500 mt-1">
          Información integrada desde Admisión y la matriz del periodo.
        </p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
        <div class="border rounded-md px-3 py-2 bg-white shadow-sm">
          <div class="text-xs text-gray-500">Ingresantes</div>
          <div class="text-lg sm:text-xl font-bold text-gray-800 leading-tight mt-0.5">
            {{ resumen.total }}
          </div>
        </div>

        <div class="border rounded-md px-3 py-2 bg-white shadow-sm">
          <div class="text-xs text-gray-500">Programas</div>
          <div class="text-lg sm:text-xl font-bold text-gray-800 leading-tight mt-0.5">
            {{ resumen.programas }}
          </div>
        </div>

        <div class="border rounded-md px-3 py-2 bg-white shadow-sm">
          <div class="text-xs text-gray-500">Procesos de Admisión</div>
          <div class="text-lg sm:text-xl font-bold text-gray-800 leading-tight mt-0.5">
            {{ resumen.procesos }}
          </div>
        </div>
      </div>

      <div class="bg-white border rounded-md p-3">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
          <div>
            <label class="block text-xs font-semibold mb-1">Buscar</label>
            <InputText
              v-model="busqueda"
              placeholder="DNI, código o estudiante"
              class="w-full compact-control"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold mb-1">Programa</label>
            <Dropdown
              v-model="programaFiltro"
              :options="programas"
              placeholder="Todos los programas"
              showClear
              filter
              class="w-full compact-control"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold mb-1">
              Proceso de Admisión
            </label>
            <Dropdown
              v-model="procesoFiltro"
              :options="procesos"
              placeholder="Todos los procesos"
              showClear
              filter
              class="w-full compact-control"
            />
          </div>
        </div>

        <div class="flex flex-wrap justify-between items-center gap-2 mt-3">
          <div class="text-xs text-gray-600">
            Mostrando
            <strong>{{ ingresantesFiltrados.length }}</strong>
            de
            <strong>{{ ingresantes.length }}</strong>
            registros.
          </div>

          <Button
            label="Exportar Excel"
            icon="pi pi-download"
            size="small"
            @click="exportarExcel"
          />
        </div>
      </div>

      <div class="w-full min-w-0 max-w-full bg-white border rounded-md overflow-hidden">
        <div class="w-full max-w-full overflow-x-auto table-scroll">
          <table class="w-full border-collapse text-[10.5px] sm:text-[11px] min-w-[1320px]">
            <thead class="text-white bg-[var(--primary-color)]">
              <tr>
                <th class="celda">CÓDIGO</th>
                <th class="celda">DNI</th>
                <th class="celda">APELLIDOS Y NOMBRES</th>
                <th class="celda">SEXO</th>
                <th class="celda">EMAIL</th>
                <th class="celda">CELULAR</th>
                <th class="celda">PROGRAMA</th>
                <th class="celda">PROCESO</th>
                <th class="celda">MODALIDAD</th>

                <th
                  v-for="n in 11"
                  :key="`c-${n}`"
                  class="celda competencia-col"
                >
                  C{{ n }}
                </th>

                <th class="celda observacion-col">OBSERVACIÓN</th>
              </tr>
            </thead>

            <tbody>
              <tr
                v-for="item in ingresantesFiltrados"
                :key="`${item.id_periodo}-${item.dni_ingr}`"
                class="hover:bg-gray-50"
              >
                <td class="celda text-center">
                  {{ item.codigo_est }}
                </td>

                <td class="celda text-center">
                  {{ item.dni_ingr }}
                </td>

                <td class="celda">
                  {{ item.primer_apellido }}
                  {{ item.segundo_apellido }},
                  {{ item.nombres_ingr }}
                </td>

                <td class="celda text-center">
                  {{ item.sexo || '-' }}
                </td>

                <td class="celda">
                  {{ item.email || '-' }}
                </td>

                <td class="celda text-center">
                  {{ item.celular_ingre || '-' }}
                </td>

                <td class="celda">
                  {{ item.programa }}
                </td>

                <td class="celda">
                  {{ item.proceso_ingr || '-' }}
                </td>

                <td class="celda">
                  {{ item.mod_ingr || '-' }}
                </td>

                <td
                  v-for="n in 11"
                  :key="`${item.dni_ingr}-c-${n}`"
                  class="celda text-center"
                >
                  {{ aSiNo(item[`i_C${n}_R`]) }}
                </td>

                <td class="celda">
                  {{ item.observacion_matriz || '-' }}
                </td>
              </tr>

              <tr v-if="!loading && ingresantesFiltrados.length === 0">
                <td
                  colspan="21"
                  class="text-center p-6 text-gray-500"
                >
                  No se encontraron ingresantes para los filtros seleccionados.
                </td>
              </tr>

              <tr v-if="loading">
                <td
                  colspan="21"
                  class="text-center p-6 text-gray-500"
                >
                  Cargando información...
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="bg-white border rounded-md px-3 py-2">
        <p class="text-xs text-gray-700">
          <strong>SI</strong>: requiere nivelación.
          <strong class="ml-3">NO</strong>: no requiere nivelación.
          <strong class="ml-3">--</strong>: sin valor interpretable.
        </p>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { computed, onMounted, ref } from 'vue'
import Button from 'primevue/button'
import Dropdown from 'primevue/dropdown'
import InputText from 'primevue/inputtext'
import * as XLSX from 'xlsx'

const ingresantes = ref([])
const periodoActual = ref('')
const loading = ref(false)

const busqueda = ref('')
const programaFiltro = ref(null)
const procesoFiltro = ref(null)

const resumen = ref({
  total: 0,
  programas: 0,
  procesos: 0
})

const programas = ref([])
const procesos = ref([])

const aSiNo = v => {
  if (v === null || v === undefined || v === '') return '--'

  const n = Number(String(v).replace(',', '.'))

  if (Number.isNaN(n)) return '--'
  if (n <= 10) return 'SI'
  if (n >= 11 && n <= 20) return 'NO'

  return '--'
}

const ingresantesFiltrados = computed(() => {
  const term = busqueda.value.trim().toLowerCase()

  return ingresantes.value.filter(item => {
    if (
      programaFiltro.value &&
      item.programa !== programaFiltro.value
    ) {
      return false
    }

    if (
      procesoFiltro.value &&
      item.proceso_ingr !== procesoFiltro.value
    ) {
      return false
    }

    if (!term) return true

    const texto = [
      item.codigo_est,
      item.dni_ingr,
      item.primer_apellido,
      item.segundo_apellido,
      item.nombres_ingr,
      item.programa,
      item.proceso_ingr
    ]
      .filter(Boolean)
      .join(' ')
      .toLowerCase()

    return texto.includes(term)
  })
})

const getIngresantes = async () => {
  loading.value = true

  try {
    const res = await axios.get('/ingresantes')

    ingresantes.value = Array.isArray(res.data?.datos)
      ? res.data.datos
      : []

    periodoActual.value = res.data?.periodo_actual ?? ''

    resumen.value = res.data?.resumen ?? {
      total: ingresantes.value.length,
      programas: 0,
      procesos: 0
    }

    programas.value = Array.isArray(res.data?.filtros?.programas)
      ? res.data.filtros.programas
      : []

    procesos.value = Array.isArray(res.data?.filtros?.procesos)
      ? res.data.filtros.procesos
      : []
  } finally {
    loading.value = false
  }
}

const exportarExcel = () => {
  const data = ingresantesFiltrados.value.map(i => ({
    Código: i.codigo_est,
    DNI: i.dni_ingr,
    'Apellidos y Nombres':
      `${i.primer_apellido} ${i.segundo_apellido}, ${i.nombres_ingr}`,
    Sexo: i.sexo,
    Email: i.email,
    Celular: i.celular_ingre,
    Programa: i.programa,
    'Proceso de Admisión': i.proceso_ingr,
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
    C11: aSiNo(i.i_C11_R),

    Observación: i.observacion_matriz || ''
  }))

  const ws = XLSX.utils.json_to_sheet(data)
  const wb = XLSX.utils.book_new()

  XLSX.utils.book_append_sheet(
    wb,
    ws,
    'Ingresantes'
  )

  XLSX.writeFile(
    wb,
    `ingresantes_${periodoActual.value || 'periodo'}.xlsx`
  )
}

onMounted(getIngresantes)
</script>

<style scoped>
.data-ingreso-page {
  overflow: hidden;
}

.celda {
  border: 1px solid #d9d9d9;
  padding: 4px 5px;
  vertical-align: middle;
  line-height: 1.2;
}

th.celda {
  padding-top: 5px;
  padding-bottom: 5px;
  font-size: 10px;
  font-weight: 700;
  white-space: nowrap;
}

.competencia-col {
  width: 38px;
  min-width: 38px;
  max-width: 38px;
}

.observacion-col {
  min-width: 160px;
  max-width: 200px;
}

.table-scroll {
  -webkit-overflow-scrolling: touch;
  scrollbar-width: thin;
}

:deep(.compact-control.p-inputtext),
:deep(.compact-control .p-inputtext) {
  font-size: 12px;
  padding-top: 0.45rem;
  padding-bottom: 0.45rem;
}

:deep(.compact-control.p-dropdown) {
  min-height: 34px;
}

:deep(.compact-control.p-dropdown .p-dropdown-label) {
  font-size: 12px;
  padding: 0.45rem 0.65rem;
}

:deep(.compact-control.p-dropdown .p-dropdown-trigger) {
  width: 2rem;
}

:deep(.p-button.p-button-sm) {
  font-size: 11px;
  padding: 0.45rem 0.7rem;
}

@media (max-width: 640px) {
  .data-ingreso-page {
    padding-left: 0;
    padding-right: 0;
  }

  .celda {
    padding: 3px 4px;
  }
}
</style>
