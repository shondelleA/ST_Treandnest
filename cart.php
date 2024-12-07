<?php
session_start();
include ('db.php');
include ('cart_helper.php');

// Determine if the user is logged in
$user_id = $_SESSION['user_id'] ?? null;

// Initialize cart variables
$cart_items = [];
$cart_message = "Your cart is empty.";
$total_price = 0;

try {
    if ($user_id) {
        // Fetch cart items from the database for logged-in users
        $cart_items = getCartItems($dbConn, $user_id);
    } else if (!empty($_SESSION['cart'])) {
        // Use session-based cart for guests
        $cart_items = $_SESSION['cart'];
    }

    if (!empty($cart_items)) {
        // Calculate total price using helper function
        $total_price = calculateCartTotal($cart_items);

        //store the total in the session for checkout
        $_SESSION['total'] =$total_price;
        
        $cart_message = "";
    }
} catch (PDOException $e) {
    die("Error fetching cart items: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - ST Trendnest</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <nav class="navigation">
        <a href="index.php">Home</a>
        <a href="shop.php">Shop</a>
        <a href="cart.php">Cart</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="logout.php">Logout</a>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="registration.php">Register</a>
        <?php endif; ?>
    </nav>
</header>

<main>
    <h1>Your Cart</h1>

    <?php if (!empty($cart_message)): ?>
        <p><?php echo $cart_message; ?></p>
    <?php else: ?>
        <section class="cart-items">
            <?php foreach ($cart_items as $item): ?>
                <div class="cart-item">
                    <h2><?php echo htmlspecialchars($item['name'] ?? 'unamed Product'); ?></h2>
                    <p>Price: $<?php echo number_format($item['price'] ?? 0, 2); ?></p>
                    <p>Quantity: <?php echo htmlspecialchars($item['quantity'] ?? 0); ?></p>
                    
                    <form method="POST" action="remove_from_cart.php">
                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item['product_id'] ?? 0); ?>">
                        <button type="submit">Remove</button>
                    </form>
                </div>
                <hr>
            <?php endforeach; ?>
        </section>

        <section class="cart-summary">
            <h2>Cart Summary</h2>
            <p>Total Items: <?php echo array_sum(array_column($cart_items, 'quantity')); ?></p>
            <p><strong>Total Price: $<?php echo number_format($total_price, 2); ?></strong></p>
            <!-- Use form for checkout to avoid anchor tag issues -->
            <form method="GET" action="checkout.php">
                <button type="submit">Proceed to Checkout</button>
            </form>
        </section>
    <?php endif; ?>
</main>
<footer>
    <p>&copy; 2024 ST Trendnest. All rights reserved.</p>
</footer>
</body>
</html>