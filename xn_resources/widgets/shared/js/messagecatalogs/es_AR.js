dojo.provide('xg.shared.messagecatalogs.es_AR');

dojo.require('xg.index.i18n');

/**
 * Texts for the es_AR locale.
 */
// Use UTF-8 byte sequences instead of HTML entities, e.g., & instead of &amp;, … instead of &hellip;  [Jon Aquino 2007-01-10]


dojo.evalObjPath('xg.feed.nls', true);
dojo.lang.mixin(xg.feed.nls, xg.index.i18n, {
    edit: 'Editar',
    title: 'Título:',
    feedUrl: 'URL:',
    show: 'Mostrar:',
    titles: 'Sólo títulos',
    titlesAndDescriptions: 'Vista de detalles',
    display: 'Pantalla',
    cancel: 'Cancelar',
    save: 'Guardar',
    loading: 'Cargando...',
    items: 'artículos'
});


dojo.evalObjPath('xg.forum.nls', true);
dojo.lang.mixin(xg.forum.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'El número de caracteres (' + n + ') sobrepasa el máximo (' + maximum + ') '; },
    pleaseEnterFirstPost: 'Escribí la primera publicación para la discusión',
    pleaseEnterTitle: 'Título',
    save: 'Guardar',
    cancel: 'Cancelar',
    yes: 'Sí',
    no: 'No',
    edit: 'Editar',
    deleteCategory: 'Eliminar categoría',
    discussionsWillBeDeleted: 'Las discusiones de esta categoría se eliminarán.',
    whatDoWithDiscussions: '¿Qué te gustaría hacer con las discusiones de esta categoría?',
    moveDiscussionsTo: 'Mover discusiones a:',
    moveToCategory: 'Mover a Categoría…',
    deleteDiscussions: 'Eliminar discusiones',
    'delete': 'Eliminar',
    deleteReply: 'Eliminar respuesta',
    deleteReplyQ: '¿Eliminar esta respuesta?',
    deletingReplies: 'Eliminando respuestas…',
    doYouWantToRemoveReplies: '¿También querés eliminar las respuestas a este comentario?',
    pleaseKeepWindowOpen: 'Mantené abierta esta ventana del explorador mientras se realiza el procesamiento. Puede tardar unos minutos.',
    from: 'De',
    show: 'Mostrar',
    discussions: 'discusiones',
    discussionsFromACategory: 'Discusiones de una categoría…',
    display: 'Pantalla',
    items: 'elementos',
    view: 'Ver'
});


dojo.evalObjPath('xg.groups.nls', true);
dojo.lang.mixin(xg.groups.nls, xg.index.i18n, {
    pleaseChooseAName: 'Elegí un nombre para tu grupo.',
    pleaseChooseAUrl: 'Elegí una dirección web para tu grupo.',
    urlCanContainOnlyLetters: 'La dirección  web puede incluir sólo letras y números (no espacios).',
    descriptionTooLong: function(n, maximum) { return 'La longitud de la descripción de tu grupo (' + n + ') sobrepasa el máximo (' + maximum + ') '; },
    nameTaken: 'Ese nombre ya existe. Por favor, elegí otro.',
    urlTaken: 'Esa dirección ya existe. Por favor elegí otra.',
    whyNot: '¿Por qué no?',
    groupCreatorDetermines: function(href) { return 'El creador del grupo decide quién puede unirse. Si creés que te bloquearon por error, <a ' + href + '>ponete en contacto con el creador del grupo</a> '; },
    edit: 'Editar',
    from: 'De',
    show: 'Mostrar',
    groups: 'grupos',
    pleaseEnterName: 'Nombre',
    pleaseEnterEmailAddress: 'Ingresá tu dirección de correo electrónico',
    xIsNotValidEmailAddress: function(x) { return x + ' is not a valid email address'; },
    save: 'Guardar',
    cancel: 'Cancelar'
});


dojo.evalObjPath('xg.html.nls', true);
dojo.lang.mixin(xg.html.nls, xg.index.i18n, {
    contentsTooLong: function(maximum) { return 'El contenido es demasiado largo. Por favor usá menos de ' + maximum + ' caracteres. '; },
    edit: 'Editar',
    save: 'Guardar',
    cancel: 'Cancelar',
    saving: 'Guardando…',
    addAWidget: function(url) { return '<a href="' + url + '">Agregar un widget</a> a este cuadro de texto '; }
});


dojo.evalObjPath('xg.index.nls', true);
dojo.lang.mixin(xg.index.nls, xg.index.i18n, {
    sendInvitation: 'Enviar invitación',
    sendInvitationToNFriends: function(n) {
        switch(n) {
            case 1: return '¿Enviar invitación a un amigo? ';
            default: return '¿Enviar invitación a ' + n + ' amigos? ';
        }
    },
    yourMessageOptional: '<label>Tu mensaje</label> (Opcional)',
    pleaseChoosePeople: 'Elegí algunas personas a las que querés invitar.',
    pleaseEnterEmailAddress: 'Dirección de correo electrónico',
    pleaseEnterPassword: function(emailAddress) { return 'Ingresá tu contraseña ' + emailAddress + '. '; },
    sorryWeDoNotSupport: 'Lamentablemente, no trabajamos con la libreta de direcciones de tu dirección de correo electrónico. Intentá hacer clic en \'Address Book Application\' que se encuentra más abajo, para usar las direcciones de tu PC.',
    sorryWeDontSupport: 'Lamentablemente, no trabajamos con la libreta de direcciones de tu dirección de correo electrónico. Intentá hacer clic en \'Email Application\' que se encuentra más abajo, para usar las direcciones de tu PC.',
    pleaseSelectSecondPart: 'Seleccioná la segunda parte de tu dirección de correo electrónico, por ej., gmail.com.',
    atSymbolNotAllowed: 'Aseguráte de que el símbolo @ no esté en la primera parte de la dirección de correo electrónico.',
    resetTextQ: '¿Restablecer texto?',
    resetTextToOriginalVersion: '¿Estás seguro de que querés restablecer todo el texto a la versión original? Se perderán todos los cambios.',
    changeQuestionsToPublic: '¿Cambiar preguntas a públicas?',
    changingPrivateQuestionsToPublic: 'Cambiar preguntas privadas a públicas expondrá todas las respuestas de los miembros. ¿Estás seguro de que querés hacerlo?',
    youHaveUnsavedChanges: 'Hay cambios sin guardar.',
    pleaseEnterASiteName: 'Ingresá un nombre para la red social, por ej.: Club de payasos',
    pleaseEnterShorterSiteName: 'Ingresá un nombre más corto (máx. 64 caracteres)',
    pleaseEnterShorterSiteDescription: 'Ingresá una descripción más corta (máx. 140 caracteres)',
    siteNameHasInvalidCharacters: 'El nombre incluye caracteres no válidos',
    thereIsAProblem: 'Hay un problema con tu información',
    thisSiteIsOnline: 'La red social está Conectada',
    onlineSiteCanBeViewed: '<strong>Online</strong> - La red es visible según tu configuración de privacidad.',
    takeOffline: 'Desconectarse',
    thisSiteIsOffline: 'La red social está desconectada',
    offlineOnlyYouCanView: '<strong>Offline</strong> - Sólo vos podés ver esta red social.',
    takeOnline: 'Conectarse',
    themeSettings: 'Configuración de tema',
    addYourOwnCss: 'Avanzado',
    error: 'Error',
    pleaseEnterTitleForFeature: function(displayName) { return 'Ingresá un título para la característica ' + displayName + ' '; },
    thereIsAProblemWithTheInformation: 'Hay un problema con la información ingresada',
    photos: 'Fotos',
    videos: 'Videos',
    pleaseEnterTheChoicesFor: function(questionTitle) { return 'Ingresá las opciones para "' + questionTitle + '" por ej.: escalar, leer, ir de compras '; },
    pleaseEnterTheChoices: 'Ingresá las opciones, por ej.: escalar, leer, ir de compras',
    shareWithFriends: 'Compartir con amigos',
    email: 'correo electrónico',
    separateMultipleAddresses: 'Separar varias direcciones con comas',
    subject: 'Asunto',
    message: 'Mensaje',
    send: 'Enviar',
    cancel: 'Cancelar',
    pleaseEnterAValidEmail: 'Ingresá una dirección de correo electrónico válida',
    go: 'Ir a',
    areYouSureYouWant: '¿Estás seguro de que querés hacer esto?',
    processing: 'Procesando…',
    pleaseKeepWindowOpen: 'Mantené abierta esta ventana del explorador mientras se realiza el procesamiento. Puede tardar unos minutos.',
    complete: '¡Listo!',
    processIsComplete: 'El proceso está listo.',
    ok: 'OK',
    body: 'Cuerpo',
    pleaseEnterASubject: 'Ingresá un asunto',
    pleaseEnterAMessage: 'Ingresá un mensaje',
    thereHasBeenAnError: 'Se produjo un error',
    fileNotFound: 'No se encuentra el archivo',
    pleaseProvideADescription: 'Da una descripción',
    pleaseEnterYourFriendsAddresses: 'Ingresá las direcciones de tus amigos o sus ID de Ning',
    pleaseEnterSomeFeedback: 'Ingresá algún comentario',
    title: 'Título:',
    setAsMainSiteFeature: 'Configurar como característica principal',
    thisIsTheMainSiteFeature: 'Esta es la característica principal',
    customized: 'Personalizado',
    copyHtmlCode: 'Copiar código HTML',
    playerSize: 'Tamaño del reproductor',
    selectSource: 'Seleccionar origen',
    myAlbums: 'Mis álbumes',
    myMusic: 'Mi música',
    myVideos: 'Mis videos',
    showPlaylist: 'Mostrar lista de reproducción',
    change: 'Cambiar',
    changing: 'Cambiando...',
    changePrivacy: '¿Cambiar privacidad?',
    keepWindowOpenWhileChanging: 'Mantené abierta esta ventana del explorador mientras se cambian las configuraciones. El proceso puede tardar unos minutos.',
    htmlNotAllowed: 'No se permite HTML',
    showingNFriends: function(n, searchString) {
        switch(n) {
            case 1: return 'Mostrando 1 amigo que coincide con "' + searchString + '". <a href="#">Mostrar a todos</a> ';
            default: return 'Mostrando ' + n + ' amigos que coinciden con "' + searchString + '". <a href="#">Mostrar a todos</a> ';
        }
    },
    sendMessage: 'Enviar mensaje',
    sendMessageToNFriends: function(n) {
        switch(n) {
            case 1: return '¿Enviar el mensaje a 1 amigo? ';
            default: return '¿Enviar el mensaje a ' + n + ' amigos? ';
        }
    },
    invitingNFriends: function(n) {
        switch(n) {
            case 1: return 'Invitando a 1 amigo… ';
            default: return 'Invitando a ' + n + ' amigos… ';
        }
    },
    nFriends: function(n) {
        switch(n) {
            case 1: return '1 amigo… ';
            default: return n + ' amigos… ';
        }
    },
    sendingMessageToNFriends: function(n) {
        switch(n) {
            case 1: return 'Enviando el mensaje a 1 amigo… ';
            default: return 'Enviando el mensaje a ' + n + ' amigos… ';
        }
    },
    noPeopleSelected: 'No hay personas seleccionadas',
    pleaseChooseFriends: 'Seleccioná a algún amigo antes de enviar el mensaje.',
    noFriendsFound: 'No se encontraron amigos que coincidan con la búsqueda.',
    subjectIsTooLong: function(n) { return 'Tu asunto es demasiado largo. Por favor usá '+n+' caracteres o menos.'; },
    addingInstructions: 'Por favor, dejá esta ventana abierta mientras se agrega el contenido que estás cargando.',
    addingLabel: 'Agregando.. .',
    cannotKeepFiles: 'Si querés ver más opciones, tenés que volver a elegir tus archivos.  ¿Querés continuar?',
    done: 'Listo',
    looksLikeNotImage: 'Parece que uno o más archivos no  están en formato .jpg, .gif o .png.  ¿Te gustaría intentar cargarlos de todas formas?',
    looksLikeNotMusic: 'Parece que el archivo que seleccionaste no  está en formato .mp3.  ¿Te gustaría intentar cargarlos de todas formas?',
    looksLikeNotVideo: 'Parece que el archivo que seleccionaste no  está en formato .mov, .mpg, .mp4, .avi, .3gp o .wmv.  ¿Te gustaría intentar cargarlos de todas formas?',
    messageIsTooLong: function(n) { return 'Tu mensaje es demasiado largo.  Por favor, usá '+n+' caracteres o menos.'; },
    pleaseSelectPhotoToUpload: 'Seleccioná una foto para subir.',
    processingFailed: 'Lo lamentamos, falló el procesamiento. Por favor, intentá otra vez más tarde.',
    selectOrPaste: 'Tenés que seleccionar un video o pegar el código \'incrustado\'',
    selectOrPasteMusic: 'Tenés que seleccionar una canción o pegar la dirección URL',
    sendingLabel: 'Enviando... .',
    thereWasAProblem: 'Hubo un problema al agregar el contenido.  Por favor, intentá otra vez más tarde.',
    uploadingInstructions: 'Por favor, dejá esta ventana abierta mientras termina la carga.',
    uploadingLabel: 'Subiendo.. .',
    youNeedToAddEmailRecipient: 'Tenés que agregar un destinatario de correo electrónico.',
    yourMessage: 'Tu mensaje',
    yourMessageIsBeingSent: 'Tu mensaje se está enviando.',
    yourSubject: 'Tu Tema'
});


dojo.evalObjPath('xg.music.nls', true);
dojo.lang.mixin(xg.music.nls, xg.index.i18n, {
    shufflePlaylist: 'Lista de reproducción al azar',
    play: 'reproducir',
    pleaseSelectTrackToUpload: 'Seleccioná la canción que querés subir.',
    pleaseEnterTrackLink: 'Ingresá la dirección URL de la canción.',
    thereAreUnsavedChanges: 'Hay cambios sin guardar.',
    autoplay: 'Reproducción automática',
    showPlaylist: 'Mostrar lista de reproducción',
    playLabel: 'Reproducir',
    url: 'URL',
    rssXspfOrM3u: 'rss, xspf o m3u',
    save: 'Guardar',
    cancel: 'Cancelar',
    edit: 'Editar',
    fileIsNotAnMp3: 'Uno de los archivos parece no ser un MP3. ¿Querés intentar subirlo de todas formas?',
    entryNotAUrl: 'Una de las entradas parece no ser una dirección URL. Controlá que todas las entradas comiencen con <kbd>http://</kbd>'
});


dojo.evalObjPath('xg.page.nls', true);
dojo.lang.mixin(xg.page.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'El número de caracteres (' + n + ') sobrepasa el máximo (' + maximum + ') '; },
    pleaseEnterContent: 'Ingresa el contenido de la página',
    pleaseEnterTitle: 'Ingresá un título para la página',
    pleaseEnterAComment: 'Ingresá un comentario',
    deleteThisComment: '¿Estás seguro de que querés eliminar este comentario?',
    save: 'Guardar',
    cancel: 'Cancelar',
    discussionTitle: 'Título de página:',
    tags: 'Etiquetas:',
    edit: 'Editar',
    close: 'Cerrar',
    displayPagePosts: 'Mostrar publicaciones de la página',
    directory: 'Directorio',
    displayTab: 'Mostrar ficha',
    addAnotherPage: 'Agregar otra página',
    tabText: 'Texto de la ficha',
    urlDirectory: 'Directorio URL',
    displayTabForPage: 'Si mostrar o no una ficha para la página',
    tabTitle: 'Título de la ficha',
    remove: 'Eliminar',
    thereIsAProblem: 'Hay un problema con tu información'
});


dojo.evalObjPath('xg.photo.nls', true);
dojo.lang.mixin(xg.photo.nls, xg.index.i18n, {
    randomOrder: 'Orden aleatorio',
    untitled: 'Sin título',
    photos: 'Fotos',
    edit: 'Editar',
    photosFromAnAlbum: 'Álbumes',
    show: 'Mostrar',
    rows: 'filas',
    cancel: 'Cancelar',
    save: 'Guardar',
    deleteThisPhoto: '¿Eliminar esta foto?',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'El número de caracteres (' + n + ') sobrepasa el máximo (' + maximum + ') '; },
    weCouldNotLookUpAddress: function(address) { return 'Lamentablemente no pudimos buscar la dirección "' + address + '". '; },
    pleaseSelectPhotoToUpload: 'Seleccioná una foto para subir.',
    pleaseEnterAComment: 'Ingresá un comentario.',
    addToExistingAlbum: 'Agregar al álbum existente',
    addToNewAlbumTitled: 'Agregar a un nuevo álbum llamado…',
    deleteThisComment: '¿Eliminar este comentario?',
    importingNofMPhotos: function(n,m) { return 'Importando <span id="currentP">' + n + '</span> de ' + m + ' fotos. '},
    starting: 'Comenzando…',
    done: '¡Listo!',
    from: 'De',
    display: 'Pantalla',
    takingYou: 'Llevándote a ver tus fotos…',
    anErrorOccurred: 'Lamentablemente, se produjo un error. Informá acerca del problema por medio del enlace que se encuentra en la parte inferior de la página.',
    weCouldntFind: 'No pudimos encontrar ninguna foto. ¿Por qué no intentás con alguna de las otras opciones?'
});


dojo.evalObjPath('xg.activity.nls', true);
dojo.lang.mixin(xg.activity.nls, xg.index.i18n, {
    edit: 'Editar',
    show: 'Mostrar',
    events: 'eventos',
    setWhatActivityGetsDisplayed: 'Configurar qué actividad se muestra',
    save: 'Guardar',
    cancel: 'Cancelar'
});


dojo.evalObjPath('xg.profiles.nls', true);
dojo.lang.mixin(xg.profiles.nls, xg.index.i18n, {
    pleaseEnterValueForPost: 'Ingresá un valor para la publicación',
    pleaseProvideAValidDate: 'Elegí una fecha válida',
    uploadAFile: 'Subir un archivo',
    pleaseEnterUrlOfLink: 'Ingresá la dirección URL del vínculo:',
    pleaseEnterTextOfLink: '¿Qué texto querés vincular?',
    edit: 'Editar',
    recentlyAdded: 'Agregado recientemente',
    featured: 'Destacados',
    iHaveRecentlyAdded: 'Lo agregué recientemente',
    fromTheSite: 'De la red social',
    cancel: 'Cancelar',
    save: 'Guardar',
    loading: 'Cargando...',
    addAsFriend: 'Agregar como amigo',
    removeAsFriend: 'Eliminar como amigo',
    requestSent: '¡Pedido enviado!',
    sendingFriendRequest: 'Enviando pedido a amigo',
    thisIsYou: '¡Sos vos!',
    isYourFriend: 'Es tu amigo',
    isBlocked: 'Está bloqueado',
    pleaseEnterAComment: 'Ingresá un comentario',
    pleaseEnterPostBody: 'Ingresá algo en el cuerpo de la publicación',
    pleaseSelectAFile: 'Seleccioná un archivo',
    pleaseEnterChatter: 'Ingresá algo para el comentario',
    toggleBetweenHTML: 'Mostrar/ocultar código HTML',
    attachAFile: 'Adjuntar un archivo',
    addAPhoto: 'Agregar una foto',
    insertALink: 'Ingresar un vínculo',
    changeTextSize: 'Cambiar tamaño del texto',
    makeABulletedList: 'Crear lista con viñetas',
    makeANumberedList: 'Crear lista numerada',
    crossOutText: 'Tachar texto',
    underlineText: 'Subrayar texto',
    italicizeText: 'Itálicas',
    boldText: 'Negritas',
    letMeApproveChatters: '¿Permitirme aprobar los comentarios para su publicación?',
    noPostChattersImmediately: 'No: publicar comentarios inmediatamente',
    yesApproveChattersFirst: 'Sí: aprobar comentarios primero',
    yourCommentMustBeApproved: 'Tu comentario tiene que ser aprobado antes de que todos puedan verlo.',
    reallyDeleteThisPost: '¿Estás seguro de que querés eliminar esta publicación?',
    commentWall: 'Comentarios',
    commentWallNComments: function(n) {
        switch(n) {
            case 1: return 'Comentarios (1 comentario) ';
            default: return 'Comentarios (' + n + ' comentarios) ';
        }
    },
    display: 'Pantalla',
    from: 'De',
    show: 'Mostrar',
    rows: 'filas',
    posts: 'publicaciones',
    returnToDefaultWarning: 'Esto hará que todas las características y el tema de Mi Página vuelvan al predeterminado de la red. ¿Querés continuar?',
    networkError: 'Error de red',
    wereSorry: 'Lo lamentamos, pero no podemos guardar tu nuevo diseño en este momento. Probablemente sea porque perdiste la conexión a Internet. Por favor, revisá tu conexión e intentá otra vez.'
});


dojo.evalObjPath('xg.shared.nls', true);
dojo.lang.mixin(xg.shared.nls, xg.index.i18n, {
    removeFriendTitle: '¿Eliminar como amigo?',
    removeAsFriend: 'Eliminar como amigo',
    removeFriendConfirm: '¿Estás seguro de que querés eliminar a esta persona como amigo?',
    locationNotFound: function(location) { return '<em>' + location + '</em> no encontrada. '; },
    confirmation: 'Confirmación',
    showMap: 'Mostrar Mapa',
    hideMap: 'Ocultar Mapa',
    yourCommentMustBeApproved: 'Tu comentario tiene que ser aprobado antes de que todos puedan verlo.',
    nComments: function(n) {
	    switch(n) {
	        case 1: return '1 Comentario ';
	        default: return n + ' Comentarios ';
	    }
	},
    uploadAFile: 'Subir un archivo',
    addExistingFile: 'o insertá un archivo existente',
    uploadAPhoto: 'Subir una foto',
    uploadAnImage: 'Subir una imagen',
    uploadAPhotoEllipsis: 'Subir una foto…',
    useExistingImage: 'Usar una imagen existente:',
    existingImage: 'Imagen existente',
    useThemeImage: 'Usar imagen del tema:',
    themeImage: 'Imagen del tema',
    noImage: 'Ninguna imagen',
    uploadImageFromComputer: 'Subí una imagen desde tu PC',
    tileThisImage: 'Mosaico de esta imagen',
    done: 'Listo',
    currentImage: 'Imagen actual',
    pickAColor: 'Elegir un color…',
    openColorPicker: 'Abrir selector de colores',
    loading: 'Cargando...',
    ok: 'OK',
    save: 'Guardar',
    cancel: 'Cancelar',
    saving: 'Guardando…',
    addAnImage: 'Agregar una imagen',
    bold: 'Negrita',
    italic: 'Itálica',
    underline: 'Subrayar',
    strikethrough: 'Tachar',
    addHyperink: 'Agregar hipervínculo',
    options: 'Opciones',
    wrapTextAroundImage: '¿Ajustar texto alrededor de la imagen?',
    imageOnLeft: '¿Imagen a la izquierda?',
    imageOnRight: '¿Imagen a la derecha?',
    createThumbnail: '¿Crear vista en miniatura?',
    pixels: 'píxeles',
    createSmallerVersion: 'Crear una versión más pequeña de la imagen para mostrar. Configurar el ancho en píxeles.',
    popupWindow: '¿Ventana emergente?',
    linkToFullSize: 'Vincular a versión en tamaño completo de la imagen en una ventana emergente.',
    add: 'Agregar',
    keepWindowOpen: 'Por favor dejá esta ventana del explorador abierta mientras el archivo se termina de cargar.',
    cancelUpload: 'Cancelar la carga',
    pleaseSelectAFile: 'Seleccioná un archivo de imagen',
    pleaseSpecifyAThumbnailSize: 'Especificá el tamaño de la vista en miniatura',
    thumbnailSizeMustBeNumber: 'El tamaño de la vista en miniatura debe ser un número',
    addExistingImage: 'o insertá una imagen existente',
    clickToEdit: 'Hacé clic para editar',
    sendingFriendRequest: 'Enviando pedido a amigo',
    requestSent: '¡Pedido enviado!',
    pleaseCorrectErrors: 'Corregí estos errores',
    tagThis: 'Etiquetar esto',
    addOrEditYourTags: 'Agregá o editá tus etiquetas:',
    addYourRating: 'Agregá tu calificación:',
    separateMultipleTagsWithCommas: 'Separar etiquetas múltiples con comas, por ej., buenísimo "nueva zelandia"',
    saved: '¡Guardado!',
    noo: 'NUEVO',
    none: 'NINGUNO',
    joinNow: 'Unirse ahora',
    join: 'Unite',
    youHaventRated: 'Todavía no calificaste este artículo.',
    yourRatedThis: function(n) {
        switch(n) {
            case 1: return 'Calificaste este artículo con 1 estrella. ';
            default: return 'Calificaste este artículo con ' + n + ' estrellas. ';
        }
    },
    yourRatingHasBeenAdded: 'Se agregó tu calificación.',
    thereWasAnErrorRating: 'Se produjo un error al calificar este contenido.',
    yourTagsHaveBeenAdded: 'Se agregaron tus etiquetas.',
    thereWasAnErrorTagging: 'Se produjo un error al agregar tus etiquetas.',
    addToFavorites: 'Agregar a favoritos',
    removeFromFavorites: 'Quitar de favoritos',
    nStarsOutOfM: function(n,m) {
        switch(n) {
            case 1: return '1 estrella de ' + m;
            default: return n + 'estrellas de ' + m;
        }
    },
    follow: 'Seguir',
    stopFollowing: 'Dejar de seguir',
    pendingPromptTitle: 'Membresía pendiente de aprobación',
    youCanDoThis: 'Podrás hacer esto una vez que los administradores hayan aprobado tu membresía.',
    yourMessage: 'Tu mensaje',
    updateMessage: 'Actualizar el mensaje',
    updateMessageQ: '¿Actualizar el mensaje?',
    removeWords: 'Para asegurarte de que el mensaje se entregó correctamente, te recomendamos volver y cambiar o eliminar las siguientes palabras:',
    warningMessage: 'parece que hay algunas palabras en este mensaje de correo electrónico que pueden hacer que tu mensaje vaya a la carpeta de Spam.',
    errorMessage: 'Hay 6 palabras o más en este mensaje de correo electrónico que pueden hacer que tu mensaje vaya a la carpeta de Spam',
    goBack: 'Volver',
    sendAnyway: 'Enviar',
    editYourTags: 'Editar tus etiquetas',
    addTags: 'Agregar etiquetas',
    editLocation: 'Editar lugar',
    editTypes: 'Editar tipo de evento',
    messageIsTooLong: function (n,m) { return 'Lo lamentamos. la cantidad máxima de caracteres es '+m+'.'; },
    pleaseEnterAComment: 'Ingresá un comentario',
    pleaseEnterAFileAddress: 'Ingresá la dirección del archivo',
    pleaseEnterAWebsite: 'Ingresá una dirección de página web válida'
});


dojo.evalObjPath('xg.video.nls', true);
dojo.lang.mixin(xg.video.nls, xg.index.i18n, {
    edit: 'Editar',
    display: 'Pantalla',
    detail: 'Detalles',
    player: 'Reproductor',
    from: 'De',
    show: 'Mostrar',
    videos: 'videos',
    cancel: 'Cancelar',
    save: 'Guardar',
    saving: 'Guardando…',
    deleteThisVideo: '¿Eliminar este video?',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'El número de caracteres (' + n + ') sobrepasa el máximo (' + maximum + ') '; },
    weCouldNotLookUpAddress: function(address) { return 'Lamentablemente no pudimos buscar la dirección "' + address + '". '; },
    approve: 'Aprobar',
    approving: 'Aprobando...',
    keepWindowOpenWhileApproving: 'Mantené abierta esta ventana del explorador mientras se están aprobando los videos. Este proceso puede tardar unos minutos.',
    'delete': 'Eliminar',
    deleting: 'Eliminando...',
    keepWindowOpenWhileDeleting: 'Mantené abierta esta ventana del explorador mientras se están eliminando los videos. Este proceso puede tardar unos minutos.',
    pasteInEmbedCode: 'Video de otro sitio: pegá el código incrustado',
    pleaseSelectVideoToUpload: 'Seleccioná el video que quieras subir.',
    embedCodeContainsMoreThanOneVideo: 'El código incrustado tiene más de un video. Fijate que tenga una sola etiqueta <object> y/o <embed>.',
    embedCodeMissingTag: 'Al código incrustado le falta una etiqueta &lt;embed&gt; o &lt;object&gt;.',
    fileIsNotAMov: 'El archivo no parece ser un .mov, .mpg, .mp4, .avi, .3gp o .wmv. ¿Querés intentar subirlo de todas formas?',
    pleaseEnterAComment: 'Ingresá un comentario.',
    youRatedVideoNStars: function(n) {
        switch(n) {
            case 1: return 'Calificaste este video con 1 estrella ';
            default: return 'Calificaste este video con ' + n + ' estrellas ';
        }
    },
    deleteThisComment: '¿Eliminar este comentario?',
    embedHTMLCode: 'Código HTML:',
    copyHTMLCode: 'Copiar código HTML',
    directLink: 'Vínculo directo',
    addToMyspace: 'Agregar a MySpace',
    shareOnFacebook: 'Compartir en facebook',
    addToOthers: 'Agregar a Otros'
});


dojo.evalObjPath('xg.gadgets.nls', true);
dojo.lang.mixin(xg.gadgets.nls, xg.index.i18n, {
    edit: 'Editar',
    title: 'Título:',
    feedUrl: 'URL:',
    cancel: 'Cancelar',
    save: 'Guardar',
    loading: 'Cargando...',
    removeGadget: 'Eliminar gadget',
    findGadgetsInDirectory: 'Buscar gadgets en el Directorio de gadgets'
});


dojo.evalObjPath('xg.uploader.nls', true);
dojo.lang.mixin(xg.uploader.nls, xg.index.i18n, {
    fileBrowserHeader: 'Mi Computadora',
    fileRoot: 'Mi Computadora',
    fileInformationHeader: 'Información',
    uploadHeader: 'Archivos para subir',
    dragOutInstructions: 'Arrastrá los archivos hacia afuera para sacarlos',
    dragInInstructions: 'Arrastrá los archivos hasta acá',
    selectInstructions: 'Elegí un archivo',
    files: 'Archivos',
    totalSize: 'Tamaño total',
    fileName: 'Nombre',
    fileSize: 'Tamaño',
    nextButton: 'Siguiente >',
    okayButton: 'OK',
    yesButton: 'Sí',
    noButton: 'No',
    uploadButton: 'Cargar',
    cancelButton: 'Cancelar',
    backButton: 'Atrás',
    continueButton: 'Continuar',
    uploadingLabel: 'Subiendo...',
    uploadingStatus: function(n, m) { return 'Subiendo ' + n + ' de ' + m; },
    uploadingInstructions: 'Por favor, dejá esta ventana abierta mientras termina la carga.',
    uploadLimitWarning: function(n) { return 'Podés subir ' + n + ' archivos por vez. '; },
    uploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Ya agregaste la cantidad máxima de archivos. ';
            case 1: return 'Podés subir un archivo más. ';
            default: return 'Podés subir ' + n + ' archivos más. ';
        }
    },
    iHaveTheRight: 'Tengo derecho a subir estos archivos según los<a href="/main/authorization/termsOfService">Términos del servicio</a>',
    updateJavaTitle: 'Actualizar Java',
    updateJavaDescription: 'El cargador masivo requiere de una versión de Java más reciente.  Hacé clic en "Aceptar" para descargar Java.',
    batchEditorLabel: 'Editar la Información de Todos los elementos',
    applyThisInfo: 'Aplicar esta información a los siguientes archivos',
    titleProperty: 'Título',
    descriptionProperty: 'Descripción',
    tagsProperty: 'Etiquetas',
    viewableByProperty: 'Pueden ser vistas por',
    viewableByEveryone: 'Cualquiera',
    viewableByFriends: 'Sólo mis amigos',
    viewableByMe: 'Sólo yo',
    albumProperty: 'Álbum',
    artistProperty: 'Artista',
    enableDownloadLinkProperty: 'Habilitar un vínculo para descargas',
    enableProfileUsageProperty: 'Permitir a usuarios poner esta canción en sus páginas',
    licenseProperty: 'Licencia',
    creativeCommonsVersion: '3.0',
    selectLicense: '— Elegir licencia —',
    copyright: '© Todos los derechos reservados',
    ccByX: function(n) { return 'Atribuciones creativas comunes ' + n; },
    ccBySaX: function(n) { return 'Atribuciones creativas comunes compartir similares ' + n; },
    ccByNdX: function(n) { return 'Atribuciones creativas comunes sin derivaciones ' + n; },
    ccByNcX: function(n) { return 'Atribuciones creativas comunes no comerciales ' + n; },
    ccByNcSaX: function(n) { return 'Atribuciones creativas comunes no comerciales compartir similares ' + n; },
    ccByNcNdX: function(n) { return 'Atribuciones creativas comunes no comerciales sin derivaciones ' + n; },
    publicDomain: 'Dominio público',
    other: 'Otros',
    errorUnexpectedTitle: 'Uhh!',
    errorUnexpectedDescription: 'Hubo un error. Por favor, intentá de nuevo.',
    errorTooManyTitle: 'Demasiados elementos',
    errorTooManyDescription: function(n) { return 'Lo lamentamos, pero sólo podés subir' + n + ' elementos por vez. '; },
    errorNotAMemberTitle: 'No está permitido',
    errorNotAMemberDescription: 'Lo lamentamos, pero tenés que ser miembro para subir elementos.',
    errorContentTypeNotAllowedTitle: 'No está permitido',
    errorContentTypeNotAllowedDescription: 'Lo lamentamos, pero no tenés permitido cargar este tipo de contenido.',
    errorUnsupportedFormatTitle: 'Uhh!',
    errorUnsupportedFormatDescription: 'Lo lamentamos, no admitimos este tipo de archivo.',
    errorUnsupportedFileTitle: 'Uhh!',
    errorUnsupportedFileDescription: 'foo.exe es un formato no admitido.',
    errorUploadUnexpectedTitle: 'Uhh!',
    errorUploadUnexpectedDescription: function(file) {
		return file ?
			('Parece que hay un problema con el archivo  ' + file + '. Por favor, sacalo de la lista antes de cargar el resto de los archivos.') :
			'Parece que hay un problema con el primer archivo de la lista. Por favor sacalo antes de cargar el resto de los archivos.';
	},
    cancelUploadTitle: '¿Cancelar la carga?',
    cancelUploadDescription: '¿Estás seguro de que querés cancelar las cargas pendientes?',
    uploadSuccessfulTitle: 'Carga completa',
    uploadSuccessfulDescription: 'Por favor, esperá mientras te llevamos hasta lo que cargaste...',
    uploadPendingDescription: 'Tus archivos se cargaron correctamente y están esperando ser aprobados.',
    photosUploadHeader: 'Fotos a cargar',
    photosDragOutInstructions: 'Arrastrá las fotos hacia afuera para sacarlas',
    photosDragInInstructions: 'Arrastrá las fotos hasta acá',
    photosSelectInstructions: 'Elegí una foto',
    photosFiles: 'Fotos',
    photosUploadingStatus: function(n, m) { return 'Cargando foto ' + n + ' de ' + m; },
    photosErrorTooManyTitle: 'Demasiadas fotos',
    photosErrorTooManyDescription: function(n) { return 'Lo lamentamos, pero sólo podés cargar ' + n + ' fotos por vez. '; },
    photosErrorContentTypeNotAllowedDescription: 'Lo lamentamos, pero se desactivó la opción subir fotos.',
    photosErrorUnsupportedFormatDescription: 'Lo lamentamos, pero sólo podés subir imágenes en formato .jpg, .gif o .png.',
    photosErrorUnsupportedFileDescription: function(n) { return n + ' no es un archivo .jpg, .gif o .png.'; },
    photosBatchEditorLabel: 'Editar la información para todas las fotos',
    photosApplyThisInfo: 'Aplicar esta información a las siguientes fotos',
    photosErrorUploadUnexpectedDescription: function(file) {
		return file ?
			('Parece que hay un problema con el archivo' + file + '. Por favor, sacalo de la lista antes de cargar el resto de las fotos.') :
			'Parece que hay un problema con la primera foto de la lista. Por favor, sacala antes de cargar el resto de las fotos.';
	},
    photosUploadSuccessfulDescription: 'Por favor, esperá mientras te llevamos hasta tus fotos...',
    photosUploadPendingDescription: 'Tus fotos se cargaron correctamente y están esperando ser aprobadas.',
    photosUploadLimitWarning: function(n) { return 'Podés subir ' + n + ' fotos por vez. '; },
    photosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Ya agregaste la cantidad máxima de fotos. ';
            case 1: return 'Podés subir una foto más. ';
            default: return 'Podés subir ' + n + ' fotos más. ';
        }
    },
    photosIHaveTheRight: 'Tengo derecho a cargar estas fotos según los<a href="/main/authorization/termsOfService">Términos del servicio</a>',
    videosUploadHeader: 'Videos para cargar',
    videosDragInInstructions: 'Arrastrá los videos hasta acá',
    videosDragOutInstructions: 'Arrastrá los videos hacia afuera para sacarlos',
    videosSelectInstructions: 'Elegí un video',
    videosFiles: 'Videos',
    videosUploadingStatus: function(n, m) { return 'Cargando Video ' + n + ' de ' + m; },
    videosErrorTooManyTitle: 'Demasiados videos',
    videosErrorTooManyDescription: function(n) { return 'Lo lamentamos, pero sólo podés cargar' + n + ' videos por vez. '; },
    videosErrorContentTypeNotAllowedDescription: 'Lo lamentamos, pero se desactivó la carga de videos.',
    videosErrorUnsupportedFormatDescription: 'Lo lamentamos, pero sólo podés subir videos en formato .avi, .mov, .mp4, .wmv o .mpg.',
    videosErrorUnsupportedFileDescription: function(x) { return x + ' no es un archivo .avi, .mov, .mp4, .wmv o .mpg.'; },
    videosBatchEditorLabel: 'Editar la información de Todos los videos',
    videosApplyThisInfo: 'Aplicar esta información a los siguientes videos',
    videosErrorUploadUnexpectedDescription: function(file) {
		return file ?
			('Parece que hay un problema con el archivo' + file + '. Por favor, sacalo de la lista antes de cargar el resto de los videos.') :
			'Parece que hay un problema con el primer video de la lista. Por favor, sacalo antes de cargar el resto de los videos.';
	},
    videosUploadSuccessfulDescription: 'Por favor, esperá mientras te llevamos hasta tus videos...',
    videosUploadPendingDescription: 'Tus videos se cargaron correctamente y están esperando ser aprobados.',
    videosUploadLimitWarning: function(n) { return 'Podés subir ' + n + ' videos por vez. '; },
    videosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Ya agregaste la cantidad máxima de videos. ';
            case 1: return 'Podés subir un video más. ';
            default: return 'Podés subir ' + n + ' videos más. ';
        }
    },
    videosIHaveTheRight: 'Tengo derecho a subir estos videos según los<a href="/main/authorization/termsOfService">Términos del servicio</a>',
    musicUploadHeader: 'Canciones para subir',
    musicTitleProperty: 'Canción',
    musicDragOutInstructions: 'Arrastrá las canciones hacia afuera para sacarlas',
    musicDragInInstructions: 'Arrastrá las canciones hasta acá',
    musicSelectInstructions: 'Elegí una Canción',
    musicFiles: 'Canciones',
    musicUploadingStatus: function(n, m) { return 'Cargando canción ' + n + ' de ' + m; },
    musicErrorTooManyTitle: 'Demasiadas canciones',
    musicErrorTooManyDescription: function(n) { return 'Lo lamentamos, pero sólo podemos cargar ' + n + ' canciones por vez. '; },
    musicErrorContentTypeNotAllowedDescription: 'Lo lamentamos, pero se desactivó la carga de canciones.',
    musicErrorUnsupportedFormatDescription: 'Lo lamentamos, pero sólo podés cargar canciones en formato .mp3.',
    musicErrorUnsupportedFileDescription: function(x) { return x + ' no es un archivo .mp3.'; },
    musicBatchEditorLabel: 'Editar la información para Todas las canciones',
    musicApplyThisInfo: 'Aplicar esta información a las siguientes canciones',
    musicErrorUploadUnexpectedDescription: function(file) {
		return file ?
			('Parece que hay un problema con el archivo ' + file + '. Por favor, sacalo de la lista antes de cargar el resto de las canciones.') :
			' Parece que hay un problema con la primera canción de la lista. Por favor, sacala antes de cargar el resto de las canciones.';
	},
    musicUploadSuccessfulDescription: 'Por favor, esperá mientras te llevamos hasta tus canciones...',
    musicUploadPendingDescription: 'Tus canciones se cargaron correctamente y están esperando ser aprobadas.',
    musicUploadLimitWarning: function(n) { return 'Podés subir ' + n + ' canciones por vez. '; },
    musicUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Ya agregaste la cantidad máxima de canciones. ';
            case 1: return 'Podés subir 1 canción más. ';
            default: return 'Podés subir ' + n + ' canciones más. ';
        }
    },
    musicIHaveTheRight: 'Tengo derecho a subir estas canciones según los<a href="/main/authorization/termsOfService">Términos del servicio</a>'
});


dojo.evalObjPath('xg.notes.nls', true);
dojo.lang.mixin(xg.notes.nls, xg.index.i18n, {
    addNewNote: 'Agregar una nota nueva',
    noteTitleTooLong: 'El título de la nota es demasiado largo',
    pleaseEnterNoteEntry: 'Ingresá una entrada de nota',
    pleaseEnterNoteTitle: 'Por favor, ingresá un título para la nota'
});


dojo.evalObjPath('xg.events.nls', true);
dojo.lang.mixin(xg.events.nls, xg.index.i18n, {
    sendMessageToGuests: 'Enviar un mensaje a los invitados',
    sendMessageToGuestsThat: 'Enviar un mensaje a los invitados que:',
    areAttending: 'Van',
    mightAttend: 'Tal vez vayan',
    haveNotYetRsvped: 'No RSVP todavía',
    areNotAttending: 'No van',
    messageSent: '¡Se envió el mensaje!',
    chooseRecipient: 'Por favor elegí un destinatario.',
    messageIsTooLong: function(n) { return 'Tu mensaje es demasiado largo. Por favor, usá '+n+' caracteres o menos.'; },
    pleaseChooseImage: 'Por favor, elegí una imagen para el evento',
    pleaseEnterAMessage: 'Por favor, ingresá un mensaje',
    pleaseEnterDescription: 'Por favor, ingresá una descripción para el evento',
    pleaseEnterLocation: 'Por favor, ingresá un lugar para el evento',
    pleaseEnterTitle: 'Por favor, ingresá un título para el evento',
    pleaseEnterType: 'Por favor, ingresá por lo menos un tipo para el evento',
    send: 'Enviar',
    sending: 'Enviando...',
    thereHasBeenAnError: 'Se produjo un error',
    yourMessage: 'Tu mensaje',
    yourMessageHasBeenSent: 'Se envió tu mensaje.',
    yourMessageIsBeingSent: 'Tu mensaje se está enviando.'
});