<?php
  
  $page_title="Edit Channel";
  $current_page="channel";
  $active_page="channel";
   
  include("includes/header.php");

	require("includes/function.php");
	require("language/language.php");

	require_once("thumbnail_images.class.php");
	
	
	//Get all Category 
	$qry="SELECT * FROM tbl_category";
	$result=mysqli_query($mysqli,$qry);
	
	if(isset($_GET['channel_id']))
	{
		$viv_qry="SELECT * FROM  tbl_channels WHERE id='".$_GET['channel_id']."'";
		$viv_res=mysqli_query($mysqli,$viv_qry);
		$viv_row=mysqli_fetch_assoc($viv_res);
		 
		 
	}
	 	
	if(isset($_POST['submit']) and isset($_GET['channel_id']))
	{  

    if($_FILES['channel_poster']['error']!=4){

      unlink('images/'.$viv_row['channel_poster']);
      unlink('images/thumbs/'.$viv_row['channel_poster']);

      // for movie poster
      $channel_poster=rand(0,99999)."_".$_FILES['channel_poster']['name'];
      $pic1=$_FILES['channel_poster']['tmp_name'];

            
      $tpath1='images/'.$channel_poster; 
      copy($pic1,$tpath1);

      $thumbpath='images/thumbs/'.$channel_poster;
        
      $obj_img = new thumbnail_images();
      $obj_img->PathImgOld = $tpath1;
      $obj_img->PathImgNew =$thumbpath;
      $obj_img->NewWidth = 270;
      $obj_img->NewHeight = 390;
      if (!$obj_img->create_thumbnail_images()) 
      {
        echo $_SESSION['msg']="Thumbnail not created... please upload image again";
        exit;
      }
    }else{
      $channel_poster=$viv_row['channel_poster'];
    }

    if($_FILES['channel_cover']['error']!=4){
      unlink('images/'.$viv_row['channel_cover']);
      unlink('images/thumbs/'.$viv_row['channel_cover']);

      // for movies cover
      $channel_cover=rand(0,99999)."_".$_FILES['channel_cover']['name'];
      $pic1=$_FILES['channel_cover']['tmp_name'];

            
      $tpath1='images/'.$channel_cover; 
      copy($pic1,$tpath1);

      $thumbpath='images/thumbs/'.$channel_cover;
        
      $obj_img = new thumbnail_images();
      $obj_img->PathImgOld = $tpath1;
      $obj_img->PathImgNew =$thumbpath;
      $obj_img->NewWidth = 600;
      $obj_img->NewHeight = 350;
      if (!$obj_img->create_thumbnail_images()) 
      {
        echo $_SESSION['msg']="Thumbnail not created... please upload image again";
        exit;
      }

    }else{
      $channel_cover=$viv_row['channel_thumbnail'];
    }

    $agent_name=($_POST['user_agent_type']=='custom') ? trim($_POST['user_agent_name']) : '';
	          
    $data = array( 
      'cat_id'  =>  $_POST['category_id'],
      'channel_type'  =>  $_POST['channel_type'],
      'channel_title'  =>  $_POST['channel_title'],
      'channel_url'  =>  $_POST['channel_url'],
      'channel_type_ios'  =>  $_POST['channel_type_ios'],
      'channel_url_ios'  =>  $_POST['channel_url_ios'],
      'channel_desc'  =>  addslashes($_POST['channel_desc']),
      'channel_poster'  =>  $channel_poster,
      'channel_thumbnail'  =>  $channel_cover,
      'user_agent'  =>  trim($_POST['user_agent']),
      'user_agent_type'  =>  trim($_POST['user_agent_type']),
      'user_agent_name'  =>  $agent_name,
    );	
		
		
		$channel_edit=Update('tbl_channels', $data, "WHERE id = '".$_POST['channel_id']."'"); 

		$_SESSION['msg']="11"; 
		

    if(isset($_GET['redirect']))
      header( "Location:".$_GET['redirect']);
    else  
      header( "Location:edit_channel.php?channel_id=".$_POST['channel_id']);
    exit;		 
		  
	}	
	 
?>
   
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="page_title_block">
            <div class="col-md-5 col-xs-12">
              <div class="page_title">Edit Channel</div>
            </div>
            <div class="col-md-7 col-xs-12" style="text-align:right;">
           <a href="manage_channels.php" style="background:white;margin-top:10px;font-size:25px;">X</a>
        </div>
          </div>
          <div class="clearfix"></div>
          <div class="card-body mrg_bottom">
            <form class="form form-horizontal" action="" method="post"  enctype="multipart/form-data">
            	<input  type="hidden" name="channel_id" value="<?php echo $_GET['channel_id'];?>" />
              <div class="section">
                <div class="section-body">
                  <div class="form-group">
                    <label class="col-md-3 control-label">Select Category :-</label>
                    <div class="col-md-6">
                      <select name="category_id" id="category_id" class="select2">
                        <option value="">--Select Category--</option>
          							<?php
          									while($row=mysqli_fetch_array($result))
          									{
          							?>
          						 
           							<?php if($viv_row['cat_id']==$row['cid']){ ?>
          							
          							<option value="<?php echo $row['cid'];?>"  selected="selected"><?php echo $row['category_name'];?> </option>								
          							
          							<?php }else{?>

          							<option value="<?php echo $row['cid'];?>"><?php echo $row['category_name'];?></option>								
          							<?php }?>

          							 
          							<?php
          								}
          							?>
                      </select>
                    </div>
                  </div>                  
                  <div class="form-group">
                    <label class="col-md-3 control-label">Channel Title :-</label>
                    <div class="col-md-6">
                      <input type="text" name="channel_title" id="channel_title" value="<?php echo $viv_row['channel_title'];?>" class="form-control">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-3 control-label">Type :-</label>
                    <div class="col-md-6">
                      <select name="channel_type" id="channel_type" class="select2">
                        <option value="live_url" <?php if($viv_row['channel_type']=="live_url"){?>selected<?php }?>>Live URL</option>
                        <option value="youtube" <?php if($viv_row['channel_type']=="youtube"){?>selected<?php }?>>YouTube</option>
                        <option value="embedded_url" <?php if($viv_row['channel_type']=="embedded_url"){?>selected<?php }?>>Embedded URL (Open Load, Very Stream, Daily motion, Vimeo)</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-3 control-label">Channel Url :-</label>
                    <div class="col-md-6">
                      <input type="text" name="channel_url" id="channel_url" value="<?php echo $viv_row['channel_url'];?>" class="form-control">
                    </div>
                  </div>
                  <div class="or_link_item">
                  <h2>OR</h2>
                  </div>
                  <div class="form-group">
                    <label class="col-md-3 control-label">iOS Type :-</label>
                    <div class="col-md-6">
                      <select name="channel_type_ios" id="channel_type_ios" class="select2">
                        <option value="live_url" <?php if($viv_row['channel_type_ios']=="live_url"){?>selected<?php }?>>Live URL</option>
                        <option value="youtube" <?php if($viv_row['channel_type_ios']=="youtube"){?>selected<?php }?>>YouTube</option>
                        <option value="embedded_url" <?php if($viv_row['channel_type_ios']=="embedded_url"){?>selected<?php }?>>Embedded URL (Open Load, Very Stream, Daily motion, Vimeo)</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-3 control-label">iOS Channel Url :-
                      <p class="control-label-help">(M3u8,MP4)</p>
                    </label>
                    <div class="col-md-6">
                      <input type="text" value="<?php echo $viv_row['channel_url_ios'];?>" name="channel_url_ios" id="channel_url_ios" class="form-control">
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-md-3 control-label">Channel Poster Image:-
                      <p class="control-label-help" id="square_lable_info">(Recommended resolution: 185x278 portrait)</p>
                    </label>
                    <div class="col-md-6">
                      <div class="fileupload_block">
                        <input type="file" name="channel_poster" value="" id="fileupload">
                        <div class="fileupload_img">
                          <?php 
                            $img_src="";

                            if(!isset($_GET['channel_id']) || !file_exists('images/'.$viv_row['channel_poster'])){
                              $img_src='assets/images/series-poster.jpg';
                            }else{
                              $img_src='images/'.$viv_row['channel_poster'];
                            }
                          ?>
                          <img type="image" src="<?=$img_src?>" alt="no poster image" style="width: 80px;height: 115px" />
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-md-3 control-label">Channel Cover Image:-
                      <p class="control-label-help" id="square_lable_info">(Recommended resolution: 300x150 landscape)</p>
                    </label>
                    <div class="col-md-6">
                      <div class="fileupload_block">
                        <input type="file" name="channel_cover" value="" id="fileupload">
                        <div class="fileupload_img">
                          <?php 
                            $img_src="";

                            if(!isset($_GET['channel_id']) || !file_exists('images/'.$viv_row['channel_thumbnail'])){
                              $img_src='assets/images/series-cover.jpg';
                            }else{
                              $img_src='images/'.$viv_row['channel_thumbnail'];
                            }

                          ?>
                          <img type="image" src="<?=$img_src?>" alt="poster image" style="width: 150px;height: 86px"/>
                        </div>
                      </div>
                    </div>
                  </div>

                  
                  <div class="form-group">
                    <div class="col-md-3">
                      <label class="control-label">Channel Description :-</label>
                    </div>
                    <div class="col-md-6">
                      <textarea name="channel_desc" id="channel_desc" rows="5" class="form-control"><?php echo $viv_row['channel_desc'];?></textarea>
                      <script>                             
                        CKEDITOR.replace('channel_desc');
                      </script>
                    </div>
                  </div>
                  <br/>
                  <div class="form-group" style="display:none;">
                    <label class="col-md-3 control-label">User Agent :-</label>
                    <div class="col-md-6">
                      <select name="user_agent" id="user_agent" class="select2">
                        <option value="false" <?=($viv_row['user_agent']=='false') ? 'selected="selected"' : '';?>>False</option>
                        <option value="true" <?=($viv_row['user_agent']=='true') ? 'selected="selected"' : '';?>>True</option>
                      </select>
                    </div>
                  </div>
                  <div class="user_agent" style="display: none">
                      <div class="form-group">
                        <label class="col-md-3 control-label">User Agent Type :-</label>
                        <div class="col-md-6">
                          <select name="user_agent_type" id="user_agent_type" class="select2">
                            <option value="setting" <?=($viv_row['user_agent_type']=='setting') ? 'selected="selected"' : '';?>>Get from setting</option>
                            <option value="custom" <?=($viv_row['user_agent_type']=='custom') ? 'selected="selected"' : '';?>>Custom</option>
                          </select>
                        </div>
                      </div>
                      <div class="form-group" style="display: none">
                        <label class="col-md-3 control-label">User Agent Name :-</label>
                        <div class="col-md-6">
                          <input type="text" name="user_agent_name" value="<?=($viv_row['user_agent_type']=='custom') ? $viv_row['user_agent_name'] : '';?>" id="user_agent_name" class="form-control">
                        </div>
                      </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-9 col-md-offset-3">
                      <button type="submit" name="submit" class="btn btn-primary" style="border-radius:10px;">Update</button>
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
  function readURL(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      
      reader.onload = function(e) {
        $("input[name='channel_poster']").next(".fileupload_img").find("img").attr('src', e.target.result);
      }
      
      reader.readAsDataURL(input.files[0]);
    }
  }
  $("input[name='channel_poster']").change(function() { 
    readURL(this);
  });

  function readURL1(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      
      reader.onload = function(e) {
        $("input[name='channel_cover']").next(".fileupload_img").find("img").attr('src', e.target.result);
      }
      
      reader.readAsDataURL(input.files[0]);
    }
  }
  $("input[name='channel_cover']").change(function() { 
    readURL1(this);
  });


  var val=$("select[name='user_agent']").val();

  if(val==='true'){
    $(".user_agent").show();
  }
  else{
    $(".user_agent").hide();
  }


  $("select[name='user_agent']").change(function(e){
    var val=$(this).val();

    if(val==='true'){
      $(".user_agent").show();
    }
    else{
      $(".user_agent").hide();
    }

  });

  $("select[name='user_agent_type']").change(function(e){
    var val=$(this).val();

    if(val==='custom'){
      $(this).parent().parent().next(".form-group").show();
    }
    else{
      $(this).parent().parent().next(".form-group").hide();
    }

  });

  var val=$("select[name='user_agent_type']").val();

  if(val==='custom'){
    $("select[name='user_agent_type']").parent().parent().next(".form-group").show();
  }
  else{
    $("select[name='user_agent_type']").parent().parent().next(".form-group").hide();
  }
</script>  
