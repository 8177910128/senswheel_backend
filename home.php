<?php 
    $page_title="Dashboard";
    $active_page="dashboard";

    include("includes/header.php");
    include("includes/function.php");

    $qry_cat="SELECT COUNT(*) as num FROM tbl_category";
    $total_category= mysqli_fetch_array(mysqli_query($mysqli,$qry_cat));
    $total_category = $total_category['num'];

    $qry_channels="SELECT COUNT(*) as num FROM tbl_channels";
    $total_channels = mysqli_fetch_array(mysqli_query($mysqli,$qry_channels));
    $total_channels = $total_channels['num'];

    $qry_comments="SELECT COUNT(*) as num FROM tbl_comments";
    $total_comments = mysqli_fetch_array(mysqli_query($mysqli,$qry_comments));
    $total_comments = $total_comments['num'];

    $qry_reports="SELECT COUNT(*) as num FROM tbl_reports";
    $total_reports = mysqli_fetch_array(mysqli_query($mysqli,$qry_reports));
    $total_reports = $total_reports['num'];


    $qry_movie="SELECT COUNT(*) as num FROM tbl_movies";
    $total_movies = mysqli_fetch_array(mysqli_query($mysqli,$qry_movie));
    $total_movies = $total_movies['num'];

    $qry_series="SELECT COUNT(*) as num FROM tbl_series";
    $total_series = mysqli_fetch_array(mysqli_query($mysqli,$qry_series));
    $total_series = $total_series['num'];
    
    $qry_shortfilms="SELECT COUNT(*) as num FROM tbl_shortfilms";
    $total_shortfilms = mysqli_fetch_array(mysqli_query($mysqli,$qry_shortfilms));
    $total_shortfilms = $total_shortfilms['num'];
    
    $qry_drama="SELECT COUNT(*) as num FROM tbl_drama";
    $total_drama = mysqli_fetch_array(mysqli_query($mysqli,$qry_drama));
    $total_drama = $total_drama['num'];
    
    $qry_songs="SELECT COUNT(*) as num FROM tbl_songs";
    $total_songs = mysqli_fetch_array(mysqli_query($mysqli,$qry_songs));
    $total_songs = $total_songs['num'];

    $qry_users="SELECT COUNT(*) as num FROM tbl_users";
    $total_users = mysqli_fetch_array(mysqli_query($mysqli,$qry_users));
    $total_users = $total_users['num'];
    
    // Query to get the views from the respective tables
    $qry_movies_views = "SELECT sum(total_views) as views FROM tbl_movies";
    $total_movies_views = mysqli_fetch_array(mysqli_query($mysqli, $qry_movies_views));
    $total_movies_views = $total_movies_views['views'];
   
    $qry_songs_views = "SELECT sum(total_views) as views FROM tbl_songs";
    $total_songs_views = mysqli_fetch_array(mysqli_query($mysqli, $qry_songs_views));
    $total_songs_views =  $total_songs_views['views'];
    
    $qry_series_views = "SELECT sum(total_views)  as views FROM tbl_series";
    $total_series_views = mysqli_fetch_array(mysqli_query($mysqli, $qry_series_views));
    $total_series_views = $total_series_views['views'];
    
    $qry_channels_views = "SELECT sum(total_views) as views FROM tbl_channels";
    $total_channels_views = mysqli_fetch_array(mysqli_query($mysqli, $qry_channels_views));
    $total_channels_views = $total_channels_views['views'];
    
    // Calculate the total views
    $total_views = $total_movies_views + $total_songs_views + $total_series_views + $total_channels_views;

    $moviesPercentage = ($total_movies_views / $total_views) * 100;
    $songsPercentage = ($total_songs_views / $total_views) * 100;
    $seriesPercentage = ($total_series_views / $total_views) * 100;
    $channelsPercentage = ($total_channels_views / $total_views) * 100;
    
   
   
    $countStr='';
    $no_data_status=false;
    $count=$monthCount=0;

    for ($mon=1; $mon<=12; $mon++) {

        if(date('n') < $mon){
          break;
        }
        
        if(isset($_GET['filterByYear'])){

          $year=$_GET['filterByYear'];

          $month = date('M', mktime(0,0,0,$mon, 1, $year));

          $sql_user="SELECT `id` FROM tbl_users WHERE DATE_FORMAT(FROM_UNIXTIME(`register_on`), '%c') = '$mon' AND DATE_FORMAT(FROM_UNIXTIME(`register_on`), '%Y') = '$year'";
        }
        else{

          $month = date('M', mktime(0,0,0,$mon, 1, date('Y')));

          $sql_user="SELECT `id` FROM tbl_users WHERE DATE_FORMAT(FROM_UNIXTIME(`register_on`), '%c') = '$mon'";
        }

        $count=mysqli_num_rows(mysqli_query($mysqli, $sql_user));

        $countStr.="['".$month."', ".$count."], ";

        if($count!=0){
          $monthCount++;
        }

    }

    if($monthCount!=0){
      $no_data_status=false;
    }
    else{
      $no_data_status=true;
    }

    $countStr=rtrim($countStr, ", ");

?>     
<style>
    .legend-rect {
    display: inline-block;
    width: 25px;   /* Adjust the width for your desired size */
    height: 10px;  /* Adjust the height for your desired size */
    border-radius: 4px;  /* Slightly rounded corners, optional */
    margin-right: 5px;  /* Space between the rectangle and the text */
    vertical-align: middle;  /* Align with the text */
}

</style>

<div class="row">
    <div class="container-fluid">
    <div class="card" style="background: #FFF; box-shadow: 0px 5px 10px 0px #CCC; border-radius: 20px;">
        <div class="card-body">
            <div class="col-lg-4">
                    <a href="manage_movies.php" class="card card-banner" style="border-radius: 10px;">
                        <div class="card-body d-flex align-items-center">
                            <!-- Left Side: Icon -->
                            <div class="icon" style="flex: 0 0 auto; margin-right: 15px;background:#EEF9FF;">
                                <span class="fa fa-users" style="font-size:30px;color:#47AAE4;"></span>
                            </div>
                            <!-- Right Side: Title and Count -->
                            <div class="content">
                                <div class="value"><span class="sign"></span><?php echo thousandsNumberFormat($total_users);?></div>
                                <div class="title">Users</div>
          
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4">
                    <a href="manage_movies.php" class="card card-banner" style="border-radius: 10px;">
                        <div class="card-body d-flex align-items-center">
                            <!-- Left Side: Icon -->
                            <div class="icon" style="flex: 0 0 auto; margin-right: 15px;background:#F7EDFF;">
                                <span class="fa fa-video-camera" style="font-size:30px;color:#D4A2FC;"></span>
                            </div>
                            <!-- Right Side: Title and Count -->
                            <div class="content">
                                <div class="value"><span class="sign"></span><?php echo thousandsNumberFormat($total_movies);?></div>
                                <div class="title">Movies</div>
          
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4">
                    <a href="manage_movies.php" class="card card-banner" style="border-radius: 10px;">
                        <div class="card-body d-flex align-items-center">
                            <!-- Left Side: Icon -->
                            <div class="icon" style="flex: 0 0 auto; margin-right: 15px;background:#FFE7F8;">
                                <span class="fa fa-tv" style="font-size:30px;color:#EE58C4;"></span>
                            </div>
                            <!-- Right Side: Title and Count -->
                            <div class="content">
                                <div class="value"><span class="sign"></span><?php echo thousandsNumberFormat($total_series);?></div>
                                <div class="title">Tv Series</div>
          
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4">
                    <a href="manage_movies.php" class="card card-banner" style="border-radius: 10px;">
                        <div class="card-body d-flex align-items-center">
                            <!-- Left Side: Icon -->
                            <div class="icon" style="flex: 0 0 auto; margin-right: 15px;background:#EAE2FF;">
                                <span class="fa fa-video-camera" style="font-size:30px;color:#7A50EF;"></span>
                            </div>
                            <!-- Right Side: Title and Count -->
                            <div class="content">
                                <div class="value"><span class="sign"></span><?php echo thousandsNumberFormat($total_shortfilms);?></div>
                                <div class="title">Shortfilms</div>
          
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4">
                    <a href="manage_movies.php" class="card card-banner" style="border-radius: 10px;">
                        <div class="card-body d-flex align-items-center">
                            <!-- Left Side: Icon -->
                            <div class="icon" style="flex: 0 0 auto; margin-right: 15px;background:#EEF9FF;">
                                <span class="glyphicon glyphicon-th-large" style="font-size:30px;color:#47AAE4;"></span>
                            </div>
                            <!-- Right Side: Title and Count -->
                            <div class="content">
                                <div class="value"><span class="sign"></span><?php echo thousandsNumberFormat($total_drama);?></div>
                                <div class="title">Drama</div>
          
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4">
                    <a href="manage_movies.php" class="card card-banner" style="border-radius: 10px;">
                        <div class="card-body d-flex align-items-center">
                            <!-- Left Side: Icon -->
                            <div class="icon" style="flex: 0 0 auto; margin-right: 15px;background:#EAE2FF;">
                                <span class="fa fa-music" style="font-size:30px;color:#7A50EF;"></span>
                            </div>
                            <!-- Right Side: Title and Count -->
                            <div class="content">
                                <div class="value"><span class="sign"></span><?php echo thousandsNumberFormat($total_songs);?></div>
                                <div class="title">Songs</div>
          
                            </div>
                        </div>
                    </a>
                </div>
        </div>
    </div>
</div>
</div>




<div class="row">
  <div class="col-lg-4">
    <div class="container-fluid" style="background: #FFF;box-shadow: 0px 5px 10px 0px #CCC;border-radius: 20px;">
      <div class="col-lg-7">
        <h3>Views</h3>
      </div>
      
          <div class="col-lg-12" style="margin-top:30px;">
            <!-- Semi-circle Chart -->
            <div id="viewsChart" style="height: 200px;"></div>
            <!-- Total Views Display -->
            <div id="totalViews" style="position: relative; top: -150px; font-size: 24px;    margin-left: 90px;margin-top: 30px;">
                <span></span><br/>
                <small>Total Views</small>
            </div>
        </div>
        <!-- Labels -->
        <div class="col-lg-12">
            <div style="display: flex; justify-content: space-around; padding-top: 10px;margin-bottom:10px;margin-top:20px;">
                <div class="row" >
                    <div class="col-6" style="padding:10px;">
                        <span class="legend-rect" style="background-color: #DDA0DD;"></span> Movies <?= number_format($moviesPercentage, 1); ?>%
                    </div>
                    <div class="col-6" style="padding:10px;">
                        <span class="legend-rect" style="background-color: #FF69B4;"></span> Series <?= number_format($seriesPercentage, 1); ?>%
                    </div>
                </div>
                <div class="row">
                    <div class="col-6" style="padding:10px;">
                        <span class="legend-rect" style="background-color: #8A2BE2;"></span> Channels <?= number_format($channelsPercentage, 1); ?>%
                    </div>
                    <div class="col-6" style="padding:10px;">
                         <span class="legend-rect" style="background-color: #00BFFF;"></span> Songs <?= number_format($songsPercentage, 1); ?>%
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
  <div class="col-lg-8">
    <div class="container-fluid" style="background: #FFF;box-shadow: 0px 5px 10px 0px #CCC;border-radius: 20px;">
      <div class="col-lg-8">
        <h3>Users Analysis</h3>
        <p>New registrations</p>
      </div>
      <div class="col-lg-4" style="padding-top: 20px">
        <form method="get" id="graphFilter">
          <select class="form-control" name="filterByYear" style="box-shadow: none;height: auto;border-radius: 0px;font-size: 16px;border-radius:8px;">
            <?php 
              $currentYear=date('Y');
              $minYear=2020;

              for ($i=$currentYear; $i >= $minYear ; $i--) { 
                ?>
                <option value="<?=$i?>" <?=(isset($_GET['filterByYear']) && $_GET['filterByYear']==$i) ? 'selected' : ''?>><?=$i?></option>
                <?php
              }
            ?>
          </select>
        </form>
      </div>
      <div class="col-lg-12">
        <div id="registerChart">
              <p style="text-align: center;"><i class="fa fa-spinner fa-spin" style="font-size:3em;color:#aaa;margin-bottom:50px" aria-hidden="true"></i></p>
            </div>
      </div>
    </div>
  </div>
</div>


<div class="row" style="margin-bottom:50px;">
  <div class="col-lg-4">
    <div class="container-fluid" style="background: #FFF;box-shadow: 0px 5px 10px 0px #CCC;border-radius: 10px">
      <h3>Most viewed series</h3>
      <p>Series with more views.</p>
      <table class="table table-hover">
        <?php 
          $sql="SELECT * FROM tbl_series WHERE `total_views` > 5 ORDER BY `total_views` DESC LIMIT 10";
          $res=mysqli_query($mysqli, $sql);
          if(mysqli_num_rows($res) > 0)
          {

            while ($row=mysqli_fetch_assoc($res)) {
            ?>
            <tr>
              <td>
                <div style="float: left;padding-right: 20px">
                  <?php if($row['series_cover']!='' OR !file_exists($row['series_cover'])){ ?>
                    <img src="<?=$row['series_cover']?>" style="width: 220px;height: 86px;border-radius: 6px;"/>  
                  <?php }?>
                   
                </div>
                <div>
                  <a href="javascript:void(0)" title="<?=$row['series_name']?>" style="color: inherit;">
                    <?php 
                      if(strlen($row['series_name']) > 25){
                        echo substr(stripslashes($row['series_name']), 0, 25).'...';  
                      }else{
                        echo $row['series_name'];
                      }
                    ?>
                    <p style="font-weight: 500"><span class="label label-default" style="font-size: 10px;padding: 2px 8px;">Views: <?=thousandsNumberFormat($row['total_views'])?></p> 
                  </a>
                </div>
              </td>
            </tr>
            <?php }
              mysqli_free_result($res);
            }
            else{
              ?>
              <tr>
                <td class="text-center">No data available !</td>
              </tr>
              <?php
            }
        ?>
      </table>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="container-fluid" style="background: #FFF;box-shadow: 0px 5px 10px 0px #CCC;border-radius: 10px">
      <h3>Most viewed movies</h3>
      <p>Movies with more views.</p>
      <table class="table table-hover">
        <?php 
          $sql="SELECT * FROM tbl_movies WHERE `total_views` > 5 ORDER BY `total_views` DESC LIMIT 10";
          $res=mysqli_query($mysqli, $sql);

          if(mysqli_num_rows($res) > 0)
          {

            while ($row=mysqli_fetch_assoc($res)) {
            ?>
            <tr>
              <td>
                <div style="float: left;padding-right: 20px">
                  <?php if($row['movie_cover']!='' OR !file_exists($row['movie_cover'])){ ?>
                    <img src="<?=$row['movie_cover']?>" style="width: 220px;height: 86px;border-radius: 6px;"/>  
                  <?php } ?>
                    
                </div>
                <div>
                  <a href="javascript:void(0)" title="<?=$row['movie_title']?>" style="color: inherit;">
                    <?php 
                      if(strlen($row['movie_title']) > 25){
                        echo substr(stripslashes($row['movie_title']), 0, 25).'...';  
                      }else{
                        echo $row['movie_title'];
                      }
                    ?>
                    <p style="font-weight: 500"><span class="label label-default" style="font-size: 10px;padding: 2px 8px;">Views: <?=thousandsNumberFormat($row['total_views'])?></p> 
                  </a>
                </div>
              </td>
            </tr>
            <?php }
              mysqli_free_result($res);

            }
            else{
              ?>
              <tr>
                <td class="text-center">No data available !</td>
              </tr>
              <?php
            }
        ?>
      </table>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="container-fluid" style="background: #FFF;box-shadow: 0px 5px 10px 0px #CCC;border-radius: 10px">
      <h3>Most viewed Shows</h3>
      <p>Shows with more views.</p>
      <table class="table table-hover">
        <?php 
          $sql="SELECT * FROM tbl_shows WHERE `total_views` > 5 ORDER BY `total_views` DESC LIMIT 10";
          $res=mysqli_query($mysqli, $sql);
          if(mysqli_num_rows($res) > 0)
          {

            while ($row=mysqli_fetch_assoc($res)) {
            ?>
            <tr>
              <td>
                <div style="float: left;padding-right: 20px">
                  <?php if($row['shows_cover']!='' OR !file_exists($row['shows_cover'])){ ?>
                    <img src="<?=$row['shows_cover']?>" style="width: 220px;height: 86px;border-radius: 6px;"/>  
                  <?php }?>
                   
                </div>
                <div>
                  <a href="javascript:void(0)" title="<?=$row['shows_name']?>" style="color: inherit;">
                    <?php 
                      if(strlen($row['shows_name']) > 25){
                        echo substr(stripslashes($row['series_name']), 0, 25).'...';  
                      }else{
                        echo $row['shows_name'];
                      }
                    ?>
                    <p style="font-weight: 500"><span class="label label-default" style="font-size: 10px;padding: 2px 8px;">Views: <?=thousandsNumberFormat($row['total_views'])?></p> 
                  </a>
                </div>
              </td>
            </tr>
            <?php }
              mysqli_free_result($res);
            }
            else{
              ?>
              <tr>
                <td class="text-center">No data available !</td>
              </tr>
              <?php
            }
        ?>
      </table>
    </div>
  </div>
</div>

        
<?php include("includes/footer.php");?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script type="text/javascript">
  // Load the Google Charts library
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {

  var data = new google.visualization.DataTable();
  data.addColumn('string', 'Month');
  data.addColumn('number', 'Users');

  // Use the existing data array
  data.addRows([<?=$countStr?>]);

  var options = {
    curveType: 'function',  // Smooth curve lines
    fontSize: 15,  // Font size for the chart
    hAxis: {
      title: "Months of <?=(isset($_GET['filterByYear'])) ? $_GET['filterByYear'] : date('Y')?>",  // Dynamic year in axis title
      titleTextStyle: {
        color: '#000',
        bold: true,
        italic: false
      },
      gridlines: { color: 'transparent' },  // Remove horizontal gridlines for cleaner look
      textStyle: { color: '#999' }  // Adjust the month labels color
    },
    vAxis: {
      title: "Nos of Users",
      titleTextStyle: {
        color: '#000',
        bold: true,
        italic: false,
      },
      gridlines: { count: 6, color: '#e0e0e0' },  // Set visible gridlines for better readability
      format: '#',
      viewWindowMode: "explicit", 
      viewWindow: { min: 0 },  // Ensure the y-axis starts at 0
      textStyle: { color: '#999' }  // Adjust the y-axis label color
    },
    height: 400,  // Chart height
    chartArea: {
      left: 100, top: 20, width: '85%', height: '70%'  // Adjust the chart's positioning within the container
    },
    legend: { position: 'none' },  // Hide legend since it's not required
    lineWidth: 4,  // Thicker line for better visibility
    colors: ['#A020F0'],  // Purple line color
    pointSize: 7,  // Size of the points at each data point
    pointShape: 'circle',  // Use circles for data points
    animation: {
      startup: true,
      duration: 1200,
      easing: 'out',
    },
    // Fill the area under the line with gradient
    series: [{
      color: '#A020F0',  // Purple line color
      areaOpacity: 0.4,  // Opacity for the gradient
    }],
    // Gradient styling for the fill below the curve
    backgroundColor: { fill: 'transparent' },
  };

  // Draw the chart in the div with id 'registerChart'
  var chart = new google.visualization.AreaChart(document.getElementById('registerChart'));
  chart.draw(data, options);
}

// Ensure chart redraws when the window is resized
$(document).ready(function () {
    $(window).resize(function(){
        drawChart();
    });
});

    
    // Load the Google Charts library
google.charts.load('current', {
    packages: ['corechart']
});

// Set a callback function to run when the library is loaded
google.charts.setOnLoadCallback(drawViewsChart);




function drawViewsChart() {
    
     var moviesViews = <?= $total_movies_views ?>;
     var songsViews = <?= $total_songs_views ?>;
     var seriesViews = <?= $total_series_views ?>;
     var cahnnelsViews = <?= $total_channels_views ?>;
     var totalViews = moviesViews + songsViews + seriesViews + cahnnelsViews;
     
     var moviesPercentage = (moviesViews / totalViews) * 100;
     var songsPercentage = (songsViews / totalViews) * 100;
     var seriesPercentage = (seriesViews / totalViews) * 100;
     var channelsPercentage = (cahnnelsViews / totalViews) * 100;
     
    // Data for the chart
    var data = google.visualization.arrayToDataTable([
        ['Category', 'Views'],
        ['Movies', moviesPercentage],    // 18% for Movies
        ['Series', seriesPercentage],    // 27% for Series
        ['Channels', channelsPercentage],  // 10% for Channels
        ['Songs', songsPercentage],     // 45% for Songs
        ['Invisible', 30] // This slice will be invisible to create the bottom half-circle
    ]);

    // Options for the chart
    var options = {
        pieHole: 0.8,  // Creates a donut chart by cutting out the center
        pieStartAngle: 180,  // Start from the bottom (semi-circle)
        height: 250,  // Adjust height
        width: 300,   // Adjust width
        chartArea: {width: '100%', height: '100%'},  // Use full area
        legend: {position: 'none'},  // Hide default legend
        colors: ['#DDA0DD', '#FF69B4', '#8A2BE2', '#00BFFF', 'transparent'],  // Transparent color for the invisible slice
        pieSliceText: 'none',  // Hide text inside slices
    };

    // Draw the chart inside the div with id 'viewsChart'
    var chart = new google.visualization.PieChart(document.getElementById('viewsChart'));
    chart.draw(data, options);
}




</script>

<script type="text/javascript">
  
  // filter of graph
  $("select[name='filterByYear']").on("change",function(e){
    $("#graphFilter").submit();
  });

</script>       
