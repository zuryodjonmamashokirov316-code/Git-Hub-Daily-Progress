
<?php
$host = "localhost";
$dbname = "github_practice";
$username = "root";
$password = "";

$students = [];
$error = "";

$sort = $_GET["sort"] ?? "id";
$order = strtoupper($_GET["order"] ?? "DESC");

// Allow only known database columns
$allowedSort = ["id", "name", "age"];

if (!in_array($sort, $allowedSort, true)) {
    $sort = "id";
}

if (!in_array($order, ["ASC", "DESC"], true)) {
    $order = "DESC";
}

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $sql = "SELECT id, name, age FROM students
            ORDER BY $sort $order";

    $stmt = $pdo->query($sql);
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
    <title>Student Sorting - Day 12</title>
</head>
<body>

<h2>Student Sorting System</h2>

<form method="GET">
    <label>Sort By:</label>
    <select name="sort">
        <option value="id" <?= $sort === "id" ? "selected" : "" ?>>
            ID
        </option>
        <option value="name" <?= $sort === "name" ? "selected" : "" ?>>
            Name
        </option>
        <option value="age" <?= $sort === "age" ? "selected" : "" ?>>
            Age
        </option>
    </select>

    <label>Order:</label>
    <select name="order">
        <option value="ASC" <?= $order === "ASC" ? "selected" : "" ?>>
            Ascending
        </option>
        <option value="DESC" <?= $order === "DESC" ? "selected" : "" ?>>
            Descending
        </option>
    </select>

    <button type="submit">Sort</button>
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

<p>Total students: <?= count($students) ?></p>

</body>
</html>
