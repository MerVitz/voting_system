<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <form action="register_process.php" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="username">Email:</label>
            <input type="email" id="eamil" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="pasword" name="password" minlength="9" required>
        </form>
    </div>
</body>
</html>