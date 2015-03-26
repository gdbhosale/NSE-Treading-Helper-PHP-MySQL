<div class="navbar-default sidebar" role="navigation">
    <div class="sidebar-nav navbar-collapse">
        <ul class="nav" id="side-menu">
            <!--
            <li class="sidebar-search">
                <div class="input-group custom-search-form">
                    <input type="text" class="form-control" placeholder="Search...">
                    <span class="input-group-btn">
                    <button class="btn btn-default" type="button">
                        <i class="fa fa-search"></i>
                    </button>
                    </span>
                </div>
            </li>
            -->
            <li>
                <a href="<?php echo $base_url; ?>/home"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
            </li>
            <li>
                <a href="<?php echo $base_url; ?>/calendar"><i class="fa fa-table fa-fw"></i> Data Calendar</a>
            </li>
            <li>
                <a href="<?php echo $base_url; ?>/companies"><i class="fa fa-building-o fa-fw"></i> Companies</a>
            </li>
            <li>
                <a href="#"><i class="fa fa-line-chart fa-fw"></i> Charts<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li><a href="<?php echo $base_url; ?>/home/charts">Candlestick chart</a></li>
                </ul>
            </li>
            <li>
                <a href="#"><i class="fa fa-send fa-fw"></i> Export<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li><a href="<?php echo $base_url; ?>/home/company_export">Company Data</a></li>
                </ul>
            </li>
            
        </ul>
    </div>
</div>