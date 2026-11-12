
<?php
session_start();

$error = "";

if (isset($_GET["logout"])) {
    $_SESSION = [];
    session_destroy();
    header("Location: day3.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"] ?? "";
    $password = $_POST["password"] ?? "";

    if ($username === "admin" && $password === "1234") {
        session_regenerate_id(true);
        $_SESSION["username"] = $username;
        header("Location: day3.php");
        exit;
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>PHP Login System</title>
</head>
<body>

<h2>PHP Login System</h2>

<?php if (isset($_SESSION["username"])): ?>

    <h3>Welcome,
        <?= htmlspecialchars($_SESSION["username"]) ?>!
    </h3>

    <a href="day3.php?logout=1">Logout</a>

<?php else: ?>

    <form method="POST">
        <input type="text" name="username"
               placeholder="Username" required>

        <br><br>

        <input type="password" name="password"
               placeholder="Password" required>

        <br><br>

        <button type="submit">Login</button>
    </form>

    <p style="color:red;">
        <?= htmlspecialchars($error) ?>
    </p>

<?php endif; ?>

</body>
</html>
