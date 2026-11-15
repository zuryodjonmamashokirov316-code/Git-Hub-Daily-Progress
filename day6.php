
<?php
session_start();

if (!isset($_SESSION["students"])) {
    $_SESSION["students"] = [];
}

// Add a student
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $age = filter_input(INPUT_POST, "age", FILTER_VALIDATE_INT);

    if ($name !== "" && $age !== false
        && $age !== null && $age >= 1 && $age <= 120) {

        $_SESSION["students"][] = [
            "name" => $name,
            "age" => $age
        ];
    }

    header("Location: day6.php");
    exit;
}

// Search and filter
$search = trim($_GET["search"] ?? "");
$ageFilter = trim($_GET["age"] ?? "");

$filteredStudents = array_filter(
    $_SESSION["students"],
    function ($student) use ($search, $ageFilter) {
        $matchesName = $search === ""
            || stripos($student["name"], $search) !== false;

        $matchesAge = $ageFilter === ""
            || (string)$student["age"] === $ageFilter;

        return $matchesName && $matchesAge;
    }
);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Student Search System</title>
</head>
<body>

<h2>Student Search & Filter</h2>

<h3>Add Student</h3>
<form method="POST">
    <input type="text" name="name"
           placeholder="Student name" required>

    <input type="number" name="age"
           min="1" max="120" placeholder="Age" required>

    <button type="submit">Add</button>
</form>

<h3>Search Students</h3>
<form method="GET">
    <input type="text" name="search"
           placeholder="Search by name"
           value="<?= htmlspecialchars($search) ?>">

    <input type="number" name="age"
           placeholder="Filter by age" min="1" max="120"
           value="<?= htmlspecialchars($ageFilter) ?>">

    <button type="submit">Search</button>
    <a href="day6.php">Clear</a>
</form>

<h3>Student List</h3>

<table border="1" cellpadding="10">
    <tr>
        <th>Name</th>
        <th>Age</th>
    </tr>

    <?php foreach ($filteredStudents as $student): ?>
    <tr>
        <td><?= htmlspecialchars($student["name"]) ?></td>
        <td><?= htmlspecialchars((string)$student["age"]) ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<p>Results: <?= count($filteredStudents) ?></p>

</body>
</html>
