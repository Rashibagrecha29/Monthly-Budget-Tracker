<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$current_month = $_GET['month'] ?? date('Y-m');

// Fetch available months from Expenses table for dropdown
$months_result = $conn->prepare("SELECT DISTINCT DATE_FORMAT(expense_date, '%Y-%m') AS month FROM Expenses WHERE user_id = ? ORDER BY month DESC");
$months_result->bind_param("i", $user_id);
$months_result->execute();
$months = $months_result->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch expenses for selected month
$expenses_stmt = $conn->prepare("SELECT expense_date, amount, description FROM Expenses WHERE user_id = ? AND DATE_FORMAT(expense_date, '%Y-%m') = ?");
$expenses_stmt->bind_param("is", $user_id, $current_month);
$expenses_stmt->execute();
$expenses_result = $expenses_stmt->get_result();

$total_monthly_spent = 0;
$expenses = [];
while ($row = $expenses_result->fetch_assoc()) {
    $total_monthly_spent += $row['amount'];
    $expenses[] = $row;
}

// Fetch the budget for that month
$budget_stmt = $conn->prepare("SELECT amount FROM Budget WHERE user_id = ? AND month = ?");
$budget_stmt->bind_param("is", $user_id, $current_month);
$budget_stmt->execute();
$budget_result = $budget_stmt->get_result()->fetch_assoc();
$monthly_budget = $budget_result['amount'] ?? 0.00;

$remaining_budget = $monthly_budget - $total_monthly_spent;
?>

<!DOCTYPE html>
<html>
<head>
    <title>📅 Monthly Report</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            background: #fff4f0;
            border-radius: 10px;
        }
        h2 {
            text-align: center;
            color: #e76f51;
        }
        select, button {
            padding: 8px;
            margin: 10px 0;
            border-radius: 4px;
            width: 100%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #ccc;
            text-align: left;
        }
        .total {
            font-weight: bold;
            margin-top: 10px;
            text-align: right;
        }
        .dark-toggle {
            float: right;
            padding: 6px 10px;
            background: #444;
            color: #ffb997;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }
        .back-btn {
            display: inline-block;
            margin-bottom: 15px;
            text-decoration: none;
            background: #f4a261;
            color: white;
            padding: 6px 10px;
            border-radius: 4px;
            font-weight: bold;
        }
        .back-btn:hover {
            background: #e76f51;
        }
        body.dark-mode {
            background-color: #121212;
            color: #eee;
        }
        body.dark-mode .container {
            background-color: #1e1e1e;
        }
        body.dark-mode table {
            background-color: #1e1e1e;
            color: #ddd;
        }
        body.dark-mode th, body.dark-mode td {
            border-color: #444;
        }
        body.dark-mode select, 
        body.dark-mode button {
            background-color: #333;
            color: #ffb997;
        }
    </style>
</head>
<body>
<div class="container">
    <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
    <button class="dark-toggle" onclick="toggleDarkMode()">🌓</button>

    <h2>Monthly Report</h2>

    <form method="GET">
        <label for="month">Select Month:</label>
        <select name="month" onchange="this.form.submit()">
            <?php foreach ($months as $m): ?>
                <option value="<?= $m['month'] ?>" <?= $m['month'] === $current_month ? 'selected' : '' ?>>
                    <?= $m['month'] ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <div class="total">Budget: ₹<?= number_format($monthly_budget, 2) ?></div>
    <div class="total">Total Spent: ₹<?= number_format($total_monthly_spent, 2) ?></div>
    <div class="total">Remaining: ₹<?= number_format($remaining_budget, 2) ?></div>

    <table>
        <tr>
            <th>Date</th>
            <th>Description</th>
            <th>Amount (₹)</th>
        </tr>
        <?php if (count($expenses) > 0): ?>
            <?php foreach ($expenses as $exp): ?>
                <tr>
                    <td><?= $exp['expense_date'] ?></td>
                    <td><?= htmlspecialchars($exp['description']) ?></td>
                    <td><?= number_format($exp['amount'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="3" style="text-align:center;">No expenses for this month.</td></tr>
        <?php endif; ?>
    </table>
</div>

<script>
    function toggleDarkMode() {
        document.body.classList.toggle('dark-mode');
        localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
    }

    window.onload = () => {
        if (localStorage.getItem('darkMode') === 'true') {
            document.body.classList.add('dark-mode');
        }
    };
</script>
</body>
</html>
