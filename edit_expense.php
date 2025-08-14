<?php
session_start();
include 'db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$expense_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Fetch current expense details
$stmt = $conn->prepare("SELECT * FROM Expenses WHERE expense_id = ? AND user_id = ?");
$stmt->bind_param("ii", $expense_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$expense = $result->fetch_assoc();

// Handle update form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $date = $_POST['expense_date'];

    $update = $conn->prepare("UPDATE Expenses SET amount = ?, description = ?, expense_date = ? WHERE expense_id = ? AND user_id = ?");
    $update->bind_param("dssii", $amount, $description, $date, $expense_id, $user_id);
    $update->execute();

    header("Location: view_expenses.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Expense</title>
</head>
<body>
  <h2>Edit Expense</h2>
  <form method="POST">
    <label>Amount:</label>
    <input type="number" name="amount" value="<?= $expense['amount'] ?>" required><br><br>

    <label>Description:</label>
    <input type="text" name="description" value="<?= $expense['description'] ?>"><br><br>

    <label>Date:</label>
    <input type="date" name="expense_date" value="<?= $expense['expense_date'] ?>" required><br><br>

    <button type="submit">Update</button>
  </form>
</body>
</html>
