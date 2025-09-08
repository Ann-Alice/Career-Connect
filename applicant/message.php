  <style type="text/css">
    .mailbox-controls .btn {
      padding: 3px 8px;
      margin: 0px 2px;
    }
    .message-card {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      border-left: 4px solid #3c8dbc;
      margin-bottom: 15px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
      position: relative;
      overflow: hidden;
    }
    .message-card:hover {
      transform: translateX(5px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .message-card.unread {
      background-color: #f8f9fa;
      border-left-color: #28a745;
    }
    .message-card.unread::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 4px;
      height: 100%;
      background: #28a745;
      animation: pulse 2s infinite;
    }
    @keyframes pulse {
      0% { opacity: 1; }
      50% { opacity: 0.5; }
      100% { opacity: 1; }
    }
    .message-card .company-name {
      color: #2c3e50;
      font-weight: 600;
      font-size: 1.1em;
      margin-bottom: 5px;
    }
    .message-card .company-name a {
      color: inherit;
      text-decoration: none;
    }
    .message-card .company-name a:hover {
      color: #3c8dbc;
    }
    .message-card .message-date {
      color: #6c757d;
      font-size: 0.85em;
    }
    .message-card .message-status {
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.85em;
      font-weight: 500;
      display: inline-block;
      margin-bottom: 8px;
    }
    .message-card .status-pending {
      background-color: #fff3cd;
      color: #856404;
      border: 1px solid #ffeeba;
    }
    .message-card .status-approved {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    .message-card .status-rejected {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
    .message-card .status-interview {
      background-color: #cce5ff;
      color: #004085;
      border: 1px solid #b8daff;
    }
    .search-box {
      position: relative;
      margin-bottom: 25px;
    }
    .search-box input {
      padding: 12px 20px 12px 45px;
      border-radius: 25px;
      border: 2px solid #e9ecef;
      width: 100%;
      transition: all 0.3s ease;
      font-size: 1em;
    }
    .search-box input:focus {
      border-color: #3c8dbc;
      box-shadow: 0 0 0 0.2rem rgba(60, 141, 188, 0.25);
      outline: none;
    }
    .search-box i {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #6c757d;
      font-size: 1.2em;
    }
    .message-content {
      color: #495057;
      line-height: 1.5;
      margin: 8px 0;
    }
    .message-preview {
      color: #6c757d;
      font-size: 0.9em;
      margin-top: 5px;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    .empty-state {
      text-align: center;
      padding: 40px 20px;
      background: #f8f9fa;
      border-radius: 8px;
      margin-top: 20px;
    }
    .empty-state i {
      font-size: 4em;
      color: #dee2e6;
      margin-bottom: 20px;
    }
    .empty-state h4 {
      color: #6c757d;
      margin-bottom: 10px;
    }
    .empty-state p {
      color: #adb5bd;
    }
    .message-filters {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }
    .filter-btn {
      padding: 8px 16px;
      border: 1px solid #dee2e6;
      border-radius: 20px;
      background: #f8f9fa;
      color: #6c757d;
      font-size: 0.9em;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .filter-btn:hover {
      background: #e9ecef;
      color: #495057;
    }
    .filter-btn.active {
      background: #3c8dbc;
      color: white;
      border-color: #3c8dbc;
    }
    .filter-btn i {
      font-size: 0.9em;
    }
    @media (max-width: 768px) {
      .message-filters {
        gap: 8px;
      }
      
      .filter-btn {
        padding: 6px 12px;
        font-size: 0.85em;
      }
    }
    @media (max-width: 576px) {
      .message-filters {
        justify-content: center;
      }
      
      .filter-btn {
        flex: 1;
        min-width: 120px;
        justify-content: center;
      }
    }
    .interview-info {
      margin-top: 15px;
      padding: 12px;
      background: #f8f9fa;
      border-radius: 6px;
      border-left: 3px solid #17a2b8;
    }
    .interview-deadline {
      display: flex;
      align-items: center;
      gap: 8px;
      color: #6c757d;
      font-size: 0.9em;
      margin-bottom: 10px;
    }
    .interview-deadline i {
      color: #17a2b8;
    }
    .interview-info .btn-primary {
      padding: 6px 15px;
      border-radius: 20px;
      font-size: 0.9em;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.3s ease;
    }
    .interview-info .btn-primary:hover {
      transform: translateY(-1px);
      box-shadow: 0 2px 4px rgba(23, 162, 184, 0.2);
    }
  </style>
<?php 
if (!isset($_GET['p'])) {
  # code... 
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-envelope"></i> Messages</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="search-box">
                <i class="fa fa-search"></i>
                <input type="text" class="form-control" placeholder="Search messages..." id="messageSearch">
                </div>
              
              <div class="message-filters">
                <button class="filter-btn active" data-filter="all">
                  <i class="fa fa-inbox"></i> All Messages
                </button>
                <button class="filter-btn" data-filter="unread">
                  <i class="fa fa-envelope"></i> Unread
                </button>
                <button class="filter-btn" data-filter="approved">
                  <i class="fa fa-check-circle"></i> Approved
                </button>
                <button class="filter-btn" data-filter="interview">
                  <i class="fa fa-calendar-check-o"></i> Interview
                </button>
                <button class="filter-btn" data-filter="rejected">
                  <i class="fa fa-times-circle"></i> Rejected
                </button>
              </div>
              
              <div class="message-list">
                    <?php 
                $sql = "SELECT jr.*, c.COMPANYNAME, j.OCCUPATIONTITLE, f.FEEDBACK 
                        FROM tbljobregistration jr 
                        JOIN tblcompany c ON jr.COMPANYID = c.COMPANYID 
                        JOIN tbljob j ON jr.JOBID = j.JOBID 
                        LEFT JOIN tblfeedback f ON jr.REGISTRATIONID = f.REGISTRATIONID 
                        WHERE jr.APPLICANTID = '{$_SESSION['APPLICANTID']}' 
                        AND jr.PENDINGAPPLICATION = 0 
                        ORDER BY jr.DATETIMEAPPROVED DESC";
                        $mydb->setQuery($sql);
                        $cur = $mydb->loadResultList();
                
                if ($cur) {
                        foreach ($cur as $result) {
                    $statusClass = '';
                    switch(strtolower($result->REMARKS)) {
                      case 'pending':
                        $statusClass = 'status-pending';
                        break;
                      case 'approved':
                        $statusClass = 'status-approved';
                        break;
                      case 'rejected':
                        $statusClass = 'status-rejected';
                        break;
                      case 'interview scheduled':
                        $statusClass = 'status-interview';
                        break;
                    }
                    
                    $unreadClass = $result->HVIEW == 0 ? 'unread' : '';
                    $messagePreview = isset($result->FEEDBACK) ? $result->FEEDBACK : $result->REMARKS;
                    ?>
                    <div class="message-card <?php echo $unreadClass; ?>" data-status="<?php echo strtolower($result->REMARKS); ?>">
                      <div class="card-body p-3">
                        <div class="row">
                          <div class="col-md-8">
                            <h5 class="company-name">
                              <a href="index.php?view=message&p=readmessage&id=<?php echo $result->REGISTRATIONID; ?>">
                                <?php echo $result->COMPANYNAME; ?>
                              </a>
                            </h5>
                            <div class="message-content">
                              <strong><?php echo $result->OCCUPATIONTITLE; ?></strong>
                              <div class="message-preview"><?php echo $messagePreview; ?></div>
                              
                              <?php if (strtolower($result->REMARKS) == 'interview scheduled'): 
                                $sql = "SELECT * FROM tblinterviewinvitations WHERE REGISTRATIONID = '{$result->REGISTRATIONID}' AND EXPIRY_DATE > NOW()";
                                $mydb->setQuery($sql);
                                $invitation = $mydb->loadSingleResult();
                                if ($invitation):
                              ?>
                                <div class="interview-info">
                                  <div class="interview-deadline">
                                    <i class="fa fa-calendar-times-o"></i>
                                    <span>Interview Deadline: <?php echo date('F d, Y', strtotime($invitation->EXPIRY_DATE)); ?></span>
                                  </div>
                                </div>
                              <?php 
                                endif;
                              endif; 
                              ?>
                            </div>
                          </div>
                          <div class="col-md-4 text-right">
                            <span class="message-status <?php echo $statusClass; ?>">
                              <?php echo ucfirst($result->REMARKS); ?>
                            </span>
                            <div class="message-date mt-2">
                              <i class="fa fa-clock-o"></i> <?php echo date('M d, Y h:i A', strtotime($result->DATETIMEAPPROVED)); ?>
                </div>
              </div>
                    </div>
                  </div>
                </div>
                    <?php
                  }
                } else {
                  echo '<div class="empty-state">
                          <i class="fa fa-inbox"></i>
                          <h4>No Messages</h4>
                          <p>You don\'t have any messages yet.</p>
                        </div>';
                }
                ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
   
  <script>
  $(document).ready(function() {
    // Search functionality
    $("#messageSearch").on("keyup", function() {
      var value = $(this).val().toLowerCase();
      $(".message-card").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
    });

    // Filter functionality
    $('.filter-btn').click(function() {
      $('.filter-btn').removeClass('active');
      $(this).addClass('active');
      
      var filter = $(this).data('filter');
      if (filter === 'all') {
        $('.message-card').show();
      } else if (filter === 'unread') {
        $('.message-card').hide();
        $('.message-card.unread').show();
      } else if (filter === 'interview') {
        $('.message-card').hide();
        $('.message-card[data-status="interview scheduled"]').show();
      } else {
        $('.message-card').hide();
        $('.message-card[data-status="' + filter + '"]').show();
      }
    });
  });
  </script>
   
 <?php }else{  
  require_once('readmessage.php');
 } ?>