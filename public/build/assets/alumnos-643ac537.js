import{b as x,a as w,c as C}from"./TopMenu-80c3d1e9.js";import"./multiselect.esm-81da1ec5.js";import{A as V}from"./LayoutSuperadmi-0a19bc29.js";import{r as i,i as k,c as A,a as e,b as a,w as r,F as N,o as h,Z as S,d as o,n as v,k as z,g as D,t as m}from"./app-69d41ace.js";import{s as _,a as l}from"./column.esm-ab607ddf.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./NavigationMobile-3431074c.js";const $={class:"bg-white shadow-xs p-4",style:{height:"calc(100vh - 110px)","font-family":"Arial, Helvetica, sans-serif"}},B={class:"flex",style:{"justify-content":"space-between"}},j={class:"p-input-icon-left"},F=o("i",{class:"pi pi-search"},null,-1),R={class:"flex",style:{"justify-content":"flex-start"}},U={class:"flex"},J={__name:"alumnos",setup(E){const u=i([]),n=i(""),b=i(0),g=i(1),p=async d=>{let t=await axios.post("getAlumnosc",{page:g.value,competencia:1,buscar:n.value});u.value=t.data.datos,b.value=t.data.datos.total};k(n,(d,t)=>{p()});const c=i(!1),f=i([]),y=d=>{c.value=!0,f.value=[d]};return p(),(d,t)=>(h(),A(N,null,[e(a(S),{title:"Alumnos"}),e(V,null,{default:r(()=>[o("div",$,[o("div",B,[o("span",j,[F,e(a(x),{modelValue:n.value,"onUpdate:modelValue":t[0]||(t[0]=s=>n.value=s),placeholder:"Search"},null,8,["modelValue"])])]),e(a(_),{value:u.value,class:v("p-datatable-sm"),paginator:"",rows:10,tableStyle:"min-width: 50rem",style:{"font-size":"0.8rem"}},{default:r(()=>[e(a(l),{field:"dni",header:"Dni"}),e(a(l),{field:"codigo",header:"codigo"}),d.conf_codigo===!0?(h(),z(a(l),{key:0,field:"Codigo",header:"Código"})):D("",!0),e(a(l),{field:"nombres",header:"Nombres"},{body:r(({data:s})=>[o("div",R,[o("div",null,m(s.nombres)+", "+m(s.paterno)+" "+m(s.materno),1)])]),_:1}),e(a(l),{field:"sexo",header:"Sexo"}),e(a(l),{field:"email",header:"Correo"}),e(a(l),{field:"telefono",header:"Teléfono"}),e(a(l),{field:"programa",header:"Programa"}),e(a(l),{field:"modalidad",header:"modalidad"}),e(a(l),{field:"programa",header:"Ver",width:"90px"},{body:r(({data:s})=>[o("div",U,[e(a(w),{class:"",severity:"help",icon:"pi pi-eye","aria-label":"Submit",onClick:H=>y(s),size:"small",style:{width:"25px",height:"25px"}},null,8,["onClick"])])]),_:1})]),_:1},8,["value"])])]),_:1}),e(a(C),{visible:c.value,"onUpdate:visible":t[1]||(t[1]=s=>c.value=s),maximizable:"",modal:"",header:"Detalles del Alumno",style:{width:"50vw"}},{default:r(()=>[e(a(_),{value:f.value,class:v("p-datatable-sm"),style:{"font-size":"0.8rem"}},{default:r(()=>[e(a(l),{field:"dni",header:"Dni"}),e(a(l),{field:"nombre_colegio",header:"Nombre del Colegio"}),e(a(l),{field:"anio_egreso",header:"anio_egreso"}),e(a(l),{field:"semestre",header:"semestre"}),e(a(l),{field:"nombre",header:"Nombre"}),e(a(l),{field:"nota",header:"Notas"}),e(a(l),{field:"C1_R",header:"C1 R"})]),_:1},8,["value"])]),_:1},8,["visible"])],64))}};export{J as default};