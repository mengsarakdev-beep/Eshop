<?php
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../src/config/db.php';

if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access.'
    ]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?: [];

$contact_id = intval($input['contact_id'] ?? 0);
$to_email = trim($input['to_email'] ?? '');
$to_name = trim($input['to_name'] ?? '');
$subject = trim($input['subject'] ?? '');
$message = trim($input['message'] ?? '');

if (!$contact_id || !$to_email || !$to_name || !$subject || !$message) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields.'
    ]);
    exit;
}

if (!filter_var($to_email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid email address.'
    ]);
    exit;
}

try {
    $pdo = Database::getInstance();

    // Verify the contact message exists
    $stmt = $pdo->prepare('SELECT id FROM contact_messages WHERE id = ?');
    $stmt->execute([$contact_id]);

    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Contact message not found.'
        ]);
        exit;
    }

    // Send email using PHP mail function
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/plain; charset=UTF-8',
        'From: Eshop Admin <noreply@eshop.local>',
        'Reply-To: noreply@eshop.local',
        'X-Mailer: PHP/' . phpversion()
    ];

    $email_body = "Dear {$to_name},\n\n{$message}\n\nBest regards,\nEshop Admin Team\n\n---\nThis is an automated response from your contact form submission.";

    // For development environment, we'll simulate email sending
    // In production, you would configure proper SMTP settings
    $is_development = strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false ||
                     strpos($_SERVER['HTTP_HOST'] ?? '', '127.0.0.1') !== false;

    if ($is_development) {
        // Simulate successful email sending for development
        $mail_sent = true;

        // Log the email to file for viewing
        $emails_dir = __DIR__ . '/../../../emails_sent';
        if (!is_dir($emails_dir)) {
            mkdir($emails_dir, 0755, true);
        }

        $email_log = [
            'timestamp' => date('Y-m-d H:i:s'),
            'to' => $to_email,
            'to_name' => $to_name,
            'subject' => $subject,
            'message' => $message,
            'full_body' => $email_body,
            'contact_id' => $contact_id,
            'admin_id' => $_SESSION['user_id'] ?? null
        ];

        $log_file = $emails_dir . '/email_' . date('Y-m-d_H-i-s_') . uniqid() . '.json';
        file_put_contents($log_file, json_encode($email_log, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        // Also log to PHP error logs
        error_log("DEVELOPMENT MODE: Email sent to {$to_email} | Subject: {$subject} | Log: {$log_file}");
    } else {
        // Production: Use actual mail function
        $mail_sent = mail($to_email, $subject, $email_body, implode("\r\n", $headers));
    }

    if ($mail_sent) {
        // Log the reply in database (optional)
        $stmt = $pdo->prepare('
            INSERT INTO contact_replies (contact_id, admin_id, subject, message, sent_at)
            VALUES (?, ?, ?, ?, NOW())
        ');

        // Create contact_replies table if it doesn't exist
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS contact_replies (
                id INT(11) NOT NULL AUTO_INCREMENT,
                contact_id INT(11) NOT NULL,
                admin_id INT(11) DEFAULT NULL,
                subject VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                sent_at TIMESTAMP NOT NULL DEFAULT current_timestamp(),
                PRIMARY KEY (id),
                KEY idx_contact_replies_contact_id (contact_id),
                KEY idx_contact_replies_sent_at (sent_at),
                CONSTRAINT fk_contact_replies_contact FOREIGN KEY (contact_id) REFERENCES contact_messages (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        $stmt->execute([$contact_id, $_SESSION['user_id'] ?? null, $subject, $message]);

        $success_message = $is_development
            ? 'Reply simulated successfully (development mode). Emails are saved in the /emails_sent folder.'
            : 'Reply sent successfully.';

        echo json_encode([
            'success' => true,
            'message' => $success_message
        ]);
    } else {
        $is_development = strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false ||
                         strpos($_SERVER['HTTP_HOST'] ?? '', '127.0.0.1') !== false;

        if ($is_development) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Email sending failed. Check the /emails_sent folder for logs.'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to send email. Please check mail server configuration.'
            ]);
        }
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Unable to send reply.',
        'error' => $e->getMessage(),
    ]);
}
?>