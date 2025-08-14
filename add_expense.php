<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$categories = [];
$stmt = $conn->prepare("SELECT category_id, name FROM ExpenseCategories WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}

$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $date = $_POST['expense_date'];
    $category_id = $_POST['category_id'];
    $payment_method = $_POST['payment_method'];

    $stmt = $conn->prepare("INSERT INTO Expenses (user_id, amount, description, expense_date, category_id, payment_method)
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("idssis", $user_id, $amount, $description, $date, $category_id, $payment_method);
    if ($stmt->execute()) {
        header("Location: view_expenses.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Expense</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #fff8f2;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
        }

        .container {
            max-width: 500px;
            width: 100%;
            background: #fff4f0;
            padding: 25px;
            border-radius: 10px;
        }

        h2 {
            color: #e76f51;
            text-align: center;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
        }

        input, select, textarea, button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            background-color: #f4a261;
            color: white;
            font-weight: bold;
            margin-top: 20px;
            cursor: pointer;
            border: none;
        }

        button:hover {
            background-color: #e76f51;
        }

        a.back-btn {
            display: inline-block;
            margin-top: 15px;
            background: #f4a261;
            color: white;
            padding: 6px 10px;
            border-radius: 4px;
            text-decoration: none;
        }

        .dark-toggle {
            float: right;
            margin-top: -10px;
            padding: 6px 10px;
            background: #444;
            color: #ffb997;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
        }

        /* Dark Mode */
        body.dark-mode {
            background-color: #121212;
            color: #fff;
        }

        body.dark-mode .container {
            background-color: #1e1e1e;
        }

        body.dark-mode input,
        body.dark-mode select,
        body.dark-mode textarea,
        body.dark-mode button {
            background-color: #333;
            color: #ffb997;
        }

        body.dark-mode a.back-btn {
            background-color: #333;
            color: #ffb997;
        }

        body.dark-mode button:hover,
        body.dark-mode a.back-btn:hover {
            background-color: #555;
        }
    </style>
</head>
<body>
<div class="container">
    <button class="dark-toggle" onclick="toggleDarkMode()">🌙</button>
    <h2>Add New Expense</h2>
    <form method="POST">
        <label>Amount (₹):</label>
        <input type="number" step="0.01" name="amount" required>

        <label>Description:</label>
        <textarea name="description" required></textarea>

        <label>Date:</label>
        <input type="date" name="expense_date" required>

        <label>Category:</label>
        <select name="category_id" required>
            <option value="">-- Select Category --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Payment Method:</label>
        <select name="payment_method" required>
            <option value="">-- Select Method --</option>
            <option value="Cash">Cash</option>
            <option value="Credit Card">Credit Card</option>
            <option value="Debit Card">Debit Card</option>
            <option value="UPI">UPI</option>
            <option value="Net Banking">Net Banking</option>
        </select>

        <button type="submit">Add Expense</button>
    </form>
    <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
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
