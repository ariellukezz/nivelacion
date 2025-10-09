import{s as j}from"./index.esm-0e4f3215.js";import{a3 as a,v as m,D as s,C as A,s as g,x as E,e as c,j as d,w as f,b as h,T as z,y as O,c as v,z as S,A as b,B as y,n as p,i as C,f as u,t as H}from"./app-f994730f.js";import{c as F,F as R}from"./TopMenu-f2fc4239.js";import{O as B}from"./dropdown.esm-99078355.js";var K={name:"ConfirmPopup",inheritAttrs:!1,props:{group:String},data(){return{visible:!1,confirmation:null,autoFocusAccept:null,autoFocusReject:null}},target:null,outsideClickListener:null,scrollHandler:null,resizeListener:null,container:null,confirmListener:null,closeListener:null,mounted(){this.confirmListener=e=>{e&&e.group===this.group&&(this.confirmation=e,this.target=e.target,this.confirmation.onShow&&this.confirmation.onShow(),this.visible=!0)},this.closeListener=()=>{this.visible=!1,this.confirmation=null},a.on("confirm",this.confirmListener),a.on("close",this.closeListener)},beforeUnmount(){a.off("confirm",this.confirmListener),a.off("close",this.closeListener),this.unbindOutsideClickListener(),this.scrollHandler&&(this.scrollHandler.destroy(),this.scrollHandler=null),this.unbindResizeListener(),this.container&&(m.clear(this.container),this.container=null),this.target=null,this.confirmation=null},methods:{accept(){this.confirmation.accept&&this.confirmation.accept(),this.visible=!1},reject(){this.confirmation.reject&&this.confirmation.reject(),this.visible=!1},onHide(){this.confirmation.onHide&&this.confirmation.onHide(),this.visible=!1},onAcceptKeydown(e){(e.code==="Space"||e.code==="Enter")&&(this.accept(),s.focus(this.target),e.preventDefault())},onRejectKeydown(e){(e.code==="Space"||e.code==="Enter")&&(this.reject(),s.focus(this.target),e.preventDefault())},onEnter(e){this.autoFocusAccept=this.confirmation.defaultFocus===void 0||this.confirmation.defaultFocus==="accept",this.autoFocusReject=this.confirmation.defaultFocus==="reject",this.bindOutsideClickListener(),this.bindScrollListener(),this.bindResizeListener(),m.set("overlay",e,this.$primevue.config.zIndex.overlay)},onAfterEnter(){this.focus()},onLeave(){this.autoFocusAccept=null,this.autoFocusReject=null,this.unbindOutsideClickListener(),this.unbindScrollListener(),this.unbindResizeListener()},onAfterLeave(e){m.clear(e)},alignOverlay(){s.absolutePosition(this.container,this.target);const e=s.getOffset(this.container),i=s.getOffset(this.target);let l=0;e.left<i.left&&(l=i.left-e.left),this.container.style.setProperty("--overlayArrowLeft",`${l}px`),e.top<i.top&&s.addClass(this.container,"p-confirm-popup-flipped")},bindOutsideClickListener(){this.outsideClickListener||(this.outsideClickListener=e=>{this.visible&&this.container&&!this.container.contains(e.target)&&!this.isTargetClicked(e)?(this.confirmation.onHide&&this.confirmation.onHide(),this.visible=!1):this.alignOverlay()},document.addEventListener("click",this.outsideClickListener))},unbindOutsideClickListener(){this.outsideClickListener&&(document.removeEventListener("click",this.outsideClickListener),this.outsideClickListener=null)},bindScrollListener(){this.scrollHandler||(this.scrollHandler=new A(this.target,()=>{this.visible&&(this.visible=!1)})),this.scrollHandler.bindScrollListener()},unbindScrollListener(){this.scrollHandler&&this.scrollHandler.unbindScrollListener()},bindResizeListener(){this.resizeListener||(this.resizeListener=()=>{this.visible&&!s.isTouchDevice()&&(this.visible=!1)},window.addEventListener("resize",this.resizeListener))},unbindResizeListener(){this.resizeListener&&(window.removeEventListener("resize",this.resizeListener),this.resizeListener=null)},focus(){let e=this.container.querySelector("[autofocus]");e&&e.focus({preventScroll:!0})},isTargetClicked(e){return this.target&&(this.target===e.target||this.target.contains(e.target))},containerRef(e){this.container=e},onOverlayClick(e){B.emit("overlay-click",{originalEvent:e,target:this.target})},onOverlayKeydown(e){e.code==="Escape"&&(a.emit("close",this.closeListener),s.focus(this.target))}},computed:{containerClass(){return["p-confirm-popup p-component",{"p-input-filled":this.$primevue.config.inputStyle==="filled","p-ripple-disabled":this.$primevue.config.ripple===!1}]},message(){return this.confirmation?this.confirmation.message:null},iconClass(){return["p-confirm-popup-icon",this.confirmation?this.confirmation.icon:null]},acceptLabel(){return this.confirmation?this.confirmation.acceptLabel||this.$primevue.config.locale.accept:null},rejectLabel(){return this.confirmation?this.confirmation.rejectLabel||this.$primevue.config.locale.reject:null},acceptIcon(){return this.confirmation?this.confirmation.acceptIcon:null},rejectIcon(){return this.confirmation?this.confirmation.rejectIcon:null},acceptClass(){return["p-confirm-popup-accept p-button-sm",this.confirmation?this.confirmation.acceptClass:null]},rejectClass(){return["p-confirm-popup-reject p-button-sm",this.confirmation?this.confirmation.rejectClass||"p-button-text":null]}},components:{CPButton:j,Portal:F},directives:{focustrap:R}};const x=["aria-modal"],P={key:0,class:"p-confirm-popup-content"},T={class:"p-confirm-popup-message"},D={class:"p-confirm-popup-footer"};function I(e,i,l,r,n,t){const L=g("CPButton"),k=g("Portal"),w=E("focustrap");return c(),d(k,null,{default:f(()=>[h(z,{name:"p-confirm-popup",onEnter:t.onEnter,onAfterEnter:t.onAfterEnter,onLeave:t.onLeave,onAfterLeave:t.onAfterLeave},{default:f(()=>[n.visible?O((c(),v("div",S({key:0,ref:t.containerRef,role:"alertdialog",class:t.containerClass,"aria-modal":n.visible,onClick:i[2]||(i[2]=(...o)=>t.onOverlayClick&&t.onOverlayClick(...o)),onKeydown:i[3]||(i[3]=(...o)=>t.onOverlayKeydown&&t.onOverlayKeydown(...o))},e.$attrs),[e.$slots.message?(c(),d(y(e.$slots.message),{key:1,message:n.confirmation},null,8,["message"])):(c(),v("div",P,[b(e.$slots,"icon",{class:"p-confirm-popup-icon"},()=>[e.$slots.icon?(c(),d(y(e.$slots.icon),{key:0,class:"p-confirm-popup-icon"})):n.confirmation.icon?(c(),v("span",{key:1,class:p(t.iconClass)},null,2)):C("",!0)]),u("span",T,H(n.confirmation.message),1)])),u("div",D,[h(L,{label:t.rejectLabel,class:p(t.rejectClass),onClick:i[0]||(i[0]=o=>t.reject()),onKeydown:t.onRejectKeydown,autofocus:n.autoFocusReject},{icon:f(o=>[b(e.$slots,"rejecticon",{},()=>[u("span",{class:p([t.rejectIcon,o.class])},null,2)])]),_:3},8,["label","class","onKeydown","autofocus"]),h(L,{label:t.acceptLabel,class:p(t.acceptClass),onClick:i[1]||(i[1]=o=>t.accept()),onKeydown:t.onAcceptKeydown,autofocus:n.autoFocusAccept},{icon:f(o=>[b(e.$slots,"accepticon",{},()=>[u("span",{class:p([t.acceptIcon,o.class])},null,2)])]),_:3},8,["label","class","onKeydown","autofocus"])])],16,x)),[[w]]):C("",!0)]),_:3},8,["onEnter","onAfterEnter","onLeave","onAfterLeave"])]),_:3})}function N(e,i){i===void 0&&(i={});var l=i.insertAt;if(!(!e||typeof document>"u")){var r=document.head||document.getElementsByTagName("head")[0],n=document.createElement("style");n.type="text/css",l==="top"&&r.firstChild?r.insertBefore(n,r.firstChild):r.appendChild(n),n.styleSheet?n.styleSheet.cssText=e:n.appendChild(document.createTextNode(e))}}var _=`
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
`;N(_);K.render=I;export{K as s};
