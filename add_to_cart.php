<?php
session_start();
include('db.php');  // Include the database connection
include('cart_helper.php');  // Include the helper file

// Check if product ID and quantity are set
if (!isset($_POST['product_id'], $_POST['quantity'])) {
    die("Invalid request.");
}

$product_id = intval($_POST['product_id']);
$quantity = intval($_POST['quantity']);

// Ensure quantity is valid
if ($quantity < 1) {
    die("Quantity must be at least 1.");
}

try {
    // Fetch product details (name, price) from the database
    $query = "SELECT id, name, price FROM products WHERE id = :product_id";
    $stmt = $dbConn->prepare($query);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        die("Product not found.");
    }

    // Check if user is logged in
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        // Check if product already exists in the user's database cart
        $cartQuery = "SELECT quantity FROM cart WHERE user_id = :user_id AND product_id = :product_id";
        $cartStmt = $dbConn->prepare($cartQuery);
        $cartStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $cartStmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $cartStmt->execute();
        $cartItem = $cartStmt->fetch(PDO::FETCH_ASSOC);

        if ($cartItem) {
            // Update quantity if the product is already in the cart
            $updateQuery = "UPDATE cart SET quantity = quantity + :quantity WHERE user_id = :user_id AND product_id = :product_id";
            $updateStmt = $dbConn->prepare($updateQuery);
            $updateStmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $updateStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $updateStmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $updateStmt->execute();
        } else {
            // Insert new cart item if it doesn't exist
            $insertQuery = "INSERT INTO cart (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)";
            $insertStmt = $dbConn->prepare($insertQuery);
            $insertStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $insertStmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $insertStmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $insertStmt->execute();
        }
    } else {
        // Guest user: Use session to store cart data
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$product_id])) {
            // Update quantity if the product already exists in session cart
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            // Add new product to session cart
            $_SESSION['cart'][$product_id] = [
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $quantity
            ];
        }
    }

    header("Location: cart.php"); // Redirect to cart page
    exit();
} catch (PDOException $e) {
    die("Error adding to cart: " . $e->getMessage());
}