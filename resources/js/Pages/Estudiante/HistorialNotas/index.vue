<template>
<Head title="Historial de Notas"/>
<AuthenticatedLayout>

    <div class="encabezado px-5 py-4 mb-4">
        <h1 class="text-white font-bold text-lg m-0">Historial de Notas</h1>
        <p class="text-white text-sm m-0" style="opacity:0.8;">
            Registro académico completo por periodo
        </p>
    </div>

    <div class="px-3 pb-6">

        <div v-if="cargando" class="flex justify-center items-center py-16">
            <i class="pi pi-spin pi-spinner" style="font-size:1.8rem; color:var(--primary-color);"></i>
            <span class="ml-3 text-gray-500 text-sm">Cargando historial...</span>
        </div>

        <div v-else-if="notas.length === 0" class="text-center py-16">
            <i class="pi pi-inbox" style="font-size:2.5rem; color:#ccc;"></i>
            <p class="text-gray-400 mt-3">No se encontraron notas registradas.</p>
        </div>

        <div v-else>

            <!-- Resumen compacto: 3 columnas -->
            <div class="resumen-fila mb-4">
                <div class="resumen-item">
                    <span class="resumen-num">{{ periodosAgrupados.length }}</span>
                    <span class="resumen-txt">Periodos</span>
                </div>
                <div class="resumen-item">
                    <span class="resumen-num" style="color:#16a34a;">{{ totalAprobados }}</span>
                    <span class="resumen-txt">Aprobados</span>
                </div>
                <div class="resumen-item">
                    <span class="resumen-num" style="color:#dc2626;">{{ totalReprobados }}</span>
                    <span class="resumen-txt">Reprobados</span>
                </div>
            </div>

            <!-- Periodos -->
            <div v-for="periodo in periodosAgrupados" :key="periodo.id" class="periodo-bloque mb-5">

                <div class="periodo-header flex items-center justify-between px-4 py-2"
                     :class="periodo.estado === 'activo' ? 'header-activo' : 'header-inactivo'">
                    <div class="flex items-center gap-2">
                        <i class="pi pi-calendar" style="font-size:.9rem;"></i>
                        <span class="font-semibold text-sm">Periodo {{ periodo.nombre }}</span>
                        <span v-if="periodo.estado === 'activo'" class="badge-actual">EN CURSO</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="badge-mini badge-ap">✓ {{ periodo.cursos.filter(c => esAprobado(c.nota)).length }}</span>
                        <span class="badge-mini badge-re">✗ {{ periodo.cursos.filter(c => !esAprobado(c.nota)).length }}</span>
                        <span class="text-xs" style="opacity:.6;">{{ periodo.cursos.length }} cursos</span>
                    </div>
                </div>

                <!-- Vista móvil -->
                <div class="mobile-cards">
                    <div
                        v-for="(curso, idx) in periodo.cursos"
                        :key="idx"
                        class="curso-card"
                        :class="{ 'card-reprobado': !esAprobado(curso.nota) }"
                    >
                        <div class="card-top">
                            <span class="cod-tag">{{ curso.cod }}</span>
                            <span class="nota-pill" :class="notaClase(curso.nota)">
                                {{ curso.nota ?? '--' }}
                            </span>
                        </div>
                        <div class="card-nombre">{{ curso.curso_nombre }}</div>
                        <div class="card-docente">
                            <i class="pi pi-user" style="font-size:.75rem;"></i>
                            <span v-if="curso.nombres">{{ curso.paterno }} {{ curso.materno }}, {{ curso.nombres }}</span>
                            <span v-else style="color:#d97706; font-weight:600;">SIN ASIGNACIÓN</span>
                        </div>
                        <div class="card-estado">
                            <span class="estado-pill" :class="esAprobado(curso.nota) ? 'estado-aprobado' : 'estado-reprobado'">
                                {{ estadoTexto(curso.nota) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Vista desktop -->
                <div class="desktop-tabla">
                    <table class="tabla-formal">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>COMP.</th>
                                <th>CURSO</th>
                                <th>DOCENTE</th>
                                <th>GRUPO</th>
                                <th>NOTA</th>
                                <th>ESTADO</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(curso, idx) in periodo.cursos"
                                :key="idx"
                                :class="{ 'tr-reprobado': !esAprobado(curso.nota) }"
                            >
                                <td class="text-center text-gray-400" style="width:40px;">{{ idx + 1 }}</td>
                                <td class="text-center" style="width:80px;">
                                    <span class="cod-tag">{{ curso.cod }}</span>
                                </td>
                                <td>
                                    <div style="font-size:.88rem; font-weight:500;">{{ curso.curso_nombre }}</div>
                                </td>
                                <td style="width:260px;">
                                    <div v-if="curso.nombres" style="font-size:.85rem;">
                                        {{ curso.paterno }} {{ curso.materno }}, {{ curso.nombres }}
                                    </div>
                                    <div v-else style="color:#d97706; font-weight:600; font-size:.82rem;">
                                        <i class="pi pi-exclamation-triangle" style="font-size:.75rem;"></i>
                                        SIN ASIGNACIÓN
                                    </div>
                                </td>
                                <td class="text-center" style="width:70px; font-size:.88rem;">{{ curso.grupo }}</td>
                                <td class="text-center" style="width:80px;">
                                    <span class="nota-pill" :class="notaClase(curso.nota)">
                                        {{ curso.nota ?? '--' }}
                                    </span>
                                </td>
                                <td class="text-center" style="width:110px;">
                                    <span class="estado-pill" :class="esAprobado(curso.nota) ? 'estado-aprobado' : 'estado-reprobado'">
                                        {{ estadoTexto(curso.nota) }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

</AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/LayoutEstudiante.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const notas    = ref([])
const cargando = ref(true)

// ✅ Función central para determinar si aprobado
// Cubre: null, '', undefined, "0.00", 0, valores < 10.5
const esAprobado = (nota) => {
    if (nota === null || nota === undefined || String(nota).trim() === '') return false
    const n = parseFloat(String(nota).replace(',', '.'))
    if (isNaN(n)) return false
    return n >= 10.5
}

const estadoTexto = (nota) => {
    if (nota === null || nota === undefined || String(nota).trim() === '') return 'Sin nota'
    return esAprobado(nota) ? 'Aprobado' : 'Reprobado'
}

const notaClase = (nota) => {
    if (nota === null || nota === undefined || String(nota).trim() === '') return 'nota-sin'
    const n = parseFloat(nota)
    if (isNaN(n)) return 'nota-sin'
    if (n >= 15)   return 'nota-alta'
    if (n >= 10.5) return 'nota-media'
    return 'nota-baja'
}

const periodosAgrupados = computed(() => {
    const agrupados = {};
    notas.value.forEach(n => {
        if (!agrupados[n.periodo_nombre]) {
            agrupados[n.periodo_nombre] = {
                nombre: n.periodo_nombre,
                estado: n.periodo_estado,
                id:     n.id_periodo,
                cursos: []
            };
        }
        agrupados[n.periodo_nombre].cursos.push(n);
    });
    return Object.values(agrupados).sort((a, b) => b.id - a.id);
});

const totalAprobados  = computed(() => notas.value.filter(n => esAprobado(n.nota)).length)
const totalReprobados = computed(() => notas.value.filter(n => !esAprobado(n.nota)).length)

const getHistorialNotas = async () => {
    try {
        cargando.value = true;
        const res = await axios.get('/estudiante/get-historial-notas');
        console.log('datos recibidos:', res.data.datos);
        notas.value = Array.isArray(res.data?.datos) ? res.data.datos : [];
    } catch (e) {
        notas.value = [];
    } finally {
        cargando.value = false;
    }
}


getHistorialNotas();
</script>

<style scoped>
.encabezado { background: var(--primary-color); }

/* Resumen — 3 columnas */
.resumen-fila {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
}
.resumen-item {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 12px 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2px;
}
.resumen-num { font-size: 1.5rem; font-weight: 700; }
.resumen-txt { font-size: .72rem; color: #9ca3af; text-transform: uppercase; letter-spacing:.05em; }

/* Bloque periodo */
.periodo-bloque {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
    background: white;
}
.periodo-header   { border-bottom: 1px solid #e5e7eb; }
.header-activo    { background: #f0fdf4; color: #166534; }
.header-inactivo  { background: #f8fafc; color: #475569; }
.badge-actual {
    background: #166534; color: white;
    font-size: .65rem; padding: 1px 7px;
    border-radius: 99px; font-weight: 600;
}
.badge-mini {
    font-size: .72rem; font-weight: 600;
    padding: 1px 8px; border-radius: 99px;
}
.badge-ap { background: #dcfce7; color: #15803d; }
.badge-re { background: #fee2e2; color: #dc2626; }

/* Pills */
.cod-tag {
    background: #eff6ff; color: #1d4ed8;
    font-size: .75rem; font-weight: 600;
    padding: 2px 8px; border-radius: 4px;
}
.nota-pill {
    font-size: .82rem; font-weight: 700;
    padding: 2px 10px; border-radius: 99px;
}
.nota-alta   { background: #f0fdf4; color: #15803d; }
.nota-media  { background: #eff6ff; color: #1d4ed8; }
.nota-baja   { background: #fef2f2; color: #dc2626; }
.nota-sin    { background: #f3f4f6; color: #9ca3af; }

.estado-pill {
    font-size: .75rem; font-weight: 600;
    padding: 2px 10px; border-radius: 99px;
}
.estado-aprobado  { background: #f0fdf4; color: #15803d; }
.estado-reprobado { background: #fef2f2; color: #dc2626; }
.estado-sin       { background: #f3f4f6; color: #9ca3af; }

/* Tabla desktop */
.desktop-tabla { display: block; }
.mobile-cards  { display: none;  }

.tabla-formal { width: 100%; border-collapse: collapse; font-size: .88rem; }
.tabla-formal th {
    background: #f8fafc; color: #64748b;
    font-size: .75rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: .05em;
    padding: 9px 12px; border-bottom: 1px solid #e5e7eb;
    text-align: left;
}
.tabla-formal td {
    padding: 9px 12px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}
.tabla-formal tr:last-child td { border-bottom: none; }
.tr-reprobado { background: #fff8f8; }

/* Responsivo */
@media (max-width: 768px) {
    .desktop-tabla { display: none; }
    .mobile-cards  { display: block; padding: 10px; }
    .resumen-fila  { grid-template-columns: repeat(3, 1fr); }

    .curso-card {
        border: 1px solid #e5e7eb; border-radius: 8px;
        padding: 12px; margin-bottom: 10px; background: #fafafa;
    }
    .card-reprobado { background: #fff8f8; border-color: #fecaca; }
    .card-top {
        display: flex; justify-content: space-between;
        align-items: center; margin-bottom: 6px;
    }
    .card-nombre  { font-weight: 600; font-size: .88rem; margin-bottom: 4px; }
    .card-docente {
        font-size: .78rem; color: #64748b;
        display: flex; align-items: center;
        gap: 5px; margin-bottom: 6px;
    }
    .card-estado { display: flex; }
}
</style>
