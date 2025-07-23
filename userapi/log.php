<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
date_default_timezone_set('Asia/Kolkata');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");
header('Content-Type: application/json; charset=utf-8');
define("ENCRYPTION_KEY", "!@#$%^&*");
$GLOBALS['username'] = "u293792719_sensewheel";
$GLOBALS['db_name'] = "u293792719_sensewheel";
$GLOBALS['pass'] = "senseWheel@123$$";
$GLOBALS['firebase_server_key'] = "";
$GLOBALS['file_name'] = "appsync.txt";

$date = date('Y-m-d');
$time = date('H:i');
$full_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$img_append_url = "";


 $conn = mysqli_connect('localhost', $GLOBALS['username'], $GLOBALS['pass'], $GLOBALS['db_name']);
 $conn->query("SET session TIME_ZONE = '+05:30'");
function query($query)
{
    global $conn;
    return $conn->query($query);
}

function getRecords($query)
{
    $records = [];
    $result = query($query);

    while ($record = $result->fetch_assoc())
        $records[] = $record;

    return $records;
}

function getRecord($query)
{   
    $result = getRecords($query);

    if (count($result))
        return $result[0];

    return null;
}
function getRecordField($query)
    {
        $result = query($query);
        if ($row = mysqli_fetch_row($result))
            return $row[0];
        else
            return null;
    }

function last_insert_id()
{
    global $conn;
    return $conn->insert_id;
}

function escape_string($str)
{
    global $conn;
    return $conn->real_escape_string($str);
}


function getSearchMovies($search, $userid)
    {
        $response_movies = getRecords("select m.* ,m.movie_type as url_type  ,m.movie_cost_type as cost_type,  lang.`language_name`, IF(tbl_likes.`is_like` = 1, 1, 0) AS liked,IF(tbl_likes.`is_like` = 0, 1, 0) AS disliked from tbl_movies m   LEFT JOIN tbl_language lang ON m.language_id = lang.id  
                                               LEFT JOIN tbl_likes tbl_likes ON m.id = tbl_likes.post_id  AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'movie' where m.movie_title like '%$search%' and m.status=1 ");
               if(sizeof($response_movies) > 0){
                    foreach ($response_movies as &$movie) {
                        $genreIds = explode(',', $movie['genre_id']);
                        $genreNames = [];
                         if (!empty($genreIds)) {
                                $genreIdStr = implode(',', array_map('intval', $genreIds)); // Ensure integers for safety
                                $genreQuery = "SELECT genre_name FROM tbl_genres WHERE gid IN ($genreIdStr)";
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
                        unset($movie['liked'], $movie['disliked']);
                         
                    }
               }
               
               return  $response_movies;
    }
    
    
    
    
    function getSearchSeries($search, $userid)
    {
       $response_series = getRecords("select s.*,s.series_cost_type as cost_type,lang.`language_name`, IF(tbl_likes.`is_like` = 1, 1, 0) AS liked,IF(tbl_likes.`is_like` = 0, 1, 0) AS disliked from tbl_series s LEFT JOIN  tbl_language lang ON s.`language_id` = lang.`id` LEFT JOIN tbl_likes tbl_likes ON s.id = tbl_likes.post_id  AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'series' where s.series_name like '%$search%' and s.status=1 ");
               if(sizeof($response_series) > 0){
                  foreach($response_series as $key => $seriesData){
                      $genreIds = explode(',', $seriesData['genre_id']);
                        $genreNames = [];
                         if (!empty($genreIds)) {
                                $genreIdStr = implode(',', array_map('intval', $genreIds)); // Ensure integers for safety
                                $genreQuery = "SELECT genre_name FROM tbl_genres WHERE gid IN ($genreIdStr)";
                                $genres = getRecords($genreQuery);
                                 foreach ($genres as $genre) {
                                    $genreNames[] = $genre['genre_name'];
                                 }
                         }
                         $response_series[$key]['genres'] = implode(',', $genreNames);
                           $response_series[$key]['user_interaction'] = [
                             'liked' => $seriesData['liked'] == 1, // Convert to boolean
                            'disliked' => $seriesData['disliked'] == 1 // Convert to boolean
                        ];
                        unset($seriesData['liked'], $seriesData['disliked']);
                        $datas= getRecords("SELECT * FROM `tbl_season` where series_id like '%{$seriesData['id']}%' and status = 1");
                         $response_series[$key]['total_seasons'] = sizeof($datas);
		             $response_series[$key]['seasons'] = $datas;
                      $Data  = getRecords("select * from tbl_episode where series_id like '%{$seriesData['id']}%' and status = 1");
                      $response_series[$key]['total_episode'] = sizeof($Data);
		             $response_series[$key]['episode_data'] = $Data;
                  }
               }
               
               return  $response_series;
    }
    
    
    
    
    function getSearchshows($search, $userid)
    {
      $response_shows = getRecords("select s.*,lang.`language_name`,'free' AS cost_type, IF(tbl_likes.`is_like` = 1, 1, 0) AS liked,IF(tbl_likes.`is_like` = 0, 1, 0) AS disliked from tbl_shows s  LEFT JOIN  tbl_language lang ON s.`language_id` = lang.`id` LEFT JOIN tbl_likes tbl_likes ON s.id = tbl_likes.post_id  AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'shows'  where s.shows_name like '%$search%' and s.status=1 ");
                if(sizeof($response_shows) > 0){
                  foreach($response_shows as $key => $showsData){
                      $genreIds = explode(',', $showsData['genre_id']);
                        $genreNames = [];
                         if (!empty($genreIds)) {
                                $genreIdStr = implode(',', array_map('intval', $genreIds)); // Ensure integers for safety
                                $genreQuery = "SELECT genre_name FROM tbl_genres WHERE gid IN ($genreIdStr)";
                                $genres = getRecords($genreQuery);
                                 foreach ($genres as $genre) {
                                    $genreNames[] = $genre['genre_name'];
                                 }
                         }
                          $response_shows[$key]['genres'] = implode(',', $genreNames);
                          $response_shows[$key]['user_interaction'] = [
                             'liked' => $showsData['liked'] == 1, // Convert to boolean
                            'disliked' => $showsData['disliked'] == 1 // Convert to boolean
                        ];
                        unset($showsData['liked'], $showsData['disliked']);
                        
                        $datas= getRecords("SELECT * FROM `tbl_tv_season` where shows_id like '%{$showsData['id']}%' and status = 1");
                         $response_shows[$key]['total_seasons'] = sizeof($datas);
		             $response_shows[$key]['seasons'] = $datas;
                      $sData  = getRecords("select * from tbl_tv_episode where shows_id like '%{$showsData['id']}%' and status = 1");
                      $response_shows[$key]['total_episode'] = sizeof($sData);
		             $response_shows[$key]['episode'] = $sData;
                  }
               }
               
               return  $response_shows;
    }
    
    
    
    function getSearchshortfilms($search, $userid)
    {
      $response_shortfilms = getRecords("select s.*, s.movie_type as url_type  ,'free' AS cost_type,lang.`language_name`, IF(tbl_likes.`is_like` = 1, 1, 0) AS liked,IF(tbl_likes.`is_like` = 0, 1, 0) AS disliked from tbl_shortfilms s  LEFT JOIN  tbl_language lang ON s.`language_id` = lang.`id` LEFT JOIN tbl_likes tbl_likes ON s.id = tbl_likes.post_id  AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'shortfilm' where s.movie_title like '%$search%' and s.status=1 ");
               if(sizeof($response_shortfilms) > 0){
                    foreach ($response_shortfilms as &$movie) {
                        $genreIds = explode(',', $movie['genre_id']);
                        $genreNames = [];
                         if (!empty($genreIds)) {
                                $genreIdStr = implode(',', array_map('intval', $genreIds)); // Ensure integers for safety
                                $genreQuery = "SELECT genre_name FROM tbl_genres WHERE gid IN ($genreIdStr)";
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
                        unset($movie['liked'], $movie['disliked']);
                         
                    }
               }
               
               return  $response_shortfilms;
    }
    
    
    function getSearchdrama($search, $userid)
    {
      $response_drama = getRecords("select d.*,d.drama_type as url_type,'free' AS cost_type,lang.`language_name`, IF(tbl_likes.`is_like` = 1, 1, 0) AS liked,IF(tbl_likes.`is_like` = 0, 1, 0) AS disliked from tbl_drama d LEFT JOIN  tbl_language lang ON d.`language_id` = lang.`id`  LEFT JOIN tbl_likes tbl_likes ON d.id = tbl_likes.post_id  AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'drama'  where d.drama_title like '%$search%' and d.status=1 ");
               if(sizeof($response_drama) > 0){
                    foreach ($response_drama as &$drama) {
                         $drama['user_interaction'] = [
                             'liked' => $drama['liked'] == 1, // Convert to boolean
                            'disliked' => $drama['disliked'] == 1 // Convert to boolean
                        ];
                        unset($drama['liked'], $drama['disliked']);
                    }
               }
               
               return  $response_drama;
    }
    
    
    
    
    function getSearchsongs($search, $userid)
    {
      $response_songs = getRecords("select  s.*,s.song_type as url_type,lang.`language_name`, 'free' AS cost_type,IF(tbl_likes.`is_like` = 1, 1, 0) AS liked,IF(tbl_likes.`is_like` = 0, 1, 0) AS disliked from tbl_songs s LEFT JOIN  tbl_language lang ON s.`language_id` = lang.`id` LEFT JOIN tbl_likes tbl_likes ON s.id = tbl_likes.post_id  AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'song' where s.song_title like '%$search%' and s.status=1 ");
                if(sizeof($response_songs) > 0){
                    foreach ($response_songs as &$song) {
                         $song['user_interaction'] = [
                             'liked' => $song['liked'] == 1, // Convert to boolean
                            'disliked' => $song['disliked'] == 1 // Convert to boolean
                        ];
                        unset($song['liked'], $song['disliked']);
                    }
               }
               
               return  $response_songs;
    }
    
    
    
     function getSearchevents($search, $userid)
    {
      $response_events = getRecords("select e.*,e.event_type as url_type,lang.`language_name`, 'free' AS cost_type,IF(tbl_likes.`is_like` = 1, 1, 0) AS liked,IF(tbl_likes.`is_like` = 0, 1, 0) AS disliked from tbl_events e  LEFT JOIN  tbl_language lang ON e.`language_id` = lang.`id`  LEFT JOIN tbl_likes tbl_likes ON e.id = tbl_likes.post_id  AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'event'  where e.event_title like '%$search%' and e.status=1 ");
               if(sizeof($response_events) > 0){
                    foreach ($response_events as &$events) {
                         $events['user_interaction'] = [
                             'liked' => $events['liked'] == 1, // Convert to boolean
                            'disliked' => $events['disliked'] == 1 // Convert to boolean
                        ];
                        unset($events['liked'], $events['disliked']);
                    }
               }
               
               return  $response_events;
    }
    
    function getSearchliveTv($search, $userid){
        $response_livetv=getRecords("SELECT c.*, IF(tbl_likes.`is_like` = 1, 1, 0) AS liked,IF(tbl_likes.`is_like` = 0, 1, 0) AS disliked  FROM `tbl_channels` c   LEFT JOIN tbl_likes tbl_likes ON c.id = tbl_likes.post_id  AND tbl_likes.userid = '$userid' AND tbl_likes.type = 'livetv' WHERE c.channel_title like '%$search%' and c.status=1 ");
         if(sizeof($response_livetv) > 0){
             foreach ($response_livetv as &$livetv) {
                 $livetv['user_interaction'] = [
                             'liked' => $livetv['liked'] == 1, // Convert to boolean
                            'disliked' => $livetv['disliked'] == 1 // Convert to boolean
                        ];
                        unset($livetv['liked'], $livetv['disliked']);
             }
         }
        return  $response_livetv;
    }

function getGenres($genreIds)
    {
        if (empty($genreIds)) return '';
        $genreIdStr = implode(',', array_map('intval', explode(',', $genreIds)));
        $genres = getRecords("SELECT genre_name FROM tbl_genres WHERE gid IN ($genreIdStr)");
        return implode(',', array_column($genres, 'genre_name'));
    }

    // Common function to check user interaction
    function getUserInteraction($postId, $userid, $type)
    {
        $query = "SELECT liked, disliked FROM tbl_likes WHERE post_id = '$postId' AND userid = '$userid' AND type = '$type'";
        $result = getRecords($query);
        return [
            'liked' => !empty($result) && $result[0]['liked'] == 1,
            'disliked' => !empty($result) && $result[0]['disliked'] == 1
        ];
    }


function parseDeviceInfo($deviceInfoRecords) {
    $device_info_array = [];

    foreach ($deviceInfoRecords as $device) {
        // Extract the device_info string
        $deviceInfoString = $device['device_info'];

        // Convert the string into an associative array
        $deviceDetails = [];
        $pairs = explode(", ", $deviceInfoString);
        foreach ($pairs as $pair) {
            list($key, $value) = explode(": ", $pair);
            $deviceDetails[trim($key)] = trim($value);
        }

        // Add parsed device details to the array
        $device_info_array[] = $deviceDetails;
    }

    return $device_info_array;
}




function app_login($userid){
			if(intval($userid))
			{
				if(getRecordField($query = "select id from tbl_users where id='{$userid}' and deleted=0 and status=1;"))
					return true;
				
				return false;
			}
			else
				return false;
		}
		
function executor($sql, $array = true)
{

    $conn = mysqli_connect('localhost', $GLOBALS['username'], $GLOBALS['pass'], $GLOBALS['db_name']);
    

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    if ($sql == null) {
        echo "Error in query";
        exit;
    }
    $result = $conn->query($sql);
    $products = array();
    if ($result != null) {
        while ($product = mysqli_fetch_assoc($result)) {
            if ($array) {
                $products[] = array_merge($product);
            } else {
                $products = array_merge($product);
            }
        }
    }
    return $products;
    mysqli_close($conn);
}


function write_file($txt, $append = false)
{
    $myfile = fopen($GLOBALS['file_name'], $append ? "a" : "w") or die("Unable to open file!");
    fwrite($myfile, ($append ? "\n" : "").$txt);
    fclose($myfile);
}

function encrypt($pure_string)
{
    $encrypted_string = openssl_encrypt($pure_string, "AES-128-ECB", $GLOBALS['pass']);
    return $encrypted_string;
}

function array_to_xml($data)
{
    $xml = new SimpleXMLElement('<root/>');
    array_walk_recursive($data, array($xml, 'addChild'));
    print $xml->asXML();
}

/**
 * Returns decrypted original string
 */
function decrypt($encrypted_string)
{
    $decrypted_string = openssl_decrypt($encrypted_string, "AES-128-ECB", $GLOBALS['pass']);
    return $decrypted_string;
}




function insertor($sql)
{

    $conn = mysqli_connect('localhost', $GLOBALS['db_name'], $GLOBALS['pass'], $GLOBALS['db_name']);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    if ($sql == null) {
        echo "Error in query";
        exit;
    }
    if (mysqli_query($conn, $sql)) {
        return array("status" => "Success", "id" => $conn->insert_id);
    } else {
        return "Failed";
    }
    mysqli_close($conn);
}

function call_curl($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $res = curl_exec($ch);
    return $res;
}

function send_notification($to, $message, $title)
{

    $finalTopic = "'" . $to . "' in topics";

    $API_ACCESS_KEY = $GLOBALS['firebase_server_key'];

    $data = array("condition" => $finalTopic,  "priority" => "high", "notification" => array("title" => "" . $title, "body" => "" . $message));
    $data_string = json_encode($data);
    // echo "The Json Data : ".$data_string;
    $headers = array('Authorization: key=' . $API_ACCESS_KEY, 'Content-Type: application/json');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    $result = curl_exec($ch);
    curl_close($ch);
}

function generateRandomString($length = 10)
{
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
function generateRandomNumber($length = 10)
{
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function getBaseUrl($array=false) {
        $protocol = "http";
        $host = "";
        $port = "";
        $dir = "";  

        // Get protocol
        if(array_key_exists("HTTPS", $_SERVER) && $_SERVER["HTTPS"] != "") {
            if($_SERVER["HTTPS"] == "on") { $protocol = "https"; }
            else { $protocol = "http"; }
        } elseif(array_key_exists("REQUEST_SCHEME", $_SERVER) && $_SERVER["REQUEST_SCHEME"] != "") { $protocol = $_SERVER["REQUEST_SCHEME"]; }

        // Get host
        if(array_key_exists("HTTP_X_FORWARDED_HOST", $_SERVER) && $_SERVER["HTTP_X_FORWARDED_HOST"] != "") { $host = trim(end(explode(',', $_SERVER["HTTP_X_FORWARDED_HOST"]))); }
        elseif(array_key_exists("SERVER_NAME", $_SERVER) && $_SERVER["SERVER_NAME"] != "") { $host = $_SERVER["SERVER_NAME"]; }
        elseif(array_key_exists("HTTP_HOST", $_SERVER) && $_SERVER["HTTP_HOST"] != "") { $host = $_SERVER["HTTP_HOST"]; }
        elseif(array_key_exists("SERVER_ADDR", $_SERVER) && $_SERVER["SERVER_ADDR"] != "") { $host = $_SERVER["SERVER_ADDR"]; }
        //elseif(array_key_exists("SSL_TLS_SNI", $_SERVER) && $_SERVER["SSL_TLS_SNI"] != "") { $host = $_SERVER["SSL_TLS_SNI"]; }

        // Get port
        if(array_key_exists("SERVER_PORT", $_SERVER) && $_SERVER["SERVER_PORT"] != "") { $port = $_SERVER["SERVER_PORT"]; }
        elseif(stripos($host, ":") !== false) { $port = substr($host, (stripos($host, ":")+1)); }
        // Remove port from host
        $host = preg_replace("/:\d+$/", "", $host);

        // Get dir
        if(array_key_exists("SCRIPT_NAME", $_SERVER) && $_SERVER["SCRIPT_NAME"] != "") { $dir = $_SERVER["SCRIPT_NAME"]; }
        elseif(array_key_exists("PHP_SELF", $_SERVER) && $_SERVER["PHP_SELF"] != "") { $dir = $_SERVER["PHP_SELF"]; }
        elseif(array_key_exists("REQUEST_URI", $_SERVER) && $_SERVER["REQUEST_URI"] != "") { $dir = $_SERVER["REQUEST_URI"]; }
        // Shorten to main dir
        if(stripos($dir, "/") !== false) { $dir = substr($dir, 0, (strripos($dir, "/")+1)); }

        // Create return value
        if(!$array) {
            if($port == "80" || $port == "443" || $port == "") { $port = ""; }
            else { $port = ":".$port; } 
            return htmlspecialchars($protocol."://".$host.$port.$dir, ENT_QUOTES); 
        } else { return ["protocol" => $protocol, "host" => $host, "port" => $port, "dir" => $dir]; }
    } 
