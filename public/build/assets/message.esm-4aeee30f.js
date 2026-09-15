import{b as m,c as d,d as u,e as f,R as b,a as k}from"./index.esm-af07d4c6.js";import{y as I,A as x,e as i,j as l,w as B,B as g,f as c,n as r,G as p,H as T,c as v,E as w,i as _,N as A,T as S}from"./app-1afcd6dc.js";var N={name:"Message",emits:["close"],props:{severity:{type:String,default:"info"},closable:{type:Boolean,default:!0},sticky:{type:Boolean,default:!0},life:{type:Number,default:3e3},icon:{type:String,default:void 0},closeIcon:{type:String,default:void 0},closeButtonProps:{type:null,default:null}},timeout:null,data(){return{visible:!0}},mounted(){this.sticky||this.closeAfterDelay()},methods:{close(e){this.visible=!1,this.$emit("close",e)},closeAfterDelay(){setTimeout(()=>{this.visible=!1},this.life)}},computed:{containerClass(){return"p-message p-component p-message-"+this.severity},iconComponent(){return{info:m,success:d,warn:u,error:f}[this.severity]},closeAriaLabel(){return this.$primevue.config.locale.aria?this.$primevue.config.locale.aria.close:void 0}},directives:{ripple:b},components:{TimesIcon:k,InfoCircleIcon:m,CheckIcon:d,ExclamationTriangleIcon:u,TimesCircleIcon:f}};const D={class:"p-message-wrapper"},E={class:"p-message-text"},$=["aria-label"];function j(e,n,t,a,s,o){const y=I("TimesIcon"),h=x("ripple");return i(),l(S,{name:"p-message",appear:""},{default:B(()=>[g(c("div",{class:r(o.containerClass),role:"alert","aria-live":"assertive","aria-atomic":"true"},[c("div",D,[p(e.$slots,"messageicon",{class:"p-message-icon"},()=>[(i(),l(T(t.icon?"span":o.iconComponent),{class:r(["p-message-icon",t.icon])},null,8,["class"]))]),c("div",E,[p(e.$slots,"default")]),t.closable?g((i(),v("button",w({key:0,class:"p-message-close p-link","aria-label":o.closeAriaLabel,type:"button",onClick:n[0]||(n[0]=C=>o.close(C))},t.closeButtonProps),[p(e.$slots,"closeicon",{class:"p-message-close-icon"},()=>[t.closeIcon?(i(),v("i",{key:0,class:r(["p-message-close-icon",t.closeIcon])},null,2)):(i(),l(y,{key:1,class:"p-message-close-icon"}))])],16,$)),[[h]]):_("",!0)])],2),[[A,s.visible]])]),_:3})}function z(e,n){n===void 0&&(n={});var t=n.insertAt;if(!(!e||typeof document>"u")){var a=document.head||document.getElementsByTagName("head")[0],s=document.createElement("style");s.type="text/css",t==="top"&&a.firstChild?a.insertBefore(s,a.firstChild):a.appendChild(s),s.styleSheet?s.styleSheet.cssText=e:s.appendChild(document.createTextNode(e))}}var P=`
.p-message-wrapper {
    display: flex;
    align-items: center;
}
.p-message-close {
    display: flex;
    align-items: center;
    justify-content: center;
}
.p-message-close.p-link {
    margin-left: auto;
    overflow: hidden;
    position: relative;
}
.p-message-enter-from {
    opacity: 0;
}
.p-message-enter-active {
    transition: opacity 0.3s;
}
.p-message.p-message-leave-from {
    max-height: 1000px;
}
.p-message.p-message-leave-to {
    max-height: 0;
    opacity: 0;
    margin: 0 !important;
}
.p-message-leave-active {
    overflow: hidden;
    transition: max-height 0.3s cubic-bezier(0, 1, 0, 1), opacity 0.3s, margin 0.15s;
}
.p-message-leave-active .p-message-close {
    display: none;
}
`;z(P);N.render=j;export{N as s};
