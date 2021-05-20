DROP TABLE IF EXISTS phpauth_config;
CREATE TABLE phpauth_config (
                              id serial NOT NULL,
                              setting VARCHAR(100) NOT NULL,
                              value VARCHAR(100) DEFAULT NULL,
                              PRIMARY KEY (id),
                              UNIQUE (setting)
);

INSERT INTO phpauth_config (setting, value) VALUES
('attack_mitigation_time',  '+30 minutes'),
('attempts_before_ban', '30'),
('attempts_before_verify',  '5'),
('bcrypt_cost', '10'),
('cookie_domain', NULL),
('cookie_forget', '+30 minutes'),
('cookie_http', '0'),
('cookie_name', 'phpauth_session_cookie'),
('cookie_path', '/'),
('cookie_remember', '+1 month'),
('cookie_secure', '0'),
('cookie_renew', '+5 minutes'),
('allow_concurrent_sessions', FALSE),
('emailmessage_suppress_activation',  '0'),
('emailmessage_suppress_reset', '0'),
('mail_charset','UTF-8'),
('password_min_score',  '3'),
('site_activation_page',  'activate'),
('site_activation_page_append_code', '0'),
('site_email',  'no-reply@phpauth.cuonic.com'),
('site_key',  'fghuior.)/!/jdUkd8s2!7HVHG7777ghg'),
('site_name', 'PHPAuth'),
('site_password_reset_page',  'reset'),
('site_password_reset_page_append_code',  '0'),
('site_timezone', 'Europe/Paris'),
('site_url',  'https://github.com/PHPAuth/PHPAuth'),
('site_language', 'en_GB'),
('smtp',  '0'),
('smtp_debug',  '0'),
('smtp_auth', '1'),
('smtp_host', 'smtp.example.com'),
('smtp_password', 'password'),
('smtp_port', '25'),
('smtp_security', NULL),
('smtp_username', 'email@example.com'),
('table_attempts',  'phpauth_attempts'),
('table_requests',  'phpauth_requests'),
('table_sessions',  'phpauth_sessions'),
('table_users', 'phpauth_users'),
('table_emails_banned', 'phpauth_emails_banned'),
('table_translations', 'phpauth_translation_dictionary'),
('verify_email_max_length', '100'),
('verify_email_min_length', '5'),
('verify_email_use_banlist',  '1'),
('verify_password_min_length',  '3'),
('request_key_expiration', '+10 minutes'),
('translation_source', 'php'),
('recaptcha_enabled', 0),
('recaptcha_site_key', ''),
('recaptcha_secret_key', ''),
('custom_datetime_format', 'Y-m-d H:i');

DROP TABLE IF EXISTS phpauth_attempts;
CREATE TABLE phpauth_attempts (
                                id serial NOT NULL,
                                ip character(39) NOT NULL,
                                expiredate timestamp without time zone NOT NULL,
                                PRIMARY KEY (id)
);

CREATE TYPE request_type AS ENUM('activation','reset');

DROP TABLE IF EXISTS phpauth_requests;
CREATE TABLE phpauth_requests (
                                id serial NOT NULL,
                                uid integer NOT NULL,
                                token character (20) NOT NULL,
                                expire timestamp without time zone NOT NULL,
                                type request_type NOT NULL,
                                PRIMARY KEY (id)
);

DROP TABLE IF EXISTS phpauth_sessions;
CREATE TABLE phpauth_sessions (
                                id serial NOT NULL,
                                uid integer NOT NULL,
                                hash character (40) NOT NULL,
                                expiredate timestamp without time zone NOT NULL,
                                ip VARCHAR(39) NOT NULL,
                                device_id VARCHAR(36) DEFAULT NULL,
                                agent VARCHAR(200) NOT NULL,
                                cookie_crc character (40) NOT NULL,
                                PRIMARY KEY (id)
);

DROP TABLE IF EXISTS phpauth_users;
CREATE TABLE phpauth_users (
                             id serial NOT NULL,
                             email VARCHAR(100) DEFAULT NULL,
                             password VARCHAR(255) DEFAULT NULL,
                             isactive smallint NOT NULL DEFAULT '0',
                             dt timestamp without time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
                             is_retired SMALLINT NOT NULL DEFAULT 0,
                             PRIMARY KEY (id)
);

DROP TABLE IF EXISTS phpauth_emails_banned;
CREATE TABLE phpauth_emails_banned (
                                     id serial NOT NULL,
                                     domain VARCHAR(100) DEFAULT NULL,
                                     PRIMARY KEY (id)
);

-- app tables --
DROP TABLE IF EXISTS products CASCADE;
CREATE TABLE products(
                       id serial PRIMARY KEY,
                       name VARCHAR(255) NOT NULL,
                       price INT,
                       UNIQUE (name)
);

DROP TABLE IF EXISTS categories CASCADE;
CREATE TABLE categories (
                          id SERIAL PRIMARY KEY,
                          name VARCHAR(255) NOT NULL,
                          description TEXT NOT NULL,
                          UNIQUE (name)
);

DROP TABLE IF EXISTS product_categories;
CREATE TABLE product_categories (
                                  id serial PRIMARY KEY,
                                  product_id INT NOT NULL REFERENCES products(id) ON UPDATE RESTRICT ON DELETE CASCADE ,
                                  category_id INT NOT NULL REFERENCES categories(id) ON UPDATE RESTRICT ON DELETE CASCADE,
                                  UNIQUE (product_id, category_id)
);

-- заказы пользователей --
DROP TABLE IF EXISTS orders CASCADE;
CREATE TABLE orders (
                      id SERIAL PRIMARY KEY,
                      user_id INT NOT NULL REFERENCES phpauth_users(id) ON DELETE CASCADE,
                      total INT NOT NULL DEFAULT 0,
                      created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- товары в заказе --
DROP TABLE IF EXISTS order_products CASCADE;
CREATE TABLE order_products (
                              id SERIAL PRIMARY KEY,
                              order_id INT NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
                              product_id INT NOT NULL REFERENCES products(id) ON DELETE CASCADE,
                              cnt SMALLINT NOT NULL DEFAULT 1
);

INSERT INTO "categories" (name, description) VALUES
('Книги', 'Книги'), ('Обувь', 'Мужская обувь'), ('Авто', 'Автомобили');
INSERT INTO "products" (name, price) VALUES
('Совершеный код',3300), ('Фиолетовая коров', 500), ('Ботинки Байкал', 9000), ('Hyundai Solaris', 500000);

INSERT INTO product_categories (product_id, category_id) VALUES (1, 1), (2, 1), (3, 2), (4, 3);

INSERT INTO phpauth_users (email, password, isactive, is_retired) VALUES ('test@test.com', 'i38RrNkNfYQIOOV0mHbAMrXLMUYJ0a46tufqoUMGktWYwC5OkqiJnBzaqiAbDVP9VZ5c6zcqPW9WNfmt1bwS49YzgoMbbgCUbHi6ZjRsTT4zrwXeTCnkYSEiAeCiOH1nDULtSvjY6hsjlYIl5MzLE7QkGBlTTjaNUXpw00A9FsIwtfM1espf8FC79mgWvaQMqjdPnJiB5I7wb12nzdRXF0YHziSC5uLiu9wS12NezrKnuxxGI8k42cAmEoQLMue', 1, 0);
INSERT INTO orders(user_id, total) VALUES (1, 3800);
INSERT INTO order_products (order_id, product_id) VALUES (1, 1), (1, 2);
