<?php

/**
 * Dispatches requests pertaining to instances of the Page widget.
 */
class Page_InstanceController extends W_Controller {

    /**
     * Displays the form for editing Page instances.
     *
     * Expected GET variables:
     *     - saved - whether the update succeeded
     */
    public function action_edit() {
        XG_SecurityHelper::redirectIfNotOwner();
        $this->_widget->includeFileOnce('/lib/helpers/Page_InstanceHelper.php');
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->data = $json->encode(Page_InstanceHelper::load());
        $this->saved = $_GET['saved'];
    }

    /**
     * Processes the form for editing Page instances.
     *
     * Expected GET variables:
     *     - xn_out - should be "json"
     *
     * Expected POST variables:
     *     - data - json array of data for the desired Page instances
     *
     * JSON output:
     *     - success - whether the update succeeded
     *     - errors - HTML error messages keyed by instance index and field name
     */
    public function action_update() {
        XG_SecurityHelper::redirectIfNotOwner();
        XG_HttpHelper::trimGetAndPostValues();
        $json = new NF_JSON(SERVICES_JSON_LOOSE_TYPE);
        $data = $json->decode($_POST['data']);
        $this->_widget->includeFileOnce('/lib/helpers/Page_InstanceHelper.php');
        $this->errors = Page_InstanceHelper::save($data);
        $this->success = count($this->errors) == 0;
    }

}
