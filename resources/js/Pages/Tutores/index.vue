<template>
<Head title="Tutores"/>
<AuthenticatedLayout>
  <!-- <div class="bg-white shadow-xs p-4" style="height: calc(100vh - 110px); font-family: Arial, Helvetica, sans-serif;">
    <v-btn color="primary" variant="tonal" :ripple="true" @click="dialog = true" nocaps>
      Nuevo
    </v-btn>

    <v-dialog
      v-model="dialog"
      width="500px"
    >
      <v-card>
        <v-card-header>
          <div style="display: flex; justify-content: space-between;">
            <div class="p-3">
              <h3 style="font-weight:bold; font-family: Arial, Helvetica, sans-serif;">Crear nuevo docente</h3>
            </div>            

            <v-btn color="gray" variant="text"  :ripple="true" @click="dialog = false">
              x
            </v-btn>
          </div>
        </v-card-header>
        <v-card-text>

          <div class="flex" style="justify-content: space-between; align-items: center; ">
            <div style="margin-bottom: 20px; width: 220px;">
              <div><label>Tipo documento</label></div>
              <v-btn-toggle v-model="tipo_doc" variant="outlined" density="compact" :rounded="100" color="primary" >
                <v-btn width="73">
                  Dni
                </v-btn>

                <v-btn width="74">
                  C. Ext
                </v-btn>
                <v-btn width="73">
                  Pass
                </v-btn>
              </v-btn-toggle>
            </div>

            <div style="margin-bottom: 20px; width: 220px;">
            
            </div>

            
          </div>

          <div class="flex" style="justify-content: space-between;">
            <div style="width: 220px;">
              <label>Nro de documento</label>
              <v-text-field density="compact" variant="outlined"></v-text-field>
            </div>
            <div style="width: 220px;">
              <label>Nombres</label>
              <v-text-field density="compact" variant="outlined"></v-text-field>
            </div>
          </div>
          
          <div class="flex" style="justify-content: space-between;">
            <div style="width: 220px;">
              <label>Apellido paterno</label>
              <v-text-field density="compact" variant="outlined"></v-text-field>
            </div>
            <div style="width: 220px;">
              <label>Apellido materno</label>
              <v-text-field density="compact" variant="outlined"></v-text-field>
            </div>
          </div>



          <div class="flex" style="justify-content: space-between; align-items: center; ">
            <div style="margin-bottom: 20px; width: 220px;">


              <v-date-picker v-model="picker2" color="green lighten-1" header-color="primary"></v-date-picker>

            </div>

            <div style="margin-bottom: 20px; width: 220px;">

              <div><label>Sexo</label></div>
              <v-btn-toggle v-model="sexo" variant="outlined" density="compact" :rounded="100" color="primary" >
                <v-btn width="110">
                  Masculino
                </v-btn>
                <v-btn width="110">
                  Femenino
                </v-btn>
              </v-btn-toggle>
            
            </div>

            
          </div>


        </v-card-text>
        <v-card-actions class="flex" style="justify-content: flex-end;">
          <v-btn color="primary" variant="tonal" :ripple="true" @click="dialog = false"> Guardar </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

      
  </div> -->
</AuthenticatedLayout>
</template>
  
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';



//   const props = defineProps({
//       users: Object
//   })
</script>

<style scoped>


</style>