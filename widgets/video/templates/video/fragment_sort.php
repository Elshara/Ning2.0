<?php
/**
 * Example:
 * $this->renderPartial('fragment_sort', 'video', array(
 *          'changeUrl' => $this->_buildUrl('index', 'index'), 'sortParamName' => 'sort', 'selectedSorting' => $this->sort));
 *
 * @param changeUrl The url of for submitting the change of sort order. May contain parameters.
 * @param sortParamName   The name of the sort parameter
 * @param selectedSorting The currently selected sorting - one of the items returned by Video_VideoHelper::getKnownSortingOrders()
 */
 ?>
<%= xg_html('SORT_BY');%> 
<select onchange="javascript:location.href = '<%= Video_HtmlHelper::addParamToUrl($changeUrl, $sortParamName, '', false) %>' + this.options[this.selectedIndex].value;">
    <?php
    foreach (Video_VideoHelper::getKnownSortingOrders() as $optionValue => $optionData) { ?>
        <option value="<%= $optionValue %>" <?php if ($selectedSorting['name'] == $optionData['name']) { $selectedSortingValue = $optionValue; ?>selected="selected"<?php } ?>><%= xnhtmlentities($optionData['name']) %></option>
    <?php
    } ?>
</select>

