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

// Check if lecturer ID is set in the session
if (!isset($_SESSION['user_id'])) {
    echo "Lecturer not logged in. Please log in first.";
    exit();
}

$lecturer_id = $_SESSION['user_id'];

// Handle form submissions for edit and delete
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];

    if (isset($_POST['delete'])) {
        $stmt = $conn->prepare("UPDATE students SET lecturer_id = NULL WHERE id = ?");
        $stmt->execute([$student_id]);
        $_SESSION['success'] = "Student removed successfully.";
        header("Location: lecturer_dashboard.php"); // Redirect to prevent form resubmission
        exit();
    } elseif (isset($_POST['edit'])) {
        // Add edit functionality here
        // For example: redirect to an edit page with the student ID
        header("Location: edit_student.php?id=" . $student_id);
        exit();
    }
}

// Fetch students assigned to the lecturer
$stmt = $conn->prepare("SELECT * FROM students WHERE lecturer_id = ?");
$stmt->execute([$lecturer_id]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Debugging: Check SQL query result
// var_dump($students);
// exit();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .alert {
            transition: opacity 0.5s ease-in-out;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">SchoolMS</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <h1>Welcome to Lecturer Dashboard</h1>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success" id="alertMessage">
                <?php 
                echo $_SESSION['success']; 
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <?php if ($students): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?= htmlspecialchars($student['firstname']) . ' ' . htmlspecialchars($student['lastname']); ?></td>
                            <td><?= htmlspecialchars($student['email']); ?></td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="student_id" value="<?= htmlspecialchars($student['id']); ?>">
                                    <button type="submit" name="edit" class="btn btn-warning">Edit</button>
                                    <button type="submit" name="delete" class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No students assigned.</p>
        <?php endif; ?>
    </div>

    <script>
        // Set timeout to fade out alert messages
        setTimeout(function() {
            var alert = document.getElementById('alertMessage');
            if (alert) {
                alert.style.opacity = '0';
                setTimeout(function() { alert.remove(); }, 500);
            }
        }, 3000); // 3 seconds timeout before fading out
    </script>
</body>
</html>
