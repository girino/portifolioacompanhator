<html>
  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
<?php
// connect to DB
require "db_config.inc";

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
	global $conn;
        $dt = new DateTime();
        $dt->modify($modifier);
        $date = $dt->format('U');
	$stmt = $conn->prepare(
        "SELECT time,
		BTCBRL,
		DCRBTC *1000000,
		ETHBTC *1000000,
		LTCBTC *1000000,
		BTCUSD,
		BTCEUR,
		USDBRL * 1000
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
		drawChart(<?php makeDataDays(7); ?>, '7 days', 'chart7'); 
		drawChart(<?php makeDataDays(30); ?>, '30 days', 'chart30'); 
		drawChart(<?php makeDataDays(60); ?>, '60 days', 'chart60'); 
		drawChart(<?php makeData("10 years ago"); ?>, 'All Time', 'chartall'); 
      });

      function addDataHeader(data) {
	return [['date', 'BTCBRL', 'DCR', 'ETH', 'LTC', 'BTCUSD', 'BTCEUR', 'USDBRL']].concat(data);
      }

      function drawChart(raw_data, title, domid) {
        var data = google.visualization.arrayToDataTable(addDataHeader(raw_data));

        var options = {
          title: title,
          //curveType: 'function',
          legend: { position: 'bottom' },
	series: {
          0: {targetAxisIndex: 0},
          1: {targetAxisIndex: 1},
          2: {targetAxisIndex: 1},
          3: {targetAxisIndex: 1},
          4: {targetAxisIndex: 1},
          5: {targetAxisIndex: 1},
          6: {targetAxisIndex: 1},
        },
        vAxes: {
          // Adds titles to each axis.
          0: {title: 'BRL'},
          1: {title: 'uBTC/USD/EUR'},
        }
        };

        var chart = new google.visualization.LineChart(document.getElementById(domid));
        chart.draw(data, options);
      }
    </script>
  </head>
  <body>
    <div id="chartweek" style="width: 900px; height: 500px"></div>
    <div id="chartmonth" style="width: 900px; height: 500px"></div>
    <div id="chart7" style="width: 900px; height: 500px"></div>
    <div id="chart30" style="width: 900px; height: 500px"></div>
    <div id="chart60" style="width: 900px; height: 500px"></div>
    <div id="chartall" style="width: 900px; height: 500px"></div>
  </body>
</html>
<?php
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
$conn = null;
?>
