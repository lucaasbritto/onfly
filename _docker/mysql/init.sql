CREATE DATABASE IF NOT EXISTS laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS laravel_testing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

GRANT ALL PRIVILEGES ON laravel.* TO 'laravel'@'%';
GRANT ALL PRIVILEGES ON laravel_testing.* TO 'laravel'@'%';
FLUSH PRIVILEGES;