<?php 

$page_title="Manage Active Users";

include('includes/header.php'); 
include('includes/function.php');
include('language/language.php');  


if(isset($_POST['user_search']))
{

	$searchInput=cleanInput($_POST['search_value']);

	$user_qry="SELECT * FROM tbl_users WHERE tbl_users.`status`=1 and tbl_users.`id` LIKE '%$searchInput%' OR  tbl_users.`name` LIKE '%$searchInput%' OR  tbl_users.`phone` LIKE '%$searchInput%' OR tbl_users.`email` LIKE '%$searchInput%'  ORDER BY tbl_users.`id` DESC";  

	$users_result=mysqli_query($mysqli,$user_qry);


}
else
{

	$tableName="tbl_users";		
	$targetpage = "manage_users.php"; 	
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


	$users_qry="SELECT * FROM tbl_users where tbl_users.`status`=1 
	ORDER BY tbl_users.`id` DESC LIMIT $start, $limit";  

	$users_result=mysqli_query($mysqli,$users_qry);

}
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
								<input class="form-control input-sm" placeholder="Search..." aria-controls="DataTables_Table_0" type="search" value="<?=(isset($_POST['search_value'])) ? trim($_POST['search_value']) : ''?>" name="search_value" required>
								<button type="submit" name="user_search" class="btn-search"><i class="fa fa-search"></i></button>
							</form>  
						</div>
						<!--<div class="add_btn_primary"> <a href="add_user.php?add">Add User  &nbsp;<i class="fa fa-plus-circle"></i></a> </div>-->
					</div>
				</div>
				
				</div>
				<div class="clearfix"></div>
				<div class="col-md-12 mrg-top">
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th style="width: 50px"></th>
								<th>Userid</th>
								<th>Name</th>						 
								<th>Email</th>
								<th>Phone</th>
								<th>Userprofile</th>	
								<th>Register On</th>
								<th>Status</th>				   
								<th class="text-center">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$i=0;

							if(mysqli_num_rows($users_result) > 0)
							{
								while($users_row=mysqli_fetch_array($users_result))
								{
								        $user_id = $users_row['id'];
                                       
                                        $subscription = null;
                                        $query = "SELECT u.plan_type,u.plan_validation,u.plan_buy_date,s.plan FROM tbl_users u  LEFT JOIN tbl_subscription s on u.plan_id=s.id  WHERE u.id = '$user_id' and u.plan_expire=0 ORDER BY u.id DESC LIMIT 1";
                                        $result = mysqli_query($mysqli, $query);
                                        if ($result && mysqli_num_rows($result) > 0) {
                                            $subscription = mysqli_fetch_array($result); // or mysqli_fetch_assoc($result)
                                        }
									?>
									<tr>
										<td>
											<div class="checkbox" style="float: right;margin: 0px">
												<input type="checkbox" name="post_ids[]" id="checkbox<?php echo $i;?>" value="<?php echo $users_row['id']; ?>" class="post_ids" style="margin: 0px;">
												<label for="checkbox<?php echo $i;?>">
												</label>
											</div>
										</td>
											<td><?php echo $users_row['id'];?></td>
										<td><?php echo $users_row['name'];?></td>
										<td><?php echo $users_row['email'];?></td>   
										<td><?php echo $users_row['phone'];?></td>
										<td width="30px;"><img src="<?php echo $users_row['image'];?>"/> </td>
											<td><?php echo $users_row['register_on'];?></td>
										<td>
											<?php if($users_row['status']!="0"){?>
												<a title="Change Status" class="toggle_btn_a" href="javascript:void(0)" data-id="<?=$users_row['id']?>" data-action="deactive" data-column="status"><span class="badge badge-success badge-icon"><i class="fa fa-check" aria-hidden="true"></i><span>Enable</span></span></a>

											<?php }else{?>
												<a title="Change Status" class="toggle_btn_a" href="javascript:void(0)" data-id="<?=$users_row['id']?>" data-action="active" data-column="status"><span class="badge badge-danger badge-icon"><i class="fa fa-check" aria-hidden="true"></i><span>Disable </span></span></a>
											<?php }?>
										</td>

										<td class="text-center">
											<!--<a href="add_user.php?user_id=<?php echo $users_row['id'];?>&redirect=<?=$redirectUrl?>" class="btn btn-primary btn_edit" data-toogle="tooltip" data-tooltip="Edit">
												<i class="fa fa-edit"></i>
											</a>-->

											<a href="" class="btn btn-danger btn_delete" data-id="<?=$users_row['id']?>" data-toogle="tooltip" data-tooltip="Delete">
												<i class="fa fa-trash"></i>
											</a>
											
											<a href="logoutt.php?user_id=<?php echo $users_row['id'];?>" class="btn btn-success" data-toggle="tooltip" title="Logout">
                                                <i class="fa fa-sign-out"></i>
                                            </a>
                                            
                                            
                                            
                                           <a href="#" class="btn btn-info" data-toggle="modal" data-target="#subscriptionModal">
                                            <i class="fa fa-tasks"></i>
                                            </a>
                                            <div class="modal fade" id="subscriptionModal" tabindex="-1" role="dialog" aria-labelledby="subscriptionModalLabel" aria-hidden="true">
                                              <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                            
                                                  <div class="modal-header">
                                                    <h5 class="modal-title" id="subscriptionModalLabel" style="text-align:left;">Current Subscription Info</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <!-- For Bootstrap 5: data-bs-dismiss -->
                                                      <span aria-hidden="true">&times;</span>
                                                    </button>
                                                  </div>
                                            
                                                  <div class="modal-body" style="text-align:left;">
                                						<?php if (!empty($subscription)) { 
                                                            $buyDate = $subscription['plan_buy_date'];
                                                            $validationDays = intval($subscription['plan_validation']);
                                                            
                                                            $expireDate = date('Y-m-d', strtotime($buyDate . " +$validationDays days"));
                                                            $today = date('Y-m-d');
                                                            $daysLeft = (strtotime($expireDate) - strtotime($today)) / (60 * 60 * 24);
                                                            $daysLeft = max(0, (int)$daysLeft); // prevent negative values
                                                        ?>
                                                            <p><strong>Plan Name :-</strong> <?= $subscription['plan']; ?></p>
                                                            <p><strong>Plan Type :-</strong> <?= $subscription['plan_type']; ?></p>
                                                            <p><strong>Plan Validation :-</strong> <?= $validationDays; ?> Days</p>
                                                            <p><strong>Subscribe Date :-</strong> <?= date('d M Y', strtotime($buyDate)); ?></p>
                                                            <p><strong>Expiry Date :-</strong> <?= date('d M Y', strtotime($expireDate)); ?></p>
                                                            <p><strong>Days Left :-</strong> <?= $daysLeft; ?> days</p>
                                                        <?php } else { ?>
                                                            <p>No subscription found for this user.</p>
                                                        <?php } ?>

                                					</div>
                                                </div>
                                              </div>
                                            </div>
                                            
                                            
                                            <a href="getUserBuyMovies.php?user_id=<?php echo $users_row['id'];?>" class="btn btn-warning" data-toggle="tooltip" title="Get Buy Movies" style="margin-top:5px;">
                                                <i class="fa fa-video-camera"></i>
                                            </a>
										</td>
									</tr>
									<?php		
									$i++;
								}
							}
							else{
								?>
								<tr>
									<td colspan="7">
										<p class="not_data"><strong>Sorry!</strong> no data found</p>
									</td>
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



<div class="subscription-details-box mt-2" id="details-<?= $users_row['id'] ?>" style="display:none; border:1px solid #ccc; padding:10px; border-radius:5px;"></div>



	<?php include('includes/footer.php');?>   
	
	



	<script type="text/javascript">
		$(".toggle_btn_a").on("click",function(e){
			e.preventDefault();

			var _for=$(this).data("action");
			var _id=$(this).data("id");
			var _column=$(this).data("column");
			var _table='tbl_users';

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

		$(".btn_delete").click(function(e){

			e.preventDefault();

			var _id=$(this).data("id");
			var _table='tbl_users';

			swal({
				title: "Are you sure?",
				text: "All data will be deleted of this user.",
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
									text: "User is deleted...", 
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

						var _table='tbl_users';

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
				swal("Sorry no users selected !!")
			}
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