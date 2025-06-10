<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Інтернет-магазин</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .header {
            background-color: #333;
            color: white;
            padding: 10px 0;
            text-align: center;
        }
        .header a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
            padding: 5px 10px;
            border: 1px solid #ccc;
            background-color: #555;
        }
        .header a:hover {
            background-color: #777;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border: 2px solid #333;
        }
        .footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px 0;
            margin-top: 20px;
        }
        .footer a {
            color: white;
            text-decoration: none;
            margin: 0 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #333;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #333;
            color: white;
        }
        input[type="number"] {
            width: 50px;
            text-align: center;
        }
        .btn {
            background-color: #333;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            margin: 10px 5px;
        }
        .btn:hover {
            background-color: #555;
        }
        .error {
            color: red;
            background-color: #ffebeb;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid red;
        }
        .success {
            color: green;
            background-color: #ebffeb;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid green;
        }
        .product-item {
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="index.php">Home</a> |
        <a href="products.php">Products</a> |
        <a href="cart.php">Cart</a>
    </div>