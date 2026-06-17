<?php
/**
 * Database Configuration
 * Update these values with your database credentials
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'security_incidents_db');

// Application Configuration
define('APP_NAME', 'Security Incident Reporting System');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/OpenSource_Assignment_Program_Group_9');

// Security Configuration
define('SESSION_TIMEOUT', 3600);
define('PASSWORD_MIN_LENGTH', 8);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_DURATION', 900);

// File Upload Configuration
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 5242880);
define('ALLOWED_FILE_TYPES', ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'txt', 'zip']);

// Email Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-email-password');
define('FROM_EMAIL', 'noreply@university-of-dodoma.ac.tz');

// Debug Mode
define('DEBUG_MODE', false);
define('LOG_DIR', __DIR__ . '/../logs/');

// Pagination
define('ITEMS_PER_PAGE', 20);

// Date Format
define('DATE_FORMAT', 'Y-m-d');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');

// Error Reporting
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}

// Timezone
date_default_timezone_set('Africa/Dar_es_Salaam');
