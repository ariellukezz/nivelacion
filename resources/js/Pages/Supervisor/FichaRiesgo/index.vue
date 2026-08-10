<script setup>

import { onMounted, reactive, ref } from 'vue';
import axios from 'axios';
import XLSX from 'xlsx';

import LayoutSupervisor from '@/Layouts/LayoutSupervisor.vue';

import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dialog from 'primevue/dialog';
import RadioButton from 'primevue/radiobutton';
import Textarea from 'primevue/textarea';
import Message from 'primevue/message';


/* =========================================================
   LISTADO
========================================================= */

const cargando = ref(false);

const rows = ref([]);

const resumen = ref({
    total: 0,
    tercera: 0,
    cuarta: 0
});

const escuelasFiltro = ref([]);

const pagina = ref(1);
const ultimaPagina = ref(1);
const totalRegistros = ref(0);
const desde = ref(0);
const hasta = ref(0);


/* =========================================================
   FILTROS
========================================================= */

const filtros = reactive({

    q: '',

    escuela: null,

    ciclo: null,

    condicion: null,

    fecha_desde: '',

    fecha_hasta: '',

    per_page: 20

});


/* =========================================================
   ESCUELAS PROFESIONALES OFICIALES
========================================================= */

const escuelasProfesionales = [

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
   CICLOS
========================================================= */

const ciclos = Array.from(

    { length: 15 },

    (_, i) => ({

        label: `Ciclo ${i + 1}`,

        value: i + 1

    })

);


/* =========================================================
   CONDICIÓN DE MATRÍCULA
========================================================= */

const condiciones = [

    {
        label: 'Tercera matrícula',
        value: 3
    },

    {
        label: 'Cuarta matrícula',
        value: 4
    }

];


/* =========================================================
   CANTIDAD POR PÁGINA
========================================================= */

const cantidadesPagina = [

    {
        label: '10 registros',
        value: 10
    },

    {
        label: '20 registros',
        value: 20
    },

    {
        label: '50 registros',
        value: 50
    },

    {
        label: '100 registros',
        value: 100
    }

];


/* =========================================================
   FRECUENCIAS
========================================================= */

const frecuencias = [

    {
        label: 'Nunca',
        value: 1
    },

    {
        label: 'Casi nunca',
        value: 2
    },

    {
        label: 'A veces',
        value: 3
    },

    {
        label: 'Casi siempre',
        value: 4
    },

    {
        label: 'Siempre',
        value: 5
    }

];


const opcionesSiNo = [

    {
        label: 'SI',
        value: 1
    },

    {
        label: 'NO',
        value: 0
    }

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
   CARGAR REPORTE
========================================================= */

const cargar = async (nuevaPagina = 1) => {

    cargando.value = true;

    try {

        const response = await axios.get(

            '/supervisor/fichas-riesgo-data',

            {
                params: {

                    ...filtros,

                    page: nuevaPagina

                }
            }

        );


        if (!response.data.ok) {

            return;

        }


        resumen.value = response.data.resumen;


        escuelasFiltro.value = (

            response.data.escuelas || []

        ).map(item => ({

            label: item,

            value: item

        }));


        const paginacion = response.data.fichas;


        rows.value =
            paginacion.data || [];


        pagina.value =
            paginacion.current_page || 1;


        ultimaPagina.value =
            paginacion.last_page || 1;


        totalRegistros.value =
            paginacion.total || 0;


        desde.value =
            paginacion.from || 0;


        hasta.value =
            paginacion.to || 0;


    } catch (error) {

        console.error(
            'Error cargando reporte:',
            error
        );

    } finally {

        cargando.value = false;

    }

};


/* =========================================================
   LIMPIAR FILTROS
========================================================= */

const limpiar = () => {

    filtros.q = '';

    filtros.escuela = null;

    filtros.ciclo = null;

    filtros.condicion = null;

    filtros.fecha_desde = '';

    filtros.fecha_hasta = '';

    filtros.per_page = 20;


    cargar(1);

};


/* =========================================================
   PAGINACIÓN
========================================================= */

const paginaAnterior = () => {

    if (pagina.value > 1) {

        cargar(
            pagina.value - 1
        );

    }

};


const paginaSiguiente = () => {

    if (
        pagina.value <
        ultimaPagina.value
    ) {

        cargar(
            pagina.value + 1
        );

    }

};


/* =========================================================
   TEXTOS
========================================================= */

const condicionTexto = (valor) => {

    if (Number(valor) === 3) {

        return 'Tercera matrícula';

    }


    if (Number(valor) === 4) {

        return 'Cuarta matrícula';

    }


    return '—';

};


const fechaTexto = (fecha) => {

    if (!fecha) {

        return '—';

    }


    return String(fecha)

        .substring(0, 10)

        .split('-')

        .reverse()

        .join('/');

};


/* =========================================================
   MODAL
========================================================= */

const mostrarModal = ref(false);

const cargandoFicha = ref(false);

const guardando = ref(false);

const mensajeModal = ref('');

const errorModal = ref('');

const errores = ref({});


/* =========================================================
   FICHA EDITABLE
========================================================= */

const ficha = reactive({

    id: null,

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


    f1: null,
    f2: null,
    f3: null,
    f4: null,
    f5: null,
    f6: null,
    f7: null,
    f8: null

});


/* =========================================================
   VER / EDITAR
========================================================= */

const abrirFicha = async (id) => {

    mostrarModal.value = true;

    cargandoFicha.value = true;

    mensajeModal.value = '';

    errorModal.value = '';

    errores.value = {};


    try {

        const response = await axios.get(

            `/supervisor/fichas-riesgo/${id}`

        );


        if (response.data.ok) {

            const datos =
                response.data.ficha;


            Object.keys(ficha).forEach(key => {

                if (

                    Object.prototype
                        .hasOwnProperty
                        .call(
                            datos,
                            key
                        )

                ) {

                    ficha[key] =
                        datos[key];

                }

            });


            if (datos.fecha) {

                ficha.fecha =
                    String(datos.fecha)
                        .substring(0, 10);

            }

        }


    } catch (error) {

        console.error(error);


        errorModal.value =
            error.response?.data?.mensaje ||
            'No se pudo cargar la ficha.';


    } finally {

        cargandoFicha.value = false;

    }

};


/* =========================================================
   GUARDAR EDICIÓN
========================================================= */

const guardarEdicion = async () => {

    guardando.value = true;

    mensajeModal.value = '';

    errorModal.value = '';

    errores.value = {};


    try {

        const response = await axios.put(

            `/supervisor/fichas-riesgo/${ficha.id}`,

            ficha

        );


        if (response.data.ok) {

            mensajeModal.value =
                response.data.mensaje ||
                'Ficha actualizada correctamente.';


            await cargar(
                pagina.value
            );

        }


    } catch (error) {

        console.error(error);


        if (
            error.response?.status === 422
        ) {

            errores.value =
                error.response.data.errors || {};


            errorModal.value =
                error.response.data.mensaje ||
                'Revise los campos del formulario.';

        } else {

            errorModal.value =
                error.response?.data?.mensaje ||
                'No se pudo actualizar la ficha.';

        }


    } finally {

        guardando.value = false;

    }

};


/* =========================================================
   OBTENER ERROR
========================================================= */

const obtenerError = (campo) => {

    if (!errores.value[campo]) {

        return '';

    }


    return errores.value[campo][0];

};


/* =========================================================
   IMPRIMIR
========================================================= */

const imprimirFicha = (id) => {

    window.open(

        `/supervisor/fichas-riesgo/${id}/imprimir`,

        '_blank'

    );

};


/* =========================================================
   PDF
========================================================= */

const descargarPDF = (id) => {

    window.open(

        `/supervisor/fichas-riesgo/${id}/pdf`,

        '_blank'

    );

};


/* =========================================================
   ELIMINAR
========================================================= */

const idEliminando = ref(null);


const eliminarFicha = async (registro) => {

    const confirmar = window.confirm(

        '¿Está seguro de eliminar esta ficha?\n\n' +

        'Estudiante: ' +

        registro.nombres_apellidos +

        '\nDNI: ' +

        registro.dni +

        '\n\nEsta acción eliminará el registro.'

    );


    if (!confirmar) {

        return;

    }


    idEliminando.value =
        registro.id;


    try {

        const response =
            await axios.delete(

                `/supervisor/fichas-riesgo/${registro.id}`

            );


        if (response.data.ok) {

            alert(

                response.data.mensaje ||
                'Ficha eliminada correctamente.'

            );


            if (

                rows.value.length === 1 &&

                pagina.value > 1

            ) {

                await cargar(
                    pagina.value - 1
                );

            } else {

                await cargar(
                    pagina.value
                );

            }

        }


    } catch (error) {

        console.error(error);


        alert(

            error.response?.data?.mensaje ||
            'No se pudo eliminar la ficha.'

        );


    } finally {

        idEliminando.value = null;

    }

};


/* =========================================================
   FUNCIONES EXCEL
========================================================= */

const frecuenciaExcel = (valor) => {

    const respuestas = {

        1: 'Nunca',

        2: 'Casi nunca',

        3: 'A veces',

        4: 'Casi siempre',

        5: 'Siempre'

    };


    return respuestas[
        Number(valor)
    ] || '';

};


const siNoExcel = (valor) => {

    if (
        valor === null ||
        valor === undefined
    ) {

        return '';

    }


    return Number(valor) === 1
        ? 'SI'
        : 'NO';

};


const condicionExcel = (valor) => {

    if (Number(valor) === 3) {

        return 'Tercera matrícula';

    }


    if (Number(valor) === 4) {

        return 'Cuarta matrícula';

    }


    return '';

};


/* =========================================================
   EXPORTAR EXCEL
========================================================= */

const exportandoExcel = ref(false);


const exportarExcel = async () => {

    exportandoExcel.value = true;


    try {

        const response = await axios.get(

            '/supervisor/fichas-riesgo-excel'

        );


        if (!response.data.ok) {

            alert(
                'No se pudo obtener la información.'
            );

            return;

        }


        const datos =
            response.data.datos || [];


        if (datos.length === 0) {

            alert(
                'No existen registros para exportar.'
            );

            return;

        }


        const datosExcel =
            datos.map(
                (item, index) => ({


                    /* =========================================
                       DATOS GENERALES
                    ========================================= */

                    'N°':
                        index + 1,


                    'FECHA':

                        item.fecha

                            ? String(item.fecha)
                                .substring(0, 10)
                                .split('-')
                                .reverse()
                                .join('/')

                            : '',


                    'FECHA DE REGISTRO':

                        item.created_at

                            ? String(item.created_at)
                                .replace('T', ' ')
                                .substring(0, 19)

                            : '',


                    'CONDICIÓN DE MATRÍCULA':

                        condicionExcel(
                            item.condicion_matricula
                        ),


                    'NOMBRES Y APELLIDOS':

                        item.nombres_apellidos || '',


                    'ESCUELA PROFESIONAL':

                        item.escuela_profesional || '',


                    'CICLO ACADÉMICO':

                        item.ciclo_academico || '',


                    'CÓDIGO':

                        item.codigo || '',


                    'DNI':

                        item.dni || '',


                    'NÚMERO DE CELULAR':

                        item.celular || '',


                    'NÚMERO DE CELULAR DE SU TUTOR (PADRE O MADRE)':

                        item.celular_tutor || '',


                    'NÚMERO DE CELULAR DE PARIENTE (HERMANO O FAMILIARES)':

                        item.celular_pariente || '',


                    'FACEBOOK':

                        item.facebook || '',


                    'CORREO ELECTRÓNICO':

                        item.correo_registro || '',


                    'CORREO':

                        item.correo || '',


                    'LUGAR DE PROCEDENCIA':

                        item.lugar_procedencia || '',


                    'DIRECCIÓN ACTUAL DE RESIDENCIA':

                        item.direccion_actual || '',


                    /* =========================================
                       ACADÉMICOS
                    ========================================= */

                    'A.1 Dificultades para asistir puntualmente':

                        frecuenciaExcel(
                            item.a1
                        ),


                    'A.2 Reprobación de exámenes parciales':

                        frecuenciaExcel(
                            item.a2
                        ),


                    'A.3 Dificultades para trabajar en grupo':

                        frecuenciaExcel(
                            item.a3
                        ),


                    'A.4 Dificultades para exponer':

                        frecuenciaExcel(
                            item.a4
                        ),


                    'A.5 Dificultades para realizar y presentar trabajos':

                        frecuenciaExcel(
                            item.a5
                        ),


                    'A.6 Conflictos con algún docente':

                        frecuenciaExcel(
                            item.a6
                        ),


                    'A.7 Habilidades y capacidades de aprender':

                        frecuenciaExcel(
                            item.a7
                        ),


                    'A.8 Técnicas y hábitos de estudio':

                        frecuenciaExcel(
                            item.a8
                        ),


                    'A.9 Vocación e identificación de la carrera':

                        frecuenciaExcel(
                            item.a9
                        ),


                    'A.10 Interés y motivación para estudiar':

                        frecuenciaExcel(
                            item.a10
                        ),


                    /* =========================================
                       PERSONALES
                    ========================================= */

                    'P.1 Problemas con la salud y estado físico':

                        frecuenciaExcel(
                            item.p1
                        ),


                    'P.2 Problemas con la alimentación':

                        frecuenciaExcel(
                            item.p2
                        ),


                    'P.3 Cuenta con una vivienda propia':

                        siNoExcel(
                            item.p3
                        ),


                    'P.4 Problemas con la autonomía y toma de decisiones':

                        frecuenciaExcel(
                            item.p4
                        ),


                    'P.5 Conflictos en las relaciones con sus compañeros':

                        frecuenciaExcel(
                            item.p5
                        ),


                    'P.6 Dificultades para integrarse al grupo':

                        frecuenciaExcel(
                            item.p6
                        ),


                    'P.7 Se siente estresado continuamente':

                        frecuenciaExcel(
                            item.p7
                        ),


                    'P.8 Problemas con la seguridad personal / emocional':

                        frecuenciaExcel(
                            item.p8
                        ),


                    'P.9 Se siente discriminado (a), marginado':

                        frecuenciaExcel(
                            item.p9
                        ),


                    'P.10 Problemas con sus creencias, religión':

                        siNoExcel(
                            item.p10
                        ),


                    'P.11 Hostigamiento sexual':

                        frecuenciaExcel(
                            item.p11
                        ),


                    'P.12 Limitaciones para establecer metas y aspiraciones personales (proyecto de vida)':

                        frecuenciaExcel(
                            item.p12
                        ),


                    'P.13 Problemas con la autoestima':

                        frecuenciaExcel(
                            item.p13
                        ),


                    /* =========================================
                       FAMILIARES
                    ========================================= */

                    'F.1 Conflicto en su relación con un familiar':

                        frecuenciaExcel(
                            item.f1
                        ),


                    'F.2 Vive solo y le afecta':

                        frecuenciaExcel(
                            item.f2
                        ),


                    'F.3 No cuenta con el soporte económico familiar para continuar sus estudios':

                        frecuenciaExcel(
                            item.f3
                        ),


                    'F.4 Tiene un familiar enfermo':

                        siNoExcel(
                            item.f4
                        ),


                    'F.5 Tiene familiares que dependen del estudiante':

                        siNoExcel(
                            item.f5
                        ),


                    'F.6 Tiene problemas de convivencia en pareja':

                        frecuenciaExcel(
                            item.f6
                        ),


                    'F.7 Tiene hijos y dificultades para afrontar sus responsabilidades':

                        siNoExcel(
                            item.f7
                        ),


                    'F.8 Ha sufrido la pérdida de un familiar cercano':

                        siNoExcel(
                            item.f8
                        )

                })
            );


        /* =====================================================
           CREAR HOJA
        ===================================================== */

        const hoja =
            XLSX.utils.json_to_sheet(
                datosExcel
            );


        /* =====================================================
           ANCHO DE COLUMNAS
        ===================================================== */

        const encabezados =
            Object.keys(
                datosExcel[0]
            );


        hoja['!cols'] =
            encabezados.map(
                encabezado => {

                    let ancho = 18;


                    if (
                        encabezado ===
                        'NOMBRES Y APELLIDOS'
                    ) {

                        ancho = 32;

                    }


                    if (
                        encabezado ===
                        'ESCUELA PROFESIONAL'
                    ) {

                        ancho = 38;

                    }


                    if (
                        encabezado.includes(
                            'DIRECCIÓN'
                        )
                    ) {

                        ancho = 35;

                    }


                    if (
                        encabezado.includes(
                            'CORREO'
                        )
                    ) {

                        ancho = 28;

                    }


                    if (
                        encabezado.startsWith(
                            'A.'
                        ) ||
                        encabezado.startsWith(
                            'P.'
                        ) ||
                        encabezado.startsWith(
                            'F.'
                        )
                    ) {

                        ancho = 42;

                    }


                    return {

                        wch: ancho

                    };

                }
            );


        /* =====================================================
           CREAR LIBRO
        ===================================================== */

        const libro =
            XLSX.utils.book_new();


        XLSX.utils.book_append_sheet(

            libro,

            hoja,

            'Fichas Riesgo'

        );


        /* =====================================================
           NOMBRE ARCHIVO
        ===================================================== */

        const hoy = new Date();


        const fechaArchivo =

            hoy.getFullYear() +

            '-' +

            String(
                hoy.getMonth() + 1
            ).padStart(
                2,
                '0'
            ) +

            '-' +

            String(
                hoy.getDate()
            ).padStart(
                2,
                '0'
            );


        /* =====================================================
           DESCARGAR
        ===================================================== */

        XLSX.writeFile(

            libro,

            `fichas_riesgo_academico_${fechaArchivo}.xlsx`

        );


    } catch (error) {

        console.error(
            'Error exportando Excel:',
            error
        );


        alert(
            'Ocurrió un error al exportar el Excel.'
        );


    } finally {

        exportandoExcel.value = false;

    }

};


/* =========================================================
   INICIO
========================================================= */

onMounted(() => {

    cargar();

});

</script>


<template>

    <LayoutSupervisor>

        <div class="reporte">


            <!-- =====================================================
                 CABECERA
            ====================================================== -->

            <div class="cabecera-reporte">

                <h2>
                    Fichas de Riesgo Académico
                </h2>

                <p>
                    Consulta, búsqueda, edición, impresión,
                    exportación y administración de las fichas
                    registradas.
                </p>

            </div>


            <!-- =====================================================
                 RESUMEN
            ====================================================== -->

            <div class="resumen-grid">


                <div class="resumen-card">

                    <div class="resumen-icono total">

                        <i class="pi pi-file"></i>

                    </div>

                    <div>

                        <span>
                            Total de fichas
                        </span>

                        <strong>
                            {{ resumen.total }}
                        </strong>

                    </div>

                </div>


                <div class="resumen-card">

                    <div class="resumen-icono tercera-icon">

                        <i class="pi pi-users"></i>

                    </div>

                    <div>

                        <span>
                            Tercera matrícula
                        </span>

                        <strong>
                            {{ resumen.tercera }}
                        </strong>

                    </div>

                </div>


                <div class="resumen-card">

                    <div class="resumen-icono cuarta-icon">

                        <i class="pi pi-exclamation-triangle"></i>

                    </div>

                    <div>

                        <span>
                            Cuarta matrícula
                        </span>

                        <strong>
                            {{ resumen.cuarta }}
                        </strong>

                    </div>

                </div>

            </div>


            <!-- =====================================================
                 FILTROS
            ====================================================== -->

            <div class="panel">

                <div class="panel-titulo">

                    <i class="pi pi-filter"></i>

                    <span>
                        Filtros de búsqueda
                    </span>

                </div>


                <div class="filtros-grid">


                    <!-- BUSCAR -->

                    <div class="filtro buscador">

                        <label>
                            Buscar estudiante
                        </label>

                        <InputText
                            v-model="filtros.q"
                            placeholder="Nombre, DNI, código o escuela"
                            class="w-full"
                            @keyup.enter="cargar(1)"
                        />

                    </div>


                    <!-- ESCUELA -->

                    <div class="filtro">

                        <label>
                            Escuela Profesional
                        </label>

                        <Dropdown
                            v-model="filtros.escuela"
                            :options="escuelasFiltro"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Todas"
                            filter
                            showClear
                            class="w-full"
                        />

                    </div>


                    <!-- CICLO -->

                    <div class="filtro">

                        <label>
                            Ciclo académico
                        </label>

                        <Dropdown
                            v-model="filtros.ciclo"
                            :options="ciclos"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Todos"
                            showClear
                            class="w-full"
                        />

                    </div>


                    <!-- CONDICIÓN -->

                    <div class="filtro">

                        <label>
                            Condición de matrícula
                        </label>

                        <Dropdown
                            v-model="filtros.condicion"
                            :options="condiciones"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Todas"
                            showClear
                            class="w-full"
                        />

                    </div>


                    <!-- FECHA DESDE -->

                    <div class="filtro">

                        <label>
                            Fecha desde
                        </label>

                        <InputText
                            v-model="filtros.fecha_desde"
                            type="date"
                            class="w-full"
                        />

                    </div>


                    <!-- FECHA HASTA -->

                    <div class="filtro">

                        <label>
                            Fecha hasta
                        </label>

                        <InputText
                            v-model="filtros.fecha_hasta"
                            type="date"
                            class="w-full"
                        />

                    </div>


                    <!-- MOSTRAR -->

                    <div class="filtro">

                        <label>
                            Mostrar
                        </label>

                        <Dropdown
                            v-model="filtros.per_page"
                            :options="cantidadesPagina"
                            optionLabel="label"
                            optionValue="value"
                            class="w-full"
                        />

                    </div>

                </div>


                <!-- BOTONES -->

                <div class="botones-filtro">

                    <Button
                        label="Buscar"
                        icon="pi pi-search"
                        size="small"
                        :loading="cargando"
                        @click="cargar(1)"
                    />


                    <Button
                        label="Limpiar"
                        icon="pi pi-filter-slash"
                        severity="secondary"
                        size="small"
                        outlined
                        @click="limpiar"
                    />


                    <Button
                        label="Exportar Excel"
                        icon="pi pi-file-excel"
                        severity="success"
                        size="small"
                        :loading="exportandoExcel"
                        @click="exportarExcel"
                    />

                </div>

            </div>


            <!-- =====================================================
                 TABLA
            ====================================================== -->

            <div class="panel tabla-panel">

                <div class="panel-titulo">

                    <i class="pi pi-list"></i>

                    <span>
                        Fichas registradas
                    </span>

                </div>


                <DataTable
                    :value="rows"
                    :loading="cargando"
                    stripedRows
                    responsiveLayout="scroll"
                    class="p-datatable-sm"
                >

                    <template #empty>

                        <div class="sin-registros">

                            <i class="pi pi-inbox"></i>

                            <span>
                                No se encontraron registros.
                            </span>

                        </div>

                    </template>


                    <!-- FECHA -->

                    <Column
                        field="fecha"
                        header="FECHA"
                    >

                        <template #body="{ data }">

                            {{
                                fechaTexto(
                                    data.fecha
                                )
                            }}

                        </template>

                    </Column>


                    <!-- CÓDIGO -->

                    <Column
                        field="codigo"
                        header="CÓDIGO"
                    />


                    <!-- DNI -->

                    <Column
                        field="dni"
                        header="DNI"
                    />


                    <!-- ESTUDIANTE -->

                    <Column
                        field="nombres_apellidos"
                        header="ESTUDIANTE"
                        style="min-width: 210px"
                    />


                    <!-- ESCUELA -->

                    <Column
                        field="escuela_profesional"
                        header="ESCUELA"
                        style="min-width: 240px"
                    />


                    <!-- CICLO -->

                    <Column
                        field="ciclo_academico"
                        header="CICLO"
                    />


                    <!-- CONDICIÓN -->

                    <Column
                        header="CONDICIÓN"
                        style="min-width: 160px"
                    >

                        <template #body="{ data }">

                            <span
                                class="condicion"
                                :class="{

                                    tercera:
                                        Number(
                                            data.condicion_matricula
                                        ) === 3,

                                    cuarta:
                                        Number(
                                            data.condicion_matricula
                                        ) === 4

                                }"
                            >

                                {{
                                    condicionTexto(
                                        data.condicion_matricula
                                    )
                                }}

                            </span>

                        </template>

                    </Column>


                    <!-- ACCIONES -->

                    <Column
                        header="ACCIONES"
                        style="min-width: 300px"
                    >

                        <template #body="{ data }">

                            <div class="acciones-tabla">


                                <!-- EDITAR -->

                                <Button
                                    icon="pi pi-pencil"
                                    label="Editar"
                                    size="small"
                                    outlined
                                    @click="
                                        abrirFicha(
                                            data.id
                                        )
                                    "
                                />


                                <!-- IMPRIMIR -->

                                <Button
                                    icon="pi pi-print"
                                    size="small"
                                    severity="secondary"
                                    outlined
                                    title="Imprimir ficha"
                                    @click="
                                        imprimirFicha(
                                            data.id
                                        )
                                    "
                                />


                                <!-- PDF -->

                                <Button
                                    icon="pi pi-file-pdf"
                                    size="small"
                                    severity="danger"
                                    outlined
                                    title="Descargar PDF"
                                    @click="
                                        descargarPDF(
                                            data.id
                                        )
                                    "
                                />


                                <!-- ELIMINAR -->

                                <Button
                                    icon="pi pi-trash"
                                    size="small"
                                    severity="danger"
                                    title="Eliminar ficha"
                                    :loading="
                                        idEliminando ===
                                        data.id
                                    "
                                    :disabled="
                                        idEliminando !== null
                                    "
                                    @click="
                                        eliminarFicha(
                                            data
                                        )
                                    "
                                />

                            </div>

                        </template>

                    </Column>

                </DataTable>


                <!-- =================================================
                     PAGINACIÓN
                ================================================== -->

                <div class="paginacion">

                    <div class="paginacion-info">

                        Mostrando

                        <strong>
                            {{ desde }}
                        </strong>

                        -

                        <strong>
                            {{ hasta }}
                        </strong>

                        de

                        <strong>
                            {{ totalRegistros }}
                        </strong>

                        registros

                    </div>


                    <div class="paginacion-botones">

                        <Button
                            icon="pi pi-chevron-left"
                            size="small"
                            severity="secondary"
                            outlined
                            :disabled="
                                pagina <= 1
                            "
                            @click="
                                paginaAnterior
                            "
                        />


                        <span class="pagina-actual">

                            Página
                            {{ pagina }}
                            de
                            {{ ultimaPagina }}

                        </span>


                        <Button
                            icon="pi pi-chevron-right"
                            size="small"
                            severity="secondary"
                            outlined
                            :disabled="
                                pagina >=
                                ultimaPagina
                            "
                            @click="
                                paginaSiguiente
                            "
                        />

                    </div>

                </div>

            </div>


            <!-- =====================================================
                 MODAL EDITAR
            ====================================================== -->

            <Dialog
                v-model:visible="mostrarModal"
                modal
                header="Ficha de Riesgo Académico"
                :draggable="false"
                :style="{
                    width: '95vw',
                    maxWidth: '1100px'
                }"
            >


                <!-- CARGANDO -->

                <div
                    v-if="cargandoFicha"
                    class="cargando-modal"
                >

                    <i
                        class="
                            pi
                            pi-spin
                            pi-spinner
                        "
                    ></i>

                    <span>
                        Cargando ficha...
                    </span>

                </div>


                <div v-else>


                    <Message
                        v-if="mensajeModal"
                        severity="success"
                        class="mb-3"
                    >

                        {{ mensajeModal }}

                    </Message>


                    <Message
                        v-if="errorModal"
                        severity="error"
                        class="mb-3"
                    >

                        {{ errorModal }}

                    </Message>


                    <!-- =============================================
                         DATOS DEL ESTUDIANTE
                    ============================================== -->

                    <div class="seccion-modal">

                        <div class="titulo-modal">

                            <i class="pi pi-user"></i>

                            DATOS DEL ESTUDIANTE

                        </div>


                        <div class="modal-grid">


                            <!-- CORREO REGISTRO -->

                            <div class="campo">

                                <label>
                                    Correo electrónico
                                </label>

                                <InputText
                                    v-model="
                                        ficha.correo_registro
                                    "
                                    type="email"
                                    class="w-full"
                                    :class="{
                                        'p-invalid':
                                            errores.correo_registro
                                    }"
                                />

                                <small
                                    v-if="
                                        errores.correo_registro
                                    "
                                    class="p-error"
                                >

                                    {{
                                        obtenerError(
                                            'correo_registro'
                                        )
                                    }}

                                </small>

                            </div>


                            <!-- FECHA -->

                            <div class="campo">

                                <label>
                                    Fecha
                                </label>

                                <InputText
                                    v-model="ficha.fecha"
                                    type="date"
                                    class="w-full"
                                />

                            </div>


                            <!-- CONDICIÓN -->

                            <div class="campo">

                                <label>
                                    Condición de matrícula
                                </label>

                                <Dropdown
                                    v-model="
                                        ficha.condicion_matricula
                                    "
                                    :options="condiciones"
                                    optionLabel="label"
                                    optionValue="value"
                                    placeholder="Seleccione"
                                    class="w-full"
                                />

                            </div>


                            <!-- CICLO -->

                            <div class="campo">

                                <label>
                                    Ciclo académico
                                </label>

                                <Dropdown
                                    v-model="
                                        ficha.ciclo_academico
                                    "
                                    :options="ciclos"
                                    optionLabel="label"
                                    optionValue="value"
                                    placeholder="Seleccione"
                                    class="w-full"
                                />

                            </div>


                            <!-- NOMBRES -->

                            <div class="campo campo-full">

                                <label>
                                    Nombres y Apellidos
                                </label>

                                <InputText
                                    v-model="
                                        ficha.nombres_apellidos
                                    "
                                    class="w-full"
                                />

                            </div>


                            <!-- ESCUELA CORREGIDA -->

                            <div class="campo campo-full">

                                <label>
                                    Escuela Profesional
                                </label>

                                <Dropdown
                                    v-model="
                                        ficha.escuela_profesional
                                    "
                                    :options="
                                        escuelasProfesionales
                                    "
                                    optionLabel="label"
                                    optionValue="value"
                                    placeholder="Seleccione Escuela Profesional"
                                    filter
                                    class="w-full"
                                />

                                <small
                                    v-if="
                                        errores.escuela_profesional
                                    "
                                    class="p-error"
                                >

                                    {{
                                        obtenerError(
                                            'escuela_profesional'
                                        )
                                    }}

                                </small>

                            </div>


                            <!-- CÓDIGO -->

                            <div class="campo">

                                <label>
                                    Código
                                </label>

                                <InputText
                                    v-model="ficha.codigo"
                                    class="w-full"
                                />

                            </div>


                            <!-- DNI -->

                            <div class="campo">

                                <label>
                                    DNI
                                </label>

                                <InputText
                                    v-model="ficha.dni"
                                    class="w-full"
                                />

                            </div>


                            <!-- CELULAR -->

                            <div class="campo">

                                <label>
                                    Numero de celular
                                </label>

                                <InputText
                                    v-model="ficha.celular"
                                    class="w-full"
                                />

                            </div>


                            <!-- TUTOR -->

                            <div class="campo">

                                <label>
                                    Numero de celular de su Tutor
                                    (Padre o Madre)
                                </label>

                                <InputText
                                    v-model="
                                        ficha.celular_tutor
                                    "
                                    class="w-full"
                                />

                            </div>


                            <!-- PARIENTE -->

                            <div class="campo">

                                <label>
                                    Numero de celular de pariente
                                    (Hermano o familiares)
                                </label>

                                <InputText
                                    v-model="
                                        ficha.celular_pariente
                                    "
                                    class="w-full"
                                />

                            </div>


                            <!-- FACEBOOK -->

                            <div class="campo">

                                <label>
                                    Facebook
                                </label>

                                <InputText
                                    v-model="ficha.facebook"
                                    class="w-full"
                                />

                            </div>


                            <!-- CORREO -->

                            <div class="campo">

                                <label>
                                    Correo
                                </label>

                                <InputText
                                    v-model="ficha.correo"
                                    type="email"
                                    class="w-full"
                                />

                            </div>


                            <!-- PROCEDENCIA -->

                            <div class="campo">

                                <label>
                                    Lugar de procedencia
                                </label>

                                <InputText
                                    v-model="
                                        ficha.lugar_procedencia
                                    "
                                    class="w-full"
                                />

                            </div>


                            <!-- DIRECCIÓN -->

                            <div class="campo campo-full">

                                <label>
                                    Dirección actual de residencia
                                </label>

                                <Textarea
                                    v-model="
                                        ficha.direccion_actual
                                    "
                                    rows="3"
                                    autoResize
                                    class="w-full"
                                />

                            </div>

                        </div>

                    </div>


                    <!-- =============================================
                         ACADÉMICOS
                    ============================================== -->

                    <div class="seccion-modal">

                        <div class="titulo-modal">

                            <i class="pi pi-book"></i>

                            ACADÉMICOS

                        </div>


                        <div
                            v-for="
                                pregunta in
                                preguntasAcademicas
                            "
                            :key="
                                pregunta.campo
                            "
                            class="pregunta-modal"
                        >

                            <strong>

                                {{ pregunta.texto }}

                            </strong>


                            <div class="radio-grid">

                                <label
                                    v-for="
                                        opcion in
                                        frecuencias
                                    "
                                    :key="
                                        `${pregunta.campo}-${opcion.value}`
                                    "
                                    class="radio-opcion"
                                >

                                    <RadioButton
                                        v-model="
                                            ficha[
                                                pregunta.campo
                                            ]
                                        "
                                        :inputId="
                                            `edit-${pregunta.campo}-${opcion.value}`
                                        "
                                        :name="
                                            pregunta.campo
                                        "
                                        :value="
                                            opcion.value
                                        "
                                    />

                                    <span>

                                        {{ opcion.label }}

                                    </span>

                                </label>

                            </div>

                        </div>

                    </div>


                    <!-- =============================================
                         PERSONALES
                    ============================================== -->

                    <div class="seccion-modal">

                        <div class="titulo-modal">

                            <i class="pi pi-user-edit"></i>

                            PERSONALES

                        </div>


                        <div
                            v-for="
                                pregunta in
                                preguntasPersonales
                            "
                            :key="
                                pregunta.campo
                            "
                            class="pregunta-modal"
                        >

                            <strong>

                                {{ pregunta.texto }}

                            </strong>


                            <div class="radio-grid">

                                <label
                                    v-for="
                                        opcion in
                                        (
                                            pregunta.tipo ===
                                            'frecuencia'
                                                ? frecuencias
                                                : opcionesSiNo
                                        )
                                    "
                                    :key="
                                        `${pregunta.campo}-${opcion.value}`
                                    "
                                    class="radio-opcion"
                                >

                                    <RadioButton
                                        v-model="
                                            ficha[
                                                pregunta.campo
                                            ]
                                        "
                                        :inputId="
                                            `edit-${pregunta.campo}-${opcion.value}`
                                        "
                                        :name="
                                            pregunta.campo
                                        "
                                        :value="
                                            opcion.value
                                        "
                                    />

                                    <span>

                                        {{ opcion.label }}

                                    </span>

                                </label>

                            </div>

                        </div>

                    </div>


                    <!-- =============================================
                         FAMILIARES
                    ============================================== -->

                    <div class="seccion-modal">

                        <div class="titulo-modal">

                            <i class="pi pi-users"></i>

                            FAMILIARES

                        </div>


                        <div
                            v-for="
                                pregunta in
                                preguntasFamiliares
                            "
                            :key="
                                pregunta.campo
                            "
                            class="pregunta-modal"
                        >

                            <strong>

                                {{ pregunta.texto }}

                            </strong>


                            <div class="radio-grid">

                                <label
                                    v-for="
                                        opcion in
                                        (
                                            pregunta.tipo ===
                                            'frecuencia'
                                                ? frecuencias
                                                : opcionesSiNo
                                        )
                                    "
                                    :key="
                                        `${pregunta.campo}-${opcion.value}`
                                    "
                                    class="radio-opcion"
                                >

                                    <RadioButton
                                        v-model="
                                            ficha[
                                                pregunta.campo
                                            ]
                                        "
                                        :inputId="
                                            `edit-${pregunta.campo}-${opcion.value}`
                                        "
                                        :name="
                                            pregunta.campo
                                        "
                                        :value="
                                            opcion.value
                                        "
                                    />

                                    <span>

                                        {{ opcion.label }}

                                    </span>

                                </label>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- =================================================
                     FOOTER MODAL
                ================================================== -->

                <template #footer>

                    <div class="footer-modal">


                        <div class="footer-modal-izq">

                            <Button
                                v-if="ficha.id"
                                label="Imprimir"
                                icon="pi pi-print"
                                severity="secondary"
                                outlined
                                @click="
                                    imprimirFicha(
                                        ficha.id
                                    )
                                "
                            />


                            <Button
                                v-if="ficha.id"
                                label="PDF"
                                icon="pi pi-file-pdf"
                                severity="danger"
                                outlined
                                @click="
                                    descargarPDF(
                                        ficha.id
                                    )
                                "
                            />

                        </div>


                        <div class="footer-modal-der">

                            <Button
                                label="Cerrar"
                                severity="secondary"
                                outlined
                                @click="
                                    mostrarModal = false
                                "
                            />


                            <Button
                                label="Guardar cambios"
                                icon="pi pi-save"
                                :loading="guardando"
                                :disabled="guardando"
                                @click="
                                    guardarEdicion
                                "
                            />

                        </div>

                    </div>

                </template>

            </Dialog>

        </div>

    </LayoutSupervisor>

</template>


<style scoped>

* {
    box-sizing: border-box;
}


/* =========================================================
   GENERAL
========================================================= */

.reporte {

    padding: 20px;

}


/* =========================================================
   CABECERA
========================================================= */

.cabecera-reporte {

    margin-bottom: 20px;

}


.cabecera-reporte h2 {

    margin: 0;

    font-size: 1.5rem;

    color: #263238;

}


.cabecera-reporte p {

    margin: 7px 0 0;

    color: #6b7280;

    font-size: .9rem;

}


/* =========================================================
   RESUMEN
========================================================= */

.resumen-grid {

    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 15px;

    margin-bottom: 20px;

}


.resumen-card {

    display: flex;

    align-items: center;

    gap: 14px;

    padding: 18px;

    background: #fff;

    border-radius: 10px;

    box-shadow:
        0 2px 8px
        rgba(0, 0, 0, .07);

}


.resumen-icono {

    width: 48px;

    height: 48px;

    min-width: 48px;

    border-radius: 10px;

    display: flex;

    justify-content: center;

    align-items: center;

    font-size: 1.3rem;

}


.resumen-icono.total {

    color: #1565c0;

    background: #e3f2fd;

}


.resumen-icono.tercera-icon {

    color: #2e7d32;

    background: #e8f5e9;

}


.resumen-icono.cuarta-icon {

    color: #e65100;

    background: #fff3e0;

}


.resumen-card span {

    display: block;

    color: #6b7280;

    font-size: .82rem;

}


.resumen-card strong {

    display: block;

    margin-top: 3px;

    font-size: 1.65rem;

    color: #263238;

}


/* =========================================================
   PANEL
========================================================= */

.panel {

    background: #fff;

    border-radius: 10px;

    padding: 18px;

    box-shadow:
        0 2px 8px
        rgba(0, 0, 0, .07);

    margin-bottom: 20px;

}


.tabla-panel {

    padding: 0;

    overflow: hidden;

}


.panel-titulo {

    display: flex;

    align-items: center;

    gap: 8px;

    font-weight: 600;

    color: #37474f;

    margin-bottom: 17px;

}


.tabla-panel .panel-titulo {

    padding: 17px 18px 0;

}


/* =========================================================
   FILTROS
========================================================= */

.filtros-grid {

    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 15px;

}


.buscador {

    grid-column: span 2;

}


.filtro label,
.campo label {

    display: block;

    margin-bottom: 6px;

    font-size: .82rem;

    font-weight: 600;

    color: #455a64;

}


.botones-filtro {

    display: flex;

    gap: 8px;

    margin-top: 17px;

    flex-wrap: wrap;

}


/* =========================================================
   TABLA
========================================================= */

.condicion {

    display: inline-block;

    padding: 5px 9px;

    border-radius: 5px;

    font-size: .75rem;

    font-weight: 600;

    white-space: nowrap;

}


.condicion.tercera {

    background: #e8f5e9;

    color: #2e7d32;

}


.condicion.cuarta {

    background: #fff3e0;

    color: #e65100;

}


.acciones-tabla {

    display: flex;

    align-items: center;

    gap: 5px;

    white-space: nowrap;

}


.sin-registros {

    padding: 25px;

    display: flex;

    flex-direction: column;

    align-items: center;

    gap: 8px;

    color: #78909c;

}


.sin-registros i {

    font-size: 1.8rem;

}


/* =========================================================
   PAGINACIÓN
========================================================= */

.paginacion {

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 15px;

    padding: 13px 16px;

    border-top: 1px solid #eceff1;

}


.paginacion-info {

    font-size: .82rem;

    color: #607d8b;

}


.paginacion-botones {

    display: flex;

    align-items: center;

    gap: 7px;

}


.pagina-actual {

    font-size: .8rem;

    color: #607d8b;

    padding: 0 6px;

}


/* =========================================================
   MODAL
========================================================= */

.cargando-modal {

    min-height: 180px;

    display: flex;

    align-items: center;

    justify-content: center;

    gap: 9px;

    color: #607d8b;

}


.cargando-modal i {

    font-size: 1.5rem;

}


.seccion-modal {

    margin-bottom: 24px;

    border: 1px solid #e7ecef;

    border-radius: 8px;

    overflow: hidden;

}


.titulo-modal {

    display: flex;

    align-items: center;

    gap: 8px;

    background: #f4f8fb;

    color: #1565c0;

    padding: 12px 15px;

    font-size: .95rem;

    font-weight: 700;

}


.modal-grid {

    display: grid;

    grid-template-columns:
        repeat(2, 1fr);

    gap: 15px;

    padding: 17px;

}


.campo-full {

    grid-column: 1 / -1;

}


/* =========================================================
   PREGUNTAS
========================================================= */

.pregunta-modal {

    padding: 14px 17px;

    border-bottom: 1px solid #eceff1;

}


.pregunta-modal:last-child {

    border-bottom: none;

}


.pregunta-modal strong {

    display: block;

    margin-bottom: 11px;

    font-size: .85rem;

    color: #37474f;

    line-height: 1.45;

}


.radio-grid {

    display: flex;

    flex-wrap: wrap;

    gap: 8px 17px;

}


.radio-opcion {

    display: flex;

    align-items: center;

    gap: 6px;

    cursor: pointer;

    font-size: .8rem;

    color: #546e7a;

}


/* =========================================================
   FOOTER MODAL
========================================================= */

.footer-modal {

    width: 100%;

    display: flex;

    justify-content: space-between;

    gap: 10px;

}


.footer-modal-izq,
.footer-modal-der {

    display: flex;

    gap: 7px;

}


/* =========================================================
   PRIMEVUE
========================================================= */

:deep(.p-inputtext),
:deep(.p-dropdown),
:deep(.p-inputtextarea) {

    width: 100%;

}


/* =========================================================
   TABLET
========================================================= */

@media (max-width: 950px) {

    .filtros-grid {

        grid-template-columns:
            repeat(2, 1fr);

    }


    .buscador {

        grid-column: span 2;

    }

}


/* =========================================================
   CELULAR
========================================================= */

@media (max-width: 650px) {

    .reporte {

        padding: 10px;

    }


    .cabecera-reporte h2 {

        font-size: 1.2rem;

    }


    .resumen-grid {

        grid-template-columns: 1fr;

        gap: 9px;

    }


    .resumen-card {

        padding: 13px;

    }


    .filtros-grid {

        grid-template-columns: 1fr;

    }


    .buscador {

        grid-column: auto;

    }


    .botones-filtro {

        flex-direction: column;

    }


    .botones-filtro :deep(.p-button) {

        width: 100%;

        justify-content: center;

    }


    .acciones-tabla {

        flex-wrap: wrap;

    }


    .paginacion {

        flex-direction: column;

        align-items: stretch;

        text-align: center;

    }


    .paginacion-botones {

        justify-content: center;

    }


    .modal-grid {

        grid-template-columns: 1fr;

        padding: 13px;

    }


    .campo-full {

        grid-column: auto;

    }


    .radio-grid {

        flex-direction: column;

        gap: 10px;

    }


    .radio-opcion {

        width: 100%;

        padding: 5px 0;

    }


    .footer-modal {

        flex-direction: column;

    }


    .footer-modal-izq,
    .footer-modal-der {

        flex-direction: column;

        width: 100%;

    }


    .footer-modal :deep(.p-button) {

        width: 100%;

        justify-content: center;

    }

}

</style>