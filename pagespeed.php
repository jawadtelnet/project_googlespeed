<?php


  

  function checkPageSpeed($url){    
    if (function_exists('file_get_contents')) {   
      
    $result = file_get_contents($url);
    }    
  if ($result == '') {    
        $ch = curl_init();    
        $timeout = 60;    
        curl_setopt($ch, CURLOPT_URL, $url);    
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);  
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);    
        $result = curl_exec($ch);    
        curl_close($ch);    
 }    

 return $result;    
}  

$key = "AIzaSyC1-TX-CnpegLsbNhYlM4eHGcj-7UlX-_c";  
$url = "https://www.voici.fr/";  
$url_req = 'https://pagespeedonline.googleapis.com/pagespeedonline/v5/runPagespeed?category=performance&url=https://www.voici.fr/';

$results = checkPageSpeed($url_req);  

$obj = json_decode($results,true); 

//print_r($obj['lighthouseResult']['audits']['user-timings']);

$items = $obj['lighthouseResult']['audits']['user-timings']['details']['items'];

$items2 = $obj['lighthouseResult']['audits']['network-requests']['details']['items'];


$mark_array = array();
$measure_array = array();
$javascript_array = array();
foreach($items as $i => $item) {
    if(isset($item['timingType']) && $item['timingType'] == 'Mark') {
      $mark_array[]=array((string)$item['name'],floatval($item['startTime']));
    }

    if(isset($item['timingType']) && $item['timingType'] == 'Measure') {
      $measure_array[] =array((string)$item['name'],floatval($item['duration']));
    }
  }

  foreach($items2 as $i => $item) {
    if(isset($item['resourceType']) && $item['resourceType'] == 'Script') {
      $javascript_array[]=array((string)$item['url'],intval($item['resourceSize']));
    }
   
  }
 
  echo "<pre>";
$data1=json_encode($mark_array);
$data2=json_encode($measure_array);
$data3=json_encode($javascript_array);

print_r($data3);

?>

<html>
     <head>
       <!--Load the AJAX API-->
       <script type="text/javascript" src="https://www.google.com/jsapi"></script>
       <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
       <script type="text/javascript">

  
       google.load('visualization', '1', {'packages':['corechart']});

     
       google.setOnLoadCallback(drawChart);

       function drawChart() {

       
        var data = new google.visualization.DataTable();
           data.addColumn("string", "MS");
           data.addColumn("number", "Start Time");
           data.addRows(<?php echo $data1 ?>);  
        
         var options = {
              title: 'User Timing Marks',
             is3D: 'true',
             width: 800,
             height: 600
           };
        
         var chart = new google.visualization.ColumnChart(document.getElementById('user_timming_div'));
         chart.draw(data, options);

         var data2 = new google.visualization.DataTable();
         data2.addColumn("string", "MS");
         data2.addColumn("number", "Duration");
         data2.addRows(<?php echo $data2 ?>);  
        
         var options2 = {
              title: 'User Timing Measure',
             is3D: 'true',
             width: 800,
             height: 600
           };
        
         var chart2 = new google.visualization.PieChart(document.getElementById('measure_timming_div'));
         chart2.draw(data2, options2);

         var data3 = new google.visualization.DataTable();
         data3.addColumn("string", "Byte");
         data3.addColumn("number", "Size");
         data3.addRows(<?php echo $data3 ?>);  
        
         var options3 = {
              title: 'application/javascript',
             is3D: 'true',
             width: 800,
             height: 600
           };
        
         var chart3 = new google.visualization.ComboChart(document.getElementById('javascript_div'));
         chart3.draw(data3, options3);


         
       }
       </script>

<div id="columnchart_values" style="width: 900px; height: 300px;"></div>
     </head>

     <body>    <!--Div that will hold the pie chart-->
       <div id="user_timming_div"></div>
       <div id="measure_timming_div"></div>
       <div id="javascript_div"></div>
     </body>
</html>


