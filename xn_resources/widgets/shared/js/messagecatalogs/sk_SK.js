dojo.provide('xg.shared.messagecatalogs.sk_SK');

dojo.require('xg.index.i18n');

/**
 * Texts for the Slovak (Slovakia)
 */
// Use UTF-8 byte
dojo.evalObjPath('xg.feed.nls', true);
dojo.lang.mixin(xg.feed.nls, xg.index.i18n, {
    edit: 'Upraviť',
    title: 'Názov:',
    feedUrl: 'Adresa URL:',
    show: 'Zobraziť:',
    titles: 'Iba názvy',
    titlesAndDescriptions: 'Detailné zobrazenie',
    display: 'Displej',
    cancel: 'Zrušiť',
    save: 'Uložiť',
    loading: 'Načítavanie...',
    items: 'položky'
});


dojo.evalObjPath('xg.opensocial.nls', true);
dojo.lang.mixin(xg.opensocial.nls, xg.index.i18n, {
    edit: 'Upraviť',
    title: 'Názov:',
    appUrl: 'Adresa URL:',
    cancel: 'Zrušiť',
    save: 'Uložiť',
    loading: 'Načítavanie...',
    removeBox: 'Kôš',
    removeBoxText: function(feature) { return '<p>Naozaj chcete odstrániť pole ' + feature + ' z lokality Moja stránka?</p><p>Táto funkcia zostane dostupná z lokality Moje pridané funkcie.</p> '},
    removeFeature: 'Odstrániť funkciu',
    removeFeatureText: 'Naozaj chcete túto funkciu úplne odstrániť? Funkcia už nebude dostupná z lokality Moja stránka ani z lokality Moje pridané funkcie.',
    canSendMessages: 'Poštite mi správu',
    canAddActivities: 'Zobraziť aktualizácie v module Najnovšie aktivity na lokalite Moja stránka',
    addFeature: 'Pridať funkciu',
    youAreAboutToAdd: function(feature, linkAttributes) { return '<p>Chystáte sa pridať <strong>' + feature + '</strong> na lokalitu Moja stránka. Túto funkciu vytvorila tretia strana.</p> <p>Kliknutím na položku Pridať funkciu potvrdíte súhlas s <a ' + linkAttributes + '>podmienkami používania </a> aplikácií na tejto platforme.</p> '},
    featureSettings: 'Nastavenie funkcie',
    allowThisFeatureTo: 'V rámci funkcie aktivujte:',
    updateSettings: 'Aktualizácia nastavenia',
    msgObjectExpected: 'Očakáva sa objekt správy',
    recipientsShdBeSpecified: 'Je potrebné uviesť príjemcov a nemôže byť prázdne',
    rateLimitExceeded: 'Bol prekročený limit',
    userCancelled: 'Používateľ zrušil operáciu',
    onlyEmailMsgSupported: 'Je podporovaná len správa typu EMAIL',
    msgExpectedToContain: 'Správa musí obsahovať všetky políčka: typ, názov a telo',
    recipientsShdBeString: 'Príjemci môžu byť len reťazec (zoznam oddelený čiarkou je ok)',
    unauthorizedRecipients: 'Neoprávnení príjemcovia uvedení pre odoslanie správy'
});


dojo.evalObjPath('xg.gadgets.nls', true);
dojo.lang.mixin(xg.gadgets.nls, xg.index.i18n, {
    edit: 'Upraviť',
    title: 'Názov:',
    feedUrl: 'Adresa URL:',
    cancel: 'Zrušiť',
    save: 'Uložiť',
    loading: 'Načítavanie...',
    removeGadget: 'Odstránenie miniaplikácie',
    findGadgetsInDirectory: 'Vyhľadať miniaplikácie v adresári miniaplikácií'
});


dojo.evalObjPath('xg.forum.nls', true);
dojo.lang.mixin(xg.forum.nls, xg.index.i18n, {
    items: 'položky',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Počet znakov (' + n + ') presahuje maximálnu hodnotu (' + maximum + ') '; },
    pleaseEnterFirstPost: 'Napíšte prvý príspevok do diskusie',
    pleaseEnterTitle: 'Zadajte názov diskusie',
    save: 'Uložiť',
    cancel: 'Zrušiť',
    yes: 'Áno',
    no: 'Nie',
    edit: 'Upraviť',
    deleteCategory: 'Odstrániť kategóriu',
    discussionsWillBeDeleted: 'Diskusné príspevky v tejto kategórii budú odstránené.',
    whatDoWithDiscussions: 'Čo chcete urobiť s diskusnými príspevkami v tejto kategórii?',
    moveDiscussionsTo: 'Presunúť diskusné príspevky do:',
    deleteDiscussions: 'Odstrániť diskusné príspevky',
    'delete': 'Odstrániť',
    deleteReply: 'Odstrániť odpoveď',
    deleteReplyQ: 'Odstrániť túto odpoveď?',
    deletingReplies: 'Odstraňovanie odpovedí...',
    doYouWantToRemoveReplies: 'Chcete odstrániť aj odpovede na tento komentár?',
    pleaseKeepWindowOpen: 'Nechajte okno prehľadávača otvorené, kým proces pokračuje. Môže to trvať niekoľko minút.',
    contributorSaid: function(x) { return x + 'povedali: '},
    display: 'Displej',
    from: 'Od',
    show: 'Zobraziť',
    view: 'Zobrazenie',
    discussions: 'diskusné príspevky',
    discussionsFromACategory: 'Diskusné príspevky z kategórie...'
});


dojo.evalObjPath('xg.groups.nls', true);
dojo.lang.mixin(xg.groups.nls, xg.index.i18n, {
    pleaseChooseAName: 'Vyberte názov pre svoju skupinu.',
    pleaseChooseAUrl: 'Vyberte webovú adresu pre svoju skupinu.',
    urlCanContainOnlyLetters: 'Webová adresa môže obsahovať iba písmená a číslice (bez medzier).',
    descriptionTooLong: function(n, maximum) { return 'Dĺžka popisu vašej skupiny (' + n + ') presahuje maximálnu hodnotu (' + maximum + ') '; },
    nameTaken: 'Je nám ľúto – tento názov si už vybral iný účastník. Vyberte iný názov.',
    urlTaken: 'Je nám ľúto – túto webovú adresu si už vybral iný účastník. Vyberte inú adresu.',
    whyNot: 'Prečo nie?',
    groupCreatorDetermines: function(href) { return 'Ten, kto skupinu vytvoril, stanoví, kto sa k nej môže pripojiť. Ak máte pocit, že váš prístup je blokovaný omylom, <a ' + href + '>obráťte sa tvorcu skupiny</a> '; },
    edit: 'Upraviť',
    from: 'Od',
    show: 'Zobraziť',
    groups: 'skupiny',
    pleaseEnterName: 'Zadajte svoje meno',
    pleaseEnterEmailAddress: 'Zadajte svoju e-mailovú adresu',
    xIsNotValidEmailAddress: function(x) { return x + 'je neplatná emailová adresa '; },
    save: 'Uložiť',
    cancel: 'Zrušiť'
});


dojo.evalObjPath('xg.html.nls', true);
dojo.lang.mixin(xg.html.nls, xg.index.i18n, {
    contentsTooLong: function(maximum) { return 'Obsah je príliš dlhý. Použite menej ako maximálny počet ' + maximum + ' znakov. '; },
    edit: 'Upraviť',
    save: 'Uložiť',
    cancel: 'Zrušiť',
    saving: 'Ukladanie...',
    addAWidget: function(url) { return '<a href="' + url + '">Pridať miniaplikáciu</a> k tomuto textovému poľu '; }
});


dojo.evalObjPath('xg.index.nls', true);
dojo.lang.mixin(xg.index.nls, xg.index.i18n, {
    done: 'Dokončené',
    yourMessageIsBeingSent: 'Správa sa posiela.',
    youNeedToAddEmailRecipient: 'Musíte uviesť adresáta správy.',
    checkPageOut: function (network) {return 'Pozrite si túto stránku na '+network},
    checkingOutTitle: function (title, network) {return 'Pozrite si "'+title+'" na '+network},
    selectOrPaste: 'Musíte vybrať video alebo nalepiť vložený kód',
    selectOrPasteMusic: 'Musíte vybrať pieseň alebo prilepiť adresu URL',
    cannotKeepFiles: 'Ak chcete, aby sa zobrazilo viac možností, musíte vybrať svoje súbory znovu. Chcete pokračovať?',
    pleaseSelectPhotoToUpload: 'Vyberte fotografiu, ktorú chcete preniesť.',
    addingLabel: 'Pridávanie...',
    sendingLabel: 'Odosielanie...',
    addingInstructions: 'Nechajte toto okno otvorené, kým prebieha pridávanie obsahu.',
    looksLikeNotImage: 'Jeden alebo viac súborov pravdepodobne nie sú vo formáte .jpg, .gif ani .png. Chcete sa aj tak pokúsiť o prenos?',
    looksLikeNotVideo: 'Vybraný súbor pravdepodobne nie je vo formáte .mov, .mpg, .mp4, .avi, .3gp ani .wmv. Chcete sa aj tak pokúsiť o prenos?',
    looksLikeNotMusic: 'Vybraný súbor pravdepodobne nie je vo formáte .mp3. Chcete sa aj tak pokúsiť o prenos',
    showingNFriends: function(n, searchString) {
        switch(n) {
            case 1: return 'Zobrazenie jedného priateľa, ktorý zodpovedá kritériu ' + searchString + '. <a href="#">Zobraziť všetkých</a> ';
            default: return 'Zobrazenie ' + n + ' priateľov zodpovedajúcich kritériu ' + searchString + '. <a href="#">Zobraziť všetkých</a> ';
        }
    },
    sendInvitation: 'Poslať pozvánku',
    sendMessage: 'Poslať správu',
    sendInvitationToNFriends: function(n) {
        switch(n) {
            case 1: return 'Poslať pozvánku jednému priateľovi? ';
            default: return 'Poslať pozvánku ' + n + ' priateľom? ';
        }
    },
    sendMessageToNFriends: function(n) {
        switch(n) {
            case 1: return 'Poslať správu jednému priateľovi? ';
            default: return 'Poslať správu ' + n + ' priateľom? ';
        }
    },
    invitingNFriends: function(n) {
        switch(n) {
            case 1: return 'Posiela sa pozvánka jednému priateľovi... ';
            default: return 'Posiela sa pozvánka ' + n + ' priateľom... ';
        }
    },
    nFriends: function(n) {
        switch(n) {
            case 1: return 'jeden priateľ... ';
            default: return n + 'priatelia... ';
        }
    },
    sendingMessageToNFriends: function(n) {
        switch(n) {
            case 1: return 'Posiela sa správa jednému priateľovi... ';
            default: return 'Posiela sa správa ' + n + ' priateľom... ';
        }
    },
    yourMessageOptional: '<label>Správa</label> (voliteľné)',
    subjectIsTooLong: function(n) { return 'Predmet je príliš dlhý. Použite maximálne '+n+' znakov. '; },
    messageIsTooLong: function(n) { return 'Správa je príliš dlhá. Použite maximálne '+n+' znakov. '; },
    pleaseChoosePeople: 'Vyberte osoby, ktoré chcete pozvať.',
    noPeopleSelected: 'Nie sú vybrané žiadne osoby',
    pleaseEnterEmailAddress: 'Zadajte svoju e-mailovú adresu.',
    pleaseEnterPassword: function(emailAddress) { return 'Zadajte heslo zodpovedajúce adrese ' + emailAddress + '. '; },
    sorryWeDoNotSupport: 'Je nám ľúto, ale nepodporujeme webový adresár k danej e-mailovej adrese. Skúste kliknúť nižšie na aplikáciu Adresár a použiť adresy z vášho počítača.',
    pleaseSelectSecondPart: 'Vyberte druhú časť e-mailovej adresy, napr. gmail.com.',
    atSymbolNotAllowed: 'Skontrolujte, či sa symbol @ nenachádza v prvej časti zadanej e-mailovej adresy.',
    resetTextQ: 'Obnoviť pôvodný text?',
    resetTextToOriginalVersion: 'Naozaj chcete obnoviť text v pôvodnej verzii? Stratia sa všetky zadané zmeny.',
    changeQuestionsToPublic: 'Zmeniť otázky na verejné?',
    changingPrivateQuestionsToPublic: 'Zmena súkromných otázok na verejné sprístupní odpovede všetkých členov. Naozaj chcete urobiť  zmenu?',
    youHaveUnsavedChanges: 'V texte sú neuložené zmeny.',
    pleaseEnterASiteName: 'Zadajte názov spoločenskej siete, napr. Klub malých klaunov',
    pleaseEnterShorterSiteName: 'Zadajte kratší názov (max. 64 znakov)',
    pleaseEnterShorterSiteDescription: 'Zadajte kratší popis (max. 140 znakov)',
    siteNameHasInvalidCharacters: 'Názov obsahuje neplatné znaky',
    thereIsAProblem: 'Vyskytol sa problém súvisiaci so zadanou informáciou',
    thisSiteIsOnline: 'Táto spoločenská sieť je v režime online',
    online: '<strong>Online</strong>',
    onlineSiteCanBeViewed: '<strong>Online</strong> - Sieť možno prezerať v rozsahu danom nastavením ochrany súkromia.',
    takeOffline: 'Prejsť do režimu offline',
    thisSiteIsOffline: 'Táto spoločenská sieť je v režime offline',
    offline: '<strong>Offline</strong>',
    offlineOnlyYouCanView: '<strong>Offline</strong> - Túto spoločenskúu sieť môžete prezerať iba vy.',
    takeOnline: 'Prejsť do režimu online',
    themeSettings: 'Nastavenie motívu',
    addYourOwnCss: 'Pokročilé',
    error: 'Chyba',
    pleaseEnterTitleForFeature: function(displayName) { return 'Zadajte názov pre funkciu ' + displayName + ' '; },
    thereIsAProblemWithTheInformation: 'Vyskytol sa problém súvisiaci so zadanou informáciou',
    photos: 'Fotografie',
    videos: 'Videá',
    pleaseEnterTheChoicesFor: function(questionTitle) { return 'Zadajte možnosti pre ' + questionTitle + ', napr. turistika, čítanie, nakupovanie '; },
    pleaseEnterTheChoices: 'Zadajte možnosti, napr. turistika, čítanie, nakupovanie',
    email: 'e-mail',
    subject: 'Predmet',
    message: 'Správa',
    send: 'Odoslať',
    cancel: 'Zrušiť',
    go: 'Spustiť',
    areYouSureYouWant: 'Naozaj to chcete urobiť?',
    processing: 'Prebieha spracovanie...',
    pleaseKeepWindowOpen: 'Nechajte okno prehľadávača otvorené, kým proces pokračuje. Môže to trvať niekoľko minút.',
    complete: 'Dokončené',
    processIsComplete: 'Proces je ukončený.',
    processingFailed: 'V procese nastala chyba. Skúste ešte raz neskôr.',
    ok: 'OK',
    body: 'Text správy',
    pleaseEnterASubject: 'Zadajte predmet správy',
    pleaseEnterAMessage: 'Zadajte správu',
    pleaseChooseFriends: 'Pred odoslaním správy vyberte niekoho z priateľov',
    thereHasBeenAnError: 'Chyba',
    thereWasAProblem: 'Pri pridávaní obsahu sa vyskytol problém. Skúste ešte raz neskôr.',
    fileNotFound: 'Súbor sa nenašiel',
    pleaseProvideADescription: 'Zadajte popis',
    pleaseEnterSomeFeedback: 'Zadajte nejakú spätnú väzbu',
    title: 'Názov:',
    setAsMainSiteFeature: 'Nastaviť ako hlavnú funkciu',
    thisIsTheMainSiteFeature: 'Toto je hlavná funkcia',
    customized: 'Prispôsobené',
    copyHtmlCode: 'Skopírujte kód HTML',
    playerSize: 'Veľkosť prehrávača',
    selectSource: 'Vybrať zdroj',
    myAlbums: 'Albumy',
    myMusic: 'Hudba',
    myVideos: 'Video',
    showPlaylist: 'Zobraziť zoznam skladieb',
    change: 'Zmeniť',
    changing: 'Prebieha zmena...',
    changeSettings: 'Zmeniť nastavenie?',
    keepWindowOpenWhileChanging: 'Nechajte okno prehliadača otvorené, kým prebieha zmena nastavenia ochrany súkromia. Môže to trvať niekoľko minút.',
    htmlNotAllowed: 'Nepovolený formát HTML',
    noFriendsFound: 'Neboli nájdení žiadni priatelia, ktorí zodpovedajú kritériám vyhľadávania.',
    yourSubject: 'Predmet',
    yourMessage: 'Správa',
    pleaseEnterFbApiKey: 'Zadajte svoj kód pre rozhranie API siete Facebook.',
    pleaseEnterValidFbApiKey: 'Zadajte platný kód pre rozhranie API siete Facebook.',
    pleaseEnterFbApiSecret: 'Zadajte dôverný kód pre rozhranie API siete Facebook.',
    pleaseEnterValidFbApiSecret: 'Zadajte platný dôverný kód pre rozhranie API siete Facebook.',
    pleaseEnterFbTabName: 'Zadajte názov vašej karty aplikácií v rámci siete Facebook.',
    pleaseEnterValidFbTabName: function(maxChars) {
                                   return 'Zadajte kratší názov vašej karty aplikácií v rámci siete Facebook. Maximálna dĺžka je ' + maxChars + ' znakov' + (maxChars == 1 ? '' : 's ') + '. ';
                               },
    newTab: 'Nová karta',
    areYouSureNavigateAway: 'Máte neuložené zmeny',
    youTabUpdated: 'Nová karta bola uložená',
    resetToDefaults: 'Späť na pôvodné nastavenie',
    saveYourTab: 'Uložiť túto kartu?',
    yes: 'Áno',
    no: 'Nie',
    youMustSpecifyTabName: 'Musíte uviesť názov karty',
    saveYourChanges: 'Chcete uložiť zmeny na tejto karte?',
    youNaviWillbeRestored: 'Vaše navigačné karty boli zmenené späť na pôvodnú navigáciu siete.',
    removeConfirm: 'Keď odstránite túto hlavnú kartu, odstráni sa aj vedľajšia karta. Keď chcete pokračovať, kliknite na OK.',
    hiddenWarningTop: function(n) { return 'Táto karta nebola pridaná na vašu sieť. Bol stanovený limit '+n+' hlavných kariet. '+ 'Odstráňte hlavné karty alebo ich zmeňte na vedľajšie karty.' },
    hiddenWarningSub: function(n) { return 'Táto vedľajšia karta nebola pridaná na vašu sieť. Bol stanovený limit '+n+' vedľajších kariet na hlavnú kartu. '+ 'Odstráňte vedľajšie karty alebo ich zmeňte na hlavné karty.' }
});


dojo.evalObjPath('xg.music.nls', true);
dojo.lang.mixin(xg.music.nls, xg.index.i18n, {
    play: 'prehrať',
    pleaseSelectTrackToUpload: 'Vyberte pieseň, ktorú chcete preniesť.',
    pleaseEnterTrackLink: 'Zadajte adresu URL piesne.',
    thereAreUnsavedChanges: 'Urobili ste zmeny, ktoré nie sú uložené.',
    autoplay: 'Automatické prehrávanie',
    showPlaylist: 'Zobraziť zoznam skladieb',
    playLabel: 'Prehrať',
    url: 'adresa URL',
    rssXspfOrM3u: 'rss, xspf alebo m3u',
    save: 'Uložiť',
    cancel: 'Zrušiť',
    edit: 'Upraviť',
    shufflePlaylist: 'Náhodný výber zo zoznamu skladieb',
    fileIsNotAnMp3: 'Jeden zo súborov pravdepodobne nie je vo formáte mp3. Chcete ho aj tak preniesť?',
    entryNotAUrl: 'Jedna zo zadaných položiek pravdepodobne nie je adresa URL. Skontrolujte, či sa všetky položky začínajú na <kbd>http://</kbd>'
});


dojo.evalObjPath('xg.page.nls', true);
dojo.lang.mixin(xg.page.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Počet znakov (' + n + ') presahuje maximálnu hodnotu (' + maximum + ') '; },
    pleaseEnterContent: 'Zadajte obsah stránky',
    pleaseEnterTitle: 'Zadajte názov stránky',
    pleaseEnterAComment: 'Zadajte komentár',
    deleteThisComment: 'Naozaj chcete vymazať tento komentár?',
    save: 'Uložiť',
    cancel: 'Zrušiť',
    edit: 'Upraviť',
    close: 'Zavrieť',
    displayPagePosts: 'Zobraziť príspevky na stránke',
    directory: 'Adresár',
    displayTab: 'Zobraziť kartu',
    addAnotherPage: 'Pridať ďalšiu stranu',
    tabText: 'Text na karte',
    urlDirectory: 'Adresár URL',
    displayTabForPage: 'Zobraziť kartu pre danú stránku alebo nie',
    tabTitle: 'Názov karty',
    remove: 'Odstrániť',
    thereIsAProblem: 'Vyskytol sa problém súvisiaci so zadanou informáciou'
});


dojo.evalObjPath('xg.photo.nls', true);
dojo.lang.mixin(xg.photo.nls, xg.index.i18n, {
    randomOrder: 'Náhodné poradie',
    untitled: 'Bez názvu',
    photos: 'Fotografie',
    edit: 'Upraviť',
    photosFromAnAlbum: 'Albumy',
    show: 'Zobraziť',
    rows: 'riadky',
    cancel: 'Zrušiť',
    save: 'Uložiť',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Počet znakov (' + n + ') presahuje maximálnu hodnotu (' + maximum + ') '; },
    pleaseSelectPhotoToUpload: 'Vyberte fotografiu, ktorú chcete preniesť.',
    importingNofMPhotos: function(n,m) { return 'Prebieha prenos <span id="currentP">' + n + '</span> z ' + m + ' fotografií. '},
    starting: 'Spúšťa sa...',
    done: 'Dokončené.',
    from: 'Od',
    display: 'Displej',
    takingYou: 'Pripravuje sa prezeranie fotografií...',
    anErrorOccurred: 'Je nám ľúto, ale vyskytla sa chyba. Ohláste ju pomocou prepojenia uvedeného v dolnej časti stránky.',
    weCouldntFind: 'Nenašli sme žiadne fotografie. Nechcete skúsiť niektorú z ďalších možností?'
});


dojo.evalObjPath('xg.activity.nls', true);
dojo.lang.mixin(xg.activity.nls, xg.index.i18n, {
    edit: 'Upraviť',
    show: 'Zobraziť',
    events: 'udalosti',
    setWhatActivityGetsDisplayed: 'Nastavte aktivitu, ktorá sa má zobraziť',
    save: 'Uložiť',
    cancel: 'Zrušiť'
});


dojo.evalObjPath('xg.profiles.nls', true);
dojo.lang.mixin(xg.profiles.nls, xg.index.i18n, {
    removeFriendTitle: function(username) {return 'Remove ' + username + ' As Friend?'; },
    removeFriendConfirm: function(username) {return 'Ste si istí, že chcete odstrániť ' + username + ' as a friend?' },
    pleaseEnterValueForPost: 'Zadajte hodnotu pre príspevok',
    edit: 'Upraviť',
    recentlyAdded: 'Nedávno pridané',
    featured: 'Predstavené',
    iHaveRecentlyAdded: 'Moje nedávno pridané',
    fromTheSite: 'Zo spoločenskej siete',
    cancel: 'Zrušiť',
    save: 'Uložiť',
    loading: 'Načítavanie...',
    addAsFriend: 'Pridať ako priateľa',
    requestSent: 'Požiadavka odoslaná',
    sendingFriendRequest: 'Odosiela sa žiadosť o priateľstvo',
    thisIsYou: 'Toto ste vy',
    isYourFriend: 'je váš priateľ',
    isBlocked: 'je zablokované',
    pleaseEnterPostBody: 'Zadajte text príspevku',
    pleaseEnterChatter: 'Zadajte svoj komentár',
    letMeApproveChatters: 'Umožniť schválenie komentárov pred odoslaním?',
    noPostChattersImmediately: 'Nie – okamžite odoslať komentáre',
    yesApproveChattersFirst: 'Áno – schvaľovať komentáre pre odoslaním',
    yourCommentMustBeApproved: 'Váš komentár musí byť schválený, kým sa zobrazí pre iných.',
    reallyDeleteThisPost: 'Naozaj chcete odstrániť tento príspevok?',
    commentWall: 'Stena komentárov',
    commentWallNComments: function(n) {
        switch(n) {
            case 1: return 'Stena komentárov (1 komentár) ';
            default: return 'Stena komentárov (' + n + ' komentáre/-ov) ';
        }
    },
    display: 'Displej',
    from: 'Od',
    show: 'Zobraziť',
    rows: 'riadky',
    posts: 'príspevky',
    returnToDefaultWarning: 'Týmto krokom sa obnoví predvolené nastavenie všetkých funkcií a motívu na lokalite Moja stránka. Chcete pokračovať?',
    networkError: 'Chyba siete',
    wereSorry: 'Je nám ľúto, ale v tejto chvíli nie sme schopní uložiť váš nový návrh. Príčinou je pravdepodobne prerušenie internetového pripojenia. Skontrolujte pripojenie a skúste znova.',
    addFeature: 'Pridať funkciu',
    addFeatureConfirmation: function(linkAttributes) { return '<p>Chystáte sa pridať novú funkciu na lokalitu Moja stránka. Táto funkcia bola vyvinutá treťou stranou.</p> <p>Kliknutím na položku Pridať funkciu potvrdíte súhlas s <a ' + linkAttributes + '>podmienkami používania </a> aplikácií na tejto platforme.</p> '; }
});


dojo.evalObjPath('xg.shared.nls', true);
dojo.lang.mixin(xg.shared.nls, xg.index.i18n, {
    yourMessage: 'Správa',
    updateMessage: 'Aktualizovať správu',
    updateMessageQ: 'Aktualizovať správu?',
    removeWords: 'V záujme úspešného doručenia e-mailu odporúčame, aby ste sa vrátili späť a zmenili alebo odstránili nasledujúce slová:',
    warningMessage: 'V tomto e-maile je pravdepodobne niekoľko slov, ktoré môžu spôsobiť vyradenie vášho e-mailu do priečinka pre nevyžiadanú poštu.',
    errorMessage: 'V tomto e-maile je minimálne 6 slov, ktoré môžu spôsobiť vyradenie vášho e-mailu do priečinka pre nevyžiadanú poštu.',
    goBack: 'Späť',
    sendAnyway: 'Aj tak odoslať',
    messageIsTooLong: function(n,m) { return 'Je nám ľúto, ale maximálny počet slov je '+m+'. ' },
    locationNotFound: function(location) { return '<em>' + location + '</em> sa nenašla. '; },
    confirmation: 'Potvrdenie',
    showMap: 'Zobraziť mapu',
    hideMap: 'Skryť mapu',
    yourCommentMustBeApproved: 'Váš komentár musí byť schválený, kým sa zobrazí pre iných.',
    nComments: function(n) {
        switch(n) {
            case 1: return '1 komentár ';
            default: return n + 'poznámky ';
        }
    },
    pleaseEnterAComment: 'Zadajte komentár',
    uploadAPhoto: 'Preniesť fotografiu',
    uploadAnImage: 'Preniesť obrázok',
    uploadAPhotoEllipsis: 'Preniesť fotografiu...',
    useExistingImage: 'Použiť existujúci obrázok:',
    existingImage: 'Existujúci obrázok',
    useThemeImage: 'Použiť obrázok motívu:',
    themeImage: 'Obrázok motívu',
    noImage: 'Žiadny obrázok',
    uploadImageFromComputer: 'Preniesť obrázok z počítača',
    tileThisImage: 'Usporiadať obrázok',
    done: 'Dokončené',
    currentImage: 'Aktuálny obrázok',
    pickAColor: 'Vybrať farbu...',
    openColorPicker: 'Otvoriť farebnú škálu',
    transparent: 'Priehľadná',
    loading: 'Načítavanie...',
    ok: 'OK',
    save: 'Uložiť',
    cancel: 'Zrušiť',
    saving: 'Ukladanie...',
    addAnImage: 'Pridať obrázok',
    uploadAFile: 'Preniesť súbor',
    pleaseEnterAWebsite: 'Zadajte adresu webovej lokality',
    pleaseEnterAFileAddress: 'Zadajte adresu súboru',
    bold: 'Tučné',
    italic: 'Kurzíva',
    underline: 'Podčiarknuté',
    strikethrough: 'Prečiarknuté',
    addHyperink: 'Pridať hypertextový odkaz',
    options: 'Možnosti',
    wrapTextAroundImage: 'Zalomiť text okolo obrázka?',
    imageOnLeft: 'Obrázok vľavo?',
    imageOnRight: 'Obrázok vpravo?',
    createThumbnail: 'Vytvoriť miniatúru?',
    pixels: 'pixely',
    createSmallerVersion: 'Vytvorte menšiu verziu obrázka, ktorá sa zobrazí. Zadajte šírku v pixeloch.',
    popupWindow: 'Kontextové okno?',
    linkToFullSize: 'Prepojenie k obrázku v plnej veľkosti v kontextovom okne.',
    add: 'Pridať',
    keepWindowOpen: 'Nechajte okno prehliadača otvorené, kým prebieha prenos.',
    cancelUpload: 'Zrušiť prenos',
    pleaseSelectAFile: 'Vyberte súbor s obrázkom',
    pleaseSpecifyAThumbnailSize: 'Zadajte veľkosť miniatúry',
    thumbnailSizeMustBeNumber: 'Veľkosť miniatúry musí byť zadaná vo forme číslice',
    addExistingImage: 'alebo vložte existujúci obrázok',
    addExistingFile: 'alebo vložte existujúci súbor',
    clickToEdit: 'Kliknite a upravte',
    sendingFriendRequest: 'Odosiela sa žiadosť o priateľstvo',
    requestSent: 'Požiadavka odoslaná',
    pleaseCorrectErrors: 'Opravte tieto chyby',
    noo: 'NOVÝ',
    none: 'ŽIADNY',
    joinNow: 'Pripojiť teraz',
    join: 'Pripojiť',
    addToFavorites: 'Pridať do obľúbených položiek',
    removeFromFavorites: 'Odstrániť z obľúbených položiek',
    follow: 'Pokračovať',
    stopFollowing: 'Zastaviť pokračovanie',
    pendingPromptTitle: 'Prebieha schvaľovanie členstva',
    youCanDoThis: 'Tento krok môžete urobiť až po schválení vášho členstva zo strany správcov.',
    editYourTags: 'Upraviť značky',
    addTags: 'Pridať značky',
    editLocation: 'Upraviť umiestnenie',
    editTypes: 'Upraviť typ udalosti',
    charactersLeft: function(n) {
        if (n >= 0) {
            return '&nbsp;(' + n + ' left)' ;
        } else {
            return  '&nbsp;(' + Math.abs(n) + ' over)' ;
        }
    },
    commentWall: 'Stena komentárov',
    commentWallNComments: function(n) { switch(n) { case 1: return 'Stena komentárov (1 komentár)'; default: return 'Stena komentárov (' + n + ' komentáre(ov))'; } }
});


dojo.evalObjPath('xg.video.nls', true);
dojo.lang.mixin(xg.video.nls, xg.index.i18n, {
    edit: 'Upraviť',
    display: 'Displej',
    detail: 'Detail',
    player: 'Prehrávač',
    from: 'Od',
    show: 'Zobraziť',
    videos: 'video',
    cancel: 'Zrušiť',
    save: 'Uložiť',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Počet znakov (' + n + ') presahuje maximálnu hodnotu (' + maximum + ') '; },
    approve: 'Schváliť',
    approving: 'Schvaľovanie...',
    keepWindowOpenWhileApproving: 'Nechajte okno prehľadávača otvorené, kým prebieha schvaľovanie videa. Môže to trvať niekoľko minút.',
    'delete': 'Odstrániť',
    deleting: 'Odstraňuje sa....',
    keepWindowOpenWhileDeleting: 'Nechajte okno prehľadávača otvorené, kým  prebieha odstránenie videa. Môže to trvať niekoľko minút.',
    pasteInEmbedCode: 'Nalepte vložený kód pre video z inej stránky.',
    pleaseSelectVideoToUpload: 'Vyberte video, ktoré chcete preniesť.',
    embedCodeContainsMoreThanOneVideo: 'Vložený kód obsahuje viac ako jedno video. Skontrolujte, či má iba jednu <objektovú>, prípadne <vloženú> značku.',
    embedCodeMissingTag: 'Vo vloženom kóde chýba &lt;vložená&gt; alebo &lt;objektová&gt; značka.',
    fileIsNotAMov: 'Súbor pravdepodobne nie je vo formáte .mov, .mpg, .mp4, .avi, .3gp ani .wmv. Naozaj ho chcete skúsiť preniesť?',
    embedHTMLCode: 'Vložený kód HTML:',
    directLink: 'Priame prepojenie',
    addToMyspace: 'Pridať na lokalitu MySpace',
    shareOnFacebook: 'Zdieľať v sieti Facebook',
    addToOthers: 'Pridať k iným'
});


dojo.evalObjPath('xg.uploader.nls', true);
dojo.lang.mixin(xg.uploader.nls, xg.index.i18n, {
    fileBrowserHeader: 'Tento počítač',
    fileRoot: 'Tento počítač',
    fileInformationHeader: 'Informácie',
    uploadHeader: 'Súbory na prenesenie',
    dragOutInstructions: 'Súbory odstránite, ak ich odsuniete pomocou myši',
    dragInInstructions: 'Presuňte súbory sem.',
    selectInstructions: 'Vyberte súbor',
    files: 'Súbory',
    totalSize: 'Celková veľkosť',
    fileName: 'Názov',
    fileSize: 'Veľkosť',
    nextButton: 'Ďalej >',
    okayButton: 'OK',
    yesButton: 'Áno',
    noButton: 'Nie',
    uploadButton: 'Preniesť',
    cancelButton: 'Zrušiť',
    backButton: 'Späť',
    continueButton: 'Pokračovať',
    uploadingStatus: function(n, m) { return 'Prenos ' + n + ' z ' + m; },
    uploadLimitWarning: function(n) { return 'Môžete preniesť ' + n + ' súborov naraz. '; },
    uploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Pridali ste maximálny počet súborov. ';
            case 1: return 'Môžete preniesť ešte jeden súbor. ';
            default: return 'Môžete preniesť ešte ' + n + ' súborov. ';
        }
    },
    uploadingLabel: 'Prebieha prenos...',
    uploadingInstructions: 'Počas prenosu nechajte toto okno otvorené.',
    iHaveTheRight: 'Mám právo preniesť tieto súbory v súlade s <a href="/main/authorization/termsOfService">podmienkami poskytovania služby</a>',
    updateJavaTitle: 'Aktualizovať jazyk Java',
    updateJavaDescription: 'Aplikácia na hromadný prenos údajov si vyžaduje aktuálnejšiu verziu jazyka Java. Kliknite na položku OK a prevezmite jazyk Java.',
    batchEditorLabel: 'Upraviť informácie pre všetky položky',
    applyThisInfo: 'Aplikovať tieto informácie na nasledujúce súbory',
    titleProperty: 'Názov',
    descriptionProperty: 'Popis',
    tagsProperty: 'Značky',
    viewableByProperty: 'Prezeranie povolené',
    viewableByEveryone: 'všetkým',
    viewableByFriends: 'iba mojim priateľom',
    viewableByMe: 'iba mne',
    albumProperty: 'Album',
    artistProperty: 'Interpret',
    enableDownloadLinkProperty: 'Aktivovať prepojenie na prevzatie údajov',
    enableProfileUsageProperty: 'Umožniť ľuďom, aby si preniesli túto pieseň na svoju stránku.',
    licenseProperty: 'Licencia',
    creativeCommonsVersion: '3.0',
    selectLicense: '— Vybrať licenciu —',
    copyright: '© Všetky práva vyhradené',
    ccByX: function(n) { return 'Creative Commons Attribution ' + n; },
    ccBySaX: function(n) { return 'Creative Commons Attribution Share Alike ' + n; },
    ccByNdX: function(n) { return 'Creative Commons Attribution No Derivatives ' + n; },
    ccByNcX: function(n) { return 'Creative Commons Attribution Non-commercial ' + n; },
    ccByNcSaX: function(n) { return 'Creative Commons Attribution Non-commercial Share Alike ' + n; },
    ccByNcNdX: function(n) { return 'Creative Commons Attribution Non-commercial No Derivatives ' + n; },
    publicDomain: 'Verejná doména',
    other: 'Iné',
    errorUnexpectedTitle: 'Ejha!',
    errorUnexpectedDescription: 'Došlo k chybe. Skúste znova.',
    errorTooManyTitle: 'Príliš veľa položiek',
    errorTooManyDescription: function(n) { return 'Je nám ľúto, ale nemôžete preniesť viac ako ' + n + ' položiek naraz. '; },
    errorNotAMemberTitle: 'Nepovolené',
    errorNotAMemberDescription: 'Je nám ľúto, ale prenášať údaje môžu iba členovia.',
    errorContentTypeNotAllowedTitle: 'Nepovolené',
    errorContentTypeNotAllowedDescription: 'Je nám ľúto, ale nemáte povolenie na prenos tohto typu obsahu.',
    errorUnsupportedFormatTitle: 'Ejha!',
    errorUnsupportedFormatDescription: 'Je nám ľúto, ale nepodporujeme tento typ súboru.',
    errorUnsupportedFileTitle: 'Ejha!',
    errorUnsupportedFileDescription: 'foo.exe nie je podporovaný formát.',
    errorUploadUnexpectedTitle: 'Ejha!',
    errorUploadUnexpectedDescription: function(file) {
        return file ?
            ('Zdá sa, že sa vyskytol problém v súbore ' + file + '. Prosím, odstráňte ho zo zoznamu pred odoslaním vašich ďalších súborov.') :
            'Zdá sa, že sa vyskytol problém so súborom navrchu zoznamu. Prosím, odstráňte ho pred odoslaním vašich ďalších súborov. ';
    },
    cancelUploadTitle: 'Zrušiť prenos?',
    cancelUploadDescription: 'Naozaj chcete zrušiť zostávajúci prenos?',
    uploadSuccessfulTitle: 'Prenos je dokončený',
    uploadSuccessfulDescription: 'Počkajte, kým budú vaše súbory k dispozícii...',
    uploadPendingDescription: 'Vaše súbory boli úspešne prenesené a čakajú na schválenie.',
    photosUploadHeader: 'Fotografie, ktoré majú byť prenesené',
    photosDragOutInstructions: 'Fotografie odstránite, ak ich odsuniete pomocou myši',
    photosDragInInstructions: 'Presuňte fotografie sem',
    photosSelectInstructions: 'Vybrať fotografiu',
    photosFiles: 'Fotografie',
    photosUploadingStatus: function(n, m) { return 'Prenáša sa ' + n + ' fotografia z ' + m; },
    photosErrorTooManyTitle: 'Príliš veľa fotografií',
    photosErrorTooManyDescription: function(n) { return 'Je nám ľúto, ale nemôžete preniesť viac ako ' + n + ' fotografií naraz. '; },
    photosErrorContentTypeNotAllowedDescription: 'Je nám ľúto, ale prenos fotografií bol zrušený.',
    photosErrorUnsupportedFormatDescription: 'Je nám ľúto, ale prenášať môžete iba obrázky vo formáte .jpg, .gif alebo .png.',
    photosErrorUnsupportedFileDescription: function(n) { return n + 'nie je súbor .jpg, .gif alebo .png. '; },
    photosBatchEditorLabel: 'Upraviť informácie pre všetky fotografie',
    photosApplyThisInfo: 'Aplikovať tieto informácie na nasledujúce fotografie',
    photosErrorUploadUnexpectedDescription: function(file) {
        return file ?
            ('Zdá sa, že sa vyskytol problém v súbore ' + file + '. Prosím, odstráňte ho zo zoznamu pred odoslaním vašich ďalších fotografií.') :
            'Zdá sa, že sa vyskytol problém s fotografiou navrchu zoznamu. Prosím, odstráňte ju pred odoslaním vašich ďalších fotografií. ';
    },
    photosUploadSuccessfulDescription: 'Počkajte, kým budú vaše fotografie k dispozícii...',
    photosUploadPendingDescription: 'Vaše fotografie boli úspešne prenesené a čakajú na schválenie.',
    photosUploadLimitWarning: function(n) { return 'Môžete preniesť naraz ' + n + ' fotografií. '; },
    photosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Pridali ste maximálny počet fotografií. ';
            case 1: return 'Môžete preniesť ešte 1 fotografiu. ';
            default: return 'Môžete preniesť ešte ' + n + ' fotografie(-ií). ';
        }
    },
    photosIHaveTheRight: 'Mám právo preniesť tieto fotografie v súlade s <a href="/main/authorization/termsOfService">podmienkami poskytovania služby</a>',
    videosUploadHeader: 'Videá na prenesenie',
    videosDragInInstructions: 'Presuňte videá sem',
    videosDragOutInstructions: 'Videá odstránite, ak ich odsuniete pomocou myši',
    videosSelectInstructions: 'Vybrať video',
    videosFiles: 'Videá',
    videosUploadingStatus: function(n, m) { return 'Prenos ' + n + ' videa z ' + m; },
    videosErrorTooManyTitle: 'Príliš veľa videí',
    videosErrorTooManyDescription: function(n) { return 'Je nám ľúto, ale nemôžete preniesť viac ako ' + n + ' videí naraz. '; },
    videosErrorContentTypeNotAllowedDescription: 'Je nám ľúto, ale prenos videa bol zrušený.',
    videosErrorUnsupportedFormatDescription: 'Je nám ľúto, ale prenášať môžete iba videá vo formátoch .avi, .mov, .mp4, .wmv alebo .mpg.',
    videosErrorUnsupportedFileDescription: function(x) { return x + 'nie je súbor .avi, .mov, .mp4, .wmv alebo .mpg. '; },
    videosBatchEditorLabel: 'Upraviť informácie pre všetky videá',
    videosApplyThisInfo: 'Aplikovať tieto informácie na nasledujúce videá',
    videosErrorUploadUnexpectedDescription: function(file) {
        return file ?
            ('Zdá sa, že sa vyskytol problém so súborom ' + file + '. Prosím, odstráňte ho zo zoznamu pred odoslaním vašich ďalších videí.') :
            'Zdá sa, že sa vyskytol problém s videom navrchu zoznamu. Prosím, odstráňte ho pred odoslaním vašich ďalších videí. ';
    },
    videosUploadSuccessfulDescription: 'Počkajte, kým budú vaše videá k dispozícii...',
    videosUploadPendingDescription: 'Vaše videá boli úspešne prenesené a čakajú na schválenie.',
    videosUploadLimitWarning: function(n) { return 'Môžete preniesť ' + n + ' videí naraz. '; },
    videosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Pridali ste maximálny počet videí. ';
            case 1: return 'Môžete preniesť ešte jedno video. ';
            default: return 'Môžete preniesť ešte ' + n + ' videá(-í). ';
        }
    },
    videosIHaveTheRight: 'Mám právo preniesť tieto videá v súlade s <a href="/main/authorization/termsOfService">podmienkami poskytovania služby</a>',
    musicUploadHeader: 'Piesne na prenesenie',
    musicTitleProperty: 'Názov piesne',
    musicDragOutInstructions: 'Piesne odstránite, ak ich odsuniete pomocou myši',
    musicDragInInstructions: 'Presuňte piesne sem',
    musicSelectInstructions: 'Vybrať pieseň',
    musicFiles: 'Piesne',
    musicUploadingStatus: function(n, m) { return 'Prenos ' + n + ' piesne z ' + m; },
    musicErrorTooManyTitle: 'Príliš veľa piesní',
    musicErrorTooManyDescription: function(n) { return 'Je nám ľúto, ale nemôžete preniesť viac ako ' + n + ' skladieb naraz. '; },
    musicErrorContentTypeNotAllowedDescription: 'Je nám ľúto, ale prenos piesne bol zrušený.',
    musicErrorUnsupportedFormatDescription: 'Je nám ľúto, ale preniesť môžete iba piesne vo formáte mp3.',
    musicErrorUnsupportedFileDescription: function(x) { return x + 'nie je súbor .mp3. '; },
    musicBatchEditorLabel: 'Upraviť informácie pre všetky piesne',
    musicApplyThisInfo: 'Aplikovať tieto informácie na nasledujúce piesne',
    musicErrorUploadUnexpectedDescription: function(file) {
        return file ?
            ('Zdá sa, že sa vyskytol problém so súborom ' + file + '. Prosím, odstráňte ho zo zoznamu pred odoslaním vašich ďalších piesní.') :
            'Zdá sa, že sa vyskytol problém s piesňou navrchu zoznamu. Prosím, odstráňte ho pred odoslaním vašich ďalších piesní. ';
    },
    musicUploadSuccessfulDescription: 'Počkajte, kým budú vaše piesne k dispozícii...',
    musicUploadPendingDescription: 'Vaše piesne boli úspešne prenesené a čakajú na schválenie.',
    musicUploadLimitWarning: function(n) { return 'Môžete preniesť ' + n + ' skladieb naraz. '; },
    musicUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Pridali ste maximálny počet piesní. ';
            case 1: return 'Môžete preniesť ešte jednu pieseň. ';
            default: return 'Môžete preniesť ešte ' + n + ' piesne(-í). ';
        }
    },
    musicIHaveTheRight: 'Mám právo preniesť tieto piesne v súlade s <a href="/main/authorization/termsOfService">podmienkami poskytovania služby</a>'
});


dojo.evalObjPath('xg.events.nls', true);
dojo.lang.mixin(xg.events.nls, xg.index.i18n, {
    pleaseEnterTitle: 'Zadajte názov udalosti',
    pleaseEnterDescription: 'Zadajte popis udalosti',
    messageIsTooLong: function(n) { return 'Správa je príliš dlhá. Použite maximálne '+n+' znakov. '; },
    pleaseEnterLocation: 'Zadajte miesto udalosti',
    pleaseChooseImage: 'Vyberte fotografiu pre udalosť',
    pleaseEnterType: 'Zadajte aspoň jeden typ udalosti',
    sendMessageToGuests: 'Postať správu hosťom',
    sendMessageToGuestsThat: 'Poslať správu hosťom, ktorí:',
    areAttending: 'sú prítomní',
    mightAttend: 'by mohli byť prítomní',
    haveNotYetRsvped: 'Ešte neodpovedal(-a) na pozvánku',
    areNotAttending: 'nie sú prítomní',
    yourMessage: 'Správa',
    send: 'Odoslať',
    sending: 'Odosielanie...',
    yourMessageIsBeingSent: 'Správa sa posiela.',
    messageSent: 'Správa bola odoslaná.',
    yourMessageHasBeenSent: 'Vaša správa bola odoslaná.',
    chooseRecipient: 'Vyberte adresáta.',
    pleaseEnterAMessage: 'Zadajte správu',
    thereHasBeenAnError: 'Chyba'
});


dojo.evalObjPath('xg.notes.nls', true);
dojo.lang.mixin(xg.notes.nls, xg.index.i18n, {
    addNewNote: 'Pridať novú poznámku',
    pleaseEnterNoteTitle: 'Zadajte názov poznámky.',
    noteTitleTooLong: 'Názov poznámky je príliš dlhý',
    pleaseEnterNoteEntry: 'Zadajte poznámku'
});