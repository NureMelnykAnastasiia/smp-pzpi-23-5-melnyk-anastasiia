<?php
function getCartQuantity($product_id) {
    if (!isset($_SESSION['cart'])) {
        return 0;
    }
    
    foreach ($_SESSION['cart'] as $item) {
        if ($item['id'] == $product_id) {
            return $item['count'];
        }
    }
    return 0;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $products = getAllProducts($pdo);
    $cart_items = array();
    $valid = true;
    $error_message = '';
    
    foreach ($products as $product) {
        $count_field = 'count_' . $product['id'];
        if (isset($_POST[$count_field]) && $_POST[$count_field] > 0) {
            $count = (int)$_POST[$count_field];
            if ($count > 0) {
                $cart_items[] = array(
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'count' => $count
                );
            }
        }
    }
    
    if (empty($cart_items)) {
        $error_message = 'Перевірте будь ласка введені дані. Оберіть хоча б один товар.';
        $valid = false;
    }
    
    if ($valid) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }
        
        foreach ($cart_items as $item) {
            $found = false;
            foreach ($_SESSION['cart'] as &$cart_item) {
                if ($cart_item['id'] == $item['id']) {
                    $cart_item['count'] = $item['count'];
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $_SESSION['cart'][] = $item;
            }
        }
        header('Location: index.php?page=cart');
        exit;
    }
}
?>

<div class="container">
    <h2>Список товарів</h2>

    <?php if (!empty($error_message)): ?>
        <div class="error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <table>
            <tr>
                <th>ID</th>
                <th>Назва</th>
                <th>Ціна</th>
                <th>Кількість в кошику</th>
                <th>Нова кількість</th>
            </tr>
            
            <?php 
            $products = getAllProducts($pdo);
            foreach ($products as $product): 
                $current_quantity = getCartQuantity($product['id']);
            ?>
            <tr>
                <td><?php echo $product['id']; ?></td>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td><?php echo $product['price']; ?> грн</td>
                <td style="font-weight: bold; color: <?php echo $current_quantity > 0 ? 'green' : 'gray'; ?>">
                    <?php echo $current_quantity > 0 ? $current_quantity : 'Немає'; ?>
                </td>
                <td>
                    <input type="number" 
                           name="count_<?php echo $product['id']; ?>" 
                           min="0" 
                           max="999" 
                           value="<?php echo $current_quantity; ?>"
                           style="width: 80px;">
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        
        <br>
        <input type="submit" name="submit" value="Оновити кошик" class="btn">
    </form>
</div>