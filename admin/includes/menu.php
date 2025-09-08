<?php
if (!isset($_SESSION['ADMIN_USERID'])) {
    redirect(web_root . "admin/login.php");
}
?>
<ul class="nav nav-sidebar">
    <li class="<?php echo ($_SERVER['PHP_SELF'] == web_root . "admin/index.php") ? 'active' : ''; ?>">
        <a href="<?php echo web_root; ?>admin/index.php">
            <i class="fa fa-dashboard"></i> Dashboard
        </a>
    </li>
    <li class="<?php echo ($_SERVER['PHP_SELF'] == web_root . "admin/company/index.php") ? 'active' : ''; ?>">
        <a href="<?php echo web_root; ?>admin/company/index.php">
            <i class="fa fa-building"></i> Company
        </a>
    </li>
    <li class="<?php echo ($_SERVER['PHP_SELF'] == web_root . "admin/vacancy/index.php") ? 'active' : ''; ?>">
        <a href="<?php echo web_root; ?>admin/vacancy/index.php">
            <i class="fa fa-briefcase"></i> Vacancy
        </a>
    </li>
    <li class="<?php echo ($_SERVER['PHP_SELF'] == web_root . "admin/applicants/index.php") ? 'active' : ''; ?>">
        <a href="<?php echo web_root; ?>admin/applicants/index.php">
            <i class="fa fa-users"></i> Applicants
        </a>
    </li>
    <li class="<?php echo ($_SERVER['PHP_SELF'] == web_root . "admin/interview-invitation.php") ? 'active' : ''; ?>">
        <a href="<?php echo web_root; ?>admin/interview-invitation.php">
            <i class="fa fa-video-camera"></i> AI Interviews
        </a>
    </li>
    <li class="<?php echo ($_SERVER['PHP_SELF'] == web_root . "admin/interview-results.php") ? 'active' : ''; ?>">
        <a href="<?php echo web_root; ?>admin/interview-results.php">
            <i class="fa fa-bar-chart"></i> Interview Results
        </a>
    </li>
    <!-- <li class="<?php echo ($_SERVER['PHP_SELF'] == web_root . "admin/reports.php") ? 'active' : ''; ?>">
        <a href="<?php echo web_root; ?>admin/reports.php">
            <i class="fa fa-file-text"></i> Reports
        </a>
    </li> -->
    <li class="<?php echo ($_SERVER['PHP_SELF'] == web_root . "admin/employee/index.php") ? 'active' : ''; ?>">
        <a href="<?php echo web_root; ?>admin/employee/index.php">
            <i class="fa fa-user"></i> Employees
        </a>
    </li>
    <li>
        <a href="<?php echo web_root; ?>admin/logout.php">
            <i class="fa fa-sign-out"></i> Logout
        </a>
    </li>
</ul> 