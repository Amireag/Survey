<?php
$title = t('dashboard') . ' - Admin';
$adminTheme = true;
$bodyClass = 'admin-dashboard';

require_once __DIR__ . '/../core/functions.php';
initializeSystem();
requireRole('admin');

$user = getCurrentUser();
$stats = getDashboardStats();

require_once __DIR__ . '/../templates/header.php';
?>

<div class="admin-layout">
    <!-- Admin Sidebar -->
    <nav class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <svg width="32" height="32" viewBox="0 0 40 40" fill="none">
                    <circle cx="20" cy="20" r="20" fill="url(#logoGradient)"/>
                    <path d="M15 12h10v4h-6v12h-4V12z" fill="white"/>
                    <defs>
                        <linearGradient id="logoGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#8B5CF6"/>
                            <stop offset="100%" style="stop-color:#A78BFA"/>
                        </linearGradient>
                    </defs>
                </svg>
                <span class="sidebar-title">Survey Admin</span>
            </div>
            <button class="sidebar-toggle" onclick="toggleSidebar()">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
        
        <div class="sidebar-menu">
            <div class="menu-section">
                <div class="menu-title"><?php echo t('main_menu'); ?></div>
                <ul class="menu-items">
                    <li class="menu-item active">
                        <a href="<?php echo getBaseUrl(); ?>/admin/" class="menu-link">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 7V5a2 2 0 012-2h4a2 2 0 012 2v2m-6 4h4"/>
                            </svg>
                            <span><?php echo t('dashboard'); ?></span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="<?php echo getBaseUrl(); ?>/admin/users.php" class="menu-link">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                            </svg>
                            <span><?php echo t('users'); ?></span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="<?php echo getBaseUrl(); ?>/admin/surveys.php" class="menu-link">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                            <span><?php echo t('surveys'); ?></span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="<?php echo getBaseUrl(); ?>/admin/statistics.php" class="menu-link">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 00-2-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 00-2 2"/>
                            </svg>
                            <span><?php echo t('statistics'); ?></span>
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="menu-section">
                <div class="menu-title"><?php echo t('management'); ?></div>
                <ul class="menu-items">
                    <li class="menu-item">
                        <a href="<?php echo getBaseUrl(); ?>/admin/themes.php" class="menu-link">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h4a2 2 0 002-2V9a2 2 0 00-2-2H7a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                            </svg>
                            <span><?php echo t('themes'); ?></span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="<?php echo getBaseUrl(); ?>/admin/settings.php" class="menu-link">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span><?php echo t('settings'); ?></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="sidebar-footer">
            <div class="user-profile">
                <div class="user-avatar">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div class="user-details">
                    <div class="user-name"><?php echo htmlspecialchars($user['email']); ?></div>
                    <div class="user-role">Administrator</div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Admin Content -->
    <div class="admin-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-title">
                <h1><?php echo t('dashboard'); ?></h1>
                <p><?php echo t('welcome_back'); ?>, <?php echo htmlspecialchars($user['email']); ?>!</p>
            </div>
            <div class="page-actions">
                <button class="btn btn-primary" onclick="refreshStats()">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <?php echo t('refresh'); ?>
                </button>
            </div>
        </div>

        <!-- Dashboard Stats -->
        <div class="dashboard-grid">
            <!-- Key Metrics -->
            <div class="stats-row">
                <div class="stat-card primary">
                    <div class="stat-icon">
                        <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value" data-animate="<?php echo $stats['total_users']; ?>">0</div>
                        <div class="stat-label"><?php echo t('total_users'); ?></div>
                    </div>
                </div>
                
                <div class="stat-card success">
                    <div class="stat-icon">
                        <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value" data-animate="<?php echo $stats['total_surveys']; ?>">0</div>
                        <div class="stat-label"><?php echo t('total_surveys'); ?></div>
                    </div>
                </div>
                
                <div class="stat-card info">
                    <div class="stat-icon">
                        <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value" data-animate="<?php echo $stats['total_responses']; ?>">0</div>
                        <div class="stat-label"><?php echo t('total_responses'); ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Credit Statistics -->
            <div class="stats-row">
                <div class="stat-card warning">
                    <div class="stat-icon">
                        <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value" data-animate="<?php echo $stats['total_credits_used']; ?>">0</div>
                        <div class="stat-label"><?php echo t('credits_used'); ?></div>
                    </div>
                </div>
                
                <div class="stat-card success">
                    <div class="stat-icon">
                        <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value" data-animate="<?php echo $stats['total_credits_available']; ?>">0</div>
                        <div class="stat-label"><?php echo t('credits_available'); ?></div>
                    </div>
                </div>
                
                <div class="stat-card primary">
                    <div class="stat-icon">
                        <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value" data-animate="<?php echo $stats['total_credits_bought']; ?>">0</div>
                        <div class="stat-label"><?php echo t('credits_bought'); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Tables -->
        <div class="dashboard-content">
            <div class="content-row">
                <!-- Subscription Breakdown -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3><?php echo t('users_by_subscription'); ?></h3>
                    </div>
                    <div class="card-content">
                        <div class="subscription-breakdown">
                            <?php foreach ($stats['users_by_subscription'] as $sub): ?>
                            <div class="subscription-item">
                                <div class="subscription-info">
                                    <span class="subscription-name"><?php echo t('subscription_level_' . $sub['subscription_level']); ?></span>
                                    <span class="subscription-count"><?php echo $sub['count']; ?> <?php echo t('users'); ?></span>
                                </div>
                                <div class="subscription-bar">
                                    <div class="bar-fill" style="width: <?php echo ($sub['count'] / $stats['total_users']) * 100; ?>%"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Top Users -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3><?php echo t('top_users'); ?></h3>
                    </div>
                    <div class="card-content">
                        <div class="top-users-list">
                            <?php foreach (array_slice($stats['top_users'], 0, 5) as $index => $user): ?>
                            <div class="user-item">
                                <div class="user-rank">#<?php echo $index + 1; ?></div>
                                <div class="user-info">
                                    <div class="user-name"><?php echo htmlspecialchars($user['email']); ?></div>
                                    <div class="user-stats"><?php echo $user['credits_used']; ?> <?php echo t('credits_used'); ?></div>
                                </div>
                                <div class="user-badge">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Top Surveys -->
            <div class="dashboard-card full-width">
                <div class="card-header">
                    <h3><?php echo t('top_surveys'); ?></h3>
                </div>
                <div class="card-content">
                    <div class="surveys-table-wrapper">
                        <table class="surveys-table">
                            <thead>
                                <tr>
                                    <th><?php echo t('survey_title'); ?></th>
                                    <th><?php echo t('creator'); ?></th>
                                    <th><?php echo t('survey_responses'); ?></th>
                                    <th><?php echo t('survey_link'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($stats['top_surveys'], 0, 10) as $survey): ?>
                                <tr>
                                    <td>
                                        <div class="survey-title"><?php echo htmlspecialchars($survey['title']); ?></div>
                                    </td>
                                    <td>
                                        <div class="creator-email"><?php echo htmlspecialchars($survey['creator_email']); ?></div>
                                    </td>
                                    <td>
                                        <div class="response-count"><?php echo $survey['response_count']; ?></div>
                                    </td>
                                    <td>
                                        <div class="survey-link">
                                            <code><?php echo $survey['survey_id']; ?></code>
                                            <button class="btn-copy" onclick="copyToClipboard('/survey/<?php echo $survey['survey_id']; ?>')" title="<?php echo t('copy_link'); ?>">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom inline JavaScript -->
<script>
// Initialize dashboard
document.addEventListener('DOMContentLoaded', function() {
    animateNumbers();
    initializeCharts();
});

// Animate stat numbers
function animateNumbers() {
    const statElements = document.querySelectorAll('[data-animate]');
    statElements.forEach(element => {
        const target = parseInt(element.dataset.animate);
        let current = 0;
        const increment = target / 100;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            element.textContent = Math.floor(current).toLocaleString();
        }, 20);
    });
}

// Toggle sidebar
function toggleSidebar() {
    document.getElementById('adminSidebar').classList.toggle('collapsed');
}

// Refresh stats
function refreshStats() {
    showLoading();
    location.reload();
}

// Copy to clipboard
function copyToClipboard(text) {
    const baseUrl = window.location.origin;
    navigator.clipboard.writeText(baseUrl + text).then(() => {
        showMessage('<?php echo t("link_copied"); ?>', 'success');
    });
}
</script>

<?php
$inlineJS = "
// Additional admin dashboard initialization
console.log('Admin Dashboard Loaded');
";

require_once __DIR__ . '/../templates/footer.php';
?>
