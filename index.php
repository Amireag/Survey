<?php
$title = 'Survey Platform';
$bodyClass = 'homepage';

require_once __DIR__ . '/core/functions.php';
initializeSystem();

$user = getCurrentUser();

// If user is logged in, redirect to appropriate dashboard
if ($user) {
    if ($user['role'] === 'admin') {
        redirectTo('admin/');
    } else {
        redirectTo('creator/');
    }
    exit;
}

require_once __DIR__ . '/templates/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <h1><?php echo t('create_beautiful_surveys'); ?></h1>
                <p><?php echo t('powerful_survey_platform_description'); ?></p>
                <div class="hero-actions">
                    <button onclick="showRegisterModal()" class="btn btn-primary btn-lg">
                        <?php echo t('get_started_free'); ?>
                    </button>
                    <button onclick="showLoginModal()" class="btn btn-outline btn-lg">
                        <?php echo t('sign_in'); ?>
                    </button>
                </div>
                <div class="hero-features">
                    <div class="feature-item">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span><?php echo t('unlimited_responses'); ?></span>
                    </div>
                    <div class="feature-item">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span><?php echo t('custom_themes'); ?></span>
                    </div>
                    <div class="feature-item">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span><?php echo t('real_time_analytics'); ?></span>
                    </div>
                </div>
            </div>
            <div class="hero-image">
                <div class="hero-graphic">
                    <svg width="400" height="300" viewBox="0 0 400 300" fill="none">
                        <!-- Survey Form Illustration -->
                        <rect x="50" y="50" width="300" height="200" rx="20" fill="url(#heroGradient)" opacity="0.1"/>
                        <rect x="70" y="70" width="260" height="160" rx="15" fill="white" stroke="url(#heroGradient)" stroke-width="2"/>
                        
                        <!-- Form Elements -->
                        <rect x="90" y="90" width="220" height="15" rx="7" fill="#f1f5f9"/>
                        <rect x="90" y="115" width="180" height="15" rx="7" fill="#f1f5f9"/>
                        <rect x="90" y="140" width="200" height="15" rx="7" fill="#f1f5f9"/>
                        
                        <circle cx="95" cy="170" r="6" fill="none" stroke="#8B5CF6" stroke-width="2"/>
                        <circle cx="95" cy="190" r="6" fill="none" stroke="#8B5CF6" stroke-width="2"/>
                        <circle cx="95" cy="210" r="6" fill="#8B5CF6"/>
                        
                        <rect x="260" y="200" width="50" height="25" rx="12" fill="url(#heroGradient)"/>
                        
                        <defs>
                            <linearGradient id="heroGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#8B5CF6"/>
                                <stop offset="50%" style="stop-color:#A78BFA"/>
                                <stop offset="100%" style="stop-color:#EC4899"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <div class="section-header">
            <h2><?php echo t('powerful_features'); ?></h2>
            <p><?php echo t('everything_you_need_for_surveys'); ?></p>
        </div>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <h3><?php echo t('drag_drop_builder'); ?></h3>
                <p><?php echo t('create_surveys_easily'); ?></p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h4a2 2 0 002-2V9a2 2 0 00-2-2H7a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3><?php echo t('custom_branding'); ?></h3>
                <p><?php echo t('brand_your_surveys'); ?></p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 00-2-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 00-2 2"/>
                    </svg>
                </div>
                <h3><?php echo t('advanced_analytics'); ?></h3>
                <p><?php echo t('detailed_insights'); ?></p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h3><?php echo t('secure_reliable'); ?></h3>
                <p><?php echo t('enterprise_security'); ?></p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                </div>
                <h3><?php echo t('easy_sharing'); ?></h3>
                <p><?php echo t('share_surveys_anywhere'); ?></p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                    </svg>
                </div>
                <h3><?php echo t('multilingual'); ?></h3>
                <p><?php echo t('support_multiple_languages'); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2><?php echo t('ready_to_get_started'); ?></h2>
            <p><?php echo t('join_thousands_of_users'); ?></p>
            <div class="cta-actions">
                <button onclick="showRegisterModal()" class="btn btn-primary btn-lg">
                    <?php echo t('start_free_trial'); ?>
                </button>
                <a href="/demo" class="btn btn-outline btn-lg">
                    <?php echo t('view_demo'); ?>
                </a>
            </div>
        </div>
    </div>
</section>

<style>
/* Homepage specific styles */
.homepage .main-header {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
}

.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 120px 0 80px;
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.05"><circle cx="30" cy="30" r="4"/></g></svg>');
    opacity: 0.5;
}

.hero-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--spacing-xxl);
    align-items: center;
    position: relative;
    z-index: 1;
}

.hero-text {
    max-width: 600px;
}

.hero-section h1 {
    font-size: 3.5rem;
    font-weight: 700;
    line-height: 1.1;
    margin-bottom: var(--spacing-lg);
    color: white;
}

.hero-section p {
    font-size: 1.25rem;
    opacity: 0.9;
    margin-bottom: var(--spacing-xl);
    line-height: 1.6;
}

.hero-actions {
    display: flex;
    gap: var(--spacing-lg);
    margin-bottom: var(--spacing-xl);
}

.hero-features {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-md);
}

.feature-item {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    font-size: 1rem;
    color: white;
    opacity: 0.9;
}

.hero-image {
    display: flex;
    justify-content: center;
    align-items: center;
}

.hero-graphic {
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

.features-section {
    padding: var(--spacing-xxl) 0;
    background: var(--background-color);
}

.section-header {
    text-align: center;
    max-width: 600px;
    margin: 0 auto var(--spacing-xxl);
}

.section-header h2 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: var(--spacing-md);
    color: var(--text-color);
}

.section-header p {
    font-size: 1.125rem;
    color: var(--secondary-color);
    margin: 0;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: var(--spacing-xl);
}

.feature-card {
    background: white;
    padding: var(--spacing-xl);
    border-radius: var(--border-radius-xl);
    box-shadow: var(--shadow-md);
    text-align: center;
    transition: all var(--transition-normal);
    border: 1px solid var(--border-color);
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.feature-icon {
    width: 80px;
    height: 80px;
    border-radius: var(--border-radius-xl);
    background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto var(--spacing-lg);
}

.feature-card h3 {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: var(--spacing-md);
    color: var(--text-color);
}

.feature-card p {
    color: var(--secondary-color);
    margin: 0;
    line-height: 1.6;
}

.cta-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: var(--spacing-xxl) 0;
    text-align: center;
}

.cta-content h2 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: var(--spacing-md);
    color: white;
}

.cta-content p {
    font-size: 1.125rem;
    opacity: 0.9;
    margin-bottom: var(--spacing-xl);
}

.cta-actions {
    display: flex;
    gap: var(--spacing-lg);
    justify-content: center;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .hero-content {
        grid-template-columns: 1fr;
        text-align: center;
        gap: var(--spacing-xl);
    }
    
    .hero-section h1 {
        font-size: 2.5rem;
    }
    
    .hero-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .features-grid {
        grid-template-columns: 1fr;
    }
    
    .cta-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .section-header h2 {
        font-size: 2rem;
    }
    
    .cta-content h2 {
        font-size: 2rem;
    }
}

@media (max-width: 480px) {
    .hero-section {
        padding: 80px 0 60px;
    }
    
    .hero-section h1 {
        font-size: 2rem;
    }
    
    .hero-actions .btn {
        width: 100%;
    }
    
    .feature-card {
        padding: var(--spacing-lg);
    }
    
    .features-section,
    .cta-section {
        padding: var(--spacing-xl) 0;
    }
}
</style>

<?php require_once __DIR__ . '/templates/footer.php'; ?>
