CREATE DATABASE IF NOT EXISTS todo_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE todo_app;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_users_username (username)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS posts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(180) NOT NULL,
    slug VARCHAR(220) NOT NULL UNIQUE,
    excerpt VARCHAR(320) NOT NULL,
    content MEDIUMTEXT NOT NULL,
    cover_image_url VARCHAR(255) DEFAULT NULL,
    status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    published_at DATETIME DEFAULT NULL,
    INDEX idx_posts_status_published_at (status, published_at),
    INDEX idx_posts_featured_published (is_featured, published_at),
    INDEX idx_posts_created_at (created_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tasks (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description MEDIUMTEXT NOT NULL,
    status ENUM('pending', 'done') NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL,
    INDEX idx_tasks_status_created_at (status, created_at)
) ENGINE=InnoDB;

INSERT INTO posts (title, slug, excerpt, content, cover_image_url, status, is_featured, created_at, updated_at, published_at)
VALUES
(
    'Novy editorialny web s premium dizajnom',
    'novy-editorialny-web-s-premium-dizajnom',
    'Verejna homepage s admin panelom, dynamickymi sekciami a bezpecnym pristupom pre administraciu.',
    'Tento projekt je postaveny ako moderny verejny web s oddelenou administraciou. Návštevníci vidia obsah bez prihlasenia, kym admin spravuje články cez session protected rozhranie.',
    'https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=1200&q=80',
    'published',
    1,
    NOW(),
    NOW(),
    NOW()
),
(
    'Prehľadna sprava obsahu',
    'prehladna-sprava-obsahu',
    'Admin panel umoznuje publikovat, schovat a oznacit prispevok ako featured.',
    'Kazdy prispevok obsahuje titulok, perex, plny obsah, URL obrazka a stav publikovania. To vytvara viac dynamickej casti na stranke bez potreby frameworku.',
    'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1200&q=80',
    'published',
    1,
    NOW(),
    NOW(),
    NOW()
),
(
    'Bezpecnost a session autorizačny flow',
    'bezpecnost-a-session-autoriza-cny-flow',
    'Hashovanie hesiel, CSRF tokeny a HttpOnly cookie chránia admin vstup.',
    'Autorizacia v admin rozhrani pouziva password_hash, password_verify, session cookies s Lax SameSite a CSRF ochranu pre POST akcie.',
    'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1200&q=80',
    'draft',
    0,
    NOW(),
    NOW(),
    NULL
)
ON DUPLICATE KEY UPDATE slug = slug;
