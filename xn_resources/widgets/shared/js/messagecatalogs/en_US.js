dojo.provide('xg.shared.messagecatalogs.en_US');

dojo.require('xg.index.i18n');

/**
 * Texts for the English (U.S.) locale.
 */
// Use UTF-8 byte sequences instead of HTML entities, e.g., & instead of &amp;, … instead of &hellip;  [Jon Aquino 2007-01-10]

dojo.evalObjPath('xg.feed.nls', true);
dojo.lang.mixin(xg.feed.nls, xg.index.i18n, {
    edit: 'Edit',
    title: 'Title:',
    feedUrl: 'URL:',
    show: 'Show:',
    titles: 'Titles Only',
    titlesAndDescriptions: 'Detail View',
    display: 'Display',
    cancel: 'Cancel',
    save: 'Save',
    loading: 'Loading…',
    items: 'items'
});

dojo.evalObjPath('xg.opensocial.nls', true);
dojo.lang.mixin(xg.opensocial.nls, xg.index.i18n, {
    edit: 'Edit',
    title: 'Title:',
    untitled: 'Untitled',
    appUrl: 'URL:',
    cancel: 'Cancel',
    save: 'Save',
    loading: 'Loading…',
    removeBox: 'Remove Box',
    removeBoxText: function(app) { return '<p>Are you sure you want to remove the "' + app + '" box from My Page?</p><p>You\'ll still be able to access this application from My Applications.</p>'},
    removeApplication: 'Remove Application',
    removeApplicationText: 'Are you sure you want to remove this application completely? It will no longer be available from My Page or My Applications.',
    removeBoxAndRemoveApplication: 'Remove Box / Remove Application',
    removeBoxAndRemoveApplicationHelp: '<p>\'Remove Box\' will remove this application box from your profile page only.</p><p>\'Remove Application\' will remove the application from your profile page and My Applications list.</p>',
    canSendAlerts: 'Send me and my friends alerts',
    allowSendAlerts: 'Allow this application to send me and my friends alerts',
    canAddActivities: 'Add updates in the Latest Activity module on My Page',
    canShowActivities: 'Allow this application to add updates in the Latest Activity module on My Page',
    addApplication: 'Add Application',
    youCanOnlyAdd: function(limit) { return 'You can only add up to '+limit+' applications. Please remove one or more features and try again.' },
    yourApplicationIsBeingAdded: 'Your application is being added.',
    yourApplicationIsBeingRemoved: 'Your application is being removed.',
    onlyEmailMsgSupported: 'Only EMAIL message type is supported',
    msgExpectedToContain: 'Message is expected to contain all fields: type, title and body',
    msgObjectExpected: 'Message object expected',
    recipientsShdBeStringOrArray: 'Recipients can only be a string (comma-separated list is ok) or an Array',
    recipientsShdBeSpecified: 'Recipients should be specified and can not be empty',
    unauthorizedSender: 'Unauthorized Sender: only logged-in members can send messages',
    unauthorizedRecipients: 'Unauthorized recipients specified to send mail to',
    rateLimitExceeded: 'Rate limit exceeded',
    operationCancelled: 'Operation cancelled',
    // TODO: waiting on terms of use link location
    youAreAboutToAdd: function(app, linkAttributes) { return '<p>You are about to add <strong>' + app + '</strong> to your My Page. This application was developed by a third party.</p><br/><p>By clicking \'Add Application\' you agree to the <a ' + linkAttributes + '>Applications Terms of Use</a>.</p>'},
    youAreAboutToAddNing: function(app, linkAttributes) { return '<p>You are about to add <strong>' + app + '</strong> to your My Page. This application was developed by Ning.</p><br /><p>By clicking \'Add Application\' you agree to the <a ' + linkAttributes + '>Applications Terms of Use</a>.</p>'},
    followingMessageWasSent: function(recipients, title, message) { return '<p>Following message was sent to '+recipients+'. <blockquote><strong><em>'+title+'</em></strong><br/>'+message+'</blockquote></p>'},
    reviewIsTooLong: function(len) { return 'Your review is '+len+' characters long.  The maximum is 4000.' },
    mustSupplyRating: 'Please supply a rating along with your review.',
    mustSupplyReview: 'Your review must include some text.',
    messageWasNotSent: function(cause) { return '<p>Message was <strong>not</strong> sent because: <strong>'+cause+'</strong>.'},
    settingIsDontSendMessage: 'Application setting is set to "Don\'t send messages"',
    applicationSettings: 'Application Settings',
    messageSent: 'Message Sent',
    messageNotSent: 'Message Not Sent',
    allowThisApplicationTo: 'Allow this application to:',
    updateSettings: 'Update Settings',
    isOnMyPage: 'Add a box on My Page',
    youNeedToAddEmailRecipient: 'You need to add an email recipient.',
    yourMessageIsBeingSent: 'Your message is being sent.',
    sendingLabel: 'Sending...',
    deleteReview: 'Delete Review',
    deleteReviewQ: 'Delete review?',
    replaceReview: 'Replace Review',
    replaceReviewQ: 'You have already added a review.  Would you like to replace the existing review?',
    'delete': 'Delete',
    addApplicationConfirmation: function(linkAttributes) { return '<p>You are about to add a new application to your My Page. This application was developed by a third party.</p><br /><p>By clicking \'Add Application\' you agree to the <a ' + linkAttributes + '>Applications Terms of Use</a>.</p>'; },
    thereHasBeenAnError: 'There has been an error',
    whatsThis: 'What\'s This?'    
});

dojo.evalObjPath('xg.forum.nls', true);
dojo.lang.mixin(xg.forum.nls, xg.index.i18n, {
    items: 'items',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'The number of characters (' + n + ') exceeds the maximum (' + maximum + ')'; },
    pleaseEnterFirstPost: 'Please write the first post for the discussion',
    pleaseEnterTitle: 'Please enter a title for the discussion',
    save: 'Save',
    cancel: 'Cancel',
    yes: 'Yes',
    no: 'No',
    edit: 'Edit',
    ok: 'OK',
    deleteCategory: 'Delete Category',
    discussionsWillBeDeleted: 'The discussions in this category will be deleted.',
    whatDoWithDiscussions: 'What would you like to do with the discussions in this category?',
    moveDiscussionsTo: 'Move discussions to:',
    deleteDiscussions: 'Delete discussions',
    'delete': 'Delete',
    deleteReply: 'Delete Reply',
    deleteReplyQ: 'Delete this reply?',
    deletingReplies: 'Deleting Replies…',
    doYouWantToRemoveReplies: 'Do you also want to remove the replies to this comment?',
    pleaseKeepWindowOpen: 'Please keep this browser window open while processing continues. It may take a few minutes.',
    contributorSaid: function(x) { return x + ' said:'},
    /*Embed Options*/
    display: 'Display',
    from: 'From',
    show: 'Show',
    view: 'View',
    discussions: 'discussions',
    discussionsFromACategory: 'Discussions from a category…'
});


dojo.evalObjPath('xg.groups.nls', true);
dojo.lang.mixin(xg.groups.nls, xg.index.i18n, {
    pleaseChooseAName: 'Please choose a name for your group.',
    pleaseChooseAUrl: 'Please choose a web address for your group.',
    urlCanContainOnlyLetters: 'The web address can contain only letters and numbers (no spaces).',
    descriptionTooLong: function(n, maximum) { return 'The length of your group\'s description (' + n + ') exceeds the maximum (' + maximum + ')'; },
    nameTaken: 'Our apologies - that name has already been taken. Please choose another name.',
    urlTaken: 'Our apologies - that web address has already been taken. Please choose another web address.',
    edit: 'Edit',
    from: 'From',
    show: 'Show',
    groups: 'groups',
    pleaseEnterName: 'Please enter your name',
    pleaseEnterEmailAddress: 'Please enter your email address',
    xIsNotValidEmailAddress: function(x) { return x + ' is not a valid email address'; },
    save: 'Save',
    cancel: 'Cancel'
});


dojo.evalObjPath('xg.html.nls', true);
dojo.lang.mixin(xg.html.nls, xg.index.i18n, {
    contentsTooLong: function(maximum) { return 'The contents are too long. Please use less than ' + maximum + ' characters.'; },
    edit: 'Edit',
    save: 'Save',
    cancel: 'Cancel',
    saving: 'Saving…',
    addAWidget: function(url) { return '<a href="' + url + '">Add a widget</a> to this textbox'; }
});


dojo.evalObjPath('xg.index.nls', true);
dojo.lang.mixin(xg.index.nls, xg.index.i18n, {
    yes: 'Yes',
    done: 'Done',
    yourMessageIsBeingSent: 'Your message is being sent.',
    youNeedToAddEmailRecipient: 'You need to add an email recipient.',
    checkPageOut: function (network) {return 'Check out this page on '+network},
    checkingOutTitle: function (title, network) {return 'Check out "'+title+'" on '+network},
    selectOrPaste: 'You need to select a video or paste the \'embed\' code',
    selectOrPasteMusic: 'You need to select a song or paste the URL',
    cannotKeepFiles: 'You will have to choose your files again if you wish to view more options. Would you like to continue?',
    pleaseSelectPhotoToUpload: 'Please select a photo to upload.',
    addingLabel: 'Adding...',
    sendingLabel: 'Sending...',
    addingInstructions: 'Please leave this window open while your content is being added.',
    looksLikeNotImage: 'One or more files do not seem to be in .jpg, .gif, or .png format. Would you like to try uploading anyway?',
    looksLikeNotVideo: 'The file you selected does not seem to be in .mov, .mpg, .mp4, .avi, .3gp or .wmv format. Would you like to try uploading anyway?',
    looksLikeNotMusic: 'The file you selected does not seem to be in .mp3 format. Would you like to try uploading anyway?',
    showingNFriends: function(n, searchString) {
        switch(n) {
            case 1: return 'Showing 1 friend matching "' + searchString + '". <a href="#">Show everyone</a>';
            default: return 'Showing ' + n + ' friends matching "' + searchString + '". <a href="#">Show everyone</a>';
        }
    },
    sendInvitation: 'Send Invitation',
    sendMessage: 'Send Message',
    sendInvitationToNFriends: function(n) {
        switch(n) {
            case 1: return 'Send invitation to 1 friend?';
            default: return 'Send invitation to ' + n + ' friends?';
        }
    },
    sendMessageToNFriends: function(n) {
        switch(n) {
            case 1: return 'Send message to 1 friend?';
            default: return 'Send message to ' + n + ' friends?';
        }
    },
    invitingNFriends: function(n) {
        switch(n) {
            case 1: return 'Inviting 1 friend…';
            default: return 'Inviting ' + n + ' friends…';
        }
    },
    nFriendsSelected: function(n) {
        switch(n) {
            case 1: return '1 friend selected';
            default: return n + ' friends selected';
        }
    },
    sendingMessageToNFriends: function(n) {
        switch(n) {
            case 1: return 'Sending message to 1 friend…';
            default: return 'Sending message to ' + n + ' friends…';
        }
    },
    yourMessageOptional: '<label>Your Message</label> (Optional)',
    subjectIsTooLong: function(n) { return 'Your subject is too long. Please use '+n+' characters or less.'; },
    messageIsTooLong: function(n) { return 'Your message is too long. Please use '+n+' characters or less.'; },
    pleaseChoosePeople: 'Please choose some people to invite.',
    noPeopleSelected: 'No People Selected',
    pleaseEnterEmailAddress: 'Please enter your email address.',
    pleaseEnterPassword: function(emailAddress) { return 'Please enter your password for ' + emailAddress + '.'; },
    sorryWeDoNotSupport: 'Sorry, we don\'t support the web address book for your email address. Try clicking \'Address Book Application\' below to use addresses from your computer.',
    pleaseSelectSecondPart: 'Please select the second part of your email address, e.g., gmail.com.',
    atSymbolNotAllowed: 'Please ensure that the @ symbol is not in the first part of the email address.',
    resetTextQ: 'Reset Text?',
    resetTextToOriginalVersion: 'Are you sure you wish to reset all of your text to the original version? All of your changes will be lost.',
    changeQuestionsToPublic: 'Change questions to public?',
    changingPrivateQuestionsToPublic: 'Changing private questions to public will expose all members\' answers. Are you sure?',
    youHaveUnsavedChanges: 'You have unsaved changes.',
    pleaseEnterASiteName: 'Please enter a name for the social network, e.g. Tiny Clown Club',
    pleaseEnterShorterSiteName: 'Please enter a shorter name (max 64 characters)',
    pleaseEnterShorterSiteDescription: 'Please enter a shorter description (max 140 characters)',
    siteNameHasInvalidCharacters: 'The name has some invalid characters',
    thereIsAProblem: 'There is a problem with your information',
    thisSiteIsOnline: 'This social network is Online',
    online: '<strong>Online</strong>',
    onlineSiteCanBeViewed: '<strong>Online</strong> - Network can be viewed with respect to your privacy settings. ',
    takeOffline: 'Take Offline',
    thisSiteIsOffline: 'This social network is Offline',
    offline: '<strong>Offline</strong>',
    offlineOnlyYouCanView: '<strong>Offline</strong> - Only you can view this social network.',
    takeOnline: 'Take Online',
    themeSettings: 'Theme Settings',
    addYourOwnCss: 'Advanced',
    error: 'Error',
    pleaseEnterTheChoicesFor: function(questionTitle) { return 'Please enter the choices for "' + questionTitle + '" e.g. Hiking, Reading, Shopping'; },
    pleaseEnterTheChoices: 'Please enter the choices e.g. Hiking, Reading, Shopping',
    email: 'email',
    subject: 'Subject',
    message: 'Message',
    send: 'Send',
    cancel: 'Cancel',
    areYouSureYouWant: 'Are you sure you want to do this?',
    processing: 'Processing…',
    pleaseKeepWindowOpen: 'Please keep this browser window open while processing continues. It may take a few minutes.',
    complete: 'Complete!',
    processIsComplete: 'Process is complete.',
    processingFailed: 'Sorry, processing failed. Please try again later.',
    ok: 'OK',
    body: 'Body',
    pleaseEnterASubject: 'Please enter a subject',
    pleaseEnterAMessage: 'Please enter a message',
    pleaseChooseFriends: 'Please select some friends before sending your message.',
    thereHasBeenAnError: 'There has been an error',
    thereWasAProblem: 'There was a problem adding your content. Please try again later.',
    fileNotFound: 'File not found',
    pleaseProvideADescription: 'Please provide a description',
    pleaseEnterSomeFeedback: 'Please enter some feedback',
    title: 'Title:',
    customized: 'Customized',
    copyHtmlCode: 'Copy HTML Code',
    playerSize: 'Player Size',
    selectSource: 'Select Source',
    myAlbums: 'My Albums',
    myMusic: 'My Music',
    myVideos: 'My Videos',
    showPlaylist: 'Show Playlist',
    change: 'Change',
    changing: 'Changing...',
    changeSettings: 'Change Settings?',
    keepWindowOpenWhileChanging: 'Please keep this browser window open while privacy settings are being changed. This process may take a few minutes.',
    htmlNotAllowed: 'HTML not allowed',
    noFriendsFound: 'No friends found that match your search.',
    yourSubject: 'Your Subject',
    yourMessage: 'Your Message',
    pleaseEnterFbApiKey: 'Please enter your Facebook API key.',
    pleaseEnterValidFbApiKey: 'Please enter a valid Facebook API key.',
    pleaseEnterFbApiSecret: 'Please enter your Facebook API secret.',
    pleaseEnterValidFbApiSecret: 'Please enter a valid Facebook API secret.',
    pleaseEnterFbTabName: 'Please enter a name for your Facebook application tab.',
    pleaseEnterValidFbTabName: function(maxChars) {
                                   return 'Please enter a shorter name for your Facebook application tab.  The maximum length is ' + maxChars + ' character' + (maxChars == 1 ? '' : 's') + '.';
    },
    newTab: 'New Tab',
    resetToDefaults: 'Reset to Defaults',
    youNaviWillbeRestored: 'Your navigation tabs will be restored to the network\'s original navigation.',
    hiddenWarningTop: function(n) { return 'This tab has not been added to your network. There is a limit of '+n+' top-level tabs. '+
        'Please remove top-level tabs or make top-level tabs to sub-tabs.' },
    hiddenWarningSub: function(n) { return 'This sub-tab has not been added to your network. There is a limit of '+n+' sub-tabs per top-level tab. '+
        'Please remove sub-tabs or make sub-tabs into top-level tabs.' },
    removeConfirm: 'By removing this top-level tab, it will remove its sub-tabs as well. Click OK to continue.',
    no: 'No',
    youMustSpecifyTabName: 'You must specify a tab name'
});


dojo.evalObjPath('xg.music.nls', true);
dojo.lang.mixin(xg.music.nls, xg.index.i18n, {
    play: 'play',
    pleaseSelectTrackToUpload: 'Please select a song to upload.',
    pleaseEnterTrackLink: 'Please enter a song URL.',
    thereAreUnsavedChanges: 'There are unsaved changes.',
    autoplay: 'Autoplay',
    showPlaylist: 'Show Playlist',
    playLabel: 'Play',
    url: 'URL',
    rssXspfOrM3u: 'rss, xspf, or m3u',
    save: 'Save',
    cancel: 'Cancel',
    edit: 'Edit',
    shufflePlaylist: 'Shuffle Playlist',
    fileIsNotAnMp3: 'One of the files does not seem to be an MP3. Try uploading it anyway?',
    entryNotAUrl: 'One of the entries does not appear to be a URL. Make sure all entries start with <kbd>http://</kbd>'
});


dojo.evalObjPath('xg.page.nls', true);
dojo.lang.mixin(xg.page.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'The number of characters (' + n + ') exceeds the maximum (' + maximum + ')'; },
    pleaseEnterContent: 'Please enter the page content',
    pleaseEnterTitle: 'Please enter a title for the page',
    pleaseEnterAComment: 'Please enter a comment',
    save: 'Save',
    cancel: 'Cancel',
    edit: 'Edit',
    close: 'Close',
    displayPagePosts: 'Display Page Posts',
    directory: 'Directory',
    displayTab: 'Display tab',
    addAnotherPage: 'Add Another Page',
    tabText: 'Tab text',
    urlDirectory: 'URL directory',
    displayTabForPage: 'Whether to display a tab for the page',
    tabTitle: 'Tab Title',
    remove: 'Remove',
    thereIsAProblem: 'There is a problem with your information'
});


dojo.evalObjPath('xg.photo.nls', true);
dojo.lang.mixin(xg.photo.nls, xg.index.i18n, {
    randomOrder: 'Random Order',
    untitled: 'Untitled',
    photos: 'Photos',
    edit: 'Edit',
    photosFromAnAlbum: 'Albums',
    show: 'Show',
    rows: 'rows',
    cancel: 'Cancel',
    save: 'Save',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'The number of characters (' + n + ') exceeds the maximum (' + maximum + ')'; },
    pleaseSelectPhotoToUpload: 'Please select a photo to upload.',
    importingNofMPhotos: function(n,m) { return 'Importing <span id="currentP">' + n + '</span> of ' + m + ' photos.'},
    starting: 'Starting…',
    done: 'Done!',
    from: 'From',
    display: 'Display',
    takingYou: 'Taking you to see your photos…',
    anErrorOccurred: 'Unfortunately an error occurred. Please report this issue using the link at the bottom of the page.',
    weCouldntFind: 'We couldn\'t find any photos! Why don\'t you try one of the other options?'
});

dojo.evalObjPath('xg.activity.nls', true);
dojo.lang.mixin(xg.activity.nls, xg.index.i18n, {
    edit: 'Edit',
    show: 'Show',
    events: 'events',
    setWhatActivityGetsDisplayed: 'Set what activity gets displayed',
    save: 'Save',
    cancel: 'Cancel'
});


dojo.evalObjPath('xg.profiles.nls', true);
dojo.lang.mixin(xg.profiles.nls, xg.index.i18n, {
    messageIsTooLong: function(n) { return 'Your message is too long. Please use '+n+' characters or less.'; },
    comments: 'comments',
    requestLimitExceeded: 'Friend Request Limit Exceeded',
    removeFriendTitle: function(username) {return 'Remove ' + username + ' As Friend?'; },
    removeFriendConfirm: function(username) {return 'Are you sure you want to remove ' + username + ' as a friend?'},
    pleaseEnterValueForPost: 'Please enter a value for the post',
    edit: 'Edit',
    recentlyAdded: 'Recently Added',
    featured: 'Featured',
    iHaveRecentlyAdded: 'I\'ve Recently Added',
    fromTheSite: 'From the Social Network',
    cancel: 'Cancel',
    save: 'Save',
    loading: 'Loading…',
    pleaseEnterPostBody: 'Please enter something for the post body',
    pleaseEnterChatter: 'Please enter something for your comment',
    letMeApproveChatters: 'Let me approve comments before posting?',
    noPostChattersImmediately: 'No – post comments immediately',
    yesApproveChattersFirst: 'Yes – approve comments first',
    memberHasChosenToModerate: function(name) {return name + ' has chosen to moderate comments.'},
    reallyDeleteThisPost: 'Really delete this post?',
    commentWall: 'Comment Wall',
    commentWallNComments: function(n) {
        switch(n) {
            case 1: return 'Comment Wall (1 comment)';
            default: return 'Comment Wall (' + n + ' comments)';
        }
    },
    /*Embed Options*/
    display: 'Display',
    from: 'From',
    show: 'Show',
    rows: 'rows',
    posts: 'posts',
    returnToDefaultWarning: 'This will move all features and the theme on My Page back to the network\'s default. Would you like to proceed?',
    networkError: 'Network Error',
    wereSorry: 'We\'re sorry, but we are unable to save your new layout at this time. This is likely due to a lost Internet connection. Please check your connection and try again.',
    //TODO: add terms of use link location
    unableToCompleteAction: 'Sorry, we were unable to complete your last action. Please try again later.',
    selectAtLeastOneMessage: 'Sorry, you have to select at least one message to perform that action.',
    selectedSendersBlocked: function(n) {
        switch(n) {
            case 1: return 'The selected sender has been blocked.';
            default: return 'The selected senders have been blocked.';
        }
    },
    bulkConfirm_blockSender: 'This will block the senders of the checked messages.',
    bulkConfirm_delete: 'This will delete the checked messages.',
    messageSent: 'Message Sent',
    yourMessageHasBeenSent: 'Your message has been sent!',
    supportsTextOnly: 'Supports text only.'
});


dojo.evalObjPath('xg.shared.nls', true);
dojo.lang.mixin(xg.shared.nls, xg.index.i18n, {
    friendLimitExceeded: 'Friend Limit Exceeded',
    requestLimitExceeded: 'Friend Request Limit Exceeded',
    addNameAsFriend: function(name) { return 'Add ' + name + ' as a friend?'; },
    nameMustConfirmFriendship: function(name) { return name + ' will have to confirm your friendship.'; },
    addPersonalMessage: 'Add a personal message',
    typePersonalMessage: 'Type your personal message…',
    thereHasBeenAnError: 'There has been an error',
    message: 'Message',
    send: 'Send',
    friendRequestSent: 'Friend Request Sent!',
    yourFriendRequestHasBeenSent: 'Your friend request has been sent.',
    yourMessage: 'Your Message',
    updateMessage: 'Update Message',
    updateMessageQ: 'Update Message?',
    removeWords: 'To make sure your email is delivered successfully, we recommend going back to change or remove the following words:',
    warningMessage: 'It looks like there are some words in this email that might send your email to a Spam folder.',
    errorMessage: 'There are 6 or more words in this email that might send your email to a Spam folder.',
    goBack: 'Go Back',
    sendAnyway: 'Send Anyway',
    messageIsTooLong: function(n,m) { return 'We\'re sorry. The maximum number of characters is '+m+'.' },
    yourMessageIsTooLong: function(n) { return 'Your message is too long. Please use '+n+' characters or less.'; },
    locationNotFound: function(location) { return '<em>' + location + '</em> not found.'; },
    confirmation: 'Confirmation',
    showMap: 'Show Map',
    hideMap: 'Hide Map',
    yourCommentMustBeApproved: 'Your comment must be approved before everyone can see it.',
    nComments: function(n) {
        switch(n) {
            case 1: return '1 Comment';
            default: return n + ' Comments';
        }
    },
    pleaseEnterAComment: 'Please enter a comment',
    uploadAPhoto: 'Upload a Photo',
    uploadAnImage: 'Upload an image',
    uploadAPhotoEllipsis: 'Upload a Photo…',
    useExistingImage: 'Use existing image:',
    existingImage: 'Existing Image',
    useThemeImage: 'Use theme image:',
    themeImage: 'Theme Image',
    noImage: 'No image',
    uploadImageFromComputer: 'Upload an image from your computer',
    tileThisImage: 'Tile this image',
    done: 'Done',
    currentImage: 'Current image',
    pickAColor: 'Pick a Color…',
    openColorPicker: 'Open Color Picker',
    transparent: 'Transparent',
    loading: 'Loading…',
    ok: 'OK',
    save: 'Save',
    cancel: 'Cancel',
    saving: 'Saving…',
    addAnImage: 'Add an Image',
    uploadAFile: 'Upload a File',
    pleaseEnterAWebsite: 'Please enter a website address',
    pleaseEnterAFileAddress: 'Please enter the address of the file',
    bold: 'Bold',
    italic: 'Italic',
    underline: 'Underline',
    strikethrough: 'Strikethrough',
    addHyperink: 'Add Hyperlink',
    options: 'Options',
    wrapTextAroundImage: 'Wrap text around image?',
    imageOnLeft: 'Image on left?',
    imageOnRight: 'Image on right?',
    createThumbnail: 'Create thumbnail?',
    pixels: 'pixels',
    createSmallerVersion: 'Create a smaller version of your image to display. Set the width in pixels.',
    popupWindow: 'Popup Window?',
    linkToFullSize: 'Link to the full-size version of the image in a popup window.',
    add: 'Add',
    keepWindowOpen: 'Please keep this browser window open while upload continues.',
    cancelUpload: 'Cancel Upload',
    pleaseSelectAFile: 'Please select an Image File',
    pleaseSpecifyAThumbnailSize: 'Please specify a thumbnail size',
    thumbnailSizeMustBeNumber: 'The thumbnail size must be a number',
    addExistingImage: 'or insert an existing image',
    addExistingFile: 'or insert an existing file',
    clickToEdit: 'Click to edit',
    requestSent: 'Request Sent!',
    pleaseCorrectErrors: 'Please correct these errors',
    noo: 'NEW',
    none: 'NONE',
    joinNow: 'Join Now',
    join: 'Join',
    addToFavorites: 'Add to Favorites',
    removeFromFavorites: 'Remove from Favorites',
    follow: 'Follow',
    stopFollowing: 'Stop Following',
    pendingPromptTitle: 'Membership Pending Approval',
    youCanDoThis: 'You can do this once your membership has been approved by the administrators.',
    editYourTags: 'Edit Your Tags',
    addTags: 'Add Tags', // this and the two below are passed by reference, so they fail the I18NTest
    editLocation: 'Edit Location',
    editTypes: 'Edit Event Type',
    charactersLeft: function(n) {
        if (n >= 0) {
            return '&nbsp;(' + n + ' left)';
        } else {
            return  '&nbsp;(' + Math.abs(n) + ' over)';
        }
    },
    commentWall: 'Comment Wall',
    commentWallNComments: function(n) {
        switch(n) {
            case 1: return 'Comment Wall (1 comment)';
            default: return 'Comment Wall (' + n + ' comments)';
        }
    }
});


dojo.evalObjPath('xg.video.nls', true);
dojo.lang.mixin(xg.video.nls, xg.index.i18n, {
    edit: 'Edit',
    display: 'Display',
    detail: 'Detail',
    player: 'Player',
    from: 'From',
    show: 'Show',
    videos: 'videos',
    cancel: 'Cancel',
    save: 'Save',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'The number of characters (' + n + ') exceeds the maximum (' + maximum + ')'; },
    approve: 'Approve',
    approving: 'Approving…',
    keepWindowOpenWhileApproving: 'Please keep this browser window open while videos are being approved. This process may take a few minutes.',
    'delete': 'Delete',
    deleting: 'Deleting…',
    keepWindowOpenWhileDeleting: 'Please keep this browser window open while videos are being deleted. This process may take a few minutes.',
    pasteInEmbedCode: 'Please paste in the embed code for a video from another site.',
    pleaseSelectVideoToUpload: 'Please select a video to upload.',
    embedCodeContainsMoreThanOneVideo: 'The embed code contains more than one video. Please make sure it has only one <object> and/or <embed> tag.',
    embedCodeMissingTag: 'The embed code is missing an &lt;embed&gt; or &lt;object&gt; tag.',
    fileIsNotAMov: 'This file does not seem to be a .mov, .mpg, .mp4, .avi, .3gp or .wmv. Try uploading it anyway?',
    embedHTMLCode: 'HTML Embed Code:',
    directLink: 'Direct Link',
    addToMyspace: 'Add to MySpace',
    shareOnFacebook: 'Share on Facebook',
    addToOthers: 'Add to Others'
});

dojo.evalObjPath('xg.uploader.nls', true);
dojo.lang.mixin(xg.uploader.nls, xg.index.i18n, {
    fileBrowserHeader: 'My Computer',
    fileRoot: 'My Computer',
    fileInformationHeader: 'Information',
    uploadHeader: 'Files to Upload',
    dragOutInstructions: 'Drag files out to remove them',
    dragInInstructions: 'Drag Files Here',
    selectInstructions: 'Select a File',
    files: 'Files',
    totalSize: 'Total Size',
    fileName: 'Name',
    fileSize: 'Size',
    nextButton: 'Next >',
    okayButton: 'OK',
    yesButton: 'Yes',
    noButton: 'No',
    uploadButton: 'Upload',
    cancelButton: 'Cancel',
    backButton: 'Back',
    continueButton: 'Continue',
    uploadingStatus: function(n, m) { return 'Uploading ' + n + ' of ' + m; },
    uploadLimitWarning: function(n) { return 'You can upload ' + n + ' files at a time.'; },
    uploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'You\'ve added the maximum number of files.';
            case 1: return 'You can upload 1 more file.';
            default: return 'You can upload ' + n + ' more files.';
        }
    },
    uploadingLabel: 'Uploading...',
    uploadingInstructions: 'Please leave this window open while your upload is in progress',
    iHaveTheRight: 'I have the right to upload these files under the <a href="/main/authorization/termsOfService">Terms of Service</a>',
    updateJavaTitle: 'Update Java',
    updateJavaDescription: 'The bulk uploader requires a more recent version of Java. Click "Okay" to get Java.',
    batchEditorLabel: 'Edit Information for All Items',
    applyThisInfo: 'Apply this info to the files below',
    titleProperty: 'Title',
    descriptionProperty: 'Description',
    tagsProperty: 'Tags',
    viewableByProperty: 'Can be viewed by',
    viewableByEveryone: 'Anyone',
    viewableByFriends: 'Just My Friends',
    viewableByMe: 'Just Me',
    albumProperty: 'Album',
    artistProperty: 'Artist',
    enableDownloadLinkProperty: 'Enable download link',
    enableProfileUsageProperty: 'Allow people to put this song on their pages',
    licenseProperty: 'License',
    creativeCommonsVersion: '3.0',
    selectLicense: '— Select license —',
    copyright: '© All Rights Reserved',
    ccByX: function(n) { return 'Creative Commons Attribution ' + n; },
    ccBySaX: function(n) { return 'Creative Commons Attribution Share Alike ' + n; },
    ccByNdX: function(n) { return 'Creative Commons Attribution No Derivatives ' + n; },
    ccByNcX: function(n) { return 'Creative Commons Attribution Non-commercial ' + n; },
    ccByNcSaX: function(n) { return 'Creative Commons Attribution Non-commercial Share Alike ' + n; },
    ccByNcNdX: function(n) { return 'Creative Commons Attribution Non-commercial No Derivatives ' + n; },
    publicDomain: 'Public Domain',
    other: 'Other',
    errorUnexpectedTitle: 'Oops!',
    errorUnexpectedDescription: 'There\'s been an error. Please try again.',
    errorTooManyTitle: 'Too Many Items',
    errorTooManyDescription: function(n) { return 'We\'re sorry, but you can only upload ' + n + ' items at a time.'; },
    errorNotAMemberTitle: 'Not Allowed',
    errorNotAMemberDescription: 'We\'re sorry, but you need to be a member to upload.',
    errorContentTypeNotAllowedTitle: 'Not Allowed',
    errorContentTypeNotAllowedDescription: 'We\'re sorry, but you\'re not allowed to upload this type of content.',
    errorUnsupportedFormatTitle: 'Oops!',
    errorUnsupportedFormatDescription: 'We\'re sorry, but we don\'t support this type of file.',
    errorUnsupportedFileTitle: 'Oops!',
    errorUnsupportedFileDescription: 'foo.exe is in an unsupported format.',
    errorUploadUnexpectedTitle: 'Oops!',
    errorUploadUnexpectedDescription: function(file) {
        return file ?
            ('There appears to be a problem with the ' + file + ' file. Please remove it from the list before uploading the rest of your files.') :
            'There appears to be a problem with the file at the top of the list. Please remove it before uploading the rest of your files.';
    },
    cancelUploadTitle: 'Cancel Upload?',
    cancelUploadDescription: 'Are you sure you want to cancel the remaining uploads?',
    uploadSuccessfulTitle: 'Upload Completed',
    uploadSuccessfulDescription: 'Please wait while we take you to your uploads...',
    uploadPendingDescription: 'Your files were successfully uploaded and are awaiting approval.',
    photosUploadHeader: 'Photos to Upload',
    photosDragOutInstructions: 'Drag photos out to remove them',
    photosDragInInstructions: 'Drag Photos Here',
    photosSelectInstructions: 'Select a Photo',
    photosFiles: 'Photos',
    photosUploadingStatus: function(n, m) { return 'Uploading Photo ' + n + ' of ' + m; },
    photosErrorTooManyTitle: 'Too Many Photos',
    photosErrorTooManyDescription: function(n) { return 'We\'re sorry, but you can only upload ' + n + ' photos at a time.'; },
    photosErrorContentTypeNotAllowedDescription: 'We\'re sorry, but photo uploading has been disabled.',
    photosErrorUnsupportedFormatDescription: 'We\'re sorry, but you can only upload .jpg, .gif or .png format images.',
    photosErrorUnsupportedFileDescription: function(n) { return n + ' is not a .jpg, .gif or .png file.'; },
    photosBatchEditorLabel: 'Edit Information for All Photos',
    photosApplyThisInfo: 'Apply this info to the photos below',
    photosErrorUploadUnexpectedDescription: function(file) {
        return file ?
            ('There appears to be a problem with the ' + file + ' file. Please remove it from the list before uploading the rest of your photos.') :
            'There appears to be a problem with the photo at the top of the list. Please remove it before uploading the rest of your photos.';
    },
    photosUploadSuccessfulDescription: 'Please wait while we take you to your photos...',
    photosUploadPendingDescription: 'Your photos were successfully uploaded and are awaiting approval.',
    photosUploadLimitWarning: function(n) { return 'You can upload ' + n + ' photos at a time.'; },
    photosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'You\'ve added the maximum number of photos.';
            case 1: return 'You can upload 1 more photo.';
            default: return 'You can upload ' + n + ' more photos.';
        }
    },
    photosIHaveTheRight: 'I have the right to upload these photos under the <a href="/main/authorization/termsOfService">Terms of Service</a>',
    videosUploadHeader: 'Videos to Upload',
    videosDragInInstructions: 'Drag Videos Here',
    videosDragOutInstructions: 'Drag videos out to remove them',
    videosSelectInstructions: 'Select a Video',
    videosFiles: 'Videos',
    videosUploadingStatus: function(n, m) { return 'Uploading Video ' + n + ' of ' + m; },
    videosErrorTooManyTitle: 'Too Many Videos',
    videosErrorTooManyDescription: function(n) { return 'We\'re sorry, but you can only upload ' + n + ' videos at a time.'; },
    videosErrorContentTypeNotAllowedDescription: 'We\'re sorry, but video uploading has been disabled.',
    videosErrorUnsupportedFormatDescription: 'We\'re sorry, but you can only upload .avi, .mov, .mp4, .wmv or .mpg format videos.',
    videosErrorUnsupportedFileDescription: function(x) { return x + ' is not a .avi, .mov, .mp4, .wmv or .mpg file.'; },
    videosBatchEditorLabel: 'Edit Information for All Videos',
    videosApplyThisInfo: 'Apply this info to the videos below',
    videosErrorUploadUnexpectedDescription:  function(file) {
        return file ?
            ('There appears to be a problem with the ' + file + ' file. Please remove it from the list before uploading the rest of your videos.') :
            'There appears to be a problem with the video at the top of the list. Please remove it before uploading the rest of your videos.';
    },
    videosUploadSuccessfulDescription: 'Please wait while we take you to your videos...',
    videosUploadPendingDescription: 'Your videos were successfully uploaded and are awaiting approval.',
    videosUploadLimitWarning: function(n) { return 'You can upload ' + n + ' videos at a time.'; },
    videosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'You\'ve added the maximum number of videos.';
            case 1: return 'You can upload 1 more video.';
            default: return 'You can upload ' + n + ' more videos.';
        }
    },
    videosIHaveTheRight: 'I have the right to upload these videos under the <a href="/main/authorization/termsOfService">Terms of Service</a>',
    musicUploadHeader: 'Songs to Upload',
    musicTitleProperty: 'Song Title',
    musicDragOutInstructions: 'Drag songs out to remove them',
    musicDragInInstructions: 'Drag Songs Here',
    musicSelectInstructions: 'Select a Song',
    musicFiles: 'Songs',
    musicUploadingStatus: function(n, m) { return 'Uploading Song ' + n + ' of ' + m; },
    musicErrorTooManyTitle: 'Too Many Songs',
    musicErrorTooManyDescription: function(n) { return 'We\'re sorry, but you can only upload ' + n + ' songs at a time.'; },
    musicErrorContentTypeNotAllowedDescription: 'We\'re sorry, but song uploading has been disabled.',
    musicErrorUnsupportedFormatDescription: 'We\'re sorry, but you can only upload .mp3 format songs.',
    musicErrorUnsupportedFileDescription: function(x) { return x + ' is not a .mp3 file.'; },
    musicBatchEditorLabel: 'Edit Information for All Songs',
    musicApplyThisInfo: 'Apply this info to the songs below',
    musicErrorUploadUnexpectedDescription:  function(file) {
        return file ?
            ('There appears to be a problem with the ' + file + ' file. Please remove it from the list before uploading the rest of your songs.') :
            'There appears to be a problem with the song at the top of the list. Please remove it before uploading the rest of your songs.';
    },
    musicUploadSuccessfulDescription: 'Please wait while we take you to your songs...',
    musicUploadPendingDescription: 'Your songs were successfully uploaded and are awaiting approval.',
    musicUploadLimitWarning: function(n) { return 'You can upload ' + n + ' songs at a time.'; },
    musicUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'You\'ve added the maximum number of songs.';
            case 1: return 'You can upload 1 more song.';
            default: return 'You can upload ' + n + ' more songs.';
        }
    },
    musicIHaveTheRight: 'I have the right to upload these songs under the <a href="/main/authorization/termsOfService">Terms of Service</a>'
});

dojo.evalObjPath('xg.events.nls', true);
dojo.lang.mixin(xg.events.nls, xg.index.i18n, {
    pleaseEnterTitle: 'Please enter a title for the event',
    pleaseEnterDescription: 'Please enter a description for the event',
    messageIsTooLong: function(n) { return 'Your message is too long. Please use '+n+' characters or less.'; },
    pleaseEnterLocation: 'Please enter a location for the event',
    pleaseChooseImage:'Please choose an image for the event',
    pleaseEnterType:'Please enter at least one type for the event',
    sendMessageToGuests: 'Send Message to Guests',
    sendMessageToGuestsThat: 'Send message to guests that:',
    areAttending: 'Are attending',
    mightAttend: 'Might attend',
    haveNotYetRsvped: 'Have not yet RSVPed',
    areNotAttending: 'Are not attending',
    yourMessage: 'Your Message',
    send: 'Send',
    sending: 'Sending…',
    yourMessageIsBeingSent: 'Your message is being sent.',
    messageSent: 'Message Sent!',
    yourMessageHasBeenSent: 'Your message has been sent.',
    chooseRecipient: 'Please choose a recipient.',
    pleaseEnterAMessage: 'Please enter a message',
    thereHasBeenAnError: 'There has been an error'
});

dojo.evalObjPath('xg.notes.nls', true);
dojo.lang.mixin(xg.notes.nls, xg.index.i18n, {
    addNewNote: 'Add New Note',
    pleaseEnterNoteTitle: 'Please enter a note title!',
    noteTitleTooLong: 'Note title is too long',
    pleaseEnterNoteEntry: 'Please enter a note entry'
});
