
<?php
try {
    // Connect to SQLite database using PDO
    $db = new PDO('sqlite:c:\\xampp\\htdocs\\st_trendnest\\products.db');


    /// Set the PDO error mode to exception
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $db->prepare('
    INSERT INTO products (name, description, price, stock, image_url)
    VALUES (:name, :description, :price, :stock, :image_url)
');
	
$products = [
    ['name' => 'Red dress', 'description' => 'Classy dress', 'price' => 19.99, 'stock' => 10, 'image_url' => 'https://media.istockphoto.com/id/186543936/photo/fashion-in-new-york-city.jpg?s=612x612&w=0&k=20&c=iuvNG1M5Kgqp7PilQ8FpwgkUS0pr5ok7EdwXu5bCHG0='],
    ['name' => 'Mermaid dress', 'description' => 'African mommy dress', 'price' => 29.99, 'stock' => 15, 'image_url' => 'https://www.xdressy.com/uploads/product/1/6/16751/unique-blue-sequin-cut-out-mermaid-black-girl-prom-gown-1-thumb.webp'],
    ['name' => 'African mermaid dress', 'description' => 'African mommy dress', 'price' => 9.99, 'stock' => 50, 'image_url' => 'https://i.etsystatic.com/25903201/r/il/d90c72/3029630660/il_570xN.3029630660_rxlw.jpg'],
    ['name' => 'Black dress', 'description' => 'Attractive stylish dress', 'price' => 19.99, 'stock' => 10, 'image_url' => 'https://i.pinimg.com/736x/5c/45/a6/5c45a6dd979fb779a15286187d8a4a61.jpg'],
    ['name' => 'Large capacity bag', 'description' => 'Waterproof travel bag', 'price' => 29.99, 'stock' => 15, 'image_url' => 'https://i.ebayimg.com/images/g/6kIAAOSwkaBj6FYY/s-l1200.jpg'],
    ['name' => 'Leather bag', 'description' => 'Overnight leather bag', 'price' => 9.99, 'stock' => 50, 'image_url' => 'https://cluci.com/cdn/shop/files/1_9855f751-ec16-4bf3-b0b8-ee946a34dfe0.jpg?v=1700299217&width=2'],
    ['name' => 'Lingerie', 'description' => 'Unlined mesh', 'price' => 9.99, 'stock' => 50, 'image_url' => 'https://i.ebayimg.com/images/g/VZwAAOSw435kJnTS/s-l1200.jpg'],
    ['name' => 'Push-up Bra', 'description' => 'Wired push-up bra', 'price' => 29.99, 'stock' => 15, 'image_url' => 'https://ae-pic-a1.aliexpress-media.com/kf/Sb90ce619910c459c8b6deb58b6532795F/Sexy-Lingerie-Comfort-Women-Set-Push-Up-Bra-Victoria-s-Secret-Lingerie-Set-Female-2-Piece.jpg_640x640Q90.jpg_.webp'],
    ['name' => 'Indie Lingerie', 'description' => 'Push-up indie lingerie', 'price' => 9.99, 'stock' => 50, 'image_url' => 'https://media.self.com/photos/5a56742b8e04125cad2cba19/1:1/w_4193,h_4193,c_limit/TIMPA_16449_NeonPink_0.jpg'],
];
// Loop through the products array and insert each product
foreach ($products as $product) {
    $stmt->bindValue(':name', $product['name'], SQLITE3_TEXT);
    $stmt->bindValue(':description', $product['description'], SQLITE3_TEXT);
    $stmt->bindValue(':price', $product['price'], SQLITE3_FLOAT);
    $stmt->bindValue(':stock', $product['stock'], SQLITE3_INTEGER);
    $stmt->bindValue(':image_url', $product['image_url'], SQLITE3_TEXT);

    // Execute the prepared statement
    if ($stmt->execute()) {
        echo "Product " . $product['name'] . " inserted successfully!<br>";
    } else {
        echo "Error inserting product: " . $db->errorInfo()[2] . "<br>";
    }
}


// Close the database connection
$db = null;
} catch (PDOException $e) {
    // If an exception occurs, it will be caught here
    echo "Error: " . $e->getMessage();  // Display the error message
}

?>