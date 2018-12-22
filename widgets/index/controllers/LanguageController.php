<?php

/**
 * Dispatches requests pertaining to translations and I18N.
 */
class Index_LanguageController extends W_Controller {

    /** Number of file fields to display on the upload form. */
    const UPLOAD_FIELD_COUNT = 1; // BAZ-4375 [Jon Aquino 2007-09-07]

    /**
     * Displays a list of languages.
     */
    public function action_list() {
        XG_SecurityHelper::redirectIfNotAdmin();
    }

    /**
     * Displays a form for uploading old Ning translation files.
     *
     * @param $errors array  error messages for errors that occurred while processing the form
     *
     * Expected GET variables:
     *     locale - locale code for the translation, or null to use the network's current language
     */
    public function action_upload($errors = array()) {
        XG_SecurityHelper::redirectIfNotAdmin();
        $locale = $_GET['locale'] ? $_GET['locale'] : XG_LOCALE;
        $this->form = new XNC_Form(array('locale' => $locale));
        $this->uploadFieldCount = self::UPLOAD_FIELD_COUNT;
        $this->errors = $errors;
    }

    /**
     * Processes the form for uploading old Ning translation files.
     *
     * Expected POST variables:
     *     locale - locale to which to append the contents of the translation files
     *     file1, file2, ..., file12 - uploaded files
     */
    public function action_doUpload() {
        XG_SecurityHelper::redirectIfNotAdmin();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirectTo('upload', 'language', array('locale' => $_POST['locale']));
            return;
        }
        XG_App::includeFileOnce('/lib/XG_FileHelper.php');
        for ($i = 1; $i <= self::UPLOAD_FIELD_COUNT; $i++) {
            if ($_POST["file$i"] && $_POST["file$i:status"]) { $errors["file$i"] = ($_POST["file$i"] ? $_POST["file$i"] : xg_text('FILE_N', $i)) . ' - ' . XG_FileHelper::uploadErrorMessage($_POST["file$i:status"]); }
        }
        if (count($errors)) {
            $this->forwardTo('upload', 'language', array($errors));
            return;
        }
        $this->_widget->includeFileOnce('/lib/helpers/Index_MessageCatalogReader.php');
        $this->_widget->includeFileOnce('/lib/helpers/Index_LanguageHelper.php');
        $errors = array();
        $newMessages = array();
        try {
            for ($i = 1; $i <= self::UPLOAD_FIELD_COUNT; $i++) {
                if (! $_POST["file$i"]) { continue; }
                $reader = new Index_MessageCatalogReader();
                $reader->read(XN_Request::uploadedFileContents($_POST["file$i"]));
                $messages = $reader->getData();
                $newMessages = array_merge($newMessages, $messages);
                foreach (Index_LanguageHelper::validate($_POST['locale'], $messages) as $name => $error) {
                    $errors[] = $_POST["file$i"] . ' - ' . Index_LanguageHelper::displayName($name) . ' - ' . $error;
                }
            }
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
        if ($errors) {
            $this->forwardTo('upload', 'language', array($errors));
            return;
        }
        $oldMessages = array();
        foreach (Index_LanguageHelper::messages($_POST['locale']) as $name => $message) {
            $oldMessages[$name] = $message['targetText'];
        }
        $customMessages = Index_LanguageHelper::customMessages($_POST['locale']);
        $this->writeCatalogs($_POST['locale'], array_merge($customMessages, $newMessages), $oldMessages);
        $this->redirectTo('edit', 'language', array('locale' => $_POST['locale'], 'uploaded' => 1));
    }

    /**
     * Displays a form for editing a translation
     *
     * @param $errors array  error messages keyed by message name (e.g., "POPULAR_MEMBERS", "xg.forum.nls.deleteCategory", "SPECIAL RULES", "TAB NAMES")
     *
     * Expected GET variables:
     *     locale - locale code for the translation, or null to use the network's current language
     *     q - optional search terms, separated by spaces
     *     filter - "all" (default), "missing", or "changed"
     *     page - current page number
     *     saved - whether the current page was just saved successfully
     *     localeChanged - whether the locale was just made the current locale
     *     uploaded - whether an upload just finished successfully
     *     localeDeleted - whether the text has been reset to the original version
     *
     * Expected POST variables:
     *     messages - messages submitted by the user: message name => string or JavaScript/PHP code
     */
    public function action_edit($errors = array()) {
        XG_SecurityHelper::redirectIfNotAdmin();
        $this->_widget->includeFileOnce('/lib/helpers/Index_LanguageHelper.php');
        $this->locale = $_GET['locale'] ? $_GET['locale'] : XG_LOCALE;
        if ($this->locale == Index_LanguageHelper::NEW_TRANSLATION_LOCALE) {
            $this->redirectTo('new');
            return;
        }
        $this->errors = $errors;
        $this->localeName = XG_LanguageHelper::name($this->locale);
        $this->searchTerms = $_GET['q'];
        $this->filter = $_GET['filter'] ? $_GET['filter'] : 'all';
        $this->messages = Index_LanguageHelper::messages($this->locale, $this->searchTerms, $this->filter, $_POST['messages'], $this->errors, $percentComplete);
        unset($this->messages[Index_LanguageHelper::TAB_NAMES_KEY]);  // This function is deprecated [Jon Aquino 2007-08-20]
        $this->percentComplete = $percentComplete;
        $this->percentCompleteThreshold = 75;
        $this->totalMessageCount = count($this->messages);
        $this->pageSize = 100;
        $this->page = $_GET['page'] ? $_GET['page'] : 1;
         XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
        $this->messages = array_slice($this->messages, XG_PaginationHelper::computeStart($this->page, $this->pageSize), $this->pageSize);
        $this->hasPreviousPage = $this->page > 1;
        $this->hasNextPage = $this->page < ceil($this->totalMessageCount / $this->pageSize);
        $this->displayRestoreDefaultsButton = file_exists(XG_LanguageHelper::customPhpCatalogPath($this->locale)) && file_exists(XG_LanguageHelper::phpCatalogPath($this->locale));
        if ($_GET['saved']) { $this->displaySavedNotification = true; }
        elseif ($_GET['localeChanged']) { $this->displayLocaleChangedNotification = true; }
        elseif ($_GET['uploaded']) { $this->displayUploadedNotification = true; }
        elseif ($_GET['localeDeleted']) { $this->displayLocaleDeletedNotification = true; }
    }

    /**
     * Processes the form for editing a translation. Depending on whether this app is symlinked or not,
     * this action will either overwrite the "decentralized" message-catalog files or the original message-catalog files.
     *
     * Expected GET variables:
     *     locale - locale code for the translation, e.g., fr_CA
     *     q - optional search terms, separated by spaces
     *     filter - "all" (default), "missing", or "changed"
     *     page - current page number
     *
     * Expected POST variables
     *     messages - message name => string or JavaScript/PHP code
     *     messageNames - array of message names
     */
    public function action_update() {
        XG_SecurityHelper::redirectIfNotAdmin();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirectTo('edit', 'language', array('locale' => $_GET['locale'], 'q' => $_GET['q'], 'filter' => $_GET['filter'], 'page' => $_GET['page']));
            return;
        }
        $this->_widget->includeFileOnce('/lib/helpers/Index_LanguageHelper.php');
        $newMessages = array();
        $customMessages = Index_LanguageHelper::customMessages($_GET['locale']);
        $oldMessages = array();
        foreach (Index_LanguageHelper::messages($_GET['locale']) as $name => $message) {
            $oldMessages[$name] = $message['targetText'];
        }
        // Detect whether user explicitly set a string to empty. We don't want to blindly accept all empty strings,
        // as empty strings override the default en_US strings.  [Jon Aquino 2007-08-09]
        foreach ($_POST['messageNames'] as $name) {
            if (! $_POST['messages'][$name]) {
                $_POST['messages'][$name] = ! is_null($oldMessages[$name]) ? '' : null;
            }
        }
        foreach ($_POST['messages'] as $name => $submittedMessage) {
            if (Index_LanguageHelper::messagesSame($submittedMessage, $oldMessages[$name])) { continue; };
            $customMessages[$name] = $submittedMessage;
            $newMessages[$name] = $submittedMessage;
        }
        $errors = Index_LanguageHelper::validate($_GET['locale'], $newMessages);
        if (!XG_App::symlinked() && $_GET['locale'] == 'en_US' && XN_AtomHelper::$DOMAIN_SUFFIX != '.ning.com') {
            $errors = array('' => 'Note to XNA developers: We shouldn\'t use the Language Editor to edit the en_US texts; otherwise we will lose the comments and whitespace between each mozzle\'s section. This isn\'t a big deal, but it would be nice to preserve these. Instead, edit the en_US files directly. [Jon Aquino 2007-08-13]');
        }
        if ($errors) {
            $this->forwardTo('edit', 'language', array($errors));
            return;
        }
        $this->writeCatalogs($_GET['locale'], $customMessages, $oldMessages);
        $this->redirectTo('edit', 'language', array('locale' => $_GET['locale'], 'q' => $_GET['q'], 'filter' => $_GET['filter'], 'page' => $_GET['page'], 'saved' => 1));
    }

    /**
     * Depending on whether this app is symlinked or not, either overwrites
     * the "decentralized" message-catalog files or the original message-catalog files.
     *
     * @param $locale string  locale code for the translation, e.g., fr_CA
     * @param $customMessages array  messages that differ from the original catalog
     * @param $oldMessages array  messages in the original catalog
     */
    private function writeCatalogs($locale, &$customMessages, &$oldMessages) {
        $this->_widget->includeFileOnce('/lib/helpers/Index_MessageCatalogWriter.php');
        XG_App::includeFileOnce('/lib/XG_FileHelper.php');
        // TODO: Remove special handling for non-symlinked apps. [Jon Aquino 2008-09-10]
        if (! XG_App::symlinked() && file_exists(XG_LanguageHelper::phpCatalogPath($locale)) && ! file_exists(XG_LanguageHelper::customPhpCatalogPath($locale))) {
            $this->writeCatalogsProper($locale, $customMessages, $oldMessages);
        } else {
            $this->writeCustomCatalogsProper($locale, $customMessages);
        }
        if ($locale == XG_LOCALE) { XG_LanguageHelper::updateLocaleConfig(); }
    }

    /**
     * Overwrites the original PHP and JavaScript message-catalog files.
     *
     * @param $locale string  locale code for the translation, e.g., fr_CA
     * @param $customMessages array  messages that differ from the original catalog
     * @param $oldMessages array  messages in the original catalog
     */
    private function writeCatalogsProper($locale, &$customMessages, &$oldMessages) {
        // Sanity checks aren't critical here, as non-symlinked apps are typically owned by Ning developers,
        // and in any case we back up translation files in /xn_private/xn_volatile/backups  [Jon Aquino 2007-08-10]
        $writer = new Index_MessageCatalogWriter();
        list($php, $javaScript) = $writer->write($locale, array_merge($oldMessages, $customMessages));
        $this->_widget->includeFileOnce('/lib/helpers/Index_MessageCatalogReader.php');
        XG_App::includeFileOnce('/lib/XG_LangHelper.php');
        $mergedPhp = $originalPhp = file_get_contents(XG_LanguageHelper::phpCatalogPath($locale));
        $mergedPhp = XG_LangHelper::replaceOnce(Index_MessageCatalogReader::extractPhpArray($originalPhp, true), Index_MessageCatalogReader::extractPhpArray($php, true), $mergedPhp);
        $mergedPhp = XG_LangHelper::replaceOnce(Index_MessageCatalogReader::extractPhpSpecialRules($originalPhp, true), Index_MessageCatalogReader::extractPhpSpecialRules($php, true), $mergedPhp);
        $mergedPhp = XG_LangHelper::replaceOnce(Index_MessageCatalogReader::extractPhpTabNamingRules($originalPhp, true), Index_MessageCatalogReader::extractPhpTabNamingRules($php, true), $mergedPhp);
        $javaScript = str_replace('xg.custom', 'xg', $javaScript);
        XG_FileHelper::filePutContentsWithBackup(XG_LanguageHelper::phpCatalogPath($locale), $mergedPhp);
        XG_FileHelper::filePutContentsWithBackup(XG_LanguageHelper::javaScriptCatalogPath($locale), $javaScript);
    }

    /**
     * Saves the PHP and JavaScript custom-message-catalog files.
     *
     * @param $locale string  locale code for the translation, e.g., fr_CA
     * @param $customMessages array  messages that differ from the original catalog
     */
    private function writeCustomCatalogsProper($locale, &$customMessages) {
        $writer = new Index_MessageCatalogWriter();
        list($php, $javaScript) = $writer->write($locale, $customMessages);
        @mkdir(dirname(XG_LanguageHelper::customPhpCatalogPath($locale)), 0777, true);
        @mkdir(dirname(XG_LanguageHelper::customJavaScriptCatalogPath($locale)), 0777, true);
        XG_FileHelper::filePutContentsWithBackup(XG_LanguageHelper::customPhpCatalogPath($locale), $php);
        XG_FileHelper::filePutContentsWithBackup(XG_LanguageHelper::customJavaScriptCatalogPath($locale), $javaScript);
    }

    /**
     * Sets the language for the network.
     *
     * Expected GET variables:
     *     locale - locale code for the translation, e.g., fr_CA
     *     q - optional search terms, separated by spaces
     *     filter - "all" (default), "missing", or "changed"
     *     page - current page number
     */
    public function action_setCurrentLocale() {
        XG_SecurityHelper::redirectIfNotAdmin();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirectTo('edit', 'language', array('locale' => $_GET['locale'], 'q' => $_GET['q'], 'filter' => $_GET['filter'], 'page' => $_GET['page']));
            return;
        }
        XG_LanguageHelper::setCurrentLocale($_GET['locale']);
        $this->redirectTo('edit', 'language', array('locale' => $_GET['locale'], 'q' => $_GET['q'], 'filter' => $_GET['filter'], 'page' => $_GET['page'], 'localeChanged' => 1));
    }

    /**
     * Deletes the customizations for the specified locale.
     *
     * Expected GET variables:
     *     locale - locale code for the translation, e.g., fr_CA
     *     removeFromLocaleList - whether to remove the locale from the list of languages
     *     target - URL to redirect to afterwards; localeDeleted=1 will be appended to the URL
     */
    public function action_delete() {
        XG_SecurityHelper::redirectIfNotAdmin();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: ' . $_GET['target']);
        }
    // @todo unlink only if the file exists [ywh 2008-05-02]
    // create new lang, back to lang editor, delete
        unlink(XG_LanguageHelper::customPhpCatalogPath($_GET['locale']));
        unlink(XG_LanguageHelper::customJavaScriptCatalogPath($_GET['locale']));
        if ($_GET['removeFromLocaleList']) {
            XG_LanguageHelper::removeCustomLocaleMetadata($_GET['locale']);
            if ($_GET['locale'] == XG_LOCALE) { XG_LanguageHelper::setCurrentLocale('en_US'); }
        } elseif ($_GET['locale'] == XG_LOCALE) {
            XG_LanguageHelper::updateLocaleConfig();
        }
        header('Location: ' . XG_HttpHelper::addParameter($_GET['target'], 'localeDeleted', 1));
    }

    /**
     * Displays a form for creating a translation.
     *
     * @param $error string  An error message
     *
     * Expected GET variables:
     *         target - "upload" to go to the Upload page afterwards; otherwise will go to the Edit page
     */
    public function action_new($error = null) {
        XG_SecurityHelper::redirectIfNotAdmin();
        $this->_widget->includeFileOnce('/lib/helpers/Index_LanguageHelper.php');
        $this->form = new XNC_Form(array('target' => $_GET['target'], 'baseLocale' => 'en_US'));
        $this->error = $error;
    }

    /**
     * Processes the form for creating a translation.
     *
     * Expected POST variables:
     *         name - name for the new translation, e.g., French (France)
     *         baseLocale - locale for filling in missing strings, e.g., fr_CA
     *         target - "upload" to go to the Upload page afterwards; otherwise will go to the Edit page
     */
    public function action_create() {
        XG_SecurityHelper::redirectIfNotAdmin();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirectTo('new');
            return;
        }
        $this->_widget->includeFileOnce('/lib/helpers/Index_LanguageHelper.php');
        $name = trim(mb_substr($_POST['name'], 0, Index_LanguageHelper::MAX_LANGUAGE_NAME_LENGTH));
        if (! $name) {
            $this->forwardTo('new', 'language', array(xg_text('ENTER_NAME_FOR_TRANSLATION')));
            return;
        }
        foreach (XG_LanguageHelper::localesAndNames() as $otherName) {
            if (mb_strtolower(preg_replace('@\s@ui', '', $name)) == mb_strtolower(preg_replace('@\s@ui', '', $otherName))) {
                $this->forwardTo('new', 'language', array(xg_text('LANGUAGE_NAME_TAKEN')));
                return;
            }
        }
        $locale = 'custom_' . mt_rand();
        XG_LanguageHelper::addCustomLocaleMetadata($locale, $name, $_POST['baseLocale']);
        if ($_POST['target'] == 'upload') {
            $this->redirectTo('upload', 'language', array('locale' => $locale));
            return;
        } else {
            $this->redirectTo('edit', 'language', array('locale' => $locale));
            return;
        }
    }

}


