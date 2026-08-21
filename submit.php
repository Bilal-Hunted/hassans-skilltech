<?php
// ============================================
// COMPLETE BACKEND - Hassan's SkillTech Solutions
// Email: hazratbilal20458@gmail.com
// ============================================

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set response header to JSON
header('Content-Type: application/json');

// ============================================
// CONFIGURATION - UPDATED WITH YOUR EMAIL
// ============================================

// YOUR EMAIL ADDRESS - UPDATED
$to_email = 'hazratbilal20458@gmail.com';

// Backup email (add more if needed)
$backup_email = 'hazratbilal20458@gmail.com';

// Email subject
$email_subject = "New Enquiry - Hassan's SkillTech Solutions";

// ============================================
// SANITIZATION FUNCTION
// ============================================

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// ============================================
// PROCESS FORM DATA
// ============================================

$response = array('status' => 'error', 'message' => '');

try {
    // Check if form was submitted
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method.');
    }

    // ============================================
    // COLLECT AND SANITIZE DATA
    // ============================================

    // Personal Information
    $fullName = isset($_POST['fullName']) ? sanitize_input($_POST['fullName']) : '';
    $fatherName = isset($_POST['fatherName']) ? sanitize_input($_POST['fatherName']) : '';
    $cnic = isset($_POST['cnic']) ? sanitize_input($_POST['cnic']) : '';
    $dob = isset($_POST['dob']) ? sanitize_input($_POST['dob']) : '';
    $email = isset($_POST['email']) ? sanitize_input($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitize_input($_POST['phone']) : '';
    $whatsapp = isset($_POST['whatsapp']) ? sanitize_input($_POST['whatsapp']) : '';
    $gender = isset($_POST['gender']) ? sanitize_input($_POST['gender']) : '';
    $address = isset($_POST['address']) ? sanitize_input($_POST['address']) : '';
    
    // Purpose
    $purpose = isset($_POST['purpose']) ? sanitize_input($_POST['purpose']) : '';
    
    // Agency Services
    $agencyServices = isset($_POST['agencyServices']) ? $_POST['agencyServices'] : array();
    $projectDetails = isset($_POST['projectDetails']) ? sanitize_input($_POST['projectDetails']) : '';
    $budget = isset($_POST['budget']) ? sanitize_input($_POST['budget']) : '';
    $timeline = isset($_POST['timeline']) ? sanitize_input($_POST['timeline']) : '';
    $infrastructure = isset($_POST['infrastructure']) ? sanitize_input($_POST['infrastructure']) : '';
    
    // Academy
    $courseCategories = isset($_POST['courseCategories']) ? $_POST['courseCategories'] : array();
    $courseName = isset($_POST['courseName']) ? sanitize_input($_POST['courseName']) : '';
    $educationLevel = isset($_POST['educationLevel']) ? sanitize_input($_POST['educationLevel']) : '';
    $experience = isset($_POST['experience']) ? sanitize_input($_POST['experience']) : '';
    $courseMode = isset($_POST['courseMode']) ? sanitize_input($_POST['courseMode']) : '';
    $timing = isset($_POST['timing']) ? $_POST['timing'] : array();
    $motivation = isset($_POST['motivation']) ? sanitize_input($_POST['motivation']) : '';
    
    // Additional
    $hearAbout = isset($_POST['hearAbout']) ? sanitize_input($_POST['hearAbout']) : '';
    $specialRequests = isset($_POST['specialRequests']) ? sanitize_input($_POST['specialRequests']) : '';
    $terms = isset($_POST['terms']) ? 'Yes' : 'No';

    // ============================================
    // VALIDATION
    // ============================================

    $errors = array();

    if (empty($fullName)) $errors[] = 'Full Name is required.';
    if (empty($cnic)) $errors[] = 'CNIC Number is required.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid Email Address is required.';
    if (empty($phone)) $errors[] = 'Phone Number is required.';
    if (empty($address)) $errors[] = 'Address is required.';
    if (empty($purpose)) $errors[] = 'Please select your purpose.';

    // Validate based on purpose
    if ($purpose === 'agency' || $purpose === 'both') {
        if (empty($agencyServices)) $errors[] = 'Please select at least one agency service.';
        if (empty($projectDetails)) $errors[] = 'Project details are required.';
    }
    
    if ($purpose === 'academy' || $purpose === 'both') {
        if (empty($courseCategories)) $errors[] = 'Please select at least one course category.';
        if (empty($courseName)) $errors[] = 'Please specify the course name.';
        if (empty($motivation)) $errors[] = 'Please share your motivation for joining.';
    }
    
    if (empty($terms) || $terms !== 'Yes') $errors[] = 'You must agree to the Terms & Privacy Policy.';

    // If there are validation errors
    if (!empty($errors)) {
        throw new Exception(implode(' ', $errors));
    }

    // ============================================
    // BUILD EMAIL CONTENT
    // ============================================

    $emailBody = "
    ================================================
    NEW ENQUIRY - HASSAN'S SKILLTECH SOLUTIONS
    ================================================
    
    --- PERSONAL INFORMATION ---
    Full Name: $fullName
    Father's Name: $fatherName
    CNIC / B-Form: $cnic
    Date of Birth: $dob
    Gender: $gender
    Email: $email
    Phone: $phone
    WhatsApp: $whatsapp
    Address: $address
    
    --- PURPOSE ---
    $purpose
    
    ";

    // Agency Section
    if ($purpose === 'agency' || $purpose === 'both') {
        $emailBody .= "
    --- AGENCY SERVICES ---
    Services Selected: " . implode(', ', $agencyServices) . "
    Project Details: $projectDetails
    Budget Range: $budget
    Timeline: $timeline
    Existing Infrastructure: $infrastructure
    
    ";
    }

    // Academy Section
    if ($purpose === 'academy' || $purpose === 'both') {
        $emailBody .= "
    --- ACADEMY ENROLLMENT ---
    Course Categories: " . implode(', ', $courseCategories) . "
    Specific Course: $courseName
    Education Level: $educationLevel
    Previous Experience: $experience
    Course Mode: $courseMode
    Preferred Timing: " . implode(', ', $timing) . "
    Motivation: $motivation
    
    ";
    }

    // Additional Information
    $emailBody .= "
    --- ADDITIONAL INFORMATION ---
    How did you hear about us? $hearAbout
    Special Requests: $specialRequests
    Terms Agreed: $terms
    
    ================================================
    Submitted via: hassansskilltech.com
    Date & Time: " . date('Y-m-d H:i:s') . "
    IP Address: " . $_SERVER['REMOTE_ADDR'] . "
    ================================================
    ";

    // ============================================
    // SEND EMAIL TO hazratbilal20458@gmail.com
    // ============================================

    // Email Headers
    $headers = "From: " . $email . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $headers .= "X-Priority: 1\r\n";

    // Send main email
    $main_sent = mail($to_email, $email_subject, $emailBody, $headers);
    
    // Send to backup if main fails
    if (!$main_sent && !empty($backup_email)) {
        $backup_sent = mail($backup_email, $email_subject, $emailBody, $headers);
    }

    // ============================================
    // SEND AUTO-RESPONSE TO USER
    // ============================================

    $userSubject = "Thank You - Hassan's SkillTech Solutions";
    $userMessage = "
    Dear $fullName,

    Thank you for contacting Hassan's SkillTech Solutions!

    We have received your enquiry and our team will reach out to you within 24 hours.

    --- Your Enquiry Summary ---
    Purpose: $purpose
    Name: $fullName
    Email: $email
    Phone: $phone

    If you need immediate assistance, please call us at:
    +92 301 9005410
    +92 333 9943403

    We look forward to serving you!

    Best regards,
    Hassan Basri (CEO)
    Hassan's SkillTech Solutions
    Wana, South Waziristan, KPK, Pakistan

    *Empowering Businesses, Building Careers.*
    ";

    $userHeaders = "From: info@hassansskilltech.com\r\n";
    $userHeaders .= "Reply-To: hazratbilal20458@gmail.com\r\n";
    $userHeaders .= "Content-Type: text/plain; charset=UTF-8\r\n";

    mail($email, $userSubject, $userMessage, $userHeaders);

    // ============================================
    // SAVE BACKUP TO FILE (Always good to have)
    // ============================================

    $backup_data = "========================================\n";
    $backup_data .= "Date: " . date('Y-m-d H:i:s') . "\n";
    $backup_data .= "Name: $fullName\n";
    $backup_data .= "Email: $email\n";
    $backup_data .= "Phone: $phone\n";
    $backup_data .= "Purpose: $purpose\n";
    $backup_data .= "----------------------------------------\n\n";
    
    file_put_contents('submissions_backup.txt', $backup_data, FILE_APPEND | LOCK_EX);

    // ============================================
    // SUCCESS RESPONSE
    // ============================================

    if ($main_sent || isset($backup_sent)) {
        $response['status'] = 'success';
        $response['message'] = '✅ Your enquiry has been submitted successfully! Our team will contact you within 24 hours.';
    } else {
        // Email failed but data is saved to file
        $response['status'] = 'success';
        $response['message'] = '✅ Your enquiry has been recorded! We will contact you within 24 hours. (Check your spam folder if you don\'t see our reply)';
    }

} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = '❌ ' . $e->getMessage();
}

// ============================================
// RETURN JSON RESPONSE
// ============================================

echo json_encode($response);
exit;
?>