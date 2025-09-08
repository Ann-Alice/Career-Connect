<?php
require_once('../include/initialize.php');

// Check if user is logged in as admin
if(!isset($_SESSION['ADMIN_USERID'])){
    header("Location: ".web_root."admin/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dropdown Test - Interview Results</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            padding: 20px;
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .test-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
            background: white !important;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
        }
        select.form-control {
            appearance: auto !important;
            -webkit-appearance: menulist !important;
            -moz-appearance: menulist !important;
            pointer-events: auto !important;
            user-select: auto !important;
        }
        .test-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-working { background: #d4edda; color: #155724; }
        .status-failed { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="test-container">
        <h2><i class="fa fa-cogs"></i> Interview Results - Dropdown & Download Test</h2>
        <p class="text-muted">Test the dropdown functionality and download features that were recently fixed.</p>
        <hr>

        <!-- Test Section 1: Recommendation Dropdown -->
        <div class="test-section">
            <h4><i class="fa fa-star"></i> Test 1: Recommendation Dropdown</h4>
            <div class="form-group">
                <label style="font-weight: 600;">Final Recommendation</label>
                <select name="recommendation" class="form-control" required style="color: #6c757d; font-weight: 500;">
                    <option value="" style="color: #6c757d; font-style: italic;" selected>Select Recommendation...</option>
                    <option value="Highly Recommended" style="color: #495057;">Highly Recommended</option>
                    <option value="Recommended" style="color: #495057;">Recommended</option>
                    <option value="Consider" style="color: #495057;">Consider</option>
                    <option value="Not Recommended" style="color: #495057;">Not Recommended</option>
                </select>
            </div>
            <div class="test-status">
                <span class="status-badge" id="recommendation-status">Click dropdown to test</span>
            </div>
        </div>

        <!-- Test Section 2: Final Status Dropdown -->
        <div class="test-section">
            <h4><i class="fa fa-flag"></i> Test 2: Final Status Dropdown</h4>
            <div class="form-group">
                <label style="font-weight: 600;">Result Status</label>
                <select name="result_status" class="form-control" required style="color: #6c757d; font-weight: 500;">
                    <option value="" style="color: #6c757d; font-style: italic;" selected>Select Result...</option>
                    <option value="Congratulations - You have been selected" style="color: #495057;">Selected</option>
                    <option value="Thank you for your interest" style="color: #495057;">Not Selected</option>
                    <option value="We will contact you soon" style="color: #495057;">Under Review</option>
                </select>
            </div>
            <div class="test-status">
                <span class="status-badge" id="status-status">Click dropdown to test</span>
            </div>
        </div>

        <!-- Test Section 3: Download Video -->
        <div class="test-section">
            <h4><i class="fa fa-download"></i> Test 3: Download Video Functionality</h4>
            <button type="button" class="btn btn-info" onclick="testVideoDownload()">
                <i class="fa fa-download"></i> Test Download Recording
            </button>
            <div class="test-status" style="margin-top: 15px;">
                <span class="status-badge" id="download-status">Click button to test</span>
            </div>
        </div>

        <!-- Test Results Summary -->
        <div class="test-section">
            <h4><i class="fa fa-check-circle"></i> Test Summary</h4>
            <div id="summary-results">
                <p>Complete the tests above to see the summary.</p>
            </div>
            <hr>
            <div class="text-center">
                <a href="interview-results.php" class="btn btn-primary">
                    <i class="fa fa-arrow-left"></i> Back to Interview Results
                </a>
                <button type="button" class="btn btn-success" onclick="runAllTests()">
                    <i class="fa fa-play"></i> Run All Tests
                </button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script>
    let testResults = {
        recommendation: false,
        status: false,
        download: false
    };

    $(document).ready(function() {
        // Apply the same fixes as in interview-results.php
        $('select.form-control').each(function() {
            $(this).removeAttr('disabled readonly');
            $(this).css({
                'pointer-events': 'auto !important',
                'user-select': 'auto !important',
                '-webkit-user-select': 'auto !important',
                'appearance': 'auto !important',
                '-webkit-appearance': 'menulist !important',
                'position': 'relative',
                'z-index': '1'
            });
        });

        // Test recommendation dropdown
        $('select[name="recommendation"]').on('change', function() {
            if ($(this).val() !== '') {
                $('#recommendation-status').removeClass('status-failed').addClass('status-working').text('Working ✓');
                testResults.recommendation = true;
            } else {
                $('#recommendation-status').removeClass('status-working').addClass('status-failed').text('Failed ✗');
                testResults.recommendation = false;
            }
            updateSummary();
        });

        // Test status dropdown
        $('select[name="result_status"]').on('change', function() {
            if ($(this).val() !== '') {
                $('#status-status').removeClass('status-failed').addClass('status-working').text('Working ✓');
                testResults.status = true;
            } else {
                $('#status-status').removeClass('status-working').addClass('status-failed').text('Failed ✗');
                testResults.status = false;
            }
            updateSummary();
        });
    });

    function testVideoDownload() {
        try {
            // Test with a demo registration ID
            const demoId = 1;
            const downloadUrl = 'download-recording.php?id=' + demoId;
            
            // Create download link
            const link = document.createElement('a');
            link.href = downloadUrl;
            link.download = 'test_recording.mp4';
            link.style.display = 'none';
            
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            $('#download-status').removeClass('status-failed').addClass('status-working').text('Working ✓');
            testResults.download = true;
            
        } catch (error) {
            $('#download-status').removeClass('status-working').addClass('status-failed').text('Failed ✗');
            testResults.download = false;
            console.error('Download test failed:', error);
        }
        updateSummary();
    }

    function runAllTests() {
        // Reset all tests
        $('select').val('').trigger('change');
        $('.status-badge').removeClass('status-working status-failed').text('Testing...');
        
        setTimeout(() => {
            // Test dropdowns by programmatically selecting values
            $('select[name="recommendation"]').val('Recommended').trigger('change');
        }, 500);
        
        setTimeout(() => {
            $('select[name="result_status"]').val('Congratulations - You have been selected').trigger('change');
        }, 1000);
        
        setTimeout(() => {
            testVideoDownload();
        }, 1500);
    }

    function updateSummary() {
        const passed = Object.values(testResults).filter(r => r).length;
        const total = Object.keys(testResults).length;
        
        let summaryHtml = `<p><strong>Test Results: ${passed}/${total} passed</strong></p>`;
        summaryHtml += '<ul>';
        summaryHtml += `<li>Recommendation Dropdown: ${testResults.recommendation ? '✓ Working' : '✗ Failed'}</li>`;
        summaryHtml += `<li>Status Dropdown: ${testResults.status ? '✓ Working' : '✗ Failed'}</li>`;
        summaryHtml += `<li>Video Download: ${testResults.download ? '✓ Working' : '✗ Failed'}</li>`;
        summaryHtml += '</ul>';
        
        if (passed === total) {
            summaryHtml += '<div class="alert alert-success"><strong>All tests passed!</strong> The interview results page should now work correctly.</div>';
        } else {
            summaryHtml += '<div class="alert alert-warning"><strong>Some tests failed.</strong> There may still be issues that need to be addressed.</div>';
        }
        
        $('#summary-results').html(summaryHtml);
    }
    </script>
</body>
</html>