<?php 
	if(isset($_GET['cat_id'])){ 
		$page_title= 'Edit Category';
	}
	else{ 
		$page_title='Add Category'; 
	}

	$current_page="category";
	$active_page="channel";

	include("includes/header.php");
	require("includes/function.php");
	require("language/language.php");

	require_once("thumbnail_images.class.php");
	
	if(isset($_POST['submit']) and isset($_GET['add']))
	{
	
		$category_image=rand(0,99999)."_".$_FILES['category_image']['name'];
		$pic1=$_FILES['category_image']['tmp_name'];

					
		$tpath1='images/'.$category_image; 
		copy($pic1,$tpath1);

		$thumbpath='images/thumbs/'.$category_image;
			
		$obj_img = new thumbnail_images();
		$obj_img->PathImgOld = $tpath1;
		$obj_img->PathImgNew =$thumbpath;
		$obj_img->NewWidth = 300;
		$obj_img->NewHeight = 300;
		if (!$obj_img->create_thumbnail_images()) 
		{
			echo "Thumbnail not created... please upload image again";
		}
          
        $data = array( 
			'category_name'  =>  cleanInput($_POST['category_name']),
			'category_image'  =>  $category_image
		);		

 		$qry = Insert('tbl_category',$data);			

		$_SESSION['msg']="10";
 
		header( "Location:manage_category.php");
		exit;	
		
	}
	
	if(isset($_GET['cat_id']))
	{	 
		$qry="SELECT * FROM tbl_category where cid='".$_GET['cat_id']."'";
		$result=mysqli_query($mysqli,$qry);
		$row=mysqli_fetch_assoc($result);
	}
	if(isset($_POST['submit']) and isset($_POST['cat_id']))
	{
		 
		if($_FILES['category_image']['name']!="")
		{		

			if($row['category_image']!="")
			{
				unlink('images/thumbs/'.$row['category_image']);
				unlink('images/'.$row['category_image']);
			}

			$category_image=rand(0,99999)."_".$_FILES['category_image']['name'];
			$pic1=$_FILES['category_image']['tmp_name'];
			$tpath1='images/'.$category_image;

			copy($pic1,$tpath1);

			$thumbpath='images/thumbs/'.$category_image;

			$obj_img = new thumbnail_images();
			$obj_img->PathImgOld = $tpath1;
			$obj_img->PathImgNew =$thumbpath;
			$obj_img->NewWidth = 300;
			$obj_img->NewHeight = 300;
			if (!$obj_img->create_thumbnail_images()) 
			{
				echo "Thumbnail not created... please upload image again";
			}

			$data = array(
				'category_name'  =>  cleanInput($_POST['category_name']),
				'category_image'  =>  $category_image
			);

			$category_edit=Update('tbl_category', $data, "WHERE cid = '".$_POST['cat_id']."'");
		}
		else
		{

			$data = array(
				'category_name'  =>  cleanInput($_POST['category_name'])
			);	

			$category_edit=Update('tbl_category', $data, "WHERE cid = '".$_POST['cat_id']."'");

		}

	    $_SESSION['msg']="11"; 

	    if(isset($_GET['redirect']))
	      header( "Location:".$_GET['redirect']);
	    else  
	      header( "Location:add_category.php?cat_id=".$_POST['cat_id']);
		
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
           <a href="manage_category.php" style="background:white;margin-top:10px;font-size:25px;">X</a>
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="card-body mrg_bottom"> 
        <form action="" name="addeditcategory" method="post" class="form form-horizontal" enctype="multipart/form-data">
        	<input  type="hidden" name="cat_id" value="<?php echo $_GET['cat_id'];?>" />

          <div class="section">
            <div class="section-body">
              <div class="form-group">
                <label class="col-md-3 control-label">Category Name :-</label>
                <div class="col-md-6">
                  <input type="text" name="category_name" id="category_name" value="<?php if(isset($_GET['cat_id'])){echo $row['category_name'];}?>" class="form-control" required>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label">Select Image :-
                <p class="control-label-help">(Recommended resolution: 300x150)</p>
                </label>
                <div class="col-md-6">
                  <div class="fileupload_block" style="border-radius:10px;height:189px;">
                  	<input type="file" name="category_image" value="fileupload" accept=".png, .jpg, .jpeg, .svg, .gif" <?php echo (!isset($_GET['cat_id'])) ? 'required="require"' : '' ?> id="fileupload">
                  	<div class="fileupload_img">
                  	<?php 
                    	$img_src="";

                    	if(!isset($_GET['cat_id']) || !file_exists('images/'.$row['category_image'])){
                      		$img_src='assets/images/browse.svg';
                    	}else{
                      		$img_src='images/'.$row['category_image'];
                    	}
                  	?>
                  	<img type="image" src="<?=$img_src?>" alt="poster image" style="width: 150px;height: 86px" />
                    </div>	 
                  </div>
                </div>
              </div>
              <div class="form-group">
                <div class="col-md-9 col-md-offset-3">
                  <button type="submit" name="submit" class="btn btn-primary" style="border-radius:10px;"><?php if(isset($_GET['cat_id'])){ ?>Update <?php }else{ ?> Save <?php } ?> </button>
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
        $("input[name='category_image']").next(".fileupload_img").find("img").attr('src', e.target.result);
      }
      
      reader.readAsDataURL(input.files[0]);
    }
  }
  $("input[name='category_image']").change(function() { 
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
</script>       
