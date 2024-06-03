<?php
// Include your database connection file
include('fetch_data.php');

$id = $_POST['id'];
// Escape the $id variable to prevent SQL injection
$id = mysqli_real_escape_string($conn, $id);

$sql = "SELECT DISTINCT school FROM polytechnics WHERE Polytechnic_name = '$id'";
$result = mysqli_query($conn, $sql);

$out ='';
$out .= '<option value="" selected>Select School</option>';
while($row = mysqli_fetch_assoc($result))
{
    $out .= '<option>'.$row['school'].'</option>';
}
echo $out;
?>
