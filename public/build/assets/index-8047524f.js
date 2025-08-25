import{A as ce}from"./AuthenticatedLayout-5c4a81b3.js";import{r as n,u as pe,j as me,k as x,c as m,a as t,b as l,w as i,F as ve,o as v,Z as fe,e,f as z,t as w,h as f,n as A}from"./app-9957fcc0.js";import{s as h,a as S}from"./index.esm-b6b36824.js";import{s as N}from"./datatable.esm-42a8ea18.js";import{s as d}from"./column.esm-434bfdc1.js";import{s as he,a as Z}from"./TopMenu-5fc87428.js";import{s as U}from"./dropdown.esm-7dc5e2ba.js";import{s as ye,a as _e}from"./confirmpopup.esm-6289b0a2.js";import"./radiobutton.esm-3ea7ad20.js";import"./divider.esm-fc694e07.js";import"./calendar.esm-2805e350.js";import{s as $}from"./tag.esm-0a079832.js";import"./breadcrumb.esm-6a6b986e.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./inputnumber.esm-2a310672.js";import"./basecomponent.esm-a1de724f.js";import"./index.esm-6fffe4db.js";function ge(V,u){u===void 0&&(u={});var C=u.insertAt;if(!(!V||typeof document>"u")){var g=document.head||document.getElementsByTagName("head")[0],c=document.createElement("style");c.type="text/css",C==="top"&&g.firstChild?g.insertBefore(c,g.firstChild):g.appendChild(c),c.styleSheet?c.styleSheet.cssText=V:c.appendChild(document.createTextNode(V))}}var we=`
.p-autocomplete {
    display: inline-flex;
    position: relative;
}
.p-autocomplete-loader {
    position: absolute;
    top: 50%;
    margin-top: -0.5rem;
}
.p-autocomplete-dd .p-autocomplete-input {
    flex: 1 1 auto;
    width: 1%;
}
.p-autocomplete-dd .p-autocomplete-input,
.p-autocomplete-dd .p-autocomplete-multiple-container {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}
.p-autocomplete-dd .p-autocomplete-dropdown {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0px;
}
.p-autocomplete .p-autocomplete-panel {
    min-width: 100%;
}
.p-autocomplete-panel {
    position: absolute;
    overflow: auto;
    top: 0;
    left: 0;
}
.p-autocomplete-items {
    margin: 0;
    padding: 0;
    list-style-type: none;
}
.p-autocomplete-item {
    cursor: pointer;
    white-space: nowrap;
    position: relative;
    overflow: hidden;
}
.p-autocomplete-multiple-container {
    margin: 0;
    padding: 0;
    list-style-type: none;
    cursor: text;
    overflow: hidden;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
}
.p-autocomplete-token {
    cursor: default;
    display: inline-flex;
    align-items: center;
    flex: 0 0 auto;
}
.p-autocomplete-token-icon {
    cursor: pointer;
}
.p-autocomplete-input-token {
    flex: 1 1 auto;
    display: inline-flex;
}
.p-autocomplete-input-token input {
    border: 0 none;
    outline: 0 none;
    background-color: transparent;
    margin: 0;
    padding: 0;
    box-shadow: none;
    border-radius: 0;
    width: 100%;
}
.p-fluid .p-autocomplete {
    display: flex;
}
.p-fluid .p-autocomplete-dd .p-autocomplete-input {
    width: 1%;
}
`;ge(we);const xe={class:"flex mb-0",style:{"justify-content":"space-between","align-items":"center","margin-top":"0px","border-bottom":"solid 1px #cdcdcd9D",height:"50px",background:"white"}},be={class:"flex"},Ve={key:0,class:"flex justify-content-center",style:{"align-items":"center"}},Ce=e("i",{class:"pi pi-angle-right"},null,-1),ke={style:{"max-width":"180px","white-space":"nowrap","text-overflow":"ellipsis",overflow:"hidden"}},je={key:1,class:"flex justify-content-center",style:{"align-items":"center"}},ze=e("i",{class:"pi pi-angle-right"},null,-1),Ae={style:{"max-width":"180px","white-space":"nowrap","text-overflow":"ellipsis",overflow:"hidden"}},Se={key:0},Ne={class:"flex mr-4",style:{"justify-content":"flex-end"}},Ue={class:"p-input-icon-left"},$e=e("i",{class:"pi pi-search"},null,-1),De={key:1},Ee={class:"flex align-items-center",style:{width:"280px","font-size":"0.9rem","white-space":"nowrap","text-overflow":"ellipsis",overflow:"hidden"}},Be={class:"bg-white shadow-xs p-4",style:{height:"calc(100vh - 140px)","font-family":"Arial, Helvetica, sans-serif"}},Me={key:0,class:"card"},Te={key:0},Ge={class:"flex",style:{"justify-content":"space-between"}},Le={class:"flex mb-3",style:{"justify-content":"flex-end"}},Ie={class:"p-input-icon-left"},Pe=e("i",{class:"pi pi-search"},null,-1),Fe={class:"mt-3"},Re={class:"flex",style:{"justify-content":"flex-start"}},qe={class:"flex",style:{"justify-content":"center"}},He={key:0},Xe={key:1},Ze={class:"flex"},Je={class:"mr-2"},Ke={key:1},Oe={class:"flex",style:{"justify-content":"space-between"}},Qe={class:"flex mb-3",style:{"justify-content":"flex-end"}},We={class:"p-input-icon-left"},Ye=e("i",{class:"pi pi-search"},null,-1),et={class:"mt-3"},tt={class:"flex",style:{"justify-content":"center"}},lt={key:0},ot={key:1},at={class:"flex mt-0 mb-3 align-items-center",style:{"justify-content":"flex-end"}},st=e("label",null,"Estado",-1),nt={class:"ml-3"},it={class:"flex",style:{width:"100%","justify-content":"space-between"}},dt={class:"mb-3",style:{width:"68%"}},ut=e("div",null,[e("label",null,"Nombre")],-1),rt={class:"mb-3",style:{width:"28%"}},ct=e("div",null,[e("label",null,"Grupo")],-1),pt={class:"flex align-items-center",style:{"font-size":"0.9rem","white-space":"nowrap","text-overflow":"ellipsis",overflow:"hidden"}},mt={class:"flex",style:{width:"100%","justify-content":"space-between"}},vt={class:"mb-3",style:{width:"100%"}},ft=e("div",null,[e("label",null,"Competencia")],-1),ht={class:"flex align-items-center",style:{width:"400px","font-size":"0.9rem","white-space":"nowrap","text-overflow":"ellipsis",overflow:"hidden"}},yt={class:"flex",style:{width:"100%","justify-content":"space-between"}},_t={class:"mb-3",style:{width:"100%"}},gt=e("div",null,[e("label",null,"Docente")],-1),wt={class:"flex align-items-center",style:{width:"400px","font-size":"0.9rem","white-space":"nowrap","text-overflow":"ellipsis",overflow:"hidden"}},xt={class:"flex",style:{"justify-content":"flex-end"}},bt={class:"flex",style:{width:"100%","justify-content":"flex-end"}},It={__name:"index",setup(V){const u=n(null),C=pe(),g=me(),c=n(null),D=n([]);n("");const M=n([]),p=n(null),E=n([]),J=n([]),K=n(0),T=n(1),_=n(""),k=n(null),O=n([]),y=n(!1),G=n("");n("A");const Q=n([{value:"A",label:"Grupo A"},{value:"B",label:"Grupo B"},{value:"C",label:"Grupo C"}]),L=n([]);n([{label:"Computer"},{label:"Notebook"}]);const I=n([]),W=n(null),P=n([]),F=n([]);n();const b=n(null),r=n({id:null,nombre:"",id_docente:"",grupo:"A",estado:!0}),j=async a=>{let o=await axios.post("get-docentes?page="+T.value,{term:_.value});O.value=o.data.datos.data,K.value=o.data.datos.total},Y=async()=>{let a=await axios.post("get-programas?page="+T.value,{term:""});J.value=a.data.datos.data},ee=async()=>{let a=await axios.post("get-escuelas",{term:""});P.value=a.data.datos.data},te=async()=>{let a=await axios.post("get-competencias?page=",{term:""});E.value=a.data.datos.data},le=async()=>{let a=await axios.post("get-docente-competencia?page=",{term:"",competencia:b.value});I.value=a.data.datos.data,W.value=a.data.datos.data[0].id},R=async()=>{let a=await axios.post("get-cursos?page=",{term:"",competencia:k.value,escuela:u.value.escuela});M.value=a.data.datos.data},oe=async()=>{let a=await axios.post("save-curso",{id:r.value.id,nombre:r.value.nombre,id_competencia:b.value,id_docente:r.value.id_docente,escuela:u.value.escuela,grupo:r.value.grupo,estado:r.value.estado});B(a.data.tipo,a.data.titulo,a.data.mensaje),j(),y.value=!1,limpiar()},q=async()=>{let a=await axios.post("get-detalle-curso?page=",{term:"",curso:p.value.id});F.value=a.data.datos.data},ae=async()=>{let a=await axios.post("asignar-curso-nivelacion",{curso:p.value.id,alumnos:D.value});B(a.data.tipo,a.data.titulo,a.data.mensaje),q(),c.value=!1},se=async a=>{let o=await axios.get("delete-docente/"+a);B(o.data.tipo,o.data.titulo,o.data.mensaje),j()},ne=async()=>{let a=await axios.post("get-alumnos-registro?page=",{term:""});L.value=a.data.datos.data},H=n(!1),ie=async a=>{y.value=!0,H.value=!0,docente.value.id=a.id,docente.value.tipo_doc=a.tipo_doc,docente.value.nro_doc=a.nro_doc,docente.value.nombres=a.nombres,docente.value.primer_apellido=a.paterno,docente.value.segundo_apellido=a.materno,docente.value.celular=a.telefono,docente.value.correo=a.email,docente.value.direccion=a.direccion,docente.value.fecha=a.f_nac,G.value=a.f_nac,docente.value.sexo=a.sexo,a.estado===1?docente.value.estado=!0:docente.value.estado=!1};x(y,(a,o)=>{H.value==!0&&y.value==!1&&(y.value=!1,docente.value.id=null,docente.value.tipo_doc=1,docente.value.nro_doc=null,docente.value.nombres=null,docente.value.primer_apellido=null,docente.value.segundo_apellido=null,docente.value.celular=null,docente.value.correo=null,docente.value.direccion=null,docente.value.fecha=null,G.value=null,docente.value.sexo="M",docente.value.estado=!0)}),x(_,(a,o)=>{j()}),x(b,(a,o)=>{le()}),x(k,(a,o)=>{R()}),x(u,(a,o)=>{R()}),x(p,(a,o)=>{q()});const B=(a,o,s)=>{C.add({severity:a,summary:o,detail:s,life:3e3})},de=(a,o)=>{g.require({target:a.currentTarget,message:"¿Estas seguro de eliminar al docente "+o.nombres+"?",icon:"pi pi-info-circle",acceptClass:"p-button-danger",accept:()=>{se(o.id)},reject:()=>{C.add({severity:"error",summary:"Elimación cancelada",detail:"Se ha cancelado la eliminación del docente "+o.nombres,life:3e3})}})},ue=()=>{u.value=null,p.value=null},re=()=>{p.value=null};return j(),Y(),te(),ee(),ne(),(a,o)=>(v(),m(ve,null,[t(l(fe),{title:"Nivelación"}),t(ce,null,{default:i(()=>[e("div",xe,[e("div",be,[t(l(h),{severity:"secondary",style:{"font-size":"0.9rem"},text:"",onClick:ue},{default:i(()=>[z(" Inicio ")]),_:1}),u.value!==null?(v(),m("div",Ve,[Ce,t(l(h),{severity:"secondary",onClick:re,style:{"font-size":"0.9rem"},text:""},{default:i(()=>[e("div",ke,[e("span",null,w(u.value.escuela),1)])]),_:1})])):f("",!0),p.value!==null?(v(),m("div",je,[ze,t(l(h),{severity:"secondary",style:{"font-size":"0.9rem"},text:""},{default:i(()=>[e("div",Ae,[e("span",null,w(p.value.nombre),1)])]),_:1})])):f("",!0)]),u.value===null?(v(),m("div",Se,[e("div",Ne,[e("span",Ue,[$e,t(l(S),{modelValue:_.value,"onUpdate:modelValue":o[0]||(o[0]=s=>_.value=s),style:{"padding-left":"40px",height:"40px"},placeholder:"Buscar"},null,8,["modelValue"])])])])):f("",!0),u.value!==null&&p.value===null?(v(),m("div",De,[t(l(U),{modelValue:k.value,"onUpdate:modelValue":o[1]||(o[1]=s=>k.value=s),options:E.value,severity:"primary",optionLabel:"label",optionValue:"value",placeholder:"Selecciona una competencia",style:{width:"325px",height:"38px"},class:"w-full md:w-11rem mr-4"},{option:i(s=>[e("div",Ee,[e("div",null,w(s.option.label),1)])]),_:1},8,["modelValue","options"])])):f("",!0)]),e("div",Be,[e("div",null,[u.value===null?(v(),m("div",Me,[t(l(N),{selection:u.value,"onUpdate:selection":o[2]||(o[2]=s=>u.value=s),selectionMode:"single",value:P.value,class:A("p-datatable-sm"),tableStyle:"min-width: 50rem",style:{"font-size":".9rem"},paginator:!0,rows:10,filters:a.filters},{default:i(()=>[t(l(d),{field:"escuela",header:"Escuela"}),t(l(d),{field:"facultad",header:"Facultad"}),t(l(d),{field:"area",header:"Area"})]),_:1},8,["selection","value","filters"])])):f("",!0)]),u.value!==null&&p.value===null?(v(),m("div",Te,[e("div",Ge,[t(l(h),{severity:"primary",onClick:o[3]||(o[3]=s=>y.value=!0),style:{height:"40px"}},{default:i(()=>[z(" Nuevo ")]),_:1}),e("div",null,[e("div",Le,[e("span",Ie,[Pe,t(l(S),{modelValue:_.value,"onUpdate:modelValue":o[4]||(o[4]=s=>_.value=s),style:{"padding-left":"40px",height:"40px"},placeholder:"Buscar"},null,8,["modelValue"])])])])]),e("div",Fe,[t(l(N),{selection:p.value,"onUpdate:selection":o[5]||(o[5]=s=>p.value=s),selectionMode:"single",value:M.value,class:A("p-datatable-sm"),tableStyle:"min-width: 50rem",style:{"font-size":".9rem"},paginator:!0,rows:9},{default:i(()=>[t(l(d),{field:"nombre",header:"Nombre"}),t(l(d),{field:"competencia",header:"Competencia"}),t(l(d),{field:"docente",header:"Docente"},{body:i(({data:s})=>[e("div",Re,[e("div",null,w(s.docente),1)])]),_:1}),t(l(d),{field:"grupo",header:"Grupo"}),t(l(d),{field:"escuela",header:"Escuela Prof."}),t(l(d),{field:"estado",style:{"justify-content":"center",display:"flex"},header:"Estado",width:"70px"},{body:i(({data:s})=>[e("div",qe,[s.estado===1?(v(),m("div",He,[t(l($),{severity:"info",value:"Activo"})])):f("",!0),s.estado===0?(v(),m("div",Xe,[t(l($),{style:{background:"#CDCDCD"},value:"Inactivo"})])):f("",!0)])]),_:1}),t(l(d),{field:"id_programa",header:"Acciones",width:"90px"},{body:i(({data:s})=>[e("div",Ze,[e("div",Je,[t(l(h),{class:"secondary",icon:"pi pi-pencil","aria-label":"Submit",onClick:X=>ie(s),size:"small",style:{width:"25px",height:"25px"}},null,8,["onClick"])]),t(l(h),{icon:"pi pi-trash",severity:"danger","aria-label":"Submit",onClick:X=>de(X,s),size:"small",style:{width:"25px",height:"25px"}},null,8,["onClick"])])]),_:1})]),_:1},8,["selection","value"])])])):f("",!0),u.value!==null&&p.value!==null?(v(),m("div",Ke,[e("div",Oe,[t(l(h),{severity:"primary",onClick:o[6]||(o[6]=s=>c.value=!0),style:{height:"40px"}},{default:i(()=>[z(" Nuevo ")]),_:1}),e("div",null,[e("div",Qe,[e("span",We,[Ye,t(l(S),{modelValue:_.value,"onUpdate:modelValue":o[7]||(o[7]=s=>_.value=s),style:{"padding-left":"40px",height:"40px"},placeholder:"Buscar"},null,8,["modelValue"])])])])]),e("div",et,[t(l(N),{selectionMode:"single",value:F.value,class:A("p-datatable-sm"),tableStyle:"min-width: 50rem",style:{"font-size":".9rem"},paginator:!0,rows:9},{default:i(()=>[t(l(d),{field:"codigo_est",header:"codigo_est"}),t(l(d),{field:"nombres",header:"Nombres"}),t(l(d),{field:"paterno",header:"Paterno"}),t(l(d),{field:"materno",header:"Materno"}),t(l(d),{field:"curso",header:"Curso"}),t(l(d),{field:"nota",header:"Nota"}),t(l(d),{field:"estado",style:{"justify-content":"center",display:"flex"},header:"Condición",width:"70px"},{body:i(({data:s})=>[e("div",tt,[s.nota>=10.5?(v(),m("div",lt,[t(l($),{severity:"info",value:"Aprobado"})])):f("",!0),s.nota<=10.49?(v(),m("div",ot,[t(l($),{severity:"danger",value:"Desprobado"})])):f("",!0)])]),_:1})]),_:1},8,["value"])])])):f("",!0),t(l(he)),t(l(ye)),t(l(Z),{visible:y.value,"onUpdate:visible":o[14]||(o[14]=s=>y.value=s),modal:"",header:"Registrar Docente",style:{width:"500px"}},{footer:i(()=>[e("div",xt,[e("div",null,[t(l(h),{label:"Cancelar",outlined:"",onClick:o[13]||(o[13]=s=>y.value=!1),size:"small"})]),t(l(h),{label:"Guardar",onClick:oe,size:"small"})])]),default:i(()=>[e("div",at,[st,e("div",nt,[t(l(_e),{modelValue:r.value.estado,"onUpdate:modelValue":o[8]||(o[8]=s=>r.value.estado=s)},null,8,["modelValue"])])]),e("div",it,[e("div",dt,[ut,t(l(S),{style:{width:"100%",height:"40px"},type:"text",modelValue:r.value.nombre,"onUpdate:modelValue":o[9]||(o[9]=s=>r.value.nombre=s)},null,8,["modelValue"])]),e("div",rt,[ct,t(l(U),{modelValue:r.value.grupo,"onUpdate:modelValue":o[10]||(o[10]=s=>r.value.grupo=s),options:Q.value,optionLabel:"label",optionValue:"value",placeholder:"Selecciona una competencia",style:{width:"100%"},class:"w-full md:w-11rem"},{option:i(s=>[e("div",pt,[e("div",null,w(s.option.label),1)])]),_:1},8,["modelValue","options"])])]),e("div",mt,[e("div",vt,[ft,t(l(U),{modelValue:b.value,"onUpdate:modelValue":o[11]||(o[11]=s=>b.value=s),options:E.value,optionLabel:"label",optionValue:"value",placeholder:"Selecciona una competencia",style:{width:"100%"},class:"w-full md:w-11rem"},{option:i(s=>[e("div",ht,[e("div",null,w(s.option.label),1)])]),_:1},8,["modelValue","options"])])]),e("div",yt,[e("div",_t,[gt,t(l(U),{modelValue:r.value.id_docente,"onUpdate:modelValue":o[12]||(o[12]=s=>r.value.id_docente=s),options:I.value,filter:"",optionLabel:"nombres",optionValue:"id",placeholder:"Selecciona una competencia",style:{width:"100%"},class:"w-full md:w-11rem"},{option:i(s=>[e("div",wt,[e("div",null,w(s.option.nombres),1)])]),_:1},8,["modelValue","options"])])])]),_:1},8,["visible"]),t(l(Z),{visible:c.value,"onUpdate:visible":o[16]||(o[16]=s=>c.value=s),modal:"",header:"Asignar Alumnos",style:{width:"900px"}},{default:i(()=>[t(l(N),{selection:D.value,"onUpdate:selection":o[15]||(o[15]=s=>D.value=s),"row-selection":a.rowSelection,value:L.value,class:A("p-datatable-sm"),tableStyle:"min-width: 50rem",style:{"font-size":".9rem"},paginator:!0,rows:9},{default:i(()=>[t(l(d),{selectionMode:"multiple",headerStyle:"width: 3rem"}),t(l(d),{field:"codigo_est",header:"codigo_est"}),t(l(d),{field:"nombres",header:"Nombres"}),t(l(d),{field:"paterno",header:"Paterno"}),t(l(d),{field:"materno",header:"Materno"})]),_:1},8,["selection","row-selection","value"]),e("div",bt,[t(l(h),{severity:"primary",style:{"font-size":"0.9rem"},text:"",onClick:ae},{default:i(()=>[z(" Asignar ")]),_:1})])]),_:1},8,["visible"])])]),_:1})],64))}};export{It as default};
