<?php
XG_App::ningLoaderRequire('xg.shared.SubTabHover');
XG_App::includeFileOnce('/lib/XG_TabLayout.php');
$this->_widget->includeFileOnce('/lib/helpers/Index_TablayoutHelper.php');
XG_App::addToCssSection(Index_TablayoutHelper::createInternalStyleSheet($subTabColors));
foreach($tabs as $tabKey => $tab){
    $div = '';
    $dojoType = '';
    $classes = array('xg_subtab');
    if($tab['tabKey'] == $this->navHighlight) {
        $classes[] = 'this';
    }
    if(count($tab['subTabs']) > 0){
        $dojoType = 'dojoType="SubTabHover" ';
        $div .= '<div class="xg_subtab" style="display:none;position:absolute;"><ul class="xg_subtab" style="display:block;" >';
        foreach($tab['subTabs'] as $subTabKey => $subTab){
            $div .= '<li style="list-style:none !important;display:block;text-align:left;">' . Index_TablayoutHelper::formatTabAnchor($subTab, true) . '</li>';
        }
        $div .= '</ul></div>';
    }
    echo '<li ' . $dojoType . 'id="xg_tab_' . $tab['tabKey'] . '" class="' . implode(' ', $classes) . '">' . Index_TablayoutHelper::formatTabAnchor($tab);
    echo $div. '</li>';
}
