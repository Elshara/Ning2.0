(function(){var $wnd = window;var $doc = $wnd.document;var $moduleName, $moduleBase;var _,nD='com.google.gwt.core.client.',oD='com.google.gwt.i18n.client.',pD='com.google.gwt.lang.',qD='com.google.gwt.user.client.',rD='com.google.gwt.user.client.impl.',sD='com.google.gwt.user.client.ui.',tD='com.google.gwt.user.client.ui.impl.',uD='com.ning.client.',vD='java.lang.',wD='java.util.';function mD(){}
function Cw(a){return this===a;}
function Dw(){return ay(this);}
function Ew(){return this.tN+'@'+this.hC();}
function Aw(){}
_=Aw.prototype={};_.eQ=Cw;_.hC=Dw;_.tS=Ew;_.toString=function(){return this.tS();};_.tN=vD+'Object';_.tI=1;function o(){return v();}
function p(a){return a==null?null:a.tN;}
var q=null;function t(a){return a==null?0:a.$H?a.$H:(a.$H=w());}
function u(a){return a==null?0:a.$H?a.$H:(a.$H=w());}
function v(){return $moduleBase;}
function w(){return ++y;}
var y=0;function B(b,a){if(!ac(a,2)){return false;}return F(b,Fb(a,2));}
function C(a){return t(a);}
function D(){return [];}
function E(){return {};}
function ab(a){return B(this,a);}
function F(a,b){return a===b;}
function bb(){return C(this);}
function db(){return cb(this);}
function cb(a){if(a.toString)return a.toString();return '[object]';}
function z(){}
_=z.prototype=new Aw();_.eQ=ab;_.hC=bb;_.tS=db;_.tN=nD+'JavaScriptObject';_.tI=7;function ib(){ib=mD;lb=aC(new fB());}
function fb(b,a){ib();if(a===null||ox('',a)){throw dw(new cw(),'Cannot create a Dictionary with a null or empty name');}b.b='Dictionary '+a;hb(b,a);if(b.a===null){throw gD(new fD(),"Cannot find JavaScript object with the name '"+a+"'",a,null);}return b;}
function gb(b,a){for(x in b.a){a.C(x);}}
function hb(c,b){try{if(typeof $wnd[b]!='object'){nb(b);}c.a=$wnd[b];}catch(a){nb(b);}}
function jb(b,a){var c=b.a[a];if(c==null|| !Object.prototype.hasOwnProperty.call(b.a,a)){b.nc(a);}return String(c);}
function kb(b){var a;a=AC(new zC());gb(b,a);return a;}
function mb(a){ib();var b;b=Fb(gC(lb,a),3);if(b===null){b=fb(new eb(),a);hC(lb,a,b);}return b;}
function ob(b){var a,c;c=kb(this);a="Cannot find '"+b+"' in "+this;if(c.a.c<20){a+='\n keys found: '+c;}throw gD(new fD(),a,this.b,b);}
function nb(a){ib();throw gD(new fD(),"'"+a+"' is not a JavaScript object and cannot be used as a Dictionary",null,a);}
function pb(){return this.b;}
function eb(){}
_=eb.prototype=new Aw();_.nc=ob;_.tS=pb;_.tN=oD+'Dictionary';_.tI=8;_.a=null;_.b=null;var lb;function rb(c,a,d,b,e){c.a=a;c.b=b;c.tN=e;c.tI=d;return c;}
function tb(a,b,c){return a[b]=c;}
function ub(b,a){return b[a];}
function wb(b,a){return b[a];}
function vb(a){return a.length;}
function yb(e,d,c,b,a){return xb(e,d,c,b,0,vb(b),a);}
function xb(j,i,g,c,e,a,b){var d,f,h;if((f=ub(c,e))<0){throw new rw();}h=rb(new qb(),f,ub(i,e),ub(g,e),j);++e;if(e<a){j=tx(j,1);for(d=0;d<f;++d){tb(h,d,xb(j,i,g,c,e,a,b));}}else{for(d=0;d<f;++d){tb(h,d,b);}}return h;}
function zb(f,e,c,g){var a,b,d;b=vb(g);d=rb(new qb(),b,e,c,f);for(a=0;a<b;++a){tb(d,a,wb(g,a));}return d;}
function Ab(a,b,c){if(c!==null&&a.b!=0&& !ac(c,a.b)){throw new pv();}return tb(a,b,c);}
function qb(){}
_=qb.prototype=new Aw();_.tN=pD+'Array';_.tI=0;function Db(b,a){return !(!(b&&ec[b][a]));}
function Eb(a){return String.fromCharCode(a);}
function Fb(b,a){if(b!=null)Db(b.tI,a)||dc();return b;}
function ac(b,a){return b!=null&&Db(b.tI,a);}
function bc(a){return a&65535;}
function dc(){throw new Bv();}
function cc(a){if(a!==null){throw new Bv();}return a;}
function fc(b,d){_=d.prototype;if(b&& !(b.tI>=_.tI)){var c=b.toString;for(var a in _){b[a]=_[a];}b.toString=c;}return b;}
var ec;function cy(b,a){b.a=a;return b;}
function ey(){var a,b;a=p(this);b=this.a;if(b!==null){return a+': '+b;}else{return a;}}
function by(){}
_=by.prototype=new Aw();_.tS=ey;_.tN=vD+'Throwable';_.tI=3;_.a=null;function aw(b,a){cy(b,a);return b;}
function Fv(){}
_=Fv.prototype=new by();_.tN=vD+'Exception';_.tI=4;function ax(b,a){aw(b,a);return b;}
function Fw(){}
_=Fw.prototype=new Fv();_.tN=vD+'RuntimeException';_.tI=5;function jc(b,a){return b;}
function ic(){}
_=ic.prototype=new Fw();_.tN=qD+'CommandCanceledException';_.tI=9;function Fc(a){a.a=nc(new mc(),a);a.b=oA(new mA());a.d=rc(new qc(),a);a.f=vc(new uc(),a);}
function ad(a){Fc(a);return a;}
function cd(c){var a,b,d;a=xc(c.f);Ac(c.f);b=null;if(ac(a,5)){b=jc(new ic(),Fb(a,5));}else{}if(b!==null){d=q;}fd(c,false);ed(c);}
function dd(e,d){var a,b,c,f;f=false;try{fd(e,true);Bc(e.f,e.b.b);xf(e.a,10000);while(yc(e.f)){b=zc(e.f);c=true;try{if(b===null){return;}if(ac(b,5)){a=Fb(b,5);a.eb();}else{}}finally{f=Cc(e.f);if(f){return;}if(c){Ac(e.f);}}if(id(Fx(),d)){return;}}}finally{if(!f){uf(e.a);fd(e,false);ed(e);}}}
function ed(a){if(!vA(a.b)&& !a.e&& !a.c){gd(a,true);xf(a.d,1);}}
function fd(b,a){b.c=a;}
function gd(b,a){b.e=a;}
function hd(b,a){pA(b.b,a);ed(b);}
function id(a,b){return qw(a-b)>=100;}
function lc(){}
_=lc.prototype=new Aw();_.tN=qD+'CommandExecutor';_.tI=0;_.c=false;_.e=false;function vf(){vf=mD;Df=oA(new mA());{Cf();}}
function tf(a){vf();return a;}
function uf(a){if(a.b){yf(a.c);}else{zf(a.c);}xA(Df,a);}
function wf(a){if(!a.b){xA(Df,a);}a.pc();}
function xf(b,a){if(a<=0){throw dw(new cw(),'must be positive');}uf(b);b.b=false;b.c=Af(b,a);pA(Df,b);}
function yf(a){vf();$wnd.clearInterval(a);}
function zf(a){vf();$wnd.clearTimeout(a);}
function Af(b,a){vf();return $wnd.setTimeout(function(){b.fb();},a);}
function Bf(){var a;a=q;{wf(this);}}
function Cf(){vf();bg(new pf());}
function of(){}
_=of.prototype=new Aw();_.fb=Bf;_.tN=qD+'Timer';_.tI=10;_.b=false;_.c=0;var Df;function oc(){oc=mD;vf();}
function nc(b,a){oc();b.a=a;tf(b);return b;}
function pc(){if(!this.a.c){return;}cd(this.a);}
function mc(){}
_=mc.prototype=new of();_.pc=pc;_.tN=qD+'CommandExecutor$1';_.tI=11;function sc(){sc=mD;vf();}
function rc(b,a){sc();b.a=a;tf(b);return b;}
function tc(){gd(this.a,false);dd(this.a,Fx());}
function qc(){}
_=qc.prototype=new of();_.pc=tc;_.tN=qD+'CommandExecutor$2';_.tI=12;function vc(b,a){b.d=a;return b;}
function xc(a){return sA(a.d.b,a.b);}
function yc(a){return a.c<a.a;}
function zc(b){var a;b.b=b.c;a=sA(b.d.b,b.c++);if(b.c>=b.a){b.c=0;}return a;}
function Ac(a){wA(a.d.b,a.b);--a.a;if(a.b<=a.c){if(--a.c<0){a.c=0;}}a.b=(-1);}
function Bc(b,a){b.a=a;}
function Cc(a){return a.b==(-1);}
function Dc(){return yc(this);}
function Ec(){return zc(this);}
function uc(){}
_=uc.prototype=new Aw();_.mb=Dc;_.xb=Ec;_.tN=qD+'CommandExecutor$CircularIterator';_.tI=0;_.a=0;_.b=(-1);_.c=0;function ld(){ld=mD;te=oA(new mA());{le=new mg();yg(le);}}
function md(b,a){ld();Dg(le,b,a);}
function nd(a,b){ld();return rg(le,a,b);}
function od(){ld();return Fg(le,'div');}
function pd(a){ld();return ah(le,a);}
function qd(){ld();return Fg(le,'span');}
function rd(){ld();return Fg(le,'tbody');}
function sd(){ld();return Fg(le,'td');}
function td(){ld();return Fg(le,'tr');}
function ud(){ld();return Fg(le,'table');}
function xd(b,a,d){ld();var c;c=q;{wd(b,a,d);}}
function wd(b,a,c){ld();var d;if(a===se){if(ae(b)==8192){se=null;}}d=vd;vd=b;try{c.Ab(b);}finally{vd=d;}}
function yd(b,a){ld();bh(le,b,a);}
function zd(a){ld();return ch(le,a);}
function Ad(a){ld();return dh(le,a);}
function Bd(a){ld();return eh(le,a);}
function Cd(a){ld();return fh(le,a);}
function Dd(a){ld();return gh(le,a);}
function Ed(a){ld();return sg(le,a);}
function Fd(a){ld();return tg(le,a);}
function ae(a){ld();return hh(le,a);}
function be(a){ld();ug(le,a);}
function ce(a){ld();return vg(le,a);}
function de(a){ld();return ih(le,a);}
function ge(a,b){ld();return lh(le,a,b);}
function ee(a,b){ld();return jh(le,a,b);}
function fe(a,b){ld();return kh(le,a,b);}
function he(a){ld();return mh(le,a);}
function ie(a){ld();return wg(le,a);}
function je(a){ld();return nh(le,a);}
function ke(a){ld();return xg(le,a);}
function me(c,b,d,a){ld();og(le,c,b,d,a);}
function ne(b,a){ld();return zg(le,b,a);}
function oe(a){ld();var b,c;c=true;if(te.b>0){b=cc(sA(te,te.b-1));if(!(c=null.Ac())){yd(a,true);be(a);}}return c;}
function pe(a){ld();if(se!==null&&nd(a,se)){se=null;}Ag(le,a);}
function qe(b,a){ld();oh(le,b,a);}
function re(b,a){ld();ph(le,b,a);}
function ue(a){ld();se=a;Bg(le,a);}
function ve(b,a,c){ld();qh(le,b,a,c);}
function ye(a,b,c){ld();th(le,a,b,c);}
function we(a,b,c){ld();rh(le,a,b,c);}
function xe(a,b,c){ld();sh(le,a,b,c);}
function ze(a,b){ld();uh(le,a,b);}
function Ae(a,b){ld();vh(le,a,b);}
function Be(b,a,c){ld();wh(le,b,a,c);}
function Ce(a,b){ld();Cg(le,a,b);}
function De(a){ld();return xh(le,a);}
var vd=null,le=null,se=null,te;function Fe(){Fe=mD;bf=ad(new lc());}
function af(a){Fe();if(a===null){throw uw(new tw(),'cmd can not be null');}hd(bf,a);}
var bf;function ef(a){if(ac(a,6)){return nd(this,Fb(a,6));}return B(fc(this,cf),a);}
function ff(){return C(fc(this,cf));}
function gf(){return De(this);}
function cf(){}
_=cf.prototype=new z();_.eQ=ef;_.hC=ff;_.tS=gf;_.tN=qD+'Element';_.tI=13;function lf(a){return B(fc(this,hf),a);}
function mf(){return C(fc(this,hf));}
function nf(){return ce(this);}
function hf(){}
_=hf.prototype=new z();_.eQ=lf;_.hC=mf;_.tS=nf;_.tN=qD+'Event';_.tI=14;function rf(){while((vf(),Df).b>0){uf(Fb(sA((vf(),Df),0),7));}}
function sf(){return null;}
function pf(){}
_=pf.prototype=new Aw();_.hc=rf;_.ic=sf;_.tN=qD+'Timer$1';_.tI=15;function ag(){ag=mD;cg=oA(new mA());kg=oA(new mA());{gg();}}
function bg(a){ag();pA(cg,a);}
function dg(){ag();var a,b;for(a=zy(cg);sy(a);){b=Fb(ty(a),8);b.hc();}}
function eg(){ag();var a,b,c,d;d=null;for(a=zy(cg);sy(a);){b=Fb(ty(a),8);c=b.ic();{d=c;}}return d;}
function fg(){ag();var a,b;for(a=zy(kg);sy(a);){b=cc(ty(a));null.Ac();}}
function gg(){ag();__gwt_initHandlers(function(){jg();},function(){return ig();},function(){hg();$wnd.onresize=null;$wnd.onbeforeclose=null;$wnd.onclose=null;});}
function hg(){ag();var a;a=q;{dg();}}
function ig(){ag();var a;a=q;{return eg();}}
function jg(){ag();var a;a=q;{fg();}}
var cg,kg;function Dg(c,b,a){b.appendChild(a);}
function Fg(b,a){return $doc.createElement(a);}
function ah(c,a){var b;b=Fg(c,'select');if(a){rh(c,b,'multiple',true);}return b;}
function bh(c,b,a){b.cancelBubble=a;}
function ch(b,a){return !(!a.altKey);}
function dh(b,a){return !(!a.ctrlKey);}
function eh(b,a){return a.which||(a.keyCode|| -1);}
function fh(b,a){return !(!a.metaKey);}
function gh(b,a){return !(!a.shiftKey);}
function hh(b,a){switch(a.type){case 'blur':return 4096;case 'change':return 1024;case 'click':return 1;case 'dblclick':return 2;case 'focus':return 2048;case 'keydown':return 128;case 'keypress':return 256;case 'keyup':return 512;case 'load':return 32768;case 'losecapture':return 8192;case 'mousedown':return 4;case 'mousemove':return 64;case 'mouseout':return 32;case 'mouseover':return 16;case 'mouseup':return 8;case 'scroll':return 16384;case 'error':return 65536;case 'mousewheel':return 131072;case 'DOMMouseScroll':return 131072;}}
function ih(c,b){var a=$doc.getElementById(b);return a||null;}
function lh(d,a,b){var c=a[b];return c==null?null:String(c);}
function jh(c,a,b){return !(!a[b]);}
function kh(d,a,c){var b=parseInt(a[c]);if(!b){return 0;}return b;}
function mh(b,a){return a.__eventBits||0;}
function nh(c,a){var b=a.innerHTML;return b==null?null:b;}
function oh(c,b,a){b.removeChild(a);}
function ph(c,b,a){b.removeAttribute(a);}
function qh(c,b,a,d){b.setAttribute(a,d);}
function th(c,a,b,d){a[b]=d;}
function rh(c,a,b,d){a[b]=d;}
function sh(c,a,b,d){a[b]=d;}
function uh(c,a,b){a.__listener=b;}
function vh(c,a,b){if(!b){b='';}a.innerHTML=b;}
function wh(c,b,a,d){b.style[a]=d;}
function xh(b,a){return a.outerHTML;}
function lg(){}
_=lg.prototype=new Aw();_.tN=rD+'DOMImpl';_.tI=0;function rg(c,a,b){return a==b;}
function sg(b,a){return a.target||null;}
function tg(b,a){return a.relatedTarget||null;}
function ug(b,a){a.preventDefault();}
function vg(b,a){return a.toString();}
function wg(c,b){var a=b.firstChild;while(a&&a.nodeType!=1)a=a.nextSibling;return a||null;}
function xg(c,a){var b=a.parentNode;if(b==null){return null;}if(b.nodeType!=1)b=null;return b||null;}
function yg(d){$wnd.__dispatchCapturedMouseEvent=function(b){if($wnd.__dispatchCapturedEvent(b)){var a=$wnd.__captureElem;if(a&&a.__listener){xd(b,a,a.__listener);b.stopPropagation();}}};$wnd.__dispatchCapturedEvent=function(a){if(!oe(a)){a.stopPropagation();a.preventDefault();return false;}return true;};$wnd.addEventListener('click',$wnd.__dispatchCapturedMouseEvent,true);$wnd.addEventListener('dblclick',$wnd.__dispatchCapturedMouseEvent,true);$wnd.addEventListener('mousedown',$wnd.__dispatchCapturedMouseEvent,true);$wnd.addEventListener('mouseup',$wnd.__dispatchCapturedMouseEvent,true);$wnd.addEventListener('mousemove',$wnd.__dispatchCapturedMouseEvent,true);$wnd.addEventListener('mousewheel',$wnd.__dispatchCapturedMouseEvent,true);$wnd.addEventListener('keydown',$wnd.__dispatchCapturedEvent,true);$wnd.addEventListener('keyup',$wnd.__dispatchCapturedEvent,true);$wnd.addEventListener('keypress',$wnd.__dispatchCapturedEvent,true);$wnd.__dispatchEvent=function(b){var c,a=this;while(a&& !(c=a.__listener))a=a.parentNode;if(a&&a.nodeType!=1)a=null;if(c)xd(b,a,c);};$wnd.__captureElem=null;}
function zg(c,b,a){while(a){if(b==a){return true;}a=a.parentNode;if(a&&a.nodeType!=1){a=null;}}return false;}
function Ag(b,a){if(a==$wnd.__captureElem)$wnd.__captureElem=null;}
function Bg(b,a){$wnd.__captureElem=a;}
function Cg(c,b,a){b.__eventBits=a;b.onclick=a&1?$wnd.__dispatchEvent:null;b.ondblclick=a&2?$wnd.__dispatchEvent:null;b.onmousedown=a&4?$wnd.__dispatchEvent:null;b.onmouseup=a&8?$wnd.__dispatchEvent:null;b.onmouseover=a&16?$wnd.__dispatchEvent:null;b.onmouseout=a&32?$wnd.__dispatchEvent:null;b.onmousemove=a&64?$wnd.__dispatchEvent:null;b.onkeydown=a&128?$wnd.__dispatchEvent:null;b.onkeypress=a&256?$wnd.__dispatchEvent:null;b.onkeyup=a&512?$wnd.__dispatchEvent:null;b.onchange=a&1024?$wnd.__dispatchEvent:null;b.onfocus=a&2048?$wnd.__dispatchEvent:null;b.onblur=a&4096?$wnd.__dispatchEvent:null;b.onlosecapture=a&8192?$wnd.__dispatchEvent:null;b.onscroll=a&16384?$wnd.__dispatchEvent:null;b.onload=a&32768?$wnd.__dispatchEvent:null;b.onerror=a&65536?$wnd.__dispatchEvent:null;b.onmousewheel=a&131072?$wnd.__dispatchEvent:null;}
function pg(){}
_=pg.prototype=new lg();_.tN=rD+'DOMImplStandard';_.tI=0;function og(e,c,d,f,a){var b=new Option(d,f);if(a== -1||a>c.children.length-1){c.appendChild(b);}else{c.insertBefore(b,c.children[a]);}}
function mg(){}
_=mg.prototype=new pg();_.tN=rD+'DOMImplSafari';_.tI=0;function up(b,a){vp(b,xp(b)+Eb(45)+a);}
function vp(b,a){eq(b.o,a,true);}
function xp(a){return cq(a.o);}
function yp(b,a){zp(b,xp(b)+Eb(45)+a);}
function zp(b,a){eq(b.o,a,false);}
function Ap(d,b,a){var c=b.parentNode;if(!c){return;}c.insertBefore(a,b);c.removeChild(b);}
function Bp(b,a){if(b.o!==null){Ap(b,b.o,a);}b.o=a;}
function Cp(b,a){Be(b.o,'height',a);}
function Dp(b,a){dq(b.o,a);}
function Ep(a,b){if(b===null||sx(b)==0){re(a.o,'title');}else{ve(a.o,'title',b);}}
function Fp(a,b){Be(a.o,'width',b);}
function aq(b,a){Ce(b.o,a|he(b.o));}
function bq(a){return ge(a,'className');}
function cq(a){var b,c;b=bq(a);c=px(b,32);if(c>=0){return ux(b,0,c);}return b;}
function dq(a,b){ye(a,'className',b);}
function eq(c,j,a){var b,d,e,f,g,h,i;if(c===null){throw ax(new Fw(),'Null widget handle. If you are creating a composite, ensure that initWidget() has been called.');}j=vx(j);if(sx(j)==0){throw dw(new cw(),'Style names cannot be empty');}i=bq(c);e=qx(i,j);while(e!=(-1)){if(e==0||mx(i,e-1)==32){f=e+sx(j);g=sx(i);if(f==g||f<g&&mx(i,f)==32){break;}}e=rx(i,j,e+1);}if(a){if(e==(-1)){if(sx(i)>0){i+=' ';}ye(c,'className',i+j);}}else{if(e!=(-1)){b=vx(ux(i,0,e));d=vx(tx(i,e+sx(j)));if(sx(b)==0){h=d;}else if(sx(d)==0){h=b;}else{h=b+' '+d;}ye(c,'className',h);}}}
function fq(){if(this.o===null){return '(null handle)';}return De(this.o);}
function tp(){}
_=tp.prototype=new Aw();_.tS=fq;_.tN=sD+'UIObject';_.tI=0;_.o=null;function Fq(a){if(a.m){throw gw(new fw(),"Should only call onAttach when the widget is detached from the browser's document");}a.m=true;ze(a.o,a);a.bb();a.fc();}
function ar(a){if(!a.m){throw gw(new fw(),"Should only call onDetach when the widget is attached to the browser's document");}try{a.gc();}finally{a.cb();ze(a.o,null);a.m=false;}}
function br(a){if(a.n!==null){a.n.mc(a);}else if(a.n!==null){throw gw(new fw(),"This widget's parent does not implement HasWidgets");}}
function cr(b,a){if(b.m){ze(b.o,null);}Bp(b,a);if(b.m){ze(a,b);}}
function dr(c,b){var a;a=c.n;if(b===null){if(a!==null&&a.m){c.ac();}c.n=null;}else{if(a!==null){throw gw(new fw(),'Cannot set a new parent without first clearing the old parent');}c.n=b;if(b.m){c.zb();}}}
function er(){}
function fr(){}
function gr(){Fq(this);}
function hr(a){}
function ir(){ar(this);}
function jr(){}
function kr(){}
function lr(a){cr(this,a);}
function nq(){}
_=nq.prototype=new tp();_.bb=er;_.cb=fr;_.zb=gr;_.Ab=hr;_.ac=ir;_.fc=jr;_.gc=kr;_.rc=lr;_.tN=sD+'Widget';_.tI=16;_.m=false;_.n=null;function hn(b,a){dr(a,b);}
function kn(b,a){dr(a,null);}
function ln(){var a,b;for(b=this.ub();sq(b);){a=tq(b);a.zb();}}
function mn(){var a,b;for(b=this.ub();sq(b);){a=tq(b);a.ac();}}
function nn(){}
function on(){}
function gn(){}
_=gn.prototype=new nq();_.bb=ln;_.cb=mn;_.fc=nn;_.gc=on;_.tN=sD+'Panel';_.tI=17;function ti(a){a.f=wq(new oq(),a);}
function ui(a){ti(a);return a;}
function vi(c,a,b){br(a);xq(c.f,a);md(b,a.o);hn(c,a);}
function xi(b,c){var a;if(c.n!==b){return false;}kn(b,c);a=c.o;qe(ke(a),a);Dq(b.f,c);return true;}
function yi(){return Bq(this.f);}
function zi(a){return xi(this,a);}
function si(){}
_=si.prototype=new gn();_.ub=yi;_.mc=zi;_.tN=sD+'ComplexPanel';_.tI=18;function zh(a){ui(a);a.rc(od());Be(a.o,'position','relative');Be(a.o,'overflow','hidden');return a;}
function Ah(a,b){vi(a,b,a.o);}
function Ch(a){Be(a,'left','');Be(a,'top','');Be(a,'position','');}
function Dh(b){var a;a=xi(this,b);if(a){Ch(b.o);}return a;}
function yh(){}
_=yh.prototype=new si();_.mc=Dh;_.tN=sD+'AbsolutePanel';_.tI=19;function Eh(){}
_=Eh.prototype=new Aw();_.tN=sD+'AbstractImagePrototype';_.tI=0;function wk(){wk=mD;fs(),hs;}
function sk(a){fs(),hs;return a;}
function tk(b,a){fs(),hs;Ak(b,a);return b;}
function uk(b,a){if(b.k===null){b.k=oi(new ni());}pA(b.k,a);}
function vk(b,a){if(b.l===null){b.l=hm(new gm());}pA(b.l,a);}
function xk(a){if(a.k!==null){qi(a.k,a);}}
function yk(a){return !ee(a.o,'disabled');}
function zk(b,a){switch(ae(a)){case 1:if(b.k!==null){qi(b.k,b);}break;case 4096:case 2048:break;case 128:case 512:case 256:if(b.l!==null){mm(b.l,b,a);}break;}}
function Ak(b,a){cr(b,a);aq(b,7041);}
function Bk(b,a){we(b.o,'disabled',!a);}
function Ck(a){zk(this,a);}
function Dk(a){Ak(this,a);}
function Ek(a){Bk(this,a);}
function rk(){}
_=rk.prototype=new nq();_.Ab=Ck;_.rc=Dk;_.sc=Ek;_.tN=sD+'FocusWidget';_.tI=20;_.k=null;_.l=null;function ci(){ci=mD;fs(),hs;}
function bi(b,a){fs(),hs;tk(b,a);return b;}
function ai(){}
_=ai.prototype=new rk();_.tN=sD+'ButtonBase';_.tI=21;function ei(a){ui(a);a.e=ud();a.d=rd();md(a.e,a.d);a.rc(a.e);return a;}
function gi(c,b,a){ye(b,'align',a.a);}
function hi(c,b,a){Be(b,'verticalAlign',a.a);}
function di(){}
_=di.prototype=new si();_.tN=sD+'CellPanel';_.tI=22;_.d=null;_.e=null;function jy(d,a,b){var c;while(a.mb()){c=a.xb();if(b===null?c===null:b.eQ(c)){return a;}}return null;}
function ly(a){throw gy(new fy(),'add');}
function my(b){var a;a=jy(this,this.ub(),b);return a!==null;}
function ny(){var a,b,c;c=ex(new dx());a=null;fx(c,'[');b=this.ub();while(b.mb()){if(a!==null){fx(c,a);}else{a=', ';}fx(c,Cx(b.xb()));}fx(c,']');return jx(c);}
function iy(){}
_=iy.prototype=new Aw();_.C=ly;_.E=my;_.tS=ny;_.tN=wD+'AbstractCollection';_.tI=0;function yy(b,a){throw jw(new iw(),'Index: '+a+', Size: '+b.b);}
function zy(a){return qy(new py(),a);}
function Ay(b,a){throw gy(new fy(),'add');}
function By(a){this.B(this.wc(),a);return true;}
function Cy(e){var a,b,c,d,f;if(e===this){return true;}if(!ac(e,18)){return false;}f=Fb(e,18);if(this.wc()!=f.wc()){return false;}c=zy(this);d=f.ub();while(sy(c)){a=ty(c);b=ty(d);if(!(a===null?b===null:a.eQ(b))){return false;}}return true;}
function Dy(){var a,b,c,d;c=1;a=31;b=zy(this);while(sy(b)){d=ty(b);c=31*c+(d===null?0:d.hC());}return c;}
function Ey(){return zy(this);}
function Fy(a){throw gy(new fy(),'remove');}
function oy(){}
_=oy.prototype=new iy();_.B=Ay;_.C=By;_.eQ=Cy;_.hC=Dy;_.ub=Ey;_.lc=Fy;_.tN=wD+'AbstractList';_.tI=23;function nA(a){{qA(a);}}
function oA(a){nA(a);return a;}
function pA(b,a){bB(b.a,b.b++,a);return true;}
function qA(a){a.a=D();a.b=0;}
function sA(b,a){if(a<0||a>=b.b){yy(b,a);}return DA(b.a,a);}
function tA(b,a){return uA(b,a,0);}
function uA(c,b,a){if(a<0){yy(c,a);}for(;a<c.b;++a){if(CA(b,DA(c.a,a))){return a;}}return (-1);}
function vA(a){return a.b==0;}
function wA(c,a){var b;b=sA(c,a);FA(c.a,a,1);--c.b;return b;}
function xA(c,b){var a;a=tA(c,b);if(a==(-1)){return false;}wA(c,a);return true;}
function zA(a,b){if(a<0||a>this.b){yy(this,a);}yA(this.a,a,b);++this.b;}
function AA(a){return pA(this,a);}
function yA(a,b,c){a.splice(b,0,c);}
function BA(a){return tA(this,a)!=(-1);}
function CA(a,b){return a===b||a!==null&&a.eQ(b);}
function EA(a){return sA(this,a);}
function DA(a,b){return a[b];}
function aB(a){return wA(this,a);}
function FA(a,c,b){a.splice(c,b);}
function bB(a,b,c){a[b]=c;}
function cB(){return this.b;}
function mA(){}
_=mA.prototype=new oy();_.B=zA;_.C=AA;_.E=BA;_.kb=EA;_.lc=aB;_.wc=cB;_.tN=wD+'ArrayList';_.tI=24;_.a=null;_.b=0;function ji(a){oA(a);return a;}
function li(d,c){var a,b;for(a=zy(d);sy(a);){b=Fb(ty(a),9);b.Bb(c);}}
function ii(){}
_=ii.prototype=new mA();_.tN=sD+'ChangeListenerCollection';_.tI=25;function oi(a){oA(a);return a;}
function qi(d,c){var a,b;for(a=zy(d);sy(a);){b=Fb(ty(a),10);b.Fb(c);}}
function ni(){}
_=ni.prototype=new mA();_.tN=sD+'ClickListenerCollection';_.tI=26;function jj(){jj=mD;fs(),hs;}
function hj(a,b){fs(),hs;gj(a);dj(a.h,b);return a;}
function gj(a){fs(),hs;bi(a,Br((pk(),qk)));aq(a,6269);ak(a,kj(a,null,'up',0));Dp(a,'gwt-CustomButton');return a;}
function ij(a){if(a.f||a.g){pe(a.o);a.f=false;a.g=false;a.Cb();}}
function kj(d,a,c,b){return Ci(new Bi(),a,d,c,b);}
function lj(a){if(a.a===null){yj(a,a.h);}}
function mj(a){lj(a);return a.a;}
function nj(a){if(a.d===null){zj(a,kj(a,oj(a),'down-disabled',5));}return a.d;}
function oj(a){if(a.c===null){Aj(a,kj(a,a.h,'down',1));}return a.c;}
function pj(a){if(a.e===null){Bj(a,kj(a,oj(a),'down-hovering',3));}return a.e;}
function qj(b,a){switch(a){case 1:return oj(b);case 0:return b.h;case 3:return pj(b);case 2:return sj(b);case 4:return rj(b);case 5:return nj(b);default:throw gw(new fw(),a+' is not a known face id.');}}
function rj(a){if(a.i===null){Fj(a,kj(a,a.h,'up-disabled',4));}return a.i;}
function sj(a){if(a.j===null){bk(a,kj(a,a.h,'up-hovering',2));}return a.j;}
function tj(a){return (1&mj(a).a)>0;}
function uj(a){return (2&mj(a).a)>0;}
function vj(a){xk(a);}
function yj(b,a){if(b.a!==a){if(b.a!==null){yp(b,b.a.b);}b.a=a;wj(b,cj(a));up(b,b.a.b);}}
function xj(c,a){var b;b=qj(c,a);yj(c,b);}
function wj(b,a){if(b.b!==a){if(b.b!==null){qe(b.o,b.b);}b.b=a;md(b.o,b.b);}}
function Cj(b,a){if(a!=b.sb()){dk(b);}}
function zj(b,a){b.d=a;}
function Aj(b,a){b.c=a;}
function Bj(b,a){b.e=a;}
function Dj(b,a){if(a){cs((pk(),qk),b.o);}else{Fr((pk(),qk),b.o);}}
function Ej(b,a){if(a!=uj(b)){ek(b);}}
function Fj(a,b){a.i=b;}
function ak(a,b){a.h=b;}
function bk(a,b){a.j=b;}
function ck(b){var a;a=mj(b).a^4;a&=(-3);xj(b,a);}
function dk(b){var a;a=mj(b).a^1;xj(b,a);}
function ek(b){var a;a=mj(b).a^2;a&=(-5);xj(b,a);}
function fk(){return tj(this);}
function gk(){lj(this);Fq(this);}
function hk(a){var b,c;if(yk(this)==false){return;}c=ae(a);switch(c){case 4:Dj(this,true);this.Db();ue(this.o);this.f=true;be(a);break;case 8:if(this.f){this.f=false;pe(this.o);if(uj(this)){this.Eb();}}break;case 64:if(this.f){be(a);}break;case 32:if(ne(this.o,Ed(a))&& !ne(this.o,Fd(a))){if(this.f){this.Cb();}Ej(this,false);}break;case 16:if(ne(this.o,Ed(a))){Ej(this,true);if(this.f){this.Db();}}break;case 1:return;case 4096:if(this.g){this.g=false;this.Cb();}break;case 8192:if(this.f){this.f=false;this.Cb();}break;}zk(this,a);b=bc(Bd(a));switch(c){case 128:if(b==32){this.g=true;this.Db();}break;case 512:if(this.g&&b==32){this.g=false;this.Eb();}break;case 256:if(b==10||b==13){this.Db();this.Eb();}break;}}
function kk(){vj(this);}
function ik(){}
function jk(){}
function lk(){ar(this);ij(this);}
function mk(a){Cj(this,a);}
function nk(a){if(yk(this)!=a){ck(this);Bk(this,a);if(!a){ij(this);}}}
function Ai(){}
_=Ai.prototype=new ai();_.sb=fk;_.zb=gk;_.Ab=hk;_.Eb=kk;_.Cb=ik;_.Db=jk;_.ac=lk;_.qc=mk;_.sc=nk;_.tN=sD+'CustomButton';_.tI=27;_.a=null;_.b=null;_.c=null;_.d=null;_.e=null;_.f=false;_.g=false;_.h=null;_.i=null;_.j=null;function aj(c,a,b){c.e=b;c.c=a;return c;}
function cj(a){if(a.d===null){if(a.c===null){a.d=od();return a.d;}else{return cj(a.c);}}else{return a.d;}}
function dj(b,a){b.d=a.o;ej(b);}
function ej(a){if(a.e.a!==null&&cj(a.e.a)===cj(a)){wj(a.e,a.d);}}
function fj(){return this.ib();}
function Fi(){}
_=Fi.prototype=new Aw();_.tS=fj;_.tN=sD+'CustomButton$Face';_.tI=0;_.c=null;_.d=null;function Ci(c,a,b,e,d){c.b=e;c.a=d;aj(c,a,b);return c;}
function Ei(){return this.b;}
function Bi(){}
_=Bi.prototype=new Fi();_.ib=Ei;_.tN=sD+'CustomButton$1';_.tI=0;function pk(){pk=mD;qk=(fs(),gs);}
var qk;function fl(){fl=mD;dl(new cl(),'center');gl=dl(new cl(),'left');dl(new cl(),'right');}
var gl;function dl(b,a){b.a=a;return b;}
function cl(){}
_=cl.prototype=new Aw();_.tN=sD+'HasHorizontalAlignment$HorizontalAlignmentConstant';_.tI=0;_.a=null;function ml(){ml=mD;kl(new jl(),'bottom');kl(new jl(),'middle');nl=kl(new jl(),'top');}
var nl;function kl(a,b){a.a=b;return a;}
function jl(){}
_=jl.prototype=new Aw();_.tN=sD+'HasVerticalAlignment$VerticalAlignmentConstant';_.tI=0;_.a=null;function ql(a){a.a=(fl(),gl);a.c=(ml(),nl);}
function rl(a){ei(a);ql(a);a.b=td();md(a.d,a.b);ye(a.e,'cellSpacing','0');ye(a.e,'cellPadding','0');return a;}
function sl(b,c){var a;a=ul(b);md(b.b,a);vi(b,c,a);}
function ul(b){var a;a=sd();gi(b,a,b.a);hi(b,a,b.c);return a;}
function vl(c){var a,b;b=ke(c.o);a=xi(this,c);if(a){qe(this.b,b);}return a;}
function pl(){}
_=pl.prototype=new di();_.mc=vl;_.tN=sD+'HorizontalPanel';_.tI=28;_.b=null;function dm(){dm=mD;aC(new fB());}
function cm(c,e,b,d,f,a){dm();Bl(new Al(),c,e,b,d,f,a);Dp(c,'gwt-Image');return c;}
function em(a){switch(ae(a)){case 1:{break;}case 4:case 8:case 64:case 16:case 32:{break;}case 131072:break;case 32768:{break;}case 65536:{break;}}}
function wl(){}
_=wl.prototype=new nq();_.Ab=em;_.tN=sD+'Image';_.tI=29;function zl(){}
function xl(){}
_=xl.prototype=new Aw();_.eb=zl;_.tN=sD+'Image$1';_.tI=30;function Fl(){}
_=Fl.prototype=new Aw();_.tN=sD+'Image$State';_.tI=0;function Cl(){Cl=mD;El=new mr();}
function Bl(d,b,f,c,e,g,a){Cl();b.rc(or(El,f,c,e,g,a));aq(b,131197);Dl(d,b);return d;}
function Dl(b,a){af(new xl());}
function Al(){}
_=Al.prototype=new Fl();_.tN=sD+'Image$ClippedState';_.tI=0;var El;function hm(a){oA(a);return a;}
function jm(f,e,b,d){var a,c;for(a=zy(f);sy(a);){c=Fb(ty(a),11);c.cc(e,b,d);}}
function km(f,e,b,d){var a,c;for(a=zy(f);sy(a);){c=Fb(ty(a),11);c.dc(e,b,d);}}
function lm(f,e,b,d){var a,c;for(a=zy(f);sy(a);){c=Fb(ty(a),11);c.ec(e,b,d);}}
function mm(d,c,a){var b;b=nm(a);switch(ae(a)){case 128:jm(d,c,bc(Bd(a)),b);break;case 512:lm(d,c,bc(Bd(a)),b);break;case 256:km(d,c,bc(Bd(a)),b);break;}}
function nm(a){return (Dd(a)?1:0)|(Cd(a)?8:0)|(Ad(a)?2:0)|(zd(a)?4:0);}
function gm(){}
_=gm.prototype=new mA();_.tN=sD+'KeyboardListenerCollection';_.tI=31;function Cm(){Cm=mD;fs(),hs;en=new rm();}
function wm(a){Cm();xm(a,false);return a;}
function xm(b,a){Cm();tk(b,pd(a));aq(b,1024);Dp(b,'gwt-ListBox');return b;}
function ym(b,a){if(b.a===null){b.a=ji(new ii());}pA(b.a,a);}
function zm(b,a){an(b,a,(-1));}
function Am(b,a,c){bn(b,a,c,(-1));}
function Bm(b,a){if(a<0||a>=Dm(b)){throw new iw();}}
function Dm(a){return tm(en,a.o);}
function Em(a){return fe(a.o,'selectedIndex');}
function Fm(b,a){Bm(b,a);return um(en,b.o,a);}
function an(c,b,a){bn(c,b,b,a);}
function bn(c,b,d,a){me(c.o,b,d,a);}
function cn(b,a){xe(b.o,'selectedIndex',a);}
function dn(a,b){xe(a.o,'size',b);}
function fn(a){if(ae(a)==1024){if(this.a!==null){li(this.a,this);}}else{zk(this,a);}}
function pm(){}
_=pm.prototype=new rk();_.Ab=fn;_.tN=sD+'ListBox';_.tI=32;_.a=null;var en;function qm(){}
_=qm.prototype=new Aw();_.tN=sD+'ListBox$Impl';_.tI=0;function tm(b,a){return a.children.length;}
function um(c,b,a){return b.children[a].value;}
function rm(){}
_=rm.prototype=new qm();_.tN=sD+'ListBox$ImplSafari';_.tI=0;function sn(){sn=mD;fs(),hs;}
function qn(a){{Dp(a,'gwt-PushButton');}}
function rn(a,b){fs(),hs;hj(a,b);qn(a);return a;}
function vn(){this.qc(false);vj(this);}
function tn(){this.qc(false);}
function un(){this.qc(true);}
function pn(){}
_=pn.prototype=new Ai();_.Eb=vn;_.Cb=tn;_.Db=un;_.tN=sD+'PushButton';_.tI=33;function oo(){oo=mD;fs(),hs;}
function mo(a){a.a=ks(new js());}
function no(a){fs(),hs;sk(a);mo(a);Ak(a,a.a.b);Dp(a,'gwt-RichTextArea');return a;}
function po(a){if(a.a!==null){return a.a;}return null;}
function qo(a){if(a.a!==null&&(ls(),ss)){return a.a;}return null;}
function ro(){return bt(this.a);}
function so(){Fq(this);ct(this.a);}
function to(a){switch(ae(a)){case 4:case 8:case 64:case 16:case 32:break;default:zk(this,a);}}
function uo(){ar(this);ot(this.a);}
function vo(a){jt(this.a,a);}
function wn(){}
_=wn.prototype=new rk();_.gb=ro;_.zb=so;_.Ab=to;_.ac=uo;_.uc=vo;_.tN=sD+'RichTextArea';_.tI=34;function Bn(){Bn=mD;ao=An(new zn(),1);co=An(new zn(),2);En=An(new zn(),3);Dn=An(new zn(),4);Cn=An(new zn(),5);bo=An(new zn(),6);Fn=An(new zn(),7);}
function An(b,a){Bn();b.a=a;return b;}
function eo(){return nw(this.a);}
function zn(){}
_=zn.prototype=new Aw();_.tS=eo;_.tN=sD+'RichTextArea$FontSize';_.tI=0;_.a=0;var Cn,Dn,En,Fn,ao,bo,co;function ho(){ho=mD;io=go(new fo(),'Center');jo=go(new fo(),'Left');ko=go(new fo(),'Right');}
function go(b,a){ho();b.a=a;return b;}
function lo(){return 'Justify '+this.a;}
function fo(){}
_=fo.prototype=new Aw();_.tS=lo;_.tN=sD+'RichTextArea$Justification';_.tI=0;_.a=null;var io,jo,ko;function Co(){Co=mD;ap=aC(new fB());}
function Bo(b,a){Co();zh(b);if(a===null){a=Do();}b.rc(a);b.zb();return b;}
function Eo(c){Co();var a,b;b=Fb(gC(ap,c),12);if(b!==null){return b;}a=null;if(c!==null){if(null===(a=de(c))){return null;}}if(ap.c==0){Fo();}hC(ap,c,b=Bo(new wo(),a));return b;}
function Do(){Co();return $doc.body;}
function Fo(){Co();bg(new xo());}
function wo(){}
_=wo.prototype=new yh();_.tN=sD+'RootPanel';_.tI=35;var ap;function zo(){var a,b;for(b=sz(aA((Co(),ap)));zz(b);){a=Fb(Az(b),12);if(a.m){a.ac();}}}
function Ao(){return null;}
function xo(){}
_=xo.prototype=new Aw();_.hc=zo;_.ic=Ao;_.tN=sD+'RootPanel$1';_.tI=36;function np(){np=mD;fs(),hs;}
function lp(a){{Dp(a,pp);}}
function mp(a,b){fs(),hs;hj(a,b);lp(a);return a;}
function op(b,a){Cj(b,a);}
function qp(){return tj(this);}
function rp(){dk(this);vj(this);}
function sp(a){op(this,a);}
function kp(){}
_=kp.prototype=new Ai();_.sb=qp;_.Eb=rp;_.qc=sp;_.tN=sD+'ToggleButton';_.tI=37;var pp='gwt-ToggleButton';function hq(a){a.a=(fl(),gl);a.b=(ml(),nl);}
function iq(a){ei(a);hq(a);ye(a.e,'cellSpacing','0');ye(a.e,'cellPadding','0');return a;}
function jq(b,d){var a,c;c=td();a=lq(b);md(c,a);md(b.d,c);vi(b,d,a);}
function lq(b){var a;a=sd();gi(b,a,b.a);hi(b,a,b.b);return a;}
function mq(c){var a,b;b=ke(c.o);a=xi(this,c);if(a){qe(this.d,ke(b));}return a;}
function gq(){}
_=gq.prototype=new di();_.mc=mq;_.tN=sD+'VerticalPanel';_.tI=38;function wq(b,a){b.a=yb('[Lcom.google.gwt.user.client.ui.Widget;',[0],[14],[4],null);return b;}
function xq(a,b){Aq(a,b,a.b);}
function zq(b,c){var a;for(a=0;a<b.b;++a){if(b.a[a]===c){return a;}}return (-1);}
function Aq(d,e,a){var b,c;if(a<0||a>d.b){throw new iw();}if(d.b==d.a.a){c=yb('[Lcom.google.gwt.user.client.ui.Widget;',[0],[14],[d.a.a*2],null);for(b=0;b<d.a.a;++b){Ab(c,b,d.a[b]);}d.a=c;}++d.b;for(b=d.b-1;b>a;--b){Ab(d.a,b,d.a[b-1]);}Ab(d.a,a,e);}
function Bq(a){return qq(new pq(),a);}
function Cq(c,b){var a;if(b<0||b>=c.b){throw new iw();}--c.b;for(a=b;a<c.b;++a){Ab(c.a,a,c.a[a+1]);}Ab(c.a,c.b,null);}
function Dq(b,c){var a;a=zq(b,c);if(a==(-1)){throw new iD();}Cq(b,a);}
function oq(){}
_=oq.prototype=new Aw();_.tN=sD+'WidgetCollection';_.tI=0;_.a=null;_.b=0;function qq(b,a){b.b=a;return b;}
function sq(a){return a.a<a.b.b-1;}
function tq(a){if(a.a>=a.b.b){throw new iD();}return a.b.a[++a.a];}
function uq(){return sq(this);}
function vq(){return tq(this);}
function pq(){}
_=pq.prototype=new Aw();_.mb=uq;_.xb=vq;_.tN=sD+'WidgetCollection$WidgetIterator';_.tI=0;_.a=(-1);function or(c,f,b,e,g,a){var d;d=qd();Ae(d,pr(c,f,b,e,g,a));return ie(d);}
function pr(e,g,c,f,h,b){var a,d;d='width: '+h+'px; height: '+b+'px; background: url('+g+') no-repeat '+(-c+'px ')+(-f+'px');a="<img src='"+o()+"clear.cache.gif' style='"+d+"' border='0'>";return a;}
function mr(){}
_=mr.prototype=new Aw();_.tN=tD+'ClippedImageImpl';_.tI=0;function rr(c,e,b,d,f,a){c.d=e;c.b=b;c.c=d;c.e=f;c.a=a;return c;}
function tr(a){return cm(new wl(),a.d,a.b,a.c,a.e,a.a);}
function qr(){}
_=qr.prototype=new Eh();_.tN=tD+'ClippedImagePrototype';_.tI=0;_.a=0;_.b=0;_.c=0;_.d=null;_.e=0;function fs(){fs=mD;gs=Er(new Dr());hs=gs!==null?es(new ur()):gs;}
function es(a){fs();return a;}
function ur(){}
_=ur.prototype=new Aw();_.tN=tD+'FocusImpl';_.tI=0;var gs,hs;function yr(){yr=mD;fs();}
function wr(a){a.a=zr(a);a.b=Ar(a);a.c=bs(a);}
function xr(a){yr();es(a);wr(a);return a;}
function zr(b){return function(a){if(this.parentNode.onblur){this.parentNode.onblur(a);}};}
function Ar(b){return function(a){if(this.parentNode.onfocus){this.parentNode.onfocus(a);}};}
function Br(c){var a=$doc.createElement('div');var b=c.F();b.addEventListener('blur',c.a,false);b.addEventListener('focus',c.b,false);a.addEventListener('mousedown',c.c,false);a.appendChild(b);return a;}
function Cr(){var a=$doc.createElement('input');a.type='text';a.style.width=a.style.height=0;a.style.zIndex= -1;a.style.position='absolute';return a;}
function vr(){}
_=vr.prototype=new ur();_.F=Cr;_.tN=tD+'FocusImplOld';_.tI=0;function as(){as=mD;yr();}
function Er(a){as();xr(a);return a;}
function Fr(b,a){$wnd.setTimeout(function(){a.firstChild.blur();},0);}
function bs(b){return function(){var a=this.firstChild;$wnd.setTimeout(function(){a.focus();},0);};}
function cs(b,a){$wnd.setTimeout(function(){a.firstChild.focus();},0);}
function ds(){var a=$doc.createElement('input');a.type='text';a.style.opacity=0;a.style.zIndex= -1;a.style.height='1px';a.style.width='1px';a.style.overflow='hidden';a.style.position='absolute';return a;}
function Dr(){}
_=Dr.prototype=new vr();_.F=ds;_.tN=tD+'FocusImplSafari';_.tI=0;function Dt(a){a.b=ms(a);return a;}
function Ft(a){a.nb();}
function is(){}
_=is.prototype=new Aw();_.tN=tD+'RichTextAreaImpl';_.tI=0;_.b=null;function As(a){a.a=od();}
function Bs(a){Dt(a);As(a);return a;}
function Ds(a){return $doc.createElement('iframe');}
function Fs(c,a,b){if(dt(c,c.b)){c.tc(true);Es(c,a,b);}}
function Es(c,a,b){c.b.contentWindow.document.execCommand(a,false,b);}
function bt(a){return a.a===null?at(a):je(a.a);}
function at(a){return a.b.contentWindow.document.body.innerHTML;}
function ct(b){var a=b;setTimeout(function(){a.b.contentWindow.document.designMode='On';a.bc();},1);}
function dt(b,a){return a.contentWindow.document.designMode.toUpperCase()=='ON';}
function ft(b,a){if(dt(b,b.b)){b.tc(true);return et(b,a);}else{return false;}}
function et(b,a){return !(!b.b.contentWindow.document.queryCommandState(a));}
function gt(b,a){Fs(b,'FontName',a);}
function ht(b,a){Fs(b,'FontSize',nw(a.a));}
function jt(b,a){if(b.a===null){it(b,a);}else{Ae(b.a,a);}}
function it(b,a){b.b.contentWindow.document.body.innerHTML=a;}
function kt(b,a){if(a===(ho(),io)){Fs(b,'JustifyCenter',null);}else if(a===(ho(),jo)){Fs(b,'JustifyLeft',null);}else if(a===(ho(),ko)){Fs(b,'JustifyRight',null);}}
function lt(a){Fs(a,'Bold','false');}
function mt(a){Fs(a,'Italic','false');}
function nt(a){Fs(a,'Underline','False');}
function ot(b){var a;rs(b);a=bt(b);b.a=od();Ae(b.a,a);}
function pt(a){Fs(this,'CreateLink',a);}
function qt(){var b=this.b;var c=b.contentWindow;b.__gwt_handler=function(a){if(b.__listener){b.__listener.Ab(a);}};b.__gwt_focusHandler=function(a){if(b.__gwt_isFocused){return;}b.__gwt_isFocused=true;b.__gwt_handler(a);};b.__gwt_blurHandler=function(a){if(!b.__gwt_isFocused){return;}b.__gwt_isFocused=false;b.__gwt_handler(a);};c.addEventListener('keydown',b.__gwt_handler,true);c.addEventListener('keyup',b.__gwt_handler,true);c.addEventListener('keypress',b.__gwt_handler,true);c.addEventListener('mousedown',b.__gwt_handler,true);c.addEventListener('mouseup',b.__gwt_handler,true);c.addEventListener('mousemove',b.__gwt_handler,true);c.addEventListener('mouseover',b.__gwt_handler,true);c.addEventListener('mouseout',b.__gwt_handler,true);c.addEventListener('click',b.__gwt_handler,true);c.addEventListener('focus',b.__gwt_focusHandler,true);c.addEventListener('blur',b.__gwt_blurHandler,true);}
function rt(){Fs(this,'InsertHorizontalRule',null);}
function st(a){Fs(this,'InsertImage',a);}
function tt(){Fs(this,'InsertOrderedList',null);}
function ut(){Fs(this,'InsertUnorderedList',null);}
function vt(){return ft(this,'Strikethrough');}
function wt(){Fs(this,'Outdent',null);}
function xt(){Ft(this);if(this.a!==null){it(this,je(this.a));this.a=null;}}
function yt(){Fs(this,'RemoveFormat',null);}
function zt(){Fs(this,'Unlink','false');}
function At(){Fs(this,'Indent',null);}
function Bt(a){if(a){this.b.contentWindow.focus();}else{this.b.contentWindow.blur();}}
function Ct(){Fs(this,'Strikethrough','false');}
function zs(){}
_=zs.prototype=new is();_.ab=pt;_.nb=qt;_.ob=rt;_.pb=st;_.qb=tt;_.rb=ut;_.tb=vt;_.wb=wt;_.bc=xt;_.jc=yt;_.kc=zt;_.oc=At;_.tc=Bt;_.xc=Ct;_.tN=tD+'RichTextAreaImplStandard';_.tI=0;function ls(){ls=mD;xs=zb('[Ljava.lang.String;',0,1,['medium','xx-small','x-small','small','medium','large','x-large','xx-large']);ys=ts();ss=ys>=420;vs=ys<=420;}
function ks(a){ls();Bs(a);return a;}
function ms(a){return Ds(a);}
function ns(a){return !(!a.b.__gwt_isBold);}
function os(a){return !(!a.b.__gwt_isItalic);}
function ps(a){return !(!a.b.__gwt_isUnderlined);}
function qs(c,a){var b;if(vs){b=a.a;if(b>=0&&b<=7){Fs(c,'FontSize',xs[b]);}}else{ht(c,a);}}
function rs(b){var a=b.b;var c=a.contentWindow;c.removeEventListener('keydown',a.__gwt_handler,true);c.removeEventListener('keyup',a.__gwt_handler,true);c.removeEventListener('keypress',a.__gwt_handler,true);c.removeEventListener('mousedown',a.__gwt_handler,true);c.removeEventListener('mouseup',a.__gwt_handler,true);c.removeEventListener('mousemove',a.__gwt_handler,true);c.removeEventListener('mouseover',a.__gwt_handler,true);c.removeEventListener('mouseout',a.__gwt_handler,true);c.removeEventListener('click',a.__gwt_handler,true);a.__gwt_restoreSelection=null;a.__gwt_handler=null;a.onfocus=null;a.onblur=null;}
function ts(){ls();var a=/ AppleWebKit\/([\d]+)/;var b=a.exec(navigator.userAgent);if(b){var c=parseInt(b[1]);if(c){return c;}}return 0;}
function us(){var d=this.b;var e=d.contentWindow;var c=e.document;d.__gwt_selection={'baseOffset':0,'extentOffset':0,'baseNode':null,'extentNode':null};d.__gwt_restoreSelection=function(){var a=d.__gwt_selection;if(e.getSelection){e.getSelection().setBaseAndExtent(a.baseNode,a.baseOffset,a.extentNode,a.extentOffset);}};d.__gwt_handler=function(a){var b=e.getSelection();d.__gwt_selection={'baseOffset':b.baseOffset,'extentOffset':b.extentOffset,'baseNode':b.baseNode,'extentNode':b.extentNode};d.__gwt_isBold=c.queryCommandState('Bold');d.__gwt_isItalic=c.queryCommandState('Italic');d.__gwt_isUnderlined=c.queryCommandState('Underline');if(d.__listener){d.__listener.Ab(a);}};e.addEventListener('keydown',d.__gwt_handler,true);e.addEventListener('keyup',d.__gwt_handler,true);e.addEventListener('keypress',d.__gwt_handler,true);e.addEventListener('mousedown',d.__gwt_handler,true);e.addEventListener('mouseup',d.__gwt_handler,true);e.addEventListener('mousemove',d.__gwt_handler,true);e.addEventListener('mouseover',d.__gwt_handler,true);e.addEventListener('mouseout',d.__gwt_handler,true);e.addEventListener('click',d.__gwt_handler,true);d.onfocus=function(a){if(d.__listener){d.__listener.Ab(a);}};d.onblur=function(a){if(d.__listener){d.__listener.Ab(a);}};}
function ws(b){var a=this.b;if(b){a.focus();if(a.__gwt_restoreSelection){a.__gwt_restoreSelection();}}else{a.blur();}}
function js(){}
_=js.prototype=new zs();_.nb=us;_.tc=ws;_.tN=tD+'RichTextAreaImplSafari';_.tI=0;var ss,vs,xs,ys;function ku(a){a.f=zb('[Lcom.google.gwt.user.client.ui.RichTextArea$FontSize;',0,0,[(Bn(),ao),(Bn(),co),(Bn(),En),(Bn(),Dn),(Bn(),Cn),(Bn(),bo),(Bn(),Fn)]);}
function lu(a){ku(a);return a;}
function nu(b){var a;a=wm(new pm());ym(a,b.q);dn(a,1);Am(a,jb(b.o,'FONT'),'');zm(a,'Andale Mono');zm(a,'Arial Black');zm(a,'Comics Sans');zm(a,'Courier');zm(a,'Futura');zm(a,'Georgia');zm(a,'Gill Sans');zm(a,'Helvetica');zm(a,'Impact');zm(a,'Lucida');zm(a,'Times New Roman');zm(a,'Trebuchet');zm(a,'Verdana');return a;}
function ou(b){var a;a=wm(new pm());ym(a,b.q);dn(a,1);zm(a,jb(b.o,'SIZE'));zm(a,jb(b.o,'XXSMALL'));zm(a,jb(b.o,'XSMALL'));zm(a,jb(b.o,'SMALL'));zm(a,jb(b.o,'MEDIUM'));zm(a,jb(b.o,'LARGE'));zm(a,jb(b.o,'XLARGE'));zm(a,jb(b.o,'XXLARGE'));return a;}
function pu(c,a,d){var b;b=rn(new pn(),tr(a));uk(b,c.q);Ep(b,jb(c.o,d));return b;}
function qu(c){var a,b,d;c.c=no(new wn());Cp(c.c,'30em');Fp(c.c,'100%');c.v=iq(new gq());b=zu(new yu());d=rl(new pl());a=rl(new pl());jq(c.v,d);jq(c.v,a);c.a=po(c.c);c.d=qo(c.c);if(c.a!==null){sl(d,c.b=ru(c,(Au(),Cu),'TOGGLE_BOLD'));sl(d,c.k=ru(c,(Au(),cv),'TOGGLE_ITALIC'));sl(d,c.y=ru(c,(Au(),mv),'TOGGLE_UNDERLINE'));sl(d,c.m=pu(c,(Au(),ev),'JUSTIFY_LEFT'));sl(d,c.l=pu(c,(Au(),dv),'JUSTIFY_CENTER'));sl(d,c.n=pu(c,(Au(),fv),'JUSTIFY_RIGHT'));sl(a,c.g=nu(c));sl(a,c.e=ou(c));vk(c.c,c.q);uk(c.c,c.q);}if(c.d!==null){sl(d,c.u=ru(c,(Au(),kv),'TOGGLE_STRIKETHROUGH'));sl(d,c.j=pu(c,(Au(),av),'INDENT_LEFT'));sl(d,c.t=pu(c,(Au(),hv),'INDENT_RIGHT'));sl(d,c.h=pu(c,(Au(),Fu),'INSERT_HR'));sl(d,c.s=pu(c,(Au(),gv),'INSERT_OL'));sl(d,c.w=pu(c,(Au(),lv),'INSERT_UL'));sl(d,c.i=pu(c,(Au(),bv),'INSERT_IMAGE'));sl(d,c.r=pu(c,(Au(),Eu),'CREATE_NOTELINK'));sl(d,c.p=pu(c,(Au(),Du),'CREATE_LINK'));sl(d,c.A=pu(c,(Au(),jv),'REMOVE_LINK'));sl(d,c.z=pu(c,(Au(),iv),'REMOVE_FORMATTING'));}}
function ru(c,a,d){var b;b=mp(new kp(),tr(a));uk(b,c.q);Ep(b,jb(c.o,d));return b;}
function su(g,f){var b=g.c;var h=$wnd.notes;var c=g.d;var d=g.g;var e=g.e;h.editorGetText=function(){return b.gb();};h.editorSetText=function(a){b.uc(a);f.yc();};h.editorInsertImage=function(a){c.pb(a);};h.editorCreateLink=function(a){c.ab(a);};h.editorDisableToolbar=function(){d.sc(false);e.sc(false);};h.editorEnableToolbar=function(){d.sc(true);e.sc(true);};h.editorSetText(h.savedContent);h.componentIsReady(0);}
function tu(a){$wnd.notes.widgetInsertImage();}
function uu(a){$wnd.notes.widgetInsertLink();}
function vu(a){$wnd.notes.widgetInsertNoteLink();}
function wu(a){a.o=mb('notesStrings');a.q=cu(new bu(),a);qu(a);Ah(Eo('noteEditorToolbar'),a.v);Ah(Eo('noteEditor'),a.c);su(a,a);}
function xu(a){if(a.a!==null){op(a.b,ns(a.a));op(a.k,os(a.a));op(a.y,ps(a.a));}if(a.d!==null){op(a.u,a.d.tb());}}
function nv(){xu(this);}
function au(){}
_=au.prototype=new Aw();_.yc=nv;_.tN=uD+'NoteEditor';_.tI=0;_.a=null;_.b=null;_.c=null;_.d=null;_.e=null;_.g=null;_.h=null;_.i=null;_.j=null;_.k=null;_.l=null;_.m=null;_.n=null;_.o=null;_.p=null;_.q=null;_.r=null;_.s=null;_.t=null;_.u=null;_.v=null;_.w=null;_.y=null;_.z=null;_.A=null;function cu(b,a){b.a=a;return b;}
function eu(a){if(a===this.a.g){gt(this.a.a,Fm(this.a.g,Em(this.a.g)));cn(this.a.g,0);}else if(a===this.a.e){qs(this.a.a,this.a.f[Em(this.a.e)-1]);cn(this.a.e,0);}else{return;}}
function fu(a){if(a===this.a.b){lt(this.a.a);}else if(a===this.a.k){mt(this.a.a);}else if(a===this.a.y){nt(this.a.a);}else if(a===this.a.u){this.a.d.xc();}else if(a===this.a.j){this.a.d.oc();}else if(a===this.a.t){this.a.d.wb();}else if(a===this.a.m){kt(this.a.a,(ho(),jo));}else if(a===this.a.l){kt(this.a.a,(ho(),io));}else if(a===this.a.n){kt(this.a.a,(ho(),ko));}else if(a===this.a.i){tu(this.a);return;}else if(a===this.a.p){uu(this.a);return;}else if(a===this.a.r){vu(this.a);return;}else if(a===this.a.A){this.a.d.kc();}else if(a===this.a.h){this.a.d.ob();}else if(a===this.a.s){this.a.d.qb();}else if(a===this.a.w){this.a.d.rb();}else if(a===this.a.z){this.a.d.jc();}else if(a===this.a.c){xu(this.a);}}
function gu(c,a,b){}
function hu(c,a,b){}
function iu(c,a,b){if(c===this.a.c){xu(this.a);}}
function bu(){}
_=bu.prototype=new Aw();_.Bb=eu;_.Fb=fu;_.cc=gu;_.dc=hu;_.ec=iu;_.tN=uD+'NoteEditor$EventListener';_.tI=39;function Au(){Au=mD;Bu=o()+'B73D14400050EDAE39B4CF65DFB55829.cache.png';Cu=rr(new qr(),Bu,0,0,20,20);Du=rr(new qr(),Bu,20,0,20,20);Eu=rr(new qr(),Bu,40,0,20,20);Fu=rr(new qr(),Bu,60,0,20,20);av=rr(new qr(),Bu,80,0,20,20);bv=rr(new qr(),Bu,100,0,20,20);cv=rr(new qr(),Bu,120,0,20,20);dv=rr(new qr(),Bu,140,0,20,20);ev=rr(new qr(),Bu,160,0,20,20);fv=rr(new qr(),Bu,180,0,20,20);gv=rr(new qr(),Bu,200,0,20,20);hv=rr(new qr(),Bu,220,0,20,20);iv=rr(new qr(),Bu,240,0,20,20);jv=rr(new qr(),Bu,260,0,20,20);kv=rr(new qr(),Bu,280,0,20,20);lv=rr(new qr(),Bu,300,0,20,20);mv=rr(new qr(),Bu,320,0,20,20);}
function zu(a){Au();return a;}
function yu(){}
_=yu.prototype=new Aw();_.tN=uD+'NoteEditor_Images_generatedBundle';_.tI=0;var Bu,Cu,Du,Eu,Fu,av,bv,cv,dv,ev,fv,gv,hv,iv,jv,kv,lv,mv;function pv(){}
_=pv.prototype=new Fw();_.tN=vD+'ArrayStoreException';_.tI=40;function tv(){tv=mD;uv=sv(new rv(),false);vv=sv(new rv(),true);}
function sv(a,b){tv();a.a=b;return a;}
function wv(a){return ac(a,17)&&Fb(a,17).a==this.a;}
function xv(){var a,b;b=1231;a=1237;return this.a?1231:1237;}
function yv(){return this.a?'true':'false';}
function zv(a){tv();return a?vv:uv;}
function rv(){}
_=rv.prototype=new Aw();_.eQ=wv;_.hC=xv;_.tS=yv;_.tN=vD+'Boolean';_.tI=41;_.a=false;var uv,vv;function Bv(){}
_=Bv.prototype=new Fw();_.tN=vD+'ClassCastException';_.tI=42;function dw(b,a){ax(b,a);return b;}
function cw(){}
_=cw.prototype=new Fw();_.tN=vD+'IllegalArgumentException';_.tI=43;function gw(b,a){ax(b,a);return b;}
function fw(){}
_=fw.prototype=new Fw();_.tN=vD+'IllegalStateException';_.tI=44;function jw(b,a){ax(b,a);return b;}
function iw(){}
_=iw.prototype=new Fw();_.tN=vD+'IndexOutOfBoundsException';_.tI=45;function xw(){xw=mD;{zw();}}
function zw(){xw();yw=/^[+-]?\d*\.?\d*(e[+-]?\d+)?$/i;}
var yw=null;function mw(){mw=mD;xw();}
function nw(a){mw();return Bx(a);}
function qw(a){return a<0?-a:a;}
function rw(){}
_=rw.prototype=new Fw();_.tN=vD+'NegativeArraySizeException';_.tI=46;function uw(b,a){ax(b,a);return b;}
function tw(){}
_=tw.prototype=new Fw();_.tN=vD+'NullPointerException';_.tI=47;function mx(b,a){return b.charCodeAt(a);}
function ox(b,a){if(!ac(a,1))return false;return wx(b,a);}
function px(b,a){return b.indexOf(String.fromCharCode(a));}
function qx(b,a){return b.indexOf(a);}
function rx(c,b,a){return c.indexOf(b,a);}
function sx(a){return a.length;}
function tx(b,a){return b.substr(a,b.length-a);}
function ux(c,a,b){return c.substr(a,b-a);}
function vx(c){var a=c.replace(/^(\s*)/,'');var b=a.replace(/\s*$/,'');return b;}
function wx(a,b){return String(a)==b;}
function xx(a){return ox(this,a);}
function zx(){var a=yx;if(!a){a=yx={};}var e=':'+this;var b=a[e];if(b==null){b=0;var f=this.length;var d=f<64?1:f/32|0;for(var c=0;c<f;c+=d){b<<=1;b+=this.charCodeAt(c);}b|=0;a[e]=b;}return b;}
function Ax(){return this;}
function Bx(a){return ''+a;}
function Cx(a){return a!==null?a.tS():'null';}
_=String.prototype;_.eQ=xx;_.hC=zx;_.tS=Ax;_.tN=vD+'String';_.tI=2;var yx=null;function ex(a){gx(a);return a;}
function fx(c,d){if(d===null){d='null';}var a=c.js.length-1;var b=c.js[a].length;if(c.length>b*b){c.js[a]=c.js[a]+d;}else{c.js.push(d);}c.length+=d.length;return c;}
function gx(a){hx(a,'');}
function hx(b,a){b.js=[a];b.length=a.length;}
function jx(a){a.yb();return a.js[0];}
function kx(){if(this.js.length>1){this.js=[this.js.join('')];this.length=this.js[0].length;}}
function lx(){return jx(this);}
function dx(){}
_=dx.prototype=new Aw();_.yb=kx;_.tS=lx;_.tN=vD+'StringBuffer';_.tI=0;function Fx(){return new Date().getTime();}
function ay(a){return u(a);}
function gy(b,a){ax(b,a);return b;}
function fy(){}
_=fy.prototype=new Fw();_.tN=vD+'UnsupportedOperationException';_.tI=48;function qy(b,a){b.c=a;return b;}
function sy(a){return a.a<a.c.wc();}
function ty(a){if(!sy(a)){throw new iD();}return a.c.kb(a.b=a.a++);}
function uy(a){if(a.b<0){throw new fw();}a.c.lc(a.b);a.a=a.b;a.b=(-1);}
function vy(){return sy(this);}
function wy(){return ty(this);}
function py(){}
_=py.prototype=new Aw();_.mb=vy;_.xb=wy;_.tN=wD+'AbstractList$IteratorImpl';_.tI=0;_.a=0;_.b=(-1);function Ez(f,d,e){var a,b,c;for(b=BB(f.db());uB(b);){a=vB(b);c=a.hb();if(d===null?c===null:d.eQ(c)){if(e){wB(b);}return a;}}return null;}
function Fz(b){var a;a=b.db();return cz(new bz(),b,a);}
function aA(b){var a;a=fC(b);return qz(new pz(),b,a);}
function bA(a){return Ez(this,a,false)!==null;}
function cA(d){var a,b,c,e,f,g,h;if(d===this){return true;}if(!ac(d,19)){return false;}f=Fb(d,19);c=Fz(this);e=f.vb();if(!jA(c,e)){return false;}for(a=ez(c);lz(a);){b=mz(a);h=this.lb(b);g=f.lb(b);if(h===null?g!==null:!h.eQ(g)){return false;}}return true;}
function dA(b){var a;a=Ez(this,b,false);return a===null?null:a.jb();}
function eA(){var a,b,c;b=0;for(c=BB(this.db());uB(c);){a=vB(c);b+=a.hC();}return b;}
function fA(){return Fz(this);}
function gA(){var a,b,c,d;d='{';a=false;for(c=BB(this.db());uB(c);){b=vB(c);if(a){d+=', ';}else{a=true;}d+=Cx(b.hb());d+='=';d+=Cx(b.jb());}return d+'}';}
function az(){}
_=az.prototype=new Aw();_.D=bA;_.eQ=cA;_.lb=dA;_.hC=eA;_.vb=fA;_.tS=gA;_.tN=wD+'AbstractMap';_.tI=49;function jA(e,b){var a,c,d;if(b===e){return true;}if(!ac(b,20)){return false;}c=Fb(b,20);if(c.wc()!=e.wc()){return false;}for(a=c.ub();a.mb();){d=a.xb();if(!e.E(d)){return false;}}return true;}
function kA(a){return jA(this,a);}
function lA(){var a,b,c;a=0;for(b=this.ub();b.mb();){c=b.xb();if(c!==null){a+=c.hC();}}return a;}
function hA(){}
_=hA.prototype=new iy();_.eQ=kA;_.hC=lA;_.tN=wD+'AbstractSet';_.tI=50;function cz(b,a,c){b.a=a;b.b=c;return b;}
function ez(b){var a;a=BB(b.b);return jz(new iz(),b,a);}
function fz(a){return this.a.D(a);}
function gz(){return ez(this);}
function hz(){return this.b.a.c;}
function bz(){}
_=bz.prototype=new hA();_.E=fz;_.ub=gz;_.wc=hz;_.tN=wD+'AbstractMap$1';_.tI=51;function jz(b,a,c){b.a=c;return b;}
function lz(a){return uB(a.a);}
function mz(b){var a;a=vB(b.a);return a.hb();}
function nz(){return lz(this);}
function oz(){return mz(this);}
function iz(){}
_=iz.prototype=new Aw();_.mb=nz;_.xb=oz;_.tN=wD+'AbstractMap$2';_.tI=0;function qz(b,a,c){b.a=a;b.b=c;return b;}
function sz(b){var a;a=BB(b.b);return xz(new wz(),b,a);}
function tz(a){return eC(this.a,a);}
function uz(){return sz(this);}
function vz(){return this.b.a.c;}
function pz(){}
_=pz.prototype=new iy();_.E=tz;_.ub=uz;_.wc=vz;_.tN=wD+'AbstractMap$3';_.tI=0;function xz(b,a,c){b.a=c;return b;}
function zz(a){return uB(a.a);}
function Az(a){var b;b=vB(a.a).jb();return b;}
function Bz(){return zz(this);}
function Cz(){return Az(this);}
function wz(){}
_=wz.prototype=new Aw();_.mb=Bz;_.xb=Cz;_.tN=wD+'AbstractMap$4';_.tI=0;function cC(){cC=mD;jC=pC();}
function FB(a){{bC(a);}}
function aC(a){cC();FB(a);return a;}
function bC(a){a.a=D();a.d=E();a.b=fc(jC,z);a.c=0;}
function dC(b,a){if(ac(a,1)){return tC(b.d,Fb(a,1))!==jC;}else if(a===null){return b.b!==jC;}else{return sC(b.a,a,a.hC())!==jC;}}
function eC(a,b){if(a.b!==jC&&rC(a.b,b)){return true;}else if(oC(a.d,b)){return true;}else if(mC(a.a,b)){return true;}return false;}
function fC(a){return zB(new qB(),a);}
function gC(c,a){var b;if(ac(a,1)){b=tC(c.d,Fb(a,1));}else if(a===null){b=c.b;}else{b=sC(c.a,a,a.hC());}return b===jC?null:b;}
function hC(c,a,d){var b;if(ac(a,1)){b=wC(c.d,Fb(a,1),d);}else if(a===null){b=c.b;c.b=d;}else{b=vC(c.a,a,d,a.hC());}if(b===jC){++c.c;return null;}else{return b;}}
function iC(c,a){var b;if(ac(a,1)){b=yC(c.d,Fb(a,1));}else if(a===null){b=c.b;c.b=fc(jC,z);}else{b=xC(c.a,a,a.hC());}if(b===jC){return null;}else{--c.c;return b;}}
function kC(e,c){cC();for(var d in e){if(d==parseInt(d)){var a=e[d];for(var f=0,b=a.length;f<b;++f){c.C(a[f]);}}}}
function lC(d,a){cC();for(var c in d){if(c.charCodeAt(0)==58){var e=d[c];var b=jB(c.substring(1),e);a.C(b);}}}
function mC(f,h){cC();for(var e in f){if(e==parseInt(e)){var a=f[e];for(var g=0,b=a.length;g<b;++g){var c=a[g];var d=c.jb();if(rC(h,d)){return true;}}}}return false;}
function nC(a){return dC(this,a);}
function oC(c,d){cC();for(var b in c){if(b.charCodeAt(0)==58){var a=c[b];if(rC(d,a)){return true;}}}return false;}
function pC(){cC();}
function qC(){return fC(this);}
function rC(a,b){cC();if(a===b){return true;}else if(a===null){return false;}else{return a.eQ(b);}}
function uC(a){return gC(this,a);}
function sC(f,h,e){cC();var a=f[e];if(a){for(var g=0,b=a.length;g<b;++g){var c=a[g];var d=c.hb();if(rC(h,d)){return c.jb();}}}}
function tC(b,a){cC();return b[':'+a];}
function vC(f,h,j,e){cC();var a=f[e];if(a){for(var g=0,b=a.length;g<b;++g){var c=a[g];var d=c.hb();if(rC(h,d)){var i=c.jb();c.vc(j);return i;}}}else{a=f[e]=[];}var c=jB(h,j);a.push(c);}
function wC(c,a,d){cC();a=':'+a;var b=c[a];c[a]=d;return b;}
function xC(f,h,e){cC();var a=f[e];if(a){for(var g=0,b=a.length;g<b;++g){var c=a[g];var d=c.hb();if(rC(h,d)){if(a.length==1){delete f[e];}else{a.splice(g,1);}return c.jb();}}}}
function yC(c,a){cC();a=':'+a;var b=c[a];delete c[a];return b;}
function fB(){}
_=fB.prototype=new az();_.D=nC;_.db=qC;_.lb=uC;_.tN=wD+'HashMap';_.tI=52;_.a=null;_.b=null;_.c=0;_.d=null;var jC;function hB(b,a,c){b.a=a;b.b=c;return b;}
function jB(a,b){return hB(new gB(),a,b);}
function kB(b){var a;if(ac(b,21)){a=Fb(b,21);if(rC(this.a,a.hb())&&rC(this.b,a.jb())){return true;}}return false;}
function lB(){return this.a;}
function mB(){return this.b;}
function nB(){var a,b;a=0;b=0;if(this.a!==null){a=this.a.hC();}if(this.b!==null){b=this.b.hC();}return a^b;}
function oB(a){var b;b=this.b;this.b=a;return b;}
function pB(){return this.a+'='+this.b;}
function gB(){}
_=gB.prototype=new Aw();_.eQ=kB;_.hb=lB;_.jb=mB;_.hC=nB;_.vc=oB;_.tS=pB;_.tN=wD+'HashMap$EntryImpl';_.tI=53;_.a=null;_.b=null;function zB(b,a){b.a=a;return b;}
function BB(a){return sB(new rB(),a.a);}
function CB(c){var a,b,d;if(ac(c,21)){a=Fb(c,21);b=a.hb();if(dC(this.a,b)){d=gC(this.a,b);return rC(a.jb(),d);}}return false;}
function DB(){return BB(this);}
function EB(){return this.a.c;}
function qB(){}
_=qB.prototype=new hA();_.E=CB;_.ub=DB;_.wc=EB;_.tN=wD+'HashMap$EntrySet';_.tI=54;function sB(c,b){var a;c.c=b;a=oA(new mA());if(c.c.b!==(cC(),jC)){pA(a,hB(new gB(),null,c.c.b));}lC(c.c.d,a);kC(c.c.a,a);c.a=zy(a);return c;}
function uB(a){return sy(a.a);}
function vB(a){return a.b=Fb(ty(a.a),21);}
function wB(a){if(a.b===null){throw gw(new fw(),'Must call next() before remove().');}else{uy(a.a);iC(a.c,a.b.hb());a.b=null;}}
function xB(){return uB(this);}
function yB(){return vB(this);}
function rB(){}
_=rB.prototype=new Aw();_.mb=xB;_.xb=yB;_.tN=wD+'HashMap$EntrySetIterator';_.tI=0;_.a=null;_.b=null;function AC(a){a.a=aC(new fB());return a;}
function CC(a){var b;b=hC(this.a,a,zv(true));return b===null;}
function DC(a){return dC(this.a,a);}
function EC(){return ez(Fz(this.a));}
function FC(){return this.a.c;}
function aD(){return Fz(this.a).tS();}
function zC(){}
_=zC.prototype=new hA();_.C=CC;_.E=DC;_.ub=EC;_.wc=FC;_.tS=aD;_.tN=wD+'HashSet';_.tI=55;_.a=null;function gD(d,c,a,b){ax(d,c);return d;}
function fD(){}
_=fD.prototype=new Fw();_.tN=wD+'MissingResourceException';_.tI=56;function iD(){}
_=iD.prototype=new Fw();_.tN=wD+'NoSuchElementException';_.tI=57;function ov(){wu(lu(new au()));}
function gwtOnLoad(b,d,c){$moduleName=d;$moduleBase=c;if(b)try{ov();}catch(a){b(d);}else{ov();}}
var ec=[{},{},{1:1},{4:1},{4:1},{4:1},{4:1},{2:1},{3:1},{4:1},{7:1},{7:1},{7:1},{2:1,6:1},{2:1},{8:1},{13:1,14:1,15:1,16:1},{13:1,14:1,15:1,16:1},{13:1,14:1,15:1,16:1},{13:1,14:1,15:1,16:1},{13:1,14:1,15:1,16:1},{13:1,14:1,15:1,16:1},{13:1,14:1,15:1,16:1},{18:1},{18:1},{18:1},{18:1},{13:1,14:1,15:1,16:1},{13:1,14:1,15:1,16:1},{13:1,14:1,15:1,16:1},{5:1},{18:1},{13:1,14:1,15:1,16:1},{13:1,14:1,15:1,16:1},{13:1,14:1,15:1,16:1},{12:1,13:1,14:1,15:1,16:1},{8:1},{13:1,14:1,15:1,16:1},{13:1,14:1,15:1,16:1},{9:1,10:1,11:1},{4:1},{17:1},{4:1},{4:1},{4:1},{4:1},{4:1},{4:1},{4:1},{19:1},{20:1},{20:1},{19:1},{21:1},{20:1},{20:1},{4:1},{4:1}];if (com_ning_NoteEditor) {  var __gwt_initHandlers = com_ning_NoteEditor.__gwt_initHandlers;  com_ning_NoteEditor.onScriptLoad(gwtOnLoad);}})();