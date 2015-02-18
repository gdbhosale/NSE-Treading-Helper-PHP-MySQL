<?php
include("header.php");
?>
	<div id="body">
        <h5><?php echo $message; ?></h5>
        Updating...
        <p style="color:red">
            <?php if(isset($error)) echo $error; ?>
        </p>
    </div>
<?php
include("footer.php");
?>