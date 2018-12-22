<?php
/*  $Id: $
 *
 *  Displays embed module
 *
 *  Parameters:
 *  	$this->isOwner
 *		$this->form			XG_Form
 *		$this->columns		int			the number of columns
 *		$this->profileName	string		screenName of the profile module owner. NULL for homepage module
 *		$this->setValuesUrl	string
 *		$this->hasEvents	bool		Use has events
 *		$this->events		XG_PagingList<Event>
 *		$this->calendar		hash<yyyy-mm:days>
 *		$this->settings		hash
 *		$this->viewAllUrl
 *      $this->embed       XG_Embed   stores the module data
 */
if ($this->isOwner) {
	if ($this->profileName && ($this->profileName != XN_Profile::current()->screenName || !$this->hasEvents)) {
		return; // for the profile page display nothing if there are no events
    }
?>
<div class="xg_module module_events" dojoType="EventEmbedModule" _url="<%=xnhtmlentities($this->setValuesUrl)%>" _updateEmbedUrl="<%= xnhtmlentities($this->updateEmbedUrl) %>">
    <div class="xg_module_head">
        <h2><%=$this->profileName
            ? ($this->profileName == XN_Profile::current()->screenName
                ? xg_html('MY_EVENTS')
                : xg_html('USER_EVENTS',xnhtmlentities(xg_username($this->profileName))))
            : xg_html('EVENTS')%></h2>
        <p class="edit" style="display:none"><a class="button" href="#"><%=xg_html('EDIT')%></a></p>
    </div>
<?php
    XG_App::ningLoaderRequire('xg.events.EventEmbedModule');
    $this->renderPartial('fragment_form','embed', array('form' => $this->form, 'profileName' => $this->profileName));
} elseif (!$this->hasEvents) {
    return; // nothing to do
} else {?>
<div class="xg_module module_events">
    <div class="xg_module_head">
        <h2><%=$this->profileName ? xg_html('USER_EVENTS',xnhtmlentities(xg_username($this->profileName))) : xg_html('EVENTS')%></h2>
    </div>
<?php } ?>
    <?php $this->renderPartial('fragment_block','embed', array(
        'events'	=> $this->events,
        'settings'	=> $this->settings,
        'calendar'	=> $this->calendar,
        'columns' 	=> $this->columns,
        'viewAllUrl'=> $this->viewAllUrl,
        'profileName'=> $this->profileName,
        'embed'      => $this->embed,
    ));?>
</div>
<?php XG_App::addToCssSection('<link rel="stylesheet" type="text/css" media="screen,projection" href="'.xnhtmlentities(XG_Version::addXnVersionParameter($this->_widget->buildResourceUrl('css/module.css'))).'" />');?>
