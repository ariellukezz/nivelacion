<template>
  <Head title="Evento de Inducción" />
  <AuthenticatedLayout>
    <div class="bg-white shadow-xs p-4" style="height: calc(100vh - 110px);">

      <div class="mb-4">
        <h2 class="text-lg font-semibold">Evento de Inducción para Ingresantes</h2>
        <p class="text-sm text-gray-600 mt-1">
          Bienvenido a la universidad 🎓. Este evento es la primera actividad oficial para
          estudiantes ingresantes. Aquí recibiras información académica.
        </p>
      </div>

      <!-- Siempre visible porque evento siempre es un objeto -->
      <div class="border rounded p-4 mb-4">
        <h3 class="text-base font-semibold mb-2">Tu estado de participación</h3>

        <p class="text-sm mb-2">
          <strong>Mesa asignada:</strong>
          {{ (evento.mesa !== null && evento.mesa !== undefined && evento.mesa !== '') ? evento.mesa : '—' }} <br>
          <strong>Número:</strong>
          {{ (evento.numero !== null && evento.numero !== undefined && evento.numero !== '') ? evento.numero : '—' }}
        </p>

        <div>
          <Tag
            v-if="Number(evento.firma_ingreso) === 1"
            severity="success"
            value="Ya ingresaste a la inducción ✅"
          />
          <Tag
            v-else
            severity="warning"
            value="Aún no has ingresado ❌"
          />
        </div>

        <div v-if="Number(evento.firma_ingreso) !== 1" class="mt-3 text-sm text-gray-700">
          Recuerda:
          <ul class="list-disc pl-5 space-y-1">
            <li>Preséntate con tu DNI para registrar tu entrada.</li>
            <li>Debes firmar tu ingreso para registrar la asistencia.</li>
          </ul>
        </div>
      </div>
      <div v-if="error" class="mt-3 text-sm text-red-600">
        {{ error }}
      </div>




      <div class="card" style="max-width: 720px">
    <Message severity="info" :closable="false">
      📢 Síguenos en nuestra página de
      <a
        href="https://www.facebook.com/VicerrectoradoAcademicoVRAUNAP"
        target="_blank"
        rel="noopener noreferrer"
        class="fb-link"
      >
        <i class="pi pi-facebook"></i> Vicerrectorado Académico UNA Puno
      </a>
      para recibir comunicados, cronogramas y novedades académicas.
    </Message>
  </div>


    </div>

  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/LayoutEstudiante.vue'
import { Head } from '@inertiajs/vue3'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Tag from 'primevue/tag'
import axios from 'axios'
import { ref, computed } from 'vue'
import { onMounted } from 'vue';

const cargando = ref(false)
const error = ref('')

// Siempre empezamos con un objeto por defecto (para que renderice “—”)
const evento = ref({
  mesa: null,
  numero: null,
  firma_ingreso: null,
})



const eventoTabla = computed(() => [evento.value])

const getEvento = async () => {
  try {
    cargando.value = true
    error.value = ''
    const res = await axios.post('get-evento-induccion')
    const datos = res?.data?.datos

    // Si la API devuelve null, mantenemos el objeto por defecto (ya muestra —)
    if (datos && typeof datos === 'object') {
      evento.value = {
        mesa: datos.mesa ?? null,
        numero: datos.numero ?? null,
        firma_ingreso: datos.firma_ingreso != null ? Number(datos.firma_ingreso) : null,
      }
    }
  } catch (e) {
    error.value = 'No se pudo cargar la información del evento.'
    console.error(e)
  } finally {
    cargando.value = false
  }
}

getEvento()
</script>

<style scoped>
.fb-link {
  display: inline-flex;
  align-items: center;
  gap: .35rem;
  font-weight: 600;
  color: #1877f2; /* azul oficial de Facebook */
  text-decoration: none;
}
.fb-link:hover {
  text-decoration: underline;
}
</style>
