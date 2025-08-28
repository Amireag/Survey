<?php
require_once 'database.php';
require_once 'i18n.php';

// System initialization
function initializeSystem() {
    // Start session
    startSession();
    
    // Initialize i18n first (doesn't require database)
    I18n::getInstance();
    
    // Initialize database with error handling
    try {
        initializeDatabase();
    } catch (Exception $e) {
        // Log error but don't crash the entire site
        error_log("Database initialization failed: " . $e->getMessage());
        // You can still show the homepage without database features
    }
}

// Input validation and sanitization
function sanitizeInput($input) {
    return trim(htmlspecialchars($input, ENT_QUOTES, 'UTF-8'));
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validatePhone($phone) {
    // Basic phone validation - adjust pattern as needed
    return preg_match('/^[\+]?[0-9]{10,15}$/', preg_replace('/[\s\-\(\)]/', '', $phone));
}

// URL helper for subdirectory support
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    
    // For Survey subdirectory
    $baseDir = '/Survey';
    
    return $protocol . '://' . $host . $baseDir;
}

function redirectTo($path) {
    $baseUrl = getBaseUrl();
    $fullUrl = $baseUrl . '/' . ltrim($path, '/');
    header('Location: ' . $fullUrl);
    exit;
}

// Session management
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    $db = Database::getInstance();
    return $db->fetch("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
}

function requireAuth() {
    if (!isLoggedIn()) {
        header('HTTP/1.1 401 Unauthorized');
        exit('Unauthorized');
    }
}

function requireRole($role) {
    requireAuth();
    $user = getCurrentUser();
    if ($user['role'] !== $role) {
        header('HTTP/1.1 403 Forbidden');
        exit('Access denied');
    }
}

// Authentication functions
function authenticateUser($identifier, $password) {
    $db = Database::getInstance();
    
    // Check if identifier is email or phone
    $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL);
    $field = $isEmail ? 'email' : 'phone';
    
    $user = $db->fetch("SELECT * FROM users WHERE $field = ?", [$identifier]);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        return $user;
    }
    
    return false;
}

function createUser($email, $phone, $password, $role = 'creator', $subscriptionLevel = 1, $credits = 0) {
    $db = Database::getInstance();
    
    // Check if user already exists
    $existing = $db->fetch("SELECT id FROM users WHERE email = ? OR phone = ?", [$email, $phone]);
    if ($existing) {
        return ['success' => false, 'error' => 'User already exists'];
    }
    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    try {
        $db->query("INSERT INTO users (email, phone, password, role, subscription_level, credits) 
                   VALUES (?, ?, ?, ?, ?, ?)", 
                   [$email, $phone, $hashedPassword, $role, $subscriptionLevel, $credits]);
        
        $userId = $db->lastInsertId();
        
        // Log initial credits if any
        if ($credits > 0) {
            logCreditTransaction($userId, 'add', $credits, $credits, 'Initial credits');
        }
        
        return ['success' => true, 'user_id' => $userId];
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
    }
}

function logout() {
    session_destroy();
    return true;
}

// User management functions
function getAllUsers() {
    $db = Database::getInstance();
    return $db->fetchAll("SELECT id, email, phone, role, subscription_level, credits, created_at 
                          FROM users ORDER BY created_at DESC");
}

function getUserById($userId) {
    $db = Database::getInstance();
    return $db->fetch("SELECT * FROM users WHERE id = ?", [$userId]);
}

function updateUserCredits($userId, $amount) {
    $db = Database::getInstance();
    
    $user = getUserById($userId);
    if (!$user) {
        return false;
    }
    
    $newBalance = $user['credits'] + $amount;
    $db->query("UPDATE users SET credits = ? WHERE id = ?", [$newBalance, $userId]);
    
    // Log transaction
    logCreditTransaction($userId, 'add', $amount, $newBalance, 'Admin credit adjustment');
    
    return true;
}

// Survey functions
function generateSurveyId() {
    return bin2hex(random_bytes(8)); // 16-character hex string
}

function createSurvey($userId, $title, $description = '') {
    $db = Database::getInstance();
    
    $surveyId = generateSurveyId();
    
    try {
        $db->query("INSERT INTO surveys (user_id, title, description, survey_id, status) 
                   VALUES (?, ?, ?, ?, 'draft')", 
                   [$userId, $title, $description, $surveyId]);
        
        return ['success' => true, 'survey_id' => $db->lastInsertId(), 'survey_link_id' => $surveyId];
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
    }
}

function getSurveysByUser($userId) {
    $db = Database::getInstance();
    return $db->fetchAll("SELECT s.*, 
                         (SELECT COUNT(*) FROM survey_responses WHERE survey_id = s.id) as response_count
                         FROM surveys s 
                         WHERE s.user_id = ? 
                         ORDER BY s.updated_at DESC", [$userId]);
}

function getSurveyByLinkId($linkId) {
    $db = Database::getInstance();
    return $db->fetch("SELECT * FROM surveys WHERE survey_id = ?", [$linkId]);
}

function updateSurvey($surveyId, $title, $description, $status = null) {
    $db = Database::getInstance();
    
    $params = [$title, $description, $surveyId];
    $sql = "UPDATE surveys SET title = ?, description = ?";
    
    if ($status) {
        $sql .= ", status = ?";
        $params = [$title, $description, $status, $surveyId];
    }
    
    $sql .= " WHERE id = ?";
    
    try {
        $db->query($sql, $params);
        return ['success' => true];
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
    }
}

function deleteSurvey($surveyId) {
    $db = Database::getInstance();
    
    try {
        $db->query("DELETE FROM surveys WHERE id = ?", [$surveyId]);
        return ['success' => true];
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
    }
}

// Survey questions functions
function addSurveyQuestion($surveyId, $questionText, $questionType, $options = null, $required = false, $orderIndex = 0) {
    $db = Database::getInstance();
    
    $optionsJson = $options ? json_encode($options) : null;
    
    try {
        $db->query("INSERT INTO survey_questions (survey_id, question_text, question_type, options, required, order_index) 
                   VALUES (?, ?, ?, ?, ?, ?)", 
                   [$surveyId, $questionText, $questionType, $optionsJson, $required, $orderIndex]);
        
        return ['success' => true, 'question_id' => $db->lastInsertId()];
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
    }
}


function updateSurveyQuestion($questionId, $questionText, $questionType, $options = null, $required = false) {
    $db = Database::getInstance();
    
    $optionsJson = $options ? json_encode($options) : null;
    
    try {
        $db->query("UPDATE survey_questions SET question_text = ?, question_type = ?, options = ?, required = ? 
                   WHERE id = ?", 
                   [$questionText, $questionType, $optionsJson, $required, $questionId]);
        
        return ['success' => true];
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
    }
}

function deleteSurveyQuestion($questionId) {
    $db = Database::getInstance();
    
    try {
        $db->query("DELETE FROM survey_questions WHERE id = ?", [$questionId]);
        return ['success' => true];
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
    }
}

function reorderSurveyQuestions($surveyId, $questionOrders) {
    $db = Database::getInstance();
    
    try {
        foreach ($questionOrders as $questionId => $order) {
            $db->query("UPDATE survey_questions SET order_index = ? WHERE id = ? AND survey_id = ?", 
                      [$order, $questionId, $surveyId]);
        }
        return ['success' => true];
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
    }
}

// Survey response functions
function reserveCredits($userId, $amount, $surveyId) {
    $db = Database::getInstance();
    
    $user = getUserById($userId);
    if (!$user || $user['credits'] < $amount) {
        return false;
    }
    
    $newBalance = $user['credits'] - $amount;
    $db->query("UPDATE users SET credits = ? WHERE id = ?", [$newBalance, $userId]);
    
    // Log reservation
    logCreditTransaction($userId, 'reserve', $amount, $newBalance, 'Reserved for survey response', $surveyId);
    
    return true;
}

function spendReservedCredits($userId, $amount, $surveyId) {
    $db = Database::getInstance();
    
    $user = getUserById($userId);
    if (!$user) {
        return false;
    }
    
    // Log spending (balance already adjusted in reserve)
    logCreditTransaction($userId, 'spend', $amount, $user['credits'], 'Survey response completed', $surveyId);
    
    return true;
}

function releaseReservedCredits($userId, $amount, $surveyId) {
    $db = Database::getInstance();
    
    $user = getUserById($userId);
    if (!$user) {
        return false;
    }
    
    $newBalance = $user['credits'] + $amount;
    $db->query("UPDATE users SET credits = ? WHERE id = ?", [$newBalance, $userId]);
    
    // Log release
    logCreditTransaction($userId, 'release', $amount, $newBalance, 'Reserved credits released', $surveyId);
    
    return true;
}

function submitSurveyResponse($surveyId, $responseData, $ipAddress = null, $userAgent = null) {
    $db = Database::getInstance();
    
    $survey = getSurveyById($surveyId);
    if (!$survey) {
        return ['success' => false, 'error' => 'Survey not found'];
    }
    
    if ($survey['status'] !== 'active') {
        return ['success' => false, 'error' => 'Survey is not active'];
    }
    
    try {
        $db->query("INSERT INTO survey_responses (survey_id, response_data, ip_address, user_agent) 
                   VALUES (?, ?, ?, ?)", 
                   [$surveyId, json_encode($responseData), $ipAddress, $userAgent]);
        
        // Spend credits for survey creator
        $creditCost = 1; // 1 credit per response
        spendReservedCredits($survey['user_id'], $creditCost, $surveyId);
        
        return ['success' => true, 'response_id' => $db->lastInsertId()];
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
    }
}

function getSurveyResponses($surveyId) {
    $db = Database::getInstance();
    return $db->fetchAll("SELECT * FROM survey_responses WHERE survey_id = ? ORDER BY submitted_at DESC", [$surveyId]);
}

// Credit transaction functions
function logCreditTransaction($userId, $type, $amount, $balanceAfter, $description = null, $surveyId = null) {
    $db = Database::getInstance();
    
    try {
        $db->query("INSERT INTO credit_transactions (user_id, transaction_type, amount, balance_after, description, survey_id) 
                   VALUES (?, ?, ?, ?, ?, ?)", 
                   [$userId, $type, $amount, $balanceAfter, $description, $surveyId]);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function getCreditTransactions($userId = null) {
    $db = Database::getInstance();
    
    if ($userId) {
        return $db->fetchAll("SELECT ct.*, s.title as survey_title 
                             FROM credit_transactions ct 
                             LEFT JOIN surveys s ON ct.survey_id = s.id 
                             WHERE ct.user_id = ? 
                             ORDER BY ct.created_at DESC", [$userId]);
    } else {
        return $db->fetchAll("SELECT ct.*, u.email, s.title as survey_title 
                             FROM credit_transactions ct 
                             LEFT JOIN users u ON ct.user_id = u.id 
                             LEFT JOIN surveys s ON ct.survey_id = s.id 
                             ORDER BY ct.created_at DESC");
    }
}

// Theme functions
function getPredefinedThemes($minSubscriptionLevel = 1) {
    $db = Database::getInstance();
    return $db->fetchAll("SELECT * FROM predefined_themes WHERE min_subscription_level <= ? ORDER BY min_subscription_level ASC", [$minSubscriptionLevel]);
}

function getCustomThemes($userId) {
    $db = Database::getInstance();
    return $db->fetchAll("SELECT * FROM custom_themes WHERE user_id = ? ORDER BY created_at DESC", [$userId]);
}

function createCustomTheme($userId, $name, $cssVariables) {
    $db = Database::getInstance();
    
    // Check user's subscription level for theme limits
    $user = getUserById($userId);
    $maxThemes = getMaxCustomThemes($user['subscription_level']);
    
    $currentThemes = $db->fetch("SELECT COUNT(*) as count FROM custom_themes WHERE user_id = ?", [$userId]);
    
    if ($currentThemes['count'] >= $maxThemes) {
        return ['success' => false, 'error' => 'Theme limit reached for your subscription level'];
    }
    
    try {
        $db->query("INSERT INTO custom_themes (user_id, name, css_variables) VALUES (?, ?, ?)", 
                   [$userId, $name, json_encode($cssVariables)]);
        
        return ['success' => true, 'theme_id' => $db->lastInsertId()];
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
    }
}

function deleteCustomTheme($themeId, $userId) {
    $db = Database::getInstance();
    
    try {
        $db->query("DELETE FROM custom_themes WHERE id = ? AND user_id = ?", [$themeId, $userId]);
        return ['success' => true];
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
    }
}

function getMaxCustomThemes($subscriptionLevel) {
    $limits = [
        1 => 0,  // Basic - no custom themes
        2 => 0,  // Standard - no custom themes  
        3 => 3,  // Premium - 3 custom themes
        4 => 10, // Professional - 10 custom themes
        5 => 50  // Enterprise - 50 custom themes
    ];
    
    return $limits[$subscriptionLevel] ?? 0;
}

// Survey Builder Functions
function createSurveyWithQuestions($userId, $surveyData, $questions = []) {
    $db = Database::getInstance();
    
    try {
        $db->query("BEGIN");
        
        // Create the survey
        $allowAnonymous = $surveyData['allow_anonymous'] ?? 0;
        $showProgress = $surveyData['show_progress_bar'] ?? 1;
        
        $db->query("INSERT INTO surveys (user_id, title, description, status, allow_anonymous, show_progress_bar) VALUES (?, ?, ?, 'draft', ?, ?)", 
                   [$userId, $surveyData['title'], $surveyData['description'], $allowAnonymous, $showProgress]);
        
        $surveyId = $db->lastInsertId();
        
        // Add questions
        if (!empty($questions)) {
            foreach ($questions as $index => $question) {
                $options = null;
                if (in_array($question['question_type'], ['radio', 'checkbox', 'dropdown']) && isset($question['options'])) {
                    $options = json_encode($question['options']);
                }
                
                $db->query("INSERT INTO survey_questions (survey_id, question_text, question_type, required, options, order_index) VALUES (?, ?, ?, ?, ?, ?)", 
                          [$surveyId, $question['question_text'], $question['question_type'], $question['required'], $options, $index]);
            }
        }
        
        $db->query("COMMIT");
        
        return ['success' => true, 'survey_id' => $surveyId];
    } catch (Exception $e) {
        $db->query("ROLLBACK");
        return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
    }
}

function updateSurveyWithQuestions($surveyId, $userId, $surveyData, $questions = []) {
    $db = Database::getInstance();
    
    try {
        $db->query("BEGIN");
        
        // Update the survey
        $allowAnonymous = $surveyData['allow_anonymous'] ?? 0;
        $showProgress = $surveyData['show_progress_bar'] ?? 1;
        
        $db->query("UPDATE surveys SET title = ?, description = ?, allow_anonymous = ?, show_progress_bar = ?, updated_at = NOW() WHERE id = ? AND user_id = ?", 
                   [$surveyData['title'], $surveyData['description'], $allowAnonymous, $showProgress, $surveyId, $userId]);
        
        // Delete existing questions
        $db->query("DELETE FROM survey_questions WHERE survey_id = ?", [$surveyId]);
        
        // Add new questions
        if (!empty($questions)) {
            foreach ($questions as $index => $question) {
                $options = null;
                if (in_array($question['question_type'], ['radio', 'checkbox', 'dropdown']) && isset($question['options'])) {
                    $options = json_encode($question['options']);
                }
                
                $db->query("INSERT INTO survey_questions (survey_id, question_text, question_type, required, options, order_index) VALUES (?, ?, ?, ?, ?, ?)", 
                          [$surveyId, $question['question_text'], $question['question_type'], $question['required'], $options, $index]);
            }
        }
        
        $db->query("COMMIT");
        
        return ['success' => true];
    } catch (Exception $e) {
        $db->query("ROLLBACK");
        return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
    }
}

function getSurveyById($surveyId) {
    $db = Database::getInstance();
    return $db->fetch("SELECT * FROM surveys WHERE id = ?", [$surveyId]);
}

function getSurveyQuestions($surveyId) {
    $db = Database::getInstance();
    $questions = $db->fetchAll("SELECT * FROM survey_questions WHERE survey_id = ? ORDER BY order_index ASC", [$surveyId]);
    
    // Parse options JSON for questions that have options
    foreach ($questions as &$question) {
        if ($question['options']) {
            $question['options'] = json_decode($question['options'], true);
        }
    }
    
    return $questions;
}

function publishSurvey($surveyId, $userId) {
    $db = Database::getInstance();
    
    try {
        // Check if survey belongs to user
        $survey = $db->fetch("SELECT * FROM surveys WHERE id = ? AND user_id = ?", [$surveyId, $userId]);
        if (!$survey) {
            return ['success' => false, 'error' => 'Survey not found or access denied'];
        }
        
        // Check if survey has questions
        $questionCount = $db->fetch("SELECT COUNT(*) as count FROM survey_questions WHERE survey_id = ?", [$surveyId]);
        if ($questionCount['count'] == 0) {
            return ['success' => false, 'error' => 'Cannot publish survey without questions'];
        }
        
        $db->query("UPDATE surveys SET status = 'active', published_at = NOW() WHERE id = ?", [$surveyId]);
        
        return ['success' => true];
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
    }
}

function closeSurvey($surveyId, $userId) {
    $db = Database::getInstance();
    
    try {
        $db->query("UPDATE surveys SET status = 'closed', closed_at = NOW() WHERE id = ? AND user_id = ?", [$surveyId, $userId]);
        return ['success' => true];
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
    }
}

function duplicateSurvey($surveyId, $userId) {
    $db = Database::getInstance();
    
    try {
        $db->query("BEGIN");
        
        // Get original survey
        $survey = $db->fetch("SELECT * FROM surveys WHERE id = ? AND user_id = ?", [$surveyId, $userId]);
        if (!$survey) {
            throw new Exception('Survey not found or access denied');
        }
        
        // Create duplicate survey
        $newTitle = $survey['title'] . ' (Copy)';
        $db->query("INSERT INTO surveys (user_id, title, description, status, allow_anonymous, show_progress_bar) VALUES (?, ?, ?, 'draft', ?, ?)", 
                   [$userId, $newTitle, $survey['description'], $survey['allow_anonymous'], $survey['show_progress_bar']]);
        
        $newSurveyId = $db->lastInsertId();
        
        // Copy questions
        $questions = $db->fetchAll("SELECT * FROM survey_questions WHERE survey_id = ? ORDER BY order_index ASC", [$surveyId]);
        foreach ($questions as $question) {
            $db->query("INSERT INTO survey_questions (survey_id, question_text, question_type, required, options, order_index) VALUES (?, ?, ?, ?, ?, ?)", 
                      [$newSurveyId, $question['question_text'], $question['question_type'], $question['required'], $question['options'], $question['order_index']]);
        }
        
        $db->query("COMMIT");
        
        return ['success' => true, 'survey_id' => $newSurveyId];
    } catch (Exception $e) {
        $db->query("ROLLBACK");
        return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
    }
}

function setSurveyTheme($surveyId, $themeId = null, $customThemeData = null) {
    $db = Database::getInstance();
    
    try {
        $customThemeJson = $customThemeData ? json_encode($customThemeData) : null;
        $db->query("UPDATE surveys SET theme_id = ?, custom_theme_data = ? WHERE id = ?", 
                   [$themeId, $customThemeJson, $surveyId]);
        return ['success' => true];
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
    }
}

// Statistics functions
function getDashboardStats() {
    $db = Database::getInstance();
    
    $stats = [];
    
    // Total users
    $stats['total_users'] = $db->fetch("SELECT COUNT(*) as count FROM users")['count'];
    
    // Total surveys
    $stats['total_surveys'] = $db->fetch("SELECT COUNT(*) as count FROM surveys")['count'];
    
    // Total responses
    $stats['total_responses'] = $db->fetch("SELECT COUNT(*) as count FROM survey_responses")['count'];
    
    // Credits statistics
    $stats['total_credits_used'] = $db->fetch("SELECT COALESCE(SUM(amount), 0) as total 
                                              FROM credit_transactions 
                                              WHERE transaction_type = 'spend'")['total'];
    
    $stats['total_credits_available'] = $db->fetch("SELECT COALESCE(SUM(credits), 0) as total FROM users")['total'];
    
    $stats['total_credits_bought'] = $db->fetch("SELECT COALESCE(SUM(amount), 0) as total 
                                               FROM credit_transactions 
                                               WHERE transaction_type = 'add'")['total'];
    
    // Users by subscription level
    $stats['users_by_subscription'] = $db->fetchAll("SELECT subscription_level, COUNT(*) as count 
                                                     FROM users 
                                                     GROUP BY subscription_level 
                                                     ORDER BY subscription_level");
    
    // Top users by credit usage
    $stats['top_users'] = $db->fetchAll("SELECT u.email, u.phone, COALESCE(SUM(ct.amount), 0) as credits_used 
                                        FROM users u 
                                        LEFT JOIN credit_transactions ct ON u.id = ct.user_id AND ct.transaction_type = 'spend'
                                        GROUP BY u.id 
                                        ORDER BY credits_used DESC 
                                        LIMIT 10");
    
    // Top surveys by response count
    $stats['top_surveys'] = $db->fetchAll("SELECT s.title, s.survey_id, u.email as creator_email, COUNT(sr.id) as response_count 
                                          FROM surveys s 
                                          LEFT JOIN survey_responses sr ON s.id = sr.survey_id 
                                          LEFT JOIN users u ON s.user_id = u.id 
                                          GROUP BY s.id 
                                          ORDER BY response_count DESC 
                                          LIMIT 10");
    
    return $stats;
}


function generateCSV($data, $filename) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    if (!empty($data)) {
        // Write header
        fputcsv($output, array_keys($data[0]));
        
        // Write data
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
    }
    
    fclose($output);
    exit;
}

function exportSurveyResults($surveyId) {
    $survey = getSurveyById($surveyId);
    if (!$survey) {
        return false;
    }
    
    $questions = getSurveyQuestions($surveyId);
    $responses = getSurveyResponses($surveyId);
    
    $csvData = [];
    
    // Build headers
    $headers = ['Response ID', 'Submitted At', 'IP Address'];
    foreach ($questions as $question) {
        $headers[] = $question['question_text'];
    }
    
    // Build data rows
    foreach ($responses as $response) {
        $responseData = json_decode($response['response_data'], true);
        $row = [
            $response['id'],
            $response['submitted_at'],
            $response['ip_address']
        ];
        
        foreach ($questions as $question) {
            $questionId = 'question_' . $question['id'];
            $answer = $responseData[$questionId] ?? '';
            
            // Handle array answers (checkboxes)
            if (is_array($answer)) {
                $answer = implode(', ', $answer);
            }
            
            $row[] = $answer;
        }
        
        $csvData[] = $row;
    }
    
    // Add headers as first row
    array_unshift($csvData, $headers);
    
    generateCSV($csvData, 'survey_' . $survey['survey_id'] . '_results');
}

?>