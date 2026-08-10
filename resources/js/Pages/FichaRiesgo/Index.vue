<script setup>
import { reactive, ref } from 'vue';
import axios from 'axios';

import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import RadioButton from 'primevue/radiobutton';
import Textarea from 'primevue/textarea';
import Button from 'primevue/button';
import Message from 'primevue/message';

/* =========================================================
   ESTADO
========================================================= */

const guardando = ref(false);
const mensajeExito = ref('');
const mensajeError = ref('');
const errores = ref({});


/* =========================================================
   FORMULARIO
========================================================= */

const datosIniciales = () => ({
    correo_registro: '',
    fecha: '',
    condicion_matricula: null,
    nombres_apellidos: '',
    escuela_profesional: null,
    ciclo_academico: null,
    codigo: '',
    dni: '',
    celular: '',
    celular_tutor: '',
    celular_pariente: '',
    facebook: '',
    correo: '',
    lugar_procedencia: '',
    direccion_actual: '',

    // ACADÉMICOS
    a1: null,
    a2: null,
    a3: null,
    a4: null,
    a5: null,
    a6: null,
    a7: null,
    a8: null,
    a9: null,
    a10: null,

    // PERSONALES
    p1: null,
    p2: null,
    p3: null,
    p4: null,
    p5: null,
    p6: null,
    p7: null,
    p8: null,
    p9: null,
    p10: null,
    p11: null,
    p12: null,
    p13: null,

    // FAMILIARES
    f1: null,
    f2: null,
    f3: null,
    f4: null,
    f5: null,
    f6: null,
    f7: null,
    f8: null,
});

const form = reactive(datosIniciales());


/* =========================================================
   OPCIONES
========================================================= */

const condicionesMatricula = [
    { label: 'Tercera matrícula', value: 3 },
    { label: 'Cuarta matrícula', value: 4 }
];


/* =========================================================
   ESCUELAS PROFESIONALES
   Agregar aquí las 45 escuelas
========================================================= */

const escuelas = [

    'INGENIERIA AGRONOMICA',
    'INGENIERIA AGROINDUSTRIAL',
    'INGENIERIA TOPOGRAFICA Y AGRIMENSURA',
    'MEDICINA VETERINARIA Y ZOOTECNIA',
    'INGENIERIA ECONOMICA',
    'CIENCIAS CONTABLES',
    'ADMINISTRACION',
    'TRABAJO SOCIAL',
    'ENFERMERIA',
    'INGENIERIA DE MINAS',
    'HUMANIDADES',
    'SOCIOLOGIA',
    'TURISMO',
    'ANTROPOLOGIA',
    'CIENCIAS DE LA COMUNICACION SOCIAL',

    'ARTE: ARTES PLASTICAS',
    'ARTE: DANZA',
    'ARTE: MUSICA',

    'BIOLOGIA: ECOLOGIA',
    'BIOLOGIA: MICROBIOLOGIA Y LABORATORIO CLINICO',
    'BIOLOGIA: PESQUERIA',

    'EDUC. SEC.: CIENCIA, TECNOLOGIA Y AMBIENTE',
    'EDUC. SEC.: CIENCIAS SOCIALES',
    'EDUC. SEC.: LEN. LIT, PSICOLOGIA Y FILOSOFIA',
    'EDUC. SEC.: MATEMATICA, FISICA, COMP. E INFORMATICA',

    'EDUCACION PRIMARIA',
    'EDUCACION INICIAL',
    'EDUCACION FISICA',

    'INGENIERIA ESTADISTICA E INFORMATICA',
    'DERECHO',
    'INGENIERIA QUIMICA',
    'ODONTOLOGIA',
    'NUTRICION HUMANA',
    'INGENIERIA GEOLOGICA',
    'INGENIERIA METALURGICA',
    'INGENIERIA CIVIL',
    'ARQUITECTURA Y URBANISMO',

    'CIENCIAS FISICO MATEMATICAS: FISICA',
    'CIENCIAS FISICO MATEMATICAS: MATEMATICAS',

    'INGENIERIA AGRICOLA',
    'MEDICINA HUMANA',
    'INGENIERIA MECANICA ELECTRICA',
    'INGENIERIA ELECTRONICA',
    'INGENIERIA DE SISTEMAS',
    'PSICOLOGÍA',
    'INGENIERÍA DE TELECOMUNICACIONES',

    'INGENIERIA ECONÓMICA - AZANGARO',
    'INGENIERIA DE MINAS - AZANGARO',

    'CIENCIAS CONTABLES - JULI',
    'ARQUITECTURA Y URBANISMO - JULI',

    'INGENIERIA INTELIGENCIA ARTIFICIAL Y CIENCIA DE DATOS',

    'INGENIERIA AGROINDUSTRIAL - JULI'

].map(nombre => ({
    label: nombre,
    value: nombre
}));


/* =========================================================
   CICLOS 1 AL 15
========================================================= */

const ciclos = Array.from(
    { length: 15 },
    (_, i) => ({
        label: `Ciclo ${i + 1}`,
        value: i + 1
    })
);


/* =========================================================
   FRECUENCIAS
========================================================= */

const frecuencias = [
    { label: 'Nunca', value: 1 },
    { label: 'Casi nunca', value: 2 },
    { label: 'A veces', value: 3 },
    { label: 'Casi siempre', value: 4 },
    { label: 'Siempre', value: 5 }
];


const siNo = [
    { label: 'SI', value: 1 },
    { label: 'NO', value: 0 }
];


/* =========================================================
   PREGUNTAS ACADÉMICAS
========================================================= */

const preguntasAcademicas = [
    {
        campo: 'a1',
        texto: 'A.1 Dificultades para asistir puntualmente'
    },
    {
        campo: 'a2',
        texto: 'A.2 Reprobación de exámenes parciales'
    },
    {
        campo: 'a3',
        texto: 'A.3 Dificultades para trabajar en grupo'
    },
    {
        campo: 'a4',
        texto: 'A.4 Dificultades para exponer'
    },
    {
        campo: 'a5',
        texto: 'A.5 Dificultades para realizar y presentar trabajos'
    },
    {
        campo: 'a6',
        texto: 'A.6 Conflictos con algún docente'
    },
    {
        campo: 'a7',
        texto: 'A.7 Habilidades y capacidades de aprender'
    },
    {
        campo: 'a8',
        texto: 'A.8 Técnicas y hábitos de estudio'
    },
    {
        campo: 'a9',
        texto: 'A.9 Vocación e identificación de la carrera'
    },
    {
        campo: 'a10',
        texto: 'A.10 Interés y motivación para estudiar'
    }
];


/* =========================================================
   PREGUNTAS PERSONALES
========================================================= */

const preguntasPersonales = [
    {
        campo: 'p1',
        texto: 'P.1 Problemas con la salud y estado físico',
        tipo: 'frecuencia'
    },
    {
        campo: 'p2',
        texto: 'P.2 Problemas con la alimentación',
        tipo: 'frecuencia'
    },
    {
        campo: 'p3',
        texto: 'P.3 Cuenta con una vivienda propia',
        tipo: 'sino'
    },
    {
        campo: 'p4',
        texto: 'P.4 Problemas con la autonomía y toma de decisiones',
        tipo: 'frecuencia'
    },
    {
        campo: 'p5',
        texto: 'P.5 Conflictos en las relaciones con sus compañeros',
        tipo: 'frecuencia'
    },
    {
        campo: 'p6',
        texto: 'P.6 Dificultades para integrarse al grupo',
        tipo: 'frecuencia'
    },
    {
        campo: 'p7',
        texto: 'P.7 Se siente estresado continuamente',
        tipo: 'frecuencia'
    },
    {
        campo: 'p8',
        texto: 'P.8 Problemas con la seguridad personal / emocional',
        tipo: 'frecuencia'
    },
    {
        campo: 'p9',
        texto: 'P.9 Se siente discriminado (a), marginado',
        tipo: 'frecuencia'
    },
    {
        campo: 'p10',
        texto: 'P.10 Problemas con sus creencias, religión',
        tipo: 'sino'
    },
    {
        campo: 'p11',
        texto: 'P.11 Hostigamiento sexual',
        tipo: 'frecuencia'
    },
    {
        campo: 'p12',
        texto: 'P.12 Limitaciones para establecer metas y aspiraciones personales (proyecto de vida)',
        tipo: 'frecuencia'
    },
    {
        campo: 'p13',
        texto: 'P.13 Problemas con la autoestima',
        tipo: 'frecuencia'
    }
];


/* =========================================================
   PREGUNTAS FAMILIARES
========================================================= */

const preguntasFamiliares = [
    {
        campo: 'f1',
        texto: 'F.1 Conflicto en su relación con un familiar',
        tipo: 'frecuencia'
    },
    {
        campo: 'f2',
        texto: 'F.2 Vive solo y le afecta',
        tipo: 'frecuencia'
    },
    {
        campo: 'f3',
        texto: 'F.3 No cuenta con el soporte económico familiar para continuar sus estudios',
        tipo: 'frecuencia'
    },
    {
        campo: 'f4',
        texto: 'F.4 Tiene un familiar enfermo',
        tipo: 'sino'
    },
    {
        campo: 'f5',
        texto: 'F.5 Tiene familiares que dependen del estudiante',
        tipo: 'sino'
    },
    {
        campo: 'f6',
        texto: 'F.6 Tiene problemas de convivencia en pareja',
        tipo: 'frecuencia'
    },
    {
        campo: 'f7',
        texto: 'F.7 Tiene hijos y dificultades para afrontar sus responsabilidades',
        tipo: 'sino'
    },
    {
        campo: 'f8',
        texto: 'F.8 Ha sufrido la pérdida de un familiar cercano',
        tipo: 'sino'
    }
];


/* =========================================================
   ERROR
========================================================= */

const obtenerError = (campo) => {

    if (!errores.value[campo]) {
        return '';
    }

    return errores.value[campo][0];
};


/* =========================================================
   LIMPIAR
========================================================= */

const limpiar = () => {

    const nuevo = datosIniciales();

    Object.keys(nuevo).forEach(key => {
        form[key] = nuevo[key];
    });

    errores.value = {};
};


/* =========================================================
   GUARDAR
========================================================= */

const guardar = async () => {

    guardando.value = true;

    mensajeExito.value = '';
    mensajeError.value = '';
    errores.value = {};

    try {

        const response = await axios.post(
            '/fichariesgo/guardar',
            form
        );

        if (response.data.ok) {

            mensajeExito.value =
                response.data.mensaje ||
                'Ficha registrada correctamente.';

            limpiar();

            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

    } catch (error) {

        console.error(error);

        if (error.response?.status === 422) {

            errores.value =
                error.response.data.errors || {};

            mensajeError.value =
                error.response.data.mensaje ||
                'Complete todos los campos obligatorios.';

        } else {

            mensajeError.value =
                error.response?.data?.mensaje ||
                'Ocurrió un error al guardar la ficha.';
        }

        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });

    } finally {

        guardando.value = false;
    }
};

</script>


<template>

    <div class="pagina">

        <div class="contenedor">


            <!-- =====================================================
                 CABECERA
            ====================================================== -->

            <div class="cabecera">

                <div class="cabecera-icono">
                    <i class="pi pi-file-edit"></i>
                </div>

                <div class="cabecera-texto">

                    <div class="etiqueta">
                        SUBUNIDAD DE TUTORIA Y SERVICIO PSICOPEDAGÓGICO - FRANK ME PROMETIO SU PRIMA BORRARE CUANDO LO CUMPLA
                    </div>

                    <h1>
                        FICHA DE DATOS DE ESTUDIANTES EN
                        RIESGO ACADÉMICO 2026 - II
                    </h1>

                    <p class="declaracion">
                        Todas los datos llenados serán tomados como
                        Declaración Jurada
                    </p>

                </div>

            </div>


            <!-- CONSENTIMIENTO -->

            <div class="consentimiento">

                <i class="pi pi-info-circle"></i>

                <div>

                    <strong>Información importante</strong>

                    <p>
                        Al responder esta encuesta doy mi consentimiento para
                        que pueda llenar FICHA DE DATOS DE ESTUDIANTES EN
                        RIESGO ACADÉMICO 2026 - II. Se me ha informado de que
                        se mantendrá absoluta confidencialidad de los datos
                        personales y de la información que proporcione.
                        Acepto participar en el llenado de la ficha de datos
                        de estudiantes en riesgo académico 2026 - II.
                    </p>

                    <span>
                        <b>*</b> Indica que la pregunta es obligatoria
                    </span>

                </div>

            </div>


            <!-- =====================================================
                 MENSAJES
            ====================================================== -->

            <Message
                v-if="mensajeExito"
                severity="success"
                class="mensaje"
            >
                {{ mensajeExito }}
            </Message>


            <Message
                v-if="mensajeError"
                severity="error"
                class="mensaje"
            >
                {{ mensajeError }}
            </Message>


            <form @submit.prevent="guardar">


                <!-- =================================================
                     DATOS ESTUDIANTE
                ================================================== -->

                <section class="card">

                    <div class="card-header">

                        <div class="numero">
                            01
                        </div>

                        <div>
                            <h2>Datos del estudiante</h2>
                            <p>
                                Complete sus datos personales y académicos.
                            </p>
                        </div>

                    </div>


                    <div class="form-grid">


                        <!-- CORREO REGISTRO -->

                        <div class="campo campo-completo">

                            <label>
                                Correo electrónico
                                <b>*</b>
                            </label>

                            <InputText
                                v-model="form.correo_registro"
                                type="email"
                                placeholder="ejemplo@correo.com"
                                class="w-full"
                                :class="{
                                    'p-invalid': errores.correo_registro
                                }"
                            />

                            <small
                                v-if="errores.correo_registro"
                                class="error"
                            >
                                {{ obtenerError('correo_registro') }}
                            </small>

                        </div>


                        <!-- FECHA -->

                        <div class="campo">

                            <label>
                                Fecha
                                <b>*</b>
                            </label>

                            <InputText
                                v-model="form.fecha"
                                type="date"
                                class="w-full"
                                :class="{
                                    'p-invalid': errores.fecha
                                }"
                            />

                            <small
                                v-if="errores.fecha"
                                class="error"
                            >
                                {{ obtenerError('fecha') }}
                            </small>

                        </div>


                        <!-- CONDICIÓN -->

                        <div class="campo">

                            <label>
                                Condición de matricula
                                <b>*</b>
                            </label>

                            <Dropdown
                                v-model="form.condicion_matricula"
                                :options="condicionesMatricula"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Seleccione"
                                class="w-full"
                                :class="{
                                    'p-invalid':
                                    errores.condicion_matricula
                                }"
                            />

                            <small
                                v-if="errores.condicion_matricula"
                                class="error"
                            >
                                {{
                                    obtenerError(
                                        'condicion_matricula'
                                    )
                                }}
                            </small>

                        </div>


                        <!-- NOMBRES -->

                        <div class="campo campo-completo">

                            <label>
                                Nombres y Apellidos
                                <b>*</b>
                            </label>

                            <InputText
                                v-model="form.nombres_apellidos"
                                placeholder="Ingrese nombres y apellidos"
                                class="w-full"
                                :class="{
                                    'p-invalid':
                                    errores.nombres_apellidos
                                }"
                            />

                            <small
                                v-if="errores.nombres_apellidos"
                                class="error"
                            >
                                {{
                                    obtenerError(
                                        'nombres_apellidos'
                                    )
                                }}
                            </small>

                        </div>


                        <!-- ESCUELA -->

                        <div class="campo">

                            <label>
                                Escuela Profesional
                                <b>*</b>
                            </label>

                            <Dropdown
                                v-model="form.escuela_profesional"
                                :options="escuelas"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Seleccione su escuela"
                                filter
                                class="w-full"
                                :class="{
                                    'p-invalid':
                                    errores.escuela_profesional
                                }"
                            />

                            <small
                                v-if="errores.escuela_profesional"
                                class="error"
                            >
                                {{
                                    obtenerError(
                                        'escuela_profesional'
                                    )
                                }}
                            </small>

                        </div>


                        <!-- CICLO -->

                        <div class="campo">

                            <label>
                                Ciclo académico
                                <b>*</b>
                            </label>

                            <Dropdown
                                v-model="form.ciclo_academico"
                                :options="ciclos"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Seleccione"
                                class="w-full"
                                :class="{
                                    'p-invalid':
                                    errores.ciclo_academico
                                }"
                            />

                            <small
                                v-if="errores.ciclo_academico"
                                class="error"
                            >
                                {{
                                    obtenerError(
                                        'ciclo_academico'
                                    )
                                }}
                            </small>

                        </div>


                        <!-- CÓDIGO -->

                        <div class="campo">

                            <label>
                                Código
                                <b>*</b>
                            </label>

                            <InputText
                                v-model="form.codigo"
                                placeholder="Código de estudiante"
                                class="w-full"
                                :class="{
                                    'p-invalid': errores.codigo
                                }"
                            />

                            <small
                                v-if="errores.codigo"
                                class="error"
                            >
                                {{ obtenerError('codigo') }}
                            </small>

                        </div>


                        <!-- DNI -->

                        <div class="campo">

                            <label>
                                DNI
                                <b>*</b>
                            </label>

                            <InputText
                                v-model="form.dni"
                                maxlength="15"
                                placeholder="Ingrese DNI"
                                class="w-full"
                                :class="{
                                    'p-invalid': errores.dni
                                }"
                            />

                            <small
                                v-if="errores.dni"
                                class="error"
                            >
                                {{ obtenerError('dni') }}
                            </small>

                        </div>


                        <!-- CELULAR -->

                        <div class="campo">

                            <label>
                                Numero de celular
                                <b>*</b>
                            </label>

                            <InputText
                                v-model="form.celular"
                                placeholder="Número de celular"
                                class="w-full"
                                :class="{
                                    'p-invalid': errores.celular
                                }"
                            />

                            <small
                                v-if="errores.celular"
                                class="error"
                            >
                                {{ obtenerError('celular') }}
                            </small>

                        </div>


                        <!-- TUTOR -->

                        <div class="campo">

                            <label>
                                Numero de celular de su Tutor
                                (Padre o Madre)
                                <b>*</b>
                            </label>

                            <InputText
                                v-model="form.celular_tutor"
                                placeholder="Número de celular"
                                class="w-full"
                                :class="{
                                    'p-invalid':
                                    errores.celular_tutor
                                }"
                            />

                            <small
                                v-if="errores.celular_tutor"
                                class="error"
                            >
                                {{
                                    obtenerError(
                                        'celular_tutor'
                                    )
                                }}
                            </small>

                        </div>


                        <!-- PARIENTE -->

                        <div class="campo campo-completo">

                            <label>
                                Numero de celular de pariente
                                (Hermano o familiares)
                                <b>*</b>
                            </label>

                            <InputText
                                v-model="form.celular_pariente"
                                placeholder="Número de celular"
                                class="w-full"
                                :class="{
                                    'p-invalid':
                                    errores.celular_pariente
                                }"
                            />

                            <small
                                v-if="errores.celular_pariente"
                                class="error"
                            >
                                {{
                                    obtenerError(
                                        'celular_pariente'
                                    )
                                }}
                            </small>

                        </div>


                        <!-- FACEBOOK -->

                        <div class="campo">

                            <label>
                                Facebook
                                <b>*</b>
                            </label>

                            <InputText
                                v-model="form.facebook"
                                placeholder="Facebook"
                                class="w-full"
                                :class="{
                                    'p-invalid': errores.facebook
                                }"
                            />

                            <small
                                v-if="errores.facebook"
                                class="error"
                            >
                                {{ obtenerError('facebook') }}
                            </small>

                        </div>


                        <!-- CORREO -->

                        <div class="campo">

                            <label>
                                Correo
                                <b>*</b>
                            </label>

                            <InputText
                                v-model="form.correo"
                                type="email"
                                placeholder="Correo electrónico"
                                class="w-full"
                                :class="{
                                    'p-invalid': errores.correo
                                }"
                            />

                            <small
                                v-if="errores.correo"
                                class="error"
                            >
                                {{ obtenerError('correo') }}
                            </small>

                        </div>


                        <!-- PROCEDENCIA -->

                        <div class="campo campo-completo">

                            <label>
                                Lugar de procedencia
                                <b>*</b>
                            </label>

                            <InputText
                                v-model="form.lugar_procedencia"
                                placeholder="Ingrese lugar de procedencia"
                                class="w-full"
                                :class="{
                                    'p-invalid':
                                    errores.lugar_procedencia
                                }"
                            />

                            <small
                                v-if="errores.lugar_procedencia"
                                class="error"
                            >
                                {{
                                    obtenerError(
                                        'lugar_procedencia'
                                    )
                                }}
                            </small>

                        </div>


                        <!-- DIRECCIÓN -->

                        <div class="campo campo-completo">

                            <label>
                                Dirección actual de residencia
                                <b>*</b>
                            </label>

                            <Textarea
                                v-model="form.direccion_actual"
                                rows="3"
                                autoResize
                                placeholder="Ingrese su dirección actual"
                                class="w-full"
                                :class="{
                                    'p-invalid':
                                    errores.direccion_actual
                                }"
                            />

                            <small
                                v-if="errores.direccion_actual"
                                class="error"
                            >
                                {{
                                    obtenerError(
                                        'direccion_actual'
                                    )
                                }}
                            </small>

                        </div>

                    </div>

                </section>


                <!-- =================================================
                     ACADÉMICOS
                ================================================== -->

                <section class="card">

                    <div class="card-header">

                        <div class="numero">
                            02
                        </div>

                        <div>
                            <h2>ACADÉMICOS</h2>
                            <p>
                                Seleccione una respuesta en cada pregunta.
                            </p>
                        </div>

                    </div>


                    <div class="preguntas">

                        <div
                            v-for="pregunta in preguntasAcademicas"
                            :key="pregunta.campo"
                            class="pregunta"
                        >

                            <div class="pregunta-texto">

                                {{ pregunta.texto }}

                                <b>*</b>

                            </div>


                            <div class="opciones">

                                <label
                                    v-for="opcion in frecuencias"
                                    :key="
                                        `${pregunta.campo}-${opcion.value}`
                                    "
                                    class="opcion"
                                >

                                    <RadioButton
                                        v-model="
                                            form[pregunta.campo]
                                        "
                                        :inputId="
                                            `${pregunta.campo}-${opcion.value}`
                                        "
                                        :name="pregunta.campo"
                                        :value="opcion.value"
                                    />

                                    <span>
                                        {{ opcion.label }}
                                    </span>

                                </label>

                            </div>


                            <small
                                v-if="errores[pregunta.campo]"
                                class="error"
                            >
                                {{ obtenerError(pregunta.campo) }}
                            </small>

                        </div>

                    </div>

                </section>


                <!-- =================================================
                     PERSONALES
                ================================================== -->

                <section class="card">

                    <div class="card-header">

                        <div class="numero">
                            03
                        </div>

                        <div>
                            <h2>PERSONALES</h2>
                            <p>
                                Seleccione una respuesta en cada pregunta.
                            </p>
                        </div>

                    </div>


                    <div class="preguntas">

                        <div
                            v-for="pregunta in preguntasPersonales"
                            :key="pregunta.campo"
                            class="pregunta"
                        >

                            <div class="pregunta-texto">

                                {{ pregunta.texto }}

                                <b>*</b>

                            </div>


                            <div class="opciones">

                                <label
                                    v-for="opcion in
                                        (
                                            pregunta.tipo === 'frecuencia'
                                                ? frecuencias
                                                : siNo
                                        )"
                                    :key="
                                        `${pregunta.campo}-${opcion.value}`
                                    "
                                    class="opcion"
                                >

                                    <RadioButton
                                        v-model="
                                            form[pregunta.campo]
                                        "
                                        :inputId="
                                            `${pregunta.campo}-${opcion.value}`
                                        "
                                        :name="pregunta.campo"
                                        :value="opcion.value"
                                    />

                                    <span>
                                        {{ opcion.label }}
                                    </span>

                                </label>

                            </div>


                            <small
                                v-if="errores[pregunta.campo]"
                                class="error"
                            >
                                {{ obtenerError(pregunta.campo) }}
                            </small>

                        </div>

                    </div>

                </section>


                <!-- =================================================
                     FAMILIARES
                ================================================== -->

                <section class="card">

                    <div class="card-header">

                        <div class="numero">
                            04
                        </div>

                        <div>
                            <h2>FAMILIARES</h2>
                            <p>
                                Seleccione una respuesta en cada pregunta.
                            </p>
                        </div>

                    </div>


                    <div class="preguntas">

                        <div
                            v-for="pregunta in preguntasFamiliares"
                            :key="pregunta.campo"
                            class="pregunta"
                        >

                            <div class="pregunta-texto">

                                {{ pregunta.texto }}

                                <b>*</b>

                            </div>


                            <div class="opciones">

                                <label
                                    v-for="opcion in
                                        (
                                            pregunta.tipo === 'frecuencia'
                                                ? frecuencias
                                                : siNo
                                        )"
                                    :key="
                                        `${pregunta.campo}-${opcion.value}`
                                    "
                                    class="opcion"
                                >

                                    <RadioButton
                                        v-model="
                                            form[pregunta.campo]
                                        "
                                        :inputId="
                                            `${pregunta.campo}-${opcion.value}`
                                        "
                                        :name="pregunta.campo"
                                        :value="opcion.value"
                                    />

                                    <span>
                                        {{ opcion.label }}
                                    </span>

                                </label>

                            </div>


                            <small
                                v-if="errores[pregunta.campo]"
                                class="error"
                            >
                                {{ obtenerError(pregunta.campo) }}
                            </small>

                        </div>

                    </div>

                </section>


                <!-- =================================================
                     BOTONES
                ================================================== -->

                <div class="acciones">

                    <Button
                        type="button"
                        label="Limpiar"
                        icon="pi pi-refresh"
                        severity="secondary"
                        outlined
                        @click="limpiar"
                    />

                    <Button
                        type="submit"
                        label="Enviar ficha"
                        icon="pi pi-send"
                        :loading="guardando"
                        :disabled="guardando"
                    />

                </div>


                <div class="pie">

                    <i class="pi pi-lock"></i>

                    La información registrada será tratada
                    de manera confidencial.

                </div>

            </form>

        </div>

    </div>

</template>


<style scoped>

/* =========================================================
   GENERAL
========================================================= */

* {
    box-sizing: border-box;
}

.pagina {
    min-height: 100vh;
    background:
        linear-gradient(
            180deg,
            #eef6ff 0,
            #f6f8fb 300px,
            #f6f8fb 100%
        );
    padding: 32px 16px 60px;
}

.contenedor {
    width: 100%;
    max-width: 980px;
    margin: auto;
}


/* =========================================================
   CABECERA
========================================================= */

.cabecera {
    display: flex;
    align-items: center;
    gap: 20px;
    background: linear-gradient(
        135deg,
        #0757a6,
        #1684cf
    );
    color: #fff;
    padding: 30px;
    border-radius: 16px 16px 0 0;
    box-shadow: 0 8px 25px rgba(0, 70, 140, .14);
}

.cabecera-icono {
    width: 65px;
    height: 65px;
    min-width: 65px;
    border-radius: 15px;
    background: rgba(255, 255, 255, .16);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 29px;
}

.cabecera h1 {
    font-size: 1.55rem;
    line-height: 1.35;
    margin: 6px 0 8px;
    font-weight: 700;
}

.etiqueta {
    display: inline-block;
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: .8px;
    background: rgba(255, 255, 255, .15);
    padding: 5px 10px;
    border-radius: 20px;
}

.declaracion {
    margin: 0;
    opacity: .9;
    font-size: .9rem;
}


/* =========================================================
   CONSENTIMIENTO
========================================================= */

.consentimiento {
    display: flex;
    gap: 14px;
    background: #fff;
    border: 1px solid #dae7f4;
    border-top: none;
    padding: 22px 26px;
    margin-bottom: 22px;
    border-radius: 0 0 16px 16px;
}

.consentimiento > i {
    color: #1479c9;
    font-size: 1.4rem;
    margin-top: 2px;
}

.consentimiento strong {
    color: #164f7e;
}

.consentimiento p {
    margin: 8px 0 10px;
    font-size: .91rem;
    line-height: 1.6;
    color: #56616d;
}

.consentimiento span {
    color: #68727d;
    font-size: .82rem;
}

.consentimiento b {
    color: #e53935;
}


/* =========================================================
   MENSAJES
========================================================= */

.mensaje {
    margin-bottom: 20px;
}


/* =========================================================
   CARD
========================================================= */

.card {
    background: #fff;
    border-radius: 14px;
    margin-bottom: 24px;
    overflow: hidden;
    border: 1px solid #e3e8ef;
    box-shadow: 0 3px 12px rgba(30, 60, 90, .06);
}

.card-header {
    display: flex;
    gap: 14px;
    align-items: center;
    padding: 18px 22px;
    background: #f6faff;
    border-bottom: 1px solid #e1edf8;
}

.numero {
    width: 42px;
    height: 42px;
    min-width: 42px;
    border-radius: 10px;
    background: #1479c9;
    color: #fff;
    font-size: .85rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card-header h2 {
    color: #164f7e;
    margin: 0;
    font-size: 1.05rem;
}

.card-header p {
    margin: 4px 0 0;
    color: #7a8490;
    font-size: .82rem;
}


/* =========================================================
   CAMPOS
========================================================= */

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 21px;
    padding: 25px;
}

.campo {
    min-width: 0;
}

.campo-completo {
    grid-column: 1 / -1;
}

.campo label {
    display: block;
    margin-bottom: 8px;
    color: #364553;
    font-size: .88rem;
    font-weight: 600;
}

.campo label b {
    color: #e53935;
}

.error {
    display: block;
    color: #d32f2f;
    margin-top: 5px;
    font-size: .78rem;
}


/* =========================================================
   PRIMEVUE
========================================================= */

:deep(.p-inputtext),
:deep(.p-dropdown),
:deep(.p-inputtextarea) {
    width: 100%;
}

:deep(.p-inputtext),
:deep(.p-dropdown) {
    min-height: 43px;
}

:deep(.p-dropdown-label) {
    display: flex;
    align-items: center;
}


/* =========================================================
   PREGUNTAS
========================================================= */

.preguntas {
    padding: 5px 24px;
}

.pregunta {
    padding: 21px 4px;
    border-bottom: 1px solid #edf0f3;
}

.pregunta:last-child {
    border-bottom: none;
}

.pregunta-texto {
    font-size: .92rem;
    color: #303b45;
    line-height: 1.5;
    font-weight: 600;
    margin-bottom: 14px;
}

.pregunta-texto b {
    color: #e53935;
}

.opciones {
    display: flex;
    flex-wrap: wrap;
    gap: 9px;
}

.opcion {
    display: flex;
    align-items: center;
    gap: 7px;
    border: 1px solid #e0e6ec;
    padding: 9px 13px;
    border-radius: 8px;
    cursor: pointer;
    background: #fafbfd;
    transition: all .15s ease;
}

.opcion:hover {
    border-color: #68aee0;
    background: #f1f8fd;
}

.opcion span {
    color: #46535f;
    font-size: .84rem;
    white-space: nowrap;
}


/* =========================================================
   BOTONES
========================================================= */

.acciones {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 5px 0 20px;
}

.pie {
    text-align: center;
    color: #84909b;
    font-size: .8rem;
}

.pie i {
    margin-right: 4px;
}


/* =========================================================
   TABLET
========================================================= */

@media (max-width: 768px) {

    .pagina {
        padding: 15px 10px 40px;
    }

    .cabecera {
        padding: 22px 18px;
        border-radius: 12px 12px 0 0;
    }

    .cabecera-icono {
        width: 52px;
        height: 52px;
        min-width: 52px;
        font-size: 23px;
    }

    .cabecera h1 {
        font-size: 1.2rem;
    }

    .consentimiento {
        padding: 18px;
    }

    .form-grid {
        grid-template-columns: 1fr;
        padding: 20px 18px;
        gap: 18px;
    }

    .campo-completo {
        grid-column: auto;
    }

    .preguntas {
        padding: 4px 18px;
    }

    .card-header {
        padding: 16px 18px;
    }

}


/* =========================================================
   CELULAR
========================================================= */

@media (max-width: 520px) {

    .pagina {
        padding: 0 0 35px;
    }

    .contenedor {
        max-width: 100%;
    }

    .cabecera {
        border-radius: 0;
        padding: 20px 15px;
        align-items: flex-start;
    }

    .cabecera-icono {
        width: 45px;
        height: 45px;
        min-width: 45px;
        border-radius: 10px;
        font-size: 20px;
    }

    .cabecera h1 {
        font-size: 1rem;
        line-height: 1.4;
    }

    .etiqueta {
        font-size: .62rem;
    }

    .declaracion {
        font-size: .77rem;
    }

    .consentimiento {
        border-radius: 0;
        margin-bottom: 14px;
        padding: 16px 14px;
    }

    .consentimiento p {
        font-size: .83rem;
    }

    .card {
        border-radius: 0;
        margin-bottom: 14px;
        box-shadow: none;
        border-left: none;
        border-right: none;
    }

    .card-header {
        padding: 15px;
    }

    .numero {
        width: 37px;
        height: 37px;
        min-width: 37px;
    }

    .card-header h2 {
        font-size: .95rem;
    }

    .form-grid {
        padding: 18px 15px;
    }

    .preguntas {
        padding: 0 15px;
    }

    .pregunta {
        padding: 18px 0;
    }

    .pregunta-texto {
        font-size: .88rem;
    }

    .opciones {
        flex-direction: column;
        gap: 8px;
    }

    .opcion {
        width: 100%;
        padding: 11px 12px;
    }

    .opcion span {
        white-space: normal;
    }

    .acciones {
        padding: 8px 15px 20px;
        flex-direction: column-reverse;
    }

    .acciones :deep(.p-button) {
        width: 100%;
        justify-content: center;
    }

    .pie {
        padding: 0 15px;
    }

}

</style>