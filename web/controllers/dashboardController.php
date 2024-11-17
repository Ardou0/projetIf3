<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/core/options.php');

class dashboardController
{
    private $_view;
    private $_model;

    public function __construct($url)
    {
        $this->_model = new model();
        if (!isset($_SESSION['type']) || $_SESSION['type'] != "company") {
            header('location:' . URL);
            exit();
        } else {
            $this->_view = new view("dashboard");
            $sql = "SELECT 
                c.company_id,
                COUNT(r.reservation_id) AS total_reservations_count,
                SUM(
                    CASE
                        WHEN r.package_id IS NOT NULL THEN pr.price
                        ELSE 0
                    END +
                    CASE
                        WHEN r.accommodation_id IS NOT NULL THEN ar.price_per_night * DATEDIFF(r.travel_date_to, r.travel_date_from)
                        ELSE 0
                    END +
                    CASE
                        WHEN r.transport_id IS NOT NULL THEN tr.price
                        ELSE 0
                    END
                ) AS total_revenue,
                COUNT(CASE WHEN r.status = 'pending' THEN 1 END) AS pending_reservations_count,
                COUNT(CASE WHEN r.status = 'cancelled' THEN 1 END) AS cancelled_reservations_count,
                COUNT(CASE WHEN r.status = 'completed' OR r.status = 'confirmed' THEN 1 END) AS confirmed_reservations_count,
                SUM(
                    CASE
                        WHEN pm.payment_status = 'completed' THEN pm.amount
                        ELSE 0
                    END
                ) AS total_payment_received,
                SUM(
                    CASE
                        WHEN pm.payment_status = 'pending' THEN pm.amount
                        ELSE 0
                    END
                ) AS total_payment_pending,
                SUM(
                    CASE
                        WHEN pm.payment_status = 'refunded' THEN pm.amount
                        ELSE 0
                    END
                ) AS total_payment_refunded
            FROM 
                reservation r
            LEFT JOIN package p ON r.package_id = p.package_id
            LEFT JOIN package_reference pr ON p.package_reference_id = pr.package_reference_id
            LEFT JOIN accommodation a ON r.accommodation_id = a.accommodation_id
            LEFT JOIN accommodation_reference ar ON a.accommodation_reference_id = ar.accommodation_reference_id
            LEFT JOIN transport t ON r.transport_id = t.transport_id
            LEFT JOIN transport_reference tr ON t.transport_reference_id = tr.transport_reference_id
            LEFT JOIN company c ON (pr.company_id = c.company_id OR ar.company_id = c.company_id OR tr.company_id = c.company_id)
            LEFT JOIN payment pm ON r.reservation_id = pm.reservation_id
            WHERE 
                c.company_id = ?
            GROUP BY 
                c.company_id;";
            $summarize = $this->_model->executeQuery($sql, [$_SESSION['id']]);

            if (empty($summarize)) {
                $summarize = [
                    "company_id" => $_SESSION['id'],
                    "total_reservations_count" => 0,
                    "total_revenue" => 0.0,
                    "pending_reservations_count" => 0,
                    "cancelled_reservations_count" => 0,
                    "confirmed_reservations_count" => 0,
                    "total_payment_received" => 0.0,
                    "total_payment_pending" => 0.0,
                    "total_payment_refunded" => 0.0
                ];
                $clients = [];
            } else {
                $summarize = $summarize[0];
                // Get clients related to the company
                $clients_sql = "SELECT 
                cl.client_id,
                cl.first_name,
                cl.last_name,
                cl.email,
                cl.phone_number,
                cl.birthdate,
                cl.loyalty_points,
                r.reservation_id,
                r.status
                FROM 
                client cl
                JOIN reservation r ON cl.client_id = r.client_id
                LEFT JOIN package p ON r.package_id = p.package_id
                LEFT JOIN package_reference pr ON p.package_reference_id = pr.package_reference_id
                LEFT JOIN accommodation a ON r.accommodation_id = a.accommodation_id
                LEFT JOIN accommodation_reference ar ON a.accommodation_reference_id = ar.accommodation_reference_id
                LEFT JOIN transport t ON r.transport_id = t.transport_id
                LEFT JOIN transport_reference tr ON t.transport_reference_id = tr.transport_reference_id
                LEFT JOIN company c ON (pr.company_id = c.company_id OR ar.company_id = c.company_id OR tr.company_id = c.company_id)
                WHERE 
                c.company_id = ?";
                $clients = $this->_model->executeQuery($clients_sql, [$_SESSION['id']]);
            }

            $ratings_sql = "SELECT 
                AVG(coalesce(cm.rating, 0)) AS average_rating,
                COUNT(cm.avis_id) AS total_reviews
            FROM 
                comments cm
            LEFT JOIN package p ON cm.package_id = p.package_id
            LEFT JOIN package_reference pr ON p.package_reference_id = pr.package_reference_id
            LEFT JOIN transport t ON cm.transport_id = t.transport_id
            LEFT JOIN transport_reference tr ON t.transport_reference_id = tr.transport_reference_id
            LEFT JOIN accommodation a ON cm.accommodation_id = a.accommodation_id
            LEFT JOIN accommodation_reference ar ON a.accommodation_reference_id = ar.accommodation_reference_id
            LEFT JOIN company c ON (pr.company_id = c.company_id OR ar.company_id = c.company_id OR tr.company_id = c.company_id)
            WHERE 
                c.company_id = ?;";
            $ratings = $this->_model->executeQuery($ratings_sql, [$_SESSION['id']]);

            // Set default values if ratings query returns no results
            if (empty($ratings)) {
                $ratings = [
                    "average_rating" => 0.0,
                    "total_reviews" => 0
                ];
            } else {
                $ratings = $ratings[0];
            }

            $sql = "SELECT ac.accommodation_reference_id, ac.provider_name, ac.price_per_night, ac.amenities, d.city FROM `accommodation_reference` ac INNER JOIN destination d on ac.destination_id = d.destination_id WHERE ac.company_id = ?";
            $accommodations = $this->_model->executeQuery($sql, [$_SESSION['id']]);
            $sql = "SELECT pr.package_reference_id, pr.description, pr.price, d.city FROM `package_reference` pr INNER JOIN destination d on pr.destination_id = d.destination_id WHERE pr.company_id = ?;";
            $package = $this->_model->executeQuery($sql, [$_SESSION['id']]);
            $sql = "SELECT a.package_reference_id, a.activity_name FROM activity a WHERE a.company_id = ?";
            $activities = $this->_model->executeQuery($sql, [$_SESSION['id']]);
            $sql = "SELECT tr.provider_name, tr.transport_reference_id, tr.transport_type, tr.price, d.city FROM `transport_reference` tr INNER JOIN destination d ON tr.destination_id = d.destination_id WHERE company_id = ?";
            $transport = $this->_model->executeQuery($sql, [$_SESSION['id']]);
            $this->_view->buildUp(array("accommodations" => $accommodations, "packages" => $package, "activities" => $activities, "transports" => $transport, "summarize" => $summarize, "clients" => $clients, "ratings" => $ratings, "data" => $this->_model->extract("dashboard.json")));
        }
    }
}
