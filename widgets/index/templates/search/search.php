<?php xg_header('main', $this->pageTitle) ?>
<input id="xg_bar_menu_search_query" style="display: none" value="<%= xnhtmlentities($this->term) %>" />
<script type="text/javascript">
<?php /* Populate the ningbar search box with the search term */ ?>
document.getElementById('xn_bar_menu_search_query').value =  document.getElementById('xg_bar_menu_search_query').value;
</script>
<div id="xg_body">
 <div class="xg_colgroup">
   <div class="xg_4col first-child"><h1 id="chatterwall"><%= xnhtmlentities($this->pageTitle) %></h1></div>
   <div class="xg_colgroup">
     <div class="xg_3col first-child">
      <div class="xg_module"><?php $this->renderPartial('content',array('content' => $this->content)); ?>
      <?php $this->renderPartial('fragment_pagination', array('targetUrl' => $this->_buildUrl('search','search',array('q' => $this->term)),
                                                            'pageParamName' => 'page',
                                                            'curPage' => $this->page,
                                                            'numPages' => $this->numPages)); ?>
      </div>
     </div>
     <div class="xg_1col">
       <div class="xg_1col first-child"><?php xg_sidebar($this); ?></div>
     </div>
   </div>
  </div>
</div>
<?php xg_footer(); ?>
