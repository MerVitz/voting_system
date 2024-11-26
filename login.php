<?php
// Include the User class and database configuration
include './classes/user.php';
$config = include 'db.config.php';

// Initialize the database connection
try {
    $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4";
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Initialize variables for feedback
$error = "";

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Use the User class to authenticate the user
    $user = new User($pdo);
    if ($user->authenticateUser($username, $password)) {
        // Successful authentication
        session_start();
        $_SESSION['username'] = $username; // Store user session
        echo "Login successful! Redirecting...";
        header('Location: dashboard.php'); // Redirect to dashboard
        exit();
    } else {
        $error = "Invalid username or password. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form action="" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? Register <a href="register.php">here</a></p>
    </div>
</body>
</html>
