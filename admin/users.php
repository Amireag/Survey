<?php
$title = t('users') . ' - Admin';
$adminTheme = true;
$bodyClass = 'admin-users';

require_once __DIR__ . '/../core/functions.php';
initializeSystem();
requireRole('admin');

$user = getCurrentUser();
$users = getAllUsers();

require_once __DIR__ . '/../templates/header.php';
?>

<div class="admin-layout">
    <!-- Admin Sidebar (Same as dashboard) -->
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
                    <li class="menu-item">
                        <a href="/admin/" class="menu-link">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 7V5a2 2 0 012-2h4a2 2 0 012 2v2m-6 4h4"/>
                            </svg>
                            <span><?php echo t('dashboard'); ?></span>
                        </a>
                    </li>
                    <li class="menu-item active">
                        <a href="/admin/users.php" class="menu-link">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                            </svg>
                            <span><?php echo t('users'); ?></span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="/admin/surveys.php" class="menu-link">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                            <span><?php echo t('surveys'); ?></span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="/admin/statistics.php" class="menu-link">
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
                        <a href="/admin/themes.php" class="menu-link">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h4a2 2 0 002-2V9a2 2 0 00-2-2H7a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                            </svg>
                            <span><?php echo t('themes'); ?></span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="/admin/settings.php" class="menu-link">
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
                <h1><?php echo t('users'); ?></h1>
                <p><?php echo t('manage_users_and_permissions'); ?></p>
            </div>
            <div class="page-actions">
                <button class="btn btn-primary" onclick="showCreateUserModal()">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <?php echo t('create_user'); ?>
                </button>
            </div>
        </div>

        <!-- Users Management -->
        <div class="dashboard-content">
            <!-- User Statistics -->
            <div class="stats-row small">
                <div class="stat-card primary">
                    <div class="stat-content">
                        <div class="stat-value"><?php echo count($users); ?></div>
                        <div class="stat-label"><?php echo t('total_users'); ?></div>
                    </div>
                </div>
                <div class="stat-card success">
                    <div class="stat-content">
                        <div class="stat-value"><?php echo count(array_filter($users, fn($u) => $u['role'] === 'creator')); ?></div>
                        <div class="stat-label"><?php echo t('creators'); ?></div>
                    </div>
                </div>
                <div class="stat-card warning">
                    <div class="stat-content">
                        <div class="stat-value"><?php echo count(array_filter($users, fn($u) => $u['role'] === 'admin')); ?></div>
                        <div class="stat-label"><?php echo t('admins'); ?></div>
                    </div>
                </div>
                <div class="stat-card info">
                    <div class="stat-content">
                        <div class="stat-value"><?php echo array_sum(array_column($users, 'credits')); ?></div>
                        <div class="stat-label"><?php echo t('total_credits'); ?></div>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="dashboard-card full-width">
                <div class="card-header">
                    <h3><?php echo t('all_users'); ?></h3>
                    <div class="card-actions">
                        <div class="search-box">
                            <input type="text" id="userSearch" placeholder="<?php echo t('search_users'); ?>" onkeyup="filterUsers()">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="card-content">
                    <div class="users-table-wrapper">
                        <table class="users-table" id="usersTable">
                            <thead>
                                <tr>
                                    <th><?php echo t('user_email'); ?></th>
                                    <th><?php echo t('user_phone'); ?></th>
                                    <th><?php echo t('user_role'); ?></th>
                                    <th><?php echo t('user_subscription'); ?></th>
                                    <th><?php echo t('user_credits'); ?></th>
                                    <th><?php echo t('user_created'); ?></th>
                                    <th><?php echo t('actions'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $u): ?>
                                <tr class="user-row" data-user-id="<?php echo $u['id']; ?>">
                                    <td>
                                        <div class="user-email">
                                            <div class="email-primary"><?php echo htmlspecialchars($u['email']); ?></div>
                                            <div class="user-id">ID: <?php echo $u['id']; ?></div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="user-phone"><?php echo htmlspecialchars($u['phone']); ?></div>
                                    </td>
                                    <td>
                                        <div class="role-badge role-<?php echo $u['role']; ?>">
                                            <?php echo ucfirst($u['role']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="subscription-level">
                                            <span class="level-badge level-<?php echo $u['subscription_level']; ?>">
                                                <?php echo t('subscription_level_' . $u['subscription_level']); ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="user-credits">
                                            <span class="credits-amount"><?php echo $u['credits']; ?></span>
                                            <button class="btn-mini" onclick="showAddCreditsModal(<?php echo $u['id']; ?>, '<?php echo htmlspecialchars($u['email']); ?>')">
                                                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="created-date"><?php echo date('M j, Y', strtotime($u['created_at'])); ?></div>
                                    </td>
                                    <td>
                                        <div class="user-actions">
                                            <button class="btn-action btn-edit" onclick="editUser(<?php echo $u['id']; ?>)" title="<?php echo t('edit'); ?>">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <?php if ($u['id'] !== $user['id']): ?>
                                            <button class="btn-action btn-delete" onclick="deleteUser(<?php echo $u['id']; ?>)" title="<?php echo t('delete'); ?>">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                            <?php endif; ?>
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

<!-- Create User Modal -->
<div id="createUserModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><?php echo t('create_user'); ?></h2>
            <button class="modal-close" onclick="closeModal('createUserModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="createUserForm" onsubmit="handleCreateUser(event)">
                <div class="form-row">
                    <div class="form-group">
                        <label for="userEmail"><?php echo t('user_email'); ?></label>
                        <input type="email" id="userEmail" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="userPhone"><?php echo t('user_phone'); ?></label>
                        <input type="tel" id="userPhone" name="phone" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="userPassword"><?php echo t('password'); ?></label>
                    <input type="password" id="userPassword" name="password" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="userRole"><?php echo t('user_role'); ?></label>
                        <select id="userRole" name="role" required>
                            <option value="creator"><?php echo t('creator'); ?></option>
                            <option value="admin"><?php echo t('admin'); ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="subscriptionLevel"><?php echo t('user_subscription'); ?></label>
                        <select id="subscriptionLevel" name="subscription_level" required>
                            <option value="1"><?php echo t('subscription_level_1'); ?></option>
                            <option value="2"><?php echo t('subscription_level_2'); ?></option>
                            <option value="3"><?php echo t('subscription_level_3'); ?></option>
                            <option value="4"><?php echo t('subscription_level_4'); ?></option>
                            <option value="5"><?php echo t('subscription_level_5'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="initialCredits"><?php echo t('initial_credits'); ?></label>
                    <input type="number" id="initialCredits" name="credits" min="0" value="0">
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="closeModal('createUserModal')"><?php echo t('cancel'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo t('create_user'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Credits Modal -->
<div id="addCreditsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><?php echo t('add_credits'); ?></h2>
            <button class="modal-close" onclick="closeModal('addCreditsModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="addCreditsForm" onsubmit="handleAddCredits(event)">
                <div class="form-group">
                    <label><?php echo t('user'); ?></label>
                    <div class="user-info-display" id="creditsUserInfo"></div>
                </div>
                <div class="form-group">
                    <label for="creditAmount"><?php echo t('credit_amount'); ?></label>
                    <input type="number" id="creditAmount" name="amount" min="1" required>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="closeModal('addCreditsModal')"><?php echo t('cancel'); ?></button>
                    <button type="submit" class="btn btn-success"><?php echo t('add_credits'); ?></button>
                </div>
                <input type="hidden" id="creditsUserId" name="user_id">
            </form>
        </div>
    </div>
</div>

<script>
let currentUserId = null;

// Show create user modal
function showCreateUserModal() {
    document.getElementById('createUserModal').style.display = 'flex';
}

// Show add credits modal
function showAddCreditsModal(userId, userEmail) {
    currentUserId = userId;
    document.getElementById('creditsUserId').value = userId;
    document.getElementById('creditsUserInfo').innerHTML = `
        <div class="user-email">${userEmail}</div>
        <div class="user-id">ID: ${userId}</div>
    `;
    document.getElementById('addCreditsModal').style.display = 'flex';
}

// Handle create user form
async function handleCreateUser(event) {
    event.preventDefault();
    showLoading();
    
    const formData = new FormData(event.target);
    formData.append('action', 'create_user');
    
    try {
        const response = await fetch('/api.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMessage(result.message, 'success');
            closeModal('createUserModal');
            event.target.reset();
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

// Handle add credits form
async function handleAddCredits(event) {
    event.preventDefault();
    showLoading();
    
    const formData = new FormData(event.target);
    formData.append('action', 'add_credits');
    
    try {
        const response = await fetch('/api.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMessage(result.message, 'success');
            closeModal('addCreditsModal');
            event.target.reset();
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

// Filter users
function filterUsers() {
    const searchTerm = document.getElementById('userSearch').value.toLowerCase();
    const rows = document.querySelectorAll('.user-row');
    
    rows.forEach(row => {
        const email = row.querySelector('.user-email .email-primary').textContent.toLowerCase();
        const phone = row.querySelector('.user-phone').textContent.toLowerCase();
        const role = row.querySelector('.role-badge').textContent.toLowerCase();
        
        if (email.includes(searchTerm) || phone.includes(searchTerm) || role.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Edit user (placeholder)
function editUser(userId) {
    showMessage('Edit user functionality coming soon', 'info');
}

// Delete user (placeholder)
function deleteUser(userId) {
    if (confirm('<?php echo t("are_you_sure"); ?>')) {
        showMessage('Delete user functionality coming soon', 'info');
    }
}

// Toggle sidebar
function toggleSidebar() {
    document.getElementById('adminSidebar').classList.toggle('collapsed');
}
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
