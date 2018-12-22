<div class="xg_module_body">
    <table class="categories">
        <thead>
            <tr>
                <th class="xg_lightborder"><%= xg_html('FEATURED_DISCUSSIONS') %></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($featuredTopics as $topic) {
                $categoryLink = $this->usingCategories ? array('href="' . xnhtmlentities($this->_buildUrl('topic', 'listForCategory', array('categoryId' => $topic->my->categoryId))) . '"', xnhtmlentities($this->categories[$topic->my->categoryId]->title)) : null;
                $this->renderPartial('fragment_discussion', 'topic', array('topic' => $topic, 'showDescription' => !$this->showingReplies, 'categoryLink' => $categoryLink, 'reply' => $this->showingReplies ? $topicOrComment : null, 'featured' => true));?>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php if ($showFeaturedViewAll) { ?>
    <div class="xg_module_foot">
        <p class="right">
        <a href="<%= xnhtmlentities($this->_buildUrl('topic', 'featured')) %>"><%= xg_html('VIEW_ALL') %></a>
        </p>
    </div>
<?php } ?>