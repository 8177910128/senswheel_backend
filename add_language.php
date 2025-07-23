<?php 
  if(isset($_GET['id'])){ 
    $page_title= 'Update Language';
  }
  else{ 
    $page_title='Add Language'; 
  }
  $current_page="language";
  $active_page="movies";

  include("includes/header.php");
  require("includes/function.php");
  require("language/language.php");

  require_once("thumbnail_images.class.php");

  if(isset($_GET['id'])){
    $id=$_GET['id'];
    $sql="SELECT * FROM tbl_language WHERE id='$id'";
    $res=mysqli_query($mysqli,$sql);
    $row=mysqli_fetch_assoc($res);
  }

  if(isset($_POST['submit']) and isset($_GET['add']))
{
  $name = addslashes(trim($_POST['language_name']));
  
  // Handle image upload
  if(!empty($_FILES['bg_image']['name'])) {
    $image_name = time().'_'.$_FILES['bg_image']['name'];
    $image_tmp = $_FILES['bg_image']['tmp_name'];
    $path = "images/languages/".$image_name;

    move_uploaded_file($image_tmp, $path);
  } else {
    $image_name = ''; // No image provided
  }

  $data = array(
    'language_name' => $name,
    'language_background' => $path
  );  

  $qry = Insert('tbl_language', $data);  
  $_SESSION['msg'] = "10";
  header("Location: manage_language.php");
  exit;
}


 if(isset($_POST['submit']) and isset($_POST['id']))
{
  $name = addslashes(trim($_POST['language_name']));
  
  // Handle image upload
  if(!empty($_FILES['bg_image']['name'])) {
    $image_name = time().'_'.$_FILES['bg_image']['name'];
    $image_tmp = $_FILES['bg_image']['tmp_name'];
    $path = "images/languages/".$image_name;

    move_uploaded_file($image_tmp, $path);

    // Optionally, delete the old image file
    if(!empty($row['language_background'])) {
      unlink("images/languages/".$row['language_background']);
    }
  } else {
    // Use the existing image if no new one is uploaded
    $image_name = $row['language_background'];
  }

if($_FILES['bg_image']['name']==''){
   $data = array(
    'language_name' => $name
    
  );  
}else{
    $data = array(
    'language_name' => $name,
    'language_background' => $path
  ); 
}
   

  $update = Update('tbl_language', $data, "WHERE id = '".$_POST['id']."'");

  $_SESSION['msg'] = "11"; 
  
  if(isset($_GET['redirect']))
    header("Location:".$_GET['redirect']);
  else  
    header("Location: add_language.php?id=".$_POST['id']);
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
           <a href="manage_language.php" style="background:white;margin-top:10px;font-size:25px;">X</a>
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="card-body mrg_bottom"> 
        <form action="" name="addeditlanguage" method="post" class="form form-horizontal" enctype="multipart/form-data">
          <input  type="hidden" name="id" value="<?php echo $_GET['id'];?>" />

          <div class="section">
            <div class="section-body">
              <div class="form-group">
                <label class="col-md-3 control-label">Language Title :-
                
                </label>
                <div class="col-md-6">
                  <input type="text" name="language_name" placeholder="Enter language title" id="language_name" value="<?php if(isset($_GET['id'])){echo $row['language_name'];}?>" class="form-control" required>
                </div>
              </div>
              <div class="form-group">
                  <label class="col-md-3 control-label">Select Background Image :-</label>
                  <div class="col-md-6">
                    <input type="file" name="bg_image" class="form-control" accept="image/*" style="height:189px;  background: url('assets/images/browse.svg') no-repeat center center;">
                    <?php if(isset($_GET['id']) && !empty($row['language_background'])): ?>
                      <img src="<?php echo $row['language_background']; ?>" alt="Current Background" style="width:100px; height:100px;">
                    <?php endif; ?>
                  </div>
            </div>


              <br>
              <div class="form-group">
                <div class="col-md-9 col-md-offset-3">
                  <button type="submit" name="submit" class="btn btn-primary" style="background:#612BAD;border-radius:8px;"><?php  if(isset($_GET['id'])){ ?>Update <?php }else { ?> Save <?php }?></button>
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

<script type="text/javascript" src="assets/js/jscolor.js"></script>