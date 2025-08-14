<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    die("Access denied.");
}

$user_id = $_SESSION['user_id'];

// Get optional date filters
$start = $_GET['start_date'] ?? null;
$end   = $_GET['end_date'] ?? null;

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="expenses.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Date', 'Category', 'Amount', 'Description']);

$sql = "SELECT e.expense_date, c.name AS category, e.amount, e.description 
        FROM Expenses e
        JOIN ExpenseCategories c ON e.category_id = c.category_id
        WHERE e.user_id = ?";

$params = [$user_id];
$types = "i";

// Add date filtering
if ($start && $end) {
    $sql .= " AND e.expense_date BETWEEN ? AND ?";
    $params[] = $start;
    $params[] = $end;
    $types .= "ss";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
exit;
?>
