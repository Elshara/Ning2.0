dojo.provide('xg.shared.messagecatalogs.ro_RO');

dojo.require('xg.index.i18n');

/**
 * Texts for the Romanian (Romania)
 */
// Use UTF-8 byte
dojo.evalObjPath('xg.feed.nls', true);
dojo.lang.mixin(xg.feed.nls, xg.index.i18n, {
    edit: 'Editare',
    title: 'Titlu:',
    feedUrl: 'URL:',
    show: 'Arată:',
    titles: 'Numai titlurile',
    titlesAndDescriptions: 'Vedere detaliată',
    display: 'Redare',
    cancel: 'Renunţare',
    save: 'Salvare',
    loading: 'Se încarcă...',
    items: 'elemente'
});


dojo.evalObjPath('xg.opensocial.nls', true);
dojo.lang.mixin(xg.opensocial.nls, xg.index.i18n, {
    edit: 'Editare',
    title: 'Titlu:',
    appUrl: 'URL:',
    cancel: 'Renunţare',
    save: 'Salvare',
    loading: 'Se încarcă...',
    removeBox: 'Eliminare casetă',
    removeBoxText: function(feature) { return '<p>Sunteţi sigur că doriţi eliminarea casetei "' + feature + '" din Pagina mea ?</p><p>Veţi mai putea accesa această funcţie din „Funcţii adăugate personal”.</p> '},
    removeFeature: 'Eliminare funcţie',
    removeFeatureText: 'Eşti sigur că doreşti eliminarea completă a acestei funcţii ? Nu va fi disponibilă din Pagina mea sau din Funcţii adăugate personal.',
    canSendMessages: 'Trimiteţi-mi mesaje',
    canAddActivities: 'Arată actualizările în modulul Activităţi recente din Pagina mea',
    addFeature: 'Adăugare funcţie',
    youAreAboutToAdd: function(feature, linkAttributes) { return '<p>Eşti pe cale de a adăuga <strong>' + feature + '</strong> în Pagina Mea. Această funcţie a fost creată de către o terţă parte.</p> <p>Prin clic pe \'Adăugare funcţie\' eşti de acord cu Aplicaţiile Platformei <a ' + linkAttributes + '>Termeni de utilizare</a>.</p> '},
    featureSettings: 'Setare funcţie',
    allowThisFeatureTo: 'Permite funcţiei să:',
    updateSettings: 'Actualizare setări',
    onlyEmailMsgSupported: 'Doar mesajele de EMAIL sunt suportate',
    msgExpectedToContain: 'Mesajul trebuie să cuprindă următoarele câmpuri: tip, titlu şi conţinut',
    msgObjectExpected: 'Se aşteaptă obiectul mesajului',
    recipientsShdBeString: 'Destinatarii pot incăpea doar pe un rând (lista separată de virgule este acceptată)',
    recipientsShdBeSpecified: 'Câmpul destinatarilor nu poate rămâne necompletat',
    unauthorizedRecipients: 'Destinatari neautorizaţi pentru a li se trimite email',
    rateLimitExceeded: 'Limita scorului depăşită',
    userCancelled: 'User-ul a anulat operaţia'
});


dojo.evalObjPath('xg.gadgets.nls', true);
dojo.lang.mixin(xg.gadgets.nls, xg.index.i18n, {
    edit: 'Editare',
    title: 'Titlu:',
    feedUrl: 'URL:',
    cancel: 'Renunţare',
    save: 'Salvare',
    loading: 'Se încarcă...',
    removeGadget: 'Eliminare Miniaplicaţie',
    findGadgetsInDirectory: 'Caută Miniaplicaţii în Directorul de Miniaplicaţii'
});


dojo.evalObjPath('xg.forum.nls', true);
dojo.lang.mixin(xg.forum.nls, xg.index.i18n, {
    items: 'elemente',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Numărul de caractere (' + n + ') depăşeşte valoarea maximă (' + maximum + ') '; },
    pleaseEnterFirstPost: 'Scrie primul post al discuţiei',
    pleaseEnterTitle: 'Introdu un titlu pentru discuţie',
    save: 'Salvare',
    cancel: 'Renunţare',
    yes: 'Da',
    no: 'Nu',
    edit: 'Editare',
    deleteCategory: 'Ştergere categorie',
    discussionsWillBeDeleted: 'Discuţiile din această categorie vor fi şterse.',
    whatDoWithDiscussions: 'Ce doreşti să faci cu discuţiile din această categorie ?',
    moveDiscussionsTo: 'Mutaţi discuţiile în :',
    deleteDiscussions: 'Ştergere discuţii',
    'delete': 'Ştergere',
    deleteReply: 'Ştergere răspuns',
    deleteReplyQ: 'Ştergi acest răspuns ?',
    deletingReplies: 'Răspunsurile sunt în curs de ştergere...',
    doYouWantToRemoveReplies: 'Doreşti să ştergi şi răspunsurile asociate acestui comentariu ?',
    pleaseKeepWindowOpen: 'Păstrează deschisă această fereastră de navigaţie în timp ce procesarea continuă. Poate dura câteva minute.',
    contributorSaid: function(x) { return x + ' a spus : '},
    display: 'Redare',
    from: 'De la',
    show: 'Arată',
    view: 'Vizualizare',
    discussions: 'discuţii',
    discussionsFromACategory: 'Discuţii dintr-o categorie...'
});


dojo.evalObjPath('xg.groups.nls', true);
dojo.lang.mixin(xg.groups.nls, xg.index.i18n, {
    pleaseChooseAName: 'Alege un nume pentru grup.',
    pleaseChooseAUrl: 'Alege o adresă web pentru grup.',
    urlCanContainOnlyLetters: 'Adresa web poate conţine numai litere şi cifre (fără spaţii).',
    descriptionTooLong: function(n, maximum) { return 'Lungimea descrierii grupului (' + n + ') depăşeşte valoarea maximă (' + maximum + ') '; },
    nameTaken: 'Ne cerem scuze. Acest nume a fost deja alocat. Alege alt nume.',
    urlTaken: 'Ne cerem scuze. Această adresă web a fost deja alocată. Alege altă adresă web.',
    whyNot: 'De ce nu ?',
    groupCreatorDetermines: function(href) { return 'Creatorul grupului stabileşte cine are permisiunea de a se alătura grupului. Dacă crezi că ai fost blocat în mod eronat, <a ' + href + '>contactează creatorul grupului</a> '; },
    edit: 'Editare',
    from: 'De la',
    show: 'Arată',
    groups: 'grupuri',
    pleaseEnterName: 'Introdu numele',
    pleaseEnterEmailAddress: 'Introdu adresa de email',
    xIsNotValidEmailAddress: function(x) { return x + ' nu este o adresă email valabilă '; },
    save: 'Salvare',
    cancel: 'Renunţare'
});


dojo.evalObjPath('xg.html.nls', true);
dojo.lang.mixin(xg.html.nls, xg.index.i18n, {
    contentsTooLong: function(maximum) { return 'Conţinuturile sunt prea lungi. Utilizează un număr de maxim ' + maximum + ' caractere. '; },
    edit: 'Editare',
    save: 'Salvare',
    cancel: 'Renunţare',
    saving: 'În curs de salvare...',
    addAWidget: function(url) { return '<a href="' + url + '">Adaugă o interfaţă</a> la caseta de text '; }
});


dojo.evalObjPath('xg.index.nls', true);
dojo.lang.mixin(xg.index.nls, xg.index.i18n, {
    done: 'Gata',
    yourMessageIsBeingSent: 'Mesajul se transmite.',
    youNeedToAddEmailRecipient: 'Ai nevoie de un destinatar pentru email.',
    checkPageOut: function (network) {return 'Verifică această pagină pe'+network},
    checkingOutTitle: function (title, network) {return 'Verifică "'+titlul+'" on '+network},
    selectOrPaste: 'Trebuie să selectezi un fişier video sau să lipeşti codul incorporat',
    selectOrPasteMusic: 'Trebuie să selectezi o piesă sau să lipeşti adresa URL',
    cannotKeepFiles: 'Dacă doreşti să vizualizezi mai multe opţiuni va fi necesar să reselectezi fişierele. Doreşti să continui ?',
    pleaseSelectPhotoToUpload: 'Selectează o fotografie ce trebuie încărcată.',
    addingLabel: 'În curs de adăugare...',
    sendingLabel: 'În curs de trimitere...',
    addingInstructions: 'Lasă fereastra deschisă în timp ce se adaugă conţinutul.',
    looksLikeNotImage: 'Se pare că cel puţin un fişier nu este în format .jpg, .gif sau .png. Doreşti totuşi să încerci încărcarea ?',
    looksLikeNotVideo: 'Se pare că fişierul selectat nu este în format .mov, .mpg, .mp4, .avi, .3gp sau .wmv. Doreşti totuşi să încerci încărcarea ?',
    looksLikeNotMusic: 'Se pare că fişierul selectat nu este în format .mp3. Doreşti totuşi să încerci încărcarea ?',
    showingNFriends: function(n, searchString) {
        switch(n) {
            case 1: return 'Afişare 1 prieten conform criteriului "' + searchString + '". <a href="#">Arată toţi</a> ';
            default: return 'Afişare ' + n + ' prieteni conform criteriului "' + searchString + '". <a href="#">Arată toţi</a> ';
        }
    },
    sendInvitation: 'Trimitere invitaţie',
    sendMessage: 'Trimitere mesaj',
    sendInvitationToNFriends: function(n) {
        switch(n) {
            case 1: return 'Trimiteţi invitaţia la 1 prieten ? ';
            default: return 'Trimiteţi invitaţia la ' + n + ' prieteni ? ';
        }
    },
    sendMessageToNFriends: function(n) {
        switch(n) {
            case 1: return 'Trimiţi mesajul la 1 prieten ? ';
            default: return 'Trimiţi mesajul la ' + n + ' prieteni ? ';
        }
    },
    invitingNFriends: function(n) {
        switch(n) {
            case 1: return 'Invitaţie pentru 1 prieten... ';
            default: return 'Invitaţie pentru ' + n + ' prieteni... ';
        }
    },
    nFriends: function(n) {
        switch(n) {
            case 1: return '1 prieten... ';
            default: return n + ' prieteni... ';
        }
    },
    sendingMessageToNFriends: function(n) {
        switch(n) {
            case 1: return 'Trimitere mesaj la 1 prieten... ';
            default: return 'Trimitere mesaj la ' + n + ' prieteni... ';
        }
    },
    yourMessageOptional: '<label>Mesajul tău</label> (Opţional)',
    subjectIsTooLong: function(n) { return 'Subiectul este prea lung. Utilizează maxim '+n+' caractere. '; },
    messageIsTooLong: function(n) { return 'Mesajul este prea lung. Utilizează maxim '+n+' caractere. '; },
    pleaseChoosePeople: 'Selectează câteva persoane pentru a le invita.',
    noPeopleSelected: 'Nu ai selectat nicio persoană.',
    pleaseEnterEmailAddress: 'Introdu adresa de email.',
    pleaseEnterPassword: function(emailAddress) { return 'Introdu parola pentru ' + emailAddress + '. '; },
    sorryWeDoNotSupport: 'Regretăm, dar nu suportăm agenda asociată acestei adrese de email. Încearcă prin clic pe \'Aplicaţie agendă\' de mai jos pentru a utiliza agenda din calculator.',
    pleaseSelectSecondPart: 'Selectează a doua parte a adresei de email, de exemplu gmail.com.',
    atSymbolNotAllowed: 'Asigură-te că simbolul @ nu se află în prima parte a adresei de email.',
    resetTextQ: 'Resetare text ?',
    resetTextToOriginalVersion: 'Eşti sigur că doreşti resetarea întregului text la versiunea originală ? Toate modificările vor fi pierdute.',
    changeQuestionsToPublic: 'Faci întrebările publice ?',
    changingPrivateQuestionsToPublic: 'Transformând întrebările private în publice, vor fi vizibile toate răspunsurile membrilor. Eşti sigur ?',
    youHaveUnsavedChanges: 'Ai modificări nesalvate.',
    pleaseEnterASiteName: 'Introdu o denumire pentru reţeaua socială, de exemplu, Tiny Clown Club',
    pleaseEnterShorterSiteName: 'Introdu un nume mai scurt (max. 64 caractere)',
    pleaseEnterShorterSiteDescription: 'Introdu o descriere mai scurtă (max. 140 caractere)',
    siteNameHasInvalidCharacters: 'Numele are câteva caractere invalide',
    thereIsAProblem: 'Este o problemă cu informaţiile tale',
    thisSiteIsOnline: 'Această reţea socială este activă',
    online: '<strong>Activ</strong>',
    onlineSiteCanBeViewed: '<strong>Activ</strong> - Reţeaua poate fi vizualizată conform setărilor de intimitate.',
    takeOffline: 'Dezactivare',
    thisSiteIsOffline: 'Această reţea socială este inactivă',
    offline: '<strong>Inactiv</strong>',
    offlineOnlyYouCanView: '<strong>Inactiv</strong> - Numai tu poţi vizualiza această reţea socială.',
    takeOnline: 'Activare',
    themeSettings: 'Setări teme',
    addYourOwnCss: 'Avansat',
    error: 'Eroare',
    pleaseEnterTitleForFeature: function(displayName) { return 'Introdu un titlu pentru funcţia ' + displayName + ' '; },
    thereIsAProblemWithTheInformation: 'Este o problemă cu informaţiile introduse',
    photos: 'Fotografii',
    videos: 'Fişiere video',
    pleaseEnterTheChoicesFor: function(questionTitle) { return 'Introdu opţiunile pentru "' + questionTitle + '", de exemplu Călătorii, Lectură, Shopping '; },
    pleaseEnterTheChoices: 'Introdu opţiunile, de exemplu Călătorii, Lectură, Shopping',
    email: 'email',
    subject: 'Subiect',
    message: 'Mesaj',
    send: 'Trimitere',
    cancel: 'Renunţare',
    go: 'Acţiune',
    areYouSureYouWant: 'Sigur doreşi să faci acest lucru ?',
    processing: 'În curs de procesare...',
    pleaseKeepWindowOpen: 'Păstrează deschisă această fereastră de navigaţie în timp ce procesarea continuă. Poate dura câteva minute.',
    complete: 'Încheiat !',
    processIsComplete: 'Procesul s-a încheiat.',
    processingFailed: 'Regretăm, dar procesarea a eşuat. Încercaţi mai târziu.',
    ok: 'OK',
    body: 'Corp',
    pleaseEnterASubject: 'Introdu un subiect',
    pleaseEnterAMessage: 'Introdu un mesaj',
    pleaseChooseFriends: 'Selectează câţiva prieteni anterior transmiterii mesajului.',
    thereHasBeenAnError: 'S-a produs o eroare',
    thereWasAProblem: 'A fost o problemă la adăugarea conţinutului Încearcă mai târziu.',
    fileNotFound: 'Fişierul nu poate fi găsit',
    pleaseProvideADescription: 'Fă o descriere',
    pleaseEnterSomeFeedback: 'Introdu nişte comentarii',
    title: 'Titlu:',
    setAsMainSiteFeature: 'Setare ca Funcţie Principală',
    thisIsTheMainSiteFeature: 'Aceasta este funcţia principală',
    customized: 'Particularizat',
    copyHtmlCode: 'Copiere cod HTML',
    playerSize: 'Dimensiune player',
    selectSource: 'Selectare sursă',
    myAlbums: 'Albumele mele',
    myMusic: 'Muzica mea',
    myVideos: 'Fişierele mele video',
    showPlaylist: 'Afişare listă de cântece',
    change: 'Schimbare',
    changing: 'În curs de schimbare...',
    changeSettings: 'Schimbi setările ?',
    keepWindowOpenWhileChanging: 'Păstrează fereastra de navigaţie deschisă în timp ce sunt modificate setările de intimitate. Acest proces poate dura câteva minute.',
    htmlNotAllowed: 'HTML nu este permis.',
    noFriendsFound: 'Niciun prieten corespunde criteriilor de căutare.',
    yourSubject: 'Subiectul tău',
    yourMessage: 'Mesajul tău',
    pleaseEnterFbApiKey: 'Introdu cheia Facebook API.',
    pleaseEnterValidFbApiKey: 'Introdu o cheie Facebook API validă.',
    pleaseEnterFbApiSecret: 'Introdu secretul Facebook API.',
    pleaseEnterValidFbApiSecret: 'Introdu un secret Facebook API valid.',
    pleaseEnterFbTabName: 'Introdu un nume pentru fila aplicaţiei Facebook.',
    pleaseEnterValidFbTabName: function(maxChars) {
                                   return 'Introdu un nume mai scurt pentru fila aplicaţiei Facebook.  Lungimea maximă este de ' + maxChars + ' caractere' + (maxChars == 1 ? '' : 's') + '. ';
                               },
    newTab: 'Filă Nouă',
    saveYourChanges: 'Doreşti să salvezi schimbările acestei file?',
    areYouSureNavigateAway: 'Ai schimbări nesalvate',
    youTabUpdated: 'Noua ta filă a fost salvată',
    youTabUpdatedUrl: function(url) { return 'Fila ta a fost salvată. Clic <a href="'+url+'" target="_blank">aici</a> pentru a edita noua pagină.' },
    resetToDefaults: 'Revino la setările iniţiale',
    youNaviWillbeRestored: 'Filele tale de navigare vor fi afişate la navigarea iniţială a reţelei.',
    hiddenWarningTop: function(n) { return 'Această filă nu a fost adăugată la reţeaua ta. Există o limită de '+n+' file principale. '+ 'Te rugăm, renunţă la unele file principale sau transformă-le în file secundare.' },
    hiddenWarningSub: function(n) { return 'Această filă secundară nu a fost adăugată la reţeaua ta. Există o limită de '+n+' file secundare. '+ 'Te rugăm, renunţă la unele file secundare sau transformă-le în file principale.' },
    removeConfirm: 'Renunţând la fila principală, vei renunţa şi la filele secundare. Fă clic pe OK pentru a continua.',
    saveYourTab: 'Doreşti să vezi această filă?',
    yes: 'Da',
    no: 'Nu',
    youMustSpecifyTabName: 'Trebuie să specifici numele filei'
});


dojo.evalObjPath('xg.music.nls', true);
dojo.lang.mixin(xg.music.nls, xg.index.i18n, {
    play: 'redare',
    pleaseSelectTrackToUpload: 'Selectează o melodie ce trebuie încărcată.',
    pleaseEnterTrackLink: 'Introdu adresa URL a melodiei.',
    thereAreUnsavedChanges: 'Ai modificări nesalvate.',
    autoplay: 'Redare automată',
    showPlaylist: 'Afişare listă de cântece',
    playLabel: 'Redare',
    url: 'URL',
    rssXspfOrM3u: 'rss, xspf sau m3u',
    save: 'Salvare',
    cancel: 'Renunţare',
    edit: 'Editare',
    shufflePlaylist: 'Redare aleatorie de cântece',
    fileIsNotAnMp3: 'Se pare că unul dintre fişiere nu este format mp3. Încerci totuşi să-l încarci ?',
    entryNotAUrl: 'Se pare că una dintre poziţii nu este URL. Asigură-te că toate poziţiile încep cu <kbd>http://</kbd>'
});


dojo.evalObjPath('xg.page.nls', true);
dojo.lang.mixin(xg.page.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Numărul de caractere (' + n + ') depăşeşte valoarea maximă (' + maximum + ') '; },
    pleaseEnterContent: 'Introdu conţinutul paginii',
    pleaseEnterTitle: 'Introdu un titlu pentru pagină',
    pleaseEnterAComment: 'Introdu un comentariu',
    deleteThisComment: 'Sigur doreşti să ştergi acest comentariu?',
    save: 'Salvare',
    cancel: 'Renunţare',
    edit: 'Editare',
    close: 'Închidere',
    displayPagePosts: 'Afişare comentarii în pagină',
    directory: 'Director',
    displayTab: 'Afişare filă',
    addAnotherPage: 'Adăugare pagină nouă',
    tabText: 'Text filă',
    urlDirectory: 'Director URL',
    displayTabForPage: 'Dacă trebuie afişată o filă pentru pagină',
    tabTitle: 'Titlu filă',
    remove: 'Eliminare',
    thereIsAProblem: 'Este o problemă cu informaţiile tale'
});


dojo.evalObjPath('xg.photo.nls', true);
dojo.lang.mixin(xg.photo.nls, xg.index.i18n, {
    randomOrder: 'Ordine aleatorie',
    untitled: 'Fără titlu',
    photos: 'Fotografii',
    edit: 'Editare',
    photosFromAnAlbum: 'Albume',
    show: 'Arată',
    rows: 'linii',
    cancel: 'Renunţare',
    save: 'Salvare',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Numărul de caractere (' + n + ') depăşeşte valoarea maximă (' + maximum + ') '; },
    pleaseSelectPhotoToUpload: 'Selectează o fotografie ce trebuie încărcată.',
    importingNofMPhotos: function(n,m) { return 'Import fotografiile <span id="currentP">' + n + '</span> ' + m + '. '},
    starting: 'Începere...',
    done: 'Gata !',
    from: 'De la',
    display: 'Redare',
    takingYou: 'Fotografii în curs de afişare...',
    anErrorOccurred: 'Din păcate s-a produs o eroare. Raportează această problemă folosind linkul din josul paginii.',
    weCouldntFind: 'Nu au fost găsite fotografii ! De ce nu încerci una dintre celelalte opţiuni ?'
});


dojo.evalObjPath('xg.activity.nls', true);
dojo.lang.mixin(xg.activity.nls, xg.index.i18n, {
    edit: 'Editare',
    show: 'Arată',
    events: 'evenimente',
    setWhatActivityGetsDisplayed: 'Stabileşte ce activitate va fi afişată',
    save: 'Salvare',
    cancel: 'Renunţare'
});


dojo.evalObjPath('xg.profiles.nls', true);
dojo.lang.mixin(xg.profiles.nls, xg.index.i18n, {
    removeFriendTitle: function(username) {return 'Şterge ' + username + ' Ca prieten ?'; },
    removeFriendConfirm: function(username) {return 'Eşti sigur că doreşti să ştergi ' + username + ' ca un prieten ?' },
    pleaseEnterValueForPost: 'Introdu o valoare pentru comentariu',
    edit: 'Editare',
    recentlyAdded: 'Recent adăugate',
    featured: 'Reliefate',
    iHaveRecentlyAdded: 'Am adăugat recent',
    fromTheSite: 'de pe reţeaua socială',
    cancel: 'Renunţare',
    save: 'Salvare',
    loading: 'Se încarcă...',
    addAsFriend: 'Adăugare ca prieten',
    requestSent: 'Cerere transmisă !',
    sendingFriendRequest: 'Transmitere cerere către prieten',
    thisIsYou: 'Acesta eşti tu !',
    isYourFriend: 'Este prietenul tău',
    isBlocked: 'Este blocat',
    pleaseEnterPostBody: 'Introdu ceva în corpul comentariului',
    pleaseEnterChatter: 'Introdu ceva în comentariu',
    letMeApproveChatters: 'Trebuie să aprob comentariile anterior publicării ?',
    noPostChattersImmediately: 'Nu - postaţi comentariile imediat',
    yesApproveChattersFirst: 'Da - aprobaţi comentariile mai întâi',
    yourCommentMustBeApproved: 'Comentariul trebuie aprobat înainte ca cineva să-l poată vedea.',
    reallyDeleteThisPost: 'Chiar doreşti ştergerea acestui comentariu ?',
    commentWall: 'Panou de comentarii',
    commentWallNComments: function(n) {
        switch(n) {
            case 1: return 'Panou de comentarii (1 comentariu) ';
            default: return 'Panou de comentarii (' + n + ' comentarii) ';
        }
    },
    display: 'Redare',
    from: 'De la',
    show: 'Arată',
    rows: 'linii',
    posts: 'comentarii',
    returnToDefaultWarning: 'Această acţiune va readuce toate funcţiile şi tema din Pagina Mea înapoi la valorile implicite ale reţelei. Doreşti să continui ?',
    networkError: 'Eroare de reţea',
    wereSorry: 'Regretăm, dar deocamdată nu am putut salva noua dispunere. Este posibil ca problema să fie cauzată de pierderea conexiunii la Internet. Verifică conexiunea şi reîncearcă.',
    addFeature: 'Adăugare funcţie',
    addFeatureConfirmation: function(linkAttributes) { return '<p>Eşti pe cale de a adăuga o nouă funcţie în Pagina Mea. Această funcţie a fost dezvoltată de către o terţă parte.</p> <p>Prin clic pe \'Adăugare funcţie\' eşti de acord cu Aplicaţiile Platformei <a ' + linkAttributes + '>Termeni de utilizare</a>.</p> '; }
});


dojo.evalObjPath('xg.shared.nls', true);
dojo.lang.mixin(xg.shared.nls, xg.index.i18n, {
    yourMessage: 'Mesajul tău',
    updateMessage: 'Actualizare mesaj',
    updateMessageQ: 'Actualizare mesaj ?',
    removeWords: 'Pentru a vă asigura că mesajul a fost trimis, recomandăm să te întorci şi să modifici sau să elimini următoarele cuvinte :',
    warningMessage: 'Se pare că în acest email sunt cuvinte care ar cauza trimiterea mesajului în directorul spam.',
    errorMessage: 'În acest email sunt minim 6 cuvinte care ar cauza trimiterea mesajului în directorul spam.',
    goBack: 'Înapoi',
    sendAnyway: 'Trimitere în starea actuală',
    messageIsTooLong: function(n,m) { return 'Regretăm. Numărul maxim de caractere este '+m+'. ' },
    locationNotFound: function(location) { return '<em>' + location + '</em> nu a fost găsită. '; },
    confirmation: 'Confirmare',
    showMap: 'Afişare hartă',
    hideMap: 'Ascundere hartă',
    yourCommentMustBeApproved: 'Comentariul trebuie aprobat înainte ca cineva să-l poată vedea.',
    nComments: function(n) {
        switch(n) {
            case 1: return '1 Comentariu ';
            default: return n + ' Comentarii ';
        }
    },
    pleaseEnterAComment: 'Introdu un comentariu',
    uploadAPhoto: 'Încarcă o fotografie',
    uploadAnImage: 'Încarcă o imagine',
    uploadAPhotoEllipsis: 'Încarcă o fotografie...',
    useExistingImage: 'Utilizează imaginea existentă :',
    existingImage: 'Imagine existentă',
    useThemeImage: 'Utilizează imaginea temă:',
    themeImage: 'Imagine temă',
    noImage: 'Fără imagine',
    uploadImageFromComputer: 'Încărcare imagine din calculator',
    tileThisImage: 'Multiplicare imagine',
    done: 'Gata',
    currentImage: 'Imaginea curentă',
    pickAColor: 'Alege o culoare...',
    openColorPicker: 'Deschide paleta de culori',
    transparent: 'Transparent',
    loading: 'Se încarcă...',
    ok: 'OK',
    save: 'Salvare',
    cancel: 'Renunţare',
    saving: 'În curs de salvare...',
    addAnImage: 'Adaugă o imagine',
    uploadAFile: 'Încarcă un fişier',
    pleaseEnterAWebsite: 'Introdu o adresă a unui site web',
    pleaseEnterAFileAddress: 'Introdu adresa fişierului',
    bold: 'Aldin',
    italic: 'Cursiv',
    underline: 'Subliniat',
    strikethrough: 'Tăiat',
    addHyperink: 'Adăugare hyperlink',
    options: 'Opţiuni',
    wrapTextAroundImage: 'Scrii textul în jurul imaginii ?',
    imageOnLeft: 'Imaginea în stânga ?',
    imageOnRight: 'Imaginea în dreapta ?',
    createThumbnail: 'Generare prezentare grafică ?',
    pixels: 'pixeli',
    createSmallerVersion: 'Generare versiune micşorată a imaginii pentru afişare. Setează lăţimea în pixeli.',
    popupWindow: 'Fereastră popup ?',
    linkToFullSize: 'Legătură către versiunea de dimensiuni normale a imaginii într-o fereastră popup.',
    add: 'Adăugare',
    keepWindowOpen: 'Păstrează deschisă această fereastră de navigaţie în timp ce încărcarea continuă.',
    cancelUpload: 'Renunţare la încărcare',
    pleaseSelectAFile: 'Selectează un fişier imagine',
    pleaseSpecifyAThumbnailSize: 'Specifică dimensiunea prezentării grafice',
    thumbnailSizeMustBeNumber: 'Dimensiunea prezentării grafice trebuie să fie exprimată printr-un număr',
    addExistingImage: 'sau introdu o imagine existentă',
    addExistingFile: 'sau introdu un fişier existent',
    clickToEdit: 'Fă clic pentru editare',
    sendingFriendRequest: 'Transmitere cerere către prieten',
    requestSent: 'Cerere transmisă !',
    pleaseCorrectErrors: 'Corectează aceste erori',
    noo: 'NOU',
    none: 'NICIUNA',
    joinNow: 'Asociere acum',
    join: 'Asociere',
    addToFavorites: 'Adăugare la favorite',
    removeFromFavorites: 'Eliminare din favorite',
    follow: 'Urmează',
    stopFollowing: 'Încetare urmărire',
    pendingPromptTitle: 'Se aşteaptă aprobarea calităţii de membru',
    youCanDoThis: 'Poţi face acest lucru după ce administratorii îţi vor aproba calitatea de membru',
    editYourTags: 'Editare file proprii',
    addTags: 'Adăugare file',
    editLocation: 'Editare locaţie',
    editTypes: 'Editare tip eveniment',
    charactersLeft: function(n) {
        if (n >= 0) {
            return '&nbsp;(' + n + ' rămas)' ;
        } else {
            return  '&nbsp;(' + Math.abs(n) + ' over)' ;
        }
    },
    commentWall: 'Panou de comentarii',
    commentWallNComments: function(n) { switch(n) { case 1: return 'Comment Wall (1 comentariu)'; default: return 'Comment Wall (' + n + ' comentarii)'; } }
});


dojo.evalObjPath('xg.video.nls', true);
dojo.lang.mixin(xg.video.nls, xg.index.i18n, {
    edit: 'Editare',
    display: 'Redare',
    detail: 'Detaliu',
    player: 'Player',
    from: 'De la',
    show: 'Arată',
    videos: 'fişiere video',
    cancel: 'Renunţare',
    save: 'Salvare',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Numărul de caractere (' + n + ') depăşeşte valoarea maximă (' + maximum + ') '; },
    approve: 'Aprobare',
    approving: 'În curs de aprobare...',
    keepWindowOpenWhileApproving: 'Păstrează această fereastră de navigaţie deschisă în timp ce sunt aprobate fişierele video. Acest proces poate dura câteva minute.',
    'delete': 'Ştergere',
    deleting: 'În curs de ştergere...',
    keepWindowOpenWhileDeleting: 'Păstrează această fereastră de navigaţie deschisă în timp ce sunt şterse fişierele video. Acest proces poate dura câteva minute.',
    pasteInEmbedCode: 'Lipeşte în codul incorporat pentru un fişier video din alt site.',
    pleaseSelectVideoToUpload: 'Selectează un fişier video ce trebuie încărcat.',
    embedCodeContainsMoreThanOneVideo: 'Codul incorporat conţine mai mult de un fişier video. Asigură-te că există numai un cuvânt cheie <object> şi/sau <embed>.',
    embedCodeMissingTag: 'Din codul incorporat lipseşte un cuvânt cheie &lt;embed&gt; sau &lt;object&gt;.',
    fileIsNotAMov: 'Se pare că fişierul nu este în format .mov, .mpg, .mp4, .avi, .3gp sau .wmv. Încerci totuşi să-l încarci ?',
    embedHTMLCode: 'Cod HTML incorporat:',
    directLink: 'Legătură directă',
    addToMyspace: 'Adăugare la MySpace',
    shareOnFacebook: 'Partajare pe Facebook',
    addToOthers: 'Adăugare la altele'
});


dojo.evalObjPath('xg.uploader.nls', true);
dojo.lang.mixin(xg.uploader.nls, xg.index.i18n, {
    fileBrowserHeader: 'Computerul meu',
    fileRoot: 'Computerul meu',
    fileInformationHeader: 'Informaţii',
    uploadHeader: 'Fişiere de încărcat',
    dragOutInstructions: 'Pentru eliminarea fişierelor glisează-le în afară',
    dragInInstructions: 'Glisează fişierele aici',
    selectInstructions: 'Selectează un fişier',
    files: 'Fişiere',
    totalSize: 'Dimensiune totală',
    fileName: 'Nume',
    fileSize: 'Dimensiune',
    nextButton: 'Următorul >',
    okayButton: 'OK',
    yesButton: 'Da',
    noButton: 'Nu',
    uploadButton: 'Încărcare',
    cancelButton: 'Renunţare',
    backButton: 'Înapoi',
    continueButton: 'Continuare',
    uploadingStatus: function(n, m) { return 'Încărcare ' + n + ' din ' + m; },
    uploadLimitWarning: function(n) { return 'Poţi încărca ' + n + ' fişiere simultan. '; },
    uploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Ai adăugat numărul maxim de fişiere. ';
            case 1: return 'Poţi încărca încă 1 fişier. ';
            default: return 'Poţi încărca încă  ' + n + ' fişiere. ';
        }
    },
    uploadingLabel: 'În curs de încărcare...',
    uploadingInstructions: 'Lasă fereastra deschisă în timp ce se execută încărcarea.',
    iHaveTheRight: 'Am dreptul să încarc aceste fişiere conform <a href="/main/authorization/termsOfService">Termeni de utilizare a serviciului</a>',
    updateJavaTitle: 'Actualizare Java',
    updateJavaDescription: 'Programul de încărcare simultană necesită o versiune Java mai recentă. Pentru a actualiza Java fă clic pe „Okay”.',
    batchEditorLabel: 'Editare informaţii pentru toate elementele',
    applyThisInfo: 'Aplică această informaţie fişierelor de mai jos',
    titleProperty: 'Titlu',
    descriptionProperty: 'Descriere',
    tagsProperty: 'Cuvinte cheie',
    viewableByProperty: 'Poate fi vizualizat de',
    viewableByEveryone: 'Oricine',
    viewableByFriends: 'Numai prietenii mei',
    viewableByMe: 'Numai eu',
    albumProperty: 'Album',
    artistProperty: 'Artist',
    enableDownloadLinkProperty: 'Activare legătură descărcare',
    enableProfileUsageProperty: 'Permite altora să adauge melodia în paginile lor',
    licenseProperty: 'Licenţă',
    creativeCommonsVersion: '3.0',
    selectLicense: '— Selectare licenţă —',
    copyright: '© Toate drepturile sunt rezervate',
    ccByX: function(n) { return 'Atributii Creative Comune ' + n; },
    ccBySaX: function(n) { return 'Atribuţii Creative Comune de Partajare ' + n; },
    ccByNdX: function(n) { return 'Atribuţii Creative Comune Fără Derivaţii ' + n; },
    ccByNcX: function(n) { return 'Atribuţii Creative Comune Necomerciale ' + n; },
    ccByNcSaX: function(n) { return 'Atribuţii Creative Comune Necomerciale de Partajare ' + n; },
    ccByNcNdX: function(n) { return 'Atribuţii creative Comune Necomerciale Fără Derivaţii ' + n; },
    publicDomain: 'Domeniu public',
    other: 'Altele',
    errorUnexpectedTitle: 'Vai !',
    errorUnexpectedDescription: 'S-a produs o eroare. Încearcă din nou.',
    errorTooManyTitle: 'Prea multe elemente',
    errorTooManyDescription: function(n) { return 'Regretăm, dar poţi încărca numai ' + n + ' elemente simultan. '; },
    errorNotAMemberTitle: 'Interzis',
    errorNotAMemberDescription: 'Regretăm, dar pentru a încărca trebuie să fii membru.',
    errorContentTypeNotAllowedTitle: 'Interzis',
    errorContentTypeNotAllowedDescription: 'Regretăm, dar încărcarea acestui tip de conţinut este interzisă.',
    errorUnsupportedFormatTitle: 'Vai !',
    errorUnsupportedFormatDescription: 'Regretăm, dar nu suportăm acest tip de fişier.',
    errorUnsupportedFileTitle: 'Vai !',
    errorUnsupportedFileDescription: 'foo.exe este un format nesuportat.',
    errorUploadUnexpectedTitle: 'Vai !',
    errorUploadUnexpectedDescription: function(file) {
        return file ?
            ('Se pare că există o problemă cu fişierul ' + file + '. Şterge-l din listă înainte de a încărca restul fişierelor.' ) :
            'Se pare că există o problemă cu fişierul de la începutul listei. Şterge-l înainte de a încărca restul fişierelor.';
    },
    cancelUploadTitle: 'Renunţi la încărcare ?',
    cancelUploadDescription: 'Eşii sigur că doreşti să renunţi la încărcarea elementelor rămase ?',
    uploadSuccessfulTitle: 'Încărcare încheiată',
    uploadSuccessfulDescription: 'Aşteaptă afişarea elementelor pe care le-ai încărcat...',
    uploadPendingDescription: 'Fişierele au fost încărcate cu succes şi aşteaptă aprobarea.',
    photosUploadHeader: 'Fotografii de încărcat',
    photosDragOutInstructions: 'Pentru eliminarea fotografiilor glisează-le în afară',
    photosDragInInstructions: 'Glisează fotografiile aici',
    photosSelectInstructions: 'Selectează o fotografie',
    photosFiles: 'Fotografii',
    photosUploadingStatus: function(n, m) { return 'Încărcare fotografie ' + n + ' din ' + m; },
    photosErrorTooManyTitle: 'Prea multe fotografii',
    photosErrorTooManyDescription: function(n) { return 'Regretăm, dar poţi încărca numai ' + n + ' fotografii simultan. '; },
    photosErrorContentTypeNotAllowedDescription: 'Regretăm, dar încărcarea fotografiilor a fost dezactivată.',
    photosErrorUnsupportedFormatDescription: 'Regretăm, dar poţi încărca numai imagini în format .jpg, .gif sau .png.',
    photosErrorUnsupportedFileDescription: function(n) { return n + ' nu este un fişier .jpg, .gif sau .png. '; },
    photosBatchEditorLabel: 'Editare informaţii pentru toate fotografiile',
    photosApplyThisInfo: 'Aplică informaţia fotografiilor de mai jos',
    photosErrorUploadUnexpectedDescription: function(file) {
        return file ?
            ('Se pare că există o problemă cu fişierul ' + file + '. Şterge-l din listă înainte de a încărca restul fotografiilor.' ) :
            'Se pare că există o problemă cu fotografia de la începutul listei. Şterge-o înainte de a încărca restul fotografiilor.';
    },
    photosUploadSuccessfulDescription: 'Aşteaptă afişarea fotografiilor pe care le-ai încărcat...',
    photosUploadPendingDescription: 'Fotografiile au fost încărcate cu succes şi aşteaptă aprobarea.',
    photosUploadLimitWarning: function(n) { return 'Poţi încărca ' + n + ' fotografii simultan. '; },
    photosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Ai adăugat numărul maxim de fotografii. ';
            case 1: return 'Poţi încărca încă 1 fotografie. ';
            default: return 'Poţi încărca încă  ' + n + ' fotografii. ';
        }
    },
    photosIHaveTheRight: 'Am dreptul să încarc aceste fotografii conform <a href="/main/authorization/termsOfService">Termeni de utilizare a serviciului</a>',
    videosUploadHeader: 'Fişiere video de încărcat',
    videosDragInInstructions: 'Glisează fişierele video aici',
    videosDragOutInstructions: 'Pentru eliminarea fişierelor glisează-le în afară',
    videosSelectInstructions: 'Selectează un fişier video',
    videosFiles: 'Fişiere video',
    videosUploadingStatus: function(n, m) { return 'Încărcare fişier video ' + n + ' din ' + m; },
    videosErrorTooManyTitle: 'Prea multe fişiere video',
    videosErrorTooManyDescription: function(n) { return 'Regretăm, dar poţi încărca numai ' + n + ' fişiere video simultan. '; },
    videosErrorContentTypeNotAllowedDescription: 'Regretăm, dar încărcarea fişierelor video a fost dezactivată.',
    videosErrorUnsupportedFormatDescription: 'Regretăm, dar poţi încărca numai fişiere video format .avi, .mov, .mp4, .wmv sau .mpg.',
    videosErrorUnsupportedFileDescription: function(x) { return x + ' nu este un fişier .avi, .mov, .mp4, .wmv sau .mpg. '; },
    videosBatchEditorLabel: 'Editare informaţii pentru toate fişierele video',
    videosApplyThisInfo: 'Aplică această informaţie fişierelor video de mai jos',
    videosErrorUploadUnexpectedDescription: function(file) { return file ? ('Se pare că există o problemă cu fişierul ' + file + '. Şterge-l din listă înainte de a încărca restul imaginilor video.' ) : 'Se pare că există o problemă cu imaginea video de la începutul listei. Şterge-o înainte de a încărca restul imaginilor video.';
    },
    videosUploadSuccessfulDescription: 'Aşteaptă afişarea fişierelor video pe care le-ai încărcat...',
    videosUploadPendingDescription: 'Fişierele video au fost încărcate cu succes şi aşteaptă aprobarea.',
    videosUploadLimitWarning: function(n) { return 'Puteţi încărca ' + n + ' fişiere video simultan. '; },
    videosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Ai adăugat numărul maxim de fişiere video. ';
            case 1: return 'Poţi încărca încă 1 fişier video. ';
            default: return 'Poţi încărca încă  ' + n + ' fişiere video. ';
        }
    },
    videosIHaveTheRight: 'Am dreptul să încarc aceste fişiere video conform <a href="/main/authorization/termsOfService">Termeni de utilizare a serviciului</a>',
    musicUploadHeader: 'Melodii de încărcat',
    musicTitleProperty: 'Titlu melodie',
    musicDragOutInstructions: 'Pentru eliminarea melodiilor glisează-le în afară',
    musicDragInInstructions: 'Glisează melodiile aici',
    musicSelectInstructions: 'Selectează o melodie',
    musicFiles: 'Melodii',
    musicUploadingStatus: function(n, m) { return 'Încărcare melodie ' + n + ' din ' + m; },
    musicErrorTooManyTitle: 'Prea multe melodii',
    musicErrorTooManyDescription: function(n) { return 'Regretăm, dar poţi încărca numai ' + n + ' melodii simultan. '; },
    musicErrorContentTypeNotAllowedDescription: 'Regretăm, dar încărcarea melodiilor a fost dezactivată.',
    musicErrorUnsupportedFormatDescription: 'Regretăm, dar poţi încărca numai melodii în format .mp3.',
    musicErrorUnsupportedFileDescription: function(x) { return x + ' nu este un fişier .mp3. '; },
    musicBatchEditorLabel: 'Editare informaţii pentru toate melodiile',
    musicApplyThisInfo: 'Aplică această informaţie melodiilor de mai jos',
    musicErrorUploadUnexpectedDescription: function(file) { return file ? ('Se pare că există o problemă cu fişierul ' + file + '. Şterge-l din listă înainte de a încărca restul melodiilor.' ) : 'Se pare că există o problemă cu melodia de la începutul listei. Şterge-o înainte de a încărca restul melodiilor.'; },
    musicUploadSuccessfulDescription: 'Aşteaptă afişarea melodiilor pe care le-ai încărcat...',
    musicUploadPendingDescription: 'Melodiile au fost încărcate cu succes şi aşteaptă aprobarea.',
    musicUploadLimitWarning: function(n) { return 'Puteţi încărca ' + n + ' melodii simultan. '; },
    musicUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Ai adăugat numărul maxim de melodii. ';
            case 1: return 'Poţi încărca încă 1 melodie. ';
            default: return 'Poţi încărca încă  ' + n + ' melodii. ';
        }
    },
    musicIHaveTheRight: 'Am dreptul să încarc aceste melodii conform <a href="/main/authorization/termsOfService">Termeni de utilizare a serviciului</a>'
});


dojo.evalObjPath('xg.events.nls', true);
dojo.lang.mixin(xg.events.nls, xg.index.i18n, {
    pleaseEnterTitle: 'Introdu un titlu pentru eveniment',
    pleaseEnterDescription: 'Introdu o descriere pentru eveniment',
    messageIsTooLong: function(n) { return 'Mesajul este prea lung. Utilizează maxim '+n+' caractere. '; },
    pleaseEnterLocation: 'Introdu o locaţie pentru eveniment',
    pleaseChooseImage: 'Alege o imagine pentru eveniment',
    pleaseEnterType: 'Introdu minim un tip pentru eveniment',
    sendMessageToGuests: 'Trimite mesaje invitaţilor',
    sendMessageToGuestsThat: 'Trimite mesaje invitaţilor care:',
    areAttending: 'Participă',
    mightAttend: 'S-ar putea să participe',
    haveNotYetRsvped: 'Nu au primit încă invitaţie',
    areNotAttending: 'Nu participă',
    yourMessage: 'Mesajul tău',
    send: 'Trimitere',
    sending: 'În curs de trimitere...',
    yourMessageIsBeingSent: 'Mesajul se transmite.',
    messageSent: 'Mesaj trimis !',
    yourMessageHasBeenSent: 'Mesajul a fost trimis.',
    chooseRecipient: 'Alege un destinatar.',
    pleaseEnterAMessage: 'Introdu un mesaj',
    thereHasBeenAnError: 'S-a produs o eroare'
});


dojo.evalObjPath('xg.notes.nls', true);
dojo.lang.mixin(xg.notes.nls, xg.index.i18n, {
    addNewNote: 'Adaugă o notă nouă',
    pleaseEnterNoteTitle: 'Adaugă un titlu pentru notă',
    noteTitleTooLong: 'Titlul notei este prea lung',
    pleaseEnterNoteEntry: 'Adaugă o intrare pentru notă !'
});