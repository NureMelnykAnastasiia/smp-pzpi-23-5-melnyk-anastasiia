<div class="footer">
        <a href="index.php?page=home">Home</a> |
        
        <?php if (isset($_SESSION['user_login'])): ?>
            <a href="index.php?page=products">Products</a> |
            <a href="index.php?page=cart">Cart</a> |
            <a href="index.php?page=profile">Profile</a> |
            <a href="#about">About Us</a>
        <?php else: ?>
            <a href="index.php?page=login">Login</a> |
            <a href="#about">About Us</a>
        <?php endif; ?>
    </div>
</body>
</html>