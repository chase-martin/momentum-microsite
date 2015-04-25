<?php
class indexController extends baseController {
    function index() {

        $cms = new DCms( $this->registry);
        $user = $_SESSION['CMS_USER'];
        $cms->editing = !empty($user['author']);

        $cms->load('protected:momentum');
        $page = $_GET['page']?$_GET['page']: $this->registry->template->get_page;
        $cms->setup($page);

        $this->registry->template->cms = $cms;
    }
}