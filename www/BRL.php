<html>
  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
<?php
// connect to DB
require "db_config.inc";
//saldos
require "saldos.inc";

class TableRows extends RecursiveIteratorIterator { 
    function __construct($it) { 
        parent::__construct($it, self::LEAVES_ONLY); 
    }

    function current() {
	if (self::key() == 'time') {
		$dt = DateTime::createFromFormat( "U", parent::current() );
		return 'new Date('. $dt->format("Y, n - 1, j, G, p\a\\r\s\\e\I\\n\\t(i, 10), s")."),";
	}
        return parent::current() . ",";
    }

    function beginChildren() { 
        echo "["; 
    } 

    function endChildren() { 
        echo "]," . "\n";
    } 
} 

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    function makeDataDays($days) {
	makeData("today -$days days");
    }

    function makeData($modifier) {
	global $conn, $BTC, $DCR, $ETH, $LTC;
        $dt = new DateTime();
        $dt->modify($modifier);
        $date = $dt->format('U');
	$stmt = $conn->prepare(
        "SELECT time,
		LTCBTC * BTCBRL * $LTC,
		ETHBTC * BTCBRL * $ETH,
		DCRBTC * BTCBRL * $DCR,
		BTCBRL * $BTC,
		BTCBRL * $BTC +
                DCRBTC * BTCBRL * $DCR +
                ETHBTC * BTCBRL * $ETH +
                LTCBTC * BTCBRL * $LTC as total
        FROM quotes
        WHERE time > $date
;");
        $stmt->execute();
        // set the resulting array to associative
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
        echo '[';
        foreach(new TableRows(new RecursiveArrayIterator($stmt->fetchAll())) as $k=>$v) { 
            echo $v;
        }
        echo ']';
    }
?>
    </script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(function() { 
		drawChart(<?php makeData("last Sunday"); ?>, 'Week to date', 'chartweek'); 
		drawChart(<?php makeData("first day of this month"); ?>, 'Month to date', 'chartmonth'); 
		drawChart(<?php makeData("1 january 2020"); ?>, 'year to date', 'chartyear'); 
		drawChart(<?php makeDataDays(7); ?>, '7 days', 'chart7'); 
		drawChart(<?php makeDataDays(30); ?>, '30 days', 'chart30'); 
		drawChart(<?php makeData("10 years ago"); ?>, 'All Time', 'chartall'); 
      });

      function addDataHeader(data) {
	return [['date', 'LTC', 'ETH', 'DCR', 'BTC', 'Total']].concat(data);
      }

      function drawChart(raw_data, title, domid) {
        var data = google.visualization.arrayToDataTable(addDataHeader(raw_data));

        var options = {
          title: title,
          //curveType: 'function',
          legend: { position: 'bottom' },
	  //isStacked: true,
	series: {
          0: {targetAxisIndex: 0, type: 'line'},
          1: {targetAxisIndex: 0, type: 'line'},
          2: {targetAxisIndex: 0, type: 'line'},
          3: {targetAxisIndex: 0, type: 'line'},
          4: {targetAxisIndex: 0, type: 'line'},
        },
        vAxes: {
          // Adds titles to each axis.
          0: {title: 'BRL'},
        }
        };

        var chart = new google.visualization.ComboChart(document.getElementById(domid));
        chart.draw(data, options);
      }
    </script>
  </head>
  <body>
    <div id="chartweek" style="width: 900px; height: 500px"></div>
    <div id="chartmonth" style="width: 900px; height: 500px"></div>
    <div id="chartyear" style="width: 900px; height: 500px"></div>
    <div id="chart7" style="width: 900px; height: 500px"></div>
    <div id="chart30" style="width: 900px; height: 500px"></div>
    <div id="chartall" style="width: 900px; height: 500px"></div>
  </body>
</html>
<?php
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
$conn = null;
?>
