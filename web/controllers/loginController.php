<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

class loginController
{

    private $_view;
    private $_model;

    public function __construct($args)
    {
        $this->_model = new model();

        if (isset($args[2]) and $args[2] == "logout") {
            session_destroy();
            header('location:' . URL);
            exit();
        } else {
            if ($_POST) {
                $this->client_connect();
            } else {
                if (isset($args[2]) == "notification" and isset($args[3])) {
                    $notification = $args[3];
                } else {
                    $notification = "";
                }
                $this->_view = new view("login");
                $this->_view->buildUp(array("data" => $this->_model->extract("login.json"), "notification" => $notification));
            }
        }
    }

    private function client_connect()
    {
        $credentials = array();
        foreach ($_POST as $key => $value) {
            $credentials[$key] = htmlspecialchars($value);
        }
        if(isset($credentials["email"]) && isset($credentials["password"]) && isset($credentials["type"])) {
            $result = $this->_model->executeQuery("SELECT * FROM " . $credentials["type"] . " WHERE email= ? and password = ? ", array($credentials["email"], hash("sha256", $credentials["password"])));
            if (isset($result) && count($result) == 1) {
                $_SESSION['type'] = $credentials['type'];
                if ($credentials['type'] == "client") {
                    $_SESSION['first_name'] = $result[0]['first_name'];
                    $_SESSION['last_name'] = $result[0]['last_name'];
                    $_SESSION['username'] = $_SESSION['first_name'];
                    $_SESSION['phone'] = $result[0]['phone_number'];
                    $_SESSION['birth'] = $result[0]['birthdate'];
                    $_SESSION['transport'] = $result[0]['travel_preferences'];
                    $_SESSION['id'] = $result[0]['client_id'];
                } else {
                    $_SESSION['full_name'] = $result[0]['full_name'];
                    $_SESSION['username'] = $_SESSION['full_name'];
                    $_SESSION['photo'] = $result[0]['picture'];
                    $_SESSION['id'] = $result[0]['company_id'];
                }
                $_SESSION['email'] = $result[0]['email'];
                header('location:' . URL);
                exit();
            } else {
                header('location:' . URL . 'login/notification/error');
                exit();
            }
        }
        else {
            header('location:' . URL . 'login/notification/error');
            exit();
        }
    }
}
