<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$query = "SELECT e.expense_id, e.amount, e.description, e.expense_date, e.payment_method, c.name AS category 
          FROM Expenses e 
          JOIN ExpenseCategories c ON e.category_id = c.category_id 
          WHERE e.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Expenses</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            background: #fff4f0;
        }

        .container {
            max-width: 900px;
            width: 100%;
        }

        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        h2 {
            color: #e76f51;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px;
            border: 1px solid #ffddd2;
        }

        th {
            background-color: #f4a261;
            color: white;
        }

        td {
            background-color: #fff;
        }

        a, .btn {
            text-decoration: none;
            padding: 6px 10px;
            background-color: #e76f51;
            color: white;
            border-radius: 4px;
            margin: 5px;
            display: inline-block;
        }

        a:hover, .btn:hover {
            background-color: #d55f44;
        }

        .btn-bar {
            margin-bottom: 15px;
        }

        /* Dark mode */
        body.dark-mode {
            background-color: #121212;
            color: #e0e0e0;
        }

        body.dark-mode table {
            background-color: #1e1e1e;
            color: #ccc;
        }

        body.dark-mode th {
            background-color: #333;
            color: #ffa07a;
        }

        body.dark-mode td {
            background-color: #252525;
        }

        body.dark-mode a, .dark-toggle {
            background-color: #444;
            color: #ffb997;
        }

        body.dark-mode a:hover, .dark-toggle:hover {
            background-color: #555;
        }

        .dark-toggle {
            padding: 6px 10px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header-bar">
        <h2>Your Expenses</h2>
        <button class="dark-toggle" onclick="toggleDarkMode()">🌓 Dark Mode</button>
    </div>

    <div class="btn-bar">
        <a href="dashboard.php">← Back to Dashboard</a>
        <a href="export_expenses.php" class="btn">Export CSV</a>
        <a href="export_csv_form.php" class="btn">Export by Date</a>
    </div>

    <table>
        <tr>
            <th>Date</th>
            <th>Category</th>
            <th>Amount</th>
            <th>Description</th>
            <th>Payment Method</th>
            <th>Actions</th>
        </tr>
        <?php while($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['expense_date'] ?></td>
                <td><?= $row['category'] ?></td>
                <td>₹<?= $row['amount'] ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td><?= htmlspecialchars($row['payment_method']) ?: 'Not specified' ?></td>
                <td>
                    <a href="edit_expense.php?id=<?= $row['expense_id'] ?>">Edit</a> | 
                    <a href="delete_expense.php?id=<?= $row['expense_id'] ?>" onclick="return confirm('Delete this expense?');">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>

<script>
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
