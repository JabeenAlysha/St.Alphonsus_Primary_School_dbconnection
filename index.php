<?php
// index.php — Shows students and allows searching by full name

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// DB setup
$conn = new mysqli('localhost', 'root', '', 'StAlphonsus_Primary_school_system');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';

// Base query (joins students with classes to show class name)
$sql = "SELECT s.student_id, s.full_name, s.gender, s.address, c.class_name
        FROM students AS s
        LEFT JOIN classes AS c ON s.class_id = c.class_id";

if (!empty($searchTerm)) {
    $sql .= " WHERE s.full_name LIKE ?";
}

if (!empty($searchTerm)) {
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepared Statement Error: " . $conn->error);
    }
    $param = "%" . $searchTerm . "%";
    $stmt->bind_param("s", $param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
    if (!$result) {
        die("Query Error: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Students - St. Alphonsus School Portal</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f0faff;
        }
        .header {
            background-color: #004080;
            color: #fff;
            padding: 20px 0;
            text-align: center;
        }
        .navbar {
            background-color: #0073e6;
            padding: 10px;
            text-align: center;
        }
        .navbar a {
            color: #fff;
            text-decoration: none;
            margin: 0 20px;
            font-size: 18px;
            font-weight: bold;
        }
        .navbar a.active,
        .navbar a:hover {
            text-decoration: underline;
        }
        .container {
            width: 90%;
            max-width: 1000px;
            background: #fff;
            margin: 30px auto;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .search-form {
            text-align: center;
            margin-bottom: 30px;
        }
        .search-form input[type="text"] {
            padding: 10px;
            width: 60%;
            max-width: 400px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .search-form button {
            padding: 10px 20px;
            border: none;
            background-color: #004080;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
        }
        .search-form button:hover {
            background-color: #003366;
        }
        .section-title {
            color: #004080;
            text-align: center;
            margin-bottom: 20px;
        }
        .record-card {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-left: 4px solid #004080;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .record-card h3 {
            color: #004080;
            margin: 0 0 10px;
            font-size: 20px;
        }
        .record-card p {
            margin: 5px 0;
            color: #333;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>St. Alphonsus School Portal</h1>
</div>

<div class="navbar">
    <a href="index.php">Students</a>
    <a href="teachers.php">Teachers</a>
    <a href="parents.php">Parents</a>
    <a href="classes.php">Classes</a>
    <a href="add_student.php">Add New Student</a>
</div>

<div class="container">
    <div class="search-form">
        <form action="index.php" method="get">
            <input type="text" name="q" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Search Students">
            <button type="submit">Search</button>
        </form>
    </div>

    <h1 class="section-title">Students</h1>

    <?php
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="record-card">';
            echo '<h3>' . htmlspecialchars($row['full_name']) . ' (ID: ' . htmlspecialchars($row['student_id']) . ')</h3>';
            echo '<p><strong>Gender:</strong> ' . htmlspecialchars($row['gender']) . '</p>';
            echo '<p><strong>Address:</strong> ' . htmlspecialchars($row['address']) . '</p>';
            echo '<p><strong>Class:</strong> ' . htmlspecialchars($row['class_name']) . '</p>';
            echo '</div>';
        }
    } else {
        echo '<p style="text-align: center;">No student records found.</p>';
    }

    if (!empty($searchTerm) && isset($stmt)) {
        $stmt->close();
    }

    $conn->close();
    ?>
</div>

</body>
</html>
