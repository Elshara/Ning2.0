dojo.provide('xg.shared.messagecatalogs.de_DE');

dojo.require('xg.index.i18n');

/**
 * Texts for the de_DE locale.
 */
// Use UTF-8 byte sequences instead of HTML entities, e.g., & instead of &amp;, … instead of &hellip;  [Jon Aquino 2007-01-10]


dojo.evalObjPath('xg.events.nls', true);
dojo.lang.mixin(xg.events.nls, xg.index.i18n, {
    pleaseChooseImage: 'Bitte suchen Sie ein Bild für dieses Ereignis aus.',
    pleaseEnterAMessage: 'Bitte geben Sie eine Nachricht ein.',
    pleaseEnterDescription: 'Bitte geben Sie eine Beschreibung für die Veranstaltung ein.',
    pleaseEnterLocation: 'Bitte geben Sie einen Standort für die Veranstaltung ein.',
    pleaseEnterTitle: 'Bitte geben Sie einen Titel für die Veranstaltung ein.',
    pleaseEnterType: 'Geben Sie mindestens einen Typ für die Veranstaltung ein.',
    send: 'Senden',
    sending: 'Senden…',
    thereHasBeenAnError: 'Fehler',
    yourMessage: 'Ihre Nachricht',
    yourMessageHasBeenSent: 'Ihre Nachricht wurde gesendet.',
    yourMessageIsBeingSent: 'Ihre Nachricht wird gesendet.'    
});

dojo.evalObjPath('xg.feed.nls', true);
dojo.lang.mixin(xg.feed.nls, xg.index.i18n, {
    edit: 'Bearbeiten',
    title: 'Titel:',
    feedUrl: 'URL:',
    show: 'Einblenden:',
    titles: 'Nur Titel',
    titlesAndDescriptions: 'Detailansicht',
    display: 'Anzeigen',
    cancel: 'Abbrechen',
    save: 'Speichern',
    loading: 'Wird geladen…',
    items: 'Elemente'
});


dojo.evalObjPath('xg.gadgets.nls', true);
dojo.lang.mixin(xg.gadgets.nls, xg.index.i18n, {
    edit: 'Bearbeiten',
    title: 'Titel:',
    feedUrl: 'URL:',
    cancel: 'Abbrechen',
    save: 'Speichern',
    loading: 'Wird geladen…',
    removeGadget: 'Gadget entfrnen',
    findGadgetsInDirectory: 'Finde Gadgets im Gadgetverzeichnis'
});

dojo.evalObjPath('xg.forum.nls', true);
dojo.lang.mixin(xg.forum.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Der Text ist länger (' + n + ') als maximal zulässig (' + maximum + ')'; },
    pleaseEnterFirstPost: 'Bitte verfassen Sie den ersten Beitrag für die Diskussion',
    pleaseEnterTitle: 'Bitte geben Sie einen Titel für diese Diskussion ein',
    save: 'Speichern',
    cancel: 'Abbrechen',
    yes: 'Ja',
    no: 'Nein',
    edit: 'Bearbeiten',
    deleteCategory: 'Kategorie löschen',
    discussionsWillBeDeleted: 'Die Diskussionen in dieser Kategorie werden gelöscht.',
    whatDoWithDiscussions: 'Was möchten Sie mit den Diskussionen in dieser Kategorie machen?',
    moveDiscussionsTo: 'Diskussionen verschieben nach:',
    moveToCategory: 'Verschieben zur Kategorie…',
    deleteDiscussions: 'Diskussionen löschen',
    'delete': 'Löschen',
    deleteReply: 'Antwort löschen',
    deleteReplyQ: 'Diese Antwort löschen?',
    deletingReplies: 'Antworten werden gelöscht…',
    doYouWantToRemoveReplies: 'Möchten Sie die Antworten auf diesen Kommentar ebenfalls verschieben?',
    pleaseKeepWindowOpen: 'Bitte diese Seite während des Vorgangs geöffnet lassen. Dies kann einige Minuten dauern.',
    from: 'Von',
    show: 'Einblenden',
    discussions: 'Diskussionen',
    discussionsFromACategory: 'Diskussionen aus einer Kategorie…',
    display: 'Anzeigen',
    items: 'Elemente',
    view: 'Anzeigen'
});


dojo.evalObjPath('xg.groups.nls', true);
dojo.lang.mixin(xg.groups.nls, xg.index.i18n, {
    pleaseChooseAName: 'Bitte wählen Sie einen Namen für Ihre Gruppe.',
    pleaseChooseAUrl: 'Bitte wählen Sie eine Webadresse für Ihre Gruppe.',
    urlCanContainOnlyLetters: 'Die Webadresse darf nur Buchstaben und Zahlen enthalten (keine Leerzeichen).',
    descriptionTooLong: function(n, maximum) { return 'Ihrer Gruppenbeschreibung ist länger (' + n + ') las maximal zulässig (' + maximum + ')'; },
    nameTaken: 'Es tut uns leid, aber dieser Name ist schon vergeben. Bitte wählen Sie einen anderen Namen.',
    urlTaken: 'Es tut uns leid, aber diese Webadresse ist schon vergeben. Bitte wählen Sie eine andere Webadresse.',
    whyNot: 'Warum nicht?',
    groupCreatorDetermines: function(href) { return 'Der Gründer der Gruppe entscheidet, wer Mitglied der Gruppe werden kann. Falls Sie glauben, dass Sie versehentlich abgelehnt wurden, kontaktieren Sie bitte <a ' + href + '>den Gründer der Gruppe</a>.'; },
    edit: 'Bearbeiten',
    from: 'Von',
    show: 'Einblenden',
    groups: 'Gruppen',
    pleaseEnterName: 'Bitte geben Sie Ihren Namen ein',
    pleaseEnterEmailAddress: 'Bitte geben Sie Ihre E-Mail Adresse ein',
    xIsNotValidEmailAddress: function(x) { return x + ' ist keine gültige E-Mail Adresse'; },
    save: 'Speichern',
    cancel: 'Abbrechen'
});


dojo.evalObjPath('xg.html.nls', true);
dojo.lang.mixin(xg.html.nls, xg.index.i18n, {
    contentsTooLong: function(maximum) { return 'Der Text ist zu lang. Bitte geben Sie weniger als ' + maximum + ' Zeichen ein.'; },
    edit: 'Bearbeiten',
    save: 'Speichern',
    cancel: 'Abbrechen',
    saving: 'Wird gespeichert…',
    addAWidget: function(url) { return '<a href="' + url + '">Ein Widget zu diesem Textfeld hinzufügen</a>'; }
});


dojo.evalObjPath('xg.index.nls', true);
dojo.lang.mixin(xg.index.nls, xg.index.i18n, {
    showingNFriends: function(n, searchString) {
        switch(n) {
            case 1: return '1 Freund/in gefunden zur Suchanfrage "' + searchString + '". <a href="#">Alle anzeigen</a>';
            default: return n + ' Freunde gefunden zur Suchanfrage "' + searchString + '". <a href="#">Alle anzeigen</a>';
        }
    },
    sendInvitation: 'Einladung senden',
    sendMessage: 'Nachricht senden',
    sendInvitationToNFriends: function(n) {
        switch(n) {
            case 1: return 'Einladung an 1 Freund/in senden?';
            default: return 'Einladung an ' + n + ' Freunde senden?';
        }
    },
    sendMessageToNFriends: function(n) {
        switch(n) {
            case 1: return 'Nachricht an 1 Freund/in senden?';
            default: return 'Nachricht an ' + n + ' Freunde senden?';
        }
    },
    invitingNFriends: function(n) {
        switch(n) {
            case 1: return '1 Freund/in wird eingeladen…';
            default: return n + ' Freunde werden eingeladen…';
        }
    },
    nFriends: function(n) {
        switch(n) {
            case 1: return '1 Freund/in…';
            default: return n + ' Freunde…';
        }
    },
    sendingMessageToNFriends: function(n) {
        switch(n) {
            case 1: return 'Nachricht an 1 Freund wird gesendet…';
            default: return 'Nachricht an ' + n + ' Freunde wird gesendet…';
        }
    },
    yourMessageOptional: '<label>Ihre Nachricht</label> (optional)',
    pleaseChoosePeople: 'Bitte wählen Sie Personen aus, die Sie einladen möchten.',
    noPeopleSelected: 'Niemand selektiert',
    pleaseEnterEmailAddress: 'Bitte geben Sie Ihre E-Mail Adresse ein.',
    pleaseEnterPassword: function(emailAddress) { return 'Bitte geben Sie das Passwort für ' + emailAddress + ' ein.'; },
    sorryWeDoNotSupport: 'Leider wird das Web-Adressbuch Ihrer E-Mail-Adresse nicht unterstützt. Sie können unten auf \'Adressbuch-Anwendung\' klicken, um auf Ihrem Computer gespeicherte Adressen zu verwenden.',
    pleaseSelectSecondPart: 'Bitte wählen Sie den zweiten Teil Ihrer E-Mail Adresse, z.B. gmail.com.',
    atSymbolNotAllowed: 'Bitte vergewissern Sie sich, dass das @-Symbol nicht im ersten Teil Ihrer E-Mail Adresse steht.',
    resetTextQ: 'Text zurücksetzen?',
    resetTextToOriginalVersion: 'Sind Sie sicher, dass Sie den gesamten Text auf die ursprüngliche Version zurücksetzen möchten? Alle Änderungen gehen damit verloren.',
    changeQuestionsToPublic: 'Fragen öffentlich machen?',
    changingPrivateQuestionsToPublic: 'Wenn Sie private Fragen öffentlich machen, werden sämtliche Mitgliederantworten sichtbar. Sind Sie sicher?',
    youHaveUnsavedChanges: 'Sie haben ungespeicherte Änderungen.',
    pleaseEnterASiteName: 'Bitte geben Sie einen Namen für das soziale Netzwerk ein, z.B. Club der Kleinen Clowns',
    pleaseEnterShorterSiteName: 'Bitte geben Sie einen kürzeren Namen ein (max. 64 Zeichen)',
    pleaseEnterShorterSiteDescription: 'Bitte geben Sie eine kürzere Beschreibung ein (max. 250 Zeichen)',
    siteNameHasInvalidCharacters: 'Der Name enthält ungültige Zeichen',
    thereIsAProblem: 'Es gibt ein Problem mit den eingegeben Informationen',
    thisSiteIsOnline: 'Dieses soziale Netzwerk ist Online',
    onlineSiteCanBeViewed: '<strong>Online</strong> - Das Netzwerk ist sichtbar für alle (abhängig von Ihren Einstellungen zur Privatsphäre).',
    takeOffline: 'Vom Netz nehmen (offline)',
    thisSiteIsOffline: 'Dieses soziale Netzwerk ist Offline',
    offlineOnlyYouCanView: '<strong>Offline</strong> - Nur Sie können dieses Netzwerk sehen.',
    takeOnline: 'Ins Netz stellen (online)',
    themeSettings: 'Designeinstellungen',
    addYourOwnCss: 'Erweitert',
    error: 'Fehler',
    pleaseEnterTitleForFeature: function(displayName) { return 'Bitte geben Sie einen Titel für die ' + displayName + ' Funktion ein.'; },
    thereIsAProblemWithTheInformation: 'Es gibt ein Problem mit den eingegeben Informationen.',
    photos: 'Fotos',
    videos: 'Videos',
    pleaseEnterTheChoicesFor: function(questionTitle) { return 'Bitte geben Sie die Auswahlmöglichkeiten für "' + questionTitle + '" ein, z.B. Wandern, Lesen, Shoppen.'; },
    pleaseEnterTheChoices: 'Bitte geben Sie die Auswahlmöglichkeiten ein, z.B. Wandern, Lesen, Shoppen.',
    shareWithFriends: 'Mit Freunde teilen',
    email: 'E-Mail',
    separateMultipleAddresses: 'Mehrere Adressen durch Komma trennen',
    subject: 'Betreff',
    message: 'Nachricht',
    send: 'Senden',
    cancel: 'Abbrechen',
    pleaseEnterAValidEmail: 'Bitte geben Sie eine gültige E-Mail Adresse ein.',
    go: 'Gehe zu',
    areYouSureYouWant: 'Sind Sie sicher?',
    processing: 'Wird verarbeitet…',
    pleaseKeepWindowOpen: 'Bitte diese Seite während des Vorgangs geöffnet lassen. Dies kann einige Minuten dauern.',
    complete: 'Fertig!',
    processIsComplete: 'Vorgang abgeschlossen.',
    ok: 'OK',
    body: 'Text',
    pleaseEnterASubject: 'Bitte geben Sie einen Betreff ein',
    pleaseEnterAMessage: 'Bitte geben Sie eine Nachricht ein',
    pleaseChooseFriends: 'Bitte wählen Sie Freunde aus, bevor Sie die Nachricht senden.',
    thereHasBeenAnError: 'Es ist ein Fehler aufgetreten',
    fileNotFound: 'Datei nicht gefunden',
    pleaseProvideADescription: 'Bitte geben Sie eine Beschreibung ein',
    pleaseEnterYourFriendsAddresses: 'Bitte geben Sie die Adressen oder Ning IDs Ihrer Freunde ein',
    pleaseEnterSomeFeedback: 'Bitte geben Sie Ihr Feedback ein',
    title: 'Titel:',
    setAsMainSiteFeature: 'Als Hauptelement verwenden',
    thisIsTheMainSiteFeature: 'Dies ist das Hauptelement.',
    customized: 'Angepasst',
    copyHtmlCode: 'HTML-Einbettungscode kopieren',
    playerSize: 'Größe des Players',
    selectSource: 'Quelle auswählen',
    myAlbums: 'Meine Alben',
    myMusic: 'Meine Musik',
    myVideos: 'Meine Videos',
    showPlaylist: 'Wiedergabeliste anzeigen',
    change: 'Ändern',
    changing: 'Änderungen werden vorgenommen...',
    changePrivacy: 'Einstellungen zur Privatsphäre ändern?',
    keepWindowOpenWhileChanging: 'Bitte diese Seite geöffnet lassen, während die Einstellungen zur Privatsphäre geändert werden. Dieser Vorgang kann einige Minuten dauern.',
    htmlNotAllowed: 'HTML ist nicht erlaubt',
    noFriendsFound: 'Es wurden keine Freunde gefunden, auf die Ihre Suche passt.',
    addingInstructions: 'Bitte dieses Fenster geöffnet lassen, während Ihr Inhalt hinzugefügt wird.',
    addingLabel: 'Inhalt wird hinzugefügt… .',
    cannotKeepFiles: 'Wenn Sie mehr Optionen sehen möchten, müssen Sie Ihre Dateien erneut auswählen.  Möchten Sie fortfahren?',
    done: 'Fertig',
    looksLikeNotImage: 'Mindestens eine Datei weist nicht das .jpg-, .gif,- oder .png-Format auf.  Möchten Sie trotzdem versuchen, die Datei hochzuladen?',
    looksLikeNotMusic: 'Die ausgewählte Datei weist nicht das .mp3-Format auf.  Möchten Sie trotzdem versuchen, die Datei hochzuladen?',
    looksLikeNotVideo: 'Die ausgewählte Datei weist nicht das .mov-, .mpg-, .mp4-, .avi-, .3gp- oder .wmv-Format auf.  Möchten Sie trotzdem versuchen, die Datei hochzuladen?',
    messageIsTooLong: function(n) { return 'Ihre Nachricht ist zu lang.  Bitte '+n+'  Zeichen oder weniger verwenden.'; },
    pleaseSelectPhotoToUpload: 'Bitte wählen Sie ein Foto zum Hochladen aus.',
    processingFailed: 'Verarbeitung fehlgeschlagen.  Bitte versuchen Sie es später erneut.',
    selectOrPaste: 'Wählen Sie ein Video aus oder fügen Sie den \'Einbettungscode\' ein.',
    selectOrPasteMusic: 'Wählen Sie einen Song aus oder fügen Sie die URL ein.',
    sendingLabel: 'Senden… .',
    thereWasAProblem: 'Beim Hinzufügen Ihres Inhalts ist ein Problem aufgetreten.  Bitte versuchen Sie es später erneut.',
    uploadingInstructions: 'Bitte dieses Fenster während des Hochladens geöffnet lassen.',
    uploadingLabel: 'Hochladen.. .',
    youNeedToAddEmailRecipient: 'Geben Sie einen E-Mail-Empfänger an.',
    yourMessage: 'Ihre Nachricht',
    yourMessageIsBeingSent: 'Ihre Nachricht wird gesendet.',
    yourSubject: 'Ihr Betreff'
});


dojo.evalObjPath('xg.music.nls', true);
dojo.lang.mixin(xg.music.nls, xg.index.i18n, {
    play: 'Abspielen',
    pleaseSelectTrackToUpload: 'Bitte wählen Sie einen Song zum Hochladen aus.',
    pleaseEnterTrackLink: 'Bitte geben Sie eine Song URL ein.',
    thereAreUnsavedChanges: 'Es gibt ungespeicherte Änderungen.',
    autoplay: 'Automatische Wiedergabe',
    showPlaylist: 'Wiedergabeliste anzeigen',
    playLabel: 'Abspielen',
    url: 'URL',
    rssXspfOrM3u: 'rss, xspf, oder m3u',
    save: 'Speichern',
    cancel: 'Abbrechen',
    edit: 'Bearbeiten',
    shufflePlaylist: 'Wiedergabeliste durcheinander mischen',
    fileIsNotAnMp3: 'Eine der Dateien scheint keine MP3 zu sein. Trotzdem hochladen?',
    entryNotAUrl: 'Eine der Eingaben scheint keine URL zu sein. Vergewissern Sie sich, dass alle Eingaben mit <kbd>http://</kbd> beginnen'
});

dojo.evalObjPath('xg.notes.nls', true);
dojo.lang.mixin(xg.notes.nls, xg.index.i18n, {
    addNewNote: 'Neue Notiz hinzufügen',
    noteTitleTooLong: 'Der Titel der Notiz ist zu lang.',
    pleaseEnterNoteEntry: 'Bitte geben Sie einen Notiz-Eintrag ein.',
    pleaseEnterNoteTitle: 'Bitte geben Sie einen Titel für Ihre Notiz ein!'
});

dojo.evalObjPath('xg.page.nls', true);
dojo.lang.mixin(xg.page.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Der eingegebene Text ist länger (' + n + ') als maximal zulässig (' + maximum + ')'; },
    pleaseEnterContent: 'Bitte geben Sie den Seiteninhalt ein',
    pleaseEnterTitle: 'Bitte geben Sie einen Titel für die Seite ein',
    pleaseEnterAComment: 'Bitte geben Sie einen Kommentar ein',
    deleteThisComment: 'Möchten Sie diesen Kommentar wirklich löschen?',
    save: 'Speichern',
    cancel: 'Abbrechen',
    discussionTitle: 'Titel der Seite:',
    tags: 'Schlagworte:',
    edit: 'Bearbeiten',
    close: 'Schließen',
    displayPagePosts: 'Beiträge auf der Seite anzeigen',
    thereIsAProblem: 'Es gibt ein Problem bezüglich der eingegeben Informationen.'
});


dojo.evalObjPath('xg.photo.nls', true);
dojo.lang.mixin(xg.photo.nls, xg.index.i18n, {
    randomOrder: 'Zufällige Reihenfolge',
    untitled: 'Ohne Titel',
    photos: 'Fotos',
    edit: 'Bearbeiten',
    photosFromAnAlbum: 'Alben',
    show: 'Einblenden',
    rows: 'Zeilen',
    cancel: 'Abbrechen',
    save: 'Speichern',
    deleteThisPhoto: 'Dieses Foto löschen?',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Der eingegebene Text ist länger (' + n + ') als maximal zulässig (' + maximum + ')'; },
    weCouldNotLookUpAddress: function(address) { return 'Leider konnten wir die Adresse "' + address + '" nicht finden.'; },
    pleaseSelectPhotoToUpload: 'Bitte wählen Sie ein Foto zum Hochladen aus.',
    pleaseEnterAComment: 'Bitte geben Sie einen Kommentar ein.',
    addToExistingAlbum: 'Zu bestehendem Album hinzufügen',
    addToNewAlbumTitled: 'Hinzufügen zu einem neuen Album mit dem Titel…',
    deleteThisComment: 'Diesen Kommentar löschen?',
    importingNofMPhotos: function(n,m) { return 'Foto <span id="currentP">' + n + '</span> von ' + m + ' wird importiert.'},
    starting: 'Wird gestartet…',
    done: 'Fertig!',
    from: 'Von',
    display: 'Anzeigen',
    takingYou: 'Weiter zu Ihren Fotos…',
    anErrorOccurred: 'Leider ist ein Fehler aufgetreten. Bitte melden Sie dieses Problem mittels des Links unten auf dieser Seite.',
    weCouldntFind: 'Es wurden keine Fotos gefunden! Versuchen Sie doch eine der anderen Optionen!'
});

dojo.evalObjPath('xg.activity.nls', true);
dojo.lang.mixin(xg.activity.nls, xg.index.i18n, {
    edit: 'Bearbeiten',
    show: 'Einblenden',
    events: 'Ereignisse',
    setWhatActivityGetsDisplayed: 'Festlegen, welche Aktivitäten angezeigt wird',
    save: 'Speichern',
    cancel: 'Abbrechen'
});


dojo.evalObjPath('xg.profiles.nls', true);
dojo.lang.mixin(xg.profiles.nls, xg.index.i18n, {
    pleaseEnterValueForPost: 'Bitte geben Sie Text für den Beitrag ein.',
    edit: 'Bearbeiten',
    recentlyAdded: 'Kürzlich hinzugefügt',
    featured: 'Vorgestellt',
    iHaveRecentlyAdded: 'Kürzlich von mir hinzugefügt:',
    fromTheSite: 'Vom Netzwerk',
    cancel: 'Abbrechen',
    save: 'Speichern',
    loading: 'Wird geladen…',
    addAsFriend: 'Als Freund/in hinzufügen',
    requestSent: 'Anfrage gesendet!',
    sendingFriendRequest: 'Anfrage wird gesendet',
    thisIsYou: 'Das sind Sie!',
    isYourFriend: 'Ihr/e Freund/in',
    isBlocked: 'Blockiert',
    pleaseEnterAComment: 'Bitte geben Sie einen Kommentar ein.',
    pleaseEnterPostBody: 'Bitte geben Sie Text für den Beitrag ein.',
    pleaseEnterChatter: 'Bitte geben Sie Text für den Kommentar ein.',
    letMeApproveChatters: 'Wollen Sie Kommentare vor der Veröffentlichung kontrollieren?',
    noPostChattersImmediately: 'Nein – Kommentare sofort veröffentlichen',
    yesApproveChattersFirst: 'Ja – Kommentare zuerst kontrollieren',
    yourCommentMustBeApproved: 'Ihr Kommentar muss erst akzeptiert werden, bevor er veröffentlicht werden kann.',
    reallyDeleteThisPost: 'Möchten Sie diesen Beitrag wirklich löschen?',
    commentWall: 'Kommentarwand',
    commentWallNComments: function(n) {
        switch(n) {
            case 1: return 'Kommentarwand (1 Kommentar) ';
            default: return 'Kommentarwand (' + n + ' Kommentare) ';
        }
    },
    display: 'Anzeigen',
    from: 'Von',
    show: 'Einblenden',
    rows: 'Zeilen',
    posts: 'Beiträge'
});


dojo.evalObjPath('xg.shared.nls', true);
dojo.lang.mixin(xg.shared.nls, xg.index.i18n, {
    uploadAPhoto: 'Ein Foto hochladen',
    uploadAnImage: 'Ein Bild hochladen',
    uploadAPhotoEllipsis: 'Ein Foto hochladen…',
    useExistingImage: 'Ein vorhandenes Bild verwenden:',
    existingImage: 'Vorhandenes Bild',
    useThemeImage: 'Bild aus dem Design verwenden:',
    themeImage: 'Designbild',
    noImage: 'Kein Bild',
    uploadImageFromComputer: 'Ein Bild vom Computer hochladen',
    tileThisImage: 'Bild kacheln',
    done: 'Fertig',
    currentImage: 'Aktuelles Bild',
    pickAColor: 'Farbe aussuchen…',
    openColorPicker: 'Farbauswahl öffnen',
    loading: 'Wird geladen…',
    ok: 'OK',
    save: 'Speichern',
    cancel: 'Abbrechen',
    saving: 'Wird gespeichert…',
    addAnImage: 'Bild hinzufügen',
    bold: 'Fett',
    italic: 'Kursiv',
    underline: 'Unterstrichen',
    strikethrough: 'Durchgestrichen',
    addHyperink: 'Hyperlink hinzufügen',
    options: 'Optionen',
    wrapTextAroundImage: 'Soll der Text um das Bild herum fließen?',
    imageOnLeft: 'Bild nach links ausrichten?',
    imageOnRight: 'Bild nach rechts ausrichten?',
    createThumbnail: 'Vorschaubild erstellen?',
    pixels: 'Pixel',
    createSmallerVersion: 'Erstellen Sie zur Anzeige eine kleinere Version Ihres Bildes. Legen Sie die Breite in Pixel fest.',
    popupWindow: 'Popup Fenster?',
    linkToFullSize: 'Verweis erstellen, der das Originalbild in einem Popup-Fenster öffnet.',
    add: 'Hinzufügen',
    keepWindowOpen: 'Bitte diese Seite während des Hochladens geöffnet lassen.',
    cancelUpload: 'Hochladen abbrechen',
    pleaseSelectAFile: 'Bitte wählen Sie eine Bilddatei aus.',
    pleaseSpecifyAThumbnailSize: 'Bitte bestimmen Sie die Größe des Vorschaubildes.',
    thumbnailSizeMustBeNumber: 'Die Größe des Vorschaubildes muss eine Zahl sein',
    addExistingImage: 'oder fügen Sie ein vorhandenes Bild ein.',
    clickToEdit: 'Zum Bearbeiten hier klicken.',
    sendingFriendRequest: 'Anfrage wird gesendet',
    requestSent: 'Anfrage gesendet!',
    pleaseCorrectErrors: 'Bitte korrigieren Sie diese Fehler.',
    tagThis: 'Schlagwort hinzufügen',
    addOrEditYourTags: 'Schlagworte hinzufügen oder bearbeiten:',
    addYourRating: 'Wertung hinzufügen:',
    separateMultipleTagsWithCommas: 'Mehrere Schlagworte durch Kommas trennen, z.B. cool, \"San Francisco\"',
    saved: 'Gespeichert!',
    noo: 'NEU',
    none: 'OHNE',
    joinNow: 'Jetzt Mitglied werden',
    join: 'Mitglied werden',
    youHaventRated: 'Sie haben dieses Element noch nicht bewertet.',
    yourRatedThis: function(n) {
        switch(n) {
            case 1: return 'Sie haben dieses Element mit 1 Stern bewertet.';
            default: return 'Sie haben dieses Element mit ' + n + ' Sternen bewertet.';
        }
    },
    yourRatingHasBeenAdded: 'Ihre Wertung wurde hinzugefügt.',
    thereWasAnErrorRating: 'Ein Fehler trat auf während des Bewertens dieses Inhalts.',
    yourTagsHaveBeenAdded: 'Ihre Schlagworte wurden hinzugefügt.',
    thereWasAnErrorTagging: 'Ein Fehler trat auf während des Hinzufügens der Schlagworte.',
    addToFavorites: 'Zu Favoriten hinzufügen',
    removeFromFavorites: 'Aus Favoriten entfernen',
    nStarsOutOfM: function(n,m) {
        switch(n) {
            case 1: return '1 Stern von ' + m;
            default: return n + ' Sterne von ' + m;
        }
    },
    follow: 'Verfolgen',
    stopFollowing: 'Nicht mehr verfolgen',
    pendingPromptTitle: 'Mitgliedschaft muss noch akzeptiert werden',
    youCanDoThis: 'Diese Funktion steht Ihnen zur Verfügung, sobald Ihre Mitgliedschaft von den Administratoren akzeptiert wurde.',
    pleaseEnterAComment: 'Bitte geben Sie einen Kommentar ein.',
    pleaseEnterAFileAddress: 'Bitte geben Sie die Datei-Adresse ein.',
    pleaseEnterAWebsite: 'Bitte geben Sie eine Website-Adresse ein.'
});


dojo.evalObjPath('xg.video.nls', true);
dojo.lang.mixin(xg.video.nls, xg.index.i18n, {
    edit: 'Bearbeiten',
    display: 'Anzeigen',
    detail: 'Detail',
    player: 'Player',
    from: 'Von',
    show: 'Einblenden',
    videos: 'Videos',
    cancel: 'Abbrechen',
    save: 'Speichern',
    saving: 'Wird gespeichert…',
    deleteThisVideo: 'Dieses Video löschen?',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Der eingegebene Text ist länger (' + n + ') als maximal zulässig (' + maximum + ')'; },
    weCouldNotLookUpAddress: function(address) { return 'Leider konnten wir die Adresse \"' + address + '\" nicht finden.'; },
    approve: 'Erlauben',
    approving: 'Wird erlaubt…',
    keepWindowOpenWhileApproving: 'Bitte lassen Sie diese Seite geöffnet, während die Videos erlaubt werden. Dieser Vorgang kann einige Minuten dauern.',
    'delete': 'Löschen',
    deleting: 'Wird gelöscht…',
    keepWindowOpenWhileDeleting: 'Bitte lassen Sie diese Seite geöffnet, während die Videos gelöscht werden. Dieser Vorgang kann einige Minuten dauern.',
    pasteInEmbedCode: 'Bitte fügen Sie den Einbettungscode für das Video von einer externen Website ein.',
    pleaseSelectVideoToUpload: 'Bitte wählen Sie ein Video zum Hochladen aus.',
    embedCodeContainsMoreThanOneVideo: 'Der Einbettungscode enthält mehr als ein Video. Bitte vergewissern Sie sich, dass er nur ein <object> und/oder ein <embed> HTML Tag enthält.',
    embedCodeMissingTag: 'Der Einbettungscode enthält kein Video, ihm fehlt ein &lt-;embed&gt-; oder &lt-;object&gt-; HTML Tag.',
    fileIsNotAMov: 'Diese Datei scheint keine .mov-, .mpg-, .mp4-, .avi-, .3gp- oder .wmv-Datei zu sein. Wollen Sie sie trotzdem hochladen?',
    pleaseEnterAComment: 'Bitte geben Sie einen Kommentar ein.',
    youRatedVideoNStars: function(n) {
        switch(n) {
            case 1: return 'Sie haben dieses Video mit 1 Stern bewertet!';
            default: return 'Sie haben dieses Video mit ' + n + ' Sternen bewertet!';
        }
    },
    deleteThisComment: 'Diesen Kommentar löschen?',
    embedHTMLCode: 'HTML Einbettungscode:',
    copyHTMLCode: 'HTML Einbettungscode kopieren'
});

dojo.evalObjPath('xg.uploader.nls', true);
dojo.lang.mixin(xg.uploader.nls, xg.index.i18n, {
    fileBrowserHeader: 'Mein Computer',
    fileRoot: 'Arbeitsplatz',
    fileInformationHeader: 'Information',
    uploadHeader: 'Dateien zum Hochladen',
    dragOutInstructions: 'Ziehen Sie die Dateien aus diesem Bereich, um sie zu entfernen',
    dragInInstructions: 'Ziehen Sie Dateien hierher',
    selectInstructions: 'Wählen Sie eine Datei aus',
    files: 'Dateien',
    totalSize: 'Größe aller Dateien',
    fileName: 'Name',
    fileSize: 'Größe',
    nextButton: 'Weiter >',
    okayButton: 'OK',
    yesButton: 'Ja',
    noButton: 'Nein',
    uploadButton: 'Hochladen',
    cancelButton: 'Abbrechen',
    backButton: 'Zurück',
    continueButton: 'Weiter',
    uploadingLabel: 'Werden hochgeladen...',
    uploadingStatus: function(n, m) { return n + ' von ' + m + ' wird hochgeladen'; },
    uploadingInstructions: 'Bitte lassen Sie diese Seite offen während des Hochladens',
    uploadLimitWarning: function(n) { return 'Sie können bis zu ' + n + ' Dateien auf einmal hochladen.'; },
    uploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Sie haben die maximale Anzahl von Dateien erreicht.';
            case 1: return 'Sie können noch 1 weitere Datei hochladen.';
            default: return 'Sie können noch ' + n + ' weitere Dateien hochladen.';
        }
    },
    iHaveTheRight: 'Ich habe das Recht, diese Dateien innerhalb der <a href="/main/authorization/termsOfService">AGBs</a> hochzuladen',
    updateJavaTitle: 'Java aktualisieren',
    updateJavaDescription: 'Das Hochladetool erfordert eine neuere Java Version. Klicken Sie "OK" um Java zu aktualisieren.',
    batchEditorLabel: 'Informationen für alle Dateien bearbeiten',
    applyThisInfo: 'Diese Informationen für alle Dateien unten verwenden',
    titleProperty: 'Titel',
    descriptionProperty: 'Beschreibung',
    tagsProperty: 'Schlagworte',
    viewableByProperty: 'Kann angesehen werden von',
    viewableByEveryone: 'Allen',
    viewableByFriends: 'Nur meinen Freunden',
    viewableByMe: 'Nur mir',
    albumProperty: 'Album',
    artistProperty: 'Künstler',
    enableDownloadLinkProperty: 'Link für das Herunterladen aktivieren',
    enableProfileUsageProperty: 'Anderen erlauben, diesen Song auf ihrer persönliche Seite zu verwenden',
    licenseProperty: 'Lizenz',
    creativeCommonsVersion: '3.0',
    selectLicense: '— Lizenz wählen —',
    copyright: '© Alle Rechte vorbehalten',
    ccByX: function(n) { return 'Creative Commons Namensnennung ' + n; },
    ccBySaX: function(n) { return 'Creative Commons Namensnennung-Weitergabe unter gleichen Bedingungen ' + n; },
    ccByNdX: function(n) { return 'Creative Commons Namensnennung-KeineBearbeitung ' + n; },
    ccByNcX: function(n) { return 'Creative Commons Namensnennung-NichtKommerziell ' + n; },
    ccByNcSaX: function(n) { return 'Creative Commons Namensnennung-NichtKommerziell-Weitergabe unter gleichen Bedingungen ' + n; },
    ccByNcNdX: function(n) { return 'Creative Commons Namensnennung-NichtKommerziell-KeineBearbeitung ' + n; },
    publicDomain: 'Lizenzfrei',
    other: 'Andere',
    errorUnexpectedTitle: 'Ups!',
    errorUnexpectedDescription: 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es noch einmal.',
    errorTooManyTitle: 'Zu viele Dateien',
    errorTooManyDescription: function(n) { return 'Es tut uns leid, aber Sie können nur ' + n + ' Dateien auf einmal hochladen.'; },
    errorNotAMemberTitle: 'Nicht erlaubt',
    errorNotAMemberDescription: 'Es tut uns leid, aber Sie müssen Mitglied des Netzwerks sein, um Dateien hochladen zu können.',
    errorContentTypeNotAllowedTitle: 'Nicht erlaubt',
    errorContentTypeNotAllowedDescription: 'Es tut uns leid, aber das Hochladen von derartigen Dateien ist nicht erlaubt.',
    errorUnsupportedFormatTitle: 'Ups!',
    errorUnsupportedFormatDescription: 'Es tut uns leid, aber dieser Dateityp wird von uns nicht unterstützt.',
    errorUnsupportedFileTitle: 'Ups!',
    errorUnsupportedFileDescription: 'foo.exe ist ein nicht unterstütztes Dateiformat.',
    errorUploadUnexpectedTitle: 'Ups!',
    errorUploadUnexpectedDescription: function(file) {
        return file ?
            ('Es scheint ein problem mit der Datei ' + file + ' zu geben. Bitte entfernen Sie diese Datei aus der Liste bevor Sie es erneut versuchen.') :
            'Es scheint ein Problem mit der ersten Datei in der Liste der hochzuladenden Dateien zu geben. Bitte entfernen Sie diese Datei aus der Liste bevor Sie es erneut versuchen.';
    },
    cancelUploadTitle: 'Hochladen abbrechen?',
    cancelUploadDescription: 'Sind Sie sicher, das Sie das Hochladen der verbleibenden Dateien abbrechen wollen?',
    uploadSuccessfulTitle: 'Hochladen abgeschlossen',
    uploadSuccessfulDescription: 'Bitte warten Sie einen Moment, wir leiten Sie weiter zu den von Ihnen hochgeladenen Dateien…',
    uploadPendingDescription: 'Ihre Dateien wurden erfolgreich hochgeladen, müssen jetzt aber noch akzeptiert werden.',
    photosUploadHeader: 'Hochzuladende Fotos',
    photosDragOutInstructions: 'Ziehen Sie Fotos hierher um sie zu entfernen',
    photosDragInInstructions: 'Ziehen Sie Fotos hierher',
    photosSelectInstructions: 'Wählen Sie ein Foto aus',
    photosFiles: 'Fotos',
    photosUploadingStatus: function(n, m) { return 'Foto ' + n + ' von ' + m + ' wird hochgeladen'; },
    photosErrorTooManyTitle: 'Zu viele Fotos',
    photosErrorTooManyDescription: function(n) { return 'Es tut uns leid, aber Sie können nur ' + n + ' Fotos auf einmal hochladen.'; },
    photosErrorContentTypeNotAllowedDescription: 'Es tut uns leid, aber in diesem Netzwerk wurde das Hochladen von Fotos deaktiviert.',
    photosErrorUnsupportedFormatDescription: 'Es tut uns leid, aber wir akzeptieren nur Bilder im .jpg, .gif oder .png Format.',
    photosErrorUnsupportedFileDescription: function(n) { return n + ' ist keine .jpg, .gif, .bmp oder .png Datei.'; },
    photosBatchEditorLabel: 'Informationen für alle Fotos editieren',
    photosApplyThisInfo: 'Diese Informationen für alle Fotos unten verwenden',
    photosErrorUploadUnexpectedDescription: function(file) {
        return file ?
            ('Es scheint ein problem mit dem Foto ' + file + ' zu geben. Bitte entfernen Sie es aus der Liste bevor Sie es erneut versuchen.') :
            'Es scheint ein Problem mit dem ersten Foto in der Liste der hochzuladenden Fotos zu geben. Bitte entfernen Sie es aus der Liste bevor Sie es erneut versuchen.';
    },
    photosUploadSuccessfulDescription: 'Bitte warten Sie einen Moment, wir leiten Sie weiter zu den von Ihnen hochgeladenen Fotos…',
    photosUploadPendingDescription: 'Ihre Fotos wurden erfolgreich hochgeladen, müssen jetzt aber noch akzeptiert werden.',
    photosUploadLimitWarning: function(n) { return 'Sie können bis zu ' + n + ' Fotos auf einmal hochladen.'; },
    photosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Sie haben die maximale Anzahl von Fotos erreicht.';
            case 1: return 'Sie können noch 1 weiteres Foto hochladen.';
            default: return 'Sie können noch ' + n + ' weitere Fotos hochladen.';
        }
    },
    photosIHaveTheRight: 'Ich habe das Recht, diese Fotos innerhalb der <a href="/main/authorization/termsOfService">AGBs</a> hochzuladen',
    videosUploadHeader: 'Hochzuladende Videos',
    videosDragInInstructions: 'Ziehen Sie Videos hierher',
    videosDragOutInstructions: 'Ziehen Sie Videos hierher, um sie zu entfernen',
    videosSelectInstructions: 'Wählen Sie ein Video aus',
    videosFiles: 'Videos',
    videosUploadingStatus: function(n, m) { return 'Video ' + n + ' von ' + m + ' wird hochgeladen'; },
    videosErrorTooManyTitle: 'Zu viele Videos',
    videosErrorTooManyDescription: function(n) { return 'Es tut uns leid, aber Sie können nur ' + n + ' Videos auf einmal hochladen.'; },
    videosErrorContentTypeNotAllowedDescription: 'Es tut uns leid, aber in diesem Netzwerk wurde das Hochladen von Videos deaktiviert.',
    videosErrorUnsupportedFormatDescription: 'Es tut uns leid, aber wir akzeptieren nur Videos im .avi, .mov, .mp4, .wmv oder .mpg Format.',
    videosErrorUnsupportedFileDescription: function(x) { return x + ' ist keine .avi, .mov, .mp4, .wmv oder .mpg Datei.'; },
    videosBatchEditorLabel: 'Informationen für alle Videos editieren',
    videosApplyThisInfo: 'Diese Informationen für alle Videos unten verwenden',
    videosErrorUploadUnexpectedDescription:  function(file) {
        return file ?
            ('Es scheint ein problem mit dem Video ' + file + ' zu geben. Bitte entfernen Sie es aus der Liste bevor Sie es erneut versuchen.') :
            'Es scheint ein Problem mit dem ersten Video in der Liste der hochzuladenden Videos zu geben. Bitte entfernen Sie es aus der Liste bevor Sie es erneut versuchen.';
    },
    videosUploadSuccessfulDescription: 'Bitte warten Sie einen Moment, wir leiten Sie weiter zu den von Ihnen hochgeladenen Videos…',
    videosUploadPendingDescription: 'Ihre Videos wurden erfolgreich hochgeladen, müssen jetzt aber noch akzeptiert werden.',
    videosUploadLimitWarning: function(n) { return 'Sie können bis zu ' + n + ' Videos auf einmal hochladen.'; },
    videosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Sie haben die maximale Anzahl von Videos erreicht.';
            case 1: return 'Sie können noch 1 weiteres Video hochladen.';
            default: return 'Sie können noch ' + n + ' weitere Videos hochladen.';
        }
    },
    videosIHaveTheRight: 'Ich habe das Recht, diese Videos innerhalb der <a href="/main/authorization/termsOfService">AGBs</a> hochzuladen',
    musicUploadHeader: 'Hochzuladende Songs',
    musicTitleProperty: 'Titel',
    musicDragOutInstructions: 'Ziehen Sie Songs hierher, um sie zu entfernen',
    musicDragInInstructions: 'Ziehen Sie Songs hierher',
    musicSelectInstructions: 'Wählen Sie einen Song aus',
    musicFiles: 'Songs',
    musicUploadingStatus: function(n, m) { return 'Song ' + n + ' von ' + m + ' wird hochgeladen'; },
    musicErrorTooManyTitle: 'Zu viele Songs',
    musicErrorTooManyDescription: function(n) { return 'Es tut uns leid, aber Sie können nur ' + n + ' Songs auf einmal hochladen.'; },
    musicErrorContentTypeNotAllowedDescription: 'Es tut uns leid, aber in diesem Netzwerk wurde das Hochladen von Musik deaktiviert.',
    musicErrorUnsupportedFormatDescription: 'Es tut uns leid, aber wir akzeptieren nur Musik im .mp3 Format.',
    musicErrorUnsupportedFileDescription: function(x) { return x + ' ist keine .mp3 Datei.'; },
    musicBatchEditorLabel: 'Informationen für alle Songs editieren',
    musicApplyThisInfo: 'Diese Informationen für alle Songs unten verwenden',
    musicErrorUploadUnexpectedDescription:  function(file) {
        return file ?
            ('Es scheint ein problem mit dem Song ' + file + ' zu geben. Bitte entfernen Sie es aus der Liste bevor Sie es erneut versuchen.') :
            'Es scheint ein Problem mit dem ersten Song in der Liste der hochzuladenden Songs zu geben. Bitte entfernen Sie es aus der Liste bevor Sie es erneut versuchen.';
    },
    musicUploadSuccessfulDescription: 'Bitte warten Sie einen Moment, wir leiten Sie weiter zu den von Ihnen hochgeladenen Songs…',
    musicUploadPendingDescription: 'Ihre Songs wurden erfolgreich hochgeladen, müssen jetzt aber noch akzeptiert werden.',
    musicUploadLimitWarning: function(n) { return 'Sie können bis zu ' + n + ' Songs auf einmal hochladen.'; },
    musicUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Sie haben die maximale Anzahl von Songs erreicht.';
            case 1: return 'Sie können noch 1 weiteren Song hochladen.';
            default: return 'Sie können noch ' + n + ' weitere Songs hochladen.';
        }
    },
    musicIHaveTheRight: 'Ich habe das Recht, diese Songs innerhalb der <a href="/main/authorization/termsOfService">AGBs</a> hochzuladen'
});
