<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';

class Syntax05CmdlineTest extends CmdlineTestCase {

    public function testDojoIoBindProperties() {
        $existingViolations = array (
                'dojo.io.bind({url:this._deleteCommentAndSubCommentsUrl,method:\'post\',content:{\'counter\':counter},mimetype:\'text/json\',load:dojo.lang.hitch(this,function(t,data,e){if(!(\'contentRemaining\'indata)){throw\'contentRemainingnotpresentinresponse\';}if(data.contentRemaining>0){',
                'dojo.io.bind({url:this._url,method:\'post\',encoding:\'utf-8\',content:dojo.lang.mixin({counter:counter},this.getPostContent(counter)),mimetype:\'text/json\',load:dojo.lang.hitch(this,function(t,data,e){if(!(\'contentRemaining\'indata)){throw\'contentRemainingnotpresentinresponse\';}if(this.isDone(data.contentRemaining)){',
                'dojo.io.bind({url:\'/index.php/main/membership/addFromPanel?xn_out=json\',method:\'post\',preventCache:true,content:{recipients:recipientScreenNamesAndEmails.join(\';\'),msg:invocation.args[0].body.substring(22)},mimetype:\'text/javascript\',load:function(type,data,event){',
                'dojo.io.bind({\'url\':url,\'method\':\'post\',\'mimetype\':\'text/json\',\'content\':{\'id\':this._id,\'type\':this._type},\'load\':dojo.lang.hitch(this,function(type,data,evt){//Showtheconfirmationmessageif(data.message){vardialog=dojo.html.createNodesFromText(dojo.string.trim(\'\\',
                'dojo.io.bind({\'url\':url,\'method\':\'post\',\'mimetype\':\'text/json\',\'content\':{\'id\':this._id,\'type\':this._type},\'load\':dojo.lang.hitch(this,function(type,data,evt){dojo.require("dojo.lfx.*");dojo.fx.html.highlight(this.link,1000,\'#ffee7d\');',
                'dojo.io.bind({url:dojo.string.paramString("/xn/rest/1.0/application:%{app_id}?xn_method=PUT",{app_id:ning.CurrentApp.id}),method:"POST",mimetype:"text/json",encoding:"utf-8",content:{application_online:true}',
                'dojo.io.bind({url:dojo.string.paramString("/xn/rest/1.0/application:%{app_id}?xn_method=PUT",{app_id:ning.CurrentApp.id}),method:"POST",mimetype:"text/json",encoding:"utf-8",content:{application_online:false}',
                'dojo.io.bind({url:this._url.replace(/width=\\d+/,\'width=\'+sizeOptions[sizeSelect.selectedIndex].width).replace(/noMusicMessage=[^&]+/,\'noMusicMessage=\'+encodeURIComponent(sourceOptions[sourceSelect.selectedIndex].noMusicMessage)).replace(/playlistUrl=[^&]+/,\'playlistUrl=\'+sourceOptions[sourceSelect.selectedIndex].url).replace(/showPlaylist=[^&]+/,\'showPlaylist=\'+(showPlaylistCheckbox.checked?\'true\':\'\')),method:\'get\',preventCache:true,mimetype:\'text/javascript\',',
                'dojo.io.bind({url:this._url.replace(/width=\\d+/,\'width=\'+sizeOptions[sizeSelect.selectedIndex].width).replace(/height=\\d+/,\'height=\'+sizeOptions[sizeSelect.selectedIndex].height).replace(/noVideosMessage=[^&]+/,\'noVideosMessage=\'+encodeURIComponent(sourceOptions[sourceSelect.selectedIndex].noVideosMessage)).replace(/videoID=[^&]+/,\'videoID=\'+sourceOptions[sourceSelect.selectedIndex].videoID),method:\'get\',preventCache:true,mimetype:\'text/javascript\',',
                'dojo.io.bind({mimetype:"text/json",encoding:"utf-8",sync:true,//VID-395[JonAquino2006-08-21]url:"/xn/rest/internal/profile/validation/email:"+email,load:function(type,data,evt){if(data.error){errors.profile_email=data.error;',
                'dojo.io.bind({\'url\':url,\'mimetype\':\'text/javascript\',\'load\':function(type,data,evt){varnodes=dojo.html.createNodesFromText(data.html);//Someofthenodesarewhitespace,comments,etc.for(variinnodes){if(nodes[i].nodeName==\'FIELDSET\'){dojo.style.setOpacity(nodes[i],0);',
                'dojo.io.bind({formNode:f,method:f.method,url:f.action});},//Thesearesetinedit.phpwherewehaveaccesstothePHPAPI.',
                'dojo.io.bind({encoding:"utf-8",load:xg.index.privacy.edit.setPrivacyLevelProper,mimetype:\'text/json\',url:dojo.string.paramString("/xn/rest/1.0/application:%{app_id}",{app_id:ning.CurrentApp.id})});},',
                'dojo.io.bind({url:url,//text/plainworksforboththeXmlHttpRequestandIFrametransports(ifitistext/javascript,theIFrametransport//assumesthatitisanHTMLdocumentcontainingthejavascriptinatextarea)[JonAquino2006-05-06]mimetype:\'text/plain\',formNode:form,method:\'post\',//MustsetencodingtopreserveUTF-8inputinforms[DavidSklar2006-05-16]',
                'dojo.io.bind({url:this._setValuesUrl,method:\'post\',content:{autoplay:this._autoplay,columnCount:this._columnCount,showPlaylist:this._showplaylist,playlistSet:this._playlistSet,playlistUrl:this._playlistUrl},',
                'dojo.io.bind({url:\'/index.php/\'+xg.global.currentMozzle+\'/page/registershown?xn_out=json\',content:{id:pageId},method:\'post\',encoding:\'utf-8\',load:dojo.lang.hitch(this,function(type,data,event){})});}),5000);',
                'dojo.io.bind({url:this.submitUrl,method:\'post\',mimetype:\'text/javascript\',encoding:\'utf-8\',content:{albumId:this.albumId?this.albumId:\'\',title:dojo.string.trim(this.titleInput.value).length>0?this.titleInput.value:xg.photo.nls.text(\'untitled\'),description:this.descriptionInput.value,',
                'dojo.io.bind({url:\'/index.php/\'+xg.global.currentMozzle+\'/album/registershown\',content:{id:albumId},method:\'post\',encoding:\'utf-8\',load:dojo.lang.hitch(this,function(type,data,event){})});}),3000);',
                'dojo.io.bind({url:\'/photo/flickr/importPhoto/\',method:\'post\',mimetype:\'text/json\',encoding:\'utf-8\',content:{url:url,tags:tags,lat:lat,lng:lng,title:title,id:photoid,auth_token:auth_token,desc:getDescriptions,orig:getOriginals},',
                'dojo.io.bind({url:\'/photo/flickr/runImport/\',method:\'post\',mimetype:\'text/json\',sync:true,encoding:\'utf-8\',content:{type:type,extras:extras,auth_token:auth_token,nsid:nsid,page:page},load:function(type,data,evt){',
                'dojo.io.bind({url:form.action,method:\'post\',mimetype:\'text/json\',encoding:\'utf-8\',content:{type:importType,extras:extraVars,nsid:nsid,auth_token:auth_token},load:function(type,data,evt){//Seehttp://www.flickr.com/services/api/response.json.html',
                '//ondojo.io.bind()[DavidSklar2006-09-07]dojo.provide(\'xg.photo.TopicUpdatingText\');dojo.widget.defineWidget(\'xg.photo.TopicUpdatingText\',dojo.widget.HtmlWidget,{_url:\'<required>\',_topic:\'<required>\',_method:\'GET\',fillInTemplate:function(args,frag){varnode=this.getFragNodeRef(frag);',
                'dojo.io.bind({\'url\':this._url,\'mimetype\':\'text/json\',\'method\':this._method,\'encoding\':\'utf-8\',\'load\':function(type,data,evt){if(\'html\'indata){node.innerHTML=data.html;xg.photo.fixImagesInIE(node.getElementsByTagName(\'img\'));',
                'dojo.io.bind({url:deleteUrl,method:"post",encoding:\'utf-8\',content:params,load:function(type,data,evt){location.href=targetUrl;}',
                'dojo.io.bind({\'url\':xg.global.requestBase+\'/profiles/profile/newUploadEmailAddress?xn_out=json\',\'method\':\'POST\',\'mimetype\':\'text/json\',\'load\':function(type,data,evt){varshow=dojo.byId(\'xg_profiles_settings_email_show\');if(show&&data.uploadEmailAddress){dojo.require(\'dojo.fx.*\');',
                'dojo.io.bind({url:\'/main/flickr/setNotification\',method:\'post\',mimetype:\'text/json\',encoding:\'utf-8\',content:{notification:toggleValue},load:function(type,data,evt){//',
                'dojo.io.bind({url:this._processPhotoUrl,method:\'post\',mimetype:\'text/json\',encoding:\'utf-8\',content:xg.photo.parseUrlParameters(this._processPhotoUrl),load:dojo.lang.hitch(this,function(type,data,evt){if(data&&data.html){',
                'dojo.io.bind({url:this._paginationUrl,method:\'get\',content:{context:this._context,dir:side,begin:((side==\'prev\')?this._left:this._right)},encoding:\'utf-8\',',
                'dojo.io.bind({url:this._url,method:\'get\',content:{context:this._context},encoding:\'utf-8\',owner:this,load:function(type,data,evt){dojo.html.removeClass(p,\'working\');',
                'dojo.io.bind({url:this._url,method:\'post\',content:{id:this._photoId},encoding:\'utf-8\',load:function(type,data,evt){window.location.reload(true);}',
                'dojo.io.bind({url:\'/index.php/\'+xg.global.currentMozzle+\'/photo/registershown\',content:{id:photoId},method:\'post\',encoding:\'utf-8\',load:dojo.lang.hitch(this,function(type,data,event){})});}),5000);',
                'dojo.io.bind({url:this._actionUrl,method:"post",content:{albumId:albumIdValue,newAlbumName:newAlbumInput.value,photoId:this._photoId,render:\'bar\'},encoding:\'utf-8\',',
                'dojo.io.bind({\'url\':\'/index.php/\'+xg.global.currentMozzle+\'/comment/approve?xn_out=json\',\'method\':\'post\',\'mimetype\':\'text/javascript\',\'content\':{\'id\':commentId},\'load\':function(type,data,evt){//Removethecomment-newclassfromthecontainingdlvarcontainingDl=dojo.dom.getFirstAncestorByTag(linkNode,\'dl\');if(containingDl){',
                'dojo.io.bind({\'url\':\'/index.php/\'+xg.global.currentMozzle+\'/comment/delete?xn_out=json\',\'method\':\'post\',\'mimetype\':\'text/javascript\',\'content\':{\'id\':commentId},\'load\':function(type,data,evt){//Removethecontaining<dl/>varcontainingDl=dojo.dom.getFirstAncestorByTag(linkNode,\'dl\');if(containingDl){',
                'dojo.io.bind({url:this._url,method:\'post\',content:{displaySet:this._displaySet,sortSet:this._sortSet,postsSet:this._postsSet},',
                'dojo.io.bind({\'url\':\'/index.php/\'+xg.global.currentMozzle+\'/comment/approve?xn_out=json\',\'method\':\'post\',\'mimetype\':\'text/javascript\',\'content\':{\'id\':chatterId},\'load\':function(type,data,evt){//RemovethenotificationclassfromthecontainingdlvarcontainingDl=dojo.dom.getFirstAncestorByTag(linkNode,\'dl\');if(containingDl){',
                'dojo.io.bind({\'url\':\'/index.php/\'+xg.global.currentMozzle+\'/comment/delete?xn_out=json\',\'method\':\'post\',\'mimetype\':\'text/javascript\',\'content\':{\'id\':chatterId},\'load\':function(type,data,evt){//Removethecontaining<dl/>varcontainingDl=dojo.dom.getFirstAncestorByTag(linkNode,\'dl\');if(containingDl){',
                'dojo.io.bind({\'url\':\'/index.php/\'+xg.global.currentMozzle+\'/comment/previous?attachedTo=\'+encodeURIComponent(attachedTo.value)+\'&attachedToType=\'+encodeURIComponent(attachedToType.value)+\'&when=\'+timestamp+\'&xn_out=htmljson\',\'method\':\'get\',\'mimetype\':\'text/javascript\',\'load\':function(type,data,evt){if(data&&data.html&&data.html.length){xg.profiles.embed.chatterwall.displayFromHtml(data.html,\'last\');}',
                'dojo.io.bind({url:this._url,method:\'post\',content:{\'attachedToType\':\'User\',\'moderate\':this._moderate},preventCache:true,encoding:\'utf-8\',load:dojo.lang.hitch(this,function(type,data,event){//Nothingtodoaftertherequestfinishesotherthanhidetheform',
                'dojo.io.bind({url:this._setValuesUrl,method:\'post\',content:{displaySet:this._displaySet,sortSet:this._sortSet,rowsSet:this._rowsSet},preventCache:true,',
                'dojo.io.bind({url:url,method:\'post\',encoding:\'utf-8\',load:dojo.lang.hitch(this,function(type,data,event){if(this._hasFavorite==0){a.className="descfavorite-remove";a.innerHTML=xg.shared.nls.text(\'removeFromFavorites\');',
                'dojo.io.bind({url:url,method:\'post\',encoding:\'utf-8\',preventCache:true,load:dojo.lang.hitch(this,function(type,data,event){this._isFollowed=this._isFollowed==0?1:0;this.updateText(this.a);',
                'dojo.io.bind({url:this._url,method:\'post\',encoding:\'utf-8\',load:function(type,data,evt){window.location.reload(true);}});',
                'dojo.io.bind({encoding:\'utf-8\',url:deleteUrl,method:"post",content:{id:id},load:function(type,data,evt){location.href=targetUrl;}',
        );
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.js') as $file) {
            if (strpos($file, '/dojo') !== FALSE) { continue; }
            if (strpos($file, 'ContactList.js') !== false) { continue; }
            if (strpos($file, 'profiles/js/embed/unfriend.js') !== false) { continue; }
            if (strpos($file, 'xn_resources/js/adapter/io.js') !== false) { continue; }
            $contents = self::getFileContent($file);
            if (strpos($contents, 'dojo.io.bind(') === false) { continue; }
            $lines = explode("\n", $contents);
            for ($i = 0; $i < count($lines); $i++) {
                if (strpos($lines[$i], 'dojo.io.bind(') === false) { continue; }
                if (strpos($lines[$i], 'dojo.io.bind(req);') !== false) { continue; }
                $context = '';
                if (strpos($file, 'FriendLink.js') !== false) { $context .= 'mimetype'; }
                $contextLineCount = 8;
                if (strpos($file, 'opensocial/js/embed/embed.js') !== false) { $contextLineCount = 15; }
                for ($j = 0; $j < $contextLineCount; $j++) {
                    $context .= $lines[$i + $j] . "\n";
                }
                $contextWithoutWhitespace = preg_replace('@\s@', '', $context);
                if (in_array($contextWithoutWhitespace, $existingViolations)) { continue; }
                $this->assertTrue(strpos($context, 'preventCache') !== false, 'dojo.io.bind missing preventCache: true, on line ' . $i . ' in ' . $file);
                $this->assertTrue(strpos($context, 'encoding') !== false, 'dojo.io.bind missing encoding: \'utf-8\', on line ' . $i . ' in ' . $file);
                $this->assertTrue(strpos($context, 'mimetype') !== false, 'dojo.io.bind missing mimetype: \'text/javascript\', on line ' . $i . ' in ' . $file);
                if (strpos($context, 'preventCache') === false || strpos($context, 'encoding') === false || strpos($context, 'mimetype') === false) {
                    echo '<pre>' . xnhtmlentities($context) . '</pre>';
                }
            }
        }
    }

    public function testNoAltsWithBlankSpace() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.*') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            if (strpos($file, 'php') === false && strpos($file, 'js') === false) { continue; }
            $contents = self::getFileContent($file);
            if (strpos($contents, 'alt=" "') === false) { continue; }
            foreach (explode("\n", $contents) as $line) {
                $this->assertTrue(strpos($line, 'alt=" "') === false, $this->escape($line) . ' - ' . $file);
            }
        }
    }

    public function testUseXgProfileUrl() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.*') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            if (strpos($file, 'php') === false && strpos($file, 'js') === false) { continue; }
            $contents = self::getFileContent($file);
            if (strpos($contents, 'profile/') === false) { continue; }
            foreach (explode("\n", $contents) as $line) {
                if (strpos($line, 'profileAddress') !== false) { continue; }
                $line = str_replace('/main/profile', '', $line);
                $line = str_replace('\'/profile/\' . rawurlencode($fragment)', '', $line);
                $line = str_replace('Pass the form target URL to the profile/edit action', '', $line);
                $line = str_replace('SilverSurfer in http://networkname.ning.com/profile/SilverSurfer', '', $line);
                $line = str_replace('/xn/rest/1.0/profile/validation/email', '', $line);
                $line = str_replace('http://api.ning.com/icons/profile', '', $line);
                $line = str_replace('/xn/rest/internal/profile', '', $line);
                $line = str_replace('profiles/profile/', '', $line);
                $line = str_replace('http://api.xna.ningops.net/icons/profile/655666?default=-1&crop=1%3A1', '', $line);
                $line = str_replace('if (document.location.href.indexOf(\'/profile/\') > -1)', '', $line);
                $line = str_replace('js/profile', '', $line);
                $this->assertTrue(strpos($line, 'profile/') === false, $this->escape($line) . ' - ' . $file);
            }
        }
    }

    public function testDoNotUseNingSystemJavaScriptFunctions() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.*') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            if (strpos($file, '/dojo') !== false) { continue; }
            if (strpos($file, 'php') === false && strpos($file, 'js') === false) { continue; }
            $contents = self::getFileContent($file);
            $contents = str_replace('ning.system.buildUrl', '', $contents);
            if (strpos($contents, 'ning.system.') === false) { continue; }
            foreach (explode("\n", $contents) as $line) {
                $this->assertTrue(strpos($line, 'ning.system.') === false, $this->escape($line) . ' - ' . $file);
            }
        }
    }

    public function testAfterRedirectToUseReturnNotExit() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.*') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            if (strpos($file, 'php') === false && strpos($file, 'js') === false) { continue; }
            $contents = self::getFileContent($file);
            if (strpos($contents, 'exit') === false) { continue; }
            $previousLine = '';
            $i = 0;
            foreach (explode("\n", $contents) as $line) {
                $i++;
                $this->assertTrue(strpos($line, 'exit') === false || (strpos($previousLine, 'redirectTo(') === false && strpos($previousLine, 'forwardTo(') === false), $this->escape($line) . ' line ' . $i . ' - ' . $file);
                $previousLine = $line;
            }
        }
    }

    public function testAccountPages() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, 'test/') !== false) { continue; }
            if (strpos($file, 'templates/') === false) { continue; }
            if (strpos($file, '/widgets/index/templates/embed/header.php') !== false) { continue; }
            if (strpos($file, '/widgets/index/templates/embed/header_iphone.php') !== false) { continue; }
            $contents = self::getFileContent($file);
            if (strpos($contents, 'xgDivClass') === false) { continue; }
            $this->assertTrue(strpos($contents, 'hideNingbar') !== false || basename($file) == 'pending.php', 'Missing \'hideNingbar\' => true - ' . $file);
            $this->assertTrue(strpos($contents, 'xg_footer') !== false, 'Missing xg_footer - ' . $file);
        }
    }

    public function testRemoveAllowDebug() {
        foreach(XG_TestHelper::globr(NF_APP_BASE, '*.php') as $file) {
            if (strpos($file, '/widgets/') === false) { continue; }
            $contents = self::getFileContent($file);
            if (strpos($contents, 'allowDebug') === false) { continue; }
            $previousLine = '';
            $i = 0;
            foreach (explode("\n", $contents) as $line) {
                $i++;
                $this->assertTrue(strpos($line, 'allowDebug') === false , $this->escape($line) . ' - ' . $file . ' line ' . $i);
                $previousLine = $line;
            }
        }
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
