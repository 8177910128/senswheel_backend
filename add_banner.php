<?php 
  
  $page_title=(isset($_GET['id'])) ? 'Edit Banner' : 'Add Banner'; 

  include('includes/header.php');
  include('includes/function.php');
  include('language/language.php'); 

  //require_once("thumbnail_images.class.php");
  $date=date('Y-m-d H:i:s');
   
  if(isset($_POST['submit']) and isset($_GET['add']))
  {   
      
      $file_name= str_replace(" ","-",$_FILES['image']['name']);
      $banner_image=rand(0,99999)."_".$file_name;
      $tpath1='images/'.$banner_image; 			 
      $pic1=compress_image($_FILES["image"]["tmp_name"], $tpath1, 80);
      
       $thumbpath='images/thumbs/'.$banner_image;		
       $thumb_pic1=create_thumb_image($tpath1,$thumbpath,'250','150');  
        
        $data = array(
          'type'  =>  cleanInput($_POST['type']),
          'dt'  =>  $date,
           'image'  =>  $tpath1
          
        );
    
        $qry = Insert('tbl_banners',$data);
    
        $_SESSION['msg']="10";
        header("location:manage_banners.php");   
        exit;
    
  }
  
  if(isset($_GET['id']))
  {
    $user_qry="SELECT * FROM tbl_banners where id='".$_GET['id']."'";
    $user_result=mysqli_query($mysqli,$user_qry);
    $user_row=mysqli_fetch_assoc($user_result);
  }
  
  if(isset($_POST['submit']) and isset($_GET['id']))
  {
      
      if($_FILES['image']['name']!="")
	  {
	      if($user_row['image']!="")
			{
				unlink('images/thumbs/'.$user_row['image']);
				unlink('images/'.$user_row['image']);
			}

			$file_name= str_replace(" ","-",$_FILES['image']['name']);
			$banner_image=rand(0,99999)."_".$file_name;
	        $tpath1='images/'.$banner_image; 			 
            $pic1=compress_image($_FILES["image"]["tmp_name"], $tpath1, 80);
            
            $thumbpath='images/thumbs/'.$banner_image;		
            $thumb_pic1=create_thumb_image($tpath1,$thumbpath,'250','150');  
            $data = array(
              'type'  =>  cleanInput($_POST['type']),
              'image'  =>  $tpath1
          
              );
        
        $user_edit=Update('tbl_banners', $data, "WHERE id = '".$_GET['id']."'");
      
	  }else{
	      $data = array(
              'type'  =>  cleanInput($_POST['type'])

              );
        
	      $user_edit=Update('tbl_banners', $data, "WHERE id = '".$_GET['id']."'"); 
	  }
 
    
   
    
    $_SESSION['msg']="11";

    if(isset($_GET['redirect'])){
      header("Location:manage_banners.php");
    }
    else{
      header("Location:manage_banners.php?id=".$_POST['id']);
    }
    exit;
   
  }
  
  
?>
  

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="page_title_block">
        <div class="col-md-5 col-xs-12">
          <div class="page_title"><?=$page_title?></div>
        </div>
        <div class="col-md-7 col-xs-12" style="text-align:right;">
               <a href="manage_plan.php" style="background:white;margin-top:10px;font-size:25px;">X</a>
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="card-body mrg_bottom"> 
        <form action="" method="post" class="form form-horizontal" enctype="multipart/form-data" >
          <input  type="hidden" name="user_id" value="<?php echo $_GET['user_id'];?>" />

          <div class="section">
            <div class="section-body">
              
              
             <div class="form-group">
                <label class="col-md-3 control-label">Select Type :-</label>
                <div class="col-md-6">
                  <select name="type" id="type" class="select2">
                    <option value="">--Select Type--</option>
      				<option  value="home"  <?php if(isset($_GET['id']) && $user_row['type']=='home'){ echo 'selected'; } ?>>Home</option>
      				<option  value="movies" <?php if(isset($_GET['id']) && $user_row['type']=='movies'){ echo 'selected'; } ?>>Movies</option>
      				<option  value="series" <?php if(isset($_GET['id']) && $user_row['type']=='series'){ echo 'selected'; } ?>>Series</option>
                  </select>
                </div>
              </div>  
              
              
               <div class="form-group" >
	                <label class="col-md-3 control-label">Select Image :-
	                <p class="control-label-help"></p>
	                </label>
	                <div class="col-md-6">
	                  <div class="fileupload_block" style="border-radius:10px;height:189px;">
	                  	<input type="file" name="image"  value="fileupload" id="fileupload" accept=".png, .jpg, .jpeg, .svg, .gif" <?php echo (!isset($_GET['id'])) ? 'required="require"' : '' ?>>
	                  	<div class="fileupload_img">
	                  	<?php 
	                    	$img_src="";
	                    	if(!isset($_GET['id']) || !file_exists($user_row['image'])){
	                      		$img_src='assets/images/browse.svg';
	                    	}else{
	                      		$img_src=$user_row['image'];
	                    	}
	                     ?>
	                  	<img type="image" src="<?=$img_src?>" alt="image" style="width: 150px;height: 86px" />
	                    </div>	 
	                  </div>
	                </div>
	              </div>
             
              <div class="form-group">
                <div class="col-md-9 col-md-offset-3">
                  <button type="submit" name="submit" class="btn btn-primary" style="border-radius:10px;"><?php if(isset($_GET['id'])){ ?> Update  <?php }else { ?> Save <?php }?>
                                                                             
                  
                  
                  </button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
   

<?php include('includes/footer.php');?>