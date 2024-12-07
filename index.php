<?php
session_start();
include ('db.php'); // database connection

//initialize shopping status based on the cart
$startshopping =isset($_SESSION['cart']) && !empty($_SESSION['cart'])? true : false;

$error = "";
$success = "";

try {
    // Check and connect to the unified database
    $st_trendnestDBPath = 'c:\xampp\htdocs\st_trendnest\st_trendnest.db';
    if (!file_exists($st_trendnestDBPath)) {
        die("Database file not found at $st_trendnestDBPath");
    }
    $dbConn = new PDO("sqlite:" . $st_trendnestDBPath);
    $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch products from the database
    $products = $dbConn->query('SELECT * FROM products');
    if (!$products) {
        die("Error fetching products.");
    }

    // Login logic
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['usernameOrEmail'])) {
        $usernameOrEmail = trim($_POST['usernameOrEmail']);
        $password = trim($_POST['password']);

        try {
            $query = "SELECT * FROM users WHERE username = :usernameOrEmail OR email = :usernameOrEmail";
            $stmt = $dbConn->prepare($query);
            $stmt->bindParam(':usernameOrEmail', $usernameOrEmail, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                header("Location: homepage.php"); // Redirect to homepage or user dashboard
                exit();
            } else {
                $error = "Invalid username/email or password.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }

    // Registration logic
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_username'])) {
        $username = trim($_POST['register_username']);
        $email = trim($_POST['register_email']);
        $password = trim($_POST['register_password']);
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT); // Hash the password

        try {
            // Check if username or email already exists
            $checkQuery = "SELECT COUNT(*) FROM users WHERE username = :username OR email = :email";
            $checkStmt = $dbConn->prepare($checkQuery);
            $checkStmt->bindParam(':username', $username);
            $checkStmt->bindParam(':email', $email);
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
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>ST TRENDNEST</title>
</head>
<body>
<header>
    <nav class="navigation">
        <a href="index.php">Home</a>
        <a href="products.php">Shop</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="logout.php">Logout</a>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
            <a href="cart.php">Cart</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="registration.php">Register</a>
        <?php endif; ?>
    </nav>
</header>

<div class="cta-banner-with-image">
    <img src="https://img.freepik.com/premium-photo/woman-wearing-yellow-jacket-sunglasses-is-wearing-sun-glasses_1064589-180964.jpg?semt=ais_hybrid" alt="Special Offer" class="banner-image">
</div>
<div class="cta-content">
    <h2>Limited Time Offer- 20% off!</h2>
    <p>Don't miss out on our biggest sale of the year!</p>
    <a href="products.php" class="cta-button">Shop Now</a>
</div>
<main>
    <section id="login-section">
        <h3>Login to Your Account</h3>
        <form action="index.php" method="POST" autocomplete="on">
            <label for="username">Username:</label>
            <input type="text" id="username" name="usernameOrEmail" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
        <?php if (!empty($error)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
    </section>

    <section id="register-section">
        <h4>Register an Account</h4>
        <form action="index.php" method="POST" autocomplete="on">
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
    </section>

    <section class="featured-products">
        <?php while ($product = $products->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="product">
                <img src="<?php echo $product['image_url']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" width="100" height="100">
                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <p><?php echo htmlspecialchars($product['description']); ?></p>
                <p>Price: $<?php echo isset($product['price']) && is_numeric($product['price'])? number_format($product['price'], 2) : '0.00'; ?></p>
            </div>
        <?php endwhile; ?>
    </section>
</main>

<footer>
    <p>&copy; 2024 ST TRENDNEST. All rights reserved.</p>
</footer>

</body>
</html>