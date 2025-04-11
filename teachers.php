<?php
// ================================
// teachers.php
// This page displays teacher information and allows you to search by the teacher’s full name.
// It also includes a navigation bar linking to the other pages (Students, Teachers, Parents, Classes).
// ================================

// Turn on error reporting for debugging (Remember to disable or remove these in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// --------------------------
// Database Connection Setup
// --------------------------
$host     = 'localhost';                          // Database host (usually "localhost" in XAMPP)
$user     = 'root';                               // Default XAMPP username
$password = '';                                   // Default XAMPP password is usually empty
$database = 'StAlphonsus_Primary_school_system';  // Your database name

// Create a new MySQLi connection
$conn = new mysqli($host, $user, $password, $database);

// Check for connection errors and stop the script if any occur
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --------------------------
// Handle the Search Functionality
// --------------------------
// Check if a search term was provided via the "q" GET parameter
$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';

// Build the SQL query to fetch teacher records.
// Adjust the column names below to match your teachers table.
$sql = "SELECT teacher_id, full_name, address, phone, salary FROM teachers";

// If the user typed a search term, add a WHERE clause to filter by full_name
if (!empty($searchTerm)) {
    $sql .= " WHERE full_name LIKE ?";
}

// --------------------------
// Execute the Query
// --------------------------
// If a search term exists, use a prepared statement to securely inject the search term
if (!empty($searchTerm)) {
    $stmt = $conn->prepare($sql);  // Prepare the SQL query
    if (!$stmt) {
        die("Prepared Statement Error: " . $conn->error);
    }
    // Add wildcard characters (%) to allow partial matching
    $param = "%" . $searchTerm . "%";
    // Bind the parameter as a string ("s")
    $stmt->bind_param("s", $param);
    $stmt->execute();                // Execute the statement
    $result = $stmt->get_result();   // Get the result set from the executed statement
} else {
    // If no search term provided, run the query normally
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
    <style>
        /* ---------- Overall Page Styling ---------- */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f0faff; /* Light blue background */
        }
        /* ---------- Header Styling ---------- */
        .header {
            background-color: #004080; /* Dark blue header */
            color: #fff;
            padding: 20px 0;
            text-align: center;
        }
        /* ---------- Navigation Bar Section ---------- */
        .navbar {
            background-color: #0073e6; /* Bright blue navigation bar */
            padding: 10px;
            text-align: center;
        }
        /* Each link in the navbar is styled as follows */
        .navbar a {
            color: #fff;                 /* White text */
            text-decoration: none;       /* No underline */
            margin: 0 20px;              /* Spacing between links */
            font-size: 18px;             /* Text size */
            font-weight: bold;           /* Bold text */
        }
        /* Hover effect and active link styling */
        .navbar a:hover,
        .navbar a.active {
            text-decoration: underline;
        }
        /* ---------- Main Content Container ---------- */
        .container {
            width: 90%;
            max-width: 1000px;
            background: #fff;            /* White background for content */
            margin: 30px auto;           /* Center the container horizontally */
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1); /* Subtle shadow effect */
        }
        /* ---------- Search Form Styling ---------- */
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
        /* ---------- Section Title Styling ---------- */
        .section-title {
            color: #004080;
            text-align: center;
            margin-bottom: 20px;
        }
        /* ---------- Teacher Record Card Styling ---------- */
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
    <!-- ---------- Page Header ---------- -->
    <div class="header">
        <h1>St. Alphonsus School Portal</h1>
    </div>
    
    <!-- ---------- Navigation Bar Section ---------- -->
    <div class="navbar">
        <!-- The active link is set with a class="active" -->
        <a href="index.php" class="active">Students</a>
        <a href="teachers.php">Teachers</a>
        <a href="parents.php">Parents</a>
        <a href="classes.php">Classes</a>
        <a href="add_student.php">Add New Student</a>
    </div>
    
    <!-- ---------- Main Content Container ---------- -->
    <div class="container">
        <!-- ---------- Search Form ---------- -->
        <div class="search-form">
            <!-- Form submits via GET to teachers.php; the search term is kept in "q" -->
            <form action="teachers.php" method="get">
                <input type="text" name="q" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Search Teachers">
                <button type="submit">Search</button>
            </form>
        </div>
        
        <!-- ---------- Section Title ---------- -->
        <h1 class="section-title">Teachers</h1>
        
        <!-- ---------- Display Teacher Records as "Cards" ---------- -->
        <?php
        // Check if the query returned any results
        if ($result) {
            if ($result->num_rows > 0) {
                // Loop through each record
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="record-card">';
                    // Display teacher's full name and ID
                    echo '<h3>' . htmlspecialchars($row['full_name']) . ' (ID: ' . htmlspecialchars($row['teacher_id']) . ')</h3>';
                    // Display additional details: address, phone, and salary
                    echo '<p><strong>Address:</strong> ' . htmlspecialchars($row['address']) . '</p>';
                    echo '<p><strong>Phone:</strong> ' . htmlspecialchars($row['phone']) . '</p>';
                    echo '<p><strong>Salary:</strong> ' . htmlspecialchars($row['salary']) . '</p>';
                    echo '</div>';
                }
            } else {
                // If no matching records were found
                echo '<p style="text-align: center;">No records found.</p>';
            }
        } else {
            // If the query failed to execute properly
            echo '<p style="text-align: center;">Error executing query.</p>';
        }
        
        // --------------------------
        // Cleanup: Close the prepared statement if one was used, then close the database connection.
        if (!empty($searchTerm) && isset($stmt)) {
            $stmt->close();
        }
        $conn->close();
        ?>
    </div>
</body>
</html>
