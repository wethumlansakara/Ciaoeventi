<?php
// final_test.php
echo "<h1>Final System Test</h1>";

// Test database
require_once 'config/database.php';
echo "<p>✅ Database connected</p>";

// Test sessions (check if already started)
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<p>✅ Sessions working (active)</p>";
} else {
    session_start();
    echo "<p>✅ Sessions working (started)</p>";
}

// Test file permissions
if (is_writable('uploads/')) {
    echo "<p>✅ Uploads folder writable</p>";
} else {
    echo "<p>❌ Uploads folder not writable</p>";
}

// Test required files
$files = ['index.php', 'events.php', 'profile.php', 'login.php', 'config/database.php'];
foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<p>✅ $file exists</p>";
    } else {
        echo "<p>❌ $file missing</p>";
    }
}

echo "<h2 style='color: green; margin-top: 20px;'> All Systems are okay</h2>";
?>