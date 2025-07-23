<?php
include("connection.php");

// PayU credentials (live)
$key  = "LQjxq9";
$salt = "wvpS1GpefbVZxE6zMdHiMYvn3I2Q2mVy";

// Collect PayU response
$response = $_REQUEST;
file_put_contents("payu_response_log.txt", print_r($response, true));

// Extract fields
$status      = $response['status'];
$txnid       = $response['txnid'];
$amount      = $response['amount'];
$payu_id     = $response['mihpayid'];
$productinfo = $response['productinfo'];
$firstname   = $response['firstname'];
$email       = $response['email'];
$phone       = $response['phone'];
$userid      = $response['udf1'];
$movie_id     = $response['udf2'];
$posted_hash = $response['hash'];
$payment_time = date('Y-m-d H:i:s');

// Hash validation
$hash_string = "$key|$txnid|$amount|$productinfo|$firstname|$email|$userid|$plan_id|||||||||$salt";
$calculated_hash = strtolower(hash("sha512", $hash_string));

// if ($posted_hash !== $calculated_hash) {
//     die("❌ Hash Mismatch – Possible tampering.");
// }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Status</title>
    <link rel="icon" href="https://mtadmin.online/sensewheel/images/Layer%201.png" sizes="16x16">
    <style>
        body {
            background: #f4f4f4;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .success-circle {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background-color: #28a745;
    color: white;
    font-size: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 20px auto;
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
}
        .status-container {
            background: #fff;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
            width: 90%;
        }
        .status-container h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .status-container p {
            font-size: 16px;
            margin: 5px 0;
        }
        .success {
            color: green;
        }
        .fail {
            color: red;
        }
        .btn-home {
            margin-top: 20px;
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
        }
        .btn-home:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div class="status-container">
       <?php
if ($status == "success") {
    
    $check_mov = "SELECT id FROM tbl_buymovies WHERE userid = '$userid' and movie_id='$movie_id' and expire=0 ";
    $check_mov_result = mysqli_query($mysqli, $check_mov);
    if(mysqli_num_rows($check_mov_result)==0){
    // Check if txnid already exists
    $check_txn = "SELECT id FROM tbl_buymovies WHERE txnid = '$txnid' and payu_id='$payu_id' ";
    $check_result = mysqli_query($mysqli, $check_txn);

    if (mysqli_num_rows($check_result) == 0) {
        
        $date = new DateTime("now", new DateTimeZone("Asia/Kolkata"));
            $dd = $date->format('Y-m-d H:i:s'); 
        // Insert payment record
        $insert = "INSERT INTO tbl_buymovies 
            (userid, movie_id, txnid, amount, payu_id, payment_status,purchase_dt,type,payment_time) 
            VALUES 
            ('$userid', '$movie_id', '$txnid', '$amount', '$payu_id', '$status','$dd', 'rent','$payment_time')";

        if (mysqli_query($mysqli, $insert)) {
            

           
            ?>
           

                <div class="success-circle">
                &#10003;
            </div>

            <h1><strong><?php echo $productinfo; ?> Movie Buy Successfully</strong> </h1>
            <p><strong>Transaction ID:</strong> <?php echo $txnid; ?></p>
            <p><strong>Amount:</strong> ₹<?php echo $amount; ?></p>
            
            <p><strong>You Can Close the window</strong></p>
            <?php
        } else {
            echo "<h3 class='fail'>❌ DB Error: " . mysqli_error($mysqli) . "</h3>";
        }
    } else {
        echo "<h2 class='success'>✅ Payment Already Processed!</h2>";
        echo "<p><strong>Transaction ID:</strong> $txnid</p>";
        echo "<p><strong>You Can Close the window</strong></p>";
    }
    
    }else{
        echo "<h2 class='success'> You Already Buy this Movie !</h2>";
        echo "<p><strong>Transaction ID:</strong> $txnid</p>";
        echo "<p><strong>You Can Close the window</strong></p>";
    }
} else {
    echo "<h2 class='fail'>❌ Payment Failed or Cancelled!</h2>";
}
?>

    
    </div>
</body>
</html>
