dojo.provide('xg.shared.messagecatalogs.hu_HU');

dojo.require('xg.index.i18n');

/**
 * Texts for the Hungarian (Hungary
 */
// Use UTF-8 byte

dojo.evalObjPath('xg.feed.nls', true);
dojo.lang.mixin(xg.feed.nls, xg.index.i18n, {
    edit: 'Szerkesztés',
    title: 'Cím:',
    feedUrl: 'URL:',
    show: 'Megjelenítés:',
    titles: 'Csak címek',
    titlesAndDescriptions: 'Részletes nézet',
    display: 'Megjelenít',
    cancel: 'Mégse',
    save: 'Mentés',
    loading: 'Betöltés...',
    items: 'elemek'
});


dojo.evalObjPath('xg.opensocial.nls', true);
dojo.lang.mixin(xg.opensocial.nls, xg.index.i18n, {
    edit: 'Szerkesztés',
    title: 'Cím:',
    appUrl: 'URL:',
    cancel: 'Mégse',
    save: 'Mentés',
    loading: 'Betöltés...',
    removeBox: 'Keret eltávolítása',
    removeBoxText: function(feature) { return '<p>Biztos benne, hogy el szeretné távolítani a "' + feature + '" keretet a Kezdőlapról?</p><p>Ön még mindig hozzáférhet ehhez a funkcióhoz a "További Funkcióim" alatt.</p> '},
    removeFeature: 'Funkció eltávolítása',
    removeFeatureText: 'Biztos benne, hogy teljesen el szeretné távolítani ezt a funkciót? Ezt követően sem a Kezdőlapról, sem a További Funkcióim alatt nem lesz elérhető.',
    canSendMessages: 'Küldjenek nekem üzeneteket',
    canAddActivities: 'A frissítések megjelenítése a Legújabb tevékenységek modulban a Kezdőlapon',
    addFeature: 'Funkció hozzáadása',
    youAreAboutToAdd: function(feature, linkAttributes) { return '<p>Ön hozzá fogja adni a <strong>' + feature + '</strong>-t a Kezdőlaphoz. Ezt egy harmadik fél fejlesztette ki.</p><p>A \'Funkció hozzáadására\' kattintva Ön elfogadja a Platform Alkalmazások <a' + linkAttributes + '>Használati Feltételeit</a>.</p> '},
    featureSettings: 'Funkció beállítások',
    allowThisFeatureTo: 'Funkció engedélyezése:',
    updateSettings: 'Beállítások frissítése',
    onlyEmailMsgSupported: 'Csak az E-MAIL típusú üzenet támogatott',
    msgExpectedToContain: 'Az üzenetnek minden mezőt tartalmaznia kell: típus, cím és főszöveg',
    recipientsShdBeString: 'A címzettek csak zsinórban lehetnek (vesszővel elválasztott lista rendben van)',
    recipientsShdBeSpecified: 'A címzetteket meg kell adni és nem lehet üres a mező',
    rateLimitExceeded: 'Meghaladta a határértéket',
    userCancelled: 'A felhasználó megszakította a működést',
    msgObjectExpected: 'Üzeneti objektum elvárt',
    unauthorizedRecipients: 'jogosulatlan címzettek vannak megadva'
});


dojo.evalObjPath('xg.gadgets.nls', true);
dojo.lang.mixin(xg.gadgets.nls, xg.index.i18n, {
    edit: 'Szerkesztés',
    title: 'Cím:',
    feedUrl: 'URL:',
    cancel: 'Mégse',
    save: 'Mentés',
    loading: 'Betöltés...',
    removeGadget: 'Minialkalmazás eltávolítása',
    findGadgetsInDirectory: 'Minialkalmazások keresése a Minialkalmazás könyvtárban'
});


dojo.evalObjPath('xg.forum.nls', true);
dojo.lang.mixin(xg.forum.nls, xg.index.i18n, {
    items: 'elemek',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'A karakterek száma (' + n + ') meghaladja a maximálisan megengedettet (' + maximum + ') '; },
    pleaseEnterFirstPost: 'Kérjük, írja meg az első hozzászólást a beszélgetéshez',
    pleaseEnterTitle: 'Kérjük, adja meg a beszélgetés címét',
    save: 'Mentés',
    cancel: 'Mégse',
    yes: 'Igen',
    no: 'Nem',
    edit: 'Szerkesztés',
    deleteCategory: 'Kategória törlése',
    discussionsWillBeDeleted: 'A kategóriához tartozó beszélgetések törlésre kerülnek.',
    whatDoWithDiscussions: 'Mit szeretne tenni az ehhez a kategóriához tartozó beszélgetésekkel?',
    moveDiscussionsTo: 'Beszélgetések áthelyezése ide:',
    deleteDiscussions: 'Beszélgetések törlése',
    'delete': 'Törlés',
    deleteReply: 'Válasz törlése',
    deleteReplyQ: 'Törli ezt a választ?',
    deletingReplies: 'Válaszok törlése...',
    doYouWantToRemoveReplies: 'Szeretné eltávolítani az ehhez a megjegyzéshez tartozó válaszokat is?',
    pleaseKeepWindowOpen: 'Kérjük, ne zárja be a böngészőablakot addig, amíg a feldolgozás be nem fejeződik. Ez eltarthat néhány percig.',
    contributorSaid: function(x) { return x + 'azt mondta: '},
    display: 'Megjelenít',
    from: 'Feladó',
    show: 'Megjelenítés',
    view: 'Nézet',
    discussions: 'beszélgetések',
    discussionsFromACategory: 'Beszélgetések egy kategóriából...'
});


dojo.evalObjPath('xg.groups.nls', true);
dojo.lang.mixin(xg.groups.nls, xg.index.i18n, {
    pleaseChooseAName: 'Kérjük, válasszon nevet a csoportjának.',
    pleaseChooseAUrl: 'Kérjük, válasszon webcímet a csoportjának.',
    urlCanContainOnlyLetters: 'A webcím csak betűket és számokat tartalmazhat (szóközt nem).',
    descriptionTooLong: function(n, maximum) { return 'A csoportleírás hossza (' + n + ') meghaladja a maximálisan megengedettet (' + maximum +') '; },
    nameTaken: 'Elnézését kérjük – ez a név már foglalt. Kérjük, válasszon másik nevet.',
    urlTaken: 'Elnézését kérjük – ez a webcím már foglalt. Kérjük, válasszon másik webcímet.',
    whyNot: 'Miért ne?',
    groupCreatorDetermines: function(href) { return 'A csoport létrehozója határozza meg, hogy ki csatlakozhat. Ha Ön úgy érzi, hogy véletlenül zárták ki, lépjen kapcsolatba <a' + href + '>a csoport létrehozójával</a> '; },
    edit: 'Szerkesztés',
    from: 'Feladó',
    show: 'Megjelenítés',
    groups: 'csoportok',
    pleaseEnterName: 'Kérjük, adja meg nevét',
    pleaseEnterEmailAddress: 'Kérjük, adja meg e-mail címét',
    xIsNotValidEmailAddress: function(x) { return x + 'nem érvényes e-mail cím '; },
    save: 'Mentés',
    cancel: 'Mégse'
});


dojo.evalObjPath('xg.html.nls', true);
dojo.lang.mixin(xg.html.nls, xg.index.i18n, {
    contentsTooLong: function(maximum) { return 'A tartalom túl hosszú. Kérjük, legfeljebb ' + maximum + ' karaktert használjon. '; },
    edit: 'Szerkesztés',
    save: 'Mentés',
    cancel: 'Mégse',
    saving: 'Mentés...',
    addAWidget: function(url) { return '<a href="' + url + '">Minialkalmazás hozzáadása</a> a szövegdobozhoz '; }
});


dojo.evalObjPath('xg.index.nls', true);
dojo.lang.mixin(xg.index.nls, xg.index.i18n, {
    done: 'Kész',
    yourMessageIsBeingSent: 'Az üzenet elküldése folyamatban van.',
    youNeedToAddEmailRecipient: 'Meg kell adnia az e-mail címzettjét.',
    checkPageOut: function (network) {return 'Tekintse meg ezt az oldalt itt '+network},
    checkingOutTitle: function (title, network) {return 'Tekintse meg "'+title+'" itt '+network},
    selectOrPaste: 'Ki kell választania egy videót, vagy be kell illesztenie az \'embed\' kódot.',
    selectOrPasteMusic: 'Ki kell választania egy dalt, vagy be kell illesztenie az URL-t',
    cannotKeepFiles: 'Ismét ki kell választania a fájlokat, ha a többi opciót is meg szeretne tekinteni. Szeretné folytatni?',
    pleaseSelectPhotoToUpload: 'Kérjük, válassza ki a feltölteni kívánt fotót.',
    addingLabel: 'Hozzáadás...',
    sendingLabel: 'Küldés...',
    addingInstructions: 'Kérjük, ne zárja be ezt az ablakot, amíg a tartalom hozzáadása folyamatban van.',
    looksLikeNotImage: 'Egy vagy több fájl nem a .jpg, .gif vagy .png formátumok valamelyikében van. Ennek ellenére meg kívánja kísérelni a feltöltést?',
    looksLikeNotVideo: 'Az Ön által kiválasztott fájl nem a .mov, .mpg, .mp4, .avi, .3gp vagy .wmv formátumok valamelyikében van. Ennek ellenére meg kívánja kísérelni a feltöltést?',
    looksLikeNotMusic: 'A kiválasztott fájl vélhetően nem .mp3 formátumban van. Ennek ellenére meg kívánja kísérelni a feltöltést?',
    showingNFriends: function(n, searchString) {
        switch(n) {
            case 1: return 'A "' + searchString + '" keresési feltételnek 1 barátja felelt meg. <a href="#">Mindenkit megjelenít</a> ';
            default: return 'A "' + searchString + '" keresési feltételnek ' + n + ' barátja felelt meg. <a href="#">Mindenkit megjelenít</a> ';
        }
    },
    sendInvitation: 'Meghívó küldése',
    sendMessage: 'Üzenet küldése',
    sendInvitationToNFriends: function(n) {
        switch(n) {
            case 1: return 'Meghívó elküldése 1 barátjának? ';
            default: return 'Meghívó elküldése ' + n + ' barátjának? ';
        }
    },
    sendMessageToNFriends: function(n) {
        switch(n) {
            case 1: return 'Üzenet elküldése 1 barátjának? ';
            default: return 'Üzenet elküldése ' + n + ' barátjának? ';
        }
    },
    invitingNFriends: function(n) {
        switch(n) {
            case 1: return '1 barát meghívása... ';
            default: return ' ' + n + ' barát meghívása... ';
        }
    },
    nFriends: function(n) {
        switch(n) {
            case 1: return '1 barát... ';
            default: return n + 'barátok ';
        }
    },
    sendingMessageToNFriends: function(n) {
        switch(n) {
            case 1: return 'Üzenet elküldése 1 barátjának... ';
            default: return 'Üzenet elküldése ' + n + ' barátjának... ';
        }
    },
    yourMessageOptional: '<label>Az Ön üzenete</label> (opcionális)',
    subjectIsTooLong: function(n) { return 'Az üzenet tárgya túl hosszú. Kérjük, legfeljebb '+n+' karaktert használjon. '; },
    messageIsTooLong: function(n) { return 'Az üzenet túl hosszú. Kérjük, legfeljebb '+n+' karaktert használjon. '; },
    pleaseChoosePeople: 'Kérjük, válasszon néhány meghívni kívánt személyt.',
    noPeopleSelected: 'Senki sincs kiválasztva',
    pleaseEnterEmailAddress: 'Kérjük, adja meg e-mail címét.',
    pleaseEnterPassword: function(emailAddress) { return 'Kérjük, adja meg az '+ emailAddress+' címhez tartozó jelszavát. '; },
    sorryWeDoNotSupport: 'Sajnos nem támogatjuk a webes címjegyzék funkciót az Ön email címéhez. Próbáljon meg a \'Címjegyzék Alkalmazás\'-ra kattintani a számítógépén tárolt címek használatához.',
    pleaseSelectSecondPart: 'Kérjük, válassza ki e-mail címe második részét, pl. gmail.com.',
    atSymbolNotAllowed: 'Kérjük, ügyeljen arra, hogy a @ szimbólum ne szerepeljen e-mail címe első részében.',
    resetTextQ: 'Szöveg visszaállítása?',
    resetTextToOriginalVersion: 'Biztosan vissza kívánja állítani a teljes szöveget az eredeti változatra? Minden módosítás elveszik.',
    changeQuestionsToPublic: 'Nyilvánossá teszi a kérdéseket?',
    changingPrivateQuestionsToPublic: 'A magánjellegű kérdések nyilvánossá tétele a tagok válaszait is nyilvánosságra hozza. Biztos benne?',
    youHaveUnsavedChanges: 'Még vannak elmentetlen módosítások.',
    pleaseEnterASiteName: 'Kérjük, adja meg közösségi hálózata nevét, pl. Kis Bohócok Klubja',
    pleaseEnterShorterSiteName: 'Kérjük, adjon meg rövidebb nevet (max. 64 karakter)',
    pleaseEnterShorterSiteDescription: 'Kérjük, adjon meg rövidebb leírást (max. 140 karakter)',
    siteNameHasInvalidCharacters: 'A név érvénytelen karaktereket tartalmaz',
    thereIsAProblem: 'A megadott információkkal kapcsolatosan probléma merült fel',
    thisSiteIsOnline: 'A közösségi hálózat elérhető',
    online: '<strong>Elérhető</strong>',
    onlineSiteCanBeViewed: '<strong>Elérhető</strong> - a hálózat az adatvédelmi beállítások függvényében megtekinthető.',
    takeOffline: 'Lekapcsolás',
    thisSiteIsOffline: 'A közösségi hálózat nem elérhető',
    offline: '<strong>Nem elérhető</strong>',
    offlineOnlyYouCanView: '<strong>Nem elérhető</strong> - csak Ön tekintheti meg a közösségi hálózatot.',
    takeOnline: 'Elérhetővé tétel',
    themeSettings: 'Téma beállítások',
    addYourOwnCss: 'Haladó',
    error: 'Hiba',
    pleaseEnterTitleForFeature: function(displayName) { return 'Kérjük, adja meg a ' + displayName + ' funkciója címét. '; },
    thereIsAProblemWithTheInformation: 'A megadott információkkal kapcsolatosan probléma merült fel',
    photos: 'Fényképek',
    videos: 'Videók',
    pleaseEnterTheChoicesFor: function(questionTitle) { return 'Kérjük, adja meg a választási lehetőségeket a "' + questionTitle + '" kérdéshez, pl. hiking, olvasás, vásárlás '; },
    pleaseEnterTheChoices: 'Kérjük, adjon meg választási lehetőségeket, pl. hiking, olvasás, vásárlás',
    email: 'e-mail',
    subject: 'Tárgy',
    message: 'Üzenet',
    send: 'Küldés',
    cancel: 'Mégse',
    go: 'Ugrás',
    areYouSureYouWant: 'Biztosan végre szeretné hajtani?',
    processing: 'Feldolgozás...',
    pleaseKeepWindowOpen: 'Kérjük, ne zárja be a böngészőablakot addig, amíg a feldolgozás be nem fejeződik. Ez eltarthat néhány percig.',
    complete: 'Kész!',
    processIsComplete: 'A folyamat befejeződött.',
    processingFailed: 'Sajnos a folyamat sikertelen. Kérjük, próbálja meg később.',
    ok: 'OK',
    body: 'Főszöveg',
    pleaseEnterASubject: 'Kérjük, adja meg a tárgyat',
    pleaseEnterAMessage: 'Kérjük, írja be az üzenetet',
    pleaseChooseFriends: 'Kérjük, válassza ki néhány barátját, mielőtt elküldené az üzenetet.',
    thereHasBeenAnError: 'Hiba történt',
    thereWasAProblem: 'A tartalom hozzáadása során probléma merült fel. Kérjük, próbálja meg később.',
    fileNotFound: 'A fájl nem található',
    pleaseProvideADescription: 'Kérjük, adjon meg leírást',
    pleaseEnterSomeFeedback: 'Kérjük, adjon meg valamilyen visszajelzést',
    title: 'Cím:',
    setAsMainSiteFeature: 'Beállítás fő funkcióként',
    thisIsTheMainSiteFeature: 'Ez a fő funkció',
    customized: 'Testreszabott',
    copyHtmlCode: 'HTML kód másolása',
    playerSize: 'Lejátszó méret',
    selectSource: 'Forrás kiválasztása',
    myAlbums: 'Saját albumok',
    myMusic: 'Saját zene',
    myVideos: 'Saját videók',
    showPlaylist: 'Lejátszási lista megjelenítése',
    change: 'Módosítás',
    changing: 'Módosítás...',
    changeSettings: 'Módosítja a beállításokat?',
    keepWindowOpenWhileChanging: 'Kérjük, hagyja nyitva a böngészőablakot, amíg az adadvédelmi beállítások módosítása folyamatban van. Ez több percig is eltarthat.',
    htmlNotAllowed: 'A HTML nem megengedett',
    noFriendsFound: 'A keresési feltételeknek megfelelő barátok nem találhatók.',
    yourSubject: 'Az Ön tárgya',
    yourMessage: 'Az Ön üzenete',
    pleaseEnterFbApiKey: 'Kérjük, adja meg Facebook API kulcsát.',
    pleaseEnterValidFbApiKey: 'Kérjük, érvényes Facebook API kulcsot adjon meg.',
    pleaseEnterFbApiSecret: 'Kérjük, adja meg Facebook API titkát.',
    pleaseEnterValidFbApiSecret: 'Kérjük, érvényes Facebook API titkot adjon meg.',
    pleaseEnterFbTabName: 'Kérjük, adjon nevet Facebook alkalmazásának.',
    pleaseEnterValidFbTabName: function(maxChars) {
                                   return 'Kérjük, rövidebb nevet adjon meg Facebook alkalmazásának. A maximális hosszúság ' + maxChars + ' karakter ' + (maxChars == 1 ? '' : 's') + '.';
                               },
    saveYourTab: 'El szeretné menteni ezt a címkét?',
    yes: 'Igen',
    no: 'Nem',
    youMustSpecifyTabName: 'Meg kell adnia a címke nevét',
    newTab: 'Új címke',
    youTabUpdated: 'A címkét elmentettük',
    saveYourChanges: 'Szeretné a változtatásokat menteni ehhez a címkéhez?',
    areYouSureNavigateAway: 'Nem mentette el a változtatásokat',
    resetToDefaults: 'Alapértelmezés visszaállítása',
    youNaviWillbeRestored: 'A navigációs címkéit vissza fogjuk állítani a hálózat eredeti beállításaira.',
    hiddenWarningTop: function(n) { return 'Ezt a címkét még nem adta hozzá a hálózatához. A felső címkék '+n+'-ra vannak korlátozva. '+ 'Kérjük távolítsa el a felső címkéket, vagy változtassa őket alcímkékké.' },
    hiddenWarningSub: function(n) { return 'Ezt a címkét még nem adta hozzá a hálózatához. A felső címkék '+n+'-ra vannak korlátozva. '+ 'Kérjük távolítsa el a felső címkéket, vagy változtassa őket alcímkékké.' },
    removeConfirm: 'Ennek a felső címkének az eltávolításával az alcímkéket is eltávolítja. Kattintson az OK-ra a folytatáshoz.'
});


dojo.evalObjPath('xg.music.nls', true);
dojo.lang.mixin(xg.music.nls, xg.index.i18n, {
    play: 'lejátszás',
    pleaseSelectTrackToUpload: 'Válasszon ki egy feltöltendő dalt.',
    pleaseEnterTrackLink: 'Kérjük, adja meg a dal URL-jét.',
    thereAreUnsavedChanges: 'Egyes módosítások még nem lettek elmentve.',
    autoplay: 'Automatikus lejátszás',
    showPlaylist: 'Lejátszási lista megjelenítése',
    playLabel: 'Lejátszás',
    url: 'URL',
    rssXspfOrM3u: 'rss, xspf, vagy m3u',
    save: 'Mentés',
    cancel: 'Mégse',
    edit: 'Szerkesztés',
    shufflePlaylist: 'Lista véletlenszerű lejátszása',
    fileIsNotAnMp3: 'A fájlok egyike nem MP3. Ennek ellenére megkísérli a feltöltést?',
    entryNotAUrl: 'A bejegyzések egyike nem tűnik URL-nek. Minden bejegyzésnek <kbd>http://</kbd>-vel kell kezdődnie.'
});


dojo.evalObjPath('xg.page.nls', true);
dojo.lang.mixin(xg.page.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'A karakterek száma (' + n + ') meghaladja a maximálisan megengedettet (' + maximum + ') '; },
    pleaseEnterContent: 'Kérjük, adja meg az oldal tartalmát',
    pleaseEnterTitle: 'Kérjük, adja meg az oldal címét',
    pleaseEnterAComment: 'Kérjük, írjon megjegyzést',
    deleteThisComment: 'Biztosan törölni szeretné ezt a megjegyzést?',
    save: 'Mentés',
    cancel: 'Mégse',
    edit: 'Szerkesztés',
    close: 'Bezárás',
    displayPagePosts: 'Az oldal hozzászólásainak megjelenítése',
    directory: 'Könyvtár',
    displayTab: 'Címke megjelenítése',
    addAnotherPage: 'Újabb oldal hozzáadása',
    tabText: 'Címke szövege',
    urlDirectory: 'URL könyvtár',
    displayTabForPage: 'Megjelenjen egy címke az oldalon',
    tabTitle: 'Címke címe',
    remove: 'Eltávolítás',
    thereIsAProblem: 'A megadott információkkal kapcsolatosan probléma merült fel'
});


dojo.evalObjPath('xg.photo.nls', true);
dojo.lang.mixin(xg.photo.nls, xg.index.i18n, {
    randomOrder: 'Véletlenszerű sorrend',
    untitled: 'Cím nélkül',
    photos: 'Fényképek',
    edit: 'Szerkesztés',
    photosFromAnAlbum: 'Albumok',
    show: 'Megjelenítés',
    rows: 'sorok',
    cancel: 'Mégse',
    save: 'Mentés',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'A karakterek száma (' + n + ') meghaladja a maximálisan megengedettet (' + maximum + ') '; },
    pleaseSelectPhotoToUpload: 'Kérjük, válassza ki a feltölteni kívánt fotót.',
    importingNofMPhotos: function(n,m) { return '<span id="currentP">' + n + '</span> of ' + m + ' fényképek importálása '},
    starting: 'Kezdés...',
    done: 'Kész!',
    from: 'Feladó',
    display: 'Megjelenít',
    takingYou: 'Továbbítás a fényképek megtekintéséhez...',
    anErrorOccurred: 'Sajnos hiba történt. Kérjük, jelentse be a hibát az oldal alján található link segítségével.',
    weCouldntFind: 'Nem találhatók a fényképek! Miért nem próbálja meg a többi opció valamelyikét?'
});


dojo.evalObjPath('xg.activity.nls', true);
dojo.lang.mixin(xg.activity.nls, xg.index.i18n, {
    edit: 'Szerkesztés',
    show: 'Megjelenítés',
    events: 'események',
    setWhatActivityGetsDisplayed: 'Állítsa be, hogy mely tevékenység kerüljön megjelenítésre',
    save: 'Mentés',
    cancel: 'Mégse'
});


dojo.evalObjPath('xg.profiles.nls', true);
dojo.lang.mixin(xg.profiles.nls, xg.index.i18n, {
    removeFriendTitle: function(username) {return 'Törölni szeretné ' + username + ' mint barátot?'; },
    removeFriendConfirm: function(username) {return 'Biztos benne, hogy törölni szeretné ' + username + ' mint barátot?' },
    pleaseEnterValueForPost: 'Kérjük, adjon meg egy értéket a hozzászóláshoz',
    edit: 'Szerkesztés',
    recentlyAdded: 'Mostanában hozzáadva',
    featured: 'Kiemelt',
    iHaveRecentlyAdded: 'Nemrég adtam hozzá',
    fromTheSite: 'A közösségi hálózatról',
    cancel: 'Mégse',
    save: 'Mentés',
    loading: 'Betöltés...',
    addAsFriend: 'Hozzáadás a barátokhoz',
    requestSent: 'Bejelölés elküldve!',
    sendingFriendRequest: 'Baráti bejelölés elküldése!',
    thisIsYou: 'Ez Ön!',
    isYourFriend: 'barátja',
    isBlocked: 'letiltott',
    pleaseEnterPostBody: 'Kérjük, adjon meg valamit a hozzászólás főszövegében',
    pleaseEnterChatter: 'Kérjük, adjon meg valamit a megjegyzésben',
    letMeApproveChatters: 'A megjegyzések az elküldés előtt jóváhagyásra kerüljenek?',
    noPostChattersImmediately: 'Nem – a megjegyzések azonnal kerüljenek elküldésre',
    yesApproveChattersFirst: 'Igen – a megjegyzéseket előbb jóvá kell hagyni',
    yourCommentMustBeApproved: 'Megjegyzését előbb jóvá kell hagyni, mielőtt az mindenki számára láthatóvá válik.',
    reallyDeleteThisPost: 'Tényleg törli ezt a hozzászólást?',
    commentWall: 'Üzenőfal',
    commentWallNComments: function(n) {
        switch(n) {
            case 1: return 'Üzenőfal (1 megjegyzés) ';
            default: return 'Üzenőfal (' + n + ' megjegyzés) ';
        }
    },
    display: 'Megjelenít',
    from: 'Feladó',
    show: 'Megjelenítés',
    rows: 'sorok',
    posts: 'hozzászólások',
    returnToDefaultWarning: 'Ezzel a Kezdőlapon található összes funkció és téma a hálózati alapbeállításra állítódik vissza. Szeretné folytatni?',
    networkError: 'Hálózati hiba',
    wereSorry: 'Sajnáljuk, de nem tudjuk elmenteni az új elrendezését. Ez valószínűleg az Internet-kapcsolat megszakadása miatt történt. Kérjük, ellenőrizze csatlakozását, és próbálja meg újra.',
    addFeature: 'Funkció hozzáadása',
    addFeatureConfirmation: function(linkAttributes) { return '<p>Ön új funkciót készül hozzáadni a Kezdőlaphoz. Ezt a funkciót egy harmadik fél fejlesztette ki.</p><p>A \'Funkció hozzáadására\' kattintva Ön elfogadja a Platform Alkalmazások <a ' + linkAttributes + '>Használati Feltételeit</a>.</p> '; }
});


dojo.evalObjPath('xg.shared.nls', true);
dojo.lang.mixin(xg.shared.nls, xg.index.i18n, {
    yourMessage: 'Az Ön üzenete',
    updateMessage: 'Üzenet frissítése',
    updateMessageQ: 'Frissíti az üzenetet?',
    removeWords: 'Az e-mail sikeres elküldése érdekében javasoljuk, hogy térjen vissza az üzenethez, és módosítsa a következő szavakat:',
    warningMessage: 'Úgy tűnik, hogy az e-mailben szereplő egyes szavak következtében az e-mail a Levélszemét mappába kerülhet.',
    errorMessage: '6 vagy több olyan szó van ebben az e-mailben, melyek következtében az e-mail a Levélszemét mappába kerülhet.',
    goBack: 'Vissza',
    sendAnyway: 'Ennek ellenére elküldöm',
    messageIsTooLong: function(n,m) { return 'Sajnáljuk. A maximálisan megengedett karakterszám '+m+'. ' },
    locationNotFound: function(location) { return '<em>' + location + '</em> nem található. '; },
    confirmation: 'Megerősítés',
    showMap: 'Térkép megjelenítése',
    hideMap: 'Térkép elrejtése',
    yourCommentMustBeApproved: 'Megjegyzését előbb jóvá kell hagyni, mielőtt az mindenki számára láthatóvá válik.',
    nComments: function(n) {
        switch(n) {
            case 1: return '1 Megjegyzés ';
            default: return n + 'Megjegyzések ';
        }
    },
    pleaseEnterAComment: 'Kérjük, írjon megjegyzést',
    uploadAPhoto: 'Fénykép feltöltése',
    uploadAnImage: 'Kép feltöltése',
    uploadAPhotoEllipsis: 'Kép feltöltése...',
    useExistingImage: 'Már létező kép használata:',
    existingImage: 'Létező kép',
    useThemeImage: 'Témakép használata:',
    themeImage: 'Témakép',
    noImage: 'Nincs kép',
    uploadImageFromComputer: 'Kép feltöltése a számítógépről',
    tileThisImage: 'Kép mozaikszerű alkalmazása',
    done: 'Kész',
    currentImage: 'Aktuális kép',
    pickAColor: 'Válasszon színt...',
    openColorPicker: 'Színválasztó megnyitása',
    transparent: 'Átlátszó',
    loading: 'Betöltés...',
    ok: 'OK',
    save: 'Mentés',
    cancel: 'Mégse',
    saving: 'Mentés...',
    addAnImage: 'Kép hozzáadása',
    uploadAFile: 'Fájl feltöltése',
    pleaseEnterAWebsite: 'Kérjük, adjon meg egy weboldal címet',
    pleaseEnterAFileAddress: 'Kérjük, adja meg a fájl címét',
    bold: 'Félkövér',
    italic: 'Dőlt',
    underline: 'Aláhúzott',
    strikethrough: 'Áthúzott',
    addHyperink: 'Hiperhivatkozás hozzáadása',
    options: 'Beállítások',
    wrapTextAroundImage: 'A szöveg vegye körül a képet?',
    imageOnLeft: 'Kép a bal oldalon?',
    imageOnRight: 'Kép a jobb oldalon?',
    createThumbnail: 'Bélyegkép nézetet létrehozása?',
    pixels: 'pixel',
    createSmallerVersion: 'A kép kisebb változatának létrehozása a megjelenítéshez. A szélességet pixelben adja meg.',
    popupWindow: 'Felbukkanó ablak?',
    linkToFullSize: 'Link a kép felbukkanó ablakban való teljes méretű megjelenítéséhez',
    add: 'Hozzáadás',
    keepWindowOpen: 'Kérjük, ne zárja be az ablakot addig, amíg a feltöltés folyamatban van.',
    cancelUpload: 'Feltöltés megszakítása',
    pleaseSelectAFile: 'Kérjük, válassza ki a képfájlt',
    pleaseSpecifyAThumbnailSize: 'Kérjük, határozza meg a bélyegkép nézet méretét',
    thumbnailSizeMustBeNumber: 'A bélyegkép nézet mérete csak szám lehet',
    addExistingImage: 'vagy szúrjon be egy létező képet',
    addExistingFile: 'vagy szúrjon be egy létező fájlt',
    clickToEdit: 'Kattintson a szerkesztéshez',
    sendingFriendRequest: 'Baráti bejelölés elküldése!',
    requestSent: 'Bejelölés elküldve!',
    pleaseCorrectErrors: 'Kérjük, javítsa ki ezeket a hibákat',
    noo: 'ÚJ',
    none: 'NINCS',
    joinNow: 'Csatlakozás most',
    join: 'Csatlakozás',
    addToFavorites: 'Hozzáadás a kedvencekhez',
    removeFromFavorites: 'Eltávolítás a kedvencekből',
    follow: 'Követés',
    stopFollowing: 'Követés megszüntetése',
    pendingPromptTitle: 'A tagság jóváhagyása folyamatban',
    youCanDoThis: 'Ezt akkor teheti meg, amikor tagsági kérelmét a rendszergazdák jóváhagyják.',
    editYourTags: 'Címkék szerkesztése',
    addTags: 'Címkék hozzáadása',
    editLocation: 'Hely szerkesztése',
    editTypes: 'Esemény típusának szerkesztése',
    charactersLeft: function(n) {
        if (n >= 0) {
            return '&nbsp;(' + n + ' left)' ;
        } else {
            return  '&nbsp;(' + Math.abs(n) + ' over)' ;
        }
    },
    commentWall: 'Üzenőfal',
    commentWallNComments: function(n) { switch(n) { case 1: return 'Üzenőfal (1 hozzászólás)'; default: return 'Üzenőfal (' + n + ' hozzászólás)'; } }
});


dojo.evalObjPath('xg.video.nls', true);
dojo.lang.mixin(xg.video.nls, xg.index.i18n, {
    edit: 'Szerkesztés',
    display: 'Megjelenít',
    detail: 'Részletek',
    player: 'Lejátszó',
    from: 'Feladó',
    show: 'Megjelenítés',
    videos: 'videók',
    cancel: 'Mégse',
    save: 'Mentés',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'A karakterek száma (' + n + ') meghaladja a maximálisan megengedettet (' + maximum + ') '; },
    approve: 'Jóváhagyás',
    approving: 'Jóváhagyás',
    keepWindowOpenWhileApproving: 'Kérjük, ne zárja be a böngészőablakot a videók jóváhagyása alatt. Ez a folyamat több percet vehet igénybe.',
    'delete': 'Törlés',
    deleting: 'Törlés...',
    keepWindowOpenWhileDeleting: 'Kérjük, ne zárja be a böngészőt addig, amíg a videó törlése folyamatban van. Ez a folyamat több percig is eltarthat.',
    pasteInEmbedCode: 'Kérjük, illessze be egy másik oldal beágyazási kódját.',
    pleaseSelectVideoToUpload: 'Kérjük, válassza ki a feltöltendő videót.',
    embedCodeContainsMoreThanOneVideo: 'A beágyazott kód egynél több videót tartalmaz. Kérjük, gondoskodjon arról, hogy csak egy <object> és/vagy  <embed> címkét tartalmazzon.',
    embedCodeMissingTag: 'Az beágyazási kódból hiányzik egy &lt;embed&gt; vagy &lt;object&gt; címke.',
    fileIsNotAMov: 'Úgy tűnik, hogy a fájl nem .mov, .mpg, .avi, .3gp vagy .wmv formátumú. Ennek ellenére megkísérli a feltöltést?',
    embedHTMLCode: 'HTML beágyazási kód:',
    directLink: 'Közvetlen link',
    addToMyspace: 'Hozzáadás a MySpace-hez',
    shareOnFacebook: 'Megosztás a Facebook-on',
    addToOthers: 'Hozzáadás az egyebekhez'
});


dojo.evalObjPath('xg.uploader.nls', true);
dojo.lang.mixin(xg.uploader.nls, xg.index.i18n, {
    fileBrowserHeader: 'Saját gép',
    fileRoot: 'Saját gép',
    fileInformationHeader: 'Információ',
    uploadHeader: 'Feltöltendő fájlok',
    dragOutInstructions: 'Húzza ki a fájlokat azok eltávolításához',
    dragInInstructions: 'Húzza ide a fájlokat',
    selectInstructions: 'Válasszon ki egy fájlt',
    files: 'Fájlok',
    totalSize: 'Teljes méret',
    fileName: 'Név',
    fileSize: 'Méret',
    nextButton: 'Tovább >',
    okayButton: 'OK',
    yesButton: 'Igen',
    noButton: 'Nem',
    uploadButton: 'Feltöltés',
    cancelButton: 'Mégse',
    backButton: 'Vissza',
    continueButton: 'Folytatás',
    uploadingStatus: function(n, m) { return ' ' + n + ' feltöltése ' + m; },
    uploadLimitWarning: function(n) { return 'Egyszerre ' + n + ' fájl tölthető fel. '; },
    uploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Ön már maximális számú fájlt adott hozzá. ';
            case 1: return 'Még 1 fájl feltöltésére van lehetőség. ';
            default: return 'Még ' + n + ' fájl feltöltésére van lehetőség. ';
        }
    },
    uploadingLabel: 'Feltöltés...',
    uploadingInstructions: 'Kérjük, ne zárja be ezt az ablakot, amíg a feltöltés folyamatban van',
    iHaveTheRight: 'A <a href="/main/authorization/termsOfService"> Használati Feltételek</a> értelmében jogosult vagyok feltölteni ezeket a fájlokat.',
    updateJavaTitle: 'Java frissítése',
    updateJavaDescription: 'A tömb-feltöltőnek a Java újabb verziójára van szüksége. Kattintson az "Okay"-ra a Java letöltéséhez.',
    batchEditorLabel: 'Az összes elem adatának szerkesztése',
    applyThisInfo: 'Alkalmazza ezeket az információkat az alábbi fájlokra',
    titleProperty: 'Cím',
    descriptionProperty: 'Leírás',
    tagsProperty: 'Címkék',
    viewableByProperty: 'Megtekintheti:',
    viewableByEveryone: 'Bárki',
    viewableByFriends: 'Csak a barátaim',
    viewableByMe: 'Csak én',
    albumProperty: 'Album',
    artistProperty: 'Előadó',
    enableDownloadLinkProperty: 'A letöltési link aktiválása',
    enableProfileUsageProperty: 'Engedélyezze másoknak, hogy ezt a dalt oldalukon elhelyezzék',
    licenseProperty: 'Engedély',
    creativeCommonsVersion: '3.0',
    selectLicense: '— Engedély kiválasztása —',
    copyright: '© Minden jog fenntartva',
    ccByX: function(n) { return 'Creative Commons Tulajdonjog ' + n; },
    ccBySaX: function(n) { return 'Creative Commons Tulajdonjog Megosztás ' + n; },
    ccByNdX: function(n) { return 'Creative Commons Tulajdonjog Származék Nélkül ' + n; },
    ccByNcX: function(n) { return 'Creative Commons Tulajdonjog Nem Kereskedelmi Jellegű ' + n; },
    ccByNcSaX: function(n) { return 'Creative Commons Tulajdonjog Nem Kereskedelmi Jellegű Megosztása ' + n; },
    ccByNcNdX: function(n) { return 'Creative Commons Tulajdonjog Nem Kereskedelmi Jellegű Származék Nélkül ' + n; },
    publicDomain: 'Köztulajdon',
    other: 'Egyéb',
    errorUnexpectedTitle: 'Hoppá!',
    errorUnexpectedDescription: 'Hiba történt. Kérjük, próbálja meg újra.',
    errorTooManyTitle: 'Túl sok elem.',
    errorTooManyDescription: function(n) { return 'Sajnáljuk, de legfeljebb ' + n + ' elem tölthető fel egyszerre. '; },
    errorNotAMemberTitle: 'Nem megengedett',
    errorNotAMemberDescription: 'Sajnáljuk, de a feltöltéshez tagnak kell lennie.',
    errorContentTypeNotAllowedTitle: 'Nem megengedett',
    errorContentTypeNotAllowedDescription: 'Sajnáljuk, de az ilyen jellegű tartalmak feltöltése nem megengedett.',
    errorUnsupportedFormatTitle: 'Hoppá!',
    errorUnsupportedFormatDescription: 'Sajnáljuk, de az ilyen típusú fájlok nem támogatottak.',
    errorUnsupportedFileTitle: 'Hoppá!',
    errorUnsupportedFileDescription: 'A foo.exe nem támogatott formátum.',
    errorUploadUnexpectedTitle: 'Hoppá!',
    errorUploadUnexpectedDescription: function(file) {
        return file ?
            ('Úgy tűnik, probléma van a ' + file + ' fájllal. Kérjük, törölje a listáról mielőtt a többi fájlot feltöltené.') :
            'Úgy tűnik, probléma van a lista tetején lévő fájllal. Kérjük, törölje mielőtt a többi fájlot feltöltené. ';
    },
    cancelUploadTitle: 'Megszakítja a feltöltést?',
    cancelUploadDescription: 'Biztosan meg kívánja szakítani a még hátralévő feltöltéseket?',
    uploadSuccessfulTitle: 'A feltöltés kész',
    uploadSuccessfulDescription: 'Kérjük, várjon, amíg a feltöltésekhez továbbítjuk...',
    uploadPendingDescription: 'A fájlok feltöltése sikeresen lezáródott, most jóváhagyásra várnak.',
    photosUploadHeader: 'Feltöltendő fényképek',
    photosDragOutInstructions: 'Húzza ki a fényképeket azok eltávolításához',
    photosDragInInstructions: 'Húzza ide a fényképeket',
    photosSelectInstructions: 'Válasszon egy fényképet',
    photosFiles: 'Fényképek',
    photosUploadingStatus: function(n, m) { return ' ' + n + ' fénykép feltöltése ' + m; },
    photosErrorTooManyTitle: 'Túl sok fénykép',
    photosErrorTooManyDescription: function(n) { return 'Sajnáljuk, de egyszerre csak ' + n + ' fénykép feltöltésére van lehetőség. '; },
    photosErrorContentTypeNotAllowedDescription: 'Sajnáljuk, de a fényképek feltöltése letiltásra került.',
    photosErrorUnsupportedFormatDescription: 'Sajnáljuk, de csak .jpg, .gif vagy .png formátumú képek tölthetők fel.',
    photosErrorUnsupportedFileDescription: function(n) { return n + 'nem .jpg, .gif vagy .png fájl. '; },
    photosBatchEditorLabel: 'Az összes fénykép adatának szerkesztése',
    photosApplyThisInfo: 'Ez az információ vonatkozzon az alábbi képekre',
    photosErrorUploadUnexpectedDescription: function(file) {
        return file ?
            ('Úgy tűnik, probléma van a ' + file + ' fájllal. Kérjük, törölje a listáról mielőtt a többi képet feltöltené.' ) :
            'Úgy tűnik, probléma van a lista tetején lévő képpel. Kérjük, törölje mielőtt a többi képet feltöltené. ';
    },
    photosUploadSuccessfulDescription: 'Kérjük, várjon, amíg a fényképekhez továbbítjuk...',
    photosUploadPendingDescription: 'Fényképei sikeresen feltöltésre kerültek, és jóváhagyásra várnak.',
    photosUploadLimitWarning: function(n) { return 'Egyszerre csak ' + n + ' fénykép feltöltésére van lehetőség. '; },
    photosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Ön már hozzáadta a maximálisan megengedett számú fényképet. ';
            case 1: return 'Még 1 fénykép feltöltésére van lehetőség. ';
            default: return 'Még ' + n + ' fénykép feltöltésére van lehetőség. ';
        }
    },
    photosIHaveTheRight: 'A <a href="/main/authorization/termsOfService"> Használati Feltételek</a> értelmében jogosult vagyok ezeknek a fényképeknek a feltöltésére.',
    videosUploadHeader: 'Feltöltendő videók',
    videosDragInInstructions: 'Húzza ide a videókat',
    videosDragOutInstructions: 'Húzza ki a videókat azok eltávolításához',
    videosSelectInstructions: 'Válasszon egy videót',
    videosFiles: 'Videók',
    videosUploadingStatus: function(n, m) { return ' ' + n + ' videó feltöltése ' + m; },
    videosErrorTooManyTitle: 'Túl sok videó',
    videosErrorTooManyDescription: function(n) { return 'Sajnáljuk, de egyszerre csak ' + n + ' videó feltöltésére van lehetőség. '; },
    videosErrorContentTypeNotAllowedDescription: 'Sajnáljuk, de a videó feltöltést letiltották.',
    videosErrorUnsupportedFormatDescription: 'Sajnáljuk, de csak .avi, .mov, .mp4 vagy .mpg formátumú videók feltöltésére van lehetőség.',
    videosErrorUnsupportedFileDescription: function(x) { return x + 'nem .avi, .mov, .mp4, .wmv vagy .mpg fájl. '; },
    videosBatchEditorLabel: 'Az összes videó adatának szerkesztése',
    videosApplyThisInfo: 'Ez az információ vonatkozzon az alábbi videókra',
    videosErrorUploadUnexpectedDescription: function(file) {
        return file ?
            ('Úgy tűnik, probléma van a ' + file + ' fájllal. Kérjük, törölje a listáról mielőtt a többi videót feltöltené.' ) :
            'Úgy tűnik, probléma van a lista tetején lévő videóval. Kérjük, törölje mielőtt a többi videót feltöltené. ';
    },
    videosUploadSuccessfulDescription: 'Kérjük, várjon, amíg a videóihoz továbbítjuk...',
    videosUploadPendingDescription: 'A videók sikeresen feltöltődtek, és jóváhagyásra várnak.',
    videosUploadLimitWarning: function(n) { return 'Egyszerre ' + n + ' videó feltöltésére van lehetőség. '; },
    videosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Ön már hozzáadta a maximális számú videót. ';
            case 1: return 'Még 1 videó feltöltésére van lehetőség. ';
            default: return 'Még ' + n + ' videó feltöltésére van lehetőség. ';
        }
    },
    videosIHaveTheRight: 'A <a href="/main/authorization/termsOfService"> Használati Feltételek</a> értelmében jogosult vagyok ezeknek a videóknak a feltöltésére',
    musicUploadHeader: 'Feltöltendő dalok',
    musicTitleProperty: 'Dalcím',
    musicDragOutInstructions: 'Húzza ki a dalokat azok eltávolításához',
    musicDragInInstructions: 'Húzza ide a dalokat',
    musicSelectInstructions: 'Válasszon egy dalt',
    musicFiles: 'Dalok',
    musicUploadingStatus: function(n, m) { return ' ' + n + ' dal feltöltése ' + m; },
    musicErrorTooManyTitle: 'Túl sok dal',
    musicErrorTooManyDescription: function(n) { return 'Sajnáljuk, de legfeljebb ' + n + ' dal egyidejű feltöltésére van lehetőség. '; },
    musicErrorContentTypeNotAllowedDescription: 'Sajnáljuk, de a dalok feltöltését letiltották.',
    musicErrorUnsupportedFormatDescription: 'Sajnáljuk, de csak .mp3 formátumú dalok feltöltésére van lehetőség.',
    musicErrorUnsupportedFileDescription: function(x) { return x + 'nem .mp3 fájl. '; },
    musicBatchEditorLabel: 'Az összes dal adatának szerkesztése',
    musicApplyThisInfo: 'Ez az információ vonatkozzon az alábbi dalokra.',
    musicErrorUploadUnexpectedDescription: function(file) {
        return file ?
            ('Úgy tűnik, probléma van a ' + file + ' fájllal. Kérjük, törölje a listáról mielőtt a többi dalt feltöltené.' ) :
            'Úgy tűnik, probléma van a lista tetején lévő dallal. Kérjük, törölje mielőtt a többi dalt feltöltené. ';
    },
    musicUploadSuccessfulDescription: 'Kérjük, várjon, amíg a dalaihoz továbbítjuk...',
    musicUploadPendingDescription: 'Dalai sikeresen feltöltődtek, és jóváhagyásra várnak.',
    musicUploadLimitWarning: function(n) { return 'Egyszerre ' + n + '  dal feltöltésére van lehetőség. '; },
    musicUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Ön már hozzáadta a maximális számú dalt. ';
            case 1: return 'Még 1 dal feltöltésére van lehetősége. ';
            default: return 'Még ' + n + '  dal feltöltésére van lehetősége. ';
        }
    },
    musicIHaveTheRight: 'A <a href="/main/authorization/termsOfService"> Használati Feltételek</a> értelmében jogosult vagyok ezeknek a daloknak a feltöltésére'
});


dojo.evalObjPath('xg.events.nls', true);
dojo.lang.mixin(xg.events.nls, xg.index.i18n, {
    pleaseEnterTitle: 'Kérjük, adja meg az esemény címét',
    pleaseEnterDescription: 'Kérjük, adja meg az esemény leírását',
    messageIsTooLong: function(n) { return 'Az üzenet túl hosszú. Kérjük, legfeljebb '+n+' karaktert használjon. '; },
    pleaseEnterLocation: 'Kérjük, adja meg az esemény helyszínét',
    pleaseChooseImage: 'Kérjük, válasszon egy képet az eseményhez',
    pleaseEnterType: 'Kérjük, legalább egy típust rendeljen hozzá az eseményhez',
    sendMessageToGuests: 'Üzenet küldése a vendégeknek',
    sendMessageToGuestsThat: 'Üzenet küldése azoknak a vendégeknek, akik:',
    areAttending: 'Részt vesznek',
    mightAttend: 'Esetleg részt vesznek',
    haveNotYetRsvped: 'Akik még nem RSVP-ztek',
    areNotAttending: 'Akik nem vesznek részt',
    yourMessage: 'Az Ön üzenete',
    send: 'Küldés',
    sending: 'Küldés...',
    yourMessageIsBeingSent: 'Az üzenet elküldése folyamatban van.',
    messageSent: 'Üzenet elküldve!',
    yourMessageHasBeenSent: 'Az üzenete elküldésre került.',
    chooseRecipient: 'Kérjük, válasszon címzettet.',
    pleaseEnterAMessage: 'Kérjük, írja be az üzenetet',
    thereHasBeenAnError: 'Hiba történt'
});


dojo.evalObjPath('xg.notes.nls', true);
dojo.lang.mixin(xg.notes.nls, xg.index.i18n, {
    addNewNote: 'Új megjegyzés hozzáadása',
    pleaseEnterNoteTitle: 'Kérjük, adja meg a megjegyzés címét!',
    noteTitleTooLong: 'A megjegyzés címe túl hosszú',
    pleaseEnterNoteEntry: 'Kérjük, adja meg a megjegyzés szövegét'
});