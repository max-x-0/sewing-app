<?php
require 'db.php';
require 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($conn !== null) {
        $username = strip_tags(trim($_POST['username']));
        $password = $_POST['password'];
        $role = "user";

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");

        if (!$stmt) {
            $_SESSION['error'] = "Помилка БД при підготовці запиту";
            header("Location: login.php");
            exit;
        }

        $stmt->bind_param("sss", $username, $hash, $role);
        $ok = $stmt->execute();
        $stmt->close();

        $_SESSION[$ok ? 'success' : 'error'] = $ok ? "Користувач зареєстрований, увійдіть" : "Помилка реєстрації";
    } else {
        $_SESSION['error'] = "Неможливо зареєструватись: функціонал БД недоступний.";
    }

    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title>Реєстрація</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <h1>Реєстрація</h1>
    <form method="POST">
        <input type="text" name="username" placeholder="Логін" required>
        <input type="password" name="password" placeholder="Пароль" required>
        <button type="submit">Зареєструватися</button>
    </form>
    <p>Вже маєте акаунт? <a href="login.php">Увійти</a></p>
</body>

</html>