<?php
// cart_helper.php

function getCartItems($dbConn, $user_id ) {
    if ($user_id !== null) {
        // Fetch cart items from the database for logged-in users
        $query = "SELECT c.product_id, p.name, p.price, c.quantity
                  FROM cart c
                  INNER JOIN products p ON c.product_id = p.id
                  WHERE c.user_id = :user_id";
        $stmt = $dbConn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
     } elseif (isset($_SESSION['guest_cart'])) {
        $cart=$_SESSION['guest_cart'];
        foreach ($cart as $index => $item) {
            if (!isset($item['product_id'])) {
                error_log("Missing product_id in cart item at index $index: " . print_r($item, true));
                unset($cart[$index]); // Remove the invalid item
            } else {
                // Ensure each item has the proper structure
                $cart[$index]['quantity'] = $item['quantity'] ?? 1;  // Default quantity to 1 if missing
                $cart[$index]['name'] = $item['name'] ?? 'Unknown Product';  // Default name if missing
                $cart[$index]['price'] = $item['price'] ?? 0;  // Default price if missing
            }
        }

        return array_values($cart); // Re-index array to fix potential gaps
    }
    return [];
}


function calculateCartTotal($cart_items) {
        $total = 0;
        foreach ($cart_items as $item) {
            // Check if 'price' and 'quantity' are properly set before calculating the total
            if (isset($item['price'], $item['quantity']) && is_numeric($item['price']) && is_numeric($item['quantity'])) {
                $total += $item['price'] * $item['quantity'];
            } else {
                error_log("Missing or invalid price/quantity for product_id: " . (isset($item['product_id']) ? $item['product_id'] : 'unknown'));
            }
        }
        return $total;
    }
    

function clearCart($dbConn, $user_id = null) {
    if ($user_id) {
        // Clear cart from database
        $query = "DELETE FROM cart WHERE user_id = :user_id";
        $stmt = $dbConn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        // Clear cart from session
        unset($_SESSION['guest_cart']);
    }
}
function addToCart($dbConn, $user_id, $product_id, $quantity) {
    // If the user is logged in
    if ($user_id !== null) {
        // Check if the item already exists in the cart
        $query = "SELECT quantity FROM cart WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = $dbConn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        $existing_item = $stmt->fetch(PDO::FETCH_ASSOC);

        // If the item exists, update the quantity, else insert a new record
        if ($existing_item) {
            $new_quantity = $existing_item['quantity'] + $quantity;
            $update_query = "UPDATE cart SET quantity = :quantity WHERE user_id = :user_id AND product_id = :product_id";
            $update_stmt = $dbConn->prepare($update_query);
            $update_stmt->bindParam(':quantity', $new_quantity, PDO::PARAM_INT);
            $update_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $update_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $update_stmt->execute();
        } else {
            // If the item doesn't exist in the cart, insert it
            $insert_query = "INSERT INTO cart (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)";
            $insert_stmt = $dbConn->prepare($insert_query);
            $insert_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $insert_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $insert_stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $insert_stmt->execute();
        }
    } else {
        // For guest users, store cart in the session
        if (!isset($_SESSION['guest_cart'])) {
            $_SESSION['guest_cart'] = [];
        }

        // Check if the item already exists in the guest cart
        $found = false;
        foreach ($_SESSION['guest_cart'] as &$item) {
            if ($item['product_id'] === $product_id) {
                $item['quantity'] += $quantity;
                $found = true;
                break;
            }
        }

        // If the item was not found, add it to the guest cart
        if (!$found) {
            $_SESSION['guest_cart'][] = [
                'product_id' => $product_id,
                'quantity' => $quantity
            ];
        }
    }
}

function sendOrderConfirmationEmail($order_id, $total_price, $cart_items, $recipient_email = 'taylorshondelle@gmail.com') {
    $subject = 'New Order Confirmation';
    $message = "You have received a new order.\n\n" .
               "Order ID: $order_id\n" .
               "Total Amount: $" . number_format($total_price, 2) . "\n\n" .
               "Items:\n";

    foreach ($cart_items as $item) {
        $message .= "- " . $item['product_name'] . " (Qty: " . $item['quantity'] . ", Price: $" . number_format($item['price'], 2) . ")\n";
    }

    $headers = "From: no-reply@yourdomain.com\r\n" .
               "Reply-To: no-reply@yourdomain.com\r\n" .
               "X-Mailer: PHP/" . phpversion();

    if (mail($recipient_email, $subject, $message, $headers)) {
        return true; // Email sent successfully
    } else {
        return false; // Email sending failed
    }
}
     
?>