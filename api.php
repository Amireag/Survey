<?php
require_once 'core/functions.php';

// Initialize system
initializeSystem();

// Set JSON response headers
header('Content-Type: application/json');

// Get request method and action
$method = $_SERVER['REQUEST_METHOD'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Check for JSON action (for survey builder requests)
if (empty($action) && $method === 'POST') {
    $jsonData = json_decode(file_get_contents('php://input'), true);
    if ($jsonData && isset($jsonData['action'])) {
        $action = $jsonData['action'];
    }
}

$response = ['success' => false, 'message' => '', 'data' => null];

try {
    switch ($action) {
        // Authentication actions
        case 'login':
            if ($method !== 'POST') {
                throw new Exception('Invalid request method');
            }
            
            $identifier = sanitizeInput($_POST['identifier'] ?? '');
            $password = $_POST['password'] ?? '';
            
            if (empty($identifier) || empty($password)) {
                throw new Exception(t('login_failed'));
            }
            
            $user = authenticateUser($identifier, $password);
            if ($user) {
                $response['success'] = true;
                $response['message'] = t('login_success');
                $response['data'] = [
                    'user_id' => $user['id'],
                    'role' => $user['role'],
                    'redirect' => $user['role'] === 'admin' ? 'admin/' : 'creator/'
                ];
            } else {
                throw new Exception(t('login_failed'));
            }
            break;
            
        case 'logout':
            logout();
            $response['success'] = true;
            $response['message'] = t('logout_success');
            break;
            
        case 'register':
            if ($method !== 'POST') {
                throw new Exception('Invalid request method');
            }
            
            $email = sanitizeInput($_POST['email'] ?? '');
            $phone = sanitizeInput($_POST['phone'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            if (empty($email) || empty($phone) || empty($password)) {
                throw new Exception('All fields are required');
            }
            
            if ($password !== $confirmPassword) {
                throw new Exception('Passwords do not match');
            }
            
            if (!validateEmail($email)) {
                throw new Exception('Invalid email address');
            }
            
            if (!validatePhone($phone)) {
                throw new Exception('Invalid phone number');
            }
            
            $result = createUser($email, $phone, $password);
            if ($result['success']) {
                $response['success'] = true;
                $response['message'] = t('registration_success');
            } else {
                throw new Exception($result['error']);
            }
            break;
            
        // Survey management actions
        case 'create_survey':
            requireAuth();
            if ($method !== 'POST') {
                throw new Exception('Invalid request method');
            }
            
            // Check if JSON data is provided (from survey builder)
            $jsonData = json_decode(file_get_contents('php://input'), true);
            if ($jsonData && isset($jsonData['action']) && $jsonData['action'] === 'create_survey') {
                $surveyData = [
                    'title' => sanitizeInput($jsonData['title'] ?? ''),
                    'description' => sanitizeInput($jsonData['description'] ?? ''),
                    'allow_anonymous' => $jsonData['allow_anonymous'] ?? false,
                    'show_progress_bar' => $jsonData['show_progress_bar'] ?? true
                ];
                $questions = $jsonData['questions'] ?? [];
                
                if (empty($surveyData['title'])) {
                    throw new Exception(t('survey_title_required'));
                }
                
                $user = getCurrentUser();
                $result = createSurveyWithQuestions($user['id'], $surveyData, $questions);
                
                if ($result['success']) {
                    $response['success'] = true;
                    $response['message'] = t('survey_created_success');
                    $response['data'] = $result;
                } else {
                    throw new Exception($result['error']);
                }
            } else {
                // Legacy create survey (for simple form)
                $title = sanitizeInput($_POST['title'] ?? '');
                $description = sanitizeInput($_POST['description'] ?? '');
                
                if (empty($title)) {
                    throw new Exception('Survey title is required');
                }
                
                $user = getCurrentUser();
                $result = createSurvey($user['id'], $title, $description);
                
                if ($result['success']) {
                    $response['success'] = true;
                    $response['message'] = t('survey_created_success');
                    $response['data'] = $result;
                } else {
                    throw new Exception($result['error']);
                }
            }
            break;
            
        case 'update_survey':
            requireAuth();
            if ($method !== 'POST') {
                throw new Exception('Invalid request method');
            }
            
            // Check if JSON data is provided (from survey builder)
            $jsonData = json_decode(file_get_contents('php://input'), true);
            if ($jsonData && isset($jsonData['action']) && $jsonData['action'] === 'update_survey') {
                $surveyId = (int)($jsonData['survey_id'] ?? 0);
                $surveyData = [
                    'title' => sanitizeInput($jsonData['title'] ?? ''),
                    'description' => sanitizeInput($jsonData['description'] ?? ''),
                    'allow_anonymous' => $jsonData['allow_anonymous'] ?? false,
                    'show_progress_bar' => $jsonData['show_progress_bar'] ?? true
                ];
                $questions = $jsonData['questions'] ?? [];
                $status = sanitizeInput($jsonData['status'] ?? '');
                
                if (empty($surveyData['title'])) {
                    throw new Exception(t('survey_title_required'));
                }
                
                $user = getCurrentUser();
                
                // Check ownership
                $survey = getSurveyById($surveyId);
                if (!$survey || ($survey['user_id'] != $user['id'] && $user['role'] !== 'admin')) {
                    throw new Exception(t('access_denied'));
                }
                
                $result = updateSurveyWithQuestions($surveyId, $user['id'], $surveyData, $questions);
                
                // Update status if provided
                if ($status && $result['success']) {
                    updateSurvey($surveyId, $surveyData['title'], $surveyData['description'], $status);
                }
                
                if ($result['success']) {
                    $response['success'] = true;
                    $response['message'] = t('survey_updated_success');
                    $response['data'] = $result;
                } else {
                    throw new Exception($result['error']);
                }
            } else {
                // Legacy update survey (for simple form)
                $surveyId = (int)($_POST['survey_id'] ?? 0);
                $title = sanitizeInput($_POST['title'] ?? '');
                $description = sanitizeInput($_POST['description'] ?? '');
                $status = sanitizeInput($_POST['status'] ?? '');
                
                if (empty($title)) {
                    throw new Exception('Survey title is required');
                }
                
                // Check ownership
                $survey = getSurveyById($surveyId);
                $user = getCurrentUser();
                
                if (!$survey || ($survey['user_id'] != $user['id'] && $user['role'] !== 'admin')) {
                    throw new Exception(t('access_denied'));
                }
                
                $result = updateSurvey($surveyId, $title, $description, $status);
                
                if ($result['success']) {
                    $response['success'] = true;
                    $response['message'] = t('survey_updated_success');
                } else {
                    throw new Exception($result['error']);
                }
            }
            break;
            
        case 'delete_survey':
            requireAuth();
            if ($method !== 'POST') {
                throw new Exception('Invalid request method');
            }
            
            $surveyId = (int)($_POST['survey_id'] ?? 0);
            
            // Check ownership
            $survey = getSurveyById($surveyId);
            $user = getCurrentUser();
            
            if (!$survey || ($survey['user_id'] != $user['id'] && $user['role'] !== 'admin')) {
                throw new Exception(t('access_denied'));
            }
            
            $result = deleteSurvey($surveyId);
            
            if ($result['success']) {
                $response['success'] = true;
                $response['message'] = t('survey_deleted_success');
            } else {
                throw new Exception($result['error']);
            }
            break;
            
        case 'get_survey_questions':
            requireAuth();
            
            $surveyId = (int)($_GET['survey_id'] ?? 0);
            
            // Check ownership
            $survey = getSurveyById($surveyId);
            $user = getCurrentUser();
            
            if (!$survey || ($survey['user_id'] != $user['id'] && $user['role'] !== 'admin')) {
                throw new Exception(t('access_denied'));
            }
            
            $questions = getSurveyQuestions($surveyId);
            
            $response['success'] = true;
            $response['data'] = $questions;
            break;
            
        case 'add_question':
            requireAuth();
            if ($method !== 'POST') {
                throw new Exception('Invalid request method');
            }
            
            $surveyId = (int)($_POST['survey_id'] ?? 0);
            $questionText = sanitizeInput($_POST['question_text'] ?? '');
            $questionType = sanitizeInput($_POST['question_type'] ?? '');
            $options = $_POST['options'] ?? null;
            $required = isset($_POST['required']) && $_POST['required'] === 'true';
            $orderIndex = (int)($_POST['order_index'] ?? 0);
            
            if (empty($questionText) || empty($questionType)) {
                throw new Exception('Question text and type are required');
            }
            
            // Check ownership
            $survey = getSurveyById($surveyId);
            $user = getCurrentUser();
            
            if (!$survey || ($survey['user_id'] != $user['id'] && $user['role'] !== 'admin')) {
                throw new Exception(t('access_denied'));
            }
            
            $result = addSurveyQuestion($surveyId, $questionText, $questionType, $options, $required, $orderIndex);
            
            if ($result['success']) {
                $response['success'] = true;
                $response['message'] = 'Question added successfully';
                $response['data'] = $result;
            } else {
                throw new Exception($result['error']);
            }
            break;
            
        case 'update_question':
            requireAuth();
            if ($method !== 'POST') {
                throw new Exception('Invalid request method');
            }
            
            $questionId = (int)($_POST['question_id'] ?? 0);
            $questionText = sanitizeInput($_POST['question_text'] ?? '');
            $questionType = sanitizeInput($_POST['question_type'] ?? '');
            $options = $_POST['options'] ?? null;
            $required = isset($_POST['required']) && $_POST['required'] === 'true';
            
            if (empty($questionText) || empty($questionType)) {
                throw new Exception('Question text and type are required');
            }
            
            $result = updateSurveyQuestion($questionId, $questionText, $questionType, $options, $required);
            
            if ($result['success']) {
                $response['success'] = true;
                $response['message'] = 'Question updated successfully';
            } else {
                throw new Exception($result['error']);
            }
            break;
            
        case 'delete_question':
            requireAuth();
            if ($method !== 'POST') {
                throw new Exception('Invalid request method');
            }
            
            $questionId = (int)($_POST['question_id'] ?? 0);
            
            $result = deleteSurveyQuestion($questionId);
            
            if ($result['success']) {
                $response['success'] = true;
                $response['message'] = 'Question deleted successfully';
            } else {
                throw new Exception($result['error']);
            }
            break;
            
        case 'reorder_questions':
            requireAuth();
            if ($method !== 'POST') {
                throw new Exception('Invalid request method');
            }
            
            $surveyId = (int)($_POST['survey_id'] ?? 0);
            $questionOrders = $_POST['question_orders'] ?? [];
            
            // Check ownership
            $survey = getSurveyById($surveyId);
            $user = getCurrentUser();
            
            if (!$survey || ($survey['user_id'] != $user['id'] && $user['role'] !== 'admin')) {
                throw new Exception(t('access_denied'));
            }
            
            $result = reorderSurveyQuestions($surveyId, $questionOrders);
            
            if ($result['success']) {
                $response['success'] = true;
                $response['message'] = 'Questions reordered successfully';
            } else {
                throw new Exception($result['error']);
            }
            break;
            
        // Survey response actions
        case 'submit_response':
            if ($method !== 'POST') {
                throw new Exception('Invalid request method');
            }
            
            $surveyLinkId = sanitizeInput($_POST['survey_link_id'] ?? '');
            $responseData = $_POST['responses'] ?? [];
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
            
            if (empty($surveyLinkId) || empty($responseData)) {
                throw new Exception('Invalid survey response data');
            }
            
            $survey = getSurveyByLinkId($surveyLinkId);
            if (!$survey) {
                throw new Exception(t('survey_not_found'));
            }
            
            $result = submitSurveyResponse($survey['id'], $responseData, $ipAddress, $userAgent);
            
            if ($result['success']) {
                $response['success'] = true;
                $response['message'] = t('survey_completed');
            } else {
                throw new Exception($result['error']);
            }
            break;
            
        case 'export_results':
            requireAuth();
            
            $surveyId = (int)($_GET['survey_id'] ?? 0);
            
            // Check ownership
            $survey = getSurveyById($surveyId);
            $user = getCurrentUser();
            
            if (!$survey || ($survey['user_id'] != $user['id'] && $user['role'] !== 'admin')) {
                throw new Exception(t('access_denied'));
            }
            
            exportSurveyResults($surveyId);
            exit; // exportSurveyResults handles response
            
        // User management actions
        case 'create_user':
            requireRole('admin');
            if ($method !== 'POST') {
                throw new Exception('Invalid request method');
            }
            
            $email = sanitizeInput($_POST['email'] ?? '');
            $phone = sanitizeInput($_POST['phone'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = sanitizeInput($_POST['role'] ?? 'creator');
            $subscriptionLevel = (int)($_POST['subscription_level'] ?? 1);
            $credits = (int)($_POST['credits'] ?? 0);
            
            if (empty($email) || empty($phone) || empty($password)) {
                throw new Exception('All fields are required');
            }
            
            if (!validateEmail($email)) {
                throw new Exception('Invalid email address');
            }
            
            if (!validatePhone($phone)) {
                throw new Exception('Invalid phone number');
            }
            
            $result = createUser($email, $phone, $password, $role, $subscriptionLevel, $credits);
            
            if ($result['success']) {
                $response['success'] = true;
                $response['message'] = t('user_created_success');
            } else {
                throw new Exception($result['error']);
            }
            break;
            
        case 'add_credits':
            requireRole('admin');
            if ($method !== 'POST') {
                throw new Exception('Invalid request method');
            }
            
            $userId = (int)($_POST['user_id'] ?? 0);
            $amount = (int)($_POST['amount'] ?? 0);
            
            if ($amount <= 0) {
                throw new Exception('Credit amount must be positive');
            }
            
            $result = updateUserCredits($userId, $amount);
            
            if ($result) {
                $response['success'] = true;
                $response['message'] = t('credits_added_success');
            } else {
                throw new Exception('Failed to add credits');
            }
            break;
            
        // Theme management actions
        case 'create_custom_theme':
            requireAuth();
            if ($method !== 'POST') {
                throw new Exception('Invalid request method');
            }
            
            $name = sanitizeInput($_POST['name'] ?? '');
            $cssVariables = $_POST['css_variables'] ?? [];
            
            if (empty($name)) {
                throw new Exception('Theme name is required');
            }
            
            $user = getCurrentUser();
            $result = createCustomTheme($user['id'], $name, $cssVariables);
            
            if ($result['success']) {
                $response['success'] = true;
                $response['message'] = t('theme_created_success');
                $response['data'] = $result;
            } else {
                throw new Exception($result['error']);
            }
            break;
            
        case 'delete_custom_theme':
            requireAuth();
            if ($method !== 'POST') {
                throw new Exception('Invalid request method');
            }
            
            $themeId = (int)($_POST['theme_id'] ?? 0);
            
            $user = getCurrentUser();
            $result = deleteCustomTheme($themeId, $user['id']);
            
            if ($result['success']) {
                $response['success'] = true;
                $response['message'] = t('theme_deleted_success');
            } else {
                throw new Exception($result['error']);
            }
            break;
            
        case 'set_survey_theme':
            requireAuth();
            if ($method !== 'POST') {
                throw new Exception('Invalid request method');
            }
            
            $surveyId = (int)($_POST['survey_id'] ?? 0);
            $themeId = $_POST['theme_id'] ? (int)$_POST['theme_id'] : null;
            $customThemeData = $_POST['custom_theme_data'] ?? null;
            
            // Check ownership
            $survey = getSurveyById($surveyId);
            $user = getCurrentUser();
            
            if (!$survey || ($survey['user_id'] != $user['id'] && $user['role'] !== 'admin')) {
                throw new Exception(t('access_denied'));
            }
            
            $result = setSurveyTheme($surveyId, $themeId, $customThemeData);
            
            if ($result['success']) {
                $response['success'] = true;
                $response['message'] = 'Theme applied successfully';
            } else {
                throw new Exception($result['error']);
            }
            break;
            
        // Data retrieval actions
        case 'get_dashboard_stats':
            requireRole('admin');
            
            $stats = getDashboardStats();
            $response['success'] = true;
            $response['data'] = $stats;
            break;
            
        case 'get_users':
            requireRole('admin');
            
            $users = getAllUsers();
            $response['success'] = true;
            $response['data'] = $users;
            break;
            
        case 'get_user_surveys':
            requireAuth();
            
            $user = getCurrentUser();
            $surveys = getSurveysByUser($user['id']);
            
            $response['success'] = true;
            $response['data'] = $surveys;
            break;
            
        case 'get_survey_responses':
            requireAuth();
            
            $surveyId = (int)($_GET['survey_id'] ?? 0);
            
            // Check ownership
            $survey = getSurveyById($surveyId);
            $user = getCurrentUser();
            
            if (!$survey || ($survey['user_id'] != $user['id'] && $user['role'] !== 'admin')) {
                throw new Exception(t('access_denied'));
            }
            
            $responses = getSurveyResponses($surveyId);
            $questions = getSurveyQuestions($surveyId);
            
            $response['success'] = true;
            $response['data'] = [
                'responses' => $responses,
                'questions' => $questions
            ];
            break;
            
        case 'get_predefined_themes':
            requireAuth();
            
            $user = getCurrentUser();
            $themes = getPredefinedThemes($user['subscription_level']);
            
            $response['success'] = true;
            $response['data'] = $themes;
            break;
            
        case 'get_custom_themes':
            requireAuth();
            
            $user = getCurrentUser();
            $themes = getCustomThemes($user['id']);
            
            $response['success'] = true;
            $response['data'] = $themes;
            break;
            
        case 'get_credit_transactions':
            requireAuth();
            
            $user = getCurrentUser();
            $transactions = getCreditTransactions($user['role'] === 'admin' ? null : $user['id']);
            
            $response['success'] = true;
            $response['data'] = $transactions;
            break;
            
        // Language switching
        case 'switch_language':
            $language = sanitizeInput($_POST['language'] ?? $_GET['language'] ?? '');
            
            if (in_array($language, ['en', 'fa'])) {
                I18n::getInstance()->setLanguage($language);
                $response['success'] = true;
                $response['message'] = 'Language switched successfully';
            } else {
                throw new Exception('Invalid language');
            }
            break;
            
        default:
            throw new Exception(t('invalid_request'));
    }
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    
    // Log error for debugging
    error_log("API Error: " . $e->getMessage() . " | Action: " . $action . " | User: " . (getCurrentUser()['id'] ?? 'guest'));
}

// Output response
echo json_encode($response);
?>
