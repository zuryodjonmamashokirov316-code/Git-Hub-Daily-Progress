
<?php
$host = "localhost";
$dbname = "github_practice";
$username = "root";
$password = "";

$students = [];
$error = "";
$perPage = 5;

$page = filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT);
$page = ($page !== false && $page !== null && $page > 0)
    ? $page : 1;

$totalStudents = 0;
$totalPages = 1;

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Count all students
    $totalStudents = (int)$pdo->query(
        "SELECT COUNT(*) FROM students"
    )->fetchColumn();

    $totalPages = max(1, (int)ceil($totalStudents / $perPage));
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $perPage;

    // Get students for the selected page
    $stmt = $pdo->prepare(
        "SELECT id, name, age
         FROM students
         ORDER BY id ASC
         LIMIT :limit OFFSET :offset"
    );

    $stmt->bindValue(":limit", $perPage, PDO::PARAM_INT);
    $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();

    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log($e->getMessage());
    $error = "Database error. Check MySQL connection.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Pagination - Day 14</title>
</head>
<body>

<h2>Student Pagination System</h2>

<?php if ($error !== ""): ?>
    <p style="color:red;">
        <?= htmlspecialchars($error) ?>
    </p>
<?php endif; ?>

<p>Total Students: <?= $totalStudents ?></p>

<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Age</th>
    </tr>

    <?php foreach ($students as $student): ?>
        <tr>
            <td><?= (int)$student["id"] ?></td>
            <td><?= htmlspecialchars($student["name"]) ?></td>
            <td><?= (int)$student["age"] ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<p>Page <?= $page ?> of <?= $totalPages ?></p>

<?php if ($page > 1): ?>
    <a href="day14.php?page=<?= $page - 1 ?>">
        Previous
    </a>
<?php endif; ?>

<?php if ($page < $totalPages): ?>
    <a href="day14.php?page=<?= $page + 1 ?>">
        Next
    </a>
<?php endif; ?>

</body>
</html>
