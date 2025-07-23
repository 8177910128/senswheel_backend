<?php
include("includes/connection.php");

$user_id = intval($_POST['user_id']);

if ($user_id > 0) {
    $qry = "SELECT u.name, u.plan_buy_date, s.name AS plan_name, s.validity 
            FROM tbl_users u
            LEFT JOIN tbl_subscription s ON u.plan_id = s.id
            WHERE u.id = '$user_id'";
    $res = mysqli_query($mysqli, $qry);

    if ($row = mysqli_fetch_assoc($res)) {
        echo "<p><strong>User Name:</strong> " . $row['name'] . "</p>";
        echo "<p><strong>Plan Name:</strong> " . $row['plan_name'] . "</p>";
        echo "<p><strong>Validity:</strong> " . $row['validity'] . " days</p>";
        echo "<p><strong>Plan Buy Date:</strong> " . $row['plan_buy_date'] . "</p>";
    } else {
        echo "No subscription data found.";
    }
} else {
    echo "Invalid user ID.";
}
?>
