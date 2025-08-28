<?php
$title = t('dashboard') . ' - Creator';
$bodyClass = 'creator-dashboard';

require_once __DIR__ . '/../core/functions.php';
initializeSystem();
requireAuth();

$user = getCurrentUser();
if ($user['role'] !== 'creator') {
    header('Location: /admin/');
    exit;
}

$surveys = getSurveysByUser($user['id']);

require_once __DIR__ . '/../templates/header.php';
?>

<div class="creator-layout">
    <!-- Creator Header -->
    <div class="creator-header">
        <div class="container">
            <div class="header-content">
                <div class="user-welcome">
                    <h1><?php echo t('welcome_back'); ?>, <?php echo htmlspecialchars($user['email']); ?>!</h1>
                    <p><?php echo t('manage_your_surveys_and_themes'); ?></p>
                </div>
                <div class="user-stats">
                    <div class="stat-item">
                        <div class="stat-value"><?php echo count($surveys); ?></div>
                        <div class="stat-label"><?php echo t('total_surveys'); ?></div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo array_sum(array_column($surveys, 'response_count')); ?></div>
                        <div class="stat-label"><?php echo t('total_responses'); ?></div>
                    </div>
                    <div class="stat-item credits">
                        <div class="stat-value"><?php echo $user['credits']; ?></div>
                        <div class="stat-label"><?php echo t('credits_available'); ?></div>
                    </div>
                    <div class="stat-item subscription">
                        <div class="stat-value"><?php echo t('subscription_level_' . $user['subscription_level']); ?></div>
                        <div class="stat-label"><?php echo t('subscription_plan'); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Creator Content -->
    <div class="creator-content">
        <div class="container">
            <!-- Quick Actions -->
            <div class="quick-actions">
                <a href="/creator/create.php" class="action-card primary">
                    <div class="action-icon">
                        <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <div class="action-content">
                        <h3><?php echo t('create_survey'); ?></h3>
                        <p><?php echo t('start_new_survey'); ?></p>
                    </div>
                </a>
                
                <a href="/creator/themes.php" class="action-card success">
                    <div class="action-icon">
                        <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h4a2 2 0 002-2V9a2 2 0 00-2-2H7a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="action-content">
                        <h3><?php echo t('themes'); ?></h3>
                        <p><?php echo t('customize_appearance'); ?></p>
                    </div>
                </a>
                
                <a href="/creator/analytics.php" class="action-card info">
                    <div class="action-icon">
                        <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 00-2-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 00-2 2"/>
                        </svg>
                    </div>
                    <div class="action-content">
                        <h3><?php echo t('analytics'); ?></h3>
                        <p><?php echo t('view_detailed_analytics'); ?></p>
                    </div>
                </a>
            </div>

            <!-- Recent Surveys -->
            <div class="dashboard-section">
                <div class="section-header">
                    <h2><?php echo t('your_surveys'); ?></h2>
                    <div class="section-actions">
                        <select id="statusFilter" onchange="filterSurveys()">
                            <option value=""><?php echo t('all_statuses'); ?></option>
                            <option value="draft"><?php echo t('draft'); ?></option>
                            <option value="active"><?php echo t('active'); ?></option>
                            <option value="closed"><?php echo t('closed'); ?></option>
                        </select>
                    </div>
                </div>

                <?php if (empty($surveys)): ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <h3><?php echo t('no_surveys_yet'); ?></h3>
                    <p><?php echo t('create_your_first_survey'); ?></p>
                    <a href="/creator/create.php" class="btn btn-primary"><?php echo t('create_survey'); ?></a>
                </div>
                <?php else: ?>
                <div class="surveys-grid" id="surveysGrid">
                    <?php foreach ($surveys as $survey): ?>
                    <div class="survey-card" data-status="<?php echo $survey['status']; ?>">
                        <div class="survey-header">
                            <div class="survey-title">
                                <h3><?php echo htmlspecialchars($survey['title']); ?></h3>
                                <div class="survey-meta">
                                    <span class="survey-id"><?php echo $survey['survey_id']; ?></span>
                                    <span class="survey-date"><?php echo date('M j, Y', strtotime($survey['created_at'])); ?></span>
                                </div>
                            </div>
                            <div class="survey-status">
                                <span class="status-badge status-<?php echo $survey['status']; ?>">
                                    <?php echo t($survey['status']); ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="survey-body">
                            <?php if ($survey['description']): ?>
                            <p class="survey-description"><?php echo htmlspecialchars($survey['description']); ?></p>
                            <?php endif; ?>
                            
                            <div class="survey-stats">
                                <div class="stat">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                    <span><?php echo $survey['response_count']; ?> <?php echo t('responses'); ?></span>
                                </div>
                                <div class="stat">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span><?php echo date('M j', strtotime($survey['updated_at'])); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="survey-footer">
                            <div class="survey-actions">
                                <button class="btn btn-sm btn-outline" onclick="viewSurvey('<?php echo $survey['survey_id']; ?>')" title="<?php echo t('view'); ?>">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <?php echo t('view'); ?>
                                </button>
                                
                                <button class="btn btn-sm btn-primary" onclick="editSurvey(<?php echo $survey['id']; ?>)" title="<?php echo t('edit'); ?>">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    <?php echo t('edit'); ?>
                                </button>
                                
                                <?php if ($survey['response_count'] > 0): ?>
                                <button class="btn btn-sm btn-success" onclick="viewResults(<?php echo $survey['id']; ?>)" title="<?php echo t('view_results'); ?>">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 00-2-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 00-2 2"/>
                                    </svg>
                                    <?php echo t('results'); ?>
                                </button>
                                <?php endif; ?>
                                
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline dropdown-toggle" onclick="toggleDropdown(this)">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                        </svg>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a href="#" onclick="duplicateSurvey(<?php echo $survey['id']; ?>)" class="dropdown-item">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                            <?php echo t('duplicate_survey'); ?>
                                        </a>
                                        <a href="#" onclick="shareSurvey('<?php echo $survey['survey_id']; ?>')" class="dropdown-item">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                            </svg>
                                            <?php echo t('share_survey'); ?>
                                        </a>
                                        <?php if ($survey['response_count'] > 0): ?>
                                        <a href="/api.php?action=export_results&survey_id=<?php echo $survey['id']; ?>" class="dropdown-item">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <?php echo t('export_results'); ?>
                                        </a>
                                        <?php endif; ?>
                                        <div class="dropdown-divider"></div>
                                        <a href="#" onclick="deleteSurvey(<?php echo $survey['id']; ?>)" class="dropdown-item danger">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            <?php echo t('delete'); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Recent Activity -->
            <div class="dashboard-section">
                <div class="section-header">
                    <h2><?php echo t('recent_activity'); ?></h2>
                </div>
                
                <div class="activity-feed">
                    <div class="activity-item">
                        <div class="activity-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title"><?php echo t('welcome_to_survey_platform'); ?></div>
                            <div class="activity-description"><?php echo t('start_by_creating_your_first_survey'); ?></div>
                            <div class="activity-time"><?php echo t('just_now'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Share Survey Modal -->
<div id="shareSurveyModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><?php echo t('share_survey'); ?></h2>
            <button class="modal-close" onclick="closeModal('shareSurveyModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label><?php echo t('survey_link'); ?></label>
                <div class="link-display">
                    <input type="text" id="surveyShareLink" readonly>
                    <button class="btn btn-primary" onclick="copyShareLink()"><?php echo t('copy_link'); ?></button>
                </div>
            </div>
            <div class="share-options">
                <h4><?php echo t('share_via'); ?></h4>
                <div class="share-buttons">
                    <button class="btn btn-outline" onclick="shareViaEmail()">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <?php echo t('email'); ?>
                    </button>
                    <button class="btn btn-outline" onclick="shareViaSMS()">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <?php echo t('sms'); ?>
                    </button>
                    <button class="btn btn-outline" onclick="shareViaWhatsApp()">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.465 3.63"/>
                        </svg>
                        WhatsApp
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentSurveyLink = '';

// View survey
function viewSurvey(surveyId) {
    window.open('/survey/' + surveyId, '_blank');
}

// Edit survey
function editSurvey(surveyId) {
    window.location.href = '/creator/edit.php?id=' + surveyId;
}

// View results
function viewResults(surveyId) {
    window.location.href = '/creator/results.php?id=' + surveyId;
}

// Duplicate survey
async function duplicateSurvey(surveyId) {
    showLoading();
    // Implementation for duplicate functionality
    hideLoading();
    showMessage('Duplicate functionality coming soon', 'info');
}

// Delete survey
async function deleteSurvey(surveyId) {
    if (!confirm('<?php echo t("are_you_sure_delete_survey"); ?>')) {
        return;
    }
    
    showLoading();
    
    const formData = new FormData();
    formData.append('action', 'delete_survey');
    formData.append('survey_id', surveyId);
    
    try {
        const response = await fetch('/api.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMessage(result.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showMessage(result.message, 'error');
        }
    } catch (error) {
        showMessage('Network error occurred', 'error');
    } finally {
        hideLoading();
    }
}

// Share survey
function shareSurvey(surveyId) {
    currentSurveyLink = window.location.origin + '/survey/' + surveyId;
    document.getElementById('surveyShareLink').value = currentSurveyLink;
    document.getElementById('shareSurveyModal').style.display = 'flex';
}

// Copy share link
function copyShareLink() {
    const linkInput = document.getElementById('surveyShareLink');
    linkInput.select();
    navigator.clipboard.writeText(linkInput.value).then(() => {
        showMessage('<?php echo t("link_copied"); ?>', 'success');
    });
}

// Share via email
function shareViaEmail() {
    const subject = encodeURIComponent('<?php echo t("survey_invitation"); ?>');
    const body = encodeURIComponent('<?php echo t("please_participate_in_survey"); ?>: ' + currentSurveyLink);
    window.open(`mailto:?subject=${subject}&body=${body}`);
}

// Share via SMS
function shareViaSMS() {
    const message = encodeURIComponent('<?php echo t("please_participate_in_survey"); ?>: ' + currentSurveyLink);
    window.open(`sms:?body=${message}`);
}

// Share via WhatsApp
function shareViaWhatsApp() {
    const message = encodeURIComponent('<?php echo t("please_participate_in_survey"); ?>: ' + currentSurveyLink);
    window.open(`https://wa.me/?text=${message}`);
}

// Filter surveys
function filterSurveys() {
    const filter = document.getElementById('statusFilter').value;
    const cards = document.querySelectorAll('.survey-card');
    
    cards.forEach(card => {
        if (filter === '' || card.dataset.status === filter) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

// Toggle dropdown
function toggleDropdown(button) {
    const dropdown = button.nextElementSibling;
    const isOpen = dropdown.style.display === 'block';
    
    // Close all dropdowns
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        menu.style.display = 'none';
    });
    
    // Toggle current dropdown
    if (!isOpen) {
        dropdown.style.display = 'block';
    }
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.dropdown')) {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.style.display = 'none';
        });
    }
});
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
