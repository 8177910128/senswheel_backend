<?php

include("connection.php");

// ====== PayU Live Credentials from Dashboard ======
$key       = "LQjxq9";
$salt      = "wvpS1GpefbVZxE6zMdHiMYvn3I2Q2mVy"; // Salt 32-bit
$txnid     = uniqid("txn_"); // Unique txn ID

$userid = $_GET['userid'];
$movie_id = $_GET['movie_id'];

if (!$userid || !$movie_id) {
    die("Invalid request.");
}

$checkbuy="SELECT * FROM tbl_buymovies WHERE movie_id = '$movie_id' and userid='$userid' and expire=0";
$buy_result = mysqli_query($mysqli, $checkbuy);
if (mysqli_num_rows($buy_result) > 0) {
  die("you already buy this movie.");   
    
}


$user_qry = "SELECT * FROM tbl_users WHERE id = '$userid'";
$user_result = mysqli_query($mysqli, $user_qry);
$user_row = mysqli_fetch_assoc($user_result);

$plan_qry = "SELECT * FROM tbl_rentmovies WHERE id = '$movie_id'";
$plan_result = mysqli_query($mysqli, $plan_qry);
$plan_row = mysqli_fetch_assoc($plan_result);

// ====== Payment Details ======
$amount    = $plan_row['movie_price'];
$productinfo = $plan_row['movie_title'];
$firstname = $user_row['name']; // Replace with dynamic value
$email     = $user_row['email'];// Replace with dynamic value
$phone     = $user_row['phone']; // Replace with dynamic 
$udf1          = $user_row["id"] ;
$udf2 = $movie_id;



// ====== Redirect URLs ======
$surl = "https://mtadmin.online/sensewheel/pay/payment-success.php";
$furl = "https://mtadmin.online/sensewheel/pay/payment-failure.php";

// ====== Optional UDFs (can be blank) ======
  $udf3 = $udf4 = $udf5 = "";

// ====== Generate Hash ======
$hash_string = "$key|$txnid|$amount|$productinfo|$firstname|$email|$udf1|$udf2|$udf3|$udf4|$udf5||||||$salt";
$hash = strtolower(hash("sha512", $hash_string));

// ====== PayU Live URL ======
$payu_url = "https://secure.payu.in/_payment";
?>

<!-- ====== PayU Auto Submit Form ====== -->
<form id="payuForm" method="post" action="<?= $payu_url ?>">
    <input type="hidden" name="key" value="<?= $key ?>">
    <input type="hidden" name="txnid" value="<?= $txnid ?>">
    <input type="hidden" name="amount" value="<?= $amount ?>">
    <input type="hidden" name="productinfo" value="<?= $productinfo ?>">
    <input type="hidden" name="firstname" value="<?= $firstname ?>">
    <input type="hidden" name="email" value="<?= $email ?>">
    <input type="hidden" name="phone" value="<?= $phone ?>">
    <input type="hidden" name="surl" value="<?= $surl ?>">
    <input type="hidden" name="furl" value="<?= $furl ?>">
    <input type="hidden" name="udf1" value="<?= $udf1 ?>">
    <input type="hidden" name="udf2" value="<?= $udf2 ?>">
    <input type="hidden" name="hash" value="<?= $hash ?>">
</form>

<script>
    document.getElementById('payuForm').submit();
</script>
