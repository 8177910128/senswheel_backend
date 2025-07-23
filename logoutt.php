<?php
include("includes/connection.php");

// Get user_id from URL
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($user_id > 0) {
    // DELETE query
    $delete_qry = "DELETE FROM tbl_device_info WHERE userid = '$user_id'";
    $result = mysqli_query($mysqli, $delete_qry);
    
    
     $update_qry = "UPDATE `tbl_users` SET `device_limit`='0' WHERE id = '$user_id'";
    $resultup = mysqli_query($mysqli, $update_qry);

    if ($result) {
        // Optional: destroy session
        

        // Optional: redirect or show success
        header("Location: manage_users.php");
        exit();
    } else {
        echo "❌ Error deleting device info: " . mysqli_error($mysqli);
    }
} else {
    echo "❌ Invalid User ID";
}
?>
