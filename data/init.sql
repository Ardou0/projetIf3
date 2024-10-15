-- Création de la base de données
CREATE DATABASE IF NOT EXISTS travel_agency CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE travel_agency;

-- Table Destination
CREATE TABLE destination (
    destination_id INT AUTO_INCREMENT PRIMARY KEY,
    country VARCHAR(100) NOT NULL,
    city VARCHAR(100) NOT NULL
);

-- Table Company (Société)
CREATE TABLE company (
    company_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    picture VARCHAR(255) UNIQUE
);

-- Table Référence Transport (Transport reference)
CREATE TABLE transport_reference (
    transport_reference_id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    destination_id INT NOT NULL,
    provider_name VARCHAR(100) NOT NULL,
    transport_type ENUM('plane', 'train', 'bus', 'car') NOT NULL,
    ticket_format VARCHAR(100),
    seat_available VARCHAR(100),
    price DECIMAL(10, 2),
    FOREIGN KEY (company_id) REFERENCES company(company_id) ON DELETE CASCADE,
    FOREIGN KEY (destination_id) REFERENCES destination(destination_id) ON DELETE CASCADE
);

-- Table Référence Hébergement (Accommodation reference)
CREATE TABLE accommodation_reference (
    accommodation_reference_id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    destination_id INT NOT NULL,
    provider_name VARCHAR(100) NOT NULL,
    room_type VARCHAR(50),
    amenities TEXT,
    max_occupants INT NOT NULL DEFAULT 1,
    price_per_night DECIMAL(10, 2),
    FOREIGN KEY (company_id) REFERENCES company(company_id) ON DELETE CASCADE,
    FOREIGN KEY (destination_id) REFERENCES destination(destination_id) ON DELETE CASCADE
);

-- Table Forfait référence (Travel Package reference)
CREATE TABLE package_reference (
    package_reference_id INT AUTO_INCREMENT PRIMARY KEY,
    destination_id INT NOT NULL,
    transport_reference_id INT DEFAULT NULL,
    accommodation_reference_id INT DEFAULT NULL, 
    duration INT NOT NULL, -- durée en jours
    price DECIMAL(10, 2) NOT NULL,
    description TEXT NOT NULL,
    activity_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (destination_id) REFERENCES destination(destination_id) ON DELETE CASCADE,
    FOREIGN KEY (transport_reference_id) REFERENCES transport_reference(transport_reference_id) ON DELETE CASCADE,
    FOREIGN KEY (accommodation_reference_id) REFERENCES accommodation_reference(accommodation_reference_id) ON DELETE CASCADE
);

-- Table Activité
CREATE TABLE activity (
    activity_id INT AUTO_INCREMENT PRIMARY KEY,
    package_reference_id INT NOT NULL,
    activity_name VARCHAR(255) NOT NULL,
    activity_description TEXT,
    duration_hours DECIMAL(5, 2), -- Durée en heures
    FOREIGN KEY (package_reference_id) REFERENCES package_reference(package_reference_id) ON DELETE CASCADE
);

-- Table Transport
CREATE TABLE transport (
    transport_id INT AUTO_INCREMENT PRIMARY KEY,
    transport_reference_id INT NOT NULL,
    departure_date DATETIME NOT NULL,
    arrival_date DATETIME NOT NULL,
    ticket_number VARCHAR(100) UNIQUE,
    seat_preference VARCHAR(100),
    FOREIGN KEY (transport_reference_id) REFERENCES transport_reference(transport_reference_id) ON DELETE CASCADE
);

-- Table Hébergement (Accommodation)
CREATE TABLE accommodation (
    accommodation_id INT AUTO_INCREMENT PRIMARY KEY,
    accommodation_reference_id INT NOT NULL,
    check_in_date DATETIME NOT NULL,
    check_out_date DATETIME NOT NULL,
    FOREIGN KEY (accommodation_reference_id) REFERENCES accommodation_reference(accommodation_reference_id) ON DELETE CASCADE
);

-- Table Forfait (Travel Package)
CREATE TABLE package (
    package_id INT AUTO_INCREMENT PRIMARY KEY,
    package_reference_id INT NOT NULL,
    booked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (package_reference_id) REFERENCES package_reference(package_reference_id) ON DELETE CASCADE
);

-- Table Client
CREATE TABLE client (
    client_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone_number VARCHAR(20),
    birthdate DATE,
    loyalty_points INT DEFAULT 0,
    travel_preferences ENUM('plane', 'train', 'bus', 'car') DEFAULT 'plane',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table Réservation (Reservation)
CREATE TABLE reservation (
    reservation_id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    num_passengers INT NOT NULL DEFAULT 1,
    destination_id INT NOT NULL,
    package_id INT DEFAULT NULL,
    transport_id INT DEFAULT NULL,
    accommodation_id INT DEFAULT NULL,
    reservation_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    travel_date_from DATE NOT NULL,  -- date de début du voyage
    travel_date_to DATE NOT NULL,  -- date de fin du voyage
    loyalty_points_generated INT NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') NOT NULL DEFAULT 'pending',
    FOREIGN KEY (client_id) REFERENCES client(client_id) ON DELETE CASCADE,
    FOREIGN KEY (package_id) REFERENCES package(package_id) ON DELETE CASCADE,
    FOREIGN KEY (transport_id) REFERENCES transport(transport_id) ON DELETE CASCADE,
    FOREIGN KEY (accommodation_id) REFERENCES accommodation(accommodation_id) ON DELETE CASCADE,
    FOREIGN KEY (destination_id) REFERENCES destination(destination_id) ON DELETE CASCADE
);

-- Table Passenger
CREATE TABLE passenger (
    passenger_id INT AUTO_INCREMENT PRIMARY KEY,
    reservation_id INT NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    seat_preference VARCHAR(100),
    transport_id INT DEFAULT NULL,
    FOREIGN KEY (reservation_id) REFERENCES reservation(reservation_id) ON DELETE CASCADE,
    FOREIGN KEY (transport_id) REFERENCES transport(transport_id) ON DELETE CASCADE
);

-- Table Paiement (Payment)
CREATE TABLE payment (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    reservation_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    payment_method ENUM('credit_card', 'bank_transfer') NOT NULL,
    payment_status ENUM('pending', 'completed', 'refunded') DEFAULT 'pending',
    FOREIGN KEY (reservation_id) REFERENCES reservation(reservation_id) ON DELETE CASCADE
);

-- Table Avis (Feedback)
CREATE TABLE comments (
    avis_id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    package_id INT DEFAULT NULL,
    transport_id INT DEFAULT NULL,
    accommodation_id INT DEFAULT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    comments TEXT,
    review_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES client(client_id) ON DELETE CASCADE,
    FOREIGN KEY (package_id) REFERENCES package(package_id) ON DELETE CASCADE,
    FOREIGN KEY (transport_id) REFERENCES transport(transport_id) ON DELETE CASCADE,
    FOREIGN KEY (accommodation_id) REFERENCES accommodation(accommodation_id) ON DELETE CASCADE
);

-- Vue pour l'historique du client
CREATE OR REPLACE DEFINER='root'@'%' VIEW client_history AS
SELECT
    C.client_id,
    C.first_name,
    C.last_name,
    C.email,
    R.reservation_id,
    D.city AS destination_city,
    D.country AS destination_country,
    PR.duration,
    R.travel_date_from,
    R.travel_date_to,
    R.status AS reservation_status,
    P.amount AS payment_amount,
    P.payment_date,
    P.payment_status,
    CM.rating,
    CM.comments AS feedback
FROM
    client C
LEFT JOIN reservation R ON C.client_id = R.client_id
LEFT JOIN package PCK ON R.package_id = PCK.package_id
LEFT JOIN package_reference PR ON PCK.package_reference_id = PR.package_reference_id
LEFT JOIN destination D ON PR.destination_id = D.destination_id
LEFT JOIN payment P ON R.reservation_id = P.reservation_id
LEFT JOIN comments CM ON C.client_id = CM.client_id
ORDER BY C.client_id, R.reservation_date DESC;



-- Ajout de données test pour la table destination
INSERT INTO destination (country, city) VALUES 
('France', 'Paris'),
('USA', 'New York'),
('Italy', 'Rome');

-- Ajout de données test pour la table company
INSERT INTO company (full_name, email, password, picture) VALUES 
('Company A', 'companya@example.com', 'password123', 'companya.jpg'),
('Company B', 'companyb@example.com', 'password456', 'companyb.jpg');

-- Ajout de données test pour la table transport_reference
INSERT INTO transport_reference (company_id, destination_id, provider_name, transport_type, ticket_format, seat_available, price) VALUES 
(1, 1, 'Air France', 'plane', 'E-ticket', 'Economy', 150.00),
(2, 2, 'Delta Airlines', 'plane', 'E-ticket', 'Business', 500.00),
(1, 3, 'Trenitalia', 'train', 'Paper', 'First Class', 100.00);

-- Ajout de données test pour la table accommodation_reference
INSERT INTO accommodation_reference (company_id, destination_id, provider_name, room_type, amenities, max_occupants, price_per_night) VALUES 
(1, 1, 'Hotel Parisien', 'Double Room', 'Wifi, Breakfast', 2, 120.00),
(2, 2, 'NY Grand Hotel', 'Suite', 'Pool, Gym', 4, 300.00),
(1, 3, 'Rome Hotel', 'Single Room', 'Wifi', 1, 80.00);

-- Ajout de données test pour la table package_reference
INSERT INTO package_reference (destination_id, transport_reference_id, accommodation_reference_id, duration, price, description, activity_count) VALUES 
(1, 1, 1, 7, 1000.00, 'Paris vacation package with flights and hotel', 3),
(2, 2, 2, 5, 1500.00, 'New York vacation package with flights and hotel', 4),
(3, 3, 3, 10, 1200.00, 'Rome vacation package with train and hotel', 2);

-- Ajout de données test pour la table activity
INSERT INTO activity (package_reference_id, activity_name, activity_description, duration_hours) VALUES 
(1, 'Eiffel Tower Visit', 'Visit the iconic Eiffel Tower in Paris', 3.00),
(1, 'Louvre Museum Tour', 'Explore the world-famous Louvre Museum', 4.00),
(2, 'Statue of Liberty Tour', 'Tour to the Statue of Liberty in New York', 2.00);

-- Ajout de données test pour la table transport
INSERT INTO transport (transport_reference_id, departure_date, arrival_date, ticket_number, seat_preference) VALUES 
(1, '2024-10-20 08:00:00', '2024-10-20 10:00:00', 'AF123456', 'Window'),
(2, '2024-11-10 14:00:00', '2024-11-10 18:00:00', 'DL789012', 'Aisle');

-- Ajout de données test pour la table accommodation
INSERT INTO accommodation (accommodation_reference_id, check_in_date, check_out_date) VALUES 
(1, '2024-10-20 15:00:00', '2024-10-27 11:00:00'),
(2, '2024-11-10 15:00:00', '2024-11-15 11:00:00');

-- Ajout de données test pour la table package
INSERT INTO package (package_reference_id) VALUES 
(1),
(2);

-- Ajout de données test pour la table client
INSERT INTO client (first_name, last_name, email, password, phone_number, birthdate, loyalty_points, travel_preferences) VALUES 
('John', 'Doe', 'john.doe@example.com', 'securepassword', '555-1234', '1990-05-15', 500, 'plane'),
('Jane', 'Smith', 'jane.smith@example.com', 'anotherpassword', '555-5678', '1985-08-20', 300, 'train');

-- Ajout de données test pour la table reservation
INSERT INTO reservation (client_id, num_passengers, destination_id, package_id, transport_id, accommodation_id, travel_date_from, travel_date_to, loyalty_points_generated, status) VALUES 
(1, 2, 1, 1, 1, 1, '2024-10-20', '2024-10-27', 100, 'confirmed'),
(2, 1, 2, 2, 2, 2, '2024-11-10', '2024-11-15', 150, 'pending');

-- Ajout de données test pour la table passenger
INSERT INTO passenger (reservation_id, first_name, last_name, email, seat_preference, transport_id) VALUES 
(1, 'John', 'Doe', 'john.doe@example.com', 'Window', 1),
(1, 'Jane', 'Doe', 'jane.doe@example.com', 'Aisle', 1);

-- Ajout de données test pour la table payment
INSERT INTO payment (reservation_id, amount, payment_date, payment_method, payment_status) VALUES 
(1, 1000.00, '2024-10-10 12:00:00', 'credit_card', 'completed'),
(2, 1500.00, '2024-11-01 12:00:00', 'bank_transfer', 'pending');

-- Ajout de données test pour la table comments
INSERT INTO comments (client_id, package_id, transport_id, accommodation_id, rating, comments) VALUES 
(1, 1, 1, 1, 5, 'Amazing trip to Paris, everything was perfect!'),
(2, 2, 2, 2, 4, 'Great trip to New York, but the hotel could have been better.');
