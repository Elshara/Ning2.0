dojo.provide('xg.shared.messagecatalogs.ja_JP');

dojo.require('xg.index.i18n');

/**
 * Texts for the Japanese (Japan)
 */
// Use UTF-8 byte
dojo.evalObjPath('xg.feed.nls', true);
dojo.lang.mixin(xg.feed.nls, xg.index.i18n, {
    edit: '編集',
    title: 'タイトル：',
    feedUrl: 'URL：',
    show: '表示：',
    titles: 'タイトルのみ',
    titlesAndDescriptions: '詳細表示',
    display: '形式：',
    cancel: 'キャンセル',
    save: '上書き保存',
    loading: '読み込み中…',
    items: 'アイテム'
});


dojo.evalObjPath('xg.opensocial.nls', true);
dojo.lang.mixin(xg.opensocial.nls, xg.index.i18n, {
    edit: '編集',
    title: 'タイトル：',
    appUrl: 'URL：',
    cancel: 'キャンセル',
    save: '上書き保存',
    loading: '読み込み中…',
    removeBox: 'ボックスの削除',
    removeBoxText: function(feature) { return '<p>[マイページ] から「' + feature + '」ボックスを削除しますか？</p><p>削除しても、[マイ追加機能] からこの機能にアクセスすることはできます。</p>'},
    removeFeature: '機能の削除',
    removeFeatureText: 'この機能を完全に削除しますか？完全に削除すると、[マイページ] や [マイ追加機能] からでも使用できなくなります。',
    canSendMessages: '自分にメッセージを送信します',
    canAddActivities: '[マイページ] の [最新のアクティビティ] モジュールに更新内容を表示します',
    addFeature: '機能の追加',
    youAreAboutToAdd: function(feature, linkAttributes) { return '<p>[マイページ] に<strong>' + feature + '</strong>を追加します。この機能はサード パーティが開発したものです。</p> <p>「機能の追加」をクリックすると、このプラットフォームアプリケーションの<a ' + linkAttributes + '>使用規約</a>に同意したことになります。</p>'},
    featureSettings: '機能の設定',
    allowThisFeatureTo: 'この機能を以下の用途に使用できるようにします。',
    updateSettings: '設定の更新',
    recipientsShdBeString: '宛先は文字列のみで指定してください (複数の場合はカンマで区切る)。',
    onlyEmailMsgSupported: '「電子メール メッセージ」タイプのみに対応',
    msgExpectedToContain: 'メッセージの必須フィールド：タイプ、タイトル、本文',
    msgObjectExpected: 'メッセージに使用できるオブジェクト',
    recipientsShdBeSpecified: '宛先は空白にはできません。',
    unauthorizedRecipients: '認証されていない宛先がメールの送信先に指定されています。',
    rateLimitExceeded: 'レーティングの制限を越えています。',
    userCancelled: 'ユーザが処理をキャンセルしました。'
});


dojo.evalObjPath('xg.gadgets.nls', true);
dojo.lang.mixin(xg.gadgets.nls, xg.index.i18n, {
    edit: '編集',
    title: 'タイトル：',
    feedUrl: 'URL：',
    cancel: 'キャンセル',
    save: '上書き保存',
    loading: '読み込み中…',
    removeGadget: 'ガジェットの削除',
    findGadgetsInDirectory: 'ガジェット ディレクトリでのガジェット検索'
});


dojo.evalObjPath('xg.forum.nls', true);
dojo.lang.mixin(xg.forum.nls, xg.index.i18n, {
    items: 'アイテム',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return '文字数 (' + n + ') が最大数 (' + maximum + ') を超えています'; },
    pleaseEnterFirstPost: 'ディスカッションの最初の投稿を書き込んでください',
    pleaseEnterTitle: 'ディスカッションのタイトルを入力してください',
    save: '上書き保存',
    cancel: 'キャンセル',
    yes: 'はい',
    no: 'いいえ',
    edit: '編集',
    deleteCategory: 'カテゴリの削除',
    discussionsWillBeDeleted: 'このカテゴリにあるディスカッションは削除されます。',
    whatDoWithDiscussions: 'このカテゴリにあるディスカッションをどうしますか？',
    moveDiscussionsTo: 'ディスカッションを移動',
    deleteDiscussions: 'ディスカッションを削除',
    'delete': '削除',
    deleteReply: '返答を削除',
    deleteReplyQ: 'この返答を削除しますか？',
    deletingReplies: '返答を削除中…',
    doYouWantToRemoveReplies: 'このコメントに対する返答も削除しますか？',
    pleaseKeepWindowOpen: '処理が継続している間は、このブラウザのウィンドウを開いたままにしてください。これには数分かかることがあります。',
    contributorSaid: function(x) { return x + '発言：'},
    display: '表示',
    from: 'から',
    show: '表示',
    view: '表示',
    discussions: 'ディスカッション',
    discussionsFromACategory: 'カテゴリからのディスカッション…'
});


dojo.evalObjPath('xg.groups.nls', true);
dojo.lang.mixin(xg.groups.nls, xg.index.i18n, {
    pleaseChooseAName: 'グループの名前を選択してください。',
    pleaseChooseAUrl: 'グループの Web アドレスを選択してください。',
    urlCanContainOnlyLetters: 'Web アドレスには文字と数字以外は使用できません (スペースなし)。',
    descriptionTooLong: function(n, maximum) { return 'グループの説明の長さ (' + n + ') が最大の長さ (' + maximum + ') を超えています'; },
    nameTaken: 'この名前は既に使用されています。他の名前を選択してください。',
    urlTaken: 'この Web アドレスは既に使用されています。他の Web アドレスを選択してください。',
    whyNot: '原因',
    groupCreatorDetermines: function(href) { return 'グループの作成者が、誰が参加できるかを決定します。誤って自分が禁止されているとお考えの場合は、<a ' + href + '>グループの作成者までお問い合わせ</a> ください'; },
    edit: '編集',
    from: 'から',
    show: '表示',
    groups: 'グループ',
    pleaseEnterName: '名前を入力してください',
    pleaseEnterEmailAddress: 'メールアドレスを入力してください',
    xIsNotValidEmailAddress: function(x) { return x + '有効な電子メール アドレスではありません。'; },
    save: '上書き保存',
    cancel: 'キャンセル'
});


dojo.evalObjPath('xg.html.nls', true);
dojo.lang.mixin(xg.html.nls, xg.index.i18n, {
    contentsTooLong: function(maximum) { return 'コンテンツが長過ぎます。文字数を ' + maximum  + ' 未満にしてください。'; },
    edit: '編集',
    save: '上書き保存',
    cancel: 'キャンセル',
    saving: '保存中…',
    addAWidget: function(url) { return 'このテキストボックスに <a href="' + url + '">ウィジェットを追加</a> します'; }
});


dojo.evalObjPath('xg.index.nls', true);
dojo.lang.mixin(xg.index.nls, xg.index.i18n, {
    done: '完了',
    yourMessageIsBeingSent: 'メッセージの送信中です。',
    youNeedToAddEmailRecipient: 'メールの宛先を追加する必要があります。',
    checkPageOut: function (network) {return 'Check out this page on '+network},
    checkingOutTitle: function (title, network) {return 'Check out "'+title+'" on '+network},
    selectOrPaste: 'ビデオを選択するか、「埋め込み」コードを貼り付けてください',
    selectOrPasteMusic: '歌を選択するか、この URL を貼り付けてください',
    cannotKeepFiles: 'その他のオプションを表示する場合は、自分のファイルをもう一度選択する必要があります。続行しますか？',
    pleaseSelectPhotoToUpload: 'アップロードする写真を選択してください。',
    addingLabel: '追加中...',
    sendingLabel: '送信中...',
    addingInstructions: 'コンテンツを追加している間は、このウィンドウを開いたままにしてください。',
    looksLikeNotImage: '一部のファイルが .jpg、.gif、.png のいずれの形式ではないようです。このままアップロードしますか？',
    looksLikeNotVideo: '選択されたファイルは、.mov、.mpg、.mp4、.avi、.3gp、.wmv のいずれの形式ではないようです。このままアップロードしますか？',
    looksLikeNotMusic: '選択されたファイルは .mp3 ファイル形式ではないようです。このままアップロードしますか？',
    showingNFriends: function(n, searchString) {
        switch(n) {
            case 1: return '「' + searchString + '」に一致する 1 人の友達を表示する。<a href="#">全員を表示</a>';
            default: return '「' + searchString + '」に一致する ' + n + ' 人の友達を表示する。<a href="#">全員を表示</a>';
        }
    },
    sendInvitation: '招待状の送信',
    sendMessage: 'メッセージの送信',
    sendInvitationToNFriends: function(n) {
        switch(n) {
            case 1: return '1 人の友達に招待状を送信しますか？';
            default: return '' + n + ' 人の友達に招待状を送信しますか？';
        }
    },
    sendMessageToNFriends: function(n) {
        switch(n) {
            case 1: return '1 人の友達にメッセージを送信しますか？';
            default: return '' + n + ' 人の友達にメッセージを送信しますか？';
        }
    },
    invitingNFriends: function(n) {
        switch(n) {
            case 1: return '1 人の友達を招待中…';
            default: return '' + n + ' 人の友達を招待中…';
        }
    },
    nFriends: function(n) {
        switch(n) {
            case 1: return '1 人の友達';
            default: return n + '人の友達';
        }
    },
    sendingMessageToNFriends: function(n) {
        switch(n) {
            case 1: return 'メッセージを 1 人の友達に送信中…';
            default: return '' + n + ' 人の友達にメッセージを送信中…';
        }
    },
    yourMessageOptional: '<label>メッセージ (任意)</label>',
    subjectIsTooLong: function(n) { return '件名が長過ぎます。'+n+' 文字以下で入力してください。'; },
    messageIsTooLong: function(n) { return 'メッセージが長過ぎます。'+n+' 文字以下で入力してください。'; },
    pleaseChoosePeople: '招待する人を選択してください。',
    noPeopleSelected: '誰も選択されていません',
    pleaseEnterEmailAddress: 'メール アドレスを入力してください。',
    pleaseEnterPassword: function(emailAddress) { return '' + emailAddress + ' のパスワードを入力してください。'; },
    sorryWeDoNotSupport: 'お使いのメールアドレスの Web アドレス帳は対応していません。以下の「アドレス帳アプリケーション」をクリックして、お使いのコンピュータからのアドレスを使用してください。',
    pleaseSelectSecondPart: 'メールアドレスのドメイン (「gmail.com」など) を選択してください。',
    atSymbolNotAllowed: '@ マークがメール アドレスの最初の部分にないことを確認してください。',
    resetTextQ: 'テキストをリセットしますか？',
    resetTextToOriginalVersion: 'テキストをすべて元のバージョンにリセットしますか？加えた変更はすべて破棄されます。',
    changeQuestionsToPublic: 'の問をパブリックに変更しますか？',
    changingPrivateQuestionsToPublic: 'プライベートの質問をパブリックに変更すると、メンバーすべての回答を公開することになります。実行しますか？',
    youHaveUnsavedChanges: '変更は保存されていません。',
    pleaseEnterASiteName: 'ソーシャル ネットワークの名前 (「Tiny Clown Club」など) を入力してください',
    pleaseEnterShorterSiteName: '短い名前を入力してください (最大 64 文字)',
    pleaseEnterShorterSiteDescription: '短い説明を入力してください (最大 140 文字)',
    siteNameHasInvalidCharacters: '名前に無効な文字があります',
    thereIsAProblem: 'あなたの情報には問題があります',
    thisSiteIsOnline: 'このソーシャル ネットワークはオンラインになっています',
    online: '<strong>オンライン</strong>',
    onlineSiteCanBeViewed: '<strong>オンライン</strong> - プライバシー設定に従ってネットワークを表示できます。',
    takeOffline: 'オフラインにする',
    thisSiteIsOffline: 'このソーシャル ネットワークはオフラインになっています',
    offline: '<strong>オフライン</strong>',
    offlineOnlyYouCanView: '<strong>オフライン</strong> - このソーシャル ネットワークを表示できるのはあなただけです。',
    takeOnline: 'オンラインにする',
    themeSettings: 'テーマの設定',
    addYourOwnCss: '詳細',
    error: 'エラー',
    pleaseEnterTitleForFeature: function(displayName) { return '' + displayName + ' 機能のタイトルを入力してください'; },
    thereIsAProblemWithTheInformation: '入力された情報に問題があります',
    photos: '写真',
    videos: 'ビデオ',
    pleaseEnterTheChoicesFor: function(questionTitle) { return '「' + questionTitle + '」の選択項目を入力してください (ハイキング、読書、ショッピングなど)'; },
    pleaseEnterTheChoices: '選択項目を入力してください (ハイキング、読書、ショッピングなど)',
    email: '電子メール',
    subject: '件名',
    message: 'メッセージ',
    send: '送信',
    cancel: 'キャンセル',
    go: '検索',
    areYouSureYouWant: 'これを実行しますか？',
    processing: '処理中…',
    pleaseKeepWindowOpen: '処理が継続している間は、このブラウザのウィンドウを開いたままにしてください。これには数分かかることがあります。',
    complete: '完了です',
    processIsComplete: '処理が完了しました。',
    processingFailed: '処理に失敗しました。後でもう 1 度試してください。',
    ok: 'OK',
    body: '本文',
    pleaseEnterASubject: '件名を入力してください',
    pleaseEnterAMessage: 'メッセージを入力してください',
    pleaseChooseFriends: 'メッセージを送信する前に、友達を選択してください。',
    thereHasBeenAnError: 'エラーが発生しました。',
    thereWasAProblem: 'コンテンツを追加中に問題が発生しました。後でもう 1 度試してください。',
    fileNotFound: 'ファイルが見つかりませんでした',
    pleaseProvideADescription: '説明を入力してください',
    pleaseEnterSomeFeedback: 'フィードバックを入力してください',
    title: 'タイトル：',
    setAsMainSiteFeature: '主要機能として設定',
    thisIsTheMainSiteFeature: 'これが主要機能です',
    customized: 'カスタマイズ済み',
    copyHtmlCode: 'HTML コードのコピー',
    playerSize: 'プレーヤーのサイズ',
    selectSource: 'ソースの選択',
    myAlbums: 'マイ アルバム',
    myMusic: 'マイ ミュージック',
    myVideos: 'マイ ビデオ',
    showPlaylist: 'プレイリストの表示',
    change: '変更',
    changing: '変更中...',
    changeSettings: '設定を変更しますか？',
    keepWindowOpenWhileChanging: 'プライバシー設定が変更されている間は、このブラウザのウィンドウを開いたままにしてください。この処理には数分かかることがあります。',
    htmlNotAllowed: 'HTML は使用できません',
    noFriendsFound: '検索条件に一致する友達は見つかりませんでした。',
    yourSubject: '件名',
    yourMessage: 'メッセージ',
    pleaseEnterFbApiKey: 'Facebook の API キーを入力してください。',
    pleaseEnterValidFbApiKey: '有効な Facebook の API キーを入力してください。',
    pleaseEnterFbApiSecret: 'Facebook の API シークレットを入力してください。',
    pleaseEnterValidFbApiSecret: '有効な Facebook の API シークレットを入力してください。',
    pleaseEnterFbTabName: 'Facebook アプリケーション タブの名前を入力してください。',
    pleaseEnterValidFbTabName: function(maxChars) {
                                   return 'Facebook アプリケーション タブの名前が長すぎます。最大の長さは ' + maxChars + ' 文字 ' + (maxChars == 1 ? '' : 's') + ' です。';
                               },
    newTab: '新規タブ',
    saveYourChanges: 'このタブの変更内容を保存しますか？',
    areYouSureNavigateAway: '保存されていない変更内容があります。',
    youTabUpdated: 'タブが保存されました。',
    resetToDefaults: 'デフォルトに戻す',
    youNaviWillbeRestored: '操作タブは、ネットワークのデフォルト設定にリセットされます。',
    hiddenWarningTop: function(n) { return 'このタブはまだネットワークに追加されていません。最上レベルのタブは '+n+' 個に制限されています。'+ '最上レベルのタブを削除するか、最上レベルのタブをサブタブに変更してください。' },
    hiddenWarningSub: function(n) { return 'このタブはまだネットワークに追加されていません。最上レベルの各タブに追加できるサブタブは '+n+' 個までです。'+ 'サブタブを削除するか、サブタブを最上レベルのタブに変更してください。' },
    removeConfirm: 'この最上レベルのタブを削除すると、含まれるサブタブも同時に削除されます。継続するには「OK」をクリックしてください。',
    saveYourTab: 'このタブを保存しますか？',
    yes: 'はい',
    no: 'いいえ',
    youMustSpecifyTabName: 'タブ名を指定してください。'
});


dojo.evalObjPath('xg.music.nls', true);
dojo.lang.mixin(xg.music.nls, xg.index.i18n, {
    play: '再生',
    pleaseSelectTrackToUpload: 'アップロードする曲を選択してください。',
    pleaseEnterTrackLink: '曲の URL を入力してください。',
    thereAreUnsavedChanges: '保存されていない変更内容があります。',
    autoplay: '自動再生',
    showPlaylist: 'プレイリストの表示',
    playLabel: '再生',
    url: 'URL',
    rssXspfOrM3u: 'Rss、xspf、m3u',
    save: '上書き保存',
    cancel: 'キャンセル',
    edit: '編集',
    shufflePlaylist: 'プレイリストのランダム再生',
    fileIsNotAnMp3: 'ファイルの 1 つが MP3 形式ではないようです。このままアップロードしますか？',
    entryNotAUrl: 'エントリの 1 つが URL ではないようです。必ず、エントリがすべて <kbd>http://</kbd> で始まるようにしてください。'
});


dojo.evalObjPath('xg.page.nls', true);
dojo.lang.mixin(xg.page.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return '文字数 (' + n + ') が最大数 (' + maximum + ') を超えています'; },
    pleaseEnterContent: 'ページのコンテンツを入力してください',
    pleaseEnterTitle: 'このページのタイトルを入力してください',
    pleaseEnterAComment: 'コメントを入力してください',
    deleteThisComment: 'コメントを削除してもよろしいですか？',
    save: '上書き保存',
    cancel: 'キャンセル',
    edit: '編集',
    close: '閉じる',
    displayPagePosts: 'ページの投稿を表示',
    directory: 'ディレクトリ',
    displayTab: '表示タブ',
    addAnotherPage: '他のページの追加',
    tabText: 'タブ テキスト',
    urlDirectory: 'URL ディレクトリ',
    displayTabForPage: 'このページのタブを表示/非表示',
    tabTitle: 'タブのタイトル',
    remove: '削除',
    thereIsAProblem: 'あなたの情報には問題があります'
});


dojo.evalObjPath('xg.photo.nls', true);
dojo.lang.mixin(xg.photo.nls, xg.index.i18n, {
    randomOrder: 'ランダムな順番',
    untitled: 'タイトル未設定',
    photos: '写真',
    edit: '編集',
    photosFromAnAlbum: 'アルバム',
    show: '表示',
    rows: '行',
    cancel: 'キャンセル',
    save: '上書き保存',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return '文字数 (' + n + ') が最大数 (' + maximum + ') を超えています'; },
    pleaseSelectPhotoToUpload: 'アップロードする写真を選択してください。',
    importingNofMPhotos: function(n,m) { return '<span id="currentP">' + n + '</span> 枚の ' + m + ' 写真をインポート中です。'},
    starting: '開始中…',
    done: '完了！',
    from: 'から',
    display: '表示',
    takingYou: '写真の場所に移動中です…',
    anErrorOccurred: 'エラーが発生しました。このページの一番下にあるリンクを使用して、この問題を報告してください。',
    weCouldntFind: '写真がありません。他の写真を試してみますか？'
});


dojo.evalObjPath('xg.activity.nls', true);
dojo.lang.mixin(xg.activity.nls, xg.index.i18n, {
    edit: '編集',
    show: '表示',
    events: 'イベント',
    setWhatActivityGetsDisplayed: '表示するアクティビティを設定',
    save: '上書き保存',
    cancel: 'キャンセル'
});


dojo.evalObjPath('xg.profiles.nls', true);
dojo.lang.mixin(xg.profiles.nls, xg.index.i18n, {
    removeFriendTitle: function(username) {return ' '+ username + ' を友達として削除'; },
    removeFriendConfirm: function(username) {return ' '+ username + ' 友達として削除してもよろしいですか？'},
    pleaseEnterValueForPost: '投稿の値を入力してください',
    edit: '編集',
    recentlyAdded: '最近追加された項目',
    featured: 'フィーチャー',
    iHaveRecentlyAdded: '最近追加した項目',
    fromTheSite: 'ソーシャル ネットワークから',
    cancel: 'キャンセル',
    save: '上書き保存',
    loading: '読み込み中…',
    addAsFriend: '友達として追加',
    requestSent: 'リクエストが送信されました！',
    sendingFriendRequest: '友達にリクエストを送信中',
    thisIsYou: 'これはあなたです！',
    isYourFriend: 'あなたの友達は？',
    isBlocked: 'ブロックされています',
    pleaseEnterPostBody: '投稿の本文に何か入力してください',
    pleaseEnterChatter: 'コメントに何か入力してください',
    letMeApproveChatters: '投稿する前にコメントを承認しますか？',
    noPostChattersImmediately: 'いいえ – コメントをすぐに投稿する',
    yesApproveChattersFirst: 'はい – まずコメントを承認する',
    yourCommentMustBeApproved: 'コメントは全員に対して表示するには、承認する必要があります。',
    reallyDeleteThisPost: 'この記事を削除しますか？',
    commentWall: 'コメント ウォール',
    commentWallNComments: function(n) {
        switch(n) {
            case 1: return 'コメント ウォール (1 件のコメント)';
            default: return 'コメント ウォール (' + n + ' 件のコメント)';
        }
    },
    display: '表示',
    from: 'から',
    show: '表示',
    rows: '行',
    posts: '投稿',
    returnToDefaultWarning: 'これはすべての機能を移動して、マイ ページにあるテーマをネットワークのデフォルトに戻します。続行しますか？',
    networkError: 'ネットワーク エラー',
    wereSorry: '現時点では新規レイアウトを保存することができません。インターネットの接続が切れた可能性があります。接続を確認してもう一度試してください。',
    addFeature: '機能の追加',
    addFeatureConfirmation: function(linkAttributes) { return '<p>マイ ページに新しい機能を追加しようとしています。この機能はサードパーティが開発したものです。</p> <p>\'機能の追加\' をクリックすると、プラットフォーム アプリケーションの<a ' + linkAttributes + '>使用規約</a>に同意したことになります。</p>'; }
});


dojo.evalObjPath('xg.shared.nls', true);
dojo.lang.mixin(xg.shared.nls, xg.index.i18n, {
    yourMessage: 'メッセージ',
    updateMessage: 'メッセージの更新',
    updateMessageQ: 'メッセージを更新しますか？',
    removeWords: '送信したメールが必ず届くようにするには、戻って、次の単語を変更または削除することをお勧めします。',
    warningMessage: 'このメールにある一部の単語が原因で、メールが迷惑メール フォルダに配信されたようです。',
    errorMessage: 'このメールにある 6 つ以上の単語が原因で、メールが迷惑メール フォルダに配信されたようです。',
    goBack: '戻る',
    sendAnyway: 'とにかく送信',
    messageIsTooLong: function(n,m) { return '最大文字数は '+m+' です。' },
    locationNotFound: function(location) { return '<em>' + location + '</em> は見つかりませんでした。'; },
    confirmation: '確認',
    showMap: 'マップの表示',
    hideMap: 'マップの非表示',
    yourCommentMustBeApproved: 'コメントは全員に対して表示するには、承認する必要があります。',
    nComments: function(n) {
        switch(n) {
            case 1: return '1 コメント';
            default: return n + 'コメント';
        }
    },
    pleaseEnterAComment: 'コメントを入力してください',
    uploadAPhoto: '写真のアップロード',
    uploadAnImage: '画像のアップロード',
    uploadAPhotoEllipsis: '写真のアップロード',
    useExistingImage: '以下の既存画像を使用します。',
    existingImage: '既存画像',
    useThemeImage: '以下のテーマ画像を使用します。',
    themeImage: 'テーマ画像',
    noImage: '画像なし',
    uploadImageFromComputer: 'コンピュータの画像をアップロード',
    tileThisImage: 'この画像を並べて表示します',
    done: '完了',
    currentImage: '現在の画像',
    pickAColor: '色を選択…',
    openColorPicker: 'カラー ピッカーを開く',
    transparent: '透明',
    loading: '読み込み中…',
    ok: 'OK',
    save: '上書き保存',
    cancel: 'キャンセル',
    saving: '保存中…',
    addAnImage: '画像の追加',
    uploadAFile: 'ファイルのアップロード',
    pleaseEnterAWebsite: 'Web サイトのアドレスを入力してください',
    pleaseEnterAFileAddress: 'ファイルのアドレスを入力してください',
    bold: '太字',
    italic: '斜体',
    underline: '下線',
    strikethrough: '取り消し線',
    addHyperink: 'ハイパーリンクの追加',
    options: 'オプション',
    wrapTextAroundImage: '画像の周囲にテキストを配置しますか？',
    imageOnLeft: '画像を左に配置しますか？',
    imageOnRight: '画像を右に配置しますか？',
    createThumbnail: 'サムネールを作成しますか？',
    pixels: 'ピクセル',
    createSmallerVersion: '画像の小さいバージョンを作成して表示します。ピクセルの幅を設定します。',
    popupWindow: 'ポップアップ ウィンドウですか？',
    linkToFullSize: 'ポップアップ ウィンドウにあるフルサイズの画像にリンクします。',
    add: '追加',
    keepWindowOpen: 'アップロード中は、このブラウザのウィンドウを開いたままにしてください。',
    cancelUpload: 'アップロードのキャンセル',
    pleaseSelectAFile: '画像ファイルを選択してください',
    pleaseSpecifyAThumbnailSize: 'サムネールのサイズを指定してください',
    thumbnailSizeMustBeNumber: 'サムネールのサイズは数字で指定してください',
    addExistingImage: 'または既存の画像を挿入してください',
    addExistingFile: 'または既存のファイルを挿入',
    clickToEdit: 'クリックして編集します',
    sendingFriendRequest: '友達にリクエストを送信中',
    requestSent: 'リクエストが送信されました！',
    pleaseCorrectErrors: 'これらのエラーを修正してください',
    noo: '新規',
    none: 'なし',
    joinNow: '今すぐ参加',
    join: '参加',
    addToFavorites: 'お気に入りに追加',
    removeFromFavorites: 'お気に入りから削除',
    follow: '登録',
    stopFollowing: '登録の解除',
    pendingPromptTitle: 'メンバーシップ保留の承認',
    youCanDoThis: 'この操作は、管理者があなたのメンバーシップを承認した後に実行できます。',
    editYourTags: 'タグの編集',
    addTags: 'タグの追加',
    editLocation: '場所の編集',
    editTypes: 'イベントの種類の編集',
    charactersLeft: function(n) {
        if (n >= 0) {
            return '&nbsp;(' + n + ' 左)';
        } else {
            return  '&nbsp;(' + Math.abs(n) + ' 上)';
        }
    },
    commentWall: 'コメント ウォール',
    commentWallNComments: function(n) { switch(n) { case 1: return 'コメント ウォール (1 件のコメント)'; default: return 'コメント ウォール (' + n + ' 件のコメント)'; } }
});


dojo.evalObjPath('xg.video.nls', true);
dojo.lang.mixin(xg.video.nls, xg.index.i18n, {
    edit: '編集',
    display: '表示',
    detail: '詳細',
    player: 'プレーヤー',
    from: 'から',
    show: '表示',
    videos: 'ビデオ',
    cancel: 'キャンセル',
    save: '上書き保存',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return '文字数 (' + n + ') が最大数 (' + maximum + ') を超えています'; },
    approve: '承認',
    approving: '承認中…',
    keepWindowOpenWhileApproving: 'ビデオの承認中は、このブラウザのウィンドウを開いたままにしてください。この処理には数分かかることがあります。',
    'delete': '削除',
    deleting: '削除中…',
    keepWindowOpenWhileDeleting: 'ビデオの削除中は、このブラウザのウィンドウを開いたままにしてください。この処理には数分かかることがあります。',
    pasteInEmbedCode: '他のサイトからのビデオの埋め込みコードに貼り付けてください。',
    pleaseSelectVideoToUpload: 'アップロードするビデオを選択してください。',
    embedCodeContainsMoreThanOneVideo: '埋め込みコードに複数のビデオが入っています。コードには、必ず 1 つだけの <オブジェクト> または <埋め込み> タグが含まれるようにしてください。',
    embedCodeMissingTag: '埋め込みコードに &lt;embed&gt; または &lt;object&gt; タグがありません。',
    fileIsNotAMov: 'このファイルは .mov、.mpg、.mp4、.avi、.3gp、.wmv のいずれでもないようです。このままファイルをアップロードしますか？',
    embedHTMLCode: 'HTML 埋め込みコード：',
    directLink: '直接リンク',
    addToMyspace: 'MySpace に追加',
    shareOnFacebook: 'Facebook で共有',
    addToOthers: 'その他に追加'
});


dojo.evalObjPath('xg.uploader.nls', true);
dojo.lang.mixin(xg.uploader.nls, xg.index.i18n, {
    fileBrowserHeader: 'マイ コンピュータ',
    fileRoot: 'マイ コンピュータ',
    fileInformationHeader: '情報',
    uploadHeader: 'アップロードするファイル',
    dragOutInstructions: 'ファイルを外にドラッグして削除',
    dragInInstructions: 'ファイルをここにドラッグ',
    selectInstructions: 'ファイルを選択',
    files: 'ファイル',
    totalSize: '合計サイズ',
    fileName: '名前',
    fileSize: 'サイズ',
    nextButton: '次へ >',
    okayButton: 'OK',
    yesButton: 'はい',
    noButton: 'いいえ',
    uploadButton: 'アップロード',
    cancelButton: 'キャンセル',
    backButton: '戻る',
    continueButton: '続行',
    uploadingStatus: function(n, m) { return 'アップロード中：' + n + ' /' + m; },
    uploadLimitWarning: function(n) { return '一度に ' + n + ' のファイルをアップロードできます。'; },
    uploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return '既に、最大数のファイルを追加しています。';
            case 1: return 'ファイルをもう 1 つアップロードできます。';
            default: return 'あと ' + n + ' 個のファイルをアップロードできます。';
        }
    },
    uploadingLabel: 'アップロード中...',
    uploadingInstructions: 'アップロード中は、このウィンドウを開いたままにしてください',
    iHaveTheRight: '<a href="/main/authorization/termsOfService">サービス規約</a>に従って、私にはこれらのファイルをアップロードする権利があります。',
    updateJavaTitle: 'Java の更新',
    updateJavaDescription: '一括アップローダにはもっと新しい Java が必要です。[OK] をクリックすると、Java を入手できます。',
    batchEditorLabel: 'すべてのアイテムの情報を編集します',
    applyThisInfo: 'この情報を以下のファイルに適用します',
    titleProperty: 'タイトル',
    descriptionProperty: '説明',
    tagsProperty: 'タグ',
    viewableByProperty: '表示可能なユーザー',
    viewableByEveryone: '全員',
    viewableByFriends: '友達のみ',
    viewableByMe: '自分のみ',
    albumProperty: 'アルバム',
    artistProperty: 'アーティスト',
    enableDownloadLinkProperty: 'ダウンロードのリンクを有効にします',
    enableProfileUsageProperty: 'この曲を他の人のページに掲載することを許可します',
    licenseProperty: 'ライセンス',
    creativeCommonsVersion: '3.0',
    selectLicense: '— ライセンスの選択 —',
    copyright: '© All Rights Reserved',
    ccByX: function(n) { return 'クリエイティブ コモンズ アトリビューション (Creative Commons Attribution)' + n; },
    ccBySaX: function(n) { return 'クリエイティブ コモンズ アトリビューション、同類を共有 (Creative Commons Attribution Share Alike)' + n; },
    ccByNdX: function(n) { return 'クリエイティブ コモンズ アトリビューション、非派生物 (Creative Commons Attribution No Derivatives)' + n; },
    ccByNcX: function(n) { return 'クリエイティブ コモンズ アトリビューション、非営利 (Creative Commons Attribution Non-commercial)' + n; },
    ccByNcSaX: function(n) { return 'クリエイティブ コモンズ アトリビューション、非営利/同類を共有 (Creative Commons Attribution Non-commercial Share Alike)' + n; },
    ccByNcNdX: function(n) { return 'クリエイティブ コモンズ アトリビューション、非営利/非派生物 (Creative Commons Attribution Non-commercial No Derivatives)' + n; },
    publicDomain: 'パブリック ドメイン',
    other: 'その他',
    errorUnexpectedTitle: '',
    errorUnexpectedDescription: 'エラーが発生しました。もう一度試してください。',
    errorTooManyTitle: 'アイテムが多過ぎます',
    errorTooManyDescription: function(n) { return '一度に ' + n + ' 項目のアイテムしかアップロードできません。'; },
    errorNotAMemberTitle: '許可されていません',
    errorNotAMemberDescription: 'アップロードするにはメンバーである必要があります',
    errorContentTypeNotAllowedTitle: '許可されていません',
    errorContentTypeNotAllowedDescription: 'この種類のコンテンツをアップロードすることはできません。',
    errorUnsupportedFormatTitle: '',
    errorUnsupportedFormatDescription: 'この種類のファイルには対応していません。',
    errorUnsupportedFileTitle: '',
    errorUnsupportedFileDescription: 'foo.exe は非対応のファイル形式です。',
    errorUploadUnexpectedTitle: '',
    errorUploadUnexpectedDescription: function(file) {
        return file ?
            (' ' + file + ' ファイルに問題があるようです。残りのファイルをアップロードするには、これをリストから削除してください。') :
            'リストのトップにあるファイルに問題があるようです。残りのファイルをアップロードするには、これを削除してください。';
    },
    cancelUploadTitle: 'アップロードをキャンセルしますか？',
    cancelUploadDescription: '残りのアップロードをキャンセルしますか？',
    uploadSuccessfulTitle: 'アップロードが完了しました',
    uploadSuccessfulDescription: 'アップロードにアクセスするまでお待ちください...',
    uploadPendingDescription: 'ファイルのアップロードは完了していますが、承認を待っています。',
    photosUploadHeader: 'アップロードする写真',
    photosDragOutInstructions: '写真を外にドラッグして削除します',
    photosDragInInstructions: '写真をここにドラッグします',
    photosSelectInstructions: '写真の選択',
    photosFiles: '写真',
    photosUploadingStatus: function(n, m) { return '写真のアップロード中：' + n + ' /' + m; },
    photosErrorTooManyTitle: '写真が多過ぎます',
    photosErrorTooManyDescription: function(n) { return '一度にアップロードできる写真は ' + n + ' 枚までです。'; },
    photosErrorContentTypeNotAllowedDescription: '写真のアップロードが無効なりました。',
    photosErrorUnsupportedFormatDescription: '.jpg、.gif、.png のいずれかのファイル形式の画像しかアップロードできません。',
    photosErrorUnsupportedFileDescription: function(n) { return n + '.jpg、.gif、.png のいずれのファイル形式でもありません。'; },
    photosBatchEditorLabel: 'すべての写真の情報を編集します',
    photosApplyThisInfo: '以下の写真にこの情報を適用します',
    photosErrorUploadUnexpectedDescription: function(file) {
        return file ?
            ('' + file + ' ファイルに問題があるようです。残りの写真をアップロードするには、これをリストから削除してください。') :
            'リストのトップにある写真に問題があるようです。残りの写真をアップロードするには、これを削除してください。';
    },
    photosUploadSuccessfulDescription: '写真にアクセスするまでお待ちください...',
    photosUploadPendingDescription: '写真のアップロードは完了していますが、承認を待っています。',
    photosUploadLimitWarning: function(n) { return '一度に ' + n + ' 枚の写真をアップロードできます。'; },
    photosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return '既に、最大数の写真をアップロードしています。';
            case 1: return 'もう 1 枚写真をアップロードできます。';
            default: return 'あと ' + n + ' 枚の写真をアップロードできます。';
        }
    },
    photosIHaveTheRight: '<a href="/main/authorization/termsOfService"> サービス規約</a>に従って、私にはこれらの写真をアップロードする権利があります。',
    videosUploadHeader: 'アップロードするビデオ',
    videosDragInInstructions: 'ビデオをここにドラッグします',
    videosDragOutInstructions: 'ビデオを外にドラッグして削除します',
    videosSelectInstructions: 'ビデオの選択',
    videosFiles: 'ビデオ',
    videosUploadingStatus: function(n, m) { return 'ビデオのアップロード中：' + n + ' /' + m; },
    videosErrorTooManyTitle: 'ビデオが多過ぎます',
    videosErrorTooManyDescription: function(n) { return '一度に' + n + ' 本のビデオしかアップロードできません。'; },
    videosErrorContentTypeNotAllowedDescription: 'ビデオのアップロードが無効になりました。',
    videosErrorUnsupportedFormatDescription: '.avi、.mov、.mp4、.wmv、.mpg のいずれかのファイル形式のビデオしかアップロードできません。',
    videosErrorUnsupportedFileDescription: function(x) { return x + '.avi、 .mov、 .mp4、 .wmv、.mpg のいずれのファイル形式でもありません。'; },
    videosBatchEditorLabel: 'すべてのビデオの情報を編集します',
    videosApplyThisInfo: '以下のビデオにこの情報を適用します',
    videosErrorUploadUnexpectedDescription: function(file) {
        return file ?
            ('' + file + ' ファイルに問題があるようです。残りのビデオをアップロードするには、これをリストから削除してください。') :
            'リストのトップにあるビデオに問題があるようです。残りのビデオをアップロードするには、これを削除してください。';
    },
    videosUploadSuccessfulDescription: 'ビデオにアクセスするまでお待ちください...',
    videosUploadPendingDescription: 'ビデオのアップロードは完了していますが、承認を待っています。',
    videosUploadLimitWarning: function(n) { return '一度に ' + n + ' 本のビデオをアップロードできます。'; },
    videosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return '既に、最大数のビデオをアップロードしています。';
            case 1: return 'もう 1 本のビデオをアップロードできます。';
            default: return 'あと ' + n + ' 本のビデオをアップロードできます。';
        }
    },
    videosIHaveTheRight: '<a href="/main/authorization/termsOfService"> サービス規約</a>に従って、私にはこれらのビデオをアップロードする権利があります。',
    musicUploadHeader: 'アップロードする曲',
    musicTitleProperty: '曲のタイトル',
    musicDragOutInstructions: '曲をフィールド外にドラッグすると削除できます。',
    musicDragInInstructions: '曲をここにドラッグします',
    musicSelectInstructions: '曲の選択',
    musicFiles: '曲',
    musicUploadingStatus: function(n, m) { return '曲のアップロード中：' + n + ' /' + m; },
    musicErrorTooManyTitle: '曲が多過ぎます',
    musicErrorTooManyDescription: function(n) { return '一度に ' + n + ' 曲しかアップロードできません。'; },
    musicErrorContentTypeNotAllowedDescription: '曲のアップロードが無効になりました。',
    musicErrorUnsupportedFormatDescription: '.mp3 ファイル形式の曲しかアップロードできません。',
    musicErrorUnsupportedFileDescription: function(x) { return x + '.mp3 ファイルではありません。'; },
    musicBatchEditorLabel: 'すべての曲の情報を編集します',
    musicApplyThisInfo: '以下の曲にこの情報を適用します',
    musicErrorUploadUnexpectedDescription: function(file) {
        return file ?
            ('' + file + ' ファイルに問題があるようです。残りの曲をアップロードするには、これをリストから削除してください。') :
            'リストのトップにある曲に問題があるようです。残りの曲をアップロードするには、これを削除してください。';
    },
    musicUploadSuccessfulDescription: '曲にアクセスするまでお待ちください...',
    musicUploadPendingDescription: '曲のアップロードは完了していますが、承認を待っています。',
    musicUploadLimitWarning: function(n) { return '一度に ' + n + ' 曲をアップロードできます。'; },
    musicUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return '既に、最大数の曲をアップロードしています。';
            case 1: return 'もう 1 曲アップロードできます。';
            default: return 'あと ' + n + ' 曲アップロードできます。';
        }
    },
    musicIHaveTheRight: '<a href="/main/authorization/termsOfService"> サービス規約</a>に従って、私にはこれらの曲をアップロードする権利があります。'
});


dojo.evalObjPath('xg.events.nls', true);
dojo.lang.mixin(xg.events.nls, xg.index.i18n, {
    pleaseEnterTitle: 'イベントのタイトルを入力してください',
    pleaseEnterDescription: 'イベントの説明を入力してください',
    messageIsTooLong: function(n) { return 'メッセージが長過ぎます。'+n+' 文字以下で入力してください。'; },
    pleaseEnterLocation: 'イベントの場所を入力してください',
    pleaseChooseImage: 'イベントの画像を選択してください',
    pleaseEnterType: '少なくとも 1 種類のイベントを入力してください',
    sendMessageToGuests: 'ゲストにメッセージを送信します',
    sendMessageToGuestsThat: '以下のゲストにメッセージを送信します。',
    areAttending: '出席する',
    mightAttend: 'たぶん出席する',
    haveNotYetRsvped: 'まだ返事をしていない',
    areNotAttending: '出席しない',
    yourMessage: 'メッセージ',
    send: '送信',
    sending: '送信中…',
    yourMessageIsBeingSent: 'メッセージの送信中です。',
    messageSent: 'メッセージが送信されました。',
    yourMessageHasBeenSent: 'メッセージが送信されました。',
    chooseRecipient: '宛先を選択してください。',
    pleaseEnterAMessage: 'メッセージを入力してください',
    thereHasBeenAnError: 'エラーが発生しました。'
});


dojo.evalObjPath('xg.notes.nls', true);
dojo.lang.mixin(xg.notes.nls, xg.index.i18n, {
    addNewNote: '新規メモの追加',
    pleaseEnterNoteTitle: 'メモのタイトルを入力してください',
    noteTitleTooLong: 'メモのタイトルが長過ぎます',
    pleaseEnterNoteEntry: 'メモのエントリを入力してください'
});