<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Temperatur/Luftfeuchtigkeit</title>

<script src="js/jquery-ui-1.12.1.custom\external\jquery\jquery.js"></script>
<script src="js/jquery-ui-1.12.1.custom\jquery-ui.min.js"></script>
<script src="js/jquery-ui-1.12.1.custom\jquery.ui.datepicker-de.js"></script>
<script src="js/highcharts/highcharts.js"></script>
<script src="js/highcharts/highcharts-more.js"></script>
<script src="js/highcharts/stock.js"></script>
<script src="js/highcharts/exporting.js"></script>
<script src="js/highcharts/export-data.js"></script>
<script>
/*
var modulname = getQueryVariable("modul");

function getQueryVariable(variable) {
  var query = window.location.search.substring(1);
  var vars = query.split("&");
  for (var i=0;i<vars.length;i++) {
    var pair = vars[i].split("=");
    if (pair[0] == variable) {
      return pair[1];
    }
  } 
  alert('Query Variable ' + variable + ' not found');
}
*/
var x = new Date();
var currentTimeZoneOffset = x.getTimezoneOffset();
Highcharts.setOptions({
    global: {
        timezoneOffset: currentTimeZoneOffset
    }
});
var date_from, date_to, modulname;
$( document ).ready(function() {
  $("#datepicker1").datepicker( {defaultDate: -1} ).on( "change", function() {
    date_from = $(this).val();
	loadPage();
  });
  $("#datepicker2").datepicker( {defaultDate:  0} ).on( "change", function() {
    date_to = $(this).val();
	loadPage();
  });
	
  $("#modulname").on("change", function(){
    modulname = $("#modulname option:selected").val();
	loadPage();
  });

	modulname = $("#modulname option:selected").val();
	date_from = $("#datepicker1").val();
	date_to = $("#datepicker2").val();
	
	loadPage();
});	
	
function getDate( element ) {
  var date;
  try {
	value = element.val();

	date = $.datepicker.parseDate( dateFormat, value );
  } catch( error ) {
	date = null;
  }

  return date;
}	
function convertDate(germanDate){
	parts = germanDate.split(".");
	return parts[2]+"-"+parts[1]+"-"+parts[0];
}

function loadPage(){
	if(modulname=="" || modulname == undefined ) return false;
	if(date_from=="") return false;
	if(date_to=="") date_to = getDate();

	
    $.getJSON('/includes/getData.php?modul='+modulname+'&date_from='+convertDate(date_from)+'&date_to='+convertDate(date_to), function (data) {

        Highcharts.chart('container_gauge_temp', {

            chart: {
                type: 'gauge',
                plotBackgroundColor: null,
                plotBackgroundImage: null,
                plotBorderWidth: 0,
                plotShadow: false
            },

            title: {
                text: 'Temperatur'
            },

            pane: [{
                startAngle: -150,
                endAngle: 150,
            }],

            // the value axis
            yAxis: [{
                min: 5,
                max: 35,

                minorTickInterval: 'auto',
                minorTickWidth: 1,
                minorTickLength: 10,
                minorTickPosition: 'inside',
                minorTickColor: '#666',

                tickPixelInterval: 30,
                tickWidth: 2,
                tickPosition: 'inside',
                tickLength: 10,
                tickColor: '#666',
                labels: {
                    step: 2,
                    rotation: 'auto'
                },
                title: {
                    text: '° Celsius'
                },
                plotBands: [{
                    from: 10,
                    to: 21,
                    color: '#3397ff' // blue
                }, {
                    from: 21,
                    to: 23,
                    color: '#2fb30e' // green
                }, {
                    from: 23,
                    to: 40,
                    color: '#DF5353' // red
                }]
            }],

            series: [{
                name: 'Temperatur',
                data: [data["temp"][data["temp"].length-1][1]],
                yAxis:0,
                tooltip: {
                    valueSuffix: ' °C'
                }
            }]

        });      
      
      
        Highcharts.chart('container_gauge_humidity', {

            chart: {
                type: 'gauge',
                plotBackgroundColor: null,
                plotBackgroundImage: null,
                plotBorderWidth: 0,
                plotShadow: false
            },

            title: {
                text: 'Luftfeuchtigkeit'
            },

            pane: [{
                startAngle: -150,
                endAngle: 150,
            }],

            // the value axis
            yAxis: [{
                min: 0,
                max: 100,

                minorTickInterval: 'auto',
                minorTickWidth: 1,
                minorTickLength: 10,
                minorTickPosition: 'inside',
                minorTickColor: '#666',

                tickPixelInterval: 30,
                tickWidth: 2,
                tickPosition: 'inside',
                tickLength: 10,
                tickColor: '#666',
                labels: {
                    step: 2,
                    rotation: 'auto'
                },
                title: {
                    text: '%'
                },
                plotBands: [{
                    from: 0,
                    to: 40,
                    color: '#DF5353' // red
                }, {
                    from: 40,
                    to: 60,
                    color: '#2fb30e' // green
                }, {
                    from: 60,
                    to: 100,
                    color: '#DF5353' // red
                }]
            }],

            series: [{
                name: 'Luftfeuchtigkeit',
                data: [data["humidity"][data["humidity"].length-1][1]],
                tooltip: {
                    valueSuffix: ' %'
                }
            }]

        });      
           
            
      
        // Create the chart
        Highcharts.stockChart('container_graph', {

            rangeSelector: {
                selected: 1,
				buttons: [{
					type: 'day',
					count: 1,
					text: 'Tag'
				}, {
					type: 'week',
					count: 1,
					text: 'Woche'
				}, {
					type: 'month',
					count: 1,
					text: 'Monat'
				}, {
					type: 'month',
					count: 3,
					text: 'Quartal'
				}, {
					type: 'month',
					count: 6,
					text: 'Halbjahr'
				}, {
					type: 'year',
					count: 1,
					text: 'Jahr'
				}, {
					type: 'all',
					text: 'Alles'
				}],
				buttonTheme: {
					width: 100
				},
            },
            yAxis: [{
                labels: {
                    align: 'right',
                    x: -3
                },
                title: {
                    text: 'Temperatur'
                },
                height: '40%',
                lineWidth: 2,
                resize: {
                    enabled: true
                }
            }, {
                labels: {
                    align: 'right',
                    x: -3
                },
                title: {
                    text: 'Luftfeuchtigkeit'
                },
                top: '45%',
                height: '40%',
                offset: 0,
                lineWidth: 2
            }],
            series: [{
                name: 'Temperatur',
                data: data["temp"],
                type: 'spline',
                yAxis: 0,
                tooltip: {
                    valueDecimals: 2
                }
            },{
                name: 'Luftfeuchtigkeit',
                data: data["humidity"],
                type: 'spline',
                yAxis: 1,
                tooltip: {
                    valueDecimals: 2
                }
            }]
        });
    });
  }

</script>
<link rel="stylesheet" href="js/jquery-ui-1.12.1.custom/jquery-ui.css">

<style>
html, body {padding:0; margin:0; height:100%;}
#container_graph{height:98%;width:70%;float:right;}
#container_gauge{height:98%;width:30%;float:left;}
  .gauges{width:60%; margin:0 auto 2%; height:32%;}
  .gauges:last-child {margin-bottom:0;}
.clearer{height:0; font-size:0; line-height:0; clear:both;}
  #navigation{position: absolute;
    z-index: 10;
    left: 30%;
    font-size: 16px;
    line-height: 18px;
    top: 44px;
    margin-left: 10px;
}
  #navigation * {font-size:14px; line-height:16px;}
</style></head>

<body>
<div id="navigation">
<select id="modulname" name="modulename">
<?php 
  require_once("config.php");
  require_once("includes/db_access.php");
  if(!($stmt = $mysqli->prepare("SELECT * FROM ".$db_table_sensors." ORDER by name ASC"))){
     echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;    
  }
  $stmt->execute();
  $res = $stmt->get_result();

  while ($row = $res->fetch_assoc()){	
    echo "<option value='" . $row["mac"] . "'>" .$row["name"] . " - " . $row["raum"] . "</option>"; 
  }
?>
</select>
<input type="text" id="datepicker1" value="<?php echo date ("d.m.Y"); ?>">
<input type="text" id="datepicker2" value="<?php echo date ("d.m.Y",time() + 60 * 60 * 24); ?>">
</div>
<div id="container_gauge">
  <div id="container_gauge_temp" class="gauges"></div>
  <div id="container_gauge_humidity" class="gauges"></div>
  <div id="container_gauge_spannung" class="gauges"></div>
</div>
<div id="container_graph"></div>
<div class="clearer"></div>
</body>
</html>
