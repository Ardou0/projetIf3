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
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
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
    max_occupants INT NOT NULL DEFAULT 1,
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
    full_name VARCHAR(100) NOT NULL,
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

-- Table Passenger
CREATE TABLE passenger (
    passenger_id INT AUTO_INCREMENT PRIMARY KEY,
    reservation_id INT NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    seat_preference VARCHAR(50),
    transport_id INT,
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











-- Insertion des données pour les Destinations
INSERT INTO destination (country, city) VALUES
('France', 'Paris'),
('USA', 'New York'),
('Japan', 'Tokyo'),
('Italy', 'Rome'),
('Australia', 'Sydney'),
('Brazil', 'Rio de Janeiro'),
('Germany', 'Berlin'),
('Canada', 'Toronto'),
('Mexico', 'Mexico City'),
('UK', 'London'),
('China', 'Beijing'),
('Spain', 'Barcelona'),
('Thailand', 'Bangkok'),
('India', 'Mumbai'),
('South Africa', 'Cape Town'),
('Russia', 'Moscow'),
('Argentina', 'Buenos Aires'),
('Turkey', 'Istanbul'),
('Netherlands', 'Amsterdam'),
('Egypt', 'Cairo');
-- Insertion des données pour la table Company
INSERT INTO company (full_name, email, password, picture) VALUES
('Air France', 'contact@airfrance.com', 'password123', 'airfrance.png'),
('Hilton Hotels', 'contact@hilton.com', 'password123', 'hilton.png'),
('Uber', 'contact@uber.com', 'password123', 'uber.png'),
('Booking.com', 'contact@booking.com', 'password123', 'booking.png'),
('Delta Airlines', 'contact@delta.com', 'password123', 'delta.png');

-- Insertion des données pour la table Transport Reference
INSERT INTO transport_reference (company_id, destination_id, provider_name, transport_type, ticket_format, seat_available, price) VALUES
(1, 1, 'Air France', 'plane', 'E-ticket', 'Business Class', 350.00),
(1, 2, 'Air France', 'plane', 'E-ticket', 'Economy', 500.00),
(5, 1, 'Delta Airlines', 'plane', 'E-ticket', 'First Class', 400.00),
(3, 6, 'Uber', 'car', 'Digital', 'Sedan', 45.00),
(3, 7, 'Uber', 'car', 'Digital', 'SUV', 60.00);

-- Insertion des données pour la table Accommodation Reference
INSERT INTO accommodation_reference (company_id, destination_id, provider_name, room_type, amenities, max_occupants, price_per_night) VALUES
(2, 1, 'Hilton Paris', 'Double', 'WiFi, Breakfast', 2, 150.00),
(2, 2, 'Hilton New York', 'Double', 'WiFi, Breakfast', 2, 180.00),
(2, 3, 'Hilton Tokyo', 'Single', 'WiFi, Breakfast', 1, 200.00),
(2, 4, 'Hilton Rome', 'Suite', 'WiFi, Breakfast', 4, 350.00),
(2, 5, 'Hilton Sydney', 'Double', 'WiFi, Breakfast', 2, 160.00);

-- Insertion des données pour la table Package Reference
INSERT INTO package_reference (destination_id, duration, price, itinerary) VALUES
(1, 7, 1000.00, 'Paris Tour, Louvre, Eiffel Tower'),
(2, 5, 800.00, 'Statue of Liberty, Central Park'),
(3, 10, 1200.00, 'Tokyo Tower, Mt. Fuji'),
(4, 6, 900.00, 'Colosseum, Vatican City'),
(5, 4, 600.00, 'Sydney Opera House, Bondi Beach');

-- Insertion des données pour la table Transport
INSERT INTO transport (transport_reference_id, departure_date, arrival_date, ticket_number, seat_preference) VALUES
(1, '2024-12-01 10:00:00', '2024-12-01 12:00:00', 'AF1234', 'Window'),
(2, '2024-12-02 14:00:00', '2024-12-02 16:00:00', 'AF5678', 'Aisle'),
(3, '2024-12-03 09:00:00', '2024-12-03 11:00:00', 'DL1234', 'Window'),
(4, '2024-12-04 15:00:00', '2024-12-04 16:00:00', 'UB5678', 'Middle'),
(5, '2024-12-05 08:00:00', '2024-12-05 09:00:00', 'UB1234', 'Window');

-- Insertion des données pour la table Accommodation
INSERT INTO accommodation (accommodation_reference_id, check_in_date, check_out_date, price) VALUES
(1, '2024-12-01 14:00:00', '2024-12-07 12:00:00', 900.00),
(2, '2024-12-02 14:00:00', '2024-12-06 12:00:00', 720.00),
(3, '2024-12-03 14:00:00', '2024-12-10 12:00:00', 1400.00),
(4, '2024-12-04 14:00:00', '2024-12-08 12:00:00', 1400.00),
(5, '2024-12-05 14:00:00', '2024-12-09 12:00:00', 640.00);

-- Insertion des données pour la table Package
INSERT INTO package (package_reference_id) VALUES
(1),
(2),
(3),
(4),
(5);

-- Insertion des données pour la table Client
INSERT INTO client (full_name, email, password, phone_number, birthdate, loyalty_points, travel_preferences) VALUES
('Alice Dupont', 'alice@example.com', 'hashed_password1', '123456789', '1990-01-15', 100, 'plane'),
('Bob Martin', 'bob@example.com', 'hashed_password2', '987654321', '1985-05-22', 150, 'train'),
('Charlie Brown', 'charlie@example.com', 'hashed_password3', '5647382910', '1992-09-30', 200, 'bus'),
('David Smith', 'david@example.com', 'hashed_password4', '555444333', '1988-03-17', 50, 'car'),
('Eva Green', 'eva@example.com', 'hashed_password5', '222333444', '1995-12-01', 75, 'plane');

-- Insertion des données pour la table Reservation
INSERT INTO reservation (client_id, num_passengers, destination_id, package_id, transport_id, accommodation_id, travel_date_from, travel_date_to, loyalty_points_generated, status) VALUES
(1, 2, 1, 1, 1, 1, '2024-12-01', '2024-12-07', 10, 'confirmed'),
(2, 1, 2, 2, 3, 2, '2024-12-02', '2024-12-06', 5, 'pending'),
(3, 3, 3, 3, 4, 3, '2024-12-03', '2024-12-10', 20, 'completed'),
(4, 4, 4, 4, 5, 4, '2024-12-04', '2024-12-08', 15, 'cancelled'),
(5, 1, 5, 5, NULL, 5, '2024-12-05', '2024-12-09', 8, 'pending');

-- Insertion des données pour la table Passenger
INSERT INTO passenger (reservation_id, full_name, email, seat_preference) VALUES
(1, 'Alice Dupont', 'alice@example.com', 'Window'),
(1, 'John Doe', 'john@example.com', 'Aisle'),
(2, 'Bob Martin', 'bob@example.com', 'Middle'),
(3, 'Charlie Brown', 'charlie@example.com', 'Window'),
(4, 'David Smith', 'david@example.com', 'Aisle');

-- Insertion des données pour la table Payment
INSERT INTO payment (reservation_id, amount, payment_method, payment_status) VALUES
(1, 900.00, 'credit_card', 'completed'),
(2, 720.00, 'bank_transfer', 'pending'),
(3, 1400.00, 'credit_card', 'completed'),
(4, 1400.00, 'credit_card', 'refunded'),
(5, 640.00, 'bank_transfer', 'pending');

-- Insertion des données pour la table Comments
INSERT INTO comments (client_id, package_id, transport_id, accommodation_id, rating, comments) VALUES
(1, 1, 1, 1, 5, 'Amazing trip to Paris!'),
(2, 2, 3, 2, 4, 'Enjoyed my stay in New York.'),
(3, 3, 4, 3, 5, 'Tokyo was incredible!'),
(4, 4, NULL, 4, 3, 'Rome was good, but I expected more.'),
(5, 5, NULL, 5, 5, 'Sydney was an unforgettable adventure!');