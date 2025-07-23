<?php 
  if(isset($_GET['episode_id'])){ 
    $page_title= 'Edit Episode';
  }
  else{ 
    $page_title='Add Episode'; 
  }
  $current_page="shows episode";
  $active_page="shows";

  include("includes/header.php");
  require("includes/function.php");
  require("language/language.php");

  require_once("thumbnail_images.class.php");
  
  
		$cat_qry="SELECT * FROM tbl_language ORDER BY language_name";
    $result_cat=mysqli_query($mysqli,$cat_qry);


  function quality_info_data($post_id, $param, $type='episode'){

    global $mysqli;

    $sql="SELECT * FROM tbl_quality WHERE `post_id` = '$post_id' AND `type` = 'tv_episode'";

    $res=mysqli_query($mysqli, $sql);

    if(mysqli_num_rows($res) > 0){
        $row=mysqli_fetch_assoc($res);
        return $row[$param];
    }
    else{
      return '';
    }
    mysqli_free_result($res);
    exit;

  }

  $file_path = getBaseUrl();

  $cat_qry="SELECT * FROM tbl_shows ORDER BY shows_name";
  $cat_result=mysqli_query($mysqli,$cat_qry);
    
  if(isset($_POST['submit']) and isset($_GET['add']))
  { 
      $title=addslashes(trim($_POST['episode_title']));
      $desc=addslashes(trim($_POST['episode_description']));

      $language_id=$_POST['language_id'];
      $shows_id=$_POST['shows_id'];
      $season_id=$_POST['season_id'];
      $total_time=addslashes(trim($_POST['total_time']));
      $director_name=addslashes(trim($_POST['director_name']));
      $maturity_rating=addslashes(trim($_POST['maturity_rating']));
      $release_date=addslashes(trim($_POST['release_date']));
      
      
       if($_POST['trailer_file_type']=='youtube_url'){

        $trailer_url=$_POST['trailer_url'];
        $youtube_video_url = addslashes($_POST['trailer_url']);
        parse_str( parse_url( $youtube_video_url, PHP_URL_QUERY ), $array_of_vars );
        $trailer_id=  $array_of_vars['v'];
      }
      else if($_POST['trailer_file_type']=='server_url' OR $_POST['trailer_file_type']=='embedded_url')
      {
        $trailer_url=$_POST['trailer_url'];
        $trailer_id='';
      }
      else if($_POST['trailer_file_type']=='local'){

        $path = "uploads/"; //set your folder path

        $ext = pathinfo($_FILES['trailer']['name'], PATHINFO_EXTENSION);

        $trailer_url=date('dmYhis').'_'.rand(0,99999)."_movietrailer".".".$ext;

        $tmp = $_FILES['trailer_local']['tmp_name'];
        
        if (move_uploaded_file($tmp, $path.$trailer_url)) 
        {
          $trailer_url=$trailer_url;
        } else {
          echo "Error in uploading video file !!";
          exit;
        }
        $trailer_id='';
      }

      if($_POST['video_file_type']=='youtube_url'){

        $episode_url=$_POST['episode_url'];
        $youtube_video_url = addslashes($_POST['episode_url']);
        parse_str( parse_url( $youtube_video_url, PHP_URL_QUERY ), $array_of_vars );
        $video_id=  $array_of_vars['v'];
      }
      else if($_POST['video_file_type']=='server_url' OR $_POST['video_file_type']=='embedded_url'){
        $episode_url=$_POST['episode_url'];
        $video_id='';
      }
      else if($_POST['video_file_type']=='local'){

        $path = "uploads/"; //set your folder path

        $episode_url=rand(0,99999)."_".str_replace(" ", "-", $_FILES['video_local']['name']);

        $tmp = $_FILES['video_local']['tmp_name'];
        
        if (move_uploaded_file($tmp, $path.$episode_url)) 
        {
            $episode_url=$episode_url;
        } else {
            echo "Error in uploading video file !!";
        }

        $video_id='';
      }


      if($_POST['poster_img']=='' || $_FILES['episode_poster']['error']!=4){
          // for movies poster
          $ext = pathinfo($_FILES['episode_poster']['name'], PATHINFO_EXTENSION);
          $episode_poster=date('dmYhis').'_'.rand(0,99999).".".$ext;
          $pic1=$_FILES['episode_poster']['tmp_name'];

                
          $tpath1='images/episodes/'.$episode_poster; 
          copy($pic1,$tpath1);

          $thumbpath='images/episodes/thumbs/'.$episode_poster;
            
          $obj_img = new thumbnail_images();
          $obj_img->PathImgOld = $tpath1;
          $obj_img->PathImgNew =$thumbpath;
          $obj_img->NewWidth = 300;
          $obj_img->NewHeight = 300;
          if (!$obj_img->create_thumbnail_images()) 
          {
            echo "Thumbnail not created... please upload image again";
          }
      }
      else{

        $get_file_name = parse_url($_POST['poster_img'], PHP_URL_PATH);

        $ext = pathinfo($get_file_name, PATHINFO_EXTENSION);
        $episode_poster=date('dmYhis').'_'.rand(0,99999).".".$ext;

        $tpath1='images/episodes/'.$episode_poster;

        grab_image($_POST['poster_img'], $tpath1);

        $thumbpath='images/episodes/thumbs/'.$episode_poster;
          
        $obj_img = new thumbnail_images();
        $obj_img->PathImgOld = $tpath1;
        $obj_img->PathImgNew =$thumbpath;
        $obj_img->NewWidth = 300;
        $obj_img->NewHeight = 300;
        if (!$obj_img->create_thumbnail_images()) 
        {
          echo "Thumbnail not created... please upload image again";
        }

        
      }
          
      $data = array( 
        'language_id'  =>  $language_id,
        'shows_id'  =>  $shows_id,
        'season_id'  =>  $season_id,
        'episode_title'  =>  $title,
        'episode_description'  =>  $desc,
        'trailer_type'=> $_POST['trailer_file_type'],
		'trailer_url'  =>  $trailer_url,
        'trailer_id'  =>  $trailer_id,
        'episode_type'  =>  $_POST['video_file_type'],
        'episode_url'  =>  $episode_url,
        'video_id'  =>  $video_id,
        'episode_poster'  =>  $tpath1,
        'total_time'  =>  $total_time,
        'director_name'  =>  $director_name,
        'release_date	'  =>   cleanInput($_POST['release_date']),
        'maturity_rating'  =>  $maturity_rating,
        'subtitle'  => $_POST['subtitle'],
        'is_quality'  =>  $_POST['quality_status'],
        );    

      $qry = Insert('tbl_tv_episode',$data); 

      $last_id=mysqli_insert_id($mysqli);

      if(isset($_POST['quality_status']) && $_POST['quality_status']=='true'){

        if($_POST['video_file_type']=='local'){

          $path = "uploads/"; //set your folder path.

          $quality_arr=array('480', '720', '1080');

          $quality_480=$quality_720=$quality_1080='';

          foreach($quality_arr as $key => $value) {

              $ext = pathinfo($_FILES['quality_local']['name'][$value], PATHINFO_EXTENSION);

              $video_url=date('dmYhis').'_'.rand(0,99999)."_".$value.".".$ext;

              $tmp = $_FILES['quality_local']['tmp_name'][$value];
              
              if (move_uploaded_file($tmp, $path.$video_url)) 
              {
                $video_url=$video_url;
              }else {
                echo "Error in uploading quality video file !!";
              }

              if($value=='480'){
                $quality_480=$video_url;
              }
              else if($value=='720'){
                $quality_720=$video_url; 
              }
              else if($value=='1080'){
                $quality_1080=$video_url; 
              }

          }


          $data = array( 
            'post_id'  =>  $last_id,
            'post_upload_type'  =>  'local',
            'quality_480'  =>  $quality_480,
            'quality_720'  =>  $quality_720,
            'quality_1080'  =>  $quality_1080,
            'type'  =>  'episode',
            'created_at'  =>  strtotime(date('d-m-Y h:i:s A')),
            );    

          $qry = Insert('tbl_quality',$data);

        }
        else{
          
          $data = array( 
            'post_id'  =>  $last_id,
            'post_upload_type'  =>  'server_url',
            'quality_480'  =>  trim($_POST['quality_url']['480']),
            'quality_720'  =>  trim($_POST['quality_url']['720']),
            'quality_1080'  =>  trim($_POST['quality_url']['1080']),
            'type'  =>  'episode',
            'created_at'  =>  strtotime(date('d-m-Y h:i:s A')),
            );    

          $qry = Insert('tbl_quality',$data);
        }

      }

      if(isset($_POST['subtitle']) && $_POST['subtitle']=='true'){

          foreach ($_POST['subtitle_lang'] as $key => $value) {
            
              $subtitle_url='';

              if($_POST['subtitle_type'][$key]=='live_url')
              {
                $subtitle_url=trim($_POST['subtitle_url'][$key]);
              }
              else if($_POST['subtitle_type'][$key]=='local')
              {

                  $path = "uploads/"; //set your folder path.

                  $ext = pathinfo($_FILES['subtitle_local']['name'][$key], PATHINFO_EXTENSION);

                  $subtitle_url=date('dmYhis').'_'.rand(0,99999)."episode_subtitle.".$ext;

                  $tmp = $_FILES['subtitle_local']['tmp_name'][$key];
                  
                  if (move_uploaded_file($tmp, $path.$subtitle_url)) 
                  {
                      $subtitle_url=$subtitle_url;
                  } else {
                      echo "Error in uploading subtitle file !!";
                  }
              }

              // add in subtitle table

              $data = array( 
                'post_id'  =>  $last_id,
                'language'  =>  $_POST['subtitle_lang'][$key],
                'subtitle_type'  =>  $_POST['subtitle_type'][$key],
                'subtitle_url'  =>  $subtitle_url,
                'type'  =>  'episode',
                'created_at'  =>  strtotime(date('d-m-Y h:i:s A')),
                );    

              $qry = Insert('tbl_subtitles',$data);    
          }

      }     

      $_SESSION['msg']="10";
   
      header( "Location:manage_tv_episode.php");
      exit;
  }

  if(isset($_GET['episode_id']))
  {  
    $qry="SELECT * FROM tbl_tv_episode where id='".$_GET['episode_id']."'";
    $result=mysqli_query($mysqli,$qry);
    $row=mysqli_fetch_assoc($result);
  }
  if(isset($_POST['submit']) and isset($_GET['episode_id']))
  { 
    $title=addslashes(trim($_POST['episode_title']));
    $desc=addslashes(trim($_POST['episode_description']));
    $language_id=$_POST['language_id'];
      $shows_id=$_POST['shows_id'];
      $season_id=$_POST['season_id'];
      $total_time=addslashes(trim($_POST['total_time']));
      $director_name=addslashes(trim($_POST['director_name']));
      $maturity_rating=addslashes(trim($_POST['maturity_rating']));
      $release_date=addslashes(trim($_POST['release_date']));
   
   
   if($_POST['trailer_file_type']=='youtube_url'){

      $trailer_url=$_POST['trailer_url'];
      $youtube_video_url = addslashes($_POST['trailer_url']);
      parse_str( parse_url( $youtube_video_url, PHP_URL_QUERY ), $array_of_vars );
      $trailer_id=  $array_of_vars['v'];

      if($row['trailer_type']=='local'){
        unlink('uploads/'.$row['trailer_url']);
      }

    }
    else if($_POST['trailer_file_type']=='server_url' OR $_POST['trailer_file_type']=='embedded_url'){
      $trailer_url=$_POST['trailer_url'];
      $trailer_id='';

      if($row['trailer_type']=='local'){
        unlink('uploads/'.$row['trailer_url']);
      }

    }
    else if($_POST['trailer_file_type']=='local'){

      if($_FILES['trailer_local']['error']!=4){

        unlink('uploads/'.$row['trailer_url']);

        $path = "uploads/"; //set your folder path

        $trailer_url=rand(0,99999)."_".str_replace(" ", "-", $_FILES['trailer_local']['name']);

        $tmp = $_FILES['trailer_local']['tmp_name'];
        
        if(move_uploaded_file($tmp, $path.$trailer_url)) 
        {
            $trailer_url=$trailer_url;
        }else {
            echo "Error in uploading video file !!";
            exit;
        }
      }
      else{
        $trailer_url=$row['trailer_url'];
      }

      $trailer_id='';
    }


    if($_POST['video_file_type']=='youtube_url'){

      $episode_url=$_POST['episode_url'];
      $youtube_video_url = addslashes($_POST['episode_url']);
      parse_str( parse_url( $youtube_video_url, PHP_URL_QUERY ), $array_of_vars );
      $video_id=  $array_of_vars['v'];


      if($row['episode_type']=='local'){
        unlink('uploads/'.$row['episode_url']);
      }

    }
    else if($_POST['video_file_type']=='server_url' OR $_POST['video_file_type']=='embedded_url'){
      $episode_url=$_POST['episode_url'];
      $video_id='';

      if($row['episode_type']=='local'){
        unlink('uploads/'.$row['episode_url']);
      }

    }
    else if($_POST['video_file_type']=='local'){

      if($_FILES['video_local']['error']!=4){

        unlink('uploads/'.$row['episode_url']);

        $path = "uploads/"; //set your folder path

        $episode_url=rand(0,99999)."_".str_replace(" ", "-", $_FILES['video_local']['name']);

        $tmp = $_FILES['video_local']['tmp_name'];
        
        if(move_uploaded_file($tmp, $path.$episode_url)) 
        {
            $episode_url=$episode_url;
        }else {
            echo "Error in uploading video file !!";
        }
      }
      else{
        $episode_url=$row['episode_url'];
      }

      $video_id='';
    }

    
    if($_FILES['episode_poster']['error']!=4){

        unlink('images/episodes/'.$row['episode_poster']);
        unlink('images/episodes/thumbs/'.$row['episode_poster']);

        // for movie poster
        $ext = pathinfo($_FILES['episode_poster']['name'], PATHINFO_EXTENSION);
        $episode_poster=date('dmYhis').'_'.rand(0,99999).".".$ext;
        $pic1=$_FILES['episode_poster']['tmp_name'];

              
        $tpath1='images/episodes/'.$episode_poster; 
        copy($pic1,$tpath1);
        $thumbpath='images/episodes/thumbs/'.$episode_poster;
          
        $obj_img = new thumbnail_images();
        $obj_img->PathImgOld = $tpath1;
        $obj_img->PathImgNew =$thumbpath;
        $obj_img->NewWidth = 300;
        $obj_img->NewHeight = 300;
        if (!$obj_img->create_thumbnail_images()) 
        {
          echo $_SESSION['msg']="Thumbnail not created... please upload image again";
          exit;
        }
    }else{
        $episode_poster=$row['episode_poster'];
    }

      $data = array( 
        'language_id'  =>  $language_id,
        'shows_id'  =>  $shows_id,
        'season_id'  =>  $season_id,
        'episode_description'  =>  $desc,
        'episode_title'  =>  $title,
        'trailer_type'=> $_POST['trailer_file_type'],
		'trailer_url'  =>  $trailer_url,
        'trailer_id'  =>  $trailer_id,
        'episode_type'  =>  $_POST['video_file_type'],
        'episode_url'  =>  $episode_url,
        'video_id'  =>  $video_id,
        'episode_poster'  =>  $tpath1,
        'total_time'  =>  $total_time,
        'director_name'  =>  $director_name,
        'release_date	'  =>   cleanInput($_POST['release_date']),
        'maturity_rating'  =>  $maturity_rating,
        'subtitle'  => $_POST['subtitle'],
        'is_quality'  =>  $_POST['quality_status'],
        );

      $edit=Update('tbl_tv_episode', $data, "WHERE id = '".$_GET['episode_id']."'");

      // quality videos
      if(isset($_POST['quality_status']) && $_POST['quality_status']=='true'){

        if($_POST['video_file_type']=='local'){

          $path = "uploads/"; //set your folder path.

          $quality_arr=array('480', '720', '1080');

          $quality_480=$quality_720=$quality_1080='';

          if(quality_info_data($_GET['episode_id'], 'post_upload_type')=='server_url'){
            $data = array( 
              'quality_480'  =>  '',
              'quality_720'  =>  '',
              'quality_1080'  =>  '',
              );

            $updateSql=Update('tbl_quality', $data, "WHERE `post_id` = '".$_GET['episode_id']."' AND `type`='episode'");
          }

          foreach($quality_arr as $key => $value) {

              $param='quality_'.$value;

              if($_FILES['quality_local']['error'][$value]!=4){

                  unlink('uploads/'.quality_info_data($_GET['episode_id'], $param));

                  $ext = pathinfo($_FILES['quality_local']['name'][$value], PATHINFO_EXTENSION);

                  $video_url=date('dmYhis').'_'.rand(0,99999)."_".$value.".".$ext;

                  $tmp = $_FILES['quality_local']['tmp_name'][$value];
                  
                  if (move_uploaded_file($tmp, $path.$video_url)) 
                  {
                    $video_url=$video_url;
                  }else {
                    echo "Error in uploading quality video file !!";
                  }                
              }
              else{
                  $video_url=quality_info_data($_GET['episode_id'], $param);
              }

              if($value=='480'){
                $quality_480=$video_url;
              }
              else if($value=='720'){
                $quality_720=$video_url; 
              }
              else if($value=='1080'){
                $quality_1080=$video_url; 
              }
          }

          if(quality_info_data($_GET['episode_id'], 'id')!=''){
            $data = array( 
              'post_upload_type'  =>  'local',
              'quality_480'  =>  $quality_480,
              'quality_720'  =>  $quality_720,
              'quality_1080'  =>  $quality_1080,
              );    

            $updateSql=Update('tbl_quality', $data, "WHERE `post_id` = '".$_GET['episode_id']."' AND `type`='episode'");  
          }
          else{
              $data = array( 
              'post_id'  =>  $_GET['episode_id'],
              'post_upload_type'  =>  'local',
              'quality_480'  =>  $quality_480,
              'quality_720'  =>  $quality_720,
              'quality_1080'  =>  $quality_1080,
              'type'  =>  'episode',
              'created_at'  =>  strtotime(date('d-m-Y h:i:s A')),
              );    

            $qry = Insert('tbl_quality',$data);
          }

        }
        else{

          if(quality_info_data($_GET['episode_id'], 'post_upload_type')=='local'){
              
              unlink('uploads/'.quality_info_data($_GET['episode_id'], 'quality_480'));
              unlink('uploads/'.quality_info_data($_GET['episode_id'], 'quality_720'));
              unlink('uploads/'.quality_info_data($_GET['episode_id'], 'quality_1080'));
          }

          if(quality_info_data($_GET['episode_id'], 'id')!=''){
            $data = array( 
              'post_upload_type'  =>  'server_url',
              'quality_480'  =>  trim($_POST['quality_url']['480']),
              'quality_720'  =>  trim($_POST['quality_url']['720']),
              'quality_1080'  =>  trim($_POST['quality_url']['1080'])
              );    

            $updateSql=Update('tbl_quality', $data, "WHERE `post_id` = '".$_GET['episode_id']."' AND `type`='episode'");  
          }
          else{
              $data = array( 
              'post_id'  =>  $_GET['episode_id'],
              'post_upload_type'  =>  'server_url',
              'quality_480'  =>  trim($_POST['quality_url']['480']),
              'quality_720'  =>  trim($_POST['quality_url']['720']),
              'quality_1080'  =>  trim($_POST['quality_url']['1080']),
              'type'  =>  'episode',
              'created_at'  =>  strtotime(date('d-m-Y h:i:s A')),
              );    

              $qry = Insert('tbl_quality',$data);
          }
          
        }

      }
      // end quality

      if(isset($_POST['subtitle']) && $_POST['subtitle']=='true'){
  
          $index=1;

          foreach ($_POST['old_subtitle_id'] as $key_subtitle => $subtitle_id) {

              $index++;

              $subtitle_url='';
        
              if($_POST['subtitle_type'][$key_subtitle]=='live_url')
              {
                $subtitle_url=trim($_POST['subtitle_url'][$key_subtitle]);
              }
              else if($_POST['subtitle_type'][$key_subtitle]=='local')
              {
                  if($_FILES['subtitle_local']['error'][$key_subtitle]!=4){

                    unlink('uploads/'.$_POST['subtitle_local_url'][$key_subtitle]);

                    $path = "uploads/"; //set your folder path.

                    $ext = pathinfo($_FILES['subtitle_local']['name'][$key_subtitle], PATHINFO_EXTENSION);

                    $subtitle_url=date('dmYhis').'_'.rand(0,99999)."episode_subtitle.".$ext;

                    $tmp = $_FILES['subtitle_local']['tmp_name'][$key_subtitle];
                    
                    move_uploaded_file($tmp, $path.$subtitle_url);
                  }
                  else{
                    $subtitle_url=trim($_POST['subtitle_local_url'][$key_subtitle]);
                  }
              }

              // update in subtitle table

              $dataUpdate = array(
                'language'  =>  $_POST['subtitle_lang'][$key_subtitle],
                'subtitle_type'  =>  $_POST['subtitle_type'][$key_subtitle],
                'subtitle_url'  =>  $subtitle_url,
                );    

              $updateSubtitle=Update('tbl_subtitles', $dataUpdate, "WHERE id = '".$subtitle_id."'");

          }

          $index=$index-1;

          foreach(array_slice($_POST['subtitle_lang'], $index) as $key => $val){

              $lang_index=$key+$index;

              $subtitle_url='';

              if($_POST['subtitle_type'][$lang_index]=='live_url')
              {
                $subtitle_url=trim($_POST['subtitle_url'][$lang_index]);
              }
              else if($_POST['subtitle_type'][$lang_index]=='local')
              {

                  $path = "uploads/"; //set your folder path.

                  $ext = pathinfo($_FILES['subtitle_local']['name'][$lang_index], PATHINFO_EXTENSION);

                  $subtitle_url=date('dmYhis').'_'.rand(0,99999)."_subtitle.".$ext;

                  $tmp = $_FILES['subtitle_local']['tmp_name'][$lang_index];
                  
                  if (move_uploaded_file($tmp, $path.$subtitle_url)) 
                  {
                      $subtitle_url=$subtitle_url;
                  } else {
                      echo "Error in uploading subtitle file !!";
                  }
              }

              // add in subtitle table

              $data = array( 
                'post_id'  =>  $_GET['episode_id'],
                'language'  =>  $_POST['subtitle_lang'][$lang_index],
                'subtitle_type'  =>  $_POST['subtitle_type'][$lang_index],
                'subtitle_url'  =>  $subtitle_url,
                'type'  =>  'episode',
                'created_at'  =>  strtotime(date('d-m-Y h:i:s A')),
                );    

              $qry = Insert('tbl_subtitles',$data); 
          }

      }

      $_SESSION['msg']="11"; 

      if(isset($_GET['redirect']))
        header( "Location:".$_GET['redirect']);
      else  
        header( "Location:add_episode.php?episode_id=".$_GET['episode_id']);
      exit;
  }
   
?>
<!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> -->

<style type="text/css">
  .content, .subtitle_container, #quality_server_url, #quality_local{
    margin-bottom:10px;
    border:1px solid #aaa;
    padding:10px 5px
  }
  .content .removeMore, .removeSubtitle, .remove_quality{
    color: #F00 !important;
    font-weight:500 !important;
  }
</style>
    
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="page_title_block">
        <div class="col-md-5 col-xs-12">
          <div class="page_title"><?=$page_title?></div>
        </div>
        <div class="col-md-7 col-xs-12" style="text-align:right;">
           <a href="manage_tv_episode.php" style="background:white;margin-top:10px;font-size:25px;">X</a>
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="card-body mrg_bottom">

        
        <form class="form form-horizontal" action="" method="post"  enctype="multipart/form-data">

          <input type="hidden" name="episode_id" value="<?=(isset($_GET['episode_id'])) ? $_GET['episode_id'] : ''?>">

          <input type="hidden" name="is_quality" value="<?=(isset($_GET['episode_id'])) ? $row['is_quality'] : ''?>">

          <input type="hidden" name="is_subtitle" value="<?=(isset($_GET['episode_id'])) ? $row['subtitle'] : 'false'?>">

          <div class="section">
            <div class="section-body">
                  <div class="form-group">
                    <label class="col-md-3 control-label">Language :-</label>
                    <div class="col-md-8">
                    <select name="language_id" id="language_id" class="select2" required>
                        <option value="">Select Language</option>
                        <?php
                            while($datal=mysqli_fetch_array($result_cat))
                            {
                        ?>                       
                        <option value="<?php echo $datal['id'];?>" <?php if(isset($_GET['series_id']) && $row['language_id']==$datal['id']){ echo 'selected'; } ?>><?php echo $datal['language_name'];?></option>                          
                        <?php
                          }
                        ?>
                      </select>
                </div>
              </div> 
              <div class="form-group">
                <label class="col-md-3 control-label">Show :-</label>
                <div class="col-md-8">
                  <select name="shows_id" id="shows_id" class="select2" required>
                    <option value="">Select Tv Show</option>
                    <?php
                        while($data=mysqli_fetch_array($cat_result))
                        {
                    ?>                       
                    <option value="<?php echo $data['id'];?>" <?php if(isset($_GET['episode_id']) && $row['shows_id']==$data['id']){ echo 'selected'; } ?>><?php echo $data['shows_name'];?></option>                          
                    <?php
                      }
                    ?>
                  </select>
                </div>
              </div> 
               <div class="form-group">
                <label class="col-md-3 control-label">Season :-</label>
                <div class="col-md-8">
                  <input type="hidden" class="old_season_id" value="<?php if(isset($_GET['episode_id'])){ echo $row['season_id'];}?>">
                  <select name="season_id" id="season_id" class="select2" required>
                    <option value="">Select Season</option>
                  </select>
                </div>
              </div>                 
              <div class="form-group">
                <label class="col-md-3 control-label">Episode Title :-</label>
                <div class="col-md-8">
                  <input type="text" name="episode_title" id="episode_title" value="<?php if(isset($_GET['episode_id'])){ echo $row['episode_title'];}?>" class="form-control" required>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label">Trailer Upload Type :-</label>
                <div class="col-md-8">                       
                  <select name="trailer_file_type" id="trailer_file_type" class="select2" required>
                        <option value="">Select Type</option>
                        <option value="youtube_url" <?php if(isset($_GET['episode_id']) && $row['trailer_type']=='youtube_url'){ echo 'selected'; } ?>>YouTube URL</option>
                        <option value="server_url" <?php if(isset($_GET['episode_id']) && $row['trailer_type']=='server_url'){ echo 'selected'; } ?>>Live URL</option>
                        <option value="local" <?php if(isset($_GET['episode_id']) && $row['trailer_type']=='local'){ echo 'selected'; } ?>>Local System</option>
                        <option value="embedded_url" <?php if(isset($_GET['episode_id']) && $row['trailer_type']=='embedded_url'){ echo 'selected'; } ?>>Embedded URL (Open Load, Very Stream, Daily motion, Vimeo)</option>
                        
                  </select>
                </div>
              </div>
              
               <div id="trailer_url_holder" class="form-group" style="display:none;">
                <label class="col-md-3 control-label">Enter trailer URL :-
                  <p style="color:#F00;font-weight: 500">(This is default play video url on app player)</p>
                </label>
                <div class="col-md-8">
                  <input type="text" name="trailer_url" value="<?php if(isset($_GET['episode_id'])){ echo $row['trailer_url'];}?>" class="form-control">
                </div>
              </div>
              
              
              <div id="trailer_local_holder" class="form-group" style="display:none;">
                <label class="col-md-3 control-label">Browse trailer Video :-
                  <p style="color:#F00;font-weight: 500">(This is default play video on app player)</p>
                </label>
                <div class="col-md-6">
                  <input type="file" name="trailer_local" value="" class="form-control trailer_local">
                    <?php 
                      if(isset($_GET['episode_id']) && $row['is_quality']=='false'){
                     ?> 
                      <div style="word-break: break-all;"><label class="control-label">Current URL :-</label><?php echo $file_path.'uploads/'.$row['trailer_url']?></div><br>
                      <?php  
                    }
                    ?>
                    <div class="uploadPreview" style="<?php if(isset($_GET['episode_id']) && $row['trailer_type']=='local'){ echo 'display: block;';}else{ echo 'display: none;';}?>background: #eee;text-align: center;">
                      <video height="250" width="100%" class="video-preview" src="<?php if(isset($_GET['episode_id']) && $row['trailer_type']=='local'){ echo $file_path.'uploads/'.$row['trailer_url']; } ?>" controls="controls"/>
                    </div>
                </div>
              </div>
              
               <div class="form-group">
                <label class="col-md-3 control-label">Relsease Date :-</label>
                <div class="col-md-8">
                  <input type="date" name="release_date"   value="<?php if(isset($_GET['series_id'])){echo $row['release_date'];}?>" class="form-control" >
                </div>
              </div>
               <div class="form-group">
                <label class="col-md-3 control-label">Director Name :-</label>
                <div class="col-md-8">
                  <input type="text" name="director_name" placeholder="Enter Director Name" id="director_name" value="<?php if(isset($_GET['series_id'])){echo $row['director_name'];}?>" class="form-control" required>
                </div>
              </div>
               <div class="form-group">
                <label class="col-md-3 control-label">Maturity rating :-</label>
                <div class="col-md-8">
                  <input type="text" name="maturity_rating" placeholder="Enter maturity rating" id="maturity_rating" value="<?php if(isset($_GET['series_id'])){echo $row['maturity_rating'];}?>" class="form-control" required>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label">Total Time :-</label>
                <div class="col-md-8">
                  <input type="text" name="total_time" placeholder="Enter video time" id="total_time" value="<?php if(isset($_GET['series_id'])){echo $row['total_time'];}?>" class="form-control" required>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label">Video Upload Type :-</label>
                <div class="col-md-8">                       
                  <select name="video_file_type" id="video_file_type" class="select2" required>
                      <option value="">Select Type</option>
                      <option value="youtube_url" <?php if(isset($_GET['episode_id']) && $row['episode_type']=='youtube_url'){ echo 'selected'; } ?>>YouTube URL</option>
                      <option value="server_url" <?php if(isset($_GET['episode_id']) && $row['episode_type']=='server_url'){ echo 'selected'; } ?>>Live URL</option>
                      <option value="local" <?php if(isset($_GET['episode_id']) && $row['episode_type']=='local'){ echo 'selected'; } ?>>Local System</option>
                      <option value="embedded_url" <?php if(isset($_GET['episode_id']) && $row['movie_type']=='embedded_url'){ echo 'selected'; } ?>>Embedded URL (Open Load, Very Stream, Daily motion, Vimeo)</option>
                  </select>
                </div>
              </div>
              <div id="episode_url_holder" class="form-group" style="display:none;">
                <label class="col-md-3 control-label">Enter URL :-</label>
                <div class="col-md-8">
                  <input type="text" name="episode_url" id="episode_url" value="<?php if(isset($_GET['episode_id'])){ echo $row['episode_url'];}?>" class="form-control">
                </div>
              </div>
              <div id="episode_local_holder" class="form-group" style="display:none;">
                <label class="col-md-3 control-label">Browse Video :-</label>
                <div class="col-md-8">
                  <input type="file" name="video_local" id="video_local" value="" class="form-control">
                    <?php 
                      if(isset($_GET['episode_id'])){
                     ?> 
                      <div style="word-break: break-all;"><label class="control-label">Current URL :-</label><?php echo $file_path.'uploads/'.$row['episode_url']?></div><br>
                      <?php  
                    }
                    ?>
                    <div id="uploadPreview" style="<?php if(isset($_GET['episode_id']) && $row['episode_type']=='local'){ echo 'display: block;';}else{ echo 'display: none;';}?>background: #eee;text-align: center;">
                      <video height="400" width="100%" class="video-preview" src="<?php echo $file_path.'uploads/'.$row['episode_url']?>" controls="controls"/>
                    </div>
                </div>
              </div>

              <div id="quality_holder" style="display: none">
                <div class="form-group">
                  <div class="col-md-offset-3 col-md-8">
                    <span style="color:#F00;font-weight: 500">(You can add video with different qualities)</span>
                    <br/>
                  </div>
                </div>  
                <br/>

                <div class="form-group">
                  <label class="col-md-3 control-label">Quality ON/OFF :-</label>
                  <div class="col-md-8">                       
                    <select name="quality_status" id="quality_status" class="select2">
                      <option value="false" <?=(isset($_GET['episode_id']) && $row['is_quality']=='false') ? 'selected="selected"' : ''?>>OFF</option>
                      <option value="true"<?=(isset($_GET['episode_id']) && $row['is_quality']=='true') ? 'selected="selected"' : ''?>>ON</option>
                    </select>
                  </div>
                </div>
                <div id="quality_server_url" style="display: none;">
                  <div class="form-group">
                    <label class="col-md-3 control-label">Enter URL for 480p :-</label>
                    <div class="col-md-8">
                      <input type="text" name="quality_url[480]" value="<?=(isset($_GET['episode_id']) && $row['is_quality']=='true') ? quality_info_data($row['id'], 'quality_480') : ''?>" class="form-control">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-3 control-label">Enter URL for 720p :-</label>
                    <div class="col-md-8">
                      <input type="text" name="quality_url[720]" value="<?=(isset($_GET['episode_id']) && $row['is_quality']=='true') ? quality_info_data($row['id'], 'quality_720') : ''?>" class="form-control">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-3 control-label">Enter URL for 1080p :-</label>
                    <div class="col-md-8">
                      <input type="text" name="quality_url[1080]" value="<?=(isset($_GET['episode_id']) && $row['is_quality']=='true') ? quality_info_data($row['id'], 'quality_1080') : ''?>" class="form-control">
                    </div>
                  </div>
                </div>
                <div id="quality_local" style="display: none;">

                  <div class="form-group">
                    <label class="col-md-3 control-label">Browse Video for 480p :-</label>
                    <div class="col-md-8">
                      <input type="file" name="quality_local[480]" class="form-control video_local">
                      <?php 
                        if(isset($_GET['episode_id']) && $row['is_quality']=='true' && $row['movie_type']=='local' && quality_info_data($row['id'], 'quality_480')!=''){
                       ?> 
                        <div style="word-break: break-all;"><label class="control-label">Current URL :-</label><?php echo $file_path.'uploads/'.quality_info_data($row['id'], 'quality_480')?></div><br>
                        <?php  
                      }
                      ?>
                      <div class="uploadPreview" style="<?php if(isset($_GET['episode_id']) && $row['movie_type']=='local' && $row['is_quality']=='true' && quality_info_data($row['id'], 'quality_480')!=''){ echo 'display: block;';}else{ echo 'display: none;';}?>background: #eee;text-align: center;">
                        <video height="250" width="100%" class="video-preview" src="<?=$file_path.'uploads/'.quality_info_data($row['id'], 'quality_480')?>" controls="controls"/>
                      </div>

                      <?php 
                        if(isset($_GET['episode_id']) && $row['is_quality']=='true' && $row['movie_type']=='local' && quality_info_data($row['id'], 'quality_480')!=''){
                       ?>
                      <div style="text-align: right;margin-bottom: 20px">
                        <a href="javascript:void(0)" class="remove_quality" data-column="quality_480" data-id="<?=quality_info_data($row['id'], 'id')?>">&times; Remove</a>
                      </div>
                      <?php } ?>
                    </div>

                  </div>
                  <div class="form-group">
                    <label class="col-md-3 control-label">Browse Video for 720p :-</label>
                    <div class="col-md-8">
                      <input type="file" name="quality_local[720]" class="form-control video_local">
                      <?php 
                        if(isset($_GET['episode_id']) && $row['is_quality']=='true' && $row['movie_type']=='local' && quality_info_data($row['id'], 'quality_720')!=''){
                       ?> 
                        <div style="word-break: break-all;"><label class="control-label">Current URL :-</label><?php echo $file_path.'uploads/'.quality_info_data($row['id'], 'quality_720')?></div><br>
                        <?php  
                      }
                      ?>
                      <div class="uploadPreview" style="<?php if(isset($_GET['episode_id']) && $row['movie_type']=='local' && $row['is_quality']=='true' && quality_info_data($row['id'], 'quality_720')!=''){ echo 'display: block;';}else{ echo 'display: none;';}?>background: #eee;text-align: center;">
                        <video height="250" width="100%" class="video-preview" src="<?=$file_path.'uploads/'.quality_info_data($row['id'], 'quality_720')?>" controls="controls"/>
                      </div>

                      <?php 
                        if(isset($_GET['episode_id']) && $row['is_quality']=='true' && $row['movie_type']=='local' && quality_info_data($row['id'], 'quality_720')!=''){
                       ?>
                      <div style="text-align: right;margin-bottom: 20px">
                        <a href="javascript:void(0)" class="remove_quality" data-column="quality_720" data-id="<?=quality_info_data($row['id'], 'id')?>">&times; Remove</a>
                      </div>
                      <?php } ?>

                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-3 control-label">Browse Video for 1080p :-</label>
                    <div class="col-md-8">
                      <input type="file" name="quality_local[1080]" class="form-control video_local">
                      <?php 
                        if(isset($_GET['episode_id']) && $row['is_quality']=='true' && $row['movie_type']=='local' && quality_info_data($row['id'], 'quality_1080')!=''){
                       ?> 
                        <div style="word-break: break-all;"><label class="control-label">Current URL :-</label><?php echo $file_path.'uploads/'.quality_info_data($row['id'], 'quality_1080')?></div><br>
                        <?php  
                      }
                      ?>
                      <div class="uploadPreview" style="<?php if(isset($_GET['episode_id']) && $row['movie_type']=='local' && $row['is_quality']=='true' && quality_info_data($row['id'], 'quality_1080')!=''){ echo 'display: block;';}else{ echo 'display: none;';}?>background: #eee;text-align: center;">
                        <video height="250" width="100%" class="video-preview" src="<?=$file_path.'uploads/'.quality_info_data($row['id'], 'quality_1080')?>" controls="controls"/>
                      </div>

                      <?php 
                        if(isset($_GET['episode_id']) && $row['is_quality']=='true' && $row['movie_type']=='local' && quality_info_data($row['id'], 'quality_1080')!=''){
                       ?>
                      <div style="text-align: right;margin-bottom: 20px">
                        <a href="javascript:void(0)" class="remove_quality" data-column="quality_1080" data-id="<?=quality_info_data($row['id'], 'id')?>">&times; Remove</a>
                      </div>
                      <?php } ?>

                    </div>
                  </div>

                </div>
              </div> 

              <div class="subtitle" style="margin: 10px 0px;display: none;">
                <div class="form-group">
                  <label class="col-md-3 control-label">Subtitle ON/OFF :-</label>
                  <div class="col-md-8">                       
                    <select name="subtitle" id="subtitle" class="select2">
                      <option value="false" <?=(isset($_GET['episode_id']) && $row['subtitle']=='false') ? 'selected="selected"' : ''?>>OFF</option>
                      <option value="true"<?=(isset($_GET['episode_id']) && $row['subtitle']=='true') ? 'selected="selected"' : ''?>>ON</option>
                    </select>
                  </div>
                </div>

                <?php 
                  if(!isset($_GET['episode_id'])){
                ?>
                <div class="subtitle_fields" style="display: none">
                  <div class="subtitle_container">
                    <div class="form-group subtitle_lang">
                      <label class="col-md-3 control-label">Subtitle Language :-</label>
                      <div class="col-md-8">
                        <input type="text" name="subtitle_lang[]" value="" class="form-control">
                      </div>
                    </div>
                    <div class="form-group subtitle_type">
                      <label class="col-md-3 control-label">Subtitle Upload Type :-</label>
                      <div class="col-md-8">                       
                        <select name="subtitle_type[]" class="select2">
                          <option value="">Select Type</option>
                          <option value="live_url" <?=(isset($_GET['episode_id']) && $row['subtitle']=='false') ? 'selected="selected"' : ''?>>Live Url</option>
                          <option value="local" <?=(isset($_GET['episode_id']) && $row['subtitle']=='false') ? 'selected="selected"' : ''?>>Local System</option>
                        </select>
                      </div>
                    </div>
                    <div class="form-group subtitle_url" style="display: none">
                      <label class="col-md-3 control-label">Enter URL :-</label>
                      <div class="col-md-8">
                        <input type="text" name="subtitle_url[]" value="" class="form-control">
                      </div>
                    </div>
                    <div class="form-group subtitle_local" style="display: none">
                      <label class="col-md-3 control-label">Browse Subtitle :-</label>
                      <div class="col-md-8">
                        <input type="file" name="subtitle_local[]" accept=".srt" value="" class="form-control">
                      </div>
                    </div>
                  </div>

                  <div class="moreContainer">
                    
                  </div>

                  <div class="form-group">
                    <label class="col-md-3 col-md-offset-3">
                      <button type="button" class="add_more btn btn-danger" style="font-size: 12px; padding: 6px 9px;">+ Add More</button>
                    </label>
                  </div>
                  
                </div>
                <?php }else if(isset($_GET['episode_id']))
                {

                ?>
                <div class="subtitle_fields" style="display: none">
                <?php
                    $sql_subtitle="SELECT * FROM tbl_subtitles WHERE post_id='".$row['id']."' AND `type`='episode'";
                    $res_subtitle=mysqli_query($mysqli, $sql_subtitle);

                    if(mysqli_num_rows($res_subtitle) > 0)
                    {
                    while($row_subtitle=mysqli_fetch_assoc($res_subtitle)) {

                ?>
                  <div class="subtitle_container">
                      <input type="hidden" name="old_subtitle_id[]" value="<?=$row_subtitle['id']?>">

                      <input type="hidden" name="old_subtitle_type" value="<?=$row_subtitle['subtitle_type']?>">

                      <div class="form-group subtitle_lang">
                        <label class="col-md-3 control-label">Subtitle Language :-</label>
                        <div class="col-md-8">
                          <input type="text" name="subtitle_lang[]" value="<?=$row_subtitle['language']?>" class="form-control">
                        </div>
                      </div>
                      <div class="form-group subtitle_type">
                        <label class="col-md-3 control-label">Subtitle Upload Type :-</label>
                        <div class="col-md-8">                       
                          <select name="subtitle_type[]" class="select2">
                            <option value="" selected="">---Select Type---</option>
                            <option value="live_url" <?=($row_subtitle['subtitle_type']=='live_url') ? 'selected="selected"' : ''?>>Live Url</option>
                            <option value="local" <?=($row_subtitle['subtitle_type']=='local') ? 'selected="selected"' : ''?>>Local System</option>
                          </select>
                        </div>
                      </div>
                      <div class="form-group subtitle_url" style="display: none">
                        <label class="col-md-3 control-label">Enter URL :-</label>
                        <div class="col-md-8">
                          <input type="text" name="subtitle_url[]" value="<?=$row_subtitle['subtitle_url']?>" class="form-control">
                        </div>
                      </div>
                      <div class="form-group subtitle_local" style="display: none">
                        <label class="col-md-3 control-label">Browse Subtitle :-</label>
                        <div class="col-md-8">
                          <input type="hidden" name="subtitle_local_url[]" value="<?=$row_subtitle['subtitle_url']?>" class="form-control">
                          <input type="file" name="subtitle_local[]" accept=".srt" value="" class="form-control">

                          <?php 
                            if($row_subtitle['subtitle_type']=='local')
                            {
                          ?>
                          <div style="word-break: break-all;"><label class="control-label">Current URL :-</label><?php echo $file_path.'uploads/'.$row_subtitle['subtitle_url']?></div><br>
                          <?php } ?>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-md-offset-3 col-md-8 text-right">
                          <a href="javascript:void(0)" class="removeSubtitle" data-id="<?=$row_subtitle['id']?>">&times; Remove</a>
                        </label>
                      </div>
                  </div>

                    
                <?php } //end of while loop 
                    } //end if
                    else
                    {
                ?>
                  <div class="subtitle_container">
                    <div class="form-group subtitle_lang">
                      <label class="col-md-3 control-label">Subtitle Language :-</label>
                      <div class="col-md-8">
                        <input type="text" name="subtitle_lang[]" value="" class="form-control">
                      </div>
                    </div>
                    <div class="form-group subtitle_type">
                      <label class="col-md-3 control-label">Subtitle Upload Type :-</label>
                      <div class="col-md-8">                       
                        <select name="subtitle_type[]" class="select2">
                          <option value="" selected="">---Select Type---</option>
                          <option value="live_url">Live Url</option>
                          <option value="local">Local System</option>
                        </select>
                      </div>
                    </div>
                    <div class="form-group subtitle_url" style="display: none">
                      <label class="col-md-3 control-label">Enter URL :-</label>
                      <div class="col-md-8">
                        <input type="text" name="subtitle_url[]" value="" class="form-control">
                      </div>
                    </div>
                    <div class="form-group subtitle_local" style="display: none">
                      <label class="col-md-3 control-label">Browse Subtitle :-</label>
                      <div class="col-md-8">
                        <input type="file" name="subtitle_local[]" accept=".srt" value="" class="form-control">
                      </div>
                    </div>
                  </div>
                <?php
                    }
                ?>
                  <div class="moreContainer"></div>
                  <div class="form-group">
                    <label class="col-md-3 col-md-offset-3">
                      <button type="button" class="add_more btn btn-danger" style="font-size: 12px; padding: 6px 9px;">+ Add More</button>
                    </label>
                  </div>

                </div>

                <?php

                  mysqli_free_result($res_subtitle);

                } //end of else statement 
                ?>
                
              </div>

              <div class="form-group">
                <label class="col-md-3 control-label">Poster Image:-
                  <p class="control-label-help" id="square_lable_info">(Recommended resolution: landscape, 500x282)</p>
                </label>
                <div class="col-md-8">
                  <div class="fileupload_block" style="border-radius:10px;height:189px;">
                    <input type="file" name="episode_poster" value="" accept=".png, .jpg, .jpeg, .svg, .gif" <?php echo (!isset($_GET['episode_id'])) ? 'required="require"' : '' ?> id="fileupload" style="margin-top: 5%;">
                    <div class="fileupload_img" >
                      <?php 
                        $img_src="";

                        if(!isset($_GET['episode_id']) || !file_exists('images/episodes/'.$row['episode_poster'])){
                          $img_src='assets/images/browse.svg';
                        }else{
                          $img_src='images/episodes/'.$row['episode_poster'];
                        }

                      ?>
                      <input type="hidden" name="poster_img" value="">
                      <img type="image" src="<?=$img_src?>" class="poster_img" alt="poster image" style="width: 150px;height: 86px" />
                    </div>
                  </div>
                </div>
              </div>
              
               <div class="form-group">
                    <div class="col-md-3">
                      <label class="control-label">Episode Description :-</label>
                    </div>
                    <div class="col-md-8">
                      <textarea name="episode_description" id="episode_description" rows="5" class="form-control"><?php if(isset($_GET['episode_id'])){ echo $row['episode_description']; } ?></textarea>
                      <script>
                        CKEDITOR.replace('episode_description');
                      </script>
                    </div>
                  </div>
              
              <div class="form-group">&nbsp;</div>
              <div class="form-group">
                <div class="col-md-9 col-md-offset-3">
                  <button type="submit" name="submit" class="btn btn-primary" style="border-radius:10px;"><?php  if(isset($_GET['episode_id'])){  ?>Update <?php }else{?>Save<?php }?></button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>           
                 
        
<?php include("includes/footer.php");?>      

<script type="text/javascript">

  $(document).ready(function(event){

    $(".video_local").change(function(e){

        if(isVideo($(this).val()) && $(this).val()!=''){
          $(this).parent().find('.video-preview').attr('src', URL.createObjectURL(this.files[0]));
          $(this).parent().find('.uploadPreview').show();
        }
        else
        {

          $(this).parent().find('.video_local').val('');
          $(this).parent().find('.uploadPreview').hide();

          $('.notifyjs-corner').empty();
          $.notify(
            'Only video files are allowed to upload.',
            { position:"top center",className: 'error'}
          );

        }
      });
  });
  function isVideo(filename) {
      var ext = getExtension(filename);
      switch (ext.toLowerCase()) {
      case 'm4v':
      case 'avi':
      case 'mp4':
      case 'mov':
      case 'mpg':
      case 'mpeg':
          // etc
          return true;
      }
      return false;
  }

  function getExtension(filename) {
      var parts = filename.split('.');
      return parts[parts.length - 1];
  }

  function readURL(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      
      reader.onload = function(e) {
        $("input[name='episode_poster']").next(".fileupload_img").find("img").attr('src', e.target.result);
      }
      
      reader.readAsDataURL(input.files[0]);
    }
  }
  $("input[name='episode_poster']").change(function() { 
      var file=$(this);

      if(file[0].files.length != 0){
          if(isImage($(this).val())){
            readURL(this);
          }
          else
          {
            $(this).val('');
            $('.notifyjs-corner').empty();
            $.notify(
            'Only jpg/jpeg, png, gif and svg files are allowed!',
            { position:"top center",className: 'error'}
            );
          }
      }
  });


  $("#shows_id").on("change",function(e){
    $("#season_id").html('<option value="">--Select Season--</option>');
    var _id=$(this).val();
    $.ajax({
      type:'post',
      url:'processData.php',
      dataType:'json',
      data:{id:_id,'action':'getTvSeason'},
      success:function(res){
          console.log(res);
          if(res.status=='1'){
            $.each(res.data, function(index, item) {
                $("#season_id").append(item);
            });
          }
        }
    });
  });

  
  var _id=$("#shows_id").val();
  if(_id!=''){
    var _old_id=$(".old_season_id").val();
    $("#season_id").html('<option value="">--Select Season--</option>');
    $.ajax({
      type:'post',
      url:'processData.php',
      dataType:'json',
      data:{id:_id,'action':'getSeason'},
      success:function(res){
          console.log(res);
          if(res.status=='1'){
            $.each(res.data, function(index, item) {
                $("#season_id").append(item);

            });
            $('#season_id option[value="'+_old_id+'"]').prop('selected', true);
          }
        }
    });
  }
  
  $("#video_file_type").on("change",function(e){
    var _type=$(this).val();

    $("select[name='quality_status']").val('false').change();

    if(_type=='youtube_url' || _type=='server_url' || _type=='embedded_url'){
      $("#episode_url_holder").show();
      $("input[name='video_local']").attr("required",false);
      $("input[name='episode_url']").attr("required",true);
      $("#episode_local_holder").hide();

    }
    else if(_type=='local'){
      $("input[name='episode_url']").attr("required",false);
      $("input[name='video_local']").attr("required",true);
      $("#episode_local_holder").show();
      $("#episode_url_holder").hide();
    }
    else{
      $("#quality_holder").hide();
      $("#episode_local_holder").hide();
      $("#episode_url_holder").hide();
    }

    if(_type=='server_url' || _type=='local'){
      $("#quality_holder").show();
      $(".subtitle").show();
    }
    else{
      $("#quality_holder").hide();
      $(".subtitle").hide(); 
    }

  });


  var _type=$("#video_file_type").val();

  if(_type=='youtube_url' || _type=='server_url' || _type=='embedded_url'){
    $("#episode_url_holder").show();
    $("#episode_local_holder").hide();
  }else if(_type=='local'){
    $("#episode_local_holder").show();
    $("#episode_url_holder").hide();
  }else{
    $("#episode_local_holder").hide();
    $("#episode_url_holder").hide();
  }

  if(_type=='server_url' || _type=='local'){
    $("#quality_holder").show();
    $(".subtitle").show();
  }
  else{
    $("#quality_holder").hide();
    $(".subtitle").hide(); 
  }



  // for subtitle process

  $("select[name='subtitle']").change(function(e){
    var val=$(this).val();

    if(val==='true'){
      $(".subtitle_fields").show();
      $(this).parents(".subtitle").find("input[name='subtitle_lang[]']").attr("required", true);
      $(this).parents(".subtitle").find("select[name='subtitle_type[]']").attr("required", true);

    }else{
      $(".subtitle_fields").hide();
      $(this).parents(".subtitle").find("input[name='subtitle_lang[]']").attr("required", false);
      $(this).parents(".subtitle").find("select[name='subtitle_type[]']").attr("required", false);
    }
  });

  var val=$("select[name='subtitle']").val();

  if(val==='true'){
    $(".subtitle_fields").show();
    $("select[name='subtitle']").parents(".subtitle").find("input[name='subtitle_lang[]']").attr("required", true);
    $("select[name='subtitle']").parents(".subtitle").find("select[name='subtitle_type[]']").attr("required", true);

  }else{
    $(".subtitle_fields").hide();
    $("select[name='subtitle']").parents(".subtitle").find("input[name='subtitle_lang[]']").attr("required", false);
    $("select[name='subtitle']").parents(".subtitle").find("select[name='subtitle_type[]']").attr("required", false);
  }

  $("select[name='subtitle_type[]']").change(function(e){
    var val=$(this).val();

    if(val==='live_url'){
      $(this).parents(".subtitle_container").find(".subtitle_local").hide();
      $(this).parents(".subtitle_container").find(".subtitle_url").show();

      $(this).parents(".subtitle_container").find(".subtitle_local input[name='subtitle_local[]']").attr("required", false);
      $(this).parents(".subtitle_container").find(".subtitle_url input[name='subtitle_url[]']").attr("required", true);

    }
    else if(val==='local'){

      if($("input[name='episode_id']").val()=='' && $(this).parents(".subtitle_container").find("input[name='old_subtitle_type']").val()!='local'){
        $(this).parents(".subtitle_container").find(".subtitle_local input[name='subtitle_local[]']").attr("required", true);  
      }

      $(this).parents(".subtitle_container").find(".subtitle_url input[name='subtitle_url[]']").attr("required", false);

      $(this).parents(".subtitle_container").find(".subtitle_url").hide();
      $(this).parents(".subtitle_container").find(".subtitle_local").show();
    }
    else{
      $(this).parents(".subtitle_container").find(".subtitle_url").hide();
      $(this).parents(".subtitle_container").find(".subtitle_local").hide(); 

      $(this).parents(".subtitle_container").find(".subtitle_local input[name='subtitle_local[]']").attr("required", false);
      $(this).parents(".subtitle_container").find(".subtitle_url input[name='subtitle_url[]']").attr("required", false);

    }
  });


  $("select[name='subtitle_type[]']").each(function(index) {
      val=$(this).val();

      if(val==='live_url'){
        $(this).parents(".subtitle_container").find(".subtitle_local").hide();
        $(this).parents(".subtitle_container").find(".subtitle_url").show();

        $(this).parents(".subtitle_container").find(".subtitle_local input[name='subtitle_local[]']").attr("required", false);
        $(this).parents(".subtitle_container").find(".subtitle_url input[name='subtitle_url[]']").attr("required", true);

      }
      else if(val==='local'){

        if($("input[name='episode_id']").val()==''){
          $(this).parents(".subtitle_container").find(".subtitle_local input[name='subtitle_local[]']").attr("required", true);  
        }
        
        $(this).parents(".subtitle_container").find(".subtitle_url input[name='subtitle_url[]']").attr("required", false);

        $(this).parents(".subtitle_container").find(".subtitle_url").hide();
        $(this).parents(".subtitle_container").find(".subtitle_local").show();
      }
      else{
        $(this).parents(".subtitle_container").find(".subtitle_url").hide();
        $(this).parents(".subtitle_container").find(".subtitle_local").hide(); 

        $(this).parents(".subtitle_container").find(".subtitle_local input[name='subtitle_local[]']").attr("required", false);
        $(this).parents(".subtitle_container").find(".subtitle_url input[name='subtitle_url[]']").attr("required", false);

      }
  });

  $(".add_more").click(function(e){
    e.preventDefault();

    var is_empty=0;

    $(this).parents('.subtitle').find("input[name='subtitle_lang[]']").each(function() {
        if($(this).val()==''){
          is_empty++;
          $(this).attr("required", true);
        }
    });

    $(this).parents('.subtitle').find("select[name='subtitle_type[]']").each(function() {

        var subtitle_type=$(this).val();

        if(subtitle_type==''){
          is_empty++;
          $(this).attr("required", true);
        }
        else{

          if(subtitle_type=='live_url'){
            if($(this).parents('.subtitle_container').find("input[name='subtitle_url[]']").val()==''){
              $(this).parents('.subtitle_container').find("input[name='subtitle_url[]']").attr("required", true);
              is_empty++;
            }
            else if($(this).parents('.content').find("input[name='subtitle_url[]']").val()==''){
              $(this).parents('.content').find("input[name='subtitle_url[]']").attr("required", true);
              is_empty++;
            }
          }
          else if(subtitle_type=='local'){

            if($(this).parents('.moreContainer').html()==undefined){

                var file=$(this).parents('.subtitle_container').find("input[name='subtitle_local[]']");

                if(file[0].files.length == 0 && ($("input[name='episode_id']").val()=='' || $("input[name='is_subtitle']").val()=='false')){

                  $(this).parents('.subtitle_container').find("input[name='subtitle_local[]']").attr("required", true);
                  is_empty++;
                }
            }
            else{

                var file=$(this).parents('.moreContainer').find("input[name='subtitle_local[]']");

                if(file[0].files.length == 0){
                  $(this).parents('.moreContainer').find("input[name='subtitle_local[]']").attr("required", true);
                  is_empty++;
                }

            }
            
          }
        }
    });


    if(is_empty!=0){
      alert("Previous subtitle data must be filled !");
      return false;
    }


    $("select.select2-hidden-accessible").select2('destroy');

    var _html='<div class="form-group subtitle_lang"><label class="col-md-3 control-label">Subtitle Language :-</label><div class="col-md-8"><input type="text" name="subtitle_lang[]" value="" class="form-control" required="required"></div></div><div class="form-group subtitle_type"><label class="col-md-3 control-label">Subtitle Upload Type :-</label><div class="col-md-8"><select name="subtitle_type[]" class="select2" required="required"><option value="" selected>---Select Type---</option><option value="live_url">Live Url</option><option value="local">Local System</option></select></div></div><div class="form-group subtitle_url" style="display: none"><label class="col-md-3 control-label">Enter URL :-</label><div class="col-md-8"><input type="text" name="subtitle_url[]" value="" class="form-control"></div></div><div class="form-group subtitle_local" style="display: none"><label class="col-md-3 control-label">Browse Subtitle :-</label><div class="col-md-8"><input type="file" name="subtitle_local[]" accept=".srt" value="" class="form-control"></div></div>';

    $(".moreContainer").append('<div class="content">'+_html+'<div class="form-group"><label class="col-md-offset-3 col-md-8 text-right"><a href="" class="removeMore">&times; Remove</a></span></div></div>');


    $(".removeMore").click(function(event){
      event.preventDefault();
      $(this).parents('.content').remove()
    });

    $(".moreContainer").find(".removeSubtitle").parent().parent().remove();

    $('.select2').select2();

    $("select[name='subtitle_type[]']").change(function(event2){

        val=$(this).val();

        if(val==='live_url'){
          $(this).parents(".content").find(".subtitle_local").hide();
          $(this).parents(".content").find(".subtitle_url").show();

          $(this).parents(".content").find(".subtitle_local input[name='subtitle_local[]']").attr("required", false);
          $(this).parents(".content").find(".subtitle_url input[name='subtitle_url[]']").attr("required", true);

        }
        else if(val==='local'){

          if($("input[name='episode_id']").val()==''){
            $(this).parents(".content").find(".subtitle_local input[name='subtitle_local[]']").attr("required", true);  
          }
          
          $(this).parents(".content").find(".subtitle_url input[name='subtitle_url[]']").attr("required", false);

          $(this).parents(".content").find(".subtitle_url").hide();
          $(this).parents(".content").find(".subtitle_local").show();
        }
        else{
          $(this).parents(".content").find(".subtitle_url").hide();
          $(this).parents(".content").find(".subtitle_local").hide(); 

          $(this).parents(".content").find(".subtitle_local input[name='subtitle_local[]']").attr("required", false);
          $(this).parents(".content").find(".subtitle_url input[name='subtitle_url[]']").attr("required", false);

        }

    });

    $("input[name='subtitle_local[]']").change(function () {
        var val = $(this).val().toLowerCase(),
        regex = new RegExp("(.*?)\.(srt)$");

        if (!(regex.test(val))) {
          $(this).val('');
          $('.notifyjs-corner').empty();
          $.notify(
            'Please select only .srt file',
            { position:"top center",className: 'error'}
          );
        }
    });

  });

  $(".removeSubtitle").click(function(event){
    event.preventDefault();

    var element=$(this).parents(".subtitle_container");

    var id=$(this).data("id");

    var post_id=$("input[name='episode_id']").val();

    if(confirm("Do you really want to remove this subtitle?")){
        $.ajax({
          type:'post',
          url:'processData.php',
          dataType:'json',
          data:{id:id,'action':'remove_subtitle','type':'episode','post_id':post_id},
          success:function(res){
            if(res.status=='1'){
              element.fadeOut();

              if(res.subtitle_status=='false'){
                $("select[name='subtitle']").val('false').change();
              }
            }
            alert(res.message);
          }
      });
    }
  });


  // for qualities

  $("select[name='quality_status']").change(function(e){

      var _type=$("#video_file_type").val();

      if($(this).val()=='true'){
          if(_type=='server_url'){
            $("#quality_local").hide();
            $("#quality_server_url").show();
          }
          else if(_type=='local'){
            $("#quality_server_url").hide();
            $("#quality_local").show();
          }
      }
      else{
          $("#quality_local").hide();
          $("#quality_server_url").hide();
      }
  });

  var _quality_status=$("select[name='quality_status']").val();

  if(_quality_status=='true'){
      if(_type=='server_url'){
        $("#quality_local").hide();
        $("#quality_server_url").show();
      }
      else if(_type=='local'){
        $("#quality_server_url").hide();
        $("#quality_local").show();
      }
  }
  else{
      $("#quality_local").hide();
      $("#quality_server_url").hide();
  }

  $(".remove_quality").click(function(e){
    e.preventDefault();

    var id=$(this).data("id");
    var column=$(this).data("column");

    if(confirm("Do you really want to remove this quality?")){
        $.ajax({
          type:'post',
          url:'processData.php',
          dataType:'json',
          data:{id:id,'action':'remove_quality','column':column},
          success:function(res){
            if(res.status=='1'){
              location.reload();
            }
            alert(res.message);
          }
      });
    }

  });
  
  
  	$("#trailer_file_type").on("change",function(e){
    var _type=$(this).val();

    $("select[name='quality_status']").val('false').change();

    if(_type=='youtube_url' || _type=='server_url' || _type=='embedded_url'){
      $("#trailer_url_holder").show();
      $("input[name='trailer_local']").attr("required",false);
      $("input[name='trailer_url']").attr("required",true);
      $("#trailer_local_holder").hide();

    }
    else if(_type=='local'){
      $("input[name='trailer_url']").attr("required",false);
      $("input[name='trailer_local']").attr("required",true);
      $("#trailer_local_holder").show();
      $("#trailer_url_holder").hide();
    }
    else{
      $("#quality_holder").hide();
      $("#trailer_local_holder").hide();
      $("#trailer_url_holder").hide();
    }

    

  });
  
  var _type=$("#trailer_file_type").val();

  if(_type=='youtube_url' || _type=='server_url' || _type=='embedded_url'){
    $("#trailer_url_holder").show();
    $("#trailer_local_holder").hide();
  }else if(_type=='local'){
    $("#trailer_local_holder").show();
    $("#trailer_url_holder").hide();
  }else{
    $("#trailer_local_holder").hide();
    $("#trailer_url_holder").hide();
  }

</script>   

<script type="text/javascript">
  // fetch imdb details

  $(".btn_fetch").click(function(e){
    e.preventDefault();

    var btn=$(this);

    $(this).attr("disabled", true);
    $(this).text("Please wait..");

    var imdb_id_title=$("#imdb_id_title").val();

    if(imdb_id_title==''){
      swal("Enter IMDb ID (e.g. tt2161930)");
      btn.attr("disabled", false);
      btn.text("Fetch");
      return;
    }
    else{

      $.ajax({
        type:'post',
        url:'processImdb.php',
        dataType:'json',
        data:{'action':'getEpisodeDetails', 'id' : imdb_id_title},
        success:function(res){

            btn.attr("disabled", false);
            btn.text("Fetch");

            $('.notifyjs-corner').empty();
            $.notify(
              res.message,
              { position:"top center",className: res.class}
            );
            if(res.status=='1'){
                
                $("input[name='episode_poster']").attr("required",false);
                
                $("input[name='episode_title']").val(res.title);
                $("input[name='poster_img']").val(res.thumbnail);
                $(".poster_img").attr('src', res.thumbnail);
            }
          }
      });

    }

  });

  $(function(){
    $("input[name='subtitle_local[]']").change(function () {
        var val = $(this).val().toLowerCase(),
            regex = new RegExp("(.*?)\.(srt)$");

        if (!(regex.test(val))) {
            $(this).val('');
            $('.notifyjs-corner').empty();
            $.notify(
              'Please select only .srt file',
              { position:"top center",className: 'error'}
            );
        }
    });
  });

</script> 
