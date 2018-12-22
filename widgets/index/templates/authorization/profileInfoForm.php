<?php /* Note that this file is used on two pages: the Create Profile page and the My Settings page [Jon Aquino 2008-09-11] */ ?>
<fieldset class="nolegend profile">
    <dl<%= $this->errors['fullName'] ? ' class="error"' : '' %>>
        <dt><label for="username"><%= xg_html('NAME') %><%= $this->indicateRequiredFields ? ' *' : '' %></label></dt>
        <dd><%= $this->form->text('fullName', 'id="fullname" class="textfield" maxlength="' . User::MAX_FULL_NAME_LENGTH . '"') %></dd>
    </dl>
    <dl<%= $this->errors['photo'] ? ' class="error easyclear"' : ' class="easyclear" style="margin-bottom:0"' %>>
        <dt><label for="signup_avatar"><%= xg_html('PROFILE_PHOTO') %></label></dt>
        <dd>
            <?php
            if ($this->showSimpleUploadField) { ?>
                <%= $this->form->file('photo', 'class="file' . ($this->errors['photo'] ? ' error' : '') . '" id="signup_avatar"') %>
            <?php
            } else { ?>
                <?php XG_App::ningLoaderRequire('xg.shared.BazelImagePicker'); ?>
                <div dojoType="BazelImagePicker" trimUploadsOnSubmit="1" fieldname="photo" showUseNoImage="0" allowTile="0" swatchWidth="23px" swatchHeight="21px" currentImagePath="<%= xnhtmlentities(XG_UserHelper::getThumbnailUrl($this->_user, 64, 64,true)) %>" saveParentFormOnChange="<%= $this->saveParentFormOnChange ? '1' : '0' %>"></div>
                <?php //profile image above is slightly wider than tall because the picker overlaps the image by a few pixels ?>
                <br class="clear" />
            <?php
            } ?>
        </dd>
    </dl>
    <input type="hidden" id="aboutQuestionsShown" name="aboutQuestionsShown" value="Y" />
    <p id="aboutSection" class="last-child" style="display:none">
        <strong><%= xg_html('ABOUT_ME') %></strong><br />
        <%= $this->aboutMeHtml %> <small><a href="#"><%= xg_html('CHANGE') %></a></small>
    </p>
    <div id="infoSection">
        <?php
        if ($this->showBirthdateFields) { ?>
            <dl>
                <dt><label for="dob-month"><%= xg_html('BIRTHDAY') %></label></dt>
                <dd>
                    <%= $this->form->select('birthdateMonth', $this->monthOptions, false, 'id="dob-month" class="date"') %>
                    <%= $this->form->select('birthdateDay', $this->dayOptions, false, 'class="date"') %>
                    <%= $this->form->select('birthdateYear', $this->yearOptions, false, 'class="date"') %>
                </dd>
                <?php
                if ($this->showDisplayAgeCheckbox) { ?>
                    <dd><label for="do_not_display_age" class="nobr"><%= $this->form->checkbox('doNotDisplayAge', 1, 'id="do_not_display_age" class="checkbox"') %><%= xg_html('DO_NOT_DISPLAY_AGE') %></label></dd>
                <?php
                } else {
                    echo $this->form->hidden('doNotDisplayAge');
                } ?>
            </dl>
        <?php
        } else {
            echo $this->form->hidden('birthdateMonth');
            echo $this->form->hidden('birthdateDay');
            echo $this->form->hidden('birthdateYear');
            echo $this->form->hidden('doNotDisplayAge');
        }
        if ($this->showGenderField) { ?>
        <dl>
            <dt><%= xg_html('GENDER') %></dt>
            <dd>
                <label><%= $this->form->radio('gender', 'm', 'class="radio"') %><%= xg_html('MALE') %></label>
                <label><%= $this->form->radio('gender', 'f', 'class="radio"') %><%= xg_html('FEMALE') %></label>
                <label><%= $this->form->checkbox('doNotDisplayGender', 1, 'class="checkbox"') %><%= xg_html('DO_NOT_DISPLAY') %></label>
            </dd>
        </dl>
        <?php
        } else {
            echo $this->form->hidden('gender');
            echo $this->form->hidden('doNotDisplayGender');
        }
        if ($this->showLocationField) { ?>
            <dl>
                <dt><label for="city"><%= xg_html('CITY_STATE') %></label></dt>
                <dd><%= $this->form->text('location', 'id="city" class="textfield" size="50" maxlength="' . User::MAX_LOCATION_LENGTH . '"') %></dd>
            </dl>
        <?php
        } else {
            echo $this->form->hidden('location');
        }
        if ($this->showCountryField) { ?>
            <dl>
                <dt><label for="country"><%= xg_html('COUNTRY') %></label></dt>
                <dd><%= $this->form->select('country', $this->countryOptions, false, 'id="country"') %></dd>
            </dl>
        <?php
        } else {
            echo $this->form->hidden('country');
        }  ?>
    </div>
    <?php
    // A rare case of inline JavaScript here, to ensure that the fields are shown on browsers without JavaScript
    // but immediately hidden on browsers with JavaScript. Note that Safari ignores fields that are not displayed. [Jon Aquino 2007-09-13]
    if ($this->aboutMeHtml) { ?>
        <script>
            document.getElementById('aboutSection').style.display = '';
            document.getElementById('infoSection').style.display = 'none';
            document.getElementById('aboutQuestionsShown').value = 'N';
        </script>
    <?php
    } ?>
</fieldset>
<?php XG_App::ningLoaderRequire('xg.index.authorization.profileInfoForm'); ?>
