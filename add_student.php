<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "StAlphonsus_Primary_school_system");

// Check if connection failed
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get class and teacher data for dropdowns
$classes = $conn->query("SELECT class_id, class_name FROM classes");
$teachers = $conn->query("SELECT teacher_id FROM teachers");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Student</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Page background and font */
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f9ff;
            margin: 0;
        }

        /* Header styling */
        header {
            background-color: #004080;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 24px;
        }

        /* Navigation bar */
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

        /* Container for form */
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

        /* Input styling */
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

        /* Submit button */
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

        /* Message styling */
        .success { color: green; text-align: center; }
        .error { color: red; text-align: center; }
    </style>
</head>
<body>

<!-- Page header -->
<header>St. Alphonsus School Portal</header>

<!-- Navigation links -->
<nav>
    <a href="index.php">Students</a>
    <a href="teachers.php">Teachers</a>
    <a href="parents.php">Parents</a>
    <a href="classes.php">Classes</a>
    <a href="add_student.php">Add New Student</a>
</nav>

<!-- Form container -->
<div class="container">
    <h2>Add New Student</h2>

    <!-- Form to submit student and parent data -->
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

        <!-- Class dropdown -->
        <label>Select Class:</label>
        <select name="class_id" required>
            <option value="">Select Class</option>
            <?php while ($row = $classes->fetch_assoc()) {
                echo "<option value='{$row['class_id']}'>{$row['class_name']}</option>";
            } ?>
        </select>

        <!-- Teacher dropdown -->
        <label>Select Teacher (by ID):</label>
        <select name="teacher_id" required>
            <option value="">Select Teacher ID</option>
            <?php while ($row = $teachers->fetch_assoc()) {
                echo "<option value='{$row['teacher_id']}'>{$row['teacher_id']}</option>";
            } ?>
        </select>

        <!-- Submit button -->
        <input type="submit" name="submit" value="Add Student">
    </form>
</div>

</body>
</html>

<?php
// If the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get student info from the form
    $student_id = $conn->real_escape_string($_POST['student_id']);
    $student_name = $conn->real_escape_string($_POST['student_name']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $student_address = $conn->real_escape_string($_POST['student_address']);
    $class_id = (int) $_POST['class_id'];
    $teacher_id = (int) $_POST['teacher_id'];

    // Get parent info from the form
    $parent_id = (int) $_POST['parent_id'];
    $parent_name = $conn->real_escape_string($_POST['parent_name']);
    $parent_address = $conn->real_escape_string($_POST['parent_address']);
    $parent_email = $conn->real_escape_string($_POST['parent_email']);
    $parent_phone = $conn->real_escape_string($_POST['parent_phone']);

    // Use a flag to track success
    $success = true;

    // Insert student into students table
    $success &= $conn->query("INSERT INTO students (student_id, full_name, gender, address, class_id)
        VALUES ('$student_id', '$student_name', '$gender', '$student_address', $class_id)");

    // Insert parent into parents table
    $success &= $conn->query("INSERT INTO parents (parent_id, full_name, address, email, phone)
        VALUES ($parent_id, '$parent_name', '$parent_address', '$parent_email', '$parent_phone')");

    // Link student and parent in student_parents table
    $success &= $conn->query("INSERT INTO student_parents (student_id, parent_id)
        VALUES ('$student_id', $parent_id)");

    // Show result message
    echo $success
        ? "<p class='success'>✅ Student and parent added and linked successfully!</p>"
        : "<p class='error'>❌ Something went wrong. Check that the IDs are unique and valid.</p>";

    // Close the database connection
    $conn->close();
}
?>
