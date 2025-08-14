<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include 'db_connect.php';

$userId       = $_SESSION['user_id'];
$currentMonth = date('Y-m');

// Fetch budget
$budRes = $conn->query("
    SELECT amount 
    FROM Budget 
    WHERE user_id = $userId 
      AND month = '$currentMonth'
    LIMIT 1
");
$budget = $budRes->num_rows ? $budRes->fetch_assoc()['amount'] : 0;

// Fetch total spent
$spentRes = $conn->query("
    SELECT SUM(amount) AS total_spent 
    FROM Expenses 
    WHERE user_id = $userId 
      AND DATE_FORMAT(expense_date, '%Y-%m') = '$currentMonth'
");
$spentRow = $spentRes->fetch_assoc();
$totalSpent = $spentRow['total_spent'] ?: 0;

// Fetch category breakdown
$catRes = $conn->query("
    SELECT c.name, SUM(e.amount) AS total
    FROM Expenses e
    JOIN ExpenseCategories c 
      ON e.category_id = c.category_id
    WHERE e.user_id = $userId
      AND DATE_FORMAT(e.expense_date, '%Y-%m') = '$currentMonth'
    GROUP BY c.name
    ORDER BY total DESC
");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Monthly Summary</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <p><a href="dashboard.php">← Back to Dashboard</a></p>
  <h2>Summary for <?= date('F Y') ?></h2>

  <p>
    <strong>Budget:</strong> ₹<?= number_format($budget, 2) ?><br>
    <strong>Total Spent:</strong> ₹<?= number_format($totalSpent, 2) ?><br>
    <strong>Remaining:</strong> ₹<?= number_format($budget - $totalSpent, 2) ?>
  </p>

  <h3>Spending by Category</h3>

  <p>
    <a href="export_csv.php" style="display: inline-block; padding: 8px 15px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px;">
      Download CSV
    </a>
  </p>

  <table border="1" cellpadding="8" cellspacing="0">
    <tr>
      <th>Category</th>
      <th>Amount (₹)</th>
    </tr>
    <?php while ($row = $catRes->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= number_format($row['total'], 2) ?></td>
      </tr>
    <?php endwhile; ?>
  </table>
</body>
</html>
