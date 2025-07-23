<?php 

$page_title = "User Buy Movies";

include('includes/header.php'); 
include('includes/function.php');
include('language/language.php');  

$userid = @$_GET['user_id'];

$tableName = "tbl_buymovies";		
$targetpage = "getUserBuyMovies.php"; 	
$limit = 15; 

$query = "SELECT COUNT(*) as num FROM $tableName WHERE userid = '$userid'";
$total_pages = mysqli_fetch_array(mysqli_query($mysqli, $query));
$total_pages = $total_pages['num'];

$stages = 3;
$page = 0;
if (isset($_GET['page'])) {
	$page = mysqli_real_escape_string($mysqli, $_GET['page']);
}
if ($page) {
	$start = ($page - 1) * $limit; 
} else {
	$start = 0;	
}	

$users_qry = "SELECT bm.*, m.* 
	FROM tbl_buymovies bm 
	LEFT JOIN tbl_rentmovies m ON bm.movie_id = m.id  
	WHERE m.status = 1 AND bm.userid = '$userid' and bm.expire=0
	ORDER BY bm.id DESC 
	LIMIT $start, $limit";

$users_result = mysqli_query($mysqli, $users_qry);
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<div class="row">
	<div class="col-xs-12">
		<div class="card mrg_bottom">
			<div class="page_title_block">
				<div class="col-md-5 col-xs-12">
					<div class="page_title"><?= $page_title ?></div>
				</div>
			</div>

			<div class="clearfix"></div>

			<div class="col-md-12 mrg-top">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th style="width: 50px"></th>
							<th>Movie Name</th>
							<th style="width: 100px">Movie Img</th>
							<th>Purchase Date</th>
							<th>Expire Date</th> <!-- NEW COLUMN -->
						</tr>
					</thead>
					<tbody>
						<?php
						$i = 0;
						if (mysqli_num_rows($users_result) > 0) {
							while ($users_row = mysqli_fetch_array($users_result)) {
								$purchaseDate = $users_row['purchase_dt'];
								$validityDays = intval($users_row['movie_validation']);
								$expireDate = date('Y-m-d', strtotime($purchaseDate . " +$validityDays days"));
								?>
								<tr>
									<td><?= ++$i ?></td>
									<td><?= $users_row['movie_title'] ?></td>
									<td><img src="<?= $users_row['movie_poster'] ?>" width="80px" /></td>
									<td><?= $purchaseDate ?></td>
									<td><?= $expireDate ?></td> <!-- NEW -->
								</tr>
								<?php
							}
						} else {
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
						<?php if (!isset($_POST["search"])) { include("pagination.php"); } ?>                 
					</nav>
				</div>
			</div>

			<div class="clearfix"></div>
		</div>
	</div>
</div>

<?php include('includes/footer.php'); ?> 
