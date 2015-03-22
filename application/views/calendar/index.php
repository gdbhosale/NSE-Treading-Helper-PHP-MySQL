<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
        	Data Calendar
        	<select id="selectYear">
        		<?php
        		
        		for ($i=2000; $i < 2016; $i++) { 
        			if($cnt_year == $i) {
        				echo "<option selected>$i</option>";
        			} else {
        				echo "<option>$i</option>";
        			}
        		}
        		?>
        	</select>
        	<script type="text/javascript">
        	$(".download-again").on("click", function() {
        		
        	});
        	</script>
        </h1>
    </div>
</div>
<!-- /.row -->
<h2 class="year"><center><?php echo $cnt_year; ?></center></h2>
<div class="row">
	<?php
		for ($i=1; $i <= 12; $i++) { 
			?>
			<div class="col-lg-3 col-md-4 col-sm-6">
		        <?php echo draw_calendar($i, 2015, $report_days); ?>
		    </div>
			<?php
		}
	?>
</div>
<br><br>
<h4>Note:</h4>
<span class="calBox">1</span> : Data not loaded for this date.<br>
<span class="calBox red">1</span> : Error while loading data.<br>
<span class="calBox green">1</span> : Data successfully loaded.<br>
<br>
<br>
<br>
