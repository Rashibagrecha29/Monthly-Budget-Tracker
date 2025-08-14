<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    die("Access denied.");
}

$user_id = $_SESSION['user_id'];

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="expenses.csv"');

$output = fopen('php://output', 'w');

// CSV Header
fputcsv($output, ['Date', 'Category', 'Amount', 'Description', 'Payment Mode']);

// Fetch expenses
$query = "SELECT e.expense_date, c.name AS category, e.amount, e.description, e.payment_mode 
          FROM Expenses e
          JOIN ExpenseCategories c ON e.category_id = c.category_id
          WHERE e.user_id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
exit;
?>
