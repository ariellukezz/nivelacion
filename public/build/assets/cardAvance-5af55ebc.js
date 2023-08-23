import{s as g}from"./card.esm-a757c856.js";import{o as s,c as o,l as b,m as v,i as h,t as c,f as d,n as y,d as t,a as p,w as m,b as u}from"./app-ee7bd71e.js";import"./basecomponent.esm-c167ef46.js";var f={name:"ProgressBar",props:{value:{type:Number,default:null},mode:{type:String,default:"determinate"},showValue:{type:Boolean,default:!0}},computed:{containerClass(){return["p-progressbar p-component",{"p-progressbar-determinate":this.determinate,"p-progressbar-indeterminate":this.indeterminate}]},progressStyle(){return{width:this.value+"%",display:"flex"}},indeterminate(){return this.mode==="indeterminate"},determinate(){return this.mode==="determinate"}}};const _=["aria-valuenow"],w={key:0,class:"p-progressbar-label"},x={key:1,class:"p-progressbar-indeterminate-container"},k=t("div",{class:"p-progressbar-value p-progressbar-value-animate"},null,-1),C=[k];function z(e,a,n,i,r,l){return s(),o("div",{role:"progressbar",class:y(l.containerClass),"aria-valuemin":"0","aria-valuenow":n.value,"aria-valuemax":"100"},[l.determinate?(s(),o("div",{key:0,class:"p-progressbar-value p-progressbar-value-animate",style:b(l.progressStyle)},[n.value!=null&&n.value!==0&&n.showValue?(s(),o("div",w,[v(e.$slots,"default",{},()=>[h(c(n.value+"%"),1)])])):d("",!0)],4)):d("",!0),l.indeterminate?(s(),o("div",x,C)):d("",!0)],10,_)}function S(e,a){a===void 0&&(a={});var n=a.insertAt;if(!(!e||typeof document>"u")){var i=document.head||document.getElementsByTagName("head")[0],r=document.createElement("style");r.type="text/css",n==="top"&&i.firstChild?i.insertBefore(r,i.firstChild):i.appendChild(r),r.styleSheet?r.styleSheet.cssText=e:r.appendChild(document.createTextNode(e))}}var N=`
.p-progressbar {
    position: relative;
    overflow: hidden;
}
.p-progressbar-determinate .p-progressbar-value {
    height: 100%;
    width: 0%;
    position: absolute;
    display: none;
    border: 0 none;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}
.p-progressbar-determinate .p-progressbar-label {
    display: inline-flex;
}
.p-progressbar-determinate .p-progressbar-value-animate {
    transition: width 1s ease-in-out;
}
.p-progressbar-indeterminate .p-progressbar-value::before {
    content: '';
    position: absolute;
    background-color: inherit;
    top: 0;
    left: 0;
    bottom: 0;
    will-change: left, right;
    -webkit-animation: p-progressbar-indeterminate-anim 2.1s cubic-bezier(0.65, 0.815, 0.735, 0.395) infinite;
    animation: p-progressbar-indeterminate-anim 2.1s cubic-bezier(0.65, 0.815, 0.735, 0.395) infinite;
}
.p-progressbar-indeterminate .p-progressbar-value::after {
    content: '';
    position: absolute;
    background-color: inherit;
    top: 0;
    left: 0;
    bottom: 0;
    will-change: left, right;
    -webkit-animation: p-progressbar-indeterminate-anim-short 2.1s cubic-bezier(0.165, 0.84, 0.44, 1) infinite;
    animation: p-progressbar-indeterminate-anim-short 2.1s cubic-bezier(0.165, 0.84, 0.44, 1) infinite;
    -webkit-animation-delay: 1.15s;
    animation-delay: 1.15s;
}
@-webkit-keyframes p-progressbar-indeterminate-anim {
0% {
        left: -35%;
        right: 100%;
}
60% {
        left: 100%;
        right: -90%;
}
100% {
        left: 100%;
        right: -90%;
}
}
@keyframes p-progressbar-indeterminate-anim {
0% {
        left: -35%;
        right: 100%;
}
60% {
        left: 100%;
        right: -90%;
}
100% {
        left: 100%;
        right: -90%;
}
}
@-webkit-keyframes p-progressbar-indeterminate-anim-short {
0% {
        left: -200%;
        right: 100%;
}
60% {
        left: 107%;
        right: -8%;
}
100% {
        left: 107%;
        right: -8%;
}
}
@keyframes p-progressbar-indeterminate-anim-short {
0% {
        left: -200%;
        right: 100%;
}
60% {
        left: 107%;
        right: -8%;
}
100% {
        left: 107%;
        right: -8%;
}
}
`;S(N);f.render=z;const B={class:"flex justify-center align-center",style:{width:"100%",height:"150px","align-items":"center"}},j={class:""},V={class:"flex justify-center"},T={style:{width:"180px","white-space":"nowrap",overflow:"hidden","text-overflow":"ellipsis","text-align":"center"}},A={style:{"font-size":"1.2rem",color:"var(--primary-color)"}},E=t("div",{class:"flex justify-center"},[t("hr",{style:{width:"50px",border:"solid salmon 2px"}})],-1),$={class:"mt-5",style:{width:"180px"}},D={class:"flex justify-center"},I={style:{"font-weight":"500",color:"var(--primary-color)","font-size":"1.7rem"}},G={__name:"cardAvance",props:{titulo:String,id_escuela:Number,data:null},setup(e){return(a,n)=>(s(),o("div",null,[p(u(g),{style:{height:"200px",width:"240px"}},{content:m(()=>[t("div",B,[t("div",j,[t("div",V,[t("div",T,[t("h1",A,c(e.titulo),1)])]),E,t("div",$,[p(u(f),{value:e.data[e.id_escuela]},{default:m(()=>[h("   ")]),_:1},8,["value"])]),t("div",D,[t("span",I,c(e.data[e.id_escuela])+"%",1)])])])]),_:1})]))}};export{G as default};
