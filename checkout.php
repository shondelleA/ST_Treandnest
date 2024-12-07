<?php
session_start();
include('db.php');
include('cart_helper.php'); //include helper function

// Initialize cart variables
$cart_items = [];
$total_price = 0;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $cart_items = getCartItems($dbConn, $user_id);  // Fetch cart items from the database
} else {
    // Guest user: Use session cart
    $cart_items = $_SESSION['cart'] ?? [];
}
// determine if the user is logged in
//$user_id = $_SESSION['user_id'] ?? null;

//initialize cart variables

//$cart_items = getCartItems($dbConn, $user_id);

//retrieve the total from the session
if (!empty($cart_items)) {
    $total_price = calculateCartTotal($cart_items);
} else {
    echo "Your cart is empty.";
    header("Location: cart.php");
    exit();
}
//$total=$_SESSION['total'] ?? calculateCartTotal($cart_items);
$_SESSION['total']=$total_price;




//if (empty($cart_items)) {
   // echo "Your cart is empty.";
   // header('Location: cart.php');
   // exit();
//}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $dbConn->beginTransaction();

        // If the user is logged in, insert the order with their user_id; otherwise, insert with NULL user_id for guests
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        } else {
            $user_id = NULL;  // For guests, user_id is NULL
        }

        // Insert the order (NULL user_id for guests)
        $orderQuery = "INSERT INTO orders (user_id, total_amount, order_date) VALUES (:user_id, :total, strftime('%Y-%m-%d %H:%M:%S', 'now'))";
        $orderStmt = $dbConn->prepare($orderQuery);
        $orderStmt->bindValue(':user_id', $user_id, $user_id ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $orderStmt->bindParam(':total', $total_price);
        $orderStmt->execute();

        $order_id = $dbConn->lastInsertId();
        //$_SESSION['order_id']=$order_id;

        // Insert order items
        //foreach ($cart_items as $item) {
        foreach ($cart_items as $product_id => $item) {
            $orderItemQuery = "INSERT INTO order_items (order_id, product_id, quantity, price)
                               VALUES (:order_id, :product_id, :quantity, :price)";
            $orderItemStmt = $dbConn->prepare($orderItemQuery);
            $orderItemStmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
            $orderItemStmt->bindParam(':product_id', $item['product_id'], PDO::PARAM_INT);
            $orderItemStmt->bindParam(':quantity', $item['quantity'], PDO::PARAM_INT);
            $orderItemStmt->bindParam(':price', $item['price']);
            $orderItemStmt->execute();
        }

        // Clear the cart (Session or DB)
        //clearCart($dbConn, $user_id);

        $dbConn->commit();
        // Clear the cart (for both logged-in users and guests)
        if (isset($_SESSION['user_id'])) {
            clearCart($dbConn, $user_id);  // Clear user cart from DB
        } else {
            unset($_SESSION['cart']);  // Clear guest cart from session
            unset($_SESSION['total']);  // Clear total session variable
        }
        //$dbConn->commit();

         // Send order confirmation email
         if (sendOrderConfirmationEmail($order_id, $total_price, $cart_items)) {
            echo "Order placed successfully! Confirmation email sent.";
        } else {
            echo "Order placed successfully, but confirmation email failed.";
        }
        header('Location: order_success_page.php?order_id='. $order_id);
        exit();
    } catch (PDOException $e) {
        if ($dbConn->inTransaction()) {
            $dbConn->rollBack();
        }
        die("Error: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - ST Trendnest</title>
</head>
<body>
<h1>Checkout</h1>
<p>Total Amount: $<?php echo number_format($total_price, 2); ?></p>
<form method="POST" action="checkout.php">
    <button type="submit">Confirm Order</button>
</form>
</body>
</html>