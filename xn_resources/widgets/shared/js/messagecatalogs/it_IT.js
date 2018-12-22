dojo.provide('xg.shared.messagecatalogs.it_IT');


dojo.require('xg.index.i18n');

/**
 * Texts for the Italian locale. 
 */
// Use UTF-8 byte sequences instead of HTML entities, e.g., & instead of &amp;, … instead of &hellip;  [Jon Aquino 2007-01-10]

dojo.evalObjPath('xg.events.nls', true);
dojo.lang.mixin(xg.events.nls, xg.index.i18n, {
    pleaseChooseImage: 'Scegli un’immagine per l’evento',
    pleaseEnterAMessage: 'Inserisci un messaggio',
    pleaseEnterDescription: 'Inserisci una descrizione dell’evento',
    pleaseEnterLocation: 'Inserisci il luogo di svolgimento dell’evento',
    pleaseEnterTitle: 'Inserisci il titolo dell’evento',
    pleaseEnterType: 'Inserisci almeno un tipo di evento',
    send: 'Invia',
    sending: 'Invio in corso…',
    thereHasBeenAnError: 'Si è verificato un errore',
    yourMessage: 'Il tuo messaggio',
    yourMessageHasBeenSent: 'Il tuo messaggio è stato inviato.',
    yourMessageIsBeingSent: 'Il messaggio è in corso di invio.'
});

dojo.evalObjPath('xg.feed.nls', true);
dojo.lang.mixin(xg.feed.nls, xg.index.i18n, {
    edit: 'Modifica',
    title: 'Titolo:',
    feedUrl: 'URL:',
    show: 'Mostra:',
    titles: 'Solo titoli',
    titlesAndDescriptions: 'Vista dettaglio',
    display: 'Display',
    cancel: 'Annulla',
    save: 'Salva',
    loading: 'Caricamento...',
    items: 'articoli'
});


dojo.evalObjPath('xg.forum.nls', true);
dojo.lang.mixin(xg.forum.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Il numero di caratteri (' + n + ') supera il numero massimo (' + maximum + ') '; },
    pleaseEnterFirstPost: 'Scrivi il primo post per aprire la discussione',
    pleaseEnterTitle: 'Inserisci un titolo per la discussione',
    save: 'Salva',
    cancel: 'Annulla',
    yes: 'Si',
    no: 'No',
    edit: 'Modifica',
    deleteCategory: 'Elimina categoria',
    discussionsWillBeDeleted: 'Le discussioni in questa categoria saranno eliminate.',
    whatDoWithDiscussions: 'Cosa vuoi fare in merito alle discussioni in questa categoria?',
    moveDiscussionsTo: 'Sposta le discussioni a:',
    moveToCategory: 'Sposta alla Categoria…',
    deleteDiscussions: 'Elimina discussioni',
    'delete': 'Elimina',
    deleteReply: 'Elimina risposta',
    deleteReplyQ: 'Eliminare questa risposta?',
    deletingReplies: 'Eliminazione risposte…',
    doYouWantToRemoveReplies: 'Desideri anche rimuovere le risposte a questo commento?',
    pleaseKeepWindowOpen: 'Mantieni questa finestra del browser aperta durante l’elaborazione.  Potrebbe impiegare qualche minuto.',
    from: 'Da',
    show: 'Mostra',
    discussions: 'discussioni',
    discussionsFromACategory: 'Discussioni da una categoria…',
    display: 'Display',
    items: 'articoli',
    view: 'Visualizza'
});


dojo.evalObjPath('xg.groups.nls', true);
dojo.lang.mixin(xg.groups.nls, xg.index.i18n, {
    pleaseChooseAName: 'Scegli un nome per il tuo gruppo.',
    pleaseChooseAUrl: 'Scegli un indirizzo Web per il tuo gruppo.',
    urlCanContainOnlyLetters: 'L\'indirizzo Web può contenere solo lettere e numeri (non spazi).',
    descriptionTooLong: function(n, maximum) { return 'La lunghezza della descrizione (' + n + ') del tuo gruppo supera il massimo (' + maximum + ') '; },
    nameTaken: 'Spiacenti, quel nome non è disponibile.  Scegli un altro nome.',
    urlTaken: 'Spiacente, quell’indirizzo Web non è disponibile.  Scegli un altro indirizzo Web.',
    whyNot: 'Perché no?',
    groupCreatorDetermines: function(href) { return 'L’autore del gruppo stabilisce chi è abilitato ad entrare.  Se pensi di essere stato erroneamente bloccato, <a ' + href + '>mettiti in contatto con l’autore del gruppo</a> '; },
    edit: 'Modifica',
    from: 'Da',
    show: 'Mostra',
    groups: 'gruppi',
    pleaseEnterName: 'Inserisci il tuo nome',
    pleaseEnterEmailAddress: 'Inserisci il tuo indirizzo e-mail',
    xIsNotValidEmailAddress: function(x) { return x + ' is not a valid email address'; },
    save: 'Salva',
    cancel: 'Annulla'
});


dojo.evalObjPath('xg.html.nls', true);
dojo.lang.mixin(xg.html.nls, xg.index.i18n, {
	  contentsTooLong: function(maximum) { return 'I contenuti sono troppo lunghi. Usa meno di ' + maximum + ' caratteri. '; },
    edit: 'Modifica',
    save: 'Salva',
    cancel: 'Annulla',
    saving: 'Salvataggio in corso...',
    addAWidget: function(url) { return '<a href="' + url + '">Aggiungi un widget</a> a questa casella di testo '; }
});


dojo.evalObjPath('xg.index.nls', true);
dojo.lang.mixin(xg.index.nls, xg.index.i18n, {
    sendInvitation: 'Invia Invito',
    sendInvitationToNFriends: function(n) {
        switch(n) {
            case 1: return 'Inviare invito a 1 amico? ';
            default: return 'Inviare invito a ' + n + ' amici? ';
        }
    },
    yourMessageOptional: '<label>Tuo messaggio</label> (opzionale)',
    pleaseChoosePeople: 'Scegli qualche persona da invitare.',
    pleaseEnterEmailAddress: 'Inserisci il tuo indirizzo e-mail.',
    pleaseEnterPassword: function(emailAddress) { return 'Inserisci la tua password per ' + emailAddress + '. '; },
	sorryWeDoNotSupport: 'Spiacenti, non supportiamo la rubrica web per il tuo indirizzo di posta elettronica. Prova a fare clic su \'Address Book Application\' più in basso per utilizzare gli indirizzi presenti sul tuo computer. ',
    sorryWeDontSupport: 'Spiacenti, non supportiamo la rubrica web per il tuo indirizzo e-mail.  Prova a fare clic su \'Address Book Application\' più in basso per utilizzare gli indirizzi presenti sul tuo computer.',
    pleaseSelectSecondPart: 'Seleziona la seconda parte del tuo indirizzo di posta elettronica, ad esempio, gmail. com.',
    atSymbolNotAllowed: 'Assicurati che il simbolo @ non sia nella prima parte dell\'indirizzo di posta elettronica.',
    resetTextQ: 'Azzerare testo?',
    resetTextToOriginalVersion: 'Sei sicuro di voler azzerare tutto il testo alla versione originale?  Tutte le modifiche andranno perse.',
    changeQuestionsToPublic: 'Rendere pubbliche le domande?',
    changingPrivateQuestionsToPublic: 'La modifica di domande da private a pubbliche esporrà le risposte di tutti i membri.  Sei sicuro?',
    youHaveUnsavedChanges: 'Le modifiche non sono state salvate.',
    pleaseEnterASiteName: 'Inserisci un nome per il social network, ad esempio Club dei Pagliacci',
    pleaseEnterShorterSiteName: 'Inserisci un nome più breve (massimo 64 caratteri)',
    pleaseEnterShorterSiteDescription: 'Inserisci una descrizione più breve (massimo 250 caratteri)',
    siteNameHasInvalidCharacters: 'Il nome contiene caratteri non validi',
    thereIsAProblem: 'Si è verificato un problema con le tue informazioni',
    thisSiteIsOnline: 'Questo social network è in linea',
    onlineSiteCanBeViewed: '<strong>In linea</strong> - Il Network può essere visualizzato riguardo alle tue impostazioni di privacy.',
    takeOffline: 'Utilizza la modalità non in linea',
    thisSiteIsOffline: 'Questo social network è in modalità non in linea',
    offlineOnlyYouCanView: '<strong>Non in linea</strong>- Solo tu puoi visualizzare questo social network.',
    takeOnline: 'Utilizza la modalità non linea',
    themeSettings: 'Impostazioni tema',
    addYourOwnCss: 'Avanzato',
    error: 'Errore',
    pleaseEnterTitleForFeature: function(displayName) { return 'Inserire un titolo per la tua ' + displayName + ' caratteristica '; },
    thereIsAProblemWithTheInformation: 'Si è verificato un problema con l’informazione inserita',
    photos: 'Fotografie',
    videos: 'Video',
    pleaseEnterTheChoicesFor: function(questionTitle) { return 'Inserisci le scelte per "' + questionTitle + '" ad esempio Escursionismo, Lettura, Shopping '; },
    pleaseEnterTheChoices: 'Inserisci le scelte ad esempio Escursionismo, Lettura, Shopping',
    shareWithFriends: 'Condividi con amici',
    email: 'e-mail',
    separateMultipleAddresses: 'Separa indirizzi multipli con la virgola',
    subject: 'Oggetto',
    message: 'Messaggio',
    send: 'Invia',
    cancel: 'Annulla',
    pleaseEnterAValidEmail: 'Inserisci un indirizzo e-mail valido',
    go: 'Vai',
    areYouSureYouWant: 'Sei sicuro di voler fare questo?',
    processing: 'Elaborazione…',
    pleaseKeepWindowOpen: 'Mantieni questa finestra del browser aperta durante l’elaborazione.  Potrebbe impiegare qualche minuto.',
    complete: 'Completato!',
    processIsComplete: 'Il processo è stato completato',
    ok: 'OK',
    body: 'Corpo',
    pleaseEnterASubject: 'Inserisci un oggetto',
    pleaseEnterAMessage: 'Inserisci un messaggio',
    thereHasBeenAnError: 'Si è verificato un errore',
    fileNotFound: 'File non trovato',
    pleaseProvideADescription: 'Fornisci una descrizione',
    pleaseEnterYourFriendsAddresses: 'Inserisci gli indirizzi o identificativi Ning dei tuoi amici',
    pleaseEnterSomeFeedback: 'Inserisci commenti e suggerimenti',
    title: 'Titolo:',
    setAsMainSiteFeature: 'Imposta come Caratteristica Principale',
    thisIsTheMainSiteFeature: 'Questa è la caratteristica principale',
    customized: 'Personalizzato',
    copyHtmlCode: 'Copia codice HTML',
    playerSize: 'Dimensioni lettore',
    selectSource: 'Seleziona database di origine',
    myAlbums: 'Album',
    myMusic: 'Musica',
    myVideos: 'Video',
    showPlaylist: 'Mostra elenchi di riproduzione',
    change: 'Modifica',
    changing: 'Modifica...',
    changePrivacy: 'Modificare le impostazioni relative alla privacy?',
    keepWindowOpenWhileChanging: 'Mantieni questa finestra del browser aperta durante la modifica delle impostazioni di privacy.  Questo processo potrebbe impiegare qualche minuto.',
    htmlNotAllowed: 'HTML non consentito',
    showingNFriends: function(n, searchString) {
        switch(n) {
            case 1: return 'Visualizzazione corrispondenza di 1 amico "' + searchString + '".  <a href="#">Mostra tutti</a> ';
            default: return 'Visualizzazione corrispondenza di ' + n + ' amici "' + searchString + '".  <a href="#">Mostra tutti</a> ';
        }
    },
    sendMessage: 'Invia messaggio',
    sendMessageToNFriends: function(n) {
        switch(n) {
            case 1: return 'Inviare messaggio a 1 amico? ';
            default: return 'Inviare messaggio a ' + n + ' amici? ';
        }
    },
    invitingNFriends: function(n) {
        switch(n) {
            case 1: return 'Invio invito a 1 amico in corso… ';
            default: return 'Invio invito a  ' + n + ' amici in corso… ';
        }
    },
    nFriends: function(n) {
        switch(n) {
            case 1: return '1 amico… ';
            default: return n + ' amici… ';
        }
    },
    sendingMessageToNFriends: function(n) {
        switch(n) {
            case 1: return 'Invio messaggio a 1 amico in corso… ';
            default: return 'Invio messaggio a ' + n + ' amici in corso… ';
        }
    },
    noPeopleSelected: 'Nessuna persona selezionata',
    pleaseChooseFriends: 'Seleziona qualche amico prima di inviare il messaggio.',
    noFriendsFound: 'Impossibile trovare amici corrispondenti alla tua ricerca.',
    addingInstructions: 'Mantieni questa finestra aperta mentre il tuo contenuto viene aggiunto.',
    addingLabel: 'Aggiunta...',
    cannotKeepFiles: 'Se desideri visualizzare ulteriori opzioni, dovrai selezionare nuovamente i file.  Continuare?',
    done: 'Fine',
    looksLikeNotImage: 'Uno o più file non sembrano essere in formato .jpg, .gif o .png.  Desideri provare a caricarli comunque?',
    looksLikeNotMusic: 'Il file selezionato non sembra essere in formato .mp3.  Desideri provare a caricarli comunque?',
    looksLikeNotVideo: 'Il file selezionato non sembra essere in formato .mov, .mpg, .mp4, .avi, .3gp o .wmv.  Desideri provare a caricarli comunque?',
    messageIsTooLong: function(n) { return 'Il tuo messaggio è troppo lungo.  Usa '+n+' caratteri o meno.'; },
    pleaseSelectPhotoToUpload: 'Seleziona una fotografia da caricare.',
    processingFailed: 'Siamo spiacenti, l’elaborazione non è andata a buon fine.  Riprova più tardi.',
    selectOrPaste: 'Devi selezionare un video o incollare il codice \'incorporato\'',
    selectOrPasteMusic: 'Devi selezionare una canzone o incollare l’indirizzo URL',
    sendingLabel: 'Invio in corso...',
    thereWasAProblem: 'Si è verificato un problema durante l’aggiunta del contenuto.  Riprova più tardi.',
    uploadingInstructions: 'Mantieni questa finestra aperta durante il processo di caricamento',
    uploadingLabel: 'Caricamento in corso...',
    youNeedToAddEmailRecipient: 'Devi aggiungere un destinatario e-mail.',
    yourMessage: 'Il tuo messaggio',
    yourMessageIsBeingSent: 'Il messaggio è in corso di invio.',
    yourSubject: 'Oggetto'
});


dojo.evalObjPath('xg.music.nls', true);
dojo.lang.mixin(xg.music.nls, xg.index.i18n, {
    shufflePlaylist: 'Ripetizione casuale della playlist ',
    play: 'riprodurre',
    pleaseSelectTrackToUpload: 'Seleziona una canzone da caricare.',
    pleaseEnterTrackLink: 'Inserisci l’URL di una canzone',
    thereAreUnsavedChanges: 'Sono state apportate modifiche che non sono state salvate.',
    autoplay: 'Autoplay',
    showPlaylist: 'Mostra elenchi di riproduzione',
    playLabel: 'Riproduci',
    url: 'URL',
    rssXspfOrM3u: 'rss, xspf, o m3u',
    save: 'Salva',
    cancel: 'Annulla',
    edit: 'Modifica',
    fileIsNotAnMp3: 'Uno dei file non sembra essere un MP3.  Cercare di caricarlo comunque?',
    entryNotAUrl: 'Una delle voci non sembrerebbe essere un URL.  Assicurati che tutte le voci inizino con <kbd>http://</kbd>'
});

dojo.evalObjPath('xg.notes.nls', true);
dojo.lang.mixin(xg.notes.nls, xg.index.i18n, {
    addNewNote: 'Aggiungi una nuova nota',
    noteTitleTooLong: 'Il titolo della nota è troppo lungo',
    pleaseEnterNoteEntry: 'Inserisci una nota',
    pleaseEnterNoteTitle: 'Inserisci il titolo della nota!'
});

dojo.evalObjPath('xg.page.nls', true);
dojo.lang.mixin(xg.page.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Il numero di caratteri (' + n + ') supera il numero massimo (' + maximum + ') '; },
    pleaseEnterContent: 'Inserisci il contenuto della pagina',
    pleaseEnterTitle: 'Inserisci un titolo per la pagina',
    pleaseEnterAComment: 'Inserisci un commento',
    deleteThisComment: 'Sei sicuro di voler eliminare questo commento?',
    save: 'Salva',
    cancel: 'Annulla',
    discussionTitle: 'Titolo pagina:',
    tags: 'Tag:',
    edit: 'Modifica',
    close: 'Chiudi',
    displayPagePosts: 'Visualizza i post della pagina',
    thereIsAProblem: 'Si è verificato un problema con le tue informazioni'
});


dojo.evalObjPath('xg.photo.nls', true);
dojo.lang.mixin(xg.photo.nls, xg.index.i18n, {
	randomOrder: 'Ordine casuale ',
    untitled: 'Senza titolo',
    photos: 'Fotografie',
    edit: 'Modifica',
    photosFromAnAlbum: 'Album',
    show: 'Mostra',
    rows: 'righe',
    cancel: 'Annulla',
    save: 'Salva',
    deleteThisPhoto: 'Eliminare questa fotografia?',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Il numero di caratteri (' + n + ') supera il numero massimo (' + maximum + ') '; },
    weCouldNotLookUpAddress: function(address) { return 'Spiacenti, non siamo riusciti a cercare l’indirizzo "' + address + '". '; },
    pleaseSelectPhotoToUpload: 'Seleziona una fotografia da caricare.',
    pleaseEnterAComment: 'Inserisci un commento.',
    addToExistingAlbum: 'Aggiungi all’album esistente',
    addToNewAlbumTitled: 'Aggiungi ad un nuovo album intitolato…',
    deleteThisComment: 'Eliminare questo commento?',
      importingNofMPhotos: function(n,m) { return 'Importazione <span id="currentP">' + n + '</span> di ' + m + ' fotografie. '; },
    starting: 'Avvio…',
    done: 'Fatto!',
    from: 'Da',
    display: 'Display',
    takingYou: 'Farti vedere le tue fotografie…',
    anErrorOccurred: 'Si è verificato un errore.  Segnala questo fatto utilizzando il collegamento in fondo alla pagina.',
    weCouldntFind: 'Non abbiamo trovato nessuna fotografia!  Perché non provi una delle altre opzioni?'
});


dojo.evalObjPath('xg.activity.nls', true);
dojo.lang.mixin(xg.activity.nls, xg.index.i18n, {
    edit: 'Modifica',
    show: 'Mostra',
    events: 'eventi',
    setWhatActivityGetsDisplayed: 'Imposta quale attività viene visualizzata',
    save: 'Salva',
    cancel: 'Annulla'
});


dojo.evalObjPath('xg.profiles.nls', true);
dojo.lang.mixin(xg.profiles.nls, xg.index.i18n, {
    pleaseEnterValueForPost: 'Inserisci un valore per il post',
    pleaseProvideAValidDate: 'Fornisci una data valida',
    uploadAFile: 'Carica un file',
    pleaseEnterUrlOfLink: 'Inserisci l’URL del collegamento:',
    pleaseEnterTextOfLink: 'Con quale testo desideri creare il collegamento?',
    edit: 'Modifica',
    recentlyAdded: 'Aggiunto recentemente',
    featured: 'Selezionato',
    iHaveRecentlyAdded: 'Ho aggiunto recentemente',
    fromTheSite: 'Dal social network',
    cancel: 'Annulla',
    save: 'Salva',
    loading: 'Caricamento...',
    addAsFriend: 'Aggiungi come amico',
    requestSent: 'Richiesta inviata!',
    sendingFriendRequest: 'Invio Richiesta amico',
    thisIsYou: 'Questo sei tu!',
    isYourFriend: 'È il tuo amico',
    isBlocked: 'È bloccato',
    pleaseEnterAComment: 'Inserisci un commento',
    pleaseEnterPostBody: 'Inserisci qualcosa per il corpo del post',
    pleaseSelectAFile: 'Seleziona un file',
    pleaseEnterChatter: 'Inserisci qualcosa per il per il tuo commento',
    toggleBetweenHTML: 'Mostra/Nascondi codice HTML',
    attachAFile: 'Allega un file',
    addAPhoto: 'Aggiungi una fotografia',
    insertALink: 'Inserisci un collegamento',
    changeTextSize: 'Modifica le dimensioni del testo',
    makeABulletedList: 'Crea un elenco puntato',
    makeANumberedList: 'Crea un elenco numerato',
    crossOutText: 'Barra il testo',
    underlineText: 'Sottolinea il testo',
    italicizeText: 'Formatta il testo in corsivo',
    boldText: 'Formatta il testo in grassetto',
    letMeApproveChatters: 'Devo approvare i commenti prima della pubblicazione?',
    noPostChattersImmediately: 'No -  pubblica i commenti immediatamente',
    yesApproveChattersFirst: 'Sì - approva i commenti prima',
    yourCommentMustBeApproved: 'Il tuo commento deve essere approvato prima che qualcuno possa vederlo.',
    reallyDeleteThisPost: 'Eliminare questo post?',
    commentWall: 'Spazio commenti',
    commentWallNComments: function(n) {
        switch(n) {
            case 1: return 'Spazio commenti (1 commento) ';
            default: return 'Spazio commenti (' + n + ' commenti) ';
        }
    },
    display: 'Display',
    from: 'Da',
    show: 'Mostra',
    rows: 'righe',
    posts: 'post'
});


dojo.evalObjPath('xg.shared.nls', true);
dojo.lang.mixin(xg.shared.nls, xg.index.i18n, {
    uploadAPhoto: 'Carica una fotografia',
    uploadAnImage: 'Carica un’immagine',
    uploadAPhotoEllipsis: 'Carica una fotografia…',
    useExistingImage: 'Utilizza l’immagine esistente',
    existingImage: 'Immagine esistente',
    useThemeImage: 'Utilizza immagini a tema',
    themeImage: 'Immagini a tema',
    noImage: 'Nessuna immagine',
    uploadImageFromComputer: 'Carica un’immagine dal tuo computer',
    tileThisImage: 'Affianca questa immagine',
    done: 'Fatto',
    currentImage: 'Immagine attiva',
    pickAColor: 'Preleva un colore…',
    openColorPicker: 'Apri prelevatore di colori',
    loading: 'Caricamento...',
    ok: 'OK',
    save: 'Salva',
    cancel: 'Annulla',
    saving: 'Salvataggio in corso...',
    addAnImage: 'Aggiungi un’immagine',
    bold: 'Grassetto',
    italic: 'Corsivo',
    underline: 'Sottolineato',
    strikethrough: 'Barrato',
    addHyperink: 'Aggiungi collegamento ipertestuale',
    options: 'Opzioni',
    wrapTextAroundImage: 'Disporre il testo attorno all’immagine?',
    imageOnLeft: 'Immagine a sinistra?',
    imageOnRight: 'Immagine a destra?',
    createThumbnail: 'Creare anteprima?',
    pixels: 'pixel',
    createSmallerVersion: 'Crea una versione più piccola della tua immagine da visualizzare.  Imposta l’ampiezza in pixel.',
    popupWindow: 'Finestra pop-up?',
    linkToFullSize: 'Collegamento alla versione in grandezza naturale dell’immagine in una finestra a comparsa.',
    add: 'Aggiungi',
    keepWindowOpen: 'Mantieni questa finestra del browser aperta durante il caricamento.',
    cancelUpload: 'Annulla caricamento',
    pleaseSelectAFile: 'Seleziona un file di immagine',
    pleaseSpecifyAThumbnailSize: 'Specifica una dimensione anteprima',
    thumbnailSizeMustBeNumber: 'La dimensione anteprima deve essere un numero',
    addExistingImage: 'o inserisci un’immagine esistente',
    clickToEdit: 'Clicca per modificare',
    sendingFriendRequest: 'Invio Richiesta amico',
    requestSent: 'Richiesta inviata!',
    pleaseCorrectErrors: 'Correggi questi errori',
    tagThis: 'Contrassegna questo',
    addOrEditYourTags: 'Aggiungi o modifica i tuoi tag:',
    addYourRating: 'Aggiungi la tua valutazione:',
    separateMultipleTagsWithCommas: 'Separa tag multipli con la virgola ad esempio fico, “Nuova Zelanda”',
    saved: 'Salvato!',
    noo: 'NUOVO',
    none: 'NESSUNO',
    joinNow: 'Aderisci adesso',
    join: 'Aderisci',
    youHaventRated: 'Non hai ancora valutato questo articolo.',
    yourRatedThis: function(n) {
        switch(n) {
            case 1: return 'Hai valuto questo articolo con 1 stella ';
            default: return 'Hai valutato questo articolo con ' + n + ' stelle. ';
        }
    },
    yourRatingHasBeenAdded: 'La tua valutazione è stata aggiunta.',
    thereWasAnErrorRating: 'Si è verificato un errore nella valutazione di questo contenuto.',
    yourTagsHaveBeenAdded: 'I tuoi tag sono stati aggiunti.',
    thereWasAnErrorTagging: 'Si è verificato un errore nell’aggiunta dei tag.',
    addToFavorites: 'Aggiungi a Preferiti',
    removeFromFavorites: 'Rimuovi da Preferiti',
    nStarsOutOfM: function(n,m) {
        switch(n) {
            case 1: return '1 stella su ' + m;
            default: return n + ' stelle su ' + m;
        }
    },
    follow: 'Segui',
    stopFollowing: 'Non seguire più',
    pendingPromptTitle: 'Iscrizione in corso di approvazione',
    youCanDoThis: 'Puoi fare questo una volta che la tua iscrizione sia stata approvata dagli amministratori.',
    pleaseEnterAComment: 'Inserisci un commento',
    pleaseEnterAFileAddress: 'Inserisci l’indirizzo del file',
    pleaseEnterAWebsite: 'Inserisci un indirizzo web'
});


dojo.evalObjPath('xg.video.nls', true);
dojo.lang.mixin(xg.video.nls, xg.index.i18n, {
    edit: 'Modifica',
    display: 'Display',
    detail: 'Dettaglio',
    player: 'Lettore',
    from: 'Da',
    show: 'Mostra',
    videos: 'video',
    cancel: 'Annulla',
    save: 'Salva',
    saving: 'Salvataggio in corso...',
    deleteThisVideo: 'Eliminare questo video?',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Il numero di caratteri (' + n + ') supera il numero massimo (' + maximum + ') '; },
    weCouldNotLookUpAddress: function(address) { return 'Spiacenti, non siamo riusciti a cercare l’indirizzo "' + address + '". '; },
    approve: 'Approvare',
    approving: 'Approvazione...',
    keepWindowOpenWhileApproving: 'Mantieni aperta questa finestra del browser durante l’approvazione dei video.  Questo processo potrebbe impiegare qualche minuto.',
    'delete': 'Elimina',
    deleting: 'Eliminazione...',
    keepWindowOpenWhileDeleting: 'Mantieni aperta questa finestra del browser durante l’eliminazione dei video.  Questo processo potrebbe impiegare qualche minuto.',
    pasteInEmbedCode: 'Incolla il codice incorporato per un video da un altro sito.',
    pleaseSelectVideoToUpload: 'Seleziona un video da caricare.',
    embedCodeContainsMoreThanOneVideo: 'Il codice incorporato contiene più di un video.  Assicurati che abbia solo un <object> e/o <embed> tag.',
    embedCodeMissingTag: 'Al codice incorporato manca un &lt; embed&gt;  or &lt; object&gt;  tag.',
    fileIsNotAMov: 'Questo file non sembra essere un . mov, . mpg, . mp4, . avi, . 3gp o . wmv.  Cercare di caricarlo comunque?',
    pleaseEnterAComment: 'Inserisci un commento.',
    youRatedVideoNStars: function(n) {
        switch(n) {
            case 1: return 'Hai valutato questo video 1 stella! ';
            default: return 'Hai valutato questo video ' + n + ' stelle! ';
        }
    },
    deleteThisComment: 'Eliminare questo commento?',
    embedHTMLCode: 'Codice HTML incorporato:',
    copyHTMLCode: 'Copia codice HTML'
});


dojo.evalObjPath('xg.gadgets.nls', true);
dojo.lang.mixin(xg.gadgets.nls, xg.index.i18n, {
    edit: 'Modifica',
    title: 'Titolo:',
    feedUrl: 'URL:',
    cancel: 'Annulla',
    save: 'Salva',
    loading: 'Caricamento...',
    removeGadget: 'Rimuovi gadget',
    findGadgetsInDirectory: 'Trova gadget nella Directory dei gadget'
});
dojo.evalObjPath('xg.uploader.nls', true);
dojo.lang.mixin(xg.uploader.nls, xg.index.i18n, {
    fileBrowserHeader: 'Risorse del computer ',
    fileRoot: 'Risorse del computer ',
    fileInformationHeader: 'Informazioni ',
    uploadHeader: 'File da caricare ',
    dragOutInstructions: 'Trascina i file fuori per rimuoverli ',
    dragInInstructions: 'Trascina i file qui ',
    selectInstructions: 'Seleziona un file ',
    files: 'File ',
    totalSize: 'Dimensione totale ',
    fileName: 'Nome ',
    fileSize: 'Dimensione ',
    nextButton: 'Successivo > ',
    okayButton: 'OK ',
    yesButton: 'Si ',
    noButton: 'No ',
    uploadButton: 'Carica ',
    cancelButton: 'Annulla ',
    backButton: 'Indietro ',
    continueButton: 'Continua ',
    uploadingLabel: 'Caricamento in corso... ',
    uploadingStatus: function(n, m) { return 'Caricamento in corso di ' + n + ' di ' + m; },
    uploadingInstructions: 'Mantieni questa finestra aperta durante il processo di caricamento ',
    uploadLimitWarning: function(n) { return 'Puoi caricare ' + n + ' file alla volta. '; },
	uploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Hai aggiunto il numero massimo di file. ';
            case 1: return 'Puoi caricare ancora 1 file. ';
            default: return 'Puoi caricare ancora ' + n + ' file. ';
        }
    },
    iHaveTheRight: 'Sono autorizzato a caricare questi file secondo le <a href="/main/authorization/termsOfService">Condizioni di Servizio</a> ',
    updateJavaTitle: 'Aggiorna Java',
	updateJavaDescription: 'L’uploader richiede una versione più recente di Java. Fare clic su "Okay" per ottenere Java.',	
    batchEditorLabel: 'Modifica informazione per Tutti gli articoli ',
    applyThisInfo: 'Applica questa informazione ai file più in basso ',
    titleProperty: 'Titolo ',
    descriptionProperty: 'Descrizione ',
    tagsProperty: 'Tag ',
    viewableByProperty: 'Possono essere visualizzati da: ',
    viewableByEveryone: 'Tutti ',
    viewableByFriends: 'Solo i miei amici ',
    viewableByMe: 'Solo io ',
    albumProperty: 'album ',
    artistProperty: 'Artista ',
    enableDownloadLinkProperty: 'Abilita il link di download ',
    enableProfileUsageProperty: 'Consenti ad altri di inserire questa canzone nelle loro pagine ',
    licenseProperty: 'Licenza ',
    creativeCommonsVersion: '3.0',
    selectLicense: '— Select license —',
    copyright: '© Tutti i diritti riservati ',
    ccByX: function(n) { return 'Attribuzione Creative Commons ' + n; },
    ccBySaX: function(n) { return 'Attribuzione Creative Commons Condividi allo stesso modo ' + n; },
    ccByNdX: function(n) { return 'Attribuzione Creative Commons Nessun derivato ' + n; },
    ccByNcX: function(n) { return 'Attribuzione Creative Commons Non commerciale ' + n; },
    ccByNcSaX: function(n) { return 'Attribuzione Creative Commons Non commerciale Condividi allo stesso modo ' + n; },
    ccByNcNdX: function(n) { return 'Attribuzione Creative Commons Non commerciale Nessun derivato ' + n; },
    publicDomain: 'Dominio pubblico ',
    other: 'Altro ',
    errorUnexpectedTitle: 'Oops! ',
    errorUnexpectedDescription: 'Si è verificato un errore. Prova di nuovo. ',
    errorTooManyTitle: 'Troppi articoli ',
    errorTooManyDescription: function(n) { return 'Spiacenti, ma puoi soltanto caricare ' + n + ' articoli alla volta. '; },
    errorNotAMemberTitle: 'Non consentito ',
    errorNotAMemberDescription: 'Spiacenti, ma devi essere un membro per effettuare il caricamento. ',
    errorContentTypeNotAllowedTitle: 'Non consentito ',
    errorContentTypeNotAllowedDescription: 'Spiacenti, ma non ti è consentito caricare questo tipo di contenuto. ',
    errorUnsupportedFormatTitle: 'Oops! ',
    errorUnsupportedFormatDescription: 'Spiacenti, ma non supportiamo questo tipo di file. ',
    errorUnsupportedFileTitle: 'Oops! ',
    errorUnsupportedFileDescription: 'foo.exe non è un formato supportato. ',
    errorUploadUnexpectedTitle: 'Oops! ',
    
    errorUploadUnexpectedDescription: function(file) {
		return file ?
			('Si è verificato un problema con il ' + file + ' file. Rimuovilo dall\'elenco prima di caricare il resto dei file.') :
			'Si è verificato un problema con il primo file dell\'elenco. Rimuovilo prima di caricare il resto dei file.';
	},
    cancelUploadTitle: 'Annullare il caricamento? ',
    cancelUploadDescription: 'Sei sicuro di voler annullare i caricamenti rimanenti? ',
    uploadSuccessfulTitle: 'Caricamento completato ',
    uploadSuccessfulDescription: 'Attendi mentre stai per raggiungere i tuoi caricamenti... ',
    uploadPendingDescription: 'I tuoi file sono stati caricati con successo e sono in attesa di approvazione. ',
    photosUploadHeader: 'Fotografie da caricare ',
    photosDragOutInstructions: 'Trascina fuori le fotografie per rimuoverle ',
    photosDragInInstructions: 'Trascina le fotografie qui ',
    photosSelectInstructions: 'Seleziona una fotografia ',
    photosFiles: 'Fotografie ',
    photosUploadingStatus: function(n, m) { return 'Caricamento in corso della fotografia ' + n + ' di ' + m; },
    photosErrorTooManyTitle: 'Troppe fotografie ',
    photosErrorTooManyDescription: function(n) { return 'Spiacenti, ma puoi caricare soltanto ' + n + ' fotografie alla volta. '; },
    photosErrorContentTypeNotAllowedDescription: 'Spiacenti, ma il caricamento delle fotografie è stato disabilitato. ',
    photosErrorUnsupportedFormatDescription: 'Spiacenti, ma puoi soltanto caricare immagini in formato .jpg, .gif o .png. ',
    
    photosErrorUnsupportedFileDescription: function(n) { return n + ' non è un file .jpg, .gif o .png.'; },
    photosBatchEditorLabel: 'Modifica informazione per Tutte le fotografie ',
    photosApplyThisInfo: 'Applica questa informazione alle fotografie più in basso ',
    
    photosErrorUploadUnexpectedDescription: function(file) {
		return file ?
			('Si è verificato un problema con il ' + file + ' file. Rimuovilo dall\'elenco prima di caricare il resto delle fotografie.') :
			'Si è verificato un problema con la prima fotografia dell\'elenco. Rimuovila prima di caricare il resto delle fotografie.';
	},
    photosUploadSuccessfulDescription: 'Attendi mentre stai per raggiungere le tue fotografie... ',
    photosUploadPendingDescription: 'Le tue fotografie sono state caricate con successo e sono in attesa di approvazione. ',
    photosUploadLimitWarning: function(n) { return 'Puoi caricare ' + n + ' fotografie alla volta. '; },
	photosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Hai aggiunto il numero massimo di fotografie. ';
            case 1: return 'Puoi caricare ancora 1 fotografia. ';
            default: return 'Puoi caricare ancora ' + n + ' fotografie. ';
        }
    },
    photosIHaveTheRight: 'Sono autorizzato a caricare queste fotografie secondo le <a href="/main/authorization/termsOfService">Condizioni di servizio</a> ',
    videosUploadHeader: 'Video da caricare ',
    videosDragInInstructions: 'Trascina i video qui ',
    videosDragOutInstructions: 'Trascina fuori i video per rimuoverli ',
    videosSelectInstructions: 'Seleziona un video ',
    videosFiles: 'Video ',
    videosUploadingStatus: function(n, m) { return 'Caricamento in corso del video ' + n + ' di ' + m; },
    videosErrorTooManyTitle: 'Troppi video ',
    videosErrorTooManyDescription: function(n) { return 'Spiacenti, ma puoi caricare soltanto ' + n + ' video alla volta. '; },
    videosErrorContentTypeNotAllowedDescription: 'Spiacenti, ma il caricamento dei video è stato disabilitato. ',
    videosErrorUnsupportedFormatDescription: 'Spiacenti, ma è possibile caricare soltanto video in formato .avi, .mov, .mp4, .wmv o .mpg. ',
    
    videosErrorUnsupportedFileDescription: function(x) { return x + ' non è un file .avi, .mov, .mp4, .wmv o .mpg file.'; },
    videosBatchEditorLabel: 'Modifica informazione per Tutti i video ',
    videosApplyThisInfo: 'Applica questa informazione ai video più in basso ',
    
    videosErrorUploadUnexpectedDescription:  function(file) {
		return file ?
			('Si è verificato un problema con il ' + file + ' file. Rimuovilo dall\'elenco prima di caricare il resto dei video.') :
			'Si è verificato un problema con il primo video dell\'elenco. Rimuovilo prima di caricare il resto dei video.';
	},
    videosUploadSuccessfulDescription: 'Attendi mentre stai per raggiungere i tuoi video... ',
    videosUploadPendingDescription: 'I tuoi video sono stati caricati con successo e sono in attesa di approvazione. ',
    videosUploadLimitWarning: function(n) { return 'Puoi caricare ' + n + ' video alla volta. '; },
	videosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Hai aggiunto il numero massimo di video. ';
            case 1: return 'Puoi caricare ancora 1 video. ';
            default: return 'Puoi caricare ancora ' + n + ' video. ';
        }
    },
    videosIHaveTheRight: 'Sono autorizzato a caricare questi video secondo le  <a href="/main/authorization/termsOfService">Condizioni di Servizio</a> ',
    musicUploadHeader: 'Canzoni da caricare ',
    musicTitleProperty: 'Titolo canzone ',
    musicDragOutInstructions: 'Trascina fuori le canzoni per rimuoverle ',
    musicDragInInstructions: 'Trascina le canzoni qui ',
    musicSelectInstructions: 'Seleziona una canzone ',
    musicFiles: 'Canzoni ',
    musicUploadingStatus: function(n, m) { return 'Caricamento in corso della canzone ' + n + ' di ' + m; },
    musicErrorTooManyTitle: 'Troppe canzoni ',
    musicErrorTooManyDescription: function(n) { return 'Spiacenti, ma possiamo soltanto caricare ' + n + ' canzoni alla volta. '; },
    musicErrorContentTypeNotAllowedDescription: 'Spiacenti, ma il caricamento canzoni è stato disabilitato. ',
    musicErrorUnsupportedFormatDescription: 'Spiacenti, ma possiamo soltanto caricare canzoni in formato .mp3. ',
    
    musicErrorUnsupportedFileDescription: function(x) { return x + ' non è un file .mp3.'; },
    musicBatchEditorLabel: 'Modifica l’informazione per tutte le canzoni ',
    musicApplyThisInfo: 'Applica l’informazione alle canzoni più in basso ',
    musicErrorUploadUnexpectedDescription:  function(file) {
		return file ?
			('Si è verificato un problema con il' + file + ' file. Rimuovilo dall\'elenco prima di caricare il resto delle canzoni.') :
			'Si è verificato un problema con la prima canzone dell\'elenco. Rimuovila prima di caricare il resto delle canzoni.';
	},
    musicUploadSuccessfulDescription: 'Attendi mentre stai per raggiungere le tue canzoni... ',
    musicUploadPendingDescription: 'Le tue canzoni sono state caricate con successo e sono in attesa di approvazione. ',
    musicUploadLimitWarning: function(n) { return 'Puoi caricare ' + n + ' canzoni alla volta. '; },
	musicUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Hai aggiunto il numero massimo di canzoni. ';
            case 1: return 'Puoi caricare ancora 1 canzone. ';
            default: return 'Puoi caricare ancora ' + n + ' canzoni. ';
        }
    },
    musicIHaveTheRight: 'Sono autorizzato a caricare queste canzoni secondo le <a href="/main/authorization/termsOfService">Condizioni di Servizio</a> '
});
