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

        <div class="hero-actions">
          <Button
            label="Exportar Excel (todos)"
            icon="pi pi-file-excel"
            severity="success"
            :loading="exportando"
            @click="exportarExcelTodos"
          />

          <Button
            label="Actualizar"
            icon="pi pi-refresh"
            severity="secondary"
            outlined
            :loading="loading"
            @click="cargar(1)"
          />
        </div>
      </section>

      <section class="stats-grid">
        <article class="stat-card stat-blue">
          <div class="stat-icon"><i class="pi pi-users"></i></div>
          <div><small>Estudiantes atendidos</small><strong>{{ resumen.estudiantes }}</strong></div>
        </article>

        <article class="stat-card stat-indigo">
          <div class="stat-icon"><i class="pi pi-list-check"></i></div>
          <div><small>Atenciones</small><strong>{{ resumen.atenciones }}</strong></div>
        </article>

        <article class="stat-card stat-green">
          <div class="stat-icon"><i class="pi pi-building"></i></div>
          <div><small>Presenciales</small><strong>{{ resumen.presenciales }}</strong></div>
        </article>

        <article class="stat-card stat-cyan">
          <div class="stat-icon"><i class="pi pi-desktop"></i></div>
          <div><small>Virtuales</small><strong>{{ resumen.virtuales }}</strong></div>
        </article>

        <article class="stat-card stat-orange">
          <div class="stat-icon"><i class="pi pi-history"></i></div>
          <div><small>Seguimientos</small><strong>{{ resumen.seguimientos }}</strong></div>
        </article>

        <article class="stat-card stat-red">
          <div class="stat-icon"><i class="pi pi-share-alt"></i></div>
          <div><small>Derivaciones</small><strong>{{ resumen.derivaciones }}</strong></div>
        </article>

        <article class="stat-card stat-purple">
          <div class="stat-icon"><i class="pi pi-image"></i></div>
          <div><small>Con evidencia</small><strong>{{ resumen.con_evidencia }}</strong></div>
        </article>
      </section>

      <section class="card filtros-card">
        <div class="section-title">
          <div>
            <h2>Filtros de búsqueda</h2>
            <p>Puede combinar varios filtros.</p>
          </div>

          <div class="filter-actions">
            <Button label="Buscar" icon="pi pi-search" size="small" @click="cargar(1)" />
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
            <Dropdown v-model="f.semestre" :options="semestres" showClear class="w-full" placeholder="Todos" />
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
            <Dropdown v-model="f.facultad" :options="facultades" filter showClear class="w-full" placeholder="Todas" />
          </div>

          <div class="field col-12 md:col-3">
            <label>Escuela Profesional</label>
            <Dropdown v-model="f.escuela" :options="escuelas" filter showClear class="w-full" placeholder="Todas" />
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
            <Dropdown v-model="f.sexo" :options="sexos" showClear class="w-full" placeholder="Todos" />
          </div>

          <div class="field col-6 md:col-2">
            <label>Ciclo</label>
            <InputText v-model="f.ciclo" class="w-full" placeholder="Ej. 3" />
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
            <Dropdown v-model="f.condicion" :options="condiciones" filter showClear class="w-full" placeholder="Todas" />
          </div>

          <div class="field col-12 md:col-4">
            <label>Presunción diagnóstica</label>
            <Dropdown v-model="f.diagnostico" :options="diagnosticos" filter showClear class="w-full" placeholder="Todas" />
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

      <section class="card tabla-card">
        <div class="table-header">
          <div>
            <h2>Atenciones registradas</h2>
            <p>Mostrando {{ datos.from || 0 }} - {{ datos.to || 0 }} de {{ datos.total || 0 }} registros.</p>
          </div>

          <Dropdown v-model="perPage" :options="[10, 25, 50, 100]" class="rows-select" @change="cargar(1)" />
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
            <template #body="{ data }">{{ fecha(data.fecha_atencion) }}</template>
          </Column>

          <Column field="profesional" header="Profesional" style="min-width: 180px" />

          <Column header="Estudiante" style="min-width: 260px">
            <template #body="{ data }">
              <div class="student-cell">
                <strong>{{ data.estudiante }}</strong>
                <small>Cód. {{ data.codigo_estudiante }} · DNI {{ data.dni }}</small>
              </div>
            </template>
          </Column>

          <Column field="escuela_profesional" header="Escuela" style="min-width: 220px" />
          <Column field="ciclo" header="Ciclo" />

          <Column header="Sesión">
            <template #body="{ data }">
              <span class="session-chip">N.º {{ data.numero_sesion }}</span>
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

          <Column header="Acciones" style="width: 190px">
            <template #body="{ data }">
              <div class="action-buttons">
                <Button
                  icon="pi pi-eye"
                  severity="info"
                  text
                  rounded
                  title="Ver ficha"
                  @click="verFicha(data)"
                />

                <Button
                  icon="pi pi-pencil"
                  severity="warning"
                  text
                  rounded
                  title="Editar atención"
                  @click="abrirEditar(data)"
                />

                <Button
                  icon="pi pi-print"
                  severity="secondary"
                  text
                  rounded
                  title="Imprimir ficha"
                  @click="imprimirFicha(data)"
                />

                <Button
                  icon="pi pi-trash"
                  severity="danger"
                  text
                  rounded
                  title="Eliminar atención"
                  :loading="eliminandoId === data.id"
                  @click="eliminarRegistro(data)"
                />
              </div>
            </template>
          </Column>
        </DataTable>

        <div v-if="Number(datos.last_page || 1) > 1" class="pagination">
          <Button icon="pi pi-angle-double-left" text :disabled="datos.current_page <= 1" @click="cargar(1)" />
          <Button icon="pi pi-angle-left" text :disabled="datos.current_page <= 1" @click="cargar(datos.current_page - 1)" />
          <span>Página {{ datos.current_page }} de {{ datos.last_page }}</span>
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
              <p>{{ seleccionado.codigo_estudiante }} · DNI {{ seleccionado.dni }}</p>
            </div>
            <div class="detail-date">{{ fecha(seleccionado.fecha_atencion) }}</div>
          </div>

          <div class="detail-grid">
            <div class="detail-item"><label>Profesional responsable</label><strong>{{ seleccionado.profesional }}</strong></div>
            <div class="detail-item"><label>Semestre académico</label><strong>{{ seleccionado.semestre_academico }}</strong></div>
            <div class="detail-item"><label>Facultad</label><span>{{ seleccionado.facultad }}</span></div>
            <div class="detail-item"><label>Escuela Profesional</label><span>{{ seleccionado.escuela_profesional }}</span></div>
            <div class="detail-item"><label>Edad / Sexo</label><span>{{ seleccionado.edad }} años · {{ seleccionado.sexo }}</span></div>
            <div class="detail-item"><label>Ciclo</label><span>{{ seleccionado.ciclo }}</span></div>
            <div class="detail-item"><label>Celular</label><span>{{ seleccionado.celular || '-' }}</span></div>
            <div class="detail-item"><label>Tipo de atención</label><span>{{ seleccionado.tipo_atencion }}</span></div>
          </div>

          <div class="detail-section">
            <h4>Condición académica</h4>
            <div v-if="seleccionado.condicion_academica?.length" class="chips">
              <span v-for="x in seleccionado.condicion_academica" :key="x" class="chip chip-indigo">{{ x }}</span>
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
              <span v-for="x in seleccionado.presuncion_diagnostica" :key="x" class="chip chip-blue">{{ x }}</span>
            </div>
            <p v-if="seleccionado.otro_diagnostico" class="mt-2"><strong>Otro:</strong> {{ seleccionado.otro_diagnostico }}</p>
          </div>

          <div class="detail-section">
            <h4>Problemas académicos</h4>
            <div v-if="seleccionado.problemas_academicos?.length" class="chips">
              <span v-for="x in seleccionado.problemas_academicos" :key="x" class="chip chip-orange">{{ x }}</span>
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
            <p v-if="seleccionado.seguimiento" class="preserve">{{ seleccionado.seguimiento }}</p>
          </div>

          <div v-if="seleccionado.satisfaccion" class="detail-section">
            <h4>Encuesta de satisfacción</h4>
            <p>{{ seleccionado.satisfaccion }}</p>
          </div>

          <div v-if="seleccionado.tiene_evidencia" class="detail-section">
            <div class="evidence-title">
              <h4>Evidencia</h4>
              <Button label="Descargar" icon="pi pi-download" size="small" outlined @click="descargarEvidencia(seleccionado.id)" />
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

      <!-- EDITAR ATENCIÓN -->
      <Dialog
        v-model:visible="editarVisible"
        modal
        header="Editar atención psicopedagógica"
        :style="{ width: '960px', maxWidth: '97vw' }"
      >
        <div v-if="editandoRegistro" class="edit-wrap">
          <div class="edit-student-banner">
            <div>
              <span>Sesión N.º {{ editandoRegistro.numero_sesion }}</span>
              <h3>{{ editandoRegistro.estudiante }}</h3>
              <p>
                Código {{ editandoRegistro.codigo_estudiante }}
                · DNI {{ editandoRegistro.dni }}
              </p>
            </div>

            <div class="edit-readonly-note">
              <i class="pi pi-lock"></i>
              Datos del estudiante no editables aquí
            </div>
          </div>

          <div class="grid">
            <div class="field col-12 md:col-4">
              <label>Profesional responsable *</label>
              <Dropdown
                v-model="editForm.id_profesional"
                :options="profesionales"
                optionLabel="nombre"
                optionValue="id"
                filter
                class="w-full"
              />
            </div>

            <div class="field col-12 md:col-4">
              <label>Fecha de atención *</label>
              <InputText
                v-model="editForm.fecha_atencion"
                type="date"
                class="w-full"
              />
            </div>

            <div class="field col-12 md:col-4">
              <label>Semestre académico *</label>
              <InputText
                v-model="editForm.semestre_academico"
                class="w-full"
              />
            </div>

            <div class="field col-12 md:col-4">
              <label>Facultad</label>
              <InputText
                :modelValue="editandoRegistro.facultad"
                class="w-full edit-readonly"
                readonly
              />
            </div>

            <div class="field col-12 md:col-4">
              <label>Escuela Profesional</label>
              <InputText
                :modelValue="editandoRegistro.escuela_profesional"
                class="w-full edit-readonly"
                readonly
              />
            </div>

            <div class="field col-12 md:col-2">
              <label>Ciclo</label>
              <InputText
                :modelValue="editandoRegistro.ciclo"
                class="w-full edit-readonly"
                readonly
              />
            </div>

            <div class="field col-12 md:col-2">
              <label>Celular</label>
              <InputText
                v-model="editForm.celular"
                class="w-full"
              />
            </div>
          </div>

          <div class="edit-section">
            <h4>Condición académica</h4>

            <div class="edit-options">
              <label
                v-for="opcion in condiciones"
                :key="`edit-cond-${opcion}`"
                class="edit-option"
              >
                <Checkbox
                  v-model="editForm.condicion_academica"
                  :value="opcion"
                />
                <span>{{ opcion }}</span>
              </label>
            </div>
          </div>

          <div class="edit-section">
            <h4>Discapacidad</h4>

            <Dropdown
              v-model="editForm.discapacidad"
              :options="opcionesDiscapacidad"
              optionLabel="label"
              optionValue="value"
              class="w-full"
            />
          </div>

          <div class="edit-section">
            <h4>Presunción diagnóstica</h4>

            <div class="edit-options">
              <label
                v-for="opcion in diagnosticos"
                :key="`edit-diag-${opcion}`"
                class="edit-option"
              >
                <Checkbox
                  v-model="editForm.presuncion_diagnostica"
                  :value="opcion"
                />
                <span>{{ opcion }}</span>
              </label>
            </div>

            <div
              v-if="
                editForm.presuncion_diagnostica.includes(
                  'Otros problemas psicológicos'
                )
              "
              class="mt-3"
            >
              <label class="edit-field-label">
                Especifique otro problema psicológico
              </label>

              <Textarea
                v-model="editForm.otro_diagnostico"
                rows="2"
                class="w-full"
              />
            </div>
          </div>

          <div class="edit-section">
            <h4>Problemas académicos</h4>

            <div class="edit-options">
              <label
                v-for="opcion in problemasAcademicos"
                :key="`edit-prob-${opcion}`"
                class="edit-option"
              >
                <Checkbox
                  v-model="editForm.problemas_academicos"
                  :value="opcion"
                />
                <span>{{ opcion }}</span>
              </label>
            </div>

            <div
              v-if="
                editForm.problemas_academicos.includes(
                  'Otros problemas académicos'
                )
              "
              class="mt-3"
            >
              <label class="edit-field-label">
                Especifique otro problema académico
              </label>

              <Textarea
                v-model="editForm.otro_problema_academico"
                rows="2"
                class="w-full"
              />
            </div>
          </div>

          <div class="grid">
            <div class="field col-12 md:col-6">
              <label>Tipo de atención *</label>

              <Dropdown
                v-model="editForm.tipo_atencion"
                :options="tiposAtencion"
                optionLabel="label"
                optionValue="value"
                class="w-full"
              />
            </div>

            <div class="field col-12 md:col-6">
              <label>Encuesta de satisfacción</label>

              <Dropdown
                v-model="editForm.satisfaccion"
                :options="satisfacciones"
                showClear
                class="w-full"
                placeholder="Sin respuesta"
              />
            </div>

            <div class="field col-12">
              <label>Observaciones</label>

              <Textarea
                v-model="editForm.observaciones"
                rows="4"
                class="w-full"
              />
            </div>

            <div class="field col-12 md:col-6">
              <label>Derivación</label>

              <Textarea
                v-model="editForm.derivacion"
                rows="3"
                class="w-full"
              />
            </div>

            <div class="field col-12 md:col-6">
              <label>Seguimiento</label>

              <label class="edit-option mb-2">
                <Checkbox
                  v-model="editForm.requiere_seguimiento"
                  :binary="true"
                />
                <span>Requiere seguimiento</span>
              </label>

              <Textarea
                v-model="editForm.seguimiento"
                rows="3"
                class="w-full"
              />
            </div>
          </div>

          <div class="edit-section">
            <h4>Evidencia</h4>

            <div
              v-if="editandoRegistro.tiene_evidencia"
              class="current-evidence"
            >
              <div>
                <i class="pi pi-paperclip"></i>
                <span>
                  {{
                    editandoRegistro.evidencia_nombre ||
                    'El registro tiene una evidencia adjunta'
                  }}
                </span>
              </div>

              <Button
                label="Ver"
                icon="pi pi-eye"
                size="small"
                text
                @click="abrirEvidencia(editandoRegistro)"
              />
            </div>

            <div class="grid mt-2">
              <div class="field col-12 md:col-7">
                <label>Reemplazar evidencia</label>

                <input
                  type="file"
                  accept=".jpg,.jpeg,.png,.pdf"
                  class="edit-file"
                  @change="seleccionarEvidenciaEdicion"
                />

                <small class="edit-help">
                  JPG, PNG o PDF. Máximo 8 MB.
                </small>
              </div>

              <div
                v-if="editandoRegistro.tiene_evidencia"
                class="field col-12 md:col-5"
              >
                <label>Archivo actual</label>

                <label class="edit-option remove-file">
                  <Checkbox
                    v-model="editForm.eliminar_evidencia"
                    :binary="true"
                  />
                  <span>Eliminar evidencia actual</span>
                </label>
              </div>
            </div>
          </div>
        </div>

        <template #footer>
          <Button
            label="Cancelar"
            severity="secondary"
            outlined
            @click="editarVisible = false"
          />

          <Button
            label="Guardar cambios"
            icon="pi pi-save"
            :loading="guardandoEdicion"
            @click="guardarEdicion"
          />
        </template>
      </Dialog>

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
          <Button label="Descargar" icon="pi pi-download" @click="descargarEvidencia(evidenciaSeleccionada?.id)" />
          <Button label="Cerrar" severity="secondary" outlined @click="evidenciaVisible = false" />
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
import * as XLSX from 'xlsx';

import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import Dialog from 'primevue/dialog';
import Dropdown from 'primevue/dropdown';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';
import Textarea from 'primevue/textarea';

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
const exportando = ref(false);
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

const editarVisible = ref(false);
const guardandoEdicion = ref(false);
const eliminandoId = ref(null);
const editandoRegistro = ref(null);

const opcionesDiscapacidad = [
  { label: 'No', value: 'NO' },
  { label: 'Sí, con carnet CONADIS', value: 'SI_CONADIS' },
  { label: 'Sí, sin carnet CONADIS', value: 'SI_SIN_CONADIS' },
];

const satisfacciones = [
  'Muy satisfecho',
  'Satisfecho',
  'Regular',
  'Insatisfecho',
];

const editForm = reactive({
  id: null,
  id_profesional: null,
  fecha_atencion: '',
  semestre_academico: '',
  celular: '',

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
  requiere_seguimiento: false,
  seguimiento: '',

  evidencia: null,
  eliminar_evidencia: false,
});

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
  } catch (error) {
    console.error('Error cargando reporte psicopedagógico:', error);
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

const abrirEditar = (data) => {
  editandoRegistro.value = data;

  Object.assign(editForm, {
    id: data.id,
    id_profesional: data.id_profesional,
    fecha_atencion: String(data.fecha_atencion || '').slice(0, 10),
    semestre_academico: data.semestre_academico || '',
    celular: data.celular || '',

    condicion_academica: Array.isArray(data.condicion_academica)
      ? [...data.condicion_academica]
      : [],

    discapacidad: data.discapacidad || 'NO',

    presuncion_diagnostica: Array.isArray(data.presuncion_diagnostica)
      ? [...data.presuncion_diagnostica]
      : [],

    otro_diagnostico: data.otro_diagnostico || '',

    problemas_academicos: Array.isArray(data.problemas_academicos)
      ? [...data.problemas_academicos]
      : [],

    otro_problema_academico:
      data.otro_problema_academico || '',

    observaciones: data.observaciones || '',
    tipo_atencion: data.tipo_atencion || null,
    satisfaccion: data.satisfaccion || null,

    derivacion: data.derivacion || '',
    requiere_seguimiento: Boolean(data.requiere_seguimiento),
    seguimiento: data.seguimiento || '',

    evidencia: null,
    eliminar_evidencia: false,
  });

  editarVisible.value = true;
};

const seleccionarEvidenciaEdicion = (event) => {
  editForm.evidencia =
    event.target.files?.[0] || null;

  if (editForm.evidencia) {
    editForm.eliminar_evidencia = false;
  }
};

const guardarEdicion = async () => {
  if (!editForm.id) return;

  if (!editForm.id_profesional) {
    window.alert(
      'Seleccione el profesional responsable.'
    );
    return;
  }

  if (!editForm.fecha_atencion) {
    window.alert(
      'Ingrese la fecha de atención.'
    );
    return;
  }

  if (!editForm.tipo_atencion) {
    window.alert(
      'Seleccione el tipo de atención.'
    );
    return;
  }

  guardandoEdicion.value = true;

  try {
    const formData = new FormData();

    const agregarCampo = (campo, valor) => {
      if (Array.isArray(valor)) {
        valor.forEach((item) => {
          formData.append(
            `${campo}[]`,
            item
          );
        });
        return;
      }

      if (typeof valor === 'boolean') {
        formData.append(
          campo,
          valor ? '1' : '0'
        );
        return;
      }

      if (
        campo === 'evidencia'
      ) {
        if (valor) {
          formData.append(
            campo,
            valor
          );
        }
        return;
      }

      if (campo === 'id') {
        return;
      }

      formData.append(
        campo,
        valor ?? ''
      );
    };

    Object.entries(editForm)
      .forEach(([campo, valor]) => {
        agregarCampo(campo, valor);
      });

    const { data } = await axios.post(
      `/supervisor/servicio-psicopedagogico/${editForm.id}/actualizar`,
      formData,
      {
        headers: {
          'Content-Type':
            'multipart/form-data',
        }
      }
    );

    window.alert(
      data?.mensaje ||
      'La atención fue actualizada correctamente.'
    );

    editarVisible.value = false;
    editandoRegistro.value = null;

    await cargar(
      datos.value.current_page || 1
    );

  } catch (error) {
    console.error(
      'Error actualizando atención:',
      error
    );

    const errores =
      error?.response?.data?.errors;

    if (errores) {
      const primerError =
        Object.values(errores)?.[0]?.[0];

      window.alert(
        primerError ||
        'Revise los datos ingresados.'
      );
    } else {
      window.alert(
        error?.response?.data?.message ||
        'No se pudo actualizar la atención.'
      );
    }

  } finally {
    guardandoEdicion.value = false;
  }
};

const eliminarRegistro = async (data) => {
  const confirmar = window.confirm(
    `¿Está seguro de eliminar la sesión N.º ${data.numero_sesion} de ${data.estudiante}?\n\n` +
    'Esta acción eliminará definitivamente la atención y su evidencia adjunta, si existe.'
  );

  if (!confirmar) {
    return;
  }

  eliminandoId.value = data.id;

  try {
    const respuesta = await axios.delete(
      `/supervisor/servicio-psicopedagogico/${data.id}`
    );

    window.alert(
      respuesta?.data?.mensaje ||
      'La atención fue eliminada correctamente.'
    );

    const paginaActual =
      Number(datos.value.current_page || 1);

    const cantidadActual =
      Array.isArray(datos.value.data)
        ? datos.value.data.length
        : 0;

    const paginaDestino =
      cantidadActual === 1 &&
      paginaActual > 1
        ? paginaActual - 1
        : paginaActual;

    await cargar(paginaDestino);

  } catch (error) {
    console.error(
      'Error eliminando atención:',
      error
    );

    window.alert(
      error?.response?.data?.message ||
      'No se pudo eliminar la atención.'
    );

  } finally {
    eliminandoId.value = null;
  }
};

const escaparHtml = (valor) => {
  return String(valor ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
};

const textoImpresion = (valor) => {
  if (!valor) return '-';

  return escaparHtml(valor)
    .replace(/\n/g, '<br>');
};

const listaImpresion = (lista) => {
  if (
    !Array.isArray(lista) ||
    !lista.length
  ) {
    return '<span class="vacio">-</span>';
  }

  return `
    <ul>
      ${lista
        .map(
          (x) =>
            `<li>${escaparHtml(x)}</li>`
        )
        .join('')}
    </ul>
  `;
};

const imprimirFicha = (data) => {
  const ventana = window.open(
    '',
    '_blank',
    'width=1000,height=820'
  );

  if (!ventana) {
    window.alert(
      'El navegador bloqueó la ventana de impresión.'
    );
    return;
  }

  const evidenciaHtml =
    data.tiene_evidencia &&
    esImagen(data)
      ? `
        <div class="section">
          <h3>Evidencia fotográfica</h3>
          <img
            src="${urlEvidencia(data.id)}"
            class="evidencia"
            alt="Evidencia"
          />
        </div>
      `
      : data.tiene_evidencia
        ? `
          <div class="section">
            <h3>Evidencia</h3>
            <p>
              Documento adjunto:
              ${escaparHtml(
                data.evidencia_nombre ||
                'archivo PDF'
              )}
            </p>
          </div>
        `
        : '';

  const html = `
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Ficha de atención psicopedagógica</title>

<style>
  @page {
    size: A4;
    margin: 12mm;
  }

  * {
    box-sizing: border-box;
  }

  body {
    margin: 0;
    color: #222;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 11px;
  }

  .header {
    padding-bottom: 10px;
    margin-bottom: 12px;
    border-bottom: 3px solid #2b6cb0;
    text-align: center;
  }

  .header h1 {
    margin: 0;
    color: #244f83;
    font-size: 18px;
  }

  .header p {
    margin: 4px 0 0;
    color: #666;
  }

  .session {
    display: inline-block;
    margin-top: 7px;
    padding: 4px 10px;
    border-radius: 20px;
    background: #eaf3ff;
    color: #244f83;
    font-weight: bold;
  }

  .grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 7px;
  }

  .item {
    min-height: 42px;
    padding: 7px 9px;
    border: 1px solid #dfe5ec;
    border-radius: 5px;
  }

  .item label {
    display: block;
    margin-bottom: 3px;
    color: #687385;
    font-size: 9px;
    text-transform: uppercase;
  }

  .item strong,
  .item span {
    font-size: 11px;
  }

  .section {
    margin-top: 9px;
    padding: 8px 10px;
    border: 1px solid #dfe5ec;
    border-radius: 5px;
    page-break-inside: avoid;
  }

  .section h3 {
    margin: 0 0 6px;
    color: #244f83;
    font-size: 11px;
    text-transform: uppercase;
  }

  .section p {
    margin: 0;
    line-height: 1.45;
  }

  ul {
    margin: 0;
    padding-left: 18px;
  }

  li {
    margin: 2px 0;
  }

  .evidencia {
    display: block;
    max-width: 100%;
    max-height: 270px;
    margin: 7px auto 0;
    object-fit: contain;
  }

  .signatures {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 55px;
    margin-top: 45px;
    page-break-inside: avoid;
  }

  .firma {
    padding-top: 5px;
    border-top: 1px solid #444;
    text-align: center;
  }

  .firma small {
    color: #666;
  }

  .vacio {
    color: #777;
  }

  .footer {
    margin-top: 18px;
    color: #888;
    font-size: 9px;
    text-align: right;
  }

  @media print {
    .no-print {
      display: none;
    }
  }
</style>
</head>

<body>
  <div class="header">
    <h1>FICHA DE ATENCIÓN PSICOPEDAGÓGICA</h1>
    <p>Servicio Psicopedagógico</p>
    <div class="session">
      Sesión N.º ${escaparHtml(data.numero_sesion)}
    </div>
  </div>

  <div class="grid">
    <div class="item">
      <label>Fecha de atención</label>
      <strong>${escaparHtml(fecha(data.fecha_atencion))}</strong>
    </div>

    <div class="item">
      <label>Semestre académico</label>
      <strong>${escaparHtml(data.semestre_academico)}</strong>
    </div>

    <div class="item">
      <label>Profesional responsable</label>
      <strong>${escaparHtml(data.profesional)}</strong>
    </div>

    <div class="item">
      <label>Tipo de atención</label>
      <strong>${escaparHtml(data.tipo_atencion)}</strong>
    </div>

    <div class="item">
      <label>Estudiante</label>
      <strong>${escaparHtml(data.estudiante)}</strong>
    </div>

    <div class="item">
      <label>Código / DNI</label>
      <span>
        ${escaparHtml(data.codigo_estudiante)}
        / ${escaparHtml(data.dni)}
      </span>
    </div>

    <div class="item">
      <label>Facultad</label>
      <span>${escaparHtml(data.facultad)}</span>
    </div>

    <div class="item">
      <label>Escuela Profesional</label>
      <span>${escaparHtml(data.escuela_profesional)}</span>
    </div>

    <div class="item">
      <label>Edad / Sexo</label>
      <span>
        ${escaparHtml(data.edad)} años
        / ${escaparHtml(data.sexo)}
      </span>
    </div>

    <div class="item">
      <label>Ciclo / Celular</label>
      <span>
        ${escaparHtml(data.ciclo)}
        / ${escaparHtml(data.celular || '-')}
      </span>
    </div>
  </div>

  <div class="section">
    <h3>Condición académica</h3>
    ${listaImpresion(data.condicion_academica)}
  </div>

  <div class="section">
    <h3>Discapacidad</h3>
    <p>${escaparHtml(discapacidadTexto(data.discapacidad))}</p>
  </div>

  <div class="section">
    <h3>Presunción diagnóstica</h3>
    ${listaImpresion(data.presuncion_diagnostica)}
    ${
      data.otro_diagnostico
        ? `<p><strong>Otro:</strong> ${textoImpresion(data.otro_diagnostico)}</p>`
        : ''
    }
  </div>

  <div class="section">
    <h3>Problemas académicos</h3>
    ${listaImpresion(data.problemas_academicos)}
    ${
      data.otro_problema_academico
        ? `<p><strong>Otro:</strong> ${textoImpresion(data.otro_problema_academico)}</p>`
        : ''
    }
  </div>

  <div class="section">
    <h3>Observaciones</h3>
    <p>${textoImpresion(data.observaciones)}</p>
  </div>

  <div class="section">
    <h3>Derivación</h3>
    <p>${textoImpresion(data.derivacion)}</p>
  </div>

  <div class="section">
    <h3>Seguimiento</h3>
    <p>
      <strong>
        ${
          data.requiere_seguimiento
            ? 'Requiere seguimiento'
            : 'No requiere seguimiento'
        }
      </strong>
    </p>
    ${
      data.seguimiento
        ? `<p>${textoImpresion(data.seguimiento)}</p>`
        : ''
    }
  </div>

  <div class="section">
    <h3>Encuesta de satisfacción</h3>
    <p>${escaparHtml(data.satisfaccion || '-')}</p>
  </div>

  ${evidenciaHtml}

  <div class="signatures">
    <div class="firma">
      ${escaparHtml(data.profesional)}
      <br>
      <small>Profesional responsable</small>
    </div>

    <div class="firma">
      Servicio Psicopedagógico
      <br>
      <small>V.º B.º</small>
    </div>
  </div>

  <div class="footer">
    Ficha impresa desde el Sistema de Nivelación
  </div>

  <script>
    window.addEventListener('load', function () {
      setTimeout(function () {
        window.print();
      }, 500);
    });
  <\/script>
</body>
</html>
  `;

  ventana.document.open();
  ventana.document.write(html);
  ventana.document.close();
};

/*
 * EXPORTAR TODOS LOS REGISTROS FILTRADOS.
 * NO usa /export-data.
 * Usa únicamente /supervisor/servicio-psicopedagogico/data.
 */
const exportarExcelTodos = async () => {
  exportando.value = true;

  try {
    const todos = [];

    let pagina = 1;
    let ultimaPagina = 1;

    do {
      const { data } = await axios.get(
        '/supervisor/servicio-psicopedagogico/data',
        {
          params: {
            ...f,
            page: pagina,
            per_page: 100,
          }
        }
      );

      const paginacion = data?.datos || {};
      const registrosPagina = Array.isArray(paginacion.data)
        ? paginacion.data
        : [];

      todos.push(...registrosPagina);

      ultimaPagina = Number(paginacion.last_page || 1);
      pagina++;

    } while (pagina <= ultimaPagina);

    if (!todos.length) {
      window.alert('No existen registros para exportar con los filtros seleccionados.');
      return;
    }

    const registrosExcel = todos.map((r) => ({
      'Fecha': fecha(r.fecha_atencion),
      'Semestre académico': r.semestre_academico || '',
      'Profesional responsable': r.profesional || '',
      'N.º sesión': r.numero_sesion || '',
      'Código estudiante': r.codigo_estudiante || '',
      'DNI': r.dni || '',
      'Estudiante': r.estudiante || '',
      'Edad': r.edad ?? '',
      'Sexo': r.sexo || '',
      'Celular': r.celular || '',
      'Facultad': r.facultad || '',
      'Escuela Profesional': r.escuela_profesional || '',
      'Ciclo': r.ciclo || '',
      'Condición académica': Array.isArray(r.condicion_academica)
        ? r.condicion_academica.join(' | ')
        : '',
      'Discapacidad': discapacidadTexto(r.discapacidad),
      'Presunción diagnóstica': Array.isArray(r.presuncion_diagnostica)
        ? r.presuncion_diagnostica.join(' | ')
        : '',
      'Otro diagnóstico': r.otro_diagnostico || '',
      'Problemas académicos': Array.isArray(r.problemas_academicos)
        ? r.problemas_academicos.join(' | ')
        : '',
      'Otro problema académico': r.otro_problema_academico || '',
      'Observaciones': r.observaciones || '',
      'Tipo de atención': r.tipo_atencion || '',
      'Encuesta de satisfacción': r.satisfaccion || '',
      'Derivación': r.derivacion || '',
      'Requiere seguimiento': r.requiere_seguimiento ? 'Sí' : 'No',
      'Seguimiento': r.seguimiento || '',
      'Tiene evidencia': r.tiene_evidencia ? 'Sí' : 'No',
      'Nombre evidencia': r.evidencia_nombre || '',
    }));

    const hoja = XLSX.utils.json_to_sheet(registrosExcel);

    hoja['!cols'] = [
      { wch: 12 }, { wch: 18 }, { wch: 30 }, { wch: 10 },
      { wch: 16 }, { wch: 13 }, { wch: 38 }, { wch: 8 },
      { wch: 12 }, { wch: 15 }, { wch: 35 }, { wch: 45 },
      { wch: 10 }, { wch: 50 }, { wch: 25 }, { wch: 60 },
      { wch: 40 }, { wch: 60 }, { wch: 40 }, { wch: 60 },
      { wch: 18 }, { wch: 25 }, { wch: 50 }, { wch: 22 },
      { wch: 55 }, { wch: 18 }, { wch: 35 },
    ];

    const libro = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(libro, hoja, 'Atenciones');

    const hoy = new Date().toISOString().slice(0, 10);
    const periodo = f.semestre || 'TODOS';

    XLSX.writeFile(
      libro,
      `reporte_psicopedagogico_${periodo}_${hoy}.xlsx`
    );

  } catch (error) {
    console.error('Error exportando Excel:', error);

    window.alert(
      error?.response?.data?.message ||
      'No se pudo generar el archivo Excel.'
    );
  } finally {
    exportando.value = false;
  }
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

.hero-actions {
  display: flex;
  align-items: center;
  gap: 9px;
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

.stat-blue .stat-icon { background: #ebf8ff; color: #3182ce; }
.stat-indigo .stat-icon { background: #ebf4ff; color: #4c51bf; }
.stat-green .stat-icon { background: #f0fff4; color: #2f855a; }
.stat-cyan .stat-icon { background: #e6fffa; color: #319795; }
.stat-orange .stat-icon { background: #fffaf0; color: #dd6b20; }
.stat-red .stat-icon { background: #fff5f5; color: #c53030; }
.stat-purple .stat-icon { background: #faf5ff; color: #805ad5; }

.card {
  margin-bottom: 17px;
  padding: 20px;
  border: 1px solid #e5eaf1;
  border-radius: 14px;
  background: white;
  box-shadow: 0 3px 12px rgba(30, 55, 80, .05);
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

.w-full { width: 100%; }
.rows-select { width: 90px; }

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

.evidence-thumb:hover { transform: scale(1.08); }
.sin-evidencia { color: #a0aec0; }

.pagination {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  gap: 5px;
  margin-top: 13px;
  color: #4a5568;
  font-size: .82rem;
}

.detail-wrap { color: #344054; }

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

.detail-banner span { font-size: .76rem; opacity: .88; }
.detail-banner h3 { margin: 3px 0; }
.detail-banner p { margin: 0; font-size: .84rem; opacity: .9; }
.detail-date { font-weight: 700; }

.detail-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
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
.detail-item span { font-size: .86rem; }

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

.detail-section p { margin: 0; font-size: .84rem; }

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

.chip-indigo { color: #553c9a; background: #e9d8fd; }
.chip-blue { color: #2b6cb0; background: #ebf8ff; }
.chip-orange { color: #9c4221; background: #fffaf0; }
.preserve { white-space: pre-wrap; }

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

.action-buttons {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 2px;
}

.edit-wrap {
  color: #344054;
}

.edit-student-banner {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 15px;

  margin-bottom: 17px;
  padding: 15px 17px;

  border-radius: 11px;

  color: white;

  background:
    linear-gradient(
      135deg,
      #2b6cb0,
      #319795
    );
}

.edit-student-banner span {
  font-size: .75rem;
  opacity: .86;
}

.edit-student-banner h3 {
  margin: 3px 0;
  font-size: 1.05rem;
}

.edit-student-banner p {
  margin: 0;
  font-size: .82rem;
  opacity: .9;
}

.edit-readonly-note {
  display: flex;
  align-items: center;
  gap: 6px;

  padding: 7px 9px;

  border-radius: 8px;

  background: rgba(255,255,255,.15);

  font-size: .75rem;
}

.edit-readonly {
  background: #f6f8fa !important;
}

.edit-section {
  margin-top: 13px;
  padding: 13px;

  border: 1px solid #e8edf3;
  border-radius: 10px;

  background: #fbfcfd;
}

.edit-section h4 {
  margin: 0 0 10px;

  color: #2d3748;

  font-size: .9rem;
}

.edit-options {
  display: grid;

  grid-template-columns:
    repeat(
      2,
      minmax(0, 1fr)
    );

  gap: 9px 18px;
}

.edit-option {
  display: flex !important;
  align-items: flex-start;
  gap: 8px;

  color: #4a5568;

  font-size: .84rem;
  font-weight: 400 !important;
}

.edit-field-label {
  display: block;
  margin-bottom: 6px;

  color: #4a5568;

  font-size: .82rem;
  font-weight: 600;
}

.current-evidence {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 10px;

  padding: 9px 11px;

  border: 1px solid #bee3f8;
  border-radius: 8px;

  background: #ebf8ff;

  color: #2b6cb0;
  font-size: .82rem;
}

.current-evidence > div {
  display: flex;
  align-items: center;
  gap: 7px;

  min-width: 0;
}

.current-evidence span {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.edit-file {
  width: 100%;
  padding: 7px;

  border: 1px solid #d8dee8;
  border-radius: 7px;

  background: white;
}

.edit-help {
  display: block;

  margin-top: 4px;

  color: #8490a2;

  font-size: .73rem;
}

.remove-file {
  min-height: 39px;
  align-items: center;
}

@media (max-width: 1150px) {
  .stats-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
}

@media (max-width: 760px) {
  .reporte-page { padding: 10px; }

  .hero-actions {
    width: 100%;
    flex-direction: column;
  }

  .hero-actions :deep(.p-button) { width: 100%; }

  .hero-report,
  .section-title,
  .table-header {
    align-items: stretch;
    flex-direction: column;
  }

  .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  .detail-grid { grid-template-columns: 1fr; }
  .filter-actions { width: 100%; }
}
</style>
