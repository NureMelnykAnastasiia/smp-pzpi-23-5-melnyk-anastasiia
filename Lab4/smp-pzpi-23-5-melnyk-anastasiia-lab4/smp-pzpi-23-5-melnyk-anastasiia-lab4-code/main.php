<div class="container">
    <h1>Ласкаво просимо до нашого інтернет-магазину!</h1>
    
    <?php if (isset($_SESSION['user_login'])): ?>
        <div class="success">
            Вітаємо, <?php echo htmlspecialchars($_SESSION['user_login']); ?>! 
            Ви увійшли в систему <?php echo $_SESSION['login_time']; ?>
        </div>
    <?php endif; ?>
    
    <h2>Доступні розділи:</h2>
    <ul>
        <li><a href="index.php?page=products">Сторінка товарів</a> - перегляд та вибір товарів</li>
        <li><a href="index.php?page=cart">Кошик</a> - перегляд обраних товарів</li>
        <li><a href="index.php?page=profile">Профіль</a> - перегляд та редагування профілю</li>
    </ul>
    
    <p>Оберіть розділ для початку покупок!</p>
</div>