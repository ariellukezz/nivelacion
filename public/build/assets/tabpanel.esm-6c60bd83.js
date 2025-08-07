import{s as P}from"./basecomponent.esm-3393b696.js";import{s as k}from"./index.esm-58f41e2e.js";import{d as I}from"./column.esm-a02f2833.js";import{R as A}from"./TopMenu-1faa5edf.js";import{U as m,D as l,v as C,o,c as h,e as u,x as b,y as d,z as w,i as v,A as f,h as p,F as T,l as x,t as B,K as H}from"./app-ae1c9c93.js";var D={name:"TabView",extends:P,emits:["update:activeIndex","tab-change","tab-click"],props:{activeIndex:{type:Number,default:0},lazy:{type:Boolean,default:!1},scrollable:{type:Boolean,default:!1},tabindex:{type:Number,default:0},selectOnFocus:{type:Boolean,default:!1},previousButtonProps:{type:null,default:null},nextButtonProps:{type:null,default:null},prevIcon:{type:String,default:void 0},nextIcon:{type:String,default:void 0}},data(){return{id:this.$attrs.id,d_activeIndex:this.activeIndex,isPrevButtonDisabled:!0,isNextButtonDisabled:!1}},watch:{"$attrs.id":function(e){this.id=e||m()},activeIndex(e){this.d_activeIndex=e,this.scrollInView({index:e})}},mounted(){this.id=this.id||m(),this.updateInkBar(),this.scrollable&&this.updateButtonState()},updated(){this.updateInkBar()},methods:{isTabPanel(e){return e.type.name==="TabPanel"},isTabActive(e){return this.d_activeIndex===e},getTabProp(e,t){return e.props?e.props[t]:void 0},getKey(e,t){return this.getTabProp(e,"header")||t},getTabHeaderActionId(e){return`${this.id}_${e}_header_action`},getTabContentId(e){return`${this.id}_${e}_content`},getTabPT(e,t){return this.ptmo(this.getTabProp(e,"pt"),t,{props:e.props,parent:{props:this.$props,state:this.$data}})},onScroll(e){this.scrollable&&this.updateButtonState(),e.preventDefault()},onPrevButtonClick(){const e=this.$refs.content,t=l.getWidth(e),n=e.scrollLeft-t;e.scrollLeft=n<=0?0:n},onNextButtonClick(){const e=this.$refs.content,t=l.getWidth(e)-this.getVisibleButtonWidths(),n=e.scrollLeft+t,r=e.scrollWidth-t;e.scrollLeft=n>=r?r:n},onTabClick(e,t,n){this.changeActiveIndex(e,t,n),this.$emit("tab-click",{originalEvent:e,index:n})},onTabKeyDown(e,t,n){switch(e.code){case"ArrowLeft":this.onTabArrowLeftKey(e);break;case"ArrowRight":this.onTabArrowRightKey(e);break;case"Home":this.onTabHomeKey(e);break;case"End":this.onTabEndKey(e);break;case"PageDown":this.onPageDownKey(e);break;case"PageUp":this.onPageUpKey(e);break;case"Enter":case"Space":this.onTabEnterKey(e,t,n);break}},onTabArrowRightKey(e){const t=this.findNextHeaderAction(e.target.parentElement);t?this.changeFocusedTab(e,t):this.onTabHomeKey(e),e.preventDefault()},onTabArrowLeftKey(e){const t=this.findPrevHeaderAction(e.target.parentElement);t?this.changeFocusedTab(e,t):this.onTabEndKey(e),e.preventDefault()},onTabHomeKey(e){const t=this.findFirstHeaderAction();this.changeFocusedTab(e,t),e.preventDefault()},onTabEndKey(e){const t=this.findLastHeaderAction();this.changeFocusedTab(e,t),e.preventDefault()},onPageDownKey(e){this.scrollInView({index:this.$refs.nav.children.length-2}),e.preventDefault()},onPageUpKey(e){this.scrollInView({index:0}),e.preventDefault()},onTabEnterKey(e,t,n){this.changeActiveIndex(e,t,n),e.preventDefault()},findNextHeaderAction(e,t=!1){const n=t?e:e.nextElementSibling;return n?l.hasClass(n,"p-disabled")||l.hasClass(n,"p-tabview-ink-bar")?this.findNextHeaderAction(n):l.findSingle(n,".p-tabview-header-action"):null},findPrevHeaderAction(e,t=!1){const n=t?e:e.previousElementSibling;return n?l.hasClass(n,"p-disabled")||l.hasClass(n,"p-tabview-ink-bar")?this.findPrevHeaderAction(n):l.findSingle(n,".p-tabview-header-action"):null},findFirstHeaderAction(){return this.findNextHeaderAction(this.$refs.nav.firstElementChild,!0)},findLastHeaderAction(){return this.findPrevHeaderAction(this.$refs.nav.lastElementChild,!0)},changeActiveIndex(e,t,n){!this.getTabProp(t,"disabled")&&this.d_activeIndex!==n&&(this.d_activeIndex=n,this.$emit("update:activeIndex",n),this.$emit("tab-change",{originalEvent:e,index:n}),this.scrollInView({index:n}))},changeFocusedTab(e,t){if(t&&(l.focus(t),this.scrollInView({element:t}),this.selectOnFocus)){const n=parseInt(t.parentElement.dataset.index,10),r=this.tabs[n];this.changeActiveIndex(e,r,n)}},scrollInView({element:e,index:t=-1}){const n=e||this.$refs.nav.children[t];n&&n.scrollIntoView&&n.scrollIntoView({block:"nearest"})},updateInkBar(){let e=this.$refs.nav.children[this.d_activeIndex];this.$refs.inkbar.style.width=l.getWidth(e)+"px",this.$refs.inkbar.style.left=l.getOffset(e).left-l.getOffset(this.$refs.nav).left+"px"},updateButtonState(){const e=this.$refs.content,{scrollLeft:t,scrollWidth:n}=e,r=l.getWidth(e);this.isPrevButtonDisabled=t===0,this.isNextButtonDisabled=parseInt(t)===n-r},getVisibleButtonWidths(){const{prevBtn:e,nextBtn:t}=this.$refs;return[e,t].reduce((n,r)=>r?n+l.getWidth(r):n,0)},getTabHeaderClass(e,t){return["p-tabview-header",this.getTabProp(e,"headerClass"),{"p-highlight":this.d_activeIndex===t,"p-disabled":this.getTabProp(e,"disabled")}]},getTabContentClass(e){return["p-tabview-panel",this.getTabProp(e,"contentClass")]}},computed:{contentClasses(){return["p-tabview p-component",{"p-tabview-scrollable":this.scrollable}]},tabs(){return this.$slots.default().reduce((e,t)=>(this.isTabPanel(t)?e.push(t):t.children&&t.children instanceof Array&&t.children.forEach(n=>{this.isTabPanel(n)&&e.push(n)}),e),[])},prevButtonAriaLabel(){return this.$primevue.config.locale.aria?this.$primevue.config.locale.aria.previous:void 0},nextButtonAriaLabel(){return this.$primevue.config.locale.aria?this.$primevue.config.locale.aria.next:void 0}},directives:{ripple:A},components:{ChevronLeftIcon:k,ChevronRightIcon:I}};const K=["tabindex","aria-label"],S=["data-index"],E=["id","tabindex","aria-disabled","aria-selected","aria-controls","onClick","onKeydown"],L=["tabindex","aria-label"],N=["aria-labelledby"];function V(e,t,n,r,c,a){const g=C("ripple");return o(),h("div",d({class:a.contentClasses},e.ptm("root")),[u("div",d({class:"p-tabview-nav-container"},e.ptm("navcontainer")),[n.scrollable&&!c.isPrevButtonDisabled?b((o(),h("button",d({key:0,ref:"prevBtn",type:"button",class:"p-tabview-nav-prev p-tabview-nav-btn p-link",tabindex:n.tabindex,"aria-label":a.prevButtonAriaLabel,onClick:t[0]||(t[0]=(...i)=>a.onPrevButtonClick&&a.onPrevButtonClick(...i))},{...n.previousButtonProps,...e.ptm("prevbutton")}),[w(e.$slots,"previcon",{},()=>[(o(),v(f(n.prevIcon?"span":"ChevronLeftIcon"),d({"aria-hidden":"true",class:n.prevIcon},e.ptm("previcon")),null,16,["class"]))])],16,K)),[[g]]):p("",!0),u("div",d({ref:"content",class:"p-tabview-nav-content",onScroll:t[1]||(t[1]=(...i)=>a.onScroll&&a.onScroll(...i))},e.ptm("navcontent")),[u("ul",d({ref:"nav",class:"p-tabview-nav",role:"tablist"},e.ptm("nav")),[(o(!0),h(T,null,x(a.tabs,(i,s)=>(o(),h("li",d({key:a.getKey(i,s),style:a.getTabProp(i,"headerStyle"),class:a.getTabHeaderClass(i,s),role:"presentation","data-index":s},{...a.getTabProp(i,"headerProps"),...a.getTabPT(i,"root"),...a.getTabPT(i,"header")}),[b((o(),h("a",d({id:a.getTabHeaderActionId(s),class:"p-tabview-nav-link p-tabview-header-action",tabindex:a.getTabProp(i,"disabled")||!a.isTabActive(s)?-1:n.tabindex,role:"tab","aria-disabled":a.getTabProp(i,"disabled"),"aria-selected":a.isTabActive(s),"aria-controls":a.getTabContentId(s),onClick:y=>a.onTabClick(y,i,s),onKeydown:y=>a.onTabKeyDown(y,i,s)},{...a.getTabProp(i,"headerActionProps"),...a.getTabPT(i,"headeraction")}),[i.props&&i.props.header?(o(),h("span",d({key:0,class:"p-tabview-title"},a.getTabPT(i,"headertitle")),B(i.props.header),17)):p("",!0),i.children&&i.children.header?(o(),v(f(i.children.header),{key:1})):p("",!0)],16,E)),[[g]])],16,S))),128)),u("li",d({ref:"inkbar",class:"p-tabview-ink-bar",role:"presentation","aria-hidden":"true"},e.ptm("inkbar")),null,16)],16)],16),n.scrollable&&!c.isNextButtonDisabled?b((o(),h("button",d({key:1,ref:"nextBtn",type:"button",class:"p-tabview-nav-next p-tabview-nav-btn p-link",tabindex:n.tabindex,"aria-label":a.nextButtonAriaLabel,onClick:t[2]||(t[2]=(...i)=>a.onNextButtonClick&&a.onNextButtonClick(...i))},{...n.nextButtonProps,...e.ptm("nextbutton")}),[w(e.$slots,"nexticon",{},()=>[(o(),v(f(n.nextIcon?"span":"ChevronRightIcon"),d({"aria-hidden":"true",class:n.nextIcon},e.ptm("nexticon")),null,16,["class"]))])],16,L)),[[g]]):p("",!0)],16),u("div",d({class:"p-tabview-panels"},e.ptm("panelcontainer")),[(o(!0),h(T,null,x(a.tabs,(i,s)=>(o(),h(T,{key:a.getKey(i,s)},[!n.lazy||a.isTabActive(s)?b((o(),h("div",d({key:0,style:a.getTabProp(i,"contentStyle"),class:a.getTabContentClass(i),role:"tabpanel","aria-labelledby":a.getTabHeaderActionId(s)},{...a.getTabProp(i,"contentProps"),...a.getTabPT(i,"root"),...a.getTabPT(i,"content")}),[(o(),v(f(i)))],16,N)),[[H,n.lazy?!0:a.isTabActive(s)]]):p("",!0)],64))),128))],16)],16)}function F(e,t){t===void 0&&(t={});var n=t.insertAt;if(!(!e||typeof document>"u")){var r=document.head||document.getElementsByTagName("head")[0],c=document.createElement("style");c.type="text/css",n==="top"&&r.firstChild?r.insertBefore(c,r.firstChild):r.appendChild(c),c.styleSheet?c.styleSheet.cssText=e:c.appendChild(document.createTextNode(e))}}var _=`
.p-tabview-nav-container {
    position: relative;
}
.p-tabview-scrollable .p-tabview-nav-container {
    overflow: hidden;
}
.p-tabview-nav-content {
    overflow-x: auto;
    overflow-y: hidden;
    scroll-behavior: smooth;
    scrollbar-width: none;
    overscroll-behavior: contain auto;
}
.p-tabview-nav {
    display: flex;
    margin: 0;
    padding: 0;
    list-style-type: none;
    flex: 1 1 auto;
}
.p-tabview-header-action {
    cursor: pointer;
    user-select: none;
    display: flex;
    align-items: center;
    position: relative;
    text-decoration: none;
    overflow: hidden;
}
.p-tabview-ink-bar {
    display: none;
    z-index: 1;
}
.p-tabview-header-action:focus {
    z-index: 1;
}
.p-tabview-title {
    line-height: 1;
    white-space: nowrap;
}
.p-tabview-nav-btn {
    position: absolute;
    top: 0;
    z-index: 2;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.p-tabview-nav-prev {
    left: 0;
}
.p-tabview-nav-next {
    right: 0;
}
.p-tabview-nav-content::-webkit-scrollbar {
    display: none;
}
`;F(_);D.render=V;var W={name:"TabPanel",extends:P,props:{header:null,headerStyle:null,headerClass:null,headerProps:null,headerActionProps:null,contentStyle:null,contentClass:null,contentProps:null,disabled:Boolean}};function z(e,t,n,r,c,a){return w(e.$slots,"default")}W.render=z;export{W as a,D as s};
