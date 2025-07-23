<?php 
  
  $page_title="Manage Reports";
  include("includes/header.php");

  require("includes/function.php");
  require("language/language.php");

  function get_post($post_id,$type)
  {
    global $mysqli;

    switch ($type) {
      case 'series':
        $sql="SELECT * FROM tbl_series where id='$post_id' AND status='1'";
        break;

      case 'movie':
        $sql="SELECT * FROM tbl_movies where id='$post_id' AND status='1'";
        break;

      case 'channel':
        $sql="SELECT * FROM tbl_channels where id='$post_id' AND status='1'";
        break;
      
      default:
        break;
    }

    $res=mysqli_query($mysqli,$sql);
    $data=mysqli_fetch_assoc($res);

    return $data;
  }
  
  function get_total_reports($post_id, $type='')
  {
    global $mysqli;

    $qry="SELECT COUNT(*) AS total_report FROM tbl_reports WHERE `post_id`='$post_id' AND `type`='$type'";
    $res=mysqli_query($mysqli,$qry);
    $row=mysqli_fetch_assoc($res);
    return $row['total_report'];
  }
  
?>

<style type="text/css">
  .top{
    position: relative !important;
    padding: 0px 0px 20px 0px !important;
  }
  .dataTables_wrapper{
    overflow: unset !important;
  }
</style>

<div class="row">
  <div class="col-xs-12">
    <div class="card mrg_bottom">
      <div class="page_title_block">
        <div class="col-md-5 col-xs-12">
          <div class="page_title"><?=$page_title?></div>
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="col-md-12" style="padding: 0px">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#series_report" aria-controls="series_report" role="tab" data-toggle="tab">Series Report</a></li>
            <li role="presentation"><a href="#movie_report" aria-controls="movie_report" role="tab" data-toggle="tab">Movie Report</a></li>
            <li role="presentation"><a href="#channel_report" aria-controls="channel_report" role="tab" data-toggle="tab">Channel Report</a></li>
        </ul>
      
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="series_report">
            <div class="rows">
              <div class="col-md-12">
                <table class="datatable table table-striped table-bordered table-hover">
                  <thead>
                    <tr>
                      <th>Series</th>
                      <th>Total Reports</th>
                      <th>Last Report On</th> 
                      <th class="cat_action_list">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $sql="SELECT 
                    tbl_reports.`id` AS id,
                    tbl_reports.`type` AS type, 
                    tbl_reports.`post_id` as post_id, max(tbl_reports.`report_on`) as report_on 
                    FROM tbl_reports 
                    JOIN tbl_users 
                    ON tbl_reports.`user_id`=tbl_users.`id`
                    WHERE tbl_reports.`type`='series' GROUP BY tbl_reports.`post_id` ORDER BY tbl_reports.`id` DESC";

                    $result=mysqli_query($mysqli,$sql);

                    $i=0;
                    while($row=mysqli_fetch_array($result))
                    {
                      ?>
                      <tr>
                        <td><?php echo get_post($row['post_id'],$row['type'])['series_name'];?></td>
                        <td>
                          <a href="view_series_reports.php?post_id=<?=$row['post_id']?>">
                            <?php echo get_total_reports($row['post_id'],$row['type']);?> Reports
                          </a>
                        </td>
                        <td nowrap=""><?php echo calculate_time_span($row['report_on'],true);?></td>
                        <td>
                          <a href="" data-post="<?php echo $row['post_id'];?>" data-type="<?=$row['type']?>" class="btn btn-danger btn_delete"><i class="fa fa-trash"></i> Delete</a>
                        </td>
                      </tr>
                      <?php
                      $i++;
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="clearfix"></div>
          </div>

          <div role="tabpanel" class="tab-pane" id="movie_report">
            <div class="rows">
              <div class="col-md-12">
                <table class="datatable table table-striped table-bordered table-hover">
                  <thead>
                    <tr>
                      <th>Movie</th>
                      <th>Total Reports</th>
                      <th>Last Report On</th> 
                      <th class="cat_action_list">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $sql="SELECT 
                            tbl_reports.`id` AS id,
                            tbl_reports.`type` AS type, 
                            tbl_reports.`post_id` as post_id, max(tbl_reports.`report_on`) as report_on 
                            FROM tbl_reports 
                            JOIN tbl_users 
                            ON tbl_reports.`user_id`=tbl_users.`id`
                            WHERE tbl_reports.`type`='movie' GROUP BY tbl_reports.`post_id` ORDER BY tbl_reports.`id` DESC";

                      $result=mysqli_query($mysqli,$sql);
                      $i=0;
                      while($row=mysqli_fetch_array($result))
                      {
                    ?>
                    <tr>
                      <td><?php echo get_post($row['post_id'],$row['type'])['movie_title'];?></td>
                      <td>
                        <a href="view_movie_reports.php?post_id=<?=$row['post_id']?>">
                          <?php echo get_total_reports($row['post_id'],$row['type']);?> Reports
                        </a>
                      </td>
                      <td nowrap=""><?php echo calculate_time_span($row['report_on'],true);?></td>
                      <td>
                        <a href="" data-post="<?php echo $row['post_id'];?>" data-type="<?=$row['type']?>" class="btn btn-danger btn_delete"><i class="fa fa-trash"></i> Delete</a>
                      </td>
                    </tr>
                    <?php
                      $i++;
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div role="tabpanel" class="tab-pane" id="channel_report">
            <div class="rows">
              <div class="col-md-12">
                <table class="datatable table table-striped table-bordered table-hover">
                  <thead>
                    <tr>
                      <th>Channel</th>
                      <th>Total Reports</th>
                      <th>Last Report On</th> 
                      <th class="cat_action_list">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $sql="SELECT 
                    tbl_reports.`id` AS id,
                    tbl_reports.`type` AS type, 
                    tbl_reports.`post_id` as post_id, max(tbl_reports.`report_on`) as report_on 
                    FROM tbl_reports 
                    JOIN tbl_users 
                    ON tbl_reports.`user_id`=tbl_users.`id`
                    WHERE tbl_reports.`type`='channel' GROUP BY tbl_reports.`post_id` ORDER BY tbl_reports.`id` DESC";

                    $result=mysqli_query($mysqli,$sql);
                    $i=0;
                    while($row=mysqli_fetch_array($result))
                    {
                      ?>
                      <tr>
                        <td><?php echo get_post($row['post_id'],$row['type'])['channel_title'];?></td>
                        <td>
                          <a href="view_channel_reports.php?post_id=<?=$row['post_id']?>">
                            <?php echo get_total_reports($row['post_id'],$row['type']);?> Reports
                          </a>
                        </td>
                        <td nowrap=""><?php echo calculate_time_span($row['report_on'],true);?></td>
                        <td>
                          <a href="" data-post="<?php echo $row['post_id'];?>" data-type="<?=$row['type']?>" class="btn btn-danger btn_delete"><i class="fa fa-trash"></i> Delete</a>
                        </td>
                      </tr>
                      <?php
                      $i++;
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="clearfix"></div>
    </div>
  </div>
</div>            
        
<?php include("includes/footer.php");?>       

<script type="text/javascript">
  $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
    localStorage.setItem('activeTab', $(e.target).attr('href'));
  });
  var activeTab = localStorage.getItem('activeTab');
  if(activeTab){
    $('.nav-tabs a[href="' + activeTab + '"]').tab('show');
  }

  $(".btn_delete").click(function(e){

      e.preventDefault();

      var _id=$(this).data("post");
      var _type=$(this).data("type");

      swal({
          title: "Are you sure?",
          text: "All reports will be deleted of this.",
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
              data:{id:_id,'type': _type,'action':'removeAllReports'},
              success:function(res){
                  console.log(res);
                  if(res.status=='1'){
                    swal({
                        title: "Successfully", 
                        text: "Reports are deleted...", 
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