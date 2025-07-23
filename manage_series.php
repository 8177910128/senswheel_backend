<?php 
  
  $page_title="Manage TV Series";
  $current_page="series";
  $active_page="series";

  include("includes/header.php");
	require("includes/function.php");
	require("language/language.php");

  if(isset($_POST['search']))
  {

      $search_txt=cleanInput($_POST['search_value']);

      $sql="SELECT * FROM tbl_series WHERE (tbl_series.`series_name` LIKE '%$search_txt%') ORDER BY tbl_series.`id` DESC";

      $result=mysqli_query($mysqli,$sql); 

  }
  else
  {

    $tableName="tbl_series";   
    $targetpage = "manage_series.php"; 
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
    
   $qry="SELECT * FROM tbl_series ORDER BY tbl_series.`id` DESC LIMIT $start, $limit";

   $result=mysqli_query($mysqli,$qry); 

  }


  function getPostRating($post_id, $rate, $type){

      global $mysqli;

      $sql="SELECT COUNT(*) AS total_count FROM tbl_rating WHERE `post_id`='$post_id' AND `type`='$type' AND `rate`='$rate'";
      $res=mysqli_query($mysqli, $sql);
      $row=mysqli_fetch_assoc($res);
      return $row['total_count'];
  }

	 
?>

<style type="text/css">
  .modal-dialog {
      width: 440px;
      margin: 30px auto;
  } 
</style>
                
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
            <div class="add_btn_primary"> <a href="add_series.php?add=yes">Add Series &nbsp;<i class="fa fa-plus-circle"></i></a> </div>
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
            <div class="block_wallpaper"> 
              <div class="wall_category_block">
                   <h2><?php echo $row['series_name'];?></h2>
                <?php if($row['is_slider']!="0"){?>
                  <a class="toggle_btn_a" style="background:rgba(239, 244, 248, 0.30);color:white;"  data-id="<?=$row['id']?>" data-action="deactive" data-column="is_slider" data-toggle="tooltip" data-tooltip="Slider" style="margin-left: 5px"><div style="color:red;"><i class="fa fa-sliders"></i></div></a>
                <?php }else{?>
                    <a class="toggle_btn_a" style="background:rgba(239, 244, 248, 0.30);color:white;"  data-id="<?=$row['id']?>" data-action="active" data-column="is_slider" data-toggle="tooltip" data-tooltip="Set Slider" style="margin-left: 5px"><i class="fa fa-sliders"></i>
                    </a>
                <?php }?>

              </div>          
              <div class="wall_image_title" style="background-color:rgba(0, 0, 0, 0.4);">
               
                <ul> 
                  <li><a href="javascript:void(0)" data-toggle="tooltip"  style="background:gray;width:22px;border-radius:4px;padding20px;" data-tooltip="<?php echo $row['total_views'];?> Views"><i class="fa fa-eye" style="color:white;"></i></a></li>  
                  <li><a href="javascript:void(0)" style="background:rgba(239, 244, 248, 0.30);backdrop-filter: blur(2px);width:22px;border-radius:4px;" alt="total time" data-toggle="tooltip" data-tooltip="<?php $seconds = $row['total_watch_time']; $hours = floor($seconds / 3600); $minutes = floor(($seconds % 3600) / 60); $secs = $seconds % 60; if ($hours > 0) {echo sprintf("%02d:%02d:%02d", $hours, $minutes, $secs); } else {echo sprintf("%02d:%02d", $minutes, $secs);}?> Total Time"><i class="fa fa-clock-o"></i></a></li>
                  
                  <li>
                    <a href="javascript:void(0)" style="background:gray;width:22px;border-radius:4px;padding20px;"   data-title="<?php if(strlen($row['series_name']) > 25){ echo substr(stripslashes($row['series_name']), 0, 25).'...';}else{ echo $row['series_name'];} ?>" class="btn_show_rate" data-toggle="tooltip" data-tooltip="View Rates"><i class="fa fa-star" style="color:white;"></i></a>

                      <div class="rating_container" style="display: none">
                        <div class="list-group lg-alt lg-even-black">
                          <table width="100%">
                            <tbody>
                            <tr>
                              <td colspan="3" style="padding:15px;">
                                <div style="float:left;">

                                  <?php 
                                    $rate_avg=intval($row['rate_avg']);

                                    for ($no=1; $no <= $rate_avg ; $no++) { 
                                        echo '<img src="assets/images/star.png" style="height:40px;width:40px">';
                                    }

                                    $no=$no-1;
                                    while ($no < 5) {
                                      echo '<img src="assets/images/star_e.png" style="height:40px;width:40px">';
                                      $no++;
                                    }
                                  ?>
                                </div>
                                <span style="height:50px;display:inline-block;font-size:30pt;font-weight:bolder;padding-left:20px;line-height:40px;"><?=$rate_avg?></span>
                              </td>
                            </tr>
                            <tr>
                              <td width="50%" align="right" style="padding:5px;">
                                <img src="assets/images/star.png" style="height:30px;width:30px"> 
                                <img src="assets/images/star.png" style="height:30px;width:30px"> 
                                <img src="assets/images/star.png" style="height:30px;width:30px"> 
                                <img src="assets/images/star.png" style="height:30px;width:30px"> 
                                <img src="assets/images/star.png" style="height:30px;width:30px"></td>
                              <td width="30px" align="center"><?=thousandsNumberFormat(getPostRating($row['id'],'5','series'))?></td>

                              <td align="left" style="padding:10px"><span style="display:block;height:15px;background-color:#ea1f62;width:0%"></span></td>
                            </tr>
                            <tr>
                              <td width="50%" align="right" style="padding:5px;"><img src="assets/images/star_e.png" style="height:30px;width:30px"> <img src="assets/images/star.png" style="height:30px;width:30px"> <img src="assets/images/star.png" style="height:30px;width:30px"> <img src="assets/images/star.png" style="height:30px;width:30px"> <img src="assets/images/star.png" style="height:30px;width:30px"></td>
                              <td width="30px" align="center"><?=thousandsNumberFormat(getPostRating($row['id'],'4','series'))?></td>
                              <td align="left" style="padding:10px"><span style="display:block;height:15px;background-color:#ea1f62;width:0%"></span></td>
                            </tr>
                            <tr>
                              <td width="50%" align="right" style="padding:5px;"><img src="assets/images/star_e.png" style="height:30px;width:30px"> <img src="assets/images/star_e.png" style="height:30px;width:30px"> <img src="assets/images/star.png" style="height:30px;width:30px"> <img src="assets/images/star.png" style="height:30px;width:30px"> <img src="assets/images/star.png" style="height:30px;width:30px"></td>
                              <td width="30px" align="center"><?=thousandsNumberFormat(getPostRating($row['id'],'3','series'))?></td>
                              <td align="left" style="padding:10px"><span style="display:block;height:15px;background-color:#ea1f62;width:0%"></span></td>
                            </tr>
                            <tr>
                              <td width="50%" align="right" style="padding:5px;"><img src="assets/images/star_e.png" style="height:30px;width:30px"> <img src="assets/images/star_e.png" style="height:30px;width:30px"> <img src="assets/images/star_e.png" style="height:30px;width:30px"> <img src="assets/images/star.png" style="height:30px;width:30px"> <img src="assets/images/star.png" style="height:30px;width:30px"></td>
                              <td width="30px" align="center"><?=thousandsNumberFormat(getPostRating($row['id'],'2','series'))?></td>
                              <td align="left" style="padding:10px"><span style="display:block;height:15px;background-color:#ea1f62;width:0%"></span></td>
                            </tr>
                            <tr>
                              <td width="50%" align="right" style="padding:5px;"><img src="assets/images/star_e.png" style="height:30px;width:30px"> <img src="assets/images/star_e.png" style="height:30px;width:30px"> <img src="assets/images/star_e.png" style="height:30px;width:30px"> <img src="assets/images/star_e.png" style="height:30px;width:30px"> <img src="assets/images/star.png" style="height:30px;width:30px"></td>
                              <td width="30px" align="center"><?=thousandsNumberFormat(getPostRating($row['id'],'1','series'))?></td>
                              <td align="left" style="padding:10px"><span style="display:block;height:15px;background-color:#ea1f62;width:0%"></span></td>
                            </tr>
                            </tbody>
                          </table>
                          </div>
                      </div>
                  </li>
                  
                 <li><a href=""  style="background:gray;width:74px;border-radius:4px;padding20px;" data-id="<?php echo $row['id'];?>" class="btn_delete_a" data-toggle="tooltip" data-tooltip="Delete"> Delete <i class="fa fa-trash"></i></a></li>
                  <li><a href="add_series.php?series_id=<?php echo $row['id'];?>&redirect=<?=$redirectUrl?>" style="background:linear-gradient(91.59deg, rgb(148, 63, 213) 1.37%, rgb(97, 43, 173) 98.71%);;width:74px;border-radius:4px;" data-toggle="tooltip" data-tooltip="Edit">Edit <i class="fa fa-edit"></i></a></li>               
                 
                  
                  <?php if($row['status']!="0"){?>
                  <li><div class="row toggle_btn"><a href="javascript:void(0)" data-id="<?php echo $row['id'];?>" data-action="deactive" data-column="status" data-toggle="tooltip" data-tooltip="ENABLE"><img src="assets/images/btn_enabled.png" alt="wallpaper_1" /></a></div></li>

                  <?php }else{?>
                  
                  <li><div class="row toggle_btn"><a href="javascript:void(0)" data-id="<?=$row['id']?>" data-action="active" data-column="status" data-toggle="tooltip" data-tooltip="DISABLE"><img src="assets/images/btn_disabled.png" alt="wallpaper_1" /></a></div></li>
              
                  <?php }?>


                </ul>
              </div>
              <span><img src="<?php echo $row['series_cover'];?>" /></span>
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
            <?php if(!isset($_POST["search_value"])){ include("pagination.php");}?>
          </nav>
        </div>
      </div>
      <div class="clearfix"></div>
    </div>
  </div>
</div>
        
<!-- Modal -->
<div id="ratingModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>            
               
        
<?php include("includes/footer.php");?>       

<script type="text/javascript">

  $(".btn_show_rate").click(function(e){
    $("#ratingModal .modal-title").text($(this).data("title"));
    $("#ratingModal .modal-body").html($(this).next(".rating_container").html());

    $("#ratingModal").modal("show");

  });

  $(".toggle_btn a, .toggle_btn_a").on("click",function(e){
    e.preventDefault();

    var _for=$(this).data("action");
    var _id=$(this).data("id");
    var _column=$(this).data("column");
    var _table='tbl_series';

    $.ajax({
      type:'post',
      url:'processData.php',
      dataType:'json',
      data:{id:_id,for_action:_for,column:_column,table:_table,'action':'toggle_status','tbl_id':'id'},
      success:function(res){
          console.log(res);
          if(res.status=='1'){
            location.reload();
          }
        }
    });

  });


  $(".btn_delete_a").click(function(e){

    e.preventDefault();

    var _id=$(this).data("id");
    var _table='tbl_series';

    swal({
        title: "Are you sure?",
        text: "Do you really want to delete this series.",
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
                      text: "Series is deleted...", 
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
