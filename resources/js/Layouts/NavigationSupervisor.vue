<template>
  <aside  class="z-20 hidden w-64 overflow-y-auto md:block flex-shrink-0" style="background: #3E5F8A; color: #FFFFFF; width: 230px; border-right: 1px solid #00000010;">


    <div class="text-gray-200">
      <Link class="text-lg font-bold text-gray-100" :href="route('dashboard')">
        <div class="flex" style="align-items: center; margin-left: 20px; margin-top: 5px;">
            <div style="margin-top: 6px;">
              <img src="../../assets/imagenes/logotiny.png" width="30"/>
            </div>
            <div style="width: 1px; background-color: gray; margin: 0px 10px; height: 30px; margin-top: 8px;"></div>
            <div style="margin-top: 12px;">
              <span style="font-size: 1.2rem; font-weight: 700; letter-spacing: .300rem; font-family: Arial, Helvetica, sans-serif">VRA-UNA</span>
                <div style="margin-top: -16px !important; "><span style="font-size: .63rem; letter-spacing: .38rem; font-weight: 500; font-family: Arial, Helvetica, sans-serif">NIVELACIÓN</span></div>
                <!-- <div style=" margin-top: -12px; font-size: 0.9rem; letter-spacing: 0.03rem; font-weight: 500;">nivelación - académica</div> -->
            </div>
        </div>
      </Link>

      <ul class="mt-6 text-gray-100 " style="color: ;">
        <div class="ml-6" style="margin-top: 40px; margin-bottom: 10px;"> <span style="font-weight: 700; letter-spacing: normal; font-family: Arial, Helvetica, sans-serif;"> INICIO </span> </div>
        <li class="relative px-6 py-3" :class="[route().current('supervisor-inicio')? 'activado':'']">
          <NavLink :href="route('supervisor-inicio')" :class="[route().current('supervisor-inicio')? 'borderlef':'']">
            <template #icon>
              <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round"
                   stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                <path
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
              </svg>
            </template>
            <div>
              Inicio
            </div>

          </NavLink>
        </li>

        <li class="relative px-6 py-3" :class="[route().current('supervisor-asignacion')? 'activado':'']">
          <NavLink :href="route('supervisor-asignacion')" :active="route().current('supervisor-asignacion')">
            <template #icon>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                   xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </template>
            documento Cargados
          </NavLink>
        </li>


        <li class="relative px-6 py-3" :class="[route().current('encargados-sistemas')? 'activado':'']">
          <NavLink :href="route('encargados-sistemas')" :active="route().current('encargados-sistemas')">
            <template #icon>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                   xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </template>
            Encargados Nivelacion
          </NavLink>
        </li>



        <li class="relative px-6 py-3" :class="[route().current('supervisor-avance')? 'activado':'']">
          <NavLink :href="route('supervisor-avance')" :active="route().current('supervisor-avance')">
            <template #icon>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                   xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </template>
            Avance
          </NavLink>
        </li>


        <li class="relative px-6 py-3" :class="[route().current('notas-todosest-supervisor')? 'activado':'']">
          <NavLink :href="route('notas-todosest-supervisor')" :active="route().current('notas-todosest-supervisor')">
            <template #icon>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                   xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </template>
            notas perfil
          </NavLink>
        </li>

        <li class="relative px-6 py-3" :class="[route().current('docentes-competencias')? 'activado':'']">
          <NavLink :href="route('docentes-competencias')" :active="route().current('docentes-competencias')">
            <template #icon>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                   xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </template>
            Competencias y Docentes
          </NavLink>
        </li>

        <li class="relative px-6 py-3" :class="[route().current('busqueda-estudiantes')? 'activado':'']">
          <NavLink :href="route('busqueda-estudiantes')" :active="route().current('busqueda-estudiantes')">
            <template #icon>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                   xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </template>
            Buscar Estudiante
          </NavLink>
        </li>


        <li class="relative px-6 py-3" :class="[route().current('controles')? 'activado':'']">
          <NavLink :href="route('controles')" :active="route().current('controles')">
            <template #icon>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                   xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </template>
            Controles
          </NavLink>
        </li>



        <li class="relative px-6 py-3" :class="[route().current('notificaciones')? 'activado':'']">
          <NavLink :href="route('notificaciones')" :active="route().current('notificaciones')">
            <template #icon>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                   xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </template>
            Notificaicones
          </NavLink>
        </li>



<!-- Reemplaza los tres <li> por este bloque -->
<li
  class="relative px-6 py-3"
  :class="[
    route().current('ingresos') || route().current('sorteo') || route().current('lista.ganadores')
      ? 'activado'
      : ''
  ]"
>
  <!-- Botón cabecera -->
  <button
    type="button"
    class="w-full flex items-center justify-between"
    @click="induccionOpen = !induccionOpen"
    :aria-expanded="induccionOpen ? 'true' : 'false'"
  >
    <div class="flex items-center gap-3">
      <!-- Icono de carpeta/menu -->
      <svg class="w-5 h-5" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 7h18M3 12h18M3 17h18"/>
      </svg>
      <span>INDUCCIÓN</span>
    </div>

    <!-- Chevron -->
    <svg
      class="w-4 h-4 transition-transform duration-200"
      :class="induccionOpen ? 'transform rotate-180' : ''"
      fill="none" viewBox="0 0 24 24" stroke="currentColor"
    >
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M19 9l-7 7-7-7"/>
    </svg>
  </button>

  <!-- Submenú -->
  <ul
    v-show="induccionOpen"
    class="mt-2 ml-4 border-l border-white/20"
    style="padding-left: 12px;"
  >
    <li class="mt-1">
      <NavLink :href="route('ingresos')" :active="route().current('ingresos')">
        <template #icon>
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
               xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
          </svg>
        </template>
        Registrar Asistencia
      </NavLink>
    </li>

    <li class="mt-1">
      <NavLink :href="route('sorteo')" :active="route().current('sorteo')">
        <template #icon>
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
               xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
          </svg>
        </template>
        sorteo
      </NavLink>
    </li>

    <li class="mt-1">
      <NavLink :href="route('lista.ganadores')" :active="route().current('lista.ganadores')">
        <template #icon>
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
               xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
          </svg>
        </template>
        Descargar sorteados
      </NavLink>
    </li>
  </ul>
</li>

<!-- supervisor-docentes-competencias
        <li class="relative px-6 py-3" :class="[route().current('docente-curso')? 'activado':'']">
          <NavLink :href="route('docente-curso')" :active="route().current('docente-curso')">
            <template #icon>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                   xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </template>
            Cursos
          </NavLink>
        </li> -->

        <div style="border-bottom: 1px solid #f4f4f433; margin-top: 10px; margin-bottom: 20PX;"></div>
        <!-- <div class="ml-6" style="margin-bottom: 10px;"> <span style="font-weight: 700; letter-spacing: normal; font-family: Arial, Helvetica, sans-serif;"> CONFIGURACIÓN </span> </div>

        <li class="relative px-6 py-3" :class="[route().current('about')? 'activado':'']">
          <NavLink :href="route('about')" :active="route().current('about')">
            <template #icon>
              <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round"
                   stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                <path
                    d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
              </svg>
            </template>
            Revisar Documentos
          </NavLink>
        </li> -->

  <!--
          <li class="relative px-6 py-3" :class="[route().current('docente-encuestas')? 'activado':'']">
            <NavLink :href="route('docente-encuestas')" :class="[route().current('docente-encuestas')? 'borderlef':'']">
              <template #icon>
                <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round"
                    stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                  <path
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
              </template>
              <div>
              Encuesta
              </div>

            </NavLink>
          </li> -->

      </ul>
    </div>
  </aside>
</template>

<script>
import NavLink from '@/Components/NavLink.vue'
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue'

export default {
  components: { NavLink, Link },
  setup() {
    // ya tenías esto
    const showingTwoLevelMenu = ref(false)

    // 👇 agrega esto
    const induccionOpen = ref(
      // si quieres que abra cuando alguna ruta hija está activa:
      !!(route().current('ingresos') || route().current('sorteo') || route().current('lista.ganadores'))
    )

    // 👇 IMPORTANTE: retornarlo
    return {
      showingTwoLevelMenu,
      induccionOpen,
    }
  },
}
</script>


<style scoped>
.activado{
  background: #5f08d131;
  background-color: var(--primary-color);
  color: white;
}
.borderlef{
  border-left: 3px solid rgb(221, 24, 2);
}
</style>
