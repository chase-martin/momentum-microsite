<?php

class cmsController extends baseController {

    function index() {

    }

    public function updateAction($data) {
        $user = $_SESSION['CMS_USER'];
        if (empty($user['author']))
            return;
        $cms          = new DCms($this->registry);
        $cms->editing = true;
        $cms->load($data['docId']);
        $cms->updateField($data['namespace'], $data['value']);

    }

    public function loginAction() {
        $username = $_POST['username'];
        if ($_POST['logout']) {
            $_SESSION['CMS_USER'] = null;
        }
        else if (isset($username)) {
            $password             = $_POST['password'];
            $user                 = $this->registry->mongo->getCollection('users')->findOne(array('username' => $username, 'password' => $password));
            $_SESSION['CMS_USER'] = $user;
        }

        $this->registry->template->setView('content', 'CMS:login');
        $this->registry->template->setView('main', 'CMS:login');

    }

    public function createVariant($data) {
        $user = $_SESSION['CMS_USER'];
        if (empty($user['author']))
            return;
        $cms          = new DCms($this->registry);
        $cms->editing = true;
        $cms->load($data['docId']);
        $cms->createVariant($data['variantName']);

    }
}