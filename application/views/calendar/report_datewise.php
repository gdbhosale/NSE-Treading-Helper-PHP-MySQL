<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Daily Download Report: <?php echo $date; ?> <a class="btn btn-success btn-md pull-right" href="<?php echo $base_url; ?>/calendar?year=<?php echo $year; ?>">Back to Calendar</a></h1>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
    	<?php
    	if($report['status'] == "SUCCESS") {
    		$tag = "btn-success";
    	} else {
    		$tag = "btn-danger";
    	}
    	?>
    	<h4>Status: <a class="btn <?php echo $tag; ?> btn-xs"><?php echo $report['status']; ?></a></h4>
    	<h4>Filename: <b class="cl-blue"><?php echo $report['data']->file_name; ?></b></h4>
    	<h4>Was file offline: <b class="cl-green"><?php echo $report['data']->file_offline_status; ?></b></h4>
    	<h4>File Download status: <b class="cl-blue"><?php echo $report['data']->file_download_status; ?></b></h4>
    	<h4>Download on: <b class="cl-green"><?php echo $report['time_updated']; ?></b></h4>
    	<h4>Companies Downloaded: <b class="cl-blue"><?php echo $report['tot_com_load']; ?></b></h4>
    	<h4>New Companies: <b class="cl-red"><?php echo $report['new_com_load']; ?></b></h4>
    	<?php
    	if($report['new_com_load'] > 0) {
    		echo "<ol>";
    		foreach ($report['data']->new_companies as $value) {
				echo "<li>".$value."</li>";
			}
    		echo "</ol>";
    	}
    	?>
        <h4>Error Message:</h4>
        <p class="cl-red"><?php echo $report['data']->error_message; ?></p>
        <br><br><br>
    </div>
</div>

