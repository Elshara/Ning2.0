dojo.provide('xg.index.invitation.pageLayout');

dojo.require('xg.index.invitation.zColor');

/**
 * Boxifies invitation page.
 *
 * Exactly what happens is dependant on the theme.  There are five different cases identified dynamically by checking
 * css properties of nodes.
 */
xg.index.invitation.pageLayout = {
    /**
     * Boolean to determine if boxify() needs to be rerun when xg.index.invitation.chooseInvitationMethod causes
     * an invitation method to expand or contract.
     * Set in boxify() case 3 (and 4, if IE6) and used to determine if we need to run boxify() again in recompute()
     */
    recomputeOnResize: false,

    /**
     * Case 4 creates two divs on either side of the center column in a complementary color.
     * When recomputeOnResize is true, subsequent calls to boxify need to remove the previous side divs.
     * To provide easy access to these nodes, they are stored here in a two element array.
     */
    sideDivs: null,

    /**
     * Checks the page and based on what theme, or custom css is showing,
     * makes the invite section stand out from the rest of the page.
     *
     * Run once immediately after load, and then again as needed by recompute()
     */
    boxify: function(){
        var xg_body_color = dojo.style.getBackgroundColor('xg');
		var theme_type_5_color = dojo.style.getBackgroundColor('xg_body');
		theme_type_5_color = "rgb(" + theme_type_5_color[0] + "," + theme_type_5_color[1] + "," + theme_type_5_color[2] + ")";
        var oRGB = new xg.index.invitation.zColor.zRGB(xg_body_color[0],xg_body_color[1],xg_body_color[2]);
        var oHSL = oRGB.toHSL();
        var shade;
        if (oHSL.l > 125){
            oHSL.l = Math.round(oHSL.l /5);
            shade = 'black';
        }else{
            if (oHSL.l < '25') {
                oHSL.l = '40';
            }
            oHSL.l = Math.round(oHSL.l * 1.75);
            shade = 'white';
            if (oHSL.l > 255) {
                oHSL.l = 255;
            }
        }
        var nHSL = new xg.index.invitation.zColor.zHSL(oHSL.h, oHSL.s, oHSL.l);
        var nRGB = nHSL.toRGB();
        var xg_2col = dojo.html.getElementsByClass('xg_2col')[0];

        dojo.style.setStyleAttributes(xg_2col, "padding:0px;margin-left:235px;");
        var xg_module_bodies = dojo.html.getElementsByClass('xg_module_body', dojo.byId('xg_body'));
		dojo.lang.forEach(xg_module_bodies, function(val){
			dojo.style.setStyleAttributes(val, "padding-left:20px;padding-right:20px;");
		});
		dojo.lang.forEach(dojo.byId('xg_body').getElementsByTagName('h1'), function(val){
							  dojo.style.setStyleAttributes(val, "padding-left:20px;padding-right:20px");
		});
		// checking for transparency must check that the background-color is 'transparent'
        // or 'rgba(0, 0, 0, 0)', the latter is returned by webkit browsers
        if (dojo.style.getStyle('xg_body', 'background-color') != dojo.style.getStyle('.xg_module_body','background-color') &&
            dojo.style.getStyle('xg_body', 'background-color') != 'transparent' &&
            dojo.style.getStyle('xg_body', 'background-color') != 'rgba(0, 0, 0, 0)' &&
            dojo.style.getStyle('xg_body', 'background-image') == 'none' &&
            dojo.style.getStyle(xg_module_bodies[0],'background-color') != 'transparent' &&
            dojo.style.getStyle(xg_module_bodies[0],'background-color') != 'rgba(0, 0, 0, 0)') {
            //if theme is old/boxy like old blue jeans, don't do anything
        } else if ((dojo.style.getStyle('xg_body', 'background-color') == 'transparent' ||
                    dojo.style.getStyle('xg_body', 'background-color') == 'rgba(0, 0, 0, 0)') &&
                   (!((dojo.style.getStyle(xg_module_bodies[0],'background-color') == 'transparent' ||
                     dojo.style.getStyle(xg_module_bodies[0], 'background-color') == 'rgba(0, 0, 0, 0)')) &&
                    dojo.style.getStyle(xg_module_bodies[0],'background-image') != 'none')) {
            //if #xg_body is transparent but xg_module_body has a background color and/or a background image,
            //therefore already creating a boxy feel like veejay, do nothing
        } else if(dojo.style.getStyle('xg_body','background-color') == 'transparent' ||
                  dojo.style.getStyle('xg_body','background-color') == "rgba(0, 0, 0, 0)"){
            // xg_body is transparent, add divs around xg_module_body that bring attention to them
            xg.index.invitation.pageLayout.recomputeOnResize = true;
            dojo.lang.forEach(xg_module_bodies,function(val, _ignore, _ignore2){
                                  dojo.style.setStyle(val, 'position', 'relative');
                                  dojo.style.setStyle(val, 'z-index', '10');
                                  var opacity = shade == 'white' ? '0.10' : '0.15';
                                  var absPosition = dojo.style.getAbsolutePosition(val,true);
                                  var heightOff = dojo.style.getAbsolutePosition(dojo.html.getElementsByClass('xg_module')[0],true);
                                  absPosition.y = absPosition.y - heightOff.y;
                                  var width = dojo.style.getBorderBoxWidth(val);
                                  var height = dojo.style.getBorderBoxHeight(val) - 1;
                                  var dInsert = dojo.html.createNodesFromText('<div class="xg_invitation_pageLayout_underlay" style="position:absolute;filter:alpha(opacity=' + (opacity * 100) + ');opacity:' + opacity + ';z-index:1;top:' + absPosition.y + 'px;background-color:' + shade + ';width:' + width + 'px;height:' + height + 'px"></div>')[0];
                                  dojo.dom.insertAfter(dInsert,val);
                              });

        }else if(dojo.style.getStyle('xg_body','background-image') != 'none'){
            // background image, add divs on each side to make the invitation stand out
			if(this.sideDivs !== null){
				dojo.dom.removeNode(this.sideDivs[0]);
				dojo.dom.removeNode(this.sideDivs[1]);
			}
            dojo.html.setStyle(dojo.byId('xg_body'),"position","relative");
			var xg_body_height = "100%";
			if(dojo.render.html.ie60){
				xg.index.invitation.pageLayout.recomputeOnResize = true;
				xg_body_height = dojo.style.getBorderBoxHeight(dojo.byId('xg_body')) + 'px';
			}
            dojo.lang.forEach(xg_module_bodies,function(val, _ignore, _ignore2){
				dojo.style.setStyle(val,'border-bottom', '1px solid');
			});
            var d1 = dojo.html.createNodesFromText('<div style="height:' + xg_body_height + ';width:245px;position:absolute;top:0;left:0;filter:alpha(opacity=50);opacity:0.5;background-color:' + nRGB + '"></div>')[0];
            var d2 = dojo.html.createNodesFromText('<div style="height:' + xg_body_height + ';width:245px;position:absolute;top:0;right:0;filter:alpha(opacity=50);opacity:0.5;background-color:' + nRGB + '"></div>')[0];
			xg.index.invitation.pageLayout.sideDivs = [d1,d2];
            var colgroup = dojo.html.getElementsByClass('xg_colgroup', dojo.byId('xg_body'))[0];

            dojo.dom.insertAfter(d1, colgroup);
            dojo.dom.insertAfter(d2,d1);
            dojo.html.setStyle(xg_2col, "opacity",1.0);
            dojo.html.setStyle(xg_2col, "position","relative");

        }else{
            // plain theme, change the background colors
		    dojo.lang.forEach(xg_module_bodies,function(val, _ignore, _ignore2){
				dojo.style.setStyle(val,'border-bottom', '1px solid');
			});
            dojo.html.setStyle(xg_2col,"background-color", theme_type_5_color);
            dojo.html.setStyle(dojo.byId('xg_body'),'background-color', nRGB.toString());
        }
    },

    /**
     * Called from xg.index.invitation.chooseInvitationMethod when an invitation method is expanded or contracted
     *
     * Only executes boxify() if recomputeOnResize is true
     * If we do have to run boxify() again, the animation is jumpy because chooseInvitationMethod animates
     * the invitation method expanding and not the background divs added by pageLayout.
     * Need to add animations for each case with background divs?
     */
    recompute: function(){
        if(xg.index.invitation.pageLayout.recomputeOnResize !== true){
            return;
        }
        dojo.lang.forEach(dojo.html.getElementsByClass('xg_invitation_pageLayout_underlay'), function(el){
                              dojo.dom.removeNode(el);
                          });
        xg.index.invitation.pageLayout.boxify();
    }
};

/**
 * run boxify() on load
 */
(function(){
    xg.index.invitation.pageLayout.boxify();
})();
