<?php
// @param numPages
// @param sort

if ($numPages > 1) { ?>
    <p class="sort">
        <label for="activity"><%= xg_html('SORT_BY') %> &nbsp;</label>
        <select id="activity" tabindex="1" onchange="javascript:location.href = '<?php echo Video_HtmlHelper::addParamToUrl($this->pageUrl, 'sort', '', false) ?>' + this.options[this.selectedIndex].value;">
            <?php foreach (Video_UserHelper::getKnownSortingOrders() as $optionValue => $optionData) { ?>
                <option value="<?php echo $optionValue ?>" <?php if ($sort['name'] == $optionData['name']) { ?>selected="selected"<?php } ?>><?php echo xnhtmlentities($optionData['name']) ?></option>
            <?php } ?>
        </select>
    </p>
<?php
}
