import{e as i,c,y as B,N as U,f as a,A as x,b as m,w as p,d as y,K as D,F as A,r as w,o as G,P as Y,T as F,j as v,z as S,Q as C,v as E,R as X,U as q,x as K,n as _,B as k,t as g,g as T,i as b,s as L,S as Q,m as J,u as W,G as ee}from"./app-1b559a8b.js";import{b as N,c as j,d as M,e as z,a as te,s as P}from"./index.esm-490a0fc5.js";import{a as Z,s as se,R as ne}from"./index.esm-57370536.js";import{a as oe,s as R}from"./dialog.esm-529e88fc.js";const ae={class:"absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg","aria-hidden":"true"},ie={class:"flex"},le={class:"ml-4"},vt={__name:"NavLink",props:["href","active"],setup(e){return(t,s)=>(i(),c(A,null,[B(a("span",ae,null,512),[[U,e.active]]),a("div",ie,[x(t.$slots,"icon"),m(y(D),{href:e.href,class:"inline-flex items-center justify-between w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800"},{default:p(()=>[a("span",le,[x(t.$slots,"default")])]),_:3},8,["href"])])],64))}},yt="/build/assets/logotiny-e0fccd92.png",re={class:"flex flex-shrink-0 items-center space-x-6"},de={class:"relative"},ce={class:"absolute right-0 mt-2 w-56 rounded-md border border-gray-100 bg-white p-2 text-gray-600 shadow-md space-y-2","aria-label":"submenu"},ue={__name:"Dropdown",setup(e){const t=w(!1),s=o=>{t.value&&o.keyCode===27&&(t.value=!1)};return G(()=>document.addEventListener("keydown",s)),Y(()=>document.removeEventListener("keydown",s)),(o,n)=>(i(),c("div",null,[a("ul",re,[a("li",de,[a("div",{onClick:n[0]||(n[0]=r=>t.value=!t.value)},[x(o.$slots,"trigger")]),m(F,{"leave-active-class":"transition duration-150 ease-in","leave-from-class":"opacity-100","leave-to-class":"opacity-0"},{default:p(()=>[B(a("ul",ce,[a("li",null,[x(o.$slots,"content")])],512),[[U,t.value]])]),_:3})])])]))}},pe={__name:"DropdownLink",setup(e){return(t,s)=>(i(),v(y(D),{class:"inline-flex w-full items-center rounded-md px-2 py-1 text-sm font-semibold transition-colors duration-150 hover:bg-gray-100 hover:text-gray-800"},{default:p(()=>[x(t.$slots,"icon"),a("span",null,[x(t.$slots,"default")])]),_:3}))}};var me={name:"ChevronDownIcon",extends:Z};const he=a("path",{d:"M7.01744 10.398C6.91269 10.3985 6.8089 10.378 6.71215 10.3379C6.61541 10.2977 6.52766 10.2386 6.45405 10.1641L1.13907 4.84913C1.03306 4.69404 0.985221 4.5065 1.00399 4.31958C1.02276 4.13266 1.10693 3.95838 1.24166 3.82747C1.37639 3.69655 1.55301 3.61742 1.74039 3.60402C1.92777 3.59062 2.11386 3.64382 2.26584 3.75424L7.01744 8.47394L11.769 3.75424C11.9189 3.65709 12.097 3.61306 12.2748 3.62921C12.4527 3.64535 12.6199 3.72073 12.7498 3.84328C12.8797 3.96582 12.9647 4.12842 12.9912 4.30502C13.0177 4.48162 12.9841 4.662 12.8958 4.81724L7.58083 10.1322C7.50996 10.2125 7.42344 10.2775 7.32656 10.3232C7.22968 10.3689 7.12449 10.3944 7.01744 10.398Z",fill:"currentColor"},null,-1),fe=[he];function ge(e,t,s,o,n,r){return i(),c("svg",S({width:"14",height:"14",viewBox:"0 0 14 14",fill:"none",xmlns:"http://www.w3.org/2000/svg"},e.pti()),fe,16)}me.render=ge;var ve={name:"AngleRightIcon",extends:Z};const ye=a("path",{d:"M5.25 11.1728C5.14929 11.1694 5.05033 11.1455 4.9592 11.1025C4.86806 11.0595 4.78666 10.9984 4.72 10.9228C4.57955 10.7822 4.50066 10.5916 4.50066 10.3928C4.50066 10.1941 4.57955 10.0035 4.72 9.86283L7.72 6.86283L4.72 3.86283C4.66067 3.71882 4.64765 3.55991 4.68275 3.40816C4.71785 3.25642 4.79932 3.11936 4.91585 3.01602C5.03238 2.91268 5.17819 2.84819 5.33305 2.83149C5.4879 2.81479 5.64411 2.84671 5.78 2.92283L9.28 6.42283C9.42045 6.56346 9.49934 6.75408 9.49934 6.95283C9.49934 7.15158 9.42045 7.34221 9.28 7.48283L5.78 10.9228C5.71333 10.9984 5.63193 11.0595 5.5408 11.1025C5.44966 11.1455 5.35071 11.1694 5.25 11.1728Z",fill:"currentColor"},null,-1),be=[ye];function _e(e,t,s,o,n,r){return i(),c("svg",S({width:"14",height:"14",viewBox:"0 0 14 14",fill:"none",xmlns:"http://www.w3.org/2000/svg"},e.pti()),be,16)}ve.render=_e;function xe(e,t){t===void 0&&(t={});var s=t.insertAt;if(!(!e||typeof document>"u")){var o=document.head||document.getElementsByTagName("head")[0],n=document.createElement("style");n.type="text/css",s==="top"&&o.firstChild?o.insertBefore(n,o.firstChild):o.appendChild(n),n.styleSheet?n.styleSheet.cssText=e:n.appendChild(document.createTextNode(e))}}var Ce=`
.p-tieredmenu-overlay {
    position: absolute;
    top: 0;
    left: 0;
}
.p-tieredmenu ul {
    margin: 0;
    padding: 0;
    list-style: none;
}
.p-tieredmenu .p-submenu-list {
    position: absolute;
    min-width: 100%;
    z-index: 1;
    display: none;
}
.p-tieredmenu .p-menuitem-link {
    cursor: pointer;
    display: flex;
    align-items: center;
    text-decoration: none;
    overflow: hidden;
    position: relative;
}
.p-tieredmenu .p-menuitem-text {
    line-height: 1;
}
.p-tieredmenu .p-menuitem {
    position: relative;
}
.p-tieredmenu .p-menuitem-link .p-submenu-icon {
    margin-left: auto;
}
.p-tieredmenu .p-menuitem-active > .p-submenu-list {
    display: block;
    left: 100%;
    top: 0;
}
`;xe(Ce);function we(e,t){t===void 0&&(t={});var s=t.insertAt;if(!(!e||typeof document>"u")){var o=document.head||document.getElementsByTagName("head")[0],n=document.createElement("style");n.type="text/css",s==="top"&&o.firstChild?o.insertBefore(n,o.firstChild):o.appendChild(n),n.styleSheet?n.styleSheet.cssText=e:n.appendChild(document.createTextNode(e))}}var $e=`
.p-splitbutton[data-v-5a8b8d41] {
    display: inline-flex;
    position: relative;
}
.p-splitbutton .p-splitbutton-defaultbutton[data-v-5a8b8d41],
.p-splitbutton.p-button-rounded > .p-splitbutton-defaultbutton.p-button[data-v-5a8b8d41],
.p-splitbutton.p-button-outlined > .p-splitbutton-defaultbutton.p-button[data-v-5a8b8d41] {
    flex: 1 1 auto;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-right: 0 none;
}
.p-splitbutton-menubutton[data-v-5a8b8d41],
.p-splitbutton.p-button-rounded > .p-splitbutton-menubutton.p-button[data-v-5a8b8d41],
.p-splitbutton.p-button-outlined > .p-splitbutton-menubutton.p-button[data-v-5a8b8d41] {
    display: flex;
    align-items: center;
    justify-content: center;
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}
.p-splitbutton .p-menu[data-v-5a8b8d41] {
    min-width: 100%;
}
.p-fluid .p-splitbutton[data-v-5a8b8d41] {
    display: flex;
}
`;we($e);var V={name:"ToastMessage",emits:["close"],props:{message:{type:null,default:null},templates:{type:Object,default:null},closeIcon:{type:String,default:null},infoIcon:{type:String,default:null},warnIcon:{type:String,default:null},errorIcon:{type:String,default:null},successIcon:{type:String,default:null},closeButtonProps:{type:null,default:null}},closeTimeout:null,mounted(){this.message.life&&(this.closeTimeout=setTimeout(()=>{this.close({message:this.message,type:"life-end"})},this.message.life))},beforeUnmount(){this.clearCloseTimeout()},methods:{close(e){this.$emit("close",e)},onCloseClick(){this.clearCloseTimeout(),this.close({message:this.message,type:"close"})},clearCloseTimeout(){this.closeTimeout&&(clearTimeout(this.closeTimeout),this.closeTimeout=null)}},computed:{containerClass(){return["p-toast-message",this.message.styleClass,{"p-toast-message-info":this.message.severity==="info","p-toast-message-warn":this.message.severity==="warn","p-toast-message-error":this.message.severity==="error","p-toast-message-success":this.message.severity==="success"}]},iconComponent(){return{info:!this.infoIcon&&N,success:!this.successIcon&&j,warn:!this.warnIcon&&M,error:!this.errorIcon&&z}[this.message.severity]},iconClass(){return[{[this.infoIcon]:this.message.severity==="info",[this.warnIcon]:this.message.severity==="warn",[this.errorIcon]:this.message.severity==="error",[this.successIcon]:this.message.severity==="success"}]},closeAriaLabel(){return this.$primevue.config.locale.aria?this.$primevue.config.locale.aria.close:void 0}},components:{TimesIcon:se,InfoCircleIcon:N,CheckIcon:j,ExclamationTriangleIcon:M,TimesCircleIcon:z},directives:{ripple:ne}};const Ie={class:"p-toast-message-text"},ke={class:"p-toast-summary"},Te={class:"p-toast-detail"},Se={key:2},Ee=["aria-label"];function Be(e,t,s,o,n,r){const $=K("ripple");return i(),c("div",{class:_(r.containerClass),role:"alert","aria-live":"assertive","aria-atomic":"true"},[a("div",{class:_(["p-toast-message-content",s.message.contentStyleClass])},[s.templates.message?(i(),v(k(s.templates.message),{key:1,message:s.message},null,8,["message"])):(i(),c(A,{key:0},[(i(),v(k(s.templates.icon?s.templates.icon:r.iconComponent.name?r.iconComponent:"span"),{class:_([r.iconClass,"p-toast-message-icon"])},null,8,["class"])),a("div",Ie,[a("span",ke,g(s.message.summary),1),a("div",Te,g(s.message.detail),1)])],64)),T(g(s.message.closable)+" ",1),s.message.closable!==!1?(i(),c("div",Se,[B((i(),c("button",S({class:"p-toast-icon-close p-link",type:"button","aria-label":r.closeAriaLabel,onClick:t[0]||(t[0]=(...h)=>r.onCloseClick&&r.onCloseClick(...h)),autofocus:""},s.closeButtonProps),[(i(),v(k(s.templates.closeicon||"TimesIcon"),{class:_(["p-toast-icon-close-icon",s.closeIcon])},null,8,["class"]))],16,Ee)),[[$]])])):b("",!0)],2)],2)}V.render=Be;var Ae=0,Le={name:"Toast",inheritAttrs:!1,emits:["close","life-end"],props:{group:{type:String,default:null},position:{type:String,default:"top-right"},autoZIndex:{type:Boolean,default:!0},baseZIndex:{type:Number,default:0},breakpoints:{type:Object,default:null},closeIcon:{type:String,default:void 0},infoIcon:{type:String,default:void 0},warnIcon:{type:String,default:void 0},errorIcon:{type:String,default:void 0},successIcon:{type:String,default:void 0},closeButtonProps:{type:null,default:null}},data(){return{messages:[]}},styleElement:null,mounted(){C.on("add",this.onAdd),C.on("remove-group",this.onRemoveGroup),C.on("remove-all-groups",this.onRemoveAllGroups),this.breakpoints&&this.createStyle()},beforeUnmount(){this.destroyStyle(),this.$refs.container&&this.autoZIndex&&E.clear(this.$refs.container),C.off("add",this.onAdd),C.off("remove-group",this.onRemoveGroup),C.off("remove-all-groups",this.onRemoveAllGroups)},methods:{add(e){e.id==null&&(e.id=Ae++),this.messages=[...this.messages,e]},remove(e){let t=-1;for(let s=0;s<this.messages.length;s++)if(this.messages[s]===e.message){t=s;break}this.messages.splice(t,1),this.$emit(e.type,{message:e.message})},onAdd(e){this.group==e.group&&this.add(e)},onRemoveGroup(e){this.group===e&&(this.messages=[])},onRemoveAllGroups(){this.messages=[]},onEnter(){this.$refs.container.setAttribute(this.attributeSelector,""),this.autoZIndex&&E.set("modal",this.$refs.container,this.baseZIndex||this.$primevue.config.zIndex.modal)},onLeave(){this.$refs.container&&this.autoZIndex&&X.isEmpty(this.messages)&&setTimeout(()=>{E.clear(this.$refs.container)},200)},createStyle(){if(!this.styleElement){this.styleElement=document.createElement("style"),this.styleElement.type="text/css",document.head.appendChild(this.styleElement);let e="";for(let t in this.breakpoints){let s="";for(let o in this.breakpoints[t])s+=o+":"+this.breakpoints[t][o]+"!important;";e+=`
                        @media screen and (max-width: ${t}) {
                            .p-toast[${this.attributeSelector}] {
                                ${s}
                            }
                        }
                    `}this.styleElement.innerHTML=e}},destroyStyle(){this.styleElement&&(document.head.removeChild(this.styleElement),this.styleElement=null)}},computed:{containerClass(){return["p-toast p-component p-toast-"+this.position,{"p-input-filled":this.$primevue.config.inputStyle==="filled","p-ripple-disabled":this.$primevue.config.ripple===!1}]},attributeSelector(){return q()}},components:{ToastMessage:V,Portal:oe}};function Ne(e,t,s,o,n,r){const $=L("ToastMessage"),h=L("Portal");return i(),v(h,null,{default:p(()=>[a("div",S({ref:"container",class:r.containerClass},e.$attrs),[m(Q,{name:"p-toast-message",tag:"div",onEnter:r.onEnter,onLeave:r.onLeave},{default:p(()=>[(i(!0),c(A,null,J(n.messages,d=>(i(),v($,{key:d.id,message:d,templates:e.$slots,closeIcon:s.closeIcon,infoIcon:s.infoIcon,warnIcon:s.warnIcon,errorIcon:s.errorIcon,successIcon:s.successIcon,closeButtonProps:s.closeButtonProps,onClose:t[0]||(t[0]=I=>r.remove(I))},null,8,["message","templates","closeIcon","infoIcon","warnIcon","errorIcon","successIcon","closeButtonProps"]))),128))]),_:1},8,["onEnter","onLeave"])],16)]),_:1})}function je(e,t){t===void 0&&(t={});var s=t.insertAt;if(!(!e||typeof document>"u")){var o=document.head||document.getElementsByTagName("head")[0],n=document.createElement("style");n.type="text/css",s==="top"&&o.firstChild?o.insertBefore(n,o.firstChild):o.appendChild(n),n.styleSheet?n.styleSheet.cssText=e:n.appendChild(document.createTextNode(e))}}var Me=`
.p-toast {
    position: fixed;
    width: 25rem;
}
.p-toast-message-content {
    display: flex;
    align-items: flex-start;
}
.p-toast-message-text {
    flex: 1 1 auto;
}
.p-toast-top-right {
    top: 20px;
    right: 20px;
}
.p-toast-top-left {
    top: 20px;
    left: 20px;
}
.p-toast-bottom-left {
    bottom: 20px;
    left: 20px;
}
.p-toast-bottom-right {
    bottom: 20px;
    right: 20px;
}
.p-toast-top-center {
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
}
.p-toast-bottom-center {
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
}
.p-toast-center {
    left: 50%;
    top: 50%;
    min-width: 20vw;
    transform: translate(-50%, -50%);
}
.p-toast-icon-close {
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
}
.p-toast-icon-close.p-link {
    cursor: pointer;
}

/* Animations */
.p-toast-message-enter-from {
    opacity: 0;
    -webkit-transform: translateY(50%);
    -ms-transform: translateY(50%);
    transform: translateY(50%);
}
.p-toast-message-leave-from {
    max-height: 1000px;
}
.p-toast .p-toast-message.p-toast-message-leave-to {
    max-height: 0;
    opacity: 0;
    margin-bottom: 0;
    overflow: hidden;
}
.p-toast-message-enter-active {
    -webkit-transition: transform 0.3s, opacity 0.3s;
    transition: transform 0.3s, opacity 0.3s;
}
.p-toast-message-leave-active {
    -webkit-transition: max-height 0.45s cubic-bezier(0, 1, 0, 1), opacity 0.3s, margin-bottom 0.3s;
    transition: max-height 0.45s cubic-bezier(0, 1, 0, 1), opacity 0.3s, margin-bottom 0.3s;
}
`;je(Me);Le.render=Ne;var ze={name:"Tag",props:{value:null,severity:null,rounded:Boolean,icon:String},computed:{containerClass(){return["p-tag p-component",{"p-tag-info":this.severity==="info","p-tag-success":this.severity==="success","p-tag-warning":this.severity==="warning","p-tag-danger":this.severity==="danger","p-tag-rounded":this.rounded}]},iconClass(){return["p-tag-icon",this.icon]}}};const Pe={class:"p-tag-value"};function Re(e,t,s,o,n,r){return i(),c("span",{class:_(r.containerClass)},[e.$slots.icon?(i(),v(k(e.$slots.icon),{key:0,class:"p-tag-icon"})):s.icon?(i(),c("span",{key:1,class:_(r.iconClass)},null,2)):b("",!0),x(e.$slots,"default",{},()=>[a("span",Pe,g(s.value),1)])],2)}function Ue(e,t){t===void 0&&(t={});var s=t.insertAt;if(!(!e||typeof document>"u")){var o=document.head||document.getElementsByTagName("head")[0],n=document.createElement("style");n.type="text/css",s==="top"&&o.firstChild?o.insertBefore(n,o.firstChild):o.appendChild(n),n.styleSheet?n.styleSheet.cssText=e:n.appendChild(document.createTextNode(e))}}var De=`
.p-tag {
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.p-tag-icon,
.p-tag-value,
.p-tag-icon.pi {
    line-height: 1.5;
}
.p-tag.p-tag-rounded {
    border-radius: 10rem;
}
`;Ue(De);ze.render=Re;const Ge={class:"z-10 py-4 bg-white shadow-md",style:{"background-color":"white",height:"75px"}},Ze={class:"container flex justify-between items-center px-6 mx-auto h-full text-purple-600 md:justify-end"},Ve=a("svg",{class:"w-6 h-6","aria-hidden":"true",fill:"currentColor",viewBox:"0 0 20 20"},[a("path",{"fill-rule":"evenodd",d:"M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z","clip-rule":"evenodd"})],-1),He=[Ve],Oe={class:"flex",style:{"align-items":"center",height:"37px",color:"#000000D9"}},Ye={style:{"text-align":"end","margin-top":"0px"}},Fe={style:{width:"200px","white-space":"nowrap","text-overflow":"ellipsis",overflow:"hidden"}},Xe={style:{"font-size":"0.9rem"}},qe={style:{"margin-top":"-7px"}},Ke={style:{"font-size":"0.9rem","font-weight":"bold"}},Qe=a("div",{style:{"margin-left":"10px"}},[a("i",{class:"pi pi-angle-down"})],-1),Je=a("svg",{class:"mr-3 w-4 h-4","aria-hidden":"true",fill:"none","stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",viewBox:"0 0 24 24",stroke:"currentColor"},[a("path",{d:"M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"})],-1),We={key:1},et={key:0},tt=a("label",null,"Nueva contraseña",-1),st={style:{width:"100%"}},nt={class:"flex justify-end mt-5"},ot={key:0,class:"py-4 text-center"},at={key:1},it={key:0,class:"text-gray-500"},lt={class:"font-semibold text-base"},rt={key:0,class:"mt-1"},dt=["src"],ct={class:"font-bold text-sm whitespace-pre-line"},ut={class:"flex justify-end gap-2 mt-4"},pt=["href"],bt={__name:"TopMenu",props:["usuario"],setup(e){const t=e,s=W(),o=(l,u,f)=>{s.add({severity:l,summary:u,detail:f,life:3e3})};o("success","GUARDADO","--");const n=w(!0),r=w(""),$=async()=>{let l=await axios.post("/save-contrasenia",{contra:r.value});n.value=!1,o(l.data.tipo,l.data.titulo,l.data.mensaje)};ee(()=>{var u;const l=(((u=d.value)==null?void 0:u.tipo)||"").toLowerCase();return l==="success"?"success":l==="warning"||l==="warn"?"warning":l==="error"||l==="danger"?"danger":"info"});const h=w(!1),d=w(null),I=w(!1),H=async()=>{I.value=!0;try{const{data:l}=await axios.post("/get-noti");d.value=(l==null?void 0:l.datos)??null,d.value&&(h.value=!0)}catch(l){console.error(l)}finally{I.value=!1}},O=async()=>{var l;try{(l=d.value)!=null&&l.id&&await axios.post(`/read-noti/${d.value.id}`)}catch(u){console.error(u)}finally{h.value=!1,d.value=null}};return G(async()=>{await H()}),(l,u)=>(i(),c("header",Ge,[a("div",Ze,[a("button",{onClick:u[0]||(u[0]=f=>l.$page.props.showingMobileMenu=!l.$page.props.showingMobileMenu),class:"p-1 mr-5 -ml-1 rounded-md md:hidden focus:outline-none focus:shadow-outline-purple","aria-label":"Menu"},He),t.usuario!=null?(i(),v(ue,{key:0,style:{cursor:"pointer"}},{trigger:p(()=>[a("div",Oe,[a("div",Ye,[a("div",Fe,[a("span",Xe,g(t.usuario.escuela),1)]),a("div",qe,[a("span",Ke,g(t.usuario.nombres),1)])]),Qe])]),content:p(()=>[m(pe,{href:l.route("logout"),method:"post",as:"button"},{icon:p(()=>[Je,T(" Salir del Sistema ")]),_:1},8,["href"])]),_:1})):b("",!0),t.usuario?(i(),c("div",We,[t.usuario.e_contra==1?(i(),c("div",et,[m(y(R),{visible:n.value,"onUpdate:visible":u[2]||(u[2]=f=>n.value=f),modal:"",header:"Cambiar contraseña",style:{width:"360px"}},{default:p(()=>[tt,a("div",st,[m(y(te),{type:"password",modelValue:r.value,"onUpdate:modelValue":u[1]||(u[1]=f=>r.value=f),style:{width:"315px"}},null,8,["modelValue"])]),a("div",nt,[m(y(P),{onClick:$,style:{width:"100%","justify-content":"center"}},{default:p(()=>[T(" Cambiar contraseña ")]),_:1})])]),_:1},8,["visible"])])):b("",!0)])):b("",!0),m(y(R),{visible:h.value,"onUpdate:visible":u[3]||(u[3]=f=>h.value=f),modal:"",header:"Notificación",style:{width:"520px"}},{default:p(()=>[I.value?(i(),c("div",ot,"Cargando...")):(i(),c("div",at,[d.value?(i(),c("div",{key:1,class:_(["space-y-3 p-3 rounded-md",{"border-l-4 border-green-500 bg-green-50":d.value.tipo==="success","border-l-4 border-yellow-500 bg-yellow-50":d.value.tipo==="warning"||d.value.tipo==="warn","border-l-4 border-red-500 bg-red-50":d.value.tipo==="error","border-l-4 border-blue-500 bg-blue-50":!["success","warning","warn","error"].includes(d.value.tipo)}])},[a("div",lt,g(d.value.titulo||"Aviso"),1),d.value.imagen_url?(i(),c("div",rt,[a("img",{src:d.value.imagen_url,alt:"imagen de la notificación",class:"w-full rounded-md max-h-72 object-contain"},null,8,dt)])):b("",!0),a("div",ct,g(d.value.mensaje),1),a("div",ut,[d.value.url?(i(),c("a",{key:0,href:d.value.url,target:"_blank",rel:"noopener",class:"px-3 py-2 rounded-md border text-sm hover:bg-gray-50 underline"}," Ir al enlace ",8,pt)):b("",!0),m(y(P),{onClick:O},{default:p(()=>[T(" Leí la notificación y estoy informado ")]),_:1})])],2)):(i(),c("div",it,"No hay notificaciones pendientes."))]))]),_:1},8,["visible"])])]))}};export{vt as _,ze as a,yt as b,bt as c,ve as d,me as e,Le as s};
