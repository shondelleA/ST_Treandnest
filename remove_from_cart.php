<?php
session_start();
include('db.php');

if (!isset($_POST['product_id'])) {
    die("Invalid request.");
    //echo "Invalid request.";
    //exit();
}

$product_id = intval($_POST['product_id']);

// Check if user is logged in
$user_id = $_SESSION['user_id'] ?? null;

// Ensure the cart is initialized for guest users
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

try {
    if ($user_id) {
        // Logged-in user: Remove from the database
        $query = "DELETE FROM cart WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = $dbConn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        // Guest user: Remove from the session
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
    }

    echo "Item removed from cart.";
    header('Location: cart.php');
    exit();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>