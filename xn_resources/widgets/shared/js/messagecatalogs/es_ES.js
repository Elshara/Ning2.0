dojo.provide('xg.shared.messagecatalogs.es_ES');

dojo.require('xg.index.i18n');

/**
 * Texts for the es_ES locale. 
 */
// Use UTF-8 byte sequences instead of HTML entities, e.g., & instead of &amp;, … instead of &hellip;  [Jon Aquino 2007-01-10]

dojo.evalObjPath('xg.events.nls', true);
dojo.lang.mixin(xg.events.nls, xg.index.i18n, {
    pleaseChooseImage: 'Por favor, elige una imagen para el evento',
    pleaseEnterAMessage: 'Introduce un mensaje',
    pleaseEnterDescription: 'Introduce una descripción del evento',
    pleaseEnterLocation: 'Introduce un lugar para el evento',
    pleaseEnterTitle: 'Escribe un título para el evento',
    pleaseEnterType: 'Introduce al menos un tipo de evento',
    send: 'Enviar',
    sending: 'Enviando…',
    thereHasBeenAnError: 'Se ha producido un error',
    yourMessage: 'Tu mensaje',
    yourMessageHasBeenSent: 'Tu mensaje ha sido enviado.',
    yourMessageIsBeingSent: 'Se está enviando tu mensaje.'
});

dojo.evalObjPath('xg.feed.nls', true);
dojo.lang.mixin(xg.feed.nls, xg.index.i18n, {
    edit: 'Edición',
    title: 'Título:',
    feedUrl: 'Dirección URL:',
    show: 'Mostrar:',
    titles: 'Sólo títulos',
    titlesAndDescriptions: 'Ver detalles',
    display: 'Pantalla',
    cancel: 'Cancelar',
    save: 'Guardar',
    loading: 'Cargando…',
    items: 'elementos'
});


dojo.evalObjPath('xg.forum.nls', true);
dojo.lang.mixin(xg.forum.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'El número de caracteres (' + n + ') excede el máximo (' + maximum + ') '; },
    pleaseEnterFirstPost: 'Añade el primer post de esta discusión.',
    pleaseEnterTitle: 'Título',
    save: 'Guardar',
    cancel: 'Cancelar',
    yes: 'Sí',
    no: 'No',
    edit: 'Edición',
    deleteCategory: 'Eliminar categoría',
    discussionsWillBeDeleted: 'Se eliminarán las discusiones de esta categoría.',
    whatDoWithDiscussions: '¿Qué quieres hacer con las discusiones de esta categoría?',
    moveDiscussionsTo: 'Mover discusiones a:',
    moveToCategory: 'Mover a categoría…',
    deleteDiscussions: 'Eliminar discusiones',
    'delete': 'Eliminar',
    deleteReply: 'Borrar respuesta',
    deleteReplyQ: '¿Eliminar esta respuesta?',
    deletingReplies: 'Eliminando respuesta…',
    doYouWantToRemoveReplies: '¿También quieres eliminar las respuestas a este comentario?',
    pleaseKeepWindowOpen: 'Por favor deja esta ventana del navegador abierta mientras continúa el proceso. Este proceso puede tardar unos minutos.',
    from: 'De',
    show: 'Mostrar',
    discussions: 'Discusiones',
    discussionsFromACategory: 'Discusiones de una categoría…',
    display: 'Pantalla',
    items: 'artículos',
    view: 'Ver'
});


dojo.evalObjPath('xg.groups.nls', true);
dojo.lang.mixin(xg.groups.nls, xg.index.i18n, {
    pleaseChooseAName: 'Elige un nombre para tu grupo.',
    pleaseChooseAUrl: 'Elige una dirección de Web para tu grupo.',
    urlCanContainOnlyLetters: 'La dirección de Web puede contener sólo letras y números (sin espacios).',
    descriptionTooLong: function(n, maximum) { return 'El tamaño de la descripción de tu grupo (' + n + ') excede el máximo (' + maximum + ') '; },
    nameTaken: 'Ese nombre ya existe. Por favor elige otro nombre.',
    urlTaken: 'Esa dirección ya existe. Por favor elige otra.',
    whyNot: '¿Por qué no?',
    groupCreatorDetermines: function(href) { return 'El creador del grupo determina quién tiene permiso para unirse. Si sientes que has sido bloqueado por error, por favor <a ' + href + '>ponte en contacto con el creador del grupo</a> '; },
    edit: 'Editar',
    from: 'De',
    show: 'Mostrar',
    groups: 'Grupos',
    pleaseEnterName: 'Nombre',
    pleaseEnterEmailAddress: 'Por favor introduce tu dirección de email.',
    xIsNotValidEmailAddress: function(x) { return x + ' is not a valid email address'; },
    save: 'Guardar',
    cancel: 'Cancelar'
});


dojo.evalObjPath('xg.html.nls', true);
dojo.lang.mixin(xg.html.nls, xg.index.i18n, {
	contentsTooLong: function(maximum) { return 'La frase es muy larga. Por favor, usa menos de ' + maximum + ' caracteres. '; },
    edit: 'Edición',
    save: 'Guardar',
    cancel: 'Cancelar',
    saving: 'Guardando…',
    addAWidget: function(url) { return '<a href="' + url + '">Añade un widget</a> a este cuadro de texto '; }
});


dojo.evalObjPath('xg.index.nls', true);
dojo.lang.mixin(xg.index.nls, xg.index.i18n, {
    sendInvitation: 'Enviar invitación',
    sendInvitationToNFriends: function(n) {
        switch(n) {
            case 1: return '¿Enviar invitación a 1 amigo? ';
            default: return '¿Enviar invitación a ' + n + ' amigos? ';
        }
    },
    yourMessageOptional: '<label>Tu mensaje</label> (Opcional)',
    pleaseChoosePeople: 'Por favor elige a la gente a la que quieres invitar.',
    pleaseEnterEmailAddress: 'Dirección de email',
    pleaseEnterPassword: function(emailAddress) { return 'Por favor escribe tu contraseña para ' + emailAddress + '. '; },
    sorryWeDoNotSupport: 'Lo sentimos, no admitimos la libreta de dirección de esta Web. Intenta hacer clic en  \'aplicación de email\' que está abajo para usar las direcciones de tu ordenador. ',
    sorryWeDontSupport: 'Lo sentimos, no soportamos la libreta de dirección de esta Web. Intenta hacer clic en  \'aplicación de email\' que está abajo para usar las direcciones de tu ordenador.',
    pleaseSelectSecondPart: 'Por favor elige la segunda parte de tu dirección de email, por ejemplo, gmail.com.',
    atSymbolNotAllowed: 'Por favor asegúrate que el símbolo @ no esté en la primera parte de la dirección email.',
    resetTextQ: '¿Reestablecer el texto?',
    resetTextToOriginalVersion: '¿Estás seguro que quieres reestablecer el texto a la versión original? Se perderán todos los cambios.',
    changeQuestionsToPublic: '¿Cambiar las preguntas a públicas?',
    changingPrivateQuestionsToPublic: 'Cambiar las preguntas privadas a públicas expondrá todas las respuestas de los miembros. ¿Estás seguro?',
    youHaveUnsavedChanges: 'Hay cambios sin guardar.',
    pleaseEnterASiteName: 'Por favor escribe el nombre de tu red social, por ejemplo, el Tiny Clown Club.',
    pleaseEnterShorterSiteName: 'Por favor introduce un nombre más corto (máximo 64 caracteres)',
    pleaseEnterShorterSiteDescription: 'Por favor introduce una descripción más corta (máximo 250 caracteres)',
    siteNameHasInvalidCharacters: 'El nombre tiene algunos caracteres inválidos',
    thereIsAProblem: 'Hay un problema con tu información',
    thisSiteIsOnline: 'Esta red social está conectada',
    onlineSiteCanBeViewed: '<strong>Conectada</strong> - Se puede ver la red social según la configuración de privacidad que tenga.',
    takeOffline: 'Desconectar',
    thisSiteIsOffline: 'Esta red social está desconectada',
    offlineOnlyYouCanView: '<strong>Desconectada</strong> - Sólo tú puedes ver la red social.',
    takeOnline: 'Conectar',
    themeSettings: 'Configuración de Tema',
    addYourOwnCss: 'Avanzado',
    error: 'Error',
    pleaseEnterTitleForFeature: function(displayName) { return 'Por favor escribe el título de tu función' + displayName + ' '; },
    thereIsAProblemWithTheInformation: 'Hay un problema con la información escrita',
    photos: 'Fotos',
    videos: 'Videos',
    pleaseEnterTheChoicesFor: function(questionTitle) { return 'Por favor escribe las opciones de "' + questionTitle + '" por ejemplo escalada, lectura, compras '; },
    pleaseEnterTheChoices: 'Por favor escribe las elecciones, por ejemplo, escalada, lectura, compras',
    shareWithFriends: 'Compartir con amigos',
    email: 'email',
    separateMultipleAddresses: 'Separa múltiples direcciones con comas',
    subject: 'Asunto',
    message: 'Mensaje',
    send: 'Enviar',
    cancel: 'Cancelar',
    pleaseEnterAValidEmail: 'Por favor escribe una dirección email válida',
    go: 'Ir a',
    areYouSureYouWant: '¿Estás seguro que quieres hacerlo?',
    processing: 'Procesando…',
    pleaseKeepWindowOpen: 'Por favor deja esta ventana del navegador abierta mientras continúa el proceso. Este proceso puede tardar unos minutos.',
    complete: '¡Completo!',
    processIsComplete: 'El proceso está completo.',
    ok: 'OK',
    body: 'Cuerpo',
    pleaseEnterASubject: 'Por favor introduce un asunto',
    pleaseEnterAMessage: 'Por favor introduce un mensaje',
    thereHasBeenAnError: 'Ha ocurrido un error',
    fileNotFound: 'No se ha encontrado el archivo',
    pleaseProvideADescription: 'Por favor indica una descripción',
    pleaseEnterYourFriendsAddresses: 'Por favor escribe las direcciones de tus amigos y sus IDs Ning',
    pleaseEnterSomeFeedback: 'Por favor escribe algún comentario',
    title: 'Título:',
    setAsMainSiteFeature: 'Configurar como característica principal',
    thisIsTheMainSiteFeature: 'Esta es la característica principal',
    customized: 'Personalizado',
    copyHtmlCode: 'Copiar código HTML',
    playerSize: 'Tamaño del reproductor',
    selectSource: 'Elegir base de datos de origen',
    myAlbums: 'Mis álbumes',
    myMusic: 'Mi música',
    myVideos: 'Mis videos',
    showPlaylist: 'Mostrar lista de reproducción',
    change: 'Cambiar',
    changing: 'Cambiando...',
    changePrivacy: '¿Cambiar privacidad?',
    keepWindowOpenWhileChanging: 'Por favor deja esta ventana del navegador abierta mientras se cambia la configuración de privacidad. Este proceso puede tardar unos minutos.',
    htmlNotAllowed: 'No se permite HTML',
    showingNFriends: function(n, searchString) {
        switch(n) {
            case 1: return 'Mostrar a un amigo que coincida con "' + searchString + '". <a href="#">Mostrar a todo el mundo</a> ';
            default: return 'Mostar a ' + n + ' amigos que coincidan con "' + searchString + '". <a href="#">Mostrar a todo el mundo</a> ';
        }
    },
    sendMessage: 'Enviar mensaje',
    sendMessageToNFriends: function(n) {
        switch(n) {
            case 1: return '¿Mandar un mensaje a un amigo? ';
            default: return '¿Mandar un mensaje a ' + n + ' amigos? ';
        }
    },
    invitingNFriends: function(n) {
        switch(n) {
            case 1: return 'Invitar a un amigo… ';
            default: return 'Invitar a ' + n + ' amigos… ';
        }
    },
    nFriends: function(n) {
        switch(n) {
            case 1: return '1 amigo… ';
            default: return n + ' Amigos… ';
        }
    },
    sendingMessageToNFriends: function(n) {
        switch(n) {
            case 1: return 'Enviando un mensaje a un amigo… ';
            default: return 'Enviando un mensaje a  ' + n + ' amigos… ';
        }
    },
    noPeopleSelected: 'No se ha seleccionado a nadie',
    pleaseChooseFriends: 'Por favor elijge a algunos amigos antes de mandar tu mensaje.',
    noFriendsFound: 'No se han encontrado amigos que coincidan con tu búsqueda.',
    addingInstructions: 'Por favor deja abierta esta ventana mientras se agrega tu contenido.',
    addingLabel: 'Agregando… .',
    cannotKeepFiles: 'Tendrás que elegir tus archivos otra vez si quieres ver más opciones.  ¿Quieres continuar?',
    done: 'Listo',
    looksLikeNotImage: 'Parece ser que uno o más archivos no están en formato .jpg, .gif o .png.  ¿Quieres intentar subirlo de todas formas?',
    looksLikeNotMusic: 'El archivo que has seleccionado no parece estar en formato .mp3.  ¿Quieres intentar subirlo de todas formas?',
    looksLikeNotVideo: 'El archivo que has seleccionado no parece tener formato .mov, .mpg, .mp4, .3gp o .wmv.  ¿Quieres intentar subirlo de todas formas?',
    messageIsTooLong: function(n) {return 'Tu mensaje es demasiado largo.  Por favor usa '+n+' caracteres o menos.'; },
    pleaseSelectPhotoToUpload: 'Por favor elige una foto para subirla.',
    processingFailed: 'Lo sentimos, el proceso ha fallado.  Vuelve a intentarlo más adelante.',
    selectOrPaste: 'Tienes que elegir un vídeo o pegar el código ’embed’',
    selectOrPasteMusic: 'Tienes que elegir una canción o pegar la URL',
    sendingLabel: 'Enviando… .',
    thereWasAProblem: 'Ha habido un problema al agregar tu contenido.  Vuelve a intentarlo más adelante.',
    uploadingInstructions: 'Deja abierta esta ventana durante la subida.',
    uploadingLabel: 'Subiendo... .',
    youNeedToAddEmailRecipient: 'Tienes que agregar una dirección de correo electrónico.',
    yourMessage: 'Tu mensaje',
    yourMessageIsBeingSent: 'Se está enviando tu mensaje.',
    yourSubject: 'Tu tema'
});


dojo.evalObjPath('xg.music.nls', true);
dojo.lang.mixin(xg.music.nls, xg.index.i18n, {
    play: 'reproducir',
	shufflePlaylist: 'Mezcla la lista de canciones ',
    pleaseSelectTrackToUpload: 'Por favor elige la canción para cargarla.',
    pleaseEnterTrackLink: 'Por favor escribe la dirección URL de la canción.',
    thereAreUnsavedChanges: 'Hay cambios sin guardar.',
    autoplay: 'Reproducción automática',
    showPlaylist: 'Mostrar lista de reproducción',
    playLabel: 'Reproducir',
    url: '"URL, dirección URL"',
    rssXspfOrM3u: 'rss, xspf, o m3u',
    save: 'Guardar',
    cancel: 'Cancelar',
    edit: 'Edición',
    fileIsNotAnMp3: 'Parece ser que uno de los ficheros no es MP3. ¿Intentar cargar de todos modos?',
    entryNotAUrl: 'Una de las entradas no parece ser una dirección URL. Asegúrate de que todas las entradas empiecen con <kbd>http://</kbd>'
});

dojo.evalObjPath('xg.notes.nls', true);
dojo.lang.mixin(xg.notes.nls, xg.index.i18n, {
    addNewNote: 'Agregar una nota nueva',
    noteTitleTooLong: 'El título de la nota es muy largo',
    pleaseEnterNoteEntry: 'Por favor escribe una anotación en la nota',
    pleaseEnterNoteTitle: '¡Escribe un título para la nota!'
});

dojo.evalObjPath('xg.page.nls', true);
dojo.lang.mixin(xg.page.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'El número de caracteres (' + n + ') excede el máximo (' + maximum + ') '; },
    pleaseEnterContent: 'Ingresa el contenido de la página',
    pleaseEnterTitle: 'Por favor escribe el título de la página',
    pleaseEnterAComment: 'Por favor escribe un comentario',
    deleteThisComment: '¿Estás seguro de que quieres borrar este comentario?',
    save: 'Guardar',
    cancel: 'Cancelar',
    discussionTitle: 'Título de página:',
    tags: 'Etiquetas:',
    edit: 'Edición',
    close: 'Cerrar',
    displayPagePosts: 'Mostrar los posts de esta página',
    thereIsAProblem: 'Hay un problema con tu información'
});


dojo.evalObjPath('xg.photo.nls', true);
dojo.lang.mixin(xg.photo.nls, xg.index.i18n, {
    untitled: 'Sin título',
 	randomOrder: 'Orden aleatorio ',
    photos: 'Fotos',
    edit: 'Edición',
    photosFromAnAlbum: 'Álbumes',
    show: 'Mostrar',
    rows: 'Filas',
    cancel: 'Cancelar',
    save: 'Guardar',
    deleteThisPhoto: '¿Eliminar esta foto?',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'El número de caracteres (' + n + ') excede el máximo (' + maximum + ') '; },
    weCouldNotLookUpAddress: function(address) { return 'Lo sentimos no hemos podido buscar la dirección "' + address + '". '; },
    pleaseSelectPhotoToUpload: 'Por favor elige una foto para cargarla.',
    pleaseEnterAComment: 'Por favor escribe un comentario.',
    addToExistingAlbum: 'Añadir al álbum existente',
    addToNewAlbumTitled: 'Añadir a un nuevo título de álbum…',
    deleteThisComment: '¿Eliminar este comentario?',
      importingNofMPhotos: function(n,m) { return 'Importando <span id="currentP">' + n + '</span> de ' + m + ' fotos. '; },
    starting: 'Comenzando…',
    done: '¡Listo!',
    from: 'De',
    display: 'Pantalla',
    takingYou: 'Ir a ver tus fotos…',
    anErrorOccurred: 'Desgraciadamente ha ocurrido un error. Por favor haz un informe de este asunto haciendo clic en el enlace que está en la parte inferior de la página.',
    weCouldntFind: '¡No hemos encontrado ninguna foto! ¿Por qué no pruebas con una de las otras opciones?'
});


dojo.evalObjPath('xg.activity.nls', true);
dojo.lang.mixin(xg.activity.nls, xg.index.i18n, {
    edit: 'Edición',
    show: 'Mostrar',
    events: 'eventos',
    setWhatActivityGetsDisplayed: 'Configura qué actividad se visualiza',
    save: 'Guardar',
    cancel: 'Cancelar'
});


dojo.evalObjPath('xg.profiles.nls', true);
dojo.lang.mixin(xg.profiles.nls, xg.index.i18n, {
    pleaseEnterValueForPost: 'Por favor escribe un valor para el post',
    pleaseProvideAValidDate: 'Por favor indica una fecha válida',
    uploadAFile: 'Cargar un archivo',
    pleaseEnterUrlOfLink: 'Por favor escribe la dirección de URL del enlace:',
    pleaseEnterTextOfLink: '¿Qué texto quieres enlazar?',
    edit: 'Edición',
    recentlyAdded: 'Recientemente añadido',
    featured: 'Destacados',
    iHaveRecentlyAdded: 'Lo añadí recientemente',
    fromTheSite: 'De la red social',
    cancel: 'Cancelar',
    save: 'Guardar',
    loading: 'Cargando…',
    addAsFriend: 'Añadir como amigo',
    requestSent: '¡Pedido enviado!',
    sendingFriendRequest: 'Enviar una petición de amistad',
    thisIsYou: '¡Este eres tú!',
    isYourFriend: 'Es tu amigo',
    isBlocked: 'Está bloqueado',
    pleaseEnterAComment: 'Por favor escribe un comentario',
    pleaseEnterPostBody: 'Por favor introduce algo en el cuerpo del post',
    pleaseSelectAFile: 'Por favor elige un archivo',
    pleaseEnterChatter: 'Por favor introduce algo en tu comentario',
    toggleBetweenHTML: 'Mostrar/ocultar código HTML',
    attachAFile: 'Adjuntar fichero',
    addAPhoto: 'Añadir una foto',
    insertALink: 'Insertar un enlace',
    changeTextSize: 'Cambiar el tamaño del texto',
    makeABulletedList: 'Haz una lista con viñetas',
    makeANumberedList: 'Haz una lista con números',
    crossOutText: 'Tachar el texto',
    underlineText: 'Subrayar el texto',
    italicizeText: 'Escribir el texto en itálica',
    boldText: 'Texto en negrita',
    letMeApproveChatters: 'Permíteme aprobar los comentarios antes de publicarlos',
    noPostChattersImmediately: 'No – Publicar los comentarios inmediatamente',
    yesApproveChattersFirst: 'Sí – Aprobar los comentarios primero',
    yourCommentMustBeApproved: 'Se tienen que aprobar tus comentarios antes que todos puedan verlos.',
    reallyDeleteThisPost: '¿Quieres realmente eliminar este post?',
    commentWall: 'Comentarios',
    commentWallNComments: function(n) {
        switch(n) {
            case 1: return 'Comentario (1 comentario) ';
            default: return 'Comentario (' + n + ' comentarios) ';
        }
    },
    display: 'Pantalla',
    from: 'De',
    show: 'Mostrar',
    rows: 'Filas',
    posts: 'Posts'
});


dojo.evalObjPath('xg.shared.nls', true);
dojo.lang.mixin(xg.shared.nls, xg.index.i18n, {
    uploadAPhoto: 'Subir una foto',
    uploadAnImage: 'Cargar una imagen',
    uploadAPhotoEllipsis: 'Subir una foto…',
    useExistingImage: 'Usar una imagen que ya existe:',
    existingImage: 'Imagen existente',
    useThemeImage: 'Usar una imagen temática:',
    themeImage: 'Imagen temática',
    noImage: 'No hay imagen',
    uploadImageFromComputer: 'Carga una imagen de tu ordenador',
    tileThisImage: 'Ponle un título a esta imagen',
    done: 'Listo',
    currentImage: 'Imagen actual',
    pickAColor: 'Seleccionar el color…',
    openColorPicker: 'Abre el seleccionador de colores',
    loading: 'Cargando…',
    ok: 'OK',
    save: 'Guardar',
    cancel: 'Cancelar',
    saving: 'Guardando…',
    addAnImage: 'Añadir una imagen',
    bold: 'Negrita',
    italic: 'Itálica',
    underline: 'Subrayado',
    strikethrough: 'Tachado',
    addHyperink: 'Añadir enlace',
    options: 'Opciones',
    wrapTextAroundImage: '¿Rodear la imagen con texto?',
    imageOnLeft: '¿Poner la imagen a la izquierda?',
    imageOnRight: '¿Poner la imagen en la derecha?',
    createThumbnail: '¿Crear miniatura?',
    pixels: 'píxeles',
    createSmallerVersion: 'Crear una versión más pequeña de tu imagen para visualizarla. Configura la anchura en píxeles.',
    popupWindow: '¿Ventana emergente?',
    linkToFullSize: 'Enlace a una versión a tamaño real de la imagen en una ventana emergente.',
    add: 'Agregar',
    keepWindowOpen: 'Por favor deja esta ventana del navegador abierta mientras el archivo continuo cargando.',
    cancelUpload: 'Cancelar carga',
    pleaseSelectAFile: 'Por favor selecciona un archivo de imagen',
    pleaseSpecifyAThumbnailSize: 'Por favor especifica el tamaño de la miniatura',
    thumbnailSizeMustBeNumber: 'El tamaño de la miniatura tiene que ser un número',
    addExistingImage: 'o inserta una imagen existente',
    clickToEdit: 'Haz clic para editar',
    sendingFriendRequest: 'Enviar una petición de amistad',
    requestSent: '¡Pedido enviado!',
    pleaseCorrectErrors: 'Por favor corrige estos errores',
    tagThis: 'Ponle una etiqueta a esto',
    addOrEditYourTags: 'Añadir o editar tus etiquetas:',
    addYourRating: 'Añadir tu calificación:',
    separateMultipleTagsWithCommas: 'Separa las etiquetas múltiples con comas, por ejemplo guay, “nueva zelanda"',
    saved: '¡Guardado!',
    noo: 'NUEVO',
    none: 'NINGUNO',
    joinNow: 'Únete ahora',
    join: 'Únete',
    youHaventRated: 'Aún no has calificado este elemento.',
    yourRatedThis: function(n) {
        switch(n) {
            case 1: return 'Has calificado este elemento con 1 estrella. ';
            default: return 'Has calificado este elemento con ' + n + ' estrellas. ';
        }
    },
    yourRatingHasBeenAdded: 'Se ha añadido tu calificación.',
    thereWasAnErrorRating: 'Ha ocurrido un error en la calificación de este contenido.',
    yourTagsHaveBeenAdded: 'Se han añadido tus etiquetas.',
    thereWasAnErrorTagging: 'Ha habido un error al añadir las etiquetas.',
    addToFavorites: 'Añadir a favoritas',
    removeFromFavorites: 'Quitar de favoritos',
    nStarsOutOfM: function(n,m) {
        switch(n) {
            case 1: return '1 estrella de ' + m;
            default: return n + ' estrellas de ' + m;
        }
    },
    follow: 'Recibir Notificaciones',
    stopFollowing: 'Parar Notificaciones',
    pendingPromptTitle: 'Aprobación de membresía pendiente',
    youCanDoThis: 'Puedes hacerlo una vez que tu membresía ha sido aprobada por los administradores.',
    pleaseEnterAComment: 'Por favor escribe un comentario',
    pleaseEnterAFileAddress: 'Por favor escribe la dirección del archivo',
    pleaseEnterAWebsite: 'Por favor escribe una dirección de sitio Web'
});


dojo.evalObjPath('xg.video.nls', true);
dojo.lang.mixin(xg.video.nls, xg.index.i18n, {
    edit: 'Edición',
    display: 'Pantalla',
    detail: 'Detalles',
    player: 'Reproductor',
    from: 'De',
    show: 'Mostrar',
    videos: 'Videos',
    cancel: 'Cancelar',
    save: 'Guardar',
    saving: 'Guardando…',
    deleteThisVideo: '¿Eliminar este video?',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'El número de caracteres (' + n + ') excede el máximo (' + maximum + ') '; },
    weCouldNotLookUpAddress: function(address) { return 'Lo sentimos no hemos podido buscar la dirección "' + address + '". '; },
    approve: 'Aprobar',
    approving: 'Aprobando',
    keepWindowOpenWhileApproving: 'Por favor deja esta ventana del navegador abierta mientras los videos se aprueban. Este proceso puede tardar unos minutos.',
    'delete': 'Eliminar',
    deleting: 'Eliminando',
    keepWindowOpenWhileDeleting: 'Por favor deja esta ventana del navegador abierta mientras se borran los videos. Este proceso puede tardar unos minutos.',
    pasteInEmbedCode: 'Por favor pega el código insertado de un video de otro sitio Web.',
    pleaseSelectVideoToUpload: 'Por favor selecciona el video para cargarlo.',
    embedCodeContainsMoreThanOneVideo: 'El código insertado contiene más de un video. Por favor asegúrate que sólo tiene una etiqueta <object> y/o <embed>.',
    embedCodeMissingTag: 'Al código insertado le falta una etiqueta  &lt;embed&gt; o &lt;object&gt;.',
    fileIsNotAMov: 'No parece que este fichero sea .mov, .mpg, .mp4, .avi, .3gp o .wmv ¿Intentar cargarlo de todos modos?',
    pleaseEnterAComment: 'Por favor escribe un comentario.',
    youRatedVideoNStars: function(n) {
        switch(n) {
            case 1: return '¡Has calificado este video con 1 estrella! ';
            default: return '¡Has calificado este video con ' + n + ' estrellas! ';
        }
    },
    deleteThisComment: '¿Eliminar este comentario?',
    embedHTMLCode: 'Código HTML insertado:',
    copyHTMLCode: 'Copiar código HTML'
});


dojo.evalObjPath('xg.gadgets.nls', true);
dojo.lang.mixin(xg.gadgets.nls, xg.index.i18n, {
    edit: 'Edición',
    title: 'Título:',
    feedUrl: 'Dirección URL:',
    cancel: 'Cancelar',
    save: 'Guardar',
    loading: 'Cargando…',
    removeGadget: 'Quita gadget',
    findGadgetsInDirectory: 'Busca gadgets en el directorio de gadgets'
});

dojo.evalObjPath('xg.uploader.nls', true);
dojo.lang.mixin(xg.uploader.nls, xg.index.i18n, {
    fileBrowserHeader: 'Mi ordenador ',
    fileRoot: 'Mi ordenador ',
    fileInformationHeader: 'Información ',
    uploadHeader: 'Archivos que hay que cargar ',
    dragOutInstructions: 'Mueve los archivos que quieras eliminar ',
    dragInInstructions: 'Mueve los archivos aquí ',
    selectInstructions: 'Elige un archivo ',
    files: 'Archivos ',
    totalSize: 'Tamaño total ',
    fileName: 'Nombre ',
    fileSize: 'Tamaño ',
    nextButton: 'Siguiente > ',
    okayButton: 'OK ',
    yesButton: 'Sí ',
    noButton: 'No ',
    uploadButton: 'Cargar ',
    cancelButton: 'Cancelar ',
    backButton: 'Atrás ',
    continueButton: 'Continuar ',
    uploadingLabel: 'Cargando... ',
    uploadingStatus: function(n, m) { return 'Cargar ' + n + ' de ' + m; },
    uploadingInstructions: 'Por favor deja esta ventana abierta mientras se completa el proceso de carga. ',
    uploadLimitWarning: function(n) { return 'Puedes cargar ' + n + ' archivos a la vez. '; },
	uploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Has añadido el número máximo de archivos. ';
            case 1: return 'Puedes cargar 1 archivo más. ';
            default: return 'Puedes cargar ' + n + ' archivos más. ';
        }
    },
    iHaveTheRight: 'Tengo derecho a cargar estos archivos según los <a href="/main/authorization/termsOfService">Términos de servicio</a> ',
	updateJavaTitle: 'Actualizar Java',
	updateJavaDescription: 'El nuevo cargador requiere una versión más reciente de Java. Haga clic en "Okay" para obtener Java.',
 	batchEditorLabel: 'Edita la información de todos los objetos ',
    applyThisInfo: 'Aplicar esta información a los archivos que siguen ',
    titleProperty: 'Título ',
    descriptionProperty: 'Descripción ',
    tagsProperty: 'Etiquetas ',
    viewableByProperty: 'Puede verlo ',
    viewableByEveryone: 'Para todas las edades ',
    viewableByFriends: 'Sólo mis amistades ',
    viewableByMe: 'Sólo yo ',
    albumProperty: 'Álbum ',
    artistProperty: 'Artista ',
    enableDownloadLinkProperty: 'Permitir bajar ',
    enableProfileUsageProperty: 'Permitir a usuarios poner esta canción en sus páginas ',
    licenseProperty: 'Licencia ',
    creativeCommonsVersion: '3.0',
    selectLicense: '— Elegir licencia —',
    copyright: '© Todos los derechos reservados ',
    ccByX: function(n) { return 'Creative Commons Reconocimiento ' + n; },
    ccBySaX: function(n) { return 'Creative  Commons Compartir igual  ' + n; },
    ccByNdX: function(n) { return 'Creative Commons Sin obra derivada ' + n; },
    ccByNcX: function(n) { return 'Creative Commons Reconocimiento - No comercial ' + n; },
    ccByNcSaX: function(n) { return 'Creative Commons Reconocimiento – No comercial – Compartir igual ' + n; },
    ccByNcNdX: function(n) { return 'Creative Commons Reconocimiento - No comercial - Sin obra derivada ' + n; },
    publicDomain: 'Dominio público ',
    other: 'Otros ',
    errorUnexpectedTitle: '¡Vaya! ',
    errorUnexpectedDescription: 'Ha habido un error. Por favor, vuélvelo a intentar. ',
    errorTooManyTitle: 'Demasiados objetos ',
    errorTooManyDescription: function(n) { return 'Lo sentimos pero sólo puedes cargar ' + n + ' objetos a la vez. '; },
    errorNotAMemberTitle: 'No está permitido ',
    errorNotAMemberDescription: 'Lo sentimos, tienes que ser miembro para cargar cosas. ',
    errorContentTypeNotAllowedTitle: 'No está permitido ',
    errorContentTypeNotAllowedDescription: 'Lo sentimos, no está permitido cargar este tipo de contenido. ',
    errorUnsupportedFormatTitle: '¡Vaya! ',
    errorUnsupportedFormatDescription: 'Lo sentimos, no admitimos este tipo de archivo. ',
    errorUnsupportedFileTitle: '¡Vaya! ',
    errorUnsupportedFileDescription: 'foo.exe es un formato que no admitimos. ',
    errorUploadUnexpectedTitle: '¡Vaya! ',
    errorUploadUnexpectedDescription: function(file) {
		return file ?
			('Parece que hay un problema con el archivo ' + file + '. Por favor quítalo de la lista antes de cargar el resto de archivos.') :
			'Parece que hay un problema con el archivo que está en la parte superior de la lista. Por favor quítalo antes de cargar el resto de archivos.';
	},
    cancelUploadTitle: '¿Cancelar carga? ',
    cancelUploadDescription: '¿Estás seguro que quieres cancelar las cargas que quedan? ',
    uploadSuccessfulTitle: 'Carga completa ',
    uploadSuccessfulDescription: 'Por favor, espera mientras te llevamos hasta tus cargas... ',
    uploadPendingDescription: 'Tus archivos se han cargado con éxito, ahora hay que aprobarlos. ',
    photosUploadHeader: 'Fotos para cargar ',
    photosDragOutInstructions: 'Mueve las fotos para eliminarlas ',
    photosDragInInstructions: 'Mueve las fotos aquí ',
    photosSelectInstructions: 'Elije una foto ',
    photosFiles: 'Fotos ',
    photosUploadingStatus: function(n, m) { return 'Cargando una foto ' + n + ' de ' + m; },
    photosErrorTooManyTitle: 'Demasiadas fotos ',
    photosErrorTooManyDescription: function(n) { return 'Lo sentimos, sólo puedes cargar  ' + n + ' fotos a la vez. '; },
    photosErrorContentTypeNotAllowedDescription: 'Lo sentimos, se ha desconectado la carga de fotos. ',
    photosErrorUnsupportedFormatDescription: 'Lo sentimos, sólo puedes cargar imágenes con formato .jpg, .gif o .png. ',
    photosErrorUnsupportedFileDescription: function(n) { return n + ' no es un archivo .jpg, .gif o .png.'; },
    photosBatchEditorLabel: 'Editar información de todas las fotos ',
    photosApplyThisInfo: 'Aplicar esta información a las fotos que siguen ',
    photosErrorUploadUnexpectedDescription: function(file) {
		return file ?
			('Parece que hay un problema con el archivo ' + file + '. Por favor quítalo de la lista antes de cargar el resto de fotos.') :
			'Parece que hay un problema con el archivo que está en la parte superior de la lista. Por favor quítalo antes de cargar el resto de fotos.';
	},
    photosUploadSuccessfulDescription: 'Por favor, espera mientras te llevamos hasta tus fotos... ',
    photosUploadPendingDescription: 'Tus fotos se han cargado con éxito, ahora hay que aprobarlas. ',
    photosUploadLimitWarning: function(n) { return 'Puedes cargar ' + n + ' fotos a la vez. '; },
	photosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Has añadido el número máximo de fotos. ';
            case 1: return 'Puedes cargar 1 foto más. ';
            default: return 'Puedes cargar ' + n + ' fotos más. ';
        }
    },
    photosIHaveTheRight: 'Tengo derecho a cargar estas fotos según los <a href="/main/authorization/termsOfService">Términos de servico</a> ',
    videosUploadHeader: 'Vídeos para cargar ',
    videosDragInInstructions: 'Mueve los vídeos aquí ',
    videosDragOutInstructions: 'Mueve los vídeos que quieras eliminar ',
    videosSelectInstructions: 'Elige un vídeo ',
    videosFiles: 'Vídeos ',
    videosUploadingStatus: function(n, m) { return 'Cargando el vídeo ' + n + ' de ' + m; },
    videosErrorTooManyTitle: 'Demasiados vídeos ',
    videosErrorTooManyDescription: function(n) { return 'Los sentimos, sólo puedes cargar ' + n + ' vídeos a la vez. '; },
    videosErrorContentTypeNotAllowedDescription: 'Lo sentimos, se ha desactivado la carga de vídeos. ',
    videosErrorUnsupportedFormatDescription: 'Lo sentimos, sólo puedes cargar vídeos con formato .avi, .mov, .mp4, .wmv o .mpg. ',
    videosErrorUnsupportedFileDescription: function(x) { return x + ' no es un archivo a .avi, .mov, .mp4, .wmv o .mpg.'; },
    videosBatchEditorLabel: 'Editar información de todos los vídeos ',
    videosApplyThisInfo: 'Aplicar la información a los siguientes vídeos ',
    videosErrorUploadUnexpectedDescription:  function(file) {
		return file ?
			('Parece que hay un problema con el archivo ' + file + '. Por favor quítalo de la lista antes de cargar el resto de vídeos.') :
			'Parece que hay un problema con el archivo que está en la parte superior de la lista. Por favor quítalo antes de cargar el resto de vídeos.';
	},
    videosUploadSuccessfulDescription: 'Por favor, espera mientras te llevamos a tus vídeos... ',
    videosUploadPendingDescription: 'Tus vídeos se han cargado con éxito, ahora hay que aprobarlos. ',
    videosUploadLimitWarning: function(n) { return 'Puedes cargar ' + n + ' vídeos a la vez. '; },
	videosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Has añadido el número máximo de vídeos. ';
            case 1: return 'Puedes cargar 1 vídeo más. ';
            default: return 'Puedes cargar ' + n + ' vídeos más. ';
        }
    },
    videosIHaveTheRight: 'Tengo derecho a cargar vídeos según los <a href="/main/authorization/termsOfService">Términos de servicio</a> ',
    musicUploadHeader: 'Canciones para cargar ',
    musicTitleProperty: 'Canción ',
    musicDragOutInstructions: 'Mueve las canciones que quieras eliminar ',
    musicDragInInstructions: 'Mueve las canciones aquí ',
    musicSelectInstructions: 'Elige una canción ',
    musicFiles: 'Canciones ',
    musicUploadingStatus: function(n, m) { return 'Cargando ' + n + ' canciones de ' + m; },
    musicErrorTooManyTitle: 'Demasiadas canciones ',
    musicErrorTooManyDescription: function(n) { return 'Lo sentimos, pero sólo puedes cargar ' + n + ' canciones a la vez. '; },
    musicErrorContentTypeNotAllowedDescription: 'Lo sentimos, la carga de canciones se ha desactivado. ',
    musicErrorUnsupportedFormatDescription: 'Lo sentimos, sólo puedes cargar canciones en formato .mp3. ',
    musicErrorUnsupportedFileDescription: function(x) { return x + ' no es un archivo .mp3.'; },
    musicBatchEditorLabel: 'Editar información de todas las canciones ',
    musicApplyThisInfo: 'Aplicar la información a las siguientes canciones ',
    musicErrorUploadUnexpectedDescription:  function(file) {
		return file ?
			('Parece que hay un problema con el archivo ' + file + '. Por favor quítalo de la lista antes de cargar el resto de canciones.') :
			'Parece que hay un problema con el archivo que está en la parte superior de la lista. Por favor quítalo antes de cargar el resto de canciones.';
	},
    musicUploadSuccessfulDescription: 'Por favor espera mientras te llevamos a tus canciones... ',
    musicUploadPendingDescription: 'Tus canciones se han cargado con éxito, ahora hay que aprobarlas. ',
    musicUploadLimitWarning: function(n) { return 'Puedes cargar ' + n + ' canciones a la vez. '; },
	musicUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Has añadido el número máximo de canciones. ';
            case 1: return 'Puedes cargar 1 canción más. ';
            default: return 'Puedes cargar ' + n + ' canciones más. ';
        }
    },
    musicIHaveTheRight: 'Tengo derecho a cargar canciones según los <a href="/main/authorization/termsOfService">Términos de servicio</a> '
});

