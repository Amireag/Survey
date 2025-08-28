<?php
// Survey taking page

require_once __DIR__ . '/core/functions.php';
initializeSystem();

// Get survey ID from URL
$surveyLinkId = $_GET['id'] ?? null;

if (!$surveyLinkId) {
    http_response_code(404);
    $title = 'Survey Not Found';
    require_once __DIR__ . '/templates/header.php';
    echo '<div class="container"><div class="alert alert-danger">Survey not found. No ID provided.</div></div>';
    require_once __DIR__ . '/templates/footer.php';
    exit;
}

// Fetch survey details
$survey = getSurveyByLinkId($surveyLinkId);

if (!$survey || $survey['status'] !== 'active') {
    http_response_code(404);
    $title = 'Survey Not Found';
    require_once __DIR__ . '/templates/header.php';
    echo '<div class="container"><div class="alert alert-danger">Survey not found or is not currently active.</div></div>';
    require_once __DIR__ . '/templates/footer.php';
    exit;
}

// Fetch survey questions
$questions = getSurveyQuestions($survey['id']);

$title = $survey['title'];
$bodyClass = 'survey-page';

require_once __DIR__ . '/templates/header.php';
?>

<div class="container survey-container">
    <div class="survey-header">
        <h1><?php echo htmlspecialchars($survey['title']); ?></h1>
        <?php if (!empty($survey['description'])): ?>
            <p class="survey-description"><?php echo htmlspecialchars($survey['description']); ?></p>
        <?php endif; ?>
    </div>

    <form id="survey-form" class="survey-form">
        <input type="hidden" name="survey_link_id" value="<?php echo htmlspecialchars($surveyLinkId); ?>">

        <?php foreach ($questions as $index => $question): ?>
            <div class="card question-card">
                <div class="card-header">
                    <strong>Question <?php echo $index + 1; ?>:</strong>
                    <?php echo htmlspecialchars($question['question_text']); ?>
                    <?php if ($question['required']): ?>
                        <span class="required-indicator">*</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php
                    $questionId = 'question_' . $question['id'];
                    $inputName = 'responses[' . $questionId . ']';
                    $isRequired = $question['required'] ? 'required' : '';

                    switch ($question['question_type']) {
                        case 'text':
                            echo '<input type="text" class="form-control" name="' . $inputName . '" ' . $isRequired . '>';
                            break;

                        case 'textarea':
                            echo '<textarea class="form-control" name="' . $inputName . '" ' . $isRequired . '></textarea>';
                            break;

                        case 'radio':
                            if (!empty($question['options'])) {
                                foreach ($question['options'] as $option) {
                                    echo '<div class="form-check"><input class="form-check-input" type="radio" name="' . $inputName . '" value="' . htmlspecialchars($option) . '" ' . $isRequired . '><label class="form-check-label">' . htmlspecialchars($option) . '</label></div>';
                                }
                            }
                            break;

                        case 'checkbox':
                            if (!empty($question['options'])) {
                                echo '<div class="form-group">';
                                foreach ($question['options'] as $option) {
                                    echo '<div class="form-check"><input class="form-check-input" type="checkbox" name="' . $inputName . '[]" value="' . htmlspecialchars($option) . '"><label class="form-check-label">' . htmlspecialchars($option) . '</label></div>';
                                }
                                echo '</div>';
                            }
                            break;

                        case 'dropdown':
                            if (!empty($question['options'])) {
                                echo '<select class="form-control" name="' . $inputName . '" ' . $isRequired . '>';
                                echo '<option value="">-- Please select an option --</option>';
                                foreach ($question['options'] as $option) {
                                    echo '<option value="' . htmlspecialchars($option) . '">' . htmlspecialchars($option) . '</option>';
                                }
                                echo '</select>';
                            }
                            break;
                    }
                    ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="survey-footer">
            <button type="submit" class="btn btn-primary btn-lg">Submit Response</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const surveyForm = document.getElementById('survey-form');
    if (surveyForm) {
        surveyForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.textContent = 'Submitting...';

            const formData = new FormData(this);
            formData.append('action', 'submit_response');

            try {
                const response = await fetch('api.php', {
                    method: 'POST',
                    body: new URLSearchParams(formData)
                });

                const result = await response.json();

                if (result.success) {
                    document.querySelector('.survey-container').innerHTML = `
                        <div class="card text-center">
                            <div class="card-body">
                                <h1 class="card-title">Thank You!</h1>
                                <p class="card-text">${result.message || 'Your response has been submitted successfully.'}</p>
                                <a href="/" class="btn btn-primary">Back to Homepage</a>
                            </div>
                        </div>`;
                } else {
                    alert('Error: ' . (result.message || 'Could not submit your response.'));
                    submitButton.disabled = false;
                    submitButton.textContent = 'Submit Response';
                }
            } catch (error) {
                console.error('Error submitting survey:', error);
                alert('An error occurred while submitting the survey. Please try again.');
                submitButton.disabled = false;
                submitButton.textContent = 'Submit Response';
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/templates/footer.php'; ?>
