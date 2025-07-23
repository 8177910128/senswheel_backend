<?php 
  
  $page_title="Manage Episodes";
  $current_page="episode";
  $active_page="series";

  include("includes/header.php");
	require("includes/function.php");
	require("language/language.php");


  $tableName="tbl_episode";    
   
  $limit = 12;

  if(isset($_GET['series']))
  {

      $series_id=$_GET['series'];
      
      if(isset($_GET['season'])){

        $season_id=$_GET['season'];

        $query = "SELECT COUNT(*) as num FROM $tableName WHERE tbl_episode.`series_id`='$series_id' AND tbl_episode.`season_id`='$season_id'";

        $targetpage = "manage_episode.php?series=$series_id&season=".$season_id; 
      }
      else{
        $query = "SELECT COUNT(*) as num FROM $tableName WHERE tbl_episode.`series_id`='$series_id'";

        $targetpage = "manage_episode.php?series=$series_id";   
      }
      

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

      $sql="SELECT episode.*, series.`series_name`, season.`season_name`  FROM tbl_episode episode
            LEFT JOIN tbl_series series ON episode.`series_id`=series.`id`
            LEFT JOIN tbl_season season ON episode.`season_id`=season.`id`
            WHERE episode.`series_id`='$series_id'
            ORDER BY episode.`id` DESC LIMIT $start, $limit";

      if(isset($_GET['season'])){

        $season_id=$_GET['season'];

        $sql="SELECT episode.*, series.`series_name`, season.`season_name`  FROM tbl_episode episode
            LEFT JOIN tbl_series series ON episode.`series_id`=series.`id`
            LEFT JOIN tbl_season season ON episode.`season_id`=season.`id`
            WHERE episode.`series_id`='$series_id' AND episode.`season_id`='$season_id'
            ORDER BY episode.`id` DESC LIMIT $start, $limit";
      }

  }

  else if(isset($_POST['data_search']))
  {

      $search_txt=addslashes(trim($_POST['search_value'])); 

      $sql="SELECT episode.*, series.`series_name`, season.`season_name`  FROM tbl_episode episode
          LEFT JOIN tbl_series series ON episode.`series_id`=series.`id`
          LEFT JOIN tbl_season season ON episode.`season_id`=season.`id`
          WHERE (season.`season_name` LIKE '%$search_txt%' OR episode.`episode_title` LIKE '%$search_txt%' OR series.`series_name` LIKE '%$search_txt%')
          ORDER BY episode.`id` DESC";

   }
   else
   {
	
	     //Get all episodes 
      $targetpage = "manage_episode.php"; 
      
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
      
     
     $sql="SELECT episode.*, series.`series_name`, season.`season_name`  FROM tbl_episode episode
            LEFT JOIN tbl_series series ON episode.`series_id`=series.`id`
            LEFT JOIN tbl_season season ON episode.`season_id`=season.`id`
            ORDER BY episode.`id` DESC LIMIT $start, $limit
            ";
	
    } 

    $result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));

	 
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
                    <button type="submit" name="data_search" class="btn-search"><i class="fa fa-search"></i></button>
              </form>  
            </div>
            <div class="add_btn_primary"> <a href="add_episode.php?add=yes">Add Episode &nbsp;<i class="fa fa-plus-circle"></i></a> </div>
          </div>
        </div>
        <div class="clearfix"></div>
        <form id="filterForm" accept="" method="GET">
          <div class="col-md-3">
            <div class="" style="padding: 0px 0px 5px;">
              <select name="series" class="form-control select2 filter" style="padding: 5px 10px;height: 40px;">
                  <option value=""> Series </option>
                  <?php 
                    $sql="SELECT * FROM tbl_series ORDER BY id DESC";
                    $res_series=mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));
                    while ($info=mysqli_fetch_assoc($res_series)) {
                      ?>
                      <option value="<?=$info['id']?>" <?php if(isset($_GET['series']) && $_GET['series']==$info['id']){ echo 'selected';} ?>><?=$info['series_name']?></option>
                      <?php
                    }
                    mysqli_free_result($res_series);
                  ?>
                </select>
            </div>
          </div>
          <div class="col-md-3">
            <div class="" style="padding: 0px 0px 5px;">
              <select name="season" class="form-control select2 filter" style="padding: 5px 10px;height: 40px;">
                  <option value=""> Season </option>
                  <?php
                    if(isset($_GET['series'])){
                      $sql="SELECT * FROM tbl_season WHERE `series_id`='".$_GET['series']."' ORDER BY `season_name` ASC";
                      $res_season=mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));
                      while ($data=mysqli_fetch_assoc($res_season)) {
                        ?>
                        <option value="<?=$data['id']?>" <?php if(isset($_GET['season']) && $_GET['season']==$data['id']){ echo 'selected';} ?>><?=$data['season_name']?></option>
                        <?php
                      }
                      mysqli_free_result($res_season);
                    }
                  ?>
                </select>
            </div>
          </div>
        </form>
        <div class="col-md-4 col-xs-12 text-right" style="float: right;">
          <div class="checkbox" style="width: 95px;margin-top: 5px;margin-left: 10px;right: 100px;position: absolute;">
            <input type="checkbox" id="checkall_input">
            <label for="checkall_input">
                Select All
            </label>
          </div>
          <div class="dropdown" style="float:right">
            <button class="btn btn-primary dropdown-toggle btn_cust" type="button" data-toggle="dropdown">Action
            <span class="caret"></span></button>
            <ul class="dropdown-menu" style="right:0;left:auto;">
              <li><a href="" class="actions" data-action="enable">Enable</a></li>
              <li><a href="" class="actions" data-action="disable">Disable</a></li>
              <li><a href="" class="actions" data-action="delete">Delete !</a></li>
            </ul>
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
            <div class="block_wallpaper" style="border-radius:10px;">
              <div class="wall_category_block">
                <h2><?php echo $row['series_name'];?></h2>  
                <div class="checkbox" style="float: right;">
                  <?php
                    if(strlen($row['episode_title']) > 25){
                      echo substr(stripslashes($row['episode_title']), 0, 25).'...';  
                    }else{
                      echo $row['episode_title'];
                    }
                    ?>
                </div>
              </div>
              <div class="wall_image_title" style="background-color:rgba(0, 0, 0, 0.4);">
                <b style="font-size: 15px;font-weight: 600"><?=$row['season_name']?></b>
                
                
                <ul style="z-index: 1">
                   
                    <li><a href="" class="btn_delete_a" style="background:gray;width:74px;border-radius:4px;padding20px;" data-id="<?php echo $row['id'];?>" data-toggle="tooltip" data-tooltip="Delete">Delete <i class="fa fa-trash"></i></a></li>
                  <li><a href="add_episode.php?episode_id=<?php echo $row['id'];?>&redirect=<?=$redirectUrl?>" style="background:linear-gradient(91.59deg, rgb(148, 63, 213) 1.37%, rgb(97, 43, 173) 98.71%);;width:74px;border-radius:4px;" data-toggle="tooltip" data-tooltip="Edit">Edit <i class="fa fa-edit"></i></a></li>
                  

                  <?php if($row['status']!="0"){?>
                  <li><div class="row toggle_btn"><a href="javascript:void(0)" data-id="<?php echo $row['id'];?>" data-action="deactive" data-column="status" data-toggle="tooltip" data-tooltip="ENABLE"><img src="assets/images/btn_enabled.png" alt="wallpaper_1" /></a></div></li>

                  <?php }else{?>
                  
                  <li><div class="row toggle_btn"><a href="javascript:void(0)" data-id="<?=$row['id']?>" data-action="active" data-column="status" data-toggle="tooltip" data-tooltip="DISABLE"><img src="assets/images/btn_disabled.png" alt="wallpaper_1" /></a></div></li>
              
                  <?php }?>
                <ul>  
              </div>
              <span><img src="<?php echo $row['episode_poster'];?>" /></span>
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
    var _table='tbl_episode';

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
      var _table='tbl_episode';

      swal({
          title: "Are you sure?",
          text: "All data will be deleted of this episode.",
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
                        text: "Episode is deleted...", 
                        type: "success"
                    },function() {
                        location.reload();
                    });
                  }
                }
            });
          }
          else{
            swal.close();
          }
      });
    });


    $(".actions").click(function(e){
        e.preventDefault();

        var _ids = $.map($('.post_ids:checked'), function(c){return c.value; });
        var _action=$(this).data("action");

        if(_ids!='')
        {
          swal({
            title: "Action: "+$(this).text(),
            text: "Do you really want to perform?",
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

              var _table='tbl_episode';

              $.ajax({
                type:'post',
                url:'processData.php',
                dataType:'json',
                data:{id:_ids,for_action:_action,table:_table,'action':'multi_action'},
                success:function(res){
                    console.log(res);
                    $('.notifyjs-corner').empty();
                    if(res.status=='1'){
                      swal({
                          title: "Successfully", 
                          text: "You have successfully done", 
                          type: "success"
                      },function() {
                          location.reload();
                      });
                    }
                  }
              });
            }
            else{
              swal.close();
            }

          });
        }
        else{
          swal("Sorry no episode selected !!")
        }
  });

  $(".filter").on("change",function(e){
    $("#filterForm *").filter(":input").each(function(){
      if ($(this).val() == '')
        $(this).prop("disabled", true);
    });
    $("#filterForm").submit();
  });


  var totalItems=0;

  $("#checkall_input").click(function () {

    totalItems=0;

    $('input:checkbox').not(this).prop('checked', this.checked);
    $.each($("input[name='post_ids[]']:checked"), function(){
      totalItems=totalItems+1;
    });

    if($('input:checkbox').prop("checked") == true){
      $('.notifyjs-corner').empty();
      $.notify(
        'Total '+totalItems+' item checked',
        { position:"top center",className: 'success'}
      );
    }
    else if($('input:checkbox'). prop("checked") == false){
      totalItems=0;
      $('.notifyjs-corner').empty();
    }
  });

  var noteOption = {
      clickToHide : false,
      autoHide : false,
  }

  $.notify.defaults(noteOption);

  $(".post_ids").click(function(e){

      if($(this).prop("checked") == true){
        totalItems=totalItems+1;
      }
      else if($(this). prop("checked") == false){
        totalItems = totalItems-1;
      }

      if(totalItems==0){
        $('.notifyjs-corner').empty();
        exit();
      }

      $('.notifyjs-corner').empty();

      $.notify(
        'Total '+totalItems+' item checked',
        { position:"top center",className: 'success'}
      );


  });

</script>
