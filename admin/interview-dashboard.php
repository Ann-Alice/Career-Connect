<?php
/**
 * Interview Dashboard
 * Central hub for all interview-related functionality
 */

require_once('../include/initialize.php');

// Check if user is admin
if (!isset($_SESSION['ADMIN_USERID'])) {
    header("Location: login.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interview Dashboard - Career Connect Admin</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            background: #f8fafc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            margin-bottom: 30px;
        }
        .card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        .btn-dashboard {
            border-radius: 10px;
            padding: 12px 20px;
            font-weight: 600;
            margin: 5px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-dashboard:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .btn-primary-dashboard {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
        }
        .btn-success-dashboard {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            color: white;
        }
        .btn-info-dashboard {
            background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
            border: none;
            color: white;
        }
        .btn-warning-dashboard {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            border: none;
            color: white;
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(102,126,234,0.3);
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 10px 0;
        }
        .stat-label {
            font-size: 1rem;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-inverse navbar-fixed-top" style="background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-menu" style="border: none; background: transparent;">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar" style="background-color: white; height: 3px; border-radius: 2px;"></span>
                    <span class="icon-bar" style="background-color: white; height: 3px; border-radius: 2px;"></span>
                    <span class="icon-bar" style="background-color: white; height: 3px; border-radius: 2px;"></span>
                </button>
                <a class="navbar-brand" href="index.php" style="color: white; font-weight: 700; font-size: 1.4rem; padding: 20px 25px; text-shadow: 0 2px 4px rgba(0,0,0,0.3); transition: all 0.3s ease;">
                    <i class="fa fa-briefcase" style="margin-right: 8px;"></i>Career Connect
                </a>
            </div>
            <div class="navbar-right" style="padding: 18px 25px;">
                <div style="display: flex; align-items: center; color: white;">
                    <div style="background: rgba(255,255,255,0.2); border-radius: 50%; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                        <i class="fa fa-user" style="font-size: 16px;"></i>
                    </div>
                    <span style="font-weight: 600; text-shadow: 0 1px 3px rgba(0,0,0,0.3);">JANO</span>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid" style="margin-top: 60px; padding: 30px;">
        <div class="dashboard-header">
            <h1><i class="fa fa-video-camera"></i> AI Interview Dashboard</h1>
            <p class="lead">Manage and review all AI-powered interviews in one place</p>
        </div>

        <?php
        // Get statistics
        global $mydb;
        $stats = [
            'total_invitations' => 0,
            'pending_interviews' => 0,
            'completed_interviews' => 0,
            'graded_interviews' => 0
        ];

        // Total invitations
        $sql = "SELECT COUNT(*) as count FROM tblinterviewinvitations";
        $mydb->setQuery($sql);
        $result = $mydb->loadSingleResult();
        $stats['total_invitations'] = $result ? $result->count : 0;

        // Pending interviews
        $sql = "SELECT COUNT(*) as count FROM tbljobregistration WHERE INTERVIEW_STATUS IS NULL OR INTERVIEW_STATUS = 'pending'";
        $mydb->setQuery($sql);
        $result = $mydb->loadSingleResult();
        $stats['pending_interviews'] = $result ? $result->count : 0;

        // Completed interviews
        $sql = "SELECT COUNT(*) as count FROM tbljobregistration WHERE INTERVIEW_STATUS = 'Completed' OR INTERVIEW_STATUS = 'completed'";
        $mydb->setQuery($sql);
        $result = $mydb->loadSingleResult();
        $stats['completed_interviews'] = $result ? $result->count : 0;

        // Graded interviews
        $sql = "SELECT COUNT(*) as count FROM tbljobregistration WHERE ADMIN_GRADE IS NOT NULL AND ADMIN_GRADE != ''";
        $mydb->setQuery($sql);
        $result = $mydb->loadSingleResult();
        $stats['graded_interviews'] = $result ? $result->count : 0;
        ?>

        <div class="row">
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stat-number"><?php echo $stats['total_invitations']; ?></div>
                    <div class="stat-label">Total Invitations</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stat-number"><?php echo $stats['pending_interviews']; ?></div>
                    <div class="stat-label">Pending Interviews</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stat-number"><?php echo $stats['completed_interviews']; ?></div>
                    <div class="stat-label">Completed Interviews</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stat-number"><?php echo $stats['graded_interviews']; ?></div>
                    <div class="stat-label">Graded Interviews</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="text-center card-icon">
                        <i class="fa fa-paper-plane text-primary"></i>
                    </div>
                    <h3 class="text-center">Send Interview Invitations</h3>
                    <p class="text-center">Invite candidates to complete AI-powered interviews</p>
                    <div class="text-center">
                        <a href="interview-invitation.php" class="btn btn-dashboard btn-primary-dashboard">
                            <i class="fa fa-paper-plane"></i> Send Invitations
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="text-center card-icon">
                        <i class="fa fa-bar-chart text-success"></i>
                    </div>
                    <h3 class="text-center">Review Interview Results</h3>
                    <p class="text-center">View AI analysis, watch recordings, and grade candidates</p>
                    <div class="text-center">
                        <a href="interview-results.php" class="btn btn-dashboard btn-success-dashboard">
                            <i class="fa fa-bar-chart"></i> View Results
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="text-center card-icon">
                        <i class="fa fa-database text-info"></i>
                    </div>
                    <h3 class="text-center">Database Setup</h3>
                    <p class="text-center">Initialize or update database schema for interview functionality</p>
                    <div class="text-center">
                        <a href="init-interview-db.php" class="btn btn-dashboard btn-info-dashboard">
                            <i class="fa fa-database"></i> Initialize Database
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="text-center card-icon">
                        <i class="fa fa-cogs text-warning"></i>
                    </div>
                    <h3 class="text-center">System Testing</h3>
                    <p class="text-center">Test interview functionality and verify data</p>
                    <div class="text-center">
                        <a href="test-interview-results.php" class="btn btn-dashboard btn-warning-dashboard">
                            <i class="fa fa-cogs"></i> Run Tests
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <h3><i class="fa fa-info-circle"></i> Getting Started</h3>
            <ol>
                <li><strong>Initialize Database:</strong> Run the database initialization script to ensure all required tables and columns exist</li>
                <li><strong>Send Invitations:</strong> Invite candidates to complete AI interviews through the invitation system</li>
                <li><strong>Monitor Progress:</strong> Track interview completion status in the dashboard</li>
                <li><strong>Review Results:</strong> Analyze AI-generated results and watch interview recordings</li>
                <li><strong>Grade Candidates:</strong> Provide your own assessment and compare with AI analysis</li>
                <li><strong>Send Feedback:</strong> Communicate results to candidates through professional email templates</li>
            </ol>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>