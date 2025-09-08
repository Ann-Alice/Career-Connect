<?php
require_once('../include/initialize.php');
require_once('../include/config.php');
require_once('../include/database.php');
require_once('../include/db_object.php');
require_once('../include/session.php');
require_once('../include/functions.php');

if (!isset($_SESSION['ADMINID'])) {
    redirect(web_root . "admin/login.php");
}

$action = (isset($_GET['action']) && $_GET['action'] != '') ? $_GET['action'] : '';

switch ($action) {
    case 'add':
        doAddReview();
        break;
    default:
        showReviewForm();
        break;
}

function showReviewForm() {
    global $mydb;
    
    // Get completed interviews for review
    $sql = "SELECT r.*, a.FNAME, a.LNAME, j.OCCUPATIONTITLE 
            FROM tbljobregistration r 
            JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
            JOIN tbljob j ON r.JOBID = j.JOBID 
            WHERE r.INTERVIEW_STATUS = 'Completed' 
            ORDER BY r.INTERVIEW_COMPLETED_AT DESC";
    $mydb->setQuery($sql);
    $interviews = $mydb->loadResultList();
    
    include('header.php');
    ?>
    <style>
        .review-form-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 30px;
        }
        .rating-input {
            display: none;
        }
        .rating-label {
            font-size: 24px;
            color: #ddd;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        .rating-label:hover,
        .rating-label:hover ~ .rating-label,
        .rating-input:checked ~ .rating-label {
            color: #ffc107;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
    </style>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="review-form-container">
                    <h2 class="text-center mb-4">
                        <i class="fas fa-star"></i> Add Candidate Interview Review
                    </h2>
                    
                    <form method="POST" action="?action=add">
                        <div class="form-group">
                            <label for="registration_id"><strong>Select Candidate:</strong></label>
                            <select class="form-control" name="registration_id" id="registration_id" required>
                                <option value="">Choose a candidate...</option>
                                <?php foreach ($interviews as $interview) { ?>
                                <option value="<?php echo $interview->REGISTRATIONID; ?>">
                                    <?php echo $interview->FNAME . ' ' . $interview->LNAME; ?> - 
                                    <?php echo $interview->OCCUPATIONTITLE; ?> 
                                    (<?php echo date('M d, Y', strtotime($interview->INTERVIEW_COMPLETED_AT)); ?>)
                                </option>
                                <?php } ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label><strong>Overall Rating:</strong></label>
                            <div class="rating-container">
                                <input type="radio" name="rating" value="1" id="rating1" class="rating-input" required>
                                <label for="rating1" class="rating-label">★</label>
                                <input type="radio" name="rating" value="2" id="rating2" class="rating-input">
                                <label for="rating2" class="rating-label">★</label>
                                <input type="radio" name="rating" value="3" id="rating3" class="rating-input">
                                <label for="rating3" class="rating-label">★</label>
                                <input type="radio" name="rating" value="4" id="rating4" class="rating-input">
                                <label for="rating4" class="rating-label">★</label>
                                <input type="radio" name="rating" value="5" id="rating5" class="rating-input">
                                <label for="rating5" class="rating-label">★</label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="experience_rating"><strong>Experience Rating (1-5):</strong></label>
                            <select class="form-control" name="experience_rating" id="experience_rating" required>
                                <option value="">Select rating...</option>
                                <option value="1">1 - Poor</option>
                                <option value="2">2 - Fair</option>
                                <option value="3">3 - Good</option>
                                <option value="4">4 - Very Good</option>
                                <option value="5">5 - Excellent</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="difficulty_rating"><strong>Difficulty Rating (1-5):</strong></label>
                            <select class="form-control" name="difficulty_rating" id="difficulty_rating" required>
                                <option value="">Select difficulty...</option>
                                <option value="1">1 - Very Easy</option>
                                <option value="2">2 - Easy</option>
                                <option value="3">3 - Moderate</option>
                                <option value="4">4 - Difficult</option>
                                <option value="5">5 - Very Difficult</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="comments"><strong>Comments:</strong></label>
                            <textarea class="form-control" name="comments" id="comments" rows="4" 
                                      placeholder="Share your thoughts about the interview experience..."></textarea>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-submit">
                                <i class="fas fa-save"></i> Save Review
                            </button>
                            <a href="interview-results.php" class="btn btn-secondary ml-2">
                                <i class="fas fa-arrow-left"></i> Back to Results
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
    include('footer.php');
}

function doAddReview() {
    global $mydb;
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect("add-interview-review.php");
    }
    
    $registration_id = $_POST['registration_id'];
    $rating = $_POST['rating'];
    $experience_rating = $_POST['experience_rating'];
    $difficulty_rating = $_POST['difficulty_rating'];
    $comments = $_POST['comments'];
    
    // Get candidate name
    $sql = "SELECT a.FNAME, a.LNAME 
            FROM tbljobregistration r 
            JOIN tblapplicants a ON r.APPLICANTID = a.APPLICANTID 
            WHERE r.REGISTRATIONID = '{$registration_id}'";
    $mydb->setQuery($sql);
    $candidate = $mydb->loadSingleResult();
    
    if (!$candidate) {
        message("Candidate not found.", "error");
        redirect("add-interview-review.php");
    }
    
    $candidate_name = $candidate->FNAME . ' ' . $candidate->LNAME;
    
    // Insert review
    $sql = "INSERT INTO tblinterviewreviews (REGISTRATIONID, CANDIDATE_NAME, RATING, EXPERIENCE_RATING, DIFFICULTY_RATING, COMMENTS) 
            VALUES ('{$registration_id}', '{$candidate_name}', '{$rating}', '{$experience_rating}', '{$difficulty_rating}', '{$comments}')";
    $mydb->setQuery($sql);
    
    if ($mydb->executeQuery()) {
        message("Review added successfully!", "success");
    } else {
        message("Error adding review.", "error");
    }
    
    redirect("interview-results.php");
}
?> 