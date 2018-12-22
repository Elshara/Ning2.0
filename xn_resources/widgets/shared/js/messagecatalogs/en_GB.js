dojo.provide('xg.shared.messagecatalogs.en_GB');

dojo.require('xg.index.i18n');

/**
 * Texts for the British English
 */
// Use UTF-8 byte sequences instead of HTML entities, e.g., & instead of &amp;, â€¦ instead of &hellip;  [Jon Aquino 2007-01-10]


dojo.evalObjPath('xg.events.nls', true);
dojo.lang.mixin(xg.events.nls, xg.index.i18n, {
    pleaseChooseImage: 'Please choose an image for the event',
    pleaseEnterAMessage: 'Please enter a message',
    pleaseEnterDescription: 'Please enter a description for the event',
    pleaseEnterLocation: 'Please enter a location for the event',
    pleaseEnterTitle: 'Please enter a title for the event',
    pleaseEnterType: 'Please enter at least one type for the event',
    send: 'Send',
    sending: 'Sending…',
    thereHasBeenAnError: 'There has been an error',
    yourMessage: 'Your Message',
    yourMessageHasBeenSent: 'Your message has been sent.',
    yourMessageIsBeingSent: 'Your message is being sent.'
});

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


dojo.evalObjPath('xg.gadgets.nls', true);
dojo.lang.mixin(xg.gadgets.nls, xg.index.i18n, {
    edit: 'Edit',
    title: 'Title:',
    feedUrl: 'URL:',
    cancel: 'Cancel',
    save: 'Save',
    loading: 'Loading…',
    removeGadget: 'Remove Gadget',
    findGadgetsInDirectory: 'Find Gadgets in the Gadget Directory'
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
    uploadingLabel: 'Uploading...',
    uploadingStatus: function(n, m) { return 'Uploading ' + n + ' of ' + m; },
    uploadingInstructions: 'Please leave this window open while your upload is in progress',
    uploadLimitWarning: function(n) { return 'You can upload ' + n + ' files at a time. '; },
    uploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'You\'ve added the maximum number of files. ';
            case 1: return 'You can upload 1 more file. ';
            default: return 'You can upload ' + n + ' more files. ';
        }
    },
    iHaveTheRight: 'I have the right to upload these files under the <a href="/main/authorization/termsOfService">Terms of Service</a>',
    updateJavaTitle: 'Update Java',
    updateJavaDescription: 'The bulk uploader requires a more recent version of Java. Click "OK" to get Java.',
    batchEditorLabel: 'Edit Information for All Items',
    applyThisInfo: 'Apply this info to the files below',
    titleProperty: 'Title',
    descriptionProperty: 'Description',
    tagsProperty: 'Tags',
    viewableByProperty: 'Can be viewed by',
    viewableByEveryone: 'Everyone',
    viewableByFriends: 'Just My Friends',
    viewableByMe: 'Just Me',
    albumProperty: 'Album',
    artistProperty: 'Artist',
    enableDownloadLinkProperty: 'Enable download link',
    enableProfileUsageProperty: 'Allow people to put this song on their pages',
    licenseProperty: 'Licence',
    creativeCommonsVersion: '3.0',
    selectLicense: '- Select licence -',
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
    errorTooManyDescription: function(n) { return 'We\'re sorry, but you can only upload ' + n + ' items at a time. '; },
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
    photosErrorTooManyDescription: function(n) { return 'We\'re sorry, but you can only upload ' + n + ' photos at a time. '; },
    photosErrorContentTypeNotAllowedDescription: 'We\'re sorry, but photo uploading has been disabled.',
    photosErrorUnsupportedFormatDescription: 'We\'re sorry, but you can only upload .jpg, .gif or .png format images.',
    photosErrorUnsupportedFileDescription: function(n) { return n + ' is not a .jpg, .gif or .png file.'; },
    photosBatchEditorLabel: 'Edit Information for All Photos',
    photosApplyThisInfo: 'Apply this info to the below photos',
    photosErrorUploadUnexpectedDescription: function(file) {
        return file ?
            ('There appears to be a problem with the ' + file + ' file. Please remove it from the list before uploading the rest of your photos.') :
            'There appears to be a problem with the photo at the top of the list. Please remove it before uploading the rest of your photos.';
    },
    photosUploadSuccessfulDescription: 'Please wait while we take you to your photos...',
    photosUploadPendingDescription: 'Your photos were successfully uploaded and are awaiting approval.',
    photosUploadLimitWarning: function(n) { return 'You can upload ' + n + ' photos at a time. '; },
    photosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'You\'ve added the maximum number of photos. ';
            case 1: return 'You can upload 1 more photo. ';
            default: return 'You can upload ' + n + ' more photos. ';
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
    videosErrorTooManyDescription: function(n) { return 'We\'re sorry, but you can only upload ' + n + ' videos at a time. '; },
    videosErrorContentTypeNotAllowedDescription: 'We\'re sorry, but video uploading has been disabled.',
    videosErrorUnsupportedFormatDescription: 'We\'re sorry, but you can only upload .avi, .mov, .mp4, .wmv or .mpg format videos.',
    videosErrorUnsupportedFileDescription: function(x) { return x + ' is not a .avi, .mov, .mp4, .wmv or .mpg file.'; },
    videosBatchEditorLabel: 'Edit Information for All Videos',
    videosApplyThisInfo: 'Apply this info to the videos below',
    videosErrorUploadUnexpectedDescription: function(file) {
        return file ?
            ('There appears to be a problem with the ' + file + ' file. Please remove it from the list before uploading the rest of your videos.') :
            'There appears to be a problem with the video at the top of the list. Please remove it before uploading the rest of your videos.';
    },
    videosUploadSuccessfulDescription: 'Please wait while we take you to your videos...',
    videosUploadPendingDescription: 'Your videos were successfully uploaded and are awaiting approval.',
    videosUploadLimitWarning: function(n) { return 'You can upload ' + n + ' videos at a time. '; },
    videosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'You\'ve added the maximum number of videos. ';
            case 1: return 'You can upload 1 more video. ';
            default: return 'You can upload ' + n + ' more videos. ';
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
    musicErrorTooManyDescription: function(n) { return 'We\'re sorry, but you can only upload ' + n + ' songs at a time. '; },
    musicErrorContentTypeNotAllowedDescription: 'We\'re sorry, but song uploading has been disabled.',
    musicErrorUnsupportedFormatDescription: 'We\'re sorry, but you can only upload .mp3 format songs.',
    musicErrorUnsupportedFileDescription: function(x) { return x + ' is not a .mp3 file.'; },
    musicBatchEditorLabel: 'Edit Information for All Songs',
    musicApplyThisInfo: 'Apply this info to the songs below',
    musicErrorUploadUnexpectedDescription: function(file) {
        return file ?
            ('There appears to be a problem with the ' + file + ' file. Please remove it from the list before uploading the rest of your songs.') :
            'There appears to be a problem with the song at the top of the list. Please remove it before uploading the rest of your songs.';
    },
    musicUploadSuccessfulDescription: 'Please wait while we take you to your songs...',
    musicUploadPendingDescription: 'Your songs were successfully uploaded and are awaiting approval.',
    musicUploadLimitWarning: function(n) { return 'You can upload ' + n + ' songs at a time. '; },
    musicUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'You\'ve added the maximum number of songs. ';
            case 1: return 'You can upload 1 more song. ';
            default: return 'You can upload ' + n + ' more songs. ';
        }
    },
    musicIHaveTheRight: 'I have the right to upload these songs under the <a href="/main/authorization/termsOfService">Terms of Service</a>'
});


dojo.evalObjPath('xg.forum.nls', true);
dojo.lang.mixin(xg.forum.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'The number of characters (' + n + ') exceeds the maximum (' + maximum + ') '; },
    pleaseEnterFirstPost: 'Please write the first post for the discussion',
    pleaseEnterTitle: 'Please enter a title for the discussion',
    save: 'Save',
    cancel: 'Cancel',
    yes: 'Yes',
    no: 'No',
    edit: 'Edit',
    deleteCategory: 'Delete Category',
    discussionsWillBeDeleted: 'The discussions in this category deleted.',
    whatDoWithDiscussions: 'What would you like to do with the discussions in this category?',
    moveDiscussionsTo: 'Move discussions to:',
    moveToCategory: 'Move to Category…',
    deleteDiscussions: 'Delete discussions',
    'delete': 'Delete',
    deleteReply: 'Delete Reply',
    deleteReplyQ: 'Delete this reply?',
    deletingReplies: 'Deleting Replies…',
    doYouWantToRemoveReplies: 'Do you also want to remove the replies to this comment?',
    pleaseKeepWindowOpen: 'Please keep this browser window open while processing continues. It may take a few minutes.',
    from: 'From',
    show: 'Show',
    discussions: 'discussions',
    discussionsFromACategory: 'Discussions from a category…',
    display: 'Display',
    items: 'Items',
    view: 'View'
});


dojo.evalObjPath('xg.groups.nls', true);
dojo.lang.mixin(xg.groups.nls, xg.index.i18n, {
    pleaseChooseAName: 'Please choose a name for your group.',
    pleaseChooseAUrl: 'Please choose a web address for your group.',
    urlCanContainOnlyLetters: 'The web address can contain only letters and numbers (no spaces).',
    descriptionTooLong: function(n, maximum) { return 'The length of your group\'s description (' + n + ') exceeds the maximum (' + maximum + ') '; },
    nameTaken: 'Sorry - that name has already been taken. Please choose another name.',
    urlTaken: 'Sorry - that web address has already been taken. Please choose another web address.',
    whyNot: 'Why not?',
    groupCreatorDetermines: function(href) { return 'The group creator determines who is allowed to join. If you feel that you may have been mistakenly blocked, please <a ' + href + '>contact the group creator</a> '; },
    edit: 'Edit',
    from: 'From',
    show: 'Show',
    groups: 'groups',
    pleaseEnterName: 'Please enter your name',
    pleaseEnterEmailAddress: 'Please enter your e-mail address',
    xIsNotValidEmailAddress: function(x) { return x + ' is not a valid email address'; },
    save: 'Save',
    cancel: 'Cancel'
});


dojo.evalObjPath('xg.html.nls', true);
dojo.lang.mixin(xg.html.nls, xg.index.i18n, {
    contentsTooLong: function(maximum) { return 'The contents are too long. Please use less than ' + maximum + ' characters. '; },
    edit: 'Edit',
    save: 'Save',
    cancel: 'Cancel',
    saving: 'Saving…',
    addAWidget: function(url) { return '<a href="' + url + '">Add a widget</a> to this textbox '; }
});


dojo.evalObjPath('xg.index.nls', true);
dojo.lang.mixin(xg.index.nls, xg.index.i18n, {
    sendInvitation: 'Send Invitation',
    sendInvitationToNFriends: function(n) {
        switch(n) {
            case 1: return 'Send invitation to 1 friend? ';
            default: return 'Send invitation to ' + n + ' friends? ';
        }
    },
    showingNFriends: function(n, searchString) {
        switch(n) {
            case 1: return 'Showing 1 friend matching "' + searchString + '". <a href="#">Show everyone</a> ';
            default: return 'Showing ' + n + ' friends matching "' + searchString + '". <a href="#">Show everyone</a> ';
        }
    },
    sendMessage: 'Send Message',
    sendMessageToNFriends: function(n) {
        switch(n) {
            case 1: return 'Send message to 1 friend? ';
            default: return 'Send message to ' + n + ' friends? ';
        }
    },
    invitingNFriends: function(n) {
        switch(n) {
            case 1: return 'Inviting 1 friend… ';
            default: return 'Inviting ' + n + ' friends… ';
        }
    },
    nFriends: function(n) {
        switch(n) {
            case 1: return '1 friend… ';
            default: return n + ' friends… ';
        }
    },
    sendingMessageToNFriends: function(n) {
        switch(n) {
            case 1: return 'Sending message to 1 friend… ';
            default: return 'Sending message to ' + n + ' friends… ';
        }
    },
    noPeopleSelected: 'No People Selected',
    sorryWeDoNotSupport: 'Sorry, we don\'t support the web address book for your e-mail address. Try clicking \'Address Book Application\' below to use addresses from your computer.',
    pleaseChooseFriends: 'Please select some friends before sending your message.',
    htmlNotAllowed: 'HTML not allowed',
    noFriendsFound: 'No friends found that match your search.',
    yourMessageOptional: '<label>Your Message</label> (Optional)',
    pleaseChoosePeople: 'Please choose some people to invite.',
    pleaseEnterEmailAddress: 'Please enter your e-mail address',
    pleaseEnterPassword: function(emailAddress) { return 'Please enter your password for ' + emailAddress + '. '; },
    sorryWeDontSupport: 'Sorry, we don\'t support the web address book for your e-mail address. Try clicking \'E-mail Application\' below to use addresses from your computer.',
    pleaseSelectSecondPart: 'Please select the second part of your e-mail address, e.g., gmail.com.',
    atSymbolNotAllowed: 'Please ensure that the @ symbol is not in the first part of the e-mail address.',
    resetTextQ: 'Reset Text?',
    resetTextToOriginalVersion: 'Are you sure you wish to reset all of your text to the original version? All of your changes will be lost.',
    changeQuestionsToPublic: 'Change questions to public?',
    changingPrivateQuestionsToPublic: 'Changing private questions to public will expose all members\' answers. Are you sure?',
    youHaveUnsavedChanges: 'You have unsaved changes.',
    pleaseEnterASiteName: 'Please enter a name for the social network, e.g. Tiny Clown Club',
    pleaseEnterShorterSiteName: 'Please enter a shorter name (max 64 characters)',
    pleaseEnterShorterSiteDescription: 'Please enter a shorter description (max 250 characters)',
    siteNameHasInvalidCharacters: 'The name has some invalid characters',
    thereIsAProblem: 'There is a problem with your information',
    thisSiteIsOnline: 'This social network is Online',
    onlineSiteCanBeViewed: '<strong>Online</strong> - Network can be viewed with respect to your privacy settings.',
    takeOffline: 'Take Offline',
    thisSiteIsOffline: 'This social network is Offline',
    offlineOnlyYouCanView: '<strong>Offline</strong> - Only you can view this social network.',
    takeOnline: 'Take Online',
    themeSettings: 'Theme Settings',
    addYourOwnCss: 'Advanced',
    error: 'Error',
    pleaseEnterTitleForFeature: function(displayName) { return 'Please enter a title for your ' + displayName + ' feature '; },
    thereIsAProblemWithTheInformation: 'There is a problem with the information entered',
    photos: 'Photos',
    videos: 'Videos',
    pleaseEnterTheChoicesFor: function(questionTitle) { return 'Please enter the choices for "' + questionTitle + '" e.g. Hiking, Reading, Shopping '; },
    pleaseEnterTheChoices: 'Please enter the choices e.g. Hiking, Reading, Shopping',
    shareWithFriends: 'Share with Friends',
    email: 'e-mail',
    separateMultipleAddresses: 'Separate multiple addresses with commas',
    subject: 'Subject',
    message: 'Message',
    send: 'Send',
    cancel: 'Cancel',
    pleaseEnterAValidEmail: 'Please enter a valid e-mail address',
    go: 'Go',
    areYouSureYouWant: 'Are you sure you want to do this?',
    processing: 'Processing…',
    pleaseKeepWindowOpen: 'Please keep this browser window open while processing continues. It may take a few minutes.',
    complete: 'Complete!',
    processIsComplete: 'Process is complete.',
    ok: 'OK',
    body: 'Body',
    pleaseEnterASubject: 'Please enter a subject',
    pleaseEnterAMessage: 'Please enter a message',
    thereHasBeenAnError: 'There has been an error',
    fileNotFound: 'File not found',
    pleaseProvideADescription: 'Please provide a description',
    pleaseEnterYourFriendsAddresses: 'Please enter your friends\' addresses or Ning IDs',
    pleaseEnterSomeFeedback: 'Please enter some feedback',
    title: 'Title:',
    setAsMainSiteFeature: 'Set as Main Feature',
    thisIsTheMainSiteFeature: 'This is the main feature',
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
    changePrivacy: 'Change Privacy?',
    keepWindowOpenWhileChanging: 'Please keep this browser window open while privacy settings are being changed. This process may take a few minutes.',
    addingInstructions: 'Please leave this window open while your content is being added.',
    addingLabel: 'Adding… .',
    cannotKeepFiles: 'You will have to choose your files again if you wish to view more options.  Would you like to continue?',
    done: 'Done',
    looksLikeNotImage: 'One or more files do not seem to be in the .jpg, .gif, or .png format.  Would you like to try uploading anyway?',
    looksLikeNotMusic: 'The file you have selected does not seem to be in the .mp3 format.  Would you like to try uploading anyway?',
    looksLikeNotVideo: 'The file you have selected does not seem to be in the .mov, .mpg, .mp4, .avi, .3gp or .wmv format.  Would you like to try uploading anyway?',
    messageIsTooLong: function(n) { return 'Your message is too long.  Please use '+n+' characters or less.'; },
    pleaseSelectPhotoToUpload: 'Please select a photo to upload.',
    processingFailed: 'Sorry, processing has failed.  Please try again later.',
    selectOrPaste: 'You need to select a video or paste the \'embed\' code',
    selectOrPasteMusic: 'You need to select a song or paste the URL',
    sendingLabel: 'Sending… .',
    thereWasAProblem: 'There was a problem adding your content.  Please try again later.',
    uploadingInstructions: 'Please leave this window open while your upload is in progress',
    uploadingLabel: 'Uploading… .',
    youNeedToAddEmailRecipient: 'You need to add an e-mail recipient.',
    yourMessage: 'Your Message',
    yourMessageIsBeingSent: 'Your message is being sent.',
    yourSubject: 'Your Subject'
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
    fileIsNotAnMp3: 'One of the files does not seem to be an MP3. Do you want to try uploading it anyway?',
    entryNotAUrl: 'One of the entries does not appear to be a URL. Make sure all entries start with <kbd>http://</kbd>',
    shufflePlaylist: 'Shuffle Playlist'
});

dojo.evalObjPath('xg.notes.nls', true);
dojo.lang.mixin(xg.notes.nls, xg.index.i18n, {
    addNewNote: 'Add New Note',
    noteTitleTooLong: 'Note title is too long',
    pleaseEnterNoteEntry: 'Please enter a note entry',
    pleaseEnterNoteTitle: 'Please enter a note title!'
});

dojo.evalObjPath('xg.page.nls', true);
dojo.lang.mixin(xg.page.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'The number of characters (' + n + ') exceeds the maximum (' + maximum + ') '; },
    pleaseEnterContent: 'Please enter the page content',
    pleaseEnterTitle: 'Please enter a title for the page',
    pleaseEnterAComment: 'Please enter a comment',
    deleteThisComment: 'Are you sure you want to delete this comment?',
    save: 'Save',
    cancel: 'Cancel',
    discussionTitle: 'Page Title:',
    tags: 'Tags:',
    edit: 'Edit',
    close: 'Close',
    displayPagePosts: 'Display Page Posts',
    thereIsAProblem: 'There is a problem with your information'
});


dojo.evalObjPath('xg.photo.nls', true);
dojo.lang.mixin(xg.photo.nls, xg.index.i18n, {
    untitled: 'Untitled',
    photos: 'Photos',
    edit: 'Edit',
    photosFromAnAlbum: 'Albums',
    show: 'Show',
    rows: 'rows',
    cancel: 'Cancel',
    save: 'Save',
    deleteThisPhoto: 'Delete this photo?',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'The number of characters (' + n + ') exceeds the maximum (' + maximum + ') '; },
    weCouldNotLookUpAddress: function(address) { return 'Sorry, we couldn\'t look up the address "' + address + '". '; },
    pleaseSelectPhotoToUpload: 'Please select a photo to upload.',
    pleaseEnterAComment: 'Please enter a comment.',
    addToExistingAlbum: 'Add to Existing Album',
    addToNewAlbumTitled: 'Add to a New Album Titled…',
    deleteThisComment: 'Delete this comment?',
    importingNofMPhotos: function(n,m) { return 'Importing <span id="currentP">' + n + '</span> of ' + m + ' photos. '},
    starting: 'Starting…',
    done: 'Done!',
    from: 'From',
    display: 'Display',
    takingYou: 'Taking you to see your photos…',
    anErrorOccurred: 'Unfortunately an error occurred. Please report this issue using the link at the bottom of the page.',
    weCouldntFind: 'We couldn\'t find any photos! Why don\'t you try one of the other options?',
    randomOrder: 'Random Order'
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
    pleaseEnterValueForPost: 'Please enter a value for the post',
    pleaseProvideAValidDate: 'Please provide a valid date',
    uploadAFile: 'Upload a File',
    pleaseEnterUrlOfLink: 'Please enter the URL of the link:',
    pleaseEnterTextOfLink: 'What text would you like to link?',
    edit: 'Edit',
    recentlyAdded: 'Recently Added',
    featured: 'Featured',
    iHaveRecentlyAdded: 'I\'ve Recently Added',
    fromTheSite: 'From the Social Network',
    cancel: 'Cancel',
    save: 'Save',
    loading: 'Loading…',
    addAsFriend: 'Add as friend',
    requestSent: 'Request Sent!',
    sendingFriendRequest: 'Sending Friend Request',
    thisIsYou: 'This is you!',
    isYourFriend: 'Is your friend',
    isBlocked: 'Is blocked',
    pleaseEnterAComment: 'Please enter a comment',
    pleaseEnterPostBody: 'Please enter something for the post body',
    pleaseSelectAFile: 'Please select a file',
    pleaseEnterChatter: 'Please enter something for your comment',
    toggleBetweenHTML: 'Show/Hide HTML Code',
    attachAFile: 'Attach a File',
    addAPhoto: 'Add a Photo',
    insertALink: 'Insert a Link',
    changeTextSize: 'Change Text Size',
    makeABulletedList: 'Make a Bulleted List',
    makeANumberedList: 'Make a Numbered List',
    crossOutText: 'Cross-out Text',
    underlineText: 'Underline Text',
    italicizeText: 'Italicize Text',
    boldText: 'Bold Text',
    letMeApproveChatters: 'Let me approve comments before posting?',
    noPostChattersImmediately: 'No – post comments immediately',
    yesApproveChattersFirst: 'Yes – approve comments first',
    yourCommentMustBeApproved: 'Your comment must be approved before everyone can see it.',
    reallyDeleteThisPost: 'Really delete this post?',
    commentWall: 'Comment Wall',
    commentWallNComments: function(n) {
        switch(n) {
            case 1: return 'Comment Wall (1 comment) ';
            default: return 'Comment Wall (' + n + ' comments) ';
        }
    },
    display: 'Display',
    from: 'From',
    show: 'Show',
    rows: 'rows',
    posts: 'posts'
});


dojo.evalObjPath('xg.shared.nls', true);
dojo.lang.mixin(xg.shared.nls, xg.index.i18n, {
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
    pickAColor: 'Pick a Colour…',
    openColorPicker: 'Open Colour Picker',
    loading: 'Loading…',
    ok: 'OK',
    save: 'Save',
    cancel: 'Cancel',
    saving: 'Saving…',
    addAnImage: 'Add an Image',
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
    clickToEdit: 'Click to edit',
    sendingFriendRequest: 'Sending Friend Request',
    requestSent: 'Request Sent!',
    pleaseCorrectErrors: 'Please correct these errors',
    tagThis: 'Tag This',
    addOrEditYourTags: 'Add or edit your tags:',
    addYourRating: 'Add your rating:',
    separateMultipleTagsWithCommas: 'Separate multiple tags with commas e.g. cool, "new zealand"',
    saved: 'Saved!',
    noo: 'NEW',
    none: 'NONE',
    joinNow: 'Join Now',
    join: 'Join',
    youHaventRated: 'You haven\'t rated this item yet.',
    yourRatedThis: function(n) {
        switch(n) {
            case 1: return 'You rated this item with 1 star. ';
            default: return 'You rated this item with ' + n + ' stars. ';
        }
    },
    yourRatingHasBeenAdded: 'Your rating has been added.',
    thereWasAnErrorRating: 'There was an error rating this content.',
    yourTagsHaveBeenAdded: 'Your tags have been added.',
    thereWasAnErrorTagging: 'There was an error adding tags.',
    addToFavorites: 'Add to Favourites',
    removeFromFavorites: 'Remove from Favourites',
    nStarsOutOfM: function(n,m) {
        switch(n) {
            case 1: return '1 star out of ' + m;
            default: return n + ' stars out of ' + m;
        }
    },
    follow: 'Follow',
    stopFollowing: 'Stop Following',
    pendingPromptTitle: 'Membership Pending Approval',
    youCanDoThis: 'You can do this once your membership has been approved by the administrators.',
    pleaseEnterAComment: 'Please enter a comment',
    pleaseEnterAFileAddress: 'Please enter the address of the file',
    pleaseEnterAWebsite: 'Please enter a website address'
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
    saving: 'Saving…',
    deleteThisVideo: 'Delete this video?',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'The number of characters (' + n + ') exceeds the maximum (' + maximum + ') '; },
    weCouldNotLookUpAddress: function(address) { return 'Sorry, we couldn\'t look up the address "' + address + '". '; },
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
    pleaseEnterAComment: 'Please enter a comment.',
    youRatedVideoNStars: function(n) {
        switch(n) {
            case 1: return 'You rated this video 1 star! ';
            default: return 'You rated this video ' + n + ' stars! ';
        }
    },
    deleteThisComment: 'Delete this comment?',
    embedHTMLCode: 'HTML Embed Code:',
    copyHTMLCode: 'Copy HTML Code'
});