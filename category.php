    <section id="content">
        <div class="container content">     
        <!-- Category Jobs Section -->
        <div class="row">
            <div class="col-md-12">
                <div class="page-header">
                    <h2><i class="fa fa-briefcase"></i> Jobs in <?php echo isset($_GET['search']) ? $_GET['search'] : 'All Categories'; ?> <small>Find your perfect career opportunity</small></h2>
                </div>
            </div>
        </div>
     
        <?php
        if (isset($_GET['search'])) {
            $category = $_GET['search'];
        } else {
            $category = '';
        }
        $sql = "SELECT * FROM `tblcompany` c,`tbljob` j WHERE c.`COMPANYID`=j.`COMPANYID` AND CATEGORY LIKE '%" . $category ."%' ORDER BY DATEPOSTED DESC";
        $mydb->setQuery($sql);
        $cur = $mydb->loadResultList();

        if (count($cur) == 0) {
            echo '<div class="alert alert-info">
                    <strong><i class="fa fa-info-circle"></i> No jobs found</strong> in this category. Please try another search.
                    <p class="mt-2">
                        <a href="'.web_root.'index.php?q=advancesearch" class="btn btn-default"><i class="fa fa-search"></i> Try Advanced Search</a>
                    </p>
                  </div>';
        }

        foreach ($cur as $result) { 
        ?>  
          <div class="panel panel-primary">
              <div class="panel-header">
                   <div style="border-bottom: 1px solid #ddd;padding: 15px;font-size: 22px;font-weight: bold;color: #337ab7;margin-bottom: 5px;background-color: #f8f8f8;border-radius: 4px 4px 0 0;">
                       <a href="<?php echo web_root.'index.php?q=viewjob&search='.$result->JOBID;?>">
                           <i class="fa fa-briefcase"></i> <?php echo $result->OCCUPATIONTITLE; ?>
                       </a>
                       <span class="pull-right" style="font-size: 14px;color: #777;">
                           <i class="fa fa-building-o"></i> <?php echo $result->COMPANYNAME; ?>
                       </span>
                   </div> 
              </div>
              <div class="panel-body contentbody">
                    <div class="row">
                        <div class="col-sm-10">
                            <div class="job-details">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <p><strong><i class="fa fa-map-marker"></i> Location:</strong> <?php echo $result->COMPANYADDRESS; ?></p>
                                            <p><strong><i class="fa fa-calendar"></i> Posted:</strong> <?php echo date_format(date_create($result->DATEPOSTED), 'M d, Y'); ?></p>
                                        </div>
                                        <div class="col-sm-6">
                                            <p><strong><i class="fa fa-tags"></i> Category:</strong> <?php echo $result->CATEGORY; ?></p>
                                            <p><strong><i class="fa fa-clock-o"></i> Employment Type:</strong> <?php echo $result->DURATION_EMPLOYEMENT; ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div style="margin-top: 15px; border-top: 1px dashed #ddd; padding-top: 15px;">
                                        <h4><i class="fa fa-file-text-o"></i> Qualification/Work Experience:</h4>
                                        <div style="padding-left: 15px;">
                                            <?php echo $result->QUALIFICATION_WORKEXPERIENCE; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12"> 
                                    <div style="margin-top: 15px; border-top: 1px dashed #ddd; padding-top: 15px;">
                                        <h4><i class="fa fa-tasks"></i> Job Description:</h4>
                                        <div style="padding-left: 15px;">
                                            <?php echo $result->JOBDESCRIPTION; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2 text-center"> 
                            <div style="margin-top: 20px;">
                                <a href="<?php echo web_root; ?>index.php?q=apply&job=<?php echo $result->JOBID;?>&view=personalinfo" class="btn btn-primary btn-lg" style="width: 100%;">
                                    <i class="fa fa-send"></i><br>Apply Now
                                </a>
                                <a href="<?php echo web_root.'index.php?q=viewjob&search='.$result->JOBID;?>" class="btn btn-info" style="width: 100%; margin-top: 10px;">
                                    <i class="fa fa-info-circle"></i> Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div> 
              <div class="panel-footer" style="background-color: #f5f5f5; color: #666; font-style: italic;">
                  <i class="fa fa-clock-o"></i> Posted on: <?php echo date_format(date_create($result->DATEPOSTED), 'M d, Y'); ?>
                  <?php if(!empty($result->SECTOR_VACANCY)): ?>
                  <span class="pull-right"><i class="fa fa-industry"></i> Sector: <?php echo $result->SECTOR_VACANCY; ?></span>
                  <?php endif; ?>
              </div>
          </div> 
        <?php } ?>
        
        <?php if (count($cur) > 0): ?>
        <div class="text-center">
            <a href="<?php echo web_root; ?>index.php?q=advancesearch" class="btn btn-lg btn-primary">
                <i class="fa fa-search"></i> Advanced Search
            </a>
            <a href="<?php echo web_root; ?>index.php?q=hiring" class="btn btn-lg btn-success">
                <i class="fa fa-list"></i> See All Available Jobs
            </a>
        </div>
        <?php endif; ?>
        
     </div>
    </section>  
