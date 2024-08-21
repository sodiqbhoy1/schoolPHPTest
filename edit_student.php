<?php
session_start();

// Database connection
$host = 'localhost';
$dbname = 'school_management';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

// Check if lecturer is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Lecturer not logged in. Please log in first.";
    exit();
}

$lecturer_id = $_SESSION['user_id'];

// Check if student ID is provided
if (!isset($_GET['id'])) {
    echo "No student selected for editing.";
    exit();
}

$student_id = $_GET['id'];

// Fetch the student's current details
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ? AND lecturer_id = ?");
$stmt->execute([$student_id, $lecturer_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    echo "Student not found or not assigned to you.";
    exit();
}

// Handle form submission for updating student details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("UPDATE students SET firstname = ?, lastname = ?, email = ? WHERE id = ?");
    $stmt->execute([$firstname, $lastname, $email, $student_id]);

    $_SESSION['success'] = "Student details updated successfully.";
    header("Location: lecturer_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Edit Student</h1>
        <form method="post">
            <div class="mb-3">
                <label for="firstname" class="form-label">First Name</label>
                <input type="text" class="form-control" id="firstname" name="firstname" value="<?= htmlspecialchars($student['firstname']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="lastname" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="lastname" name="lastname" value="<?= htmlspecialchars($student['lastname']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($student['email']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="lecturer_dashboard.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
