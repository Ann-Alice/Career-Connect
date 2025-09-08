<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

echo "<h1>AI Interview Session</h1>";

// Initialize conversation if not started
if (!isset($_SESSION['conversation'])) {
    $_SESSION['conversation'] = [];
}

// Handle candidate's response
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['candidate_response'])) {
        $candidate_response = trim($_POST['candidate_response']);
        
        // Record response with context
        $_SESSION['conversation'][] = [
            'role' => 'candidate',
            'message' => $candidate_response,
            'question_index' => $_SESSION['interview_state']['current_question'],
            'timestamp' => date('Y-m-d H:i:s'),
            'is_elaboration' => $_SESSION['interview_state']['last_response_analyzed']
        ];
        
        // AI response logic
        $ai_response = generateAIResponse($candidate_response);
        $_SESSION['conversation'][] = [
            'role' => 'ai',
            'message' => $ai_response,
            'question_index' => $_SESSION['interview_state']['current_question'],
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
}

// Display conversation interface
echo "<div style='margin: 20px;'>";
echo "<div id='conversation' style='margin-bottom: 20px;'>";
foreach ($_SESSION['conversation'] as $message) {
    $role = $message['role'];
    $text = htmlspecialchars($message['message']);
    $color = $role === 'ai' ? '#e3f2fd' : '#f5f5f5';
    $question_num = isset($message['question_index']) ? "Q" . ($message['question_index'] + 1) : '';
    $timestamp = isset($message['timestamp']) ? "<small style='color: #666;'>" . $message['timestamp'] . "</small>" : '';
    
    echo "<div style='background: $color; padding: 10px; margin: 5px; border-radius: 5px;'>";
    echo "<strong>" . ucfirst($role) . " $question_num:</strong> $text<br>";
    echo $timestamp;
    echo "</div>";
}
echo "</div>";

// Input form for candidate
echo "<form method='POST' action=''>";
echo "<textarea name='candidate_response' rows='4' style='width: 100%; margin-bottom: 10px;' placeholder='Type your response here...'></textarea><br>";
echo "<button type='submit' style='padding: 10px 20px;'>Send Response</button>";
echo "</form>";

// Reset conversation button
echo "<form method='POST' action=''>";
echo "<input type='hidden' name='reset' value='1'>";
echo "<button type='submit' style='margin-top: 10px; padding: 5px 10px;'>Reset Conversation</button>";
echo "</form>";
echo "</div>";

// Handle conversation reset
if (isset($_POST['reset'])) {
    $_SESSION['conversation'] = [];
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Add this function before the closing PHP tag
function generateAIResponse($candidate_response) {
    // Initialize interview state if not set
    if (!isset($_SESSION['interview_state'])) {
        $_SESSION['interview_state'] = [
            'current_question' => 0,
            'last_response_analyzed' => false,
            'questions' => [
                "Tell me about your programming experience.",
                "What programming languages are you most comfortable with?",
                "Can you describe a challenging project you've worked on?",
                "How do you handle technical difficulties during development?",
                "What's your approach to learning new technologies?"
            ],
            'response_stage' => 'initial'
        ];
        return "Welcome to the interview! " . $_SESSION['interview_state']['questions'][0];
    }

    $state = &$_SESSION['interview_state'];
    
    // Handle response based on current stage
    if ($state['response_stage'] === 'initial') {
        // First response to the question
        $state['last_response_analyzed'] = false;
        $response = "Thank you for sharing that. Could you please elaborate more on ";
        
        // Add context-specific follow-up based on current question
        switch ($state['current_question']) {
            case 0:
                $response .= "your specific roles and responsibilities in your programming experience?";
                break;
            case 1:
                $response .= "how you've applied these programming languages in real projects?";
                break;
            case 2:
                $response .= "how you specifically solved those challenges?";
                break;
            case 3:
                $response .= "a specific example of a technical difficulty you resolved?";
                break;
            case 4:
                $response .= "a recent technology you learned and how you mastered it?";
                break;
            default:
                $response .= "that?";
        }
        
        $state['response_stage'] = 'followup';
        $state['last_response_analyzed'] = true;
        return $response;
    } 
    elseif ($state['response_stage'] === 'followup') {
        // After receiving elaboration, move to next question
        $state['last_response_analyzed'] = false;
        $response = "Thank you for providing those details. ";
        
        // Move to next question
        $state['current_question']++;
        $state['response_stage'] = 'initial';
        
        // Check if there are more questions
        if ($state['current_question'] < count($state['questions'])) {
            $response .= "\n\nNow, let's move to the next question: " . $state['questions'][$state['current_question']];
        } else {
            $response .= "\n\nWe've completed all the questions. Thank you for your time. Do you have any questions for me?";
            $state['response_stage'] = 'complete';
        }
        
        return $response;
    }
    
    // Handle final stage
    if ($state['response_stage'] === 'complete') {
        return "Thank you for your questions. I appreciate your participation in this interview. Best of luck with your application!";
    }
    
    return "Thank you for your participation.";
}
?>