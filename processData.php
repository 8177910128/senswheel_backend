<?php 
	require("includes/connection.php");
	require("includes/function.php");
	require("language/language.php");

	$response=array();

	// get total comments
	function total_comments($news_id)
	{
		global $mysqli;

		$query="SELECT COUNT(*) AS total_comments FROM tbl_comments WHERE `news_id`='$news_id'";
		$sql = mysqli_query($mysqli,$query)or die(mysqli_error());
		$row=mysqli_fetch_assoc($sql);
		return stripslashes($row['total_comments']);
	}

	switch ($_POST['action']) {
		case 'toggle_status':
			$id=$_POST['id'];
			$for_action=$_POST['for_action'];
			$column=$_POST['column'];
			$tbl_id=$_POST['tbl_id'];
			$table_nm=$_POST['table'];

			if($for_action=='active'){
				$data = array($column  =>  '1');
			    $edit_status=Update($table_nm, $data, "WHERE $tbl_id = '$id'");

			    if($column=='is_slider' || $column=='slider_channel'){
			    	$_SESSION['msg']="20";
			    }
			    else{
			    	$_SESSION['msg']="13";
			    }

			}else{
				$data = array($column  =>  '0');
			    $edit_status=Update($table_nm, $data, "WHERE $tbl_id = '$id'");

			    if($column=='is_slider' || $column=='slider_channel'){
			    	$_SESSION['msg']="21";
			    }
			    else{
			    	$_SESSION['msg']="14";
			    }
			}
			
	      	$response['status']=1;
	      	$response['action']=$for_action;
	      	echo json_encode($response);
			break;

		case 'removeComment':
			$id=$_POST['id'];

			Delete('tbl_comments','id='.$id);

	      	$response['status']=1;
	      	$response['message']='Something went to wrong !';
	      	echo json_encode($response);
			break;

		case 'removeAllComment':
			$news_id=$_POST['news_id'];

			Delete('tbl_comments','news_id='.$news_id);

	      	$response['status']=1;
	      	echo json_encode($response);
			break;

		case 'removeReport':
			$id=$_POST['id'];

			Delete('tbl_reports','id='.$id);

	      	$response['status']=1;
	      	$response['message']='Something went to wrong !';
	      	echo json_encode($response);
			break;

		case 'removeAllReports':
			$id=$_POST['id'];
			$type=$_POST['type'];

			$deleteSql="DELETE FROM tbl_reports WHERE `post_id`='$id' AND `type`='$type'";
			
			if(mysqli_query($mysqli, $deleteSql)){
				$response['status']=1;
	      		$response['message']='Something went to wrong !';	
			}
			else{
				$response['status']=-2;
	      		$response['message']='Something went to wrong !';
			}

	      	
	      	echo json_encode($response);
			break;

		case 'getSeason':
			$id=$_POST['id'];

			$sql="SELECT * FROM tbl_season WHERE series_id='$id'";
			$res=mysqli_query($mysqli,$sql);
			$result=array();
			while ($row=mysqli_fetch_assoc($res)) {
				$opt='<option value="'.$row['id'].'">'.$row['season_name'].'</option>';
				array_push($result, $opt);
			}

	      	$response['status']=1;
	      	$response['data']=$result;
	      	echo json_encode($response);
			break;
			
			
			case 'getTvSeason':
			$id=$_POST['id'];

			$sql="SELECT * FROM tbl_tv_season WHERE shows_id='$id'";
			$res=mysqli_query($mysqli,$sql);
			$result=array();
			while ($row=mysqli_fetch_assoc($res)) {
				$opt='<option value="'.$row['id'].'">'.$row['season_name'].'</option>';
				array_push($result, $opt);
			}

	      	$response['status']=1;
	      	$response['data']=$result;
	      	echo json_encode($response);
			break;

		case 'multi_delete':

			$ids=implode(",", $_POST['id']);

			if($ids==''){
				$ids=$_POST['id'];
			}

			$tbl_nm=$_POST['tbl_nm'];

			if($tbl_nm=='tbl_movies'){
				$sql="SELECT * FROM $tbl_nm WHERE `id` IN ($ids)";
				$res=mysqli_query($mysqli, $sql);
				while ($row=mysqli_fetch_assoc($res)){
					if($row['movie_cover']!="")
					{
						unlink('images/movies/'.$row['movie_cover']);
						unlink('images/movies/thumbs/'.$row['movie_cover']);
					}

					if($row['movie_poster']!="")
					{
						unlink('images/movies/'.$row['movie_poster']);
						unlink('images/movies/thumbs/'.$row['movie_poster']);
					}

					if($row['movie_type']=="local")
					{
						unlink('uploads/'.$row['movie_url']);
					}

					$sql_subtitle="SELECT * FROM tbl_subtitles WHERE `post_id`='".$row['id']."' AND `type`='movie'";
					$res_subtitle=mysqli_query($mysqli, $sql_subtitle);

					while ($row_subtitle=mysqli_fetch_assoc($res_subtitle)) {
						if($row_subtitle['subtitle_type']=='local'){
							unlink('uploads/'.$row_subtitle['subtitle_url']);	
						}

						Delete('tbl_subtitles','id='.$row_subtitle['id']);
					}

					mysqli_free_result($res_subtitle);

					$sql_quality="SELECT * FROM tbl_quality WHERE `post_id`='".$row['id']."' AND `type`='movie'";
					$res_quality=mysqli_query($mysqli, $sql_quality);

					while ($row_quality=mysqli_fetch_assoc($res_quality)) {
						if($row_quality['post_upload_type']=='local'){
							unlink('uploads/'.$row_quality['quality_480']);
							unlink('uploads/'.$row_quality['quality_720']);	
							unlink('uploads/'.$row_quality['quality_1080']);
						}

						Delete('tbl_quality','id='.$row_quality['id']);
					}

					mysqli_free_result($res_quality);

				}
				$deleteSql="DELETE FROM $tbl_nm WHERE `id` IN ($ids)";
			}

			else if($tbl_nm=='tbl_series'){

				$sql="SELECT * FROM tbl_episode WHERE `series_id` IN ($ids)";

				$res=mysqli_query($mysqli, $sql);
				while ($row=mysqli_fetch_assoc($res)){
					if($row['episode_poster']!="")
					{
						unlink('images/episodes/'.$row['episode_poster']);
						unlink('images/episodes/thumbs/'.$row['episode_poster']);
					}

					if($row['episode_type']=="local")
					{
						unlink('uploads/'.$row['episode_url']);
					}


					$sql_subtitle="SELECT * FROM tbl_subtitles WHERE `post_id`='".$row['id']."' AND `type`='episode'";
					$res_subtitle=mysqli_query($mysqli, $sql_subtitle);

					while ($row_subtitle=mysqli_fetch_assoc($res_subtitle)) {
						if($row_subtitle['subtitle_type']=='local'){
							unlink('uploads/'.$row_subtitle['subtitle_url']);	
						}

						Delete('tbl_subtitles','id='.$row_subtitle['id']);
					}

					mysqli_free_result($res_subtitle);

					$sql_quality="SELECT * FROM tbl_quality WHERE `post_id`='".$row['id']."' AND `type`='episode'";
					$res_quality=mysqli_query($mysqli, $sql_quality);

					while ($row_quality=mysqli_fetch_assoc($res_quality)) {
						if($row_quality['post_upload_type']=='local'){
							unlink('uploads/'.$row_quality['quality_480']);
							unlink('uploads/'.$row_quality['quality_720']);	
							unlink('uploads/'.$row_quality['quality_1080']);
						}

						Delete('tbl_quality','id='.$row_quality['id']);
					}

					mysqli_free_result($res_quality);
				}

				$deleteSql="DELETE FROM tbl_season WHERE `series_id` IN ($ids)";
				mysqli_query($mysqli, $deleteSql);

				$deleteSql="DELETE FROM tbl_comments WHERE `post_id` IN ($ids) AND `type`='series'";
				mysqli_query($mysqli, $deleteSql);

				$deleteSql="DELETE FROM tbl_reports WHERE `post_id` IN ($ids) AND `type`='series'";
				mysqli_query($mysqli, $deleteSql);

				$deleteSql="DELETE FROM tbl_rating WHERE `post_id` IN ($ids) AND `type`='series'";
				mysqli_query($mysqli, $deleteSql);

				$res_series=mysqli_query($mysqli,"SELECT * FROM tbl_series WHERE `id` IN ($ids)");
				while($row=mysqli_fetch_assoc($res_series)){
					if($row['series_poster']!="" AND file_exists('images/series/'.$row['series_poster']))
					{
						unlink('images/series/'.$row['series_poster']);
						unlink('images/series/thumbs/'.$row['series_poster']);
					}

					if($row['series_cover']!="" AND file_exists('images/series/'.$row['series_cover']))
					{
						unlink('images/series/'.$row['series_cover']);
						unlink('images/series/thumbs/'.$row['series_cover']);
					}
				}

				$deleteSql="DELETE FROM $tbl_nm WHERE `id` IN ($ids)";

			}

			else if($tbl_nm=='tbl_season'){

				$sql="SELECT * FROM tbl_episode WHERE `season_id` IN ($ids)";

				$res=mysqli_query($mysqli, $sql);
				while ($row=mysqli_fetch_assoc($res)){
					if($row['episode_poster']!="")
					{
						unlink('images/episodes/'.$row['episode_poster']);
						unlink('images/episodes/thumbs/'.$row['episode_poster']);
					}

					if($row['episode_type']=="local")
					{
						unlink('uploads/'.$row['episode_url']);
					}


					$sql_subtitle="SELECT * FROM tbl_subtitles WHERE `post_id`='".$row['id']."' AND `type`='episode'";
					$res_subtitle=mysqli_query($mysqli, $sql_subtitle);

					while ($row_subtitle=mysqli_fetch_assoc($res_subtitle)) {
						if($row_subtitle['subtitle_type']=='local'){
							unlink('uploads/'.$row_subtitle['subtitle_url']);	
						}

						Delete('tbl_subtitles','id='.$row_subtitle['id']);
					}

					mysqli_free_result($res_subtitle);

					$sql_quality="SELECT * FROM tbl_quality WHERE `post_id`='".$row['id']."' AND `type`='episode'";
					$res_quality=mysqli_query($mysqli, $sql_quality);

					while ($row_quality=mysqli_fetch_assoc($res_quality)) {
						if($row_quality['post_upload_type']=='local'){
							unlink('uploads/'.$row_quality['quality_480']);
							unlink('uploads/'.$row_quality['quality_720']);	
							unlink('uploads/'.$row_quality['quality_1080']);
						}

						Delete('tbl_quality','id='.$row_quality['id']);
					}

					mysqli_free_result($res_quality);
				}

				$deleteSql="DELETE FROM $tbl_nm WHERE `id` IN ($ids)";

			}
            
            else if($tbl_nm=='tbl_tv_season'){
                $sql="SELECT * FROM tbl_tv_episode WHERE `season_id` IN ($ids)";

				$res=mysqli_query($mysqli, $sql);
				while ($row=mysqli_fetch_assoc($res)){
					if($row['episode_poster']!="")
					{
						unlink('images/episodes/'.$row['episode_poster']);
						unlink('images/episodes/thumbs/'.$row['episode_poster']);
					}

					if($row['episode_type']=="local")
					{
						unlink('uploads/'.$row['episode_url']);
					}


					$sql_subtitle="SELECT * FROM tbl_subtitles WHERE `post_id`='".$row['id']."' AND `type`='episode'";
					$res_subtitle=mysqli_query($mysqli, $sql_subtitle);

					while ($row_subtitle=mysqli_fetch_assoc($res_subtitle)) {
						if($row_subtitle['subtitle_type']=='local'){
							unlink('uploads/'.$row_subtitle['subtitle_url']);	
						}

						Delete('tbl_subtitles','id='.$row_subtitle['id']);
					}

					mysqli_free_result($res_subtitle);

					$sql_quality="SELECT * FROM tbl_quality WHERE `post_id`='".$row['id']."' AND `type`='episode'";
					$res_quality=mysqli_query($mysqli, $sql_quality);

					while ($row_quality=mysqli_fetch_assoc($res_quality)) {
						if($row_quality['post_upload_type']=='local'){
							unlink('uploads/'.$row_quality['quality_480']);
							unlink('uploads/'.$row_quality['quality_720']);	
							unlink('uploads/'.$row_quality['quality_1080']);
						}

						Delete('tbl_quality','id='.$row_quality['id']);
					}

					mysqli_free_result($res_quality);
				}

				$deleteSql="DELETE FROM $tbl_nm WHERE `id` IN ($ids)";
            }
			else if($tbl_nm=='tbl_episode'){
				$sql="SELECT * FROM $tbl_nm WHERE `id` IN ($ids)";
				$res=mysqli_query($mysqli, $sql);
				while ($row=mysqli_fetch_assoc($res)){
					if($row['episode_poster']!="")
					{
						unlink('images/episodes/'.$row['episode_poster']);
						unlink('images/episodes/thumbs/'.$row['episode_poster']);
					}

					if($row['episode_type']=="local")
					{
						unlink('uploads/'.$row['episode_url']);
					}


					$sql_subtitle="SELECT * FROM tbl_subtitles WHERE `post_id`='".$row['id']."' AND `type`='episode'";
					$res_subtitle=mysqli_query($mysqli, $sql_subtitle);

					while ($row_subtitle=mysqli_fetch_assoc($res_subtitle)) {
						if($row_subtitle['subtitle_type']=='local'){
							unlink('uploads/'.$row_subtitle['subtitle_url']);	
						}

						Delete('tbl_subtitles','id='.$row_subtitle['id']);
					}

					mysqli_free_result($res_subtitle);


					$sql_quality="SELECT * FROM tbl_quality WHERE `post_id`='".$row['id']."' AND `type`='episode'";
					$res_quality=mysqli_query($mysqli, $sql_quality);

					while ($row_quality=mysqli_fetch_assoc($res_quality)) {
						if($row_quality['post_upload_type']=='local'){
							unlink('uploads/'.$row_quality['quality_480']);
							unlink('uploads/'.$row_quality['quality_720']);	
							unlink('uploads/'.$row_quality['quality_1080']);
						}

						Delete('tbl_quality','id='.$row_quality['id']);
					}

					mysqli_free_result($res_quality);
				}
				$deleteSql="DELETE FROM $tbl_nm WHERE `id` IN ($ids)";

			}
			
			elseif($tbl_nm=='tbl_tv_episode'){
			    $sql="SELECT * FROM $tbl_nm WHERE `id` IN ($ids)";
				$res=mysqli_query($mysqli, $sql);
				while ($row=mysqli_fetch_assoc($res)){
					if($row['episode_poster']!="")
					{
						unlink('images/episodes/'.$row['episode_poster']);
						unlink('images/episodes/thumbs/'.$row['episode_poster']);
					}

					if($row['episode_type']=="local")
					{
						unlink('uploads/'.$row['episode_url']);
					}


					$sql_subtitle="SELECT * FROM tbl_subtitles WHERE `post_id`='".$row['id']."' AND `type`='episode'";
					$res_subtitle=mysqli_query($mysqli, $sql_subtitle);

					while ($row_subtitle=mysqli_fetch_assoc($res_subtitle)) {
						if($row_subtitle['subtitle_type']=='local'){
							unlink('uploads/'.$row_subtitle['subtitle_url']);	
						}

						Delete('tbl_subtitles','id='.$row_subtitle['id']);
					}

					mysqli_free_result($res_subtitle);


					$sql_quality="SELECT * FROM tbl_quality WHERE `post_id`='".$row['id']."' AND `type`='episode'";
					$res_quality=mysqli_query($mysqli, $sql_quality);

					while ($row_quality=mysqli_fetch_assoc($res_quality)) {
						if($row_quality['post_upload_type']=='local'){
							unlink('uploads/'.$row_quality['quality_480']);
							unlink('uploads/'.$row_quality['quality_720']);	
							unlink('uploads/'.$row_quality['quality_1080']);
						}

						Delete('tbl_quality','id='.$row_quality['id']);
					}

					mysqli_free_result($res_quality);
				}
				$deleteSql="DELETE FROM $tbl_nm WHERE `id` IN ($ids)";
			}

			else if($tbl_nm=='tbl_channels'){

				$sql="SELECT * FROM $tbl_nm WHERE `id` IN ($ids)";
				$res=mysqli_query($mysqli, $sql);
				while ($row=mysqli_fetch_assoc($res)){

					if($row['channel_thumbnail']!="" AND file_exists('images/'.$row['channel_thumbnail']))
				    {
				      	unlink('images/'.$row['channel_thumbnail']);
				  	  	unlink('images/thumbs/'.$row['channel_thumbnail']);
				  	}

				  	if($row['channel_poster']!="" AND file_exists('images/'.$row['channel_poster']))
				    {
				      	unlink('images/'.$row['channel_poster']);
				  	  	unlink('images/thumbs/'.$row['channel_poster']);
				  	}

				  	$deleteSql="DELETE FROM tbl_comments WHERE `post_id`='".$row['id']."' AND `type`='channel'";
					mysqli_query($mysqli, $deleteSql);

					$deleteSql="DELETE FROM tbl_reports WHERE `post_id`='".$row['id']."' AND `type`='channel'";
					mysqli_query($mysqli, $deleteSql);

					$deleteSql="DELETE FROM tbl_rating WHERE `post_id`='".$row['id']."' AND `type`='channel'";
					mysqli_query($mysqli, $deleteSql);

				}

				$deleteSql="DELETE FROM $tbl_nm WHERE `id` IN ($ids)";

			}

			else if($tbl_nm=='tbl_category'){

				$sql="SELECT * FROM tbl_channels WHERE `cat_id` IN ($ids)";
				$res=mysqli_query($mysqli, $sql);
				while ($row=mysqli_fetch_assoc($res)){

					if($row['channel_thumbnail']!="" AND file_exists('images/'.$row['channel_thumbnail']))
				    {
				      	unlink('images/'.$row['channel_thumbnail']);
				  	  	unlink('images/thumbs/'.$row['channel_thumbnail']);
				  	}

				  	if($row['channel_poster']!="" AND file_exists('images/'.$row['channel_poster']))
				    {
				      	unlink('images/'.$row['channel_poster']);
				  	  	unlink('images/thumbs/'.$row['channel_poster']);
				  	}

				  	$deleteSql="DELETE FROM tbl_comments WHERE `post_id`='".$row['id']."' AND `type`='channel'";
					mysqli_query($mysqli, $deleteSql);

					$deleteSql="DELETE FROM tbl_reports WHERE `post_id`='".$row['id']."' AND `type`='channel'";
					mysqli_query($mysqli, $deleteSql);

					$deleteSql="DELETE FROM tbl_rating WHERE `post_id`='".$row['id']."' AND `type`='channel'";
					mysqli_query($mysqli, $deleteSql);

				}

				$deleteSql="DELETE FROM tbl_channels WHERE `cat_id` IN ($ids)";
				mysqli_query($mysqli, $deleteSql);

				$sql="SELECT * FROM tbl_category WHERE `cid` IN ($ids)";
				$res=mysqli_query($mysqli, $sql);
				while ($row=mysqli_fetch_assoc($res)) {
					if($row['category_image']!="" AND file_exists('images/'.$row['category_image']))
				    {
				    	unlink('images/'.$row['category_image']);
						unlink('images/thumbs/'.$row['category_image']);
					}
				}

				$deleteSql="DELETE FROM $tbl_nm WHERE `cid` IN ($ids)";

			}

			else if($tbl_nm=='tbl_users'){

				$deleteSql="DELETE FROM tbl_comments WHERE `user_id` IN ($ids)";
				mysqli_query($mysqli, $deleteSql);

				$deleteSql="DELETE FROM tbl_reports WHERE `user_id` IN ($ids)";
				mysqli_query($mysqli, $deleteSql);

				$deleteSql="DELETE FROM tbl_active_log WHERE `user_id` IN ($ids)";
				mysqli_query($mysqli, $deleteSql);

				$deleteSql="DELETE FROM tbl_rating WHERE `user_id` IN ($ids)";
				mysqli_query($mysqli, $deleteSql);

				$deleteSql="DELETE FROM $tbl_nm WHERE `id` IN ($ids)";

			}
			else if($tbl_nm=='tbl_subscription'){

			

				$deleteSql="DELETE FROM $tbl_nm WHERE `id` IN ($ids)";

			}
			
			else if($tbl_nm=='tbl_shortfilms'){

			

				$deleteSql="DELETE FROM $tbl_nm WHERE `id` IN ($ids)";

			}
				else if($tbl_nm=='tbl_drama'){

			

				$deleteSql="DELETE FROM $tbl_nm WHERE `id` IN ($ids)";

			}else if($tbl_nm=='tbl_songs'){

			

				$deleteSql="DELETE FROM $tbl_nm WHERE `id` IN ($ids)";

			}
			else if($tbl_nm=='tbl_home'){

			

				$deleteSql="DELETE FROM $tbl_nm WHERE `id` IN ($ids)";

			}else if($tbl_nm=='tbl_shows'){
			    	$deleteSql="DELETE FROM $tbl_nm WHERE `id` IN ($ids)";
			}
			else if($tbl_nm=='tbl_events'){

			

				$deleteSql="DELETE FROM $tbl_nm WHERE `id` IN ($ids)";

			}
			else if($tbl_nm=='tbl_rentmovies'){

			

				$deleteSql="DELETE FROM $tbl_nm WHERE `id` IN ($ids)";

			}
			else if($tbl_nm=='tbl_banners'){

			

				$deleteSql="DELETE FROM $tbl_nm WHERE `id` IN ($ids)";

			}

			else if($tbl_nm=='tbl_language'){
				if($_POST['_check']=='true'){
					// delete all thing which related to this

					$sql="SELECT * FROM tbl_movies WHERE `language_id` IN ($ids)";
					$res=mysqli_query($mysqli, $sql);
					while ($row=mysqli_fetch_assoc($res)){
						if($row['movie_cover']!="")
						{
							unlink('images/movies/'.$row['movie_cover']);
							unlink('images/movies/thumbs/'.$row['movie_cover']);
						}

						if($row['movie_poster']!="")
						{
							unlink('images/movies/'.$row['movie_poster']);
							unlink('images/movies/thumbs/'.$row['movie_poster']);
						}

						if($row['movie_type']=="local")
						{
							unlink('uploads/'.$row['movie_url']);
						}

						$sql_subtitle="SELECT * FROM tbl_subtitles WHERE `post_id`='".$row['id']."' AND `type`='movie'";
						$res_subtitle=mysqli_query($mysqli, $sql_subtitle);

						while ($row_subtitle=mysqli_fetch_assoc($res_subtitle)) {
							if($row_subtitle['subtitle_type']=='local'){
								unlink('uploads/'.$row_subtitle['subtitle_url']);	
							}

							Delete('tbl_subtitles','id='.$row_subtitle['id']);
						}

						mysqli_free_result($res_subtitle);

						$sql_quality="SELECT * FROM tbl_quality WHERE `post_id`='".$row['id']."' AND `type`='movie'";
						$res_quality=mysqli_query($mysqli, $sql_quality);

						while ($row_quality=mysqli_fetch_assoc($res_quality)) {
							if($row_quality['post_upload_type']=='local'){
								unlink('uploads/'.$row_quality['quality_480']);
								unlink('uploads/'.$row_quality['quality_720']);	
								unlink('uploads/'.$row_quality['quality_1080']);
							}

							Delete('tbl_quality','id='.$row_quality['id']);
						}

						mysqli_free_result($res_quality);

						$deleteSql="DELETE FROM tbl_movies WHERE `language_id` IN ($ids)";
						mysqli_query($mysqli, $deleteSql);

						$deleteSql="DELETE FROM tbl_comments WHERE `post_id`='".$row['id']."' AND `type`='movie'";
						mysqli_query($mysqli, $deleteSql);

						$deleteSql="DELETE FROM tbl_reports WHERE `post_id`='".$row['id']."' AND `type`='movie'";
						mysqli_query($mysqli, $deleteSql);

						$deleteSql="DELETE FROM tbl_rating WHERE `post_id`='".$row['id']."' AND `type`='movie'";
						mysqli_query($mysqli, $deleteSql);

					}
					$deleteSql="DELETE FROM $tbl_nm WHERE `id` IN ($ids)";

				}
				else{
					// just remove only language
					$deleteSql="DELETE FROM $tbl_nm WHERE `id` IN ($ids)";
				}
			}

			else if($tbl_nm=='tbl_genres'){

				$sql="SELECT * FROM tbl_genres WHERE `gid` IN ($ids)";
				$res=mysqli_query($mysqli, $sql);
				while ($row=mysqli_fetch_assoc($res)) {
					if($row['genre_image']!="" AND file_exists('images/'.$row['genre_image']))
				    {
				    	unlink('images/'.$row['genre_image']);
						unlink('images/thumbs/'.$row['genre_image']);
					}
				}
		 
				$deleteSql="DELETE FROM $tbl_nm WHERE `gid` IN ($ids)";
			}

			mysqli_query($mysqli, $deleteSql);
			
	      	$response['status']=1;

	      	$_SESSION['msg']="12";
	      	
	      	echo json_encode($response);
			break;
		
		case 'remove_subtitle':
			$id=$_POST['id'];

			$type=$_POST['type'];
			$post_id=$_POST['post_id'];

			$sql="SELECT * FROM tbl_subtitles WHERE `id` = '$id'";
			$res=mysqli_query($mysqli, $sql);
			$row=mysqli_fetch_assoc($res);
			
			if($row['subtitle_type']=='local'){
				unlink('uploads/'.$row['subtitle_url']);
			}

			mysqli_free_result($res);

			Delete('tbl_subtitles','id='.$id);

			$sql="SELECT * FROM tbl_subtitles WHERE `post_id`='$post_id' AND `type`='$type'";
			$res=mysqli_query($mysqli, $sql);

			$response['subtitle_status']=(mysqli_num_rows($res) > 0) ? 'true' : 'false';
	      	$response['status']=1;
	      	$response['message']=$client_lang['remove_subtitle_success'];
	      	echo json_encode($response);
			break;

		case 'remove_quality':

			$id=$_POST['id'];
			$column=$_POST['column'];

			$sql="SELECT * FROM tbl_quality WHERE `id` = '$id'";
			$res=mysqli_query($mysqli, $sql);
			$row=mysqli_fetch_assoc($res);

			if(file_exists('uploads/'.$row[$column])){
				unlink('uploads/'.$row[$column]);
			}

			$data = array(
				$column  =>  ''
			);

			$update=Update('tbl_quality', $data, "WHERE id = '$id'");
			
			mysqli_free_result($res);

			$is_blank='false';

			if($row['quality_480']=='' && $row['quality_720']!='' && $row['quality_1080']!=''){
				$is_blank='true';
			}
			
	      	$response['status']=1;
	      	$response['message']=$client_lang['remove_quality_success'];
	      	echo json_encode($response);
			break;

		case 'multi_action':

			$action=$_POST['for_action'];
			$ids=implode(",", $_POST['id']);
			$table=$_POST['table'];

			if($action=='enable'){

			    $sql="UPDATE $table SET `status`='1' WHERE `id` IN ($ids)";
				mysqli_query($mysqli, $sql);
				$response['status']=1;	
				$_SESSION['msg']="13";
				
			}
			else if($action=='disable'){
				$sql="UPDATE $table SET `status`='0' WHERE `id` IN ($ids)";
				if(mysqli_query($mysqli, $sql)){
					$response['status']=1;	
					$_SESSION['msg']="14";
				}
			}
			else if($action=='delete'){
				
				if($table=='tbl_users'){

					$deleteSql="DELETE FROM tbl_comments WHERE `user_id` IN ($ids)";
					mysqli_query($mysqli, $deleteSql);

					$deleteSql="DELETE FROM tbl_reports WHERE `user_id` IN ($ids)";
					mysqli_query($mysqli, $deleteSql);

					$deleteSql="DELETE FROM tbl_active_log WHERE `user_id` IN ($ids)";
					mysqli_query($mysqli, $deleteSql);

					$deleteSql="DELETE FROM tbl_rating WHERE `user_id` IN ($ids)";
					mysqli_query($mysqli, $deleteSql);

					$deleteSql="DELETE FROM $table WHERE `id` IN ($ids)";

					mysqli_query($mysqli, $deleteSql);
				}
				else if($table=='tbl_movies'){
					$sql="SELECT * FROM $table WHERE `id` IN ($ids)";
					$res=mysqli_query($mysqli, $sql);
					while ($row=mysqli_fetch_assoc($res)){
						if($row['movie_cover']!="")
						{
							unlink('images/movies/'.$row['movie_cover']);
							unlink('images/movies/thumbs/'.$row['movie_cover']);
						}

						if($row['movie_poster']!="")
						{
							unlink('images/movies/'.$row['movie_poster']);
							unlink('images/movies/thumbs/'.$row['movie_poster']);
						}

						if($row['movie_type']=="local")
						{
							unlink('uploads/'.$row['movie_url']);
						}

						$sql_subtitle="SELECT * FROM tbl_subtitles WHERE `post_id`='".$row['id']."' AND `type`='movie'";
						$res_subtitle=mysqli_query($mysqli, $sql_subtitle);

						while ($row_subtitle=mysqli_fetch_assoc($res_subtitle)) {
							if($row_subtitle['subtitle_type']=='local'){
								unlink('uploads/'.$row_subtitle['subtitle_url']);	
							}

							Delete('tbl_subtitles','id='.$row_subtitle['id']);
						}

						mysqli_free_result($res_subtitle);

						$sql_quality="SELECT * FROM tbl_quality WHERE `post_id`='".$row['id']."' AND `type`='movie'";
						$res_quality=mysqli_query($mysqli, $sql_quality);

						while ($row_quality=mysqli_fetch_assoc($res_quality)) {
							if($row_quality['post_upload_type']=='local'){
								unlink('uploads/'.$row_quality['quality_480']);
								unlink('uploads/'.$row_quality['quality_720']);	
								unlink('uploads/'.$row_quality['quality_1080']);
							}

							Delete('tbl_quality','id='.$row_quality['id']);
						}

						mysqli_free_result($res_quality);

					}
					$deleteSql="DELETE FROM $table WHERE `id` IN ($ids)";
					mysqli_query($mysqli, $deleteSql);
				}
				else if($table=='tbl_episode')
				{
					$sql="SELECT * FROM $table WHERE `id` IN ($ids)";
					$res=mysqli_query($mysqli, $sql);
					while ($row=mysqli_fetch_assoc($res)){
						if($row['episode_poster']!="")
						{
							unlink('images/episodes/'.$row['episode_poster']);
							unlink('images/episodes/thumbs/'.$row['episode_poster']);
						}

						if($row['episode_type']=="local")
						{
							unlink('uploads/'.$row['episode_url']);
						}


						$sql_subtitle="SELECT * FROM tbl_subtitles WHERE `post_id`='".$row['id']."' AND `type`='episode'";
						$res_subtitle=mysqli_query($mysqli, $sql_subtitle);

						while ($row_subtitle=mysqli_fetch_assoc($res_subtitle)) {
							if($row_subtitle['subtitle_type']=='local'){
								unlink('uploads/'.$row_subtitle['subtitle_url']);	
							}

							Delete('tbl_subtitles','id='.$row_subtitle['id']);
						}

						mysqli_free_result($res_subtitle);


						$sql_quality="SELECT * FROM tbl_quality WHERE `post_id`='".$row['id']."' AND `type`='episode'";
						$res_quality=mysqli_query($mysqli, $sql_quality);

						while ($row_quality=mysqli_fetch_assoc($res_quality)) {
							if($row_quality['post_upload_type']=='local'){
								unlink('uploads/'.$row_quality['quality_480']);
								unlink('uploads/'.$row_quality['quality_720']);	
								unlink('uploads/'.$row_quality['quality_1080']);
							}

							Delete('tbl_quality','id='.$row_quality['id']);
						}

						mysqli_free_result($res_quality);
					}

					$deleteSql="DELETE FROM $table WHERE `id` IN ($ids)";
					mysqli_query($mysqli, $deleteSql);

				}
				else if($table=='tbl_channels'){

					$sql="SELECT * FROM $table WHERE `id` IN ($ids)";
					$res=mysqli_query($mysqli, $sql);
					while ($row=mysqli_fetch_assoc($res)){

						if($row['channel_thumbnail']!="" AND file_exists('images/'.$row['channel_thumbnail']))
					    {
					      	unlink('images/'.$row['channel_thumbnail']);
					  	  	unlink('images/thumbs/'.$row['channel_thumbnail']);
					  	}

					  	if($row['channel_poster']!="" AND file_exists('images/'.$row['channel_poster']))
					    {
					      	unlink('images/'.$row['channel_poster']);
					  	  	unlink('images/thumbs/'.$row['channel_poster']);
					  	}

					  	$deleteSql="DELETE FROM tbl_comments WHERE `post_id`='".$row['id']."' AND `type`='channel'";
						mysqli_query($mysqli, $deleteSql);

						$deleteSql="DELETE FROM tbl_reports WHERE `post_id`='".$row['id']."' AND `type`='channel'";
						mysqli_query($mysqli, $deleteSql);

						$deleteSql="DELETE FROM tbl_rating WHERE `post_id`='".$row['id']."' AND `type`='channel'";
						mysqli_query($mysqli, $deleteSql);

					}

					$deleteSql="DELETE FROM $table WHERE `id` IN ($ids)";
					mysqli_query($mysqli, $deleteSql);

				}

				$_SESSION['msg']="12";
			}

			$response['status']=1;	

	      	echo json_encode($response);
			break;

		default:
			# code...
			break;
	}

?>