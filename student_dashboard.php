<?php
session_start();
$host = 'localhost';
$dbname = 'school_management';
$username = 'root';
$password = '';

$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$student_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM lecturers");
$stmt->execute();
$lecturers = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lecturer_id = $_POST['lecturer_id'];

    $stmt = $conn->prepare("UPDATE students SET lecturer_id = ? WHERE id = ?");
    $stmt->execute([$lecturer_id, $student_id]);

    echo "<script>alert('Lecturer selected successfully!');</script>";
}

$stmt = $conn->prepare("SELECT lecturer_id FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$selected_lecturer = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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



        <h1>Welcome to Student Dashboard</h1>

        <p>Firstname: <?php echo $_SESSION['firstname'] ?> </p>
        <p>Lastname: <?php echo $_SESSION['lastname'] ?> </p>
        <p>Email: <?php echo $_SESSION['email'] ?> </p>
        
        <form method="post">
            <div class="mb-3">
                <label for="lecturer" class="form-label">Select Lecturer</label>
                <select class="form-control" id="lecturer" name="lecturer_id" <?php if($selected_lecturer) echo 'disabled'; ?>>
                    <?php foreach ($lecturers as $lecturer): ?>
                        <option value="<?= $lecturer['id']; ?>" <?php if($selected_lecturer == $lecturer['id']) echo 'selected'; ?>><?= $lecturer['firstname'] . ' ' . $lecturer['lastname']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if(!$selected_lecturer): ?>
                <button type="submit" class="btn btn-primary">Select Lecturer</button>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
