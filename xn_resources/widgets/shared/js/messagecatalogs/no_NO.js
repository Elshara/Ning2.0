dojo.provide('xg.shared.messagecatalogs.no_NO');

dojo.require('xg.index.i18n');

/**
 * Texts for the no_NO
 */
// Use UTF-8 byte sequences instead of HTML entities, e.g., & instead of &amp;, â€¦ instead of &hellip;  [Jon Aquino 2007-01-10]

dojo.evalObjPath('xg.events.nls', true);
dojo.lang.mixin(xg.events.nls, xg.index.i18n, {
    pleaseChooseImage: 'Velg et bilde for arrangementet',
    pleaseEnterAMessage: 'Legg inn en melding',
    pleaseEnterDescription: 'Legg inn en beskrivelse av arrangementet',
    pleaseEnterLocation: 'Legg inn et sted for arrangementet',
    pleaseEnterTitle: 'Legg inn en tittel på arrangementet',
    pleaseEnterType: 'Legg inn minst en arrangementstype',
    send: 'Send',
    sending: 'Sender…',
    thereHasBeenAnError: 'Det har oppstått en feil',
    yourMessage: 'Meldingen din',
    yourMessageHasBeenSent: 'Meldingen din er sendt.',
    yourMessageIsBeingSent: 'Meldingen din sendes.'
});

dojo.evalObjPath('xg.feed.nls', true);
dojo.lang.mixin(xg.feed.nls, xg.index.i18n, {
    edit: 'Rediger',
    title: 'Tittel:',
    feedUrl: 'URL-adresse:',
    show: 'Vis:',
    titles: 'Kun titler',
    titlesAndDescriptions: 'Detaljert visning',
    display: 'Vis',
    cancel: 'Avbryt',
    save: 'Lagre',
    loading: 'Laster...',
    items: 'artikler'
});


dojo.evalObjPath('xg.gadgets.nls', true);
dojo.lang.mixin(xg.gadgets.nls, xg.index.i18n, {
    edit: 'Rediger',
    title: 'Tittel:',
    feedUrl: 'URL-adresse:',
    cancel: 'Avbryt',
    save: 'Lagre',
    loading: 'Laster…',
    removeGadget: 'Fjern gadget',
    findGadgetsInDirectory: 'Søk etter gadgeter i Gadget-katalogen'
});


dojo.evalObjPath('xg.uploader.nls', true);
dojo.lang.mixin(xg.uploader.nls, xg.index.i18n, {
    fileBrowserHeader: 'Min datamaskin',
    fileRoot: 'Min datamaskin',
    fileInformationHeader: 'Informasjon',
    uploadHeader: 'Filer til opplasting',
    dragOutInstructions: 'Dra ut filer for å fjerne dem',
    dragInInstructions: 'Dra filer hit',
    selectInstructions: 'Velg en fil',
    files: 'Filer',
    totalSize: 'Total størrelse',
    fileName: 'Navn',
    fileSize: 'Størrelse',
    nextButton: 'Neste >',
    okayButton: 'OK',
    yesButton: 'Ja',
    noButton: 'Nei',
    uploadButton: 'Last opp',
    cancelButton: 'Avbryt',
    backButton: 'Tilbake',
    continueButton: 'Fortsett',
    uploadingLabel: 'Laster opp…',
    uploadingStatus: function(n, m) { return 'Laster opp ' + n + ' av ' + m; },
    uploadingInstructions: 'Behold dette vinduet åpent mens opplastingen pågår',
    uploadLimitWarning: function(n) { return 'Du kan laste opp ' + n + ' filer om gangen. '; },
    uploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Du har lagt til maksimalt antall filer. ';
            case 1: return 'Du kan laste opp 1 fil til. ';
            default: return 'Du kan laste opp ' + n + ' filer til. ';
        }
    },
    iHaveTheRight: 'Jeg har rett til å laste opp disse filene i henhold til <a href="/main/authorization/termsOfService">Tjenestevilkår</a>',
    updateJavaTitle: 'Oppdater Java',
    updateJavaDescription: 'Masseopplasteren krever en nyere Java-versjon. Klikk "Okay" for å skaffe Java.',
    batchEditorLabel: 'Rediger informasjon for Alle elementer',
    applyThisInfo: 'Bruk denne informasjonen for følgende filer',
    titleProperty: 'Tittel',
    descriptionProperty: 'Beskrivelse',
    tagsProperty: 'Koder',
    viewableByProperty: 'Kan sees av',
    viewableByEveryone: 'Alle',
    viewableByFriends: 'Bare vennene mine',
    viewableByMe: 'Bare meg',
    albumProperty: ' Album  ',
    artistProperty: 'Artist',
    enableDownloadLinkProperty: 'Aktiver nedlastingslenke',
    enableProfileUsageProperty: 'Tillat folk å sette denne sangen på sine sider',
    licenseProperty: 'Lisens',
    creativeCommonsVersion: '3.0',
    selectLicense: '- Velg lisens -',
    copyright: '© Med enerett',
    ccByX: function(n) { return 'Creative Commons-attribusjon ' + n; },
    ccBySaX: function(n) { return 'Creative Commons-attribusjonen Share Alike ' + n; },
    ccByNdX: function(n) { return 'Creative Commons-attribusjonen No Derivatives ' + n; },
    ccByNcX: function(n) { return 'Creative Commons-attribusjonen Non-commercial ' + n; },
    ccByNcSaX: function(n) { return 'Creative Commons-attribusjonen Non-commercial Share Alike ' + n; },
    ccByNcNdX: function(n) { return 'Creative Commons-attribusjonen Non-commercial No Derivatives ' + n; },
    publicDomain: 'Offentlig eiendom',
    other: 'Annet',
    errorUnexpectedTitle: 'Uff da!',
    errorUnexpectedDescription: 'Det oppstod en feil. Prøv på nytt.',
    errorTooManyTitle: 'For mange elementer',
    errorTooManyDescription: function(n) { return 'Beklager, men du kan bare laste opp ' + n + ' elementer om gangen. '; },
    errorNotAMemberTitle: 'Ikke tillatt',
    errorNotAMemberDescription: 'Beklager, men du må være medlem for å kunne laste opp.',
    errorContentTypeNotAllowedTitle: 'Ikke tillatt',
    errorContentTypeNotAllowedDescription: 'Beklager, men du har ikke tillatelse til å laste opp denne typen innhold.',
    errorUnsupportedFormatTitle: 'Uff da!',
    errorUnsupportedFormatDescription: 'Beklager, men vi støtter ikke denne filtypen.',
    errorUnsupportedFileTitle: 'Uff da!',
    errorUnsupportedFileDescription: 'foo.exe er i et format som ikke støttes.',
    errorUploadUnexpectedTitle: 'Uff da!',
    errorUploadUnexpectedDescription: function(file) {
		return file ?
			('Det er et problem med filen  ' + file + '. Fjern den fra listen før du laster opp resten av filene.') :
			'Det er et problem med den øverste filen på listen. Fjern den før du laster opp resten av filene.';
	},
    cancelUploadTitle: 'Avbryt opplasting?',
    cancelUploadDescription: 'Er du sikker på at du vil avbryte gjenværende opplastinger?',
    uploadSuccessfulTitle: 'Opplasting er fullført',
    uploadSuccessfulDescription: 'Vent litt mens vi tar deg til dine opplastinger…',
    uploadPendingDescription: 'Filene er lastet opp og avventer godkjenning.',
    photosUploadHeader: 'Bilder til opplasting',
    photosDragOutInstructions: 'Dra ut bilder for å fjerne dem',
    photosDragInInstructions: 'Dra bildene hit',
    photosSelectInstructions: 'Velg et bilde',
    photosFiles: 'Bilder',
    photosUploadingStatus: function(n, m) { return 'Laster opp bilde ' + n + ' av ' + m; },
    photosErrorTooManyTitle: 'For mange bilder',
    photosErrorTooManyDescription: function(n) { return 'Beklager, men du kan bare laste opp ' + n + ' bilder om gangen. '; },
    photosErrorContentTypeNotAllowedDescription: 'Beklager, men bildeopplasting er deaktivert.',
    photosErrorUnsupportedFormatDescription: 'Beklager, men du kan bare laste opp bilder i formatene jpg, gif eller png.',
    photosErrorUnsupportedFileDescription: function(n) { return n + ' er ikke en fil av typen jpg, gif eller png.'; },
    photosBatchEditorLabel: 'Rediger informasjon for Alle bilder',
    photosApplyThisInfo: 'Bruk denne informasjonen for følgende bilder',
    photosErrorUploadUnexpectedDescription: function(file) {
		return file ?
			('Det er et problem med filen  ' + file + '. Fjern den fra listen før du laster opp resten av bildene.') :
			'Det er et problem med det øverste bildet på listen. Fjern det før du laster opp resten av bildene.';
	},
    photosUploadSuccessfulDescription: 'Vent litt mens vi tar deg til bildene…',
    photosUploadPendingDescription: 'Bildene er lastet opp og avventer godkjenning.',
    photosUploadLimitWarning: function(n) { return 'Du kan laste opp ' + n + ' bilder om gangen. '; },
    photosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Du har lagt til maksimalt antall bilder. ';
            case 1: return 'Du kan laste opp 1 bilde til. ';
            default: return 'Du kan laste opp ' + n + ' bilder til. ';
        }
    },
    photosIHaveTheRight: 'Jeg har rett til å laste opp disse bildene i henhold til <a href="/main/authorization/termsOfService">Tjenestevilkår</a>',
    videosUploadHeader: 'Videoer til opplasting',
    videosDragInInstructions: 'Dra videoer hit',
    videosDragOutInstructions: 'Dra ut videoer for å fjerne dem',
    videosSelectInstructions: 'Velg en video',
    videosFiles: 'Videoer',
    videosUploadingStatus: function(n, m) { return 'Laster opp video ' + n + ' av ' + m; },
    videosErrorTooManyTitle: 'For mange videoer',
    videosErrorTooManyDescription: function(n) { return 'Beklager, men du kan bare laste opp ' + n + ' videoer om gangen. '; },
    videosErrorContentTypeNotAllowedDescription: 'Beklager, men videoopplasting er deaktivert.',
    videosErrorUnsupportedFormatDescription: 'Beklager, men du kan bare laste opp videoer i formatene avi, mov, mp4, wmv eller mpg.',
    videosErrorUnsupportedFileDescription: function(x) { return x + ' er ikke en fil av typen avi, mov, mp4, wmv eller mpg.'; },
    videosBatchEditorLabel: 'Rediger informasjon for Alle videoer',
    videosApplyThisInfo: 'Bruk denne informasjonen for følgende videoer',
    videosErrorUploadUnexpectedDescription: function(file) {
		return file ?
			('Det er et problem med filen  ' + file + '. Fjern den fra listen før du laster opp resten av videoene.') :
			'Det er et problem med den øverste videoen på listen. Fjern den før du laster opp resten av videoene.';
	},
    videosUploadSuccessfulDescription: 'Vent litt mens vi tar deg til videoene…',
    videosUploadPendingDescription: 'Videoene er lastet opp og venter på godkjenning.',
    videosUploadLimitWarning: function(n) { return 'Du kan laste opp ' + n + ' videoer om gangen. '; },
    videosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Du har lagt til maksimalt antall videoer. ';
            case 1: return 'Du kan laste opp 1 video til. ';
            default: return 'Du kan laste opp ' + n + ' videoer til. ';
        }
    },
    videosIHaveTheRight: 'Jeg har rett til å laste opp disse videoene i henhold til <a href="/main/authorization/termsOfService">Tjenestevilkår</a>',
    musicUploadHeader: 'Sanger til opplasting',
    musicTitleProperty: 'Sangtittel',
    musicDragOutInstructions: 'Dra ut sanger for å fjerne dem',
    musicDragInInstructions: 'Dra sanger hit',
    musicSelectInstructions: 'Velg en sang',
    musicFiles: 'Sanger',
    musicUploadingStatus: function(n, m) { return 'Laster opp sang ' + n + ' av ' + m; },
    musicErrorTooManyTitle: 'For mange sanger',
    musicErrorTooManyDescription: function(n) { return 'Beklager, men du kan bare laste opp ' + n + ' sanger om gangen. '; },
    musicErrorContentTypeNotAllowedDescription: 'Beklager, men sangopplasting er deaktivert.',
    musicErrorUnsupportedFormatDescription: 'Beklager, men du kan bare laste opp sanger i mp3-format.',
    musicErrorUnsupportedFileDescription: function(x) { return x + ' er ikke en mp3-fil.'; },
    musicBatchEditorLabel: 'Rediger informasjon for Alle sanger',
    musicApplyThisInfo: 'Bruk denne informasjonen for følgende sanger',
    musicErrorUploadUnexpectedDescription: function(file) {
		return file ?
			('Det er et problem med filen  ' + file + '. Fjern den fra listen før du laster opp resten av sangene.') :
			'Det er et problem med den øverste sangen på listen. Fjern den før du laster opp resten av sangene.';
	},
    musicUploadSuccessfulDescription: 'Vent litt mens vi tar deg til sangene…',
    musicUploadPendingDescription: 'Sangene er lastet opp og venter på godkjenning.',
    musicUploadLimitWarning: function(n) { return 'Du kan laste opp ' + n + ' sanger om gangen. '; },
    musicUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Du har lagt til maksimalt antall sanger. ';
            case 1: return 'Du kan laste opp 1 sang til. ';
            default: return 'Du kan laste opp ' + n + ' sanger til. ';
        }
    },
    musicIHaveTheRight: 'Jeg har rett til å laste opp disse sangene i henhold til <a href="/main/authorization/termsOfService">Tjenestevilkår</a>'
});


dojo.evalObjPath('xg.forum.nls', true);
dojo.lang.mixin(xg.forum.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Antall tegn (' + n + ') overstiger maksimumsantallet (' + maximum + ') '; },
    pleaseEnterFirstPost: 'Skriv det første diskusjonsinnlegget',
    pleaseEnterTitle: 'Legg inn en tittel på diskusjonen',
    save: 'Lagre',
    cancel: 'Avbryt',
    yes: 'Ja',
    no: 'Nei',
    edit: 'Rediger',
    deleteCategory: 'Slett kategori',
    discussionsWillBeDeleted: 'Diskusjonene i denne kategorien vil bli slettet.',
    whatDoWithDiscussions: 'Hva vil du gjøre med diskusjonene i denne kategorien?',
    moveDiscussionsTo: 'Flytt diskusjonene til:',
    moveToCategory: 'Flytt til kategori...',
    deleteDiscussions: 'Slett diskusjoner',
    'delete': 'Slett',
    deleteReply: 'Slett svar',
    deleteReplyQ: 'Slette dette svaret?',
    deletingReplies: 'Sletter svar...',
    doYouWantToRemoveReplies: 'Vil du også slette svarene på denne kommentaren?',
    pleaseKeepWindowOpen: 'La nettleservinduet være åpent mens behandlingen pågår.  Det kan ta et par minutter.',
    from: 'Fra',
    show: 'Vis',
    discussions: 'diskusjoner',
    discussionsFromACategory: 'Diskusjoner fra en kategori...',
    display: 'Vis',
    items: 'elementer',
    view: 'Vis'
});


dojo.evalObjPath('xg.groups.nls', true);
dojo.lang.mixin(xg.groups.nls, xg.index.i18n, {
    pleaseChooseAName: 'Velg et navn på gruppen din.',
    pleaseChooseAUrl: 'Velg en nettadresse for gruppen din.',
    urlCanContainOnlyLetters: 'Nettadressen kan kun inneholde bokstaver og tall (ingen mellomrom).',
    descriptionTooLong: function(n, maximum) { return 'Lengden på beskrivelsen av gruppen din (' + n + ') overstiger maksimumslengden (' + maximum + ') '; },
    nameTaken: 'Vi beklager – dette navnet er allerede i bruk.  Vennligst velg et annet navn.',
    urlTaken: 'Vi beklager – denne nettadressen er allerede i bruk.  Vennligst velg en annen nettadresse.',
    whyNot: 'Hvorfor ikke?',
    groupCreatorDetermines: function(href) { return 'Oppretteren av gruppen bestemmer hvem som kan bli med.  Hvis du mener at du har blitt blokkert ved en feiltagelse, vennligst <a ' + href + '>ta kontakt med oppretteren av gruppen</a> '; },
    edit: 'Rediger',
    from: 'Fra',
    show: 'Vis',
    groups: 'grupper',
    pleaseEnterName: 'Legg inn navnet ditt',
    pleaseEnterEmailAddress: 'Legg inn e-postadressen din',
    xIsNotValidEmailAddress: function(x) { return x + ' is not a valid email address'; },
    save: 'Lagre',
    cancel: 'Avbryt'
});


dojo.evalObjPath('xg.html.nls', true);
dojo.lang.mixin(xg.html.nls, xg.index.i18n, {
    edit: 'Rediger',
    save: 'Lagre',
    cancel: 'Avbryt',
    saving: 'Lagrer...',
    addAWidget: function(url) { return '<a href="' + url + '">Legg til en innretning</a> til dette tekstvinduet '; },
    contentsTooLong: 'contentsTooLong: function(maximum) { return \'Innholdet er for langt. Bruk færre enn \' + maksimum + \' tegn. \'; }'
});


dojo.evalObjPath('xg.index.nls', true);
dojo.lang.mixin(xg.index.nls, xg.index.i18n, {
    sendInvitation: 'Send invitasjon',
    sendInvitationToNFriends: function(n) {
        switch(n) {
            case 1: return 'Send invitasjon til 1 venn? ';
            default: return 'Send invitasjon til ' + n + ' venner? ';
        }
    },
    showingNFriends: function(n, searchString) {
        switch(n) {
            case 1: return 'Viser 1 venn som samsvarer med "' + searchString + '". <a href="#">Vis alle</a> ';
            default: return 'Viser ' + n + ' venner som samsvarer med "' + searchString + '". <a href="#">Vis alle</a> ';
        }
    },
    sendMessage: 'Send melding',
    sendMessageToNFriends: function(n) {
        switch(n) {
            case 1: return 'Vil du sende melding til 1 venn? ';
            default: return 'Vil du sende melding til ' + n + ' venner? ';
        }
    },
    invitingNFriends: function(n) {
        switch(n) {
            case 1: return 'Inviterer 1 venn… ';
            default: return 'Inviterer ' + n + ' venner… ';
        }
    },
    nFriends: function(n) {
        switch(n) {
            case 1: return '1 venn… ';
            default: return n + ' venner… ';
        }
    },
    sendingMessageToNFriends: function(n) {
        switch(n) {
            case 1: return 'Sender melding til 1 venn… ';
            default: return 'Sender melding til ' + n + ' venner… ';
        }
    },
    noPeopleSelected: 'Ingen personer er valgt',
    sorryWeDoNotSupport: 'Beklager, men vi støtter ikke webadresseboken for din e-postadresse. Prøv å klikke \'Address Book Application\' (Adressebokprogrammet) nedenfor for å bruke adresser fra datamaskinen.',
    pleaseChooseFriends: 'Velg noen venner før du sender meldingen.',
    htmlNotAllowed: 'HTML ikke tillatt',
    noFriendsFound: 'Finner ingen venner som samsvarer med søket.',
    yourMessageOptional: '<label>Din melding</label> (valgfritt)',
    pleaseChoosePeople: 'Velg noen du ønsker å invitere.',
    pleaseEnterEmailAddress: 'Legg inn e-postadressen din.',
    pleaseEnterPassword: function(emailAddress) { return 'Legg inn et passord for ' + emailAddress + '. '; },
    sorryWeDontSupport: 'Beklager, vi støtter ikke nettbasert adressebok for e-postadressen din.  Prøv å klikke på \'E-postprogram\' nedenfor for å bruke adresser fra datamaskinen din.',
    pleaseSelectSecondPart: 'Velg andre del av e-postadressen din, f. eks. gmail. com.',
    atSymbolNotAllowed: 'Sørg for at @-symbolet ikke er i den første delen av e-postadressen din.',
    resetTextQ: 'Tilbakestille tekst?',
    resetTextToOriginalVersion: 'Er du sikker på at du ønsker å tilbakestille all tekst til originalversjonen?  Alle endringer du har gjort vil gå tapt.',
    changeQuestionsToPublic: 'Endre spørsmål til offentlig?',
    changingPrivateQuestionsToPublic: 'Hvis du endrer spørsmål fra privat til offentlig vil alle medlemmers svar vises.  Er du sikker?',
    youHaveUnsavedChanges: 'Du har gjort endringer som ikke er blitt lagret.',
    pleaseEnterASiteName: 'Legg inn et navn på det sosiale nettverket, f. eks. Små klovners klubb',
    pleaseEnterShorterSiteName: 'Legg inn et kortere navn (maks 64 tegn)',
    pleaseEnterShorterSiteDescription: 'Legg inn en kortere beskrivelse (maks 250 tegn)',
    siteNameHasInvalidCharacters: 'Navnet inneholder ugyldige tegn',
    thereIsAProblem: 'Et problem har oppstått med informasjonen din',
    thisSiteIsOnline: 'Dette sosiale nettverket er online',
    onlineSiteCanBeViewed: '<strong>Online</strong> - nettverket kan vises i henhold til dine personverninnstillinger.',
    takeOffline: 'Ta nettverket offline',
    thisSiteIsOffline: 'Dette sosiale nettverket er Offline',
    offlineOnlyYouCanView: '<strong>Offline</strong> - Du er den eneste som kan se dette sosiale nettverket.',
    takeOnline: 'Ta nettverket online',
    themeSettings: 'Temainnstillinger',
    addYourOwnCss: 'Avansert',
    error: 'Feil',
    pleaseEnterTitleForFeature: function(displayName) { return 'Legg inn en tittel på ' + displayName + '- funksjonen din '; },
    thereIsAProblemWithTheInformation: 'Det har oppstått et problem med informasjon som er lagt inn',
    photos: 'Bilder',
    videos: 'Videoer',
    pleaseEnterTheChoicesFor: function(questionTitle) { return 'Legg inn valgene for "' + questionTitle + '" f. eks. gå på tur, lese, shoppe '; },
    pleaseEnterTheChoices: 'Legg inn valgene, f. eks. gå på tur, lese, shoppe',
    shareWithFriends: 'Del med venner',
    email: 'e-post',
    separateMultipleAddresses: 'Adskill hver adresse med et komma',
    subject: 'Emne',
    message: 'Melding',
    send: 'Send',
    cancel: 'Avbryt',
    pleaseEnterAValidEmail: 'Legg inn en gyldig e-postadresse',
    go: 'Gå til',
    areYouSureYouWant: 'Er du sikker på at du vil gjøre dette?',
    processing: 'Behandling pågår...',
    pleaseKeepWindowOpen: 'La nettleservinduet være åpent mens behandlingen pågår.  Det kan ta et par minutter.',
    complete: 'Ferdig!',
    processIsComplete: 'Behandlingen er ferdig.',
    ok: 'OK',
    body: 'Brødtekst',
    pleaseEnterASubject: 'Legg inn et emne',
    pleaseEnterAMessage: 'Legg inn en melding',
    thereHasBeenAnError: 'Det har oppstått en feil',
    fileNotFound: 'Fil ikke funnet',
    pleaseProvideADescription: 'Oppgi en beskrivelse',
    pleaseEnterYourFriendsAddresses: 'Legg inn dine venners adresse eller Ning-ID',
    pleaseEnterSomeFeedback: 'Legg inn tilbakemelding',
    title: 'Tittel:',
    setAsMainSiteFeature: 'Still inn som hovedfunksjon',
    thisIsTheMainSiteFeature: 'Dette er hovedfunksjonen',
    customized: 'Tilpasset',
    copyHtmlCode: 'Kopier HTML-koden',
    playerSize: 'Størrelse på spilleren',
    selectSource: 'Velg kilde',
    myAlbums: 'Mine album',
    myMusic: 'Min musikk',
    myVideos: 'Mine videoer',
    showPlaylist: 'Vis spilleliste',
    change: 'Endre',
    changing: 'Endrer...',
    changePrivacy: 'Endre personvern?',
    keepWindowOpenWhileChanging: 'La nettleservinduet være åpent mens personvernsinnstillingene endres.  Dette kan ta et par minutter.',
    addingInstructions: 'Behold dette vinduet åpent mens innholdet ditt legges til.',
    addingLabel: 'Legger til...',
    cannotKeepFiles: 'Du må velge filene dine om igjen hvis du ønsker å se flere alternativer.  Vil du fortsette?',
    done: 'Ferdig',
    looksLikeNotImage: 'En eller flere av filene er ikke i .jpg-, .gif-, eller .png-format.  Vil du prøve å laste opp likevel?',
    looksLikeNotMusic: 'Filen du har valgt er ikke i .mp3-format.  Vil du prøve å laste opp likevel?',
    looksLikeNotVideo: 'Filen du har valgt er ikke i .mov-, .mpg-, .mp4-, .avi-, .3gp- eller .wmv-format.  Vil du prøve å laste opp likevel?',
    messageIsTooLong: function(n) { return 'Meldingen din er for lang.  Bruk '+n+' tegn eller færre.'; },
    pleaseSelectPhotoToUpload: 'Velg et bilde som skal lastes opp.',
    processingFailed: 'Beklager, behandlingen mislyktes.  Prøv igjen senere.',
    selectOrPaste: 'Du må velge en video eller lime inn \'embed\'-koden',
    selectOrPasteMusic: 'Du må velge en sang eller lime inn URL-en',
    sendingLabel: 'Sender...',
    thereWasAProblem: 'Det oppsto et problem da du skulle legge til innholdet ditt.  Prøv igjen senere.',
    uploadingInstructions: 'Behold dette vinduet åpent mens opplastingen pågår',
    uploadingLabel: 'Laster opp...',
    youNeedToAddEmailRecipient: 'Du må legge til en e-postmottaker.',
    yourMessage: 'Meldingen din',
    yourMessageIsBeingSent: 'Meldingen din sendes.',
    yourSubject: 'Ditt tema'
});


dojo.evalObjPath('xg.music.nls', true);
dojo.lang.mixin(xg.music.nls, xg.index.i18n, {
    shufflePlaylist: 'Stokk spilleliste',
    play: 'spille av',
    pleaseSelectTrackToUpload: 'Velg en sang som skal lastes opp',
    pleaseEnterTrackLink: 'Legg inn URL-addressen til sangen',
    thereAreUnsavedChanges: 'Det er gjort endringer som ikke er blitt lagret.',
    autoplay: 'Autoavspilling',
    showPlaylist: 'Vis spilleliste',
    playLabel: 'Spill av',
    url: 'URL-adresse',
    rssXspfOrM3u: 'rss, xspf, eller m3u',
    save: 'Lagre',
    cancel: 'Avbryt',
    edit: 'Rediger',
    fileIsNotAnMp3: 'En av disse filene er visst ikke en MP3-fil.  Vil du prøve å laste den opp likevel?',
    entryNotAUrl: 'En av disse oppføringene er ikke en URL-adresse.  Sørg for at alle oppføringer begynner med <kbd>http://</kbd>'
});

dojo.evalObjPath('xg.notes.nls', true);
dojo.lang.mixin(xg.notes.nls, xg.index.i18n, {
    addNewNote: 'Legg til nytt notat',
    noteTitleTooLong: 'Tittel på notatet er for lang',
    pleaseEnterNoteEntry: 'Legg inn et notatinnlegg',
    pleaseEnterNoteTitle: 'Legg inn en tittel på notatet!'
});

dojo.evalObjPath('xg.page.nls', true);
dojo.lang.mixin(xg.page.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Antall tegn (' + n + ') overstiger maksimumsantallet (' + maximum + ') '; },
    pleaseEnterContent: 'Legg inn sidens innhold',
    pleaseEnterTitle: 'Legg inn en tittel på siden',
    pleaseEnterAComment: 'Legg inn en kommentar',
    deleteThisComment: 'Er du sikker på at du vil slette denne kommentaren?',
    save: 'Lagre',
    cancel: 'Avbryt',
    discussionTitle: 'Sidetittel:',
    tags: 'Merker:',
    edit: 'Rediger',
    close: 'Lukk',
    displayPagePosts: 'Vis sideinnlegg',
    thereIsAProblem: 'Det er et problem med informasjonen din'
});


dojo.evalObjPath('xg.photo.nls', true);
dojo.lang.mixin(xg.photo.nls, xg.index.i18n, {
    randomOrder: 'Tilfeldig rekkefølge',
    untitled: 'Uten tittel',
    photos: 'Bilder',
    edit: 'Rediger',
    photosFromAnAlbum: 'Album',
    show: 'Vis',
    rows: 'rader',
    cancel: 'Avbryt',
    save: 'Lagre',
    deleteThisPhoto: 'Slette dette bildet?',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Antall tegn (' + n + ') overstiger maksimumsantallet (' + maximum + ') '; },
    weCouldNotLookUpAddress: function(address) { return 'Beklager, vi kunne ikke slå opp adressen "' + address + '". '; },
    pleaseSelectPhotoToUpload: 'Velg et bilde som skal lastes opp.',
    pleaseEnterAComment: 'Legg inn en kommentar.',
    addToExistingAlbum: 'Legg til eksisterende album',
    addToNewAlbumTitled: 'Legg til et nytt album kalt...',
    deleteThisComment: 'Slette denne kommentaren?',
    importingNofMPhotos: function(n,m) { return 'Importerer <span id="currentP">' + n + '</span> av ' + m + ' bilder. '},
    starting: 'Starter...',
    done: 'Ferdig!',
    from: 'Fra',
    display: 'Vis',
    takingYou: 'Vis bildene dine...',
    anErrorOccurred: 'Dessverre har det oppstått en feil.  Meld fra om dette problemet ved å bruke lenka nederst på siden.',
    weCouldntFind: 'Vi kunne ikke finne noen bilder!  Hvorfor ikke prøve noen av de andre alternativene?'
});


dojo.evalObjPath('xg.activity.nls', true);
dojo.lang.mixin(xg.activity.nls, xg.index.i18n, {
    edit: 'Rediger',
    show: 'Vis',
    events: 'begivenheter',
    setWhatActivityGetsDisplayed: 'Still inn hvilken aktivitet som skal vises',
    save: 'Lagre',
    cancel: 'Avbryt'
});


dojo.evalObjPath('xg.profiles.nls', true);
dojo.lang.mixin(xg.profiles.nls, xg.index.i18n, {
    pleaseEnterValueForPost: 'Legg inn en verdi for innlegget',
    pleaseProvideAValidDate: 'Oppgi en gyldig dato',
    uploadAFile: 'Last opp en fil',
    pleaseEnterUrlOfLink: 'Legg inn URL-adressen til lenka:',
    pleaseEnterTextOfLink: 'Hva er teksten du vil lenke til?',
    edit: 'Rediger',
    recentlyAdded: 'Nylig lagt til',
    featured: 'Presentert',
    iHaveRecentlyAdded: 'Jeg har nylig lagt til',
    fromTheSite: 'Fra det sosiale nettverket',
    cancel: 'Avbryt',
    save: 'Lagre',
    loading: 'Laster...',
    addAsFriend: 'Legg til som venn',
    requestSent: 'Forespørsel er sendt!',
    sendingFriendRequest: 'Sender venneforespørsel',
    thisIsYou: 'Dette er deg!',
    isYourFriend: 'Er din venn',
    isBlocked: 'Er blokkert',
    pleaseEnterAComment: 'Legg inn en kommentar',
    pleaseEnterPostBody: 'Legg inn noe som innleggsbrødtekst',
    pleaseSelectAFile: 'Velg en fil',
    pleaseEnterChatter: 'Legg inn noe som din kommentar',
    toggleBetweenHTML: 'Vis/skjul HTML-koden',
    attachAFile: 'Legg ved en fil',
    addAPhoto: 'Legg til et bilde',
    insertALink: 'Sett inn en lenke',
    changeTextSize: 'Endre skriftstørrelse',
    makeABulletedList: 'Lag en punktliste',
    makeANumberedList: 'Lag en nummerert liste',
    crossOutText: 'Stryk ut skrift',
    underlineText: 'Understrek skrift',
    italicizeText: 'Kursiver skrift',
    boldText: 'Fet skrift',
    letMeApproveChatters: 'La meg godkjenne kommentarer før de legges ut?',
    noPostChattersImmediately: 'Nei – legg ut kommentarer med én gang',
    yesApproveChattersFirst: 'Ja – godkjenn kommentarer først',
    yourCommentMustBeApproved: 'Kommentaren din må godkjennes før alle kan se den.',
    reallyDeleteThisPost: 'Vil du virkelig slette dette innlegget?',
    commentWall: ' Kommentarvegg  ',
    commentWallNComments: function(n) {
        switch(n) {
            case 1: return 'Kommentarvegg (1 kommentar) ';
            default: return 'Kommentarvegg (' + n + ' kommentarer) ';
        }
    },
    display: 'Vis',
    from: 'Fra',
    show: 'Vis',
    rows: 'rader',
    posts: 'innlegg'
});


dojo.evalObjPath('xg.shared.nls', true);
dojo.lang.mixin(xg.shared.nls, xg.index.i18n, {
    uploadAPhoto: 'Last opp et bilde',
    uploadAnImage: 'Last opp et bilde',
    uploadAPhotoEllipsis: 'Last opp et bilde...',
    useExistingImage: 'Bruk eksisterende bilde:',
    existingImage: 'Eksisterende bilde',
    useThemeImage: 'Bruk temabilde:',
    themeImage: 'Temabilde',
    noImage: 'Intet bilde',
    uploadImageFromComputer: 'Last opp et bilde fra datamaskinen din',
    tileThisImage: 'Vis dette bildet som store ikoner',
    done: 'Ferdig',
    currentImage: 'Nåværende bilde',
    pickAColor: 'Velg en farge...',
    openColorPicker: 'Åpne fargevelger',
    loading: 'Laster...',
    ok: 'OK',
    save: 'Lagre',
    cancel: 'Avbryt',
    saving: 'Lagrer...',
    addAnImage: 'Legg til bilde',
    bold: 'Fet',
    italic: 'Kursiv',
    underline: 'Understreking',
    strikethrough: 'Utstryking',
    addHyperink: 'Legg til hyperkobling',
    options: 'Alternativer',
    wrapTextAroundImage: 'Vil du legge teksten rundt bildet?',
    imageOnLeft: 'Bilde til venstre?',
    imageOnRight: 'Bilde til høyre?',
    createThumbnail: 'Lag miniatyrbilde?',
    pixels: 'piksler',
    createSmallerVersion: 'Lag en mindre versjon av bildet ditt som vil vises.  Still inn bredde i piksler.',
    popupWindow: 'Sprettoppvindu?',
    linkToFullSize: 'Lenke til full versjon av bildet i et sprettoppvindu.',
    add: 'Legg til',
    keepWindowOpen: 'La nettleservinduet være åpent mens opplastingen pågår.',
    cancelUpload: 'Avbryt opplasting',
    pleaseSelectAFile: 'Velg en bildefil',
    pleaseSpecifyAThumbnailSize: 'Spesifiser en miniatyrbildestørrelse',
    thumbnailSizeMustBeNumber: 'Miniatyrbildestørrelsen må være et tall',
    addExistingImage: 'eller legg inn et eksisterende bilde',
    clickToEdit: 'Klikk her for å redigere',
    sendingFriendRequest: 'Sender venneforespørsel',
    requestSent: 'Forespørsel er sendt!',
    pleaseCorrectErrors: 'Vennligst rett opp disse feilene',
    tagThis: 'Merk dette',
    addOrEditYourTags: 'Legg til eller rediger merkene dine:',
    addYourRating: 'Legg til din rangering:',
    separateMultipleTagsWithCommas: 'Adskill hvert merke med et komma, f. eks. kul, "New Zealand"',
    saved: 'Lagret!',
    noo: 'NY',
    none: 'INGEN',
    joinNow: 'Bli med nå',
    join: 'Bli med',
    youHaventRated: 'Du har ikke rangert dette elementet ennå.',
    yourRatedThis: function(n) {
        switch(n) {
            case 1: return 'Du ga dette elementet 1 stjerne. ';
            default: return 'Du ga dette elementet ' + n + ' stjerner. ';
        }
    },
    yourRatingHasBeenAdded: 'Din rangering har blitt lagt til.',
    thereWasAnErrorRating: 'Det oppsto en feil i rangeringen av dette innholdet.',
    yourTagsHaveBeenAdded: 'Dine merker har blitt lagt til.',
    thereWasAnErrorTagging: 'Det oppsto en feil under merkingen.',
    addToFavorites: 'Legg til i favoritter',
    removeFromFavorites: 'Fjern fra favoritter',
    nStarsOutOfM: function(n,m) {
        switch(n) {
            case 1: return '1 stjerne av ' + m;
            default: return n + ' stjerner av ' + m;
        }
    },
    follow: 'Følg etter',
    stopFollowing: 'Slutt med å følge etter',
    pendingPromptTitle: 'Påventer godkjenning av medlemskap',
    youCanDoThis: 'Du kan gjøre dette når medlemskapet ditt har blitt godkjent av administratorene.',
    pleaseEnterAComment: 'Legg inn en kommentar',
    pleaseEnterAFileAddress: 'Legg inn filadressen',
    pleaseEnterAWebsite: 'Legg inn en nettsideadresse'
});


dojo.evalObjPath('xg.video.nls', true);
dojo.lang.mixin(xg.video.nls, xg.index.i18n, {
    edit: 'Rediger',
    display: 'Vis',
    detail: 'Detalj',
    player: 'Spiller',
    from: 'Fra',
    show: 'Vis',
    videos: 'videoer',
    cancel: 'Avbryt',
    save: 'Lagre',
    saving: 'Lagrer...',
    deleteThisVideo: 'Slette denne videoen?',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Antall tegn (' + n + ') overstiger maksimumsantallet (' + maximum + ') '; },
    weCouldNotLookUpAddress: function(address) { return 'Beklager, vi kunne ikke slå opp adressen "' + address + '". '; },
    approve: 'Godkjenn',
    approving: 'Godkjenner...',
    keepWindowOpenWhileApproving: 'La nettleservinduet være åpent mens videoene godkjennes.  Dette kan ta et par minutter.',
    'delete': 'Slett',
    deleting: 'Sletter...',
    keepWindowOpenWhileDeleting: 'La nettleservinduet være åpent mens videoene slettes.  Dette kan ta et par minutter.',
    pasteInEmbedCode: 'Lim inn embed-koden for en video fra en annen nettside.',
    pleaseSelectVideoToUpload: 'Velg en video som skal lastes opp.',
    embedCodeContainsMoreThanOneVideo: 'Embed-koden inneholder mer enn én video.  Sørg for at den kun har et <object> og/eller <embed> merke.',
    embedCodeMissingTag: 'Embed-koden mangler en &lt; embed&gt;  eller &lt; object&gt;  merke.',
    fileIsNotAMov: 'Denne filen er visst ikke en .mov-, .mpg-, .mp4-, .avi-, .3gp- eller .wmv-fil.  Vil du prøve å laste den opp likevel?',
    pleaseEnterAComment: 'Legg inn en kommentar.',
    youRatedVideoNStars: function(n) {
        switch(n) {
            case 1: return 'Du ga denne videoen 1 stjerne! ';
            default: return 'Du ga denne videoen ' + n + ' stjerner! ';
        }
    },
    deleteThisComment: 'Slette denne kommentaren?',
    embedHTMLCode: 'HTML embed-kode:',
    copyHTMLCode: 'Kopier HTML-koden'
});