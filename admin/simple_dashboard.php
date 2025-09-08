<?php
echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";
echo "<title>Admin Dashboard - ERIS</title>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>";
echo "<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>";
echo "<style>";
echo "body { background: #f4f6f9; font-family: Arial, sans-serif; }";
echo ".navbar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }";
echo ".navbar-brand { color: white !important; font-weight: bold; }";
echo ".stats-card { background: white; border-radius: 10px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }";
echo ".stats-number { font-size: 2.5rem; font-weight: bold; color: #667eea; }";
echo ".stats-label { color: #6c757d; font-size: 1.1rem; }";
echo ".nav-btn { background: #f8f9fa; border: 2px solid #e9ecef; color: #495057; padding: 12px 20px; margin: 5px; border-radius: 8px; text-decoration: none; display: inline-block; transition: all 0.3s; }";
echo ".nav-btn:hover { background: #667eea; border-color: #667eea; color: white; text-decoration: none; transform: translateY(-2px); }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<!-- Navigation -->";
echo "<nav class='navbar navbar-default'>";
echo "<div class='container-fluid'>";
echo "<div class='navbar-header'>";
echo "<a class='navbar-brand' href='#'><i class='fa fa-briefcase'></i> ERIS Admin Panel</a>";
echo "</div>";
echo "<div class='navbar-right'>";
echo "<span style='color: white; margin-right: 15px;'><i class='fa fa-user'></i> Welcome, Admin</span>";
echo "<a href='logout.php' style='color: white; text-decoration: none; padding: 8px 15px; border: 1px solid white; border-radius: 20px;'>Logout</a>";
echo "</div>";
echo "</div>";
echo "</nav>";

echo "<div class='container-fluid' style='padding: 20px;'>";
echo "<h2 style='margin-bottom: 30px; color: #333;'><i class='fa fa-dashboard'></i> Admin Dashboard</h2>";

echo "<!-- Statistics Cards -->";
echo "<div class='row'>";
echo "<div class='col-md-3'>";
echo "<div class='stats-card text-center'>";
echo "<div class='stats-number'>25</div>";
echo "<div class='stats-label'>Total Candidates</div>";
echo "</div>";
echo "</div>";
echo "<div class='col-md-3'>";
echo "<div class='stats-card text-center'>";
echo "<div class='stats-number'>12</div>";
echo "<div class='stats-label'>Completed Interviews</div>";
echo "</div>";
echo "</div>";
echo "<div class='col-md-3'>";
echo "<div class='stats-card text-center'>";
echo "<div class='stats-number'>8</div>";
echo "<div class='stats-label'>Graded Interviews</div>";
echo "</div>";
echo "</div>";
echo "<div class='col-md-3'>";
echo "<div class='stats-card text-center'>";
echo "<div class='stats-number'>" . date('M Y') . "</div>";
echo "<div class='stats-label'>Current Month</div>";
echo "</div>";
echo "</div>";
echo "</div>";

echo "<!-- Navigation Menu -->";
echo "<div class='row' style='margin-bottom: 30px;'>";
echo "<div class='col-md-12'>";
echo "<div style='background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>";
echo "<h4 style='margin-bottom: 20px; color: #495057;'><i class='fa fa-bars'></i> Quick Navigation</h4>";
echo "<a href='applicants/' class='nav-btn'><i class='fa fa-users'></i> Applicants</a>";
echo "<a href='vacancy/' class='nav-btn'><i class='fa fa-briefcase'></i> Jobs</a>";
echo "<a href='company/' class='nav-btn'><i class='fa fa-building'></i> Companies</a>";
echo "<a href='employee/' class='nav-btn'><i class='fa fa-user-md'></i> Employees</a>";
echo "<a href='user/' class='nav-btn'><i class='fa fa-user-secret'></i> Users</a>";
echo "<a href='category/' class='nav-btn'><i class='fa fa-tags'></i> Categories</a>";
echo "<a href='reports.php' class='nav-btn'><i class='fa fa-chart-bar'></i> Reports</a>";
echo "</div>";
echo "</div>";
echo "</div>";

echo "<!-- Recent Activity -->";
echo "<div class='row'>";
echo "<div class='col-md-12'>";
echo "<div style='background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>";
echo "<h4 style='margin-bottom: 20px; color: #333;'><i class='fa fa-clock-o'></i> Recent Activity</h4>";
echo "<div class='table-responsive'>";
echo "<table class='table table-striped'>";
echo "<thead><tr><th>Action</th><th>Details</th><th>Time</th></tr></thead>";
echo "<tbody>";
echo "<tr><td><i class='fa fa-user-plus text-success'></i> New Applicant</td><td>John Doe applied for Software Engineer</td><td>2 hours ago</td></tr>";
echo "<tr><td><i class='fa fa-check-circle text-primary'></i> Interview Completed</td><td>AI interview finished for Jane Smith</td><td>4 hours ago</td></tr>";
echo "<tr><td><i class='fa fa-star text-warning'></i> Interview Graded</td><td>Admin reviewed interview results</td><td>6 hours ago</td></tr>";
echo "</tbody>";
echo "</table>";
echo "</div>";
echo "</div>";
echo "</div>";
echo "</div>";

echo "</div>"; // End container

echo "<script src='https://code.jquery.com/jquery-3.6.0.min.js'></script>";
echo "<script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'></script>";
echo "</body>";
echo "</html>";
?> 