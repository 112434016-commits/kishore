<?php
// config.php - DB connection, auto-create database + tables, helpers
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$DB_HOST = "localhost";
$DB_USER = "root";     // change if needed
$DB_PASS = "";         // change if needed
$DB_NAME = "food_delivery";

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS);
$conn->query("CREATE DATABASE IF NOT EXISTS $DB_NAME");
$conn->select_db($DB_NAME);

$conn->query("CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(30) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(10) NOT NULL,
  city VARCHAR(30) NOT NULL,
  cuisines VARCHAR(100) NOT NULL,
  gender VARCHAR(10) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  items TEXT NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'Active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
)");

// Menu (emoji-based images, no external files needed)
$FOODS = [
  1 => ["Margherita Pizza", 250, "🍕", true],
  2 => ["Veg Burger", 120, "🍔", true],
  3 => ["Chicken Biryani", 220, "🍛", true],
  4 => ["Masala Dosa", 90, "🥞", true],
  5 => ["Pasta Alfredo", 180, "🍝", false],
  6 => ["Chocolate Shake", 110, "🥤", true],
];

function food_img($emoji) {
  $svg = "<svg xmlns='http://www.w3.org/2000/svg' width='200' height='140'><rect width='200' height='140' fill='#fff1d6'/><text x='50%' y='58%' font-size='70' text-anchor='middle' dominant-baseline='middle'>$emoji</text></svg>";
  return "data:image/svg+xml;base64," . base64_encode($svg);
}
function require_login() {
  if (empty($_SESSION['uid'])) { header("Location: index.php"); exit; }
}
function e($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
