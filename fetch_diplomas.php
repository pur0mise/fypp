<?php
// Include your database connection file
include('fetch_data.php');

// Check if the POST variable id2 is set
if(isset($_POST['id2'])) {
    // Get the selected school from the POST data
    $id2 = $_POST['id2'];

    // Escape the $id2 variable to prevent SQL injection
    $id2 = mysqli_real_escape_string($conn, $id2);

    // Query to fetch diplomas based on the selected school
    $sql = "SELECT DISTINCT diploma FROM polytechnics WHERE school = '$id2'";
    $result = mysqli_query($conn, $sql);

    $out ='';
    $out .= '<option value="" selected>Select Diploma</option>';
    while($row = mysqli_fetch_assoc($result))
    {
        $out .= '<option>'.$row['diploma'].'</option>';
    }
    echo $out;
} else {
    echo "Error: School ID not set";
}
?>
