<?php
include('db.php');
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST"&& isset($_POST['username'], $_POST['email'], $_POST['password'])) {
    $username =trim( $_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT); // Hash the password

    try {
        // Step 1: Check if the username or email already exists
        $checkQuery = "SELECT COUNT(*) FROM users WHERE username = :username OR email = :email";
        $checkStmt = $dbConn->prepare($checkQuery);
        $checkStmt->bindParam(':username', $username, PDO::PARAM_STR);
        $checkStmt->bindParam(':email', $email, PDO::PARAM_STR);
        $checkStmt->execute();
        $count = $checkStmt->fetchColumn();

    if ($count > 0) {
        $error = "Username or email is already taken.";
    } else {
        // Insert new user
        $query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $dbConn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->execute();
        $success = "Registration successful. You can now log in.";
    }
// Close connection
} catch (PDOException $e){
  $error = "Database error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Register - ST TRENDNEST</title>
</head>
<body>
<header>
    <nav class="navigation">
        <a href="index.php">Home</a>
        <a href="login.php">Login</a>
    </nav>
</header>

<main>
    <h3>Register an Account</h3>
    <form action="registration.php" method="POST">
        <label for="register_username">Username:</label>
        <input type="text" id="register_username" name="register_username" required>

        <label for="register_email">Email:</label>
        <input type="email" id="register_email" name="register_email" required>

        <label for="register_password">Password:</label>
        <input type="password" id="register_password" name="register_password" required>

        <button type="submit">Register</button>
    </form>
    
    <?php if (!empty($success)): ?>
        <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
</main>

</body>
</html>