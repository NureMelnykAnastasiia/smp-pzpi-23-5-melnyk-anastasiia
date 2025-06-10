<?php
session_start();

try {
    $pdo = new PDO('sqlite:shop.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        price DECIMAL(10,2) NOT NULL
    )");
    
    $check = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    if ($check == 0) {
        $pdo->exec("INSERT INTO products (name, price) VALUES 
            ('Молоко пастеризоване', 12.00),
            ('Хліб чорний', 9.00),
            ('Сир білий', 21.00),
            ('Сметана 20%', 25.00),
            ('Кефір 1%', 19.00),
            ('Вода газована', 18.00),
            ('Печиво \"Весна\"', 14.00)");
    }
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

function getAllProducts($pdo) {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProductById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function checkUserAuth() {
    return isset($_SESSION['user_login']) && isset($_SESSION['login_time']);
}

function requireLogin() {
    if (!checkUserAuth()) {
        header('Location: index.php?page=login');
        exit;
    }
}
?>