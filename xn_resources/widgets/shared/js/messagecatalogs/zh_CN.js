dojo.provide('xg.shared.messagecatalogs.zh_CN');

dojo.require('xg.index.i18n');

/**
 * Texts for the zh_CN locale. 
 */
// Use UTF-8 byte sequences instead of HTML entities, e.g., & instead of &amp;, … instead of &hellip;  [Jon Aquino 2007-01-10]

dojo.evalObjPath('xg.events.nls', true);
dojo.lang.mixin(xg.events.nls, xg.index.i18n, {
    pleaseChooseImage: '请为事件选择一份图像',
    pleaseEnterAMessage: '请输入消息',
    pleaseEnterDescription: '请输入事件说明',
    pleaseEnterLocation: '请输入事件地点',
    pleaseEnterTitle: '请输入事件标题',
    pleaseEnterType: '请输入至少一种事件类型',
    send: '发送',
    sending: '正在发送…',
    thereHasBeenAnError: '有错误发生',
    yourMessage: '您的消息',
    yourMessageHasBeenSent: '您的消息已经送出去了。',
    yourMessageIsBeingSent: '正在发送您的消息。'
});

dojo.evalObjPath('xg.feed.nls', true);
dojo.lang.mixin(xg.feed.nls, xg.index.i18n, {
    edit: '编辑',
    title: '标题',
    feedUrl: 'URL：',
    show: '显示：',
    titles: '只有标题',
    titlesAndDescriptions: '详细浏览',
    display: '显示',
    cancel: '取消',
    save: '保存',
    loading: '正在加载...',
    items: '项目'
});


dojo.evalObjPath('xg.forum.nls', true);
dojo.lang.mixin(xg.forum.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return '字符数 (' + n + ') 超过最大限制 (' + maximum + ') '; },
    pleaseEnterFirstPost: '请您写第一个讨论帖子',
    pleaseEnterTitle: '请输入该讨论的标题',
    save: '保存',
    cancel: '取消',
    yes: '是',
    no: '否',
    edit: '编辑',
    deleteCategory: '删除类别',
    discussionsWillBeDeleted: '该类别中的讨论将被删除。',
    whatDoWithDiscussions: '您希望如何处理该类别中的讨论？',
    moveDiscussionsTo: '将讨论移至：',
    moveToCategory: '移至类别…',
    deleteDiscussions: '删除讨论',
    'delete': '删除',
    deleteReply: '删除回复',
    deleteReplyQ: '删除该回复？',
    deletingReplies: '正在删除答复…',
    doYouWantToRemoveReplies: '您是否也想删除对该评论的答复？',
    pleaseKeepWindowOpen: '在数据处理继续进行时，请不要关闭该浏览窗口。该过程可能会需要好几分钟。',
    from: '来自',
    show: '显示',
    discussions: '讨论',
    discussionsFromACategory: '来自类别的讨论…',
    display: '显示',
    items: '项目',
    view: '查看'
});


dojo.evalObjPath('xg.groups.nls', true);
dojo.lang.mixin(xg.groups.nls, xg.index.i18n, {
    pleaseChooseAName: '请给您的小组取名。',
    pleaseChooseAUrl: '请给您的小组选择网址。',
    urlCanContainOnlyLetters: '网址只能有字母和数字（无空格）。',
    descriptionTooLong: function(n, maximum) { return '您的小组说明的长度 (' + n + ') 超过最大限制 (' + maximum + ') '; },
    nameTaken: '很抱歉，该名称已被使用。请另取一个名称。',
    urlTaken: '很抱歉，该网址已被使用。请另择网址。',
    whyNot: '为什么不？',
    groupCreatorDetermines: function(href) { return '小组创建人决定谁可以加入。如果您觉得您被错误地禁止加入小组，请 <a ' + href + '> 联系小组创建人 </a> '; },
    edit: '编辑',
    from: '来自',
    show: '显示',
    groups: '组',
    pleaseEnterName: '请输入您的名字',
    pleaseEnterEmailAddress: '请输入您的电子邮件地址',
    xIsNotValidEmailAddress: function(x) { return x + ' is not a valid email address'; },
    save: '保存',
    cancel: '取消'
});


dojo.evalObjPath('xg.html.nls', true);
dojo.lang.mixin(xg.html.nls, xg.index.i18n, {
	contentsTooLong: function(maximum) { return '内容太长。请不要超过 ' + maximum + ' 个字符。 '; },
    edit: '编辑',
    save: '保存',
    cancel: '取消',
    saving: '正在保存…',
    addAWidget: function(url) { return '为该文本框 <a href="' + url + '"> 添加小组件 </a> '; }
});


dojo.evalObjPath('xg.index.nls', true);
dojo.lang.mixin(xg.index.nls, xg.index.i18n, {
    sendInvitation: '发出邀请',
    sendInvitationToNFriends: function(n) {
        switch(n) {
            case 1: return '给1位朋友发出邀请？ ';
            default: return '给 ' + n + ' 朋友发出邀请？ ';
        }
    },
    yourMessageOptional: '<label>您的信息</label>（可选）',
    pleaseChoosePeople: '请选择要邀请的人员。',
    pleaseEnterEmailAddress: '请输入您的电子邮件地址。',
    pleaseEnterPassword: function(emailAddress) { return '请输入 ' + emailAddress + ' 的密码。 '; },
    sorryWeDontSupport: '对不起，您的电邮地址的web通讯簿不符合我们的使用规则。请试着点击下面的 \'Email Application\' ，以便使用您计算机中的地址。',
	sorryWeDoNotSupport: '很抱歉，您的电邮地址的web通讯簿不符合我们的使用规则。请试着点击下面的 \'Addressbook Application\' ，以便使用您计算机中的地址。 ',
    pleaseSelectSecondPart: '请选择您电邮地址的第二部分，例如：gmail.com。',
    atSymbolNotAllowed: '请确定电邮地址的第一部分中没有 @ 符号。',
    resetTextQ: '重置文本？',
    resetTextToOriginalVersion: '您确定要把所有文本重置为原来的版本吗？您做的所有更改都将会丢失。',
    changeQuestionsToPublic: '把问题改为公开？',
    changingPrivateQuestionsToPublic: '把非公开问题改为公开，将会暴露所有成员的回答。您确定吗？',
    youHaveUnsavedChanges: '您有未保存的更改。',
    pleaseEnterASiteName: '请为社交网络输入一个名称，例如：小丑俱乐部',
    pleaseEnterShorterSiteName: '请输入短一点的名称（最长64个字符）',
    pleaseEnterShorterSiteDescription: '请输入短一点的说明（最长250个字符）',
    siteNameHasInvalidCharacters: '名称中有一些无效字符',
    thereIsAProblem: '您提供的信息有一个问题',
    thisSiteIsOnline: '该社交网络已在线',
    onlineSiteCanBeViewed: '<strong> 在线 </strong> - 网络可根据您的隐私设置被查看。',
    takeOffline: '离线',
    thisSiteIsOffline: '该社交网络已离线',
    offlineOnlyYouCanView: '<strong> 离线 </strong> - 只有您可以查看该社交网络。',
    takeOnline: '设为在线',
    themeSettings: '主题设定',
    addYourOwnCss: '高级',
    error: '错误',
    pleaseEnterTitleForFeature: function(displayName) { return '请为您的 ' + displayName + ' 功能输入标题 '; },
    thereIsAProblemWithTheInformation: '输入的信息有问题',
    photos: '照片',
    videos: '视频',
    pleaseEnterTheChoicesFor: function(questionTitle) { return '请为 "' + questionTitle + '" 输入选择，例如：徒步旅行、阅读、购物 '; },
    pleaseEnterTheChoices: '请输入选择，例如：徒步旅行、阅读、购物',
    shareWithFriends: '与朋友共享',
    email: '电邮',
    separateMultipleAddresses: '用逗号把多个地址隔开',
    subject: '主题',
    message: '消息',
    send: '发送',
    cancel: '取消',
    pleaseEnterAValidEmail: '请输入有效的电邮地址',
    go: '开始',
    areYouSureYouWant: '您确定要这么做吗？',
    processing: '正在处理…',
    pleaseKeepWindowOpen: '在数据处理继续进行时，请不要关闭该浏览窗口。该过程可能会需要好几分钟。',
    complete: '完成！',
    processIsComplete: '过程已完成。',
    ok: 'OK',
    body: '正文',
    pleaseEnterASubject: '请输入主题',
    pleaseEnterAMessage: '请输入消息',
    thereHasBeenAnError: '有错误发生',
    fileNotFound: '未找到文件',
    pleaseProvideADescription: '请提供说明',
    pleaseEnterYourFriendsAddresses: '请输入您朋友的地址或Ning IDs',
    pleaseEnterSomeFeedback: '请输入反馈信息',
    title: '标题',
    setAsMainSiteFeature: '设为主要功能',
    thisIsTheMainSiteFeature: '这是主要功能',
    customized: '定制',
    copyHtmlCode: '复制HTML代码',
    playerSize: '播放器大小',
    selectSource: '选择来源',
    myAlbums: '我的相册',
    myMusic: '我的音乐',
    myVideos: '我的视频',
    showPlaylist: '显示播放列表',
    change: '修改',
    changing: '正在更改...',
    changePrivacy: '更改隐私？',
    keepWindowOpenWhileChanging: '在隐私设置被更改过程中，请不要关闭该浏览窗口。该过程可能会需要好几分钟。',
    htmlNotAllowed: '不允许HTML',
    showingNFriends: function(n, searchString) {
        switch(n) {
            case 1: return '显示一位与 "' + searchString + '" 匹配的朋友。 <a href="#"> 显示每一位 </a> ';
            default: return '显示与 "' + searchString + '" 匹配的 ' + n + ' 位朋友。 <a href="#"> 显示每一位 </a> ';
        }
    },
    sendMessage: '发送消息',
    sendMessageToNFriends: function(n) {
        switch(n) {
            case 1: return '向 一 位朋友发送消息？ ';
            default: return '向 ' + n + ' 位朋友发送消息？ ';
        }
    },
    invitingNFriends: function(n) {
        switch(n) {
            case 1: return '邀请 一 位朋友… ';
            default: return '邀请 ' + n + ' 位朋友… ';
        }
    },
    nFriends: function(n) {
        switch(n) {
            case 1: return '一 位朋友… ';
            default: return n + ' 朋友… ';
        }
    },
    sendingMessageToNFriends: function(n) {
        switch(n) {
            case 1: return '正在向 一 位朋友发送消息… ';
            default: return '正在向 ' + n + ' 位朋友发送消息… ';
        }
    },
    noPeopleSelected: '未选取任何人',
    pleaseChooseFriends: '发送消息前，请选取一些朋友。',
    noFriendsFound: '没有找到与您的搜索条件相符的朋友。',
    addingInstructions: '正在添加您的内容，请不要关闭该浏览窗口。',
    addingLabel: '正在添加.. .',
    cannotKeepFiles: '如果您想查看更多选项，必须再次选择您的文件。  您想要继续吗？',
    done: '完毕',
    looksLikeNotImage: '一个或多个文件似乎不是.jpg、.gif或.png格式。  您仍想尝试上传吗？',
    looksLikeNotMusic: '您选取的文件似乎不是.mp3格式。  您仍想尝试上传吗？',
    looksLikeNotVideo: '您选取的文件似乎不是.mov、.mpg、.mp4、.avi、.3gp或.wmv格式。  您仍想尝试上传吗？',
    messageIsTooLong: function(n) { return '您的消息太长了。  请使用不超过 '+n+' 个字符。'; },
    pleaseSelectPhotoToUpload: '请选择要上传的照片。',
    processingFailed: '很抱歉，处理失败。  请稍后再试。',
    selectOrPaste: '您需要选取视频或粘贴内嵌代码',
    selectOrPasteMusic: '您需要选取歌曲或粘贴URL',
    sendingLabel: '正在发送… .',
    thereWasAProblem: '无法添加内容。  请稍后再试。',
    uploadingInstructions: '正在上传，请不要关闭该浏览窗口',
    uploadingLabel: '上传.. .',
    youNeedToAddEmailRecipient: '您需要添加电邮收件人。',
    yourMessage: '您的消息',
    yourMessageIsBeingSent: '正在发送您的消息。',
    yourSubject: '您的主题'
});


dojo.evalObjPath('xg.music.nls', true);
dojo.lang.mixin(xg.music.nls, xg.index.i18n, {
    shufflePlaylist: '无序播放列表 ',
    play: '播放',
    pleaseSelectTrackToUpload: '请选择上传的歌曲。',
    pleaseEnterTrackLink: '请输入歌曲的URL。',
    thereAreUnsavedChanges: '有尚未保存的更改。',
    autoplay: '自动播放',
    showPlaylist: '显示播放列表',
    playLabel: '播放',
    url: 'URL',
    rssXspfOrM3u: 'rss, xspf, 或 m3u',
    save: '保存',
    cancel: '取消',
    edit: '编辑',
    fileIsNotAnMp3: '有一个文件好像不是MP3。还是要上传吗？',
    entryNotAUrl: '有一项输入好像不是URL。请确定所有输入都是以 <kbd>http://</kbd> 开头'
});

dojo.evalObjPath('xg.notes.nls', true);
dojo.lang.mixin(xg.notes.nls, xg.index.i18n, {
    addNewNote: '添加新注解',
    noteTitleTooLong: '注解标题太长',
    pleaseEnterNoteEntry: '请输入注解内容',
    pleaseEnterNoteTitle: '请输入注解标题！'
});

dojo.evalObjPath('xg.page.nls', true);
dojo.lang.mixin(xg.page.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return '字符数 (' + n + ') 超过最大限制 (' + maximum + ') '; },
    pleaseEnterContent: '请输入该页面的内容',
    pleaseEnterTitle: '请输入该页面的标题',
    pleaseEnterAComment: '请输入评论',
    deleteThisComment: '您确定要删除该评论吗？',
    save: '保存',
    cancel: '取消',
    discussionTitle: '页面标题：',
    tags: '标签：',
    edit: '编辑',
    close: '关闭',
    displayPagePosts: '显示页面贴子',
    thereIsAProblem: '您提供的信息有一个问题'
});


dojo.evalObjPath('xg.photo.nls', true);
dojo.lang.mixin(xg.photo.nls, xg.index.i18n, {
	randomOrder: '随机顺序 ',
    untitled: '无标题',
    photos: '照片',
    edit: '编辑',
    photosFromAnAlbum: '相册',
    show: '显示',
    rows: '行',
    cancel: '取消',
    save: '保存',
    deleteThisPhoto: '删除该照片？',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return '字符数 (' + n + ') 超过最大限制 (' + maximum + ') '; },
    weCouldNotLookUpAddress: function(address) { return '对不起，我们不能查找该地址 "' + address + '" 。 '; },
    pleaseSelectPhotoToUpload: '请选择要上传的照片。',
    pleaseEnterAComment: '请输入评论。',
    addToExistingAlbum: '添加到已有的相册',
    addToNewAlbumTitled: '添加到新的相册，标题是…',
    deleteThisComment: '删除该评论？',
      importingNofMPhotos: function(n,m) { return '输入 ' + m + ' 照片的 <span id="currentP">' + n + '</span> 。 '; },
    starting: '正在启动…',
    done: '完毕！',
    from: '来自',
    display: '显示',
    takingYou: '正在带您去看您的照片…',
    anErrorOccurred: '很遗憾，出现了错误。请使用本页底部的链接报告该问题。',
    weCouldntFind: '我们无法找到任何照片！请试试其他的选项好吗？'
});


dojo.evalObjPath('xg.activity.nls', true);
dojo.lang.mixin(xg.activity.nls, xg.index.i18n, {
    edit: '编辑',
    show: '显示',
    events: '事件',
    setWhatActivityGetsDisplayed: '设置要显示的活动',
    save: '保存',
    cancel: '取消'
});


dojo.evalObjPath('xg.profiles.nls', true);
dojo.lang.mixin(xg.profiles.nls, xg.index.i18n, {
    pleaseEnterValueForPost: '请输入该贴子的数据',
    pleaseProvideAValidDate: '请提供一个有效日期',
    uploadAFile: '上传文件',
    pleaseEnterUrlOfLink: '请输入该链接的URL：',
    pleaseEnterTextOfLink: '您想要链接什么文本？',
    edit: '编辑',
    recentlyAdded: '最近添加的',
    featured: '推荐的',
    iHaveRecentlyAdded: '我最近新添加的',
    fromTheSite: '来自社交网络',
    cancel: '取消',
    save: '保存',
    loading: '正在加载...',
    addAsFriend: '加入您的朋友名单中',
    requestSent: '请求已被发送。',
    sendingFriendRequest: '发送朋友请求',
    thisIsYou: '这就是您！',
    isYourFriend: '是您的朋友',
    isBlocked: '被阻止',
    pleaseEnterAComment: '请输入评论',
    pleaseEnterPostBody: '请为帖子正文输入一些内容',
    pleaseSelectAFile: '请选择一个文件',
    pleaseEnterChatter: '请为您的评论输入一些内容',
    toggleBetweenHTML: '显示/隐藏HTML代码',
    attachAFile: '附加文件',
    addAPhoto: '添加照片',
    insertALink: '插入链接',
    changeTextSize: '更改文本大小',
    makeABulletedList: '创建项目符号列表',
    makeANumberedList: '创建编号列表',
    crossOutText: '删去文本',
    underlineText: '文本带下划线',
    italicizeText: '文本变斜体',
    boldText: '文本变粗体',
    letMeApproveChatters: '在发表前我要先审查评论？',
    noPostChattersImmediately: '否 – 立即张贴评论',
    yesApproveChattersFirst: '是 – 先审查评论',
    yourCommentMustBeApproved: '您的评论必须经过审查后大家才能看到。',
    reallyDeleteThisPost: '真要删除该贴子？',
    commentWall: '评论墙',
    commentWallNComments: function(n) {
        switch(n) {
            case 1: return '评论墙（1条评论） ';
            default: return '评论墙（' + n + ' 评论） ';
        }
    },
    display: '显示',
    from: '来自',
    show: '显示',
    rows: '行',
    posts: '贴子'
});


dojo.evalObjPath('xg.uploader.nls', true);
dojo.lang.mixin(xg.uploader.nls, xg.index.i18n, {
    fileBrowserHeader: '我的电脑 ',
    fileRoot: '我的电脑 ',
    fileInformationHeader: '信息 ',
    uploadHeader: '要上传的文件 ',
    dragOutInstructions: '拖出文件删除 ',
    dragInInstructions: '把文件拖放到这里 ',
    selectInstructions: '选取文件 ',
    files: '文件 ',
    totalSize: '总计大小 ',
    fileName: '名字 ',
    fileSize: '大小 ',
    nextButton: '下一步 > ',
    okayButton: 'OK ',
    yesButton: '是 ',
    noButton: '否 ',
    uploadButton: '上传 ',
    cancelButton: '取消 ',
    backButton: '返回 ',
    continueButton: '继续 ',
    uploadingLabel: '上传... ',
    uploadingStatus: function(n, m) { return '上传 ' + n + ' ，共计 ' + m; },
    uploadingInstructions: '正在上传，请不要关闭该浏览窗口 ',
    uploadLimitWarning: function(n) { return '您可以一次上传 ' + n + ' 个文件。 '; },
	uploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return '您添加的文件已经达到最大数。 ';
            case 1: return '您可以再上传一个文件。 ';
            default: return '您可以再上传 ' + n + ' 个文件。 ';
        }
    },
    iHaveTheRight: '我有权根据 <a href="/main/authorization/termsOfService">服务条款</a>上传这些文件 ',
	updateJavaTitle: 'Java 更新页',
	updateJavaDescription:  '批量上传服务,  需要更多的最新版本的Java 。 点击"Okay" ，以获得的Java 。',
    batchEditorLabel: '编辑所有项目的信息 ',
    applyThisInfo: '将此信息应用到以下文件 ',
    titleProperty: '标题 ',
    descriptionProperty: '描述 ',
    tagsProperty: '标签 ',
    viewableByProperty: '谁能观看 ',
    viewableByEveryone: '任何人 ',
    viewableByFriends: '只有我的朋友 ',
    viewableByMe: '只有我 ',
    albumProperty: '专辑 ',
    artistProperty: '歌手 ',
    enableDownloadLinkProperty: '开启下载链接 ',
    enableProfileUsageProperty: '允许大家将该歌曲放入他们的页面 ',
    licenseProperty: '授权 ',
    creativeCommonsVersion: '3.0',
    selectLicense: '— 选择授权 —',
    copyright: '©保留所有权利 ',
    ccByX: function(n) { return '创作共用－署名 ' + n; },
    ccBySaX: function(n) { return '创作共用－署名－保持一致 ' + n; },
    ccByNdX: function(n) { return '创作共用-署名-非派生作品 ' + n; },
    ccByNcX: function(n) { return '创作共用-署名-非商业用途 ' + n; },
    ccByNcSaX: function(n) { return '创作共用－署名－非商业用途－保持一致 ' + n; },
    ccByNcNdX: function(n) { return '创作共用－署名－非商业用途 - 非派生作品 ' + n; },
    publicDomain: '公共领域 ',
    other: '其他 ',
    errorUnexpectedTitle: '哎呀！ ',
    errorUnexpectedDescription: '出错了。请再试一次。 ',
    errorTooManyTitle: '项目太多 ',
    errorTooManyDescription: function(n) { return '很抱歉，您一次只能上传 ' + n + ' 个项目。 '; },
    errorNotAMemberTitle: '不允许 ',
    errorNotAMemberDescription: '很抱歉，您必须是成员才能上传。 ',
    errorContentTypeNotAllowedTitle: '不允许 ',
    errorContentTypeNotAllowedDescription: '很抱歉，您不可以上传此类内容。 ',
    errorUnsupportedFormatTitle: '哎呀！ ',
    errorUnsupportedFormatDescription: '很抱歉，我们不支持此类文件。 ',
    errorUnsupportedFileTitle: '哎呀！ ',
    errorUnsupportedFileDescription: '不支持foo.exe格式。 ',
    errorUploadUnexpectedTitle: '哎呀！ ',
    errorUploadUnexpectedDescription: function(file) {
		return file ?
		    (' ' + file + ' 文件似乎有问题。 请先将其从列表中删除，然后再上传其余文件。') :
			'列表顶端的文件似乎有问题。 请先将其删除，然后再上传其余文件。';
	},
    cancelUploadTitle: '取消上传? ',
    cancelUploadDescription: '您确实要取消剩余的上传吗？ ',
    uploadSuccessfulTitle: '完成上传 ',
    uploadSuccessfulDescription: '我们正带您到您的上传内容，请稍候... ',
    uploadPendingDescription: '您的文件已经成功上传，正在等待批准。 ',
    photosUploadHeader: '要上传的照片 ',
    photosDragOutInstructions: '拖出照片删除 ',
    photosDragInInstructions: '把照片拖放到这里 ',
    photosSelectInstructions: '选取一张照片 ',
    photosFiles: '照片 ',
    photosUploadingStatus: function(n, m) { return '上传第' + n + ' 张照片，共计 ' + m; },
    photosErrorTooManyTitle: '太多照片 ',
    photosErrorTooManyDescription: function(n) { return '很抱歉，您一次只能上传 ' + n + ' 张照片。 '; },
    photosErrorContentTypeNotAllowedDescription: '很抱歉，照片上传已被禁用。 ',
    photosErrorUnsupportedFormatDescription: '很抱歉，您只能上传 .jpg、.gif、或.png格式的图片。 ',
    photosErrorUnsupportedFileDescription: function(n) { return n + ' 不是.jpg、.gif .或.png文件。'; },
    photosBatchEditorLabel: '编辑所有照片的信息 ',
    photosApplyThisInfo: '将此信息应用到以下照片 ',
    photosErrorUploadUnexpectedDescription: function(file) {
		return file ?
		    (' ' + file + ' 文件似乎有问题。 请先将其从列表中删除，然后再上传其余照片') :
			'列表顶端的照片似乎有问题。 请先将其删除，然后再上传其余照片。';
	},
    photosUploadSuccessfulDescription: '我们正带您到您的照片那里，请稍候... ',
    photosUploadPendingDescription: '您的照片已经成功上传，正在等待批准。 ',
    photosUploadLimitWarning: function(n) { return '您可以一次上传 ' + n + ' 张照片。 '; },
	photosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return '您添加的照片已经达到最大数。 ';
            case 1: return '您可以再上传一张照片。 ';
            default: return '您可以再上传 ' + n + ' 张照片。 ';
        }
    },
    photosIHaveTheRight: '我有权根据 <a href="/main/authorization/termsOfService">服务条款</a>上传这些照片 ',
    videosUploadHeader: '要上传的视频 ',
    videosDragInInstructions: '把视频拖放到这里 ',
    videosDragOutInstructions: '拖出视频删除 ',
    videosSelectInstructions: '选取一个视频 ',
    videosFiles: '视频 ',
    videosUploadingStatus: function(n, m) { return '上传第' + n + ' 个视频，共计 ' + m; },
    videosErrorTooManyTitle: '太多视频 ',
    videosErrorTooManyDescription: function(n) { return '很抱歉，您一次只能上传 ' + n + ' 个视频。 '; },
    videosErrorContentTypeNotAllowedDescription: '很抱歉，视频上传已被禁用。 ',
    videosErrorUnsupportedFormatDescription: '很抱歉，您只能上传.avi、.mov、.mp4、.wmv或.mpg格式的视频。 ',
    videosErrorUnsupportedFileDescription: function(x) { return x + ' 不是.avi、.mov、.mp4、.wmv或.mpg 文件。'; },
    videosBatchEditorLabel: '编辑所有视频的信息 ',
    videosApplyThisInfo: '将此信息应用到以下视频 ',
    videosErrorUploadUnexpectedDescription:  function(file) {
		return file ?
		    (' ' + file + ' 文件似乎有问题。 请先将其从列表中删除，然后再上传其余视频。') :
			'列表顶端的视频似乎有问题。 请先将其删除，然后再上传其余视频。';
	},
    videosUploadSuccessfulDescription: '我们正带您到您的视频那里，请稍候... ',
    videosUploadPendingDescription: '您的视频已经成功上传，正在等待批准。 ',
    videosUploadLimitWarning: function(n) { return '您可以一次上传 ' + n + ' 个视频。 '; },
	videosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return '您添加的视频已经达到最大数。 ';
            case 1: return '您可以再上传一个视频。 ';
            default: return '您可以再上传 ' + n + ' 个视频。 ';
        }
    },
    videosIHaveTheRight: '我有权根据 <a href="/main/authorization/termsOfService">服务条款</a>上传这些视频 ',
    musicUploadHeader: '要上传的歌曲 ',
    musicTitleProperty: '歌曲标题 ',
    musicDragOutInstructions: '拖出歌曲删除 ',
    musicDragInInstructions: '把歌曲拖放到这里 ',
    musicSelectInstructions: '选取一首歌曲 ',
    musicFiles: '歌曲 ',
    musicUploadingStatus: function(n, m) { return '上传第' + n + ' 首歌曲，共计 ' + m; },
    musicErrorTooManyTitle: '太多歌曲 ',
    musicErrorTooManyDescription: function(n) { return '很抱歉，您一次只能上传 ' + n + ' 首歌曲。 '; },
    musicErrorContentTypeNotAllowedDescription: '很抱歉，歌曲上传已被禁用。 ',
    musicErrorUnsupportedFormatDescription: '很抱歉，您只能上传.mp3格式的歌曲。 ',
    musicErrorUnsupportedFileDescription: function(x) { return x + ' 不是.mp3文件。'; },
    musicBatchEditorLabel: '编辑所有歌曲的信息 ',
    musicApplyThisInfo: '将此信息应用到以下歌曲 ',
    musicErrorUploadUnexpectedDescription:  function(file) {
		return file ?
		    (' ' + file + ' 文件似乎有问题。 请先将其从列表中删除，然后再上传其余歌曲。') :
			'列表顶端的歌曲似乎有问题。 请先将其删除，然后再上传其余歌曲。';
	},
    musicUploadSuccessfulDescription: '我们正带您到您的歌曲那里，请稍候... ',
    musicUploadPendingDescription: '您的歌曲已经成功上传，正在等待批准。 ',
    musicUploadLimitWarning: function(n) { return '您可以一次上传 ' + n + ' 首歌曲。 '; },
	musicUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return '您添加的歌曲已经达到最大数。 ';
            case 1: return '您可以再上传一首歌曲。 ';
            default: return '您可以再上传 ' + n + ' 首歌曲。 ';
        }
    },
    musicIHaveTheRight: '我有权根据 <a href="/main/authorization/termsOfService">服务条款</a>上传这些歌曲 '
});



dojo.evalObjPath('xg.shared.nls', true);
dojo.lang.mixin(xg.shared.nls, xg.index.i18n, {
    uploadAPhoto: '上传照片',
    uploadAnImage: '上传图像',
    uploadAPhotoEllipsis: '上传照片…',
    useExistingImage: '使用现有的图像：',
    existingImage: '现有的图像',
    useThemeImage: '使用主题图像：',
    themeImage: '主题图像',
    noImage: '无图像',
    uploadImageFromComputer: '从您的电脑上传一个图像',
    tileThisImage: '平铺该图像',
    done: '完毕',
    currentImage: '当前图像',
    pickAColor: '选择一种颜色…',
    openColorPicker: '打开颜色选择器',
    loading: '正在加载...',
    ok: 'OK',
    save: '保存',
    cancel: '取消',
    saving: '正在保存…',
    addAnImage: '添加一个图像',
    bold: '粗体',
    italic: '斜体',
    underline: '下划线',
    strikethrough: '删除线',
    addHyperink: '添加超链接',
    options: '选项',
    wrapTextAroundImage: '让文本环绕图像？',
    imageOnLeft: '图像在左侧？',
    imageOnRight: '图像在右侧？',
    createThumbnail: '创建缩略图？',
    pixels: '像素',
    createSmallerVersion: '创建一个较小版本的显示图像。设置像素单位的宽度。',
    popupWindow: '弹出式窗口？',
    linkToFullSize: '链接到弹出式窗口中实际大小的图像。',
    add: '添加',
    keepWindowOpen: '在内容上传时请不要关闭该浏览窗口。',
    cancelUpload: '取消上传',
    pleaseSelectAFile: '请选择一个图像文件',
    pleaseSpecifyAThumbnailSize: '请指定缩略图的大小',
    thumbnailSizeMustBeNumber: '缩略图的大小必须是数字',
    addExistingImage: '或插入一个现有的图像',
    clickToEdit: '点击编辑',
    sendingFriendRequest: '发送朋友请求',
    requestSent: '请求已被发送。',
    pleaseCorrectErrors: '请改正这些错误',
    tagThis: '为此加上标签',
    addOrEditYourTags: '添加或编辑您的标签：',
    addYourRating: '添加您的评分：',
    separateMultipleTagsWithCommas: '用逗号把多个标签隔开，例如：酷, "新西兰"',
    saved: '已保存！',
    noo: '新',
    none: '无',
    joinNow: '现在加入',
    join: '加入',
    youHaventRated: '您还没有为该项目评分。',
    yourRatedThis: function(n) {
        switch(n) {
            case 1: return '您把该项目评为1星。 ';
            default: return '您把该项目评为 ' + n + ' 星。 ';
        }
    },
    yourRatingHasBeenAdded: '您的评分已被加入。',
    thereWasAnErrorRating: '在给这个内容评分时有一个错误。',
    yourTagsHaveBeenAdded: '您的标签已被加入。',
    thereWasAnErrorTagging: '添加标签时有一个错误。',
    addToFavorites: '添加到最爱',
    removeFromFavorites: '从最爱中删除',
    nStarsOutOfM: function(n,m) {
        switch(n) {
            case 1: return '1星，最多是 ' + m;
            default: return n + '星，最多是 ' + m;
        }
    },
    follow: '跟随',
    stopFollowing: '停止跟随',
    pendingPromptTitle: '成员资格正在等候批准中',
    youCanDoThis: '一旦您的成员资格被管理员批准后，您就可以做了。',
    pleaseEnterAComment: '请输入评论',
    pleaseEnterAFileAddress: '请输入文件的地址',
    pleaseEnterAWebsite: '请输入网站地址'
});


dojo.evalObjPath('xg.video.nls', true);
dojo.lang.mixin(xg.video.nls, xg.index.i18n, {
    edit: '编辑',
    display: '显示',
    detail: '详细内容',
    player: '播放器',
    from: '来自',
    show: '显示',
    videos: '视频',
    cancel: '取消',
    save: '保存',
    saving: '正在保存…',
    deleteThisVideo: '删除该视频？',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return '字符数 (' + n + ') 超过最大限制 (' + maximum + ') '; },
    weCouldNotLookUpAddress: function(address) { return '对不起，我们不能查找该地址 "' + address + '" 。 '; },
    approve: '批准',
    approving: '正在批准中...',
    keepWindowOpenWhileApproving: '在视频被批准过程中请不要关闭该浏览窗口。该过程可能会需要好几分钟。',
    'delete': '删除',
    deleting: '正在删除中...',
    keepWindowOpenWhileDeleting: '在视频被删除过程中请不要关闭该浏览窗口。该过程可能会需要好几分钟。',
    pasteInEmbedCode: '请从另一个网站粘贴一个视频的内嵌代码。',
    pleaseSelectVideoToUpload: '请选择要上传的视频。',
    embedCodeContainsMoreThanOneVideo: '该内嵌代码包含一个以上的视频。请确定该代码只有一个 <object> 和/或 <embed> 标签。',
    embedCodeMissingTag: '该内嵌代码缺少一个 〈embed〉 或 〈object〉 标签。',
    fileIsNotAMov: '该文件好像不是 .mov, .mpg, .mp4, .avi, .3gp 或 .wmv。还是要上传吗？',
    pleaseEnterAComment: '请输入评论。',
    youRatedVideoNStars: function(n) {
        switch(n) {
            case 1: return '您把该视频评为1星！ ';
            default: return '您把该视频评为 ' + n + ' 星！ ';
        }
    },
    deleteThisComment: '删除该评论？',
    embedHTMLCode: 'HTML内嵌代码：',
    copyHTMLCode: '复制HTML代码'
});


dojo.evalObjPath('xg.gadgets.nls', true);
dojo.lang.mixin(xg.gadgets.nls, xg.index.i18n, {
    edit: '编辑',
    title: '标题',
    feedUrl: 'URL：',
    cancel: '取消',
    save: '保存',
    loading: '正在加载...',
    removeGadget: '删除小工具',
    findGadgetsInDirectory: '在“小工具目录”中查找小工具'
});
