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
        <li class="relative px-6 py-3" :class="[route().current('superadmi-inicio')? 'activado':'']">
          <NavLink :href="route('superadmi-inicio')" :class="[route().current('superadmi-inicio')? 'borderlef':'']">
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

        <li class="relative px-6 py-3" :class="[route().current('superadmi-usuarios')? 'activado':'']">
          <NavLink :href="route('superadmi-usuarios')" :active="route().current('superadmi-usuarios')">
            <template #icon>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                   xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </template>
            Usuarios restablecer
          </NavLink>
        </li>


        <li class="relative px-6 py-3" :class="[route().current('superadmi-estudiante')? 'activado':'']">
          <NavLink :href="route('superadmi-estudiante')" :active="route().current('superadmi-estudiante')">
            <template #icon>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                   xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </template>
            Estudiantes
          </NavLink>
        </li>

        <li class="relative px-6 py-3" :class="[route().current('superadmi-docentes')? 'activado':'']">
          <NavLink :href="route('superadmi-docentes')" :active="route().current('superadmi-docentes')">
            <template #icon>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                   xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </template>
            Docentes
          </NavLink>
        </li>


        <li class="relative px-6 py-3" :class="[route().current('superadmi-avance')? 'activado':'']">
          <NavLink :href="route('superadmi-avance')" :active="route().current('superadmi-avance')">
            <template #icon>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                   xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </template>
            Documentos
          </NavLink>
        </li>


        <li class="relative px-6 py-3" :class="[route().current('pregun')? 'activado':'']">
          <NavLink :href="route('pregun')" :active="route().current('pregun')">
            <template #icon>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                   xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </template>
            Preguntas
          </NavLink>
        </li>



        <li class="relative px-6 py-3" :class="[route().current('superadmi-documento')? 'activado':'']">
          <NavLink :href="route('superadmi-documento')" :active="route().current('superadmi-documento')">
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

        <li class="relative px-6 py-3" :class="[route().current('notas-todosest')? 'activado':'']">
          <NavLink :href="route('notas-todosest')" :active="route().current('notas-todosest')">
            <template #icon>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                   xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </template>
            final notas perfil
          </NavLink>
        </li>

        <li class="relative px-6 py-3" :class="[route().current('info_est')? 'activado':'']">
          <NavLink :href="route('info_est')" :active="route().current('info_est')">
            <template #icon>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                   xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </template>
            Información Estudiante
          </NavLink>
        </li>

        <div style="border-bottom: 1px solid #f4f4f433; margin-top: 10px; margin-bottom: 20PX;"></div>
        <div class="ml-6" style="margin-bottom: 10px;"> <span style="font-weight: 700; letter-spacing: normal; font-family: Arial, Helvetica, sans-serif;"> CONFIGURACIÓN </span> </div>
        <li class="relative px-6 py-3" :class="[route().current('asignacion-superadmi')? 'activado':'']">
          <NavLink :href="route('asignacion-superadmi')">
            <template #icon>
              <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round"
                   stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                <path
                    d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
              </svg>
            </template>
            Asignación docentes
          </NavLink>
        </li>


        <li class="relative px-6 py-3" :class="[route().current('super-docente')? 'activado':'']">
          <NavLink :href="route('super-docente')">
            <template #icon>
              <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke-linecap="round"
                   stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                <path
                    d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
              </svg>
            </template>
            Super docente
          </NavLink>
        </li>

        <!-- <li class="relative px-6 py-3" :class="[route().current('superadmi-inicio')? 'activado':'']">
          <NavLink :href="route('superadmi-inicio')" :active="route().current('superadmi-inicio')">
            <template #icon>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                   xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </template>
            Avance
          </NavLink>
        </li> -->
<!--
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
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue'

export default {
  components: {
    NavLink,
    Link,
  },

  setup() {
    let showingTwoLevelMenu = ref(false)

    return {
      showingTwoLevelMenu
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
