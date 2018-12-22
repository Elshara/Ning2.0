dojo.provide('xg.shared.messagecatalogs.cs_CZ');

dojo.require('xg.index.i18n');

/**
 * Texts for the cs_CZ
 */
// Use UTF-8 byte
dojo.evalObjPath('xg.feed.nls', true);
dojo.lang.mixin(xg.feed.nls, xg.index.i18n, {
    edit: 'Upravit',
    title: 'Název:',
    feedUrl: 'Adresa URL:',
    show: 'Zobrazit:',
    titles: 'Pouze názvy',
    titlesAndDescriptions: 'Detailní pohled',
    display: 'Zobrazit',
    cancel: 'Storno',
    save: 'Uložit',
    loading: 'Načítání…',
    items: 'položek'
});


dojo.evalObjPath('xg.gadgets.nls', true);
dojo.lang.mixin(xg.gadgets.nls, xg.index.i18n, {
    edit: 'Upravit',
    title: 'Název:',
    feedUrl: 'Adresa URL:',
    cancel: 'Storno',
    save: 'Uložit',
    loading: 'Načítání…',
    removeGadget: 'Odebrat miniaplikaci',
    findGadgetsInDirectory: 'Vyhledat miniaplikace v adresáři miniaplikací'
});


dojo.evalObjPath('xg.forum.nls', true);
dojo.lang.mixin(xg.forum.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Počet znaků (' + n + ') převyšuje maximum (' + maximum + ') '; },
    pleaseEnterFirstPost: 'Napište první příspěvek do diskuse',
    pleaseEnterTitle: 'Zadejte název diskuse',
    save: 'Uložit',
    cancel: 'Storno',
    yes: 'Ano',
    no: 'Ne',
    edit: 'Upravit',
    deleteCategory: 'Odstranit kategorii',
    discussionsWillBeDeleted: 'Diskuse v této kategorii budou odstraněny.',
    whatDoWithDiscussions: 'Co chcete provést s diskusemi v této kategorii?',
    moveDiscussionsTo: 'Přesunout diskuse do:',
    moveToCategory: 'Přesunout do kategorie…',
    deleteDiscussions: 'Odstranit diskuse',
    'delete': 'Odstranit',
    deleteReply: 'Odstranit odpověď',
    deleteReplyQ: 'Odstranit tuto odpověď?',
    deletingReplies: 'Odstraňování odpovědí…',
    doYouWantToRemoveReplies: 'Chcete také odebrat odpovědi na tento komentář?',
    pleaseKeepWindowOpen: 'Probíhá zpracování - nechte toto okno prohlížeče otevřené.  Operace může nějakou dobu trvat.',
    from: 'Od:',
    show: 'Zobrazit',
    discussions: 'diskuse',
    discussionsFromACategory: 'Diskuse z kategorie…',
    display: 'Zobrazit',
    items: 'položky',
    view: 'Prohlédnout'
});


dojo.evalObjPath('xg.groups.nls', true);
dojo.lang.mixin(xg.groups.nls, xg.index.i18n, {
    pleaseChooseAName: 'Zvolte název skupiny.',
    pleaseChooseAUrl: 'Zvolte webovou adresu skupiny.',
    urlCanContainOnlyLetters: 'Webová adresa smí obsahovat pouze písmena a čísla (ne mezery).',
    descriptionTooLong: function(n, maximum) { return 'Počet znaků (' + n + ') převyšuje limit (' + maximum + ') '; },
    nameTaken: 'Omlouváme se - toto jméno už je obsazeno.  Zvolte jiný název.',
    urlTaken: 'Omlouváme se - tato adresa už je obsazena.  Zvolte jinou adresu.',
    whyNot: 'Proč ne?',
    groupCreatorDetermines: function(href) { return 'Zakladatel skupiny určuje, kdo smí být členem a kdo ne.  Pokud máte dojem, že jste byli zablokováni omylem, <a ' + href + '>kontaktujte zakladatele skupiny</a> '; },
    edit: 'Upravit',
    from: 'Od:',
    show: 'Zobrazit',
    groups: 'skupiny',
    pleaseEnterName: 'Zadejte prosím jméno.',
    pleaseEnterEmailAddress: 'Zadejte emailovou adresu.',
    xIsNotValidEmailAddress: function(x) { return x + ' is not a valid email address'; },
    save: 'Uložit',
    cancel: 'Storno'
});


dojo.evalObjPath('xg.html.nls', true);
dojo.lang.mixin(xg.html.nls, xg.index.i18n, {
    contentsTooLong: function(maximum) { return 'Text je příliš dlouhý. Použijte méně než ' + maximum + ' znaků. '; },
    edit: 'Upravit',
    save: 'Uložit',
    cancel: 'Storno',
    saving: 'Ukládání…',
    addAWidget: function(url) { return '<a href="' + url + '">Přidat miniaplikaci</a> do tohoto textového pole '; }
});


dojo.evalObjPath('xg.index.nls', true);
dojo.lang.mixin(xg.index.nls, xg.index.i18n, {
    showingNFriends: function(n, searchString) {
        switch(n) {
            case 1: return 'Zobrazen 1 přítel odpovídající zadání "' + searchString + '". <a href="#">Zobrazit všechny</a> ';
            default: return 'Zobrazeni přátelé (' + n + ') odpovídající zadání "' + searchString + '". <a href="#">Zobrazit všechny</a> ';
        }
    },
    sendInvitation: 'Poslat pozvánku',
    sendMessage: 'Odeslat zprávu',
    sendInvitationToNFriends: function(n) {
        switch(n) {
            case 1: return 'Poslat pozvánku 1 příteli? ';
            default: return 'Poslat pozvánku ' + n + ' přátelům? ';
        }
    },
    sendMessageToNFriends: function(n) {
        switch(n) {
            case 1: return 'Poslat zprávu 1 příteli? ';
            default: return 'Poslat zprávu ' + n + ' přátelům? ';
        }
    },
    invitingNFriends: function(n) {
        switch(n) {
            case 1: return 'Zasílání pozvánky 1 příteli… ';
            default: return 'Zasílání pozvánky ' + n + ' přátelům… ';
        }
    },
    nFriends: function(n) {
        switch(n) {
            case 1: return '1 přítel… ';
            default: return n + ' přátel(é)… ';
        }
    },
    sendingMessageToNFriends: function(n) {
        switch(n) {
            case 1: return 'Zasílání zprávy 1 příteli… ';
            default: return 'Zasílání zprávy ' + n + ' přátelům… ';
        }
    },
    yourMessageOptional: '<label>Vaše zpráva</label> (volitelné)',
    pleaseChoosePeople: 'Vyberte pozvané osoby.',
    noPeopleSelected: 'Žádné osoby nejsou vybrány',
    pleaseEnterEmailAddress: 'Zadejte svou emailovou adresu.',
    pleaseEnterPassword: function(emailAddress) { return 'Zadejte heslo pro ' + emailAddress + '. '; },
    sorryWeDoNotSupport: 'Bohužel, server nepodporuje vyhledání emailových adres z webového adresáře. Klepněte na volbu "Prohledat adresář" a využijte adresy z počítače.',
    atSymbolNotAllowed: 'Zkontrolujte, zda symbol @ není v první části adresy.',
    resetTextQ: 'Smazat text?',
    resetTextToOriginalVersion: 'Opravdu chcete smazat veškerý zadaný text a obnovit původní verzi?  Všechny vaše změny budou ztraceny.',
    changeQuestionsToPublic: 'Změnit otázky na veřejné?',
    changingPrivateQuestionsToPublic: 'Změna statusu soukromých otázek na veřejné zveřejní odpovědi všech členů.  Jste si jisti?',
    youHaveUnsavedChanges: 'Udělali jste neuložené změny.',
    pleaseEnterASiteName: 'Zadejte název komunity, např. Klub veteránů.',
    pleaseEnterShorterSiteName: 'Zadejte kratší název (max. 64 znaků)',
    pleaseEnterShorterSiteDescription: 'Zadejte kratší popis (max. 140 znaků)',
    siteNameHasInvalidCharacters: 'Název obsahuje neplatné znaky',
    thereIsAProblem: 'Zadané informace jsou problematické.',
    thisSiteIsOnline: 'Tato komunita je online!',
    onlineSiteCanBeViewed: '<strong>Online</strong> - komunitu lze na webu prohlížet (podle nastavené úrovně soukromí).',
    takeOffline: 'Převést do režimu offline',
    thisSiteIsOffline: 'Tato komunita je offline!',
    offlineOnlyYouCanView: '<strong>Offline</strong> - tuto komunitu uvidíte pouze vy.',
    takeOnline: 'Převést do režimu online',
    themeSettings: 'Nastavení motivu',
    addYourOwnCss: 'Rozšířené volby',
    error: 'Chyba',
    pleaseEnterTitleForFeature: function(displayName) { return 'Zadejte vlastní název funkce ' + displayName + ' '; },
    thereIsAProblemWithTheInformation: 'Zadané informace jsou problematické.',
    photos: 'Snímky',
    videos: 'Videoklipy',
    pleaseEnterTheChoicesFor: function(questionTitle) { return 'Zadejte možnosti, např. Turistika, Čtení, Nakupování '; },
    pleaseEnterTheChoices: 'Zadejte možnosti, např. Turistika, Čtení, Nakupování',
    shareWithFriends: 'Sdílet s přáteli',
    email: 'email',
    separateMultipleAddresses: 'Zadáváte-li více adres, oddělte je čárkami',
    subject: 'Předmět',
    message: 'Zpráva',
    send: 'Odeslat',
    cancel: 'Storno',
    pleaseEnterAValidEmail: 'Zadejte platnou emailovou adresu.',
    go: 'Spustit',
    areYouSureYouWant: 'Opravdu chcete operaci provést?',
    processing: 'Zpracování…',
    pleaseKeepWindowOpen: 'Probíhá zpracování - nechte toto okno prohlížeče otevřené.  Operace může nějakou dobu trvat.',
    complete: 'Hotovo!',
    processIsComplete: 'Proces je dokončen.',
    ok: 'OK',
    body: 'Text zprávy:',
    pleaseEnterASubject: 'Zadejte předmět.',
    pleaseEnterAMessage: 'Napište zprávu.',
    thereHasBeenAnError: 'Došlo k chybě',
    fileNotFound: 'Soubor nebyl nalezen',
    pleaseProvideADescription: 'Zadejte popis',
    pleaseEnterYourFriendsAddresses: 'Zadejte adresy přátel nebo jejich uživatelská jména (Ning ID).',
    pleaseEnterSomeFeedback: 'Napište svůj názor',
    title: 'Název:',
    setAsMainSiteFeature: 'Nastavit jako hlavní funkci',
    thisIsTheMainSiteFeature: 'Tato funkce je hlavní.',
    customized: 'Upraveno',
    copyHtmlCode: 'Kopírovat HTML kód',
    playerSize: 'Velikost přehrávače',
    selectSource: 'Vyberte zdroj',
    myAlbums: 'Moje alba',
    myMusic: 'Moje hudba',
    myVideos: 'Moje videoklipy',
    showPlaylist: 'Zobrazit playlist',
    change: 'Změnit',
    changing: 'Provádění změny...',
    changePrivacy: 'Změnit úroveň soukromí?',
    keepWindowOpenWhileChanging: 'Probíhá změna nastavení soukromí - nechte toto okno prohlížeče otevřené.  Operace může trvat několik minut.',
    htmlNotAllowed: 'Kód HTML není povolen',
    pleaseChooseFriends: 'Před zasláním zprávy vyberte adresáty.',
    noFriendsFound: 'Žádní přátelé odpovídající zadaným parametrům nebyli nalezeni.',
    pleaseSelectSecondPart: 'Vyberte doménovou část své adresy, např. gmail. com.',
    subjectIsTooLong: function(n) { return 'Předmět zprávy je příliš dlouhý. Použijte pouze '+n+' nebo méně znaků.'; },
    addingInstructions: 'Přidávání materiálu - než proces skončí, nechte toto okno otevřené.',
    addingLabel: 'Přidávání...',
    cannotKeepFiles: 'Pokud si chcete prohlédnout více možností, musíte soubory vybrat znovu.  Chcete pokračovat?',
    done: 'Hotovo',
    looksLikeNotImage: 'Minimálně jeden soubor zřejmě není ve formátu jpg, gif, nebo png.  Chcete se o upload přesto pokusit?',
    looksLikeNotMusic: 'Vybraný soubor zřejmě není ve formátu mp3.  Chcete se o upload přesto pokusit?',
    looksLikeNotVideo: 'Vybraný soubor zřejmě není ve formátu mov, mpg, mp4, avi, 3gp nebo wmv.  Chcete se o upload přesto pokusit?',
    messageIsTooLong: function(n) { return 'Vaše zpráva je příliš dlouhá. Použijte pouze '+n+' nebo méně znaků.'; },
    pleaseSelectPhotoToUpload: 'Vyberte fotografii k uploadu.',
    processingFailed: 'Zpracování se nezdařilo. Zkuste to prosím znovu později.',
    selectOrPaste: 'Musíte vybrat videoklip nebo zkopírovat vložený kód.',
    selectOrPasteMusic: 'Musíte vybrat skladbu nebo vložit zkopírovanou webovou adresu.',
    sendingLabel: 'Odesílání...',
    thereWasAProblem: 'Při přidávání materiálu došlo k problému.  Zkuste to prosím znovu později.',
    uploadingInstructions: 'Probíhá upload - než skončí, nechte toto okno otevřené',
    uploadingLabel: 'Probíhá upload...',
    youNeedToAddEmailRecipient: 'Musíte zadat příjemce emailu.',
    yourMessage: 'Vaše zpráva',
    yourMessageIsBeingSent: 'Vaše zpráve je odesílána.',
    yourSubject: 'Váš předmět'
});


dojo.evalObjPath('xg.music.nls', true);
dojo.lang.mixin(xg.music.nls, xg.index.i18n, {
    play: 'přehrát',
    pleaseSelectTrackToUpload: 'Vyberte skladbu k uploadu.',
    pleaseEnterTrackLink: 'Zadejte URL skladby.',
    thereAreUnsavedChanges: 'Udělali jste neuložené změny.',
    autoplay: 'Automatické přehrávání',
    showPlaylist: 'Zobrazit playlist',
    playLabel: 'Přehrát',
    url: 'URL',
    rssXspfOrM3u: 'rss, xspf nebo m3u',
    save: 'Uložit',
    cancel: 'Storno',
    edit: 'Upravit',
    shufflePlaylist: 'Zamíchat playlist',
    fileIsNotAnMp3: 'Jeden ze souborů zřejmě není ve formátu MP3.  Chcete se přesto pokusit o jeho upload?',
    entryNotAUrl: 'Jedna z položek zřejmě není URL.  Zkontrolujte, zda veškeré URL začínají výrazem <kbd>http://</kbd>'
});


dojo.evalObjPath('xg.page.nls', true);
dojo.lang.mixin(xg.page.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Počet znaků (' + n + ') převyšuje maximum (' + maximum + ') '; },
    pleaseEnterContent: 'Zadejte obsah stránky',
    pleaseEnterTitle: 'Zadejte název stránky',
    pleaseEnterAComment: 'Zadejte komentář',
    deleteThisComment: 'Opravdu chcete tento komentář odstranit?',
    save: 'Uložit',
    cancel: 'Storno',
    discussionTitle: 'Název stránky:',
    tags: 'Popisky:',
    edit: 'Upravit',
    close: 'Zavřít',
    displayPagePosts: 'Zobrazit příspěvky na stránce',
    directory: 'Adresář',
    urlDirectory: 'URL adresář',
    remove: 'Odstranit',
    displayTab: 'Zobrazit kartu',
    tabText: 'Text karty',
    displayTabForPage: 'Zda zobrazit kartu pro stránku',
    tabTitle: 'Název karty',
    addAnotherPage: 'Přidat další stranu',
    thereIsAProblem: 'Existuje problém s vašimi informacemi'
});


dojo.evalObjPath('xg.photo.nls', true);
dojo.lang.mixin(xg.photo.nls, xg.index.i18n, {
    untitled: 'Bez názvu',
    randomOrder: 'Náhodné pořadí',
    photos: 'Snímky',
    edit: 'Upravit',
    photosFromAnAlbum: 'Alba',
    show: 'Zobrazit',
    rows: 'řádky',
    cancel: 'Storno',
    save: 'Uložit',
    deleteThisPhoto: 'Odstranit tento snímek?',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Počet znaků (' + n + ') převyšuje maximum (' + maximum + ') '; },
    weCouldNotLookUpAddress: function(address) { return 'Adresu „' + address + '" se bohužel nepodařilo najít. '; },
    pleaseSelectPhotoToUpload: 'Vyberte fotografii k uploadu.',
    pleaseEnterAComment: 'Zadejte komentář',
    addToExistingAlbum: 'Přidat do stávajícího alba',
    addToNewAlbumTitled: 'Přidat do nového alba s názvem…',
    deleteThisComment: 'Odstranit tento komentář?',
    importingNofMPhotos: function(n,m) { return 'Importování <span id="currentP">' + n + '</span> z ' + m + ' snímků. '},
    starting: 'Spouštění…',
    done: 'Hotovo.',
    from: 'Od:',
    display: 'Zobrazit',
    takingYou: 'Otevírání vašich snímků…',
    anErrorOccurred: 'Bohužel došlo k chybě.  Nahlašte tento problém pomocí odkazu dole na stránce.',
    weCouldntFind: 'Žádné snímky nebyly nalezeny!  Co takhle zkusit jinou z možností?'
});


dojo.evalObjPath('xg.activity.nls', true);
dojo.lang.mixin(xg.activity.nls, xg.index.i18n, {
    edit: 'Upravit',
    show: 'Zobrazit',
    events: 'události',
    setWhatActivityGetsDisplayed: 'Nastavit zobrazované aktivity',
    save: 'Uložit',
    cancel: 'Storno'
});


dojo.evalObjPath('xg.profiles.nls', true);
dojo.lang.mixin(xg.profiles.nls, xg.index.i18n, {
    pleaseEnterValueForPost: 'Zadejte prosím hodnocení příspěvku',
    pleaseProvideAValidDate: 'Zadejte platné datum',
    uploadAFile: 'Uploadovat soubor',
    pleaseEnterAFileAddress: 'Zadejte adresu souboru',
    addExistingFile: 'nebo vložte stávající soubor.',
    pleaseEnterUrlOfLink: 'Zadejte URL odkazu:',
    pleaseEnterTextOfLink: 'Jaký text má sloužit jako odkaz?',
    edit: 'Upravit',
    recentlyAdded: 'Přidáno nedávno',
    featured: 'Zajímavé',
    iHaveRecentlyAdded: 'Přidal(a) jsem nedávno',
    fromTheSite: 'Z naší komunity',
    cancel: 'Storno',
    save: 'Uložit',
    loading: 'Načítání…',
    addAsFriend: 'Přidat jako přítele',
    removeAsFriend: 'Odebrat ze skupiny přátel',
    requestSent: 'Žádost odeslána!',
    sendingFriendRequest: 'Odesílání žádosti o zařazení mezi přátele',
    thisIsYou: 'To jste vy!',
    isYourFriend: 'je váš přítel',
    isBlocked: 'je blokován(a)',
    pleaseEnterAComment: 'Zadejte komentář',
    pleaseEnterPostBody: 'Napište obsah příspěvku',
    pleaseSelectAFile: 'Vyberte soubor',
    pleaseEnterChatter: 'Zadejte komentář',
    toggleBetweenHTML: 'Zobrazit/skrýt HTML kód',
    attachAFile: 'Připojit soubor',
    addAPhoto: 'Přidat snímek',
    insertALink: 'Vložit odkaz',
    changeTextSize: 'Změnit velikost textu',
    makeABulletedList: 'Vytvořit seznam s odrážkami',
    makeANumberedList: 'Vytvořit číslovaný seznam',
    crossOutText: 'Přeškrtnout text',
    underlineText: 'Podtrhnout text',
    italicizeText: 'Napsat kurzívou',
    boldText: 'Napsat tučně',
    letMeApproveChatters: 'Budu komentáře před zveřejněním schvalovat?',
    noPostChattersImmediately: 'Ne - komentáře budou zveřejněny okamžitě',
    yesApproveChattersFirst: 'Ano - komentáře musí být nejprve schváleny',
    yourCommentMustBeApproved: 'Váš komentář musí být před zveřejněním schválen.',
    reallyDeleteThisPost: 'Opravdu chcete tento příspěvek odstranit?',
    commentWall: 'Nástěnka',
    commentWallNComments: function(n) {
        switch(n) {
            case 1: return 'Nástěnka (1 komentář) ';
            default: return 'Nástěnka (' + n + ' komentářů) ';
        }
    },
    display: 'Zobrazit',
    from: 'Od:',
    show: 'Zobrazit',
    rows: 'řádky',
    posts: 'příspěvky',
    wereSorry: 'Litujeme, ale vaše rozvržení momentálně nelze uložit. Pravděpodobně bylo přerušeno připojení k internetu. Zkontrolujte, zda jste připojeni a zkuste znovu.',
    networkError: 'Chyba sítě',
    returnToDefaultWarning: 'Tímto se všechny funkce a motiv položky Moje stránka vrátí na výchozí nastavení komunity. Chcete pokračovat?'
});


dojo.evalObjPath('xg.shared.nls', true);
dojo.lang.mixin(xg.shared.nls, xg.index.i18n, {
    removeFriendTitle: 'Odebrat ze skupiny přátel?',
    removeAsFriend: 'Odebrat ze skupiny přátel',
    removeFriendConfirm: 'Opravdu chcete tuto osobu odebrat ze skupiny přátel?',
    locationNotFound: function(location) { return '<em>' + location + '</em> nebylo nalezeno. '; },
    confirmation: 'Potvrzení',
    showMap: 'Zobrazit mapu',
    hideMap: 'Skrýt mapu',
    yourCommentMustBeApproved: 'Váš komentář musí být před zveřejněním schválen.',
    nComments: function(n) {
        switch(n) {
            case 1: return '1 komentář ';
            default: return n + ' Komentáře ';
        }
    },
    uploadAPhoto: 'Uploadovat snímek',
    uploadAnImage: 'Uploadovat obrázek',
    uploadAPhotoEllipsis: 'Uploadovat snímek…',
    useExistingImage: 'Použít stávající obrázek:',
    existingImage: 'Stávající obrázek',
    useThemeImage: 'Použít obrázek z motivu:',
    themeImage: 'Obrázek z motivu',
    noImage: 'Bez obrázku',
    uploadImageFromComputer: 'Uploadovat obrázek z počítače',
    tileThisImage: 'Použít obrázek jako dlaždice',
    done: 'Hotovo',
    currentImage: 'Aktuální obrázek',
    pickAColor: 'Zvolit barvu…',
    openColorPicker: 'Otevřít vzorník barev',
    loading: 'Načítání…',
    ok: 'OK',
    save: 'Uložit',
    cancel: 'Storno',
    saving: 'Ukládání…',
    addAnImage: 'Přidat obrázek',
    uploadAFile: 'Uploadujte soubor',
    bold: 'Tučné',
    italic: 'Kurzíva',
    underline: 'Podtržení',
    strikethrough: 'Přeškrtnuté',
    addHyperink: 'Přidat hypertextový odkaz',
    options: 'Možnosti',
    wrapTextAroundImage: 'Obtékání textu kolem obrázku?',
    imageOnLeft: 'Obrázek vlevo?',
    imageOnRight: 'Obrázek vpravo?',
    createThumbnail: 'Vytvořit náhled?',
    pixels: 'pixelů',
    createSmallerVersion: 'Vytvořte menší verzi obrázku k zobrazení.  Nastavte šířku v pixelech.',
    popupWindow: 'Automaticky otevírané okno?',
    linkToFullSize: 'Odkaz na obrázek v plné velikosti v automaticky otevíraném okně.',
    add: 'Přidat',
    keepWindowOpen: 'Probíhá upload - nechte toto okno prohlížeče otevřené.',
    cancelUpload: 'Zrušit upload',
    pleaseSelectAFile: 'Vyberte obrázek',
    pleaseSpecifyAThumbnailSize: 'Zadejte velikost náhledu',
    thumbnailSizeMustBeNumber: 'Velikost náhledu musí být číslo',
    addExistingImage: 'nebo vložte stávající obrázek',
    clickToEdit: 'Klepněte sem, chcete-li výsledek upravit.',
    sendingFriendRequest: 'Odesílání žádosti o zařazení mezi přátele',
    requestSent: 'Žádost odeslána!',
    pleaseCorrectErrors: 'Opravte prosím tyto chyby',
    tagThis: 'Přidat popisek',
    addOrEditYourTags: 'Přidejte nebo upravte své popisky:',
    addYourRating: 'Přidejte hodnocení:',
    separateMultipleTagsWithCommas: 'Zadáváte-li více popisků, oddělte je čárkami, např. špičkový, "Nový Zéland"',
    saved: 'Uloženo!',
    noo: 'NOVINKA',
    none: 'ŽÁDNÉ',
    joinNow: 'Přidejte se',
    join: 'Přidat se',
    youHaventRated: 'Tento příspěvek jste ještě nehodnotili.',
    yourRatedThis: function(n) {
        switch(n) {
            case 1: return 'Tento příspěvek jste ohodnotili 1 hvězdičkou. ';
            default: return 'Tento příspěvek jste ohodnotili ' + n + ' hvězdičkami. ';
        }
    },
    yourRatingHasBeenAdded: 'Hodnocení bylo přidáno.',
    thereWasAnErrorRating: 'Při hodnocení došlo k chybě.',
    yourTagsHaveBeenAdded: 'Popisky byly přidány.',
    thereWasAnErrorTagging: 'Při přidávání popisků došlo k chybě.',
    addToFavorites: 'Přidat k oblíbeným',
    removeFromFavorites: 'Odebrat z oblíbených',
    nStarsOutOfM: function(n,m) {
        switch(n) {
            case 1: return '1 hvězdička z ' + m;
            default: return n + ' hvězdiček z ' + m;
        }
    },
    follow: 'Sledovat',
    stopFollowing: 'Zastavit sledování',
    pendingPromptTitle: 'Vaše členství čeká na schválení',
    youCanDoThis: 'Tuto operaci můžete provést, jakmile budete jako člen schválen(a) správci.',
    addExistingFile: 'nebo vložte stávající soubor.',
    editYourTags: 'Upravit popisky',
    addTags: 'Přidat popisky',
    yourMessage: 'Vaše zpráva',
    updateMessage: 'Aktualizovat zprávu',
    updateMessageQ: 'Aktualizovat zprávu?',
    removeWords: 'Aby vaše emailová zpráva byla úspěšně doručena, doporučujeme změnit nebo vypustit následující slova:',
    warningMessage: 'Vypadá to, že ve vaší zprávě jsou slova, která mohou umístit vaši poštu do složky nevyžádané pošty.',
    errorMessage: 'V této zprávě je 6 nebo více slov, která mohou umístit vaši poštu do složky nevyžádané pošty.',
    goBack: 'Jít zpět',
    sendAnyway: 'Přesto odeslat',
    messageIsTooLong: function(n,m) { return 'Litujeme. maximální počet znaků je '+m+'.' },
    editLocation: 'Upravit umístění',
    editTypes: 'Upravit typ události',
    pleaseEnterAComment: 'Zadejte komentář',
    pleaseEnterAFileAddress: 'Zadejte adresu souboru',
    pleaseEnterAWebsite: 'Zadejte adresu webových stránek.'
});


dojo.evalObjPath('xg.video.nls', true);
dojo.lang.mixin(xg.video.nls, xg.index.i18n, {
    edit: 'Upravit',
    display: 'Zobrazit',
    detail: 'Detail',
    player: 'Přehrávač',
    from: 'Od:',
    show: 'Zobrazit',
    videos: 'videoklipy',
    cancel: 'Storno',
    save: 'Uložit',
    saving: 'Ukládání…',
    deleteThisVideo: 'Odstranit tento videoklip?',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Počet znaků (' + n + ') převyšuje maximum (' + maximum + ') '; },
    weCouldNotLookUpAddress: function(address) { return 'Adresu „' + address + '" se bohužel nepodařilo najít. '; },
    approve: 'Schválit',
    approving: 'Schvalování…',
    keepWindowOpenWhileApproving: 'Schvalování videoklipů - nechte toto okno prohlížeče otevřené.  Operace může trvat několik minut.',
    'delete': 'Odstranit',
    deleting: 'Odstraňování…',
    keepWindowOpenWhileDeleting: 'Odstraňování videoklipů - nechte toto okno prohlížeče otevřené.  Operace může trvat několik minut.',
    pasteInEmbedCode: 'Vložte sem HTML kód videoklipu z jiných webových stránek.',
    pleaseSelectVideoToUpload: 'Vyberte videoklip k uploadu.',
    embedCodeContainsMoreThanOneVideo: 'Vložený kód obsahuje odkaz na více videoklipů.  Zkontrolujte, zda má pouze jednu značku <object> nebo <embed>.',
    embedCodeMissingTag: 'Vloženému kódu chybí značka typu &lt; embed&gt;   nebo &lt; objekt&gt;  tag.',
    fileIsNotAMov: 'Tento soubor zřejmě není ve formátu mov, . mpg, . mp4, . avi, . 3gp nebo . wmv.  Chcete se přesto pokusit o jeho upload?',
    pleaseEnterAComment: 'Zadejte komentář',
    youRatedVideoNStars: function(n) {
        switch(n) {
            case 1: return 'Tento videoklip jste ohodnotili 1 hvězdičkou! ';
            default: return 'Tento videoklip jste ohodnotili ' + n + 'hvězdičkami! ';
        }
    },
    deleteThisComment: 'Odstranit tento komentář?',
    embedHTMLCode: 'Vložený HTML kód:',
    copyHTMLCode: 'Kopírovat HTML kód',
    shareOnFacebook: 'Sdílet na Facebooku',
    directLink: 'Přímý odkaz',
    addToMyspace: 'Přidat na MySpace',
    addToOthers: 'Přidat na Jiné'
});


dojo.evalObjPath('xg.uploader.nls', true);
dojo.lang.mixin(xg.uploader.nls, xg.index.i18n, {
    fileBrowserHeader: 'Tento počítač',
    fileRoot: 'Tento počítač',
    fileInformationHeader: 'Informace',
    uploadHeader: 'Soubory k uploadu',
    dragOutInstructions: 'Přetáhněte ven soubory, které chcete odebrat',
    dragInInstructions: 'Přetáhněte soubory sem',
    selectInstructions: 'Vyberte soubor',
    files: 'Soubory',
    totalSize: 'Velikost celkem',
    fileName: 'Název',
    fileSize: 'Velikost',
    nextButton: 'Další >',
    okayButton: 'OK',
    yesButton: 'Ano',
    noButton: 'Ne',
    uploadButton: 'Uploadovat',
    cancelButton: 'Storno',
    backButton: 'Zpět',
    continueButton: 'Pokračovat',
    uploadingLabel: 'Probíhá upload...',
    uploadingStatus: function(n, m) { return 'Uploadování ' + n + ' z ' + m; },
    uploadingInstructions: 'Probíhá upload - než skončí, nechte toto okno otevřené',
    uploadLimitWarning: function(n) { return 'Můžete uploadovat až ' + n + ' souborů najednou. '; },
    uploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Přidali jste maximální počet souborů. ';
            case 1: return 'Můžete uploadovat 1 další soubor. ';
            default: return 'Můžete uploadovat další soubory (' + n + '). ';
        }
    },
    iHaveTheRight: 'Tyto soubory mám právo uploadovat v souladu s <a href="/main/authorization/termsOfService">Podmínkami služby</a>',
    updateJavaTitle: 'Aktualizace Javy',
    updateJavaDescription: 'Hromadný upload vyžaduje novější verzi Javy. Chcete-li aktualizaci Javy stáhnout, klepněte na "OK".',
    batchEditorLabel: 'Upravit informace pro veškeré položky',
    applyThisInfo: 'Použít tyto informace u následujících souborů',
    titleProperty: 'Název',
    descriptionProperty: 'Popis',
    tagsProperty: 'Popisky',
    viewableByProperty: 'Smí si ho prohlížet',
    viewableByEveryone: 'Kdokoli',
    viewableByFriends: 'Jen přátelé',
    viewableByMe: 'Jen já',
    albumProperty: 'Album',
    artistProperty: 'Interpret',
    enableDownloadLinkProperty: 'Povolit odkaz ke stažení',
    enableProfileUsageProperty: 'Umožněte ostatním, aby si tuto skladbu přidali na své stránky',
    licenseProperty: 'Licence',
    creativeCommonsVersion: '3.0',
    selectLicense: '- Vyberte licenci -',
    copyright: '© Všechna práva vyhrazena',
    ccByX: function(n) { return 'Licence Creative Commons Attribution ' + n; },
    ccBySaX: function(n) { return 'Licence Creative Commons Attribution Share Alike ' + n; },
    ccByNdX: function(n) { return 'Licence Creative Commons Attribution No Derivatives ' + n; },
    ccByNcX: function(n) { return 'Licence Creative Commons Attribution Non-commercial ' + n; },
    ccByNcSaX: function(n) { return 'Licence Creative Commons Attribution Non-commercial Share Alike ' + n; },
    ccByNcNdX: function(n) { return 'Licence Creative Commons Attribution Non-commercial No Derivatives ' + n; },
    publicDomain: 'Veřejná doména',
    other: 'Jiný',
    errorUnexpectedTitle: 'Bohužel!',
    errorUnexpectedDescription: 'Došlo k chybě. Opakujte akci.',
    errorTooManyTitle: 'Příliš mnoho souborů',
    errorTooManyDescription: function(n) { return 'Uploadovat můžete bohužel pouze ' + n + ' soubory (souborů) najednou. '; },
    errorNotAMemberTitle: 'Není povoleno',
    errorNotAMemberDescription: 'Bohužel, k uploadu musíte být naším členem.',
    errorContentTypeNotAllowedTitle: 'Není povoleno',
    errorContentTypeNotAllowedDescription: 'Bohužel, uploadování materiálů tohoto typu nemáte povoleno.',
    errorUnsupportedFormatTitle: 'Bohužel!',
    errorUnsupportedFormatDescription: 'Bohužel, tento typ souborů server nepodporuje.',
    errorUnsupportedFileTitle: 'Bohužel!',
    errorUnsupportedFileDescription: 'Soubor foo.exe je v nepodporovaném formátu.',
    errorUploadUnexpectedTitle: 'Bohužel!',
    errorUploadUnexpectedDescription: function(file) {
        return file ?
			('Podle všeho došlo k problémům se souborem ' + file + '. Odeberte jej ze seznamu, než soubory uploadujete.') :
            'Podle všeho došlo k problémům s prvním souborem na seznamu. Odeberte jej, než soubory uploadujete.';
    },
    cancelUploadTitle: 'Zrušit upload?',
    cancelUploadDescription: 'Opravdu chcete zrušit upload zbývajících souborů?',
    uploadSuccessfulTitle: 'Upload dokončen',
    uploadSuccessfulDescription: 'Počkejte prosím, než vás přesměrujeme k uploadovaným souborům...',
    uploadPendingDescription: 'Soubory byly úspěšně odeslány a čekají na schválení.',
    photosUploadHeader: 'Snímky k uploadu',
    photosDragOutInstructions: 'Přetáhněte ven snímky, které chcete odebrat',
    photosDragInInstructions: 'Přetáhněte snímky sem',
    photosSelectInstructions: 'Vyberte snímek',
    photosFiles: 'Snímky',
    photosUploadingStatus: function(n, m) { return 'Uploadování ' + n + '. snímku z ' + m; },
    photosErrorTooManyTitle: 'Příliš mnoho snímků',
    photosErrorTooManyDescription: function(n) { return 'Uploadovat můžete bohužel pouze ' + n + ' snímky (snímků) najednou. '; },
    photosErrorContentTypeNotAllowedDescription: 'Bohužel, upload snímků byl deaktivován.',
    photosErrorUnsupportedFormatDescription: 'Uploadovat bohužel můžete pouze obrázky ve formátu .jpg, .gif nebo .png.',
    photosErrorUnsupportedFileDescription: function(n) { return n + ' není ve formátu .jpg, .gif nebo .png.'; },
    photosBatchEditorLabel: 'Upravit informace pro veškeré snímky',
    photosApplyThisInfo: 'Použít tyto informace u následujících snímků',
    photosErrorUploadUnexpectedDescription: function(file) {
        return file ?
            ('Podle všeho došlo k problémům se souborem ' + file + '. Odeberte jej ze seznamu, než snímky uploadujete.') :
            'Podle všeho došlo k problémům s prvním snímkem na seznamu. Odeberte jej, než snímky uploadujete.';
    },
    photosUploadSuccessfulDescription: 'Počkejte prosím, než vás přesměrujeme k vašim snímkům...',
    photosUploadPendingDescription: 'Snímky byly úspěšně odeslány a čekají na schválení.',
    photosUploadLimitWarning: function(n) { return 'Můžete uploadovat až ' + n + ' snímky (snímků) najednou. '; },
    photosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Přidali jste maximální počet snímků. ';
            case 1: return 'Můžete uploadovat ještě 1 snímek. ';
            default: return 'Můžete uploadovat ještě další snímky (' + n + '). ';
        }
    },
    photosIHaveTheRight: 'Tyto snímky mám právo uploadovat v souladu s <a href="/main/authorization/termsOfService">Podmínkami služby</a>',
    videosUploadHeader: 'Videoklipy k uploadu',
    videosDragInInstructions: 'Přetáhněte videoklipy sem',
    videosDragOutInstructions: 'Přetáhněte ven videoklipy, které chcete odebrat',
    videosSelectInstructions: 'Vyberte videoklip',
    videosFiles: 'Videoklipy',
    videosUploadingStatus: function(n, m) { return 'Uploadování ' + n + '. videoklipu z ' + m; },
    videosErrorTooManyTitle: 'Příliš mnoho videoklipů',
    videosErrorTooManyDescription: function(n) { return 'Uploadovat můžete bohužel pouze ' + n + ' videoklipy (videoklipů) najednou. '; },
    videosErrorContentTypeNotAllowedDescription: 'Bohužel, upload videoklipů byl deaktivován.',
    videosErrorUnsupportedFormatDescription: 'Uploadovat bohužel můžete pouze videoklipy ve formátu .avi, .mov, .mp4, .wmv nebo .mpg.',
    videosErrorUnsupportedFileDescription: function(x) { return x + ' není ve formátu .avi, .mov, .mp4, .wmv ani .mpg.'; },
    videosBatchEditorLabel: 'Upravit informace pro veškeré videoklipy',
    videosApplyThisInfo: 'Použít tyto informace u následujících videoklipů',
    videosErrorUploadUnexpectedDescription: function(file) {
        return file ?
            ('Podle všeho došlo k problémům se souborem ' + file + '. Odeberte jej ze seznamu, než videoklipy uploadujete.') :
            'Podle všeho došlo k problémům s prvním videoklipem na seznamu. Odeberte jej, než videoklipy uploadujete.';
    },
    videosUploadSuccessfulDescription: 'Počkejte prosím, než vás přesměrujeme k vašim videoklipům...',
    videosUploadPendingDescription: 'Videoklipy byly úspěšně odeslány a čekají na schválení.',
    videosUploadLimitWarning: function(n) { return 'Můžete uploadovat až ' + n + ' videoklipy (videoklipů) najednou. '; },
    videosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Přidali jste maximální počet videoklipů. ';
            case 1: return 'Můžete uploadovat ještě 1 videoklip. ';
            default: return 'Můžete uploadovat ještě další videoklipy (' + n + '). ';
        }
    },
    videosIHaveTheRight: 'Tyto videoklipy mám právo uploadovat v souladu s <a href="/main/authorization/termsOfService">Podmínkami služby</a>',
    musicUploadHeader: 'Skladby k uploadu',
    musicTitleProperty: 'Název skladby',
    musicDragOutInstructions: 'Přetáhněte ven skladby, které chcete odebrat',
    musicDragInInstructions: 'Přetáhněte skladby sem',
    musicSelectInstructions: 'Vyberte skladbu',
    musicFiles: 'Skladby',
    musicUploadingStatus: function(n, m) { return 'Uploadování ' + n + '. skladby z ' + m; },
    musicErrorTooManyTitle: 'Příliš mnoho skladeb',
    musicErrorTooManyDescription: function(n) { return 'Uploadovat můžete bohužel pouze ' + n + ' skladby (skladeb) najednou. '; },
    musicErrorContentTypeNotAllowedDescription: 'Bohužel, upload skladeb byl deaktivován.',
    musicErrorUnsupportedFormatDescription: 'Uploadovat můžete bohužel pouze skladby ve formátu .mp3.',
    musicErrorUnsupportedFileDescription: function(x) { return x + ' není ve formátu .mp3.'; },
    musicBatchEditorLabel: 'Upravit informace pro veškeré skladby',
    musicApplyThisInfo: 'Použít tyto informace u následujících skladeb',
    musicErrorUploadUnexpectedDescription: function(file) {
        return file ?
            ('Podle všeho došlo k problémům se souborem ' + file + '. Odeberte jej ze seznamu, než skladby uploadujete.') :
            'Podle všeho došlo k problémům s první skladbou na seznamu. Odeberte ji, než skladby uploadujete.';
    },
    musicUploadSuccessfulDescription: 'Počkejte prosím, než vás přesměrujeme k vašim skladbám...',
    musicUploadPendingDescription: 'Skladby byly úspěšně odeslány a čekají na schválení.',
    musicUploadLimitWarning: function(n) { return 'Můžete uploadovat až ' + n + ' skladby (skladeb) najednou. '; },
    musicUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Přidali jste maximální počet skladeb. ';
            case 1: return 'Můžete uploadovat ještě 1 skladbu. ';
            default: return 'Můžete uploadovat ještě další skladby (' + n + '). ';
        }
    },
    musicIHaveTheRight: 'Tyto skladby mám právo uploadovat v souladu s <a href="/main/authorization/termsOfService">Podmínkami služby</a>'
});


dojo.evalObjPath('xg.events.nls', true);
dojo.lang.mixin(xg.events.nls, xg.index.i18n, {
    haveNotYetRsvped: 'Dosud neodeslal(a) odpověď na pozvánku',
    areAttending: 'se účastní',
    mightAttend: 'se možná zúčastní',
    areNotAttending: 'Nezúčastní se',
    messageIsTooLong: function(n) { return 'Vaše zpráva je příliš dlouhá. Použijte maximálně '+n+' znaků nebo méně'; },
    sendMessageToGuests: 'Poslat zprávu hostům',
    sendMessageToGuestsThat: 'Postal hostům zprávu, že:',
    messageSent: 'Zpráva odeslána!',
    chooseRecipient: 'Prosím vyberte adresáta',
    pleaseChooseImage: 'Vyberte obrázek pro událost',
    pleaseEnterAMessage: 'Prosím zadejte zprávu',
    pleaseEnterDescription: 'Zadejte popis události',
    pleaseEnterLocation: 'Zadejte místo události',
    pleaseEnterTitle: 'Zadejte název události',
    pleaseEnterType: 'Zadejte alespoň jeden typ události',
    send: 'Odeslat',
    sending: 'Probíhá odesílání',
    thereHasBeenAnError: 'Došlo k chybě',
    yourMessage: 'Vaše zpráva',
    yourMessageHasBeenSent: 'Vaše zpráva byla odeslána.',
    yourMessageIsBeingSent: 'Vaše zpráve je odesílána.'
});


dojo.evalObjPath('xg.notes.nls', true);
dojo.lang.mixin(xg.notes.nls, xg.index.i18n, {
    addNewNote: 'Přidat novou poznámku',
    noteTitleTooLong: 'Název poznámky je příliš dlouhý',
    pleaseEnterNoteEntry: 'Zadejte poznámku.',
    pleaseEnterNoteTitle: 'Zadejte název poznámky!'
});