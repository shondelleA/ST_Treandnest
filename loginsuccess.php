<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Welcome to ST Trendnest</title>
</head>
<body>
    <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
    <p>You have successfully logged in.</p>
    <form method="post" action="logout.php">
        <button type="submit">Logout</button>
    </form>
</body>
</html>