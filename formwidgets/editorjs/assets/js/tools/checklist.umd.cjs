(function(M,A){typeof exports=="object"&&typeof module<"u"?module.exports=A():typeof define=="function"&&define.amd?define(A):(M=typeof globalThis<"u"?globalThis:M||self,M.Checklist=A())})(this,function(){"use strict";function M(O){return O&&O.__esModule&&Object.prototype.hasOwnProperty.call(O,"default")?O.default:O}var A={exports:{}};(function(O,H){(function(k,x){O.exports=x()})(window,function(){return function(k){var x={};function l(i){if(x[i])return x[i].exports;var c=x[i]={i,l:!1,exports:{}};return k[i].call(c.exports,c,c.exports,l),c.l=!0,c.exports}return l.m=k,l.c=x,l.d=function(i,c,h){l.o(i,c)||Object.defineProperty(i,c,{enumerable:!0,get:h})},l.r=function(i){typeof Symbol<"u"&&Symbol.toStringTag&&Object.defineProperty(i,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(i,"__esModule",{value:!0})},l.t=function(i,c){if(1&c&&(i=l(i)),8&c||4&c&&typeof i=="object"&&i&&i.__esModule)return i;var h=Object.create(null);if(l.r(h),Object.defineProperty(h,"default",{enumerable:!0,value:i}),2&c&&typeof i!="string")for(var y in i)l.d(h,y,function(b){return i[b]}.bind(null,y));return h},l.n=function(i){var c=i&&i.__esModule?function(){return i.default}:function(){return i};return l.d(c,"a",c),c},l.o=function(i,c){return Object.prototype.hasOwnProperty.call(i,c)},l.p="/",l(l.s=6)}([function(k,x,l){var i=l(1);typeof i=="string"&&(i=[[k.i,i,""]]);var c={hmr:!0,transform:void 0,insertInto:void 0};l(3)(i,c),i.locals&&(k.exports=i.locals)},function(k,x,l){(k.exports=l(2)(!1)).push([k.i,`.cdx-checklist {
    gap: 6px;
    display: flex;
    flex-direction: column;
}

    .cdx-checklist__item {
        display: flex;
        box-sizing: content-box;
        align-items: flex-start;
    }

    .cdx-checklist__item-text {
            outline: none;
            flex-grow: 1;
            line-height: 1.57em;
        }

    .cdx-checklist__item-checkbox {
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            margin-right: 8px;
            margin-top: calc(1.57em/2 - 22px/2);
            cursor: pointer;
        }

    .cdx-checklist__item-checkbox svg {
                opacity: 0;
                height: 20px;
                width: 20px;
                position: absolute;
                left: -1px;
                top: -1px;
                max-height: 20px;
            }

    @media (hover: hover) {
                        .cdx-checklist__item-checkbox:not(.cdx-checklist__item-checkbox--no-hover):hover .cdx-checklist__item-checkbox-check svg {
                            opacity: 1;
                        }
            }

    .cdx-checklist__item-checkbox-check {
                cursor: pointer;
                display: inline-block;
                flex-shrink: 0;
                position: relative;
                width: 20px;
                height: 20px;
                box-sizing: border-box;
                margin-left: 0;
                border-radius: 5px;
                border: 1px solid #C9C9C9;
                background: #fff;
            }

    .cdx-checklist__item-checkbox-check::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    right: 0;
                    bottom: 0;
                    left: 0;
                    border-radius: 100%;
                    background-color: #369FFF;
                    visibility: hidden;
                    pointer-events: none;
                    transform: scale(1);
                    transition: transform 400ms ease-out, opacity 400ms;
                }

    @media (hover: hover) {
                        .cdx-checklist__item--checked .cdx-checklist__item-checkbox:not(.cdx-checklist__item--checked .cdx-checklist__item-checkbox--no-hover):hover .cdx-checklist__item-checkbox-check {
                            background: #0059AB;
                            border-color: #0059AB;
                        }
                }

    .cdx-checklist__item--checked .cdx-checklist__item-checkbox-check {
                    background: #369FFF;
                    border-color: #369FFF;
                }

    .cdx-checklist__item--checked .cdx-checklist__item-checkbox-check svg {
                        opacity: 1;
                    }

    .cdx-checklist__item--checked .cdx-checklist__item-checkbox-check svg path {
                            stroke: #fff;
                        }

    .cdx-checklist__item--checked .cdx-checklist__item-checkbox-check::before {
                        opacity: 0;
                        visibility: visible;
                        transform: scale(2.5);
                    }
`,""])},function(k,x){k.exports=function(l){var i=[];return i.toString=function(){return this.map(function(c){var h=function(y,b){var w=y[1]||"",p=y[3];if(!p)return w;if(b&&typeof btoa=="function"){var L=(I=p,"/*# sourceMappingURL=data:application/json;charset=utf-8;base64,"+btoa(unescape(encodeURIComponent(JSON.stringify(I))))+" */"),S=p.sources.map(function(u){return"/*# sourceURL="+p.sourceRoot+u+" */"});return[w].concat(S).concat([L]).join(`
`)}var I;return[w].join(`
`)}(c,l);return c[2]?"@media "+c[2]+"{"+h+"}":h}).join("")},i.i=function(c,h){typeof c=="string"&&(c=[[null,c,""]]);for(var y={},b=0;b<this.length;b++){var w=this[b][0];typeof w=="number"&&(y[w]=!0)}for(b=0;b<c.length;b++){var p=c[b];typeof p[0]=="number"&&y[p[0]]||(h&&!p[2]?p[2]=h:h&&(p[2]="("+p[2]+") and ("+h+")"),i.push(p))}},i}},function(k,x,l){var i,c,h={},y=(i=function(){return window&&document&&document.all&&!window.atob},function(){return c===void 0&&(c=i.apply(this,arguments)),c}),b=function(n){return document.querySelector(n)},w=function(n){var t={};return function(s){if(typeof s=="function")return s();if(t[s]===void 0){var a=b.call(this,s);if(window.HTMLIFrameElement&&a instanceof window.HTMLIFrameElement)try{a=a.contentDocument.head}catch{a=null}t[s]=a}return t[s]}}(),p=null,L=0,S=[],I=l(4);function u(n,t){for(var s=0;s<n.length;s++){var a=n[s],f=h[a.id];if(f){f.refs++;for(var d=0;d<f.parts.length;d++)f.parts[d](a.parts[d]);for(;d<a.parts.length;d++)f.parts.push(C(a.parts[d],t))}else{var g=[];for(d=0;d<a.parts.length;d++)g.push(C(a.parts[d],t));h[a.id]={id:a.id,refs:1,parts:g}}}}function e(n,t){for(var s=[],a={},f=0;f<n.length;f++){var d=n[f],g=t.base?d[0]+t.base:d[0],v={css:d[1],media:d[2],sourceMap:d[3]};a[g]?a[g].parts.push(v):s.push(a[g]={id:g,parts:[v]})}return s}function r(n,t){var s=w(n.insertInto);if(!s)throw new Error("Couldn't find a style target. This probably means that the value for the 'insertInto' parameter is invalid.");var a=S[S.length-1];if(n.insertAt==="top")a?a.nextSibling?s.insertBefore(t,a.nextSibling):s.appendChild(t):s.insertBefore(t,s.firstChild),S.push(t);else if(n.insertAt==="bottom")s.appendChild(t);else{if(typeof n.insertAt!="object"||!n.insertAt.before)throw new Error(`[Style Loader]

 Invalid value for parameter 'insertAt' ('options.insertAt') found.
 Must be 'top', 'bottom', or Object.
 (https://github.com/webpack-contrib/style-loader#insertat)
`);var f=w(n.insertInto+" "+n.insertAt.before);s.insertBefore(t,f)}}function o(n){if(n.parentNode===null)return!1;n.parentNode.removeChild(n);var t=S.indexOf(n);t>=0&&S.splice(t,1)}function m(n){var t=document.createElement("style");return n.attrs.type===void 0&&(n.attrs.type="text/css"),_(t,n.attrs),r(n,t),t}function _(n,t){Object.keys(t).forEach(function(s){n.setAttribute(s,t[s])})}function C(n,t){var s,a,f,d;if(t.transform&&n.css){if(!(d=t.transform(n.css)))return function(){};n.css=d}if(t.singleton){var g=L++;s=p||(p=m(t)),a=R.bind(null,s,g,!1),f=R.bind(null,s,g,!0)}else n.sourceMap&&typeof URL=="function"&&typeof URL.createObjectURL=="function"&&typeof URL.revokeObjectURL=="function"&&typeof Blob=="function"&&typeof btoa=="function"?(s=function(v){var j=document.createElement("link");return v.attrs.type===void 0&&(v.attrs.type="text/css"),v.attrs.rel="stylesheet",_(j,v.attrs),r(v,j),j}(t),a=F.bind(null,s,t),f=function(){o(s),s.href&&URL.revokeObjectURL(s.href)}):(s=m(t),a=B.bind(null,s),f=function(){o(s)});return a(n),function(v){if(v){if(v.css===n.css&&v.media===n.media&&v.sourceMap===n.sourceMap)return;a(n=v)}else f()}}k.exports=function(n,t){if(typeof DEBUG<"u"&&DEBUG&&typeof document!="object")throw new Error("The style-loader cannot be used in a non-browser environment");(t=t||{}).attrs=typeof t.attrs=="object"?t.attrs:{},t.singleton||typeof t.singleton=="boolean"||(t.singleton=y()),t.insertInto||(t.insertInto="head"),t.insertAt||(t.insertAt="bottom");var s=e(n,t);return u(s,t),function(a){for(var f=[],d=0;d<s.length;d++){var g=s[d];(v=h[g.id]).refs--,f.push(v)}for(a&&u(e(a,t),t),d=0;d<f.length;d++){var v;if((v=f[d]).refs===0){for(var j=0;j<v.parts.length;j++)v.parts[j]();delete h[v.id]}}}};var E,T=(E=[],function(n,t){return E[n]=t,E.filter(Boolean).join(`
`)});function R(n,t,s,a){var f=s?"":a.css;if(n.styleSheet)n.styleSheet.cssText=T(t,f);else{var d=document.createTextNode(f),g=n.childNodes;g[t]&&n.removeChild(g[t]),g.length?n.insertBefore(d,g[t]):n.appendChild(d)}}function B(n,t){var s=t.css,a=t.media;if(a&&n.setAttribute("media",a),n.styleSheet)n.styleSheet.cssText=s;else{for(;n.firstChild;)n.removeChild(n.firstChild);n.appendChild(document.createTextNode(s))}}function F(n,t,s){var a=s.css,f=s.sourceMap,d=t.convertToAbsoluteUrls===void 0&&f;(t.convertToAbsoluteUrls||d)&&(a=I(a)),f&&(a+=`
/*# sourceMappingURL=data:application/json;base64,`+btoa(unescape(encodeURIComponent(JSON.stringify(f))))+" */");var g=new Blob([a],{type:"text/css"}),v=n.href;n.href=URL.createObjectURL(g),v&&URL.revokeObjectURL(v)}},function(k,x){k.exports=function(l){var i=typeof window<"u"&&window.location;if(!i)throw new Error("fixUrls requires window.location");if(!l||typeof l!="string")return l;var c=i.protocol+"//"+i.host,h=c+i.pathname.replace(/\/[^\/]*$/,"/");return l.replace(/url\s*\(((?:[^)(]|\((?:[^)(]+|\([^)(]*\))*\))*)\)/gi,function(y,b){var w,p=b.trim().replace(/^"(.*)"$/,function(L,S){return S}).replace(/^'(.*)'$/,function(L,S){return S});return/^(#|data:|http:\/\/|https:\/\/|file:\/\/\/|\s*$)/i.test(p)?y:(w=p.indexOf("//")===0?p:p.indexOf("/")===0?c+p:h+p.replace(/^\.\//,""),"url("+JSON.stringify(w)+")")})}},function(k,x,l){Element.prototype.matches||(Element.prototype.matches=Element.prototype.msMatchesSelector||Element.prototype.webkitMatchesSelector),Element.prototype.closest||(Element.prototype.closest=function(i){var c=this;if(!document.documentElement.contains(c))return null;do{if(c.matches(i))return c;c=c.parentElement||c.parentNode}while(c!==null&&c.nodeType===1);return null})},function(k,x,l){l.r(x),l.d(x,"default",function(){return I}),l(0);const i='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M7 12L10.4884 15.8372C10.5677 15.9245 10.705 15.9245 10.7844 15.8372L17 9"/></svg>';function c(u){return function(e){if(Array.isArray(e))return h(e)}(u)||function(e){if(typeof Symbol<"u"&&Symbol.iterator in Object(e))return Array.from(e)}(u)||function(e,r){if(e){if(typeof e=="string")return h(e,r);var o=Object.prototype.toString.call(e).slice(8,-1);if(o==="Object"&&e.constructor&&(o=e.constructor.name),o==="Map"||o==="Set")return Array.from(e);if(o==="Arguments"||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(o))return h(e,r)}}(u)||function(){throw new TypeError(`Invalid attempt to spread non-iterable instance.
In order to be iterable, non-array objects must have a [Symbol.iterator]() method.`)}()}function h(u,e){(e==null||e>u.length)&&(e=u.length);for(var r=0,o=new Array(e);r<e;r++)o[r]=u[r];return o}function y(){var u=document.activeElement,e=window.getSelection().getRangeAt(0),r=e.cloneRange();return r.selectNodeContents(u),r.setStart(e.endContainer,e.endOffset),r.extractContents()}function b(u){var e,r=arguments.length>1&&arguments[1]!==void 0?arguments[1]:null,o=arguments.length>2&&arguments[2]!==void 0?arguments[2]:{},m=document.createElement(u);Array.isArray(r)?(e=m.classList).add.apply(e,c(r)):r&&m.classList.add(r);for(var _ in o)m[_]=o[_];return m}function w(u){return u.innerHTML.replace("<br>"," ").trim()}function p(u){var e=arguments.length>1&&arguments[1]!==void 0&&arguments[1],r=arguments.length>2&&arguments[2]!==void 0?arguments[2]:void 0,o=document.createRange(),m=window.getSelection();o.selectNodeContents(u),r!==void 0&&(o.setStart(u,r),o.setEnd(u,r)),o.collapse(e),m.removeAllRanges(),m.addRange(o)}l(5);function L(u,e){for(var r=0;r<e.length;r++){var o=e[r];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(u,o.key,o)}}function S(u,e,r){return e&&L(u.prototype,e),r&&L(u,r),u}var I=function(){function u(e){var r=e.data,o=(e.config,e.api),m=e.readOnly;(function(_,C){if(!(_ instanceof C))throw new TypeError("Cannot call a class as a function")})(this,u),this._elements={wrapper:null,items:[]},this.readOnly=m,this.api=o,this.data=r||{}}return S(u,null,[{key:"isReadOnlySupported",get:function(){return!0}},{key:"enableLineBreaks",get:function(){return!0}},{key:"toolbox",get:function(){return{icon:'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M9.2 12L11.0586 13.8586C11.1367 13.9367 11.2633 13.9367 11.3414 13.8586L14.7 10.5"/><rect width="14" height="14" x="5" y="5" stroke="currentColor" stroke-width="2" rx="4"/></svg>',title:"Checklist"}}},{key:"conversionConfig",get:function(){return{export:function(e){return e.items.map(function(r){return r.text}).join(". ")},import:function(e){return{items:[{text:e,checked:!1}]}}}}}]),S(u,[{key:"render",value:function(){var e=this;return this._elements.wrapper=b("div",[this.CSS.baseBlock,this.CSS.wrapper]),this.data.items||(this.data.items=[{text:"",checked:!1}]),this.data.items.forEach(function(r){var o=e.createChecklistItem(r);e._elements.wrapper.appendChild(o)}),this.readOnly||(this._elements.wrapper.addEventListener("keydown",function(r){switch(r.keyCode){case 13:e.enterPressed(r);break;case 8:e.backspace(r)}},!1),this._elements.wrapper.addEventListener("click",function(r){e.toggleCheckbox(r)})),this._elements.wrapper}},{key:"save",value:function(){var e=this,r=this.items.map(function(o){return{text:w(e.getItemInput(o)),checked:o.classList.contains(e.CSS.itemChecked)}});return{items:r=r.filter(function(o){return o.text.trim().length!==0})}}},{key:"validate",value:function(e){return!!e.items.length}},{key:"toggleCheckbox",value:function(e){var r=this,o=e.target.closest(".".concat(this.CSS.item)),m=o.querySelector(".".concat(this.CSS.checkboxContainer));m.contains(e.target)&&(o.classList.toggle(this.CSS.itemChecked),m.classList.add(this.CSS.noHover),m.addEventListener("mouseleave",function(){return r.removeSpecialHoverBehavior(m)},{once:!0}))}},{key:"createChecklistItem",value:function(){var e=arguments.length>0&&arguments[0]!==void 0?arguments[0]:{},r=b("div",this.CSS.item),o=b("span",this.CSS.checkbox),m=b("div",this.CSS.checkboxContainer),_=b("div",this.CSS.textField,{innerHTML:e.text?e.text:"",contentEditable:!this.readOnly});return e.checked&&r.classList.add(this.CSS.itemChecked),o.innerHTML=i,m.appendChild(o),r.appendChild(m),r.appendChild(_),r}},{key:"enterPressed",value:function(e){e.preventDefault();var r=this.items,o=document.activeElement.closest(".".concat(this.CSS.item));if(r.indexOf(o)===r.length-1&&w(this.getItemInput(o)).length===0){var m=this.api.blocks.getCurrentBlockIndex();return o.remove(),this.api.blocks.insert(),void this.api.caret.setToBlock(m+1)}var _,C,E=y(),T=(_=E,(C=document.createElement("div")).appendChild(_),C.innerHTML),R=this.createChecklistItem({text:T,checked:!1});this._elements.wrapper.insertBefore(R,o.nextSibling),p(this.getItemInput(R),!0)}},{key:"backspace",value:function(e){var r=e.target.closest(".".concat(this.CSS.item)),o=this.items.indexOf(r),m=this.items[o-1];if(m&&window.getSelection().focusOffset===0){e.preventDefault();var _=y(),C=this.getItemInput(m),E=C.childNodes.length;C.appendChild(_),p(C,void 0,E),r.remove()}}},{key:"removeSpecialHoverBehavior",value:function(e){e.classList.remove(this.CSS.noHover)}},{key:"getItemInput",value:function(e){return e.querySelector(".".concat(this.CSS.textField))}},{key:"CSS",get:function(){return{baseBlock:this.api.styles.block,wrapper:"cdx-checklist",item:"cdx-checklist__item",itemChecked:"cdx-checklist__item--checked",noHover:"cdx-checklist__item-checkbox--no-hover",checkbox:"cdx-checklist__item-checkbox-check",textField:"cdx-checklist__item-text",checkboxContainer:"cdx-checklist__item-checkbox"}}},{key:"items",get:function(){return Array.from(this._elements.wrapper.querySelectorAll(".".concat(this.CSS.item)))}}]),u}()}]).default})})(A);var U=A.exports;return M(U)});
