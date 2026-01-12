import{R as m,D as i,e as f,c as u,z as h,f as g}from"./app-1b559a8b.js";var C={name:"BaseIcon",props:{label:{type:String,value:void 0},spin:{type:Boolean,value:!1}},methods:{pti(){const e=m.isEmpty(this.label);return{class:["p-icon",{"p-icon-spin":this.spin}],role:e?void 0:"img","aria-label":e?void 0:this.label,"aria-hidden":e}}}};function v(e,t){t===void 0&&(t={});var n=t.insertAt;if(!(!e||typeof document>"u")){var s=document.head||document.getElementsByTagName("head")[0],o=document.createElement("style");o.type="text/css",n==="top"&&s.firstChild?s.insertBefore(o,s.firstChild):s.appendChild(o),o.styleSheet?o.styleSheet.cssText=e:o.appendChild(document.createTextNode(e))}}var y=`
.p-icon {
    display: inline-block;
}
.p-icon-spin {
    -webkit-animation: p-icon-spin 2s infinite linear;
    animation: p-icon-spin 2s infinite linear;
}
@-webkit-keyframes p-icon-spin {
0% {
        -webkit-transform: rotate(0deg);
        transform: rotate(0deg);
}
100% {
        -webkit-transform: rotate(359deg);
        transform: rotate(359deg);
}
}
@keyframes p-icon-spin {
0% {
        -webkit-transform: rotate(0deg);
        transform: rotate(0deg);
}
100% {
        -webkit-transform: rotate(359deg);
        transform: rotate(359deg);
}
}
`;v(y);let a;function k(e){e.addEventListener("mousedown",d)}function b(e){e.removeEventListener("mousedown",d)}function w(e){let t=document.createElement("span");t.className="p-ink",t.setAttribute("role","presentation"),t.setAttribute("aria-hidden","true"),e.appendChild(t),t.addEventListener("animationend",p)}function x(e){let t=c(e);t&&(b(e),t.removeEventListener("animationend",p),t.remove())}function d(e){let t=e.currentTarget,n=c(t);if(!n||getComputedStyle(n,null).display==="none")return;if(i.removeClass(n,"p-ink-active"),!i.getHeight(n)&&!i.getWidth(n)){let l=Math.max(i.getOuterWidth(t),i.getOuterHeight(t));n.style.height=l+"px",n.style.width=l+"px"}let s=i.getOffset(t),o=e.pageX-s.left+document.body.scrollTop-i.getWidth(n)/2,r=e.pageY-s.top+document.body.scrollLeft-i.getHeight(n)/2;n.style.top=r+"px",n.style.left=o+"px",i.addClass(n,"p-ink-active"),a=setTimeout(()=>{n&&i.removeClass(n,"p-ink-active")},401)}function p(e){a&&clearTimeout(a),i.removeClass(e.currentTarget,"p-ink-active")}function c(e){for(let t=0;t<e.children.length;t++)if(typeof e.children[t].className=="string"&&e.children[t].className.indexOf("p-ink")!==-1)return e.children[t];return null}const N={mounted(e,t){t.instance.$primevue&&t.instance.$primevue.config&&t.instance.$primevue.config.ripple&&(w(e),k(e))},unmounted(e){x(e)}};var L={name:"TimesIcon",extends:C};const E=g("path",{d:"M8.01186 7.00933L12.27 2.75116C12.341 2.68501 12.398 2.60524 12.4375 2.51661C12.4769 2.42798 12.4982 2.3323 12.4999 2.23529C12.5016 2.13827 12.4838 2.0419 12.4474 1.95194C12.4111 1.86197 12.357 1.78024 12.2884 1.71163C12.2198 1.64302 12.138 1.58893 12.0481 1.55259C11.9581 1.51625 11.8617 1.4984 11.7647 1.50011C11.6677 1.50182 11.572 1.52306 11.4834 1.56255C11.3948 1.60204 11.315 1.65898 11.2488 1.72997L6.99067 5.98814L2.7325 1.72997C2.59553 1.60234 2.41437 1.53286 2.22718 1.53616C2.03999 1.53946 1.8614 1.61529 1.72901 1.74767C1.59663 1.88006 1.5208 2.05865 1.5175 2.24584C1.5142 2.43303 1.58368 2.61419 1.71131 2.75116L5.96948 7.00933L1.71131 11.2675C1.576 11.403 1.5 11.5866 1.5 11.7781C1.5 11.9696 1.576 12.1532 1.71131 12.2887C1.84679 12.424 2.03043 12.5 2.2219 12.5C2.41338 12.5 2.59702 12.424 2.7325 12.2887L6.99067 8.03052L11.2488 12.2887C11.3843 12.424 11.568 12.5 11.7594 12.5C11.9509 12.5 12.1346 12.424 12.27 12.2887C12.4053 12.1532 12.4813 11.9696 12.4813 11.7781C12.4813 11.5866 12.4053 11.403 12.27 11.2675L8.01186 7.00933Z",fill:"currentColor"},null,-1),T=[E];function B(e,t,n,s,o,r){return f(),u("svg",h({width:"14",height:"14",viewBox:"0 0 14 14",fill:"none",xmlns:"http://www.w3.org/2000/svg"},e.pti()),T,16)}L.render=B;export{N as R,C as a,L as s};
