-- Création de la base de données
CREATE DATABASE IF NOT EXISTS travel_agency;
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
    tag VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    picture VARCHAR(100) UNIQUE
);

-- Table Référence Transport (Transport reference)
CREATE TABLE transport_reference (
    transport_reference_id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    destination_id INT NOT NULL,
    provider_name VARCHAR(100) NOT NULL,
    transport_type ENUM('plane', 'train', 'bus', 'car') NOT NULL,
    ticket_format VARCHAR(50),
    seat_available VARCHAR(50),
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
    price_per_night DECIMAL(10, 2),
    FOREIGN KEY (company_id) REFERENCES company(company_id) ON DELETE CASCADE,
    FOREIGN KEY (destination_id) REFERENCES destination(destination_id) ON DELETE CASCADE
);

-- Table Forfait référence (Travel Package reference)
CREATE TABLE package_reference (
    package_reference_id INT AUTO_INCREMENT PRIMARY KEY,
    destination_id INT NOT NULL,
    duration INT NOT NULL, -- durée en jours
    price DECIMAL(10, 2) NOT NULL,
    itinerary TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (destination_id) REFERENCES destination(destination_id) ON DELETE CASCADE
);

-- Table Transport
CREATE TABLE transport (
    transport_id INT AUTO_INCREMENT PRIMARY KEY,
    transport_reference_id INT NOT NULL,
    departure_date DATETIME NOT NULL,
    arrival_date DATETIME NOT NULL,
    ticket_number VARCHAR(50) UNIQUE,
    seat_preference VARCHAR(50),
    FOREIGN KEY (transport_reference_id) REFERENCES transport_reference(transport_reference_id) ON DELETE CASCADE
);

-- Table Hébergement (Accommodation)
CREATE TABLE accommodation (
    accommodation_id INT AUTO_INCREMENT PRIMARY KEY,
    accommodation_reference_id INT NOT NULL,
    check_in_date DATETIME NOT NULL,
    check_out_date DATETIME NOT NULL,
    price DECIMAL(10, 2),
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
    tag VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone_number VARCHAR(20),
    loyalty_points INT DEFAULT 0,
    travel_preferences ENUM('plane', 'train', 'bus', 'car') DEFAULT 'plane',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table Utilisateur pour l'authentification
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    tag VARCHAR(255) NOT NULL,
    role ENUM('client', 'company') DEFAULT 'client',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table Réservation (Reservation)
CREATE TABLE reservation (
    reservation_id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    destination_id INT NOT NULL,
    package_id INT,
    transport_id INT,
    accommodation_id INT,
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
    package_id INT,
    transport_id INT,
    accommodation_id INT,
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
    C.full_name,
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
