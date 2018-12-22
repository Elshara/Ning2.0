dojo.provide('xg.profiles.profile.emailSettings');

xg.addOnRequire(function() {

    // When you click the 'opt out' button or check the 'never' checkbox,
    // clear all the other email notification checkboxes
    var form = dojo.byId('settings_form');
    var optOutCheckbox = form['emailNeverPref'];
    var optInCheckboxes = [];
    dojo.lang.forEach(dojo.html.getElementsByClass('email-optin',form,'input'),function(checkbox) {
        //  Find all email opt in checkboxes, make each disable the opt out checkbox when clicked
        optInCheckboxes.push(checkbox);
        if (optOutCheckbox) {
            dojo.event.connect(checkbox,'onclick',function(evt) {
                optOutCheckbox.checked = false;
            });
        }
    }, false);
    var clearOptInCheckboxes = function() {
        dojo.lang.forEach(optInCheckboxes, function(checkbox) {
            checkbox.checked = false;
        }, false);
    };

    if (optOutCheckbox) {
        //  Now make the opy out checkbox clear all of the opt IN checkboxes when clicked
        // We must attach to onclick here (not onchange) so as to
        // appease IE (see http://www.webmasterworld.com/forum91/1770.htm)
        dojo.event.connect(optOutCheckbox, 'onclick',function(evt) {
            if (optOutCheckbox.checked == true) {
                clearOptInCheckboxes();
            }
        });
    }
}); /* addOnRequire */
