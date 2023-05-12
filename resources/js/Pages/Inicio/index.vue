<template>
    <Head title="Inicio"/>
    <AuthenticatedLayout>
            
        <DataTable 
            :showGridlines= "false"
            :value="competencias" 
            :class="'p-datatable-sm'"  
            tableStyle="min-width: 50rem" 
            style="font-size: .9rem;"
            >
            <Column field="competencia" header="COMPETENCIA"></Column>
            <Column field="perfil" header="PERFIL DEL INGRESANTE UNA-PUNO"></Column>
            <Column field="curso" header="CURSO"></Column>
        </DataTable> 
        
    </AuthenticatedLayout>
    </template>
    <script setup>
    import AuthenticatedLayout from '@/Layouts/LayoutEstudiante.vue';
    import { Head } from '@inertiajs/vue3';
    import DataTable from 'primevue/datatable';
    import Column from 'primevue/column';
    import { ref } from 'vue'
    
    
    const competencias = ref([
        {
            competencia: "C1",
            perfil:"Se reconoce como persona valiosa y se identifica con su cultura en diferentes contextos.",
            curso:"Educación cívica"
        },
        {
            competencia: "C2",
            perfil:"Propicia la vida en democracia a partir del reconocimiento de sus derechos y deberes y de la comprensión de los procesos históricos y sociales de nuestro país y del mundo.",
            curso:"Historia y Geografía"
        },
        {
            competencia: "C3",
            perfil:"Practica una vida activa y saludable para su bienestar, cuida su cuerpo e interactúa respetuosamente en la práctica de distintas actividades físicas, cotidianas o deportivas.",
            curso:"Educación física"
        },
        {
            competencia: "C4",
            perfil:"Aprecia manifestaciones artístico-culturales para comprender el aporte del arte a la cultura y a la sociedad, y crea proyectos artísticos utilizando los diversos lenguajes del arte para comunicar sus ideas a otros.",
            curso:"Arte y cultura"
        },
        {
            competencia: "C5",
            perfil:"Comunica en su lengua materna, en castellano como segunda lengua y en inglés como lengua extranjera de manera asertiva y responsable para interactuar con otras personas en diversos contextos y con distintos propósitos.",
            curso:"Lenguaje, Literatura, Razonamiento Verbal, Ingles, quechua y aymara"
        },
        {
            competencia: "C6",
            perfil:"Indaga y comprende el mundo natural y artificial utilizando conocimientos científicos en diálogo con saberes locales la calidad de vida y cuidando la naturaleza.",
            curso:"Física, Química y Biología"
        },
        {
            competencia: "C7",
            perfil:"El estudiante interpreta la realidad y toma decisiones a partir de conocimientos matemáticos que aporten a su contexto.",
            curso:"Matemática"
        },
        {
            competencia: "C8",
            perfil:"Gestiona proyectos de emprendimiento económico o social de manera ética, que le permiten articularse con el mundo del trabajo y con el desarrollo social, económico y ambiental del entorno.",
            curso:"Educación para el trabajo, economía"
        },        
        {
            competencia: "C9",
            perfil:"Aprovecha responsablemente las tecnologías de la información y de la comunicación (TIC) para interactuar con la información, gestionar su comunicación y aprendizaje.",
            curso:"TIC"
        },
                
        {
            competencia: "C10",
            perfil:"Desarrolla procesos autónomos de aprendizaje en forma permanente para la mejora continua de su proceso de aprendizaje y de sus resultados.",
            curso:"Estrategia de aprendizaje (Enfoque transversal)"
        },        
        {
            competencia: "C11",
            perfil:"Comprende y aprecia la dimensión espiritual y religiosa en la vida de las personas y de las sociedades.",
            curso:"Religión"
        },
    
    ])
    
    </script>