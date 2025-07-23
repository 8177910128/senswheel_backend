<?php 
	
	$page_title="Manage Comments";

	include('includes/header.php'); 

    include('includes/function.php');
	include('language/language.php');  
	
	function get_post($post_id,$type)
	{
		global $mysqli;
		switch ($type) 
		{
		  case 'series':
		    $sql="SELECT * FROM tbl_series where id='$post_id' AND status='1'";
		    $res=mysqli_query($mysqli,$sql);
			$data=mysqli_fetch_assoc($res);

			return $data['series_name'];
		    break;

		  case 'movie':
		    $sql="SELECT * FROM tbl_movies where id='$post_id' AND status='1'";
		    $res=mysqli_query($mysqli,$sql);
			$data=mysqli_fetch_assoc($res);

			return $data['movie_title'];
		    break;

		  case 'channel':
		    $sql="SELECT * FROM tbl_channels where id='$post_id' AND status='1'";
		    $res=mysqli_query($mysqli,$sql);
			$data=mysqli_fetch_assoc($res);

			return $data['channel_title'];
		    break;
		  
		  default:
	    	break;
		}


	}

	$tableName="tbl_comments";	
	$limit = 12;

	$stages = 3;
	$page=0;
	if(isset($_GET['page'])){
		$page = mysqli_real_escape_string($mysqli,$_GET['page']);
	}
	if($page){
		$start = ($page - 1) * $limit; 
	}else{
		$start = 0;	
	}
	
	if(isset($_GET['type'])){

		$type=$_GET['type'];

		if(isset($_GET['post_id'])){
			$post_id=$_GET['post_id'];
	
			$targetpage = "manage_comments.php?post_id=".$post_id."&type=".$type;

			$query = "SELECT COUNT(*) as num FROM $tableName WHERE `post_id`='$post_id' AND `type`='$type'";
			$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query));
			$total_pages = $total_pages['num'];

			$users_qry="SELECT tbl_comments.*, tbl_users.`name` FROM tbl_comments, tbl_users 
						WHERE tbl_comments.`user_id`=tbl_users.`id`
						AND `post_id`='$post_id' AND tbl_comments.`type`='$type'
					  	ORDER BY tbl_comments.`id` DESC LIMIT $start, $limit"; 
			
		}
		else if(isset($_GET['user_id'])){

			$user_id=$_GET['user_id'];
	
			$targetpage = "manage_comments.php?user_id=".$user_id; 	

			$query = "SELECT COUNT(*) as num FROM $tableName WHERE `user_id`='$user_id'";
			$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query));
			$total_pages = $total_pages['num'];

			$users_qry="SELECT tbl_comments.*, tbl_users.`name` FROM tbl_comments, tbl_users 
						WHERE tbl_comments.`user_id`=tbl_users.`id`
						AND `user_id`='$user_id'
					  	ORDER BY tbl_comments.`id` DESC LIMIT $start, $limit"; 
		}
		else{

			$targetpage = "manage_comments.php?type=".$type; 	
			 
			$query = "SELECT COUNT(*) as num FROM $tableName WHERE `type`='$type'";
			$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query));
			$total_pages = $total_pages['num'];

			$users_qry="SELECT tbl_comments.*, tbl_users.`name` FROM tbl_comments, tbl_users 
						WHERE tbl_comments.`user_id`=tbl_users.`id`
						AND tbl_comments.`type`='$type'
					  	ORDER BY tbl_comments.`id` DESC LIMIT $start, $limit";  
		}
	}
	else{	

		$targetpage = "manage_comments.php"; 	
		
		$query = "SELECT COUNT(*) as num FROM $tableName";
		$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query));
		$total_pages = $total_pages['num'];

		$users_qry="SELECT tbl_comments.*, tbl_users.`name` FROM tbl_comments, tbl_users 
					WHERE tbl_comments.`user_id`=tbl_users.`id`
				  	ORDER BY tbl_comments.`id` DESC LIMIT $start, $limit";  
	}

	$users_result=mysqli_query($mysqli,$users_qry) or die(mysqli_error($mysqli));
	
?>


<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="page_title_block">
        <div class="col-md-5 col-xs-12">
          <div class="page_title"><?=$page_title?></div>
        </div>	
        <div class="clearfix"></div>	
        <div class="col-md-3">
        	<form id="filterForm" accept="" method="GET">
        		<select name="type" class="form-control select2 filter" style="padding: 5px 10px;height: 40px;">
        			<option value="">--Comments of--</option>
        			<option value="movie" <?php if(isset($_GET['type']) && $_GET['type']=='movie'){ echo 'selected';} ?>>Movies</option>
        			<option value="series" <?php if(isset($_GET['type']) && $_GET['type']=='series'){ echo 'selected';} ?>>TV Series</option>
        			<option value="channel" <?php if(isset($_GET['type']) && $_GET['type']=='channel'){ echo 'selected';} ?>>Channels</option>
        		</select>
        	</form>
        </div>
      </div> 
	  <div class="clearfix"></div>	
      <div class="card-body mrg_bottom">
        <?php
        	if(mysqli_num_rows($users_result) > 0){

			$i=0;
			while($users_row=mysqli_fetch_array($users_result))
			{	 
		?>
        <div class="col-md-6">
		  <ul class="timeline timeline-simple">
			<li class="timeline-inverted">
			  <div class="timeline-badge danger"> 
				<img src="assets/images/photo.jpg" class="img-profile"> 
			  </div>
			  <div class="timeline-panel">
				<div class="timeline-heading"> 
					<a href="manage_comments.php?post_id=<?php echo $users_row['post_id'].'&type='.$users_row['type'];?>" title="">
						<span class="label label-danger">
							<?php echo get_post($users_row['post_id'],$users_row['type']);?>
						</span> 
					</a> 
					<span class="pull-right text-center"> 
					<a href="" class="btn_delete_a" data-id="<?php echo $users_row['id'];?>" data-toggle="tooltip" data-tooltip="Delete"> <i class="fa fa-trash" style="color:red"></i> </a> 
					 </span> 
				</div>
				<div class="timeline-body">
				  <p><?php echo $users_row['comment_text'];?></p>
				</div>
				<hr>
				<a href="manage_comments.php?user_id=<?php echo $users_row['user_id'];?>" title=""> <small class="label label-rose"> <span><?php echo $users_row['name'];?></span> </small> </a> <span class="pull-right about_time" title="<?php echo calculate_time_span($users_row['comment_on'],true);?>"><?php echo calculate_time_span($users_row['comment_on'],true);?></span>
			</div>
			</li>
		  </ul>
		</div>
			<?php		
			$i++;
			}
		  }
		  else{
		  	?>
		  	<p class="not_data"><strong>Sorry!</strong> no data found !</p>
		  	<?php
		  }
		?>
		<div class="col-md-12 col-xs-12">
	        <div class="pagination_item_block">
	          <nav>
	            <?php if(!isset($_POST["search"])){ include("pagination.php");}?>
	          </nav>
	        </div>
	    </div>
		<div class="clearfix"></div>
      </div>
    </div>
	<div class="clearfix"></div>
  </div>
</div>   



<?php include('includes/footer.php');?>          

<script type="text/javascript">

	$(".filter").on("change",function(e){
	    $("#filterForm *").filter(":input").each(function(){
	      if ($(this).val() == '')
	        $(this).prop("disabled", true);
	    });
	    $("#filterForm").submit();
	});

	$(".btn_delete_a").click(function(e){

      e.preventDefault();

      var _id=$(this).data("id");

      swal({
          title: "Are you sure?",
          text: "Do you want to delete this comment.",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          cancelButtonClass: "btn-warning",
          confirmButtonText: "Yes",
          cancelButtonText: "No",
          closeOnConfirm: false,
          closeOnCancel: false,
          showLoaderOnConfirm: true
        },
        function(isConfirm) {
          if (isConfirm) {

            $.ajax({
              type:'post',
              url:'processData.php',
              dataType:'json',
              data:{id:_id,'action':'removeComment'},
              success:function(res){
                  console.log(res);
                  if(res.status=='1'){
                    swal({
                        title: "Successfully", 
                        text: "Comment is deleted...", 
                        type: "success"
                    },function() {
                        location.reload();
                    });
                  }
                  else if(res.status=='-2'){
	                  swal(res.message);
	                }
                }
            });
          }
          else{
            swal.close();
          }
      });
    });
</script>
