<?php
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../src/config/db.php';

// Check if user is admin
if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access.'
    ]);
    exit;
}

// Check if in development mode
$is_development = strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false ||
                 strpos($_SERVER['HTTP_HOST'] ?? '', '127.0.0.1') !== false;

if (!$is_development) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Email logs are only available in development mode.'
    ]);
    exit;
}

try {
    $emails_dir = __DIR__ . '/../../../emails_sent';
    $emails = [];

    if (is_dir($emails_dir)) {
        $files = array_reverse(scandir($emails_dir));
        
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'json') {
                $file_path = $emails_dir . '/' . $file;
                $content = file_get_contents($file_path);
                $email_data = json_decode($content, true);
                
                if ($email_data) {
                    $emails[] = $email_data;
                }
            }
        }
    }

    echo json_encode([
        'success' => true,
        'emails' => $emails,
        'total' => count($emails)
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Unable to read email logs.',
        'error' => $e->getMessage()
    ]);
}
?>
