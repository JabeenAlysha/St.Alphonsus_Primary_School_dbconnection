<?php
// ================================
// parents.php
// This page displays Parents information and allows searching by the parent's full name.
// ================================

// Enable error reporting (for development only)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// --------------------------
// Database Connection Setup
// --------------------------
$host     = 'localhost';
$user     = 'root';
$password = '';
$database = 'StAlphonsus_Primary_school_system';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --------------------------
// Handle the Search Function
// --------------------------
$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';

// Build the SQL query to select parent records
$sql = "SELECT parent_id, full_name, phone, email FROM parents";
// If a search term is provided, filter by full_name
if (!empty($searchTerm)) {
    $sql .= " WHERE full_name LIKE ?";
}

// --------------------------
// Execute the Query
// --------------------------
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
    <title>Parents - St. Alphonsus School Portal</title>
    <style>
        /* ---------- Overall Styling ---------- */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f0faff;
        }
        /* ---------- Header Styling ---------- */
        .header {
            background-color: #004080;
            color: #fff;
            padding: 20px 0;
            text-align: center;
        }
        /* ---------- Navigation Bar ---------- */
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
        /* ---------- Main Content Container ---------- */
        .container {
            width: 90%;
            max-width: 1000px;
            background: #fff;
            margin: 30px auto;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        /* ---------- Search Form ---------- */
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
        /* ---------- Section Title & Record Card Styling ---------- */
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
    <!-- ---------- Header ---------- -->
    <div class="header">
        <h1>St. Alphonsus School Portal</h1>
    </div>
    <!-- ---------- Navigation Bar ---------- -->
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
            <form action="parents.php" method="get">
                <input type="text" name="q" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Search Parents">
                <button type="submit">Search</button>
            </form>
        </div>
        <!-- ---------- Section Title ---------- -->
        <h1 class="section-title">Parents</h1>
        <!-- ---------- Display Parent Records ---------- -->
        <?php
        if ($result) {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="record-card">';
                    // Show parent's full name and ID
                    echo '<h3>' . htmlspecialchars($row['full_name']) . ' (ID: ' . htmlspecialchars($row['parent_id']) . ')</h3>';
                    // Display parent's phone number and email
                    echo '<p><strong>Phone:</strong> ' . htmlspecialchars($row['phone']) . '</p>';
                    echo '<p><strong>Email:</strong> ' . htmlspecialchars($row['email']) . '</p>';
                    echo '</div>';
                }
            } else {
                echo '<p style="text-align: center;">No records found.</p>';
            }
        } else {
            echo '<p style="text-align: center;">Error executing query.</p>';
        }
        // Cleanup
        if (!empty($searchTerm) && isset($stmt)) {
            $stmt->close();
        }
        $conn->close();
        ?>
    </div>
</body>
</html>
