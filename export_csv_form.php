<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Export Expenses (CSV)</title>
</head>
<body>
    <h2>Export Expenses by Date Range</h2>
    <form action="export_csv.php" method="get">
        <label>Start Date:
            <input type="date" name="start_date" required>
        </label><br><br>
        <label>End Date:
            <input type="date" name="end_date" required>
        </label><br><br>
        <button type="submit">Export CSV</button>
    </form>
    <p><a href="view_expenses.php">← Back to Expenses</a></p>
</body>
</html>
