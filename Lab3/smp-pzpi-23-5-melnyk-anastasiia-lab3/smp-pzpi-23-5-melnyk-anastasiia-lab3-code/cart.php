<?php
require_once 'config.php';

if (isset($_GET['remove']) && isset($_SESSION['cart'])) {
    $remove_id = (int)$_GET['remove'];
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $remove_id) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            break;
        }
    }
    header('Location: cart.php');
    exit;
}

include 'header.php';
?>

<div class="container">
    <h1>Кошик</h1>
    
    <?php if (empty($_SESSION['cart'])): ?>
        <p>Ваш кошик порожній.</p>
        <p><a href="products.php">Перейти до покупок</a></p>
    <?php else: ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Count</th>
                <th>Sum</th>
                <th>Дія</th>
            </tr>
            
            <?php 
            $total = 0;
            foreach ($_SESSION['cart'] as $item): 
                $sum = $item['price'] * $item['count'];
                $total += $sum;
            ?>
            <tr>
                <td><?php echo $item['id']; ?></td>
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td><?php echo number_format($item['price'], 2); ?></td>
                <td><?php echo $item['count']; ?></td>
                <td><?php echo number_format($sum, 2); ?></td>
                <td>
                    <a href="cart.php?remove=<?php echo $item['id']; ?>" 
                       style="color: red; text-decoration: none; font-size: 18px;">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
            
            <tr style="background-color: #f0f0f0; font-weight: bold;">
                <td colspan="4">Total</td>
                <td><?php echo number_format($total, 2); ?></td>
                <td></td>
            </tr>
        </table>
        
        <div style="margin-top: 20px;">
            <button class="btn" onclick="window.location.href='cart.php?clear=1'">Cancel</button>
            <button class="btn" onclick="window.location.href='cart.php?clear=1'; alert='Оплата успішна'" >Pay</button>

        </div>
        
        <?php
        if (isset($_GET['clear'])) {
            unset($_SESSION['cart']);
            header('Location: cart.php');
            exit;
        }
        ?>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>