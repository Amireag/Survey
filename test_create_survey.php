<?php
require_once 'core/functions.php';

echo "<pre>";

try {
    initializeSystem();
    $db = Database::getInstance();

    echo "🔧 Setting up test survey...\n\n";

    // 1. Find or create a user
    $testEmail = 'test-creator@example.com';
    $user = $db->fetch("SELECT * FROM users WHERE email = ?", [$testEmail]);

    if (!$user) {
        echo "Creating a test user...\n";
        $result = createUser($testEmail, '1234567890', 'password');
        if (!$result['success']) {
            throw new Exception("Failed to create user: " . $result['error']);
        }
        $userId = $result['user_id'];
        echo "✅ Test user created (ID: $userId).\n\n";
    } else {
        $userId = $user['id'];
        echo "✅ Found existing test user (ID: $userId).\n\n";
    }

    // 2. Create a new survey
    echo "Creating a new survey...\n";
    $surveyTitle = "My Test Survey";
    $surveyDescription = "This is a survey created for testing purposes.";
    $surveyResult = createSurvey($userId, $surveyTitle, $surveyDescription);

    if (!$surveyResult['success']) {
        throw new Exception("Failed to create survey: " . $surveyResult['error']);
    }

    $surveyId = $surveyResult['survey_id'];
    $surveyLinkId = $surveyResult['survey_link_id'];
    echo "✅ Survey created (ID: $surveyId, Link ID: $surveyLinkId).\n\n";

    // 3. Add questions to the survey
    echo "Adding questions...\n";
    addSurveyQuestion($surveyId, "What is your favorite color?", "radio", ["Red", "Green", "Blue"], true, 1);
    addSurveyQuestion($surveyId, "Which features do you like? (Select all that apply)", "checkbox", ["Feature A", "Feature B", "Feature C"], false, 2);
    addSurveyQuestion($surveyId, "Any additional comments?", "textarea", null, false, 3);
    echo "✅ Questions added.\n\n";

    // 4. Publish the survey
    echo "Publishing the survey...\n";
    $publishResult = publishSurvey($surveyId, $userId);
    if (!$publishResult['success']) {
        throw new Exception("Failed to publish survey: " . $publishResult['error']);
    }
    echo "✅ Survey published.\n\n";

    // 5. Output the link
    $surveyUrl = getBaseUrl() . '/' . $surveyLinkId;
    echo "🎉 Test setup complete!\n\n";
    echo "You can now test the survey at the following URL:\n";
    echo "<a href='$surveyUrl' target='_blank'>$surveyUrl</a>\n";

} catch (Exception $e) {
    echo "❌ An error occurred during test setup:\n";
    echo $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
}

echo "</pre>";
?>
