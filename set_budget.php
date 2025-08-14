<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) header("Location: login.php");

$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'];
    $month = date('Y-m');
    $stmt = $conn->prepare("REPLACE INTO Budget (user_id, month, amount) VALUES (?, ?, ?)");
    $stmt->bind_param("isd", $_SESSION['user_id'], $month, $amount);
   if ($stmt->execute()) {
    header("Location: set_daily_limit.php");
    exit;
}

}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Set Budget</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <button class="dark-toggle" onclick="toggleDarkMode()">🌙</button>
    <h2>💰 Set Budget</h2>
    <a class="back-btn" href="dashboard.php">← Back to Dashboard</a>
    <?php if ($success) echo "<p class='success'>Budget saved successfully!</p>"; ?>
    <form method="post">
        <label>Enter Monthly Budget (₹):</label>
        <input type="number" name="amount" step="0.01" required>
        <button type="submit">Save Budget</button>
    </form>
</div>
<script>
    const toggleDarkMode = () => {
        document.body.classList.toggle('dark-mode');
        localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
    };
    window.onload = () => {
        if (localStorage.getItem('darkMode') === 'true') {
            document.body.classList.add('dark-mode');
        }
    };
</script>
</body>
</html>
