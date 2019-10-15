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
<script src="js/highcharts/exporting.js"></script>
<script src="js/highcharts/export-data.js"></script>
<script src="js/highcharts/stock.js"></script>

<script>
var date_from, date_to, modulname;
var x = new Date();
var currentTimeZoneOffset = x.getTimezoneOffset();
Highcharts.setOptions({
    global: {
        timezoneOffset: currentTimeZoneOffset
    }
});
$( document ).ready(function() {
  $("#datepicker1").datepicker( {defaultDate: -1} ).on( "change", function() {
    date_from = $(this).val();
	loadPage();
  });
  $("#datepicker2").datepicker( {defaultDate:  0} ).on( "change", function() {
    date_to = $(this).val();
	loadPage();
  });
	

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
	if(date_from=="") return false;
	if(date_to=="") date_to = getDate();

var seriesOptions = [],
  seriesCounter = 0,
  names = ['Sensor 2', 'Sensor 3','Diff-Sensor 2-Sensor 3', 'Sensor 4'];
  //names = ['Sensor 2', 'Sensor 3','Sensor 1'];





/**
 * Create the chart when all data is loaded
 * @returns {undefined}
 */
function createChart() {

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
                offset: 0,
                resize: {
                    enabled: true
                }
            }, {
                labels: {
                    align: 'right',
                    x: -3
                },
                title: {
                    text: 'DIFF-Temperatur'
                },
                height: '40%',
                lineWidth: 2,
                offset: 40,
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
            }, {
                labels: {
                    align: 'right',
                    x: -3
                },
                title: {
                    text: 'DIFF-Luftfeuchtigkeit'
                },
                top: '45%',
                height: '40%',
                lineWidth: 2,
                offset: 40,
            }],

    series: seriesOptions
  });
}

names.forEach(function (name, i) {

  Highcharts.getJSON(
    '/includes/getData.php?modulname='+name+'&date_from='+convertDate(date_from)+'&date_to='+convertDate(date_to),
    function (data) {

      if(name.includes("Diff-"))
      {
        seriesOptions[i] = {
          name: name,
          data: data["temp"],
          type: 'spline',
          yAxis: 1,
          tooltip: {
            valueDecimals: 2
          },
	  dashStyle: 'longdash'
        };
        seriesOptions[(i+names.length)] = {
          name: name,
          data: data["humidity"],
          type: 'spline',
          yAxis: 3,
          tooltip: {
            valueDecimals: 2
          },
	  dashStyle: 'longdash'
        };
      }else{
        seriesOptions[i] = {
          name: name,
          data: data["temp"],
          type: 'spline',
          yAxis: 0,
          tooltip: {
            valueDecimals: 2
          }
        };
        seriesOptions[(i+names.length)] = {
          name: name,
          data: data["humidity"],
          type: 'spline',
          yAxis: 2,
          tooltip: {
            valueDecimals: 2
          }
        };
      }
      // As we're loading the data asynchronously, we don't know what order it will arrive. So
      // we keep a counter and create the chart when all the data is loaded.
      seriesCounter += 1;

      if (seriesCounter === names.length) {
        createChart();
      }
    }
  );
});




}


</script>
<link rel="stylesheet" href="js/jquery-ui-1.12.1.custom/jquery-ui.css">

<style>
html, body {padding:0; margin:0; height:100%;}
#container_graph{height:98%;width:100%;}
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
<input type="text" id="datepicker1" value="<?php echo date ("d.m.Y"); ?>">
<input type="text" id="datepicker2" value="<?php echo date ("d.m.Y",time() + 60 * 60 * 24); ?>">
</div>
<div id="container_graph"></div>
<div class="clearer"></div>
</body>
</html>
