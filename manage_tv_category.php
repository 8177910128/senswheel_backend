<?php 
  $page_title="Manage TV Category";
  $current_page="TV Category";
  

  include("includes/header.php");
	require("includes/function.php");
	require("language/language.php");
	
  if(isset($_POST['data_search']))
  {

      $searchInput=cleanInput($_POST['search_value']);

      $qry="SELECT * FROM tbl_tv_category WHERE tbl_tv_category.`category_name` LIKE '%$searchInput%' ORDER BY tbl_tv_category.`category_name`";
 
      $result=mysqli_query($mysqli,$qry); 

  }
  else
  {
   
      $tableName="tbl_tv_category";   
      $targetpage = "manage_tv_category.php"; 
      $limit = 12; 
      
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
      
      $qry="SELECT * FROM tbl_tv_category ORDER BY tbl_tv_category.`id` DESC LIMIT $start, $limit";
 
      $result=mysqli_query($mysqli,$qry); 
  
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
              <input class="form-control input-sm" placeholder="Search genre..." aria-controls="DataTables_Table_0" type="search" name="search_value" value="<?php if(isset($_POST['search_value'])){ echo $_POST['search_value']; }?>" required>
                    <button type="submit" name="data_search" class="btn-search"><i class="fa fa-search"></i></button>
              </form>  
            </div>
            <div class="add_btn_primary"> <a href="add_tv_category.php?add=yes">Add TV Category &nbsp;<i class="fa fa-plus-circle"></i></a> </div>
          </div>
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="col-md-12 mrg-top">
        <div class="row">
          <?php 
              $i=0;
              while($row=mysqli_fetch_array($result))
              {         
          ?>
          <div class="col-lg-3 col-sm-6 col-xs-12">
            <div class="block_wallpaper add_wall_category" style="border-radius: 10px;box-shadow: 0px 2px 5px #999">           
              <div class="wall_image_title">
                <h2><a href="" style=""><?php echo $row['category_name'];?> </a></h2>
                <ul>    
                 <li class="btn btn-info" style="background:#EFF4F8;width:74px;border-radius:4px;"><a href="" style="color:#898889;" data-id="<?php echo $row['id'];?>" class="btn_delete_a" data-toggle="tooltip" data-tooltip="Delete">Delete <i class="fa fa-trash" style="color:#898889;"></i></a></li>
                  <li class="btn btn-info" style="background:linear-gradient(91.59deg, rgb(148, 63, 213) 1.37%, rgb(97, 43, 173) 98.71%);width:74px;border-radius:4px;"><a href="add_tv_category.php?id=<?php echo $row['id'];?>&redirect=<?=$redirectUrl?>" data-toggle="tooltip" data-tooltip="Edit"> Edit <i class="fa fa-edit"></i></a></li>               
                  
                </ul>
              </div>
              <span><img src="<?php echo $row['category_image'];?>" style="height: 250px !important;"/></span>
            </div>
          </div>
      <?php
        $i++;
        }
      ?>     
      </div>
      </div>
       <div class="col-md-12 col-xs-12">
        <div class="pagination_item_block">
          <nav>
            <?php if(!isset($_POST["data_search"])){ include("pagination.php");}?>
          </nav>
        </div>
      </div>
      <div class="clearfix"></div>
    </div>
  </div>
</div>
        
<?php include("includes/footer.php");?>

<script type="text/javascript">

  $(".btn_delete_a").click(function(e){

    e.preventDefault();

    var _id=$(this).data("id");
    var _table='tbl_tv_category';

    swal({
        title: "Are you sure?",
        text: "Do you really want to delete this.",
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
                      text: "Genre is deleted...", 
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


