<section id="content">
    <div class="container content">     
        <!-- Search and Filter Section -->
        <div class="row">
            <div class="col-md-12">
                <div class="well well-sm" style="margin-bottom: 20px;">
                    <form action="" method="GET" class="form-horizontal">
                        <input type="hidden" name="q" value="hiring">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                    <input type="text" name="search" class="form-control" placeholder="Search by company name..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary" type="submit">Search</button>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="<?php echo web_root; ?>index.php?q=hiring" class="btn btn-default">View All</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Job Listings Section -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4><i class="fa fa-briefcase"></i> Available Positions</h4>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="job-listings" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th><i class="fa fa-briefcase"></i> Job Title</th>
                                        <th><i class="fa fa-building-o"></i> Company</th>
                                        <th><i class="fa fa-map-marker"></i> Location</th>
                                        <th><i class="fa fa-calendar"></i> Date Posted</th>
                                        <th><i class="fa fa-info-circle"></i> Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (isset($_GET['search'])) {
                                        $COMPANYNAME = $_GET['search'];
                                    } else {
                                        $COMPANYNAME = '';
                                    }
                                    $sql = "SELECT * FROM `tblcompany` c,`tbljob` j WHERE c.`COMPANYID`=j.`COMPANYID` AND COMPANYNAME LIKE '%" . $COMPANYNAME ."%' ORDER BY DATEPOSTED DESC";
                                    $mydb->setQuery($sql);
                                    $cur = $mydb->loadResultList();

                                    if (count($cur) > 0) {
                                        foreach ($cur as $result) {
                                            echo '<tr>';
                                            echo '<td><a href="'.web_root.'index.php?q=viewjob&search='.$result->JOBID.'" class="job-title">'.$result->OCCUPATIONTITLE.'</a></td>';
                                            echo '<td>'.$result->COMPANYNAME.'</td>';
                                            echo '<td>'.$result->COMPANYADDRESS.'</td>';
                                            echo '<td>'.date_format(date_create($result->DATEPOSTED),'M d, Y').'</td>';
                                            echo '<td><a href="'.web_root.'index.php?q=viewjob&search='.$result->JOBID.'" class="btn btn-info btn-sm"><i class="fa fa-info-circle"></i> Details</a></td>';
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="5" class="text-center">No job listings found.</td></tr>';
                                    }
                                    ?> 
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Featured Job Cards Section -->
        <div class="row">
            <div class="col-md-12">
                <h3 class="page-header"><i class="fa fa-star"></i> Featured Positions</h3>
            </div>
            
            <?php
            // Get featured/latest jobs for card display
            $featuredSql = "SELECT * FROM `tblcompany` c,`tbljob` j WHERE c.`COMPANYID`=j.`COMPANYID` ORDER BY DATEPOSTED DESC LIMIT 6";
            $mydb->setQuery($featuredSql);
            $featuredJobs = $mydb->loadResultList();
            
            foreach ($featuredJobs as $job) {
            ?>
            <div class="col-md-4 col-sm-6">
                <div class="panel panel-default" style="margin-bottom: 20px;">
                    <div class="panel-heading" style="background-color: #f8f8f8;">
                        <h4 class="panel-title" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <i class="fa fa-briefcase"></i> <?php echo $job->OCCUPATIONTITLE; ?>
                        </h4>
                    </div>
                    <div class="panel-body">
                        <p><strong><i class="fa fa-building-o"></i> Company:</strong> <?php echo $job->COMPANYNAME; ?></p>
                        <p><strong><i class="fa fa-map-marker"></i> Location:</strong> <?php echo $job->COMPANYADDRESS; ?></p>
                        <p><strong><i class="fa fa-calendar"></i> Posted:</strong> <?php echo date_format(date_create($job->DATEPOSTED),'M d, Y'); ?></p>
                        <div class="text-center" style="margin-top: 15px;">
                            <a href="<?php echo web_root; ?>index.php?q=viewjob&search=<?php echo $job->JOBID; ?>" class="btn btn-primary">
                                <i class="fa fa-search"></i> View Details
                            </a>
                            <a href="<?php echo web_root; ?>index.php?q=apply&job=<?php echo $job->JOBID; ?>&view=personalinfo" class="btn btn-success">
                                <i class="fa fa-check-circle"></i> Apply Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</section>

<script type="text/javascript">
    $(document).ready(function() {
        $('#job-listings').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "pageLength": 10,
            "order": [[ 3, "desc" ]]
        });
    });
</script> 