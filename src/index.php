<?php
require_once 'functions.php';

// State variables
$registration_message = '';
$verification_message = '';

// Handle registration form
if (isset($_POST['email'])) {
    $email = trim($_POST['email']);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $code = generateVerificationCode();
        file_put_contents(__DIR__ . '/.verification_' . md5($email), $code);
        sendVerificationEmail($email, $code);
        $registration_message = 'Verification code sent to your email.';
    } else {
        $registration_message = 'Invalid email address.';
    }
}

// Handle verification form
if (isset($_POST['verification_code']) && isset($_POST['verify_email'])) {
    $email = trim($_POST['verify_email']);
    $code = trim($_POST['verification_code']);
    $stored_code = @file_get_contents(__DIR__ . '/.verification_' . md5($email));
    if ($stored_code && $code === $stored_code) {
        registerEmail($email);
        unlink(__DIR__ . '/.verification_' . md5($email));
        $verification_message = 'Email verified and registered!';
    } else {
        $verification_message = 'Invalid verification code.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register for GitHub Timeline Updates</title>
</head>
<body>
    <h2>Register Email</h2>
    <form method="post">
        <input type="email" name="email" required>
        <button id="submit-email">Submit</button>
    </form>
    <p><?php echo htmlspecialchars($registration_message); ?></p>

    <h2>Verify Email</h2>
    <form method="post">
        <input type="email" name="verify_email" required placeholder="Enter your email">
        <input type="text" name="verification_code" maxlength="6" required placeholder="Enter verification code">
        <button id="submit-verification">Verify</button>
    </form>
    <p><?php echo htmlspecialchars($verification_message); ?></p>

    <h2>Unsubscribe</h2>
    <form action="unsubscribe.php" method="get">
        <input type="email" name="unsubscribe_email" required>
        <button id="submit-unsubscribe">Unsubscribe</button>
    </form>
</body>
</html>
