<?php
try{
    
    // Create a database connection
    $dbConn = new PDO('sqlite:C:/xampp/htdocs/st_trendnest/st_trendnest.db');
    $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
?>