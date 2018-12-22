<?php
/*  $Id: $
 *
 *	Event create/edit form
 *
 *  @param	$edit		bool		Edit-mode
 *  @param	$form		XG_Form		Form
 */
?>
    <input type="hidden" name="cancelTarget" value="<%=xnhtmlentities($this->cancelTarget)%>">
    <div class="xg_module">
        <div class="xg_module_body nopad">
            <div class="pad5">
<?php if (!$edit) { ?>
                <ol class="steps easyclear">
                    <li class="current"><%=xg_html('EVENT_CREATE_STEP1')%></li>
                    <li><%=xg_html('EVENT_CREATE_STEP2')%></li>
                </ol>
<?php } ?>
            </div>
            <div class="xg_column xg_span-10">
                <div class="pad5" style="padding-right: 8px;">
                    <dl class="errordesc msg" id="event_form_notify" <%= $this->errors ? '' : 'style="display: none"' %>>
                        <?php
                        if ($this->errors) { ?>
                            <dt><%= xg_html('THERE_HAS_BEEN_AN_ERROR') %></dt>
                            <dd>
                                <ol>
                                    <?php
                                    foreach ($this->errors as $error) { ?>
                                        <li><%= xnhtmlentities($error) %></li>
                                    <?php
                                    } ?>
                                </ol>
                            </dd>
                        <?php
                        } ?>
                    </dl>
                    <fieldset class="nolegend">
                        <%=$form->hidden('hideEnd')%>
                        <div class="legend"><%=xg_html('EVENT_INFORMATION')%></div>
                        <dl>
<?php
echo $form->field('EVENT_NAME',			array('text', 'title', 1, 'style="width:95%" size="40" maxlength="'.Event::MAX_TITLE_LENGTH.'"'));
echo $form->field('EVENT_IMAGE',			array('image', 'photo', 1), '<p class="small clear">'.xg_html('EVENT_IMAGE_HINT').'</p>');
echo $form->field('DESCRIPTION',	array('editor', 'description', 1, '_maxlength="'.Event::MAX_DESCRIPTION_LENGTH.'" rows="8" cols="50"'));
echo $form->field('EVENT_TYPE',		array('text', 'type', 1, 'size="50" style="width:95%" maxlength="'.Event::MAX_EVENT_TYPE_LENGTH.'"'), '<p class="small">'.xg_html('DEFINE_EVENT_TYPE').'</p>');
echo $form->field('START_TIME',
    array('date','start', 'y:0:2md', 1),
	' ',
	array('time','start', 'hi', 1)
);
echo $form->field('END_TIME','',
    '<a href="javascript:void(0);" id="addEndTime" onclick="event_showEndTime()"'.($form->get('hideEnd')?'':' style="display:none"').'>'.xg_html('ADD_END_TIME').'</a>',
    '<span id="removeEndTime"'.($form->get('hideEnd')?' style="display:none"':'').'>',
        array('date','end', 'y:0:2md', 1),
		' ',
        array('time','end', 'hi', 1),
    '<br /><a href="javascript:void(0);" onclick="event_hideEndTime()">'.xg_html('REMOVE_END_TIME').'</a>'.
    '</span>'
);
echo $form->field('LOCATION',		array('text', 'location', 1, 'size="50" style="width:95%" maxlength="'.Event::MAX_LOCATION_LENGTH.'"'), '<p class="small">'.xg_html('PROVIDE_THE_LOCATION').'</p>');
echo $form->field('STREET',			array('text', 'street', 0, 'size="50" style="width:95%" maxlength="'.Event::MAX_STREET_LENGTH.'"'));
echo $form->field('CITY_TOWN',		array('text', 'city', 0, 'size="50" style="width:95%" maxlength="'.Event::MAX_CITY_LENGTH.'"'));
echo $form->field('WEBSITE_OR_MAP',	array('text', 'website', 0, 'size="50" style="width:95%" maxlength="'.Event::MAX_WEBSITE_LENGTH.'"'),'<p class="small">'.xg_html('ADD_WEB_ADDRESS','href="http://maps.google.com" target="_new"').'</p>');
// TODO: Rename contact to contactInfo, to match the model [Jon Aquino 2008-04-02]
echo $form->field('PHONE',	array('text', 'contact', 0, 'size="50" style="width:95%" maxlength="'.Event::MAX_CONTACT_INFO_LENGTH.'"'));
echo $form->field('ORGANIZED_BY',	array('text', 'organizedBy', 0, 'size="50" style="width:95%" maxlength="'.Event::MAX_ORGANIZED_BY_LENGTH.'"'),'<p class="small">'.xg_html('IF_YOU_HOST').'</p>');
?>
                        </dl>
                    </fieldset>
                </div>
            </div>
            <div class="xg_column xg_span-6 xg_last">
                <div class="pad5">
                    <fieldset>
                        <div class="legend"><%=xg_html('PRIVACY')%></div>
                        <ul class="options">
                            <li><label><%=$form->radio('privacy','anyone')%><%=xg_html('ANYONE_CAN_SEE')%></label></li>
                            <li><label><%=$form->radio('privacy','invited')%><%=xg_html('ONLY_INVITED_EVENT')%></label></li>
                        </ul>
                    </fieldset>
                    <fieldset>
                        <ul class="options">
                            <li><label for="disablersvp"><%=$form->checkbox('disableRsvp')%><%=xg_html('DISABLE_RSVP')%></label></li>
                            <li><label for="eventcomments"><%=$form->checkbox('hideGuests', $form->get('disableRsvp') ? 'disabled="disabled"' : '')%><%=xg_html('HIDE_GUEST_LIST')%></label></li>
                            <?php if ($edit) { ?>
                            <li><label><%=$form->checkbox('isClosed', $form->get('disableRsvp') ? 'disabled="disabled"' : '')%><%=xg_html('CLOSE_EVENT')%></label></li>
                            <?php } ?>
                        </ul>
                    </fieldset>
                </div>
            </div>
            <div class="pad5 clear">
              <p class="buttongroup last-child">
                <input class="button button-primary" type="submit" value="<%=xg_html($edit ? 'SAVE' : 'ADD_EVENT')%>"/>
                <a href="<%=$this->cancelTarget%>" class="button"><%= xg_html('CANCEL') %></a>
              </p>
            </div>
        </div>
    </div>
<?php XG_App::ningLoaderRequire('xg.events.form'); ?>
