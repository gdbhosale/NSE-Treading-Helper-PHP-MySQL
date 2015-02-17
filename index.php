<?php

// select any company table
// check if latest date is todays
//if yes
	// Go Back with success
//else
	// download opline files of missing days
	// generate log
	// push them to database
	// go back

?>
<center>
	<h1>NSE Broker Help 1.0</h1><br><br>
	<a href="update.php">Update DB</a><br><br>
	<a href="select_com.php">Select Companies</a><br><br>
	<a href="merge_duplicates.php">Merge Duplicate Companies</a><br><br>
	<a href="candlestick_chart.php">Candlestick chart</a><br><br> <!--https://developers.google.com/chart/interactive/docs/gallery/candlestickchart-->
	<a href="export.php">Export</a> <!--mql http://docs.mql4.com/-->
</center>