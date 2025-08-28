<?php
// Debug file to test basic PHP functionality
echo "PHP is working! ✅\n";
echo "<br>";
echo "PHP Version: " . phpversion() . "\n";
echo "<br>";
echo "Server Time: " . date('Y-m-d H:i:s') . "\n";
echo "<br>";

// Test if we can include files
echo "Testing file includes...\n";
echo "<br>";

if (file_exists('core/database.php')) {
    echo "✅ core/database.php exists\n";
} else {
    echo "❌ core/database.php NOT found\n";
}
echo "<br>";

if (file_exists('core/functions.php')) {
    echo "✅ core/functions.php exists\n";
} else {
    echo "❌ core/functions.php NOT found\n";
}
echo "<br>";

if (file_exists('core/i18n.php')) {
    echo "✅ core/i18n.php exists\n";
} else {
    echo "❌ core/i18n.php NOT found\n";
}
echo "<br>";

// Test syntax
echo "Testing syntax...\n";
echo "<br>";

try {
    include_once 'core/i18n.php';
    echo "✅ i18n.php syntax OK\n";
} catch (Exception $e) {
    echo "❌ i18n.php error: " . $e->getMessage() . "\n";
}
echo "<br>";

try {
    include_once 'core/database.php';
    echo "✅ database.php syntax OK\n";
} catch (Exception $e) {
    echo "❌ database.php error: " . $e->getMessage() . "\n";
}
echo "<br>";

try {
    include_once 'core/functions.php';
    echo "✅ functions.php syntax OK\n";
} catch (Exception $e) {
    echo "❌ functions.php error: " . $e->getMessage() . "\n";
}
echo "<br>";

echo "<br>🔧 Debug complete!";
?>
