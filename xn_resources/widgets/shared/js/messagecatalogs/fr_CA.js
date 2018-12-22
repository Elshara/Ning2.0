dojo.provide('xg.shared.messagecatalogs.fr_CA');

dojo.require('xg.index.i18n');

/**
 * Texts for the French (Canada) locale.
 */
// Use UTF-8 byte sequences instead of HTML entities, e.g., & instead of &amp;, … instead of &hellip;  [Jon Aquino 2007-01-10]

dojo.evalObjPath('xg.index.nls', true);
dojo.lang.mixin(xg.index.nls, xg.index.i18n, {
    myVideos: 'Mes vidéos',
    showingNFriends: function(n, searchString) {
        switch(n) {
            case 1: return 'Montrant 1 ami correspondant "' + searchString + '". <a href="#">Montrer tout le monde</a> ';
            default: return 'Montrant ' + n + ' amis correspondants "' + searchString + '". <a href="#"> Montrer tout le monde</a> ';
        }
    },
    sendInvitation: 'Envoyer Invitation',
    sendMessage: 'Envoyer Message',
    sendInvitationToNFriends: function(n) {
        switch(n) {
            case 1: return 'Envoyer une invitation à 1 ami? ';
            default: return 'Envoyer une invitation à ' + n + ' amis? ';
        }
    },
    sendMessageToNFriends: function(n) {
        switch(n) {
            case 1: return 'Envoyer le message à 1 ami? ';
            default: return 'Envoyer une message à ' + n + ' amis? ';
        }
    },
    invitingNFriends: function(n) {
        switch(n) {
            case 1: return 'Invitation d’1 ami… ';
            default: return 'Invitation de ' + n + ' amis… ';
        }
    },
    nFriends: function(n) {
        switch(n) {
            case 1: return '1 ami… ';
            default: return n + ' des amis… ';
        }
    },
    sendingMessageToNFriends: function(n) {
        switch(n) {
            case 1: return 'Envoi de message à 1 ami… ';
            default: return 'Envoi de message à ' + n + ' amis… ';
        }
    },
    yourMessageOptional: '<label>Votre message</label> (au choix)',
    pleaseChoosePeople: 'Veuillez choisir des personnes à inviter.',
    noPeopleSelected: 'Aucune personne sélectionnée',
    pleaseEnterEmailAddress: 'Veuillez entrer votre adresse courriel.',
    pleaseEnterPassword: function(emailAddress) { return 'Veuillez entrer votre mot de passe de ' + emailAddress + '. '; },
    sorryWeDoNotSupport: 'Désolé, nous ne supportons pas le carnet d’adresses web pour votre courriel. Essayer de cliquer sur « Gestionnaire d\'adresses courriels » plus bas pour vous servir des adresses de votre ordinateur.',
    pleaseSelectSecondPart: 'Veuillez sélectionner la deuxième partie de votre adresse courriel, par exemple, gmail.com.',
    atSymbolNotAllowed: 'Veuillez vous assurer que le symbole @ n’est pas dans la première partie de l\'adresse courriel.',
    resetTextQ: 'Remettre le texte à l’état initial?',
    resetTextToOriginalVersion: 'Êtes-vous sûr que vous souhaitez ramener tout votre texte à sa version initiale? Toutes vos modifications seront perdues.',
    changeQuestionsToPublic: 'Modifier les questions au public?',
    changingPrivateQuestionsToPublic: 'Modifier les questions du privé au public exposera toutes les réponses des membres. Êtes-vous sûr?',
    youHaveUnsavedChanges: 'Vous avez des modifications non enregistrées.',
    pleaseEnterASiteName: 'Veuillez entrer un nom pour le réseau social, par exemple. Tiny Clown Club',
    pleaseEnterShorterSiteName: 'Veuillez entrer un nom plus court (max 64 caractères)',
    pleaseEnterShorterSiteDescription: 'Veuillez entrer une description plus courte (max 140 caractères)',
    siteNameHasInvalidCharacters: 'Le nom a des caractères non valables',
    thereIsAProblem: 'Il y a un problème avec votre information',
    thisSiteIsOnline: 'Le réseau social est en ligne',
    onlineSiteCanBeViewed: '<strong>En ligne</strong> - Le réseau peut être visualisé selon vos paramètres de confidentialité.',
    takeOffline: 'Prendre Hors connexion',
    thisSiteIsOffline: 'Ce réseau social est déconnecté',
    offlineOnlyYouCanView: '<strong>Hors connexion</strong> - Vous seul pouvez visualiser le réseau social.',
    takeOnline: 'Prendre En ligne',
    themeSettings: 'Paramètres Thème',
    addYourOwnCss: 'Avancé',
    error: 'Erreur',
    pleaseEnterTitleForFeature: function(displayName) { return 'Veuillez entrer un titre pour votre fonctionnalité ' + displayName + ' '; },
    thereIsAProblemWithTheInformation: 'Il y a un problème avec l’information entrée',
    photos: 'Photos',
    videos: 'Vidéos',
    pleaseEnterTheChoicesFor: function(questionTitle) { return 'Veuillez entrer les choix pour "' + questionTitle + '" par exemple : Randonnée, Lecture, Magasinage '; },
    pleaseEnterTheChoices: 'Veuillez entrer les choix par exemple : Randonnée, Lecture, Magasinage',
    shareWithFriends: 'À partager avec les Amis',
    email: 'Courriel',
    separateMultipleAddresses: 'Séparer d’une virgule les adresses multiples',
    subject: 'Sujet',
    message: 'Message',
    send: 'Envoyer',
    cancel: 'Annuler',
    pleaseEnterAValidEmail: 'Veuillez entrer une adresse électronique valable',
    go: 'Allez',
    areYouSureYouWant: 'Êtes-vous sûr de vouloir le faire?',
    processing: 'En traitement…',
    pleaseKeepWindowOpen: 'Veuillez garder cette fenêtre de navigateur ouverte pendant que le traitement continue. Cela peut prendre quelques minutes.',
    complete: 'Terminé!',
    processIsComplete: 'Le processus est terminé.',
    ok: 'OK',
    body: 'Corps',
    pleaseEnterASubject: 'Veuillez entrer un sujet',
    pleaseEnterAMessage: 'Veuillez entrer un message',
    pleaseChooseFriends: 'Veuillez sélectionner quelques amis avant d’envoyer votre message.',
    thereHasBeenAnError: 'Il y a eu une erreur',
    fileNotFound: 'Fichier introuvable',
    pleaseProvideADescription: 'Veuillez fournir une description',
    pleaseEnterYourFriendsAddresses: 'Veuillez entrer les adresses ou identifiants Ning de vos amis',
    pleaseEnterSomeFeedback: 'Veuillez entrer des commentaires',
    title: 'Titre :',
    setAsMainSiteFeature: 'Configurer en avant-plan',
    thisIsTheMainSiteFeature: 'Mettre ceci en avant-plan',
    customized: 'Personnalisé',
    copyHtmlCode: 'Copier le code HTML',
    playerSize: 'Taille de lecteur',
    selectSource: 'Sélectionner la source',
    myAlbums: 'Mes Albums',
    myMusic: 'Ma musique',
    showPlaylist: 'Montrer la sélection',
    change: 'Changer',
    changing: 'Modification...',
    changePrivacy: 'Modifier la confidentialité?',
    keepWindowOpenWhileChanging: 'Veuillez garder cette fenêtre de navigateur ouverte pendant la modification des paramètres de confidentialité. Ce processus peut prendre quelques minutes.',
    htmlNotAllowed: 'HTML non autorisé',
    noFriendsFound: 'Aucun ami trouvé ne correspond à votre recherche.',
    subjectIsTooLong: function(n) { return 'Votre sujet est trop long. Veuillez utiliser '+n+' caractères ou moins.'; },
    addingInstructions: 'Veuillez laisser cette fenêtre ouverte pendant que votre contenu est ajouté.',
    addingLabel: 'Ajout...',
    cannotKeepFiles: 'Vous devrez sélectionner vos fichiers encore une fois si vous souhaitez visualiser plus d\'options.  Voulez-vous continuer?',
    done: 'Fini',
    looksLikeNotImage: 'Un ou plusieurs fichiers ne semblent pas être sous format : .jpg, .gif ou .png.  Voulez-vous quand même essayer de faire le téléchargement?',
    looksLikeNotMusic: 'Le fichier que vous avez sélectionné ne semble pas être sous format .mp3.  Voulez-vous quand même essayer de faire le téléchargement?',
    looksLikeNotVideo: 'Le fichier que vous avez sélectionné ne semble pas être sous format .mov, .mpg, .mp4, .avi, .3gp ou .wmv.  Voulez-vous quand même essayer de faire le téléchargement?',
    messageIsTooLong: function(n) { return 'Votre message est trop long. Veuillez utiliser '+n+' caractères ou moins.'; },
    pleaseSelectPhotoToUpload: 'Veuillez sélectionner une photo à télécharger.',
    processingFailed: 'Désolé, échec de traitement. Veuillez réessayer plus tard.',
    selectOrPaste: 'Vous devez sélectionner une vidéo ou coller le code d\'incrustation',
    selectOrPasteMusic: 'Vous devez sélectionner une chanson ou coller l’URL',
    sendingLabel: 'Transmission en cours...',
    thereWasAProblem: 'Il y a eu un problème en ajoutant votre contenu.  Veuillez réessayer plus tard.',
    uploadingInstructions: 'Veuillez laisser cette fenêtre ouverte pendant que votre téléchargement est en progression',
    uploadingLabel: 'Téléchargement...',
    youNeedToAddEmailRecipient: 'Vous devez ajouter un destinataire de courrier électronique.',
    yourMessage: 'Votre message',
    yourMessageIsBeingSent: 'Votre message est en transmission.',
    yourSubject: 'Votre sujet'
});


dojo.evalObjPath('xg.video.nls', true);
dojo.lang.mixin(xg.video.nls, xg.index.i18n, {
    deleteThisVideo: 'Supprimer cette Vidéo?',
    pasteInEmbedCode: 'Veuillez y coller le code d\'incrustation pour la vidéo d’un autre site.',
    pleaseSelectVideoToUpload: 'Veuillez choisir une vidéo à télécharger.',
    embedCodeContainsMoreThanOneVideo: 'Le code d\'incrustation contient plus d’une vidéo. Veuillez vous assurez qu’il n’y a qu’un <objet> ou une balise <intégré>.',
    edit: 'Modifier',
    display: 'Affichage',
    detail: 'Détail',
    player: 'Lecteur',
    from: 'De',
    show: 'Montrer',
    videos: 'vidéos',
    cancel: 'Annuler',
    save: 'Enregistrer',
    saving: 'Sauvegarde…',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Le nombre de caractères (' + n + ') dépasse le maximum (' + maximum + ') '; },
    weCouldNotLookUpAddress: function(address) { return 'Désolé, nous n’avons pas pu chercher l’adresse "' + address + '". '; },
    approve: 'Autoriser',
    approving: 'Autorisation…',
    keepWindowOpenWhileApproving: 'Veuillez garder cette fenêtre ouverte pendant l\'autorisation des videos. Cela peut prendre quelques minutes.',
    'delete': 'Supprimer',
    deleting: 'Suppression...',
    keepWindowOpenWhileDeleting: 'Veuillez garder cette fenêtre de navigateur ouverte pendant la suppression des vidéos. Ce processus peut prendre quelques minutes.',
    embedCodeMissingTag: 'Un &lt;embed&gt ou une balise &lt;object&gt; manque au code d\'incrustation.',
    fileIsNotAMov: 'Ce fichier ne semble pas être au format .mov, .mpg, .mp4, .avi, .3gp ou .wmv. Quand même essayer de le télécharger?',
    pleaseEnterAComment: 'Veuillez entrer un commentaire.',
    youRatedVideoNStars: function(n) {
        switch(n) {
            case 1: return 'Vous avez classé cette video d\'une étoile! ';
            default: return 'Vous avez classé cette video de  ' + n + ' étoiles! ';
        }
    },
    deleteThisComment: 'Supprimer ce commentaire?',
    embedHTMLCode: 'Code d\'incrustation HTML :',
    copyHTMLCode: 'Copier le code HTML',
    addToMyspace: 'Ajouter à MySpace',
    shareOnFacebook: 'Partager sur Facebook',
    addToOthers: 'Ajouter à Autres',
    directLink: 'Lien direct'
});


dojo.evalObjPath('xg.forum.nls', true);
dojo.lang.mixin(xg.forum.nls, xg.index.i18n, {
    pleaseKeepWindowOpen: 'Veuillez garder cette fenêtre de navigateur ouverte pendant que le traitement continue. Cela peut prendre quelques minutes.',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Le nombre de caractères (' + n + ') dépasse le maximum (' + maximum + ') '; },
    pleaseEnterFirstPost: 'Veuillez rédiger le premier billet pour la discussion.',
    pleaseEnterTitle: 'Veuillez entrer un titre pour la discussion',
    save: 'Enregistrer',
    cancel: 'Annuler',
    yes: 'Oui',
    no: 'Non',
    edit: 'Modifier',
    deleteCategory: 'Catégorie Supprimer',
    discussionsWillBeDeleted: 'Les discussions de cette catégorie seront supprimées.',
    whatDoWithDiscussions: 'Que souhaitez-vous faire avec les discussions de cette catégorie?',
    moveDiscussionsTo: 'Déplacer les discussions vers :',
    moveToCategory: 'Déplacer vers la catégorie…',
    deleteDiscussions: 'Supprimer les discussions',
    'delete': 'Supprimer',
    deleteReply: 'Supprimer Réponse',
    deleteReplyQ: 'Supprimer cette réponse?',
    deletingReplies: 'Suppression des réponses…',
    doYouWantToRemoveReplies: 'Voulez-vous aussi retirer les réponses à ce commentaire?',
    from: 'De',
    show: 'Montrer',
    discussions: 'discussions',
    discussionsFromACategory: 'Discussions depuis une catégorie…',
    display: 'Afficher',
    items: 'éléments',
    view: 'visualiser'
});


dojo.evalObjPath('xg.shared.nls', true);
dojo.lang.mixin(xg.shared.nls, xg.index.i18n, {
    removeFriendTitle: 'Retirer de ma liste d\'amis ?',
    removeAsFriend: 'Retirer de ma liste d\'amis',
    uploadAFile: 'Télécharger un fichier',
    addExistingFile: 'ou insérer un fichier existant',
    removeFriendConfirm: 'Êtes-vous sûr de vouloir retirer cette personne de votre liste d\'amis ?',
    locationNotFound: function(location) { return '<em>' + emplacement+ '</em> non trouvé. '; },
    confirmation: 'Confirmation',
    showMap: 'Montrer la carte',
    hideMap: 'Cacher la carte',
    yourCommentMustBeApproved: 'Votre commentaire doit être autorisé avant que les gens ne puissent le voir.',
    nComments: function(n) {
	    switch(n) {
	        case 1: return '1 Commentaire ';
	        default: return n + ' Commentaires ';
	    }
	},
    createSmallerVersion: 'Créer une version plus petite de votre image à afficher. Régler la largeur en pixels.',
    tagThis: 'Baliser ceci',
    separateMultipleTagsWithCommas: 'Séparer les balises multiples à l’aide de guillemets, par exemple, frais, « Nouvelle Zélande »',
    yourTagsHaveBeenAdded: 'Vos balises ont été ajoutées.',
    thereWasAnErrorTagging: 'Une erreur est survenue pendant l’ajout des balises.',
    thereWasAnErrorRating: 'Une erreur est survenue en classant ce contenu.',
    uploadAPhoto: 'Télécharger une photo',
    uploadAnImage: 'Télécharger une image',
    uploadAPhotoEllipsis: 'Télécharger une Photo…',
    useExistingImage: 'Utiliser une image existante :',
    existingImage: 'Image existante',
    useThemeImage: 'Utiliser une image à theme :',
    themeImage: 'Image à Thème',
    noImage: 'Aucune image',
    uploadImageFromComputer: 'Télécharger une image de votre ordinateur',
    tileThisImage: 'Disposer l’image en mosaïque',
    done: 'Fini',
    currentImage: 'Image actuelle',
    pickAColor: 'Choisir une couleur…',
    openColorPicker: 'Ouvrir Outil Pipette',
    loading: 'Chargement...',
    ok: 'OK',
    save: 'Enregistrer',
    cancel: 'Annuler',
    saving: 'enregistre…',
    addAnImage: 'Ajouter une image',
    bold: 'Caractère gras',
    italic: 'Italique',
    underline: 'Souligner',
    strikethrough: 'Biffer',
    addHyperink: 'Ajouter Hyperlien',
    options: 'Options',
    wrapTextAroundImage: 'Entourer l’image avec le texte?',
    imageOnLeft: 'Image à gauche?',
    imageOnRight: 'Image à droite?',
    createThumbnail: 'Créer une vignette?',
    pixels: 'pixels',
    popupWindow: 'Fenêtre Contextuelle?',
    linkToFullSize: 'Lien vers la version de taille complète de l’image dans une fenêtre contextuelle.',
    add: 'Ajouter',
    keepWindowOpen: 'Veuillez garder cette fenêtre ouverte pendant le téléchargement.',
    cancelUpload: 'Annuler téléchargement',
    pleaseSelectAFile: 'Veuillez choisir un fichier Image',
    pleaseSpecifyAThumbnailSize: 'Veuillez spécifier une taille de vignette',
    thumbnailSizeMustBeNumber: 'La taille de vignette doit être un nombre',
    addExistingImage: 'Ou insérer une image existante',
    clickToEdit: 'Cliquer pour modifier',
    sendingFriendRequest: 'Envoi de Requête à un(e) Ami(e)',
    requestSent: 'Requête Envoyée!',
    pleaseCorrectErrors: 'Veuillez corriger ces erreurs',
    addOrEditYourTags: 'Ajouter ou modifier vos balises :',
    addYourRating: 'Ajouter votre classement :',
    saved: 'Sauvegarder!',
    noo: 'NOUVEAU',
    none: 'AUCUN',
    joinNow: 'Adhérer maintenant',
    join: 'Se Joindre',
    youHaventRated: 'Vous n’avez pas encore classé cet article.',
    yourRatedThis: function(n) {
        switch(n) {
            case 1: return 'Vous avez classé cet article à l’aide d’1 étoile ';
            default: return 'Vous avez classé cet article à l’aide de ' + n + ' étoiles. ';
        }
    },
    yourRatingHasBeenAdded: 'Votre classement a été ajouté.',
    addToFavorites: 'Ajouter aux Favoris',
    removeFromFavorites: 'Enlever de mes Favoris',
    nStarsOutOfM: function(n,m) {
        switch(n) {
            case 1: return '1 étoile hors de ' + m;
            default: return n + ' Des étoiles hors de ' + m;
        }
    },
    follow: 'Suivre',
    stopFollowing: 'Arrêter de suivre',
    pendingPromptTitle: 'Adhésion en attente d’autorisation',
    youCanDoThis: 'Vous pouvez le faire dès que votre adhésion a été approuvée par les administrateurs.',
    yourMessage: 'Votre message',
    updateMessage: 'Mise à jour du message',
    updateMessageQ: 'Mise à jour du message?',
    goBack: 'Reculer',
    sendAnyway: 'Envoyer de toute façon',
    messageIsTooLong: function(n,m) { return 'Désolé. Le nombre maximal de caractères est '+m+'.' },
    editYourTags: 'Modifier vos balises',
    addTags: 'Ajouter balises',
    editLocation: 'Modifier emplacement',
    editTypes: 'Modifier type d\'évènement',
    warningMessage: 'Il semblerait qu\'il y a des mots dans ce courriel qui pourraient envoyer votre courriel au dossier de courrier indésirable.',
    errorMessage: 'Il y a 6 mots ou plus dans ce courriel qui pourraient envoyer votre courriel au dossier de courrier indésirable.',
    removeWords: 'Pour vous assurez que votre courriel est remis correctement, nous vous recommandons de retourner modifier ou retirer les mots suivants :',
    pleaseEnterAComment: 'Veuillez entrer un commentaire',
    pleaseEnterAFileAddress: 'Veuillez entrer l’adresse du fichier',
    pleaseEnterAWebsite: 'Veuillez entrer une adresse de site Web'
});


dojo.evalObjPath('xg.groups.nls', true);
dojo.lang.mixin(xg.groups.nls, xg.index.i18n, {
    pleaseChooseAUrl: 'Veuillez choisir une adresse web pour votre groupe.',
    urlCanContainOnlyLetters: 'L\'adresse web ne peut contenir que des lettres ou des chiffres (pas d\'espaces).',
    urlTaken: 'Nous sommes désolés - cette adresse web est déjà prise. Veuillez choisir une autre adresse web.',
    pleaseChooseAName: 'Veuillez choisir un nom pour votre groupe.',
    descriptionTooLong: function(n, maximum) { return 'La durée de la discussion de votre groupe (' + n + ') dépasse le maximum (' + maximum + ') '; },
    nameTaken: 'Nous sommes désolés - ce nom est déjà pris. Veuillez choisir un autre nom.',
    whyNot: 'Pourquoi pas?',
    groupCreatorDetermines: function(href) { return 'Le créateur du groupe détermine qui est autorisé à prendre part. Si vous pensez que vous avez été bloqué par erreur, veuillez <a ' + href + '>communiquer avec le créateur du groupe</a> '; },
    edit: 'Modifier',
    from: 'De',
    show: 'Montrer',
    groups: 'groupes',
    pleaseEnterName: 'Veuillez entrer votre nom',
    pleaseEnterEmailAddress: 'Veuillez entrer votre adresse courriel',
    xIsNotValidEmailAddress: function(x) { return x + ' est une adresse courriel non valide'; },
    save: 'Enregistrer',
    cancel: 'Annuler'
});


dojo.evalObjPath('xg.feed.nls', true);
dojo.lang.mixin(xg.feed.nls, xg.index.i18n, {
    edit: 'Modifier',
    title: 'Titre :',
    feedUrl: 'Adresse URL :',
    show: 'Montrer :',
    titles: 'Titres seulement',
    titlesAndDescriptions: 'Vue détaillée',
    display: 'Afficher',
    cancel: 'Annuler',
    save: 'Enregistrer',
    loading: 'Chargement...',
    items: 'articles'
});


dojo.evalObjPath('xg.gadgets.nls', true);
dojo.lang.mixin(xg.gadgets.nls, xg.index.i18n, {
    edit: 'Modifier',
    title: 'Titre :',
    feedUrl: 'Adresse URL :',
    cancel: 'Annuler',
    save: 'Enregistrer',
    loading: 'Chargement...',
    removeGadget: 'Retirer le gadget',
    findGadgetsInDirectory: 'Trouver le gadget dans le répertoire Gadgets'
});


dojo.evalObjPath('xg.html.nls', true);
dojo.lang.mixin(xg.html.nls, xg.index.i18n, {
    contentsTooLong: function(maximum) { return 'Le contenu est trop long. Veuillez utiliser moins de ' + maximum + ' caractères. '; },
    edit: 'Modifier',
    save: 'Enregistrer',
    cancel: 'Annuler',
    saving: 'Enregistre…',
    addAWidget: function(url) { return '<a href="' + url + '">Ajouter une métachose</a> dans cette zone de texte '; }
});


dojo.evalObjPath('xg.music.nls', true);
dojo.lang.mixin(xg.music.nls, xg.index.i18n, {
    sorryWeDoNotSupport: 'Désolé, nous ne supportons pas le carnet d’adresses Web pour votre adresse électronique. Essayer de cliquer \'Email Application\' plus bas pour vous servir des adresses de votre ordinateur.',
    play: 'lire',
    shufflePlaylist: 'Lecture aléatoire de la liste',
    pleaseSelectTrackToUpload: 'Veuillez sélectionner une chanson à télécharger.',
    pleaseEnterTrackLink: 'Veuillez entrer l’adresse URL d’une chanson.',
    thereAreUnsavedChanges: 'Il y a des modifications non enregistrées.',
    autoplay: 'Lecture automatique',
    showPlaylist: 'Montrer la sélection',
    playLabel: 'Lecture',
    url: 'URL',
    rssXspfOrM3u: 'rss, xspf ou m3u',
    save: 'Enregistrer',
    cancel: 'Annuler',
    edit: 'Modifier',
    fileIsNotAnMp3: 'Un des fichiers ne semble pas être au format MP3. Tentative de téléchargement tout de même?',
    entryNotAUrl: 'Une des entrées ne semble pas être une adresse URL. Assurez-vous que toutes les entrées commencent par <kbd>http://</kbd>'
});


dojo.evalObjPath('xg.page.nls', true);
dojo.lang.mixin(xg.page.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Le nombre de caractères (' + n + ') dépasse le maximum (' + maximum + ') '; },
    pleaseEnterContent: 'Veuillez entrer le contenu de la page',
    pleaseEnterTitle: 'Veuillez entrer un titre pour la page',
    pleaseEnterAComment: 'Veuillez entrer un commentaire',
    deleteThisComment: 'Êtes-vous sûr de vouloir supprimer ce commentaire?',
    save: 'Enregistrer',
    cancel: 'Annuler',
    discussionTitle: 'Titre de la page :',
    tags: 'Balises :',
    edit: 'Modifier',
    close: 'Fermer',
    displayPagePosts: 'Afficher les billets de la page',
    displayTab: 'Afficher onglet',
    displayTabForPage: 'Pour afficher un onglet pour la page',
    directory: 'Répertoire',
    addAnotherPage: 'Ajouter autre page',
    tabText: 'Tab texte',
    urlDirectory: 'Répertoire URL',
    tabTitle: 'Titre d\'onglet',
    remove: 'Retirer',
    thereIsAProblem: 'Il y a un problème avec votre information'
});


dojo.evalObjPath('xg.photo.nls', true);
dojo.lang.mixin(xg.photo.nls, xg.index.i18n, {
    randomOrder: 'Ordre aléatoire',
    untitled: 'Sans Titre',
    photos: 'Photos',
    edit: 'Modifier',
    photosFromAnAlbum: 'Albums',
    show: 'Montrer',
    rows: 'Lignes',
    cancel: 'Annuler',
    save: 'Enregistrer',
    deleteThisPhoto: 'Supprimer cette photo?',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Le nombre de caractères (' + n + ') dépasse le maximum (' + maximum + ') '; },
    weCouldNotLookUpAddress: function(address) { return 'Désolé, nous n’avons pas pu chercher l’adresse "' + address + '". '; },
    pleaseSelectPhotoToUpload: 'Veuillez sélectionner une photo à télécharger.',
    pleaseEnterAComment: 'Veuillez entrer un commentaire.',
    addToExistingAlbum: 'Ajouter à un Album Existant',
    addToNewAlbumTitled: 'Ajouter à un Nouvel Album Titré…',
    deleteThisComment: 'Supprimer ce commentaire?',
    importingNofMPhotos: function(n,m) { return 'Importation <span id="currentP">' + n + '</span> de ' + m + ' photos. ';},
    starting: 'Démarrage…',
    done: 'Terminé!',
    from: 'De',
    display: 'Affichage',
    takingYou: 'Allons voir vos photos…',
    anErrorOccurred: 'Malheureusement, une erreur est survenue. Veuillez rapporter ce problème à l’aide du lien en bas de page.',
    weCouldntFind: 'Nous n’avons pas pu trouver de photos! Pourquoi ne pas essayer une des autres options?'
});


dojo.evalObjPath('xg.activity.nls', true);
dojo.lang.mixin(xg.activity.nls, xg.index.i18n, {
    edit: 'Modifier',
    show: 'Montrer',
    events: 'évènements',
    setWhatActivityGetsDisplayed: 'Configurer l’activité devant s\'afficher',
    save: 'Enregistrer',
    cancel: 'Annuler'
});


dojo.evalObjPath('xg.profiles.nls', true);
dojo.lang.mixin(xg.profiles.nls, xg.index.i18n, {
    pleaseEnterValueForPost: 'Veuillez entrer un texte pour le billet',
    pleaseProvideAValidDate: 'Veuillez fournir une date valable',
    uploadAFile: 'Télécharger un fichier',
    pleaseEnterUrlOfLink: 'Veuillez entrer l’adresse URL du lien :',
    pleaseEnterTextOfLink: 'Quel texte voulez-vous lier?',
    edit: 'Modifier',
    recentlyAdded: 'ajoutée récemment',
    featured: 'En primeur',
    iHaveRecentlyAdded: 'Que j\'ai ajoutée récemment',
    fromTheSite: 'Du réseau social',
    cancel: 'Annuler',
    save: 'Enregistrer',
    loading: 'Chargement...',
    addAsFriend: 'Ajouter à ma liste d\'amis!',
    removeAsFriend: 'Retirer de ma liste d\'amis',
    requestSent: 'Requête Envoyée!',
    sendingFriendRequest: 'Envoi de Requête à un(e) Ami(e)',
    thisIsYou: 'Ceci c’est vous!',
    isYourFriend: 'Est votre ami(e)',
    isBlocked: 'Est bloqué',
    pleaseEnterAComment: 'Veuillez entrer un commentaire',
    pleaseEnterPostBody: 'Veuillez entrer quelque chose pour le corps du billet',
    pleaseSelectAFile: 'Veuillez sélectionner un fichier',
    pleaseEnterChatter: 'Veuillez entrer quelque chose pour votre commentaire',
    toggleBetweenHTML: 'Montrer/Cacher le code HTML',
    attachAFile: 'Joindre un Fichier',
    addAPhoto: 'Ajouter une photo',
    insertALink: 'Insérer un Lien',
    changeTextSize: 'Modifier la Taille du Texte',
    makeABulletedList: 'Faire une Liste à Puces',
    makeANumberedList: 'Faire une Liste Numérotée',
    crossOutText: 'Texte barré',
    underlineText: 'Texte Souligné',
    italicizeText: 'Texte en Italique',
    boldText: 'Texte en Caractères Gras',
    letMeApproveChatters: 'Me laisser tout d\'abord autoriser les commentaires avant publication?',
    noPostChattersImmediately: 'Non – publier immédiatement les commentaires',
    yesApproveChattersFirst: 'Oui – autoriser d\'abord les commentaires',
    yourCommentMustBeApproved: 'Votre commentaire doit être autorisé avant que les gens ne puissent le voir.',
    reallyDeleteThisPost: 'Supprimer vraiment ce billet?',
    commentWall: 'Mur de commentaires',
    commentWallNComments: function(n) {
        switch(n) {
            case 1: return 'Mur de commentaires(1 commentaire) ';
            default: return 'Mur de commentaires(' + n + ' commentaires) ';
        }
    },
    display: 'Affichage',
    from: 'De',
    show: 'Montrer',
    rows: 'Lignes',
    posts: 'Billets',
    networkError: 'Erreur de réseau',
    returnToDefaultWarning: 'Cela réinitialisera toutes vos fonctionnalités et le thème de Ma page aux valeurs par défaut du réseau. Voulez -vous continuer?',
    wereSorry: 'Nous sommes désolés, mais nous avons été incapable de enregistrer votre nouvelle mise à jour en ce moment. Une perte de connexion internet est une cause probable. Veuillez vérifier votre connexion et réessayer.'
});


dojo.evalObjPath('xg.uploader.nls', true);
dojo.lang.mixin(xg.uploader.nls, xg.index.i18n, {
    fileBrowserHeader: 'Poste de travail',
    fileRoot: 'Poste de travail',
    fileInformationHeader: 'Information',
    uploadHeader: 'Fichiers à télécharger',
    dragOutInstructions: 'Faites glisser les fichiers à l’extérieur pour les supprimer',
    dragInInstructions: 'Faites glisser les fichiers ici',
    selectInstructions: 'Sélectionner un fichier',
    files: 'Fichiers',
    totalSize: 'Taille totale',
    fileName: 'Nom',
    fileSize: 'Taille',
    nextButton: 'Suivant >',
    okayButton: 'OK',
    yesButton: 'Oui',
    noButton: 'Non',
    uploadButton: 'Télécharger',
    cancelButton: 'Annuler',
    backButton: 'Précédent',
    continueButton: 'Continuer',
    uploadingLabel: 'Téléchargement...',
    uploadingStatus: function(n, m) { return 'Téléchargement de ' + n + ' de ' + m; },
    uploadingInstructions: 'Veuillez garder cette fenêtre ouverte pendant le téléchargement.',
    uploadLimitWarning: function(n) { return 'Vous pouvez télécharger ' + n + ' fichiers à la fois. '; },
    uploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Vous avez ajouté le nombre maximal des fichiers. ';
            case 1: return 'Vous pouvez télécharger un fichier de plus. ';
            default: return 'Vous pouvez télécharger ' + n + ' fichiers supplémentaires. ';
        }
    },
    iHaveTheRight: 'J’ai le droit de télécharger ces fichiers en vertu des <a href="/main/authorization/termsOfService">Conditions d\'utilisation</a>',
    updateJavaTitle: 'Mettre à jour Java',
    updateJavaDescription: 'Le téléchargeur en lot a besoin d’une version plus récente de Java.  Cliquez sur « Ok » pour obtenir Java.',
    batchEditorLabel: 'Modifier l’information pour tous les items',
    applyThisInfo: 'Utiliser cette information pour tous les fichiers ci-dessous.',
    titleProperty: 'Titre',
    descriptionProperty: 'Description',
    tagsProperty: 'Repères',
    viewableByProperty: 'Peut être vue par',
    viewableByEveryone: 'Tout le monde',
    viewableByFriends: 'Juste mes amis',
    viewableByMe: 'Juste moi',
    albumProperty: 'Album',
    artistProperty: 'Artiste',
    enableDownloadLinkProperty: 'Permettre le téléchargement de la chanson',
    enableProfileUsageProperty: 'Permet aux membres d\'ajouter cette chanson à leurs pages',
    licenseProperty: 'Licence',
    creativeCommonsVersion: '3.0',
    selectLicense: '— Sélectionner une licence —',
    copyright: '© Tous droits réservés',
    ccByX: function(n) { return 'Creative Commons Attribution ' + n; },
    ccBySaX: function(n) { return 'Creative Commons Attribution Partage également ' + n; },
    ccByNdX: function(n) { return 'Creative Commons Attribution Aucune modification ' + n; },
    ccByNcX: function(n) { return 'Creative Commons Attribution Aucune utilisation commerciale ' + n; },
    ccByNcSaX: function(n) { return 'Creative Commons Attribution Aucune utilisation commerciale Partager également ' + n; },
    ccByNcNdX: function(n) { return 'Creative Commons Attribution Aucune utilisation commerciale Aucune modification ' + n; },
    publicDomain: 'Domaine Publique',
    other: 'Autre',
    errorUnexpectedTitle: 'Oups!',
    errorUnexpectedDescription: 'Il y a eu une erreur. Veuillez réessayer.',
    errorTooManyTitle: 'Trop d’items.',
    errorTooManyDescription: function(n) { return 'Nous sommes désolés, mais vous pouvez seulement télécharger ' + n + ' éléments à la fois. '; },
    errorNotAMemberTitle: 'Non Permis',
    errorNotAMemberDescription: 'Nous sommes désolés, mais vous devez être membre pour télécharger.',
    errorContentTypeNotAllowedTitle: 'Non Permis',
    errorContentTypeNotAllowedDescription: 'Nous sommes désolés, mais vous n’êtes pas autorisés à télécharger un contenu de ce type.',
    errorUnsupportedFormatTitle: 'Oups!',
    errorUnsupportedFormatDescription: 'Nous sommes désolés, mais nous n’acceptons pas ce type des fichiers.',
    errorUnsupportedFileTitle: 'Oups!',
    errorUnsupportedFileDescription: 'foo.exe est un format qui n’est pas pris en charge.',
    errorUploadUnexpectedTitle: 'Oups!',
    errorUploadUnexpectedDescription: function(file) { return file ? ('Il semble y avoir un problème avec le fichier ' + file + '. Veuillez le retirer de la liste avant de télécharger vos autres fichiers.') : 'Il semble y avoir un problème avec le fichier en haut de la liste. Veuillez le retirer avant de télécharger vos autres fichiers.'; 
	},
    cancelUploadTitle: 'Annuler téléchargement?',
    cancelUploadDescription: 'Êtes-vous certain(e) de vouloir annuler les téléchargements restants?',
    uploadSuccessfulTitle: 'Téléchargement complété',
    uploadSuccessfulDescription: 'Veuillez patienter, nous ouvrons la section de vos téléchargements…',
    uploadPendingDescription: 'Vos fichiers ont été téléchargés avec succès et ils sont en attente d’approbation.',
    photosUploadHeader: 'Photos à télécharger',
    photosDragOutInstructions: 'Faites glisser les photos à l’extérieur pour les supprimer',
    photosDragInInstructions: 'Faites glisser vos fichiers ici',
    photosSelectInstructions: 'Sélectionnez une photo',
    photosFiles: 'Photos',
    photosUploadingStatus: function(n, m) { return 'Téléchargement de la photo ' + n + ' de ' + m; },
    photosErrorTooManyTitle: 'Trop de photos',
    photosErrorTooManyDescription: function(n) { return 'Nous sommes désolés, mais vous pouvez seulement télécharger ' + n + ' photos à la fois. '; },
    photosErrorContentTypeNotAllowedDescription: 'Nous sommes désolés, mais le téléchargement des photos a été désactivé.',
    photosErrorUnsupportedFormatDescription: 'Nous sommes désolés, mais vous pouvez seulement télécharger des images en format .jpg, .gif or .png.',
    photosErrorUnsupportedFileDescription: function(n) { return n + ' n’est pas un fichier .jpg, .gif or .png.'; },
    photosBatchEditorLabel: 'Modifier les informations pour toutes les photos',
    photosApplyThisInfo: 'Utiliser cette information pour toutes les photos ci-dessous',
    photosErrorUploadUnexpectedDescription: function(file) {
		return file ?
			('Il semble y avoir un problème avec le fichier' + file + '. Veuillez l’enlever de la liste avant de télécharger vos autres photos.') :
			'Il semble y avoir un problème avec la photo en haut de la liste. Veuillez l’enlever avant de télécharger vos autres photos.';
	},
    photosUploadSuccessfulDescription: 'Veuillez patienter, nous ouvrons la section de vos photos…',
    photosUploadPendingDescription: 'Vos photos ont été téléchargées avec succès et elles sont en attente d’approbation.',
    photosUploadLimitWarning: function(n) { return 'Vous pouvez télécharger ' + n + ' photos à la fois. '; },
    photosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Vous avez ajouté le nombre maximal des photos. ';
            case 1: return 'Vous pouvez télécharger une photo de plus. ';
            default: return 'Vous pouvez télécharger ' + n + ' photos de plus. ';
        }
    },
    photosIHaveTheRight: 'J’ai le droit de télécharger ces photos en vertu des <a href="/main/authorization/termsOfService">Conditions d\'utilisation</a>',
    videosUploadHeader: 'Vidéos à télécharger',
    videosDragInInstructions: 'Faites glisser vos vidéos ici',
    videosDragOutInstructions: 'Faites glisser les vidéos à l’extérieur pour les supprimer',
    videosSelectInstructions: 'Sélectionnez une vidéo',
    videosFiles: 'Vidéos',
    videosUploadingStatus: function(n, m) { return 'Téléchargement de vidéo ' + n + ' de ' + m; },
    videosErrorTooManyTitle: 'Trop de vidéos',
    videosErrorTooManyDescription: function(n) { return 'Nous sommes désolés, mais vous pouvez seulement télécharger ' + n + ' vidéos à la fois. '; },
    videosErrorContentTypeNotAllowedDescription: 'Nous sommes désolés, mais le téléchargement des vidéos a été désactivé.',
    videosErrorUnsupportedFormatDescription: 'Nous sommes désolés, mais vous pouvez seulement télécharger des vidéos en format .avi, .mov, .mp4, .wmv ou .mpg.',
    videosErrorUnsupportedFileDescription: function(x) { return x + ' n’est pas un fichier .avi, .mov, .mp4, .wmv ou .mpg.';  },
    videosBatchEditorLabel: 'Modifier l’information pour toutes les vidéos',
    videosApplyThisInfo: 'Utiliser cette information pour toutes les vidéos ci-dessous',
    videosErrorUploadUnexpectedDescription: function(file) {
		return file ?
			('Il semble y avoir un problème avec le fichier' + file + '. Veuillez l’enlever de la liste avant de télécharger vos autres photos vidéos.') :
			'Il semble y avoir un problème avec la vidéo en haut de la liste. Veuillez l’enlever avant de télécharger vos autres vidéos.';
	},
    videosUploadSuccessfulDescription: 'Veuillez patienter, nous ouvrons la section de vos vidéos...',
    videosUploadPendingDescription: 'Vos vidéos ont été téléchargées avec succès et elles sont en attente d’approbation.',
    videosUploadLimitWarning: function(n) { return 'Vous pouvez télécharger ' + n + ' vidéos à la fois. '; },
    videosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Vous avez ajouté le nombre maximal des vidéos. ';
            case 1: return 'Vous pouvez télécharger une vidéo de plus. ';
            default: return 'Vous pouvez télécharger ' + n + ' vidéos de plus. ';
        }
    },
    videosIHaveTheRight: 'J’ai le droit de télécharger ces vidéos en vertu des <a href="/main/authorization/termsOfService">Conditions d\'utilisation</a>',
    musicUploadHeader: 'Fichiers audio à télécharger',
    musicTitleProperty: 'Titre de la chanson',
    musicDragOutInstructions: 'Faites glisser les fichiers audio à l’extérieur pour les supprimer',
    musicDragInInstructions: 'Faites glisser les fichiers audio ici',
    musicSelectInstructions: 'Sélectionnez un fichier audio',
    musicFiles: 'Fichiers audio',
    musicUploadingStatus: function(n, m) { return 'Téléchargement des fichiers audio ' + n + ' de ' + m; },
    musicErrorTooManyTitle: 'Trop de fichiers audio',
    musicErrorTooManyDescription: function(n) { return 'Nous sommes désolés, mais vous pouvez seulement télécharger ' + n + ' fichiers audio à la fois. '; },
    musicErrorContentTypeNotAllowedDescription: 'Nous sommes désolés, mais le téléchargement des fichiers audio a été désactivé.',
    musicErrorUnsupportedFormatDescription: 'Nous sommes désolés, mais vous pouvez seulement télécharger des fichiers audio en format .mp3',
    musicErrorUnsupportedFileDescription: function(x) { return x + ' n’est pas un fichier .mp3.'; },
    musicBatchEditorLabel: 'Modifier l’information pour tous les fichiers audio',
    musicApplyThisInfo: 'Utiliser cette information pour tous les fichiers audio ci-dessous',
    musicErrorUploadUnexpectedDescription: function(file) {
		return file ?
			('Il semble y avoir un problème avec le fichier' + file + '. Veuillez l’enlever de la liste avant de télécharger vos autres fichiers audio.') :
			'Il semble y avoir un problème avec le fichier audio en haut de la liste. Veuillez l’enlever avant de télécharger vos autres fichiers audio.';
	},
    musicUploadSuccessfulDescription: 'Veuillez patienter, nous ouvrons la section de vos fichiers audio...',
    musicUploadPendingDescription: 'Vos fichiers audio ont été téléchargés avec succès et ils sont en attente d’approbation.',
    musicUploadLimitWarning: function(n) { return 'Vous pouvez télécharger ' + n + ' fichiers audio à la fois. '; },
    musicUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Vous avez ajouté le nombre maximal des fichiers audio. ';
            case 1: return 'Vous pouvez télécharger un fichier audio de plus. ';
            default: return 'Vous pouvez télécharger ‘ + n + ’ fichiers audio supplémentaires. ';
        }
    },
    musicIHaveTheRight: 'J’ai le droit de télécharger ces fichiers audio en vertu des <a href="/main/authorization/termsOfService">Conditions d\'utilisation</a>'
});


dojo.evalObjPath('xg.events.nls', true);
dojo.lang.mixin(xg.events.nls, xg.index.i18n, {
    messageIsTooLong: function(n) { return 'Votre message est trop long. Veuillez utilisez '+n+' caractères ou moins.'; },
    sendMessageToGuests: 'Envoyer message aux invités',
    sendMessageToGuestsThat: 'Envoyer message aux invités qui :',
    areAttending: 'Sont présent',
    mightAttend: 'Pourrait assister',
    haveNotYetRsvped: 'N\'ont pas encore RSVP',
    areNotAttending: 'Ne sont pas présent',
    messageSent: 'Message envoyé!',
    chooseRecipient: 'Veuillez choisir un destinataire',
    pleaseChooseImage: 'Veuillez choisir une image pour l\'évènement',
    pleaseEnterAMessage: 'Veuillez entrer un message',
    pleaseEnterDescription: 'Veuillez entrer une description pour l\'évènement',
    pleaseEnterLocation: 'Veuillez entrer un emplacement pour l\'évènement',
    pleaseEnterTitle: 'Veuillez entrer un titre pour l\'évènement',
    pleaseEnterType: 'Veuillez entrer au moins un type pour l\'évènement',
    send: 'Envoyer',
    sending: 'Transmission...',
    thereHasBeenAnError: 'Il y a eu une erreur',
    yourMessage: 'Votre message',
    yourMessageHasBeenSent: 'Votre message a été envoyé.',
    yourMessageIsBeingSent: 'Votre message est en transmission.'
});


dojo.evalObjPath('xg.notes.nls', true);
dojo.lang.mixin(xg.notes.nls, xg.index.i18n, {
    addNewNote: 'Ajouter nouvelle note',
    noteTitleTooLong: 'Le titre de la note est trop long',
    pleaseEnterNoteEntry: 'Veuillez entrer une note',
    pleaseEnterNoteTitle: 'Veuillez entrer un titre de note!'
});