import{A as O}from"./AuthenticatedLayout-f2e431ec.js";import{u as Z,h as q,r as i,i as r,c as I,a as s,b as o,w as m,F as J,o as v,Z as K,d as l,n as Q,k as f,g as y,t as U,f as V}from"./app-857e9101.js";import{a as $,b as j,s as W,c as X}from"./TopMenu-128f62e5.js";import{s as Y,a as u,b as L}from"./column.esm-ee0205ea.js";import{s as ee,a as p}from"./confirmpopup.esm-e18c6d26.js";import"./tag.esm-8404e9ef.js";import{s as le}from"./overlaypanel.esm-1f318eab.js";import"./_plugin-vue_export-helper-c27b6911.js";const ae={class:"bg-white shadow-xs p-4",style:{height:"calc(100vh - 110px)","font-family":"Arial, Helvetica, sans-serif"}},oe={class:"flex",style:{"justify-content":"space-between"}},te={class:"p-input-icon-left"},se=l("i",{class:"pi pi-search"},null,-1),de=l("i",{class:"pi pi-cog"},null,-1),ie={class:"card"},ue=l("div",{class:"flex justify-content-center mb-4"},null,-1),ne={class:"flex",style:{"justify-content":"flex-start"}},re={class:"flex",style:{"justify-content":"flex-start"}},me={style:{width:"200px","white-space":"nowrap","text-overflow":"ellipsis",overflow:"hidden"}},ve={class:"flex mt-2 align-items-center",style:{"justify-content":"flex-end"}},pe=l("div",{class:"mr-3"},[l("label",null,"Estado")],-1),ce={class:"flex",style:{width:"100%","justify-content":"space-between"}},fe={class:"mb-2",style:{width:"48%"}},ye=l("div",null,[l("label",null,"Nombres")],-1),Ve={class:"mb-2",style:{width:"48%"}},ge=l("div",null,[l("label",null,"Apellidos")],-1),he={class:"mb-2"},_e=l("div",null,[l("label",null,"Rol")],-1),xe={class:"flex",style:{width:"100%","justify-content":"space-between"}},we={class:"mb-2",style:{width:"48%"}},be=l("div",null,[l("label",null,"Correo")],-1),Ce={class:"mb-2",style:{width:"48%"}},je=l("div",null,[l("label",null,"Contraseña")],-1),ke={class:"mb-2"},Ue=l("div",null,[l("label",null,"Programa")],-1),$e={class:"flex",style:{"justify-content":"flex-end"}},Ne={class:"card"},Ae={class:"flex mt-1",style:{"justify-content":"flex-end"}},Te={class:"flex mt-1",style:{"justify-content":"flex-end"}},ze={class:"flex mt-1",style:{"justify-content":"flex-end"}},Se={class:"flex mt-1",style:{"justify-content":"flex-end"}},Ee={class:"flex mt-1",style:{"justify-content":"flex-end"}},Be={class:"flex mt-1",style:{"justify-content":"flex-end"}},Le={class:"flex mt-1",style:{"justify-content":"flex-end"}},qe={__name:"index",setup(Re){const R=Z();q();const N=i([]),P=i(0),A=i(1),k=i(""),T=i([]),z=i([]),c=i(!1),g=i(!0),h=i(!1),_=i(!1),x=i(!1),w=i(!1),b=i(!0),C=i(!1),S=i(),D=d=>{S.value.toggle(d)},t=i({id:null,nombres:"",apellidos:"",rol:2,programa:3,email:"",password:"",estado:!0}),n=async d=>{let e=await axios.post("get-alumnos?page="+A.value,{term:k.value,telefono:g.value,codigo:h.value,colegio:_.value,tipo_colegio:x.value,estado_civil:w.value,area:b.value,modalidad:C.value});z.value=e.data.datos.data,P.value=e.data.datos.total},E=async()=>{let d=await axios.post("get-programas?page="+A.value,{term:""});N.value=d.data.datos.data},F=async(d="")=>{let e=await axios.post("get-roles?page=",{term:""});T.value=e.data.datos.data},G=async()=>{let d=await axios.post("save-usuario",{id:t.value.id,nombres:t.value.nombres,apellidos:t.value.apellidos,email:t.value.email,password:t.value.password,programa:t.value.programa,estado:t.value.estado,rol:t.value.rol});M(d.data.tipo,d.data.titulo,d.data.mensaje),n(),c.value=!1,B()},H=d=>{console.log("::ROL::",d)};r(k,(d,e)=>{n()});const M=(d,e,a)=>{R.add({severity:d,summary:e,detail:a,life:3e3})};r(c,(d,e)=>{c.value==!1&&t.value.id!=null&&B()});const B=()=>{t.value.id=null,t.value.nombres="",t.value.apellidos="",t.value.rol=2,t.value.programa=3,t.value.email="",t.value.password="",t.value.estado=!0};return r(h,(d,e)=>{n()}),r(g,(d,e)=>{n()}),r(_,(d,e)=>{n()}),r(x,(d,e)=>{n()}),r(w,(d,e)=>{n()}),r(b,(d,e)=>{n()}),r(C,(d,e)=>{n()}),n(),E(),F(),(d,e)=>(v(),I(J,null,[s(o(K),{title:"Alumnos"}),s(O,null,{default:m(()=>[l("div",ae,[l("div",null,[l("div",oe,[s(o($),{label:"Nuevo",onClick:e[0]||(e[0]=a=>c.value=!0),size:"small",style:{height:"40px"}}),l("span",te,[se,s(o(j),{modelValue:k.value,"onUpdate:modelValue":e[1]||(e[1]=a=>k.value=a),style:{"padding-left":"40px",height:"40px"},placeholder:"Search"},null,8,["modelValue"]),s(o($),{label:"Nuevo",onClick:D,size:"small",style:{height:"40px"}},{default:m(()=>[de]),_:1})])])]),s(o(W)),s(o(ee)),l("div",null,[l("div",ie,[ue,s(o(Y),{value:z.value,class:Q("p-datatable-sm"),tableStyle:"min-width: 50rem",style:{"font-size":".9rem"}},{default:m(()=>[s(o(u),{field:"dni",header:"Dni"}),h.value===!0?(v(),f(o(u),{key:0,field:"codigo",header:"Código"})):y("",!0),s(o(u),{field:"nombres",header:"Nombres"},{body:m(({data:a})=>[l("div",ne,[l("div",null,U(a.nombres)+" "+U(a.paterno)+" "+U(a.materno),1)])]),_:1}),s(o(u),{field:"sexo",header:"Sexo"}),s(o(u),{field:"tipo_examen",header:"Tipo Examen"}),s(o(u),{field:"programa",header:"Programa"},{body:m(({data:a})=>[l("div",re,[l("div",me,[l("span",null,U(a.programa),1)])])]),_:1}),g.value===!0?(v(),f(o(u),{key:1,field:"telefono",header:"Telefono"})):y("",!0),_.value===!0?(v(),f(o(u),{key:2,field:"colegio",header:"Colegio"})):y("",!0),x.value===!0?(v(),f(o(u),{key:3,field:"tipo_colegio",header:"Tipo Colegio"})):y("",!0),w.value===!0?(v(),f(o(u),{key:4,field:"estado_civil",header:"Est civ"})):y("",!0),b.value===!0?(v(),f(o(u),{key:5,field:"area",header:"Area"})):y("",!0),C.value===!0?(v(),f(o(u),{key:6,field:"modalidad",header:"Modalidad"})):y("",!0)]),_:1},8,["value"])])]),s(o(X),{visible:c.value,"onUpdate:visible":e[10]||(e[10]=a=>c.value=a),modal:"",header:"Nuevo usuario",style:{width:"50vw"}},{footer:m(()=>[l("div",$e,[l("div",null,[s(o($),{label:"Cancelar",outlined:"",onClick:e[9]||(e[9]=a=>c.value=!1),size:"small"})]),s(o($),{label:"Guardar",onClick:G,size:"small"})])]),default:m(()=>[l("div",ve,[pe,s(o(p),{modelValue:t.value.estado,"onUpdate:modelValue":e[2]||(e[2]=a=>t.value.estado=a)},null,8,["modelValue"])]),l("div",ce,[l("div",fe,[ye,s(o(j),{style:{width:"100%",height:"40px"},type:"text",modelValue:t.value.nombres,"onUpdate:modelValue":e[3]||(e[3]=a=>t.value.nombres=a)},null,8,["modelValue"])]),l("div",Ve,[ge,s(o(j),{style:{width:"100%",height:"40px"},type:"text",modelValue:t.value.apellidos,"onUpdate:modelValue":e[4]||(e[4]=a=>t.value.apellidos=a)},null,8,["modelValue"])])]),l("div",he,[_e,s(o(L),{modelValue:t.value.rol,"onUpdate:modelValue":e[5]||(e[5]=a=>t.value.rol=a),optionValue:"value",options:T.value,showClear:"",style:{height:"45px"},onComplete:H,optionLabel:"label",placeholder:"Selecciona el rol",class:"w-full md:w-10rem"},null,8,["modelValue","options"])]),l("div",xe,[l("div",we,[be,s(o(j),{style:{width:"100%",height:"40px"},type:"text",modelValue:t.value.email,"onUpdate:modelValue":e[6]||(e[6]=a=>t.value.email=a)},null,8,["modelValue"])]),l("div",Ce,[je,s(o(j),{style:{width:"100%",height:"40px"},type:"password",modelValue:t.value.password,"onUpdate:modelValue":e[7]||(e[7]=a=>t.value.password=a)},null,8,["modelValue"])])]),l("div",ke,[Ue,s(o(L),{modelValue:t.value.programa,"onUpdate:modelValue":e[8]||(e[8]=a=>t.value.programa=a),showClear:"",style:{height:"45px"},options:N.value,onComplete:E,optionValue:"value",optionLabel:"label",placeholder:"Selecciona el programa",class:"w-full md:w-14rem"},null,8,["modelValue","options"])])]),_:1},8,["visible"]),s(o(le),{ref_key:"op",ref:S},{default:m(()=>[l("div",Ne,[l("div",Ae,[V("Codigo: "),s(o(p),{modelValue:h.value,"onUpdate:modelValue":e[11]||(e[11]=a=>h.value=a)},null,8,["modelValue"])]),l("div",Te,[V("telefono: "),s(o(p),{modelValue:g.value,"onUpdate:modelValue":e[12]||(e[12]=a=>g.value=a)},null,8,["modelValue"])]),l("div",ze,[V("Colegio: "),s(o(p),{modelValue:_.value,"onUpdate:modelValue":e[13]||(e[13]=a=>_.value=a)},null,8,["modelValue"])]),l("div",Se,[V("Tipo colegio: "),s(o(p),{modelValue:x.value,"onUpdate:modelValue":e[14]||(e[14]=a=>x.value=a)},null,8,["modelValue"])]),l("div",Ee,[V("Estado civil: "),s(o(p),{modelValue:w.value,"onUpdate:modelValue":e[15]||(e[15]=a=>w.value=a)},null,8,["modelValue"])]),l("div",Be,[V("area: "),s(o(p),{modelValue:b.value,"onUpdate:modelValue":e[16]||(e[16]=a=>b.value=a)},null,8,["modelValue"])]),l("div",Le,[V("modalidad: "),s(o(p),{modelValue:C.value,"onUpdate:modelValue":e[17]||(e[17]=a=>C.value=a)},null,8,["modelValue"])])])]),_:1},512)])]),_:1})],64))}};export{qe as default};