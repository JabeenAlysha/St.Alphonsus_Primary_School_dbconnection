<?php
// classes.php — Show a list of classes and allow search by class name



// Connect to the MySQL database
$conn = new mysqli('localhost', 'root', '', 'StAlphonsus_Primary_school_system');

// Check if the connection failed
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the search term from the input box if typed
$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';

// Start writing the SQL to get class info from the classes table
$sql = "SELECT class_id, class_name, teacher_id, capacity FROM classes";

// If search term is entered, add a filter to the SQL
if (!empty($searchTerm)) {
    $sql .= " WHERE class_name LIKE ?";
}

// Run the SQL query
if (!empty($searchTerm)) {
    // Prepare a safe query using prepared statements
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepared Statement Error: " . $conn->error);
    }

    // Add wildcards so partial names also match
    $param = "%" . $searchTerm . "%";

    // Add the search term into the query
    $stmt->bind_param("s", $param);

    // Run the query
    $stmt->execute();

    // Get the results to display later
    $result = $stmt->get_result();
} else {
    // No search typed, just run the basic query
    $result = $conn->query($sql);

    // Stop if there's an error
    if (!$result) {
        die("Query Error: " . $conn->error);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Classes - St. Alphonsus School Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Set up background and fonts */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f0faff;
        }

        /* Header at the top of the page */
        .header {
            background-color: #004080;
            color: #fff;
            padding: 20px 0;
            text-align: center;
        }

        /* Navigation bar with page links */
        .navbar {
            background-color: #0073e6;
            padding: 10px;
            text-align: center;
        }

        /* Each link in the navbar */
        .navbar a {
            color: #fff;
            text-decoration: none;
            margin: 0 20px;
            font-size: 18px;
            font-weight: bold;
        }

        /* Highlight active page or hovered link */
        .navbar a.active,
        .navbar a:hover {
            text-decoration: underline;
        }

        /* Container for main content */
        .container {
            width: 90%;
            max-width: 1000px;
            background: #fff;
            margin: 30px auto;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        /* Styling for the search box and button */
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

        /* Section title above class list */
        .section-title {
            color: #004080;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Each class card shown on the page */
        .record-card {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-left: 4px solid #004080;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        /* Title inside each record card (class name) */
        .record-card h3 {
            color: #004080;
            margin: 0 0 10px;
            font-size: 20px;
        }

        /* Other text in the class card */
        .record-card p {
            margin: 5px 0;
            color: #333;
        }
    </style>
</head>
<body>

<!-- Page header -->
<div class="header">
    <h1>St. Alphonsus School Portal</h1>
</div>

<!-- Navigation bar to other sections -->
<div class="navbar">
    <a href="index.php">Students</a>
    <a href="teachers.php">Teachers</a>
    <a href="parents.php">Parents</a>
    <a href="classes.php" class="active">Classes</a>
    <a href="add_student.php">Add New Student</a>
</div>

<!-- Main content area -->
<div class="container">

    <!-- Search box for class name -->
    <div class="search-form">
        <form action="classes.php" method="get">
            <input type="text" name="q" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Search Classes">
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Title before list of classes -->
    <h1 class="section-title">Classes</h1>

    <?php
    // If there are results to show
    if ($result) {
        if ($result->num_rows > 0) {
            // Go through each class and display it
            while ($row = $result->fetch_assoc()) {
                echo '<div class="record-card">';
                // Show class name and class ID
                echo '<h3>' . htmlspecialchars($row['class_name']) . ' (ID: ' . htmlspecialchars($row['class_id']) . ')</h3>';
                // Show teacher assigned and max capacity
                echo '<p><strong>Teacher ID:</strong> ' . htmlspecialchars($row['teacher_id']) . '</p>';
                echo '<p><strong>Capacity:</strong> ' . htmlspecialchars($row['capacity']) . '</p>';
                echo '</div>';
            }
        } else {
            // No classes found
            echo '<p style="text-align: center;">No records found.</p>';
        }
    } else {
        // Problem with the query
        echo '<p style="text-align: center;">Error executing query.</p>';
    }

    // Close the statement if search was used
    if (!empty($searchTerm) && isset($stmt)) {
        $stmt->close();
    }

    // Always close the database connection
    $conn->close();
    ?>
</div>

</body>
</html>
