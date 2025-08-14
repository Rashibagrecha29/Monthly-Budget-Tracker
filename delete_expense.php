<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$expense_id = $_GET['id'];

// Ensure only the owner can delete
$stmt = $conn->prepare("DELETE FROM Expenses WHERE expense_id = ? AND user_id = ?");
$stmt->bind_param("ii", $expense_id, $user_id);
$stmt->execute();

header("Location: view_expenses.php");
exit;
?>
