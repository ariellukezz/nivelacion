import{B as n,l as d,w as i,o as r,a,b as t,Z as u,e as s,c as f,t as _,h as p,g as w,f as h,n as g}from"./app-ec9fab13.js";import{_ as b}from"./GuestLayout-2def794c.js";import{_ as x,a as y,b as v}from"./TextInput-1028801c.js";import{_ as k}from"./PrimaryButton-60cc4141.js";const V={class:"flex flex-col overflow-y-auto md:flex-row"},B=s("div",{class:"h-32 md:h-auto md:w-1/2"},[s("img",{"aria-hidden":"true",class:"object-cover w-full h-full",src:"/images/forgot-password-office.jpeg",alt:"Office"})],-1),N={class:"flex items-center justify-center p-6 sm:p-12 md:w-1/2"},$={class:"w-full"},j=s("h1",{class:"mb-4 font-semibold text-gray-700"}," Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one. ",-1),S={key:0,class:"mb-4 text-sm font-medium text-green-600"},C=["onSubmit"],E={class:"flex items-center justify-end mt-4"},D={__name:"ForgotPassword",props:{status:String},setup(o){const e=n({email:""}),m=()=>{e.post(route("password.email"))};return(F,l)=>(r(),d(b,null,{default:i(()=>[a(t(u),{title:"Forgot Password"}),s("div",V,[B,s("div",N,[s("div",$,[j,o.status?(r(),f("div",S,_(o.status),1)):p("",!0),s("form",{onSubmit:w(m,["prevent"])},[s("div",null,[a(x,{for:"email",value:"Email"}),a(y,{id:"email",type:"email",class:"block w-full mt-1",modelValue:t(e).email,"onUpdate:modelValue":l[0]||(l[0]=c=>t(e).email=c),required:"",autofocus:"",autocomplete:"username"},null,8,["modelValue"]),a(v,{class:"mt-2",message:t(e).errors.email},null,8,["message"])]),s("div",E,[a(k,{class:g({"opacity-25":t(e).processing}),disabled:t(e).processing},{default:i(()=>[h(" Email Password Reset Link ")]),_:1},8,["class","disabled"])])],40,C)])])])]),_:1}))}};export{D as default};