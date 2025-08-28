<?php
class Database {
    private static $instance = null;
    private $connection;
    
    private $host = 'localhost';
    private $username = 'mohman_admin';
    private $password = '@Amirmm21671381';
    private $database = 'mohman_survey';
    
    private function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host={$this->host};dbname={$this->database};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_PERSISTENT => false,
                    PDO::ATTR_TIMEOUT => 5,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
                    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
                ]
            );
            
            // Set timezone
            $this->connection->exec("SET time_zone = '+00:00'");
            
        } catch(PDOException $e) {
            // Log error for debugging but don't expose details in production
            error_log("Database connection failed: " . $e->getMessage());
            
            // Show user-friendly error page
            http_response_code(500);
            if (file_exists(__DIR__ . '/../error-pages/500.php')) {
                include __DIR__ . '/../error-pages/500.php';
            } else {
                die("Service temporarily unavailable. Please try again later.");
            }
            exit;
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            // Log the error with query details for debugging
            error_log("Database query error: " . $e->getMessage() . " | SQL: " . $sql);
            throw $e;
        }
    }
    
    public function fetch($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }
    
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
    
    // Prevent cloning and unserialization
    private function __clone() {}
    public function __wakeup() {}
}

// Initialize database tables
function initializeDatabase() {
    $db = Database::getInstance();
    
    // Users table
    $db->query("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) UNIQUE,
        phone VARCHAR(20) UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'creator') NOT NULL DEFAULT 'creator',
        subscription_level INT DEFAULT 1,
        credits INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_email (email),
        INDEX idx_phone (phone),
        INDEX idx_role (role)
    )");
    
    // Surveys table
    $db->query("CREATE TABLE IF NOT EXISTS surveys (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        survey_id VARCHAR(16) UNIQUE NOT NULL,
        theme_id INT,
        custom_theme_data JSON,
        status ENUM('draft', 'active', 'closed') DEFAULT 'draft',
        allow_anonymous BOOLEAN DEFAULT TRUE,
        show_progress_bar BOOLEAN DEFAULT TRUE,
        published_at TIMESTAMP NULL,
        closed_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_survey_id (survey_id),
        INDEX idx_user_id (user_id),
        INDEX idx_status (status)
    )");
    
    // Survey questions table
    $db->query("CREATE TABLE IF NOT EXISTS survey_questions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        survey_id INT NOT NULL,
        question_text TEXT NOT NULL,
        question_type ENUM('text', 'textarea', 'radio', 'checkbox', 'dropdown') NOT NULL,
        options JSON,
        required BOOLEAN DEFAULT FALSE,
        order_index INT DEFAULT 0,
        FOREIGN KEY (survey_id) REFERENCES surveys(id) ON DELETE CASCADE,
        INDEX idx_survey_id (survey_id),
        INDEX idx_order (order_index)
    )");
    
    // Survey responses table
    $db->query("CREATE TABLE IF NOT EXISTS survey_responses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        survey_id INT NOT NULL,
        response_data JSON NOT NULL,
        ip_address VARCHAR(45),
        user_agent TEXT,
        submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (survey_id) REFERENCES surveys(id) ON DELETE CASCADE,
        INDEX idx_survey_id (survey_id),
        INDEX idx_submitted_at (submitted_at)
    )");
    
    // Predefined themes table
    $db->query("CREATE TABLE IF NOT EXISTS predefined_themes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        css_variables JSON NOT NULL,
        min_subscription_level INT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Custom themes table
    $db->query("CREATE TABLE IF NOT EXISTS custom_themes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        css_variables JSON NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_user_id (user_id)
    )");
    
    // Credit transactions table
    $db->query("CREATE TABLE IF NOT EXISTS credit_transactions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        transaction_type ENUM('add', 'spend', 'reserve', 'release') NOT NULL,
        amount INT NOT NULL,
        balance_after INT NOT NULL,
        description TEXT,
        survey_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (survey_id) REFERENCES surveys(id) ON DELETE SET NULL,
        INDEX idx_user_id (user_id),
        INDEX idx_transaction_type (transaction_type),
        INDEX idx_created_at (created_at)
    )");
    
    // Sessions table
    $db->query("CREATE TABLE IF NOT EXISTS sessions (
        id VARCHAR(128) PRIMARY KEY,
        user_id INT NOT NULL,
        data TEXT,
        expires_at TIMESTAMP NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_user_id (user_id),
        INDEX idx_expires_at (expires_at)
    )");
    
    // Insert default predefined themes
    insertDefaultThemes();
    
    // Add missing columns to existing tables (graceful upgrades)
    addMissingColumns();
}

function addMissingColumns() {
    $db = Database::getInstance();
    
    try {
        // Check and add allow_anonymous column to surveys table
        $result = $db->query("SHOW COLUMNS FROM surveys LIKE 'allow_anonymous'");
        if ($result->rowCount() == 0) {
            $db->query("ALTER TABLE surveys ADD COLUMN allow_anonymous BOOLEAN DEFAULT TRUE AFTER status");
        }
        
        // Check and add show_progress_bar column to surveys table
        $result = $db->query("SHOW COLUMNS FROM surveys LIKE 'show_progress_bar'");
        if ($result->rowCount() == 0) {
            $db->query("ALTER TABLE surveys ADD COLUMN show_progress_bar BOOLEAN DEFAULT TRUE AFTER allow_anonymous");
        }
        
        // Check and add published_at column to surveys table
        $result = $db->query("SHOW COLUMNS FROM surveys LIKE 'published_at'");
        if ($result->rowCount() == 0) {
            $db->query("ALTER TABLE surveys ADD COLUMN published_at TIMESTAMP NULL AFTER show_progress_bar");
        }
        
        // Check and add closed_at column to surveys table
        $result = $db->query("SHOW COLUMNS FROM surveys LIKE 'closed_at'");
        if ($result->rowCount() == 0) {
            $db->query("ALTER TABLE surveys ADD COLUMN closed_at TIMESTAMP NULL AFTER published_at");
        }
        
    } catch (PDOException $e) {
        // Log the error but don't stop execution
        error_log("Error adding missing columns: " . $e->getMessage());
    }
}

function insertDefaultThemes() {
    $db = Database::getInstance();
    
    $themes = [
        [
            'name' => 'Default',
            'css_variables' => json_encode([
                '--primary-color' => '#007bff',
                '--secondary-color' => '#6c757d',
                '--success-color' => '#28a745',
                '--danger-color' => '#dc3545',
                '--warning-color' => '#ffc107',
                '--info-color' => '#17a2b8',
                '--light-color' => '#f8f9fa',
                '--dark-color' => '#343a40',
                '--background-color' => '#ffffff',
                '--text-color' => '#212529',
                '--border-color' => '#dee2e6'
            ]),
            'min_subscription_level' => 1
        ],
        [
            'name' => 'Cosmic',
            'css_variables' => json_encode([
                '--primary-color' => '#8B5CF6',
                '--secondary-color' => '#A78BFA',
                '--success-color' => '#10B981',
                '--danger-color' => '#EF4444',
                '--warning-color' => '#F59E0B',
                '--info-color' => '#06B6D4',
                '--light-color' => '#F3F4F6',
                '--dark-color' => '#1F2937',
                '--background-color' => '#111827',
                '--text-color' => '#F9FAFB',
                '--border-color' => '#374151'
            ]),
            'min_subscription_level' => 2
        ],
        [
            'name' => 'Dark',
            'css_variables' => json_encode([
                '--primary-color' => '#0d6efd',
                '--secondary-color' => '#6c757d',
                '--success-color' => '#198754',
                '--danger-color' => '#dc3545',
                '--warning-color' => '#ffc107',
                '--info-color' => '#0dcaf0',
                '--light-color' => '#495057',
                '--dark-color' => '#212529',
                '--background-color' => '#212529',
                '--text-color' => '#ffffff',
                '--border-color' => '#495057'
            ]),
            'min_subscription_level' => 2
        ],
        [
            'name' => 'Green',
            'css_variables' => json_encode([
                '--primary-color' => '#28a745',
                '--secondary-color' => '#20c997',
                '--success-color' => '#28a745',
                '--danger-color' => '#dc3545',
                '--warning-color' => '#ffc107',
                '--info-color' => '#17a2b8',
                '--light-color' => '#f8f9fa',
                '--dark-color' => '#155724',
                '--background-color' => '#f0fff4',
                '--text-color' => '#155724',
                '--border-color' => '#c3e6cb'
            ]),
            'min_subscription_level' => 2
        ],
        [
            'name' => 'Blue',
            'css_variables' => json_encode([
                '--primary-color' => '#007bff',
                '--secondary-color' => '#17a2b8',
                '--success-color' => '#28a745',
                '--danger-color' => '#dc3545',
                '--warning-color' => '#ffc107',
                '--info-color' => '#17a2b8',
                '--light-color' => '#f8f9fa',
                '--dark-color' => '#004085',
                '--background-color' => '#f0f8ff',
                '--text-color' => '#004085',
                '--border-color' => '#b8daff'
            ]),
            'min_subscription_level' => 2
        ]
    ];
    
    foreach ($themes as $theme) {
        $existing = $db->fetch("SELECT id FROM predefined_themes WHERE name = ?", [$theme['name']]);
        if (!$existing) {
            $db->query("INSERT INTO predefined_themes (name, css_variables, min_subscription_level) VALUES (?, ?, ?)",
                [$theme['name'], $theme['css_variables'], $theme['min_subscription_level']]);
        }
    }
}
?>
