<?php 
  $page_title= 'Edit Tv Season';
  $current_page="tv season";
  

  include("includes/header.php");
  require("includes/function.php");
  require("language/language.php");

  require_once("thumbnail_images.class.php");

  $sql="SELECT * FROM tbl_shows ORDER BY shows_name";
  $result_series=mysqli_query($mysqli,$sql);


  $id=$_GET['id'];
  $sql="SELECT * FROM tbl_tv_season WHERE id='$id'";
  $res=mysqli_query($mysqli,$sql);
  $row=mysqli_fetch_assoc($res);
  

  if(isset($_POST['submit']))
  {
  
    $shows_id=addslashes(trim($_POST['shows_id']));
    $season_name=addslashes(trim($_POST['season_name']));

    $data = array(
          'shows_id'  =>  $shows_id,
          'season_name'  =>  $season_name
      );  

    $update=Update('tbl_tv_season', $data, "WHERE id = '".$id."'"); 
     
    $_SESSION['msg']="11";

    if(isset($_GET['redirect']))
      header( "Location:".$_GET['redirect']);
    else  
      header( "Location:edit_tv_season.php?id=".$id);
    exit;

  }

?>

<div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="page_title_block">
            <div class="col-md-5 col-xs-12">
              <div class="page_title"><?=$page_title?></div>
            </div>
            <div class="col-md-7 col-xs-12" style="text-align:right;">
           <a href="manage_season.php" style="background:white;margin-top:10px;font-size:25px;">X</a>
        </div>
          </div>
          <div class="clearfix"></div>
          <div class="card-body mrg_bottom"> 
            <form action="" name="addeditlanguage" method="post" class="form form-horizontal" enctype="multipart/form-data">

              <div class="section">
                <div class="section-body">
                  
                  <div class="form-group">
                    <label class="col-md-3 control-label">Shows :-</label>
                    <div class="col-md-6">
                      <select name="shows_id" id="shows_id" class="select2" required>
                        <option value="">--Select Shows--</option>
                        <?php
                            while($data=mysqli_fetch_array($result_series))
                            {
                        ?>                       
                        <option value="<?php echo $data['id'];?>" <?php if($row['shows_id']==$data['id']){ echo 'selected'; } ?>><?php echo $data['shows_name'];?></option>                          
                        <?php
                          }
                        ?>
                      </select>
                    </div>
                  </div> 
                  <div class="input-container">
                    <div class="form-group">
                      <label class="col-md-3 control-label">Season name :-</label>
                      <div class="col-md-6">
                        <input type="text" name="season_name" class="form-control" value="<?=$row['season_name']?>" required>
                      </div>
                    </div>
                  </div>

                  <br>
                  <div class="form-group">
                    <div class="col-md-9 col-md-offset-3">
                      <button type="submit" name="submit" class="btn btn-primary" style="border-radius:10px;">Update</button>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
        
<?php include("includes/footer.php");?>       
