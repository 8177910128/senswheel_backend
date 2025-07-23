<?php 
	if(isset($_GET['shows_id'])){ 
		$page_title= 'Edit Tv Shows';
	}
	else{ 
		$page_title='Add Tv Shows'; 
	}
	$current_page="shows";
	$active_page="shows";

	include("includes/header.php");
	require("includes/function.php");
	require("language/language.php");

	require_once("thumbnail_images.class.php");
	
	$cat_qry="SELECT * FROM tbl_language ORDER BY language_name";
    $cat_result=mysqli_query($mysqli,$cat_qry);
	
	$genre_qry="SELECT * FROM tbl_genres ORDER BY genre_name";
    $genre_result=mysqli_query($mysqli,$genre_qry); 
    
    
	if(isset($_POST['submit']) and isset($_GET['add']))
	{
		
		$title=addslashes(trim($_POST['shows_name']));
		$desc=addslashes(trim($_POST['shows_desc']));
		$language_id=$_POST['language_id'];
		$director_name=addslashes(trim($_POST['director_name']));
        $genre_id=implode(',', $_POST['genre_id']);
        $maturity_rating=addslashes(trim($_POST['maturity_rating']));
        $release_date=addslashes(trim($_POST['release_date']));

		if($_POST['poster_img']=='' || $_FILES['shows_poster']['error']!=4){
			// for shows poster
			$shows_poster=rand(0,99999)."_".$_FILES['shows_poster']['name'];
			$pic1=$_FILES['shows_poster']['tmp_name'];

						
			$tpath1='images/shows/'.$shows_poster; 
			copy($pic1,$tpath1);
            $tpath1p=$tpath1;
			$thumbpath='images/shows/thumbs/'.$shows_poster;
				
			$obj_img = new thumbnail_images();
			$obj_img->PathImgOld = $tpath1;
			$obj_img->PathImgNew =$thumbpath;
			$obj_img->NewWidth = 270;
			$obj_img->NewHeight = 390;
			if (!$obj_img->create_thumbnail_images()) 
			{
				echo "Thumbnail not created... please upload image again";
				exit;
			}
		}
		else{
			$get_file_name = parse_url($_POST['poster_img'], PHP_URL_PATH);

	        $ext = pathinfo($get_file_name, PATHINFO_EXTENSION);
	        $shows_poster=date('dmYhis').'_'.rand(0,99999).".".$ext;

	        $tpath1='images/shows/'.$shows_poster;

	        grab_image($_POST['poster_img'], $tpath1);

	        $thumbpath='images/shows/thumbs/'.$shows_poster;
	          
	        $obj_img = new thumbnail_images();
	        $obj_img->PathImgOld = $tpath1;
	        $obj_img->PathImgNew =$thumbpath;
	        $obj_img->NewWidth = 300;
	        $obj_img->NewHeight = 300;
	        if (!$obj_img->create_thumbnail_images()) 
	        {
	          echo "Thumbnail not created... please upload image again";
	          exit;
	        }
		}

		// for shows cover
		$shows_cover=rand(0,99999)."_".$_FILES['shows_cover']['name'];
		$pic1=$_FILES['shows_cover']['tmp_name'];

					
		$tpath1='images/shows/'.$shows_cover; 
		copy($pic1,$tpath1);
$tpath1c=$tpath1;
		$thumbpath='images/shows/thumbs/'.$shows_cover;
			
		$obj_img = new thumbnail_images();
		$obj_img->PathImgOld = $tpath1;
		$obj_img->PathImgNew =$thumbpath;
		$obj_img->NewWidth = 600;
		$obj_img->NewHeight = 350;
		if (!$obj_img->create_thumbnail_images()) 
		{
			echo "Thumbnail not created... please upload image again";
			exit;
		}
        
        $data = array( 
               'language_id'  =>  $language_id,
                'genre_id'  =>  $genre_id,
                 'imdb_rating'  =>  $_POST['imdb_rating'],
                 'cast_names'  =>  $_POST['cast_names'],
			    'shows_name'  =>  $title,
			    'shows_desc'  =>  $desc,
			     'director_name'  =>  $director_name,
			     'release_date'  =>   cleanInput($_POST['release_date']),
			     'home_cat_id'  =>   cleanInput($_POST['home_cat_id']),
                'maturity_rating'  =>  $maturity_rating,
			   	'shows_poster'  =>  $tpath1p,
			   	'shows_cover'  =>  $tpath1c
			    );		

 		$qry = Insert('tbl_shows',$data);			

		$_SESSION['msg']="10";
 
		header( "Location:manage_shows.php");
		exit;	
		
	}
	
	if(isset($_GET['shows_id']))
	{	 
		$qry="SELECT * FROM tbl_shows where id='".$_GET['shows_id']."'";
		$result=mysqli_query($mysqli,$qry);
		$row=mysqli_fetch_assoc($result);
	}
	if(isset($_POST['submit']) and isset($_GET['shows_id']))
	{	
		$title=addslashes(trim($_POST['shows_name']));
		$desc=addslashes(trim($_POST['shows_desc']));
		$language_id=$_POST['language_id'];
       $genre_id=implode(',', $_POST['genre_id']);
       $director_name=addslashes(trim($_POST['director_name']));
       $maturity_rating=addslashes(trim($_POST['maturity_rating']));
		
		if($_FILES['shows_poster']['error']!=4){

			unlink('images/shows/'.$row['shows_poster']);
			unlink('images/shows/thumbs/'.$row['shows_poster']);

			// for shows poster
			$shows_poster=rand(0,99999)."_".$_FILES['shows_poster']['name'];
			$pic1=$_FILES['shows_poster']['tmp_name'];

						
			$tpath1='images/shows/'.$shows_poster; 
			copy($pic1,$tpath1);
            $tpath1p=$tpath1;
			$thumbpath='images/shows/thumbs/'.$shows_poster;
				
			$obj_img = new thumbnail_images();
			$obj_img->PathImgOld = $tpath1;
			$obj_img->PathImgNew =$thumbpath;
			$obj_img->NewWidth = 270;
			$obj_img->NewHeight = 390;
			if (!$obj_img->create_thumbnail_images()) 
			{
				echo "Thumbnail not created... please upload image again";
				exit;
			}
		}else{
			$tpath1p=$row['shows_poster'];
		}

		if($_FILES['shows_cover']['error']!=4){
			unlink('images/shows/'.$row['shows_cover']);
			unlink('images/shows/thumbs/'.$row['shows_cover']);

			// for shows cover
			$shows_cover=rand(0,99999)."_".$_FILES['shows_cover']['name'];
			$pic1=$_FILES['shows_cover']['tmp_name'];

						
			$tpath1='images/shows/'.$shows_cover; 
			copy($pic1,$tpath1);
$tpath1c=$tpath1;
			$thumbpath='images/shows/thumbs/'.$shows_cover;
				
			$obj_img = new thumbnail_images();
			$obj_img->PathImgOld = $tpath1;
			$obj_img->PathImgNew =$thumbpath;
			$obj_img->NewWidth = 600;
			$obj_img->NewHeight = 350;
			if (!$obj_img->create_thumbnail_images()) 
			{
				echo "Thumbnail not created... please upload image again";
				exit;
			}

		}else{
			$tpath1c=$row['shows_cover'];
		}

		$data = array( 
		      'language_id'  =>  $language_id,
               'genre_id'  =>  $genre_id,
               'imdb_rating'  =>  $_POST['imdb_rating'],
                 'cast_names'  =>  $_POST['cast_names'],
			   'shows_name'  =>  $title,
			   'director_name'  =>  $director_name,
			   'maturity_rating'  =>   cleanInput($_POST['maturity_rating']),
			   'home_cat_id'  =>   cleanInput($_POST['home_cat_id']),
			   'release_date'  =>  $_POST['release_date'],
			   'shows_desc'  =>  $desc,
			   'shows_poster'  =>  $tpath1p,
			   'shows_cover'  =>  $tpath1c
			    );

		$edit=Update('tbl_shows', $data, "WHERE id = '".$_POST['shows_id']."'");

		$_SESSION['msg']="11"; 
		

		if(isset($_GET['redirect']))
	      header( "Location:".$_GET['redirect']);
	    else  
	      header( "Location:add_shows.php?shows_id=".$_POST['shows_id']);
	    exit;

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
               <a href="manage_shows.php" style="background:white;margin-top:10px;font-size:25px;">X</a>
            </div>
          </div>
          <div class="clearfix"></div>
          <div class="card-body mrg_bottom"> 



            <form action="" name="addeditcategory" method="post" class="form form-horizontal" enctype="multipart/form-data">
            	<input  type="hidden" name="shows_id" value="<?php echo $_GET['shows_id'];?>" />

              <div class="section">
                <div class="section-body">
                    <div class="form-group">
                    <label class="col-md-3 control-label">Language :-</label>
                    <div class="col-md-6">
                    <select name="language_id" id="language_id" class="select2" required>
                        <option value="">Select Language</option>
                        <?php
                            while($data=mysqli_fetch_array($cat_result))
                            {
                        ?>                       
                        <option value="<?php echo $data['id'];?>" <?php if(isset($_GET['shows_id']) && $row['language_id']==$data['id']){ echo 'selected'; } ?>><?php echo $data['language_name'];?></option>                          
                        <?php
                          }
                        ?>
                      </select>
                </div>
              </div> 
               <div class="form-group">
                <label class="col-md-3 control-label">Genre :-</label>
                <div class="col-md-6">
                  <select name="genre_id[]" id="genre_id" class="select2" required multiple="">
                    <option value="">Select Genre</option>
                    <?php
                        while($genre_row=mysqli_fetch_array($genre_result))
                        {
                    ?>                       
                    <option value="<?php echo $genre_row['gid'];?>" <?php $genre_list=explode(",", $row['genre_id']);
                            foreach($genre_list as $ids)
                            {if($genre_row['gid']==$ids){ echo 'selected'; }}?>><?php echo $genre_row['genre_name'];?></option>                           
                    <?php
                      }
                    ?>
                  </select>
                </div>
              </div>   
              <div class="form-group">
                <label class="col-md-3 control-label">Home Category :-</label>
                <div class="col-md-6">
                  <select name="home_cat_id" id="home_cat_id" class="select2" required>
                    <option value="">Select Home Category</option>
                    <?php
                    	$dir_home="SELECT * FROM tbl_home where status=1 ORDER BY home_title";
                        $result_home=mysqli_query($mysqli,$dir_home);
                        while($rowhome=mysqli_fetch_array($result_home))
                        {
                    ?>                       
                    <option value="<?php echo $rowhome['id'];?>" <?php if(isset($_GET['shows_id']) && $row['home_cat_id']==$rowhome['id']){ echo 'selected'; } ?>><?php echo $rowhome['home_title'];?></option>                          
                    <?php
                      }
                    ?>
                  </select>
                </div>
              </div>
                  <div class="form-group">
                    <label class="col-md-3 control-label">Tv Shows :-</label>
                    <div class="col-md-6">
                      <input type="text" name="shows_name" id="shows_name" value="<?php if(isset($_GET['shows_id'])){echo $row['shows_name'];}?>" class="form-control" required>
                    </div>
                  </div>
                  <div class="form-group">
                <label class="col-md-3 control-label">Relsease Date :-</label>
                <div class="col-md-6">
                  <input type="date" name="release_date"   value="<?php if(isset($_GET['shows_id'])){echo $row['release_date'];}?>" class="form-control" >
                </div>
              </div>
               <div class="form-group">
                <label class="col-md-3 control-label">Director Name :-</label>
                <div class="col-md-6">
                  <input type="text" name="director_name" placeholder="Enter Director Name" id="director_name" value="<?php if(isset($_GET['shows_id'])){echo $row['director_name'];}?>" class="form-control" required>
                </div>
              </div>
               <div class="form-group">
                <label class="col-md-3 control-label">Casts :-</label>
                <div class="col-md-6">
                  <input type="text" name="cast_names" placeholder="Enter Casts" id="total_time" value="<?php if(isset($_GET['shows_id'])){echo $row['cast_names'];}?>" class="form-control" required>
                </div>
              </div>
               <div class="form-group">
                <label class="col-md-3 control-label">Maturity rating :-</label>
                <div class="col-md-6">
                  <input type="text" name="maturity_rating" placeholder="Enter maturity rating" id="maturity_rating" value="<?php if(isset($_GET['shows_id'])){echo $row['maturity_rating'];}?>" class="form-control" required>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label">Imdb Rating :-</label>
                <div class="col-md-6">
                  <input type="text" name="imdb_rating" placeholder="Enter imdb rating" id="imdb_rating" value="<?php if(isset($_GET['shows_id'])){echo $row['imdb_rating'];}?>" class="form-control" required>
                </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-3 control-label">Poster Image:-
                      <p class="control-label-help" id="square_lable_info">(Recommended resolution: 185x278 portrait)</p>
                    </label>
                    <div class="col-md-6">
                      <div class="fileupload_block" style="border-radius:10px;height: 189px;">
                        <input type="file" name="shows_poster" value="" accept=".png, .jpg, .jpeg, .svg, .gif" <?php echo (!isset($_GET['shows_id'])) ? 'required="require"' : '' ?> id="fileupload">
                        <div class="fileupload_img">
                        	<?php 
                        		$img_src="";

                        		if(!isset($_GET['shows_id']) || !file_exists('images/shows/'.$row['shows_poster'])){
                        			$img_src='assets/images/browse.svg';
                        		}else{
                        			$img_src='images/shows/'.$row['shows_poster'];
                        		}

                        	?>

                        	<input type="hidden" name="poster_img" value="">

                          <img type="image" src="<?=$img_src?>" class="poster_img" alt="poster image" style="width: 80px;height: 115px" />
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-md-3 control-label">Cover Image:-
                      <p class="control-label-help" id="square_lable_info">(Recommended resolution: 500x282 landscape)</p>
                    </label>
                    <div class="col-md-6">
                      <div class="fileupload_block" style="border-radius:10px;height: 189px;">
                        <input type="file" name="shows_cover" value="" accept=".png, .jpg, .jpeg, .svg, .gif" <?php echo (!isset($_GET['shows_id'])) ? 'required="require"' : '' ?> id="fileupload">
                        <div class="fileupload_img">
                        	<?php 
                        		$img_src="";

                        		if(!isset($_GET['shows_id']) || !file_exists('images/shows/'.$row['shows_cover'])){
                        			$img_src='assets/images/browse.svg';
                        		}else{
                        			$img_src='images/shows/'.$row['shows_cover'];
                        		}

                        	?>
                          <img type="image" src="<?=$img_src?>" alt="cover image" style="width: 150px;height: 86px" />
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-3">
                      <label class="control-label">Shows Description:-</label>
                    </div>
                    <div class="col-md-6">
                      <textarea name="shows_desc" id="shows_desc" rows="5" class="form-control"><?php if(isset($_GET['shows_id'])){ echo $row['shows_desc']; } ?></textarea>
                      <script>
                        CKEDITOR.replace('shows_desc');
                      </script>
                    </div>
                  </div>
                  <br/>
                  <div class="form-group">
                    <div class="col-md-9 col-md-offset-3">
                      <button type="submit" name="submit" class="btn btn-primary" style="border-radius:10px;"><?php  if(isset($_GET['shows_id'])){ ?>Update <?php }else { ?> Save <?php }?></button>
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
	      $("input[name='shows_poster']").next(".fileupload_img").find("img").attr('src', e.target.result);
	    }
	    
	    reader.readAsDataURL(input.files[0]);
	  }
	}

	$("input[name='shows_poster']").change(function() { 
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

	function readURL1(input) {
	  if (input.files && input.files[0]) {
	    var reader = new FileReader();
	    
	    reader.onload = function(e) {
	      $("input[name='shows_cover']").next(".fileupload_img").find("img").attr('src', e.target.result);
	    }
	    
	    reader.readAsDataURL(input.files[0]);
	  }
	}

	$("input[name='shows_cover']").change(function() { 

		var file=$(this);

	    if(file[0].files.length != 0){
	        if(isImage($(this).val())){
	          readURL1(this);
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
        data:{'action':'getshowsDetails', 'id' : imdb_id_title},
        success:function(res){

            btn.attr("disabled", false);
            btn.text("Fetch");

            $('.notifyjs-corner').empty();
            $.notify(
              res.message,
              { position:"top center",className: res.class}
            );
            if(res.status=='1'){
                
                $("input[name='shows_poster']").attr("required",false);
                
                $("input[name='shows_name']").val(res.title);
                $("input[name='poster_img']").val(res.thumbnail);
                $(".poster_img").attr('src', res.thumbnail);
                $("textarea[name='shows_desc']").val(res.plot);
                CKEDITOR.instances['shows_desc'].setData(res.plot);
            }
          }
      });

    }

  });

</script> 