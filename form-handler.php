<?php
// form-handler.php - Script to process form submissions and send emails

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get form data and sanitize inputs
    $name = filter_var($_POST["name"] ?? "", FILTER_SANITIZE_STRING);
    $email = filter_var($_POST["email"] ?? "", FILTER_SANITIZE_EMAIL);
    $phone = filter_var($_POST["phone"] ?? "", FILTER_SANITIZE_STRING);
    $age = filter_var($_POST["age"] ?? "", FILTER_SANITIZE_NUMBER_INT);
    $background = filter_var($_POST["background"] ?? "", FILTER_SANITIZE_STRING);
    $interests = filter_var($_POST["interests"] ?? "", FILTER_SANITIZE_STRING);
    
    // Validate required fields
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email address is required";
    }
    
    if (empty($age) || $age < 18 || $age > 30) {
        $errors[] = "Age must be between 18 and 30";
    }
    
    if (empty($background)) {
        $errors[] = "Background information is required";
    }
    
    // If there are validation errors, redirect back with error message
    if (!empty($errors)) {
        $error_string = implode(", ", $errors);
        header("Location: index.html?status=error&message=" . urlencode($error_string));
        exit;
    }
    
    // Set up email parameters
    $to = "johannesburghub.youngaileaders@gmail.com"; // Replace with your actual email
    $subject = "New AI for Good Johannesburg Hub Membership Application";
    
    // Create email body
    $message = "
    <html>
    <head>
        <title>New Membership Application</title>
    </head>
    <body>
        <h2>New Membership Application Details</h2>
        <table>
            <tr>
                <td><strong>Name:</strong></td>
                <td>$name</td>
            </tr>
            <tr>
                <td><strong>Email:</strong></td>
                <td>$email</td>
            </tr>
            <tr>
                <td><strong>Phone:</strong></td>
                <td>$phone</td>
            </tr>
            <tr>
                <td><strong>Age:</strong></td>
                <td>$age</td>
            </tr>
            <tr>
                <td><strong>Background:</strong></td>
                <td>$background</td>
            </tr>
            <tr>
                <td><strong>Interests:</strong></td>
                <td>$interests</td>
            </tr>
            <tr>
                <td><strong>Submitted on:</strong></td>
                <td>" . date("Y-m-d H:i:s") . "</td>
            </tr>
        </table>
    </body>
    </html>
    ";
    
    // Set headers for HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: $name <$email>" . "\r\n";
    
    // Send email
    $mail_sent = mail($to, $subject, $message, $headers);
    
    // Create auto-response email
    $auto_subject = "Thank you for your interest in AI for Good Johannesburg Hub";
    $auto_message = "
    <html>
    <head>
        <title>Thank You for Your Application</title>
    </head>
    <body>
        <h2>Thank You for Your Application!</h2>
        <p>Dear $name,</p>
        <p>Thank you for your interest in joining the AI for Good Johannesburg Hub. We have received your application and will review it shortly.</p>
        <p>Here's a summary of the information you provided:</p>
        <ul>
            <li><strong>Name:</strong> $name</li>
            <li><strong>Email:</strong> $email</li>
            <li><strong>Phone:</strong> $phone</li>
            <li><strong>Age:</strong> $age</li>
            <li><strong>Background:</strong> $background</li>
        </ul>
        <p>We'll be in touch with you soon regarding the next steps. If you have any questions in the meantime, please don't hesitate to contact us at johannesburghub.youngaileaders@gmail.com.</p>
        <p>Best regards,<br>
        AI for Good Johannesburg Hub Team</p>
    </body>
    </html>
    ";
    
    // Set headers for auto-response
    $auto_headers = "MIME-Version: 1.0" . "\r\n";
    $auto_headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $auto_headers .= "From: AI for Good Johannesburg Hub <johannesburghub.youngaileaders@gmail.com>" . "\r\n";
    
    // Send auto-response
    $auto_mail_sent = mail($email, $auto_subject, $auto_message, $auto_headers);
    
    // Create a simple database entry (optional) - uncomment if you have database setup
    /*
    $servername = "localhost";
    $username = "your_db_username";
    $password = "your_db_password";
    $dbname = "your_database";
    
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
    } else {
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO applicants (name, email, phone, age, background, interests) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiss", $name, $email, $phone, $age, $background, $interests);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }
    */
    
    // Redirect based on email sending status
    if ($mail_sent) {
        header("Location: index.html?status=success&message=" . urlencode("Thank you for your application! We'll be in touch soon."));
    } else {
        header("Location: index.html?status=error&message=" . urlencode("There was a problem sending your application. Please try again or contact us directly."));
    }
    exit;
} else {
    // If the form wasn't submitted using POST method, redirect to the main page
    header("Location: index.html");
    exit;
}
?>
