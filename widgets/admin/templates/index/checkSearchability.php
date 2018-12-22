<?php if (count($this->resetModels)) { ?>
    <p><%= xg_html('MODELS_RESET') %></p>
    <ul>
    <?php foreach ($this->resetModels as $model) { ?>
        <li><%= xnhtmlentities($model) %></li>
    <?php } ?>
    </ul>
<?php } else { ?>
    <%= xg_html('NO_MODELS_RESET'); %>
<?php } ?>

