  <section id="banner">
  
  <style>
  .job-thumbnail {
    background-color: #086e96;
    padding: 15px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    text-align: center;
    height: 100%;
    cursor: pointer;
    min-height: 150px;
    transform: translateY(0);
    transition: all 0.3s ease;
  }
  
  .job-thumbnail:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 20px rgba(0,0,0,0.2);
  }
  </style>
  
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
   
  <!-- Slider -->
        <div id="main-slider" class="flexslider" data-aos="fade-in">
            <ul class="slides">
              <li>
                <img src="<?php echo web_root; ?>plugins/home-plugins/img/slides/2.1.jpg" alt="" style="opacity: 0.5;"/>
                <div class="flex-caption">
                    <h3 style="color: white;">innovation</h3> 
          <p style="color: white;">We create the opportunities</p> 
           
                </div>
              </li>
              <li>
                <img src="<?php echo web_root; ?>plugins/home-plugins/img/slides/4.jpg" alt="" style="opacity: 0.5;"/>
                <div class="flex-caption">
                    <h3 style="color:white;">Specialize</h3> 
                    <p style="color: white; margin-bottom: 10rem;">Success dends on work</p> 
           
                </div>
              </li>
            </ul>
        </div>
  <!-- end slider -->
 
  </section> 
  <section id="call-to-action-2">
    <div class="container">
      <div class="row">
        <div class="col-md-7 col-sm-7" data-aos="fade-right" data-aos-delay="100">
          <h3>Partner with Business Leaders</h3>
          <p>Connect with top executives and decision-makers across industries. Our platform bridges the gap between ambitious talent and forward-thinking organizations, creating opportunities for meaningful career advancement and strategic growth.</p>
          
          <!-- Counter Stats -->
          <div class="row" style="margin-top: 30px;">
            <div class="col-md-3 col-xs-6 text-center" data-aos="fade-up" data-aos-delay="200">
              <div class="counter-item">
                <i class="fa fa-users" style="font-size: 24px; color: #086e96;"></i>
                <h3><span class="counter-number" data-count="500">0</span>+</h3>
                <p>Business Partners</p>
              </div>
            </div>
            <div class="col-md-3 col-xs-6 text-center" data-aos="fade-up" data-aos-delay="300">
              <div class="counter-item">
                <i class="fa fa-building" style="font-size: 24px; color: #086e96;"></i>
                <h3><span class="counter-number" data-count="125">0</span>+</h3>
                <p>Companies</p>
              </div>
            </div>
            <div class="col-md-3 col-xs-6 text-center" data-aos="fade-up" data-aos-delay="400">
              <div class="counter-item">
                <i class="fa fa-briefcase" style="font-size: 24px; color: #086e96;"></i>
                <h3><span class="counter-number" data-count="3500">0</span>+</h3>
                <p>Jobs Posted</p>
              </div>
            </div>
            <div class="col-md-3 col-xs-6 text-center" data-aos="fade-up" data-aos-delay="500">
              <div class="counter-item">
                <i class="fa fa-handshake" style="font-size: 24px; color: #086e96;"></i>
                <h3><span class="counter-number" data-count="95">0</span>%</h3>
                <p>Success Rate</p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-5 col-sm-5" data-aos="fade-left" data-aos-delay="300">
          <div class="card" style="border-radius: 8px; overflow: hidden; box-shadow: 0 10px 20px rgba(0,0,0,0.25); height: 100%; border: 1px solid #e0e0e0;">
            <img src="<?php echo web_root; ?>plugins/home-plugins/img/businessmen-handshake.jpg" alt="Business Leaders" style="width: 100%; max-height: 220px; object-fit: contain; background-color: #f8f9fa; padding: 6px;">
          </div>
        </div>
       <!--  <div class="col-md-2 col-sm-3">
          <a href="#" class="btn btn-primary">Read More</a>
        </div> -->
      </div>
    </div>
  </section>
  
  <!-- Add counter script -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Wait for AOS to initialize
      setTimeout(function() {
        const counterElements = document.querySelectorAll('.counter-number');
        
        const startCounting = function(entries, observer) {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              const target = entry.target;
              const countTo = parseInt(target.getAttribute('data-count'));
              let count = 0;
              const duration = 2000; // 2 seconds
              const increment = countTo / (duration / 15); // Update every 15ms
              
              const timer = setInterval(function() {
                count += increment;
                if (count >= countTo) {
                  target.textContent = countTo;
                  clearInterval(timer);
                } else {
                  target.textContent = Math.floor(count);
                }
              }, 15);
              
              observer.unobserve(target);
            }
          });
        };
        
        const observer = new IntersectionObserver(startCounting, {
          root: null,
          threshold: 0.1
        });
        
        counterElements.forEach(element => {
          observer.observe(element);
        });
      }, 500);
    });
  </script>
  
  
  
  <section class="section-padding gray-bg">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="section-title text-center" data-aos="fade-up">
            <h2>Popular Jobs</h2>  
          </div>
        </div>
      </div>
      <div class="row">
          <?php 
            $sql = "SELECT * FROM `tblcategory`";
            $mydb->setQuery($sql);
            $cur = $mydb->loadResultList();

          $delay = 0;
            foreach ($cur as $result) {
            // Get appropriate icon and color based on category
            $icon = 'fa-briefcase'; // default icon
            $color = '#086e96'; // default color
            $category = strtolower($result->CATEGORY);
            
            if (strpos($category, 'it') !== false || strpos($category, 'tech') !== false) {
              $icon = 'fa-laptop';
              $color = '#2c3e50';
            } elseif (strpos($category, 'health') !== false || strpos($category, 'medical') !== false) {
              $icon = 'fa-heartbeat';
              $color = '#e74c3c';
            } elseif (strpos($category, 'education') !== false || strpos($category, 'teacher') !== false) {
              $icon = 'fa-graduation-cap';
              $color = '#27ae60';
            } elseif (strpos($category, 'finance') !== false || strpos($category, 'account') !== false) {
              $icon = 'fa-money';
              $color = '#f39c12';
            } elseif (strpos($category, 'sales') !== false || strpos($category, 'marketing') !== false) {
              $icon = 'fa-money';
              $color = '#8e44ad';
            } elseif (strpos($category, 'engineer') !== false) {
              $icon = 'fa-cogs';
              $color = '#16a085';
            } elseif (strpos($category, 'design') !== false || strpos($category, 'creative') !== false) {
              $icon = 'fa-paint-brush';
              $color = '#d35400';
            } elseif (strpos($category, 'customer') !== false || strpos($category, 'service') !== false) {
              $icon = 'fa-headset';
              $color = '#2980b9';
            } elseif (strpos($category, 'legal') !== false || strpos($category, 'law') !== false) {
              $icon = 'fa-balance-scale';
              $color = '#34495e';
            } elseif (strpos($category, 'food') !== false || strpos($category, 'restaurant') !== false) {
              $icon = 'fa-utensils';
              $color = '#c0392b';
            }

            echo '<div class="col-md-3 col-sm-6" style="margin-bottom: 20px;" data-aos="zoom-in" data-aos-delay="'.$delay.'">
                    <a href="'.web_root.'index.php?q=category&search='.$result->CATEGORY.'" style="text-decoration: none;">
                      <div class="job-thumbnail" style="background: '.$color.';">
                        <i class="fa '.$icon.'" style="font-size: 35px; color: white; margin-bottom: 10px;"></i>
                        <h4 style="margin: 10px 0; color: white; font-size: 16px;">'.$result->CATEGORY.'</h4>
                      </div>
                    </a>
                  </div>';
                  
            // Increment delay for staggered animation
            $delay += 50;
          }
        ?>
      </div>
    </div>
  </section>    
  <section id="content-3-10" class="content-block data-section nopad content-3-10">
  <div class="image-container col-sm-6 col-xs-12 pull-left" data-aos="fade-right">
    <div class="background-image-holder">

    </div>
  </div>

  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-6 col-sm-offset-6 col-xs-12 content" data-aos="fade-left">
        <div class="editContent">
          <h3 style="font-size: 28px; margin-bottom: 25px; color: #2c3e50; position: relative; padding-bottom: 15px;">
            Our Team
            <span style="display: block; position: absolute; width: 50px; height: 3px; background-color: #086e96; bottom: 0;"></span>
          </h3>
        </div>
        <div class="team-content" style="height:auto;">
          <div class="team-point" data-aos="fade-up" data-aos-delay="200" style="margin-bottom: 20px;">
            <div style="display: flex; align-items: flex-start;">
              <div style="margin-right: 15px; padding-top: 5px;"><i class="fa fa-users" style="color: #086e96; font-size: 20px;"></i></div>
              <p style="margin: 0;"><strong style="color: #2c3e50;">Collaborative Spirit</strong><br>
              Our "one team" attitude breaks down silos and helps us engage equally effectively from the C-suite to the front line. Our collaborative working style emphasizes teamwork, trust, and tolerance for diverging opinions.</p>
            </div>
          </div>
          
          <div class="team-point" data-aos="fade-up" data-aos-delay="300" style="margin-bottom: 20px;">
            <div style="display: flex; align-items: flex-start;">
              <div style="margin-right: 15px; padding-top: 5px;"><i class="fa fa-rocket" style="color: #086e96; font-size: 20px;"></i></div>
              <p style="margin: 0;"><strong style="color: #2c3e50;">Results-Driven</strong><br>
              We have a passion for our clients' true results and a pragmatic drive for action that starts Monday morning 8am and doesn't let up. We rally clients with our infectious energy, to make change stick.</p>
            </div>
          </div>
          
          <div class="team-point" data-aos="fade-up" data-aos-delay="400" style="margin-bottom: 20px;">
            <div style="display: flex; align-items: flex-start;">
              <div style="margin-right: 15px; padding-top: 5px;"><i class="fa fa-handshake" style="color: #086e96; font-size: 20px;"></i></div>
              <p style="margin: 0;"><strong style="color: #2c3e50;">Supportive Environment</strong><br>
              We never go it alone. We support and are supported to develop our own personal results stories. We balance challenging and co-creating with our clients, building the internal capabilities required for them to create repeatable results.</p>
            </div>
          </div>
          
          
        </div> 
      </div>
    </div><!-- /.row-->
  </div><!-- /.container -->
</section>
  
  <div class="about home-about">
        <div class="container">
            <div class="row">
              <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <!-- Heading and para -->
                <div class="block-heading-two">
                  <h3 style="font-size: 24px; margin-bottom: 20px; color: #2c3e50; position: relative; padding-bottom: 15px;">
                    Our Programs
                    <span style="display: block; position: absolute; width: 40px; height: 3px; background-color: #086e96; bottom: 0;"></span>
                  </h3>
                </div>
                <div class="program-item" data-aos="fade-up" data-aos-delay="150">
                  <div style="margin-bottom: 15px;">
                    <i class="fa fa-certificate" style="color: #086e96; font-size: 18px; margin-right: 8px;"></i>
                    <strong style="color: #2c3e50; font-size: 16px;">Career Development</strong>
                  </div>
                  <p>Our career development programs help professionals at all levels identify and achieve their career goals through personalized coaching, skill assessments, and strategic planning.</p>
                </div>
                <div class="program-item" data-aos="fade-up" data-aos-delay="200">
                  <div style="margin-bottom: 15px;">
                    <i class="fa fa-users" style="color: #086e96; font-size: 18px; margin-right: 8px;"></i>
                    <strong style="color: #2c3e50; font-size: 16px;">Leadership Training</strong>
                  </div>
                  <p>Develop essential leadership skills that will help you inspire teams, drive innovation, and navigate organizational challenges with confidence and strategic vision.</p>
                </div>
              </div>
              
              <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="block-heading-two">
                  <h3 style="font-size: 24px; margin-bottom: 20px; color: #2c3e50; position: relative; padding-bottom: 15px;">
                    Latest News
                    <span style="display: block; position: absolute; width: 40px; height: 3px; background-color: #086e96; bottom: 0;"></span>
                  </h3>
                </div>    
                <!-- Accordion starts -->
                <div class="panel-group" id="accordion-alt3">
                 <!-- Panel. Use "panel-XXX" class for different colors. Replace "XXX" with color. -->
                  <div class="panel" data-aos="fade-up" data-aos-delay="350"> 
                  <!-- Panel heading -->
                   <div class="panel-heading" style="background-color: #f8f9fa; border-radius: 4px;">
                    <h4 class="panel-title">
                      <a data-toggle="collapse" data-parent="#accordion-alt3" href="#collapseOne-alt3" style="color: #2c3e50; text-decoration: none; display: block; padding: 10px 15px;">
                      <i class="fa fa-angle-right" style="color: #086e96; margin-right: 10px;"></i> New Job Portal Features Released
                      </a>
                    </h4>
                   </div>
                   <div id="collapseOne-alt3" class="panel-collapse collapse in">
                    <!-- Panel body -->
                    <div class="panel-body" style="padding: 15px; border-top: 1px solid #eee;">
                      Our job portal has been updated with new search features that make finding your dream job easier than ever. Filter by skills, location, and salary to find the perfect match for your career goals.
                    </div>
                   </div>
                  </div>
                  <div class="panel" data-aos="fade-up" data-aos-delay="400">
                   <div class="panel-heading" style="background-color: #f8f9fa; border-radius: 4px; margin-top: 10px;">
                    <h4 class="panel-title">
                      <a data-toggle="collapse" data-parent="#accordion-alt3" href="#collapseTwo-alt3" style="color: #2c3e50; text-decoration: none; display: block; padding: 10px 15px;">
                      <i class="fa fa-angle-right" style="color: #086e96; margin-right: 10px;"></i> Upcoming Virtual Career Fair
                      </a>
                    </h4>
                   </div>
                   <div id="collapseTwo-alt3" class="panel-collapse collapse">
                    <div class="panel-body" style="padding: 15px; border-top: 1px solid #eee;">
                      Join our virtual career fair on June 15th and connect with top employers from tech, healthcare, and finance industries. Pre-register now to secure your spot and schedule interviews with hiring managers.
                    </div>
                   </div>
                  </div>
                </div>
                <!-- Accordion ends -->
              </div>
              
              <div class="col-md-4" data-aos="fade-up" data-aos-delay="500">
                <div class="block-heading-two">
                  <h3 style="font-size: 24px; margin-bottom: 20px; color: #2c3e50; position: relative; padding-bottom: 15px;">
                    Testimonials
                    <span style="display: block; position: absolute; width: 40px; height: 3px; background-color: #086e96; bottom: 0;"></span>
                  </h3>
                </div>  
                <!-- Testimonials Carousel -->
                <div id="testimonialCarousel" class="carousel slide" data-ride="carousel" data-interval="5000" data-aos="fade-up" data-aos-delay="550">
                  <!-- Carousel indicators -->
                  <ol class="carousel-indicators" style="bottom: -40px;">
                    <li data-target="#testimonialCarousel" data-slide-to="0" class="active" style="background-color: #086e96;"></li>
                    <li data-target="#testimonialCarousel" data-slide-to="1" style="background-color: #086e96;"></li>
                    <li data-target="#testimonialCarousel" data-slide-to="2" style="background-color: #086e96;"></li>
                  </ol>
                  
                  <!-- Carousel items -->
                  <div class="carousel-inner">
                    <div class="item active">
                      <div class="testimonial-box" style="background-color: #f8f9fa; border-radius: 8px; padding: 20px; box-shadow: 0 3px 10px rgba(0,0,0,0.1);">
                        <p style="font-style: italic; color: #555;">"This platform completely transformed my job search. Within two weeks of creating my profile, I received three interview requests and landed my dream position at a tech startup!"</p>
                        <div style="display: flex; align-items: center; margin-top: 15px;">
                          <img src="<?php echo web_root; ?>plugins/home-plugins/img/team1.jpg" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; margin-right: 15px;">
                          <div>
                            <h4 style="margin: 0; color: #2c3e50; font-size: 16px;">Sarah Johnson</h4>
                            <p style="margin: 0; color: #086e96;">Software Developer</p>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="item">
                      <div class="testimonial-box" style="background-color: #f8f9fa; border-radius: 8px; padding: 20px; box-shadow: 0 3px 10px rgba(0,0,0,0.1);">
                        <p style="font-style: italic; color: #555;">"As a hiring manager, I've been impressed with the quality of candidates I've found through this platform. The matching algorithm truly understands our needs and company culture."</p>
                        <div style="display: flex; align-items: center; margin-top: 15px;">
                          <img src="<?php echo web_root; ?>plugins/home-plugins/img/team2.jpg" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; margin-right: 15px;">
                          <div>
                            <h4 style="margin: 0; color: #2c3e50; font-size: 16px;">Michael Roberts</h4>
                            <p style="margin: 0; color: #086e96;">HR Director</p>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="item">
                      <div class="testimonial-box" style="background-color: #f8f9fa; border-radius: 8px; padding: 20px; box-shadow: 0 3px 10px rgba(0,0,0,0.1);">
                        <p style="font-style: italic; color: #555;">"After struggling to find the right fit for months, the career coaching services helped me identify what I really wanted. The insight was invaluable, and I'm now in a role I love."</p>
                        <div style="display: flex; align-items: center; margin-top: 15px;">
                          <img src="<?php echo web_root; ?>plugins/home-plugins/img/team3.jpg" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; margin-right: 15px;">
                          <div>
                            <h4 style="margin: 0; color: #2c3e50; font-size: 16px;">Steve Peters</h4>
                            <p style="margin: 0; color: #086e96;">Marketing Specialist</p>
                          </div>
                      </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Carousel controls -->
                  <a class="left carousel-control" href="#testimonialCarousel" data-slide="prev" style="background: none; width: auto; left: -15px;">
                    <span class="fa fa-angle-left" style="color: #086e96; font-size: 24px; position: absolute; top: 50%; margin-top: -10px;"></span>
                  </a>
                  <a class="right carousel-control" href="#testimonialCarousel" data-slide="next" style="background: none; width: auto; right: -15px;">
                    <span class="fa fa-angle-right" style="color: #086e96; font-size: 24px; position: absolute; top: 50%; margin-top: -10px;"></span>
                  </a>
                </div>
                <!-- End Testimonials Carousel -->
              </div>
            </div>
            <br>
            </div>
          </div>