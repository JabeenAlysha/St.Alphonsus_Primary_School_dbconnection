<?php
$conn = new mysqli("localhost", "root", "", "StAlphonsus_Primary_school_system");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch dropdown data
$classes = $conn->query("SELECT class_id, class_name FROM classes");
$teachers = $conn->query("SELECT teacher_id FROM teachers");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Student</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f9ff;
            margin: 0;
        }

        header {
            background-color: #004080;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 24px;
        }

        nav {
            background-color: #1e90ff;
            padding: 10px;
            text-align: center;
        }

        nav a {
            color: white;
            margin: 0 15px;
            text-decoration: none;
            font-weight: bold;
        }

        nav a:hover {
            text-decoration: underline;
        }

        .container {
            max-width: 800px;
            margin: 30px auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #004080;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
        }

        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        input[type="submit"] {
            background-color: #004080;
            color: white;
            font-size: 16px;
            cursor: pointer;
            border: none;
        }

        input[type="submit"]:hover {
            background-color: #003366;
        }

        .success { color: green; text-align: center; }
        .error { color: red; text-align: center; }
    </style>
</head>
<body>

<header>St. Alphonsus School Portal</header>

<nav>
    <a href="index.php">Students</a>
    <a href="teachers.php">Teachers</a>
    <a href="parents.php">Parents</a>
    <a href="classes.php">Classes</a>
    <a href="add_student.php">Add New Student</a>
</nav>

<div class="container">
    <h2>Add New Student</h2>
    <form method="post" action="add_student.php">
        <!-- Student Info -->
        <label>Student ID:</label>
        <input type="text" name="student_id" required>

        <label>Full Name:</label>
        <input type="text" name="student_name" required>

        <label>Gender:</label>
        <select name="gender" required>
            <option value="">Select</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>

        <label>Address:</label>
        <textarea name="student_address" required></textarea>

        <!-- Parent Info -->
        <label>Parent ID:</label>
        <input type="number" name="parent_id" required>

        <label>Parent Full Name:</label>
        <input type="text" name="parent_name" required>

        <label>Parent Address:</label>
        <input type="text" name="parent_address" required>

        <label>Parent Email:</label>
        <input type="email" name="parent_email" required>

        <label>Parent Phone:</label>
        <input type="text" name="parent_phone" required>

        <!-- Class Dropdown -->
        <label>Select Class:</label>
        <select name="class_id" required>
            <option value="">Select Class</option>
            <?php while ($row = $classes->fetch_assoc()) {
                echo "<option value='{$row['class_id']}'>{$row['class_name']}</option>";
            } ?>
        </select>

        <!-- Teacher Dropdown -->
        <label>Select Teacher (by ID):</label>
        <select name="teacher_id" required>
            <option value="">Select Teacher ID</option>
            <?php while ($row = $teachers->fetch_assoc()) {
                echo "<option value='{$row['teacher_id']}'>{$row['teacher_id']}</option>";
            } ?>
        </select>

        <input type="submit" name="submit" value="Add Student">
    </form>
</div>

</body>
</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $student_id = $conn->real_escape_string($_POST['student_id']);
    $student_name = $conn->real_escape_string($_POST['student_name']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $student_address = $conn->real_escape_string($_POST['student_address']);
    $class_id = (int) $_POST['class_id'];
    $teacher_id = (int) $_POST['teacher_id'];

    $parent_id = (int) $_POST['parent_id'];
    $parent_name = $conn->real_escape_string($_POST['parent_name']);
    $parent_address = $conn->real_escape_string($_POST['parent_address']);
    $parent_email = $conn->real_escape_string($_POST['parent_email']);
    $parent_phone = $conn->real_escape_string($_POST['parent_phone']);

    $success = true;

    // Insert into students
    $success &= $conn->query("INSERT INTO students (student_id, full_name, gender, address, class_id)
        VALUES ('$student_id', '$student_name', '$gender', '$student_address', $class_id)");

    // Insert into parents
    $success &= $conn->query("INSERT INTO parents (parent_id, full_name, address, email, phone)
        VALUES ($parent_id, '$parent_name', '$parent_address', '$parent_email', '$parent_phone')");

    // Link in student_parents
    $success &= $conn->query("INSERT INTO student_parents (student_id, parent_id)
        VALUES ('$student_id', $parent_id)");

    echo $success
        ? "<p class='success'>✅ Student and parent added and linked successfully!</p>"
        : "<p class='error'>❌ Something went wrong. Check that the IDs are unique and valid.</p>";

    $conn->close();
}
?>
