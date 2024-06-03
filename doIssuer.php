<?php
session_start();
include('fetch_data.php'); // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form data
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $nric = mysqli_real_escape_string($conn, $_POST['nric']);

    // Optional fields
    $polytechnic = isset($_POST['polytechnic']) ? mysqli_real_escape_string($conn, $_POST['polytechnic']) : null;
    $school = isset($_POST['school']) ? mysqli_real_escape_string($conn, $_POST['school']) : null;
    $diploma = isset($_POST['diploma']) ? mysqli_real_escape_string($conn, $_POST['diploma']) : null;

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Handle Polytechnic ID
    $polytechnics_id = null;
    if (!empty($polytechnic) && !empty($school) && !empty($diploma)) {
        // Check if the polytechnic, school, and diploma combination exists
        $query = "SELECT Polytechnics_Id FROM polytechnics WHERE Polytechnic_name='$polytechnic' AND school='$school' AND diploma='$diploma'";
        $result = mysqli_query($conn, $query);
        if (!$result) {
            die('Error with SELECT query: ' . htmlspecialchars(mysqli_error($conn)));
        }
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $polytechnics_id = $row['Polytechnics_Id'];
        } else {
            // Insert new Polytechnic, School, and Diploma combination if it doesn't exist
            $insert_poly = "INSERT INTO polytechnics (Polytechnic_name, school, diploma) VALUES ('$polytechnic', '$school', '$diploma')";
            if (!mysqli_query($conn, $insert_poly)) {
                die('Error with INSERT query: ' . htmlspecialchars(mysqli_error($conn)));
            }
            $polytechnics_id = mysqli_insert_id($conn);
        }
    }

    // Prepare the SQL statement
    $insert_issuer = "INSERT INTO issuer (Polytechnics_id, Issuer_name, Issuer_NRIC, Issuer_email, Issuer_password) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insert_issuer);
    
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars(mysqli_error($conn)));
    }

    // Bind the parameters
    mysqli_stmt_bind_param($stmt, 'issss', $polytechnics_id, $name, $nric, $email, $hashed_password);

    // Execute the statement
    if (mysqli_stmt_execute($stmt)) {
        echo "New issuer record created successfully";
    } else {
        echo "Error: " . $insert_issuer . "<br>" . htmlspecialchars(mysqli_error($conn));
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>
