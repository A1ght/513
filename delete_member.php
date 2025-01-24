<?php

$dbname = "513.db";

$conn = new SQLite3($dbname);

$customerId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($customerId) {
    $stmt = $conn->prepare("DELETE FROM customers WHERE id = :customerId");
    $stmt->bindValue(':customerId', $customerId, SQLITE3_INTEGER);
    if ($stmt->execute()) {
        header("Location: admin.php"); 
        exit();
    } else {
        echo "<p>Error deleting member: " . $stmt->lastErrorMsg() . "</p>";
    }
}

$conn->close();
?>