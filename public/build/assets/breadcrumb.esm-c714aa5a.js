import{a as B}from"./datatable.esm-71078f41.js";import{s as y,e as i,c as l,F as k,j as o,w as I,f as x,n as u,B as f,i as r,t as p,m as w,A,b as C}from"./app-df00ec4e.js";var v={name:"BreadcrumbItem",props:{item:null,templates:null,exact:null},methods:{onClick(t,n){this.item.command&&this.item.command({originalEvent:t,item:this.item}),this.item.to&&n&&n(t)},containerClass(){return["p-menuitem",{"p-disabled":this.disabled()},this.item.class]},linkClass(t){return["p-menuitem-link",{"router-link-active":t&&t.isActive,"router-link-active-exact":this.exact&&t&&t.isExactActive}]},visible(){return typeof this.item.visible=="function"?this.item.visible():this.item.visible!==!1},disabled(){return typeof this.item.disabled=="function"?this.item.disabled():this.item.disabled},label(){return typeof this.item.label=="function"?this.item.label():this.item.label},isCurrentUrl(){const{to:t,url:n}=this.item;let e=this.$router?this.$router.currentRoute.path:"";return t===e||n===e?"page":void 0}}};const E=["href","aria-current","onClick"],N={key:2,class:"p-menuitem-text"},R=["href","target","aria-current"],S={key:2,class:"p-menuitem-text"};function T(t,n,e,m,s,a){const d=y("router-link");return a.visible()?(i(),l("li",{key:0,class:u(a.containerClass())},[e.templates.item?(i(),o(f(e.templates.item),{key:1,item:e.item},null,8,["item"])):(i(),l(k,{key:0},[e.item.to?(i(),o(d,{key:0,to:e.item.to,custom:""},{default:I(({navigate:c,href:h,isActive:b,isExactActive:_})=>[x("a",{href:h,class:u(a.linkClass({isActive:b,isExactActive:_})),"aria-current":a.isCurrentUrl(),onClick:g=>a.onClick(g,c)},[e.templates.itemicon?(i(),o(f(e.templates.itemicon),{key:0,item:e.item,class:"p-menuitem-icon"},null,8,["item"])):e.item.icon?(i(),l("span",{key:1,class:u(["p-menuitem-icon",e.item.icon])},null,2)):r("",!0),e.item.label?(i(),l("span",N,p(a.label()),1)):r("",!0)],10,E)]),_:1},8,["to"])):(i(),l("a",{key:1,href:e.item.url||"#",class:u(a.linkClass()),target:e.item.target,"aria-current":a.isCurrentUrl(),onClick:n[0]||(n[0]=(...c)=>a.onClick&&a.onClick(...c))},[e.templates.itemicon?(i(),o(f(e.templates.itemicon),{key:0,item:e.item,class:"p-menuitem-icon"},null,8,["item"])):e.item.icon?(i(),l("span",{key:1,class:u(["p-menuitem-icon",e.item.icon])},null,2)):r("",!0),e.item.label?(i(),l("span",S,p(a.label()),1)):r("",!0)],10,R))],64))],2)):r("",!0)}v.render=T;var U={name:"Breadcrumb",props:{model:{type:Array,default:null},home:{type:null,default:null},exact:{type:Boolean,default:!0}},components:{BreadcrumbItem:v,ChevronRightIcon:B}};const V={class:"p-breadcrumb p-component"},j={class:"p-breadcrumb-list"},z={key:0,class:"p-menuitem-separator"};function D(t,n,e,m,s,a){const d=y("BreadcrumbItem"),c=y("ChevronRightIcon");return i(),l("nav",V,[x("ol",j,[e.home?(i(),o(d,{key:0,item:e.home,class:"p-breadcrumb-home",templates:t.$slots,exact:e.exact},null,8,["item","templates","exact"])):r("",!0),(i(!0),l(k,null,w(e.model,(h,b)=>(i(),l(k,{key:h.label},[e.home||b!==0?(i(),l("li",z,[A(t.$slots,"separator",{},()=>[C(c,{"aria-hidden":"true"})])])):r("",!0),C(d,{item:h,templates:t.$slots,exact:e.exact},null,8,["item","templates","exact"])],64))),128))])])}function F(t,n){n===void 0&&(n={});var e=n.insertAt;if(!(!t||typeof document>"u")){var m=document.head||document.getElementsByTagName("head")[0],s=document.createElement("style");s.type="text/css",e==="top"&&m.firstChild?m.insertBefore(s,m.firstChild):m.appendChild(s),s.styleSheet?s.styleSheet.cssText=t:s.appendChild(document.createTextNode(t))}}var L=`
.p-breadcrumb {
    overflow-x: auto;
}
.p-breadcrumb .p-breadcrumb-list {
    margin: 0;
    padding: 0;
    list-style-type: none;
    display: flex;
    align-items: center;
    flex-wrap: nowrap;
}
.p-breadcrumb .p-menuitem-text {
    line-height: 1;
}
.p-breadcrumb .p-menuitem-link {
    text-decoration: none;
    display: flex;
    align-items: center;
}
.p-breadcrumb .p-menuitem-separator {
    display: flex;
    align-items: center;
}
.p-breadcrumb::-webkit-scrollbar {
    display: none;
}
`;F(L);U.render=D;export{U as s};
