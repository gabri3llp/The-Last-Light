
CREATE DATABASE IF NOT EXISTS The_Last_Light;
USE The_Last_Light;

CREATE TABLE categories (
    category_id   INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(30) NOT NULL UNIQUE
);

INSERT INTO categories (category_name) VALUES
    ('Medicine'), ('Food'), ('Weapons'), ('Shelter'),
    ('Materials'), ('Services'), ('Quests');

CREATE TABLE users (
    user_id       INT AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(30) NOT NULL UNIQUE,
    settlement    VARCHAR(50) NOT NULL,
    email         VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role          ENUM('survivor', 'merchant', 'admin') NOT NULL DEFAULT 'survivor',
    trust_rating  DECIMAL(2,1) NOT NULL DEFAULT 3.0,
    profile_img   VARCHAR(255) DEFAULT 'default.jpg',
    joined_at     DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE notices (
    notice_id     INT AUTO_INCREMENT PRIMARY KEY,
    user_id       INT NOT NULL,
    category_id   INT NOT NULL,
    title         VARCHAR(120) NOT NULL,
    description   TEXT NOT NULL,
    trade_value   VARCHAR(50),
    settlement    VARCHAR(50) NOT NULL,
    rarity        ENUM('common', 'uncommon', 'rare', 'epic', 'legendary', 'mythic')
                  NOT NULL DEFAULT 'common',
    status        ENUM('active', 'expired') NOT NULL DEFAULT 'active',
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)     REFERENCES users(user_id),
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

CREATE TABLE comments (
    comment_id  INT AUTO_INCREMENT PRIMARY KEY,
    notice_id   INT NOT NULL,
    user_id     INT NOT NULL,
    comment     TEXT NOT NULL,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (notice_id) REFERENCES notices(notice_id),
    FOREIGN KEY (user_id)   REFERENCES users(user_id)
);