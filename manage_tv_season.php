<?php 
	$page_title="Manage TV Season";
	$current_page="tvseasons";
    $active_page="shows";


	include('includes/header.php'); 
    include('includes/function.php');
	include('language/language.php');  


	if(isset($_POST['search']))
	{

	  	$search_txt=addslashes(trim($_POST['search_value'])); 

	    $sql="SELECT tbl_tv_season.*, tbl_shows.`shows_name` FROM tbl_tv_season 
				LEFT JOIN tbl_shows ON tbl_tv_season.`shows_id`=tbl_shows.`id`
				WHERE (tbl_shows.`shows_name` LIKE '%$search_txt%' OR tbl_tv_season.`season_name` LIKE '%$search_txt%')
				ORDER BY tbl_tv_season.`id` DESC";

	  	$result=mysqli_query($mysqli,$sql); 

	}
	else
	{
	 
		$tableName="tbl_tv_season";		
		$targetpage = "manage_season.php"; 	
		$limit = 15; 

		$query = "SELECT COUNT(*) as num FROM $tableName";
		$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query));
		$total_pages = $total_pages['num'];

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

		$sql="SELECT tbl_tv_season.*, tbl_shows.`shows_name` FROM tbl_tv_season 
				LEFT JOIN tbl_shows ON tbl_tv_season.`shows_id`=tbl_shows.`id`
				ORDER BY tbl_tv_season.`id` DESC LIMIT $start, $limit";

		$result=mysqli_query($mysqli,$sql);
							
	}
?>


<div class="row">
  <div class="col-xs-12">
    <div class="card mrg_bottom">
      <div class="page_title_block">
        <div class="col-md-5 col-xs-12">
          <div class="page_title"><?=$page_title?></div>
        </div>
        <div class="col-md-7 col-xs-12">              
          <div class="search_list">
            <div class="search_block">
              <form  method="post" action="">
                <input class="form-control input-sm" placeholder="Search..." aria-controls="DataTables_Table_0" type="search" name="search_value" value="<?php if(isset($_POST['search_value'])){ echo $_POST['search_value']; }?>" required>
                <button type="submit" name="search" class="btn-search"><i class="fa fa-search"></i></button>
              </form>  
            </div>
            <div class="add_btn_primary"> <a href="add_tv_season.php?add=yes">Add Season &nbsp;<i class="fa fa-plus-circle"></i></a> </div>
          </div>
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="col-md-12 mrg-top">
        <table class="table table-striped table-bordered table-hover">
          <thead>
            <tr>
              <th>Shows</th>						 
			  <th>Season</th>				   
              <th class="cat_action_list">Action</th>
            </tr>
          </thead>
          <tbody>
          	<?php
				$i=0;
				while($row=mysqli_fetch_array($result))
				{ 
			?>
            <tr>
               <td><?php echo $row['shows_name'];?></td>
	           <td><?php echo $row['season_name'];?></td>   
	            
               <td><a href="edit_tv_season.php?id=<?php echo $row['id'];?>&redirect=<?=$redirectUrl?>" class="btn btn-primary btn_edit" style="backgroung:linear-gradient(91.59deg, rgb(148, 63, 213) 1.37%, rgb(97, 43, 173) 98.71%);" data-tooltip="Edit"><i class="fa fa-edit"></i></a>
                <a href="" data-id="<?php echo $row['id'];?>" data-tooltip="Delete" class="btn btn-danger btn_delete btn_delete_a" style="background:#FFCBCC;color:#E13437;"><i class="fa fa-trash"></i></a></td>
            </tr>
           <?php
				}
		   ?>
          </tbody>
        </table>
      </div>
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
</div>     



<?php include('includes/footer.php');?> 

<script type="text/javascript">
	$(".btn_delete_a").click(function(e){

	    e.preventDefault();

	    var _id=$(this).data("id");
	    var _table='tbl_tv_season';

	    swal({
	        title: "Are you sure?",
        	text: "Do you really want to delete this season.",
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
	            data:{id:_id,tbl_nm:_table,'action':'multi_delete'},
	            success:function(res){
	                console.log(res);
	                if(res.status=='1'){
	                  swal({
	                      title: "Successfully", 
	                      text: "Season is deleted...", 
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