<?php
// Start session to manage cart
session_start();
include('db.php');
try {
    // Fetch all products from the database
    $query = "SELECT id, name, description, price, image_url FROM products";
    $stmt = $dbConn->prepare($query);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error fetching products: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - ST Trendnest</title>
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
    <h1>Shop Our Products</h1>
    <section class="products">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="product">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" width="150" height="150">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p><?php echo htmlspecialchars($product['description']); ?></p>
                    <p>Price: $<?php echo number_format($product['price'], 2); ?></p>

                    <!-- Add to Cart Form -->
                    <form method="POST" action="add_to_cart.php">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <label for="quantity_<?php echo $product['id']; ?>">Quantity:</label>
                        <input type="number" id="quantity_<?php echo $product['id']; ?>" name="quantity" value="1" min="1" required>
                        <button type="submit">Add to Cart</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No products available at the moment. Please check back later!</p>
        <?php endif; ?>
    </section>
</main>

<footer>
    <p>&copy; 2024 ST Trendnest. All rights reserved.</p>
</footer>
</body>
</html>