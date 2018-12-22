<div id="add-<%= $this->prefix %>" class="add_section">
  <h3><%= xg_html('ADD_YOUR_FIRST_BLOG_POST') %></h3>
  <img class="feature_logo" alt="" src="<%= xg_cdn(W_Cache::getWidget('main')->buildResourceUrl('gfx/features/blog.gif')) %>" />
  <fieldset class="nolegend">
  <p>
    <label for="<%= $this->prefix %>_subject"><%= xg_html('SUBJECT') %></label><br />
    <input id="<%= $this->prefix %>_subject" name="<%= $this->prefix %>[subject]" type="text" class="textfield" />
  </p>
  <p>
    <label for="<%= $this->prefix %>_entry"><%= xg_html('ENTRY') %></label><br />
    <textarea id="<%= $this->prefix %>_entry" name="<%= $this->prefix %>[entry]" rows="10" cols="45"><%= xg_html('MY_FIRST_BLOG_POST') %></textarea>
  </p>
</fieldset>
</div>
