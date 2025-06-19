<?php
// UNIQUE TEST COMMENT FOR GITHUB COMPARISON

/**
 * Generate a 6-digit numeric verification code.
 */
function generateVerificationCode() {
    return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

/**
 * Send a verification code to an email.
 */
function sendVerificationEmail($email, $code) {
    $subject = 'Your Verification Code';
    $message = "<p>Your verification code is: <strong>$code</strong></p>";
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: no-reply@example.com' . "\r\n";
    return mail($email, $subject, $message, $headers);
}

/**
 * Register an email by storing it in a file.
 */
function registerEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    $emails = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
    if (!in_array($email, $emails)) {
        file_put_contents($file, $email . "\n", FILE_APPEND);
        return true;
    }
    return false;
}

/**
 * Unsubscribe an email by removing it from the list.
 */
function unsubscribeEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) return false;
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $emails = array_filter($emails, function($e) use ($email) { return trim($e) !== trim($email); });
    file_put_contents($file, implode("\n", $emails) . (count($emails) ? "\n" : ""));
    return true;
}

/**
 * Fetch GitHub timeline.
 */
function fetchGitHubTimeline() {
    $url = 'https://www.github.com/timeline';
    $context = stream_context_create([
        'http' => [
            'header' => "User-Agent: PHP"
        ]
    ]);
    $json = @file_get_contents($url, false, $context);
    if ($json === false) return [];
    $data = json_decode($json, true);
    return $data ? $data : [];
}

/**
 * Format GitHub timeline data. Returns a valid HTML sting.
 */
function formatGitHubData($data) {
    $html = '<h2>GitHub Timeline Updates</h2>';
    $html .= '<table border="1">';
    $html .= '<tr><th>Event</th><th>User</th></tr>';
    if (is_array($data)) {
        foreach ($data as $event) {
            $eventType = isset($event['type']) ? htmlspecialchars($event['type']) : 'N/A';
            $user = isset($event['actor']['login']) ? htmlspecialchars($event['actor']['login']) : 'N/A';
            $html .= "<tr><td>$eventType</td><td>$user</td></tr>";
        }
    }
    $html .= '</table>';
    return $html;
}

/**
 * Send the formatted GitHub updates to registered emails.
 */
function sendGitHubUpdatesToSubscribers() {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) return;
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $data = fetchGitHubTimeline();
    $html = formatGitHubData($data);
    foreach ($emails as $email) {
        $unsubscribeUrl = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/unsubscribe.php?email=" . urlencode($email);
        $body = $html . '<p><a href="' . $unsubscribeUrl . '" id="unsubscribe-button">Unsubscribe</a></p>';
        $subject = 'Latest GitHub Updates';
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: no-reply@example.com' . "\r\n";
        mail($email, $subject, $body, $headers);
    }
}
