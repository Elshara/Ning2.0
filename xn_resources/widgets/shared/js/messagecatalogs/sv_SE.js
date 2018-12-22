dojo.provide('xg.shared.messagecatalogs.sv_SE');

dojo.require('xg.index.i18n');

/**
 * Texts for the sv_SE
 */
// Use UTF-8 byte sequences instead of HTML entities, e.g., & instead of &amp;, â€¦ instead of &hellip;  [Jon Aquino 2007-01-10]

dojo.evalObjPath('xg.events.nls', true);
dojo.lang.mixin(xg.events.nls, xg.index.i18n, {
    pleaseChooseImage: 'Välj en bild för eventet',
    pleaseEnterAMessage: 'Skicka ett meddelande',
    pleaseEnterDescription: 'Ge en beskrivning av eventet',
    pleaseEnterLocation: 'Ange en plats för eventet',
    pleaseEnterTitle: 'Ange en rubrik för eventet',
    pleaseEnterType: 'Ange åtminstone en typ för eventet',
    send: 'Skicka',
    sending: 'Skickar...',
    thereHasBeenAnError: 'Ett fel har uppstått',
    yourMessage: 'Ditt meddelande',
    yourMessageHasBeenSent: 'Ditt meddelande har skickats',
    yourMessageIsBeingSent: 'Ditt meddelande skickas.'
});

dojo.evalObjPath('xg.feed.nls', true);
dojo.lang.mixin(xg.feed.nls, xg.index.i18n, {
    edit: 'Ändra',
    title: 'Titel:',
    feedUrl: 'URL:',
    show: 'Visa:',
    titles: 'Endast Titlar',
    titlesAndDescriptions: 'Visa Detalj',
    display: 'Bildskärm',
    cancel: 'Avbryt',
    save: 'Spara',
    loading: 'Laddar…',
    items: 'artiklar'
});


dojo.evalObjPath('xg.gadgets.nls', true);
dojo.lang.mixin(xg.gadgets.nls, xg.index.i18n, {
    edit: 'Ändra',
    title: 'Titel:',
    feedUrl: 'URL:',
    cancel: 'Avbryt',
    save: 'Spara',
    loading: 'Laddar…',
    removeGadget: 'Ta bort Gadget',
    findGadgetsInDirectory: 'Sök efter Gadgets i Gadgetbiblioteket'
});


dojo.evalObjPath('xg.forum.nls', true);
dojo.lang.mixin(xg.forum.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Antalet tecken (' + n + ') överskrider det maximalt tillåtna (' + maximum + ') '; },
    pleaseEnterFirstPost: 'Skriv första diskussionsinlägget',
    pleaseEnterTitle: 'Ange en titel på diskussionen',
    save: 'Spara',
    cancel: 'Avbryt',
    yes: 'Ja',
    no: 'Nej',
    edit: 'Ändra',
    deleteCategory: 'Ta bort Kategori',
    discussionsWillBeDeleted: 'Diskussionerna i denna kategori kommer att tas bort.',
    whatDoWithDiscussions: 'Vad vill du göra med diskussionerna I denna kategori?',
    moveDiscussionsTo: 'Flytta diskussioner till:',
    moveToCategory: 'Flytta till Kategori…',
    deleteDiscussions: 'Ta bort diskussioner',
    'delete': 'Ta bort',
    deleteReply: 'Ta bort Svar',
    deleteReplyQ: 'Ta bort detta svar?',
    deletingReplies: 'Tar bort Svar…',
    doYouWantToRemoveReplies: 'Vill du även ta bort svaren på inlägget?',
    pleaseKeepWindowOpen: 'Håll detta webbläsarfönster öppet medan bearbetningen pågår.  Det kan ta några minuter.',
    from: 'Från',
    show: 'Visa',
    discussions: 'diskussioner',
    discussionsFromACategory: 'Diskussioner från en kategori…',
    display: 'Visa',
    items: 'punkter',
    view: 'Visa'
});


dojo.evalObjPath('xg.uploader.nls', true);
dojo.lang.mixin(xg.uploader.nls, xg.index.i18n, {
    fileBrowserHeader: 'Min dator',
    fileRoot: 'Min dator',
    fileInformationHeader: 'Information',
    uploadHeader: 'Filer att ladda upp',
    dragOutInstructions: 'Drag ut filerna för att ta bort dem',
    dragInInstructions: 'Drag dina filer hit',
    selectInstructions: 'Välj en fil',
    files: 'Filer',
    totalSize: 'Total storlek',
    fileName: 'Namn',
    fileSize: 'Storlek',
    nextButton: 'Nästa >',
    okayButton: 'OK',
    yesButton: 'Ja',
    noButton: 'Nej',
    uploadButton: 'Ladda upp',
    cancelButton: 'Avbryt',
    backButton: 'Tillbaka',
    continueButton: 'Fortsätt',
    uploadingLabel: 'Laddar upp...',
    uploadingStatus: function(n, m) { return 'Laddar upp ' + n + ' av ' + m; },
    uploadingInstructions: 'Lämna detta fönster öppet medan uppladdningsprocessen pågår.',
    uploadLimitWarning: function(n) { return 'Du kan ladda upp ' + n + ' filer åt gången. '; },
    uploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Du har lagt till maximalt antal filer. ';
            case 1: return 'Du kan ladda upp 1 fil till. ';
            default: return 'Du kan ladda upp ' + n + ' filer till. ';
        }
    },
    iHaveTheRight: 'Jag har rätt att ladda upp filerna enligt <a href="/main/authorization/termsOfService">Användarvillkoren</a>',
    updateJavaTitle: 'Uppdatera Java',
    updateJavaDescription: 'Innehållsuppladdaren kräver en nyare version av Java. Klicka på "Okay" för att komma till Java.',
    batchEditorLabel: 'Ändra informationen på samtliga punkter',
    applyThisInfo: 'Använd denna information för filerna nedan',
    titleProperty: 'Titel',
    descriptionProperty: 'Beskrivning',
    tagsProperty: 'Taggar',
    viewableByProperty: 'Kan ses av',
    viewableByEveryone: 'alla',
    viewableByFriends: 'Endast Mina Vänner',
    viewableByMe: 'Endast Jag',
    albumProperty: 'Album',
    artistProperty: 'Artist',
    enableDownloadLinkProperty: 'Aktivera hämtningslänk',
    enableProfileUsageProperty: 'Tillåt andra att lägga in låten på sina sidor',
    licenseProperty: 'Licens',
    creativeCommonsVersion: '3.0',
    selectLicense: '- Välj licens -',
    copyright: '© Alla rättigheter förbehålles',
    ccByX: function(n) { return 'Creative Commons beteckning ' + n; },
    ccBySaX: function(n) { return 'Creative Commons beteckning Dela lika ' + n; },
    ccByNdX: function(n) { return 'Creative Commons beteckning Inga härledningar ' + n; },
    ccByNcX: function(n) { return 'Creative Commons beteckning Icke-kommersiell ' + n; },
    ccByNcSaX: function(n) { return 'Creative Commons beteckning Icke kommersiell Dela lika ' + n; },
    ccByNcNdX: function(n) { return 'Creative Commons beteckning Icke-kommersiell Inga härledningar ' + n; },
    publicDomain: 'Offentlig domän',
    other: 'Annat',
    errorUnexpectedTitle: 'Hoppsan!',
    errorUnexpectedDescription: 'Ett fel har uppstått. Försök på nytt.',
    errorTooManyTitle: 'För många saker',
    errorTooManyDescription: function(n) { return 'Vi är ledsna men du kan bara ladda upp ' + n + ' saker åt gången. '; },
    errorNotAMemberTitle: 'Inte tillåtet',
    errorNotAMemberDescription: 'Vi är ledsna men du måste vara medlem för att få ladda upp.',
    errorContentTypeNotAllowedTitle: 'Inte tillåtet',
    errorContentTypeNotAllowedDescription: 'Vi är ledsna men du får inte ladda upp den typen av innehåll.',
    errorUnsupportedFormatTitle: 'Hoppsan!',
    errorUnsupportedFormatDescription: 'Vi är ledsna men vi stödjer inte den här typen av fil.',
    errorUnsupportedFileTitle: 'Hoppsan!',
    errorUnsupportedFileDescription: 'foo.exe är ett format som inte stöds.',
    errorUploadUnexpectedTitle: 'Hoppsan!',
    errorUploadUnexpectedDescription: function(file) {
		return file ?
			('Det verkar vara något problem med ' + file + ' filen. Tag bort den från listan innan du laddar upp resten av filerna.') :
			'Det verkar vara något problem med filen högst upp på listan. Ta bort den innan du laddar upp resten av filerna.';
	},
    cancelUploadTitle: 'Avbryt Uppladdning?',
    cancelUploadDescription: 'Är du säker på att du vill avbryta återstående uppladdningar?',
    uploadSuccessfulTitle: 'Uppladdningen är klar',
    uploadSuccessfulDescription: 'Vänta medan vi tar dig till dina uppladdningar...',
    uploadPendingDescription: 'Dina filer laddades upp utan problem och väntar på godkännande.',
    photosUploadHeader: 'Bilder att ladda upp',
    photosDragOutInstructions: 'Drag ut bilderna för att ta bort dem',
    photosDragInInstructions: 'Drag dina bilder hit',
    photosSelectInstructions: 'Välj en bild',
    photosFiles: 'Bilder',
    photosUploadingStatus: function(n, m) { return 'Laddar upp bild ' + n + ' av ' + m; },
    photosErrorTooManyTitle: 'För många bilder',
    photosErrorTooManyDescription: function(n) { return 'Vi är ledsna men du kan bara ladda upp ' + n + ' bilder åt gången. '; },
    photosErrorContentTypeNotAllowedDescription: 'Vi är ledsna men bilduppladdningen är stängd.',
    photosErrorUnsupportedFormatDescription: 'Vi är ledsna men du kan bara ladda upp bilder i .jpg, .gif eller .png format.',
    photosErrorUnsupportedFileDescription: function(n) { return n + ' är inte en .jpg, .gif eller .png fil.'; },
    photosBatchEditorLabel: 'Ändra information för samtliga bilder',
    photosApplyThisInfo: 'Använd denna information för nedanstående bilder',
    photosErrorUploadUnexpectedDescription: function(file) {
		return file ?
			('Det verkar vara något problem med ' + file + ' filen. Ta bort den från listan innan du laddar upp resten av bilderna.') :
			'Det verkar vara något problem med bilden högst upp på listan. Ta bort den innan du laddar upp resten av bilderna.';
	},
    photosUploadSuccessfulDescription: 'Vänta medan vi tar dig till dina bilder...',
    photosUploadPendingDescription: 'Dina bilder laddades upp utan problem och väntar på godkännande.',
    photosUploadLimitWarning: function(n) { return 'Du kan ladda upp ' + n + ' bilder åt gången. '; },
    photosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Du har lagt till maximalt antal bilder. ';
            case 1: return 'Du kan ladda upp 1 bild till. ';
            default: return 'Du kan ladda upp ' + n + ' bilder till. ';
        }
    },
    photosIHaveTheRight: 'Jag har rätt att ladda upp bilderna enligt<a href="/main/authorization/termsOfService">Användarvillkoren</a>',
    videosUploadHeader: 'Videor för uppladdning',
    videosDragInInstructions: 'Drag dina videor hit',
    videosDragOutInstructions: 'Drag ut dina videor för att ta bort dem.',
    videosSelectInstructions: 'Välj en video',
    videosFiles: 'Videor',
    videosUploadingStatus: function(n, m) { return 'Laddar upp video ' + n + ' av ' + m; },
    videosErrorTooManyTitle: 'För många videor',
    videosErrorTooManyDescription: function(n) { return 'Vi är ledsna men du kan bara ladda upp ' + n + ' videor åt gången. '; },
    videosErrorContentTypeNotAllowedDescription: 'Vi är ledsna men videouppladdningen är stängd.',
    videosErrorUnsupportedFormatDescription: 'Vi är ledsna men du kan bara ladda upp videor i .avi, .mov, .mp4, .wmv eller .mpg format.',
    videosErrorUnsupportedFileDescription: function(x) { return x + ' är inte en .avi, .mov, .mp4, .wmv eller .mpg fil.'; },
    videosBatchEditorLabel: 'Ändra information för samtliga videor',
    videosApplyThisInfo: 'Använd denna information för videorna nedan',
    videosErrorUploadUnexpectedDescription: function(file) {
		return file ?
			('Det verkar vara något problem med ' + file + ' filen. Ta bort den från listan innan du laddar upp resten av videorna.') :
			'Det verkar vara något problem med videon högst upp på listan. Ta bort den innan du laddar upp resten av videorna.';
	},
    videosUploadSuccessfulDescription: 'Vänta medan vi tar dig till dina videor...',
    videosUploadPendingDescription: 'Dina videor laddades upp utan problem och väntar på godkännande.',
    videosUploadLimitWarning: function(n) { return 'Du kan ladda upp ' + n + ' videor åt gången. '; },
    videosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Du har lagt till maximalt antal videor. ';
            case 1: return 'Du kan ladda upp 1 video till. ';
            default: return 'Du kan ladda upp ' + n + ' videor till. ';
        }
    },
    videosIHaveTheRight: 'Jag har rätt att ladda upp videorna enligt<a href="/main/authorization/termsOfService">Användarvillkoren</a>',
    musicUploadHeader: 'Låtar att ladda upp',
    musicTitleProperty: 'Låttitel',
    musicDragOutInstructions: 'Drag ut dina låtar för att ta bort dem',
    musicDragInInstructions: 'Drag dina låtar hit',
    musicSelectInstructions: 'Välj en låt',
    musicFiles: 'Låtar',
    musicUploadingStatus: function(n, m) { return 'Laddar upp låt ' + n + ' av ' + m; },
    musicErrorTooManyTitle: 'För många låtar',
    musicErrorTooManyDescription: function(n) { return 'Vi är ledsna men du kan bara ladda upp ' + n + ' låtar åt gången. '; },
    musicErrorContentTypeNotAllowedDescription: 'Vi är ledsna men låtuppladdningen är stängd.',
    musicErrorUnsupportedFormatDescription: 'Vi är ledsna men du kan bara ladda upp låtar i .mp3 format.',
    musicErrorUnsupportedFileDescription: function(x) { return x + ' är inte en .mp3 fil.'; },
    musicBatchEditorLabel: 'Ändra information för samtliga låtar',
    musicApplyThisInfo: 'Använd denna information för låtarna nedan',
    musicErrorUploadUnexpectedDescription: function(file) {
		return file ?
			('Det verkar vara något problem med ' + file + ' filen. Ta bort den från listan innan du laddar upp resten av låtarna.') :
			'Det verkar vara något problem med låten högst upp på listan. Ta bort den innan du laddar upp resten av låtarna.';
	},
    musicUploadSuccessfulDescription: 'Vänta medan vi tar dig till dina låtar...',
    musicUploadPendingDescription: 'Dina låtar laddades upp utan problem och väntar på godkännande.',
    musicUploadLimitWarning: function(n) { return 'Du kan ladda upp ' + n + ' låtar åt gången. '; },
    musicUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Du har lagt till maximalt antal låtar. ';
            case 1: return 'Du kan ladda upp 1 låt till. ';
            default: return 'Du kan ladda upp ' + n + ' låtar till. ';
        }
    },
    musicIHaveTheRight: 'Jag har rätt att ladda upp låtarna enligt<a href="/main/authorization/termsOfService">Användarvillkoren</a>'
});


dojo.evalObjPath('xg.groups.nls', true);
dojo.lang.mixin(xg.groups.nls, xg.index.i18n, {
    pleaseChooseAName: 'Välj ett namn på din grupp.',
    pleaseChooseAUrl: 'Välj en webbadress till din grupp.',
    urlCanContainOnlyLetters: 'Webbadressen får endast innehålla bokstäver och siffror (inga mellanslag).',
    descriptionTooLong: function(n, maximum) { return 'Längden på beskrivningen av din grupp (' + n + ') överskrider den maximalt tillåtna (' + maximum + ') '; },
    nameTaken: 'Vi beklagar – detta namn är redan upptaget.  Välj ett annat namn.',
    urlTaken: 'Vi beklagar – denna webbadress är redan upptagen.  Välj en annan webbadress.',
    whyNot: 'Varför inte?',
    groupCreatorDetermines: function(href) { return 'Gruppens bildare bestämmer vem som får vara med.  Om du känner att du blivit blockerad av misstag <a ' + href + '>kontakta gruppens bildare</a> '; },
    edit: 'Ändra',
    from: 'Från',
    show: 'Visa',
    groups: 'grupper',
    pleaseEnterName: 'Ange ditt namn',
    pleaseEnterEmailAddress: 'Ange din e-postadress',
    xIsNotValidEmailAddress: function(x) { return x + ' is not a valid email address'; },
    save: 'Spara',
    cancel: 'Avbryt'
});


dojo.evalObjPath('xg.html.nls', true);
dojo.lang.mixin(xg.html.nls, xg.index.i18n, {
    edit: 'Ändra',
    save: 'Spara',
    cancel: 'Avbryt',
    saving: 'Sparar…',
    addAWidget: function(url) { return '<a href="' + url + '">Ange en widget</a> för denna textruta '; },
    contentsTooLong: function(maximum) { return 'Innehållet är för långt. Använd färre än ' + maximum + ' bokstäver. '; }
});


dojo.evalObjPath('xg.index.nls', true);
dojo.lang.mixin(xg.index.nls, xg.index.i18n, {
    showingNFriends: function(n, searchString) {
        switch(n) {
            case 1: return 'Visar 1 vän som matchar "' + searchString + '". <a href="#">Visa alla</a> ';
            default: return 'Visar ' + n + ' vänner som matchar "' + searchString + '". <a href="#">Visa alla</a> ';
        }
    },
    sendMessage: 'Skicka meddelande',
    sendMessageToNFriends: function(n) {
        switch(n) {
            case 1: return 'Skicka meddelande till 1 vän? ';
            default: return 'Skicka meddelande till ' + n + ' vänner? ';
        }
    },
    invitingNFriends: function(n) {
        switch(n) {
            case 1: return 'Bjuder in 1 vän… ';
            default: return 'Bjuder in ' + n + ' vänner… ';
        }
    },
    nFriends: function(n) {
        switch(n) {
            case 1: return '1 vän… ';
            default: return n + ' vänner… ';
        }
    },
    sendingMessageToNFriends: function(n) {
        switch(n) {
            case 1: return 'Skickar meddelande till 1 vän… ';
            default: return 'Skickar meddelande till ' + n + ' vänner… ';
        }
    },
    noPeopleSelected: 'Inga personer har valts',
    sorryWeDoNotSupport: 'Tyvärr stödjer vi inte adressboken till din e-postadress. Klicka på  \'Address Book Application\' nedan för att använda adresserna från din dator.',
    pleaseChooseFriends: 'Välj ut några vänner innan du skickar ditt meddelande.',
    htmlNotAllowed: 'HTML ej tillåtet',
    noFriendsFound: 'Det fanns inga vänner som matchade din sökning.',
    sendInvitation: 'Sänd Inbjudan',
    sendInvitationToNFriends: function(n) {
        switch(n) {
            case 1: return 'Sänd inbjudan till 1 vän? ';
            default: return 'Sänd inbjudan till ' + n + ' vänner? ';
        }
    },
    yourMessageOptional: '<label>Ditt Meddelande</label> (Valfritt)',
    pleaseChoosePeople: 'Välj några personer att bjuda in.',
    pleaseEnterEmailAddress: 'Ange din e-postadress.',
    pleaseEnterPassword: function(emailAddress) { return 'Ange ditt lösenord för ' + emailAddress + '. '; },
    sorryWeDontSupport: 'Vi stöder tyvärr inte webbadressboken för dina e-postadresser.  Försök att klicka på \'E-postprogram\' nedan för att använda adresser från din dator.',
    pleaseSelectSecondPart: 'Välj den andra delen av din e-postadress, t.ex. gmail. com.',
    atSymbolNotAllowed: 'Se till att @-symbolen inte finns i den första delen av e-postadressen.',
    resetTextQ: 'Återställ Text?',
    resetTextToOriginalVersion: 'Är du säker på att du vill återställa all din text till den ursprungliga versionen?  Alla dina ändringar förloras.',
    changeQuestionsToPublic: 'Vill du göra frågorna offentliga?',
    changingPrivateQuestionsToPublic: 'Förändring av privata frågor till offentliga kommer att göra alla medlemmars svar tillgängliga.  Är du säker?',
    youHaveUnsavedChanges: 'Du har ändringar som inte sparats.',
    pleaseEnterASiteName: 'Ange ett namn för det sociala nätverket, t.ex. Tiny Clown Club',
    pleaseEnterShorterSiteName: 'Ange ett kortare namn (max 64 tecken)',
    pleaseEnterShorterSiteDescription: 'Ge en kortare beskrivning (max 250 tecken)',
    siteNameHasInvalidCharacters: 'Namnet har en del ogiltiga tecken',
    thereIsAProblem: 'Det finns ett problem i din information',
    thisSiteIsOnline: 'Detta sociala nätverk är Online',
    onlineSiteCanBeViewed: '<strong>Online</strong> - Nätverket kan visas beträffande dina integritetsinställningar.',
    takeOffline: 'Ta Offline',
    thisSiteIsOffline: 'Detta sociala nätverk är Offline',
    offlineOnlyYouCanView: '<strong>Offline</strong> - Endast du kan se detta sociala nätverk.',
    takeOnline: 'Ta Online',
    themeSettings: 'Temainställningar',
    addYourOwnCss: 'Avancerad',
    error: 'Fel',
    pleaseEnterTitleForFeature: function(displayName) { return 'Ange en titel på din ' + displayName + ' funktion '; },
    thereIsAProblemWithTheInformation: 'Det finns ett problem i den inmatade informationen',
    photos: 'Bilder',
    videos: 'Videor',
    pleaseEnterTheChoicesFor: function(questionTitle) { return 'Ange valen för "' + questionTitle + '" t.ex. Utflykter, Läsning, Shopping '; },
    pleaseEnterTheChoices: 'Ange valen, t.ex. Utflykter, Läsning, Shopping',
    shareWithFriends: 'Dela med Vänner',
    email: 'e-post',
    separateMultipleAddresses: 'Avdela multipla adresser med kommatecken',
    subject: 'Ämne',
    message: 'Meddelande',
    send: 'Skicka',
    cancel: 'Avbryt',
    pleaseEnterAValidEmail: 'Ange en giltig e-postadress',
    go: 'Gå',
    areYouSureYouWant: 'Är du säker på att du vill göra detta?',
    processing: 'Databehandling…',
    pleaseKeepWindowOpen: 'Håll detta webbläsarfönster öppet medan bearbetningen pågår.  Det kan ta några minuter.',
    complete: 'Klar!',
    processIsComplete: 'Bearbetningen är klar.',
    ok: 'OK',
    body: 'Brödtext',
    pleaseEnterASubject: 'Ange ett ämne',
    pleaseEnterAMessage: 'Ange ett meddelande',
    thereHasBeenAnError: 'Ett fel har uppstått',
    fileNotFound: 'Filen återfinns ej',
    pleaseProvideADescription: 'Lämna en beskrivning',
    pleaseEnterYourFriendsAddresses: 'Ange din väns adresser eller Ning-ID',
    pleaseEnterSomeFeedback: 'Ge någon form av feedback',
    title: 'Titel:',
    setAsMainSiteFeature: 'Ställ in som Huvudfunktion',
    thisIsTheMainSiteFeature: 'Detta är huvudfunktionen',
    customized: 'Anpassad',
    copyHtmlCode: 'Kopiera HTML-kod',
    playerSize: 'Spelarstorlek',
    selectSource: 'Välj Källa',
    myAlbums: 'Mina Album',
    myMusic: 'Min musik',
    myVideos: 'Mina videoklipp',
    showPlaylist: 'Visa Spellista',
    change: 'Ändra',
    changing: 'Ändrar...',
    changePrivacy: 'Ändra Integritet?',
    keepWindowOpenWhileChanging: 'Håll detta webbläsarfönster öppet medan integritetsinställningar ändras.  Denna bearbetning kan ta några minuter.',
    addingInstructions: 'Lämna detta fönster öppet medan ditt innehåll läggs till.',
    addingLabel: 'Lägger till.. .',
    cannotKeepFiles: 'Du måste välja filer på nytt om du vill visa fler alternativ.  Vill du fortsätta?',
    done: 'Klart',
    looksLikeNotImage: 'En eller flera filer verkar inte vara i .jpg, .gif, eller .png format.  Vill du försöka ladda upp ändå?',
    looksLikeNotMusic: 'Den fil du valde verkar inte vara i mp3-format.  Vill du försöka ladda upp ändå?',
    looksLikeNotVideo: 'Den fil du valde verkar inte vara i .mov, .mpg, .mp4, .avi, .3gp eller .wmv format.  Vill du försöka ladda upp ändå?',
    messageIsTooLong: function(n) { return 'Ditt meddelande är för långt.  Använd '+n+' tecken eller mindre.'; },
    pleaseSelectPhotoToUpload: 'Välj en bild att ladda upp.',
    processingFailed: 'Vi beklagar men bearbetningen misslyckades.  Försök igen senare.',
    selectOrPaste: 'Du måste välja en video eller klistra in \'embed\'-koden',
    selectOrPasteMusic: 'Du måste välja en låt eller klistra in URL:en',
    sendingLabel: 'Skickar...',
    thereWasAProblem: 'Ett problem uppstod när ditt innehåll laddades upp.  Försök igen senare.',
    uploadingInstructions: 'Lämna detta fönster öppet medan uppladdningsprocessen pågår.',
    uploadingLabel: 'Laddar upp...',
    youNeedToAddEmailRecipient: 'Du måste lägga till en e-postmottagare.',
    yourMessage: 'Ditt meddelande',
    yourMessageIsBeingSent: 'Ditt meddelande skickas.',
    yourSubject: 'Ditt ämne'
});


dojo.evalObjPath('xg.music.nls', true);
dojo.lang.mixin(xg.music.nls, xg.index.i18n, {
    play: 'Spela upp',
    pleaseSelectTrackToUpload: 'Välj en låt att ladda upp.',
    pleaseEnterTrackLink: 'Ange URL för en låt.',
    thereAreUnsavedChanges: 'Det finns ändringar som inte sparats.',
    autoplay: 'Spela upp automatiskt',
    showPlaylist: 'Visa Spellista',
    playLabel: 'Spela upp',
    url: 'URL',
    rssXspfOrM3u: 'rss, xspf, eller m3u',
    save: 'Spara',
    cancel: 'Avbryt',
    edit: 'Ändra',
    fileIsNotAnMp3: 'En av filerna tycks inte vara en MP3-fil.  Försök att ladda upp den ändå?',
    entryNotAUrl: 'En av dessa inmatningar tycks inte vara en URL.  Se till att alla inmatningar börjar med <kbd>http://</kbd>',
    shufflePlaylist: 'Blanda spellistan'
});

dojo.evalObjPath('xg.notes.nls', true);
dojo.lang.mixin(xg.notes.nls, xg.index.i18n, {
    addNewNote: 'Lägg till en ny anteckning',
    noteTitleTooLong: 'Anteckningsrubriken är för lång',
    pleaseEnterNoteEntry: 'Gör ett anteckningsinlägg',
    pleaseEnterNoteTitle: 'Ange en anteckningsrubrik.'
});

dojo.evalObjPath('xg.page.nls', true);
dojo.lang.mixin(xg.page.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Antalet tecken (' + n + ') överskrider det maximalt tillåtna (' + maximum + ') '; },
    pleaseEnterContent: 'Ange sidans innehåll',
    pleaseEnterTitle: 'Ange en titel på sidan',
    pleaseEnterAComment: 'Skriv ett inlägg',
    deleteThisComment: 'Är du säker på att du vill ta bort detta inlägg?',
    save: 'Spara',
    cancel: 'Avbryt',
    discussionTitle: 'Sidrubrik:',
    tags: 'Taggar:',
    edit: 'Ändra',
    close: 'Stäng',
    displayPagePosts: 'Visa sidposter',
    thereIsAProblem: 'Det finns ett problem med din information'
});


dojo.evalObjPath('xg.photo.nls', true);
dojo.lang.mixin(xg.photo.nls, xg.index.i18n, {
    untitled: 'Utan titel',
    photos: 'Bilder',
    edit: 'Ändra',
    photosFromAnAlbum: 'Album',
    show: 'Visa',
    rows: 'rader',
    cancel: 'Avbryt',
    save: 'Spara',
    deleteThisPhoto: 'Ta bort denna bild?',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Antalet tecken (' + n + ') överskrider det maximalt tillåtna (' + maximum + ') '; },
    weCouldNotLookUpAddress: function(address) { return 'Vi kunde tyvärr inte hitta adressen "' + address + '". '; },
    pleaseSelectPhotoToUpload: 'Välj en bild att ladda upp.',
    pleaseEnterAComment: 'Skriv ett inlägg.',
    addToExistingAlbum: 'Lägg till i Befintligt Album',
    addToNewAlbumTitled: 'Lägg till i Nytt Album med Titeln…',
    deleteThisComment: 'Ta bort detta inlägg?',
    importingNofMPhotos: function(n,m) { return 'Hämtar <span id="currentP">' + n + '</span> av ' + m + ' bilder. '},
    starting: 'Startar…',
    done: 'Klar!',
    from: 'Från',
    display: 'Bildskärm',
    takingYou: 'Tar med dig för att se dina bilder…',
    anErrorOccurred: 'Ett fel har tyvärr uppstått.  Rapportera denna fråga via länken längst ner på sidan.',
    weCouldntFind: 'Vi kunde inte hitta några bilder!  Varför inte pröva något av de andra alternativen?',
    randomOrder: 'Slumpordning'
});


dojo.evalObjPath('xg.activity.nls', true);
dojo.lang.mixin(xg.activity.nls, xg.index.i18n, {
    edit: 'Ändra',
    show: 'Visa',
    events: 'händelser',
    setWhatActivityGetsDisplayed: 'Ställ in vilken aktivitet som visas',
    save: 'Spara',
    cancel: 'Avbryt'
});


dojo.evalObjPath('xg.profiles.nls', true);
dojo.lang.mixin(xg.profiles.nls, xg.index.i18n, {
    pleaseEnterValueForPost: 'Ange ett värde för posten',
    pleaseProvideAValidDate: 'Ange ett giltigt datum',
    uploadAFile: 'Ladda upp en Fil',
    pleaseEnterUrlOfLink: 'Ange länkens URL:',
    pleaseEnterTextOfLink: 'Vilken text vill du länka?',
    edit: 'Ändra',
    recentlyAdded: 'Nyligen Tillagda',
    featured: 'Framhållna',
    iHaveRecentlyAdded: 'Jag har Nyligen Lagt till',
    fromTheSite: 'Från det Sociala Nätverket',
    cancel: 'Avbryt',
    save: 'Spara',
    loading: 'Laddar…',
    addAsFriend: 'Lägg till som vän',
    requestSent: 'Begäran Avsänd!',
    sendingFriendRequest: 'Sänder Vänförfrågan',
    thisIsYou: 'Detta är du!',
    isYourFriend: 'Är din vän',
    isBlocked: 'Är blockerad',
    pleaseEnterAComment: 'Skriv ett inlägg',
    pleaseEnterPostBody: 'Lämna något som brödtext i posten',
    pleaseSelectAFile: 'Välj en fil',
    pleaseEnterChatter: 'Skriv någonting som inlägg',
    toggleBetweenHTML: 'Visa/Dölj HTML-kod',
    attachAFile: 'Bifoga en Fil',
    addAPhoto: 'Lägg till en Bild',
    insertALink: 'Infoga en Länk',
    changeTextSize: 'Ändra Textstorlek',
    makeABulletedList: 'Gör en Punktlista',
    makeANumberedList: 'Gör en Numrerad Lista',
    crossOutText: 'Stryk över Text',
    underlineText: 'Stryk under Text',
    italicizeText: 'Kursivera Text',
    boldText: 'Fet Text',
    letMeApproveChatters: 'Låt mig godkänna inlägg innan de anslås?',
    noPostChattersImmediately: 'Nej – anslå inlägg omedelbart',
    yesApproveChattersFirst: 'Ja – godkänn inlägg först',
    yourCommentMustBeApproved: 'Ditt inlägg måste godkännas innan alla kan se det.',
    reallyDeleteThisPost: 'Vill du verkligen ta bort denna post?',
    commentWall: 'kommentarplank',
    commentWallNComments: function(n) {
        switch(n) {
            case 1: return 'Kommentarplank (1 inlägg) ';
            default: return 'Kommentarplank (' + n + ' inlägg) ';
        }
    },
    display: 'Bildskärm',
    from: 'Från',
    show: 'Visa',
    rows: 'rader',
    posts: 'poster'
});


dojo.evalObjPath('xg.shared.nls', true);
dojo.lang.mixin(xg.shared.nls, xg.index.i18n, {
    uploadAPhoto: 'Ladda upp en Bild',
    uploadAnImage: 'Ladda upp en bild',
    uploadAPhotoEllipsis: 'Ladda upp en Bild…',
    useExistingImage: 'Använd existerande bild:',
    existingImage: 'Existerande Bild',
    useThemeImage: 'Använd temabild:',
    themeImage: 'Temabild',
    noImage: 'Ingen bild',
    uploadImageFromComputer: 'Ladda upp en bild från din dator',
    tileThisImage: 'Lägg denna bild sida vid sida',
    done: 'Klart',
    currentImage: 'Aktuell bild',
    pickAColor: 'Välj en färg…',
    openColorPicker: 'Öppna färgväljare',
    loading: 'Laddar…',
    ok: 'OK',
    save: 'Spara',
    cancel: 'Avbryt',
    saving: 'Sparar…',
    addAnImage: 'Lägg till en Bild',
    bold: 'Fet',
    italic: 'Kursiv',
    underline: 'Understruken',
    strikethrough: 'Genomstruken',
    addHyperink: 'Lägg till Hyperlänk',
    options: 'alternativ',
    wrapTextAroundImage: 'Lägg texten runt bilden?',
    imageOnLeft: 'Bilden till vänster?',
    imageOnRight: 'Bilden till höger?',
    createThumbnail: 'Skapa miniatyr?',
    pixels: 'bildpunkter',
    createSmallerVersion: 'Skapa en mindre kopia av din bild som visas upp.  Ställ in bredden i bildpunkter.',
    popupWindow: 'Popupp-fönster?',
    linkToFullSize: 'Länka till  fullskaleversionen av bilden i ett popupp-fönster.',
    add: 'Lägg till',
    keepWindowOpen: 'Håll detta webbläsarfönster öppet medan uppladdningen pågår.',
    cancelUpload: 'Avbryt Uppladdning',
    pleaseSelectAFile: 'Välj en Bildfil',
    pleaseSpecifyAThumbnailSize: 'Ange en miniatyrstorlek',
    thumbnailSizeMustBeNumber: 'Miniatyrstorleken måste vara en siffra',
    addExistingImage: 'eller infoga en existerande bild',
    clickToEdit: 'Klicka för att ändra',
    sendingFriendRequest: 'Sänder Vänförfrågan',
    requestSent: 'Begäran Avsänd!',
    pleaseCorrectErrors: 'Korrigera dessa fel',
    tagThis: 'Tagga Detta',
    addOrEditYourTags: 'Lägg till eller ändra dina taggar:',
    addYourRating: 'Lägg till din klassning:',
    separateMultipleTagsWithCommas: 'Separera multipla taggar med kommatecken, t.ex. cool, ”Nya Zealand”',
    saved: 'Sparat!',
    noo: 'NY',
    none: 'INGEN',
    joinNow: 'Gå med Nu',
    join: 'Gå med',
    youHaventRated: 'Du har ännu inte klassat denna artikel.',
    yourRatedThis: function(n) {
        switch(n) {
            case 1: return 'Du klassade denna artikel med 1 stjärna. ';
            default: return 'Du klassade denna artikel med ' + n + ' stjärnor. ';
        }
    },
    yourRatingHasBeenAdded: 'Din klassning har lagts till.',
    thereWasAnErrorRating: 'Ett fel uppstod vid klassning av detta innehåll.',
    yourTagsHaveBeenAdded: 'Dina taggar har lagts till.',
    thereWasAnErrorTagging: 'Ett fel uppstod när taggarna lades till.',
    addToFavorites: 'Lägg till i Favoriter',
    removeFromFavorites: 'Ta bort från Favoriter',
    nStarsOutOfM: function(n,m) {
        switch(n) {
            case 1: return '1 stjärna av ' + m;
            default: return n + ' stjärnor av ' + m;
        }
    },
    follow: 'Följ',
    stopFollowing: 'Sluta Följa',
    pendingPromptTitle: 'Medlemskap Väntar på Godkännande',
    youCanDoThis: 'Du kan göra detta när ditt medlemskap väl har godkänts av administratörerna.',
    pleaseEnterAComment: 'Gör en kommentar',
    pleaseEnterAFileAddress: 'Ange filadressen',
    pleaseEnterAWebsite: 'Ange en webbadress'
});


dojo.evalObjPath('xg.video.nls', true);
dojo.lang.mixin(xg.video.nls, xg.index.i18n, {
    edit: 'Ändra',
    display: 'Bildskärm',
    detail: 'Detalj',
    player: 'Spelare',
    from: 'Från',
    show: 'Visa',
    videos: 'videor',
    cancel: 'Avbryt',
    save: 'Spara',
    saving: 'Sparar…',
    deleteThisVideo: 'Ta bort denna video?',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Antalet tecken (' + n + ') överskrider det maximalt tillåtna (' + maximum + ') '; },
    weCouldNotLookUpAddress: function(address) { return 'Vi kunde tyvärr inte hitta adressen "' + address + '". '; },
    approve: 'Godkänn',
    approving: 'Godkänner…',
    keepWindowOpenWhileApproving: 'Håll detta webbläsarfönster öppet medan videor godkänns.  Denna bearbetning kan ta några minuter.',
    'delete': 'Ta bort',
    deleting: 'Tar bort…',
    keepWindowOpenWhileDeleting: 'Håll detta webbläsarfönster öppet medan videor tas bort.  Denna bearbetning kan ta några minuter.',
    pasteInEmbedCode: 'Klistra in den inbäddade koden för en video från en annan plats.',
    pleaseSelectVideoToUpload: 'Välj en video att ladda upp.',
    embedCodeContainsMoreThanOneVideo: 'Den inbäddade koden innehåller mer än en video.  Se till att den endast har en  <object> och/eller <embed> tagg.',
    embedCodeMissingTag: 'Den inbäddade koden saknar en &lt; embed&gt;  eller &lt; object&gt;  tagg.',
    fileIsNotAMov: 'Denna fil tycks inte vara en . mov, . mpg, . mp4, . avi, . 3gp eller . wmv.  Försök att ladda upp den ändå?',
    pleaseEnterAComment: 'Skriv ett inlägg.',
    youRatedVideoNStars: function(n) {
        switch(n) {
            case 1: return 'Du klassade denna video med 1 stjärna! ';
            default: return 'Du klassade denna video med ' + n + ' stjärnor! ';
        }
    },
    deleteThisComment: 'Ta bort detta inlägg?',
    embedHTMLCode: 'HTML inbäddad kod:',
    copyHTMLCode: 'Kopiera HTML-kod'
});