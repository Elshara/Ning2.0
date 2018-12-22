dojo.provide('xg.shared.messagecatalogs.ko_KR');

dojo.require('xg.index.i18n');

/**
 * Texts for the ko_KR locale. 
 */
// Use UTF-8 byte sequences instead of HTML entities, e.g., & instead of &amp;, â€¦ instead of &hellip;  [Jon Aquino 2007-01-10]

dojo.evalObjPath('xg.events.nls', true);
dojo.lang.mixin(xg.events.nls, xg.index.i18n, {
    pleaseChooseImage: '이벤트용 이미지를 선택하시기 바랍니다',
    pleaseEnterAMessage: '메시지를 입력하시기 바랍니다',
    pleaseEnterDescription: '이벤트용 설명을 입력하시기 바랍니다',
    pleaseEnterLocation: '이벤트용 위치를 입력하시기 바랍니다',
    pleaseEnterTitle: '이벤트용 제목을 입력하시기 바랍니다',
    pleaseEnterType: '이벤트용 타입을 최소한 1개 선택하시기 바랍니다',
    send: '전송',
    sending: '전송 중…',
    thereHasBeenAnError: '오류 발생',
    yourMessage: '회원님의 메시지',
    yourMessageHasBeenSent: '메시지를 전송했습니다.',
    yourMessageIsBeingSent: '회원님의 메시지를 전송 중입니다.'
});

dojo.evalObjPath('xg.feed.nls', true);
dojo.lang.mixin(xg.feed.nls, xg.index.i18n, {
    edit: '편집',
    title: '제목:',
    feedUrl: 'URL:',
    show: '보기:',
    titles: '제목만',
    titlesAndDescriptions: '세부 내용 보기',
    display: '보여주기',
    cancel: '취소하기',
    save: '저장하기',
    loading: '로딩 중...',
    items: '항목'
});


dojo.evalObjPath('xg.gadgets.nls', true);
dojo.lang.mixin(xg.gadgets.nls, xg.index.i18n, {
    edit: '편집',
    title: '제목:',
    feedUrl: 'URL:',
    cancel: '취소하다',
    save: '저장하기',
    loading: '로딩 중…',
    removeGadget: 'Gadget를 제거합니다',
    findGadgetsInDirectory: 'Gadget 디렉터리에서 Gadget를 찾습니다'
});


dojo.evalObjPath('xg.forum.nls', true);
dojo.lang.mixin(xg.forum.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return '문자의 수 (' + n + ')가 최대 한도 (' + maximum + ')자를 초과합니다 '; },
    pleaseEnterFirstPost: '토론할 첫 게시물을 작성하세요',
    pleaseEnterTitle: '토론의 주제를 입력하세요',
    save: '저장하기',
    cancel: '취소하기',
    yes: '예',
    no: '아니오',
    edit: '편집',
    deleteCategory: '카테고리 삭제',
    discussionsWillBeDeleted: '이 카테고리에 속하는 모든 토론이 삭제됩니다.',
    whatDoWithDiscussions: '이 카테고리에 속하는 토론들을 어떻게 처리할까요?',
    moveDiscussionsTo: '토론을 다음으로 이동:',
    moveToCategory: '카테고리로 이동...',
    deleteDiscussions: '토론 삭제',
    'delete': '삭제하기',
    deleteReply: '답글 삭제',
    deleteReplyQ: '이 답글을 삭제할까요?',
    deletingReplies: '답글 삭제 중...',
    doYouWantToRemoveReplies: '이 코멘트에 대한 답글을 제거하기 원합니까?',
    pleaseKeepWindowOpen: '처리되는 동안 이 브라우저 창을 계속해서 열어 놓으세요.  몇 분 정도 소요될 수 있습니다.',
    from: '보낸 사람',
    show: '보기',
    discussions: '토론',
    discussionsFromACategory: '카테고리에 속하는 토론들...',
    display: '표시',
    items: '항목',
    view: '보기'
});


dojo.evalObjPath('xg.groups.nls', true);
dojo.lang.mixin(xg.groups.nls, xg.index.i18n, {
    pleaseChooseAName: '회원님의 그룹명을 선택하세요.',
    pleaseChooseAUrl: '회원님의 그룹 웹 주소를 선택하세요.',
    urlCanContainOnlyLetters: '웹 주소는 문자와 번호만을 사용하여 만드실 수 있습니다 (빈 칸은 허용되지 않습니다).',
    descriptionTooLong: function(n, maximum) { return '회원님의 그룹 설명의 문자 수(' + n + ')가 최대 한도(' + maximum + ')를 초과합니다 '; },
    nameTaken: '죄송합니다 – 입력하신 이름은 이미 다른 회원이 사용하고 있습니다.  다른 이름을 선택하세요.',
    urlTaken: '죄송합니다 – 입력하신 웹 주소는 이미 사용 중입니다.  다른 웹 주소를 선택하세요.',
    whyNot: '왜 안 되죠?',
    groupCreatorDetermines: function(href) { return '그룹 운영자가 가입 여부를 결정합니다.  오해에 의해 자신이 차단됐다고 생각되면 <a ' + href + '>그룹 운영자에게 연락하세요</a> '; },
    edit: '편집',
    from: '보낸 사람',
    show: '보기',
    groups: '그룹',
    pleaseEnterName: '회원님의 이름을 입력하세요',
    pleaseEnterEmailAddress: '회원님의 이메일 주소를 입력하세요',
    xIsNotValidEmailAddress: function(x) { return x + ' is not a valid email address'; },
    save: '저장하기',
    cancel: '취소하기'
});


dojo.evalObjPath('xg.html.nls', true);
dojo.lang.mixin(xg.html.nls, xg.index.i18n, {
    edit: '편집',
    save: '저장하기',
    cancel: '취소하기',
    saving: '저장 중...',
    addAWidget: function(url) { return '이 글상자에 <a href="' + url + '">위젯을 추가</a>하세요 '; },
    contentsTooLong: function(maximum) { return '내용이 너무 깁니다.  ' + maximum + '자 이하로 사용해 주시기 바랍니다. '; }
});


dojo.evalObjPath('xg.index.nls', true);
dojo.lang.mixin(xg.index.nls, xg.index.i18n, {
    sendInvitation: '초대장 전송',
    sendInvitationToNFriends: function(n) {
        switch(n) {
            case 1: return '친구 한 명에게 초대장을 전송할까요? ';
            default: return '친구들 ' + n + '명에게 초대장을 전송할까요? ';
        }
    },
    showingNFriends: function(n, searchString) {
        switch(n) {
            case 1: return '"' + searchString + '" 검색 결과, 친구 1명을 찾았습니다. <a href="#">검색 결과 모두 보기</a>             ';
            default: return '"' + searchString + '" 검색 결과, 친구 ' + n + ' 명을 찾았습니다. <a href="#">검색 결과 모두 보기</a> ';
        }
    },
    sendMessage: '메시지(쪽지)를 발송합니다',
    sendMessageToNFriends: function(n) {
        switch(n) {
            case 1: return '친구(1명)에게 메시지를 보내시겠습니까? ';
            default: return '친구  ' + n + '명에게 메시지를 보내시겠습니까? ';
        }
    },
    invitingNFriends: function(n) {
        switch(n) {
            case 1: return '친구(1명)를 초대하고 있습니다…… ';
            default: return '친구 ' + n + '명을 초대하고 있습니다…… ';
        }
    },
    nFriends: function(n) {
        switch(n) {
            case 1: return '친구 1명…… ';
            default: return n + ' 친구 XXXX명…… ';
        }
    },
    sendingMessageToNFriends: function(n) {
        switch(n) {
            case 1: return '친구(1명)에게 메시지를 보내고 있습니다…… ';
            default: return '친구 ' + n + '명에게 메시지를 보내고 있습니다… ';
        }
    },
    noPeopleSelected: '수신인을 선택하지 않았습니다',
    sorryWeDoNotSupport: '죄송합니다. 저희는 사용자님의 이메일 계정이 사용하는 웹 주소록을 지원하지 않습니다. 아래의 ‘주소록 프로그램’을 클릭하여 사용자님의 컴퓨터에 있는 주소록을 사용해 보십시오.',
    pleaseChooseFriends: '메시지를 보내기에 앞서 친구를 선택하시기 바랍니다.',
    htmlNotAllowed: 'HTML은 사용할 수 없습니다',
    noFriendsFound: '검색 결과, 친구를 찾지 못 했습니다.',
    yourMessageOptional: '<label>회원님의 메시지</label>(선택사항)',
    pleaseChoosePeople: '초대할 사람들을 선택하세요.',
    pleaseEnterEmailAddress: '회원님의 이메일 주소를 입력하세요.',
    pleaseEnterPassword: function(emailAddress) { return '회원님의 ' + emailAddress + '에 대한 비밀번호를 입력하세요. '; },
    sorryWeDontSupport: '죄송합니다, Ning은 회원님의 이메일 주소에 연계된 웹 주소록을 지원하지 않습니다.  회원님의 컴퓨터에 저장되어 있는 주소를 사용하시려면 아래의 \'이메일 애플리케이션\'을 클릭해 보세요.',
    pleaseSelectSecondPart: '회원님의 이메일 주소의 두 번째 부분을 선택하세요, 예: gmail. com.',
    atSymbolNotAllowed: '이메일 주소의 첫 부분에 @ 부호를 적지 않도록 유의하세요.',
    resetTextQ: '텍스트를 재설정할까요?',
    resetTextToOriginalVersion: '정말로 회원님의 모든 텍스트를 원래의 버전으로 재설정하기 원하세요?  변경하신 모든 사항들을 잃게 됩니다.',
    changeQuestionsToPublic: '공개 질문으로 변경할까요?',
    changingPrivateQuestionsToPublic: '비공개 질문을 공개로 변경하면 회원들의 모든 답글이 공개됩니다.  확실하세요?',
    youHaveUnsavedChanges: '아직 저장하시지 않은 변경사항이 있습니다.',
    pleaseEnterASiteName: '사교 네트워크의 이름을 입력하세요, 예: Tiny Clown Club',
    pleaseEnterShorterSiteName: '더 짧은 이름을 입력해 주세요(최대 64자)',
    pleaseEnterShorterSiteDescription: '더 짧은 내용을 입력해 주세요(최대 250자)',
    siteNameHasInvalidCharacters: '이름에 사용할 수 없는 문자가 포함되어 있습니다',
    thereIsAProblem: '회원님이 기입하신 정보에 문제가 있습니다',
    thisSiteIsOnline: '이 사교 네트워크는 온라인 상태입니다',
    onlineSiteCanBeViewed: '<strong>온라인</strong> -네트워크의 공개 정도는 회원님의 프라이버시 설정 기준에 따릅니다.',
    takeOffline: '오프라인으로 설정',
    thisSiteIsOffline: '이 사교 네트워크는 오프라인 상태입니다',
    offlineOnlyYouCanView: '<strong>오프라인</strong> - 오직 회원님만 이 사교 네트워크를 보실 수 있습니다.',
    takeOnline: '온라인으로 설정',
    themeSettings: '테마 설정',
    addYourOwnCss: '고급',
    error: '오류',
    pleaseEnterTitleForFeature: function(displayName) { return '회원님의 ' + displayName + ' 기능의 제목을 입력하세요 '; },
    thereIsAProblemWithTheInformation: '입력하신 정보에 문제가 있습니다',
    photos: '사진',
    videos: '동영상',
    pleaseEnterTheChoicesFor: function(questionTitle) { return '"' + questionTitle + '"에 대한 선택사항들을 입력하세요. 예: 하이킹, 독서, 쇼핑 '; },
    pleaseEnterTheChoices: '선택사항들을 입력하세요. 예: 하이킹, 독서, 쇼핑',
    shareWithFriends: '친구들과 공유',
    email: '이메일',
    separateMultipleAddresses: '여러 개의 주소를 입력하실 경우 쉼표로 분리하세요',
    subject: '제목',
    message: '메시지',
    send: '보내기',
    cancel: '취소하기',
    pleaseEnterAValidEmail: '유효한 이메일 주소를 입력하세요',
    go: '"(로, 으로) 이동하다"',
    areYouSureYouWant: '그대로 실행하기 원하는 것이 확실합니까?',
    processing: '처리 중...',
    pleaseKeepWindowOpen: '처리되는 동안 이 브라우저 창을 계속해서 열어 놓으세요.  몇 분 정도 소요될 수 있습니다.',
    complete: '완료!',
    processIsComplete: '처리가 완료됐습니다.',
    ok: 'OK',
    body: '본문',
    pleaseEnterASubject: '제목을 입력하세요',
    pleaseEnterAMessage: '메시지를 입력하세요',
    thereHasBeenAnError: '오류가 발생했습니다',
    fileNotFound: '파일을 찾지 못 했습니다',
    pleaseProvideADescription: '내용을 입력해 주세요',
    pleaseEnterYourFriendsAddresses: '회원님 친구들의 주소나 Ning ID를 입력하세요',
    pleaseEnterSomeFeedback: '평가 소감이 있으시면 입력해 주세요',
    title: '제목:',
    setAsMainSiteFeature: 'Main Feature로 설정',
    thisIsTheMainSiteFeature: 'Main Feature입니다',
    customized: '나의 맞춤형 플레이어',
    copyHtmlCode: 'HTML 코드 복사',
    playerSize: '플레이어 크기',
    selectSource: '출처 선택',
    myAlbums: '나의 앨범',
    myMusic: '내 음악',
    myVideos: '내 비디오',
    showPlaylist: '플레이 리스트 보기',
    change: '변경',
    changing: '변경 중...',
    changePrivacy: '프라이버시 설정을 변경할까요?',
    keepWindowOpenWhileChanging: '프라이버시 설정을 변경하는 동안 이 브라우저 창을 계속해서 열어 놓으세요.  몇 분 정도 소요될 수 있습니다.',
    addingInstructions: '컨텐츠가 추가되는 동안, 이 창을 열어두시기 바랍니다.',
    addingLabel: '추가 중…',
    cannotKeepFiles: '더 많은 옵션을 보시려면, 회원님의 파일을 다시 선택하셔야 할 것입니다.  계속하시겠습니까?',
    done: '완료',
    looksLikeNotImage: '한 개 이상의 파일이 .jpg, .gif, 또는 .png 형식이 아닌 듯합니다.  이대로 업로드를 시도해보시겠습니까?',
    looksLikeNotMusic: '선택하신 파일이 .mp3형식이 아닌 것 같습니다.  이대로 업로드를 시도해보시겠습니까?',
    looksLikeNotVideo: '선택하신 파일이 .mov, .mpg, .mp4, .avi, .3gp 또는 .wmv 형식이 아닌 것 같습니다.  이대로 업로드를 시도해보시겠습니까?',
    messageIsTooLong: function(n) { return '메시지가 너무 깁니다.   '+n+'자 이내로 줄여주시기 바랍니다.'; },
    pleaseSelectPhotoToUpload: '업로드할 사진을 선택하시기 바랍니다.',
    processingFailed: '미안합니다, 처리하는 데 실패했습니다.  잠시 후, 다시 시도해보시기 바랍니다.',
    selectOrPaste: '비디오를 선택하거나 \'임베드\' 코드 붙여넣기를 하셔야 합니다.',
    selectOrPasteMusic: '노래를 선택하거나 URL 붙여넣기를 하셔야 합니다.',
    sendingLabel: '전송 중… .',
    thereWasAProblem: '회원님의 컨텐츠를 추가하는 데 문제가 발생했습니다.  잠시 후, 다시 시도해보시기 바랍니다.',
    uploadingInstructions: '업로드가 진행되는 동안에 이 창을 열어 두시기 바랍니다',
    uploadingLabel: '업로드 중…',
    youNeedToAddEmailRecipient: '이메일을 받을 사람을 추가하셔야 합니다.',
    yourMessage: '회원님의 메시지',
    yourMessageIsBeingSent: '회원님의 메시지를 전송 중입니다.',
    yourSubject: '제목'
});


dojo.evalObjPath('xg.music.nls', true);
dojo.lang.mixin(xg.music.nls, xg.index.i18n, {
    play: '재생하다',
    pleaseSelectTrackToUpload: '업로드할 곡을 선택하세요',
    pleaseEnterTrackLink: '곡이 위치한 URL을 입력하세요.',
    thereAreUnsavedChanges: '아직 저장되지 않은 변경사항이 있습니다.',
    autoplay: '자동재생',
    showPlaylist: '플레이 리스트 보기',
    playLabel: '재생',
    url: 'URL',
    rssXspfOrM3u: 'rss, xspf, 또는 m3u',
    save: '저장하기',
    cancel: '취소하기',
    edit: '편집',
    fileIsNotAnMp3: '파일 중 하나가 MP3가 아닌 것 같습니다.  그대로 업로드를 시도할까요?',
    entryNotAUrl: '입력 사항 중 하나가 URL이 아닌 것 같습니다.  모든 입력사항이 <kbd>http://</kbd>로 시작하는지 확인해 주세요.',
    shufflePlaylist: '재생목록 재구성(셔플)'
});

dojo.evalObjPath('xg.notes.nls', true);
dojo.lang.mixin(xg.notes.nls, xg.index.i18n, {
    addNewNote: '새 주 추가',
    noteTitleTooLong: '주의 제목이 너무 깁니다',
    pleaseEnterNoteEntry: '주의 입력내용을 입력하시기 바랍니다',
    pleaseEnterNoteTitle: '주 제목을 입력하시기 바랍니다!'
});

dojo.evalObjPath('xg.page.nls', true);
dojo.lang.mixin(xg.page.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return '문자의 수 (' + n + ')가 최대 한도 (' + maximum + ')자를 초과합니다 '; },
    pleaseEnterContent: '페이지 내용을 입력하세요',
    pleaseEnterTitle: '페이지 제목을 입력하세요',
    pleaseEnterAComment: '코멘트를 입력하세요.',
    deleteThisComment: '이 코멘트를 정말로 삭제하기 원하세요?',
    save: '저장하기',
    cancel: '취소하기',
    discussionTitle: '페이지 제목:',
    tags: '태그:',
    edit: '편집',
    close: '닫다',
    displayPagePosts: '페이지 게시물 표시',
    thereIsAProblem: '회원님의 정보에 문제가 있습니다'
});


dojo.evalObjPath('xg.photo.nls', true);
dojo.lang.mixin(xg.photo.nls, xg.index.i18n, {
    untitled: '제목 없슴',
    photos: '사진',
    edit: '편집',
    photosFromAnAlbum: '앨범',
    show: '보기',
    rows: '줄',
    cancel: '취소하기',
    save: '저장하기',
    deleteThisPhoto: '이 사진을 삭제할까요?',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return '문자의 수 (' + n + ')가 최대 한도 (' + maximum + ')자를 초과합니다 '; },
    weCouldNotLookUpAddress: function(address) { return '죄송합니다, 이 "' + address + '" 주소를 찾아 볼 수 없었습니다. '; },
    pleaseSelectPhotoToUpload: '업로드할 사진을 선택하세요.',
    pleaseEnterAComment: '코멘트를 입력하세요.',
    addToExistingAlbum: '기존 앨범에 추가',
    addToNewAlbumTitled: '라는 제목의 새 앨범에 추가...',
    deleteThisComment: '이 코멘트를 삭제할까요?',
    importingNofMPhotos: function(n,m) { return ' ' + m + '장의 사진 중 <span id="currentP">' + n + '</span>장의 가져오기를 실행 중입니다. ';},
    starting: '시작하고 있습니다...',
    done: '완료!',
    from: '보낸 사람',
    display: '보여주기',
    takingYou: '회원님의 사진을 보러 가는 중입니다...',
    anErrorOccurred: '죄송합니다, 오류가 발생했습니다.  이 페이지의 하단에 있는 링크를 이용해 이 오류 사항을 신고해 주세요.',
    weCouldntFind: '사진을 한 장도 찾지 못 했습니다!  다른 옵션을 시도해 보시겠어요?',
    randomOrder: '무작위 순서'
});


dojo.evalObjPath('xg.activity.nls', true);
dojo.lang.mixin(xg.activity.nls, xg.index.i18n, {
    edit: '편집',
    show: '보기',
    events: '이벤트',
    setWhatActivityGetsDisplayed: '어떤 활동을 표시할 것인지 설정하세요',
    save: '저장하기',
    cancel: '취소하기'
});


dojo.evalObjPath('xg.profiles.nls', true);
dojo.lang.mixin(xg.profiles.nls, xg.index.i18n, {
    pleaseEnterValueForPost: '게시물의 값을 입력하세요',
    pleaseProvideAValidDate: '유효한 날짜를 지정하세요',
    uploadAFile: '파일을 업로드하세요',
    pleaseEnterUrlOfLink: '링크의 URL을 입력하세요:',
    pleaseEnterTextOfLink: '어떤 텍스트에 링크하기 원하세요?',
    edit: '편집',
    recentlyAdded: '최근 추가 항목',
    featured: '추천',
    iHaveRecentlyAdded: '나의 최근 추가항목',
    fromTheSite: '사교 네트워크에서',
    cancel: '취소하기',
    save: '저장하기',
    loading: '로딩 중...',
    addAsFriend: '친구로 추가',
    requestSent: '요청 전송 완료!',
    sendingFriendRequest: '친구 만들기 요청 전송 중',
    thisIsYou: '회원님입니다!',
    isYourFriend: '회원님의 친구입니다',
    isBlocked: '차단돼 있습니다',
    pleaseEnterAComment: '코멘트를 입력하세요.',
    pleaseEnterPostBody: '게시물 본문에 내용을 입력하세요',
    pleaseSelectAFile: '파일을 선택하세요',
    pleaseEnterChatter: '회원님의 코멘트에 내용을 입력하세요',
    toggleBetweenHTML: 'HTML 코드 표시/숨기기',
    attachAFile: '파일을 첨부하세요',
    addAPhoto: '사진 추가',
    insertALink: '링크 삽입',
    changeTextSize: '글자 크기 변경',
    makeABulletedList: '불릿 목록 작성',
    makeANumberedList: '번호 목록 작성',
    crossOutText: '글 취소하기',
    underlineText: '글에 밑줄 귿기',
    italicizeText: '이탤릭체로 만들기',
    boldText: '굵은 체로 만들기',
    letMeApproveChatters: '글을 게시하기에 앞서 회원님의 승인을 받도록 할까요?',
    noPostChattersImmediately: '아니오–  코멘트를 즉석에서 게시합니다',
    yesApproveChattersFirst: '예–  먼저 승인한 후에 게시합니다',
    yourCommentMustBeApproved: '회원님의 글은 다른 사람들에게 공개되기 전에 먼저 승인을 받아야 합니다.',
    reallyDeleteThisPost: '정말 이 게시물을 삭제할까요?',
    commentWall: 'Comment Wall',
    commentWallNComments: function(n) {
        switch(n) {
            case 1: return 'Comment Wall (글 수: 1) ';
            default: return 'Comment Wall (글 수: ‘+ n +’) ';
        }
    },
    display: '보여주기',
    from: '보낸 사람',
    show: '보기',
    rows: '줄',
    posts: '게시물'
});


dojo.evalObjPath('xg.shared.nls', true);
dojo.lang.mixin(xg.shared.nls, xg.index.i18n, {
    uploadAPhoto: '사진 업로드',
    uploadAnImage: '이미지 업로드',
    uploadAPhotoEllipsis: '사진 업로드...',
    useExistingImage: '기존 이미지 사용:',
    existingImage: '기존 이미지',
    useThemeImage: '테마 이미지 사용:',
    themeImage: '테마 이미지',
    noImage: '이미지가 없습니다',
    uploadImageFromComputer: '회원님의 컴퓨터에서 이미지 업로드하기',
    tileThisImage: '이미지를 타일로 만들기',
    done: '완료',
    currentImage: '현재 이미지',
    pickAColor: '색상 선택...',
    openColorPicker: '색상 선택기 열기',
    loading: '로딩 중...',
    ok: 'OK',
    save: '저장하기',
    cancel: '취소하기',
    saving: '저장 중...',
    addAnImage: '이미지 추가',
    bold: '굵은 체',
    italic: '이탤릭체',
    underline: '밑줄',
    strikethrough: '취소선 긋기',
    addHyperink: '하이퍼링크 추가',
    options: '옵션',
    wrapTextAroundImage: '이미지를 글 중심에 배치할까요?',
    imageOnLeft: '이미지를 왼쪽에 배치할까요?',
    imageOnRight: '이미지를 오른쪽에 배치할까요?',
    createThumbnail: '섬네일을 생성할까요?',
    pixels: '픽셀',
    createSmallerVersion: '게시용으로 회원님의 이미지를 조그만 버젼으로 만들기.  가로는 몇 픽셀로 할지 설정하세요.',
    popupWindow: '팝업 창을 사용할까요?',
    linkToFullSize: '이미지의 풀 사이즈 버전을 팝업 창에 링크하세요.',
    add: '추가',
    keepWindowOpen: '업로드가 진행되는 동안 이 브라우저 창을 계속해서 열어 놓으세요.',
    cancelUpload: '업로드 취소',
    pleaseSelectAFile: '이미지 파일을 선택하세요',
    pleaseSpecifyAThumbnailSize: '섬네일 크기를 지정하세요',
    thumbnailSizeMustBeNumber: '섬네일 크기는 숫자로만 지정됩니다.',
    addExistingImage: '또는 기존 이미지를 삽입하세요',
    clickToEdit: '편집하시려면 클릭해 주세요.',
    sendingFriendRequest: '친구 만들기 요청 전송 중',
    requestSent: '요청 전송 완료!',
    pleaseCorrectErrors: '이 오류 사항들을 수정하세요',
    tagThis: '태그하기',
    addOrEditYourTags: '회원님의 태그 추가 또는 편집:',
    addYourRating: '회원님의 등급 추가:',
    separateMultipleTagsWithCommas: '복수 태그는 쉼표로 분리하세요 예: cool, "new zealand"',
    saved: '저장 완료!',
    noo: '신규',
    none: '없음',
    joinNow: '지금 가입하세요',
    join: '가입하다',
    youHaventRated: '이 항목을 아직 평가하지 않았습니다.',
    yourRatedThis: function(n) {
        switch(n) {
            case 1: return '이 항목을 별 1개로 평가했습니다. ';
            default: return '이 항목을 별 ' + n + ' 개로 평가했습니다. ';
        }
    },
    yourRatingHasBeenAdded: '회원님의 평가가 추가됐습니다.',
    thereWasAnErrorRating: '이 콘텐츠를 평가하는 과정에서 오류가 발생했습니다.',
    yourTagsHaveBeenAdded: '회원님의 태그가 추가됐습니다.',
    thereWasAnErrorTagging: '태그를 추가하는 과정에서 오류가 발생했습니다.',
    addToFavorites: '즐겨찾기에 추가',
    removeFromFavorites: '즐겨찾기에서 제거',
    nStarsOutOfM: function(n,m) {
        switch(n) {
            case 1: return '별점 1점/ ' + m;
            default: return n + ' X점/ ' + m;
        }
    },
    follow: '따라가기',
    stopFollowing: '따라가기 중단',
    pendingPromptTitle: '회원 가입 신청 – 승인 대기 중',
    youCanDoThis: '운영자들이 회원님의 가입 신청을 승인하는 즉시 이 작업을 실행할 수 있습니다.',
    pleaseEnterAComment: '의견(코멘트)을 입력하시기 바랍니다',
    pleaseEnterAFileAddress: '파일 주소를 입력하시기 바랍니다',
    pleaseEnterAWebsite: '웹사이트 주소를 입력하시기 바랍니다'
});


dojo.evalObjPath('xg.video.nls', true);
dojo.lang.mixin(xg.video.nls, xg.index.i18n, {
    edit: '편집',
    display: '보여주기',
    detail: '세부 내용',
    player: '플레이어',
    from: '보낸 사람',
    show: '보기',
    videos: '동영상',
    cancel: '취소하기',
    save: '저장하기',
    saving: '저장 중...',
    deleteThisVideo: '이 동영상을 삭제할까요?',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return '문자의 수 (' + n + ')가 최대 한도 (' + maximum + ')자를 초과합니다 '; },
    weCouldNotLookUpAddress: function(address) { return '죄송합니다, 이 "' + address + '" 주소를 찾아 볼 수 없었습니다. '; },
    approve: '승인',
    approving: '승인 중...',
    keepWindowOpenWhileApproving: '동영상이 승인을 기다리는 동안 이 브라우저 창을 계속해서 열어 놓으세요.  몇 분 정도 소요될 수 있습니다.',
    'delete': '삭제하기',
    deleting: '삭제 중...',
    keepWindowOpenWhileDeleting: '동영상이 삭제되는 동안 이 브라우저 창을 계속해서 열어 놓으세요.  몇 분 정도 소요될 수 있습니다.',
    pasteInEmbedCode: '다른 사이트에 있는 동영상의 임베드 코드를 붙여 넣으세요.',
    pleaseSelectVideoToUpload: '업로드할 동영상을 선택하세요.',
    embedCodeContainsMoreThanOneVideo: '임베드 코드는 한 개 이상의 동영상을 포함하고 있습니다.  반드시 한 개의 <object> 그리고/또는 <embed> 태그만 포함하는지 확인하세요.',
    embedCodeMissingTag: '임베드 코드에 &lt; embed&gt;  또는 &lt; object&gt;  태그가 없습니다.',
    fileIsNotAMov: '이 파일은 . mov, . mpg, . mp4, . avi, . 3gp 또는 . wmv가 아닌 것 같습니다.  그대로 업로드를 시도할까요?',
    pleaseEnterAComment: '코멘트를 입력하세요.',
    youRatedVideoNStars: function(n) {
        switch(n) {
            case 1: return '이 항목을 별 1개로 평가했습니다! ';
            default: return '이 동영상을 별 ' + n + '개로 평가했습니다! ';
        }
    },
    deleteThisComment: '이 코멘트를 삭제할까요?',
    embedHTMLCode: 'HTML 임베드 코드:',
    copyHTMLCode: 'HTML 코드 복사'
});


dojo.evalObjPath('xg.uploader.nls', true);
dojo.lang.mixin(xg.uploader.nls, xg.index.i18n, {
    fileBrowserHeader: '내 컴퓨터',
    fileRoot: '내 컴퓨터',
    fileInformationHeader: '정보',
    uploadHeader: '업로드할 파일',
    dragOutInstructions: '파일을 밖으로 드래그(끌어오기)하여 제거합니다',
    dragInInstructions: '파일을 이곳으로 드래그(끌어오기)합니다',
    selectInstructions: '파일을 선택합니다',
    files: '파일',
    totalSize: '파일 총 용량',
    fileName: '이름',
    fileSize: '용량',
    nextButton: '다음 >',
    okayButton: '확인',
    yesButton: '예',
    noButton: '아니오',
    uploadButton: '업로드',
    cancelButton: '취소하다',
    backButton: '뒤로',
    continueButton: '계속',
    uploadingLabel: '업로드 중…',
    uploadingStatus: function(n, m) { return '' + n + ' 파일 업로드 중: 총 ' + m; },
    uploadingInstructions: '업로드가 진행되는 동안  이 창을 계속해서 열어 두시기 바랍니다',
    uploadLimitWarning: function(n) { return '한 번에 최대 ' + n + '개의 파일을 업로드할 수 있습니다. '; },
    uploadLimitCountdown: function(n) { switch(n) {
            case 0: return '파일을 최대한도까지 추가하셨습니다. ';
            case 1: return '파일 1개를 더 업로드할 수 있습니다. ';
            default: return '파일 ' + n + '개를 더 업로드할 수 있습니다. ';
        }
    },
    iHaveTheRight: '본인은 <a href="/main/authorization/termsOfService">이용약관</a>에 의거해 이 파일을 업로드할 권한을 보유하고 있습니다.',
    updateJavaTitle: '자바 업데이트',
    updateJavaDescription: '‘대량 업로드’ 툴을 이용하려면, 자바 버전을 업데이트해야 합니다. ‘오케이’ 버튼을 클릭해 자바 버전을 업데이트하십시오.',
    batchEditorLabel: '모든 항목에 대한 정보를 편집합니다',
    applyThisInfo: '아래의 파일에 이 정보를 적용합니다',
    titleProperty: '제목',
    descriptionProperty: '설명',
    tagsProperty: '태그',
    viewableByProperty: '다음 사람들에게 보기를 허용합니까',
    viewableByEveryone: '모두',
    viewableByFriends: '본인의 친구들에 한해',
    viewableByMe: '본인에 한해',
    albumProperty: '앨범',
    artistProperty: '아티스트',
    enableDownloadLinkProperty: '다운로드 링크 활성화시킵니다',
    enableProfileUsageProperty: '다른 사람들이 자신들의 페이지에 이 노래를 올릴 수 있도록 허용합니다',
    licenseProperty: '라이선스',
    creativeCommonsVersion: '3.0',
    selectLicense: '- 라이선스 선택-',
    copyright: '© All Rights Reserved(모든 저작권을 보유 중임)',
    ccByX: function(n) { return 'Creative Commons 공유 저작권 표시 ' + n; },
    ccBySaX: function(n) { return 'Creative Commons 공유 저작권 표시 동일조건변경허락 ' + n; },
    ccByNdX: function(n) { return 'Creative Commons 공유 저작권 표시 변경금지 ' + n; },
    ccByNcX: function(n) { return 'Creative Commons 공유 저작권 표시 비영리 ' + n; },
    ccByNcSaX: function(n) { return 'Creative Commons 공유 저작권 표시 비영리 및 동일조건변경허락 ' + n; },
    ccByNcNdX: function(n) { return 'Creative Commons 공유 저작권 표시 비영리 및 변경금지 ' + n; },
    publicDomain: '공공 도메인',
    other: '기타',
    errorUnexpectedTitle: '앗!',
    errorUnexpectedDescription: '오류가 발생했습니다. 다시 시도해 보시기 바랍니다.',
    errorTooManyTitle: '항목이 너무 많습니다',
    errorTooManyDescription: function(n) { return '죄송합니다. 한 번에 최대 ' + n + '개의 항목을 업로드만 가능합니다. '; },
    errorNotAMemberTitle: '허용되지 않음',
    errorNotAMemberDescription: '죄송합니다. 회원으로 가입하셔야만 업로드 기능을 사용할 수 있습니다.',
    errorContentTypeNotAllowedTitle: '허용되지 않음',
    errorContentTypeNotAllowedDescription: '죄송합니다. 이러한 유형의 컨텐츠는 업로드가 허용되지 않습니다.',
    errorUnsupportedFormatTitle: '앗!',
    errorUnsupportedFormatDescription: '죄송합니다. 이런 형식의 파일을 지원하지 않습니다.',
    errorUnsupportedFileTitle: '앗!',
    errorUnsupportedFileDescription: 'foo.exe는 지원되지 않는 포맷입니다.',
    errorUploadUnexpectedTitle: '앗!',
    cancelUploadTitle: '업로드를 취소하시겠습니까?',
    cancelUploadDescription: '남은 업로드를 확실히 취소하시겠습니까?',
    uploadSuccessfulTitle: '업로드 완료함',
    uploadSuccessfulDescription: '회원님의 업로드 파일로 이동하는 동안 기다려 주시기 바랍니다……',
    uploadPendingDescription: '회원님의 파일은 성공적으로 업로드되었으며 운영자의 게시 승인을 받기 위해 대기 중입니다.',
    photosUploadHeader: '업로드할 사진',
    photosDragOutInstructions: '사진을 밖으로 드래그(끌어오기)하여 제거합니다',
    photosDragInInstructions: '사진을 이곳으로 드래그(끌어오기)합니다',
    photosSelectInstructions: '사진을 선택합니다',
    photosFiles: '사진',
    photosUploadingStatus: function(n, m) { return '' + n + ' 장의 사진 업로드 중: 총 ' + m; },
    photosErrorTooManyTitle: '사진이 너무 많습니다',
    photosErrorTooManyDescription: function(n) { return '죄송합니다. 한 번에 최대 ' + n + '개의 비디오를 업로드만 가능합니다. '; },
    photosErrorContentTypeNotAllowedDescription: '죄송합니다. 사진 업로드 기능을 사용할 수 없습니다.',
    photosErrorUnsupportedFormatDescription: '죄송합니다. .jpg, .gif 또는 .png 형식의 이미지 업로드만 가능합니다.',
    photosErrorUnsupportedFileDescription: function(n) { return n + '-.jpg, .gif 또는 .png 형식의 파일이 아닙니다.'; },
    photosBatchEditorLabel: '모든 사진 정보 편집합니다',
    photosApplyThisInfo: '다음의 비디오에 이 정보를 적용합니다',
    photosUploadSuccessfulDescription: '회원님의 사진으로 이동하는 동안 기다려 주시기 바랍니다…...',
    photosUploadPendingDescription: '회원님의 사진은 성공적으로 업로드되었으며 게시 승인을 받기 위해 대기 중입니다.',
    photosUploadLimitWarning: function(n) { return '한 번에 최대 ' + n + '장의 사진을 업로드할 수 있습니다. '; },
    photosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return '사진을 최대 한도까지 추가하셨습니다. ';
            case 1: return '사진 1장을 더 업로드할 수 있습니다. ';
            default: return '사진 ' + n + '장을 더 업로드할 수 있습니다. ';
        }
    },
    photosIHaveTheRight: '본인은 <a href="/main/authorization/termsOfService">이용약관</a>에 의거해 이 사진을 업로드할 권한을리를 보소유하고 있습니다.',
    videosUploadHeader: '업로드할 비디오',
    videosDragInInstructions: '비디오를 이곳으로 드래그(끌어오기)합니다',
    videosDragOutInstructions: '비디오를 밖으로 드래그(끌어오기)하여 제거합니다',
    videosSelectInstructions: '비디오를 선택합니다',
    videosFiles: '비디오를',
    videosUploadingStatus: function(n, m) { return '' + n + ' 비디오를 업로드 중: 총 ' + m; },
    videosErrorTooManyTitle: '비디오가 너무 많습니다',
    videosErrorTooManyDescription: function(n) { return '죄송합니다. 한 번에 최대 ' + n + '개의 비디오를 업로드만 가능합니다. '; },
    videosErrorContentTypeNotAllowedDescription: '죄송합니다. 비디오 업로드 기능을 사용할 수 없습니다.',
    videosErrorUnsupportedFormatDescription: '죄송합니다. .avi, .mov, .mp4, .wmv 또는 .mpg 형식의 비디오의 업로드만 가능합니다.',
    videosErrorUnsupportedFileDescription: function(x) { return x + '.avi, .mov, .mp4, .wmv 또는 .mpg 형식의 파일이 아닙니다.'; },
    videosBatchEditorLabel: '모든 비디오에 대한 정보를 편집합니다',
    videosApplyThisInfo: '다음의 비디오에 이 정보를 적용합니다',
    videosUploadSuccessfulDescription: '회원님의 비디오로 이동하는 동안 기다려 주시기 바랍니다......',
    videosUploadPendingDescription: '회원님의 비디오는 성공적으로 업로드되었으며 운영자의 게시 승인을 받기 위해 대기 중입니다.',
    videosUploadLimitWarning: function(n) { return '한 번에 최대 ' + n + '개의 비디오를 업로드할 수 있습니다. '; },
    videosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return '비디오를 최대한도까지 추가하셨습니다. ';
            case 1: return '비디오 1개를 더 업로드할 수 있습니다. ';
            default: return '비디오 ' + n + '개를 더 업로드할 수 있습니다. ';
        }
    },
    videosIHaveTheRight: '본인은 <a href="/main/authorization/termsOfService">이용약관</a>에 의거해 이 비디오를 업로드할 권한을 보유하고 있습니다.',
    musicUploadHeader: '업로드할 노래',
    musicTitleProperty: '노래 제목',
    musicDragOutInstructions: '노래를 밖으로 드래그(끌어오기)하여 제거합니다',
    musicDragInInstructions: '노래를 이곳으로 드래그(끌어오기)합니다',
    musicSelectInstructions: '노래를 선택합니다',
    musicFiles: '노래',
    musicUploadingStatus: function(n, m) { return '' + n + ' 곡 업로드 중 ' + m; },
    musicErrorTooManyTitle: '노래가 너무 많습니다',
    musicErrorTooManyDescription: function(n) { return '죄송합니다. 한 번에 최대 ' + n + '곡을 업로드할 수 있습니다. '; },
    musicErrorContentTypeNotAllowedDescription: '죄송합니다. 노래 업로드 기능을 사용할 수 없습니다.',
    musicErrorUnsupportedFormatDescription: '죄송합니다. .mp3 형식의 곡만 업로드할 수 있습니다.',
    musicErrorUnsupportedFileDescription: function(x) { return x + '.mp3 형식의 파일이 아닙니다.'; },
    musicBatchEditorLabel: '모든 음악에 대한 정보를 편집합니다',
    musicApplyThisInfo: '아래의 곡에 이 정보를 적용합니다',
    musicUploadSuccessfulDescription: '회원님의 노래로 이동하는 동안 기다려 주시기 바랍니다……',
    musicUploadPendingDescription: '회원님의 노래는 성공적으로 업로드되었으며 운영자의 게시 승인을 받기 위해 대기 중입니다.',
    musicUploadLimitWarning: function(n) { return '동시에 최대 ' + n + '곡의 노래를 업로드할 수 있습니다. '; },
    musicUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return '노래를 최대한도까지 추가하셨습니다. ';
            case 1: return '노래 1곡을 더 업로드할 수 있습니다. ';
            default: return '노래 ' + n + '곡을 더 업로드할 수 있습니다. ';
        }
    },
    musicIHaveTheRight: '본인은 <a href="/main/authorization/termsOfService">이용약관</a>에 의거해 이 노래들을 업로드할 권한을 보유합니다.',
    errorUploadUnexpectedDescription: function(file) {  return file  ? ('' + file + ' 파일에 문제가 있는 것 같습니다. 남은 파일을 업로드하기 전에 목록에서 이 파일을 제거하시기 바랍니다.')  : '목록 최상단의 파일에 문제가 있어 보입니다. 남은 파일을 업로드하기 전에 목록에서 이 파일을 제거하시기 바랍니다.';  },
    photosErrorUploadUnexpectedDescription: function(file) {  return file  ? (''  + file + ' 파일에 문제가 있어 보입니다. 남은 사진을 업로드하기 전에 목록에서 이 파일을 제거하시기 바랍니다.')  : '목록 최상단의 사진에 문제가 있어 보입니다. 남은 사진을 업로드하기 전에 목록에서 이 파일을 제거하시기 바랍니다.';  },
    videosErrorUploadUnexpectedDescription: function(file) { return file ? ('' + file + ' 파일에 문제가 있어 보입니다. 남은 비디오를 업로드하기에 앞서 이 파일을 제거하시기 바랍니다.') : '목록 최상단의 비디오에 문제가 있어 보입니다. 남은 비디오를 업로드하기 전에 이 비디오를 제거하시기 바랍니다.'; },
    musicErrorUploadUnexpectedDescription: function(file) { return file ? ('' + file + ' 파일에 문제가 있어 보입니다. 남은 곡을 업로드하기 전에 목록에서 이 파일을 제거하시기 바랍니다.') : '목록 최상단의 곡에 문제가 있어 보입니다. 남은 곡을 업로드하기 전에 목록에서 이 파일을 제거하시기 바랍니다.';}
});