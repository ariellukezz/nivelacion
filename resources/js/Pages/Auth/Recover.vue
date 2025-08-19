<template>
  <div class="max-w-xl mx-auto p-6">
    <h1 class="text-xl font-bold mb-4">Recuperar contraseña</h1>

    <!-- Paso 1 -->
    <Card class="mb-4">
      <template #content>
        <form @submit.prevent="buscar">
          <div class="mb-3">
            <label class="block mb-1">Tipo de usuario</label>
            <div class="flex gap-4">
              <div class="flex items-center gap-2">
                <RadioButton inputId="alumno" value="alumno" v-model="form.tipo" />
                <label for="alumno">Alumno</label>
              </div>
              <div class="flex items-center gap-2">
                <RadioButton inputId="docente" value="docente" v-model="form.tipo" />
                <label for="docente">Docente</label>
              </div>
            </div>
            <small class="p-error" v-if="errors.tipo">{{ errors.tipo }}</small>
          </div>

          <div class="mb-3">
            <label class="block mb-1">{{ form.tipo === 'docente' ? 'DNI del docente' : 'Código del alumno' }}</label>
            <InputText v-model="form.identificador" class="w-full" placeholder="Ingrese DNI o código" />
            <small class="p-error" v-if="errors.identificador">{{ errors.identificador }}</small>
          </div>

          <Button type="submit" label="Buscar" :loading="form.processing" />
        </form>
      </template>
    </Card>

    <!-- Paso 2 -->
    <Card v-if="found" class="mb-3">
      <template #content>
        <p class="mb-2">Encontramos un usuario con correo: <strong>{{ emailMasked }}</strong></p>
        <div class="flex flex-col gap-2">
          <!-- <Button label="Enviar enlace de reseteo (recomendado)" @click="enviar('link')" :loading="sendForm.processing" /> -->
          <Button label="Enviar contraseña temporal" severity="secondary" outlined @click="enviar('password')" :loading="sendForm.processing" />
        </div>
      </template>
    </Card>

    <Message v-if="$page.props.flash?.status" severity="success" :closable="false" class="mb-2">
      {{ $page.props.flash.status }}
    </Message>

    <Message v-if="serverError" severity="error" :closable="false" class="mb-2">
      {{ serverError }}
    </Message>

    <div class="mt-4">
      <Link href="/login" class="underline">Volver al inicio de sesión</Link>
    </div>
  </div>
</template>

<script setup>
import { useForm, usePage, Link } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import Card from 'primevue/card'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import RadioButton from 'primevue/radiobutton'
import Message from 'primevue/message'

const page = usePage()

const form = useForm({
  tipo: '',
  identificador: ''
})
const errors = form.errors

const found = ref(false)
const emailMasked = ref('')
const lookupToken = ref('')
const serverError = ref(null)

const buscar = async () => {
  serverError.value = null
  found.value = false
  await form.post('/recover/lookup', {
    onSuccess: () => {
      const flash = page.props.flash || {}
      if (flash.found) {
        found.value = true
        emailMasked.value = flash.email_masked
        lookupToken.value = flash.lookup_token
      }
    }
  })
}

const sendForm = useForm({
  lookup_token: '',
  modo: 'link'
})

watch(lookupToken, v => (sendForm.lookup_token = v))

const enviar = async (modo) => {
  serverError.value = null
  sendForm.modo = modo
  await sendForm.post('/recover/send', {
    preserveScroll: true,
    onSuccess: () => {
      found.value = false
      emailMasked.value = ''
      lookupToken.value = ''
    },
    onError: () => {
      const errs = sendForm.errors
      if (errs && Object.keys(errs).length) {
        serverError.value = Object.values(errs)[0]
      }
    }
  })
}
</script>
