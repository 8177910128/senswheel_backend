<?php 
  
  $page_title=(isset($_GET['id'])) ? 'Edit Subscription Plan' : 'Add Subscription Plan'; 

  include('includes/header.php');
  include('includes/function.php');
  include('language/language.php'); 

  //require_once("thumbnail_images.class.php");
   
  if(isset($_POST['submit']) and isset($_GET['add']))
  {   
        
    $data = array(
      'plan'  =>  cleanInput($_POST['plan']),
      'description'  =>  cleanInput($_POST['description']),
      'validity'  =>  cleanInput($_POST['validity']),
       'price'  =>  cleanInput($_POST['price']),
       'discount_price'  =>  cleanInput($_POST['discount_price']),
      'color'  =>  cleanInput($_POST['color'])
    );

    $qry = Insert('tbl_subscription',$data);

    $_SESSION['msg']="10";
    header("location:manage_plan.php");   
    exit;
    
  }
  
  if(isset($_GET['id']))
  {
    $user_qry="SELECT * FROM tbl_subscription where id='".$_GET['id']."'";
    $user_result=mysqli_query($mysqli,$user_qry);
    $user_row=mysqli_fetch_assoc($user_result);
  }
  
  if(isset($_POST['submit']) and isset($_GET['id']))
  {
      
    
      $data = array(
          'plan_type'  =>  cleanInput($_POST['plan_type']),
         'plan'  =>  cleanInput($_POST['plan']),
      'description'  =>  cleanInput($_POST['description']),
      'validity'  =>  cleanInput($_POST['validity']),
       'price'  =>  cleanInput($_POST['price']),
       'discount_price'  =>  cleanInput($_POST['discount_price'])
      
      );
    
 
    
    $user_edit=Update('tbl_subscription', $data, "WHERE id = '".$_GET['id']."'");
    
    $_SESSION['msg']="11";

    if(isset($_GET['redirect'])){
      header("Location:manage_plan.php");
    }
    else{
      header("Location:manage_plan.php?id=".$_POST['id']);
    }
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
               <a href="manage_plan.php" style="background:white;margin-top:10px;font-size:25px;">X</a>
            </div>
      </div>
      <div class="clearfix"></div>
      <div class="card-body mrg_bottom"> 
        <form action="" method="post" class="form form-horizontal" enctype="multipart/form-data" >
          <input  type="hidden" name="user_id" value="<?php echo $_GET['user_id'];?>" />

          <div class="section">
            <div class="section-body">
             <div class="form-group">
                <label class="col-md-3 control-label">Plan Type :-</label>
                <div class="col-md-6">
                  <select name="plan_type" id="plan_type" class="select2" required>
                    <option value="">-- Select Type --</option>
                    <option value="free" <?=(isset($_GET['id']) && $user_row['plan_type']=='free') ? 'selected="selected"' : ''?>>Free</option>
                    <option value="ad-lite"<?=(isset($_GET['id']) && $user_row['plan_type']=='ad-lite') ? 'selected="selected"' : ''?>>Ad-Lite</option>
                    <option value="ad-free"<?=(isset($_GET['id']) && $user_row['plan_type']=='ad-free') ? 'selected="selected"' : ''?>>Ad-Free</option>
                    
                     
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label">Subscription Title :-</label>
                <div class="col-md-6">
                  <input type="text" name="plan"  id="plan" value="<?php if(isset($_GET['id'])){echo $user_row['plan'];}?>" class="form-control" placeholder="Enter Subscription Title" required>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label">Plan Description :-</label>
                <div class="col-md-6">
                 <!-- <input type="text" name="description" id="description" value=""  required>-->
                 <textarea name="description" id="description" class="form-control"  rows="10"  placeholder="Enter Description"><?php if(isset($_GET['id'])){echo $user_row['description'];}?></textarea>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label">Plan Validity :-</label>
                <div class="col-md-6">
                  <input type="number" name="validity" id="validity" value="<?php if(isset($_GET['id'])){echo $user_row['validity'];}?>" class="form-control"  placeholder="Enter Validity" required>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label">Plan Original Price :-</label>
                <div class="col-md-6">
                  <input type="number" name="price" id="price" value="<?php if(isset($_GET['id'])){echo $user_row['price'];}?>" class="form-control" placeholder="Enter Price"  required>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label">Plan Discount Price :-</label>
                <div class="col-md-6">
                  <input type="number" name="discount_price" id="'discount_price'  =>  cleanInput($_POST['discount_price'])," value="<?php if(isset($_GET['id'])){echo $user_row['discount_price'];}?>" class="form-control" placeholder="Enter discount price"  required>
                </div>
              </div>
              
              <!--<div class="form-group">
                <label class="col-md-3 control-label">Select Color :-</label>
                <div class="col-md-6">
                  <input type="color" id="color" name="color" value="<?php if(isset($_GET['id'])){echo $user_row['color'];}?>" required style=" margin: 0.4rem;">
                </div>
              </div>-->
             
              <div class="form-group">
                <div class="col-md-9 col-md-offset-3">
                  <button type="submit" name="submit" class="btn btn-primary" style="border-radius:10px;"><?php if(isset($_GET['id'])){ ?> Update  <?php }else { ?> Save <?php }?>
                                                                             
                  
                  
                  </button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
   

<?php include('includes/footer.php');?>