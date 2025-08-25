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
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
            </svg>
            </template>
             Restablecer Usuarios
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
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            </template>
            Docentes
          </NavLink>
        </li>


        <li class="relative px-6 py-3" :class="[route().current('superadmi-avance')? 'activado':'']">
          <NavLink :href="route('superadmi-avance')" :active="route().current('superadmi-avance')">
            <template #icon>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            </template>
            Documentos
          </NavLink>
        </li>


        <li class="relative px-6 py-3" :class="[route().current('pregun')? 'activado':'']">
          <NavLink :href="route('pregun')" :active="route().current('pregun')">
            <template #icon>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            </template>
            Preguntas
          </NavLink>
        </li>



        <li class="relative px-6 py-3" :class="[route().current('superadmi-documento')? 'activado':'']">
          <NavLink :href="route('superadmi-documento')" :active="route().current('superadmi-documento')">
            <template #icon>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
</svg>
            </template>
            Avance
          </NavLink>
        </li>

        <!-- <li class="relative px-6 py-3" :class="[route().current('periodos')? 'activado':'']">
          <NavLink :href="route('periodos')" :active="route().current('periodos')">
            <template #icon>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                   xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </template>
            Periodo
          </NavLink>
        </li>

        <li class="relative px-6 py-3" :class="[route().current('superadmi.notas.actualizar')? 'activado':'']">
          <NavLink :href="route('superadmi.notas.actualizar')" :active="route().current('superadmi.notas.actualizar')">
            <template #icon>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                   xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </template>
            actualizar
          </NavLink>
        </li> -->

        <li class="relative px-6 py-3" :class="[route().current('notas-todosest')? 'activado':'']">
          <NavLink :href="route('notas-todosest')" :active="route().current('notas-todosest')">
            <template #icon>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
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
             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
            </svg>
            </template>
            Super docente
          </NavLink>
        </li>

        <!-- Menú desplegable para Configuración de Periodo -->
    <div style="border-bottom: 1px solid #f4f4f433; margin-top: 10px; margin-bottom: 20PX;"></div>
      <div class="ml-6" style="margin-bottom: 10px;">
        <button
          @click="toggleMenu('periodo')"
          class="flex items-center w-full text-left focus:outline-none"
          style="font-weight: 700; letter-spacing: normal; font-family: Arial, Helvetica, sans-serif;"
        >
          <span>CONFIG. PERIODO</span>
          <svg
            class="w-4 h-4 ml-1 transition-transform duration-200"
            :class="{ 'transform rotate-90': isMenuOpen.periodo }"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </button>

        <ul v-show="shouldShowMenu('periodo')" class="mt-2 ml-2">
          <li class="relative px-6 py-3" :class="[route().current('periodos')? 'activado':'']">
            <NavLink :href="route('periodos')" :active="route().current('periodos')">
              <template #icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
              </template>
              Periodo
            </NavLink>
          </li>

          <li class="relative px-6 py-3" :class="[route().current('eventos')? 'activado':'']">
            <NavLink :href="route('eventos')" :active="route().current('eventos')">
              <template #icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
              </template>
              eventos
            </NavLink>
          </li>

          <li class="relative px-6 py-3" :class="[route().current('superadmi.notas.actualizar')? 'activado':'']">
            <NavLink :href="route('superadmi.notas.actualizar')" :active="route().current('superadmi.notas.actualizar')">
              <template #icon>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
              </template>
              Actualizar
            </NavLink>
          </li>
          <!-- <li class="relative px-6 py-3" :class="[route().current('alumnos-importar') ? 'activado' : '']">
        <NavLink :href="route('alumnos-importar')" :active="route().current('alumnos-importar')">
            <template #icon>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            </template>
            Importar
        </NavLink>
        </li> -->


        </ul>
      </div>

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
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'

export default {
  components: {
    NavLink,
    Link,
  },

  setup() {
    const showingTwoLevelMenu = ref(false)
    const mobileMenuOpen = ref(false)

    // Estado para múltiples menús desplegables
    const isMenuOpen = ref({
      periodo: false,
      // agregar otros menús aquí si es necesario
    })

    // Función para determinar si un menú debe mostrarse
    const shouldShowMenu = (menuKey) => {
      // Mostrar si está abierto manualmente o si está en una de sus rutas
      return isMenuOpen.value[menuKey] ||
             (menuKey === 'periodo' &&
              (route().current('periodos') || route().current('superadmi.notas.actualizar')))
    }

    // Alternar menú
    const toggleMenu = (menuKey) => {
      isMenuOpen.value[menuKey] = !isMenuOpen.value[menuKey]
    }

    // Cerrar menú móvil al navegar
    router.on('navigate', () => {
      mobileMenuOpen.value = false
    })

    // Abrir automáticamente el menú si está en una de sus rutas
    watch(() => route().current(), (newRoute) => {
      if (newRoute === 'periodos' || newRoute === 'superadmi.notas.actualizar') {
        isMenuOpen.value.periodo = true
      }
    }, { immediate: true })

    return {
      showingTwoLevelMenu,
      isMenuOpen,
      toggleMenu,
      shouldShowMenu,
      mobileMenuOpen
    }
  },
}
</script>

<style scoped>
.activado {
  background: #5f08d131;
  background-color: var(--primary-color);
  color: white;
}
.borderlef {
  border-left: 3px solid rgb(221, 24, 2);
}

/* Transiciones para el menú móvil */
.transform {
  transition: transform 0.2s ease-in-out;
}

/* Estilo para el submenú */
ul ul {
  transition: all 0.3s ease;
  max-height: 500px; /* Ajusta según necesidad */
  overflow: hidden;
}

/* Estilo para el botón del menú desplegable */
button:hover {
  color: #ddd;
}

/* Mejoras para móvil */
@media (max-width: 767px) {
  .mobile-menu {
    width: 80%;
  }
}
</style>
