<?php
require 'db.php';
require 'functions.php';

if (!empty($_SESSION['user_id'])) {
    if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        header("Location: admin.php");
    } else {
        header("Location: index.php");
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $_SESSION['error'] = "Вкажіть логін та пароль";
        header("Location: login.php");
        exit;
    }

    if ($conn !== null) {
        $stmt = $conn->prepare("SELECT id, password_hash, role FROM users WHERE username = ?");

        if (!$stmt) {
            $_SESSION['error'] = "Помилка БД при підготовці запиту";
            header("Location: login.php");
            exit;
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($id, $hash, $role);
        $found = $stmt->fetch();
        $stmt->close();

        if ($found && password_verify($password, $hash)) {
            session_regenerate_id(true);

            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

            if ($role === 'admin') {
                header("Location: admin.php");
                exit;
            } else {
                header("Location: index.php");
                exit;
            }
        } else {
            $_SESSION['error'] = "Невірний логін або пароль";
            header("Location: login.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Виникла помилка підключення до бази даних. Спробуйте пізніше.";
        header("Location: login.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title>Вхід</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <h1>Вхід</h1>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="success"><?= e($_SESSION['success']); ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="error"><?= e($_SESSION['error']); ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="POST" action="login.php" autocomplete="off" novalidate>
        <input type="text" name="username" placeholder="Логін" required>
        <input type="password" name="password" placeholder="Пароль" required>
        <button type="submit">Увійти</button>
    </form>

    <p>Ще не маєте акаунта? <a href="register.php">Зареєструватися</a></p>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const msg = document.querySelector('.success, .error');
            if (msg) {
                setTimeout(() => {
                    msg.classList.add('fade-out');
                    setTimeout(() => msg.remove(), 500);
                }, 3000);
            }
        });
    </script>
</body>

</html>