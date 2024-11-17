<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

class offerController
{

    private $_view;
    private $_model;


    public function __construct($url)
    {
        $this->_model = new model();
        if (isset($_SESSION['type']) and $_SESSION['type'] == "company") {
            if (isset($url[2]) and isset($url[3]) and isset($url[4]) and !empty($url[4])) {
                if ($url[2] == "edit" and !isset($_POST['name'])) {
                    $this->editViewOffer($url[3], $url[4]);
                } elseif ($url[2] == "edit" and isset($_POST['name'])) {
                    $this->editOffer($url[4], $url[3]);
                } elseif ($url[2] == "delete") {
                    $this->deleteOffer($url[4], $url[3]);
                } else {
                    header("location:" . URL . "dashboard");
                    exit();
                }
            } elseif (isset($url[2]) and isset($url[3]) and (!isset($url[4]) or empty($url[4]))) {
                if ($url[2] == "new" and isset($_POST['name'])) {
                    $this->createOffer($url[3]);
                } else {
                    header("location:" . URL . "dashboard");
                    exit();
                }
            } elseif (isset($_POST['type']) and (!isset($url[3]) or empty($url[3]))) {
                $this->generateView("post/" . $_POST['type']);
            } else {
                header("location:" . URL . "dashboard");
                exit();
            }
        } else {
            header("location:" . URL);
            exit();
        }
    }

    private function generateView($page, array $params = [])
    {
        $sql = "SELECT * FROM destination";
        $destination = $this->_model->executeQuery($sql);
        $params['destinations'] = $destination;
        $params['data'] = $this->_model->extract($page . ".json");
        $this->_view = new view($page);
        $this->_view->buildUp($params);
    }

    private function editViewOffer($type, $id)
    {
        $sql = "SELECT * FROM " . $type . "_reference WHERE " . $type . "_reference_id = ? and company_id = ?";
        $offer = $this->_model->executeQuery($sql, [$id, $_SESSION['id']]);

        if ($offer) {
            $assets = [$type => $offer[0]];
            $assets['method'] = "edit";
        }

        if ($type == "package") {
            $sql = "SELECT * FROM activity WHERE package_reference_id = ? and company_id = ?";
            $activities = $this->_model->executeQuery($sql, [$id, $_SESSION['id']]);
            $sql = "SELECT * FROM itinerary WHERE " . $type . "_reference_id = ?";
            $itinerary = $this->_model->executeQuery($sql, [$id]);
            if ($activities) {
                $assets["activities"] = $activities;
            }
            if ($itinerary) {
                $assets["itinerary"] = $itinerary[0];
            }
        }

        if ($offer) {
            $this->generateView("post/" . $type, $assets);
        }
    }

    private function editOffer($id, $type)
    {
        if ($type == "transport") {
            $sql = "UPDATE `transport_reference` SET `destination_id`= ?,`provider_name`= ?,`transport_type`= ?,`ticket_format`= ?,`seat_available`= ?,`price`=? WHERE transport_reference_id = ? and company_id = ?";
            $params = [
                $_POST['destination'],
                $_POST['provider'],
                $_POST['type'],
                $_POST['ticket'],
                $_POST['seat'],
                $_POST['price'],
                $id,
                $_SESSION['id']
            ];
            $this->_model->executeQuery($sql, $params);
            header('location:' . URL . 'dashboard');
            exit();
        } elseif ($type == "accommodation") {

            if (empty($_POST['picture']) or !isset($_POST['picture'])) {
                $sql = "UPDATE `accommodation_reference` SET `destination_id`= ?,`provider_name`= ?,`room_type`= ?,`amenities`= ?,`max_occupants`= ?,`price_per_night`= ? WHERE company_id = ? and accommodation_reference_id = ?";
                $params = [
                    $_POST['destination'],
                    $_POST['provider'],
                    $_POST['room'],
                    $_POST['amenities'],
                    $_POST['occupants'],
                    $_POST['price'],
                    $_SESSION['id'],
                    $id
                ];
            } else {

                $picture = $this->uploadPicture("accommodations", "picture");
                $sql = "UPDATE `accommodation_reference` SET `destination_id`= ?,`provider_name`= ?,`accommodation_photo`= ?,`room_type`= ?,`amenities`= ?,`max_occupants`= ?,`price_per_night`= ? WHERE company_id = ? and accommodation_reference_id = ?";
                $params = [
                    $_POST['destination'],
                    $_POST['provider'],
                    $picture,
                    $_POST['room'],
                    $_POST['amenities'],
                    $_POST['occupants'],
                    $_POST['price'],
                    $_SESSION['id'],
                    $id
                ];
            }

            $this->_model->executeQuery($sql, $params);
            header('location:' . URL . 'dashboard');
            exit();
        } elseif ($type == "package") {
            // Verify destinations consistency
            $destinationId = $_POST['destination'];
            $transportId = $_POST['transport'];
            $accommodationId = $_POST['accommodation'];

            $sqlTransport = "SELECT destination_id FROM transport_reference WHERE transport_reference_id = ? AND company_id = ?";
            $transport = $this->_model->executeQuery($sqlTransport, [$transportId, $_SESSION['id']]);

            $sqlAccommodation = "SELECT destination_id FROM accommodation_reference WHERE accommodation_reference_id = ? AND company_id = ?";
            $accommodation = $this->_model->executeQuery($sqlAccommodation, [$accommodationId, $_SESSION['id']]);

            if (!$transport || !$accommodation || $transport[0]['destination_id'] != $destinationId || $accommodation[0]['destination_id'] != $destinationId) {
                header('location:' . URL . 'dashboard/error');
                exit();
            }

            // update package reference
            $sql = "UPDATE package_reference SET destination_id= ?,transport_reference_id= ?,accommodation_reference_id= ?,duration= ?,price= ?,description= ? WHERE company_id = ? and package_reference_id = ?";
            $params = [
                $destinationId,
                $transportId,
                $accommodationId,
                $_POST['duration'],
                $_POST['price'],
                $_POST['description'],
                $_SESSION['id'],
                $id
            ];
            $this->_model->executeQuery($sql, $params);

            $sql = "UPDATE itinerary SET schedule_description= ?,emergency_contact= ? WHERE package_reference_id = ?";
            $params = [
                htmlspecialchars($_POST['itinerary']),
                htmlspecialchars($_POST['emergency_contact']),
                $id
            ];
            $this->_model->executeQuery($sql, $params);

            // Keep track of activities that are being updated or inserted
            $updatedActivityIds = [];

            // Insert or update activities
            for ($i = 0; $i < 6; $i++) {
                if (isset($_POST['activity-' . $i . '-name'])) {

                    if ($_POST['activity-' . $i . '-id'] != 0) {
                        $activityName = $_POST['activity-' . $i . '-name'];
                        $activityDescription = $_POST['activity-' . $i . '-description'];
                        $activityDuration = $_POST['activity-' . $i . '-duration'];
                        if (isset($_FILES['activity-' . $i . '-picture']) && !empty($_FILES['activity-' . $i . '-picture']['name'])) {
                            $activityPicture = $this->uploadPicture("activities", 'activity-' . $i . '-picture');

                            $sqlActivity = "UPDATE activity SET activity_name= ?,activity_photo= ?,activity_description= ?,duration_hours= ? WHERE activity_id = ? and package_reference_id = ? and company_id = ?";
                            $activityParams = [
                                $activityName,
                                $activityPicture,
                                $activityDescription,
                                $activityDuration,
                                $_POST['activity-' . $i . '-id'],
                                $id,
                                $_SESSION['id']
                            ];
                        } else {
                            $sqlActivity = "UPDATE activity SET activity_name= ?,activity_description= ?,duration_hours= ? WHERE activity_id = ? and package_reference_id = ? and company_id = ?";
                            $activityParams = [
                                $activityName,
                                $activityDescription,
                                $activityDuration,
                                $_POST['activity-' . $i . '-id'],
                                $id,
                                $_SESSION['id']
                            ];
                        }
                        $this->_model->executeQuery($sqlActivity, $activityParams);
                        $updatedActivityIds[] = $_POST['activity-' . $i . '-id'];
                    } else {
                        $activityName = $_POST['activity-' . $i . '-name'];
                        $activityDescription = $_POST['activity-' . $i . '-description'];
                        $activityDuration = $_POST['activity-' . $i . '-duration'];
                        $activityPicture = $this->uploadPicture("activities", 'activity-' . $i . '-picture');

                        $sqlActivity = "INSERT INTO activity(company_id, package_reference_id, activity_name, activity_photo, activity_description, duration_hours) VALUES (?, ?, ?, ?, ?, ?)";
                        $activityParams = [
                            $_SESSION['id'],
                            $id,
                            $activityName,
                            $activityPicture,
                            $activityDescription,
                            $activityDuration,
                        ];
                        $this->_model->executeQuery($sqlActivity, $activityParams);
                        $updatedActivityIds[] = $this->_model->executeQuery("SELECT activity_id FROM activity WHERE package_reference_id = ? and company_id = ? and activity_photo = ? ORDER BY activity_id DESC LIMIT 1", [$id, $_SESSION['id'], $activityPicture])[0]['activity_id'];
                    }
                }
            }
            
            // Delete activities that were not updated or inserted
            if (!empty($updatedActivityIds)) {
                $sqlDeleteActivities = "DELETE FROM activity WHERE package_reference_id = ? AND company_id = ? AND activity_id NOT IN (" . implode(",", array_fill(0, count($updatedActivityIds), '?')) . ")";
                $deleteParams = array_merge([
                    $id,
                    $_SESSION['id']
                ], $updatedActivityIds);
                $this->_model->executeQuery($sqlDeleteActivities, $deleteParams);
            } else {
                // If there are no updated activities, delete all activities for this package
                $sqlDeleteAllActivities = "DELETE FROM activity WHERE package_reference_id = ? AND company_id = ?";
                $this->_model->executeQuery($sqlDeleteAllActivities, [$id, $_SESSION['id']]);
            }

            header('location:' . URL . 'dashboard');
            exit();
        }
    }

    private function createOffer($type)
    {
        if ($type == "transport") {
            $sql = "INSERT INTO `transport_reference`(`company_id`, `destination_id`, `provider_name`, `transport_type`, `ticket_format`, `seat_available`, `price`) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $params = [
                $_SESSION['id'],
                $_POST['destination'],
                $_POST['provider'],
                $_POST['type'],
                $_POST['ticket'],
                $_POST['seat'],
                $_POST['price'],
            ];
            $this->_model->executeQuery($sql, $params);
            header('location:' . URL . 'dashboard');
            exit();
        } elseif ($type == "accommodation") {
            $sql = "INSERT INTO `accommodation_reference`(`company_id`, `destination_id`, `provider_name`, `accommodation_photo`, `room_type`, `amenities`, `max_occupants`, `price_per_night`) VALUES (?,?,?,?,?,?,?,?)";
            $picture = $this->uploadPicture("accommodations", "picture");
            $params = [
                $_SESSION['id'],
                $_POST['destination'],
                $_POST['provider'],
                $picture,
                $_POST['room'],
                $_POST['amenities'],
                $_POST['occupants'],
                $_POST['price'],
            ];
            $this->_model->executeQuery($sql, $params);
            header('location:' . URL . 'dashboard');
            exit();
        } elseif ($type == "package") {
            // Verify destinations consistency
            $destinationId = $_POST['destination'];
            $transportId = $_POST['transport'];
            $accommodationId = $_POST['accommodation'];

            $sqlTransport = "SELECT destination_id FROM transport_reference WHERE transport_reference_id = ? AND company_id = ?";
            $transport = $this->_model->executeQuery($sqlTransport, [$transportId, $_SESSION['id']]);

            $sqlAccommodation = "SELECT destination_id FROM accommodation_reference WHERE accommodation_reference_id = ? AND company_id = ?";
            $accommodation = $this->_model->executeQuery($sqlAccommodation, [$accommodationId, $_SESSION['id']]);

            if (!$transport || !$accommodation || $transport[0]['destination_id'] != $destinationId || $accommodation[0]['destination_id'] != $destinationId) {
                header('location:' . URL . 'dashboard/error');
                exit();
            }

            // Insert package reference
            $sql = "INSERT INTO `package_reference`(`company_id`, `destination_id`, `transport_reference_id`, `accommodation_reference_id`, `duration`, `price`, `description`) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $params = [
                $_SESSION['id'],
                $destinationId,
                $transportId,
                $accommodationId,
                $_POST['duration'],
                $_POST['price'],
                $_POST['description'],
            ];
            $this->_model->executeQuery($sql, $params);

            // Retrieve the inserted package reference ID
            $sqlGetPackageId = "SELECT package_reference_id FROM package_reference WHERE company_id = ? ORDER BY package_reference_id DESC LIMIT 1";
            $packageReference = $this->_model->executeQuery($sqlGetPackageId, [$_SESSION['id']]);
            $packageReferenceId = $packageReference[0]['package_reference_id'];

            $sql = "INSERT INTO `itinerary`(`package_reference_id`, `schedule_description`, `emergency_contact`) VALUES (?, ?, ?)";
            $params = [
                $packageReferenceId,
                htmlspecialchars($_POST['itinerary']),
                htmlspecialchars($_POST['emergency_contact'])
            ];
            $this->_model->executeQuery($sql, $params);

            // Insert activities
            for ($i = 0; $i < 5; $i++) {
                if (isset($_POST['activity-' . $i . '-name'])) {

                    $activityName = $_POST['activity-' . $i . '-name'];
                    $activityDescription = $_POST['activity-' . $i . '-description'];
                    $activityDuration = $_POST['activity-' . $i . '-duration'];
                    $activityPicture = $this->uploadPicture("activities", 'activity-' . $i . '-picture');

                    $sqlActivity = "INSERT INTO `activity`(`company_id`, `package_reference_id`, `activity_name`, `activity_photo`, `activity_description`, `duration_hours`) VALUES (?, ?, ?, ?, ?, ?)";
                    $activityParams = [
                        $_SESSION['id'],
                        $packageReferenceId,
                        $activityName,
                        $activityPicture,
                        $activityDescription,
                        $activityDuration,
                    ];
                    $this->_model->executeQuery($sqlActivity, $activityParams);
                }
            }

            header('location:' . URL . 'dashboard');
            exit();
        }
    }

    private function deleteOffer($id, $type)
    {
        if ($type == "transport") {
            $sql = "DELETE FROM `transport_reference` WHERE transport_reference_id = ? and company_id = ?";
            $params = [
                $id,
                $_SESSION['id']
            ];
            $this->_model->executeQuery($sql, $params);
            header('location:' . URL . 'dashboard');
            exit();
        } elseif ($type == "accommodation") {
            $sql = "DELETE FROM `accommodation_reference` WHERE accommodation_reference_id = ? and company_id = ?";
            $params = [
                $id,
                $_SESSION['id']
            ];
            $this->_model->executeQuery($sql, $params);
            header('location:' . URL . 'dashboard');
            exit();
        } elseif ($type == "package") {
            $sql = "DELETE FROM `package_reference` WHERE package_reference_id = ? and company_id = ?";
            $params = [
                $id,
                $_SESSION['id']
            ];
            $this->_model->executeQuery($sql, $params);
            header('location:' . URL . 'dashboard');
            exit();
        }
    }

    private function uploadPicture($dir, $name)
    {
        if (!empty($_FILES[$name])) {
            $allowed = array('png', 'jpg', 'PNG', 'JPG', 'jpeg', 'JPEG');
            $filename = $_FILES[$name]['name'];
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if (!in_array($ext, $allowed)) {
                header('Location: ' . URL . 'profile/error');
            }
            // Move uploaded file if provided
            $targetDir = PATH . "public/img/" . $dir . "/";
            $pictureName = hash('sha256', basename($_FILES[$name]['name'] . time())) . "." . $ext;
            $targetFile = $targetDir . $pictureName;
            move_uploaded_file($_FILES[$name]['tmp_name'], $targetFile);
            return $pictureName;
        }
    }
}
