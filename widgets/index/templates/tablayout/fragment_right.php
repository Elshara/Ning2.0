<?php XG_App::ningLoaderRequire('xg.shared.BazelColorPicker'); ?>

<h3><%= xg_html('TAB_INFORMATION') %></h3>

<p id="instructions" class="xg_lightfont">
    <%= xg_html('DRAG_AND_DROP_TABS_TO_REORDER', $this->maxTabs, $this->maxTabLength); %>
</p>

<p id="xj_msgbox" style="display:none"></p>

<form id="xj_tab_form" onsubmit="return false" style="visibility:hidden">
    <fieldset class="nolegend xg_lightborder">
		<dl>
            <dt><label for="tabName" id="xj_label_tabName"><%= xg_html('TAB_NAME'); %></label></dt>
            <dd id="xj_dd_tabName"><input id="tabName" name="tabName" class="required textfield tabname" type="text" maxlength="<%=$this->maxTabLength%>" /></dd>
        </dl>
		<dl>
            <dd><input type="checkbox" id="isSubTab" name="isSubTab" /> <label for="isSubTab"><%= xg_html('MAKE_THIS_A_SUBTAB'); %></label></dd>
        </dl>
		<dl>
            <dt><label for="url"><%= xg_html('TARGET_PAGE'); %></label></dt>
            <dd><input name="targetType" id="targetTypePage" type="radio" value="page" class="radio" /><input type="hidden" name="targetPageId" id="targetPageId" value="" /> <label for="targetTypePage"><%= xg_html('CREATE_NEW_PAGE'); %></label></dd>
            <dd><input name="targetType" id="targetTypeUrl" type="radio" value="url" class="radio" /> <label for="targetTypeUrl"><%= xg_html('USE_EXISTING_URL_COLON') %></label>
                <input type="text" name="uri" id="url" class="textfield url" /></dd>
            <dd><input type="checkbox" name="openInNewWindow" id="openInNewWindow" /> <label for="openInNewWindow"><%= xg_html('OPEN_IN_NEW_WINDOW'); %></label></dd>
            <br />
            <dt><label for="visibility"><%= xg_html('MAKE_TAB_VISIBLE_TO'); %></label></dt>
            <dd>
                <select id="visibility" name="visibility">
                    <option value="all"><%= xg_html('TAB_VISIBILITY_ALL'); %></option>
                    <option value="member"><%= xg_html('TAB_VISIBILITY_MEMBER'); %></option>
                    <option value="admin"><%= xg_html('TAB_VISIBILITY_ADMIN'); %></option>
                </select>
                <p class="small"><em><%= xg_html('TAB_VISIBILITY_DOES_NOT_AFFECT_PRIVACY'); %></em></p>
            </dd>
        </dl>
    </fieldset>
</form>
<br />
<h3><%= xg_html('SUBTAB_MENU_COLORS') %></h3>
<form id="xj_tab_global_form" onsubmit="return false" method="post" action="<%=qh($this->_buildUrl('tablayout','update'))%>">
    <fieldset class="nolegend xg_lightborder subtabcolors">
        <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
        <input type="hidden" name="layoutJson" value="">
        <dl><?php XG_App::includeFileOnce('/lib/XG_AppearanceTemplateHelper.php'); ?>
            <dt><label for="textColor"><%= xg_html('TEXT_COLOR'); %></label></dt>
            <dd>
				<div id="xj_textColor" dojoType="BazelColorPicker" fieldName="textColor" defaultValue="<%= $this->subTabColors['textColor'] %>" _allowTransparent="false">
                    <span class="swatch"></span><button class="icon"><img src="<%= xg_cdn(W_Cache::getWidget('main')->buildResourceUrl('gfx/button/palette.gif')) %>"/></button>
                </div>
            </dd>
            <dt><label for="textColorHover"><%= xg_html('TEXT_COLOR_ON_HOVER'); %></label></dt>
            <dd>
                <div id="xj_textColorHover" dojoType="BazelColorPicker" fieldName="textColorHover" defaultValue="<%= $this->subTabColors['textColorHover'] %>" _allowTransparent="false">
                    <span class="swatch"></span><button class="icon"><img src="<%= xg_cdn(W_Cache::getWidget('main')->buildResourceUrl('gfx/button/palette.gif')) %>"/></button>
                </div>
            </dd>
            <dt><label for="backgroundColor"><%= xg_html('BACKGROUND_COLOR'); %></label></dt>
            <dd>
				<div id="xj_bgColor" dojoType="BazelColorPicker" fieldName="backgroundColor" defaultValue="<%= $this->subTabColors['backgroundColor'] %>" _allowTransparent="true">
                    <span class="swatch"></span><button class="icon"><img src="<%= xg_cdn(W_Cache::getWidget('main')->buildResourceUrl('gfx/button/palette.gif')) %>"/></button>
                </div>
            </dd>
            <dt><label for="backgroundColorHover"><%= xg_html('BACKGROUND_COLOR_ON_HOVER'); %></label></dt>
            <dd>
                <div id="xj_bgColorHover" dojoType="BazelColorPicker" fieldName="backgroundColorHover" defaultValue="<%= $this->subTabColors['backgroundColorHover'] %>" _allowTransparent="true">
                    <span class="swatch"></span><button class="icon"><img src="<%= xg_cdn(W_Cache::getWidget('main')->buildResourceUrl('gfx/button/palette.gif')) %>"/></button>
                </div>
            </dd>
        </dl>
        <br />
        <p>
			<a href="#" id="xj_reset_colors" _url="<%=qh($this->_buildUrl('tablayout','getColors','?xn_out=json'))%>"><%=xg_html('PICK_SUB_TAB_MENU')%></a>
			<img id="xj_spinner2" src="<%=xg_cdn('/xn_resources/widgets/index/gfx/icon/spinner.gif')%>" style="display:none"/>
		</p>
    </fieldset>
    <br />
    <p class="buttongroup">
	<img id="xj_spinner" src="<%= xg_cdn('/xn_resources/widgets/index/gfx/icon/spinner.gif') %>" style="display:none;" />
        <input class="button button-primary" type="button" value="<%= xg_html('SAVE_ALL_TAB_SETTINGS'); %>" name="save" />
        <input class="button" type="button" value="<%= xg_html('RESET_TO_DEFAULTS'); %>" name="reset_all" _url="<%=qh($this->_buildUrl('tablayout','reset'))%>" />
        <input class="button" type="button" value="<%= xg_html('CANCEL'); %>" name="cancel" onclick="try {window.location='<%=qh($this->_buildUrl('admin','manage'))%>'}catch(e){}"/>
    </p>
</form>
