<?php
echo "<h2>🔍 Testing Dashboard Access</h2>";

// Test 1: Check if initialize.php loads
echo "<h3>Test 1: Loading initialize.php</h3>";
try {
    require_once("include/initialize.php");
    echo "<p style='color: green;'>✅ initialize.php loaded successfully</p>";
    
    // Check if database object exists
    if (isset($mydb) && $mydb instanceof Database) {
        echo "<p style='color: green;'>✅ Database object created successfully</p>";
    } else {
        echo "<p style='color: red;'>❌ Database object not found or invalid</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error loading initialize.php: " . $e->getMessage() . "</p>";
}

// Test 2: Check if home.php exists and is readable
echo "<h3>Test 2: Checking home.php</h3>";
if (file_exists('home.php')) {
    echo "<p style='color: green;'>✅ home.php file exists</p>";
    
    if (is_readable('home.php')) {
        echo "<p style='color: green;'>✅ home.php is readable</p>";
    } else {
        echo "<p style='color: red;'>❌ home.php is not readable</p>";
    }
} else {
    echo "<p style='color: red;'>❌ home.php file not found</p>";
}

// Test 3: Check if theme/templates.php exists
echo "<h3>Test 3: Checking theme/templates.php</h3>";
if (file_exists('theme/templates.php')) {
    echo "<p style='color: green;'>✅ theme/templates.php exists</p>";
} else {
    echo "<p style='color: red;'>❌ theme/templates.php not found</p>";
}

// Test 4: Check web_root constant
echo "<h3>Test 4: Checking web_root constant</h3>";
if (defined('web_root')) {
    echo "<p style='color: green;'>✅ web_root constant defined: " . web_root . "</p>";
} else {
    echo "<p style='color: red;'>❌ web_root constant not defined</p>";
}

// Test 5: Check database connection
echo "<h3>Test 5: Testing database connection</h3>";
try {
    $conn = mysqli_connect('localhost', 'root', '', 'erisdb', 4306);
    if ($conn) {
        echo "<p style='color: green;'>✅ Database connection successful</p>";
        
        // Check if tables exist
        $result = mysqli_query($conn, "SHOW TABLES");
        $table_count = mysqli_num_rows($result);
        echo "<p style='color: green;'>✅ Database has $table_count tables</p>";
        
        mysqli_close($conn);
    } else {
        echo "<p style='color: red;'>❌ Database connection failed</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
}

// Test 6: Try to include home.php content
echo "<h3>Test 6: Testing home.php inclusion</h3>";
try {
    ob_start();
    include('home.php');
    $home_content = ob_get_clean();
    
    if (strlen($home_content) > 100) {
        echo "<p style='color: green;'>✅ home.php content loaded successfully (" . strlen($home_content) . " characters)</p>";
        echo "<p style='color: blue;'>First 200 characters: " . substr($home_content, 0, 200) . "...</p>";
    } else {
        echo "<p style='color: red;'>❌ home.php content seems too short</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error including home.php: " . $e->getMessage() . "</p>";
}

echo "<h3>🎯 Dashboard Test Complete</h3>";
echo "<p><a href='index.php'>Try accessing index.php now</a></p>";
?> 