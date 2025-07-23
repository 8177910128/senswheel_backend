<?php 
	error_reporting(0);
	ob_start();
	session_start();

	header("Content-Type: text/html;charset=UTF-8");


	if($_SERVER['HTTP_HOST']==="mtadmin.online" or $_SERVER['HTTP_HOST']==="localhost")
	{
		DEFINE ('DB_USER', 'u293792719_sensewheel');
		DEFINE ('DB_PASSWORD', 'senseWheel@123$$');
		DEFINE ('DB_HOST', 'localhost');
		DEFINE ('DB_NAME', 'u293792719_sensewheel');
	}
	else
	{
		DEFINE ('DB_USER', 'root');
		DEFINE ('DB_PASSWORD', '');
		DEFINE ('DB_HOST', 'localhost');
		DEFINE ('DB_NAME', 'ott');
	}


	$mysqli =mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);

	if ($mysqli->connect_errno) 
	{
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}

	mysqli_query($mysqli,"SET NAMES 'utf8'");	 

	//Settings
	$setting_qry="SELECT * FROM tbl_settings where id='1'";
	$setting_result=mysqli_query($mysqli,$setting_qry);
	$settings_details=mysqli_fetch_assoc($setting_result);

	define("ONESIGNAL_APP_ID",$settings_details['onesignal_app_id']);
	define("ONESIGNAL_REST_KEY",$settings_details['onesignal_rest_key']);

	define("APP_NAME",$settings_details['app_name']);
	define("APP_LOGO",$settings_details['app_logo']);
	define("APP_FROM_EMAIL",$settings_details['email_from']);

	define("API_LATEST_LIMIT",$settings_details['api_latest_limit']);
	define("API_CAT_ORDER_BY",$settings_details['api_cat_order_by']);
	define("API_CAT_POST_ORDER_BY",$settings_details['api_cat_post_order_by']);

	//Profile
	if(isset($_SESSION['id']))
	{
		$profile_qry="SELECT * FROM tbl_admin where id='".$_SESSION['id']."'";
		$profile_result=mysqli_query($mysqli,$profile_qry);
		$profile_details=mysqli_fetch_assoc($profile_result);

		define("PROFILE_IMG",$profile_details['image']);
	}

?>