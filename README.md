# Monthly-Budget-Tracker
A personal budget management system built with PHP, MySQL, HTML/CSS, and JavaScript. This responsive web app helps users manage their monthly budget, daily spending limits, and view categorized expense reports — all in a clean and friendly UI with dark mode support.



💸 Budget Tracker Web App

A simple PHP & MySQL-based budget tracking app that allows users to set budgets, track expenses, and view monthly reports.

🚀 Features

- Add, edit, and delete expenses
- Set monthly budget and daily limits
- Monthly reports with summaries
- Dark mode toggle
- Export expenses to CSV
- Category management

🛠️ Tech Stack

- PHP (Vanilla)
- MySQL
- HTML5, CSS3
- JavaScript (for dark mode)
- Hosted locally using XAMPP or PHP built-in server

 🗃️ Database Setup

The database structure includes:
- `Users`
- `Expenses`
- `ExpenseCategories`
- `Budget`
- `DailyLimit`

🔒 **Important:**  
Database connection credentials are stored in `db_connect.php`, which is not included in this repo for security.

Set up the database using MySQL Workbench or phpMyAdmin.

Create your own db_connect.php:
<?php
$conn = new mysqli('localhost', 'your_username', 'your_password', 'budget_tracker');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
