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
	<div class="container">
		<div class="row">
			<div class="col-md-12" data-aos="fade-up">
				<div class="about-logo" style="text-align: center; margin-bottom: 50px;">
					<h3 style="font-size: 32px; margin-bottom: 25px; color: #2c3e50; position: relative; padding-bottom: 15px; display: inline-block;">
						Get In Touch With Us
						<span style="display: block; position: absolute; width: 60px; height: 3px; background-color: #086e96; bottom: 0; left: 50%; transform: translateX(-50%);"></span>
					</h3>
					<p style="max-width: 800px; margin: 0 auto; line-height: 1.8; color: #555;">Our team is here to help you find the perfect career opportunity or connect with top talent for your organization. Whether you have questions about our platform, need assistance with your job search, or want to explore partnership options, we're just a message away.</p>
				</div>  
			</div>
		</div>
		
		<div class="row" style="margin-top: 20px;">
			<div class="col-md-6" data-aos="fade-right" data-aos-delay="100">
				<div class="contact-info" style="margin-bottom: 30px;">
					<div style="display: flex; align-items: center; margin-bottom: 20px;">
						<div style="margin-right: 15px; background-color: #f1f8ff; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
							<i class="fa fa-map-marker" style="color: #086e96; font-size: 24px;"></i>
						</div>
						<div>
							<h4 style="margin: 0 0 5px; color: #2c3e50; font-size: 18px;">Our Location</h4>
							<p style="margin: 0; color: #555;">2880 Broadway, New York, NY 10025</p>
						</div>
					</div>
					
					<div style="display: flex; align-items: center; margin-bottom: 20px;">
						<div style="margin-right: 15px; background-color: #f1f8ff; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
							<i class="fa fa-phone" style="color: #086e96; font-size: 24px;"></i>
						</div>
						<div>
							<h4 style="margin: 0 0 5px; color: #2c3e50; font-size: 18px;">Call Us</h4>
							<p style="margin: 0; color: #555;">+1 (800) 123-4567</p>
						</div>
					</div>
					
					<div style="display: flex; align-items: center; margin-bottom: 30px;">
						<div style="margin-right: 15px; background-color: #f1f8ff; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
							<i class="fa fa-envelope" style="color: #086e96; font-size: 20px;"></i>
						</div>
						<div>
							<h4 style="margin: 0 0 5px; color: #2c3e50; font-size: 18px;">Email Us</h4>
							<p style="margin: 0; color: #555;">info@careerconnect.com</p>
						</div>
					</div>
				</div>
				
				<!-- Contact Form -->
				<div class="contact-form" data-aos="fade-up" data-aos-delay="200">
					<div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.08);">
						<h3 style="color: #2c3e50; margin-top: 0; margin-bottom: 20px; font-size: 24px;">Send Us a Message</h3>
						<form name="sentMessage" id="contactForm" novalidate>
							<div class="control-group" style="margin-bottom: 20px;">
								<div class="controls">
									<div style="position: relative;">
										<input type="text" class="form-control" placeholder="Full Name" id="name" required
											data-validation-required-message="Please enter your name" style="padding: 10px 15px; border-radius: 4px; border: 1px solid #ddd; width: 100%;" />
										<i class="fa fa-user" style="position: absolute; right: 15px; top: 12px; color: #086e96;"></i>
									</div>
									<p class="help-block"></p>
								</div>
							</div>
							
							<div class="control-group" style="margin-bottom: 20px;">
								<div class="controls">
									<div style="position: relative;">
										<input type="email" class="form-control" placeholder="Email" id="email" required
											data-validation-required-message="Please enter your email" style="padding: 10px 15px; border-radius: 4px; border: 1px solid #ddd; width: 100%;" />
										<i class="fa fa-envelope" style="position: absolute; right: 15px; top: 12px; color: #086e96;"></i>
									</div>
								</div>
							</div>
							
							<div class="control-group" style="margin-bottom: 20px;">
								<div class="controls">
									<div style="position: relative;">
										<textarea rows="5" class="form-control" placeholder="Message" id="message" required
											data-validation-required-message="Please enter your message" minlength="5"
											data-validation-minlength-message="Min 5 characters" maxlength="999" 
											style="padding: 10px 15px; border-radius: 4px; border: 1px solid #ddd; width: 100%; resize: none;"></textarea>
										<i class="fa fa-comment" style="position: absolute; right: 15px; top: 12px; color: #086e96;"></i>
									</div>
								</div>
							</div>
							
							<div id="success"></div> <!-- For success/fail messages -->
							<button type="submit" class="btn btn-primary" style="background-color: #086e96; border: none; padding: 10px 25px; border-radius: 4px; transition: all 0.3s ease; float: right;">
								<i class="fa fa-paper-plane" style="margin-right: 8px;"></i> Send Message
							</button>
							<div style="clear: both;"></div>
						</form>
					</div>
				</div>
			</div>
			
			<div class="col-md-6" data-aos="fade-left" data-aos-delay="100">
				<div style="border-radius: 8px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); height: 450px; margin-bottom: 20px;">
				<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2640.557873948367!2d-0.112652547752463!3d5.5935500481107105!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xfdf85c37ff200f9%3A0x2e12a7fa5631a0f6!2sTeshie%20Tsui%20Bleoo%20coldstore!5e0!3m2!1sen!2sgh!4v1746294826633!5m2!1sen!2sgh" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>				</div>
				
				<div class="business-hours" data-aos="fade-up" data-aos-delay="300">
					<div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.08);">
						<h4 style="color: #2c3e50; margin-top: 0; margin-bottom: 15px; font-size: 18px;">
							<i class="fa fa-clock-o" style="color: #086e96; margin-right: 8px;"></i> Business Hours
						</h4>
						<div class="row">
							<div class="col-md-6">
								<ul style="list-style: none; padding-left: 0; margin-bottom: 0;">
									<li style="padding: 6px 0; border-bottom: 1px dashed #eee; display: flex; justify-content: space-between;">
										<span>Monday - Friday:</span>
										<span>9:00 AM - 6:00 PM</span>
									</li>
									<li style="padding: 6px 0; border-bottom: 1px dashed #eee; display: flex; justify-content: space-between;">
										<span>Saturday:</span>
										<span>10:00 AM - 2:00 PM</span>
									</li>
									<li style="padding: 6px 0; display: flex; justify-content: space-between;">
										<span>Sunday:</span>
										<span>Closed</span>
									</li>
								</ul>
							</div>
							<div class="col-md-6">
								<div style="background-color: #f8f9fa; padding: 15px; border-radius: 4px; border-left: 3px solid #086e96;">
									<p style="margin: 0; font-size: 14px;">Our support team is available 24/7 via email for urgent inquiries.</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Form input effect
  const formInputs = document.querySelectorAll('.form-control');
  
  formInputs.forEach(input => {
    input.addEventListener('focus', function() {
      this.parentNode.style.boxShadow = '0 0 0 2px rgba(8, 110, 150, 0.2)';
    });
    
    input.addEventListener('blur', function() {
      this.parentNode.style.boxShadow = 'none';
    });
  });
  
  // Button hover effect
  const submitBtn = document.querySelector('.btn-primary');
  
  submitBtn.addEventListener('mouseenter', function() {
    this.style.backgroundColor = '#075d7e';
  });
  
  submitBtn.addEventListener('mouseleave', function() {
    this.style.backgroundColor = '#086e96';
  });
});
</script>
 