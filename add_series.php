<?php 
	if(isset($_GET['series_id'])){ 
		$page_title= 'Edit Series';
	}
	else{ 
		$page_title='Add Series'; 
	}
	$current_page="series";
	$active_page="series";

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
		
		$title=addslashes(trim($_POST['series_name']));
		$desc=addslashes(trim($_POST['series_desc']));
       $language_id=$_POST['language_id'];
      $genre_id=implode(',', $_POST['genre_id']);
      $total_time=addslashes(trim($_POST['total_time']));
      $director_name=addslashes(trim($_POST['director_name']));
      $cast_names=addslashes(trim($_POST['cast_names']));
      $maturity_rating=addslashes(trim($_POST['maturity_rating']));
      $release_date=addslashes(trim($_POST['release_date']));
      $series_cost_type=addslashes(trim($_POST['series_cost_type']));
      $series_price=addslashes(trim($_POST['series_price']));
      
      
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
      
		if($_POST['poster_img']=='' || $_FILES['series_poster']['error']!=4){
			// for series poster
			$series_poster=rand(0,99999)."_".$_FILES['series_poster']['name'];
			$pic1=$_FILES['series_poster']['tmp_name'];

						
			$tpath1p='images/series/'.$series_poster; 
			copy($pic1,$tpath1p);

			$thumbpath='images/series/thumbs/'.$series_poster;
				
			$obj_img = new thumbnail_images();
			$obj_img->PathImgOld = $tpath1p;
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
	        $series_poster=date('dmYhis').'_'.rand(0,99999).".".$ext;

	        $tpath1p='images/series/'.$series_poster;

	        grab_image($_POST['poster_img'], $tpath1p);

	        $thumbpath='images/series/thumbs/'.$series_poster;
	          
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

		// for series cover
		$series_cover=rand(0,99999)."_".$_FILES['series_cover']['name'];
		$pic1=$_FILES['series_cover']['tmp_name'];

					
		$tpath1c='images/series/'.$series_cover; 
		copy($pic1,$tpath1c);

		$thumbpath='images/series/thumbs/'.$series_cover;
			
		$obj_img = new thumbnail_images();
		$obj_img->PathImgOld = $tpath1c;
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
			    'series_name'  =>  $title,
			    'series_cost_type'  =>  $series_cost_type,
			    'series_price'  =>  $series_price,
			    'category_id'  =>  cleanInput($_POST['category_id']),
			    'home_cat_id'  =>  $_POST['home_cat_id'],
			    'trailer_type'=> $_POST['trailer_file_type'],
			    'trailer_url'  =>  $trailer_url,
                'trailer_id'  =>  $trailer_id,
			    'series_desc'  =>  $desc,
			     'total_time'  =>  $total_time,
			     'imdb_rating'=> $_POST['imdb_rating'],
                'director_name'  =>  $director_name,
                'cast_names'  =>  $cast_names,
                'release_date	'  =>   cleanInput($_POST['release_date']),
                'maturity_rating'  =>  $maturity_rating,
			   	'series_poster'  =>  $tpath1p,
			   	'series_cover'  =>  $tpath1c
			    );		

 		$qry = Insert('tbl_series',$data);			

		$_SESSION['msg']="10";
 
		header( "Location:manage_series.php");
		exit;	
		
	}
	
	if(isset($_GET['series_id']))
	{	 
		$qry="SELECT * FROM tbl_series where id='".$_GET['series_id']."'";
		$result=mysqli_query($mysqli,$qry);
		$row=mysqli_fetch_assoc($result);
	}
	
	if(isset($_POST['submit']) and isset($_GET['series_id']))
	{	
		$title=addslashes(trim($_POST['series_name']));
		$desc=addslashes(trim($_POST['series_desc']));
		$language_id=$_POST['language_id'];
      $genre_id=implode(',', $_POST['genre_id']);
      $total_time=addslashes(trim($_POST['total_time']));
         $director_name=addslashes(trim($_POST['director_name']));
       $maturity_rating=addslashes(trim($_POST['maturity_rating']));
        $cast_names=addslashes(trim($_POST['cast_names']));
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
		
		if($_FILES['series_poster']['error']!=4){

			unlink('images/series/'.$row['series_poster']);
			unlink('images/series/thumbs/'.$row['series_poster']);

			// for series poster
			$series_poster=rand(0,99999)."_".$_FILES['series_poster']['name'];
			$pic1=$_FILES['series_poster']['tmp_name'];

						
			$tpath1p='images/series/'.$series_poster; 
			copy($pic1,$tpath1p);

			$thumbpath='images/series/thumbs/'.$series_poster;
				
			$obj_img = new thumbnail_images();
			$obj_img->PathImgOld = $tpath1p;
			$obj_img->PathImgNew =$thumbpath;
			$obj_img->NewWidth = 270;
			$obj_img->NewHeight = 390;
			if (!$obj_img->create_thumbnail_images()) 
			{
				echo "Thumbnail not created... please upload image again";
				exit;
			}
		}else{
			$series_poster=$row['series_poster'];
		}

		if($_FILES['series_cover']['error']!=4){
			unlink('images/series/'.$row['series_cover']);
			unlink('images/series/thumbs/'.$row['series_cover']);

			// for series cover
			$series_cover=rand(0,99999)."_".$_FILES['series_cover']['name'];
			$pic1=$_FILES['series_cover']['tmp_name'];

						
			$tpath1c='images/series/'.$series_cover; 
			copy($pic1,$tpath1c);

			$thumbpath='images/series/thumbs/'.$series_cover;
				
			$obj_img = new thumbnail_images();
			$obj_img->PathImgOld = $tpath1c;
			$obj_img->PathImgNew =$thumbpath;
			$obj_img->NewWidth = 600;
			$obj_img->NewHeight = 350;
			if (!$obj_img->create_thumbnail_images()) 
			{
				echo "Thumbnail not created... please upload image again";
				exit;
			}

		}else{
			$series_cover=$row['series_cover'];
		}

      
      
		$data = array( 
		        'release_date'  =>  $_POST['release_date'],
                'maturity_rating'  =>  $_POST['maturity_rating'],
                'category_id'  =>  $_POST['category_id'],
                'home_cat_id'  =>  $_POST['home_cat_id'],
                'series_name'  =>  $_POST['series_name'],
                'series_desc'  =>  $_POST['series_desc'],
                'imdb_rating'=> $_POST['imdb_rating'],
                'genre_id'  =>  $genre_id,
                'trailer_type'=> $_POST['trailer_file_type'],
			    'trailer_url'  =>  $trailer_url,
                'trailer_id'  =>  $trailer_id,
                'language_id'  =>  $_POST['language_id'],
		        'director_name'  =>  $_POST['director_name'],
		        'total_time'  =>  $_POST['total_time'],
		        'cast_names'  =>  $_POST['cast_names'],
			    'series_cost_type'  =>  $_POST['series_cost_type'],
			    'series_price'  =>  $_POST['series_price'],
			     'series_poster'  =>  $tpath1p,
			   	'series_cover'  =>  $tpath1c
			    );
			    
			   // print_r($data);

	
		 $edit=Update('tbl_series', $data, "WHERE id = '".$_POST['series_id']."'");
		 
		  
		$_SESSION['msg']="11"; 
		

		if(isset($_GET['redirect']))
	      header( "Location:".$_GET['redirect']);
	    else  
	      header( "Location:add_series.php?series_id=".$_GET['series_id']);
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
               <a href="manage_series.php" style="background:white;margin-top:10px;font-size:25px;">X</a>
            </div>
          </div>
          <div class="clearfix"></div>
          <div class="card-body mrg_bottom"> 



            <form class="form form-horizontal" action="" method="post"  enctype="multipart/form-data">
            	<input  type="hidden" name="series_id" value="<?php echo $_GET['series_id'];?>" />

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
                        <option value="<?php echo $data['id'];?>" <?php if(isset($_GET['series_id']) && $row['language_id']==$data['id']){ echo 'selected'; } ?>><?php echo $data['language_name'];?></option>                          
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
                  <select name="home_cat_id" id="home_cat_id" class="select2" >
                    <option value="">Select Home Category</option>
                    <?php
                    	$dir_home="SELECT * FROM tbl_home where status=1 ORDER BY home_title";
                        $result_home=mysqli_query($mysqli,$dir_home);
                        while($rowhome=mysqli_fetch_array($result_home))
                        {
                    ?>                       
                    <option value="<?php echo $rowhome['id'];?>" <?php if(isset($_GET['movie_id']) && $row['home_cat_id']==$rowhome['id']){ echo 'selected'; } ?>><?php echo $rowhome['home_title'];?></option>                          
                    <?php
                      }
                    ?>
                  </select>
                </div>
              </div>
                  <div class="form-group">
                    <label class="col-md-3 control-label">Series Name :-</label>
                    <div class="col-md-6">
                      <input type="text" name="series_name" id="series_name" value="<?php if(isset($_GET['series_id'])){echo $row['series_name'];}?>" class="form-control" required>
                    </div>
                  </div>
                  <div class="form-group">
                <label class="col-md-3 control-label">Series Cost Type :-</label>
                <div class="col-md-6">                       
                  <select name="series_cost_type" id="series_cost_type" class="select2" required>
                        <option value="">Select Type</option>
                        <option value="free" <?php if(isset($_GET['series_id']) && $row['series_cost_type']=='free'){ echo 'selected'; } ?>>Free</option>
                        <option value="paid" <?php if(isset($_GET['series_id']) && $row['series_cost_type']=='paid'){ echo 'selected'; } ?>>Paid</option>
                        
                  </select>
                </div>
              </div>
              
                <div class="form-group">
                <label class="col-md-3 control-label">Trailer Upload Type :-</label>
                <div class="col-md-6">                       
                  <select name="trailer_file_type" id="trailer_file_type" class="select2" required>
                        <option value="">Select Type</option>
                        <option value="youtube_url" <?php if(isset($_GET['series_id']) && $row['trailer_type']=='youtube_url'){ echo 'selected'; } ?>>YouTube URL</option>
                        <option value="server_url" <?php if(isset($_GET['series_id']) && $row['trailer_type']=='server_url'){ echo 'selected'; } ?>>Live URL</option>
                        <option value="local" <?php if(isset($_GET['series_id']) && $row['trailer_type']=='local'){ echo 'selected'; } ?>>Local System</option>
                        <option value="embedded_url" <?php if(isset($_GET['series_id']) && $row['trailer_type']=='embedded_url'){ echo 'selected'; } ?>>Embedded URL (Open Load, Very Stream, Daily motion, Vimeo)</option>
                        
                  </select>
                </div>
              </div>
              
               <div id="trailer_url_holder" class="form-group" style="display:none;">
                <label class="col-md-3 control-label">Enter trailer URL :-
                  <p style="color:#F00;font-weight: 500">(This is default play video url on app player)</p>
                </label>
                <div class="col-md-6">
                  <input type="text" name="trailer_url" value="<?php if(isset($_GET['series_id'])){ echo $row['trailer_url'];}?>" class="form-control">
                </div>
              </div>
              
              
              <div id="trailer_local_holder" class="form-group" style="display:none;">
                <label class="col-md-3 control-label">Browse trailer Video :-
                  <p style="color:#F00;font-weight: 500">(This is default play video on app player)</p>
                </label>
                <div class="col-md-6">
                  <input type="file" name="trailer_local" value="" class="form-control trailer_local">
                    <?php 
                      if(isset($_GET['series_id']) && $row['is_quality']=='false'){
                     ?> 
                      <div style="word-break: break-all;"><label class="control-label">Current URL :-</label><?php echo $file_path.'uploads/'.$row['trailer_url']?></div><br>
                      <?php  
                    }
                    ?>
                    <div class="uploadPreview" style="<?php if(isset($_GET['series_id']) && $row['trailer_type']=='local'){ echo 'display: block;';}else{ echo 'display: none;';}?>background: #eee;text-align: center;">
                      <video height="250" width="100%" class="video-preview" src="<?php if(isset($_GET['series_id']) && $row['trailer_type']=='local'){ echo $file_path.'uploads/'.$row['trailer_url']; } ?>" controls="controls"/>
                    </div>
                </div>
              </div>
              
               <div class="form-group">
                <label class="col-md-3 control-label">Relsease Date :-</label>
                <div class="col-md-6">
                  <input type="date" name="release_date"   value="<?php if(isset($_GET['series_id'])){echo $row['release_date'];}?>" class="form-control" >
                </div>
              </div>
               <div class="form-group">
                <label class="col-md-3 control-label">Director Name :-</label>
                <div class="col-md-6">
                  <input type="text" name="director_name" placeholder="Enter Director Name" id="director_name" value="<?php if(isset($_GET['series_id'])){echo $row['director_name'];}?>" class="form-control" required>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label">Casts :-</label>
                <div class="col-md-6">
                  <input type="text" name="cast_names" placeholder="Enter Casts" id="total_time" value="<?php if(isset($_GET['series_id'])){echo $row['cast_names'];}?>" class="form-control" required>
                </div>
              </div>
               <div class="form-group">
                <label class="col-md-3 control-label">Maturity rating :-</label>
                <div class="col-md-6">
                  <input type="text" name="maturity_rating" placeholder="Enter maturity rating" id="maturity_rating" value="<?php if(isset($_GET['series_id'])){echo $row['maturity_rating'];}?>" class="form-control" required>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label">Imdb Rating :-</label>
                <div class="col-md-6">
                  <input type="text" name="imdb_rating" placeholder="Enter imdb rating" id="imdb_rating" value="<?php if(isset($_GET['series_id'])){echo $row['imdb_rating'];}?>" class="form-control" required>
                </div>
                  </div>
              <div class="form-group">
                <label class="col-md-3 control-label">Total Time :-</label>
                <div class="col-md-6">
                  <input type="text" name="total_time" placeholder="Enter video time" id="total_time" value="<?php if(isset($_GET['series_id'])){echo $row['total_time'];}?>" class="form-control" required>
                </div>
              </div>
                  <div class="form-group">
                    <label class="col-md-3 control-label">Poster Image:-
                      <p class="control-label-help" id="square_lable_info">(Recommended resolution: 185x278 portrait)</p>
                    </label>
                    <div class="col-md-6">
                      <div class="fileupload_block" style="border-radius:10px;height: 189px;">
                        <input type="file" name="series_poster" value="" accept=".png, .jpg, .jpeg, .svg, .gif" <?php echo (!isset($_GET['series_id'])) ? 'required="require"' : '' ?> id="fileupload">
                        <div class="fileupload_img">
                        	<?php 
                        		$img_src="";

                        		if(!isset($_GET['series_id']) || !file_exists('images/series/'.$row['series_poster'])){
                        			$img_src='assets/images/browse.svg';
                        		}else{
                        			$img_src='images/series/'.$row['series_poster'];
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
                        <input type="file" name="series_cover" value="" accept=".png, .jpg, .jpeg, .svg, .gif" <?php echo (!isset($_GET['series_id'])) ? 'required="require"' : '' ?> id="fileupload">
                        <div class="fileupload_img">
                        	<?php 
                        		$img_src="";

                        		if(!isset($_GET['series_id']) || !file_exists('images/series/'.$row['series_cover'])){
                        			$img_src='assets/images/browse.svg';
                        		}else{
                        			$img_src='images/series/'.$row['series_cover'];
                        		}

                        	?>
                          <img type="image" src="<?=$img_src?>" alt="cover image" style="width: 150px;height: 86px" />
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-3">
                      <label class="control-label">Series Description :-</label>
                    </div>
                    <div class="col-md-6">
                      <textarea name="series_desc" id="series_desc" rows="5" class="form-control"><?php if(isset($_GET['series_id'])){ echo $row['series_desc']; } ?></textarea>
                      <script>
                        CKEDITOR.replace('series_desc');
                      </script>
                    </div>
                  </div>
                  <br/>
                  <div class="form-group">
                    <div class="col-md-9 col-md-offset-3">
                      <button type="submit" name="submit" class="btn btn-primary" style="border-radius:10px;"><?php  if(isset($_GET['series_id'])){ ?>Update <?php }else { ?> Save <?php }?></button>
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
	      $("input[name='series_poster']").next(".fileupload_img").find("img").attr('src', e.target.result);
	    }
	    
	    reader.readAsDataURL(input.files[0]);
	  }
	}

	$("input[name='series_poster']").change(function() { 
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
	      $("input[name='series_cover']").next(".fileupload_img").find("img").attr('src', e.target.result);
	    }
	    
	    reader.readAsDataURL(input.files[0]);
	  }
	}

	$("input[name='series_cover']").change(function() { 

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
<script>
    $(document).ready(function() {
  function toggleSeriesPrice() {
    var costType = $("#series_cost_type").val();
    if (costType === "paid") {
      $("#series_price_holder").show();
      $("input[name='series_price']").attr("required", true);
    } else {
      $("#series_price_holder").hide();
      $("input[name='series_price']").attr("required", false);
    }
  }

  // Run on page load (to handle pre-selected value when editing)
  toggleSeriesPrice();

  // Run when select box value changes
  $("#series_cost_type").on("change", function() {
    toggleSeriesPrice();
  });
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
        data:{'action':'getSeriesDetails', 'id' : imdb_id_title},
        success:function(res){

            btn.attr("disabled", false);
            btn.text("Fetch");

            $('.notifyjs-corner').empty();
            $.notify(
              res.message,
              { position:"top center",className: res.class}
            );
            if(res.status=='1'){
                
                $("input[name='series_poster']").attr("required",false);
                
                $("input[name='series_name']").val(res.title);
                $("input[name='poster_img']").val(res.thumbnail);
                $(".poster_img").attr('src', res.thumbnail);
                $("textarea[name='series_desc']").val(res.plot);
                CKEDITOR.instances['series_desc'].setData(res.plot);
            }
          }
      });

    }

  });

</script> 