dojo.provide('xg.shared.messagecatalogs.nl_NL');

dojo.require('xg.index.i18n');

/**
 * Texts for the Dutch (the Netherlands) locale.
 */
// Use UTF-8 byte sequences instead of HTML entities, e.g., & instead of &amp;, … instead of &hellip;  [Jon Aquino 2007-01-10]


dojo.evalObjPath('xg.feed.nls', true);
dojo.lang.mixin(xg.feed.nls, xg.index.i18n, {
    edit: 'Bewerken',
    title: 'Titel:',
    feedUrl: 'URL:',
    show: 'Weergeven:',
    titles: 'Alleen titels',
    titlesAndDescriptions: 'Details',
    display: 'Weergave',
    cancel: 'Annuleren',
    save: 'Opslaan',
    loading: 'Bezig met laden...',
    items: '"items, artikel"'
});


dojo.evalObjPath('xg.forum.nls', true);
dojo.lang.mixin(xg.forum.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Het aantal tekens (' + n + ') is groter dan het maximum (' + maximum + ') '; },
    pleaseEnterFirstPost: 'Schrijf het eerste bericht voor de discussie',
    pleaseEnterTitle: 'Voer een titel in voor de discussie',
    save: 'Opslaan',
    cancel: 'Annuleren',
    yes: 'Ja',
    no: 'Nee',
    edit: 'Bewerken',
    deleteCategory: 'Categorie verwijderen',
    discussionsWillBeDeleted: 'De discussies in deze categorie worden verwijderd.',
    whatDoWithDiscussions: 'Wat wilt u doen met de discussies in deze categorie?',
    moveDiscussionsTo: 'Discussies verplaatsen naar:',
    moveToCategory: 'Bezig met verplaatsen naar categorie…',
    deleteDiscussions: 'Discussies verwijderen',
    'delete': 'Verwijderen',
    deleteReply: 'Reactie wissen',
    deleteReplyQ: 'Deze reactie  verwijderen?',
    deletingReplies: 'Bezig met het verwijderen van reacties…',
    doYouWantToRemoveReplies: 'Wilt u ook de reacties op dit commentaar verwijderen?',
    pleaseKeepWindowOpen: 'Houd dit browservenster open tot de verwerking voltooid is. Dit kan enige minuten duren.',
    from: 'Van',
    show: 'Weergeven',
    discussions: 'discussies',
    discussionsFromACategory: 'Discussies uit een categorie…',
    display: 'Weergave',
    items: 'items',
    view: 'Weergave'
});


dojo.evalObjPath('xg.groups.nls', true);
dojo.lang.mixin(xg.groups.nls, xg.index.i18n, {
    pleaseChooseAName: 'Kies een naam voor je groep.',
    pleaseChooseAUrl: 'Kies een web-adres voor je groep.',
    urlCanContainOnlyLetters: 'Het webadres mag alleen letters en cijfers bevatten (geen spaties).',
    descriptionTooLong: function(n, maximum) { return 'De lengte van de beschrijving van je groep (' + n + ') is groter dan het maximum (' + maximum + ') '; },
    nameTaken: 'Helaas - die naam is al bezet. Kies een andere naam.',
    urlTaken: 'Helaas - dat webadres is al bezet. Kies een ander webadres.',
    whyNot: 'Waarom niet?',
    groupCreatorDetermines: function(href) { return 'Degene die de groep heeft gemaakt, bepaalt wie er mag meedoen. Als je denkt dat je ten onrechte bent geblokkeerd, <a ' + href + '>neem dan contact op met de initiatiefnemer van de groep</a> '; },
    edit: 'Bewerken',
    from: 'Van',
    show: 'Weergeven',
    groups: 'groepen',
    pleaseEnterName: 'Voer je naam in',
    pleaseEnterEmailAddress: 'Voer je e-mailadres in',
    xIsNotValidEmailAddress: function(x) { return x + ' is not a valid email address'; },
    save: 'Opslaan',
    cancel: 'Annuleren'
});


dojo.evalObjPath('xg.html.nls', true);
dojo.lang.mixin(xg.html.nls, xg.index.i18n, {
    contentsTooLong: function(maximum) { return 'De inhoud is te lang. Gebruik minder dan ' + maximum + ' lettertekens. '; },
    edit: 'Bewerken',
    save: 'Opslaan',
    cancel: 'Annuleren',
    saving: 'Bezig met opslaan...',
    addAWidget: function(url) { return '<a href="' + url + '">Voeg een widget toe</a> aan dit tekstvak '; }
});


dojo.evalObjPath('xg.index.nls', true);
dojo.lang.mixin(xg.index.nls, xg.index.i18n, {
    sendInvitation: 'Uitnodiging verzenden',
    sendInvitationToNFriends: function(n) {
        switch(n) {
            case 1: return 'Een uitnodiging verzenden naar 1 vriend? ';
            default: return 'Een uitnodiging verzenden naar ' + n + ' vrienden? ';
        }
    },
    yourMessageOptional: '<label>Jouw bericht</label> (optioneel)',
    pleaseChoosePeople: 'Kies mensen uit die je wilt uitnodigen.',
    pleaseEnterEmailAddress: 'Voer je emailadres in',
    pleaseEnterPassword: function(emailAddress) { return 'Geef je wachtwoord voor ' + emailAddress + '. '; },
    sorryWeDoNotSupport: 'Sorry, maar we bieden geen ondersteuning voor het webadresboek voor jouw e-mailadres. Klik op \'E-mailtoepassing\' hieronder als je wilt proberen adressen vanaf je computer te gebruiken.',
    sorryWeDontSupport: 'Sorry, maar we bieden geen ondersteuning voor het webadresboek voor jouw e-mailadres. Klik op \'E-mailtoepassing\' hieronder als je wilt proberen adressen vanaf je computer te gebruiken.',
    pleaseSelectSecondPart: 'Selecteer het tweede gedeelte van je e-mailadres, bijv. gmail.com.',
    atSymbolNotAllowed: 'Zorg ervoor dat het teken @ niet voorkomt in het eerste deel van het e-mailadres.',
    resetTextQ: 'Tekst herstellen?',
    resetTextToOriginalVersion: 'Weet je zeker dat je al je tekst wilt herstellen naar de oorspronkelijke versie? Al je wijzigingen gaan verloren.',
    changeQuestionsToPublic: 'Vragen wijzigen in openbaar?',
    changingPrivateQuestionsToPublic: 'Wanneer je privé-vragen wijzigt in openbare vragen, worden alle antwoorden van leden ook openbaar gemaakt. Weet je het zeker?',
    youHaveUnsavedChanges: 'Er zijn wijzigingen die je nog niet hebt opgeslagen.',
    pleaseEnterASiteName: 'Voer een naam in voor de community, bijv. KleineKlownsKlup',
    pleaseEnterShorterSiteName: 'Voer een kortere naam in (max. 64 tekens)',
    pleaseEnterShorterSiteDescription: 'Voer een kortere beschrijving in (max. 140 tekens)',
    siteNameHasInvalidCharacters: 'Er zitten ongeldige tekens in de naam',
    thereIsAProblem: 'Er is een probleem met je gegevens',
    thisSiteIsOnline: 'Deze community is online',
    onlineSiteCanBeViewed: '<strong>Online</strong> - De community kan bekeken worden voor zover in overeenstemming met je privacy-instellingen.',
    takeOffline: 'Offline halen',
    thisSiteIsOffline: 'Deze community is offline',
    offlineOnlyYouCanView: '<strong>Offline</strong> - Alleen jij kunt deze community bekijken.',
    takeOnline: 'Online plaatsen',
    themeSettings: 'Thema-instellingen',
    addYourOwnCss: 'Geavanceerd',
    error: 'Fout',
    pleaseEnterTitleForFeature: function(displayName) { return 'Voer een titel in voor de functie ' + displayName + ' '; },
    thereIsAProblemWithTheInformation: 'Er is een probleem met de ingevoerde gegevens',
    photos: 'Foto\'s',
    videos: 'Video\'s',
    pleaseEnterTheChoicesFor: function(questionTitle) { return 'Voer de keuzemogelijkheden in voor "' + questionTitle + '", bijv. Wandelen, Lezen, Winkelen '; },
    pleaseEnterTheChoices: 'Voer de keuzemogelijkheden in, bijv. Wandelen, Lezen, Winkelen',
    shareWithFriends: 'Delen met vrienden',
    email: 'e-mail',
    separateMultipleAddresses: 'Scheid meerdere adressen door middel van komma\'s',
    subject: 'Onderwerp',
    message: 'Bericht',
    send: 'Verzenden',
    cancel: 'Annuleren',
    pleaseEnterAValidEmail: 'Voer een geldig e-mailadres in',
    go: 'Gaan!',
    areYouSureYouWant: 'Weet je zeker dat je dit wilt doen?',
    processing: 'Bezig met verwerken…',
    pleaseKeepWindowOpen: 'Houd dit browservenster open tot de verwerking voltooid is. Dit kan enige minuten duren.',
    complete: 'Klaar!',
    processIsComplete: 'Verwerking voltooid.',
    ok: 'OK',
    body: 'Tekst van het bericht',
    pleaseEnterASubject: 'Voer een onderwerp in',
    pleaseEnterAMessage: 'Voer een bericht in',
    thereHasBeenAnError: 'Er is iets misgegaan',
    fileNotFound: 'Bestand niet gevonden',
    pleaseProvideADescription: 'Geef een beschrijving',
    pleaseEnterYourFriendsAddresses: 'Voer de adressen of Ning ID\'s van je vrienden in',
    pleaseEnterSomeFeedback: 'Geef commentaar',
    title: 'Titel:',
    setAsMainSiteFeature: 'Instellen als hoofdfunctie',
    thisIsTheMainSiteFeature: 'Dit is de hoofdfunctie',
    customized: 'Aangepast',
    copyHtmlCode: 'HTML-code kopiëren',
    playerSize: 'Formaat van speler',
    selectSource: 'Selecteer bron',
    myAlbums: 'Mijn albums',
    myMusic: 'Mijn muziek',
    showPlaylist: 'Afspeellijst weergeven',
    change: 'Wijzigen',
    changing: 'Bezig met wijzigen...',
    changePrivacy: 'Privacy-instellingen wijzigen?',
    keepWindowOpenWhileChanging: 'Houd dit browservenster open tot de privacyinstellingen gewijzigd zijn. Dit kan enige minuten in beslag nemen.',
    htmlNotAllowed: 'HTML niet toegestaan',
    showingNFriends: function(n, searchString) {
        switch(n) {
            case 1: return '1 vriend die voldoet aan "' + searchString + '" wordt weergegeven. <a href="#">Iedereen weergeven</a> ';
            default: return '' + n + ' vrienden die voldoen aan "' + searchString + '" worden weergegeven. <a href="#">Iedereen weergeven</a> ';
        }
    },
    sendMessage: 'Stuur bericht',
    sendMessageToNFriends: function(n) {
        switch(n) {
            case 1: return 'Bericht sturen aan 1 vriend? ';
            default: return 'Bericht sturen aan ' + n + ' vrienden? ';
        }
    },
    invitingNFriends: function(n) {
        switch(n) {
            case 1: return '1 vriend wordt uitgenodigd... ';
            default: return '' + n + ' vrienden worden uitgenodigd... ';
        }
    },
    nFriends: function(n) {
        switch(n) {
            case 1: return '1 vriend... ';
            default: return n + ' vrienden... ';
        }
    },
    sendingMessageToNFriends: function(n) {
        switch(n) {
            case 1: return 'Bericht wordt verstuurd aan 1 vriend... ';
            default: return 'Bericht wordt verstuurd aan ' + n + ' vrienden... ';
        }
    },
    noPeopleSelected: 'Niemand geselecteerd',
    pleaseChooseFriends: 'Selecteer enkele vrienden voordat je je bericht verstuurt.',
    myVideos: 'Mijn video\'s',
    noFriendsFound: 'Geen vrienden gevonden die aan je criteria voldoen.',
    subjectIsTooLong: function(n) { return 'Onderwerp is te lang, gebruik '+n+' tekens of minder.'; },
    addingInstructions: 'Laat dit venster open terwijl je inhoud wordt toegevoegd.',
    addingLabel: 'Bezig met toevoegen...',
    cannotKeepFiles: 'Als je meer opties wilt bekijken, moet je je bestanden opnieuw selecteren.  Wil je verder gaan?',
    done: 'Klaar',
    looksLikeNotImage: 'Een of meer bestanden lijken niet de indeling .jpg, .gif of .png te hebben.  Wil je toch proberen te uploaden?',
    looksLikeNotMusic: 'Het bestand dat je hebt geselecteerd lijkt niet de indeling .mp3 te hebben.  Wil je toch proberen te uploaden?',
    looksLikeNotVideo: 'Het bestand dat je hebt geselecteerd lijkt niet de indeling .mov, .mpg, .mp4, .avi, .3gp of .wmv te hebben.  Wil je toch proberen te uploaden?',
    messageIsTooLong: function(n) { return 'Onderwerp is te lang, gebruik '+n+' tekens of minder.'; },
    pleaseSelectPhotoToUpload: 'Kies een foto die je wilt uploaden.',
    processingFailed: 'Verwerking is helaas mislukt. Probeer het later nog eens.',
    selectOrPaste: 'Je moet een video selecteren of de \'embed\'-code plakken.',
    selectOrPasteMusic: 'Je moet een liedje selecteren of het webadres plakken.',
    sendingLabel: 'Bezig met verzenden…',
    thereWasAProblem: 'Er was een probleem bij het toevoegen van je inhoud.  Probeer het later nog eens.',
    uploadingInstructions: 'Laat dit venster open zolang het uploaden bezig is',
    uploadingLabel: 'Bezig met uploaden...',
    youNeedToAddEmailRecipient: 'Je moet een e-mailontvanger toevoegen.',
    yourMessage: 'Jouw bericht',
    yourMessageIsBeingSent: 'Je bericht is verzonden.',
    yourSubject: 'Onderwerp'
});


dojo.evalObjPath('xg.music.nls', true);
dojo.lang.mixin(xg.music.nls, xg.index.i18n, {
    shufflePlaylist: 'Willekeurige afspeellijst',
    play: 'afspelen',
    pleaseSelectTrackToUpload: 'Kies een liedje dat je wilt uploaden.',
    pleaseEnterTrackLink: 'Geef de URL van een liedje.',
    thereAreUnsavedChanges: 'Er zijn wijzigingen die nog niet zijn opgeslagen.',
    autoplay: 'Automatisch afspelen',
    showPlaylist: 'Afspeellijst weergeven',
    playLabel: 'Afspelen',
    url: 'URL',
    rssXspfOrM3u: 'rss, xspf of m3u',
    save: 'Opslaan',
    cancel: 'Annuleren',
    edit: 'Bewerken',
    fileIsNotAnMp3: 'Een van deze bestanden lijkt geen MP3 te zijn. Toch proberen te uploaden?',
    entryNotAUrl: 'Een van deze onderdelen lijkt geen URL te zijn. Zorg ervoor dat alle onderdelen beginnen met <kbd>http://</kbd>'
});


dojo.evalObjPath('xg.page.nls', true);
dojo.lang.mixin(xg.page.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Het aantal tekens (' + n + ') is groter dan het maximum (' + maximum + ') '; },
    pleaseEnterContent: 'Voer de inhoud van de pagina in',
    pleaseEnterTitle: 'Voer een titel in voor de pagina',
    pleaseEnterAComment: 'Voer een commentaar in',
    deleteThisComment: 'Weet je zeker dat je dit commentaar wilt verwijderen?',
    save: 'Opslaan',
    cancel: 'Annuleren',
    discussionTitle: 'Paginatitel:',
    tags: 'Tags:',
    edit: 'Bewerken',
    close: 'Sluiten',
    displayPagePosts: 'Berichten van pagina weergeven',
    directory: 'Directory',
    displayTab: 'Tab weergeven',
    addAnotherPage: 'Nog een pagina toevoegen',
    tabText: 'Tekst tab',
    urlDirectory: 'Directory URL',
    displayTabForPage: 'Of een tab voor de pagina moet worden weergegeven',
    tabTitle: 'Titel tab',
    remove: 'Verwijderen',
    thereIsAProblem: 'Er is een probleem met je informatie'
});


dojo.evalObjPath('xg.photo.nls', true);
dojo.lang.mixin(xg.photo.nls, xg.index.i18n, {
    randomOrder: 'Willekeurige volgorde',
    untitled: 'Zonder titel',
    photos: 'Foto\'s',
    edit: 'Bewerken',
    photosFromAnAlbum: 'Albums',
    show: 'Weergeven',
    rows: 'rijen',
    cancel: 'Annuleren',
    save: 'Opslaan',
    deleteThisPhoto: 'Deze foto verwijderen?',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Het aantal tekens (' + n + ') is groter dan het maximum (' + maximum + ') '; },
    weCouldNotLookUpAddress: function(address) { return 'Sorry, maar we konden het adres "' + address + '" niet vinden. '; },
    pleaseSelectPhotoToUpload: 'Kies een foto die je wilt uploaden.',
    pleaseEnterAComment: 'Voer een commentaar in.',
    addToExistingAlbum: 'Toevoegen aan bestaand album',
    addToNewAlbumTitled: 'Toevoegen aan een nieuw album met als titel…',
    deleteThisComment: 'Dit commentaar verwijderen?',
    importingNofMPhotos: function(n,m) { return 'Foto <span id="currentP">' + n + '</span> van ' + m + ' wordt geïmporteerd. '; },
    starting: 'Klaar voor de start…',
    done: 'Klaar!',
    from: 'Van',
    display: 'Weergave',
    anErrorOccurred: 'Er is helaas een fout opgetreden. Maak alsjeblieft melding van dit probleem met de koppeling onder aan deze pagina.',
    weCouldntFind: 'Geen foto\'s gevonden! Probeer eens een van de andere opties.',
    takingYou: 'We laten je jouw foto\'s zien...'
});


dojo.evalObjPath('xg.activity.nls', true);
dojo.lang.mixin(xg.activity.nls, xg.index.i18n, {
    edit: 'Bewerken',
    show: 'Weergeven',
    events: 'gebeurtenissen',
    setWhatActivityGetsDisplayed: 'Instellen welke activiteiten worden weergegeven',
    save: 'Opslaan',
    cancel: 'Annuleren'
});


dojo.evalObjPath('xg.profiles.nls', true);
dojo.lang.mixin(xg.profiles.nls, xg.index.i18n, {
    pleaseEnterValueForPost: 'Voer een waarde in voor het bericht',
    pleaseProvideAValidDate: 'Geef een geldige datum',
    uploadAFile: 'Een bestand uploaden',
    pleaseEnterUrlOfLink: 'Voer de URL van de koppeling in:',
    pleaseEnterTextOfLink: 'Naar welke tekst wil je verwijzen?',
    edit: 'Bewerken',
    recentlyAdded: 'Onlangs toegevoegd',
    featured: 'Op hoofdpagina',
    iHaveRecentlyAdded: 'Ik heb onlangs toegevoegd',
    fromTheSite: 'Van de community',
    cancel: 'Annuleren',
    save: 'Opslaan',
    loading: 'Bezig met laden...',
    addAsFriend: 'Als vriend toevoegen',
    removeAsFriend: 'Als vriend verwijderen',
    requestSent: 'Verzoek verzonden!',
    sendingFriendRequest: 'Verzoek om vriend te worden wordt verzonden',
    thisIsYou: 'Dit ben jij!',
    isYourFriend: 'Is je vriend',
    isBlocked: 'Is geblokkeerd',
    pleaseEnterAComment: 'Voer een commentaar in',
    pleaseEnterPostBody: 'Voer de hoofdtekst van het bericht in',
    pleaseSelectAFile: 'Selecteer een bestand',
    pleaseEnterChatter: 'Voer je commentaar in',
    toggleBetweenHTML: 'HTML-code weergeven/verbergen',
    attachAFile: 'Een bestand toevoegen',
    addAPhoto: 'Een foto toevoegen',
    insertALink: 'Een koppeling invoegen',
    changeTextSize: 'De tekstgrootte wijzigen',
    makeABulletedList: 'Een lijst met opsommingstekens maken',
    makeANumberedList: 'Een genummerde lijst maken',
    crossOutText: 'Tekst doorstrepen',
    underlineText: 'Tekst onderstrepen',
    italicizeText: 'Tekst cursief maken',
    boldText: 'Tekst vet maken',
    letMeApproveChatters: 'Wil je commentaar eerst goedkeuren voor het gepost wordt?',
    noPostChattersImmediately: 'Nee, commentaar direct plaatsen',
    yesApproveChattersFirst: 'Ja, ik wil commentaar eerst goedkeuren',
    yourCommentMustBeApproved: 'Je commentaar moet eerst goedgekeurd worden voordat iedereen het kan zien.',
    reallyDeleteThisPost: 'Dit bericht werkelijk verwijderen?',
    commentWall: 'Prikbord',
    commentWallNComments: function(n) {
        switch(n) {
            case 1: return 'Prikbord (1 commentaar) ';
            default: return 'Prikbord (' + n + ' commentaren) ';
        }
    },
    display: 'Weergave',
    from: 'Van',
    show: 'Weergeven',
    rows: 'rijen',
    posts: 'berichten',
    returnToDefaultWarning: 'Dit zet alle mogelijkheden en het thema van Mijn Pagina terug naar de standaardinstelling van het netwerk. Wil je doorgaan?',
    networkError: 'Netwerkfout',
    wereSorry: 'We hebben je nieuwe lay-out niet kunnen opslaan. Waarschijnlijk komt dit doordat de internetverbinding is verbroken. Controleer je verbinding en probeer het nog eens.'
});


dojo.evalObjPath('xg.shared.nls', true);
dojo.lang.mixin(xg.shared.nls, xg.index.i18n, {
    removeFriendTitle: 'Als vriend verwijderen?',
    removeAsFriend: 'Als vriend verwijderen',
    removeFriendConfirm: 'Weet je zeker dat je deze persoon als vriend wilt verwijderen?',
    locationNotFound: function(location) { return '<em>' + location + '</em> niet gevonden. '; },
    confirmation: 'Bevestiging',
    showMap: 'Kaart weergeven',
    hideMap: 'Kaart verbergen',
    yourCommentMustBeApproved: 'Je reactie moet eerst goedgekeurd worden voordat iedereen het kan zien.',
    nComments: function(n) {
	    switch(n) {
	        case 1: return '1 reactie ';
	        default: return n + ' Reacties ';
	    }
	},
    uploadAFile: 'Een bestand uploaden',
    addExistingFile: 'of voeg een bestaand bestand in',
    uploadAPhoto: 'Een foto uploaden',
    uploadAnImage: 'Een afbeelding uploaden',
    uploadAPhotoEllipsis: 'Een foto uploaden…',
    useExistingImage: 'Bestaande afbeelding gebruiken:',
    existingImage: 'Bestaande afbeelding',
    useThemeImage: 'Thema-afbeelding gebruiken:',
    themeImage: 'Thema-afbeelding',
    noImage: 'Geen afbeelding',
    uploadImageFromComputer: 'Een afbeelding uploaden vanaf je computer',
    tileThisImage: 'Deze afbeelding steeds herhalen',
    done: 'Klaar',
    currentImage: 'Huidige afbeelding',
    pickAColor: 'Kies een kleur…',
    openColorPicker: 'Open het kleurenpalet',
    loading: 'Bezig met laden...',
    ok: 'OK',
    save: 'Opslaan',
    cancel: 'Annuleren',
    saving: 'Bezig met opslaan...',
    addAnImage: 'Een afbeelding toevoegen',
    bold: 'Vet',
    italic: 'Cursief',
    underline: 'Onderstrepen',
    strikethrough: 'Doorhalen',
    addHyperink: 'Koppeling toevoegen',
    options: 'Opties',
    wrapTextAroundImage: 'Tekst om afbeelding laten lopen?',
    imageOnLeft: 'Afbeelding links?',
    imageOnRight: 'Afbeelding rechts?',
    createThumbnail: 'Miniatuur maken?',
    pixels: 'pixels',
    createSmallerVersion: 'Maak een kleinere versie van je afbeelding voor weergave. Stel de breedte in pixels in.',
    popupWindow: 'Pop-upvenster?',
    linkToFullSize: 'Maak een koppeling naar de versie op ware grootte in een pop-upvenster.',
    add: 'Toevoegen',
    keepWindowOpen: 'Houd dit browservenster open terwijl de upload bezig is.',
    cancelUpload: 'Uploaden annuleren',
    pleaseSelectAFile: 'Selecteer een afbeeldingsbestand',
    pleaseSpecifyAThumbnailSize: 'Geef een formaat op voor de miniatuur',
    thumbnailSizeMustBeNumber: 'Het formaat voor de miniatuur moet een getal zijn',
    addExistingImage: 'of voeg een bestaande afbeelding in',
    clickToEdit: 'Klik om te bewerken',
    sendingFriendRequest: 'Verzoek om vriend te worden wordt verzonden',
    requestSent: 'Verzoek verzonden!',
    pleaseCorrectErrors: 'Corrigeer alsjeblieft deze fouten',
    tagThis: 'Voeg hieraan een tag toe',
    addOrEditYourTags: 'Je tags toevoegen of bewerken:',
    addYourRating: 'Je waardering toevoegen:',
    saved: 'Opgeslagen!',
    noo: 'NIEUW',
    none: 'GEEN',
    joinNow: 'Nu deelnemen',
    join: 'Deelnemen',
    youHaventRated: 'Je hebt nog geen waardering gegeven voor dit onderdeel.',
    yourRatedThis: function(n) {
        switch(n) {
            case 1: return 'Je hebt dit onderdeel gewaardeerd met 1 ster. ';
            default: return 'Je hebt dit onderdeel gewaardeerd met ' + n + ' sterren. ';
        }
    },
    yourRatingHasBeenAdded: 'Je waardering is toegevoegd.',
    thereWasAnErrorRating: 'Er is een fout opgetreden bij het waarderen van dit onderdeel.',
    yourTagsHaveBeenAdded: 'Je tags zijn toegevoegd.',
    thereWasAnErrorTagging: 'Er is een fout opgetreden bij het toevoegen van tags.',
    addToFavorites: 'Toevoegen aan favorieten',
    removeFromFavorites: 'Verwijderen uit Favorieten',
    nStarsOutOfM: function(n,m) {
        switch(n) {
            case 1: return '1 ster van ' + m;
            default: return n + ' sterren van ' + m;
        }
    },
    follow: 'Volgen',
    stopFollowing: 'Stoppen met volgen',
    pendingPromptTitle: 'Je lidmaatschapsaanvraag is in behandeling',
    youCanDoThis: 'Je kunt dit doen als je lidmaatschapsaanvraag is goedgekeurd door de systeembeheerders.',
    separateMultipleTagsWithCommas: 'Afzonderlijke meervoudige tags met comma\'s bijv. cool, "Nieuw Zeeland"',
    yourMessage: 'Jouw bericht',
    updateMessage: 'Bericht bijwerken',
    updateMessageQ: 'Bericht bijwerken?',
    removeWords: 'Om ervoor te zorgen dat de e-mail ook echt wordt afgeleverd, raden we aan om terug te gaan of de volgende woorden te veranderen:',
    warningMessage: 'Het is mogelijk dat er woorden in deze e-mail staan waardoor deze in de Spam-map verdwijnt.',
    errorMessage: 'Er zijn tenminste 6 woorden in deze e-mail waardoor deze mogelijk in de Spam-map verdwijnt.',
    goBack: 'Ga terug',
    sendAnyway: 'Toch versturen',
    messageIsTooLong: function(n,m) { return 'Helaas. Het maximum aantal tekens is '+m+'.' },
    editYourTags: 'Tags bewerken',
    addTags: 'Tags toevoegen',
    editLocation: 'Locatie bewerken',
    editTypes: 'Soort gebeurtenis bewerken',
    pleaseEnterAComment: 'Voer een reactie in',
    pleaseEnterAFileAddress: 'Voer het adres van het bestand in',
    pleaseEnterAWebsite: 'Voer een webadres in'
});


dojo.evalObjPath('xg.video.nls', true);
dojo.lang.mixin(xg.video.nls, xg.index.i18n, {
    edit: 'Bewerken',
    display: 'Weergave',
    detail: 'Detail',
    player: 'Speler',
    from: 'Van',
    show: 'Weergeven',
    videos: 'video\\’s',
    cancel: 'Annuleren',
    save: 'Opslaan',
    saving: 'Bezig met opslaan...',
    deleteThisVideo: 'Deze video verwijderen?',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Het aantal tekens (' + n + ') is groter dan het maximum (' + maximum + ') '; },
    weCouldNotLookUpAddress: function(address) { return 'Sorry, maar we konden het adres "' + address + '" niet vinden. '; },
    approve: 'Goedkeuren',
    approving: 'Bezig met goedkeuren...',
    keepWindowOpenWhileApproving: 'Houd dit browservenster open tot de video\'s goedgekeurd zijn. Dit kan enige tijd duren.',
    'delete': 'Verwijderen',
    deleting: 'Bezig met verwijderen...',
    keepWindowOpenWhileDeleting: 'Houd dit browservenster open tot de video\'s verwijderd zijn. Dit kan enige minuten duren.',
    pasteInEmbedCode: 'Plak de insluitcode (embedded HTML) voor een video vanaf een andere webpagina.',
    pleaseSelectVideoToUpload: 'Kies een video die je wilt uploaden.',
    embedCodeContainsMoreThanOneVideo: 'De insluitcode bevat meer dan één video. Hij mag slechts één <object>- en/of <embed>-tag bevatten.',
    embedCodeMissingTag: 'De insluitcode bevat geen &lt;embed&gt;- of &lt;object&gt;-tag.',
    fileIsNotAMov: 'Dit bestand lijkt geen .mov, .mpg, .mp4, .avi, .3gp of .wmv te zijn. Toch proberen te uploaden?',
    pleaseEnterAComment: 'Voer een commentaar in.',
    youRatedVideoNStars: function(n) {
        switch(n) {
            case 1: return 'Je hebt deze video gewaardeerd met 1 ster! ';
            default: return 'Je hebt deze video gewaardeerd met ' + n + ' sterren! ';
        }
    },
    deleteThisComment: 'Dit commentaar verwijderen?',
    embedHTMLCode: 'Embedded HTML-code:',
    copyHTMLCode: 'HTML-code kopiëren',
    directLink: 'Directe link',
    addToMyspace: 'Aan MySpace toevoegen',
    shareOnFacebook: 'Delen op Facebook',
    addToOthers: 'Toevoegen aan andere'
});


dojo.evalObjPath('xg.gadgets.nls', true);
dojo.lang.mixin(xg.gadgets.nls, xg.index.i18n, {
    edit: 'Bewerken',
    title: 'Titel:',
    feedUrl: 'URL:',
    cancel: 'Annuleren',
    save: 'Opslaan',
    loading: 'Bezig met laden...',
    removeGadget: 'Gadget verwijderen',
    findGadgetsInDirectory: 'Gadgets zoeken in het gadget-overzicht'
});


dojo.evalObjPath('xg.uploader.nls', true);
dojo.lang.mixin(xg.uploader.nls, xg.index.i18n, {
    fileBrowserHeader: 'Mijn computer',
    fileRoot: 'Mijn computer',
    fileInformationHeader: 'Informatie',
    uploadHeader: 'Bestanden voor upload',
    dragOutInstructions: 'Sleep bestanden weg die je wilt verwijderen',
    dragInInstructions: 'Sleep bestanden hierheen',
    selectInstructions: 'Selecteer een bestand',
    files: 'Bestanden',
    totalSize: 'Totale grootte',
    fileName: 'Naam',
    fileSize: 'Grootte',
    nextButton: 'Volgende >',
    okayButton: 'OK',
    yesButton: 'Ja',
    noButton: 'Nee',
    uploadButton: 'Uploaden',
    cancelButton: 'Annuleren',
    backButton: 'Terug',
    continueButton: 'Doorgaan',
    uploadingLabel: 'Bezig met uploaden...',
    uploadingStatus: function(n, m) { return 'Bezig met uploaden van '+ n + ' van ' + m; },
    uploadingInstructions: 'Laat dit venster open zolang het uploaden bezig is',
    uploadLimitWarning: function(n) { return 'Je kunt ' + n + ' bestanden tegelijkertijd uploaden. '; },
    uploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Je hebt het maximumaantal bestanden toegevoegd. ';
            case 1: return 'Je kunt nog 1 bestand uploaden. ';
            default: return 'Je kunt nog ' + n + ' bestanden uploaden. ';
        }
    },
    iHaveTheRight: 'Ik heb het recht om deze bestanden te uploaden volgens de <a href="/main/authorization/termsOfService">Gebruiksvoorwaarden</a>',
    updateJavaTitle: 'Java bijwerken',
    updateJavaDescription: 'Voor de massa-uploader is een recentere versie van Java nodig.  Klik op "OK" om Java op te halen.',
    batchEditorLabel: 'Informatie voor alle onderdelen bewerken',
    applyThisInfo: 'Pas deze informatie toe op onderstaande bestanden',
    titleProperty: 'Titel',
    descriptionProperty: 'Beschrijving',
    tagsProperty: 'Tags',
    viewableByProperty: 'Kan worden bekeken door',
    viewableByEveryone: 'Iedereen',
    viewableByFriends: 'Alleen mijn vrienden',
    viewableByMe: 'Alleen ik',
    albumProperty: 'Album',
    artistProperty: 'Artiest/Auteur',
    enableDownloadLinkProperty: 'Maak koppeling naar download',
    enableProfileUsageProperty: 'Sta toe dat anderen dit bestand op hun eigen site publiceren.',
    licenseProperty: 'Licentie',
    creativeCommonsVersion: '3.0',
    selectLicense: '— Selecteer licentie —',
    copyright: '© Alle rechten voorbehouden.',
    ccByX: function(n) { return 'Creative Commons Naamsvermelding ' + n; },
    ccBySaX: function(n) { return 'Creative Commons Naamsvermelding-GelijkDelen ' + n; },
    ccByNdX: function(n) { return 'Creative Commons Naamsvermelding-GeenAfgeleideWerken ' + n; },
    ccByNcX: function(n) { return 'Creative Commons Naamsvermelding-NietCommercieel ' + n; },
    ccByNcSaX: function(n) { return 'Creative Commons NietCommercieel-GelijkDelen ' + n; },
    ccByNcNdX: function(n) { return 'Creative Commons NietCommercieel-GeenAfgeleideWerken ' + n; },
    publicDomain: 'Rechtenvrij',
    other: 'Overige',
    errorUnexpectedTitle: 'Ojee!',
    errorUnexpectedDescription: 'Er is een fout opgetreden. Probeer het nog eens.',
    errorTooManyTitle: 'Te veel onderdelen',
    errorTooManyDescription: function(n) { return 'Sorry, maar je kunt maar '+ n + ' onderdelen tegelijkertijd uploaden. '; },
    errorNotAMemberTitle: 'Niet toegestaan',
    errorNotAMemberDescription: 'Sorry, maar je moet lid zijn om te mogen uploaden.',
    errorContentTypeNotAllowedTitle: 'Niet toegestaan',
    errorContentTypeNotAllowedDescription: 'Sorry, maar je mag geen inhoud van dit type uploaden.',
    errorUnsupportedFormatTitle: 'Ojee!',
    errorUnsupportedFormatDescription: 'Sorry, maar dit type bestand wordt niet ondersteund.',
    errorUnsupportedFileTitle: 'Ojee!',
    errorUnsupportedFileDescription: 'De indeling van foo.exe wordt niet ondersteund.',
    errorUploadUnexpectedTitle: 'Ojee!',
    errorUploadUnexpectedDescription: function(file) { return file ? ('Mogelijk is er een probleem met het bestand ' + file + '. Verwijder dit uit de lijst voordat je de rest van je bestanden uploadt.') : 'Er is mogelijk een probleem met het bovenste bestand in de lijst. Verwijder dit voordat je de rest van je bestanden uploadt.'; },
    cancelUploadTitle: 'Uploaden annuleren?',
    cancelUploadDescription: 'Weet je zeker dat je de overgebleven uploads wilt annuleren?',
    uploadSuccessfulTitle: 'Upload voltooid',
    uploadSuccessfulDescription: 'Even geduld terwijl we je naar je uploads brengen...',
    uploadPendingDescription: 'Je bestanden zijn correct geüpload en moeten nu nog goedgekeurd worden.',
    photosUploadHeader: 'Foto\'s voor upload',
    photosDragOutInstructions: 'Sleep foto\'s weg die je wilt verwijderen',
    photosDragInInstructions: 'Sleep foto\'s hierheen',
    photosSelectInstructions: 'Selecteer een foto',
    photosFiles: 'Foto\'s',
    photosUploadingStatus: function(n, m) { return 'Bezig met uploaden van foto '+ n + ' van ' + m; },
    photosErrorTooManyTitle: 'Te veel foto\'s',
    photosErrorTooManyDescription: function(n) { return 'Sorry, maar je kunt maar '+ n + ' foto\'s tegelijkertijd uploaden. '; },
    photosErrorContentTypeNotAllowedDescription: 'Sorry, maar het uploaden van foto\'s is uitgeschakeld.',
    photosErrorUnsupportedFormatDescription: 'Sorry, maar je kunt alleen afbeeldingen in .jpg-, .gif- of .png-indeling uploaden.',
    photosErrorUnsupportedFileDescription: function(n) { return n + ' is geen .jpg-, .gif- of .png-bestand.'; },
    photosBatchEditorLabel: 'Informatie voor alle foto\'s bewerken',
    photosApplyThisInfo: 'Pas deze informatie toe op onderstaande foto\'s',
    photosErrorUploadUnexpectedDescription: function(file) { return file ? ('Mogelijk is er een probleem met het bestand ' + file + '. Verwijder dit uit de lijst voordat je de rest van je foto’s uploadt.') : 'Er is mogelijk een probleem met de bovenste foto in de lijst. Verwijder deze voordat je de rest van je foto’s uploadt.'; },
    photosUploadSuccessfulDescription: 'Even geduld terwijl we je naar je foto\'s brengen...',
    photosUploadPendingDescription: 'Je foto\'s zijn correct geüpload en moeten nu nog goedgekeurd worden.',
    photosUploadLimitWarning: function(n) { return 'Je kunt ' + n + ' foto\'s tegelijkertijd uploaden. '; },
    photosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Je hebt het maximumaantal foto\'s toegevoegd. ';
            case 1: return 'Je kunt nog 1 foto uploaden. ';
            default: return 'Je kunt nog ' + n + ' foto\'s uploaden. ';
        }
    },
    photosIHaveTheRight: 'Ik heb het recht om deze foto\'s te uploaden volgens de <a href="/main/authorization/termsOfService">Gebruiksvoorwaarden</a>',
    videosUploadHeader: 'Video\'s voor upload',
    videosDragInInstructions: 'Sleep video\'s hierheen',
    videosDragOutInstructions: 'Sleep video\'s weg die je wilt verwijderen',
    videosSelectInstructions: 'Selecteer een video',
    videosFiles: 'Video\'s',
    videosUploadingStatus: function(n, m) { return 'Bezig met uploaden van video '+ n + ' van ' + m; },
    videosErrorTooManyTitle: 'Te veel video\'s',
    videosErrorTooManyDescription: function(n) { return 'Sorry, maar je kunt maar '+ n + ' video\'s tegelijkertijd uploaden. '; },
    videosErrorContentTypeNotAllowedDescription: 'Sorry, maar het uploaden van video\'s is uitgeschakeld.',
    videosErrorUnsupportedFormatDescription: 'Sorry, maar je kunt alleen video\'s in .avi-, .mov-, .mp4-, .wmv- of .mpg-indeling uploaden.',
    videosErrorUnsupportedFileDescription: function(x) { return x + ' is geen .avi-, .mov-, .mp4-, .wmv- of .mpg-bestand.'; },
    videosBatchEditorLabel: 'Informatie voor alle video\'s bewerken',
    videosApplyThisInfo: 'Pas deze informatie toe op onderstaande video\'s',
    videosErrorUploadUnexpectedDescription: function(file) { return file ? ('Mogelijk is er een probleem met het bestand ' + file + '. Verwijder dit uit de lijst voordat je de rest van je video’s uploadt.') : 'Er is mogelijk een probleem met de bovenste foto in de lijst. Verwijder deze voordat je de rest van je video’s uploadt.'; },
    videosUploadSuccessfulDescription: 'Even geduld terwijl we je naar je video\'s brengen...',
    videosUploadPendingDescription: 'Je video\'s zijn correct geüpload en moeten nu nog goedgekeurd worden.',
    videosUploadLimitWarning: function(n) { return 'Je kunt ' + n + ' video\'s tegelijkertijd uploaden. '; },
    videosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Je hebt het maximumaantal video\'s toegevoegd. ';
            case 1: return 'Je kunt nog 1 video uploaden. ';
            default: return 'Je kunt nog ' + n + ' video\'s uploaden. ';
        }
    },
    videosIHaveTheRight: 'Ik heb het recht om deze video\'s te uploaden volgens de <a href="/main/authorization/termsOfService">Gebruiksvoorwaarden</a>',
    musicUploadHeader: 'Liedjes voor upload',
    musicTitleProperty: 'Titel',
    musicDragOutInstructions: 'Sleep liedjes weg die je wilt verwijderen',
    musicDragInInstructions: 'Sleep liedjes hierheen',
    musicSelectInstructions: 'Selecteer een liedje',
    musicFiles: 'Liedjes',
    musicUploadingStatus: function(n, m) { return 'Bezig met uploaden van liedje '+ n + ' van ' + m; },
    musicErrorTooManyTitle: 'Te veel liedjes',
    musicErrorTooManyDescription: function(n) { return 'Sorry, maar je kunt maar '+ n + ' liedjes tegelijkertijd uploaden. '; },
    musicErrorContentTypeNotAllowedDescription: 'Sorry, maar het uploaden van liedjes is uitgeschakeld.',
    musicErrorUnsupportedFormatDescription: 'Sorry, maar je kunt alleen liedjes in .mp3-indeling uploaden.',
    musicErrorUnsupportedFileDescription: function(x) { return x + ' is geen .mp3-bestand.'; },
    musicBatchEditorLabel: 'Informatie voor alle liedjes bewerken',
    musicApplyThisInfo: 'Pas deze informatie toe op onderstaande liedjes',
    musicErrorUploadUnexpectedDescription: function(file) { return file ? ('Mogelijk is er een probleem met het bestand ' + file + '. Verwijder dit uit de lijst voordat je de rest van je liedjes uploadt.') : 'Er is mogelijk een probleem met de bovenste foto in de lijst. Verwijder deze voordat je de rest van je liedjes uploadt.'; },
    musicUploadSuccessfulDescription: 'Even geduld terwijl we je naar je liedjes brengen...',
    musicUploadPendingDescription: 'Je liedjes zijn correct geüpload en moeten nu nog goedgekeurd worden.',
    musicUploadLimitWarning: function(n) { return 'Je kunt ' + n + ' liedjes tegelijkertijd uploaden. '; },
    musicUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Je hebt het maximumaantal liedjes toegevoegd. ';
            case 1: return 'Je kunt nog 1 liedje uploaden. ';
            default: return 'Je kunt nog ' + n + ' liedjes uploaden. ';
        }
    },
    musicIHaveTheRight: 'Ik heb het recht om deze liedjes te uploaden volgens de <a href="/main/authorization/termsOfService">Gebruiksvoorwaarden</a>'
});


dojo.evalObjPath('xg.events.nls', true);
dojo.lang.mixin(xg.events.nls, xg.index.i18n, {
    messageIsTooLong: function(n) { return 'Je bericht is te lang. Gebruik ten hoogste '+n+' tekens.'; },
    sendMessageToGuests: 'Bericht versturen aan gasten',
    sendMessageToGuestsThat: 'Bericht versturen aan gasten die:',
    areAttending: 'Gaan bijwonen',
    mightAttend: 'Eventueel gaan bijwonen',
    haveNotYetRsvped: 'Nog niet hebben gereageerd',
    areNotAttending: 'Niet gaan bijwonen',
    messageSent: 'Bericht verzonden!',
    chooseRecipient: 'Kies een ontvanger.',
    pleaseChooseImage: 'Kies een afbeelding voor de gebeurtenis',
    pleaseEnterAMessage: 'Voer een bericht in',
    pleaseEnterDescription: 'Voer een omschrijving in van de gebeurtenis',
    pleaseEnterLocation: 'Voer een plaats van de gebeurtenis in',
    pleaseEnterTitle: 'Voer een titel in voor de gebeurtenis',
    pleaseEnterType: 'Voer tenminste een type voor de gebeurtenis in',
    send: 'Versturen',
    sending: 'Bezig met versturen…',
    thereHasBeenAnError: 'Er heeft zich een fout voorgedaan',
    yourMessage: 'Jouw bericht',
    yourMessageHasBeenSent: 'Je bericht is verzonden.',
    yourMessageIsBeingSent: 'Je bericht is verzonden.'
});


dojo.evalObjPath('xg.notes.nls', true);
dojo.lang.mixin(xg.notes.nls, xg.index.i18n, {
    addNewNote: 'Een nieuwe notitie toevoegen',
    noteTitleTooLong: 'Titel notitie is te lang',
    pleaseEnterNoteEntry: 'Voer een notitie in',
    pleaseEnterNoteTitle: 'Voer een titel in voor de notitie!'
});