/* Survey Platform Main JavaScript */

// Global variables
let currentUser = null;
let loadingTimeout = null;

// Initialize application
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
    setupEventListeners();
    loadCurrentUser();
});

// Application initialization
function initializeApp() {
    console.log('Survey Platform v3.0 - Initializing...');
    
    // Close modals when clicking outside
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal')) {
            closeModal(event.target.id);
        }
    });
    
    // Close modals with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeAllModals();
        }
    });
    
    // Auto-hide messages after 5 seconds
    setTimeout(hideMessages, 5000);
}

// Setup global event listeners
function setupEventListeners() {
    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', validateForm);
    });
    
    // Auto-resize textareas
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', autoResizeTextarea);
    });
    
    // Password strength indicator
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    passwordInputs.forEach(input => {
        input.addEventListener('input', checkPasswordStrength);
    });
}

// User session management
function loadCurrentUser() {
    // This would typically be loaded from PHP session
    // For now, we'll check if user elements exist
    const userInfo = document.querySelector('.user-info');
    if (userInfo) {
        currentUser = {
            id: userInfo.dataset.userId || null,
            email: userInfo.querySelector('.user-name')?.textContent || '',
            role: userInfo.dataset.userRole || 'creator'
        };
    }
}

// Authentication functions
async function handleLogin(event) {
    event.preventDefault();
    showLoading();
    
    const formData = new FormData(event.target);
    formData.append('action', 'login');
    
    try {
        const response = await fetch('/api.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMessage(result.message, 'success');
            closeModal('loginModal');
            
            // Redirect based on user role
            setTimeout(() => {
                window.location.href = result.data.redirect;
            }, 1000);
        } else {
            showMessage(result.message, 'error');
        }
    } catch (error) {
        showMessage('Network error occurred', 'error');
        console.error('Login error:', error);
    } finally {
        hideLoading();
    }
}

async function handleRegister(event) {
    event.preventDefault();
    
    const password = event.target.password.value;
    const confirmPassword = event.target.confirm_password.value;
    
    if (password !== confirmPassword) {
        showMessage('Passwords do not match', 'error');
        return;
    }
    
    showLoading();
    
    const formData = new FormData(event.target);
    formData.append('action', 'register');
    
    try {
        const response = await fetch('/api.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMessage(result.message, 'success');
            closeModal('registerModal');
            event.target.reset();
            
            // Show login modal after successful registration
            setTimeout(() => {
                showLoginModal();
            }, 1500);
        } else {
            showMessage(result.message, 'error');
        }
    } catch (error) {
        showMessage('Network error occurred', 'error');
        console.error('Registration error:', error);
    } finally {
        hideLoading();
    }
}

async function logout() {
    showLoading();
    
    try {
        const response = await fetch('/api.php?action=logout');
        const result = await response.json();
        
        if (result.success) {
            showMessage(result.message, 'success');
            setTimeout(() => {
                window.location.href = '/';
            }, 1000);
        } else {
            showMessage('Logout failed', 'error');
        }
    } catch (error) {
        showMessage('Network error occurred', 'error');
        console.error('Logout error:', error);
    } finally {
        hideLoading();
    }
}

// Modal management
function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        // Focus first input
        const firstInput = modal.querySelector('input, select, textarea');
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 100);
        }
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
        
        // Reset forms in modal
        const forms = modal.querySelectorAll('form');
        forms.forEach(form => form.reset());
    }
}

function closeAllModals() {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.style.display = 'none';
    });
    document.body.style.overflow = '';
}

function showLoginModal() {
    showModal('loginModal');
}

function showRegisterModal() {
    showModal('registerModal');
}

// Message system
function showMessage(text, type = 'info', duration = 5000) {
    const container = document.getElementById('messageContainer') || createMessageContainer();
    
    const message = document.createElement('div');
    message.className = `message ${type}`;
    message.innerHTML = `
        <span>${text}</span>
        <button class="message-close" onclick="hideMessage(this)">&times;</button>
    `;
    
    container.appendChild(message);
    
    // Auto-remove after duration
    if (duration > 0) {
        setTimeout(() => {
            hideMessage(message.querySelector('.message-close'));
        }, duration);
    }
}

function createMessageContainer() {
    const container = document.createElement('div');
    container.id = 'messageContainer';
    container.className = 'message-container';
    document.body.appendChild(container);
    return container;
}

function hideMessage(closeButton) {
    const message = closeButton.parentElement;
    message.style.animation = 'messageSlideOut 0.3s ease forwards';
    setTimeout(() => {
        message.remove();
    }, 300);
}

function hideMessages() {
    const messages = document.querySelectorAll('.message');
    messages.forEach(message => {
        const closeButton = message.querySelector('.message-close');
        if (closeButton) {
            hideMessage(closeButton);
        }
    });
}

// Add slide out animation
const style = document.createElement('style');
style.textContent = `
@keyframes messageSlideOut {
    from {
        opacity: 1;
        transform: translateX(0);
    }
    to {
        opacity: 0;
        transform: translateX(100%);
    }
}
`;
document.head.appendChild(style);

// Loading management
function showLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    // Prevent indefinite loading
    if (loadingTimeout) {
        clearTimeout(loadingTimeout);
    }
    loadingTimeout = setTimeout(hideLoading, 30000); // 30 second timeout
}

function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    if (loadingTimeout) {
        clearTimeout(loadingTimeout);
        loadingTimeout = null;
    }
}

// Form validation
function validateForm(event) {
    const form = event.target;
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('error');
            isValid = false;
        } else {
            field.classList.remove('error');
        }
    });
    
    // Email validation
    const emailFields = form.querySelectorAll('input[type="email"]');
    emailFields.forEach(field => {
        if (field.value && !isValidEmail(field.value)) {
            field.classList.add('error');
            showMessage('Please enter a valid email address', 'error');
            isValid = false;
        }
    });
    
    // Phone validation
    const phoneFields = form.querySelectorAll('input[type="tel"]');
    phoneFields.forEach(field => {
        if (field.value && !isValidPhone(field.value)) {
            field.classList.add('error');
            showMessage('Please enter a valid phone number', 'error');
            isValid = false;
        }
    });
    
    if (!isValid) {
        event.preventDefault();
        showMessage('Please fill in all required fields correctly', 'error');
    }
}

// Validation helpers
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function isValidPhone(phone) {
    const phoneRegex = /^[\+]?[0-9\-\(\)\s]+$/;
    return phoneRegex.test(phone) && phone.replace(/\D/g, '').length >= 8;
}

// Password strength checker
function checkPasswordStrength(event) {
    const password = event.target.value;
    const strengthIndicator = event.target.nextElementSibling;
    
    if (!strengthIndicator || !strengthIndicator.classList.contains('password-strength')) {
        return;
    }
    
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]/)) strength++;
    if (password.match(/[A-Z]/)) strength++;
    if (password.match(/[0-9]/)) strength++;
    if (password.match(/[^a-zA-Z0-9]/)) strength++;
    
    const levels = ['very-weak', 'weak', 'fair', 'good', 'strong'];
    const colors = ['#dc3545', '#fd7e14', '#ffc107', '#20c997', '#28a745'];
    
    strengthIndicator.className = `password-strength ${levels[strength]}`;
    strengthIndicator.style.background = colors[strength];
    strengthIndicator.style.width = `${(strength + 1) * 20}%`;
}

// Auto-resize textareas
function autoResizeTextarea(event) {
    const textarea = event.target;
    textarea.style.height = 'auto';
    textarea.style.height = textarea.scrollHeight + 'px';
}

// Utility functions
function copyToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
            showMessage('Copied to clipboard', 'success');
        }).catch(err => {
            console.error('Failed to copy: ', err);
            fallbackCopyTextToClipboard(text);
        });
    } else {
        fallbackCopyTextToClipboard(text);
    }
}

function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.position = "fixed";
    textArea.style.left = "-999999px";
    textArea.style.top = "-999999px";
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        document.execCommand('copy');
        showMessage('Copied to clipboard', 'success');
    } catch (err) {
        showMessage('Failed to copy to clipboard', 'error');
    }
    
    document.body.removeChild(textArea);
}

// Language switching
async function switchLanguage(language) {
    showLoading();
    
    try {
        const response = await fetch('/api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=switch_language&language=${language}`
        });
        
        const result = await response.json();
        
        if (result.success) {
            location.reload();
        } else {
            showMessage('Failed to switch language', 'error');
        }
    } catch (error) {
        showMessage('Network error occurred', 'error');
        console.error('Language switch error:', error);
    } finally {
        hideLoading();
    }
}

// AJAX helper function
async function makeRequest(url, options = {}) {
    const defaultOptions = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        },
        credentials: 'same-origin'
    };
    
    const config = { ...defaultOptions, ...options };
    
    try {
        const response = await fetch(url, config);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return await response.json();
        } else {
            return await response.text();
        }
    } catch (error) {
        console.error('Request failed:', error);
        throw error;
    }
}

// Survey management functions
async function createSurvey(title, description) {
    showLoading();
    
    const formData = new FormData();
    formData.append('action', 'create_survey');
    formData.append('title', title);
    formData.append('description', description);
    
    try {
        const result = await makeRequest('/api.php', {
            method: 'POST',
            body: formData
        });
        
        if (result.success) {
            showMessage(result.message, 'success');
            return result.data;
        } else {
            showMessage(result.message, 'error');
            return null;
        }
    } catch (error) {
        showMessage('Failed to create survey', 'error');
        return null;
    } finally {
        hideLoading();
    }
}

async function updateSurvey(surveyId, title, description, status) {
    showLoading();
    
    const formData = new FormData();
    formData.append('action', 'update_survey');
    formData.append('survey_id', surveyId);
    formData.append('title', title);
    formData.append('description', description);
    if (status) {
        formData.append('status', status);
    }
    
    try {
        const result = await makeRequest('/api.php', {
            method: 'POST',
            body: formData
        });
        
        if (result.success) {
            showMessage(result.message, 'success');
            return true;
        } else {
            showMessage(result.message, 'error');
            return false;
        }
    } catch (error) {
        showMessage('Failed to update survey', 'error');
        return false;
    } finally {
        hideLoading();
    }
}

// Question management
async function addQuestion(surveyId, questionData) {
    const formData = new FormData();
    formData.append('action', 'add_question');
    formData.append('survey_id', surveyId);
    formData.append('question_text', questionData.text);
    formData.append('question_type', questionData.type);
    formData.append('required', questionData.required ? 'true' : 'false');
    formData.append('order_index', questionData.orderIndex || 0);
    
    if (questionData.options) {
        formData.append('options', JSON.stringify(questionData.options));
    }
    
    try {
        const result = await makeRequest('/api.php', {
            method: 'POST',
            body: formData
        });
        
        if (result.success) {
            return result.data;
        } else {
            showMessage(result.message, 'error');
            return null;
        }
    } catch (error) {
        showMessage('Failed to add question', 'error');
        return null;
    }
}

async function updateQuestion(questionId, questionData) {
    const formData = new FormData();
    formData.append('action', 'update_question');
    formData.append('question_id', questionId);
    formData.append('question_text', questionData.text);
    formData.append('question_type', questionData.type);
    formData.append('required', questionData.required ? 'true' : 'false');
    
    if (questionData.options) {
        formData.append('options', JSON.stringify(questionData.options));
    }
    
    try {
        const result = await makeRequest('/api.php', {
            method: 'POST',
            body: formData
        });
        
        if (result.success) {
            return true;
        } else {
            showMessage(result.message, 'error');
            return false;
        }
    } catch (error) {
        showMessage('Failed to update question', 'error');
        return false;
    }
}

async function deleteQuestion(questionId) {
    if (!confirm('Are you sure you want to delete this question?')) {
        return false;
    }
    
    const formData = new FormData();
    formData.append('action', 'delete_question');
    formData.append('question_id', questionId);
    
    try {
        const result = await makeRequest('/api.php', {
            method: 'POST',
            body: formData
        });
        
        if (result.success) {
            return true;
        } else {
            showMessage(result.message, 'error');
            return false;
        }
    } catch (error) {
        showMessage('Failed to delete question', 'error');
        return false;
    }
}

// Theme management
async function loadPredefinedThemes() {
    try {
        const result = await makeRequest('/api.php?action=get_predefined_themes');
        
        if (result.success) {
            return result.data;
        } else {
            console.error('Failed to load predefined themes');
            return [];
        }
    } catch (error) {
        console.error('Failed to load predefined themes:', error);
        return [];
    }
}

async function loadCustomThemes() {
    try {
        const result = await makeRequest('/api.php?action=get_custom_themes');
        
        if (result.success) {
            return result.data;
        } else {
            console.error('Failed to load custom themes');
            return [];
        }
    } catch (error) {
        console.error('Failed to load custom themes:', error);
        return [];
    }
}

async function createCustomTheme(name, cssVariables) {
    const formData = new FormData();
    formData.append('action', 'create_custom_theme');
    formData.append('name', name);
    formData.append('css_variables', JSON.stringify(cssVariables));
    
    try {
        const result = await makeRequest('/api.php', {
            method: 'POST',
            body: formData
        });
        
        if (result.success) {
            showMessage(result.message, 'success');
            return result.data;
        } else {
            showMessage(result.message, 'error');
            return null;
        }
    } catch (error) {
        showMessage('Failed to create theme', 'error');
        return null;
    }
}

// Survey taking functions
async function submitSurveyResponse(surveyLinkId, responseData) {
    showLoading();
    
    const formData = new FormData();
    formData.append('action', 'submit_response');
    formData.append('survey_link_id', surveyLinkId);
    
    // Add all response data
    for (const [key, value] of Object.entries(responseData)) {
        if (Array.isArray(value)) {
            formData.append(`responses[${key}]`, JSON.stringify(value));
        } else {
            formData.append(`responses[${key}]`, value);
        }
    }
    
    try {
        const result = await makeRequest('/api.php', {
            method: 'POST',
            body: formData
        });
        
        if (result.success) {
            showMessage(result.message, 'success');
            return true;
        } else {
            showMessage(result.message, 'error');
            return false;
        }
    } catch (error) {
        showMessage('Failed to submit survey', 'error');
        return false;
    } finally {
        hideLoading();
    }
}

// Data loading functions
async function loadUserSurveys() {
    try {
        const result = await makeRequest('/api.php?action=get_user_surveys');
        
        if (result.success) {
            return result.data;
        } else {
            console.error('Failed to load surveys');
            return [];
        }
    } catch (error) {
        console.error('Failed to load surveys:', error);
        return [];
    }
}

async function loadSurveyQuestions(surveyId) {
    try {
        const result = await makeRequest(`/api.php?action=get_survey_questions&survey_id=${surveyId}`);
        
        if (result.success) {
            return result.data;
        } else {
            console.error('Failed to load questions');
            return [];
        }
    } catch (error) {
        console.error('Failed to load questions:', error);
        return [];
    }
}

async function loadSurveyResponses(surveyId) {
    try {
        const result = await makeRequest(`/api.php?action=get_survey_responses&survey_id=${surveyId}`);
        
        if (result.success) {
            return result.data;
        } else {
            console.error('Failed to load responses');
            return { responses: [], questions: [] };
        }
    } catch (error) {
        console.error('Failed to load responses:', error);
        return { responses: [], questions: [] };
    }
}

// Utility functions
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
}

function formatDate(dateString) {
    const options = { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

function formatNumber(number) {
    return new Intl.NumberFormat().format(number);
}

// Error handling
window.addEventListener('error', function(event) {
    console.error('Global error caught:', event.error);
    showMessage('An unexpected error occurred', 'error');
});

window.addEventListener('unhandledrejection', function(event) {
    console.error('Unhandled promise rejection:', event.reason);
    showMessage('An unexpected error occurred', 'error');
});

// Performance monitoring
if ('performance' in window) {
    window.addEventListener('load', function() {
        setTimeout(() => {
            const navigation = performance.getEntriesByType('navigation')[0];
            const loadTime = navigation.loadEventEnd - navigation.loadEventStart;
            console.log(`Page load time: ${loadTime}ms`);
        }, 0);
    });
}

// Service Worker registration (optional)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        // navigator.registerServiceWorker('/sw.js') - uncomment if you add a service worker
    });
}

// Export functions for use in other scripts
window.SurveyPlatform = {
    // Authentication
    handleLogin,
    handleRegister,
    logout,
    
    // Modals
    showModal,
    closeModal,
    showLoginModal,
    showRegisterModal,
    
    // Messages
    showMessage,
    hideMessage,
    hideMessages,
    
    // Loading
    showLoading,
    hideLoading,
    
    // Forms
    validateForm,
    
    // Surveys
    createSurvey,
    updateSurvey,
    loadUserSurveys,
    loadSurveyQuestions,
    loadSurveyResponses,
    submitSurveyResponse,
    
    // Questions
    addQuestion,
    updateQuestion,
    deleteQuestion,
    
    // Themes
    loadPredefinedThemes,
    loadCustomThemes,
    createCustomTheme,
    
    // Utilities
    copyToClipboard,
    debounce,
    throttle,
    formatDate,
    formatNumber,
    isValidEmail,
    isValidPhone
};

console.log('Survey Platform JavaScript loaded successfully');
