import{d as m,e as d,f as u,g as f,R as b,h as k}from"./TopMenu-19b80a18.js";import{m as x,q as I,o as i,k as l,w as T,s as g,d as c,n as r,v as p,x as B,c as v,y as w,g as _,z as S,T as A}from"./app-faeb0e8d.js";var D={name:"Message",emits:["close"],props:{severity:{type:String,default:"info"},closable:{type:Boolean,default:!0},sticky:{type:Boolean,default:!0},life:{type:Number,default:3e3},icon:{type:String,default:void 0},closeIcon:{type:String,default:void 0},closeButtonProps:{type:null,default:null}},timeout:null,data(){return{visible:!0}},mounted(){this.sticky||this.closeAfterDelay()},methods:{close(e){this.visible=!1,this.$emit("close",e)},closeAfterDelay(){setTimeout(()=>{this.visible=!1},this.life)}},computed:{containerClass(){return"p-message p-component p-message-"+this.severity},iconComponent(){return{info:m,success:d,warn:u,error:f}[this.severity]},closeAriaLabel(){return this.$primevue.config.locale.aria?this.$primevue.config.locale.aria.close:void 0}},directives:{ripple:b},components:{TimesIcon:k,InfoCircleIcon:m,CheckIcon:d,ExclamationTriangleIcon:u,TimesCircleIcon:f}};const N={class:"p-message-wrapper"},z={class:"p-message-text"},E=["aria-label"];function $(e,n,t,a,s,o){const h=x("TimesIcon"),y=I("ripple");return i(),l(A,{name:"p-message",appear:""},{default:T(()=>[g(c("div",{class:r(o.containerClass),role:"alert","aria-live":"assertive","aria-atomic":"true"},[c("div",N,[p(e.$slots,"messageicon",{class:"p-message-icon"},()=>[(i(),l(B(t.icon?"span":o.iconComponent),{class:r(["p-message-icon",t.icon])},null,8,["class"]))]),c("div",z,[p(e.$slots,"default")]),t.closable?g((i(),v("button",w({key:0,class:"p-message-close p-link","aria-label":o.closeAriaLabel,type:"button",onClick:n[0]||(n[0]=C=>o.close(C))},t.closeButtonProps),[p(e.$slots,"closeicon",{class:"p-message-close-icon"},()=>[t.closeIcon?(i(),v("i",{key:0,class:r(["p-message-close-icon",t.closeIcon])},null,2)):(i(),l(h,{key:1,class:"p-message-close-icon"}))])],16,E)),[[y]]):_("",!0)])],2),[[S,s.visible]])]),_:3})}function P(e,n){n===void 0&&(n={});var t=n.insertAt;if(!(!e||typeof document>"u")){var a=document.head||document.getElementsByTagName("head")[0],s=document.createElement("style");s.type="text/css",t==="top"&&a.firstChild?a.insertBefore(s,a.firstChild):a.appendChild(s),s.styleSheet?s.styleSheet.cssText=e:s.appendChild(document.createTextNode(e))}}var j=`
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
`;P(j);D.render=$;export{D as s};
