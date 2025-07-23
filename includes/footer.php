<footer class="app-footer" style="background:white; position:fixed; bottom:0; width:100%; text-align:center;">
  <div class="row">
    <div class="col-xs-12">
      <div class="footer-copyright" style="margin-top:20px;">
        Copyright © 2024 <a href="" target="_blank">Sensewheel</a>. All Rights Reserved.
      </div>
    </div>
  </div>
</footer>

  </div>
</div>
<script type="text/javascript" src="assets/js/vendor.js"></script> 
<script type="text/javascript" src="assets/js/app.js"></script>

<script src="assets/js/notify.min.js"></script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript" src="assets/sweetalert/sweetalert.min.js"></script>

<?php if(isset($_SESSION['msg'])){?>
<script type="text/javascript">
  $('.notifyjs-corner').empty();
  $.notify(
    '<?php echo $client_lang[$_SESSION["msg"]];?>',
    { position:"top center",className: '<?=$_SESSION["class"]?>'}
  );
</script>
<?php
  unset($_SESSION['msg']);
  unset($_SESSION['class']); 
  } 
?>

</body>

<script type="text/javascript">

  function isImage(filename) {
    var ext = getExtension(filename);
    switch (ext.toLowerCase()) {
    case 'jpg':
    case 'jpeg':
    case 'png':
    case 'svg':
    case 'gif':
        return true;
    }
    return false;
  }

  function getExtension(filename) {
    var parts = filename.split('.');
    return parts[parts.length - 1];
  }

  if($(".dropdown-li").hasClass("active")){
    var _test='<?php echo $active_page; ?>';
    $("."+_test).next(".cust-dropdown-container").show();
    //$("."+_test).find(".title").next("i").removeClass("fa fa-caret-up");
    $("."+_test).find(".title").next("i").addClass("fa fa-caret-down");
  }

  $(document).ready(function(e){
    var _flag=false;

    $(".dropdown-a").click(function(e){

      $(this).parents("ul").find(".cust-dropdown-container").slideUp();

      $(this).parents("ul").find(".title").next("i").addClass("fa fa-caret-down");
     // $(this).parents("ul").find(".title").next("i").removeClass("fa fa-caret-up");

      if($(this).parent("li").next(".cust-dropdown-container").css('display') !='none'){
          $(this).parent("li").next(".cust-dropdown-container").slideUp();
          $(this).find(".title").next("i").addClass("fa fa-caret-down");
         // $(this).find(".title").next("i").removeClass("fa fa-caret-up");
      }else{
        $(this).parent("li").next(".cust-dropdown-container").slideDown();
        //$(this).find(".title").next("i").removeClass("fa fa-caret-down");
        $(this).find(".title").next("i").addClass("fa fa-caret-up");
      }

    });
  });
</script>

</body>
</html>