<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) header("Location: login.php");

$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $limit = $_POST['limit'];
    $stmt = $conn->prepare("REPLACE INTO DailyLimits (user_id, limit_amount) VALUES (?, ?)");
    $stmt->bind_param("id", $_SESSION['user_id'], $limit);
   if ($stmt->execute()) {
    header("Location: add_category.php");
    exit;
}

}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Set Daily Limit</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <button class="dark-toggle" onclick="toggleDarkMode()">🌙</button>
    <h2>📌 Set Daily Limit</h2>
    <a class="back-btn" href="dashboard.php">← Back to Dashboard</a>
    <?php if ($success) echo "<p class='success'>Daily limit saved successfully!</p>"; ?>
    <form method="post">
        <label>Enter Daily Limit (₹):</label>
        <input type="number" name="limit" step="0.01" required>
        <button type="submit">Save Limit</button>
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
