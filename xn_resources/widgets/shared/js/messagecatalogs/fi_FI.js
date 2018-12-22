dojo.provide('xg.shared.messagecatalogs.fi_FI');

dojo.require('xg.index.i18n');

/**
 * Texts for the fi_FI
 */
// Use UTF-8 byte sequences instead of HTML entities, e.g., & instead of &amp;, â€¦ instead of &hellip;  [Jon Aquino 2007-01-10]

dojo.evalObjPath('xg.events.nls', true);
dojo.lang.mixin(xg.events.nls, xg.index.i18n, {
    pleaseChooseImage: 'Valitse tapahtumalle kuva',
    pleaseEnterAMessage: 'Kirjoita viesti',
    pleaseEnterDescription: 'Anna tapahtumalle kuvaus',
    pleaseEnterLocation: 'Anna tapahtumalle paikka',
    pleaseEnterTitle: 'Anna tapahtumalle nimi',
    pleaseEnterType: 'Anna tapahtumalle vähintään yksi tyyppi',
    send: 'Lähetä',
    sending: 'Lähetetään...',
    thereHasBeenAnError: 'Virhe tapahtui',
    yourMessage: 'Viestisi',
    yourMessageHasBeenSent: 'Viestisi on lähetetty.',
    yourMessageIsBeingSent: 'Viestisi on lähetetty'
});

dojo.evalObjPath('xg.feed.nls', true);
dojo.lang.mixin(xg.feed.nls, xg.index.i18n, {
    edit: 'Muokkaa',
    title: 'Nimi:',
    feedUrl: 'URL:',
    show: 'Näytä:',
    titles: 'Vain nimet',
    titlesAndDescriptions: 'Yksityiskohtainen näkymä',
    display: 'Näyttö',
    cancel: 'Peruuta',
    save: 'Tallenna',
    loading: 'Lataa...',
    items: 'nimikkeet'
});


dojo.evalObjPath('xg.gadgets.nls', true);
dojo.lang.mixin(xg.gadgets.nls, xg.index.i18n, {
    edit: 'Muokkaa',
    title: 'Nimi:',
    feedUrl: 'URL:',
    cancel: 'Peruuta',
    save: 'Tallenna',
    loading: 'Lataa…',
    removeGadget: 'Poista laite',
    findGadgetsInDirectory: 'Etsi laitteita laiteluettelosta'
});


dojo.evalObjPath('xg.forum.nls', true);
dojo.lang.mixin(xg.forum.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Merkkimäärä  (' + n + ') ylittää enimmäismäärän (' + maximum + ') '; },
    pleaseEnterFirstPost: 'Kirjoita keskustelun ensimmäinen viesti',
    pleaseEnterTitle: 'Anna keskustelulle nimi',
    save: 'Tallenna',
    cancel: 'Peruuta',
    yes: 'Kyllä',
    no: 'Ei',
    edit: 'Muokkaa',
    deleteCategory: 'Poista luokka',
    discussionsWillBeDeleted: 'Tämän luokan keskustelut poistetaan.',
    whatDoWithDiscussions: 'Mitä tämän luokan keskusteluille tehdään?',
    moveDiscussionsTo: 'Siirrä keskustelut:',
    moveToCategory: 'Siirrä luokkaan…',
    deleteDiscussions: 'Poista keskustelut',
    'delete': 'Poista',
    deleteReply: 'Poista vastaus',
    deleteReplyQ: 'Poistetaanko tämä vastaus?',
    deletingReplies: 'Poistaa vastauksia…',
    doYouWantToRemoveReplies: 'Haluatko myös poistaa tämän kommentin vastaukset?',
    pleaseKeepWindowOpen: 'Älä sulje selaimen ikkunaa käsittelyn aikana.  Se voi kestää muutaman minuutin.',
    from: 'Mistä',
    show: 'Näytä',
    discussions: 'keskustelut',
    discussionsFromACategory: 'Keskustelut luokasta…',
    display: 'Näyttö',
    items: 'kohteet',
    view: 'Katso'
});


dojo.evalObjPath('xg.uploader.nls', true);
dojo.lang.mixin(xg.uploader.nls, xg.index.i18n, {
    fileBrowserHeader: 'Oma tietokone',
    fileRoot: 'Oma tietokone',
    fileInformationHeader: 'Tiedot',
    uploadHeader: 'Ladattavat tiedostot',
    dragOutInstructions: 'Poista tiedostot vetämällä',
    dragInInstructions: 'Vedä tiedostot tähän',
    selectInstructions: 'Valitse tiedosto',
    files: 'Tiedostot',
    totalSize: 'Yhteenlaskettu koko',
    fileName: 'Nimi',
    fileSize: 'Koko',
    nextButton: 'Seuraava >',
    okayButton: 'OK',
    yesButton: 'Kyllä',
    noButton: 'Ei',
    uploadButton: 'Lataa',
    cancelButton: 'Peruuta',
    backButton: 'Palaa',
    continueButton: 'Jatka',
    uploadingLabel: 'Ladataan...',
    uploadingStatus: function(n, m) { return 'Ladataan ' + n + '  ' + m; },
    uploadingInstructions: 'Älä sulje selaimen ikkunaa niin kauan kuin lataaminen on käynnissä',
    uploadLimitWarning: function(n) { return 'Voit ladata ' + n + ' tiedostoa yhdellä kertaa. '; },
    uploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Olet lisännyt maksimimäärän tiedostoja. ';
            case 1: return 'Voit ladata 1 tiedoston lisää. ';
            default: return 'Voit ladata ' + n + ' tiedostoa lisää. ';
        }
    },
    iHaveTheRight: 'Minulla on oikeus ladata nämä tiedostot<a href="/main/authorization/termsOfService">Palveluehdot</a>',
    updateJavaTitle: 'Päivitä Java',
    updateJavaDescription: 'Joukkolataaminen vaatii uudemman Java version. Klikkaa ”Okay” ladataksesi Javan.',
    batchEditorLabel: 'Muokkaa kaikkien tietoja',
    applyThisInfo: 'Käytä tätä alla oleviin tiedostoihin',
    titleProperty: 'Nimi',
    descriptionProperty: 'Kuvaus',
    tagsProperty: 'Tagit',
    viewableByProperty: 'Tämän saa katsoa:',
    viewableByEveryone: 'Kaikki',
    viewableByFriends: 'Vain omat ystäväni',
    viewableByMe: 'Vain minä',
    albumProperty: 'Levy',
    artistProperty: 'Esittäjä',
    enableDownloadLinkProperty: 'Ota käyttöön latauslinkki',
    enableProfileUsageProperty: 'Salli ihmisten laittaa tämä kappale sivuilleen',
    licenseProperty: 'Lisenssi',
    creativeCommonsVersion: '3.0',
    selectLicense: '- Valitse lisenssi -',
    copyright: '© Kaikki oikeudet pidätetään',
    ccByX: function(n) { return 'Creative Commons Lisenssi ' + n; },
    ccBySaX: function(n) { return 'Creative Commons Share Alike-lisenssi  ' + n; },
    ccByNdX: function(n) { return 'Creative Commons No Derivates-lisenssi ' + n; },
    ccByNcX: function(n) { return 'Creative Commons lisenssi Ei-kaupalliseen käyttöön ' + n; },
    ccByNcSaX: function(n) { return 'Creative Commons Share Alike-lisenssi Ei kaupalliseen käyttöön ' + n; },
    ccByNcNdX: function(n) { return 'Creative Commons No Derivates- lisenssi Ei kaupalliseen käyttöön ' + n; },
    publicDomain: 'Julkinen alue',
    other: 'Muut',
    errorUnexpectedTitle: 'Hups!',
    errorUnexpectedDescription: 'Tapahtui virhe. Yritä uudestaan.',
    errorTooManyTitle: 'Liian monta tiedostoa',
    errorTooManyDescription: function(n) { return 'Olemme pahoillamme. Voit ladata vain ' + n + ' tiedostoa kerralla. '; },
    errorNotAMemberTitle: 'Ei sallittu',
    errorNotAMemberDescription: 'Olemme pahoillamme, mutta sinun tulee olla jäsen suorittaaksesi latauksia.',
    errorContentTypeNotAllowedTitle: 'Ei sallittu',
    errorContentTypeNotAllowedDescription: 'Olemme pahoillamme, mutta et voi ladata tällaista sisältöä.',
    errorUnsupportedFormatTitle: 'Hups!',
    errorUnsupportedFormatDescription: 'Olemme pahoillamme, mutta emme tue tätä tiedostotyyppiä.',
    errorUnsupportedFileTitle: 'Hups!',
    errorUnsupportedFileDescription: 'foo.exe ei ole tuettu muoto.',
    errorUploadUnexpectedTitle: 'Hups!',
    errorUploadUnexpectedDescription: function(file) {
        return file ?
            ('Seuraavan tiedoston kanssa on ongelmia ' + file + ' . Poista se listalta ennen kuin lataat loput tiedostoistasi.') :
            'Listalla ylimpänä olevan tiedoston kanssa on ongelmia. Poista se listalta ennen kuin lataat loput tiedostoistasi.';
    },
    cancelUploadTitle: 'Peruuta lataaminen ?',
    cancelUploadDescription: 'Oletko varma, että haluat peruuttaa jäljellä olevat lataukset?',
    uploadSuccessfulTitle: 'Lataaminen loppuun suoritettu',
    uploadSuccessfulDescription: 'Odota siirrämme sinut hetken päästä latauksiisi...',
    uploadPendingDescription: 'Tiedostojesi lataaminen on onnistunut ja ne odottavat hyväksymistäsi.',
    photosUploadHeader: 'Ladattavia kuvia',
    photosDragOutInstructions: 'Poista kuvia vetämällä',
    photosDragInInstructions: 'Vedä kuvat tänne',
    photosSelectInstructions: 'Valitse kuva',
    photosFiles: 'Kuvat',
    photosUploadingStatus: function(n, m) { return 'Lataa kuva ' + n + '  ' + m; },
    photosErrorTooManyTitle: 'Liian monta kuvaa',
    photosErrorTooManyDescription: function(n) { return 'Olemme pahoillamme, mutta voit ladata vain ' + n + ' kuvaa kerralla. '; },
    photosErrorContentTypeNotAllowedDescription: 'Olemme pahoillamme, mutta kuvien lataustoiminto on poissa käytöstä.',
    photosErrorUnsupportedFormatDescription: 'Olemme pahoillamme, mutta voit ladata vain .jpg, .gif tai .png muotoisia kuvia.',
    photosErrorUnsupportedFileDescription: function(n) { return n + ' ei ole  .jpg, .gif tai .png tiedosto.'; },
    photosBatchEditorLabel: 'Muotoile kaikkien kuvien tietoja',
    photosApplyThisInfo: 'Käytä tätä tietoa alla oleviin kuviin',
    photosErrorUploadUnexpectedDescription: function(file) {
        return file ?
            ('Seuraavan tiedoston kanssa on ongelmia ' + file + '. Poista se listalta ennen kuin lataat loput kuvistasi.') :
            'Listalla ylimpänä olevan kuvan kanssa on ongelmia. Poista se listalta ennen kuin lataat loput kuvistasi.';
    },
    photosUploadSuccessfulDescription: 'Odota siirrämme sinut hetken päästä kuviisi...',
    photosUploadPendingDescription: 'Kuviesi lataaminen onnistui ja ne odottavat hyväksyntääsi.',
    photosUploadLimitWarning: function(n) { return 'Sinä voit ladata ' + n + ' kuvaa kerralla. '; },
    photosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Sinä olet lisännyt maksimimäärän kuvia. ';
            case 1: return 'Sinä voit ladata vielä 1 kuvan. ';
            default: return 'Sinä voit ladata ' + n + ' kuvaa lisää. ';
        }
    },
    photosIHaveTheRight: 'Minulla on oikeus näiden kuvien lataamiseen <a href="/main/authorization/termsOfService">Palvelun ehdot</a>',
    videosUploadHeader: 'Ladattavat videot',
    videosDragInInstructions: 'Vedä videot tänne',
    videosDragOutInstructions: 'Poista videot vetämällä',
    videosSelectInstructions: 'Valitse video',
    videosFiles: 'Videot',
    videosUploadingStatus: function(n, m) { return 'Lataa videota ' + n + '  ' + m; },
    videosErrorTooManyTitle: 'Liian monta videota',
    videosErrorTooManyDescription: function(n) { return 'Olemme pahoillamme, mutta voit ladata vain ' + n + ' videota kerralla. '; },
    videosErrorContentTypeNotAllowedDescription: 'Olemme pahoillamme, mutta videoiden lataustoiminto on poissa käytöstä.',
    videosErrorUnsupportedFormatDescription: 'Olemme pahoillamme, mutta voit ladata vain .avi, .mov, .mp4, .wmv tai .mpg muotoisia videoita.',
    videosErrorUnsupportedFileDescription: function(x) { return x + ' ei ole .avi, .mov, .mp4, .wmv tai .mpg tiedosto.'; },
    videosBatchEditorLabel: 'Muokkaa kaikkien videoiden tietoja',
    videosApplyThisInfo: 'Käytä tätä tietoa alla oleviin videoihin',
    videosErrorUploadUnexpectedDescription: function(file) {
        return file ?
            ('Seuraavan tiedoston kanssa on ongelmia ' + file + ' . Poista se listalta ennen kuin lataat loput videoistasi.') :
            'Listalla ylimpänä olevan videon kanssa on ongelmia. Poista se listalta ennen kuin lataat loput videoistasi.';
    },
    videosUploadSuccessfulDescription: 'Odota siirrämme sinut videoihisi...',
    videosUploadPendingDescription: 'Videosi on onnistuneesti ladattu ja odottavat hyväksymistäsi.',
    videosUploadLimitWarning: function(n) { return 'Sinä voit ladata ' + n + ' videota kerralla. '; },
    videosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Olet lisännyt maksimimäärän videoita. ';
            case 1: return 'Sinä voit ladata vielä 1 videon. ';
            default: return 'Sinä voit ladata vielä ' + n + ' videota. ';
        }
    },
    videosIHaveTheRight: 'Minulla on oikeus ladata nämä videot <a href="/main/authorization/termsOfService">Palveluehdot</a>',
    musicUploadHeader: 'Ladattavat kappaleet',
    musicTitleProperty: 'Kappaleen nimi',
    musicDragOutInstructions: 'Poista kappaleita vetämällä',
    musicDragInInstructions: 'Vedä kappaleet tänne',
    musicSelectInstructions: 'Valitse kappale',
    musicFiles: 'Kappaleet',
    musicUploadingStatus: function(n, m) { return 'Ladataan kappaletta ' + n + '  ' + m; },
    musicErrorTooManyTitle: 'Liian monta kappaletta',
    musicErrorTooManyDescription: function(n) { return 'Olemme pahoillamme, mutta voit ladata vain ' + n + ' kappaletta kerralla. '; },
    musicErrorContentTypeNotAllowedDescription: 'Olemme pahoillamme, mutta musiikin lataustoiminto on poissa käytöstä.',
    musicErrorUnsupportedFormatDescription: 'Olemme pahoillamme, mutta voit ladata vain .mp3 muotoisia kappaleita.',
    musicErrorUnsupportedFileDescription: function(x) { return x + ' ei ole .mp3 tiedosto.'; },
    musicBatchEditorLabel: 'Muotoile kaikkien kappaleiden tietoja',
    musicApplyThisInfo: 'Käytä tätä tietoa alla oleviin kappaleisiin',
    musicErrorUploadUnexpectedDescription: function(file) {
        return file ?
            ('Seuraavan tiedoston kanssa on ongelmia ' + file + ' . Poista se listalta ennen kuin lataat loput kappaleistasi.') :
            'Listalla ylimpänä olevan kappaleen kanssa on ongelmia. Poista se listalta ennen kuin lataat loput kappaleista.';
    },
    musicUploadSuccessfulDescription: 'Odota hetki kunnes siirrämme sinut musiikkikappaleisiisi...',
    musicUploadPendingDescription: 'Kappaleesi on onnistuneesti ladattu ja odottavat hyväksyntääsi.',
    musicUploadLimitWarning: function(n) { return 'Sinä voit ladata ' + n + ' kappaletta kerralla. '; },
    musicUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Sinä olet ladannut maksimimäärän kappaleita. ';
            case 1: return 'Sinä voit ladata vielä 1 kappaleen. ';
            default: return 'Sinä voit ladata vielä ' + n + ' kappaletta. ';
        }
    },
    musicIHaveTheRight: 'Minulla on oikeus ladata nämä kappaleet <a href="/main/authorization/termsOfService">Palveluehdot</a>'
});


dojo.evalObjPath('xg.groups.nls', true);
dojo.lang.mixin(xg.groups.nls, xg.index.i18n, {
    pleaseChooseAName: 'Anna ryhmällesi nimi.',
    pleaseChooseAUrl: 'Valitse ryhmällesi verkko-osoite..',
    urlCanContainOnlyLetters: 'URI voi sisältää vain kirjaimia ja numeroita (ei välilyöntejä).',
    descriptionTooLong: function(n, maximum) { return 'Ryhmän kuvaus  (' + n + ') ylittää maksimin (' + maximum + ') '; },
    nameTaken: 'Valitsemasi nimi on jo valitettavasti käytössä.  Valitse toinen nimi.',
    urlTaken: 'Valitsemasi URI on jo käytössä.  Valitse toinen URI.',
    whyNot: 'Miksi ei?',
    groupCreatorDetermines: function(href) { return 'Ryhmän perustaja päättää kuka saa liittyä.  Jos pääsysi on estetty virheellisesti  <a ' + href + '>ota yhteys ryhmän perustajaan</a> '; },
    edit: 'Muokkaa',
    from: 'Mistä',
    show: 'Näytä',
    groups: 'ryhmät',
    pleaseEnterName: 'Anna nimesi',
    pleaseEnterEmailAddress: 'Anna sähköpostiosoitteesi',
    xIsNotValidEmailAddress: function(x) { return x + ' is not a valid email address'; },
    save: 'Tallenna',
    cancel: 'Peruuta'
});


dojo.evalObjPath('xg.html.nls', true);
dojo.lang.mixin(xg.html.nls, xg.index.i18n, {
    contentsTooLong: function(maximum) { return 'Sisältö on liian pitkä. Käytä vähemmän kuin ' + maximum + ' merkkiä. '; },
    edit: 'Muokkaa',
    save: 'Tallenna',
    cancel: 'Peruuta',
    saving: 'Tallentaa…',
    addAWidget: function(url) { return '<a href="' + url + '">Lisää widget</a> tähän tekstiruutuun '; }
});


dojo.evalObjPath('xg.index.nls', true);
dojo.lang.mixin(xg.index.nls, xg.index.i18n, {
    sendInvitation: 'Lähetä kutsu',
    sendInvitationToNFriends: function(n) {
        switch(n) {
            case 1: return 'Haluatko lähettää kutsun 1 ystävälle? ';
            default: return 'Haluatko lähettää kutsun ' + n + ' ystävälle? ';
        }
    },
    yourMessageOptional: '<label>Viestisi</label> (Valinnainen)',
    pleaseChoosePeople: 'Valitse kutsuttavat henkilöt.',
    pleaseEnterEmailAddress: 'Anna sähköpostiosoitteesi.',
    pleaseEnterPassword: function(emailAddress) { return 'Anna salasanasi osoitteelle ' + emailAddress + '. '; },
    sorryWeDontSupport: 'Valitettavasti emme tue sähköpostiosoitteesi osoitteistoa.  Klikkaa \'Email Application\' käyttääksesi tietokoneesi osoitteita.',
    pleaseSelectSecondPart: 'Valitse sähköpostiosoitteesi loppuosa, esimerkiksi gmail. com.',
    atSymbolNotAllowed: 'Varmista, ettei @ merkki ole osoitteen ensimmäisessä osassa.',
    resetTextQ: 'Palautetaanko teksti?',
    resetTextToOriginalVersion: 'Oletko varma, että haluat palauttaa tekstisi alkuperäisen version?  Kaikki tekemäsi muutokset katoavat.',
    changeQuestionsToPublic: 'Muutetaanko kysymykset julkisiksi?',
    changingPrivateQuestionsToPublic: 'Yksityisten kysymysten muuttaminen julkisiksi paljastaa kaikkien jäsenten vastaukset.  Oletko varma?',
    youHaveUnsavedChanges: 'Et ole tallentanut muutoksia.',
    pleaseEnterASiteName: 'Anna sosiaaliselle verkostolle nimi, esimerkiksi Pienten Klovnien Kerho',
    pleaseEnterShorterSiteName: 'Anna lyhyempi nimi (max 64 merkkiä)',
    pleaseEnterShorterSiteDescription: 'Anna lyhyempi kuvaus (max 250 merkkiä)',
    siteNameHasInvalidCharacters: 'Nimessä on epäkelpoja merkkejä',
    thereIsAProblem: 'Tietojesi kanssa on ongelma',
    thisSiteIsOnline: 'Sosiaalinen verkosto on Online',
    onlineSiteCanBeViewed: '<strong>Online</strong> - Verkosto näkyy yksityisyysasetustesi mukaisesti.',
    takeOffline: 'Siirry Offline-tilaan',
    thisSiteIsOffline: 'Sosiaalinen verkosto on Offline',
    offlineOnlyYouCanView: '<strong>Offline</strong> - Vain sinä näet sosiaalisen verkoston.',
    takeOnline: 'Siirry Online',
    themeSettings: 'Teema-asetukset',
    addYourOwnCss: 'Erikoishaku',
    error: 'Virhe',
    pleaseEnterTitleForFeature: function(displayName) { return 'Anna nimi  ' + displayName + ' ominaisuudelle '; },
    thereIsAProblemWithTheInformation: 'Syötetyssä tiedossa on ongelma.',
    photos: 'Kuvat',
    videos: 'Videot',
    pleaseEnterTheChoicesFor: function(questionTitle) { return 'Anna vaihtoehdot  "' + questionTitle + '" esimerkiksi Patikointi, Lukeminen, Shoppailu '; },
    pleaseEnterTheChoices: 'Anna vaihtoehdot esimerkiksi Patikointi, Lukeminen, Shoppailu',
    shareWithFriends: 'Jaa ystävien kanssa',
    email: 'sähköposti',
    separateMultipleAddresses: 'Erottele useammat osoitteet pilkuilla',
    subject: 'Aihe',
    message: 'Viesti',
    send: 'Lähetä',
    cancel: 'Peruuta',
    pleaseEnterAValidEmail: 'Anna kelvollinen sähköpostiosoite',
    go: 'Siirry',
    areYouSureYouWant: 'Oletko varma, että haluat suorittaa toiminnon?',
    processing: 'Käsittelee…',
    pleaseKeepWindowOpen: 'Älä sulje selaimen ikkunaa käsittelyn aikana.  Se voi kestää muutaman minuutin.',
    complete: 'Valmis!',
    processIsComplete: 'Toiminto on loppuunsuoritettu.',
    ok: 'OK',
    body: 'Leipäteksti',
    pleaseEnterASubject: 'Anna aihe',
    pleaseEnterAMessage: 'Kirjoita viesti',
    thereHasBeenAnError: 'On tapahtunut virhe',
    fileNotFound: 'Tiedostoa ei löydy',
    pleaseProvideADescription: 'Anna kuvaus',
    pleaseEnterYourFriendsAddresses: 'Kirjoita ystäviesi osoitteet tai Ning  ID-tunnukset',
    pleaseEnterSomeFeedback: 'Anna palautetta',
    title: 'Nimi:',
    setAsMainSiteFeature: 'Aseta pääominaisuudeksi',
    thisIsTheMainSiteFeature: 'tämä on pääominaisuus',
    customized: 'Mukautettu',
    copyHtmlCode: 'Kopioi HTML-koodi',
    playerSize: 'Soittimen koko',
    selectSource: 'Valitse lähde',
    myAlbums: 'Omat albumit',
    myMusic: 'Oma musiikki',
    myVideos: 'Omat videot',
    showPlaylist: 'Näytä soittolista',
    change: 'Vaihda',
    changing: 'Vaihtaa..',
    changePrivacy: 'Muutetaanko yksityisyysasetus?',
    keepWindowOpenWhileChanging: 'Älä sulje selaimen ikkunaa ennen kuin yksityisyysasetukset on muutettu.  Käsittely voi kestää muutaman minuutin.',
    showingNFriends: function(n, searchString) {
        switch(n) {
            case 1: return 'Löytänyt 1 sopivan ystävän "' + searchString + '". <a href="#">Näytä kaikki</a> ';
            default: return 'Löytänyt ' + n + ' sopivaa ystävää "' + searchString + '". <a href="#">Näytä kaikki</a> ';
        }
    },
    sendMessage: 'Lähetä viesti',
    sendMessageToNFriends: function(n) {
        switch(n) {
            case 1: return 'Haluatko lähettää viestin 1 ystävälle? ';
            default: return 'Haluatko lähettää viestin ' + n + ' ystävälle? ';
        }
    },
    invitingNFriends: function(n) {
        switch(n) {
            case 1: return 'Kutsuu 1 ystävää… ';
            default: return 'Kutsuu ' + n + ' ystävää… ';
        }
    },
    nFriends: function(n) {
        switch(n) {
            case 1: return '1 ystävä… ';
            default: return n + ' ystävät… ';
        }
    },
    sendingMessageToNFriends: function(n) {
        switch(n) {
            case 1: return 'Lähettää viestiä 1 ystävälle… ';
            default: return 'Lähettää viestiä ' + n + ' ystävälle… ';
        }
    },
    noPeopleSelected: 'Ei henkilöitä valittuna',
    sorryWeDoNotSupport: 'Valitettavasti emme tue sähköpostiosoitteesi osoitekirjaa. Yritä klikata \'Osoitekirjasovellus\' alla käyttääksesi tietokoneesi osoitteita.',
    pleaseChooseFriends: 'Valitse ensin ystävät ennen kuin lähetät viestin.',
    htmlNotAllowed: 'HTML ei ole sallittu',
    noFriendsFound: 'Yksikään ystävä ei vastaa hakukriteeriäsi.',
    addingInstructions: 'Älä sulje selaimen ikkunaa niin kauan kuin sisältöä lisätään.',
    addingLabel: 'Lisää...',
    cannotKeepFiles: 'Sinun on valittava tiedostosi uudelleen, jos haluat näyttää lisää vaihtoehtoja.  Haluatko jatkaa?',
    done: 'Valmis',
    looksLikeNotImage: 'Vähintään yksi tiedosto ei ole muotoa .jpg, .gif, tai .png.  Haluatko kuitenkin yrittää lataamista?',
    looksLikeNotMusic: 'Valitsemasi tiedosto ei ole .mp3-muotoinen.  Haluatko kuitenkin yrittää lataamista?',
    looksLikeNotVideo: 'Valitsemasi tiedosto ei ole muotoa .mov, .mpg, .mp4, .avi, .3gp tai .wmv.  Haluatko kuitenkin yrittää lataamista?',
    messageIsTooLong: function(n) { return 'Viestisi on liian pitkä.  Käytä enintään '+n+' merkkiä.'; },
    pleaseSelectPhotoToUpload: 'Valitse ladattava kuva.',
    processingFailed: 'Käsittely epäonnistui.  Yritä uudelleen myöhemmin.',
    selectOrPaste: 'Sinun on valittava video tai liitettävä koodi \'embed\'.',
    selectOrPasteMusic: 'Sinun on valittava kappale tai liitettävä URL-osoite',
    sendingLabel: 'Lähetetään...',
    thereWasAProblem: 'Ongelma esiintyi siältöäsi lisättäessä.  Yritä uudelleen myöhemmin.',
    uploadingInstructions: 'Älä sulje selaimen ikkunaa niin kauan kuin lataaminen on käynnissä',
    uploadingLabel: 'Ladataan...',
    youNeedToAddEmailRecipient: 'Sinun on lisättävä sähköpostin vastaanottaja.',
    yourMessage: 'Viestisi',
    yourMessageIsBeingSent: 'Viestisi on lähetetty',
    yourSubject: 'Aiheesi'
});


dojo.evalObjPath('xg.music.nls', true);
dojo.lang.mixin(xg.music.nls, xg.index.i18n, {
    play: 'toistaa',
    pleaseSelectTrackToUpload: 'Valitse ladattava kappale.',
    pleaseEnterTrackLink: 'Anna kappaleen URL.',
    thereAreUnsavedChanges: 'Muutoksia ei ole tallennettu.',
    autoplay: 'Automaattinen toisto',
    showPlaylist: 'Näytä soittolista',
    playLabel: 'Toista',
    url: 'URL',
    rssXspfOrM3u: 'rss, xspf, tai m3u',
    save: 'Tallenna',
    cancel: 'Peruuta',
    shufflePlaylist: 'Sekoita soittolista',
    edit: 'Muokkaa',
    fileIsNotAnMp3: 'Joku tiedostoista ei ole MP3-muotoa.  Yritetäänkö silti lataamista?',
    entryNotAUrl: 'Yksi syötetyistä tiedoista ei ole URL.  Varmista, että kaikki syötettävät tiedot alkavat  <kbd>http://</kbd>'
});

dojo.evalObjPath('xg.notes.nls', true);
dojo.lang.mixin(xg.notes.nls, xg.index.i18n, {
    addNewNote: 'Lisää uusi viesti',
    noteTitleTooLong: 'Viestin otsikko on liian pitkä',
    pleaseEnterNoteEntry: 'Kirjoita viestimerkintä',
    pleaseEnterNoteTitle: 'Anna viestille otsikko!'
});

dojo.evalObjPath('xg.page.nls', true);
dojo.lang.mixin(xg.page.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Merkkimäärä  (' + n + ') ylittää enimmäismäärän (' + maximum + ') '; },
    pleaseEnterContent: 'Kirjoita sivun sisältö',
    pleaseEnterTitle: 'Anna sivulle nimi',
    pleaseEnterAComment: 'Kirjoita kommentti',
    deleteThisComment: 'Haluatko varmasti poistaa kommentin?',
    save: 'Tallenna',
    cancel: 'Peruuta',
    discussionTitle: 'Sivun otsikko:',
    tags: 'Tagit:',
    edit: 'Muokkaa',
    close: 'Sulje',
    displayPagePosts: 'Näytä sivun viestit',
    thereIsAProblem: 'Ongelma esiintyi tietoihisi liittyen'
});


dojo.evalObjPath('xg.photo.nls', true);
dojo.lang.mixin(xg.photo.nls, xg.index.i18n, {
    randomOrder: 'Satunnainen järjestys',
    untitled: 'Nimetön',
    photos: 'Kuvat',
    edit: 'Muokkaa',
    photosFromAnAlbum: 'Albumit',
    show: 'Näytä',
    rows: 'rivit',
    cancel: 'Peruuta',
    save: 'Tallenna',
    deleteThisPhoto: 'Poistetaanko tämä kuva?',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Merkkimäärä  (' + n + ') ylittää enimmäismäärän (' + maximum + ') '; },
    weCouldNotLookUpAddress: function(address) { return 'Valitettavasti emme voineet hakea osoitetta "' + address + '". '; },
    pleaseSelectPhotoToUpload: 'Valitse ladattava kuva.',
    pleaseEnterAComment: 'Lisää kommentti.',
    addToExistingAlbum: 'Lisää olemassaolevaan albumiin',
    addToNewAlbumTitled: 'Lisää uuteen albumiin nimeltä…',
    deleteThisComment: 'Poistetaanko kommentti?',
    importingNofMPhotos: function(n,m) { return 'Tuo <span id="currentP">' + n + '</span> of ' + m + ' kuvia. '},
    starting: 'Käynnistyy…',
    done: 'Valmis!',
    from: 'Mistä',
    display: 'Näyttö',
    takingYou: 'Näyttää sinulle kuvat…',
    anErrorOccurred: 'Valitettavasti on tapahtunut virhe.  Ilmoita ongelmasta käyttämällä sivun alareunan linkkiä.',
    weCouldntFind: 'Kuvia ei löytynyt!  Mikset kokeilisi jotain muuta asetusta?'
});


dojo.evalObjPath('xg.activity.nls', true);
dojo.lang.mixin(xg.activity.nls, xg.index.i18n, {
    edit: 'Muokkaa',
    show: 'Näytä',
    events: 'tapahtumat',
    setWhatActivityGetsDisplayed: 'Valitse mikä aktiviteetti näytetään',
    save: 'Tallenna',
    cancel: 'Peruuta'
});


dojo.evalObjPath('xg.profiles.nls', true);
dojo.lang.mixin(xg.profiles.nls, xg.index.i18n, {
    pleaseEnterValueForPost: 'Lisää viestin arvo',
    pleaseProvideAValidDate: 'Anna kelvollinen päivämäärä',
    uploadAFile: 'Lataa tiedosto',
    pleaseEnterUrlOfLink: 'Anna linkin URL :',
    pleaseEnterTextOfLink: 'Minkä tekstin haluat linkittää?',
    edit: 'Muokkaa',
    recentlyAdded: 'Viimeksi lisätyt',
    featured: 'Ominaisuudet',
    iHaveRecentlyAdded: 'Viimeksi lisätyt',
    fromTheSite: 'Sosiaalisesta verkostosta',
    cancel: 'Peruuta',
    save: 'Tallenna',
    loading: 'Lataa...',
    addAsFriend: 'Lisää ystäväksi',
    requestSent: 'Pyyntö lähetetty!',
    sendingFriendRequest: 'Lähettää pyyntöä ystävälle',
    thisIsYou: 'Tämä olet sinä!',
    isYourFriend: 'on ystäväsi',
    isBlocked: 'on estetty',
    pleaseEnterAComment: 'Kirjoita kommentti',
    pleaseEnterPostBody: 'Kirjoita jotain viestin tekstiksi',
    pleaseSelectAFile: 'Valitse tiedosto',
    pleaseEnterChatter: 'Kirjoita jotain kommentiksi',
    toggleBetweenHTML: 'Näytä/piilota HTML-koodi',
    attachAFile: 'Liitä tiedosto',
    addAPhoto: 'Lisää kuva',
    insertALink: 'Lisää linkki.',
    changeTextSize: 'Muuta tekstin koko',
    makeABulletedList: 'Käytä luettelomerkkejä',
    makeANumberedList: 'Tee numeroitu lista',
    crossOutText: 'Korosta teksti',
    underlineText: 'Alleviivaa teksti',
    italicizeText: 'Kursivoi teksti',
    boldText: 'Lihavoi teksti',
    letMeApproveChatters: 'Haluatko hyväksyä kommentit ennen julkaisua?',
    noPostChattersImmediately: 'Ei - julkaise kommentit heti',
    yesApproveChattersFirst: 'Kyllä - kommenttien hyväksyntä ensin',
    yourCommentMustBeApproved: 'Kommenttisi täytyy tulla hyväksytyksi ennen kuin kaikki voivat nähdä sen.',
    reallyDeleteThisPost: 'Poistetaanko tämä viesti?',
    commentWall: 'Kommenttitaulu',
    commentWallNComments: function(n) {
        switch(n) {
            case 1: return 'Kommenttitaulu (1 kommentti) ';
            default: return 'Kommenttitaulu (' + n + ' kommenttia) ';
        }
    },
    display: 'Näyttö',
    from: 'Mistä',
    show: 'Näytä',
    rows: 'rivit',
    posts: 'artikkelit'
});


dojo.evalObjPath('xg.shared.nls', true);
dojo.lang.mixin(xg.shared.nls, xg.index.i18n, {
    uploadAPhoto: 'Lataa valokuva',
    uploadAnImage: 'Lataa kuva',
    uploadAPhotoEllipsis: 'Lataa valokuva…',
    useExistingImage: 'Käytä nykyistä kuvaa:',
    existingImage: 'Nykyinen kuva',
    useThemeImage: 'Käytä teemakuvaa:',
    themeImage: 'Teemakuva',
    noImage: 'Ei kuvaa',
    uploadImageFromComputer: 'Lataa kuva tietokoneeltasi',
    tileThisImage: 'Asettele tämä kuva',
    done: 'Valmis',
    currentImage: 'Nykyinen kuva',
    pickAColor: 'Valitse väri…',
    openColorPicker: 'Avaa värinpoimija',
    loading: 'Lataa...',
    ok: 'OK',
    save: 'Tallenna',
    cancel: 'Peruuta',
    saving: 'Tallentaa…',
    addAnImage: 'Lisää kuva',
    bold: 'Lihavointi',
    italic: 'Kursivointi',
    underline: 'Alleviivaus',
    strikethrough: 'Yliviivaus',
    addHyperink: 'Lisää hyperlinkki',
    options: 'asetukset',
    wrapTextAroundImage: 'Sijoitetaanko teksti kuvan ympärille?',
    imageOnLeft: 'Kuva vasemmalle?',
    imageOnRight: 'Kuva oikealle?',
    createThumbnail: 'Pienennetäänkö kuva?',
    pixels: 'kuvapisteet',
    createSmallerVersion: 'Tee kuvasta pienempi näyttöversio.  Anna leveys kuvapisteinä.',
    popupWindow: 'Ponnahdusikkuna?',
    linkToFullSize: 'Linkitä kuva alkuperäisessä koossa ponnahdusikkunaan.',
    add: 'Lisää',
    keepWindowOpen: 'Pidä selaimen ikkuna auki niin kauan kuin lataaminen jatkuu.',
    cancelUpload: 'Peruuta lataaminen',
    pleaseSelectAFile: 'Valitse kuvatiedosto',
    pleaseSpecifyAThumbnailSize: 'Määrittele pienennetyn kuvan koko.',
    thumbnailSizeMustBeNumber: 'Pienennetyn kuvan koon tulee olla numero',
    addExistingImage: 'tai lisää nykyinen kuva',
    clickToEdit: 'Klikkaa muokataksesi',
    sendingFriendRequest: 'Lähettää pyyntöä ystävälle',
    requestSent: 'Pyyntö lähetetty!',
    pleaseCorrectErrors: 'Korjaa nämä virheet.',
    tagThis: 'Lisää tähän tag',
    addOrEditYourTags: 'Lisää tai muokkaa tagejasi:',
    addYourRating: 'Lisää arvosana:',
    separateMultipleTagsWithCommas: 'Erottele moniosaiset tagit lainausmerkeillä esimerkiksi "new zealand"',
    saved: 'Tallennettu!',
    noo: 'UUSI',
    none: 'TYHJÄ',
    joinNow: 'Liity nyt',
    join: 'Liity',
    youHaventRated: 'Et ole arvostellut tätä vielä.',
    yourRatedThis: function(n) {
        switch(n) {
            case 1: return 'Olet arvostellut tämän 1 tähdellä. ';
            default: return 'Olet arvostellut tämän ' + n + ' tähdellä. ';
        }
    },
    yourRatingHasBeenAdded: 'Arvostelusi on lisätty.',
    thereWasAnErrorRating: 'Tämän sisällön arvostelussa tapahtui virhe.',
    yourTagsHaveBeenAdded: 'Tagisi on lisätty.',
    thereWasAnErrorTagging: 'Tagien lisäämisessä tapahtui virhe.',
    addToFavorites: 'Lisää suosikkeihin',
    removeFromFavorites: 'Poista suosikeista',
    nStarsOutOfM: function(n,m) {
        switch(n) {
            case 1: return '1 tähti  ' + m;
            default: return n + ' tähteä  ' + m;
        }
    },
    follow: 'Seuraa',
    stopFollowing: 'Pysäytä seuranta',
    pendingPromptTitle: 'Jäsenyys odottaa hyväksymistä',
    youCanDoThis: 'Tämä toiminto on mahdollinen vasta kun järjestelmänvalvojat ovat hyväksyneet jäsenyytesi.',
    pleaseEnterAComment: 'Kirjoita kommentti',
    pleaseEnterAFileAddress: 'Anna tiedoston osoite',
    pleaseEnterAWebsite: 'Anna Internet-sivun osoite'
});


dojo.evalObjPath('xg.video.nls', true);
dojo.lang.mixin(xg.video.nls, xg.index.i18n, {
    edit: 'Muokkaa',
    display: 'Näyttö',
    detail: 'Yksityiskohta',
    player: 'Soitin',
    from: 'Mistä',
    show: 'Näytä',
    videos: 'videot',
    cancel: 'Peruuta',
    save: 'Tallenna',
    saving: 'Tallentaa…',
    deleteThisVideo: 'Poistetaanko tämä video?',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Merkkimäärä  (' + n + ') ylittää enimmäismäärän (' + maximum + ') '; },
    weCouldNotLookUpAddress: function(address) { return 'Valitettavasti emme voineet hakea osoitetta "' + address + '". '; },
    approve: 'Hyväksy',
    approving: 'Hyväksyy…',
    keepWindowOpenWhileApproving: 'Älä sulje selaimen ikkunaa kun videoita hyväksytään.  Käsittely voi kestää muutaman minuutin.',
    'delete': 'Poista',
    deleting: 'Poistaa…',
    keepWindowOpenWhileDeleting: 'Älä sulje selaimen ikkunaa kun videoita poistetaan.  Käsittely voi kestää muutaman minuutin.',
    pasteInEmbedCode: 'Liitä upotettu videon koodi toiselta sivustolta.',
    pleaseSelectVideoToUpload: 'Valitse ladattava video.',
    embedCodeContainsMoreThanOneVideo: 'Upotettu koodi sisältää useamman kuin yhden videon.  Varmista, että sillä on vain yksi  <object> ja/tai <embed> tag.',
    embedCodeMissingTag: 'Upotetusta koodista puuttuu &lt; embed&gt;  tai &lt; object&gt;  tag.',
    fileIsNotAMov: 'Tiedosto ei näytä olevan .. mov, . mpg, . mp4, . avi, . 3gp tai wmv.  Yritetäänkö silti lataamista?',
    pleaseEnterAComment: 'Lisää kommentti.',
    youRatedVideoNStars: function(n) {
        switch(n) {
            case 1: return 'Arvostelit tämän videon 1 tähdellä! ';
            default: return 'Arvostelit tämän videon ' + n + ' tähdellä! ';
        }
    },
    deleteThisComment: 'Poistetaanko kommentti?',
    embedHTMLCode: 'HTML upotettu koodi:',
    copyHTMLCode: 'Kopioi HTML-koodi'
});