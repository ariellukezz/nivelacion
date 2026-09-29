<template>
  <AuthenticatedLayout>
    <Head title="Reporte Servicio Psicopedagógico" />

    <div class="reporte-page">
      <section class="hero-report">
        <div>
          <span class="hero-kicker">SERVICIO PSICOPEDAGÓGICO</span>
          <h1>Reporte de atenciones</h1>
          <p>Consulta de sesiones, profesionales responsables, seguimiento y evidencias registradas.</p>
        </div>

        <Button
          label="Actualizar"
          icon="pi pi-refresh"
          severity="secondary"
          outlined
          :loading="loading"
          @click="cargar(1)"
        />
      </section>

      <!-- RESUMEN -->
      <section class="stats-grid">
        <article class="stat-card stat-blue">
          <div class="stat-icon"><i class="pi pi-users"></i></div>
          <div>
            <small>Estudiantes atendidos</small>
            <strong>{{ resumen.estudiantes }}</strong>
          </div>
        </article>

        <article class="stat-card stat-indigo">
          <div class="stat-icon"><i class="pi pi-list-check"></i></div>
          <div>
            <small>Atenciones</small>
            <strong>{{ resumen.atenciones }}</strong>
          </div>
        </article>

        <article class="stat-card stat-green">
          <div class="stat-icon"><i class="pi pi-building"></i></div>
          <div>
            <small>Presenciales</small>
            <strong>{{ resumen.presenciales }}</strong>
          </div>
        </article>

        <article class="stat-card stat-cyan">
          <div class="stat-icon"><i class="pi pi-desktop"></i></div>
          <div>
            <small>Virtuales</small>
            <strong>{{ resumen.virtuales }}</strong>
          </div>
        </article>

        <article class="stat-card stat-orange">
          <div class="stat-icon"><i class="pi pi-history"></i></div>
          <div>
            <small>Seguimientos</small>
            <strong>{{ resumen.seguimientos }}</strong>
          </div>
        </article>

        <article class="stat-card stat-red">
          <div class="stat-icon"><i class="pi pi-share-alt"></i></div>
          <div>
            <small>Derivaciones</small>
            <strong>{{ resumen.derivaciones }}</strong>
          </div>
        </article>

        <article class="stat-card stat-purple">
          <div class="stat-icon"><i class="pi pi-image"></i></div>
          <div>
            <small>Con evidencia</small>
            <strong>{{ resumen.con_evidencia }}</strong>
          </div>
        </article>
      </section>

      <!-- FILTROS -->
      <section class="card filtros-card">
        <div class="section-title">
          <div>
            <h2>Filtros de búsqueda</h2>
            <p>Puede combinar varios filtros.</p>
          </div>

          <div class="filter-actions">
            <Button
              label="Buscar"
              icon="pi pi-search"
              size="small"
              @click="cargar(1)"
            />
            <Button
              label="Limpiar"
              icon="pi pi-filter-slash"
              severity="secondary"
              outlined
              size="small"
              @click="limpiar"
            />
          </div>
        </div>

        <div class="grid">
          <div class="field col-12 md:col-3">
            <label>Semestre académico</label>
            <Dropdown
              v-model="f.semestre"
              :options="semestres"
              showClear
              class="w-full"
              placeholder="Todos"
            />
          </div>

          <div class="field col-12 md:col-3">
            <label>Profesional responsable</label>
            <Dropdown
              v-model="f.id_profesional"
              :options="profesionales"
              optionLabel="nombre"
              optionValue="id"
              filter
              showClear
              class="w-full"
              placeholder="Todos"
            />
          </div>

          <div class="field col-12 md:col-3">
            <label>Facultad</label>
            <Dropdown
              v-model="f.facultad"
              :options="facultades"
              filter
              showClear
              class="w-full"
              placeholder="Todas"
            />
          </div>

          <div class="field col-12 md:col-3">
            <label>Escuela Profesional</label>
            <Dropdown
              v-model="f.escuela"
              :options="escuelas"
              filter
              showClear
              class="w-full"
              placeholder="Todas"
            />
          </div>

          <div class="field col-12 md:col-3">
            <label>Buscar estudiante</label>
            <InputText
              v-model="f.buscar"
              class="w-full"
              placeholder="Código, DNI o nombre"
              @keyup.enter="cargar(1)"
            />
          </div>

          <div class="field col-6 md:col-2">
            <label>Sexo</label>
            <Dropdown
              v-model="f.sexo"
              :options="sexos"
              showClear
              class="w-full"
              placeholder="Todos"
            />
          </div>

          <div class="field col-6 md:col-2">
            <label>Ciclo</label>
            <InputText
              v-model="f.ciclo"
              class="w-full"
              placeholder="Ej. 3"
            />
          </div>

          <div class="field col-12 md:col-2">
            <label>Tipo atención</label>
            <Dropdown
              v-model="f.tipo_atencion"
              :options="tiposAtencion"
              optionLabel="label"
              optionValue="value"
              showClear
              class="w-full"
              placeholder="Todos"
            />
          </div>

          <div class="field col-12 md:col-3">
            <label>Seguimiento</label>
            <Dropdown
              v-model="f.seguimiento"
              :options="opcionesSiNo"
              optionLabel="label"
              optionValue="value"
              showClear
              class="w-full"
              placeholder="Todos"
            />
          </div>

          <div class="field col-12 md:col-4">
            <label>Condición académica</label>
            <Dropdown
              v-model="f.condicion"
              :options="condiciones"
              filter
              showClear
              class="w-full"
              placeholder="Todas"
            />
          </div>

          <div class="field col-12 md:col-4">
            <label>Presunción diagnóstica</label>
            <Dropdown
              v-model="f.diagnostico"
              :options="diagnosticos"
              filter
              showClear
              class="w-full"
              placeholder="Todas"
            />
          </div>

          <div class="field col-12 md:col-4">
            <label>Problema académico</label>
            <Dropdown
              v-model="f.problema_academico"
              :options="problemasAcademicos"
              filter
              showClear
              class="w-full"
              placeholder="Todos"
            />
          </div>
        </div>
      </section>

      <!-- TABLA -->
      <section class="card tabla-card">
        <div class="table-header">
          <div>
            <h2>Atenciones registradas</h2>
            <p>
              Mostrando {{ datos.from || 0 }} - {{ datos.to || 0 }}
              de {{ datos.total || 0 }} registros.
            </p>
          </div>

          <Dropdown
            v-model="perPage"
            :options="[10, 25, 50, 100]"
            class="rows-select"
            @change="cargar(1)"
          />
        </div>

        <DataTable
          :value="datos.data || []"
          :loading="loading"
          class="p-datatable-sm compact-table"
          responsiveLayout="scroll"
          tableStyle="min-width: 1280px"
        >
          <Column header="N.º" style="width: 58px">
            <template #body="{ index }">
              {{ (Number(datos.from || 1) - 1) + index + 1 }}
            </template>
          </Column>

          <Column field="fecha_atencion" header="Fecha">
            <template #body="{ data }">
              {{ fecha(data.fecha_atencion) }}
            </template>
          </Column>

          <Column field="profesional" header="Profesional" style="min-width: 180px" />

          <Column header="Estudiante" style="min-width: 260px">
            <template #body="{ data }">
              <div class="student-cell">
                <strong>{{ data.estudiante }}</strong>
                <small>
                  Cód. {{ data.codigo_estudiante }} · DNI {{ data.dni }}
                </small>
              </div>
            </template>
          </Column>

          <Column field="escuela_profesional" header="Escuela" style="min-width: 220px" />

          <Column field="ciclo" header="Ciclo" />

          <Column header="Sesión">
            <template #body="{ data }">
              <span class="session-chip">
                N.º {{ data.numero_sesion }}
              </span>
            </template>
          </Column>

          <Column header="Atención">
            <template #body="{ data }">
              <Tag
                :severity="data.tipo_atencion === 'VIRTUAL' ? 'info' : 'success'"
                :value="data.tipo_atencion === 'VIRTUAL' ? 'Virtual' : 'Presencial'"
              />
            </template>
          </Column>

          <Column header="Seguimiento">
            <template #body="{ data }">
              <Tag
                :severity="data.requiere_seguimiento ? 'warning' : 'secondary'"
                :value="data.requiere_seguimiento ? 'Sí' : 'No'"
              />
            </template>
          </Column>

          <Column header="Evidencia" style="width: 110px">
            <template #body="{ data }">
              <div v-if="data.tiene_evidencia" class="evidence-cell">
                <img
                  v-if="esImagen(data)"
                  :src="urlEvidencia(data.id)"
                  class="evidence-thumb"
                  alt="Evidencia"
                  @click="abrirEvidencia(data)"
                />
                <Button
                  v-else
                  icon="pi pi-file-pdf"
                  severity="danger"
                  text
                  rounded
                  title="Ver archivo"
                  @click="abrirEvidencia(data)"
                />
              </div>
              <span v-else class="sin-evidencia">-</span>
            </template>
          </Column>

          <Column header="Acciones" style="width: 110px">
            <template #body="{ data }">
              <Button
                icon="pi pi-eye"
                label="Ver"
                size="small"
                text
                @click="verFicha(data)"
              />
            </template>
          </Column>
        </DataTable>

        <div
          v-if="Number(datos.last_page || 1) > 1"
          class="pagination"
        >
          <Button
            icon="pi pi-angle-double-left"
            text
            :disabled="datos.current_page <= 1"
            @click="cargar(1)"
          />
          <Button
            icon="pi pi-angle-left"
            text
            :disabled="datos.current_page <= 1"
            @click="cargar(datos.current_page - 1)"
          />
          <span>
            Página {{ datos.current_page }} de {{ datos.last_page }}
          </span>
          <Button
            icon="pi pi-angle-right"
            text
            :disabled="datos.current_page >= datos.last_page"
            @click="cargar(datos.current_page + 1)"
          />
          <Button
            icon="pi pi-angle-double-right"
            text
            :disabled="datos.current_page >= datos.last_page"
            @click="cargar(datos.last_page)"
          />
        </div>
      </section>

      <!-- DETALLE -->
      <Dialog
        v-model:visible="detalleVisible"
        modal
        header="Detalle de atención psicopedagógica"
        :style="{ width: '920px', maxWidth: '96vw' }"
      >
        <div v-if="seleccionado" class="detail-wrap">
          <div class="detail-banner">
            <div>
              <span>Sesión N.º {{ seleccionado.numero_sesion }}</span>
              <h3>{{ seleccionado.estudiante }}</h3>
              <p>
                {{ seleccionado.codigo_estudiante }} · DNI {{ seleccionado.dni }}
              </p>
            </div>

            <div class="detail-date">
              {{ fecha(seleccionado.fecha_atencion) }}
            </div>
          </div>

          <div class="detail-grid">
            <div class="detail-item">
              <label>Profesional responsable</label>
              <strong>{{ seleccionado.profesional }}</strong>
            </div>
            <div class="detail-item">
              <label>Semestre académico</label>
              <strong>{{ seleccionado.semestre_academico }}</strong>
            </div>
            <div class="detail-item">
              <label>Facultad</label>
              <span>{{ seleccionado.facultad }}</span>
            </div>
            <div class="detail-item">
              <label>Escuela Profesional</label>
              <span>{{ seleccionado.escuela_profesional }}</span>
            </div>
            <div class="detail-item">
              <label>Edad / Sexo</label>
              <span>{{ seleccionado.edad }} años · {{ seleccionado.sexo }}</span>
            </div>
            <div class="detail-item">
              <label>Ciclo</label>
              <span>{{ seleccionado.ciclo }}</span>
            </div>
            <div class="detail-item">
              <label>Celular</label>
              <span>{{ seleccionado.celular || '-' }}</span>
            </div>
            <div class="detail-item">
              <label>Tipo de atención</label>
              <span>{{ seleccionado.tipo_atencion }}</span>
            </div>
          </div>

          <div class="detail-section">
            <h4>Condición académica</h4>
            <div v-if="seleccionado.condicion_academica?.length" class="chips">
              <span
                v-for="x in seleccionado.condicion_academica"
                :key="x"
                class="chip chip-indigo"
              >
                {{ x }}
              </span>
            </div>
            <span v-else>-</span>
          </div>

          <div class="detail-section">
            <h4>Discapacidad</h4>
            <p>{{ discapacidadTexto(seleccionado.discapacidad) }}</p>
          </div>

          <div class="detail-section">
            <h4>Presunción diagnóstica</h4>
            <div v-if="seleccionado.presuncion_diagnostica?.length" class="chips">
              <span
                v-for="x in seleccionado.presuncion_diagnostica"
                :key="x"
                class="chip chip-blue"
              >
                {{ x }}
              </span>
            </div>
            <p v-if="seleccionado.otro_diagnostico" class="mt-2">
              <strong>Otro:</strong> {{ seleccionado.otro_diagnostico }}
            </p>
          </div>

          <div class="detail-section">
            <h4>Problemas académicos</h4>
            <div v-if="seleccionado.problemas_academicos?.length" class="chips">
              <span
                v-for="x in seleccionado.problemas_academicos"
                :key="x"
                class="chip chip-orange"
              >
                {{ x }}
              </span>
            </div>
            <p v-if="seleccionado.otro_problema_academico" class="mt-2">
              <strong>Otro:</strong> {{ seleccionado.otro_problema_academico }}
            </p>
          </div>

          <div v-if="seleccionado.observaciones" class="detail-section">
            <h4>Observaciones</h4>
            <p class="preserve">{{ seleccionado.observaciones }}</p>
          </div>

          <div v-if="seleccionado.derivacion" class="detail-section">
            <h4>Derivación</h4>
            <p class="preserve">{{ seleccionado.derivacion }}</p>
          </div>

          <div class="detail-section">
            <h4>Seguimiento</h4>
            <p>
              <Tag
                :severity="seleccionado.requiere_seguimiento ? 'warning' : 'secondary'"
                :value="seleccionado.requiere_seguimiento ? 'Requiere seguimiento' : 'No requiere seguimiento'"
              />
            </p>
            <p v-if="seleccionado.seguimiento" class="preserve">
              {{ seleccionado.seguimiento }}
            </p>
          </div>

          <div v-if="seleccionado.satisfaccion" class="detail-section">
            <h4>Encuesta de satisfacción</h4>
            <p>{{ seleccionado.satisfaccion }}</p>
          </div>

          <div v-if="seleccionado.tiene_evidencia" class="detail-section">
            <div class="evidence-title">
              <h4>Evidencia</h4>
              <Button
                label="Descargar"
                icon="pi pi-download"
                size="small"
                outlined
                @click="descargarEvidencia(seleccionado.id)"
              />
            </div>

            <img
              v-if="esImagen(seleccionado)"
              :src="urlEvidencia(seleccionado.id)"
              class="detail-image"
              alt="Evidencia"
              @click="abrirEvidencia(seleccionado)"
            />

            <Button
              v-else
              label="Abrir documento PDF"
              icon="pi pi-file-pdf"
              severity="danger"
              outlined
              @click="abrirEvidencia(seleccionado)"
            />
          </div>
        </div>
      </Dialog>

      <!-- EVIDENCIA GRANDE -->
      <Dialog
        v-model:visible="evidenciaVisible"
        modal
        header="Evidencia de la atención"
        :style="{ width: '980px', maxWidth: '97vw' }"
      >
        <div v-if="evidenciaSeleccionada" class="evidence-modal">
          <img
            v-if="esImagen(evidenciaSeleccionada)"
            :src="urlEvidencia(evidenciaSeleccionada.id)"
            class="evidence-large"
            alt="Evidencia"
          />

          <iframe
            v-else
            :src="urlEvidencia(evidenciaSeleccionada.id)"
            class="pdf-frame"
            title="Evidencia PDF"
          ></iframe>
        </div>

        <template #footer>
          <Button
            label="Descargar"
            icon="pi pi-download"
            @click="descargarEvidencia(evidenciaSeleccionada?.id)"
          />
          <Button
            label="Cerrar"
            severity="secondary"
            outlined
            @click="evidenciaVisible = false"
          />
        </template>
      </Dialog>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/LayoutSupervisor.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted, reactive, ref } from 'vue';
import axios from 'axios';

import Button from 'primevue/button';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import Dialog from 'primevue/dialog';
import Dropdown from 'primevue/dropdown';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';

const props = defineProps({
  profesionales: { type: Array, default: () => [] },
  semestres: { type: Array, default: () => [] },
  facultades: { type: Array, default: () => [] },
  escuelas: { type: Array, default: () => [] },
  condiciones: { type: Array, default: () => [] },
  diagnosticos: { type: Array, default: () => [] },
  problemasAcademicos: { type: Array, default: () => [] },
});

const loading = ref(false);
const perPage = ref(25);

const resumen = reactive({
  estudiantes: 0,
  atenciones: 0,
  presenciales: 0,
  virtuales: 0,
  seguimientos: 0,
  derivaciones: 0,
  con_evidencia: 0,
});

const datos = ref({
  data: [],
  total: 0,
  from: 0,
  to: 0,
  current_page: 1,
  last_page: 1,
});

const f = reactive({
  semestre: null,
  id_profesional: null,
  facultad: null,
  escuela: null,
  buscar: '',
  sexo: null,
  ciclo: '',
  tipo_atencion: null,
  seguimiento: null,
  condicion: null,
  diagnostico: null,
  problema_academico: null,
});

const sexos = ['Masculino', 'Femenino', 'Otro'];

const tiposAtencion = [
  { label: 'Presencial', value: 'PRESENCIAL' },
  { label: 'Virtual', value: 'VIRTUAL' },
];

const opcionesSiNo = [
  { label: 'Sí', value: 'SI' },
  { label: 'No', value: 'NO' },
];

const seleccionado = ref(null);
const detalleVisible = ref(false);

const evidenciaSeleccionada = ref(null);
const evidenciaVisible = ref(false);

const cargar = async (page = 1) => {
  loading.value = true;

  try {
    const { data } = await axios.get(
      '/supervisor/servicio-psicopedagogico/data',
      {
        params: {
          ...f,
          page,
          per_page: perPage.value,
        }
      }
    );

    Object.assign(resumen, data.resumen || {});
    datos.value = data.datos || datos.value;
  } finally {
    loading.value = false;
  }
};

const limpiar = () => {
  Object.assign(f, {
    semestre: null,
    id_profesional: null,
    facultad: null,
    escuela: null,
    buscar: '',
    sexo: null,
    ciclo: '',
    tipo_atencion: null,
    seguimiento: null,
    condicion: null,
    diagnostico: null,
    problema_academico: null,
  });

  cargar(1);
};

const fecha = (valor) => {
  if (!valor) return '-';

  const partes = String(valor).slice(0, 10).split('-');

  if (partes.length !== 3) return valor;

  return `${partes[2]}/${partes[1]}/${partes[0]}`;
};

const discapacidadTexto = (valor) => {
  if (valor === 'SI_CONADIS') return 'Sí, con carnet CONADIS';
  if (valor === 'SI_SIN_CONADIS') return 'Sí, sin carnet CONADIS';
  return 'No';
};

const esImagen = (data) => {
  return ['jpg', 'jpeg', 'png', 'webp'].includes(
    String(data?.evidencia_extension || '').toLowerCase()
  );
};

const urlEvidencia = (id) =>
  `/supervisor/servicio-psicopedagogico/evidencia/${id}`;

const verFicha = (data) => {
  seleccionado.value = data;
  detalleVisible.value = true;
};

const abrirEvidencia = (data) => {
  evidenciaSeleccionada.value = data;
  evidenciaVisible.value = true;
};

const descargarEvidencia = (id) => {
  if (!id) return;

  window.open(
    `/supervisor/servicio-psicopedagogico/evidencia/${id}/descargar`,
    '_blank'
  );
};

onMounted(() => cargar(1));
</script>

<style scoped>
.reporte-page {
  min-height: 100vh;
  padding: 20px;
  background: #f5f7fb;
}

.hero-report {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 20px;
  padding: 24px 26px;
  margin-bottom: 17px;
  border-radius: 16px;
  color: white;
  background: linear-gradient(135deg, #244f83, #2e75b9 60%, #319795);
  box-shadow: 0 10px 25px rgba(36, 79, 131, .18);
}

.hero-kicker {
  font-size: .72rem;
  letter-spacing: .08em;
  opacity: .82;
}

.hero-report h1 {
  margin: 4px 0 5px;
  font-size: 1.55rem;
}

.hero-report p {
  margin: 0;
  opacity: .9;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(7, minmax(0, 1fr));
  gap: 10px;
  margin-bottom: 17px;
}

.stat-card {
  display: flex;
  align-items: center;
  gap: 10px;
  min-height: 82px;
  padding: 12px;
  border: 1px solid #e5eaf1;
  border-radius: 12px;
  background: white;
}

.stat-icon {
  display: grid;
  place-items: center;
  width: 36px;
  height: 36px;
  flex: 0 0 36px;
  border-radius: 10px;
}

.stat-card small {
  display: block;
  color: #718096;
  font-size: .72rem;
  line-height: 1.15;
}

.stat-card strong {
  display: block;
  margin-top: 3px;
  color: #25364a;
  font-size: 1.25rem;
}

.stat-blue .stat-icon { background:#ebf8ff; color:#3182ce; }
.stat-indigo .stat-icon { background:#ebf4ff; color:#4c51bf; }
.stat-green .stat-icon { background:#f0fff4; color:#2f855a; }
.stat-cyan .stat-icon { background:#e6fffa; color:#319795; }
.stat-orange .stat-icon { background:#fffaf0; color:#dd6b20; }
.stat-red .stat-icon { background:#fff5f5; color:#c53030; }
.stat-purple .stat-icon { background:#faf5ff; color:#805ad5; }

.card {
  margin-bottom: 17px;
  padding: 20px;
  border: 1px solid #e5eaf1;
  border-radius: 14px;
  background: white;
  box-shadow: 0 3px 12px rgba(30,55,80,.05);
}

.section-title,
.table-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 15px;
  margin-bottom: 17px;
}

.section-title h2,
.table-header h2 {
  margin: 0;
  color: #25364a;
  font-size: 1.05rem;
}

.section-title p,
.table-header p {
  margin: 4px 0 0;
  color: #8490a2;
  font-size: .8rem;
}

.filter-actions {
  display: flex;
  gap: 7px;
}

.field label {
  display: block;
  margin-bottom: 6px;
  color: #4a5568;
  font-size: .82rem;
  font-weight: 600;
}

.w-full {
  width: 100%;
}

.rows-select {
  width: 90px;
}

.student-cell strong {
  display: block;
  color: #2d3748;
  font-size: .84rem;
}

.student-cell small {
  display: block;
  margin-top: 3px;
  color: #8490a2;
  font-size: .73rem;
}

.session-chip {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 999px;
  color: #553c9a;
  background: #e9d8fd;
  font-size: .74rem;
  font-weight: 600;
}

.evidence-cell {
  display: flex;
  align-items: center;
  justify-content: center;
}

.evidence-thumb {
  width: 50px;
  height: 42px;
  border: 2px solid #edf2f7;
  border-radius: 7px;
  object-fit: cover;
  cursor: pointer;
  transition: transform .16s ease;
}

.evidence-thumb:hover {
  transform: scale(1.08);
}

.sin-evidencia {
  color: #a0aec0;
}

.pagination {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  gap: 5px;
  margin-top: 13px;
  color: #4a5568;
  font-size: .82rem;
}

.detail-wrap {
  color: #344054;
}

.detail-banner {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 15px;
  margin-bottom: 18px;
  padding: 17px;
  border-radius: 12px;
  color: white;
  background: linear-gradient(135deg, #2b6cb0, #319795);
}

.detail-banner span {
  font-size: .76rem;
  opacity: .88;
}

.detail-banner h3 {
  margin: 3px 0;
}

.detail-banner p {
  margin: 0;
  font-size: .84rem;
  opacity: .9;
}

.detail-date {
  font-weight: 700;
}

.detail-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0,1fr));
  gap: 10px;
}

.detail-item {
  padding: 11px;
  border: 1px solid #edf0f5;
  border-radius: 9px;
  background: #fafbfc;
}

.detail-item label {
  display: block;
  margin-bottom: 4px;
  color: #8490a2;
  font-size: .73rem;
}

.detail-item strong,
.detail-item span {
  font-size: .86rem;
}

.detail-section {
  margin-top: 14px;
  padding: 13px;
  border: 1px solid #edf0f5;
  border-radius: 9px;
}

.detail-section h4 {
  margin: 0 0 8px;
  color: #2d3748;
  font-size: .9rem;
}

.detail-section p {
  margin: 0;
  font-size: .84rem;
}

.chips {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

.chip {
  padding: 5px 8px;
  border-radius: 999px;
  font-size: .74rem;
}

.chip-indigo { color:#553c9a; background:#e9d8fd; }
.chip-blue { color:#2b6cb0; background:#ebf8ff; }
.chip-orange { color:#9c4221; background:#fffaf0; }

.preserve {
  white-space: pre-wrap;
}

.evidence-title {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.detail-image {
  display: block;
  max-width: 100%;
  max-height: 420px;
  margin: 10px auto 0;
  border-radius: 10px;
  cursor: zoom-in;
}

.evidence-modal {
  display: flex;
  justify-content: center;
  min-height: 300px;
}

.evidence-large {
  max-width: 100%;
  max-height: 72vh;
  object-fit: contain;
}

.pdf-frame {
  width: 100%;
  height: 72vh;
  border: 0;
}

@media (max-width: 1150px) {
  .stats-grid {
    grid-template-columns: repeat(4, minmax(0,1fr));
  }
}

@media (max-width: 760px) {
  .reporte-page {
    padding: 10px;
  }

  .hero-report,
  .section-title,
  .table-header {
    align-items: stretch;
    flex-direction: column;
  }

  .stats-grid {
    grid-template-columns: repeat(2, minmax(0,1fr));
  }

  .detail-grid {
    grid-template-columns: 1fr;
  }

  .filter-actions {
    width: 100%;
  }
}
</style>
