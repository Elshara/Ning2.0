dojo.provide('xg.index.authorization.profileInfoForm');

/**
 * Behavior for the "profile info" section of the Create Profile and Edit Profile pages
 */
(function() {

    var aboutSection = dojo.byId('aboutSection');
    if (aboutSection) {
        dojo.event.connect(aboutSection.getElementsByTagName('a')[0], 'onmousedown', function(event) {
            dojo.style.hide(aboutSection);
            dojo.style.show(dojo.byId('infoSection'));
            dojo.byId('aboutQuestionsShown').value = 'Y';
        });
    }

}());
