dojo.provide('xg.shared.messagecatalogs.pt_BR');

dojo.require('xg.index.i18n');

/**
 * Texts for the Portuguese (Brazil) locale.
 */

// Use UTF-8 byte sequences instead of HTML entities, e.g., & instead of &amp;, … instead of &hellip;  [Jon Aquino 2007-01-10]


dojo.evalObjPath('xg.feed.nls', true);
dojo.lang.mixin(xg.feed.nls, xg.index.i18n, {
    edit: 'Editar',
    title: 'Título:',
    feedUrl: 'URL:',
    show: 'Exibir:',
    titles: 'Apenas títulos',
    titlesAndDescriptions: 'Exibição detalhada',
    display: 'Mostrar',
    cancel: 'Cancelar',
    save: 'Salvar',
    loading: 'Carregando...',
    items: 'itens'
});


dojo.evalObjPath('xg.forum.nls', true);
dojo.lang.mixin(xg.forum.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'O número de caracteres (' + n + ') ultrapassa o número máximo (' + maximum + ') '; },
    pleaseEnterFirstPost: 'Escreva a mensagem inicial deste tópico',
    pleaseEnterTitle: 'Digite um título para seu tópico',
    save: 'Salvar',
    cancel: 'Cancelar',
    yes: 'Sim',
    no: 'Não',
    edit: 'Editar',
    deleteCategory: 'Excluir categoria',
    discussionsWillBeDeleted: 'Os tópicos nesta categoria serão excluídos.',
    whatDoWithDiscussions: 'O que você quer fazer com os tópicos nesta categoria?',
    moveDiscussionsTo: 'Mover os tópicos para:',
    moveToCategory: 'Mover para categoria…',
    deleteDiscussions: 'Excluir tópicos',
    'delete': 'Excluir',
    deleteReply: 'Excluir resposta',
    deleteReplyQ: 'Excluir esta resposta?',
    deletingReplies: 'Excluindo respostas…',
    doYouWantToRemoveReplies: 'Você deseja excluir as respostas a este comentário.',
    pleaseKeepWindowOpen: 'Deixe esta janela do navegador aberta enquanto o processo estiver em andamento. Isso pode levar alguns minutos.',
    from: 'De',
    show: 'Exibir',
    discussions: 'discussões',
    discussionsFromACategory: 'Tópicos de uma categoria…',
    display: 'Mostrar',
    items: 'itens',
    view: 'Visualizar'
});


dojo.evalObjPath('xg.groups.nls', true);
dojo.lang.mixin(xg.groups.nls, xg.index.i18n, {
    pleaseChooseAName: 'Escolha um nome para seu grupo.',
    pleaseChooseAUrl: 'Escolha um endereço web para seu grupo.',
    urlCanContainOnlyLetters: 'O endereço web deve conter apenas letras e números (sem espaços).',
    descriptionTooLong: function(n, maximum) { return 'O tamanho da descrição de seu grupo (' + n + ') ultrapassa o tamanho máximo (' + maximum + ') '; },
    nameTaken: 'Desculpe-nos - este nome já foi utilizado. Escolha outro nome.',
    urlTaken: 'Desculpe-nos - este endereço web já foi cadastrado. Escolha outro.',
    whyNot: 'Por que não?',
    groupCreatorDetermines: function(href) { return 'O criador do grupo determina quem pode se associar. Se você acha que foi bloqueado por engano, <a ' + href + '>entre em contato com o criador do grupo</a> '; },
    edit: 'Editar',
    from: 'De',
    show: 'Exibir',
    groups: 'grupos',
    pleaseEnterName: 'Digite seu nome',
    pleaseEnterEmailAddress: 'Digite seu endereço de e-mail.',
    xIsNotValidEmailAddress: function(x) { return x + ' is not a valid email address'; },
    save: 'Salvar',
    cancel: 'Cancelar'
});


dojo.evalObjPath('xg.html.nls', true);
dojo.lang.mixin(xg.html.nls, xg.index.i18n, {
    contentsTooLong: function(maximum) { return 'O conteúdo é longo demais. Use menos do que ' + maximum + ' caracteres. '; },
    edit: 'Editar',
    save: 'Salvar',
    cancel: 'Cancelar',
    saving: 'Salvando…',
    addAWidget: function(url) { return '<a href="' + url + '">Adicionar um componente gráfico</a> a esta caixa de texto '; }
});


dojo.evalObjPath('xg.index.nls', true);
dojo.lang.mixin(xg.index.nls, xg.index.i18n, {
    sendInvitation: 'Enviar um convite',
    sendInvitationToNFriends: function(n) {
        switch(n) {
            case 1: return 'Enviar convite a 1 amigo? ';
            default: return 'Enviar convite a ' + n + ' amigos? ';
        }
    },
    yourMessageOptional: '<label>Sua mensagem</label> (opcional)',
    pleaseChoosePeople: 'Selecione algumas pessoas para convidar.',
    pleaseEnterEmailAddress: 'Seu endereço de e-mail',
    pleaseEnterPassword: function(emailAddress) { return 'Digite sua senha para ' + emailAddress + '. '; },
    sorryWeDoNotSupport: 'Desculpe-nos, a sua lista de contatos da web não é compatível com nosso sistema. Tente clicar em \'Aplicativo de Contatos\' abaixo para utilizar endereços de seu computador',
    pleaseSelectSecondPart: 'Selecione a segunda parte de seu endereço de email, por exemplo, gmail.com.',
    atSymbolNotAllowed: 'Certifique-se que o símbolo @ não faça parte do endereço de e-mail.',
    resetTextQ: 'Reiniciar o texto?',
    resetTextToOriginalVersion: 'Você quer mesmo reiniciar todo o seu texto para a versão original? Todas as alterações serão perdidas.',
    changeQuestionsToPublic: 'Alterar as perguntas para públicas?',
    changingPrivateQuestionsToPublic: 'Alterar as perguntas para públicas irá expor as respostas dos membros. Você quer mesmo fazer isso?',
    youHaveUnsavedChanges: 'Há alterações que não foram salvas.',
    pleaseEnterASiteName: 'Digite o nome da rede social, por exemplo, Clube do Palhacinho.',
    pleaseEnterShorterSiteName: 'Digite um nome mais curto (máx. de 64 caracteres)',
    pleaseEnterShorterSiteDescription: 'Digite uma descrição mais curta (máx. de 140 caracteres)',
    siteNameHasInvalidCharacters: 'Este nome possui caracteres inválidos',
    thereIsAProblem: 'Há um problema com sua informação',
    thisSiteIsOnline: 'Esta rede social está online',
    onlineSiteCanBeViewed: '<strong>Online</strong> - A rede pode ser visualizada com relação às suas configurações de privacidade.',
    takeOffline: 'Passar para offline',
    thisSiteIsOffline: 'A rede social está offline',
    offlineOnlyYouCanView: '<strong>Offline</strong> - Apenas você pode visualizar esta rede social.',
    takeOnline: 'Passar para online',
    themeSettings: 'Configurações do tema',
    addYourOwnCss: 'Avançado',
    error: 'Erro',
    pleaseEnterTitleForFeature: function(displayName) { return 'Digite um nome para seu recurso' + displayName + ' '; },
    thereIsAProblemWithTheInformation: 'Há um problema com a informação digitada',
    photos: 'Fotos',
    videos: 'Vídeos',
    pleaseEnterTheChoicesFor: function(questionTitle) { return 'Digite as escolhas para "' + questionTitle + '" por exemplo, Caminhar, Ler, Fazer compras '; },
    pleaseEnterTheChoices: 'Digite as escolhas, por exemplo, Caminhar, Ler, Fazer compras',
    shareWithFriends: 'Compartilhar com amigos',
    email: 'e-mail',
    separateMultipleAddresses: 'Separe vários endereços de e-mail por vírgulas',
    subject: 'Assunto',
    message: 'Mensagem',
    send: 'Enviar',
    cancel: 'Cancelar',
    pleaseEnterAValidEmail: 'Digite um endereço de e-mail válido.',
    go: 'Ir',
    areYouSureYouWant: 'Você realmente deseja fazer isto?',
    processing: 'Processando…',
    pleaseKeepWindowOpen: 'Deixe esta janela do navegador aberta enquanto o processo estiver em andamento. Isso pode levar alguns minutos.',
    complete: 'Concluído!',
    processIsComplete: 'O processo foi concluído',
    ok: 'OK',
    body: 'Corpo',
    pleaseEnterASubject: 'Digite um assunto',
    pleaseEnterAMessage: 'Digite uma mensagem',
    thereHasBeenAnError: 'Houve um erro',
    fileNotFound: 'Arquivo não encontrado',
    pleaseProvideADescription: 'Forneça uma descrição',
    pleaseEnterYourFriendsAddresses: 'Digite os endereços ou Ning IDs de seus amigos.',
    pleaseEnterSomeFeedback: 'Digite seus comentários',
    title: 'Título:',
    setAsMainSiteFeature: 'Configurar como recurso principal',
    thisIsTheMainSiteFeature: 'Este é o recurso principal',
    customized: 'Personalizado',
    copyHtmlCode: 'Copiar código HTML',
    playerSize: 'Tamanho do Player',
    selectSource: 'Selecionar fonte',
    myAlbums: 'Meus álbuns',
    myMusic: 'Minhas músicas',
    myVideos: 'Meus vídeos',
    showPlaylist: 'Exibir lista de reprodução',
    change: 'Alterar',
    changing: 'Alterando...',
    changePrivacy: 'Alterar privacidade?',
    keepWindowOpenWhileChanging: 'Deixe esta janela do navegador aberta enquanto as configurações de privacidade são alteradas. Este processo pode levar alguns minutos.',
    htmlNotAllowed: 'Não é permitido usar HTML',
    showingNFriends: function(n, searchString) {
        switch(n) {
            case 1: return 'Exibindo 1 amigo que atende ao critério "' + searchString + '". <a href="#">Exibir todos</a> ';
            default: return 'Exibindo ' + n + ' amigos que atendem ao critério "' + searchString + '". <a href="#">Exibir todos</a> ';
        }
    },
    sendMessage: 'Enviar mensagem',
    sendMessageToNFriends: function(n) {
        switch(n) {
            case 1: return 'Enviar mensagem para 1 amigo? ';
            default: return 'Enviar mensagem para ' + n + ' amigos? ';
        }
    },
    invitingNFriends: function(n) {
        switch(n) {
            case 1: return 'Convidando 1 amigo… ';
            default: return 'Convidando ' + n + ' amigos… ';
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
            case 1: return 'Enviando mensagem para 1 amigo… ';
            default: return 'Enviando mensagem para ' + n + ' amigos… ';
        }
    },
    noPeopleSelected: 'Não há pessoas selecionadas',
    pleaseChooseFriends: 'Selecione alguns amigos antes de enviar sua mensagem.',
    noFriendsFound: 'Não foram encontrados amigos que atendam seus critérios.',
    sorryWeDontSupport: 'Não aceitamos o catálogo de endereços da web para seu endereço de e-mail. Tente clicar em \'Email Application\' abaixo para usar os endereços de seu computador.',
    subjectIsTooLong: function(n) { return 'Seu assunto está muito longo. Use '+n+' caracteres ou menos.'; },
    addingInstructions: 'Deixe esta janela aberta enquanto seu conteúdo estiver sendo acrescentado.',
    addingLabel: 'Adicionando.. .',
    cannotKeepFiles: 'Você terá que escolher seus arquivos novamente se desejar visualizar mais opções.  Deseja continuar?',
    done: 'Pronto',
    looksLikeNotImage: 'Um ou mais arquivos não parecem estar no formato .jpg, .gif, ou .png.  Gostaria de tentar carregá-los mesmo assim?',
    looksLikeNotMusic: 'O arquivo que você selecionou não parece estar no formato .mp3.  Gostaria de tentar carregá-los mesmo assim?',
    looksLikeNotVideo: 'Este arquivo não parece estar no formato .mov, .mpg, .mp4, .avi, .3gp ou .wmv.  Gostaria de tentar carregá-los mesmo assim?',
    messageIsTooLong: function(n) { return 'Sua mensagem está muito longa. Use '+n+' caracteres ou menos.'; },
    pleaseSelectPhotoToUpload: 'Selecione uma foto para carregar.',
    processingFailed: 'Desculpe, o processamento falhou. Tente novamente mais tarde.',
    selectOrPaste: 'Você precisa selecionar um vídeo ou colar o código \’embutido\’.',
    selectOrPasteMusic: 'Você precisa selecionar uma música ou colar o URL.',
    sendingLabel: 'Enviando… .',
    thereWasAProblem: 'Houve um problema ao acrescentar seu conteúdo.  Tente novamente mais tarde.',
    uploadingInstructions: 'Deixe esta janela aberta enquanto o processo de carregamento estiver em andamento.',
    uploadingLabel: 'Carregando.. .',
    youNeedToAddEmailRecipient: 'Você precisa acrescentar um destinatário de email.',
    yourMessage: 'Sua mensagem',
    yourMessageIsBeingSent: 'Sua mensagem está sendo enviada.',
    yourSubject: 'Seu assunto'
});


dojo.evalObjPath('xg.music.nls', true);
dojo.lang.mixin(xg.music.nls, xg.index.i18n, {
    shufflePlaylist: 'Lista de reprod. aleatória',
    play: 'executar',
    pleaseSelectTrackToUpload: 'Selecione uma música para carregar',
    pleaseEnterTrackLink: 'Digite o URL da música.',
    thereAreUnsavedChanges: 'Há alterações que não foram salvas.',
    autoplay: 'Reprodução automática',
    showPlaylist: 'Exibir lista de reprodução',
    playLabel: 'Executar',
    url: 'URL',
    rssXspfOrM3u: 'rss, xspf ou m3u',
    save: 'Salvar',
    cancel: 'Cancelar',
    edit: 'Editar',
    fileIsNotAnMp3: 'Um dos arquivos não parece estar no formato MP3. Tentar carregar assim mesmo?',
    entryNotAUrl: 'Uma das entradas não parece ser uma URL. Certifique-se que todas as entradas começam com <kbd>http://</kbd>'
});


dojo.evalObjPath('xg.page.nls', true);
dojo.lang.mixin(xg.page.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'O número de caracteres (' + n + ') ultrapassa o número máximo (' + maximum + ') '; },
    pleaseEnterContent: 'Digite o conteúdo da página',
    pleaseEnterTitle: 'Digite um título para a página',
    pleaseEnterAComment: 'Digite um comentário',
    deleteThisComment: 'Você realmente deseja excluir este comentário?',
    save: 'Salvar',
    cancel: 'Cancelar',
    discussionTitle: 'Título da página:',
    tags: 'Tags:',
    edit: 'Editar',
    close: 'Fechar',
    displayPagePosts: 'Exibir mensagens da página',
    displayTab: 'Mostrar guia',
    displayTabForPage: 'Se é para exibir uma guia para a página',
    directory: 'Diretório',
    addAnotherPage: 'Adicionar outra página',
    tabText: 'Texto da guia',
    urlDirectory: 'Diretório URL',
    tabTitle: 'Título da guia',
    remove: 'Remover',
    thereIsAProblem: 'Há um problema com sua informação.'
});


dojo.evalObjPath('xg.photo.nls', true);
dojo.lang.mixin(xg.photo.nls, xg.index.i18n, {
    randomOrder: 'Ordem aleatória',
    untitled: 'Sem título',
    photos: 'Fotos',
    edit: 'Editar',
    photosFromAnAlbum: 'Álbuns',
    show: 'Exibir',
    rows: 'linhas',
    cancel: 'Cancelar',
    save: 'Salvar',
    deleteThisPhoto: 'Excluir esta foto?',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'O número de caracteres (' + n + ') ultrapassa o número máximo (' + maximum + ') '; },
    weCouldNotLookUpAddress: function(address) { return 'Desculpe, não foi possível procurar o endereço "' + address + '". '; },
    pleaseSelectPhotoToUpload: 'Selecione uma foto para carregar,',
    pleaseEnterAComment: 'Digite um comentário.',
    addToExistingAlbum: 'Adicionar a um álbum existente',
    addToNewAlbumTitled: 'Adicionar a um novo álbum chamado…',
    deleteThisComment: 'Excluir este comentário?',
    importingNofMPhotos: function(n,m) { return 'Importando <span id="currentP">' + n + '</span> de ' + m + ' fotos. '},
    starting: 'Iniciando…',
    done: 'Pronto!',
    from: 'De',
    display: 'Mostrar',
    takingYou: 'Agora vamos ver suas fotos…',
    anErrorOccurred: 'Infelizmente ocorreu um erro. Informe o problema usando o link na parte inferior da página.',
    weCouldntFind: 'Não foi possível encontrar fotos! Por que você não tenta uma outra opção?'
});


dojo.evalObjPath('xg.activity.nls', true);
dojo.lang.mixin(xg.activity.nls, xg.index.i18n, {
    edit: 'Editar',
    show: 'Exibir',
    events: 'eventos',
    setWhatActivityGetsDisplayed: 'Configurar qual atividade é exibida',
    save: 'Salvar',
    cancel: 'Cancelar'
});


dojo.evalObjPath('xg.profiles.nls', true);
dojo.lang.mixin(xg.profiles.nls, xg.index.i18n, {
    pleaseEnterValueForPost: 'Digite um valor para o tópico',
    pleaseProvideAValidDate: 'Forneça uma data válida',
    uploadAFile: 'Carregar um arquivo',
    pleaseEnterUrlOfLink: 'Digite o URL do link:',
    pleaseEnterTextOfLink: 'Qual texto deseja vincular?',
    edit: 'Editar',
    recentlyAdded: 'Recém-adicionados(as)',
    featured: 'Apresentado',
    iHaveRecentlyAdded: 'Adicionei recentemente',
    fromTheSite: 'Da rede social',
    cancel: 'Cancelar',
    save: 'Salvar',
    loading: 'Carregando...',
    addAsFriend: 'Adicionar como amigo!',
    removeAsFriend: 'Remover como amigo',
    requestSent: 'Pedido enviado!',
    sendingFriendRequest: 'Enviando pedido de amigo',
    thisIsYou: 'É você!',
    isYourFriend: 'É seu amigo!',
    isBlocked: 'Está bloqueado',
    pleaseEnterAComment: 'Digite um comentário',
    pleaseEnterPostBody: 'Digite algo para o corpo da mensagem',
    pleaseSelectAFile: 'Selecione um arquivo',
    pleaseEnterChatter: 'Digite algo para seu comentário',
    toggleBetweenHTML: 'Exibir/ocultar o código HTML',
    attachAFile: 'Anexar um arquivo',
    addAPhoto: 'Adicionar foto',
    insertALink: 'Inserir um link',
    changeTextSize: 'Mudar o tamanho do texto',
    makeABulletedList: 'Criar uma lista com marcadores',
    makeANumberedList: 'Criar uma lista numerada',
    crossOutText: 'Riscar o texto',
    underlineText: 'Sublinhar o texto',
    italicizeText: 'Texto em itálico',
    boldText: 'Texto em negrito',
    letMeApproveChatters: 'Os comentários passam pelo meu crivo antes de serem publicados?',
    noPostChattersImmediately: 'Não - Publique os comentários imediatamente',
    yesApproveChattersFirst: 'Sim - Passam pelo meu crivo antes',
    yourCommentMustBeApproved: 'Seu comentário precisa ser aprovado antes que todos possam vê-lo.',
    reallyDeleteThisPost: 'Excluir realmente este lançamento?',
    commentWall: 'Caixa de Recados',
    commentWallNComments: function(n) {
        switch(n) {
            case 1: return 'Caixa de Recados (1 comentário) ';
            default: return 'Caixa de Recados (' + n + ' comentários) ';
        }
    },
    display: 'Mostrar',
    from: 'De',
    show: 'Exibir',
    rows: 'linhas',
    posts: 'lançamentos',
    returnToDefaultWarning: 'Isto moverá todos os destaques e o tema em Minha Página de volta ao padrão da rede. Deseja continuar?',
    networkError: 'Erro de rede',
    wereSorry: 'Desculpe, não podemos salvar seu novo layout neste momento. Isto é provavelmente devido à perda da conexão à Internet. Verifique sua conexão e tente novamente.'
});


dojo.evalObjPath('xg.shared.nls', true);
dojo.lang.mixin(xg.shared.nls, xg.index.i18n, {
    removeFriendTitle: 'Remover como amigo?',
    removeAsFriend: 'Remover como amigo',
    removeFriendConfirm: 'Tem certeza de que deseja remover esta pessoal como amigo?',
    locationNotFound: function(location) { return '<em>' + local+ '</em> não encontrado.'; },
    confirmation: 'Confirmação',
    showMap: 'Exibir mapa',
    hideMap: 'Ocultar mapa',
    yourCommentMustBeApproved: 'Seu comentário deve ser aprovado antes que alguém possa vê-lo.',
    nComments: function(n) { switch(n) { case 1: return '1 Comment'; default: return n + ' Comentários'; } },
    uploadAFile: 'Carregar arquivo',
    addExistingFile: 'ou insira um arquivo existente',
    uploadAPhoto: 'Carregar uma foto',
    uploadAnImage: 'Carregar uma imagem',
    uploadAPhotoEllipsis: 'Carregar uma foto…',
    useExistingImage: 'Usar imagem existente:',
    existingImage: 'Imagem existente',
    useThemeImage: 'Usar imagem temática:',
    themeImage: 'Imagem temática',
    noImage: 'Sem imagens',
    uploadImageFromComputer: 'Carregar uma imagem de seu computador',
    tileThisImage: 'Colocar a imagem lado a lado',
    done: 'Pronto',
    currentImage: 'Imagem atual',
    pickAColor: 'Selecione uma cor…',
    openColorPicker: 'Abra o Seletor de Cores',
    loading: 'Carregando...',
    ok: 'OK',
    save: 'Salvar',
    cancel: 'Cancelar',
    saving: 'Salvando…',
    addAnImage: 'Adicionar uma imagem',
    bold: 'Negrito',
    italic: 'Itálico',
    underline: 'Sublinhar',
    strikethrough: 'Riscar',
    addHyperink: 'Adicionar hiperlink',
    options: 'Opções',
    wrapTextAroundImage: 'Dispor texto ao redor da imagem?',
    imageOnLeft: 'Imagem à esquerda?',
    imageOnRight: 'Imagem à direita?',
    createThumbnail: 'Criar miniatura?',
    pixels: 'pixels',
    createSmallerVersion: 'Criar uma imagem menor que sua imagem para exibição. Configure a largura em pixels.',
    popupWindow: 'Janela pop-up?',
    linkToFullSize: 'Fazer um link à versão de tamanho original da imagem em uma janela pop-up.',
    add: 'Adicionar',
    keepWindowOpen: 'Mantenha esta janela do navegador aberta enquanto o carregamento estiver em andamento.',
    cancelUpload: 'Cancelar carregamento',
    pleaseSelectAFile: 'Selecione um arquivo de imagem',
    pleaseSpecifyAThumbnailSize: 'Especifique o tamanho da miniatura',
    thumbnailSizeMustBeNumber: 'O tamanho da miniatura deve ser um número',
    addExistingImage: 'ou coloque uma imagem existente',
    clickToEdit: 'Clique para editar',
    sendingFriendRequest: 'Enviando pedido de amigo',
    requestSent: 'Pedido enviado!',
    pleaseCorrectErrors: 'Corrija estes erros',
    tagThis: 'Coloque tag',
    addOrEditYourTags: 'Adicione ou edite seus tags:',
    addYourRating: 'Adicione sua classificação:',
    separateMultipleTagsWithCommas: 'Separe vários tags com vírgulas, por exemplo, legal, “Nova Zelândia”',
    saved: 'Foi salvo!',
    noo: 'NOVO',
    none: 'NENHUM',
    joinNow: 'Associar-se agora',
    join: 'Associar-se',
    youHaventRated: 'Você ainda não classificou este item.',
    yourRatedThis: function(n) {
        switch(n) {
            case 1: return 'Você classificou este item com 1 estrela. ';
            default: return 'Você classificou este item com ' + n + ' estrelas. ';
        }
    },
    yourRatingHasBeenAdded: 'Sua classificação foi adicionada.',
    thereWasAnErrorRating: 'Houve um erro ao classificar este conteúdo.',
    yourTagsHaveBeenAdded: 'Suas tags foram adicionadas.',
    thereWasAnErrorTagging: 'Houve um erro ao adicionar tags.',
    addToFavorites: 'Adicionar aos Favoritos',
    removeFromFavorites: 'Remover dos Favoritos',
    nStarsOutOfM: function(n,m) {
        switch(n) {
            case 1: return '1 estrela entre ' + m;
            default: return n + 'estrelas entre ' + m;
        }
    },
    follow: 'Seguir',
    stopFollowing: 'Parar de acompanhar',
    pendingPromptTitle: 'Associação esperando aprovação',
    youCanDoThis: 'Você pode fazê-lo tão logo sua associação tenha sido aprovada pelos administradores.',
    yourMessage: 'Sua mensagem',
    updateMessage: 'Atualizar mensagem',
    updateMessageQ: 'Atualizar mensagem?',
    editYourTags: 'Editar suas tags',
    editTypes: 'Editar tipo de evento',
    goBack: 'Voltar',
    sendAnyway: 'Enviar assim mesmo',
    addTags: 'Adicionar tags',
    editLocation: 'Editar local',
    errorMessage: 'Há 6 ou mais palavras neste e-mail que pode enviar seu e-mail para uma pasta de spam.',
    removeWords: 'Para ter certeza de que seu e-mail será entregue com sucesso, recomendamos voltar para mudar ou remover as seguintes palavras:',
    warningMessage: 'Parece que há algumas palavras neste e-mail que pode enviá-lo para uma pasta de Spam.',
    messageIsTooLong: function(n,m) { return 'Desculpe-nos. O número máximo de caracteres é '+m+'.' },
    pleaseEnterAComment: 'Digite um comentário.',
    pleaseEnterAFileAddress: 'Digite o endereço do arquivo.',
    pleaseEnterAWebsite: 'Digite um endereço de site.'
});


dojo.evalObjPath('xg.video.nls', true);
dojo.lang.mixin(xg.video.nls, xg.index.i18n, {
    edit: 'Editar',
    display: 'Mostrar',
    detail: 'Detalhes',
    player: 'Player',
    from: 'De',
    show: 'Exibir',
    videos: 'vídeos',
    cancel: 'Cancelar',
    save: 'Salvar',
    saving: 'Salvando…',
    deleteThisVideo: 'Excluir este vídeo?',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'O número de caracteres (' + n + ') ultrapassa o número máximo (' + maximum + ') '; },
    weCouldNotLookUpAddress: function(address) { return 'Desculpe, não foi possível procurar o endereço "' + address + '". '; },
    approve: 'Aprovar',
    approving: 'Aprovando...',
    keepWindowOpenWhileApproving: 'Deixe esta janela do navegador aberta enquanto os vídeos estão sendo aprovados. Este processo pode levar alguns minutos.',
    'delete': 'Excluir',
    deleting: 'Excluindo...',
    keepWindowOpenWhileDeleting: 'Deixe esta janela do navegador aberta enquanto os vídeos estão sendo excluídos. Este processo pode levar alguns minutos.',
    pasteInEmbedCode: 'Cole o código incorporado de um vídeo de outro local.',
    pleaseSelectVideoToUpload: 'Selecione um vídeo para carregar.',
    embedCodeContainsMoreThanOneVideo: 'O código incorporado contém mais de um vídeo. Certifique de que possui apenas um tag <object> e/ou <embed>.',
    embedCodeMissingTag: 'No código incorporado falta um tag &lt;embed&gt; ou &lt;object&gt;.',
    fileIsNotAMov: 'Este arquivo não parece estar no formato .mov, .mpg, .mp4, .avi, .3gp ou .wmv. Deve-se tentar carregar assim mesmo?',
    pleaseEnterAComment: 'Digite um comentário.',
    youRatedVideoNStars: function(n) {
        switch(n) {
            case 1: return 'Você classificou este vídeo com 1 estrela! ';
            default: return 'Você classificou este vídeo com ' + n + ' estrelas! ';
        }
    },
    deleteThisComment: 'Excluir este comentário?',
    embedHTMLCode: 'Código HTML:',
    copyHTMLCode: 'Copiar código HTML',
    shareOnFacebook: 'Compartilhar no Facebook',
    directLink: 'Link direto',
    addToMyspace: 'Adicionar ao MySpace',
    addToOthers: 'Adicionar a Outros'
});


dojo.evalObjPath('xg.gadgets.nls', true);
dojo.lang.mixin(xg.gadgets.nls, xg.index.i18n, {
    save: 'Salvar',
    cancel: 'Cancelar',
    edit: 'Editar',
    title: 'Título:',
    feedUrl: 'URL:',
    loading: 'Carregando...',
    removeGadget: 'Remover gadget',
    findGadgetsInDirectory: 'Encontrar gadgets no diretório de gadgets'
});


dojo.evalObjPath('xg.uploader.nls', true);
dojo.lang.mixin(xg.uploader.nls, xg.index.i18n, {
    fileBrowserHeader: 'Meu Computador',
    fileRoot: 'Meu Computador',
    fileInformationHeader: 'Informações',
    uploadHeader: 'Arquivos para carregar',
    dragOutInstructions: 'Arraste os arquivos para removê-los',
    dragInInstructions: 'Arraste os arquivos aqui',
    selectInstructions: 'Selecione um arquivo',
    files: 'Arquivos',
    totalSize: 'Tamanho total',
    fileName: 'Nome',
    fileSize: 'Tamanho',
    nextButton: 'Próximo >',
    okayButton: 'OK',
    yesButton: 'Sim',
    noButton: 'Não',
    uploadButton: 'Carregar',
    cancelButton: 'Cancelar',
    backButton: 'Voltar',
    continueButton: 'Continuar',
    uploadingLabel: 'Carregando...',
    uploadingStatus: function(n, m) { return 'Carregando' + n + ' de ' + m; },
    uploadingInstructions: 'Deixe esta janela aberta enquanto o processo de carregamento estiver em andamento.',
    uploadLimitWarning: function(n) { return 'Você pode carregar ' + n + ' arquivos por vez. '; },
    uploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Você adicionou o número máximo de arquivos. ';
            case 1: return 'Você pode carregar mais 1 arquivo. ';
            default: return 'Você pode carregar mais ' + n + ' arquivos. ';
        }
    },
    iHaveTheRight: 'Tenho o direito de carregar estes arquivos conforme os <a href="/main/authorization/termsOfService">Termos de Serviço</a>',
    updateJavaTitle: 'Atualizar Java',
    updateJavaDescription: 'O carregador por blocos exige uma versão mais recente de Java.  Clique em "OK" para obter Java.',
    batchEditorLabel: 'Editar as informações para todos os itens',
    applyThisInfo: 'Aplicar estas informações aos arquivos abaixo',
    titleProperty: 'Título',
    descriptionProperty: 'Descrição',
    tagsProperty: 'Tags',
    viewableByProperty: 'Pode ser vista por',
    viewableByEveryone: 'Qualquer um',
    viewableByFriends: 'Somente meus amigos',
    viewableByMe: 'Somente eu',
    albumProperty: 'Álbum',
    artistProperty: 'Artista',
    enableDownloadLinkProperty: 'Habilitar link para download',
    enableProfileUsageProperty: 'Permitir que pessoas coloquem esta música em suas páginas',
    licenseProperty: 'Licença',
    creativeCommonsVersion: '3.0',
    selectLicense: '— Selecionar licença —',
    copyright: '© Todos os direitos reservados',
    ccByX: function(n) { return 'Atribuição da Creative Commons ' + n; },
    ccBySaX: function(n) { return 'Atribuição-Compartilhamento pela mesma Licença da Creative Commons ' + n; },
    ccByNdX: function(n) { return 'Atribuição-Vedada a Criação de Obras Derivadas Creative Commons ' + n; },
    ccByNcX: function(n) { return 'Atribuição-Uso Não-Comercial da Creative Commons ' + n; },
    ccByNcSaX: function(n) { return 'Atribuição-Uso Não-Comercial-Compartilhamento pela mesma Licença da Creative Commons ' + n; },
    ccByNcNdX: function(n) { return 'Atribuição-Uso Não-Comercial-Vedada a Criação de Obras Derivadas da Creative Commons ' + n; },
    publicDomain: 'Domínio público',
    other: 'Outros',
    errorUnexpectedTitle: 'Opa!',
    errorUnexpectedDescription: 'Ocorreu um erro. Tente novamente.',
    errorTooManyTitle: 'Há itens demais',
    errorTooManyDescription: function(n) { return 'Desculpe, mas você só pode carregar ' + n + ' itens por vez. '; },
    errorNotAMemberTitle: 'Não permitido',
    errorNotAMemberDescription: 'Desculpe, você precisa ser associado para carregar.',
    errorContentTypeNotAllowedTitle: 'Não permitido',
    errorContentTypeNotAllowedDescription: 'Desculpe, você não tem permissão para carregar este tipo de conteúdo.',
    errorUnsupportedFormatTitle: 'Opa!',
    errorUnsupportedFormatDescription: 'Desculpe, este tipo de arquivo não é suportado.',
    errorUnsupportedFileTitle: 'Opa!',
    errorUnsupportedFileDescription: 'foo.exe é um formato não suportado.',
    errorUploadUnexpectedTitle: 'Opa!',
    errorUploadUnexpectedDescription: function(file) {
		return file ?
			('Parece haver um problema com o arquivo ' + file + '. Remova-o da lista antes de carregar os arquivos restantes.') :
			'Parece haver um problema com o arquivo no topo da lista. Remova-o da lista antes de carregar os arquivos restantes.';
	},
    cancelUploadTitle: 'Cancelar carregamento?',
    cancelUploadDescription: 'Tem certeza que deseja cancelar os carregamentos restantes?',
    uploadSuccessfulTitle: 'Carregamento concluído.',
    uploadSuccessfulDescription: 'Espere enquanto o levamos ao que você carregou...',
    uploadPendingDescription: 'Seus arquivos foram carregados com êxito e estão aguardando aprovação.',
    photosUploadHeader: 'Fotos para carregar',
    photosDragOutInstructions: 'Arraste as fotos para fora para removê-las',
    photosDragInInstructions: 'Arraste suas fotos aqui',
    photosSelectInstructions: 'Selecione uma foto',
    photosFiles: 'Fotos',
    photosUploadingStatus: function(n, m) { return 'Carregando foto' + n + ' de ' + m; },
    photosErrorTooManyTitle: 'Há fotos demais',
    photosErrorTooManyDescription: function(n) { return 'Desculpe, você pode carregar apenas ' + n + ' fotos por vez. '; },
    photosErrorContentTypeNotAllowedDescription: 'Desculpe, o recurso para carregar fotos está desabilitado.',
    photosErrorUnsupportedFormatDescription: 'Desculpe, você pode carregar imagens apenas nos formatos .jpg, .gif ou .png.',
    photosErrorUnsupportedFileDescription: function(n) { return n + ' não é arquivo a .jpg, .gif ou .png.'; },
    photosBatchEditorLabel: 'Editar informações sobre todas as fotos',
    photosApplyThisInfo: 'Aplicar estas informações às fotos abaixo',
    photosErrorUploadUnexpectedDescription: function(file) {
		return file ?
			('Parece haver um problema com o arquivo  ' + file + '. Remova-o da lista antes de carregar as fotos restantes.') :
			'Parece haver um problema com a foto no topo da lista. Remova-a da lista antes de carregar as fotos restantes.';
	},
    photosUploadSuccessfulDescription: 'Aguarde enquanto o levamos às suas fotos...',
    photosUploadPendingDescription: 'Suas fotos foram carregadas com êxito e estão aguardando aprovação.',
    photosUploadLimitWarning: function(n) { return 'Você pode carregar ' + n + ' fotos por vez. '; },
    photosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Você adicionou o número máximo de fotos. ';
            case 1: return 'Você pode carregar mais 1 foto. ';
            default: return 'Você pode carregar mais ' + n + ' fotos. ';
        }
    },
    photosIHaveTheRight: 'Eu tenho o direito de carregar estas fotos conforme os <a href="/main/authorization/termsOfService">Termos de Serviço</a>',
    videosUploadHeader: 'Vídeos para carregar',
    videosDragInInstructions: 'Arraste os vídeos aqui',
    videosDragOutInstructions: 'Arraste os vídeos para removê-los',
    videosSelectInstructions: 'Selecione um vídeo',
    videosFiles: 'Vídeos',
    videosUploadingStatus: function(n, m) { return 'Carregando vídeo ' + n + ' de ' + m; },
    videosErrorTooManyTitle: 'Há vídeos demais',
    videosErrorTooManyDescription: function(n) { return 'Desculpe, você pode carregar apenas ' + n + ' vídeos por vez. '; },
    videosErrorContentTypeNotAllowedDescription: 'Desculpe, o recurso para carregar vídeos está desabilitado.',
    videosErrorUnsupportedFormatDescription: 'Desculpe, você pode carregar vídeos apenas nos formatos .avi, .mov, .mp4, .wmv ou .mpg.',
    videosErrorUnsupportedFileDescription: function(x) { return x + ' não é arquivo .avi, .mov, .mp4, .wmv or .mpg.'; },
    videosBatchEditorLabel: 'Editar informações para todos os vídeos',
    videosApplyThisInfo: 'Aplicar estas informações aos vídeos abaixo',
    videosErrorUploadUnexpectedDescription: function(file) {
		return file ?
			('Parece haver um problema com o arquivo ' + file + '. Remova-o da lista antes de carregar os vídeos restantes.') :
			'Parece haver um problema com o vídeo no topo da lista. Remova-o da lista antes de carregar os vídeos restantes.';
	},
    videosUploadSuccessfulDescription: 'Espere enquanto o levamos aos seus vídeos...',
    videosUploadPendingDescription: 'Seus vídeos foram carregados com êxito e estão aguardando aprovação.',
    videosUploadLimitWarning: function(n) { return 'Você pode carregar ' + n + ' vídeos por vez. '; },
    videosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Você adicionou o número máximo de vídeos. ';
            case 1: return 'Você pode carregar mais 1 vídeo. ';
            default: return 'Você pode carregar mais ' + n + ' vídeos. ';
        }
    },
    videosIHaveTheRight: 'Eu tenho o direito de carregar estes vídeos conforme os <a href="/main/authorization/termsOfService">Termos de Serviço</a>',
    musicUploadHeader: 'Músicas para carregar',
    musicTitleProperty: 'Título da música',
    musicDragOutInstructions: 'Arraste as músicas para removê-las',
    musicDragInInstructions: 'Arraste as músicas aqui',
    musicSelectInstructions: 'Selecione uma música',
    musicFiles: 'Músicas',
    musicUploadingStatus: function(n, m) { return 'Carregando músicas ' + n + ' de ' + m; },
    musicErrorTooManyTitle: 'Há músicas demais',
    musicErrorTooManyDescription: function(n) { return 'Desculpe, você pode carregar apenas ' + n + ' músicas por vez. '; },
    musicErrorContentTypeNotAllowedDescription: 'Desculpe, o recurso para carregar músicas está desabilitado.',
    musicErrorUnsupportedFormatDescription: 'Desculpe, você pode carregar músicas apenas no formato .mp3.',
    musicErrorUnsupportedFileDescription: function(x) { return x + ' não é um arquivo .mp3.'; },
    musicBatchEditorLabel: 'Editar informações sobre todas as músicas',
    musicApplyThisInfo: 'Aplicar estas informações às músicas abaixo',
    musicErrorUploadUnexpectedDescription: function(file) {
		return file ?
			('Parece haver um problema com o arquivo ' + file + '. Remova-a da lista antes de carregar as músicas restantes.') :
			'Parece haver um problema com a música no topo da lista. Remova-a da lista antes de carregar as músicas restantes.';
	},
    musicUploadSuccessfulDescription: 'Espere enquanto o levamos às suas músicas...',
    musicUploadPendingDescription: 'Suas músicas foram carregadas com êxito e estão aguardando aprovação.',
    musicUploadLimitWarning: function(n) { return 'Você pode carregar ' + n + ' músicas por vez. '; },
    musicUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Você adicionou o número máximo de músicas. ';
            case 1: return 'Você pode carregar mais 1 música. ';
            default: return 'Você pode carregar mais ' + n + ' músicas. ';
        }
    },
    musicIHaveTheRight: 'Eu tenho o direito de carregar estas músicas conforme os <a href="/main/authorization/termsOfService">Termos de Serviço</a>'
});


dojo.evalObjPath('xg.events.nls', true);
dojo.lang.mixin(xg.events.nls, xg.index.i18n, {
    sendMessageToGuests: 'Enviar mensagens aos convidados',
    sendMessageToGuestsThat: 'Enviar mensagem aos convidados que',
    messageSent: 'Mensagem enviada!',
    haveNotYetRsvped: 'Ainda não foi dado RSVP',
    areAttending: 'Comparecerá',
    mightAttend: 'Poderá comparecer',
    areNotAttending: 'Não comparecerá',
    chooseRecipient: 'Escolha um recipiente.',
    messageIsTooLong: function(n) { return 'Sua mensagem está muito longa. Use '+n+' caracteres ou menos.'; },
    pleaseChooseImage: 'Escolha uma imagem para o evento.',
    pleaseEnterAMessage: 'Digite uma mensagem.',
    pleaseEnterDescription: 'Digite uma descrição para o evento.',
    pleaseEnterLocation: 'Digite um local para o evento.',
    pleaseEnterTitle: 'Digite um título para o evento.',
    pleaseEnterType: 'Digite pelo menos um tipo para o evento.',
    send: 'Enviar',
    sending: 'Enviando...',
    thereHasBeenAnError: 'Houve um erro',
    yourMessage: 'Sua mensagem',
    yourMessageHasBeenSent: 'A sua mensagem foi enviada.',
    yourMessageIsBeingSent: 'Sua mensagem está sendo enviada.'
});


dojo.evalObjPath('xg.notes.nls', true);
dojo.lang.mixin(xg.notes.nls, xg.index.i18n, {
    addNewNote: 'Adicionar nova nota',
    noteTitleTooLong: 'O título da nota é longo demais',
    pleaseEnterNoteEntry: 'Digite uma nota.',
    pleaseEnterNoteTitle: 'Digite um título para a nota!'
});