<?php
// parents.php - View and search parent info


// Show errors while developing (turn off later for security)
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Connect to the MySQL database
$conn = new mysqli('localhost', 'root', '', 'StAlphonsus_Primary_school_system');

// Check if connection to database failed
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // stop and show error message
}

// Get the search keyword from the URL (if typed)
$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : ''; // e.g., ?q=John

// SQL query to get all parents

$sql = "SELECT parent_id, full_name, phone, email FROM parents";

// If search term is entered, change SQL to filter by full name
if (!empty($searchTerm)) {
    $sql .= " WHERE full_name LIKE ?";
}

// Run the SQL query (with or without search)
if (!empty($searchTerm)) {
    // Use a prepared statement to safely insert user input
    $stmt = $conn->prepare($sql);

    // Check if prepare() failed
    if (!$stmt) {
        die("Query error: " . $conn->error);
    }

    // Add % to match any name that contains the search word
    $param = "%" . $searchTerm . "%";

    // Bind the value to the SQL query
    $stmt->bind_param("s", $param);

    // Run the query
    $stmt->execute();

    // Get results
    $result = $stmt->get_result();
} else {
    // Run normal query without search
    $result = $conn->query($sql);

    // Stop and show error if query fails
    if (!$result) {
        die("Query error: " . $conn->error);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta settings for browser -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parents - St. Alphonsus School Portal</title>

    <!-- Page styling using CSS -->
    <style>
        /* Set basic page background and font */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f0faff;
        }

        /* Header style at top of the page */
        .header {
            background-color: #004080;
            color: #fff;
            padding: 20px 0;
            text-align: center;
        }

        /* Navigation bar styling */
        .navbar {
            background-color: #0073e6;
            padding: 10px;
            text-align: center;
        }

        /* Style for each link in the navbar */
        .navbar a {
            color: #fff;
            text-decoration: none;
            margin: 0 20px;
            font-size: 18px;
            font-weight: bold;
        }

        /* Highlight the active or hovered link */
        .navbar a.active,
        .navbar a:hover {
            text-decoration: underline;
        }

        /* Main content container styling */
        .container {
            width: 90%;
            max-width: 1000px;
            background: #fff;
            margin: 30px auto;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        /* Search bar and button style */
        .search-form {
            text-align: center;
            margin-bottom: 30px;
        }

        /* Style for search input box */
        .search-form input[type="text"] {
            padding: 10px;
            width: 60%;
            max-width: 400px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        /* Style for search button */
        .search-form button {
            padding: 10px 20px;
            border: none;
            background-color: #004080;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
        }

        /* Button hover effect */
        .search-form button:hover {
            background-color: #003366;
        }

        /* Style for the big title above the records */
        .section-title {
            color: #004080;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Style for each parent record block */
        .record-card {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-left: 4px solid #004080;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        /* Style for parent's name */
        .record-card h3 {
            color: #004080;
            margin: 0 0 10px;
            font-size: 20px;
        }

        /* Style for phone and email details */
        .record-card p {
            margin: 5px 0;
            color: #333;
        }
    </style>
</head>
<body>

<!-- Top header text -->
<div class="header">
    <h1>St. Alphonsus School Portal</h1>
</div>

<!-- Navigation bar with links to other pages -->
<div class="navbar">
    <a href="index.php">Students</a>
    <a href="teachers.php">Teachers</a>
    <a href="parents.php">Parents</a>
    <a href="classes.php">Classes</a>
    <a href="add_student.php">Add New Student</a>
</div>

<!-- Main area to display search and results -->
<div class="container">

    <!-- Search form for finding parents -->
    <div class="search-form">
        <form action="parents.php" method="get">
            <!-- Search input -->
            <input type="text" name="q" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Search Parents">
            <!-- Search button -->
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Page title -->
    <h1 class="section-title">Parents</h1>

    <?php
    // If we got results from the database
    if ($result) {
        // If at least one parent found
        if ($result->num_rows > 0) {
            // Go through each parent record
            while ($row = $result->fetch_assoc()) {
                echo '<div class="record-card">';

                // Show full name and ID
                echo '<h3>' . htmlspecialchars($row['full_name']) . ' (ID: ' . htmlspecialchars($row['parent_id']) . ')</h3>';

                // Show phone number
                echo '<p><strong>Phone:</strong> ' . htmlspecialchars($row['phone']) . '</p>';

                // Show email
                echo '<p><strong>Email:</strong> ' . htmlspecialchars($row['email']) . '</p>';

                echo '</div>';
            }
        } else {
            // No parents found matching search
            echo '<p style="text-align: center;">No records found.</p>';
        }
    } else {
        // Error running query
        echo '<p style="text-align: center;">Could not load data.</p>';
    }

    // Close statement if used
    if (!empty($searchTerm) && isset($stmt)) {
        $stmt->close();
    }

    // Close the database connection
    $conn->close();
    ?>
</div>

</body>
</html>
