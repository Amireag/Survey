<?php
require_once __DIR__ . '/../core/functions.php';
initializeSystem();

$user = getCurrentUser();
$i18n = I18n::getInstance();
?>
<!DOCTYPE html>
<html lang="<?php echo $i18n->getCurrentLanguage(); ?>" dir="<?php echo dir(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? t('dashboard') . ' - Survey Platform'; ?></title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/public/css/main.css">
    <?php if (isset($adminTheme) && $adminTheme): ?>
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/public/css/admin-cosmic.css">
    <?php endif; ?>
    
    <!-- Font Loading -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Persian Font -->
    <style>
        @font-face {
            font-family: 'Morabba';
            src: url('<?php echo getBaseUrl(); ?>/public/fonts/morabba.woff2') format('woff2');
            font-weight: 300 700;
            font-display: swap;
        }
        
        :root {
            --font-primary: <?php echo font(); ?>, sans-serif;
        }
        
        body {
            font-family: var(--font-primary);
            direction: <?php echo dir(); ?>;
        }
    </style>
    
    <!-- Dynamic Theme Variables -->
    <?php if (isset($themeVariables) && $themeVariables): ?>
    <style>
        :root {
            <?php foreach ($themeVariables as $variable => $value): ?>
            <?php echo $variable; ?>: <?php echo $value; ?>;
            <?php endforeach; ?>
        }
    </style>
    <?php endif; ?>
</head>
<body class="<?php echo $bodyClass ?? ''; ?>">
    
    <!-- Language Switcher -->
    <div class="language-switcher">
        <a href="<?php echo $i18n->getLanguageSwitchUrl('en'); ?>" class="lang-btn <?php echo $i18n->getCurrentLanguage() === 'en' ? 'active' : ''; ?>">EN</a>
        <a href="<?php echo $i18n->getLanguageSwitchUrl('fa'); ?>" class="lang-btn <?php echo $i18n->getCurrentLanguage() === 'fa' ? 'active' : ''; ?>">فا</a>
    </div>

    <!-- Main Header -->
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="<?php echo getBaseUrl(); ?>/">
                        <img src="<?php echo getBaseUrl(); ?>/public/images/logo.svg" alt="Survey Platform" width="40" height="40">
                        <span>Survey Platform</span>
                    </a>
                </div>
                
                <nav class="main-nav">
                    <?php if ($user): ?>
                        <!-- Logged in navigation -->
                        <div class="nav-links">
                            <?php if ($user['role'] === 'admin'): ?>
                                <a href="<?php echo getBaseUrl(); ?>/admin/" class="nav-link"><?php echo t('dashboard'); ?></a>
                                <a href="<?php echo getBaseUrl(); ?>/admin/users.php" class="nav-link"><?php echo t('users'); ?></a>
                                <a href="<?php echo getBaseUrl(); ?>/admin/statistics.php" class="nav-link"><?php echo t('statistics'); ?></a>
                            <?php else: ?>
                                <a href="<?php echo getBaseUrl(); ?>/creator/" class="nav-link"><?php echo t('dashboard'); ?></a>
                                <a href="<?php echo getBaseUrl(); ?>/creator/surveys.php" class="nav-link"><?php echo t('surveys'); ?></a>
                                <a href="<?php echo getBaseUrl(); ?>/creator/create.php" class="nav-link"><?php echo t('create_survey'); ?></a>
                                <a href="<?php echo getBaseUrl(); ?>/creator/themes.php" class="nav-link"><?php echo t('themes'); ?></a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="user-menu">
                            <div class="user-info">
                                <span class="user-name"><?php echo htmlspecialchars($user['email']); ?></span>
                                <span class="user-role"><?php echo ucfirst($user['role']); ?></span>
                                <span class="user-credits"><?php echo $user['credits']; ?> <?php echo t('credits'); ?></span>
                            </div>
                            <div class="user-actions">
                                <button onclick="logout()" class="btn btn-outline"><?php echo t('logout'); ?></button>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Guest navigation -->
                        <div class="auth-buttons">
                            <button onclick="showLoginModal()" class="btn btn-primary"><?php echo t('login'); ?></button>
                            <button onclick="showRegisterModal()" class="btn btn-outline"><?php echo t('register'); ?></button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Login Modal -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><?php echo t('login_title'); ?></h2>
                <button class="modal-close" onclick="closeModal('loginModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="loginForm" onsubmit="handleLogin(event)">
                    <div class="form-group">
                        <label for="loginIdentifier"><?php echo t('email_or_phone'); ?></label>
                        <input type="text" id="loginIdentifier" name="identifier" required>
                    </div>
                    <div class="form-group">
                        <label for="loginPassword"><?php echo t('password'); ?></label>
                        <input type="password" id="loginPassword" name="password" required>
                    </div>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember_me">
                            <span class="checkmark"></span>
                            <?php echo t('remember_me'); ?>
                        </label>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-block"><?php echo t('login'); ?></button>
                    </div>
                    <div class="form-footer">
                        <a href="#" onclick="showForgotPassword()"><?php echo t('forgot_password'); ?></a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div id="registerModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><?php echo t('register_title'); ?></h2>
                <button class="modal-close" onclick="closeModal('registerModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="registerForm" onsubmit="handleRegister(event)">
                    <div class="form-group">
                        <label for="registerEmail"><?php echo t('user_email'); ?></label>
                        <input type="email" id="registerEmail" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="registerPhone"><?php echo t('user_phone'); ?></label>
                        <input type="tel" id="registerPhone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="registerPassword"><?php echo t('password'); ?></label>
                        <input type="password" id="registerPassword" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="registerConfirmPassword"><?php echo t('confirm_password'); ?></label>
                        <input type="password" id="registerConfirmPassword" name="confirm_password" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-block"><?php echo t('register'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <div id="messageContainer" class="message-container"></div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-spinner"></div>
        <div class="loading-text"><?php echo t('loading'); ?></div>
    </div>

    <!-- Main Content Wrapper -->
    <main class="main-content">
