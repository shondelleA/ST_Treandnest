<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: index.php"); // Redirect to login if not authenticated
  exit();
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage - Welcome <?php echo htmlspecialchars($_SESSION['username']); ?></title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to a CSS file -->
</head>
<body>
    <header>
        <nav class="navigation">
            <a href="index.php">Home</a>
            <a href="products.php">Shop</a>
            <a href="cart.php">View Cart</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
            <a href="logout.php">Logout</a>
            <?php else: ?>
        <a href="login.php">Login</a>
        <a href="registration.php">Register</a>
    <?php endif; ?>
        </nav>
    </header>
    <main>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <p>We're glad to have you back. Explore the shop or view your cart.</p>
        <div class="cta-section">
            <a href="products.php" class="cta-button">Shop</a>
            <a href="cart.php" class="cta-button">View Cart</a>
        </div>
    </main>
   

    <footer>
        <p>&copy; 2024 ST TRENDNEST. All rights reserved.</p>
    </footer>
</body>
</html>