import{o as i,c as o,k as c,x as d,n as l,g as p,v as u,d as g,t as m}from"./app-faeb0e8d.js";var h={name:"Tag",props:{value:null,severity:null,rounded:Boolean,icon:String},computed:{containerClass(){return["p-tag p-component",{"p-tag-info":this.severity==="info","p-tag-success":this.severity==="success","p-tag-warning":this.severity==="warning","p-tag-danger":this.severity==="danger","p-tag-rounded":this.rounded}]},iconClass(){return["p-tag-icon",this.icon]}}};const y={class:"p-tag-value"};function v(n,s,a,t,e,r){return i(),o("span",{class:l(r.containerClass)},[n.$slots.icon?(i(),c(d(n.$slots.icon),{key:0,class:"p-tag-icon"})):a.icon?(i(),o("span",{key:1,class:l(r.iconClass)},null,2)):p("",!0),u(n.$slots,"default",{},()=>[g("span",y,m(a.value),1)])],2)}function f(n,s){s===void 0&&(s={});var a=s.insertAt;if(!(!n||typeof document>"u")){var t=document.head||document.getElementsByTagName("head")[0],e=document.createElement("style");e.type="text/css",a==="top"&&t.firstChild?t.insertBefore(e,t.firstChild):t.appendChild(e),e.styleSheet?e.styleSheet.cssText=n:e.appendChild(document.createTextNode(n))}}var C=`
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
`;f(C);h.render=v;export{h as s};
