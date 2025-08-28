<?php
$title = t('create_survey') . ' - Creator';
$bodyClass = 'creator-survey-builder';

require_once __DIR__ . '/../core/functions.php';
initializeSystem();
requireRole('creator');

$user = getCurrentUser();

// Get survey ID if editing
$surveyId = $_GET['id'] ?? null;
$survey = null;
$questions = [];

if ($surveyId) {
    $survey = getSurveyById($surveyId);
    if (!$survey || ($survey['user_id'] != $user['id'] && $user['role'] !== 'admin')) {
        redirectTo('creator/');
    }
    $questions = getSurveyQuestions($surveyId);
    $title = t('edit_survey') . ' - ' . $survey['title'];
}

require_once __DIR__ . '/../templates/header.php';
?>

<div class="survey-builder">
    <!-- Builder Header -->
    <div class="builder-header">
        <div class="container">
            <div class="header-content">
                <div class="builder-title">
                    <h1><?php echo $surveyId ? t('edit_survey') : t('create_survey'); ?></h1>
                    <p><?php echo t('drag_drop_survey_builder'); ?></p>
                </div>
                <div class="builder-actions">
                    <button class="btn btn-outline" onclick="previewSurvey()">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <?php echo t('preview'); ?>
                    </button>
                    <button class="btn btn-success" onclick="saveSurvey()">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <?php echo t('save_survey'); ?>
                    </button>
                    <button class="btn btn-primary" onclick="publishSurvey()">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        <?php echo t('publish_survey'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="builder-container">
        <!-- Left Sidebar - Question Types -->
        <div class="builder-sidebar">
            <div class="sidebar-header">
                <h3><?php echo t('question_types'); ?></h3>
                <p><?php echo t('drag_questions_to_add'); ?></p>
            </div>
            
            <div class="question-types">
                <div class="question-type" draggable="true" data-type="text">
                    <div class="type-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                        </svg>
                    </div>
                    <div class="type-info">
                        <h4><?php echo t('text_input'); ?></h4>
                        <p><?php echo t('short_text_response'); ?></p>
                    </div>
                </div>
                
                <div class="question-type" draggable="true" data-type="textarea">
                    <div class="type-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                        </svg>
                    </div>
                    <div class="type-info">
                        <h4><?php echo t('textarea'); ?></h4>
                        <p><?php echo t('long_text_response'); ?></p>
                    </div>
                </div>
                
                <div class="question-type" draggable="true" data-type="radio">
                    <div class="type-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="type-info">
                        <h4><?php echo t('radio_buttons'); ?></h4>
                        <p><?php echo t('single_choice_response'); ?></p>
                    </div>
                </div>
                
                <div class="question-type" draggable="true" data-type="checkbox">
                    <div class="type-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="type-info">
                        <h4><?php echo t('checkboxes'); ?></h4>
                        <p><?php echo t('multiple_choice_response'); ?></p>
                    </div>
                </div>
                
                <div class="question-type" draggable="true" data-type="dropdown">
                    <div class="type-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                    <div class="type-info">
                        <h4><?php echo t('dropdown'); ?></h4>
                        <p><?php echo t('dropdown_selection'); ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Survey Settings -->
            <div class="survey-settings">
                <h3><?php echo t('survey_settings'); ?></h3>
                
                <div class="setting-group">
                    <label for="surveyTitle"><?php echo t('survey_title'); ?></label>
                    <input type="text" id="surveyTitle" placeholder="<?php echo t('enter_survey_title'); ?>" value="<?php echo $survey ? htmlspecialchars($survey['title']) : ''; ?>">
                </div>
                
                <div class="setting-group">
                    <label for="surveyDescription"><?php echo t('survey_description'); ?></label>
                    <textarea id="surveyDescription" rows="3" placeholder="<?php echo t('enter_survey_description'); ?>"><?php echo $survey ? htmlspecialchars($survey['description']) : ''; ?></textarea>
                </div>
                
                <div class="setting-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="allowAnonymous" <?php echo ($survey && $survey['allow_anonymous']) ? 'checked' : ''; ?>>
                        <span class="checkmark"></span>
                        <?php echo t('allow_anonymous_responses'); ?>
                    </label>
                </div>
                
                <div class="setting-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="showProgressBar" checked>
                        <span class="checkmark"></span>
                        <?php echo t('show_progress_bar'); ?>
                    </label>
                </div>
            </div>
        </div>

        <!-- Main Content Area - Survey Builder -->
        <div class="builder-content">
            <div class="survey-preview">
                <div class="survey-header">
                    <h2 id="previewTitle"><?php echo $survey ? htmlspecialchars($survey['title']) : t('untitled_survey'); ?></h2>
                    <p id="previewDescription"><?php echo $survey ? htmlspecialchars($survey['description']) : t('survey_description_placeholder'); ?></p>
                </div>
                
                <div class="questions-container" id="questionsContainer">
                    <?php if (empty($questions)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <svg width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <h3><?php echo t('no_questions_yet'); ?></h3>
                        <p><?php echo t('drag_question_types_to_start'); ?></p>
                    </div>
                    <?php else: ?>
                    <!-- Render existing questions -->
                    <?php foreach ($questions as $question): ?>
                    <div class="question-item" data-id="<?php echo $question['id']; ?>" data-type="<?php echo $question['question_type']; ?>">
                        <!-- Question content will be rendered by JavaScript -->
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <div class="drop-zone" id="dropZone">
                    <div class="drop-indicator">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span><?php echo t('drop_question_here'); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Sidebar - Question Properties -->
        <div class="builder-properties" id="propertiesPanel">
            <div class="properties-header">
                <h3><?php echo t('question_properties'); ?></h3>
                <p><?php echo t('select_question_to_edit'); ?></p>
            </div>
            
            <div class="properties-content" id="propertiesContent">
                <div class="empty-properties">
                    <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    <p><?php echo t('click_question_to_edit_properties'); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Question Editor Modal -->
<div id="questionModal" class="modal">
    <div class="modal-content large">
        <div class="modal-header">
            <h2 id="modalTitle"><?php echo t('edit_question'); ?></h2>
            <button class="modal-close" onclick="closeQuestionModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="question-editor" id="questionEditor">
                <!-- Dynamic content based on question type -->
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeQuestionModal()"><?php echo t('cancel'); ?></button>
            <button class="btn btn-primary" onclick="saveQuestionChanges()"><?php echo t('save_changes'); ?></button>
        </div>
    </div>
</div>

<!-- Survey Preview Modal -->
<div id="previewModal" class="modal">
    <div class="modal-content extra-large">
        <div class="modal-header">
            <h2><?php echo t('survey_preview'); ?></h2>
            <button class="modal-close" onclick="closePreviewModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="survey-preview-content" id="surveyPreviewContent">
                <!-- Survey preview will be rendered here -->
            </div>
        </div>
    </div>
</div>

<style>
/* Survey Builder Styles */
.survey-builder {
    min-height: 100vh;
    background: var(--background-color);
}

.builder-header {
    background: white;
    border-bottom: 1px solid var(--border-color);
    padding: var(--spacing-lg) 0;
    position: sticky;
    top: 0;
    z-index: 100;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.builder-title h1 {
    margin: 0;
    color: var(--text-color);
}

.builder-title p {
    margin: 0;
    color: var(--secondary-color);
}

.builder-actions {
    display: flex;
    gap: var(--spacing-md);
}

.builder-container {
    display: grid;
    grid-template-columns: 280px 1fr 320px;
    min-height: calc(100vh - 100px);
}

/* Sidebar Styles */
.builder-sidebar {
    background: white;
    border-right: 1px solid var(--border-color);
    padding: var(--spacing-lg);
    overflow-y: auto;
}

.sidebar-header h3 {
    margin: 0 0 var(--spacing-sm);
    color: var(--text-color);
}

.sidebar-header p {
    margin: 0 0 var(--spacing-lg);
    color: var(--secondary-color);
    font-size: 0.9rem;
}

.question-types {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-sm);
    margin-bottom: var(--spacing-xl);
}

.question-type {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    padding: var(--spacing-md);
    background: var(--background-light);
    border: 2px dashed var(--border-color);
    border-radius: var(--border-radius-md);
    cursor: grab;
    transition: all var(--transition-normal);
}

.question-type:hover {
    border-color: var(--primary-color);
    background: var(--primary-light);
}

.question-type:active {
    cursor: grabbing;
}

.type-icon {
    flex-shrink: 0;
    color: var(--primary-color);
}

.type-info h4 {
    margin: 0;
    font-size: 0.9rem;
    color: var(--text-color);
}

.type-info p {
    margin: 0;
    font-size: 0.8rem;
    color: var(--secondary-color);
}

/* Survey Settings */
.survey-settings {
    border-top: 1px solid var(--border-color);
    padding-top: var(--spacing-lg);
}

.survey-settings h3 {
    margin: 0 0 var(--spacing-md);
    color: var(--text-color);
}

.setting-group {
    margin-bottom: var(--spacing-md);
}

.setting-group label {
    display: block;
    margin-bottom: var(--spacing-sm);
    font-weight: 500;
    color: var(--text-color);
}

.setting-group input,
.setting-group textarea {
    width: 100%;
    padding: var(--spacing-sm);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius-sm);
    font-family: inherit;
}

/* Builder Content */
.builder-content {
    background: var(--background-light);
    padding: var(--spacing-lg);
    overflow-y: auto;
}

.survey-preview {
    background: white;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-md);
    min-height: 600px;
}

.survey-header {
    padding: var(--spacing-xl);
    border-bottom: 1px solid var(--border-color);
}

.survey-header h2 {
    margin: 0 0 var(--spacing-md);
    color: var(--text-color);
}

.survey-header p {
    margin: 0;
    color: var(--secondary-color);
}

.questions-container {
    padding: var(--spacing-lg);
    min-height: 400px;
}

.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 400px;
    text-align: center;
}

.empty-icon {
    color: var(--secondary-color);
    margin-bottom: var(--spacing-lg);
}

.empty-state h3 {
    margin: 0 0 var(--spacing-sm);
    color: var(--text-color);
}

.empty-state p {
    margin: 0;
    color: var(--secondary-color);
}

.question-item {
    background: var(--background-light);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius-md);
    padding: var(--spacing-lg);
    margin-bottom: var(--spacing-md);
    position: relative;
    cursor: pointer;
    transition: all var(--transition-normal);
}

.question-item:hover {
    border-color: var(--primary-color);
    box-shadow: var(--shadow-sm);
}

.question-item.selected {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px var(--primary-light);
}

.drop-zone {
    border: 2px dashed var(--border-color);
    border-radius: var(--border-radius-md);
    padding: var(--spacing-xl);
    margin-top: var(--spacing-lg);
    text-align: center;
    transition: all var(--transition-normal);
}

.drop-zone.drag-over {
    border-color: var(--primary-color);
    background: var(--primary-light);
}

.drop-indicator {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--spacing-sm);
    color: var(--secondary-color);
}

/* Properties Panel */
.builder-properties {
    background: white;
    border-left: 1px solid var(--border-color);
    padding: var(--spacing-lg);
    overflow-y: auto;
}

.properties-header h3 {
    margin: 0 0 var(--spacing-sm);
    color: var(--text-color);
}

.properties-header p {
    margin: 0 0 var(--spacing-lg);
    color: var(--secondary-color);
    font-size: 0.9rem;
}

.empty-properties {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: var(--spacing-xl) 0;
    color: var(--secondary-color);
}

.empty-properties svg {
    margin-bottom: var(--spacing-md);
}

/* Responsive Design */
@media (max-width: 1200px) {
    .builder-container {
        grid-template-columns: 250px 1fr 280px;
    }
}

@media (max-width: 992px) {
    .builder-container {
        grid-template-columns: 1fr;
        grid-template-rows: auto 1fr;
    }
    
    .builder-sidebar,
    .builder-properties {
        display: none;
    }
}

@media (max-width: 768px) {
    .builder-header .header-content {
        flex-direction: column;
        gap: var(--spacing-md);
        text-align: center;
    }
    
    .builder-actions {
        flex-wrap: wrap;
        justify-content: center;
    }
}
</style>

<script>
// Survey Builder JavaScript
class SurveyBuilder {
    constructor() {
        this.questions = <?php echo json_encode($questions); ?>;
        this.surveyId = <?php echo $surveyId ? $surveyId : 'null'; ?>;
        this.selectedQuestion = null;
        this.questionCounter = 0;
        
        this.init();
    }
    
    init() {
        this.setupDragAndDrop();
        this.setupEventListeners();
        this.renderQuestions();
        this.updatePreview();
    }
    
    setupDragAndDrop() {
        const questionTypes = document.querySelectorAll('.question-type');
        const dropZone = document.getElementById('dropZone');
        
        questionTypes.forEach(type => {
            type.addEventListener('dragstart', this.handleDragStart.bind(this));
        });
        
        dropZone.addEventListener('dragover', this.handleDragOver.bind(this));
        dropZone.addEventListener('drop', this.handleDrop.bind(this));
        dropZone.addEventListener('dragleave', this.handleDragLeave.bind(this));
    }
    
    setupEventListeners() {
        // Survey settings
        document.getElementById('surveyTitle').addEventListener('input', this.updatePreview.bind(this));
        document.getElementById('surveyDescription').addEventListener('input', this.updatePreview.bind(this));
        
        // Question selection
        document.addEventListener('click', this.handleQuestionClick.bind(this));
    }
    
    handleDragStart(e) {
        e.dataTransfer.setData('text/plain', e.target.dataset.type);
        e.dataTransfer.effectAllowed = 'copy';
    }
    
    handleDragOver(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'copy';
        e.target.closest('.drop-zone').classList.add('drag-over');
    }
    
    handleDragLeave(e) {
        e.target.closest('.drop-zone').classList.remove('drag-over');
    }
    
    handleDrop(e) {
        e.preventDefault();
        const questionType = e.dataTransfer.getData('text/plain');
        const dropZone = e.target.closest('.drop-zone');
        
        dropZone.classList.remove('drag-over');
        this.addQuestion(questionType);
    }
    
    addQuestion(type) {
        const questionId = `temp_${++this.questionCounter}`;
        const question = {
            id: questionId,
            question_text: this.getDefaultQuestionText(type),
            question_type: type,
            required: false,
            options: this.getDefaultOptions(type),
            order_index: this.questions.length
        };
        
        this.questions.push(question);
        this.renderQuestions();
        this.selectQuestion(questionId);
        this.updatePreview();
    }
    
    getDefaultQuestionText(type) {
        const defaults = {
            'text': '<?php echo t("text_question_default"); ?>',
            'textarea': '<?php echo t("textarea_question_default"); ?>',
            'radio': '<?php echo t("radio_question_default"); ?>',
            'checkbox': '<?php echo t("checkbox_question_default"); ?>',
            'dropdown': '<?php echo t("dropdown_question_default"); ?>'
        };
        return defaults[type] || '<?php echo t("new_question"); ?>';
    }
    
    getDefaultOptions(type) {
        if (type === 'radio' || type === 'checkbox' || type === 'dropdown') {
            return ['<?php echo t("option_1"); ?>', '<?php echo t("option_2"); ?>'];
        }
        return null;
    }
    
    renderQuestions() {
        const container = document.getElementById('questionsContainer');
        const emptyState = container.querySelector('.empty-state');
        
        if (this.questions.length === 0) {
            if (!emptyState) {
                container.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-icon">
                            <svg width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <h3><?php echo t("no_questions_yet"); ?></h3>
                        <p><?php echo t("drag_question_types_to_start"); ?></p>
                    </div>
                `;
            }
            return;
        }
        
        if (emptyState) {
            emptyState.remove();
        }
        
        const questionsHtml = this.questions.map(question => this.renderQuestion(question)).join('');
        container.innerHTML = questionsHtml;
    }
    
    renderQuestion(question) {
        const requiredBadge = question.required ? `<span class="required-badge"><?php echo t("required"); ?></span>` : '';
        
        return `
            <div class="question-item ${this.selectedQuestion === question.id ? 'selected' : ''}" 
                 data-id="${question.id}" 
                 data-type="${question.question_type}">
                <div class="question-header">
                    <h4>${this.escapeHtml(question.question_text)}</h4>
                    ${requiredBadge}
                    <div class="question-actions">
                        <button class="btn-icon" onclick="editQuestion('${question.id}')" title="<?php echo t("edit"); ?>">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </button>
                        <button class="btn-icon btn-danger" onclick="deleteQuestion('${question.id}')" title="<?php echo t("delete"); ?>">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="question-preview">
                    ${this.renderQuestionPreview(question)}
                </div>
            </div>
        `;
    }
    
    renderQuestionPreview(question) {
        switch (question.question_type) {
            case 'text':
                return '<input type="text" placeholder="<?php echo t("text_input_placeholder"); ?>" disabled>';
            case 'textarea':
                return '<textarea placeholder="<?php echo t("textarea_placeholder"); ?>" rows="3" disabled></textarea>';
            case 'radio':
                return this.renderOptionsPreview(question.options, 'radio');
            case 'checkbox':
                return this.renderOptionsPreview(question.options, 'checkbox');
            case 'dropdown':
                return `<select disabled><option><?php echo t("select_option"); ?></option></select>`;
            default:
                return '';
        }
    }
    
    renderOptionsPreview(options, type) {
        if (!options) return '';
        
        return options.map((option, index) => `
            <label class="option-label">
                <input type="${type}" name="preview_${this.questionCounter}" disabled>
                <span>${this.escapeHtml(option)}</span>
            </label>
        `).join('');
    }
    
    handleQuestionClick(e) {
        const questionItem = e.target.closest('.question-item');
        if (questionItem && !e.target.closest('.question-actions')) {
            const questionId = questionItem.dataset.id;
            this.selectQuestion(questionId);
        }
    }
    
    selectQuestion(questionId) {
        // Remove previous selection
        document.querySelectorAll('.question-item').forEach(item => {
            item.classList.remove('selected');
        });
        
        // Add new selection
        const selectedItem = document.querySelector(`[data-id="${questionId}"]`);
        if (selectedItem) {
            selectedItem.classList.add('selected');
            this.selectedQuestion = questionId;
            this.showQuestionProperties(questionId);
        }
    }
    
    showQuestionProperties(questionId) {
        const question = this.questions.find(q => q.id === questionId);
        if (!question) return;
        
        const propertiesContent = document.getElementById('propertiesContent');
        propertiesContent.innerHTML = this.renderQuestionProperties(question);
    }
    
    renderQuestionProperties(question) {
        const hasOptions = ['radio', 'checkbox', 'dropdown'].includes(question.question_type);
        
        return `
            <div class="properties-form">
                <div class="form-group">
                    <label><?php echo t("question_text"); ?></label>
                    <textarea id="propQuestionText" rows="2">${this.escapeHtml(question.question_text)}</textarea>
                </div>
                
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="propRequired" ${question.required ? 'checked' : ''}>
                        <span class="checkmark"></span>
                        <?php echo t("required_question"); ?>
                    </label>
                </div>
                
                ${hasOptions ? this.renderOptionsEditor(question.options || []) : ''}
                
                <div class="properties-actions">
                    <button class="btn btn-primary btn-sm" onclick="surveyBuilder.saveQuestionProperties('${question.id}')">
                        <?php echo t("save_changes"); ?>
                    </button>
                    <button class="btn btn-outline btn-sm" onclick="surveyBuilder.duplicateQuestion('${question.id}')">
                        <?php echo t("duplicate"); ?>
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="surveyBuilder.deleteQuestion('${question.id}')">
                        <?php echo t("delete"); ?>
                    </button>
                </div>
            </div>
        `;
    }
    
    renderOptionsEditor(options) {
        const optionsHtml = options.map((option, index) => `
            <div class="option-editor">
                <input type="text" value="${this.escapeHtml(option)}" placeholder="<?php echo t("option_text"); ?>">
                <button class="btn-icon btn-danger" onclick="surveyBuilder.removeOption(${index})">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        `).join('');
        
        return `
            <div class="form-group">
                <label><?php echo t("answer_options"); ?></label>
                <div class="options-editor">
                    ${optionsHtml}
                    <button class="btn btn-outline btn-sm" onclick="surveyBuilder.addOption()">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <?php echo t("add_option"); ?>
                    </button>
                </div>
            </div>
        `;
    }
    
    saveQuestionProperties(questionId) {
        const question = this.questions.find(q => q.id === questionId);
        if (!question) return;
        
        const questionText = document.getElementById('propQuestionText').value;
        const required = document.getElementById('propRequired').checked;
        
        question.question_text = questionText;
        question.required = required;
        
        // Update options if applicable
        const optionInputs = document.querySelectorAll('.option-editor input');
        if (optionInputs.length > 0) {
            question.options = Array.from(optionInputs).map(input => input.value).filter(value => value.trim());
        }
        
        this.renderQuestions();
        this.updatePreview();
        this.selectQuestion(questionId);
        
        showMessage('<?php echo t("question_updated"); ?>', 'success');
    }
    
    duplicateQuestion(questionId) {
        const question = this.questions.find(q => q.id === questionId);
        if (!question) return;
        
        const newQuestion = {
            ...question,
            id: `temp_${++this.questionCounter}`,
            question_text: question.question_text + ' (<?php echo t("copy"); ?>)',
            order_index: this.questions.length
        };
        
        this.questions.push(newQuestion);
        this.renderQuestions();
        this.selectQuestion(newQuestion.id);
        this.updatePreview();
    }
    
    deleteQuestion(questionId) {
        if (confirm('<?php echo t("confirm_delete_question"); ?>')) {
            this.questions = this.questions.filter(q => q.id !== questionId);
            this.selectedQuestion = null;
            this.renderQuestions();
            this.updatePreview();
            
            // Clear properties panel
            document.getElementById('propertiesContent').innerHTML = `
                <div class="empty-properties">
                    <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    <p><?php echo t("click_question_to_edit_properties"); ?></p>
                </div>
            `;
        }
    }
    
    addOption() {
        const optionsEditor = document.querySelector('.options-editor');
        const newOption = document.createElement('div');
        newOption.className = 'option-editor';
        newOption.innerHTML = `
            <input type="text" value="" placeholder="<?php echo t("option_text"); ?>">
            <button class="btn-icon btn-danger" onclick="this.parentElement.remove()">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        `;
        optionsEditor.insertBefore(newOption, optionsEditor.lastElementChild);
        newOption.querySelector('input').focus();
    }
    
    removeOption(index) {
        const optionEditors = document.querySelectorAll('.option-editor');
        if (optionEditors.length > 1) {
            optionEditors[index].remove();
        }
    }
    
    updatePreview() {
        const title = document.getElementById('surveyTitle').value || '<?php echo t("untitled_survey"); ?>';
        const description = document.getElementById('surveyDescription').value || '<?php echo t("survey_description_placeholder"); ?>';
        
        document.getElementById('previewTitle').textContent = title;
        document.getElementById('previewDescription').textContent = description;
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Global functions
let surveyBuilder;

document.addEventListener('DOMContentLoaded', function() {
    surveyBuilder = new SurveyBuilder();
});

function previewSurvey() {
    // Implementation for survey preview
    const modal = document.getElementById('previewModal');
    const content = document.getElementById('surveyPreviewContent');
    
    // Generate preview content
    content.innerHTML = generateSurveyPreview();
    modal.classList.add('show');
}

function saveSurvey() {
    const title = document.getElementById('surveyTitle').value;
    const description = document.getElementById('surveyDescription').value;
    
    if (!title.trim()) {
        showMessage('<?php echo t("survey_title_required"); ?>', 'error');
        return;
    }
    
    const surveyData = {
        action: surveyBuilder.surveyId ? 'update_survey' : 'create_survey',
        survey_id: surveyBuilder.surveyId,
        title: title,
        description: description,
        questions: surveyBuilder.questions
    };
    
    showLoading();
    
    fetch('/api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(surveyData)
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showMessage(data.message, 'success');
            if (!surveyBuilder.surveyId && data.data && data.data.survey_id) {
                // Redirect to edit mode for new survey
                window.location.href = `create.php?id=${data.data.survey_id}`;
            }
        } else {
            showMessage(data.message, 'error');
        }
    })
    .catch(error => {
        hideLoading();
        showMessage('<?php echo t("error_saving_survey"); ?>', 'error');
    });
}

function publishSurvey() {
    saveSurvey().then(() => {
        // Additional logic for publishing
        const publishData = {
            action: 'update_survey',
            survey_id: surveyBuilder.surveyId,
            status: 'active'
        };
        
        fetch('/api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(publishData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage('<?php echo t("survey_published"); ?>', 'success');
            }
        });
    });
}

function closeQuestionModal() {
    document.getElementById('questionModal').classList.remove('show');
}

function closePreviewModal() {
    document.getElementById('previewModal').classList.remove('show');
}

function generateSurveyPreview() {
    const title = document.getElementById('surveyTitle').value || '<?php echo t("untitled_survey"); ?>';
    const description = document.getElementById('surveyDescription').value || '';
    
    let html = `
        <div class="survey-form">
            <div class="survey-header">
                <h2>${surveyBuilder.escapeHtml(title)}</h2>
                ${description ? `<p>${surveyBuilder.escapeHtml(description)}</p>` : ''}
            </div>
            <div class="survey-questions">
    `;
    
    surveyBuilder.questions.forEach((question, index) => {
        html += `
            <div class="question-group">
                <label class="question-label">
                    ${index + 1}. ${surveyBuilder.escapeHtml(question.question_text)}
                    ${question.required ? '<span class="required">*</span>' : ''}
                </label>
                ${surveyBuilder.renderQuestionPreview(question)}
            </div>
        `;
    });
    
    html += `
            </div>
            <div class="survey-footer">
                <button class="btn btn-primary" disabled><?php echo t("submit_survey"); ?></button>
            </div>
        </div>
    `;
    
    return html;
}
</script>

<?php
$additionalJS = [
    getBaseUrl() . '/public/js/survey-builder.js'
];

require_once __DIR__ . '/../templates/footer.php';
?>
