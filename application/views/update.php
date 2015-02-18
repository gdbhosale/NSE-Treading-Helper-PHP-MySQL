<?php
include("header.php");
?>
	<div id="body">
        <h4>Total Companies: <?php echo $total_companies; ?></h4>
        <h5><?php echo $message; ?></h5>
        Updating...
        <p style="color:red">
            <?php if(isset($error)) echo $error; ?>
        </p>
    </div>
<?php
include("footer.php");
?>