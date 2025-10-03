<?php
require 'db.php';
require 'functions.php';
require_login('user');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Невірний CSRF токен";
        header("Location: index.php");
        exit;
    }

    $name = $_SESSION['username'];
    $phone = strip_tags(trim($_POST['phone']));
    $service = strip_tags(trim($_POST['service']));
    $desc = strip_tags(trim($_POST['description']));

    $stmt = $conn->prepare("INSERT INTO orders (customer_name, phone, service, description) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $phone, $service, $desc);
    $ok = $stmt->execute();
    $stmt->close();

    $_SESSION[$ok ? 'success' : 'error'] = $ok ? "Замовлення додано!" : "Помилка додавання";
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title>Кабінет користувача</title>
    <link rel="stylesheet" href="styles.css">
</head>

<script>
    setTimeout(() => {
        document.querySelectorAll('.success, .error').forEach(el => {
            el.classList.add('fade-out');
            setTimeout(() => el.remove(), 500);
        });
    }, 3000);
</script>


<body>
    <h1>Вітаю, <?= e($_SESSION['username']); ?>!</h1>
    <p><a href="logout.php">Вийти</a></p>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="success"><?= e($_SESSION['success']);
                                unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="error"><?= e($_SESSION['error']);
                            unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <h2>Нове замовлення</h2>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= e(generate_csrf_token()); ?>">
        <input type="text" name="phone" placeholder="Телефон" required>
        <input type="text" name="service" placeholder="Тип послуги" required>
        <textarea name="description" placeholder="Опис"></textarea>
        <button type="submit" name="submit">Відправити</button>
    </form>

    <h2>Мої замовлення</h2>
    <table>
        <tr>
            <th>Послуга</th>
            <th>Телефон</th>
            <th>Опис</th>
            <th>Статус</th>
            <th>Дата</th>
        </tr>
        <?php
        $name = $_SESSION['username'];
        $stmt = $conn->prepare("SELECT service, status, description, phone, created_at FROM orders WHERE customer_name=? ORDER BY created_at DESC");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()):
        ?>
            <tr>
                <td><?= e($row['service']); ?></td>
                <td><?= e($row['phone']); ?></td>
                <td><?= e($row['description']); ?></td>
                <td><?= e($row['status']); ?></td>
                <td><?= e($row['created_at']); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>

</html>