<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
        	Company: <?php echo $company['symbol']; ?>
            <a class="btn btn-success pull-right" href="<?php echo $base_url."/companies/export?com_id=".$company['id']; ?>">Export</a>
        </h1>
    </div>
</div>
<h3>Share Data:</h3>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Multiple Axes Line Chart
            </div>
            <div class="panel-body">
                <div class="flot-chart">
                    <div class="flot-chart-content" id="flot-line-chart-multi"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Flot Charts JavaScript -->
<script src="<?php echo $base_url; ?>/bower_components/flot/excanvas.min.js"></script>
<script src="<?php echo $base_url; ?>/bower_components/flot/jquery.flot.js"></script>
<script src="<?php echo $base_url; ?>/bower_components/flot/jquery.flot.pie.js"></script>
<script src="<?php echo $base_url; ?>/bower_components/flot/jquery.flot.resize.js"></script>
<script src="<?php echo $base_url; ?>/bower_components/flot/jquery.flot.time.js"></script>
<script src="<?php echo $base_url; ?>/bower_components/flot.tooltip/js/jquery.flot.tooltip.min.js"></script>
<script type="text/javascript">

//Flot Multiple Axes Line Chart
$(function() {
    var openValues = [
        <?php
        foreach($data_days as $row) {
            echo "[".strtotime($row['date']." 18:00:00")."000, ".$row['open_price']."],\n";
        }
        ?>
    ];
    var closeValues = [
        <?php
        foreach($data_days as $row) {
            echo "[".strtotime($row['date']." 18:00:00")."000, ".$row['close_price']."],\n";
        }
        ?>
    ];
    var highValues = [
        <?php
        foreach($data_days as $row) {
            echo "[".strtotime($row['date']." 18:00:00")."000, ".$row['high_price']."],\n";
        }
        ?>
    ];
    var lowValues = [
        <?php
        foreach($data_days as $row) {
            echo "[".strtotime($row['date']." 18:00:00")."000, ".$row['low_price']."],\n";
        }
        ?>
    ];

    function euroFormatter(v, axis) {
        return v.toFixed(axis.tickDecimals) + "";
    }

    function doPlot(position) {
        $.plot($("#flot-line-chart-multi"), [{
            data: openValues,
            label: "Open Value"
        }, {
            data: closeValues,
            label: "Close Value",
            yaxis: 2
        }, {
            data: highValues,
            label: "High Value",
            yaxis: 2
        }, {
            data: lowValues,
            label: "Low Value",
            yaxis: 2
        }], {
            xaxes: [{mode: 'time'}],
            yaxes: [{min: 0}, {
                // align if we are to the right
                alignTicksWithAxis: position == "right" ? 1 : null,
                position: position,
                tickFormatter: euroFormatter
            }],
            legend: { position: 'sw' },
            grid: {
                hoverable: true //IMPORTANT! this is needed for tooltip to work
            },
            tooltip: true,
            tooltipOpts: {
                content: "%s for %x was %y",
                xDateFormat: "%d-%m-%Y",
                onHover: function(flotItem, $tooltipEl) {
                    // console.log(flotItem, $tooltipEl);
                }
            }

        });
    }
    doPlot("right");
});
</script>

<table class="table table-bordered">
<thead>
    <tr>
        <th>Date</th>
        <th>Open</th>
        <th>High</th>
        <th>Low</th>
        <th>Close</th>
    </tr>
</thead>
<tbody>
    <?php
    foreach ($data_days as $row) {
        echo "<tr>";
        echo "<td>".$row['date']."</td>";
        echo "<td>".$row['open_price']."</td>";
        echo "<td>".$row['high_price']."</td>";
        echo "<td>".$row['low_price']."</td>";
        echo "<td>".$row['close_price']."</td>";
        echo "</tr>";
    }
    ?>
    
</tbody>
</table>
<br>
<br>
<br>