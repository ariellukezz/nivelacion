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

      <!-- <div style="border-bottom: 1px solid #00000033; margin-top: 10px;"></div> -->

      <!-- <div class="flex" style="align-items: center; justify-content: flex-start; margin-left: 34px;">
          <div style="color: white; margin-top: 0px; margin-left: -10px;"> -->
            <!-- <div style="font-size: 0.8rem; justify-content: center; padding-right: 10px; text-align: center;" >Facultad de mecánica Electrica, Electrónica y Sistemas</div> -->
            <!-- <div style="font-size: 0.6rem; letter-spacing: .1rem; justify-content: center; margin-top: 10px; padding-right: 10px; text-align: center;" >Programa de estudios</div> -->
            <!-- <div style="font-size: 0.85rem; letter-spacing: .0rem; justify-content: center; margin-top: 10px; padding-right: 10px; text-align: left; text-transform: uppercase; ">BIOLOGÍA: MICROBIOLOGÍA Y LABORATORIO CLÍNICO</div>
          </div>
      </div>
      <div style="border-bottom: 1px solid #f4f4f443; margin-top: 10px;">
      </div> -->

      <ul class="mt-6 text-gray-100 " style="color: ;">
        <div class="ml-6" style="margin-top: 40px; margin-bottom: 10px;"> <span style="font-weight: 700; letter-spacing: normal; font-family: Arial, Helvetica, sans-serif;"> INICIO </span> </div>
        <li class="relative px-6 py-3" :class="[route().current('dashboard')? 'activado':'']">
          <NavLink :href="route('dashboard')" :class="[route().current('dashboard')? 'borderlef':'']">
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

        <!-- <li class="relative px-6 py-3" :class="[route().current('coordinador')? 'activado':'']">
          <NavLink :href="route('coordinador')">
            <template #icon>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                   xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </template>
            Coordinadores
          </NavLink>
        </li> -->

        <li class="relative px-6 py-3" :class="[route().current('encargado-index')? 'activado':'']">
          <NavLink :href="route('encargado-index')">
            <template #icon>
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-book-open"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
            </template>
            Encargado nivelación
          </NavLink>
        </li>

        <li class="relative px-6 py-3" :class="[route().current('coordinador-estudiante')? 'activado':'']">
          <NavLink :href="route('coordinador-estudiante')">
            <template #icon>
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </template>
            Estudiantes
          </NavLink>
        </li>

        <li class="relative px-6 py-3" :class="[route().current('coordinador-docente')? 'activado':'']">
          <NavLink :href="route('coordinador-docente')">
            <template #icon>
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v12H4z"></path><path d="M12 16v4"></path><path d="M8 20h8"></path></svg>
            </template>
            Docentes
          </NavLink>
        </li>

        <div style="border-bottom: 1px solid #f4f4f433; margin-top: 10px; margin-bottom: 20PX;"></div>
        <div class="ml-6" style="margin-bottom: 10px;"> <span style="font-weight: 700; letter-spacing: normal; font-family: Arial, Helvetica, sans-serif;"> CONFIGURACIÓN </span> </div>
        <li class="relative px-6 py-3" :class="[route().current('coordinador-asignacion')? 'activado':'']">
          <NavLink :href="route('coordinador-asignacion')">
            <template #icon>
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"></path></svg>
            </template>
            Asignación docentes
          </NavLink>
        </li>

        <li class="relative px-6 py-3" :class="[route().current('about')? 'activado':'']">
          <NavLink :href="route('about')">
            <template #icon>
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            </template>
            Documentos
          </NavLink>
        </li>

        <li class="relative px-6 py-3" :class="[route().current('notas-perfiles')? 'activado':'']">
          <NavLink :href="route('notas-perfiles')">
            <template #icon>
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4h14v16H5z"></path><path d="M9 4v16"></path><path d="M15 10h2"></path><path d="M15 14h2"></path></svg>
            </template>
            Notas perfil
          </NavLink>
        </li>






        <!-- Agrupar en un submenú: Data Nivelación -->
<li class="relative px-6 py-3"
    :class="[(route().current('data-ingreso') || route().current('reprobados-nivelacion')) ? 'activado' : '']">

  <!-- Botón/encabezado del submenú -->
  <button type="button"
          class="w-full flex items-center justify-between focus:outline-none"
          @click="isDataOpen = !isDataOpen">
    <div class="flex items-center">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"></path><path d="M18 17V9"></path><path d="M13 17V5"></path><path d="M8 17v-3"></path></svg>

      <span class="ml-3">Data Nivelación</span>
    </div>

    <!-- Flechita que rota cuando abre -->
    <svg :class="['w-4 h-4 transform transition-transform', isDataOpen ? 'rotate-90' : '']"
         fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 5l7 7-7 7" />
    </svg>
  </button>

  <!-- Contenido del submenú -->
  <transition name="fade" mode="out-in">
    <ul v-show="isDataOpen" class="mt-2 ml-6 space-y-1">
      <li :class="[route().current('data-ingreso') ? 'activado' : '']">
        <NavLink :href="route('data-ingreso')">
          <template #icon>
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 4h11a3 3 0 0 1 3 3v13H7.5A3.5 3.5 0 0 1 4 16.5V4z"></path><path d="M17 20V7a3 3 0 0 0-3-3"></path></svg>
          </template>
          Notas Ingreso
        </NavLink>
      </li>

      <li :class="[route().current('reprobados-nivelacion') ? 'activado' : '']">
        <NavLink :href="route('reprobados-nivelacion')">
          <template #icon>
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12" y2="17"></line></svg>
          </template>
          Notas Desaprobados
        </NavLink>
      </li>
    </ul>
  </transition>
</li>









      </ul>
    </div>
  </aside>
</template>

<script>
import NavLink from '@/Components/NavLink.vue'
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue'

// export default {
//   components: {
//     NavLink,
//     Link,
//   },

//   setup() {
//     let showingTwoLevelMenu = ref(false)

//     return {
//       showingTwoLevelMenu
//     }
//   },
// }




export default {
  components: { NavLink, Link },

  setup() {
    const defaultOpen =
      route().current('data-ingreso') || route().current('reprobados-nivelacion')

    const isDataOpen = ref(defaultOpen)
    const showingTwoLevelMenu = ref(false)

    return {
      showingTwoLevelMenu,
      isDataOpen,
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
