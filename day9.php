
<?php
$host = "localhost";
$dbname = "github_practice";
$username = "root";
$password = "";

$error = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Add or delete student
    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $action = $_POST["action"] ?? "";

        if ($action === "add") {
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

                header("Location: day9.php");
                exit;
            } else {
                $error = "Please enter a valid name and age.";
            }
        }

        if ($action === "delete") {
            $id = filter_input(
                INPUT_POST, "id", FILTER_VALIDATE_INT
            );

            if ($id !== false && $id !== null && $id > 0) {
                $stmt = $pdo->prepare(
                    "DELETE FROM students WHERE id = ?"
                );
                $stmt->execute([$id]);

                header("Location: day9.php");
                exit;
            } else {
                $error = "Invalid student ID.";
            }
        }
    }

    $stmt = $pdo->query(
        "SELECT id, name, age FROM students ORDER BY id DESC"
    );
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log($e->getMessage());
    $error = "Database error. Check MySQL connection.";
    $students = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Management - Day 9</title>
</head>
<body>

<h2>Student Management System</h2>

<?php if ($error !== ""): ?>
    <p style="color:red;">
        <?= htmlspecialchars($error) ?>
    </p>
<?php endif; ?>

<h3>Add Student</h3>

<form method="POST">
    <input type="hidden" name="action" value="add">

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
        <th>Action</th>
    </tr>

    <?php foreach ($students as $student): ?>
        <tr>
            <td><?= (int)$student["id"] ?></td>
            <td>
                <?= htmlspecialchars($student["name"]) ?>
            </td>
            <td><?= (int)$student["age"] ?></td>
            <td>
                <form method="POST"
                      onsubmit="return confirm('Delete this student?');">

                    <input type="hidden"
                           name="action" value="delete">

                    <input type="hidden" name="id"
                           value="<?= (int)$student["id"] ?>">

                    <button type="submit">Delete</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
