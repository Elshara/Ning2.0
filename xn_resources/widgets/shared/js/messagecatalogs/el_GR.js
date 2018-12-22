dojo.provide('xg.shared.messagecatalogs.el_GR');

dojo.require('xg.index.i18n');

/**
 * Texts for the Greek
 */
// Use UTF-8 byte sequences instead of HTML entities, e.g., & instead of &amp;, â€¦ instead of &hellip;  [Jon Aquino 2007-01-10]

dojo.evalObjPath('xg.feed.nls', true);
dojo.lang.mixin(xg.feed.nls, xg.index.i18n, {
    edit: 'Επεξεργασία',
    title: 'Τίτλος:',
    feedUrl: 'Διεύθυνση URL:',
    show: 'Εμφάνιση:',
    titles: 'Μόνο τίτλοι',
    titlesAndDescriptions: 'Προβολή λεπτομερειών',
    display: 'Εμφάνιση',
    cancel: 'Άκυρο',
    save: 'Αποθήκευση',
    loading: 'Γίνεται φόρτωση...',
    items: 'στοιχεία'
});


dojo.evalObjPath('xg.gadgets.nls', true);
dojo.lang.mixin(xg.gadgets.nls, xg.index.i18n, {
    edit: 'Επεξεργασία',
    title: 'Τίτλος:',
    feedUrl: 'Διεύθυνση URL:',
    cancel: 'Άκυρο',
    save: 'Αποθήκευση',
    loading: 'Γίνεται φόρτωση…',
    removeGadget: 'Κατάργηση μικροεφαρμογής',
    findGadgetsInDirectory: 'Εύρεση μικροεφαρμογών στον κατάλογο μικροεφαρμογών'
});


dojo.evalObjPath('xg.uploader.nls', true);
dojo.lang.mixin(xg.uploader.nls, xg.index.i18n, {
    fileBrowserHeader: 'Ο υπολογιστής μου',
    fileRoot: 'Ο υπολογιστής μου',
    fileInformationHeader: 'Πληροφορίες',
    uploadHeader: 'Αρχεία για αποστολή',
    dragOutInstructions: 'Μεταφορά αρχείων έξω για να τα καταργήσετε',
    dragInInstructions: 'Μεταφορά των αρχείων εδώ',
    selectInstructions: 'Επιλογή αρχείου',
    files: 'Αρχεία',
    totalSize: 'Συνολικό μέγεθος',
    fileName: 'Όνομα',
    fileSize: 'Μέγεθος',
    nextButton: 'Επόμενο >',
    okayButton: 'ΟΚ',
    yesButton: 'Ναι',
    noButton: 'Όχι',
    uploadButton: 'Αποστολή',
    cancelButton: 'Άκυρο',
    backButton: 'Πίσω',
    continueButton: 'Συνέχεια',
    uploadingLabel: 'Γίνεται αποστολή…',
    uploadingStatus: function(n, m) { return 'Αποστολή ' + n + ' από ' + m; },
    uploadingInstructions: 'Παρακαλώ αφήστε αυτό το παράθυρο ανοικτό όσο βρίσκεται σε εξέλιξη η διαδικασία αποστολής.',
    uploadLimitWarning: function(n) { return 'Μπορείτε να αποστείλετε ' + n + ' αρχεία κάθε φορά. '; },
    uploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Έχετε προσθέσει το μέγιστο αριθμό αρχείων. ';
            case 1: return 'Μπορείτε να αποστείλετε 1 ακόμα αρχείο. ';
            default: return 'Μπορείτε να αποστείλετε ' + n + ' ακόμα αρχεία. ';
        }
    },
    iHaveTheRight: 'Έχω το δικαίωμα να αποστείλω αυτά τα αρχεία σύμφωνα με τους <ahref="/main/authorization/termsOfService">Όρους χρήσης</a>',
    updateJavaTitle: 'Αναβάθμιση της Java',
    updateJavaDescription: 'Το πρόγραμμα μαζικής αποστολής απαιτεί μια πιο πρόσφατη έκδοση της Java.  Κάντε κλικ στο "OK" για τη λήψη Java.',
    batchEditorLabel: 'Επεξεργασία πληροφοριών για όλα τα στοιχεία',
    applyThisInfo: 'Εφαρμογή αυτών των πληροφοριών στα παρακάτω αρχεία',
    titleProperty: 'Τίτλος',
    descriptionProperty: 'Περιγραφή',
    tagsProperty: 'Ετικέτες',
    viewableByProperty: 'Μπορεί να προβληθεί από',
    viewableByEveryone: 'Οποιοσδήποτε',
    viewableByFriends: 'Μόνο οι φίλοι μου',
    viewableByMe: 'Μόνο εγώ',
    albumProperty: 'Άλμπουμ',
    artistProperty: 'Καλλιτέχνης',
    enableDownloadLinkProperty: 'Ενεργοποίηση σύνδεσης λήψης',
    enableProfileUsageProperty: 'Επιτρέψτε σε άλλους να τοποθετούν αυτό το τραγούδι στις σελίδες τους',
    licenseProperty: 'Άδεια χρήσης',
    creativeCommonsVersion: '3.0',
    selectLicense: '- Επιλογή άδειας χρήσης -',
    copyright: '© Με επιφύλαξη παντός δικαιώματος',
    ccByX: function(n) { return 'Creative Commons Attribution (Άδεια που επιτρέπει την διανομή και τροποποίηση, εμπορική και μη αρκεί να αναφέρετε ο αρχικός δημιουργός) ' + n; },
    ccBySaX: function(n) { return 'Creative Commons Attribution Share Alike (Άδεια που επιτρέπει τη διανομή και τροποποίηση, εμπορική και μη αρκεί να αναφέρετε ο αρχικός δημιουργός και να γίνεται η διανομή του τελικού προϊόντος με τους ίδιους όρους) ' + n; },
    ccByNdX: function(n) { return 'Creative Commons Attribution No Derivatives (Άδεια που επιτρέπει την αναδιανομή, εμπορική και μη, με την προϋπόθεση ότι δεν γίνονται αλλαγές ή περικοπές και αναφέρεται το όνομα του δημιουργού στο τελικό προϊόν) ' + n; },
    ccByNcX: function(n) { return 'Creative Commons Attribution Non-commercial (Άδεια που επιτρέπει την αναδιανομή και τροποποίηση για μη εμπορικούς σκοπούς με την προϋπόθεση ότι αναφέρεται το όνομα του δημιουργού στο τελικό προϊόν) ' + n; },
    ccByNcSaX: function(n) { return 'Creative Commons Attribution Non-commercial Share Alike (Άδεια που επιτρέπει την αναδιανομή και τροποποίηση για μη εμπορικούς σκοπούς με την προϋπόθεση ότι αναφέρεται το όνομα του δημιουργού στο τελικό προϊόν και να διατεθεί με ακριβώς τον ίδιο τύπο άδειας) ' + n; },
    ccByNcNdX: function(n) { return 'Creative Commons Attribution Non-commercial No Derivatives (Άδεια που επιτρέπει μόνο την αναδιανομή, με την προϋπόθεση ότι δεν γίνονται αλλαγές ή περικοπές και εμπορική χρήση και αναφέρεται το όνομα του δημιουργού στο τελικό προϊόν) ' + n; },
    publicDomain: 'Δημόσιος τομέας',
    other: 'Άλλο',
    errorUnexpectedTitle: 'Λυπούμαστε!',
    errorUnexpectedDescription: 'Υπάρχει λάθος. Προσπαθήστε πάλι.',
    errorTooManyTitle: 'Υπερβολικά πολλά στοιχεία δεδομένων',
    errorTooManyDescription: function(n) { return 'Λυπούμαστε, αλλά μπορείτε μόνο να αποστείλετε ' + n + ' στοιχεία δεδομένων κάθε φορά. '; },
    errorNotAMemberTitle: 'Δεν επιτρέπεται',
    errorNotAMemberDescription: 'Λυπούμαστε, αλλά πρέπει να είστε μέλος για να αποστείλετε.',
    errorContentTypeNotAllowedTitle: 'Δεν επιτρέπεται',
    errorContentTypeNotAllowedDescription: 'Λυπούμαστε, αλλά δεν σας επιτρέπεται να αποστείλετε αυτόν τον τύπο περιεχομένου.',
    errorUnsupportedFormatTitle: 'Oops!',
    errorUnsupportedFormatDescription: 'Λυπούμαστε, αλλά δεν υποστηρίζουμε αυτόν τον τύπο αρχείου.',
    errorUnsupportedFileTitle: 'Oops!',
    errorUnsupportedFileDescription: 'Το foo.exe είναι μορφή που δεν υποστηρίζεται.',
    errorUploadUnexpectedTitle: 'Oops!',
    errorUploadUnexpectedDescription: function(file) {
		return file ?
			('Φαίνεται να υπάρχει πρόβλημα με το αρχείο ' + file + '. Παρακαλούμε καταργήστε το από τη λίστα πριν αποστείλετε τα υπόλοιπα αρχεία σας.') :
			'Φαίνεται να υπάρχει πρόβλημα με το αρχείο στην κορυφή της λίστας. Παρακαλούμε καταργήστε το πριν αποστείλετε τα υπόλοιπα αρχεία σας.';
	},
    cancelUploadTitle: 'Να ακυρωθεί η αποστολή;',
    cancelUploadDescription: 'Είστε βέβαιοι ότι θέλετε να ακυρώστε τις υπόλοιπες αποστολές;',
    uploadSuccessfulTitle: 'Ολοκλήρωση αποστολής',
    uploadSuccessfulDescription: 'Παρακαλούμε περιμένετε μέχρι να μεταβούμε στις αποστολές…',
    uploadPendingDescription: 'Τα αρχεία σας αποστάλθηκαν με επιτυχία και αναμένεται η έγκρισή τους.',
    photosUploadHeader: 'Φωτογραφίες για αποστολή',
    photosDragOutInstructions: 'Σύρετε τις φωτογραφίες για να τις καταργήσετε.',
    photosDragInInstructions: 'Σύρετε εδώ τις φωτογραφίες σας',
    photosSelectInstructions: 'Επιλέξτε μια φωτογραφία',
    photosFiles: 'Φωτογραφίες',
    photosUploadingStatus: function(n, m) { return 'Αποστολή φωτογραφίας ' + n + ' του ' + m; },
    photosErrorTooManyTitle: 'Υπερβολικά πολλές φωτογραφίες',
    photosErrorTooManyDescription: function(n) { return 'Λυπούμαστε, αλλά μπορείτε μόνο να αποστείλετε ' + n + ' φωτογραφίες κάθε φορά. '; },
    photosErrorContentTypeNotAllowedDescription: 'Λυπούμαστε, αλλά η αποστολή φωτογραφιών έχει απενεργοποιηθεί.',
    photosErrorUnsupportedFormatDescription: 'Λυπούμαστε, αλλά μπορείτε να αποστείλετε μόνο εικόνες των μορφών .jpg, .gif ή .png.',
    photosErrorUnsupportedFileDescription: function(n) { return n + ' δεν είναι αρχείο .jpg, .gif ή .png.' ; },
    photosBatchEditorLabel: 'Επεξεργασία πληροφοριών για όλες τις φωτογραφίες',
    photosApplyThisInfo: 'Εφαρμογή αυτών των πληροφοριών στις παρακάτω φωτογραφίες',
    photosErrorUploadUnexpectedDescription: function(file) { return file ? ('There appears to be a problem with the ' + file + ' file. Please remove it from the list before uploading the rest of your photos.') : 'There appears to be a problem with the photo at the top of the list. Please remove it before uploading the rest of your photos.'; },
    photosUploadSuccessfulDescription: 'Παρακαλούμε περιμένετε μέχρι να μεταβούμε στις φωτογραφίες σας…',
    photosUploadPendingDescription: 'Οι φωτογραφίες σας αποστάλθηκαν με επιτυχία και αναμένεται η έγκρισή τους.',
    photosUploadLimitWarning: function(n) { return 'Μπορείτε να αποστείλετε ‘ + n + ‘ φωτογραφίες κάθε φορά. '; },
    photosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Έχετε προσθέσει το μέγιστο αριθμό φωτογραφιών. ';
            case 1: return 'Μπορείτε να αποστείλετε 1 ακόμα φωτογραφία. ';
            default: return 'Μπορείτε να αποστείλετε ' + n + ' ακόμα φωτογραφίες. ';
        }
    },
    photosIHaveTheRight: 'Έχω το δικαίωμα να αποστείλω αυτές τις φωτογραφίες σύμφωνα με τους <a href="/main/authorization/termsOfService">Όρους χρήσης</a>',
    videosUploadHeader: 'Βίντεο για αποστολή',
    videosDragInInstructions: 'Σύρετε τα βίντεό σας εδώ',
    videosDragOutInstructions: 'Σύρετε τα βίντεό σας έξω για να τα καταργήσετε',
    videosSelectInstructions: 'Επιλέξτε ένα βίντεο',
    videosFiles: 'Βίντεο',
    videosUploadingStatus: function(n, m) { return 'Αποστολή βίντεο ' + n + ' του ' + m; },
    videosErrorTooManyTitle: 'Υπερβολικά πολλά βίντεο',
    videosErrorTooManyDescription: function(n) { return 'Λυπούμαστε, αλλά μπορείτε να αποστείλετε μόνο ' + n + ' βίντεο κάθε φορά. '; },
    videosErrorContentTypeNotAllowedDescription: 'Λυπούμαστε, αλλά η αποστολή βίντεο έχει απενεργοποιηθεί.',
    videosErrorUnsupportedFormatDescription: 'Λυπούμαστε, αλλά μπορείτε να αποστείλετε μόνο βίντεο της μορφής .avi, .mov, .mp4, .wmv ή .mpg.',
    videosErrorUnsupportedFileDescription: function(x) { return x + ' δεν είναι αρχείο .avi, .mov, .mp4, .wmv ή .mpg.'; },
    videosBatchEditorLabel: 'Επεξεργασία πληροφοριών για όλα τα βίντεο',
    videosApplyThisInfo: 'Εφαρμογή αυτών των πληροφοριών στα παρακάτω βίντεο',
    videosErrorUploadUnexpectedDescription: function(file) { return file ? ('There appears to be a problem with the ' + file + ' file. Please remove it from the list before uploading the rest of your videos.') : 'There appears to be a problem with the video at the top of the list. Please remove it before uploading the rest of your videos.'; },
    videosUploadSuccessfulDescription: 'Παρακαλούμε περιμένετε μέχρι να μεταβούμε στα βίντεό σας…',
    videosUploadPendingDescription: 'Τα βίντεό σας αποστάλθηκαν με επιτυχία και αναμένεται η έγκρισή τους.',
    videosUploadLimitWarning: function(n) { return 'Μπορείτε να αποστείλετε ' + n + ' βίντεο κάθε φορά. '; },
    videosUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Έχετε προσθέσει το μέγιστο αριθμό βίντεο. ';
            case 1: return 'Μπορείτε να αποστείλετε 1 ακόμα βίντεο. ';
            default: return 'Μπορείτε να αποστείλετε ' + n + ' ακόμα βίντεο. ';
        }
    },
    videosIHaveTheRight: 'Έχω το δικαίωμα να αποστείλω αυτά τα βίντεο σύμφωνα με τους <a href="/main/authorization/termsOfService">Όρους χρήσης</a>',
    musicUploadHeader: 'Τραγούδια για αποστολή',
    musicTitleProperty: 'Τίτλος τραγουδιού',
    musicDragOutInstructions: 'Σύρετε τα τραγούδια σας έξω για να τα καταργήσετε',
    musicDragInInstructions: 'Σύρετε τα τραγούδια σας εδώ',
    musicSelectInstructions: 'Επιλέξετε ένα τραγούδι',
    musicFiles: 'Τραγούδια',
    musicUploadingStatus: function(n, m) { return 'Αποστολή τραγουδιού ' + n + ' του ' + m; },
    musicErrorTooManyTitle: 'Υπερβολικά πολλά τραγούδια',
    musicErrorTooManyDescription: function(n) { return 'Λυπούμαστε, αλλά μπορείτε να αποστείλετε μόνο ' + n + ' τραγούδια κάθε φορά. '; },
    musicErrorContentTypeNotAllowedDescription: 'Λυπούμαστε, αλλά η αποστολή τραγουδιών έχει απενεργοποιηθεί.',
    musicErrorUnsupportedFormatDescription: 'Λυπούμαστε, αλλά μπορείτε να αποστείλετε μόνο τραγούδια της μορφής .mp3',
    musicErrorUnsupportedFileDescription: function(x) { return x +  ' δεν είναι αρχείο .mp3.'; },
    musicBatchEditorLabel: 'Επεξεργασία πληροφοριών για όλα τα τραγούδια',
    musicApplyThisInfo: 'Εφαρμογή αυτών των πληροφοριών στα παρακάτω τραγούδια',
    musicErrorUploadUnexpectedDescription: function(file) { return file ? ('There appears to be a problem with the ' + file + ' file. Please remove it from the list before uploading the rest of your songs.') : 'There appears to be a problem with the song at the top of the list. Please remove it before uploading the rest of your songs.'; },
    musicUploadSuccessfulDescription: 'Παρακαλούμε περιμένετε μέχρι να μεταβούμε στα τραγούδια σας…',
    musicUploadPendingDescription: 'Τα τραγούδια σας αποστάλθηκαν με επιτυχία και αναμένεται η έγκρισή τους.',
    musicUploadLimitWarning: function(n) { return 'Μπορείτε να αποστείλετε μόνο ' + n + ' τραγούδια κάθε φορά. '; },
    musicUploadLimitCountdown: function(n) {
        switch(n) {
            case 0: return 'Έχετε προσθέσει το μέγιστο αριθμό τραγουδιών. ';
            case 1: return 'Μπορείτε να αποστείλετε 1 ακόμα τραγούδι. ';
            default: return 'Μπορείτε να αποστείλετε ' + n + ' ακόμα τραγούδια. ';
        }
    },
    musicIHaveTheRight: 'Έχω το δικαίωμα να αποστείλω αυτά τα τραγούδια σύμφωνα με τους <a href="/main/authorization/termsOfService">Όρους χρήσης</a>'
});


dojo.evalObjPath('xg.forum.nls', true);
dojo.lang.mixin(xg.forum.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Ο αριθμός χαρακτήρων (' + n + ') υπερβαίνει το μέγιστο όριο (' + maximum + ') '; },
    pleaseEnterFirstPost: 'Συντάξτε την πρώτη καταχώριση για τη συζήτηση',
    pleaseEnterTitle: 'Εισάγετε έναν τίτλο για τη συζήτηση',
    save: 'Αποθήκευση',
    cancel: 'Άκυρο',
    yes: 'Ναι',
    no: 'Όχι',
    edit: 'Επεξεργασία',
    deleteCategory: 'Διαγραφή καταλόγου',
    discussionsWillBeDeleted: 'Οι συζητήσεις σε αυτή την κατηγορία θα διαγραφούν.',
    whatDoWithDiscussions: 'Τι θέλετε να κάνετε με τις συζητήσεις σε αυτή την κατηγορία;',
    moveDiscussionsTo: 'Μετακίνηση συζητήσεων σε:',
    moveToCategory: 'Μετακίνηση σε κατηγορία...',
    deleteDiscussions: 'Διαγραφή συζητήσεων',
    'delete': 'Διαγραφή',
    deleteReply: 'Διαγραφή απάντησης',
    deleteReplyQ: 'Να γίνει διαγραφή της απάντησης;',
    deletingReplies: 'Γίνεται διαγραφή απαντήσεων...',
    doYouWantToRemoveReplies: 'Θέλετε επίσης να καταργήσετε τις απαντήσεις σε αυτό το σχόλιο;',
    pleaseKeepWindowOpen: 'Κρατήστε ανοικτό αυτό το παράθυρο του προγράμματος περιήγησης όσο συνεχίζεται η επεξεργασία.  Η διαδικασία μπορεί να διαρκέσει μερικά λεπτά.',
    from: 'Από',
    show: 'Εμφάνιση',
    discussions: 'συζητήσεις',
    discussionsFromACategory: 'Συζητήσεις από μια κατηγορία...',
    display: 'Εμφάνιση',
    items: 'είδη',
    view: 'Προβολή'
});


dojo.evalObjPath('xg.groups.nls', true);
dojo.lang.mixin(xg.groups.nls, xg.index.i18n, {
    pleaseChooseAName: 'Επιλέξτε ένα όνομα για την ομάδα σας.',
    pleaseChooseAUrl: 'Επιλέξτε μια διεύθυνση Web για την ομάδα σας.',
    urlCanContainOnlyLetters: 'Η διεύθυνση Web μπορεί να περιέχει μόνο γράμματα και αριθμούς (χωρίς κενά διαστήματα).',
    descriptionTooLong: function(n, maximum) { return 'Το μήκος της περιγραφής της ομάδας σας (' + n + ') υπερβαίνει το μέγιστο όριο (' + maximum + ') '; },
    nameTaken: 'Λυπούμαστε, αλλά αυτό το όνομα χρησιμοποιείται ήδη από άλλον.  Επιλέξτε διαφορετικό όνομα.',
    urlTaken: 'Λυπούμαστε, αλλά αυτή η διεύθυνση Web χρησιμοποιείται ήδη από άλλον.  Επιλέξτε διαφορετική διεύθυνση Web.',
    whyNot: 'Γιατί όχι;',
    groupCreatorDetermines: function(href) { return 'Ο δημιουργός της ομάδας καθορίζει ποιος θα γίνει μέλος.  Εάν πιστεύετε ότι ίσως έχετε αποκλειστεί κατά λάθος, <a ' + href + '>επικοινωνήστε με το δημιουργό της ομάδας</a> '; },
    edit: 'Επεξεργασία',
    from: 'Από',
    show: 'Εμφάνιση',
    groups: 'ομάδες',
    pleaseEnterName: 'Εισάγετε το όνομά σας',
    pleaseEnterEmailAddress: 'Εισάγετε τη διεύθυνση ηλεκτρονικού ταχυδρομείου σας',
    xIsNotValidEmailAddress: function(x) { return x + ' is not a valid email address'; },
    save: 'Αποθήκευση',
    cancel: 'Άκυρο',
    addingInstructions: 'Παρακαλώ αφήστε αυτό το παράθυρο ανοικτό όσο γίνεται προσθήκη του περιεχομένου σας.',
    addingLabel: 'Γίνεται προσθήκη…',
    cannotKeepFiles: 'Θα πρέπει να επιλέξετε ξανά τα αρχεία σας εάν θέλετε να δείτε περισσότερες επιλογές.  Θέλετε να συνεχίσετε;',
    done: 'Τέλος',
    looksLikeNotImage: 'Ένα ή περισσότερα αρχεία δεν φαίνεται να έχουν μορφή .jpg, .gif ή .png.  Θέλετε παρ\' όλα αυτά να συνεχίσετε με την αποστολή;',
    looksLikeNotMusic: 'Το αρχείο που επιλέξατε δεν φαίνεται να έχει μορφή .mp3.  Θέλετε παρ\' όλα αυτά να συνεχίσετε με την αποστολή;',
    looksLikeNotVideo: 'Το αρχείο που επιλέξατε δεν φαίνεται να έχει μορφή .mov, .mpg, .mp4, .avi, .3gp ή .wmv.  Θέλετε παρ\' όλα αυτά να συνεχίσετε με την αποστολή;',
    messageIsTooLong: function(n) { return 'Το μήνυμα είναι πάρα πολύ μεγάλο. Χρησιμοποιήστε '+n+' χαρακτήρες ή λιγότερους.'; },
    pleaseSelectPhotoToUpload: 'Επιλέξτε μια φωτογραφία για αποστολή.',
    processingFailed: 'Λυπούμαστε, η επεξεργασία απέτυχε. Προσπαθήστε πάλι αργότερα.',
    selectOrPaste: 'Πρέπει να επιλέξετε ένα βίντεο ή να επικολλήσετε τον κώδικα \'embed\'',
    selectOrPasteMusic: 'Πρέπει να επιλέξετε ένα τραγούδι ή να επικολλήσετε τη διεύθυνση URL',
    sendingLabel: 'Aποστολή...',
    thereWasAProblem: 'Παρουσιάστηκε πρόβλημα με την προσθήκη του περιεχομένου σας.  Προσπαθήστε πάλι αργότερα.',
    uploadingInstructions: 'Παρακαλώ αφήστε αυτό το παράθυρο ανοικτό όσο βρίσκεται σε εξέλιξη η διαδικασία αποστολής.',
    uploadingLabel: 'Γίνεται αποστολή…',
    youNeedToAddEmailRecipient: 'Πρέπει να προσθέσετε έναν παραλήπτη ηλεκτρονικού ταχυδρομείου.',
    yourMessage: 'Το μήνυμά σας',
    yourMessageIsBeingSent: 'Το μήνυμά σας αποστέλλεται.',
    yourSubject: 'Το θέμα σας'
});


dojo.evalObjPath('xg.html.nls', true);
dojo.lang.mixin(xg.html.nls, xg.index.i18n, {
    contentsTooLong: function(maximum) { return 'Τα περιεχόμενα είναι πολύ εκτενή. Χρησιμοποιήστε λιγότερους από ‘ + maximum + ‘ χαρακτήρες. '; },
    edit: 'Επεξεργασία',
    save: 'Αποθήκευση',
    cancel: 'Άκυρο',
    saving: 'Γίνεται αποθήκευση...',
    addAWidget: function(url) { return '<a href="' + url + '">Προσθήκη γραφικού στοιχείου</a> σε αυτό το πλαίσιο κειμένου '; }
});


dojo.evalObjPath('xg.index.nls', true);
dojo.lang.mixin(xg.index.nls, xg.index.i18n, {
    showingNFriends: function(n, searchString) {
	        switch(n) {
	            case 1: return 'Εμφάνιση 1 φίλου που αντιστοιχεί στο "' + searchString + '". <a href="#">Εμφάνιση όλων</a> ';
	            default: return 'Εμφάνιση ' + n + ' φίλων που αντιστοιχούν στο "' + searchString + '". <a href="#">Εμφάνιση όλων</a> ';
	        }
	    },
    sendMessage: 'Αποστολή μηνύματος',
    sendMessageToNFriends: function(n) {
	        switch(n) {
	            case 1: return 'Να αποσταλεί μήνυμα σε 1 φίλο; ';
	            default: return 'Να αποσταλεί μήνυμα σε ' + n + ' φίλους; ';
	        }
	    },
    invitingNFriends: function(n) {
	        switch(n) {
	            case 1: return 'Γίνεται πρόσκληση σε 1 φίλο… ';
	            default: return 'Γίνεται πρόσκληση σε ' + n + ' φίλους... ';
	        }
	    },
    nFriends: function(n) {
	        switch(n) {
	            case 1: return '1 φίλος… ';
	            default: return n + ' φίλοι… ';
	        }
	    },
    sendingMessageToNFriends: function(n) {
	        switch(n) {
	            case 1: return 'Αποστολή μηνύματος σε 1 φίλο… ';
	            default: return 'Αποστολή μηνύματος σε ' + n + ' φίλους… ';
	        }
	    },
    noPeopleSelected: 'Δεν έχουν επιλεγεί άτομα',
    sorryWeDoNotSupport: 'Λυπούμαστε, δεν υποστηρίζουμε το βιβλίο διευθύνσεων web για τη διεύθυνσή σας ηλεκτρονικού ταχυδρομείου.. Προσπαθήστε να κάνετε κλικ στο \\’Εφαρμογή βιβλίου διευθύνσεων\\’ παρακάτω για να χρησιμοποιήσετε τις διευθύνσεις από τον υπολογιστή σας.',
    pleaseChooseFriends: 'Παρακαλούμε επιλέξτε μερικούς φίλους πριν στείλετε το μήνυμά σας.',
    htmlNotAllowed: 'Δεν επιτρέπεται η χρήση της HTML',
    noFriendsFound: 'Δεν βρέθηκαν φίλοι που να αντιστοιχούν στην αναζήτησή σας.',
    sendInvitation: 'Αποστολή πρόσκλησης',
    sendInvitationToNFriends: function(n) {
        switch(n) {
            case 1: return 'Αποστολή πρόσκλησης σε 1 φίλο; ';
            default: return 'Αποστολή πρόσκλησης σε ' + n + ' φίλους; ';
        }
    },
    yourMessageOptional: '<label>Το μήνυμά σας</label> (Προαιρετικό)',
    pleaseChoosePeople: 'Επιλέξτε μερικά άτομα που θα προσκαλέσετε.',
    pleaseEnterEmailAddress: 'Εισάγετε τη διεύθυνση ηλεκτρονικού ταχυδρομείου σας.',
    pleaseEnterPassword: function(emailAddress) { return 'Εισάγετε τον κωδικό πρόσβασης για τη διεύθυνση ' + emailAddress + '. '; },
    sorryWeDontSupport: 'Λυπούμαστε, δεν υποστηρίζουμε το βιβλίο διευθύνσεων Web για τη δική σας διεύθυνση ηλεκτρονικού ταχυδρομείου.  Κάντε κλικ στο \'Εφαρμογή ηλεκτρονικού ταχυδρομείου\' παρακάτω για να χρησιμοποιήσετε διευθύνσεις από τον υπολογιστή σας.',
    pleaseSelectSecondPart: 'Επιλέξτε το δεύτερο μέρος της διεύθυνσης ηλεκτρονικού ταχυδρομείου, π.χ. gmail. com.',
    atSymbolNotAllowed: 'Βεβαιωθείτε ότι το σύμβολο @ δεν υπάρχει στο πρώτο μέρος της διεύθυνσης ηλεκτρονικού ταχυδρομείου.',
    resetTextQ: 'Επαναφορά κειμένου;',
    resetTextToOriginalVersion: 'Θέλετε να γίνει επαναφορά όλου του κειμένου σας στην αρχική έκδοση;  Όλες οι αλλαγές σας θα χαθούν.',
    changeQuestionsToPublic: 'Αλλαγή ερωτήσεων σε δημόσιες;',
    changingPrivateQuestionsToPublic: 'Η αλλαγή των ιδιωτικών ερωτήσεων σε δημόσιες θα εκθέσει σε κοινή θέα όλες τις απαντήσεις των μελών.  Είστε σίγουροι;',
    youHaveUnsavedChanges: 'Έχετε αλλαγές που δεν έχουν αποθηκευτεί.',
    pleaseEnterASiteName: 'Εισάγετε ένα όνομα για το κοινωνικό δίκτυο, π.χ. Tiny Clown Club',
    pleaseEnterShorterSiteName: 'Εισάγετε ένα μικρότερο όνομα (μέχρι 64 χαρακτήρες)',
    pleaseEnterShorterSiteDescription: 'Εισάγετε μια μικρότερη περιγραφή (μέχρι 140 χαρακτήρες)',
    siteNameHasInvalidCharacters: 'Το όνομα περιέχει κάποιους χαρακτήρες που δεν είναι έγκυροι',
    thereIsAProblem: 'Υπάρχει πρόβλημα με τις πληροφορίες σας',
    thisSiteIsOnline: 'Αυτό το κοινωνικό δίκτυο είναι σε σύνδεση',
    onlineSiteCanBeViewed: '<strong>Σε σύνδεση</strong> - Το δίκτυο μπορεί να προβληθεί σε σχέση με τις δικές σας ρυθμίσεις απορρήτου.',
    takeOffline: 'Εκτός σύνδεσης',
    thisSiteIsOffline: 'Αυτό το κοινωνικό δίκτυο είναι εκτός σύνδεσης',
    offlineOnlyYouCanView: '<strong>Εκτός σύνδεσης</strong> - Μόνο εσείς μπορείτε δείτε αυτό το κοινωνικό δίκτυο.',
    takeOnline: 'Σε σύνδεση',
    themeSettings: 'Ρυθμίσεις θέματος',
    addYourOwnCss: 'Για προχωρημένους',
    error: 'Σφάλμα',
    pleaseEnterTitleForFeature: function(displayName) { return 'Εισάγετε έναν τίτλο για το χαρακτηριστικό ' + displayName + ' '; },
    thereIsAProblemWithTheInformation: 'Υπάρχει πρόβλημα με τις καταχωρημένες πληροφορίες',
    photos: 'Φωτογραφίες',
    videos: 'Βίντεο',
    pleaseEnterTheChoicesFor: function(questionTitle) { return 'Εισάγετε τις επιλογές για το "' + questionTitle + '" π.χ. Πεζοπορία, Διάβασμα, Αγορές '; },
    pleaseEnterTheChoices: 'Εισάγετε τις επιλογές π.χ. Πεζοπορία, Διάβασμα, Αγορές',
    shareWithFriends: 'Κοινή χρήση με φίλους',
    email: 'ηλεκτρονικό ταχυδρομείο',
    separateMultipleAddresses: 'Διαχωρίστε πολλαπλές διευθύνσεις με κόμμα',
    subject: 'Θέμα',
    message: 'Μήνυμα',
    send: 'Αποστολή',
    cancel: 'Άκυρο',
    pleaseEnterAValidEmail: 'Εισάγετε μια έγκυρη διεύθυνση ηλεκτρονικού ταχυδρομείου',
    go: 'Μετάβαση',
    areYouSureYouWant: 'Είστε σίγουροι ότι θέλετε να το κάνετε;',
    processing: 'Γίνεται επεξεργασία...',
    pleaseKeepWindowOpen: 'Κρατήστε ανοικτό αυτό το παράθυρο του προγράμματος περιήγησης όσο συνεχίζεται η επεξεργασία.  Η διαδικασία μπορεί να διαρκέσει μερικά λεπτά.',
    complete: 'Ολοκληρώθηκε!',
    processIsComplete: 'Η επεξεργασία ολοκληρώθηκε.',
    ok: 'OK',
    body: 'Σώμα',
    pleaseEnterASubject: 'Εισάγετε ένα θέμα',
    pleaseEnterAMessage: 'Εισάγετε ένα μήνυμα',
    thereHasBeenAnError: 'Παρουσιάστηκε σφάλμα',
    fileNotFound: 'Το αρχείο δεν βρέθηκε',
    pleaseProvideADescription: 'Δώστε μια περιγραφή',
    pleaseEnterYourFriendsAddresses: 'Εισάγετε τις διευθύνσεις των φίλων σας ή τα αναγνωριστικά Ning',
    pleaseEnterSomeFeedback: 'Εισάγετε κάποια σχόλια',
    title: 'Τίτλος:',
    setAsMainSiteFeature: 'Ρύθμιση ως κύριο χαρακτηριστικό',
    thisIsTheMainSiteFeature: 'Αυτό είναι το κύριο χαρακτηριστικό',
    customized: 'Προσαρμοσμένο',
    copyHtmlCode: 'Αντιγραφή κώδικα HTML',
    playerSize: 'Μέγεθος προγράμματος αναπαραγωγής',
    selectSource: 'Επιλέξτε προέλευση',
    myAlbums: 'Τα άλμπουμ μου',
    myMusic: 'Η μουσική μου',
    myVideos: 'Τα βίντεό μου',
    showPlaylist: 'Εμφάνιση λίστας αναπαραγ.',
    change: 'Αλλαγή',
    changing: 'Γίνεται αλλαγή...',
    changePrivacy: 'Αλλαγή απορρήτου;',
    keepWindowOpenWhileChanging: 'Κρατήστε ανοικτό αυτό το παράθυρο του προγράμματος περιήγησης όσο διαρκεί η αλλαγή στις ρυθμίσεις απορρήτου.  Αυτή η διαδικασία μπορεί να διαρκέσει μερικά λεπτά.',
    subjectIsTooLong: function(n) { return 'Το θέμα είναι πάρα πολύ μεγάλο. Χρησιμοποιήστε '+n+' χαρακτήρες ή λιγότερους.'; },
    messageIsTooLong: function(n) { return 'Το μήνυμα είναι πάρα πολύ μεγάλο. Χρησιμοποιήστε '+n+' χαρακτήρες ή λιγότερους.'; },
    processingFailed: 'Λυπούμαστε, η επεξεργασία απέτυχε. Προσπαθήστε πάλι αργότερα.',
    yourSubject: 'Το θέμα σας',
    yourMessage: 'Το μήνυμά σας'
});


dojo.evalObjPath('xg.music.nls', true);
dojo.lang.mixin(xg.music.nls, xg.index.i18n, {
    play: 'αναπαραγωγή',
    pleaseSelectTrackToUpload: 'Επιλέξτε ένα τραγούδι για αποστολή.',
    pleaseEnterTrackLink: 'Εισάγετε τη διεύθυνση URL ενός τραγουδιού.',
    thereAreUnsavedChanges: 'Υπάρχουν αλλαγές που δεν έχουν αποθηκευτεί.',
    autoplay: 'Αυτόματη αναπαραγωγή',
    showPlaylist: 'Εμφάνιση λίστας αναπαραγ.',
    playLabel: 'Αναπαραγ.',
    url: 'διεύθυνση URL',
    rssXspfOrM3u: 'rss, xspf ή m3u',
    save: 'Αποθήκευση',
    cancel: 'Άκυρο',
    edit: 'Επεξεργασία',
    shufflePlaylist: 'Τυχαία σειρά λίστας',
    fileIsNotAnMp3: 'Ένα από τα αρχεία δεν φαίνεται να είναι MP3.  Είστε βέβαιοι ότι θέλετε να γίνει αποστολή;',
    entryNotAUrl: 'Μία από τις καταχωρήσεις δεν φαίνεται να είναι URL.  Βεβαιωθείτε ότι όλες οι καταχωρήσεις αρχίζουν με <kbd>http://</kbd>'
});


dojo.evalObjPath('xg.page.nls', true);
dojo.lang.mixin(xg.page.nls, xg.index.i18n, {
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Ο αριθμός χαρακτήρων (' + n + ') υπερβαίνει το μέγιστο όριο (' + maximum + ') '; },
    pleaseEnterContent: 'Εισάγετε το περιεχόμενο σελίδας',
    pleaseEnterTitle: 'Εισάγετε έναν τίτλο για τη σελίδα',
    pleaseEnterAComment: 'Εισάγετε ένα σχόλιο',
    deleteThisComment: 'Είστε σίγουροι ότι θέλετε να διαγράψετε αυτό το σχόλιο;',
    save: 'Αποθήκευση',
    cancel: 'Άκυρο',
    discussionTitle: 'Τίτλος σελίδας:',
    tags: 'Ετικέτες;',
    edit: 'Επεξεργασία',
    close: 'Κλείσιμο',
    displayPagePosts: 'Εμφάνιση καταχωρίσεων σελίδας',
    directory: 'Κατάλογος',
    displayTab: 'Καρτέλα εμφάνισης',
    addAnotherPage: 'Προσθήκη επιπλέον σελίδας',
    tabText: 'Κείμενο καρτέλας',
    urlDirectory: 'Κατάλογος URL',
    displayTabForPage: 'Αν θα εμφανιστεί καρτέλα για τη σελίδα',
    tabTitle: 'Τίτλος καρτέλας',
    remove: 'Κατάργηση',
    thereIsAProblem: 'Υπάρχει πρόβλημα με τις πληροφορίες σας'
});


dojo.evalObjPath('xg.photo.nls', true);
dojo.lang.mixin(xg.photo.nls, xg.index.i18n, {
    randomOrder: 'Τυχαία σειρά',
    untitled: 'Χωρίς τίτλο',
    photos: 'Φωτογραφίες',
    edit: 'Επεξεργασία',
    photosFromAnAlbum: 'Άλμπουμ',
    show: 'Εμφάνιση',
    rows: 'σειρές',
    cancel: 'Άκυρο',
    save: 'Αποθήκευση',
    deleteThisPhoto: 'Να γίνει διαγραφή της φωτογραφίας;',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Ο αριθμός χαρακτήρων (' + n + ') υπερβαίνει το μέγιστο όριο (' + maximum + ') '; },
    weCouldNotLookUpAddress: function(address) { return 'Δεν μπορούμε να αναζητήσουμε τη διεύθυνση "' + address + '". '; },
    pleaseSelectPhotoToUpload: 'Επιλέξτε μια φωτογραφία για αποστολή.',
    pleaseEnterAComment: 'Εισάγετε ένα σχόλιο.',
    addToExistingAlbum: 'Προσθήκη σε υπάρχον άλμπουμ',
    addToNewAlbumTitled: 'Προσθήκη σε νέο άλμπουμ με τίτλο...',
    deleteThisComment: 'Να γίνει διαγραφή του σχολίου;',
    importingNofMPhotos: function(n,m) { return 'Εισαγωγή <span id="currentP">' + n + '</span> από ' + m + ' φωτογραφίες. '},
    starting: 'Γίνεται εκκίνηση...',
    done: 'Τέλος!',
    from: 'Από',
    display: 'Εμφάνιση',
    takingYou: 'Μετάβαση για να δείτε τις φωτογραφίες σας...',
    anErrorOccurred: 'Δυστυχώς παρουσιάστηκε σφάλμα.  Μπορείτε να αναφέρετε αυτό το ζήτημα χρησιμοποιώντας τη σύνδεση στο κάτω μέρος της σελίδας.',
    weCouldntFind: 'Δεν μπορέσαμε να βρούμε φωτογραφίες!  Γιατί δεν δοκιμάζετε μία από τις άλλες επιλογές;'
});


dojo.evalObjPath('xg.activity.nls', true);
dojo.lang.mixin(xg.activity.nls, xg.index.i18n, {
    edit: 'Επεξεργασία',
    show: 'Εμφάνιση',
    events: 'καταχωρήσεων',
    setWhatActivityGetsDisplayed: 'Ρυθμίστε ποια δραστηριότητα θα εμφανίζεται',
    save: 'Αποθήκευση',
    cancel: 'Άκυρο'
});


dojo.evalObjPath('xg.profiles.nls', true);
dojo.lang.mixin(xg.profiles.nls, xg.index.i18n, {
    pleaseEnterValueForPost: 'Εισάγετε μια τιμή για την καταχώριση',
    pleaseProvideAValidDate: 'Εισάγετε μια έγκυρη ημερομηνία',
    uploadAFile: 'Αποστολή αρχείου',
    pleaseEnterUrlOfLink: 'Εισάγετε τη διεύθυνση URL της σύνδεσης:',
    pleaseEnterTextOfLink: 'Ποιο κείμενο θέλετε να συνδέσετε;',
    edit: 'Επεξεργασία',
    recentlyAdded: 'Πρόσφατη προσθ.',
    featured: 'Προβεβλημένο',
    iHaveRecentlyAdded: 'Πρόσφατη προσθ.',
    fromTheSite: 'Από το κοινωνικό δίκτυο',
    cancel: 'Άκυρο',
    save: 'Αποθήκευση',
    loading: 'Γίνεται φόρτωση...',
    addAsFriend: 'Προσθήκη ως φίλος',
    removeAsFriend: 'Κατάργηση ως φίλος',
    requestSent: 'Αίτηση εστάλη!',
    sendingFriendRequest: 'Αποστολή αίτησης φίλου',
    thisIsYou: 'Είστε εσείς!',
    isYourFriend: 'Είναι ο φίλος σας',
    isBlocked: 'Είναι αποκλεισμένο',
    pleaseEnterAComment: 'Εισάγετε ένα σχόλιο',
    pleaseEnterPostBody: 'Πληκτρολογήστε κάτι στο σώμα της καταχώρισης',
    pleaseSelectAFile: 'Επιλέξτε ένα αρχείο',
    pleaseEnterChatter: 'Πληκτρολογήστε κάτι για το σχόλιό σας',
    toggleBetweenHTML: 'Εμφάνιση/απόκρυψη κώδικα HTML',
    attachAFile: 'Επισύναψη αρχείου',
    addAPhoto: 'Προσθήκη φωτογραφίας',
    insertALink: 'Εισαγωγή σύνδεσης',
    changeTextSize: 'Αλλαγή μεγέθους κειμένου',
    makeABulletedList: 'Δημιουργία λίστας με κουκίδες',
    makeANumberedList: 'Δημιουργία λίστας με αρίθμηση',
    crossOutText: 'Διακριτή διαγραφή κειμένου',
    underlineText: 'Υπογράμμιση κειμένου',
    italicizeText: 'Πλάγια γραφή κειμένου',
    boldText: 'Έντονη γραφή κειμένου',
    letMeApproveChatters: 'Μπορώ να εγκρίνω σχόλια πριν από την καταχώριση;',
    noPostChattersImmediately: 'Όχι, να γίνει άμεση καταχώριση σχολίων',
    yesApproveChattersFirst: 'Ναι, να γίνει πρώτα έγκριση των σχολίων',
    yourCommentMustBeApproved: 'Το σχόλιό σας πρέπει να εγκριθεί πριν να μπορέσει κάποιος να το δει.',
    reallyDeleteThisPost: 'Να γίνει πράγματι διαγραφή της καταχώρισης;',
    commentWall: 'Πίνακας σχολίων',
    commentWallNComments: function(n) {
        switch(n) {
            case 1: return 'Πίνακας σχολίων (1 σχόλιο) ';
            default: return 'Πίνακας σχολίων (' + n + ' σχόλια) ';
        }
    },
    display: 'Εμφάνιση',
    from: 'Από',
    show: 'Εμφάνιση',
    rows: 'σειρές',
    posts: 'καταχωρίσεις',
    returnToDefaultWarning: 'Αυτό θα μεταφέρει όλα τα χαρακτηριστικά και το θέμα της Η σελίδα μου στην προεπιλογή του δικτύου. Θέλετε να συνεχίσετε;',
    networkError: 'Σφάλμα δικτύου',
    wereSorry: 'Λυπούμαστε, αλλά δεν μπορούμε να αποθηκεύσουμε τη νέα σας διάταξη αυτή τη στιγμή. Αυτό οφείλεται μάλλον σε απώλεια της σύνδεσης με το Internet. Ελέγξτε τη σύνδεσή σας και προσπαθήστε πάλι.'
});


dojo.evalObjPath('xg.shared.nls', true);
dojo.lang.mixin(xg.shared.nls, xg.index.i18n, {
    removeFriendTitle: 'Να γίνει κατάργηση ως φίλος;',
    removeAsFriend: 'Κατάργηση ως φίλος',
    removeFriendConfirm: 'Είστε βέβαιοι ότι θέλετε να καταργήσετε αυτό το άτομο ως φίλο;',
    locationNotFound: function(location) { return '<em>' + location + '</em> not found.'; },
    confirmation: 'Επιβεβαίωση',
    showMap: 'Εμφάνιση χάρτη',
    hideMap: 'Απόκρυψη χάρτη',
    yourCommentMustBeApproved: 'Το σχόλιό σας πρέπει να εγκριθεί πριν να γίνει ορατό σε όλους.',
    nComments: function(n) {
	    switch(n) {
	        case 1: return '1 σχόλιο ';
	        default: return n + ' Σχόλια ';
	    }
	},
    uploadAFile: 'Αποστολή αρχείου',
    addExistingFile: 'ή εισαγάγετε ένα υπάρχον αρχείο',
    uploadAPhoto: 'Αποστολή φωτογραφίας',
    uploadAnImage: 'Αποστολή εικόνας',
    uploadAPhotoEllipsis: 'Αποστολή φωτογραφίας...',
    useExistingImage: 'Χρήση υπάρχουσας εικόνας:',
    existingImage: 'Υπάρχουσα εικόνα',
    useThemeImage: 'Χρήση εικόνας θέματος:',
    themeImage: 'Εικόνα θέματος',
    noImage: 'Χωρίς εικόνα',
    uploadImageFromComputer: 'Αποστολή εικόνας από τον υπολογιστή σας',
    tileThisImage: 'Παράθεση αυτής της εικόνας',
    done: 'Τέλος',
    currentImage: 'Τρέχουσα εικόνα',
    pickAColor: 'Επιλογή χρώματος...',
    openColorPicker: 'Άνοιγμα επιλογέα χρωμάτων',
    loading: 'Γίνεται φόρτωση...',
    ok: 'OK',
    save: 'Αποθήκευση',
    cancel: 'Άκυρο',
    saving: 'Γίνεται αποθήκευση...',
    addAnImage: 'Προσθήκη εικόνας',
    bold: 'Έντονα',
    italic: 'Πλάγια',
    underline: 'Υπογράμμιση',
    strikethrough: 'Διακριτή διαγραφή',
    addHyperink: 'Προσθήκη υπερ-σύνδεσης',
    options: 'Επιλογές',
    wrapTextAroundImage: 'Αναδίπλωση κειμένου γύρω από εικόνα;',
    imageOnLeft: 'Εικόνα στα αριστερά;',
    imageOnRight: 'Εικόνα στα δεξιά;',
    createThumbnail: 'Δημιουργία μικρογραφίας;',
    pixels: 'pixel',
    createSmallerVersion: 'Δημιουργήστε μια μικρότερη έκδοση της εικόνας σας προς εμφάνιση.  Ρυθμίστε το πλάτος σε pixel.',
    popupWindow: 'Αναδυόμενο παράθυρο;',
    linkToFullSize: 'Δημιουργήστε σύνδεση στην πλήρη έκδοση της εικόνας σε ένα αναδυόμενο παράθυρο.',
    add: 'Προσθήκη',
    keepWindowOpen: 'Κρατήστε ανοικτό αυτό το παράθυρο του προγράμματος περιήγησης όσο συνεχίζεται η αποστολή.',
    cancelUpload: 'Ακύρωση αποστολής',
    pleaseSelectAFile: 'Επιλέξτε ένα αρχείο εικόνας',
    pleaseSpecifyAThumbnailSize: 'Καθορίστε ένα μέγεθος μικρογραφίας',
    thumbnailSizeMustBeNumber: 'Το μέγεθος μικρογραφίας πρέπει να είναι αριθμός',
    addExistingImage: 'ή εισάγετε μια υπάρχουσα εικόνα',
    clickToEdit: 'Κάντε κλικ για επεξεργασία',
    sendingFriendRequest: 'Αποστολή αίτησης φίλου',
    requestSent: 'Αίτηση εστάλη!',
    pleaseCorrectErrors: 'Διορθώστε αυτά τα σφάλματα',
    tagThis: 'Βάλτε ετικέτα σε αυτό',
    addOrEditYourTags: 'Προσθέστε ή επεξεργαστείτε τις ετικέτες σας:',
    addYourRating: 'Προσθέστε την αξιολόγησή σας:',
    separateMultipleTagsWithCommas: 'Ξεχωρίστε τις πολλαπλές ετικέτες με κόμμα, π.χ. εντυπωσιακό, "Νέα Ζηλανδία"',
    saved: 'Αποθηκεύτηκε!',
    noo: 'ΝΕΟ',
    none: 'ΚΑΝΕΝΑ',
    joinNow: 'Γίνετε μέλος τώρα',
    join: 'Γίνετε μέλος',
    youHaventRated: 'Δεν έχετε αξιολογήσει ακόμα αυτό το στοιχείο.',
    yourRatedThis: function(n) {
        switch(n) {
            case 1: return 'Αξιολογήσατε το στοιχείο με 1 αστέρι. ';
            default: return 'Αξιολογήσατε το στοιχείο με ' + n + ' αστέρια. ';
        }
    },
    yourRatingHasBeenAdded: 'Η αξιολόγησή σας προστέθηκε.',
    thereWasAnErrorRating: 'Παρουσιάστηκε σφάλμα στην αξιολόγηση αυτού του περιεχομένου.',
    yourTagsHaveBeenAdded: 'Οι ετικέτες σας προστέθηκαν.',
    thereWasAnErrorTagging: 'Παρουσιάστηκε σφάλμα στην προσθήκη ετικετών.',
    addToFavorites: 'Προσθήκη στα Αγαπημένα',
    removeFromFavorites: 'Κατάργηση από τα Αγαπημένα',
    nStarsOutOfM: function(n,m) {
        switch(n) {
            case 1: return '1 αστέρι από ' + m;
            default: return n + ' αστέρια από ' + m;
        }
    },
    follow: 'Παρακολούθηση',
    stopFollowing: 'Διακοπή παρακολούθησης',
    pendingPromptTitle: 'Έγκριση ιδιότητας μέλους σε εκκρεμότητα',
    youCanDoThis: 'Μπορείτε να το κάνετε όταν η ιδιότητά σας ως μέλος εγκριθεί από τους διαχειριστές.',
    yourMessage: 'Το μήνυμά σας',
    updateMessage: 'Ενημέρωση μηνύματος',
    updateMessageQ: 'Να ενημερωθεί το μήνυμα;',
    removeWords: 'Για να βεβαιωθείτε ότι το ηλεκτρονικό σας μήνυμα παραδόθηκε με επιτυχία, συνιστούμε να επιστρέψετε και να αλλάξετε ή να καταργήσετε τις ακόλουθες λέξεις;',
    warningMessage: 'Φαίνεται ότι υπάρχουν μερικές λέξεις σε αυτό το ηλεκτρονικό μήνυμα που μπορεί να στείλουν το μήνυμά σας σε φάκελο ανεπιθύμητης ηλεκτρονικής αλληλογραφίας.',
    errorMessage: 'Υπάρχουν 6 ή περισσότερες λέξεις σε αυτό το μήνυμα που μπορεί να στείλουν το μήνυμά σας σε φάκελο ανεπιθύμητης ηλεκτρονικής αλληλογραφίας.',
    goBack: 'Επιστροφή',
    sendAnyway: 'Αποστολή',
    messageIsTooLong: function(n,m) { return 'Λυπούμαστε. Ο μέγιστος αριθμός χαρακτήρων είναι '+m+'.' },
    editYourTags: 'Επεξεργασία των ετικετών σας',
    addTags: 'Προσθήκη ετικετών',
    editLocation: 'Επεξεργασία τοποθεσίας',
    editTypes: 'Επεξεργασία τύπου εκδήλωσης',
    pleaseEnterAComment: 'Πληκτρολογήστε ένα σχόλιο',
    pleaseEnterAFileAddress: 'Πληκτρολογήστε τη διεύθυνση του αρχείου',
    pleaseEnterAWebsite: 'Πληκτρολογήστε μια διεύθυνση web'
});


dojo.evalObjPath('xg.video.nls', true);
dojo.lang.mixin(xg.video.nls, xg.index.i18n, {
    edit: 'Επεξεργασία',
    display: 'Εμφάνιση',
    detail: 'Λεπτομέρεια',
    player: 'Πρόγραμμα αναπαραγωγής',
    from: 'Από',
    show: 'Εμφάνιση',
    videos: 'βίντεο',
    cancel: 'Άκυρο',
    save: 'Αποθήκευση',
    saving: 'Γίνεται αποθήκευση...',
    deleteThisVideo: 'Να γίνει διαγραφή του βίντεο;',
    numberOfCharactersExceedsMaximum: function(n, maximum) { return 'Ο αριθμός χαρακτήρων (' + n + ') υπερβαίνει το μέγιστο όριο (' + maximum + ') '; },
    weCouldNotLookUpAddress: function(address) { return 'Δεν μπορούμε να αναζητήσουμε τη διεύθυνση "' + address + '". '; },
    approve: 'Έγκριση',
    approving: 'Γίνεται έγκριση...',
    keepWindowOpenWhileApproving: 'Κρατήστε ανοικτό αυτό το παράθυρο του προγράμματος περιήγησης όσο διαρκεί η έγκριση των βίντεο.  Αυτή η διαδικασία μπορεί να διαρκέσει μερικά λεπτά.',
    'delete': 'Διαγραφή',
    deleting: 'Γίνεται διαγραφή...',
    keepWindowOpenWhileDeleting: 'Κρατήστε ανοικτό αυτό το παράθυρο του προγράμματος περιήγησης όσο διαρκεί η διαγραφή των βίντεο.  Αυτή η διαδικασία μπορεί να διαρκέσει μερικά λεπτά.',
    pasteInEmbedCode: 'Για ένα βίντεο από άλλη τοποθεσία, πραγματοποιήστε επικόλληση στον ενσωματωμένο κώδικα.',
    pleaseSelectVideoToUpload: 'Επιλέξτε ένα βίντεο για αποστολή.',
    embedCodeContainsMoreThanOneVideo: 'Ο ενσωματωμένος κώδικας περιέχει περισσότερα από ένα βίντεο.  Βεβαιωθείτε ότι έχει μόνο ένα <object> ή/και μια <embed> ετικέτα.',
    embedCodeMissingTag: 'Από τον ενσωματωμένο κώδικα λείπει ένα &lt; embed&gt;  ή μια &lt; object&gt;  ετικέτα.',
    fileIsNotAMov: 'Αυτό το αρχείο δεν φαίνεται να είναι ένα αρχείο . mov, . mpg, . mp4, . avi, . 3gp ή . wmv.  Είστε βέβαιοι ότι θέλετε να γίνει αποστολή;',
    pleaseEnterAComment: 'Εισάγετε ένα σχόλιο.',
    youRatedVideoNStars: function(n) {
        switch(n) {
            case 1: return 'Αξιολογήσατε το βίντεο με 1 αστέρι! ';
            default: return 'Αξιολογήσατε το βίντεο με ' + n + ' αστέρια! ';
        }
    },
    deleteThisComment: 'Να γίνει διαγραφή του σχολίου;',
    embedHTMLCode: 'Ενσωματωμένος κώδικας HTML:',
    copyHTMLCode: 'Αντιγραφή κώδικα HTML',
    directLink: 'Άμεσος σύνδεσμος',
    addToMyspace: 'Προσθήκη στο MySpace',
    shareOnFacebook: 'Κοινή χρήση με το Facebook',
    addToOthers: 'Προσθήκη σε άλλο'
});


dojo.evalObjPath('xg.events.nls', true);
dojo.lang.mixin(xg.events.nls, xg.index.i18n, {
    messageIsTooLong: function(n) { return 'Το μήνυμά σας είναι πολύ μεγάλο. Χρησιμοποιήστε '+n+' χαρακτήρες ή λιγότερους.'; },
    sendMessageToGuests: 'Αποστολή μηνύματος σε καλεσμένους',
    sendMessageToGuestsThat: 'Αποστολή μηνύματος σε καλεσμένους που:',
    areAttending: 'θα παρευρεθούν',
    mightAttend: 'ίσως παρευρεθούν',
    haveNotYetRsvped: 'δεν έχουν λάβει ακόμα απάντηση στην πρόσκληση',
    areNotAttending: 'δε θα παρευρεθούν',
    messageSent: 'Το μήνυμα εστάλη!',
    chooseRecipient: 'Επιλέξτε παραλήπτη.',
    pleaseChooseImage: 'Επιλέξτε μια εικόνα για την εκδήλωση',
    pleaseEnterAMessage: 'Πληκτρολογήστε ένα μήνυμα.',
    pleaseEnterDescription: 'Πληκτρολογήστε μια περιγραφή για την εκδήλωση',
    pleaseEnterLocation: 'Πληκτρολογήστε μια τοποθεσία για την εκδήλωση',
    pleaseEnterTitle: 'Πληκτρολογήστε έναν τίτλο για την εκδήλωση',
    pleaseEnterType: 'Πληκτρολογήστε έναν τουλάχιστο τύπο για την εκδήλωση',
    send: 'Αποστολή',
    sending: 'Γίνεται αποστολή...',
    thereHasBeenAnError: 'Παρουσιάστηκε σφάλμα',
    yourMessage: 'Το μήνυμά σας',
    yourMessageHasBeenSent: 'Το μήνυμά σας εστάλη.',
    yourMessageIsBeingSent: 'Το μήνυμά σας αποστέλλεται.'
});


dojo.evalObjPath('xg.notes.nls', true);
dojo.lang.mixin(xg.notes.nls, xg.index.i18n, {
    addNewNote: 'Προσθήκη νέας σημείωσης',
    noteTitleTooLong: 'Ο τίτλος της σημείωσης είναι πολύ μεγάλος',
    pleaseEnterNoteEntry: 'Εισάγετε μια καταχώρηση σημείωσης',
    pleaseEnterNoteTitle: 'Πληκτρολογήστε τον τίτλο σημείωσης!'
});