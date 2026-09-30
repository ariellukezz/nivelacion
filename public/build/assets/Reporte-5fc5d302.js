import{r as $,a5 as se,o as xe,j as ae,w as V,a as Y,e as u,b as n,d as a,Z as Ce,f as e,t,Y as ke,g as G,c as p,i as y,F as U,v as T,p as $e,q as Ee}from"./app-5325b6d4.js";import{A as Se}from"./LayoutSupervisor-b0ec7d8b.js";import{a as J}from"./xlsx-ed97fc94.js";import{s as b}from"./index.esm-bddf61b8.js";import{s as L}from"./checkbox.esm-4608bfdd.js";import{s as S}from"./column.esm-434bfdc1.js";import{s as Ae}from"./datatable.esm-57f950dc.js";import{a as ne}from"./TopMenu-eacd8f79.js";import{s as x}from"./dropdown.esm-ce50f219.js";import{s as D}from"./inputtext.esm-308c096b.js";import{s as te}from"./tag.esm-5cddff58.js";import{s as B}from"./textarea.esm-ce328df5.js";import{_ as Ne}from"./_plugin-vue_export-helper-c27b6911.js";import"./ResponsiveNavLink-4fc6cb3d.js";import"./inputnumber.esm-2f8ecc0d.js";import"./overlayeventbus.esm-5bdb4709.js";import"./password.esm-17c8ad97.js";const s=w=>($e("data-v-39602838"),w=w(),Ee(),w),Ie={class:"reporte-page"},Pe={class:"hero-report"},De=s(()=>e("div",null,[e("span",{class:"hero-kicker"},"SERVICIO PSICOPEDAGÓGICO"),e("h1",null,"Reporte de atenciones"),e("p",null,"Consulta de sesiones, profesionales responsables, seguimiento y evidencias registradas.")],-1)),Oe={class:"hero-actions"},Ue={class:"stats-grid"},Te={class:"stat-card stat-blue"},Fe=s(()=>e("div",{class:"stat-icon"},[e("i",{class:"pi pi-users"})],-1)),qe=s(()=>e("small",null,"Estudiantes atendidos",-1)),je={class:"stat-card stat-indigo"},Re=s(()=>e("div",{class:"stat-icon"},[e("i",{class:"pi pi-list-check"})],-1)),ze=s(()=>e("small",null,"Atenciones",-1)),Le={class:"stat-card stat-green"},Be=s(()=>e("div",{class:"stat-icon"},[e("i",{class:"pi pi-building"})],-1)),Ge=s(()=>e("small",null,"Presenciales",-1)),Me={class:"stat-card stat-cyan"},He=s(()=>e("div",{class:"stat-icon"},[e("i",{class:"pi pi-desktop"})],-1)),We=s(()=>e("small",null,"Virtuales",-1)),Ke={class:"stat-card stat-orange"},Ye=s(()=>e("div",{class:"stat-icon"},[e("i",{class:"pi pi-history"})],-1)),Je=s(()=>e("small",null,"Seguimientos",-1)),Ze={class:"stat-card stat-red"},Qe=s(()=>e("div",{class:"stat-icon"},[e("i",{class:"pi pi-share-alt"})],-1)),Xe=s(()=>e("small",null,"Derivaciones",-1)),ei={class:"stat-card stat-purple"},ii=s(()=>e("div",{class:"stat-icon"},[e("i",{class:"pi pi-image"})],-1)),li=s(()=>e("small",null,"Con evidencia",-1)),oi={class:"card filtros-card"},si={class:"section-title"},ai=s(()=>e("div",null,[e("h2",null,"Filtros de búsqueda"),e("p",null,"Puede combinar varios filtros.")],-1)),ni={class:"filter-actions"},ti={class:"grid"},ci={class:"field col-12 md:col-3"},di=s(()=>e("label",null,"Semestre académico",-1)),ri={class:"field col-12 md:col-3"},ui=s(()=>e("label",null,"Profesional responsable",-1)),pi={class:"field col-12 md:col-3"},mi=s(()=>e("label",null,"Facultad",-1)),vi={class:"field col-12 md:col-3"},_i=s(()=>e("label",null,"Escuela Profesional",-1)),gi={class:"field col-12 md:col-3"},hi=s(()=>e("label",null,"Buscar estudiante",-1)),fi={class:"field col-6 md:col-2"},bi=s(()=>e("label",null,"Sexo",-1)),yi={class:"field col-6 md:col-2"},wi=s(()=>e("label",null,"Ciclo",-1)),Vi={class:"field col-12 md:col-2"},xi=s(()=>e("label",null,"Tipo atención",-1)),Ci={class:"field col-12 md:col-3"},ki=s(()=>e("label",null,"Seguimiento",-1)),$i={class:"field col-12 md:col-4"},Ei=s(()=>e("label",null,"Condición académica",-1)),Si={class:"field col-12 md:col-4"},Ai=s(()=>e("label",null,"Presunción diagnóstica",-1)),Ni={class:"field col-12 md:col-4"},Ii=s(()=>e("label",null,"Problema académico",-1)),Pi={class:"card tabla-card"},Di={class:"table-header"},Oi=s(()=>e("h2",null,"Atenciones registradas",-1)),Ui={class:"student-cell"},Ti={class:"session-chip"},Fi={key:0,class:"evidence-cell"},qi=["src","onClick"],ji={key:1,class:"sin-evidencia"},Ri={class:"action-buttons"},zi={key:0,class:"pagination"},Li={key:0,class:"detail-wrap"},Bi={class:"detail-banner"},Gi={class:"detail-date"},Mi={class:"detail-grid"},Hi={class:"detail-item"},Wi=s(()=>e("label",null,"Profesional responsable",-1)),Ki={class:"detail-item"},Yi=s(()=>e("label",null,"Semestre académico",-1)),Ji={class:"detail-item"},Zi=s(()=>e("label",null,"Facultad",-1)),Qi={class:"detail-item"},Xi=s(()=>e("label",null,"Escuela Profesional",-1)),el={class:"detail-item"},il=s(()=>e("label",null,"Edad / Sexo",-1)),ll={class:"detail-item"},ol=s(()=>e("label",null,"Ciclo",-1)),sl={class:"detail-item"},al=s(()=>e("label",null,"Celular",-1)),nl={class:"detail-item"},tl=s(()=>e("label",null,"Tipo de atención",-1)),cl={class:"detail-section"},dl=s(()=>e("h4",null,"Condición académica",-1)),rl={key:0,class:"chips"},ul={key:1},pl={class:"detail-section"},ml=s(()=>e("h4",null,"Discapacidad",-1)),vl={class:"detail-section"},_l=s(()=>e("h4",null,"Presunción diagnóstica",-1)),gl={key:0,class:"chips"},hl={key:1,class:"mt-2"},fl=s(()=>e("strong",null,"Otro:",-1)),bl={class:"detail-section"},yl=s(()=>e("h4",null,"Problemas académicos",-1)),wl={key:0,class:"chips"},Vl={key:1,class:"mt-2"},xl=s(()=>e("strong",null,"Otro:",-1)),Cl={key:0,class:"detail-section"},kl=s(()=>e("h4",null,"Observaciones",-1)),$l={class:"preserve"},El={key:1,class:"detail-section"},Sl=s(()=>e("h4",null,"Derivación",-1)),Al={class:"preserve"},Nl={class:"detail-section"},Il=s(()=>e("h4",null,"Seguimiento",-1)),Pl={key:0,class:"preserve"},Dl={key:2,class:"detail-section"},Ol=s(()=>e("h4",null,"Encuesta de satisfacción",-1)),Ul={key:3,class:"detail-section"},Tl={class:"evidence-title"},Fl=s(()=>e("h4",null,"Evidencia",-1)),ql=["src"],jl={key:0,class:"edit-wrap"},Rl={class:"edit-student-banner"},zl=s(()=>e("div",{class:"edit-readonly-note"},[e("i",{class:"pi pi-lock"}),G(" Datos del estudiante no editables aquí ")],-1)),Ll={class:"grid"},Bl={class:"field col-12 md:col-4"},Gl=s(()=>e("label",null,"Profesional responsable *",-1)),Ml={class:"field col-12 md:col-4"},Hl=s(()=>e("label",null,"Fecha de atención *",-1)),Wl={class:"field col-12 md:col-4"},Kl=s(()=>e("label",null,"Semestre académico *",-1)),Yl={class:"field col-12 md:col-4"},Jl=s(()=>e("label",null,"Facultad",-1)),Zl={class:"field col-12 md:col-4"},Ql=s(()=>e("label",null,"Escuela Profesional",-1)),Xl={class:"field col-12 md:col-2"},eo=s(()=>e("label",null,"Ciclo",-1)),io={class:"field col-12 md:col-2"},lo=s(()=>e("label",null,"Celular",-1)),oo={class:"edit-section"},so=s(()=>e("h4",null,"Condición académica",-1)),ao={class:"edit-options"},no={class:"edit-section"},to=s(()=>e("h4",null,"Discapacidad",-1)),co={class:"edit-section"},ro=s(()=>e("h4",null,"Presunción diagnóstica",-1)),uo={class:"edit-options"},po={key:0,class:"mt-3"},mo=s(()=>e("label",{class:"edit-field-label"}," Especifique otro problema psicológico ",-1)),vo={class:"edit-section"},_o=s(()=>e("h4",null,"Problemas académicos",-1)),go={class:"edit-options"},ho={key:0,class:"mt-3"},fo=s(()=>e("label",{class:"edit-field-label"}," Especifique otro problema académico ",-1)),bo={class:"grid"},yo={class:"field col-12 md:col-6"},wo=s(()=>e("label",null,"Tipo de atención *",-1)),Vo={class:"field col-12 md:col-6"},xo=s(()=>e("label",null,"Encuesta de satisfacción",-1)),Co={class:"field col-12"},ko=s(()=>e("label",null,"Observaciones",-1)),$o={class:"field col-12 md:col-6"},Eo=s(()=>e("label",null,"Derivación",-1)),So={class:"field col-12 md:col-6"},Ao=s(()=>e("label",null,"Seguimiento",-1)),No={class:"edit-option mb-2"},Io=s(()=>e("span",null,"Requiere seguimiento",-1)),Po={class:"edit-section"},Do=s(()=>e("h4",null,"Evidencia",-1)),Oo={key:0,class:"current-evidence"},Uo=s(()=>e("i",{class:"pi pi-paperclip"},null,-1)),To={class:"grid mt-2"},Fo={class:"field col-12 md:col-7"},qo=s(()=>e("label",null,"Reemplazar evidencia",-1)),jo=s(()=>e("small",{class:"edit-help"}," JPG, PNG o PDF. Máximo 8 MB. ",-1)),Ro={key:0,class:"field col-12 md:col-5"},zo=s(()=>e("label",null,"Archivo actual",-1)),Lo={class:"edit-option remove-file"},Bo=s(()=>e("span",null,"Eliminar evidencia actual",-1)),Go={key:0,class:"evidence-modal"},Mo=["src"],Ho=["src"],Wo={__name:"Reporte",props:{profesionales:{type:Array,default:()=>[]},semestres:{type:Array,default:()=>[]},facultades:{type:Array,default:()=>[]},escuelas:{type:Array,default:()=>[]},condiciones:{type:Array,default:()=>[]},diagnosticos:{type:Array,default:()=>[]},problemasAcademicos:{type:Array,default:()=>[]}},setup(w){const M=$(!1),Z=$(!1),Q=$(25),I=se({estudiantes:0,atenciones:0,presenciales:0,virtuales:0,seguimientos:0,derivaciones:0,con_evidencia:0}),g=$({data:[],total:0,from:0,to:0,current_page:1,last_page:1}),v=se({semestre:null,id_profesional:null,facultad:null,escuela:null,buscar:"",sexo:null,ciclo:"",tipo_atencion:null,seguimiento:null,condicion:null,diagnostico:null,problema_academico:null}),re=["Masculino","Femenino","Otro"],ce=[{label:"Presencial",value:"PRESENCIAL"},{label:"Virtual",value:"VIRTUAL"}],ue=[{label:"Sí",value:"SI"},{label:"No",value:"NO"}],r=$(null),X=$(!1),O=$(null),H=$(!1),F=$(!1),ee=$(!1),ie=$(null),C=$(null),pe=[{label:"No",value:"NO"},{label:"Sí, con carnet CONADIS",value:"SI_CONADIS"},{label:"Sí, sin carnet CONADIS",value:"SI_SIN_CONADIS"}],me=["Muy satisfecho","Satisfecho","Regular","Insatisfecho"],c=se({id:null,id_profesional:null,fecha_atencion:"",semestre_academico:"",celular:"",condicion_academica:[],discapacidad:"NO",presuncion_diagnostica:[],otro_diagnostico:"",problemas_academicos:[],otro_problema_academico:"",observaciones:"",tipo_atencion:null,satisfaccion:null,derivacion:"",requiere_seguimiento:!1,seguimiento:"",evidencia:null,eliminar_evidencia:!1}),k=async(o=1)=>{M.value=!0;try{const{data:l}=await Y.get("/supervisor/servicio-psicopedagogico/data",{params:{...v,page:o,per_page:Q.value}});Object.assign(I,l.resumen||{}),g.value=l.datos||g.value}catch(l){console.error("Error cargando reporte psicopedagógico:",l)}finally{M.value=!1}},ve=()=>{Object.assign(v,{semestre:null,id_profesional:null,facultad:null,escuela:null,buscar:"",sexo:null,ciclo:"",tipo_atencion:null,seguimiento:null,condicion:null,diagnostico:null,problema_academico:null}),k(1)},W=o=>{if(!o)return"-";const l=String(o).slice(0,10).split("-");return l.length!==3?o:`${l[2]}/${l[1]}/${l[0]}`},le=o=>o==="SI_CONADIS"?"Sí, con carnet CONADIS":o==="SI_SIN_CONADIS"?"Sí, sin carnet CONADIS":"No",K=o=>["jpg","jpeg","png","webp"].includes(String((o==null?void 0:o.evidencia_extension)||"").toLowerCase()),q=o=>`/supervisor/servicio-psicopedagogico/evidencia/${o}`,_e=o=>{r.value=o,X.value=!0},j=o=>{O.value=o,H.value=!0},de=o=>{o&&window.open(`/supervisor/servicio-psicopedagogico/evidencia/${o}/descargar`,"_blank")},ge=o=>{C.value=o,Object.assign(c,{id:o.id,id_profesional:o.id_profesional,fecha_atencion:String(o.fecha_atencion||"").slice(0,10),semestre_academico:o.semestre_academico||"",celular:o.celular||"",condicion_academica:Array.isArray(o.condicion_academica)?[...o.condicion_academica]:[],discapacidad:o.discapacidad||"NO",presuncion_diagnostica:Array.isArray(o.presuncion_diagnostica)?[...o.presuncion_diagnostica]:[],otro_diagnostico:o.otro_diagnostico||"",problemas_academicos:Array.isArray(o.problemas_academicos)?[...o.problemas_academicos]:[],otro_problema_academico:o.otro_problema_academico||"",observaciones:o.observaciones||"",tipo_atencion:o.tipo_atencion||null,satisfaccion:o.satisfaccion||null,derivacion:o.derivacion||"",requiere_seguimiento:!!o.requiere_seguimiento,seguimiento:o.seguimiento||"",evidencia:null,eliminar_evidencia:!1}),F.value=!0},he=o=>{var l;c.evidencia=((l=o.target.files)==null?void 0:l[0])||null,c.evidencia&&(c.eliminar_evidencia=!1)},fe=async()=>{var o,l,i,m,E,_;if(c.id){if(!c.id_profesional){window.alert("Seleccione el profesional responsable.");return}if(!c.fecha_atencion){window.alert("Ingrese la fecha de atención.");return}if(!c.tipo_atencion){window.alert("Seleccione el tipo de atención.");return}ee.value=!0;try{const f=new FormData,P=(A,d)=>{if(Array.isArray(d)){d.forEach(z=>{f.append(`${A}[]`,z)});return}if(typeof d=="boolean"){f.append(A,d?"1":"0");return}if(A==="evidencia"){d&&f.append(A,d);return}A!=="id"&&f.append(A,d??"")};Object.entries(c).forEach(([A,d])=>{P(A,d)});const{data:N}=await Y.post(`/supervisor/servicio-psicopedagogico/${c.id}/actualizar`,f,{headers:{"Content-Type":"multipart/form-data"}});window.alert((N==null?void 0:N.mensaje)||"La atención fue actualizada correctamente."),F.value=!1,C.value=null,await k(g.value.current_page||1)}catch(f){console.error("Error actualizando atención:",f);const P=(l=(o=f==null?void 0:f.response)==null?void 0:o.data)==null?void 0:l.errors;if(P){const N=(m=(i=Object.values(P))==null?void 0:i[0])==null?void 0:m[0];window.alert(N||"Revise los datos ingresados.")}else window.alert(((_=(E=f==null?void 0:f.response)==null?void 0:E.data)==null?void 0:_.message)||"No se pudo actualizar la atención.")}finally{ee.value=!1}}},be=async o=>{var i,m,E;if(window.confirm(`¿Está seguro de eliminar la sesión N.º ${o.numero_sesion} de ${o.estudiante}?

Esta acción eliminará definitivamente la atención y su evidencia adjunta, si existe.`)){ie.value=o.id;try{const _=await Y.delete(`/supervisor/servicio-psicopedagogico/${o.id}`);window.alert(((i=_==null?void 0:_.data)==null?void 0:i.mensaje)||"La atención fue eliminada correctamente.");const f=Number(g.value.current_page||1),N=(Array.isArray(g.value.data)?g.value.data.length:0)===1&&f>1?f-1:f;await k(N)}catch(_){console.error("Error eliminando atención:",_),window.alert(((E=(m=_==null?void 0:_.response)==null?void 0:m.data)==null?void 0:E.message)||"No se pudo eliminar la atención.")}finally{ie.value=null}}},h=o=>String(o??"").replaceAll("&","&amp;").replaceAll("<","&lt;").replaceAll(">","&gt;").replaceAll('"',"&quot;").replaceAll("'","&#039;"),R=o=>o?h(o).replace(/\n/g,"<br>"):"-",oe=o=>!Array.isArray(o)||!o.length?'<span class="vacio">-</span>':`
    <ul>
      ${o.map(l=>`<li>${h(l)}</li>`).join("")}
    </ul>
  `,ye=o=>{const l=window.open("","_blank","width=1000,height=820");if(!l){window.alert("El navegador bloqueó la ventana de impresión.");return}const i=o.tiene_evidencia&&K(o)?`
        <div class="section">
          <h3>Evidencia fotográfica</h3>
          <img
            src="${q(o.id)}"
            class="evidencia"
            alt="Evidencia"
          />
        </div>
      `:o.tiene_evidencia?`
          <div class="section">
            <h3>Evidencia</h3>
            <p>
              Documento adjunto:
              ${h(o.evidencia_nombre||"archivo PDF")}
            </p>
          </div>
        `:"",m=`
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
      Sesión N.º ${h(o.numero_sesion)}
    </div>
  </div>

  <div class="grid">
    <div class="item">
      <label>Fecha de atención</label>
      <strong>${h(W(o.fecha_atencion))}</strong>
    </div>

    <div class="item">
      <label>Semestre académico</label>
      <strong>${h(o.semestre_academico)}</strong>
    </div>

    <div class="item">
      <label>Profesional responsable</label>
      <strong>${h(o.profesional)}</strong>
    </div>

    <div class="item">
      <label>Tipo de atención</label>
      <strong>${h(o.tipo_atencion)}</strong>
    </div>

    <div class="item">
      <label>Estudiante</label>
      <strong>${h(o.estudiante)}</strong>
    </div>

    <div class="item">
      <label>Código / DNI</label>
      <span>
        ${h(o.codigo_estudiante)}
        / ${h(o.dni)}
      </span>
    </div>

    <div class="item">
      <label>Facultad</label>
      <span>${h(o.facultad)}</span>
    </div>

    <div class="item">
      <label>Escuela Profesional</label>
      <span>${h(o.escuela_profesional)}</span>
    </div>

    <div class="item">
      <label>Edad / Sexo</label>
      <span>
        ${h(o.edad)} años
        / ${h(o.sexo)}
      </span>
    </div>

    <div class="item">
      <label>Ciclo / Celular</label>
      <span>
        ${h(o.ciclo)}
        / ${h(o.celular||"-")}
      </span>
    </div>
  </div>

  <div class="section">
    <h3>Condición académica</h3>
    ${oe(o.condicion_academica)}
  </div>

  <div class="section">
    <h3>Discapacidad</h3>
    <p>${h(le(o.discapacidad))}</p>
  </div>

  <div class="section">
    <h3>Presunción diagnóstica</h3>
    ${oe(o.presuncion_diagnostica)}
    ${o.otro_diagnostico?`<p><strong>Otro:</strong> ${R(o.otro_diagnostico)}</p>`:""}
  </div>

  <div class="section">
    <h3>Problemas académicos</h3>
    ${oe(o.problemas_academicos)}
    ${o.otro_problema_academico?`<p><strong>Otro:</strong> ${R(o.otro_problema_academico)}</p>`:""}
  </div>

  <div class="section">
    <h3>Observaciones</h3>
    <p>${R(o.observaciones)}</p>
  </div>

  <div class="section">
    <h3>Derivación</h3>
    <p>${R(o.derivacion)}</p>
  </div>

  <div class="section">
    <h3>Seguimiento</h3>
    <p>
      <strong>
        ${o.requiere_seguimiento?"Requiere seguimiento":"No requiere seguimiento"}
      </strong>
    </p>
    ${o.seguimiento?`<p>${R(o.seguimiento)}</p>`:""}
  </div>

  <div class="section">
    <h3>Encuesta de satisfacción</h3>
    <p>${h(o.satisfaccion||"-")}</p>
  </div>

  ${i}

  <div class="signatures">
    <div class="firma">
      ${h(o.profesional)}
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
  `;l.document.open(),l.document.write(m),l.document.close()},we=async()=>{var o,l;Z.value=!0;try{const i=[];let m=1,E=1;do{const{data:d}=await Y.get("/supervisor/servicio-psicopedagogico/data",{params:{...v,page:m,per_page:100}}),z=(d==null?void 0:d.datos)||{},Ve=Array.isArray(z.data)?z.data:[];i.push(...Ve),E=Number(z.last_page||1),m++}while(m<=E);if(!i.length){window.alert("No existen registros para exportar con los filtros seleccionados.");return}const _=i.map(d=>({Fecha:W(d.fecha_atencion),"Semestre académico":d.semestre_academico||"","Profesional responsable":d.profesional||"","N.º sesión":d.numero_sesion||"","Código estudiante":d.codigo_estudiante||"",DNI:d.dni||"",Estudiante:d.estudiante||"",Edad:d.edad??"",Sexo:d.sexo||"",Celular:d.celular||"",Facultad:d.facultad||"","Escuela Profesional":d.escuela_profesional||"",Ciclo:d.ciclo||"","Condición académica":Array.isArray(d.condicion_academica)?d.condicion_academica.join(" | "):"",Discapacidad:le(d.discapacidad),"Presunción diagnóstica":Array.isArray(d.presuncion_diagnostica)?d.presuncion_diagnostica.join(" | "):"","Otro diagnóstico":d.otro_diagnostico||"","Problemas académicos":Array.isArray(d.problemas_academicos)?d.problemas_academicos.join(" | "):"","Otro problema académico":d.otro_problema_academico||"",Observaciones:d.observaciones||"","Tipo de atención":d.tipo_atencion||"","Encuesta de satisfacción":d.satisfaccion||"",Derivación:d.derivacion||"","Requiere seguimiento":d.requiere_seguimiento?"Sí":"No",Seguimiento:d.seguimiento||"","Tiene evidencia":d.tiene_evidencia?"Sí":"No","Nombre evidencia":d.evidencia_nombre||""})),f=J.utils.json_to_sheet(_);f["!cols"]=[{wch:12},{wch:18},{wch:30},{wch:10},{wch:16},{wch:13},{wch:38},{wch:8},{wch:12},{wch:15},{wch:35},{wch:45},{wch:10},{wch:50},{wch:25},{wch:60},{wch:40},{wch:60},{wch:40},{wch:60},{wch:18},{wch:25},{wch:50},{wch:22},{wch:55},{wch:18},{wch:35}];const P=J.utils.book_new();J.utils.book_append_sheet(P,f,"Atenciones");const N=new Date().toISOString().slice(0,10),A=v.semestre||"TODOS";J.writeFile(P,`reporte_psicopedagogico_${A}_${N}.xlsx`)}catch(i){console.error("Error exportando Excel:",i),window.alert(((l=(o=i==null?void 0:i.response)==null?void 0:o.data)==null?void 0:l.message)||"No se pudo generar el archivo Excel.")}finally{Z.value=!1}};return xe(()=>k(1)),(o,l)=>(u(),ae(Se,null,{default:V(()=>[n(a(Ce),{title:"Reporte Servicio Psicopedagógico"}),e("div",Ie,[e("section",Pe,[De,e("div",Oe,[n(a(b),{label:"Exportar Excel (todos)",icon:"pi pi-file-excel",severity:"success",loading:Z.value,onClick:we},null,8,["loading"]),n(a(b),{label:"Actualizar",icon:"pi pi-refresh",severity:"secondary",outlined:"",loading:M.value,onClick:l[0]||(l[0]=i=>k(1))},null,8,["loading"])])]),e("section",Ue,[e("article",Te,[Fe,e("div",null,[qe,e("strong",null,t(I.estudiantes),1)])]),e("article",je,[Re,e("div",null,[ze,e("strong",null,t(I.atenciones),1)])]),e("article",Le,[Be,e("div",null,[Ge,e("strong",null,t(I.presenciales),1)])]),e("article",Me,[He,e("div",null,[We,e("strong",null,t(I.virtuales),1)])]),e("article",Ke,[Ye,e("div",null,[Je,e("strong",null,t(I.seguimientos),1)])]),e("article",Ze,[Qe,e("div",null,[Xe,e("strong",null,t(I.derivaciones),1)])]),e("article",ei,[ii,e("div",null,[li,e("strong",null,t(I.con_evidencia),1)])])]),e("section",oi,[e("div",si,[ai,e("div",ni,[n(a(b),{label:"Buscar",icon:"pi pi-search",size:"small",onClick:l[1]||(l[1]=i=>k(1))}),n(a(b),{label:"Limpiar",icon:"pi pi-filter-slash",severity:"secondary",outlined:"",size:"small",onClick:ve})])]),e("div",ti,[e("div",ci,[di,n(a(x),{modelValue:v.semestre,"onUpdate:modelValue":l[2]||(l[2]=i=>v.semestre=i),options:w.semestres,showClear:"",class:"w-full",placeholder:"Todos"},null,8,["modelValue","options"])]),e("div",ri,[ui,n(a(x),{modelValue:v.id_profesional,"onUpdate:modelValue":l[3]||(l[3]=i=>v.id_profesional=i),options:w.profesionales,optionLabel:"nombre",optionValue:"id",filter:"",showClear:"",class:"w-full",placeholder:"Todos"},null,8,["modelValue","options"])]),e("div",pi,[mi,n(a(x),{modelValue:v.facultad,"onUpdate:modelValue":l[4]||(l[4]=i=>v.facultad=i),options:w.facultades,filter:"",showClear:"",class:"w-full",placeholder:"Todas"},null,8,["modelValue","options"])]),e("div",vi,[_i,n(a(x),{modelValue:v.escuela,"onUpdate:modelValue":l[5]||(l[5]=i=>v.escuela=i),options:w.escuelas,filter:"",showClear:"",class:"w-full",placeholder:"Todas"},null,8,["modelValue","options"])]),e("div",gi,[hi,n(a(D),{modelValue:v.buscar,"onUpdate:modelValue":l[6]||(l[6]=i=>v.buscar=i),class:"w-full",placeholder:"Código, DNI o nombre",onKeyup:l[7]||(l[7]=ke(i=>k(1),["enter"]))},null,8,["modelValue"])]),e("div",fi,[bi,n(a(x),{modelValue:v.sexo,"onUpdate:modelValue":l[8]||(l[8]=i=>v.sexo=i),options:re,showClear:"",class:"w-full",placeholder:"Todos"},null,8,["modelValue"])]),e("div",yi,[wi,n(a(D),{modelValue:v.ciclo,"onUpdate:modelValue":l[9]||(l[9]=i=>v.ciclo=i),class:"w-full",placeholder:"Ej. 3"},null,8,["modelValue"])]),e("div",Vi,[xi,n(a(x),{modelValue:v.tipo_atencion,"onUpdate:modelValue":l[10]||(l[10]=i=>v.tipo_atencion=i),options:ce,optionLabel:"label",optionValue:"value",showClear:"",class:"w-full",placeholder:"Todos"},null,8,["modelValue"])]),e("div",Ci,[ki,n(a(x),{modelValue:v.seguimiento,"onUpdate:modelValue":l[11]||(l[11]=i=>v.seguimiento=i),options:ue,optionLabel:"label",optionValue:"value",showClear:"",class:"w-full",placeholder:"Todos"},null,8,["modelValue"])]),e("div",$i,[Ei,n(a(x),{modelValue:v.condicion,"onUpdate:modelValue":l[12]||(l[12]=i=>v.condicion=i),options:w.condiciones,filter:"",showClear:"",class:"w-full",placeholder:"Todas"},null,8,["modelValue","options"])]),e("div",Si,[Ai,n(a(x),{modelValue:v.diagnostico,"onUpdate:modelValue":l[13]||(l[13]=i=>v.diagnostico=i),options:w.diagnosticos,filter:"",showClear:"",class:"w-full",placeholder:"Todas"},null,8,["modelValue","options"])]),e("div",Ni,[Ii,n(a(x),{modelValue:v.problema_academico,"onUpdate:modelValue":l[14]||(l[14]=i=>v.problema_academico=i),options:w.problemasAcademicos,filter:"",showClear:"",class:"w-full",placeholder:"Todos"},null,8,["modelValue","options"])])])]),e("section",Pi,[e("div",Di,[e("div",null,[Oi,e("p",null,"Mostrando "+t(g.value.from||0)+" - "+t(g.value.to||0)+" de "+t(g.value.total||0)+" registros.",1)]),n(a(x),{modelValue:Q.value,"onUpdate:modelValue":l[15]||(l[15]=i=>Q.value=i),options:[10,25,50,100],class:"rows-select",onChange:l[16]||(l[16]=i=>k(1))},null,8,["modelValue"])]),n(a(Ae),{value:g.value.data||[],loading:M.value,class:"p-datatable-sm compact-table",responsiveLayout:"scroll",tableStyle:"min-width: 1280px"},{default:V(()=>[n(a(S),{header:"N.º",style:{width:"58px"}},{body:V(({index:i})=>[G(t(Number(g.value.from||1)-1+i+1),1)]),_:1}),n(a(S),{field:"fecha_atencion",header:"Fecha"},{body:V(({data:i})=>[G(t(W(i.fecha_atencion)),1)]),_:1}),n(a(S),{field:"profesional",header:"Profesional",style:{"min-width":"180px"}}),n(a(S),{header:"Estudiante",style:{"min-width":"260px"}},{body:V(({data:i})=>[e("div",Ui,[e("strong",null,t(i.estudiante),1),e("small",null,"Cód. "+t(i.codigo_estudiante)+" · DNI "+t(i.dni),1)])]),_:1}),n(a(S),{field:"escuela_profesional",header:"Escuela",style:{"min-width":"220px"}}),n(a(S),{field:"ciclo",header:"Ciclo"}),n(a(S),{header:"Sesión"},{body:V(({data:i})=>[e("span",Ti,"N.º "+t(i.numero_sesion),1)]),_:1}),n(a(S),{header:"Atención"},{body:V(({data:i})=>[n(a(te),{severity:i.tipo_atencion==="VIRTUAL"?"info":"success",value:i.tipo_atencion==="VIRTUAL"?"Virtual":"Presencial"},null,8,["severity","value"])]),_:1}),n(a(S),{header:"Seguimiento"},{body:V(({data:i})=>[n(a(te),{severity:i.requiere_seguimiento?"warning":"secondary",value:i.requiere_seguimiento?"Sí":"No"},null,8,["severity","value"])]),_:1}),n(a(S),{header:"Evidencia",style:{width:"110px"}},{body:V(({data:i})=>[i.tiene_evidencia?(u(),p("div",Fi,[K(i)?(u(),p("img",{key:0,src:q(i.id),class:"evidence-thumb",alt:"Evidencia",onClick:m=>j(i)},null,8,qi)):(u(),ae(a(b),{key:1,icon:"pi pi-file-pdf",severity:"danger",text:"",rounded:"",title:"Ver archivo",onClick:m=>j(i)},null,8,["onClick"]))])):(u(),p("span",ji,"-"))]),_:1}),n(a(S),{header:"Acciones",style:{width:"190px"}},{body:V(({data:i})=>[e("div",Ri,[n(a(b),{icon:"pi pi-eye",severity:"info",text:"",rounded:"",title:"Ver ficha",onClick:m=>_e(i)},null,8,["onClick"]),n(a(b),{icon:"pi pi-pencil",severity:"warning",text:"",rounded:"",title:"Editar atención",onClick:m=>ge(i)},null,8,["onClick"]),n(a(b),{icon:"pi pi-print",severity:"secondary",text:"",rounded:"",title:"Imprimir ficha",onClick:m=>ye(i)},null,8,["onClick"]),n(a(b),{icon:"pi pi-trash",severity:"danger",text:"",rounded:"",title:"Eliminar atención",loading:ie.value===i.id,onClick:m=>be(i)},null,8,["loading","onClick"])])]),_:1})]),_:1},8,["value","loading"]),Number(g.value.last_page||1)>1?(u(),p("div",zi,[n(a(b),{icon:"pi pi-angle-double-left",text:"",disabled:g.value.current_page<=1,onClick:l[17]||(l[17]=i=>k(1))},null,8,["disabled"]),n(a(b),{icon:"pi pi-angle-left",text:"",disabled:g.value.current_page<=1,onClick:l[18]||(l[18]=i=>k(g.value.current_page-1))},null,8,["disabled"]),e("span",null,"Página "+t(g.value.current_page)+" de "+t(g.value.last_page),1),n(a(b),{icon:"pi pi-angle-right",text:"",disabled:g.value.current_page>=g.value.last_page,onClick:l[19]||(l[19]=i=>k(g.value.current_page+1))},null,8,["disabled"]),n(a(b),{icon:"pi pi-angle-double-right",text:"",disabled:g.value.current_page>=g.value.last_page,onClick:l[20]||(l[20]=i=>k(g.value.last_page))},null,8,["disabled"])])):y("",!0)]),n(a(ne),{visible:X.value,"onUpdate:visible":l[24]||(l[24]=i=>X.value=i),modal:"",header:"Detalle de atención psicopedagógica",style:{width:"920px",maxWidth:"96vw"}},{default:V(()=>{var i,m,E;return[r.value?(u(),p("div",Li,[e("div",Bi,[e("div",null,[e("span",null,"Sesión N.º "+t(r.value.numero_sesion),1),e("h3",null,t(r.value.estudiante),1),e("p",null,t(r.value.codigo_estudiante)+" · DNI "+t(r.value.dni),1)]),e("div",Gi,t(W(r.value.fecha_atencion)),1)]),e("div",Mi,[e("div",Hi,[Wi,e("strong",null,t(r.value.profesional),1)]),e("div",Ki,[Yi,e("strong",null,t(r.value.semestre_academico),1)]),e("div",Ji,[Zi,e("span",null,t(r.value.facultad),1)]),e("div",Qi,[Xi,e("span",null,t(r.value.escuela_profesional),1)]),e("div",el,[il,e("span",null,t(r.value.edad)+" años · "+t(r.value.sexo),1)]),e("div",ll,[ol,e("span",null,t(r.value.ciclo),1)]),e("div",sl,[al,e("span",null,t(r.value.celular||"-"),1)]),e("div",nl,[tl,e("span",null,t(r.value.tipo_atencion),1)])]),e("div",cl,[dl,(i=r.value.condicion_academica)!=null&&i.length?(u(),p("div",rl,[(u(!0),p(U,null,T(r.value.condicion_academica,_=>(u(),p("span",{key:_,class:"chip chip-indigo"},t(_),1))),128))])):(u(),p("span",ul,"-"))]),e("div",pl,[ml,e("p",null,t(le(r.value.discapacidad)),1)]),e("div",vl,[_l,(m=r.value.presuncion_diagnostica)!=null&&m.length?(u(),p("div",gl,[(u(!0),p(U,null,T(r.value.presuncion_diagnostica,_=>(u(),p("span",{key:_,class:"chip chip-blue"},t(_),1))),128))])):y("",!0),r.value.otro_diagnostico?(u(),p("p",hl,[fl,G(" "+t(r.value.otro_diagnostico),1)])):y("",!0)]),e("div",bl,[yl,(E=r.value.problemas_academicos)!=null&&E.length?(u(),p("div",wl,[(u(!0),p(U,null,T(r.value.problemas_academicos,_=>(u(),p("span",{key:_,class:"chip chip-orange"},t(_),1))),128))])):y("",!0),r.value.otro_problema_academico?(u(),p("p",Vl,[xl,G(" "+t(r.value.otro_problema_academico),1)])):y("",!0)]),r.value.observaciones?(u(),p("div",Cl,[kl,e("p",$l,t(r.value.observaciones),1)])):y("",!0),r.value.derivacion?(u(),p("div",El,[Sl,e("p",Al,t(r.value.derivacion),1)])):y("",!0),e("div",Nl,[Il,e("p",null,[n(a(te),{severity:r.value.requiere_seguimiento?"warning":"secondary",value:r.value.requiere_seguimiento?"Requiere seguimiento":"No requiere seguimiento"},null,8,["severity","value"])]),r.value.seguimiento?(u(),p("p",Pl,t(r.value.seguimiento),1)):y("",!0)]),r.value.satisfaccion?(u(),p("div",Dl,[Ol,e("p",null,t(r.value.satisfaccion),1)])):y("",!0),r.value.tiene_evidencia?(u(),p("div",Ul,[e("div",Tl,[Fl,n(a(b),{label:"Descargar",icon:"pi pi-download",size:"small",outlined:"",onClick:l[21]||(l[21]=_=>de(r.value.id))})]),K(r.value)?(u(),p("img",{key:0,src:q(r.value.id),class:"detail-image",alt:"Evidencia",onClick:l[22]||(l[22]=_=>j(r.value))},null,8,ql)):(u(),ae(a(b),{key:1,label:"Abrir documento PDF",icon:"pi pi-file-pdf",severity:"danger",outlined:"",onClick:l[23]||(l[23]=_=>j(r.value))}))])):y("",!0)])):y("",!0)]}),_:1},8,["visible"]),n(a(ne),{visible:F.value,"onUpdate:visible":l[44]||(l[44]=i=>F.value=i),modal:"",header:"Editar atención psicopedagógica",style:{width:"960px",maxWidth:"97vw"}},{footer:V(()=>[n(a(b),{label:"Cancelar",severity:"secondary",outlined:"",onClick:l[43]||(l[43]=i=>F.value=!1)}),n(a(b),{label:"Guardar cambios",icon:"pi pi-save",loading:ee.value,onClick:fe},null,8,["loading"])]),default:V(()=>[C.value?(u(),p("div",jl,[e("div",Rl,[e("div",null,[e("span",null,"Sesión N.º "+t(C.value.numero_sesion),1),e("h3",null,t(C.value.estudiante),1),e("p",null," Código "+t(C.value.codigo_estudiante)+" · DNI "+t(C.value.dni),1)]),zl]),e("div",Ll,[e("div",Bl,[Gl,n(a(x),{modelValue:c.id_profesional,"onUpdate:modelValue":l[25]||(l[25]=i=>c.id_profesional=i),options:w.profesionales,optionLabel:"nombre",optionValue:"id",filter:"",class:"w-full"},null,8,["modelValue","options"])]),e("div",Ml,[Hl,n(a(D),{modelValue:c.fecha_atencion,"onUpdate:modelValue":l[26]||(l[26]=i=>c.fecha_atencion=i),type:"date",class:"w-full"},null,8,["modelValue"])]),e("div",Wl,[Kl,n(a(D),{modelValue:c.semestre_academico,"onUpdate:modelValue":l[27]||(l[27]=i=>c.semestre_academico=i),class:"w-full"},null,8,["modelValue"])]),e("div",Yl,[Jl,n(a(D),{modelValue:C.value.facultad,class:"w-full edit-readonly",readonly:""},null,8,["modelValue"])]),e("div",Zl,[Ql,n(a(D),{modelValue:C.value.escuela_profesional,class:"w-full edit-readonly",readonly:""},null,8,["modelValue"])]),e("div",Xl,[eo,n(a(D),{modelValue:C.value.ciclo,class:"w-full edit-readonly",readonly:""},null,8,["modelValue"])]),e("div",io,[lo,n(a(D),{modelValue:c.celular,"onUpdate:modelValue":l[28]||(l[28]=i=>c.celular=i),class:"w-full"},null,8,["modelValue"])])]),e("div",oo,[so,e("div",ao,[(u(!0),p(U,null,T(w.condiciones,i=>(u(),p("label",{key:`edit-cond-${i}`,class:"edit-option"},[n(a(L),{modelValue:c.condicion_academica,"onUpdate:modelValue":l[29]||(l[29]=m=>c.condicion_academica=m),value:i},null,8,["modelValue","value"]),e("span",null,t(i),1)]))),128))])]),e("div",no,[to,n(a(x),{modelValue:c.discapacidad,"onUpdate:modelValue":l[30]||(l[30]=i=>c.discapacidad=i),options:pe,optionLabel:"label",optionValue:"value",class:"w-full"},null,8,["modelValue"])]),e("div",co,[ro,e("div",uo,[(u(!0),p(U,null,T(w.diagnosticos,i=>(u(),p("label",{key:`edit-diag-${i}`,class:"edit-option"},[n(a(L),{modelValue:c.presuncion_diagnostica,"onUpdate:modelValue":l[31]||(l[31]=m=>c.presuncion_diagnostica=m),value:i},null,8,["modelValue","value"]),e("span",null,t(i),1)]))),128))]),c.presuncion_diagnostica.includes("Otros problemas psicológicos")?(u(),p("div",po,[mo,n(a(B),{modelValue:c.otro_diagnostico,"onUpdate:modelValue":l[32]||(l[32]=i=>c.otro_diagnostico=i),rows:"2",class:"w-full"},null,8,["modelValue"])])):y("",!0)]),e("div",vo,[_o,e("div",go,[(u(!0),p(U,null,T(w.problemasAcademicos,i=>(u(),p("label",{key:`edit-prob-${i}`,class:"edit-option"},[n(a(L),{modelValue:c.problemas_academicos,"onUpdate:modelValue":l[33]||(l[33]=m=>c.problemas_academicos=m),value:i},null,8,["modelValue","value"]),e("span",null,t(i),1)]))),128))]),c.problemas_academicos.includes("Otros problemas académicos")?(u(),p("div",ho,[fo,n(a(B),{modelValue:c.otro_problema_academico,"onUpdate:modelValue":l[34]||(l[34]=i=>c.otro_problema_academico=i),rows:"2",class:"w-full"},null,8,["modelValue"])])):y("",!0)]),e("div",bo,[e("div",yo,[wo,n(a(x),{modelValue:c.tipo_atencion,"onUpdate:modelValue":l[35]||(l[35]=i=>c.tipo_atencion=i),options:ce,optionLabel:"label",optionValue:"value",class:"w-full"},null,8,["modelValue"])]),e("div",Vo,[xo,n(a(x),{modelValue:c.satisfaccion,"onUpdate:modelValue":l[36]||(l[36]=i=>c.satisfaccion=i),options:me,showClear:"",class:"w-full",placeholder:"Sin respuesta"},null,8,["modelValue"])]),e("div",Co,[ko,n(a(B),{modelValue:c.observaciones,"onUpdate:modelValue":l[37]||(l[37]=i=>c.observaciones=i),rows:"4",class:"w-full"},null,8,["modelValue"])]),e("div",$o,[Eo,n(a(B),{modelValue:c.derivacion,"onUpdate:modelValue":l[38]||(l[38]=i=>c.derivacion=i),rows:"3",class:"w-full"},null,8,["modelValue"])]),e("div",So,[Ao,e("label",No,[n(a(L),{modelValue:c.requiere_seguimiento,"onUpdate:modelValue":l[39]||(l[39]=i=>c.requiere_seguimiento=i),binary:!0},null,8,["modelValue"]),Io]),n(a(B),{modelValue:c.seguimiento,"onUpdate:modelValue":l[40]||(l[40]=i=>c.seguimiento=i),rows:"3",class:"w-full"},null,8,["modelValue"])])]),e("div",Po,[Do,C.value.tiene_evidencia?(u(),p("div",Oo,[e("div",null,[Uo,e("span",null,t(C.value.evidencia_nombre||"El registro tiene una evidencia adjunta"),1)]),n(a(b),{label:"Ver",icon:"pi pi-eye",size:"small",text:"",onClick:l[41]||(l[41]=i=>j(C.value))})])):y("",!0),e("div",To,[e("div",Fo,[qo,e("input",{type:"file",accept:".jpg,.jpeg,.png,.pdf",class:"edit-file",onChange:he},null,32),jo]),C.value.tiene_evidencia?(u(),p("div",Ro,[zo,e("label",Lo,[n(a(L),{modelValue:c.eliminar_evidencia,"onUpdate:modelValue":l[42]||(l[42]=i=>c.eliminar_evidencia=i),binary:!0},null,8,["modelValue"]),Bo])])):y("",!0)])])])):y("",!0)]),_:1},8,["visible"]),n(a(ne),{visible:H.value,"onUpdate:visible":l[47]||(l[47]=i=>H.value=i),modal:"",header:"Evidencia de la atención",style:{width:"980px",maxWidth:"97vw"}},{footer:V(()=>[n(a(b),{label:"Descargar",icon:"pi pi-download",onClick:l[45]||(l[45]=i=>{var m;return de((m=O.value)==null?void 0:m.id)})}),n(a(b),{label:"Cerrar",severity:"secondary",outlined:"",onClick:l[46]||(l[46]=i=>H.value=!1)})]),default:V(()=>[O.value?(u(),p("div",Go,[K(O.value)?(u(),p("img",{key:0,src:q(O.value.id),class:"evidence-large",alt:"Evidencia"},null,8,Mo)):(u(),p("iframe",{key:1,src:q(O.value.id),class:"pdf-frame",title:"Evidencia PDF"},null,8,Ho))])):y("",!0)]),_:1},8,["visible"])])]),_:1}))}},us=Ne(Wo,[["__scopeId","data-v-39602838"]]);export{us as default};
