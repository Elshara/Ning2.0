    <div class="row" <%= $this->errors['fullName'] ? ' class="error"' : '' %>>
        <label for="username"><%= $this->indicateRequiredFields ? '<span class="icon required"></span>' : '' %><%= xg_html('NAME') %></label>
        <%= $this->form->text('fullName', 'id="fullname" class="textfield" maxlength="' . User::MAX_FULL_NAME_LENGTH . '"') %>
    </div>
    <input type="hidden" id="aboutQuestionsShown" name="aboutQuestionsShown" value="Y" />
        <?php
        echo $this->form->hidden('birthdateMonth');
        echo $this->form->hidden('birthdateDay');
        echo $this->form->hidden('birthdateYear');
        echo $this->form->hidden('doNotDisplayAge');
        if ($this->showGenderField) { ?>
        	<div class="row">
            <label for="gender"><%= xg_html('GENDER_COLON') %></label>
            <%= $this->form->select('gender', array('m' => xg_html('MALE'), 'f' => xg_html('FEMALE')), false, 'id="gender"') %>
            </div>
        	<div class="row">
            <label for="display_gender"><%= xg_html('DISPLAY_GENDER_COLON') %></label><span id="display_gender_toggle" class="checkbox checked" onclick="javascript:void(0)"></span><%= $this->form->checkbox('doNotDisplayGender', 0, 'id="display_gender" class="checkbox" style="display:none;"') %>
            </div>
        <?php
        } else {
            echo $this->form->hidden('gender');
            echo $this->form->hidden('doNotDisplayGender');
        }
        if ($this->showLocationField) { ?>
        	<div class="row">
            <label for="city"><%= xg_html('CITY_STATE_COLON') %></label>
            <%= $this->form->text('location', 'id="city" class="textfield" size="50" maxlength="' . User::MAX_LOCATION_LENGTH . '"') %>
            </div>
        <?php
        } else {
            echo $this->form->hidden('location');
        }
        if ($this->showCountryField) { ?>
        	<div class="row">
            <label for="country"><%= xg_html('COUNTRY') %></label>
            <%= $this->form->select('country', $this->countryOptions, false, 'id="country"') %>
            </div>
        <?php
        } else {
            echo $this->form->hidden('country');
        }  ?>