<?php
require 'db.php';
require 'functions.php';
require_login('admin');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Невірний CSRF токен";
        header("Location: admin.php");
        exit;
    }

    if ($_POST['action'] === 'update') {
        $id = (int)$_POST['id'];
        $status = $_POST['status'];
        $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
        $stmt->close();
        $_SESSION['success'] = "Замовлення #$id оновлено";
    }

    if ($_POST['action'] === 'delete') {
        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("DELETE FROM orders WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $_SESSION['success'] = "Замовлення #$id видалено";
    }

    header("Location: admin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title>Lab 7 — Адмін-панель</title>
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
    <h1>Адмін-панель</h1>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="success"><?= e($_SESSION['success']);
                                unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="error"><?= e($_SESSION['error']);
                            unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <table>
        <tr>
            <th>ID</th>
            <th>Клієнт</th>
            <th>Телефон</th>
            <th>Послуга</th>
            <th>Опис</th>
            <th>Статус</th>
            <th>Дії</th>
        </tr>
        <?php
        $res = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
        while ($row = $res->fetch_assoc()):
        ?>
            <tr>
                <td><?= e($row['id']); ?></td>
                <td><?= e($row['customer_name']); ?></td>
                <td><?= e($row['phone']); ?></td>
                <td><?= e($row['service']); ?></td>
                <td><?= e($row['description']); ?></td>
                <td><?= e($row['status']); ?></td>
                <td>
                    <form method="POST" style="display:inline-block;">
                        <input type="hidden" name="csrf_token" value="<?= e(generate_csrf_token()); ?>">
                        <input type="hidden" name="id" value="<?= e($row['id']); ?>">
                        <select name="status">
                            <?php foreach (['Прийнято', 'В роботі', 'Готово', 'Видано'] as $s): ?>
                                <option <?= $row['status'] == $s ? 'selected' : ''; ?>><?= $s; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" name="action" value="update">Оновити</button>
                    </form>

                    <form method="POST" style="display:inline-block;" onsubmit="return confirm('Видалити замовлення?');">
                        <input type="hidden" name="csrf_token" value="<?= e(generate_csrf_token()); ?>">
                        <input type="hidden" name="id" value="<?= e($row['id']); ?>">
                        <button type="submit" class="delete-btn" name="action" value="delete">Видалити</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
    <p><a href="logout.php">Вийти</a></p>

</body>

</html>