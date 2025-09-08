<?php 
if (!isset($_SESSION['ADMIN_USERID'])){
    message("Please login first!", "error");
    redirect(web_root."admin/index.php");
} 
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            List of Vacancies
            <a href="index.php?view=add" class="btn btn-primary btn-xs">
                <i class="fa fa-plus-circle fw-fa"></i> Add Job Vacancy
            </a>
        </h1>
    </div>
</div>

<!-- Filters -->
<div class="row" style="margin-bottom: 15px;">
    <form id="filterForm" class="form-inline col-lg-12" method="GET" action="">
        <div class="form-group">
            <label for="filter_company">Company:</label>
            <select class="form-control input-sm" id="filter_company" name="company">
                <option value="">All</option>
                <?php
                $mydb->setQuery("SELECT * FROM tblcompany ORDER BY COMPANYNAME");
                $companies = $mydb->loadResultList();
                foreach ($companies as $company) {
                    $selected = (isset($_GET['company']) && $_GET['company'] == $company->COMPANYID) ? 'selected' : '';
                    echo '<option value="' . htmlspecialchars($company->COMPANYID) . '" ' . $selected . '>' . htmlspecialchars($company->COMPANYNAME) . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="filter_status">Status:</label>
            <select class="form-control input-sm" id="filter_status" name="status">
                <option value="">All</option>
                <option value="Active" <?php if(isset($_GET['status']) && $_GET['status']=='Active') echo 'selected'; ?>>Active</option>
                <option value="Inactive" <?php if(isset($_GET['status']) && $_GET['status']=='Inactive') echo 'selected'; ?>>Inactive</option>
                <option value="Pending" <?php if(isset($_GET['status']) && $_GET['status']=='Pending') echo 'selected'; ?>>Pending</option>
            </select>
        </div>
        <div class="form-group">
            <label for="filter_sector">Sector:</label>
            <input type="text" class="form-control input-sm" id="filter_sector" name="sector" value="<?php echo isset($_GET['sector']) ? htmlspecialchars($_GET['sector']) : ''; ?>" placeholder="Sector">
        </div>
        <div class="form-group">
            <label for="filter_search">Search:</label>
            <input type="text" class="form-control input-sm" id="filter_search" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" placeholder="Keyword">
        </div>
        <button type="submit" class="btn btn-default btn-sm">Filter</button>
        <a href="index.php" class="btn btn-warning btn-sm">Reset Filters</a>
    </form>
</div>

<form action="controller.php?action=delete" Method="POST" id="vacancyForm">
    <div class="table-responsive">
        <table id="dash-table" class="table table-striped table-bordered table-hover" style="font-size:12px" cellspacing="0">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all"></th>
                    <th>Company Name</th>
                    <th>Occupation Title</th>
                    <th>Required Employees</th>
                    <th>Salaries</th>
                    <th>Duration</th>
                    <th>Qualification</th>
                    <th>Description</th>
                    <th>Preferred Sex</th>
                    <th>Sector</th>
                    <th>Status</th>
                    <th width="10%" align="center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Build filter query
                $where = [];
                if (!empty($_GET['company'])) {
                    $where[] = "j.COMPANYID = '" . intval($_GET['company']) . "'";
                }
                if (!empty($_GET['status'])) {
                    $where[] = "j.JOBSTATUS = '" . addslashes($_GET['status']) . "'";
                }
                if (!empty($_GET['sector'])) {
                    $where[] = "j.SECTOR_VACANCY LIKE '%" . addslashes($_GET['sector']) . "%'";
                }
                if (!empty($_GET['search'])) {
                    $search = addslashes($_GET['search']);
                    $where[] = "(j.OCCUPATIONTITLE LIKE '%$search%' OR j.JOBDESCRIPTION LIKE '%$search%' OR j.QUALIFICATION_WORKEXPERIENCE LIKE '%$search%')";
                }
                $whereSQL = $where ? ' AND ' . implode(' AND ', $where) : '';
                $mydb->setQuery("SELECT * FROM `tbljob` j, `tblcompany` c WHERE j.COMPANYID=c.COMPANYID $whereSQL ORDER BY j.JOBID DESC");
                $cur = $mydb->loadResultList();
                if ($cur) {
                    foreach ($cur as $result) {
                        $statusClass = '';
                        switch(strtolower($result->JOBSTATUS)) {
                            case 'active': $statusClass = 'success'; break;
                            case 'inactive': $statusClass = 'danger'; break;
                            case 'pending': $statusClass = 'warning'; break;
                        }
                        echo '<tr>';
                        echo '<td><input type="checkbox" name="selector[]" value="' . htmlspecialchars($result->JOBID) . '"></td>';
                        echo '<td>' . htmlspecialchars($result->COMPANYNAME) . '</td>';
                        echo '<td>' . htmlspecialchars($result->OCCUPATIONTITLE) . '</td>';
                        echo '<td>' . htmlspecialchars($result->REQ_NO_EMPLOYEES) . '</td>';
                        echo '<td>' . htmlspecialchars($result->SALARIES) . '</td>';
                        echo '<td>' . htmlspecialchars($result->DURATION_EMPLOYEMENT) . '</td>';
                        echo '<td><span title="' . htmlspecialchars($result->QUALIFICATION_WORKEXPERIENCE) . '">' . htmlspecialchars(mb_strimwidth($result->QUALIFICATION_WORKEXPERIENCE, 0, 30, '...')) . '</span></td>';
                        echo '<td><span title="' . htmlspecialchars($result->JOBDESCRIPTION) . '">' . htmlspecialchars(mb_strimwidth($result->JOBDESCRIPTION, 0, 50, '...')) . '</span></td>';
                        echo '<td>' . htmlspecialchars($result->PREFEREDSEX) . '</td>';
                        echo '<td>' . htmlspecialchars($result->SECTOR_VACANCY) . '</td>';
                        echo '<td><span class="badge badge-' . $statusClass . '">' . htmlspecialchars($result->JOBSTATUS) . '</span></td>';
                        echo '<td align="center">
                                <a title="Edit" href="index.php?view=edit&id=' . htmlspecialchars($result->JOBID) . '" class="btn btn-primary btn-xs"><span class="fa fa-edit fw-fa"></span></a>
                                <a title="Delete" href="controller.php?action=delete&id=' . htmlspecialchars($result->JOBID) . '" class="btn btn-danger btn-xs" onclick="return confirm(\'Are you sure you want to delete this vacancy?\')"><span class="fa fa-trash-o fw-fa"></span></a>
                            </td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="12" class="text-center">No vacancies found</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
    <div class="btn-group">
        <button type="submit" class="btn btn-danger" name="delete" onclick="return confirm('Are you sure you want to delete selected vacancies?')">
            <i class="fa fa-trash"></i> Delete Selected
        </button>
    </div>
</form>

<script>
$(document).ready(function() {
    var table = $('#dash-table').DataTable({
        responsive: true,
        "order": [[1, "desc"]],
        "pageLength": 10,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        "language": {
            "search": "Search:",
            "lengthMenu": "Show _MENU_ entries per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries"
        }
    });
    // Select/Deselect all checkboxes
    $('#select-all').on('click', function(){
        var rows = table.rows({ 'search': 'applied' }).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);
    });
    // Tooltip for truncated text
    $('[title]').tooltip({container: 'body'});
});
</script>
<style>
.badge-success { background-color: #5cb85c; }
.badge-danger { background-color: #d9534f; }
.badge-warning { background-color: #f0ad4e; }
.badge { padding: 5px 10px; font-size: 12px; color: #fff; }
.table > tbody > tr > td { vertical-align: middle; }
.btn { margin-right: 5px; }
@media (max-width: 767px) {
    .form-inline .form-group { display: block; margin-bottom: 10px; }
}
</style>	 