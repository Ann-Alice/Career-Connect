<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <?php if (!isset($_GET['p'])) { ?>
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-briefcase"></i> Applied Jobs</h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <div class="table-responsive">
              <table id="dash-table" class="table table-hover table-striped">
                <thead>
                  <tr>
                    <th>Job Title</th>
                    <th>Company</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Applied Date</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $sql = "SELECT r.*, c.COMPANYNAME, c.COMPANYADDRESS, j.OCCUPATIONTITLE, j.DATEPOSTED 
                          FROM `tblcompany` c, `tbljobregistration` r, `tbljob` j 
                          WHERE c.`COMPANYID`=r.`COMPANYID` 
                          AND r.`JOBID`=j.`JOBID` 
                          AND r.`APPLICANTID` = {$_SESSION['APPLICANTID']}
                          ORDER BY r.REGISTRATIONID DESC";
                  $mydb->setQuery($sql);
                  $cur = $mydb->loadResultList();
                  foreach ($cur as $result) {
                    $statusClass = '';
                    switch(strtolower($result->REMARKS)) {
                      case 'pending':
                        $statusClass = 'label-warning';
                        break;
                      case 'hired':
                        $statusClass = 'label-success';
                        break;
                      case 'rejected':
                        $statusClass = 'label-danger';
                        break;
                      default:
                        $statusClass = 'label-info';
                    }
                    echo '<tr>';
                    echo '<td><a href="index.php?view=appliedjobs&p=job&id='.$result->REGISTRATIONID.'" class="text-primary"><i class="fa fa-briefcase"></i> '.$result->OCCUPATIONTITLE.'</a></td>';
                    echo '<td><i class="fa fa-building"></i> '.$result->COMPANYNAME.'</td>';
                    echo '<td><i class="fa fa-map-marker"></i> '.$result->COMPANYADDRESS.'</td>';
                    echo '<td><span class="label '.$statusClass.'">'.$result->REMARKS.'</span></td>';
                    echo '<td><i class="fa fa-calendar"></i> '.date('M d, Y', strtotime($result->REGISTRATIONDATE)).'</td>';
                    echo '<td>
                            <a href="index.php?view=appliedjobs&p=job&id='.$result->REGISTRATIONID.'" class="btn btn-primary btn-sm" title="View Details">
                              <i class="fa fa-eye"></i>
                            </a>
                          </td>';
                    echo '</tr>';
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- /.col -->
      <?php } else {
        require_once("viewjob.php");
      } ?>
    </div>
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>

<script>
$(document).ready(function() {
  $('#dash-table').DataTable({
    "responsive": true,
    "language": {
      "search": "Search jobs:",
      "lengthMenu": "Show _MENU_ entries per page",
      "info": "Showing _START_ to _END_ of _TOTAL_ applications"
    },
    "order": [[4, "desc"]], // Sort by applied date by default
    "pageLength": 10
  });
});
</script>
   
 