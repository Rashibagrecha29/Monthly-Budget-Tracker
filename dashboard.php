<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$current_month = date('Y-m');
$prev_month = date('Y-m', strtotime('first day of -1 month'));

// Current month budget
$budget_stmt = $conn->prepare("SELECT amount FROM Budget WHERE user_id = ? AND month = ?");
$budget_stmt->bind_param("is", $user_id, $current_month);
$budget_stmt->execute();
$this_month_budget = $budget_stmt->get_result()->fetch_assoc()['amount'] ?? 0.00;

// Current month spending
$expense_stmt = $conn->prepare("SELECT SUM(amount) as total FROM Expenses WHERE user_id = ? AND DATE_FORMAT(expense_date, '%Y-%m') = ?");
$expense_stmt->bind_param("is", $user_id, $current_month);
$expense_stmt->execute();
$this_month_spent = $expense_stmt->get_result()->fetch_assoc()['total'] ?? 0.00;
$this_month_remaining = $this_month_budget - $this_month_spent;

// Previous month budget
$prev_budget_stmt = $conn->prepare("SELECT amount FROM Budget WHERE user_id = ? AND month = ?");
$prev_budget_stmt->bind_param("is", $user_id, $prev_month);
$prev_budget_stmt->execute();
$prev_budget = $prev_budget_stmt->get_result()->fetch_assoc()['amount'] ?? 0.00;

// Previous month spending
$prev_exp_stmt = $conn->prepare("SELECT SUM(amount) as total FROM Expenses WHERE user_id = ? AND DATE_FORMAT(expense_date, '%Y-%m') = ?");
$prev_exp_stmt->bind_param("is", $user_id, $prev_month);
$prev_exp_stmt->execute();
$prev_spent = $prev_exp_stmt->get_result()->fetch_assoc()['total'] ?? 0.00;

// Calculate totals
$prev_remaining = max($prev_budget - $prev_spent, 0);
$total_available = $this_month_budget + $prev_remaining;

// Daily limit check
$today = date('Y-m-d');
$today_stmt = $conn->prepare("SELECT SUM(amount) as today_spent FROM Expenses WHERE user_id = ? AND expense_date = ?");
$today_stmt->bind_param("is", $user_id, $today);
$today_stmt->execute();
$today_spent = $today_stmt->get_result()->fetch_assoc()['today_spent'] ?? 0.00;

$limit_stmt = $conn->prepare("SELECT limit_amount FROM DailyLimits WHERE user_id = ?");
$limit_stmt->bind_param("i", $user_id);
$limit_stmt->execute();
$daily_limit = $limit_stmt->get_result()->fetch_assoc()['limit_amount'] ?? 0.00;

$limit_exceeded = ($daily_limit > 0 && $today_spent > $daily_limit);

// Category-wise chart
$cat_stmt = $conn->prepare("
    SELECT EC.name, SUM(E.amount) as total
    FROM Expenses E
    JOIN ExpenseCategories EC ON E.category_id = EC.category_id
    WHERE E.user_id = ? AND DATE_FORMAT(E.expense_date, '%Y-%m') = ?
    GROUP BY EC.name
");
$cat_stmt->bind_param("is", $user_id, $current_month);
$cat_stmt->execute();
$cat_result = $cat_stmt->get_result();

$categories = [];
$totals = [];

while ($row = $cat_result->fetch_assoc()) {
    $categories[] = $row['name'];
    $totals[] = $row['total'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            background: #fff8f2;
            color: #333;
        }

        .container {
            max-width: 900px;
            width: 100%;
        }

        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        h2, h3 {
            color: #ff6b5c;
        }

        .summary {
            margin-bottom: 20px;
        }

        .summary p {
            font-size: 16px;
            margin: 4px 0;
        }

        .alert {
            background: #ffe6e6;
            border: 1px solid red;
            padding: 10px;
            color: red;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            margin-top: 30px;
            background-color: #ffe0c1;
            padding: 15px;
            border-radius: 12px;
        }

        .actions a,
        .dark-toggle {
            padding: 10px 14px;
            background-color: #ff865e;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: background 0.3s, transform 0.2s;
        }

        .actions a:hover,
        .dark-toggle:hover {
            background-color: #ff7043;
            transform: scale(1.05);
        }

        canvas#expenseChart {
            max-width: 400px;
            margin: 20px auto;
        }

        /* Dark Mode */
        body.dark-mode {
            background-color: #1f1f1f;
            color: #e0e0e0;
        }

        body.dark-mode h2, 
        body.dark-mode h3 {
            color: #ffccbc;
        }

        body.dark-mode .alert {
            background: #7f1d1d;
            color: #ffcdd2;
            border: 1px solid #f87171;
        }

        body.dark-mode .actions {
            background-color: #3e2c23;
        }

        body.dark-mode .actions a,
        body.dark-mode .dark-toggle {
            background-color: #ff7043;
            color: #fff;
        }

        body.dark-mode .actions a:hover,
        body.dark-mode .dark-toggle:hover {
            background-color: #ff5722;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header-bar">
        <h2>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
        <button class="dark-toggle" onclick="toggleDarkMode()">🌓 Dark Mode</button>
    </div>

    <h3>Monthly Dashboard (<?= $current_month ?>)</h3>

    <?php if ($limit_exceeded): ?>
        <div class="alert">
            ⚠️ Alert: You've exceeded your daily limit of ₹<?= number_format($daily_limit, 2) ?> today.<br>
            Today's spending: ₹<?= number_format($today_spent, 2) ?>
        </div>
    <?php endif; ?>

    <div class="summary">
        <p><strong>Previous Month's Remaining:</strong> ₹<?= number_format($prev_remaining, 2) ?></p>
        <p><strong>This Month's Budget:</strong> ₹<?= number_format($this_month_budget, 2) ?></p>
        <p><strong>Total Available:</strong> ₹<?= number_format($total_available, 2) ?></p>
        <p><strong>Spent This Month:</strong> ₹<?= number_format($this_month_spent, 2) ?></p>
        <p><strong>Remaining This Month:</strong> ₹<?= number_format($this_month_remaining, 2) ?></p>
    </div>

    <h3>Category-wise Expense Chart</h3>
    <canvas id="expenseChart"></canvas>

    <div class="actions">
        <a href="set_budget.php">💰 Set Budget</a>
        <a href="set_daily_limit.php">📌 Set Daily Limit</a>
        <a href="add_category.php">📂 Add Category</a>
        <a href="add_expense.php">➕ Add Expense</a>
        <a href="view_expenses.php">📄 View Expenses</a>
        <a href="monthly_report.php">📅 Monthly Report</a>
        <a href="expense_trends.php">📈 Expense Trends</a>
        <a href="edit_profile.php">👤 Edit Profile</a>
        <a href="logout.php">🚪 Logout</a>
    </div>
</div>

<script>
    const ctx = document.getElementById('expenseChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: <?= json_encode($categories) ?>,
            datasets: [{
                data: <?= json_encode($totals) ?>,
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40']
            }]
        }
    });

    function toggleDarkMode() {
        document.body.classList.toggle('dark-mode');
        localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
    }

    window.onload = function () {
        if (localStorage.getItem('darkMode') === 'true') {
            document.body.classList.add('dark-mode');
        }
    };
</script>
</body>
</html>
