<?php
$profile_file = 'profile_data.php';
$profile_data = array(
    'first_name' => '',
    'last_name' => '',
    'birth_date' => '',
    'about' => '',
    'photo' => ''
);

if (file_exists($profile_file)) {
    include $profile_file;
    if (isset($saved_profile)) {
        $profile_data = array_merge($profile_data, $saved_profile);
    }
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_profile'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $birth_date = trim($_POST['birth_date']);
    $about = trim($_POST['about']);
    
    $errors = array();
    
    if (empty($first_name) || empty($last_name) || empty($birth_date) || empty($about)) {
        $errors[] = 'Всі поля повинні бути заповнені.';
    }
    if (strlen($first_name) <= 1 || !preg_match('/^[а-яА-ЯёЁa-zA-Z\s]+$/u', $first_name)) {
        $errors[] = 'Ім\'я повинно містити більше одного символу і складатися тільки з літер.';
    }
    if (strlen($last_name) <= 1 || !preg_match('/^[а-яА-ЯёЁa-zA-Z\s]+$/u', $last_name)) {
        $errors[] = 'Прізвище повинно містити більше одного символу і складатися тільки з літер.';
    }
    
    if (!empty($birth_date)) {
        $birth_timestamp = strtotime($birth_date);
        $current_timestamp = time();
        $age = floor(($current_timestamp - $birth_timestamp) / (365.25 * 24 * 60 * 60));
        
        if ($age < 16) {
            $errors[] = 'Користувачеві має бути не менше 16 років.';
        }
    }
    
    if (strlen($about) < 50) {
        $errors[] = 'Стисла інформація має містити не менше 50 символів.';
    }
    
    $photo_path = $profile_data['photo'];
    
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif');
        $file_type = $_FILES['photo']['type'];
        $file_size = $_FILES['photo']['size'];
        $max_size = 5 * 1024 * 1024;
        
        if (!in_array($file_type, $allowed_types)) {
            $errors[] = 'Фото повинно бути у форматі JPEG, PNG або GIF.';
        }
        
        if ($file_size > $max_size) {
            $errors[] = 'Розмір фото не повинен перевищувати 5MB.';
        }
        
        if (empty($errors)) {
            $upload_dir = 'photos/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $new_filename = 'profile_' . $_SESSION['user_login'] . '_' . time() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                if (!empty($profile_data['photo']) && file_exists($profile_data['photo'])) {
                    unlink($profile_data['photo']);
                }
                $photo_path = $upload_path;
            } else {
                $errors[] = 'Помилка при завантаженні фото.';
            }
        }
    }
    
    if (empty($errors)) {
        $profile_data = array(
            'first_name' => $first_name,
            'last_name' => $last_name,
            'birth_date' => $birth_date,
            'about' => $about,
            'photo' => $photo_path
        );
        
        $php_array = "<?php\n\$saved_profile = " . var_export($profile_data, true) . ";\n?>";
        
        if (file_put_contents($profile_file, $php_array)) {
            $success_message = 'Профіль успішно збережено!';
        } else {
            $error_message = 'Помилка при збереженні профілю.';
        }
    } else {
        $error_message = implode('<br>', $errors);
    }
}
?>

<div class="container">
    <h1>Профіль користувача</h1>
    
    <?php if (!empty($error_message)): ?>
        <div class="error"><?php echo $error_message; ?></div>
    <?php endif; ?>
    
    <?php if (!empty($success_message)): ?>
        <div class="success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <table style="width: 100%; margin: 20px 0;">
            <tr>
                <td style="width: 30%; vertical-align: top; padding: 10px;">
                    <label for="first_name">Ім'я:</label>
                </td>
                <td style="padding: 10px;">
                    <input type="text" 
                           id="first_name" 
                           name="first_name" 
                           value="<?php echo htmlspecialchars($profile_data['first_name']); ?>"
                           required
                           style="width: 300px; padding: 8px;">
                </td>
            </tr>
            
            <tr>
                <td style="vertical-align: top; padding: 10px;">
                    <label for="last_name">Прізвище:</label>
                </td>
                <td style="padding: 10px;">
                    <input type="text" 
                           id="last_name" 
                           name="last_name" 
                           value="<?php echo htmlspecialchars($profile_data['last_name']); ?>"
                           required
                           style="width: 300px; padding: 8px;">
                </td>
            </tr>
            
            <tr>
                <td style="vertical-align: top; padding: 10px;">
                    <label for="birth_date">Дата народження:</label>
                </td>
                <td style="padding: 10px;">
                    <input type="date" 
                           id="birth_date" 
                           name="birth_date" 
                           value="<?php echo htmlspecialchars($profile_data['birth_date']); ?>"
                           required
                           style="width: 300px; padding: 8px;">
                </td>
            </tr>
            
            <tr>
                <td style="vertical-align: top; padding: 10px;">
                    <label for="about">Стисла інформація (мін. 50 символів):</label>
                </td>
                <td style="padding: 10px;">
                    <textarea id="about" 
                              name="about" 
                              required
                              rows="6"
                              style="width: 300px; padding: 8px; resize: vertical;"
                              placeholder="Розкажіть про себе (мінімум 50 символів)..."><?php echo htmlspecialchars($profile_data['about']); ?></textarea>
                    <br><small>Символів: <span id="char-count"><?php echo strlen($profile_data['about']); ?></span></small>
                </td>
            </tr>
            
            <tr>
                <td style="vertical-align: top; padding: 10px;">
                    <label for="photo">Фото:</label>
                </td>
                <td style="padding: 10px;">
                    <?php if (!empty($profile_data['photo']) && file_exists($profile_data['photo'])): ?>
                        <div style="margin-bottom: 10px;">
                            <img src="<?php echo htmlspecialchars($profile_data['photo']); ?>" 
                                 alt="Поточне фото" 
                                 style="max-width: 200px; max-height: 200px; border: 1px solid #ccc;">
                            <p><small>Поточне фото</small></p>
                        </div>
                    <?php endif; ?>
                    
                    <input type="file" 
                           id="photo" 
                           name="photo" 
                           accept="image/jpeg,image/jpg,image/png,image/gif"
                           style="width: 300px; padding: 8px;">
                    <br><small>Підтримувані формати: JPEG, PNG, GIF. Максимальний розмір: 5MB</small>
                </td>
            </tr>
            
            <tr>
                <td colspan="2" style="text-align: center; padding: 20px;">
                    <input type="submit" name="save_profile" value="Зберегти" class="btn">
                    <a href="index.php?page=home" class="btn" style="margin-left: 10px; text-decoration: none; display: inline-block;">Скасувати</a>
                </td>
            </tr>
        </table>
    </form>
</div>

<script>
document.getElementById('about').addEventListener('input', function() {
    document.getElementById('char-count').textContent = this.value.length;
});
</script>