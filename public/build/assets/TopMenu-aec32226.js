import{e as i,c as d,A as V,M as oe,f as s,E as L,b as C,w as x,d as v,K as ae,F as Q,r as k,o as ie,O as ke,T as le,j as I,D as h,P as re,B,v as de,y as R,U as G,x as W,z as q,n as D,t as _,i as b,G as j,g as T,Q as P,R as Ee,m as Le,L as Ie,u as ze,h as $e,a as F}from"./app-e101615c.js";import{R as ce,a as ue,b as J,c as ee,d as te,e as ne,s as X}from"./index.esm-896c8d8f.js";import{a as me}from"./inputtext.esm-f637a8ea.js";import{s as pe}from"./overlayeventbus.esm-d9c2702a.js";import{s as O}from"./password.esm-8b5b97bd.js";const Se={class:"absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg","aria-hidden":"true"},De={class:"flex"},Te={class:"ml-4"},$n={__name:"NavLink",props:["href","active"],setup(e){return(n,t)=>(i(),d(Q,null,[V(s("span",Se,null,512),[[oe,e.active]]),s("div",De,[L(n.$slots,"icon"),C(v(ae),{href:e.href,class:"inline-flex items-center justify-between w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800"},{default:x(()=>[s("span",Te,[L(n.$slots,"default")])]),_:3},8,["href"])])],64))}},Sn="/build/assets/logotiny-e0fccd92.png",Be={class:"flex flex-shrink-0 items-center space-x-6"},Me={class:"relative"},Pe={class:"absolute right-0 mt-2 w-56 rounded-md border border-gray-100 bg-white p-2 text-gray-600 shadow-md space-y-2","aria-label":"submenu"},Ve={__name:"Dropdown",setup(e){const n=k(!1),t=l=>{n.value&&l.keyCode===27&&(n.value=!1)};return ie(()=>document.addEventListener("keydown",t)),ke(()=>document.removeEventListener("keydown",t)),(l,a)=>(i(),d("div",null,[s("ul",Be,[s("li",Me,[s("div",{onClick:a[0]||(a[0]=o=>n.value=!n.value)},[L(l.$slots,"trigger")]),C(le,{"leave-active-class":"transition duration-150 ease-in","leave-from-class":"opacity-100","leave-to-class":"opacity-0"},{default:x(()=>[V(s("ul",Pe,[s("li",null,[L(l.$slots,"content")])],512),[[oe,n.value]])]),_:3})])])]))}},Re={__name:"DropdownLink",setup(e){return(n,t)=>(i(),I(v(ae),{class:"inline-flex w-full items-center rounded-md px-2 py-1 text-sm font-semibold transition-colors duration-150 hover:bg-gray-100 hover:text-gray-800"},{default:x(()=>[L(n.$slots,"icon"),s("span",null,[L(n.$slots,"default")])]),_:3}))}};function Ae(e,n){const{onFocusIn:t,onFocusOut:l}=n.value||{};e.$_pfocustrap_mutationobserver=new MutationObserver(a=>{a.forEach(o=>{if(o.type==="childList"&&!e.contains(document.activeElement)){const g=m=>{const c=h.isFocusableElement(m)?m:h.getFirstFocusableElement(m);return re.isNotEmpty(c)?c:g(m.nextSibling)};h.focus(g(o.nextSibling))}})}),e.$_pfocustrap_mutationobserver.disconnect(),e.$_pfocustrap_mutationobserver.observe(e,{childList:!0}),e.$_pfocustrap_focusinlistener=a=>t&&t(a),e.$_pfocustrap_focusoutlistener=a=>l&&l(a),e.addEventListener("focusin",e.$_pfocustrap_focusinlistener),e.addEventListener("focusout",e.$_pfocustrap_focusoutlistener)}function se(e){e.$_pfocustrap_mutationobserver&&e.$_pfocustrap_mutationobserver.disconnect(),e.$_pfocustrap_focusinlistener&&e.removeEventListener("focusin",e.$_pfocustrap_focusinlistener)&&(e.$_pfocustrap_focusinlistener=null),e.$_pfocustrap_focusoutlistener&&e.removeEventListener("focusout",e.$_pfocustrap_focusoutlistener)&&(e.$_pfocustrap_focusoutlistener=null)}function He(e,n){const{autoFocusSelector:t="",firstFocusableSelector:l="",autoFocus:a=!1}=n.value||{};let o=h.getFirstFocusableElement(e,`[autofocus]:not(.p-hidden-focusable)${t}`);a&&!o&&(o=h.getFirstFocusableElement(e,`:not(.p-hidden-focusable)${l}`)),h.focus(o)}function Fe(e){const{currentTarget:n,relatedTarget:t}=e,l=t===n.$_pfocustrap_lasthiddenfocusableelement?h.getFirstFocusableElement(n.parentElement,`:not(.p-hidden-focusable)${n.$_pfocustrap_focusableselector}`):n.$_pfocustrap_lasthiddenfocusableelement;h.focus(l)}function je(e){const{currentTarget:n,relatedTarget:t}=e,l=t===n.$_pfocustrap_firsthiddenfocusableelement?h.getLastFocusableElement(n.parentElement,`:not(.p-hidden-focusable)${n.$_pfocustrap_focusableselector}`):n.$_pfocustrap_firsthiddenfocusableelement;h.focus(l)}function Ne(e,n){const{tabIndex:t=0,firstFocusableSelector:l="",lastFocusableSelector:a=""}=n.value||{},o=c=>{const r=document.createElement("span");return r.classList="p-hidden-accessible p-hidden-focusable",r.tabIndex=t,r.setAttribute("aria-hidden","true"),r.setAttribute("role","presentation"),r.addEventListener("focus",c),r},g=o(Fe),m=o(je);g.$_pfocustrap_lasthiddenfocusableelement=m,g.$_pfocustrap_focusableselector=l,m.$_pfocustrap_firsthiddenfocusableelement=g,m.$_pfocustrap_focusableselector=a,e.prepend(g),e.append(m)}const Oe={mounted(e,n){const{disabled:t}=n.value||{};t||(Ne(e,n),Ae(e,n),He(e,n))},updated(e,n){const{disabled:t}=n.value||{};t&&se(e)},unmounted(e){se(e)}};var fe={name:"WindowMaximizeIcon",extends:me};const Ze=s("g",{"clip-path":"url(#clip0_414_20927)"},[s("path",{"fill-rule":"evenodd","clip-rule":"evenodd",d:"M7 14H11.8C12.3835 14 12.9431 13.7682 13.3556 13.3556C13.7682 12.9431 14 12.3835 14 11.8V2.2C14 1.61652 13.7682 1.05694 13.3556 0.644365C12.9431 0.231785 12.3835 0 11.8 0H2.2C1.61652 0 1.05694 0.231785 0.644365 0.644365C0.231785 1.05694 0 1.61652 0 2.2V7C0 7.15913 0.063214 7.31174 0.175736 7.42426C0.288258 7.53679 0.44087 7.6 0.6 7.6C0.75913 7.6 0.911742 7.53679 1.02426 7.42426C1.13679 7.31174 1.2 7.15913 1.2 7V2.2C1.2 1.93478 1.30536 1.68043 1.49289 1.49289C1.68043 1.30536 1.93478 1.2 2.2 1.2H11.8C12.0652 1.2 12.3196 1.30536 12.5071 1.49289C12.6946 1.68043 12.8 1.93478 12.8 2.2V11.8C12.8 12.0652 12.6946 12.3196 12.5071 12.5071C12.3196 12.6946 12.0652 12.8 11.8 12.8H7C6.84087 12.8 6.68826 12.8632 6.57574 12.9757C6.46321 13.0883 6.4 13.2409 6.4 13.4C6.4 13.5591 6.46321 13.7117 6.57574 13.8243C6.68826 13.9368 6.84087 14 7 14ZM9.77805 7.42192C9.89013 7.534 10.0415 7.59788 10.2 7.59995C10.3585 7.59788 10.5099 7.534 10.622 7.42192C10.7341 7.30985 10.798 7.15844 10.8 6.99995V3.94242C10.8066 3.90505 10.8096 3.86689 10.8089 3.82843C10.8079 3.77159 10.7988 3.7157 10.7824 3.6623C10.756 3.55552 10.701 3.45698 10.622 3.37798C10.5099 3.2659 10.3585 3.20202 10.2 3.19995H7.00002C6.84089 3.19995 6.68828 3.26317 6.57576 3.37569C6.46324 3.48821 6.40002 3.64082 6.40002 3.79995C6.40002 3.95908 6.46324 4.11169 6.57576 4.22422C6.68828 4.33674 6.84089 4.39995 7.00002 4.39995H8.80006L6.19997 7.00005C6.10158 7.11005 6.04718 7.25246 6.04718 7.40005C6.04718 7.54763 6.10158 7.69004 6.19997 7.80005C6.30202 7.91645 6.44561 7.98824 6.59997 8.00005C6.75432 7.98824 6.89791 7.91645 6.99997 7.80005L9.60002 5.26841V6.99995C9.6021 7.15844 9.66598 7.30985 9.77805 7.42192ZM1.4 14H3.8C4.17066 13.9979 4.52553 13.8498 4.78763 13.5877C5.04973 13.3256 5.1979 12.9707 5.2 12.6V10.2C5.1979 9.82939 5.04973 9.47452 4.78763 9.21242C4.52553 8.95032 4.17066 8.80215 3.8 8.80005H1.4C1.02934 8.80215 0.674468 8.95032 0.412371 9.21242C0.150274 9.47452 0.00210008 9.82939 0 10.2V12.6C0.00210008 12.9707 0.150274 13.3256 0.412371 13.5877C0.674468 13.8498 1.02934 13.9979 1.4 14ZM1.25858 10.0586C1.29609 10.0211 1.34696 10 1.4 10H3.8C3.85304 10 3.90391 10.0211 3.94142 10.0586C3.97893 10.0961 4 10.147 4 10.2V12.6C4 12.6531 3.97893 12.704 3.94142 12.7415C3.90391 12.779 3.85304 12.8 3.8 12.8H1.4C1.34696 12.8 1.29609 12.779 1.25858 12.7415C1.22107 12.704 1.2 12.6531 1.2 12.6V10.2C1.2 10.147 1.22107 10.0961 1.25858 10.0586Z",fill:"currentColor"})],-1),Ue=s("defs",null,[s("clipPath",{id:"clip0_414_20927"},[s("rect",{width:"14",height:"14",fill:"white"})])],-1),Ke=[Ze,Ue];function Ye(e,n,t,l,a,o){return i(),d("svg",B({width:"14",height:"14",viewBox:"0 0 14 14",fill:"none",xmlns:"http://www.w3.org/2000/svg"},e.pti()),Ke,16)}fe.render=Ye;var he={name:"WindowMinimizeIcon",extends:me};const Xe=s("g",{"clip-path":"url(#clip0_414_20939)"},[s("path",{"fill-rule":"evenodd","clip-rule":"evenodd",d:"M11.8 0H2.2C1.61652 0 1.05694 0.231785 0.644365 0.644365C0.231785 1.05694 0 1.61652 0 2.2V7C0 7.15913 0.063214 7.31174 0.175736 7.42426C0.288258 7.53679 0.44087 7.6 0.6 7.6C0.75913 7.6 0.911742 7.53679 1.02426 7.42426C1.13679 7.31174 1.2 7.15913 1.2 7V2.2C1.2 1.93478 1.30536 1.68043 1.49289 1.49289C1.68043 1.30536 1.93478 1.2 2.2 1.2H11.8C12.0652 1.2 12.3196 1.30536 12.5071 1.49289C12.6946 1.68043 12.8 1.93478 12.8 2.2V11.8C12.8 12.0652 12.6946 12.3196 12.5071 12.5071C12.3196 12.6946 12.0652 12.8 11.8 12.8H7C6.84087 12.8 6.68826 12.8632 6.57574 12.9757C6.46321 13.0883 6.4 13.2409 6.4 13.4C6.4 13.5591 6.46321 13.7117 6.57574 13.8243C6.68826 13.9368 6.84087 14 7 14H11.8C12.3835 14 12.9431 13.7682 13.3556 13.3556C13.7682 12.9431 14 12.3835 14 11.8V2.2C14 1.61652 13.7682 1.05694 13.3556 0.644365C12.9431 0.231785 12.3835 0 11.8 0ZM6.368 7.952C6.44137 7.98326 6.52025 7.99958 6.6 8H9.8C9.95913 8 10.1117 7.93678 10.2243 7.82426C10.3368 7.71174 10.4 7.55913 10.4 7.4C10.4 7.24087 10.3368 7.08826 10.2243 6.97574C10.1117 6.86321 9.95913 6.8 9.8 6.8H8.048L10.624 4.224C10.73 4.11026 10.7877 3.95982 10.7849 3.80438C10.7822 3.64894 10.7192 3.50063 10.6093 3.3907C10.4994 3.28077 10.3511 3.2178 10.1956 3.21506C10.0402 3.21232 9.88974 3.27002 9.776 3.376L7.2 5.952V4.2C7.2 4.04087 7.13679 3.88826 7.02426 3.77574C6.91174 3.66321 6.75913 3.6 6.6 3.6C6.44087 3.6 6.28826 3.66321 6.17574 3.77574C6.06321 3.88826 6 4.04087 6 4.2V7.4C6.00042 7.47975 6.01674 7.55862 6.048 7.632C6.07656 7.70442 6.11971 7.7702 6.17475 7.82524C6.2298 7.88029 6.29558 7.92344 6.368 7.952ZM1.4 8.80005H3.8C4.17066 8.80215 4.52553 8.95032 4.78763 9.21242C5.04973 9.47452 5.1979 9.82939 5.2 10.2V12.6C5.1979 12.9707 5.04973 13.3256 4.78763 13.5877C4.52553 13.8498 4.17066 13.9979 3.8 14H1.4C1.02934 13.9979 0.674468 13.8498 0.412371 13.5877C0.150274 13.3256 0.00210008 12.9707 0 12.6V10.2C0.00210008 9.82939 0.150274 9.47452 0.412371 9.21242C0.674468 8.95032 1.02934 8.80215 1.4 8.80005ZM3.94142 12.7415C3.97893 12.704 4 12.6531 4 12.6V10.2C4 10.147 3.97893 10.0961 3.94142 10.0586C3.90391 10.0211 3.85304 10 3.8 10H1.4C1.34696 10 1.29609 10.0211 1.25858 10.0586C1.22107 10.0961 1.2 10.147 1.2 10.2V12.6C1.2 12.6531 1.22107 12.704 1.25858 12.7415C1.29609 12.779 1.34696 12.8 1.4 12.8H3.8C3.85304 12.8 3.90391 12.779 3.94142 12.7415Z",fill:"currentColor"})],-1),Ge=s("defs",null,[s("clipPath",{id:"clip0_414_20939"},[s("rect",{width:"14",height:"14",fill:"white"})])],-1),We=[Xe,Ge];function qe(e,n,t,l,a,o){return i(),d("svg",B({width:"14",height:"14",viewBox:"0 0 14 14",fill:"none",xmlns:"http://www.w3.org/2000/svg"},e.pti()),We,16)}he.render=qe;var Z={name:"Dialog",inheritAttrs:!1,emits:["update:visible","show","hide","after-hide","maximize","unmaximize","dragend"],props:{header:{type:null,default:null},footer:{type:null,default:null},visible:{type:Boolean,default:!1},modal:{type:Boolean,default:null},contentStyle:{type:null,default:null},contentClass:{type:String,default:null},contentProps:{type:null,default:null},rtl:{type:Boolean,default:null},maximizable:{type:Boolean,default:!1},dismissableMask:{type:Boolean,default:!1},closable:{type:Boolean,default:!0},closeOnEscape:{type:Boolean,default:!0},showHeader:{type:Boolean,default:!0},baseZIndex:{type:Number,default:0},autoZIndex:{type:Boolean,default:!0},position:{type:String,default:"center"},breakpoints:{type:Object,default:null},draggable:{type:Boolean,default:!0},keepInViewport:{type:Boolean,default:!0},minX:{type:Number,default:0},minY:{type:Number,default:0},appendTo:{type:String,default:"body"},closeIcon:{type:String,default:void 0},maximizeIcon:{type:String,default:void 0},minimizeIcon:{type:String,default:void 0},closeButtonProps:{type:null,default:null},_instance:null},provide(){return{dialogRef:de(()=>this._instance)}},data(){return{containerVisible:this.visible,maximized:!1,focusableMax:null,focusableClose:null}},documentKeydownListener:null,container:null,mask:null,content:null,headerContainer:null,footerContainer:null,maximizableButton:null,closeButton:null,styleElement:null,dragging:null,documentDragListener:null,documentDragEndListener:null,lastPageX:null,lastPageY:null,updated(){this.visible&&(this.containerVisible=this.visible)},beforeUnmount(){this.unbindDocumentState(),this.unbindGlobalListeners(),this.destroyStyle(),this.mask&&this.autoZIndex&&R.clear(this.mask),this.container=null,this.mask=null},mounted(){this.breakpoints&&this.createStyle()},methods:{close(){this.$emit("update:visible",!1)},onBeforeEnter(e){e.setAttribute(this.attributeSelector,"")},onEnter(){this.$emit("show"),this.focus(),this.enableDocumentSettings(),this.bindGlobalListeners(),this.autoZIndex&&R.set("modal",this.mask,this.baseZIndex+this.$primevue.config.zIndex.modal)},onBeforeLeave(){this.modal&&h.addClass(this.mask,"p-component-overlay-leave")},onLeave(){this.$emit("hide"),this.focusableClose=null,this.focusableMax=null},onAfterLeave(){this.autoZIndex&&R.clear(this.mask),this.containerVisible=!1,this.unbindDocumentState(),this.unbindGlobalListeners(),this.$emit("after-hide")},onMaskClick(e){this.dismissableMask&&this.modal&&this.mask===e.target&&this.close()},focus(){const e=t=>t.querySelector("[autofocus]");let n=this.$slots.footer&&e(this.footerContainer);n||(n=this.$slots.header&&e(this.headerContainer),n||(n=this.$slots.default&&e(this.content),n||(this.maximizable?(this.focusableMax=!0,n=this.maximizableButton):(this.focusableClose=!0,n=this.closeButton)))),n&&h.focus(n)},maximize(e){this.maximized?(this.maximized=!1,this.$emit("unmaximize",e)):(this.maximized=!0,this.$emit("maximize",e)),this.modal||(this.maximized?h.addClass(document.body,"p-overflow-hidden"):h.removeClass(document.body,"p-overflow-hidden"))},enableDocumentSettings(){(this.modal||this.maximizable&&this.maximized)&&h.addClass(document.body,"p-overflow-hidden")},unbindDocumentState(){(this.modal||this.maximizable&&this.maximized)&&h.removeClass(document.body,"p-overflow-hidden")},onKeyDown(e){e.code==="Escape"&&this.closeOnEscape&&this.close()},bindDocumentKeyDownListener(){this.documentKeydownListener||(this.documentKeydownListener=this.onKeyDown.bind(this),window.document.addEventListener("keydown",this.documentKeydownListener))},unbindDocumentKeyDownListener(){this.documentKeydownListener&&(window.document.removeEventListener("keydown",this.documentKeydownListener),this.documentKeydownListener=null)},getPositionClass(){const n=["left","right","top","topleft","topright","bottom","bottomleft","bottomright"].find(t=>t===this.position);return n?`p-dialog-${n}`:""},containerRef(e){this.container=e},maskRef(e){this.mask=e},contentRef(e){this.content=e},headerContainerRef(e){this.headerContainer=e},footerContainerRef(e){this.footerContainer=e},maximizableRef(e){this.maximizableButton=e},closeButtonRef(e){this.closeButton=e},createStyle(){if(!this.styleElement){this.styleElement=document.createElement("style"),this.styleElement.type="text/css",document.head.appendChild(this.styleElement);let e="";for(let n in this.breakpoints)e+=`
                        @media screen and (max-width: ${n}) {
                            .p-dialog[${this.attributeSelector}] {
                                width: ${this.breakpoints[n]} !important;
                            }
                        }
                    `;this.styleElement.innerHTML=e}},destroyStyle(){this.styleElement&&(document.head.removeChild(this.styleElement),this.styleElement=null)},initDrag(e){h.hasClass(e.target,"p-dialog-header-icon")||h.hasClass(e.target.parentElement,"p-dialog-header-icon")||this.draggable&&(this.dragging=!0,this.lastPageX=e.pageX,this.lastPageY=e.pageY,this.container.style.margin="0",h.addClass(document.body,"p-unselectable-text"))},bindGlobalListeners(){this.draggable&&(this.bindDocumentDragListener(),this.bindDocumentDragEndListener()),this.closeOnEscape&&this.closable&&this.bindDocumentKeyDownListener()},unbindGlobalListeners(){this.unbindDocumentDragListener(),this.unbindDocumentDragEndListener(),this.unbindDocumentKeyDownListener()},bindDocumentDragListener(){this.documentDragListener=e=>{if(this.dragging){let n=h.getOuterWidth(this.container),t=h.getOuterHeight(this.container),l=e.pageX-this.lastPageX,a=e.pageY-this.lastPageY,o=this.container.getBoundingClientRect(),g=o.left+l,m=o.top+a,c=h.getViewport();this.container.style.position="fixed",this.keepInViewport?(g>=this.minX&&g+n<c.width&&(this.lastPageX=e.pageX,this.container.style.left=g+"px"),m>=this.minY&&m+t<c.height&&(this.lastPageY=e.pageY,this.container.style.top=m+"px")):(this.lastPageX=e.pageX,this.container.style.left=g+"px",this.lastPageY=e.pageY,this.container.style.top=m+"px")}},window.document.addEventListener("mousemove",this.documentDragListener)},unbindDocumentDragListener(){this.documentDragListener&&(window.document.removeEventListener("mousemove",this.documentDragListener),this.documentDragListener=null)},bindDocumentDragEndListener(){this.documentDragEndListener=e=>{this.dragging&&(this.dragging=!1,h.removeClass(document.body,"p-unselectable-text"),this.$emit("dragend",e))},window.document.addEventListener("mouseup",this.documentDragEndListener)},unbindDocumentDragEndListener(){this.documentDragEndListener&&(window.document.removeEventListener("mouseup",this.documentDragEndListener),this.documentDragEndListener=null)}},computed:{maskClass(){return["p-dialog-mask",{"p-component-overlay p-component-overlay-enter":this.modal},this.getPositionClass()]},dialogClass(){return["p-dialog p-component",{"p-dialog-rtl":this.rtl,"p-dialog-maximized":this.maximizable&&this.maximized,"p-input-filled":this.$primevue.config.inputStyle==="filled","p-ripple-disabled":this.$primevue.config.ripple===!1}]},maximizeIconComponent(){return this.maximized?this.minimizeIcon?"span":"WindowMinimizeIcon":this.maximizeIcon?"span":"WindowMaximizeIcon"},maximizeIconClass(){return`p-dialog-header-maximize-icon ${this.maximized?this.minimizeIcon:this.maximizeIcon}`},ariaId(){return G()},ariaLabelledById(){return this.header!=null||this.$attrs["aria-labelledby"]!==null?this.ariaId+"_header":null},closeAriaLabel(){return this.$primevue.config.locale.aria?this.$primevue.config.locale.aria.close:void 0},attributeSelector(){return G()},contentStyleClass(){return["p-dialog-content",this.contentClass]}},directives:{ripple:ce,focustrap:Oe},components:{Portal:pe,WindowMinimizeIcon:he,WindowMaximizeIcon:fe,TimesIcon:ue}};const Qe=["aria-labelledby","aria-modal"],Je=["id"],et={class:"p-dialog-header-icons"},tt=["autofocus","tabindex"],nt=["autofocus","aria-label"];function st(e,n,t,l,a,o){const g=W("Portal"),m=q("ripple"),c=q("focustrap");return i(),I(g,{appendTo:t.appendTo},{default:x(()=>[a.containerVisible?(i(),d("div",{key:0,ref:o.maskRef,class:D(o.maskClass),onClick:n[3]||(n[3]=(...r)=>o.onMaskClick&&o.onMaskClick(...r))},[C(le,{name:"p-dialog",onBeforeEnter:o.onBeforeEnter,onEnter:o.onEnter,onBeforeLeave:o.onBeforeLeave,onLeave:o.onLeave,onAfterLeave:o.onAfterLeave,appear:""},{default:x(()=>[t.visible?V((i(),d("div",B({key:0,ref:o.containerRef,class:o.dialogClass,role:"dialog","aria-labelledby":o.ariaLabelledById,"aria-modal":t.modal},e.$attrs),[t.showHeader?(i(),d("div",{key:0,ref:o.headerContainerRef,class:"p-dialog-header",onMousedown:n[2]||(n[2]=(...r)=>o.initDrag&&o.initDrag(...r))},[L(e.$slots,"header",{},()=>[t.header?(i(),d("span",{key:0,id:o.ariaLabelledById,class:"p-dialog-title"},_(t.header),9,Je)):b("",!0)]),s("div",et,[t.maximizable?V((i(),d("button",{key:0,ref:o.maximizableRef,autofocus:a.focusableMax,class:"p-dialog-header-icon p-dialog-header-maximize p-link",onClick:n[0]||(n[0]=(...r)=>o.maximize&&o.maximize(...r)),type:"button",tabindex:t.maximizable?"0":"-1"},[L(e.$slots,"maximizeicon",{maximized:a.maximized},()=>[(i(),I(j(o.maximizeIconComponent),{class:D(o.maximizeIconClass)},null,8,["class"]))])],8,tt)),[[m]]):b("",!0),t.closable?V((i(),d("button",B({key:1,ref:o.closeButtonRef,autofocus:a.focusableClose,class:"p-dialog-header-icon p-dialog-header-close p-link",onClick:n[1]||(n[1]=(...r)=>o.close&&o.close(...r)),"aria-label":o.closeAriaLabel,type:"button"},t.closeButtonProps),[L(e.$slots,"closeicon",{},()=>[(i(),I(j(t.closeIcon?"span":"TimesIcon"),{class:D(["p-dialog-header-close-icon",t.closeIcon])},null,8,["class"]))])],16,nt)),[[m]]):b("",!0)])],544)):b("",!0),s("div",B({ref:o.contentRef,class:o.contentStyleClass,style:t.contentStyle},t.contentProps),[L(e.$slots,"default")],16),t.footer||e.$slots.footer?(i(),d("div",{key:1,ref:o.footerContainerRef,class:"p-dialog-footer"},[L(e.$slots,"footer",{},()=>[T(_(t.footer),1)])],512)):b("",!0)],16,Qe)),[[c,{disabled:!t.modal}]]):b("",!0)]),_:3},8,["onBeforeEnter","onEnter","onBeforeLeave","onLeave","onAfterLeave"])],2)):b("",!0)]),_:3},8,["appendTo"])}function ot(e,n){n===void 0&&(n={});var t=n.insertAt;if(!(!e||typeof document>"u")){var l=document.head||document.getElementsByTagName("head")[0],a=document.createElement("style");a.type="text/css",t==="top"&&l.firstChild?l.insertBefore(a,l.firstChild):l.appendChild(a),a.styleSheet?a.styleSheet.cssText=e:a.appendChild(document.createTextNode(e))}}var at=`
.p-dialog-mask {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    pointer-events: none;
}
.p-dialog-mask.p-component-overlay {
    pointer-events: auto;
}
.p-dialog {
    display: flex;
    flex-direction: column;
    pointer-events: auto;
    max-height: 90%;
    transform: scale(1);
}
.p-dialog-content {
    overflow-y: auto;
}
.p-dialog-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.p-dialog-footer {
    flex-shrink: 0;
}
.p-dialog .p-dialog-header-icons {
    display: flex;
    align-items: center;
}
.p-dialog .p-dialog-header-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
}

/* Fluid */
.p-fluid .p-dialog-footer .p-button {
    width: auto;
}

/* Animation */
/* Center */
.p-dialog-enter-active {
    transition: all 150ms cubic-bezier(0, 0, 0.2, 1);
}
.p-dialog-leave-active {
    transition: all 150ms cubic-bezier(0.4, 0, 0.2, 1);
}
.p-dialog-enter-from,
.p-dialog-leave-to {
    opacity: 0;
    transform: scale(0.7);
}

/* Top, Bottom, Left, Right, Top* and Bottom* */
.p-dialog-top .p-dialog,
.p-dialog-bottom .p-dialog,
.p-dialog-left .p-dialog,
.p-dialog-right .p-dialog,
.p-dialog-topleft .p-dialog,
.p-dialog-topright .p-dialog,
.p-dialog-bottomleft .p-dialog,
.p-dialog-bottomright .p-dialog {
    margin: 0.75rem;
    transform: translate3d(0px, 0px, 0px);
}
.p-dialog-top .p-dialog-enter-active,
.p-dialog-top .p-dialog-leave-active,
.p-dialog-bottom .p-dialog-enter-active,
.p-dialog-bottom .p-dialog-leave-active,
.p-dialog-left .p-dialog-enter-active,
.p-dialog-left .p-dialog-leave-active,
.p-dialog-right .p-dialog-enter-active,
.p-dialog-right .p-dialog-leave-active,
.p-dialog-topleft .p-dialog-enter-active,
.p-dialog-topleft .p-dialog-leave-active,
.p-dialog-topright .p-dialog-enter-active,
.p-dialog-topright .p-dialog-leave-active,
.p-dialog-bottomleft .p-dialog-enter-active,
.p-dialog-bottomleft .p-dialog-leave-active,
.p-dialog-bottomright .p-dialog-enter-active,
.p-dialog-bottomright .p-dialog-leave-active {
    transition: all 0.3s ease-out;
}
.p-dialog-top .p-dialog-enter-from,
.p-dialog-top .p-dialog-leave-to {
    transform: translate3d(0px, -100%, 0px);
}
.p-dialog-bottom .p-dialog-enter-from,
.p-dialog-bottom .p-dialog-leave-to {
    transform: translate3d(0px, 100%, 0px);
}
.p-dialog-left .p-dialog-enter-from,
.p-dialog-left .p-dialog-leave-to,
.p-dialog-topleft .p-dialog-enter-from,
.p-dialog-topleft .p-dialog-leave-to,
.p-dialog-bottomleft .p-dialog-enter-from,
.p-dialog-bottomleft .p-dialog-leave-to {
    transform: translate3d(-100%, 0px, 0px);
}
.p-dialog-right .p-dialog-enter-from,
.p-dialog-right .p-dialog-leave-to,
.p-dialog-topright .p-dialog-enter-from,
.p-dialog-topright .p-dialog-leave-to,
.p-dialog-bottomright .p-dialog-enter-from,
.p-dialog-bottomright .p-dialog-leave-to {
    transform: translate3d(100%, 0px, 0px);
}

/* Maximize */
.p-dialog-maximized {
    -webkit-transition: none;
    transition: none;
    transform: none;
    width: 100vw !important;
    height: 100vh !important;
    top: 0px !important;
    left: 0px !important;
    max-height: 100%;
    height: 100%;
}
.p-dialog-maximized .p-dialog-content {
    flex-grow: 1;
}

/* Position */
.p-dialog-left {
    justify-content: flex-start;
}
.p-dialog-right {
    justify-content: flex-end;
}
.p-dialog-top {
    align-items: flex-start;
}
.p-dialog-topleft {
    justify-content: flex-start;
    align-items: flex-start;
}
.p-dialog-topright {
    justify-content: flex-end;
    align-items: flex-start;
}
.p-dialog-bottom {
    align-items: flex-end;
}
.p-dialog-bottomleft {
    justify-content: flex-start;
    align-items: flex-end;
}
.p-dialog-bottomright {
    justify-content: flex-end;
    align-items: flex-end;
}
.p-confirm-dialog .p-dialog-content {
    display: flex;
    align-items: center;
}
`;ot(at);Z.render=st;var ge={name:"ToastMessage",emits:["close"],props:{message:{type:null,default:null},templates:{type:Object,default:null},closeIcon:{type:String,default:null},infoIcon:{type:String,default:null},warnIcon:{type:String,default:null},errorIcon:{type:String,default:null},successIcon:{type:String,default:null},closeButtonProps:{type:null,default:null}},closeTimeout:null,mounted(){this.message.life&&(this.closeTimeout=setTimeout(()=>{this.close({message:this.message,type:"life-end"})},this.message.life))},beforeUnmount(){this.clearCloseTimeout()},methods:{close(e){this.$emit("close",e)},onCloseClick(){this.clearCloseTimeout(),this.close({message:this.message,type:"close"})},clearCloseTimeout(){this.closeTimeout&&(clearTimeout(this.closeTimeout),this.closeTimeout=null)}},computed:{containerClass(){return["p-toast-message",this.message.styleClass,{"p-toast-message-info":this.message.severity==="info","p-toast-message-warn":this.message.severity==="warn","p-toast-message-error":this.message.severity==="error","p-toast-message-success":this.message.severity==="success"}]},iconComponent(){return{info:!this.infoIcon&&J,success:!this.successIcon&&ee,warn:!this.warnIcon&&te,error:!this.errorIcon&&ne}[this.message.severity]},iconClass(){return[{[this.infoIcon]:this.message.severity==="info",[this.warnIcon]:this.message.severity==="warn",[this.errorIcon]:this.message.severity==="error",[this.successIcon]:this.message.severity==="success"}]},closeAriaLabel(){return this.$primevue.config.locale.aria?this.$primevue.config.locale.aria.close:void 0}},components:{TimesIcon:ue,InfoCircleIcon:J,CheckIcon:ee,ExclamationTriangleIcon:te,TimesCircleIcon:ne},directives:{ripple:ce}};const it={class:"p-toast-message-text"},lt={class:"p-toast-summary"},rt={class:"p-toast-detail"},dt={key:2},ct=["aria-label"];function ut(e,n,t,l,a,o){const g=q("ripple");return i(),d("div",{class:D(o.containerClass),role:"alert","aria-live":"assertive","aria-atomic":"true"},[s("div",{class:D(["p-toast-message-content",t.message.contentStyleClass])},[t.templates.message?(i(),I(j(t.templates.message),{key:1,message:t.message},null,8,["message"])):(i(),d(Q,{key:0},[(i(),I(j(t.templates.icon?t.templates.icon:o.iconComponent.name?o.iconComponent:"span"),{class:D([o.iconClass,"p-toast-message-icon"])},null,8,["class"])),s("div",it,[s("span",lt,_(t.message.summary),1),s("div",rt,_(t.message.detail),1)])],64)),T(_(t.message.closable)+" ",1),t.message.closable!==!1?(i(),d("div",dt,[V((i(),d("button",B({class:"p-toast-icon-close p-link",type:"button","aria-label":o.closeAriaLabel,onClick:n[0]||(n[0]=(...m)=>o.onCloseClick&&o.onCloseClick(...m)),autofocus:""},t.closeButtonProps),[(i(),I(j(t.templates.closeicon||"TimesIcon"),{class:D(["p-toast-icon-close-icon",t.closeIcon])},null,8,["class"]))],16,ct)),[[g]])])):b("",!0)],2)],2)}ge.render=ut;var mt=0,ve={name:"Toast",inheritAttrs:!1,emits:["close","life-end"],props:{group:{type:String,default:null},position:{type:String,default:"top-right"},autoZIndex:{type:Boolean,default:!0},baseZIndex:{type:Number,default:0},breakpoints:{type:Object,default:null},closeIcon:{type:String,default:void 0},infoIcon:{type:String,default:void 0},warnIcon:{type:String,default:void 0},errorIcon:{type:String,default:void 0},successIcon:{type:String,default:void 0},closeButtonProps:{type:null,default:null}},data(){return{messages:[]}},styleElement:null,mounted(){P.on("add",this.onAdd),P.on("remove-group",this.onRemoveGroup),P.on("remove-all-groups",this.onRemoveAllGroups),this.breakpoints&&this.createStyle()},beforeUnmount(){this.destroyStyle(),this.$refs.container&&this.autoZIndex&&R.clear(this.$refs.container),P.off("add",this.onAdd),P.off("remove-group",this.onRemoveGroup),P.off("remove-all-groups",this.onRemoveAllGroups)},methods:{add(e){e.id==null&&(e.id=mt++),this.messages=[...this.messages,e]},remove(e){let n=-1;for(let t=0;t<this.messages.length;t++)if(this.messages[t]===e.message){n=t;break}this.messages.splice(n,1),this.$emit(e.type,{message:e.message})},onAdd(e){this.group==e.group&&this.add(e)},onRemoveGroup(e){this.group===e&&(this.messages=[])},onRemoveAllGroups(){this.messages=[]},onEnter(){this.$refs.container.setAttribute(this.attributeSelector,""),this.autoZIndex&&R.set("modal",this.$refs.container,this.baseZIndex||this.$primevue.config.zIndex.modal)},onLeave(){this.$refs.container&&this.autoZIndex&&re.isEmpty(this.messages)&&setTimeout(()=>{R.clear(this.$refs.container)},200)},createStyle(){if(!this.styleElement){this.styleElement=document.createElement("style"),this.styleElement.type="text/css",document.head.appendChild(this.styleElement);let e="";for(let n in this.breakpoints){let t="";for(let l in this.breakpoints[n])t+=l+":"+this.breakpoints[n][l]+"!important;";e+=`
                        @media screen and (max-width: ${n}) {
                            .p-toast[${this.attributeSelector}] {
                                ${t}
                            }
                        }
                    `}this.styleElement.innerHTML=e}},destroyStyle(){this.styleElement&&(document.head.removeChild(this.styleElement),this.styleElement=null)}},computed:{containerClass(){return["p-toast p-component p-toast-"+this.position,{"p-input-filled":this.$primevue.config.inputStyle==="filled","p-ripple-disabled":this.$primevue.config.ripple===!1}]},attributeSelector(){return G()}},components:{ToastMessage:ge,Portal:pe}};function pt(e,n,t,l,a,o){const g=W("ToastMessage"),m=W("Portal");return i(),I(m,null,{default:x(()=>[s("div",B({ref:"container",class:o.containerClass},e.$attrs),[C(Ee,{name:"p-toast-message",tag:"div",onEnter:o.onEnter,onLeave:o.onLeave},{default:x(()=>[(i(!0),d(Q,null,Le(a.messages,c=>(i(),I(g,{key:c.id,message:c,templates:e.$slots,closeIcon:t.closeIcon,infoIcon:t.infoIcon,warnIcon:t.warnIcon,errorIcon:t.errorIcon,successIcon:t.successIcon,closeButtonProps:t.closeButtonProps,onClose:n[0]||(n[0]=r=>o.remove(r))},null,8,["message","templates","closeIcon","infoIcon","warnIcon","errorIcon","successIcon","closeButtonProps"]))),128))]),_:1},8,["onEnter","onLeave"])],16)]),_:1})}function ft(e,n){n===void 0&&(n={});var t=n.insertAt;if(!(!e||typeof document>"u")){var l=document.head||document.getElementsByTagName("head")[0],a=document.createElement("style");a.type="text/css",t==="top"&&l.firstChild?l.insertBefore(a,l.firstChild):l.appendChild(a),a.styleSheet?a.styleSheet.cssText=e:a.appendChild(document.createTextNode(e))}}var ht=`
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
`;ft(ht);ve.render=pt;const gt={class:"z-10 py-4 bg-white shadow-md",style:{"background-color":"white",height:"75px"}},vt={class:"container flex justify-between items-center px-6 mx-auto h-full text-purple-600 md:justify-end"},bt=s("svg",{class:"w-6 h-6","aria-hidden":"true",fill:"currentColor",viewBox:"0 0 20 20"},[s("path",{"fill-rule":"evenodd",d:"M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z","clip-rule":"evenodd"})],-1),yt=[bt],_t={class:"flex",style:{"align-items":"center",height:"37px",color:"#000000D9"}},Ct={style:{"text-align":"end","margin-top":"0px"}},xt={style:{width:"200px","white-space":"nowrap","text-overflow":"ellipsis",overflow:"hidden"}},wt={style:{"font-size":"0.9rem"}},kt={style:{"margin-top":"-7px"}},Et={style:{"font-size":"0.9rem","font-weight":"bold"}},Lt=s("div",{style:{"margin-left":"10px"}},[s("i",{class:"pi pi-angle-down"})],-1),It=s("i",{class:"pi pi-user mr-3"},null,-1),zt=s("i",{class:"pi pi-sign-out mr-3"},null,-1),$t={key:0,class:"py-5 text-center"},St=s("i",{class:"pi pi-spin pi-spinner mr-2"},null,-1),Dt={key:1},Tt={class:"grid grid-cols-1 sm:grid-cols-2 gap-4"},Bt=s("span",{class:"text-sm text-gray-500"}," Nombres y apellidos ",-1),Mt={class:"font-semibold text-gray-800"},Pt=s("span",{class:"text-sm text-gray-500"}," Correo ",-1),Vt={class:"font-semibold text-gray-800 break-all"},Rt=s("span",{class:"text-sm text-gray-500"}," Rol ",-1),At={class:"font-semibold text-gray-800"},Ht={key:0},Ft=s("span",{class:"text-sm text-gray-500"}," Documento ",-1),jt={class:"font-semibold text-gray-800"},Nt={key:1},Ot=s("span",{class:"text-sm text-gray-500"}," Código de estudiante ",-1),Zt={class:"font-semibold text-gray-800"},Ut={key:2},Kt=s("span",{class:"text-sm text-gray-500"}," Escuela Profesional ",-1),Yt={class:"font-semibold text-gray-800"},Xt={key:3},Gt=s("span",{class:"text-sm text-gray-500"}," Programa ",-1),Wt={class:"font-semibold text-gray-800"},qt={key:4},Qt=s("span",{class:"text-sm text-gray-500"}," Celular ",-1),Jt={class:"font-semibold text-gray-800"},en={class:"border-t mt-6 pt-5"},tn=s("h3",{class:"font-bold text-gray-800 mb-4"}," Cambiar contraseña ",-1),nn={class:"mb-3"},sn=s("label",{class:"block mb-1"}," Contraseña actual ",-1),on={class:"mb-3"},an=s("label",{class:"block mb-1"}," Nueva contraseña ",-1),ln=s("small",{class:"text-gray-500"}," La contraseña debe tener como mínimo 5 caracteres. ",-1),rn={class:"mb-4"},dn=s("label",{class:"block mb-1"}," Confirmar nueva contraseña ",-1),cn={class:"flex justify-end"},un=s("label",{class:"block mb-2"}," Nueva contraseña ",-1),mn=s("small",{class:"text-gray-500"}," La contraseña debe tener como mínimo 5 caracteres. ",-1),pn={class:"flex justify-end mt-5"},fn={key:0,class:"py-4 text-center"},hn=s("i",{class:"pi pi-spin pi-spinner mr-2"},null,-1),gn={key:1},vn={key:0,class:"text-gray-500"},bn={class:"font-semibold text-base"},yn={key:0,class:"mt-1"},_n=["src"],Cn={class:"font-bold text-sm whitespace-pre-line"},xn={class:"flex justify-end gap-2 mt-4"},wn=["href"],Dn={__name:"TopMenu",setup(e){const n=Ie(),t=de(()=>n.props.auth_user??null),l=ze(),a=(f,u,p)=>{l.add({severity:f,summary:u,detail:p,life:4e3})},o=k(!1),g=k(!1),m=k(!1),c=k(null),r=k({actual:"",nueva:"",confirmacion:""}),be=async()=>{var f,u,p,$;if(o.value=!0,!c.value){g.value=!0;try{const z=await F.get("/mi-perfil");c.value=z.data.datos}catch(z){a("error","ERROR",((u=(f=z.response)==null?void 0:f.data)==null?void 0:u.mensaje)||(($=(p=z.response)==null?void 0:p.data)==null?void 0:$.message)||"No se pudo cargar la información del perfil.")}finally{g.value=!1}}},ye=async()=>{var f,u,p,$,z,A,H,E;if(!r.value.actual||!r.value.nueva||!r.value.confirmacion){a("warn","DATOS INCOMPLETOS","Complete todos los campos.");return}if(r.value.nueva.length<=4){a("warn","CONTRASEÑA","La nueva contraseña debe tener como mínimo 5 caracteres.");return}if(r.value.nueva!==r.value.confirmacion){a("warn","CONTRASEÑA","La confirmación de la contraseña no coincide.");return}m.value=!0;try{const w=await F.post("/save-contrasenia",{current_password:r.value.actual,password:r.value.nueva,password_confirmation:r.value.confirmacion});if(a(w.data.tipo,w.data.titulo,w.data.mensaje),!w.data.estado)return;r.value={actual:"",nueva:"",confirmacion:""}}catch(w){const S=(u=(f=w.response)==null?void 0:f.data)==null?void 0:u.errors,we=((p=S==null?void 0:S.current_password)==null?void 0:p[0])||(($=S==null?void 0:S.password)==null?void 0:$[0])||((A=(z=w.response)==null?void 0:z.data)==null?void 0:A.mensaje)||((E=(H=w.response)==null?void 0:H.data)==null?void 0:E.message)||"No se pudo modificar la contraseña.";a("error","ERROR",we)}finally{m.value=!1}},U=k(!0),M=k(""),K=k(!1),_e=async()=>{var f,u,p,$,z,A,H;if(!M.value){a("warn","CONTRASEÑA","Ingrese una nueva contraseña.");return}if(M.value.length<=4){a("warn","CONTRASEÑA","La contraseña debe tener como mínimo 5 caracteres.");return}K.value=!0;try{const E=await F.post("/save-contrasenia",{contra:M.value});if(a(E.data.tipo,E.data.titulo,E.data.mensaje),!E.data.estado)return;M.value="",U.value=!1,t.value&&(t.value.e_contra=0)}catch(E){const w=(u=(f=E.response)==null?void 0:f.data)==null?void 0:u.errors,S=((p=w==null?void 0:w.contra)==null?void 0:p[0])||((z=($=E.response)==null?void 0:$.data)==null?void 0:z.mensaje)||((H=(A=E.response)==null?void 0:A.data)==null?void 0:H.message)||"No se pudo modificar la contraseña.";a("error","ERROR",S)}finally{K.value=!1}},N=k(!1),y=k(null),Y=k(!1),Ce=async()=>{Y.value=!0;try{const{data:f}=await F.post("/get-noti");y.value=(f==null?void 0:f.datos)??null,y.value&&(N.value=!0)}catch(f){console.error("Error al cargar notificación:",f)}finally{Y.value=!1}},xe=async()=>{var f;try{(f=y.value)!=null&&f.id&&await F.post(`/read-noti/${y.value.id}`)}catch(u){console.error("Error al marcar notificación:",u)}finally{N.value=!1,y.value=null}};return ie(async()=>{await Ce()}),(f,u)=>(i(),d("header",gt,[s("div",vt,[s("button",{type:"button",onClick:u[0]||(u[0]=p=>f.$page.props.showingMobileMenu=!f.$page.props.showingMobileMenu),class:"p-1 mr-5 -ml-1 rounded-md md:hidden focus:outline-none focus:shadow-outline-purple","aria-label":"Menu"},yt),v(t)?(i(),I(Ve,{key:0,style:{cursor:"pointer"}},{trigger:x(()=>[s("div",_t,[s("div",Ct,[s("div",xt,[s("span",wt,_(v(t).escuela||v(t).programa||v(t).nombre_rol||""),1)]),s("div",kt,[s("span",Et,_(v(t).nombres||v(t).email||"Usuario"),1)])]),Lt])]),content:x(()=>[s("button",{type:"button",onClick:be,class:"inline-flex w-full items-center rounded-md px-2 py-2 text-sm font-semibold transition-colors duration-150 hover:bg-gray-100 hover:text-gray-800"},[It,T(" Mi perfil ")]),C(Re,{href:f.route("logout"),method:"post",as:"button"},{icon:x(()=>[zt]),default:x(()=>[T(" Salir del Sistema ")]),_:1},8,["href"])]),_:1})):b("",!0),C(v(Z),{visible:o.value,"onUpdate:visible":u[4]||(u[4]=p=>o.value=p),modal:"",header:"Mi perfil",style:{width:"520px"}},{default:x(()=>[g.value?(i(),d("div",$t,[St,T(" Cargando información... ")])):c.value?(i(),d("div",Dt,[s("div",Tt,[s("div",null,[Bt,s("p",Mt,_(c.value.nombre_completo||"-"),1)]),s("div",null,[Pt,s("p",Vt,_(c.value.email||"-"),1)]),s("div",null,[Rt,s("p",At,_(c.value.rol||"-"),1)]),c.value.documento?(i(),d("div",Ht,[Ft,s("p",jt,_(c.value.documento),1)])):b("",!0),c.value.codigo_est?(i(),d("div",Nt,[Ot,s("p",Zt,_(c.value.codigo_est),1)])):b("",!0),c.value.escuela?(i(),d("div",Ut,[Kt,s("p",Yt,_(c.value.escuela),1)])):b("",!0),c.value.programa?(i(),d("div",Xt,[Gt,s("p",Wt,_(c.value.programa),1)])):b("",!0),c.value.telefono?(i(),d("div",qt,[Qt,s("p",Jt,_(c.value.telefono),1)])):b("",!0)]),s("div",en,[tn,s("div",nn,[sn,C(v(O),{modelValue:r.value.actual,"onUpdate:modelValue":u[1]||(u[1]=p=>r.value.actual=p),toggleMask:"",feedback:!1,class:"w-full",inputClass:"w-full",autocomplete:"current-password"},null,8,["modelValue"])]),s("div",on,[an,C(v(O),{modelValue:r.value.nueva,"onUpdate:modelValue":u[2]||(u[2]=p=>r.value.nueva=p),toggleMask:"",feedback:!1,class:"w-full",inputClass:"w-full",autocomplete:"new-password"},null,8,["modelValue"]),ln]),s("div",rn,[dn,C(v(O),{modelValue:r.value.confirmacion,"onUpdate:modelValue":u[3]||(u[3]=p=>r.value.confirmacion=p),toggleMask:"",feedback:!1,class:"w-full",inputClass:"w-full",autocomplete:"new-password"},null,8,["modelValue"])]),s("div",cn,[C(v(X),{type:"button",label:"Cambiar contraseña",icon:"pi pi-key",loading:m.value,onClick:$e(ye,["prevent"])},null,8,["loading","onClick"])])])])):b("",!0)]),_:1},8,["visible"]),v(t)&&v(t).e_contra==1?(i(),I(v(Z),{key:1,visible:U.value,"onUpdate:visible":u[6]||(u[6]=p=>U.value=p),modal:"",header:"Cambiar contraseña",closable:!1,style:{width:"360px"}},{default:x(()=>[un,C(v(O),{modelValue:M.value,"onUpdate:modelValue":u[5]||(u[5]=p=>M.value=p),toggleMask:"",feedback:!1,class:"w-full",inputClass:"w-full",autocomplete:"new-password"},null,8,["modelValue"]),mn,s("div",pn,[C(v(X),{type:"button",label:"Cambiar contraseña",icon:"pi pi-key",loading:K.value,onClick:_e,class:"w-full"},null,8,["loading"])])]),_:1},8,["visible"])):b("",!0),C(v(Z),{visible:N.value,"onUpdate:visible":u[7]||(u[7]=p=>N.value=p),modal:"",header:"Notificación",style:{width:"520px"}},{default:x(()=>[Y.value?(i(),d("div",fn,[hn,T(" Cargando... ")])):(i(),d("div",gn,[y.value?(i(),d("div",{key:1,class:D(["space-y-3 p-3 rounded-md",{"border-l-4 border-green-500 bg-green-50":y.value.tipo==="success","border-l-4 border-yellow-500 bg-yellow-50":y.value.tipo==="warning"||y.value.tipo==="warn","border-l-4 border-red-500 bg-red-50":y.value.tipo==="error","border-l-4 border-blue-500 bg-blue-50":!["success","warning","warn","error"].includes(y.value.tipo)}])},[s("div",bn,_(y.value.titulo||"Aviso"),1),y.value.imagen_url?(i(),d("div",yn,[s("img",{src:y.value.imagen_url,alt:"imagen de la notificación",class:"w-full rounded-md max-h-72 object-contain"},null,8,_n)])):b("",!0),s("div",Cn,_(y.value.mensaje),1),s("div",xn,[y.value.url?(i(),d("a",{key:0,href:y.value.url,target:"_blank",rel:"noopener",class:"px-3 py-2 rounded-md border text-sm hover:bg-gray-50 underline"}," Ir al enlace ",8,wn)):b("",!0),C(v(X),{type:"button",onClick:xe},{default:x(()=>[T(" Leí la notificación y estoy informado ")]),_:1})])],2)):(i(),d("div",vn," No hay notificaciones pendientes. "))]))]),_:1},8,["visible"]),C(v(ve))])]))}};export{Oe as F,$n as _,Z as a,Sn as b,Dn as c,ve as s};
