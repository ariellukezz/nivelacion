import{o as c,c as h,e as u,y as k,n as p,a4 as f,q as m,D as a,C as S,s as g,v as A,l as b,w as d,a as v,T as E,x as O,z as y,A as L,h as C,t as z}from"./app-67c51190.js";import{a as B,d as F,F as H}from"./TopMenu-a9feaa5e.js";import{O as x}from"./column.esm-bab9c357.js";var I={name:"InputSwitch",emits:["click","update:modelValue","change","input","focus","blur"],props:{modelValue:{type:null,default:!1},trueValue:{type:null,default:!0},falseValue:{type:null,default:!1},disabled:{type:Boolean,default:!1},inputId:{type:String,default:null},inputClass:{type:[String,Object],default:null},inputStyle:{type:Object,default:null},inputProps:{type:null,default:null},"aria-labelledby":{type:String,default:null},"aria-label":{type:String,default:null}},data(){return{focused:!1}},methods:{onClick(e){if(!this.disabled){const t=this.checked?this.falseValue:this.trueValue;this.$emit("click",e),this.$emit("update:modelValue",t),this.$emit("change",e),this.$emit("input",t),this.$refs.input.focus()}e.preventDefault()},onFocus(e){this.focused=!0,this.$emit("focus",e)},onBlur(e){this.focused=!1,this.$emit("blur",e)}},computed:{containerClass(){return["p-inputswitch p-component",{"p-inputswitch-checked":this.checked,"p-disabled":this.disabled,"p-focus":this.focused}]},checked(){return this.modelValue===this.trueValue}}};const R={class:"p-hidden-accessible"},T=["id","checked","disabled","aria-checked","aria-labelledby","aria-label"],V=u("span",{class:"p-inputswitch-slider"},null,-1);function K(e,t,s,o,n,i){return c(),h("div",{class:p(i.containerClass),onClick:t[2]||(t[2]=l=>i.onClick(l))},[u("div",R,[u("input",k({ref:"input",id:s.inputId,type:"checkbox",role:"switch",class:s.inputClass,style:s.inputStyle,checked:i.checked,disabled:s.disabled,"aria-checked":i.checked,"aria-labelledby":e.ariaLabelledby,"aria-label":e.ariaLabel,onFocus:t[0]||(t[0]=l=>i.onFocus(l)),onBlur:t[1]||(t[1]=l=>i.onBlur(l))},s.inputProps),null,16,T)]),V],2)}function P(e,t){t===void 0&&(t={});var s=t.insertAt;if(!(!e||typeof document>"u")){var o=document.head||document.getElementsByTagName("head")[0],n=document.createElement("style");n.type="text/css",s==="top"&&o.firstChild?o.insertBefore(n,o.firstChild):o.appendChild(n),n.styleSheet?n.styleSheet.cssText=e:n.appendChild(document.createTextNode(e))}}var D=`
.p-inputswitch {
    position: relative;
    display: inline-block;
}
.p-inputswitch-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border: 1px solid transparent;
}
.p-inputswitch-slider:before {
    position: absolute;
    content: '';
    top: 50%;
}
`;P(D);I.render=K;var N={name:"ConfirmPopup",inheritAttrs:!1,props:{group:String},data(){return{visible:!1,confirmation:null,autoFocusAccept:null,autoFocusReject:null}},target:null,outsideClickListener:null,scrollHandler:null,resizeListener:null,container:null,confirmListener:null,closeListener:null,mounted(){this.confirmListener=e=>{e&&e.group===this.group&&(this.confirmation=e,this.target=e.target,this.confirmation.onShow&&this.confirmation.onShow(),this.visible=!0)},this.closeListener=()=>{this.visible=!1,this.confirmation=null},f.on("confirm",this.confirmListener),f.on("close",this.closeListener)},beforeUnmount(){f.off("confirm",this.confirmListener),f.off("close",this.closeListener),this.unbindOutsideClickListener(),this.scrollHandler&&(this.scrollHandler.destroy(),this.scrollHandler=null),this.unbindResizeListener(),this.container&&(m.clear(this.container),this.container=null),this.target=null,this.confirmation=null},methods:{accept(){this.confirmation.accept&&this.confirmation.accept(),this.visible=!1},reject(){this.confirmation.reject&&this.confirmation.reject(),this.visible=!1},onHide(){this.confirmation.onHide&&this.confirmation.onHide(),this.visible=!1},onAcceptKeydown(e){(e.code==="Space"||e.code==="Enter")&&(this.accept(),a.focus(this.target),e.preventDefault())},onRejectKeydown(e){(e.code==="Space"||e.code==="Enter")&&(this.reject(),a.focus(this.target),e.preventDefault())},onEnter(e){this.autoFocusAccept=this.confirmation.defaultFocus===void 0||this.confirmation.defaultFocus==="accept",this.autoFocusReject=this.confirmation.defaultFocus==="reject",this.bindOutsideClickListener(),this.bindScrollListener(),this.bindResizeListener(),m.set("overlay",e,this.$primevue.config.zIndex.overlay)},onAfterEnter(){this.focus()},onLeave(){this.autoFocusAccept=null,this.autoFocusReject=null,this.unbindOutsideClickListener(),this.unbindScrollListener(),this.unbindResizeListener()},onAfterLeave(e){m.clear(e)},alignOverlay(){a.absolutePosition(this.container,this.target);const e=a.getOffset(this.container),t=a.getOffset(this.target);let s=0;e.left<t.left&&(s=t.left-e.left),this.container.style.setProperty("--overlayArrowLeft",`${s}px`),e.top<t.top&&a.addClass(this.container,"p-confirm-popup-flipped")},bindOutsideClickListener(){this.outsideClickListener||(this.outsideClickListener=e=>{this.visible&&this.container&&!this.container.contains(e.target)&&!this.isTargetClicked(e)?(this.confirmation.onHide&&this.confirmation.onHide(),this.visible=!1):this.alignOverlay()},document.addEventListener("click",this.outsideClickListener))},unbindOutsideClickListener(){this.outsideClickListener&&(document.removeEventListener("click",this.outsideClickListener),this.outsideClickListener=null)},bindScrollListener(){this.scrollHandler||(this.scrollHandler=new S(this.target,()=>{this.visible&&(this.visible=!1)})),this.scrollHandler.bindScrollListener()},unbindScrollListener(){this.scrollHandler&&this.scrollHandler.unbindScrollListener()},bindResizeListener(){this.resizeListener||(this.resizeListener=()=>{this.visible&&!a.isTouchDevice()&&(this.visible=!1)},window.addEventListener("resize",this.resizeListener))},unbindResizeListener(){this.resizeListener&&(window.removeEventListener("resize",this.resizeListener),this.resizeListener=null)},focus(){let e=this.container.querySelector("[autofocus]");e&&e.focus({preventScroll:!0})},isTargetClicked(e){return this.target&&(this.target===e.target||this.target.contains(e.target))},containerRef(e){this.container=e},onOverlayClick(e){x.emit("overlay-click",{originalEvent:e,target:this.target})},onOverlayKeydown(e){e.code==="Escape"&&(f.emit("close",this.closeListener),a.focus(this.target))}},computed:{containerClass(){return["p-confirm-popup p-component",{"p-input-filled":this.$primevue.config.inputStyle==="filled","p-ripple-disabled":this.$primevue.config.ripple===!1}]},message(){return this.confirmation?this.confirmation.message:null},iconClass(){return["p-confirm-popup-icon",this.confirmation?this.confirmation.icon:null]},acceptLabel(){return this.confirmation?this.confirmation.acceptLabel||this.$primevue.config.locale.accept:null},rejectLabel(){return this.confirmation?this.confirmation.rejectLabel||this.$primevue.config.locale.reject:null},acceptIcon(){return this.confirmation?this.confirmation.acceptIcon:null},rejectIcon(){return this.confirmation?this.confirmation.rejectIcon:null},acceptClass(){return["p-confirm-popup-accept p-button-sm",this.confirmation?this.confirmation.acceptClass:null]},rejectClass(){return["p-confirm-popup-reject p-button-sm",this.confirmation?this.confirmation.rejectClass||"p-button-text":null]}},components:{CPButton:B,Portal:F},directives:{focustrap:H}};const _=["aria-modal"],q={key:0,class:"p-confirm-popup-content"},U={class:"p-confirm-popup-message"},Y={class:"p-confirm-popup-footer"};function Z(e,t,s,o,n,i){const l=g("CPButton"),w=g("Portal"),j=A("focustrap");return c(),b(w,null,{default:d(()=>[v(E,{name:"p-confirm-popup",onEnter:i.onEnter,onAfterEnter:i.onAfterEnter,onLeave:i.onLeave,onAfterLeave:i.onAfterLeave},{default:d(()=>[n.visible?O((c(),h("div",k({key:0,ref:i.containerRef,role:"alertdialog",class:i.containerClass,"aria-modal":n.visible,onClick:t[2]||(t[2]=(...r)=>i.onOverlayClick&&i.onOverlayClick(...r)),onKeydown:t[3]||(t[3]=(...r)=>i.onOverlayKeydown&&i.onOverlayKeydown(...r))},e.$attrs),[e.$slots.message?(c(),b(L(e.$slots.message),{key:1,message:n.confirmation},null,8,["message"])):(c(),h("div",q,[y(e.$slots,"icon",{class:"p-confirm-popup-icon"},()=>[e.$slots.icon?(c(),b(L(e.$slots.icon),{key:0,class:"p-confirm-popup-icon"})):n.confirmation.icon?(c(),h("span",{key:1,class:p(i.iconClass)},null,2)):C("",!0)]),u("span",U,z(n.confirmation.message),1)])),u("div",Y,[v(l,{label:i.rejectLabel,class:p(i.rejectClass),onClick:t[0]||(t[0]=r=>i.reject()),onKeydown:i.onRejectKeydown,autofocus:n.autoFocusReject},{icon:d(r=>[y(e.$slots,"rejecticon",{},()=>[u("span",{class:p([i.rejectIcon,r.class])},null,2)])]),_:3},8,["label","class","onKeydown","autofocus"]),v(l,{label:i.acceptLabel,class:p(i.acceptClass),onClick:t[1]||(t[1]=r=>i.accept()),onKeydown:i.onAcceptKeydown,autofocus:n.autoFocusAccept},{icon:d(r=>[y(e.$slots,"accepticon",{},()=>[u("span",{class:p([i.acceptIcon,r.class])},null,2)])]),_:3},8,["label","class","onKeydown","autofocus"])])],16,_)),[[j]]):C("",!0)]),_:3},8,["onEnter","onAfterEnter","onLeave","onAfterLeave"])]),_:3})}function $(e,t){t===void 0&&(t={});var s=t.insertAt;if(!(!e||typeof document>"u")){var o=document.head||document.getElementsByTagName("head")[0],n=document.createElement("style");n.type="text/css",s==="top"&&o.firstChild?o.insertBefore(n,o.firstChild):o.appendChild(n),n.styleSheet?n.styleSheet.cssText=e:n.appendChild(document.createTextNode(e))}}var G=`
.p-confirm-popup {
    position: absolute;
    margin-top: 10px;
    top: 0;
    left: 0;
}
.p-confirm-popup-flipped {
    margin-top: 0;
    margin-bottom: 10px;
}

/* Animation */
.p-confirm-popup-enter-from {
    opacity: 0;
    transform: scaleY(0.8);
}
.p-confirm-popup-leave-to {
    opacity: 0;
}
.p-confirm-popup-enter-active {
    transition: transform 0.12s cubic-bezier(0, 0, 0.2, 1), opacity 0.12s cubic-bezier(0, 0, 0.2, 1);
}
.p-confirm-popup-leave-active {
    transition: opacity 0.1s linear;
}
.p-confirm-popup:after,
.p-confirm-popup:before {
    bottom: 100%;
    left: calc(var(--overlayArrowLeft, 0) + 1.25rem);
    content: ' ';
    height: 0;
    width: 0;
    position: absolute;
    pointer-events: none;
}
.p-confirm-popup:after {
    border-width: 8px;
    margin-left: -8px;
}
.p-confirm-popup:before {
    border-width: 10px;
    margin-left: -10px;
}
.p-confirm-popup-flipped:after,
.p-confirm-popup-flipped:before {
    bottom: auto;
    top: 100%;
}
.p-confirm-popup.p-confirm-popup-flipped:after {
    border-bottom-color: transparent;
}
.p-confirm-popup.p-confirm-popup-flipped:before {
    border-bottom-color: transparent;
}
.p-confirm-popup .p-confirm-popup-content {
    display: flex;
    align-items: center;
}
`;$(G);N.render=Z;export{I as a,N as s};
