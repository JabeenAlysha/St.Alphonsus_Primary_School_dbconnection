<?php
// teachers.php — Show a list of teachers and allow search by full name

// Show errors while developing (disable before final submission)
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'StAlphonsus_Primary_school_system');

// Stop the script if the connection fails
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the search term from the input box (if any)
$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';

// Create the SQL query to get teacher data
$sql = "SELECT teacher_id, full_name, address, phone, salary FROM teachers";

// If the user typed something in the search box, filter the results
if (!empty($searchTerm)) {
    $sql .= " WHERE full_name LIKE ?";
}

// Run the SQL query (use prepared statements if searching)
if (!empty($searchTerm)) {
    $stmt = $conn->prepare($sql); // Prepare query
    if (!$stmt) {
        die("Prepared Statement Error: " . $conn->error);
    }

    $param = "%" . $searchTerm . "%";  // Add wildcard to match part of a name
    $stmt->bind_param("s", $param);    // Bind the search term to the query
    $stmt->execute();                  // Run the query
    $result = $stmt->get_result();     // Store the result
} else {
    // No search — run the normal query
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
    <title>Teachers - St. Alphonsus School Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Basic page styling */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f0faff;
        }

        /* Top header */
        .header {
            background-color: #004080;
            color: #fff;
            padding: 20px 0;
            text-align: center;
        }

        /* Navigation bar for links */
        .navbar {
            background-color: #0073e6;
            padding: 10px;
            text-align: center;
        }

        /* Links in the navbar */
        .navbar a {
            color: #fff;
            text-decoration: none;
            margin: 0 20px;
            font-size: 18px;
            font-weight: bold;
        }

        /* Highlight link when hovered or active */
        .navbar a:hover,
        .navbar a.active {
            text-decoration: underline;
        }

        /* Main white box container */
        .container {
            width: 90%;
            max-width: 1000px;
            background: #fff;
            margin: 30px auto;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        /* Search bar and button */
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

        /* Title before the results */
        .section-title {
            color: #004080;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Box for each teacher record */
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

<!-- Header -->
<div class="header">
    <h1>St. Alphonsus School Portal</h1>
</div>

<!-- Navigation bar -->
<div class="navbar">
    <a href="index.php">Students</a>
    <a href="teachers.php" class="active">Teachers</a>
    <a href="parents.php">Parents</a>
    <a href="classes.php">Classes</a>
    <a href="add_student.php">Add New Student</a>
</div>

<!-- Page content area -->
<div class="container">

    <!-- Search box -->
    <div class="search-form">
        <form action="teachers.php" method="get">
            <input type="text" name="q" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Search Teachers">
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Title before the list -->
    <h1 class="section-title">Teachers</h1>

    <?php
    // If we have results
    if ($result) {
        if ($result->num_rows > 0) {
            // Loop through each teacher and show the info
            while ($row = $result->fetch_assoc()) {
                echo '<div class="record-card">';
                echo '<h3>' . htmlspecialchars($row['full_name']) . ' (ID: ' . htmlspecialchars($row['teacher_id']) . ')</h3>';
                echo '<p><strong>Address:</strong> ' . htmlspecialchars($row['address']) . '</p>';
                echo '<p><strong>Phone:</strong> ' . htmlspecialchars($row['phone']) . '</p>';
                echo '<p><strong>Salary:</strong> ' . htmlspecialchars($row['salary']) . '</p>';
                echo '</div>';
            }
        } else {
            // No matching teachers found
            echo '<p style="text-align: center;">No records found.</p>';
        }
    } else {
        // Query didn't run properly
        echo '<p style="text-align: center;">Error executing query.</p>';
    }

    // Close the prepared statement (if used)
    if (!empty($searchTerm) && isset($stmt)) {
        $stmt->close();
    }

    // Close the database connection
    $conn->close();
    ?>
</div>

</body>
</html>
