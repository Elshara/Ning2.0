dojo.provide('xg.shared.messagecatalogs.zh_TW');

dojo.require('xg.index.i18n');

/**
 * Texts for the Traditional Chinese
 */
// Use UTF-8 byte sequences instead of HTML entities, e.g., & instead of &amp;, … instead of &hellip;  [Jon Aquino 2007-01-10]

dojo.evalObjPath('xg.events.nls', true);
dojo.lang.mixin(xg.events.nls, xg.index.i18n, {
    pleaseChooseImage: '請為活動選擇一個圖像',
    pleaseEnterAMessage: '請輸入一條訊息',
    pleaseEnterDescription: '請輸入活動說明',
    pleaseEnterLocation: '請輸入活動地點',
    pleaseEnterTitle: '請輸入活動標題',
    pleaseEnterType: '請為活動輸入至少一種類別',
    send: '發送',
    sending: '正在發送…',
    thereHasBeenAnError: '出現錯誤',
    yourMessage: '您的訊息',
    yourMessageHasBeenSent: '您的訊息已發送。',
    yourMessageIsBeingSent: '您的訊息正在發送。'
});

dojo.evalObjPath('xg.feed.nls', true);
dojo.lang.mixin(xg.feed.nls, xg.index.i18n, {
    edit: '編輯',
    title: '標題',
    feedUrl: '網址',
    show: '顯示:',
    titles: '只有標題',
    titlesAndDescriptions: '詳細資訊',
    display: '顯示',
    cancel: '取消',
    save: '儲存',
    loading: '正在載入...',
    items: '項目'
});


dojo.evalObjPath('xg.forum.nls', true);
dojo.lang.mixin(xg.forum.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return '字元的數目 (' + n + ') 超過最大值 (' + maximum + ') '; },
    pleaseEnterFirstPost: '請寫入本項討論的第一篇文章。',
    pleaseEnterTitle: '請輸入本項討論的標題',
    save: '儲存',
    cancel: '取消',
    yes: '是',
    no: '否',
    edit: '編輯',
    deleteCategory: '刪除類別',
    discussionsWillBeDeleted: '該類別的討論將被刪除。',
    whatDoWithDiscussions: '您想怎麼處理該類別的討論？',
    moveDiscussionsTo: '將討論移至:',
    moveToCategory: '移至類別...',
    deleteDiscussions: '刪除討論',
    'delete': '刪除',
    deleteReply: '刪除回複',
    deleteReplyQ: '刪除此回複?',
    deletingReplies: '刪除回複...',
    doYouWantToRemoveReplies: '您是否也想刪除對此評論的回覆？',
    pleaseKeepWindowOpen: '在處理過程中請勿關閉此窗口。過程可能需要幾分鐘。 。',
    from: '來自',
    show: '顯示',
    discussions: '討論',
    discussionsFromACategory: '來自一個類別的討論...',
    display: '顯示',
    items: '項目',
    view: '瀏覽'
});


dojo.evalObjPath('xg.uploader.nls', true);
dojo.lang.mixin(xg.uploader.nls, xg.index.i18n, {
    fileBrowserHeader: '我的電腦',
    fileRoot: '我的電腦',
    fileInformationHeader: '資訊',
    uploadHeader: '要上傳的檔案',
    dragOutInstructions: '拖出檔案進行刪除',
    dragInInstructions: '把檔案拖放到這裡',
    selectInstructions: '選擇檔案',
    files: '檔案',
    totalSize: '總大小',
    fileName: '名字',
    fileSize: '大小',
    nextButton: '下一步 >',
    okayButton: '確定',
    yesButton: '是',
    noButton: '否',
    uploadButton: '上傳',
    cancelButton: '取消',
    backButton: '返回',
    continueButton: '繼續',
    uploadingLabel: '正在上傳...',
    uploadingStatus: function(n, m) { return '上傳 ' + n + ' ' + m; },
    uploadingInstructions: '正在上傳，請不要關閉該窗口',
    uploadLimitWarning: function(n) { return '您每次可上傳 ' + n + ' 個檔案。 '; },
    uploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return '您已添加了最大數目的檔案。 ';
            case 1: return '您可以再上傳 1 個檔案。 ';
            default: return '您可以再上傳 ' + n + ' 個檔案。 ';
        }
    },
    iHaveTheRight: '根據<a href="/main/authorization/termsOfService">服務條款</a>，我有上傳這些檔案的權限。',
    updateJavaTitle: '更新 Java',
    updateJavaDescription: '批量上傳程式要求最新版本的 Java。點擊「確定」以獲取 Java。',
    batchEditorLabel: '編輯所有項目的資訊',
    applyThisInfo: '將本資訊應用到下面的檔案',
    titleProperty: '標題',
    descriptionProperty: '描述',
    tagsProperty: '標籤',
    viewableByProperty: '誰能觀看',
    viewableByEveryone: '每個人',
    viewableByFriends: '只有我的朋友',
    viewableByMe: '只有我',
    albumProperty: '專輯',
    artistProperty: '歌手',
    enableDownloadLinkProperty: '開啟下載鏈接',
    enableProfileUsageProperty: '允許大家將該歌曲放入他們的頁面',
    licenseProperty: '授權',
    creativeCommonsVersion: '3.0',
    selectLicense: '— 選擇授權 —',
    copyright: '© 版權所有',
    ccByX: function(n) { return '創意公共園地原作者 ' + n; },
    ccBySaX: function(n) { return '創意公共園地授權條款一致 ' + n; },
    ccByNdX: function(n) { return '創意公共園地 - 不允許衍生著作 ' + n; },
    ccByNcX: function(n) { return '創意公共園地 - 非商業用途 ' + n; },
    ccByNcSaX: function(n) { return '創意公共園地 - 非商業用途授權條款一致 ' + n; },
    ccByNcNdX: function(n) { return '創意公共園地 - 非商業用途 - 不允許衍生著作 ' + n; },
    publicDomain: '公共領域',
    other: '其他',
    errorUnexpectedTitle: '哎呀！',
    errorUnexpectedDescription: '有一個錯誤。請再次嘗試。',
    errorTooManyTitle: '項目太多',
    errorTooManyDescription: function(n) { return '很抱歉，但您每次只能上傳 ' + n + ' 個項目。 '; },
    errorNotAMemberTitle: '不允許',
    errorNotAMemberDescription: '很抱歉，但您需要是會員才上傳。',
    errorContentTypeNotAllowedTitle: '不允許',
    errorContentTypeNotAllowedDescription: '很抱歉，但不允許您上傳這種類型的內容。',
    errorUnsupportedFormatTitle: '哎呀！',
    errorUnsupportedFormatDescription: '很抱歉，但我們不支持這種檔案類型。',
    errorUnsupportedFileTitle: '哎呀！',
    errorUnsupportedFileDescription: 'foo.exe 是不受支持的格式。',
    errorUploadUnexpectedTitle: '哎呀！',
    errorUploadUnexpectedDescription: function(file) {
        return file ?
            ('檔案 ' + file + ' 好像有問題。在上傳其餘檔案之前，請將其從清單中刪除。') :
            '清單頂端的檔案好像有問題。在上傳其他檔案之前，請將其刪除。';
    },
    cancelUploadTitle: '取消上傳?',
    cancelUploadDescription: '您確實想取消剩餘的上傳嗎？',
    uploadSuccessfulTitle: '上傳已完成',
    uploadSuccessfulDescription: '我們正在處理您的上傳內容，請等待...',
    uploadPendingDescription: '您的檔案已成功上傳，正等待核准。',
    photosUploadHeader: '要上傳的照片',
    photosDragOutInstructions: '拖出照片進行刪除',
    photosDragInInstructions: '把照片拖放到這裡',
    photosSelectInstructions: '選擇照片',
    photosFiles: '照片',
    photosUploadingStatus: function(n, m) { return '上傳照片 ' + n + ' ' + m; },
    photosErrorTooManyTitle: '照片太多',
    photosErrorTooManyDescription: function(n) { return '很抱歉，但您每次只能上傳 ' + n + ' 張照片。 '; },
    photosErrorContentTypeNotAllowedDescription: '很抱歉，但照片上傳功能已被停用。',
    photosErrorUnsupportedFormatDescription: '很抱歉，但您只能上傳 .jpg、.gif 或 .png 格式的影像。',
    photosErrorUnsupportedFileDescription: function(n) { return n + ' 不是 .jpg、.gif 或 .png 檔案。'; },
    photosBatchEditorLabel: '編輯所有照片的資訊',
    photosApplyThisInfo: '將本資訊應用到下面的照片',
    photosErrorUploadUnexpectedDescription: function(file) {
        return file ?
            ('檔案 ' + file + ' 好像有問題。在上傳其他照片之前，請將其從清單中刪除。') :
            '清單頂端的照片好像有問題。在上傳其他照片之前，請將其刪除。';
    },
    photosUploadSuccessfulDescription: '我們正在帶您去您的照片，請等待...',
    photosUploadPendingDescription: '您的照片已成功上傳，正等待核准。',
    photosUploadLimitWarning: function(n) { return '您每次可上傳 ' + n + ' 張照片。 '; },
    photosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return '您已添加了最大的照片數目。 ';
            case 1: return '您可以再上傳 1 張照片。 ';
            default: return '您可以再上傳 ' + n + ' 張照片。 ';
        }
    },
    photosIHaveTheRight: '根據<a href="/main/authorization/termsOfService">服務條款</a>，我有上傳這些照片的權限。',
    videosUploadHeader: '要上傳的視頻',
    videosDragInInstructions: '把視頻拖放到這裡',
    videosDragOutInstructions: '拖出視頻進行刪除',
    videosSelectInstructions: '選擇視頻',
    videosFiles: '視頻',
    videosUploadingStatus: function(n, m) { return '上傳視頻 ' + n + ' ' + m; },
    videosErrorTooManyTitle: '視頻太多',
    videosErrorTooManyDescription: function(n) { return '很抱歉，但您每次只能上傳 ' + n + ' 個視頻。 '; },
    videosErrorContentTypeNotAllowedDescription: '很抱歉，但視頻上傳功能已被停用。',
    videosErrorUnsupportedFormatDescription: '很抱歉，但您只能上傳 .avi、.mov、.mp4、.wmv 或 .mpg 格式的視頻。',
    videosErrorUnsupportedFileDescription: function(x) { return x + ' is not a .avi, .mov, .mp4, .wmv or .mpg file.'; },
    videosBatchEditorLabel: '不是 .avi、.mov、.mp4、.wmv 或 .mpg 檔案。',
    videosApplyThisInfo: '將本資訊應用到下面的視頻',
    videosErrorUploadUnexpectedDescription: function(file) {
        return file ?
            ('檔案 ' + file + ' 好像有問題。在上傳其他視頻之前，請將其從清單中刪除。') :
            '清單頂端的視頻好像有問題。在上傳其他視頻之前，請將其刪除。';
    },
    videosUploadSuccessfulDescription: '我們正在帶您到您的視頻，請等待...',
    videosUploadPendingDescription: '您的視頻已成功上傳，正等待核准。',
    videosUploadLimitWarning: function(n) { return '您每次可上傳 ' + n + ' 個視頻。 '; },
    videosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return '您已添加了最大的視頻數目。 ';
            case 1: return '您可以再上傳 1 個視頻。 ';
            default: return '您可以再上傳 ' + n + ' 個視頻。 ';
        }
    },
    videosIHaveTheRight: '根據<a href="/main/authorization/termsOfService">服務條款</a>，我有上傳這些視頻的權限。',
    musicUploadHeader: '要上傳的歌曲',
    musicTitleProperty: '歌曲標題',
    musicDragOutInstructions: '拖出歌曲進行刪除',
    musicDragInInstructions: '把歌曲拖放到這裡',
    musicSelectInstructions: '選擇歌曲',
    musicFiles: '歌曲',
    musicUploadingStatus: function(n, m) { return '上傳歌曲 ' + n + ' ' + m; },
    musicErrorTooManyTitle: '歌曲太多',
    musicErrorTooManyDescription: function(n) { return '很抱歉，但您每次只能上傳 ' + n + ' 首歌曲。 '; },
    musicErrorContentTypeNotAllowedDescription: '很抱歉，但歌曲上傳功能已被停用。',
    musicErrorUnsupportedFormatDescription: '很抱歉，但您只能上傳 .mp3 格式的歌曲。',
    musicErrorUnsupportedFileDescription: function(x) { return x + ' 不是 .mp3 檔案。'; },
    musicBatchEditorLabel: '編輯所有歌曲的資訊',
    musicApplyThisInfo: '將本資訊應用到下面的歌曲',
    musicErrorUploadUnexpectedDescription: function(file) {
        return file ?
            ('檔案 ' + file + ' 好像有問題。在上傳其他歌曲之前，請將其從清單中刪除。') :
            '清單頂端的歌曲好像有問題。在上傳其他歌曲之前，請將其刪除。';
    },
    musicUploadSuccessfulDescription: '我們正在帶您到您的歌曲，請等待...',
    musicUploadPendingDescription: '您的歌曲已成功上傳，正等待核准。',
    musicUploadLimitWarning: function(n) { return '您每次可上傳 ' + n + ' 首歌曲。 '; },
    musicUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return '您已添加了最大的歌曲數目。 ';
            case 1: return '您可以再上傳 1 首歌曲。 ';
            default: return '您可以再上傳 ' + n + ' 首歌曲。 ';
        }
    },
    musicIHaveTheRight: '根據<a href="/main/authorization/termsOfService">服務條款</a>，我有上傳這些歌曲的權限。'
});


dojo.evalObjPath('xg.gadgets.nls', true);
dojo.lang.mixin(xg.gadgets.nls, xg.index.i18n, {
    edit: '編輯',
    title: '標題',
    feedUrl: '網址',
    cancel: '取消',
    save: '儲存',
    loading: '正在載入…',
    removeGadget: '刪除小工具',
    findGadgetsInDirectory: '在小工具目錄中查找小工具'
});


dojo.evalObjPath('xg.groups.nls', true);
dojo.lang.mixin(xg.groups.nls, xg.index.i18n, {
    pleaseChooseAName: '請給您的小組取名.',
    pleaseChooseAUrl: '請給您的小組選擇網址.',
    urlCanContainOnlyLetters: '網址只能有字母和數字（無空格）.',
    descriptionTooLong: function(n, maximum) { return '您的小組的s簡介(' + n + ')的長度超過最大值 (' + maximum + ') '; },
    nameTaken: '很抱歉，該名稱已被使用。請另取一個名稱。',
    urlTaken: '很抱歉，該網址已被使用。請另擇網址。',
    whyNot: '為什麼不這樣?',
    groupCreatorDetermines: function(href) { return '小組的創建者決定誰可以加入。若您覺得您可能因為操作錯誤而被封鎖的話，請<a ' + href + '>聯絡小組的創建者</a> '; },
    edit: '編輯',
    from: '來自',
    show: '顯示',
    groups: '小組',
    pleaseEnterName: '請輸入您的名字',
    pleaseEnterEmailAddress: '請輸入您的電子郵件地址',
    xIsNotValidEmailAddress: function(x) { return x + ' is not a valid email address'; },
    save: '儲存',
    cancel: '取消'
});


dojo.evalObjPath('xg.html.nls', true);
dojo.lang.mixin(xg.html.nls, xg.index.i18n, {
    contentsTooLong: function(maximum) { return '內容太長。請使用少於 ' + maximum + ' 個字元。 '; },
    edit: '編輯',
    save: '儲存',
    cancel: '取消',
    saving: '儲存...',
    addAWidget: function(url) { return '<a href="' + url + '">把一個小工具添加</a>于 該文字箱 '; }
});


dojo.evalObjPath('xg.index.nls', true);
dojo.lang.mixin(xg.index.nls, xg.index.i18n, {
    sendInvitation: '發送邀請函',
    sendInvitationToNFriends: function(n) {
        switch(n) {
            case 1: return '發送邀請函給1個朋友？ ';
            default: return '發送邀請函給 ' + n + ' 個朋友? ';
        }
    },
    showingNFriends: function(n, searchString) {
        switch(n) {
            case 1: return '顯示符合 "' + searchString + '" 的 1 位朋友。<a href="#">顯示每個人</a> ';
            default: return '顯示符合 "' + searchString + '" 的 ' + n + ' 位朋友。<a href="#">顯示每個人</a> ';
        }
    },
    sendMessage: '發送訊息',
    sendMessageToNFriends: function(n) {
        switch(n) {
            case 1: return '向 1 位朋友發送訊息？ ';
            default: return '向 ' + n + ' 位朋友發送訊息？ ';
        }
    },
    invitingNFriends: function(n) {
        switch(n) {
            case 1: return '正在邀請 1 位朋友… ';
            default: return '正在邀請 ' + n + ' 位朋友… ';
        }
    },
    nFriends: function(n) {
        switch(n) {
            case 1: return '1 位朋友… ';
            default: return n + '位朋友… ';
        }
    },
    sendingMessageToNFriends: function(n) {
        switch(n) {
            case 1: return '正向 1 位朋友發送訊息… ';
            default: return '正向 ' + n + ' 位朋友發送訊息… ';
        }
    },
    noPeopleSelected: '沒有選擇任何人',
    sorryWeDoNotSupport: '對不起，我們不支持您的電郵地址的網絡地址簿。請嘗試點擊下面的「通訊簿申請」來使用您電腦裡的地址。',
    pleaseChooseFriends: '請在發送訊息之前選擇一些朋友。',
    htmlNotAllowed: '不允許使用HTML',
    noFriendsFound: '沒有找到符合您搜尋條件的朋友。',
    yourMessageOptional: '<label>您的信息</label> (可選)',
    pleaseChoosePeople: '請選擇一些要邀請的人士。',
    pleaseEnterEmailAddress: '請輸入您的電子郵件地址',
    pleaseEnterPassword: function(emailAddress) { return '請輸入您的密碼，以獲取' + emailAddress + '。'; },
    sorryWeDontSupport: '對不起，我們不支持您的電郵地址的網絡地址簿。請點擊下面的電郵申請來使用您的電腦裡的地址。',
    pleaseSelectSecondPart: '請選擇您的電郵地址的第二部分，如gmail.com。',
    atSymbolNotAllowed: '請確保電郵地址第一部分裡沒有@符號。',
    resetTextQ: '重新設定文字？',
    resetTextToOriginalVersion: '您是否確定要重新設定原有版本上的所有文字？所有的更改將被遺失。',
    changeQuestionsToPublic: '將提問改為公開的提問?',
    changingPrivateQuestionsToPublic: '將不公開的提問改為公開的提問會暴露所有會員的提問。您確定麼？',
    youHaveUnsavedChanges: '您有尚未儲存的修改。',
    pleaseEnterASiteName: '請為社交網絡輸入一個名稱，如Tiny Clown Club',
    pleaseEnterShorterSiteName: '請輸入一個更簡短的名稱（最多為64個字元）',
    pleaseEnterShorterSiteDescription: '請輸入一個更簡短的說明（最多為250個字元）',
    siteNameHasInvalidCharacters: '名稱含有一些無效的字元',
    thereIsAProblem: '您的資料裡有一個問題',
    thisSiteIsOnline: '該社交網絡在線上',
    onlineSiteCanBeViewed: '<strong>在線上</strong> - 可在網絡上瀏覽您的隱私權設定。',
    takeOffline: '離線',
    thisSiteIsOffline: '該社交網絡已離線',
    offlineOnlyYouCanView: '<strong>離線</strong> -只有您能夠瀏覽該社交網絡。',
    takeOnline: '連線',
    themeSettings: '主題設定',
    addYourOwnCss: '高級',
    error: '錯誤',
    pleaseEnterTitleForFeature: function(displayName) { return '請為您的' +displayName+ '功能輸入一個標題'; },
    thereIsAProblemWithTheInformation: '您輸入的資料有問題',
    photos: '照片',
    videos: '視頻',
    pleaseEnterTheChoicesFor: function(questionTitle) { return '請為"' +questionTitle+ '"（如徒步旅行、閱讀、購物）輸入選項 '; },
    pleaseEnterTheChoices: '請輸入選項，如徒步旅行、閱讀、購物',
    shareWithFriends: '與朋友分享',
    email: '電郵',
    separateMultipleAddresses: '用逗號將多項地址分開',
    subject: '主題',
    message: '訊息',
    send: '發送',
    cancel: '取消',
    pleaseEnterAValidEmail: '請輸入一個有效的電郵地址',
    go: '移至',
    areYouSureYouWant: '您確定要這樣做嗎？',
    processing: '處理中...',
    pleaseKeepWindowOpen: '在處理過程中請勿關閉此窗口。過程可能需要幾分鐘。 。',
    complete: '已完成！',
    processIsComplete: '處理程序已完成。',
    ok: 'OK',
    body: '正文',
    pleaseEnterASubject: '請輸入主題',
    pleaseEnterAMessage: '請輸入訊息',
    thereHasBeenAnError: '有錯誤發生',
    fileNotFound: '沒有找到文檔',
    pleaseProvideADescription: '請提供一項說明',
    pleaseEnterYourFriendsAddresses: '請輸入您朋友的郵件地址或Ning ID',
    pleaseEnterSomeFeedback: '請輸入一些反饋',
    title: '標題',
    setAsMainSiteFeature: '設定為主要功能',
    thisIsTheMainSiteFeature: '這是主要功能',
    customized: '自訂',
    copyHtmlCode: '複製 HTML 編碼',
    playerSize: '播放機大小',
    selectSource: '選擇來源',
    myAlbums: '我的相冊',
    myMusic: '我的音樂',
    myVideos: '我的視訊',
    showPlaylist: '顯示播放清單',
    change: '修改',
    changing: '修改...',
    changePrivacy: '修改隱私權?',
    keepWindowOpenWhileChanging: '在處理過程中請勿關閉此窗口。過程可能需要幾分鐘。',
    addingInstructions: '正在添加內容，請不要關閉該窗口',
    addingLabel: '正在添加… .',
    cannotKeepFiles: '如果您希望瀏覽更多選項，您將需要再次選擇您的文件。  您是否要繼續？',
    done: '完成',
    looksLikeNotImage: '有一個或多個文件看上去不是.jpg、.gif或.png格式。  您是否仍然想要嘗試上傳？',
    looksLikeNotMusic: '您選擇的文件看上去不是.mp3格式。  您是否仍然想要嘗試上傳？',
    looksLikeNotVideo: '您選擇的文件看上去不是.mov、.mpg、.mp4、.avi、.3gp或.wmv格式。  您是否仍然想要嘗試上傳？',
    messageIsTooLong: function(n) { return '您的訊息太長。  請最多使用'+n+'個字符。'; },
    pleaseSelectPhotoToUpload: '請選擇照片上傳。',
    processingFailed: '對不起，處理失敗。  請稍後再試。',
    selectOrPaste: '您需要選擇一段影片或貼入\'embed\'代碼。',
    selectOrPasteMusic: '您需要選擇一首歌曲或貼入網址。',
    sendingLabel: '正在發送...',
    thereWasAProblem: '添加您的內容出現問題。  請稍後再試。',
    uploadingInstructions: '正在上傳，請不要關閉該窗口',
    uploadingLabel: '正在上傳...',
    youNeedToAddEmailRecipient: '您需要添加一名電子郵件收件人。',
    yourMessage: '您的訊息',
    yourMessageIsBeingSent: '您的訊息正在發送。',
    yourSubject: '您的主題'
});


dojo.evalObjPath('xg.music.nls', true);
dojo.lang.mixin(xg.music.nls, xg.index.i18n, {
    shufflePlaylist: '隨機播放清單',
    play: '播放',
    pleaseSelectTrackToUpload: '請選擇一首歌上傳。',
    pleaseEnterTrackLink: '請輸入一首歌的網址。',
    thereAreUnsavedChanges: '您有尚未儲存的修改。',
    autoplay: '自動播放',
    showPlaylist: '顯示播放清單',
    playLabel: '播放',
    url: '網址',
    rssXspfOrM3u: 'Rss、 xspf 或 m3u',
    save: '儲存',
    cancel: '取消',
    edit: '編輯',
    fileIsNotAnMp3: '有一文件似乎不是MP3。要繼續上傳嗎﹖',
    entryNotAUrl: '一項輸入看起來不是URL 。請確定所有輸入均以<kbd>http://</kbd>開始'
});

dojo.evalObjPath('xg.notes.nls', true);
dojo.lang.mixin(xg.notes.nls, xg.index.i18n, {
    addNewNote: '添加新的筆記',
    noteTitleTooLong: '註釋標題太長',
    pleaseEnterNoteEntry: '請輸入註釋帖子',
    pleaseEnterNoteTitle: '請輸入註釋標題！'
});

dojo.evalObjPath('xg.page.nls', true);
dojo.lang.mixin(xg.page.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return '字元的數目 (' + n + ') 超過最大值 (' + maximum + ') '; },
    pleaseEnterContent: '請輸入該頁面的內容',
    pleaseEnterTitle: '請輸入該頁面的標題',
    pleaseEnterAComment: '請輸入一項評論',
    deleteThisComment: '您確定要刪除這項評論麼？',
    save: '儲存',
    cancel: '取消',
    discussionTitle: '頁面標題：',
    tags: '標籤：',
    edit: '編輯',
    close: '關閉',
    displayPagePosts: '顯示頁面的貼文',
    thereIsAProblem: '您的資訊有問題'
});


dojo.evalObjPath('xg.photo.nls', true);
dojo.lang.mixin(xg.photo.nls, xg.index.i18n, {
    randomOrder: '隨機順序',
    untitled: '無標題',
    photos: '照片',
    edit: '編輯',
    photosFromAnAlbum: '專輯',
    show: '顯示',
    rows: '列',
    cancel: '取消',
    save: '儲存',
    deleteThisPhoto: '刪除該照片？',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return '字元的數目 (' + n + ') 超過最大值 (' + maximum + ') '; },
    weCouldNotLookUpAddress: function(address) { return '對不起，我們無法察看地址"' +地址+ '"。 '; },
    pleaseSelectPhotoToUpload: '請選擇一個要上傳的照片。',
    pleaseEnterAComment: '請輸入一項評論。',
    addToExistingAlbum: '添加至現有的專輯',
    addToNewAlbumTitled: '添加至標題為的新專輯...',
    deleteThisComment: '刪除該評論？',
    importingNofMPhotos: function(n,m) { return '匯入 <span id="currentP">' + n + '</span>  ' + m + ' 照片。 '},
    starting: '正在開始...',
    done: '完畢!',
    from: '來自',
    display: '顯示',
    takingYou: '帶您去看您的照片...',
    anErrorOccurred: '不幸出現一個錯誤。請使用頁面底部的鏈接報告該問題。',
    weCouldntFind: '我們無法找到任何照片！您為什麼不試試其他的選項？'
});


dojo.evalObjPath('xg.activity.nls', true);
dojo.lang.mixin(xg.activity.nls, xg.index.i18n, {
    edit: '編輯',
    show: '顯示',
    events: '事件',
    setWhatActivityGetsDisplayed: '設定哪些活動被顯示',
    save: '儲存',
    cancel: '取消'
});


dojo.evalObjPath('xg.profiles.nls', true);
dojo.lang.mixin(xg.profiles.nls, xg.index.i18n, {
    pleaseEnterValueForPost: '請為貼文輸入一個價值',
    pleaseProvideAValidDate: '請提供一個有效日期',
    uploadAFile: '上傳一個文檔',
    pleaseEnterUrlOfLink: '請輸入鏈接的URL:',
    pleaseEnterTextOfLink: '您想鏈接哪些文字？',
    edit: '編輯',
    recentlyAdded: '最近添加',
    featured: '特選的',
    iHaveRecentlyAdded: '我最近新添加的',
    fromTheSite: '來自社交網絡',
    cancel: '取消',
    save: '儲存',
    loading: '正在載入...',
    addAsFriend: '加入您的朋友名單中',
    requestSent: '請求已被發送.',
    sendingFriendRequest: '發送朋友的請求',
    thisIsYou: '這就是您!',
    isYourFriend: '是您的朋友',
    isBlocked: '已被封鎖',
    pleaseEnterAComment: '請輸入一項評論',
    pleaseEnterPostBody: '請為貼文的正文部分輸入一些內容',
    pleaseSelectAFile: '請選擇一個文檔',
    pleaseEnterChatter: '請輸入您的評論內容',
    toggleBetweenHTML: '顯示/隱藏 HTML 編碼',
    attachAFile: '附加一個文檔',
    addAPhoto: '添加照片',
    insertALink: '插入一個鏈接',
    changeTextSize: '修改文字的大小',
    makeABulletedList: '制定項目清單',
    makeANumberedList: '列出編號清單',
    crossOutText: '劃去文字',
    underlineText: '在文字下面劃線',
    italicizeText: '將文字變成斜體',
    boldText: '讓文字加黑',
    letMeApproveChatters: '在張貼前讓我批准評論？',
    noPostChattersImmediately: '不， –  直接張貼評論',
    yesApproveChattersFirst: '是， –  先批准評論',
    yourCommentMustBeApproved: '您的評論須經過批准後大家才能看到。',
    reallyDeleteThisPost: '真要刪除該張貼？',
    commentWall: '評論牆',
    commentWallNComments: function(n) {
        switch(n) {
            case 1: return '評論墻 (1 項評論) ';
            default: return '評論牆(' + n + ' 註解) ';
        }
    },
    display: '顯示',
    from: '來自',
    show: '顯示',
    rows: '列',
    posts: '張貼'
});


dojo.evalObjPath('xg.shared.nls', true);
dojo.lang.mixin(xg.shared.nls, xg.index.i18n, {
    uploadAPhoto: '上傳照片',
    uploadAnImage: '上傳影像',
    uploadAPhotoEllipsis: '上傳照片...',
    useExistingImage: '使用現有影像:',
    existingImage: '現有影像',
    useThemeImage: '使用主題影像:',
    themeImage: '主題影像',
    noImage: '沒有影像',
    uploadImageFromComputer: '從您的電腦上傳影像',
    tileThisImage: '用該影像拼圖',
    done: '完畢',
    currentImage: '當前的影像',
    pickAColor: '挑選一種顏色...',
    openColorPicker: '打開顏色挑選器',
    loading: '正在載入...',
    ok: 'OK',
    save: '儲存',
    cancel: '取消',
    saving: '儲存...',
    addAnImage: '添加影像',
    bold: '加黑',
    italic: '斜體',
    underline: '下劃線',
    strikethrough: '刪除線',
    addHyperink: '添加超鏈接',
    options: '選項',
    wrapTextAroundImage: '讓文字環繞影像周圍？',
    imageOnLeft: '影像在左邊？',
    imageOnRight: '影像在右邊？',
    createThumbnail: '創建縮略圖？',
    pixels: '像素',
    createSmallerVersion: '在播放器上創建您更小版本的影像以供顯示。設定寬度的像素。',
    popupWindow: '彈跳視窗？',
    linkToFullSize: '鏈接至彈跳視窗中的大型影像。',
    add: '添加',
    keepWindowOpen: '在內容上傳時請勿關閉視窗。',
    cancelUpload: '取消上傳',
    pleaseSelectAFile: '請選擇一個影像文件。',
    pleaseSpecifyAThumbnailSize: '請具體確定縮略圖尺寸',
    thumbnailSizeMustBeNumber: '縮略圖尺寸必須是一個數字',
    addExistingImage: '或插入一個現有的影像',
    clickToEdit: '點擊編輯',
    sendingFriendRequest: '發送朋友的請求',
    requestSent: '請求已被發送.',
    pleaseCorrectErrors: '請改正這些錯誤',
    tagThis: '在此加上標籤',
    addOrEditYourTags: '添加或編輯您的標籤:',
    addYourRating: '添加您的評分:',
    separateMultipleTagsWithCommas: '用逗號將多個標籤分開，如酷，“紐西蘭”',
    saved: '已儲存!',
    noo: '新的',
    none: '沒有',
    joinNow: '現在加入',
    join: '加入',
    youHaventRated: '您還沒有為該項目評分呢。',
    yourRatedThis: function(n) {
        switch(n) {
            case 1: return '您評此項目為1星。 ';
            default: return '您評此項目為' + n + '星。 ';
        }
    },
    yourRatingHasBeenAdded: '已添加您的評分。',
    thereWasAnErrorRating: '為此內容評分時出現錯誤。',
    yourTagsHaveBeenAdded: '已添加您的標籤。',
    thereWasAnErrorTagging: '.添加標籤時出現錯誤。',
    addToFavorites: '添加到最愛',
    removeFromFavorites: '從最愛中刪除',
    nStarsOutOfM: function(n,m) {
        switch(n) {
            case 1: return '1星，滿分 ' + m;
            default: return n + ' 星，滿分 ' + m;
        }
    },
    follow: '跟隨',
    stopFollowing: '停止跟隨',
    pendingPromptTitle: '會員申請待批準',
    youCanDoThis: '一旦管理員批準您的會員資格，您便可進行這項操作。',
    pleaseEnterAComment: '請輸入評論',
    pleaseEnterAFileAddress: '請輸入文件地址',
    pleaseEnterAWebsite: '請輸入一個網站地址'
});


dojo.evalObjPath('xg.video.nls', true);
dojo.lang.mixin(xg.video.nls, xg.index.i18n, {
    edit: '編輯',
    display: '顯示',
    detail: '詳細內容',
    player: '播放機',
    from: '來自',
    show: '顯示',
    videos: '視頻',
    cancel: '取消',
    save: '儲存',
    saving: '儲存...',
    deleteThisVideo: '刪除該視頻?',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return '字元的數目 (' + n + ') 超過最大值 (' + maximum + ') '; },
    weCouldNotLookUpAddress: function(address) { return '對不起，我們無法察看地址"' +地址+ '"。 '; },
    approve: '批准',
    approving: '正在批准中...',
    keepWindowOpenWhileApproving: '在照片被批准過程中請勿關閉該窗口放。該過程可能會需要好幾分鐘。',
    'delete': '刪除',
    deleting: '正在刪除中...',
    keepWindowOpenWhileDeleting: '在照片被批准過程中請勿關閉該窗口。該過程可能會需要好幾分鐘。',
    pasteInEmbedCode: '請貼入另一網站的視頻的內嵌編碼。',
    pleaseSelectVideoToUpload: '請選擇一個要上傳的視頻。',
    embedCodeContainsMoreThanOneVideo: '內嵌的編碼包含不止一個視頻。請確保它僅有一個<物件> 及/或 <內嵌> 標籤。',
    embedCodeMissingTag: '內嵌的編碼沒有一個&lt;embed&gt;或&lt;object&gt;標籤。',
    fileIsNotAMov: '該文件看上去不是 .mov、 .mpg、 .mp4、 .avi、 .3gp 或.wmv。要試圖上傳嗎﹖。',
    pleaseEnterAComment: '請輸入一項評論。',
    youRatedVideoNStars: function(n) {
        switch(n) {
            case 1: return '您評該視頻為1星! ';
            default: return '您評該視頻為' + n + '星! ';
        }
    },
    deleteThisComment: '刪除該評論？',
    embedHTMLCode: 'HTML內嵌代碼：',
    copyHTMLCode: '複製 HTML 編碼'
});