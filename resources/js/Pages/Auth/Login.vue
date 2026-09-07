<template>
  <GuestLayout>
    <Head title="Sistema de Nivelación de Ingresantes" />

    <main class="min-h-screen w-full grid grid-cols-1 lg:grid-cols-5">

      <!-- =====================================================
           INFORMACIÓN INSTITUCIONAL
           En móvil aparece SEGUNDO
           En escritorio aparece PRIMERO
      ====================================================== -->
      <section
        class="relative overflow-hidden
               order-2 lg:order-1
               lg:col-span-3
               min-h-[650px]
               lg:min-h-screen"
      >
        <!-- Imagen de fondo -->
        <img
          src="/images/login-office.jpeg"
          alt="Sistema de Nivelación de Ingresantes"
          class="absolute inset-0 w-full h-full object-cover"
        />

        <!-- Fondo azul institucional -->
        <div
          class="absolute inset-0"
          style="
            background:
              linear-gradient(
                120deg,
                rgba(1, 36, 80, 0.97) 0%,
                rgba(8, 73, 140, 0.94) 50%,
                rgba(3, 47, 98, 0.84) 100%
              );
          "
        ></div>

        <!-- Contenido -->
        <div
          class="relative z-10
                 min-h-[650px]
                 lg:min-h-screen
                 flex flex-col
                 px-6 py-8
                 sm:px-10
                 md:px-14
                 lg:px-16
                 xl:px-20
                 lg:py-10
                 text-white"
        >

          <!-- =================================================
               CABECERA
          ================================================== -->
          <div class="flex items-center justify-between gap-5">

            <div class="flex items-center gap-4">

              <div
                class="bg-white rounded-xl shadow-lg
                       flex items-center justify-center
                       p-2
                       w-16 h-16
                       sm:w-20 sm:h-20"
              >
                <img
                  src="/imagenes/logotiny.png"
                  alt="Universidad Nacional del Altiplano"
                  class="max-w-full max-h-full object-contain"
                />
              </div>

              <div>
                <p
                  class="text-xs sm:text-sm
                         uppercase
                         tracking-wide"
                >
                  Universidad Nacional del
                </p>

                <p
                  class="text-sm sm:text-lg
                         font-bold
                         uppercase
                         tracking-wide"
                >
                  Altiplano - Puno
                </p>
              </div>

            </div>

            <!-- Logo Vicerrectorado -->
            <div
              class="hidden sm:flex
                     bg-white
                     rounded-xl
                     shadow-lg
                     px-4 py-2
                     items-center"
            >
              <img
                src="/imagenes/vracad.png"
                alt="Vicerrectorado Académico"
                class="h-12 md:h-14 object-contain"
              />
            </div>

          </div>


          <!-- =================================================
               PRESENTACIÓN
          ================================================== -->
          <div class="mt-10 lg:mt-14 max-w-3xl">

            <div
              class="inline-flex items-center
                     rounded-full
                     px-4 py-2
                     text-xs sm:text-sm
                     font-semibold
                     mb-5"
              style="
                background: rgba(255,255,255,0.14);
                border: 1px solid rgba(255,255,255,0.20);
              "
            >
              <i class="pi pi-building mr-2"></i>
              Vicerrectorado Académico
            </div>

            <h1
              class="font-bold
                     leading-tight
                     text-3xl
                     sm:text-4xl
                     md:text-5xl
                     xl:text-6xl"
            >
              Sistema de Nivelación
              <br class="hidden sm:block" />
              de Ingresantes
            </h1>

            <p
              class="mt-5
                     max-w-2xl
                     leading-relaxed
                     text-sm
                     sm:text-base
                     md:text-lg"
              style="color: rgba(255,255,255,0.92);"
            >
              Plataforma institucional destinada a apoyar la gestión,
              seguimiento y registro del proceso de nivelación de los
              estudiantes ingresantes de la Universidad Nacional del
              Altiplano.
            </p>
          </div>


          <!-- =================================================
               FINALIDAD
          ================================================== -->
          <div
            class="mt-8
                   max-w-3xl
                   rounded-2xl
                   p-5"
            style="
              background: rgba(255,255,255,0.10);
              border: 1px solid rgba(255,255,255,0.16);
              backdrop-filter: blur(5px);
            "
          >
            <div class="flex items-start gap-4">

              <div
                class="flex-shrink-0
                       w-11 h-11
                       rounded-xl
                       flex items-center
                       justify-center"
                style="background: rgba(255,255,255,0.15);"
              >
                <i class="pi pi-graduation-cap text-xl"></i>
              </div>

              <div>
                <h3 class="font-bold text-base sm:text-lg">
                  Finalidad del proceso
                </h3>

                <p
                  class="mt-2
                         text-sm
                         sm:text-base
                         leading-relaxed"
                  style="color: rgba(255,255,255,0.88);"
                >
                  Contribuir a que los estudiantes ingresantes alcancen las
                  competencias previstas en el perfil de ingreso,
                  permitiendo identificar las competencias que requieren
                  nivelación y realizar el seguimiento correspondiente.
                </p>
              </div>

            </div>
          </div>


          

          <!-- =================================================
               PARTICIPANTES
          ================================================== -->
          <div class="mt-8 max-w-3xl">

            <p
              class="text-xs
                     uppercase
                     tracking-wider
                     font-semibold"
              style="color: rgba(255,255,255,0.70);"
            >
              Participan en el proceso
            </p>

            <div
              class="flex
                     flex-wrap
                     gap-2
                     mt-3"
            >
              <span class="etiqueta-proceso">
                <i class="pi pi-building mr-1"></i>
                Escuelas Profesionales
              </span>

              <span class="etiqueta-proceso">
                <i class="pi pi-user mr-1"></i>
                Docentes
              </span>

              <span class="etiqueta-proceso">
                <i class="pi pi-users mr-1"></i>
                Estudiantes
              </span>

              <span class="etiqueta-proceso">
                <i class="pi pi-eye mr-1"></i>
                Supervisión
              </span>
            </div>

          </div>


          <!-- Pie -->
          <div
            class="mt-auto pt-10
                   flex
                   flex-col
                   sm:flex-row
                   sm:items-center
                   sm:justify-between
                   gap-2
                   text-xs"
            style="color: rgba(255,255,255,0.70);"
          >
            <span>
              Universidad Nacional del Altiplano - Puno
            </span>

            <span>
              © 2026
            </span>
          </div>

        </div>
      </section>


      <!-- =====================================================
           LOGIN
           En móvil aparece PRIMERO
           En escritorio aparece a la DERECHA
      ====================================================== -->
      <section
        class="order-1 lg:order-2
               lg:col-span-2
               min-h-screen
               flex
               items-center
               justify-center
               bg-white
               px-6 py-10
               sm:px-10
               md:px-14
               lg:px-12
               xl:px-20"
      >
        <div class="w-full max-w-lg">

          <!-- =================================================
               LOGOS
          ================================================== -->
          <div
            class="flex
                   justify-center
                   items-center
                   gap-5
                   mb-8
                   lg:hidden"
          >
            <img
              src="/imagenes/logotiny.png"
              alt="UNA Puno"
              class="h-16 sm:h-20 object-contain"
            />

            <div
              class="h-12
                     w-px
                     bg-gray-200"
            ></div>

            <img
              src="/imagenes/vracad.png"
              alt="Vicerrectorado Académico"
              class="h-11 sm:h-14 max-w-[190px] object-contain"
            />
          </div>


          <!-- Sistema en escritorio -->
          <div class="hidden lg:block mb-8">
            <div class="flex items-center gap-3">

              <img
                src="/imagenes/logotiny.png"
                alt="UNA Puno"
                class="h-16 object-contain"
              />

              <div>
                <p
                  class="text-xs
                         uppercase
                         tracking-wide
                         text-gray-500"
                >
                  Universidad Nacional del Altiplano
                </p>

                <p
                  class="text-sm
                         font-bold
                         text-gray-700"
                >
                  Vicerrectorado Académico
                </p>
              </div>

            </div>
          </div>


          <!-- =================================================
               TÍTULO LOGIN
          ================================================== -->
          <div class="mb-8">

            <p
              class="text-sm
                     font-bold
                     uppercase
                     tracking-wider
                     mb-2"
              style="color: #155799;"
            >
              Acceso institucional
            </p>

            <h2
              class="text-3xl
                     sm:text-4xl
                     font-bold
                     text-gray-800"
            >
              Iniciar sesión
            </h2>

            <p
              class="text-sm
                     sm:text-base
                     text-gray-500
                     mt-3
                     leading-relaxed"
            >
              Ingrese sus credenciales para acceder al Sistema de
              Nivelación de Ingresantes.
            </p>

          </div>


          <!-- =================================================
               STATUS
          ================================================== -->
          <div
            v-if="status"
            class="mb-5
                   px-4 py-3
                   rounded-lg
                   text-sm
                   font-medium
                   bg-green-50
                   text-green-700
                   border
                   border-green-200"
          >
            {{ status }}
          </div>


          <!-- =================================================
               FORMULARIO
          ================================================== -->
          <form @submit.prevent="submit">

            <!-- Usuario -->
            <div class="mb-5">

              <InputLabel
                for="email"
                value="Correo / Usuario"
                class="mb-2 font-semibold"
              />

              <div class="relative">

                <i
                  class="pi pi-user
                         absolute
                         left-4
                         top-1/2
                         -translate-y-1/2
                         text-gray-400
                         z-10"
                ></i>

                <TextInput
                  id="email"
                  type="text"
                  v-model="form.email"
                  required
                  autofocus
                  autocomplete="username"
                  placeholder="Ingrese su correo o usuario"
                  class="block w-full pl-11"
                />

              </div>

              <InputError
                class="mt-2"
                :message="form.errors.email"
              />

            </div>


            <!-- Contraseña -->
            <div class="mb-5">

              <InputLabel
                for="password"
                value="Contraseña"
                class="mb-2 font-semibold"
              />

              <Password
                id="password"
                v-model="form.password"
                toggleMask
                :feedback="false"
                autocomplete="current-password"
                placeholder="Ingrese su contraseña"
                class="w-full"
                inputClass="w-full"
              />

              <InputError
                class="mt-2"
                :message="form.errors.password"
              />

            </div>


            <!-- Opciones -->
            <div
              class="flex
                     flex-col
                     sm:flex-row
                     sm:items-center
                     sm:justify-between
                     gap-4
                     mt-5"
            >
              <label
                class="flex
                       items-center
                       cursor-pointer"
              >
                <Checkbox
                  name="remember"
                  v-model:checked="form.remember"
                />

                <span class="ml-2 text-sm text-gray-600">
                  Recuérdame
                </span>
              </label>

              <Link
                href="/recover"
                class="text-sm
                       font-semibold
                       hover:underline"
                style="color: #155799;"
              >
                ¿Olvidaste tu contraseña?
              </Link>

            </div>


            <!-- Botón -->
            <button
              type="submit"
              :disabled="form.processing"
              class="w-full
                     mt-8
                     py-3.5
                     px-4
                     rounded-lg
                     text-white
                     text-base
                     font-semibold
                     shadow-sm
                     transition
                     duration-200
                     flex
                     items-center
                     justify-center
                     gap-2
                     hover:opacity-90"
              style="background-color: #155799;"
              :class="{
                'opacity-60 cursor-not-allowed':
                  form.processing
              }"
            >

              <i
                v-if="form.processing"
                class="pi pi-spin pi-spinner"
              ></i>

              <i
                v-else
                class="pi pi-sign-in"
              ></i>

              {{
                form.processing
                  ? 'Ingresando...'
                  : 'Iniciar sesión'
              }}

            </button>

          </form>


          <!-- =================================================
               INFORMACIÓN DE ACCESO
          ================================================== -->
          <div
            class="mt-9
                   pt-6
                   border-t
                   border-gray-200"
          >

            <div class="flex items-start gap-3">

              <div
                class="flex-shrink-0
                       w-9 h-9
                       rounded-full
                       flex
                       items-center
                       justify-center"
                style="background-color: #eef6ff;"
              >
                <i
                  class="pi pi-info-circle"
                  style="color: #155799;"
                ></i>
              </div>

              <div>
                <p
                  class="text-xs
                         sm:text-sm
                         text-gray-600
                         leading-relaxed"
                >
                  El acceso está destinado a usuarios autorizados que
                  participan en el proceso de Nivelación de Ingresantes.
                </p>

                <p
                  class="text-xs
                         text-gray-400
                         mt-2"
                >
                  Directores, coordinadores, docentes, estudiantes,
                  supervisores y administradores.
                </p>
              </div>

            </div>

          </div>


          <!-- =================================================
               MÓVIL: INFORMACIÓN BREVE
          ================================================== -->
          <div
            class="lg:hidden
                   mt-7
                   rounded-xl
                   p-4
                   bg-gray-50
                   border
                   border-gray-200"
          >
            <p
              class="font-semibold
                     text-sm
                     text-gray-700"
            >
              Sistema de Nivelación de Ingresantes
            </p>

            <p
              class="text-xs
                     text-gray-500
                     mt-2
                     leading-relaxed"
            >
              El sistema permite gestionar competencias de ingreso,
              estudiantes, docentes, notas y el seguimiento del proceso
              de nivelación.
            </p>
          </div>


          <!-- Pie -->
          <div class="text-center mt-8">

            <p
              class="text-xs
                     font-semibold
                     text-gray-500"
            >
              Vicerrectorado Académico
            </p>

            <p
              class="text-xs
                     text-gray-400
                     mt-1"
            >
              Universidad Nacional del Altiplano - Puno
            </p>

            <p
              class="text-xs
                     text-gray-400
                     mt-1"
            >
              © 2026
            </p>

          </div>

        </div>
      </section>

    </main>
  </GuestLayout>
</template>


<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

import Password from 'primevue/password';

import {
  Head,
  Link,
  useForm
} from '@inertiajs/vue3';

import 'primeicons/primeicons.css';


defineProps({
  canResetPassword: Boolean,
  status: String
});


const form = useForm({
  email: '',
  password: '',
  remember: false
});


const submit = () => {

  form.post(route('login'), {

    onFinish: () => {
      form.reset('password');
    }

  });

};
</script>


<style scoped>
.etiqueta-proceso {
  display: inline-flex;
  align-items: center;
  padding: 7px 11px;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
  background: rgba(255, 255, 255, 0.10);
  border: 1px solid rgba(255, 255, 255, 0.15);
  color: white;
}
</style>
