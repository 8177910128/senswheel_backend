<?php 
      
    $page_title="Settings";

    include("includes/header.php");
    require("includes/function.php");
    require("language/language.php");

    $qry="SELECT * FROM tbl_settings where id='1'";
    $result=mysqli_query($mysqli,$qry);
    $settings_row=mysqli_fetch_assoc($result);

    if(isset($_POST['submit']))
    {
        if($_FILES['app_logo']['name']!="")
        {
            unlink('images/'.$settings_row['app_logo']);   

            $app_logo=$_FILES['app_logo']['name'];
            $pic1=$_FILES['app_logo']['tmp_name'];

            $tpath1='images/'.$app_logo;      
            copy($pic1,$tpath1);

            $data = array(      
            'email_from'  =>  '-',   
            'app_name'  =>  cleanInput($_POST['app_name']),
            'app_logo'  =>  $tpath1,  
            'app_description'  => addslashes($_POST['app_description']),
            'upi_id'=> cleanInput($_POST['upi_id']),
            'app_version'  =>  cleanInput($_POST['app_version']),
            'app_author'  =>  cleanInput($_POST['app_author']),
            'app_contact'  =>  cleanInput($_POST['app_contact']),
            'app_email'  =>  cleanInput($_POST['app_email']),   
            'app_website'  =>  cleanInput($_POST['app_website']),
            'app_developed_by'  =>  cleanInput($_POST['app_developed_by'])                    

            );

        }
        else
        {

          $data = array(      
            'email_from'  =>  '-',   
            'app_name'  =>  cleanInput($_POST['app_name']),
            'app_description'  => addslashes($_POST['app_description']),
            'upi_id'=> cleanInput($_POST['upi_id']),
            'app_version'  =>  cleanInput($_POST['app_version']),
            'app_author'  =>  cleanInput($_POST['app_author']),
            'app_contact'  =>  cleanInput($_POST['app_contact']),
            'app_email'  =>  cleanInput($_POST['app_email']),   
            'app_website'  =>  cleanInput($_POST['app_website']),
            'app_developed_by'  =>  cleanInput($_POST['app_developed_by'])
          );
        } 

        $settings_edit=Update('tbl_settings', $data, "WHERE id = '1'");

        $_SESSION['msg']="11";
        header( "Location:settings.php");
        exit;

    }
    else if(isset($_POST['admob_submit']))
    {

        $data = array(
            'publisher_id'  =>  cleanInput($_POST['publisher_id']),
            'interstital_ad'  =>  ($_POST['interstital_ad']) ? 'true' : 'false',
            'interstital_ad_type'  =>  cleanInput($_POST['interstital_ad_type']),
            'interstital_ad_id'  =>  cleanInput($_POST['interstital_ad_id']),
            'interstital_facebook_id'  =>  cleanInput($_POST['interstital_facebook_id']),
            'interstital_ad_click'  =>  cleanInput($_POST['interstital_ad_click']),
            'banner_ad'  =>  ($_POST['banner_ad']) ? 'true' : 'false',
            'banner_ad_type'  =>  cleanInput($_POST['banner_ad_type']),
            'banner_ad_id'  =>  cleanInput($_POST['banner_ad_id']),
            'banner_facebook_id'  =>  cleanInput($_POST['banner_facebook_id']),
            'publisher_id_ios'  =>  cleanInput($_POST['publisher_id_ios']),
            'app_id_ios'  =>  '-',

            'interstital_ad_ios'  =>  ($_POST['interstital_ad_ios']) ? 'true' : 'false',
            'ios_interstital_ad_type'  =>  cleanInput($_POST['ios_interstital_ad_type']),
            'interstital_ad_id_ios'  =>  cleanInput($_POST['interstital_ad_id_ios']),
            'ios_interstital_facebook_id'  =>  cleanInput($_POST['ios_interstital_facebook_id']),
            'interstital_ad_click_ios'  =>  cleanInput($_POST['interstital_ad_click_ios']),
            'banner_ad_ios'  =>  ($_POST['banner_ad_ios']) ? 'true' : 'false',
            'ios_banner_ad_type'  =>  cleanInput($_POST['ios_banner_ad_type']),
            'banner_ad_id_ios'  =>  cleanInput($_POST['banner_ad_id_ios']),
            'ios_banner_facebook_id'  =>  cleanInput($_POST['ios_banner_facebook_id'])
        );


        $settings_edit=Update('tbl_settings', $data, "WHERE id = '1'");


        $_SESSION['msg']="11";
        header( "Location:settings.php");
        exit;

    }
    else if(isset($_POST['user_agent_submit']))
    {

        $data = array(
            'user_agent' => cleanInput($_POST['user_agent'])
        );

        $settings_edit=Update('tbl_settings', $data, "WHERE id = '1'");

        $_SESSION['msg']="11";
        header( "Location:settings.php");
        exit;

    }
    else if(isset($_POST['omdb_api_submit']))
    {

        $data = array(
            'omdb_api_key' => cleanInput($_POST['omdb_api_key'])
        );

        $settings_edit=Update('tbl_settings', $data, "WHERE id = '1'");

        $_SESSION['msg']="11";
        header( "Location:settings.php");
        exit;

    }
    else if(isset($_POST['api_submit']))
    {

        $data = array(
            'api_latest_limit'  =>  cleanInput($_POST['api_latest_limit']),
            'api_page_limit'  =>  cleanInput($_POST['api_page_limit']),
            'api_cat_order_by'  =>  cleanInput($_POST['api_cat_order_by']),
            'api_cat_post_order_by'  =>  cleanInput($_POST['api_cat_post_order_by']),
            'api_lan_order_by'  =>  cleanInput($_POST['api_lan_order_by']),
            'api_gen_order_by'  =>  cleanInput($_POST['api_gen_order_by'])
        );

        $settings_edit=Update('tbl_settings', $data, "WHERE id = '1'");

        $_SESSION['msg']="11";
        header( "Location:settings.php");
        exit;
    }
    else if(isset($_POST['app_pri_poly']))
    {

        $data = array(
          'app_privacy_policy'  =>  trim($_POST['app_privacy_policy'])
        );

        $settings_edit=Update('tbl_settings', $data, "WHERE id = '1'");

        $_SESSION['msg']="11";
        header( "Location:settings.php");
        exit;
    }
?>

<style type="text/css">
  .field_lable {
    margin-bottom: 10px;
    margin-top: 10px;
    color: #666;
    padding-left: 15px;
    font-size: 14px;
    line-height: 24px;
  }
</style>
 

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="page_title_block">
        <div class="col-md-5 col-xs-12">
          <div class="page_title"><?=$page_title?></div>
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="card-body mrg_bottom" style="padding: 0px">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#app_settings" aria-controls="app_settings" role="tab" data-toggle="tab">App Settings</a></li>
            <li role="presentation"><a href="#admob_settings" aria-controls="admob_settings" role="tab" data-toggle="tab">Ads Settings</a></li>
            
            <li role="presentation"><a href="#api_privacy_policy" aria-controls="api_privacy_policy" role="tab" data-toggle="tab">App Privacy Policy</a></li>
        </ul>
        
        <div class="rows">
          <div class="col-md-12">
             <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="app_settings">   
                  <form action="" name="settings_from" method="post" class="form form-horizontal" enctype="multipart/form-data">
                    <div class="section">
                      <div class="section-body">
                        <div class="form-group">
                          <label class="col-md-3 control-label">App Name :-</label>
                          <div class="col-md-6">
                            <input type="text" name="app_name" id="app_name" value="<?php echo $settings_row['app_name'];?>" class="form-control">
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="col-md-3 control-label">App Logo :-</label>
                          <div class="col-md-6">
                            <div class="fileupload_block">
                              <input type="file" name="app_logo" id="fileupload">

                              <?php if($settings_row['app_logo']!="") {?>
                                <div class="fileupload_img"><img type="image" src="<?php echo $settings_row['app_logo'];?>" alt="image" style="width: 100px;height: 100px;" /></div>
                              <?php } else {?>
                                <div class="fileupload_img"><img type="image" src="assets/images/add-image.png" alt="image" /></div>
                              <?php }?>

                            </div>
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="col-md-3 control-label">App Description :-</label>
                          <div class="col-md-6">

                            <textarea name="app_description" id="app_description" class="form-control"><?php echo $settings_row['app_description'];?></textarea>

                            <script>CKEDITOR.replace( 'app_description' );</script>
                          </div>
                        </div>
                        <div class="form-group">&nbsp;</div>                 
                        <div class="form-group">
                          <label class="col-md-3 control-label">App Version :-</label>
                          <div class="col-md-6">
                            <input type="text" name="app_version" id="app_version" value="<?php echo $settings_row['app_version'];?>" class="form-control">
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="col-md-3 control-label">Author :-</label>
                          <div class="col-md-6">
                            <input type="text" name="app_author" id="app_author" value="<?php echo $settings_row['app_author'];?>" class="form-control">
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="col-md-3 control-label">Contact :-</label>
                          <div class="col-md-6">
                            <input type="text" name="app_contact" id="app_contact" value="<?php echo $settings_row['app_contact'];?>" class="form-control">
                          </div>
                        </div>    
                        <div class="form-group">
                          <label class="col-md-3 control-label">UPI Id :-</label>
                          <div class="col-md-6">
                            <input type="text" name="upi_id" id="upi_id" value="<?php echo $settings_row['upi_id'];?>" class="form-control">
                          </div>
                        </div>    
                        <div class="form-group">
                          <label class="col-md-3 control-label">Email :-</label>
                          <div class="col-md-6">
                            <input type="text" name="app_email" id="app_email" value="<?php echo $settings_row['app_email'];?>" class="form-control">
                          </div>
                        </div>                 
                        <div class="form-group">
                          <label class="col-md-3 control-label">Website :-</label>
                          <div class="col-md-6">
                            <input type="text" name="app_website" id="app_website" value="<?php echo $settings_row['app_website'];?>" class="form-control">
                          </div>
                        </div> 
                        <div class="form-group">
                          <label class="col-md-3 control-label">Developed By :-</label>
                          <div class="col-md-6">
                            <input type="text" name="app_developed_by" id="app_developed_by" value="<?php echo $settings_row['app_developed_by'];?>" class="form-control">
                          </div>
                        </div> 
                        <div class="form-group">
                          <div class="col-md-9 col-md-offset-3">
                            <button type="submit" name="submit" class="btn btn-primary" style="border-radius:10px;">Save</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>

                <!-- admob settings -->
                <div role="tabpanel" class="tab-pane" id="admob_settings">   
                  <form action="" name="admob_settings" method="post" class="form form-horizontal" enctype="multipart/form-data">
                    <div class="section">
                       <div class="section-body">            
                          <div class="row">
                            <div class="form-group">
                              <div class="col-md-6">                
                                <div class="col-md-12">
                                  <div class="admob_title">Android</div>
                                  <div class="form-group">
                                    <label class="col-md-3 control-label">Publisher ID :-</label>
                                    <div class="col-md-9">
                                      <input type="text" name="publisher_id" id="publisher_id" value="<?php echo $settings_row['publisher_id'];?>" class="form-control">
                                    </div>
                                    <div style="height:60px;display:inline-block;position:relative"></div>
                                  </div>
                                  <div class="banner_ads_block">
                                    <div class="banner_ad_item">
                                      <label class="control-label">Banner Ads :-</label>
                                      <div class="row toggle_btn">
                                        <input type="checkbox" id="chk_banner" name="banner_ad" value="true" class="cbx hidden" <?=($settings_row['banner_ad']=='true') ? 'checked=""' : '' ?>>
                                        <label for="chk_banner" class="lbl"></label>
                                      </div>                               
                                    </div>
                                    <div class="col-md-12">
                                      <div class="form-group">
                                        <p class="field_lable">Banner Ad Type :-</p>
                                        <div class="col-md-12">
                                         <select name="banner_ad_type" id="banner_ad_type" class="select2">
                                            <option value="admob" <?php if($settings_row['banner_ad_type']=='admob'){ echo 'selected="selected"'; }?>>Admob</option>
                                            <option value="facebook" <?php if($settings_row['banner_ad_type']=='facebook'){ echo 'selected="selected"'; }?>>Facebook</option>
                                          </select>
                                        </div>
                                      </div>
                                      <div class="form-group">
                                        <p class="field_lable">Banner ID :-</p>

                                        <div class="col-md-12 banner_ad_id" style="display: none">
                                          <input type="text" name="banner_ad_id" id="banner_ad_id" value="<?php echo $settings_row['banner_ad_id'];?>" class="form-control">
                                        </div>
                                        <div class="col-md-12 banner_facebook_id" style="display: none">
                                          <input type="text" name="banner_facebook_id" id="banner_facebook_id" value="<?php echo $settings_row['banner_facebook_id'];?>" class="form-control">
                                        </div>

                                      </div>                    
                                    </div>
                                  </div>  
                                </div>

                                <div class="col-md-12">
                                  <div class="interstital_ads_block">
                                    <div class="interstital_ad_item">
                                      <label class="control-label">Interstitial Ads :-</label>
                                      <div class="row toggle_btn">
                                        <input type="checkbox" id="chk_interstitial" name="interstital_ad" value="true" class="cbx hidden" <?php if($settings_row['interstital_ad']=='true'){?>checked <?php }?>/>
                                        <label for="chk_interstitial" class="lbl"></label>
                                      </div>                  
                                    </div>  
                                    <div class="col-md-12"> 
                                      <div class="form-group">
                                        <p class="field_lable">Interstitial Ad Type :-</p>
                                        <div class="col-md-12"> 
                                          <select name="interstital_ad_type" id="interstital_ad_type" class="select2">
                                            <option value="admob" <?php if($settings_row['interstital_ad_type']=='admob'){ echo 'selected="selected"'; }?>>Admob</option>
                                            <option value="facebook" <?php if($settings_row['interstital_ad_type']=='facebook'){ echo 'selected="selected"'; }?>>Facebook</option>
                            
                                          </select>                                 
                                        </div>
                                      </div>
                                      <div class="form-group">
                                        <p class="field_lable">Interstitial Ad ID :-</p>
                                        <div class="col-md-12 interstital_ad_id" style="display: none">
                                          <input type="text" name="interstital_ad_id" id="interstital_ad_id" value="<?php echo $settings_row['interstital_ad_id'];?>" class="form-control">
                                        </div>

                                        <div class="col-md-12 interstital_facebook_id" style="display: none">
                                          <input type="text" name="interstital_facebook_id" id="interstital_facebook_id" value="<?php echo $settings_row['interstital_facebook_id'];?>" class="form-control">
                                        </div>
                                      </div>
                                      <div class="form-group">
                                        <p class="field_lable">Interstitial Clicks :-</p>
                                        <div class="col-md-12">
                                          <input type="text" name="interstital_ad_click" id="interstital_ad_click" value="<?php echo $settings_row['interstital_ad_click'];?>" class="form-control">
                                        </div>
                                      </div>                    
                                    </div>                  
                                  </div>  
                                </div>
                              </div>


                              <div class="col-md-6">                
                                <div class="col-md-12">
                                  <div class="admob_title">iOS</div>
                                  <div class="form-group">
                                    <label class="col-md-3 control-label">Publisher ID :-</label>
                                    <div class="col-md-9">
                                      <input type="text" name="publisher_id_ios" id="publisher_id_ios" value="<?php echo $settings_row['publisher_id_ios'];?>" class="form-control">
                                    </div>
                                    <div style="height:60px;display:inline-block;position:relative"></div>
                                  </div>
                                  <div class="banner_ads_block">
                                    <div class="banner_ad_item">
                                      <label class="control-label">Banner Ads :-</label>
                                      <div class="row toggle_btn">
                                        <input type="checkbox" id="chk_banner_ios" name="banner_ad_ios" value="true" class="cbx hidden" <?=($settings_row['banner_ad_ios']=='true') ? 'checked=""' : '' ?>>
                                        <label for="chk_banner_ios" class="lbl"></label>
                                      </div>                               
                                    </div>
                                    <div class="col-md-12">
                                      <div class="form-group">
                                        <p class="field_lable">Banner Ad Type :-</p>
                                        <div class="col-md-12">
                                         <select name="ios_banner_ad_type" id="ios_banner_ad_type" class="select2">
                                            <option value="admob" <?php if($settings_row['ios_banner_ad_type']=='admob'){ echo 'selected="selected"'; }?>>Admob</option>
                                            <option value="facebook" <?php if($settings_row['ios_banner_ad_type']=='facebook'){ echo 'selected="selected"'; }?>>Facebook</option>
                                          </select>
                                        </div>
                                      </div>
                                      <div class="form-group">
                                        <p class="field_lable">Banner ID :-</p>

                                        <div class="col-md-12 banner_ad_id_ios" style="display: none">
                                          <input type="text" name="banner_ad_id_ios" id="banner_ad_id_ios" value="<?php echo $settings_row['banner_ad_id_ios'];?>" class="form-control">
                                        </div>
                                        <div class="col-md-12 ios_banner_facebook_id" style="display: none">
                                          <input type="text" name="ios_banner_facebook_id" id="ios_banner_facebook_id" value="<?php echo $settings_row['ios_banner_facebook_id'];?>" class="form-control">
                                        </div>

                                      </div>                    
                                    </div>
                                  </div>  
                                </div>

                                <div class="col-md-12">
                                  <div class="interstital_ads_block">
                                    <div class="interstital_ad_item">
                                      <label class="control-label">Interstitial Ads :-</label>
                                      <div class="row toggle_btn">
                                        <input type="checkbox" id="chk_interstitial_ios" name="interstital_ad_ios" value="true" class="cbx hidden" <?php if($settings_row['interstital_ad_ios']=='true'){?>checked <?php }?>/>
                                        <label for="chk_interstitial_ios" class="lbl"></label>
                                      </div>                  
                                    </div>  
                                    <div class="col-md-12"> 
                                      <div class="form-group">
                                        <p class="field_lable">Interstitial Ad Type :-</p>
                                        <div class="col-md-12"> 
                                          <select name="ios_interstital_ad_type" id="ios_interstital_ad_type" class="select2">
                                            <option value="admob" <?php if($settings_row['ios_interstital_ad_type']=='admob'){ echo 'selected="selected"'; }?>>Admob</option>
                                            <option value="facebook" <?php if($settings_row['ios_interstital_ad_type']=='facebook'){ echo 'selected="selected"'; }?>>Facebook</option>
                            
                                          </select>                                 
                                        </div>
                                      </div>
                                      <div class="form-group">
                                        <p class="field_lable">Interstitial Ad ID :-</p>
                                        <div class="col-md-12 interstital_ad_id_ios" style="display: none">
                                          <input type="text" name="interstital_ad_id_ios" id="interstital_ad_id_ios" value="<?php echo $settings_row['interstital_ad_id_ios'];?>" class="form-control">
                                        </div>

                                        <div class="col-md-12 ios_interstital_facebook_id" style="display: none">
                                          <input type="text" name="ios_interstital_facebook_id" id="ios_interstital_facebook_id" value="<?php echo $settings_row['ios_interstital_facebook_id'];?>" class="form-control">
                                        </div>
                                      </div>
                                      <div class="form-group">
                                        <p class="field_lable">Interstitial Clicks :-</p>
                                        <div class="col-md-12">
                                          <input type="text" name="interstital_ad_click_ios" id="interstital_ad_click_ios" value="<?php echo $settings_row['interstital_ad_click_ios'];?>" class="form-control">
                                        </div>
                                      </div>                    
                                    </div>                  
                                  </div>  
                                </div>
                              </div>
                            </div>
                          </div>                        
                          <div class="form-group">
                            <div class="col-md-9">
                            <button type="submit" name="admob_submit" class="btn btn-primary" style="border-radius:10px;">Save</button>
                            </div>
                          </div>
                      </div>
                    </div>
                  </form>
                </div>

                   

                <!-- api settings -->
                <div role="tabpanel" class="tab-pane" id="api_settings">   
                    <form action="" name="settings_api" method="post" class="form form-horizontal" enctype="multipart/form-data" id="api_form">
                      <input type="hidden" name="length" value="45">
                        <div class="section">
                          <div class="section-body">
                             
                            <div class="form-group">
                              <label class="col-md-3 control-label">Latest Limit:-</label>
                              <div class="col-md-6">
                                 
                                <input type="number" name="api_latest_limit" id="api_latest_limit" value="<?php echo $settings_row['api_latest_limit'];?>" class="form-control"> 
                              </div>
                              
                            </div>
                            <div class="form-group">
                              <label class="col-md-3 control-label">Page Limit:-</label>
                              <div class="col-md-6">
                                 
                                <input type="number" name="api_page_limit" id="api_page_limit" value="<?php echo $settings_row['api_page_limit'];?>" class="form-control"> 
                              </div>
                              
                            </div>
                            <div class="form-group">
                              <label class="col-md-3 control-label">Category List Order By:-</label>
                              <div class="col-md-6">
                                 
                                  
                                  <select name="api_cat_order_by" id="api_cat_order_by" class="select2">
                                    <option value="cid" <?php if($settings_row['api_cat_order_by']=='cid'){?>selected<?php }?>>ID</option>
                                    <option value="category_name" <?php if($settings_row['api_cat_order_by']=='category_name'){?>selected<?php }?>>Name</option>
                        
                                  </select>
                                  
                              </div>
                             
                            </div>
                            <div class="form-group">
                              <label class="col-md-3 control-label">Category Post Order By:-</label>
                              <div class="col-md-6">
                                 
                                  
                                  <select name="api_cat_post_order_by" id="api_cat_post_order_by" class="select2">
                                    <option value="id" <?php if($settings_row['api_cat_post_order_by']=='id'){?>selected<?php }?>>Channel ID</option>
                                    <option value="channel_title" <?php if($settings_row['api_cat_post_order_by']=='channel_title'){?>selected<?php }?>>Channel Name</option>
                        
                                  </select>
                                  
                              </div>
                             
                            </div>
                            <div class="form-group">
                              <label class="col-md-3 control-label">Language List Order By:-</label>
                              <div class="col-md-6">
                                 
                                  
                                  <select name="api_lan_order_by" id="api_lan_order_by" class="select2">
                                    <option value="id" <?php if($settings_row['api_lan_order_by']=='id'){?>selected<?php }?>>ID</option>
                                    <option value="language_name" <?php if($settings_row['api_lan_order_by']=='language_name'){?>selected<?php }?>>Name</option>
                        
                                  </select>
                                  
                              </div>
                             
                            </div>
                            <div class="form-group">
                              <label class="col-md-3 control-label">Genre List Order By:-</label>
                              <div class="col-md-6">
                                  <select name="api_gen_order_by" id="api_gen_order_by" class="select2">
                                    <option value="id" <?php if($settings_row['api_gen_order_by']=='id'){?>selected<?php }?>>ID</option>
                                    <option value="genre_name" <?php if($settings_row['api_gen_order_by']=='genre_name'){?>selected<?php }?>>Name</option>
                                  </select>
                                  
                              </div>
                             
                            </div>
                              
                            <div class="form-group">
                              <div class="col-md-9 col-md-offset-3">
                                <button type="submit" name="api_submit" class="btn btn-primary">Save</button>
                              </div>
                            </div>
                          </div>
                        </div>
                   </form>
                </div>

                <!-- app privacy policy -->
                <div role="tabpanel" class="tab-pane" id="api_privacy_policy">   
                  <form action="" name="api_privacy_policy" method="post" class="form form-horizontal" enctype="multipart/form-data">
                    <div class="section">
                      <div class="section-body">
                        <?php 
                        if(file_exists('privacy_policy.php'))
                        {
                          ?>
                          <div class="form-group">
                            <label class="col-md-3 control-label">App Privacy Policy URL :-</label>
                            <div class="col-md-9">
                              <input type="text" readonly class="form-control" value="<?=getBaseUrl().'privacy_policy.php'?>">
                            </div>
                          </div>
                        <?php } ?>
                        <div class="form-group">
                          <label class="col-md-3 control-label">App Privacy Policy :-</label>
                          <div class="col-md-9">
                            <textarea name="app_privacy_policy" id="privacy_policy" class="form-control"><?php echo $settings_row['app_privacy_policy'];?></textarea>
                            <script>CKEDITOR.replace( 'privacy_policy' );</script>
                          </div>
                        </div>
                        <br/>
                        <div class="form-group">
                          <div class="col-md-9 col-md-offset-3">
                            <button type="submit" name="app_pri_poly" class="btn btn-primary" style="border-radius:10px;">Save</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
                
              </div>
            </div>
          </div>
          <div class="clearfix"></div>
      </div>
    </div>
  </div>
</div>
     
        
<?php include("includes/footer.php");?>       

<script type="text/javascript">


  $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
    localStorage.setItem('activeTab', $(e.target).attr('href'));
    document.title = $(this).text()+" | <?=APP_NAME?>";
  });

  var activeTab = localStorage.getItem('activeTab');
  if(activeTab){
    $('.nav-tabs a[href="' + activeTab + '"]').tab('show');
  }

  /* For android */
  if($("select[name='banner_ad_type']").val()==='facebook'){
    $(".banner_ad_id").hide();
    $(".banner_facebook_id").show();
  }
  else{
    $(".banner_facebook_id").hide();
    $(".banner_ad_id").show(); 
  }

  $("select[name='banner_ad_type']").change(function(e){
    if($(this).val()==='facebook'){
      $(".banner_ad_id").hide();
      $(".banner_facebook_id").show();
    }
    else{
      $(".banner_facebook_id").hide();
      $(".banner_ad_id").show(); 
    }
  });


  if($("select[name='interstital_ad_type']").val()==='facebook'){
    $(".interstital_ad_id").hide();
    $(".interstital_facebook_id").show();
  }
  else{
    $(".interstital_facebook_id").hide();
    $(".interstital_ad_id").show(); 
  }

  $("select[name='interstital_ad_type']").change(function(e){

    if($(this).val()==='facebook'){
      $(".interstital_ad_id").hide();
      $(".interstital_facebook_id").show();
    }
    else{
      $(".interstital_facebook_id").hide();
      $(".interstital_ad_id").show(); 
    }
  });

  /* End */

  /*  For iOS */
  if($("select[name='ios_banner_ad_type']").val()==='facebook'){
    $(".banner_ad_id_ios").hide();
    $(".ios_banner_facebook_id").show();
  }
  else{
    $(".ios_banner_facebook_id").hide();
    $(".banner_ad_id_ios").show(); 
  }

  $("select[name='ios_banner_ad_type']").change(function(e){
    if($(this).val()==='facebook'){
      $(".banner_ad_id_ios").hide();
      $(".ios_banner_facebook_id").show();
    }
    else{
      $(".ios_banner_facebook_id").hide();
      $(".banner_ad_id_ios").show(); 
    }
  });


  if($("select[name='ios_interstital_ad_type']").val()==='facebook'){
    $(".interstital_ad_id_ios").hide();
    $(".ios_interstital_facebook_id").show();
  }
  else{
    $(".ios_interstital_facebook_id").hide();
    $(".interstital_ad_id_ios").show(); 
  }

  $("select[name='ios_interstital_ad_type']").change(function(e){

    if($(this).val()==='facebook'){
      $(".interstital_ad_id_ios").hide();
      $(".ios_interstital_facebook_id").show();
    }
    else{
      $(".ios_interstital_facebook_id").hide();
      $(".interstital_ad_id_ios").show(); 
    }
  });


  if($("select[name='native_ad_type']").val()==='facebook'){
    $(".native_ad_id").hide();
    $(".native_facebook_id").show();
  }
  else{
    $(".native_facebook_id").hide();
    $(".native_ad_id").show(); 
  }

  $("select[name='native_ad_type']").change(function(e){

    if($(this).val()==='facebook'){
      $(".native_ad_id").hide();
      $(".native_facebook_id").show();
    }
    else{
      $(".native_facebook_id").hide();
      $(".native_ad_id").show(); 
    }
  });

</script>