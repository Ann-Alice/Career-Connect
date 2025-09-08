<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php
                // $query = "SELECT * FROM `tbltitle` WHERE TItleID=1";
                // $res = mysql_query($query) or die(mysql_error());
                // $viewTitle = mysql_fetch_assoc($res);
                // echo $viewTitle['Title'];
            ?>
        </title>
       <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.5 -->
        <link rel="stylesheet" href="<?php echo web_root;?>bootstrap/css/bootstrap.min.css">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="<?php echo web_root;?>plugins/font-awesome/css/font-awesome.min.css">

        <!-- <link rel="stylesheet" href="<?php echo web_root;?>plugins/datatables/dataTables.bootstrap.css">  -->
        <!-- <link rel="stylesheet" href="<?php echo web_root;?>plugins/datatables/jquery.dataTables.min.css">  -->

        <!-- Ionicons -->
        <!-- <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css"> -->
        <!-- Theme style -->
        <link rel="stylesheet" href="<?php echo web_root;?>dist/css/AdminLTE.min.css">
        <!-- AdminLTE Skins. Choose a skin from the css/skins
             folder instead of downloading all of them to reduce the load. -->
        <link rel="stylesheet" href="<?php echo web_root;?>dist/css/skins/_all-skins.min.css">
        <!-- iCheck -->
        <link rel="stylesheet" href="<?php echo web_root;?>plugins/iCheck/flat/blue.css">
        <!-- Morris chart -->
        <link rel="stylesheet" href="<?php echo web_root;?>plugins/morris/morris.css">
        <!-- jvectormap -->
        <link rel="stylesheet" href="<?php echo web_root;?>plugins/jvectormap/jquery-jvectormap-1.2.2.css">
        <!-- Date Picker -->
        <link href="<?php echo web_root; ?>plugins/datepicker/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">

        <link rel="stylesheet" href="<?php echo web_root;?>plugins/datatables/jquery.dataTables.min.css">  

        <!-- <link rel="stylesheet" href="<?php echo web_root;?>plugins/datepicker/datepicker3.css"> -->
        <!-- Daterange picker -->
        <!-- <link rel="stylesheet" href="<?php echo web_root;?>plugins/daterangepicker/daterangepicker-bs3.css"> -->
        <!-- bootstrap wysihtml5 - text editor -->
        <link rel="stylesheet" href="<?php echo web_root;?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
 
    </head>

 <body class="hold-transition skin-blue fixed sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <!-- Logo -->
    <a href="<?php echo web_root;?>/admin/" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>Career Connect</b></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>Career Connect</b></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
 
          <?php
              $user = New User();
              $singleuser = null;
              $admin_userid = isset($_SESSION['ADMIN_USERID']) ? $_SESSION['ADMIN_USERID'] : null;
              $admin_fullname = isset($_SESSION['ADMIN_FULLNAME']) ? $_SESSION['ADMIN_FULLNAME'] : 'Admin User';
              $admin_piclocation = isset($_SESSION['ADMIN_PICLOCATION']) ? $_SESSION['ADMIN_PICLOCATION'] : 'avatar.jpg';
              
              if ($admin_userid) {
                  $singleuser = $user->single_user($admin_userid);
              }

          ?>
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu" style="padding-right: 15px;"  >
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?php echo web_root.'admin/user/'. ($singleuser ? $singleuser->PICLOCATION : $admin_piclocation);?>" class="user-image" alt="User Image">
              <span class="hidden-xs"><?php echo $singleuser ? $singleuser->FULLNAME : $admin_fullname; ?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header"> 
                <img data-target="#menuModal"  data-toggle="modal"  src="<?php echo web_root.'admin/user/'. ($singleuser ? $singleuser->PICLOCATION : $admin_piclocation);?>" class="img-circle" alt="User Image" />  
              </li> 
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?php echo web_root.'admin/user/index.php?view=view&id='.($admin_userid ?: '1');?>" class="btn btn-default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                  <a href="<?php echo web_root ;?>admin/logout.php" class="btn btn-default btn-flat">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button -->
          
        </ul>
      </div>
    </nav>
  </header>



 <div class="modal fade" id="menuModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button class="close" data-dismiss="modal" type=
                                    "button">x</button>

                                    <h4 class="modal-title" id="myModalLabel">Image.</h4>
                                </div>

                                <form action="<?php echo web_root; ?>admin/user/controller.php?action=photos" enctype="multipart/form-data" method=
                                "post">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <div class="rows">
                                                <div class="col-md-12">
                                                    <div class="rows">
                                                        <div class="col-md-8"> 
                                                            <input class="mealid" type="hidden" name="mealid" id="mealid" value="">
                                                              <input name="MAX_FILE_SIZE" type="hidden" 
                                                              value="1000000"> 
                                                              <input id="photo" name="photo" type="file">
                                                        </div>

                                                        <div class="col-md-4"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button class="btn btn-default" data-dismiss="modal" type=
                                        "button">Close</button> <button class="btn btn-primary"
                                        name="savephoto" type="submit">Upload Photo</button>
                                    </div>
                                </form>
                            </div><!-- /.modal-content-->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->  



  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <?php include(__DIR__ . '/../includes/menu.php'); ?>
    </section>
  </aside>
   <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

  <section class="content-header">
      <h1>
        <?php echo isset($title) ? $title : ''; ?>
        <!-- <small>it all starts here</small> -->
      </h1>
      <ol class="breadcrumb">
        <?php

          if ($title!='Home') {
            # code... 
            $active_title = '';
            if (isset($_GET['view'])) {
              # code...
              $active_title = '<li class='.$active_title.'><a href="index.php">'.$title.'</a></li>';
            }else{
              $active_title = '<li class='.$active_title.'>'.$title.'</li>';
            }
            echo '<li><a href='.web_root.'admin/><i class="fa fa-dashboard"></i> Home</a></li>';
            echo  $active_title;
            echo  isset($_GET['view']) ? '<li class="active">'.$_GET['view'].'</li>' : '';
          } 


        ?>
      </ol>
    </section>
         <section class="content">

          <div class="row">
            <div class="col-xs-12">
              <div class="box">
                <div class="box-body">

              <?php 
               check_message();
               require_once $content; 
               ?> 
             </div>
             </div>
           </div>
         </div>
         </section>
 </div>
  <!-- /.content-wrapper -->


  <footer class="main-footer">
    <strong>Copyright &copy; 2025 <a href="#">Connect GH</a>.</strong> All rights
    reserved.
  </footer>

  

    </body>
      <script type="text/javascript" src="<?php echo web_root; ?>plugins/jQuery/jQuery-2.1.4.min.js"> </script>
      <script type="text/javascript" src="<?php echo web_root; ?>bootstrap/js/bootstrap.min.js" ></script>
      <script src="<?php echo web_root;?>dist/js/app.min.js"></script> 

      <script type="text/javascript" src="<?php echo web_root; ?>plugins/datepicker/bootstrap-datepicker.js" ></script> 
      <script type="text/javascript" src="<?php echo web_root; ?>plugins/datepicker/bootstrap-datetimepicker.js" charset="UTF-8"></script>
      <script type="text/javascript" src="<?php echo web_root; ?>plugins/datepicker/locales/bootstrap-datetimepicker.uk.js" charset="UTF-8"></script>

      <script src="<?php echo web_root; ?>plugins/datatables/jquery.dataTables.min.js"></script>
      <script type="text/javascript" src="<?php echo web_root; ?>plugins/datatables/dataTables.bootstrap.min.js" ></script> 

      <script src="<?php echo web_root; ?>plugins/slimScroll/jquery.slimscroll.min.js"></script>

      <script type="text/javascript" language="javascript" src="<?php echo web_root; ?>plugins/input-mask/jquery.inputmask.js"></script> 
      <script type="text/javascript" language="javascript" src="<?php echo web_root; ?>plugins/input-mask/jquery.inputmask.date.extensions.js"></script> 
      <script type="text/javascript" language="javascript" src="<?php echo web_root; ?>plugins/input-mask/jquery.inputmask.extensions.js"></script> 

   <!--      <script src="<?php echo web_root; ?>admin/js/jquery.dataTables.min.js"></script>
<script src="<?php echo web_root; ?>admin/js/dataTables.bootstrap.min.js"></script>
 <script type="text/javascript" src="<?php echo web_root; ?>js/jquery-1.10.2.js"></script>       
        <script type="text/javascript" src="<?php echo web_root; ?>js/jquery.mixitup.min.js" ></script>
        <script type="text/javascript" src="<?php echo web_root; ?>js/main.js" ></script> 
        <script type="text/javascript" src="<?php echo web_root; ?>js/janobe.js" ></script> 
        <script src="<?php echo web_root; ?>admin/js/ekko-lightbox.js"></script>
        <script src="<?php echo web_root; ?>admin/js/lightboxfunction.js"></script> 
  -->
<!-- jQuery 2.1.4 --> 

<script>
  $(function () {
    if ($("#dash-table").length) {
      $("#dash-table").DataTable();
    }
    if ($('#dash-table2').length) {
      $('#dash-table2').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false
      });
    }
  });

$('input[data-mask]').each(function() {
  var input = $(this);
  input.setMask(input.data('mask'));
});


$('#BIRTHDATE').inputmask({
  mask: "2/1/y",
  placeholder: "mm/dd/yyyy",
  alias: "date",
  hourFormat: "24"
});
$('#HIREDDATE').inputmask({
  mask: "2/1/y",
  placeholder: "mm/dd/yyyy",
  alias: "date",
  hourFormat: "24"
});

$('.date_picker').datetimepicker({
  format: 'mm/dd/yyyy',
  startDate : '01/01/1950', 
  language:  'en',
  weekStart: 1,
  todayBtn:  1,
  autoclose: 1,
  todayHighlight: 1, 
  startView: 2,
  minView: 2,
  forceParse: 0 

});


</script>
</html>
 