import{b as x,a as w,c as g}from"./TopMenu-80c3d1e9.js";import"./multiselect.esm-81da1ec5.js";import{A as V}from"./LayoutSuperadmi-0a19bc29.js";import{r as i,i as D,c as S,a as e,b as a,w as n,F as z,o as C,Z as $,d as o,n as h,t as u}from"./app-69d41ace.js";import{s as v,a as s}from"./column.esm-ab607ddf.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./NavigationMobile-3431074c.js";const k={class:"bg-white shadow-xs p-4",style:{height:"calc(100vh - 110px)","font-family":"Arial, Helvetica, sans-serif"}},A={class:"flex",style:{"justify-content":"space-between"}},B={class:"p-input-icon-left"},N=o("i",{class:"pi pi-search"},null,-1),j={class:"flex",style:{"justify-content":"flex-start"}},E={class:"flex"},J={__name:"docentes",setup(F){const p=i([]),r=i(""),_=i(0),b=i(1),m=async d=>{let l=await axios.post("getDocentes",{page:b.value,buscar:r.value});p.value=l.data.datos,_.value=l.data.datos.total};D(r,(d,l)=>{m()});const c=i(!1),f=i([]),y=d=>{c.value=!0,f.value=[d]};return m(),(d,l)=>(C(),S(z,null,[e(a($),{title:"Docentes"}),e(V,null,{default:n(()=>[o("div",k,[o("div",A,[o("span",B,[N,e(a(x),{modelValue:r.value,"onUpdate:modelValue":l[0]||(l[0]=t=>r.value=t),placeholder:"Search"},null,8,["modelValue"])])]),e(a(v),{value:p.value,class:h("p-datatable-sm"),paginator:"",rows:10,tableStyle:"min-width: 50rem",style:{"font-size":"0.8rem"}},{default:n(()=>[e(a(s),{field:"nro_doc",header:"nro_doc"}),e(a(s),{field:"nombres",header:"Nombres"},{body:n(({data:t})=>[o("div",j,[o("div",null,u(t.nombres)+", "+u(t.paterno)+" "+u(t.materno),1)])]),_:1}),e(a(s),{field:"sexo",header:"Sexo"}),e(a(s),{field:"email",header:"Correo"}),e(a(s),{field:"telefono",header:"Teléfono"}),e(a(s),{field:"nombre_usuario",header:"nombre_usuario"}),e(a(s),{field:"nombre_escuela",header:"nombre_escuela"}),e(a(s),{field:"programa",header:"Ver",width:"90px"},{body:n(({data:t})=>[o("div",E,[e(a(w),{class:"",severity:"help",icon:"pi pi-eye","aria-label":"Submit",onClick:U=>y(t),size:"small",style:{width:"25px",height:"25px"}},null,8,["onClick"])])]),_:1})]),_:1},8,["value"])])]),_:1}),e(a(g),{visible:c.value,"onUpdate:visible":l[1]||(l[1]=t=>c.value=t),maximizable:"",modal:"",header:"Detalles del Alumno",style:{width:"50vw"}},{default:n(()=>[e(a(v),{value:f.value,class:h("p-datatable-sm"),style:{"font-size":"0.8rem"}},{default:n(()=>[e(a(s),{field:"nro_doc",header:"Dni"}),e(a(s),{field:"estado",header:"Estado pass"})]),_:1},8,["value"])]),_:1},8,["visible"])],64))}};export{J as default};