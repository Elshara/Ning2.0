/*
	Copyright (c) 2004-2006, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/community/licensing.shtml
*/

/*
	This is a compiled version of Dojo, built for deployment and not for
	development. To get an editable version, please visit:

		http://dojotoolkit.org

	for documentation and information on getting the source.
*/

if(typeof dojo=="undefined"){
var dj_global=this;
function dj_undef(_1,_2){
if(_2==null){
_2=dj_global;
}
return (typeof _2[_1]=="undefined");
}
if(dj_undef("djConfig")){
var djConfig={};
}
if(dj_undef("dojo")){
var dojo={};
}
dojo.version={major:0,minor:3,patch:1,flag:"-ning",revision:Number("$Rev: 4342 $".match(/[0-9]+/)[0]),toString:function(){
with(dojo.version){
return major+"."+minor+"."+patch+flag+" ("+revision+")";
}
}};
dojo.evalProp=function(_3,_4,_5){
return (_4&&!dj_undef(_3,_4)?_4[_3]:(_5?(_4[_3]={}):undefined));
};
dojo.parseObjPath=function(_6,_7,_8){
var _9=(_7!=null?_7:dj_global);
var _a=_6.split(".");
var _b=_a.pop();
for(var i=0,l=_a.length;i<l&&_9;i++){
_9=dojo.evalProp(_a[i],_9,_8);
}
return {obj:_9,prop:_b};
};
dojo.evalObjPath=function(_d,_e){
if(typeof _d!="string"){
return dj_global;
}
if(_d.indexOf(".")==-1){
return dojo.evalProp(_d,dj_global,_e);
}
var _f=dojo.parseObjPath(_d,dj_global,_e);
if(_f){
return dojo.evalProp(_f.prop,_f.obj,_e);
}
return null;
};
dojo.errorToString=function(_10){
if(!dj_undef("message",_10)){
return _10.message;
}else{
if(!dj_undef("description",_10)){
return _10.description;
}else{
return _10;
}
}
};
dojo.raise=function(_11,_12){
if(_12){
_11=_11+": "+dojo.errorToString(_12);
}
try{
dojo.hostenv.println("FATAL: "+_11);
}
catch(e){
}
throw Error(_11);
};
dojo.debug=function(){
};
dojo.debugShallow=function(obj){
};
dojo.profile={start:function(){
},end:function(){
},stop:function(){
},dump:function(){
}};
function dj_eval(_14){
return dj_global.eval?dj_global.eval(_14):eval(_14);
}
dojo.unimplemented=function(_15,_16){
var _17="'"+_15+"' not implemented";
if(_16!=null){
_17+=" "+_16;
}
dojo.raise(_17);
};
dojo.deprecated=function(_18,_19,_1a){
var _1b="DEPRECATED: "+_18;
if(_19){
_1b+=" "+_19;
}
if(_1a){
_1b+=" -- will be removed in version: "+_1a;
}
dojo.debug(_1b);
};
dojo.inherits=function(_1c,_1d){
if(typeof _1d!="function"){
dojo.raise("dojo.inherits: superclass argument ["+_1d+"] must be a function (subclass: ["+_1c+"']");
}
_1c.prototype=new _1d();
_1c.prototype.constructor=_1c;
_1c.superclass=_1d.prototype;
_1c["super"]=_1d.prototype;
};
dojo.render=(function(){
function vscaffold(_1e,_1f){
var tmp={capable:false,support:{builtin:false,plugin:false},prefixes:_1e};
for(var _21 in _1f){
tmp[_21]=false;
}
return tmp;
}
return {name:"",ver:dojo.version,os:{win:false,linux:false,osx:false},html:vscaffold(["html"],["ie","opera","khtml","safari","moz"]),svg:vscaffold(["svg"],["corel","adobe","batik"]),vml:vscaffold(["vml"],["ie"]),swf:vscaffold(["Swf","Flash","Mm"],["mm"]),swt:vscaffold(["Swt"],["ibm"])};
})();
dojo.hostenv=(function(){
var _22={isDebug:false,allowQueryConfig:false,baseScriptUri:"",baseRelativePath:"",libraryScriptUri:"",iePreventClobber:false,ieClobberMinimal:true,preventBackButtonFix:true,searchIds:[],parseWidgets:true};
if(typeof djConfig=="undefined"){
djConfig=_22;
}else{
for(var _23 in _22){
if(typeof djConfig[_23]=="undefined"){
djConfig[_23]=_22[_23];
}
}
}
return {name_:"(unset)",version_:"(unset)",getName:function(){
return this.name_;
},getVersion:function(){
return this.version_;
},getText:function(uri){
dojo.unimplemented("getText","uri="+uri);
}};
})();
dojo.hostenv.getBaseScriptUri=function(){
if(djConfig.baseScriptUri.length){
return djConfig.baseScriptUri;
}
var uri=new String(djConfig.libraryScriptUri||djConfig.baseRelativePath);
if(!uri){
dojo.raise("Nothing returned by getLibraryScriptUri(): "+uri);
}
var _26=uri.lastIndexOf("/");
djConfig.baseScriptUri=djConfig.baseRelativePath;
return djConfig.baseScriptUri;
};
(function(){
var _27={pkgFileName:"__package__",loading_modules_:{},loaded_modules_:{},addedToLoadingCount:[],removedFromLoadingCount:[],inFlightCount:0,modulePrefixes_:{dojo:{name:"dojo",value:"src"}},setModulePrefix:function(_28,_29){
this.modulePrefixes_[_28]={name:_28,value:_29};
},getModulePrefix:function(_2a){
var mp=this.modulePrefixes_;
if((mp[_2a])&&(mp[_2a]["name"])){
return mp[_2a].value;
}
return _2a;
},getTextStack:[],loadUriStack:[],loadedUris:[],post_load_:false,modulesLoadedListeners:[],unloadListeners:[],loadNotifying:false};
for(var _2c in _27){
dojo.hostenv[_2c]=_27[_2c];
}
})();
dojo.hostenv.loadPath=function(_2d,_2e,cb){
var uri;
if((_2d.charAt(0)=="/")||(_2d.match(/^\w+:/))){
uri=_2d;
}else{
uri=this.getBaseScriptUri()+_2d;
}
if(djConfig.cacheBust&&dojo.render.html.capable){
uri+="?"+String(djConfig.cacheBust).replace(/\W+/g,"");
}
try{
return ((!_2e)?this.loadUri(uri,cb):this.loadUriAndCheck(uri,_2e,cb));
}
catch(e){
dojo.debug(e);
return false;
}
};
dojo.hostenv.loadUri=function(uri,cb){
if(this.loadedUris[uri]){
return 1;
}
var _33=this.getText(uri,null,true);
if(_33==null){
return 0;
}
this.loadedUris[uri]=true;
if(cb){
_33="("+_33+")";
}
var _34=dj_eval(_33);
if(cb){
cb(_34);
}
return 1;
};
dojo.hostenv.loadUriAndCheck=function(uri,_36,cb){
var ok=true;
try{
ok=this.loadUri(uri,cb);
}
catch(e){
dojo.debug("failed loading ",uri," with error: ",e);
}
return ((ok)&&(this.findModule(_36,false)))?true:false;
};
dojo.loaded=function(){
};
dojo.unloaded=function(){
};
dojo.hostenv.loaded=function(){
this.loadNotifying=true;
this.post_load_=true;
var mll=this.modulesLoadedListeners;
for(var x=0;x<mll.length;x++){
mll[x]();
}
this.modulesLoadedListeners=[];
this.loadNotifying=false;
dojo.loaded();
};
dojo.hostenv.unloaded=function(){
var mll=this.unloadListeners;
while(mll.length){
(mll.pop())();
}
dojo.unloaded();
};
dojo.addOnLoad=function(obj,_3d){
var dh=dojo.hostenv;
if(arguments.length==1){
dh.modulesLoadedListeners.push(obj);
}else{
if(arguments.length>1){
dh.modulesLoadedListeners.push(function(){
obj[_3d]();
});
}
}
if(dh.post_load_&&dh.inFlightCount==0&&!dh.loadNotifying){
dh.callLoaded();
}
};
dojo.addOnUnload=function(obj,_40){
var dh=dojo.hostenv;
if(arguments.length==1){
dh.unloadListeners.push(obj);
}else{
if(arguments.length>1){
dh.unloadListeners.push(function(){
obj[_40]();
});
}
}
};
dojo.hostenv.modulesLoaded=function(){
if(this.post_load_){
return;
}
if((this.loadUriStack.length==0)&&(this.getTextStack.length==0)){
if(this.inFlightCount>0){
dojo.debug("files still in flight!");
return;
}
dojo.hostenv.callLoaded();
}
};
dojo.hostenv.callLoaded=function(){
if(typeof setTimeout=="object"){
setTimeout("dojo.hostenv.loaded();",0);
}else{
dojo.hostenv.loaded();
}
};
dojo.hostenv.getModuleSymbols=function(_42){
var _43=_42.split(".");
for(var i=_43.length-1;i>0;i--){
var _45=_43.slice(0,i).join(".");
var _46=this.getModulePrefix(_45);
if(_46!=_45){
_43.splice(0,i,_46);
break;
}
}
return _43;
};
dojo.hostenv._global_omit_module_check=false;
dojo.hostenv.loadModule=function(_47,_48,_49){
if(!_47){
return;
}
_49=this._global_omit_module_check||_49;
var _4a=this.findModule(_47,false);
if(_4a){
return _4a;
}
if(dj_undef(_47,this.loading_modules_)){
this.addedToLoadingCount.push(_47);
}
this.loading_modules_[_47]=1;
var _4b=_47.replace(/\./g,"/")+".js";
var _4c=this.getModuleSymbols(_47);
var _4d=((_4c[0].charAt(0)!="/")&&(!_4c[0].match(/^\w+:/)));
var _4e=_4c[_4c.length-1];
var _4f=_47.split(".");
if(_4e=="*"){
_47=(_4f.slice(0,-1)).join(".");
while(_4c.length){
_4c.pop();
_4c.push(this.pkgFileName);
_4b=_4c.join("/")+".js";
if(_4d&&(_4b.charAt(0)=="/")){
_4b=_4b.slice(1);
}
ok=this.loadPath(_4b,((!_49)?_47:null));
if(ok){
break;
}
_4c.pop();
}
}else{
_4b=_4c.join("/")+".js";
_47=_4f.join(".");
var ok=this.loadPath(_4b,((!_49)?_47:null));
if((!ok)&&(!_48)){
_4c.pop();
while(_4c.length){
_4b=_4c.join("/")+".js";
ok=this.loadPath(_4b,((!_49)?_47:null));
if(ok){
break;
}
_4c.pop();
_4b=_4c.join("/")+"/"+this.pkgFileName+".js";
if(_4d&&(_4b.charAt(0)=="/")){
_4b=_4b.slice(1);
}
ok=this.loadPath(_4b,((!_49)?_47:null));
if(ok){
break;
}
}
}
if((!ok)&&(!_49)){
dojo.raise("Could not load '"+_47+"'; last tried '"+_4b+"'");
}
}
if(!_49&&!this["isXDomain"]){
_4a=this.findModule(_47,false);
if(!_4a){
dojo.raise("symbol '"+_47+"' is not defined after loading '"+_4b+"'");
}
}
return _4a;
};
dojo.hostenv.startPackage=function(_51){
var _52=dojo.evalObjPath((_51.split(".").slice(0,-1)).join("."));
this.loaded_modules_[(new String(_51)).toLowerCase()]=_52;
var _53=_51.split(/\./);
if(_53[_53.length-1]=="*"){
_53.pop();
}
return dojo.evalObjPath(_53.join("."),true);
};
dojo.hostenv.findModule=function(_54,_55){
var lmn=(new String(_54)).toLowerCase();
if(this.loaded_modules_[lmn]){
return this.loaded_modules_[lmn];
}
var _57=dojo.evalObjPath(_54);
if((_54)&&(typeof _57!="undefined")&&(_57)){
this.loaded_modules_[lmn]=_57;
return _57;
}
if(_55){
dojo.raise("no loaded module named '"+_54+"'");
}
return null;
};
dojo.kwCompoundRequire=function(_58){
var _59=_58["common"]||[];
var _5a=(_58[dojo.hostenv.name_])?_59.concat(_58[dojo.hostenv.name_]||[]):_59.concat(_58["default"]||[]);
for(var x=0;x<_5a.length;x++){
var _5c=_5a[x];
if(_5c.constructor==Array){
dojo.hostenv.loadModule.apply(dojo.hostenv,_5c);
}else{
dojo.hostenv.loadModule(_5c);
}
}
};
dojo.require=function(){
dojo.hostenv.loadModule.apply(dojo.hostenv,arguments);
};
dojo.requireIf=function(){
if((arguments[0]===true)||(arguments[0]=="common")||(arguments[0]&&dojo.render[arguments[0]].capable)){
var _5d=[];
for(var i=1;i<arguments.length;i++){
_5d.push(arguments[i]);
}
dojo.require.apply(dojo,_5d);
}
};
dojo.requireAfterIf=dojo.requireIf;
dojo.provide=function(){
return dojo.hostenv.startPackage.apply(dojo.hostenv,arguments);
};
dojo.setModulePrefix=function(_5f,_60){
return dojo.hostenv.setModulePrefix(_5f,_60);
};
dojo.exists=function(obj,_62){
var p=_62.split(".");
for(var i=0;i<p.length;i++){
if(!(obj[p[i]])){
return false;
}
obj=obj[p[i]];
}
return true;
};
}
if(typeof window=="undefined"){
dojo.raise("no window object");
}
(function(){
if(djConfig.allowQueryConfig){
var _65=document.location.toString();
var _66=_65.split("?",2);
if(_66.length>1){
var _67=_66[1];
var _68=_67.split("&");
for(var x in _68){
var sp=_68[x].split("=");
if((sp[0].length>9)&&(sp[0].substr(0,9)=="djConfig.")){
var opt=sp[0].substr(9);
try{
djConfig[opt]=eval(sp[1]);
}
catch(e){
djConfig[opt]=sp[1];
}
}
}
}
}
if(((djConfig["baseScriptUri"]=="")||(djConfig["baseRelativePath"]==""))&&(document&&document.getElementsByTagName)){
var _6c=document.getElementsByTagName("script");
var _6d=/(__package__|dojo|bootstrap1)\.js([\?\.]|$)/i;
for(var i=0;i<_6c.length;i++){
var src=_6c[i].getAttribute("src");
if(!src){
continue;
}
var m=src.match(_6d);
if(m){
var _71=src.substring(0,m.index);
if(src.indexOf("bootstrap1")>-1){
_71+="../";
}
if(!this["djConfig"]){
djConfig={};
}
if(djConfig["baseScriptUri"]==""){
djConfig["baseScriptUri"]=_71;
}
if(djConfig["baseRelativePath"]==""){
djConfig["baseRelativePath"]=_71;
}
break;
}
}
}
var dr=dojo.render;
var drh=dojo.render.html;
var drs=dojo.render.svg;
var dua=drh.UA=navigator.userAgent;
var dav=drh.AV=navigator.appVersion;
var t=true;
var f=false;
drh.capable=t;
drh.support.builtin=t;
dr.ver=parseFloat(drh.AV);
dr.os.mac=dav.indexOf("Macintosh")>=0;
dr.os.win=dav.indexOf("Windows")>=0;
dr.os.linux=dav.indexOf("X11")>=0;
drh.opera=dua.indexOf("Opera")>=0;
drh.khtml=(dav.indexOf("Konqueror")>=0)||(dav.indexOf("Safari")>=0);
drh.safari=dav.indexOf("Safari")>=0;
var _79=dua.indexOf("Gecko");
drh.mozilla=drh.moz=(_79>=0)&&(!drh.khtml);
if(drh.mozilla){
drh.geckoVersion=dua.substring(_79+6,_79+14);
}
drh.ie=(document.all)&&(!drh.opera);
drh.ie50=drh.ie&&dav.indexOf("MSIE 5.0")>=0;
drh.ie55=drh.ie&&dav.indexOf("MSIE 5.5")>=0;
drh.ie60=drh.ie&&dav.indexOf("MSIE 6.0")>=0;
drh.ie70=drh.ie&&dav.indexOf("MSIE 7.0")>=0;
dojo.locale=(drh.ie?navigator.userLanguage:navigator.language).toLowerCase();
dr.vml.capable=drh.ie;
drs.capable=f;
drs.support.plugin=f;
drs.support.builtin=f;
if(document.implementation&&document.implementation.hasFeature&&document.implementation.hasFeature("org.w3c.dom.svg","1.0")){
drs.capable=t;
drs.support.builtin=t;
drs.support.plugin=f;
}
})();
dojo.hostenv.startPackage("dojo.hostenv");
dojo.render.name=dojo.hostenv.name_="browser";
dojo.hostenv.searchIds=[];
dojo.hostenv._XMLHTTP_PROGIDS=["Msxml2.XMLHTTP","Microsoft.XMLHTTP","Msxml2.XMLHTTP.4.0"];
dojo.hostenv.getXmlhttpObject=function(){
var _7a=null;
var _7b=null;
try{
_7a=new XMLHttpRequest();
}
catch(e){
}
if(!_7a){
for(var i=0;i<3;++i){
var _7d=dojo.hostenv._XMLHTTP_PROGIDS[i];
try{
_7a=new ActiveXObject(_7d);
}
catch(e){
_7b=e;
}
if(_7a){
dojo.hostenv._XMLHTTP_PROGIDS=[_7d];
break;
}
}
}
if(!_7a){
return dojo.raise("XMLHTTP not available",_7b);
}
return _7a;
};
dojo.hostenv.getText=function(uri,_7f,_80){
var _81=this.getXmlhttpObject();
if(_7f){
_81.onreadystatechange=function(){
if(4==_81.readyState){
if((!_81["status"])||((200<=_81.status)&&(300>_81.status))){
_7f(_81.responseText);
}
}
};
}
_81.open("GET",uri,_7f?true:false);
try{
_81.send(null);
if(_7f){
return null;
}
if((_81["status"])&&((200>_81.status)||(300<=_81.status))){
throw Error("Unable to load "+uri+" status:"+_81.status);
}
}
catch(e){
if((_80)&&(!_7f)){
return null;
}else{
throw e;
}
}
return _81.responseText;
};
dojo.hostenv.defaultDebugContainerId="dojoDebug";
dojo.hostenv._println_buffer=[];
dojo.hostenv._println_safe=false;
dojo.hostenv.println=function(_82){
if(!dojo.hostenv._println_safe){
dojo.hostenv._println_buffer.push(_82);
}else{
try{
var _83=document.getElementById(djConfig.debugContainerId?djConfig.debugContainerId:dojo.hostenv.defaultDebugContainerId);
if(!_83){
_83=document.getElementsByTagName("body")[0]||document.body;
}
var div=document.createElement("div");
div.appendChild(document.createTextNode(_82));
_83.appendChild(div);
}
catch(e){
try{
document.write("<div>"+_82+"</div>");
}
catch(e2){
window.status=_82;
}
}
}
};
dojo.addOnLoad(function(){
dojo.hostenv._println_safe=true;
while(dojo.hostenv._println_buffer.length>0){
dojo.hostenv.println(dojo.hostenv._println_buffer.shift());
}
});
function dj_addNodeEvtHdlr(_85,_86,fp,_88){
var _89=_85["on"+_86]||function(){
};
_85["on"+_86]=function(){
fp.apply(_85,arguments);
_89.apply(_85,arguments);
};
return true;
}
dj_addNodeEvtHdlr(window,"load",function(){
if(arguments.callee.initialized){
return;
}
arguments.callee.initialized=true;
var _8a=function(){
if(dojo.render.html.ie){
dojo.hostenv.makeWidgets();
}
};
if(dojo.hostenv.inFlightCount==0){
_8a();
dojo.hostenv.modulesLoaded();
}else{
dojo.addOnLoad(_8a);
}
});
dj_addNodeEvtHdlr(window,"unload",function(){
dojo.hostenv.unloaded();
});
dojo.hostenv.makeWidgets=function(){
var _8b=[];
if(djConfig.searchIds&&djConfig.searchIds.length>0){
_8b=_8b.concat(djConfig.searchIds);
}
if(dojo.hostenv.searchIds&&dojo.hostenv.searchIds.length>0){
_8b=_8b.concat(dojo.hostenv.searchIds);
}
if((djConfig.parseWidgets)||(_8b.length>0)){
if(dojo.evalObjPath("dojo.widget.Parse")){
var _8c=new dojo.xml.Parse();
if(_8b.length>0){
for(var x=0;x<_8b.length;x++){
var _8e=document.getElementById(_8b[x]);
if(!_8e){
continue;
}
var _8f=_8c.parseElement(_8e,null,true);
dojo.widget.getParser().createComponents(_8f);
}
}else{
if(djConfig.parseWidgets){
var _8f=_8c.parseElement(document.getElementsByTagName("body")[0]||document.body,null,true);
dojo.widget.getParser().createComponents(_8f);
}
}
}
}
};
dojo.addOnLoad(function(){
if(!dojo.render.html.ie){
dojo.hostenv.makeWidgets();
}
});
try{
if(dojo.render.html.ie){
document.write("<style>v:*{ behavior:url(#default#VML); }</style>");
document.write("<xml:namespace ns=\"urn:schemas-microsoft-com:vml\" prefix=\"v\"/>");
}
}
catch(e){
}
dojo.hostenv.writeIncludes=function(){
};
dojo.byId=function(id,doc){
if(id&&(typeof id=="string"||id instanceof String)){
if(!doc){
doc=document;
}
return doc.getElementById(id);
}
return id;
};
if(!dojo.hostenv.findModule("ning.loader",false)){
dojo.provide("ning.loader");
(function(){
var _92=/xn_dojo_uncompressed=true/.test(window.location);
var _93={ning:{loader:{__module__:true}}};
var _94={};
var _95=[];
var _96=function(_97,_98){
var _99=_98.split(".");
var cur=_97;
for(var i=0;i<_99.length;++i){
var _9c=_99[i];
if(!cur[_9c]){
cur[_9c]={};
}
cur=cur[_9c];
}
cur["__module__"]=true;
};
var _9d=function(_9e,_9f){
var _a0=[];
var _a1=false;
for(var i in _9e){
if(i=="__module__"){
_a1=true;
}else{
_a0.push(i+_9d(_9e[i],true));
}
}
if(_a0.length>(_a1?0:1)){
return ((_a1?"{":"(")+_a0.join(",")+(_a1?"}":")")).replace(/(\)|}),/g,"$1");
}
return _a0[0]?((_9f?".":"")+_a0[0]):"";
};
var _a3=dojo.hostenv.startPackage;
dojo.hostenv.startPackage=function(_a4){
_96(_93,_a4);
return _a3.apply(dojo.hostenv,arguments);
};
ning.loader={_pending:{},_evalInTopLevel:function(_a5){
if(!_a5){
return;
}
window.execScript?window.execScript(_a5):eval.call(window,_a5);
},version:null,guard:function(_a6,_a7){
if(!dojo.hostenv.findModule(modulename,false)){
_a7();
}
},setPrefixPattern:function(_a8,_a9){
var _aa=String(_a8).replace(/^\/(.*)\/$/,"$1");
if(_aa==String(_a8)){
throw "Bogus parameter "+_a8+" passed to ning.loader.setPrefixPattern";
}
if(_94[_a8]){
if(_94[_a8]==_a9){
return;
}
throw "Pattern "+_a8+" has already been defined by calling ning.loader.setPrefixPattern";
}
_94[_aa]=_a9;
_95.push(new RegExp("^"+_aa+"$"));
},require:function(){
var _ab="@NING_RESOURCE_VERSION@_"+(ning.loader.version||(new Date()).valueOf());
var _ac=800;
var url="http://"+window.location.host+"/xn/loader?v="+_ab+"&r=";
var _ae=arguments.length-1;
var _af=false;
if(arguments[_ae] instanceof Function||typeof arguments[_ae]=="function"){
_af=arguments[_ae];
}else{
++_ae;
}
var _b0=null;
for(var i=0;i<_ae;++i){
if(arguments[i]&&!dojo.hostenv.findModule(arguments[i],false)){
if(!_b0){
_b0={};
}
_96(_b0,arguments[i]);
}
}
if(!_b0){
if(_af){
_af();
}
return;
}
url+=_9d(_b0);
url+="&p=";
if(dojo&&dojo.hostenv&&dojo.hostenv.modulePrefixes_){
for(var i in dojo.hostenv.modulePrefixes_){
var _b2=false;
var _b3=dojo.hostenv.modulePrefixes_[i].name;
for(var p=0;p<_95.length;++p){
var _b5=_95[p];
if(_b5.test(_b3)){
_b2=true;
break;
}
}
if(!_b2){
url+=encodeURIComponent(_b3)+":"+encodeURIComponent(dojo.hostenv.modulePrefixes_[i].value)+",";
}
}
}
for(var _b5 in _94){
url+=encodeURIComponent(_b5)+"=>"+encodeURIComponent(_94[_b5]);
}
if(_92){
url+="&uncompressed=true";
}
if(url.length>_ac){
for(var i=0;i<_ae;++i){
dojo.require.apply(dojo,[arguments[i]]);
}
if(_af){
_af();
}
return;
}
url+="&h="+_9d(_93);
if(url.length>_ac){
url=url.substring(0,_ac);
}
if(_af){
if(djConfig.isDebug){
var id=(new Date()).getTime();
ning.loader._pending[id]=function(){
_af();
ning.loader._pending[id]=null;
};
var _b7=document.createElement("script");
_b7.setAttribute("src",url+"&pending="+id);
document.getElementsByTagName("head").item(0).appendChild(_b7);
}else{
dojo.hostenv.getText(url,function(_b8){
try{
ning.loader._evalInTopLevel(_b8);
_af();
}
catch(e){
setTimeout(function(){
throw e;
},0);
}
});
}
}else{
ning.loader._evalInTopLevel(dojo.hostenv.getText(url));
}
},setDebug:function(_b9){
_92=!_b9;
}};
})();
}
dojo.provide("dojo.string.common");
dojo.require("dojo.string");
dojo.string.trim=function(str,wh){
if(!str.replace){
return str;
}
if(!str.length){
return str;
}
var re=(wh>0)?(/^\s+/):(wh<0)?(/\s+$/):(/^\s+|\s+$/g);
return str.replace(re,"");
};
dojo.string.trimStart=function(str){
return dojo.string.trim(str,1);
};
dojo.string.trimEnd=function(str){
return dojo.string.trim(str,-1);
};
dojo.string.repeat=function(str,_c0,_c1){
var out="";
for(var i=0;i<_c0;i++){
out+=str;
if(_c1&&i<_c0-1){
out+=_c1;
}
}
return out;
};
dojo.string.pad=function(str,len,c,dir){
var out=String(str);
if(!c){
c="0";
}
if(!dir){
dir=1;
}
while(out.length<len){
if(dir>0){
out=c+out;
}else{
out+=c;
}
}
return out;
};
dojo.string.padLeft=function(str,len,c){
return dojo.string.pad(str,len,c,1);
};
dojo.string.padRight=function(str,len,c){
return dojo.string.pad(str,len,c,-1);
};
dojo.provide("dojo.string");
dojo.require("dojo.string.common");
dojo.provide("dojo.lang.common");
dojo.require("dojo.lang");
dojo.lang._mixin=function(obj,_d0){
var _d1={};
for(var x in _d0){
if(typeof _d1[x]=="undefined"||_d1[x]!=_d0[x]){
obj[x]=_d0[x];
}
}
if(dojo.render.html.ie&&dojo.lang.isFunction(_d0["toString"])&&_d0["toString"]!=obj["toString"]){
obj.toString=_d0.toString;
}
return obj;
};
dojo.lang.mixin=function(obj,_d4){
for(var i=1,l=arguments.length;i<l;i++){
dojo.lang._mixin(obj,arguments[i]);
}
return obj;
};
dojo.lang.extend=function(_d6,_d7){
for(var i=1,l=arguments.length;i<l;i++){
dojo.lang._mixin(_d6.prototype,arguments[i]);
}
return _d6;
};
dojo.lang.find=function(arr,val,_db,_dc){
if(!dojo.lang.isArrayLike(arr)&&dojo.lang.isArrayLike(val)){
var a=arr;
arr=val;
val=a;
}
var _de=dojo.lang.isString(arr);
if(_de){
arr=arr.split("");
}
if(_dc){
var _df=-1;
var i=arr.length-1;
var end=-1;
}else{
var _df=1;
var i=0;
var end=arr.length;
}
if(_db){
while(i!=end){
if(arr[i]===val){
return i;
}
i+=_df;
}
}else{
while(i!=end){
if(arr[i]==val){
return i;
}
i+=_df;
}
}
return -1;
};
dojo.lang.indexOf=dojo.lang.find;
dojo.lang.findLast=function(arr,val,_e4){
return dojo.lang.find(arr,val,_e4,true);
};
dojo.lang.lastIndexOf=dojo.lang.findLast;
dojo.lang.inArray=function(arr,val){
return dojo.lang.find(arr,val)>-1;
};
dojo.lang.isObject=function(wh){
if(typeof wh=="undefined"){
return false;
}
return (typeof wh=="object"||wh===null||dojo.lang.isArray(wh)||dojo.lang.isFunction(wh));
};
dojo.lang.isArray=function(wh){
return (wh instanceof Array||typeof wh=="array");
};
dojo.lang.isArrayLike=function(wh){
if(dojo.lang.isString(wh)){
return false;
}
if(dojo.lang.isFunction(wh)){
return false;
}
if(dojo.lang.isArray(wh)){
return true;
}
if(typeof wh!="undefined"&&wh&&dojo.lang.isNumber(wh.length)&&isFinite(wh.length)){
return true;
}
return false;
};
dojo.lang.isFunction=function(wh){
if(!wh){
return false;
}
return (wh instanceof Function||typeof wh=="function");
};
dojo.lang.isString=function(wh){
return (wh instanceof String||typeof wh=="string");
};
dojo.lang.isAlien=function(wh){
if(!wh){
return false;
}
return !dojo.lang.isFunction()&&/\{\s*\[native code\]\s*\}/.test(String(wh));
};
dojo.lang.isBoolean=function(wh){
return (wh instanceof Boolean||typeof wh=="boolean");
};
dojo.lang.isNumber=function(wh){
return (wh instanceof Number||typeof wh=="number");
};
dojo.lang.isUndefined=function(wh){
return ((wh==undefined)&&(typeof wh=="undefined"));
};
dojo.provide("dojo.lang.extras");
dojo.require("dojo.lang.common");
dojo.lang.setTimeout=function(_f0,_f1){
var _f2=window,argsStart=2;
if(!dojo.lang.isFunction(_f0)){
_f2=_f0;
_f0=_f1;
_f1=arguments[2];
argsStart++;
}
if(dojo.lang.isString(_f0)){
_f0=_f2[_f0];
}
var _f3=[];
for(var i=argsStart;i<arguments.length;i++){
_f3.push(arguments[i]);
}
return setTimeout(function(){
_f0.apply(_f2,_f3);
},_f1);
};
dojo.lang.getNameInObj=function(ns,_f6){
if(!ns){
ns=dj_global;
}
for(var x in ns){
if(ns[x]===_f6){
return new String(x);
}
}
return null;
};
dojo.lang.shallowCopy=function(obj){
var ret={},key;
for(key in obj){
if(dojo.lang.isUndefined(ret[key])){
ret[key]=obj[key];
}
}
return ret;
};
dojo.lang.firstValued=function(){
for(var i=0;i<arguments.length;i++){
if(typeof arguments[i]!="undefined"){
return arguments[i];
}
}
return undefined;
};
dojo.lang.getObjPathValue=function(_fb,_fc,_fd){
with(dojo.parseObjPath(_fb,_fc,_fd)){
return dojo.evalProp(prop,obj,_fd);
}
};
dojo.lang.setObjPathValue=function(_fe,_ff,_100,_101){
if(arguments.length<4){
_101=true;
}
with(dojo.parseObjPath(_fe,_100,_101)){
if(obj&&(_101||(prop in obj))){
obj[prop]=_ff;
}
}
};
dojo.provide("dojo.io");
dojo.provide("dojo.io.IO");
dojo.require("dojo.string");
dojo.require("dojo.lang.extras");
dojo.io.transports=[];
dojo.io.hdlrFuncNames=["load","error","timeout"];
dojo.io.Request=function(url,_103,_104,_105){
if((arguments.length==1)&&(arguments[0].constructor==Object)){
this.fromKwArgs(arguments[0]);
}else{
this.url=url;
if(_103){
this.mimetype=_103;
}
if(_104){
this.transport=_104;
}
if(arguments.length>=4){
this.changeUrl=_105;
}
}
};
dojo.lang.extend(dojo.io.Request,{url:"",mimetype:"text/plain",method:"GET",content:undefined,transport:undefined,changeUrl:undefined,formNode:undefined,sync:false,bindSuccess:false,useCache:false,preventCache:false,load:function(type,data,evt){
},error:function(type,_10a){
},timeout:function(type){
},handle:function(){
},timeoutSeconds:0,abort:function(){
},fromKwArgs:function(_10c){
if(_10c["url"]){
_10c.url=_10c.url.toString();
}
if(_10c["formNode"]){
_10c.formNode=dojo.byId(_10c.formNode);
}
if(!_10c["method"]&&_10c["formNode"]&&_10c["formNode"].method){
_10c.method=_10c["formNode"].method;
}
if(!_10c["handle"]&&_10c["handler"]){
_10c.handle=_10c.handler;
}
if(!_10c["load"]&&_10c["loaded"]){
_10c.load=_10c.loaded;
}
if(!_10c["changeUrl"]&&_10c["changeURL"]){
_10c.changeUrl=_10c.changeURL;
}
_10c.encoding=dojo.lang.firstValued(_10c["encoding"],djConfig["bindEncoding"],"");
_10c.sendTransport=dojo.lang.firstValued(_10c["sendTransport"],djConfig["ioSendTransport"],false);
var _10d=dojo.lang.isFunction;
for(var x=0;x<dojo.io.hdlrFuncNames.length;x++){
var fn=dojo.io.hdlrFuncNames[x];
if(_10d(_10c[fn])){
continue;
}
if(_10d(_10c["handle"])){
_10c[fn]=_10c.handle;
}
}
dojo.lang.mixin(this,_10c);
}});
dojo.io.Error=function(msg,type,num){
this.message=msg;
this.type=type||"unknown";
this.number=num||0;
};
dojo.io.transports.addTransport=function(name){
this.push(name);
this[name]=dojo.io[name];
};
dojo.io.bind=function(_114){
if(!(_114 instanceof dojo.io.Request)){
try{
_114=new dojo.io.Request(_114);
}
catch(e){
dojo.debug(e);
}
}
var _115="";
if(_114["transport"]){
_115=_114["transport"];
if(!this[_115]){
return _114;
}
}else{
for(var x=0;x<dojo.io.transports.length;x++){
var tmp=dojo.io.transports[x];
if((this[tmp])&&(this[tmp].canHandle(_114))){
_115=tmp;
}
}
if(_115==""){
return _114;
}
}
this[_115].bind(_114);
_114.bindSuccess=true;
return _114;
};
dojo.io.queueBind=function(_118){
if(!(_118 instanceof dojo.io.Request)){
try{
_118=new dojo.io.Request(_118);
}
catch(e){
dojo.debug(e);
}
}
var _119=_118.load;
_118.load=function(){
dojo.io._queueBindInFlight=false;
var ret=_119.apply(this,arguments);
dojo.io._dispatchNextQueueBind();
return ret;
};
var _11b=_118.error;
_118.error=function(){
dojo.io._queueBindInFlight=false;
var ret=_11b.apply(this,arguments);
dojo.io._dispatchNextQueueBind();
return ret;
};
dojo.io._bindQueue.push(_118);
dojo.io._dispatchNextQueueBind();
return _118;
};
dojo.io._dispatchNextQueueBind=function(){
if(!dojo.io._queueBindInFlight){
dojo.io._queueBindInFlight=true;
if(dojo.io._bindQueue.length>0){
dojo.io.bind(dojo.io._bindQueue.shift());
}else{
dojo.io._queueBindInFlight=false;
}
}
};
dojo.io._bindQueue=[];
dojo.io._queueBindInFlight=false;
dojo.io.argsFromMap=function(map,_11e,last){
var enc=/utf/i.test(_11e||"")?encodeURIComponent:dojo.string.encodeAscii;
var _121=[];
var _122=new Object();
for(var name in map){
var _124=function(elt){
var val=enc(name)+"="+enc(elt);
_121[(last==name)?"push":"unshift"](val);
};
if(!_122[name]){
var _127=map[name];
if(dojo.lang.isArray(_127)){
dojo.lang.forEach(_127,_124);
}else{
_124(_127);
}
}
}
return _121.join("&");
};
dojo.io.setIFrameSrc=function(_128,src,_12a){
try{
var r=dojo.render.html;
if(!_12a){
if(r.safari){
_128.location=src;
}else{
frames[_128.name].location=src;
}
}else{
var idoc;
if(r.ie){
idoc=_128.contentWindow.document;
}else{
if(r.safari){
idoc=_128.document;
}else{
idoc=_128.contentWindow;
}
}
if(!idoc){
_128.location=src;
return;
}else{
idoc.location.replace(src);
}
}
}
catch(e){
dojo.debug(e);
dojo.debug("setIFrameSrc: "+e);
}
};
dojo.provide("dojo.lang.array");
dojo.require("dojo.lang.common");
dojo.lang.has=function(obj,name){
try{
return (typeof obj[name]!="undefined");
}
catch(e){
return false;
}
};
dojo.lang.isEmpty=function(obj){
if(dojo.lang.isObject(obj)){
var tmp={};
var _131=0;
for(var x in obj){
if(obj[x]&&(!tmp[x])){
_131++;
break;
}
}
return (_131==0);
}else{
if(dojo.lang.isArrayLike(obj)||dojo.lang.isString(obj)){
return obj.length==0;
}
}
};
dojo.lang.map=function(arr,obj,_135){
var _136=dojo.lang.isString(arr);
if(_136){
arr=arr.split("");
}
if(dojo.lang.isFunction(obj)&&(!_135)){
_135=obj;
obj=dj_global;
}else{
if(dojo.lang.isFunction(obj)&&_135){
var _137=obj;
obj=_135;
_135=_137;
}
}
if(Array.map){
var _138=Array.map(arr,_135,obj);
}else{
var _138=[];
for(var i=0;i<arr.length;++i){
_138.push(_135.call(obj,arr[i]));
}
}
if(_136){
return _138.join("");
}else{
return _138;
}
};
dojo.lang.forEach=function(_13a,_13b,_13c){
if(dojo.lang.isString(_13a)){
_13a=_13a.split("");
}
if(Array.forEach){
Array.forEach(_13a,_13b,_13c);
}else{
if(!_13c){
_13c=dj_global;
}
for(var i=0,l=_13a.length;i<l;i++){
_13b.call(_13c,_13a[i],i,_13a);
}
}
};
dojo.lang._everyOrSome=function(_13e,arr,_140,_141){
if(dojo.lang.isString(arr)){
arr=arr.split("");
}
if(Array.every){
return Array[(_13e)?"every":"some"](arr,_140,_141);
}else{
if(!_141){
_141=dj_global;
}
for(var i=0,l=arr.length;i<l;i++){
var _143=_140.call(_141,arr[i],i,arr);
if((_13e)&&(!_143)){
return false;
}else{
if((!_13e)&&(_143)){
return true;
}
}
}
return (_13e)?true:false;
}
};
dojo.lang.every=function(arr,_145,_146){
return this._everyOrSome(true,arr,_145,_146);
};
dojo.lang.some=function(arr,_148,_149){
return this._everyOrSome(false,arr,_148,_149);
};
dojo.lang.filter=function(arr,_14b,_14c){
var _14d=dojo.lang.isString(arr);
if(_14d){
arr=arr.split("");
}
if(Array.filter){
var _14e=Array.filter(arr,_14b,_14c);
}else{
if(!_14c){
if(arguments.length>=3){
dojo.raise("thisObject doesn't exist!");
}
_14c=dj_global;
}
var _14e=[];
for(var i=0;i<arr.length;i++){
if(_14b.call(_14c,arr[i],i,arr)){
_14e.push(arr[i]);
}
}
}
if(_14d){
return _14e.join("");
}else{
return _14e;
}
};
dojo.lang.unnest=function(){
var out=[];
for(var i=0;i<arguments.length;i++){
if(dojo.lang.isArrayLike(arguments[i])){
var add=dojo.lang.unnest.apply(this,arguments[i]);
out=out.concat(add);
}else{
out.push(arguments[i]);
}
}
return out;
};
dojo.lang.toArray=function(_153,_154){
var _155=[];
for(var i=_154||0;i<_153.length;i++){
_155.push(_153[i]);
}
return _155;
};
dojo.provide("dojo.lang.func");
dojo.require("dojo.lang.common");
dojo.lang.hitch=function(_157,_158){
if(dojo.lang.isString(_158)){
var fcn=_157[_158];
}else{
var fcn=_158;
}
return function(){
return fcn.apply(_157,arguments);
};
};
dojo.lang.anonCtr=0;
dojo.lang.anon={};
dojo.lang.nameAnonFunc=function(_15a,_15b,_15c){
var nso=(_15b||dojo.lang.anon);
if((_15c)||((dj_global["djConfig"])&&(djConfig["slowAnonFuncLookups"]==true))){
for(var x in nso){
if(nso[x]===_15a){
return x;
}
}
}
var ret="__"+dojo.lang.anonCtr++;
while(typeof nso[ret]!="undefined"){
ret="__"+dojo.lang.anonCtr++;
}
nso[ret]=_15a;
return ret;
};
dojo.lang.forward=function(_160){
return function(){
return this[_160].apply(this,arguments);
};
};
dojo.lang.curry=function(ns,func){
var _163=[];
ns=ns||dj_global;
if(dojo.lang.isString(func)){
func=ns[func];
}
for(var x=2;x<arguments.length;x++){
_163.push(arguments[x]);
}
var _165=(func["__preJoinArity"]||func.length)-_163.length;
function gather(_166,_167,_168){
var _169=_168;
var _16a=_167.slice(0);
for(var x=0;x<_166.length;x++){
_16a.push(_166[x]);
}
_168=_168-_166.length;
if(_168<=0){
var res=func.apply(ns,_16a);
_168=_169;
return res;
}else{
return function(){
return gather(arguments,_16a,_168);
};
}
}
return gather([],_163,_165);
};
dojo.lang.curryArguments=function(ns,func,args,_170){
var _171=[];
var x=_170||0;
for(x=_170;x<args.length;x++){
_171.push(args[x]);
}
return dojo.lang.curry.apply(dojo.lang,[ns,func].concat(_171));
};
dojo.lang.tryThese=function(){
for(var x=0;x<arguments.length;x++){
try{
if(typeof arguments[x]=="function"){
var ret=(arguments[x]());
if(ret){
return ret;
}
}
}
catch(e){
dojo.debug(e);
}
}
};
dojo.lang.delayThese=function(farr,cb,_177,_178){
if(!farr.length){
if(typeof _178=="function"){
_178();
}
return;
}
if((typeof _177=="undefined")&&(typeof cb=="number")){
_177=cb;
cb=function(){
};
}else{
if(!cb){
cb=function(){
};
if(!_177){
_177=0;
}
}
}
setTimeout(function(){
(farr.shift())();
cb();
dojo.lang.delayThese(farr,cb,_177,_178);
},_177);
};
dojo.provide("dojo.string.extras");
dojo.require("dojo.string.common");
dojo.require("dojo.lang");
dojo.string.substituteParams=function(_179,hash){
var map=(typeof hash=="object")?hash:dojo.lang.toArray(arguments,1);
return _179.replace(/\%\{(\w+)\}/g,function(_17c,key){
if(dojo.lang.isUndefined(map[key])){
dojo.raise("Substitution not found: "+key);
}
return map[key];
});
};
dojo.string.paramString=function(str,_17f,_180){
dojo.deprecated("dojo.string.paramString","use dojo.string.substituteParams instead","0.4");
for(var name in _17f){
var re=new RegExp("\\%\\{"+name+"\\}","g");
str=str.replace(re,_17f[name]);
}
if(_180){
str=str.replace(/%\{([^\}\s]+)\}/g,"");
}
return str;
};
dojo.string.capitalize=function(str){
if(!dojo.lang.isString(str)){
return "";
}
if(arguments.length==0){
str=this;
}
var _184=str.split(" ");
for(var i=0;i<_184.length;i++){
_184[i]=_184[i].charAt(0).toUpperCase()+_184[i].substring(1);
}
return _184.join(" ");
};
dojo.string.isBlank=function(str){
if(!dojo.lang.isString(str)){
return true;
}
return (dojo.string.trim(str).length==0);
};
dojo.string.encodeAscii=function(str){
if(!dojo.lang.isString(str)){
return str;
}
var ret="";
var _189=escape(str);
var _18a,re=/%u([0-9A-F]{4})/i;
while((_18a=_189.match(re))){
var num=Number("0x"+_18a[1]);
var _18c=escape("&#"+num+";");
ret+=_189.substring(0,_18a.index)+_18c;
_189=_189.substring(_18a.index+_18a[0].length);
}
ret+=_189.replace(/\+/g,"%2B");
return ret;
};
dojo.string.escape=function(type,str){
var args=dojo.lang.toArray(arguments,1);
switch(type.toLowerCase()){
case "xml":
case "html":
case "xhtml":
return dojo.string.escapeXml.apply(this,args);
case "sql":
return dojo.string.escapeSql.apply(this,args);
case "regexp":
case "regex":
return dojo.string.escapeRegExp.apply(this,args);
case "javascript":
case "jscript":
case "js":
return dojo.string.escapeJavaScript.apply(this,args);
case "ascii":
return dojo.string.encodeAscii.apply(this,args);
default:
return str;
}
};
dojo.string.escapeXml=function(str,_191){
str=str.replace(/&/gm,"&amp;").replace(/</gm,"&lt;").replace(/>/gm,"&gt;").replace(/"/gm,"&quot;");
if(!_191){
str=str.replace(/'/gm,"&#39;");
}
return str;
};
dojo.string.escapeSql=function(str){
return str.replace(/'/gm,"''");
};
dojo.string.escapeRegExp=function(str){
return str.replace(/\\/gm,"\\\\").replace(/([\f\b\n\t\r[\^$|?*+(){}])/gm,"\\$1");
};
dojo.string.escapeJavaScript=function(str){
return str.replace(/(["'\f\b\n\t\r])/gm,"\\$1");
};
dojo.string.escapeString=function(str){
return ("\""+str.replace(/(["\\])/g,"\\$1")+"\"").replace(/[\f]/g,"\\f").replace(/[\b]/g,"\\b").replace(/[\n]/g,"\\n").replace(/[\t]/g,"\\t").replace(/[\r]/g,"\\r");
};
dojo.string.summary=function(str,len){
if(!len||str.length<=len){
return str;
}else{
return str.substring(0,len).replace(/\.+$/,"")+"...";
}
};
dojo.string.endsWith=function(str,end,_19a){
if(_19a){
str=str.toLowerCase();
end=end.toLowerCase();
}
if((str.length-end.length)<0){
return false;
}
return str.lastIndexOf(end)==str.length-end.length;
};
dojo.string.endsWithAny=function(str){
for(var i=1;i<arguments.length;i++){
if(dojo.string.endsWith(str,arguments[i])){
return true;
}
}
return false;
};
dojo.string.startsWith=function(str,_19e,_19f){
if(_19f){
str=str.toLowerCase();
_19e=_19e.toLowerCase();
}
return str.indexOf(_19e)==0;
};
dojo.string.startsWithAny=function(str){
for(var i=1;i<arguments.length;i++){
if(dojo.string.startsWith(str,arguments[i])){
return true;
}
}
return false;
};
dojo.string.has=function(str){
for(var i=1;i<arguments.length;i++){
if(str.indexOf(arguments[i])>-1){
return true;
}
}
return false;
};
dojo.string.normalizeNewlines=function(text,_1a5){
if(_1a5=="\n"){
text=text.replace(/\r\n/g,"\n");
text=text.replace(/\r/g,"\n");
}else{
if(_1a5=="\r"){
text=text.replace(/\r\n/g,"\r");
text=text.replace(/\n/g,"\r");
}else{
text=text.replace(/([^\r])\n/g,"$1\r\n");
text=text.replace(/\r([^\n])/g,"\r\n$1");
}
}
return text;
};
dojo.string.splitEscaped=function(str,_1a7){
var _1a8=[];
for(var i=0,prevcomma=0;i<str.length;i++){
if(str.charAt(i)=="\\"){
i++;
continue;
}
if(str.charAt(i)==_1a7){
_1a8.push(str.substring(prevcomma,i));
prevcomma=i+1;
}
}
_1a8.push(str.substr(prevcomma));
return _1a8;
};
dojo.provide("dojo.dom");
dojo.require("dojo.lang.array");
dojo.dom.ELEMENT_NODE=1;
dojo.dom.ATTRIBUTE_NODE=2;
dojo.dom.TEXT_NODE=3;
dojo.dom.CDATA_SECTION_NODE=4;
dojo.dom.ENTITY_REFERENCE_NODE=5;
dojo.dom.ENTITY_NODE=6;
dojo.dom.PROCESSING_INSTRUCTION_NODE=7;
dojo.dom.COMMENT_NODE=8;
dojo.dom.DOCUMENT_NODE=9;
dojo.dom.DOCUMENT_TYPE_NODE=10;
dojo.dom.DOCUMENT_FRAGMENT_NODE=11;
dojo.dom.NOTATION_NODE=12;
dojo.dom.dojoml="http://www.dojotoolkit.org/2004/dojoml";
dojo.dom.xmlns={svg:"http://www.w3.org/2000/svg",smil:"http://www.w3.org/2001/SMIL20/",mml:"http://www.w3.org/1998/Math/MathML",cml:"http://www.xml-cml.org",xlink:"http://www.w3.org/1999/xlink",xhtml:"http://www.w3.org/1999/xhtml",xul:"http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul",xbl:"http://www.mozilla.org/xbl",fo:"http://www.w3.org/1999/XSL/Format",xsl:"http://www.w3.org/1999/XSL/Transform",xslt:"http://www.w3.org/1999/XSL/Transform",xi:"http://www.w3.org/2001/XInclude",xforms:"http://www.w3.org/2002/01/xforms",saxon:"http://icl.com/saxon",xalan:"http://xml.apache.org/xslt",xsd:"http://www.w3.org/2001/XMLSchema",dt:"http://www.w3.org/2001/XMLSchema-datatypes",xsi:"http://www.w3.org/2001/XMLSchema-instance",rdf:"http://www.w3.org/1999/02/22-rdf-syntax-ns#",rdfs:"http://www.w3.org/2000/01/rdf-schema#",dc:"http://purl.org/dc/elements/1.1/",dcq:"http://purl.org/dc/qualifiers/1.0","soap-env":"http://schemas.xmlsoap.org/soap/envelope/",wsdl:"http://schemas.xmlsoap.org/wsdl/",AdobeExtensions:"http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/"};
dojo.dom.isNode=function(wh){
if(typeof Element=="object"){
try{
return wh instanceof Element;
}
catch(E){
}
}else{
return wh&&!isNaN(wh.nodeType);
}
};
dojo.dom.getTagName=function(node){
dojo.deprecated("dojo.dom.getTagName","use node.tagName instead","0.4");
var _1ac=node.tagName;
if(_1ac.substr(0,5).toLowerCase()!="dojo:"){
if(_1ac.substr(0,4).toLowerCase()=="dojo"){
return "dojo:"+_1ac.substring(4).toLowerCase();
}
var djt=node.getAttribute("dojoType")||node.getAttribute("dojotype");
if(djt){
return "dojo:"+djt.toLowerCase();
}
if((node.getAttributeNS)&&(node.getAttributeNS(this.dojoml,"type"))){
return "dojo:"+node.getAttributeNS(this.dojoml,"type").toLowerCase();
}
try{
djt=node.getAttribute("dojo:type");
}
catch(e){
}
if(djt){
return "dojo:"+djt.toLowerCase();
}
if((!dj_global["djConfig"])||(!djConfig["ignoreClassNames"])){
var _1ae=node.className||node.getAttribute("class");
if((_1ae)&&(_1ae.indexOf)&&(_1ae.indexOf("dojo-")!=-1)){
var _1af=_1ae.split(" ");
for(var x=0;x<_1af.length;x++){
if((_1af[x].length>5)&&(_1af[x].indexOf("dojo-")>=0)){
return "dojo:"+_1af[x].substr(5).toLowerCase();
}
}
}
}
}
return _1ac.toLowerCase();
};
dojo.dom.getUniqueId=function(){
do{
var id="dj_unique_"+(++arguments.callee._idIncrement);
}while(document.getElementById(id));
return id;
};
dojo.dom.getUniqueId._idIncrement=0;
dojo.dom.firstElement=dojo.dom.getFirstChildElement=function(_1b2,_1b3){
var node=_1b2.firstChild;
while(node&&node.nodeType!=dojo.dom.ELEMENT_NODE){
node=node.nextSibling;
}
if(_1b3&&node&&node.tagName&&node.tagName.toLowerCase()!=_1b3.toLowerCase()){
node=dojo.dom.nextElement(node,_1b3);
}
return node;
};
dojo.dom.lastElement=dojo.dom.getLastChildElement=function(_1b5,_1b6){
var node=_1b5.lastChild;
while(node&&node.nodeType!=dojo.dom.ELEMENT_NODE){
node=node.previousSibling;
}
if(_1b6&&node&&node.tagName&&node.tagName.toLowerCase()!=_1b6.toLowerCase()){
node=dojo.dom.prevElement(node,_1b6);
}
return node;
};
dojo.dom.nextElement=dojo.dom.getNextSiblingElement=function(node,_1b9){
if(!node){
return null;
}
do{
node=node.nextSibling;
}while(node&&node.nodeType!=dojo.dom.ELEMENT_NODE);
if(node&&_1b9&&_1b9.toLowerCase()!=node.tagName.toLowerCase()){
return dojo.dom.nextElement(node,_1b9);
}
return node;
};
dojo.dom.prevElement=dojo.dom.getPreviousSiblingElement=function(node,_1bb){
if(!node){
return null;
}
if(_1bb){
_1bb=_1bb.toLowerCase();
}
do{
node=node.previousSibling;
}while(node&&node.nodeType!=dojo.dom.ELEMENT_NODE);
if(node&&_1bb&&_1bb.toLowerCase()!=node.tagName.toLowerCase()){
return dojo.dom.prevElement(node,_1bb);
}
return node;
};
dojo.dom.moveChildren=function(_1bc,_1bd,trim){
var _1bf=0;
if(trim){
while(_1bc.hasChildNodes()&&_1bc.firstChild.nodeType==dojo.dom.TEXT_NODE){
_1bc.removeChild(_1bc.firstChild);
}
while(_1bc.hasChildNodes()&&_1bc.lastChild.nodeType==dojo.dom.TEXT_NODE){
_1bc.removeChild(_1bc.lastChild);
}
}
while(_1bc.hasChildNodes()){
_1bd.appendChild(_1bc.firstChild);
_1bf++;
}
return _1bf;
};
dojo.dom.copyChildren=function(_1c0,_1c1,trim){
var _1c3=_1c0.cloneNode(true);
return this.moveChildren(_1c3,_1c1,trim);
};
dojo.dom.removeChildren=function(node){
var _1c5=node.childNodes.length;
while(node.hasChildNodes()){
node.removeChild(node.firstChild);
}
return _1c5;
};
dojo.dom.replaceChildren=function(node,_1c7){
dojo.dom.removeChildren(node);
node.appendChild(_1c7);
};
dojo.dom.removeNode=function(node){
if(node&&node.parentNode){
return node.parentNode.removeChild(node);
}
};
dojo.dom.getAncestors=function(node,_1ca,_1cb){
var _1cc=[];
var _1cd=dojo.lang.isFunction(_1ca);
while(node){
if(!_1cd||_1ca(node)){
_1cc.push(node);
}
if(_1cb&&_1cc.length>0){
return _1cc[0];
}
node=node.parentNode;
}
if(_1cb){
return null;
}
return _1cc;
};
dojo.dom.getAncestorsByTag=function(node,tag,_1d0){
tag=tag.toLowerCase();
return dojo.dom.getAncestors(node,function(el){
return ((el.tagName)&&(el.tagName.toLowerCase()==tag));
},_1d0);
};
dojo.dom.getFirstAncestorByTag=function(node,tag){
return dojo.dom.getAncestorsByTag(node,tag,true);
};
dojo.dom.isDescendantOf=function(node,_1d5,_1d6){
if(_1d6&&node){
node=node.parentNode;
}
while(node){
if(node==_1d5){
return true;
}
node=node.parentNode;
}
return false;
};
dojo.dom.innerXML=function(node){
if(node.innerXML){
return node.innerXML;
}else{
if(node.xml){
return node.xml;
}else{
if(typeof XMLSerializer!="undefined"){
return (new XMLSerializer()).serializeToString(node);
}
}
}
};
dojo.dom.createDocument=function(){
var doc=null;
if(!dj_undef("ActiveXObject")){
var _1d9=["MSXML2","Microsoft","MSXML","MSXML3"];
for(var i=0;i<_1d9.length;i++){
try{
doc=new ActiveXObject(_1d9[i]+".XMLDOM");
}
catch(e){
}
if(doc){
break;
}
}
}else{
if((document.implementation)&&(document.implementation.createDocument)){
doc=document.implementation.createDocument("","",null);
}
}
return doc;
};
dojo.dom.createDocumentFromText=function(str,_1dc){
if(!_1dc){
_1dc="text/xml";
}
if(!dj_undef("DOMParser")){
var _1dd=new DOMParser();
return _1dd.parseFromString(str,_1dc);
}else{
if(!dj_undef("ActiveXObject")){
var _1de=dojo.dom.createDocument();
if(_1de){
_1de.async=false;
_1de.loadXML(str);
return _1de;
}else{
dojo.debug("toXml didn't work?");
}
}else{
if(document.createElement){
var tmp=document.createElement("xml");
tmp.innerHTML=str;
if(document.implementation&&document.implementation.createDocument){
var _1e0=document.implementation.createDocument("foo","",null);
for(var i=0;i<tmp.childNodes.length;i++){
_1e0.importNode(tmp.childNodes.item(i),true);
}
return _1e0;
}
return ((tmp.document)&&(tmp.document.firstChild?tmp.document.firstChild:tmp));
}
}
}
return null;
};
dojo.dom.prependChild=function(node,_1e3){
if(_1e3.firstChild){
_1e3.insertBefore(node,_1e3.firstChild);
}else{
_1e3.appendChild(node);
}
return true;
};
dojo.dom.insertBefore=function(node,ref,_1e6){
if(_1e6!=true&&(node===ref||node.nextSibling===ref)){
return false;
}
var _1e7=ref.parentNode;
_1e7.insertBefore(node,ref);
return true;
};
dojo.dom.insertAfter=function(node,ref,_1ea){
var pn=ref.parentNode;
if(ref==pn.lastChild){
if((_1ea!=true)&&(node===ref)){
return false;
}
pn.appendChild(node);
}else{
return this.insertBefore(node,ref.nextSibling,_1ea);
}
return true;
};
dojo.dom.insertAtPosition=function(node,ref,_1ee){
if((!node)||(!ref)||(!_1ee)){
return false;
}
switch(_1ee.toLowerCase()){
case "before":
return dojo.dom.insertBefore(node,ref);
case "after":
return dojo.dom.insertAfter(node,ref);
case "first":
if(ref.firstChild){
return dojo.dom.insertBefore(node,ref.firstChild);
}else{
ref.appendChild(node);
return true;
}
break;
default:
ref.appendChild(node);
return true;
}
};
dojo.dom.insertAtIndex=function(node,_1f0,_1f1){
var _1f2=_1f0.childNodes;
if(!_1f2.length){
_1f0.appendChild(node);
return true;
}
var _1f3=null;
for(var i=0;i<_1f2.length;i++){
var _1f5=_1f2.item(i)["getAttribute"]?parseInt(_1f2.item(i).getAttribute("dojoinsertionindex")):-1;
if(_1f5<_1f1){
_1f3=_1f2.item(i);
}
}
if(_1f3){
return dojo.dom.insertAfter(node,_1f3);
}else{
return dojo.dom.insertBefore(node,_1f2.item(0));
}
};
dojo.dom.textContent=function(node,text){
if(text){
dojo.dom.replaceChildren(node,document.createTextNode(text));
return text;
}else{
var _1f8="";
if(node==null){
return _1f8;
}
for(var i=0;i<node.childNodes.length;i++){
switch(node.childNodes[i].nodeType){
case 1:
case 5:
_1f8+=dojo.dom.textContent(node.childNodes[i]);
break;
case 3:
case 2:
case 4:
_1f8+=node.childNodes[i].nodeValue;
break;
default:
break;
}
}
return _1f8;
}
};
dojo.dom.collectionToArray=function(_1fa){
dojo.deprecated("dojo.dom.collectionToArray","use dojo.lang.toArray instead","0.4");
return dojo.lang.toArray(_1fa);
};
dojo.dom.hasParent=function(node){
return node&&node.parentNode&&dojo.dom.isNode(node.parentNode);
};
dojo.dom.isTag=function(node){
if(node&&node.tagName){
var arr=dojo.lang.toArray(arguments,1);
return arr[dojo.lang.find(node.tagName,arr)]||"";
}
return "";
};
dojo.provide("dojo.undo.browser");
dojo.require("dojo.io");
try{
if((!djConfig["preventBackButtonFix"])&&(!dojo.hostenv.post_load_)){
document.write("<iframe style='border: 0px; width: 1px; height: 1px; position: absolute; bottom: 0px; right: 0px; visibility: visible;' name='djhistory' id='djhistory' src='"+(dojo.hostenv.getBaseScriptUri()+"iframe_history.html")+"'></iframe>");
}
}
catch(e){
}
if(dojo.render.html.opera){
dojo.debug("Opera is not supported with dojo.undo.browser, so back/forward detection will not work.");
}
dojo.undo.browser={initialHref:window.location.href,initialHash:window.location.hash,moveForward:false,historyStack:[],forwardStack:[],historyIframe:null,bookmarkAnchor:null,locationTimer:null,setInitialState:function(args){
this.initialState={"url":this.initialHref,"kwArgs":args,"urlHash":this.initialHash};
},addToHistory:function(args){
var hash=null;
if(!this.historyIframe){
this.historyIframe=window.frames["djhistory"];
}
if(!this.bookmarkAnchor){
this.bookmarkAnchor=document.createElement("a");
(document.body||document.getElementsByTagName("body")[0]).appendChild(this.bookmarkAnchor);
this.bookmarkAnchor.style.display="none";
}
if((!args["changeUrl"])||(dojo.render.html.ie)){
var url=dojo.hostenv.getBaseScriptUri()+"iframe_history.html?"+(new Date()).getTime();
this.moveForward=true;
dojo.io.setIFrameSrc(this.historyIframe,url,false);
}
if(args["changeUrl"]){
this.changingUrl=true;
hash="#"+((args["changeUrl"]!==true)?args["changeUrl"]:(new Date()).getTime());
setTimeout("window.location.href = '"+hash+"'; dojo.undo.browser.changingUrl = false;",1);
this.bookmarkAnchor.href=hash;
if(dojo.render.html.ie){
var _202=args["back"]||args["backButton"]||args["handle"];
var tcb=function(_204){
if(window.location.hash!=""){
setTimeout("window.location.href = '"+hash+"';",1);
}
_202.apply(this,[_204]);
};
if(args["back"]){
args.back=tcb;
}else{
if(args["backButton"]){
args.backButton=tcb;
}else{
if(args["handle"]){
args.handle=tcb;
}
}
}
this.forwardStack=[];
var _205=args["forward"]||args["forwardButton"]||args["handle"];
var tfw=function(_207){
if(window.location.hash!=""){
window.location.href=hash;
}
if(_205){
_205.apply(this,[_207]);
}
};
if(args["forward"]){
args.forward=tfw;
}else{
if(args["forwardButton"]){
args.forwardButton=tfw;
}else{
if(args["handle"]){
args.handle=tfw;
}
}
}
}else{
if(dojo.render.html.moz){
if(!this.locationTimer){
this.locationTimer=setInterval("dojo.undo.browser.checkLocation();",200);
}
}
}
}
this.historyStack.push({"url":url,"kwArgs":args,"urlHash":hash});
},checkLocation:function(){
if(!this.changingUrl){
var hsl=this.historyStack.length;
if((window.location.hash==this.initialHash||window.location.href==this.initialHref)&&(hsl==1)){
this.handleBackButton();
return;
}
if(this.forwardStack.length>0){
if(this.forwardStack[this.forwardStack.length-1].urlHash==window.location.hash){
this.handleForwardButton();
return;
}
}
if((hsl>=2)&&(this.historyStack[hsl-2])){
if(this.historyStack[hsl-2].urlHash==window.location.hash){
this.handleBackButton();
return;
}
}
}
},iframeLoaded:function(evt,_20a){
if(!dojo.render.html.opera){
var _20b=this._getUrlQuery(_20a.href);
if(_20b==null){
if(this.historyStack.length==1){
this.handleBackButton();
}
return;
}
if(this.moveForward){
this.moveForward=false;
return;
}
if(this.historyStack.length>=2&&_20b==this._getUrlQuery(this.historyStack[this.historyStack.length-2].url)){
this.handleBackButton();
}else{
if(this.forwardStack.length>0&&_20b==this._getUrlQuery(this.forwardStack[this.forwardStack.length-1].url)){
this.handleForwardButton();
}
}
}
},handleBackButton:function(){
var _20c=this.historyStack.pop();
if(!_20c){
return;
}
var last=this.historyStack[this.historyStack.length-1];
if(!last&&this.historyStack.length==0){
last=this.initialState;
}
if(last){
if(last.kwArgs["back"]){
last.kwArgs["back"]();
}else{
if(last.kwArgs["backButton"]){
last.kwArgs["backButton"]();
}else{
if(last.kwArgs["handle"]){
last.kwArgs.handle("back");
}
}
}
}
this.forwardStack.push(_20c);
},handleForwardButton:function(){
var last=this.forwardStack.pop();
if(!last){
return;
}
if(last.kwArgs["forward"]){
last.kwArgs.forward();
}else{
if(last.kwArgs["forwardButton"]){
last.kwArgs.forwardButton();
}else{
if(last.kwArgs["handle"]){
last.kwArgs.handle("forward");
}
}
}
this.historyStack.push(last);
},_getUrlQuery:function(url){
var _210=url.split("?");
if(_210.length<2){
return null;
}else{
return _210[1];
}
}};
dojo.provide("dojo.io.BrowserIO");
dojo.require("dojo.io");
dojo.require("dojo.lang.array");
dojo.require("dojo.lang.func");
dojo.require("dojo.string.extras");
dojo.require("dojo.dom");
dojo.require("dojo.undo.browser");
dojo.io.checkChildrenForFile=function(node){
var _212=false;
var _213=node.getElementsByTagName("input");
dojo.lang.forEach(_213,function(_214){
if(_212){
return;
}
if(_214.getAttribute("type")=="file"){
_212=true;
}
});
return _212;
};
dojo.io.formHasFile=function(_215){
return dojo.io.checkChildrenForFile(_215);
};
dojo.io.updateNode=function(node,_217){
node=dojo.byId(node);
var args=_217;
if(dojo.lang.isString(_217)){
args={url:_217};
}
args.mimetype="text/html";
args.load=function(t,d,e){
while(node.firstChild){
if(dojo["event"]){
try{
dojo.event.browser.clean(node.firstChild);
}
catch(e){
}
}
node.removeChild(node.firstChild);
}
node.innerHTML=d;
};
dojo.io.bind(args);
};
dojo.io.formFilter=function(node){
var type=(node.type||"").toLowerCase();
return !node.disabled&&node.name&&!dojo.lang.inArray(type,["file","submit","image","reset","button"]);
};
dojo.io.encodeForm=function(_21e,_21f,_220){
if((!_21e)||(!_21e.tagName)||(!_21e.tagName.toLowerCase()=="form")){
dojo.raise("Attempted to encode a non-form element.");
}
if(!_220){
_220=dojo.io.formFilter;
}
var enc=/utf/i.test(_21f||"")?encodeURIComponent:dojo.string.encodeAscii;
var _222=[];
for(var i=0;i<_21e.elements.length;i++){
var elm=_21e.elements[i];
if(!elm||elm.tagName.toLowerCase()=="fieldset"||!_220(elm)){
continue;
}
var name=enc(elm.name);
var type=elm.type.toLowerCase();
if(type=="select-multiple"){
for(var j=0;j<elm.options.length;j++){
if(elm.options[j].selected){
_222.push(name+"="+enc(elm.options[j].value));
}
}
}else{
if(dojo.lang.inArray(type,["radio","checkbox"])){
if(elm.checked){
_222.push(name+"="+enc(elm.value));
}
}else{
_222.push(name+"="+enc(elm.value));
}
}
}
var _228=_21e.getElementsByTagName("input");
for(var i=0;i<_228.length;i++){
var _229=_228[i];
if(_229.type.toLowerCase()=="image"&&_229.form==_21e&&_220(_229)){
var name=enc(_229.name);
_222.push(name+"="+enc(_229.value));
_222.push(name+".x=0");
_222.push(name+".y=0");
}
}
return _222.join("&")+"&";
};
dojo.io.FormBind=function(args){
this.bindArgs={};
if(args&&args.formNode){
this.init(args);
}else{
if(args){
this.init({formNode:args});
}
}
};
dojo.lang.extend(dojo.io.FormBind,{form:null,bindArgs:null,clickedButton:null,init:function(args){
var form=dojo.byId(args.formNode);
if(!form||!form.tagName||form.tagName.toLowerCase()!="form"){
throw new Error("FormBind: Couldn't apply, invalid form");
}else{
if(this.form==form){
return;
}else{
if(this.form){
throw new Error("FormBind: Already applied to a form");
}
}
}
dojo.lang.mixin(this.bindArgs,args);
this.form=form;
this.connect(form,"onsubmit","submit");
for(var i=0;i<form.elements.length;i++){
var node=form.elements[i];
if(node&&node.type&&dojo.lang.inArray(node.type.toLowerCase(),["submit","button"])){
this.connect(node,"onclick","click");
}
}
var _22f=form.getElementsByTagName("input");
for(var i=0;i<_22f.length;i++){
var _230=_22f[i];
if(_230.type.toLowerCase()=="image"&&_230.form==form){
this.connect(_230,"onclick","click");
}
}
},onSubmit:function(form){
return true;
},submit:function(e){
e.preventDefault();
if(this.onSubmit(this.form)){
dojo.io.bind(dojo.lang.mixin(this.bindArgs,{formFilter:dojo.lang.hitch(this,"formFilter")}));
}
},click:function(e){
var node=e.currentTarget;
if(node.disabled){
return;
}
this.clickedButton=node;
},formFilter:function(node){
var type=(node.type||"").toLowerCase();
var _237=false;
if(node.disabled||!node.name){
_237=false;
}else{
if(dojo.lang.inArray(type,["submit","button","image"])){
if(!this.clickedButton){
this.clickedButton=node;
}
_237=node==this.clickedButton;
}else{
_237=!dojo.lang.inArray(type,["file","submit","reset","button"]);
}
}
return _237;
},connect:function(_238,_239,_23a){
if(dojo.evalObjPath("dojo.event.connect")){
dojo.event.connect(_238,_239,this,_23a);
}else{
var fcn=dojo.lang.hitch(this,_23a);
_238[_239]=function(e){
if(!e){
e=window.event;
}
if(!e.currentTarget){
e.currentTarget=e.srcElement;
}
if(!e.preventDefault){
e.preventDefault=function(){
window.event.returnValue=false;
};
}
fcn(e);
};
}
}});
dojo.io.XMLHTTPTransport=new function(){
var _23d=this;
var _23e={};
this.useCache=false;
this.preventCache=false;
function getCacheKey(url,_240,_241){
return url+"|"+_240+"|"+_241.toLowerCase();
}
function addToCache(url,_243,_244,http){
_23e[getCacheKey(url,_243,_244)]=http;
}
function getFromCache(url,_247,_248){
return _23e[getCacheKey(url,_247,_248)];
}
this.clearCache=function(){
_23e={};
};
function doLoad(_249,http,url,_24c,_24d){
if(((http.status>=200)&&(http.status<300))||(http.status==304)||(location.protocol=="file:"&&(http.status==0||http.status==undefined))||(location.protocol=="chrome:"&&(http.status==0||http.status==undefined))){
var ret;
if(_249.method.toLowerCase()=="head"){
var _24f=http.getAllResponseHeaders();
ret={};
ret.toString=function(){
return _24f;
};
var _250=_24f.split(/[\r\n]+/g);
for(var i=0;i<_250.length;i++){
var pair=_250[i].match(/^([^:]+)\s*:\s*(.+)$/i);
if(pair){
ret[pair[1]]=pair[2];
}
}
}else{
if(_249.mimetype=="text/javascript"){
try{
ret=dj_eval(http.responseText);
}
catch(e){
dojo.debug(e);
dojo.debug(http.responseText);
ret=null;
}
}else{
if(_249.mimetype=="text/json"){
try{
ret=dj_eval("("+http.responseText+")");
}
catch(e){
dojo.debug(e);
dojo.debug(http.responseText);
ret=false;
}
}else{
if((_249.mimetype=="application/xml")||(_249.mimetype=="text/xml")){
ret=http.responseXML;
if(!ret||typeof ret=="string"||!http.getResponseHeader("Content-Type")){
ret=dojo.dom.createDocumentFromText(http.responseText);
}
}else{
ret=http.responseText;
}
}
}
}
if(_24d){
addToCache(url,_24c,_249.method,http);
}
_249[(typeof _249.load=="function")?"load":"handle"]("load",ret,http,_249);
}else{
var _253=new dojo.io.Error("XMLHttpTransport Error: "+http.status+" "+http.statusText);
_249[(typeof _249.error=="function")?"error":"handle"]("error",_253,http,_249);
}
}
function setHeaders(http,_255){
if(_255["headers"]){
for(var _256 in _255["headers"]){
if(_256.toLowerCase()=="content-type"&&!_255["contentType"]){
_255["contentType"]=_255["headers"][_256];
}else{
http.setRequestHeader(_256,_255["headers"][_256]);
}
}
}
}
this.inFlight=[];
this.inFlightTimer=null;
this.startWatchingInFlight=function(){
if(!this.inFlightTimer){
this.inFlightTimer=setInterval("dojo.io.XMLHTTPTransport.watchInFlight();",10);
}
};
this.watchInFlight=function(){
var now=null;
for(var x=this.inFlight.length-1;x>=0;x--){
var tif=this.inFlight[x];
if(!tif){
this.inFlight.splice(x,1);
continue;
}
if(4==tif.http.readyState){
this.inFlight.splice(x,1);
doLoad(tif.req,tif.http,tif.url,tif.query,tif.useCache);
}else{
if(tif.startTime){
if(!now){
now=(new Date()).getTime();
}
if(tif.startTime+(tif.req.timeoutSeconds*1000)<now){
if(typeof tif.http.abort=="function"){
tif.http.abort();
}
this.inFlight.splice(x,1);
tif.req[(typeof tif.req.timeout=="function")?"timeout":"handle"]("timeout",null,tif.http,tif.req);
}
}
}
}
if(this.inFlight.length==0){
clearInterval(this.inFlightTimer);
this.inFlightTimer=null;
}
};
var _25a=dojo.hostenv.getXmlhttpObject()?true:false;
this.canHandle=function(_25b){
return _25a&&dojo.lang.inArray((_25b["mimetype"].toLowerCase()||""),["text/plain","text/html","application/xml","text/xml","text/javascript","text/json"])&&!(_25b["formNode"]&&dojo.io.formHasFile(_25b["formNode"]));
};
this.multipartBoundary="45309FFF-BD65-4d50-99C9-36986896A96F";
this.bind=function(_25c){
if(!_25c["url"]){
if(!_25c["formNode"]&&(_25c["backButton"]||_25c["back"]||_25c["changeUrl"]||_25c["watchForURL"])&&(!djConfig.preventBackButtonFix)){
dojo.deprecated("Using dojo.io.XMLHTTPTransport.bind() to add to browser history without doing an IO request","Use dojo.undo.browser.addToHistory() instead.","0.4");
dojo.undo.browser.addToHistory(_25c);
return true;
}
}
var url=_25c.url;
var _25e="";
if(_25c["formNode"]){
var ta=_25c.formNode.getAttribute("action");
if((ta)&&(!_25c["url"])){
url=ta;
}
var tp=_25c.formNode.getAttribute("method");
if((tp)&&(!_25c["method"])){
_25c.method=tp;
}
_25e+=dojo.io.encodeForm(_25c.formNode,_25c.encoding,_25c["formFilter"]);
}
if(url.indexOf("#")>-1){
dojo.debug("Warning: dojo.io.bind: stripping hash values from url:",url);
url=url.split("#")[0];
}
if(_25c["file"]){
_25c.method="post";
}
if(!_25c["method"]){
_25c.method="get";
}
if(_25c.method.toLowerCase()=="get"){
_25c.multipart=false;
}else{
if(_25c["file"]){
_25c.multipart=true;
}else{
if(!_25c["multipart"]){
_25c.multipart=false;
}
}
}
if(_25c["backButton"]||_25c["back"]||_25c["changeUrl"]){
dojo.undo.browser.addToHistory(_25c);
}
var _261=_25c["content"]||{};
if(_25c.sendTransport){
_261["dojo.transport"]="xmlhttp";
}
do{
if(_25c.postContent){
_25e=_25c.postContent;
break;
}
if(_261){
_25e+=dojo.io.argsFromMap(_261,_25c.encoding);
}
if(_25c.method.toLowerCase()=="get"||!_25c.multipart){
break;
}
var t=[];
if(_25e.length){
var q=_25e.split("&");
for(var i=0;i<q.length;++i){
if(q[i].length){
var p=q[i].split("=");
t.push("--"+this.multipartBoundary,"Content-Disposition: form-data; name=\""+p[0]+"\"","",p[1]);
}
}
}
if(_25c.file){
if(dojo.lang.isArray(_25c.file)){
for(var i=0;i<_25c.file.length;++i){
var o=_25c.file[i];
t.push("--"+this.multipartBoundary,"Content-Disposition: form-data; name=\""+o.name+"\"; filename=\""+("fileName" in o?o.fileName:o.name)+"\"","Content-Type: "+("contentType" in o?o.contentType:"application/octet-stream"),"",o.content);
}
}else{
var o=_25c.file;
t.push("--"+this.multipartBoundary,"Content-Disposition: form-data; name=\""+o.name+"\"; filename=\""+("fileName" in o?o.fileName:o.name)+"\"","Content-Type: "+("contentType" in o?o.contentType:"application/octet-stream"),"",o.content);
}
}
if(t.length){
t.push("--"+this.multipartBoundary+"--","");
_25e=t.join("\r\n");
}
}while(false);
var _267=_25c["sync"]?false:true;
var _268=_25c["preventCache"]||(this.preventCache==true&&_25c["preventCache"]!=false);
var _269=_25c["useCache"]==true||(this.useCache==true&&_25c["useCache"]!=false);
if(!_268&&_269){
var _26a=getFromCache(url,_25e,_25c.method);
if(_26a){
doLoad(_25c,_26a,url,_25e,false);
return;
}
}
var http=dojo.hostenv.getXmlhttpObject(_25c);
var _26c=false;
if(_267){
var _26d=this.inFlight.push({"req":_25c,"http":http,"url":url,"query":_25e,"useCache":_269,"startTime":_25c.timeoutSeconds?(new Date()).getTime():0});
this.startWatchingInFlight();
}
if(_25c.method.toLowerCase()=="post"){
http.open("POST",url,_267);
setHeaders(http,_25c);
http.setRequestHeader("Content-Type",_25c.multipart?("multipart/form-data; boundary="+this.multipartBoundary):(_25c.contentType||"application/x-www-form-urlencoded"));
try{
http.send(_25e);
}
catch(e){
if(typeof http.abort=="function"){
http.abort();
}
doLoad(_25c,{status:404},url,_25e,_269);
}
}else{
var _26e=url;
if(_25e!=""){
_26e+=(_26e.indexOf("?")>-1?"&":"?")+_25e;
}
if(_268){
_26e+=(dojo.string.endsWithAny(_26e,"?","&")?"":(_26e.indexOf("?")>-1?"&":"?"))+"dojo.preventCache="+new Date().valueOf();
}
http.open(_25c.method.toUpperCase(),_26e,_267);
setHeaders(http,_25c);
try{
http.send(null);
}
catch(e){
if(typeof http.abort=="function"){
http.abort();
}
doLoad(_25c,{status:404},url,_25e,_269);
}
}
if(!_267){
doLoad(_25c,http,url,_25e,_269);
}
_25c.abort=function(){
return http.abort();
};
return;
};
dojo.io.transports.addTransport("XMLHTTPTransport");
};
dojo.provide("dojo.io.cookie");
dojo.io.cookie.setCookie=function(name,_270,days,path,_273,_274){
var _275=-1;
if(typeof days=="number"&&days>=0){
var d=new Date();
d.setTime(d.getTime()+(days*24*60*60*1000));
_275=d.toGMTString();
}
_270=escape(_270);
document.cookie=name+"="+_270+";"+(_275!=-1?" expires="+_275+";":"")+(path?"path="+path:"")+(_273?"; domain="+_273:"")+(_274?"; secure":"");
};
dojo.io.cookie.set=dojo.io.cookie.setCookie;
dojo.io.cookie.getCookie=function(name){
var idx=document.cookie.lastIndexOf(name+"=");
if(idx==-1){
return null;
}
var _279=document.cookie.substring(idx+name.length+1);
var end=_279.indexOf(";");
if(end==-1){
end=_279.length;
}
_279=_279.substring(0,end);
_279=unescape(_279);
return _279;
};
dojo.io.cookie.get=dojo.io.cookie.getCookie;
dojo.io.cookie.deleteCookie=function(name){
dojo.io.cookie.setCookie(name,"-",0);
};
dojo.io.cookie.setObjectCookie=function(name,obj,days,path,_280,_281,_282){
if(arguments.length==5){
_282=_280;
_280=null;
_281=null;
}
var _283=[],cookie,value="";
if(!_282){
cookie=dojo.io.cookie.getObjectCookie(name);
}
if(days>=0){
if(!cookie){
cookie={};
}
for(var prop in obj){
if(prop==null){
delete cookie[prop];
}else{
if(typeof obj[prop]=="string"||typeof obj[prop]=="number"){
cookie[prop]=obj[prop];
}
}
}
prop=null;
for(var prop in cookie){
_283.push(escape(prop)+"="+escape(cookie[prop]));
}
value=_283.join("&");
}
dojo.io.cookie.setCookie(name,value,days,path,_280,_281);
};
dojo.io.cookie.getObjectCookie=function(name){
var _286=null,cookie=dojo.io.cookie.getCookie(name);
if(cookie){
_286={};
var _287=cookie.split("&");
for(var i=0;i<_287.length;i++){
var pair=_287[i].split("=");
var _28a=pair[1];
if(isNaN(_28a)){
_28a=unescape(pair[1]);
}
_286[unescape(pair[0])]=_28a;
}
}
return _286;
};
dojo.io.cookie.isSupported=function(){
if(typeof navigator.cookieEnabled!="boolean"){
dojo.io.cookie.setCookie("__TestingYourBrowserForCookieSupport__","CookiesAllowed",90,null);
var _28b=dojo.io.cookie.getCookie("__TestingYourBrowserForCookieSupport__");
navigator.cookieEnabled=(_28b=="CookiesAllowed");
if(navigator.cookieEnabled){
this.deleteCookie("__TestingYourBrowserForCookieSupport__");
}
}
return navigator.cookieEnabled;
};
if(!dojo.io.cookies){
dojo.io.cookies=dojo.io.cookie;
}
dojo.provide("dojo.date");
dojo.date.setDayOfYear=function(_28c,_28d){
_28c.setMonth(0);
_28c.setDate(_28d);
return _28c;
};
dojo.date.getDayOfYear=function(_28e){
var _28f=new Date(_28e.getFullYear(),0,1);
return Math.floor((_28e.getTime()-_28f.getTime())/86400000);
};
dojo.date.setWeekOfYear=function(_290,week,_292){
if(arguments.length==1){
_292=0;
}
dojo.unimplemented("dojo.date.setWeekOfYear");
};
dojo.date.getWeekOfYear=function(_293,_294){
if(arguments.length==1){
_294=0;
}
var _295=new Date(_293.getFullYear(),0,1);
var day=_295.getDay();
_295.setDate(_295.getDate()-day+_294-(day>_294?7:0));
return Math.floor((_293.getTime()-_295.getTime())/604800000);
};
dojo.date.setIsoWeekOfYear=function(_297,week,_299){
if(arguments.length==1){
_299=1;
}
dojo.unimplemented("dojo.date.setIsoWeekOfYear");
};
dojo.date.getIsoWeekOfYear=function(_29a,_29b){
if(arguments.length==1){
_29b=1;
}
dojo.unimplemented("dojo.date.getIsoWeekOfYear");
};
dojo.date.setIso8601=function(_29c,_29d){
var _29e=(_29d.indexOf("T")==-1)?_29d.split(" "):_29d.split("T");
dojo.date.setIso8601Date(_29c,_29e[0]);
if(_29e.length==2){
dojo.date.setIso8601Time(_29c,_29e[1]);
}
return _29c;
};
dojo.date.fromIso8601=function(_29f){
return dojo.date.setIso8601(new Date(0,0),_29f);
};
dojo.date.setIso8601Date=function(_2a0,_2a1){
var _2a2="^([0-9]{4})((-?([0-9]{2})(-?([0-9]{2}))?)|"+"(-?([0-9]{3}))|(-?W([0-9]{2})(-?([1-7]))?))?$";
var d=_2a1.match(new RegExp(_2a2));
if(!d){
dojo.debug("invalid date string: "+_2a1);
return false;
}
var year=d[1];
var _2a5=d[4];
var date=d[6];
var _2a7=d[8];
var week=d[10];
var _2a9=(d[12])?d[12]:1;
_2a0.setYear(year);
if(_2a7){
dojo.date.setDayOfYear(_2a0,Number(_2a7));
}else{
if(week){
_2a0.setMonth(0);
_2a0.setDate(1);
var gd=_2a0.getDay();
var day=(gd)?gd:7;
var _2ac=Number(_2a9)+(7*Number(week));
if(day<=4){
_2a0.setDate(_2ac+1-day);
}else{
_2a0.setDate(_2ac+8-day);
}
}else{
if(_2a5){
_2a0.setDate(1);
_2a0.setMonth(_2a5-1);
}
if(date){
_2a0.setDate(date);
}
}
}
return _2a0;
};
dojo.date.fromIso8601Date=function(_2ad){
return dojo.date.setIso8601Date(new Date(0,0),_2ad);
};
dojo.date.setIso8601Time=function(_2ae,_2af){
var _2b0="Z|(([-+])([0-9]{2})(:?([0-9]{2}))?)$";
var d=_2af.match(new RegExp(_2b0));
var _2b2=0;
if(d){
if(d[0]!="Z"){
_2b2=(Number(d[3])*60)+Number(d[5]);
_2b2*=((d[2]=="-")?1:-1);
}
_2b2-=_2ae.getTimezoneOffset();
_2af=_2af.substr(0,_2af.length-d[0].length);
}
var _2b3="^([0-9]{2})(:?([0-9]{2})(:?([0-9]{2})(.([0-9]+))?)?)?$";
var d=_2af.match(new RegExp(_2b3));
if(!d){
dojo.debug("invalid time string: "+_2af);
return false;
}
var _2b4=d[1];
var mins=Number((d[3])?d[3]:0);
var secs=(d[5])?d[5]:0;
var ms=d[7]?(Number("0."+d[7])*1000):0;
_2ae.setHours(_2b4);
_2ae.setMinutes(mins);
_2ae.setSeconds(secs);
_2ae.setMilliseconds(ms);
if(_2b2!=0){
_2ae.setTime(_2ae.getTime()+_2b2*60000);
}
return _2ae;
};
dojo.date.fromIso8601Time=function(_2b8){
return dojo.date.setIso8601Time(new Date(0,0),_2b8);
};
dojo.date.shortTimezones=["IDLW","BET","HST","MART","AKST","PST","MST","CST","EST","AST","NFT","BST","FST","AT","GMT","CET","EET","MSK","IRT","GST","AFT","AGTT","IST","NPT","ALMT","MMT","JT","AWST","JST","ACST","AEST","LHST","VUT","NFT","NZT","CHAST","PHOT","LINT"];
dojo.date.timezoneOffsets=[-720,-660,-600,-570,-540,-480,-420,-360,-300,-240,-210,-180,-120,-60,0,60,120,180,210,240,270,300,330,345,360,390,420,480,540,570,600,630,660,690,720,765,780,840];
dojo.date.months=["January","February","March","April","May","June","July","August","September","October","November","December"];
dojo.date.shortMonths=["Jan","Feb","Mar","Apr","May","June","July","Aug","Sep","Oct","Nov","Dec"];
dojo.date.days=["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
dojo.date.shortDays=["Sun","Mon","Tues","Wed","Thur","Fri","Sat"];
dojo.date.getDaysInMonth=function(_2b9){
var _2ba=_2b9.getMonth();
var days=[31,28,31,30,31,30,31,31,30,31,30,31];
if(_2ba==1&&dojo.date.isLeapYear(_2b9)){
return 29;
}else{
return days[_2ba];
}
};
dojo.date.isLeapYear=function(_2bc){
var year=_2bc.getFullYear();
return (year%400==0)?true:(year%100==0)?false:(year%4==0)?true:false;
};
dojo.date.getDayName=function(_2be){
return dojo.date.days[_2be.getDay()];
};
dojo.date.getDayShortName=function(_2bf){
return dojo.date.shortDays[_2bf.getDay()];
};
dojo.date.getMonthName=function(_2c0){
return dojo.date.months[_2c0.getMonth()];
};
dojo.date.getMonthShortName=function(_2c1){
return dojo.date.shortMonths[_2c1.getMonth()];
};
dojo.date.getTimezoneName=function(_2c2){
var _2c3=-(_2c2.getTimezoneOffset());
for(var i=0;i<dojo.date.timezoneOffsets.length;i++){
if(dojo.date.timezoneOffsets[i]==_2c3){
return dojo.date.shortTimezones[i];
}
}
function $(s){
s=String(s);
while(s.length<2){
s="0"+s;
}
return s;
}
return (_2c3<0?"-":"+")+$(Math.floor(Math.abs(_2c3)/60))+":"+$(Math.abs(_2c3)%60);
};
dojo.date.getOrdinal=function(_2c6){
var date=_2c6.getDate();
if(date%100!=11&&date%10==1){
return "st";
}else{
if(date%100!=12&&date%10==2){
return "nd";
}else{
if(date%100!=13&&date%10==3){
return "rd";
}else{
return "th";
}
}
}
};
dojo.date.format=dojo.date.strftime=function(_2c8,_2c9){
var _2ca=null;
function _(s,n){
s=String(s);
n=(n||2)-s.length;
while(n-->0){
s=(_2ca==null?"0":_2ca)+s;
}
return s;
}
function $(_2cd){
switch(_2cd){
case "a":
return dojo.date.getDayShortName(_2c8);
break;
case "A":
return dojo.date.getDayName(_2c8);
break;
case "b":
case "h":
return dojo.date.getMonthShortName(_2c8);
break;
case "B":
return dojo.date.getMonthName(_2c8);
break;
case "c":
return _2c8.toLocaleString();
break;
case "C":
return _(Math.floor(_2c8.getFullYear()/100));
break;
case "d":
return _(_2c8.getDate());
break;
case "D":
return $("m")+"/"+$("d")+"/"+$("y");
break;
case "e":
if(_2ca==null){
_2ca=" ";
}
return _(_2c8.getDate(),2);
break;
case "g":
break;
case "G":
break;
case "F":
return $("Y")+"-"+$("m")+"-"+$("d");
break;
case "H":
return _(_2c8.getHours());
break;
case "I":
return _(_2c8.getHours()%12||12);
break;
case "j":
return _(dojo.date.getDayOfYear(_2c8),3);
break;
case "m":
return _(_2c8.getMonth()+1);
break;
case "M":
return _(_2c8.getMinutes());
break;
case "n":
return "\n";
break;
case "p":
return _2c8.getHours()<12?"am":"pm";
break;
case "r":
return $("I")+":"+$("M")+":"+$("S")+" "+$("p");
break;
case "R":
return $("H")+":"+$("M");
break;
case "S":
return _(_2c8.getSeconds());
break;
case "t":
return "\t";
break;
case "T":
return $("H")+":"+$("M")+":"+$("S");
break;
case "u":
return String(_2c8.getDay()||7);
break;
case "U":
return _(dojo.date.getWeekOfYear(_2c8));
break;
case "V":
return _(dojo.date.getIsoWeekOfYear(_2c8));
break;
case "W":
return _(dojo.date.getWeekOfYear(_2c8,1));
break;
case "w":
return String(_2c8.getDay());
break;
case "x":
break;
case "X":
break;
case "y":
return _(_2c8.getFullYear()%100);
break;
case "Y":
return String(_2c8.getFullYear());
break;
case "z":
var _2ce=_2c8.getTimezoneOffset();
return (_2ce>0?"-":"+")+_(Math.floor(Math.abs(_2ce)/60))+":"+_(Math.abs(_2ce)%60);
break;
case "Z":
return dojo.date.getTimezoneName(_2c8);
break;
case "%":
return "%";
break;
}
}
var _2cf="";
var i=0,index=0,switchCase;
while((index=_2c9.indexOf("%",i))!=-1){
_2cf+=_2c9.substring(i,index++);
switch(_2c9.charAt(index++)){
case "_":
_2ca=" ";
break;
case "-":
_2ca="";
break;
case "0":
_2ca="0";
break;
case "^":
switchCase="upper";
break;
case "#":
switchCase="swap";
break;
default:
_2ca=null;
index--;
break;
}
var _2d1=$(_2c9.charAt(index++));
if(switchCase=="upper"||(switchCase=="swap"&&/[a-z]/.test(_2d1))){
_2d1=_2d1.toUpperCase();
}else{
if(switchCase=="swap"&&!/[a-z]/.test(_2d1)){
_2d1=_2d1.toLowerCase();
}
}
var _2d2=null;
_2cf+=_2d1;
i=index;
}
_2cf+=_2c9.substring(i);
return _2cf;
};
dojo.date.compareTypes={DATE:1,TIME:2};
dojo.date.compare=function(_2d3,_2d4,_2d5){
var dA=_2d3;
var dB=_2d4||new Date();
var now=new Date();
var opt=_2d5||(dojo.date.compareTypes.DATE|dojo.date.compareTypes.TIME);
var d1=new Date(((opt&dojo.date.compareTypes.DATE)?(dA.getFullYear()):now.getFullYear()),((opt&dojo.date.compareTypes.DATE)?(dA.getMonth()):now.getMonth()),((opt&dojo.date.compareTypes.DATE)?(dA.getDate()):now.getDate()),((opt&dojo.date.compareTypes.TIME)?(dA.getHours()):0),((opt&dojo.date.compareTypes.TIME)?(dA.getMinutes()):0),((opt&dojo.date.compareTypes.TIME)?(dA.getSeconds()):0));
var d2=new Date(((opt&dojo.date.compareTypes.DATE)?(dB.getFullYear()):now.getFullYear()),((opt&dojo.date.compareTypes.DATE)?(dB.getMonth()):now.getMonth()),((opt&dojo.date.compareTypes.DATE)?(dB.getDate()):now.getDate()),((opt&dojo.date.compareTypes.TIME)?(dB.getHours()):0),((opt&dojo.date.compareTypes.TIME)?(dB.getMinutes()):0),((opt&dojo.date.compareTypes.TIME)?(dB.getSeconds()):0));
if(d1.valueOf()>d2.valueOf()){
return 1;
}
if(d1.valueOf()<d2.valueOf()){
return -1;
}
return 0;
};
dojo.date.dateParts={YEAR:0,MONTH:1,DAY:2,HOUR:3,MINUTE:4,SECOND:5,MILLISECOND:6};
dojo.date.add=function(d,unit,_2de){
var n=(_2de)?_2de:1;
var v;
switch(unit){
case dojo.date.dateParts.YEAR:
v=new Date(d.getFullYear()+n,d.getMonth(),d.getDate(),d.getHours(),d.getMinutes(),d.getSeconds(),d.getMilliseconds());
break;
case dojo.date.dateParts.MONTH:
v=new Date(d.getFullYear(),d.getMonth()+n,d.getDate(),d.getHours(),d.getMinutes(),d.getSeconds(),d.getMilliseconds());
break;
case dojo.date.dateParts.HOUR:
v=new Date(d.getFullYear(),d.getMonth(),d.getDate(),d.getHours()+n,d.getMinutes(),d.getSeconds(),d.getMilliseconds());
break;
case dojo.date.dateParts.MINUTE:
v=new Date(d.getFullYear(),d.getMonth(),d.getDate(),d.getHours(),d.getMinutes()+n,d.getSeconds(),d.getMilliseconds());
break;
case dojo.date.dateParts.SECOND:
v=new Date(d.getFullYear(),d.getMonth(),d.getDate(),d.getHours(),d.getMinutes(),d.getSeconds()+n,d.getMilliseconds());
break;
case dojo.date.dateParts.MILLISECOND:
v=new Date(d.getFullYear(),d.getMonth(),d.getDate(),d.getHours(),d.getMinutes(),d.getSeconds(),d.getMilliseconds()+n);
break;
default:
v=new Date(d.getFullYear(),d.getMonth(),d.getDate()+n,d.getHours(),d.getMinutes(),d.getSeconds(),d.getMilliseconds());
}
return v;
};
dojo.date.toString=function(date,_2e2){
dojo.deprecated("dojo.date.toString","use dojo.date.format instead","0.4");
if(_2e2.indexOf("#d")>-1){
_2e2=_2e2.replace(/#dddd/g,dojo.date.getDayOfWeekName(date));
_2e2=_2e2.replace(/#ddd/g,dojo.date.getShortDayOfWeekName(date));
_2e2=_2e2.replace(/#dd/g,(date.getDate().toString().length==1?"0":"")+date.getDate());
_2e2=_2e2.replace(/#d/g,date.getDate());
}
if(_2e2.indexOf("#M")>-1){
_2e2=_2e2.replace(/#MMMM/g,dojo.date.getMonthName(date));
_2e2=_2e2.replace(/#MMM/g,dojo.date.getShortMonthName(date));
_2e2=_2e2.replace(/#MM/g,((date.getMonth()+1).toString().length==1?"0":"")+(date.getMonth()+1));
_2e2=_2e2.replace(/#M/g,date.getMonth()+1);
}
if(_2e2.indexOf("#y")>-1){
var _2e3=date.getFullYear().toString();
_2e2=_2e2.replace(/#yyyy/g,_2e3);
_2e2=_2e2.replace(/#yy/g,_2e3.substring(2));
_2e2=_2e2.replace(/#y/g,_2e3.substring(3));
}
if(_2e2.indexOf("#")==-1){
return _2e2;
}
if(_2e2.indexOf("#h")>-1){
var _2e4=date.getHours();
_2e4=(_2e4>12?_2e4-12:(_2e4==0)?12:_2e4);
_2e2=_2e2.replace(/#hh/g,(_2e4.toString().length==1?"0":"")+_2e4);
_2e2=_2e2.replace(/#h/g,_2e4);
}
if(_2e2.indexOf("#H")>-1){
_2e2=_2e2.replace(/#HH/g,(date.getHours().toString().length==1?"0":"")+date.getHours());
_2e2=_2e2.replace(/#H/g,date.getHours());
}
if(_2e2.indexOf("#m")>-1){
_2e2=_2e2.replace(/#mm/g,(date.getMinutes().toString().length==1?"0":"")+date.getMinutes());
_2e2=_2e2.replace(/#m/g,date.getMinutes());
}
if(_2e2.indexOf("#s")>-1){
_2e2=_2e2.replace(/#ss/g,(date.getSeconds().toString().length==1?"0":"")+date.getSeconds());
_2e2=_2e2.replace(/#s/g,date.getSeconds());
}
if(_2e2.indexOf("#T")>-1){
_2e2=_2e2.replace(/#TT/g,date.getHours()>=12?"PM":"AM");
_2e2=_2e2.replace(/#T/g,date.getHours()>=12?"P":"A");
}
if(_2e2.indexOf("#t")>-1){
_2e2=_2e2.replace(/#tt/g,date.getHours()>=12?"pm":"am");
_2e2=_2e2.replace(/#t/g,date.getHours()>=12?"p":"a");
}
return _2e2;
};
dojo.date.daysInMonth=function(_2e5,year){
dojo.deprecated("daysInMonth(month, year)","replaced by getDaysInMonth(dateObject)","0.4");
return dojo.date.getDaysInMonth(new Date(year,_2e5,1));
};
dojo.date.toLongDateString=function(date){
dojo.deprecated("dojo.date.toLongDateString","use dojo.date.format(date, \"%B %e, %Y\") instead","0.4");
return dojo.date.format(date,"%B %e, %Y");
};
dojo.date.toShortDateString=function(date){
dojo.deprecated("dojo.date.toShortDateString","use dojo.date.format(date, \"%b %e, %Y\") instead","0.4");
return dojo.date.format(date,"%b %e, %Y");
};
dojo.date.toMilitaryTimeString=function(date){
dojo.deprecated("dojo.date.toMilitaryTimeString","use dojo.date.format(date, \"%T\")","0.4");
return dojo.date.format(date,"%T");
};
dojo.date.toRelativeString=function(date){
var now=new Date();
var diff=(now-date)/1000;
var end=" ago";
var _2ee=false;
if(diff<0){
_2ee=true;
end=" from now";
diff=-diff;
}
if(diff<60){
diff=Math.round(diff);
return diff+" second"+(diff==1?"":"s")+end;
}else{
if(diff<3600){
diff=Math.round(diff/60);
return diff+" minute"+(diff==1?"":"s")+end;
}else{
if(diff<3600*24){
diff=Math.round(diff/3600);
return diff+" hour"+(diff==1?"":"s")+end;
}else{
if(diff<3600*24*7){
diff=Math.round(diff/(3600*24));
if(diff==1){
return _2ee?"Tomorrow":"Yesterday";
}else{
return diff+" days"+end;
}
}else{
return dojo.date.toShortDateString(date);
}
}
}
}
};
dojo.date.getDayOfWeekName=function(date){
dojo.deprecated("dojo.date.getDayOfWeekName","use dojo.date.getDayName instead","0.4");
return dojo.date.days[date.getDay()];
};
dojo.date.getShortDayOfWeekName=function(date){
dojo.deprecated("dojo.date.getShortDayOfWeekName","use dojo.date.getDayShortName instead","0.4");
return dojo.date.shortDays[date.getDay()];
};
dojo.date.getShortMonthName=function(date){
dojo.deprecated("dojo.date.getShortMonthName","use dojo.date.getMonthShortName instead","0.4");
return dojo.date.shortMonths[date.getMonth()];
};
dojo.date.toSql=function(date,_2f3){
return dojo.date.format(date,"%F"+!_2f3?" %T":"");
};
dojo.date.fromSql=function(_2f4){
var _2f5=_2f4.split(/[\- :]/g);
while(_2f5.length<6){
_2f5.push(0);
}
return new Date(_2f5[0],(parseInt(_2f5[1],10)-1),_2f5[2],_2f5[3],_2f5[4],_2f5[5]);
};
dojo.provide("dojo.xml.Parse");
dojo.require("dojo.dom");
dojo.xml.Parse=function(){
function getDojoTagName(node){
var _2f7=node.tagName;
if(_2f7.substr(0,5).toLowerCase()!="dojo:"){
if(_2f7.substr(0,4).toLowerCase()=="dojo"){
return "dojo:"+_2f7.substring(4).toLowerCase();
}
var djt=node.getAttribute("dojoType")||node.getAttribute("dojotype");
if(djt){
return "dojo:"+djt.toLowerCase();
}
if(node.getAttributeNS&&node.getAttributeNS(dojo.dom.dojoml,"type")){
return "dojo:"+node.getAttributeNS(dojo.dom.dojoml,"type").toLowerCase();
}
try{
djt=node.getAttribute("dojo:type");
}
catch(e){
}
if(djt){
return "dojo:"+djt.toLowerCase();
}
if(!dj_global["djConfig"]||!djConfig["ignoreClassNames"]){
var _2f9=node.className||node.getAttribute("class");
if(_2f9&&_2f9.indexOf&&_2f9.indexOf("dojo-")!=-1){
var _2fa=_2f9.split(" ");
for(var x=0;x<_2fa.length;x++){
if(_2fa[x].length>5&&_2fa[x].indexOf("dojo-")>=0){
return "dojo:"+_2fa[x].substr(5).toLowerCase();
}
}
}
}
}
return _2f7.toLowerCase();
}
this.parseElement=function(node,_2fd,_2fe,_2ff){
if(node.getAttribute("parseWidgets")=="false"){
return {};
}
var _300={};
var _301=getDojoTagName(node);
_300[_301]=[];
if((!_2fe)||(_301.substr(0,4).toLowerCase()=="dojo")){
var _302=parseAttributes(node);
for(var attr in _302){
if((!_300[_301][attr])||(typeof _300[_301][attr]!="array")){
_300[_301][attr]=[];
}
_300[_301][attr].push(_302[attr]);
}
_300[_301].nodeRef=node;
_300.tagName=_301;
_300.index=_2ff||0;
}
var _304=0;
var tcn,i=0,nodes=node.childNodes;
while(tcn=nodes[i++]){
switch(tcn.nodeType){
case dojo.dom.ELEMENT_NODE:
_304++;
var ctn=getDojoTagName(tcn);
if(!_300[ctn]){
_300[ctn]=[];
}
_300[ctn].push(this.parseElement(tcn,true,_2fe,_304));
if((tcn.childNodes.length==1)&&(tcn.childNodes.item(0).nodeType==dojo.dom.TEXT_NODE)){
_300[ctn][_300[ctn].length-1].value=tcn.childNodes.item(0).nodeValue;
}
break;
case dojo.dom.TEXT_NODE:
if(node.childNodes.length==1){
_300[_301].push({value:node.childNodes.item(0).nodeValue});
}
break;
default:
break;
}
}
return _300;
};
function parseAttributes(node){
var _308={};
var atts=node.attributes;
var _30a,i=0;
while(_30a=atts[i++]){
if((dojo.render.html.capable)&&(dojo.render.html.ie)){
if(!_30a){
continue;
}
if((typeof _30a=="object")&&(typeof _30a.nodeValue=="undefined")||(_30a.nodeValue==null)||(_30a.nodeValue=="")){
continue;
}
}
var nn=(_30a.nodeName.indexOf("dojo:")==-1)?_30a.nodeName:_30a.nodeName.split("dojo:")[1];
_308[nn]={value:_30a.nodeValue};
}
return _308;
}
};
dojo.provide("dojo.lang.declare");
dojo.require("dojo.lang.common");
dojo.require("dojo.lang.extras");
dojo.lang.declare=function(_30c,_30d,init,_30f){
if((dojo.lang.isFunction(_30f))||((!_30f)&&(!dojo.lang.isFunction(init)))){
var temp=_30f;
_30f=init;
init=temp;
}
var _311=[];
if(dojo.lang.isArray(_30d)){
_311=_30d;
_30d=_311.shift();
}
if(!init){
init=dojo.evalObjPath(_30c,false);
if((init)&&(!dojo.lang.isFunction(init))){
init=null;
}
}
var ctor=dojo.lang.declare._makeConstructor();
var scp=(_30d?_30d.prototype:null);
if(scp){
scp.prototyping=true;
ctor.prototype=new _30d();
scp.prototyping=false;
}
ctor.superclass=scp;
ctor.mixins=_311;
for(var i=0,l=_311.length;i<l;i++){
dojo.lang.extend(ctor,_311[i].prototype);
}
ctor.prototype.initializer=null;
ctor.prototype.declaredClass=_30c;
if(dojo.lang.isArray(_30f)){
dojo.lang.extend.apply(dojo.lang,[ctor].concat(_30f));
}else{
dojo.lang.extend(ctor,(_30f)||{});
}
dojo.lang.extend(ctor,dojo.lang.declare.base);
ctor.prototype.constructor=ctor;
ctor.prototype.initializer=(ctor.prototype.initializer)||(init)||(function(){
});
dojo.lang.setObjPathValue(_30c,ctor,null,true);
};
dojo.lang.declare._makeConstructor=function(){
return function(){
var self=this._getPropContext();
var s=self.constructor.superclass;
if((s)&&(s.constructor)){
if(s.constructor==arguments.callee){
this.inherited("constructor",arguments);
}else{
this._inherited(s,"constructor",arguments);
}
}
var m=(self.constructor.mixins)||([]);
for(var i=0,l=m.length;i<l;i++){
(((m[i].prototype)&&(m[i].prototype.initializer))||(m[i])).apply(this,arguments);
}
if((!this.prototyping)&&(self.initializer)){
self.initializer.apply(this,arguments);
}
};
};
dojo.lang.declare.base={_getPropContext:function(){
return (this.___proto||this);
},_inherited:function(_319,_31a,args){
var _31c=this.___proto;
this.___proto=_319;
var _31d=_319[_31a].apply(this,(args||[]));
this.___proto=_31c;
return _31d;
},inheritedFrom:function(ctor,prop,args){
var p=((ctor)&&(ctor.prototype)&&(ctor.prototype[prop]));
return (dojo.lang.isFunction(p)?p.apply(this,(args||[])):p);
},inherited:function(prop,args){
var p=this._getPropContext();
do{
if((!p.constructor)||(!p.constructor.superclass)){
return;
}
p=p.constructor.superclass;
}while(!(prop in p));
return (dojo.lang.isFunction(p[prop])?this._inherited(p,prop,args):p[prop]);
}};
dojo.declare=dojo.lang.declare;
dojo.provide("dojo.event");
dojo.require("dojo.lang.array");
dojo.require("dojo.lang.extras");
dojo.require("dojo.lang.func");
dojo.event=new function(){
this.canTimeout=dojo.lang.isFunction(dj_global["setTimeout"])||dojo.lang.isAlien(dj_global["setTimeout"]);
function interpolateArgs(args,_326){
var dl=dojo.lang;
var ao={srcObj:dj_global,srcFunc:null,adviceObj:dj_global,adviceFunc:null,aroundObj:null,aroundFunc:null,adviceType:(args.length>2)?args[0]:"after",precedence:"last",once:false,delay:null,rate:0,adviceMsg:false};
switch(args.length){
case 0:
return;
case 1:
return;
case 2:
ao.srcFunc=args[0];
ao.adviceFunc=args[1];
break;
case 3:
if((dl.isObject(args[0]))&&(dl.isString(args[1]))&&(dl.isString(args[2]))){
ao.adviceType="after";
ao.srcObj=args[0];
ao.srcFunc=args[1];
ao.adviceFunc=args[2];
}else{
if((dl.isString(args[1]))&&(dl.isString(args[2]))){
ao.srcFunc=args[1];
ao.adviceFunc=args[2];
}else{
if((dl.isObject(args[0]))&&(dl.isString(args[1]))&&(dl.isFunction(args[2]))){
ao.adviceType="after";
ao.srcObj=args[0];
ao.srcFunc=args[1];
var _329=dl.nameAnonFunc(args[2],ao.adviceObj,_326);
ao.adviceFunc=_329;
}else{
if((dl.isFunction(args[0]))&&(dl.isObject(args[1]))&&(dl.isString(args[2]))){
ao.adviceType="after";
ao.srcObj=dj_global;
var _329=dl.nameAnonFunc(args[0],ao.srcObj,_326);
ao.srcFunc=_329;
ao.adviceObj=args[1];
ao.adviceFunc=args[2];
}
}
}
}
break;
case 4:
if((dl.isObject(args[0]))&&(dl.isObject(args[2]))){
ao.adviceType="after";
ao.srcObj=args[0];
ao.srcFunc=args[1];
ao.adviceObj=args[2];
ao.adviceFunc=args[3];
}else{
if((dl.isString(args[0]))&&(dl.isString(args[1]))&&(dl.isObject(args[2]))){
ao.adviceType=args[0];
ao.srcObj=dj_global;
ao.srcFunc=args[1];
ao.adviceObj=args[2];
ao.adviceFunc=args[3];
}else{
if((dl.isString(args[0]))&&(dl.isFunction(args[1]))&&(dl.isObject(args[2]))){
ao.adviceType=args[0];
ao.srcObj=dj_global;
var _329=dl.nameAnonFunc(args[1],dj_global,_326);
ao.srcFunc=_329;
ao.adviceObj=args[2];
ao.adviceFunc=args[3];
}else{
if((dl.isString(args[0]))&&(dl.isObject(args[1]))&&(dl.isString(args[2]))&&(dl.isFunction(args[3]))){
ao.srcObj=args[1];
ao.srcFunc=args[2];
var _329=dl.nameAnonFunc(args[3],dj_global,_326);
ao.adviceObj=dj_global;
ao.adviceFunc=_329;
}else{
if(dl.isObject(args[1])){
ao.srcObj=args[1];
ao.srcFunc=args[2];
ao.adviceObj=dj_global;
ao.adviceFunc=args[3];
}else{
if(dl.isObject(args[2])){
ao.srcObj=dj_global;
ao.srcFunc=args[1];
ao.adviceObj=args[2];
ao.adviceFunc=args[3];
}else{
ao.srcObj=ao.adviceObj=ao.aroundObj=dj_global;
ao.srcFunc=args[1];
ao.adviceFunc=args[2];
ao.aroundFunc=args[3];
}
}
}
}
}
}
break;
case 6:
ao.srcObj=args[1];
ao.srcFunc=args[2];
ao.adviceObj=args[3];
ao.adviceFunc=args[4];
ao.aroundFunc=args[5];
ao.aroundObj=dj_global;
break;
default:
ao.srcObj=args[1];
ao.srcFunc=args[2];
ao.adviceObj=args[3];
ao.adviceFunc=args[4];
ao.aroundObj=args[5];
ao.aroundFunc=args[6];
ao.once=args[7];
ao.delay=args[8];
ao.rate=args[9];
ao.adviceMsg=args[10];
break;
}
if(dl.isFunction(ao.aroundFunc)){
var _329=dl.nameAnonFunc(ao.aroundFunc,ao.aroundObj,_326);
ao.aroundFunc=_329;
}
if(dl.isFunction(ao.srcFunc)){
ao.srcFunc=dl.getNameInObj(ao.srcObj,ao.srcFunc);
}
if(dl.isFunction(ao.adviceFunc)){
ao.adviceFunc=dl.getNameInObj(ao.adviceObj,ao.adviceFunc);
}
if((ao.aroundObj)&&(dl.isFunction(ao.aroundFunc))){
ao.aroundFunc=dl.getNameInObj(ao.aroundObj,ao.aroundFunc);
}
if(!ao.srcObj){
dojo.raise("bad srcObj for srcFunc: "+ao.srcFunc);
}
if(!ao.adviceObj){
dojo.raise("bad adviceObj for adviceFunc: "+ao.adviceFunc);
}
return ao;
}
this.connect=function(){
if(arguments.length==1){
var ao=arguments[0];
}else{
var ao=interpolateArgs(arguments,true);
}
if(dojo.lang.isArray(ao.srcObj)&&ao.srcObj!=""){
var _32b={};
for(var x in ao){
_32b[x]=ao[x];
}
var mjps=[];
dojo.lang.forEach(ao.srcObj,function(src){
if((dojo.render.html.capable)&&(dojo.lang.isString(src))){
src=dojo.byId(src);
}
_32b.srcObj=src;
mjps.push(dojo.event.connect.call(dojo.event,_32b));
});
return mjps;
}
var mjp=dojo.event.MethodJoinPoint.getForMethod(ao.srcObj,ao.srcFunc);
if(ao.adviceFunc){
var mjp2=dojo.event.MethodJoinPoint.getForMethod(ao.adviceObj,ao.adviceFunc);
}
mjp.kwAddAdvice(ao);
return mjp;
};
this.log=function(a1,a2){
var _333;
if((arguments.length==1)&&(typeof a1=="object")){
_333=a1;
}else{
_333={srcObj:a1,srcFunc:a2};
}
_333.adviceFunc=function(){
var _334=[];
for(var x=0;x<arguments.length;x++){
_334.push(arguments[x]);
}
dojo.debug("("+_333.srcObj+")."+_333.srcFunc,":",_334.join(", "));
};
this.kwConnect(_333);
};
this.connectBefore=function(){
var args=["before"];
for(var i=0;i<arguments.length;i++){
args.push(arguments[i]);
}
return this.connect.apply(this,args);
};
this.connectAround=function(){
var args=["around"];
for(var i=0;i<arguments.length;i++){
args.push(arguments[i]);
}
return this.connect.apply(this,args);
};
this.connectOnce=function(){
var ao=interpolateArgs(arguments,true);
ao.once=true;
return this.connect(ao);
};
this._kwConnectImpl=function(_33b,_33c){
var fn=(_33c)?"disconnect":"connect";
if(typeof _33b["srcFunc"]=="function"){
_33b.srcObj=_33b["srcObj"]||dj_global;
var _33e=dojo.lang.nameAnonFunc(_33b.srcFunc,_33b.srcObj,true);
_33b.srcFunc=_33e;
}
if(typeof _33b["adviceFunc"]=="function"){
_33b.adviceObj=_33b["adviceObj"]||dj_global;
var _33e=dojo.lang.nameAnonFunc(_33b.adviceFunc,_33b.adviceObj,true);
_33b.adviceFunc=_33e;
}
return dojo.event[fn]((_33b["type"]||_33b["adviceType"]||"after"),_33b["srcObj"]||dj_global,_33b["srcFunc"],_33b["adviceObj"]||_33b["targetObj"]||dj_global,_33b["adviceFunc"]||_33b["targetFunc"],_33b["aroundObj"],_33b["aroundFunc"],_33b["once"],_33b["delay"],_33b["rate"],_33b["adviceMsg"]||false);
};
this.kwConnect=function(_33f){
return this._kwConnectImpl(_33f,false);
};
this.disconnect=function(){
var ao=interpolateArgs(arguments,true);
if(!ao.adviceFunc){
return;
}
var mjp=dojo.event.MethodJoinPoint.getForMethod(ao.srcObj,ao.srcFunc);
return mjp.removeAdvice(ao.adviceObj,ao.adviceFunc,ao.adviceType,ao.once);
};
this.kwDisconnect=function(_342){
return this._kwConnectImpl(_342,true);
};
};
dojo.event.MethodInvocation=function(_343,obj,args){
this.jp_=_343;
this.object=obj;
this.args=[];
for(var x=0;x<args.length;x++){
this.args[x]=args[x];
}
this.around_index=-1;
};
dojo.event.MethodInvocation.prototype.proceed=function(){
this.around_index++;
if(this.around_index>=this.jp_.around.length){
return this.jp_.object[this.jp_.methodname].apply(this.jp_.object,this.args);
}else{
var ti=this.jp_.around[this.around_index];
var mobj=ti[0]||dj_global;
var meth=ti[1];
return mobj[meth].call(mobj,this);
}
};
dojo.event.MethodJoinPoint=function(obj,_34b){
this.object=obj||dj_global;
this.methodname=_34b;
this.methodfunc=this.object[_34b];
this.before=[];
this.after=[];
this.around=[];
};
dojo.event.MethodJoinPoint.getForMethod=function(obj,_34d){
if(!obj){
obj=dj_global;
}
if(!obj[_34d]){
obj[_34d]=function(){
};
if(!obj[_34d]){
dojo.raise("Cannot set do-nothing method on that object "+_34d);
}
}else{
if((!dojo.lang.isFunction(obj[_34d]))&&(!dojo.lang.isAlien(obj[_34d]))){
return null;
}
}
var _34e=_34d+"$joinpoint";
var _34f=_34d+"$joinpoint$method";
var _350=obj[_34e];
if(!_350){
var _351=false;
if(dojo.event["browser"]){
if((obj["attachEvent"])||(obj["nodeType"])||(obj["addEventListener"])){
_351=true;
dojo.event.browser.addClobberNodeAttrs(obj,[_34e,_34f,_34d]);
}
}
var _352=obj[_34d].length;
obj[_34f]=obj[_34d];
_350=obj[_34e]=new dojo.event.MethodJoinPoint(obj,_34f);
obj[_34d]=function(){
var args=[];
if((_351)&&(!arguments.length)){
var evt=null;
try{
if(obj.ownerDocument){
evt=obj.ownerDocument.parentWindow.event;
}else{
if(obj.documentElement){
evt=obj.documentElement.ownerDocument.parentWindow.event;
}else{
evt=window.event;
}
}
}
catch(e){
evt=window.event;
}
if(evt){
args.push(dojo.event.browser.fixEvent(evt,this));
}
}else{
for(var x=0;x<arguments.length;x++){
if((x==0)&&(_351)&&(dojo.event.browser.isEvent(arguments[x]))){
args.push(dojo.event.browser.fixEvent(arguments[x],this));
}else{
args.push(arguments[x]);
}
}
}
return _350.run.apply(_350,args);
};
obj[_34d].__preJoinArity=_352;
}
return _350;
};
dojo.lang.extend(dojo.event.MethodJoinPoint,{unintercept:function(){
this.object[this.methodname]=this.methodfunc;
this.before=[];
this.after=[];
this.around=[];
},disconnect:dojo.lang.forward("unintercept"),run:function(){
var obj=this.object||dj_global;
var args=arguments;
var _358=[];
for(var x=0;x<args.length;x++){
_358[x]=args[x];
}
var _35a=function(marr){
if(!marr){
dojo.debug("Null argument to unrollAdvice()");
return;
}
var _35c=marr[0]||dj_global;
var _35d=marr[1];
if(!_35c[_35d]){
dojo.raise("function \""+_35d+"\" does not exist on \""+_35c+"\"");
}
var _35e=marr[2]||dj_global;
var _35f=marr[3];
var msg=marr[6];
var _361;
var to={args:[],jp_:this,object:obj,proceed:function(){
return _35c[_35d].apply(_35c,to.args);
}};
to.args=_358;
var _363=parseInt(marr[4]);
var _364=((!isNaN(_363))&&(marr[4]!==null)&&(typeof marr[4]!="undefined"));
if(marr[5]){
var rate=parseInt(marr[5]);
var cur=new Date();
var _367=false;
if((marr["last"])&&((cur-marr.last)<=rate)){
if(dojo.event.canTimeout){
if(marr["delayTimer"]){
clearTimeout(marr.delayTimer);
}
var tod=parseInt(rate*2);
var mcpy=dojo.lang.shallowCopy(marr);
marr.delayTimer=setTimeout(function(){
mcpy[5]=0;
_35a(mcpy);
},tod);
}
return;
}else{
marr.last=cur;
}
}
if(_35f){
_35e[_35f].call(_35e,to);
}else{
if((_364)&&((dojo.render.html)||(dojo.render.svg))){
dj_global["setTimeout"](function(){
if(msg){
_35c[_35d].call(_35c,to);
}else{
_35c[_35d].apply(_35c,args);
}
},_363);
}else{
if(msg){
_35c[_35d].call(_35c,to);
}else{
_35c[_35d].apply(_35c,args);
}
}
}
};
if(this.before.length>0){
dojo.lang.forEach(this.before,_35a);
}
var _36a;
if(this.around.length>0){
var mi=new dojo.event.MethodInvocation(this,obj,args);
_36a=mi.proceed();
}else{
if(this.methodfunc){
_36a=this.object[this.methodname].apply(this.object,args);
}
}
if(this.after.length>0){
dojo.lang.forEach(this.after,_35a);
}
return (this.methodfunc)?_36a:null;
},getArr:function(kind){
var arr=this.after;
if((typeof kind=="string")&&(kind.indexOf("before")!=-1)){
arr=this.before;
}else{
if(kind=="around"){
arr=this.around;
}
}
return arr;
},kwAddAdvice:function(args){
this.addAdvice(args["adviceObj"],args["adviceFunc"],args["aroundObj"],args["aroundFunc"],args["adviceType"],args["precedence"],args["once"],args["delay"],args["rate"],args["adviceMsg"]);
},addAdvice:function(_36f,_370,_371,_372,_373,_374,once,_376,rate,_378){
var arr=this.getArr(_373);
if(!arr){
dojo.raise("bad this: "+this);
}
var ao=[_36f,_370,_371,_372,_376,rate,_378];
if(once){
if(this.hasAdvice(_36f,_370,_373,arr)>=0){
return;
}
}
if(_374=="first"){
arr.unshift(ao);
}else{
arr.push(ao);
}
},hasAdvice:function(_37b,_37c,_37d,arr){
if(!arr){
arr=this.getArr(_37d);
}
var ind=-1;
for(var x=0;x<arr.length;x++){
var aao=(typeof _37c=="object")?(new String(_37c)).toString():_37c;
var a1o=(typeof arr[x][1]=="object")?(new String(arr[x][1])).toString():arr[x][1];
if((arr[x][0]==_37b)&&(a1o==aao)){
ind=x;
}
}
return ind;
},removeAdvice:function(_383,_384,_385,once){
var arr=this.getArr(_385);
var ind=this.hasAdvice(_383,_384,_385,arr);
if(ind==-1){
return false;
}
while(ind!=-1){
arr.splice(ind,1);
if(once){
break;
}
ind=this.hasAdvice(_383,_384,_385,arr);
}
return true;
}});
dojo.require("dojo.event");
dojo.provide("dojo.event.topic");
dojo.event.topic=new function(){
this.topics={};
this.getTopic=function(_389){
if(!this.topics[_389]){
this.topics[_389]=new this.TopicImpl(_389);
}
return this.topics[_389];
};
this.registerPublisher=function(_38a,obj,_38c){
var _38a=this.getTopic(_38a);
_38a.registerPublisher(obj,_38c);
};
this.subscribe=function(_38d,obj,_38f){
var _38d=this.getTopic(_38d);
_38d.subscribe(obj,_38f);
};
this.unsubscribe=function(_390,obj,_392){
var _390=this.getTopic(_390);
_390.unsubscribe(obj,_392);
};
this.destroy=function(_393){
this.getTopic(_393).destroy();
delete this.topics[_393];
};
this.publishApply=function(_394,args){
var _394=this.getTopic(_394);
_394.sendMessage.apply(_394,args);
};
this.publish=function(_396,_397){
var _396=this.getTopic(_396);
var args=[];
for(var x=1;x<arguments.length;x++){
args.push(arguments[x]);
}
_396.sendMessage.apply(_396,args);
};
};
dojo.event.topic.TopicImpl=function(_39a){
this.topicName=_39a;
this.subscribe=function(_39b,_39c){
var tf=_39c||_39b;
var to=(!_39c)?dj_global:_39b;
dojo.event.kwConnect({srcObj:this,srcFunc:"sendMessage",adviceObj:to,adviceFunc:tf});
};
this.unsubscribe=function(_39f,_3a0){
var tf=(!_3a0)?_39f:_3a0;
var to=(!_3a0)?null:_39f;
dojo.event.kwDisconnect({srcObj:this,srcFunc:"sendMessage",adviceObj:to,adviceFunc:tf});
};
this.destroy=function(){
dojo.event.MethodJoinPoint.getForMethod(this,"sendMessage").disconnect();
};
this.registerPublisher=function(_3a3,_3a4){
dojo.event.connect(_3a3,_3a4,this,"sendMessage");
};
this.sendMessage=function(_3a5){
};
};
dojo.provide("dojo.event.browser");
dojo.require("dojo.event");
dojo._ie_clobber=new function(){
this.clobberNodes=[];
function nukeProp(node,prop){
try{
node[prop]=null;
}
catch(e){
}
try{
delete node[prop];
}
catch(e){
}
try{
node.removeAttribute(prop);
}
catch(e){
}
}
this.clobber=function(_3a8){
var na;
var tna;
if(_3a8){
tna=_3a8.all||_3a8.getElementsByTagName("*");
na=[_3a8];
for(var x=0;x<tna.length;x++){
if(tna[x]["__doClobber__"]){
na.push(tna[x]);
}
}
}else{
try{
window.onload=null;
}
catch(e){
}
na=(this.clobberNodes.length)?this.clobberNodes:document.all;
}
tna=null;
var _3ac={};
for(var i=na.length-1;i>=0;i=i-1){
var el=na[i];
if(el["__clobberAttrs__"]){
for(var j=0;j<el.__clobberAttrs__.length;j++){
nukeProp(el,el.__clobberAttrs__[j]);
}
nukeProp(el,"__clobberAttrs__");
nukeProp(el,"__doClobber__");
}
}
na=null;
};
};
if(dojo.render.html.ie){
dojo.addOnUnload(function(){
dojo._ie_clobber.clobber();
try{
if((dojo["widget"])&&(dojo.widget["manager"])){
dojo.widget.manager.destroyAll();
}
}
catch(e){
}
try{
window.onload=null;
}
catch(e){
}
try{
window.onunload=null;
}
catch(e){
}
dojo._ie_clobber.clobberNodes=[];
});
}
dojo.event.browser=new function(){
var _3b0=0;
this.clean=function(node){
if(dojo.render.html.ie){
dojo._ie_clobber.clobber(node);
}
};
this.addClobberNode=function(node){
if(!dojo.render.html.ie){
return;
}
if(!node["__doClobber__"]){
node.__doClobber__=true;
dojo._ie_clobber.clobberNodes.push(node);
node.__clobberAttrs__=[];
}
};
this.addClobberNodeAttrs=function(node,_3b4){
if(!dojo.render.html.ie){
return;
}
this.addClobberNode(node);
for(var x=0;x<_3b4.length;x++){
node.__clobberAttrs__.push(_3b4[x]);
}
};
this.removeListener=function(node,_3b7,fp,_3b9){
if(!_3b9){
var _3b9=false;
}
_3b7=_3b7.toLowerCase();
if(_3b7.substr(0,2)=="on"){
_3b7=_3b7.substr(2);
}
if(node.removeEventListener){
node.removeEventListener(_3b7,fp,_3b9);
}
};
this.addListener=function(node,_3bb,fp,_3bd,_3be){
if(!node){
return;
}
if(!_3bd){
var _3bd=false;
}
_3bb=_3bb.toLowerCase();
if(_3bb.substr(0,2)!="on"){
_3bb="on"+_3bb;
}
var _3bf;
if(!_3be){
_3bf=function(evt){
if(!evt){
evt=window.event;
}
var ret=fp(dojo.event.browser.fixEvent(evt,this));
if(_3bd){
dojo.event.browser.stopEvent(evt);
}
return ret;
};
}else{
_3bf=fp;
}
if(node.addEventListener){
node.addEventListener(_3bb.substr(2),_3bf,_3bd);
return _3bf;
}else{
if(typeof node[_3bb]=="function"){
var _3c2=node[_3bb];
node[_3bb]=function(e){
_3c2(e);
return _3bf(e);
};
}else{
node[_3bb]=_3bf;
}
if(dojo.render.html.ie){
this.addClobberNodeAttrs(node,[_3bb]);
}
return _3bf;
}
};
this.isEvent=function(obj){
return (typeof obj!="undefined")&&(typeof Event!="undefined")&&(obj)&&(obj.eventPhase);
};
this.currentEvent=null;
this.callListener=function(_3c5,_3c6){
if(typeof _3c5!="function"){
dojo.raise("listener not a function: "+_3c5);
}
dojo.event.browser.currentEvent.currentTarget=_3c6;
return _3c5.call(_3c6,dojo.event.browser.currentEvent);
};
this.stopPropagation=function(){
dojo.event.browser.currentEvent.cancelBubble=true;
};
this.preventDefault=function(){
dojo.event.browser.currentEvent.returnValue=false;
};
this.keys={KEY_BACKSPACE:8,KEY_TAB:9,KEY_ENTER:13,KEY_SHIFT:16,KEY_CTRL:17,KEY_ALT:18,KEY_PAUSE:19,KEY_CAPS_LOCK:20,KEY_ESCAPE:27,KEY_SPACE:32,KEY_PAGE_UP:33,KEY_PAGE_DOWN:34,KEY_END:35,KEY_HOME:36,KEY_LEFT_ARROW:37,KEY_UP_ARROW:38,KEY_RIGHT_ARROW:39,KEY_DOWN_ARROW:40,KEY_INSERT:45,KEY_DELETE:46,KEY_LEFT_WINDOW:91,KEY_RIGHT_WINDOW:92,KEY_SELECT:93,KEY_F1:112,KEY_F2:113,KEY_F3:114,KEY_F4:115,KEY_F5:116,KEY_F6:117,KEY_F7:118,KEY_F8:119,KEY_F9:120,KEY_F10:121,KEY_F11:122,KEY_F12:123,KEY_NUM_LOCK:144,KEY_SCROLL_LOCK:145};
this.revKeys=[];
for(var key in this.keys){
this.revKeys[this.keys[key]]=key;
}
this.fixEvent=function(evt,_3c9){
if((!evt)&&(window["event"])){
var evt=window.event;
}
if((evt["type"])&&(evt["type"].indexOf("key")==0)){
evt.keys=this.revKeys;
for(var key in this.keys){
evt[key]=this.keys[key];
}
if((dojo.render.html.ie)&&(evt["type"]=="keypress")){
evt.charCode=evt.keyCode;
}
}
if(dojo.render.html.ie){
if(!evt.target){
evt.target=evt.srcElement;
}
if(!evt.currentTarget){
evt.currentTarget=(_3c9?_3c9:evt.srcElement);
}
if(!evt.layerX){
evt.layerX=evt.offsetX;
}
if(!evt.layerY){
evt.layerY=evt.offsetY;
}
var _3cb=((dojo.render.html.ie55)||(document["compatMode"]=="BackCompat"))?document.body:document.documentElement;
if(!evt.pageX){
evt.pageX=evt.clientX+(_3cb.scrollLeft||0);
}
if(!evt.pageY){
evt.pageY=evt.clientY+(_3cb.scrollTop||0);
}
if(evt.type=="mouseover"){
evt.relatedTarget=evt.fromElement;
}
if(evt.type=="mouseout"){
evt.relatedTarget=evt.toElement;
}
this.currentEvent=evt;
evt.callListener=this.callListener;
evt.stopPropagation=this.stopPropagation;
evt.preventDefault=this.preventDefault;
}
return evt;
};
this.stopEvent=function(ev){
if(window.event){
ev.returnValue=false;
ev.cancelBubble=true;
}else{
ev.preventDefault();
ev.stopPropagation();
}
};
};
dojo.kwCompoundRequire({common:["dojo.event","dojo.event.topic"],browser:["dojo.event.browser"],dashboard:["dojo.event.browser"]});
dojo.provide("dojo.event.*");
dojo.provide("dojo.widget.Manager");
dojo.require("dojo.lang.array");
dojo.require("dojo.lang.func");
dojo.require("dojo.event.*");
dojo.widget.manager=new function(){
this.widgets=[];
this.widgetIds=[];
this.topWidgets={};
var _3cd={};
var _3ce=[];
this.getUniqueId=function(_3cf){
return _3cf+"_"+(_3cd[_3cf]!=undefined?++_3cd[_3cf]:_3cd[_3cf]=0);
};
this.add=function(_3d0){
dojo.profile.start("dojo.widget.manager.add");
this.widgets.push(_3d0);
if(!_3d0.extraArgs["id"]){
_3d0.extraArgs["id"]=_3d0.extraArgs["ID"];
}
if(_3d0.widgetId==""){
if(_3d0["id"]){
_3d0.widgetId=_3d0["id"];
}else{
if(_3d0.extraArgs["id"]){
_3d0.widgetId=_3d0.extraArgs["id"];
}else{
_3d0.widgetId=this.getUniqueId(_3d0.widgetType);
}
}
}
if(this.widgetIds[_3d0.widgetId]){
dojo.debug("widget ID collision on ID: "+_3d0.widgetId);
}
this.widgetIds[_3d0.widgetId]=_3d0;
dojo.profile.end("dojo.widget.manager.add");
};
this.destroyAll=function(){
for(var x=this.widgets.length-1;x>=0;x--){
try{
this.widgets[x].destroy(true);
delete this.widgets[x];
}
catch(e){
}
}
};
this.remove=function(_3d2){
var tw=this.widgets[_3d2].widgetId;
delete this.widgetIds[tw];
this.widgets.splice(_3d2,1);
};
this.removeById=function(id){
for(var i=0;i<this.widgets.length;i++){
if(this.widgets[i].widgetId==id){
this.remove(i);
break;
}
}
};
this.getWidgetById=function(id){
return this.widgetIds[id];
};
this.getWidgetsByType=function(type){
var lt=type.toLowerCase();
var ret=[];
dojo.lang.forEach(this.widgets,function(x){
if(x.widgetType.toLowerCase()==lt){
ret.push(x);
}
});
return ret;
};
this.getWidgetsOfType=function(id){
dojo.deprecated("getWidgetsOfType","use getWidgetsByType","0.4");
return dojo.widget.manager.getWidgetsByType(id);
};
this.getWidgetsByFilter=function(_3dc,_3dd){
var ret=[];
dojo.lang.every(this.widgets,function(x){
if(_3dc(x)){
ret.push(x);
if(_3dd){
return false;
}
}
return true;
});
return (_3dd?ret[0]:ret);
};
this.getAllWidgets=function(){
return this.widgets.concat();
};
this.getWidgetByNode=function(node){
var w=this.getAllWidgets();
for(var i=0;i<w.length;i++){
if(w[i].domNode==node){
return w[i];
}
}
return null;
};
this.byId=this.getWidgetById;
this.byType=this.getWidgetsByType;
this.byFilter=this.getWidgetsByFilter;
this.byNode=this.getWidgetByNode;
var _3e3={};
var _3e4=["dojo.widget"];
for(var i=0;i<_3e4.length;i++){
_3e4[_3e4[i]]=true;
}
this.registerWidgetPackage=function(_3e6){
if(!_3e4[_3e6]){
_3e4[_3e6]=true;
_3e4.push(_3e6);
}
};
this.getWidgetPackageList=function(){
return dojo.lang.map(_3e4,function(elt){
return (elt!==true?elt:undefined);
});
};
this.getImplementation=function(_3e8,_3e9,_3ea){
var impl=this.getImplementationName(_3e8);
if(impl){
var ret=new impl(_3e9);
return ret;
}
};
this.getImplementationName=function(_3ed){
var _3ee=_3ed.toLowerCase();
var impl=_3e3[_3ee];
if(impl){
return impl;
}
if(!_3ce.length){
for(var _3f0 in dojo.render){
if(dojo.render[_3f0]["capable"]===true){
var _3f1=dojo.render[_3f0].prefixes;
for(var i=0;i<_3f1.length;i++){
_3ce.push(_3f1[i].toLowerCase());
}
}
}
_3ce.push("");
}
for(var i=0;i<_3e4.length;i++){
var _3f3=dojo.evalObjPath(_3e4[i]);
if(!_3f3){
continue;
}
for(var j=0;j<_3ce.length;j++){
if(!_3f3[_3ce[j]]){
continue;
}
for(var _3f5 in _3f3[_3ce[j]]){
if(_3f5.toLowerCase()!=_3ee){
continue;
}
_3e3[_3ee]=_3f3[_3ce[j]][_3f5];
return _3e3[_3ee];
}
}
for(var j=0;j<_3ce.length;j++){
for(var _3f5 in _3f3){
if(_3f5.toLowerCase()!=(_3ce[j]+_3ee)){
continue;
}
_3e3[_3ee]=_3f3[_3f5];
return _3e3[_3ee];
}
}
}
throw new Error("Could not locate \""+_3ed+"\" class");
};
this.resizing=false;
this.onWindowResized=function(){
if(this.resizing){
return;
}
try{
this.resizing=true;
for(var id in this.topWidgets){
var _3f7=this.topWidgets[id];
if(_3f7.checkSize){
_3f7.checkSize();
}
}
}
catch(e){
}
finally{
this.resizing=false;
}
};
if(typeof window!="undefined"){
dojo.addOnLoad(this,"onWindowResized");
dojo.event.connect(window,"onresize",this,"onWindowResized");
}
};
(function(){
var dw=dojo.widget;
var dwm=dw.manager;
var h=dojo.lang.curry(dojo.lang,"hitch",dwm);
var g=function(_3fc,_3fd){
dw[(_3fd||_3fc)]=h(_3fc);
};
g("add","addWidget");
g("destroyAll","destroyAllWidgets");
g("remove","removeWidget");
g("removeById","removeWidgetById");
g("getWidgetById");
g("getWidgetById","byId");
g("getWidgetsByType");
g("getWidgetsByFilter");
g("getWidgetsByType","byType");
g("getWidgetsByFilter","byFilter");
g("getWidgetByNode","byNode");
dw.all=function(n){
var _3ff=dwm.getAllWidgets.apply(dwm,arguments);
if(arguments.length>0){
return _3ff[n];
}
return _3ff;
};
g("registerWidgetPackage");
g("getImplementation","getWidgetImplementation");
g("getImplementationName","getWidgetImplementationName");
dw.widgets=dwm.widgets;
dw.widgetIds=dwm.widgetIds;
dw.root=dwm.root;
})();
dojo.provide("dojo.widget.Widget");
dojo.provide("dojo.widget.tags");
dojo.require("dojo.lang.func");
dojo.require("dojo.lang.array");
dojo.require("dojo.lang.extras");
dojo.require("dojo.lang.declare");
dojo.require("dojo.widget.Manager");
dojo.require("dojo.event.*");
dojo.declare("dojo.widget.Widget",null,{initializer:function(){
this.children=[];
this.extraArgs={};
},parent:null,isTopLevel:false,isModal:false,isEnabled:true,isHidden:false,isContainer:false,widgetId:"",widgetType:"Widget",toString:function(){
return "[Widget "+this.widgetType+", "+(this.widgetId||"NO ID")+"]";
},repr:function(){
return this.toString();
},enable:function(){
this.isEnabled=true;
},disable:function(){
this.isEnabled=false;
},hide:function(){
this.isHidden=true;
},show:function(){
this.isHidden=false;
},onResized:function(){
this.notifyChildrenOfResize();
},notifyChildrenOfResize:function(){
for(var i=0;i<this.children.length;i++){
var _401=this.children[i];
if(_401.onResized){
_401.onResized();
}
}
},create:function(args,_403,_404){
this.satisfyPropertySets(args,_403,_404);
this.mixInProperties(args,_403,_404);
this.postMixInProperties(args,_403,_404);
dojo.widget.manager.add(this);
this.buildRendering(args,_403,_404);
this.initialize(args,_403,_404);
this.postInitialize(args,_403,_404);
this.postCreate(args,_403,_404);
return this;
},destroy:function(_405){
this.destroyChildren();
this.uninitialize();
this.destroyRendering(_405);
dojo.widget.manager.removeById(this.widgetId);
},destroyChildren:function(){
while(this.children.length>0){
var tc=this.children[0];
this.removeChild(tc);
tc.destroy();
}
},getChildrenOfType:function(type,_408){
var ret=[];
var _40a=dojo.lang.isFunction(type);
if(!_40a){
type=type.toLowerCase();
}
for(var x=0;x<this.children.length;x++){
if(_40a){
if(this.children[x] instanceof type){
ret.push(this.children[x]);
}
}else{
if(this.children[x].widgetType.toLowerCase()==type){
ret.push(this.children[x]);
}
}
if(_408){
ret=ret.concat(this.children[x].getChildrenOfType(type,_408));
}
}
return ret;
},getDescendants:function(){
var _40c=[];
var _40d=[this];
var elem;
while(elem=_40d.pop()){
_40c.push(elem);
dojo.lang.forEach(elem.children,function(elem){
_40d.push(elem);
});
}
return _40c;
},satisfyPropertySets:function(args){
return args;
},mixInProperties:function(args,frag){
if((args["fastMixIn"])||(frag["fastMixIn"])){
for(var x in args){
this[x]=args[x];
}
return;
}
var _414;
var _415=dojo.widget.lcArgsCache[this.widgetType];
if(_415==null){
_415={};
for(var y in this){
_415[((new String(y)).toLowerCase())]=y;
}
dojo.widget.lcArgsCache[this.widgetType]=_415;
}
var _417={};
for(var x in args){
if(!this[x]){
var y=_415[(new String(x)).toLowerCase()];
if(y){
args[y]=args[x];
x=y;
}
}
if(_417[x]){
continue;
}
_417[x]=true;
if((typeof this[x])!=(typeof _414)){
if(typeof args[x]!="string"){
this[x]=args[x];
}else{
if(dojo.lang.isString(this[x])){
this[x]=args[x];
}else{
if(dojo.lang.isNumber(this[x])){
this[x]=new Number(args[x]);
}else{
if(dojo.lang.isBoolean(this[x])){
this[x]=(args[x].toLowerCase()=="false")?false:true;
}else{
if(dojo.lang.isFunction(this[x])){
if(args[x].search(/[^\w\.]+/i)==-1){
this[x]=dojo.evalObjPath(args[x],false);
}else{
var tn=dojo.lang.nameAnonFunc(new Function(args[x]),this);
dojo.event.connect(this,x,this,tn);
}
}else{
if(dojo.lang.isArray(this[x])){
this[x]=args[x].split(";");
}else{
if(this[x] instanceof Date){
this[x]=new Date(Number(args[x]));
}else{
if(typeof this[x]=="object"){
if(this[x] instanceof dojo.uri.Uri){
this[x]=args[x];
}else{
var _419=args[x].split(";");
for(var y=0;y<_419.length;y++){
var si=_419[y].indexOf(":");
if((si!=-1)&&(_419[y].length>si)){
this[x][_419[y].substr(0,si).replace(/^\s+|\s+$/g,"")]=_419[y].substr(si+1);
}
}
}
}else{
this[x]=args[x];
}
}
}
}
}
}
}
}
}else{
this.extraArgs[x.toLowerCase()]=args[x];
}
}
},postMixInProperties:function(){
},initialize:function(args,frag){
return false;
},postInitialize:function(args,frag){
return false;
},postCreate:function(args,frag){
return false;
},uninitialize:function(){
return false;
},buildRendering:function(){
dojo.unimplemented("dojo.widget.Widget.buildRendering, on "+this.toString()+", ");
return false;
},destroyRendering:function(){
dojo.unimplemented("dojo.widget.Widget.destroyRendering");
return false;
},cleanUp:function(){
dojo.unimplemented("dojo.widget.Widget.cleanUp");
return false;
},addedTo:function(_421){
},addChild:function(_422){
dojo.unimplemented("dojo.widget.Widget.addChild");
return false;
},removeChild:function(_423){
for(var x=0;x<this.children.length;x++){
if(this.children[x]===_423){
this.children.splice(x,1);
break;
}
}
return _423;
},resize:function(_425,_426){
this.setWidth(_425);
this.setHeight(_426);
},setWidth:function(_427){
if((typeof _427=="string")&&(_427.substr(-1)=="%")){
this.setPercentageWidth(_427);
}else{
this.setNativeWidth(_427);
}
},setHeight:function(_428){
if((typeof _428=="string")&&(_428.substr(-1)=="%")){
this.setPercentageHeight(_428);
}else{
this.setNativeHeight(_428);
}
},setPercentageHeight:function(_429){
return false;
},setNativeHeight:function(_42a){
return false;
},setPercentageWidth:function(_42b){
return false;
},setNativeWidth:function(_42c){
return false;
},getPreviousSibling:function(){
var idx=this.getParentIndex();
if(idx<=0){
return null;
}
return this.getSiblings()[idx-1];
},getSiblings:function(){
return this.parent.children;
},getParentIndex:function(){
return dojo.lang.indexOf(this.getSiblings(),this,true);
},getNextSibling:function(){
var idx=this.getParentIndex();
if(idx==this.getSiblings().length-1){
return null;
}
if(idx<0){
return null;
}
return this.getSiblings()[idx+1];
}});
dojo.widget.lcArgsCache={};
dojo.widget.tags={};
dojo.widget.tags.addParseTreeHandler=function(type){
var _430=type.toLowerCase();
this[_430]=function(_431,_432,_433,_434,_435){
return dojo.widget.buildWidgetFromParseTree(_430,_431,_432,_433,_434,_435);
};
};
dojo.widget.tags.addParseTreeHandler("dojo:widget");
dojo.widget.tags["dojo:propertyset"]=function(_436,_437,_438){
var _439=_437.parseProperties(_436["dojo:propertyset"]);
};
dojo.widget.tags["dojo:connect"]=function(_43a,_43b,_43c){
var _43d=_43b.parseProperties(_43a["dojo:connect"]);
};
dojo.widget.buildWidgetFromParseTree=function(type,frag,_440,_441,_442,_443){
var _444=type.split(":");
_444=(_444.length==2)?_444[1]:type;
var _445=_443||_440.parseProperties(frag["dojo:"+_444]);
var _446=dojo.widget.manager.getImplementation(_444);
if(!_446){
throw new Error("cannot find \""+_444+"\" widget");
}else{
if(!_446.create){
throw new Error("\""+_444+"\" widget object does not appear to implement *Widget");
}
}
_445["dojoinsertionindex"]=_442;
var ret=_446.create(_445,frag,_441);
return ret;
};
dojo.widget.defineWidget=function(_448,_449,_44a,init,_44c){
if(dojo.lang.isString(arguments[3])){
dojo.widget._defineWidget(arguments[0],arguments[3],arguments[1],arguments[4],arguments[2]);
}else{
var args=[arguments[0]],p=3;
if(dojo.lang.isString(arguments[1])){
args.push(arguments[1],arguments[2]);
}else{
args.push("",arguments[1]);
p=2;
}
if(dojo.lang.isFunction(arguments[p])){
args.push(arguments[p],arguments[p+1]);
}else{
args.push(null,arguments[p]);
}
dojo.widget._defineWidget.apply(this,args);
}
};
dojo.widget.defineWidget.renderers="html|svg|vml";
dojo.widget._defineWidget=function(_44e,_44f,_450,init,_452){
var _453=_44e.split(".");
var type=_453.pop();
var regx="\\.("+(_44f?_44f+"|":"")+dojo.widget.defineWidget.renderers+")\\.";
var r=_44e.search(new RegExp(regx));
_453=(r<0?_453.join("."):_44e.substr(0,r));
dojo.widget.manager.registerWidgetPackage(_453);
dojo.widget.tags.addParseTreeHandler("dojo:"+type.toLowerCase());
_452=(_452)||{};
_452.widgetType=type;
if((!init)&&(_452["classConstructor"])){
init=_452.classConstructor;
delete _452.classConstructor;
}
dojo.declare(_44e,_450,init,_452);
};
dojo.provide("dojo.widget.Parse");
dojo.require("dojo.widget.Manager");
dojo.require("dojo.dom");
dojo.widget.Parse=function(_457){
this.propertySetsList=[];
this.fragment=_457;
this.createComponents=function(frag,_459){
var _45a=[];
var _45b=false;
try{
if((frag)&&(frag["tagName"])&&(frag!=frag["nodeRef"])){
var _45c=dojo.widget.tags;
var tna=String(frag["tagName"]).split(";");
for(var x=0;x<tna.length;x++){
var ltn=(tna[x].replace(/^\s+|\s+$/g,"")).toLowerCase();
if(_45c[ltn]){
_45b=true;
frag.tagName=ltn;
var ret=_45c[ltn](frag,this,_459,frag["index"]);
_45a.push(ret);
}else{
if((dojo.lang.isString(ltn))&&(ltn.substr(0,5)=="dojo:")){
dojo.debug("no tag handler registed for type: ",ltn);
}
}
}
}
}
catch(e){
dojo.debug("dojo.widget.Parse: error:",e);
}
if(!_45b){
_45a=_45a.concat(this.createSubComponents(frag,_459));
}
return _45a;
};
this.createSubComponents=function(_461,_462){
var frag,comps=[];
for(var item in _461){
frag=_461[item];
if((frag)&&(typeof frag=="object")&&(frag!=_461.nodeRef)&&(frag!=_461["tagName"])){
comps=comps.concat(this.createComponents(frag,_462));
}
}
return comps;
};
this.parsePropertySets=function(_465){
return [];
var _466=[];
for(var item in _465){
if((_465[item]["tagName"]=="dojo:propertyset")){
_466.push(_465[item]);
}
}
this.propertySetsList.push(_466);
return _466;
};
this.parseProperties=function(_468){
var _469={};
for(var item in _468){
if((_468[item]==_468["tagName"])||(_468[item]==_468.nodeRef)){
}else{
if((_468[item]["tagName"])&&(dojo.widget.tags[_468[item].tagName.toLowerCase()])){
}else{
if((_468[item][0])&&(_468[item][0].value!="")&&(_468[item][0].value!=null)){
try{
if(item.toLowerCase()=="dataprovider"){
var _46b=this;
this.getDataProvider(_46b,_468[item][0].value);
_469.dataProvider=this.dataProvider;
}
_469[item]=_468[item][0].value;
var _46c=this.parseProperties(_468[item]);
for(var _46d in _46c){
_469[_46d]=_46c[_46d];
}
}
catch(e){
dojo.debug(e);
}
}
}
}
}
return _469;
};
this.getDataProvider=function(_46e,_46f){
dojo.io.bind({url:_46f,load:function(type,_471){
if(type=="load"){
_46e.dataProvider=_471;
}
},mimetype:"text/javascript",sync:true});
};
this.getPropertySetById=function(_472){
for(var x=0;x<this.propertySetsList.length;x++){
if(_472==this.propertySetsList[x]["id"][0].value){
return this.propertySetsList[x];
}
}
return "";
};
this.getPropertySetsByType=function(_474){
var _475=[];
for(var x=0;x<this.propertySetsList.length;x++){
var cpl=this.propertySetsList[x];
var cpcc=cpl["componentClass"]||cpl["componentType"]||null;
if((cpcc)&&(propertySetId==cpcc[0].value)){
_475.push(cpl);
}
}
return _475;
};
this.getPropertySets=function(_479){
var ppl="dojo:propertyproviderlist";
var _47b=[];
var _47c=_479["tagName"];
if(_479[ppl]){
var _47d=_479[ppl].value.split(" ");
for(var _47e in _47d){
if((_47e.indexOf("..")==-1)&&(_47e.indexOf("://")==-1)){
var _47f=this.getPropertySetById(_47e);
if(_47f!=""){
_47b.push(_47f);
}
}else{
}
}
}
return (this.getPropertySetsByType(_47c)).concat(_47b);
};
this.createComponentFromScript=function(_480,_481,_482){
var ltn="dojo:"+_481.toLowerCase();
if(dojo.widget.tags[ltn]){
_482.fastMixIn=true;
return [dojo.widget.tags[ltn](_482,this,null,null,_482)];
}else{
if(ltn.substr(0,5)=="dojo:"){
dojo.debug("no tag handler registed for type: ",ltn);
}
}
};
};
dojo.widget._parser_collection={"dojo":new dojo.widget.Parse()};
dojo.widget.getParser=function(name){
if(!name){
name="dojo";
}
if(!this._parser_collection[name]){
this._parser_collection[name]=new dojo.widget.Parse();
}
return this._parser_collection[name];
};
dojo.widget.createWidget=function(name,_486,_487,_488){
var _489=name.toLowerCase();
var _48a="dojo:"+_489;
var _48b=(dojo.byId(name)&&(!dojo.widget.tags[_48a]));
if((arguments.length==1)&&((typeof name!="string")||(_48b))){
var xp=new dojo.xml.Parse();
var tn=(_48b)?dojo.byId(name):name;
return dojo.widget.getParser().createComponents(xp.parseElement(tn,null,true))[0];
}
function fromScript(_48e,name,_490){
_490[_48a]={dojotype:[{value:_489}],nodeRef:_48e,fastMixIn:true};
return dojo.widget.getParser().createComponentFromScript(_48e,name,_490,true);
}
if(typeof name!="string"&&typeof _486=="string"){
dojo.deprecated("dojo.widget.createWidget","argument order is now of the form "+"dojo.widget.createWidget(NAME, [PROPERTIES, [REFERENCENODE, [POSITION]]])","0.4");
return fromScript(name,_486,_487);
}
_486=_486||{};
var _491=false;
var tn=null;
var h=dojo.render.html.capable;
if(h){
tn=document.createElement("span");
}
if(!_487){
_491=true;
_487=tn;
if(h){
document.body.appendChild(_487);
}
}else{
if(_488){
dojo.dom.insertAtPosition(tn,_487,_488);
}else{
tn=_487;
}
}
var _493=fromScript(tn,name,_486);
if(!_493||!_493[0]||typeof _493[0].widgetType=="undefined"){
throw new Error("createWidget: Creation of \""+name+"\" widget failed.");
}
if(_491){
if(_493[0].domNode.parentNode){
_493[0].domNode.parentNode.removeChild(_493[0].domNode);
}
}
return _493[0];
};
dojo.widget.fromScript=function(name,_495,_496,_497){
dojo.deprecated("dojo.widget.fromScript"," use "+"dojo.widget.createWidget instead","0.4");
return dojo.widget.createWidget(name,_495,_496,_497);
};
dojo.provide("dojo.uri.Uri");
dojo.uri=new function(){
this.joinPath=function(){
var arr=[];
for(var i=0;i<arguments.length;i++){
arr.push(arguments[i]);
}
return arr.join("/").replace(/\/{2,}/g,"/").replace(/((https*|ftps*):)/i,"$1/");
};
this.dojoUri=function(uri){
return new dojo.uri.Uri(dojo.hostenv.getBaseScriptUri(),uri);
};
this.Uri=function(){
var uri=arguments[0];
for(var i=1;i<arguments.length;i++){
if(!arguments[i]){
continue;
}
var _49d=new dojo.uri.Uri(arguments[i].toString());
var _49e=new dojo.uri.Uri(uri.toString());
if(_49d.path==""&&_49d.scheme==null&&_49d.authority==null&&_49d.query==null){
if(_49d.fragment!=null){
_49e.fragment=_49d.fragment;
}
_49d=_49e;
}else{
if(_49d.scheme==null){
_49d.scheme=_49e.scheme;
if(_49d.authority==null){
_49d.authority=_49e.authority;
if(_49d.path.charAt(0)!="/"){
var path=_49e.path.substring(0,_49e.path.lastIndexOf("/")+1)+_49d.path;
var segs=path.split("/");
for(var j=0;j<segs.length;j++){
if(segs[j]=="."){
if(j==segs.length-1){
segs[j]="";
}else{
segs.splice(j,1);
j--;
}
}else{
if(j>0&&!(j==1&&segs[0]=="")&&segs[j]==".."&&segs[j-1]!=".."){
if(j==segs.length-1){
segs.splice(j,1);
segs[j-1]="";
}else{
segs.splice(j-1,2);
j-=2;
}
}
}
}
_49d.path=segs.join("/");
}
}
}
}
uri="";
if(_49d.scheme!=null){
uri+=_49d.scheme+":";
}
if(_49d.authority!=null){
uri+="//"+_49d.authority;
}
uri+=_49d.path;
if(_49d.query!=null){
uri+="?"+_49d.query;
}
if(_49d.fragment!=null){
uri+="#"+_49d.fragment;
}
}
this.uri=uri.toString();
var _4a2="^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\\?([^#]*))?(#(.*))?$";
var r=this.uri.match(new RegExp(_4a2));
this.scheme=r[2]||(r[1]?"":null);
this.authority=r[4]||(r[3]?"":null);
this.path=r[5];
this.query=r[7]||(r[6]?"":null);
this.fragment=r[9]||(r[8]?"":null);
if(this.authority!=null){
_4a2="^((([^:]+:)?([^@]+))@)?([^:]*)(:([0-9]+))?$";
r=this.authority.match(new RegExp(_4a2));
this.user=r[3]||null;
this.password=r[4]||null;
this.host=r[5];
this.port=r[7]||null;
}
this.toString=function(){
return this.uri;
};
};
};
dojo.kwCompoundRequire({common:[["dojo.uri.Uri",false,false]]});
dojo.provide("dojo.uri.*");
dojo.provide("dojo.widget.DomWidget");
dojo.require("dojo.event.*");
dojo.require("dojo.widget.Widget");
dojo.require("dojo.dom");
dojo.require("dojo.xml.Parse");
dojo.require("dojo.uri.*");
dojo.require("dojo.lang.func");
dojo.require("dojo.lang.extras");
dojo.widget._cssFiles={};
dojo.widget._cssStrings={};
dojo.widget._templateCache={};
dojo.widget.defaultStrings={dojoRoot:dojo.hostenv.getBaseScriptUri(),baseScriptUri:dojo.hostenv.getBaseScriptUri()};
dojo.widget.buildFromTemplate=function(){
dojo.lang.forward("fillFromTemplateCache");
};
dojo.widget.fillFromTemplateCache=function(obj,_4a5,_4a6,_4a7,_4a8){
var _4a9=_4a5||obj.templatePath;
var _4aa=_4a6||obj.templateCssPath;
if(_4a9&&!(_4a9 instanceof dojo.uri.Uri)){
_4a9=dojo.uri.dojoUri(_4a9);
dojo.deprecated("templatePath should be of type dojo.uri.Uri",null,"0.4");
}
if(_4aa&&!(_4aa instanceof dojo.uri.Uri)){
_4aa=dojo.uri.dojoUri(_4aa);
dojo.deprecated("templateCssPath should be of type dojo.uri.Uri",null,"0.4");
}
var _4ab=dojo.widget._templateCache;
if(!obj["widgetType"]){
do{
var _4ac="__dummyTemplate__"+dojo.widget._templateCache.dummyCount++;
}while(_4ab[_4ac]);
obj.widgetType=_4ac;
}
var wt=obj.widgetType;
if(_4aa&&!dojo.widget._cssFiles[_4aa.toString()]){
if((!obj.templateCssString)&&(_4aa)){
obj.templateCssString=dojo.hostenv.getText(_4aa);
obj.templateCssPath=null;
}
if((obj["templateCssString"])&&(!obj.templateCssString["loaded"])){
dojo.style.insertCssText(obj.templateCssString,null,_4aa);
if(!obj.templateCssString){
obj.templateCssString="";
}
obj.templateCssString.loaded=true;
}
dojo.widget._cssFiles[_4aa.toString()]=true;
}
var ts=_4ab[wt];
if(!ts){
_4ab[wt]={"string":null,"node":null};
if(_4a8){
ts={};
}else{
ts=_4ab[wt];
}
}
if((!obj.templateString)&&(!_4a8)){
obj.templateString=_4a7||ts["string"];
}
if((!obj.templateNode)&&(!_4a8)){
obj.templateNode=ts["node"];
}
if((!obj.templateNode)&&(!obj.templateString)&&(_4a9)){
var _4af=dojo.hostenv.getText(_4a9);
if(_4af){
_4af=_4af.replace(/^\s*<\?xml(\s)+version=[\'\"](\d)*.(\d)*[\'\"](\s)*\?>/im,"");
var _4b0=_4af.match(/<body[^>]*>\s*([\s\S]+)\s*<\/body>/im);
if(_4b0){
_4af=_4b0[1];
}
}else{
_4af="";
}
obj.templateString=_4af;
if(!_4a8){
_4ab[wt]["string"]=_4af;
}
}
if((!ts["string"])&&(!_4a8)){
ts.string=obj.templateString;
}
};
dojo.widget._templateCache.dummyCount=0;
dojo.widget.attachProperties=["dojoAttachPoint","id"];
dojo.widget.eventAttachProperty="dojoAttachEvent";
dojo.widget.onBuildProperty="dojoOnBuild";
dojo.widget.waiNames=["waiRole","waiState"];
dojo.widget.wai={waiRole:{name:"waiRole",namespace:"http://www.w3.org/TR/xhtml2",alias:"x2",prefix:"wairole:",nsName:"role"},waiState:{name:"waiState",namespace:"http://www.w3.org/2005/07/aaa",alias:"aaa",prefix:"",nsName:"state"},setAttr:function(node,attr,_4b3){
if(dojo.render.html.ie){
node.setAttribute(this[attr].alias+":"+this[attr].nsName,this[attr].prefix+_4b3);
}else{
node.setAttributeNS(this[attr].namespace,this[attr].nsName,this[attr].prefix+_4b3);
}
}};
dojo.widget.attachTemplateNodes=function(_4b4,_4b5,_4b6){
var _4b7=dojo.dom.ELEMENT_NODE;
function trim(str){
return str.replace(/^\s+|\s+$/g,"");
}
if(!_4b4){
_4b4=_4b5.domNode;
}
if(_4b4.nodeType!=_4b7){
return;
}
var _4b9=_4b4.all||_4b4.getElementsByTagName("*");
var _4ba=_4b5;
for(var x=-1;x<_4b9.length;x++){
var _4bc=(x==-1)?_4b4:_4b9[x];
var _4bd=[];
for(var y=0;y<this.attachProperties.length;y++){
var _4bf=_4bc.getAttribute(this.attachProperties[y]);
if(_4bf){
_4bd=_4bf.split(";");
for(var z=0;z<_4bd.length;z++){
if(dojo.lang.isArray(_4b5[_4bd[z]])){
_4b5[_4bd[z]].push(_4bc);
}else{
_4b5[_4bd[z]]=_4bc;
}
}
break;
}
}
var _4c1=_4bc.getAttribute(this.templateProperty);
if(_4c1){
_4b5[_4c1]=_4bc;
}
dojo.lang.forEach(dojo.widget.waiNames,function(name){
var wai=dojo.widget.wai[name];
var val=_4bc.getAttribute(wai.name);
if(val){
dojo.widget.wai.setAttr(_4bc,wai.name,val);
}
},this);
var _4c5=_4bc.getAttribute(this.eventAttachProperty);
if(_4c5){
var evts=_4c5.split(";");
for(var y=0;y<evts.length;y++){
if((!evts[y])||(!evts[y].length)){
continue;
}
var _4c7=null;
var tevt=trim(evts[y]);
if(evts[y].indexOf(":")>=0){
var _4c9=tevt.split(":");
tevt=trim(_4c9[0]);
_4c7=trim(_4c9[1]);
}
if(!_4c7){
_4c7=tevt;
}
var tf=function(){
var ntf=new String(_4c7);
return function(evt){
if(_4ba[ntf]){
_4ba[ntf](dojo.event.browser.fixEvent(evt,this));
}
};
}();
dojo.event.browser.addListener(_4bc,tevt,tf,false,true);
}
}
for(var y=0;y<_4b6.length;y++){
var _4cd=_4bc.getAttribute(_4b6[y]);
if((_4cd)&&(_4cd.length)){
var _4c7=null;
var _4ce=_4b6[y].substr(4);
_4c7=trim(_4cd);
var _4cf=[_4c7];
if(_4c7.indexOf(";")>=0){
_4cf=dojo.lang.map(_4c7.split(";"),trim);
}
for(var z=0;z<_4cf.length;z++){
if(!_4cf[z].length){
continue;
}
var tf=function(){
var ntf=new String(_4cf[z]);
return function(evt){
if(_4ba[ntf]){
_4ba[ntf](dojo.event.browser.fixEvent(evt,this));
}
};
}();
dojo.event.browser.addListener(_4bc,_4ce,tf,false,true);
}
}
}
var _4d2=_4bc.getAttribute(this.onBuildProperty);
if(_4d2){
eval("var node = baseNode; var widget = targetObj; "+_4d2);
}
}
};
dojo.widget.getDojoEventsFromStr=function(str){
var re=/(dojoOn([a-z]+)(\s?))=/gi;
var evts=str?str.match(re)||[]:[];
var ret=[];
var lem={};
for(var x=0;x<evts.length;x++){
if(evts[x].legth<1){
continue;
}
var cm=evts[x].replace(/\s/,"");
cm=(cm.slice(0,cm.length-1));
if(!lem[cm]){
lem[cm]=true;
ret.push(cm);
}
}
return ret;
};
dojo.declare("dojo.widget.DomWidget",dojo.widget.Widget,{initializer:function(){
if((arguments.length>0)&&(typeof arguments[0]=="object")){
this.create(arguments[0]);
}
},templateNode:null,templateString:null,templateCssString:null,preventClobber:false,domNode:null,containerNode:null,addChild:function(_4da,_4db,pos,ref,_4de){
if(!this.isContainer){
dojo.debug("dojo.widget.DomWidget.addChild() attempted on non-container widget");
return null;
}else{
this.addWidgetAsDirectChild(_4da,_4db,pos,ref,_4de);
this.registerChild(_4da,_4de);
}
return _4da;
},addWidgetAsDirectChild:function(_4df,_4e0,pos,ref,_4e3){
if((!this.containerNode)&&(!_4e0)){
this.containerNode=this.domNode;
}
var cn=(_4e0)?_4e0:this.containerNode;
if(!pos){
pos="after";
}
if(!ref){
if(!cn){
cn=document.body;
}
ref=cn.lastChild;
}
if(!_4e3){
_4e3=0;
}
_4df.domNode.setAttribute("dojoinsertionindex",_4e3);
if(!ref){
cn.appendChild(_4df.domNode);
}else{
if(pos=="insertAtIndex"){
dojo.dom.insertAtIndex(_4df.domNode,ref.parentNode,_4e3);
}else{
if((pos=="after")&&(ref===cn.lastChild)){
cn.appendChild(_4df.domNode);
}else{
dojo.dom.insertAtPosition(_4df.domNode,cn,pos);
}
}
}
},registerChild:function(_4e5,_4e6){
_4e5.dojoInsertionIndex=_4e6;
var idx=-1;
for(var i=0;i<this.children.length;i++){
if(this.children[i].dojoInsertionIndex<_4e6){
idx=i;
}
}
this.children.splice(idx+1,0,_4e5);
_4e5.parent=this;
_4e5.addedTo(this);
delete dojo.widget.manager.topWidgets[_4e5.widgetId];
},removeChild:function(_4e9){
dojo.dom.removeNode(_4e9.domNode);
return dojo.widget.DomWidget.superclass.removeChild.call(this,_4e9);
},getFragNodeRef:function(frag){
if(!frag||!frag["dojo:"+this.widgetType.toLowerCase()]){
dojo.raise("Error: no frag for widget type "+this.widgetType+", id "+this.widgetId+" (maybe a widget has set it's type incorrectly)");
}
return (frag?frag["dojo:"+this.widgetType.toLowerCase()]["nodeRef"]:null);
},postInitialize:function(args,frag,_4ed){
var _4ee=this.getFragNodeRef(frag);
if(_4ed&&(_4ed.snarfChildDomOutput||!_4ee)){
_4ed.addWidgetAsDirectChild(this,"","insertAtIndex","",args["dojoinsertionindex"],_4ee);
}else{
if(_4ee){
if(this.domNode&&(this.domNode!==_4ee)){
var _4ef=_4ee.parentNode.replaceChild(this.domNode,_4ee);
}
}
}
if(_4ed){
_4ed.registerChild(this,args.dojoinsertionindex);
}else{
dojo.widget.manager.topWidgets[this.widgetId]=this;
}
if(this.isContainer){
var _4f0=dojo.widget.getParser();
_4f0.createSubComponents(frag,this);
}
},buildRendering:function(args,frag){
var ts=dojo.widget._templateCache[this.widgetType];
if((!this.preventClobber)&&((this.templatePath)||(this.templateNode)||((this["templateString"])&&(this.templateString.length))||((typeof ts!="undefined")&&((ts["string"])||(ts["node"]))))){
this.buildFromTemplate(args,frag);
}else{
this.domNode=this.getFragNodeRef(frag);
}
this.fillInTemplate(args,frag);
},buildFromTemplate:function(args,frag){
var _4f6=false;
if(args["templatecsspath"]){
args["templateCssPath"]=args["templatecsspath"];
}
if(args["templatepath"]){
_4f6=true;
args["templatePath"]=args["templatepath"];
}
dojo.widget.fillFromTemplateCache(this,args["templatePath"],args["templateCssPath"],null,_4f6);
var ts=dojo.widget._templateCache[this.widgetType];
if((ts)&&(!_4f6)){
if(!this.templateString.length){
this.templateString=ts["string"];
}
if(!this.templateNode){
this.templateNode=ts["node"];
}
}
var _4f8=false;
var node=null;
var tstr=this.templateString;
if((!this.templateNode)&&(this.templateString)){
_4f8=this.templateString.match(/\$\{([^\}]+)\}/g);
if(_4f8){
var hash=this.strings||{};
for(var key in dojo.widget.defaultStrings){
if(dojo.lang.isUndefined(hash[key])){
hash[key]=dojo.widget.defaultStrings[key];
}
}
for(var i=0;i<_4f8.length;i++){
var key=_4f8[i];
key=key.substring(2,key.length-1);
var kval=(key.substring(0,5)=="this.")?dojo.lang.getObjPathValue(key.substring(5),this):hash[key];
var _4ff;
if((kval)||(dojo.lang.isString(kval))){
_4ff=(dojo.lang.isFunction(kval))?kval.call(this,key,this.templateString):kval;
tstr=tstr.replace(_4f8[i],_4ff);
}
}
}else{
this.templateNode=this.createNodesFromText(this.templateString,true)[0];
if(!_4f6){
ts.node=this.templateNode;
}
}
}
if((!this.templateNode)&&(!_4f8)){
dojo.debug("weren't able to create template!");
return false;
}else{
if(!_4f8){
node=this.templateNode.cloneNode(true);
if(!node){
return false;
}
}else{
node=this.createNodesFromText(tstr,true)[0];
}
}
this.domNode=node;
this.attachTemplateNodes(this.domNode,this);
if(this.isContainer&&this.containerNode){
var src=this.getFragNodeRef(frag);
if(src){
dojo.dom.moveChildren(src,this.containerNode);
}
}
},attachTemplateNodes:function(_501,_502){
if(!_502){
_502=this;
}
return dojo.widget.attachTemplateNodes(_501,_502,dojo.widget.getDojoEventsFromStr(this.templateString));
},fillInTemplate:function(){
},destroyRendering:function(){
try{
delete this.domNode;
}
catch(e){
}
},cleanUp:function(){
},getContainerHeight:function(){
dojo.unimplemented("dojo.widget.DomWidget.getContainerHeight");
},getContainerWidth:function(){
dojo.unimplemented("dojo.widget.DomWidget.getContainerWidth");
},createNodesFromText:function(){
dojo.unimplemented("dojo.widget.DomWidget.createNodesFromText");
}});
dojo.provide("dojo.graphics.color");
dojo.require("dojo.lang.array");
dojo.graphics.color.Color=function(r,g,b,a){
if(dojo.lang.isArray(r)){
this.r=r[0];
this.g=r[1];
this.b=r[2];
this.a=r[3]||1;
}else{
if(dojo.lang.isString(r)){
var rgb=dojo.graphics.color.extractRGB(r);
this.r=rgb[0];
this.g=rgb[1];
this.b=rgb[2];
this.a=g||1;
}else{
if(r instanceof dojo.graphics.color.Color){
this.r=r.r;
this.b=r.b;
this.g=r.g;
this.a=r.a;
}else{
this.r=r;
this.g=g;
this.b=b;
this.a=a;
}
}
}
};
dojo.graphics.color.Color.fromArray=function(arr){
return new dojo.graphics.color.Color(arr[0],arr[1],arr[2],arr[3]);
};
dojo.lang.extend(dojo.graphics.color.Color,{toRgb:function(_509){
if(_509){
return this.toRgba();
}else{
return [this.r,this.g,this.b];
}
},toRgba:function(){
return [this.r,this.g,this.b,this.a];
},toHex:function(){
return dojo.graphics.color.rgb2hex(this.toRgb());
},toCss:function(){
return "rgb("+this.toRgb().join()+")";
},toString:function(){
return this.toHex();
},blend:function(_50a,_50b){
return dojo.graphics.color.blend(this.toRgb(),new dojo.graphics.color.Color(_50a).toRgb(),_50b);
}});
dojo.graphics.color.named={white:[255,255,255],black:[0,0,0],red:[255,0,0],green:[0,255,0],blue:[0,0,255],navy:[0,0,128],gray:[128,128,128],silver:[192,192,192]};
dojo.graphics.color.blend=function(a,b,_50e){
if(typeof a=="string"){
return dojo.graphics.color.blendHex(a,b,_50e);
}
if(!_50e){
_50e=0;
}else{
if(_50e>1){
_50e=1;
}else{
if(_50e<-1){
_50e=-1;
}
}
}
var c=new Array(3);
for(var i=0;i<3;i++){
var half=Math.abs(a[i]-b[i])/2;
c[i]=Math.floor(Math.min(a[i],b[i])+half+(half*_50e));
}
return c;
};
dojo.graphics.color.blendHex=function(a,b,_514){
return dojo.graphics.color.rgb2hex(dojo.graphics.color.blend(dojo.graphics.color.hex2rgb(a),dojo.graphics.color.hex2rgb(b),_514));
};
dojo.graphics.color.extractRGB=function(_515){
var hex="0123456789abcdef";
_515=_515.toLowerCase();
if(_515.indexOf("rgb")==0){
var _517=_515.match(/rgba*\((\d+), *(\d+), *(\d+)/i);
var ret=_517.splice(1,3);
return ret;
}else{
var _519=dojo.graphics.color.hex2rgb(_515);
if(_519){
return _519;
}else{
return dojo.graphics.color.named[_515]||[255,255,255];
}
}
};
dojo.graphics.color.hex2rgb=function(hex){
var _51b="0123456789ABCDEF";
var rgb=new Array(3);
if(hex.indexOf("#")==0){
hex=hex.substring(1);
}
hex=hex.toUpperCase();
if(hex.replace(new RegExp("["+_51b+"]","g"),"")!=""){
return null;
}
if(hex.length==3){
rgb[0]=hex.charAt(0)+hex.charAt(0);
rgb[1]=hex.charAt(1)+hex.charAt(1);
rgb[2]=hex.charAt(2)+hex.charAt(2);
}else{
rgb[0]=hex.substring(0,2);
rgb[1]=hex.substring(2,4);
rgb[2]=hex.substring(4);
}
for(var i=0;i<rgb.length;i++){
rgb[i]=_51b.indexOf(rgb[i].charAt(0))*16+_51b.indexOf(rgb[i].charAt(1));
}
return rgb;
};
dojo.graphics.color.rgb2hex=function(r,g,b){
if(dojo.lang.isArray(r)){
g=r[1]||0;
b=r[2]||0;
r=r[0]||0;
}
var ret=dojo.lang.map([r,g,b],function(x){
x=new Number(x);
var s=x.toString(16);
while(s.length<2){
s="0"+s;
}
return s;
});
ret.unshift("#");
return ret.join("");
};
dojo.provide("dojo.style");
dojo.require("dojo.graphics.color");
dojo.require("dojo.uri.Uri");
dojo.require("dojo.lang.common");
(function(){
var h=dojo.render.html;
var ds=dojo.style;
var db=document["body"]||document["documentElement"];
ds.boxSizing={MARGIN_BOX:"margin-box",BORDER_BOX:"border-box",PADDING_BOX:"padding-box",CONTENT_BOX:"content-box"};
var bs=ds.boxSizing;
ds.getBoxSizing=function(node){
if((h.ie)||(h.opera)){
var cm=document["compatMode"];
if((cm=="BackCompat")||(cm=="QuirksMode")){
return bs.BORDER_BOX;
}else{
return bs.CONTENT_BOX;
}
}else{
if(arguments.length==0){
node=document.documentElement;
}
var _52a=ds.getStyle(node,"-moz-box-sizing");
if(!_52a){
_52a=ds.getStyle(node,"box-sizing");
}
return (_52a?_52a:bs.CONTENT_BOX);
}
};
ds.isBorderBox=function(node){
return (ds.getBoxSizing(node)==bs.BORDER_BOX);
};
ds.getUnitValue=function(node,_52d,_52e){
var s=ds.getComputedStyle(node,_52d);
if((!s)||((s=="auto")&&(_52e))){
return {value:0,units:"px"};
}
if(dojo.lang.isUndefined(s)){
return ds.getUnitValue.bad;
}
var _530=s.match(/(\-?[\d.]+)([a-z%]*)/i);
if(!_530){
return ds.getUnitValue.bad;
}
return {value:Number(_530[1]),units:_530[2].toLowerCase()};
};
ds.getUnitValue.bad={value:NaN,units:""};
ds.getPixelValue=function(node,_532,_533){
var _534=ds.getUnitValue(node,_532,_533);
if(isNaN(_534.value)){
return 0;
}
if((_534.value)&&(_534.units!="px")){
return NaN;
}
return _534.value;
};
ds.getNumericStyle=function(){
dojo.deprecated("dojo.(style|html).getNumericStyle","in favor of dojo.(style|html).getPixelValue","0.4");
return ds.getPixelValue.apply(this,arguments);
};
ds.setPositivePixelValue=function(node,_536,_537){
if(isNaN(_537)){
return false;
}
node.style[_536]=Math.max(0,_537)+"px";
return true;
};
ds._sumPixelValues=function(node,_539,_53a){
var _53b=0;
for(var x=0;x<_539.length;x++){
_53b+=ds.getPixelValue(node,_539[x],_53a);
}
return _53b;
};
ds.isPositionAbsolute=function(node){
return (ds.getComputedStyle(node,"position")=="absolute");
};
ds.getBorderExtent=function(node,side){
return (ds.getStyle(node,"border-"+side+"-style")=="none"?0:ds.getPixelValue(node,"border-"+side+"-width"));
};
ds.getMarginWidth=function(node){
return ds._sumPixelValues(node,["margin-left","margin-right"],ds.isPositionAbsolute(node));
};
ds.getBorderWidth=function(node){
return ds.getBorderExtent(node,"left")+ds.getBorderExtent(node,"right");
};
ds.getPaddingWidth=function(node){
return ds._sumPixelValues(node,["padding-left","padding-right"],true);
};
ds.getPadBorderWidth=function(node){
return ds.getPaddingWidth(node)+ds.getBorderWidth(node);
};
ds.getContentBoxWidth=function(node){
node=dojo.byId(node);
return node.offsetWidth-ds.getPadBorderWidth(node);
};
ds.getBorderBoxWidth=function(node){
node=dojo.byId(node);
return node.offsetWidth;
};
ds.getMarginBoxWidth=function(node){
return ds.getInnerWidth(node)+ds.getMarginWidth(node);
};
ds.setContentBoxWidth=function(node,_548){
node=dojo.byId(node);
if(ds.isBorderBox(node)){
_548+=ds.getPadBorderWidth(node);
}
return ds.setPositivePixelValue(node,"width",_548);
};
ds.setMarginBoxWidth=function(node,_54a){
node=dojo.byId(node);
if(!ds.isBorderBox(node)){
_54a-=ds.getPadBorderWidth(node);
}
_54a-=ds.getMarginWidth(node);
return ds.setPositivePixelValue(node,"width",_54a);
};
ds.getContentWidth=ds.getContentBoxWidth;
ds.getInnerWidth=ds.getBorderBoxWidth;
ds.getOuterWidth=ds.getMarginBoxWidth;
ds.setContentWidth=ds.setContentBoxWidth;
ds.setOuterWidth=ds.setMarginBoxWidth;
ds.getMarginHeight=function(node){
return ds._sumPixelValues(node,["margin-top","margin-bottom"],ds.isPositionAbsolute(node));
};
ds.getBorderHeight=function(node){
return ds.getBorderExtent(node,"top")+ds.getBorderExtent(node,"bottom");
};
ds.getPaddingHeight=function(node){
return ds._sumPixelValues(node,["padding-top","padding-bottom"],true);
};
ds.getPadBorderHeight=function(node){
return ds.getPaddingHeight(node)+ds.getBorderHeight(node);
};
ds.getContentBoxHeight=function(node){
node=dojo.byId(node);
return node.offsetHeight-ds.getPadBorderHeight(node);
};
ds.getBorderBoxHeight=function(node){
node=dojo.byId(node);
return node.offsetHeight;
};
ds.getMarginBoxHeight=function(node){
return ds.getInnerHeight(node)+ds.getMarginHeight(node);
};
ds.setContentBoxHeight=function(node,_553){
node=dojo.byId(node);
if(ds.isBorderBox(node)){
_553+=ds.getPadBorderHeight(node);
}
return ds.setPositivePixelValue(node,"height",_553);
};
ds.setMarginBoxHeight=function(node,_555){
node=dojo.byId(node);
if(!ds.isBorderBox(node)){
_555-=ds.getPadBorderHeight(node);
}
_555-=ds.getMarginHeight(node);
return ds.setPositivePixelValue(node,"height",_555);
};
ds.getContentHeight=ds.getContentBoxHeight;
ds.getInnerHeight=ds.getBorderBoxHeight;
ds.getOuterHeight=ds.getMarginBoxHeight;
ds.setContentHeight=ds.setContentBoxHeight;
ds.setOuterHeight=ds.setMarginBoxHeight;
ds.getAbsolutePosition=ds.abs=function(node,_557){
node=dojo.byId(node);
var ret=[];
ret.x=ret.y=0;
var st=dojo.html.getScrollTop();
var sl=dojo.html.getScrollLeft();
if(h.ie){
with(node.getBoundingClientRect()){
ret.x=left-2;
ret.y=top-2;
}
}else{
if(document.getBoxObjectFor){
var bo=document.getBoxObjectFor(node);
ret.x=bo.x-ds.sumAncestorProperties(node,"scrollLeft");
ret.y=bo.y-ds.sumAncestorProperties(node,"scrollTop");
}else{
if(node["offsetParent"]){
var _55c;
if((h.safari)&&(node.style.getPropertyValue("position")=="absolute")&&(node.parentNode==db)){
_55c=db;
}else{
_55c=db.parentNode;
}
if(node.parentNode!=db){
var nd=node;
if(window.opera){
nd=db;
}
ret.x-=ds.sumAncestorProperties(nd,"scrollLeft");
ret.y-=ds.sumAncestorProperties(nd,"scrollTop");
}
do{
var n=node["offsetLeft"];
ret.x+=isNaN(n)?0:n;
var m=node["offsetTop"];
ret.y+=isNaN(m)?0:m;
node=node.offsetParent;
}while((node!=_55c)&&(node!=null));
}else{
if(node["x"]&&node["y"]){
ret.x+=isNaN(node.x)?0:node.x;
ret.y+=isNaN(node.y)?0:node.y;
}
}
}
}
if(_557){
ret.y+=st;
ret.x+=sl;
}
ret[0]=ret.x;
ret[1]=ret.y;
return ret;
};
ds.sumAncestorProperties=function(node,prop){
node=dojo.byId(node);
if(!node){
return 0;
}
var _562=0;
while(node){
var val=node[prop];
if(val){
_562+=val-0;
if(node==document.body){
break;
}
}
node=node.parentNode;
}
return _562;
};
ds.getTotalOffset=function(node,type,_566){
return ds.abs(node,_566)[(type=="top")?"y":"x"];
};
ds.getAbsoluteX=ds.totalOffsetLeft=function(node,_568){
return ds.getTotalOffset(node,"left",_568);
};
ds.getAbsoluteY=ds.totalOffsetTop=function(node,_56a){
return ds.getTotalOffset(node,"top",_56a);
};
ds.styleSheet=null;
ds.insertCssRule=function(_56b,_56c,_56d){
if(!ds.styleSheet){
if(document.createStyleSheet){
ds.styleSheet=document.createStyleSheet();
}else{
if(document.styleSheets[0]){
ds.styleSheet=document.styleSheets[0];
}else{
return null;
}
}
}
if(arguments.length<3){
if(ds.styleSheet.cssRules){
_56d=ds.styleSheet.cssRules.length;
}else{
if(ds.styleSheet.rules){
_56d=ds.styleSheet.rules.length;
}else{
return null;
}
}
}
if(ds.styleSheet.insertRule){
var rule=_56b+" { "+_56c+" }";
return ds.styleSheet.insertRule(rule,_56d);
}else{
if(ds.styleSheet.addRule){
return ds.styleSheet.addRule(_56b,_56c,_56d);
}else{
return null;
}
}
};
ds.removeCssRule=function(_56f){
if(!ds.styleSheet){
dojo.debug("no stylesheet defined for removing rules");
return false;
}
if(h.ie){
if(!_56f){
_56f=ds.styleSheet.rules.length;
ds.styleSheet.removeRule(_56f);
}
}else{
if(document.styleSheets[0]){
if(!_56f){
_56f=ds.styleSheet.cssRules.length;
}
ds.styleSheet.deleteRule(_56f);
}
}
return true;
};
ds.insertCssFile=function(URI,doc,_572){
if(!URI){
return;
}
if(!doc){
doc=document;
}
var _573=dojo.hostenv.getText(URI);
_573=ds.fixPathsInCssText(_573,URI);
if(_572){
var _574=doc.getElementsByTagName("style");
var _575="";
for(var i=0;i<_574.length;i++){
_575=(_574[i].styleSheet&&_574[i].styleSheet.cssText)?_574[i].styleSheet.cssText:_574[i].innerHTML;
if(_573==_575){
return;
}
}
}
var _577=ds.insertCssText(_573);
if(_577&&djConfig.isDebug){
_577.setAttribute("dbgHref",URI);
}
return _577;
};
ds.insertCssText=function(_578,doc,URI){
if(!_578){
return;
}
if(!doc){
doc=document;
}
if(URI){
_578=ds.fixPathsInCssText(_578,URI);
}
var _57b=doc.createElement("style");
_57b.setAttribute("type","text/css");
var head=doc.getElementsByTagName("head")[0];
if(!head){
dojo.debug("No head tag in document, aborting styles");
return;
}else{
head.appendChild(_57b);
}
if(_57b.styleSheet){
_57b.styleSheet.cssText=_578;
}else{
var _57d=doc.createTextNode(_578);
_57b.appendChild(_57d);
}
return _57b;
};
ds.fixPathsInCssText=function(_57e,URI){
if(!_57e||!URI){
return;
}
var pos=0;
var str="";
var url="";
while(pos!=-1){
pos=0;
url="";
pos=_57e.indexOf("url(",pos);
if(pos<0){
break;
}
str+=_57e.slice(0,pos+4);
_57e=_57e.substring(pos+4,_57e.length);
url+=_57e.match(/^[\t\s\w()\/.\\'"-:#=&?]*\)/)[0];
_57e=_57e.substring(url.length-1,_57e.length);
url=url.replace(/^[\s\t]*(['"]?)([\w()\/.\\'"-:#=&?]*)\1[\s\t]*?\)/,"$2");
if(url.search(/(file|https?|ftps?):\/\//)==-1){
url=(new dojo.uri.Uri(URI,url).toString());
}
str+=url;
}
return str+_57e;
};
ds.getBackgroundColor=function(node){
node=dojo.byId(node);
var _584;
do{
_584=ds.getStyle(node,"background-color");
if(_584.toLowerCase()=="rgba(0, 0, 0, 0)"){
_584="transparent";
}
if(node==document.getElementsByTagName("body")[0]){
node=null;
break;
}
node=node.parentNode;
}while(node&&dojo.lang.inArray(_584,["transparent",""]));
if(_584=="transparent"){
_584=[255,255,255,0];
}else{
_584=dojo.graphics.color.extractRGB(_584);
}
return _584;
};
ds.getComputedStyle=function(node,_586,_587){
node=dojo.byId(node);
var _586=ds.toSelectorCase(_586);
var _588=ds.toCamelCase(_586);
if(!node||!node.style){
return _587;
}else{
if(document.defaultView){
try{
var cs=document.defaultView.getComputedStyle(node,"");
if(cs){
return cs.getPropertyValue(_586);
}
}
catch(e){
if(node.style.getPropertyValue){
return node.style.getPropertyValue(_586);
}else{
return _587;
}
}
}else{
if(node.currentStyle){
return node.currentStyle[_588];
}
}
}
if(node.style.getPropertyValue){
return node.style.getPropertyValue(_586);
}else{
return _587;
}
};
ds.getStyleProperty=function(node,_58b){
node=dojo.byId(node);
return (node&&node.style?node.style[ds.toCamelCase(_58b)]:undefined);
};
ds.getStyle=function(node,_58d){
var _58e=ds.getStyleProperty(node,_58d);
return (_58e?_58e:ds.getComputedStyle(node,_58d));
};
ds.setStyle=function(node,_590,_591){
node=dojo.byId(node);
if(node&&node.style){
var _592=ds.toCamelCase(_590);
node.style[_592]=_591;
}
};
ds.toCamelCase=function(_593){
var arr=_593.split("-"),cc=arr[0];
for(var i=1;i<arr.length;i++){
cc+=arr[i].charAt(0).toUpperCase()+arr[i].substring(1);
}
return cc;
};
ds.toSelectorCase=function(_596){
return _596.replace(/([A-Z])/g,"-$1").toLowerCase();
};
ds.setOpacity=function setOpacity(node,_598,_599){
node=dojo.byId(node);
if(!_599){
if(_598>=1){
if(h.ie){
ds.clearOpacity(node);
return;
}else{
_598=0.999999;
}
}else{
if(_598<0){
_598=0;
}
}
}
if(h.ie){
if(node.nodeName.toLowerCase()=="tr"){
var tds=node.getElementsByTagName("td");
for(var x=0;x<tds.length;x++){
tds[x].style.filter="Alpha(Opacity="+_598*100+")";
}
}
node.style.filter="Alpha(Opacity="+_598*100+")";
}else{
if(h.moz){
node.style.opacity=_598;
node.style.MozOpacity=_598;
}else{
if(h.safari){
node.style.opacity=_598;
node.style.KhtmlOpacity=_598;
}else{
node.style.opacity=_598;
}
}
}
};
ds.getOpacity=function getOpacity(node){
node=dojo.byId(node);
if(h.ie){
var opac=(node.filters&&node.filters.alpha&&typeof node.filters.alpha.opacity=="number"?node.filters.alpha.opacity:100)/100;
}else{
var opac=node.style.opacity||node.style.MozOpacity||node.style.KhtmlOpacity||1;
}
return opac>=0.999999?1:Number(opac);
};
ds.clearOpacity=function clearOpacity(node){
node=dojo.byId(node);
var ns=node.style;
if(h.ie){
try{
if(node.filters&&node.filters.alpha){
ns.filter="";
}
}
catch(e){
}
}else{
if(h.moz){
ns.opacity=1;
ns.MozOpacity=1;
}else{
if(h.safari){
ns.opacity=1;
ns.KhtmlOpacity=1;
}else{
ns.opacity=1;
}
}
}
};
ds.setStyleAttributes=function(node,_5a1){
var _5a2={"opacity":dojo.style.setOpacity,"content-height":dojo.style.setContentHeight,"content-width":dojo.style.setContentWidth,"outer-height":dojo.style.setOuterHeight,"outer-width":dojo.style.setOuterWidth};
var _5a3=_5a1.replace(/(;)?\s*$/,"").split(";");
for(var i=0;i<_5a3.length;i++){
var _5a5=_5a3[i].split(":");
var name=_5a5[0].replace(/\s*$/,"").replace(/^\s*/,"").toLowerCase();
var _5a7=_5a5[1].replace(/\s*$/,"").replace(/^\s*/,"");
if(dojo.lang.has(_5a2,name)){
_5a2[name](node,_5a7);
}else{
node.style[dojo.style.toCamelCase(name)]=_5a7;
}
}
};
ds._toggle=function(node,_5a9,_5aa){
node=dojo.byId(node);
_5aa(node,!_5a9(node));
return _5a9(node);
};
ds.show=function(node){
node=dojo.byId(node);
if(ds.getStyleProperty(node,"display")=="none"){
ds.setStyle(node,"display",(node.dojoDisplayCache||""));
node.dojoDisplayCache=undefined;
}
};
ds.hide=function(node){
node=dojo.byId(node);
if(typeof node["dojoDisplayCache"]=="undefined"){
var d=ds.getStyleProperty(node,"display");
if(d!="none"){
node.dojoDisplayCache=d;
}
}
ds.setStyle(node,"display","none");
};
ds.setShowing=function(node,_5af){
ds[(_5af?"show":"hide")](node);
};
ds.isShowing=function(node){
return (ds.getStyleProperty(node,"display")!="none");
};
ds.toggleShowing=function(node){
return ds._toggle(node,ds.isShowing,ds.setShowing);
};
ds.displayMap={tr:"",td:"",th:"",img:"inline",span:"inline",input:"inline",button:"inline"};
ds.suggestDisplayByTagName=function(node){
node=dojo.byId(node);
if(node&&node.tagName){
var tag=node.tagName.toLowerCase();
return (tag in ds.displayMap?ds.displayMap[tag]:"block");
}
};
ds.setDisplay=function(node,_5b5){
ds.setStyle(node,"display",(dojo.lang.isString(_5b5)?_5b5:(_5b5?ds.suggestDisplayByTagName(node):"none")));
};
ds.isDisplayed=function(node){
return (ds.getComputedStyle(node,"display")!="none");
};
ds.toggleDisplay=function(node){
return ds._toggle(node,ds.isDisplayed,ds.setDisplay);
};
ds.setVisibility=function(node,_5b9){
ds.setStyle(node,"visibility",(dojo.lang.isString(_5b9)?_5b9:(_5b9?"visible":"hidden")));
};
ds.isVisible=function(node){
return (ds.getComputedStyle(node,"visibility")!="hidden");
};
ds.toggleVisibility=function(node){
return ds._toggle(node,ds.isVisible,ds.setVisibility);
};
ds.toCoordinateArray=function(_5bc,_5bd){
if(dojo.lang.isArray(_5bc)){
while(_5bc.length<4){
_5bc.push(0);
}
while(_5bc.length>4){
_5bc.pop();
}
var ret=_5bc;
}else{
var node=dojo.byId(_5bc);
var pos=ds.getAbsolutePosition(node,_5bd);
var ret=[pos.x,pos.y,ds.getBorderBoxWidth(node),ds.getBorderBoxHeight(node)];
}
ret.x=ret[0];
ret.y=ret[1];
ret.w=ret[2];
ret.h=ret[3];
return ret;
};
})();
dojo.provide("dojo.html");
dojo.require("dojo.lang.func");
dojo.require("dojo.dom");
dojo.require("dojo.style");
dojo.require("dojo.string");
dojo.lang.mixin(dojo.html,dojo.dom);
dojo.lang.mixin(dojo.html,dojo.style);
dojo.html.clearSelection=function(){
try{
if(window["getSelection"]){
if(dojo.render.html.safari){
window.getSelection().collapse();
}else{
window.getSelection().removeAllRanges();
}
}else{
if(document.selection){
if(document.selection.empty){
document.selection.empty();
}else{
if(document.selection.clear){
document.selection.clear();
}
}
}
}
return true;
}
catch(e){
dojo.debug(e);
return false;
}
};
dojo.html.disableSelection=function(_5c1){
_5c1=dojo.byId(_5c1)||document.body;
var h=dojo.render.html;
if(h.mozilla){
_5c1.style.MozUserSelect="none";
}else{
if(h.safari){
_5c1.style.KhtmlUserSelect="none";
}else{
if(h.ie){
_5c1.unselectable="on";
}else{
return false;
}
}
}
return true;
};
dojo.html.enableSelection=function(_5c3){
_5c3=dojo.byId(_5c3)||document.body;
var h=dojo.render.html;
if(h.mozilla){
_5c3.style.MozUserSelect="";
}else{
if(h.safari){
_5c3.style.KhtmlUserSelect="";
}else{
if(h.ie){
_5c3.unselectable="off";
}else{
return false;
}
}
}
return true;
};
dojo.html.selectElement=function(_5c5){
_5c5=dojo.byId(_5c5);
if(document.selection&&document.body.createTextRange){
var _5c6=document.body.createTextRange();
_5c6.moveToElementText(_5c5);
_5c6.select();
}else{
if(window["getSelection"]){
var _5c7=window.getSelection();
if(_5c7["selectAllChildren"]){
_5c7.selectAllChildren(_5c5);
}
}
}
};
dojo.html.selectInputText=function(_5c8){
_5c8=dojo.byId(_5c8);
if(document.selection&&document.body.createTextRange){
var _5c9=_5c8.createTextRange();
_5c9.moveStart("character",0);
_5c9.moveEnd("character",_5c8.value.length);
_5c9.select();
}else{
if(window["getSelection"]){
var _5ca=window.getSelection();
_5c8.setSelectionRange(0,_5c8.value.length);
}
}
_5c8.focus();
};
dojo.html.isSelectionCollapsed=function(){
if(document["selection"]){
return document.selection.createRange().text=="";
}else{
if(window["getSelection"]){
var _5cb=window.getSelection();
if(dojo.lang.isString(_5cb)){
return _5cb=="";
}else{
return _5cb.isCollapsed;
}
}
}
};
dojo.html.getEventTarget=function(evt){
if(!evt){
evt=window.event||{};
}
var t=(evt.srcElement?evt.srcElement:(evt.target?evt.target:null));
while((t)&&(t.nodeType!=1)){
t=t.parentNode;
}
return t;
};
dojo.html.getDocumentWidth=function(){
dojo.deprecated("dojo.html.getDocument*","replaced by dojo.html.getViewport*","0.4");
return dojo.html.getViewportWidth();
};
dojo.html.getDocumentHeight=function(){
dojo.deprecated("dojo.html.getDocument*","replaced by dojo.html.getViewport*","0.4");
return dojo.html.getViewportHeight();
};
dojo.html.getDocumentSize=function(){
dojo.deprecated("dojo.html.getDocument*","replaced of dojo.html.getViewport*","0.4");
return dojo.html.getViewportSize();
};
dojo.html.getViewportWidth=function(){
var w=0;
if(window.innerWidth){
w=window.innerWidth;
}
if(dojo.exists(document,"documentElement.clientWidth")){
var w2=document.documentElement.clientWidth;
if(!w||w2&&w2<w){
w=w2;
}
return w;
}
if(document.body){
return document.body.clientWidth;
}
return 0;
};
dojo.html.getViewportHeight=function(){
if(window.innerHeight){
return window.innerHeight;
}
if(dojo.exists(document,"documentElement.clientHeight")){
return document.documentElement.clientHeight;
}
if(document.body){
return document.body.clientHeight;
}
return 0;
};
dojo.html.getViewportSize=function(){
var ret=[dojo.html.getViewportWidth(),dojo.html.getViewportHeight()];
ret.w=ret[0];
ret.h=ret[1];
return ret;
};
dojo.html.getScrollTop=function(){
return window.pageYOffset||document.documentElement.scrollTop||document.body.scrollTop||0;
};
dojo.html.getScrollLeft=function(){
return window.pageXOffset||document.documentElement.scrollLeft||document.body.scrollLeft||0;
};
dojo.html.getScrollOffset=function(){
var off=[dojo.html.getScrollLeft(),dojo.html.getScrollTop()];
off.x=off[0];
off.y=off[1];
return off;
};
dojo.html.getParentOfType=function(node,type){
dojo.deprecated("dojo.html.getParentOfType","replaced by dojo.html.getParentByType*","0.4");
return dojo.html.getParentByType(node,type);
};
dojo.html.getParentByType=function(node,type){
var _5d6=dojo.byId(node);
type=type.toLowerCase();
while((_5d6)&&(_5d6.nodeName.toLowerCase()!=type)){
if(_5d6==(document["body"]||document["documentElement"])){
return null;
}
_5d6=_5d6.parentNode;
}
return _5d6;
};
dojo.html.getAttribute=function(node,attr){
node=dojo.byId(node);
if((!node)||(!node.getAttribute)){
return null;
}
var ta=typeof attr=="string"?attr:new String(attr);
var v=node.getAttribute(ta.toUpperCase());
if((v)&&(typeof v=="string")&&(v!="")){
return v;
}
if(v&&v.value){
return v.value;
}
if((node.getAttributeNode)&&(node.getAttributeNode(ta))){
return (node.getAttributeNode(ta)).value;
}else{
if(node.getAttribute(ta)){
return node.getAttribute(ta);
}else{
if(node.getAttribute(ta.toLowerCase())){
return node.getAttribute(ta.toLowerCase());
}
}
}
return null;
};
dojo.html.hasAttribute=function(node,attr){
node=dojo.byId(node);
return dojo.html.getAttribute(node,attr)?true:false;
};
dojo.html.getClass=function(node){
node=dojo.byId(node);
if(!node){
return "";
}
var cs="";
if(node.className){
cs=node.className;
}else{
if(dojo.html.hasAttribute(node,"class")){
cs=dojo.html.getAttribute(node,"class");
}
}
return dojo.string.trim(cs);
};
dojo.html.getClasses=function(node){
var c=dojo.html.getClass(node);
return (c=="")?[]:c.split(/\s+/g);
};
dojo.html.hasClass=function(node,_5e2){
return dojo.lang.inArray(dojo.html.getClasses(node),_5e2);
};
dojo.html.prependClass=function(node,_5e4){
_5e4+=" "+dojo.html.getClass(node);
return dojo.html.setClass(node,_5e4);
};
dojo.html.addClass=function(node,_5e6){
if(dojo.html.hasClass(node,_5e6)){
return false;
}
_5e6=dojo.string.trim(dojo.html.getClass(node)+" "+_5e6);
return dojo.html.setClass(node,_5e6);
};
dojo.html.setClass=function(node,_5e8){
node=dojo.byId(node);
var cs=new String(_5e8);
try{
if(typeof node.className=="string"){
node.className=cs;
}else{
if(node.setAttribute){
node.setAttribute("class",_5e8);
node.className=cs;
}else{
return false;
}
}
}
catch(e){
dojo.debug("dojo.html.setClass() failed",e);
}
return true;
};
dojo.html.removeClass=function(node,_5eb,_5ec){
var _5eb=dojo.string.trim(new String(_5eb));
try{
var cs=dojo.html.getClasses(node);
var nca=[];
if(_5ec){
for(var i=0;i<cs.length;i++){
if(cs[i].indexOf(_5eb)==-1){
nca.push(cs[i]);
}
}
}else{
for(var i=0;i<cs.length;i++){
if(cs[i]!=_5eb){
nca.push(cs[i]);
}
}
}
dojo.html.setClass(node,nca.join(" "));
}
catch(e){
dojo.debug("dojo.html.removeClass() failed",e);
}
return true;
};
dojo.html.replaceClass=function(node,_5f1,_5f2){
dojo.html.removeClass(node,_5f2);
dojo.html.addClass(node,_5f1);
};
dojo.html.classMatchType={ContainsAll:0,ContainsAny:1,IsOnly:2};
dojo.html.getElementsByClass=function(_5f3,_5f4,_5f5,_5f6,_5f7){
_5f4=dojo.byId(_5f4)||document;
var _5f8=_5f3.split(/\s+/g);
var _5f9=[];
if(_5f6!=1&&_5f6!=2){
_5f6=0;
}
var _5fa=new RegExp("(\\s|^)(("+_5f8.join(")|(")+"))(\\s|$)");
var _5fb=[];
if(!_5f7&&document.evaluate){
var _5fc=".//"+(_5f5||"*")+"[contains(";
if(_5f6!=dojo.html.classMatchType.ContainsAny){
_5fc+="concat(' ',@class,' '), ' "+_5f8.join(" ') and contains(concat(' ',@class,' '), ' ")+" ')]";
}else{
_5fc+="concat(' ',@class,' '), ' "+_5f8.join(" ')) or contains(concat(' ',@class,' '), ' ")+" ')]";
}
var _5fd=document.evaluate(_5fc,_5f4,null,XPathResult.ANY_TYPE,null);
var _5fe=_5fd.iterateNext();
while(_5fe){
try{
_5fb.push(_5fe);
_5fe=_5fd.iterateNext();
}
catch(e){
break;
}
}
return _5fb;
}else{
if(!_5f5){
_5f5="*";
}
_5fb=_5f4.getElementsByTagName(_5f5);
var node,i=0;
outer:
while(node=_5fb[i++]){
var _600=dojo.html.getClasses(node);
if(_600.length==0){
continue outer;
}
var _601=0;
for(var j=0;j<_600.length;j++){
if(_5fa.test(_600[j])){
if(_5f6==dojo.html.classMatchType.ContainsAny){
_5f9.push(node);
continue outer;
}else{
_601++;
}
}else{
if(_5f6==dojo.html.classMatchType.IsOnly){
continue outer;
}
}
}
if(_601==_5f8.length){
if((_5f6==dojo.html.classMatchType.IsOnly)&&(_601==_600.length)){
_5f9.push(node);
}else{
if(_5f6==dojo.html.classMatchType.ContainsAll){
_5f9.push(node);
}
}
}
}
return _5f9;
}
};
dojo.html.getElementsByClassName=dojo.html.getElementsByClass;
dojo.html.getCursorPosition=function(e){
e=e||window.event;
var _604={x:0,y:0};
if(e.pageX||e.pageY){
_604.x=e.pageX;
_604.y=e.pageY;
}else{
var de=document.documentElement;
var db=document.body;
_604.x=e.clientX+((de||db)["scrollLeft"])-((de||db)["clientLeft"]);
_604.y=e.clientY+((de||db)["scrollTop"])-((de||db)["clientTop"]);
}
return _604;
};
dojo.html.overElement=function(_607,e){
_607=dojo.byId(_607);
var _609=dojo.html.getCursorPosition(e);
with(dojo.html){
var top=getAbsoluteY(_607,true);
var _60b=top+getInnerHeight(_607);
var left=getAbsoluteX(_607,true);
var _60d=left+getInnerWidth(_607);
}
return (_609.x>=left&&_609.x<=_60d&&_609.y>=top&&_609.y<=_60b);
};
dojo.html.setActiveStyleSheet=function(_60e){
var i=0,a,els=document.getElementsByTagName("link");
while(a=els[i++]){
if(a.getAttribute("rel").indexOf("style")!=-1&&a.getAttribute("title")){
a.disabled=true;
if(a.getAttribute("title")==_60e){
a.disabled=false;
}
}
}
};
dojo.html.getActiveStyleSheet=function(){
var i=0,a,els=document.getElementsByTagName("link");
while(a=els[i++]){
if(a.getAttribute("rel").indexOf("style")!=-1&&a.getAttribute("title")&&!a.disabled){
return a.getAttribute("title");
}
}
return null;
};
dojo.html.getPreferredStyleSheet=function(){
var i=0,a,els=document.getElementsByTagName("link");
while(a=els[i++]){
if(a.getAttribute("rel").indexOf("style")!=-1&&a.getAttribute("rel").indexOf("alt")==-1&&a.getAttribute("title")){
return a.getAttribute("title");
}
}
return null;
};
dojo.html.body=function(){
return document.body||document.getElementsByTagName("body")[0];
};
dojo.html.isTag=function(node){
node=dojo.byId(node);
if(node&&node.tagName){
var arr=dojo.lang.map(dojo.lang.toArray(arguments,1),function(a){
return String(a).toLowerCase();
});
return arr[dojo.lang.find(node.tagName.toLowerCase(),arr)]||"";
}
return "";
};
dojo.html.copyStyle=function(_615,_616){
if(dojo.lang.isUndefined(_616.style.cssText)){
_615.setAttribute("style",_616.getAttribute("style"));
}else{
_615.style.cssText=_616.style.cssText;
}
dojo.html.addClass(_615,dojo.html.getClass(_616));
};
dojo.html._callExtrasDeprecated=function(_617,args){
var _619="dojo.html.extras";
dojo.deprecated("dojo.html."+_617,"moved to "+_619,"0.4");
dojo["require"](_619);
return dojo.html[_617].apply(dojo.html,args);
};
dojo.html.createNodesFromText=function(){
return dojo.html._callExtrasDeprecated("createNodesFromText",arguments);
};
dojo.html.gravity=function(){
return dojo.html._callExtrasDeprecated("gravity",arguments);
};
dojo.html.placeOnScreen=function(){
return dojo.html._callExtrasDeprecated("placeOnScreen",arguments);
};
dojo.html.placeOnScreenPoint=function(){
return dojo.html._callExtrasDeprecated("placeOnScreenPoint",arguments);
};
dojo.html.renderedTextContent=function(){
return dojo.html._callExtrasDeprecated("renderedTextContent",arguments);
};
dojo.html.BackgroundIframe=function(){
return dojo.html._callExtrasDeprecated("BackgroundIframe",arguments);
};
dojo.require("dojo.html");
dojo.provide("dojo.html.extras");
dojo.require("dojo.string.extras");
dojo.html.gravity=function(node,e){
node=dojo.byId(node);
var _61c=dojo.html.getCursorPosition(e);
with(dojo.html){
var _61d=getAbsoluteX(node,true)+(getInnerWidth(node)/2);
var _61e=getAbsoluteY(node,true)+(getInnerHeight(node)/2);
}
with(dojo.html.gravity){
return ((_61c.x<_61d?WEST:EAST)|(_61c.y<_61e?NORTH:SOUTH));
}
};
dojo.html.gravity.NORTH=1;
dojo.html.gravity.SOUTH=1<<1;
dojo.html.gravity.EAST=1<<2;
dojo.html.gravity.WEST=1<<3;
dojo.html.renderedTextContent=function(node){
node=dojo.byId(node);
var _620="";
if(node==null){
return _620;
}
for(var i=0;i<node.childNodes.length;i++){
switch(node.childNodes[i].nodeType){
case 1:
case 5:
var _622="unknown";
try{
_622=dojo.style.getStyle(node.childNodes[i],"display");
}
catch(E){
}
switch(_622){
case "block":
case "list-item":
case "run-in":
case "table":
case "table-row-group":
case "table-header-group":
case "table-footer-group":
case "table-row":
case "table-column-group":
case "table-column":
case "table-cell":
case "table-caption":
_620+="\n";
_620+=dojo.html.renderedTextContent(node.childNodes[i]);
_620+="\n";
break;
case "none":
break;
default:
if(node.childNodes[i].tagName&&node.childNodes[i].tagName.toLowerCase()=="br"){
_620+="\n";
}else{
_620+=dojo.html.renderedTextContent(node.childNodes[i]);
}
break;
}
break;
case 3:
case 2:
case 4:
var text=node.childNodes[i].nodeValue;
var _624="unknown";
try{
_624=dojo.style.getStyle(node,"text-transform");
}
catch(E){
}
switch(_624){
case "capitalize":
text=dojo.string.capitalize(text);
break;
case "uppercase":
text=text.toUpperCase();
break;
case "lowercase":
text=text.toLowerCase();
break;
default:
break;
}
switch(_624){
case "nowrap":
break;
case "pre-wrap":
break;
case "pre-line":
break;
case "pre":
break;
default:
text=text.replace(/\s+/," ");
if(/\s$/.test(_620)){
text.replace(/^\s/,"");
}
break;
}
_620+=text;
break;
default:
break;
}
}
return _620;
};
dojo.html.createNodesFromText=function(txt,trim){
if(trim){
txt=dojo.string.trim(txt);
}
var tn=document.createElement("div");
tn.style.visibility="hidden";
document.body.appendChild(tn);
var _628="none";
if((/^<t[dh][\s\r\n>]/i).test(dojo.string.trimStart(txt))){
txt="<table><tbody><tr>"+txt+"</tr></tbody></table>";
_628="cell";
}else{
if((/^<tr[\s\r\n>]/i).test(dojo.string.trimStart(txt))){
txt="<table><tbody>"+txt+"</tbody></table>";
_628="row";
}else{
if((/^<(thead|tbody|tfoot)[\s\r\n>]/i).test(dojo.string.trimStart(txt))){
txt="<table>"+txt+"</table>";
_628="section";
}
}
}
tn.innerHTML=txt;
if(tn["normalize"]){
tn.normalize();
}
var _629=null;
switch(_628){
case "cell":
_629=tn.getElementsByTagName("tr")[0];
break;
case "row":
_629=tn.getElementsByTagName("tbody")[0];
break;
case "section":
_629=tn.getElementsByTagName("table")[0];
break;
default:
_629=tn;
break;
}
var _62a=[];
for(var x=0;x<_629.childNodes.length;x++){
_62a.push(_629.childNodes[x].cloneNode(true));
}
tn.style.display="none";
document.body.removeChild(tn);
return _62a;
};
dojo.html.placeOnScreen=function(node,_62d,_62e,_62f,_630){
if(dojo.lang.isArray(_62d)){
_630=_62f;
_62f=_62e;
_62e=_62d[1];
_62d=_62d[0];
}
if(!isNaN(_62f)){
_62f=[Number(_62f),Number(_62f)];
}else{
if(!dojo.lang.isArray(_62f)){
_62f=[0,0];
}
}
var _631=dojo.html.getScrollOffset();
var view=dojo.html.getViewportSize();
node=dojo.byId(node);
var w=node.offsetWidth+_62f[0];
var h=node.offsetHeight+_62f[1];
if(_630){
_62d-=_631.x;
_62e-=_631.y;
}
var x=_62d+w;
if(x>view.w){
x=view.w-w;
}else{
x=_62d;
}
x=Math.max(_62f[0],x)+_631.x;
var y=_62e+h;
if(y>view.h){
y=view.h-h;
}else{
y=_62e;
}
y=Math.max(_62f[1],y)+_631.y;
node.style.left=x+"px";
node.style.top=y+"px";
var ret=[x,y];
ret.x=x;
ret.y=y;
return ret;
};
dojo.html.placeOnScreenPoint=function(node,_639,_63a,_63b,_63c){
if(dojo.lang.isArray(_639)){
_63c=_63b;
_63b=_63a;
_63a=_639[1];
_639=_639[0];
}
if(!isNaN(_63b)){
_63b=[Number(_63b),Number(_63b)];
}else{
if(!dojo.lang.isArray(_63b)){
_63b=[0,0];
}
}
var _63d=dojo.html.getScrollOffset();
var view=dojo.html.getViewportSize();
node=dojo.byId(node);
var _63f=node.style.display;
node.style.display="";
var w=dojo.style.getInnerWidth(node);
var h=dojo.style.getInnerHeight(node);
node.style.display=_63f;
if(_63c){
_639-=_63d.x;
_63a-=_63d.y;
}
var x=-1,y=-1;
if((_639+_63b[0])+w<=view.w&&(_63a+_63b[1])+h<=view.h){
x=(_639+_63b[0]);
y=(_63a+_63b[1]);
}
if((x<0||y<0)&&(_639-_63b[0])<=view.w&&(_63a+_63b[1])+h<=view.h){
x=(_639-_63b[0])-w;
y=(_63a+_63b[1]);
}
if((x<0||y<0)&&(_639+_63b[0])+w<=view.w&&(_63a-_63b[1])<=view.h){
x=(_639+_63b[0]);
y=(_63a-_63b[1])-h;
}
if((x<0||y<0)&&(_639-_63b[0])<=view.w&&(_63a-_63b[1])<=view.h){
x=(_639-_63b[0])-w;
y=(_63a-_63b[1])-h;
}
if(x<0||y<0||(x+w>view.w)||(y+h>view.h)){
return dojo.html.placeOnScreen(node,_639,_63a,_63b,_63c);
}
x+=_63d.x;
y+=_63d.y;
node.style.left=x+"px";
node.style.top=y+"px";
var ret=[x,y];
ret.x=x;
ret.y=y;
return ret;
};
dojo.html.BackgroundIframe=function(node){
if(dojo.render.html.ie55||dojo.render.html.ie60){
var html="<iframe "+"style='position: absolute; left: 0px; top: 0px; width: 100%; height: 100%;"+"z-index: -1; filter:Alpha(Opacity=\"0\");' "+">";
this.iframe=document.createElement(html);
if(node){
node.appendChild(this.iframe);
this.domNode=node;
}else{
document.body.appendChild(this.iframe);
this.iframe.style.display="none";
}
}
};
dojo.lang.extend(dojo.html.BackgroundIframe,{iframe:null,onResized:function(){
if(this.iframe&&this.domNode&&this.domNode.parentElement){
var w=dojo.style.getOuterWidth(this.domNode);
var h=dojo.style.getOuterHeight(this.domNode);
if(w==0||h==0){
dojo.lang.setTimeout(this,this.onResized,50);
return;
}
var s=this.iframe.style;
s.width=w+"px";
s.height=h+"px";
}
},size:function(node){
if(!this.iframe){
return;
}
var _64a=dojo.style.toCoordinateArray(node,true);
var s=this.iframe.style;
s.width=_64a.w+"px";
s.height=_64a.h+"px";
s.left=_64a.x+"px";
s.top=_64a.y+"px";
},setZIndex:function(node){
if(!this.iframe){
return;
}
if(dojo.dom.isNode(node)){
this.iframe.style.zIndex=dojo.html.getStyle(node,"z-index")-1;
}else{
if(!isNaN(node)){
this.iframe.style.zIndex=node;
}
}
},show:function(){
if(!this.iframe){
return;
}
this.iframe.style.display="block";
},hide:function(){
if(!this.ie){
return;
}
var s=this.iframe.style;
s.display="none";
},remove:function(){
dojo.dom.removeNode(this.iframe);
}});
dojo.provide("dojo.lfx.Animation");
dojo.provide("dojo.lfx.Line");
dojo.require("dojo.lang.func");
dojo.lfx.Line=function(_64e,end){
this.start=_64e;
this.end=end;
if(dojo.lang.isArray(_64e)){
var diff=[];
dojo.lang.forEach(this.start,function(s,i){
diff[i]=this.end[i]-s;
},this);
this.getValue=function(n){
var res=[];
dojo.lang.forEach(this.start,function(s,i){
res[i]=(diff[i]*n)+s;
},this);
return res;
};
}else{
var diff=end-_64e;
this.getValue=function(n){
return (diff*n)+this.start;
};
}
};
dojo.lfx.easeIn=function(n){
return Math.pow(n,3);
};
dojo.lfx.easeOut=function(n){
return (1-Math.pow(1-n,3));
};
dojo.lfx.easeInOut=function(n){
return ((3*Math.pow(n,2))-(2*Math.pow(n,3)));
};
dojo.lfx.IAnimation=function(){
};
dojo.lang.extend(dojo.lfx.IAnimation,{curve:null,duration:1000,easing:null,repeatCount:0,rate:25,handler:null,beforeBegin:null,onBegin:null,onAnimate:null,onEnd:null,onPlay:null,onPause:null,onStop:null,play:null,pause:null,stop:null,fire:function(evt,args){
if(this[evt]){
this[evt].apply(this,(args||[]));
}
},_active:false,_paused:false});
dojo.lfx.Animation=function(_65d,_65e,_65f,_660,_661,rate){
dojo.lfx.IAnimation.call(this);
if(dojo.lang.isNumber(_65d)||(!_65d&&_65e.getValue)){
rate=_661;
_661=_660;
_660=_65f;
_65f=_65e;
_65e=_65d;
_65d=null;
}else{
if(_65d.getValue||dojo.lang.isArray(_65d)){
rate=_660;
_661=_65f;
_660=_65e;
_65f=_65d;
_65e=null;
_65d=null;
}
}
if(dojo.lang.isArray(_65f)){
this.curve=new dojo.lfx.Line(_65f[0],_65f[1]);
}else{
this.curve=_65f;
}
if(_65e!=null&&_65e>0){
this.duration=_65e;
}
if(_661){
this.repeatCount=_661;
}
if(rate){
this.rate=rate;
}
if(_65d){
this.handler=_65d.handler;
this.beforeBegin=_65d.beforeBegin;
this.onBegin=_65d.onBegin;
this.onEnd=_65d.onEnd;
this.onPlay=_65d.onPlay;
this.onPause=_65d.onPause;
this.onStop=_65d.onStop;
this.onAnimate=_65d.onAnimate;
}
if(_660&&dojo.lang.isFunction(_660)){
this.easing=_660;
}
};
dojo.inherits(dojo.lfx.Animation,dojo.lfx.IAnimation);
dojo.lang.extend(dojo.lfx.Animation,{_startTime:null,_endTime:null,_timer:null,_percent:0,_startRepeatCount:0,play:function(_663,_664){
if(_664){
clearTimeout(this._timer);
this._active=false;
this._paused=false;
this._percent=0;
}else{
if(this._active&&!this._paused){
return this;
}
}
this.fire("handler",["beforeBegin"]);
this.fire("beforeBegin");
if(_663>0){
setTimeout(dojo.lang.hitch(this,function(){
this.play(null,_664);
}),_663);
return this;
}
this._startTime=new Date().valueOf();
if(this._paused){
this._startTime-=(this.duration*this._percent/100);
}
this._endTime=this._startTime+this.duration;
this._active=true;
this._paused=false;
var step=this._percent/100;
var _666=this.curve.getValue(step);
if(this._percent==0){
if(!this._startRepeatCount){
this._startRepeatCount=this.repeatCount;
}
this.fire("handler",["begin",_666]);
this.fire("onBegin",[_666]);
}
this.fire("handler",["play",_666]);
this.fire("onPlay",[_666]);
this._cycle();
return this;
},pause:function(){
clearTimeout(this._timer);
if(!this._active){
return this;
}
this._paused=true;
var _667=this.curve.getValue(this._percent/100);
this.fire("handler",["pause",_667]);
this.fire("onPause",[_667]);
return this;
},gotoPercent:function(pct,_669){
clearTimeout(this._timer);
this._active=true;
this._paused=true;
this._percent=pct;
if(_669){
this.play();
}
},stop:function(_66a){
clearTimeout(this._timer);
var step=this._percent/100;
if(_66a){
step=1;
}
var _66c=this.curve.getValue(step);
this.fire("handler",["stop",_66c]);
this.fire("onStop",[_66c]);
this._active=false;
this._paused=false;
return this;
},status:function(){
if(this._active){
return this._paused?"paused":"playing";
}else{
return "stopped";
}
},_cycle:function(){
clearTimeout(this._timer);
if(this._active){
var curr=new Date().valueOf();
var step=(curr-this._startTime)/(this._endTime-this._startTime);
if(step>=1){
step=1;
this._percent=100;
}else{
this._percent=step*100;
}
if((this.easing)&&(dojo.lang.isFunction(this.easing))){
step=this.easing(step);
}
var _66f=this.curve.getValue(step);
this.fire("handler",["animate",_66f]);
this.fire("onAnimate",[_66f]);
if(step<1){
this._timer=setTimeout(dojo.lang.hitch(this,"_cycle"),this.rate);
}else{
this._active=false;
this.fire("handler",["end"]);
this.fire("onEnd");
if(this.repeatCount>0){
this.repeatCount--;
this.play(null,true);
}else{
if(this.repeatCount==-1){
this.play(null,true);
}else{
if(this._startRepeatCount){
this.repeatCount=this._startRepeatCount;
this._startRepeatCount=0;
}
}
}
}
}
return this;
}});
dojo.lfx.Combine=function(){
dojo.lfx.IAnimation.call(this);
this._anims=[];
this._animsEnded=0;
var _670=arguments;
if(_670.length==1&&(dojo.lang.isArray(_670[0])||dojo.lang.isArrayLike(_670[0]))){
_670=_670[0];
}
var _671=this;
dojo.lang.forEach(_670,function(anim){
_671._anims.push(anim);
var _673=(anim["onEnd"])?dojo.lang.hitch(anim,"onEnd"):function(){
};
anim.onEnd=function(){
_673();
_671._onAnimsEnded();
};
});
};
dojo.inherits(dojo.lfx.Combine,dojo.lfx.IAnimation);
dojo.lang.extend(dojo.lfx.Combine,{_animsEnded:0,play:function(_674,_675){
if(!this._anims.length){
return this;
}
this.fire("beforeBegin");
if(_674>0){
setTimeout(dojo.lang.hitch(this,function(){
this.play(null,_675);
}),_674);
return this;
}
if(_675||this._anims[0].percent==0){
this.fire("onBegin");
}
this.fire("onPlay");
this._animsCall("play",null,_675);
return this;
},pause:function(){
this.fire("onPause");
this._animsCall("pause");
return this;
},stop:function(_676){
this.fire("onStop");
this._animsCall("stop",_676);
return this;
},_onAnimsEnded:function(){
this._animsEnded++;
if(this._animsEnded>=this._anims.length){
this.fire("onEnd");
}
return this;
},_animsCall:function(_677){
var args=[];
if(arguments.length>1){
for(var i=1;i<arguments.length;i++){
args.push(arguments[i]);
}
}
var _67a=this;
dojo.lang.forEach(this._anims,function(anim){
anim[_677](args);
},_67a);
return this;
}});
dojo.lfx.Chain=function(){
dojo.lfx.IAnimation.call(this);
this._anims=[];
this._currAnim=-1;
var _67c=arguments;
if(_67c.length==1&&(dojo.lang.isArray(_67c[0])||dojo.lang.isArrayLike(_67c[0]))){
_67c=_67c[0];
}
var _67d=this;
dojo.lang.forEach(_67c,function(anim,i,_680){
_67d._anims.push(anim);
var _681=(anim["onEnd"])?dojo.lang.hitch(anim,"onEnd"):function(){
};
if(i<_680.length-1){
anim.onEnd=function(){
_681();
_67d._playNext();
};
}else{
anim.onEnd=function(){
_681();
_67d.fire("onEnd");
};
}
},_67d);
};
dojo.inherits(dojo.lfx.Chain,dojo.lfx.IAnimation);
dojo.lang.extend(dojo.lfx.Chain,{_currAnim:-1,play:function(_682,_683){
if(!this._anims.length){
return this;
}
if(_683||!this._anims[this._currAnim]){
this._currAnim=0;
}
var _684=this._anims[this._currAnim];
this.fire("beforeBegin");
if(_682>0){
setTimeout(dojo.lang.hitch(this,function(){
this.play(null,_683);
}),_682);
return this;
}
if(_684){
if(this._currAnim==0){
this.fire("handler",["begin",this._currAnim]);
this.fire("onBegin",[this._currAnim]);
}
this.fire("onPlay",[this._currAnim]);
_684.play(null,_683);
}
return this;
},pause:function(){
if(this._anims[this._currAnim]){
this._anims[this._currAnim].pause();
this.fire("onPause",[this._currAnim]);
}
return this;
},playPause:function(){
if(this._anims.length==0){
return this;
}
if(this._currAnim==-1){
this._currAnim=0;
}
var _685=this._anims[this._currAnim];
if(_685){
if(!_685._active||_685._paused){
this.play();
}else{
this.pause();
}
}
return this;
},stop:function(){
var _686=this._anims[this._currAnim];
if(_686){
_686.stop();
this.fire("onStop",[this._currAnim]);
}
return _686;
},_playNext:function(){
if(this._currAnim==-1||this._anims.length==0){
return this;
}
this._currAnim++;
if(this._anims[this._currAnim]){
this._anims[this._currAnim].play(null,true);
}
return this;
}});
dojo.lfx.combine=function(){
var _687=arguments;
if(dojo.lang.isArray(arguments[0])){
_687=arguments[0];
}
return new dojo.lfx.Combine(_687);
};
dojo.lfx.chain=function(){
var _688=arguments;
if(dojo.lang.isArray(arguments[0])){
_688=arguments[0];
}
return new dojo.lfx.Chain(_688);
};
dojo.provide("dojo.lfx.html");
dojo.require("dojo.lfx.Animation");
dojo.require("dojo.html");
dojo.lfx.html._byId=function(_689){
if(!_689){
return [];
}
if(dojo.lang.isArray(_689)){
if(!_689.alreadyChecked){
var n=[];
dojo.lang.forEach(_689,function(node){
n.push(dojo.byId(node));
});
n.alreadyChecked=true;
return n;
}else{
return _689;
}
}else{
var n=[];
n.push(dojo.byId(_689));
n.alreadyChecked=true;
return n;
}
};
dojo.lfx.html.propertyAnimation=function(_68c,_68d,_68e,_68f){
_68c=dojo.lfx.html._byId(_68c);
if(_68c.length==1){
dojo.lang.forEach(_68d,function(prop){
if(typeof prop["start"]=="undefined"){
if(prop.property!="opacity"){
prop.start=parseInt(dojo.style.getComputedStyle(_68c[0],prop.property));
}else{
prop.start=dojo.style.getOpacity(_68c[0]);
}
}
});
}
var _691=function(_692){
var _693=new Array(_692.length);
for(var i=0;i<_692.length;i++){
_693[i]=Math.round(_692[i]);
}
return _693;
};
var _695=function(n,_697){
n=dojo.byId(n);
if(!n||!n.style){
return;
}
for(var s in _697){
if(s=="opacity"){
dojo.style.setOpacity(n,_697[s]);
}else{
n.style[s]=_697[s];
}
}
};
var _699=function(_69a){
this._properties=_69a;
this.diffs=new Array(_69a.length);
dojo.lang.forEach(_69a,function(prop,i){
if(dojo.lang.isArray(prop.start)){
this.diffs[i]=null;
}else{
if(prop.start instanceof dojo.graphics.color.Color){
prop.startRgb=prop.start.toRgb();
prop.endRgb=prop.end.toRgb();
}else{
this.diffs[i]=prop.end-prop.start;
}
}
},this);
this.getValue=function(n){
var ret={};
dojo.lang.forEach(this._properties,function(prop,i){
var _6a1=null;
if(dojo.lang.isArray(prop.start)){
}else{
if(prop.start instanceof dojo.graphics.color.Color){
_6a1=(prop.units||"rgb")+"(";
for(var j=0;j<prop.startRgb.length;j++){
_6a1+=Math.round(((prop.endRgb[j]-prop.startRgb[j])*n)+prop.startRgb[j])+(j<prop.startRgb.length-1?",":"");
}
_6a1+=")";
}else{
_6a1=((this.diffs[i])*n)+prop.start+(prop.property!="opacity"?prop.units||"px":"");
}
}
ret[dojo.style.toCamelCase(prop.property)]=_6a1;
},this);
return ret;
};
};
var anim=new dojo.lfx.Animation({onAnimate:function(_6a4){
dojo.lang.forEach(_68c,function(node){
_695(node,_6a4);
});
}},_68e,new _699(_68d),_68f);
return anim;
};
dojo.lfx.html._makeFadeable=function(_6a6){
var _6a7=function(node){
if(dojo.render.html.ie){
if((node.style.zoom.length==0)&&(dojo.style.getStyle(node,"zoom")=="normal")){
node.style.zoom="1";
}
if((node.style.width.length==0)&&(dojo.style.getStyle(node,"width")=="auto")){
node.style.width="auto";
}
}
};
if(dojo.lang.isArrayLike(_6a6)){
dojo.lang.forEach(_6a6,_6a7);
}else{
_6a7(_6a6);
}
};
dojo.lfx.html.fadeIn=function(_6a9,_6aa,_6ab,_6ac){
_6a9=dojo.lfx.html._byId(_6a9);
dojo.lfx.html._makeFadeable(_6a9);
var anim=dojo.lfx.propertyAnimation(_6a9,[{property:"opacity",start:dojo.style.getOpacity(_6a9[0]),end:1}],_6aa,_6ab);
if(_6ac){
var _6ae=(anim["onEnd"])?dojo.lang.hitch(anim,"onEnd"):function(){
};
anim.onEnd=function(){
_6ae();
_6ac(_6a9,anim);
};
}
return anim;
};
dojo.lfx.html.fadeOut=function(_6af,_6b0,_6b1,_6b2){
_6af=dojo.lfx.html._byId(_6af);
dojo.lfx.html._makeFadeable(_6af);
var anim=dojo.lfx.propertyAnimation(_6af,[{property:"opacity",start:dojo.style.getOpacity(_6af[0]),end:0}],_6b0,_6b1);
if(_6b2){
var _6b4=(anim["onEnd"])?dojo.lang.hitch(anim,"onEnd"):function(){
};
anim.onEnd=function(){
_6b4();
_6b2(_6af,anim);
};
}
return anim;
};
dojo.lfx.html.fadeShow=function(_6b5,_6b6,_6b7,_6b8){
var anim=dojo.lfx.html.fadeIn(_6b5,_6b6,_6b7,_6b8);
var _6ba=(anim["beforeBegin"])?dojo.lang.hitch(anim,"beforeBegin"):function(){
};
anim.beforeBegin=function(){
_6ba();
if(dojo.lang.isArrayLike(_6b5)){
dojo.lang.forEach(_6b5,dojo.style.show);
}else{
dojo.style.show(_6b5);
}
};
return anim;
};
dojo.lfx.html.fadeHide=function(_6bb,_6bc,_6bd,_6be){
var anim=dojo.lfx.html.fadeOut(_6bb,_6bc,_6bd,function(){
if(dojo.lang.isArrayLike(_6bb)){
dojo.lang.forEach(_6bb,dojo.style.hide);
}else{
dojo.style.hide(_6bb);
}
if(_6be){
_6be(_6bb,anim);
}
});
return anim;
};
dojo.lfx.html.wipeIn=function(_6c0,_6c1,_6c2,_6c3){
_6c0=dojo.lfx.html._byId(_6c0);
var _6c4=[];
dojo.lang.forEach(_6c0,function(node){
var _6c6=dojo.style.getStyle(node,"overflow");
if(_6c6=="visible"){
node.style.overflow="hidden";
}
node.style.height="0px";
dojo.style.show(node);
var anim=dojo.lfx.propertyAnimation(node,[{property:"height",start:0,end:node.scrollHeight}],_6c1,_6c2);
var _6c8=(anim["onEnd"])?dojo.lang.hitch(anim,"onEnd"):function(){
};
anim.onEnd=function(){
_6c8();
node.style.overflow=_6c6;
node.style.height="auto";
if(_6c3){
_6c3(node,anim);
}
};
_6c4.push(anim);
});
if(_6c0.length>1){
return dojo.lfx.combine(_6c4);
}else{
return _6c4[0];
}
};
dojo.lfx.html.wipeOut=function(_6c9,_6ca,_6cb,_6cc){
_6c9=dojo.lfx.html._byId(_6c9);
var _6cd=[];
dojo.lang.forEach(_6c9,function(node){
var _6cf=dojo.style.getStyle(node,"overflow");
if(_6cf=="visible"){
node.style.overflow="hidden";
}
dojo.style.show(node);
var anim=dojo.lfx.propertyAnimation(node,[{property:"height",start:dojo.style.getContentBoxHeight(node),end:0}],_6ca,_6cb);
var _6d1=(anim["onEnd"])?dojo.lang.hitch(anim,"onEnd"):function(){
};
anim.onEnd=function(){
_6d1();
dojo.style.hide(node);
node.style.overflow=_6cf;
if(_6cc){
_6cc(node,anim);
}
};
_6cd.push(anim);
});
if(_6c9.length>1){
return dojo.lfx.combine(_6cd);
}else{
return _6cd[0];
}
};
dojo.lfx.html.slideTo=function(_6d2,_6d3,_6d4,_6d5,_6d6){
_6d2=dojo.lfx.html._byId(_6d2);
var _6d7=[];
dojo.lang.forEach(_6d2,function(node){
var top=null;
var left=null;
var init=(function(){
var _6dc=node;
return function(){
top=_6dc.offsetTop;
left=_6dc.offsetLeft;
if(!dojo.style.isPositionAbsolute(_6dc)){
var ret=dojo.style.abs(_6dc,true);
dojo.style.setStyleAttributes(_6dc,"position:absolute;top:"+ret.y+"px;left:"+ret.x+"px;");
top=ret.y;
left=ret.x;
}
};
})();
init();
var anim=dojo.lfx.propertyAnimation(node,[{property:"top",start:top,end:_6d3[0]},{property:"left",start:left,end:_6d3[1]}],_6d4,_6d5);
var _6df=(anim["beforeBegin"])?dojo.lang.hitch(anim,"beforeBegin"):function(){
};
anim.beforeBegin=function(){
_6df();
init();
};
if(_6d6){
var _6e0=(anim["onEnd"])?dojo.lang.hitch(anim,"onEnd"):function(){
};
anim.onEnd=function(){
_6e0();
_6d6(_6d2,anim);
};
}
_6d7.push(anim);
});
if(_6d2.length>1){
return dojo.lfx.combine(_6d7);
}else{
return _6d7[0];
}
};
dojo.lfx.html.slideBy=function(_6e1,_6e2,_6e3,_6e4,_6e5){
_6e1=dojo.lfx.html._byId(_6e1);
var _6e6=[];
dojo.lang.forEach(_6e1,function(node){
var top=null;
var left=null;
var init=(function(){
var _6eb=node;
return function(){
top=node.offsetTop;
left=node.offsetLeft;
if(!dojo.style.isPositionAbsolute(_6eb)){
var ret=dojo.style.abs(_6eb);
dojo.style.setStyleAttributes(_6eb,"position:absolute;top:"+ret.y+"px;left:"+ret.x+"px;");
top=ret.y;
left=ret.x;
}
};
})();
init();
var anim=dojo.lfx.propertyAnimation(node,[{property:"top",start:top,end:top+_6e2[0]},{property:"left",start:left,end:left+_6e2[1]}],_6e3,_6e4);
var _6ee=(anim["beforeBegin"])?dojo.lang.hitch(anim,"beforeBegin"):function(){
};
anim.beforeBegin=function(){
_6ee();
init();
};
if(_6e5){
var _6ef=(anim["onEnd"])?dojo.lang.hitch(anim,"onEnd"):function(){
};
anim.onEnd=function(){
_6ef();
_6e5(_6e1,anim);
};
}
_6e6.push(anim);
});
if(_6e1.length>1){
return dojo.lfx.combine(_6e6);
}else{
return _6e6[0];
}
};
dojo.lfx.html.explode=function(_6f0,_6f1,_6f2,_6f3,_6f4){
_6f0=dojo.byId(_6f0);
_6f1=dojo.byId(_6f1);
var _6f5=dojo.style.toCoordinateArray(_6f0,true);
var _6f6=document.createElement("div");
dojo.html.copyStyle(_6f6,_6f1);
with(_6f6.style){
position="absolute";
display="none";
}
document.body.appendChild(_6f6);
with(_6f1.style){
visibility="hidden";
display="block";
}
var _6f7=dojo.style.toCoordinateArray(_6f1,true);
with(_6f1.style){
display="none";
visibility="visible";
}
var anim=new dojo.lfx.propertyAnimation(_6f6,[{property:"height",start:_6f5[3],end:_6f7[3]},{property:"width",start:_6f5[2],end:_6f7[2]},{property:"top",start:_6f5[1],end:_6f7[1]},{property:"left",start:_6f5[0],end:_6f7[0]},{property:"opacity",start:0.3,end:1}],_6f2,_6f3);
anim.beforeBegin=function(){
dojo.style.setDisplay(_6f6,"block");
};
anim.onEnd=function(){
dojo.style.setDisplay(_6f1,"block");
_6f6.parentNode.removeChild(_6f6);
};
if(_6f4){
var _6f9=(anim["onEnd"])?dojo.lang.hitch(anim,"onEnd"):function(){
};
anim.onEnd=function(){
_6f9();
_6f4(_6f1,anim);
};
}
return anim;
};
dojo.lfx.html.implode=function(_6fa,end,_6fc,_6fd,_6fe){
_6fa=dojo.byId(_6fa);
end=dojo.byId(end);
var _6ff=dojo.style.toCoordinateArray(_6fa,true);
var _700=dojo.style.toCoordinateArray(end,true);
var _701=document.createElement("div");
dojo.html.copyStyle(_701,_6fa);
dojo.style.setOpacity(_701,0.3);
with(_701.style){
position="absolute";
display="none";
}
document.body.appendChild(_701);
var anim=new dojo.lfx.propertyAnimation(_701,[{property:"height",start:_6ff[3],end:_700[3]},{property:"width",start:_6ff[2],end:_700[2]},{property:"top",start:_6ff[1],end:_700[1]},{property:"left",start:_6ff[0],end:_700[0]},{property:"opacity",start:1,end:0.3}],_6fc,_6fd);
anim.beforeBegin=function(){
dojo.style.hide(_6fa);
dojo.style.show(_701);
};
anim.onEnd=function(){
_701.parentNode.removeChild(_701);
};
if(_6fe){
var _703=(anim["onEnd"])?dojo.lang.hitch(anim,"onEnd"):function(){
};
anim.onEnd=function(){
_703();
_6fe(_6fa,anim);
};
}
return anim;
};
dojo.lfx.html.highlight=function(_704,_705,_706,_707,_708){
_704=dojo.lfx.html._byId(_704);
var _709=[];
dojo.lang.forEach(_704,function(node){
var _70b=dojo.style.getBackgroundColor(node);
var bg=dojo.style.getStyle(node,"background-color").toLowerCase();
var _70d=dojo.style.getStyle(node,"background-image");
var _70e=(bg=="transparent"||bg=="rgba(0, 0, 0, 0)");
while(_70b.length>3){
_70b.pop();
}
var rgb=new dojo.graphics.color.Color(_705);
var _710=new dojo.graphics.color.Color(_70b);
var anim=dojo.lfx.propertyAnimation(node,[{property:"background-color",start:rgb,end:_710}],_706,_707);
var _712=(anim["beforeBegin"])?dojo.lang.hitch(anim,"beforeBegin"):function(){
};
anim.beforeBegin=function(){
_712();
if(_70d){
node.style.backgroundImage="none";
}
node.style.backgroundColor="rgb("+rgb.toRgb().join(",")+")";
};
var _713=(anim["onEnd"])?dojo.lang.hitch(anim,"onEnd"):function(){
};
anim.onEnd=function(){
_713();
if(_70d){
node.style.backgroundImage=_70d;
}
if(_70e){
node.style.backgroundColor="transparent";
}
if(_708){
_708(node,anim);
}
};
_709.push(anim);
});
if(_704.length>1){
return dojo.lfx.combine(_709);
}else{
return _709[0];
}
};
dojo.lfx.html.unhighlight=function(_714,_715,_716,_717,_718){
_714=dojo.lfx.html._byId(_714);
var _719=[];
dojo.lang.forEach(_714,function(node){
var _71b=new dojo.graphics.color.Color(dojo.style.getBackgroundColor(node));
var rgb=new dojo.graphics.color.Color(_715);
var _71d=dojo.style.getStyle(node,"background-image");
var anim=dojo.lfx.propertyAnimation(node,[{property:"background-color",start:_71b,end:rgb}],_716,_717);
var _71f=(anim["beforeBegin"])?dojo.lang.hitch(anim,"beforeBegin"):function(){
};
anim.beforeBegin=function(){
_71f();
if(_71d){
node.style.backgroundImage="none";
}
node.style.backgroundColor="rgb("+_71b.toRgb().join(",")+")";
};
var _720=(anim["onEnd"])?dojo.lang.hitch(anim,"onEnd"):function(){
};
anim.onEnd=function(){
_720();
if(_718){
_718(node,anim);
}
};
_719.push(anim);
});
if(_714.length>1){
return dojo.lfx.combine(_719);
}else{
return _719[0];
}
};
dojo.lang.mixin(dojo.lfx,dojo.lfx.html);
dojo.kwCompoundRequire({browser:["dojo.lfx.html"],dashboard:["dojo.lfx.html"]});
dojo.provide("dojo.lfx.*");
dojo.provide("dojo.lfx.toggle");
dojo.require("dojo.lfx.*");
dojo.lfx.toggle.plain={show:function(node,_722,_723,_724){
dojo.style.show(node);
if(dojo.lang.isFunction(_724)){
_724();
}
},hide:function(node,_726,_727,_728){
dojo.style.hide(node);
if(dojo.lang.isFunction(_728)){
_728();
}
}};
dojo.lfx.toggle.fade={show:function(node,_72a,_72b,_72c){
dojo.lfx.fadeShow(node,_72a,_72b,_72c).play();
},hide:function(node,_72e,_72f,_730){
dojo.lfx.fadeHide(node,_72e,_72f,_730).play();
}};
dojo.lfx.toggle.wipe={show:function(node,_732,_733,_734){
dojo.lfx.wipeIn(node,_732,_733,_734).play();
},hide:function(node,_736,_737,_738){
dojo.lfx.wipeOut(node,_736,_737,_738).play();
}};
dojo.lfx.toggle.explode={show:function(node,_73a,_73b,_73c,_73d){
dojo.lfx.explode(_73d||[0,0,0,0],node,_73a,_73b,_73c).play();
},hide:function(node,_73f,_740,_741,_742){
dojo.lfx.implode(node,_742||[0,0,0,0],_73f,_740,_741).play();
}};
dojo.provide("dojo.widget.HtmlWidget");
dojo.require("dojo.widget.DomWidget");
dojo.require("dojo.html");
dojo.require("dojo.html.extras");
dojo.require("dojo.lang.extras");
dojo.require("dojo.lang.func");
dojo.require("dojo.lfx.toggle");
dojo.declare("dojo.widget.HtmlWidget",dojo.widget.DomWidget,{widgetType:"HtmlWidget",templateCssPath:null,templatePath:null,toggle:"plain",toggleDuration:150,animationInProgress:false,initialize:function(args,frag){
},postMixInProperties:function(args,frag){
this.toggleObj=dojo.lfx.toggle[this.toggle.toLowerCase()]||dojo.lfx.toggle.plain;
},getContainerHeight:function(){
dojo.unimplemented("dojo.widget.HtmlWidget.getContainerHeight");
},getContainerWidth:function(){
return this.parent.domNode.offsetWidth;
},setNativeHeight:function(_747){
var ch=this.getContainerHeight();
},createNodesFromText:function(txt,wrap){
return dojo.html.createNodesFromText(txt,wrap);
},destroyRendering:function(_74b){
try{
if(!_74b){
dojo.event.browser.clean(this.domNode);
}
this.domNode.parentNode.removeChild(this.domNode);
delete this.domNode;
}
catch(e){
}
},isShowing:function(){
return dojo.style.isShowing(this.domNode);
},toggleShowing:function(){
if(this.isHidden){
this.show();
}else{
this.hide();
}
},show:function(){
this.animationInProgress=true;
this.isHidden=false;
this.toggleObj.show(this.domNode,this.toggleDuration,null,dojo.lang.hitch(this,this.onShow),this.explodeSrc);
},onShow:function(){
this.animationInProgress=false;
this.checkSize();
},hide:function(){
this.animationInProgress=true;
this.isHidden=true;
this.toggleObj.hide(this.domNode,this.toggleDuration,null,dojo.lang.hitch(this,this.onHide),this.explodeSrc);
},onHide:function(){
this.animationInProgress=false;
},_isResized:function(w,h){
if(!this.isShowing()){
return false;
}
w=w||dojo.style.getOuterWidth(this.domNode);
h=h||dojo.style.getOuterHeight(this.domNode);
if(this.width==w&&this.height==h){
return false;
}
this.width=w;
this.height=h;
return true;
},checkSize:function(){
if(!this._isResized()){
return;
}
this.onResized();
},resizeTo:function(w,h){
if(!this._isResized(w,h)){
return;
}
dojo.style.setOuterWidth(this.domNode,w);
dojo.style.setOuterHeight(this.domNode,h);
this.onResized();
},resizeSoon:function(){
if(this.isShowing()){
dojo.lang.setTimeout(this,this.onResized,0);
}
},onResized:function(){
dojo.lang.forEach(this.children,function(_750){
_750.checkSize();
});
}});
dojo.kwCompoundRequire({common:["dojo.xml.Parse","dojo.widget.Widget","dojo.widget.Parse","dojo.widget.Manager"],browser:["dojo.widget.DomWidget","dojo.widget.HtmlWidget"],dashboard:["dojo.widget.DomWidget","dojo.widget.HtmlWidget"],svg:["dojo.widget.SvgWidget"],rhino:["dojo.widget.SwtWidget"]});
dojo.provide("dojo.widget.*");
if((dojo!=null)&&(dojo.provide!=null)){
dojo.provide("trimpath.template");
}
TrimPath=new Object();
(function(){
if(TrimPath.evalEx==null){
TrimPath.evalEx=function(src){
return eval(src);
};
}
var _752;
if(Array.prototype.pop==null){
Array.prototype.pop=function(){
if(this.length===0){
return _752;
}
return this[--this.length];
};
}
if(Array.prototype.push==null){
Array.prototype.push=function(){
for(var i=0;i<arguments.length;++i){
this[this.length]=arguments[i];
}
return this.length;
};
}
var _754;
var _755;
var _756;
var _757;
var _758;
var _759;
var _75a;
var _75b;
_754=function(_75c){
_75c=_75c.replace(/\t/g,"    ");
_75c=_75c.replace(/\r\n/g,"\n");
_75c=_75c.replace(/\r/g,"\n");
_75c=_75c.replace(/^(\s*\S*(\s+\S+)*)\s*$/,"$1");
return _75c;
};
_755=function(_75d){
_75d=_75d.replace(/^\s+/g,"");
_75d=_75d.replace(/\s+$/g,"");
_75d=_75d.replace(/\s+/g," ");
_75d=_75d.replace(/^(\s*\S*(\s+\S+)*)\s*$/,"$1");
return _75d;
};
_756=function(_75e,_75f,_760){
var expr=_75e[_75f];
if(_75f<=0){
_760.push(expr);
return;
}
var _762=expr.split(":");
_760.push("_MODIFIERS[\"");
_760.push(_762[0]);
_760.push("\"](");
_756(_75e,_75f-1,_760);
if(_762.length>1){
_760.push(",");
_760.push(_762[1]);
}
_760.push(")");
};
_757=function(_763,_764,_765,_766,etc){
var _768=_763.slice(1,-1).split(" ");
var stmt=etc.statementDef[_768[0]];
if(stmt==null){
_75a(_763,_765);
return;
}
if(stmt.delta<0){
if(_764.stack.length<=0){
throw new etc.ParseError(_766,_764.line,"close tag does not match any previous statement: "+_763);
}
_764.stack.pop();
}
if(stmt.delta>0){
_764.stack.push(_763);
}
if(stmt.paramMin!=null&&stmt.paramMin>=_768.length){
throw new etc.ParseError(_766,_764.line,"statement needs more parameters: "+_763);
}
if(stmt.prefixFunc!=null){
_765.push(stmt.prefixFunc(_768,_764,_766,etc));
}else{
_765.push(stmt.prefix);
}
if(stmt.suffix!=null){
if(_768.length<=1){
if(stmt.paramDefault!=null){
_765.push(stmt.paramDefault);
}
}else{
for(var i=1;i<_768.length;i++){
if(i>1){
_765.push(" ");
}
_765.push(_768[i]);
}
}
_765.push(stmt.suffix);
}
};
_758=function(text,_76c){
if(text==null||text.length<=0){
return;
}
text=text.replace(/\\/g,"\\\\");
text=text.replace(/\n/g,"\\n");
text=text.replace(/"/g,"\\\"");
_76c.push("_OUT.write(\"");
_76c.push(text);
_76c.push("\");");
};
_759=function(line,_76e){
var _76f="}";
var _770=-1;
while(_770+_76f.length<line.length){
var _771="${",endMark="}";
var _772=line.indexOf(_771,_770+_76f.length);
if(_772<0){
break;
}
if(line.charAt(_772+2)=="%"){
_771="${%";
endMark="%}";
}
var _773=line.indexOf(endMark,_772+_771.length);
if(_773<0){
break;
}
_758(line.substring(_770+_76f.length,_772),_76e);
var _774=line.substring(_772+_771.length,_773).replace(/\|\|/g,"#@@#").split("|");
for(var k in _774){
if(_774[k].replace){
_774[k]=_774[k].replace(/#@@#/g,"||");
}
}
_76e.push("_OUT.write(");
_756(_774,_774.length-1,_76e);
_76e.push(");");
_770=_773;
_76f=endMark;
}
_758(line.substring(_770+_76f.length),_76e);
};
_75a=function(text,_777){
if(text.length<=0){
return;
}
var _778=0;
var _779=text.length-1;
while(_778<text.length&&(text.charAt(_778)=="\n")){
_778++;
}
while(_779>=0&&(text.charAt(_779)==" "||text.charAt(_779)=="\t")){
_779--;
}
if(_779<_778){
_779=_778;
}
if(_778>0){
_777.push("if (_FLAGS.keepWhitespace == true) _OUT.write(\"");
var s=text.substring(0,_778).replace("\n","\\n");
while(s.charAt(s.length-1)=="\n"){
s=s.substring(0,s.length-1);
}
_777.push(s);
_777.push("\");");
}
var _77b=text.substring(_778,_779+1).split("\n");
for(var i=0;i<_77b.length;i++){
_759(_77b[i],_777);
if(i<_77b.length-1){
_777.push("_OUT.write(\"\\n\");\n");
}
}
if(_779+1<text.length){
_777.push("if (_FLAGS.keepWhitespace == true) _OUT.write(\"");
var s=text.substring(_779+1).replace("\n","\\n");
while(s.charAt(s.length-1)=="\n"){
s=s.substring(0,s.length-1);
}
_777.push(s);
_777.push("\");");
}
};
_75b=function(body,_77e,etc){
body=_754(body);
var _780=["var TrimPath_Template_TEMP = function(_OUT, _CONTEXT, _FLAGS) { with (_CONTEXT) {"];
var _781={stack:[],line:1};
var _782=-1;
while(_782+1<body.length){
var _783=_782;
_783=body.indexOf("{",_783+1);
while(_783>=0){
var _784=body.indexOf("}",_783+1);
var stmt=body.substring(_783,_784);
var _786=stmt.match(/^\{(cdata|minify|eval)/);
if(_786){
var _787=_786[1];
var _788=_783+_787.length+1;
var _789=body.indexOf("}",_788);
if(_789>=0){
var _78a;
if(_789-_788<=0){
_78a="{/"+_787+"}";
}else{
_78a=body.substring(_788+1,_789);
}
var _78b=body.indexOf(_78a,_789+1);
if(_78b>=0){
_75a(body.substring(_782+1,_783),_780);
var _78c=body.substring(_789+1,_78b);
if(_787=="cdata"){
_758(_78c,_780);
}else{
if(_787=="minify"){
_758(_755(_78c),_780);
}else{
if(_787=="eval"){
if(_78c!=null&&_78c.length>0){
_780.push("_OUT.write( (function() { "+_78c+" })() );");
}
}
}
}
_783=_782=_78b+_78a.length-1;
}
}
}else{
if(body.charAt(_783-1)!="$"&&body.charAt(_783-1)!="\\"){
var _78d=(body.charAt(_783+1)=="/"?2:1);
if(body.substring(_783+_78d,_783+10+_78d).search(TrimPath.parseTemplate_etc.statementTag)==0){
break;
}
}
}
_783=body.indexOf("{",_783+1);
}
if(_783<0){
break;
}
var _784=body.indexOf("}",_783+1);
if(_784<0){
break;
}
_75a(body.substring(_782+1,_783),_780);
_757(body.substring(_783,_784+1),_781,_780,_77e,etc);
_782=_784;
}
_75a(body.substring(_782+1),_780);
if(_781.stack.length!=0){
throw new etc.ParseError(_77e,_781.line,"unclosed, unmatched statement(s): "+_781.stack.join(","));
}
_780.push("}}; TrimPath_Template_TEMP");
return _780.join("");
};
TrimPath.parseTemplate=function(_78e,_78f,_790){
if(_790==null){
_790=TrimPath.parseTemplate_etc;
}
var _791=_75b(_78e,_78f,_790);
var func=TrimPath.evalEx(_791,_78f,1);
if(func!=null){
return new _790.Template(_78f,_78e,_791,func,_790);
}
return null;
};
try{
String.prototype.process=function(_793,_794){
var _795=TrimPath.parseTemplate(this,null);
if(_795!=null){
return _795.process(_793,_794);
}
return this;
};
}
catch(e){
}
TrimPath.parseTemplate_etc={};
TrimPath.parseTemplate_etc.statementTag="forelse|for|if|elseif|else|var|macro";
TrimPath.parseTemplate_etc.statementDef={"if":{delta:1,prefix:"if (",suffix:") {",paramMin:1},"else":{delta:0,prefix:"} else {"},"elseif":{delta:0,prefix:"} else if (",suffix:") {",paramDefault:"true"},"/if":{delta:-1,prefix:"}"},"for":{delta:1,paramMin:3,prefixFunc:function(_796,_797,_798,etc){
if(_796[2]!="in"){
throw new etc.ParseError(_798,_797.line,"bad for loop statement: "+_796.join(" "));
}
var _79a=_796[1];
var _79b="__LIST__"+_79a;
return ["var ",_79b," = ",_796[3],";","var __LENGTH_STACK__;","if (typeof(__LENGTH_STACK__) == 'undefined' || !__LENGTH_STACK__.length) __LENGTH_STACK__ = new Array();","__LENGTH_STACK__[__LENGTH_STACK__.length] = 0;","if ((",_79b,") != null) { ","var ",_79a,"_ct = 0;","for (var ",_79a,"_index in ",_79b,") { ",_79a,"_ct++;","if (typeof(",_79b,"[",_79a,"_index]) == 'function') {continue;}","__LENGTH_STACK__[__LENGTH_STACK__.length - 1]++;","var ",_79a," = ",_79b,"[",_79a,"_index];"].join("");
}},"forelse":{delta:0,prefix:"} } if (__LENGTH_STACK__[__LENGTH_STACK__.length - 1] == 0) { if (",suffix:") {",paramDefault:"true"},"/for":{delta:-1,prefix:"} }; delete __LENGTH_STACK__[__LENGTH_STACK__.length - 1];"},"var":{delta:0,prefix:"var ",suffix:";"},"macro":{delta:1,prefixFunc:function(_79c,_79d,_79e,etc){
var _7a0=_79c[1].split("(")[0];
return ["var ",_7a0," = function",_79c.slice(1).join(" ").substring(_7a0.length),"{ var _OUT_arr = []; var _OUT = { write: function(m) { if (m) _OUT_arr.push(m); } }; "].join("");
}},"/macro":{delta:-1,prefix:" return _OUT_arr.join(''); };"}};
TrimPath.parseTemplate_etc.modifierDef={"eat":function(v){
return "";
},"escape":function(s){
return String(s).replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;");
},"capitalize":function(s){
return String(s).toUpperCase();
},"default":function(s,d){
return s!=null?s:d;
}};
TrimPath.parseTemplate_etc.modifierDef.h=TrimPath.parseTemplate_etc.modifierDef.escape;
TrimPath.parseTemplate_etc.Template=function(_7a6,_7a7,_7a8,func,etc){
this.process=function(_7ab,_7ac){
if(_7ab==null){
_7ab={};
}
if(_7ab._MODIFIERS==null){
_7ab._MODIFIERS={};
}
if(_7ab.defined==null){
_7ab.defined=function(str){
return (_7ab[str]!=undefined);
};
}
for(var k in etc.modifierDef){
if(_7ab._MODIFIERS[k]==null){
_7ab._MODIFIERS[k]=etc.modifierDef[k];
}
}
if(_7ac==null){
_7ac={};
}
var _7af=[];
var _7b0={write:function(m){
_7af.push(m);
}};
try{
func(_7b0,_7ab,_7ac);
}
catch(e){
if(_7ac.throwExceptions==true){
throw e;
}
var _7b2=new String(_7af.join("")+"[ERROR: "+e.toString()+(e.message?"; "+e.message:"")+"]");
_7b2["exception"]=e;
return _7b2;
}
return _7af.join("");
};
this.name=_7a6;
this.source=_7a7;
this.sourceFunc=_7a8;
this.toString=function(){
return "TrimPath.Template ["+_7a6+"]";
};
};
TrimPath.parseTemplate_etc.ParseError=function(name,line,_7b5){
this.name=name;
this.line=line;
this.message=_7b5;
};
TrimPath.parseTemplate_etc.ParseError.prototype.toString=function(){
return ("TrimPath template ParseError in "+this.name+": line "+this.line+", "+this.message);
};
TrimPath.parseDOMTemplate=function(_7b6,_7b7,_7b8){
if(_7b7==null){
_7b7=document;
}
var _7b9=_7b7.getElementById(_7b6);
var _7ba=_7b9.value;
if(_7ba==null){
_7ba=_7b9.innerHTML;
}
_7ba=_7ba.replace(/&lt;/g,"<").replace(/&gt;/g,">");
return TrimPath.parseTemplate(_7ba,_7b6,_7b8);
};
TrimPath.processDOMTemplate=function(_7bb,_7bc,_7bd,_7be,_7bf){
return TrimPath.parseDOMTemplate(_7bb,_7be,_7bf).process(_7bc,_7bd);
};
})();
dojo.provide("dojo.AdapterRegistry");
dojo.require("dojo.lang.func");
dojo.AdapterRegistry=function(){
this.pairs=[];
};
dojo.lang.extend(dojo.AdapterRegistry,{register:function(name,_7c1,wrap,_7c3){
if(_7c3){
this.pairs.unshift([name,_7c1,wrap]);
}else{
this.pairs.push([name,_7c1,wrap]);
}
},match:function(){
for(var i=0;i<this.pairs.length;i++){
var pair=this.pairs[i];
if(pair[1].apply(this,arguments)){
return pair[2].apply(this,arguments);
}
}
throw new Error("No match found");
},unregister:function(name){
for(var i=0;i<this.pairs.length;i++){
var pair=this.pairs[i];
if(pair[0]==name){
this.pairs.splice(i,1);
return true;
}
}
return false;
}});
dojo.provide("dojo.json");
dojo.require("dojo.lang.func");
dojo.require("dojo.string.extras");
dojo.require("dojo.AdapterRegistry");
dojo.json={jsonRegistry:new dojo.AdapterRegistry(),register:function(name,_7ca,wrap,_7cc){
dojo.json.jsonRegistry.register(name,_7ca,wrap,_7cc);
},evalJson:function(json){
try{
return eval("("+json+")");
}
catch(e){
dojo.debug(e);
return json;
}
},evalJSON:function(json){
dojo.deprecated("dojo.json.evalJSON","use dojo.json.evalJson","0.4");
return this.evalJson(json);
},serialize:function(o){
var _7d0=typeof (o);
if(_7d0=="undefined"){
return "undefined";
}else{
if((_7d0=="number")||(_7d0=="boolean")){
return o+"";
}else{
if(o===null){
return "null";
}
}
}
if(_7d0=="string"){
return dojo.string.escapeString(o);
}
var me=arguments.callee;
var _7d2;
if(typeof (o.__json__)=="function"){
_7d2=o.__json__();
if(o!==_7d2){
return me(_7d2);
}
}
if(typeof (o.json)=="function"){
_7d2=o.json();
if(o!==_7d2){
return me(_7d2);
}
}
if(_7d0!="function"&&typeof (o.length)=="number"){
var res=[];
for(var i=0;i<o.length;i++){
var val=me(o[i]);
if(typeof (val)!="string"){
val="undefined";
}
res.push(val);
}
return "["+res.join(",")+"]";
}
try{
window.o=o;
_7d2=dojo.json.jsonRegistry.match(o);
return me(_7d2);
}
catch(e){
}
if(_7d0=="function"){
return null;
}
res=[];
for(var k in o){
var _7d7;
if(typeof (k)=="number"){
_7d7="\""+k+"\"";
}else{
if(typeof (k)=="string"){
_7d7=dojo.string.escapeString(k);
}else{
continue;
}
}
val=me(o[k]);
if(typeof (val)!="string"){
continue;
}
res.push(_7d7+":"+val);
}
return "{"+res.join(",")+"}";
}};
dojo.provide("ning.system");
dojo.require("dojo.io");
dojo.require("dojo.json");
dojo.require("dojo.string");
ning.system.buildUrl=function(args){
if(!args){
args={};
}
if(dojo.lang.isString(args)){
args={subdomain:args};
}
var _7d9=args.ssl?"https":"http";
var _7da=args.subdomain||"www";
var path=args.path||"/";
var _7dc=args.query||"";
if(dojo.lang.isString(_7dc)){
if(_7dc.length>0){
_7dc="?"+_7dc;
}
}else{
var tmp="";
for(var i in _7dc){
tmp+=dojo.string.substituteParams("%{separator}%{key}=%{value}",{separator:tmp.length?"&":"?",key:encodeURIComponent(i),value:encodeURIComponent(_7dc[i])});
}
_7dc=tmp;
}
return dojo.string.substituteParams("%{scheme}://%{subdomain}.%{host}%{port}%{path}%{query}",{scheme:_7d9,subdomain:_7da,path:path,query:_7dc,host:ning._.domains.base,port:args.ssl?(ning._.domains.ports.ssl==443?"":":"+ning._.domains.ports.ssl):(ning._.domains.ports.http==80?"":":"+ning._.domains.ports.http)});
};
ning.system.generateCaptcha=function(_7df){
dojo.io.bind({url:"/xn/rest/1.0/captcha",load:function(type,data,evt){
_7df(data.captcha);
},mimetype:"text/json",encoding:"utf-8",error:function(type,err){
alert("Error Fetching Captcha");
},preventCache:true});
};
ning.system.signIn=function(args){
var _7e6={};
for(var i in args.params){
_7e6[i]=args.params[i];
}
_7e6.xn_user=args.id;
_7e6.xn_password=args.password;
_7e6.xn_rememberMe=args.remember;
_7e6.target=args.target||window.location;
_7e6.errorTarget=args.errorTarget||_7e6.target;
try{
if(!/[:\/?&]/.test(_7e6.target)){
_7e6.target=decodeURIComponent(_7e6.target);
}
if(!/[:\/?&]/.test(_7e6.errorTarget)){
_7e6.errorTarget=decodeURIComponent(_7e6.errorTarget);
}
}
catch(e){
}
window.location=ning.system.buildUrl({subdomain:ning.CurrentApp.id,ssl:true,path:"/xn/identity/1.0/signin",query:_7e6});
};
ning.system.signOut=function(args){
if(!args){
args={};
}
args.target=args.target||window.location;
args.errorTarget=args.errorTarget||args.target;
try{
args.target=decodeURIComponent(args.target);
args.errorTarget=decodeURIComponent(args.errorTarget);
}
catch(e){
}
window.location=ning.system.buildUrl({subdomain:ning.CurrentApp.id,ssl:false,path:"/xn/identity/1.0/signout",query:args});
};
ning.system.signUp=function(args){
var form=args.formNode;
delete args.formNode;
args.target=args.target||window.location;
args.errorTarget=args.errorTarget||args.target;
try{
args.target=decodeURIComponent(args.target);
args.errorTarget=decodeURIComponent(args.errorTarget);
}
catch(e){
}
args.profile_originAppId=ning.CurrentApp.id;
form.setAttribute("action",ning.system.buildUrl({subdomain:ning.CurrentApp.id,ssl:true,path:"/xn/identity/1.0/signup",query:args}));
form.submit();
};
ning.system.cloneApp=function(args){
var a={url:"/xn/rest/1.0/application",method:"POST",mimetype:"text/json",encoding:"utf-8",load:function(type,data,evt){
if(data.errors){
if(args.failure){
args.failure(data.errors);
}
}else{
ning._.track("Clone App","cloned");
if(args.success){
args.success(data.application);
}
}
},error:function(type,data,_7f2){
if(data&&data.type=="unknown"&&_7f2&&_7f2.responseText){
data=dojo.json.evalJSON(_7f2.responseText);
}
if(args.failure){
args.failure((data&&data.errors)?data.errors:["An unexpected error occurred"]);
}
}};
if(args.error){
a.error=args.error;
}
if(args.formNode){
a.formNode=args.formNode;
}
if(args.content){
a.content=args.content;
}
dojo.io.bind(a);
};
ning.system.findProfiles=function(term,from,to,_7f6,_7f7){
if(dojo.lang.isFunction(from)){
_7f6=from;
_7f7=to;
from=0;
to=1;
}
_7f7=_7f7||dj_global;
var url="/xn/rest/internal/profile/search?term="+term+"&from="+from+"&to="+to;
dojo.io.bind({url:url,mimetype:"text/json",encoding:"utf-8",method:"GET",load:function(a,data,b){
_7f6.apply(_7f7,[data.results]);
}});
};
dojo.setModulePrefix("trimpath","../trimpath");
dojo.setModulePrefix("snazzy","../../snazzy/js");
dojo.setModulePrefix("ning","../ning");
dojo.provide("ning.ning");
dojo.require("dojo.io.cookie");
dojo.require("dojo.undo.browser");
dojo.require("ning.system");
ning._={CurrentClientTime:dojo.date.format(new Date(),"%Y-%m-%dT%H:%M:%S%z"),bar:{},loaded:false,track:function(name,_7fd,_7fe){
ning.whenLoaded(function(){
if(!window["s_xn"]){
return;
}
s_xn.pageName="Ning - "+ning.CurrentApp.name.replace(/'/gm,"&#039;");
s_xn.channel=_7fe||"Ningbar";
s_xn.prop1="";
if(_7fd=="invited"){
s_xn.prop1+="Message Sent ";
}
s_xn.prop1=_7fe||"Ningbar";
var _7ff=(ning.Bar.getOpenPanelName()=="user");
var _800=_7ff?"User":ning.Bar.getOpenPanelLabel();
if(_800){
s_xn.prop1+=" - "+_800;
}
if(name){
s_xn.prop1+=" - "+name;
}
s_xn.eVar2=ning.CurrentApp.id;
s_xn.events="";
if(_7fd=="registered"){
s_xn.events="event1";
}else{
if(_7fd=="invited"){
s_xn.events="event2";
}else{
if(_7fd=="cloned"){
s_xn.events="event3";
}else{
if(_7fd=="befriended"){
s_xn.events="event5";
}else{
if(_7fd=="contributed"){
s_xn.events="event6";
}else{
if(_7fd=="bought"){
s_xn.events="event7";
}
}
}
}
}
}
void (s_xn.t());
});
}};
(function(){
var _801=dojo.hostenv.getBaseScriptUri();
var _802=_801.substring(0,_801.indexOf("/js/"))+"/";
ning._.buildStaticPath=function(path){
return _802+path;
};
})();
ning.whenLoaded=function(_804){
if(ning._.loaded){
_804();
}else{
dojo.addOnLoad(_804);
}
};
dojo.addOnLoad(function(){
ning._.loaded=true;
dojo.undo.browser.setInitialState({back:function(){
ning.Bar._disable();
}});
if(dojo.io.cookie.getCookie("xn_track_app_creation")=="true"){
dojo.io.cookie.setCookie("xn_track_app_creation","false",0,"/","."+ning._.domains.base);
ning._.track("Clone App","cloned");
}
if(dojo.io.cookie.getCookie("xn_track_profile_creation")=="true"){
dojo.io.cookie.setCookie("xn_track_profile_creation","false",0,"/","."+ning._.domains.base);
ning._.track("Sign Up","registered");
}
});
dojo.provide("ning.hooks.bar");
(function(){
if(typeof (ning.hooks.bar.allowPanelAddition)=="undefined"){
ning.hooks.bar.allowPanelAddition=function(args){
return true;
};
}
if(typeof (ning.hooks.bar.allowDialogAddition)=="undefined"){
ning.hooks.bar.allowDialogAddition=function(args){
return true;
};
}
if(typeof (ning.hooks.bar.allowBrandPanelAddition)=="undefined"){
ning.hooks.bar.allowBrandPanelAddition=function(args){
return true;
};
}
})();
dojo.provide("ning.hooks.tags");
dojo.require("dojo.string");
dojo.require("dojo.event.*");
dojo.require("dojo.event.browser");
dojo.require("ning.system");
if(typeof (ning.hooks.viewTag)=="undefined"){
ning.hooks.viewTag=function(tag,_809){
if(_809){
dojo.event.browser.stopEvent(_809);
}
window.location=ning.system.buildUrl({subdomain:"browse",path:"/any/any/"+tag});
};
}
dojo.provide("ning.api");
dojo.require("dojo.json");
(function(){
var _80a=function(_80b,_80c,_80d,_80e,_80f){
var ctx=_80f||dj_global;
dojo.io.bind({url:_80c.replace(/\[/g,"%5B").replace(/\]/g,"%5D").replace(/\|/g,"%7C"),method:_80b,content:_80d,mimetype:"text/json",encoding:"utf-8",load:function(type,_812){
if(!_812||_812.errors){
if(dojo.lang.isFunction(_80e.error)){
_80e.error.apply(ctx,[_812]);
}
}else{
if(dojo.lang.isFunction(_80e.success)){
_80e.success.apply(ctx,[_812]);
}
}
if(dojo.lang.isFunction(_80e.after)){
_80e.after.apply(ctx,[]);
}
},error:function(type,_814,_815){
if(_814&&_814.type=="unknown"&&_815&&_815.responseText){
_814=dojo.json.evalJSON(_815.responseText);
}
if(dojo.lang.isFunction(_80e.error)){
_80e.error.apply(ctx,[_814]);
}
if(dojo.lang.isFunction(_80e.after)){
_80e.after.apply(ctx,[]);
}
},preventCache:true});
};
ning.api.get=function(url,_817,_818){
_80a("GET",url,null,_817||{},_818);
};
ning.api.post=function(url,_81a,_81b,_81c){
_80a("POST",url,_81a,_81b||{},_81c);
};
})();
dojo.provide("ning.hooks.profiles");
dojo.require("dojo.string");
dojo.require("dojo.event.*");
dojo.require("dojo.event.browser");
dojo.require("ning.api");
dojo.require("ning.system");
if(typeof (ning.hooks.viewProfile)=="undefined"){
ning.hooks.viewProfile_panelWidget=null;
ning.hooks.viewProfile=function(_81d,_81e){
if(_81e){
dojo.event.browser.stopEvent(_81e);
}
var _81f=ning&&ning.Bar&&ning.Bar.panelExists("friend-requests");
var _820=ning&&ning.Bar&&ning.Bar.panelExists("people");
if(_81f||_820){
if(!_81d){
ning.Bar.open(_820?"people":"friend-requests");
}else{
ning.api.get("/xn/rest/1.0/profile:"+_81d,{success:function(_821){
if(_81f){
ning._.bar.user.viewProfile(_821.profile);
}else{
ning._.bar.people.viewProfile(_821.profile);
}
},error:function(_822){
ning.Bar.open(_820?"people":"friend-requests");
}});
}
}else{
var _823=function(){
ning.loader.require("dojo.widget.*","ning._.bar.people.xnPeopleOnApp","ning._.bar.people.xnPeopleUserPinned",function(){
if(_81d=="__internal_allOnApp"){
ning.hooks.viewProfile_panelWidget.setContent(dojo.widget.createWidget("xnPeopleOnApp",{panelWidget:ning.hooks.viewProfile_panelWidget}));
}else{
ning.api.get("/xn/rest/1.0/profile:"+_81d,{success:function(_824){
ning.hooks.viewProfile_panelWidget.setContent(dojo.widget.createWidget("xnPeopleUserPinned",{panelWidget:ning.hooks.viewProfile_panelWidget,profile:_824.profile}));
},error:function(_825){
ning.hooks.viewProfile_panelWidget.showError("Sorry, that user does not exist.","",function(){
ning.Bar.close();
});
}});
}
});
};
if(ning.hooks.viewProfile_panelWidget){
_823();
}else{
ning.Bar.addPanel({name:"view-profile",order:103,icon:ning._.buildStaticPath("ningbar/gfx/menu/request.gif"),iconSize:{width:10,height:13},label:"",open:function(div,_827){
ning.hooks.viewProfile_panelWidget=_827;
_823();
},cache:false,close:function(){
if(ning.hooks.viewProfile_panelWidget){
ning.hooks.viewProfile_panelWidget=null;
ning.Bar.removePanel("view-profile");
}
}}).menuListItem.id="xn_friends";
ning.Bar.select("view-profile");
}
}
};
}
dojo.provide("ning.hooks.*");
dojo.kwCompoundRequire({common:["ning.hooks.bar","ning.hooks.tags","ning.hooks.profiles"]});

