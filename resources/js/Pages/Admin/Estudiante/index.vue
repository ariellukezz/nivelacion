<template>
    <Head title="Alumnos"/>
    <AuthenticatedLayout>
    <div class="bg-white shadow-xs p-4" style=" height: calc(100vh - 110px); font-family: Arial, Helvetica, sans-serif;">

        <div>
          <div class="flex" style="justify-content: space-between;">
            <Button label="Lista de Estudiantes" @click="visible = true" size="small" style="height: 40px;"/>

            <div class="flex align-items-center gap-2" style="flex-wrap:wrap; justify-content:flex-end;">
                <Dropdown
                    v-model="filtroEstado"
                    :options="opcionesEstado"
                    optionLabel="label"
                    optionValue="value"
                    style="width:160px; height:40px"
                />
                <span class="p-input-icon-left">
                    <i class="pi pi-search" />
                    <InputText v-model="buscar" style="padding-left: 40px; height: 40px;" placeholder="Buscar estudiante" />
                </span>
                <Button label="Columnas" @click="toggle" size="small" style="height: 40px;">
                    <i class="pi pi-cog" />
                </Button>
            </div>

          </div>
        </div>
        <Toast />
        <ConfirmPopup></ConfirmPopup>

        <div>
          <div class="card">
            <div class="flex justify-content-center mb-4">
            </div>
            <DataTable :value="usuarios" :class="'p-datatable-sm'" paginator :rows="10"  tableStyle="min-width: 50rem" style="font-size: .9rem;">
                <!--bdhh <Column field="dni" header="Dni"></Column> -->
                <Column field="codigo_est" header="codigo_est"></Column>
                <Column field="semestre" header="Ingreso"></Column>
                <Column v-if="conf_codigo === true" field="codigo" header="Código"></Column>
                <Column field="nombres" header="Nombres">
                    <template #body="{ data }">
                        <div class="flex" style="justify-content: flex-start;">
                            <div>
                                {{ data.nombres }} {{ data.paterno }} {{ data.materno }}
                            </div>
                        </div>
                    </template>
                </Column>
                <Column field="sexo" header="Sexo"></Column>
                <Column field="tipo_examen" header="Tipo Examen"></Column>
                <Column field="programa" header="Programa">
                    <template #body="{ data }">
                        <div class="flex" style="justify-content: flex-start;">
                            <div style=" width: 200px; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                                <span> {{ data.programa }} </span>
                            </div>
                        </div>
                    </template>
                </Column>
                <Column field="estado_nivelacion" header="Estado" style="width:110px">
                    <template #body="{ data }">
                        <Tag
                            :severity="Number(data.estado_nivelacion) === 1 ? 'success' : 'danger'"
                            :value="Number(data.estado_nivelacion) === 1 ? 'Activo' : 'Retirado'"
                        />
                    </template>
                </Column>
                <Column header="Acción" style="width:135px">
                    <template #body="{ data }">
                        <Button
                            v-if="Number(data.estado_nivelacion) === 1"
                            label="Retirar"
                            icon="pi pi-user-minus"
                            severity="danger"
                            outlined
                            size="small"
                            @click="abrirRetiro(data)"
                        />
                        <Button
                            v-else
                            label="Reactivar"
                            icon="pi pi-user-plus"
                            severity="success"
                            outlined
                            size="small"
                            @click="confirmarReactivar($event, data)"
                        />
                    </template>
                </Column>
                <Column v-if="conf_telefono === true" field="telefono" header="Telefono"></Column>
                <Column v-if="conf_colegio === true" field="colegio" header="Colegio"></Column>
                <Column v-if="conf_tipo_colegio === true" field="tipo_colegio" header="Tipo Colegio"></Column>
                <Column v-if="conf_estado_civil === true" field="estado_civil" header="Est civ"></Column>
                <Column v-if="conf_area === true" field="area" header="Area"></Column>
                <Column v-if="conf_modalidad === true" field="modalidad" header="Modalidad"></Column>
            </DataTable>
          </div>
        </div>

        <OverlayPanel ref="op">
            <div class="card">
                <div class="flex mt-1" style="justify-content: flex-end;">Codigo: <InputSwitch v-model="conf_codigo" /></div>
                <div class="flex mt-1" style="justify-content: flex-end;">telefono: <InputSwitch v-model="conf_telefono" /></div>
                <div class="flex mt-1" style="justify-content: flex-end;">Colegio: <InputSwitch v-model="conf_colegio" /></div>
                <div class="flex mt-1" style="justify-content: flex-end;">Tipo colegio: <InputSwitch v-model="conf_tipo_colegio" /></div>
                <div class="flex mt-1" style="justify-content: flex-end;">Estado civil: <InputSwitch v-model="conf_estado_civil" /></div>
                <div class="flex mt-1" style="justify-content: flex-end;">area: <InputSwitch v-model="conf_area" /></div>
                <div class="flex mt-1" style="justify-content: flex-end;">modalidad: <InputSwitch v-model="conf_modalidad" /></div>
            </div>
        </OverlayPanel>

        <Dialog v-model:visible="modalRetiro" modal header="Retirar estudiante de nivelación" :style="{ width: '560px', maxWidth: '95vw' }">
            <div v-if="estudianteSeleccionado" class="mb-3">
                <div style="font-size:.85rem; color:#666">Estudiante</div>
                <strong>{{ estudianteSeleccionado.nombres }} {{ estudianteSeleccionado.paterno }} {{ estudianteSeleccionado.materno }}</strong>
                <div style="font-size:.85rem; margin-top:4px">{{ estudianteSeleccionado.programa }}</div>
            </div>
            <label style="font-weight:bold">Motivo del retiro</label>
            <Textarea v-model="motivoRetiro" rows="4" autoResize class="w-full mt-2" placeholder="Ej.: retiro voluntario, traslado, abandono de estudios..." />
            <small style="color:#666">No se eliminarán sus notas ni matrículas históricas.</small>
            <template #footer>
                <Button label="Cancelar" outlined @click="modalRetiro = false" />
                <Button label="Confirmar retiro" icon="pi pi-check" severity="danger" :loading="guardandoEstado" @click="guardarRetiro" />
            </template>
        </Dialog>

    </div>
    </AuthenticatedLayout>
    </template>

    <script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';
    import { ref, watch } from 'vue';
    import Button from 'primevue/button';
    import InputText from 'primevue/inputtext';
    import DataTable from 'primevue/datatable';
    import Column from 'primevue/column';
    import InputSwitch from 'primevue/inputswitch';
    import Toast from 'primevue/toast';
    import { useToast } from "primevue/usetoast";
    import { useConfirm } from "primevue/useconfirm";
    import ConfirmPopup from 'primevue/confirmpopup';
    import OverlayPanel from 'primevue/overlaypanel';
    import Dropdown from 'primevue/dropdown';
    import Dialog from 'primevue/dialog';
    import Textarea from 'primevue/textarea';
    import Tag from 'primevue/tag';

    const toast = useToast();
    const confirm = useConfirm();

    const totalpaginas = ref(0)
    const pagina = ref(1)
    const buscar = ref("")

    const usuarios = ref([])
    const visible = ref(false);
    const filtroEstado = ref('todos');
    const opcionesEstado = [
      { label: 'Todos', value: 'todos' },
      { label: 'Activos', value: '1' },
      { label: 'Retirados', value: '0' },
    ];
    const modalRetiro = ref(false);
    const estudianteSeleccionado = ref(null);
    const motivoRetiro = ref('');
    const guardandoEstado = ref(false);



    const conf_telefono = ref(true);
    const conf_codigo = ref(false);
    const conf_colegio = ref(false);
    const conf_tipo_colegio = ref(false);
    const conf_estado_civil = ref(false);
    const conf_area = ref(true);
    const conf_modalidad = ref(false);

    const op = ref();
    const toggle = (event) => {
        op.value.toggle(event);
    }

    const usuario = ref({
      id: null,
      nombres:"",
      apellidos:"",
      rol:2,
      programa:3,
      email:"",
      password:"",
      estado:true
    })

    const getAlumnos =  async (event) => {
      let res = await axios.post(
      "/coordinador/get-alumnos?page=" + pagina.value,
      {
        term: buscar.value,
        telefono: conf_telefono.value,
        codigo: conf_codigo.value,
        colegio: conf_colegio.value,
        tipo_colegio: conf_tipo_colegio.value,
        estado_civil: conf_estado_civil.value,
        area: conf_area.value,
        modalidad: conf_modalidad.value,
        estado_nivelacion: filtroEstado.value
     }
      );
      usuarios.value = res.data.datos.data;
      totalpaginas.value = res.data.datos.total;
    }

    const guardar =  async () => {
      let res = await axios.post(
        "save-usuario",
        {
          id: usuario.value.id,
          nombres : usuario.value.nombres,
          apellidos : usuario.value.apellidos,
          email : usuario.value.email,
          password : usuario.value.password,
          programa : usuario.value.programa,
          estado : usuario.value.estado,
          rol : usuario.value.rol
        }
      );

      showToast(res.data.tipo, res.data.titulo, res.data.mensaje)
      getAlumnos()
      visible.value = false
      limpiar()

    }

    const eliminar =  async (id) => {
      let res = await axios.get(
      "delete-usuario/"+id);
      showToast(res.data.tipo, res.data.titulo, res.data.mensaje)
      getAlumnos()
    }


    const editar =  async (item) => {
      visible.value = true;
      usuario.value.id = item.id
      usuario.value.nombres = item.nombres
      usuario.value.apellidos = item.apellidos
      usuario.value.email = item.email
      usuario.value.programa = item.id_programa
      usuario.value.rol = item.id_rol
      console.log(item);
    }

    const abrirRetiro = (item) => {
      estudianteSeleccionado.value = item;
      motivoRetiro.value = '';
      modalRetiro.value = true;
    }

    const guardarRetiro = async () => {
      if (!estudianteSeleccionado.value?.id) return;
      if (!motivoRetiro.value.trim()) {
        showToast('warn', 'Falta información', 'Indique el motivo del retiro.');
        return;
      }

      guardandoEstado.value = true;
      try {
        const res = await axios.patch(`/coordinador/estudiante/${estudianteSeleccionado.value.id}/estado-nivelacion`, {
          estado_nivelacion: false,
          motivo: motivoRetiro.value.trim(),
        });
        showToast(res.data.tipo, res.data.titulo, res.data.mensaje);
        modalRetiro.value = false;
        estudianteSeleccionado.value = null;
        motivoRetiro.value = '';
        await getAlumnos();
      } catch (error) {
        showToast('error', 'Error', error?.response?.data?.mensaje || 'No se pudo retirar al estudiante.');
      } finally {
        guardandoEstado.value = false;
      }
    }

    const reactivar = async (item) => {
      try {
        const res = await axios.patch(`/coordinador/estudiante/${item.id}/estado-nivelacion`, {
          estado_nivelacion: true,
          motivo: null,
        });
        showToast(res.data.tipo, res.data.titulo, res.data.mensaje);
        await getAlumnos();
      } catch (error) {
        showToast('error', 'Error', error?.response?.data?.mensaje || 'No se pudo reactivar al estudiante.');
      }
    }

    const confirmarReactivar = (event, item) => {
      confirm.require({
        target: event.currentTarget,
        message: `¿Reactivar a ${item.nombres} ${item.paterno}?`,
        icon: 'pi pi-info-circle',
        acceptLabel: 'Reactivar',
        rejectLabel: 'Cancelar',
        accept: () => reactivar(item),
      });
    };

    const changeRol = (event) => {
      console.log("::ROL::",event);
    }

    watch(buscar, () => { getAlumnos() })
    watch(filtroEstado, () => { getAlumnos() })


    const showToast = (tipo, titulo, detalle) => {
        toast.add({ severity: tipo, summary: titulo, detail: detalle, life: 3000 });
    };

    const confirm2 = (event,user) => {
        confirm.require({
            target: event.currentTarget,
            message: '¿Estas seguro de eliminar al usuario '+ user.nombres+'?',
            icon: 'pi pi-info-circle',
            acceptClass: 'p-button-danger',
            accept: () => {
              eliminar(user.id)
            },
            reject: () => {
                toast.add({ severity: 'error', summary: 'Elimación cancelada', detail: 'Se ha cancelado la eliminación del usuario'+user.nombres, life: 3000 });
            }
        });
    };

    watch(visible, ( newValue, oldValue ) => {
      if(visible.value == false && usuario.value.id != null ){
        limpiar()
      }

    })

    const limpiar = () => {
      usuario.value.id= null,
      usuario.value.nombres = "",
      usuario.value.apellidos = "",
      usuario.value.rol = 2,
      usuario.value.programa = 3,
      usuario.value.email = "",
      usuario.value.password = "",
      usuario.value.estado = true
    }

    watch(conf_codigo, ( newValue, oldValue ) => { getAlumnos()})
    watch(conf_telefono, ( newValue, oldValue ) => { getAlumnos()})
    watch(conf_colegio, ( newValue, oldValue ) => { getAlumnos()})
    watch(conf_tipo_colegio, ( newValue, oldValue ) => { getAlumnos()})
    watch(conf_estado_civil, ( newValue, oldValue ) => { getAlumnos()})
    watch(conf_area, ( newValue, oldValue ) => { getAlumnos()})
    watch(conf_modalidad, ( newValue, oldValue ) => { getAlumnos()})


    getAlumnos()

    </script>
