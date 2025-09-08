<!-- Required Scripts - Move these to the top -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<!-- CSS files -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 10px; overflow: hidden; box-shadow: 0 15px 30px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background: linear-gradient(135deg,rgb(4, 91, 150),rgb(4, 91, 150)); border-bottom: none;">
                <button class="close" data-dismiss="modal" type="button" style="color: white; opacity: 0.8;">×</button>
                <h4 class="modal-title" id="myModalLabel" style="font-weight: 600;"><i class="fa fa-user fs-5" style="margin-right: 10px; color:white;"></i><span style="color:white;">Account Login</span></h4>
            </div>

            <div class="modal-body" style="padding: 0;">
                <div class="row" style="margin: 0;">
                    <!-- Left Side - Branding/Info Panel with Background Image -->
                    <div class="col-md-5 login-left-panel" style="position: relative; min-height: 350px; display: flex; flex-direction: column; justify-content: space-between;">
                        <div class="overlay"></div>
                        <div class="content-wrapper" style="position: relative; z-index: 2; padding: 30px;">
                            <div>
                                <div class="text-center">
                                    <h3 style="margin-bottom: 20px; font-weight: 600; color:white;"><i class="fa fa-briefcase" style="margin-right: 10px; color:white;"></i>Welcome Back</h3>
                                    <p style="margin-bottom: 20px; color: #ecf0f1;">Find your dream job with our platform</p>
                                </div>
                                <div style="margin-top: 30px;">
                                    <h4 style="color: white; font-weight: 600; margin-bottom: 15px;">Why join us?</h4>
                                    <ul style="list-style-type: none; padding-left: 5px; color:white;">
                                        <li style="margin-bottom: 10px;"><i class="fa fa-check-circle" style="margin-right: 10px; color: #2ecc71;"></i> Connect with top employers</li>
                                        <li style="margin-bottom: 10px;"><i class="fa fa-check-circle" style="margin-right: 10px; color: #2ecc71;"></i> Discover opportunities</li>
                                        <li style="margin-bottom: 10px;"><i class="fa fa-check-circle" style="margin-right: 10px; color: #2ecc71;"></i> Professional profile</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="text-center" style="margin-top: 20px;">
                                <p style="color: #bdc3c7;">Don't have an account?</p>
                                <a href="<?php echo web_root; ?>index.php?q=register" class="btn btn-outline-light" style="border: 1px solid #3498db; color: #fff; background-color: transparent; border-radius: 20px; padding: 8px 20px; transition: all 0.3s;">Register Now</a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Side - Login Form -->
                    <div class="col-md-7" style="padding: 30px; background-color: #fff;">
                        <div id="loginerrormessage"></div>
                        <div class="login-box" style="width: 100%;">
                            <div class="login-box-body" style="border: none; padding: 10px 0; min-height: 300px;">
                                <h4 style="margin-bottom: 25px; font-weight: 600; color: #2c3e50; text-align: center;">Sign in to your account</h4>
                                <form action="" method="post">
                                    <div class="form-group" style="margin-bottom: 25px;">
                                        <label for="user_email" style="font-weight: 500; color: #7f8c8d; margin-bottom: 8px;">Username</label>
                                        <div class="input-group">
                                            <span class="input-group-addon" style="background-color: #f9f9f9; border-right: none;"><i class="fa fa-user" style="color: #3498db;"></i></span>
                                            <input type="text" class="form-control" placeholder="Enter your username" name="user_email" id="user_email" style="height: 42px; border-left: none; box-shadow: none;">
                                        </div>
                                    </div>
                                    <div class="form-group" style="margin-bottom: 25px;">
                                        <label for="user_pass" style="font-weight: 500; color: #7f8c8d; margin-bottom: 8px;">Password</label>
                                        <div class="input-group">
                                            <span class="input-group-addon" style="background-color: #f9f9f9; border-right: none;"><i class="fa fa-lock" style="color: #3498db;"></i></span>
                                            <input type="password" class="form-control" placeholder="Enter your password" name="user_pass" id="user_pass" style="height: 42px; border-left: none; box-shadow: none;">
                                        </div>
                                    </div>
                                    <div class="row" style="margin-bottom: 20px;">
                                        <div class="col-xs-6">
                                            <div class="checkbox">
                                                <label style="color: #7f8c8d;">
                                                    <input type="checkbox"> Remember Me
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-xs-6 text-right">
                                            <a href="#" style="color: #3498db;">Forgot Password?</a>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button type="button" class="btn btn-primary btn-block" name="btnlogin" id="btnlogin" style="background: linear-gradient(135deg, #3498db, #2980b9); border: none; padding: 10px; border-radius: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">Login</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<style>
.login-left-panel {
    background-image: url('https://images.unsplash.com/photo-1521737711867-e3b97375f902?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=774&q=80');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

.login-left-panel .overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(4, 91, 150, 0.9), rgba(13, 107, 170, 0.9));
    z-index: 1;
}

.login-left-panel .content-wrapper {
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

/* Animation for the login form */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.login-box {
    animation: fadeInUp 0.5s ease-out;
}

/* Hover effects */
.btn-outline-light:hover {
    background-color: rgba(255, 255, 255, 0.1);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.form-control:focus {
    border-color: #3498db;
    box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .login-left-panel {
        min-height: 250px;
    }
    
    .login-left-panel .content-wrapper {
        padding: 20px;
    }
}

/* Additional styles for form focus */
.form-group.focused label {
    color: #3498db !important;
}

.form-group.focused .input-group-addon {
    background-color: #e8f4fc;
    border-color: #3498db;
}

.form-group.focused .form-control {
    border-color: #3498db;
}
</style>

<script>
$(document).ready(function() {
    $('#btnlogin, #btnlogin-footer').click(function() {
        var username = $('#user_email').val();
        var pass = $('#user_pass').val();
        
        if(!username || !pass) {
            $('#loginerrormessage').html('<div class="alert alert-warning" style="border-radius: 4px; background-color: #fcf8e3; border-color: #faebcc; color: #8a6d3b;"><i class="fa fa-exclamation-triangle"></i> Please enter both username and password.</div>');
            return false;
        }
        
        $('#loginerrormessage').html('<div class="text-center" style="padding: 10px;"><i class="fa fa-spinner fa-spin" style="color: #3498db; font-size: 20px;"></i> Authenticating...</div>');
        
        $.ajax({
            type: "POST",
            url: "process.php?action=login",
            dataType: "text",
            data: {USERNAME: username, PASS: pass, ajax: true},
            success: function(data) {
                if (data == "success") {
                    $('#loginerrormessage').html('<div class="alert alert-success" style="border-radius: 4px; background-color: #dff0d8; border-color: #d6e9c6; color: #3c763d;"><i class="fa fa-check-circle"></i> Login successful. Redirecting...</div>');
                    setTimeout(function() {
                        window.location = "applicant/";
                    }, 1000);
                } else {
                    $('#loginerrormessage').html('<div class="alert alert-danger" style="border-radius: 4px; background-color: #f2dede; border-color: #ebccd1; color: #a94442;"><i class="fa fa-times-circle"></i> ' + data + '</div>');
                }
            },
            error: function() {
                $('#loginerrormessage').html('<div class="alert alert-danger" style="border-radius: 4px; background-color: #f2dede; border-color: #ebccd1; color: #a94442;"><i class="fa fa-times-circle"></i> Connection error. Please try again.</div>');
            }
        });
    });
    
    // Add animation effects when the modal opens
    $('#myModal').on('show.bs.modal', function() {
        setTimeout(function() {
            $('.modal-content').addClass('animated fadeIn');
        }, 100);
    });
    
    // Form field animation
    $('.form-control').focus(function() {
        $(this).closest('.form-group').addClass('focused');
    });
    
    $('.form-control').blur(function() {
        if ($(this).val() === '') {
            $(this).closest('.form-group').removeClass('focused');
        }
    });
    
    // Enable pressing Enter key to submit form
    $('#user_pass').keypress(function(e) {
        if(e.which == 13) {
            $('#btnlogin').click();
        }
    });
});
</script>