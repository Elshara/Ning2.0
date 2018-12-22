dojo.provide("xg.index.membership.questions");

dojo.require('xg.index.util.FormHelper');
dojo.require('xg.shared.util');
dojo.require('dojo.dnd.*');
dojo.require('dojo.lfx.*');

xg.index.membership.questions = {

    maxCounter: 0,

    getMaxCounter: function() { return xg.index.membership.questions.maxCounter; },
    setMaxCounter: function (c) { xg.index.membership.questions.maxCounter = c; },

    /**
     * Checks whether the current form has any profile questions.  Returns true
     * if there are one or more questions, else false.
     */
    hasQuestions: function(container) {
        if (container && container.childNodes) {
            for (var i = 0; i < container.childNodes.length; i++) {
                if ((container.childNodes[i].nodeName.toLowerCase() == 'fieldset') &&
                    (container.childNodes[i].id.match(/^question_\d+$/i))) {
                    return true;
                }
            }
        }
        return false;
    },

    /**
     * Controls whether or not the "no questions" div is shown based on whether
     * there are any profile questions.
     */
    showOrHideQuestionsDiv: function() {
        var container = dojo.byId('xg_membership_question_container');
        if (! xg.index.membership.questions.hasQuestions(container)) {
            dojo.byId('xg_membership_no_questions_div').className = 'xg_module_body';
            dojo.byId('xg_membership_questions_div').className = 'hidden';
        } else {
            dojo.byId('xg_membership_no_questions_div').className = 'hidden';
            dojo.byId('xg_membership_questions_div').className = 'xg_module_body';
        }
    },

    /**
     * Uses an AJAX call to retrieve an HTML fragment to add a question
     *
     * @param   counter     An integer counter for use in field/div ids for
     *                      disambiguation
     * @param   fieldset    The question after which the new one should be
     *                      added.  Pass false if this is the first question
     */
    addQuestionProper: function(counter, fieldset) {
            var container = dojo.byId('xg_membership_question_container');
            if (! container || ! container.childNodes) { return false; }
            var url = '/index.php/main/membership/getQuestion?counter=' + counter + '&xn_out=htmljson';
            dojo.io.bind({'url': url,
                          'mimetype' : 'text/javascript',
                          'load': function(type, data, evt) {
                              var nodes = dojo.html.createNodesFromText(data.html);
                              // Some of the nodes are whitespace, comments, etc.
                              for (var i in nodes) {
                                  if (nodes[i].nodeName == 'FIELDSET') {
                                      dojo.style.setOpacity(nodes[i], 0);
                                      if (fieldset) {
                                          dojo.dom.insertAfter(nodes[i], fieldset);
                                      } else {
                                          container.appendChild(nodes[i]);
                                      }
                                      nodes[i].getElementsByTagName('input')[0].focus();
                                      xg.index.util.FormHelper.scrollIntoView(nodes[i]);
                                      dojo.lfx.fadeIn(nodes[i], 500, dojo.lfx.easeIn).play();
                                      break;
                                  }
                              }
                              xg.index.membership.questions.activateQuestion(counter);
                              xg.index.membership.questions.showOrHideQuestionsDiv();
                          }
            }); /* bind */
    },

    /**
     * Click action associated with the "Add a question" button; should only
     * work if there are no questions present so we check first using
     * hasQuestions.  Calls addQuestionProper to actually add the element.
     */
    addFirstQuestion: function() {
        var container = dojo.byId('xg_membership_question_container');
        if (container && container.childNodes) {
            // no questions are present, proceed with adding first element - assume counter=0
            if (! this.hasQuestions(container)) {
                this.addQuestionProper(0, false);
            }
        }
    },

    /**
     * Adds all the javascript actions to a newly added question for the remove
     * and add links, etc.
     *
     * @param   counter     An integer value corresponding to the question's
     *                      counter value
     */
    activateQuestion: function(counter) {
        var fieldset = dojo.byId('question_' + counter);
        new dojo.dnd.HtmlDragSource(fieldset);
        if (! fieldset) { return; }
        if (counter > this.getMaxCounter()) { this.setMaxCounter(counter); }

        /* show the "choices" area when the "answer type" menu is set to "Multiple Choice" */
        dojo.event.connect(dojo.byId('answer_type_' + counter), 'onchange', function() {
            if (dojo.byId('answer_type_' + counter).value == 'select') {
                dojo.html.show(dojo.byId('choices_container_' + counter));
            } else {
                dojo.html.hide(dojo.byId('choices_container_' + counter));
            }
        });

        /* remove the question when the 'remove' link is clicked */
        var remove_link = dojo.byId('remove_' + counter);
        if (remove_link) {
            dojo.event.connect(remove_link, 'onclick', function(evt) {
                dojo.event.browser.stopEvent(evt);
                fieldset.parentNode.removeChild(fieldset);
                xg.index.membership.questions.showOrHideQuestionsDiv();
            });
        }

        /* add another question when the 'add' link is clicked */
        dojo.event.connect(dojo.byId('add_' + counter), 'onclick', function(evt) {
            dojo.event.browser.stopEvent(evt);
            /* find the form that the fieldset belongs to and put another question onto its bottom */
            var form = dojo.html.getFirstAncestorByTag(fieldset, 'form');
            var newCounter = xg.index.membership.questions.getMaxCounter() + 1;
            xg.index.membership.questions.addQuestionProper(newCounter, fieldset);
        });

    },

    /**
     * Activates an array of questions
     *
     * @param   ar      An array of question counter values
     */
    activateQuestions: function(ar) {
        for (var i in ar) {
            this.activateQuestion(ar[i]);
        }
    },

    /**
     * Validates all of the proposed questions that are multiple choice to
     * ensure they have at least one available answer value.
     */
    validateQuestions: function() {
        var errors = { };
        var fieldsets = document.getElementsByTagName('fieldset');
        for (var i in fieldsets) {
            var m;
            if (fieldsets[i].id && (m = fieldsets[i].id.match(/^question_(\d+)$/))) {
                var counter = m[1];
                dojo.byId('position_' + counter).value = i;
                // Allow questions with empty titles -- they'll just get ignored
                /* if (dojo.string.trim(dojo.byId('question_title_' + counter).value).length === 0) {
                    errors['question_title_' + counter] = 'Please enter the text for the profile question e.g. Hobbies';
                } */
                if (('select' == dojo.byId('answer_type_' + counter).value) &&
                (dojo.string.trim(dojo.byId('answer_choices_' + counter).value).length === 0)) {
                    var questionTitle = dojo.byId('question_title_' + counter);
                    var questionTitleString = '';
                    if (questionTitle && dojo.string.trim(questionTitle.value).length) {
                        errors['answer_choices_' + counter] = xg.index.nls.html('pleaseEnterTheChoicesFor', dojo.string.escape('html', dojo.string.trim(questionTitle.value)));
                    } else {
                        errors['answer_choices_' + counter] = xg.index.nls.html('pleaseEnterTheChoices');
                    }
                }
            }
        }
        return errors;
    },

    initialClickStates: { },
    haveClearedOnInitialClick: function(id) {
        return (xg.index.membership.questions.initialClickStates[id] === true);
    },
    clearOnInitialClick: function(idOrNode) {
        var node = dojo.byId(idOrNode);
        if (node) {
            dojo.event.connect(node, 'onclick', function() {
                if (! xg.index.membership.questions.haveClearedOnInitialClick(node.id)) {
                    node.value = '';
                    xg.index.membership.questions.initialClickStates[node.id] = true;
                }
            });
        }
    },

    submitForm: function(evt) {
        if (evt) {
            dojo.event.browser.stopEvent(evt);
        }
        var form = dojo.byId('questions_form');
        var formIsValid = xg.index.util.FormHelper.runValidation(form, xg.index.membership.questions.validateQuestions);
        if (formIsValid) {
            this.confirmChangingPrivacyIfNecessary(dojo.lang.hitch(this, function() {
                form.submit();
            }));
        }
    },

    /**
     * Warns the user about privacy implications if she has changed any private
     * questions to public.
     *
     * @param callback  function to call if no private questions have been made
     *         public or if the user chooses to save anyway
     */
    confirmChangingPrivacyIfNecessary: function(callback) {
        if (! this.privateQuestionsMadePublic()) {
            callback();
        } else {
            xg.shared.util.confirm({
                title: xg.index.nls.text('changeQuestionsToPublic'),
                bodyHtml: '<p>' + xg.index.nls.html('changingPrivateQuestionsToPublic') + '</p>',
                okButtonText: xg.index.nls.text('change'),
                onOk: callback
            });
        }
    },

    /**
     * Returns whether any private questions have been made public.
     *
     * @return  true if the user unchecked any Private checkboxes
     */
    privateQuestionsMadePublic: function() {
        return dojo.lang.filter(dojo.byId('xg_membership_question_container').getElementsByTagName('input'), function(input) { return !input.checked && input.getAttribute('_originallyChecked') == 'Y'; }).length > 0;
    },

    handleLaunchBarSubmit: function(url, evt) {
        dojo.event.browser.stopEvent(evt);
        var form = dojo.byId('questions_form');
        if (form.successTarget && url) {
            form.successTarget.value = url;
        }
        xg.index.membership.questions.submitForm();
    }
};

xg.addOnRequire(function() {
    var form = dojo.byId('questions_form');
    dojo.event.connect(form, 'onsubmit', xg.index.membership.questions, 'submitForm');
    var dropTarget = new dojo.dnd.HtmlDropTarget(dojo.byId('xg_membership_question_container'), '*');
    dropTarget.createDropIndicator = function() {
        this.dropIndicator = document.createElement("div");
        dojo.html.addClass(this.dropIndicator, 'fieldset_drop_indicator');
        this.dropIndicator.style.left = dojo.style.getAbsoluteX(this.domNode, true) + "px";
    };
});
