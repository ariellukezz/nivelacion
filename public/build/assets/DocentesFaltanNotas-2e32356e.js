import{r as h,G as b,o as z,j as R,w,a as k,e as i,b as x,d as c,Z as M,f as e,c as p,t as s,F as O,m as G,g as N,i as H,a6 as Y,n as F,p as Z,q as J}from"./app-1b559a8b.js";import{A as K}from"./LayoutSupervisor-c1857506.js";import{s as V}from"./dropdown.esm-3b0d50af.js";import{s as T}from"./index.esm-490a0fc5.js";import{_ as Q}from"./_plugin-vue_export-helper-c27b6911.js";import"./TopMenu-b51e71cb.js";import"./index.esm-57370536.js";import"./dialog.esm-529e88fc.js";import"./ResponsiveNavLink-cf2b2672.js";const n=f=>(Z("data-v-d9cab450"),f=f(),J(),f),W={class:"p-4 bg-white rounded-lg shadow"},X={class:"flex justify-between items-center mb-6"},ee=n(()=>e("div",null,[e("h2",{class:"text-2xl font-bold text-gray-800"},"Docentes que Faltan Subir Notas"),e("p",{class:"text-gray-600"},"Lista de docentes con estudiantes pendientes de calificación")],-1)),te={class:"mb-6 grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-gray-50 rounded-lg"},oe={key:0},ae={key:1},se={class:"flex justify-between items-center"},ne={class:"text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded"},re={key:0,class:"text-center py-8"},de=n(()=>e("i",{class:"pi pi-spin pi-spinner",style:{"font-size":"2rem"}},null,-1)),le=n(()=>e("p",{class:"mt-2"},"Cargando docentes...",-1)),ie=[de,le],ce={key:1,class:"bg-white rounded-lg border"},pe={class:"p-4 bg-orange-600 text-white font-bold flex justify-between items-center"},ue={class:"overflow-x-auto"},ge={class:"min-w-full divide-y divide-gray-200"},_e=n(()=>e("thead",{class:"bg-gray-50"},[e("tr",null,[e("th",{class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Docente "),e("th",{class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Competencia - Programa "),e("th",{class:"px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"}," Total Estudiantes "),e("th",{class:"px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"}," Sin Nota "),e("th",{class:"px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"}," Con Nota "),e("th",{class:"px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"}," % Sin Nota "),e("th",{class:"px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"}," % Con Nota ")])],-1)),he={class:"bg-white divide-y divide-gray-200"},me={class:"px-6 py-4 whitespace-nowrap"},xe={class:"font-semibold text-orange-700"},fe={class:"px-6 py-4"},ye={class:"font-medium text-gray-900"},ve={class:"text-sm text-gray-500"},be={class:"px-6 py-4 whitespace-nowrap text-center"},we={class:"px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold"},ke={class:"px-6 py-4 whitespace-nowrap text-center"},Ne={class:"px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold"},Se={class:"px-6 py-4 whitespace-nowrap text-center"},De={class:"px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold"},Ce={class:"px-6 py-4 whitespace-nowrap text-center"},Pe={class:"flex items-center justify-center"},$e={class:"px-6 py-4 whitespace-nowrap text-center"},Ae={class:"px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold"},Fe={key:2,class:"text-center py-12 bg-gray-50 rounded-lg"},Ve=n(()=>e("i",{class:"pi pi-check-circle",style:{"font-size":"3rem",color:"#10B981"}},null,-1)),Te=n(()=>e("h3",{class:"mt-4 text-lg font-medium text-gray-900"},"¡Todos los docentes están al día!",-1)),je=n(()=>e("p",{class:"mt-2 text-gray-500"},"No hay docentes pendientes de subir notas para los filtros seleccionados.",-1)),Ee=[Ve,Te,je],Le={key:3,class:"mt-6 p-4 bg-gray-50 rounded-lg"},Ie=n(()=>e("h3",{class:"font-semibold mb-2"},"Información del Reporte",-1)),Ue={class:"grid grid-cols-1 md:grid-cols-3 gap-4 text-sm"},qe=n(()=>e("strong",null,"Período:",-1)),Be=n(()=>e("strong",null,"Programa:",-1)),ze=n(()=>e("strong",null,"Fecha:",-1)),Re={__name:"DocentesFaltanNotas",setup(f){const r=h([]),g=h([]),u=h(null),_=h(null),d=h([]),y=h(!1),S=b(()=>r.value.find(t=>t.id_periodo===u.value)),D=b(()=>g.value.find(t=>t.id===_.value)),C=b(()=>new Date().toLocaleDateString("es-ES",{year:"numeric",month:"long",day:"numeric"})),P=new URLSearchParams(window.location.search),$=P.get("periodo"),A=P.get("programa"),j=t=>{const a=g.value.find(l=>l.id===t);return a?`${a.programa} (${a.filial})`:""},E=t=>t>=50?"bg-red-100 text-red-800":t>=30?"bg-orange-100 text-orange-800":"bg-yellow-100 text-yellow-800",L=async()=>{try{const t=await k.get("obtener-periodos");if(t.data&&Array.isArray(t.data.periodos)?r.value=t.data.periodos:t.data&&Array.isArray(t.data.raw)?r.value=t.data.raw:Array.isArray(t.data)?r.value=t.data:r.value=[],$)u.value=parseInt($);else if(r.value.length>0){const a=r.value.find(l=>l.estado==="activo")||r.value[0];u.value=a.id_periodo}}catch(t){console.error("Error cargando periodos:",t),r.value=[]}},I=async()=>{try{const t=await k.get("get-programas-filial");t.data.estado&&Array.isArray(t.data.programas)?g.value=t.data.programas:g.value=[],A&&(_.value=parseInt(A))}catch(t){console.error("Error cargando programas:",t),g.value=[]}},v=async()=>{if(u.value)try{y.value=!0;const t={periodo:u.value};_.value&&(t.programa=_.value);const a=await k.get("/docentes-faltan-notas",{params:t});a.data.estado?d.value=a.data.datos||[]:d.value=[]}catch(t){console.error("Error cargando docentes:",t),d.value=[]}finally{y.value=!1}},U=()=>{Y.get(route("monitoreo-docentes"))},q=()=>{var l,m;const t=`
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Docentes que Faltan Subir Notas</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
                .header h1 { color: #2c5aa0; margin: 0; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
                th { background-color: #f59e0b; color: white; }
                tr:nth-child(even) { background-color: #f2f2f2; }
                .info { background-color: #f5f5f5; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Reporte de Docentes que Faltan Subir Notas</h1>
                <p>Sistema de Nivelación - Universidad</p>
            </div>

            <div class="info">
                <p><strong>Período:</strong> ${((l=S.value)==null?void 0:l.nombre)||"No seleccionado"}</p>
                <p><strong>Programa:</strong> ${((m=D.value)==null?void 0:m.programa)||"Todos los programas"}</p>
                <p><strong>Fecha:</strong> ${C.value}</p>
                <p><strong>Total de docentes pendientes:</strong> ${d.value.length}</p>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Docente</th>
                        <th>Competencia</th>
                        <th>Programa</th>
                        <th>Total Estudiantes</th>
                        <th>Sin Nota</th>
                        <th>Con Nota</th>
                        <th>% Sin Nota</th>
                        <th>% Con Nota</th>
                    </tr>
                </thead>
                <tbody>
                    ${d.value.map(o=>`
                        <tr>
                            <td>${o.docente_nombre}</td>
                            <td>${o.competencia_nombre}</td>
                            <td>${o.programa}</td>
                            <td>${o.total_estudiantes}</td>
                            <td>${o.estudiantes_sin_nota}</td>
                            <td>${o.estudiantes_con_nota}</td>
                            <td>${o.porcentaje_sin_nota}%</td>
                            <td>${o.porcentaje_con_nota}%</td>
                        </tr>
                    `).join("")}
                </tbody>
            </table>
        </body>
        </html>
    `,a=window.open("","_blank");a.document.write(t),a.document.close(),setTimeout(()=>{a.print()},500)};return z(async()=>{await L(),await I(),setTimeout(()=>{v()},100)}),(t,a)=>(i(),R(K,null,{default:w(()=>{var l,m;return[x(c(M),{title:"Docentes que Faltan Subir Notas"}),e("div",W,[e("div",X,[ee,x(c(T),{label:"Volver al Dashboard",icon:"pi pi-arrow-left",onClick:U,class:"p-button-secondary"})]),e("div",te,[x(c(V),{modelValue:u.value,"onUpdate:modelValue":a[0]||(a[0]=o=>u.value=o),options:r.value,optionLabel:"nombre",optionValue:"id_periodo",placeholder:"Seleccionar Periodo",class:"w-full",onChange:v,showClear:"",filter:""},null,8,["modelValue","options"]),x(c(V),{modelValue:_.value,"onUpdate:modelValue":a[1]||(a[1]=o=>_.value=o),options:g.value,optionLabel:"programa",optionValue:"id",placeholder:"Seleccionar Programa",class:"w-full",onChange:v,showClear:"",filter:""},{value:w(o=>[o.value?(i(),p("div",oe,[e("span",null,s(j(o.value)),1)])):(i(),p("span",ae,s(o.placeholder),1))]),option:w(o=>[e("div",se,[e("span",null,s(o.option.programa),1),e("span",ne,s(o.option.filial),1)])]),_:1},8,["modelValue","options"])]),y.value?(i(),p("div",re,ie)):d.value.length>0?(i(),p("div",ce,[e("div",pe,[e("span",null,"Docentes Pendientes ("+s(d.value.length)+")",1),x(c(T),{label:"Exportar PDF",icon:"pi pi-file-pdf",onClick:q,class:"p-button-outlined p-button-sm"})]),e("div",ue,[e("table",ge,[_e,e("tbody",he,[(i(!0),p(O,null,G(d.value,(o,B)=>(i(),p("tr",{key:o.docente_id,class:F(B%2===0?"bg-white":"bg-gray-50")},[e("td",me,[e("div",xe,s(o.docente_nombre),1)]),e("td",fe,[e("div",ye,s(o.competencia_nombre),1),e("div",ve,s(o.programa)+" - "+s(o.filial),1)]),e("td",be,[e("span",we,s(o.total_estudiantes),1)]),e("td",ke,[e("span",Ne,s(o.estudiantes_sin_nota),1)]),e("td",Se,[e("span",De,s(o.estudiantes_con_nota),1)]),e("td",Ce,[e("div",Pe,[e("span",{class:F(["px-3 py-1 rounded-full text-sm font-semibold",E(o.porcentaje_sin_nota)])},s(o.porcentaje_sin_nota)+"% ",3)])]),e("td",$e,[e("span",Ae,s(o.porcentaje_con_nota)+"% ",1)])],2))),128))])])])])):(i(),p("div",Fe,Ee)),d.value.length>0?(i(),p("div",Le,[Ie,e("div",Ue,[e("div",null,[qe,N(" "+s(((l=c(S))==null?void 0:l.nombre)||"No seleccionado"),1)]),e("div",null,[Be,N(" "+s(((m=c(D))==null?void 0:m.programa)||"Todos los programas"),1)]),e("div",null,[ze,N(" "+s(c(C)),1)])])])):H("",!0)])]}),_:1}))}},We=Q(Re,[["__scopeId","data-v-d9cab450"]]);export{We as default};
