<?php
$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    
    if (empty($username) || empty($password)) {
        $error_message = 'Будь ласка, заповніть всі поля.';
    } else {
        if ($username === $credentials['userName'] && $password === $credentials['password']) {
            $_SESSION['user_login'] = $username;
            $_SESSION['login_time'] = date('Y-m-d H:i:s');
            header('Location: index.php?page=products');
            exit;
        } else {
            $error_message = 'Невірні дані для входу.';
        }
    }
}
?>

<div class="container">
    <h1>Вхід в систему</h1>
    
    <?php if (!empty($error_message)): ?>
        <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>
    
    <?php if (!empty($success_message)): ?>
        <div class="success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <table style="margin: 20px 0;">
            <tr>
                <td><label for="username">Ім'я користувача:</label></td>
                <td>
                    <input type="text" 
                           id="username" 
                           name="username" 
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                           required
                           style="padding: 10px; width: 200px;">
                </td>
            </tr>
            <tr>
                <td><label for="password">Пароль:</label></td>
                <td>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required
                           style="padding: 10px; width: 200px;">
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center; padding-top: 20px;">
                    <input type="submit" name="login" value="Увійти" class="btn">
                </td>
            </tr>
        </table>
    </form>
</div>