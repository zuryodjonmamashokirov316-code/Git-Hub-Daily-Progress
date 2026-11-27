
<?php
$host = "localhost";
$dbname = "github_practice";
$username = "root";
$password = "";

$search = trim($_GET["search"] ?? "");
$page = filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT);
$page = ($page && $page > 0) ? $page : 1;

$perPage = 5;
$students = [];
$total = 0;
$totalPages = 1;
$error = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $count = $pdo->prepare(
        "SELECT COUNT(*) FROM students WHERE name LIKE :search"
    );
    $count->execute(["search" => "%" . $search . "%"]);

    $total = (int)$count->fetchColumn();
    $totalPages = max(1, (int)ceil($total / $perPage));
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $perPage;

    $stmt = $pdo->prepare(
        "SELECT id, name, age FROM students
         WHERE name LIKE :search
         ORDER BY id ASC
         LIMIT :limit OFFSET :offset"
    );

    $stmt->bindValue(":search", "%" . $search . "%");
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
    <title>Student Search and Pagination</title>
</head>
<body>

<h2>Student Search & Pagination</h2>

<form method="GET">
    <input type="text" name="search"
           placeholder="Search student name"
           value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Search</button>
    <a href="day15.php">Clear</a>
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
            <td><?= htmlspecialchars($student["name"]) ?></td>
            <td><?= (int)$student["age"] ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<p>Results: <?= $total ?></p>
<p>Page <?= $page ?> of <?= $totalPages ?></p>

<?php
$query = "search=" . urlencode($search);
?>

<?php if ($page > 1): ?>
    <a href="day15.php?<?= $query ?>&page=<?= $page - 1 ?>">
        Previous
    </a>
<?php endif; ?>

<?php if ($page < $totalPages): ?>
    <a href="day15.php?<?= $query ?>&page=<?= $page + 1 ?>">
        Next
    </a>
<?php endif; ?>

</body>
</html>
