<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get monthly totals
$stmt = $conn->prepare("
    SELECT DATE_FORMAT(expense_date, '%Y-%m') AS month, SUM(amount) AS total
    FROM Expenses
    WHERE user_id = ?
    GROUP BY month
    ORDER BY month
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$months = [];
$totals = [];
while ($row = $result->fetch_assoc()) {
    $months[] = $row['month'];
    $totals[] = $row['total'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>📈 Expense Trends</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        canvas {
            display: block;
            max-width: 100%;
            margin: 30px auto;
        }
        .back-btn, .dark-toggle {
            padding: 6px 10px;
            background: #f4a261;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            margin: 5px;
        }
        .back-btn:hover, .dark-toggle:hover {
            background: #e76f51;
        }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        body.dark-mode {
            background-color: #121212;
            color: #eee;
        }
        body.dark-mode .container {
            background-color: #1e1e1e;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="top-bar">
        <a class="back-btn" href="dashboard.php">← Back to Dashboard</a>
        <button class="dark-toggle" onclick="toggleDarkMode()">🌓 Dark Mode</button>
    </div>
    <h2>Monthly Expense Trends</h2>
    <canvas id="trendChart"></canvas>
</div>

<script>
    const ctx = document.getElementById('trendChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($months) ?>,
            datasets: [{
                label: 'Total Expenses (₹)',
                data: <?= json_encode($totals) ?>,
                backgroundColor: '#f4a261',
                borderColor: '#e76f51',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

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
