<?php
/*  $Id: $
 *
 *  Displays setup form
 *
 *  Parameters:
 *  	$this->prefix
 *		$form		XG_Form
 *		$columns	int			the number of columns
 *		$isHomepage
 *
 */
?>
<form style="display: none;" class="xg_module_options">
    <fieldset>
        <dl>
<?php
        if ($isHomepage) {
            echo $form->field('OPTION_DISPLAY',array('select',"{$this->prefix}_display_$columns",array(
                'details'	=> xg_text('DETAIL_VIEW'),
                'titles'	=> xg_text('TITLES_ONLY'),
                'note'		=> xg_text('SINGLE_NOTE'),
            )));
            echo '<dt style="display:none">',xg_html('OPTION_TITLE'),'</dt><dd style="display:none">',$form->text("{$this->prefix}_title_$columns"),'</dd>';
            echo $form->field('OPTION_FROM', array('select',"{$this->prefix}_from_$columns", array(
                'updated'		=> xg_text('RECENTLY_UPDATED'),
                'created'		=> xg_text('RECENTLY_ADDED'),
                'featured'		=> xg_text('FEATURED_NOTES'),
            )));
            echo $form->field('OPTION_SHOW', array('select',"{$this->prefix}_count_$columns", array(0,1,2,3,4,5,10,20), 0, 'class="short"'),' '.xg_html('SHOW_NOTES'));
        } else {
            echo $form->field('OPTION_DISPLAY',array('select',"{$this->prefix}_display_$columns",array(
                'details'	=> xg_text('DETAIL_VIEW'),
                'titles'	=> xg_text('TITLES_ONLY'),
            )));
            echo $form->field('OPTION_SHOW', array('select',"{$this->prefix}_count_$columns", array(0,1,2,3,4,5,10,20)),' '.xg_html('SHOW_NOTES'));
        }
?>
        </dl>
        <p class="buttongroup">
            <input type="submit" name="save" value="<%=xg_html('SAVE')%>" class="button button-primary"/>
            <input type="button" name="cancel" value="<%=xg_html('CANCEL')%>" class="button"/>
        </p>
    </fieldset>
</form>
