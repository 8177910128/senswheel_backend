<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require_once ('log.php');
date_default_timezone_set('Asia/Kolkata');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With,Authorization");
header('Content-Type: application/json; charset=utf-8');

$func = @$_REQUEST['func'];
$fdate=date('Y-m-d H:i:s');
$file_path = getBaseUrl();
//***************************************************************************************************************************************************************************************************************************************************************************************************
switch ($func) {
	// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		case "getSettings" :
	 	   
	 	  $getdata= getRecord("SELECT  * FROM tbl_settings WHERE id=1");
	        if($getdata){
	           $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Data Loaded Soccessfully','data'=>$getdata);
	        }else{
	           $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'No data found');  
	        }   
	     echo json_encode($response);
        exit();
		break;
		
	
 // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
     case "register" :
        $full_name = @$_POST['full_name'];
        $email = @$_POST['email'];
        $phone = @$_POST['phone'];
        $password = @$_POST['password'];
        $token = @$_POST['token'];
        $device_info = trim(@$_POST['device_info'], '{}');

        
        if(isset($full_name) && isset($email) && isset($phone) && isset($password)){
            $checkemail = getRecord("SELECT email FROM tbl_users WHERE email = '$email'"); 
            if(!empty($checkemail)){
                $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' => 'Email Already Exist');
                echo json_encode($response);
                exit(); 
            }
            
            $checkphone = getRecord("SELECT phone FROM tbl_users WHERE phone = '$phone'"); 
            if(!empty($checkphone)){
                $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' => 'Mobile Already Exist');
                echo json_encode($response);
                exit(); 
            }
            
            $refferal_code = generateRandomString(7);
            
            // Insert into database with device_info
            $insertQuery = query("INSERT INTO `tbl_users`(`user_type`, `name`, `email`, `password`, `phone`,  `refferal_code`, `register_on`)  VALUES ('user','$full_name','$email','$password','$phone','$refferal_code','$fdate')");
             $userid = last_insert_id();
             $insertQuery = query("INSERT INTO `tbl_device_info`(`userid`, `phone`, `token`, `device_info`)  VALUES ('$userid', '$phone','$token', '$device_info')");
             $updatecode = query("UPDATE `tbl_users` SET `device_limit` = `device_limit` + 1  WHERE phone='$phone'");
            if($insertQuery){
                $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' => 'User Registered Successfully', 'userid' => $userid);  
            }
        } else {
            $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' => 'Missing required params');     
        }
        
        echo json_encode($response);
        exit();   
    break;

	// -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
       /* case "loginWithEmail" :
        
            $email = @$_POST['email'];
    	    $password =  @$_POST['password'];
    	    $token= @$_POST['token'];
    	    $device_info = trim(@$_POST['device_info'], '{}');

    	    if(isset($email) && isset($password)){
                $getlogin = getRecord("SELECT * FROM tbl_users WHERE email='$email' and  password='$password'  and status=1");
               
                if ($getlogin) {
                     $userId = $getlogin['id'];
                     $phone=$getlogin['phone'];
                      $deviceCount = getRecordField("SELECT device_limit FROM tbl_users WHERE id='$userId'");
                    if ($deviceCount < 2) { // Allow login only if devices are less than 2
                        // Insert device info
                        $insertQuery = query("INSERT INTO `tbl_device_info`(`userid`,`token`, `phone`, `device_info`) 
                                              VALUES ('$userId','$token', '$phone', '$device_info')");
        
                        // Update device limit
                        $updatecode = query("UPDATE `tbl_users` SET `device_limit` = `device_limit` + 1   WHERE phone='$phone'");
        
                        // Fetch updated user data
                        $getdata = getRecord("SELECT * FROM tbl_users WHERE phone='$phone' AND status=1");
        
                        $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' => 'User Login Successfully', 'user_data' => $getdata);
                    } else {
                        $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' => 'Device limit exceeded. You can log in on up to 2 devices.');
                    }
                }else{
                    $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid Credentials');   
                }
            }else{
                $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'missing required params');  
            }
            
        echo json_encode($response);
        exit();   
		break;*/
		
		
		
		case "loginWithEmail":

    $email = @$_POST['email'];
    $password = @$_POST['password'];
    $token = @$_POST['token'];
    $device_info = trim(@$_POST['device_info'], '{}');

    if (isset($email) && isset($password)) {
        $getlogin = getRecord("SELECT * FROM tbl_users WHERE email='$email' AND password='$password' AND status=1 and deleted=0");

        if ($getlogin) {
            $userId = $getlogin['id'];
            $phone = $getlogin['phone'];
            $deviceCount = getRecordField("SELECT device_limit FROM tbl_users WHERE id='$userId'");

            if ($deviceCount < 2) {
                // Check if token already exists for this user
                $tokenExists = getRecord("SELECT * FROM tbl_device_info WHERE token='$token' AND userid='$userId'");

                if (!$tokenExists) {
                    // Insert new device info
                    $insertQuery = query("INSERT INTO `tbl_device_info`(`userid`, `token`, `phone`, `device_info`) 
                                          VALUES ('$userId', '$token', '$phone', '$device_info')");

                    // Update device limit
                    $updatecode = query("UPDATE `tbl_users` SET `device_limit` = `device_limit` + 1 WHERE phone='$phone'");

                    // Fetch updated user data
                    $getdata = getRecord("SELECT * FROM tbl_users WHERE phone='$phone' AND status=1");

                    $response = array(
                        'status' => true,
                        'error' => 0,
                        'success' => 1,
                        'msg' => 'User Login Successfully',
                        'user_data' => $getdata
                    );
                } else {
                    // Token already used
                    $response = array(
                        'status' => false,
                        'error' => 1,
                        'success' => 0,
                        'msg' => 'This device is already logged in.'
                    );
                }
            } else {
                // Device limit reached
                $response = array(
                    'status' => false,
                    'error' => 1,
                    'success' => 0,
                    'msg' => 'Device limit exceeded. You can log in on up to 2 devices.'
                );
            }
        } else {
            $response = array(
                'status' => false,
                'error' => 1,
                'success' => 0,
                'msg' => 'Invalid Credentials'
            );
        }
    } else {
        $response = array(
            'status' => false,
            'error' => 1,
            'success' => 0,
            'msg' => 'Missing required params'
        );
    }

    echo json_encode($response);
    exit();
    break;

		
		/*case "loginWithPhone":
            $device_info = trim(@$_POST['device_info'], '{}');
             $token= @$_POST['token'];
            if ($phone = @$_POST['phone']) {
                
                // Fetch user details
                $getlogin = getRecord("SELECT * FROM tbl_users WHERE phone='$phone' AND status=1");
                
                if ($getlogin) {
                    $userId = $getlogin['id'];
        
                    // Check existing device count
                     $deviceCount = getRecordField("SELECT device_limit FROM tbl_users WHERE id='$userId'");
           // print_r($deviceCount);die;
                    if ($deviceCount < 2) { // Allow login only if devices are less than 2
                        // Insert device info
                         $insertQuery = query("INSERT INTO `tbl_device_info`(`userid`,`token`, `phone`, `device_info`) 
                                              VALUES ('$userId','$token', '$phone', '$device_info')");
        
                        // Update device limit
                        $updatecode = query("UPDATE `tbl_users` SET `device_limit` = `device_limit` + 1   WHERE phone='$phone'");
        
                        // Fetch updated user data
                        $getdata = getRecord("SELECT * FROM tbl_users WHERE phone='$phone' AND status=1");
        
                        $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' => 'User Login Successfully', 'user_data' => $getdata);
                    } else {
                        $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' => 'Device limit exceeded. You can log in on up to 2 devices.');
                    }
                } else {
                    $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' => 'Invalid Credentials');
                }
            } else {
                $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' => 'Missing required params');
            }
            
            echo json_encode($response);
            exit();
            break;*/
            
            
            case "loginWithPhone":

    $device_info = trim(@$_POST['device_info'], '{}');
    $token = @$_POST['token'];
    $phone = @$_POST['phone'];

    if (isset($phone) && isset($device_info) && isset($token)) {

        // Fetch user details
        $getlogin = getRecord($query="SELECT * FROM tbl_users WHERE phone='$phone' AND status=1 and deleted=0");
        
       

        if ($getlogin) {
            $userId = $getlogin['id'];

            // Check existing device count
            $deviceCount = getRecordField("SELECT device_limit FROM tbl_users WHERE id='$userId'");

            if ($deviceCount < 2) {
                // Check if token already exists for this user
                $tokenExists = getRecord("SELECT * FROM tbl_device_info WHERE token='$token' AND userid='$userId'");

                if (!$tokenExists) {
                    // Insert device info
                    $insertQuery = query("INSERT INTO `tbl_device_info`(`userid`, `token`, `phone`, `device_info`) 
                                          VALUES ('$userId','$token', '$phone', '$device_info')");

                    // Update device limit
                    $updatecode = query("UPDATE `tbl_users` SET `device_limit` = `device_limit` + 1 WHERE phone='$phone'");

                    // Fetch updated user data
                    $getdata = getRecord("SELECT * FROM tbl_users WHERE phone='$phone' AND status=1");

                    $response = array(
                        'status' => true,
                        'error' => 0,
                        'success' => 1,
                        'msg' => 'User Login Successfully',
                        'user_data' => $getdata
                    );
                } else {
                    // Token already used
                    $response = array(
                        'status' => false,
                        'error' => 1,
                        'success' => 0,
                        'msg' => 'This device is already logged in.'
                    );
                }
            } else {
               $gettokensid=getRecordField("SELECT id FROM tbl_device_info WHERE userid='$userId' order by id desc limit 1 ");
               $updatecode = query("UPDATE `tbl_device_info` SET `token`='$token',`device_info`='$device_info' WHERE  phone='$phone' and  id ='$gettokensid'"); 
               $getdata = getRecord("SELECT * FROM tbl_users WHERE phone='$phone' AND status=1");

                    $response = array(
                        'status' => true,
                        'error' => 0,
                        'success' => 1,
                        'msg' => 'User Login Successfully',
                        'user_data' => $getdata
                    );
            }
        } else {
            $response = array(
                'status' => false,
                'error' => 1,
                'success' => 0,
                'msg' => 'Invalid Credentials'
            );
        }
    } else {
        $response = array(
            'status' => false,
            'error' => 1,
            'success' => 0,
            'msg' => 'Missing required params'
        );
    }

    echo json_encode($response);
    exit();
    break;


       
       	case "logout":
         if (!app_login(intval($userid = @$_POST['userid']))) {
            $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' => 'Invalid Userid or User Not Active');
            echo json_encode($response);
            exit();
        }
        $token= @$_POST['token'];
    
        if (!$userid || !$token) {
            $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' => 'Missing required parameters');
            echo json_encode($response);
            exit();
        }
    
        // Check if the user exists
        $user = getRecord("SELECT * FROM tbl_users WHERE id='$userid'");
    
        if (!$user) {
            $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' => 'Invalid User ID');
            echo json_encode($response);
            exit();
        }
    
        // Remove the specific device info entry
        $deleteDevice = query("DELETE FROM tbl_device_info WHERE userid='$userid' AND token='$token'");
    
        // Reduce device count
        $updateDeviceLimit = query("UPDATE tbl_users SET device_limit = GREATEST(device_limit - 1, 0) WHERE id='$userid'");
    
        if ($deleteDevice) {
            $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' => 'Logout Successfully');
        } else {
            $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' => 'Device Not Found');
        }
    
        echo json_encode($response);
        exit();
        break;
        
        
        case "deleteUser":
           if(!app_login(intval($userid = @$_POST['userid'])))
    		{
              $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid Userid or User Not Active');  
    			echo json_encode($response);
    			exit();
    		}  
    		
    		if($userid){
    		    
    		    $deletequery=query("UPDATE `tbl_users` SET `deleted`='1'  WHERE id='$userid' ");
    		    if($deletequery){
    		        $lquery=("DELETE FROM `tbl_device_info` WHERE userid= '$userid' ");
    		      $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' => 'Delete Successfully');  
    		    }else {
                    $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' => 'Something went wrong');
                }
    		    
    		}
            
        echo json_encode($response);
        exit();
        break;

   // ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
      /* case "getUserProfile" :
         
    	    if(!app_login(intval($userid = @$_POST['userid'])))
    		{
              $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid Userid or User Not Active');  
    			echo json_encode($response);
    			exit();
    		}  
    		
            $getdata = getRecord("SELECT * FROM tbl_users WHERE id='$userid'");
            if (!empty($getdata['device_info'])) {
                // Parse the 'device_info' string into an associative array
                $parsed_device_info = parseDeviceInfo($getdata['device_info']);
                // Add the parsed device info to the user data
                $getdata['device_info'] = $parsed_device_info;
            }
            if($getdata){
                $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Loaded Successfully','user_data'=>$getdata);
            }
           
   
        echo json_encode($response);
        exit();   
		break;*/
		
		
	case "getUserProfile":

    if (!app_login(intval($userid = @$_POST['userid']))) {
        $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' => 'Invalid Userid or User Not Active');
        echo json_encode($response);
        exit();
    }
    $token= @$_POST['token'];
    if (!$userid ) {
            $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' => 'Missing required parameters');
            echo json_encode($response);
            exit();
        }
    
    // Fetch user details
    $getdata = getRecord("SELECT * FROM tbl_users WHERE id='$userid'");
                    $plan_id = $getdata['plan_id'];
                    $plan_date = $getdata['plan_buy_date'];
               $validity_days = $getdata['plan_validation'] ; 

                $current_date = date('Y-m-d H:i:s');
                
                 $expire_date = date('Y-m-d H:i:s', strtotime("$plan_date + $validity_days days"));

            // Calculate remaining days
            $current_date = date('Y-m-d H:i:s');
            $remaining_days = (strtotime($expire_date) > strtotime($current_date)) ? date_diff(date_create($current_date), date_create($expire_date))->days : 0;
                $getdata['plan_validation'] = $validity_days . " days"; 
                $getdata['remaining_days'] = $remaining_days . " days"; 
                $getdata['plan_name'] = getRecordField("SELECT plan FROM tbl_subscription WHERE id='$plan_id'");
                $getdata['rent_page_link'] = 'https://mtadmin.online/sensewheel/pay/buy_now.php';
     $getdata['token'] = getRecordField("SELECT token FROM tbl_device_info WHERE token='$token'");
    // Fetch device info list
    $deviceInfoRecords = getRecords("SELECT device_info FROM tbl_device_info WHERE userid='$userid'");

    // Parse and format device_info properly
    $getdata['device_info'] = parseDeviceInfo($deviceInfoRecords);
    
    

    if ($getdata) {
        $response = array(
            'status' => true,
            'error' => 0,
            'success' => 1,
            'msg' => 'Loaded Successfully',
            'user_data' => $getdata
        );
    } else {
        $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' => 'User not found');
    }

    echo json_encode($response);
    exit();
    break;


   // ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        case "updateProfile" :
        if (!app_login(intval($userid = @$_POST['userid']))) {
            $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' => 'Invalid Userid or User Not Active');  
            echo json_encode($response);
            exit();
        } 
    
        $full_name = @$_POST['full_name'];
        $email = @$_POST['email'];
        if (isset($full_name) && isset($email)) {
            $updatequery = query("UPDATE `tbl_users` SET `name`='$full_name', `email`='$email' WHERE id='$userid'"); 
    
            if (@isset($_FILES['image']['name'])) {
                $target_dir = "../uploads/";
                $file_name = str_replace(" ", "", $_FILES["image"]["name"]);
                $target_file_name = $target_dir . basename($file_name);
                
                if (@isset($_FILES["image"]["tmp_name"])) {
                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file_name)) {
                       
                        $database_file_path = "uploads/" . basename($file_name);
                        
                        $updatequery = query("UPDATE `tbl_users` SET `image`='$database_file_path' WHERE id='$userid'"); 
                    }
                }
            }
            
            $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' => 'Update Profile Successfully');
        } else {
            $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' => 'missing required params'); 
        }
    
        echo json_encode($response);
        exit();   
        break;

	// ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        case "changePassword" :

            if(!app_login(intval($userid = @$_POST['userid'])))
    		{
              $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid Userid or User Not Active');  
    			echo json_encode($response);
    			exit();
    		}
    		$current_password = @$_POST['current_password'];
    		$new_password = @$_POST['new_password'];
    		if(isset($current_password) && isset($new_password)){
    		    $checkpassword = getRecordField("SELECT password FROM tbl_users WHERE id='$userid' ");
                if($checkpassword==$current_password){
                    $updatequery = query("UPDATE `tbl_users` SET `password`='$new_password'  WHERE id='$userid'"); 
                    $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Changed Password Successfully','new_password'=>$new_password);
                }else{
                    $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid Current Password'); 
                }
    		}else{
    		    $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'missing required params'); 
    		}
   
        echo json_encode($response);
        exit();   
		break;
		
		// -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        
		
		
		case "movieSuccess" :
		     
		echo json_encode($response);
        exit();   
		break; 
		
		
		case "movieFail" :
		     
		echo json_encode($response);
        exit();   
		break; 
	// -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        case "getSubscriptionPlans" :
   
          if(!app_login(intval($userid = @$_POST['userid'])))
    		{
              $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid Userid or User Not Active');  
    			echo json_encode($response);
    			exit();
    		}
    		$getdata=getRecords("SELECT * FROM tbl_subscription WHERE status=1 and deleted=0");
    		if(sizeof($getdata)>0){
    		  $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Loaded Successfully','plans'=>$getdata);  
    		}else{
    		  $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Data Not Found');   
    		}
        echo json_encode($response);
        exit();   
		break;	
	// ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        case "getBanners" :
   
          if(!app_login(intval($userid = @$_POST['userid'])))
    		{
              $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid Userid or User Not Active');  
    			echo json_encode($response);
    			exit();
    		}
    		$type= @$_POST['type'];
    		if(isset($type)){
    		   $jsonObj = [];
    		   if($type=='home') {
    		       $sql = "SELECT movie.*, lang.`language_name`, g.genre_name,  IF(tbl_likes.`is_like` = 1, 1, 0) AS liked, IF(tbl_likes.`is_like` = 0, 1, 0) AS disliked    FROM tbl_movies movie LEFT JOIN tbl_language lang ON movie.`language_id` = lang.`id`
    		       LEFT JOIN tbl_genres g on movie.genre_id=g.gid  LEFT JOIN tbl_likes tbl_likes ON movie.`id` = tbl_likes.`post_id`   AND tbl_likes.`userid` = '$userid'   AND tbl_likes.`type` = 'movies' 
    		       WHERE movie.`status` = '1' AND lang.`status` = '1' AND movie.`is_slider` = '1' ORDER BY movie.`id` DESC";
                   $movies = getRecords($sql);
       
               
                   foreach ($movies as $movie) {
                    $genreIds = explode(',', $movie['genre_id']); // Convert genre_id string to array
                    $genreNames = [];
                
                    if (!empty($genreIds)) {
                        $genreIdStr = implode(',', array_map('intval', $genreIds)); // Ensure integers for safety
                        $genreQuery = "SELECT `genre_name` FROM `tbl_genres` WHERE `gid` IN ($genreIdStr)";
                        $genres = getRecords($genreQuery);
                
                        foreach ($genres as $genre) {
                            $genreNames[] = $genre['genre_name'];
                        }
                    }
                    $userInteraction = [
                     'liked' => $movie['liked'] == 1,
                    'disliked' => $movie['disliked'] == 1
                    ];
                       $jsonObj[] = [
                        'id' => $movie['id'],
                        'title' => $movie['movie_title'],
                        'language_name' => $movie['language_name'],
                        'genre_name' => implode(', ', $genreNames), // Properly formatted genres
                        'slide_image' => $movie['movie_cover'],
                        'description' => $movie['movie_desc'],
                        'total_time'=>$movie['total_time'],
                        'director_name'=>$movie['director_name'],
                        'cast_names'=>$movie['cast_names'],
                        'maturity_rating'=>$movie['maturity_rating'],
                        'release_date'=>$movie['release_date'],
                        'cost_type'=>$movie['movie_cost_type'],
                        'price'=>$movie['movie_price'],
                        'trailer_url'=>$movie['trailer_url'],
                        'trailer_type'=>$movie['trailer_type'],
                        'url_type'=>$movie['movie_type'],
                        'url'=>$movie['movie_url'],
                        'type' => 'movies',
                        'imdb_rating'=>$movie['imdb_rating'],
                         'user_interaction' => $userInteraction
                    ];
                   
                }

        
                $sql = "SELECT s.*,lang.`language_name` , g.genre_name, se.id as season_id ,IF(tbl_likes.`is_like` = 1, 1, 0) AS liked, IF(tbl_likes.`is_like` = 0, 1, 0) AS disliked  FROM tbl_series s  LEFT JOIN tbl_language lang ON s.`language_id` = lang.`id`   LEFT JOIN tbl_genres g on s.genre_id=g.gid
                LEFT JOIN tbl_likes tbl_likes ON s.`id` = tbl_likes.`post_id`   AND tbl_likes.`userid` = '$userid' AND tbl_likes.`type` = 'series'  left join tbl_season se on se.series_id=s.id  WHERE s.`status` = '1' AND s.is_slider = '1'  GROUP BY s.id  ORDER BY s.`id` DESC";
                $series = getRecords($sql);
        
                foreach ($series as $serie) {
                    $genreIds = explode(',', $serie['genre_id']); // Convert genre_id string to array
                    $genreNames = [];
                
                    if (!empty($genreIds)) {
                        $genreIdStr = implode(',', array_map('intval', $genreIds)); // Ensure integers for safety
                        $genreQuery = "SELECT `genre_name` FROM `tbl_genres` WHERE `gid` IN ($genreIdStr)";
                        $genres = getRecords($genreQuery);
                
                        foreach ($genres as $genre) {
                            $genreNames[] = $genre['genre_name'];
                        }
                    }
                    
                    $userInteraction = [
                     'liked' => $serie['liked'] == 1,
                    'disliked' => $serie['disliked'] == 1
                    ];
                    
                    $seasons=getRecords("SELECT * FROM tbl_season where series_id='{$serie['id']}'");
                    $jsonObj[] = [
                        'id' => $serie['id'],
                         'season_id' => $serie['season_id'],
                        'title' => $serie['series_name'],
                        'language_name' =>  $serie['language_name'],
                        'genre_name' => implode(', ', $genreNames), 
                        'slide_image' =>  $serie['series_cover'],
                        'description' => $serie['series_desc'],
                        'total_time'=>$serie['total_time'],
                        'director_name'=>$serie['director_name'],
                        'cast_names'=>$serie['cast_names'],
                        'cost_type'=>$serie['series_cost_type'],
                        'price'=>$serie['series_price'],
                        'maturity_rating'=>$serie['maturity_rating'],
                        'release_date'=>$serie['release_date'],
                        'trailer_url'=>$serie['trailer_url'],
                        'trailer_type'=>$serie['trailer_type'],
                        'type' => 'series',
                        'imdb_rating'=>$movie['imdb_rating'],
                        'seasons'=>$seasons,
                         'user_interaction' => $userInteraction
                    ];
                     
                }
                
                
                 $sql = "SELECT s.*,se.id as season_id ,lang.`language_name` , g.genre_name,IF(tbl_likes.`is_like` = 1, 1, 0) AS liked, IF(tbl_likes.`is_like` = 0, 1, 0) AS disliked FROM tbl_shows s  LEFT JOIN tbl_language lang ON s.`language_id` = lang.`id`  LEFT JOIN tbl_genres g on s.genre_id=g.gid 
                  LEFT JOIN tbl_likes tbl_likes ON s.`id` = tbl_likes.`post_id`  AND tbl_likes.`userid` = '$userid' AND tbl_likes.`type` = 'shows' left join tbl_tv_season se on se.shows_id=s.id
                 WHERE s.`status` = '1' AND s.is_slider = '1'  GROUP BY s.id ORDER BY s.`id` DESC";
                $shows = getRecords($sql);
    
                foreach ($shows as $show) {
                     $genreIds = explode(',', $show['genre_id']); // Convert genre_id string to array
                    $genreNames = [];
                
                    if (!empty($genreIds)) {
                        $genreIdStr = implode(',', array_map('intval', $genreIds)); // Ensure integers for safety
                        $genreQuery = "SELECT `genre_name` FROM `tbl_genres` WHERE `gid` IN ($genreIdStr)";
                        $genres = getRecords($genreQuery);
                
                        foreach ($genres as $genre) {
                            $genreNames[] = $genre['genre_name'];
                        }
                    }
                    $userInteraction = [
                     'liked' => $show['liked'] == 1,
                    'disliked' => $show['disliked'] == 1
                    ];
                    $seasons=getRecords("SELECT * FROM tbl_tv_season where shows_id='{$show['id']}'");
                    $jsonObj[] = [
                        'id' => $show['id'],
                        'season_id' => $show['season_id'],
                        'title' => $show['shows_name'],
                        'language_name' => $show['language_name'],
                        'genre_name' => implode(', ', $genreNames),
                        'slide_image' =>    $show['shows_cover'],
                        'description' => $show['shows_desc'],
                         'total_time'=>$show['total_time'],
                          'director_name'=>$show['director_name'],
                        'cast_names'=>$show['cast_names'],
                         'cost_type'=>'free',
                         'price'=>'0',
                        'maturity_rating'=>$show['maturity_rating'],
                        'release_date'=>$show['release_date'],
                         'trailer_url'=>$show['trailer_url'],
                         'trailer_type'=>$show['trailer_type'],
                        'type' => 'show',
                        'imdb_rating'=>$movie['imdb_rating'],
                        'seasons'=>$seasons,
                        'user_interaction' => $userInteraction
                    ];
                }
                
                 $sql = "SELECT movie.*, lang.`language_name`, g.genre_name,IF(tbl_likes.`is_like` = 1, 1, 0) AS liked, IF(tbl_likes.`is_like` = 0, 1, 0) AS disliked  FROM tbl_shortfilms movie LEFT JOIN tbl_language lang ON movie.`language_id` = lang.`id` LEFT JOIN tbl_genres g on movie.genre_id=g.gid
                 LEFT JOIN tbl_likes tbl_likes ON movie.`id` = tbl_likes.`post_id`   AND tbl_likes.`userid` = '$userid'   AND tbl_likes.`type` = 'shortfilm'
                 WHERE movie.`status` = '1' AND lang.`status` = '1' AND movie.`is_slider` = '1' ORDER BY movie.`id` DESC";
                $shortfilms = getRecords($sql);
        
                foreach ($shortfilms as $shortfilm) {
                     $genreIds = explode(',', $shortfilm['genre_id']); // Convert genre_id string to array
                    $genreNames = [];
                
                    if (!empty($genreIds)) {
                        $genreIdStr = implode(',', array_map('intval', $genreIds)); // Ensure integers for safety
                        $genreQuery = "SELECT `genre_name` FROM `tbl_genres` WHERE `gid` IN ($genreIdStr)";
                        $genres = getRecords($genreQuery);
                
                        foreach ($genres as $genre) {
                            $genreNames[] = $genre['genre_name'];
                        }
                    }
                    
                     $userInteraction = [
                     'liked' => $shortfilm['liked'] == 1,
                    'disliked' => $shortfilm['disliked'] == 1
                    ];
                    $jsonObj[] = [
                        'id' => $shortfilm['id'],
                        'title' => $shortfilm['movie_title'],
                        'language_name' => $shortfilm['language_name'],
                        'genre_name' => implode(', ', $genreNames), // Properly formatted genres
                        'slide_image' =>  $shortfilm['movie_cover'],
                        'description' => $shortfilm['movie_desc'],
                         'total_time'=>$shortfilm['total_time'],
                          'director_name'=>$shortfilm['director_name'],
                        'cast_names'=>$shortfilm['cast_names'],
                         'cost_type'=>'free',
                         'price'=>'0',
                        'maturity_rating'=>$shortfilm['maturity_rating'],
                        'release_date'=>$shortfilm['release_date'],
                         'trailer_url'=>$shortfilm['trailer_url'],
                         'trailer_type'=>$shortfilm['trailer_type'],
                         'url_type'=>$shortfilm['movie_type'],
                         'url'=>$shortfilm['movie_url'],
                        'type' => 'shortfilm',
                        'imdb_rating'=>$movie['imdb_rating'],
                        'user_interaction' => $userInteraction
                    ];
                }
                
                
                $sql = "SELECT movie.*, lang.`language_name`, IF(tbl_likes.`is_like` = 1, 1, 0) AS liked, IF(tbl_likes.`is_like` = 0, 1, 0) AS disliked  FROM tbl_drama movie LEFT JOIN tbl_language lang ON movie.`language_id` = lang.`id` 
                 LEFT JOIN tbl_likes tbl_likes ON movie.`id` = tbl_likes.`post_id`   AND tbl_likes.`userid` = '$userid'   AND tbl_likes.`type` = 'drama'
                 WHERE movie.`status` = '1' AND lang.`status` = '1' AND movie.`is_slider` = '1' ORDER BY movie.`id` DESC";
                $dramas = getRecords($sql);
        
                foreach ($dramas as $drama) {
                     
                    
                     $userInteraction = [
                     'liked' => $drama['liked'] == 1,
                    'disliked' => $drama['disliked'] == 1
                    ];
                    $jsonObj[] = [
                        'id' => $drama['id'],
                        'title' => $drama['drama_title'],
                        'language_name' => $drama['language_name'],
                        'genre_name' => implode(', ', $genreNames),// Properly formatted genres
                        'slide_image' =>  $drama['drama_cover'],
                        'description' => $drama['drama_desc'],
                         'total_time'=>$drama['total_time'],
                          'director_name'=>$drama['director_name'],
                        'cast_names'=>$drama['cast_names'],
                         'cost_type'=>'free',
                         'price'=>'0',
                        'maturity_rating'=>$drama['maturity_rating'],
                        'release_date'=>$drama['release_date'],
                         'trailer_url'=>$drama['trailer_url'],
                         'trailer_type'=>$drama['trailer_type'],
                         'url_type'=>$drama['drama_type'],
                         'url'=>$drama['drama_url'],
                        'type' => 'drama',
                        'imdb_rating'=>$movie['imdb_rating'],
                        'user_interaction' => $userInteraction
                    ];
                }
                
                
                $sql = "SELECT movie.*, lang.`language_name`, IF(tbl_likes.`is_like` = 1, 1, 0) AS liked, IF(tbl_likes.`is_like` = 0, 1, 0) AS disliked  FROM tbl_songs movie LEFT JOIN tbl_language lang ON movie.`language_id` = lang.`id` 
                 LEFT JOIN tbl_likes tbl_likes ON movie.`id` = tbl_likes.`post_id`   AND tbl_likes.`userid` = '$userid'   AND tbl_likes.`type` = 'song'
                 WHERE movie.`status` = '1' AND lang.`status` = '1' AND movie.`is_slider` = '1' ORDER BY movie.`id` DESC";
                $songs = getRecords($sql);
        
                foreach ($songs as $song) {
                     
                    
                     $userInteraction = [
                     'liked' => $song['liked'] == 1,
                    'disliked' => $song['disliked'] == 1
                    ];
                    $jsonObj[] = [
                        'id' => $song['id'],
                        'title' => $song['song_title'],
                        'language_name' => $song['language_name'],
                        // Properly formatted genres
                        'slide_image' =>  $song['song_cover'],
                        'description' => $song['song_desc'],
                         'total_time'=>$song['total_time'],
                          'director_name'=>$song['director_name'],
                        'cast_names'=>$song['cast_names'],
                         'cost_type'=>'free',
                         'price'=>'0',
                        'maturity_rating'=>$song['maturity_rating'],
                        'release_date'=>$song['release_date'],
                         'trailer_url'=>$song['trailer_url'],
                         'trailer_type'=>$song['trailer_type'],
                         'url_type'=>$song['song_type'],
                         'url'=>$song['song_url'],
                        'type' => 'song',
                        'imdb_rating'=>$movie['imdb_rating'],
                        'user_interaction' => $userInteraction
                    ];
                }
                
                
                
                  if (!empty($jsonObj)) {
                        $response = [
                            'status' => true,
                            'error' => 0,
                            'success' => 1,
                            'msg' => 'Loaded Successfully',
                            'banners' => $jsonObj
                        ];
                    } else {
                        $response = [
                            'status' => false,
                            'error' => 1,
                            'success' => 0,
                            'msg' => 'Data Not Found'
                        ];
                    }
    		
           }else if ($type == 'movies') {

                    $sql = "SELECT movie.*, 
                                   lang.`language_name`,  
                                   IF(tbl_likes.`is_like` = 1, 1, 0) AS liked, 
                                   IF(tbl_likes.`is_like` = 0, 1, 0) AS disliked    
                            FROM tbl_movies movie 
                            LEFT JOIN tbl_language lang ON movie.`language_id` = lang.`id`
                            LEFT JOIN tbl_likes tbl_likes ON movie.`id` = tbl_likes.`post_id`   
                                AND tbl_likes.`userid` = '$userid'   
                                AND tbl_likes.`type` = 'movies' 
                            WHERE movie.`status` = '1' 
                              AND lang.`status` = '1' 
                              AND movie.`is_slider` = '1' 
                            ORDER BY movie.`id` DESC";
                
                    $movies = getRecords($sql);
                    $jsonObj = [];
                
                    foreach ($movies as $movie) {
                        $genreIds = explode(',', $movie['genre_id']);
                        $genreNames = [];
                
                        if (!empty($genreIds)) {
                            $genreIdStr = implode(',', array_map('intval', $genreIds));
                            $genreQuery = "SELECT `genre_name` FROM `tbl_genres` WHERE `gid` IN ($genreIdStr)";
                            $genres = getRecords($genreQuery);
                
                            foreach ($genres as $genre) {
                                $genreNames[] = $genre['genre_name'];
                            }
                        }
                
                        $userInteraction = [
                            'liked' => $movie['liked'] == 1,
                            'disliked' => $movie['disliked'] == 1
                        ];
                
                        $jsonObj[] = [
                            'id' => $movie['id'],
                            'title' => $movie['movie_title'],
                            'language_name' => $movie['language_name'],
                            'genre_name' => implode(', ', $genreNames),
                            'slide_image' => $movie['movie_cover'],
                            'description' => $movie['movie_desc'],
                            'total_time' => $movie['total_time'],
                            'director_name' => $movie['director_name'],
                            'cast_names' => $movie['cast_names'],
                            'maturity_rating' => $movie['maturity_rating'],
                            'release_date' => $movie['release_date'],
                            'cost_type' => $movie['movie_cost_type'],
                            'price' => $movie['movie_price'],
                            'trailer_url' => $movie['trailer_url'],
                            'trailer_type' => $movie['trailer_type'],
                            'url' => $movie['movie_url'],
                            'url_type' => $movie['movie_type'],
                            'type' => 'movies',
                            'imdb_rating'=>$movie['imdb_rating'],
                            'user_interaction' => $userInteraction
                        ];
                    }
                
                    if (!empty($jsonObj)) {
                        $response = array(
                            'status' => true,
                            'error' => 0,
                            'success' => 1,
                            'msg' => 'Loaded Successfully',
                            'banners' => $jsonObj
                        );
                    } else {
                        $response = array(
                            'status' => false,
                            'error' => 1,
                            'success' => 0,
                            'msg' => 'Data Not Found'
                        );
                    }
            }
            else if($type=='series'){
    		      $sql = "SELECT s.*, 
                   lang.`language_name`, 
                   se.id as season_id, 
                   IF(tbl_likes.`is_like` = 1, 1, 0) AS liked, 
                   IF(tbl_likes.`is_like` = 0, 1, 0) AS disliked  
                        FROM tbl_series s  
                        LEFT JOIN tbl_language lang ON s.`language_id` = lang.`id`   
                        LEFT JOIN tbl_likes tbl_likes 
                            ON s.`id` = tbl_likes.`post_id`   
                           AND tbl_likes.`userid` = '$userid' 
                           AND tbl_likes.`type` = 'series'  
                        LEFT JOIN tbl_season se ON se.series_id = s.id  
                        WHERE s.`status` = '1' 
                          AND s.`is_slider` = '1'   GROUP by s.id 
                        ORDER BY s.`id` DESC";
            
                $series = getRecords($sql);
                $jsonObj = [];

                foreach ($series as $serie) {
                    $genreIds = explode(',', $serie['genre_id']); // Convert genre_id string to array
                    $genreNames = [];
            
                    if (!empty($genreIds)) {
                        $genreIdStr = implode(',', array_map('intval', $genreIds)); // Ensure integers for safety
                        $genreQuery = "SELECT `genre_name` FROM `tbl_genres` WHERE `gid` IN ($genreIdStr)";
                        $genres = getRecords($genreQuery);
            
                        foreach ($genres as $genre) {
                            $genreNames[] = $genre['genre_name'];
                        }
                    }
            
                    $userInteraction = [
                        'liked' => $serie['liked'] == 1,
                        'disliked' => $serie['disliked'] == 1
                    ];
             $seasons=getRecords("SELECT * FROM tbl_season where series_id='{$serie['id']}'");
                    $jsonObj[] = [
                        'id' => $serie['id'],
                        'season_id' => $serie['season_id'],
                        'title' => $serie['series_name'],
                        'language_name' => $serie['language_name'],
                        'genre_name' => implode(', ', $genreNames),
                        'slide_image' =>  $serie['series_cover'],
                        'description' => $serie['series_desc'],
                        'total_time' => $serie['total_time'],
                        'director_name' => $serie['director_name'],
                        'cast_names' => $serie['cast_names'],
                        'cost_type' => $serie['series_cost_type'],
                        'price' => $serie['series_price'],
                        'maturity_rating' => $serie['maturity_rating'],
                        'release_date' => $serie['release_date'],
                        'trailer_url' => $serie['trailer_url'],
                        'trailer_type' => $serie['trailer_type'],
                        'type' => 'series',
                        'imdb_rating'=>$serie['imdb_rating'],
                        'seasons'=>$seasons,
                        'user_interaction' => $userInteraction
                    ];
                }
                
                
                if (!empty($jsonObj)) {
                    $response = array(
                        'status' => true,
                        'error' => 0,
                        'success' => 1,
                        'msg' => 'Loaded Successfully',
                        'banners' => $jsonObj
                    );
                } else {
                    $response = array(
                        'status' => false,
                        'error' => 1,
                        'success' => 0,
                        'msg' => 'Data Not Found'
                    );
                }


    		   }
    		else if($type=='shows'){
    		    $sql = "SELECT s.*,se.id as season_id ,lang.`language_name` , g.genre_name,IF(tbl_likes.`is_like` = 1, 1, 0) AS liked, IF(tbl_likes.`is_like` = 0, 1, 0) AS disliked FROM tbl_shows s  LEFT JOIN tbl_language lang ON s.`language_id` = lang.`id`  LEFT JOIN tbl_genres g on s.genre_id=g.gid 
                  LEFT JOIN tbl_likes tbl_likes ON s.`id` = tbl_likes.`post_id`  AND tbl_likes.`userid` = '$userid' AND tbl_likes.`type` = 'shows' left join tbl_tv_season se on se.shows_id=s.id
                 WHERE s.`status` = '1' AND s.is_slider = '1'  ORDER BY s.`id` DESC";
                $shows = getRecords($sql);
    
                foreach ($shows as $show) {
                     $genreIds = explode(',', $show['genre_id']); // Convert genre_id string to array
                    $genreNames = [];
                
                    if (!empty($genreIds)) {
                        $genreIdStr = implode(',', array_map('intval', $genreIds)); // Ensure integers for safety
                        $genreQuery = "SELECT `genre_name` FROM `tbl_genres` WHERE `gid` IN ($genreIdStr)";
                        $genres = getRecords($genreQuery);
                
                        foreach ($genres as $genre) {
                            $genreNames[] = $genre['genre_name'];
                        }
                    }
                    $userInteraction = [
                     'liked' => $show['liked'] == 1,
                    'disliked' => $show['disliked'] == 1
                    ];
                     $seasons=getRecords("SELECT * FROM tbl_tv_season where shows_id='{$show['id']}'");
                    $jsonObj[] = [
                        'id' => $show['id'],
                        'season_id' => $show['season_id'],
                        'title' => $show['shows_name'],
                        'language_name' => $show['language_name'],
                        'genre_name' => implode(', ', $genreNames),
                        'slide_image' =>  $show['shows_cover'],
                        'description' => $show['shows_desc'],
                         'total_time'=>$show['total_time'],
                          'director_name'=>$show['director_name'],
                        'cast_names'=>$show['cast_names'],
                         'cost_type'=>'free',
                         'price'=>'0',
                        'maturity_rating'=>$show['maturity_rating'],
                        'release_date'=>$show['release_date'],
                        'trailer_url' => $show['trailer_url'],
                        'trailer_type' => $show['trailer_type'],
                        'imdb_rating'=>$show['imdb_rating'],
                        'type' => 'show',
                        'seasons'=>$seasons,
                        'user_interaction' => $userInteraction
                    ];
                }
                
                if (!empty($jsonObj)) {
                    $response = array(
                        'status' => true,
                        'error' => 0,
                        'success' => 1,
                        'msg' => 'Loaded Successfully',
                        'banners' => $jsonObj
                    );
                } else {
                    $response = array(
                        'status' => false,
                        'error' => 1,
                        'success' => 0,
                        'msg' => 'Data Not Found'
                    );
                }
    		}
    		else if($type=='shortfilms'){
    		    $sql = "SELECT movie.*, lang.`language_name`, g.genre_name,IF(tbl_likes.`is_like` = 1, 1, 0) AS liked, IF(tbl_likes.`is_like` = 0, 1, 0) AS disliked  FROM tbl_shortfilms movie LEFT JOIN tbl_language lang ON movie.`language_id` = lang.`id` LEFT JOIN tbl_genres g on movie.genre_id=g.gid
                 LEFT JOIN tbl_likes tbl_likes ON movie.`id` = tbl_likes.`post_id`   AND tbl_likes.`userid` = '$userid'   AND tbl_likes.`type` = 'shortfilm'
                 WHERE movie.`status` = '1' AND lang.`status` = '1' AND movie.`is_slider` = '1' ORDER BY movie.`id` DESC";
                $shortfilms = getRecords($sql);
        
                foreach ($shortfilms as $shortfilm) {
                     $genreIds = explode(',', $shortfilm['genre_id']); // Convert genre_id string to array
                    $genreNames = [];
                
                    if (!empty($genreIds)) {
                        $genreIdStr = implode(',', array_map('intval', $genreIds)); // Ensure integers for safety
                        $genreQuery = "SELECT `genre_name` FROM `tbl_genres` WHERE `gid` IN ($genreIdStr)";
                        $genres = getRecords($genreQuery);
                
                        foreach ($genres as $genre) {
                            $genreNames[] = $genre['genre_name'];
                        }
                    }
                    
                     $userInteraction = [
                     'liked' => $shortfilm['liked'] == 1,
                    'disliked' => $shortfilm['disliked'] == 1
                    ];
                    $jsonObj[] = [
                        'id' => $shortfilm['id'],
                        'title' => $shortfilm['movie_title'],
                        'language_name' => $shortfilm['language_name'],
                        'genre_name' => implode(', ', $genreNames), // Properly formatted genres
                        'slide_image' =>  $shortfilm['movie_cover'],
                        'description' => $shortfilm['movie_desc'],
                         'total_time'=>$shortfilm['total_time'],
                          'director_name'=>$shortfilm['director_name'],
                        'cast_names'=>$shortfilm['cast_names'],
                         'cost_type'=>'free',
                         'price'=>'0',
                        'maturity_rating'=>$shortfilm['maturity_rating'],
                        'release_date'=>$shortfilm['release_date'],
                         'trailer_url' => $shortfilm['trailer_url'],
                            'trailer_type' => $shortfilm['trailer_type'],
                            'url' => $shortfilm['movie_url'],
                            'url_type' => $shortfilm['movie_type'],
                        'type' => 'shortfilm',
                        'user_interaction' => $userInteraction
                    ];
                }
                
                if (!empty($jsonObj)) {
                    $response = array(
                        'status' => true,
                        'error' => 0,
                        'success' => 1,
                        'msg' => 'Loaded Successfully',
                        'banners' => $jsonObj
                    );
                } else {
                    $response = array(
                        'status' => false,
                        'error' => 1,
                        'success' => 0,
                        'msg' => 'Data Not Found'
                    );
                }
    		}
    		else if($type=='drama'){
    		    $sql = "SELECT movie.*, lang.`language_name`, IF(tbl_likes.`is_like` = 1, 1, 0) AS liked, IF(tbl_likes.`is_like` = 0, 1, 0) AS disliked  FROM tbl_drama movie LEFT JOIN tbl_language lang ON movie.`language_id` = lang.`id` 
                 LEFT JOIN tbl_likes tbl_likes ON movie.`id` = tbl_likes.`post_id`   AND tbl_likes.`userid` = '$userid'   AND tbl_likes.`type` = 'drama'
                 WHERE movie.`status` = '1' AND lang.`status` = '1' AND movie.`is_slider` = '1' ORDER BY movie.`id` DESC";
                $dramas = getRecords($sql);
        
                foreach ($dramas as $drama) {
                     
                    
                     $userInteraction = [
                     'liked' => $drama['liked'] == 1,
                    'disliked' => $drama['disliked'] == 1
                    ];
                    $jsonObj[] = [
                        'id' => $drama['id'],
                        'title' => $drama['drama_title'],
                        'language_name' => $drama['language_name'],
                        // Properly formatted genres
                        'slide_image' =>  $drama['drama_cover'],
                        'description' => $drama['drama_desc'],
                         'total_time'=>$drama['total_time'],
                          'director_name'=>$drama['director_name'],
                        'cast_names'=>$drama['cast_names'],
                         'cost_type'=>'free',
                         'price'=>'0',
                        'maturity_rating'=>$drama['maturity_rating'],
                        'release_date'=>$drama['release_date'],
                         'trailer_url' => $show['trailer_url'],
                        'trailer_type' => $show['trailer_type'],
                        'url_type'=>$drama['drama_type'],
                        'url'=>$drama['drama_url'],
                        'imdb_rating'=>$show['imdb_rating'],
                        'type' => 'drama',
                        'user_interaction' => $userInteraction
                    ];
                }
                
                if (!empty($jsonObj)) {
        $response = array(
            'status' => true,
            'error' => 0,
            'success' => 1,
            'msg' => 'Loaded Successfully',
            'banners' => $jsonObj
        );
    } else {
        $response = array(
            'status' => false,
            'error' => 1,
            'success' => 0,
            'msg' => 'Data Not Found'
        );
    }
    		}
    		else if($type=='songs'){
    		     $sql = "SELECT movie.*, lang.`language_name`, IF(tbl_likes.`is_like` = 1, 1, 0) AS liked, IF(tbl_likes.`is_like` = 0, 1, 0) AS disliked  FROM tbl_songs movie LEFT JOIN tbl_language lang ON movie.`language_id` = lang.`id` 
                 LEFT JOIN tbl_likes tbl_likes ON movie.`id` = tbl_likes.`post_id`   AND tbl_likes.`userid` = '$userid'   AND tbl_likes.`type` = 'song'
                 WHERE movie.`status` = '1' AND lang.`status` = '1' AND movie.`is_slider` = '1' ORDER BY movie.`id` DESC";
                $songs = getRecords($sql);
        
                foreach ($songs as $song) {
                     
                    
                     $userInteraction = [
                     'liked' => $song['liked'] == 1,
                    'disliked' => $song['disliked'] == 1
                    ];
                    $jsonObj[] = [
                        'id' => $song['id'],
                        'title' => $song['song_title'],
                        'language_name' => $song['language_name'],
                        // Properly formatted genres
                        'slide_image' =>  $song['song_cover'],
                        'description' => $song['song_desc'],
                         'total_time'=>$song['total_time'],
                          'director_name'=>$song['director_name'],
                        'cast_names'=>$song['cast_names'],
                         'cost_type'=>'free',
                         'price'=>'0',
                        'maturity_rating'=>$song['maturity_rating'],
                        'release_date'=>$song['release_date'],
                         'trailer_url'=>$song['trailer_url'],
                         'url'=>$song['song_url'],
                        'type' => 'song',
                        'user_interaction' => $userInteraction
                    ];
                }
                
                if (!empty($jsonObj)) {
                        $response = array(
                            'status' => true,
                            'error' => 0,
                            'success' => 1,
                            'msg' => 'Loaded Successfully',
                            'banners' => $jsonObj
                        );
                    } else {
                        $response = array(
                            'status' => false,
                            'error' => 1,
                            'success' => 0,
                            'msg' => 'Data Not Found'
                        );
                    }
    		}
    		   else{
    		      $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid type'); 
    		   }
    		}else{
    		   $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Missing Required Params');    
    		}
        echo json_encode($response);
        exit();   
		break;	
	// -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        case "purchasePlan" :
          if(!app_login(intval($userid = @$_POST['userid'])))
    		{
              $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid Userid or User Not Active');  
    			echo json_encode($response);
    			exit();
    		}
    		
    		$plan_id = @$_POST['plan_id'];
            if(isset($plan_id)){
                $checkid = getRecord("SELECT * FROM `tbl_subscription` WHERE  id = '$plan_id'"); 
    	        if(empty($checkid)){
    	           $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid Planid');
    	            echo json_encode($response);
                    exit(); 
    	       }
    	       $validatiinplan = getRecordField("SELECT validity FROM `tbl_subscription` WHERE  id = '$plan_id'"); 
    	       $getdata=getRecord("SELECT *   from tbl_users  WHERE id = '$userid' and plan_expire=1 ");
    	       if($getdata){
    	            $updatequery = query("UPDATE `tbl_users` SET `plan_expire`='0' , `plan_buy_date`='$fdate' ,`plan_id`='$plan_id',`plan_validation`='{$validatiinplan}'  WHERE id='$userid'");
    	            if($updatequery){
    	                 $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully Subcribed this plan');
    	            }
    	       }else{
    	           $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Still plan not Expire');
    	       }
            }else{
    		   $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Missing Required Params');    
    		}
        echo json_encode($response);
        exit();   
		break;	
  // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
      
        case "cronPlan":
        
          $current_date = date('Y-m-d H:i:s');
$users = getRecords("SELECT d.id, d.plan_buy_date, d.plan_validation FROM tbl_users d WHERE d.status = 1 AND d.plan_expire = 0");

if ($users) {
    foreach ($users as $user) {
        $userid         = $user['id'];
        $plan_date      = $user['plan_buy_date'];
        $validity_days  = $user['plan_validation'];

        $expiration_date = date('Y-m-d H:i:s', strtotime("$plan_date +$validity_days days"));

        

        if (strtotime($current_date) > strtotime($expiration_date)) {
            $up = query("UPDATE tbl_users SET plan_expire = 1 WHERE id = '$userid'");
            
        } else {
           
        }
    }

    $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' => 'Successfully updated expired plans');
} else {
    $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' => 'No users found');
}

echo json_encode($response);
exit();

   // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
      
        case "videoCheck":
            if (!app_login(intval($userid = @$_POST['userid']))) {
                $response = array(
                    'status' => false,
                    'error' => 1,
                    'success' => 0,
                    'msg' => 'Invalid Userid or User Not Active'
                );
                echo json_encode($response);
                exit();
            }
        
            $vid = @$_POST['vid']; 
            if (isset($vid) && !empty($vid)) {
                $api_url = "https://dev.vdocipher.com/api/videos/$vid/otp"; 
                $api_secret = "Apisecret xHKjqDbehRlvByYr0AWrfOFD1nYQ3Ryv9NuBNsI1WbRsjzpjSzXnQWFwPSpX5g4V";
        
                
                $curl = curl_init();
        
                
                curl_setopt_array($curl, [
                    CURLOPT_URL => $api_url,
                    CURLOPT_RETURNTRANSFER => true, 
                    CURLOPT_POST => true,           
                    CURLOPT_HTTPHEADER => [
                        "Accept: application/json",
                        "Authorization: $api_secret",
                        "Content-Type: application/json"
                    ],
                ]);
        
                
                $api_response = curl_exec($curl);
        
                
                if (curl_errno($curl)) {
                    $response = array(
                        'status' => false,
                        'error' => 1,
                        'success' => 0,
                        'msg' => 'API Request Failed: ' . curl_error($curl)
                    );
                } else {
                    
                    $api_data = json_decode($api_response, true);
        
                    
                    if ($api_data && isset($api_data['otp'])) {
                        $response = array(
                            'status' => true,
                            'error' => 0,
                            'success' => 1,
                            'msg' => 'Video OTP fetched successfully',
                            'data' => $api_data 
                        );
                    } else {
                        $response = array(
                            'status' => false,
                            'error' => 1,
                            'success' => 0,
                            'msg' => 'Invalid API Response'
                        );
                    }
                }
        
               
                curl_close($curl);
            } else {
                $response = array(
                    'status' => false,
                    'error' => 1,
                    'success' => 0,
                    'msg' => 'Missing Required Params'
                );
            }

        
        echo json_encode($response);
        exit();
        break;
	// -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
     case "getHome":
    if (!app_login(intval($userid = @$_POST['userid']))) {
        $response = [
            'status' => false,
            'error' => 1,
            'success' => 0,
            'msg' => 'Invalid User ID or User Not Active',
        ];
        echo json_encode($response);
        exit();
    }

    $jsonObj = [];
    $data = [];

    // Fetch slider movies
    $sql = "SELECT movie.*, lang.`language_name`, lang.`language_background` 
            FROM tbl_movies movie
            LEFT JOIN tbl_language lang ON movie.`language_id` = lang.`id`
            WHERE movie.`status` = '1' AND lang.`status` = '1' AND movie.`is_slider` = '1'
            ORDER BY movie.`id` DESC";

    $movies = getRecords($sql);
    foreach ($movies as $movie) {
        $jsonObj[] = [
            'id' => $movie['id'],
            'title' => $movie['movie_title'],
            'price_data' => $movie['price'],
            'sub_title' => $movie['language_name'],
            'slide_image' => $file_path . 'images/movies/' . $movie['movie_cover'],
            'type' => 'movies',
        ];
    }

    // Fetch slider series
    $sql = "SELECT * FROM tbl_series 
            WHERE `status` = '1' AND is_slider = '1' 
            ORDER BY `id` DESC";
    $series = getRecords($sql);
    foreach ($series as $serie) {
        $jsonObj[] = [
            'id' => $serie['id'],
            'title' => $serie['series_name'],
            'sub_title' => '',
            'slide_image' => $file_path . 'images/series/' . $serie['series_cover'],
            'type' => 'series',
        ];
    }

    // Fetch slider channels
    $sql = "SELECT tbl_channels.*, tbl_category.`category_name` 
            FROM tbl_channels
            LEFT JOIN tbl_category ON tbl_channels.`cat_id` = tbl_category.`cid`
            WHERE tbl_channels.`status` = '1' AND tbl_channels.`slider_channel` = '1'
            ORDER BY tbl_channels.`id` DESC";
    $channels = getRecords($sql);
    foreach ($channels as $channel) {
        $jsonObj[] = [
            'id' => $channel['id'],
            'title' => $channel['channel_title'],
            'sub_title' => $channel['category_name'],
            'slide_image' => $file_path . 'images/' . $channel['channel_thumbnail'],
            'type' => 'channel',
        ];
    }

    $row['banner'] = $jsonObj;

    // Fetch category list
    $jsonObj = [];
    $sql = "SELECT cid, category_name, category_image 
            FROM tbl_category 
            WHERE `status` = '1' 
            ORDER BY cid DESC 
            LIMIT 0, 5";
    $categories = getRecords($sql);
    foreach ($categories as $category) {
        $jsonObj[] = [
            'cid' => $category['cid'],
            'category_name' => $category['category_name'],
            'category_image' => $file_path . 'images/' . $category['category_image'],
            'category_image_thumb' => $file_path . 'images/thumbs/' . $category['category_image'],
        ];
    }

    $row['cat_list'] = $jsonObj;

    // Fetch latest movies
    $jsonObj = [];
    $sql = "SELECT movie.*, lang.`language_name`, lang.`language_background` 
            FROM tbl_movies movie
            LEFT JOIN tbl_language lang ON movie.`language_id` = lang.`id`
            WHERE movie.`status` = '1' AND lang.`status` = '1'
            ORDER BY movie.`id` DESC 
            LIMIT 0, 5";
    $movies = getRecords($sql);
    foreach ($movies as $movie) {
        $jsonObj[] = [
            'id' => $movie['id'],
            'language_id' => $movie['language_id'],
            'movie_title' => $movie['movie_title'],
            'movie_desc' => addslashes($movie['movie_desc']),
            'movie_price' => addslashes($movie['price']),
            'movie_poster' => $file_path . 'images/movies/' . $movie['movie_poster'],
            'movie_poster_thumb' => $file_path . 'images/movies/thumbs/' . $movie['movie_poster'],
            'movie_cover' => $file_path . 'images/movies/' . $movie['movie_cover'],
            'movie_cover_thumb' => $file_path . 'images/movies/thumbs/' . $movie['movie_cover'],
            'video_type' => $movie['movie_type'] === 'local' ? 'local_url' : $movie['movie_type'],
            'movie_url' => $movie['movie_type'] === 'local' ? $file_path . 'uploads/' . $movie['movie_url'] : $movie['movie_url'],
            'language_name' => $movie['language_name'],
            'language_background' => '#' . $movie['language_background'],
        ];
    }

    $row['latest_movies'] = $jsonObj;

    // Fetch latest series
    $jsonObj = [];
    $sql = "SELECT * FROM tbl_series 
            WHERE `status` = '1' 
            ORDER BY `id` DESC 
            LIMIT 0, 5";
    $series = getRecords($sql);
    foreach ($series as $serie) {
        $jsonObj[] = [
            'id' => $serie['id'],
            'series_name' => $serie['series_name'],
            'series_desc' => addslashes($serie['series_desc']),
            'series_poster' => $file_path . 'images/series/' . $serie['series_poster'],
            'series_cover' => $file_path . 'images/series/' . $serie['series_cover'],
        ];
    }

    $row['tv_series'] = $jsonObj;

    // Fetch latest channels
    $jsonObj = [];
    $sql = "SELECT tbl_channels.*, tbl_category.`category_name`, tbl_category.`category_image` 
            FROM tbl_channels
            LEFT JOIN tbl_category ON tbl_channels.`cat_id` = tbl_category.`cid`
            WHERE tbl_channels.`status` = '1' 
            ORDER BY tbl_channels.`id` DESC 
            LIMIT 0, 5";
    $channels = getRecords($sql);
    foreach ($channels as $channel) {
        $jsonObj[] = [
            'id' => $channel['id'],
            'channel_title' => $channel['channel_title'],
            'channel_thumbnail' => $file_path . 'images/' . $channel['channel_thumbnail'],
            'category_name' => $channel['category_name'],
            'category_image' => $file_path . 'images/' . $channel['category_image'],
        ];
    }

    $row['latest_channels'] = $jsonObj;

    $response = [
        'status' => true,
        'error' => 0,
        'success' => 1,
        'data' => $row,
    ];

    echo json_encode($response);
    exit();
    break;
// -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
       case "getHomeMovies":
    if (!app_login(intval($userid = @$_POST['userid']))) {
        $response = [
            'status' => false,
            'error' => 1,
            'success' => 0,
            'msg' => 'Invalid User ID or User Not Active',
        ];
        echo json_encode($response);
        exit();
    }

    $sql = "SELECT movie.*, lang.`language_name`, lang.`language_background`, 
            IF(tbl_likes.`is_like` = 1, 1, 0) AS liked,
            IF(tbl_likes.`is_like` = 0, 1, 0) AS disliked
            FROM tbl_movies movie 
            LEFT JOIN tbl_language lang ON movie.`language_id` = lang.`id`   
            LEFT JOIN tbl_likes tbl_likes ON movie.`id` = tbl_likes.`post_id`  
                AND tbl_likes.`userid` = '$userid' 
                AND tbl_likes.`type` = 'movies'
            WHERE movie.`status` = '1' AND lang.`status` = '1' AND movie.`is_slider` = '1'
            ORDER BY movie.`id` DESC";

    $movies = getRecords($sql);
    
    if ($movies) {
        foreach ($movies as &$movie) {
            // Convert liked and disliked to boolean
            

            // Fetch genre names
            $genreIds = explode(',', $movie['genre_id']);
            $genreNames = [];

            if (!empty($genreIds)) {
                $genreIdStr = implode(',', array_map('intval', $genreIds)); // Ensure integers for safety
                $genreQuery = "SELECT `genre_name` FROM `tbl_genres` WHERE `gid` IN ($genreIdStr)";
                $genres = getRecords($genreQuery);
                
                foreach ($genres as $genre) {
                    $genreNames[] = $genre['genre_name'];
                }
            }

            $movie['genres'] = implode(',', $genreNames);
            
            
            $movie['user_interaction'] = [
                'liked' => $movie['liked'] == 1,
                'disliked' => $movie['disliked'] == 1
            ];
            unset($movie['liked'], $movie['disliked']);// Convert array to comma-separated string
        }

        $response = [
            'status' => true,
            'error' => 0,
            'success' => 1,
            'msg' => 'Successfully Loaded',
            'data' => $movies
        ];
    } else {
        $response = [
            'status' => false,
            'error' => 1,
            'success' => 0,
            'msg' => 'No data Found'
        ];
    }

    echo json_encode($response);
    exit();
    break;


	// ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        case "getLatesMovies" :
         if(!app_login(intval($userid = @$_POST['userid'])))
    		{
              $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid Userid or User Not Active');  
    			echo json_encode($response);
    			exit();
    		}
    		$sql = "SELECT movie.*,movie.movie_cost_type as cost_type, lang.`language_name`, lang.`language_background` , IF(tbl_likes.`is_like` = 1, 1, 0) AS liked,
            IF(tbl_likes.`is_like` = 0, 1, 0) AS disliked
            FROM tbl_movies movie
            LEFT JOIN tbl_language lang ON movie.`language_id` = lang.`id` 
            LEFT JOIN tbl_likes tbl_likes ON movie.`id` = tbl_likes.`post_id`  AND tbl_likes.`userid` = '$userid' AND tbl_likes.`type` = 'movies'
            WHERE movie.`status` = '1' AND lang.`status` = '1'
            ORDER BY movie.`id` DESC 
            LIMIT 0, 35";
           $movies = getRecords($sql);
         if($movies){
              foreach ($movies as &$movie) {
                  $genreIds = explode(',', $movie['genre_id']);
            $genreNames = [];

            if (!empty($genreIds)) {
                $genreIdStr = implode(',', array_map('intval', $genreIds)); // Ensure integers for safety
                $genreQuery = "SELECT `genre_name` FROM `tbl_genres` WHERE `gid` IN ($genreIdStr)";
                $genres = getRecords($genreQuery);
                
                foreach ($genres as $genre) {
                    $genreNames[] = $genre['genre_name'];
                }
            }

            $movie['genres'] = implode(',', $genreNames);
            $movie['user_interaction'] = [
                'liked' => $movie['liked'] == 1, // Convert to boolean
                'disliked' => $movie['disliked'] == 1 // Convert to boolean
            ];
            unset($movie['liked'], $movie['disliked']); // Remove temporary fields
        }
              $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully Loaded','data'=>$movies);
         }else{
             $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'No data Found');    
         }
        echo json_encode($response);
        exit();   
		break;	
  // ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
      case "getRentMovies":
    if (!app_login(intval($userid = @$_POST['userid']))) {
        $response = array(
            'status' => false,
            'error' => 1,
            'success' => 0,
            'msg' => 'Invalid Userid or User Not Active'
        );
        echo json_encode($response);
        exit();
    }

    $sql = "SELECT movie.*, lang.`language_name`, lang.`language_background`, 
            IF(tbl_likes.`is_like` = 1, 1, 0) AS liked,
            IF(tbl_likes.`is_like` = 0, 1, 0) AS disliked
            FROM tbl_rentmovies movie 
            LEFT JOIN tbl_language lang ON movie.`language_id` = lang.`id`   
            LEFT JOIN tbl_likes tbl_likes ON movie.`id` = tbl_likes.`post_id`  
                AND tbl_likes.`userid` = '$userid' 
                AND tbl_likes.`type` = 'rentmovie'
            WHERE movie.`status` = '1' AND lang.`status` = '1' 
            ORDER BY movie.`id` DESC";

    $movies = getRecords($sql);

    if ($movies) {
        foreach ($movies as &$movie) {
            // Get genres
            $genreIds = explode(',', $movie['genre_id']);
            $genreNames = [];

            if (!empty($genreIds)) {
                $genreIdStr = implode(',', array_map('intval', $genreIds));
                $genreQuery = "SELECT `genre_name` FROM `tbl_genres` WHERE `gid` IN ($genreIdStr)";
                $genres = getRecords($genreQuery);

                foreach ($genres as $genre) {
                    $genreNames[] = $genre['genre_name'];
                }
            }

            $movie['genres'] = implode(',', $genreNames);

            // User interaction
            $movie['user_interaction'] = [
                'liked' => $movie['liked'] == 1,
                'disliked' => $movie['disliked'] == 1
            ];
            unset($movie['liked'], $movie['disliked']);
            
            
            
            

                    
            // Check rent and calculate remaining days
            $getbuym = getRecord("SELECT * FROM `tbl_buymovies` WHERE userid='$userid' AND movie_id='{$movie['id']}' AND expire=0");

            if ($getbuym) {
                
                $userHaveRentAccess = false;
                $rentValidityDate = null;
                
                
                if ($getbuym && $getbuym['expire'] == 0) {
                        $validTill = date('Y-m-d', strtotime($getbuym['purchase_dt'] . ' + ' . intval($movie['movie_validation']) . ' days'));
                        if (strtotime($validTill) >= time()) {
                            $userHaveRentAccess = true;
                            $rentValidityDate = $validTill;
                        }
                    }

                    
                $purchaseDate = new DateTime($getbuym['purchase_dt']);
                $validationDays = intval($movie['movie_validation']);
                $expiryDate = clone $purchaseDate;
                $expiryDate->modify("+{$validationDays} days");

                $today = new DateTime();
                $remainingDays = $today < $expiryDate ? $today->diff($expiryDate)->days : 0;

                $movie['remaining_days'] = $remainingDays;
               $movie ['user_have_rent_access' ] =  $userHaveRentAccess;
               $movie['rent_validity_date'] = $rentValidityDate;
               $movie ['is_rented' ] = true;

                // 🔴 Check if rental is still valid
                if ($remainingDays > 0) {
                    $movie['rent_status'] = "1";
                } else {
                    $movie['rent_status'] = "0";
                }
            } else {
                $movie['rent_status'] = "0";
                $movie['remaining_days'] = 0;
            }
        }

        $response = [
            'status' => true,
            'error' => 0,
            'success' => 1,
            'msg' => 'Successfully Loaded',
            'data' => $movies
        ];
    } else {
        $response = [
            'status' => false,
            'error' => 1,
            'success' => 0,
            'msg' => 'No data Found'
        ];
    }

    echo json_encode($response);
    exit();
    break;
    
    
    case "getMyGetRentedMovies":
    if (!app_login(intval($userid = @$_POST['userid']))) {
        $response = array(
            'status' => false,
            'error' => 1,
            'success' => 0,
            'msg' => 'Invalid Userid or User Not Active'
        );
        echo json_encode($response);
        exit();
    }

    $sql = "SELECT movie.*, lang.`language_name`, lang.`language_background`, 
            IF(tbl_likes.`is_like` = 1, 1, 0) AS liked,
            IF(tbl_likes.`is_like` = 0, 1, 0) AS disliked
            FROM tbl_buymovies  bm LEFT JOIN tbl_rentmovies movie 
            on bm.movie_id = movie.`id`
            LEFT JOIN tbl_language lang ON movie.`language_id` = lang.`id`   
            LEFT JOIN tbl_likes tbl_likes ON movie.`id` = tbl_likes.`post_id`  
                AND tbl_likes.`userid` = '$userid' 
                AND tbl_likes.`type` = 'rentmovie'
            WHERE bm.userid ='$userid' AND  movie.`status` = '1' AND lang.`status` = '1' 
            ORDER BY movie.`id` DESC";

    $movies = getRecords($sql);

    if ($movies) {
        foreach ($movies as &$movie) {
            // Get genres
            $genreIds = explode(',', $movie['genre_id']);
            $genreNames = [];

            if (!empty($genreIds)) {
                $genreIdStr = implode(',', array_map('intval', $genreIds));
                $genreQuery = "SELECT `genre_name` FROM `tbl_genres` WHERE `gid` IN ($genreIdStr)";
                $genres = getRecords($genreQuery);

                foreach ($genres as $genre) {
                    $genreNames[] = $genre['genre_name'];
                }
            }

            $movie['genres'] = implode(',', $genreNames);

            // User interaction
            $movie['user_interaction'] = [
                'liked' => $movie['liked'] == 1,
                'disliked' => $movie['disliked'] == 1
            ];
            unset($movie['liked'], $movie['disliked']);
            
            
            
            

            // Check rent and calculate remaining days
            $getbuym = getRecord("SELECT * FROM `tbl_buymovies` WHERE userid='$userid' AND movie_id='{$movie['id']}' AND expire=0");

            if ($getbuym) {
                
                 $userHaveRentAccess = false;
                $rentValidityDate = null;
                
                
                if ($getbuym && $getbuym['expire'] == 0) {
                        $validTill = date('Y-m-d', strtotime($movie['purchase_dt'] . ' + ' . intval($movie['movie_validation']) . ' days'));
                        if (strtotime($validTill) >= time()) {
                            $userHaveRentAccess = true;
                            $rentValidityDate = $validTill;
                        }
                    }
                    
                    
                $purchaseDate = new DateTime($movie['purchase_dt']);
                $validationDays = intval($movie['movie_validation']);
                $expiryDate = clone $purchaseDate;
                $expiryDate->modify("+{$validationDays} days");

                $today = new DateTime();
                $remainingDays = $today < $expiryDate ? $today->diff($expiryDate)->days : 0;

                $movie['remaining_days'] = $remainingDays;
                $movie ['user_have_rent_access' ] =  $userHaveRentAccess;
                $movie['rent_validity_date'] = $rentValidityDate;
                $movie ['is_rented' ] =  true;

                // 🔴 Check if rental is still valid
                if ($remainingDays > 0) {
                    $movie['rent_status'] = "1";
                } else {
                    $movie['rent_status'] = "0";
                }
            } else {
                $movie['rent_status'] = "0";
                $movie['remaining_days'] = 0;
            }
        }

        $response = [
            'status' => true,
            'error' => 0,
            'success' => 1,
            'msg' => 'Successfully Loaded',
            'data' => $movies
        ];
    } else {
        $response = [
            'status' => false,
            'error' => 1,
            'success' => 0,
            'msg' => 'No data Found'
        ];
    }

    echo json_encode($response);
    exit();
    break;

   // ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    case "getMoreLikeMovies":
    if (!app_login(intval($userid = @$_POST['userid']))) {
        $response = [
            'status' => false,
            'error' => 1,
            'success' => 0,
            'msg' => 'Invalid User ID or User Not Active',
        ];
        echo json_encode($response);
        exit();
    }

    // Get input parameters
    $moid = @$_POST['moid'] ?? null; // Movie ID to filter similar movies

    if (!isset($moid)) {
        $response = [
            'status' => false,
            'error' => 1,
            'success' => 0,
            'msg' => 'Missing or invalid required movie ID parameter',
        ];
        echo json_encode($response);
        exit();
    }

    // Fetch the language_id and genre_id of the provided movie
    $query = "
        SELECT 
            `language_id`, 
            `genre_id` 
        FROM 
            tbl_movies 
        WHERE 
            `id` = '$moid' AND `status` = '1'
        LIMIT 1";
    $movieDetails = getRecord($query);

    if (!$movieDetails) {
        $response = [
            'status' => false,
            'error' => 1,
            'success' => 0,
            'msg' => 'Invalid Movie ID or Movie Not Found',
        ];
        echo json_encode($response);
        exit();
    }

    $language_id = $movieDetails['language_id'];
    $genre_id = $movieDetails['genre_id'];

    // Build WHERE clause for filtering
    $where_clauses = [
        "movie.`status` = '1'",
        "lang.`status` = '1'",
        "movie.`id` != '$moid'", // Exclude the provided movie ID
        "movie.`language_id` = '$language_id'", // Match by language
        "FIND_IN_SET(genre.gid, '$genre_id')" // Match by genre
    ];

    $where = implode(' AND ', $where_clauses);

    // Main SQL query to get similar movies
    $sql = "
        SELECT 
            movie.*,movie.movie_cost_type as cost_type,
            lang.`language_name`,
            lang.`language_background`,
            GROUP_CONCAT(DISTINCT genre.genre_name ORDER BY genre.genre_name ASC) AS genres,
            IF(tbl_likes.`is_like` = 1, 1, 0) AS liked,
            IF(tbl_likes.`is_like` = 0, 1, 0) AS disliked
        FROM 
            tbl_movies movie
        LEFT JOIN 
            tbl_language lang ON movie.`language_id` = lang.`id`
        LEFT JOIN 
            tbl_genres genre ON FIND_IN_SET(genre.`gid`, movie.`genre_id`)
        LEFT JOIN 
            tbl_likes tbl_likes ON movie.`id` = tbl_likes.`post_id` 
            AND tbl_likes.`userid` = '$userid' 
            AND tbl_likes.`type` = 'movies'
        WHERE 
            $where
        GROUP BY 
            movie.`id`
        ORDER BY 
            movie.`release_date` DESC";

    $movies = getRecords($sql);

    if ($movies) {
        foreach ($movies as &$movie) {
            $movie['user_interaction'] = [
                'liked' => $movie['liked'] == 1,
                'disliked' => $movie['disliked'] == 1
            ];
            unset($movie['liked'], $movie['disliked']);
        }

        $response = [
            'status' => true,
            'error' => 0,
            'success' => 1,
            'msg' => 'Successfully Loaded',
            'data' => $movies
        ];
    } else {
        $response = [
            'status' => false,
            'error' => 1,
            'success' => 0,
            'msg' => 'No data Found'
        ];
    }

    echo json_encode($response);
    exit();
    break;



    // ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		
	case "comment" :
         if(!app_login($userid = intval(@$_POST['userid'])))
    		{
              $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid Userid or User Not Active');  
    			echo json_encode($response);
    			exit();
    		}
    		$mid = @$_POST['mid'];
    		$type = @$_POST['type'];
    		$comment = @$_POST['comment'];
    		if(isset($mid) &&  isset($type)  && isset($comment)){
    		    
    		    if($type=='movies'){
    		        $check=getRecordField("SELECT * FROM tbl_movies WHERE id ='$mid'");
    		       // print_r($check);die;
    		        if(empty($check)){
    		          $response = [
                            'status' => false,
                            'error' => 1,
                            'success' => 0,
                            'msg' => 'Invalid Movie ID ',
                        ];
                        echo json_encode($response);
                        exit();  
    		        }
    		        $query = "insert into tbl_comments (user_id, post_id, comment_text,type) values('{$userid}', '{$mid}', '".@$_POST['comment']."','movies');";
                    query($query);
                
                    $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully Posted');
    		    }
    		    else if($type=='series'){
    		        $check=getRecord("SELECT * FROM  tbl_series WHERE id = '$mid' ");
    		        if(empty($check)){
    		          $response = [
                            'status' => false,
                            'error' => 1,
                            'success' => 0,
                            'msg' => 'Invalid series ID ',
                        ];
                        echo json_encode($response);
                        exit();  
    		        }
    		       $query = "insert into tbl_comments (user_id, post_id, comment_text,type) values('{$userid}', '{$mid}', '".@$_POST['comment']."','series');";
                    query($query);
                
                    $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully Posted'); 
    		    }
    		    else if($type=='shows'){
    		        $check=getRecord("SELECT * FROM  tbl_shows WHERE id = '$mid' ");
    		        if(empty($check)){
    		          $response = [
                            'status' => false,
                            'error' => 1,
                            'success' => 0,
                            'msg' => 'Invalid shows ID ',
                        ];
                        echo json_encode($response);
                        exit();  
    		        }
    		       $query = "insert into tbl_comments (user_id, post_id, comment_text,type) values('{$userid}', '{$mid}', '".@$_POST['comment']."','shows');";
                    query($query);
                
                    $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully Posted'); 
    		    }else if($type=='drama'){
    		        $check=getRecord("SELECT * FROM  tbl_drama WHERE id = '$mid' ");
    		        if(empty($check)){
    		          $response = [
                            'status' => false,
                            'error' => 1,
                            'success' => 0,
                            'msg' => 'Invalid drama ID ',
                        ];
                        echo json_encode($response);
                        exit();  
    		        }
    		       $query = "insert into tbl_comments (user_id, post_id, comment_text,type) values('{$userid}', '{$mid}', '".@$_POST['comment']."','drama');";
                    query($query);
                
                    $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully Posted'); 
    		    }else if($type=='shortfilm'){
    		        $check=getRecord("SELECT * FROM  tbl_shortfilms WHERE id = '$mid' ");
    		        if(empty($check)){
    		          $response = [
                            'status' => false,
                            'error' => 1,
                            'success' => 0,
                            'msg' => 'Invalid film ID ',
                        ];
                        echo json_encode($response);
                        exit();  
    		        }
    		       $query = "insert into tbl_comments (user_id, post_id, comment_text,type) values('{$userid}', '{$mid}', '".@$_POST['comment']."','shortfilm');";
                    query($query);
                
                    $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully Posted'); 
    		    }else if($type=='song'){
    		        $check=getRecord("SELECT * FROM  tbl_songs WHERE id = '$mid' ");
    		        if(empty($check)){
    		          $response = [
                            'status' => false,
                            'error' => 1,
                            'success' => 0,
                            'msg' => 'Invalid song ID ',
                        ];
                        echo json_encode($response);
                        exit();  
    		        }
    		       $query = "insert into tbl_comments (user_id, post_id, comment_text,type) values('{$userid}', '{$mid}', '".@$_POST['comment']."','song');";
                    query($query);
                
                    $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully Posted'); 
    		    }else if($type=='event'){
    		        $check=getRecord("SELECT * FROM  tbl_events WHERE id = '$mid' ");
    		        if(empty($check)){
    		          $response = [
                            'status' => false,
                            'error' => 1,
                            'success' => 0,
                            'msg' => 'Invalid event ID ',
                        ];
                        echo json_encode($response);
                        exit();  
    		        }
    		       $query = "insert into tbl_comments (user_id, post_id, comment_text,type) values('{$userid}', '{$mid}', '".@$_POST['comment']."','event');";
                    query($query);
                
                    $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully Posted'); 
    		    }
    		    else{
    		         $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'invalid type');
    		    }
		}
		else
		    $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Missing required params');
        echo json_encode($response);
        exit();   
	break;
  // ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		
	case "getcomments" :
         if(!app_login($userid = intval(@$_POST['userid'])))
    		{
              $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid Userid or User Not Active');  
    			echo json_encode($response);
    			exit();
    		}
    		$mid = @$_POST['mid'];
    		$type = @$_POST['type'];
    			if(isset($mid) &&  isset($type)  ){
                
            if($type=='movies'){
                $check=getRecord("SELECT * FROM  tbl_movies WHERE id = '$mid' ");
    		        if(empty($check)){
    		          $response = [
                            'status' => false,
                            'error' => 1,
                            'success' => 0,
                            'msg' => 'Invalid Movie ID ',
                        ];
                        echo json_encode($response);
                        exit();  
    		        }
                $getdata=getRecords("SELECT c.*,u.name as username,u.image as userimage FROM tbl_comments c LEFT JOIN tbl_users u on c.user_id=u.id WHERE c.type='movies' and c.post_id='$mid'");
                if($getdata){
                    $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully Loaded','data'=>$getdata);
                }else{
                    $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'No data found');
                }
                
            }elseif($type=='series'){
                $check=getRecord("SELECT * FROM  tbl_series WHERE id = '$mid' ");
    		        if(empty($check)){
    		          $response = [
                            'status' => false,
                            'error' => 1,
                            'success' => 0,
                            'msg' => 'Invalid series ID ',
                        ];
                        echo json_encode($response);
                        exit();  
    		        }
                $getdata=getRecords("SELECT c.*,u.name as username,u.image as userimage FROM tbl_comments c LEFT JOIN tbl_users u on c.user_id=u.id WHERE type='series' and post_id='$mid'");
                
                 if($getdata){
                    $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully Loaded','data'=>$getdata);
                }else{
                    $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'No data found');
                }
            }elseif($type=='shows'){
                $check=getRecord("SELECT * FROM  tbl_shows WHERE id = '$mid' ");
    		        if(empty($check)){
    		          $response = [
                            'status' => false,
                            'error' => 1,
                            'success' => 0,
                            'msg' => 'Invalid shows ID ',
                        ];
                        echo json_encode($response);
                        exit();  
    		        }
                $getdata=getRecords("SELECT c.*,u.name as username,u.image as userimage FROM tbl_comments c LEFT JOIN tbl_users u on c.user_id=u.id WHERE type='shows' and post_id='$mid'");
                
                if($getdata){
                    $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully Loaded','data'=>$getdata);
                }else{
                    $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'No data found');
                }
            }elseif($type=='shortfilm'){
                $check=getRecord("SELECT * FROM  tbl_shortfilms WHERE id = '$mid' ");
    		        if(empty($check)){
    		          $response = [
                            'status' => false,
                            'error' => 1,
                            'success' => 0,
                            'msg' => 'Invalid film ID ',
                        ];
                        echo json_encode($response);
                        exit();  
    		        }
                $getdata=getRecords("SELECT c.*,u.name as username,u.image as userimage FROM tbl_comments c LEFT JOIN tbl_users u on c.user_id=u.id WHERE type='shortfilm' and post_id='$mid'");
                
                 if($getdata){
                    $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully Loaded','data'=>$getdata);
                }else{
                    $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'No data found');
                }
            }elseif($type=='song'){
                $check=getRecord("SELECT * FROM  tbl_songs WHERE id = '$mid' ");
    		        if(empty($check)){
    		          $response = [
                            'status' => false,
                            'error' => 1,
                            'success' => 0,
                            'msg' => 'Invalid song ID ',
                        ];
                        echo json_encode($response);
                        exit();  
    		        }
                $getdata=getRecords("SELECT c.*,u.name as username,u.image as userimage FROM tbl_comments c LEFT JOIN tbl_users u on c.user_id=u.id WHERE type='song' and post_id='$mid'");
                
                 if($getdata){
                    $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully Loaded','data'=>$getdata);
                }else{
                    $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'No data found');
                }
            }elseif($type=='drama'){
                $check=getRecord("SELECT * FROM  tbl_drama WHERE id = '$mid' ");
    		        if(empty($check)){
    		          $response = [
                            'status' => false,
                            'error' => 1,
                            'success' => 0,
                            'msg' => 'Invalid drama ID ',
                        ];
                        echo json_encode($response);
                        exit();  
    		        }
                $getdata=getRecords("SELECT c.*,u.name as username,u.image as userimage FROM tbl_comments c LEFT JOIN tbl_users u on c.user_id=u.id WHERE type='drama' and post_id='$mid'");
                
                if($getdata){
                    $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully Loaded','data'=>$getdata);
                }else{
                    $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'No data found');
                }
            }elseif($type=='event'){
                $check=getRecord("SELECT * FROM  tbl_events WHERE id = '$mid' ");
    		        if(empty($check)){
    		          $response = [
                            'status' => false,
                            'error' => 1,
                            'success' => 0,
                            'msg' => 'Invalid event ID ',
                        ];
                        echo json_encode($response);
                        exit();  
    		        }
                $getdata=getRecords("SELECT c.*,u.name as username,u.image as userimage FROM tbl_comments c LEFT JOIN tbl_users u on c.user_id=u.id WHERE type='event' and post_id='$mid'");
                
                 if($getdata){
                    $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully Loaded','data'=>$getdata);
                }else{
                    $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'No data found');
                }
            }
            else{
    		         $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'invalid type');
    		    }   
		}
		else
		    $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Missing params');
        echo json_encode($response);
        exit();   
	break;
// ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
case 'send_otp':

    $phone = @$_REQUEST['phone'];

    if (isset($phone)) {
        $otp = rand(1111, 9999);
        $api_key = '7ebb202c-b633-11ef-8b17-0200cd936042'; // Replace with your valid API key
        $message = urlencode("Login OTP");
        $url = "https://2factor.in/API/V1/$api_key/SMS/$phone/$otp/$message";

        // Send OTP via cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $api_response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code == 200) {
            // Only insert OTP if SMS was sent successfully
            $query = "UPDATE user_otp SET deleted = 1 WHERE phone = '{$phone}'";
            query($query);

            $query = "INSERT INTO user_otp (phone, otp) VALUES ('{$phone}', '{$otp}')";
            query($query);

            $response = array(
                'status' => true,
                'error' => 0,
                'success' => 1,
                'result' => array(
                    'message' => 'OTP sent successfully',
                    'otp' => $otp
                )
            );
        } else {
            // cURL failed or API returned error
            $response = array(
                'status' => false,
                'error' => 1,
                'success' => 0,
                'msg' => 'Failed to send OTP. Please try again.',
                'api_response' => $api_response
            );
        }

    } else {
        $response = array(
            'status' => false,
            'error' => 1,
            'success' => 0,
            'msg' => 'Missing required params'
        );
    }

    echo json_encode($response);
    exit();
    break;


	// ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	case 'verify_otp':
	    if (($otp = @$_REQUEST['otp']) && ($phone = @$_REQUEST['phone'])) {
	        
	        $checkphone=getRecordField($query = "SELECT * FROM `tbl_users` WHERE phone='$phone' ");
	        if (empty($checkphone)) {
            $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' => 'Invalid Mobile No');
            echo json_encode($response);
            exit();
        }
    
			
			
				$data = getRecordField($query = "select otp from user_otp where phone='{$_REQUEST['phone']}' and deleted=0 order by id desc limit 1");
			
		if($_REQUEST['otp'] == $data)
			{
				$query = "update user_otp set deleted=1 where phone='{$_REQUEST['phone']}';";
				query($query);
				
				$response = array('status' => true, 'error' => 0, 'success' => 1,'message'=> "Sucessfully Verified");
			}
			else
				$response = array('status' => false, 'error' => 1, 'success' => 0, 'message' => "Wrong OTP Or Wrong Phone");
		}else{
		    $response = array('status' => false, 'error' => 1, 'success' => 0, 'message' => "Missing required param");
		}
		
		echo json_encode($response);
		exit();
	    break;
	// ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        case "getTvCategories" :
        if (!app_login(intval($userid = @$_POST['userid']))) {
            $response = array(
                'status' => false,
                'error' => 1,
                'success' => 0,
                'msg' => 'Invalid Userid or User Not Active'
            );
            echo json_encode($response);
            exit();
        }
        
       
        
        	$getdata=getRecords("SELECT * FROM tbl_tv_category WHERE  deleted=0");
    		if(sizeof($getdata)>0){
    		  $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Loaded Successfully','data'=>$getdata);  
    		}else{
    		  $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Data Not Found');   
    		}
   
        echo json_encode($response);
        exit();   
		break;
	// ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
      case "getCategoriesWiseTVSeries" :
    if (!app_login(intval($userid = @$_POST['userid']))) {
        $response = array(
            'status' => false,
            'error' => 1,
            'success' => 0,
            'msg' => 'Invalid Userid or User Not Active'
        );
        echo json_encode($response);
        exit();
    }
    
    // Fetch categories
    $category_sql = "SELECT * FROM `tbl_tv_category` WHERE status = 1";
    $categories = getRecords($category_sql);
    
    // Check if categories exist
    if ($categories) {
        
        // Prepare array to hold series data
        $response_data = [];
        
        // Loop through each category and fetch the series for that category
        foreach ($categories as $category) {
            $category_id = $category['id'];
            $sql = "SELECT s.*, 
                        lang.`language_name`,
                        lang.`language_background`,
                        GROUP_CONCAT(DISTINCT genre.genre_name ORDER BY genre.genre_name ASC) AS genres,
                        IF(tbl_likes.`is_like` = 1, 1, 0) AS liked,
            IF(tbl_likes.`is_like` = 0, 1, 0) AS disliked,
                        cat.category_name
                    FROM tbl_series s
                    LEFT JOIN tbl_tv_category cat ON cat.id = s.category_id
                    LEFT JOIN tbl_language lang ON s.`language_id` = lang.`id`
                    LEFT JOIN tbl_genres genre ON FIND_IN_SET(genre.`gid`, s.`genre_id`)
                    LEFT JOIN tbl_likes tbl_likes ON s.`id` = tbl_likes.`post_id` 
                        AND tbl_likes.`userid` = '$userid' 
                        AND tbl_likes.`type` = 'series'
                    WHERE s.status = '1' 
                    AND s.category_id = '$category_id'
                    GROUP BY s.id
                    ORDER BY s.id DESC";
            $series = getRecords($sql);
            
            // If series found for this category, process them
            if ($series) {
                foreach ($series as &$item) {
                    $item['user_interaction'] = [
                        'liked' => $item['liked'] == 1, // Convert to boolean
                        'disliked' => $item['disliked'] == 1 // Convert to boolean
                    ];
                    unset($item['liked'], $item['disliked']);
                    $series_id = $item['id']; // Get the series ID

                    // Fetch seasons for the current series
                    $item['seasons'] = getRecords("SELECT * FROM tbl_season WHERE series_id = $series_id");
                }
                
                // Add the series to the category
                $category['series'] = $series;
            }
            
            // Add the category and its series to the response data
            $response_data[] = $category;
        }

        $response = array(
            'status' => true,
            'error' => 0,
            'success' => 1,
            'msg' => 'Successfully Loaded',
            'data' => $response_data
        );
    } else {
        $response = array(
            'status' => false,
            'error' => 1,
            'success' => 0,
            'msg' => 'No categories found'
        );
    }

    echo json_encode($response);
    exit();
    break;

	// ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
     
       case "getTVSeries":
        if (!app_login(intval($userid = @$_POST['userid']))) {
            $response = array(
                'status' => false,
                'error' => 1,
                'success' => 0,
                'msg' => 'Invalid Userid or User Not Active'
            );
            echo json_encode($response);
            exit();
        }

        // Fetch TV series with categories
        $sql = "SELECT s.*, series_cost_type as cost_type,
        lang.`language_name`,
            lang.`language_background`,
                       GROUP_CONCAT(DISTINCT genre.genre_name ORDER BY genre.genre_name ASC) AS genres,cat.category_name,
                        IF(tbl_likes.`is_like` = 1, 1, 0) AS liked,
            IF(tbl_likes.`is_like` = 0, 1, 0) AS disliked
                FROM tbl_series s
                LEFT JOIN tbl_tv_category cat ON cat.id=s.category_id
                LEFT JOIN 
            tbl_language lang ON s.`language_id` = lang.`id`
                LEFT JOIN tbl_genres genre ON FIND_IN_SET(genre.`gid`, s.`genre_id`)
                LEFT JOIN tbl_likes tbl_likes ON s.`id` = tbl_likes.`post_id`  AND tbl_likes.`userid` = '$userid' AND tbl_likes.`type` = 'series'
                WHERE s.status = '1'
                GROUP BY s.id
                ORDER BY s.id DESC;";
        $series = getRecords($sql);
    
        if ($series) {
            foreach ($series as &$item) {
                $item['user_interaction'] = [
                     'liked' => $item['liked'] == 1, // Convert to boolean
                'disliked' => $item['disliked'] == 1 // Convert to boolean
                    ];
                    
                     unset($movie['liked'], $movie['disliked']);
                $series_id = $item['id']; // Get the series ID
    
                // Fetch seasons for the current series
                $item['seasons'] = getRecords("SELECT * FROM tbl_season WHERE series_id = $series_id");
            }
            
           
    
            $response = array(
                'status' => true,
                'error' => 0,
                'success' => 1,
                'msg' => 'Successfully Loaded',
                'data' => $series
            );
        } else {
            $response = array(
                'status' => false,
                'error' => 1,
                'success' => 0,
                'msg' => 'No data Found'
            );
        }
    
        echo json_encode($response);
        exit();
        break;
	   
	// ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
       case "getSerieseEpisodes":
        if (!app_login(intval($userid = @$_POST['userid']))) {
            $response = array(
                'status' => false,
                'error' => 1,
                'success' => 0,
                'msg' => 'Invalid Userid or User Not Active'
            );
            echo json_encode($response);
            exit();
        }
         $series_id = @$_POST['series_id'];
         $season_id = @$_POST['season_id'];
         if(isset($series_id) && (isset($season_id))){
             $checkid= getRecord("SELECT * FROM `tbl_series` WHERE id='$series_id' ");
             if(empty($checkid)){
    	           $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid series_id');
    	            echo json_encode($response);
                    exit(); 
    	       }
    	       
    	       
    	       $checksid= getRecord("SELECT * FROM `tbl_season` WHERE id='$season_id' ");
             if(empty($checksid)){
    	           $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid tbl_season');
    	            echo json_encode($response);
                    exit(); 
    	       }
             
             $sql = "SELECT * FROM `tbl_episode`  WHERE series_id='$series_id' and season_id='$season_id' and status=1 ";
            $episodes = getRecords($sql);
             if ($episodes) {
                 $response = array(
                'status' => true,
                'error' => 0,
                'success' => 1,
                'msg' => 'Successfully Loaded',
                'data' => $episodes
            );
             } else {
            $response = array(
                'status' => false,
                'error' => 1,
                'success' => 0,
                'msg' => 'No data Found'
            );
            }
         }else{
             $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Missing Required Params');    
         }

        // Fetch TV series with categories
        echo json_encode($response);
        exit();
        break;
	// ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        case "getTVShows" :
         if (!app_login(intval($userid = @$_POST['userid']))) {
            $response = array(
                'status' => false,
                'error' => 1,
                'success' => 0,
                'msg' => 'Invalid Userid or User Not Active'
            );
            echo json_encode($response);
            exit();
        }

        // Fetch TV series with categories
        $sql = "SELECT s.id,s.category_id,s.home_cat_id,s.genre_id,s.language_id,s.shows_name as series_name,s.shows_desc as series_desc ,s.shows_poster as series_poster ,s.shows_cover as series_cover, s.total_views,
        s.total_rate,s.trailer_url,s.trailer_type,s.trailer_id,s.rate_avg,s.is_featured,s.is_slider,s.status,s.release_date,s.director_name,s.maturity_rating,s.total_watch_time,s.imdb_rating,
        lang.`language_name`,
            lang.`language_background`,
                       GROUP_CONCAT(DISTINCT genre.genre_name ORDER BY genre.genre_name ASC) AS genres,cat.category_name,
                        IF(tbl_likes.`is_like` = 1, 1, 0) AS liked,
            IF(tbl_likes.`is_like` = 0, 1, 0) AS disliked
                FROM tbl_shows s
                LEFT JOIN tbl_tv_category cat ON cat.id=s.category_id
                LEFT JOIN 
            tbl_language lang ON s.`language_id` = lang.`id`
                LEFT JOIN tbl_genres genre ON FIND_IN_SET(genre.`gid`, s.`genre_id`)
                LEFT JOIN tbl_likes tbl_likes ON s.`id` = tbl_likes.`post_id`  AND tbl_likes.`userid` = '$userid' AND tbl_likes.`type` = 'shows'
                WHERE s.status = '1'
                GROUP BY s.id
                ORDER BY s.id DESC;";
        $series = getRecords($sql);
    
        if ($series) {
            foreach ($series as &$item) {
                $item['user_interaction'] = [
                     'liked' => $item['liked'] == 1, // Convert to boolean
                'disliked' => $item['disliked'] == 1 // Convert to boolean
                    ];
                    
                     unset($movie['liked'], $movie['disliked']);
                $series_id = $item['id']; // Get the series ID
    
                // Fetch seasons for the current series
                $item['seasons'] = getRecords("SELECT * FROM tbl_tv_season WHERE shows_id = $series_id");
            }
            
           
    
            $response = array(
                'status' => true,
                'error' => 0,
                'success' => 1,
                'msg' => 'Successfully Loaded',
                'data' => $series
            );
        } else {
            $response = array(
                'status' => false,
                'error' => 1,
                'success' => 0,
                'msg' => 'No data Found'
            );
        }
    
        echo json_encode($response);
        exit();
        break;
	   
        
		
			case "getShowsEpisodes":
         if (!app_login(intval($userid = @$_POST['userid']))) {
            $response = array(
                'status' => false,
                'error' => 1,
                'success' => 0,
                'msg' => 'Invalid Userid or User Not Active'
            );
            echo json_encode($response);
            exit();
        }
         $shows_id = @$_POST['shows_id'];
         $season_id = @$_POST['season_id'];
         if(isset($shows_id) && (isset($season_id))){
             $checkid= getRecord("SELECT * FROM `tbl_shows` WHERE id='$shows_id' ");
             if(empty($checkid)){
    	           $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid shows_id');
    	            echo json_encode($response);
                    exit(); 
    	       }
    	       
    	       
    	       $checksid= getRecord("SELECT * FROM `tbl_tv_season` WHERE id='$season_id' ");
             if(empty($checksid)){
    	           $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid tbl_season');
    	            echo json_encode($response);
                    exit(); 
    	       }
             
             $sql = "SELECT * FROM `tbl_tv_episode`  WHERE shows_id='$shows_id' and season_id='$season_id' and status=1 ";
            $episodes = getRecords($sql);
             if ($episodes) {
                 $response = array(
                'status' => true,
                'error' => 0,
                'success' => 1,
                'msg' => 'Successfully Loaded',
                'data' => $episodes
            );
             } else {
            $response = array(
                'status' => false,
                'error' => 1,
                'success' => 0,
                'msg' => 'No data Found'
            );
            }
         }else{
             $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Missing Required Params');    
         }

        // Fetch TV series with categories
        echo json_encode($response);
        exit();
        break;
	 // ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        case "getTvshowswithEpisode" :
         if (!app_login(intval($userid = @$_POST['userid']))) {
            $response = array(
                'status' => false,
                'error' => 1,
                'success' => 0,
                'msg' => 'Invalid Userid or User Not Active'
            );
            echo json_encode($response);
            exit();
         }
         
          $sql = "SELECT s.*, lang.`language_name`, lang.`language_background`, IF(tbl_likes.`is_like` = 1, 1, 0) AS liked,IF(tbl_likes.`is_like` = 0, 1, 0) AS disliked FROM tbl_shows s
            LEFT JOIN tbl_language lang ON s.`language_id` = lang.`id`    LEFT JOIN tbl_likes tbl_likes ON s.`id` = tbl_likes.`post_id`  AND tbl_likes.`userid` = '$userid' AND tbl_likes.`type` = 'shows'
            WHERE s.status = '1' ORDER BY s.id DESC;";
          $shows = getRecords($sql);
           if ($shows) {
               foreach ($shows as &$show) {
                   $genreIds = explode(',', $show['genre_id']);
            $genreNames = [];

            if (!empty($genreIds)) {
                $genreIdStr = implode(',', array_map('intval', $genreIds)); // Ensure integers for safety
                $genreQuery = "SELECT `genre_name` FROM `tbl_genres` WHERE `gid` IN ($genreIdStr)";
                $genres = getRecords($genreQuery);
                
                foreach ($genres as $genre) {
                    $genreNames[] = $genre['genre_name'];
                }
            }

            $show['genres'] = implode(',', $genreNames);
            $show['user_interaction'] = [
                'liked' => $show['liked'] == 1,
                'disliked' => $show['disliked'] == 1
            ];
            unset($show['liked'], $show['disliked']);
             $show_id = intval($show['id']); // Ensure it's an integer
            $episodeQuery = "SELECT e.*,s.season_name  FROM `tbl_tv_episode` e left join tbl_tv_season s on e.shows_id=s.id  WHERE e.`shows_id` = '$show_id'  and e.status=1 ORDER BY e.`id` desc";
            $episodes = getRecords($episodeQuery);
            $show['episodes'] = $episodes ?: [];
               }
               $response = [
                'status' => true,
                'error' => 0,
                'success' => 1,
                'msg' => 'Successfully Loaded',
                'data' => $shows
            ];
           }else{
               $response = [
            'status' => false,
            'error' => 1,
            'success' => 0,
            'msg' => 'No data Found'
        ];
           }
   
        echo json_encode($response);
        exit();   
		break;	
	// ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
       case "likeDislike":
        // Check if user is logged in
        $userid = intval(@$_POST['userid']);
        if (!app_login($userid)) {
            $response = array(
                'status' => false,
                'error' => 1,
                'success' => 0,
                'msg' => 'Invalid User ID or User Not Active'
            );
            echo json_encode($response);
            exit();
        }
    
        // Fetch required parameters
        $post_id = intval(@$_POST['post_id']);
        $type = @$_POST['type'];
        $is_like = intval(@$_POST['is_like']); // 0 for unlike, 1 for like
    
        // Validate input parameters
        if (!isset($post_id, $type, $is_like) || ($is_like !== 0 && $is_like !== 1)) {
            $response = array(
                'status' => false,
                'error' => 1,
                'success' => 0,
                'msg' => 'Missing or invalid required parameters'
            );
            echo json_encode($response);
            exit();
        }
    
        // Check if the like/dislike already exists
        $existingRecord = getRecord("SELECT `id`, `is_like` FROM `tbl_likes` WHERE `userid` = '$userid' AND `post_id` = '$post_id' AND `type` = '$type' LIMIT 1");
    
        if ($existingRecord) {
            // Update the record if it exists
            $currentLikeStatus = $existingRecord['is_like'];
            if ($currentLikeStatus == $is_like) {
                $response = array(
                    'status' => false,
                    'error' => 1,
                    'success' => 0,
                    'msg' => 'Action already performed'
                );
            } else {
                query("DELETE FROM `tbl_likes` WHERE `id` = '{$existingRecord['id']}'");
                $response = array(
                    'status' => true,
                    'error' => 0,
                    'success' => 1,
                    'msg' => $is_like ? 'Post liked successfully' : 'Post unliked successfully'
                );
            }
        } else {
            // Insert a new record if none exists
            query("INSERT INTO `tbl_likes` (`userid`, `post_id`, `type`, `is_like`) VALUES ('$userid', '$post_id', '$type', '$is_like')");
            $response = array(
                'status' => true,
                'error' => 0,
                'success' => 1,
                'msg' => $is_like ? 'Post liked successfully' : 'Post unliked successfully'
            );
        }

        // Return the response
         echo json_encode($response);
         exit();
         break;
    // ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        case "checkLikeDislike" :
        $userid = intval(@$_POST['userid']);
        if (!app_login($userid)) {
                $response = array(
                    'status' => false,
                    'error' => 1,
                    'success' => 0,
                    'msg' => 'Invalid User ID or User Not Active'
                );
                echo json_encode($response);
                exit();
            }
         $post_id = intval(@$_POST['post_id']);
         $type = @$_POST['type'];
          if (!isset($post_id, $type) ) {
            $response = array(
                'status' => false,
                'error' => 1,
                'success' => 0,
                'msg' => 'Missing or invalid required parameters'
            );
            echo json_encode($response);
            exit();
        }
    
         
         $check=getRecord("SELECT `id`, `is_like` FROM `tbl_likes` WHERE `userid` = '$userid' AND `post_id` = '$post_id' AND `type` = '$type' LIMIT 1");
         if($check){
            $response = [
            'status' => true,
            'error' => 0,
            'success' => 1,
            'msg' => 'U Already Liked'
            ]; 
         }else{
             $response = array(
                    'status' => false,
                    'error' => 1,
                    'success' => 0,
                    'msg' => 'U did not Liked'
                );
             
         }
   
        echo json_encode($response);
        exit();   
		break;
   
	// ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
       case "buyRentMovie" :
        $userid = intval(@$_POST['userid']);
        $movie_id = intval(@$_POST['movie_id']);
        $amount = @$_POST['amount'];
        if (!app_login($userid)) {
            $response = array(
                'status' => false,
                'error' => 1,
                'success' => 0,
                'msg' => 'Invalid User ID or User Not Active'
            );
            echo json_encode($response);
            exit();
        }
        
        if(isset($movie_id) && isset($amount)){
            $checkmovie=getRecord("SELECT * FROM tbl_rentmovies WHERE id='$movie_id' and status=1");
            if(empty($checkmovie)){
                 $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid Movie_id');     
                 echo json_encode($response);
                 exit();   
            }
            
            
                $getdata=getRecord("SELECT * FROM tbl_buymovies WHERE userid='$userid' and movie_id='$movie_id'  ");
                if($getdata){
                    $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'You already buy this movie');   
                }else{
                  $insertQuery=query("INSERT INTO `tbl_buymovies`(`userid`, `movie_id`, `amount`, `purchase_dt`,`type`) VALUES ('$userid','$movie_id','$amount','$fdate','rent')");   
                  $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Buy Movie Successfully','movie_id'=>$movie_id);  
                }
                
            
            
        }else{
           $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Missing Required Params');     
        }
   
        echo json_encode($response);
        exit();   
		break;	
	// ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
case "searchAll":
    if (!app_login(intval($userid = @$_POST['userid']))) {
        echo json_encode([
            'status' => false,
            'error' => 1,
            'success' => 0,
            'msg' => 'Invalid Userid or User Not Active'
        ]);
        exit();
    }

    $search = trim(@$_POST['search']);
    $type = @$_POST['type']; // Accepts a single type or multiple types as a comma-separated string

    if (isset($search) && isset($type)) {
        if (!empty($search)) {
            $response = [
                'status' => true,
                'error' => 0,
                'success' => 1
            ];

            // Convert comma-separated types to an array
            $types = explode(',', $type);
            $validTypes = ['movies', 'series', 'shows', 'shortfilms', 'drama', 'songs', 'events', 'all','livetv'];

            // Validate types
            $types = array_intersect($types, $validTypes);
            if (empty($types)) {
                echo json_encode([
                    'status' => false,
                    'error' => 1,
                    'success' => 0,
                    'msg' => 'Invalid search type'
                ]);
                exit();
            }

            // If 'all' is present, override and search all types
            if (in_array('all', $types)) {
                $types = ['movies', 'series', 'shows', 'shortfilms', 'drama', 'songs', 'events','livetv'];
            }

            // Perform search for each type
            foreach ($types as $t) {
                switch ($t) {
                    case "movies":
                        $response['movies'] = getSearchMovies($search, $userid) ?: [];
                        break;
                    case "series":
                        $response['series'] = getSearchSeries($search, $userid) ?: [];
                        break;
                    case "shows":
                        $response['shows'] = getSearchshows($search, $userid) ?: [];
                        break;
                    case "shortfilms":
                        $response['shortfilms'] = getSearchshortfilms($search, $userid) ?: [];
                        break;
                    case "drama":
                        $response['drama'] = getSearchdrama($search, $userid) ?: [];
                        break;
                    case "songs":
                        $response['songs'] = getSearchsongs($search, $userid) ?: [];
                        break;
                    case "events":
                        $response['events'] = getSearchevents($search, $userid) ?: [];
                        break;
                    case "livetv":
                        $response['livetv'] = getSearchliveTv($search, $userid) ?: [];
                        break;
                }
            }

            // Check if all results are empty
            $hasResults = false;
            foreach ($types as $t) {
                if (!empty($response[$t])) {
                    $hasResults = true;
                    break;
                }
            }

            if (!$hasResults) {
                $response['msg'] = 'No results found';
            }

            echo json_encode($response);
            exit();
        } else {
            echo json_encode([
                'status' => false,
                'error' => 1,
                'success' => 0,
                'msg' => 'Search query cannot be empty'
            ]);
            exit();
        }
    } else {
        echo json_encode([
            'status' => false,
            'error' => 1,
            'success' => 0,
            'msg' => 'Missing required fields'
        ]);
        exit();
    }
    break;



	// ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
       
	// ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        case "getShortfilms" :
   
       if(!app_login(intval($userid = @$_POST['userid'])))
        		{
                  $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid Userid or User Not Active');  
        			echo json_encode($response);
        			exit();
        		}
        		
    		
    		 $sql = "SELECT movie.*, lang.`language_name`, lang.`language_background`, 
            IF(tbl_likes.`is_like` = 1, 1, 0) AS liked,
            IF(tbl_likes.`is_like` = 0, 1, 0) AS disliked
            FROM tbl_shortfilms movie 
            LEFT JOIN tbl_language lang ON movie.`language_id` = lang.`id`   
            LEFT JOIN tbl_likes tbl_likes ON movie.`id` = tbl_likes.`post_id`  
                AND tbl_likes.`userid` = '$userid' 
                AND tbl_likes.`type` = 'shortfilm'
            WHERE movie.`status` = '1' 
            ORDER BY movie.`id` DESC";

           $movies = getRecords($sql);
            if ($movies) {
        foreach ($movies as &$movie) {
            // Convert liked and disliked to boolean
            

            // Fetch genre names
            $genreIds = explode(',', $movie['genre_id']);
            $genreNames = [];

            if (!empty($genreIds)) {
                $genreIdStr = implode(',', array_map('intval', $genreIds)); // Ensure integers for safety
                $genreQuery = "SELECT `genre_name` FROM `tbl_genres` WHERE `gid` IN ($genreIdStr)";
                $genres = getRecords($genreQuery);
                
                foreach ($genres as $genre) {
                    $genreNames[] = $genre['genre_name'];
                }
            }

            $movie['genres'] = implode(',', $genreNames);
            
            
            $movie['user_interaction'] = [
                'liked' => $movie['liked'] == 1,
                'disliked' => $movie['disliked'] == 1
            ];
            unset($movie['liked'], $movie['disliked']);// Convert array to comma-separated string
            
            
            
        }

        $response = [
            'status' => true,
            'error' => 0,
            'success' => 1,
            'msg' => 'Successfully Loaded',
            'data' => $movies
        ];
    } else {
        $response = [
            'status' => false,
            'error' => 1,
            'success' => 0,
            'msg' => 'No data Found'
        ];
    }
   
        echo json_encode($response);
        exit();   
		break;		
		
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        case "getDrama" :
   
       if(!app_login(intval($userid = @$_POST['userid'])))
        		{
                  $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid Userid or User Not Active');  
        			echo json_encode($response);
        			exit();
        		}
        		
    		
    		 $sql = "SELECT movie.*, lang.`language_name`, lang.`language_background`, 
            IF(tbl_likes.`is_like` = 1, 1, 0) AS liked,
            IF(tbl_likes.`is_like` = 0, 1, 0) AS disliked
            FROM tbl_drama movie 
            LEFT JOIN tbl_language lang ON movie.`language_id` = lang.`id`   
            LEFT JOIN tbl_likes tbl_likes ON movie.`id` = tbl_likes.`post_id`  
                AND tbl_likes.`userid` = '$userid' 
                AND tbl_likes.`type` = 'drama'
            WHERE movie.`status` = '1' 
            ORDER BY movie.`id` DESC";

           $movies = getRecords($sql);
            if ($movies) {
        foreach ($movies as &$movie) {
            $movie['user_interaction'] = [
                'liked' => $movie['liked'] == 1,
                'disliked' => $movie['disliked'] == 1
            ];
            unset($movie['liked'], $movie['disliked']);// Convert array to comma-separated string
            
            
            
        }

        $response = [
            'status' => true,
            'error' => 0,
            'success' => 1,
            'msg' => 'Successfully Loaded',
            'data' => $movies
        ];
    } else {
        $response = [
            'status' => false,
            'error' => 1,
            'success' => 0,
            'msg' => 'No data Found'
        ];
    }
   
        echo json_encode($response);
        exit();   
		break;	
// ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        case "getSongs" :
         if(!app_login(intval($userid = @$_POST['userid'])))
        	{
              $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid Userid or User Not Active');  
        		echo json_encode($response);
        		exit();
        	}
        	
        	$sql = "SELECT movie.*, lang.`language_name`, lang.`language_background`, 
            IF(tbl_likes.`is_like` = 1, 1, 0) AS liked,
            IF(tbl_likes.`is_like` = 0, 1, 0) AS disliked
            FROM tbl_songs movie 
            LEFT JOIN tbl_language lang ON movie.`language_id` = lang.`id`   
            LEFT JOIN tbl_likes tbl_likes ON movie.`id` = tbl_likes.`post_id`  
                AND tbl_likes.`userid` = '$userid' 
                AND tbl_likes.`type` = 'song'
            WHERE movie.`status` = '1' 
            ORDER BY movie.`id` DESC";

           $movies = getRecords($sql);
            if ($movies) {
        foreach ($movies as &$movie) {
            $movie['user_interaction'] = [
                'liked' => $movie['liked'] == 1,
                'disliked' => $movie['disliked'] == 1
            ];
            unset($movie['liked'], $movie['disliked']);// Convert array to comma-separated string
            
            
            
        }

        $response = [
            'status' => true,
            'error' => 0,
            'success' => 1,
            'msg' => 'Successfully Loaded',
            'data' => $movies
        ];
    } else {
        $response = [
            'status' => false,
            'error' => 1,
            'success' => 0,
            'msg' => 'No data Found'
        ];
    }
        		
   
        echo json_encode($response);
        exit();   
		break;	
	// ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        case "getEvents" :
        if(!app_login(intval($userid = @$_POST['userid'])))
        	{
              $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid Userid or User Not Active');  
        		echo json_encode($response);
        		exit();
        	}
        	
        	$sql = "SELECT movie.*, lang.`language_name`, lang.`language_background`, 
            IF(tbl_likes.`is_like` = 1, 1, 0) AS liked,
            IF(tbl_likes.`is_like` = 0, 1, 0) AS disliked
            FROM tbl_events movie 
            LEFT JOIN tbl_language lang ON movie.`language_id` = lang.`id`   
            LEFT JOIN tbl_likes tbl_likes ON movie.`id` = tbl_likes.`post_id`  
                AND tbl_likes.`userid` = '$userid' 
                AND tbl_likes.`type` = 'event'
            WHERE movie.`status` = '1' 
            ORDER BY movie.`id` DESC";

           $movies = getRecords($sql);
            if ($movies) {
        foreach ($movies as &$movie) {
            $movie['user_interaction'] = [
                'liked' => $movie['liked'] == 1,
                'disliked' => $movie['disliked'] == 1
            ];
            unset($movie['liked'], $movie['disliked']);// Convert array to comma-separated string
            
            
            
        }

        $response = [
            'status' => true,
            'error' => 0,
            'success' => 1,
            'msg' => 'Successfully Loaded',
            'data' => $movies
        ];
    } else {
        $response = [
            'status' => false,
            'error' => 1,
            'success' => 0,
            'msg' => 'No data Found'
        ];
    }
        		
   
        echo json_encode($response);
        exit();   
		break;	
	// ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        case "getLiveTvCategories" :
         if(!app_login(intval($userid = @$_POST['userid'])))
        	{
              $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid Userid or User Not Active');  
        		echo json_encode($response);
        		exit();
        	}
        	
        	$sql=getRecords("SELECT * FROM `tbl_category` WHERE status=1 ORDER BY cid dESc");
        	if($sql){
        	     $response = ['status' => true, 'error' => 0,
                    'success' => 1,
                    'msg' => 'Successfully Loaded',
                    'data' => $sql
                ]; 
        	}else{
        	    $response = [
                    'status' => false,
                    'error' => 1,
                    'success' => 0,
                    'msg' => 'No data Found'
                ];
        	    
        	}
        	
   
        echo json_encode($response);
        exit();   
		break;	
	// ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        case "getLiveChannels" :
   
       if(!app_login(intval($userid = @$_POST['userid'])))
        	{
              $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid Userid or User Not Active');  
        		echo json_encode($response);
        		exit();
        	}
        	
        	$cat_id= @$_POST['cat_id'];
        	if(isset($cat_id)){
        	    $checkid=getRecord("SELECT * FROM tbl_category WHERE cid='$cat_id'  ");
        	    if(empty($checkid)){
        	        $response = [
                    'status' => false,
                    'error' => 1,
                    'success' => 0,
                    'msg' => 'Invalid cat_id'
                ];
                echo json_encode($response);
                 exit(); 
        	    }
        	    
        	    $getdata=getRecords("SELECT * FROM `tbl_channels` WHERE cat_id='$cat_id' AND status=1 ORDER By id desc ");
        	    if($getdata){
        	        $response = ['status' => true, 'error' => 0,
                    'success' => 1,
                    'msg' => 'Successfully Loaded',
                    'data' => $getdata
                ]; 
        	    }else{
        	        $response = [
                    'status' => false,
                    'error' => 1,
                    'success' => 0,
                    'msg' => 'No data Found'
                ];
        	    }
        	    
        	}else{
        	    $response = [
                    'status' => false,
                    'error' => 1,
                    'success' => 0,
                    'msg' => 'Requird missing param'
                ];
        	}
        	
        echo json_encode($response);
        exit();   
		break;
	// ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        case "totalViews" :
        if(!app_login(intval($userid = @$_POST['userid'])))
        	{
              $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid Userid or User Not Active');  
        		echo json_encode($response);
        		exit();
        	}
        	
        	$post_id= @$_POST['post_id'];
        	$type= @$_POST['type'];
        	if(isset($post_id) && isset($type)){
        	    if($type=='movies'){
        	        $checkid=getRecord("SELECT * FROM tbl_movies WHERE id = '$post_id'");
        	        if(empty($checkid)){
        	          $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid movie id');  
        		        echo json_encode($response);
        		        exit();  
        	        }
        	        $check=getRecord("SELECT * FROM `tbl_watch` WHERE userid='$userid' and type='movies' and post_id='$post_id' ");
        	        if(empty($check)){
        	            $insertq=query("INSERT INTO `tbl_watch`(`userid`, `type`, `post_id`, `dt`) VALUES ('$userid','movies','$post_id','$fdate')");
        	            if($insertq){
        	                $upquery=query("UPDATE tbl_movies SET total_views = total_views + 1 WHERE id='$post_id'");
        	                $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully');
        	                
        	            }
        	        }else{
        	            $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'U Already View');  
        	        }
        	        
        	    }else if($type=='rentmovie'){
        	        $checkid=getRecord("SELECT * FROM tbl_rentmovies WHERE id = '$post_id'");
        	        if(empty($checkid)){
        	          $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid movie id');  
        		        echo json_encode($response);
        		        exit();  
        	        }
        	        $check=getRecord("SELECT * FROM `tbl_watch` WHERE userid='$userid' and type='rentmovie' and post_id='$post_id' ");
        	        if(empty($check)){
        	            $insertq=query("INSERT INTO `tbl_watch`(`userid`, `type`, `post_id`, `dt`) VALUES ('$userid','rentmovie','$post_id','$fdate')");
        	            if($insertq){
        	                $upquery=query("UPDATE tbl_rentmovies SET total_views = total_views + 1 WHERE id='$post_id'");
        	                $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully');
        	            }
        	        }else{
        	            $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'U Already View');  
        	        }
        	        
        	    }
        	    
        	    else if($type=='series'){
        	         $checkid=getRecord("SELECT * FROM tbl_series WHERE id = '$post_id'");
        	        if(empty($checkid)){
        	          $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid series id');  
        		        echo json_encode($response);
        		        exit();  
        	        }
        	        $check=getRecord("SELECT * FROM `tbl_watch` WHERE userid='$userid' and type='series' and post_id='$post_id' ");
        	        if(empty($check)){
        	            $insertq=query("INSERT INTO `tbl_watch`(`userid`, `type`, `post_id`, `dt`) VALUES ('$userid','series','$post_id','$fdate')");
        	            if($insertq){
        	                $upquery=query("UPDATE tbl_series SET total_views = total_views + 1 WHERE id='$post_id'");
        	                $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully');  
        	            }
        	        }else{
        	            $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'U Already View');  
        	        }
        	        
        	    }else if($type=='shows'){
        	       $checkid=getRecord("SELECT * FROM tbl_shows WHERE id = '$post_id'");
        	        if(empty($checkid)){
        	          $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid shows id');  
        		        echo json_encode($response);
        		        exit();  
        	        }
        	       $check=getRecord("SELECT * FROM `tbl_watch` WHERE userid='$userid' and type='shows' and post_id='$post_id' ");
        	        if(empty($check)){
        	            $insertq=query("INSERT INTO `tbl_watch`(`userid`, `type`, `post_id`, `dt`) VALUES ('$userid','shows','$post_id','$fdate')");
        	            if($insertq){
        	                $upquery=query("UPDATE tbl_shows SET total_views = total_views + 1 WHERE id='$post_id'");
        	                $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully');
        	            }
        	        }else{
        	            $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'U Already View');  
        	        } 
        	        
        	    }else if($type=='shortfilm'){
        	        
        	        $checkid=getRecord("SELECT * FROM tbl_shortfilms WHERE id = '$post_id'");
        	        if(empty($checkid)){
        	          $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid shortfilm id');  
        		        echo json_encode($response);
        		        exit();  
        	        }
        	        
        	        $check=getRecord("SELECT * FROM `tbl_watch` WHERE userid='$userid' and type='shortfilm' and post_id='$post_id' ");
        	        if(empty($check)){
        	            $insertq=query("INSERT INTO `tbl_watch`(`userid`, `type`, `post_id`, `dt`) VALUES ('$userid','shortfilm','$post_id','$fdate')");
        	            if($insertq){
        	                $upquery=query("UPDATE tbl_shortfilms SET total_views = total_views + 1 WHERE id='$post_id'");
        	                $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully');
        	            }
        	        }else{
        	            $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'U Already View');  
        	        }
        	        
        	    }else if($type=='drama'){
        	       
        	       $checkid=getRecord("SELECT * FROM tbl_drama WHERE id = '$post_id'");
        	        if(empty($checkid)){
        	          $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid drama id');  
        		        echo json_encode($response);
        		        exit();  
        	        }
        	        
        	        $check=getRecord("SELECT * FROM `tbl_watch` WHERE userid='$userid' and type='drama' and post_id='$post_id' ");
        	        if(empty($check)){
        	            $insertq=query("INSERT INTO `tbl_watch`(`userid`, `type`, `post_id`, `dt`) VALUES ('$userid','drama','$post_id','$fdate')");
        	            if($insertq){
        	                $upquery=query("UPDATE tbl_drama SET total_views = total_views + 1 WHERE id='$post_id'");
        	                $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully');
        	            }
        	        }else{
        	            $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'U Already View');  
        	        }
        	        
        	    }else if($type=='song'){
        	        
        	        $checkid=getRecord("SELECT * FROM tbl_songs WHERE id = '$post_id'");
        	        if(empty($checkid)){
        	          $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid soong id');  
        		        echo json_encode($response);
        		        exit();  
        	        }
        	        
        	        $check=getRecord("SELECT * FROM `tbl_watch` WHERE userid='$userid' and type='song' and post_id='$post_id' ");
        	        if(empty($check)){
        	            $insertq=query("INSERT INTO `tbl_watch`(`userid`, `type`, `post_id`, `dt`) VALUES ('$userid','song','$post_id','$fdate')");
        	            if($insertq){
        	                $upquery=query("UPDATE tbl_songs SET total_views = total_views + 1 WHERE id='$post_id'");
        	                $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully');
        	            }
        	        }else{
        	            $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'U Already View');  
        	        }
        	        
        	        
        	    }else if($type=='event'){
        	        
        	         $checkid=getRecord("SELECT * FROM tbl_events WHERE id = '$post_id'");
        	        if(empty($checkid)){
        	          $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid event id');  
        		        echo json_encode($response);
        		        exit();  
        	        }
        	        
        	        
        	        $check=getRecord("SELECT * FROM `tbl_watch` WHERE userid='$userid' and type='event' and post_id='$post_id' ");
        	        if(empty($check)){
        	            $insertq=query("INSERT INTO `tbl_watch`(`userid`, `type`, `post_id`, `dt`) VALUES ('$userid','event','$post_id','$fdate')");
        	            if($insertq){
        	                $upquery=query("UPDATE tbl_events SET total_views = total_views + 1 WHERE id='$post_id'");
        	                $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully');
        	            }
        	        }else{
        	            $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'U Already View');  
        	        }
        	        
        	    }else if($type=='livetv'){
        	        
        	        $checkid=getRecord("SELECT * FROM tbl_channels WHERE id = '$post_id'");
        	        if(empty($checkid)){
        	          $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid channel id');  
        		        echo json_encode($response);
        		        exit();  
        	        }
        	        $check=getRecord("SELECT * FROM `tbl_watch` WHERE userid='$userid' and type='livetv' and post_id='$post_id' ");
        	        if(empty($check)){
        	            $insertq=query("INSERT INTO `tbl_watch`(`userid`, `type`, `post_id`, `dt`) VALUES ('$userid','livetv','$post_id','$fdate')");
        	            if($insertq){
        	                $upquery=query("UPDATE tbl_channels SET total_views = total_views + 1 WHERE id='$post_id'");
        	                $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully');
        	            }
        	        }else{
        	            $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'U Already View');  
        	        }
        	        
        	    }else{
        	         $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid Type'); 
        	    }
        	    
        	}else{
        	     $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Missing Required Params');  
        	}
        	
   
        echo json_encode($response);
        exit();   
		break;
    // ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        case "totalWatchTime" :
        if(!app_login(intval($userid = @$_POST['userid'])))
        	{
              $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid Userid or User Not Active');  
        		echo json_encode($response);
        		exit();
        	}
        	
        	$post_id= @$_POST['post_id'];
        	$type= @$_POST['type'];
        	$total_time=@$_POST['total_time'];
        	if(isset($post_id) && isset($type) && isset($total_time)){
        	    if($type=='movies'){
        	        $checkid=getRecord("SELECT * FROM tbl_movies WHERE id = '$post_id'");
        	        if(empty($checkid)){
        	          $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid movie id');  
        		        echo json_encode($response);
        		        exit();  
        	        }
        	       
        	       $upquery=query("UPDATE tbl_movies SET total_watch_time = total_watch_time + '$total_time' WHERE id='$post_id'");
        	       $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully');  
        	        
        	        
        	    }else if($type=='rentmovie'){
        	        $checkid=getRecord("SELECT * FROM tbl_rentmovies WHERE id = '$post_id'");
        	        if(empty($checkid)){
        	          $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid movie id');  
        		        echo json_encode($response);
        		        exit();  
        	        }
        	       
        	           $upquery=query("UPDATE tbl_rentmovies SET total_watch_time = total_watch_time + '$total_time' WHERE id='$post_id'");
        	         $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully');  
        	    }
        	    
        	    else if($type=='series'){
        	         $checkid=getRecord("SELECT * FROM tbl_series WHERE id = '$post_id'");
        	        if(empty($checkid)){
        	          $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid series id');  
        		        echo json_encode($response);
        		        exit();  
        	        }
        	       
        	       $upquery=query("UPDATE tbl_series SET total_watch_time = total_watch_time + '$total_time' WHERE id='$post_id'");
        	       $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully');  
        	            
        	        
        	    }else if($type=='shows'){
        	       $checkid=getRecord("SELECT * FROM tbl_shows WHERE id = '$post_id'");
        	        if(empty($checkid)){
        	          $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid shows id');  
        		        echo json_encode($response);
        		        exit();  
        	        }
        	      
        	                $upquery=query("UPDATE tbl_shows SET total_watch_time = total_watch_time + '$total_time' WHERE id='$post_id'");
        	                $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully');
        	          
        	        
        	    }else if($type=='shortfilm'){
        	        
        	        $checkid=getRecord("SELECT * FROM tbl_shortfilms WHERE id = '$post_id'");
        	        if(empty($checkid)){
        	          $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid shortfilm id');  
        		        echo json_encode($response);
        		        exit();  
        	        }
        	        
        	       
        	                $upquery=query("UPDATE tbl_shortfilms SET total_watch_time = total_watch_time + '$total_time' WHERE id='$post_id'");
        	                $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully');
        	            
        	        
        	    }else if($type=='drama'){
        	       
        	       $checkid=getRecord("SELECT * FROM tbl_drama WHERE id = '$post_id'");
        	        if(empty($checkid)){
        	          $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid drama id');  
        		        echo json_encode($response);
        		        exit();  
        	        }
        	        
        	        
        	                $upquery=query("UPDATE tbl_drama SET total_watch_time = total_watch_time + '$total_time' WHERE id='$post_id'");
        	                $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully');
        	            
        	        
        	    }else if($type=='song'){
        	        
        	        $checkid=getRecord("SELECT * FROM tbl_songs WHERE id = '$post_id'");
        	        if(empty($checkid)){
        	          $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid soong id');  
        		        echo json_encode($response);
        		        exit();  
        	        }
        	        
        	       
        	                $upquery=query("UPDATE tbl_songs SET total_watch_time = total_watch_time + '$total_time' WHERE id='$post_id'");
        	                $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully');
        	           
        	        
        	        
        	    }else if($type=='event'){
        	        
        	         $checkid=getRecord("SELECT * FROM tbl_events WHERE id = '$post_id'");
        	        if(empty($checkid)){
        	          $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid event id');  
        		        echo json_encode($response);
        		        exit();  
        	        }
        	        
        	        
        	       
        	                $upquery=query("UPDATE tbl_events SET total_watch_time = total_watch_time + '$total_time' WHERE id='$post_id'");
        	                $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully');
        	           
        	        
        	    }else if($type=='livetv'){
        	        
        	        $checkid=getRecord("SELECT * FROM tbl_channels WHERE id = '$post_id'");
        	        if(empty($checkid)){
        	          $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid channel id');  
        		        echo json_encode($response);
        		        exit();  
        	        }
        	        
        	                $upquery=query("UPDATE tbl_channels SET total_watch_time = total_watch_time + '$total_time'  WHERE id='$post_id'");
        	                $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully');
        	           
        	        
        	    }else{
        	         $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid Type'); 
        	    }
        	}else{
        	     $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Missing Required Params');  
        	}
        echo json_encode($response);
        exit();   
		break;	
	// ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        case "addToDownloads" :
          if(!app_login(intval($userid = @$_POST['userid'])))
        	{
              $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Invalid Userid or User Not Active');  
        		echo json_encode($response);
        		exit();
        	}
        	$post_id= @$_POST['post_id'];
        	$type= @$_POST['type'];
        	if(isset($post_id) && isset($type)){
        	    
        	    $checkdata=getRecord("SELECT * FROM tbl_download WHERE post_id='$post_id' AND userid='$userid' and type='$type' and deleted=0");
        	    if($checkdata){
        	         $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'already download');
        	    }else{
        	         $insertqu=query("INSERT INTO `tbl_download`(`userid`, `post_id`, `type`, `dt`) VALUES ('$userid','$post_id','$type','$fdate')");
        	          $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' =>'Successfully');
        	    }
        	    
        	   
        	    
        		    
        	}else{
        	     $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' =>'Missing Required Params');  
        	}
   
        echo json_encode($response);
        exit();   
		break;	
	// ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    case "getDownloaddata":
    $userid = intval(@$_POST['userid']);
   
    if (!app_login($userid)) {
        echo json_encode([
            'status' => false,
            'error' => 1,
            'success' => 0,
            'msg' => 'Invalid Userid or User Not Active'
        ]);
        exit();
    }

    $getdata = getRecords("SELECT * FROM tbl_download WHERE userid='$userid' AND deleted=0");

    if (sizeof($getdata) > 0) {
        $downloadData = [];

        foreach ($getdata as $row) {
            $type = $row['type'];
            $postId = $row['post_id'];
            $content = null;

            switch ($type) {
                case 'movies':
                    $content = getRecord("SELECT m.*,lang.`language_name`,
                     IF(lk.`is_like` = 1, 1, 0) AS liked,
            IF(lk.`is_like` = 0, 1, 0) AS disliked FROM tbl_movies m  
                    LEFT JOIN tbl_language lang on m.language_id=lang.id
                     LEFT JOIN tbl_likes  lk on m.id=lk.post_id
                      AND lk.userid = '$userid' 
                            AND lk.type = 'movies'
                    WHERE m.id='$postId'");
                    if($content){
                         $content['user_interaction'] = [
                'liked' => $content['liked'] == 1,
                'disliked' => $content['disliked'] == 1
            ];
            unset($content['liked'], $content['disliked']);
                    }
                    break;
               case 'series':
                $content = getRecord("SELECT m.*,lang.`language_name`,  IF(lk.`is_like` = 1, 1, 0) AS liked,
            IF(lk.`is_like` = 0, 1, 0) AS disliked  FROM tbl_series m LEFT JOIN tbl_language lang on m.language_id=lang.id 
                LEFT JOIN tbl_likes  lk on m.id=lk.post_id AND lk.userid = '$userid'  AND lk.type = 'series'
                WHERE m.id='$postId'");
                if ($content) {
                    // Fetch seasons for series
                    $seasons = getRecords("SELECT * FROM tbl_season WHERE series_id='$postId' AND status=1");
                    $content['seasons'] = $seasons; // append to content
                    
                    
                     $content['user_interaction'] = [
                'liked' => $content['liked'] == 1,
                'disliked' => $content['disliked'] == 1
            ];
             unset($content['liked'], $content['disliked']);
                }
                break;
            
            case 'shows':
                $content = getRecord("SELECT m.*,lang.`language_name`, IF(lk.`is_like` = 1, 1, 0) AS liked,
            IF(lk.`is_like` = 0, 1, 0) AS disliked FROM tbl_shows m  LEFT JOIN tbl_language lang on m.language_id=lang.id 
             LEFT JOIN tbl_likes  lk on m.id=lk.post_id AND lk.userid = '$userid'  AND lk.type = 'shows'
            
            WHERE m.id='$postId'");
                if ($content) {
                    // Fetch seasons for shows
                    $seasons = getRecords("SELECT * FROM tbl_tv_season WHERE shows_id='$postId'  AND status=1");
                    $content['seasons'] = $seasons;
                    
                     $content['user_interaction'] = [
                'liked' => $content['liked'] == 1,
                'disliked' => $content['disliked'] == 1
            ];
             unset($content['liked'], $content['disliked']);
                }
                break;
                 case 'drama':
                    $content = getRecord("SELECT m.*,lang.`language_name`,
                     IF(lk.`is_like` = 1, 1, 0) AS liked,
            IF(lk.`is_like` = 0, 1, 0) AS disliked FROM tbl_drama m  
                    LEFT JOIN tbl_language lang on m.language_id=lang.id
                     LEFT JOIN tbl_likes  lk on m.id=lk.post_id
                      AND lk.userid = '$userid' 
                            AND lk.type = 'drama'
                    WHERE m.id='$postId'");
                    if($content){
                         $content['user_interaction'] = [
                'liked' => $content['liked'] == 1,
                'disliked' => $content['disliked'] == 1
            ];
            unset($content['liked'], $content['disliked']);
                    }
                    break;
                 case 'shortfilm':
                    
                    
                     $content = getRecord("SELECT m.*,lang.`language_name`,
                     IF(lk.`is_like` = 1, 1, 0) AS liked,
            IF(lk.`is_like` = 0, 1, 0) AS disliked FROM tbl_shortfilms m  
                    LEFT JOIN tbl_language lang on m.language_id=lang.id
                     LEFT JOIN tbl_likes  lk on m.id=lk.post_id
                      AND lk.userid = '$userid' 
                            AND lk.type = 'shortfilm'
                    WHERE m.id='$postId'");
                    if($content){
                         $content['user_interaction'] = [
                'liked' => $content['liked'] == 1,
                'disliked' => $content['disliked'] == 1
            ];
            unset($content['liked'], $content['disliked']);
                    }
                    break;
                case 'songs':
                    
                    
                    
                    $content = getRecord("SELECT m.*,lang.`language_name`,
                     IF(lk.`is_like` = 1, 1, 0) AS liked,
            IF(lk.`is_like` = 0, 1, 0) AS disliked FROM tbl_songs m  
                    LEFT JOIN tbl_language lang on m.language_id=lang.id
                     LEFT JOIN tbl_likes  lk on m.id=lk.post_id
                      AND lk.userid = '$userid' 
                            AND lk.type = 'song'
                    WHERE m.id='$postId'");
                    if($content){
                         $content['user_interaction'] = [
                'liked' => $content['liked'] == 1,
                'disliked' => $content['disliked'] == 1
            ];
            unset($content['liked'], $content['disliked']);
                    }
                    break;
            }

            if ($content) {
    $item = [
        'type'              => $type,
        'id'                => $content['id'] ?? null,
        'title'             => $content['movie_title'] ?? $content['series_name'] ?? $content['shows_name'] ?? $content['drama_title'] ?? $content['song_title'] ?? '',
        'cover'             => $content['movie_cover'] ?? $content['series_cover'] ?? $content['shows_cover'] ?? $content['drama_cover'] ?? $content['song_cover'] ?? '',
        'poster'            => $content['movie_poster'] ?? $content['series_poster'] ?? $content['shows_poster'] ?? $content['drama_poster'] ?? $content['song_poster'] ?? '',
        'cost_type'         =>$content['movie_cost_type'] ?? $content['series_cost_type']  ?? '',
        'url_type'    => $content['movie_type'] ?? $content['song_type'] ?? $content['drama_type']  ?? '',
        'url'               => $content['movie_url'] ??  $content['drama_url'] ?? $content['songs_url'] ?? '',
        'desc'              => $content['movie_desc'] ?? $content['series_desc'] ?? $content['shos_desc'] ?? $content['drama_desc'] ?? $content['song_desc'] ?? '',
        'release_date'      => $content['release_date'] ?? '',
        'total_time'        => $content['total_time'] ?? '',
        'language_name'   => $content['language_name'] ?? '',
        'maturity_rating'   => $content['maturity_rating'] ?? '',
        'trailer_type'   => $content['trailer_type'] ?? '',
        'trailer_url'   => $content['trailer_url'] ?? '',
        'director_name'     => $content['director_name'] ?? '',
        'cast_names'        => $content['cast_names'] ?? '',
        
        'imdb_rating'       => $content['imdb_rating'] ?? '',
        'total_watch_time'  => $content['total_watch_time'] ?? '',
        'total_views'       => $content['total_views'] ?? '0',
        'rate_avg'          => $content['rate_avg'] ?? '0',
        'is_slider'         => $content['is_slider'] ?? '0',
        'status'            => $content['status'] ?? '0',
'user_interaction' => $content['user_interaction'] ?? ['liked' => false, 'disliked' => false],    ];

    // Only add 'seasons' if the type is 'series' or 'shows'
    if (in_array($type, ['series', 'shows'])) {
        $item['seasons'] = $content['seasons'] ?? [];
    }

    $downloadData[] = $item;
}

        }

        echo json_encode([
            'status' => true,
            'error' => 0,
            'success' => 1,
            'msg' => 'Loaded Successfully',
            'data' => $downloadData
        ]);
    } else {
        echo json_encode([
            'status' => false,
            'error' => 1,
            'success' => 0,
            'msg' => 'No data found'
        ]);
    }
    exit();
    break;
    
    
     case "removeFromDownload":
         $userid = intval(@$_POST['userid']);
    $post_id = intval(@$_POST['post_id']);
    $type = trim(@$_POST['type']);

    if (!$userid || !$post_id || !$type) {
        echo json_encode([
            'status' => false,
            'success' => 0,
            'msg' => 'Missing parameters'
        ]);
        exit();
    }

    if (!app_login($userid)) {
        echo json_encode([
            'status' => false,
            'success' => 0,
            'msg' => 'Invalid or inactive user'
        ]);
        exit();
    }
   $getdata=getRecord("SELECT * FROM tbl_download WHERE userid = '$userid' AND post_id = '$post_id' AND type = '$type' ");
   
   if(empty($getdata))
   {
      echo json_encode([
            'status' => false,
            'success' => 0,
            'msg' => 'Invalid post_id'
        ]);
        exit(); 
   }
    // Attempt to delete the entry
    $delete = query("DELETE FROM tbl_download WHERE userid = '$userid' AND post_id = '$post_id' AND type = '$type'");

    if ($delete) {
        echo json_encode([
            'status' => true,
            'success' => 1,
            'msg' => 'Removed from Downlaod successfully'
        ]);
    } else {
        echo json_encode([
            'status' => false,
            'success' => 0,
            'msg' => 'Failed to remove from downlaod or item not found'
        ]);
    }

      exit();
    break;
	// ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
       case "getHomeDataold":
        $userid = intval(@$_POST['userid']);
        $token = @$_POST['token'];
        if (!app_login($userid)) {
            echo json_encode(['status' => false,'error' => 1,'success' => 0,'msg' => 'Invalid Userid or User Not Active']);
            exit();
        }
        if(empty($token)){
           echo json_encode(['status' => false,'error' => 1,'success' => 0,'msg' => 'missing required params'    ]);
            exit(); 
        }
        $getdatatoken = getRecord("SELECT * FROM tbl_device_info WHERE token='$token'");
        if(sizeof($getdatatoken)>0){
            $gethomeCategories = getRecords("SELECT * FROM tbl_home ORDER BY  sequence ASC");
            $homeData = [];
            if (sizeof($gethomeCategories) > 0) {
                foreach ($gethomeCategories as $cat) {
                    $sectionId = $cat['id'];
                    $sectionseq = $cat['sequence'];
                    $sectionTitle = $cat['home_title'];
                    $sectionType = $cat['type']; 
                    $items = [];
                    if ($sectionType == 'movies') {
                        $query = "SELECT movie.*, lang.language_name, lang.language_background,IF(tbl_likes.is_like = 1, 1, 0) AS liked,IF(tbl_likes.is_like = 0, 1, 0) AS disliked FROM tbl_movies movie LEFT JOIN tbl_language lang ON movie.language_id = lang.id  LEFT JOIN tbl_likes ON movie.id = tbl_likes.post_id AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'movies' WHERE movie.status = 1 AND movie.home_cat_id = '$sectionId' ORDER BY movie.id DESC";
                        $movieItems = getRecords($query);
                        foreach ($movieItems as $item) {
                            $items[] = [
                                'id' => $item['id'],
                                'title' => $item['movie_title'],
                                'language_name' => $item['language_name'],
                                'imdb_rating'=> $item['imdb_rating'],
                                'genre_name' => '', // optional, or fetch genre if needed
                                'cover_image' => $item['movie_cover'],
                                'poster_image' => $item['movie_poster'],
                                'description' => $item['movie_desc'],
                                'total_time' => $item['total_time'],
                                'director_name' => $item['director_name'],
                                'cast_names' => $item['cast_names'],
                                'cost_type' => $item['movie_cost_type'],
                                'price' => '0',
                                'maturity_rating' => $item['maturity_rating'],
                                'release_date' => $item['release_date'],
                                'url_type'=>$item['movie_type'],
                                'url' => $item['movie_url'],
                                'trailer_type' => $item['trailer_type'],
                                 'trailer_url' => $item['trailer_url'],
                                'type' => 'movies',
                                'user_interaction' => [
                                    'liked' => $item['liked'] == 1,
                                    'disliked' => $item['disliked'] == 1
                                ]
                            ];
                            unset($item['liked'], $item['disliked']);
                        }
                    } elseif ($sectionType == 'rented') {
                        $query = "SELECT movie.*, lang.language_name, lang.language_background,IF(tbl_likes.is_like = 1, 1, 0) AS liked, IF(tbl_likes.is_like = 0, 1, 0) AS disliked FROM tbl_rentmovies movie
                        LEFT JOIN tbl_language lang ON movie.language_id = lang.id LEFT JOIN tbl_likes ON movie.id = tbl_likes.post_id AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'rentedmovies' WHERE 
                        movie.status = 1 AND movie.home_cat_id = '$sectionId' ORDER BY movie.id DESC";
                        $movieItems = getRecords($query);
                        foreach ($movieItems as $item) {
                            $buy = getRecord("SELECT purchase_dt, expire FROM tbl_buymovies WHERE movie_id = '{$item['id']}' AND userid = '$userid' ORDER BY id DESC LIMIT 1");
                            $userHaveRentAccess = false;
                            $rentValidityDate = null;
                            if($buy && $buy['expire'] == 0) {
                                $validTill = date('Y-m-d', strtotime($buy['purchase_dt'] . ' + ' . intval($item['movie_validation']) . ' days'));
                                 if (strtotime($validTill) >= time()) {
                                    $userHaveRentAccess = true;
                                    $rentValidityDate = $validTill;
                                } else {
                                    $userHaveRentAccess = false;
                                    $rentValidityDate = null;
                                }
                            }
                            $items[] = [
                                'id' => $item['id'],
                                'title' => $item['movie_title'],
                                'language_name' => $item['language_name'],
                                'imdb_rating'=> $item['imdb_rating'],
                                'genre_name' => '', // optional, or fetch genre if needed
                                'cover_image' => $item['movie_cover'],
                                'poster_image' => $item['movie_poster'],
                                'description' => $item['movie_desc'],
                                'total_time' => $item['total_time'],
                                'director_name' => $item['director_name'],
                                'cast_names' => $item['cast_names'],
                               'cost_type' => "rent",
                                'price' => $item['movie_price'],
                                'is_rented'=>true,
                                'user_have_rent_access'=>$userHaveRentAccess,
                                'rent_validity_date'=>$rentValidityDate,
                                'maturity_rating' => $item['maturity_rating'],
                                'release_date' => $item['release_date'],
                                'url_type'=>$item['movie_type'],
                                'url' => $item['movie_url'],
                                'trailer_type' => $item['trailer_type'],
                                 'trailer_url' => $item['trailer_url'],
                                'type' => 'movies',
                                'user_interaction' => [
                                    'liked' => $item['liked'] == 1,
                                    'disliked' => $item['disliked'] == 1
                                ]
                            ];
                            
                            unset($item['liked'], $item['disliked']);
                        }
                    } elseif ($sectionType == 'series') {
                        $query = "SELECT s.*, lang.language_name, lang.language_background, GROUP_CONCAT(DISTINCT genre.genre_name ORDER BY genre.genre_name ASC) AS genre_name,IF(tbl_likes.is_like = 1, 1, 0) AS liked,
                        IF(tbl_likes.is_like = 0, 1, 0) AS disliked FROM tbl_series s LEFT JOIN tbl_language lang ON s.language_id = lang.id LEFT JOIN tbl_genres genre ON FIND_IN_SET(genre.gid, s.genre_id) LEFT JOIN tbl_likes ON s.id = tbl_likes.post_id AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'series'  WHERE s.status = 1 AND s.home_cat_id = '$sectionId' GROUP BY s.id ORDER BY s.id DESC";
                        $seriesItems = getRecords($query);
                        foreach ($seriesItems as $item) {
                            // Fetch seasons for the current series
                            $seriesId = $item['id'];
                            $seasonQuery = "SELECT `id`, `series_id`, `season_name`, `status` FROM `tbl_season` WHERE `series_id` = '$seriesId'";
                            $seasons = getRecords($seasonQuery); // This should return an array of seasons
                            $items[] = [
                                'id' => $item['id'],
                                'title' => $item['series_name'],
                                'language_name' => $item['language_name'],
                                'genre_name' => $item['genre_name'],
                                'cover_image' => $item['series_cover'],
                                'imdb_rating'=> $item['imdb_rating'],
                                'poster_image' => $item['series_poster'],
                                'description' => $item['series_desc'],
                                'total_time' => $item['total_time'],
                                'director_name' => $item['director_name'],
                                'cast_names' => $item['cast_names'],
                                'cost_type' => $item['series_cost_type'],
                                'price' => '0',
                                'maturity_rating' => $item['maturity_rating'],
                                'release_date' => $item['release_date'],
                                'trailer_url' => $item['trailer_url'],
                                'trailer_type' => $item['trailer_type'],
                                'url' => $item['series_url'],
                                'type' => 'series',
                                'user_interaction' => [
                                    'liked' => $item['liked'] == 1,
                                    'disliked' => $item['disliked'] == 1
                                ],
                                'seasons' => $seasons // 👈 Add seasons here
                            ];
                            unset($item['liked'], $item['disliked']);
                        }
                    }elseif ($sectionType == 'shows') {
                        $query = "SELECT s.*, lang.language_name, lang.language_background, GROUP_CONCAT(DISTINCT genre.genre_name ORDER BY genre.genre_name ASC) AS genre_name,IF(tbl_likes.is_like = 1, 1, 0) AS liked,
                        IF(tbl_likes.is_like = 0, 1, 0) AS disliked FROM tbl_shows s LEFT JOIN tbl_language lang ON s.language_id = lang.id  LEFT JOIN tbl_genres genre ON FIND_IN_SET(genre.gid, s.genre_id)  LEFT JOIN  
                        tbl_likes ON s.id = tbl_likes.post_id AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'shows' WHERE s.status = 1 AND s.home_cat_id = '$sectionId'  GROUP BY s.id  ORDER BY s.id DESC";    
                        $showsItems = getRecords($query);
                        foreach ($showsItems as $item) {
                            // Fetch seasons for the current series
                            $showsId = $item['id'];
                            $seasonQuery = "SELECT * FROM `tbl_tv_season` WHERE `shows_id` = '$showsId'";
                            $seasons = getRecords($seasonQuery); // This should return an array of seasons
                            $items[] = [
                                'id' => $item['id'],
                                'title' => $item['shows_name'],
                                'language_name' => $item['language_name'],
                                'genre_name' => $item['genre_name'],
                                'cover_image' => $item['shows_cover'],
                                'imdb_rating'=> $item['imdb_rating'],
                                'poster_image' => $item['shows_poster'],
                                'description' => $item['shows_desc'],
                                'total_time' => $item['total_time'],
                                'director_name' => $item['director_name'],
                                'cast_names' => $item['cast_names'],
                                'cost_type' => 'free',
                                'price' => '0',
                                'maturity_rating' => $item['maturity_rating'],
                                'release_date' => $item['release_date'],
                                'trailer_url' => $item['trailer_url'],
                                'trailer_type' => $item['trailer_type'],
                                'url' => $item['shows_url'],
                                'type' => 'shows',
                                'user_interaction' => [
                                    'liked' => $item['liked'] == 1,
                                    'disliked' => $item['disliked'] == 1
                                ],
                                'seasons' => $seasons // 👈 Add seasons here
                            ];
                            unset($item['liked'], $item['disliked']);
                        }
                    }elseif ($sectionType == 'shortfilms') {
                        $query = "SELECT sf.*, lang.language_name, lang.language_background, GROUP_CONCAT(DISTINCT genre.genre_name ORDER BY genre.genre_name ASC) AS genre_name,IF(tbl_likes.is_like = 1, 1, 0) AS liked,
                        IF(tbl_likes.is_like = 0, 1, 0) AS disliked FROM tbl_shortfilms sf LEFT JOIN tbl_language lang ON sf.language_id = lang.id  LEFT JOIN tbl_genres genre ON FIND_IN_SET(genre.gid, sf.genre_id) LEFT 
                        JOIN tbl_likes ON sf.id = tbl_likes.post_id AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'shortfilm' WHERE sf.status = 1 AND sf.home_cat_id = '$sectionId' GROUP BY sf.id ORDER BY sf.id DESC";
                        $shortfilmItems = getRecords($query);
                        foreach ($shortfilmItems as $item) {
                            $items[] = [
                                'id' => $item['id'],
                                'title' => $item['movie_title'],
                                'language_name' => $item['language_name'],
                                'genre_name' => $item['genre_name'],
                                'cover_image' => $item['movie_cover'],
                                'imdb_rating'=> $item['imdb_rating'],
                                'poster_image' => $item['movie_poster'],
                                'description' => $item['movie_desc'],
                                'total_time' => $item['total_time'],
                                'director_name' => $item['director_name'],
                                'cast_names' => $item['cast_names'],
                                'cost_type' => 'free',
                                'price' => '0',
                                'maturity_rating' => $item['maturity_rating'],
                                'release_date' => $item['release_date'],
                                'url_type'=>$item['movie_type'],
                                'url' => $item['movie_url'],
                                'trailer_type' => $item['trailer_type'],
                                'trailer_url' => $item['trailer_url'],
                                'type' => 'shortfilm',
                                'user_interaction' => [
                                    'liked' => $item['liked'] == 1,
                                    'disliked' => $item['disliked'] == 1
                                            ]
                            ];
                            unset($item['liked'], $item['disliked']);
                        }
                    }elseif ($sectionType == 'songs') {
                       $query = "SELECT sf.*, lang.language_name, lang.language_background, IF(tbl_likes.is_like = 1, 1, 0) AS liked,IF(tbl_likes.is_like = 0, 1, 0) AS disliked FROM tbl_songs sf LEFT JOIN tbl_language lang ON sf.language_id = lang.id LEFT JOIN tbl_likes ON sf.id = tbl_likes.post_id AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'song' WHERE sf.status = 1 AND sf.home_cat_id = '$sectionId' GROUP BY sf.id ORDER BY sf.id DESC";
                       $shortfilmItems = getRecords($query);
                       foreach ($shortfilmItems as $item) {
                          $items[] = [
                            'id' => $item['id'],
                            'title' => $item['song_title'],
                            'language_name' => $item['language_name'],
                            'cover_image' => $item['song_cover'],
                            'imdb_rating'=> $item['imdb_rating'],
                            'poster_image' => $item['song_poster'],
                            'description' => $item['song_desc'],
                            'total_time' => $item['total_time'],
                            'director_name' => $item['director_name'],
                            'cast_names' => $item['cast_names'],
                            'cost_type' => 'free',
                            'price' => '0',
                            'maturity_rating' => $item['maturity_rating'],
                            'release_date' => $item['release_date'],
                            'url_type'=>$item['song_type'],
                            'url' => $item['song_url'],
                            'trailer_type' => $item['trailer_type'],
                            'trailer_url' => $item['trailer_url'],
                            'type' => 'songs',
                            'user_interaction' => [
                                'liked' => $item['liked'] == 1,
                                'disliked' => $item['disliked'] == 1
                             ]
                          ];
                            unset($item['liked'], $item['disliked']);
                        }
                    }elseif ($sectionType == 'drama') {
                        $query = "SELECT sf.*, lang.language_name, lang.language_background, IF(tbl_likes.is_like = 1, 1, 0) AS liked,IF(tbl_likes.is_like = 0, 1, 0) AS disliked FROM tbl_drama sf LEFT JOIN tbl_language lang ON sf.language_id = lang.id LEFT JOIN tbl_likes ON sf.id = tbl_likes.post_id  AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'drama' WHERE sf.status = 1 AND sf.home_cat_id = '$sectionId' GROUP BY sf.id ORDER BY sf.id DESC";
                        $shortfilmItems = getRecords($query);
                        foreach ($shortfilmItems as $item) {
                            $items[] = [
                                'id' => $item['id'],
                                'title' => $item['drama_title'],
                                'language_name' => $item['language_name'],
                                'cover_image' => $item['drama_cover'],
                                'imdb_rating'=> $item['imdb_rating'],
                                'poster_image' => $item['drama_poster'],
                                'description' => $item['drama_desc'],
                                'total_time' => $item['total_time'],
                                'director_name' => $item['director_name'],
                                'cast_names' => $item['cast_names'],
                                'cost_type' => 'free',
                                'price' => '0',
                                'maturity_rating' => $item['maturity_rating'],
                                'release_date' => $item['release_date'],
                                'url_type'=>$item['drama_type'],
                                'url' => $item['drama_url'],
                                'trailer_type' => $item['trailer_type'],
                                'trailer_url' => $item['trailer_url'],
                                'type' => 'drama',
                                'user_interaction' => [
                                    'liked' => $item['liked'] == 1,
                                    'disliked' => $item['disliked'] == 1
                                ]
                            ];
                            unset($item['liked'], $item['disliked']);
                        }
                    }
                     $homeData[] = ['home_cat_id' => $sectionId,'sequence' => $sectionseq,'title' => $sectionTitle,'type' => $sectionType,'items' => $items];
                }
                $continueWatching = [];
                $watchlistQuery = "SELECT post_id, type FROM tbl_continue_watchlist WHERE userid = '$userid' ORDER by id DESC";
                $watchlistItems = getRecords($watchlistQuery);
                foreach ($watchlistItems as $item) {
                    $id = $item['post_id'];
                    $type = $item['type'];
                    switch ($type) {
                    case 'movies':
                        $query = "SELECT m.*, lang.language_name,IF(l.is_like = 1, 1, 0) AS liked,IF(l.is_like = 0, 1, 0) AS disliked  FROM tbl_movies m LEFT JOIN tbl_language lang ON m.language_id = lang.id
                          LEFT JOIN tbl_likes l ON m.id = l.post_id AND l.userid = '$userid' AND l.type = 'movies' WHERE m.id = '$id' AND m.status = 1";             
                        $record = getRecord($query);                
                        if ($record) {
                            $continueWatching[] = [
                                'id' => $record['id'],
                                'title' => $record['movie_title'],
                                'language_name' => $record['language_name'],
                                'imdb_rating' => $record['imdb_rating'],
                                'genre_name' => '',
                                'cover_image' => $record['movie_cover'],
                                'poster_image' => $record['movie_poster'],
                                'description' => $record['movie_desc'],
                                'total_time' => $record['total_time'],
                                'director_name' => $record['director_name'],
                                'cast_names' => $record['cast_names'],
                                'cost_type' => $record['movie_cost_type'],
                                'price' => '0',
                                'maturity_rating' => $record['maturity_rating'],
                                'release_date' => $record['release_date'],
                                'url_type' => $record['movie_type'],
                                'url' => $record['movie_url'],
                                'trailer_type' => $record['trailer_type'],
                                'trailer_url' => $record['trailer_url'],
                                'type' => 'movies',
                                'user_interaction' => [
                                    'liked' => $record['liked'] == 1,
                                    'disliked' => $record['disliked'] == 1
                                ]
                            ];
                        }         
                    break;              
                    }
                }
                if (!empty($continueWatching)) {
                    $homeData[] = ['home_cat_id' => 0,'sequence' => 0,'title' => 'Continue Watching','type' => 'continuewatching','items' => $continueWatching];
                }
                echo json_encode([ 'status' => true,'error' => 0,'success' => 1,'msg' => 'Home Data Loaded','data' => $homeData]);
            } else {
                echo json_encode(['status' => false,'error' => 1,'success' => 0,'msg' => 'No data found']);
            }
            
            
            
            
        
        
        
        
        
    
    
        }else{
         http_response_code(403);
         echo json_encode(['status' => true,'statusCode' => 403,'error' => 0,'success' => 1,'msg' => 'Logged into another device']); 
        }
    exit();
    break;
    
    
    case "getHomeData":
    $userid = intval(@$_POST['userid']);
    $token = @$_POST['token'];

    if (!app_login($userid)) {
        echo json_encode(['status' => false, 'error' => 1, 'success' => 0, 'msg' => 'Invalid Userid or User Not Active']);
        exit();
    }

    if (empty($token)) {
        echo json_encode(['status' => false, 'error' => 1, 'success' => 0, 'msg' => 'Missing required params']);
        exit();
    }

    $getdatatoken = getRecord("SELECT * FROM tbl_device_info WHERE token='$token'");

    if (sizeof($getdatatoken) > 0) {
        $homeData = [];

        // STEP 1: Load Continue Watching Section
        $continueWatching = [];
        $watchlistQuery = "SELECT post_id, type FROM tbl_continue_watchlist WHERE userid = '$userid' ORDER BY id DESC";
        $watchlistItems = getRecords($watchlistQuery);

        foreach ($watchlistItems as $item) {
            $id = $item['post_id'];
            $type = $item['type'];

            if ($type == 'movies') {
                $query = "SELECT m.*, wh.seconds, lang.language_name, 
                          IF(l.is_like = 1, 1, 0) AS liked, IF(l.is_like = 0, 1, 0) AS disliked  
                          FROM tbl_movies m 
                          LEFT JOIN tbl_language lang ON m.language_id = lang.id  
                          LEFT JOIN tbl_genres g on m.genre_id=g.gid 
                          LEFT JOIN tbl_continue_watchlist wh ON wh.post_id = m.id AND wh.userid = '$userid'
                          LEFT JOIN tbl_likes l ON m.id = l.post_id AND l.userid = '$userid' AND l.type = 'movies' 
                          WHERE m.id = '$id' AND m.status = 1";

                $records = getRecords($query);
                foreach ($records as $record) {
                     $genreIds = explode(',', $record['genre_id']); // Convert genre_id string to array
                    $genreNames = [];
                
                    if (!empty($genreIds)) {
                        $genreIdStr = implode(',', array_map('intval', $genreIds)); // Ensure integers for safety
                        $genreQuery = "SELECT `genre_name` FROM `tbl_genres` WHERE `gid` IN ($genreIdStr)";
                        $genres = getRecords($genreQuery);
                
                        foreach ($genres as $genre) {
                            $genreNames[] = $genre['genre_name'];
                        }
                    }
                    $continueWatching[] = [
                        'id' => $record['id'],
                        'title' => $record['movie_title'],
                        'language_name' => $record['language_name'],
                        'imdb_rating' => $record['imdb_rating'],
                        'genre_name' => implode(', ', $genreNames),
                        'seconds' => $record['seconds'],
                        'cover_image' => $record['movie_cover'],
                        'poster_image' => $record['movie_poster'],
                        'description' => $record['movie_desc'],
                        'total_time' => $record['total_time'],
                        'director_name' => $record['director_name'],
                        'cast_names' => $record['cast_names'],
                        'cost_type' => $record['movie_cost_type'],
                        'price' => '0',
                        'maturity_rating' => $record['maturity_rating'],
                        'release_date' => $record['release_date'],
                        'url_type' => $record['movie_type'],
                        'url' => $record['movie_url'],
                        'trailer_type' => $record['trailer_type'],
                        'trailer_url' => $record['trailer_url'],
                        'type' => 'movies',
                        'user_interaction' => [
                            'liked' => $record['liked'] == 1,
                            'disliked' => $record['disliked'] == 1
                        ]
                    ];
                }

            } elseif ($type == 'rented') {
                $query = "SELECT movie.*, lang.language_name, 
                          IF(tbl_likes.is_like = 1, 1, 0) AS liked, 
                          IF(tbl_likes.is_like = 0, 1, 0) AS disliked 
                          FROM tbl_rentmovies movie
                          LEFT JOIN tbl_language lang ON movie.language_id = lang.id 
                           LEFT JOIN tbl_genres g on movie.genre_id=g.gid 
                           LEFT JOIN tbl_continue_watchlist wh ON wh.post_id = m.id AND wh.userid = '$userid'
                          LEFT JOIN tbl_likes ON movie.id = tbl_likes.post_id AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'rentedmovies' 
                          WHERE movie.status = 1 AND movie.id = '$id'";

                $movieItems = getRecords($query);
                foreach ($movieItems as $record) {
                    $genreIds = explode(',', $record['genre_id']); // Convert genre_id string to array
                    $genreNames = [];
                
                    if (!empty($genreIds)) {
                        $genreIdStr = implode(',', array_map('intval', $genreIds)); // Ensure integers for safety
                        $genreQuery = "SELECT `genre_name` FROM `tbl_genres` WHERE `gid` IN ($genreIdStr)";
                        $genres = getRecords($genreQuery);
                
                        foreach ($genres as $genre) {
                            $genreNames[] = $genre['genre_name'];
                        }
                    }
                    
                    
                    
                    $buy = getRecord("SELECT purchase_dt, expire FROM tbl_buymovies WHERE movie_id = '{$record['id']}' AND userid = '$userid' ORDER BY id DESC LIMIT 1");
                    $userHaveRentAccess = false;
                    $rentValidityDate = null;

                    if ($buy && $buy['expire'] == 0) {
                        $validTill = date('Y-m-d', strtotime($buy['purchase_dt'] . ' + ' . intval($record['movie_validation']) . ' days'));
                        if (strtotime($validTill) >= time()) {
                            $userHaveRentAccess = true;
                            $rentValidityDate = $validTill;
                        }
                    }

                    $continueWatching[] = [
                        'id' => $record['id'],
                        'title' => $record['movie_title'],
                        'language_name' => $record['language_name'],
                        'imdb_rating' => $record['imdb_rating'],
                        'genre_name' => implode(', ', $genreNames),
                        'cover_image' => $record['movie_cover'],
                        'poster_image' => $record['movie_poster'],
                        'description' => $record['movie_desc'],
                        'total_time' => $record['total_time'],
                        'director_name' => $record['director_name'],
                        'cast_names' => $record['cast_names'],
                        'cost_type' => 'rent',
                        'price' => $record['movie_price'],
                        'is_rented' => true,
                        'user_have_rent_access' => $userHaveRentAccess,
                        'rent_validity_date' => $rentValidityDate,
                        'maturity_rating' => $record['maturity_rating'],
                        'release_date' => $record['release_date'],
                        'url_type' => $record['movie_type'],
                        'url' => $record['movie_url'],
                        'trailer_type' => $record['trailer_type'],
                        'trailer_url' => $record['trailer_url'],
                        'type' => 'movies',
                        'user_interaction' => [
                            'liked' => $record['liked'] == 1,
                            'disliked' => $record['disliked'] == 1
                        ]
                    ];
                }

            } elseif ($type == 'series') {
                $query = "SELECT e.*, s.*, e.id as  episode_id ,lang.language_name,wh.seconds 
                          FROM tbl_episode e  
                          LEFT JOIN tbl_series s ON e.series_id = s.id  
                          LEFT JOIN tbl_language lang ON s.language_id = lang.id  
                          LEFT JOIN tbl_genres g on s.genre_id=g.gid 
                           LEFT JOIN tbl_continue_watchlist wh ON wh.post_id = e.id AND wh.userid = '$userid' and wh.type='series'
                          WHERE e.id = '$id' AND e.status = 1 ";

                $seriesItems = getRecords($query);
                foreach ($seriesItems as $record) {
                    
                     $genreIds = explode(',', $record['genre_id']); // Convert genre_id string to array
                    $genreNames = [];
                
                    if (!empty($genreIds)) {
                        $genreIdStr = implode(',', array_map('intval', $genreIds)); // Ensure integers for safety
                        $genreQuery = "SELECT `genre_name` FROM `tbl_genres` WHERE `gid` IN ($genreIdStr)";
                        $genres = getRecords($genreQuery);
                
                        foreach ($genres as $genre) {
                            $genreNames[] = $genre['genre_name'];
                        }
                    }
                            $seriesId = $record['series_id'];
                            $seasonQuery = "SELECT `id`, `series_id`, `season_name`, `status` FROM `tbl_season` WHERE `series_id` = '$seriesId'";
                            $seasons = getRecords($seasonQuery);
                    $continueWatching[] = [
                        'id' => $record['episode_id'],
                        'title' => $record['series_name'] ?? '',
                        'language_name' => $record['language_name'],
                        'genre_name' => implode(', ', $genreNames),
                        'cover_image' => $record['series_cover'],
                        'poster_image' => $record['series_poster'],
                        'description' => $record['series_desc'],
                        'total_time' => $record['total_time'],
                        'director_name' => $record['director_name'],
                        'cast_names' => $record['cast_names'],
                        'cost_type' => $record['series_cost_type'] ?? 'free',
                        'price' => '0',
                        'maturity_rating' => $record['maturity_rating'],
                        'release_date' => $record['release_date'],
                        'episode_title' => $record['episode_title'],
                        'series_id' => $record['series_id'],
                        'selected_season_id' => $record['season_id'],
                        'url' => $record['episode_url'],
                        'url_type' => $record['episode_type'],
                        'seconds' => $record['seconds'],
                        'trailer_type' => $record['trailer_type'],
                        'trailer_url' => $record['trailer_url'],
                        'type' => 'series',
                        'user_interaction' => [
                            'liked' => false,
                            'disliked' => false
                        ],
                        'seasons' => $seasons
                    ];
                }
            }elseif ($type == 'shows') {
                 $query = "SELECT e.*, s.*, e.id as episode_id ,lang.language_name,wh.seconds 
                          FROM tbl_tv_episode e  
                          LEFT JOIN tbl_shows s ON e.shows_id = s.id  
                          LEFT JOIN tbl_genres g on s.genre_id=g.gid 
                          LEFT JOIN tbl_language lang ON s.language_id = lang.id  
                           LEFT JOIN tbl_continue_watchlist wh ON wh.post_id = e.id AND wh.userid = '$userid' and wh.type='shows'
                          WHERE e.id = '$id' AND e.status = 1 ";

                $seriesItems = getRecords($query);
                foreach ($seriesItems as $record) {
                    
                     $genreIds = explode(',', $record['genre_id']); // Convert genre_id string to array
                    $genreNames = [];
                
                    if (!empty($genreIds)) {
                        $genreIdStr = implode(',', array_map('intval', $genreIds)); // Ensure integers for safety
                        $genreQuery = "SELECT `genre_name` FROM `tbl_genres` WHERE `gid` IN ($genreIdStr)";
                        $genres = getRecords($genreQuery);
                
                        foreach ($genres as $genre) {
                            $genreNames[] = $genre['genre_name'];
                        }
                    }
                    
                    $showsId = $record['shows_id'];
                            $seasonQuery = "SELECT * FROM `tbl_tv_season` WHERE `shows_id` = '$showsId'";
                            $seasons = getRecords($seasonQuery);
                    $continueWatching[] = [
                        'id' => $record['episode_id'],
                        'title' => $record['shows_name'] ?? '',
                        'language_name' => $record['language_name'],
                        'genre_name' => implode(', ', $genreNames),
                        'cover_image' => $record['shows_cover'],
                        'poster_image' => $record['shows_poster'],
                        'description' => $record['shows_desc'],
                        'total_time' => $record['total_time'],
                        'director_name' => $record['director_name'],
                        'cast_names' => $record['cast_names'],
                        'cost_type' =>  'free',
                        'price' => '0',
                        'maturity_rating' => $record['maturity_rating'],
                        'release_date' => $record['release_date'],
                        'episode_title' => $record['episode_title'],
                        'selected_season_id' => $record['season_id'],
                        'url' => $record['episode_url'],
                        'url_type' => $record['episode_type'],
                        'seconds' => $record['seconds'],
                        'trailer_type' => $record['trailer_type'],
                        'trailer_url' => $record['trailer_url'],
                        'type' => 'shows',
                        'user_interaction' => [
                            'liked' => false,
                            'disliked' => false
                        ],
                        'seasons' => $seasons
                    ];
                }
            }elseif ($type == 'shortfilms') {
                
                $query = "SELECT sf.*, lang.language_name, lang.language_background,wh.seconds GROUP_CONCAT(DISTINCT genre.genre_name ORDER BY genre.genre_name ASC) AS genre_name,IF(tbl_likes.is_like = 1, 1, 0) AS liked,
                        IF(tbl_likes.is_like = 0, 1, 0) AS disliked FROM tbl_shortfilms sf LEFT JOIN tbl_language lang ON sf.language_id = lang.id  LEFT JOIN tbl_genres genre ON FIND_IN_SET(genre.gid, sf.genre_id) LEFT 
                        JOIN tbl_likes ON sf.id = tbl_likes.post_id AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'shortfilm' 
                        LEFT JOIN tbl_continue_watchlist wh on sf.id=wh.post_id and wh.type='shortfilm' and wh.userid='$userid'
                        WHERE sf.status = 1 AND sf.id = '$id' GROUP BY sf.id ORDER BY sf.id DESC";

                $records = getRecords($query);
                foreach ($records as $record) {
                    $continueWatching[] = [
                        'id' => $record['id'],
                        'title' => $record['movie_title'],
                        'language_name' => $record['language_name'],
                        'imdb_rating' => $record['imdb_rating'],
                        'genre_name' => '',
                        'seconds' => $record['seconds'],
                        'cover_image' => $record['movie_cover'],
                        'poster_image' => $record['movie_poster'],
                        'description' => $record['movie_desc'],
                        'total_time' => $record['total_time'],
                        'director_name' => $record['director_name'],
                        'cast_names' => $record['cast_names'],
                        'cost_type' => 'free',
                        'price' => '0',
                        'maturity_rating' => $record['maturity_rating'],
                        'release_date' => $record['release_date'],
                        'url_type' => $record['movie_type'],
                        'url' => $record['movie_url'],
                        'trailer_type' => $record['trailer_type'],
                        'trailer_url' => $record['trailer_url'],
                        'type' => 'shortfilm',
                        'user_interaction' => [
                            'liked' => $record['liked'] == 1,
                            'disliked' => $record['disliked'] == 1
                        ]
                    ];
                }
            }elseif($type=='song'){
                
                $query = "SELECT sf.*, lang.language_name, lang.language_background,wh.seconds, IF(tbl_likes.is_like = 1, 1, 0) AS liked,IF(tbl_likes.is_like = 0, 1, 0) AS disliked FROM tbl_songs sf LEFT JOIN tbl_language lang ON sf.language_id = lang.id LEFT JOIN tbl_likes ON sf.id = tbl_likes.post_id AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'song'
                LEFT JOIN tbl_continue_watchlist wh ON wh.post_id = sf.id AND wh.userid = '$userid' and wh.type='song'
                
                WHERE sf.status = 1 AND sf.id = '$id' GROUP BY sf.id ORDER BY sf.id DESC";

                $records = getRecords($query);
                foreach ($records as $record) {
                    $continueWatching[] = [
                        'id' => $record['id'],
                        'title' => $record['song_title'],
                        'language_name' => $record['language_name'],
                        'imdb_rating' => $record['imdb_rating'],
                        'genre_name' => '',
                        'seconds' => $record['seconds'],
                        'cover_image' => $record['song_cover'],
                        'poster_image' => $record['song_poster'],
                        'description' => $record['song_desc'],
                        'total_time' => $record['total_time'],
                        'director_name' => $record['director_name'],
                        'cast_names' => $record['cast_names'],
                        'cost_type' => 'free',
                        'price' => '0',
                        'maturity_rating' => $record['maturity_rating'],
                        'release_date' => $record['release_date'],
                        'url_type' => $record['song_type'],
                        'url' => $record['song_url'],
                        'trailer_type' => $record['trailer_type'],
                        'trailer_url' => $record['trailer_url'],
                        'type' => 'song',
                        'user_interaction' => [
                            'liked' => $record['liked'] == 1,
                            'disliked' => $record['disliked'] == 1
                        ]
                    ];
                }
                
            }elseif($type=='drama'){
                
                 $query = "SELECT sf.*, lang.language_name, lang.language_background,wh.seconds, IF(tbl_likes.is_like = 1, 1, 0) AS liked,IF(tbl_likes.is_like = 0, 1, 0) AS disliked FROM tbl_drama sf LEFT JOIN tbl_language lang ON sf.language_id = lang.id LEFT JOIN tbl_likes ON sf.id = tbl_likes.post_id  AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'drama'
                 
                  LEFT JOIN tbl_continue_watchlist wh ON wh.post_id = sf.id AND wh.userid = '$userid' and wh.type='drama'
                 
                 WHERE sf.status = 1 AND sf.id = '$id' GROUP BY sf.id ORDER BY sf.id DESC";

                $records = getRecords($query);
                foreach ($records as $record) {
                    $continueWatching[] = [
                        'id' => $record['id'],
                        'title' => $record['drama_title'],
                        'language_name' => $record['language_name'],
                        'imdb_rating' => $record['imdb_rating'],
                        'genre_name' => '',
                        'seconds' => $record['seconds'],
                        'cover_image' => $record['drama_cover'],
                        'poster_image' => $record['drama_poster'],
                        'description' => $record['drama_desc'],
                        'total_time' => $record['total_time'],
                        'director_name' => $record['director_name'],
                        'cast_names' => $record['cast_names'],
                        'cost_type' => 'free',
                        'price' => '0',
                        'maturity_rating' => $record['maturity_rating'],
                        'release_date' => $record['release_date'],
                        'url_type' => $record['drama_type'],
                        'url' => $record['drama_url'],
                        'trailer_type' => $record['trailer_type'],
                        'trailer_url' => $record['trailer_url'],
                        'type' => 'drama',
                        'user_interaction' => [
                            'liked' => $record['liked'] == 1,
                            'disliked' => $record['disliked'] == 1
                        ]
                    ];
                }
                
            }
        }

        // Add Continue Watching to homeData first if exists
        if (!empty($continueWatching)) {
            $homeData[] = [
                'home_cat_id' => 0,
                'sequence' => 0,
                'title' => 'Continue Watching',
                'type' => 'continuewatching',
                'items' => $continueWatching
            ];
        }

        // STEP 2: Load Home Categories
        $gethomeCategories = getRecords("SELECT * FROM tbl_home ORDER BY sequence ASC");

        if (!empty($gethomeCategories)) {
            foreach ($gethomeCategories as $cat) {
                    $sectionId = $cat['id'];
                    $sectionseq = $cat['sequence'];
                    $sectionTitle = $cat['home_title'];
                    $sectionType = $cat['type']; 
                    $items = [];
                    if ($sectionType == 'movies') {
                        $query = "SELECT movie.*, lang.language_name, lang.language_background,IF(tbl_likes.is_like = 1, 1, 0) AS liked,IF(tbl_likes.is_like = 0, 1, 0) AS disliked FROM tbl_movies movie LEFT JOIN tbl_language lang ON movie.language_id = lang.id LEFT JOIN tbl_genres g on movie.genre_id=g.gid  LEFT JOIN tbl_likes ON movie.id = tbl_likes.post_id AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'movies' WHERE movie.status = 1 AND movie.home_cat_id = '$sectionId' ORDER BY movie.id DESC";
                        $movieItems = getRecords($query);
                        foreach ($movieItems as $item) {
                             $genreIds = explode(',', $item['genre_id']); // Convert genre_id string to array
                    $genreNames = [];
                
                    if (!empty($genreIds)) {
                        $genreIdStr = implode(',', array_map('intval', $genreIds)); // Ensure integers for safety
                        $genreQuery = "SELECT `genre_name` FROM `tbl_genres` WHERE `gid` IN ($genreIdStr)";
                        $genres = getRecords($genreQuery);
                
                        foreach ($genres as $genre) {
                            $genreNames[] = $genre['genre_name'];
                        }
                    }
                            $items[] = [
                                'id' => $item['id'],
                                'title' => $item['movie_title'],
                                'language_name' => $item['language_name'],
                                'imdb_rating'=> $item['imdb_rating'],
                                'genre_name' => implode(', ', $genreNames), 
                                'cover_image' => $item['movie_cover'],
                                'poster_image' => $item['movie_poster'],
                                'description' => $item['movie_desc'],
                                'total_time' => $item['total_time'],
                                'director_name' => $item['director_name'],
                                'cast_names' => $item['cast_names'],
                                'cost_type' => $item['movie_cost_type'],
                                'price' => '0',
                                'maturity_rating' => $item['maturity_rating'],
                                'release_date' => $item['release_date'],
                                'url_type'=>$item['movie_type'],
                                'url' => $item['movie_url'],
                                'trailer_type' => $item['trailer_type'],
                                 'trailer_url' => $item['trailer_url'],
                                'type' => 'movies',
                                'user_interaction' => [
                                    'liked' => $item['liked'] == 1,
                                    'disliked' => $item['disliked'] == 1
                                ]
                            ];
                            unset($item['liked'], $item['disliked']);
                        }
                    } elseif ($sectionType == 'rented') {
                        $query = "SELECT movie.*, lang.language_name, lang.language_background,IF(tbl_likes.is_like = 1, 1, 0) AS liked, IF(tbl_likes.is_like = 0, 1, 0) AS disliked FROM tbl_rentmovies movie
                        LEFT JOIN tbl_language lang ON movie.language_id = lang.id LEFT JOIN tbl_genres g on movie.genre_id=g.gid LEFT JOIN tbl_likes ON movie.id = tbl_likes.post_id AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'rentedmovies' WHERE 
                        movie.status = 1 AND movie.home_cat_id = '$sectionId' ORDER BY movie.id DESC";
                        $movieItems = getRecords($query);
                        foreach ($movieItems as $item) {
                            $genreIds = explode(',', $item['genre_id']); // Convert genre_id string to array
                    $genreNames = [];
                
                    if (!empty($genreIds)) {
                        $genreIdStr = implode(',', array_map('intval', $genreIds)); // Ensure integers for safety
                        $genreQuery = "SELECT `genre_name` FROM `tbl_genres` WHERE `gid` IN ($genreIdStr)";
                        $genres = getRecords($genreQuery);
                
                        foreach ($genres as $genre) {
                            $genreNames[] = $genre['genre_name'];
                        }
                    }
                            $getbuym = getRecord("SELECT * FROM `tbl_buymovies` WHERE userid='$userid' AND movie_id='{$item['id']}' AND expire=0");

                            $userHaveRentAccess = false;
                            $rentValidityDate = null;       
                             if ($getbuym && $getbuym['expire'] == 0) {
                        $validTill = date('Y-m-d', strtotime($item['purchase_dt'] . ' + ' . intval($item['movie_validation']) . ' days'));
                        if (strtotime($validTill) >= time()) {
                            $userHaveRentAccess = true;
                            $rentValidityDate = $validTill;
                        }
                    }
                    
                            $items[] = [
                                'id' => $item['id'],
                                'title' => $item['movie_title'],
                                'language_name' => $item['language_name'],
                                'imdb_rating'=> $item['imdb_rating'],
                                  'genre_name' => implode(', ', $genreNames), 
                                'cover_image' => $item['movie_cover'],
                                'poster_image' => $item['movie_poster'],
                                'description' => $item['movie_desc'],
                                'total_time' => $item['total_time'],
                                'director_name' => $item['director_name'],
                                'cast_names' => $item['cast_names'],
                               'cost_type' => "rent",
                                'price' => $item['movie_price'],
                                'is_rented'=>true,
                                'user_have_rent_access'=>$userHaveRentAccess,
                                'rent_validity_date'=>$rentValidityDate,
                                'maturity_rating' => $item['maturity_rating'],
                                'release_date' => $item['release_date'],
                                'url_type'=>$item['movie_type'],
                                'url' => $item['movie_url'],
                                'trailer_type' => $item['trailer_type'],
                                 'trailer_url' => $item['trailer_url'],
                                'type' => 'movies',
                                'user_interaction' => [
                                    'liked' => $item['liked'] == 1,
                                    'disliked' => $item['disliked'] == 1
                                ]
                            ];
                            
                            unset($item['liked'], $item['disliked']);
                        }
                    } elseif ($sectionType == 'series') {
                        $query = "SELECT s.*, lang.language_name, lang.language_background, GROUP_CONCAT(DISTINCT genre.genre_name ORDER BY genre.genre_name ASC) AS genre_name,IF(tbl_likes.is_like = 1, 1, 0) AS liked,
                        IF(tbl_likes.is_like = 0, 1, 0) AS disliked FROM tbl_series s LEFT JOIN tbl_language lang ON s.language_id = lang.id LEFT JOIN tbl_genres genre ON FIND_IN_SET(genre.gid, s.genre_id) LEFT JOIN tbl_likes ON s.id = tbl_likes.post_id AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'series'  WHERE s.status = 1 AND s.home_cat_id = '$sectionId' GROUP BY s.id ORDER BY s.id DESC";
                        $seriesItems = getRecords($query);
                        foreach ($seriesItems as $item) {
                             $genreIds = explode(',', $item['genre_id']); 
                             $genreNames = [];
                             if (!empty($genreIds)) {
                                 $genreIdStr = implode(',', array_map('intval', $genreIds)); 
                                 $genreQuery = "SELECT `genre_name` FROM `tbl_genres` WHERE `gid` IN ($genreIdStr)";
                                 $genres = getRecords($genreQuery);
                
                                 foreach ($genres as $genre) {
                                    $genreNames[] = $genre['genre_name'];
                                 }
                            }
                            // Fetch seasons for the current series
                            $seriesId = $item['id'];
                            $seasonQuery = "SELECT `id`, `series_id`, `season_name`, `status` FROM `tbl_season` WHERE `series_id` = '$seriesId'";
                            $seasons = getRecords($seasonQuery); // This should return an array of seasons
                            $items[] = [
                                'id' => $item['id'],
                                'title' => $item['series_name'],
                                'language_name' => $item['language_name'],
                                'genre_name' => implode(', ', $genreNames),
                                'cover_image' => $item['series_cover'],
                                'imdb_rating'=> $item['imdb_rating'],
                                'poster_image' => $item['series_poster'],
                                'description' => $item['series_desc'],
                                'total_time' => $item['total_time'],
                                'director_name' => $item['director_name'],
                                'cast_names' => $item['cast_names'],
                                'cost_type' => $item['series_cost_type'],
                                'price' => '0',
                                'maturity_rating' => $item['maturity_rating'],
                                'release_date' => $item['release_date'],
                                'trailer_url' => $item['trailer_url'],
                                'trailer_type' => $item['trailer_type'],
                                'url' => $item['series_url'],
                                'type' => 'series',
                                'user_interaction' => [
                                    'liked' => $item['liked'] == 1,
                                    'disliked' => $item['disliked'] == 1
                                ],
                                'seasons' => $seasons // 👈 Add seasons here
                            ];
                            unset($item['liked'], $item['disliked']);
                        }
                    }elseif ($sectionType == 'shows') {
                        $query = "SELECT s.*, lang.language_name, lang.language_background, GROUP_CONCAT(DISTINCT genre.genre_name ORDER BY genre.genre_name ASC) AS genre_name,IF(tbl_likes.is_like = 1, 1, 0) AS liked,
                        IF(tbl_likes.is_like = 0, 1, 0) AS disliked FROM tbl_shows s LEFT JOIN tbl_language lang ON s.language_id = lang.id  LEFT JOIN tbl_genres genre ON FIND_IN_SET(genre.gid, s.genre_id)  LEFT JOIN  
                        tbl_likes ON s.id = tbl_likes.post_id AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'shows' WHERE s.status = 1 AND s.home_cat_id = '$sectionId'  GROUP BY s.id  ORDER BY s.id DESC";    
                        $showsItems = getRecords($query);
                        foreach ($showsItems as $item) {
                            // Fetch seasons for the current series
                            $genreIds = explode(',', $item['genre_id']); // Convert genre_id string to array
                             $genreNames = [];
                             if (!empty($genreIds)) {
                                 $genreIdStr = implode(',', array_map('intval', $genreIds)); // Ensure integers for safety
                                 $genreQuery = "SELECT `genre_name` FROM `tbl_genres` WHERE `gid` IN ($genreIdStr)";
                                 $genres = getRecords($genreQuery);
                
                                 foreach ($genres as $genre) {
                                    $genreNames[] = $genre['genre_name'];
                                 }
                            }
                            $showsId = $item['id'];
                            $seasonQuery = "SELECT * FROM `tbl_tv_season` WHERE `shows_id` = '$showsId'";
                            $seasons = getRecords($seasonQuery); // This should return an array of seasons
                            $items[] = [
                                'id' => $item['id'],
                                'title' => $item['shows_name'],
                                'language_name' => $item['language_name'],
                                'genre_name' => implode(', ', $genreNames),
                                'cover_image' => $item['shows_cover'],
                                'imdb_rating'=> $item['imdb_rating'],
                                'poster_image' => $item['shows_poster'],
                                'description' => $item['shows_desc'],
                                'total_time' => $item['total_time'],
                                'director_name' => $item['director_name'],
                                'cast_names' => $item['cast_names'],
                                'cost_type' => 'free',
                                'price' => '0',
                                'maturity_rating' => $item['maturity_rating'],
                                'release_date' => $item['release_date'],
                                'trailer_url' => $item['trailer_url'],
                                'trailer_type' => $item['trailer_type'],
                                'url' => $item['shows_url'],
                                'type' => 'shows',
                                'user_interaction' => [
                                    'liked' => $item['liked'] == 1,
                                    'disliked' => $item['disliked'] == 1
                                ],
                                'seasons' => $seasons // 👈 Add seasons here
                            ];
                            unset($item['liked'], $item['disliked']);
                        }
                    }elseif ($sectionType == 'shortfilms') {
                        $query = "SELECT sf.*, lang.language_name, lang.language_background, GROUP_CONCAT(DISTINCT genre.genre_name ORDER BY genre.genre_name ASC) AS genre_name,IF(tbl_likes.is_like = 1, 1, 0) AS liked,
                        IF(tbl_likes.is_like = 0, 1, 0) AS disliked FROM tbl_shortfilms sf LEFT JOIN tbl_language lang ON sf.language_id = lang.id  LEFT JOIN tbl_genres genre ON FIND_IN_SET(genre.gid, sf.genre_id) LEFT 
                        JOIN tbl_likes ON sf.id = tbl_likes.post_id AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'shortfilm' WHERE sf.status = 1 AND sf.home_cat_id = '$sectionId' GROUP BY sf.id ORDER BY sf.id DESC";
                        $shortfilmItems = getRecords($query);
                        foreach ($shortfilmItems as $item) {
                            $items[] = [
                                'id' => $item['id'],
                                'title' => $item['movie_title'],
                                'language_name' => $item['language_name'],
                                'genre_name' => $item['genre_name'],
                                'cover_image' => $item['movie_cover'],
                                'imdb_rating'=> $item['imdb_rating'],
                                'poster_image' => $item['movie_poster'],
                                'description' => $item['movie_desc'],
                                'total_time' => $item['total_time'],
                                'director_name' => $item['director_name'],
                                'cast_names' => $item['cast_names'],
                                'cost_type' => 'free',
                                'price' => '0',
                                'maturity_rating' => $item['maturity_rating'],
                                'release_date' => $item['release_date'],
                                'url_type'=>$item['movie_type'],
                                'url' => $item['movie_url'],
                                'trailer_type' => $item['trailer_type'],
                                'trailer_url' => $item['trailer_url'],
                                'type' => 'shortfilm',
                                'user_interaction' => [
                                    'liked' => $item['liked'] == 1,
                                    'disliked' => $item['disliked'] == 1
                                            ]
                            ];
                            unset($item['liked'], $item['disliked']);
                        }
                    }elseif ($sectionType == 'songs') {
                       $query = "SELECT sf.*, lang.language_name, lang.language_background, IF(tbl_likes.is_like = 1, 1, 0) AS liked,IF(tbl_likes.is_like = 0, 1, 0) AS disliked FROM tbl_songs sf LEFT JOIN tbl_language lang ON sf.language_id = lang.id LEFT JOIN tbl_likes ON sf.id = tbl_likes.post_id AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'song' WHERE sf.status = 1 AND sf.home_cat_id = '$sectionId' GROUP BY sf.id ORDER BY sf.id DESC";
                       $shortfilmItems = getRecords($query);
                       foreach ($shortfilmItems as $item) {
                          $items[] = [
                            'id' => $item['id'],
                            'title' => $item['song_title'],
                            'language_name' => $item['language_name'],
                            'cover_image' => $item['song_cover'],
                            'imdb_rating'=> $item['imdb_rating'],
                            'poster_image' => $item['song_poster'],
                            'description' => $item['song_desc'],
                            'total_time' => $item['total_time'],
                            'director_name' => $item['director_name'],
                            'cast_names' => $item['cast_names'],
                            'cost_type' => 'free',
                            'price' => '0',
                            'maturity_rating' => $item['maturity_rating'],
                            'release_date' => $item['release_date'],
                            'url_type'=>$item['song_type'],
                            'url' => $item['song_url'],
                            'trailer_type' => $item['trailer_type'],
                            'trailer_url' => $item['trailer_url'],
                            'type' => 'songs',
                            'user_interaction' => [
                                'liked' => $item['liked'] == 1,
                                'disliked' => $item['disliked'] == 1
                             ]
                          ];
                            unset($item['liked'], $item['disliked']);
                        }
                    }elseif ($sectionType == 'drama') {
                        $query = "SELECT sf.*, lang.language_name, lang.language_background, IF(tbl_likes.is_like = 1, 1, 0) AS liked,IF(tbl_likes.is_like = 0, 1, 0) AS disliked FROM tbl_drama sf LEFT JOIN tbl_language lang ON sf.language_id = lang.id LEFT JOIN tbl_likes ON sf.id = tbl_likes.post_id  AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'drama' WHERE sf.status = 1 AND sf.home_cat_id = '$sectionId' GROUP BY sf.id ORDER BY sf.id DESC";
                        $shortfilmItems = getRecords($query);
                        foreach ($shortfilmItems as $item) {
                            $items[] = [
                                'id' => $item['id'],
                                'title' => $item['drama_title'],
                                'language_name' => $item['language_name'],
                                'cover_image' => $item['drama_cover'],
                                'imdb_rating'=> $item['imdb_rating'],
                                'poster_image' => $item['drama_poster'],
                                'description' => $item['drama_desc'],
                                'total_time' => $item['total_time'],
                                'director_name' => $item['director_name'],
                                'cast_names' => $item['cast_names'],
                                'cost_type' => 'free',
                                'price' => '0',
                                'maturity_rating' => $item['maturity_rating'],
                                'release_date' => $item['release_date'],
                                'url_type'=>$item['drama_type'],
                                'url' => $item['drama_url'],
                                'trailer_type' => $item['trailer_type'],
                                'trailer_url' => $item['trailer_url'],
                                'type' => 'drama',
                                'user_interaction' => [
                                    'liked' => $item['liked'] == 1,
                                    'disliked' => $item['disliked'] == 1
                                ]
                            ];
                            unset($item['liked'], $item['disliked']);
                        }
                    }
                     $homeData[] = ['home_cat_id' => $sectionId,'sequence' => $sectionseq,'title' => $sectionTitle,'type' => $sectionType,'items' => $items];
                }

            // STEP 3: Return Final JSON
            echo json_encode([
                'status' => true,
                'error' => 0,
                'success' => 1,
                'msg' => 'Home Data Loaded',
                'data' => $homeData
            ]);
        } else {
            echo json_encode(['status' => false, 'error' => 1, 'success' => 0, 'msg' => 'No data found']);
        }

    } else {
        http_response_code(403);
        echo json_encode(['status' => true, 'statusCode' => 403, 'error' => 0, 'success' => 1, 'msg' => 'Logged into another device']);
    }

    exit();
    break;

    
    case "addTopSearchLog":
    $userid = intval(@$_POST['userid']);
    $post_id = intval(@$_POST['post_id']);
    $type = trim(@$_POST['type']);

    if (!app_login($userid)) {
        $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' => 'Invalid Userid or User Not Active');  
        echo json_encode($response);
        exit();
    }

    // Validate inputs
    if (empty($post_id) || empty($type)) {
        $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' => 'Missing Required Params');  
        echo json_encode($response);
        exit();
    }

    // Optional: limit to allowed types
    $allowedTypes = ['movies', 'series', 'shows', 'shortfilm', 'song'];
    if (!in_array($type, $allowedTypes)) {
        $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' => 'Invalid Content Type');  
        echo json_encode($response);
        exit();
    }

    // Insert into tbl_search_log
    $insert = query("INSERT INTO `tbl_search_log` (`user_id`, `post_id`, `post_type`, `searched_at`)
                     VALUES ('$userid', '$post_id', '$type', '$fdate')");

    if ($insert) {
        $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' => 'Search log added successfully');
    } else {
        $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' => 'Failed to insert search log');
    }

    echo json_encode($response);
    exit();
    break;

    
    case "getTopSearch":
    $userid = intval(@$_POST['userid']);

    if (!app_login($userid)) {
        echo json_encode([
            'status' => false,
            'error' => 1,
            'success' => 0,
            'msg' => 'Invalid Userid or User Not Active'
        ]);
        exit();
    }

    $searchQuery = "SELECT post_id, post_type, COUNT(*) AS total
                    FROM tbl_search_log
                    WHERE user_id = '$userid'
                    GROUP BY post_id, post_type
                    ORDER BY total DESC
                    LIMIT 20";

    $topItems = getRecords($searchQuery);
    $results = [];

    foreach ($topItems as $item) {
        $id = $item['post_id'];
        $type = $item['post_type'];

        switch ($type) {
            case 'movies':
                $query = "SELECT m.*, lang.language_name, lang.language_background,
                                IF(l.is_like = 1, 1, 0) AS liked,
                                IF(l.is_like = 0, 1, 0) AS disliked
                          FROM tbl_movies m
                          LEFT JOIN tbl_language lang ON m.language_id = lang.id
                          LEFT JOIN tbl_likes l ON m.id = l.post_id 
                              AND l.userid = '$userid' AND l.type = 'movies'
                          WHERE m.id = '$id' AND m.status = 1";
                $record = getRecord($query);
                if ($record) {
                    $results[] = [
                        'id' => $record['id'],
                        'title' => $record['movie_title'],
                        'language_name' => $record['language_name'],
                        'imdb_rating' => $record['imdb_rating'],
                        'genre_name' => '',
                        'cover_image' => $record['movie_cover'],
                        'poster_image' => $record['movie_poster'],
                        'description' => $record['movie_desc'],
                        'total_time' => $record['total_time'],
                        'director_name' => $record['director_name'],
                        'cast_names' => $record['cast_names'],
                        'cost_type' => $record['movie_cost_type'],
                        'price' => '0',
                        'maturity_rating' => $record['maturity_rating'],
                        'release_date' => $record['release_date'],
                        'url_type' => $record['movie_type'],
                        'url' => $record['movie_url'],
                        'trailer_type' => $record['trailer_type'],
                        'trailer_url' => $record['trailer_url'],
                        'type' => 'movies',
                        'user_interaction' => [
                            'liked' => $record['liked'] == 1,
                            'disliked' => $record['disliked'] == 1
                        ]
                    ];
                }
                break;

            case 'series':
                $query = "SELECT s.*, lang.language_name, lang.language_background,
                                 GROUP_CONCAT(DISTINCT g.genre_name ORDER BY g.genre_name ASC) AS genre_name,
                                 IF(l.is_like = 1, 1, 0) AS liked,
                                 IF(l.is_like = 0, 1, 0) AS disliked
                          FROM tbl_series s
                          LEFT JOIN tbl_language lang ON s.language_id = lang.id
                          LEFT JOIN tbl_genres g ON FIND_IN_SET(g.gid, s.genre_id)
                          LEFT JOIN tbl_likes l ON s.id = l.post_id 
                              AND l.userid = '$userid' AND l.type = 'series'
                          WHERE s.id = '$id' AND s.status = 1
                          GROUP BY s.id";
                $record = getRecord($query);
                if ($record) {
                    $seasonQuery = "SELECT `id`, `series_id`, `season_name`, `status` FROM `tbl_season` WHERE `series_id` = '$id'";
                    $seasons = getRecords($seasonQuery);
                    $results[] = [
                        'id' => $record['id'],
                        'title' => $record['series_name'],
                        'language_name' => $record['language_name'],
                        'imdb_rating' => $record['imdb_rating'],
                        'genre_name' => $record['genre_name'],
                        'cover_image' => $record['series_cover'],
                        'poster_image' => $record['series_poster'],
                        'description' => $record['series_desc'],
                        'total_time' => $record['total_time'],
                        'director_name' => $record['director_name'],
                        'cast_names' => $record['cast_names'],
                        'cost_type' => $record['series_cost_type'],
                        'price' => '0',
                        'maturity_rating' => $record['maturity_rating'],
                        'release_date' => $record['release_date'],
                        'url' => $record['series_url'],
                        'trailer_type' => $record['trailer_type'],
                        'trailer_url' => $record['trailer_url'],
                        'type' => 'series',
                        'user_interaction' => [
                            'liked' => $record['liked'] == 1,
                            'disliked' => $record['disliked'] == 1
                        ],
                        'seasons' => $seasons
                    ];
                }
                break;

            case 'shows':
                $query = "SELECT s.*, lang.language_name, lang.language_background,
                                 GROUP_CONCAT(DISTINCT g.genre_name ORDER BY g.genre_name ASC) AS genre_name,
                                 IF(l.is_like = 1, 1, 0) AS liked,
                                 IF(l.is_like = 0, 1, 0) AS disliked
                          FROM tbl_shows s
                          LEFT JOIN tbl_language lang ON s.language_id = lang.id
                          LEFT JOIN tbl_genres g ON FIND_IN_SET(g.gid, s.genre_id)
                          LEFT JOIN tbl_likes l ON s.id = l.post_id 
                              AND l.userid = '$userid' AND l.type = 'shows'
                          WHERE s.id = '$id' AND s.status = 1
                          GROUP BY s.id";
                $record = getRecord($query);
                if ($record) {
                    $seasonQuery = "SELECT * FROM `tbl_tv_season` WHERE `shows_id` = '$id'";
                    $seasons = getRecords($seasonQuery);
                    $results[] = [
                        'id' => $record['id'],
                        'title' => $record['shows_name'],
                        'language_name' => $record['language_name'],
                        'imdb_rating' => $record['imdb_rating'],
                        'genre_name' => $record['genre_name'],
                        'cover_image' => $record['shows_cover'],
                        'poster_image' => $record['shows_poster'],
                        'description' => $record['shows_desc'],
                        'total_time' => $record['total_time'],
                        'director_name' => $record['director_name'],
                        'cast_names' => $record['cast_names'],
                        'cost_type' => 'free',
                        'price' => '0',
                        'maturity_rating' => $record['maturity_rating'],
                        'release_date' => $record['release_date'],
                        'url' => $record['shows_url'],
                        'trailer_type' => $record['trailer_type'],
                        'trailer_url' => $record['trailer_url'],
                        'type' => 'shows',
                        'user_interaction' => [
                            'liked' => $record['liked'] == 1,
                            'disliked' => $record['disliked'] == 1
                        ],
                        'seasons' => $seasons
                    ];
                }
                break;

            // Extend for shortfilm, song, etc., if needed

        }
    }

    echo json_encode([
        'status' => true,
        'success' => 1,
        'msg' => 'Top Search Results',
        'data' => $results
    ]);
    break;
	// ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	
        case "addToWatchlist":
            
    $userid = intval(@$_POST['userid']);
    $post_id = intval(@$_POST['post_id']);
    $type = trim(@$_POST['type']);

    // Check login
    if (!app_login($userid)) {
        $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' => 'Invalid Userid or User Not Active');
        echo json_encode($response);
        exit();
    }

    // Validate inputs
    if (empty($post_id) || empty($type)) {
        $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' => 'Missing Required Params');
        echo json_encode($response);
        exit();
    }

    // Optional: restrict to allowed types
    $allowedTypes = ['movies', 'series', 'shows', 'shortfilm', 'drama', 'song'];
    if (!in_array($type, $allowedTypes)) {
        $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' => 'Invalid Content Type');
        echo json_encode($response);
        exit();
    }

    // Check if already in watchlist
    $check = getRecord("SELECT id FROM tbl_watchlist WHERE userid = '$userid' AND post_id = '$post_id' AND type = '$type'");

    if ($check) {
        $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' => 'Already in Watchlist');
        echo json_encode($response);
        exit();
    }

    // Insert into watchlist
    $insert = query("INSERT INTO tbl_watchlist (userid, post_id, type, created_at)
                     VALUES ('$userid', '$post_id', '$type', '$fdate')");

    if ($insert) {
        $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' => 'Added to Watchlist');
    } else {
        $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' => 'Failed to add to Watchlist');
    }

    echo json_encode($response);
    exit();
    break;

// ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        case "getWatchlist":
    $userid = intval(@$_POST['userid']);

    if (!app_login($userid)) {
        echo json_encode([
            'status' => false,
            'error' => 1,
            'success' => 0,
            'msg' => 'Invalid Userid or User Not Active'
        ]);
        exit();
    }

    // Fetch user's watchlist
    $watchlistQuery = "SELECT post_id, type FROM tbl_watchlist WHERE userid = '$userid' ORDER by id DESC";
    $watchlistItems = getRecords($watchlistQuery);
    $results = [];

    foreach ($watchlistItems as $item) {
        $id = $item['post_id'];
        $type = $item['type'];

        switch ($type) {
            case 'movies':
                $query = "SELECT m.*, lang.language_name, lang.language_background,
                                IF(l.is_like = 1, 1, 0) AS liked,
                                IF(l.is_like = 0, 1, 0) AS disliked
                          FROM tbl_movies m
                          LEFT JOIN tbl_language lang ON m.language_id = lang.id
                          LEFT JOIN tbl_likes l ON m.id = l.post_id 
                              AND l.userid = '$userid' AND l.type = 'movies'
                          WHERE m.id = '$id' AND m.status = 1";
                $record = getRecord($query);
                if ($record) {
                    $results[] = [
                        'id' => $record['id'],
                        'title' => $record['movie_title'],
                        'language_name' => $record['language_name'],
                        'imdb_rating' => $record['imdb_rating'],
                        'genre_name' => '',
                        'cover_image' => $record['movie_cover'],
                        'poster_image' => $record['movie_poster'],
                        'description' => $record['movie_desc'],
                        'total_time' => $record['total_time'],
                        'director_name' => $record['director_name'],
                        'cast_names' => $record['cast_names'],
                        'cost_type' => $record['movie_cost_type'],
                        'price' => '0',
                        'maturity_rating' => $record['maturity_rating'],
                        'release_date' => $record['release_date'],
                        'url_type' => $record['movie_type'],
                        'url' => $record['movie_url'],
                        'trailer_type' => $record['trailer_type'],
                        'trailer_url' => $record['trailer_url'],
                        'type' => 'movies',
                        'user_interaction' => [
                            'liked' => $record['liked'] == 1,
                            'disliked' => $record['disliked'] == 1
                        ]
                    ];
                }
                break;

            case 'series':
                $query = "SELECT s.*, lang.language_name, lang.language_background,
                                 GROUP_CONCAT(DISTINCT g.genre_name ORDER BY g.genre_name ASC) AS genre_name,
                                 IF(l.is_like = 1, 1, 0) AS liked,
                                 IF(l.is_like = 0, 1, 0) AS disliked
                          FROM tbl_series s
                          LEFT JOIN tbl_language lang ON s.language_id = lang.id
                          LEFT JOIN tbl_genres g ON FIND_IN_SET(g.gid, s.genre_id)
                          LEFT JOIN tbl_likes l ON s.id = l.post_id 
                              AND l.userid = '$userid' AND l.type = 'series'
                          WHERE s.id = '$id' AND s.status = 1
                          GROUP BY s.id";
                $record = getRecord($query);
                if ($record) {
                    $seasonQuery = "SELECT id, series_id, season_name, status FROM tbl_season WHERE series_id = '$id'";
                    $seasons = getRecords($seasonQuery);
                    $results[] = [
                        'id' => $record['id'],
                        'title' => $record['series_name'],
                        'language_name' => $record['language_name'],
                        'imdb_rating' => $record['imdb_rating'],
                        'genre_name' => $record['genre_name'],
                        'cover_image' => $record['series_cover'],
                        'poster_image' => $record['series_poster'],
                        'description' => $record['series_desc'],
                        'total_time' => $record['total_time'],
                        'director_name' => $record['director_name'],
                        'cast_names' => $record['cast_names'],
                        'cost_type' => $record['series_cost_type'],
                        'price' => '0',
                        'maturity_rating' => $record['maturity_rating'],
                        'release_date' => $record['release_date'],
                        'url' => $record['series_url'],
                        'trailer_type' => $record['trailer_type'],
                        'trailer_url' => $record['trailer_url'],
                        'type' => 'series',
                        'user_interaction' => [
                            'liked' => $record['liked'] == 1,
                            'disliked' => $record['disliked'] == 1
                        ],
                        'seasons' => $seasons
                    ];
                }
                break;

            case 'shows':
                $query = "SELECT s.*, lang.language_name, lang.language_background,
                                 GROUP_CONCAT(DISTINCT g.genre_name ORDER BY g.genre_name ASC) AS genre_name,
                                 IF(l.is_like = 1, 1, 0) AS liked,
                                 IF(l.is_like = 0, 1, 0) AS disliked
                          FROM tbl_shows s
                          LEFT JOIN tbl_language lang ON s.language_id = lang.id
                          LEFT JOIN tbl_genres g ON FIND_IN_SET(g.gid, s.genre_id)
                          LEFT JOIN tbl_likes l ON s.id = l.post_id 
                              AND l.userid = '$userid' AND l.type = 'shows'
                          WHERE s.id = '$id' AND s.status = 1
                          GROUP BY s.id";
                $record = getRecord($query);
                if ($record) {
                    $seasonQuery = "SELECT * FROM tbl_tv_season WHERE shows_id = '$id'";
                    $seasons = getRecords($seasonQuery);
                    $results[] = [
                        'id' => $record['id'],
                        'title' => $record['shows_name'],
                        'language_name' => $record['language_name'],
                        'imdb_rating' => $record['imdb_rating'],
                        'genre_name' => $record['genre_name'],
                        'cover_image' => $record['shows_cover'],
                        'poster_image' => $record['shows_poster'],
                        'description' => $record['shows_desc'],
                        'total_time' => $record['total_time'],
                        'director_name' => $record['director_name'],
                        'cast_names' => $record['cast_names'],
                        'cost_type' => 'free',
                        'price' => '0',
                        'maturity_rating' => $record['maturity_rating'],
                        'release_date' => $record['release_date'],
                        'url' => $record['shows_url'],
                        'trailer_type' => $record['trailer_type'],
                        'trailer_url' => $record['trailer_url'],
                        'type' => 'shows',
                        'user_interaction' => [
                            'liked' => $record['liked'] == 1,
                            'disliked' => $record['disliked'] == 1
                        ],
                        'seasons' => $seasons
                    ];
                }
                break;
                case  "shortfilm" :
                   $query = "SELECT sf.*, 
                        lang.language_name, lang.language_background, 
                        GROUP_CONCAT(DISTINCT genre.genre_name ORDER BY genre.genre_name ASC) AS genre_name,
                        IF(tbl_likes.is_like = 1, 1, 0) AS liked,
                        IF(tbl_likes.is_like = 0, 1, 0) AS disliked
                        FROM tbl_shortfilms sf
                        LEFT JOIN tbl_language lang ON sf.language_id = lang.id
                        LEFT JOIN tbl_genres genre ON FIND_IN_SET(genre.gid, sf.genre_id)
                        LEFT JOIN tbl_likes ON sf.id = tbl_likes.post_id 
                        AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'shortfilm'
                        WHERE sf.status = 1 AND sf.id = '$id'
                        GROUP BY sf.id
                        ORDER BY sf.id DESC";
                $record = getRecord($query);
                if ($record) {
                    $results[] = [
                        'id' => $record['id'],
                                    'title' => $record['movie_title'],
                                    'language_name' => $record['language_name'],
                                    'genre_name' => $record['genre_name'],
                                    'cover_image' => $record['movie_cover'],
                                    'imdb_rating'=> $record['imdb_rating'],
                                    'poster_image' => $record['movie_poster'],
                                    'description' => $record['movie_desc'],
                                    'total_time' => $record['total_time'],
                                    'director_name' => $record['director_name'],
                                    'cast_names' => $record['cast_names'],
                                    'cost_type' => 'free',
                                    'price' => '0',
                                    'maturity_rating' => $record['maturity_rating'],
                                    'release_date' => $record['release_date'],
                                    'url_type'=>$record['movie_type'],
                                    'url' => $record['movie_url'],
                                    'trailer_type' => $record['trailer_type'],
                                     'trailer_url' => $record['trailer_url'],
                                    'type' => 'shortfilm',
                                    'user_interaction' => [
                                        'liked' => $record['liked'] == 1,
                                        'disliked' => $record['disliked'] == 1
                                    ]
                    ];
                } 
                    
                break;
                case "drama":
                    
                     $query = "SELECT sf.*, 
                                        lang.language_name, lang.language_background, 
                                       
                                        IF(tbl_likes.is_like = 1, 1, 0) AS liked,
                                        IF(tbl_likes.is_like = 0, 1, 0) AS disliked
                                      FROM tbl_drama sf
                                      LEFT JOIN tbl_language lang ON sf.language_id = lang.id
                                      
                                      LEFT JOIN tbl_likes ON sf.id = tbl_likes.post_id 
                                          AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'drama'
                                      WHERE sf.status = 1 AND sf.id = '$id'
                                      GROUP BY sf.id
                                      ORDER BY sf.id DESC";
                $record = getRecord($query);
                if ($record) {
                    $results[] = [
                       'id' => $record['id'],
                                    'title' => $record['drama_title'],
                                    'language_name' => $record['language_name'],
                                    
                                    'cover_image' => $record['drama_cover'],
                                    'imdb_rating'=> $record['imdb_rating'],
                                    'poster_image' => $record['drama_poster'],
                                    'description' => $record['drama_desc'],
                                    'total_time' => $record['total_time'],
                                    'director_name' => $record['director_name'],
                                    'cast_names' => $record['cast_names'],
                                    'cost_type' => 'free',
                                    'price' => '0',
                                    'maturity_rating' => $record['maturity_rating'],
                                    'release_date' => $record['release_date'],
                                    'url_type'=>$record['drama_type'],
                                    'url' => $record['drama_url'],
                                    'trailer_type' => $record['trailer_type'],
                                     'trailer_url' => $record['trailer_url'],
                                    'type' => 'drama',
                                    'user_interaction' => [
                                        'liked' => $record['liked'] == 1,
                                        'disliked' => $record['disliked'] == 1
                                    ]
                                
                    ];
                } 
                    
                break;
                case "song":
                    
                     $query = "SELECT sf.*, 
                                        lang.language_name, lang.language_background, 
                                       
                                        IF(tbl_likes.is_like = 1, 1, 0) AS liked,
                                        IF(tbl_likes.is_like = 0, 1, 0) AS disliked
                                      FROM tbl_songs sf
                                      LEFT JOIN tbl_language lang ON sf.language_id = lang.id
                                      
                                      LEFT JOIN tbl_likes ON sf.id = tbl_likes.post_id 
                                          AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'song'
                                      WHERE sf.status = 1 AND sf.id = '$id'
                                      GROUP BY sf.id
                                      ORDER BY sf.id DESC";
                $record = getRecord($query);
                if ($record) {
                    $results[] = [
                       'id' => $record['id'],
                                    'title' => $record['song_title'],
                                    'language_name' => $record['language_name'],
                                    
                                    'cover_image' => $record['song_cover'],
                                    'imdb_rating'=> $record['imdb_rating'],
                                    'poster_image' => $record['song_poster'],
                                    'description' => $record['song_desc'],
                                    'total_time' => $record['total_time'],
                                    'director_name' => $record['director_name'],
                                    'cast_names' => $record['cast_names'],
                                    'cost_type' => 'free',
                                    'price' => '0',
                                    'maturity_rating' => $record['maturity_rating'],
                                    'release_date' => $record['release_date'],
                                    'url_type'=>$record['song_type'],
                                    'url' => $record['song_url_url'],
                                    'trailer_type' => $record['trailer_type'],
                                     'trailer_url' => $record['trailer_url'],
                                    'type' => 'song',
                                    'user_interaction' => [
                                        'liked' => $record['liked'] == 1,
                                        'disliked' => $record['disliked'] == 1
                                    ]
                                
                    ];
                } 
                    
                break;

           
        }
    }

    echo json_encode([
        'status' => true,
        'success' => 1,
        'msg' => 'Watchlist Fetched Successfully',
        'data' => $results
    ]);
    exit();
    break;
		// ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
       case "removefromWatchlist":
    $userid = intval(@$_POST['userid']);
    $post_id = intval(@$_POST['post_id']);
    $type = trim(@$_POST['type']);

    if (!$userid || !$post_id || !$type) {
        echo json_encode([
            'status' => false,
            'success' => 0,
            'msg' => 'Missing parameters'
        ]);
        exit();
    }

    if (!app_login($userid)) {
        echo json_encode([
            'status' => false,
            'success' => 0,
            'msg' => 'Invalid or inactive user'
        ]);
        exit();
    }
   $getdata=getRecord("SELECT * FROM tbl_watchlist WHERE userid = '$userid' AND post_id = '$post_id' AND type = '$type' ");
   
   if(empty($getdata))
   {
      echo json_encode([
            'status' => false,
            'success' => 0,
            'msg' => 'Invalid post_id'
        ]);
        exit(); 
   }
    // Attempt to delete the entry
    $delete = query("DELETE FROM tbl_watchlist WHERE userid = '$userid' AND post_id = '$post_id' AND type = '$type'");

    if ($delete) {
        echo json_encode([
            'status' => true,
            'success' => 1,
            'msg' => 'Removed from watchlist successfully'
        ]);
    } else {
        echo json_encode([
            'status' => false,
            'success' => 0,
            'msg' => 'Failed to remove from watchlist or item not found'
        ]);
    }

    exit();
    break;
    
    
    	// ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	
        case "addToContineWatchlist":
            
    $userid = intval(@$_POST['userid']);
    $post_id = intval(@$_POST['post_id']);
    $seconds = @$_POST['seconds'];
    $type = trim(@$_POST['type']);

    // Check login
    if (!app_login($userid)) {
        $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' => 'Invalid Userid or User Not Active');
        echo json_encode($response);
        exit();
    }

    // Validate inputs
    if (empty($post_id) || empty($type) || empty($seconds)) {
        $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' => 'Missing Required Params');
        echo json_encode($response);
        exit();
    }

    // Optional: restrict to allowed types
    $allowedTypes = ['movies', 'series', 'shows', 'shortfilm', 'drama', 'song'];
    if (!in_array($type, $allowedTypes)) {
        $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' => 'Invalid Content Type');
        echo json_encode($response);
        exit();
    }

    // Check if already in watchlist
    $check = getRecord("SELECT id FROM tbl_continue_watchlist WHERE userid = '$userid' AND post_id = '$post_id' AND type = '$type'");

    if ($check) {
        $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' => 'Already in Contine Watchlist');
        echo json_encode($response);
        exit();
    }

    // Insert into watchlist
    $insert = query("INSERT INTO tbl_continue_watchlist (userid, post_id,  type,seconds, created_at)
                     VALUES ('$userid', '$post_id', '$type', '$seconds', '$fdate')");

    if ($insert) {
        $response = array('status' => true, 'error' => 0, 'success' => 1, 'msg' => 'Added to Continue Watchlist');
    } else {
        $response = array('status' => false, 'error' => 1, 'success' => 0, 'msg' => 'Failed to add to Continue Watchlist');
    }

    echo json_encode($response);
    exit();
    break;
    
    // ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
       case "removefromContinueWatchlist":
    $userid = intval(@$_POST['userid']);
    $post_id = intval(@$_POST['post_id']);
    $type = trim(@$_POST['type']);

    if (!$userid || !$post_id || !$type) {
        echo json_encode([
            'status' => false,
            'success' => 0,
            'msg' => 'Missing parameters'
        ]);
        exit();
    }

    if (!app_login($userid)) {
        echo json_encode([
            'status' => false,
            'success' => 0,
            'msg' => 'Invalid or inactive user'
        ]);
        exit();
    }
   $getdata=getRecord("SELECT * FROM tbl_continue_watchlist WHERE userid = '$userid' AND post_id = '$post_id' AND type = '$type' ");
   
   if(empty($getdata))
   {
      echo json_encode([
            'status' => false,
            'success' => 0,
            'msg' => 'Invalid post_id'
        ]);
        exit(); 
   }
    // Attempt to delete the entry
    $delete = query("DELETE FROM tbl_continue_watchlist WHERE userid = '$userid' AND post_id = '$post_id' AND type = '$type'");

    if ($delete) {
        echo json_encode([
            'status' => true,
            'success' => 1,
            'msg' => 'Removed from Continue watchlist successfully'
        ]);
    } else {
        echo json_encode([
            'status' => false,
            'success' => 0,
            'msg' => 'Failed to remove from Continue watchlist or item not found'
        ]);
    }

    exit();
    break;

// ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        case "getContinueWatchlist":
    $userid = intval(@$_POST['userid']);

    if (!app_login($userid)) {
        echo json_encode([
            'status' => false,
            'error' => 1,
            'success' => 0,
            'msg' => 'Invalid Userid or User Not Active'
        ]);
        exit();
    }

    // Fetch user's watchlist
    $watchlistQuery = "SELECT post_id, type FROM tbl_continue_watchlist WHERE userid = '$userid' ORDER by id DESC";
    $watchlistItems = getRecords($watchlistQuery);
    
   // print_r($watchlistItems);die;
    $results = [];

    foreach ($watchlistItems as $item) {
        $id = $item['post_id'];
        $type = $item['type'];
       
        switch ($type) {
            case 'movies':
                
                 $query = "SELECT m.*, wh.seconds, lang.language_name, 
                          IF(l.is_like = 1, 1, 0) AS liked, IF(l.is_like = 0, 1, 0) AS disliked  
                          FROM tbl_movies m 
                          LEFT JOIN tbl_language lang ON m.language_id = lang.id  
                          LEFT JOIN tbl_continue_watchlist wh ON wh.post_id = m.id AND wh.userid = '$userid'
                          LEFT JOIN tbl_likes l ON m.id = l.post_id AND l.userid = '$userid' AND l.type = 'movies' 
                          WHERE m.id = '$id' AND m.status = 1";

                $records = getRecords($query);
                foreach ($records as $record) {
                    $results[] = [
                        'id' => $record['id'],
                        'title' => $record['movie_title'],
                        'language_name' => $record['language_name'],
                        'imdb_rating' => $record['imdb_rating'],
                        'genre_name' => '',
                        'seconds' => $record['seconds'],
                        'cover_image' => $record['movie_cover'],
                        'poster_image' => $record['movie_poster'],
                        'description' => $record['movie_desc'],
                        'total_time' => $record['total_time'],
                        'director_name' => $record['director_name'],
                        'cast_names' => $record['cast_names'],
                        'cost_type' => $record['movie_cost_type'],
                        'price' => '0',
                        'maturity_rating' => $record['maturity_rating'],
                        'release_date' => $record['release_date'],
                        'url_type' => $record['movie_type'],
                        'url' => $record['movie_url'],
                        'trailer_type' => $record['trailer_type'],
                        'trailer_url' => $record['trailer_url'],
                        'type' => 'movies',
                        'user_interaction' => [
                            'liked' => $record['liked'] == 1,
                            'disliked' => $record['disliked'] == 1
                        ]
                    ];
                }
                break;
            case 'rented':
                 $query = "SELECT movie.*, lang.language_name, 
                          IF(tbl_likes.is_like = 1, 1, 0) AS liked, 
                          IF(tbl_likes.is_like = 0, 1, 0) AS disliked 
                          FROM tbl_rentmovies movie
                          LEFT JOIN tbl_language lang ON movie.language_id = lang.id 
                           LEFT JOIN tbl_continue_watchlist wh ON wh.post_id = m.id AND wh.userid = '$userid'
                          LEFT JOIN tbl_likes ON movie.id = tbl_likes.post_id AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'rentedmovies' 
                          WHERE movie.status = 1 AND movie.id = '$id'";

                $movieItems = getRecords($query);
                foreach ($movieItems as $record) {
                    $buy = getRecord("SELECT purchase_dt, expire FROM tbl_buymovies WHERE movie_id = '{$record['id']}' AND userid = '$userid' ORDER BY id DESC LIMIT 1");
                    $userHaveRentAccess = false;
                    $rentValidityDate = null;

                    if ($buy && $buy['expire'] == 0) {
                        $validTill = date('Y-m-d', strtotime($buy['purchase_dt'] . ' + ' . intval($record['movie_validation']) . ' days'));
                        if (strtotime($validTill) >= time()) {
                            $userHaveRentAccess = true;
                            $rentValidityDate = $validTill;
                        }
                    }

                    $results[] = [
                        'id' => $record['id'],
                        'title' => $record['movie_title'],
                        'language_name' => $record['language_name'],
                        'imdb_rating' => $record['imdb_rating'],
                        'genre_name' => '',
                        'cover_image' => $record['movie_cover'],
                        'poster_image' => $record['movie_poster'],
                        'description' => $record['movie_desc'],
                        'total_time' => $record['total_time'],
                        'director_name' => $record['director_name'],
                        'cast_names' => $record['cast_names'],
                        'cost_type' => 'rent',
                        'price' => $record['movie_price'],
                        'is_rented' => true,
                        'user_have_rent_access' => $userHaveRentAccess,
                        'rent_validity_date' => $rentValidityDate,
                        'maturity_rating' => $record['maturity_rating'],
                        'release_date' => $record['release_date'],
                        'url_type' => $record['movie_type'],
                        'url' => $record['movie_url'],
                        'trailer_type' => $record['trailer_type'],
                        'trailer_url' => $record['trailer_url'],
                        'type' => 'movies',
                        'user_interaction' => [
                            'liked' => $record['liked'] == 1,
                            'disliked' => $record['disliked'] == 1
                        ]
                    ];
                }
            break;

            case 'series':
                $query = "SELECT e.*, s.*, lang.language_name,wh.seconds 
                          FROM tbl_episode e  
                          LEFT JOIN tbl_series s ON e.series_id = s.id  
                          LEFT JOIN tbl_language lang ON s.language_id = lang.id  
                           LEFT JOIN tbl_continue_watchlist wh ON wh.post_id = e.id AND wh.userid = '$userid' and wh.type='series'
                          WHERE e.id = '$id' AND e.status = 1 ";

                $seriesItems = getRecords($query);
                foreach ($seriesItems as $record) {
                            $seriesId = $record['series_id'];
                            $seasonQuery = "SELECT `id`, `series_id`, `season_name`, `status` FROM `tbl_season` WHERE `series_id` = '$seriesId'";
                            $seasons = getRecords($seasonQuery);
                    $results[] = [
                        'id' => $record['id'],
                        'title' => $record['series_name'] ?? '',
                        'language_name' => $record['language_name'],
                        'genre_name' => '',
                        'cover_image' => $record['series_cover'],
                        'poster_image' => $record['series_poster'],
                        'description' => $record['series_desc'],
                        'total_time' => $record['total_time'],
                        'director_name' => $record['director_name'],
                        'cast_names' => $record['cast_names'],
                        'cost_type' => $record['series_cost_type'] ?? 'free',
                        'price' => '0',
                        'maturity_rating' => $record['maturity_rating'],
                        'release_date' => $record['release_date'],
                        'episode_title' => $record['episode_title'],
                        'series_id' => $record['series_id'],
                        'selected_season_id' => $record['season_id'],
                        'url' => $record['episode_url'],
                        'url_type' => $record['episode_type'],
                        'seconds' => $record['seconds'],
                        'trailer_type' => $record['trailer_type'],
                        'trailer_url' => $record['trailer_url'],
                        'type' => 'series',
                        'user_interaction' => [
                            'liked' => false,
                            'disliked' => false
                        ],
                        'seasons' => $seasons
                    ];
                }
                break;

            case 'shows':
                 $query = "SELECT e.*, s.*, lang.language_name,wh.seconds 
                          FROM tbl_tv_episode e  
                          LEFT JOIN tbl_shows s ON e.shows_id = s.id  
                          LEFT JOIN tbl_language lang ON s.language_id = lang.id  
                           LEFT JOIN tbl_continue_watchlist wh ON wh.post_id = e.id AND wh.userid = '$userid' and wh.type='shows'
                          WHERE e.id = '$id' AND e.status = 1 ";

                $seriesItems = getRecords($query);
                foreach ($seriesItems as $record) {
                    
                    $showsId = $record['shows_id'];
                            $seasonQuery = "SELECT * FROM `tbl_tv_season` WHERE `shows_id` = '$showsId'";
                            $seasons = getRecords($seasonQuery);
                    $results[] = [
                        'id' => $record['id'],
                        'title' => $record['shows_name'] ?? '',
                        'language_name' => $record['language_name'],
                        'genre_name' => '',
                        'cover_image' => $record['shows_cover'],
                        'poster_image' => $record['shows_poster'],
                        'description' => $record['shows_desc'],
                        'total_time' => $record['total_time'],
                        'director_name' => $record['director_name'],
                        'cast_names' => $record['cast_names'],
                        'cost_type' =>  'free',
                        'price' => '0',
                        'maturity_rating' => $record['maturity_rating'],
                        'release_date' => $record['release_date'],
                        'episode_title' => $record['episode_title'],
                        'selected_season_id' => $record['season_id'],
                        'url' => $record['episode_url'],
                        'url_type' => $record['episode_type'],
                        'seconds' => $record['seconds'],
                        'trailer_type' => $record['trailer_type'],
                        'trailer_url' => $record['trailer_url'],
                        'type' => 'shows',
                        'user_interaction' => [
                            'liked' => false,
                            'disliked' => false
                        ],
                        'seasons' => $seasons
                    ];
                }
                break;
                case  "shortfilm" :
                   $query = "SELECT sf.*, lang.language_name, lang.language_background,wh.seconds GROUP_CONCAT(DISTINCT genre.genre_name ORDER BY genre.genre_name ASC) AS genre_name,IF(tbl_likes.is_like = 1, 1, 0) AS liked,
                        IF(tbl_likes.is_like = 0, 1, 0) AS disliked FROM tbl_shortfilms sf LEFT JOIN tbl_language lang ON sf.language_id = lang.id  LEFT JOIN tbl_genres genre ON FIND_IN_SET(genre.gid, sf.genre_id) LEFT 
                        JOIN tbl_likes ON sf.id = tbl_likes.post_id AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'shortfilm' 
                        LEFT JOIN tbl_continue_watchlist wh on sf.id=wh.post_id and wh.type='shortfilm' and wh.userid='$userid'
                        WHERE sf.status = 1 AND sf.id = '$id' GROUP BY sf.id ORDER BY sf.id DESC";

                $records = getRecords($query);
                foreach ($records as $record) {
                    $results[] = [
                        'id' => $record['id'],
                        'title' => $record['movie_title'],
                        'language_name' => $record['language_name'],
                        'imdb_rating' => $record['imdb_rating'],
                        'genre_name' => '',
                        'seconds' => $record['seconds'],
                        'cover_image' => $record['movie_cover'],
                        'poster_image' => $record['movie_poster'],
                        'description' => $record['movie_desc'],
                        'total_time' => $record['total_time'],
                        'director_name' => $record['director_name'],
                        'cast_names' => $record['cast_names'],
                        'cost_type' => 'free',
                        'price' => '0',
                        'maturity_rating' => $record['maturity_rating'],
                        'release_date' => $record['release_date'],
                        'url_type' => $record['movie_type'],
                        'url' => $record['movie_url'],
                        'trailer_type' => $record['trailer_type'],
                        'trailer_url' => $record['trailer_url'],
                        'type' => 'shortfilm',
                        'user_interaction' => [
                            'liked' => $record['liked'] == 1,
                            'disliked' => $record['disliked'] == 1
                        ]
                    ];
                }
                    
                break;
                case "drama":
                    $query = "SELECT sf.*, lang.language_name, lang.language_background,wh.seconds, IF(tbl_likes.is_like = 1, 1, 0) AS liked,IF(tbl_likes.is_like = 0, 1, 0) AS disliked FROM tbl_drama sf LEFT JOIN tbl_language lang ON sf.language_id = lang.id LEFT JOIN tbl_likes ON sf.id = tbl_likes.post_id  AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'drama'
                 
                  LEFT JOIN tbl_continue_watchlist wh ON wh.post_id = sf.id AND wh.userid = '$userid' and wh.type='drama'
                 
                 WHERE sf.status = 1 AND sf.id = '$id' GROUP BY sf.id ORDER BY sf.id DESC";

                $records = getRecords($query);
                foreach ($records as $record) {
                    $results[] = [
                        'id' => $record['id'],
                        'title' => $record['drama_title'],
                        'language_name' => $record['language_name'],
                        'imdb_rating' => $record['imdb_rating'],
                        'genre_name' => '',
                        'seconds' => $record['seconds'],
                        'cover_image' => $record['drama_cover'],
                        'poster_image' => $record['drama_poster'],
                        'description' => $record['drama_desc'],
                        'total_time' => $record['total_time'],
                        'director_name' => $record['director_name'],
                        'cast_names' => $record['cast_names'],
                        'cost_type' => 'free',
                        'price' => '0',
                        'maturity_rating' => $record['maturity_rating'],
                        'release_date' => $record['release_date'],
                        'url_type' => $record['drama_type'],
                        'url' => $record['drama_url'],
                        'trailer_type' => $record['trailer_type'],
                        'trailer_url' => $record['trailer_url'],
                        'type' => 'drama',
                        'user_interaction' => [
                            'liked' => $record['liked'] == 1,
                            'disliked' => $record['disliked'] == 1
                        ]
                    ];
                }
                break;
                case "song":
                                   $query = "SELECT sf.*, lang.language_name, lang.language_background,wh.seconds, IF(tbl_likes.is_like = 1, 1, 0) AS liked,IF(tbl_likes.is_like = 0, 1, 0) AS disliked FROM tbl_songs sf LEFT JOIN tbl_language lang ON sf.language_id = lang.id LEFT JOIN tbl_likes ON sf.id = tbl_likes.post_id AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'song'
                LEFT JOIN tbl_continue_watchlist wh ON wh.post_id = sf.id AND wh.userid = '$userid' and wh.type='song'
                
                WHERE sf.status = 1 AND sf.id = '$id' GROUP BY sf.id ORDER BY sf.id DESC";

                $records = getRecords($query);
                foreach ($records as $record) {
                    $results[] = [
                        'id' => $record['id'],
                        'title' => $record['song_title'],
                        'language_name' => $record['language_name'],
                        'imdb_rating' => $record['imdb_rating'],
                        'genre_name' => '',
                        'seconds' => $record['seconds'],
                        'cover_image' => $record['song_cover'],
                        'poster_image' => $record['song_poster'],
                        'description' => $record['song_desc'],
                        'total_time' => $record['total_time'],
                        'director_name' => $record['director_name'],
                        'cast_names' => $record['cast_names'],
                        'cost_type' => 'free',
                        'price' => '0',
                        'maturity_rating' => $record['maturity_rating'],
                        'release_date' => $record['release_date'],
                        'url_type' => $record['song_type'],
                        'url' => $record['song_url'],
                        'trailer_type' => $record['trailer_type'],
                        'trailer_url' => $record['trailer_url'],
                        'type' => 'song',
                        'user_interaction' => [
                            'liked' => $record['liked'] == 1,
                            'disliked' => $record['disliked'] == 1
                        ]
                    ];
                }
                    
                break;

           
        }
    }

    echo json_encode([
        'status' => true,
        'success' => 1,
        'msg' => 'Continue Watchlist Fetched Successfully',
        'data' => $results
    ]);
    exit();
    break;


	// ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

        case "viewAllData":
    $userid = intval(@$_POST['userid']);

    if (!app_login($userid)) {
        echo json_encode([
            'status' => false,
            'error' => 1,
            'success' => 0,
            'msg' => 'Invalid Userid or User Not Active'
        ]);
        exit();
    }

    $type = @$_POST['type'];
    $items = []; // ✅ Initialize array

    if ($type) {
        if ($type == 'movies') {
            $query = "SELECT movie.*, lang.language_name, lang.language_background,
                            IF(tbl_likes.is_like = 1, 1, 0) AS liked,
                            IF(tbl_likes.is_like = 0, 1, 0) AS disliked
                      FROM tbl_movies movie
                      LEFT JOIN tbl_language lang ON movie.language_id = lang.id
                      LEFT JOIN tbl_likes ON movie.id = tbl_likes.post_id 
                          AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'movies'
                      WHERE movie.status = 1 
                      ORDER BY movie.id DESC";
            $movieItems = getRecords($query);

            foreach ($movieItems as $item) {
                $items[] = [
                    'id' => $item['id'],
                    'title' => $item['movie_title'],
                    'language_name' => $item['language_name'],
                    'imdb_rating'=> $item['imdb_rating'],
                    'genre_name' => '', // optional, can be filled
                    'cover_image' => $item['movie_cover'],
                    'poster_image' => $item['movie_poster'],
                    'description' => $item['movie_desc'],
                    'total_time' => $item['total_time'],
                    'director_name' => $item['director_name'],
                    'cast_names' => $item['cast_names'],
                    'cost_type' => $item['movie_cost_type'],
                    'price' => '0',
                    'maturity_rating' => $item['maturity_rating'],
                    'release_date' => $item['release_date'],
                    'url_type' => $item['movie_type'],
                    'url' => $item['movie_url'],
                    'trailer_type' => $item['trailer_type'],
                    'trailer_url' => $item['trailer_url'],
                    'type' => 'movies',
                    'user_interaction' => [
                        'liked' => $item['liked'] == 1,
                        'disliked' => $item['disliked'] == 1
                    ]
                ];
            }

        }
        elseif ($type == 'rented'){
           $query = "SELECT movie.*, lang.language_name, lang.language_background,
                            IF(tbl_likes.is_like = 1, 1, 0) AS liked,
                            IF(tbl_likes.is_like = 0, 1, 0) AS disliked
                          FROM tbl_rentmovies movie
                          LEFT JOIN tbl_language lang ON movie.language_id = lang.id
                          LEFT JOIN tbl_likes ON movie.id = tbl_likes.post_id 
                              AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'rentedmovies'
                          WHERE movie.status = 1 
                          ORDER BY movie.id DESC";
                $movieItems = getRecords($query);

                foreach ($movieItems as $item) {
                   
                      $buy = getRecord(" SELECT purchase_dt, expire  FROM tbl_buymovies  WHERE movie_id = '{$item['id']}'    AND userid = '$userid'  ORDER BY id DESC  LIMIT 1 ");
                      $userHaveRentAccess = false;
                      $rentValidityDate = null;
                      if ($buy && $buy['expire'] == 0) {
            
                        $validTill = date('Y-m-d', strtotime($buy['purchase_dt'] . ' + ' . intval($item['movie_validation']) . ' days'));
            
                        if (strtotime($validTill) >= time()) {
                            $userHaveRentAccess = true;
                            $rentValidityDate = $validTill;
                        } else {
                            $userHaveRentAccess = false;
                            $rentValidityDate = null;
            
                            
                        }
                    }

                      
                      
                    $items[] = [
                        'id' => $item['id'],
                        'title' => $item['movie_title'],
                        'language_name' => $item['language_name'],
                        'imdb_rating'=> $item['imdb_rating'],
                        'genre_name' => '', // optional, or fetch genre if needed
                        'cover_image' => $item['movie_cover'],
                        'poster_image' => $item['movie_poster'],
                        'description' => $item['movie_desc'],
                        'total_time' => $item['total_time'],
                        'director_name' => $item['director_name'],
                        'cast_names' => $item['cast_names'],
                       'cost_type' => $item['movie_cost_type'],
                        'price' => '0',
                        'is_rented'=>true,
                        'user_have_rent_access'=>$userHaveRentAccess,
                        'rent_validity_date'=>$rentValidityDate,
                        'maturity_rating' => $item['maturity_rating'],
                        'release_date' => $item['release_date'],
                        'url_type'=>$item['movie_type'],
                        'url' => $item['movie_url'],
                        'trailer_type' => $item['trailer_type'],
                         'trailer_url' => $item['trailer_url'],
                        'type' => 'movies',
                        'user_interaction' => [
                            'liked' => $item['liked'] == 1,
                            'disliked' => $item['disliked'] == 1
                        ]
                    ];
                    
                    unset($item['liked'], $item['disliked']);
                } 
        }
        
        elseif ($type == 'series') {
        
             
              $query = "SELECT s.*, 
                            lang.language_name, lang.language_background, 
                            GROUP_CONCAT(DISTINCT genre.genre_name ORDER BY genre.genre_name ASC) AS genre_name,
                            IF(tbl_likes.is_like = 1, 1, 0) AS liked,
                            IF(tbl_likes.is_like = 0, 1, 0) AS disliked
                          FROM tbl_series s
                          LEFT JOIN tbl_language lang ON s.language_id = lang.id
                          LEFT JOIN tbl_genres genre ON FIND_IN_SET(genre.gid, s.genre_id)
                          LEFT JOIN tbl_likes ON s.id = tbl_likes.post_id 
                              AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'series'
                          WHERE s.status = 1 
                          GROUP BY s.id
                          ORDER BY s.id DESC";
            
                $seriesItems = getRecords($query);
            
                foreach ($seriesItems as $item) {
                    // Fetch seasons for the current series
                    $seriesId = $item['id'];
                    $seasonQuery = "SELECT `id`, `series_id`, `season_name`, `status` FROM `tbl_season` WHERE `series_id` = '$seriesId'";
                    $seasons = getRecords($seasonQuery); // This should return an array of seasons
            
                    $items[] = [
                        'id' => $item['id'],
                        'title' => $item['series_name'],
                        'language_name' => $item['language_name'],
                        'genre_name' => $item['genre_name'],
                        'cover_image' => $item['series_cover'],
                        'imdb_rating'=> $item['imdb_rating'],
                        'poster_image' => $item['series_poster'],
                        'description' => $item['series_desc'],
                        'total_time' => $item['total_time'],
                        'director_name' => $item['director_name'],
                        'cast_names' => $item['cast_names'],
                        'cost_type' => $item['series_cost_type'],
                        'price' => '0',
                        'maturity_rating' => $item['maturity_rating'],
                        'release_date' => $item['release_date'],
                        'trailer_url' => $item['trailer_url'],
                        'trailer_type' => $item['trailer_type'],
                        'url' => $item['series_url'],
                        'type' => 'series',
                        'user_interaction' => [
                            'liked' => $item['liked'] == 1,
                            'disliked' => $item['disliked'] == 1
                        ],
                        'seasons' => $seasons // 👈 Add seasons here
                    ];
                    unset($item['liked'], $item['disliked']);
                }
        
        }
        elseif ($type == 'shows') {
            
            $query = "SELECT s.*, 
                            lang.language_name, lang.language_background, 
                            GROUP_CONCAT(DISTINCT genre.genre_name ORDER BY genre.genre_name ASC) AS genre_name,
                            IF(tbl_likes.is_like = 1, 1, 0) AS liked,
                            IF(tbl_likes.is_like = 0, 1, 0) AS disliked
                          FROM tbl_shows s
                          LEFT JOIN tbl_language lang ON s.language_id = lang.id
                          LEFT JOIN tbl_genres genre ON FIND_IN_SET(genre.gid, s.genre_id)
                          LEFT JOIN tbl_likes ON s.id = tbl_likes.post_id 
                              AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'shows'
                          WHERE s.status = 1 
                          GROUP BY s.id
                          ORDER BY s.id DESC";
            
                $showsItems = getRecords($query);
            
                foreach ($showsItems as $item) {
                    // Fetch seasons for the current series
                    $showsId = $item['id'];
                    $seasonQuery = "SELECT * FROM `tbl_tv_season` WHERE `shows_id` = '$showsId'";
                    $seasons = getRecords($seasonQuery); // This should return an array of seasons
            
                    $items[] = [
                        'id' => $item['id'],
                        'title' => $item['shows_name'],
                        'language_name' => $item['language_name'],
                        'genre_name' => $item['genre_name'],
                        'cover_image' => $item['shows_cover'],
                        'imdb_rating'=> $item['imdb_rating'],
                        'poster_image' => $item['shows_poster'],
                        'description' => $item['shows_desc'],
                        'total_time' => $item['total_time'],
                        'director_name' => $item['director_name'],
                        'cast_names' => $item['cast_names'],
                        'cost_type' => 'free',
                        'price' => '0',
                        'maturity_rating' => $item['maturity_rating'],
                        'release_date' => $item['release_date'],
                        'trailer_url' => $item['trailer_url'],
                        'trailer_type' => $item['trailer_type'],
                        'url' => $item['shows_url'],
                        'type' => 'shows',
                        'user_interaction' => [
                            'liked' => $item['liked'] == 1,
                            'disliked' => $item['disliked'] == 1
                        ],
                        'seasons' => $seasons // 👈 Add seasons here
                    ];
                    unset($item['liked'], $item['disliked']);
                }
            
            
            
            
            
        
            
        }elseif($type == 'shortfilms'){
            
        $query = "SELECT sf.*, 
                                        lang.language_name, lang.language_background, 
                                        GROUP_CONCAT(DISTINCT genre.genre_name ORDER BY genre.genre_name ASC) AS genre_name,
                                        IF(tbl_likes.is_like = 1, 1, 0) AS liked,
                                        IF(tbl_likes.is_like = 0, 1, 0) AS disliked
                                      FROM tbl_shortfilms sf
                                      LEFT JOIN tbl_language lang ON sf.language_id = lang.id
                                      LEFT JOIN tbl_genres genre ON FIND_IN_SET(genre.gid, sf.genre_id)
                                      LEFT JOIN tbl_likes ON sf.id = tbl_likes.post_id 
                                          AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'shortfilm'
                                      WHERE sf.status = 1 
                                      GROUP BY sf.id
                                      ORDER BY sf.id DESC";
                            $shortfilmItems = getRecords($query);
            
                            foreach ($shortfilmItems as $item) {
                                $items[] = [
                                    'id' => $item['id'],
                                    'title' => $item['movie_title'],
                                    'language_name' => $item['language_name'],
                                    'genre_name' => $item['genre_name'],
                                    'cover_image' => $item['movie_cover'],
                                    'imdb_rating'=> $item['imdb_rating'],
                                    'poster_image' => $item['movie_poster'],
                                    'description' => $item['movie_desc'],
                                    'total_time' => $item['total_time'],
                                    'director_name' => $item['director_name'],
                                    'cast_names' => $item['cast_names'],
                                    'cost_type' => 'free',
                                    'price' => '0',
                                    'maturity_rating' => $item['maturity_rating'],
                                    'release_date' => $item['release_date'],
                                    'url_type'=>$item['movie_type'],
                                    'url' => $item['movie_url'],
                                    'trailer_type' => $item['trailer_type'],
                                     'trailer_url' => $item['trailer_url'],
                                    'type' => 'shortfilm',
                                    'user_interaction' => [
                                        'liked' => $item['liked'] == 1,
                                        'disliked' => $item['disliked'] == 1
                                    ]
                                ];
                                unset($item['liked'], $item['disliked']);
                            }
        }
        elseif($type =='songs'){
            
            $query = "SELECT sf.*, 
                                        lang.language_name, lang.language_background, 
                                       
                                        IF(tbl_likes.is_like = 1, 1, 0) AS liked,
                                        IF(tbl_likes.is_like = 0, 1, 0) AS disliked
                                      FROM tbl_songs sf
                                      LEFT JOIN tbl_language lang ON sf.language_id = lang.id
                                      
                                      LEFT JOIN tbl_likes ON sf.id = tbl_likes.post_id 
                                          AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'song'
                                      WHERE sf.status = 1
                                      GROUP BY sf.id
                                      ORDER BY sf.id DESC";
                            $shortfilmItems = getRecords($query);
            
                            foreach ($shortfilmItems as $item) {
                                $items[] = [
                                    'id' => $item['id'],
                                    'title' => $item['song_title'],
                                    'language_name' => $item['language_name'],
                                    
                                    'cover_image' => $item['song_cover'],
                                    'imdb_rating'=> $item['imdb_rating'],
                                    'poster_image' => $item['song_poster'],
                                    'description' => $item['song_desc'],
                                    'total_time' => $item['total_time'],
                                    'director_name' => $item['director_name'],
                                    'cast_names' => $item['cast_names'],
                                    'cost_type' => 'free',
                                    'price' => '0',
                                    'maturity_rating' => $item['maturity_rating'],
                                    'release_date' => $item['release_date'],
                                    'url_type'=>$item['song_type'],
                                    'url' => $item['song_url'],
                                    'trailer_type' => $item['trailer_type'],
                                     'trailer_url' => $item['trailer_url'],
                                    'type' => 'songs',
                                    'user_interaction' => [
                                        'liked' => $item['liked'] == 1,
                                        'disliked' => $item['disliked'] == 1
                                    ]
                                ];
                                unset($item['liked'], $item['disliked']);
                            }
            
        }
        elseif($type =='drama'){
             $query = "SELECT sf.*, 
                                        lang.language_name, lang.language_background, 
                                       
                                        IF(tbl_likes.is_like = 1, 1, 0) AS liked,
                                        IF(tbl_likes.is_like = 0, 1, 0) AS disliked
                                      FROM tbl_drama sf
                                      LEFT JOIN tbl_language lang ON sf.language_id = lang.id
                                      
                                      LEFT JOIN tbl_likes ON sf.id = tbl_likes.post_id 
                                          AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'drama'
                                      WHERE sf.status = 1 
                                      GROUP BY sf.id
                                      ORDER BY sf.id DESC";
                            $shortfilmItems = getRecords($query);
            
                            foreach ($shortfilmItems as $item) {
                                $items[] = [
                                    'id' => $item['id'],
                                    'title' => $item['drama_title'],
                                    'language_name' => $item['language_name'],
                                    
                                    'cover_image' => $item['drama_cover'],
                                    'imdb_rating'=> $item['imdb_rating'],
                                    'poster_image' => $item['drama_poster'],
                                    'description' => $item['drama_desc'],
                                    'total_time' => $item['total_time'],
                                    'director_name' => $item['director_name'],
                                    'cast_names' => $item['cast_names'],
                                    'cost_type' => 'free',
                                    'price' => '0',
                                    'maturity_rating' => $item['maturity_rating'],
                                    'release_date' => $item['release_date'],
                                    'url_type'=>$item['drama_type'],
                                    'url' => $item['drama_url'],
                                    'trailer_type' => $item['trailer_type'],
                                     'trailer_url' => $item['trailer_url'],
                                    'type' => 'drama',
                                    'user_interaction' => [
                                        'liked' => $item['liked'] == 1,
                                        'disliked' => $item['disliked'] == 1
                                    ]
                                ];
                                unset($item['liked'], $item['disliked']);
                            }
            
        }
        else {
            echo json_encode([
                'status' => false,
                'error' => 1,
                'success' => 0,
                'msg' => 'Invalid type'
            ]);
            exit();
        }

        // ✅ Final successful response
        $response = [
            'status' => true,
            'error' => 0,
            'success' => 1,
            'msg' => 'Data fetched successfully',
            'data' => $items
        ];
        echo json_encode($response);
        exit();

    } else {
        echo json_encode([
            'status' => false,
            'error' => 1,
            'success' => 0,
            'msg' => 'Missing required params'
        ]);
        exit();
    }

    break;

	// ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        case "ex" :
   
   
        echo json_encode($response);
        exit();   
		break;	
	// -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	
	default :
		echo "Please enter Function name";
}
















