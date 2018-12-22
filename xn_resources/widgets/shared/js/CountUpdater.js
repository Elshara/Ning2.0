dojo.provide('xg.shared.CountUpdater');

/**
 * Provide an interface for updating counts dynamically
 *
 * When updating counts, one also passes a count key.  This key is used to determine
 * what nodes in the DOM to update.  This module supports dynamic text by count values.
 * To set it up:
 *
 * <span class="xj_count_key xj_count_key_0">...</span>
 * <span class="xj_count_key xj_count_key_n">...default for all other count values...</span>
 *
 * When updating the count, the new count will be used to determine which span to show.
 * It is important that you also include the base class "xj_count_key" in addition to the
 * count-specific class.
 *
 * Within the above spans, you may include as many <span class="xj_count">#</span> nodes as
 * you wish.  In addition to selectively showing a span based on the count, all counts will be
 * updated in spans with a class of "xj_count".  The update is limited only to xj_count spans
 * that are nested in xj_count_key for the specified key the caller is updating, therefore it
 * is safe to use this to update multiple unrelated counts on the same page.  *NOTE* the
 * xj_count span must containing nothing but the count value since we update it by setting
 * .innerHTML.
 *
 * Similarly, because we use CSS classes, it's possible for one key to have multiple update
 * locations in the DOM.
 *
 * Example usage:
 * - $numFriends is the number of friends in this example
 * 
 * <span class="xj_count_numfriends xj_count_numfriends_0"<%= $numFriends > 0 ? ' style="display:none;"' : '' %>>You don't have any friends!</span>
 * <span class="xj_count_numfriends xj_count_numfriends_n"<%= $numFriends > 0 ? '' : ' style="display:none;"' %>>You have <span class="xj_count">x</span> friends!</span>
 * <?php XG_App::ningLoaderRequire('xg.shared.CountUpdater'); ?>
 *
 * Then in any actions which update the number of friends, use:
 * xg.shared.CountUpdater.set('numfriends', <new value>);
 */

xg.shared.CountUpdater = {

    set: function(key, value) {
        x$('span.xj_count_'+key).hide();
        dojo.lang.forEach(x$('span.xj_count_'+key+' span.xj_count'), function(span) {
            x$(span).html(xg.shared.util.formatNumber(value));
        });
        if (x$('span.xj_count_'+key+'_'+value).length > 0) {
            x$('span.xj_count_'+key+'_'+value).show();
        } else if (x$('span.xj_count_'+key+'_n').length > 0) {
            x$('span.xj_count_'+key+'_n').show();
        }
    },

    _getCurrentValue: function(key) {
        if (x$('span.xj_count_'+key+' span.xj_count').length > 0) {
            return xg.shared.util.parseFormattedNumber(x$('span.xj_count_'+key+' span.xj_count').html());
        }
        return -1;
    },

    increment: function(key, delta) {
        this.set(key, this._getCurrentValue(key) + delta);
    },
    
    decrement: function(key, delta) {
        this.set(key, this._getCurrentValue(key) - delta);
    }

};
