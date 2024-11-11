<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

class profileController
{
    private $_view;
    private $_model;

    public function __construct($url)
    {
        $this->_model = new model();
        $this->_view = new view("profile");

        if (!isset($_SESSION['type'])) {
            header('location:' . URL . 'login');
            exit();
        } else {
            $this->updateProfile();
            if(isset($url[2])) {
                $notification = $url[2];
            }
            else {
                $notification = "";
            }
            $this->_view->buildUp(array("data" => $this->_model->extract("profile.json"), "notification" => $notification));
        }
    }

    public function updateProfile()
    {
        // Check if this is a login information update (change of password)
        if (isset($_POST['oldpass']) && isset($_POST['newpass'])) {
            $oldPassword = hash('sha256', $_POST['oldpass']);
            $newPassword = hash('sha256', $_POST['newpass']);
            $email = htmlspecialchars($_POST['email']);

            // Verify old password
            $sql = "SELECT * FROM " . $_SESSION['type'] . " WHERE email = ? AND password = ?";
            $params = [$_SESSION['email'], $oldPassword];
            $currentPassword = $this->_model->executeQuery($sql, $params);

            if (isset($currentPassword) && count($currentPassword) == 1) {
                $updateSql = "UPDATE " . $_SESSION['type'] . " SET password = ?, email = ? WHERE email = ?;";
                $this->_model->executeQuery($updateSql, [$newPassword, $email, $_SESSION['email']]);
                $_SESSION['email'] = $email;
                header('Location: ' . URL . 'profile/updated');
                return;
            } else {
                header('Location: ' . URL . 'profile/error');
                return;
            }
        }

        // Check if this is a client personal data update
        if ($_SESSION['type'] == 'client' && isset($_POST['first-name']) && isset($_POST['last-name'])) {
            $firstName = htmlspecialchars($_POST['first-name']);
            $lastName = htmlspecialchars($_POST['last-name']);
            $phone = htmlspecialchars($_POST['phone']);
            $birth = $_POST['birth'];
            $preference = $_POST['preference'];

            $sql = "UPDATE client SET first_name = ?, last_name = ?, phone_number = ?, birthdate = ?, travel_preferences = ? WHERE email = ?";
            $params = [
                $firstName,
                $lastName,
                $phone,
                $birth,
                $preference,
                $_SESSION['email']
            ];

            $this->_model->executeQuery($sql, $params);
            $_SESSION['first_name'] = $firstName;
            $_SESSION['last_name'] = $lastName;
            $_SESSION['username'] = $firstName;
            $_SESSION['phone'] = $phone;
            $_SESSION['birth'] = $birth;
            $_SESSION['transport'] = $preference;
            header('Location: ' . URL . 'profile/updated');
            return;
        }
        // Check if this is a company profile data update
        if ($_SESSION['type'] == 'company' && isset($_POST['full_name'])) {
            $fullName = $_POST['full_name'];
            if (!empty($_FILES['picture'])) {
                $allowed = array('png', 'jpg', 'PNG', 'JPG', 'jpeg', 'JPEG');
                $filename = $_FILES['picture']['name'];
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                if (!in_array($ext, $allowed)) {
                    header('Location: ' . URL . 'profile/error');
                }
                // Move uploaded file if provided
                $targetDir = PATH . "public/img/profile/";
                $pictureName = hash('sha256', basename($_FILES['picture']['name'])) . "." . $ext;
                $targetFile = $targetDir . $pictureName;
                move_uploaded_file($_FILES['picture']['tmp_name'], $targetFile);

                $sql = "UPDATE company SET full_name = ?, picture = ? WHERE email = ?";
                $params = [
                    $fullName,
                    $pictureName,
                    $_SESSION['email']
                ];
                $_SESSION['photo'] = $pictureName;
            } else {
                $sql = "UPDATE company SET full_name = ? WHERE email = ?";
                $params = [
                    $fullName,
                    $_SESSION['email']
                ];
            }

            $this->_model->executeQuery($sql, $params);
            $_SESSION['full_name'] = $fullName;
            
        }

        // Redirect to profile after updating
    }
}
