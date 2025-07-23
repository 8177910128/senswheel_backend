<?php
	$page_title="View Series Reports";
	$active_page="report";

	include('includes/header.php'); 
	include('includes/function.php');
	include('language/language.php');

	$img='assets/images/user_photo.png';

	function total_comments($post_id, $type='series')
	{
		global $mysqli;

		$query="SELECT COUNT(*) AS total_comments FROM tbl_comments WHERE `post_id`='$post_id' AND `type`='$type'";
		$sql = mysqli_query($mysqli,$query)or die(mysqli_error());
		$row=mysqli_fetch_assoc($sql);
		return stripslashes($row['total_comments']);
	}

 	$id=trim($_GET['post_id']);

	$sql="SELECT * FROM tbl_series WHERE `id`='$id' ORDER BY tbl_series.`id` DESC";

	$res=mysqli_query($mysqli,$sql);
	$row=mysqli_fetch_assoc($res);

 	$sql1="SELECT tbl_reports.*, tbl_users.`name` FROM tbl_reports, tbl_users 
 			WHERE tbl_reports.`user_id`=tbl_users.`id` AND tbl_reports.`post_id`='$id' AND tbl_reports.`type`='series' ORDER BY tbl_reports.`id` DESC";

	$res_report=mysqli_query($mysqli, $sql1) or die(mysqli_error($mysqli));
	$arr_dates=array();
	$i=0;
	while($report=mysqli_fetch_assoc($res_report)){
		$dates=date('d M Y',$report['report_on']);
		$arr_dates[$dates][$i++]=$report;
	}

?>
<style type="text/css">
	.app-messaging ul.chat li .message{
		padding: 5px 10px 15px 5px;
		min-height: 60px
	}
	.app-messaging ul.chat li .message span.comment-text-item{
		margin-top:8px;
		display:inline-block;
	}
	.app-messaging .messaging {
	    transform: translate(0, 0);
	}
	@media (max-width: 767px) {
		.app-messaging .heading .title{
			font-size:14px !important;
		}	
	}
</style>

<div class="app-messaging-container">
	<div class="app-messaging" id="collapseMessaging">
	<div class="messaging">
		<h3 style="padding-left: 20px">Report Section</h3>
		<div class="heading">
			<div class="title" style="font-size: 16px">
				<a class="btn-back" href="manage_reports.php">
					<i class="fa fa-angle-left" aria-hidden="true"></i>
				</a>
				<strong>Title:</strong>&nbsp;&nbsp;<?=$row['series_name']?>
			</div>
		</div>
		<ul class="chat" style="flex: unset;height: 500px;">
		<?php 
		if(!empty($arr_dates))
		{
			foreach ($arr_dates as $key => $val) {
			?>
			<li class="line">
				<div class="title"><?=$key?></div>
			</li>
			<?php 
			foreach ($val as $key1 => $value) {

			?>
			<li class="<?=$value['id']?>" style="padding-right: 20px">

			<div class="message">
			<img src="<?=$img?>" style="width: 50px;float: left;margin-right: 10px;border-radius: 50%;box-shadow: 0px 0px 2px 1px #ccc">
			<span style="color: #000;font-weight: 600"><?=$value['name']?></span>
			<br/>
			<span class="comment-text-item">
			<?=$value['report']?>	
			</span>
			</div>
			<div class="info" style="clear: both;">
			<div class="datetime">
				<span title="<?=date('d-m-Y',$value['report_on'])?>"><?=calculate_time_span($value['report_on'],true)?></span>
				<a href="javascript:void(0)" title="Delete" class="btn_delete" data-id="<?=$value['id']?>" style="color: red;text-decoration: none;"><i class="fa fa-trash"></i> Delete</a>
			</div>
			</div>
			</li>
			<?php } // end of inner foreach
			}	// end of main foreach
		}	// end of if
		else{
		?>
		<div class="jumbotron" style="width: 100%; text-align: center;">
		<h3>Sorry !</h3> 
		<p>No comments available</p> 
		</div>
		<?php
		} 
		?>
		</ul>
	</div>
</div>
</div>


<?php 
include('includes/footer.php');
?> 

<script type="text/javascript">

	$(".btn_delete").click(function(e){

      e.preventDefault();

      var _id=$(this).data("id");

      swal({
          title: "Are you sure?",
          text: "Do you really want delete this.",
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
              data:{id:_id,'action':'removeReport'},
              error: function (request, error) {
				swal('Something went to wrong !');
			  },
              success:function(res){
                  console.log(res);
                  if(res.status=='1'){
                    swal({
                        title: "Successfully", 
                        text: "Report is deleted...", 
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