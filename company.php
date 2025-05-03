<!-- Add AOS CSS and JS files -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    AOS.init({
      duration: 800,
      easing: 'ease-in-out',
      once: true
    });
  });
</script>

<section id="content">
    <div class="container content">
        <!-- Section Header -->
        <div class="row">
            <div class="col-md-12" data-aos="fade-up">
                <div style="text-align: center; margin-bottom: 50px;">
                    <h2 style="font-size: 32px; margin-bottom: 25px; color: #2c3e50; position: relative; padding-bottom: 15px; display: inline-block;">
                        Our Partner Companies
                        <span style="display: block; position: absolute; width: 60px; height: 3px; background-color: #086e96; bottom: 0; left: 50%; transform: translateX(-50%);"></span>
                    </h2>
                    <p style="max-width: 800px; margin: 0 auto; line-height: 1.8; color: #555;">
                        Discover our network of trusted partner companies offering exciting career opportunities across various industries. Connect with leading organizations that are actively hiring talented professionals.
                    </p>
                </div>
            </div>
        </div>
    
        <!-- Service Blocks -->  
        <div class="row">
            <?php 
                  $sql = "SELECT * FROM `tblcompany`";
                  $mydb->setQuery($sql);
                  $comp = $mydb->loadResultList(); 
                  
                  $delay = 100;
                  foreach ($comp as $company ) { 
            ?>
                    <div class="col-sm-4 info-blocks" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                        <div style="background-color: white; padding: 25px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); margin-bottom: 30px; transition: all 0.3s ease; height: 100%;" onmouseover="this.style.transform='translateY(-10px)';this.style.boxShadow='0 15px 30px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 5px 15px rgba(0,0,0,0.08)';">
                            <div style="display: flex; align-items: flex-start;">
                                <i class="fa fa-building" style="font-size: 28px; color: #086e96; margin-right: 15px; background-color: #f1f8ff; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;"></i>
                                <div class="info-blocks-in">
                                    <h3 style="margin-top: 0; margin-bottom: 15px; color: #2c3e50; font-size: 20px;"><?php echo '<a href="'.web_root.'index.php?q=hiring&search='.$company->COMPANYNAME.'" style="color: #2c3e50; text-decoration: none; transition: color 0.3s ease;" onmouseover="this.style.color=\'#086e96\';" onmouseout="this.style.color=\'#2c3e50\';">'.$company->COMPANYNAME.'</a>';?></h3>
                                    <!-- <p><?php echo $company->COMPANYMISSION;?></p> -->
                                    <p style="margin-bottom: 8px; color: #555; display: flex; align-items: center;">
                                        <i class="fa fa-map-marker" style="color: #086e96; margin-right: 8px;"></i>
                                        <span>Address: <?php echo $company->COMPANYADDRESS;?></span>
                                    </p>
                                    <p style="margin-bottom: 0; color: #555; display: flex; align-items: center;">
                                        <i class="fa fa-phone" style="color: #086e96; margin-right: 8px;"></i>
                                        <span>Contact No.: <?php echo $company->COMPANYCONTACTNO;?></span>
                                    </p>
                                    <div style="margin-top: 15px;">
                                        <a href="<?php echo web_root; ?>index.php?q=hiring&search=<?php echo $company->COMPANYNAME; ?>" class="btn btn-sm" style="background-color: #086e96; color: white; border: none; padding: 6px 15px; border-radius: 4px; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#075d7e';" onmouseout="this.style.backgroundColor='#086e96';">
                                            <i class="fa fa-briefcase" style="margin-right: 5px;"></i> View Jobs
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php 
                  $delay += 100;
                  } 
            ?>
         </div> 
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if there are no companies and show a message
    const companyBlocks = document.querySelectorAll('.info-blocks');
    if (companyBlocks.length === 0) {
        const rowElement = document.querySelector('.row:nth-child(2)');
        if (rowElement) {
            const noCompaniesDiv = document.createElement('div');
            noCompaniesDiv.className = 'col-md-12 text-center';
            noCompaniesDiv.innerHTML = '<div style="padding: 50px; background-color: #f8f9fa; border-radius: 8px; margin: 30px 0;"><i class="fa fa-info-circle" style="font-size: 48px; color: #086e96; margin-bottom: 20px;"></i><h4 style="color: #2c3e50; margin-bottom: 10px;">No Companies Found</h4><p style="color: #555;">There are currently no partner companies listed. Please check back later.</p></div>';
            rowElement.appendChild(noCompaniesDiv);
        }
    }
});
</script>