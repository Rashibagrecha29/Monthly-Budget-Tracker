CREATE DATABASE budget_tracker;
USE budget_tracker;
-- USERS TABLE
CREATE TABLE Users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) UNIQUE,
  email VARCHAR(100),
  password VARCHAR(255),
  role VARCHAR(20) DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- BUDGET TABLE
CREATE TABLE Budget (
  budget_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  month VARCHAR(7),
  amount DECIMAL(10,2),
  FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

-- EXPENSE CATEGORIES TABLE
CREATE TABLE ExpenseCategories (
  category_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  name VARCHAR(100),
  month VARCHAR(7),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

-- EXPENSES TABLE
CREATE TABLE Expenses (
  expense_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  category_id INT,
  amount DECIMAL(10,2),
  description TEXT,
  expense_date DATE,
  payment_mode VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES Users(user_id),
  FOREIGN KEY (category_id) REFERENCES ExpenseCategories(category_id)
);

-- DAILY LIMIT TABLE
CREATE TABLE DailyLimits (
  limit_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  limit_amount DECIMAL(10,2),
  FOREIGN KEY (user_id) REFERENCES Users(user_id)
);
