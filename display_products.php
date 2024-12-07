<?php
try {
    // Connect to SQLite database using PDO
    $db = new PDO('c:\xampp\htdocs\st_trendnest\st_trendnest.sqbpro');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to get all products from the database
    $query = "SELECT * FROM products";
    $stmt = $db->query($query);

    // Fetch the products
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Display the products
    echo "<h1>Our Products</h1>";
    echo "<div class='products-container'>";
    
    foreach ($products as $product) {
        echo "<div class='product'>";
        echo "<img src='" . $product['image_url'] . "' alt='" . $product['name'] . "' />";
        echo "<h2>" . $product['name'] . "</h2>";
        echo "<p>" . $product['description'] . "</p>";
        echo "<p>Price: $" . $product['price'] . "</p>";
        echo "<p>Stock: " . $product['stock'] . " available</p>";
        echo "</div>";
    }

    echo "</div>";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>