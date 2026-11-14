
<?php
session_start();

if (!isset($_SESSION["students"])) {
    $_SESSION["students"] = [];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["add"])) {
        $name = trim($_POST["name"] ?? "");
        $age = filter_input(INPUT_POST, "age", FILTER_VALIDATE_INT);

        if ($name !== "" && $age !== false && $age !== null
            && $age > 0 && $age <= 120) {
            $_SESSION["students"][] = [
                "name" => $name,
                "age" => $age
            ];
        }
    }

    if (isset($_POST["delete"])) {
        $index = filter_input(
            INPUT_POST, "index", FILTER_VALIDATE_INT
        );

        if ($index !== false && $index !== null
            && isset($_SESSION["students"][$index])) {
            array_splice($_SESSION["students"], $index, 1);
        }
    }

    header("Location: day5.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Management System</title>
</head>
<body>
    <h2>Student Management System</h2>

    <form method="POST">
        <input type="text" name="name"
               placeholder="Student Name" required>
        <input type="number" name="age"
               placeholder="Age" min="1" max="120" required>
        <button type="submit" name="add">Add Student</button>
    </form>

    <h3>Student List</h3>

    <table border="1" cellpadding="10">
        <tr>
            <th>Name</th>
            <th>Age</th>
            <th>Action</th>
        </tr>

        <?php foreach ($_SESSION["students"] as $index => $student): ?>
            <tr>
                <td><?= htmlspecialchars($student["name"]) ?></td>
                <td><?= htmlspecialchars((string)$student["age"]) ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="index"
                               value="<?= $index ?>">
                        <button type="submit" name="delete">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
