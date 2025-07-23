<?php 

$page_title="Manage Plan";

include('includes/header.php'); 
include('includes/function.php');
include('language/language.php');  


if(isset($_POST['user_search']))
{

	$searchInput=cleanInput($_POST['search_value']);

	$user_qry="SELECT * FROM tbl_subscription WHERE tbl_subscription.`subscription_name` LIKE '%$searchInput%' OR tbl_subscription.`subscription_name` LIKE '%$searchInput%' ORDER BY tbl_subscription.`id` DESC";  

	$users_result=mysqli_query($mysqli,$user_qry);


}
else
{

	$tableName="tbl_subscription";		
	$targetpage = "manage_plan.php"; 	
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


	$users_qry="SELECT * FROM tbl_subscription
	ORDER BY tbl_subscription.`id` DESC LIMIT $start, $limit";  

	$users_result=mysqli_query($mysqli,$users_qry);

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
								<input class="form-control input-sm" placeholder="Search..." aria-controls="DataTables_Table_0" type="search" value="<?=(isset($_POST['search_value'])) ? trim($_POST['search_value']) : ''?>" name="search_value" required>
								<button type="submit" name="user_search" class="btn-search"><i class="fa fa-search"></i></button>
							</form>  
						</div>
						<div class="add_btn_primary"> <a href="add_plan.php?add">Add Plan  &nbsp;<i class="fa fa-plus-circle"></i></a> </div>
					</div>
				</div>
				
				</div>
				<div class="clearfix"></div>
				<div class="col-md-12 mrg-top">
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th style="width: 50px"></th>
								<th>Plan Name</th>
								<th>Plan Type</th>
								<th>plan Description</th>
								<th>Validity</th>
								<th>Price</th>
								<th>Discount Price</th>
								<th>Color</th>
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

									?>
									<tr>
										<td>
											<?php echo $users_row['id'];?>
										</td>
										<td><?php echo $users_row['plan'];?></td>
										<td><?php echo $users_row['plan_type'];?></td>
										<td><?php echo $users_row['description'];?></td>   
										<td><?php echo $users_row['validity'];?></td>
										<td><?php echo $users_row['price'];?></td>
										<td><?php echo $users_row['discount_price'];?></td>
										<td><div style="height: 50px;background-color:<?php echo $users_row['color'];?>;"> </div></td>
										<td>
											<?php if($users_row['status']!="0"){?>
												<a title="Change Status" class="toggle_btn_a" href="javascript:void(0)" data-id="<?=$users_row['id']?>" data-action="deactive" data-column="status"><span class="badge badge-success badge-icon"><i class="fa fa-check" aria-hidden="true"></i><span>Enable</span></span></a>

											<?php }else{?>
												<a title="Change Status" class="toggle_btn_a" href="javascript:void(0)" data-id="<?=$users_row['id']?>" data-action="active" data-column="status"><span class="badge badge-danger badge-icon"><i class="fa fa-check" aria-hidden="true"></i><span>Disable </span></span></a>
											<?php }?>
										</td>

										<td class="text-center">
											<a href="add_plan.php?id=<?php echo $users_row['id'];?>&redirect=<?=$redirectUrl?>" class="btn btn-warning btn_edit" style="backgroung:linear-gradient(91.59deg, rgb(148, 63, 213) 1.37%, rgb(97, 43, 173) 98.71%);" data-toogle="tooltip" data-tooltip="Edit">
												<i class="fa fa-edit"></i>
											</a>

											<a href="" class="btn btn-info btn_delete" data-id="<?=$users_row['id']?>" data-toogle="tooltip" data-tooltip="Delete">
												<i class="fa fa-trash"></i>
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



	<?php include('includes/footer.php');?>   

	<script type="text/javascript">
		$(".toggle_btn_a").on("click",function(e){
			e.preventDefault();

			var _for=$(this).data("action");
			var _id=$(this).data("id");
			var _column=$(this).data("column");
			var _table='tbl_subscription';

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
			var _table='tbl_subscription';

			swal({
				title: "Are you sure?",
				text: "All data will be deleted of this Plan.",
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
									text: "Plan is deleted...", 
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

						var _table='tbl_subscription';

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