<?php 
	if(isset($_GET['cat_id'])){ 
		$page_title= 'Edit Category';
	}
	else{ 
		$page_title='Add Category'; 
	}

	$current_page="category";
	$active_page="home";

	include("includes/header.php");
	require("includes/function.php");
	require("language/language.php");

	require_once("thumbnail_images.class.php");
	
	if(isset($_POST['submit']) and isset($_GET['add']))
	{
	
		
          
        $data = array( 
			'home_title'  =>  cleanInput($_POST['home_title']),
			'sequence'  =>  cleanInput($_POST['sequence']),
			'type'  =>  cleanInput($_POST['type'])
		
		);		

 		$qry = Insert('tbl_home',$data);			

		$_SESSION['msg']="10";
 
		header( "Location:manage_home_category.php");
		exit;	
		
	}
	
	if(isset($_GET['cat_id']))
	{	 
		$qry="SELECT * FROM tbl_home where id='".$_GET['cat_id']."'";
		$result=mysqli_query($mysqli,$qry);
		$row=mysqli_fetch_assoc($result);
	}
	if(isset($_POST['submit']) and isset($_POST['cat_id']))
	{
		 
		

			$data = array(
				'home_title'  =>  cleanInput($_POST['home_title']),
				'sequence'  =>  cleanInput($_POST['sequence']),
				'type'  =>  cleanInput($_POST['type'])
			);	

			$category_edit=Update('tbl_home', $data, "WHERE id = '".$_POST['cat_id']."'");

	

	    $_SESSION['msg']="11"; 

	    if(isset($_GET['redirect']))
	      header( "Location:".$_GET['redirect']);
	    else  
	      header( "Location:add_home_category.php?cat_id=".$_POST['cat_id']);
		
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
           <a href="manage_home_category.php" style="background:white;margin-top:10px;font-size:25px;">X</a>
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="card-body mrg_bottom"> 
        <form action="" name="addeditcategory" method="post" class="form form-horizontal" enctype="multipart/form-data">
        	<input  type="hidden" name="cat_id" value="<?php echo $_GET['cat_id'];?>" />

          <div class="section">
            <div class="section-body">
                <div class="form-group">
                <label class="col-md-3 control-label">Category :-</label>
                <div class="col-md-6">
                  <select name="type" id="type" class="select2" required>
                    <option value="">--Select Type--</option>
                    <option value="movies" <?=(isset($_GET['cat_id']) && $row['type']=='movie') ? 'selected="selected"' : ''?>>Movies</option>
                    <option value="series"<?=(isset($_GET['cat_id']) && $row['type']=='series') ? 'selected="selected"' : ''?>>Series</option>
                    <option value="shows"<?=(isset($_GET['cat_id']) && $row['type']=='shows') ? 'selected="selected"' : ''?>>Tv Shows</option>
                    <option value="songs"<?=(isset($_GET['cat_id']) && $row['type']=='songs') ? 'selected="selected"' : ''?>>Songs</option>
                    <option value="shortfilms"<?=(isset($_GET['cat_id']) && $row['type']=='shortfilms') ? 'selected="selected"' : ''?>>Shortfilms</option>
                    <option value="drama"<?=(isset($_GET['cat_id']) && $row['type']=='drama') ? 'selected="selected"' : ''?>>Drama</option>
                     
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label">Title :-</label>
                <div class="col-md-6">
                  <input type="text" name="home_title" id="home_title" value="<?php if(isset($_GET['cat_id'])){echo $row['home_title'];}?>" class="form-control" required>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label">Sequence :-</label>
                <div class="col-md-6">
                  <input type="text" name="sequence" id="sequence" value="<?php if(isset($_GET['cat_id'])){echo $row['sequence'];}?>" class="form-control" required>
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
