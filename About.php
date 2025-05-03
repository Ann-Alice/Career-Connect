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
	<section class="section-padding">
		<div class="container">
			<div class="row showcase-section">
				<div class="col-md-6" data-aos="fade-right" data-aos-delay="100">
					<img src="<?php echo web_root; ?>plugins/home-plugins/img/Careerconnect.png" alt="showcase image" class="img-responsive" style="border-radius: 8px; box-shadow: 0 10px 20px rgba(0,0,0,0.15);">
				</div>
				<div class="col-md-6" data-aos="fade-left" data-aos-delay="300">
					<div class="about-text">
						<h3 style="font-size: 28px; margin-bottom: 25px; color: #2c3e50; position: relative; padding-bottom: 15px;">
							About Our Platform
							<span style="display: block; position: absolute; width: 50px; height: 3px; background-color: #086e96; bottom: 0;"></span>
						</h3>
						<p style="line-height: 1.8;">Our innovative job platform connects talented professionals with forward-thinking companies across industries. We leverage cutting-edge technology to create meaningful matches that benefit both job seekers and employers.</p>
						<p style="line-height: 1.8;">Our mission is to transform the hiring process by focusing on skills, cultural fit, and career development opportunities. We believe that the right job match can transform lives and help businesses thrive.</p>
					</div>
				</div>
			</div>
		</div>
	</section>
	<div class="container">
		<div class="about">
			<div class="row">
				<div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
					<!-- Heading and para -->
					<div class="block-heading-two">
						<h3 style="font-size: 24px; margin-bottom: 20px; color: #2c3e50; position: relative; padding-bottom: 15px;">
							Why Choose Us?
							<span style="display: block; position: absolute; width: 40px; height: 3px; background-color: #086e96; bottom: 0;"></span>
						</h3>
					</div>
					<div class="feature-item" data-aos="fade-up" data-aos-delay="150" style="margin-bottom: 20px;">
						<div style="display: flex; align-items: flex-start;">
							<div style="margin-right: 15px; background-color: #f1f8ff; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
								<i class="fa fa-check" style="color: #086e96; font-size: 18px;"></i>
							</div>
							<div>
								<h4 style="margin-top: 0; color: #2c3e50; font-size: 18px;">Advanced Matching</h4>
								<p style="line-height: 1.6;">Our proprietary algorithm connects you with opportunities that align with your skills, experience, and career goals.</p>
							</div>
						</div>
					</div>
					<div class="feature-item" data-aos="fade-up" data-aos-delay="200" style="margin-bottom: 20px;">
						<div style="display: flex; align-items: flex-start;">
							<div style="margin-right: 15px; background-color: #f1f8ff; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
								<i class="fa fa-shield" style="color: #086e96; font-size: 18px;"></i>
							</div>
							<div>
								<h4 style="margin-top: 0; color: #2c3e50; font-size: 18px;">Privacy First</h4>
								<p style="line-height: 1.6;">Your information is secure with our platform, giving you control over what employers see and when they see it.</p>
							</div>
						</div>
					</div>
					<div class="feature-item" data-aos="fade-up" data-aos-delay="250" style="margin-bottom: 20px;">
						<div style="display: flex; align-items: flex-start;">
							<div style="margin-right: 15px; background-color: #f1f8ff; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
								<i class="fa fa-rocket" style="color: #086e96; font-size: 18px;"></i>
							</div>
							<div>
								<h4 style="margin-top: 0; color: #2c3e50; font-size: 18px;">Career Growth</h4>
								<p style="line-height: 1.6;">Access exclusive resources, coaching, and development tools to help you advance in your chosen field.</p>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
					<div class="block-heading-two">
						<h3 style="font-size: 24px; margin-bottom: 20px; color: #2c3e50; position: relative; padding-bottom: 15px;">
							Our Solution
							<span style="display: block; position: absolute; width: 40px; height: 3px; background-color: #086e96; bottom: 0;"></span>
						</h3>
					</div>		
					<!-- Accordion starts -->
					<div class="panel-group" id="accordion-alt3">
						<div class="panel" data-aos="fade-up" data-aos-delay="350"> 
							<div class="panel-heading" style="background-color: #f8f9fa; border-radius: 4px;">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion-alt3" href="#collapseOne-alt3" style="color: #2c3e50; text-decoration: none; display: block; padding: 10px 15px;">
										<i class="fa fa-angle-right" style="color: #086e96; margin-right: 10px;"></i> For Job Seekers
									</a>
								</h4>
							</div>
							<div id="collapseOne-alt3" class="panel-collapse collapse in">
								<div class="panel-body" style="padding: 15px; border-top: 1px solid #eee; line-height: 1.6;">
									Our platform helps you showcase your skills and experience to the right employers. Create a customized profile, set job preferences, and receive personalized job recommendations that match your career goals.
								</div>
							</div>
						</div>
						<div class="panel" data-aos="fade-up" data-aos-delay="400">
							<div class="panel-heading" style="background-color: #f8f9fa; border-radius: 4px; margin-top: 10px;">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion-alt3" href="#collapseTwo-alt3" style="color: #2c3e50; text-decoration: none; display: block; padding: 10px 15px;">
										<i class="fa fa-angle-right" style="color: #086e96; margin-right: 10px;"></i> For Employers
									</a>
								</h4>
							</div>
							<div id="collapseTwo-alt3" class="panel-collapse collapse">
								<div class="panel-body" style="padding: 15px; border-top: 1px solid #eee; line-height: 1.6;">
									Find qualified candidates faster with our advanced matching system. Post jobs, search our talent database, and use our AI-powered tools to identify the best candidates for your organization's culture and requirements.
								</div>
							</div>
						</div>
						<div class="panel" data-aos="fade-up" data-aos-delay="450">
							<div class="panel-heading" style="background-color: #f8f9fa; border-radius: 4px; margin-top: 10px;">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion-alt3" href="#collapseThree-alt3" style="color: #2c3e50; text-decoration: none; display: block; padding: 10px 15px;">
										<i class="fa fa-angle-right" style="color: #086e96; margin-right: 10px;"></i> Career Development
									</a>
								</h4>
							</div>
							<div id="collapseThree-alt3" class="panel-collapse collapse">
								<div class="panel-body" style="padding: 15px; border-top: 1px solid #eee; line-height: 1.6;">
									Access personalized learning paths, skill assessments, and career coaching to help you grow professionally. Our resources are designed to help you advance in your current role or successfully transition to a new career.
								</div>
							</div>
						</div>
					</div>
					<!-- Accordion ends -->
				</div>
				
				<div class="col-md-4" data-aos="fade-up" data-aos-delay="500">
					<div class="block-heading-two">
						<h3 style="font-size: 24px; margin-bottom: 20px; color: #2c3e50; position: relative; padding-bottom: 15px;">
							Our Expertise
							<span style="display: block; position: absolute; width: 40px; height: 3px; background-color: #086e96; bottom: 0;"></span>
						</h3>
					</div>								
					<div class="skill-item" data-aos="fade-up" data-aos-delay="550" style="margin-bottom: 25px;">
						<div style="display: flex; align-items: center; margin-bottom: 10px; justify-content: space-between;">
							<h6 style="margin: 0; font-weight: 600; color: #2c3e50;">Talent Matching</h6>
							<span style="color: #086e96; font-weight: 600;">95%</span>
						</div>
						<div class="progress" style="height: 8px; margin-bottom: 0; border-radius: 4px; background-color: #f1f1f1; box-shadow: none;">
							<div class="progress-bar" role="progressbar" aria-valuenow="95" aria-valuemin="0" aria-valuemax="100" style="width: 95%; background-color: #086e96; border-radius: 4px;">
							</div>
						</div>
					</div>
					<div class="skill-item" data-aos="fade-up" data-aos-delay="600" style="margin-bottom: 25px;">
						<div style="display: flex; align-items: center; margin-bottom: 10px; justify-content: space-between;">
							<h6 style="margin: 0; font-weight: 600; color: #2c3e50;">Career Coaching</h6>
							<span style="color: #27ae60; font-weight: 600;">85%</span>
						</div>
						<div class="progress" style="height: 8px; margin-bottom: 0; border-radius: 4px; background-color: #f1f1f1; box-shadow: none;">
							<div class="progress-bar" role="progressbar" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100" style="width: 85%; background-color: #27ae60; border-radius: 4px;">
							</div>
						</div>
					</div>
					<div class="skill-item" data-aos="fade-up" data-aos-delay="650" style="margin-bottom: 25px;">
						<div style="display: flex; align-items: center; margin-bottom: 10px; justify-content: space-between;">
							<h6 style="margin: 0; font-weight: 600; color: #2c3e50;">Employer Solutions</h6>
							<span style="color: #f39c12; font-weight: 600;">90%</span>
						</div>
						<div class="progress" style="height: 8px; margin-bottom: 0; border-radius: 4px; background-color: #f1f1f1; box-shadow: none;">
							<div class="progress-bar" role="progressbar" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100" style="width: 90%; background-color: #f39c12; border-radius: 4px;">
							</div>
						</div>
					</div>
					<div class="skill-item" data-aos="fade-up" data-aos-delay="700" style="margin-bottom: 25px;">
						<div style="display: flex; align-items: center; margin-bottom: 10px; justify-content: space-between;">
							<h6 style="margin: 0; font-weight: 600; color: #2c3e50;">Industry Knowledge</h6>
							<span style="color: #8e44ad; font-weight: 600;">88%</span>
						</div>
						<div class="progress" style="height: 8px; margin-bottom: 0; border-radius: 4px; background-color: #f1f1f1; box-shadow: none;">
							<div class="progress-bar" role="progressbar" aria-valuenow="88" aria-valuemin="0" aria-valuemax="100" style="width: 88%; background-color: #8e44ad; border-radius: 4px;">
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<br>
			<!-- Our Team starts -->
			<div class="block-heading-six" data-aos="fade-up">
				<h4 class="bg-color" style="font-size: 28px; margin-bottom: 40px; color: #2c3e50; position: relative; padding-bottom: 15px; text-align: center; background: none;">
					Our Team
					<span style="display: block; position: absolute; width: 60px; height: 3px; background-color: #086e96; bottom: 0; left: 50%; transform: translateX(-50%);"></span>
				</h4>
			</div>
			
			<div class="team-six">
				<div class="row">
					<div class="col-md-3 col-sm-6" data-aos="fade-up" data-aos-delay="100">
						<div class="team-member" style="background-color: white; border-radius: 8px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); transition: all 0.3s ease;">
							<img class="img-responsive" src="img/team1.jpg" alt="" style="width: 100%;">
							<div style="padding: 20px; text-align: center;">
								<h4 style="margin-top: 0; margin-bottom: 5px; color: #2c3e50;">John Doe</h4>
								<span class="deg" style="color: #086e96; font-weight: 500;">Chief Executive Officer</span>
								<div class="team-social" style="margin-top: 15px;">
									<a href="#" style="color: #3b5998; margin: 0 5px; font-size: 16px;"><i class="fa fa-facebook"></i></a>
									<a href="#" style="color: #1da1f2; margin: 0 5px; font-size: 16px;"><i class="fa fa-twitter"></i></a>
									<a href="#" style="color: #0077b5; margin: 0 5px; font-size: 16px;"><i class="fa fa-linkedin"></i></a>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-3 col-sm-6" data-aos="fade-up" data-aos-delay="200">
						<div class="team-member" style="background-color: white; border-radius: 8px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); transition: all 0.3s ease;">
							<img class="img-responsive" src="img/team2.jpg" alt="" style="width: 100%;">
							<div style="padding: 20px; text-align: center;">
								<h4 style="margin-top: 0; margin-bottom: 5px; color: #2c3e50;">Jennifer Smith</h4>
								<span class="deg" style="color: #086e96; font-weight: 500;">Head of Technology</span>
								<div class="team-social" style="margin-top: 15px;">
									<a href="#" style="color: #3b5998; margin: 0 5px; font-size: 16px;"><i class="fa fa-facebook"></i></a>
									<a href="#" style="color: #1da1f2; margin: 0 5px; font-size: 16px;"><i class="fa fa-twitter"></i></a>
									<a href="#" style="color: #0077b5; margin: 0 5px; font-size: 16px;"><i class="fa fa-linkedin"></i></a>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-3 col-sm-6" data-aos="fade-up" data-aos-delay="300">
						<div class="team-member" style="background-color: white; border-radius: 8px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); transition: all 0.3s ease;">
							<img class="img-responsive" src="img/team3.jpg" alt="" style="width: 100%;">
							<div style="padding: 20px; text-align: center;">
								<h4 style="margin-top: 0; margin-bottom: 5px; color: #2c3e50;">Christian Watson</h4>
								<span class="deg" style="color: #086e96; font-weight: 500;">VP of Operations</span>
								<div class="team-social" style="margin-top: 15px;">
									<a href="#" style="color: #3b5998; margin: 0 5px; font-size: 16px;"><i class="fa fa-facebook"></i></a>
									<a href="#" style="color: #1da1f2; margin: 0 5px; font-size: 16px;"><i class="fa fa-twitter"></i></a>
									<a href="#" style="color: #0077b5; margin: 0 5px; font-size: 16px;"><i class="fa fa-linkedin"></i></a>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-3 col-sm-6" data-aos="fade-up" data-aos-delay="400">
						<div class="team-member" style="background-color: white; border-radius: 8px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); transition: all 0.3s ease;">
							<img class="img-responsive" src="img/team4.jpg" alt="" style="width: 100%;">
							<div style="padding: 20px; text-align: center;">
								<h4 style="margin-top: 0; margin-bottom: 5px; color: #2c3e50;">Katherine Lee</h4>
								<span class="deg" style="color: #086e96; font-weight: 500;">Head of Marketing</span>
								<div class="team-social" style="margin-top: 15px;">
									<a href="#" style="color: #3b5998; margin: 0 5px; font-size: 16px;"><i class="fa fa-facebook"></i></a>
									<a href="#" style="color: #1da1f2; margin: 0 5px; font-size: 16px;"><i class="fa fa-twitter"></i></a>
									<a href="#" style="color: #0077b5; margin: 0 5px; font-size: 16px;"><i class="fa fa-linkedin"></i></a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- Our team ends -->
		</div>
	</div>
</section> 

<script>
// Add hover effect for team members
document.addEventListener('DOMContentLoaded', function() {
  const teamMembers = document.querySelectorAll('.team-member');
  
  teamMembers.forEach(member => {
    member.addEventListener('mouseenter', function() {
      this.style.transform = 'translateY(-10px)';
      this.style.boxShadow = '0 15px 30px rgba(0,0,0,0.15)';
    });
				
    member.addEventListener('mouseleave', function() {
      this.style.transform = 'translateY(0)';
      this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.1)';
    });
  });
});
</script> 