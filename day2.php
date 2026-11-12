
<!DOCTYPE html>
<html>
<head>
    <title>Student Grade Calculator</title>
</head>
<body>

<h2>Student Grade Calculator</h2>

<form method="POST">
    <label>Enter your score:</label>
    <input type="number" name="score" required>
    <button type="submit">Calculate</button>
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $score = $_POST["score"];

    if ($score >= 90 && $score <= 100) {
        $grade = "A";
    } elseif ($score >= 80 && $score < 90) {
        $grade = "B";
    } elseif ($score >= 70 && $score < 80) {
        $grade = "C";
    } elseif ($score >= 60 && $score < 70) {
        $grade = "D";
    } elseif ($score >= 0 && $score < 60) {
        $grade = "F";
    } else {
        $grade = "Invalid Score";
    }

    echo "<h3>Your Grade: $grade</h3>";
}
?>

</body>
</html>
