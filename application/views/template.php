<?php
$sitename = $this->config->item('sitename');
$section = $this->router->fetch_class();
include('includes/header.php');
?>

<body>
    <div class='lgdat hide' lg='<?php echo $isLogin; ?>' section='<?php echo $section; ?>' main_content='<?php echo $main_content; ?>' base_url='<?php echo $base_url; ?>'></div>

    <div id="wrapper">
        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.html">NSE Helper 1.0</a>
            </div>
            <!-- /.navbar-header -->
            <?php
            include("includes/nav_top.php");
            include("includes/nav_left.php");
            ?>
        </nav>
        <div id="page-wrapper">
            <?php
            if(isset($showError)) {
                include("error_display.php");
            } else {
                include($load_from."/".$main_content.".php");
            }
            ?>
        </div>
        <!-- /#page-wrapper -->
    </div>
    <!-- /#wrapper -->

    <script src="<?php echo $base_url; ?>/bower_components/jquery/dist/jquery.min.js"></script>
    <script src="<?php echo $base_url; ?>/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- Metis Menu Plugin JavaScript -->
    <script src="<?php echo $base_url; ?>/bower_components/metisMenu/dist/metisMenu.min.js"></script>
    <?php
    if($section == "charts" && $main_content == "cadlestick") {
        ?>
        <!-- Morris Charts JavaScript -->
        <script src="<?php echo $base_url; ?>/bower_components/raphael/raphael-min.js"></script>
        <script src="<?php echo $base_url; ?>/bower_components/morrisjs/morris.min.js"></script>
        <script src="<?php echo $base_url; ?>/js/morris-data.js"></script>
        <?php
    }
    ?>
    <!-- Custom Theme JavaScript -->
    <script src="<?php echo $base_url; ?>/dist/js/sb-admin-2.js"></script>
</body>
</html>