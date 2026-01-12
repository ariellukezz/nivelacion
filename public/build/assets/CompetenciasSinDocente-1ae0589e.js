import{r as _,G as b,L as z,o as R,j as M,w,a as C,e as d,b as x,d as c,Z as O,f as e,c as p,t as s,F as G,m as H,g as S,i as K,a6 as Y,n as E,p as Z,q as J}from"./app-1b559a8b.js";import{A as Q}from"./LayoutSupervisor-c1857506.js";import{s as V}from"./dropdown.esm-3b0d50af.js";import{s as T}from"./index.esm-490a0fc5.js";import{_ as W}from"./_plugin-vue_export-helper-c27b6911.js";import"./TopMenu-b51e71cb.js";import"./index.esm-57370536.js";import"./dialog.esm-529e88fc.js";import"./ResponsiveNavLink-cf2b2672.js";const n=f=>(Z("data-v-d034fd5a"),f=f(),J(),f),X={class:"p-4 bg-white rounded-lg shadow"},ee={class:"flex justify-between items-center mb-6"},te=n(()=>e("div",null,[e("h2",{class:"text-2xl font-bold text-gray-800"},"Competencias Sin Docente Asignado"),e("p",{class:"text-gray-600"},"Lista de competencias que requieren asignación de docente")],-1)),oe={class:"mb-6 grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-gray-50 rounded-lg"},ae={key:0},se={key:1},ne={class:"flex justify-between items-center"},re={class:"text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded"},ie={key:0,class:"text-center py-8"},le=n(()=>e("i",{class:"pi pi-spin pi-spinner",style:{"font-size":"2rem"}},null,-1)),de=n(()=>e("p",{class:"mt-2"},"Cargando competencias...",-1)),ce=[le,de],pe={key:1,class:"bg-white rounded-lg border"},ue={class:"p-4 bg-blue-600 text-white font-bold flex justify-between items-center"},ge={class:"overflow-x-auto"},me={class:"min-w-full divide-y divide-gray-200"},_e=n(()=>e("thead",{class:"bg-gray-50"},[e("tr",null,[e("th",{class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Competencia "),e("th",{class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Programa - Filial "),e("th",{class:"px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"}," Total Estudiantes "),e("th",{class:"px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"}," Estudiantes Sin Nota "),e("th",{class:"px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"}," Estudiantes Con Nota "),e("th",{class:"px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"}," % Sin Nota ")])],-1)),he={class:"bg-white divide-y divide-gray-200"},xe={class:"px-6 py-4 whitespace-nowrap"},fe={class:"font-semibold text-blue-700"},ye={class:"px-6 py-4 whitespace-nowrap"},ve={class:"text-sm text-gray-900"},be={class:"text-sm text-gray-500"},we={class:"px-6 py-4 whitespace-nowrap text-center"},Ce={class:"px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold"},Se={class:"px-6 py-4 whitespace-nowrap text-center"},ke={class:"px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold"},Ae={class:"px-6 py-4 whitespace-nowrap text-center"},De={class:"px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold"},Pe={class:"px-6 py-4 whitespace-nowrap text-center"},$e={class:"flex items-center justify-center"},Ne={key:2,class:"text-center py-12 bg-gray-50 rounded-lg"},Ee=n(()=>e("i",{class:"pi pi-inbox",style:{"font-size":"3rem",color:"#9CA3AF"}},null,-1)),Ve=n(()=>e("h3",{class:"mt-4 text-lg font-medium text-gray-900"},"No hay competencias sin docente",-1)),Te=n(()=>e("p",{class:"mt-2 text-gray-500"},"Todas las competencias tienen docente asignado para los filtros seleccionados.",-1)),Fe=[Ee,Ve,Te],Le={key:3,class:"mt-6 p-4 bg-gray-50 rounded-lg"},je=n(()=>e("h3",{class:"font-semibold mb-2"},"Información del Reporte",-1)),Ie={class:"grid grid-cols-1 md:grid-cols-3 gap-4 text-sm"},Ue=n(()=>e("strong",null,"Período:",-1)),Be=n(()=>e("strong",null,"Programa:",-1)),qe=n(()=>e("strong",null,"Fecha:",-1)),ze={__name:"CompetenciasSinDocente",setup(f){const r=_([]),g=_([]),u=_(null),m=_(null),i=_([]),y=_(!1),k=b(()=>r.value.find(t=>t.id_periodo===u.value)),A=b(()=>g.value.find(t=>t.id===m.value)),D=b(()=>new Date().toLocaleDateString("es-ES",{year:"numeric",month:"long",day:"numeric"}));z();const P=new URLSearchParams(window.location.search),$=P.get("periodo"),N=P.get("programa"),F=t=>{const a=g.value.find(l=>l.id===t);return a?`${a.programa} (${a.filial})`:""},L=t=>t>=80?"bg-red-100 text-red-800":t>=50?"bg-orange-100 text-orange-800":"bg-yellow-100 text-yellow-800",j=async()=>{try{const t=await C.get("obtener-periodos");if(t.data&&Array.isArray(t.data.periodos)?r.value=t.data.periodos:t.data&&Array.isArray(t.data.raw)?r.value=t.data.raw:Array.isArray(t.data)?r.value=t.data:r.value=[],$)u.value=parseInt($);else if(r.value.length>0){const a=r.value.find(l=>l.estado==="activo")||r.value[0];u.value=a.id_periodo}}catch(t){console.error("Error cargando periodos:",t),r.value=[]}},I=async()=>{try{const t=await C.get("get-programas-filial");t.data.estado&&Array.isArray(t.data.programas)?g.value=t.data.programas:g.value=[],N&&(m.value=parseInt(N))}catch(t){console.error("Error cargando programas:",t),g.value=[]}},v=async()=>{if(u.value)try{y.value=!0;const t={periodo:u.value};m.value&&(t.programa=m.value);const a=await C.get("competencias-sin-docente",{params:t});a.data.estado?i.value=a.data.datos||[]:i.value=[]}catch(t){console.error("Error cargando competencias:",t),i.value=[]}finally{y.value=!1}},U=()=>{Y.get(route("monitoreo-docentes"))},B=()=>{var l,h;const t=`
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Competencias Sin Docente</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
                .header h1 { color: #2c5aa0; margin: 0; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
                th { background-color: #2c5aa0; color: white; }
                tr:nth-child(even) { background-color: #f2f2f2; }
                .info { background-color: #f5f5f5; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Reporte de Competencias Sin Docente</h1>
                <p>Sistema de Nivelación - Universidad</p>
            </div>

            <div class="info">
                <p><strong>Período:</strong> ${((l=k.value)==null?void 0:l.nombre)||"No seleccionado"}</p>
                <p><strong>Programa:</strong> ${((h=A.value)==null?void 0:h.programa)||"Todos los programas"}</p>
                <p><strong>Fecha:</strong> ${D.value}</p>
                <p><strong>Total de competencias:</strong> ${i.value.length}</p>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Competencia</th>
                        <th>Programa</th>
                        <th>Filial</th>
                        <th>Total Estudiantes</th>
                        <th>Est. Sin Nota</th>
                        <th>Est. Con Nota</th>
                        <th>% Sin Nota</th>
                    </tr>
                </thead>
                <tbody>
                    ${i.value.map(o=>`
                        <tr>
                            <td>${o.competencia_nombre}</td>
                            <td>${o.programa}</td>
                            <td>${o.filial}</td>
                            <td>${o.total_estudiantes}</td>
                            <td>${o.estudiantes_sin_nota}</td>
                            <td>${o.estudiantes_con_nota}</td>
                            <td>${o.porcentaje_sin_nota}%</td>
                        </tr>
                    `).join("")}
                </tbody>
            </table>
        </body>
        </html>
    `,a=window.open("","_blank");a.document.write(t),a.document.close(),setTimeout(()=>{a.print()},500)};return R(async()=>{await j(),await I(),setTimeout(()=>{v()},100)}),(t,a)=>(d(),M(Q,null,{default:w(()=>{var l,h;return[x(c(O),{title:"Competencias Sin Docente"}),e("div",X,[e("div",ee,[te,x(c(T),{label:"Volver al Dashboard",icon:"pi pi-arrow-left",onClick:U,class:"p-button-secondary"})]),e("div",oe,[x(c(V),{modelValue:u.value,"onUpdate:modelValue":a[0]||(a[0]=o=>u.value=o),options:r.value,optionLabel:"nombre",optionValue:"id_periodo",placeholder:"Seleccionar Periodo",class:"w-full",onChange:v,showClear:"",filter:""},null,8,["modelValue","options"]),x(c(V),{modelValue:m.value,"onUpdate:modelValue":a[1]||(a[1]=o=>m.value=o),options:g.value,optionLabel:"programa",optionValue:"id",placeholder:"Seleccionar Programa",class:"w-full",onChange:v,showClear:"",filter:""},{value:w(o=>[o.value?(d(),p("div",ae,[e("span",null,s(F(o.value)),1)])):(d(),p("span",se,s(o.placeholder),1))]),option:w(o=>[e("div",ne,[e("span",null,s(o.option.programa),1),e("span",re,s(o.option.filial),1)])]),_:1},8,["modelValue","options"])]),y.value?(d(),p("div",ie,ce)):i.value.length>0?(d(),p("div",pe,[e("div",ue,[e("span",null,"Competencias Sin Docente ("+s(i.value.length)+")",1),x(c(T),{label:"Exportar PDF",icon:"pi pi-file-pdf",onClick:B,class:"p-button-outlined p-button-sm"})]),e("div",ge,[e("table",me,[_e,e("tbody",he,[(d(!0),p(G,null,H(i.value,(o,q)=>(d(),p("tr",{key:o.competencia_id,class:E(q%2===0?"bg-white":"bg-gray-50")},[e("td",xe,[e("div",fe,s(o.competencia_nombre),1)]),e("td",ye,[e("div",ve,s(o.programa),1),e("div",be,s(o.filial),1)]),e("td",we,[e("span",Ce,s(o.total_estudiantes),1)]),e("td",Se,[e("span",ke,s(o.estudiantes_sin_nota),1)]),e("td",Ae,[e("span",De,s(o.estudiantes_con_nota),1)]),e("td",Pe,[e("div",$e,[e("span",{class:E(["px-3 py-1 rounded-full text-sm font-semibold",L(o.porcentaje_sin_nota)])},s(o.porcentaje_sin_nota)+"% ",3)])])],2))),128))])])])])):(d(),p("div",Ne,Fe)),i.value.length>0?(d(),p("div",Le,[je,e("div",Ie,[e("div",null,[Ue,S(" "+s(((l=c(k))==null?void 0:l.nombre)||"No seleccionado"),1)]),e("div",null,[Be,S(" "+s(((h=c(A))==null?void 0:h.programa)||"Todos los programas"),1)]),e("div",null,[qe,S(" "+s(c(D)),1)])])])):K("",!0)])]}),_:1}))}},Qe=W(ze,[["__scopeId","data-v-d034fd5a"]]);export{Qe as default};
