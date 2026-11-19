
<?php
$host = "localhost";
$dbname = "github_practice";
$username = "root";
$password = "";

$error = "";
$students = [];

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Update student
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
        $name = trim($_POST["name"] ?? "");
        $age = filter_input(INPUT_POST, "age", FILTER_VALIDATE_INT);

        if ($id && $name !== "" && $age !== false
            && $age !== null && $age >= 1 && $age <= 120) {

            $stmt = $pdo->prepare(
                "UPDATE students SET name = ?, age = ? WHERE id = ?"
            );

            $stmt->execute([$name, $age, $id]);

            header("Location: day10.php");
            exit;
        } else {
            $error = "Please enter valid student information.";
        }
    }

    // Get student to edit
    $editId = filter_input(INPUT_GET, "edit", FILTER_VALIDATE_INT);
    $editStudent = null;

    if ($editId) {
        $stmt = $pdo->prepare(
            "SELECT id, name, age FROM students WHERE id = ?"
        );
        $stmt->execute([$editId]);
        $editStudent = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get all students
    $stmt = $pdo->query(
        "SELECT id, name, age FROM students ORDER BY id DESC"
    );
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
    <title>Student Update - Day 10</title>
</head>
<body>

<h2>Student Management — Update</h2>

<?php if ($error !== ""): ?>
    <p style="color:red;">
        <?= htmlspecialchars($error) ?>
    </p>
<?php endif; ?>

<?php if ($editStudent): ?>
    <h3>Edit Student</h3>

    <form method="POST">
        <input type="hidden" name="id"
               value="<?= (int)$editStudent["id"] ?>">

        <input type="text" name="name"
               value="<?= htmlspecialchars($editStudent["name"]) ?>"
               required>

        <input type="number" name="age"
               value="<?= (int)$editStudent["age"] ?>"
               min="1" max="120" required>

        <button type="submit">Update Student</button>
        <a href="day10.php">Cancel</a>
    </form>
<?php endif; ?>

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
            <td><?= htmlspecialchars($student["name"]) ?></td>
            <td><?= (int)$student["age"] ?></td>
            <td>
                <a href="day10.php?edit=<?= (int)$student["id"] ?>">
                    Edit
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
