
<?php
session_start();

if (!isset($_SESSION["day7_students"])) {
    $_SESSION["day7_students"] = [];
}

$error = "";

// Add or update a student
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $age = filter_input(INPUT_POST, "age", FILTER_VALIDATE_INT);
    $action = $_POST["action"] ?? "";

    if ($name === "" || $age === false || $age === null
        || $age < 1 || $age > 120) {
        $error = "Please enter a valid name and age.";
    } elseif ($action === "add") {
        $_SESSION["day7_students"][] = [
            "name" => $name,
            "age" => $age
        ];
        header("Location: day7.php");
        exit;
    } elseif ($action === "update") {
        $index = filter_input(
            INPUT_POST, "index", FILTER_VALIDATE_INT
        );

        if ($index !== false && $index !== null
            && isset($_SESSION["day7_students"][$index])) {
            $_SESSION["day7_students"][$index] = [
                "name" => $name,
                "age" => $age
            ];
            header("Location: day7.php");
            exit;
        }
        $error = "Student not found.";
    }
}

// Select student to edit
$editIndex = filter_input(INPUT_GET, "edit", FILTER_VALIDATE_INT);
$editing = $editIndex !== false
    && $editIndex !== null
    && isset($_SESSION["day7_students"][$editIndex]);

$editStudent = $editing
    ? $_SESSION["day7_students"][$editIndex]
    : ["name" => "", "age" => ""];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Edit System</title>
</head>
<body>

<h2>Student Management System</h2>

<h3><?= $editing ? "Edit Student" : "Add Student" ?></h3>

<?php if ($error !== ""): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="action"
           value="<?= $editing ? "update" : "add" ?>">

    <?php if ($editing): ?>
        <input type="hidden" name="index"
               value="<?= $editIndex ?>">
    <?php endif; ?>

    <input type="text" name="name"
           placeholder="Student Name"
           value="<?= htmlspecialchars($editStudent["name"]) ?>"
           required>

    <input type="number" name="age"
           min="1" max="120"
           value="<?= htmlspecialchars((string)$editStudent["age"]) ?>"
           required>

    <button type="submit">
        <?= $editing ? "Update Student" : "Add Student" ?>
    </button>
</form>

<h3>Student List</h3>

<table border="1" cellpadding="10">
    <tr>
        <th>Name</th>
        <th>Age</th>
        <th>Action</th>
    </tr>

    <?php foreach ($_SESSION["day7_students"] as $index => $student): ?>
        <tr>
            <td><?= htmlspecialchars($student["name"]) ?></td>
            <td><?= htmlspecialchars((string)$student["age"]) ?></td>
            <td>
                <a href="day7.php?edit=<?= $index ?>">Edit</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
