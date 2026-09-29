<template>
  <div class="pagina">
    <Head title="Servicio Psicopedagógico" />

    <div class="contenedor">
      <header class="hero">
        <div class="hero-icon">
          <i class="pi pi-heart-fill"></i>
        </div>
        <div>
          <h1>Servicio Psicopedagógico</h1>
          <p>Registro de atención y seguimiento estudiantil</p>
        </div>
      </header>

      <Message
        v-if="mensaje"
        :severity="mensajeTipo"
        :closable="true"
        class="mb-3"
        @close="mensaje = ''"
      >
        {{ mensaje }}
      </Message>

      <!-- BÚSQUEDA -->
      <section class="bloque bloque-busqueda">
        <div class="titulo-seccion">
          <div class="numero azul">1</div>
          <div>
            <h2>Buscar estudiante</h2>
            <p>Ingrese el código universitario o DNI.</p>
          </div>
        </div>

        <div class="busqueda-row">
          <InputText
            v-model="textoBusqueda"
            placeholder="Código o DNI del estudiante"
            class="busqueda-input"
            @keyup.enter="buscarEstudiante"
          />
          <Button
            label="Buscar estudiante"
            icon="pi pi-search"
            :loading="buscando"
            @click="buscarEstudiante"
          />
        </div>

        <div v-if="estudiantePadron" class="estudiante-card">
          <div class="estudiante-avatar">
            <i class="pi pi-user"></i>
          </div>

          <div class="estudiante-info">
            <h3>{{ estudiantePadron.nombre_completo }}</h3>

            <div class="datos-mini">
              <span><strong>Código:</strong> {{ estudiantePadron.codigo }}</span>
              <span><strong>DNI:</strong> {{ estudiantePadron.dni }}</span>
              <span><strong>Sexo:</strong> {{ estudiantePadron.sexo }}</span>
              <span><strong>Edad:</strong> {{ estudiantePadron.edad }}</span>
              <span><strong>Ciclo:</strong> {{ estudiantePadron.ciclo }}</span>
            </div>

            <p class="escuela">
              {{ estudiantePadron.facultad }} · {{ estudiantePadron.escuela_profesional }}
            </p>

            <p v-if="estudiantePadron.programa" class="programa">
              Programa: {{ estudiantePadron.programa }}
            </p>
          </div>

          <div class="sesion-badge">
            <small>Nueva atención</small>
            <strong>Sesión {{ numeroSesion }}</strong>
          </div>
        </div>
      </section>

      <!-- HISTORIAL -->
      <section v-if="estudiantePadron && historial.length" class="bloque historial-bloque">
        <div class="titulo-seccion">
          <div class="numero morado">2</div>
          <div>
            <h2>Atenciones anteriores</h2>
            <p>
              El estudiante registra {{ historial.length }}
              {{ historial.length === 1 ? 'atención anterior' : 'atenciones anteriores' }}.
            </p>
          </div>
        </div>

        <div class="historial-lista">
          <article
            v-for="atencion in historial"
            :key="atencion.id"
            class="historial-item"
          >
            <div class="historial-head">
              <div>
                <span class="session-pill">Sesión {{ atencion.numero_sesion }}</span>
                <strong>{{ formatearFecha(atencion.fecha_atencion) }}</strong>
              </div>
              <span class="tipo-pill">
                {{ atencion.tipo_atencion === 'VIRTUAL' ? 'Virtual' : 'Presencial' }}
              </span>
            </div>

            <div class="historial-profesional">
              <i class="pi pi-user-edit"></i>
              Atendido por: <strong>{{ atencion.profesional }}</strong>
            </div>

            <div class="historial-resumen">
              <div v-if="atencion.presuncion_diagnostica?.length">
                <span class="mini-label">Presunción diagnóstica</span>
                <div class="chips">
                  <span
                    v-for="item in atencion.presuncion_diagnostica"
                    :key="item"
                    class="chip chip-blue"
                  >
                    {{ item }}
                  </span>
                </div>
              </div>

              <div v-if="atencion.problemas_academicos?.length">
                <span class="mini-label">Dificultades académicas</span>
                <div class="chips">
                  <span
                    v-for="item in atencion.problemas_academicos"
                    :key="item"
                    class="chip chip-orange"
                  >
                    {{ item }}
                  </span>
                </div>
              </div>
            </div>

            <div class="historial-actions">
              <Button
                :label="detalleAbierto === atencion.id ? 'Ocultar detalle' : 'Ver detalle'"
                :icon="detalleAbierto === atencion.id ? 'pi pi-eye-slash' : 'pi pi-eye'"
                text
                size="small"
                @click="toggleDetalle(atencion.id)"
              />
              <Button
                label="Tomar como base"
                icon="pi pi-copy"
                size="small"
                severity="secondary"
                outlined
                @click="usarComoBase(atencion)"
              />
            </div>

            <div v-if="detalleAbierto === atencion.id" class="detalle-anterior">
              <div v-if="atencion.condicion_academica?.length">
                <strong>Condición académica:</strong>
                {{ atencion.condicion_academica.join(', ') }}
              </div>
              <div>
                <strong>Discapacidad:</strong>
                {{ textoDiscapacidad(atencion.discapacidad) }}
              </div>
              <div v-if="atencion.otro_diagnostico">
                <strong>Otro diagnóstico:</strong>
                {{ atencion.otro_diagnostico }}
              </div>
              <div v-if="atencion.otro_problema_academico">
                <strong>Otro problema académico:</strong>
                {{ atencion.otro_problema_academico }}
              </div>
              <div v-if="atencion.observaciones">
                <strong>Observaciones:</strong>
                {{ atencion.observaciones }}
              </div>
              <div v-if="atencion.derivacion">
                <strong>Derivación:</strong>
                {{ atencion.derivacion }}
              </div>
              <div v-if="atencion.seguimiento">
                <strong>Seguimiento:</strong>
                {{ atencion.seguimiento }}
              </div>
            </div>
          </article>
        </div>

        <div class="nuevo-limpio">
          <i class="pi pi-info-circle"></i>
          <span>
            Si no desea reutilizar información anterior, continúe con una atención nueva usando solo los datos del padrón.
          </span>
          <Button
            label="Nueva atención limpia"
            icon="pi pi-file"
            text
            @click="nuevaAtencionLimpia"
          />
        </div>
      </section>

      <form v-if="estudiantePadron" @submit.prevent="guardar">
        <!-- PROFESIONAL -->
        <section class="bloque">
          <div class="titulo-seccion">
            <div class="numero verde">{{ historial.length ? '3' : '2' }}</div>
            <div>
              <h2>Profesional responsable</h2>
              <p>Seleccione al psicólogo que realiza esta atención.</p>
            </div>
          </div>

          <div class="grid">
            <div class="field col-12 md:col-6">
              <label>Psicólogo que realiza la atención *</label>
              <Dropdown
                v-model="form.id_profesional"
                :options="profesionales"
                optionLabel="nombre"
                optionValue="id"
                placeholder="Seleccione su nombre"
                filter
                class="w-full"
              />
            </div>

            <div class="field col-12 md:col-3">
              <label>Fecha de atención *</label>
              <InputText v-model="form.fecha_atencion" type="date" class="w-full" />
            </div>

            <div class="field col-12 md:col-3">
              <label>Semestre académico *</label>
              <InputText v-model="form.semestre_academico" class="w-full" readonly />
            </div>
          </div>
        </section>

        <!-- DATOS -->
        <section class="bloque">
          <div class="titulo-seccion">
            <div class="numero celeste">{{ historial.length ? '4' : '3' }}</div>
            <div>
              <h2>Datos del estudiante</h2>
              <p>Información cargada automáticamente desde el padrón institucional.</p>
            </div>
          </div>

          <div class="grid">
            <div class="field col-12 md:col-6">
              <label>Facultad</label>
              <InputText v-model="form.facultad" class="w-full readonly" readonly />
            </div>

            <div class="field col-12 md:col-6">
              <label>Escuela Profesional</label>
              <InputText v-model="form.escuela_profesional" class="w-full readonly" readonly />
            </div>

            <div class="field col-12 md:col-8">
              <label>Apellidos y nombres</label>
              <InputText v-model="form.estudiante" class="w-full readonly" readonly />
            </div>

            <div class="field col-6 md:col-2">
              <label>Edad</label>
              <InputNumber v-model="form.edad" :useGrouping="false" class="w-full" disabled />
            </div>

            <div class="field col-6 md:col-2">
              <label>Sexo</label>
              <InputText v-model="form.sexo" class="w-full readonly" readonly />
            </div>

            <div class="field col-12 md:col-3">
              <label>Código</label>
              <InputText v-model="form.codigo_estudiante" class="w-full readonly" readonly />
            </div>

            <div class="field col-12 md:col-3">
              <label>DNI</label>
              <InputText v-model="form.dni" class="w-full readonly" readonly />
            </div>

            <div class="field col-12 md:col-3">
              <label>Celular</label>
              <InputText v-model="form.celular" class="w-full" placeholder="Registrar celular" />
            </div>

            <div class="field col-12 md:col-3">
              <label>Ciclo</label>
              <InputText v-model="form.ciclo" class="w-full readonly" readonly />
            </div>
          </div>
        </section>

        <!-- ACADÉMICO -->
        <section class="bloque">
          <div class="titulo-seccion">
            <div class="numero naranja">{{ historial.length ? '5' : '4' }}</div>
            <div>
              <h2>Información académica</h2>
              <p>Puede seleccionar una o varias opciones.</p>
            </div>
          </div>

          <div class="field">
            <label>Condición académica</label>
            <div class="opciones">
              <label v-for="opcion in condiciones" :key="opcion" class="opcion-check">
                <Checkbox v-model="form.condicion_academica" :value="opcion" />
                <span>{{ opcion }}</span>
              </label>
            </div>
          </div>

          <div class="field mt-4">
            <label>Estudiante con discapacidad *</label>
            <div class="opciones-radio">
              <label class="opcion-check">
                <RadioButton v-model="form.discapacidad" inputId="disc-no" value="NO" />
                <span>No</span>
              </label>
              <label class="opcion-check">
                <RadioButton v-model="form.discapacidad" inputId="disc-con" value="SI_CONADIS" />
                <span>Sí, con carnet CONADIS</span>
              </label>
              <label class="opcion-check">
                <RadioButton v-model="form.discapacidad" inputId="disc-sin" value="SI_SIN_CONADIS" />
                <span>Sí, sin carnet CONADIS</span>
              </label>
            </div>
          </div>
        </section>

        <!-- ATENCIÓN -->
        <section class="bloque">
          <div class="titulo-seccion">
            <div class="numero rojo">{{ historial.length ? '6' : '5' }}</div>
            <div>
              <h2>Atención psicopedagógica</h2>
              <p>Registre la evaluación correspondiente a esta sesión.</p>
            </div>
          </div>

          <div class="field">
            <label>Presunción diagnóstica</label>
            <div class="opciones">
              <label v-for="opcion in diagnosticos" :key="opcion" class="opcion-check">
                <Checkbox v-model="form.presuncion_diagnostica" :value="opcion" />
                <span>{{ opcion }}</span>
              </label>
            </div>

            <div v-if="form.presuncion_diagnostica.includes('Otros problemas psicológicos')" class="mt-3">
              <label>Especifique otro problema psicológico</label>
              <Textarea v-model="form.otro_diagnostico" rows="2" class="w-full" />
            </div>
          </div>

          <div class="field mt-4">
            <label>Dificultades académicas</label>
            <div class="opciones">
              <label v-for="opcion in problemasAcademicos" :key="opcion" class="opcion-check">
                <Checkbox v-model="form.problemas_academicos" :value="opcion" />
                <span>{{ opcion }}</span>
              </label>
            </div>

            <div v-if="form.problemas_academicos.includes('Otros problemas académicos')" class="mt-3">
              <label>Especifique otro problema académico</label>
              <Textarea v-model="form.otro_problema_academico" rows="2" class="w-full" />
            </div>
          </div>

          <div class="grid mt-3">
            <div class="field col-12 md:col-4">
              <label>Tipo de atención *</label>
              <Dropdown
                v-model="form.tipo_atencion"
                :options="tiposAtencion"
                optionLabel="label"
                optionValue="value"
                placeholder="Seleccione"
                class="w-full"
              />
            </div>

            <div class="field col-12 md:col-4">
              <label>Encuesta de satisfacción</label>
              <Dropdown
                v-model="form.satisfaccion"
                :options="satisfacciones"
                showClear
                placeholder="Opcional"
                class="w-full"
              />
            </div>

            <div class="field col-12 md:col-4">
              <label>Evidencia JPG/PNG/PDF</label>
              <input
                type="file"
                accept=".jpg,.jpeg,.png,.pdf"
                class="input-file"
                @change="seleccionarArchivo"
              />
              <small class="text-muted">Máximo 8 MB.</small>
            </div>

            <div class="field col-12">
              <label>Observaciones</label>
              <Textarea v-model="form.observaciones" rows="4" class="w-full" />
            </div>

            <div class="field col-12 md:col-6">
              <label>Derivaciones, cuando corresponda</label>
              <Textarea v-model="form.derivacion" rows="3" class="w-full" />
            </div>

            <div class="field col-12 md:col-6">
              <label>Seguimiento del caso</label>
              <label class="opcion-check mb-2">
                <Checkbox v-model="form.requiere_seguimiento" :binary="true" />
                <span>Requiere seguimiento</span>
              </label>
              <Textarea v-model="form.seguimiento" rows="3" class="w-full" />
            </div>
          </div>
        </section>

        <section class="bloque acciones">
          <div class="guardado-info">
            <i class="pi pi-info-circle"></i>
            Se registrará como <strong>Sesión N.º {{ numeroSesion }}</strong>.
          </div>

          <div class="botones">
            <Button
              type="submit"
              label="Guardar atención"
              icon="pi pi-save"
              :loading="guardando"
            />
            <Button
              type="button"
              label="Cancelar / buscar otro"
              severity="secondary"
              outlined
              @click="reiniciarTodo"
            />
          </div>
        </section>
      </form>
    </div>
  </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';
import axios from 'axios';

import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import Dropdown from 'primevue/dropdown';
import InputNumber from 'primevue/inputnumber';
import InputText from 'primevue/inputtext';
import Message from 'primevue/message';
import RadioButton from 'primevue/radiobutton';
import Textarea from 'primevue/textarea';

const props = defineProps({
  profesionales: { type: Array, default: () => [] },
  periodoActivo: { type: Object, default: null },
  condiciones: { type: Array, default: () => [] },
  diagnosticos: { type: Array, default: () => [] },
  problemasAcademicos: { type: Array, default: () => [] },
});

const hoy = new Date().toISOString().slice(0, 10);

const textoBusqueda = ref('');
const buscando = ref(false);
const estudiantePadron = ref(null);
const historial = ref([]);
const numeroSesion = ref(1);
const detalleAbierto = ref(null);

const guardando = ref(false);
const mensaje = ref('');
const mensajeTipo = ref('success');

const crearFormulario = () => ({
  id_profesional: null,
  fecha_atencion: hoy,
  semestre_academico: props.periodoActivo?.nombre || '',

  facultad: '',
  escuela_profesional: '',
  estudiante: '',
  edad: null,
  sexo: '',
  codigo_estudiante: '',
  dni: '',
  celular: '',
  ciclo: '',

  condicion_academica: [],
  discapacidad: 'NO',

  presuncion_diagnostica: [],
  otro_diagnostico: '',

  problemas_academicos: [],
  otro_problema_academico: '',

  observaciones: '',
  tipo_atencion: null,
  satisfaccion: null,

  derivacion: '',
  seguimiento: '',
  requiere_seguimiento: false,

  evidencia: null,
});

const form = reactive(crearFormulario());

const tiposAtencion = [
  { label: 'Presencial', value: 'PRESENCIAL' },
  { label: 'Virtual', value: 'VIRTUAL' },
];

const satisfacciones = [
  'Muy satisfecho',
  'Satisfecho',
  'Regular',
  'Insatisfecho',
];

const cargarPadronEnFormulario = (e) => {
  form.facultad = e.facultad || '';
  form.escuela_profesional = e.escuela_profesional || '';
  form.estudiante = e.nombre_completo || '';
  form.edad = Number(e.edad) || null;
  form.sexo = e.sexo || '';
  form.codigo_estudiante = e.codigo || '';
  form.dni = e.dni || '';
  form.ciclo = e.ciclo !== null && typeof e.ciclo !== 'undefined'
    ? String(e.ciclo)
    : '';
};

const limpiarDatosAtencion = () => {
  form.condicion_academica = [];
  form.discapacidad = 'NO';
  form.presuncion_diagnostica = [];
  form.otro_diagnostico = '';
  form.problemas_academicos = [];
  form.otro_problema_academico = '';
  form.observaciones = '';
  form.tipo_atencion = null;
  form.satisfaccion = null;
  form.derivacion = '';
  form.seguimiento = '';
  form.requiere_seguimiento = false;
  form.evidencia = null;
};

const buscarEstudiante = async () => {
  mensaje.value = '';

  const buscar = textoBusqueda.value.trim();

  if (!buscar) {
    mensajeTipo.value = 'warn';
    mensaje.value = 'Ingrese el código o DNI del estudiante.';
    return;
  }

  buscando.value = true;

  try {
    const { data } = await axios.get('/servicio-psicopedagogico/buscar-estudiante', {
      params: { buscar }
    });

    estudiantePadron.value = data.estudiante;
    historial.value = data.historial || [];
    numeroSesion.value = Number(data.numero_sesion || 1);
    detalleAbierto.value = null;

    // Conservamos psicólogo si ya fue elegido antes.
    const profesional = form.id_profesional;
    const semestre = form.semestre_academico;

    Object.assign(form, crearFormulario());
    form.id_profesional = profesional;
    form.semestre_academico = semestre;

    cargarPadronEnFormulario(data.estudiante);

    // Si existe historial, reutilizamos solo el celular más reciente como ayuda.
    if (historial.value.length && historial.value[0]?.celular) {
      form.celular = historial.value[0].celular;
    }
  } catch (error) {
    estudiantePadron.value = null;
    historial.value = [];
    numeroSesion.value = 1;

    mensajeTipo.value = 'warn';
    mensaje.value =
      error?.response?.data?.mensaje ||
      'No se encontró al estudiante en el padrón.';
  } finally {
    buscando.value = false;
  }
};

const usarComoBase = (atencion) => {
  // Copiamos la clasificación del caso, pero NO observaciones,
  // derivación, seguimiento, satisfacción ni evidencia.
  form.condicion_academica = [...(atencion.condicion_academica || [])];
  form.discapacidad = atencion.discapacidad || 'NO';
  form.presuncion_diagnostica = [...(atencion.presuncion_diagnostica || [])];
  form.otro_diagnostico = atencion.otro_diagnostico || '';
  form.problemas_academicos = [...(atencion.problemas_academicos || [])];
  form.otro_problema_academico = atencion.otro_problema_academico || '';
  form.tipo_atencion = atencion.tipo_atencion || null;

  if (atencion.celular) {
    form.celular = atencion.celular;
  }

  form.observaciones = '';
  form.satisfaccion = null;
  form.derivacion = '';
  form.seguimiento = '';
  form.requiere_seguimiento = false;
  form.evidencia = null;

  mensajeTipo.value = 'info';
  mensaje.value =
    `Se tomaron como base los datos de clasificación de la sesión N.º ${atencion.numero_sesion}. ` +
    'Las observaciones, derivación y seguimiento quedaron vacíos para registrar la nueva atención.';

  window.scrollTo({ top: document.body.scrollHeight * 0.45, behavior: 'smooth' });
};

const nuevaAtencionLimpia = () => {
  limpiarDatosAtencion();

  if (historial.value.length && historial.value[0]?.celular) {
    form.celular = historial.value[0].celular;
  }

  mensajeTipo.value = 'info';
  mensaje.value = 'Se inició una atención nueva usando únicamente los datos del padrón.';
};

const toggleDetalle = (id) => {
  detalleAbierto.value = detalleAbierto.value === id ? null : id;
};

const textoDiscapacidad = (valor) => {
  if (valor === 'SI_CONADIS') return 'Sí, con carnet CONADIS';
  if (valor === 'SI_SIN_CONADIS') return 'Sí, sin carnet CONADIS';
  return 'No';
};

const formatearFecha = (fecha) => {
  if (!fecha) return '-';
  const [y, m, d] = String(fecha).slice(0, 10).split('-');
  return `${d}/${m}/${y}`;
};

const seleccionarArchivo = (event) => {
  form.evidencia = event.target.files?.[0] || null;
};

const validar = () => {
  const campos = [
    ['id_profesional', 'Seleccione el psicólogo que realiza la atención.'],
    ['fecha_atencion', 'Ingrese la fecha de atención.'],
    ['semestre_academico', 'No se encontró el semestre académico activo.'],
    ['codigo_estudiante', 'Busque primero al estudiante.'],
    ['dni', 'Busque primero al estudiante.'],
    ['tipo_atencion', 'Seleccione el tipo de atención.'],
  ];

  for (const [campo, texto] of campos) {
    if (
      form[campo] === null ||
      typeof form[campo] === 'undefined' ||
      String(form[campo]).trim() === ''
    ) {
      mensajeTipo.value = 'warn';
      mensaje.value = texto;
      window.scrollTo({ top: 0, behavior: 'smooth' });
      return false;
    }
  }

  return true;
};

const guardar = async () => {
  mensaje.value = '';

  if (!validar()) return;

  guardando.value = true;

  try {
    const data = new FormData();

    Object.entries(form).forEach(([campo, valor]) => {
      if (campo === 'evidencia') {
        if (valor) data.append(campo, valor);
        return;
      }

      if (Array.isArray(valor)) {
        valor.forEach(item => data.append(`${campo}[]`, item));
        return;
      }

      if (typeof valor === 'boolean') {
        data.append(campo, valor ? '1' : '0');
        return;
      }

      data.append(campo, valor ?? '');
    });

    const respuesta = await axios.post('/servicio-psicopedagogico', data, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });

    mensajeTipo.value = 'success';
    mensaje.value =
      `${respuesta.data.mensaje} Se registró como sesión N.º ${respuesta.data.numero_sesion}.`;

    // Volvemos a consultar para mostrar la nueva sesión en historial.
    numeroSesion.value = Number(respuesta.data.numero_sesion) + 1;
    await buscarEstudiante();

    window.scrollTo({ top: 0, behavior: 'smooth' });
  } catch (error) {
    mensajeTipo.value = 'error';

    const errors = error?.response?.data?.errors;

    if (errors) {
      mensaje.value = Object.values(errors)?.[0]?.[0] || 'Revise los datos ingresados.';
    } else {
      mensaje.value =
        error?.response?.data?.mensaje ||
        error?.response?.data?.message ||
        'No se pudo registrar la atención.';
    }

    window.scrollTo({ top: 0, behavior: 'smooth' });
  } finally {
    guardando.value = false;
  }
};

const reiniciarTodo = () => {
  const profesional = form.id_profesional;
  const semestre = form.semestre_academico;

  Object.assign(form, crearFormulario());

  form.id_profesional = profesional;
  form.semestre_academico = semestre;

  textoBusqueda.value = '';
  estudiantePadron.value = null;
  historial.value = [];
  numeroSesion.value = 1;
  detalleAbierto.value = null;
  mensaje.value = '';
};
</script>

<style scoped>
.pagina {
  min-height: 100vh;
  padding: 24px 12px 45px;
  background:
    radial-gradient(circle at top left, rgba(49, 130, 206, .12), transparent 30%),
    radial-gradient(circle at top right, rgba(49, 151, 149, .10), transparent 28%),
    #f4f7fb;
}

.contenedor {
  width: 100%;
  max-width: 1080px;
  margin: 0 auto;
}

.hero {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 25px 28px;
  margin-bottom: 18px;
  border-radius: 18px;
  color: white;
  background: linear-gradient(135deg, #275d9f, #2f80c9 55%, #39a6a3);
  box-shadow: 0 12px 30px rgba(37, 93, 159, .22);
}

.hero-icon {
  display: grid;
  place-items: center;
  width: 58px;
  height: 58px;
  flex: 0 0 58px;
  border-radius: 16px;
  background: rgba(255,255,255,.16);
  font-size: 1.6rem;
}

.hero h1 {
  margin: 0;
  font-size: 1.65rem;
}

.hero p {
  margin: 5px 0 0;
  opacity: .92;
}

.bloque {
  margin-bottom: 17px;
  padding: 22px;
  border: 1px solid #e5eaf1;
  border-radius: 15px;
  background: #fff;
  box-shadow: 0 4px 14px rgba(30, 55, 80, .06);
}

.bloque-busqueda {
  border-top: 4px solid #3182ce;
}

.historial-bloque {
  border-top: 4px solid #805ad5;
}

.titulo-seccion {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  margin-bottom: 18px;
}

.titulo-seccion h2 {
  margin: 0;
  font-size: 1.08rem;
  color: #223247;
}

.titulo-seccion p {
  margin: 4px 0 0;
  color: #718096;
  font-size: .88rem;
}

.numero {
  display: grid;
  place-items: center;
  width: 31px;
  height: 31px;
  flex: 0 0 31px;
  border-radius: 9px;
  color: #fff;
  font-weight: 700;
}

.azul { background: #3182ce; }
.morado { background: #805ad5; }
.verde { background: #2f855a; }
.celeste { background: #319795; }
.naranja { background: #dd6b20; }
.rojo { background: #c53030; }

.busqueda-row {
  display: flex;
  gap: 10px;
}

.busqueda-input {
  flex: 1;
}

.estudiante-card {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-top: 18px;
  padding: 17px;
  border: 1px solid #bee3f8;
  border-radius: 13px;
  background: linear-gradient(135deg, #ebf8ff, #f7fcff);
}

.estudiante-avatar {
  display: grid;
  place-items: center;
  width: 54px;
  height: 54px;
  flex: 0 0 54px;
  border-radius: 50%;
  background: #3182ce;
  color: white;
  font-size: 1.35rem;
}

.estudiante-info {
  flex: 1;
  min-width: 0;
}

.estudiante-info h3 {
  margin: 0 0 7px;
  color: #1a365d;
  font-size: 1.08rem;
}

.datos-mini {
  display: flex;
  flex-wrap: wrap;
  gap: 7px 16px;
  font-size: .84rem;
  color: #4a5568;
}

.escuela,
.programa {
  margin: 7px 0 0;
  color: #4a5568;
  font-size: .84rem;
}

.sesion-badge {
  min-width: 120px;
  padding: 10px 14px;
  border-radius: 11px;
  background: #fff;
  text-align: center;
  box-shadow: 0 2px 8px rgba(0,0,0,.06);
}

.sesion-badge small {
  display: block;
  color: #718096;
}

.sesion-badge strong {
  display: block;
  margin-top: 3px;
  color: #2b6cb0;
}

.historial-lista {
  display: grid;
  gap: 12px;
}

.historial-item {
  padding: 15px;
  border: 1px solid #e9d8fd;
  border-radius: 12px;
  background: #fcfaff;
}

.historial-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 10px;
}

.historial-head > div {
  display: flex;
  align-items: center;
  gap: 10px;
}

.session-pill,
.tipo-pill {
  padding: 4px 9px;
  border-radius: 999px;
  font-size: .75rem;
  font-weight: 600;
}

.session-pill {
  color: #553c9a;
  background: #e9d8fd;
}

.tipo-pill {
  color: #2c5282;
  background: #bee3f8;
}

.historial-profesional {
  margin-top: 9px;
  color: #4a5568;
  font-size: .86rem;
}

.historial-profesional i {
  margin-right: 6px;
  color: #805ad5;
}

.historial-resumen {
  display: grid;
  gap: 9px;
  margin-top: 12px;
}

.mini-label {
  display: block;
  margin-bottom: 5px;
  color: #718096;
  font-size: .75rem;
}

.chips {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

.chip {
  padding: 4px 8px;
  border-radius: 999px;
  font-size: .73rem;
}

.chip-blue {
  color: #2b6cb0;
  background: #ebf8ff;
  border: 1px solid #bee3f8;
}

.chip-orange {
  color: #9c4221;
  background: #fffaf0;
  border: 1px solid #feebc8;
}

.historial-actions {
  display: flex;
  justify-content: flex-end;
  gap: 7px;
  margin-top: 12px;
}

.detalle-anterior {
  display: grid;
  gap: 8px;
  margin-top: 12px;
  padding: 12px;
  border-left: 3px solid #805ad5;
  background: #fff;
  color: #4a5568;
  font-size: .84rem;
}

.nuevo-limpio {
  display: flex;
  align-items: center;
  gap: 9px;
  margin-top: 14px;
  padding: 10px 12px;
  border-radius: 10px;
  color: #4a5568;
  background: #f7fafc;
  font-size: .84rem;
}

.nuevo-limpio span {
  flex: 1;
}

.field > label:first-child {
  display: block;
  margin-bottom: 7px;
  color: #374151;
  font-size: .89rem;
  font-weight: 600;
}

.readonly {
  background: #f8fafc !important;
}

.w-full {
  width: 100%;
}

.opciones {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 11px 22px;
  margin-top: 10px;
}

.opciones-radio {
  display: flex;
  flex-wrap: wrap;
  gap: 18px;
  margin-top: 10px;
}

.opcion-check {
  display: flex !important;
  align-items: flex-start;
  gap: 9px;
  color: #4a5568;
  font-size: .89rem;
  font-weight: 400 !important;
  cursor: pointer;
}

.input-file {
  width: 100%;
  padding: 8px;
  border: 1px solid #d8dee8;
  border-radius: 7px;
  background: #fff;
}

.text-muted {
  display: block;
  margin-top: 4px;
  color: #8a94a6;
}

.acciones {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 15px;
  border-top: 4px solid #2f855a;
}

.guardado-info {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #4a5568;
  font-size: .9rem;
}

.guardado-info i {
  color: #2f855a;
}

.botones {
  display: flex;
  gap: 9px;
}

@media (max-width: 760px) {
  .pagina {
    padding: 10px 6px 28px;
  }

  .hero {
    padding: 18px;
  }

  .hero h1 {
    font-size: 1.32rem;
  }

  .bloque {
    padding: 16px;
  }

  .busqueda-row {
    flex-direction: column;
  }

  .estudiante-card {
    align-items: flex-start;
    flex-wrap: wrap;
  }

  .sesion-badge {
    width: 100%;
  }

  .opciones {
    grid-template-columns: 1fr;
  }

  .acciones,
  .nuevo-limpio {
    align-items: stretch;
    flex-direction: column;
  }

  .botones {
    flex-direction: column;
  }

  .botones :deep(.p-button) {
    width: 100%;
  }
}
</style>
