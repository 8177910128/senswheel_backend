<?php
// PayU credentials
$key  = "LQjxq9";
$salt = "wvpS1GpefbVZxE6zMdHiMYvn3I2Q2mVy";

// Collect PayU response
$response = $_REQUEST;
file_put_contents("payu_response_log.txt", print_r($response, true)); // For debugging

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
$plan_id     = $response['udf2'];
$error_msg   = $response['error_Message'] ?? '';
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
    .status-container {
      background: #fff;
      padding: 30px 40px;
      border-radius: 15px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      text-align: center;
      max-width: 500px;
      width: 90%;
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
    }
    .fail-circle {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      background-color: #dc3545;
      color: white;
      font-size: 50px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 20px auto;
    }
    h2 {
      margin-top: 10px;
    }
    .btn-home {
      margin-top: 20px;
      display: inline-block;
      background: #007bff;
      color: white;
      padding: 10px 20px;
      border-radius: 8px;
      text-decoration: none;
    }
  </style>
</head>
<body>
  <div class="status-container">
    <?php if ($status === "success") { ?>
      <div class="success-circle">&#10003;</div>
      <h2>Payment Successful</h2>
      <p><strong>Transaction ID:</strong> <?= htmlspecialchars($txnid) ?></p>
      <p><strong>Amount:</strong> ₹<?= htmlspecialchars($amount) ?></p>
      <p><strong>Name:</strong> <?= htmlspecialchars($firstname) ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
      <p><strong>Phone:</strong> <?= htmlspecialchars($phone) ?></p>
    <?php } else { ?>
      <div class="fail-circle">&#10005;</div>
      <h2>Failed to buy <?php echo $productinfo ;?>  Movie</h2>
      <p><strong>Transaction ID:</strong> <?= htmlspecialchars($txnid) ?></p>
      <p><strong>Status:</strong> <?= htmlspecialchars($status) ?></p>
      <p><strong>Reason:</strong> <?= htmlspecialchars($error_msg ?: 'Unknown error') ?></p>
    <?php } ?>
    
  </div>
</body>
</html>
