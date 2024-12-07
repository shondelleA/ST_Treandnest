<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include('db.php');  // Include the database connection

if (isset($_SESSION['user_id'])) {
    // If logged in, redirect to the homepage
    header("Location: homepage.php");
    exit();
}
 $error="";
 if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['usernameOrEmail'])) {
    $usernameOrEmail = trim($_POST['usernameOrEmail']);
    $password = trim($_POST['password']);

    try {
        // Check if the username or email exists
        $query = "SELECT * FROM users WHERE username = :usernameOrEmail OR email = :usernameOrEmail";
        $stmt = $dbConn->prepare($query);
        $stmt->bindParam(':usernameOrEmail', $usernameOrEmail, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Successful login, set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            
            // Redirect to homepage after login
            header("Location: index.php");
            exit();
        } else {
            // Invalid credentials
            $error = "Invalid username/email or password.";
        }
    } catch (PDOException $e) {
        // Database error
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
    <title>Login - ST TRENDNEST</title>
</head>
<body>
<header>
    <nav class="navigation">
        <a href="index.php">Home</a>
        <a href="registration.php">Register</a>
    </nav>
</header>

<main>
    <h3>Login to Your Account</h3>
    <form action="login.php" method="POST">
        <label for="username">Username or Email:</label>
        <input type="text" id="username" name="usernameOrEmail" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Login</button>
    </form>
    
    <?php if (!empty($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
</main>

</body>
</html>

 