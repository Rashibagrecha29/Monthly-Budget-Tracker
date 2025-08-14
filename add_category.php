<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) header("Location: login.php");

$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $stmt = $conn->prepare("INSERT INTO ExpenseCategories (user_id, name) VALUES (?, ?)");
    $stmt->bind_param("is", $_SESSION['user_id'], $name);
   if ($stmt->execute()) {
    header("Location: add_expense.php");
    exit;
}

}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Category</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <button class="dark-toggle" onclick="toggleDarkMode()">🌙</button>
    <h2>📂 Add Category</h2>
    <a class="back-btn" href="dashboard.php">← Back to Dashboard</a>
    <?php if ($success) echo "<p class='success'>Category added successfully!</p>"; ?>
    <form method="post">
        <label>Category Name:</label>
        <input type="text" name="name" required>
        <button type="submit">Add Category</button>
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
