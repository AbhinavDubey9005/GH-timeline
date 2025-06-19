<?php
require_once 'functions.php';

$unsubscribe_message = '';
$verify_message = '';

// Handle unsubscribe form
if (isset($_GET['unsubscribe_email'])) {
    $email = trim($_GET['unsubscribe_email']);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $code = generateVerificationCode();
        file_put_contents(__DIR__ . '/.unsubscribe_' . md5($email), $code);
        sendVerificationEmail($email, $code);
        $unsubscribe_message = 'Unsubscribe verification code sent to your email.';
    } else {
        $unsubscribe_message = 'Invalid email address.';
    }
}

// Handle unsubscribe verification form
if (isset($_POST['unsubscribe_verification_code']) && isset($_POST['unsubscribe_email'])) {
    $email = trim($_POST['unsubscribe_email']);
    $code = trim($_POST['unsubscribe_verification_code']);
    $stored_code = @file_get_contents(__DIR__ . '/.unsubscribe_' . md5($email));
    if ($stored_code && $code === $stored_code) {
        unsubscribeEmail($email);
        unlink(__DIR__ . '/.unsubscribe_' . md5($email));
        $verify_message = 'You have been unsubscribed.';
    } else {
        $verify_message = 'Invalid verification code.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Unsubscribe from GitHub Timeline Updates</title>
</head>
<body>
    <h2>Unsubscribe</h2>
    <form method="get">
        <input type="email" name="unsubscribe_email" required>
        <button id="submit-unsubscribe">Unsubscribe</button>
    </form>
    <p><?php echo htmlspecialchars($unsubscribe_message); ?></p>

    <h2>Verify Unsubscription</h2>
    <form method="post">
        <input type="email" name="unsubscribe_email" required placeholder="Enter your email">
        <input type="text" name="unsubscribe_verification_code" required placeholder="Enter code">
        <button id="verify-unsubscribe">Verify</button>
    </form>
    <p><?php echo htmlspecialchars($verify_message); ?></p>
</body>
</html>
