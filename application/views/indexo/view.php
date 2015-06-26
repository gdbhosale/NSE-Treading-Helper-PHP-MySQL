<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
        	Index Option: <?php echo $year." / ".$month. " / " . $file; ?>
            <a class="btn btn-success pull-right" href="<?php echo $base_url."/indexo/export?year=".$year."&month=".$month."&file=".$file; ?>">Export</a>
        </h1>
    </div>
</div>

<table class="table table-bordered">
<thead>
    <tr>
        <th>Date</th>
        <th>Open</th>
        <th>High</th>
        <th>Low</th>
        <th>Close</th>
        <th>OPEN_INT</th>
        <th>NOTION_VAL</th>
        <th>TRD_QTY</th>
    </tr>
</thead>
<tbody>
    <?php
    //CNT_DATE,OPEN_PRICE,HI_PRICE,LO_PRICE,CLOSE_PRICE,OPEN_INT,NOTION_VAL,TRD_QTY
    
    foreach ($csvData as $row) {
        echo "<tr>";
        echo "<td>".$row['CNT_DATE']."</td>";
        echo "<td>".$row['OPEN_PRICE']."</td>";
        echo "<td>".$row['HI_PRICE']."</td>";
        echo "<td>".$row['LO_PRICE']."</td>";
        echo "<td>".$row['CLOSE_PRICE']."</td>";
       	echo "<td>".$row['OPEN_INT']."</td>";
       	echo "<td>".$row['NOTION_VAL']."</td>";
       	echo "<td>".$row['TRD_QTY']."</td>";
        echo "</tr>";
    }
    ?>
    
</tbody>
</table>
<br>
<br>
<br>