<?php
/*  $Id: $
 *
 *  Display setup form
 *
 *  Parameters:
 *		$form		XG_Form
 *		$profileName	string		screenName of the profile module owner. NULL for homepage module
 *
 */
?>
<form style="display: none;" class="xg_module_options">
    <fieldset>
        <dl>
<?php
            echo $form->field('OPTION_DISPLAY',array('select',"{$this->prefix}_display",array(
                // TODO: XG_Form should do the escaping. [Jon Aquino 2008-04-02]
                'list'		=> xg_text('LIST_VIEW'),
                'calendar'	=> xg_text('CALENDAR'),
                'detail'	=> xg_text('DETAIL_VIEW'),
            )));
            if ($profileName) {
                echo $form->field('OPTION_FROM', array('select',"{$this->prefix}_from", array(
                    'attending'		=> xg_html('I_AM_ATTENDING'),
                    'all'			=> xg_html('ALL_MY_EVENTS'),
                )));
            } else {
                echo $form->field('OPTION_FROM', array('select',"{$this->prefix}_from", array(
                    'upcoming'		=> xg_html('UPCOMING_EVENTS'),
                    'featured'		=> xg_html('FEATURED_EVENTS'),
                )));
            }
            echo $form->field('OPTION_SHOW', array('select',"{$this->prefix}_count", array(0,2,4,6,8,10), 0, 'class="short"'),' '.xg_html('SHOW_EVENTS'));
?>
        </dl>
        <p class="buttongroup">
            <input type="submit" name="save" value="<%=xg_html('SAVE')%>" class="button button-primary"/>
            <input type="button" name="cancel" value="<%=xg_html('CANCEL')%>" class="button"/>
        </p>
    </fieldset>
</form>
