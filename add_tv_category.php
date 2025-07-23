<?php 
	if(isset($_GET['id'])){ 
		$page_title= 'Edit TV Category';
	}
	else{ 
		$page_title='Add TV Category'; 
	}
	$current_page="TV Category";
	

	include("includes/header.php");
	require("includes/function.php");
	require("language/language.php");

	require_once("thumbnail_images.class.php");
	

	

	if(isset($_POST['submit']) and isset($_GET['add']))
	{
	
	   $file_name= str_replace(" ","-",$_FILES['category_image']['name']);

	   $category_image=rand(0,99999)."_".$file_name;
		 	 
       //Main Image
	   $tpath1='images/'.$category_image; 			 
       $pic1=compress_image($_FILES["category_image"]["tmp_name"], $tpath1, 80);
	 
	   //Thumb Image 
	   $thumbpath='images/thumbs/'.$category_image;		
       $thumb_pic1=create_thumb_image($tpath1,$thumbpath,'250','150');   
 
          
       $data = array( 
		    'category_name'  =>  cleanInput($_POST['category_name']),
		    'category_image'  =>  $tpath1
	    );		

 		$qry = Insert('tbl_tv_category',$data);	

 	      
		$_SESSION['msg']="10";
 
		header( "Location:manage_tv_category.php");
		exit;
		
	}
	
	if(isset($_GET['id']))
	{
			 
		$qry="SELECT * FROM tbl_tv_category where id='".$_GET['id']."'";
		$result=mysqli_query($mysqli,$qry);
		$row=mysqli_fetch_assoc($result);

	}
	if(isset($_POST['submit']) and isset($_POST['id']))
	{
		 
		 if($_FILES['category_image']['name']!="")
		 {		

			if($row['category_image']!="")
			{
				unlink('images/thumbs/'.$row['category_image']);
				unlink('images/'.$row['category_image']);
			}

			$file_name= str_replace(" ","-",$_FILES['category_image']['name']);

			$category_image=rand(0,99999)."_".$file_name;

			//Main Image
			$tpath1='images/'.$category_image; 			 
			$pic1=compress_image($_FILES["category_image"]["tmp_name"], $tpath1, 80);

			//Thumb Image 
			$thumbpath='images/thumbs/'.$category_image;		
			$thumb_pic1=create_thumb_image($tpath1,$thumbpath,'250','150');

			$data = array(
				'category_name'  =>  cleanInput($_POST['category_name']),
				'category_image'  =>  $tpath1
			);

			$genre_edit=Update('tbl_tv_category', $data, "WHERE id = '".$_POST['id']."'");

		 }
		 else
		 {

			$data = array(
	          'category_name'  =>  cleanInput($_POST['category_name'])
			);	

	         $genre_edit=Update('tbl_tv_category', $data, "WHERE id = '".$_POST['id']."'");

		 }
		 
		$_SESSION['msg']="11"; 

		if(isset($_GET['redirect']))
	      header( "Location:".$_GET['redirect']);
	    else  
	      header( "Location:add_tv_category.php?id=".$_POST['id']);
	    exit;
 
	}


?>
<div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="page_title_block">
            <div class="col-md-5 col-xs-12">
              <div class="page_title"><?php echo $page_title; ?></div>
            </div>
            <div class="col-md-7 col-xs-12" style="text-align:right;">
               <a href="manage_tv_category.php" style="background:white;margin-top:10px;font-size:25px;">X</a>
            </div>
          </div>
          <div class="clearfix"></div>
          <div class="row mrg-top">
            <div class="col-md-12">
               
              <div class="col-md-12 col-sm-12">
                <?php if(isset($_SESSION['msg'])){?> 
               	 <div class="alert alert-success alert-dismissible" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                	<?php echo $client_lang[$_SESSION['msg']] ; ?></a> </div>
                <?php unset($_SESSION['msg']);}?>	
              </div>
            </div>
          </div>
          <div class="card-body mrg_bottom"> 
            <form action="" name="addeditcategory" method="post" class="form form-horizontal" enctype="multipart/form-data">
            	<input type="hidden" name="id" value="<?php echo $_GET['id'];?>" />

              <div class="section">
                <div class="section-body">
                  <div class="form-group">
                    <label class="col-md-3 control-label">Category Name :-</label>
                    <div class="col-md-6">
                      <input type="text" name="category_name" placeholder="Enter Category name" id="category_name" value="<?php if(isset($_GET['id'])){echo $row['category_name'];}?>" class="form-control" required>
                    </div>
                  </div>
                  <div class="form-group" >
	                <label class="col-md-3 control-label">Select Image :-
	                <p class="control-label-help">(Recommended resolution: 250x150,350x210)</p>
	                </label>
	                <div class="col-md-6">
	                  <div class="fileupload_block" style="border-radius:10px;height:189px;">
	                  	<input type="file" name="category_image"  value="fileupload" id="fileupload" accept=".png, .jpg, .jpeg, .svg, .gif" <?php echo (!isset($_GET['id'])) ? 'required="require"' : '' ?>>
	                  	<div class="fileupload_img">
	                  	<?php 
	                    	$img_src="";

	                    	if(!isset($_GET['id']) || !file_exists('images/'.$row['category_image'])){
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
                      <button type="submit" name="submit" class="btn btn-primary" style="background:#612BAD;border-radius:8px;"><?php  if(isset($_GET['id'])){ ?>Update <?php }else { ?> Save <?php }?></button>
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
