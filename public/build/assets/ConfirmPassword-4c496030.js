import{G as d,m as c,w as r,o as n,a,b as e,Z as m,d as s,e as f,k as u,n as _}from"./app-594504e0.js";import{_ as p}from"./GuestLayout-1003e8a7.js";import{_ as w,a as h,b}from"./TextInput-15de961f.js";import{_ as g}from"./PrimaryButton-2ae06c54.js";const x={class:"flex flex-col overflow-y-auto md:flex-row"},v=s("div",{class:"h-32 md:h-auto md:w-1/2"},[s("img",{"aria-hidden":"true",class:"object-cover w-full h-full",src:"{{ asset('images/forgot-password-office.jpeg') }}",alt:"Office"})],-1),y={class:"flex items-center justify-center p-6 sm:p-12 md:w-1/2"},V={class:"w-full"},k=s("h1",{class:"mb-4 font-semibold text-gray-700 dark:text-gray-200"}," This is a secure area of the application. Please confirm your password before continuing. ",-1),C=["onSubmit"],$={class:"flex justify-end mt-4"},q={__name:"ConfirmPassword",setup(j){const o=d({password:""}),i=()=>{o.post(route("password.confirm"),{onFinish:()=>o.reset()})};return(P,t)=>(n(),c(p,null,{default:r(()=>[a(e(m),{title:"Confirm Password"}),s("div",x,[v,s("div",y,[s("div",V,[k,s("form",{onSubmit:f(i,["prevent"])},[s("div",null,[a(w,{for:"password",value:"Password"}),a(h,{id:"password",type:"password",class:"mt-1 block w-full",modelValue:e(o).password,"onUpdate:modelValue":t[0]||(t[0]=l=>e(o).password=l),required:"",autocomplete:"current-password",autofocus:""},null,8,["modelValue"]),a(b,{class:"mt-2",message:e(o).errors.password},null,8,["message"])]),s("div",$,[a(g,{class:_(["ml-4",{"opacity-25":e(o).processing}]),disabled:e(o).processing},{default:r(()=>[u(" Confirm ")]),_:1},8,["class","disabled"])])],40,C)])])])]),_:1}))}};export{q as default};