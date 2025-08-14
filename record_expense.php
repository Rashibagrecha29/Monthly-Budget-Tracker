<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include 'db_connect.php';

$userId       = $_SESSION['user_id'];
$currentMonth = date('Y-m');
$message      = '';

// Fetch categories for dropdown
$catRes = $conn->query("
    SELECT category_id, name 
    FROM ExpenseCategories 
    WHERE user_id = $userId 
      AND month = '$currentMonth'
    ORDER BY name
");

// Handle expense form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $catId  = intval($_POST['category_id']);
    $amount = floatval($_POST['amount']);
    $date   = $_POST['expense_date'];
    $desc   = $conn->real_escape_string(trim($_POST['description']));

    if ($amount > 0 && $date) {
        $conn->query("
            INSERT INTO Expenses 
              (user_id, category_id, amount, description, expense_date)
            VALUES 
              ($userId, $catId, $amount, '$desc', '$date')
        ");
        $message = 'Expense recorded successfully.';
    } else {
        $message = 'Please enter a valid amount and date.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Record Expense</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <p><a href="dashboard.php">← Back to Dashboard</a></p>
  <h2>Record an Expense for <?= date('F Y') ?></h2>

  <?php if ($message): ?>
    <p class="success"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>

  <form method="POST" action="record_expense.php">
    <label>Category</label>
    <select name="category_id" required>
      <?php while ($cat = $catRes->fetch_assoc()): ?>
        <option value="<?= $cat['category_id'] ?>">
          <?= htmlspecialchars($cat['name']) ?>
        </option>
      <?php endwhile; ?>
    </select>

    <label>Amount (₹)</label>
    <input type="number" step="0.01" name="amount" required>

    <label>Date</label>
    <input type="date" name="expense_date" value="<?= date('Y-m-d') ?>" required>

    <label>Description (optional)</label>
    <input type="text" name="description">

    <button type="submit">Save Expense</button>
  </form>
</body>
</html>
