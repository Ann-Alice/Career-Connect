<?php
// Simple dashboard test - bypass routing
require_once("include/initialize.php");

// Set title
$title = "Dashboard Test";
$active_home = 'active';

echo "<!DOCTYPE html>";
echo "<html><head>";
echo "<title>Dashboard Test</title>";
echo "<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>";
echo "<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>";
echo "</head><body>";

echo "<div class='container-fluid'>";
echo "<h1>🎯 Dashboard Test - Direct Access</h1>";
echo "<p>This bypasses the routing system to test if the dashboard content works.</p>";

// Include the dashboard content directly
echo "<div class='row'>";
echo "<div class='col-md-12'>";
echo "<h3>Dashboard Content:</h3>";
echo "<hr>";

// Include home.php content
ob_start();
include('home.php');
$dashboard_content = ob_get_clean();

echo $dashboard_content;

echo "</div>";
echo "</div>";

echo "<hr>";
echo "<h3>🔧 Debug Information:</h3>";
echo "<ul>";
echo "<li><strong>Database Tables:</strong> ";
$result = $mydb->setQuery("SHOW TABLES");
$result->execute();
echo $result->num_rows() . " tables found</li>";
echo "<li><strong>Web Root:</strong> " . web_root . "</li>";
echo "<li><strong>Site Root:</strong> " . SITE_ROOT . "</li>";
echo "<li><strong>Database Object:</strong> " . (isset($mydb) ? "Available" : "Missing") . "</li>";
echo "</ul>";

echo "<p><a href='index.php' class='btn btn-primary'>Try Main Index Now</a></p>";
echo "</div>";

echo "<script src='https://code.jquery.com/jquery-3.5.1.min.js'></script>";
echo "<script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'></script>";
echo "</body></html>";
?> 