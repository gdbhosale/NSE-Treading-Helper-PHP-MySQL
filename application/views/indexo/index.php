<style>
#selectYear, #selectMonth, #selectType{vertical-align:middle;float:right;margin:5px;}
</style>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
        	Index Options
        	<select id="selectType">
        		<option value="B" <?php if($cnt_type == "B") echo "selected"; ?>>Both</option>
        		<option value="BN" <?php if($cnt_type == "BN") echo "selected"; ?>>Bank Nifty</option>
        		<option value="N" <?php if($cnt_type == "N") echo "selected"; ?>>Nifty</option>
        	</select>
        	<select id="selectYear">
        		<?php
        		for ($i=2000; $i < 2030; $i++) { 
        			if($cnt_year == $i) {
        				echo "<option selected>$i</option>";
        			} else {
        				echo "<option>$i</option>";
        			}
        		}
        		?>
        	</select>
        	<select id="selectMonth">
        		<?php
        		for ($i=0; $i < 12; $i++) { 
        			if($cnt_month == $i) {
        				echo "<option selected>$i</option>";
        			} else {
        				echo "<option>$i</option>";
        			}
        		}
        		?>
        	</select>
        	<script type="text/javascript">
        	function reload() {
        		var m = $("#selectMonth").val();
        		if(m < 10) {
        			m = "0"+m;
        		}
        		window.location.href = "<?php echo $base_url; ?>/indexo?type="+$("#selectType").val()+"&year="+$("#selectYear").val()+"&month="+m;
        	}
        	$("#selectYear, #selectMonth, #selectType").on("change", function() {
        		reload();
        	});
        	</script>
        </h1>
    </div>
</div>
<!-- /.row -->
<h2 class="year"><center><?php echo $cnt_year; ?></center></h2>
<div class="row">
	<table class="table table-bordered">
	<thead>
	    <tr>
	        <th>Name</th>
	        <th>Action</th>
	    </tr>
	</thead>
	<tbody>
	    <?php
	    foreach ($datarows as $row) {
	    	//echo $row;
	        echo "<tr>";
	        echo "<td>".basename($row)."</td>";
	        echo "<td>";
	        echo "<a class='btn btn-xs btn-primary' href='".$this->base_url."/indexo/view?year=".$cnt_year."&month=".$cnt_month."&file=".basename($row)."'>View</a> ";
	        echo "<a class='btn btn-xs btn-success' href='".$this->base_url."/indexo/export?year=".$cnt_year."&month=".$cnt_month."&file=".basename($row)."'>Export</a>";
	        echo "</td>";
	        echo "</tr>";
	    }
	    ?>
	    
	</tbody>
	</table>
</div><br>