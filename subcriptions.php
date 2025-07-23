<?php
include("includes/connection.php");

$userid = @$_GET['userid'];

// Fetch subscription plans
$sql = "SELECT `id`, `plan`, `validity`, `description`, `price`, `discount_price` 
        FROM `tbl_subscription` 
        WHERE `deleted` = 0 AND `status` = 1 
        ORDER BY `id` ASC";
$result = mysqli_query($mysqli, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Subscription Cards</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="<?php echo APP_LOGO;?>" sizes="16x16">
  <meta name="description" content="Watch the latest movies, TV shows, and originals on SensWheel. Stream anytime, anywhere on your favorite devices.">
  <meta name="keywords" content="SensWheel, OTT, streaming, movies, TV shows, web series, entertainment">
  <meta name="author" content="SensWheel Team">

  <!-- Open Graph -->
  <meta property="og:type" content="website">
  <meta property="og:title" content="SensWheel - Watch the Best OTT Content Online">
  <meta property="og:description" content="Stream movies, web series, and more on SensWheel. Unlimited entertainment on demand.">
  <meta property="og:image" content="https://mtadmin.online/sensewheel/images/Layer%201.png">
  <meta property="og:url" content="https://mtadmin.online/sensewheel/subcriptions.php">
  <meta property="og:site_name" content="SensWheel">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #111;
      color: #fff;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      padding-top: 90px; /* Space for fixed logo */
    }

    .card-subscription {
      border-radius: 16px;
      padding: 20px;
      color: white;
      margin-bottom: 20px;
      cursor: pointer;
    }
    .d-inline-block {
        margin-left : 20px;   
    }
    .premium-plus {
      background: linear-gradient(to right, #a64bf4, #6e37ec);
    }

    .plus {
      background: linear-gradient(to right, #00d2ff, #3a7bd5);
    }

    .price {
      font-size: 20px;
      font-weight: bold;
      float: right;
    }

    .title {
      font-size: 20px;
      font-weight: bold;
    }

    .desc {
      font-size: 13px;
      opacity: 0.8;
      margin-top: 5px;
    }

    footer a {
      color: #fff;
      text-decoration: none;
      margin-right: 15px;
    }

    footer a:hover {
      text-decoration: underline;
      color: #fff;
    }
  </style>
</head>
<body>

<!-- Top Logo Section -->
<div class="text-left py-3 bg-dark fixed-top" style="z-index: 1050;">
  <a href="#" class="d-inline-block">
    <img src="https://mtadmin.online/sensewheel/images/Layer%201.png" alt="Logo" height="50">
  </a>
</div>

<!-- Main Content -->
<div class="container py-5">
  <h3 class="mb-4">Subscription</h3>

  <?php while ($plan = mysqli_fetch_assoc($result)):
    $class = '';
    if (stripos($plan['plan'], 'premium') !== false) {
      $class = 'premium-plus';
    } elseif (stripos($plan['plan'], 'plus') !== false) {
      $class = 'plus';
    } else {
      $class = 'premium-plus'; // fallback
    }
  ?>
    <form action="subscribe_now.php" method="post" class="card-form">
      <input type="hidden" name="userid" value="<?= $userid ?>">
      <input type="hidden" name="plan_id" value="<?= $plan['id'] ?>">

      <div class="card-subscription <?= $class ?>">
        <div class="d-flex justify-content-between">
          <div class="title"><?= htmlspecialchars($plan['plan']) ?></div>
          <div class="price">
            ₹<?= $plan['discount_price'] && $plan['discount_price'] < $plan['price']
                ? $plan['discount_price'] . ' <small class="text-decoration-line-through text-muted">₹' . $plan['price'] . '</small>'
                : $plan['price'] ?>/m
          </div>
        </div>
        <div class="desc">
          <?= nl2br(htmlspecialchars($plan['description'])) ?>
        </div>
      </div>
    </form>
  <?php endwhile; ?>
</div>

<!-- Footer Links -->
<footer class="bg-dark text-center text-white py-3 mt-5">
  <div class="container">
    <a href="terms.php">Terms & Conditions</a>
    <a href="privacy.php">Privacy Policy</a>
    <a href="help.php">Help</a>
    <a href="disclaimer.php">Disclaimer</a>
  </div>
</footer>

<!-- Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Click to submit on card -->
<script>
  document.querySelectorAll('.card-form').forEach(function(form) {
    form.addEventListener('click', function() {
      this.submit();
    });
  });
</script>

</body>
</html>
