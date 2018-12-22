dojo.provide('xg.shared.messagecatalogs.pl_PL');

dojo.require('xg.index.i18n');

/**
 * Texts for the pl_PL
 */
// Use UTF-8 byte sequences instead of HTML entities, e.g., & instead of &amp;, … instead of &hellip;  [Jon Aquino 2007-01-10]


dojo.evalObjPath('xg.feed.nls', true);
dojo.lang.mixin(xg.feed.nls, xg.index.i18n, {
    edit: 'Edytuj',
    title: 'Tytuł:',
    feedUrl: 'Adres URL:',
    show: 'Pokaż:',
    titles: 'Tylko tytuły',
    titlesAndDescriptions: 'Widok szczegółowy',
    display: 'Wyświetl',
    cancel: 'Anuluj',
    save: 'Zapisz',
    loading: 'Trwa ładowanie…',
    items: 'elementy'
});


dojo.evalObjPath('xg.gadgets.nls', true);
dojo.lang.mixin(xg.gadgets.nls, xg.index.i18n, {
    edit: 'Edytuj',
    title: 'Tytuł:',
    feedUrl: 'Adres URL:',
    cancel: 'Anuluj',
    save: 'Zapisz',
    loading: 'Trwa ładowanie…',
    removeGadget: 'Usuń gadżet',
    findGadgetsInDirectory: 'Znajdź gadżety w Katalogu gadżetów'
});


dojo.evalObjPath('xg.forum.nls', true);
dojo.lang.mixin(xg.forum.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Liczba znaków (' + n + ') przekracza maksimum (' + maximum + ') '; },
    pleaseEnterFirstPost: 'Wpisz pierwszy artykuł w tej dyskusji',
    pleaseEnterTitle: 'Wprowadź tytuł dyskusji',
    save: 'Zapisz',
    cancel: 'Anuluj',
    yes: 'Tak',
    no: 'Nie',
    edit: 'Edytuj',
    deleteCategory: 'Usuń kategorię',
    discussionsWillBeDeleted: 'Dyskusje w tej kategorii zostaną usunięte.',
    whatDoWithDiscussions: 'Co chcesz zrobić z dyskusjami w tej kategorii?',
    moveDiscussionsTo: 'Przenieś dyskusje do:',
    moveToCategory: 'Przenieś do kategorii…',
    deleteDiscussions: 'Usuń dyskusje',
    'delete': 'Usuń',
    deleteReply: 'Usuń odpowiedź',
    deleteReplyQ: 'Czy usunąć tę odpowiedź?',
    deletingReplies: 'Trwa usuwanie odpowiedzi…',
    doYouWantToRemoveReplies: 'Czy chcesz również usunąć odpowiedzi do tego komentarza?',
    pleaseKeepWindowOpen: 'Nie zamykaj okna przeglądarki podczas przetwarzania.  Operacja może potrwać kilka minut.',
    from: 'Od',
    show: 'Pokaż',
    discussions: 'dyskusje',
    discussionsFromACategory: 'Dyskusje w kategorii…',
    display: 'Wyświetlaj',
    items: 'elementów',
    view: 'Wyświetl'
});


dojo.evalObjPath('xg.uploader.nls', true);
dojo.lang.mixin(xg.uploader.nls, xg.index.i18n, {
    fileBrowserHeader: 'Mój komputer',
    fileRoot: 'Mój komputer',
    fileInformationHeader: 'Informacja',
    uploadHeader: 'Pliki do przekazania',
    dragOutInstructions: 'Wyciągnij pliki, aby je usunąć',
    dragInInstructions: 'Przeciągnij pliki tutaj',
    selectInstructions: 'Zaznacz plik',
    files: 'Pliki',
    totalSize: 'Całkowita wielkość',
    fileName: 'Nazwisko',
    fileSize: 'Wielkość',
    nextButton: 'Dalej >',
    okayButton: 'OK',
    yesButton: 'Tak',
    noButton: 'Nie',
    uploadButton: 'Przekaż',
    cancelButton: 'Anuluj',
    backButton: 'Wróć',
    continueButton: 'Kontynuuj',
    uploadingLabel: 'Trwa przekazywanie...',
    uploadingStatus: function(n, m) { return 'Przekazywanie ' + n + ' z ' + m; },
    uploadingInstructions: 'Nie zamykaj tego okna podczas przekazywania',
    uploadLimitWarning: function(n) { return 'Możesz przekazać ' + n + ' plików jednocześnie. '; },
    uploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Dodałeś maksymalną liczbę plików. ';
            case 1: return 'Możesz przekazać jeszcze 1 plik. ';
            default: return 'Możesz przekazać jeszcze ' + n + ' plików. ';
        }
    },
    iHaveTheRight: 'Mam prawo przekazać te pliki zgodnie z <a href="/main/authorization/termsOfService">Warunkami serwisu</a>',
    updateJavaTitle: 'Aktualizuj oprogramowanie Java',
    updateJavaDescription: 'Masowe przekazywanie wymaga najnowszej wersji oprogramowania Java. Kliknij „OK”, aby pobrać oprogramowanie Java.',
    batchEditorLabel: 'Edytuj informacje dla wszystkich pozycji',
    applyThisInfo: 'Zastosuj informację do poniższych plików',
    titleProperty: 'Tytuł',
    descriptionProperty: 'Opis',
    tagsProperty: 'Znaczniki',
    viewableByProperty: 'Osoby mogące wyświetlać to zdjęcie:',
    viewableByEveryone: 'Wszyscy',
    viewableByFriends: 'Tylko moi znajomi',
    viewableByMe: 'Tylko ja',
    albumProperty: 'Album',
    artistProperty: 'Artysta',
    enableDownloadLinkProperty: 'Udostępnij łącze pobierania',
    enableProfileUsageProperty: 'Zezwalaj, aby inne osoby umieszczały ten utwór na swoich stronach',
    licenseProperty: 'Licencja',
    creativeCommonsVersion: '3.0',
    selectLicense: '- Wybierz licencję -',
    copyright: '© Wszelkie prawa zastrzeżone',
    ccByX: function(n) { return 'Uznanie Creative Commons ' + n; },
    ccBySaX: function(n) { return 'Uznanie Creative Commons Udostępniaj podobnie ' + n; },
    ccByNdX: function(n) { return 'Uznanie Creative Commons Brak pochodnych ' + n; },
    ccByNcX: function(n) { return 'Uznanie Creative Commons Niekomercyjny ' + n; },
    ccByNcSaX: function(n) { return 'Uznanie Creative Commons Niekomercyjny, udostępniaj podobnie ' + n; },
    ccByNcNdX: function(n) { return 'Uznanie Creative Commons Niekomercyjny, brak pochodnych ' + n; },
    publicDomain: 'Domena publiczna',
    other: 'Inne',
    errorUnexpectedTitle: 'Ojej!',
    errorUnexpectedDescription: 'Wystąpił błąd. Spróbuj ponownie.',
    errorTooManyTitle: 'Zbyt wiele pozycji',
    errorTooManyDescription: function(n) { return 'Przepraszamy, ale możesz przekazać tylko ' + n + ' pozycji jednocześnie. '; },
    errorNotAMemberTitle: 'Niedozwolone',
    errorNotAMemberDescription: 'Przepraszamy, ale musisz być członkiem, aby przekazywać.',
    errorContentTypeNotAllowedTitle: 'Niedozwolone',
    errorContentTypeNotAllowedDescription: 'Przepraszamy, ale nie wolno Ci przekazywać tego typu zawartości.',
    errorUnsupportedFormatTitle: 'Ojej!',
    errorUnsupportedFormatDescription: 'Przepraszamy, ale nie obsługujemy tego typu plików.',
    errorUnsupportedFileTitle: 'Ojej!',
    errorUnsupportedFileDescription: 'foo.exe jest nieobsługiwanym formatem.',
    errorUploadUnexpectedTitle: 'Ojej!',
    errorUploadUnexpectedDescription: function(file) {
		return file ?
			('Występuje problem z plikiem ' + file + '. Usuń go z listy, zanim przekażesz swoje pozostałe pliki.') :
			'Występuje problem z plikiem na początku listy. Usuń go, zanim przekażesz swoje pozostałe pliki.';
	},
    cancelUploadTitle: 'Anuluj przekazywanie ?',
    cancelUploadDescription: 'Czy na pewno chcesz anulować pozostałe przekazywanie?',
    uploadSuccessfulTitle: 'Przekazywanie zakończone',
    uploadSuccessfulDescription: 'Zaczekaj, aż zostaniesz przeniesiony do swoich przekazanych plików...',
    uploadPendingDescription: 'Twoje pliki zostały pomyślnie przekazane i oczekują na zatwierdzenie.',
    photosUploadHeader: 'Zdjęcia do przekazania',
    photosDragOutInstructions: 'Wyciągnij zdjęcia, aby je usunąć',
    photosDragInInstructions: 'Przeciągnij zdjęcia tutaj',
    photosSelectInstructions: 'Zaznacz zdjęcie',
    photosFiles: 'Zdjęcia',
    photosUploadingStatus: function(n, m) { return 'Przekazywanie zdjęcia ' + n + ' z ' + m; },
    photosErrorTooManyTitle: 'Zbyt wiele zdjęć',
    photosErrorTooManyDescription: function(n) { return 'Przepraszamy, ale możesz przekazać tylko ' + n + ' zdjęć jednocześnie. '; },
    photosErrorContentTypeNotAllowedDescription: 'Przepraszamy, ale przekazywanie zdjęć zostało wyłączone.',
    photosErrorUnsupportedFormatDescription: 'Przepraszamy, ale możesz przekazywać tylko obrazy w formatach .jpg, .gif lub .png.',
    photosErrorUnsupportedFileDescription: function(n) { return n + ' nie jest plikiem .jpg, .gif lub .png.'; },
    photosBatchEditorLabel: 'Edytuj informacje wszystkich zdjęć',
    photosApplyThisInfo: 'Zastosuj informację do poniższych zdjęć',
    photosErrorUploadUnexpectedDescription: function(file) {
		return file ?
			('Występuje problem z plikiem ' + file + '. Usuń go z listy, zanim przekażesz swoje pozostałe zdjęcia.') :
			'Występuje problem ze zdjęciem na początku listy. Usuń je, zanim przekażesz swoje pozostałe zdjęcia.';
	},
    photosUploadSuccessfulDescription: 'Zaczekaj, aż zostaniesz przeniesiony do swoich zdjęć...',
    photosUploadPendingDescription: 'Twoje zdjęcia zostały pomyślnie przekazane i oczekują na zatwierdzenie.',
    photosUploadLimitWarning: function(n) { return 'Możesz przekazać ' + n + ' zdjęć jednocześnie. '; },
    photosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Dodałeś maksymalną liczbę zdjęć. ';
            case 1: return 'Możesz przekazać jeszcze 1 zdjęcie. ';
            default: return 'Możesz przekazać jeszcze ' + n + ' zdjęć. ';
        }
    },
    photosIHaveTheRight: 'Mam prawo przekazać te zdjęcia zgodnie z <a href="/main/authorization/termsOfService">Warunkami serwisu</a>',
    videosUploadHeader: 'Pliki wideo do przekazania',
    videosDragInInstructions: 'Przeciągnij pliki wideo tutaj',
    videosDragOutInstructions: 'Wyciągnij pliki wideo, aby je usunąć',
    videosSelectInstructions: 'Zaznacz plik wideo',
    videosFiles: 'Wideo',
    videosUploadingStatus: function(n, m) { return 'Przekazywanie pliku wideo ' + n + ' z ' + m; },
    videosErrorTooManyTitle: 'Zbyt dużo plików wideo',
    videosErrorTooManyDescription: function(n) { return 'Przepraszamy, ale możesz przekazać tylko ' + n + ' plików wideo jednocześnie. '; },
    videosErrorContentTypeNotAllowedDescription: 'Przepraszamy, ale przekazywanie plików wideo zostało wyłączone.',
    videosErrorUnsupportedFormatDescription: 'Przepraszamy, ale możesz przekazywać tylko pliki wideo w formatach .avi, .mov, .mp4, .wmv lub .mpg.',
    videosErrorUnsupportedFileDescription: function(x) { return x + ' nie jest plikiem .avi, .mov, .mp4, .wmv lub .mpg.'; },
    videosBatchEditorLabel: 'Edytuj informacje wszystkich plików wideo',
    videosApplyThisInfo: 'Zastosuj informację do poniższych plików wideo',
    videosErrorUploadUnexpectedDescription: function(file) {
		return file ?
			('Występuje problem z plikiem ' + file + '. Usuń go, zanim przekażesz swoje pozostałe pliki wideo.') :
			'Występuje problem z plikiem wideo na początku listy. Usuń go, zanim przekażesz swoje pozostałe pliki wideo.';
	},
    videosUploadSuccessfulDescription: 'Zaczekaj, aż zostaniesz przeniesiony do swoich plików wideo...',
    videosUploadPendingDescription: 'Twoje pliki wideo zostały pomyślnie przekazane i oczekują na zatwierdzenie.',
    videosUploadLimitWarning: function(n) { return 'Możesz przekazać ' + n + ' plików wideo jednocześnie. '; },
    videosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Dodałeś maksymalną liczbę plików wideo. ';
            case 1: return 'Możesz przekazać jeszcze 1 plik wideo. ';
            default: return 'Możesz przekazać jeszcze ' + n + ' plików wideo. ';
        }
    },
    videosIHaveTheRight: 'Mam prawo przekazać te pliki wideo zgodnie z <a href="/main/authorization/termsOfService">Warunkami serwisu</a>',
    musicUploadHeader: 'Utwory do przekazania',
    musicTitleProperty: 'Tytuł utworu',
    musicDragOutInstructions: 'Wyciągnij utwory, aby je usunąć',
    musicDragInInstructions: 'Przeciągnij utwory tutaj',
    musicSelectInstructions: 'Zaznacz utwór',
    musicFiles: 'Utwory',
    musicUploadingStatus: function(n, m) { return 'Przekazywanie utworu ' + n + ' z ' + m; },
    musicErrorTooManyTitle: 'Zbyt wiele utworów',
    musicErrorTooManyDescription: function(n) { return 'Przepraszamy, ale możesz przekazać tylko ' + n + ' utworów jednocześnie. '; },
    musicErrorContentTypeNotAllowedDescription: 'Przepraszamy, ale przekazywanie utworów zostało wyłączone.',
    musicErrorUnsupportedFormatDescription: 'Przepraszamy, ale możesz przekazywać tylko utwory w formacie .mp3.',
    musicErrorUnsupportedFileDescription: function(x) { return x + ' nie jest plikiem .mp3.'; },
    musicBatchEditorLabel: 'Edytuj informacje wszystkich utworów',
    musicApplyThisInfo: 'Zastosuj informację do poniższych utworów',
    musicErrorUploadUnexpectedDescription: function(file) {
		return file ?
			('Występuje problem z plikiem ' + file + '. Usuń go z listy, zanim przekażesz swoje pozostałe utwory.') :
			'Występuje problem z utworem na początku listy. Usuń go, zanim przekażesz swoje pozostałe utwory.';
	},
    musicUploadSuccessfulDescription: 'Zaczekaj, aż zostaniesz przeniesiony do swoich utworów...',
    musicUploadPendingDescription: 'Twoje utwory zostały pomyślnie przekazane i oczekują na zatwierdzenie.',
    musicUploadLimitWarning: function(n) { return 'Możesz przekazać ' + n + ' utworów jednocześnie. '; },
    musicUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Dodałeś maksymalną liczbę utworów. ';
            case 1: return 'Możesz przekazać jeszcze 1 utwór. ';
            default: return 'Możesz przekazać jeszcze ' + n + ' utworów. ';
        }
    },
    musicIHaveTheRight: 'Mam prawo przekazać te utwory zgodnie z <a href="/main/authorization/termsOfService">Warunkami serwisu</a>'
});


dojo.evalObjPath('xg.groups.nls', true);
dojo.lang.mixin(xg.groups.nls, xg.index.i18n, {
    pleaseChooseAName: 'Wybierz nazwę dla swojej grupy.',
    pleaseChooseAUrl: 'Wybierz adres www dla swojej grupy.',
    urlCanContainOnlyLetters: 'Adres www może zawierać tylko litery i cyfry (bez spacji).',
    descriptionTooLong: function(n, maximum) { return 'Długość opisu grupy (' + n + ') przekracza maksimum (' + maximum + ') '; },
    nameTaken: 'Przepraszamy - ta nazwa już istnieje.  Wybierz inną nazwę.',
    urlTaken: 'Przepraszamy - ten adres www już istnieje.  Wybierz inny adres www.',
    whyNot: 'Dlaczego nie?',
    groupCreatorDetermines: function(href) { return 'Autor grupy określa możliwość uczestnictwa.  Jeśli uważasz, że zablokowano Cię przez pomyłkę, <a ' + href + '>skontaktuj się z autorem grupy</a> '; },
    edit: 'Edytuj',
    from: 'Od',
    show: 'Pokaż',
    groups: 'grupy',
    pleaseEnterName: 'Wprowadź swoją nazwę',
    pleaseEnterEmailAddress: 'Wprowadź swój adres e-mail',
    xIsNotValidEmailAddress: function(x) { return x + ' is not a valid email address'; },
    save: 'Zapisz',
    cancel: 'Anuluj'
});


dojo.evalObjPath('xg.html.nls', true);
dojo.lang.mixin(xg.html.nls, xg.index.i18n, {
    contentsTooLong: function(maximum) { return 'Treść jest zbyt długa. Użyj mniej niż ' + maximum + ' znaków. '; },
    edit: 'Edytuj',
    save: 'Zapisz',
    cancel: 'Anuluj',
    saving: 'Trwa zapisywanie…',
    addAWidget: function(url) { return '<a href="' + url + '">Dodaj element widget</a> do tego pola tekstowego '; }
});


dojo.evalObjPath('xg.index.nls', true);
dojo.lang.mixin(xg.index.nls, xg.index.i18n, {
    sendInvitation: 'Wyślij zaproszenie',
    sendInvitationToNFriends: function(n) {
        switch(n) {
            case 1: return 'Czy wysłać zaproszenie do 1 znajomego? ';
            default: return 'Czy wysłać zaproszenie do ' + n + ' przyjaciół? ';
        }
    },
    showingNFriends: function(n, searchString) {
        switch(n) {
            case 1: return 'Pokazano 1 znajomego odpowiadającego „' + searchString + '”. <a href="#">Pokaż wszystkich</a> ';
            default: return 'Pokazano ' + n + ' znajomych odpowiadających „' + searchString + '”. <a href="#">Pokaż wszystkich</a> ';
        }
    },
    sendMessage: 'Wyślij wiadomość',
    sendMessageToNFriends: function(n) {
        switch(n) {
            case 1: return 'Czy wysłać wiadomość do 1 znajomego? ';
            default: return 'Czy wysłać wiadomość do ' + n + ' przyjaciół? ';
        }
    },
    invitingNFriends: function(n) {
        switch(n) {
            case 1: return 'Zapraszanie 1 znajomego… ';
            default: return 'Zapraszanie ' + n + ' znajomych… ';
        }
    },
    nFriends: function(n) {
        switch(n) {
            case 1: return '1 znajomy… ';
            default: return n + ' znajomi… ';
        }
    },
    sendingMessageToNFriends: function(n) {
        switch(n) {
            case 1: return 'Wysyłanie wiadomości do 1 znajomego… ';
            default: return 'Wysyłanie wiadomości do ' + n + ' znajomych… ';
        }
    },
    noPeopleSelected: 'Nie wybrano żadnych osób',
    sorryWeDoNotSupport: 'Przepraszamy, ale nie obsługujemy internetowej książki adresowej dla Twojego adresu e-mail. Kliknij \'Książka adresowa programu pocztowego\' poniżej, aby użyć adresów ze swojego komputera.',
    pleaseChooseFriends: 'Wybierz znajomych, zanim wyślesz swoją wiadomość.',
    htmlNotAllowed: 'Kod HTML jest niedozwolony',
    noFriendsFound: 'Nie znaleziono znajomych odpowiadających wyszukiwaniu.',
    yourMessageOptional: '<label>Twoja wiadomość</label> (opcja)',
    pleaseChoosePeople: 'Wybierz osoby, które chcesz zaprosić.',
    pleaseEnterEmailAddress: 'Wprowadź swój adres e-mail.',
    pleaseEnterPassword: function(emailAddress) { return 'Wprowadź swoje hasło dla ' + emailAddress + '. '; },
    sorryWeDontSupport: 'Przepraszamy, ale nie obsługujemy internetowej książki adresowej dla podanego adresu e-mail.  Kliknij  \'program pocztowy\' poniżej, aby użyć adresów z komputera.',
    pleaseSelectSecondPart: 'Wybierz drugą część swojego adresu e-mail, np. gmail. com.',
    atSymbolNotAllowed: 'Symbol @ nie może wystąpić w pierwszej części adresu e-mail.',
    resetTextQ: 'Czy przywrócić oryginalny tekst?',
    resetTextToOriginalVersion: 'Czy na pewno chcesz przywrócić oryginalną wersję całego tekstu?  Wszystkie zmiany zostaną utracone.',
    changeQuestionsToPublic: 'Czy zmienić pytania na publiczne?',
    changingPrivateQuestionsToPublic: 'Zmiana pytań prywatnych na publiczne spowoduje wyświetlenie odpowiedzi wszystkich członków.  Na pewno?',
    youHaveUnsavedChanges: 'Nie zapisano wszystkich zmian.',
    pleaseEnterASiteName: 'Wprowadź nazwę sieci społecznej, np. Tiny Clown Club',
    pleaseEnterShorterSiteName: 'Wprowadź krótszą nazwę (maks. 64 znaki)',
    pleaseEnterShorterSiteDescription: 'Wprowadź krótszy opis (maks. 140 znaków)',
    siteNameHasInvalidCharacters: 'Nazwa zawiera nieprawidłowe znaki',
    thereIsAProblem: 'Wystąpił problem z podanymi informacjami',
    thisSiteIsOnline: 'Sieć społeczna jest on-line',
    onlineSiteCanBeViewed: '<strong>On-line</strong> - Sieć może być przeglądana pod kątem ustawień prywatności.',
    takeOffline: 'Tryb off-line',
    thisSiteIsOffline: 'Sieć społeczna jest w trybie off-line',
    offlineOnlyYouCanView: '<strong>Off-line</strong> - Tylko Ty możesz przeglądać tę sieć społeczną.',
    takeOnline: 'Tryb on-line',
    themeSettings: 'Ustawienia kompozycji',
    addYourOwnCss: 'Zaawansowane',
    error: 'Błąd',
    pleaseEnterTitleForFeature: function(displayName) { return 'Wprowadź tytuł funkcji ' + displayName + ' '; },
    thereIsAProblemWithTheInformation: 'Wystąpił problem z wprowadzoną informacją',
    photos: 'Zdjęcia',
    videos: 'Wideo',
    pleaseEnterTheChoicesFor: function(questionTitle) { return 'Wprowadź opcje dla "' + questionTitle + '", np. piesze wędrówki, czytanie, zakupy '; },
    pleaseEnterTheChoices: 'Wprowadź opcje, np. piesze wędrówki, czytanie, zakupy',
    shareWithFriends: 'Udostępnij przyjaciołom',
    email: 'e-mail',
    separateMultipleAddresses: 'Oddzielaj kolejne adresy przecinkami',
    subject: 'Temat',
    message: 'Wiadomość',
    send: 'Wyślij',
    cancel: 'Anuluj',
    pleaseEnterAValidEmail: 'Wprowadź prawidłowy adres e-mail',
    go: 'Przejdź',
    areYouSureYouWant: 'Czy na pewno chcesz to zrobić?',
    processing: 'Trwa przetwarzanie…',
    pleaseKeepWindowOpen: 'Nie zamykaj okna przeglądarki podczas przetwarzania.  Proces może potrwać kilka minut.',
    complete: 'Zakończono!',
    processIsComplete: 'Proces został zakończony.',
    ok: 'OK',
    body: 'Treść',
    pleaseEnterASubject: 'Wprowadź temat',
    pleaseEnterAMessage: 'Wprowadź wiadomość',
    thereHasBeenAnError: 'Wystąpił błąd',
    fileNotFound: 'Nie znaleziono pliku',
    pleaseProvideADescription: 'Podaj opis',
    pleaseEnterYourFriendsAddresses: 'Wprowadź adresy lub identyfikatory Ning swoich przyjaciół',
    pleaseEnterSomeFeedback: 'Wprowadź opinię',
    title: 'Tytuł:',
    setAsMainSiteFeature: 'Ustaw jako funkcję główną',
    thisIsTheMainSiteFeature: 'To jest funkcja główna',
    customized: 'Dostosowano',
    copyHtmlCode: 'Kopiuj kod HTML',
    playerSize: 'Wielkość odtwarzacza',
    selectSource: 'Wybierz źródło',
    myAlbums: 'Moje albumy',
    myMusic: 'Moja muzyka',
    myVideos: 'Moje wideo',
    showPlaylist: 'Pokaż listę<br> odtwarzania',
    change: 'Zmień',
    changing: 'Trwa zmienianie...',
    changePrivacy: 'Czy zmienić prywatność?',
    keepWindowOpenWhileChanging: 'Nie zamykaj okna przeglądarki podczas zmiany ustawień prywatności.  Proces może potrwać kilka minut.',
    subjectIsTooLong: function(n) { return 'Twój temat jest za długi. Użyj '+n+' lub mniej znaków.'; },
    addingInstructions: 'Nie zamykaj tego okna podczas dodawania Twojej zawartości.',
    addingLabel: 'Trwa dodawanie.. .',
    cannotKeepFiles: 'Jeśli chcesz wyświetlić więcej opcji, musisz ponownie wybrać pliki.  Czy chcesz kontynuować?',
    done: 'Gotowe',
    looksLikeNotImage: 'Prawdopodobnie jeden lub więcej plików nie jest w formacie .jpg, .gif lub .png.  Czy mimo to chcesz spróbować je przekazać?',
    looksLikeNotMusic: 'Wybrany plik prawdopodobnie nie jest w formacie .mp3.  Czy mimo to chcesz spróbować je przekazać?',
    looksLikeNotVideo: 'Wybrany plik prawdopodobnie nie jest w formacie .mov, .mpg, .mp4, .avi, .3gp lub .wmv.  Czy mimo to chcesz spróbować je przekazać?',
    messageIsTooLong: function(n) { return 'Twoja wiadomość jest za długa.  Użyj '+n+' lub mniej znaków.'; },
    pleaseSelectPhotoToUpload: 'Wybierz zdjęcie do przekazania.',
    processingFailed: 'Niestety, przetwarzanie nie powiodło się. Spróbuj ponownie później.',
    selectOrPaste: 'Musisz wybrać plik wideo lub wkleić kod \'embed\'',
    selectOrPasteMusic: 'Musisz wybrać utwór lub wkleić adres URL',
    sendingLabel: 'Trwa wysyłanie… .',
    thereWasAProblem: 'Wystąpił problem podczas dodawania Twojej zawartości.  Spróbuj ponownie później.',
    uploadingInstructions: 'Nie zamykaj tego okna podczas przekazywania',
    uploadingLabel: 'Trwa przekazywanie.. .',
    youNeedToAddEmailRecipient: 'Musisz dodać odbiorcę wiadomości e-mail.',
    yourMessage: 'Twoja wiadomość',
    yourMessageIsBeingSent: 'Twoja wiadomość została wysłana.',
    yourSubject: 'Twój temat'
});


dojo.evalObjPath('xg.music.nls', true);
dojo.lang.mixin(xg.music.nls, xg.index.i18n, {
    play: 'odtwórz',
    pleaseSelectTrackToUpload: 'Wybierz utwór do przekazania.',
    pleaseEnterTrackLink: 'Wprowadź adres URL utworu.',
    thereAreUnsavedChanges: 'Występują niezapisane zmiany.',
    autoplay: 'Autoodtwarzanie',
    showPlaylist: 'Pokaż listę<br> odtwarzania',
    playLabel: 'Odtwórz',
    url: 'adres URL',
    rssXspfOrM3u: 'rss, xspf lub m3u',
    save: 'Zapisz',
    cancel: 'Anuluj',
    edit: 'Edytuj',
    shufflePlaylist: 'Losowa lista <br>odtwarzania',
    fileIsNotAnMp3: 'Jeden z plików nie jest w formacie MP3.  Czy mimo to chcesz go przekazać?',
    entryNotAUrl: 'Jeden z wpisów nie jest adresem URL.  Sprawdź, czy wszystkie wpisy zaczynają się od <kbd>http://</kbd>'
});


dojo.evalObjPath('xg.page.nls', true);
dojo.lang.mixin(xg.page.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Liczba znaków (' + n + ') przekracza maksimum (' + maximum + ') '; },
    pleaseEnterContent: 'Wprowadź zawartość strony',
    pleaseEnterTitle: 'Wprowadź tytuł strony',
    pleaseEnterAComment: 'Wprowadź komentarz',
    deleteThisComment: 'Czy na pewno chcesz usunąć ten komentarz?',
    save: 'Zapisz',
    cancel: 'Anuluj',
    discussionTitle: 'Tytuł strony:',
    tags: 'Znaczniki:',
    edit: 'Edytuj',
    close: 'Zamknij',
    displayPagePosts: 'Wyświetl artykuły na stronie',
    directory: 'Katalog',
    displayTab: 'Wyświetl kartę',
    addAnotherPage: 'Dodaj kolejną stronę',
    tabText: 'Tekst karty',
    urlDirectory: 'Katalog URL',
    displayTabForPage: 'Czy wyświetlać kartę dla tej strony',
    tabTitle: 'Tytuł karty',
    remove: 'Usuń',
    thereIsAProblem: 'Wystąpił problem z Twoimi informacjami'
});


dojo.evalObjPath('xg.photo.nls', true);
dojo.lang.mixin(xg.photo.nls, xg.index.i18n, {
    untitled: 'Brak tytułu',
    randomOrder: 'Losowe zlecenie',
    photos: 'Zdjęcia',
    edit: 'Edytuj',
    photosFromAnAlbum: 'Albumy',
    show: 'Pokaż',
    rows: 'wiersze',
    cancel: 'Anuluj',
    save: 'Zapisz',
    deleteThisPhoto: 'Czy usunąć to zdjęcie?',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Liczba znaków (' + n + ') przekracza maksimum (' + maximum + ') '; },
    weCouldNotLookUpAddress: function(address) { return 'Niestety nie można sprawdzić adresu "' + address + '". '; },
    pleaseSelectPhotoToUpload: 'Wybierz zdjęcie do przekazania.',
    pleaseEnterAComment: 'Wprowadź komentarz.',
    addToExistingAlbum: 'Dodaj do istniejącego albumu',
    addToNewAlbumTitled: 'Dodaj do nowego albumu zatytułowanego…',
    deleteThisComment: 'Czy usunąć ten komentarz?',
    importingNofMPhotos: function(n,m) { return 'Importowanie <span id="currentP">' + n + '</span> z ' + m + ' zdjęć. '},
    starting: 'Trwa uruchamianie…',
    done: 'Gotowe!',
    from: 'Od',
    display: 'Wyświetl',
    takingYou: 'Rozpoczynanie przeglądania Twoich zdjęć…',
    anErrorOccurred: 'Niestety wystąpił błąd.  Zgłoś ten problem, korzystając z odsyłacza na dole strony.',
    weCouldntFind: 'Nie znaleziono żadnych zdjęć!  Sprawdź inne dostępne opcje.'
});


dojo.evalObjPath('xg.activity.nls', true);
dojo.lang.mixin(xg.activity.nls, xg.index.i18n, {
    edit: 'Edytuj',
    show: 'Pokaż',
    events: 'zdarzenia',
    setWhatActivityGetsDisplayed: 'Ustawdziałanie, które ma być wyświetlane',
    save: 'Zapisz',
    cancel: 'Anuluj'
});


dojo.evalObjPath('xg.profiles.nls', true);
dojo.lang.mixin(xg.profiles.nls, xg.index.i18n, {
    pleaseEnterValueForPost: 'Wprowadź wartość dla artykułu',
    pleaseProvideAValidDate: 'Wprowadź prawidłową datę',
    uploadAFile: 'Przekaż plik',
    pleaseEnterUrlOfLink: 'Wprowadź adres URL odsyłacza:',
    pleaseEnterTextOfLink: 'Jaki tekst chcesz połączyć?',
    edit: 'Edytuj',
    recentlyAdded: 'Ostatnio dodane',
    featured: 'Zamieszczone',
    iHaveRecentlyAdded: 'Ostatnio dodane przeze mnie',
    fromTheSite: 'Z sieci społecznej',
    cancel: 'Anuluj',
    save: 'Zapisz',
    loading: 'Trwa ładowanie…',
    addAsFriend: 'Dodaj jako znajomego',
    requestSent: 'Żądanie zostało wysłane!',
    sendingFriendRequest: 'Wysyłanie prośby do znajomego',
    thisIsYou: 'To Ty!',
    isYourFriend: 'Twój znajomy',
    isBlocked: 'Zablokowane',
    pleaseEnterAComment: 'Wprowadź komentarz',
    pleaseEnterPostBody: 'Wprowadź coś w treści artykułu',
    pleaseSelectAFile: 'Wybierz plik',
    pleaseEnterChatter: 'Wprowadź coś w komentarzu',
    toggleBetweenHTML: 'Pokaż/ ukryj kod HTML',
    attachAFile: 'Dołącz plik',
    addAPhoto: 'Dodaj zdjęcie',
    insertALink: 'Wstaw łącze',
    changeTextSize: 'Zmień rozmiar tekstu',
    makeABulletedList: 'Utwórz listę punktowaną',
    makeANumberedList: 'Utwórz listę numerowaną',
    crossOutText: 'Przekreślenie',
    underlineText: 'Podkreślenie',
    italicizeText: 'Kursywa',
    boldText: 'Pogrubienie',
    letMeApproveChatters: 'Zatwierdzać komentarze przed opublikowaniem?',
    noPostChattersImmediately: 'Nie - wysyłaj komentarze od razu',
    yesApproveChattersFirst: 'Tak - najpierw zatwierdź komentarze',
    yourCommentMustBeApproved: 'Musisz zatwierdzić komentarz zanim będzie on widoczny dla wszystkich.',
    reallyDeleteThisPost: 'Na pewno usunąć ten post?',
    commentWall: 'Ściana komentarzy',
    commentWallNComments: function(n) {
        switch(n) {
            case 1: return 'Sciana komentarzy (1 komentarz) ';
            default: return 'Ściana komentarzy (' + n + ' komentarze) ';
        }
    },
    display: 'Wyświetl',
    from: 'Od',
    show: 'Pokaż',
    rows: 'wiersze',
    posts: 'artykuły',
    removeAsFriend: 'Usuń jako znajomego',
    networkError: 'Błąd sieci',
    returnToDefaultWarning: 'Spowoduje to przywrócenie domyślnych opcji  wszystkich funkcji oraz kompozycji na stronie Moja strona. Czy chcesz kontynuować?',
    wereSorry: 'Przepraszamy, ale nie możemy tym razem zapisać Twojego nowegu układu. Prawodopodobnie jest to spowodowane przerwaniem połączenia internetowego. Sprawdź połączenie internetowe i spróbuj ponownie.'
});


dojo.evalObjPath('xg.shared.nls', true);
dojo.lang.mixin(xg.shared.nls, xg.index.i18n, {
    uploadAPhoto: 'Przekaż zdjęcie',
    uploadAnImage: 'Przekaż obraz',
    uploadAPhotoEllipsis: 'Przekaż zdjęcie…',
    useExistingImage: 'Użyj istniejącego obrazu:',
    existingImage: 'Istniejący obraz',
    useThemeImage: 'Użyj obrazu kompozycji:',
    themeImage: 'Obraz kompozycji',
    noImage: 'Brak obrazu',
    uploadImageFromComputer: 'Przekaż obraz ze swojego komputera',
    tileThisImage: 'Umieść ten obraz sąsiadująco',
    done: 'Gotowe',
    currentImage: 'Aktualny obraz',
    pickAColor: 'Wybierz kolor…',
    openColorPicker: 'Otwórz selektor kolorów',
    loading: 'Trwa ładowanie…',
    ok: 'OK',
    save: 'Zapisz',
    cancel: 'Anuluj',
    saving: 'Trwa zapisywanie…',
    addAnImage: 'Dodaj obraz',
    bold: 'Pogrubienie',
    italic: 'Kursywa',
    underline: 'Podkreślenie',
    strikethrough: 'Przekreślenie',
    addHyperink: 'Dodaj hiperłącze',
    options: 'Opcje',
    wrapTextAroundImage: 'Zawijaj tekst wokół obrazu?',
    imageOnLeft: 'Obraz po lewej stronie?',
    imageOnRight: 'Obraz po prawej stronie?',
    createThumbnail: 'Utworzyć miniaturę?',
    pixels: 'piksele',
    createSmallerVersion: 'Utwórz mniejszą wersję obrazu do wyświetlenia.  Ustaw szerokość w pikselach.',
    popupWindow: 'Okno podręczne?',
    linkToFullSize: 'Połącz z wersją pełnowymiarową obrazu w oknie podręcznym.',
    add: 'Dodaj',
    keepWindowOpen: 'Podczas przekazywania okno przeglądarki powinno być otwarte.',
    cancelUpload: 'Anuluj przekazywanie',
    pleaseSelectAFile: 'Wybierz plik obrazu',
    pleaseSpecifyAThumbnailSize: 'Określ rozmiar miniatury',
    thumbnailSizeMustBeNumber: 'Rozmiar miniatury musi być liczbą',
    addExistingImage: 'lub wstaw istniejący obraz',
    clickToEdit: 'Kliknij, aby edytować',
    sendingFriendRequest: 'Wysyłanie prośby do znajomego',
    requestSent: 'Żądanie zostało wysłane!',
    pleaseCorrectErrors: 'Popraw błędy',
    tagThis: 'Oznacz to',
    addOrEditYourTags: 'Dodaj lub edytuj znaczniki:',
    addYourRating: 'Dodaj ocenę:',
    separateMultipleTagsWithCommas: 'Wielokrotne znaczniki oddzielaj przecinkami, np. zimny, "nowa zelandia"',
    saved: 'Zapisano!',
    noo: 'NOWY',
    none: 'BRAK',
    joinNow: 'Przyłącz się teraz',
    join: 'Przyłącz się',
    youHaventRated: 'Ten element nie został jeszcze oceniony.',
    yourRatedThis: function(n) {
        switch(n) {
            case 1: return 'Przyznałeś temu elementowi 1 gwiazdkę. ';
            default: return 'Przyznałeś temu elementowi ' + n + ' gwiazdki. ';
        }
    },
    yourRatingHasBeenAdded: 'Twoja ocena została dodana.',
    thereWasAnErrorRating: 'Wystąpił błąd podczas oceniania tej treści.',
    yourTagsHaveBeenAdded: 'Twoje znaczniki zostały dodane.',
    thereWasAnErrorTagging: 'Wystąpił błąd podczas dodawania znaczników.',
    addToFavorites: 'Dodaj do Ulubionych',
    removeFromFavorites: 'Usuń z Ulubionych',
    nStarsOutOfM: function(n,m) {
        switch(n) {
            case 1: return '1 gwiadka na ' + m;
            default: return n + ' gwiazdki na ' + m;
        }
    },
    follow: 'Monituj',
    stopFollowing: 'Zatrzymaj monitowanie',
    pendingPromptTitle: 'Oczekiwanie na potwierdzenie członkostwa',
    youCanDoThis: 'Można to zrobić jak tylko administratorzy zatwierdzą członkostwo.',
    removeFriendTitle: 'Czy usunąć jako znajomego?',
    removeAsFriend: 'Usuń jako znajomego',
    removeFriendConfirm: 'Czy na pewno chcesz usunąć tę osobę jako znajomego?',
    nComments: function(n) {
    switch(n) {
        case 1: return '1 komentarz ';
        default: return n + ' Komentarze ';
    }
},
    showMap: 'Pokaż mapę',
    hideMap: 'Ukryj mapę',
    yourCommentMustBeApproved: 'Twój komentarz musi zostać zatwierdzony, zanim wszyscy będą mogli go zobaczyć.',
    locationNotFound: function(location) { return 'Nie znaleziono <em>' + location + '</em>. '; },
    confirmation: 'Potwierdzenie',
    uploadAFile: 'Przekaż plik',
    addExistingFile: 'lub wstaw istniejący plik',
    yourMessage: 'Twoja wiadomość',
    updateMessage: 'Aktualizuj wiadomość',
    updateMessageQ: 'Zaktualizować wiadomość?',
    goBack: 'Wróć',
    editYourTags: 'Dodaj swoje znaczniki',
    addTags: 'Dodaj znaczniki',
    editLocation: 'Edytuj lokalizację',
    editTypes: 'Edytuj typ zdarzenia',
    sendAnyway: 'Wyślij mimo wszystko',
    removeWords: 'Jeśli chcesz upewnić się, że Twoja wiadomość e-mail została doręczona, zalecamy powrót do wiadomości i zmienienie lub usunięcie następujących słów:',
    warningMessage: 'Prawdopodobnie w tej wiadomości e-mail są słowa, które mogą spowodować, że Twoja wiadomość e-mail trafi do folderu Wiadomości-śmieci.',
    errorMessage: 'W wiadomości e-mail znajduje się 6 lub więcej słów, przez które Twoja wiadomość e-mail może trafić do folderu Wiadomości-śmieci.',
    messageIsTooLong: function(n,m) { return 'Przepraszamy. Maksymalna liczba znaków wynosi '+m+'.' },
    pleaseEnterAComment: 'Wpisz komentarz',
    pleaseEnterAFileAddress: 'Wpisz adres pliku',
    pleaseEnterAWebsite: 'Wpisz adres internetowy'
});


dojo.evalObjPath('xg.video.nls', true);
dojo.lang.mixin(xg.video.nls, xg.index.i18n, {
    edit: 'Edytuj',
    display: 'Wyświetl',
    detail: 'Szczegół',
    player: 'Odtwarzacz',
    from: 'Od',
    show: 'Pokaż',
    videos: 'Pliki wideo',
    cancel: 'Anuluj',
    save: 'Zapisz',
    saving: 'Trwa zapisywanie…',
    deleteThisVideo: 'Usunąć ten plik wideo?',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Liczba znaków (' + n + ') przekracza maksimum (' + maximum + ') '; },
    weCouldNotLookUpAddress: function(address) { return 'Niestety nie można sprawdzić adresu "' + address + '". '; },
    approve: 'Zatwierdź',
    approving: 'Zatwierdzanie…',
    keepWindowOpenWhileApproving: 'Podczas zatwierdzania plików wideo okno przeglądarki powinno być otwarte.  Proces może potrwać kilka minut.',
    'delete': 'Usuń',
    deleting: 'Usuwanie…',
    keepWindowOpenWhileDeleting: 'Podczas usuwania plików wideo okno przeglądarki powinno być otwarte.  Proces może potrwać kilka minut.',
    pasteInEmbedCode: 'Wklej osadzony kod pliku wideo z innej lokalizacji.',
    pleaseSelectVideoToUpload: 'Wybierz plik wideo do przekazania.',
    embedCodeContainsMoreThanOneVideo: 'Osadzony kod zawiera więcej niż jeden plik wideo.  Sprawdź czy ma tylko jeden <object> i/lub znacznik <embed>.',
    embedCodeMissingTag: 'Osadzonemu kodowi brakuje &lt; embed&gt;  lub &lt; object&gt;  znacznik.',
    fileIsNotAMov: 'Plik nie jest plikiem . mov, . mpg, . mp4, . avi, . 3gp ani . wmv.  Czy mimo to chcesz go przekazać?',
    pleaseEnterAComment: 'Wprowadź komentarz.',
    youRatedVideoNStars: function(n) {
        switch(n) {
            case 1: return 'Przyznałeś temu plikowi wideo 1 gwiazdkę! ';
            default: return 'Przyznałeś temu plikowi wideo ' + n + ' gwiazdki! ';
        }
    },
    deleteThisComment: 'Czy usunąć ten komentarz?',
    embedHTMLCode: 'Osadzony kod HTML:',
    copyHTMLCode: 'Kopiuj kod HTML',
    addToOthers: 'Dodaj do katergorii Inne',
    directLink: 'Bezpośrednie łącze',
    addToMyspace: 'Dodaj na stronie MySpace',
    shareOnFacebook: 'Udostępnij w serwisie Facebook'
});


dojo.evalObjPath('xg.events.nls', true);
dojo.lang.mixin(xg.events.nls, xg.index.i18n, {
    messageSent: 'Wiadomość została wysłana!',
    chooseRecipient: 'Wybierz adresata.',
    areAttending: 'Uczestniczy',
    mightAttend: 'Może uczestniczyć',
    messageIsTooLong: function(n) { return 'Twoja wiadomość jest za długa. Użyj '+n+' lub mniej znaków.'; },
    sendMessageToGuests: 'Wyślij wiadomość do gości',
    sendMessageToGuestsThat: 'Wyślij wiadomość do gości, którzy:',
    haveNotYetRsvped: 'Jeszcze nie potwierdził',
    areNotAttending: 'Nie uczestniczy',
    pleaseChooseImage: 'Wybierz obraz dla zdarzenia',
    pleaseEnterAMessage: 'Wpisz wiadomość',
    pleaseEnterDescription: 'Wpisz opis zdarzenia',
    pleaseEnterLocation: 'Podaj miejsce zdarzenia',
    pleaseEnterTitle: 'Wpisz tytuł zdarzenia',
    pleaseEnterType: 'Wpisz przynajmniej jeden typ zdarzenia',
    send: 'Wyślij',
    sending: 'Wysyłanie...',
    thereHasBeenAnError: 'Wystąpił błąd',
    yourMessage: 'Twoja wiadomość',
    yourMessageHasBeenSent: 'Twoja wiadomość została wysłana.',
    yourMessageIsBeingSent: 'Twoja wiadomość została wysłana.'
});


dojo.evalObjPath('xg.notes.nls', true);
dojo.lang.mixin(xg.notes.nls, xg.index.i18n, {
    addNewNote: 'Dodaj nową notatkę',
    noteTitleTooLong: 'Tytuł notatki jest za długi',
    pleaseEnterNoteEntry: 'Wpisz notatkę',
    pleaseEnterNoteTitle: 'Wpisz tytuł notatki!'
});