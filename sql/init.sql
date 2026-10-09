-- ============================================================
-- RGR FRANCE — Base de données MySQL 8+
-- ============================================================

CREATE DATABASE IF NOT EXISTS rgr_france
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE rgr_france;

-- ------------------------------------------------------------
-- INFORMATIONS RGR FRANCE
-- ------------------------------------------------------------

CREATE TABLE company_information (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    company_name VARCHAR(150) NOT NULL,
    legal_form VARCHAR(100) NULL,
    siren VARCHAR(20) NULL,

    phone VARCHAR(30) NOT NULL,
    email VARCHAR(190) NOT NULL,

    address_line VARCHAR(255) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    city VARCHAR(120) NOT NULL,
    country VARCHAR(100) NOT NULL DEFAULT 'France',

    google_maps_url VARCHAR(500) NULL,

    facebook_url VARCHAR(500) NULL,
    instagram_url VARCHAR(500) NULL,
    linkedin_url VARCHAR(500) NULL,
    youtube_url VARCHAR(500) NULL,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO company_information (
    company_name,
    legal_form,
    siren,
    phone,
    email,
    address_line,
    postal_code,
    city,
    country,
    google_maps_url,
    facebook_url,
    instagram_url,
    linkedin_url,
    youtube_url
) VALUES (
    'RGR France',
    'SAS',
    '999227572',
    '0749848045',
    'contact@rgrfrance.fr',
    '19 rue des Fours Banaux',
    '89690',
    'Chéroy',
    'France',
    'https://www.google.com/maps/search/?api=1&query=19+rue+des+Fours+Banaux+89690+Chéroy',
    'https://www.facebook.com/RGRofficiel',
    'https://www.instagram.com/rgr_inst/',
    'https://www.linkedin.com/in/rgr-france',
    'https://www.youtube.com/@RGR_officiel'
);
-- ------------------------------------------------------------
-- HEURES D’OUVERTURE
-- ------------------------------------------------------------

CREATE TABLE opening_hours (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    company_information_id INT UNSIGNED NOT NULL,
    day_number TINYINT UNSIGNED NOT NULL,
    day_name VARCHAR(20) NOT NULL,

    morning_open TIME NULL,
    morning_close TIME NULL,
    afternoon_open TIME NULL,
    afternoon_close TIME NULL,

    is_closed BOOLEAN NOT NULL DEFAULT FALSE,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_opening_hours_company
        FOREIGN KEY (company_information_id)
        REFERENCES company_information(id)
        ON DELETE CASCADE,

    CONSTRAINT uq_company_day
        UNIQUE (company_information_id, day_number),

    CONSTRAINT chk_day_number
        CHECK (day_number BETWEEN 1 AND 7)
);

INSERT INTO opening_hours (
    company_information_id,
    day_number,
    day_name,
    morning_open,
    morning_close,
    afternoon_open,
    afternoon_close,
    is_closed
) VALUES
(1, 1, 'Lundi',    '09:00:00', '12:00:00', '14:00:00', '18:00:00', FALSE),
(1, 2, 'Mardi',    '09:00:00', '12:00:00', '14:00:00', '18:00:00', FALSE),
(1, 3, 'Mercredi', '09:00:00', '12:00:00', '14:00:00', '18:00:00', FALSE),
(1, 4, 'Jeudi',    '09:00:00', '12:00:00', '14:00:00', '18:00:00', FALSE),
(1, 5, 'Vendredi', '09:00:00', '12:00:00', '14:00:00', '18:00:00', FALSE),
(1, 6, 'Samedi',   NULL,       NULL,       NULL,       NULL,       TRUE),
(1, 7, 'Dimanche', NULL,       NULL,       NULL,       NULL,       TRUE);
-- ------------------------------------------------------------
-- COMPTES INTERNES
-- ------------------------------------------------------------

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NULL,
    last_name VARCHAR(100) NULL,
    email VARCHAR(190) NULL,
    username VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('ADMIN', 'EMPLOYEE') NOT NULL DEFAULT 'EMPLOYEE',
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    last_login_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT uq_users_email UNIQUE (email),
    CONSTRAINT uq_users_username UNIQUE (username),
    INDEX idx_users_role_active (role, is_active)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- STATUTS DES VÉHICULES
-- Les statuts sont dans une table séparée afin de pouvoir en
-- ajouter ultérieurement sans modifier la structure de vehicles.
-- ------------------------------------------------------------

CREATE TABLE vehicle_statuses (
    id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL,
    label VARCHAR(100) NOT NULL,
    display_order TINYINT UNSIGNED NOT NULL DEFAULT 0,
    is_public BOOLEAN NOT NULL DEFAULT TRUE,

    CONSTRAINT uq_vehicle_statuses_code UNIQUE (code)
) ENGINE=InnoDB;

INSERT INTO vehicle_statuses (id, code, label, display_order, is_public) VALUES
    (1, 'AVAILABLE_FOR_SALE', 'Disponible à l’achat', 1, TRUE),
    (2, 'IN_HOMOLOGATION', 'En cours d’homologation', 2, TRUE),
    (3, 'SERVICE_SCHEDULED', 'Service programmé', 3, TRUE),
    (4, 'RETURNED', 'Véhicule rendu', 4, TRUE);

-- ------------------------------------------------------------
-- VÉHICULES
-- ------------------------------------------------------------

CREATE TABLE vehicles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    status_id TINYINT UNSIGNED NOT NULL DEFAULT 1,
    internal_reference VARCHAR(50) NOT NULL,
    vin VARCHAR(17) NULL,
    brand VARCHAR(100) NOT NULL,
    model VARCHAR(150) NOT NULL,
    trim_level VARCHAR(150) NULL,
    registration_number VARCHAR(20) NULL,
    registration_year YEAR NULL,
    mileage_km INT UNSIGNED NULL,
    fuel_type VARCHAR(50) NULL,
    transmission VARCHAR(50) NULL,
    power_hp SMALLINT UNSIGNED NULL,
    exterior_color VARCHAR(100) NULL,
    price DECIMAL(12, 2) NULL,
    description TEXT NULL,
    is_published BOOLEAN NOT NULL DEFAULT FALSE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT uq_vehicles_reference UNIQUE (internal_reference),
    CONSTRAINT uq_vehicles_vin UNIQUE (vin),
    CONSTRAINT uq_vehicles_registration UNIQUE (registration_number),
    CONSTRAINT fk_vehicles_status
        FOREIGN KEY (status_id)
        REFERENCES vehicle_statuses (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    INDEX idx_vehicles_status_published (status_id, is_published),
    INDEX idx_vehicles_brand_model (brand, model)
) ENGINE=InnoDB;

CREATE TABLE vehicle_images (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vehicle_id BIGINT UNSIGNED NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    alt_text VARCHAR(255) NULL,
    display_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    is_cover BOOLEAN NOT NULL DEFAULT FALSE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_vehicle_images_vehicle
        FOREIGN KEY (vehicle_id)
        REFERENCES vehicles (id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    INDEX idx_vehicle_images_order (vehicle_id, display_order)
) ENGINE=InnoDB;

CREATE TABLE vehicle_status_history (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vehicle_id BIGINT UNSIGNED NOT NULL,
    previous_status_id TINYINT UNSIGNED NULL,
    new_status_id TINYINT UNSIGNED NOT NULL,
    changed_by_user_id BIGINT UNSIGNED NULL,
    note TEXT NULL,
    changed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_vehicle_history_vehicle
        FOREIGN KEY (vehicle_id)
        REFERENCES vehicles (id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT fk_vehicle_history_previous_status
        FOREIGN KEY (previous_status_id)
        REFERENCES vehicle_statuses (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_vehicle_history_new_status
        FOREIGN KEY (new_status_id)
        REFERENCES vehicle_statuses (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_vehicle_history_user
        FOREIGN KEY (changed_by_user_id)
        REFERENCES users (id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    INDEX idx_vehicle_history_vehicle_date (vehicle_id, changed_at)
) ENGINE=InnoDB;

-- ============================================================
-- RGR FRANCE — VÉHICULES DE DÉMONSTRATION
-- Script réexécutable : les véhicules sont mis à jour grâce à
-- leur référence interne unique et les images ne sont pas dupliquées.
-- ============================================================

START TRANSACTION;

INSERT INTO vehicles (
    status_id,
    internal_reference,
    vin,
    brand,
    model,
    trim_level,
    registration_number,
    registration_year,
    mileage_km,
    fuel_type,
    transmission,
    power_hp,
    exterior_color,
    price,
    description,
    is_published
) VALUES
(
    1,
    'RGR-F150-RAPTOR-40TH-2018',
    NULL,
    'Ford',
    'F-150 Raptor',
    '40e Anniversary · Pack 802A · Technology Package',
    NULL,
    2018,
    150000,
    'Essence',
    'Automatique',
    450,
    'Lightning Blue',
    65000.00,
    'Un Raptor particulièrement complet équipé de la sellerie spéciale 40e Anniversary, du Pack 802A, du Technology Package et de jantes Forged de 17 pouces. Son historique Ford américain est documenté et le véhicule est proposé sur commande.',
    TRUE
),
(
    1,
    'RGR-MUSTANG-GT-CONVERTIBLE-2021',
    '1FATP8FF8M5123112',
    'Ford',
    'Mustang GT Convertible',
    'V8 5.0L · Import Floride',
    NULL,
    2021,
    80000,
    'Essence',
    NULL,
    460,
    'Orange Fury',
    50000.00,
    'Mustang GT cabriolet importée de Floride avec historique complet, entretien à jour et documents d’importation et de conformité. Le véhicule dispose notamment de la mémorisation des sièges. Malus et carte grise non compris.',
    TRUE
),
(
    1,
    'RGR-F150-XLT-E85-2016',
    '1FTEW1EF2GKF00425',
    'Ford',
    'F-150 XLT',
    'V8 5.0L FlexFuel E85 · Réservoir 120L',
    NULL,
    2016,
    147000,
    'FlexFuel E85',
    'Automatique',
    385,
    'Bleu',
    38500.00,
    'F-150 XLT V8 FlexFuel provenant du Canada, doté d’un réservoir de 120 litres et d’un historique d’entretien Ford. Il bénéficie de nombreux travaux récents et dispose notamment d’un attelage renforcé, d’un couvre-benne rigide et d’une dashcam.',
    TRUE
),
(
    1,
    'RGR-SHELBY-GT350-2016',
    '1FA6P8JZXG5522672',
    'Ford',
    'Mustang Shelby GT350',
    'V8 5.2L · Exemplaire signé Aaron Shelby',
    NULL,
    2016,
    32000,
    'Essence',
    'Manuelle',
    526,
    'Blanc',
    92000.00,
    'Shelby GT350 importée de Floride et accompagnée d’un historique complet. Cet exemplaire se distingue par une signature d’Aaron Shelby authentifiée par des photos et vidéos, ainsi que par un dossier d’expertise, d’importation et de conformité.',
    TRUE
)
ON DUPLICATE KEY UPDATE
    status_id = VALUES(status_id),
    vin = VALUES(vin),
    brand = VALUES(brand),
    model = VALUES(model),
    trim_level = VALUES(trim_level),
    registration_year = VALUES(registration_year),
    mileage_km = VALUES(mileage_km),
    fuel_type = VALUES(fuel_type),
    transmission = VALUES(transmission),
    power_hp = VALUES(power_hp),
    exterior_color = VALUES(exterior_color),
    price = VALUES(price),
    description = VALUES(description),
    is_published = VALUES(is_published);

INSERT INTO vehicle_images (
    vehicle_id,
    file_path,
    alt_text,
    display_order,
    is_cover
)
SELECT
    vehicle.id,
    image_data.file_path,
    image_data.alt_text,
    1,
    TRUE
FROM (
    SELECT
        'RGR-F150-RAPTOR-40TH-2018' AS internal_reference,
        'https://rgrfrance.fr/wp-content/uploads/2026/07/Copilot_20260717_155318.png' AS file_path,
        'Ford F-150 Raptor Lightning Blue dans un environnement montagneux' AS alt_text
    UNION ALL
    SELECT
        'RGR-MUSTANG-GT-CONVERTIBLE-2021',
        'https://rgrfrance.fr/wp-content/uploads/2026/07/Copilot_20260710_133824.png',
        'Ford Mustang GT Convertible Orange Fury vue de face'
    UNION ALL
    SELECT
        'RGR-F150-XLT-E85-2016',
        'https://rgrfrance.fr/wp-content/uploads/2026/06/F150-RAYMON.jpg',
        'Ford F-150 XLT bleu photographié de trois quarts avant'
    UNION ALL
    SELECT
        'RGR-SHELBY-GT350-2016',
        'https://rgrfrance.fr/wp-content/uploads/2026/06/Shelby-Delicato.jpg',
        'Ford Mustang Shelby GT350 blanche avec bandes bleues'
) AS image_data
INNER JOIN vehicles AS vehicle
    ON vehicle.internal_reference = image_data.internal_reference
WHERE NOT EXISTS (
    SELECT 1
    FROM vehicle_images AS existing_image
    WHERE existing_image.vehicle_id = vehicle.id
      AND existing_image.file_path = image_data.file_path
);

COMMIT;


-- ------------------------------------------------------------
-- SERVICES
-- Les 16 prestations du site actuel sont enregistrées ici une
-- seule fois et ne sont pas recopiées dans products.
-- ------------------------------------------------------------

CREATE TABLE services (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(190) NOT NULL,
    slug VARCHAR(190) NOT NULL,
    description TEXT NULL,
    regular_price DECIMAL(10, 2) NOT NULL,
    promotional_price DECIMAL(10, 2) NULL,
    image_path VARCHAR(500) NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    display_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT uq_services_name UNIQUE (name),
    CONSTRAINT uq_services_slug UNIQUE (slug),
    CONSTRAINT chk_services_regular_price CHECK (regular_price >= 0),
    CONSTRAINT chk_services_promotional_price CHECK (
        promotional_price IS NULL
        OR promotional_price >= 0
    ),

    INDEX idx_services_active_order (is_active, display_order)
) ENGINE=InnoDB;

INSERT INTO services
    (name, slug, regular_price, promotional_price, display_order)
VALUES
    ('Francisation', 'francisation', 300.00, NULL, 1),
    ('Homologation', 'homologation', 5500.00, NULL, 2),
    ('Importation', 'importation', 1200.00, NULL, 3),
    ('Pack American Touch', 'pack-american-touch', 500.00, NULL, 4),
    ('Pack Android Auto / CarPlay sans fil', 'pack-android-auto-carplay-sans-fil', 150.00, NULL, 5),
    ('Pack compteur numérique', 'pack-compteur-numerique', 350.00, NULL, 6),
    ('Pack démarrage à distance', 'pack-demarrage-a-distance', 600.00, 500.00, 7),
    ('Pack Full', 'pack-full', 950.00, NULL, 8),
    ('Pack mise à jour', 'pack-mise-a-jour', 250.00, NULL, 9),
    ('Pack Modding Sync3', 'pack-modding-sync3', 250.00, NULL, 10),
    ('Pack Stand', 'pack-stand', 250.00, NULL, 11),
    ('Pack Sync2 / Sync3', 'pack-sync2-sync3', 1200.00, NULL, 12),
    ('Pack TPMS EU/US', 'pack-tpms-eu-us', 80.00, NULL, 13),
    ('Remise en conformité', 'remise-en-conformite', 250.00, NULL, 14),
    ('Réparation électronique', 'reparation-electronique', 250.00, NULL, 15),
    ('Réparation Sync2 ou 3', 'reparation-sync2-ou-3', 250.00, NULL, 16);

-- ------------------------------------------------------------
-- PRODUITS MATÉRIELS
-- Uniquement les 5 éléments qui ne figurent pas dans Services.
-- ------------------------------------------------------------

CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(190) NOT NULL,
    slug VARCHAR(190) NOT NULL,
    sku VARCHAR(100) NULL,
    description TEXT NULL,
    regular_price DECIMAL(10, 2) NOT NULL,
    promotional_price DECIMAL(10, 2) NULL,
    stock_quantity INT UNSIGNED NOT NULL DEFAULT 0,
    image_path VARCHAR(500) NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    display_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT uq_products_name UNIQUE (name),
    CONSTRAINT uq_products_slug UNIQUE (slug),
    CONSTRAINT uq_products_sku UNIQUE (sku),
    CONSTRAINT chk_products_regular_price CHECK (regular_price >= 0),
    CONSTRAINT chk_products_promotional_price CHECK (
        promotional_price IS NULL
        OR promotional_price >= 0
    ),

    INDEX idx_products_active_order (is_active, display_order)
) ENGINE=InnoDB;

INSERT INTO products
    (name, slug, regular_price, stock_quantity, display_order)
VALUES
    ('Feux arrière F150', 'feux-arriere-f150', 680.00, 0, 1),
    ('Feux arrière Mustang', 'feux-arriere-mustang', 900.00, 0, 2),
    ('Feux avant F150', 'feux-avant-f150', 780.00, 0, 3),
    ('Pack fermeture vitres et rétros Mustang', 'pack-fermeture-vitres-retros-mustang', 250.00, 0, 4),
    ('Pack Three Bars Mustang', 'pack-three-bars-mustang', 1050.00, 0, 5);

-- ------------------------------------------------------------
-- INTERVENTIONS / SERVICES PROGRAMMÉS SUR LES VÉHICULES
-- ------------------------------------------------------------

CREATE TABLE vehicle_services (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vehicle_id BIGINT UNSIGNED NOT NULL,
    service_id BIGINT UNSIGNED NOT NULL,
    assigned_employee_id BIGINT UNSIGNED NULL,
    status ENUM(
        'PENDING',
        'SCHEDULED',
        'IN_PROGRESS',
        'COMPLETED',
        'CANCELLED'
    ) NOT NULL DEFAULT 'PENDING',
    scheduled_at DATETIME NULL,
    started_at DATETIME NULL,
    completed_at DATETIME NULL,
    agreed_price DECIMAL(10, 2) NULL,
    notes TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_vehicle_services_vehicle
        FOREIGN KEY (vehicle_id)
        REFERENCES vehicles (id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT fk_vehicle_services_service
        FOREIGN KEY (service_id)
        REFERENCES services (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_vehicle_services_employee
        FOREIGN KEY (assigned_employee_id)
        REFERENCES users (id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,
    CONSTRAINT chk_vehicle_services_price CHECK (
        agreed_price IS NULL
        OR agreed_price >= 0
    ),

    INDEX idx_vehicle_services_vehicle_status (vehicle_id, status),
    INDEX idx_vehicle_services_employee_date (assigned_employee_id, scheduled_at)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- DEMANDES DE CONTACT
-- ------------------------------------------------------------

CREATE TABLE contact_requests (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    assigned_employee_id BIGINT UNSIGNED NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL,
    phone VARCHAR(30) NULL,
    postal_address VARCHAR(255) NULL,
    postal_code VARCHAR(20) NULL,
    city VARCHAR(120) NULL,
    message TEXT NOT NULL,
    status ENUM('NEW', 'IN_PROGRESS', 'CLOSED', 'ARCHIVED')
        NOT NULL DEFAULT 'NEW',
    privacy_consent BOOLEAN NOT NULL DEFAULT FALSE,
    processed_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_contact_requests_employee
        FOREIGN KEY (assigned_employee_id)
        REFERENCES users (id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    INDEX idx_contact_requests_status_date (status, created_at),
    INDEX idx_contact_requests_email (email),
    INDEX idx_contact_requests_assigned (assigned_employee_id, status)
) ENGINE=InnoDB;

-- ============================================================
-- IMPORTANT
-- Aucun compte n'est créé par ce script afin de ne pas stocker
-- de mot de passe d'exemple. Le premier administrateur devra être
-- créé avec un hash produit par password_hash() en PHP.
-- ============================================================
