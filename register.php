<?php include 'ip_blocker.php'; ?>
<?php
session_start(); // Start session for potential feedback messages
include 'db_connect.php'; // Include your database connection

$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // --- Basic Validation ---
    if (empty($username) || empty($password) || empty($password_confirm)) {
        $error_message = 'Please fill in all fields.';
    } elseif ($password !== $password_confirm) {
        $error_message = 'Passwords do not match.';
    } elseif (strlen($password) < 6) { // Example: Enforce minimum password length
        $error_message = 'Password must be at least 6 characters long.';
    } else {
        // --- Check if username already exists ---
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $error_message = 'Username already taken. Please choose another.';
        } else {
            // --- Username is available, proceed with registration ---
            // Hash the password securely
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Prepare statement to insert new user
            $stmt_insert = $conn->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
            $stmt_insert->bind_param("ss", $username, $password_hash);

            if ($stmt_insert->execute()) {
                $success_message = 'User registered successfully! You can now log in.';
                // Optional: Redirect to login page after successful registration
                // header("Refresh: 3; url=login.php"); // Redirect after 3 seconds
            } else {
                $error_message = 'Error during registration. Please try again. ' . $stmt_insert->error;
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register New User</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .register-container { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 100%; max-width: 450px; }
        h1 { text-align: center; color: #333; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px; }
        .btn { display: block; width: 100%; padding: 12px; border: none; background-color: #28a745; color: white; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold; }
        .message { padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align: center; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .login-link { text-align: center; margin-top: 20px; }
        .login-link a { color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="register-container">
        <h1>Register New User</h1>

        <?php if ($error_message): ?>
            <p class="message error"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <p class="message success"><?= htmlspecialchars($success_message) ?></p>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="password">Password (min. 6 characters)</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="password_confirm">Confirm Password</label>
                <input type="password" id="password_confirm" name="password_confirm" required>
            </div>
            <button type="submit" class="btn">Register</button>
        </form>

        <p class="login-link">Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>