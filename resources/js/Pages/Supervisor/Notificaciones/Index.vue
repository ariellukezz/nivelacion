<template>
  <AuthenticatedLayout>
    <Head title="Notificaciones" />
    <div class="p-4" style="background:white;">
      <div class="text-center mb-4">
        <h1 class="text-xl font-bold">NOTIFICACIONES</h1>
      </div>

      <!-- Filtros simplificados para listar -->
      <div class="grid md:grid-cols-3 gap-2 mb-3">
        <div>
          <label class="lbl">Rol</label>
          <select v-model="filtros.rol_id" class="inp">
            <option :value="''">— Todos —</option>
            <option :value="1">Administradores</option>
            <option :value="4">Docentes</option>
            <option :value="5">Estudiantes</option>
          </select>
        </div>
        <div>
          <label class="lbl">Término</label>
          <InputText v-model="filtros.term" class="w-full" placeholder="Título, mensaje, usuario, DNI, código…" @keyup.enter="cargar" />
        </div>
        <div class="flex items-end">
          <Button label="Buscar" size="small" @click="cargar" :disabled="cargando" />
          <Button label="Limpiar" size="small" severity="secondary" @click="limpiar" :disabled="cargando" />
        </div>
      </div>
      <span v-if="cargando" class="text-sm text-blue-600">Cargando...</span>

      <!-- Crear / Enviar notificaciones -->
      <div class="card mb-5">
        <div class="font-semibold mb-2">Crear notificación</div>

        <div class="grid md:grid-cols-4 gap-2">
          <div class="md:col-span-2">
            <label class="lbl">Título</label>
            <InputText v-model="form.titulo" class="w-full" placeholder="Opcional" />
          </div>
          <div>
            <label class="lbl">Tipo</label>
            <select v-model="form.tipo" class="inp">
              <option value="info">info</option>
              <option value="success">success</option>
              <option value="warning">warning</option>
              <option value="error">error</option>
            </select>
          </div>
          <div>
            <label class="lbl">Enlace (URL)</label>
            <InputText v-model="form.url" class="w-full" placeholder="https://…" />
          </div>

          <div class="md:col-span-4">
            <label class="lbl">Mensaje</label>
            <textarea v-model="form.mensaje" class="inp w-full" rows="3" placeholder="Mensaje a mostrar" />
          </div>

          <!-- Imagen: subir nueva o reutilizar URL existente -->
          <div class="md:col-span-2">
            <label class="lbl">Imagen URL (reutilizar existente)</label>
            <InputText v-model="form.imagen_url" class="w-full" placeholder="Pegar URL de imagen previa (ej. /storage/notificaciones/abc.jpg)" />
          </div>
          <div class="md:col-span-2">
            <label class="lbl">O subir nueva (png/jpg/jpeg)</label>
            <input type="file" accept=".png,.jpg,.jpeg" @change="onPickImg($event, 'crear')" />
          </div>
          <div class="md:col-span-4 flex items-end gap-2">
            <img v-if="preview.crear" :src="preview.crear" alt="prev" class="h-16 rounded object-contain border" />
            <span v-else-if="form.imagen_url" class="text-gray-500 text-sm">URL: {{ form.imagen_url }}</span>
            <span v-else class="text-gray-500 text-sm">Sin imagen</span>
          </div>
        </div>

        <!-- Targeting simplificado -->
        <div class="mt-4">
          <div class="font-semibold mb-2">Destinatarios</div>

          <div class="flex items-center gap-3 mb-3">
            <label class="flex items-center gap-2">
              <input type="radio" value="filtros" v-model="modoEnvio" /> Usar filtros
            </label>
            <label class="flex items-center gap-2">
              <input type="radio" value="usuarios" v-model="modoEnvio" /> Seleccionar usuarios
            </label>
          </div>

          <!-- Modo filtros: simplificado -->
          <div v-if="modoEnvio === 'filtros'">
            <div class="grid md:grid-cols-3 gap-2 mb-3">
              <div>
                <label class="lbl">Rol *</label>
                <select v-model="targetingFiltros.rol_id" class="inp">
                  <option :value="''">— Seleccione —</option>
                  <option :value="1">Administradores</option>
                  <option :value="4">Docentes</option>
                  <option :value="5">Estudiantes</option>
                </select>
              </div>
              <div>
                <label class="lbl">Término (opcional)</label>
                <InputText v-model="targetingFiltros.term" class="w-full" placeholder="Nombre, apellido, DNI, código, email" />
              </div>
              <div class="flex items-end">
                <Button label="Estimar" size="small" @click="validarFiltros" :disabled="cargando" />
                <span class="text-sm text-gray-600">Estimados: {{ numDestinatarios }}</span>
              </div>
            </div>
          </div>

          <!-- Modo usuarios: simplificado -->
          <div v-else-if="modoEnvio === 'usuarios'">
            <div class="grid md:grid-cols-3 gap-2 mb-3">
              <div>
                <label class="lbl">Rol *</label>
                <select v-model="busqUsuarios.rol_id" class="inp">
                  <option :value="''">— Seleccione —</option>
                  <option :value="1">Administradores</option>
                  <option :value="4">Docentes</option>
                  <option :value="5">Estudiantes</option>
                </select>
              </div>
              <div>
                <label class="lbl">Buscar</label>
                <InputText v-model="busqUsuarios.term" class="w-full" placeholder="Nombre, apellido, DNI, código, email" @keyup.enter="buscarUsuarios" />
              </div>
              <div class="flex items-end">
                <Button label="Buscar" size="small" @click="buscarUsuarios" :disabled="cargando" />
                <span class="text-sm text-gray-600">Resultados: {{ usuariosEncontrados.length }}</span>
              </div>
            </div>

            <div class="grid md:grid-cols-2 gap-2">
              <div>
                <div class="subt">Coincidencias</div>
                <div class="list">
                  <div v-for="u in usuariosEncontrados" :key="u.id" class="row">
                    <span>{{ u.nombre_completo }} <small class="text-gray-500">({{ u.rol_nombre }})</small></span>
                    <Button label="Agregar" size="small" @click="agregarUsuario(u)" />
                  </div>
                  <div v-if="usuariosEncontrados.length===0" class="text-sm text-gray-500 p-2">Sin resultados</div>
                </div>
              </div>
              <div>
                <div class="subt">Seleccionados ({{ seleccionados.length }})</div>
                <div class="list">
                  <div v-for="u in seleccionados" :key="u.id" class="row">
                    <span>{{ u.nombre_completo }} <small class="text-gray-500">({{ u.rol_nombre }})</small></span>
                    <Button label="Quitar" size="small" severity="secondary" @click="quitarUsuario(u.id)" />
                  </div>
                  <div v-if="seleccionados.length===0" class="text-sm text-gray-500 p-2">Ninguno</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="flex items-center gap-2 mt-4">
          <Button label="Crear notificación" @click="crear" :disabled="cargando" />
          <span class="text-xs text-gray-500">Se marcarán como <strong>no leídas</strong> y <strong>activas</strong>.</span>
        </div>
      </div>

      <!-- Tabla de notificaciones -->
      <table style="border-radius:4px; width:100%; overflow:hidden;">
        <thead style="height:30px; color:#fff; background-color:var(--primary-color);">
          <tr style="font-size:.9rem;">
            <th class="th">TÍTULO</th>
            <th class="th">MENSAJE</th>
            <th class="th">TIPO</th>
            <th class="th">ENLACE</th>
            <th class="th">IMAGEN</th>
            <th class="th">ESTADO</th>
            <th class="th">USUARIO</th>
            <th class="th">ROL</th>
            <th class="th">FECHA</th>
            <th class="th" style="width:140px;">ACCIONES</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="n in rows" :key="n.id" style="font-size:.9rem;">
            <td class="td">{{ n.titulo || '—' }}</td>
            <td class="td"><strong>{{ n.mensaje }}</strong></td>
            <td class="td" :class="tipoClase(n.tipo)" style="text-align:center;">{{ n.tipo }}</td>
            <td class="td" style="text-align:center;">
              <a v-if="n.url" :href="n.url" target="_blank" class="underline text-blue-600">Ir al enlace</a>
              <span v-else>—</span>
            </td>
            <td class="td" style="text-align:center;">
              <img v-if="n.imagen_url" :src="n.imagen_url" alt="img" style="max-height:40px; object-fit:contain; margin:auto;" />
              <span v-else>—</span>
            </td>
            <td class="td" :class="estadoClase(n.activo)" style="text-align:center;">{{ estadoTexto(n.activo) }}</td>
            <td class="td">{{ n.usuario || '—' }}</td>
            <td class="td" style="text-align:center;">{{ n.rol_nombre }}</td>
            <td class="td" style="text-align:center;">{{ formatFecha(n.created_at) }}</td>
            <td class="td" style="text-align:center;">
              <div class="flex items-center gap-2 justify-center">
                <Button label="Editar" size="small" @click="abrirEditar(n)" :disabled="cargando" />
                <Button label="Eliminar" size="small" severity="danger" @click="eliminar(n)" :disabled="cargando" />
              </div>
            </td>
          </tr>
          <tr v-if="rows.length===0 && !cargando">
            <td class="td" colspan="10" style="text-align:center; padding:10px;">Sin resultados</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Editar (simplificado similar a crear) -->
    <Dialog v-model:visible="dlgEdit" modal header="Editar notificación" :style="{ width: '640px' }">
      <div class="grid md:grid-cols-4 gap-3">
        <div class="md:col-span-2">
          <label class="lbl">Título</label>
          <InputText v-model="edit.titulo" class="w-full" />
        </div>
        <div>
          <label class="lbl">Tipo</label>
          <select v-model="edit.tipo" class="inp">
            <option value="info">info</option>
            <option value="success">success</option>
            <option value="warning">warning</option>
            <option value="error">error</option>
          </select>
        </div>
        <div>
          <label class="lbl">Enlace (URL)</label>
          <InputText v-model="edit.url" class="w-full" />
        </div>

        <div class="md:col-span-4">
          <label class="lbl">Mensaje</label>
          <textarea v-model="edit.mensaje" class="inp w-full" rows="3" />
        </div>

        <div class="md:col-span-2">
          <label class="lbl">Imagen URL (reutilizar)</label>
          <InputText v-model="edit.imagen_url" class="w-full" placeholder="Pegar URL de imagen previa" />
        </div>
        <div class="md:col-span-2">
          <label class="lbl">O subir nueva</label>
          <input type="file" accept=".png,.jpg,.jpeg" @change="onPickImg($event, 'editar')" />
        </div>
        <div class="md:col-span-4 flex items-end gap-2">
          <img v-if="preview.editar" :src="preview.editar" class="h-16 rounded object-contain border" />
          <span v-else-if="edit.imagen_url" class="text-gray-500 text-sm">URL: {{ edit.imagen_url }}</span>
          <span v-else class="text-gray-500 text-sm">Sin imagen</span>
        </div>

        <div>
          <label class="lbl">Estado</label>
          <select v-model="edit.activo" class="inp">
            <option :value="1">Activo</option>
            <option :value="0">Inactivo</option>
          </select>
        </div>
        <div>
          <label class="lbl">Leído</label>
          <select v-model="edit.leido" class="inp">
            <option :value="0">No leído</option>
            <option :value="1">Leído</option>
          </select>
        </div>
      </div>

      <template #footer>
        <div class="flex items-center gap-2">
          <Button label="Guardar cambios" @click="guardarEdicion" :disabled="cargando" />
          <Button label="Cerrar" severity="secondary" @click="dlgEdit=false" />
        </div>
      </template>
    </Dialog>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/LayoutSupervisor.vue'
import { Head } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Dialog from 'primevue/dialog'

// --- Estados de carga ---
const cargando = ref(false)

// --- Filtros simplificados ---
const filtros = ref({ rol_id: '', term: '' })
const rows = ref([])

// Crear
const form = ref({ titulo: '', mensaje: '', tipo: 'info', url: '', imagen_url: '' })
const modoEnvio = ref('filtros')
const imgFileCrear = ref(null)
const imgFileEditar = ref(null)
const preview = ref({ crear: '', editar: '' })

// Targeting simplificado
const targetingFiltros = ref({ rol_id: '', term: '' })
const numDestinatarios = ref(0)

// Buscar usuarios simplificado
const busqUsuarios = ref({ rol_id: '', term: '' })
const usuariosEncontrados = ref([])
const seleccionados = ref([])

// Editar
const dlgEdit = ref(false)
const edit = ref({ id: null, titulo: '', mensaje: '', tipo: 'info', url: '', imagen_url: '', activo: 1, leido: 0 })

const estadoTexto = (v) => {
  if (v === true || v === 1 || v === '1') return 'Activo'
  if (v === false || v === 0 || v === '0') return 'Inactivo'
  return String(v ?? '—')
}
const estadoClase = (v) => (estadoTexto(v) === 'Activo' ? 'ok' : 'bad')
const tipoClase = (t) => {
  if (!t) return ''
  const x = String(t).toLowerCase()
  if (x === 'success') return 'success'
  if (x === 'warning' || x === 'warn') return 'warn'
  if (x === 'error' || x === 'danger') return 'error'
  return 'info'
}
const formatFecha = (f) => (!f ? '—' : new Date(f).toLocaleString())

function limpiar() {
  filtros.value = { rol_id: '', term: '' }
  cargar()
}

async function cargar() {
  if (cargando.value) return
  cargando.value = true
  try {
    const payload = {
      rol_id: filtros.value.rol_id || undefined,
      term: filtros.value.term || undefined,
      per_page: 50,
    }
    const { data } = await axios.post('/supervisor/listar-notificaciones', payload)
    rows.value = Array.isArray(data?.datos?.data) ? data.datos.data : []
  } catch (error) {
    console.error('Error al cargar notificaciones:', error)
    alert('Error al cargar notificaciones.')
    rows.value = []
  } finally {
    cargando.value = false
  }
}

async function validarFiltros() {
  if (!targetingFiltros.value.rol_id) {
    alert('Seleccione un rol para estimar destinatarios')
    return
  }
  if (cargando.value) return
  cargando.value = true
  try {
    const payload = {
      rol_id: targetingFiltros.value.rol_id,
      term: targetingFiltros.value.term || undefined,
      limite: 50,
    }
    const { data } = await axios.post('/supervisor/buscar-usuarios-notificaciones', payload)
    numDestinatarios.value = Array.isArray(data?.datos) ? data.datos.length : 0
  } catch (error) {
    console.error('Error al validar filtros:', error)
    alert('Error al estimar destinatarios.')
    numDestinatarios.value = 0
  } finally {
    cargando.value = false
  }
}

async function buscarUsuarios() {
  if (!busqUsuarios.value.rol_id) {
    alert('Seleccione un rol para buscar usuarios')
    return
  }
  if (cargando.value) return
  cargando.value = true
  try {
    const payload = {
      rol_id: busqUsuarios.value.rol_id,
      term: busqUsuarios.value.term || undefined,
      limite: 50,
    }
    const { data } = await axios.post('/supervisor/buscar-usuarios-notificaciones', payload)
    usuariosEncontrados.value = Array.isArray(data?.datos) ? data.datos : []
  } catch (error) {
    console.error('Error al buscar usuarios:', error)
    alert('Error al buscar usuarios.')
    usuariosEncontrados.value = []
  } finally {
    cargando.value = false
  }
}

function agregarUsuario(u) {
  if (!seleccionados.value.find(x => x.id === u.id)) {
    seleccionados.value.push(u)
  }
}
function quitarUsuario(id) {
  seleccionados.value = seleccionados.value.filter(x => x.id !== id)
}

function onPickImg(e, modo) {
  const file = e.target.files?.[0]
  if (!file) return
  if (!/image\/(png|jpe?g)/i.test(file.type)) {
    alert('Solo se permiten PNG/JPG/JPEG')
    return
  }
  const reader = new FileReader()
  reader.onload = () => {
    if (modo === 'crear') {
      imgFileCrear.value = file
      preview.value.crear = reader.result
      form.value.imagen_url = '' // Limpiar URL si suben nueva
    } else {
      imgFileEditar.value = file
      preview.value.editar = reader.result
      edit.value.imagen_url = '' // Limpiar URL si suben nueva
    }
  }
  reader.readAsDataURL(file)
}

async function crear() {
  if (!form.value.mensaje?.trim()) {
    alert('El mensaje es obligatorio')
    return
  }
  if (cargando.value) return
  cargando.value = true
  try {
    const fd = new FormData()
    fd.append('titulo', form.value.titulo || '')
    fd.append('mensaje', form.value.mensaje || '')
    fd.append('tipo', form.value.tipo || 'info')
    fd.append('url', form.value.url || '')
    fd.append('activo', '1')
    if (form.value.imagen_url) fd.append('imagen_url', form.value.imagen_url) // Reutilizar URL

    if (imgFileCrear.value) fd.append('imagen_file', imgFileCrear.value)

    if (modoEnvio.value === 'usuarios') {
      const ids = seleccionados.value.map(x => x.id)
      if (ids.length === 0) {
        alert('Seleccione al menos un usuario')
        return
      }
      ids.forEach(id => fd.append('user_ids[]', id))
    } else {
      if (!targetingFiltros.value.rol_id) {
        alert('Seleccione un rol para los destinatarios')
        return
      }
      fd.append('rol_id', targetingFiltros.value.rol_id)
      if (targetingFiltros.value.term) fd.append('term', targetingFiltros.value.term)
    }

    const { data } = await axios.post('/supervisor/guardar-notificacion', fd, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    alert(`${data?.mensaje || 'Creado'} (creadas: ${data?.creadas ?? 0})`)
    form.value = { titulo: '', mensaje: '', tipo: 'info', url: '', imagen_url: '' }
    imgFileCrear.value = null
    preview.value.crear = ''
    seleccionados.value = []
    targetingFiltros.value = { rol_id: '', term: '' }
    numDestinatarios.value = 0
    await cargar()
  } catch (error) {
    console.error('Error al crear notificación:', error)
    alert('Error al crear notificación.')
  } finally {
    cargando.value = false
  }
}

function abrirEditar(n) {
  edit.value = {
    id: n.id,
    titulo: n.titulo || '',
    mensaje: n.mensaje || '',
    tipo: n.tipo || 'info',
    url: n.url || '',
    imagen_url: n.imagen_url || '',
    activo: n.activo ? 1 : 0,
    leido: n.leido ? 1 : 0
  }
  imgFileEditar.value = null
  preview.value.editar = ''
  dlgEdit.value = true
}

async function guardarEdicion() {
  const id = edit.value.id
  if (!id) return
  if (cargando.value) return
  cargando.value = true
  try {
    const fd = new FormData()
    fd.append('titulo', edit.value.titulo || '')
    fd.append('mensaje', edit.value.mensaje || '')
    fd.append('tipo', edit.value.tipo || 'info')
    fd.append('url', edit.value.url || '')
    fd.append('activo', String(edit.value.activo ? 1 : 0))
    fd.append('leido', String(edit.value.leido ? 1 : 0))
    if (edit.value.imagen_url) fd.append('imagen_url', edit.value.imagen_url) // Reutilizar URL
    if (imgFileEditar.value) fd.append('imagen_file', imgFileEditar.value)

    const { data } = await axios.post(`/supervisor/actualizar-notificacion/${id}`, fd, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    alert(data?.mensaje || 'Actualizado')
    dlgEdit.value = false
    await cargar()
  } catch (error) {
    console.error('Error al actualizar notificación:', error)
    alert('Error al actualizar notificación.')
  } finally {
    cargando.value = false
  }
}

async function eliminar(n) {
  if (!confirm('¿Eliminar esta notificación?')) return
  if (cargando.value) return
  cargando.value = true
  try {
    const { data } = await axios.post(`/supervisor/eliminar-notificacion/${n.id}`)
    alert(data?.mensaje || 'Eliminado')
    await cargar()
  } catch (error) {
    console.error('Error al eliminar notificación:', error)
    alert('Error al eliminar notificación.')
  } finally {
    cargando.value = false
  }
}

// Inicialización
onMounted(() => {
  cargar()
})
</script>

<style scoped>
.lbl { font-size:.8rem; color:#374151; display:block; margin-bottom:4px; }
.inp { border:1px solid #d9d9d9; border-radius:4px; padding:6px; width:100%; }
.card { border:1px solid #e5e7eb; border-radius:8px; padding:12px; }
.subt { font-weight:600; margin-bottom:6px; }
.list { border:1px solid #e5e7eb; border-radius:6px; max-height:220px; overflow:auto; }
.row { display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #f1f5f9; padding:6px 8px; }
.row:last-child { border-bottom:0; }

.th { border:1px solid #d9d9d9; padding:6px; }
.td { border:1px solid #d9d9d9; padding:6px; }
.ok { color:#16a34a; font-weight:600; }
.bad { color:#dc2626; font-weight:600; }
.success { color:#16a34a; font-weight:600; }
.warn { color:#d97706; font-weight:600; }
.error { color:#dc2626; font-weight:600; }
.info { color:#2563eb; font-weight:600; }
</style>
