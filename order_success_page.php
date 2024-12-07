<?php
session_start();
$order_id = $_GET['order_id'] ?? null;

if (!$order_id){
    header('Location:index.php'); //redirect to home if no order ID
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Success</title>
</head>
<body>
    <h1>Order Confirmed!</h1>
    <p>Your order has been placed successfully.</p>
    <p>Order ID is: <strong><?php echo htmlspecialchars($order_id); ?></strong></p>
    <a href="shop.php">continue Shopping</a>
</body>
</html>