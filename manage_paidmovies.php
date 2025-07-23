<?php 

  $page_title="Manage Paid Movies";
 $current_page="paidmovies";
	$active_page="movies";

  include("includes/header.php");
	require("includes/function.php");
	require("language/language.php");

  $tableName="tbl_movies";    
   
  $limit = 12;

  if(isset($_GET['language']))
  {

      $lang_id=$_GET['language'];
      
      if(isset($_GET['genre'])){
        $query = "SELECT COUNT(*) as num FROM $tableName WHERE tbl_movies.`language_id`='$lang_id' AND FIND_IN_SET(".$_GET['genre'].", tbl_movies.`genre_id`)";

        $targetpage = "manage_paidmovies.php?language=$lang_id&genre=".$_GET['genre']; 
      }
      else{
        $query = "SELECT COUNT(*) as num FROM $tableName WHERE tbl_movies.`language_id`='$lang_id'";

        $targetpage = "manage_paidmovies.php?language=$lang_id";   
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

      $sql="SELECT tbl_language.`language_name`,tbl_movies.* FROM tbl_movies
          LEFT JOIN tbl_language ON tbl_movies.`language_id`= tbl_language.`id` 
          WHERE tbl_movies.`language_id`='$lang_id' and tbl_movies.`movie_cost_type`='paid'
          ORDER BY tbl_movies.`id` DESC LIMIT $start, $limit";

      if(isset($_GET['genre'])){

        $genre_id=$_GET['genre'];

        $sql="SELECT tbl_language.`language_name`,tbl_movies.* FROM tbl_movies
            LEFT JOIN tbl_language ON tbl_movies.`language_id`= tbl_language.`id` 
            WHERE tbl_movies.`language_id`='$lang_id' AND FIND_IN_SET($genre_id, tbl_movies.`genre_id`) and tbl_movies.`movie_cost_type`='paid'
            ORDER BY tbl_movies.`id` DESC LIMIT $start, $limit";
      }

  }
  else if(isset($_GET['genre']))
  {
      $genre_id=$_GET['genre'];

      $query = "SELECT COUNT(*) as num FROM $tableName WHERE FIND_IN_SET(".$genre_id.", tbl_movies.`genre_id`)";

      $targetpage = "manage_paidmovies.php?genre=".$genre_id; 

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

      $sql="SELECT tbl_language.`language_name`,tbl_movies.* FROM tbl_movies
          LEFT JOIN tbl_language ON tbl_movies.`language_id`= tbl_language.`id` 
          WHERE FIND_IN_SET(tbl_movies.`genre_id`,$genre_id) and tbl_movies.`movie_cost_type`='paid'
          ORDER BY tbl_movies.`id` DESC LIMIT $start, $limit";


  }
  else if(isset($_POST["search"]))
  {
    $search_txt=addslashes(trim($_POST['search_value'])); 

    $sql="SELECT tbl_language.`language_name`,tbl_movies.* FROM tbl_movies
          LEFT JOIN tbl_language ON tbl_movies.`language_id`= tbl_language.`id` 
          WHERE (tbl_movies.`movie_title` LIKE '%$search_txt%' OR tbl_language.`language_name` LIKE '%$search_txt%') and tbl_movies.`movie_cost_type`='paid'
          ORDER BY tbl_movies.`id` DESC";
     
  }
  else
  { 
    
    $targetpage = "manage_paidmovies.php"; 

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

    $sql="SELECT tbl_language.`language_name`,tbl_movies.* FROM tbl_movies
          LEFT JOIN tbl_language ON tbl_movies.`language_id`=tbl_language.`id` where    tbl_movies.`movie_cost_type`='paid'
          ORDER BY tbl_movies.`id` DESC LIMIT $start, $limit";
  }

	$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));

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
  
  .title_and_toggle {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;  /* Adds space between the title/toggle row and the buttons row */
}

.title_and_toggle h2 {
    margin: 0;
    font-size: 18px; /* Adjust as needed */
}

.actions_row {
    display: flex;
    justify-content: flex-start;
}

.actions_row ul {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    gap: 10px; /* Adds space between buttons */
}

.actions_row ul li {
    margin-left: 10px;  /* Space between icons */
}

.actions_row ul li a i {
    font-size: 18px; /* Adjust the icon size as needed */
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
              <form method="post" action="">
                <input class="form-control input-sm" placeholder="Search..." aria-controls="DataTables_Table_0" type="search" name="search_value" value="<?php if(isset($_POST['search_value'])){ echo $_POST['search_value']; }?>" required>
                <button type="submit" name="search" class="btn-search"><i class="fa fa-search"></i></button>
              </form>  
            </div>
            <div class="add_btn_primary"> <a href="add_paidmovie.php?add=yes">Add Paid Movie &nbsp;<i class="fa fa-plus-circle"></i></a> </div>
          </div>
        </div>
        <div class="clearfix"></div>
        <form id="filterForm" accept="" method="GET">
          <div class="col-md-3">
            <div class="" style="padding: 0px 0px 5px;">
                <select name="language" class="form-control select2 filter" style="padding: 5px 10px;height: 40px;">
                  <option value="">All Language</option>
                  <?php
                    $sql_lang="SELECT * FROM tbl_language ORDER BY language_name";
                    $res_lang=mysqli_query($mysqli,$sql_lang);
                    while($row_lang=mysqli_fetch_array($res_lang))
                    {
                  ?>                       
                  <option value="<?php echo $row_lang['id'];?>" <?php if(isset($_GET['language']) && $_GET['language']==$row_lang['id']){echo 'selected';} ?> style="background-image:url('images/31295_2.png');"><?php echo $row_lang['language_name'];?></option>                           
                  <?php
                    }
                  ?>
                </select>
            </div>
          </div>
          <div class="col-md-3">
            <div class="" style="padding: 0px 0px 5px;">
                <select name="genre" class="form-control select2 filter" style="padding: 5px 10px;height: 40px;">
                  <option value=""> Genre </option>
                  <?php 
                    $qry="SELECT * FROM tbl_genres ORDER BY gid DESC";
                    $res=mysqli_query($mysqli, $qry) or die(mysqli_error($mysqli));
                    while ($data=mysqli_fetch_assoc($res)) {
                      ?>
                      <option value="<?=$data['gid']?>" <?php if(isset($_GET['genre']) && $_GET['genre']==$data['gid']){ echo 'selected';} ?>><?=$data['genre_name']?></option>
                      <?php
                    }
                    mysqli_free_result($res);
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
            <button class="btn btn-primary dropdown-toggle btn_cust" type="button" data-toggle="dropdown" style="background:#EFF4F8;color:#612BAD;">Action
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
          <div class="col-lg-3 col-sm-6 col-xs-12" style="border-radius:20px;">
            <div class="block_wallpaper" style"width:251px;height:315px;flex-shrink: 0;border-radius:550px;">
              <div class="wall_category_block" style="border-radius:0px;">
                <h2 style="width: 260px;"><?php echo $row['movie_title'];?></h2>  
                <?php if($row['is_slider']!="0"){?>
                   <a class="toggle_btn_a" style="background:rgba(239, 244, 248, 0.30);color:white;" data-id="<?=$row['id']?>" data-action="deactive" data-column="is_slider" data-toggle="tooltip" data-tooltip="Slider" style="margin-left: 5px"><div style="color:red;"><i class="fa fa-sliders"></i></div></a> 
                <?php }else{?>
                   <a class="toggle_btn_a" style="background:rgba(239, 244, 248, 0.30);color:white;" data-id="<?=$row['id']?>" data-action="active" data-column="is_slider" data-toggle="tooltip" data-tooltip="Set Slider" style="margin-left: 5px"><i class="fa fa-sliders"></i></a> 
                <?php }?>
              </div>
              
              

              
              <div class="wall_image_title" style="background: rgba(0,0,0,0.0);border-radius:20px;">
              
               <ul>
                   <li><a href="javascript:void(0)" style="background:rgba(239, 244, 248, 0.30);backdrop-filter: blur(2px);width:22px;border-radius:4px;" data-toggle="tooltip" data-tooltip="<?php echo $row['total_views'];?> Views"><i class="fa fa-eye"></i></a></li>
                   <li><a href="javascript:void(0)" style="background:rgba(239, 244, 248, 0.30);backdrop-filter: blur(2px);width:22px;border-radius:4px;" alt="total time" data-toggle="tooltip" data-tooltip="<?php $seconds = $row['total_watch_time']; $hours = floor($seconds / 3600); $minutes = floor(($seconds % 3600) / 60); $secs = $seconds % 60; if ($hours > 0) {echo sprintf("%02d:%02d:%02d", $hours, $minutes, $secs); } else {echo sprintf("%02d:%02d", $minutes, $secs);}?> Total Time"><i class="fa fa-clock-o"></i></a></li>
                   <li>
                    <a href="javascript:void(0)" style="background:rgba(239, 244, 248, 0.30);backdrop-filter: blur(2px);width:22px;border-radius:4px;" data-title="<?php if(strlen($row['movie_title']) > 25){ echo substr(stripslashes($row['movie_title']), 0, 25).'...';}else{ echo $row['movie_title'];} ?>" class="btn_show_rate" data-toggle="tooltip" data-tooltip="View Rates"><i class="fa fa-star"></i></a>

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
                              <td width="30px" align="center"><?=thousandsNumberFormat(getPostRating($row['id'],'5','movie'))?></td>

                              <td align="left" style="padding:10px"><span style="display:block;height:15px;background-color:#ea1f62;width:0%"></span></td>
                            </tr>
                            <tr>
                              <td width="50%" align="right" style="padding:5px;"><img src="assets/images/star_e.png" style="height:30px;width:30px"> <img src="assets/images/star.png" style="height:30px;width:30px"> <img src="assets/images/star.png" style="height:30px;width:30px"> <img src="assets/images/star.png" style="height:30px;width:30px"> <img src="assets/images/star.png" style="height:30px;width:30px"></td>
                              <td width="30px" align="center"><?=thousandsNumberFormat(getPostRating($row['id'],'4','movie'))?></td>
                              <td align="left" style="padding:10px"><span style="display:block;height:15px;background-color:#ea1f62;width:0%"></span></td>
                            </tr>
                            <tr>
                              <td width="50%" align="right" style="padding:5px;"><img src="assets/images/star_e.png" style="height:30px;width:30px"> <img src="assets/images/star_e.png" style="height:30px;width:30px"> <img src="assets/images/star.png" style="height:30px;width:30px"> <img src="assets/images/star.png" style="height:30px;width:30px"> <img src="assets/images/star.png" style="height:30px;width:30px"></td>
                              <td width="30px" align="center"><?=thousandsNumberFormat(getPostRating($row['id'],'3','movie'))?></td>
                              <td align="left" style="padding:10px"><span style="display:block;height:15px;background-color:#ea1f62;width:0%"></span></td>
                            </tr>
                            <tr>
                              <td width="50%" align="right" style="padding:5px;"><img src="assets/images/star_e.png" style="height:30px;width:30px"> <img src="assets/images/star_e.png" style="height:30px;width:30px"> <img src="assets/images/star_e.png" style="height:30px;width:30px"> <img src="assets/images/star.png" style="height:30px;width:30px"> <img src="assets/images/star.png" style="height:30px;width:30px"></td>
                              <td width="30px" align="center"><?=thousandsNumberFormat(getPostRating($row['id'],'2','movie'))?></td>
                              <td align="left" style="padding:10px"><span style="display:block;height:15px;background-color:#ea1f62;width:0%"></span></td>
                            </tr>
                            <tr>
                              <td width="50%" align="right" style="padding:5px;"><img src="assets/images/star_e.png" style="height:30px;width:30px"> <img src="assets/images/star_e.png" style="height:30px;width:30px"> <img src="assets/images/star_e.png" style="height:30px;width:30px"> <img src="assets/images/star_e.png" style="height:30px;width:30px"> <img src="assets/images/star.png" style="height:30px;width:30px"></td>
                              <td width="30px" align="center"><?=thousandsNumberFormat(getPostRating($row['id'],'1','movie'))?></td>
                              <td align="left" style="padding:10px"><span style="display:block;height:15px;background-color:#ea1f62;width:0%"></span></td>
                            </tr>
                            </tbody>
                          </table>
                          </div>
                      </div>
                  </li>
                  <li><a href="" style="background:rgba(239, 244, 248, 0.30);width:74px;border-radius:4px;" class="btn_delete_a" data-id="<?php echo $row['id'];?>" data-toggle="tooltip" data-tooltip="Delete"> Delete <i class="fa fa-trash"></i></a></li>
                  <li><a href="add_paidmovie.php?movie_id=<?php echo $row['id'];?>&redirect=<?=$redirectUrl?>"  style="background:linear-gradient(91.59deg, rgb(148, 63, 213) 1.37%, rgb(97, 43, 173) 98.71%);width:74px;border-radius:4px;" data-toggle="tooltip" data-tooltip="Edit"> Edit <i class="fa fa-edit"></i></a></li>
                  <?php if($row['status']!="0"){?>
                  <li><div class="row toggle_btn"><a href="javascript:void(0)" data-id="<?php echo $row['id'];?>" data-action="deactive" data-column="status" data-toggle="tooltip" data-tooltip="ENABLE"><img src="assets/images/btn_enabled.png" alt="wallpaper_1" /></a></div></li>

                  <?php }else{?>
                  
                  <li><div class="row toggle_btn"><a href="javascript:void(0)" data-id="<?=$row['id']?>" data-action="active" data-column="status" data-toggle="tooltip" data-tooltip="DISABLE"><img src="assets/images/btn_disabled.png" alt="wallpaper_1" /></a></div></li>
              
                  <?php }?>
               </ul>
                
              </div>
              <span style="border-radius:20px;"><img src="<?php echo $row['movie_cover'];?>" /></span>
            </div>
            <div></div>
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
          	<?php if(!isset($_POST["search"])){ include("pagination.php");}?>                 
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
    var _table='tbl_movies';

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
      var _table='tbl_movies';

      swal({
          title: "Are you sure?",
          text: "All data will be deleted of this movie.",
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
                        text: "Movie is deleted...", 
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

              var _table='tbl_movies';

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
          swal("Sorry no movie selected !!")
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
