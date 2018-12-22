dojo.provide('xg.shared.messagecatalogs.bg_BG');

dojo.require('xg.index.i18n');

/**
 * Texts for the Bulgarian (Bulgaria)
 */
// Use UTF-8 byte
dojo.lang.mixin(xg.feed.nls, xg.index.i18n, {
    edit: 'Редактирай',
    title: 'Заглавие:',
    feedUrl: 'URL адрес:',
    show: 'Покажи:',
    titles: 'Само заглавия',
    titlesAndDescriptions: 'Подробен изглед',
    display: 'Покажи',
    cancel: 'Отмени',
    save: 'Запиши',
    loading: 'Зареждане…',
    items: 'артикули'
});


dojo.evalObjPath('xg.opensocial.nls', true);
dojo.lang.mixin(xg.opensocial.nls, xg.index.i18n, {
    edit: 'Редактирай',
    title: 'Заглавие:',
    appUrl: 'URL адрес:',
    cancel: 'Отмени',
    save: 'Запиши',
    loading: 'Зареждане…',
    removeBox: 'Премахни кутия',
    removeBoxText: function(feature) { return '<p>Сигурни ли сте, че желаете да премахнете "' + feature + '" кутия от Моя Страница?</p><p>Вие все още ще имате правото на достъп до тази характеристика от „Мои добавени възможности".</p> '},
    removeFeature: 'Премахни възможност',
    removeFeatureText: 'Сигурни ли сте, че желаете да премахнете напълно тази възможност? Тя няма да бъде повече достъпна от Моя страница и Мои добавени възможности.',
    canSendMessages: 'Изпращай ми съобщения',
    canAddActivities: 'Покажи актуализациите в модула Последна активност на Моя страница',
    addFeature: 'Добави възможност',
    youAreAboutToAdd: function(feature, linkAttributes) { return '<p>Вие възнамерявате да добавите <strong>' + feature + '</strong> към Моя страница. Тази възможност е разработена от трета страна.</p> <p>Чрез щракване върху опцията \'Добави възможност\' Вие приемате <a ' + linkAttributes + '> Правилата за използване</a>.</p> на приложенията от платформата. '},
    featureSettings: 'Настройки на възможности',
    allowThisFeatureTo: 'Позволете тази възможност за:',
    updateSettings: 'Актуализиране на настройки',
    onlyEmailMsgSupported: 'Поддържат се само съобщения от тип Е-ПОЩА',
    msgExpectedToContain: 'Очаква се съобщението да съдържа всички полета: тип, заглавие и основна част',
    msgObjectExpected: 'Очаквана тема на съобщение',
    recipientsShdBeString: 'Попучателят може да бъде само ред (приема се и списък, разделен със запетаи)',
    recipientsShdBeSpecified: 'Получателите трябва да се определят и не могат да бъдат празно поле',
    unauthorizedRecipients: 'Неупълномощени получатели, определени за получаване на е-поща',
    rateLimitExceeded: 'Надвишено ограничение',
    userCancelled: 'Операция, отменена от потребителя'
});


dojo.evalObjPath('xg.gadgets.nls', true);
dojo.lang.mixin(xg.gadgets.nls, xg.index.i18n, {
    edit: 'Редактирай',
    title: 'Заглавие:',
    feedUrl: 'URL адрес:',
    cancel: 'Отмени',
    save: 'Запиши',
    loading: 'Зареждане…',
    removeGadget: 'Премахване на притурка',
    findGadgetsInDirectory: 'Търсене на притурка в Указателя за притурки'
});


dojo.evalObjPath('xg.forum.nls', true);
dojo.lang.mixin(xg.forum.nls, xg.index.i18n, {
    items: 'артикули',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Броят символи (' + n + ') превишава максимално допустимия (' + maximum + ') '; },
    pleaseEnterFirstPost: 'Моля, напишете първата публикация за дискусията',
    pleaseEnterTitle: 'Моля, въведете заглавие за дискусията',
    save: 'Запиши',
    cancel: 'Отмени',
    yes: 'Да',
    no: 'Не',
    edit: 'Редактирай',
    deleteCategory: 'Изтриване на категория',
    discussionsWillBeDeleted: 'Дискусията в тази категория ще бъде изтрита.',
    whatDoWithDiscussions: 'Какво бихте желали да направите с дискусиите в тази категория?',
    moveDiscussionsTo: 'Премести дискусиите в:',
    deleteDiscussions: 'Изтрий дискусиите',
    'delete': 'Изтрий',
    deleteReply: 'Изтрий отговора',
    deleteReplyQ: 'Желаете ли изтриване на този отговор?',
    deletingReplies: 'Изтриване на отговорите…',
    doYouWantToRemoveReplies: 'Желаете ли също да бъдат изтрити и отговорите на този коментар?',
    pleaseKeepWindowOpen: 'Моля, задръжте прозореца на този браузър отворен, докато продължава обработката. Това може да отнеме няколко минути.',
    contributorSaid: function(x) { return x + 'каза: '},
    display: 'Покажи',
    from: 'От',
    show: 'Покажи',
    view: 'Изглед',
    discussions: 'дискусии',
    discussionsFromACategory: 'Дискусии от категория…'
});


dojo.evalObjPath('xg.groups.nls', true);
dojo.lang.mixin(xg.groups.nls, xg.index.i18n, {
    pleaseChooseAName: 'Моля, изберете име за Вашата група.',
    pleaseChooseAUrl: 'Моля, изберете уеб адрес за Вашата група.',
    urlCanContainOnlyLetters: 'Уеб адресът може да съдържа само букви и цифри (без интервали).',
    descriptionTooLong: function(n, maximum) { return 'Дължината на описанието на Вашата група (' + n + ') превишава максимално допустимия размер (' + maximum + ') '; },
    nameTaken: 'Извиняваме се - това име вече е заето. Моля, изберете друго име.',
    urlTaken: 'Извиняваме се - този уеб адрес вече е зает. Моля, изберете друг уеб адрес.',
    whyNot: 'Защо не?',
    groupCreatorDetermines: function(href) { return 'Създателят на групата определя кой може да се присъедини. Ако мислите, че може да сте блокирани по погрешка, моля <a ' + href + '>свържете се със създателя на групата</a> '; },
    edit: 'Редактирай',
    from: 'От',
    show: 'Покажи',
    groups: 'групи',
    pleaseEnterName: 'Моля, въведете Вашето име',
    pleaseEnterEmailAddress: 'Моля, въведете Вашия адрес на е-поща',
    xIsNotValidEmailAddress: function(x) { return x + 'не е валиден адрес на е-поща '; },
    save: 'Запиши',
    cancel: 'Отмени'
});


dojo.evalObjPath('xg.html.nls', true);
dojo.lang.mixin(xg.html.nls, xg.index.i18n, {
    contentsTooLong: function(maximum) { return 'Съдържанието е прекалено дълго. Моля, използвайте по-малко от  ' + maximum + ' букви. '; },
    edit: 'Редактирай',
    save: 'Запиши',
    cancel: 'Отмени',
    saving: 'Записване…',
    addAWidget: function(url) { return '<a href="' + url + '">Добави графичен фрагмент</a> към този текстови прозорец '; }
});


dojo.evalObjPath('xg.index.nls', true);
dojo.lang.mixin(xg.index.nls, xg.index.i18n, {
    done: 'Готово',
    yourMessageIsBeingSent: 'Вашето съобщение беше изпратено.',
    youNeedToAddEmailRecipient: 'Трябва да добавите получател на е-поща.',
    checkPageOut: function (network) {return 'Check out this page on '+network},
    checkingOutTitle: function (title, network) {return 'Check out "'+title+'" on '+network},
    selectOrPaste: 'Трябва да изберете видео клип или да поставите \'embed\' кода',
    selectOrPasteMusic: 'Трябва да изберете песен или да поставите URL адреса',
    cannotKeepFiles: 'Ще трябва да изберете отново Вашите файлове, ако желаете да виждате повече опции. Желаете ли да продължите?',
    pleaseSelectPhotoToUpload: 'Моля, изберете снимка за качване.',
    addingLabel: 'Добавяне...',
    sendingLabel: 'Изпращане...',
    addingInstructions: 'Моля, оставете този прозорец отворен докато се добавя Вашето съдържание.',
    looksLikeNotImage: 'Изглежда, че един или повече файлове не са във формат .jpg, .gif, или .png. Желаете ли въпреки това да се опитате да продължите качването?',
    looksLikeNotVideo: 'Изглежда, че избраният от Вас файл не е във формат .mov, .mpg, .mp4, .avi, .3gp или .wmv. Желаете ли въпреки това да се опитате да продължите качването?',
    looksLikeNotMusic: 'Изглежда, че избраният от Вас файл не е във формат .mp3. Желаете ли въпреки това да се опитате да продължите качването?',
    showingNFriends: function(n, searchString) {
        switch(n) {
            case 1: return 'Показване на 1 съвпадение на приятел "' + searchString + '". <a href="#">Покажи всеки</a> ';
            default: return 'Показване ' + n + ' съвпадение на приятел "' + searchString + '". <a href="#">Покажи на всеки</a> ';
        }
    },
    sendInvitation: 'Изпрати покана',
    sendMessage: 'Изпрати съобщение',
    sendInvitationToNFriends: function(n) {
        switch(n) {
            case 1: return 'Изпрати покана до 1 приятел? ';
            default: return 'Изпрати покана до ' + n + ' приятели? ';
        }
    },
    sendMessageToNFriends: function(n) {
        switch(n) {
            case 1: return 'Желаете изпращане на съобщение до 1 приятел? ';
            default: return 'Желаете изпращане на съобщение до ' + n + ' приятели? ';
        }
    },
    invitingNFriends: function(n) {
        switch(n) {
            case 1: return 'Поканване на 1 приятел… ';
            default: return 'Поканване на ' + n + ' приятели… ';
        }
    },
    nFriends: function(n) {
        switch(n) {
            case 1: return '1 приятел… ';
            default: return n + ' приятели… ';
        }
    },
    sendingMessageToNFriends: function(n) {
        switch(n) {
            case 1: return 'Изпращане на съобщение до 1 приятел… ';
            default: return 'Изпращане на съобщение до ' + n + ' приятели… ';
        }
    },
    yourMessageOptional: '<label>Вашето съобщение</label> (опция)',
    subjectIsTooLong: function(n) { return 'Вашата тема е прекалено дълга. Моля, използвайте '+n+' символа или по-малко. '; },
    messageIsTooLong: function(n) { return 'Вашето съобщение е прекалено дълго. Моля, използвайте '+n+' символа или по-малко. '; },
    pleaseChoosePeople: 'Моля изберете няколко човека, които да поканите.',
    noPeopleSelected: 'Няма избрани хора',
    pleaseEnterEmailAddress: 'Моля, въведете Вашия адрес на е-поща.',
    pleaseEnterPassword: function(emailAddress) { return 'Моля, въведете Вашата прола за ' + emailAddress + '. '; },
    sorryWeDoNotSupport: 'Съжаляваме, но не поддържаме уеб адресна книга за Вашите адреси на е-поща. Опитайте да щракнете върху "Приложение Адресна книга" по-долу, за да използвате адресите от Вашия компютър.',
    pleaseSelectSecondPart: 'Моля, изберете втората част от Вашия адрес на е-поща, напр. gmail.com.',
    atSymbolNotAllowed: 'Моля, уверете се, че символът @ не е в първата част на адреса на е-поща.',
    resetTextQ: 'Желаете ли възстановяване на текста?',
    resetTextToOriginalVersion: 'Сигурни ли сте, че желете да възстановите Вашия текст в първончалната му версия? Всички направени от Вас промени ще бъдат загубени.',
    changeQuestionsToPublic: 'Желете ли промяна на въпроса към публиката?',
    changingPrivateQuestionsToPublic: 'Промяната на поверителни въпроси към публиката  ще изложи въпросите на всички членове. Сигурни ли сте?',
    youHaveUnsavedChanges: 'Имате незаписани промени.',
    pleaseEnterASiteName: 'Моля, въведете име за социалната мрежа, напр. Клубът на малкия клоун',
    pleaseEnterShorterSiteName: 'Моля, въведете по-кратко име (макс. 64 символа)',
    pleaseEnterShorterSiteDescription: 'Моля, въведете по-кратко описание (макс. 140 символа)',
    siteNameHasInvalidCharacters: 'В името има някои невалидни символи',
    thereIsAProblem: 'Има проблем с Вашата информация',
    thisSiteIsOnline: 'Тази социална мрежа е онлайн',
    online: '<strong>Онлайн</strong>',
    onlineSiteCanBeViewed: '<strong>Онлайн</strong> - Мрежата може да се преглежда по отношение на настройките за вашата поверителност.',
    takeOffline: 'Премини извън интернет мрежата',
    thisSiteIsOffline: 'Тази социална мрежа е извън интернет мрежата',
    offline: '<strong>Извън интернет мрежата</strong>',
    offlineOnlyYouCanView: '<strong>Извън интернет мрежата</strong> - Само Вие можете да разглеждате тази социална мрежа.',
    takeOnline: 'Премини онлайн',
    themeSettings: 'Настройки на темата',
    addYourOwnCss: 'Разширени',
    error: 'Грешка',
    pleaseEnterTitleForFeature: function(displayName) { return 'Моля, въведете заглавие за Вашата ' + displayName + ' възможност '; },
    thereIsAProblemWithTheInformation: 'Наличие на проблем с въведената информация',
    photos: 'Снимки',
    videos: 'Видео файлове',
    pleaseEnterTheChoicesFor: function(questionTitle) { return 'Моля, въведете своя избор за "' + questionTitle + '" напр. Пътешестване, Четене, Пазаруване '; },
    pleaseEnterTheChoices: 'Моля, въведете своя избор, напр. Пътешестване, Четене, Пазаруване',
    email: 'Е-поща',
    subject: 'Тема',
    message: 'Съобщение',
    send: 'Изпрати',
    cancel: 'Отмени',
    go: 'Отиди',
    areYouSureYouWant: 'Сигурни ли сте, че желаете да направите това?',
    processing: 'В процес на обработка…',
    pleaseKeepWindowOpen: 'Моля, задръжте прозореца на този браузър отворен, докато продължава обработката. Това може да отнеме няколко минути.',
    complete: 'Завършено!',
    processIsComplete: 'Обработка завършена.',
    processingFailed: 'Съжаляваме, обработката е прекъсната. Моля, опитайте по-късно отново.',
    ok: 'OK',
    body: 'Основен текст',
    pleaseEnterASubject: 'Моля, въведете тема',
    pleaseEnterAMessage: 'Моля, въведете съобщение',
    pleaseChooseFriends: 'Моля, изберете някои приятели преди да изпратите Вашето съобщение.',
    thereHasBeenAnError: 'Възникнала е грешка',
    thereWasAProblem: 'Възникнал е проблем при добавяне на Вашето съдържание. Моля, опитайте по-късно отново.',
    fileNotFound: 'Файлът не е намерен',
    pleaseProvideADescription: 'Моля, представете описание',
    pleaseEnterSomeFeedback: 'Моля, въведете обратна връзка',
    title: 'Заглавие:',
    setAsMainSiteFeature: 'Задай като Основна възможност',
    thisIsTheMainSiteFeature: 'Това е основната възможност',
    customized: 'Персонализиран',
    copyHtmlCode: 'Копирай HTML кода',
    playerSize: 'Размер на плейъра',
    selectSource: 'Избери източник',
    myAlbums: 'Мои албуми',
    myMusic: 'Моя музика',
    myVideos: 'Мои видео файлове',
    showPlaylist: 'Покажи списъка за изпълнение',
    change: 'Промени',
    changing: 'Промяна...',
    changeSettings: 'Желаете ли промяна на настройките?',
    keepWindowOpenWhileChanging: 'Моля, задръжте отворен този прозорец на браузър, докато се променят настройките за поверителност. Този процес може да отнеме няколко минути.',
    htmlNotAllowed: 'Неразрешен HTML',
    noFriendsFound: 'Няма открити приятели, които съвпадат с Вашето търсене.',
    yourSubject: 'Вашата тема',
    yourMessage: 'Вашето съобщение',
    pleaseEnterFbApiKey: 'Моля, въведете Вашия API ключ за Facebook.',
    pleaseEnterValidFbApiKey: 'Моля, въведете валиден API ключ за Facebook.',
    pleaseEnterFbApiSecret: 'Моля, въведете Вашата API тайна за Facebook.',
    pleaseEnterValidFbApiSecret: 'Моля, въведете валидна API тайна за Facebook.',
    pleaseEnterFbTabName: 'Моля, въведете име за Вашия раздел с  Facebook приложения.',
    pleaseEnterValidFbTabName: function(maxChars) {
                                   return 'Моля, въведете по-кратко име за Вашия раздел с  Facebook приложения.  Максималната дължина е ' + maxChars + ' character ' + (maxChars == 1 ? '' : 's') + '. ';
                               },
    newTab: 'Нов раздел',
    saveYourChanges: 'Желаете ли да запишете Вашите промени в този раздел',
    areYouSureNavigateAway: 'Имате незаписани промени',
    youTabUpdated: 'Вашият раздел е запазен',
    youTabUpdatedUrl: function(url) { return 'Вашият раздел е запазен. Щракнете <a href="'+url+'" target="_blank">тук</a> за редакция на Вашата нова страница.' },
    resetToDefaults: 'Възстановяване до първоначални по подразбиране',
    youNaviWillbeRestored: 'Раазделите за навигация ще бъдат възстановени до първоначалната навигация на мрежата.',
    hiddenWarningTop: function(n) { return 'Този раздел не бе добавен към Вашата мрежа. Има ограничение от '+n+' раздели от най-горно ниво. '+ 'Моля премахнете раздели от най-горното ниво или направете раздели от най-горно ниво към подразделите.' },
    hiddenWarningSub: function(n) { return 'Този подраздел не бе добавен към Вашата мрежа. Има ограничение от '+n+' подраздели за раздел от най-горно ниво. '+ 'Моля премахнете подраздели или направете подраздели в най-горно ниво на разделите.' },
    removeConfirm: 'Чрез премахване на този раздел от най-горно ниво ще бъдат премахнати и неговите раздели. Щракнете върху ОК, за да продължите.',
    saveYourTab: 'Запазване на този раздел?',
    yes: 'Да',
    no: 'Не',
    youMustSpecifyTabName: 'Трябва да определите име на раздел'
});


dojo.evalObjPath('xg.music.nls', true);
dojo.lang.mixin(xg.music.nls, xg.index.i18n, {
    play: 'пусни',
    pleaseSelectTrackToUpload: 'Моля, изберете песен за качване.',
    pleaseEnterTrackLink: 'Моля, изберете URL адрес на песен.',
    thereAreUnsavedChanges: 'Има незаписани промени.',
    autoplay: 'Функция Автоматично изпълнение',
    showPlaylist: 'Покажи списъка за изпълнение',
    playLabel: 'Пусни',
    url: 'URL адрес',
    rssXspfOrM3u: 'rss, xspf или m3u',
    save: 'Запиши',
    cancel: 'Отмени',
    edit: 'Редактирай',
    shufflePlaylist: 'Разместване на съдържанието на списъка за изпълнение',
    fileIsNotAnMp3: 'Изглежда, че един от файловете не е във формат MP3. Желаете ли въпреки това да опитате да го качите?',
    entryNotAUrl: 'Изглежда, че един от въведените записи не е  URL адрес. Уверете се, че всички въведени записи започват с <kbd>http://</kbd>'
});


dojo.evalObjPath('xg.page.nls', true);
dojo.lang.mixin(xg.page.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Броят символи (' + n + ') превишава максимално допустимия (' + maximum + ') '; },
    pleaseEnterContent: 'Моля, въведете съдържанието на страницата',
    pleaseEnterTitle: 'Моля, въведете заглавие за страницата',
    pleaseEnterAComment: 'Моля, въведете коментар',
    deleteThisComment: 'Сигурни ли сте, че желаете да изтриете този коментар?',
    save: 'Запиши',
    cancel: 'Отмени',
    edit: 'Редактирай',
    close: 'Затваряне',
    displayPagePosts: 'Показва изпратени съобщения на страницата',
    directory: 'Указател',
    displayTab: 'Покажи раздел',
    addAnotherPage: 'Добави друга страница',
    tabText: 'Текст на раздел',
    urlDirectory: 'URL директория',
    displayTabForPage: 'Дали да се покаже раздел за страницата',
    tabTitle: 'Заглавие на раздел',
    remove: 'Премахни',
    thereIsAProblem: 'Има проблем с Вашата информация'
});


dojo.evalObjPath('xg.photo.nls', true);
dojo.lang.mixin(xg.photo.nls, xg.index.i18n, {
    randomOrder: 'Случайно подреждане',
    untitled: 'Без заглавие',
    photos: 'Снимки',
    edit: 'Редактирай',
    photosFromAnAlbum: 'Албуми',
    show: 'Покажи',
    rows: 'редове',
    cancel: 'Отмени',
    save: 'Запиши',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Броят символи (' + n + ') превишава максимално допустимия (' + maximum + ') '; },
    pleaseSelectPhotoToUpload: 'Моля, изберете снимка за качване.',
    importingNofMPhotos: function(n,m) { return 'Импортиране <span id="currentP">' + n + '</span> на ' + m + ' снимки. '},
    starting: 'Започване…',
    done: 'Готово!',
    from: 'От',
    display: 'Покажи',
    takingYou: 'Насочва Ви да разгледате снимките си…',
    anErrorOccurred: 'За съжаление е възникнала грешка. Моля, докладвайте за този проблем като използвате връзката в долната част на страницата.',
    weCouldntFind: 'Не можахме да намерим никакви снимки! Защо не опитате някоя от останалите опции?'
});


dojo.evalObjPath('xg.activity.nls', true);
dojo.lang.mixin(xg.activity.nls, xg.index.i18n, {
    edit: 'Редактирай',
    show: 'Покажи',
    events: 'събития',
    setWhatActivityGetsDisplayed: 'Задайте коя дейност да се показва',
    save: 'Запиши',
    cancel: 'Отмени'
});


dojo.evalObjPath('xg.profiles.nls', true);
dojo.lang.mixin(xg.profiles.nls, xg.index.i18n, {
    removeFriendTitle: function(username) {return 'Премахни ' + username + ' Като Приятел?'; },
    removeFriendConfirm: function(username) {return 'Сигурни ли сте, че искате да премахнете ' + username + ' като приятел?'},
    pleaseEnterValueForPost: 'Моля, въведете стойност за публикацията',
    edit: 'Редактирай',
    recentlyAdded: 'Последно добавено',
    featured: 'Актуализирани',
    iHaveRecentlyAdded: 'Добавих последно',
    fromTheSite: 'От социалната мрежа',
    cancel: 'Отмени',
    save: 'Запиши',
    loading: 'Зареждане…',
    addAsFriend: 'Добави като приятел',
    requestSent: 'Искането е изпратено!',
    sendingFriendRequest: 'Изпращане на искане на приятел',
    thisIsYou: 'Това си ти!',
    isYourFriend: 'Е твой приятел',
    isBlocked: 'Е блокиран',
    pleaseEnterPostBody: 'Моля, въведете нещо за основен текст на съобщението',
    pleaseEnterChatter: 'Моля, въведете нещо за свой коментар',
    letMeApproveChatters: 'Трябва ли да одобря коментарите преди изпращане на публикацията?',
    noPostChattersImmediately: 'Не – публикувай незабавно коментарите',
    yesApproveChattersFirst: 'Да – първо одобри коментарите',
    yourCommentMustBeApproved: 'Вашите коментари трябва да бъдат одобрени, преди да ги видят всички.',
    reallyDeleteThisPost: 'Наистина ли желаете изтриване на тази публикация?',
    commentWall: 'Стена за коментари',
    commentWallNComments: function(n) {
        switch(n) {
            case 1: return 'Стена за коментари (1 коментар) ';
            default: return 'Стена за коментари (' + n + ' коментара) ';
        }
    },
    display: 'Покажи',
    from: 'От',
    show: 'Покажи',
    rows: 'редове',
    posts: 'публикации',
    returnToDefaultWarning: 'Това ще върне всички възможности и темата на Моя страница обратно към настройките по подразбиране на мрежата. Желаете ли да продължите?',
    networkError: 'Мрежова грешка',
    wereSorry: 'Съжаляваме, но не сме в състояние да запишем Вашия нов модел в момента. Това може да доведе до прекъсване на интернет връзка. Моля, проверете Вашата връзка и опитайте отново.',
    addFeature: 'Добави възможност',
    addFeatureConfirmation: function(linkAttributes) { return '<p>Вие сте на път да добавите нова възможност към Моя Страница. Тази възможност е разработена от трета страна.</p> <p>Чрез щракване върху \'Добави възможност\' вие давате своето съгласие с приложенията на платформата <a ' + linkAttributes + '>Условия на ползване</a>.</p> '; }
});


dojo.evalObjPath('xg.shared.nls', true);
dojo.lang.mixin(xg.shared.nls, xg.index.i18n, {
    yourMessage: 'Вашето съобщение',
    updateMessage: 'Актуализация на съобщение',
    updateMessageQ: 'Желаете ли актуализация на съобщение?',
    removeWords: 'За да се уверите, че Вашето съобщение на е-поща е доставено успешно, препоръчваме да се върнете, за да смените или премахнете следните думи:',
    warningMessage: 'Изглежда, че в това съобщение на е-поща има думи, които биха изпратили съобщението Ви в папка Нежелани съобщения.',
    errorMessage: 'В това съобщение на е-поща има 6 или повече думи, които биха изпратили Вашето съобщение в папка Нежелани съобщения.',
    goBack: 'Връщане',
    sendAnyway: 'Изпращане на всяка цена',
    messageIsTooLong: function(n,m) { return 'Съжаляваме. Максималния брой на символите е '+m+'. ' },
    locationNotFound: function(location) { return 'Не е намерено <em>' + location + '</em>. '; },
    confirmation: 'Потвърждение',
    showMap: 'Покажи карта',
    hideMap: 'Скрий карта',
    yourCommentMustBeApproved: 'Вашите коментари трябва да бъдат одобрени, преди да ги видят всички.',
    nComments: function(n) {
        switch(n) {
            case 1: return '1 Коментар ';
            default: return n + ' Коментари ';
        }
    },
    pleaseEnterAComment: 'Моля, въведете коментар',
    uploadAPhoto: 'Качване на снимка',
    uploadAnImage: 'Качване на изображение',
    uploadAPhotoEllipsis: 'Качване на снимка…',
    useExistingImage: 'Използвай съществуващо изображение:',
    existingImage: 'Съществуващо изображение',
    useThemeImage: 'Използвай тематично изображение:',
    themeImage: 'Тематично изображение',
    noImage: 'Без изображение',
    uploadImageFromComputer: 'Качи изображение от своя компютър',
    tileThisImage: 'Подреди това изображение',
    done: 'Готово',
    currentImage: 'Текущо изображение',
    pickAColor: 'Избери цвят…',
    openColorPicker: 'Отвори Прозорец за избор на цветове',
    transparent: 'Прозрачен',
    loading: 'Зареждане…',
    ok: 'OK',
    save: 'Запиши',
    cancel: 'Отмени',
    saving: 'Записване…',
    addAnImage: 'Добави изображение',
    uploadAFile: 'Качи файл',
    pleaseEnterAWebsite: 'Моля, въведете адрес на уеб сайт',
    pleaseEnterAFileAddress: 'Моля, въведете адреса на файла',
    bold: 'удебелен',
    italic: 'наклонен',
    underline: 'Подчертано',
    strikethrough: 'Задраскано',
    addHyperink: 'Добави Хипервръзка',
    options: 'Опции',
    wrapTextAroundImage: 'Желаете ли текстът да бъде около изображението?',
    imageOnLeft: 'Изображение отляво?',
    imageOnRight: 'Изображение отдясно?',
    createThumbnail: 'Създаване на миниатюра?',
    pixels: 'пиксели',
    createSmallerVersion: 'Създайте по-малка версия на Вашето изображение за показване. Задайте широчината в пиксели.',
    popupWindow: 'Изскачащ прозорец?',
    linkToFullSize: 'Връзка към пълна версия на изображението в изскачащ прозорец.',
    add: 'Добави',
    keepWindowOpen: 'Моля, задръжте този прозорец на браузър отворен по време на качване на изображението.',
    cancelUpload: 'Отмени качването',
    pleaseSelectAFile: 'Моля, изберете файл с изображение',
    pleaseSpecifyAThumbnailSize: 'Моля, определете размера на миниатюра',
    thumbnailSizeMustBeNumber: 'Размерът на миниатюрата трябва да е цифра',
    addExistingImage: 'или вмъкнете съществуващо изображение',
    addExistingFile: 'или вмъкнете съществуващ файл',
    clickToEdit: 'Щракнете, за да редактирате',
    sendingFriendRequest: 'Изпращане на искане на приятел',
    requestSent: 'Искането е изпратено!',
    pleaseCorrectErrors: 'Моля, поправете тези грешки',
    noo: 'НОВ',
    none: 'НЯМА',
    joinNow: 'Присъединете се сега',
    join: 'Присъедини се',
    addToFavorites: 'Добави към Предпочитания',
    removeFromFavorites: 'Премахни от Предпочитания',
    follow: 'Проследи',
    stopFollowing: 'Спри проследяване',
    pendingPromptTitle: 'Чакащо одобрение за членство',
    youCanDoThis: 'Можете да направите това, след като получите одобрение за членство от администраторите.',
    editYourTags: 'Редактирай своите тагове',
    addTags: 'Добави тагове',
    editLocation: 'Редактирай местоположение',
    editTypes: 'Редактирай вида на събитието',
    charactersLeft: function(n) {
        if (n >= 0) {
            return '&nbsp;(' + n + ' left)' ;
        } else {
            return  '&nbsp;(' + Math.abs(n) + ' over)' ;
        }
    },
    commentWall: 'Стена за коментари',
    commentWallNComments: function(n) { switch(n) { case 1: return 'Стена за коментари (1 коментар)'; default: return 'Стена за коментари (' + n + ' коментари)'; } }
});


dojo.evalObjPath('xg.video.nls', true);
dojo.lang.mixin(xg.video.nls, xg.index.i18n, {
    edit: 'Редактирай',
    display: 'Покажи',
    detail: 'Детайл',
    player: 'Плейър',
    from: 'От',
    show: 'Покажи',
    videos: 'видео файлове',
    cancel: 'Отмени',
    save: 'Запиши',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Броят символи (' + n + ') превишава максимално допустимия (' + maximum + ') '; },
    approve: 'Одобри',
    approving: 'Одобрение…',
    keepWindowOpenWhileApproving: 'Моля, задръжте този прозорец на браузър отворен, докато трае одобрението на видео файловете. Този процес може да отнеме няколко минути.',
    'delete': 'Изтрий',
    deleting: 'Изтриване…',
    keepWindowOpenWhileDeleting: 'Моля, задръжте този прозорец на браузър отворен, докато трае изтриването на видео файловете. Този процес може да отнеме няколко минути.',
    pasteInEmbedCode: 'Моля, поставете в кода за внедряване за видео файл от друг сайт.',
    pleaseSelectVideoToUpload: 'Моля, изберете видео файл за качване.',
    embedCodeContainsMoreThanOneVideo: 'Кодът за внедряване съдържа повече от един видео файл. Моля, уверете се, че той има само един таг <object>  и/или <embed>.',
    embedCodeMissingTag: 'В кода за внедряване липсва таг &lt;embed&gt; или &lt;object&gt;.',
    fileIsNotAMov: 'Този файл изглежда не е във формат  .mov, .mpg, .mp4, .avi, .3gp или .wmv. Желаете ли въпреки това да се опитате да го качите?',
    embedHTMLCode: 'HTML код за внедряване:',
    directLink: 'Директна връзка',
    addToMyspace: 'Добави към MySpace',
    shareOnFacebook: 'Сподели във Facebook',
    addToOthers: 'Добави към Други'
});


dojo.evalObjPath('xg.uploader.nls', true);
dojo.lang.mixin(xg.uploader.nls, xg.index.i18n, {
    fileBrowserHeader: 'Моят компютър',
    fileRoot: 'Моят компютър',
    fileInformationHeader: 'Информация',
    uploadHeader: 'Файлове за качване',
    dragOutInstructions: 'Издърпай файлове, за да ги премахнеш',
    dragInInstructions: 'Издърпай файловете тук',
    selectInstructions: 'Избери файл',
    files: 'Файлове',
    totalSize: 'Общ размер',
    fileName: 'Име',
    fileSize: 'Размер',
    nextButton: 'Следващ >',
    okayButton: 'OK',
    yesButton: 'Да',
    noButton: 'Не',
    uploadButton: 'Качване',
    cancelButton: 'Отмени',
    backButton: 'Назад',
    continueButton: 'Продължи',
    uploadingStatus: function(n, m) { return 'Качване ' + n + ' от ' + m; },
    uploadLimitWarning: function(n) { return 'Можете да качвате ' + n + ' файла едновременно. '; },
    uploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Добавихте максималния брой файлове. ';
            case 1: return 'Можете да качите още 1 файл. ';
            default: return 'Можете да качите още ' + n + ' файла. ';
        }
    },
    uploadingLabel: 'Качване...',
    uploadingInstructions: 'Моля, оставете този прозорец отворен по време на процеса на качване на файлове',
    iHaveTheRight: 'Имам право да качвам тези файлове съгласно <a href="/main/authorization/termsOfService">Условия за услуга</a>',
    updateJavaTitle: 'Актуализация на Java',
    updateJavaDescription: 'Функцията „bulk uploader” за масово качване изисква по-нова версия на Java. Щракни "Okay", за да вземеш Java.',
    batchEditorLabel: 'Редактирай информацията за всички артикули',
    applyThisInfo: 'Приложи тази информация към файловете по-долу',
    titleProperty: 'Заглавие',
    descriptionProperty: 'Описание',
    tagsProperty: 'Тагове',
    viewableByProperty: 'Може да се разглежда от',
    viewableByEveryone: 'Всеки',
    viewableByFriends: 'Само от мои приятели',
    viewableByMe: 'Само от мен',
    albumProperty: 'Албум',
    artistProperty: 'Изпълнител',
    enableDownloadLinkProperty: 'Активирай връзка за изтегляне',
    enableProfileUsageProperty: 'Позволи на хората да поставят тази песен на своите страници',
    licenseProperty: 'Лиценз',
    creativeCommonsVersion: '3.0',
    selectLicense: '— Избери лиценз —',
    copyright: '© Всички права запазени',
    ccByX: function(n) { return 'Лиценз Creative Commons Attribution ' + n; },
    ccBySaX: function(n) { return 'Лицензът Creative Commons Attribution Share Alike  (Признание – Споделяне на споделеното) ' + n; },
    ccByNdX: function(n) { return 'Лицензът Creative Commons Attribution No Derivatives (Признание –Без производни произведения) ' + n; },
    ccByNcX: function(n) { return 'Лицензът Creative Commons Attribution Non-commercial  (Признание - некомерсиално) ' + n; },
    ccByNcSaX: function(n) { return 'Лицензът Creative Commons Attribution  Non-commercial Share Alike  (Признание – Некомерсиално – Споделяне на споделеното) ' + n; },
    ccByNcNdX: function(n) { return 'Creative Commons Attribution Non-commercial No Derivatives (Признание – Некомерсиално – Без производни произведения) ' + n; },
    publicDomain: 'Публичен домейн',
    other: 'Други',
    errorUnexpectedTitle: 'Опаа!',
    errorUnexpectedDescription: 'Възникнала е грешка. Моля, опитайте пак.',
    errorTooManyTitle: 'Прекалено много артикули',
    errorTooManyDescription: function(n) { return 'Съжаляваме, но можете да качвате само ' + n + ' артикула едновременно. '; },
    errorNotAMemberTitle: 'Непозволено',
    errorNotAMemberDescription: 'Съжаляваме, но трябва да сте член, за да имате право да качвате.',
    errorContentTypeNotAllowedTitle: 'Непозволено',
    errorContentTypeNotAllowedDescription: 'Съжаляваме, но Вие нямате право да качвате съдържание от такъв тип.',
    errorUnsupportedFormatTitle: 'Опаа!',
    errorUnsupportedFormatDescription: 'Съжаляваме, но не поддържаме този тип файл.',
    errorUnsupportedFileTitle: 'Опаа!',
    errorUnsupportedFileDescription: 'foo.exe е неподдържан формат.',
    errorUploadUnexpectedTitle: 'Опаа!',
    errorUploadUnexpectedDescription: function(file) { return file ? ('Изглежда, че има проблем с ' + file + ' файл. Премахнете го от списъка, преди да качите останалите Ваши файлове.') : 'Изглежда че има проблем с файла от горната част на списъка. Моля премахнете го преди да качите останалите Ваши файлове.'; },
    cancelUploadTitle: 'Желаете ли отмяна на качването?',
    cancelUploadDescription: 'Сигурен ли сте, че желаете да отмените качването на останалите файлове?',
    uploadSuccessfulTitle: 'Качването завършено',
    uploadSuccessfulDescription: 'Моля, изчакайте докато Ви прехвърлим към качените от Вас файлове...',
    uploadPendingDescription: 'Вашите файлове бяха качени успешно и очакват одобрение.',
    photosUploadHeader: 'Снимки за качване',
    photosDragOutInstructions: 'Издърпайте снимките, за да ги премахнете',
    photosDragInInstructions: 'Издърпайте снимките тук',
    photosSelectInstructions: 'Изберете снимка',
    photosFiles: 'Снимки',
    photosUploadingStatus: function(n, m) { return 'Качване на снимка ' + n + ' от ' + m; },
    photosErrorTooManyTitle: 'Прекалено много снимки',
    photosErrorTooManyDescription: function(n) { return 'Съжаляваме, но можете да качвате само ' + n + ' снимки едновременно. '; },
    photosErrorContentTypeNotAllowedDescription: 'Съжаляваме, но качването на снимки е деактивирано.',
    photosErrorUnsupportedFormatDescription: 'Съжаляваме, но можете да качвате изображения само .jpg, .gif or .png формат.',
    photosErrorUnsupportedFileDescription: function(n) { return n + 'не е .jpg, .gif или .png файл. '; },
    photosBatchEditorLabel: 'Редактиране на информация за всички снимки',
    photosApplyThisInfo: 'Приложи тази информация към снимките по-долу',
    photosErrorUploadUnexpectedDescription: function(file) { return file ? ('изглежда, че има проблем с ' + file + ' файл. Моля премахнете го от списъка преди да качите останалите Ваши снимки.') : 'Изглежда че има проблем със снимката в горната част на списъка. Моля премахнете я преди да качите останалите Ваши снимки.'; },
    photosUploadSuccessfulDescription: 'Моля, изчакайте докато Ви прехвърлим към Вашите снимки...',
    photosUploadPendingDescription: 'Вашите снимки бяха качени успешно и очакват одобрение.',
    photosUploadLimitWarning: function(n) { return 'Можете да качвате ' + n + ' снимки едновременно. '; },
    photosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Добавихте максималния брой снимки. ';
            case 1: return 'Можете да качите още 1 снимка. ';
            default: return 'Можете да качите още ' + n + ' снимки. ';
        }
    },
    photosIHaveTheRight: 'Имам право да качвам тези снимки съгласно <a href="/main/authorization/termsOfService">Условия на обслужване</a>',
    videosUploadHeader: 'Видео фалове за качване',
    videosDragInInstructions: 'Издърпайте видео файловете тук',
    videosDragOutInstructions: 'Издърпай видео файловете, за да ги премахнеш',
    videosSelectInstructions: 'Избери видео файл',
    videosFiles: 'Видео файлове',
    videosUploadingStatus: function(n, m) { return 'Качване на ' + n + ' видео файлове от ' + m; },
    videosErrorTooManyTitle: 'Прекалено много видео файлове',
    videosErrorTooManyDescription: function(n) { return 'Съжаляваме, но можете да качвате само ' + n + ' видео файлове едновременно. '; },
    videosErrorContentTypeNotAllowedDescription: 'Съжаляваме, но качването на видео файлове бе дезактивирано.',
    videosErrorUnsupportedFormatDescription: 'Съжаляваме, но можете да качвате видео файлове само във следните формати: .avi, .mov, .mp4, .wmv или .mpg.',
    videosErrorUnsupportedFileDescription: function(x) { return x + 'не е .avi, .mov, .mp4, .wmv или .mpg файл. '; },
    videosBatchEditorLabel: 'Редактиране на информацията за всички видео файлове',
    videosApplyThisInfo: 'Приложи тази информация към видео файловете по-долу',
    videosErrorUploadUnexpectedDescription: function(file) { return file ? ('Изглежда че има проблем с ' + file + ' файл. Моля премахнете го от листа преди да качите останалата част от Вашите видео файлове.') : 'Изглежда, че има проблем с видео файла в гората част на сисъка. Моля премахнете го преди да качите останалата част от Вашите видео файлове.'; },
    videosUploadSuccessfulDescription: 'Моля, изчакайте докато Ви прехвърлим към Вашите видео файлове...',
    videosUploadPendingDescription: 'Вашите видео файлове бяха качени успешно и очакват одобрение.',
    videosUploadLimitWarning: function(n) { return 'Можете да качвате ' + n + ' видео файлове едновременно. '; },
    videosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Добавили сте максималния брой видео файлове. ';
            case 1: return 'Можете да добавите още 1 видео файл. ';
            default: return 'Можете да качите още ' + n + ' броя видео файлове. ';
        }
    },
    videosIHaveTheRight: 'Имам право да кача тези видео файлове съгласно <a href="/main/authorization/termsOfService">Условия на обслужване</a>',
    musicUploadHeader: 'Песни за качване',
    musicTitleProperty: 'Заглавие на песен',
    musicDragOutInstructions: 'Издърпай песните, за да ги премахнеш',
    musicDragInInstructions: 'Издърпай песни тук',
    musicSelectInstructions: 'Избери песен',
    musicFiles: 'Песни',
    musicUploadingStatus: function(n, m) { return 'Качване на ' + n + ' песен от ' + m; },
    musicErrorTooManyTitle: 'Прекалено много песни',
    musicErrorTooManyDescription: function(n) { return 'Съжаляваме, но можете да качвате само ' + n + ' песни едновременно. '; },
    musicErrorContentTypeNotAllowedDescription: 'Съжаляваме, но функцията за качване на песни е дезактивирана.',
    musicErrorUnsupportedFormatDescription: 'Съжаляваме, но можете да качвате песни само във формат .mp3.',
    musicErrorUnsupportedFileDescription: function(x) { return x + 'не е .mp3 файл. '; },
    musicBatchEditorLabel: 'Редактиране на информацията за всички песни',
    musicApplyThisInfo: 'Приложи това към посочените песни по-долу',
    musicErrorUploadUnexpectedDescription: function(file) { return file ? ('Изглежда, че има проблем с ' + file + ' файл. Моля премахнете го от списъка преди да качите останалите Ваши песни.') : 'TИзглежда, че има проблем с песента в горната част на списъка. Моля премахнете я преди да качите останалите Ваши песни.'; },
    musicUploadSuccessfulDescription: 'Моля, изчакайте докато Ви прехвърлим към Вашите песни....',
    musicUploadPendingDescription: 'Вашите песни бяха успешно качени и очакваме одобрениеl.',
    musicUploadLimitWarning: function(n) { return 'Можете да качвате ' + n + ' песни едновременно. '; },
    musicUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Добавилите сте максималния брой песни. ';
            case 1: return 'Можете да качите още 1 песен. ';
            default: return 'Можете да добавите още ' + n + ' песни. ';
        }
    },
    musicIHaveTheRight: 'Имам правото да качвам тези песни съгласно <a href="/main/authorization/termsOfService">Условия на обслужване</a>'
});


dojo.evalObjPath('xg.events.nls', true);
dojo.lang.mixin(xg.events.nls, xg.index.i18n, {
    pleaseEnterTitle: 'Моля, въведете заглавие за събитието',
    pleaseEnterDescription: 'Моля, въведете заглавие за събитието',
    messageIsTooLong: function(n) { return 'Вашето съобщение е прекалено дълго. Моля, използвайте '+n+' символа или по-малко. '; },
    pleaseEnterLocation: 'Моля, въведете местоположение на събитието',
    pleaseChooseImage: 'Моля, изберете изображение за събитието',
    pleaseEnterType: 'Моля въведете поне един вид за събитието',
    sendMessageToGuests: 'Изпрати съобщение до Гости',
    sendMessageToGuestsThat: 'Изпрати съобщение до гости, че:',
    areAttending: 'Са присъстващи',
    mightAttend: 'Могат да присъстват',
    haveNotYetRsvped: 'Все още нямат отговор на покана',
    areNotAttending: 'Не са присъстващи',
    yourMessage: 'Вашето съобщение',
    send: 'Изпрати',
    sending: 'Изпращане…',
    yourMessageIsBeingSent: 'Вашето съобщение беше изпратено.',
    messageSent: 'Съобщението е изпратено!',
    yourMessageHasBeenSent: 'Вашето съобщение беше изпратено.',
    chooseRecipient: 'Моля, изберете получател.',
    pleaseEnterAMessage: 'Моля, въведете съобщение',
    thereHasBeenAnError: 'Възникнала е грешка'
});


dojo.evalObjPath('xg.notes.nls', true);
dojo.lang.mixin(xg.notes.nls, xg.index.i18n, {
    addNewNote: 'Добавете нова бележка',
    pleaseEnterNoteTitle: 'Моля въведете заглавие на бележката!',
    noteTitleTooLong: 'Заглавието на бележката е прекалено дълго',
    pleaseEnterNoteEntry: 'Моля, въведете запис на забележка'
});