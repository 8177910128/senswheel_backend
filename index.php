
<?php
  
  include("includes/connection.php");
	include("language/language.php");

	if(isset($_SESSION['admin_name']))
  {
    header("Location:home.php");
    exit;
  }
?>
<!DOCTYPE html>
<html>
<head>
<meta name="author" content="">
<meta name="description" content="">
<meta http-equiv="Content-Type"content="text/html;charset=UTF-8"/>
<meta name="viewport"content="width=device-width, initial-scale=1.0">
<title><?php echo APP_NAME;?> | Login</title>
<link rel="icon" href="<?php echo APP_LOGO;?>" sizes="16x16">
<link rel="stylesheet" type="text/css" href="assets/css/vendor.css">
<link rel="stylesheet" type="text/css" href="assets/css/flat-admin.css">

<!-- Theme -->
<link rel="stylesheet" type="text/css" href="assets/css/theme/blue-sky.css">
<link rel="stylesheet" type="text/css" href="assets/css/theme/blue.css">
<link rel="stylesheet" type="text/css" href="assets/css/theme/red.css">
<link rel="stylesheet" type="text/css" href="assets/css/theme/yellow.css">
<style>
    body {
  margin: 0;
  padding: 0;
  display: flex;
  height: 100vh;
  align-items: center;
  justify-content: center;
  background-color: #f0f4f8;
}

.login-wrapper {
  display: flex;
  width: 80%;
  height: 80vh;
  max-width: 1200px;
  background-color: white;
   border-radius: 25px;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
}

.login-form-container {
  flex: 0.4; /* Reduced width */
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 40px;
  background-color: #f5f7fa;
   border-radius: 25px 0px 0px 25px;
  text-align: center;
}

.login-form-container form {
  width: 100%; /* Full width for better alignment */
}

.login-form-container .app-brand {
  margin-bottom: 10px;
}

.login-form-container .app-brand img {
  width: 150px;
  margin-bottom: 10px;
}

.login-form-container h2 {
  font-size: 24px;
  margin-bottom: 20px;
  color: #333;
}



.input-group {
  margin-bottom: 20px;
  border-radius: 10px;
}

.input-group input {
  padding: 10px;
  border-radius: 15px;
}

.btn-submit {
  background-color: #612BAD;
  color: white;
  width: 100%;
  padding: 12px;
  border: none;
  border-radius: 5px;
}

.image-container {
  flex: 0.6; /* Increased width */
  background-image: url('uploads/Frame 58.png');
  background-size: cover;
  background-position: center;
  border-radius: 25px;
}

.btn:hover, .btn:focus, .btn.focus {
    color: white;
    text-decoration: none;
}
@media (max-width: 768px) {
  .login-wrapper {
    flex-direction: column;
    height: auto;
  }
  .login-form-container, .image-container {
    width: 100%;
    height: 300px;
  }
}

</style>

</head>
<body>
<div class="login-wrapper">
  <!-- Left: Login Form Section -->
  <div class="login-form-container">
    <div class="app-brand">
      <img src="<?php echo APP_LOGO;?>" alt="Logo">
    </div>

    <h2><b>Login</b></h2>
    

    <form action="login_db.php" method="post">
      
        <?php if(isset($_SESSION['msg'])){?>
        <div class="alert alert-info alert-dismissible" role="alert">
          <?php echo $client_lang[$_SESSION['msg']]; ?>
        </div>
        <?php unset($_SESSION['msg']);}?>
     

      <div class="input-group">
        <span class="input-group-addon" id="basic-addon1">
          <i class="fa fa-user" aria-hidden="true"></i>
        </span>
        <input type="text" name="username" id="username" class="form-control" placeholder="Username" required/>
      </div>

      <div class="input-group">
          <span class="input-group-addon"  style="cursor: pointer;">
            <i class="fa fa-eye" aria-hidden="true"  id="togglePassword"></i>
          </span>
          <input type="password" name="password" id="id_password" class="form-control" placeholder="Password" required/>
     </div>


      <div class="text-center">
        <input type="submit" class="btn btn-submit" value="Login">
      </div>
    </form>
  </div>

  <!-- Right: Image Section -->
  <div class="image-container"></div>
</div>
<script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#id_password');

         togglePassword.addEventListener('click', function (e) {
        // toggle the type attribute
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        // toggle the eye slash icon
        this.classList.toggle('fa-eye-slash');
         });
     </script>


</body>
</html>