<?php
$title = '403 - Access Forbidden';
$bodyClass = 'error-page';

require_once __DIR__ . '/../core/functions.php';
initializeSystem();

http_response_code(403);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - Survey Platform</title>
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/public/css/main.css">
    <style>
        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);
            color: white;
        }
        
        .error-content {
            text-align: center;
            max-width: 600px;
            padding: var(--spacing-xl);
        }
        
        .error-code {
            font-size: 6rem;
            font-weight: 700;
            margin-bottom: var(--spacing-lg);
            opacity: 0.8;
        }
        
        .error-title {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: var(--spacing-md);
        }
        
        .error-description {
            font-size: 1.125rem;
            opacity: 0.9;
            margin-bottom: var(--spacing-xl);
            line-height: 1.6;
        }
        
        .error-actions {
            display: flex;
            gap: var(--spacing-lg);
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .btn-primary:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        .btn-outline {
            background: transparent;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        
        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="<?php echo $bodyClass; ?>">
    <div class="error-container">
        <div class="error-content">
            <div class="error-code">403</div>
            <h1 class="error-title"><?php echo t('access_denied'); ?></h1>
            <p class="error-description">
                <?php echo t('access_forbidden_description'); ?>
            </p>
            <div class="error-actions">
                <a href="<?php echo getBaseUrl(); ?>/" class="btn btn-primary">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m0 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10M9 21h6"/>
                    </svg>
                    <?php echo t('go_home'); ?>
                </a>
                <button onclick="showLoginModal()" class="btn btn-outline">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <?php echo t('login'); ?>
                </button>
            </div>
        </div>
    </div>
</body>
</html>
