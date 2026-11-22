
<?php
$host = "localhost";
$dbname = "github_practice";
$username = "root";
$password = "";

$error = "";
$stats = [
    "total" => 0,
    "average_age" => 0,
    "youngest" => null,
    "oldest" => null
];

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $sql = "SELECT
                COUNT(*) AS total,
                AVG(age) AS average_age,
                MIN(age) AS youngest,
                MAX(age) AS oldest
            FROM students";

    $stmt = $pdo->query($sql);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $stats = $result;
    }

} catch (PDOException $e) {
    error_log($e->getMessage());
    $error = "Database error. Check MySQL connection.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Statistics - Day 13</title>
</head>
<body>

<h2>Student Statistics Dashboard</h2>

<?php if ($error !== ""): ?>
    <p style="color:red;">
        <?= htmlspecialchars($error) ?>
    </p>
<?php endif; ?>

<h3>Total Students:
    <?= (int)$stats["total"] ?>
</h3>

<h3>Average Age:
    <?= $stats["average_age"] !== null
        ? number_format((float)$stats["average_age"], 1)
        : "N/A" ?>
</h3>

<h3>Youngest Student Age:
    <?= $stats["youngest"] !== null
        ? (int)$stats["youngest"]
        : "N/A" ?>
</h3>

<h3>Oldest Student Age:
    <?= $stats["oldest"] !== null
        ? (int)$stats["oldest"]
        : "N/A" ?>
</h3>

<hr>

<a href="day12.php">Back to Student List</a>

</body>
</html>
