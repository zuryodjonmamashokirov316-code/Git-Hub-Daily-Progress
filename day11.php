
<?php
$host = "localhost";
$dbname = "github_practice";
$username = "root";
$password = "";

$students = [];
$error = "";

$search = trim($_GET["search"] ?? "");

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    if ($search !== "") {
        $stmt = $pdo->prepare(
            "SELECT id, name, age
             FROM students
             WHERE name LIKE :search
             ORDER BY id DESC"
        );

        $stmt->execute([
            "search" => "%" . $search . "%"
        ]);
    } else {
        $stmt = $pdo->query(
            "SELECT id, name, age
             FROM students
             ORDER BY id DESC"
        );
    }

    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log($e->getMessage());
    $error = "Database connection or query failed.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Search - Day 11</title>
</head>
<body>

<h2>Student Search System</h2>

<form method="GET">
    <input
        type="text"
        name="search"
        placeholder="Search student name"
        value="<?= htmlspecialchars($search) ?>"
    >

    <button type="submit">Search</button>
    <a href="day11.php">Clear</a>
</form>

<?php if ($error !== ""): ?>
    <p style="color:red;">
        <?= htmlspecialchars($error) ?>
    </p>
<?php endif; ?>

<h3>Student List</h3>

<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Age</th>
    </tr>

    <?php foreach ($students as $student): ?>
        <tr>
            <td><?= (int)$student["id"] ?></td>
            <td>
                <?= htmlspecialchars($student["name"]) ?>
            </td>
            <td><?= (int)$student["age"] ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<p>Results: <?= count($students) ?></p>

</body>
</html>
