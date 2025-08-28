<?php
// Ultra-simple test file
echo "<h1>Survey Platform Test</h1>";
echo "<p>✅ PHP is working correctly!</p>";
echo "<p>Server: " . $_SERVER['HTTP_HOST'] . "</p>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Current Time: " . date('Y-m-d H:i:s') . "</p>";

// Check if files exist
echo "<h2>File Check:</h2>";
echo "<ul>";
echo "<li>index.php: " . (file_exists('index.php') ? '✅ Found' : '❌ Missing') . "</li>";
echo "<li>api.php: " . (file_exists('api.php') ? '✅ Found' : '❌ Missing') . "</li>";
echo "<li>core/functions.php: " . (file_exists('core/functions.php') ? '✅ Found' : '❌ Missing') . "</li>";
echo "<li>core/database.php: " . (file_exists('core/database.php') ? '✅ Found' : '❌ Missing') . "</li>";
echo "<li>core/i18n.php: " . (file_exists('core/i18n.php') ? '✅ Found' : '❌ Missing') . "</li>";
echo "</ul>";

echo "<h2>Test Links:</h2>";
echo "<ul>";
echo "<li><a href='index.php'>Main Homepage (index.php)</a></li>";
echo "<li><a href='debug.php'>Detailed Debug Test</a></li>";
echo "<li><a href='test_fixes.php'>System Test</a></li>";
echo "</ul>";

echo "<h2>Instructions:</h2>";
echo "<p>1. If you see this page, PHP is working</p>";
echo "<p>2. Try clicking the links above</p>";
echo "<p>3. Check if mohman.ir redirects to mohman.ir/Survey/</p>";
echo "<p>4. Check the error logs if any issues persist</p>";
?>
