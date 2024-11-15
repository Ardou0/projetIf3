<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

class registerController
{

    private $_model;
    public function __construct()
    {
        $this->_model = new model();
        if (!isset($_POST['type'])) {
            header('location:' . URL . 'login');
            exit();
        } else {
            $credentials = array();
            foreach ($_POST as $key => $value) {
                $credentials[$key] = htmlspecialchars($value);
            }
            if (isset($credentials['type'])) {
                if ($credentials['type'] == "company") {
                    if (!isset($credentials['first_name']) || !isset($credentials['email']) || !isset($credentials['password'])) {
                        header('location:' . URL . 'login/notification/missing');
                        exit();
                    }
                }
                if ($credentials['type'] == "client") {
                    if (!isset($credentials['first_name']) || !isset($credentials['last_name']) || !isset($credentials['email']) || !isset($credentials['password'])) {
                        header('location:' . URL . 'login/notification/missing');
                        exit();
                    }
                }
                $result = $this->_model->executeQuery("SELECT * FROM " . $credentials["type"] . " WHERE email= ?", array($credentials['email']));
                if (!isset($result) || count($result) == 0) {
                    if ($credentials['type'] == "company") {
                        $result = $this->_model->executeQuery("INSERT INTO company (full_name, email, password, picture) VALUES ( ? , ? , ? , '')", array($credentials['first_name'], $credentials['email'], hash('sha256', $credentials['password'])));
                    }
                    if ($credentials['type'] == "client") {
                        $result = $this->_model->executeQuery("INSERT INTO client (first_name, last_name, email, password) VALUES ( ? , ? , ? , ?)", array($credentials['first_name'], $credentials['last_name'], $credentials['email'], hash('sha256', $credentials['password'])));
                    }
                    header('location:' . URL . 'login/notification/registered');
                    exit();
                }
            } else {
                header('location:' . URL . 'login/notification/missing');
                exit();
            }
        }
    }
}
