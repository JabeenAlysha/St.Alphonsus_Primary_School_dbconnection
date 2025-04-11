<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "StAlphonsus_Primary_school_system");

// If connection fails, stop and show message
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get class list for the dropdown
$classes = $conn->query("SELECT class_id, class_name FROM classes");

// Get teacher list for the dropdown
$teachers = $conn->query("SELECT teacher_id FROM teachers");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Student</title>
    <style>
        /* Basic page style */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0faff;
            margin: 0;
        }

        /* Top blue header */
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

        /* Main form box */
        .container {
            max-width: 800px;
            margin: 30px auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #004080;
        }

        label {
            font-weight: bold;
        }

        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            background-color: #004080;
            color: white;
            font-size: 16px;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #003366;
        }

        .success {
            color: green;
            text-align: center;
        }

        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>

<!-- Website title -->
<header>St. Alphonsus School Portal</header>

<!-- Navigation links -->
<nav>
    <a href="index.php">Students</a>
    <a href="teachers.php">Teachers</a>
    <a href="parents.php">Parents</a>
    <a href="classes.php">Classes</a>
    <a href="add_student.php">Add New Student</a>
</nav>

<!-- Form section -->
<div class="container">
    <h2>Add New Student</h2>

    <!-- Form starts here -->
    <form method="post" action="add_student.php">
        <!-- Student Information -->
        <label>Student ID:</label>
        <input type="text" name="student_id" required>

        <label>Student Full Name:</label>
        <input type="text" name="student_name" required>

        <label>Gender:</label>
        <select name="gender" required>
            <option value="">Select</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>

        <label>Student Address:</label>
        <textarea name="student_address" required></textarea>

        <!-- Parent Information -->
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

        <!-- Class selection -->
        <label>Select Class:</label>
        <select name="class_id" required>
            <option value="">Select Class</option>
            <?php
            // Show all class options
            while ($row = $classes->fetch_assoc()) {
                echo "<option value='{$row['class_id']}'>{$row['class_name']}</option>";
            }
            ?>
        </select>

        <!-- Teacher selection -->
        <label>Select Teacher ID:</label>
        <select name="teacher_id" required>
            <option value="">Select Teacher</option>
            <?php
            // Show all teacher ID options
            while ($row = $teachers->fetch_assoc()) {
                echo "<option value='{$row['teacher_id']}'>{$row['teacher_id']}</option>";
            }
            ?>
        </select>

        <!-- Submit button -->
        <input type="submit" name="submit" value="Add Student">
    </form>
</div>

</body>
</html>

<?php
// This part runs when the form is submitted
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

    // Set a flag to check if everything works
    $ok = true;

    // Save student to the database
    $sql1 = "INSERT INTO students (student_id, full_name, gender, address, class_id)
             VALUES ('$student_id', '$student_name', '$gender', '$student_address', $class_id)";
    if (!$conn->query($sql1)) {
        echo "<p class='error'>❌ Could not save student: " . $conn->error . "</p>";
        $ok = false;
    }

    // Save parent to the database
    $sql2 = "INSERT INTO parents (parent_id, full_name, address, email, phone)
             VALUES ($parent_id, '$parent_name', '$parent_address', '$parent_email', '$parent_phone')";
    if (!$conn->query($sql2)) {
        echo "<p class='error'>❌ Could not save parent: " . $conn->error . "</p>";
        $ok = false;
    }

    // Link student to parent
    $sql3 = "INSERT INTO student_parent (student_id, parent_id)
             VALUES ('$student_id', $parent_id)";
    if (!$conn->query($sql3)) {
        echo "<p class='error'>❌ Could not link student and parent: " . $conn->error . "</p>";
        $ok = false;
    }

    // Show success message if everything worked
    if ($ok) {
        echo "<p class='success'>✅ Student and parent added successfully!</p>";
    }

    // Close the database connection
    $conn->close();
}
?>
