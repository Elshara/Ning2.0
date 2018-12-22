<?php
foreach ($this->questions as $question) {
    $questionFieldName = 'question_' . $question['questionCounter'];
    $privateQuestion = $question['private'] ? '<small class="private">' . xg_html('PRIVATE') . '</small>' : '';
    $safeTitle = ($question['private'] ? '<span class="icon private"></span>' : '') . ($question['required'] ? '<span class="icon required"></span>' : '') . xnhtmlentities($question['question_title']);
    if ($this->onlyShowRequired && !$question['required']) {
        continue;
    }
    if ($question['answer_type'] == 'text') { ?>
        <div class="row">
            <label for="<%= $questionFieldName %>"><%= $safeTitle %></label>
            <%= $this->form->text($questionFieldName,'id="'.$questionFieldName.'"') %>
        </div>
    <?php
    } elseif ($question['answer_type'] == 'textarea') { ?>
        <div class="row">
            <label for="<%= $questionFieldName %>"><%= $safeTitle %></label>
			<%= $this->form->textarea($questionFieldName, 'class="lighter" id="'.$questionFieldName.'" _default="'.qh(xg_html('TAP_HERE_TO_BEGIN_WRITING')).'"') %>
        </div>
    <?php
    } elseif ($question['answer_type'] == 'select') {
    	$choices = explode(',',$question['answer_choices']); ?>
    	<div class="row">
    	<label><%= $safeTitle%></label> <?php
    	if ($question['answer_multiple']) { ?>
    		<ul class="table lined variable"> <?php
    		foreach ($choices as $i => $choice) {
    			$optionFieldName = $questionFieldName . "_option$i";
    			$choice = trim($choice);
    			$checkimg = 'checkimg_' . $optionFieldName; ?>
    			<li _checkbox="<%= $optionFieldName %>" _checkimg="<%= $checkimg %>" onclick="javascript:void(0)"><label for="<%= $optionFieldName %>" class="checkbox"><%= xnhtmlentities($choice) %></label>
    				<span id="<%= $checkimg %>" class="mark check"></span></li><%= $this->form->checkbox($questionFieldName.'[]',$choice,'checked="true" class="hidden_checkbox" id="'.$optionFieldName.'"') %> <?php
    		} ?>
    		</ul> <?php
    	} else {
			$values = array();
			foreach($choices as $choice) {
				$choice = trim($choice);
				$values[$choice] = $choice;
			}
			echo $this->form->select($questionFieldName, $values, false, 'id="'.$questionFieldName.'"');
    	} ?>
        </div> <?php
    } elseif ($question['answer_type'] == 'url') { ?>
        <div class="row">
            <label for="<%= $questionFieldName %>"><%= $safeTitle %></label>
			<%=$this->form->text($questionFieldName, 'id="'.$questionFieldName.'"') %>
        </div>
    <?php
    } elseif ($question['answer_type'] == 'date') { ?>
        <div class="row">
            <label for="<%= $questionFieldName %>_month"><%= $safeTitle %></label>
            <%= $this->form->select($questionFieldName.'_month', $this->months,false,'class="date" id="'.$questionFieldName.'_month"'); %>
            <%= $this->form->select($questionFieldName.'_day', $this->days,false,'class="date" id="'.$questionFieldName.'_day"'); %>
            <%= $this->form->select($questionFieldName.'_year', $this->years,false,'class="date" id="'.$questionFieldName.'_year"'); %>
        </div>
    <?php
    } ?>
<?php
} /* each question */ ?>
