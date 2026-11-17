
<?php
$host = "localhost";
$dbname = "github_practice";
$username = "root";
$password = ""; // Default local XAMPP only

$error = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $name = trim($_POST["name"] ?? "");
        $age = filter_input(
            INPUT_POST, "age", FILTER_VALIDATE_INT
        );

        if ($name !== "" && $age !== false
            && $age !== null && $age >= 1 && $age <= 120) {

            $stmt = $pdo->prepare(
                "INSERT INTO students (name, age) VALUES (?, ?)"
            );
            $stmt->execute([$name, $age]);

            header("Location: day8.php");
            exit;
        } else {
            $error = "Enter a valid name and age.";
        }
    }

    $stmt = $pdo->query(
        "SELECT id, name, age FROM students ORDER BY id DESC"
    );
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log($e->getMessage());
    $error = "Database connection or query failed.";
    $students = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Database - Day 8</title>
</head>
<body>

<h2>Student Database System</h2>

<?php if ($error !== ""): ?>
    <p style="color:red;">
        <?= htmlspecialchars($error) ?>
    </p>
<?php endif; ?>

<h3>Add Student</h3>

<form method="POST">
    <input type="text" name="name"
           placeholder="Student Name" required>

    <input type="number" name="age"
           placeholder="Age" min="1" max="120" required>

    <button type="submit">Add Student</button>
</form>

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

</body>
</html>
