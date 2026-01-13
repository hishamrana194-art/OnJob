<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
    <h2>Reset Your Password</h2>
    <p>Hi,</p>
    <p>You requested a password reset for your OnJob account.</p>
    <p>
        Click the link below to reset your password:
    </p>
    <p>
        <a href="{{ url('/reset-password?token=' . $token . '&email=' . $email) }}">
            Reset Password
        </a>
    </p>
    <p>If you did not request a password reset, please ignore this email.</p>
</body>
</html>
