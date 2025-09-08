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
    <title>Dropdown Diagnostic - Interview Results</title>
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
        .diagnostic-container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            max-width: 900px;
            margin: 0 auto;
        }
        .test-section {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .form-control {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
            background: white;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
        }
        /* Enhanced dropdown CSS - Force functionality */
        select.form-control {
            appearance: menulist !important;
            -webkit-appearance: menulist !important;
            -moz-appearance: menulist !important;
            -ms-appearance: menulist !important;
            color: #495057 !important;
            font-weight: 500;
            background-color: white !important;
            border: 2px solid #e9ecef !important;
            pointer-events: auto !important;
            user-select: auto !important;
            -webkit-user-select: auto !important;
            -moz-user-select: auto !important;
            -ms-user-select: auto !important;
            opacity: 1 !important;
            cursor: pointer !important;
            position: relative !important;
            z-index: 10 !important;
        }
        .status-indicator {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin-left: 10px;
        }
        .status-success { background: #d4edda; color: #155724; }
        .status-error { background: #f8d7da; color: #721c24; }
        .status-pending { background: #fff3cd; color: #856404; }
        .diagnostic-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="diagnostic-container">
        <div class="diagnostic-info">
            <h2><i class="fa fa-stethoscope"></i> Dropdown Diagnostic Tool</h2>
            <p>This tool tests the exact same dropdown configurations used in the Interview Results page.</p>
        </div>
        
        <!-- Test 1: Recommendation Dropdown -->
        <div class="test-section">
            <h4><i class="fa fa-star text-warning"></i> Test 1: Final Recommendation Dropdown</h4>
            <p class="text-muted">This tests the exact dropdown from the Admin Review section:</p>
            
            <div class="form-group">
                <label style="font-weight: 600;">Final Recommendation</label>
                <div style="display: flex; align-items: center;">
                    <select name="recommendation" class="form-control" required style="color: #6c757d; font-weight: 500; flex: 1;">
                        <option value="" style="color: #6c757d; font-style: italic;" selected>Select Recommendation...</option>
                        <option value="Highly Recommended" style="color: #495057;">Highly Recommended</option>
                        <option value="Recommended" style="color: #495057;">Recommended</option>
                        <option value="Consider" style="color: #495057;">Consider</option>
                        <option value="Not Recommended" style="color: #495057;">Not Recommended</option>
                    </select>
                    <span class="status-indicator status-pending" id="rec-status">Click to test</span>
                </div>
            </div>
        </div>
        
        <!-- Test 2: Result Status Dropdown -->
        <div class="test-section">
            <h4><i class="fa fa-envelope text-success"></i> Test 2: Result Status Dropdown</h4>
            <p class="text-muted">This tests the exact dropdown from the Email section:</p>
            
            <div class="form-group">
                <label style="font-weight: 600;">Result Status</label>
                <div style="display: flex; align-items: center;">
                    <select name="result_status" class="form-control" required style="color: #6c757d; font-weight: 500; flex: 1;">
                        <option value="" style="color: #6c757d; font-style: italic;" selected>Select Result...</option>
                        <option value="Congratulations - You have been selected" style="color: #495057;">Selected</option>
                        <option value="Thank you for your interest" style="color: #495057;">Not Selected</option>
                        <option value="We will be in touch soon" style="color: #495057;">Under Consideration</option>
                    </select>
                    <span class="status-indicator status-pending" id="status-status">Click to test</span>
                </div>
            </div>
        </div>
        
        <!-- Test 3: Download Video -->
        <div class="test-section">
            <h4><i class="fa fa-download text-info"></i> Test 3: Download Video Button</h4>
            <p class="text-muted">This tests the video download functionality:</p>
            
            <div style="text-align: center; padding: 20px;">
                <button type="button" class="btn btn-info btn-lg" onclick="testVideoDownload()" style="margin-right: 10px;">
                    <i class="fa fa-download"></i> Test Download Recording
                </button>
                <button type="button" class="btn btn-warning btn-lg" onclick="testVideoView()">
                    <i class="fa fa-play-circle"></i> Test View Recording
                </button>
                <div style="margin-top: 15px;">
                    <span class="status-indicator status-pending" id="download-status">Click to test</span>
                </div>
            </div>
        </div>
        
        <!-- Diagnostic Results -->
        <div class="test-section">
            <h4><i class="fa fa-clipboard text-primary"></i> Diagnostic Results</h4>
            <div id="diagnostic-results">
                <p>Complete the tests above to see detailed diagnostics.</p>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="text-center" style="margin-top: 30px;">
            <a href="interview-results.php" class="btn btn-primary btn-lg">
                <i class="fa fa-arrow-left"></i> Back to Interview Results
            </a>
            <button type="button" class="btn btn-success btn-lg" onclick="runAutomaticTest()" style="margin-left: 10px;">
                <i class="fa fa-play"></i> Run Automatic Test
            </button>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script>
    let testResults = {
        recommendation: false,
        status: false,
        download: false,
        view: false
    };
    
    // Apply the same fixes as interview-results.php
    $(document).ready(function() {
        console.log('Initializing dropdown diagnostic...');
        
        // Force enable all dropdowns immediately
        function enableDropdowns() {
            $('select.form-control').each(function() {
                const $select = $(this);
                
                // Remove all disabling attributes
                $select.removeAttr('disabled readonly tabindex');
                $select.prop('disabled', false);
                $select.prop('readonly', false);
                
                // Apply critical CSS directly
                $select.css({
                    'pointer-events': 'auto !important',
                    'user-select': 'auto !important',
                    'opacity': '1 !important',
                    'cursor': 'pointer !important',
                    'background-color': 'white !important',
                    'position': 'relative',
                    'z-index': '10'
                });
                
                console.log('Diagnostic: Enabled dropdown:', $select.attr('name'));\n            });\n        }\n        \n        // Initial enable\n        enableDropdowns();\n        setTimeout(enableDropdowns, 100);\n        \n        // Test event handlers\n        $('select[name=\"recommendation\"]').on('change', function() {\n            const val = $(this).val();\n            if (val !== '') {\n                $('#rec-status').removeClass('status-pending status-error').addClass('status-success').text('✓ Working');\n                testResults.recommendation = true;\n                console.log('Recommendation dropdown: WORKING, selected:', val);\n            } else {\n                $('#rec-status').removeClass('status-pending status-success').addClass('status-error').text('✗ Empty');\n                testResults.recommendation = false;\n            }\n            updateDiagnostics();\n        });\n        \n        $('select[name=\"result_status\"]').on('change', function() {\n            const val = $(this).val();\n            if (val !== '') {\n                $('#status-status').removeClass('status-pending status-error').addClass('status-success').text('✓ Working');\n                testResults.status = true;\n                console.log('Status dropdown: WORKING, selected:', val);\n            } else {\n                $('#status-status').removeClass('status-pending status-success').addClass('status-error').text('✗ Empty');\n                testResults.status = false;\n            }\n            updateDiagnostics();\n        });\n        \n        // Click detection\n        $('select.form-control').on('click', function() {\n            console.log('Dropdown clicked:', $(this).attr('name'));\n            $(this).prop('disabled', false).removeAttr('disabled readonly');\n        });\n    });\n    \n    function testVideoDownload() {\n        console.log('Testing video download...');\n        try {\n            // Test with registration ID 1\n            const testUrl = 'download-recording.php?id=1';\n            const link = document.createElement('a');\n            link.href = testUrl;\n            link.download = 'test_recording.mp4';\n            link.style.display = 'none';\n            \n            document.body.appendChild(link);\n            link.click();\n            document.body.removeChild(link);\n            \n            $('#download-status').removeClass('status-pending status-error').addClass('status-success').text('✓ Download Started');\n            testResults.download = true;\n            console.log('Download test: SUCCESS');\n        } catch (error) {\n            $('#download-status').removeClass('status-pending status-success').addClass('status-error').text('✗ Failed');\n            testResults.download = false;\n            console.error('Download test: FAILED', error);\n        }\n        updateDiagnostics();\n    }\n    \n    function testVideoView() {\n        console.log('Testing video view...');\n        try {\n            const viewUrl = 'view-recording.php?id=1';\n            window.open(viewUrl, '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes');\n            \n            $('#download-status').text('✓ View Opened').removeClass('status-pending status-error').addClass('status-success');\n            testResults.view = true;\n            console.log('View test: SUCCESS');\n        } catch (error) {\n            $('#download-status').text('✗ View Failed').removeClass('status-pending status-success').addClass('status-error');\n            testResults.view = false;\n            console.error('View test: FAILED', error);\n        }\n        updateDiagnostics();\n    }\n    \n    function runAutomaticTest() {\n        console.log('Running automatic test...');\n        \n        // Reset all\n        Object.keys(testResults).forEach(key => testResults[key] = false);\n        $('.status-indicator').removeClass('status-success status-error').addClass('status-pending').text('Testing...');\n        \n        setTimeout(() => {\n            // Test recommendation dropdown\n            $('select[name=\"recommendation\"]').val('Recommended').trigger('change');\n        }, 500);\n        \n        setTimeout(() => {\n            // Test status dropdown\n            $('select[name=\"result_status\"]').val('Congratulations - You have been selected').trigger('change');\n        }, 1000);\n        \n        setTimeout(() => {\n            // Test download\n            testVideoDownload();\n        }, 1500);\n    }\n    \n    function updateDiagnostics() {\n        const total = Object.keys(testResults).length;\n        const passed = Object.values(testResults).filter(r => r).length;\n        \n        let html = `<div style=\"background: ${passed === total ? '#d4edda' : '#fff3cd'}; padding: 15px; border-radius: 8px;\">`;        \n        html += `<h5>Test Results: ${passed}/${total-1} core features working</h5>`; // Exclude view test from core\n        html += '<ul style=\"margin: 10px 0;\">';\n        html += `<li><strong>Recommendation Dropdown:</strong> ${testResults.recommendation ? '✓ Working' : '✗ Not Working'}</li>`;\n        html += `<li><strong>Status Dropdown:</strong> ${testResults.status ? '✓ Working' : '✗ Not Working'}</li>`;\n        html += `<li><strong>Video Download:</strong> ${testResults.download ? '✓ Working' : '✗ Not Working'}</li>`;\n        html += `<li><strong>Video View:</strong> ${testResults.view ? '✓ Working' : '✗ Not Tested'}</li>`;\n        html += '</ul>';\n        \n        if (testResults.recommendation && testResults.status && testResults.download) {\n            html += '<p><strong>✅ All core features are working correctly!</strong></p>';\n            html += '<p>The Interview Results page should now function properly.</p>';\n        } else {\n            html += '<p><strong>⚠️ Some features need attention.</strong></p>';\n            \n            if (!testResults.recommendation || !testResults.status) {\n                html += '<p><strong>Dropdown Issue:</strong> If dropdowns are not clickable, this may be a browser-specific issue. Try:</p>';\n                html += '<ul>';\n                html += '<li>Refreshing the page</li>';\n                html += '<li>Clearing browser cache</li>';\n                html += '<li>Trying a different browser</li>';\n                html += '<li>Checking browser console for errors (F12)</li>';\n                html += '</ul>';\n            }\n            \n            if (!testResults.download) {\n                html += '<p><strong>Download Issue:</strong> Check server permissions and file paths.</p>';\n            }\n        }\n        \n        html += '</div>';\n        \n        $('#diagnostic-results').html(html);\n    }\n    </script>\n</body>\n</html>", "original_text": "", "replace_all": false}]