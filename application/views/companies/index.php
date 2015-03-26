<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
        	Companies
        </h1>
    </div>
</div>
<table class="table table-bordered">
<thead>
    <tr>
        <th>Name</th>
        <th>DB Table</th>
        <th>Records (Days)</th>
        <th>Action</th>
    </tr>
</thead>
<tbody>
    <?php
    foreach ($companies as $row) {
        echo "<tr>";
        echo "<td>".$row['symbol']."</td>";
        echo "<td>".$row['table']."</td>";
        echo "<td>".$row['data_days']." / ".$load_success."</td>";
        echo "<td>";
        echo "<a class='btn btn-xs btn-primary' href='".$this->base_url."/companies/view?com_id=".$row['id']."'>View</a> ";
        echo "<a class='btn btn-xs btn-success' href='".$this->base_url."/companies/export?com_id=".$row['id']."'>Export</a>";
        echo "</td>";
        echo "</tr>";
    }
    ?>
    
</tbody>
</table>
<?php
//print_r($companies);
?>
