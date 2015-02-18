<?php
include("header.php");
?>

	<div id="body">
        <h4>Total Companies: <?php echo $total_companies; ?></h4>
		<p>Select following option:</p>
        
        <a href="<?php echo $base_url; ?>/home/update">Update DB</a><br><br>
        <a href="<?php echo $base_url; ?>/home/select_com">Select Companies</a><br><br>
        <a href="<?php echo $base_url; ?>/home/merge_duplicates">Merge Duplicate Companies</a><br><br>
        <a href="<?php echo $base_url; ?>/home/candlestick_chart">Candlestick chart</a><br><br> <!--https://developers.google.com/chart/interactive/docs/gallery/candlestickchart-->
        <a href="<?php echo $base_url; ?>/home/export">Export</a> <!--mql http://docs.mql4.com/-->
    </div>

	
<?php
include("footer.php");
?>