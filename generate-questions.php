<?php
// Set headers for JSON response
header('Content-Type: application/json');

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['job_title']) || !isset($data['job_description'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required data'
    ]);
    exit;
}

try {
    // Generate questions based on job requirements
    $questions = generatePersonalizedQuestions(
        $data['job_title'],
        $data['job_description'],
        $data['job_requirements'] ?? '',
        $data['applicant_name'] ?? ''
    );
    
    echo json_encode([
        'success' => true,
        'questions' => $questions
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error generating questions: ' . $e->getMessage()
    ]);
}

function generatePersonalizedQuestions($jobTitle, $jobDescription, $requirements, $applicantName) {
    $questions = [];
    
    // Friendly and interactive opening questions
    $questions[] = "Hi there! I'm so excited to chat with you today. Could you tell me a bit about yourself and what brings you to this {$jobTitle} position? I'd love to hear your story!";
    $questions[] = "I'm really curious about your journey! What is it about this {$jobTitle} role that caught your attention? What excites you most about the opportunity?";
    
    // Skills-based questions with a friendly approach
    if (!empty($requirements)) {
        $requiredSkills = extractKeySkills($requirements);
        if (!empty($requiredSkills)) {
            $questions[] = "I'd love to hear about your experience with " . implode(', ', $requiredSkills) . "! Can you share a story about a time when you really excelled using these skills?";
        }
    }
    
    // Job-specific questions with personal touch
    $jobSpecificQuestions = generateJobSpecificQuestions($jobDescription, $requirements);
    $questions = array_merge($questions, $jobSpecificQuestions);
    
    // Interactive behavioral questions
    $questions[] = "I'm really interested in how you handle challenges! Can you tell me about a time when things didn't go as planned? What happened, and how did you turn it around?";
    $questions[] = "I love hearing about people's growth journeys! What's something you've learned about yourself in the past year that really surprised you?";
    
    // Future-oriented questions with enthusiasm
    $questions[] = "I'm curious about your dreams! Where do you see yourself in 5 years, and what steps are you taking to get there? I'd love to hear your vision!";
    $questions[] = "What really motivates you to get up in the morning? I'm interested in what drives you and keeps you passionate about your work!";
    
    // Fun and engaging closing questions
    $questions[] = "I've really enjoyed our conversation! Is there anything about this role or our company that you'd love to know more about? I'm here to help!";
    
    return $questions;
}

function extractKeySkills($text) {
    // Common technical skills to look for
    $commonSkills = [
        'programming', 'coding', 'development', 'software', 'database',
        'management', 'leadership', 'communication', 'project',
        'analysis', 'analytics', 'design', 'marketing', 'sales',
        'php', 'javascript', 'python', 'java', 'html', 'css',
        'mysql', 'sql', 'excel', 'word', 'powerpoint'
    ];
    
    $skills = [];
    foreach ($commonSkills as $skill) {
        if (stripos($text, $skill) !== false) {
            $skills[] = $skill;
        }
    }
    
    return array_unique($skills);
}

function generateJobSpecificQuestions($jobDescription, $requirements) {
    $questions = [];
    
    // Extract key responsibilities
    $responsibilities = extractResponsibilities($jobDescription);
    foreach ($responsibilities as $responsibility) {
        $questions[] = "I'm really excited about the responsibility of " . $responsibility . "! Can you tell me about a time when you handled something similar? I'd love to hear how you approach this kind of work!";
    }
    
    // Extract required qualifications
    $qualifications = extractQualifications($requirements);
    foreach ($qualifications as $qualification) {
        $questions[] = "I'm curious about your experience with " . $qualification . "! What's the most interesting project you've worked on that involved this? I'd love to hear the details!";
    }
    
    return $questions;
}

function extractResponsibilities($jobDescription) {
    // Look for responsibility indicators
    $indicators = ['responsible for', 'duties include', 'will be', 'must be able to'];
    $responsibilities = [];
    
    foreach ($indicators as $indicator) {
        if (preg_match_all('/' . $indicator . ' ([^.]*?)\./i', $jobDescription, $matches)) {
            $responsibilities = array_merge($responsibilities, $matches[1]);
        }
    }
    
    return array_slice($responsibilities, 0, 3); // Limit to 3 responsibilities
}

function extractQualifications($requirements) {
    // Look for qualification indicators
    $indicators = ['required', 'must have', 'should have', 'preferred'];
    $qualifications = [];
    
    foreach ($indicators as $indicator) {
        if (preg_match_all('/' . $indicator . ' ([^.]*?)\./i', $requirements, $matches)) {
            $qualifications = array_merge($qualifications, $matches[1]);
        }
    }
    
    return array_slice($qualifications, 0, 3); // Limit to 3 qualifications
} 