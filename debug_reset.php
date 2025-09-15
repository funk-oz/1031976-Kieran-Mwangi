<?php
/**
 * Debug Password Reset Tokens
 * This file helps debug token storage and retrieval issues
 */

require_once "includes/functions.php";

echo "<h1>Password Reset Token Debug</h1>";

// Check if user table has the required columns
$query = "DESCRIBE user";
$result = query($query);

echo "<h2>Database Structure:</h2>";
echo "<ul>";
$has_reset_token = false;
$has_reset_expires = false;

if ($result) {
    foreach ($result as $row) {
        echo "<li>{$row['Field']} - {$row['Type']}</li>";
        if ($row['Field'] == 'reset_token') $has_reset_token = true;
        if ($row['Field'] == 'reset_expires') $has_reset_expires = true;
    }
} else {
    echo "<li>Error: Could not describe user table</li>";
}

echo "</ul>";

if (!$has_reset_token || !$has_reset_expires) {
    echo "<p style='color: red;'>❌ Missing required columns! Please run this SQL:</p>";
    echo "<pre>";
    echo "ALTER TABLE user ADD COLUMN reset_token VARCHAR(255) DEFAULT NULL;\n";
    echo "ALTER TABLE user ADD COLUMN reset_expires TIMESTAMP NULL DEFAULT NULL;\n";
    echo "</pre>";
    exit;
}

echo "<p style='color: green;'>✅ Database structure looks good</p>";

// Test token generation and storage
echo "<h2>Testing Token Generation:</h2>";

$test_email = "test@example.com";
$token = bin2hex(random_bytes(32));
$expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

echo "<p>Generated token: " . substr($token, 0, 20) . "...</p>";
echo "<p>Expires: $expires</p>";

// Test storage
$store_result = store_password_reset($test_email, $token, $expires);
echo "<p>Storage result: " . ($store_result ? "✅ Success" : "❌ Failed") . "</p>";

// Test retrieval
$retrieved_data = get_password_reset($token);
if ($retrieved_data) {
    echo "<p>✅ Token retrieved successfully:</p>";
    echo "<ul>";
    echo "<li>User ID: {$retrieved_data['user_id']}</li>";
    echo "<li>Email: {$retrieved_data['email']}</li>";
    echo "<li>Expires: {$retrieved_data['reset_expires']}</li>";
    echo "</ul>";
} else {
    echo "<p>❌ Token retrieval failed</p>";
}

// Show current reset tokens in database
echo "<h2>Current Reset Tokens in Database:</h2>";
$tokens_query = "SELECT user_id, email, reset_token, reset_expires FROM user WHERE reset_token IS NOT NULL";
$tokens_result = query($tokens_query);

if ($tokens_result && count($tokens_result) > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>User ID</th><th>Email</th><th>Token (first 20 chars)</th><th>Expires</th></tr>";
    foreach ($tokens_result as $row) {
        echo "<tr>";
        echo "<td>{$row['user_id']}</td>";
        echo "<td>{$row['email']}</td>";
        echo "<td>" . substr($row['reset_token'], 0, 20) . "...</td>";
        echo "<td>{$row['reset_expires']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No reset tokens found in database</p>";
}

echo "<hr>";
echo "<p><a href='forgot_password.php'>Test Forgot Password</a></p>";
echo "<p><a href='reset_password.php?token=test123'>Test Reset Password (invalid token)</a></p>";
?>
