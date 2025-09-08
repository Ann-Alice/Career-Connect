<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>CAREER CONNECT Admin Panel</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo web_root; ?>bootstrap/css/bootstrap.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo web_root; ?>plugins/font-awesome/css/font-awesome.min.css">
    
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo web_root; ?>theme/css/AdminLTE.min.css">
    <link rel="stylesheet" href="<?php echo web_root; ?>theme/css/skins/_all-skins.min.css">
    
    <!-- jQuery -->
    <script src="<?php echo web_root; ?>plugins/jQuery/jQuery-2.1.4.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="<?php echo web_root; ?>bootstrap/js/bootstrap.min.js"></script>
    
    <!-- AdminLTE App -->
    <script src="<?php echo web_root; ?>theme/js/app.min.js"></script>
    
    <style>
        .content-wrapper {
            background-color: #f4f6f9;
        }
        .main-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .main-sidebar {
            background: #2c3e50;
        }
    </style>
</head>
<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
        <header class="main-header">
            <a href="index.php" class="logo">
                <span class="logo-mini"><b>CAREER</b>CONNECT</span>
                <span class="logo-lg"><b>CARRER CONNECT</b> Admin</span>
            </a>
            <nav class="navbar navbar-static-top">
                <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    
                </a>
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <li>
                            <a href="logout.php">
                                <i class="fa fa-sign-out"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        
        <aside class="main-sidebar">
            <section class="sidebar">
                <ul class="sidebar-menu" data-widget="tree">
                    <li class="header">MAIN NAVIGATION</li>
                    <li>
                        <a href="home.php">
                            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="interview-results.php">
                            <i class="fa fa-chart-line"></i> <span>Interview Results</span>
                        </a>
                    </li>
                    <li>
                        <a href="reports.php">
                            <i class="fa fa-file-text"></i> <span>Reports</span>
                        </a>
                    </li>
                    <li>
                        <a href="test_professional_email.php">
                            <i class="fa fa-envelope"></i> <span>Email Templates</span>
                        </a>
                    </li>
                    <li>
                        <a href="test_email_fix.php">
                            <i class="fa fa-bug"></i> <span>Email Fix Test</span>
                        </a>
                    </li>
                </ul>
            </section>
        </aside>
        
        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    <?php echo isset($page_title) ? $page_title : 'Admin Panel'; ?>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="home.php"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active"><?php echo isset($page_title) ? $page_title : 'Admin Panel'; ?></li>
                </ol>
            </section>
            
            <section class="content">
                <?php check_message(); ?> 
</original_code>```
