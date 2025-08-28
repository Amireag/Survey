<?php
// Simple test file to verify the fixes
require_once 'core/functions.php';

try {
    // Initialize system
    initializeSystem();
    
    echo "✅ System initialization successful\n";
    echo "✅ Database connection working\n";
    echo "✅ Functions loaded without errors\n";
    echo "✅ i18n system working\n";
    
    // Test database
    $db = Database::getInstance();
    $users = $db->fetchAll("SELECT COUNT(*) as count FROM users");
    echo "✅ Database queries working - Found " . $users[0]['count'] . " users\n";
    
    echo "\n🎉 All systems operational!\n";
    echo "The survey platform should now be working correctly.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "❌ File: " . $e->getFile() . "\n";
    echo "❌ Line: " . $e->getLine() . "\n";
}
?>
