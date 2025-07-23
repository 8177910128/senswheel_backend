<?php 

  $page_title="Manage Category";
  $current_page="category";
  $active_page="channel";

  include("includes/header.php");
  require("includes/function.php");
  require("language/language.php");

  if(isset($_POST['data_search']))
  {

    $qry="SELECT * FROM tbl_category                   
          WHERE tbl_category.`category_name` LIKE '%".addslashes($_POST['search_value'])."%'
          ORDER BY tbl_category.category_name";

    $result=mysqli_query($mysqli,$qry); 

  }
  else
  {
  	
  	//Get all Category 

    $tableName="tbl_category";   
    $targetpage = "manage_category.php"; 
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

    $qry="SELECT * FROM tbl_category ORDER BY tbl_category.`cid` DESC LIMIT $start, $limit";

    $result=mysqli_query($mysqli,$qry); 

  }

  function get_total_channels($cat_id)
  { 
    global $mysqli;   

    $qry_wallpaper="SELECT COUNT(*) as num FROM tbl_channels WHERE cat_id='".$cat_id."'";

    $total_wallpaper = mysqli_fetch_array(mysqli_query($mysqli,$qry_wallpaper));
    $total_wallpaper = $total_wallpaper['num'];

    return $total_wallpaper;

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
                <input class="form-control input-sm" placeholder="Search..." aria-controls="DataTables_Table_0" type="search" value="<?php if(isset($_POST['search_value'])){ echo $_POST['search_value']; } ?>" name="search_value" required>
                <button type="submit" name="data_search" class="btn-search"><i class="fa fa-search"></i></button>
              </form>  
            </div>
            <div class="add_btn_primary"> <a href="add_category.php?add=yes">Add Category  &nbsp;<i class="fa fa-plus-circle"></i></a> </div>
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
            <div class="col-lg-4 col-sm-6 col-xs-12">
              <div class="block_wallpaper add_wall_category" style="border-radius: 10px;box-shadow: 0px 2px 5px #999;height:220px">           
                <div class="wall_image_title" style="background-color:rgba(0, 0, 0, 0.4);">
                  <h2><a href="manage_channels.php?category=<?=$row['cid']?>" ><?php echo $row['category_name'];?> <br><span style="color:white;"><?php echo get_total_channels($row['cid']);?></span></a></h2>
                  <ul>                
                               
                    <li><a href="" data-id="<?php echo $row['cid'];?>" style="background:#EFF4F8;width:74px;border-radius:4px;color:#898889;" class="btn_delete_a" data-toggle="tooltip" data-tooltip="Delete">Delete <i class="fa fa-trash"  style="color:#898889;" >
                        
                    </i></a></li>
                      <li><a href="add_category.php?cat_id=<?php echo $row['cid'];?>&redirect=<?=$redirectUrl?>" data-toggle="tooltip"  style="background:linear-gradient(91.59deg, rgb(148, 63, 213) 1.37%, rgb(97, 43, 173) 98.71%);width:74px;border-radius:4px;" data-tooltip="Edit">Edit <i class="fa fa-edit"></i></a></li>  

                    <?php if($row['status']!="0"){?>
                      <li><div class="row toggle_btn"><a href="javascript:void(0)" data-id="<?php echo $row['cid'];?>" data-action="deactive" data-column="status" data-toggle="tooltip" data-tooltip="ENABLE"><img src="assets/images/btn_enabled.png" alt="wallpaper_1" /></a></div></li>

                    <?php }else{?>
                      <li><div class="row toggle_btn"><a href="javascript:void(0)" data-id="<?=$row['cid']?>" data-action="active" data-column="status" data-toggle="tooltip" data-tooltip="DISABLE"><img src="assets/images/btn_disabled.png" alt="wallpaper_1" /></a></div></li>
                    <?php }?>
                  </ul>
                </div>
                <span><img src="images/<?php echo $row['category_image'];?>" /></span>
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

  $(".toggle_btn a").on("click",function(e){
    e.preventDefault();

    var _for=$(this).data("action");
    var _id=$(this).data("id");
    var _column=$(this).data("column");
    var _table='tbl_category';

    $.ajax({
      type:'post',
      url:'processData.php',
      dataType:'json',
      data:{id:_id,for_action:_for,column:_column,table:_table,'action':'toggle_status','tbl_id':'cid'},
      success:function(res){
        console.log(res);
        if(res.status=='1'){
          location.reload();
        }
      }
    });

  });


  // delete category
  $(".btn_delete_a").click(function(e){

    e.preventDefault();

    var _id=$(this).data("id");
    var _table='tbl_category';

    swal({
      title: "Are you sure?",
      text: "All data will be remove which belong to this.",
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
                text: "Category is deleted...", 
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
