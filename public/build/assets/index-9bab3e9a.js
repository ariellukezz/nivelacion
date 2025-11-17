import{r as g,G as w,o as B,j as G,w as S,a as P,e as n,b as x,d as u,Z as H,f as e,c as d,t as a,i as C,F as V,m as E,g as $,p as O,q as Y}from"./app-df00ec4e.js";import{A as Z}from"./LayoutSupervisor-adc8305f.js";import{s as z}from"./dropdown.esm-b8162aa9.js";import{s as N}from"./index.esm-b50760ad.js";import{_ as J}from"./_plugin-vue_export-helper-c27b6911.js";import"./TopMenu-edcbe2b9.js";import"./ResponsiveNavLink-fc04ebc9.js";const r=y=>(O("data-v-c00f3c5a"),y=y(),Y(),y),K={class:"p-4 bg-white rounded-lg shadow"},Q=r(()=>e("h2",{class:"text-xl font-semibold mb-4"},"Monitoreo de Docentes",-1)),W={class:"mb-6 grid grid-cols-1 md:grid-cols-2 gap-4"},X={key:0},ee={key:1},te={class:"flex justify-between items-center"},se={class:"text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded"},ae={class:"flex gap-2 items-end"},oe={key:0,class:"text-center py-8"},ne=r(()=>e("i",{class:"pi pi-spin pi-spinner",style:{"font-size":"2rem"}},null,-1)),ie=r(()=>e("p",{class:"mt-2"},"Cargando información...",-1)),de=[ne,ie],le={key:1,class:"grid grid-cols-1 md:grid-cols-4 gap-4 mb-6"},re={class:"bg-blue-50 p-4 rounded-lg border border-blue-200"},ce={class:"text-blue-600 font-bold text-2xl"},pe=r(()=>e("div",{class:"text-blue-800 text-sm"},"Competencias sin docente",-1)),ue={class:"bg-orange-50 p-4 rounded-lg border border-orange-200"},ve={class:"text-orange-600 font-bold text-2xl"},_e=r(()=>e("div",{class:"text-orange-800 text-sm"},"Docentes faltan notas",-1)),ge={class:"bg-red-50 p-4 rounded-lg border border-red-200"},me={class:"text-red-600 font-bold text-2xl"},be=r(()=>e("div",{class:"text-red-800 text-sm"},"Estudiantes sin nota",-1)),fe={class:"bg-purple-50 p-4 rounded-lg border border-purple-200"},he={class:"text-purple-600 font-bold text-2xl"},xe=r(()=>e("div",{class:"text-purple-800 text-sm"},"Programas afectados",-1)),ye={key:2,class:"text-center py-8 text-gray-500"},we={key:3,class:"grid grid-cols-1 lg:grid-cols-2 gap-6"},$e={class:"border rounded-lg"},De={class:"bg-blue-600 text-white p-3 font-bold flex justify-between items-center"},ke=r(()=>e("span",null,"Competencias que Necesitan Docente",-1)),Se={class:"bg-blue-800 px-2 py-1 rounded-full text-sm"},Pe={class:"p-4 max-h-96 overflow-y-auto"},Ce={key:0,class:"text-gray-500 text-center py-4"},Ne={key:1,class:"space-y-3"},Fe={class:"font-semibold text-blue-700"},Te={class:"text-sm text-gray-600"},je={class:"flex justify-between items-center mt-1"},Ae={class:"text-sm text-red-600 font-semibold"},Ve={class:"text-xs bg-gray-100 px-2 py-1 rounded"},Ee={class:"border rounded-lg"},ze={class:"bg-orange-600 text-white p-3 font-bold flex justify-between items-center"},Le=r(()=>e("span",null,"Docentes que Faltan Subir Notas",-1)),Me={class:"bg-orange-800 px-2 py-1 rounded-full text-sm"},Re={class:"p-4 max-h-96 overflow-y-auto"},Ue={key:0,class:"text-gray-500 text-center py-4"},qe={key:1,class:"space-y-3"},Ie={class:"font-semibold text-orange-700"},Be={class:"text-sm text-gray-600"},Ge={class:"mt-2 space-y-1"},He={class:"flex justify-between items-center text-xs"},Oe={class:"text-red-600 font-semibold"},Ye={class:"text-red-500 font-bold"},Ze={class:"flex justify-between items-center text-xs"},Je={class:"text-green-600"},Ke={class:"flex justify-between items-center text-xs border-t pt-1 mt-1"},Qe={class:"text-blue-600 font-bold"},We={key:4,class:"mt-6 p-4 bg-gray-50 rounded-lg"},Xe=r(()=>e("h3",{class:"font-semibold mb-2"},"Información del Reporte",-1)),et={class:"grid grid-cols-1 md:grid-cols-2 gap-4 text-sm"},tt=r(()=>e("strong",null,"Período:",-1)),st=r(()=>e("strong",null,"Programa:",-1)),at=r(()=>e("strong",null,"Fecha de generación:",-1)),ot=r(()=>e("strong",null,"Total de registros:",-1)),nt={__name:"index",setup(y){const c=g([]),b=g([]),l=g(null),v=g(null),i=g(null),m=g([]),_=g([]),f=g(!1),D=g(!1),F=w(()=>c.value.find(s=>s.id_periodo===l.value)),T=w(()=>b.value.find(s=>s.id===v.value)),j=w(()=>new Date().toLocaleDateString("es-ES",{year:"numeric",month:"long",day:"numeric"})),A=w(()=>m.value.length+_.value.length),L=s=>{const o=b.value.find(p=>p.id===s);return o?`${o.programa} (${o.filial})`:""},M=async()=>{try{const s=await P.get("obtener-periodos");if(s.data&&Array.isArray(s.data.periodos)?c.value=s.data.periodos:s.data&&Array.isArray(s.data.raw)?c.value=s.data.raw:Array.isArray(s.data)?c.value=s.data:c.value=[],c.value.length>0){const o=c.value.find(p=>p.estado==="activo")||c.value[0];l.value=o.id_periodo}}catch(s){console.error("Error cargando periodos:",s),c.value=[]}},R=async()=>{try{const s=await P.get("get-programas-filial");s.data.estado&&Array.isArray(s.data.programas)?b.value=s.data.programas:b.value=[]}catch(s){console.error("Error cargando programas:",s),b.value=[]}},k=async()=>{if(l.value)try{f.value=!0;const s={periodo:l.value};v.value&&(s.programa=v.value);const o=await P.get("dashboard-docentes",{params:s});o.data.estado&&(i.value=o.data.estadisticas||{},m.value=o.data.competencias_sin_docente||[],_.value=o.data.docentes_faltan_notas||[],console.log("Datos de docentes:",_.value))}catch(s){console.error("Error cargando dashboard:",s),i.value=null}finally{f.value=!1}},U=async()=>{var s,o;if(!(!l.value||!i.value))try{D.value=!0;const p=`
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Reporte de Monitoreo de Docentes</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
                    .header h1 { color: #2c5aa0; margin: 0; }
                    .header .subtitle { color: #666; font-size: 14px; }
                    .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 30px; }
                    .stat-card { border: 1px solid #ddd; padding: 15px; text-align: center; border-radius: 5px; }
                    .stat-number { font-size: 24px; font-weight: bold; margin-bottom: 5px; }
                    .stat-label { font-size: 12px; color: #666; }
                    .section { margin-bottom: 25px; }
                    .section-title { background-color: #2c5aa0; color: white; padding: 10px; font-weight: bold; border-radius: 5px 5px 0 0; }
                    .section-content { border: 1px solid #ddd; border-top: none; padding: 15px; }
                    .item { margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #eee; }
                    .item:last-child { border-bottom: none; }
                    .item-title { font-weight: bold; color: #2c5aa0; }
                    .item-details { font-size: 12px; color: #666; margin: 5px 0; }
                    .item-stats { font-size: 12px; margin: 5px 0; }
                    .stat-row { display: flex; justify-content: space-between; margin: 2px 0; }
                    .info-section { background-color: #f5f5f5; padding: 15px; border-radius: 5px; margin-top: 20px; }
                    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 12px; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>Reporte de Monitoreo de Docentes</h1>
                    <div class="subtitle">Sistema de Nivelación - Universidad</div>
                </div>

                <div class="info-section">
                    <div class="info-grid">
                        <div><strong>Período:</strong> ${((s=F.value)==null?void 0:s.nombre)||"No seleccionado"}</div>
                        <div><strong>Programa:</strong> ${((o=T.value)==null?void 0:o.programa)||"Todos los programas"}</div>
                        <div><strong>Fecha de generación:</strong> ${j.value}</div>
                        <div><strong>Total de registros:</strong> ${A.value}</div>
                    </div>
                </div>

                <div class="stats">
                    <div class="stat-card">
                        <div class="stat-number">${i.value.competencias_sin_docente||0}</div>
                        <div class="stat-label">Competencias sin docente</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">${i.value.docentes_faltan_notas||0}</div>
                        <div class="stat-label">Docentes faltan notas</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">${i.value.total_estudiantes_sin_nota||0}</div>
                        <div class="stat-label">Estudiantes sin nota</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">${i.value.programas_afectados||0}</div>
                        <div class="stat-label">Programas afectados</div>
                    </div>
                </div>

                <div class="section">
                    <div class="section-title">Competencias que Necesitan Docente (${m.value.length})</div>
                    <div class="section-content">
                        ${m.value.length===0?'<p style="text-align: center; color: #666;">No hay competencias sin docente asignado</p>':m.value.map(t=>`
                                <div class="item">
                                    <div class="item-title">${t.competencia_nombre}</div>
                                    <div class="item-details">${t.programa} - ${t.filial}</div>
                                    <div class="item-stats">
                                        <div class="stat-row">
                                            <span>${t.estudiantes_afectados} estudiantes afectados</span>
                                            <span>${t.estudiantes_sin_nota} sin nota</span>
                                        </div>
                                    </div>
                                </div>
                            `).join("")}
                    </div>
                </div>

                <div class="section">
                    <div class="section-title">Docentes que Faltan Subir Notas (${_.value.length})</div>
                    <div class="section-content">
                        ${_.value.length===0?'<p style="text-align: center; color: #666;">Todos los docentes han subido sus notas</p>':_.value.map(t=>`
                                <div class="item">
                                    <div class="item-title">${t.docente_nombre}</div>
                                    <div class="item-details">${t.competencia_nombre} - ${t.programa}</div>
                                    <div class="item-stats">
                                        <div class="stat-row">
                                            <span style="color: red; font-weight: bold;">📝 ${t.estudiantes_sin_nota} estudiantes sin nota</span>
                                            <span style="color: red; font-weight: bold;">${t.porcentaje_sin_nota}%</span>
                                        </div>
                                        <div class="stat-row">
                                            <span style="color: green;">✅ ${t.estudiantes_con_nota||0} estudiantes con nota </span>
                                        </div>
                                        <div class="stat-row" style="border-top: 1px solid #eee; padding-top: 5px; margin-top: 5px;">
                                            <span style="color: blue; font-weight: bold;">📊 Total: ${t.total_estudiantes} estudiantes</span>
                                        </div>
                                    </div>
                                </div>
                            `).join("")}
                    </div>
                </div>
            </body>
            </html>
        `,h=window.open("","_blank");h.document.write(p),h.document.close(),setTimeout(()=>{h.print()},500)}catch(p){console.error("Error generando PDF:",p),alert("Error al generar el PDF. Por favor, intente nuevamente.")}finally{D.value=!1}},q=()=>{if(!l.value)return;const s=new URLSearchParams;s.append("periodo",l.value),v.value&&s.append("programa",v.value),window.open(`monitoreo-docentes/competencias-sin-docente?${s.toString()}`,"_blank")},I=()=>{if(!l.value)return;const s=new URLSearchParams;s.append("periodo",l.value),v.value&&s.append("programa",v.value),window.open(`monitoreo-docentes/docentes-faltan-notas?${s.toString()}`,"_blank")};return B(async()=>{await M(),await R(),setTimeout(()=>{k()},100)}),(s,o)=>(n(),G(Z,null,{default:S(()=>{var p,h;return[x(u(H),{title:"Monitoreo de Docentes"}),e("div",K,[Q,e("div",W,[x(u(z),{modelValue:l.value,"onUpdate:modelValue":o[0]||(o[0]=t=>l.value=t),options:c.value,optionLabel:"nombre",optionValue:"id_periodo",placeholder:"Seleccionar Periodo",class:"w-full",onChange:k,showClear:"",filter:""},null,8,["modelValue","options"]),x(u(z),{modelValue:v.value,"onUpdate:modelValue":o[1]||(o[1]=t=>v.value=t),options:b.value,optionLabel:"programa",optionValue:"id",placeholder:"Seleccionar Programa",class:"w-full",onChange:k,showClear:"",filter:""},{value:S(t=>[t.value?(n(),d("div",X,[e("span",null,a(L(t.value)),1)])):(n(),d("span",ee,a(t.placeholder),1))]),option:S(t=>[e("div",te,[e("span",null,a(t.option.programa),1),e("span",se,a(t.option.filial),1)])]),_:1},8,["modelValue","options"]),e("div",ae,[x(u(N),{label:"Generar PDF",icon:"pi pi-file-pdf",onClick:U,class:"p-button-danger",disabled:!l.value||!i.value,loading:D.value},null,8,["disabled","loading"]),x(u(N),{label:"Competencias sin docente",onClick:q,size:"small",class:"p-button-info",disabled:!l.value},null,8,["disabled"]),x(u(N),{label:"Docentes faltan notas",onClick:I,size:"small",class:"p-button-warning",disabled:!l.value},null,8,["disabled"])])]),f.value?(n(),d("div",oe,de)):i.value?(n(),d("div",le,[e("div",re,[e("div",ce,a(i.value.competencias_sin_docente||0),1),pe]),e("div",ue,[e("div",ve,a(i.value.docentes_faltan_notas||0),1),_e]),e("div",ge,[e("div",me,a(i.value.total_estudiantes_sin_nota||0),1),be]),e("div",fe,[e("div",he,a(i.value.programas_afectados||0),1),xe])])):f.value?C("",!0):(n(),d("div",ye," No hay datos para mostrar. Selecciona un período. ")),!f.value&&i.value?(n(),d("div",we,[e("div",$e,[e("div",De,[ke,e("span",Se,a(m.value.length),1)]),e("div",Pe,[m.value.length===0?(n(),d("div",Ce," No hay competencias sin docente asignado ")):(n(),d("div",Ne,[(n(!0),d(V,null,E(m.value,t=>(n(),d("div",{key:t.competencia_id,class:"border-b pb-2 last:border-b-0"},[e("div",Fe,a(t.competencia_nombre),1),e("div",Te,a(t.programa)+" - "+a(t.filial),1),e("div",je,[e("div",Ae,a(t.estudiantes_afectados)+" estudiantes afectados ",1),e("div",Ve,a(t.estudiantes_sin_nota)+" sin nota ",1)])]))),128))]))])]),e("div",Ee,[e("div",ze,[Le,e("span",Me,a(_.value.length),1)]),e("div",Re,[_.value.length===0?(n(),d("div",Ue," Todos los docentes han subido sus notas ")):(n(),d("div",qe,[(n(!0),d(V,null,E(_.value,t=>(n(),d("div",{key:t.docente_id,class:"border-b pb-2 last:border-b-0"},[e("div",Ie,a(t.docente_nombre),1),e("div",Be,a(t.competencia_nombre)+" - "+a(t.programa),1),e("div",Ge,[e("div",He,[e("span",Oe," 📝 "+a(t.estudiantes_sin_nota)+" estudiantes sin nota ",1),e("span",Ye,a(t.porcentaje_sin_nota)+"% ",1)]),e("div",Ze,[e("span",Je," ✅ "+a(t.estudiantes_con_nota||0)+" estudiantes con nota ",1)]),e("div",Ke,[e("span",Qe," 📊 Total: "+a(t.total_estudiantes)+" estudiantes ",1)])])]))),128))]))])])])):C("",!0),!f.value&&i.value?(n(),d("div",We,[Xe,e("div",et,[e("div",null,[tt,$(" "+a(((p=u(F))==null?void 0:p.nombre)||"No seleccionado"),1)]),e("div",null,[st,$(" "+a(((h=u(T))==null?void 0:h.programa)||"Todos los programas"),1)]),e("div",null,[at,$(" "+a(u(j)),1)]),e("div",null,[ot,$(" "+a(u(A)),1)])])])):C("",!0)])]}),_:1}))}},vt=J(nt,[["__scopeId","data-v-c00f3c5a"]]);export{vt as default};
